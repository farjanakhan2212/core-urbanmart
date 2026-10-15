<?php
class GroceryStockController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $low = isset($_GET['low']) ? (int)$_GET['low'] : 0;
        $list = GroceryStock::reportRows($page, 20, $q, (bool)$low);
        $this->view(['page' => $page, 'q' => $q, 'low' => $low, 'list' => $list]);
    }

    public function movements()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $productId = (int)($_GET['product'] ?? 0);
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $typeMap = [
            'in' => ['purchase_in', 'adjustment_in', 'opening'],
            'out' => ['sale_out', 'adjustment_out', 'damage'],
            'adjustment' => ['adjustment_in', 'adjustment_out'],
        ];
        $filter = ($type !== '' && isset($typeMap[$type])) ? $typeMap[$type] : '';
        $list = GroceryStockMovement::manager($page, 25, $q, $filter, $productId);
        $this->view(['page' => $page, 'product' => $productId, 'q' => $q, 'type' => $type, 'list' => $list]);
    }

    public function adjustments()
    {
        $list = GroceryStockAdjustment::manager(1, 30, '');
        $this->view(['list' => $list]);
    }

    public function adjustment()
    {
        $this->view([]);
    }

    public function adjustment_save($obj = null, $file = null)
    {
        $items = [];
        if (isset($_POST['cart']) && is_string($_POST['cart'])) {
            $items = json_decode($_POST['cart'], true);
        } elseif (isset($_POST['cart']) && is_array($_POST['cart'])) {
            $items = $_POST['cart'];
        }
        $res = GroceryService::adjustStock([
            'adjustment_type' => $_POST['adjustment_type'] ?? 'in',
            'reason' => $_POST['reason'] ?? '',
            'items' => array_map(function ($i) {
                return ['product_id' => $i['product_id'] ?? 0, 'adjusted_quantity' => $i['adjusted_quantity'] ?? $i['quantity'] ?? 0];
            }, $items),
        ]);
        redirect('adjustments');
    }
}