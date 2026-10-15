# UrbanMart Grocery

Grocery ERP/POS module for the **Core-PHP** framework (plain PHP + MySQL, no framework dependencies).
Includes a web UI (MVC, session-based) and a token-secured JSON REST API.

## Features

- **POS terminal** with product search, tax, invoice generation and JSON checkout
- **Products, categories, subcategories, brands, units** management (with image upload)
- **Stock ledger** — opening/purchase/sale/adjustment movements with `balance_after`, low-stock alerts
- **Customers & suppliers** with balances; **coupons** (percent/fixed)
- **Sales, purchases, orders** with per-line detail and payment tracking
- **Payments** ledger (sale/purchase/order) that updates paid/due and payment status
- **Expenses** and expense categories; **Reports & KPIs**; **Settings**
- **REST API** (`/api/`) — JWT auth (`Groceryauth`), CRUD endpoints for all masters and transactions

## Quick start

1. Import the database:
   - Local: `database/local/urbanmart_grocery_local.sql` (creates `wdpf63_farjana`)
   - Hosting: `database/hosting/urbanmart_grocery_hosting.sql` (import into an existing DB)
2. Point Apache at the project folder, ensure `mod_rewrite` is enabled, and keep `.htaccess`.
3. Check `configs/app.config.php` → `$base_url` matches your folder path.
4. Login: `urbanmart` / `admin123`.

Full instructions: [`docs/SETUP.md`](docs/SETUP.md) · schema: [`docs/DATABASE_SCHEMA.md`](docs/DATABASE_SCHEMA.md) · API + test records: [`docs/API_TESTING.md`](docs/API_TESTING.md)

## Structure

```
├─ home.php            bootstrap (ob_start at top — required)
├─ .htaccess           pretty URLs /api/... and /Module/method/
├─ configs/            app + db config (auto-loads db.hosting.php)
├─ controllers/        MVC controllers (Grocery/*)
├─ models/             models + GroceryService (grocery.model.php)
├─ api/                REST API (route.php, Grocery/*.api.php)
├─ helpers/            grocery.helper.php (grocery_json etc.), api.helper.php
├─ views/              templates + grocery sidebar menus
└─ database/           local + hosting SQL scripts
```

## API one-liner

```
POST /api/Groceryauth/login            {"name":"urbanmart","password":"admin123"}
GET  /api/Groceryproduct?perpage=10    Authorization: Bearer <token>
POST /api/Grocerysale/create           {items:[{product_id, quantity}]}
```

See [`docs/API_TESTING.md`](docs/API_TESTING.md) for the full endpoint reference.

## Environment

- PHP 7.4+/8.x, MySQL/MariaDB, XAMPP or shared hosting
- Styling: `ruangadmin` admin template (`$template` in app.config)
- Time zone: `Asia/Dhaka`