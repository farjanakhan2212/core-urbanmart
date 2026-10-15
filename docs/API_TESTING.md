# UrbanMart Grocery - API Testing

This document records automated end-to-end verification of the MVC write flow **and** the REST API.
Both suites ran against the live app on XAMPP (`http://localhost/Farjana/Projects/core/UrbanMart`).

---

## 1. MVC write-flow E2E (browser/curl, session login)

Harness: `C:\Users\LENOVO\AppData\Local\Temp\opencode\grocery_write_flow.ps1`

Login: `POST /login` `urbanmart` / `admin123` → stores cookie, then posts real forms (matching
`form_action` hidden fields the app routes render).

| # | Operation | Result |
| - | --------- | ------ |
| 1 | Category create | `Saved` (created 16-20) |
| 2 | Category update | rename persisted (verified via DB) |
| 3 | Category delete | deleted |
| 4 | Expense create / update / delete | saved + renamed + removed |
| 5 | Product create with image upload | product 44, image `wfe2e-product.png`, stock row synced available=5 |
| 6 | POS checkout | pure JSON `{"success":true,"id":15,"invoice_no":"INV-2026-0013","redirect":".../invoice/15"}` |
| 7 | Purchase create | `PR-2026-0007`, 5×3.00=15.00 paid |
| 8 | Order create | `ORD-2026-0005`, `shipping_fee`→`delivery_charge`=10, grand 15.50 partial |
| 9 | Stock adjustment (+10) | product 1 available 210; later +5 → 213 |
| 10 | Payment save | 200 |
| 11 | Product search | pure JSON `{"success":1,"items":[...]}` |

Read crawl (`grocery_http_e2e.ps1`): all grocery MVC pages returned HTTP 200 with no PHP error markers
(`Fatal`, `Warning:`, `Notice:`, `Parse error`).

Key regression fixed during this suite: JSON endpoints were previously wrapped in layout HTML
(`home.php` runs the master layout before `route.php`). Fix: `ob_start()` at top of `home.php` +
`grocery_json()` helper (buffers cleared, JSON header, exit).

---

## 2. REST API

Auth model: `POST /api/Groceryauth/login` returns a JWT (`Authorization: Bearer <token>`).
All other endpoints require the Bearer header; an invalid/absent token returns `401 {"success":0,...}`.

### 2.1 Auth

| Endpoint | Method | Body (JSON) | Result |
| -------- | ------ | ----------- | ------ |
| `/api/Groceryauth/login` | POST | `{"name":"urbanmart","password":"admin123"}` | `{"success":1,"token":"<~415 chars>","user":{id,name,role_id,full_name,...}}` |
| `/api/Groceryauth/validate` | GET | – | `{"success":1,"message":"Token is valid","user":{...}}` |
| `/api/Groceryauth/me` | GET | – | `{"success":1,"user":{id,name,role_id,role,full_name,email,mobile}}` |

### 2.2 Entities (require Bearer)

| Endpoint | Method | Notes |
| -------- | ------ | ----- |
| `/api/Grocerycategory` | GET | `?q=&page=&perpage=` — q-search verified (`q=Fruits` → 1 row) |
| `/api/Grocerycategory/find` | GET | `?id=` (or path `/find/{id}`) → 404 JSON if missing |
| `/api/Grocerycategory/save` / `update` / `delete` | POST | fields: name,slug,image,description,status,sort_order |
| `/api/Grocerycustomer` | GET | q on name,phone,email,city,address,district |
| `/api/Grocerycustomer/find` `save` `update` `delete` | – | name,phone,email,address,city,district,opening_balance,status |
| `/api/Grocerysupplier` + `find` `save` `update` `delete` | – | name,company,phone,email,address,city,opening_balance,status |
| `/api/Grocerycoupon` + `find` `save` `update` `delete` | – | code,type,value,min_order_amount,max_discount,starts_at,ends_at,usage_limit,status |

### 2.3 Product & stock

| Endpoint | Method | Notes |
| -------- | ------ | ----- |
| `/api/Groceryproduct` | GET | full grid w/ joined names; `?low=1` → low stock only |
| `/api/Groceryproduct/low` `search` `find` | GET | search: `?q=rice` → POS items; find: `?id=` detailed + images |
| `/api/Groceryproduct/save` `update` `delete` | POST | save syncs `core_grocery_stocks` (stock_quantity) |
| `/api/Grocerystock` | GET | report rows (low via `?low=1`) |
| `/api/Grocerystock/movements` | GET | `?product_id=&type=&perpage=` |
| `/api/Grocerystock/adjust` | POST | `{adjustment_type:"in|out", reason, items:[{product_id, adjusted_quantity}]}` |

