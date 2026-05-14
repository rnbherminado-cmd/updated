-- Add-Ons Product Supply Table
CREATE TABLE `product_addons` (
  `addon_id` bigint(20) NOT NULL,
  `store_id` bigint(20) NOT NULL,
  `addon_name` varchar(100) DEFAULT NULL,
  `addon_description` varchar(255) DEFAULT NULL,
  `addon_quantity` int(11) DEFAULT NULL,
  `addon_price` decimal(10,2) DEFAULT NULL,
  `addon_date_added` date DEFAULT NULL,
  PRIMARY KEY (`addon_id`),
  KEY `addon_FKIndex1` (`store_id`),
  CONSTRAINT `product_addons_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
