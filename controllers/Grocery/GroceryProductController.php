<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryProductController extends GroceryCrudController
{
    protected $model = 'GroceryProduct';
    protected $formFields = ['category_id', 'subcategory_id', 'brand_id', 'unit_id', 'name', 'slug', 'sku', 'barcode',
        'product_type', 'purchase_price', 'selling_price', 'mrp_price', 'discount_type', 'discount_value', 'tax_rate',
        'stock_quantity', 'reorder_level', 'weight', 'short_description', 'full_description', 'is_featured', 'status'];
    protected $statusInt = false;

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $low = isset($_GET['low']) ? (int)$_GET['low'] : 0;
        if ($low) {
            $rows = GroceryProduct::lowStock(1000);
            $tot = count($rows);
            $list = ['rows' => $rows, 'total' => $tot, 'page' => 1, 'per' => 1000];
        } else {
            $list = GroceryProduct::manager($page, 20, $q, '');
        }
        $this->view(['page' => $page, 'q' => $q, 'low' => $low, 'list' => $list]);
    }

    public function save($obj = null, $file = null)
    {
        $row = $this->transformFields($obj);
        if (empty($row['name'])) {
            redirect('create');
            return;
        }
        $row['status'] = $row['status'] ?? 'active';
        $row['stock_quantity'] = $row['stock_quantity'] ?? 0;
        $row['reorder_level'] = $row['reorder_level'] ?? 0;
        $row['is_featured'] = !empty($row['is_featured']) ? 1 : 0;
        $row['slug'] = $row['slug'] ?? slugify($row['name']);
        $now = date('Y-m-d H:i:s');
        $row['created_at'] = $now;
        $row['updated_at'] = $now;
        $img = $this->uploadImage($file, $row['name']);
        if ($img !== '') $row['image'] = $img;
        $id = GroceryProduct::insertRow($row);
        if ($id) GroceryService::syncProductStock($id, (float)$row['stock_quantity']);
        redirect('index');
    }

    public function update($id, $obj = null, $file = null)
    {
        $row = $this->transformFields($obj);
        if ($row) {
            $row['slug'] = $row['slug'] ?? slugify($row['name'] ?? 'product');
            $row['is_featured'] = !empty($row['is_featured']) ? 1 : 0;
            $row['updated_at'] = date('Y-m-d H:i:s');
            $img = $this->uploadImage($file, $row['name'] ?? 'product');
            if ($img !== '') $row['image'] = $img;
            GroceryProduct::updateRow((int)$id, $row);
            if (array_key_exists('stock_quantity', $row)) {
                GroceryService::syncProductStock((int)$id, (float)$row['stock_quantity']);
            }
        }
        redirect('index');
    }

public function search($obj = null, $file = null)
    {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        grocery_json(['success' => 1, 'items' => GroceryProduct::posItems($q, 20)]);
    }

    private function uploadImage($file, $name)
    {
        if (isset($file->image) && !empty($file->image->tmp_name)) {
            $res = File::upload($file->image, 'img', $name);
            return ($res && $res !== '-1') ? $res : '';
        }
        return '';
    }
}