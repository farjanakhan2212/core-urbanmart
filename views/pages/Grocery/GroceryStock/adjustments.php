<?php
$data = $grocerystock ?? null;
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Stock Adjustments']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryStock/adjustment', 'text' => 'New Adjustment']);
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>No</th><th>Date</th><th>Type</th><th>Reason</th><th>Status</th><th>By</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['adjustment_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['created_at'] ?? '') . '</td>';
    echo '<td class="text-center">'; grocery_status_badge($r['adjustment_type'] ?? ''); echo '</td>';
    echo '<td>' . htmlspecialchars($r['reason'] ?? '') . '</td>';
    echo '<td>'; grocery_status_badge($r['status'] ?? ''); echo '</td>';
    echo '<td class="small">' . htmlspecialchars($r['created_by'] ?? '') . '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="6" class="text-center">No adjustments found</td></tr>';
echo '</tbody></table></div>';
Card::close();
Col::close();
Row::close();
Page::close();