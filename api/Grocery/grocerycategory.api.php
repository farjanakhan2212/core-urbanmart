<?php

/**
 * UrbanMart Grocery - Category API
 * /api/Grocerycategory (index,q,page,perpage) | find | save | update | delete
 * All endpoints require a valid Bearer token.
 */
class GrocerycategoryApi extends GroceryApiBase
{
    private static $fields = ['name', 'slug', 'image', 'description', 'status', 'sort_order'];

    public function index($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => $this->gridRows('GroceryCategory', $data)]);
    }
    public function find($data) { $this->guard(); $this->findRow('GroceryCategory', $data->id ?? 0); }
    public function save($data) { $this->guard(); $this->saveRow('GroceryCategory', $data, self::$fields); }
    public function update($data) { $this->guard(); $this->updateRow('GroceryCategory', $data->id ?? 0, $data, self::$fields); }
    public function delete($data) { $this->guard(); $this->deleteRow('GroceryCategory', $data->id ?? 0); }
}