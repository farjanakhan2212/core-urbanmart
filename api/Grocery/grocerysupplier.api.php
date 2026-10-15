<?php

/**
 * UrbanMart Grocery - Supplier API
 * /api/Grocerysupplier (index) | find | save | update | delete
 */
class GrocerysupplierApi extends GroceryApiBase
{
    private static $fields = ['name', 'company', 'phone', 'email', 'address', 'city', 'opening_balance', 'status'];

    public function index($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => $this->gridRows('GrocerySupplier', $data)]);
    }
    public function find($data) { $this->guard(); $this->findRow('GrocerySupplier', $data->id ?? 0); }
    public function save($data) { $this->guard(); $this->saveRow('GrocerySupplier', $data, self::$fields); }
    public function update($data) { $this->guard(); $this->updateRow('GrocerySupplier', $data->id ?? 0, $data, self::$fields); }
    public function delete($data) { $this->guard(); $this->deleteRow('GrocerySupplier', $data->id ?? 0); }
}