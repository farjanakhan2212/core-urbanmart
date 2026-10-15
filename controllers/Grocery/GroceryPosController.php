<?php
class GroceryPosController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $this->view([]);
    }

    public function checkout($obj = null, $file = null)
    {
        global $base_url;
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
            grocery_json(['success' => false, 'message' => 'Cart is empty']);
            return;
        }
        $res = GroceryService::createSale([
            'customer_id' => $_POST['customer_id'] ?? null,
            'items' => $items,
            'discount' => $_POST['discount'] ?? 0,
            'tax' => $_POST['tax'] ?? 0,
            'payment_method_id' => $_POST['payment_method_id'] ?? GrocerySetting::get('default_payment_method', '1'),
            'paid_amount' => isset($_POST['paid_amount']) && $_POST['paid_amount'] !== '' ? $_POST['paid_amount'] : null,
            'payment_status' => $_POST['payment_status'] ?? 'paid',
            'transaction_id' => $_POST['transaction_id'] ?? '',
            'remark' => $_POST['remark'] ?? '',
            'sale_date' => $_POST['sale_date'] ?? date('Y-m-d'),
        ]);
        if (!empty($res['success'])) {
            $res['redirect'] = $base_url . '/GrocerySale/invoice/' . $res['id'];
        }
        grocery_json($res);
    }
}