<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryCategoryController extends GroceryCrudController
{
    protected $model = 'GroceryCategory';
    protected $formFields = ['name', 'slug', 'description', 'status'];
}