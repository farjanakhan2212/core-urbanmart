<?php

/**
 * UrbanMart Grocery - Customer API
 * /api/Grocerycustomer (index) | find | save | update | delete
 */
class GrocerycustomerApi extends GroceryApiBase
{
    private static $fields = ['name', 'phone', 'email', 'address', 'city', 'district', 'opening_balance', 'status'];

    public function index($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => $this->gridRows('GroceryCustomer', $data)]);
    }
    public function find($data) { $this->guard(); $this->findRow('GroceryCustomer', $data->id ?? 0); }
    public function save($data) { $this->guard(); $this->saveRow('GroceryCustomer', $data, self::$fields); }
    public function update($data) { $this->guard(); $this->updateRow('GroceryCustomer', $data->id ?? 0, $data, self::$fields); }
    public function delete($data) { $this->guard(); $this->deleteRow('GroceryCustomer', $data->id ?? 0); }
}