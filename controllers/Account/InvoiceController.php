<?php

class InvoiceController extends Controller{

  function __construct(){
    $this->module="Accounting";
  }
  public function index(){
    $this->view();
  }

 
  public function create(){
      $this->view();
  }

  public function save(){

  }

  public function edit($id){
    $this->view(Invoice::find($id));
  }

  public function update($id = null, $obj = null, $file = null){
    $id = (int)$id;
    if ($id <= 0) $id = (int)($_POST['id'] ?? 0);
    $old = Invoice::find($id);
    if (!$old) {
      redirect();
      return;
    }

    $now = date("Y-m-d H:i:s");
    $items = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [];
    $clean = [];
    $total = 0;
    foreach ($items as $it) {
      $it = is_array($it) ? $it : [];
      $pid = (int)($it['product_id'] ?? 0);
      $qty = (float)($it['qty'] ?? $it['quantity'] ?? 0);
      $price = (float)($it['price'] ?? 0);
      if ($pid > 0 && $qty > 0) {
        $clean[] = ['product_id' => $pid, 'qty' => $qty, 'price' => $price];
        $total += $qty * $price;
      }
    }

    $invoice = new Invoice();
    $invoice->id = $id;
    $invoice->created_at = $old->created_at;
    $invoice->updated_at = $now;
    $invoice->customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : $old->customer_id;
    $invoice->remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
    $invoice->payment_term = isset($_POST['payment_term']) ? trim($_POST['payment_term']) : 'CASH';
    $invoice->invoice_total = round($total, 2);
    $invoice->paid_total = (float)$old->paid_total;
    $invoice->previous_due = isset($_POST['previous_due']) ? (float)$_POST['previous_due'] : (float)$old->previous_due;
    $invoice->update();

    foreach (InvoiceDetail::filter($id) as $oldDetail) {
      InvoiceDetail::delete((int)$oldDetail->id);
    }
    foreach ($clean as $it) {
      $detail = new InvoiceDetail();
      $detail->invoice_id = $id;
      $detail->product_id = $it['product_id'];
      $detail->price = $it['price'];
      $detail->qty = $it['qty'];
      $detail->discount = 0;
      $detail->vat = 0;
      $detail->save();
    }

    redirect();
  }

  public function confirm($id){
       $this->view();
  }

  public function delete($id, $obj = null, $file = null){
    global $base_url;
    $id = (int)$id;
    $token = (is_object($obj) && isset($obj->_token)) ? (string)$obj->_token : (isset($_POST['_token']) ? (string)$_POST['_token'] : '');
    if ($id <= 0 || !csrf_verify($token)) {
      $_SESSION["flash"]["error"] = "Security check failed. Please try again.";
      redirect();
      return;
    }
    if (Invoice::delete($id)) {
      $_SESSION["flash"]["success"] = "Invoice INV-" . str_pad((string)$id, 4, '0', STR_PAD_LEFT) . " was deleted successfully.";
    } else {
      $_SESSION["flash"]["error"] = "Invoice could not be deleted. It may not exist.";
    }
    redirect();
  }


  public function show($id){
		$this->view(Invoice::find($id));
	}

  public function print($id){
    global $base_url;
    $data = Document::invoice($id);
    if (!$data) {
      $_SESSION["flash"]["error"] = "Invoice not found.";
      redirect();
      return;
    }
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: text/html; charset=utf-8');
    echo Document::printPage('invoice', $data);
    exit;
  }

  public function pdf($id){
    global $base_url;
    $data = Document::invoice($id);
    if (!$data) {
      $_SESSION["flash"]["error"] = "Invoice not found.";
      redirect();
      return;
    }
    Document::pdf(Document::pdfHtml('invoice', $data), $data['doc_no'] . '.pdf', false);
  }

  public function download($id){
    global $base_url;
    $data = Document::invoice($id);
    if (!$data) {
      $_SESSION["flash"]["error"] = "Invoice not found.";
      redirect();
      return;
    }
    Document::pdf(Document::pdfHtml('invoice', $data), $data['doc_no'] . '.pdf', true);
  }

}

?>