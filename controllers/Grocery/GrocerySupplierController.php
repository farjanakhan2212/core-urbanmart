<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GrocerySupplierController extends GroceryCrudController
{
    protected $model = 'GrocerySupplier';
    protected $formFields = ['name', 'company', 'contact_person', 'phone', 'email', 'address', 'status'];
}