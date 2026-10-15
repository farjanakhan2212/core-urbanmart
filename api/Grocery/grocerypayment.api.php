<?php

/**
 * UrbanMart Grocery - Payment API
 * /api/Grocerypayment (index) | create
 *
 * create body: {payable_type: sale|purchase|order, payable_id, amount,
 *               customer_id?, supplier_id?, payment_method_id, transaction_id?}
 */
class GrocerypaymentApi extends GroceryApiBase
{
    public function index($data)
    {
        $this->guard();
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        echo json_encode(['success' => 1, 'data' => GroceryPayment::manager($page, $per, $q)]);
    }

    public function create($data)
    {
        $this->guard();
        $type = trim($data->payable_type ?? 'sale');
        $id = (int)($data->payable_id ?? 0);
        $amount = (float)($data->amount ?? 0);
        $method = (int)($data->payment_method_id ?? 1);
        if ($id <= 0 || $amount <= 0) {
            echo json_encode(['success' => 0, 'message' => 'payable_id and amount (positive) are required']);
            return;
        }
        if ($type === 'sale') {
            GroceryService::recordPayment('sale', $id, $amount, $method, (int)($data->customer_id ?? 0), null, $data->transaction_id ?? '', 'API payment');
            $sale = GrocerySale::findRow($id);
            if ($sale) {
                $newPaid = (float)$sale['paid_amount'] + $amount;
                $newDue = max(0, (float)$sale['due_amount'] - $amount);
                GrocerySale::updateRow($id, [
                    'paid_amount' => $newPaid,
                    'due_amount' => $newDue,
                    'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
                ]);
            }
        } elseif ($type === 'purchase') {
            GroceryService::recordPayment('purchase', $id, $amount, $method, null, (int)($data->supplier_id ?? 0), $data->transaction_id ?? '', 'API payment');
            $pur = GroceryPurchase::findRow($id);
            if ($pur) {
                GroceryPurchase::updateRow($id, [
                    'paid_amount' => (float)$pur['paid_amount'] + $amount,
                    'due_amount' => max(0, (float)$pur['due_amount'] - $amount),
                ]);
            }
        } elseif ($type === 'order') {
            GroceryService::recordPayment('order', $id, $amount, $method, (int)($data->customer_id ?? 0), null, $data->transaction_id ?? '', 'API payment');
            $order = GroceryOrder::findRow($id);
            if ($order) {
                $newPaid = (float)$order['paid_amount'] + $amount;
                $newDue = max(0, (float)$order['due_amount'] - $amount);
                GroceryOrder::updateRow($id, [
                    'paid_amount' => $newPaid,
                    'due_amount' => $newDue,
                    'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
                ]);
            }
        } else {
            echo json_encode(['success' => 0, 'message' => 'payable_type must be sale, purchase or order']);
            return;
        }
        echo json_encode(['success' => 1, 'message' => 'Payment recorded', 'payable_type' => $type, 'payable_id' => $id, 'amount' => $amount]);
    }
}