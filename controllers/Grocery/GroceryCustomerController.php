<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryCustomerController extends GroceryCrudController
{
    protected $model = 'GroceryCustomer';
    protected $formFields = ['name', 'phone', 'email', 'address', 'status'];
}