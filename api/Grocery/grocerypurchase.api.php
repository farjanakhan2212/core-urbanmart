<?php

/**
 * UrbanMart Grocery - Purchase API
 * /api/Grocerypurchase (index) | find | create
 *
 * create body: {supplier_id, purchase_date, discount, tax, payment_method_id,
 *               paid_amount, transaction_id, remark,
 *               items:[{product_id, quantity, purchase_price, discount?, vat?}]}
 */
class GrocerypurchaseApi extends GroceryApiBase
{
    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        echo json_encode(['success' => 1, 'data' => GroceryPurchase::manager($page, $per, $q)]);
    }

    public function find($data)
    {
        $this->guard();
        $id = (int)($data->id ?? 0);
        $purchase = GroceryPurchase::findRow($id);
        if (!$purchase) {
            http_response_code(404);
            echo json_encode(['success' => 0, 'message' => 'Purchase not found']);
            return;
        }
        $purchase['supplier'] = GrocerySupplier::findRow((int)($purchase['supplier_id'] ?? 0));
        $purchase['lines'] = GroceryPurchaseDetail::rows('purchase_id = ?', [$id], 'id ASC');
        echo json_encode(['success' => 1, 'purchase' => $purchase]);
    }

    public function create($data)
    {
        $this->guard();
        $items = $data->items ?? [];
        if (!is_array($items) || !$items) {
            echo json_encode(['success' => 0, 'message' => 'items is required and must be a non-empty array']);
            return;
        }
        $items = array_map(function ($it) {
            return is_object($it) ? (array)$it : $it;
        }, $items);
        $res = GroceryService::createPurchase([
            'supplier_id' => $data->supplier_id ?? null,
            'items' => $items,
            'purchase_date' => $data->purchase_date ?? date('Y-m-d'),
            'discount' => $data->discount ?? 0,
            'tax' => $data->tax ?? 0,
            'payment_method_id' => $data->payment_method_id ?? 1,
            'paid_amount' => $data->paid_amount ?? null,
            'transaction_id' => $data->transaction_id ?? '',
            'remark' => $data->remark ?? '',
            'status' => $data->status ?? 'received',
        ]);
        echo json_encode($res['success']
            ? ['success' => 1, 'message' => 'Purchase created', 'id' => $res['id'], 'purchase_no' => $res['purchase_no']]
            : ['success' => 0, 'message' => $res['message']]);
    }
}