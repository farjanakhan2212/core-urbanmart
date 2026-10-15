# UrbanMart Grocery - Setup Guide

> Local (XAMPP/WAMP) and shared-hosting installation for the **UrbanMart Grocery** ERP/POS module built on the Core-PHP framework.

## 1. Requirements

- **Local:** XAMPP or WAMP (Apache + PHP 7.4 / 8.x + MySQL/MariaDB)
- **Hosting:** Any PHP/MySQL shared host with `.htaccess` + `mod_rewrite` enabled (cPanel/InfinityFree etc.)
- The app runs at the web root of its sub-folder, e.g. `http://localhost/Farjana/Projects/core/UrbanMart`

## 2. Folder layout

```
UrbanMart/
 ├─ home.php            bootstrap (ob_start at top - required)
 ├─ .htaccess           pretty URLs /api/... and /Module/method/...
 ├─ configs/
 │   ├─ app.config.php  sector=grocery, app_name, base_url, template
 │   └─ db.config.php   DB credentials (auto-loads db.hosting.php on hosting)
 ├─ controllers/Grocery/  MVC controllers
 ├─ models/Grocery/grocery.model.php  domain models + GroceryService
 ├─ api/               REST API (api.php + route.php + Grocery/*.api.php)
 ├─ helpers/           grocery.helper.php (grocery_json, row helpers), api.helper.php
 ├─ views/..., views/menus/grocery/   templates + grocery sidebar menus
 └─ database/          importable SQL scripts
```

## 3. Install locally (XAMPP/WAMP)

1. Copy the project to `C:\xampp\htdocs\Farjana\Projects\core\UrbanMart` (or your htdocs path).
2. Start Apache + MySQL.
3. Import the database:
   - phpMyAdmin → **Import** → `database/local/urbanmart_grocery_local.sql`
   - or CLI: `mysql -uroot -p < database/local/urbanmart_grocery_local.sql`
   - The local script creates the `wdpf63_farjana` database, all `core_grocery_*` tables and demo data.
4. Verify `configs/app.config.php`:
   - `$sector = "grocery";`
   - `$base_url = "/Farjana/Projects/core/UrbanMart";` — must match your folder path on the server.
   - `$template = "ruangadmin";`
   - Adjust only if the project lives in another folder (e.g. `/Projects/core`, `/intellect8`).
5. Open `http://localhost/Farjana/Projects/core/UrbanMart/` and log in.

## 4. Install on shared hosting

1. Upload all files to the hosting web root (e.g. `htdocs/` or `public_html/`) keeping the structure.
2. Create a MySQL database + user in your hosting control panel.
3. Import `database/hosting/urbanmart_grocery_hosting.sql` via phpMyAdmin **into the newly created database**.
   - The hosting script does **not** `CREATE DATABASE` / `USE` (your control panel already created the DB).
4. Create `configs/db.hosting.php` with your hosting credentials (it overrides the defaults):

   ```php
   <?php
   $DB_HOST     = "sqlXXX.yourhost.com";
   $DB_USER     = "your_db_user";
   $DB_PASSWORD = "your_db_password";
   $DB_DATABASE = "your_db_name";
   ```

5. Update `configs/app.config.php`:
   - `$base_url = "your_root_path_if_not_web_root_and_not_https_base";`
   - On most hosts the app is at the web root, so `$base_url = "";`
6. Make sure a `.htaccess` is present (it is already shipped) and that Apache `AllowOverride All` / `mod_rewrite` is enabled.

## 5. Default login

| Username | Password | Role  |
| -------- | -------- | ----- |
| `urbanmart` | `admin123` | Admin |

## 6. Main module URLs (MVC)

```
/                              → login
/GroceryPos                    → POS Terminal (checkout returns JSON, sale list)
/GrocerySale                   → sales & invoices
/GroceryPurchase               → purchases
/GroceryOrder                  → orders
/GroceryDelivery               → deliveries
/GroceryProduct                → products (+ /create, search JSON)
/GroceryStock                  → stock report, /movements, /adjustments
/GroceryCategory               → categories
/GrocerySubcategory            → subcategories
/GroceryBrand                  → brands
/GroceryUnit                   → units
/GrocerySupplier               → suppliers
/GroceryCustomer               → customers
/GroceryCoupon                 → coupons
/GroceryPayment                → payments
/GroceryExpense                → expenses (+ /create)
/GroceryExpenseCategory        → expense categories
/GroceryReport                 → reports & KPIs
/GrocerySettings               → settings
```

## 7. API

The REST API lives under `/api/`, e.g. `POST /api/Groceryauth/login`, `GET /api/Groceryproduct?perpage=10`.
See `docs/API_TESTING.md` for the endpoint reference and authenticated examples.

## 8. Troubleshooting

- **Route to JSON endpoints shows HTML or "Invalid JSON":** the layout renderer must not run before controllers. `ob_start()` is the first line of `home.php` — do not remove it. JSON controllers must return via `grocery_json()`.
- **`Call to undefined function _parseGet()`:** the GET param handling in `api/route.php` was fixed to read query params directly; do not reintroduce `_parseGet()`.
- **Permissions:** ensure `uploads/` (product images) is writable on hosting.
- **Base URL wrong (assets 404):** fix `$base_url` in `configs/app.config.php`.