<?php
// ============================================================
// URBANMART GROCERY - DOMAIN MODELS + BUSINESS SERVICE
// ============================================================
// Single autoloadable file (matches core.php / api.php glob
// "*.model.php"). All queries use prepared statements via the
// GroceryCrudTrait. Read methods return assoc arrays.
// ============================================================

trait GroceryCrudTrait
{
    protected static $lastError = '';

    protected static function dbTable(): string
    {
        global $tx;
        return $tx . static::$table;
    }

    protected static function selectList(): string
    {
        $cols = array_merge([static::$primaryKey], static::$columns);
        foreach ($cols as &$c) $c = '`' . $c . '`';
        return implode(',', $cols);
    }

    protected static function clean(array $data): array
    {
        $out = [];
        foreach (static::$columns as $c) {
            if (array_key_exists($c, $data)) $out[$c] = $data[$c];
        }
        return $out;
    }

    public static function exec(string $sql, array $params = [])
    {
        global $db;
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            static::$lastError = $db->error ?: 'Prepare failed';
            return null;
        }
        if ($params) {
            $types = '';
            foreach ($params as $p) {
                if ($p === null) $types .= 's';
                elseif (is_int($p)) $types .= 'i';
                elseif (is_float($p)) $types .= 'd';
                elseif (is_string($p) && preg_match('/^-?\d+(\.\d+)?$/', $p)) $types .= 'd';
                else $types .= 's';
            }
            $args = [$types];
            foreach ($params as $k => &$v) $args[] = &$params[$k];
            call_user_func_array([$stmt, 'bind_param'], $args);
        }
        if (!$stmt->execute()) {
            static::$lastError = $stmt->error ?: ($db->error ?: 'Execute failed');
            $stmt->close();
            return null;
        }
        return $stmt;
    }

    public static function lastError(): string
    {
        return static::$lastError;
    }

    public static function scalar(string $sql, array $params = [])
    {
        $stmt = static::exec($sql, $params);
        if (!$stmt) return null;
        $row = $stmt->get_result()->fetch_row();
        $stmt->close();
        return $row ? $row[0] : null;
    }

    public static function findRow($id)
    {
        $rows = static::rows('`' . static::$primaryKey . '` = ?', [$id], null, 1);
        return $rows ? $rows[0] : null;
    }

    public static function rows($where = '', array $params = [], $orderBy = null, $limit = 0)
    {
        $sql = 'SELECT ' . static::selectList() . ' FROM ' . static::dbTable();
        if ($where) $sql .= ' WHERE ' . $where;
        $sql .= ' ORDER BY ' . ($orderBy ?: static::$orderByDefault);
        if ($limit > 0) $sql .= ' LIMIT ' . (int)$limit;
        $stmt = static::exec($sql, $params);
        if (!$stmt) return [];
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) $out[] = $row;
        $stmt->close();
        return $out;
    }

    public static function pagedRows($page = 1, $perPage = 20, $where = '', array $params = [], $orderBy = null)
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $top = ($page - 1) * $perPage;
        $sql = 'SELECT ' . static::selectList() . ' FROM ' . static::dbTable();
        if ($where) $sql .= ' WHERE ' . $where;
        $sql .= ' ORDER BY ' . ($orderBy ?: static::$orderByDefault) . " LIMIT $top,$perPage";
        $stmt = static::exec($sql, $params);
        if (!$stmt) return [];
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) $out[] = $row;
        $stmt->close();
        return $out;
    }

    public static function countRows($where = '', array $params = [])
    {
        $sql = 'SELECT COUNT(*) AS total FROM ' . static::dbTable();
        if ($where) $sql .= ' WHERE ' . $where;
        $stmt = static::exec($sql, $params);
        if (!$stmt) return 0;
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$row['total'];
    }

    public static function searchRows($q = '', $where = '', array $params = [], $limit = 50)
    {
        $q = trim((string)$q);
        $conds = [];
        if ($q !== '') {
            foreach (preg_split('/\s+/', $q) as $kw) {
                $sub = [];
                foreach (static::$searchFields as $f) {
                    $sub[] = '`' . $f . '` LIKE ?';
                    $params[] = '%' . $kw . '%';
                }
                $conds[] = '(' . implode(' OR ', $sub) . ')';
            }
        }
        $sql = 'SELECT ' . static::selectList() . ' FROM ' . static::dbTable();
        $joins = '';
        if ($where) $sql .= ' WHERE ' . $where;
        if ($conds) $sql .= ($where ? ' AND ' : ' WHERE ') . implode(' AND ', $conds);
        if ($joins) $sql = str_replace(' FROM ' . static::dbTable(), ' FROM ' . static::dbTable() . ' ' . $joins, $sql);
        $sql .= ' ORDER BY ' . static::$orderByDefault . ' LIMIT ' . (int)$limit;
        $stmt = static::exec($sql, $params);
        if (!$stmt) return [];
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) $out[] = $row;
        $stmt->close();
        return $out;
    }

    public static function existsRow($field, $value, $excludeId = null)
    {
        $where = '`' . $field . '` = ?';
        $params = [$value];
        if ($excludeId !== null) {
            $where .= ' AND `' . static::$primaryKey . '` <> ?';
            $params[] = $excludeId;
        }
        return static::countRows($where, $params) > 0;
    }

    public static function grid($page = 1, $per = 20, $q = '', $where = '', array $params = [])
    {
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $q = trim((string)$q);
        $conds = [];
        if ($q !== '') {
            $sub = [];
            foreach (static::$searchFields as $f) {
                $sub[] = '`' . $f . '` LIKE ?';
                $params[] = '%' . $q . '%';
            }
            if ($sub) $conds[] = '(' . implode(' OR ', $sub) . ')';
        }
        $w = trim($where);
        if ($conds) {
            $w = ($w !== '' ? $w . ' AND ' : '') . implode(' AND ', $conds);
        }
        $total = static::countRows($w, $params);
        $rows = static::pagedRows($page, $per, $w, $params);
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public static function insertRow(array $data)
    {
        $data = static::clean($data);
        if (!$data) {
            static::$lastError = 'No valid data to insert';
            return null;
        }
        $fields = [];
        $params = [];
        foreach ($data as $c => $v) {
            $fields[] = '`' . $c . '`';
            $params[] = $v;
        }
        $sql = 'INSERT INTO ' . static::dbTable() . ' (' . implode(',', $fields) . ') VALUES ('
            . implode(',', array_fill(0, count($fields), '?')) . ')';
        $stmt = static::exec($sql, $params);
        if (!$stmt) return null;
        global $db;
        $id = $db->insert_id;
        $stmt->close();
        return $id ? (int)$id : 0;
    }

    public static function updateRow($id, array $data)
    {
        $data = static::clean($data);
        if (!$data) return false;
        $sets = [];
        $params = [];
        foreach ($data as $c => $v) {
            $sets[] = '`' . $c . '` = ?';
            $params[] = $v;
        }
        $params[] = $id;
        $sql = 'UPDATE ' . static::dbTable() . ' SET ' . implode(', ', $sets) . ' WHERE `'
            . static::$primaryKey . '` = ?';
        $stmt = static::exec($sql, $params);
        if (!$stmt) return false;
        $stmt->close();
        return true;
    }

    public static function deleteRow($id)
    {
        $sql = 'DELETE FROM ' . static::dbTable() . ' WHERE `' . static::$primaryKey . '` = ?';
        $stmt = static::exec($sql, [$id]);
        if (!$stmt) return false;
        $stmt->close();
        return true;
    }

    public static function nextSerial($prefix, $field)
    {
        $stmt = static::exec(
            'SELECT `' . $field . '` AS v FROM ' . static::dbTable() . ' WHERE `' . $field . '` LIKE ?
             ORDER BY `' . $field . '` DESC LIMIT 300',
            [$prefix . '-%']
        );
        $max = 0;
        if ($stmt) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                if (preg_match('/-(\d+)$/', (string)$row['v'], $m)) {
                    $n = (int)$m[1];
                    if ($n > $max) $max = $n;
                }
            }
            $stmt->close();
        }
        return $prefix . '-' . date('Y') . '-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}

