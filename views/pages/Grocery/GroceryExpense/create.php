<?php
$data = $groceryexpense ?? null;
$id = $data ? (int)$data->id : 0;
$row = $id > 0 ? GroceryExpense::findRow($id) : null;
$cats = GroceryExpenseCategory::rows('status = 1', [], 'name ASC');
?>
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><?= $row ? 'Edit Expense' : 'New Expense' ?></h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GroceryExpense/<?= $row ? 'update/' . $row['id'] : 'save' ?>">
                    <div class="form-group">
                        <label class="small">Category</label>
                        <select name="expense_category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($cats as $c) echo '<option value="' . $c['id'] . '"' . ($row && $row['expense_category_id'] == $c['id'] ? ' selected' : '') . '>' . htmlspecialchars($c['name']) . '</option>'; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" value="<?= $row ? htmlspecialchars($row['amount']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="small">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= $row ? htmlspecialchars($row['expense_date']) : date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="small">Remark</label>
                        <textarea name="remark" class="form-control" rows="2"><?= $row ? htmlspecialchars($row['remark'] ?? '') : '' ?></textarea>
                    </div>
                    <button class="btn btn-success btn-block">Save Expense</button>
                </form>
            </div>
        </div>
    </div>
</div>