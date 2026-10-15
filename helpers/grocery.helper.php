<?php

function grocery_json($data)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// ---- Public registration -------------------------------------------------
// Roles that must never be selectable by a self-registering public user.
function grocery_privileged_roles()
{
    return ['admin', 'super admin', 'superadmin', 'system administrator', 'developer', 'manager', 'owner'];
}

/**
 * Roles a public user is allowed to register with. Fetches existing
 * core_roles rows and filters out privileged + demo/test roles so the
 * dropdown only exposes safe roles already supported by the project.
 */
function grocery_safe_roles()
{
    global $db, $tx;
    $blocked = array_map('strtolower', grocery_privileged_roles());
    $safe = [];
    $result = $db->query("SELECT id, name FROM {$tx}roles ORDER BY id ASC");
    if (!$result) return $safe;
    while ($role = $result->fetch_assoc()) {
        $name = strtolower(trim($role['name']));
        if (in_array($name, $blocked, true)) continue;
        if (preg_match('/demo|test|dummy|^dem/i', $name)) continue;
        $safe[] = ['id' => (int)$role['id'], 'name' => trim($role['name'])];
    }
    return $safe;
}

function grocery_role_is_safe($roleId)
{
    $roleId = (int)$roleId;
    foreach (grocery_safe_roles() as $role) {
        if ((int)$role['id'] === $roleId) return true;
    }
    return false;
}

function grocery_default_role_id()
{
    $safe = grocery_safe_roles();
    return $safe ? (int)$safe[0]['id'] : 0;
}

/**
 * Registers a new user in the existing core_users table.
 *
 * The core_users table only has: id, name, email, branch, email_verified_at,
 * password, remember_token, created_at, updated_at. There is NO full_name,
 * phone, company or role_id column, so only the columns that actually exist
 * are saved. The username (name column) is generated from the email and made
 * unique when not provided.
 *
 * @param array $data fields: username (optional, stored in name), email,
 *                     password, confirm_password, terms
 * @return array ['success'=>bool, 'message'=>string, 'user'=>array|null, 'errors'=>array]
 */
function grocery_register_user(array $data)
{
    global $db, $tx, $now;

    $errors = [];
    $username = trim(isset($data['username']) ? (string)$data['username'] : '');
    $email = trim(strtolower(isset($data['email']) ? (string)$data['email'] : ''));
    $password = isset($data['password']) ? (string)$data['password'] : '';
    $confirm = isset($data['confirm_password']) ? (string)$data['confirm_password'] : '';
    $terms = !empty($data['terms']);

    // ---- validation ----
    if ($username !== '' && !preg_match('/^[a-z0-9_]{3,50}$/i', $username)) {
        $errors['username'] = 'Username must be 3-50 characters (letters, numbers, underscore).';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (strlen($email) > 50) {
        $errors['email'] = 'Email must not exceed 50 characters.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif (strlen($password) > 72) {
        $errors['password'] = 'Password must not exceed 72 characters.';
    }

    if ($confirm === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$terms) {
        $errors['terms'] = 'You must agree to the Terms & Conditions and Privacy Policy.';
    }

    if ($errors) {
        return ['success' => false, 'message' => 'Please fix the highlighted fields.', 'user' => null, 'errors' => $errors];
    }

    // ---- duplicate email ----
    $stmt = $db->prepare("SELECT id FROM {$tx}users WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'An account with this email already exists.', 'user' => null, 'errors' => ['email' => 'An account with this email already exists.']];
        }
        $stmt->close();
    }

    // ---- unique username (name column) ----
    $base = preg_replace('/[^a-z0-9_]/', '', strtolower(explode('@', $email)[0]));
    if ($base === '') $base = 'user';
    $base = substr($base, 0, 42);
    $username = ($username !== '') ? strtolower($username) : $base;
    $suffix = 1;
    $stmt = $db->prepare("SELECT id FROM {$tx}users WHERE name = ? LIMIT 1");
    while ($username !== '' && $stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) break;
        $stmt->free_result();
        $username = $base . '_' . $suffix++;
        if ($suffix > 9) $username = substr($base, 0, 40) . '_' . str_pad($suffix, 4, '0', STR_PAD_LEFT);
    }
    if ($stmt) $stmt->close();

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $insert = $db->prepare("INSERT INTO {$tx}users
        (name, email, password, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?)");
    if (!$insert) {
        return ['success' => false, 'message' => 'Registration could not be completed. Please try again.', 'user' => null, 'errors' => []];
    }
    $insert->bind_param('sssss', $username, $email, $hash, $now, $now);
    $ok = $insert->execute();
    $newId = (int)$insert->insert_id;
    if (!$ok) {
        $insert->close();
        return ['success' => false, 'message' => 'Registration could not be completed. Please try again.', 'user' => null, 'errors' => []];
    }
    $insert->close();

    return [
        'success' => true,
        'message' => 'Account created successfully. Welcome to UrbanMart Grocery!',
        'user' => ['id' => $newId, 'name' => $username, 'email' => $email],
        'errors' => [],
    ];
}

