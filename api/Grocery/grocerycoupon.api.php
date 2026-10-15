<?php

/**
 * UrbanMart Grocery - Coupon API
 * /api/Grocerycoupon (index) | find | save | update | delete
 */
class GrocerycouponApi extends GroceryApiBase
{
    private static $fields = ['code', 'type', 'value', 'min_order_amount', 'max_discount', 'starts_at', 'ends_at', 'usage_limit', 'status'];

    public function index($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => $this->gridRows('GroceryCoupon', $data)]);
    }
    public function find($data) { $this->guard(); $this->findRow('GroceryCoupon', $data->id ?? 0); }
    public function save($data) { $this->guard(); $this->saveRow('GroceryCoupon', $data, self::$fields); }
    public function update($data) { $this->guard(); $this->updateRow('GroceryCoupon', $data->id ?? 0, $data, self::$fields); }
    public function delete($data) { $this->guard(); $this->deleteRow('GroceryCoupon', $data->id ?? 0); }
}