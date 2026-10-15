<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryExpenseCategoryController extends GroceryCrudController
{
    protected $model = 'GroceryExpenseCategory';
    protected $formFields = ['name', 'description', 'status'];
}