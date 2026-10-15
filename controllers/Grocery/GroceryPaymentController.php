<?php
class GroceryPaymentController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = GroceryPayment::manager($page, 20, $q, '');
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function create()
    {
        $this->view([]);
    }

    public function save($obj = null, $file = null)
    {
        $type = $_POST['payable_type'] ?? 'sale';
        $id = (int)($_POST['payable_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $method = (int)($_POST['payment_method_id'] ?? 1);
        if ($id <= 0 || $amount <= 0) {
            redirect('create');
            return;
        }
        if ($type === 'sale') {
            GroceryService::recordPayment('sale', $id, $amount, $method, (int)($_POST['customer_id'] ?? 0), null, $_POST['transaction_id'] ?? '', 'Manual payment');
            $sale = GrocerySale::findRow($id);
            if ($sale) {
                GrocerySale::updateRow($id, ['paid_amount' => $sale['paid_amount'] + $amount, 'due_amount' => max(0, $sale['due_amount'] - $amount), 'payment_status' => max(0, $sale['due_amount'] - $amount) <= 0 ? 'paid' : 'partial']);
            }
        } elseif ($type === 'purchase') {
            GroceryService::recordPayment('purchase', $id, $amount, $method, null, (int)($_POST['supplier_id'] ?? 0), $_POST['transaction_id'] ?? '', 'Manual payment');
            $pur = GroceryPurchase::findRow($id);
            if ($pur) {
                GroceryPurchase::updateRow($id, ['paid_amount' => $pur['paid_amount'] + $amount, 'due_amount' => max(0, $pur['due_amount'] - $amount)]);
            }
        }
        redirect('index');
    }
}