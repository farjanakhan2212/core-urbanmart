<?php

/**
 * UrbanMart Grocery - Order API
 * /api/Groceryorder (index) | find | create | status
 *
 * create body: {customer_id, order_date, discount, tax, delivery_charge,
 *               delivery_address, delivery_date, payment_method_id,
 *               paid_amount, transaction_id, remark,
 *               items:[{product_id, quantity, price?, discount?, vat?}]}
 * status body: {id, status}  (pending|confirmed|processing|packed|shipped|delivered|cancelled)
 */
class GroceryorderApi extends GroceryApiBase
{
    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        $status = isset($data->status) ? trim($data->status) : '';
        echo json_encode(['success' => 1, 'data' => GroceryOrder::manager($page, $per, $q, $status)]);
    }

    public function find($data)
    {
        $this->guard();
        $id = (int)($data->id ?? 0);
        $order = GroceryOrder::findRow($id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => 0, 'message' => 'Order not found']);
            return;
        }
        $order['customer'] = GroceryCustomer::findRow((int)($order['customer_id'] ?? 0));
        $order['lines'] = GroceryOrderDetail::rows('order_id = ?', [$id], 'id ASC');
        echo json_encode(['success' => 1, 'order' => $order]);
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
        $res = GroceryService::createOrder([
            'customer_id' => $data->customer_id ?? 0,
            'items' => $items,
            'order_date' => $data->order_date ?? date('Y-m-d H:i:s'),
            'delivery_charge' => $data->delivery_charge ?? 0,
            'delivery_address' => $data->delivery_address ?? null,
            'delivery_date' => $data->delivery_date ?? null,
            'discount' => $data->discount ?? 0,
            'tax' => $data->tax ?? 0,
            'payment_method_id' => $data->payment_method_id ?? 1,
            'paid_amount' => $data->paid_amount ?? null,
            'transaction_id' => $data->transaction_id ?? '',
            'remark' => $data->remark ?? '',
        ]);
        echo json_encode($res['success']
            ? ['success' => 1, 'message' => 'Order created', 'id' => $res['id'], 'order_no' => $res['order_no']]
            : ['success' => 0, 'message' => $res['message']]);
    }

    public function status($data)
    {
        $this->guard();
        $id = (int)($data->id ?? 0);
        $status = trim($data->status ?? '');
        $allowed = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'];
        if (!$id || !in_array($status, $allowed, true)) {
            echo json_encode(['success' => 0, 'message' => 'id and a valid status are required']);
            return;
        }
        GroceryService::updateOrderStatus($id, $status);
        echo json_encode(['success' => 1, 'message' => 'Order status updated', 'id' => $id, 'status' => $status]);
    }
}