function grocery_currency()
{
    return GrocerySetting::get('currency_symbol', 'Tk');
}

function grocery_money($amount)
{
    $amount = (float)$amount;
    $c = grocery_currency();
    if ($c === 'Tk' || $c === 'BDT') return 'Tk ' . number_format($amount, 2);
    return $c . ' ' . number_format($amount, 2);
}

function grocery_status_badge($status)
{
    $label = (string)$status;
    $map = [
        'active' => 'success', 'inactive' => 'secondary', 'archived' => 'warning',
        'paid' => 'success', 'partial' => 'warning', 'unpaid' => 'danger',
        'completed' => 'success', 'cancelled' => 'danger', 'refunded' => 'info',
        'received' => 'success', 'pending' => 'warning', 'ordered' => 'info',
        'processing' => 'primary', 'shipped' => 'info', 'delivered' => 'success',
        'draft' => 'secondary', 'approved' => 'primary', 'rejected' => 'danger',
        'in' => 'success', 'out' => 'danger', 'adjustment' => 'info',
        'purchase_in' => 'success', 'opening' => 'success', 'adjustment_in' => 'info', 'return_in' => 'success',
        'sale_out' => 'danger', 'damage' => 'warning', 'adjustment_out' => 'danger',
    ];
    $pretty = [
        'purchase_in' => 'Stock In', 'opening' => 'Opening', 'adjustment_in' => 'Adjustment In', 'return_in' => 'Return In',
        'sale_out' => 'Stock Out', 'damage' => 'Damage', 'adjustment_out' => 'Adjustment Out',
    ];
    if (isset($pretty[$label])) $label = $pretty[$label];
    $color = $map[strtolower($label)] ?? $map[strtolower((string)$status)] ?? 'secondary';
    echo '<span class="badge badge-' . $color . '">' . htmlspecialchars($label) . '</span>';
}

function grocery_product_image($image)
{
    global $base_url;
    $src = $image ? htmlspecialchars($image) : 'grocery-1232944-1280-jpg.jpg';
    echo '<img src="' . $base_url . '/img/' . $src . '" class="img-thumbnail" style="max-width:60px;max-height:50px;object-fit:cover" alt="">';
}

function grocery_options($class, $id)
{
    $base = $class ? $class : (isset($_GET['class']) ? $_GET['class'] : 'GroceryProduct');
    global $base_url;
    echo '<a class="btn btn-sm btn-primary" href="' . $base_url . '/' . $base . '/edit/' . $id . '">Edit</a> ';
    echo '<a class="btn btn-sm btn-danger"
       onclick="if(confirm(\'Delete this record?\')){window.location=\'' . $base_url . '/' . $base . '/delete/' . $id . '\'}"
       href="javascript:void(0)">Delete</a>';
}

function grocery_paginate_links($page, $total, $per)
{
    $totalPages = max(1, ceil($total / max(1, $per)));
    $class = isset($_GET['class']) ? $_GET['class'] : '';
    $q = isset($_GET['q']) ? urlencode($_GET['q']) : '';
    $html = '<nav><ul class="pagination pagination-sm">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === (int)$page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="?class=' . $class . '&page=' . $i . ($q ? '&q=' . $q : '') . '">' . $i . '</a></li>';
    }
    $html .= '</ul></nav>';
    echo $html;
}

function grocery_rows($obj)
{
    if (is_object($obj) && isset($obj->list->rows)) {
        return array_map(function ($r) { return (array)$r; }, $obj->list->rows);
    }
    if (is_array($obj) && isset($obj['list']['rows'])) {
        return array_map(function ($r) { return (array)$r; }, $obj['list']['rows']);
    }
    return [];
}

function grocery_total($obj)
{
    if (is_object($obj) && isset($obj->list->total)) return (int)$obj->list->total;
    if (is_array($obj) && isset($obj['list']['total'])) return (int)$obj['list']['total'];
    return 0;
}

function grocery_list_rows($obj, $key)
{
    if (is_object($obj) && isset($obj->{$key})) {
        $v = $obj->{$key};
        return is_array($v) ? array_map(function ($r) { return (array)$r; }, $v) : [];
    }
    if (is_array($obj) && isset($obj[$key])) {
        $v = $obj[$key];
        return is_array($v) ? array_map(function ($r) { return (array)$r; }, $v) : [];
    }
    return [];
}

