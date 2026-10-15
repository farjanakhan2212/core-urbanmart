<?php
class GrocerySaleController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = GrocerySale::manager($page, 20, $q);
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function invoice($id, $obj = null, $file = null)
    {
        $data = GrocerySale::withDetails((int)$id);
        if (!$data) {
            redirect('index');
            return;
        }
        $this->view(['sale' => $data]);
    }

    /** Canonical View alias (renders the same detail page). */
    public function show($id, $obj = null, $file = null)
    {
        $_GET['method'] = 'invoice';
        $this->invoice($id);
    }

    public function print($id, $obj = null, $file = null)
    {
        $data = Document::sale($id);
        if (!$data) {
            $_SESSION["flash"]["error"] = "Sale not found.";
            redirect('index');
            return;
        }
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: text/html; charset=utf-8');
        echo Document::printPage('sale', $data);
        exit;
    }

    public function pdf($id, $obj = null, $file = null)
    {
        $data = Document::sale($id);
        if (!$data) {
            $_SESSION["flash"]["error"] = "Sale not found.";
            redirect('index');
            return;
        }
        try {
            Document::pdf(Document::pdfHtml('sale', $data), $data['doc_no'] . '.pdf', false);
        } catch (\Throwable $e) {
            $_SESSION["flash"]["error"] = "Could not generate PDF: " . $e->getMessage();
            redirect('index');
        }
    }

    public function download($id, $obj = null, $file = null)
    {
        $data = Document::sale($id);
        if (!$data) {
            $_SESSION["flash"]["error"] = "Sale not found.";
            redirect('index');
            return;
        }
        try {
            Document::pdf(Document::pdfHtml('sale', $data), $data['doc_no'] . '.pdf', true);
        } catch (\Throwable $e) {
            $_SESSION["flash"]["error"] = "Could not generate PDF: " . $e->getMessage();
            redirect('index');
        }
    }

    public function edit($id, $obj = null, $file = null)
    {
        $sale = GrocerySale::withDetails((int)$id);
        if (!$sale) {
            $_SESSION["flash"]["error"] = "Sale not found.";
            redirect('index');
            return;
        }
        if (($sale['payment_status'] ?? '') === 'paid') {
            $_SESSION["flash"]["error"] = "Invoice " . ($sale['invoice_no'] ?? '') . " is fully paid and posted; it cannot be edited. Use Print / PDF for a copy.";
            redirect('index');
            return;
        }
        $this->view([
            'sale'        => $sale,
            'customers'   => GroceryCustomer::rows('status = 1', [], 'name ASC'),
            'methods'     => GroceryPaymentMethod::rows('status = 1', [], 'name ASC'),
            'products'    => GroceryProduct::rows('status = 1', [], 'name ASC'),
            'restricted'  => false,
        ]);
    }

    public function update($id = null, $obj = null, $file = null)
    {
        $id = (int)$id;
        if ($id <= 0) $id = (int)($_POST['id'] ?? 0);
        if (!csrf_verify($_POST['_token'] ?? '')) {
            $_SESSION["flash"]["error"] = "Security check failed. Please try again.";
            redirect('index');
            return;
        }
        if ($id <= 0 || !GrocerySale::findRow($id)) {
            redirect('index');
            return;
        }

        $items = [];
        $pids   = isset($_POST['product_id']) && is_array($_POST['product_id']) ? $_POST['product_id'] : [];
        $qtys   = isset($_POST['quantity']) && is_array($_POST['quantity']) ? $_POST['quantity'] : [];
        $prices = isset($_POST['selling_price']) && is_array($_POST['selling_price']) ? $_POST['selling_price'] : [];
        $discs  = isset($_POST['line_discount']) && is_array($_POST['line_discount']) ? $_POST['line_discount'] : [];
        foreach ($pids as $k => $pid) {
            $items[] = [
                'product_id'    => (int)$pid,
                'quantity'      => (float)($qtys[$k] ?? 0),
                'selling_price' => (float)($prices[$k] ?? 0),
                'discount'      => (float)($discs[$k] ?? 0),
            ];
        }

        $res = GroceryService::updateSale($id, [
            'customer_id'       => $_POST['customer_id'] ?? null,
            'sale_date'         => $_POST['sale_date'] ?? date('Y-m-d'),
            'discount'          => $_POST['discount'] ?? 0,
            'tax'               => $_POST['tax'] ?? 0,
            'paid_amount'       => $_POST['paid_amount'] ?? null,
            'payment_method_id' => $_POST['payment_method_id'] ?? null,
            'remark'            => $_POST['remark'] ?? '',
            'items'             => $items,
        ]);

        if (!empty($res['success'])) {
            $_SESSION["flash"]["success"] = "Invoice " . ($res['invoice_no'] ?? '') . " was updated successfully.";
        } else {
            $_SESSION["flash"]["error"] = "Update failed: " . ($res['message'] ?? 'unknown error');
        }
        redirect('index');
    }

    public function confirm($id, $obj = null, $file = null)
    {
        $sale = GrocerySale::withDetails((int)$id);
        if (!$sale) {
            $_SESSION["flash"]["error"] = "Sale not found.";
            redirect('index');
            return;
        }
        $this->view(['sale' => $sale]);
    }

    public function delete($id, $obj = null, $file = null)
    {
        $id = (int)$id;
        $token = (is_object($obj) && isset($obj->_token)) ? (string)$obj->_token : (isset($_POST['_token']) ? (string)$_POST['_token'] : '');
        if ($id <= 0 || !csrf_verify($token)) {
            $_SESSION["flash"]["error"] = "Security check failed. Please try again.";
            redirect('index');
            return;
        }
        $res = GroceryService::deleteSale($id);
        if (!empty($res['success'])) {
            $_SESSION["flash"]["success"] = "Sale " . ($res['invoice_no'] ?? ('#' . $id)) . " was deleted and stock restored.";
        } else {
            $_SESSION["flash"]["error"] = "Delete failed: " . ($res['message'] ?? 'unknown error');
        }
        redirect('index');
    }
}
