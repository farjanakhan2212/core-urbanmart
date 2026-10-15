<?php
$gclass = $_GET['class'] ?? 'GroceryCategory';
$gmeta = grocery_entity_meta($gclass);
if (!$gmeta) { echo 'Unknown entity'; return; }
$gpage = (int)($_GET['page'] ?? 1);
$gq = isset($_GET['q']) ? trim($_GET['q']) : '';
$glist = call_user_func([$gclass, 'grid'], $gpage, 20, $gq);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => $gmeta['label']]);
Html::link(['class' => 'btn btn-success', 'route' => $gclass . '/create', 'text' => $gmeta['create']]);
echo '<form method="get" class="form-inline mb-3 float-right">
         <input type="hidden" name="class" value="' . $gclass . '">
         <input type="text" name="q" value="' . htmlspecialchars($gq) . '" class="form-control form-control-sm mr-1" placeholder="Search ' . $gmeta['search'] . '...">
         <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr>';
foreach ($gmeta['headers'] as $h) echo '<th>' . htmlspecialchars($h) . '</th>';
echo '<th class="text-right">Actions</th></tr></thead><tbody>';
if (empty($glist['rows'])) {
    echo '<tr><td colspan="' . (count($gmeta['headers']) + 1) . '" class="text-center">No records found</td></tr>';
} else {
    foreach ($glist['rows'] as $grow) {
        echo '<tr>';
        foreach ($gmeta['columns'] as $gi => $col) {
            echo '<td>';
            if ($col === 'status' && !empty($gmeta['status'])) {
                $sv = $grow[$col] ?? '';
                if (is_numeric($sv) && $sv !== '') $sv = (int)$sv === 1 ? 'active' : 'inactive';
                grocery_status_badge($sv);
            } elseif ($col === 'image') {
                grocery_product_image($grow[$col] ?? '');
            } else {
                echo htmlspecialchars((string)($grow[$col] ?? ''));
            }
            echo '</td>';
        }
        echo '<td class="text-right">';
        grocery_options($gclass, $grow['id']);
        echo '</td></tr>';
    }
}
echo '</tbody></table></div>';
grocery_paginate_links($gpage, $glist['total'] ?? 0, 20);
Card::close();
Col::close();
Row::close();
Page::close();