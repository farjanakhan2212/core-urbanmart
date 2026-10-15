# UrbanMart Grocery - Database Schema

Target database: `wdpf63_farjana` (local) / any hosting DB.
All tables use the framework prefix `core_` (defined as `$tx` in `configs/db.config.php`).

Import scripts:
- Local: `database/local/urbanmart_grocery_local.sql` (creates + selects `wdpf63_farjana`)
- Hosting: `database/hosting/urbanmart_grocery_hosting.sql` (no CREATE/USE)

---

## 1. Framework (login) tables

| Table | Purpose | Key columns |
| ----- | ------- | ----------- |
| `core_roles` | user roles | `id`, `name` |
| `core_users` | app users | `id`, `name`, `role_id`, `password` (bcrypt), `email`, `full_name`, `inactive`, `mobile`, `ip` |

Seed login: `urbanmart` / `admin123` (role Admin). `password` is a valid `password_hash()` bcrypt value.

## 2. Master data

| Table | Key columns |
| ----- | ----------- |
| `core_grocery_categories` | `name` (unique), `slug`, `image`, `description`, `status`, `sort_order` |
| `core_grocery_subcategories` | `name`, `slug`, `category_id`, `status`, `sort_order` |
| `core_grocery_brands` | `name`, `slug`, `image`, `status` |
| `core_grocery_units` | `name`, `short_name`, `status` |
| `core_grocery_products` | `name`, `slug`, `sku`, `barcode`, `category_id`, `subcategory_id`, `brand_id`, `unit_id`, `product_type`, `purchase_price`, `selling_price`, `mrp_price`, `discount_type`, `discount_value`, `tax_rate`, `stock_quantity`, `reorder_level`, `weight`, `short/full_description`, `image`, `is_featured`, `status` (enum active/inactive) |
| `core_grocery_product_images` | `product_id`, `image`, `is_primary` |
| `core_grocery_suppliers` | `name`, `company`, `phone`, `email`, `address`, `city`, `opening_balance`, `status` |
| `core_grocery_customers` | `name`, `phone`, `email`, `address`, `city`, `district`, `opening_balance`, `status` |
| `core_grocery_customer_addresses` | `customer_id`, `address`, `city`, `is_default` |
| `core_grocery_payment_methods` | `name` (Cash / Card / Bkash), `status` |
| `core_grocery_coupons` | `code` (unique), `type` (percent/fixed), `value`, `min_order_amount`, `max_discount`, `starts_at`, `ends_at`, `usage_limit`, `used_count`, `status` |
| `core_grocery_expense_categories` | `name`, `description`, `status` |

## 3. Stock

| Table | Key columns |
| ----- | ----------- |
| `core_grocery_stocks` | `product_id` (unique), `quantity`, `available_quantity`, `total_purchased`, `total_sold`, `last_movement_at` — one row per product |
| `core_grocery_stock_movements` | `product_id`, `movement_type` (opening/purchase_in/sale_out/adjustment_in/adjustment_out), `quantity` (+/−), `balance_after`, `reference_type` (purchase/sale/adjustment/opening), `reference_id`, `remark`, `created_by` |
| `core_grocery_stock_adjustments` | `adjustment_no`, `adjustment_type` (in/out), `reason`, `created_by`, `status` |
| `core_grocery_stock_adjustment_details` | `adjustment_id`, `product_id`, `quantity`, `adjusted_quantity` |

## 4. Transactions

| Table | Key columns |
| ----- | ----------- |
| `core_grocery_sales` | `invoice_no` (INV-YYYY-####), `customer_id`, `sale_date`, `discount`, `tax`, `subtotal`, `grand_total`, `paid_amount`, `due_amount`, `payment_method_id`, `payment_status` (paid/partial/unpaid), `status` (completed), `remark`, `created_by` |
| `core_grocery_sale_details` | `sale_id`, `product_id`, `quantity`, `selling_price`, `discount`, `vat`, `cost_price`, `subtotal` |
| `core_grocery_purchases` | `purchase_no` (PR-YYYY-####), `supplier_id`, `purchase_date`, `discount`, `tax`, `subtotal`, `grand_total`, `paid_amount`, `due_amount`, `payment_method_id`, `status`, `created_by` |
| `core_grocery_purchase_details` | `purchase_id`, `product_id`, `quantity`, `purchase_price`, `subtotal` |
| `core_grocery_orders` | `order_no` (ORD-YYYY-####), `customer_id`, `order_date`, `discount`, `tax`, `subtotal`, `grand_total`, `delivery_charge`, `customer_address_id`, `delivery_address`, `delivery_date`, `paid_amount`, `due_amount`, `payment_method_id`, `payment_status`, `order_status` (pending/confirmed/processing/packed/shipped/delivered/cancelled), `created_by` |
| `core_grocery_order_details` | `order_id`, `product_id`, `quantity`, `price`, `discount`, `vat`, `subtotal` |
| `core_grocery_delivery_statuses` | `name`, `sort_order` |
| `core_grocery_deliveries` | `delivery_no`, `order_id`, `rider_name`, `delivery_date`, `status` |
| `core_grocery_payments` | `payment_no`, `reference_type` (sale/purchase/order), `reference_id`, `payable_id`, `customer_id`, `supplier_id`, `payment_method_id`, `amount`, `transaction_id`, `note`, `created_by` |

## 5. Settings

`core_grocery_settings` — key/value pairs:
`business_name`, `business_logo`, `business_address`, `currency_symbol`, `sale_prefix` (SL), `order_prefix` (ORD),
`purchase_prefix` (PR), `payment_prefix` (PMT), `default_tax_rate` (5), `default_payment_method` (1),
`low_stock_threshold` (10), `delivery_charge` (40), `free_delivery_min` (1000).

---

## 6. Stock ledger invariants

- Every outflow (`sale_out`) and inflow (`purchase_in`, `adjustment_in`, `opening`) writes a `core_grocery_stock_movements` row with `balance_after`.
- `core_grocery_stocks.available_quantity` is derived by applying movements; POS shows `IFNULL(stocks.available_quantity, products.stock_quantity)`.
- Sales/orders/purchases reduce/increase available stock atomically via `GroceryService::createSale / createOrder / createPurchase / adjustStock / syncProductStock` (see `models/Grocery/grocery.model.php`).