<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryUnitController extends GroceryCrudController
{
    protected $model = 'GroceryUnit';
    protected $formFields = ['name', 'short_name', 'status'];
}