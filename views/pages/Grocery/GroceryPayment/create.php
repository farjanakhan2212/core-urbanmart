<?php
$methods = GroceryPaymentMethod::rows('status = 1', [], 'name ASC');
?>
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">New Payment</h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GroceryPayment/save">
                    <div class="form-group">
                        <label class="small">Payable For</label>
                        <select name="payable_type" class="form-control" id="pay-type">
                            <option value="sale">Sale / Invoice</option>
                            <option value="purchase">Purchase</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Invoice / Purchase ID</label>
                        <input type="number" name="payable_id" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="small">Customer ID (for sales, optional)</label>
                        <input type="number" name="customer_id" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label class="small">Supplier ID (for purchases, optional)</label>
                        <input type="number" name="supplier_id" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label class="small">Amount</label>
                        <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="small">Payment Method</label>
                        <select name="payment_method_id" class="form-control">
                            <?php foreach ($methods as $m) echo '<option value="' . $m['id'] . '">' . htmlspecialchars($m['name']) . '</option>'; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Transaction Ref</label>
                        <input type="text" name="transaction_id" class="form-control" placeholder="Bank / mobile ref">
                    </div>
                    <button class="btn btn-success btn-block">Save Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>