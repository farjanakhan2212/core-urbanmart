<?php
class GroceryPurchaseController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = GroceryPurchase::manager($page, 20, $q);
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function create()
    {
        $this->view([]);
    }

    public function save($obj = null, $file = null)
    {
        $items = [];
        if (isset($_POST['cart']) && is_string($_POST['cart'])) {
            $items = json_decode($_POST['cart'], true);
        } elseif (isset($_POST['cart']) && is_array($_POST['cart'])) {
            $items = $_POST['cart'];
        }
        $items = array_values(array_filter((array)$items, function ($i) {
            return is_array($i) && !empty($i['product_id']) && (float)($i['quantity'] ?? 0) > 0;
        }));
        if (!$items) {
            redirect('create');
            return;
        }
        $res = GroceryService::createPurchase([
            'supplier_id' => $_POST['supplier_id'] ?? null,
            'items' => $items,
            'discount' => $_POST['discount'] ?? 0,
            'tax' => $_POST['tax'] ?? 0,
            'payment_method_id' => $_POST['payment_method_id'] ?? 1,
            'paid_amount' => isset($_POST['paid_amount']) && $_POST['paid_amount'] !== '' ? $_POST['paid_amount'] : null,
            'payment_status' => $_POST['payment_status'] ?? 'paid',
            'transaction_id' => $_POST['transaction_id'] ?? '',
            'purchase_date' => $_POST['purchase_date'] ?? date('Y-m-d'),
            'status' => $_POST['status'] ?? 'received',
            'remark' => $_POST['remark'] ?? '',
        ]);
        redirect('index');
    }
}