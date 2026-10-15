DROP TABLE IF EXISTS `test`.`core_courier_invoices`;
CREATE TABLE  `test`.`core_courier_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `remark` varchar(45) NOT NULL,
  `payment_term` varchar(45) NOT NULL,
  `updated_at` datetime NOT NULL,
  `invoice_total` float NOT NULL,
  `paid_total` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `test`.`core_courier_invoices` 
(`sender_id`, `created_at`, `remark`, `payment_term`, `updated_at`, `invoice_total`, `paid_total`)
VALUES
(1, '2025-06-01 10:15:00', 'Monthly invoice', 'NET 30', '2025-06-01 10:15:00', 1500.00, 1500.00),
(2, '2025-06-05 09:30:00', 'Late payment penalty', 'Due on Receipt', '2025-06-06 12:00:00', 300.00, 0.00),
(3, '2025-06-10 14:45:00', 'Regular shipment', 'NET 15', '2025-06-11 08:20:00', 2200.00, 1100.00),
(1, '2025-06-15 16:00:00', 'June invoice', 'NET 30', '2025-06-16 09:10:00', 1800.00, 1800.00),
(4, '2025-06-20 11:25:00', 'Courier charges', 'NET 45', '2025-06-21 13:30:00', 500.00, 250.00);










DROP TABLE IF EXISTS `test`.`core_courier_invoice_details`;
CREATE TABLE  `test`.`core_courier_invoice_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `parcel_id` int(10) unsigned NOT NULL,
  `price` float NOT NULL,
  `qty` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




INSERT INTO `test`.`core_courier_invoice_details` 
(`invoice_id`, `parcel_id`, `price`, `qty`)
VALUES
(1, 1, 100.00, 5),
(1, 2, 200.00, 3),
(2, 3, 150.00, 2),
(3, 4, 250.00, 4),
(3, 5, 180.00, 6),
(4, 6, 300.00, 1),
(5, 5, 90.00, 10),
(5, 4, 120.00, 3);

