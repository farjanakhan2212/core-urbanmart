<?php
class GroceryOrderController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $list = GroceryOrder::manager($page, 20, $status);
        $this->view(['page' => $page, 'status' => $status, 'list' => $list]);
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
        $res = GroceryService::createOrder([
            'customer_id' => $_POST['customer_id'] ?? null,
            'items' => $items,
            'discount' => $_POST['discount'] ?? 0,
            'delivery_charge' => $_POST['shipping_fee'] ?? 0,
            'payment_method_id' => $_POST['payment_method_id'] ?? 1,
            'paid_amount' => isset($_POST['paid_amount']) && $_POST['paid_amount'] !== '' ? $_POST['paid_amount'] : null,
            'payment_status' => $_POST['payment_status'] ?? ($_POST['payment_status_drop'] ?? 'unpaid'),
            'transaction_id' => $_POST['transaction_id'] ?? '',
            'delivery_address' => $_POST['shipping_address'] ?? '',
            'delivery_date' => $_POST['expected_delivery'] ?? '',
            'remark' => $_POST['remark'] ?? '',
        ]);
        redirect('index');
    }

    public function status($id, $obj = null, $file = null)
    {
        $status = isset($_GET['st']) ? trim($_GET['st']) : 'pending';
        $allowed = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) $status = 'pending';
        GroceryService::updateOrderStatus((int)$id, $status);
        if ($status === 'shipped' || $status === 'delivered') {
            $del = GroceryService::ensureDelivery((int)$id, $_GET['person'] ?? '', $_GET['phone'] ?? '');
            if ($status === 'delivered' && !empty($del['id'])) {
                GroceryService::updateDeliveryStatus((int)$del['id'], 4);
            }
        }
        redirect('index');
    }
}