### 2.4 Transactions

| Endpoint | Method | Body / Notes |
| -------- | ------ | ------------ |
| `/api/Grocerysale` | GET | grid w/ joined customer + payment method |
| `/api/Grocerysale/find` | GET | sale + customer + lines |
| `/api/Grocerysale/create` | POST | `{customer_id?, items:[{product_id, quantity, selling_price?}], payment_method_id?, paid_amount?, tax?}` → `{id, invoice_no}` |
| `/api/Grocerypurchase/create` + `/find` | POST/GET | items need `purchase_price` → `{id, purchase_no}` |
| `/api/Groceryorder` | GET | `?status=` filter |
| `/api/Groceryorder/find` `create` | GET/POST | create: items `{product_id, quantity, price?}`, header `delivery_charge/delivery_address/delivery_date` |
| `/api/Groceryorder/status` | POST | `{id, status}` — validates against pending/confirmed/processing/packed/shipped/delivered/cancelled |
| `/api/Grocerypayment` | GET | payment ledger |
| `/api/Grocerypayment/create` | POST | `{payable_type: sale|purchase|order, payable_id, amount, payment_method_id?}`; updates paid/due + payment_status |

### 2.5 Recorded E2E run (tool `grocery_api_e2e.ps1`, all green)

| Step | Response |
| ---- | -------- |
| category save | `{"success":1,"message":"Saved","id":26}` |
| category q-search | `total:1` (matches the created row only) |
| category update | `Updated` (name changed) |
| customer save + find | row returned with generated name/phone |
| supplier save + find | row returned |
| coupon save + find | row returned |
| product save + find | `id:46`; `available:"25.000"` (stock row created from stock_quantity) |
| sale create | `{"success":1,"id":17,"invoice_no":"INV-2026-0015"}` |
| purchase create | `{"success":1,"id":12,"purchase_no":"PR-2026-0010"}` |
| order create + status | `ORD-2026-0008` → `"Order status updated ... status":"delivered"` |
| stock adjust | `ADJ-2026-0006`; movement `adjustment_in +5 → balance_after 222` |
| stock movements | ledger `sale_out -2 → 214`, `purchase_in +3 → 217`, `adjustment_in +5 → 222` |
| payment create on sale 7 | `Payment recorded`; sale paid 1500→1600, due 480→380, status still partial |
| cleanup deletes | category/customer/supplier/coupon/product all Deleted |

### 2.6 API-router fixes applied during verification

1. Removed the broken `_parseGet()` / `count($params)==2` branch — it fatally errored on any GET
   whose query contained exactly `class`+`method` (e.g. `validate`, `me`, `find`). Query params are now
   read straight from `$_GET` (`unset(class, method)`).
2. `.htaccess` sets `method=` (empty) for `/api/{Class}` — previously `method_exists` on `""` was false,
   so `index()` ran through the fallback which **discarded query params**. Empty method now defaults to `index`
   and the fallback path also forwards `$_GET` params.
3. Fallback (`method not found`) now builds params from `$_GET`, so `/api/Grocerycategory/list?q=...` still
   serves an index with `q`.
4. `grid()` in the model composed `"AND (...)"` when no base `$where` existed → invalid SQL returned 0 rows.
   Fixed to prefix WITH only when base where present.
5. `GroceryApiBase::pickFields()` visibility `private` → `protected` (children needed it).
6. `api/Inventory/product.api.php` `save()` implemented (was a stub) and `update()` photo handling fixed.

## 3. Curl quickstart

```bash
# login
curl -s -X POST http://localhost/Farjana/Projects/core/UrbanMart/api/Groceryauth/login \
  -H "Content-Type: application/json" \
  -d '{"name":"urbanmart","password":"admin123"}'

# authenticated call (token from login)
curl -s http://localhost/Farjana/Projects/core/UrbanMart/api/Groceryproduct?perpage=10 \
  -H "Authorization: Bearer <token>"

# JSON bodies on PowerShell: write to a file and use  --data-binary @file   (inline quotes get mangled)
```