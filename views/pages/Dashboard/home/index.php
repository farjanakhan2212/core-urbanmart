<?php
global $base_url, $tx;

$buzz = GroceryService::buzz();
$cur = $buzz['shop']['currency'] ?? 'Tk';

$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$prevStart = date('Y-m-01', strtotime('first day of last month'));
$prevEnd = date('Y-m-t', strtotime('last day of last month'));

$salesToday = (float)GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE status='completed' AND sale_date = ?", [$today]);
$salesMonth = (float)GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE status='completed' AND sale_date >= ?", [$monthStart]);
$salesPrev = (float)GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE status='completed' AND sale_date BETWEEN ? AND ?", [$prevStart, $prevEnd]);
$purchMonth = (float)GroceryPurchase::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_purchases WHERE status='received' AND purchase_date >= ?", [$monthStart]);
$purchPrev = (float)GroceryPurchase::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_purchases WHERE status='received' AND purchase_date BETWEEN ? AND ?", [$prevStart, $prevEnd]);
$expenseMonth = (float)GroceryExpense::scalar("SELECT IFNULL(SUM(amount),0) FROM {$tx}grocery_expenses WHERE expense_date >= ?", [$monthStart]);
$expensePrev = (float)GroceryExpense::scalar("SELECT IFNULL(SUM(amount),0) FROM {$tx}grocery_expenses WHERE expense_date BETWEEN ? AND ?", [$prevStart, $prevEnd]);
$profitMonth = (float)GrocerySaleDetail::scalar("SELECT IFNULL(SUM((sd.selling_price - sd.cost_price) * sd.quantity - IFNULL(sd.discount,0)),0) FROM {$tx}grocery_sale_details sd JOIN {$tx}grocery_sales sa ON sa.id = sd.sale_id WHERE sa.status='completed' AND sa.sale_date >= ?", [$monthStart]);
$invoicesToday = (int)GrocerySale::scalar("SELECT COUNT(*) FROM {$tx}grocery_sales WHERE sale_date = ?", [$today]);

$summary = $buzz['summary'] ?? [];
$monthly = $buzz['monthly'] ?? [];
$recentSales = $buzz['recent_sales'] ?? [];
$recentOrders = $buzz['recent_orders'] ?? [];
$lowStockItems = $buzz['low_stock_items'] ?? [];
$topProducts = $buzz['top_products'] ?? [];
$categoryDist = $buzz['category_distribution'] ?? [];

$money = function ($v) use ($cur) {
    return $cur . ' ' . number_format((float)$v, 2);
};
$pct = function ($curV, $prevV) {
    if ((float)$prevV > 0) return ((float)$curV - (float)$prevV) / (float)$prevV * 100;
    return (float)$curV > 0 ? 100 : 0;
};
$trend = function ($curV, $prevV) use ($pct) {
    $p = $pct($curV, $prevV);
    $up = $p > 0.5;
    $down = $p < -0.5;
    $cls = $up ? 'up' : ($down ? 'down' : 'flat');
    $dot = $up ? '▲' : ($down ? '▼' : '•');
    return '<span class="trend-chip ' . $cls . '">' . $dot . ' ' . number_format(abs($p), 1) . '%</span>';
};

$maxBar = 0;
foreach ($monthly as $m) $maxBar = max($maxBar, (float)$m['sales'], (float)$m['purchases']);
$barH = function ($v) use ($maxBar) {
    return $maxBar > 0 ? max(4, (float)$v / $maxBar * 100) : 4;
};
$monthLabel = function ($ym) {
    return date('M', strtotime($ym . '-01'));
};