// ---------- shared base (keeps entity classes tiny) ----------
abstract class GroceryBase extends Model
{
    use GroceryCrudTrait;

    protected static $table = '';
    protected static $primaryKey = 'id';
    protected static $columns = [];
    protected static $searchFields = ['name'];
    protected static $orderByDefault = 'id DESC';

    protected static function money($v)
    {
        return number_format((float)$v, 2);
    }
}

// ============================================================
// MASTER DATA
// ============================================================

class GroceryCategory extends GroceryBase
{
    protected static $table = 'grocery_categories';
    protected static $columns = ['name', 'slug', 'image', 'description', 'status', 'sort_order', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'slug', 'description'];
    protected static $orderByDefault = 'sort_order ASC, id ASC';
}

class GrocerySubcategory extends GroceryBase
{
    protected static $table = 'grocery_subcategories';
    protected static $columns = ['category_id', 'name', 'slug', 'image', 'description', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'slug'];
    protected static $orderByDefault = 'name ASC';

    public static function listByCategory($categoryId)
    {
        return static::rows('category_id = ? AND status = 1', [$categoryId], 'name ASC');
    }
}

class GroceryBrand extends GroceryBase
{
    protected static $table = 'grocery_brands';
    protected static $columns = ['name', 'slug', 'image', 'description', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'slug', 'description'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryUnit extends GroceryBase
{
    protected static $table = 'grocery_units';
    protected static $columns = ['name', 'short_name', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'short_name'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryProduct extends GroceryBase
{
    protected static $table = 'grocery_products';
    protected static $columns = ['name', 'slug', 'sku', 'barcode', 'category_id', 'subcategory_id', 'brand_id', 'unit_id',
        'product_type', 'purchase_price', 'selling_price', 'mrp_price', 'discount_type', 'discount_value', 'tax_rate',
        'stock_quantity', 'reorder_level', 'weight', 'short_description', 'full_description', 'image',
        'is_featured', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'sku', 'barcode'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '', $status = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $cond = [];
        $params = [];
        if ($q !== '') {
            $cond[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($status !== '' && $status !== 'all') {
            $cond[] = 'p.status = ?';
            $params[] = $status;
        }
        $where = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
        $join = "{$tx}grocery_products p
            LEFT JOIN {$tx}grocery_categories c ON c.id = p.category_id
            LEFT JOIN {$tx}grocery_subcategories s ON s.id = p.subcategory_id
            LEFT JOIN {$tx}grocery_brands b ON b.id = p.brand_id
            LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join{$where}", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'p.*, c.name AS category_name, s.name AS subcategory_name, b.name AS brand_name, u.name AS unit_name, u.short_name AS unit_short';
        $stmt = static::exec("SELECT $cols FROM $join{$where} ORDER BY p.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public static function posItems($q = '', $limit = 50)
    {
        global $tx;
        $params = [];
        $where = 'p.status = ?';
        $params[] = 'active';
        if ($q !== '') {
            $where .= ' AND (p.name LIKE ? OR p.barcode LIKE ? OR p.sku LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $sql = "SELECT p.id, p.name, p.sku, p.barcode, p.selling_price, p.mrp_price, p.purchase_price,
                p.tax_rate, p.image, p.unit_id, u.short_name AS unit_short,
                IFNULL(g.available_quantity, p.stock_quantity) AS available
                FROM {$tx}grocery_products p
                LEFT JOIN {$tx}grocery_stocks g ON g.product_id = p.id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE $where ORDER BY p.name ASC LIMIT " . (int)$limit;
        $stmt = static::exec($sql, $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }

    public static function findDetailed($id)
    {
        global $tx;
        $sql = "SELECT p.*, c.name AS category_name, s.name AS subcategory_name, b.name AS brand_name,
                u.name AS unit_name, u.short_name AS unit_short,
                IFNULL(g.available_quantity, p.stock_quantity) AS available
                FROM {$tx}grocery_products p
                LEFT JOIN {$tx}grocery_categories c ON c.id = p.category_id
                LEFT JOIN {$tx}grocery_subcategories s ON s.id = p.subcategory_id
                LEFT JOIN {$tx}grocery_brands b ON b.id = p.brand_id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                LEFT JOIN {$tx}grocery_stocks g ON g.product_id = p.id
                WHERE p.id = ?";
        $stmt = static::exec($sql, [$id]);
        if (!$stmt) return null;
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $imgs = GroceryProductImage::byProduct($id);
            $row['images'] = $imgs;
            if (!$row['image'] && $imgs) $row['image'] = $imgs[0]['image'];
        }
        return $row;
    }

    public static function lowStock($limit = 8)
    {
        global $tx;
        $sql = "SELECT p.id, p.name, p.sku, p.stock_quantity, p.reorder_level, p.image, u.short_name AS unit_short
                FROM {$tx}grocery_products p
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE p.status = 'active' AND p.stock_quantity <= p.reorder_level
                ORDER BY (p.stock_quantity - p.reorder_level) ASC LIMIT " . (int)$limit;
        $stmt = static::exec($sql);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }
}

class GroceryProductImage extends GroceryBase
{
    protected static $table = 'grocery_product_images';
    protected static $columns = ['product_id', 'image', 'is_primary', 'created_at'];
    protected static $searchFields = ['image'];
    protected static $orderByDefault = 'id ASC';

    public static function byProduct($productId)
    {
        return static::rows('product_id = ?', [$productId], 'is_primary DESC, id ASC');
    }
}

class GrocerySupplier extends GroceryBase
{
    protected static $table = 'grocery_suppliers';
    protected static $columns = ['name', 'company', 'phone', 'email', 'address', 'city', 'opening_balance', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'company', 'phone', 'email', 'city'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryCustomer extends GroceryBase
{
    protected static $table = 'grocery_customers';
    protected static $columns = ['name', 'phone', 'email', 'address', 'city', 'district', 'opening_balance', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'phone', 'email', 'city', 'address', 'district'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryCustomerAddress extends GroceryBase
{
    protected static $table = 'grocery_customer_addresses';
    protected static $columns = ['customer_id', 'address_type', 'address', 'city', 'district', 'phone', 'is_default', 'created_at'];
    protected static $searchFields = ['address', 'city'];
    protected static $orderByDefault = 'is_default DESC, id ASC';

    public static function of($customerId)
    {
        return static::rows('customer_id = ?', [$customerId]);
    }
}

// ============================================================
// INVENTORY
// ============================================================

class GroceryStock extends GroceryBase
{
    protected static $table = 'grocery_stocks';
    protected static $primaryKey = 'product_id';
    protected static $columns = ['product_id', 'quantity', 'available_quantity', 'total_purchased', 'total_sold', 'last_movement_at', 'created_at', 'updated_at'];
    protected static $searchFields = ['product_id'];
    protected static $orderByDefault = 'product_id ASC';

    public static function byProduct($productId)
    {
        return static::findRow($productId);
    }

    public static function reportRows($page = 1, $per = 20, $q = '', $lowOnly = false)
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $cond = [];
        $params = [];
        if ($q !== '') {
            $cond[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($lowOnly) $cond[] = 'g.available_quantity <= p.reorder_level';
        $where = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
        $join = "{$tx}grocery_stocks g
            LEFT JOIN {$tx}grocery_products p ON p.id = g.product_id
            LEFT JOIN {$tx}grocery_categories c ON c.id = p.category_id
            LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join{$where}", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'g.*, p.name, p.sku, p.reorder_level, c.name AS category_name, u.short_name AS unit_short,
            ROUND(p.purchase_price * g.quantity, 2) AS stock_value';
        $stmt = static::exec("SELECT $cols FROM $join{$where} ORDER BY p.name ASC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

class GroceryStockMovement extends GroceryBase
{
    protected static $table = 'grocery_stock_movements';
    protected static $columns = ['product_id', 'movement_type', 'quantity', 'balance_after', 'reference_type', 'reference_id', 'remark', 'created_by', 'created_at'];
    protected static $searchFields = ['remark'];
    protected static $orderByDefault = 'id DESC';

    public static function history($productId)
    {
        return static::rows('product_id = ?', [$productId], 'id DESC');
    }

    public static function manager($page = 1, $per = 20, $q = '', $type = '', $productId = 0)
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $cond = [];
        $params = [];
        if ($q !== '') {
            $cond[] = '(p.name LIKE ? OR p.sku LIKE ? OR m.remark LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($type !== '' && $type !== 'all' && $type !== []) {
            $types = is_array($type) ? array_values(array_filter($type)) : [$type];
            if (count($types) === 1) {
                $cond[] = 'm.movement_type = ?';
                $params[] = $types[0];
            } elseif (count($types) > 1) {
                $place = rtrim(str_repeat('?,', count($types)), ',');
                $cond[] = "m.movement_type IN ($place)";
                foreach ($types as $t) $params[] = $t;
            }
        }
        if ($productId > 0) {
            $cond[] = 'm.product_id = ?';
            $params[] = $productId;
        }
        $where = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
        $join = "{$tx}grocery_stock_movements m
            LEFT JOIN {$tx}grocery_products p ON p.id = m.product_id
            LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join{$where}", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'm.*, p.name, p.sku, p.image, u.short_name AS unit_short';
        $stmt = static::exec("SELECT $cols FROM $join{$where} ORDER BY m.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

class GroceryStockAdjustment extends GroceryBase
{
    protected static $table = 'grocery_stock_adjustments';
    protected static $columns = ['adjustment_no', 'adjustment_type', 'reason', 'remark', 'created_by', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['adjustment_no', 'reason'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $type = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($type !== '' && $type !== 'all') {
            $where = ' WHERE adjustment_type = ?';
            $params[] = $type;
        }
        $table = static::dbTable();
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $table$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $stmt = static::exec("SELECT * FROM $table$where ORDER BY id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

class GroceryStockAdjustmentDetail extends GroceryBase
{
    protected static $table = 'grocery_stock_adjustment_details';
    protected static $columns = ['adjustment_id', 'product_id', 'previous_quantity', 'adjusted_quantity', 'new_quantity', 'remark'];
    protected static $searchFields = ['remark'];
    protected static $orderByDefault = 'id ASC';

    public static function of($adjustmentId)
    {
        global $tx;
        $sql = "SELECT d.*, p.name, p.sku, u.short_name AS unit_short
                FROM {$tx}grocery_stock_adjustment_details d
                LEFT JOIN {$tx}grocery_products p ON p.id = d.product_id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE d.adjustment_id = ? ORDER BY d.id ASC";
        $stmt = static::exec($sql, [$adjustmentId]);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }
}

// ============================================================
// PURCHASE
// ============================================================

class GroceryPaymentMethod extends GroceryBase
{
    protected static $table = 'grocery_payment_methods';
    protected static $columns = ['name', 'slug', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['name', 'slug'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryPurchase extends GroceryBase
{
    protected static $table = 'grocery_purchases';
    protected static $columns = ['purchase_no', 'supplier_id', 'purchase_date', 'discount', 'tax', 'subtotal', 'grand_total',
        'paid_amount', 'due_amount', 'payment_method_id', 'status', 'remark', 'created_by', 'created_at', 'updated_at'];
    protected static $searchFields = ['purchase_no'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE (pu.purchase_no LIKE ? OR s.name LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $join = "{$tx}grocery_purchases pu
            LEFT JOIN {$tx}grocery_suppliers s ON s.id = pu.supplier_id
            LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = pu.payment_method_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'pu.*, s.name AS supplier_name, s.company, s.phone, pm.name AS payment_method_name';
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY pu.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public static function withDetails($id)
    {
        global $tx;
        $sql = "SELECT pu.*, s.name AS supplier_name, s.company, s.phone, s.address AS supplier_address,
                pm.name AS payment_method_name
                FROM {$tx}grocery_purchases pu
                LEFT JOIN {$tx}grocery_suppliers s ON s.id = pu.supplier_id
                LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = pu.payment_method_id
                WHERE pu.id = ?";
        $stmt = static::exec($sql, [$id]);
        if (!$stmt) return null;
        $head = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$head) return null;
        $head['lines'] = GroceryPurchaseDetail::of($id);
        return $head;
    }
}

class GroceryPurchaseDetail extends GroceryBase
{
    protected static $table = 'grocery_purchase_details';
    protected static $columns = ['purchase_id', 'product_id', 'quantity', 'purchase_price', 'discount', 'vat', 'subtotal'];
    protected static $searchFields = [];
    protected static $orderByDefault = 'id ASC';

    public static function of($purchaseId)
    {
        global $tx;
        $sql = "SELECT d.*, p.name, p.sku, u.short_name AS unit_short
                FROM {$tx}grocery_purchase_details d
                LEFT JOIN {$tx}grocery_products p ON p.id = d.product_id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE d.purchase_id = ? ORDER BY d.id ASC";
        $stmt = static::exec($sql, [$purchaseId]);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }
}

// ============================================================
// SALES / POS
// ============================================================

class GrocerySale extends GroceryBase
{
    protected static $table = 'grocery_sales';
    protected static $columns = ['invoice_no', 'customer_id', 'sale_date', 'discount', 'tax', 'subtotal', 'grand_total',
        'paid_amount', 'due_amount', 'payment_method_id', 'payment_status', 'status', 'remark', 'created_by', 'created_at', 'updated_at'];
    protected static $searchFields = ['invoice_no'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE (sa.invoice_no LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $join = "{$tx}grocery_sales sa
            LEFT JOIN {$tx}grocery_customers c ON c.id = sa.customer_id
            LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = sa.payment_method_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'sa.*, c.name AS customer_name, c.phone AS customer_phone, pm.name AS payment_method_name';
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY sa.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public static function withDetails($id)
    {
        global $tx;
        $sql = "SELECT sa.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
                pm.name AS payment_method_name
                FROM {$tx}grocery_sales sa
                LEFT JOIN {$tx}grocery_customers c ON c.id = sa.customer_id
                LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = sa.payment_method_id
                WHERE sa.id = ?";
        $stmt = static::exec($sql, [$id]);
        if (!$stmt) return null;
        $head = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$head) return null;
        $head['lines'] = GrocerySaleDetail::of($id);
        return $head;
    }
}

class GrocerySaleDetail extends GroceryBase
{
    protected static $table = 'grocery_sale_details';
    protected static $columns = ['sale_id', 'product_id', 'quantity', 'selling_price', 'discount', 'vat', 'cost_price', 'subtotal'];
    protected static $searchFields = [];
    protected static $orderByDefault = 'id ASC';

    public static function of($saleId)
    {
        global $tx;
        $sql = "SELECT d.*, p.name, p.sku, p.image, u.short_name AS unit_short
                FROM {$tx}grocery_sale_details d
                LEFT JOIN {$tx}grocery_products p ON p.id = d.product_id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE d.sale_id = ? ORDER BY d.id ASC";
        $stmt = static::exec($sql, [$saleId]);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }
}

// ============================================================
// ORDERS
// ============================================================

class GroceryOrder extends GroceryBase
{
    protected static $table = 'grocery_orders';
    protected static $columns = ['order_no', 'customer_id', 'customer_address_id', 'order_date', 'delivery_charge', 'discount',
        'tax', 'subtotal', 'grand_total', 'paid_amount', 'due_amount', 'payment_status', 'payment_method_id', 'order_status',
        'delivery_address', 'delivery_date', 'remark', 'created_by', 'created_at', 'updated_at'];
    protected static $searchFields = ['order_no'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '', $status = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $cond = [];
        $params = [];
        if ($q !== '') {
            $cond[] = '(o.order_no LIKE ? OR c.name LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($status !== '' && $status !== 'all') {
            $cond[] = 'o.order_status = ?';
            $params[] = $status;
        }
        $where = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
        $join = "{$tx}grocery_orders o
            LEFT JOIN {$tx}grocery_customers c ON c.id = o.customer_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'o.*, c.name AS customer_name, c.phone AS customer_phone';
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY o.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }

    public static function withDetails($id)
    {
        global $tx;
        $sql = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                pm.name AS payment_method_name
                FROM {$tx}grocery_orders o
                LEFT JOIN {$tx}grocery_customers c ON c.id = o.customer_id
                LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = o.payment_method_id
                WHERE o.id = ?";
        $stmt = static::exec($sql, [$id]);
        if (!$stmt) return null;
        $head = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$head) return null;
        $head['lines'] = GroceryOrderDetail::of($id);
        return $head;
    }
}

class GroceryOrderDetail extends GroceryBase
{
    protected static $table = 'grocery_order_details';
    protected static $columns = ['order_id', 'product_id', 'quantity', 'price', 'discount', 'vat', 'subtotal'];
    protected static $searchFields = [];
    protected static $orderByDefault = 'id ASC';

    public static function of($orderId)
    {
        global $tx;
        $sql = "SELECT d.*, p.name, p.sku, u.short_name AS unit_short
                FROM {$tx}grocery_order_details d
                LEFT JOIN {$tx}grocery_products p ON p.id = d.product_id
                LEFT JOIN {$tx}grocery_units u ON u.id = p.unit_id
                WHERE d.order_id = ? ORDER BY d.id ASC";
        $stmt = static::exec($sql, [$orderId]);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return $rows;
    }
}

// ============================================================
// DELIVERY
// ============================================================

class GroceryDeliveryStatus extends GroceryBase
{
    protected static $table = 'grocery_delivery_statuses';
    protected static $columns = ['name', 'slug', 'sort_order', 'created_at'];
    protected static $searchFields = ['name'];
    protected static $orderByDefault = 'sort_order ASC, id ASC';
}

class GroceryDelivery extends GroceryBase
{
    protected static $table = 'grocery_deliveries';
    protected static $columns = ['delivery_no', 'order_id', 'delivery_date', 'delivery_status_id', 'rider_name', 'rider_phone',
        'address', 'remark', 'created_by', 'created_at', 'updated_at'];
    protected static $searchFields = ['delivery_no', 'rider_name'];
    protected static $orderByDefault = 'id DESC';

    public static function byOrder($orderId)
    {
        return static::rows('order_id = ?', [$orderId], 'id ASC');
    }

    public static function manager($page = 1, $per = 20, $q = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE (d.delivery_no LIKE ? OR o.order_no LIKE ? OR d.rider_name LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $join = "{$tx}grocery_deliveries d
            LEFT JOIN {$tx}grocery_orders o ON o.id = d.order_id
            LEFT JOIN {$tx}grocery_customers c ON c.id = o.customer_id
            LEFT JOIN {$tx}grocery_delivery_statuses s ON s.id = d.delivery_status_id";
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $cols = 'd.*, o.order_no, o.order_status, c.name AS customer_name, c.phone AS customer_phone, s.name AS status_name';
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY d.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

// ============================================================
// PAYMENTS
// ============================================================

class GroceryPayment extends GroceryBase
{
    protected static $table = 'grocery_payments';
    protected static $columns = ['payment_no', 'payable_type', 'payable_id', 'customer_id', 'supplier_id', 'amount',
        'payment_method_id', 'transaction_id', 'payment_date', 'remark', 'created_by', 'created_at'];
    protected static $searchFields = ['payment_no'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE (pa.payment_no LIKE ? OR c.name LIKE ? OR s.name LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $join = "{$tx}grocery_payments pa
            LEFT JOIN {$tx}grocery_customers c ON c.id = pa.customer_id
            LEFT JOIN {$tx}grocery_suppliers s ON s.id = pa.supplier_id
            LEFT JOIN {$tx}grocery_payment_methods pm ON pm.id = pa.payment_method_id";
        $cols = 'pa.*, c.name AS customer_name, s.name AS supplier_name, pm.name AS payment_method_name';
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY pa.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

// ============================================================
// COUPONS / EXPENSE / SETTINGS
// ============================================================

class GroceryCoupon extends GroceryBase
{
    protected static $table = 'grocery_coupons';
    protected static $columns = ['code', 'type', 'value', 'min_order_amount', 'max_discount', 'starts_at', 'ends_at',
        'usage_limit', 'used_count', 'status', 'created_at', 'updated_at'];
    protected static $searchFields = ['code'];
    protected static $orderByDefault = 'id DESC';

    public static function findCode($code)
    {
        $rows = static::rows('code = ?', [$code], null, 1);
        return $rows ? $rows[0] : null;
    }
}

class GroceryExpenseCategory extends GroceryBase
{
    protected static $table = 'grocery_expense_categories';
    protected static $columns = ['name', 'status', 'created_at'];
    protected static $searchFields = ['name'];
    protected static $orderByDefault = 'name ASC';
}

class GroceryExpense extends GroceryBase
{
    protected static $table = 'grocery_expenses';
    protected static $columns = ['expense_no', 'expense_category_id', 'amount', 'expense_date', 'remark', 'created_by', 'created_at', 'updated_at'];
    protected static $searchFields = ['expense_no'];
    protected static $orderByDefault = 'id DESC';

    public static function manager($page = 1, $per = 20, $q = '')
    {
        global $tx;
        $page = max(1, (int)$page);
        $per = max(1, (int)$per);
        $top = ($page - 1) * $per;
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE (e.expense_no LIKE ? OR ec.name LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $join = "{$tx}grocery_expenses e
            LEFT JOIN {$tx}grocery_expense_categories ec ON ec.id = e.expense_category_id";
        $cols = 'e.*, ec.name AS category_name';
        $total = 0;
        $tStmt = static::exec("SELECT COUNT(*) AS total FROM $join$where", $params);
        if ($tStmt) {
            $total = (int)$tStmt->get_result()->fetch_assoc()['total'];
            $tStmt->close();
        }
        $stmt = static::exec("SELECT $cols FROM $join$where ORDER BY e.id DESC LIMIT $top,$per", $params);
        $rows = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            $stmt->close();
        }
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
    }
}

class GrocerySetting extends GroceryBase
{
    protected static $table = 'grocery_settings';
    protected static $columns = ['setting_key', 'setting_value', 'description', 'created_at', 'updated_at'];
    protected static $searchFields = ['setting_key', 'description'];
    protected static $orderByDefault = 'id ASC';

    public static function get($key, $default = '')
    {
        if (static::existsRow('setting_key', $key)) {
            $rows = static::rows('setting_key = ?', [$key], null, 1);
            return $rows ? $rows[0]['setting_value'] : $default;
        }
        return $default;
    }

    public static function set($key, $value)
    {
        global $now;
        if (static::countRows('setting_key = ?', [$key]) > 0) {
            $stmt = static::exec('UPDATE ' . static::dbTable() . ' SET `setting_value` = ?, `updated_at` = ? WHERE `setting_key` = ?', [$value, $now, $key]);
            $ok = (bool)$stmt;
            if ($stmt) $stmt->close();
            return $ok;
        }
        $id = static::insertRow(['setting_key' => $key, 'setting_value' => $value, 'created_at' => $now, 'updated_at' => $now]);
        return $id !== null;
    }
}

// ============================================================
// BUSINESS SERVICE (transactions, stock ledger, dashboard)
// ============================================================

class GroceryService
{
    public static function uid()
    {
        return isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
    }

    public static function setting($key, $default = '')
    {
        return GrocerySetting::get($key, $default);
    }

    public static function syncProductStock($productId, $qty = null)
    {
        global $now;
        $s = static::stockRow($productId);
        if ($qty !== null) {
            $qty = round((float)$qty, 3);
            GroceryStock::updateRow($productId, ['quantity' => $qty, 'available_quantity' => $qty, 'updated_at' => $now]);
            GroceryProduct::updateRow($productId, ['stock_quantity' => $qty, 'updated_at' => $now]);
        }
        return $s;
    }

    private static function stockRow($productId)
    {
        global $now;
        $r = GroceryStock::byProduct($productId);
        if (!$r) {
            $p = GroceryProduct::findRow($productId);
            $qty = $p ? (float)$p['stock_quantity'] : 0;
            GroceryStock::insertRow([
                'product_id' => $productId,
                'quantity' => $qty,
                'available_quantity' => $qty,
                'total_purchased' => 0,
                'total_sold' => 0,
                'last_movement_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $r = ['quantity' => $qty, 'available_quantity' => $qty, 'total_purchased' => 0, 'total_sold' => 0];
        }
        return $r;
    }

    private static function applyMovement($productId, $type, $delta, $refType, $refId, $remark)
    {
        global $now;
        $stock = static::stockRow($productId);
        $newQty = round((float)$stock['quantity'] + (float)$delta, 3);
        $newAvail = round((float)$stock['available_quantity'] + (float)$delta, 3);
        $tPurch = (float)$stock['total_purchased'];
        $tSold = (float)$stock['total_sold'];
        if ($type === 'purchase_in' || $type === 'return_in' || $type === 'adjustment_in') $tPurch += abs($delta);
        if ($type === 'sale_out') $tSold += abs($delta);
        GroceryStock::updateRow($productId, [
            'quantity' => $newQty,
            'available_quantity' => $newAvail,
            'total_purchased' => $tPurch,
            'total_sold' => $tSold,
            'last_movement_at' => $now,
            'updated_at' => $now,
        ]);
        GroceryProduct::updateRow($productId, ['stock_quantity' => $newQty, 'updated_at' => $now]);
        GroceryStockMovement::insertRow([
            'product_id' => $productId,
            'movement_type' => $type,
            'quantity' => round((float)$delta, 3),
            'balance_after' => $newQty,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'remark' => $remark,
            'created_by' => static::uid(),
            'created_at' => $now,
        ]);
    }

    private static function checkStock($productId, $needed)
    {
        if (static::setting('allow_negative_stock', '0') === '1') return true;
        $stock = static::stockRow($productId);
        if ((float)$stock['available_quantity'] < (float)$needed) {
            throw new Exception('Insufficient stock for product id ' . $productId);
        }
        return true;
    }

    public static function recordPayment($type, $payableId, $amount, $methodId, $customerId, $supplierId, $txn, $remark)
    {
        if ((float)$amount <= 0) return null;
        global $now;
        $no = GroceryPayment::nextSerial('PMT', 'payment_no');
        return GroceryPayment::insertRow([
            'payment_no' => $no,
            'payable_type' => $type,
            'payable_id' => $payableId,
            'customer_id' => $customerId ?: null,
            'supplier_id' => $supplierId ?: null,
            'amount' => round((float)$amount, 2),
            'payment_method_id' => $methodId ?: 1,
            'transaction_id' => $txn ?: null,
            'payment_date' => $now,
            'remark' => $remark,
            'created_by' => static::uid(),
            'created_at' => $now,
        ]);
    }

    // ---------- PURCHASE ----------
    public static function createPurchase(array $d)
    {
        global $db, $now;
        $items = $d['items'] ?? [];
        $items = array_values(array_filter($items, function ($i) {
            return isset($i['product_id']) && (float)($i['quantity'] ?? 0) > 0;
        }));
        if (!$items) return ['success' => false, 'message' => 'No purchase items provided'];
        $db->begin_transaction();
        try {
            $subtotal = 0;
            foreach ($items as &$i) {
                $qty = (float)$i['quantity'];
                $price = (float)$i['purchase_price'];
                $discount = (float)($i['discount'] ?? 0);
                $vat = (float)($i['vat'] ?? 0);
                $i['_subtotal'] = round($qty * $price - $discount + $vat, 2);
                $subtotal += $i['_subtotal'];
            }
            unset($i);
            $subtotal = round($subtotal, 2);
            $discount = (float)($d['discount'] ?? 0);
            $tax = (float)($d['tax'] ?? 0);
            $grand = round($subtotal - $discount + $tax, 2);
            $paid = min($grand, (float)($d['paid_amount'] ?? $grand));
            $due = round($grand - $paid, 2);
            $no = GroceryPurchase::nextSerial('PR', 'purchase_no');
            $pid = GroceryPurchase::insertRow([
                'purchase_no' => $no,
                'supplier_id' => $d['supplier_id'] ?? null,
                'purchase_date' => $d['purchase_date'] ?? date('Y-m-d'),
                'discount' => $discount,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_method_id' => $d['payment_method_id'] ?? 1,
                'status' => $d['status'] ?? 'received',
                'remark' => $d['remark'] ?? null,
                'created_by' => static::uid(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$pid) throw new Exception('Failed to save purchase: ' . GroceryPurchase::lastError());
            foreach ($items as $i) {
                GroceryPurchaseDetail::insertRow([
                    'purchase_id' => $pid,
                    'product_id' => $i['product_id'],
                    'quantity' => $i['quantity'],
                    'purchase_price' => $i['purchase_price'],
                    'discount' => $i['discount'] ?? 0,
                    'vat' => $i['vat'] ?? 0,
                    'subtotal' => $i['_subtotal'],
                ]);
                static::applyMovement((int)$i['product_id'], 'purchase_in', (float)$i['quantity'], 'purchase', $pid, 'Purchase ' . $no);
            }
            static::recordPayment('purchase', $pid, $paid, $d['payment_method_id'] ?? 1, null, (int)($d['supplier_id'] ?? 0), $d['transaction_id'] ?? '', 'Purchase ' . $no);
            $db->commit();
            return ['success' => true, 'id' => $pid, 'purchase_no' => $no];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ---------- SALE / POS ----------
    public static function createSale(array $d)
    {
        global $db, $now;
        $items = $d['items'] ?? [];
        $items = array_values(array_filter($items, function ($i) {
            return isset($i['product_id']) && (float)($i['quantity'] ?? 0) > 0;
        }));
        if (!$items) return ['success' => false, 'message' => 'No sale items provided'];
        $db->begin_transaction();
        try {
            $subtotal = 0;
            $totalTax = 0;
            foreach ($items as &$i) {
                $p = GroceryProduct::findRow((int)$i['product_id']);
                if (!$p) throw new Exception('Product not found (id ' . $i['product_id'] . ')');
                $qty = (float)$i['quantity'];
                static::checkStock((int)$p['id'], $qty);
                $price = (float)($i['selling_price'] ?? $p['selling_price']);
                $discount = (float)($i['discount'] ?? 0);
                $taxRate = (float)($p['tax_rate'] ?? 0);
                $vat = round(($qty * $price - $discount) * $taxRate / 100, 2);
                $lineSub = round($qty * $price - $discount + $vat, 2);
                $i['_price'] = $price;
                $i['_discount'] = $discount;
                $i['_vat'] = $vat;
                $i['_subtotal'] = $lineSub;
                $i['_cost'] = (float)$p['purchase_price'];
                $subtotal += $lineSub;
                $totalTax += $vat;
            }
            unset($i);
            $subtotal = round($subtotal, 2);
            $discount = (float)($d['discount'] ?? 0);
            $tax = (float)($d['tax'] ?? 0);
            if ((float)$tax == 0) $tax = $totalTax;
            $grand = round($subtotal - $discount + $tax, 2);
            $paid = min($grand, (float)($d['paid_amount'] ?? $grand));
            $due = round($grand - $paid, 2);
            $payStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            $no = GrocerySale::nextSerial('INV', 'invoice_no');
            $sid = GrocerySale::insertRow([
                'invoice_no' => $no,
                'customer_id' => $d['customer_id'] ?? null,
                'sale_date' => $d['sale_date'] ?? date('Y-m-d'),
                'discount' => $discount,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_method_id' => $d['payment_method_id'] ?? GrocerySetting::get('default_payment_method', '1'),
                'payment_status' => $payStatus,
                'status' => 'completed',
                'remark' => $d['remark'] ?? null,
                'created_by' => static::uid(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$sid) throw new Exception('Failed to save sale: ' . GrocerySale::lastError());
            foreach ($items as $i) {
                GrocerySaleDetail::insertRow([
                    'sale_id' => $sid,
                    'product_id' => $i['product_id'],
                    'quantity' => $i['quantity'],
                    'selling_price' => $i['_price'],
                    'discount' => $i['_discount'],
                    'vat' => $i['_vat'],
                    'cost_price' => $i['_cost'],
                    'subtotal' => $i['_subtotal'],
                ]);
                static::applyMovement((int)$i['product_id'], 'sale_out', -1 * (float)$i['quantity'], 'sale', $sid, 'Sale ' . $no);
            }
            static::recordPayment('sale', $sid, $paid, $d['payment_method_id'] ?? 1, (int)($d['customer_id'] ?? 0), null, $d['transaction_id'] ?? '', 'Sale ' . $no);
            $db->commit();
            return ['success' => true, 'id' => $sid, 'invoice_no' => $no];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reverse the stock impact of a set of sale lines (add quantity back and
     * decrement total_sold) without recording a purchase/return movement.
     */
    private static function reverseSaleStock(array $lines)
    {
        global $now;
        foreach ($lines as $l) {
            $pid = (int)($l['product_id'] ?? 0);
            $qty = (float)($l['quantity'] ?? 0);
            if ($pid <= 0 || $qty == 0.0) continue;
            $stock = static::stockRow($pid);
            $newQty = round((float)$stock['quantity'] + $qty, 3);
            $newSold = max(0, round((float)$stock['total_sold'] - $qty, 3));
            GroceryStock::updateRow($pid, [
                'quantity' => $newQty,
                'available_quantity' => $newQty,
                'total_sold' => $newSold,
                'last_movement_at' => $now,
                'updated_at' => $now,
            ]);
            GroceryProduct::updateRow($pid, ['stock_quantity' => $newQty, 'updated_at' => $now]);
        }
    }

    /**
     * Delete a sale, restoring stock and removing its payment + stock movement
     * rows inside a single transaction so referential integrity is preserved.
     */
    public static function deleteSale($id)
    {
        global $db, $tx;
        $id = (int)$id;
        $sale = GrocerySale::findRow($id);
        if (!$sale) return ['success' => false, 'message' => 'Sale not found'];

        $lines = GrocerySaleDetail::rows('sale_id = ?', [$id], 'id ASC');
        $db->begin_transaction();
        try {
            static::reverseSaleStock($lines);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_stock_movements WHERE reference_type = 'sale' AND reference_id = ?", [$id]);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_payments WHERE payable_type = 'sale' AND payable_id = ?", [$id]);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_sale_details WHERE sale_id = ?", [$id]);
            GrocerySale::deleteRow($id);
            $db->commit();
            return ['success' => true, 'invoice_no' => $sale['invoice_no']];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update an existing sale: reverse the old stock/payment impact and
     * re-apply the new lines. Fully paid (posted) sales are not editable.
     */
    public static function updateSale($id, array $d)
    {
        global $db, $tx, $now;
        $id = (int)$id;
        $sale = GrocerySale::findRow($id);
        if (!$sale) return ['success' => false, 'message' => 'Sale not found'];
        if (($sale['payment_status'] ?? '') === 'paid') {
            return ['success' => false, 'message' => 'Fully paid invoices cannot be edited'];
        }

        $items = $d['items'] ?? [];
        $items = array_values(array_filter($items, function ($i) {
            return isset($i['product_id']) && (int)$i['product_id'] > 0 && (float)($i['quantity'] ?? 0) > 0;
        }));
        if (!$items) return ['success' => false, 'message' => 'At least one line item is required'];

        $oldLines = GrocerySaleDetail::rows('sale_id = ?', [$id], 'id ASC');

        $db->begin_transaction();
        try {
            $subtotal = 0;
            $totalTax = 0;
            foreach ($items as &$i) {
                $p = GroceryProduct::findRow((int)$i['product_id']);
                if (!$p) throw new Exception('Product not found (id ' . $i['product_id'] . ')');
                $qty = (float)$i['quantity'];
                $price = (float)($i['selling_price'] ?? 0);
                if ($price <= 0) $price = (float)$p['selling_price'];
                $discount = (float)($i['discount'] ?? 0);
                $taxRate = (float)($p['tax_rate'] ?? 0);
                $vat = round(($qty * $price - $discount) * $taxRate / 100, 2);
                $lineSub = round($qty * $price - $discount + $vat, 2);
                $i['_price'] = $price;
                $i['_discount'] = $discount;
                $i['_vat'] = $vat;
                $i['_subtotal'] = $lineSub;
                $i['_cost'] = (float)$p['purchase_price'];
                $subtotal += $lineSub;
                $totalTax += $vat;
            }
            unset($i);
            $subtotal = round($subtotal, 2);
            $discount = (float)($d['discount'] ?? 0);
            $tax = (float)($d['tax'] ?? 0);
            if ((float)$tax == 0) $tax = $totalTax;
            $grand = round($subtotal - $discount + $tax, 2);
            $paid = min($grand, (float)($d['paid_amount'] ?? $grand));
            $due = round($grand - $paid, 2);
            $payStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

            static::reverseSaleStock($oldLines);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_stock_movements WHERE reference_type = 'sale' AND reference_id = ?", [$id]);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_payments WHERE payable_type = 'sale' AND payable_id = ?", [$id]);
            GrocerySaleDetail::exec("DELETE FROM {$tx}grocery_sale_details WHERE sale_id = ?", [$id]);

            foreach ($items as $i) {
                static::checkStock((int)$i['product_id'], (float)$i['quantity']);
            }

            GrocerySale::updateRow($id, [
                'customer_id' => $d['customer_id'] ?? null,
                'sale_date' => $d['sale_date'] ?? date('Y-m-d'),
                'discount' => $discount,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_method_id' => $d['payment_method_id'] ?? $sale['payment_method_id'],
                'payment_status' => $payStatus,
                'remark' => $d['remark'] ?? null,
                'updated_at' => $now,
            ]);

            foreach ($items as $i) {
                GrocerySaleDetail::insertRow([
                    'sale_id' => $id,
                    'product_id' => $i['product_id'],
                    'quantity' => $i['quantity'],
                    'selling_price' => $i['_price'],
                    'discount' => $i['_discount'],
                    'vat' => $i['_vat'],
                    'cost_price' => $i['_cost'],
                    'subtotal' => $i['_subtotal'],
                ]);
                static::applyMovement((int)$i['product_id'], 'sale_out', -1 * (float)$i['quantity'], 'sale', $id, 'Sale ' . $sale['invoice_no'] . ' updated');
            }
            static::recordPayment('sale', $id, $paid, $d['payment_method_id'] ?? 1, (int)($d['customer_id'] ?? 0), null, '', 'Sale ' . $sale['invoice_no']);

            $db->commit();
            return ['success' => true, 'invoice_no' => $sale['invoice_no']];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ---------- ORDER ----------
    public static function createOrder(array $d)
    {
        global $db, $now;
        $items = $d['items'] ?? [];
        $items = array_values(array_filter($items, function ($i) {
            return isset($i['product_id']) && (float)($i['quantity'] ?? 0) > 0;
        }));
        if (!$items) return ['success' => false, 'message' => 'No order items provided'];
        $db->begin_transaction();
        try {
            $subtotal = 0;
            foreach ($items as &$i) {
                $p = GroceryProduct::findRow((int)$i['product_id']);
                $qty = max(0, (float)($i['quantity'] ?? 0));
                $price = max(0, (float)($i['price'] ?? ($p['selling_price'] ?? 0)));
                $discount = max(0, (float)($i['discount'] ?? 0));
                $vat = max(0, (float)($i['vat'] ?? 0));
                $i['price'] = $price;
                $i['quantity'] = $qty;
                $i['_subtotal'] = max(0, round($qty * $price - $discount + $vat, 2));
                $subtotal += $i['_subtotal'];
            }
            unset($i);
            $subtotal = round($subtotal, 2);
            $deliveryCharge = max(0, (float)($d['delivery_charge'] ?? 0));
            $discount = max(0, (float)($d['discount'] ?? 0));
            $tax = max(0, (float)($d['tax'] ?? 0));
            $grand = max(0, round($subtotal - $discount + $tax + $deliveryCharge, 2));
            $paid = 0;
            if (isset($d['paid_amount']) && $d['paid_amount'] !== '') $paid = (float)$d['paid_amount'];
            elseif (($d['payment_status'] ?? 'paid') === 'paid') $paid = $grand;
            $paid = max(0, min($grand, $paid));
            $due = round($grand - $paid, 2);
            $payStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            $no = GroceryOrder::nextSerial('ORD', 'order_no');
            $oid = GroceryOrder::insertRow([
                'order_no' => $no,
                'customer_id' => $d['customer_id'] ?? 0,
                'customer_address_id' => $d['customer_address_id'] ?? null,
                'order_date' => $d['order_date'] ?? $now,
                'delivery_charge' => $deliveryCharge,
                'discount' => $discount,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $payStatus,
                'payment_method_id' => $d['payment_method_id'] ?? null,
                'order_status' => $d['order_status'] ?? 'pending',
                'delivery_address' => $d['delivery_address'] ?? null,
                'delivery_date' => $d['delivery_date'] ?? null,
                'remark' => $d['remark'] ?? null,
                'created_by' => static::uid(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$oid) throw new Exception('Failed to save order: ' . GroceryOrder::lastError());
            foreach ($items as $i) {
                GroceryOrderDetail::insertRow([
                    'order_id' => $oid,
                    'product_id' => $i['product_id'],
                    'quantity' => $i['quantity'],
                    'price' => $i['price'] ?? 0,
                    'discount' => $i['discount'] ?? 0,
                    'vat' => $i['vat'] ?? 0,
                    'subtotal' => $i['_subtotal'],
                ]);
            }
            static::recordPayment('order', $oid, $paid, $d['payment_method_id'] ?? 1, (int)($d['customer_id'] ?? 0), null, $d['transaction_id'] ?? '', 'Order ' . $no);
            $db->commit();
            return ['success' => true, 'id' => $oid, 'order_no' => $no];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function updateOrderStatus($orderId, $status)
    {
        global $now;
        return GroceryOrder::updateRow($orderId, ['order_status' => $status, 'updated_at' => $now]);
    }

    // ---------- DELIVERY ----------
    public static function createDelivery(array $d)
    {
        global $now;
        $no = GroceryDelivery::nextSerial('DLV', 'delivery_no');
        $did = GroceryDelivery::insertRow([
            'delivery_no' => $no,
            'order_id' => $d['order_id'] ?? null,
            'delivery_date' => $d['delivery_date'] ?? $now,
            'delivery_status_id' => $d['delivery_status_id'] ?? 1,
            'rider_name' => $d['rider_name'] ?? null,
            'rider_phone' => $d['rider_phone'] ?? null,
            'address' => $d['address'] ?? null,
            'remark' => $d['remark'] ?? null,
            'created_by' => static::uid(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($did && !empty($d['order_id'])) {
            GroceryOrder::updateRow((int)$d['order_id'], ['updated_at' => $now]);
        }
        return $did ? ['success' => true, 'id' => $did, 'delivery_no' => $no] : ['success' => false, 'message' => GroceryDelivery::lastError()];
    }

    public static function updateDeliveryStatus($deliveryId, $statusId)
    {
        global $now;
        return GroceryDelivery::updateRow($deliveryId, ['delivery_status_id' => $statusId, 'updated_at' => $now]);
    }

    public static function ensureDelivery($orderId, $rider = '', $phone = '')
    {
        $rows = GroceryDelivery::rows('order_id = ?', [$orderId], 'id DESC', 1);
        if ($rows) return ['success' => true, 'id' => (int)$rows[0]['id'], 'delivery_no' => $rows[0]['delivery_no']];
        return static::createDelivery([
            'order_id' => $orderId,
            'rider_name' => $rider !== '' ? $rider : null,
            'rider_phone' => $phone !== '' ? $phone : null,
            'delivery_status_id' => 1,
        ]);
    }

    // ---------- STOCK ADJUSTMENT ----------
    public static function adjustStock(array $d)
    {
        global $db, $now;
        $items = $d['items'] ?? [];
        $items = array_values(array_filter($items, function ($i) {
            return isset($i['product_id']) && (float)($i['adjusted_quantity'] ?? 0) >= 0;
        }));
        if (!$items) return ['success' => false, 'message' => 'No adjustment items provided'];
        $db->begin_transaction();
        try {
            $type = ($d['adjustment_type'] ?? 'in') === 'in' ? 'in' : 'out';
            $no = GroceryStockAdjustment::nextSerial('ADJ', 'adjustment_no');
            $aid = GroceryStockAdjustment::insertRow([
                'adjustment_no' => $no,
                'adjustment_type' => $type,
                'reason' => $d['reason'] ?? null,
                'remark' => $d['remark'] ?? null,
                'created_by' => static::uid(),
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$aid) throw new Exception('Failed to save adjustment: ' . GroceryStockAdjustment::lastError());
            foreach ($items as $i) {
                $productId = (int)$i['product_id'];
                $stock = static::stockRow($productId);
                $prev = (float)$stock['available_quantity'];
                $new = (float)$i['adjusted_quantity'];
                $requested = abs((float)$i['adjusted_quantity']);
                $delta = $type === 'in' ? $requested : -1 * min($requested, $prev);
                GroceryStockAdjustmentDetail::insertRow([
                    'adjustment_id' => $aid,
                    'product_id' => $productId,
                    'previous_quantity' => $prev,
                    'adjusted_quantity' => $i['adjusted_quantity'],
                    'new_quantity' => $prev + $delta,
                    'remark' => $i['remark'] ?? null,
                ]);
                static::applyMovement($productId, $type === 'in' ? 'adjustment_in' : 'adjustment_out', $delta, 'adjustment', $aid, ($type === 'in' ? 'Add - ' : 'Remove - ') . 'ADJ ' . $no);
            }
            $db->commit();
            return ['success' => true, 'id' => $aid, 'adjustment_no' => $no];
        } catch (Throwable $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ---------- DASHBOARD ----------
    public static function buzz()
    {
        global $tx;
        $nowDate = date('Y-m-d');
        $out = [];

        $q = function ($sql, $params = []) {
            $stmt = GroceryBase::exec($sql, $params);
            if (!$stmt) return null;
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $res;
        };

        $c = function ($sql, $params = []) use ($q) {
            $r = $q($sql, $params);
            return $r ? (int)$r['c'] : 0;
        };
        $s = function ($sql, $params = [], $col = 'total') use ($q) {
            $r = $q($sql, $params);
            return $r ? (float)$r[$col] : 0;
        };

        $out['product_count'] = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_products WHERE status='active'");
        $out['customer_count'] = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_customers WHERE status=1");
        $out['supplier_count'] = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_suppliers WHERE status=1");
        $out['order_count'] = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_orders");
        $out['pending_orders'] = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_orders WHERE order_status IN ('pending','confirmed','processing','packed')");
        $out['purchase_due'] = $s("SELECT SUM(due_amount) AS total FROM {$tx}grocery_purchases WHERE status='received'");
        $out['customer_due'] = $s("SELECT SUM(due_amount) AS total FROM {$tx}grocery_sales WHERE status='completed'");

        $todaySale = $s("SELECT SUM(grand_total) AS total FROM {$tx}grocery_sales WHERE status='completed' AND sale_date = ?", [$nowDate]);
        $totalSale = $s("SELECT SUM(grand_total) AS total FROM {$tx}grocery_sales WHERE status='completed'");
        $totalPurchase = $s("SELECT SUM(grand_total) AS total FROM {$tx}grocery_purchases WHERE status='received'");
        $todayOrders = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_orders WHERE DATE(order_date) = ?", [$nowDate]);
        $todayPurchase = $s("SELECT SUM(grand_total) AS total FROM {$tx}grocery_purchases WHERE purchase_date = ?", [$nowDate]);

        $profit = $s(
            "SELECT SUM((selling_price - cost_price) * quantity - discount) AS total FROM {$tx}grocery_sale_details"
        );
        $netSale = $s("SELECT SUM((selling_price - cost_price) * quantity - discount) AS total FROM {$tx}grocery_sale_details");
        $stockValue = $s("SELECT SUM(p.purchase_price * p.stock_quantity) AS total FROM {$tx}grocery_products p");
        $lowStock = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_products WHERE status='active' AND stock_quantity <= reorder_level");
        $outOfStock = $c("SELECT COUNT(*) AS c FROM {$tx}grocery_products WHERE status='active' AND stock_quantity <= 0");

        $out['summary'] = [
            'today_sales' => $todaySale,
            'total_sales' => $totalSale,
            'total_purchases' => $totalPurchase,
            'today_purchases' => $todayPurchase,
            'today_orders' => $todayOrders,
            'gross_profit' => $profit,
            'net_sales' => $netSale,
            'stock_value' => $stockValue,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ];

        $out['recent_sales'] = [];
        $stmt = static::execStmt(
            "SELECT sa.id, sa.invoice_no, sa.sale_date, sa.grand_total, sa.payment_status, c.name AS customer_name
             FROM {$tx}grocery_sales sa
             LEFT JOIN {$tx}grocery_customers c ON c.id = sa.customer_id
             WHERE sa.status='completed' ORDER BY sa.id DESC LIMIT 6"
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $out['recent_sales'][] = $r;
            $stmt->close();
        }

        $out['recent_orders'] = [];
        $stmt = static::execStmt(
            "SELECT o.id, o.order_no, o.grand_total, o.order_status, c.name AS customer_name
             FROM {$tx}grocery_orders o
             LEFT JOIN {$tx}grocery_customers c ON c.id = o.customer_id
             ORDER BY o.id DESC LIMIT 6"
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $out['recent_orders'][] = $r;
            $stmt->close();
        }

        $out['low_stock_items'] = GroceryProduct::lowStock(8);

        $out['top_products'] = [];
        $stmt = static::execStmt(
            "SELECT p.name, SUM(d.quantity) AS qty, SUM(d.subtotal) AS amount
             FROM {$tx}grocery_sale_details d
             JOIN {$tx}grocery_products p ON p.id = d.product_id
             GROUP BY d.product_id ORDER BY qty DESC LIMIT 5"
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $out['top_products'][] = $r;
            $stmt->close();
        }

        $out['category_distribution'] = [];
        $stmt = static::execStmt(
            "SELECT c.name, COUNT(p.id) AS products
             FROM {$tx}grocery_categories c
             LEFT JOIN {$tx}grocery_products p ON p.category_id = c.id
             GROUP BY c.id ORDER BY products DESC LIMIT 8"
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $out['category_distribution'][] = $r;
            $stmt->close();
        }

        $monthly = [];
        $stmt = static::execStmt(
            "SELECT DATE_FORMAT(sale_date, '%Y-%m') AS ym, SUM(grand_total) AS total
             FROM {$tx}grocery_sales WHERE status='completed' GROUP BY ym ORDER BY ym DESC LIMIT 6"
        );
        $monthMap = [];
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) $monthMap[$r['ym']] = ['sales' => (float)$r['total'], 'purchases' => 0];
            $stmt->close();
        }
        $stmt = static::execStmt(
            "SELECT DATE_FORMAT(purchase_date, '%Y-%m') AS ym, SUM(grand_total) AS total
             FROM {$tx}grocery_purchases WHERE status='received' GROUP BY ym ORDER BY ym DESC LIMIT 6"
        );
        if ($stmt) {
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                if (!isset($monthMap[$r['ym']])) $monthMap[$r['ym']] = ['sales' => 0, 'purchases' => 0];
                $monthMap[$r['ym']]['purchases'] = (float)$r['total'];
            }
            $stmt->close();
        }
        for ($i = 5; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("-$i month"));
            $monthly[] = [
                'ym' => $ym,
                'sales' => $monthMap[$ym]['sales'] ?? 0,
                'purchases' => $monthMap[$ym]['purchases'] ?? 0,
            ];
        }
        $out['monthly'] = $monthly;

        $out['shop'] = [
            'name' => GrocerySetting::get('shop_name', 'UrbanMart Grocery'),
            'phone' => GrocerySetting::get('shop_phone', ''),
            'email' => GrocerySetting::get('shop_email', ''),
            'address' => GrocerySetting::get('shop_address', ''),
            'currency' => GrocerySetting::get('currency_symbol', 'Tk'),
        ];

        return $out;
    }

    private static function execStmt($sql, $params = [])
    {
        return GroceryBase::exec($sql, $params);
    }
}