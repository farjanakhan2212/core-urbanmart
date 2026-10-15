<?php
class GroceryDeliveryController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = GroceryDelivery::manager($page, 20, $q);
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function status($id, $obj = null, $file = null)
    {
        $statusId = max(1, (int)($_GET['s'] ?? 1));
        $allowed = [1, 2, 3, 4, 5, 6, 7];
        if (!in_array($statusId, $allowed)) $statusId = 1;
        GroceryService::updateDeliveryStatus((int)$id, $statusId);
        $row = GroceryDelivery::findRow((int)$id);
        if ($row && !empty($row['order_id']) && $statusId === 5) {
            GroceryService::updateOrderStatus((int)$row['order_id'], 'delivered');
        }
        redirect('index');
    }
}