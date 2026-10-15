<?php
$data = $grocerydelivery ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
function _delivery_badge($statusName)
{
    $map = [
        'pending' => 'warning', 'assigned' => 'info', 'picked up' => 'primary',
        'in transit' => 'primary', 'delivered' => 'success', 'failed' => 'danger', 'cancelled' => 'secondary',
    ];
    $color = $map[strtolower(trim((string)$statusName))] ?? 'secondary';
    echo '<span class="badge badge-' . $color . '">' . htmlspecialchars((string)$statusName) . '</span>';
}
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Deliveries']);
Html::link(['class' => 'btn btn-primary', 'route' => 'GroceryOrder', 'text' => 'Assign From Orders']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryDelivery">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search delivery/order/rider...">
        <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>#</th><th>Delivery No</th><th>Delivery Date</th><th>Order</th><th>Customer</th><th>Phone</th><th>Rider</th><th>Address</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . $r['id'] . '</td>';
    echo '<td>' . htmlspecialchars($r['delivery_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['delivery_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['order_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['customer_name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['customer_phone'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['rider_name'] ?? '');
    if (!empty($r['rider_phone'])) echo '<br><small>' . htmlspecialchars($r['rider_phone']) . '</small>';
    echo '</td>';
    echo '<td class="small">' . htmlspecialchars($r['address'] ?? '') . '</td>';
    echo '<td>'; _delivery_badge($r['status_name'] ?? ''); echo '</td>';
    echo '<td class="text-nowrap">';
    echo '<a class="btn btn-sm btn-outline-info mr-1" href="' . $base_url . '/GroceryDelivery/status/' . $r['id'] . '?s=2">Assigned</a>';
    echo '<a class="btn btn-sm btn-outline-primary mr-1" href="' . $base_url . '/GroceryDelivery/status/' . $r['id'] . '?s=3">Picked Up</a>';
    echo '<a class="btn btn-sm btn-outline-warning mr-1" href="' . $base_url . '/GroceryDelivery/status/' . $r['id'] . '?s=4">In Transit</a>';
    echo '<a class="btn btn-sm btn-outline-success" href="' . $base_url . '/GroceryDelivery/status/' . $r['id'] . '?s=5">Delivered</a>';
    echo '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="10" class="text-center">No deliveries found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();