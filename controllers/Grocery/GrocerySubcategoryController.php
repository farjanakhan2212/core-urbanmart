<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GrocerySubcategoryController extends GroceryCrudController
{
    protected $model = 'GrocerySubcategory';
    protected $formFields = ['name', 'category_id', 'slug', 'description', 'status'];
}