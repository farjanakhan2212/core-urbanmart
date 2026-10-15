<?php

/**
 * UrbanMart Grocery - Stock API
 * /api/Grocerystock (index,q,page,perpage,low) | movements | adjust
 *
 * movements: {page?, perpage?, q?, type?, product?}
 * adjust body: {adjustment_type: in|out, reason?, items:[{product_id, adjusted_quantity}]}
 */
class GrocerystockApi extends GroceryApiBase
{
    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        $low = isset($data->low) ? (bool)(int)$data->low : false;
        echo json_encode(['success' => 1, 'data' => GroceryStock::reportRows($page, $per, $q, $low)]);
    }

    public function movements($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 25)));
        $q = isset($data->q) ? trim($data->q) : '';
        $type = isset($data->type) ? trim($data->type) : '';
        $product = (int)($data->product ?? 0);
        echo json_encode(['success' => 1, 'data' => GroceryStockMovement::manager($page, $per, $q, $type, $product)]);
    }

    public function adjust($data)
    {
        $this->guard();
        $items = $data->items ?? [];
        if (!is_array($items) || !$items) {
            echo json_encode(['success' => 0, 'message' => 'items is required and must be a non-empty array']);
            return;
        }
        $items = array_map(function ($it) {
            $arr = is_object($it) ? (array)$it : $it;
            return [
                'product_id' => $arr['product_id'] ?? 0,
                'adjusted_quantity' => $arr['adjusted_quantity'] ?? $arr['quantity'] ?? 0,
            ];
        }, $items);
        $res = GroceryService::adjustStock([
            'adjustment_type' => $data->adjustment_type ?? 'in',
            'reason' => $data->reason ?? '',
            'items' => $items,
        ]);
        echo json_encode($res['success']
            ? ['success' => 1, 'message' => 'Stock adjusted', 'id' => $res['id'], 'adjustment_no' => $res['adjustment_no']]
            : ['success' => 0, 'message' => $res['message']]);
    }
}