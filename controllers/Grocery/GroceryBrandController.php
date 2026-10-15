<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryBrandController extends GroceryCrudController
{
    protected $model = 'GroceryBrand';
    protected $formFields = ['name', 'slug', 'description', 'status'];
}