<?php
class GroceryReportController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        global $tx;
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $salesToday = GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE sale_date = ?", [$today]);
        $salesMonth = GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE sale_date >= ?", [$monthStart]);
        $purchMonth = GroceryPurchase::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_purchases WHERE purchase_date >= ?", [$monthStart]);
        $expenseMonth = GroceryExpense::scalar("SELECT IFNULL(SUM(amount),0) FROM {$tx}grocery_expenses WHERE expense_date >= ?", [$monthStart]);
        $dueTotal = GrocerySale::scalar("SELECT IFNULL(SUM(due_amount),0) FROM {$tx}grocery_sales WHERE due_amount > 0");
        $topRows = [];
        $tStmt = GrocerySaleDetail::exec(
            "SELECT sd.product_id, p.name, p.sku, SUM(sd.quantity) AS qty, SUM(sd.subtotal) AS amount
             FROM {$tx}grocery_sale_details sd
             LEFT JOIN {$tx}grocery_products p ON p.id = sd.product_id
             LEFT JOIN {$tx}grocery_sales sa ON sa.id = sd.sale_id
             WHERE sa.sale_date >= ?
             GROUP BY sd.product_id, p.name, p.sku
             ORDER BY qty DESC LIMIT 10",
            [$monthStart]
        );
        if ($tStmt) {
            $tRes = $tStmt->get_result();
            while ($r = $tRes->fetch_assoc()) $topRows[] = $r;
            $tStmt->close();
        }
        $this->view([
            'salesToday' => $salesToday, 'salesMonth' => $salesMonth, 'purchMonth' => $purchMonth,
            'expenseMonth' => $expenseMonth, 'dueTotal' => $dueTotal, 'top_products' => $topRows,
        ]);
    }

    public function top_products()
    {
        global $tx;
        $monthStart = date('Y-m-01');
        $rows = [];
        $stmt = GrocerySaleDetail::exec(
            "SELECT sd.product_id, p.name, p.sku, SUM(sd.quantity) AS qty, SUM(sd.subtotal) AS amount
             FROM {$tx}grocery_sale_details sd
             LEFT JOIN {$tx}grocery_products p ON p.id = sd.product_id
             WHERE sd.created_at >= ?
             GROUP BY sd.product_id, p.name, p.sku
             ORDER BY qty DESC LIMIT 10",
            [$monthStart]
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        $this->view(['rows' => $rows]);
    }
}