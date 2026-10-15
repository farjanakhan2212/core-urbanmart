<?php
class GroceryExpenseController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = GroceryExpense::manager($page, 20, $q);
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function create()
    {
        $this->view([]);
    }

    public function edit($id, $obj = null, $file = null)
    {
        $this->view(['id' => (int)$id]);
    }

    public function save($obj = null, $file = null)
    {
        GroceryExpense::insertRow([
            'expense_no' => GroceryExpense::nextSerial('EXP', 'expense_no'),
            'expense_category_id' => $_POST['expense_category_id'] ?? null,
            'amount' => $_POST['amount'] ?? 0,
            'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'remark' => $_POST['remark'] ?? '',
            'created_by' => isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        redirect('index');
    }

    public function update($id, $obj = null, $file = null)
    {
        GroceryExpense::updateRow((int)$id, [
            'expense_category_id' => $_POST['expense_category_id'] ?? null,
            'amount' => $_POST['amount'] ?? 0,
            'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'remark' => $_POST['remark'] ?? '',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        redirect('index');
    }

    public function delete($id, $obj = null, $file = null)
    {
        GroceryExpense::deleteRow((int)$id);
        redirect('index');
    }
}