function grocery_post_value($obj, $key, $default = '')
{
    if (is_object($obj) && isset($obj->{$key})) {
        return htmlspecialchars((string)$obj->{$key});
    }
    if (is_array($obj) && isset($obj[$key])) {
        return htmlspecialchars((string)$obj[$key]);
    }
    return isset($default) ? $default : '';
}

function grocery_collect_fields($obj, array $fields)
{
    $out = [];
    $src = is_object($obj) ? (array)$obj : $obj;
    foreach ($fields as $f) {
        if (array_key_exists($f, $src) && $src[$f] !== '') {
            $out[$f] = $src[$f];
        }
    }
    return $out;
}

function grocery_entity_meta($class)
{
    $meta = [
        'GroceryCategory' => [
            'label' => 'Categories', 'create' => 'Create Category',
            'columns' => ['name', 'slug', 'status'],
            'headers' => ['Name', 'Slug', 'Status'],
            'search' => 'name',
            'status' => true,
        ],
        'GrocerySubcategory' => [
            'label' => 'Subcategories', 'create' => 'Create Subcategory',
            'columns' => ['name', 'category_name', 'status'],
            'headers' => ['Name', 'Category', 'Status'],
            'search' => 'name', 'status' => true,
        ],
        'GroceryBrand' => [
            'label' => 'Brands', 'create' => 'Create Brand',
            'columns' => ['name', 'slug', 'status'],
            'headers' => ['Name', 'Slug', 'Status'],
            'search' => 'name', 'status' => true,
        ],
        'GroceryUnit' => [
            'label' => 'Units', 'create' => 'Create Unit',
            'columns' => ['name', 'short_name', 'status'],
            'headers' => ['Name', 'Short', 'Status'],
            'search' => 'name', 'status' => true,
        ],
        'GrocerySupplier' => [
            'label' => 'Suppliers', 'create' => 'Create Supplier',
            'columns' => ['name', 'company', 'phone', 'email', 'status'],
            'headers' => ['Name', 'Company', 'Phone', 'Email', 'Status'],
            'search' => 'name', 'status' => true,
        ],
        'GroceryCustomer' => [
            'label' => 'Customers', 'create' => 'Create Customer',
            'columns' => ['name', 'phone', 'email', 'address', 'status'],
            'headers' => ['Name', 'Phone', 'Email', 'Address', 'Status'],
            'search' => 'name', 'status' => true,
        ],
        'GroceryCoupon' => [
            'label' => 'Coupons', 'create' => 'Create Coupon',
            'columns' => ['code', 'type', 'value', 'status'],
            'headers' => ['Code', 'Type', 'Value', 'Status'],
            'search' => 'code', 'status' => true,
        ],
        'GroceryExpenseCategory' => [
            'label' => 'Expense Categories', 'create' => 'Create Expense Category',
            'columns' => ['name', 'status'],
            'headers' => ['Name', 'Status'],
            'search' => 'name', 'status' => true,
        ],
    ];
    return $meta[$class] ?? null;
}

