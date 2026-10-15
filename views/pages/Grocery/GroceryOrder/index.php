<?php
$data = $groceryorder ?? null;
$page = $data ? (int)$data->page : 1;
$status = $data ? (string)$data->status : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Orders']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryOrder/create', 'text' => 'New Order']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryOrder">
        <select name="status" class="form-control form-control-sm mr-1">
            <option value="">All statuses</option>
            <option value="pending"' . ($status === 'pending' ? ' selected' : '') . '>Pending</option>
            <option value="confirmed"' . ($status === 'confirmed' ? ' selected' : '') . '>Confirmed</option>
            <option value="processing"' . ($status === 'processing' ? ' selected' : '') . '>Processing</option>
            <option value="packed"' . ($status === 'packed' ? ' selected' : '') . '>Packed</option>
            <option value="shipped"' . ($status === 'shipped' ? ' selected' : '') . '>Shipped</option>
            <option value="delivered"' . ($status === 'delivered' ? ' selected' : '') . '>Delivered</option>
            <option value="cancelled"' . ($status === 'cancelled' ? ' selected' : '') . '>Cancelled</option>
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>Order</th><th>Date</th><th>Customer</th><th class="text-right">Total</th><th class="text-right">Due</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
foreach ($rows as $r) {
    $due = (float)($r['due_amount'] ?? 0);
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['order_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['order_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['customer_name'] ?? '') . '</td>';
    echo '<td class="text-right">' . grocery_money($r['grand_total'] ?? 0) . '</td>';
    echo '<td class="text-right' . ($due > 0 ? ' text-danger' : '') . '">' . grocery_money($due) . '</td>';
    echo '<td>'; grocery_status_badge($r['order_status'] ?? ''); echo '</td>';
    echo '<td class="text-nowrap">';
    $st = $r['order_status'] ?? '';
    if (in_array($st, ['', 'pending'])) {
        echo '<a class="btn btn-sm btn-outline-secondary mr-1" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=confirmed">Confirm</a>';
    }
    if (in_array($st, ['pending', 'confirmed'])) {
        echo '<a class="btn btn-sm btn-outline-primary mr-1" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=processing">Process</a>';
    }
    if (in_array($st, ['processing', 'confirmed'])) {
        echo '<a class="btn btn-sm btn-outline-warning mr-1" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=packed">Pack</a>';
    }
    echo '<a class="btn btn-sm btn-outline-info mr-1" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=shipped">Ship</a>';
    echo '<a class="btn btn-sm btn-outline-success" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=delivered">Deliver</a>';
    if (!in_array($st, ['cancelled', 'delivered'])) {
        echo '<a class="btn btn-sm btn-outline-danger ml-1" href="' . $base_url . '/GroceryOrder/status/' . $r['id'] . '?st=cancelled">Cancel</a>';
    }
    echo '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();