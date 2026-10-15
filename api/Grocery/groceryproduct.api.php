<?php

/**
 * UrbanMart Grocery - Product API
 * /api/Groceryproduct (index) | low | search | find | save | update | delete
 *
 * index supports: q, page, perpage, low=1 (low-stock only)
 * save/update accept stock_quantity and sync the warehouse stock row.
 */
class GroceryproductApi extends GroceryApiBase
{
    private static $fields = ['name', 'slug', 'sku', 'barcode', 'category_id', 'subcategory_id', 'brand_id', 'unit_id',
        'product_type', 'purchase_price', 'selling_price', 'mrp_price', 'discount_type', 'discount_value', 'tax_rate',
        'stock_quantity', 'reorder_level', 'weight', 'short_description', 'full_description', 'image', 'is_featured', 'status'];

    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        if (isset($data->low) && (int)$data->low === 1) {
            $rows = GroceryProduct::lowStock(1000);
            echo json_encode(['success' => 1, 'data' => ['rows' => $rows, 'total' => count($rows), 'page' => 1, 'per' => count($rows)]]);
            return;
        }
        echo json_encode(['success' => 1, 'data' => GroceryProduct::manager($page, $per, $q, '')]);
    }

    public function low($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => ['rows' => GroceryProduct::lowStock(1000)]]);
    }

    public function search($data)
    {
        $this->guard();
        $q = isset($data->q) ? trim($data->q) : '';
        echo json_encode(['success' => 1, 'data' => ['items' => GroceryProduct::posItems($q, 50)]]);
    }

    public function find($data)
    {
        $this->guard();
        $row = GroceryProduct::findDetailed((int)($data->id ?? 0));
        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => 0, 'message' => 'Product not found']);
            return;
        }
        echo json_encode(['success' => 1, 'row' => $row]);
    }

    public function save($data)
    {
        $this->guard();
        $row = $this->pickFields($data, self::$fields);
        if (empty($row['name'])) {
            echo json_encode(['success' => 0, 'message' => 'Product name is required']);
            return;
        }
        $row['status'] = $row['status'] ?? 'active';
        $row['stock_quantity'] = isset($row['stock_quantity']) ? (float)$row['stock_quantity'] : 0;
        $row['reorder_level'] = isset($row['reorder_level']) ? (float)$row['reorder_level'] : 0;
        $row['is_featured'] = !empty($row['is_featured']) ? 1 : 0;
        $row['slug'] = $row['slug'] ?? slugify($row['name']);
        $now = date('Y-m-d H:i:s');
        $row['created_at'] = $now;
        $row['updated_at'] = $now;
        $id = GroceryProduct::insertRow($row);
        if ($id) GroceryService::syncProductStock((int)$id, (float)$row['stock_quantity']);
        echo json_encode(['success' => 1, 'message' => 'Product saved', 'id' => (int)$id]);
    }

    public function update($data)
    {
        $this->guard();
        $id = (int)($data->id ?? 0);
        if (!$id) {
            echo json_encode(['success' => 0, 'message' => 'Product id required']);
            return;
        }
        $row = $this->pickFields($data, self::$fields);
        if (!$row) {
            echo json_encode(['success' => 0, 'message' => 'No valid fields provided']);
            return;
        }
        $row['slug'] = $row['slug'] ?? slugify($row['name'] ?? 'product');
        $row['is_featured'] = !empty($row['is_featured']) ? 1 : 0;
        $row['updated_at'] = date('Y-m-d H:i:s');
        GroceryProduct::updateRow($id, $row);
        if (array_key_exists('stock_quantity', $row)) {
            GroceryService::syncProductStock($id, (float)$row['stock_quantity']);
        }
        echo json_encode(['success' => 1, 'message' => 'Product updated', 'id' => $id]);
    }

    public function delete($data)
    {
        $this->guard();
        GroceryProduct::deleteRow((int)($data->id ?? 0));
        echo json_encode(['success' => 1, 'message' => 'Product deleted', 'id' => (int)($data->id ?? 0)]);
    }
}