$catTotal = array_sum(array_map(function ($c) { return (int)$c['products']; }, $categoryDist));
$donutR = 54;
$donutC = 2 * M_PI * $donutR;
$donutColors = ['#2f6fed', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#64748b'];
$donutOffset = 0;
$greet = date('H') < 12 ? 'Good morning' : (date('H') < 18 ? 'Good afternoon' : 'Good evening');
$userName = isset($_SESSION['uname']) ? htmlspecialchars($_SESSION['uname']) : 'there';
?>
<div class="page-title-wrap">
    <div>
        <h1><?= $greet ?>, <?= $userName ?>!</h1>
        <div class="page-subtitle"><?= date('D, d M Y') ?> &middot; Here is what is happening at your store today.</div>
    </div>
    <div class="d-flex flex-wrap">
        <a class="btn btn-success mr-2 mb-2" href="<?= $base_url ?>/GroceryPos"><i class="fas fa-cash-register mr-1"></i>Open POS</a>
        <a class="btn btn-outline-primary mr-2 mb-2" href="<?= $base_url ?>/GroceryProduct/create"><i class="fas fa-plus mr-1"></i>Add Product</a>
        <a class="btn btn-outline-secondary mb-2" href="<?= $base_url ?>/GroceryReport"><i class="fas fa-chart-bar mr-1"></i>Reports</a>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-green"><i class="fas fa-cash-register"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Sales Today</div>
                    <div class="kpi-value"><?= $money($salesToday) ?></div>
                    <div class="kpi-note"><?= $invoicesToday ?> invoice(s) today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-indigo"><i class="fas fa-clipboard-list"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Today's Orders</div>
                    <div class="kpi-value"><?= (int)($summary['today_orders'] ?? 0) ?></div>
                    <div class="kpi-note"><?= (int)($buzz['pending_orders'] ?? 0) ?> pending order(s)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-blue"><i class="fas fa-boxes-stacked"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Total Products</div>
                    <div class="kpi-value"><?= (int)($buzz['product_count'] ?? 0) ?></div>
                    <div class="kpi-note"><?= (int)($summary['low_stock'] ?? 0) ?> low stock &middot; <?= (int)($summary['out_of_stock'] ?? 0) ?> out of stock</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-amber"><i class="fas fa-users"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Total Customers</div>
                    <div class="kpi-value"><?= (int)($buzz['customer_count'] ?? 0) ?></div>
                    <div class="kpi-note"><?= (int)($buzz['supplier_count'] ?? 0) ?> supplier(s)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-blue"><i class="fas fa-chart-line"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Sales This Month</div>
                    <div class="kpi-value"><?= $money($salesMonth) ?></div>
                    <div class="kpi-note">vs last month</div>
                </div>
                <div class="kpi-trend"><?= $trend($salesMonth, $salesPrev) ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-cyan"><i class="fas fa-truck"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Purchases This Month</div>
                    <div class="kpi-value"><?= $money($purchMonth) ?></div>
                    <div class="kpi-note">vs last month</div>
                </div>
                <div class="kpi-trend"><?= $trend($purchMonth, $purchPrev) ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-amber"><i class="fas fa-receipt"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Expenses This Month</div>
                    <div class="kpi-value"><?= $money($expenseMonth) ?></div>
                    <div class="kpi-note">vs last month</div>
                </div>
                <div class="kpi-trend"><?= $trend($expenseMonth, $expensePrev) ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-indigo"><i class="fas fa-piggy-bank"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Gross Profit This Month</div>
                    <div class="kpi-value"><?= $money($profitMonth) ?></div>
                    <div class="kpi-note"><?= $money($summary['gross_profit'] ?? 0) ?> all time</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-red"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Customer Receivables</div>
                    <div class="kpi-value"><?= $money($buzz['customer_due'] ?? 0) ?></div>
                    <div class="kpi-note">Purchase due: <?= $money($buzz['purchase_due'] ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-blue"><i class="fas fa-clipboard-list"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Pending Orders</div>
                    <div class="kpi-value"><?= (int)($buzz['pending_orders'] ?? 0) ?></div>
                    <div class="kpi-note"><?= (int)($summary['today_orders'] ?? 0) ?> order(s) today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-icon ic-amber"><i class="fas fa-boxes-stacked"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Low Stock Items</div>
                    <div class="kpi-value"><?= (int)($summary['low_stock'] ?? 0) ?></div>
                    <div class="kpi-note"><?= (int)($summary['out_of_stock'] ?? 0) ?> out of stock &middot; <?= (int)($summary['stock_value'] ?? 0) ?> stock value</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7 col-lg-6 mb-4">
        <div class="card dash-panel h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-chart-bar mr-2 text-primary"></i>Sales vs Purchases</h6>
                <div class="chart-legend">
                    <span class="legend-item"><span class="legend-dot dot-sales"></span>Sales</span>
                    <span class="legend-item"><span class="legend-dot dot-purchases"></span>Purchases</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!$maxBar) { ?>
                    <p class="text-center text-muted mb-0">Not enough data to build a chart yet.</p>
                <?php } else { ?>
                    <div class="chart-bars">
                        <?php foreach ($monthly as $m) { ?>
                            <div class="bar-col">
                                <div class="chart-group">
                                    <div class="bar sales" style="height: <?= $barH($m['sales']) ?>%;">
                                        <span class="chart-tooltip">Sales: <?= $money($m['sales']) ?></span>
                                    </div>
                                    <div class="bar purchases" style="height: <?= $barH($m['purchases']) ?>%;">
                                        <span class="chart-tooltip">Purchases: <?= $money($m['purchases']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="bar-labels">
                        <?php foreach ($monthly as $m) { ?>
                            <span class="bar-label"><?= $monthLabel($m['ym']) ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-6 mb-4">
        <div class="card dash-panel h-100">
            <div class="card-header">
                <h6><i class="fas fa-chart-pie mr-2 text-success"></i>Products by Category</h6>
            </div>
            <div class="card-body">
                <?php if (!$catTotal) { ?>
                    <p class="text-center text-muted mb-0">No products yet.</p>
                <?php } else { ?>
                    <div class="donut-wrap">
                        <div style="position:relative; width:150px; height:150px; flex-shrink:0;">
                            <svg width="150" height="150" viewBox="0 0 150 150">
                                <circle cx="75" cy="75" r="<?= $donutR ?>" fill="none" stroke="#eef2f7" stroke-width="16"></circle>
                                <?php
                                $accum = 0;
                                foreach ($categoryDist as $i => $c) {
                                    $share = $catTotal > 0 ? (int)$c['products'] / $catTotal : 0;
                                    $len = $share * $donutC;
                                    $color = $donutColors[$i % count($donutColors)];
                                    ?>
                                    <circle cx="75" cy="75" r="<?= $donutR ?>" fill="none" stroke="<?= $color ?>" stroke-width="16"
                                        stroke-dasharray="<?= $len ?> <?= $donutC - $len ?>" stroke-dashoffset="<?= -$accum ?>"
                                        transform="rotate(-90 75 75)"></circle>
                                    <?php $accum += $len;
                                }
                                ?>
                            </svg>
                            <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none;">
                                <span style="font-size:1.5rem; font-weight:800; color:#111827;"><?= $catTotal ?></span>
                                <span style="font-size:0.7rem; color:#6b7280;">products</span>
                            </div>
                        </div>
                        <div class="donut-legend">
                            <?php foreach ($categoryDist as $i => $c) { ?>
                                <div class="lg-row">
                                    <span class="lg-dot" style="background: <?= $donutColors[$i % count($donutColors)] ?>;"></span>
                                    <span class="lg-name" title="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></span>
                                    <span class="lg-val"><?= (int)$c['products'] ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card dash-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-trophy mr-2 text-warning"></i>Top Selling Products</h6>
                <a class="btn btn-sm btn-outline-primary" href="<?= $base_url ?>/GroceryReport">View Report</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover dash-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-right">Qty Sold</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$topProducts) echo '<tr><td colspan="4" class="text-center py-4 text-muted">No sales recorded yet.</td></tr>'; ?>
                        <?php $rn = 1; foreach ($topProducts as $t) { ?>
                            <tr>
                                <td class="text-muted"><?= $rn++ ?></td>
                                <td><?= htmlspecialchars($t['name'] ?? '') ?></td>
                                <td class="amount"><?= number_format((float)$t['qty'], 2) ?></td>
                                <td class="amount"><?= $money($t['amount']) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card dash-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-box-open mr-2 text-danger"></i>Low Stock Alert</h6>
                <a class="btn btn-sm btn-outline-warning" href="<?= $base_url ?>/GroceryStock">Manage</a>
            </div>
            <div class="card-body py-2">
                <?php if (!$lowStockItems) { ?>
                    <p class="text-center text-muted my-3 mb-0">All products are well stocked.</p>
                <?php } else { ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($lowStockItems as $l) { ?>
                            <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center pr-2" style="min-width:0;">
                                    <span class="badge-soft <?= (float)$l['stock_quantity'] <= 0 ? 'danger' : 'warning' ?> mr-2">
                                        <i class="fas fa-box-open"></i>
                                    </span>
                                    <div class="text-truncate" style="min-width:0;">
                                        <div class="font-weight-bold text-truncate" style="font-size:0.85rem;"><?= htmlspecialchars($l['name']) ?></div>
                                        <div class="text-muted" style="font-size:0.75rem;"><?= (float)$l['stock_quantity'] ?> in stock &middot; reorder at <?= (float)$l['reorder_level'] ?></div>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7 col-lg-7 mb-4">
        <div class="card dash-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-file-invoice mr-2 text-primary"></i>Recent Invoices</h6>
                <a class="btn btn-sm btn-outline-success" href="<?= $base_url ?>/GrocerySale">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover dash-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$recentSales) echo '<tr><td colspan="6" class="text-center py-4 text-muted">No invoices yet.</td></tr>'; ?>
                        <?php foreach ($recentSales as $s) {
                            $pstat = $s['payment_status'] ?? '';
                            $pcls = $pstat === 'paid' ? 'success' : ($pstat === 'partial' ? 'warning' : 'danger');
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($s['invoice_no']) ?></td>
                                <td><?= htmlspecialchars($s['sale_date']) ?></td>
                                <td><?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></td>
                                <td class="amount"><?= $money($s['grand_total']) ?></td>
                                <td><span class="badge-soft <?= $pcls ?>"><?= htmlspecialchars($pstat ?: '—') ?></span></td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= $base_url ?>/GrocerySale/invoice/<?= (int)$s['id'] ?>" target="_blank"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5 mb-4">
        <div class="card dash-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-clipboard-check mr-2 text-info"></i>Recent Orders</h6>
                <a class="btn btn-sm btn-outline-info" href="<?= $base_url ?>/GroceryOrder">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover dash-table mb-0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$recentOrders) echo '<tr><td colspan="4" class="text-center py-4 text-muted">No orders yet.</td></tr>'; ?>
                        <?php foreach ($recentOrders as $o) {
                            $ostatus = $o['order_status'] ?? '';
                            $ocls = in_array($ostatus, ['completed', 'delivered', 'paid']) ? 'success' : (in_array($ostatus, ['cancelled', 'failed']) ? 'danger' : 'info');
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($o['order_no']) ?></td>
                                <td><?= htmlspecialchars($o['customer_name'] ?? '—') ?></td>
                                <td class="amount"><?= $money($o['grand_total']) ?></td>
                                <td><span class="badge-soft <?= $ocls ?>"><?= htmlspecialchars($ostatus ?: '—') ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card dash-panel">
            <div class="card-header">
                <h6><i class="fas fa-bolt mr-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryPos"><i class="fas fa-cash-register"></i> New Sale (POS)</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryPurchase"><i class="fas fa-truck"></i> New Purchase</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryOrder"><i class="fas fa-clipboard-list"></i> New Order</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryProduct/create"><i class="fas fa-plus"></i> Add Product</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryStock/adjustments"><i class="fas fa-sliders-h"></i> Adjust Stock</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryExpense"><i class="fas fa-receipt"></i> Add Expense</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryCustomer"><i class="fas fa-users"></i> Customers</a></div>
                    <div class="col-sm-6 col-lg-3 mb-2"><a class="quick-action" href="<?= $base_url ?>/GroceryReport"><i class="fas fa-chart-bar"></i> Reports &amp; KPIs</a></div>
                </div>
            </div>
        </div>
    </div>
</div>