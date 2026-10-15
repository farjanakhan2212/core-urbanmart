<?php

/**
 * UrbanMart Grocery - Sale API
 * /api/Grocerysale (index,q,page,perpage) | find | create
 *
 * create body: {customer_id, sale_date, discount, tax, payment_method_id,
 *               paid_amount, payment_status, transaction_id, remark,
 *               items:[{product_id, quantity, selling_price?}]}
 */
class GrocerysaleApi extends GroceryApiBase
{
    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        echo json_encode(['success' => 1, 'data' => GrocerySale::manager($page, $per, $q)]);
    }

    public function find($data)
    {
        $this->guard();
        $id = (int)($data->id ?? 0);
        $sale = GrocerySale::findRow($id);
        if (!$sale) {
            http_response_code(404);
            echo json_encode(['success' => 0, 'message' => 'Sale not found']);
            return;
        }
        $lines = GrocerySaleDetail::rows('sale_id = ?', [$id], 'id ASC');
        $sale['customer'] = GroceryCustomer::findRow((int)($sale['customer_id'] ?? 0));
        $sale['lines'] = $lines;
        echo json_encode(['success' => 1, 'sale' => $sale]);
    }

    public function create($data)
    {
        $this->guard();
        $items = $this->asItems($data->items ?? []);
        if (!$items) {
            echo json_encode(['success' => 0, 'message' => 'items is required and must be a non-empty array']);
            return;
        }
        $res = GroceryService::createSale([
            'customer_id' => $data->customer_id ?? null,
            'items' => $items,
            'discount' => $data->discount ?? 0,
            'tax' => $data->tax ?? 0,
            'payment_method_id' => $data->payment_method_id ?? GrocerySetting::get('default_payment_method', '1'),
            'paid_amount' => isset($data->paid_amount) && $data->paid_amount !== '' ? $data->paid_amount : null,
            'payment_status' => $data->payment_status ?? 'paid',
            'transaction_id' => $data->transaction_id ?? '',
            'remark' => $data->remark ?? '',
            'sale_date' => $data->sale_date ?? date('Y-m-d'),
        ]);
        echo json_encode($res['success']
            ? ['success' => 1, 'message' => 'Sale created', 'id' => $res['id'], 'invoice_no' => $res['invoice_no']]
            : ['success' => 0, 'message' => $res['message']]);
    }

    private function asItems($items)
    {
        if (!is_array($items)) return [];
        return array_map(function ($it) {
            return is_object($it) ? (array)$it : $it;
        }, $items);
    }
}