function grocery_form_spec($class)
{
    $spec = [
        'GroceryCategory' => [
            ['name', 'Name', 'text', ''],
            ['slug', 'Slug', 'text', ''],
            ['description', 'Description', 'textarea', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GrocerySubcategory' => [
            ['name', 'Name', 'text', ''],
            ['category_id', 'Category', 'fk', 'grocery_categories'],
            ['slug', 'Slug', 'text', ''],
            ['description', 'Description', 'textarea', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GroceryBrand' => [
            ['name', 'Name', 'text', ''],
            ['slug', 'Slug', 'text', ''],
            ['description', 'Description', 'textarea', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GroceryUnit' => [
            ['name', 'Name', 'text', ''],
            ['short_name', 'Short Name', 'text', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GrocerySupplier' => [
            ['name', 'Name', 'text', ''],
            ['company', 'Company', 'text', ''],
            ['phone', 'Phone', 'text', ''],
            ['email', 'Email', 'email', ''],
            ['address', 'Address', 'textarea', ''],
            ['city', 'City', 'text', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GroceryCustomer' => [
            ['name', 'Name', 'text', ''],
            ['phone', 'Phone', 'text', ''],
            ['email', 'Email', 'email', ''],
            ['address', 'Address', 'textarea', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'GroceryCoupon' => [
            ['code', 'Coupon Code', 'text', ''],
            ['type', 'Discount Type', 'enum', ['percent' => 'Percent', 'fixed' => 'Fixed']],
            ['value', 'Value', 'number', ''],
            ['min_order_amount', 'Min Order Amount', 'number', ''],
            ['max_discount', 'Max Discount', 'number', ''],
            ['starts_at', 'Starts At', 'datetime', ''],
            ['ends_at', 'Expires At', 'datetime', ''],
            ['usage_limit', 'Usage Limit', 'number', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive', 'expired' => 'Expired']],
        ],
        'GroceryExpenseCategory' => [
            ['name', 'Name', 'text', ''],
            ['status', 'Status', 'enum', ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
    ];
    return $spec[$class] ?? null;
}

function grocery_render_form($class, $row = null)
{
    $spec = grocery_form_spec($class);
    if (!$spec) return;
    foreach ($spec as $fs) {
        list($field, $label, $type, $opt) = $fs;
        $value = $row ? ($row[$field] ?? '') : '';
        if ($type === 'textarea') {
            Form::textarea(['name' => $field, 'label' => $label, 'value' => $value]);
        } elseif ($type === 'enum') {
            if ($field === 'status' && is_numeric($value) && $value !== '') {
                $value = (int)$value === 1 ? 'active' : 'inactive';
            }
            echo "<div class='form-group row'>";
            echo "<label class='col-sm-2 col-form-label'>$label</label>";
            echo "<div class='col-sm-10'>";
            echo "<select name='$field' id='$field' class='form-select' style='width:100%'>";
            foreach ($opt as $k => $v) {
                $sel = ($value == $k) ? ' selected' : '';
                echo "<option value='$k'$sel>$v</option>";
            }
            echo "</select></div></div>";
        } elseif ($type === 'fk') {
            Form::select(['name' => $field, 'label' => $label, 'table' => $opt, 'value' => $value]);
        } elseif ($type === 'date') {
            Form::date(['name' => $field, 'label' => $label, 'value' => $value]);
        } elseif ($type === 'datetime') {
            Form::datetime(['name' => $field, 'label' => $label, 'value' => $value]);
        } elseif ($type === 'number') {
            Form::number(['name' => $field, 'label' => $label, 'value' => $value]);
        } elseif ($type === 'email') {
            Form::email(['name' => $field, 'label' => $label, 'value' => $value]);
        } else {
            Form::text(['name' => $field, 'label' => $label, 'value' => $value]);
        }
    }
    Form::submit(['name' => 'submit', 'value' => $row ? 'Update' : 'Save', 'class' => 'btn btn-primary']);
}

/**
 * Convert a numeric amount into English words for receipts / invoices.
 * Example: 25000.00 -> "Taka Twenty Five Thousand Only"
 * Example: 333.50 -> "Taka Three Hundred Thirty Three And Paisa Fifty Only"
 */
function grocery_amount_in_words($amount)
{
    $amount = (float) $amount;
    if ($amount < 0) {
        $words = 'Taka ' . self_digits_words(-(int)floor(abs($amount))) . ' Only';
        return $words;
    }

    $whole = (int) floor($amount);
    $paisa = (int) round(($amount - $whole) * 100);
    if ($paisa === 100) {
        $whole += 1;
        $paisa = 0;
    }

    $parts = [];
    if ($whole > 0) {
        $parts[] = 'Taka ' . self_digits_words($whole);
    }
    if ($paisa > 0) {
        $parts[] = 'And Paisa ' . self_digits_words($paisa);
    }
    if (!$parts) {
        return 'Taka Zero Only';
    }
    return implode(' ', $parts) . ' Only';
}

/**
 * Convert a non-negative integer into English words.
 * Cancel-safe: "self_" prefix avoids any context collisions.
 */
function self_digits_words($num)
{
    $num = (int) $num;
    if ($num === 0) {
        return 'Zero';
    }
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $chunk = function ($n) use ($ones, $tens) {
        $txt = '';
        $h = (int) floor($n / 100);
        $r = $n % 100;
        if ($h > 0) {
            $txt .= $ones[$h] . ' Hundred';
            if ($r > 0) {
                $txt .= ' ';
            }
        }
        if ($r > 0) {
            if ($r < 20) {
                $txt .= $ones[$r];
            } else {
                $txt .= $tens[(int) floor($r / 10)];
                if ($r % 10 > 0) {
                    $txt .= ' ' . $ones[$r % 10];
                }
            }
        }
        return $txt;
    };

    $groups = [];
    $names = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
    $i = 0;
    while ($num > 0) {
        $part = $num % 1000;
        if ($part !== 0) {
            $groups[] = $chunk($part) . ($names[$i] !== '' ? ' ' . $names[$i] : '');
        }
        $num = (int) floor($num / 1000);
        $i++;
    }

    return implode(' ', array_reverse($groups));
}