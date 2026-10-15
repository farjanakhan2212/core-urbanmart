-- ============================================================
-- URBANMART GROCERY - LOCAL DATABASE SCRIPT
-- ============================================================
-- Target      : wdpf63_farjana
-- Engine      : MySQL / MariaDB (XAMPP / WAMP local environment)
-- Charset     : utf8mb4
-- File        : database/local/urbanmart_grocery_local.sql
--
-- Compatible with phpMyAdmin import. This script:
--   * Creates the wdpf63_farjana database if it does not exist
--   * Creates all `core_grocery_*` tables (IF NOT EXISTS)
--   * Creates the Core-PHP login framework tables (core_users,
--     core_roles) only if they do not exist yet
--   * Seeds realistic Bangladeshi grocery demo data
--
-- NOTE: This is the LOCAL version. The HOSTING version
-- (database/hosting/urbanmart_grocery_hosting.sql) is identical
-- but does NOT create/select a database and is safe to import
-- into an already selected hosting database.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- ============================================================
-- DATABASE
-- ============================================================
CREATE DATABASE IF NOT EXISTS `wdpf63_farjana` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wdpf63_farjana`;

-- ============================================================
-- CORE FRAMEWORK TABLES (created only if missing)
-- ============================================================
CREATE TABLE IF NOT EXISTS `core_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `core_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `role_id` int(10) DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(50) DEFAULT NULL,
  `verify_code` varchar(50) DEFAULT NULL,
  `inactive` tinyint(1) unsigned DEFAULT 0,
  `mobile` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(145) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`name`) USING BTREE,
  KEY `core_users_branch_id_index` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT IGNORE INTO `core_roles` (`id`,`name`,`created_at`) VALUES
(1,'Admin','2026-01-01 00:00:00'),
(2,'Manager','2026-01-01 00:00:00');

-- Demo login (username: urbanmart / password: admin123)
INSERT IGNORE INTO `core_users`
(`id`,`name`,`role_id`,`password`,`email`,`full_name`,`created_at`,`photo`,`inactive`,`mobile`,`updated_at`,`ip`) VALUES
(1,'urbanmart',1,'$2y$10$MioftixI.zkbSBNBeGdmgOR2QauKXPv6ItAklKlZxnPklzSZjdkVu','admin@urbanmart.com','UrbanMart Admin','2026-01-01 00:00:00','','0','01700000000','2026-01-01 00:00:00','127.0.0.1');

-- ============================================================
-- GROCERY MASTER DATA
-- ============================================================

-- ---------- categories ----------
CREATE TABLE IF NOT EXISTS `core_grocery_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_categories_name` (`name`),
  KEY `idx_grocery_categories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_categories`
(`id`,`name`,`slug`,`image`,`description`,`status`,`sort_order`,`created_at`,`updated_at`) VALUES
(1,'Fruits & Vegetables','fruits-vegetables','grocery-1232944-1280-jpg.jpg','Fresh fruits and seasonal vegetables',1,1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'Rice, Dal & Grains','rice-dal-grains','rice-webp.webp','Rice, pulses, flour and grains',1,2,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'Cooking Oil','cooking-oil','grocery-1232944-1280-jpg.jpg','Soybean, mustard and other cooking oils',1,3,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,'Spices','spices','grocery-1232944-1280-jpg.jpg','Whole and powder spices',1,4,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,'Dairy & Eggs','dairy-eggs','grocery-1232944-1280-jpg.jpg','Milk, eggs, butter & cheese',1,5,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,'Meat & Fish','meat-fish','grocery-1232944-1280-jpg.jpg','Fresh meat, chicken and fish',1,6,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,'Beverages','beverages','grocery-1232944-1280-jpg.jpg','Soft drinks, water, tea & coffee',1,7,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(8,'Snacks & Biscuits','snacks-biscuits','biscuit-jpg.jpg','Biscuits, chips and namkeen',1,8,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(9,'Bakery','bakery','biscuit-jpg.jpg','Bread, cakes & pastries',1,9,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(10,'Frozen Food','frozen-food','grocery-1232944-1280-jpg.jpg','Frozen vegetables & snacks',1,10,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(11,'Personal Care','personal-care','grocery-1232944-1280-jpg.jpg','Shampoo, soap, oral care & grooming',1,11,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(12,'Household Cleaning','household-cleaning','grocery-1232944-1280-jpg.jpg','Dishwash, detergent & floor cleaners',1,12,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(13,'Baby Care','baby-care','grocery-1232944-1280-jpg.jpg','Baby food, diapers & care items',1,13,'2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ---------- subcategories ----------
CREATE TABLE IF NOT EXISTS `core_grocery_subcategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_subcategories` (`category_id`,`name`),
  KEY `idx_grocery_subcategories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_subcategories`
(`id`,`category_id`,`name`,`slug`,`status`,`created_at`,`updated_at`) VALUES
(1,1,'Fruits','fruits',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,1,'Vegetables','vegetables',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,2,'Rice','rice',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,2,'Dal & Pulses','dal-pulses',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,2,'Flour & Grains','flour-grains',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,3,'Cooking Oil','cooking-oil',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,3,'Ghee & Butter','ghee-butter',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(8,4,'Whole Spices','whole-spices',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(9,4,'Powder Spices','powder-spices',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(10,5,'Milk','milk',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(11,5,'Eggs','eggs',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(12,5,'Cheese & Butter','cheese-butter',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(13,6,'Fresh Meat','fresh-meat',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(14,6,'Fish','fish',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(15,7,'Soft Drinks','soft-drinks',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(16,7,'Juices','juices',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(17,7,'Tea & Coffee','tea-coffee',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(18,7,'Mineral Water','mineral-water',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(19,8,'Biscuits & Cookies','biscuits-cookies',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(20,8,'Chips & Namkeen','chips-namkeen',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(21,9,'Bread','bread',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(22,9,'Cakes & Pastries','cakes-pastries',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(23,10,'Frozen Vegetables','frozen-vegetables',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(24,10,'Frozen Snacks','frozen-snacks',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(25,11,'Shampoo & Soap','shampoo-soap',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(26,11,'Oral Care','oral-care',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(27,12,'Dishwashing','dishwashing',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(28,12,'Laundry','laundry',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(29,13,'Baby Food','baby-food',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(30,13,'Diapers','diapers',1,'2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ---------- brands ----------
CREATE TABLE IF NOT EXISTS `core_grocery_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_brands_name` (`name`),
  KEY `idx_grocery_brands_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_brands`
(`id`,`name`,`slug`,`status`,`created_at`,`updated_at`) VALUES
(1,'Pran','pran',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'ACI','aci',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'Teer','teer',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,'Radhuni','radhuni',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,'Bashundhara','bashundhara',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,'Nestle','nestle',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,'Unilever','unilever',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(8,'Square','square',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(9,'Coca-Cola','coca-cola',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(10,'Akij','akij',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(11,'Olympic','olympic',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(12,'Fresh Dairy','fresh-dairy',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(13,'Aarong Dairy','aarong-dairy',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(14,'Milk Vita','milk-vita',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(15,'Danish','danish',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(16,'Kazi Farms','kazi-farms',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(17,'City Group','city-group',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(18,'Kismat','kismat',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(19,'Ispahani','ispahani',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(20,'Colgate','colgate',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(21,'Unicharm','unicharm',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(22,'Local','local',1,'2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ---------- units ----------
CREATE TABLE IF NOT EXISTS `core_grocery_units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_units_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_units`
(`id`,`name`,`short_name`,`status`,`created_at`,`updated_at`) VALUES
(1,'Kilogram','kg',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'Gram','gm',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'Litre','L',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,'Millilitre','ml',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,'Piece','pcs',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,'Pack','pack',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,'Dozen','dozen',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(8,'Box','box',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(9,'Carton','carton',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(10,'Bag','bag',1,'2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ---------- products ----------
CREATE TABLE IF NOT EXISTS `core_grocery_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `subcategory_id` int(10) unsigned DEFAULT NULL,
  `brand_id` int(10) unsigned DEFAULT NULL,
  `unit_id` int(10) unsigned DEFAULT NULL,
  `product_type` enum('goods','service') DEFAULT 'goods',
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mrp_price` decimal(12,2) DEFAULT 0.00,
  `discount_type` enum('fixed','percent') DEFAULT 'fixed',
  `discount_value` decimal(12,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `stock_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `reorder_level` decimal(12,3) NOT NULL DEFAULT 0.000,
  `weight` decimal(10,3) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_products_sku` (`sku`),
  UNIQUE KEY `uk_grocery_products_barcode` (`barcode`),
  KEY `idx_grocery_products_category` (`category_id`),
  KEY `idx_grocery_products_subcategory` (`subcategory_id`),
  KEY `idx_grocery_products_brand` (`brand_id`),
  KEY `idx_grocery_products_unit` (`unit_id`),
  KEY `idx_grocery_products_status` (`status`),
  KEY `idx_grocery_products_featured` (`is_featured`),
  KEY `idx_grocery_products_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_products`
(`id`,`name`,`slug`,`sku`,`barcode`,`category_id`,`subcategory_id`,`brand_id`,`unit_id`,`product_type`,`purchase_price`,`selling_price`,`mrp_price`,`discount_type`,`discount_value`,`tax_rate`,`stock_quantity`,`reorder_level`,`weight`,`short_description`,`full_description`,`image`,`is_featured`,`status`,`created_at`,`updated_at`) VALUES
(1,'Fresh Potato','fresh-potato','UM-FRV-POT','8864003611101',1,2,17,1,'goods',28.00,35.00,40.00,'fixed',0.00,5.00,200.000,50.000,1.000,'Big size fresh potato','Freshly harvested big size potato, suitable for all curries.','grocery-1232944-1280-jpg.jpg',1,'active','2026-08-01 10:05:00','2026-08-01 10:05:00'),
(2,'Tomato','tomato','UM-FRV-TOM','8864003611220',1,2,22,1,'goods',35.00,50.00,55.00,'fixed',0.00,5.00,120.000,40.000,1.000,'Ripe juicy tomato','Fresh ripe tomatoes, perfect for salads and cooking.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:05:00','2026-08-01 10:05:00'),
(3,'Onion','onion','UM-FRV-ONI','8864003611331',1,2,17,1,'goods',42.00,55.00,60.00,'fixed',0.00,5.00,300.000,80.000,1.000,'Nepali onion (imported)','Medium-size imported onion, strong flavour.','onion-jpg.jpg',1,'active','2026-08-01 10:05:00','2026-08-01 10:05:00'),
(4,'Red Apple','red-apple','UM-FRV-APL','8864003611442',1,1,17,1,'goods',160.00,200.00,230.00,'fixed',0.00,5.00,80.000,25.000,1.000,'Red Delicious apple','Imported Red Delicious apple, sweet and crunchy.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:05:00','2026-08-01 10:05:00'),
(5,'Miniket Rice','miniket-rice','UM-RGR-RIC1','8864003612223',2,3,1,1,'goods',78.00,90.00,95.00,'fixed',0.00,5.00,500.000,100.000,1.000,'Miniket rice 1kg','Premium Miniket rice, soft and aromatic.','rice-webp.webp',1,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(6,'Nazirshail Rice','nazirshail-rice','UM-RGR-RIC2','8864003612334',2,3,1,1,'goods',68.00,80.00,85.00,'fixed',0.00,5.00,400.000,100.000,1.000,'Nazirshail rice 1kg','Popular Nazirshail rice with nice taste.','rice-webp.webp',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(7,'Masoor Dal','masoor-dal','UM-RGR-DAL1','8864003612445',2,4,3,1,'goods',120.00,140.00,150.00,'fixed',0.00,5.00,250.000,50.000,1.000,'Red lentil (masoor dal)','Cleaned red lentil, cooks fast.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(8,'Moong Dal','moong-dal','UM-RGR-DAL2','8864003612556',2,4,2,1,'goods',140.00,160.00,170.00,'fixed',0.00,5.00,150.000,40.000,1.000,'Yellow moong dal','High quality yellow moong dal.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(9,'Bashundhara Atta','bashundhara-atta','UM-RGR-ATT','8864003612667',2,5,5,1,'goods',52.00,65.00,70.00,'fixed',0.00,5.00,300.000,80.000,1.000,'Wheat flour (atta) 1kg','Fine wheat flour for roti and baking.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(10,'White Sugar','white-sugar','UM-RGR-SUG','8864003612778',2,5,17,1,'goods',110.00,125.00,130.00,'fixed',0.00,5.00,350.000,60.000,1.000,'Refined white sugar 1kg','Pure refined crystalline sugar.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(11,'Iodized Salt','iodized-salt','UM-RGR-SAL','8864003612889',2,5,18,1,'goods',22.00,30.00,32.00,'fixed',0.00,0.00,400.000,100.000,1.000,'Iodized salt 1kg','Double refined iodized salt.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(12,'Soybean Oil','soybean-oil','UM-OIL-SOY','8864003613112',3,6,17,3,'goods',145.00,165.00,170.00,'fixed',0.00,5.00,180.000,60.000,1.000,'Refined soybean oil 1L','Clear refined soybean oil for everyday cooking.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(13,'Mustard Oil','mustard-oil','UM-OIL-MUS','8864003613223',3,6,18,3,'goods',230.00,260.00,270.00,'fixed',0.00,5.00,100.000,40.000,1.000,'Pure mustard oil 1L','Cold-pressed pure mustard oil.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(14,'Turmeric Powder','turmeric-powder','UM-SPC-TUR','8864003613334',4,9,4,2,'goods',90.00,110.00,120.00,'fixed',0.00,5.00,90.000,30.000,0.200,'Turmeric powder 200g','Pure ground turmeric with rich colour.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(15,'Chili Powder','chili-powder','UM-SPC-CHI','8864003613445',4,9,4,2,'goods',110.00,135.00,145.00,'fixed',0.00,5.00,80.000,30.000,0.200,'Red chili powder 200g','Selected hot red chili powder.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(16,'Cinnamon','cinnamon','UM-SPC-CIN','8864003613556',4,8,2,2,'goods',70.00,90.00,100.00,'fixed',0.00,5.00,60.000,20.000,0.100,'Cinnamon (daruchini) 100g','Fragrant whole cinnamon sticks.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(17,'Milk Vita Milk','milk-vita-milk','UM-DAI-MLK','8864003613667',5,10,14,3,'goods',90.00,105.00,110.00,'fixed',0.00,5.00,250.000,100.000,1.000,'UHT milk 1L','Milk Vita pasteurised UHT milk.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(18,'Farm Egg','farm-egg','UM-DAI-EGG','8864003613778',5,11,12,7,'goods',130.00,150.00,155.00,'fixed',0.00,0.00,100.000,30.000,0.600,'Deshi farm eggs (dozen)','Fresh farm eggs, tray of 12 pieces.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(19,'Aarong Butter','aarong-butter','UM-DAI-BUT','8864003613889',5,12,13,2,'goods',220.00,260.00,280.00,'fixed',0.00,5.00,40.000,15.000,0.200,'Salted butter 200g','Creamy salted table butter.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(20,'Aarong Cheese','aarong-cheese','UM-DAI-CHS','8864003613990',5,12,13,2,'goods',280.00,320.00,350.00,'fixed',0.00,5.00,35.000,10.000,0.250,'Processed cheese 250g','Smooth processed cheese slices.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(21,'Beef (Boneless)','beef-boneless','UM-MEF-BEE','8864003614111',6,13,22,1,'goods',700.00,780.00,800.00,'fixed',0.00,0.00,50.000,15.000,1.000,'Fresh boneless beef','Fresh halal boneless beef, cut per piece.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(22,'Broiler Chicken','broiler-chicken','UM-MEF-CHK','8864003614222',6,13,22,1,'goods',210.00,240.00,250.00,'fixed',0.00,0.00,70.000,30.000,1.000,'Broiler chicken (whole)','Whole broiler chicken, cleaned.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(23,'Ruhi Fish','ruhi-fish','UM-MEF-RUH','8864003614333',6,14,22,1,'goods',260.00,300.00,320.00,'fixed',0.00,0.00,60.000,20.000,1.000,'Ruhi fish cut pieces','Fresh Ruhi fish, cut pieces.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(24,'Hilsa Fish','hilsa-fish','UM-MEF-HIL','8864003614444',6,14,22,1,'goods',900.00,1100.00,1200.00,'fixed',0.00,0.00,20.000,25.000,1.000,'Fresh hilsa fish','Famous Padma river hilsa, one piece.','grocery-1232944-1280-jpg.jpg',1,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(25,'Sprite 1.25L','sprite-1-25l','UM-BEV-SPR','8864003614555',7,15,9,3,'goods',60.00,75.00,80.00,'fixed',0.00,5.00,120.000,60.000,1.250,'Sprite 1.25 litre','Ice cold lemon-lime soft drink bottle.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(26,'Coca-Cola 500ml','coca-cola-500ml','UM-BEV-COK','8864003614666',7,15,9,4,'goods',28.00,35.00,40.00,'fixed',0.00,5.00,150.000,80.000,0.500,'Coca-Cola 500ml','Original Coca-Cola PET bottle 500ml.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(27,'Mineral Water 2L','mineral-water-2l','UM-BEV-WAT','8864003614777',7,18,17,3,'goods',25.00,35.00,40.00,'fixed',0.00,5.00,200.000,100.000,2.000,'Drinking water 2L','Purified mineral drinking water bottle.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(28,'Ispahani Tea','ispahani-tea','UM-BEV-TEA','8864003614888',7,17,19,2,'goods',380.00,450.00,470.00,'fixed',0.00,5.00,45.000,20.000,0.500,'Ispahani Mirzapore tea 500g','Classic CTC tea in fresh pouch.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(29,'Nescafe Classic','nescafe-classic','UM-BEV-COF','8864003614999',7,17,6,2,'goods',500.00,580.00,600.00,'fixed',0.00,5.00,30.000,20.000,0.200,'Instant coffee 200g','Nescafe classic instant coffee jar.','grocery-1232944-1280-jpg.jpg',1,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(30,'Danish Marie Biscuit','danish-marie-biscuit','UM-SNB-MAR','8864003615111',8,19,15,2,'goods',55.00,70.00,75.00,'fixed',0.00,5.00,200.000,100.000,0.200,'Marie biscuits 200g','Classic tea-time Marie biscuits.','biscuit-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(31,'Banana Chips','banana-chips','UM-SNB-BNC','8864003615222',8,20,1,2,'goods',90.00,110.00,120.00,'fixed',0.00,5.00,60.000,20.000,0.150,'Banana chips 150g','Crispy spicy banana chips.','biscuit-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(32,'Potato Chips','potato-chips','UM-SNB-CHP','8864003615333',8,20,10,2,'goods',18.00,25.00,28.00,'fixed',0.00,5.00,250.000,100.000,0.040,'Potato chips 40g','Crunchy salted potato chips.','biscuit-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(33,'Fresh Bread','fresh-bread','UM-BAK-BRD','8864003615444',9,21,12,5,'goods',55.00,70.00,75.00,'fixed',0.00,5.00,80.000,40.000,0.500,'Sliced bread 500g','Soft fresh sliced bread loaf.','biscuit-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(34,'Frozen Paratha','frozen-paratha','UM-FRD-PAR','8864003615555',10,24,16,2,'goods',90.00,110.00,115.00,'fixed',0.00,5.00,75.000,30.000,0.400,'Frozen paratha 400g','Ready-to-cook frozen paratha, 6 pieces.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(35,'Fish Finger','fish-finger','UM-FRD-FFI','8864003615666',10,24,16,2,'goods',180.00,220.00,240.00,'fixed',0.00,5.00,0.000,20.000,0.500,'Fish finger frozen 500g','Crispy frozen fish fingers.','grocery-1232944-1280-jpg.jpg',0,'inactive','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(36,'Sunsilk Shampoo','sunsilk-shampoo','UM-PRR-SHA','8864003615777',11,25,7,5,'goods',120.00,145.00,150.00,'fixed',0.00,5.00,90.000,30.000,0.250,'Sunsilk shampoo 100ml','Sunsilk smooth & manageable shampoo.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(37,'Lux Soap','lux-soap','UM-PRR-SOA','8864003615888',11,25,7,5,'goods',55.00,70.00,75.00,'fixed',0.00,5.00,120.000,50.000,0.100,'Lux soft touch soap 100g','Lux international beauty soap.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(38,'Colgate Toothpaste','colgate-toothpaste','UM-PRR-TPA','8864003615999',11,26,20,5,'goods',130.00,155.00,160.00,'fixed',0.00,5.00,85.000,30.000,0.150,'Colgate toothpaste 150g','Dental protection toothpaste.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(39,'Wheel Dishwash','wheel-dishwash','UM-HHC-DSW','8864003616111',12,27,7,4,'goods',120.00,145.00,150.00,'fixed',0.00,5.00,70.000,30.000,0.450,'Dishwashing liquid 450ml','Cutting oil dishwashing liquid.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(40,'Surf Excel 1kg','surf-excel-1kg','UM-HHC-DET','8864003616222',12,28,7,1,'goods',210.00,240.00,245.00,'fixed',0.00,5.00,65.000,30.000,1.000,'Detergent powder 1kg','Surf Excel easy wash detergent powder.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(41,'Harpic Floor Cleaner','harpic-floor-cleaner','UM-HHC-FLC','8864003616333',12,27,7,4,'goods',130.00,155.00,160.00,'fixed',0.00,5.00,60.000,30.000,0.500,'Floor cleaner 500ml','Disinfectant bathroom & floor cleaner.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(42,'MamyPoko Diaper M12','mamypoko-diaper-m12','UM-BBY-DIA','8864003616444',13,30,21,6,'goods',480.00,540.00,550.00,'fixed',0.00,5.00,40.000,15.000,0.800,'Baby diaper M size (12 pc)','MamyPoko pants extra dry, size M 12 pcs.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00'),
(43,'Cerelac Rice','cerelac-rice','UM-BBY-CRE','8864003616555',13,29,6,6,'goods',360.00,420.00,440.00,'fixed',0.00,5.00,30.000,10.000,0.200,'Baby cereal 200g','Cerelac rice baby cereal 8+ months.','grocery-1232944-1280-jpg.jpg',0,'active','2026-08-01 10:06:00','2026-08-01 10:06:00');

-- ---------- product images ----------
CREATE TABLE IF NOT EXISTS `core_grocery_product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_product_images_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_product_images`
(`id`,`product_id`,`image`,`is_primary`,`created_at`) VALUES
(1,1,'grocery-1232944-1280-jpg.jpg',1,'2026-08-01 10:10:00'),
(2,3,'onion-jpg.jpg',1,'2026-08-01 10:10:00'),
(3,5,'rice-webp.webp',1,'2026-08-01 10:10:00'),
(4,6,'rice-webp.webp',1,'2026-08-01 10:10:00'),
(5,30,'biscuit-jpg.jpg',1,'2026-08-01 10:10:00');

-- ---------- suppliers ----------
CREATE TABLE IF NOT EXISTS `core_grocery_suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `company` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_suppliers_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_suppliers`
(`id`,`name`,`company`,`phone`,`email`,`address`,`city`,`opening_balance`,`status`,`created_at`,`updated_at`) VALUES
(1,'Kashem Traders','Shwapno / PRAN-RFL Group','01711-111111','kashem@pran.com','Kawran Bazar, Dhaka','Dhaka',0.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(2,'ACI Sales Office','ACI Limited','01722-222222','sales@aci-bd.com','ACI Centre, Motijheel, Dhaka','Dhaka',1000.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(3,'Meghna Distribution','Meghna Group','01733-333333','meghna@meghnagroup.com','Chittagong EPZ, Chittagong','Chittagong',0.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(4,'City Foods Ltd','City Group','01744-444444','city@citygroupltd.com','PG Head Office, Dhaka','Dhaka',2500.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(5,'Bashundhara Foods','Bashundhara Group','01755-555555','food@bashundhara.com','Bashundhara Complex, Dhaka','Dhaka',0.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(6,'Unilever BD Dealer','Unilever Bangladesh Ltd','01766-666666','dealer@unileverbd.com','Unilever House, Gulshan, Dhaka','Dhaka',500.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(7,'Akij Distributor','Akij Food & Beverage Ltd','01777-777777','akij@akij.net','Akij Tower, Motijheel, Dhaka','Dhaka',0.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00'),
(8,'Nestle Channel Partner','Nestle Bangladesh Ltd','01788-888888','partner@nestle-bd.com','Birulia, Savar, Dhaka','Savar',0.00,1,'2026-08-10 10:00:00','2026-08-10 10:00:00');

-- ---------- customers ----------
CREATE TABLE IF NOT EXISTS `core_grocery_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_customers_phone` (`phone`),
  KEY `idx_grocery_customers_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_customers`
(`id`,`name`,`phone`,`email`,`address`,`city`,`district`,`opening_balance`,`status`,`created_at`,`updated_at`) VALUES
(1,'Rahim Uddin','01811-000001','rahim@gmail.com','12/1 Dhanmondi, Dhaka','Dhaka','Dhaka',0.00,1,'2026-08-15 10:00:00','2026-08-15 10:00:00'),
(2,'Karim Mia','01811-000002','karim.mia@gmail.com','House 45, Uttara Sector 7','Uttara','Dhaka',200.00,1,'2026-08-15 10:00:00','2026-08-15 10:00:00'),
(3,'Fatema Begum','01811-000003','fatema.b@gmail.com','Mohakhali DOHS, Road 6','Dhaka','Dhaka',0.00,1,'2026-08-15 10:00:00','2026-08-15 10:00:00'),
(4,'Hasan Ali','01811-000004','hasan.ali@gmail.com','Flat 3B, Badda, Dhaka','Dhaka','Dhaka',0.00,1,'2026-08-15 10:00:00','2026-08-15 10:00:00'),
(5,'Nasrin Sultana','01811-000005','nasrin@gmail.com','Shakespeare Sarani, Bashundhara','Dhaka','Dhaka',150.00,1,'2026-08-15 10:00:00','2026-08-15 10:00:00'),
(6,'Ayesha Siddika','01811-000006','ayesha@yahoo.com','Mirpur 10, Dhaka','Dhaka','Dhaka',0.00,1,'2026-08-16 10:00:00','2026-08-16 10:00:00'),
(7,'Jamal Hossain','01811-000007','jamal@gmail.com','OLD Airport Road, Tejgaon','Dhaka','Dhaka',0.00,1,'2026-08-16 10:00:00','2026-08-16 10:00:00'),
(8,'Tania Akter','01811-000008','tania.akter@gmail.com','Rampura Bridge, Dhaka','Dhaka','Dhaka',0.00,1,'2026-08-16 10:00:00','2026-08-16 10:00:00');

-- ---------- customer addresses ----------
CREATE TABLE IF NOT EXISTS `core_grocery_customer_addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `address_type` enum('home','office','other') DEFAULT 'home',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_customer_addresses_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_customer_addresses`
(`id`,`customer_id`,`address_type`,`address`,`city`,`district`,`phone`,`is_default`,`created_at`) VALUES
(1,1,'home','12/1 Dhanmondi, Dhaka','Dhaka','Dhaka','01811-000001',1,'2026-08-15 10:00:00'),
(2,2,'home','House 45, Uttara Sector 7','Uttara','Dhaka','01811-000002',1,'2026-08-15 10:00:00'),
(3,3,'office','Mohakhali DOHS, Road 6','Dhaka','Dhaka','01811-000003',1,'2026-08-15 10:00:00'),
(4,5,'home','Shakespeare Sarani, Bashundhara','Dhaka','Dhaka','01811-000005',1,'2026-08-15 10:00:00');

-- ============================================================
-- INVENTORY
-- ============================================================

-- ---------- stocks ----------
CREATE TABLE IF NOT EXISTS `core_grocery_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `available_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `total_purchased` decimal(14,3) NOT NULL DEFAULT 0.000,
  `total_sold` decimal(14,3) NOT NULL DEFAULT 0.000,
  `last_movement_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_stocks_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_stocks`
(`id`,`product_id`,`quantity`,`available_quantity`,`total_purchased`,`total_sold`,`last_movement_at`,`created_at`,`updated_at`) VALUES
(1,1,200.000,200.000,200.000,13.000,'2026-09-06 11:10:00','2026-08-20 09:00:00','2026-09-06 11:10:00'),
(2,2,120.000,120.000,0.000,7.000,'2026-09-06 11:05:00','2026-08-20 09:00:00','2026-09-06 11:05:00'),
(3,3,300.000,300.000,150.000,3.000,'2026-09-02 10:00:00','2026-08-20 09:00:00','2026-09-02 10:00:00'),
(4,4,80.000,80.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(5,5,500.000,500.000,100.000,10.000,'2026-09-02 14:00:00','2026-08-20 09:00:00','2026-09-02 14:00:00'),
(6,6,400.000,400.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(7,7,250.000,250.000,50.000,0.000,'2026-08-28 10:00:00','2026-08-20 09:00:00','2026-08-28 10:00:00'),
(8,8,150.000,150.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(9,9,300.000,300.000,80.000,0.000,'2026-09-01 10:00:00','2026-08-20 09:00:00','2026-09-01 10:00:00'),
(10,10,350.000,350.000,100.000,0.000,'2026-09-01 10:00:00','2026-08-20 09:00:00','2026-09-01 10:00:00'),
(11,11,400.000,400.000,150.000,0.000,'2026-09-01 10:00:00','2026-08-20 09:00:00','2026-09-01 10:00:00'),
(12,12,180.000,180.000,60.000,5.000,'2026-09-02 14:00:00','2026-08-20 09:00:00','2026-09-02 14:00:00'),
(13,13,100.000,100.000,40.000,0.000,'2026-08-30 10:00:00','2026-08-20 09:00:00','2026-08-30 10:00:00'),
(14,14,90.000,90.000,40.000,1.000,'2026-09-03 12:00:00','2026-08-20 09:00:00','2026-09-03 12:00:00'),
(15,15,80.000,80.000,30.000,1.000,'2026-09-03 12:00:00','2026-08-20 09:00:00','2026-09-03 12:00:00'),
(16,16,60.000,60.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(17,17,250.000,250.000,0.000,10.000,'2026-09-05 15:00:00','2026-08-20 09:00:00','2026-09-05 15:00:00'),
(18,18,100.000,100.000,50.000,5.000,'2026-09-06 11:20:00','2026-08-20 09:00:00','2026-09-06 11:20:00'),
(19,19,40.000,40.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(20,20,35.000,35.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(21,21,50.000,50.000,0.000,2.000,'2026-09-03 12:00:00','2026-08-20 09:00:00','2026-09-03 12:00:00'),
(22,22,70.000,70.000,0.000,0.000,'2026-09-05 11:30:00','2026-08-20 09:00:00','2026-09-05 11:30:00'),
(23,23,60.000,60.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(24,24,20.000,20.000,0.000,1.000,'2026-09-06 11:00:00','2026-08-20 09:00:00','2026-09-06 11:00:00'),
(25,25,120.000,120.000,0.000,12.000,'2026-09-06 09:30:00','2026-08-20 09:00:00','2026-09-06 09:30:00'),
(26,26,150.000,150.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(27,27,200.000,200.000,0.000,10.000,'2026-09-06 09:30:00','2026-08-20 09:00:00','2026-09-06 09:30:00'),
(28,28,45.000,45.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(29,29,30.000,30.000,20.000,1.000,'2026-09-03 10:00:00','2026-08-20 09:00:00','2026-09-03 10:00:00'),
(30,30,200.000,200.000,0.000,6.000,'2026-09-05 15:00:00','2026-08-20 09:00:00','2026-09-05 15:00:00'),
(31,31,60.000,60.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(32,32,250.000,250.000,0.000,20.000,'2026-09-06 09:30:00','2026-08-20 09:00:00','2026-09-06 09:30:00'),
(33,33,80.000,80.000,0.000,4.000,'2026-09-05 15:00:00','2026-08-20 09:00:00','2026-09-05 15:00:00'),
(34,34,75.000,75.000,0.000,4.000,'2026-09-06 11:00:00','2026-08-20 09:00:00','2026-09-06 11:00:00'),
(35,35,0.000,0.000,0.000,2.000,'2026-09-06 11:00:00','2026-08-20 09:00:00','2026-09-06 11:00:00'),
(36,36,90.000,90.000,40.000,3.000,'2026-09-04 13:00:00','2026-08-20 09:00:00','2026-09-04 13:00:00'),
(37,37,120.000,120.000,60.000,5.000,'2026-09-04 13:00:00','2026-08-20 09:00:00','2026-09-04 13:00:00'),
(38,38,85.000,85.000,30.000,0.000,'2026-09-04 13:00:00','2026-08-20 09:00:00','2026-09-04 13:00:00'),
(39,39,70.000,70.000,0.000,2.000,'2026-09-04 13:00:00','2026-08-20 09:00:00','2026-09-04 13:00:00'),
(40,40,65.000,65.000,25.000,0.000,'2026-09-04 13:00:00','2026-08-20 09:00:00','2026-09-04 13:00:00'),
(41,41,60.000,60.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(42,42,40.000,40.000,0.000,0.000,NULL,'2026-08-20 09:00:00','2026-08-20 09:00:00'),
(43,43,30.000,30.000,15.000,0.000,'2026-09-03 10:00:00','2026-08-20 09:00:00','2026-09-03 10:00:00');

-- ---------- stock movements ----------
CREATE TABLE IF NOT EXISTS `core_grocery_stock_movements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `movement_type` enum('opening','purchase_in','sale_out','return_in','adjustment_in','adjustment_out','damage','expired','manual_in','manual_out') NOT NULL DEFAULT 'manual_in',
  `quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `balance_after` decimal(12,3) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_stock_movements_product` (`product_id`),
  KEY `idx_grocery_stock_movements_type` (`movement_type`),
  KEY `idx_grocery_stock_movements_created` (`created_at`),
  KEY `idx_grocery_stock_movements_ref` (`reference_type`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_stock_movements`
(`id`,`product_id`,`movement_type`,`quantity`,`balance_after`,`reference_type`,`reference_id`,`remark`,`created_by`,`created_at`) VALUES
(1,1,'opening',13.000,13.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(2,1,'sale_out',-5.000,8.000,'sale',1,'Sale INV-2026-0001',1,'2026-09-01 10:00:00'),
(3,1,'purchase_in',200.000,208.000,'purchase',4,'Purchase PR-2026-0004',1,'2026-09-02 09:00:00'),
(4,1,'sale_out',-8.000,200.000,'sale',8,'Sale INV-2026-0008',1,'2026-09-06 11:10:00'),
(5,2,'opening',127.000,127.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(6,2,'sale_out',-2.000,125.000,'sale',1,'Sale INV-2026-0001',1,'2026-09-01 10:00:00'),
(7,2,'sale_out',-5.000,120.000,'sale',8,'Sale INV-2026-0008',1,'2026-09-06 11:05:00'),
(8,3,'opening',153.000,153.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(9,3,'sale_out',-3.000,150.000,'sale',1,'Sale INV-2026-0001',1,'2026-09-01 10:00:00'),
(10,3,'purchase_in',150.000,300.000,'purchase',4,'Purchase PR-2026-0004',1,'2026-09-02 10:00:00'),
(11,4,'opening',80.000,80.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(12,5,'opening',410.000,410.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(13,5,'purchase_in',100.000,510.000,'purchase',1,'Purchase PR-2026-0001',1,'2026-08-28 10:00:00'),
(14,5,'sale_out',-10.000,500.000,'sale',2,'Sale INV-2026-0002',1,'2026-09-02 14:00:00'),
(15,6,'opening',400.000,400.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(16,7,'opening',200.000,200.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(17,7,'purchase_in',50.000,250.000,'purchase',1,'Purchase PR-2026-0001',1,'2026-08-28 10:00:00'),
(18,8,'opening',150.000,150.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(19,9,'opening',220.000,220.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(20,9,'purchase_in',80.000,300.000,'purchase',3,'Purchase PR-2026-0003',1,'2026-09-01 10:00:00'),
(21,10,'opening',250.000,250.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(22,10,'purchase_in',100.000,350.000,'purchase',3,'Purchase PR-2026-0003',1,'2026-09-01 10:00:00'),
(23,11,'opening',250.000,250.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(24,11,'purchase_in',150.000,400.000,'purchase',3,'Purchase PR-2026-0003',1,'2026-09-01 10:00:00'),
(25,12,'opening',125.000,125.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(26,12,'purchase_in',60.000,185.000,'purchase',1,'Purchase PR-2026-0001',1,'2026-08-28 10:00:00'),
(27,12,'sale_out',-5.000,180.000,'sale',2,'Sale INV-2026-0002',1,'2026-09-02 14:00:00'),
(28,13,'opening',60.000,60.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(29,13,'purchase_in',40.000,100.000,'purchase',2,'Purchase PR-2026-0002',1,'2026-08-30 10:00:00'),
(30,14,'opening',51.000,51.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(31,14,'purchase_in',40.000,91.000,'purchase',2,'Purchase PR-2026-0002',1,'2026-08-30 10:00:00'),
(32,14,'sale_out',-1.000,90.000,'sale',3,'Sale INV-2026-0003',1,'2026-09-03 12:00:00'),
(33,15,'opening',51.000,51.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(34,15,'purchase_in',30.000,81.000,'purchase',2,'Purchase PR-2026-0002',1,'2026-08-30 10:00:00'),
(35,15,'sale_out',-1.000,80.000,'sale',3,'Sale INV-2026-0003',1,'2026-09-03 12:00:00'),
(36,16,'opening',60.000,60.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(37,17,'opening',260.000,260.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(38,17,'sale_out',-10.000,250.000,'sale',5,'Sale INV-2026-0005',1,'2026-09-05 15:00:00'),
(39,18,'opening',55.000,55.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(40,18,'purchase_in',50.000,105.000,'purchase',4,'Purchase PR-2026-0004',1,'2026-09-02 09:00:00'),
(41,18,'sale_out',-2.000,103.000,'sale',2,'Sale INV-2026-0002',1,'2026-09-02 14:00:00'),
(42,18,'sale_out',-3.000,100.000,'sale',8,'Sale INV-2026-0008',1,'2026-09-06 11:20:00'),
(43,19,'opening',40.000,40.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(44,20,'opening',35.000,35.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(45,21,'opening',52.000,52.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(46,21,'sale_out',-2.000,50.000,'sale',3,'Sale INV-2026-0003',1,'2026-09-03 12:00:00'),
(47,22,'opening',72.000,72.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(48,22,'damage',-2.000,70.000,'adjustment',1,'Damage - ADJ-2026-0001',1,'2026-09-05 11:30:00'),
(49,23,'opening',60.000,60.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(50,24,'opening',21.000,21.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(51,24,'sale_out',-1.000,20.000,'sale',7,'Sale INV-2026-0007',1,'2026-09-06 11:00:00'),
(52,25,'opening',132.000,132.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(53,25,'sale_out',-12.000,120.000,'sale',6,'Sale INV-2026-0006',1,'2026-09-06 09:30:00'),
(54,26,'opening',150.000,150.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(55,27,'opening',210.000,210.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(56,27,'sale_out',-10.000,200.000,'sale',6,'Sale INV-2026-0006',1,'2026-09-06 09:30:00'),
(57,28,'opening',45.000,45.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(58,29,'opening',11.000,11.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(59,29,'purchase_in',20.000,31.000,'purchase',5,'Purchase PR-2026-0005',1,'2026-09-03 10:00:00'),
(60,29,'sale_out',-1.000,30.000,'sale',8,'Sale INV-2026-0008',1,'2026-09-06 11:15:00'),
(61,30,'opening',206.000,206.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(62,30,'sale_out',-6.000,200.000,'sale',5,'Sale INV-2026-0005',1,'2026-09-05 15:00:00'),
(63,31,'opening',60.000,60.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(64,32,'opening',270.000,270.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(65,32,'sale_out',-20.000,250.000,'sale',6,'Sale INV-2026-0006',1,'2026-09-06 09:30:00'),
(66,33,'opening',84.000,84.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(67,33,'sale_out',-4.000,80.000,'sale',5,'Sale INV-2026-0005',1,'2026-09-05 15:00:00'),
(68,34,'opening',79.000,79.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(69,34,'sale_out',-4.000,75.000,'sale',7,'Sale INV-2026-0007',1,'2026-09-06 11:00:00'),
(70,35,'opening',2.000,2.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(71,35,'sale_out',-2.000,0.000,'sale',7,'Sale INV-2026-0007',1,'2026-09-06 11:00:00'),
(72,36,'opening',53.000,53.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(73,36,'purchase_in',40.000,93.000,'purchase',6,'Purchase PR-2026-0006',1,'2026-09-04 13:00:00'),
(74,36,'sale_out',-3.000,90.000,'sale',4,'Sale INV-2026-0004',1,'2026-09-04 13:00:00'),
(75,37,'opening',65.000,65.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(76,37,'purchase_in',60.000,125.000,'purchase',6,'Purchase PR-2026-0006',1,'2026-09-04 13:00:00'),
(77,37,'sale_out',-5.000,120.000,'sale',4,'Sale INV-2026-0004',1,'2026-09-04 13:00:00'),
(78,38,'opening',55.000,55.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(79,38,'purchase_in',30.000,85.000,'purchase',6,'Purchase PR-2026-0006',1,'2026-09-04 13:00:00'),
(80,39,'opening',72.000,72.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(81,39,'sale_out',-2.000,70.000,'sale',4,'Sale INV-2026-0004',1,'2026-09-04 13:00:00'),
(82,40,'opening',40.000,40.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(83,40,'purchase_in',25.000,65.000,'purchase',6,'Purchase PR-2026-0006',1,'2026-09-04 13:00:00'),
(84,41,'opening',60.000,60.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(85,42,'opening',40.000,40.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(86,43,'opening',15.000,15.000,'opening',NULL,'Opening stock',1,'2026-08-20 09:00:00'),
(87,43,'purchase_in',15.000,30.000,'purchase',5,'Purchase PR-2026-0005',1,'2026-09-03 10:00:00');

-- ---------- stock adjustments ----------
CREATE TABLE IF NOT EXISTS `core_grocery_stock_adjustments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_no` varchar(30) NOT NULL,
  `adjustment_type` enum('in','out') NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_stock_adjustments_no` (`adjustment_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `core_grocery_stock_adjustment_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `previous_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `adjusted_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `new_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_stock_adjustment_details_adj` (`adjustment_id`),
  KEY `idx_grocery_stock_adjustment_details_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_stock_adjustments`
(`id`,`adjustment_no`,`adjustment_type`,`reason`,`remark`,`created_by`,`status`,`created_at`,`updated_at`) VALUES
(1,'ADJ-2026-0001','out','Damage','2 kg chicken damaged during handling',1,'approved','2026-09-05 11:30:00','2026-09-05 11:30:00');

INSERT IGNORE INTO `core_grocery_stock_adjustment_details`
(`id`,`adjustment_id`,`product_id`,`previous_quantity`,`adjusted_quantity`,`new_quantity`,`remark`) VALUES
(1,1,22,72.000,70.000,70.000,'Damaged during handling');

-- ============================================================
-- PURCHASE
-- ============================================================

-- ---------- payment methods ----------
CREATE TABLE IF NOT EXISTS `core_grocery_payment_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_payment_methods_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_payment_methods`
(`id`,`name`,`slug`,`status`,`created_at`,`updated_at`) VALUES
(1,'Cash','cash',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'Card','card',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'Bkash','bkash',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,'Nagad','nagad',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,'Rocket','rocket',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,'Bank Transfer','bank-transfer',1,'2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,'Due / Credit','due-credit',1,'2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ---------- purchases ----------
CREATE TABLE IF NOT EXISTS `core_grocery_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_no` varchar(30) NOT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `status` enum('pending','received','cancelled') DEFAULT 'pending',
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_purchases_no` (`purchase_no`),
  KEY `idx_grocery_purchases_supplier` (`supplier_id`),
  KEY `idx_grocery_purchases_date` (`purchase_date`),
  KEY `idx_grocery_purchases_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `core_grocery_purchase_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_purchase_details_purchase` (`purchase_id`),
  KEY `idx_grocery_purchase_details_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_purchases`
(`id`,`purchase_no`,`supplier_id`,`purchase_date`,`discount`,`tax`,`subtotal`,`grand_total`,`paid_amount`,`due_amount`,`payment_method_id`,`status`,`remark`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'PR-2026-0001',1,'2026-08-28',0.00,0.00,22500.00,22500.00,22500.00,0.00,1,'received','Monthly grocery restock',1,'2026-08-28 10:00:00','2026-08-28 10:00:00'),
(2,'PR-2026-0002',2,'2026-08-30',100.00,0.00,16100.00,16000.00,15000.00,1000.00,3,'received',NULL,1,'2026-08-30 10:00:00','2026-08-30 10:00:00'),
(3,'PR-2026-0003',5,'2026-09-01',0.00,0.00,18460.00,18460.00,18460.00,0.00,1,'received',NULL,1,'2026-09-01 10:00:00','2026-09-01 10:00:00'),
(4,'PR-2026-0004',3,'2026-09-02',0.00,0.00,18400.00,18400.00,18400.00,0.00,2,'received',NULL,1,'2026-09-02 09:00:00','2026-09-02 09:00:00'),
(5,'PR-2026-0005',8,'2026-09-03',0.00,0.00,15400.00,15400.00,15400.00,0.00,3,'received',NULL,1,'2026-09-03 10:00:00','2026-09-03 10:00:00'),
(6,'PR-2026-0006',6,'2026-09-04',0.00,0.00,17250.00,17250.00,11250.00,6000.00,1,'received','Partial payment',1,'2026-09-04 13:00:00','2026-09-04 13:00:00');

INSERT IGNORE INTO `core_grocery_purchase_details`
(`id`,`purchase_id`,`product_id`,`quantity`,`purchase_price`,`discount`,`vat`,`subtotal`) VALUES
(1,1,5,100.000,78.00,0.00,0.00,7800.00),
(2,1,7,50.000,120.00,0.00,0.00,6000.00),
(3,1,12,60.000,145.00,0.00,0.00,8700.00),
(4,2,14,40.000,90.00,0.00,0.00,3600.00),
(5,2,15,30.000,110.00,0.00,0.00,3300.00),
(6,2,13,40.000,230.00,0.00,0.00,9200.00),
(7,3,9,80.000,52.00,0.00,0.00,4160.00),
(8,3,10,100.000,110.00,0.00,0.00,11000.00),
(9,3,11,150.000,22.00,0.00,0.00,3300.00),
(10,4,18,50.000,130.00,0.00,0.00,6500.00),
(11,4,1,200.000,28.00,0.00,0.00,5600.00),
(12,4,3,150.000,42.00,0.00,0.00,6300.00),
(13,5,29,20.000,500.00,0.00,0.00,10000.00),
(14,5,43,15.000,360.00,0.00,0.00,5400.00),
(15,6,36,40.000,120.00,0.00,0.00,4800.00),
(16,6,37,60.000,55.00,0.00,0.00,3300.00),
(17,6,38,30.000,130.00,0.00,0.00,3900.00),
(18,6,40,25.000,210.00,0.00,0.00,5250.00);

-- ============================================================
-- SALES / POS
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'paid',
  `status` enum('completed','cancelled','returned') DEFAULT 'completed',
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_sales_invoice` (`invoice_no`),
  KEY `idx_grocery_sales_customer` (`customer_id`),
  KEY `idx_grocery_sales_date` (`sale_date`),
  KEY `idx_grocery_sales_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `core_grocery_sale_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cost_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_sale_details_sale` (`sale_id`),
  KEY `idx_grocery_sale_details_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_sales`
(`id`,`invoice_no`,`customer_id`,`sale_date`,`discount`,`tax`,`subtotal`,`grand_total`,`paid_amount`,`due_amount`,`payment_method_id`,`payment_status`,`status`,`remark`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'INV-2026-0001',1,'2026-09-01',0.00,0.00,440.00,440.00,440.00,0.00,1,'paid','completed',NULL,1,'2026-09-01 10:00:00','2026-09-01 10:00:00'),
(2,'INV-2026-0002',2,'2026-09-02',0.00,0.00,2025.00,2025.00,2025.00,0.00,2,'paid','completed',NULL,1,'2026-09-02 14:00:00','2026-09-02 14:00:00'),
(3,'INV-2026-0003',3,'2026-09-03',0.00,0.00,1805.00,1805.00,1500.00,305.00,3,'partial','completed',NULL,1,'2026-09-03 12:00:00','2026-09-03 12:00:00'),
(4,'INV-2026-0004',4,'2026-09-04',0.00,0.00,1075.00,1075.00,1075.00,0.00,1,'paid','completed',NULL,1,'2026-09-04 13:00:00','2026-09-04 13:00:00'),
(5,'INV-2026-0005',5,'2026-09-05',0.00,0.00,1750.00,1750.00,1750.00,0.00,2,'paid','completed',NULL,1,'2026-09-05 15:00:00','2026-09-05 15:00:00'),
(6,'INV-2026-0006',6,'2026-09-06',0.00,0.00,1750.00,1750.00,1750.00,0.00,3,'paid','completed','Morning POS sale',1,'2026-09-06 09:30:00','2026-09-06 09:30:00'),
(7,'INV-2026-0007',7,'2026-09-06',0.00,0.00,1980.00,1980.00,1500.00,480.00,1,'partial','completed',NULL,1,'2026-09-06 11:00:00','2026-09-06 11:00:00'),
(8,'INV-2026-0008',8,'2026-09-06',0.00,0.00,1560.00,1560.00,1560.00,0.00,2,'paid','completed',NULL,1,'2026-09-06 11:05:00','2026-09-06 11:05:00');

INSERT IGNORE INTO `core_grocery_sale_details`
(`id`,`sale_id`,`product_id`,`quantity`,`selling_price`,`discount`,`vat`,`cost_price`,`subtotal`) VALUES
(1,1,1,5.000,35.00,0.00,0.00,28.00,175.00),
(2,1,3,3.000,55.00,0.00,0.00,42.00,165.00),
(3,1,2,2.000,50.00,0.00,0.00,35.00,100.00),
(4,2,5,10.000,90.00,0.00,0.00,78.00,900.00),
(5,2,18,2.000,150.00,0.00,0.00,130.00,300.00),
(6,2,12,5.000,165.00,0.00,0.00,145.00,825.00),
(7,3,21,2.000,780.00,0.00,0.00,700.00,1560.00),
(8,3,14,1.000,110.00,0.00,0.00,90.00,110.00),
(9,3,15,1.000,135.00,0.00,0.00,110.00,135.00),
(10,4,36,3.000,145.00,0.00,0.00,120.00,435.00),
(11,4,37,5.000,70.00,0.00,0.00,55.00,350.00),
(12,4,39,2.000,145.00,0.00,0.00,120.00,290.00),
(13,5,17,10.000,105.00,0.00,0.00,90.00,1050.00),
(14,5,33,4.000,70.00,0.00,0.00,55.00,280.00),
(15,5,30,6.000,70.00,0.00,0.00,55.00,420.00),
(16,6,25,12.000,75.00,0.00,0.00,60.00,900.00),
(17,6,32,20.000,25.00,0.00,0.00,18.00,500.00),
(18,6,27,10.000,35.00,0.00,0.00,25.00,350.00),
(19,7,24,1.000,1100.00,0.00,0.00,900.00,1100.00),
(20,7,35,2.000,220.00,0.00,0.00,180.00,440.00),
(21,7,34,4.000,110.00,0.00,0.00,90.00,440.00),
(22,8,1,8.000,35.00,0.00,0.00,28.00,280.00),
(23,8,2,5.000,50.00,0.00,0.00,35.00,250.00),
(24,8,18,3.000,150.00,0.00,0.00,130.00,450.00),
(25,8,29,1.000,580.00,0.00,0.00,500.00,580.00);

-- ============================================================
-- ORDERS
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(30) NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `customer_address_id` int(10) unsigned DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `delivery_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `order_status` enum('pending','confirmed','processing','packed','shipped','delivered','cancelled') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_orders_no` (`order_no`),
  KEY `idx_grocery_orders_customer` (`customer_id`),
  KEY `idx_grocery_orders_status` (`order_status`),
  KEY `idx_grocery_orders_date` (`order_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `core_grocery_order_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_grocery_order_details_order` (`order_id`),
  KEY `idx_grocery_order_details_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_orders`
(`id`,`order_no`,`customer_id`,`customer_address_id`,`order_date`,`delivery_charge`,`discount`,`tax`,`subtotal`,`grand_total`,`paid_amount`,`due_amount`,`payment_status`,`payment_method_id`,`order_status`,`delivery_address`,`delivery_date`,`remark`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'ORD-2026-0001',3,3,'2026-09-04 11:00:00',0.00,0.00,0.00,600.00,600.00,600.00,0.00,'paid',1,'delivered','Mohakhali DOHS, Road 6, Dhaka','2026-09-05 12:00:00','Urgent delivery',1,'2026-09-04 11:00:00','2026-09-05 12:00:00'),
(2,'ORD-2026-0002',5,4,'2026-09-05 10:00:00',60.00,0.00,0.00,1180.00,1240.00,1240.00,0.00,'paid',2,'shipped','Shakespeare Sarani, Bashundhara, Dhaka','2026-09-06 12:00:00',NULL,1,'2026-09-05 10:00:00','2026-09-06 09:00:00'),
(3,'ORD-2026-0003',8,NULL,'2026-09-06 09:00:00',40.00,0.00,0.00,645.00,685.00,0.00,685.00,'unpaid',NULL,'pending','Rampura Bridge, Dhaka',NULL,'Payment on delivery',1,'2026-09-06 09:00:00','2026-09-06 09:00:00'),
(4,'ORD-2026-0004',2,2,'2026-09-06 10:30:00',40.00,0.00,0.00,910.00,950.00,500.00,450.00,'partial',3,'processing','House 45, Uttara Sector 7','2026-09-07 12:00:00',NULL,1,'2026-09-06 10:30:00','2026-09-06 10:30:00');

INSERT IGNORE INTO `core_grocery_order_details`
(`id`,`order_id`,`product_id`,`quantity`,`price`,`discount`,`vat`,`subtotal`) VALUES
(1,1,5,5.000,90.00,0.00,0.00,450.00),
(2,1,18,1.000,150.00,0.00,0.00,150.00),
(3,2,17,6.000,105.00,0.00,0.00,630.00),
(4,2,33,2.000,70.00,0.00,0.00,140.00),
(5,2,19,1.000,260.00,0.00,0.00,260.00),
(6,2,20,1.000,150.00,0.00,0.00,150.00),
(7,3,9,4.000,65.00,0.00,0.00,260.00),
(8,3,10,2.000,125.00,0.00,0.00,250.00),
(9,3,32,5.000,25.00,0.00,0.00,125.00),
(10,4,12,2.000,165.00,0.00,0.00,330.00),
(11,4,29,1.000,580.00,0.00,0.00,580.00);

-- ============================================================
-- DELIVERY
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_delivery_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `slug` varchar(30) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_delivery_statuses_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_delivery_statuses`
(`id`,`name`,`slug`,`sort_order`,`created_at`) VALUES
(1,'Pending','pending',1,'2026-08-01 10:00:00'),
(2,'Assigned','assigned',2,'2026-08-01 10:00:00'),
(3,'Picked Up','picked-up',3,'2026-08-01 10:00:00'),
(4,'In Transit','in-transit',4,'2026-08-01 10:00:00'),
(5,'Delivered','delivered',5,'2026-08-01 10:00:00'),
(6,'Failed','failed',6,'2026-08-01 10:00:00'),
(7,'Cancelled','cancelled',7,'2026-08-01 10:00:00');

CREATE TABLE IF NOT EXISTS `core_grocery_deliveries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_no` varchar(30) NOT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `delivery_status_id` int(10) unsigned DEFAULT NULL,
  `rider_name` varchar(100) DEFAULT NULL,
  `rider_phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_deliveries_no` (`delivery_no`),
  KEY `idx_grocery_deliveries_order` (`order_id`),
  KEY `idx_grocery_deliveries_status` (`delivery_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_deliveries`
(`id`,`delivery_no`,`order_id`,`delivery_date`,`delivery_status_id`,`rider_name`,`rider_phone`,`address`,`remark`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'DLV-2026-0001',1,'2026-09-05 12:00:00',5,'Mamun Islam','01720-111111','Mohakhali DOHS, Road 6, Dhaka','Delivered on time',1,'2026-09-04 11:30:00','2026-09-05 12:00:00'),
(2,'DLV-2026-0002',2,'2026-09-06 12:00:00',4,'Rashed Karim','01720-222222','Shakespeare Sarani, Bashundhara, Dhaka','In transit to customer',1,'2026-09-05 10:30:00','2026-09-06 09:00:00'),
(3,'DLV-2026-0003',4,NULL,1,NULL,NULL,'House 45, Uttara Sector 7, Dhaka','Delivery pending assignment',1,'2026-09-06 10:45:00','2026-09-06 10:45:00');

-- ============================================================
-- PAYMENTS
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(30) NOT NULL,
  `payable_type` enum('sale','purchase','order') NOT NULL DEFAULT 'sale',
  `payable_id` int(11) NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_payments_no` (`payment_no`),
  KEY `idx_grocery_payments_payable` (`payable_type`,`payable_id`),
  KEY `idx_grocery_payments_customer` (`customer_id`),
  KEY `idx_grocery_payments_supplier` (`supplier_id`),
  KEY `idx_grocery_payments_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_payments`
(`id`,`payment_no`,`payable_type`,`payable_id`,`customer_id`,`supplier_id`,`amount`,`payment_method_id`,`transaction_id`,`payment_date`,`remark`,`created_by`,`created_at`) VALUES
(1,'PMT-2026-0001','sale',1,1,NULL,440.00,1,NULL,'2026-09-01 10:00:00','Sale INV-2026-0001',1,'2026-09-01 10:00:00'),
(2,'PMT-2026-0002','sale',2,2,NULL,2025.00,2,NULL,'2026-09-02 14:00:00','Sale INV-2026-0002',1,'2026-09-02 14:00:00'),
(3,'PMT-2026-0003','sale',3,3,NULL,1500.00,3,'BK123456','2026-09-03 12:00:00','Partial payment INV-2026-0003',1,'2026-09-03 12:00:00'),
(4,'PMT-2026-0004','sale',4,4,NULL,1075.00,1,NULL,'2026-09-04 13:00:00','Sale INV-2026-0004',1,'2026-09-04 13:00:00'),
(5,'PMT-2026-0005','sale',5,5,NULL,1750.00,2,NULL,'2026-09-05 15:00:00','Sale INV-2026-0005',1,'2026-09-05 15:00:00'),
(6,'PMT-2026-0006','sale',6,6,NULL,1750.00,3,'BK654321','2026-09-06 09:30:00','Sale INV-2026-0006',1,'2026-09-06 09:30:00'),
(7,'PMT-2026-0007','sale',7,7,NULL,1500.00,1,NULL,'2026-09-06 11:00:00','Partial payment INV-2026-0007',1,'2026-09-06 11:00:00'),
(8,'PMT-2026-0008','sale',8,8,NULL,1560.00,2,'CARD88888','2026-09-06 11:05:00','Sale INV-2026-0008',1,'2026-09-06 11:05:00'),
(9,'PMT-2026-0009','purchase',1,NULL,1,22500.00,1,NULL,'2026-08-28 10:00:00','Purchase PR-2026-0001',1,'2026-08-28 10:00:00'),
(10,'PMT-2026-0010','purchase',2,NULL,2,15000.00,3,'BK99999','2026-08-30 10:00:00','Purchase PR-2026-0002',1,'2026-08-30 10:00:00'),
(11,'PMT-2026-0011','purchase',3,NULL,5,18460.00,1,NULL,'2026-09-01 10:00:00','Purchase PR-2026-0003',1,'2026-09-01 10:00:00'),
(12,'PMT-2026-0012','purchase',4,NULL,3,18400.00,2,NULL,'2026-09-02 09:00:00','Purchase PR-2026-0004',1,'2026-09-02 09:00:00'),
(13,'PMT-2026-0013','purchase',5,NULL,8,15400.00,3,'BK55555','2026-09-03 10:00:00','Purchase PR-2026-0005',1,'2026-09-03 10:00:00'),
(14,'PMT-2026-0014','purchase',6,NULL,6,11250.00,1,NULL,'2026-09-04 13:00:00','Partial payment PR-2026-0006',1,'2026-09-04 13:00:00'),
(15,'PMT-2026-0015','order',1,3,NULL,600.00,1,NULL,'2026-09-04 12:00:00','Order ORD-2026-0001',1,'2026-09-04 12:00:00'),
(16,'PMT-2026-0016','order',2,5,NULL,1240.00,2,NULL,'2026-09-05 12:00:00','Order ORD-2026-0002',1,'2026-09-05 12:00:00'),
(17,'PMT-2026-0017','order',4,2,NULL,500.00,3,'BK77777','2026-09-06 10:45:00','Partial payment ORD-2026-0004',1,'2026-09-06 10:45:00');

-- ============================================================
-- DISCOUNTS / OFFERS
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_coupons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `type` enum('fixed','percent') DEFAULT 'percent',
  `value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `usage_limit` int(11) NOT NULL DEFAULT 0,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_coupons`
(`id`,`code`,`type`,`value`,`min_order_amount`,`max_discount`,`starts_at`,`ends_at`,`usage_limit`,`used_count`,`status`,`created_at`,`updated_at`) VALUES
(1,'WELCOME10','percent',10.00,500.00,150.00,'2026-08-01 00:00:00','2026-12-31 23:59:59',100,12,'active','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'SAVE50','fixed',50.00,1000.00,NULL,'2026-08-01 00:00:00','2026-12-31 23:59:59',50,8,'active','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'SUMMER15','percent',15.00,800.00,200.00,'2026-08-01 00:00:00','2026-09-30 23:59:59',80,4,'active','2026-08-01 10:00:00','2026-08-01 10:00:00');

-- ============================================================
-- EXPENSES
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_expense_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_expense_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_expense_categories`
(`id`,`name`,`status`,`created_at`) VALUES
(1,'Rent',1,'2026-08-01 10:00:00'),
(2,'Salary',1,'2026-08-01 10:00:00'),
(3,'Electricity',1,'2026-08-01 10:00:00'),
(4,'Transport',1,'2026-08-01 10:00:00'),
(5,'Marketing',1,'2026-08-01 10:00:00');

CREATE TABLE IF NOT EXISTS `core_grocery_expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expense_no` varchar(30) NOT NULL,
  `expense_category_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expense_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_expenses_no` (`expense_no`),
  KEY `idx_grocery_expenses_category` (`expense_category_id`),
  KEY `idx_grocery_expenses_date` (`expense_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_expenses`
(`id`,`expense_no`,`expense_category_id`,`amount`,`expense_date`,`remark`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'EXP-2026-0001',1,35000.00,'2026-09-01','Shop rent for September',1,'2026-09-01 10:00:00','2026-09-01 10:00:00'),
(2,'EXP-2026-0002',2,45000.00,'2026-09-01','Staff salary for August',1,'2026-09-01 10:00:00','2026-09-01 10:00:00'),
(3,'EXP-2026-0003',3,8500.00,'2026-09-03','Electricity bill',1,'2026-09-03 10:00:00','2026-09-03 10:00:00'),
(4,'EXP-2026-0004',4,3200.00,'2026-09-05','Delivery transport cost',1,'2026-09-05 10:00:00','2026-09-05 10:00:00');

-- ============================================================
-- BUSINESS SETTINGS
-- ============================================================

CREATE TABLE IF NOT EXISTS `core_grocery_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(60) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grocery_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `core_grocery_settings`
(`id`,`setting_key`,`setting_value`,`description`,`created_at`,`updated_at`) VALUES
(1,'shop_name','UrbanMart Grocery','Shop name shown on invoices and reports','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(2,'shop_phone','+880 1700-000000','Customer service phone','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(3,'shop_email','support@urbanmart.com','Customer service email','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(4,'shop_address','House 12, Road 5, Dhanmondi, Dhaka 1205','Shop / warehouse address','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(5,'currency','BDT','Currency code used in the system','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(6,'currency_symbol','Tk','Currency symbol','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(7,'allow_negative_stock','0','Allow negative stock on sale (1=yes, 0=no)','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(8,'invoice_prefix','INV','Prefix for sales invoices','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(9,'sale_prefix','SL','Sale reference prefix','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(10,'order_prefix','ORD','Order number prefix','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(11,'purchase_prefix','PR','Purchase number prefix','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(12,'payment_prefix','PMT','Payment number prefix','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(13,'default_tax_rate','5','Default tax % applied at POS','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(14,'default_payment_method','1','Default payment method id','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(15,'low_stock_threshold','10','Default low stock warning quantity','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(16,'delivery_charge','40','Default home delivery charge','2026-08-01 10:00:00','2026-08-01 10:00:00'),
(17,'free_delivery_min','1000','Free delivery for orders above this amount','2026-08-01 10:00:00','2026-08-01 10:00:00');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;