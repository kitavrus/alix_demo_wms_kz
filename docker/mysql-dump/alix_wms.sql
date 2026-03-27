-- Adminer 4.8.1 MySQL 5.5.57-0+deb7u1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE `api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_order_id` int(11) DEFAULT '0' COMMENT 'Our in/out/return id',
  `their_order_number` varchar(256) DEFAULT '0' COMMENT 'Their in/out/return number',
  `method_name` varchar(256) DEFAULT '' COMMENT 'Method name',
  `order_type` varchar(256) DEFAULT '' COMMENT 'in/out/return b2b or b2c',
  `request_status` varchar(256) DEFAULT '' COMMENT 'Request Status',
  `request_data` text COMMENT 'Request data',
  `response_data` text COMMENT 'Response data',
  `response_code` smallint(6) DEFAULT '0' COMMENT 'Response code',
  `response_message` text COMMENT 'Response error message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `base_barcodes`;
CREATE TABLE `base_barcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_id` int(11) DEFAULT '0',
  `outbound_id` int(11) DEFAULT '0',
  `inbound_id` int(11) DEFAULT '0',
  `order_type` int(11) DEFAULT NULL COMMENT 'Type: inbound, outbound, cargo pick-up',
  `base_barcode` varchar(34) DEFAULT '' COMMENT 'Base barcode',
  `box_barcode` varchar(34) DEFAULT '' COMMENT 'Box barcode',
  `box_total` varchar(24) DEFAULT '',
  `box_number` int(4) DEFAULT '0' COMMENT 'Box number',
  `ttn_barcode` int(34) DEFAULT '0' COMMENT 'TTN',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bookkeeper`;
CREATE TABLE `bookkeeper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` int(11) DEFAULT '0' COMMENT 'for sorting',
  `tl_delivery_proposal_id` int(11) DEFAULT '0' COMMENT 'Заявка на доставку',
  `tl_delivery_proposal_route_unforeseen_expenses_id` int(11) DEFAULT '0' COMMENT 'Расход по маршруту',
  `unique_key` varchar(64) DEFAULT '',
  `department_id` int(11) DEFAULT '0' COMMENT 'Отдел: склад, трнспорт',
  `doc_type_id` int(11) DEFAULT '0' COMMENT 'Тип документа: чек, счет-фак, тд',
  `doc_file` varchar(256) DEFAULT '' COMMENT 'Путь к файлу документа',
  `name_supplier` varchar(128) DEFAULT '' COMMENT 'Название поставщика',
  `description` varchar(228) DEFAULT '' COMMENT 'Описание засхода',
  `balance_sum` decimal(26,3) DEFAULT '0.000' COMMENT 'Остаток',
  `client_id` int(11) DEFAULT '1',
  `cash_type` smallint(6) DEFAULT '1',
  `expenses_type_id` smallint(6) DEFAULT '1',
  `price` decimal(26,3) DEFAULT '0.000' COMMENT 'приход и расход',
  `type_id` smallint(6) DEFAULT '1',
  `status` smallint(6) DEFAULT '0' COMMENT 'show, hide',
  `date_at` int(11) DEFAULT '0' COMMENT 'Дата',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bosses`;
CREATE TABLE `bosses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(128) DEFAULT '',
  `password` varchar(64) DEFAULT NULL COMMENT 'Password',
  `title` varchar(128) DEFAULT '',
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `status` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `box_size`;
CREATE TABLE `box_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_height` varchar(4) DEFAULT '' COMMENT 'Height',
  `box_width` varchar(4) DEFAULT '' COMMENT 'Width',
  `box_length` varchar(4) DEFAULT '' COMMENT 'Length',
  `box_code` varchar(4) DEFAULT '' COMMENT 'Box code',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `brokers`;
CREATE TABLE `brokers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(128) DEFAULT '',
  `title` varchar(128) DEFAULT '',
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `status` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `change_address_place`;
CREATE TABLE `change_address_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_barcode` varchar(16) DEFAULT '' COMMENT 'Address/Box barcode',
  `to_barcode` varchar(16) DEFAULT '' COMMENT 'Address/Box barcode',
  `product_barcode` varchar(32) DEFAULT '' COMMENT 'шк товара',
  `product_qty` int(11) DEFAULT '0' COMMENT 'кол-во товара',
  `change_type` smallint(6) DEFAULT '0' COMMENT 'Change type',
  `message` varchar(512) DEFAULT '' COMMENT 'Message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `check_box`;
CREATE TABLE `check_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse id',
  `employee_id` int(11) DEFAULT '0' COMMENT 'Employee id',
  `inventory_id` int(11) DEFAULT '0' COMMENT 'Inventory id',
  `box_barcode` varchar(18) DEFAULT '' COMMENT 'Box barcode',
  `place_address` varchar(18) DEFAULT '' COMMENT 'Place address barcode',
  `place_address_part1` varchar(5) DEFAULT '' COMMENT 'Place address floor',
  `place_address_part2` varchar(5) DEFAULT '' COMMENT 'Place address box',
  `place_address_part3` varchar(5) DEFAULT '' COMMENT 'Place address place',
  `place_address_part4` varchar(5) DEFAULT '' COMMENT 'Place address level',
  `place_address_part5` varchar(5) DEFAULT '' COMMENT 'Place address other',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected Quantity',
  `scanned_qty` int(11) DEFAULT '0' COMMENT 'Scanned Quantity',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `box_barcode` (`box_barcode`),
  KEY `place_address` (`place_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `check_box_inventory`;
CREATE TABLE `check_box_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_key` varchar(36) DEFAULT '' COMMENT 'Inventory key',
  `status` varchar(36) DEFAULT '' COMMENT 'Статус',
  `expected_product_qty` int(11) DEFAULT '0' COMMENT 'Expected product qty',
  `scanned_product_qty` int(11) DEFAULT '0' COMMENT 'Scanned product qty',
  `expected_box_qty` int(11) DEFAULT '0' COMMENT 'Expected box qty',
  `scanned_box_qty` int(11) DEFAULT '0' COMMENT 'Scanned box qty',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `complete_date` int(11) DEFAULT NULL COMMENT 'Packing date',
  `description` text COMMENT 'description',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `check_box_stock`;
CREATE TABLE `check_box_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT '0' COMMENT 'inventory id',
  `check_box_id` int(11) DEFAULT '0' COMMENT 'check box id',
  `stock_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse id',
  `employee_id` int(11) DEFAULT '0' COMMENT 'Employee id',
  `box_barcode` varchar(15) DEFAULT '' COMMENT 'Box barcode',
  `place_address` varchar(15) DEFAULT '' COMMENT 'Place address barcode',
  `stock_inbound_id` int(11) DEFAULT '0' COMMENT 'stock inbound id',
  `stock_inbound_item_id` int(11) DEFAULT '0' COMMENT 'stock inbound item id',
  `stock_outbound_id` int(11) DEFAULT '0' COMMENT 'stock outbound id',
  `stock_outbound_item_id` int(11) DEFAULT '0' COMMENT 'stock outbound item id',
  `stock_status_availability` int(11) DEFAULT '0' COMMENT 'stock status availability',
  `stock_client_product_sku` varchar(14) DEFAULT '' COMMENT 'Stock client product sku',
  `stock_inbound_status` smallint(6) DEFAULT '0' COMMENT 'Status inbound',
  `stock_outbound_status` smallint(6) DEFAULT '0' COMMENT 'Status outbound',
  `stock_condition_type` smallint(6) DEFAULT '0' COMMENT 'Condition type',
  `product_barcode` varchar(14) DEFAULT '' COMMENT 'Product Barcode',
  `serialized_data_stock` text COMMENT 'Serialized data stock',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `scanned_datetime` int(11) DEFAULT NULL COMMENT 'Scanned datetime',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `check_box_id` (`check_box_id`),
  KEY `inventory_id` (`inventory_id`),
  KEY `box_barcode` (`box_barcode`),
  KEY `place_address` (`place_address`),
  KEY `product_barcode` (`product_barcode`),
  KEY `status` (`status`),
  KEY `deleted` (`deleted`),
  KEY `stock_status_availability` (`stock_status_availability`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL COMMENT 'City name',
  `region_id` int(11) DEFAULT NULL COMMENT 'Region id',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `client_type` int(11) DEFAULT NULL,
  `username` varchar(128) DEFAULT '',
  `legal_company_name` varchar(128) DEFAULT NULL COMMENT 'Legal company name',
  `password` varchar(64) DEFAULT NULL COMMENT 'Password',
  `title` varchar(128) DEFAULT '',
  `full_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `status` smallint(6) DEFAULT '0',
  `on_stock` smallint(6) DEFAULT '0' COMMENT 'показывем на вмс и/или тмс',
  `internal_code_count` int(11) DEFAULT '0' COMMENT 'Internal count code',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_employees`;
CREATE TABLE `client_employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT '0' COMMENT 'Store ID',
  `client_id` int(11) NOT NULL COMMENT 'Client ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(128) DEFAULT '',
  `password` varchar(64) DEFAULT NULL COMMENT 'Password',
  `full_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `manager_type` smallint(6) DEFAULT '0' COMMENT 'Manager type: Director, simple manager, etc ...',
  `status` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_groups`;
CREATE TABLE `client_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT '0' COMMENT 'Group name',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status active, no active',
  `base_type` smallint(6) DEFAULT '0' COMMENT 'Type: base,custom',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_group_to_client`;
CREATE TABLE `client_group_to_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_group_id` int(11) DEFAULT '0' COMMENT 'Client group id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_profile`;
CREATE TABLE `client_profile` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `public_email` varchar(255) DEFAULT NULL,
  `gravatar_email` varchar(255) DEFAULT NULL,
  `gravatar_id` varchar(32) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_settings`;
CREATE TABLE `client_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL COMMENT 'Client ID',
  `option_name` text COMMENT 'Option name',
  `option_value` text COMMENT 'Option value',
  `default_value` text COMMENT 'Default value for this options',
  `description` text COMMENT 'Option description',
  `option_type` smallint(6) DEFAULT NULL COMMENT 'Type: function, dropdown etc',
  `deleted` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `client_token`;
CREATE TABLE `client_token` (
  `client_id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  UNIQUE KEY `client_token_unique` (`client_id`,`code`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `codebook`;
CREATE TABLE `codebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_prefix` varchar(4) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `count_cell` int(11) DEFAULT NULL,
  `barcode` int(11) DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  `base_type` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_cross_dock`;
CREATE TABLE `consignment_cross_dock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `party_number` varchar(32) DEFAULT NULL COMMENT 'Party number, received from the client',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in party',
  `accepted_rpt_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted rpt places qty',
  `expected_rpt_places_qty` int(11) DEFAULT '0' COMMENT 'Expected rpt places qty',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan party',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_inbound_orders`;
CREATE TABLE `consignment_inbound_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `party_number` varchar(128) DEFAULT '',
  `order_type` int(11) DEFAULT NULL COMMENT 'Party type: stock, cross-doc, etc',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal to point id',
  `from_point_title` varchar(255) DEFAULT '' COMMENT 'Internal from point title',
  `to_point_title` varchar(255) DEFAULT '' COMMENT 'Internal to point title',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `expected_qty` int(11) DEFAULT NULL COMMENT 'Expected product quantity in party',
  `accepted_qty` int(11) DEFAULT NULL COMMENT 'Accepted product quantity in party',
  `allocated_qty` int(11) DEFAULT NULL COMMENT 'Allocated product quantity in party',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in party',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan party',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `data_created_on_client` int(11) DEFAULT '0' COMMENT 'Date time created order on client system',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_outbound_orders`;
CREATE TABLE `consignment_outbound_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `party_number` varchar(128) DEFAULT '',
  `order_type` int(11) DEFAULT NULL COMMENT 'Party type: stock, cross-doc, etc',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `expected_qty` int(11) DEFAULT NULL COMMENT 'Expected product quantity in party',
  `accepted_qty` int(11) DEFAULT NULL COMMENT 'Accepted product quantity in party',
  `allocated_qty` int(11) DEFAULT NULL COMMENT 'Allocated product quantity in party',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in party',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan party',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_universal`;
CREATE TABLE `consignment_universal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `from_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `to_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `party_number` varchar(128) DEFAULT NULL COMMENT 'Party number, received from the client',
  `order_type` smallint(6) DEFAULT '0' COMMENT 'Order party type: stock, cross-doc, inbound, outbound etc',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `status_created_on_client` varchar(128) DEFAULT '0' COMMENT 'Status created on client side',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in party',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in party',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated product quantity in party',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected number places quantity in party',
  `allocated_number_places_qty` int(11) DEFAULT '0' COMMENT 'Allocated number places quantity in party',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `field_extra1` text COMMENT 'Extra field 1',
  `field_extra2` text COMMENT 'Extra field 2',
  `field_extra3` text COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `comment_created_on_client` text COMMENT 'Comment created on client side',
  `comment_internal` text COMMENT 'Comment for internal using',
  `expected_datetime` int(11) DEFAULT '0' COMMENT 'The expected date of delivery in stock',
  `data_created_on_client` int(11) DEFAULT '0' COMMENT 'Date time created order on client system',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'The end time of the scan party',
  `status_notification` smallint(6) DEFAULT '0' COMMENT 'Status notification',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_universal_orders`;
CREATE TABLE `consignment_universal_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0',
  `consignment_universal_id` int(11) DEFAULT '0',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `from_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `to_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `order_type` smallint(6) DEFAULT '0' COMMENT 'Order party type: stock, cross-doc, inbound, outbound etc',
  `order_type_client` varchar(128) DEFAULT '' COMMENT 'Order party type from client: stock, cross-doc, inbound, outbound etc',
  `party_number` varchar(128) DEFAULT NULL COMMENT 'Party number, received from the client',
  `order_number` varchar(128) DEFAULT NULL COMMENT 'Order number, received from the client',
  `box_barcode_client` varchar(28) DEFAULT NULL COMMENT 'Box barcode client, received from the client',
  `box_barcode` varchar(28) DEFAULT NULL COMMENT 'Box barcode, received from the client',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `status_created_on_client` varchar(128) DEFAULT '0' COMMENT 'Status created on client side',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in party',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in party',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated product quantity in party',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected number places quantity in party',
  `allocated_number_places_qty` int(11) DEFAULT '0' COMMENT 'Allocated number places quantity in party',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `field_extra1` text COMMENT 'Extra field 1',
  `field_extra2` text COMMENT 'Extra field 2',
  `field_extra3` text COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `status_notification` smallint(6) DEFAULT '0' COMMENT 'Status notification',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `consignment_universal_orders_items`;
CREATE TABLE `consignment_universal_orders_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0',
  `consignment_universal_id` int(11) DEFAULT '0',
  `consignment_universal_order_id` int(11) DEFAULT '0',
  `inbound_order_item_id` int(11) DEFAULT '0' COMMENT 'inbound_order_item_id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `from_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `to_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `order_type` smallint(6) DEFAULT '0' COMMENT 'Order party type: stock, cross-doc, inbound, outbound etc',
  `order_type_client` varchar(128) DEFAULT '' COMMENT 'Order party type from client: stock, cross-doc, inbound, outbound etc',
  `party_number` varchar(128) DEFAULT NULL COMMENT 'Party number, received from the client',
  `order_number` varchar(128) DEFAULT NULL COMMENT 'Order number, received from the client',
  `box_barcode_client` varchar(28) DEFAULT NULL COMMENT 'Box barcode client, received from the client',
  `box_barcode` varchar(28) DEFAULT NULL COMMENT 'Box barcode, received from the client',
  `product_barcode` varchar(28) DEFAULT NULL COMMENT 'Product barcode, received from the client',
  `product_id` varchar(28) DEFAULT NULL COMMENT 'Product id, received from the client',
  `product_id_on_client` varchar(64) DEFAULT '' COMMENT 'Product id on client system',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `status_created_on_client` varchar(128) DEFAULT '0' COMMENT 'Status created on client side',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in party',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in party',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated product quantity in party',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected number places quantity in party',
  `allocated_number_places_qty` int(11) DEFAULT '0' COMMENT 'Allocated number places quantity in party',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `field_extra1` text COMMENT 'Extra field 1',
  `field_extra2` text COMMENT 'Extra field 2',
  `field_extra3` text COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL COMMENT 'Country name',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cron_manager`;
CREATE TABLE `cron_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT '' COMMENT 'Название задачи',
  `order_id` int(11) DEFAULT '0' COMMENT 'Id закрываемой накладной',
  `status` varchar(128) DEFAULT 'NEW' COMMENT 'Статус',
  `type` varchar(128) DEFAULT '' COMMENT 'b2c-in,b2b-in,b2b-re',
  `result_message` text COMMENT 'Сообщение от сервиса',
  `total_counter` int(11) DEFAULT '0' COMMENT 'Счетчик по достижению которого можно закрывать накладную',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock`;
CREATE TABLE `cross_dock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `consignment_cross_dock_id` int(11) DEFAULT '0' COMMENT 'Consignment cross dock id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'From point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'To point id',
  `to_point_title` varchar(255) DEFAULT '' COMMENT 'To point title',
  `from_point_title` varchar(255) DEFAULT '' COMMENT 'From point title',
  `internal_barcode` varchar(128) DEFAULT NULL COMMENT 'Our barcode',
  `party_number` varchar(128) DEFAULT '',
  `order_number` varchar(128) DEFAULT '' COMMENT 'Order number',
  `order_type` int(11) DEFAULT NULL COMMENT 'Party type: stock, cross-doc, etc',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `print_outbound_status` varchar(255) DEFAULT 'no' COMMENT 'print out status',
  `cargo_status` smallint(6) DEFAULT '0',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in party',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in party',
  `box_m3` varchar(32) DEFAULT '0' COMMENT 'Box size m3',
  `weight_net` varchar(32) DEFAULT '0' COMMENT 'Box net weight',
  `weight_brut` varchar(32) DEFAULT '0' COMMENT 'Box brut weight',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan party',
  `accepted_datetime` int(11) DEFAULT NULL COMMENT 'Accepted dateTime',
  `date_left_warehouse` int(11) DEFAULT NULL,
  `date_delivered` int(11) DEFAULT NULL,
  `fail_delivery_status` text COMMENT 'Fail delivery status',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`),
  KEY `status` (`status`),
  KEY `order_number` (`order_number`),
  KEY `consignment_cross_dock_id` (`consignment_cross_dock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock_audit`;
CREATE TABLE `cross_dock_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock_items`;
CREATE TABLE `cross_dock_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cross_dock_id` int(11) DEFAULT '0' COMMENT 'Cross dock id',
  `box_barcode` varchar(54) DEFAULT '' COMMENT 'Scanned box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected places quantity in order',
  `box_m3` varchar(32) DEFAULT '0' COMMENT 'Box size m3',
  `weight_net` varchar(32) DEFAULT '0' COMMENT 'Box net weight',
  `weight_brut` varchar(32) DEFAULT '0' COMMENT 'Box brut weight',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted places quantity in order',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `cross_dock_id` (`cross_dock_id`),
  KEY `status` (`status`),
  KEY `box_barcode` (`box_barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock_items_audit`;
CREATE TABLE `cross_dock_items_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock_item_products`;
CREATE TABLE `cross_dock_item_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cross_dock_item_id` int(11) NOT NULL COMMENT 'Internal cross dock order id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cross_dock_log`;
CREATE TABLE `cross_dock_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_key` varchar(34) DEFAULT '' COMMENT 'Unique key if update exist order',
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `box_barcode` varchar(54) DEFAULT '' COMMENT 'Scanned box barcode',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'From point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'To point id',
  `to_point_title` varchar(255) DEFAULT '' COMMENT 'To point title',
  `from_point_title` varchar(255) DEFAULT '' COMMENT 'From point title',
  `party_number` varchar(128) DEFAULT '',
  `order_number` varchar(128) DEFAULT '' COMMENT 'Order number',
  `order_type` int(11) DEFAULT NULL COMMENT 'Party type: stock, cross-doc, etc',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in party',
  `expected_rpt_places_qty` int(11) DEFAULT '0' COMMENT 'Expected rpt places qty',
  `box_m3` varchar(32) DEFAULT '0' COMMENT 'Box size m3',
  `weight_net` varchar(32) DEFAULT '0' COMMENT 'Box net weight',
  `weight_brut` varchar(32) DEFAULT '0' COMMENT 'Box brut weight',
  `field_extra` text COMMENT 'Записываем содержимое (товары) короба',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan party',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan party',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_accounts`;
CREATE TABLE `customs_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` smallint(6) DEFAULT '0' COMMENT 'Валюта',
  `kg_netto` decimal(26,3) DEFAULT '0.000' COMMENT 'Вес нетто',
  `kg_brutto` decimal(26,3) DEFAULT '0.000' COMMENT 'Вес брутто',
  `invoice_number` varchar(128) DEFAULT '' COMMENT 'Инвойс',
  `qty_place` int(11) DEFAULT '0' COMMENT 'Количество мест',
  `qty_tnv_codes` int(11) DEFAULT '0' COMMENT 'ТНВ коды, общее кол-во',
  `price` decimal(26,3) DEFAULT '0.000' COMMENT 'Стоимость',
  `price_nds` decimal(26,3) DEFAULT '0.000' COMMENT 'Стоимость с НДС',
  `price_expenses_total` decimal(26,3) DEFAULT '0.000' COMMENT 'Общая стоимость расходов',
  `price_expenses_cache` decimal(26,3) DEFAULT '0.000' COMMENT 'Наличные расходы',
  `price_expenses_nds` decimal(26,3) DEFAULT '0.000' COMMENT 'Безнал расходы',
  `price_profit` decimal(26,3) DEFAULT '0.000' COMMENT 'Доход',
  `comments` text COMMENT 'Комментарий',
  `status` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_account_costs`;
CREATE TABLE `customs_account_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customs_accounts_id` int(11) DEFAULT '0' COMMENT 'Таможенный счет',
  `cost_type` smallint(6) DEFAULT '0' COMMENT 'Тип расхода',
  `title` varchar(128) DEFAULT '' COMMENT 'Название',
  `price_cost_our` decimal(26,3) DEFAULT '0.000' COMMENT 'Наш расход',
  `price_nds_cost_our` decimal(26,3) DEFAULT '0.000' COMMENT 'Наш расход с НДС',
  `price_cost_client` decimal(26,3) DEFAULT '0.000' COMMENT 'Счет клиенту',
  `price_nds_cost_client` decimal(26,3) DEFAULT '0.000' COMMENT 'Счет клиенту с НДС',
  `payment_status` int(11) DEFAULT '0' COMMENT 'Счет Новый, Выставлен, Оплачен',
  `who_pay` smallint(6) DEFAULT '0' COMMENT 'Кто платит',
  `comments` text COMMENT 'Комментарий',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_documents`;
CREATE TABLE `customs_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customs_account_id` int(11) DEFAULT NULL COMMENT 'Номер инвойса',
  `customs_account_cost_id` varchar(255) DEFAULT NULL COMMENT 'Расход',
  `version` smallint(6) DEFAULT '0' COMMENT 'Версия файла',
  `filename` varchar(256) DEFAULT NULL COMMENT 'Название файла',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_document_template`;
CREATE TABLE `customs_document_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '' COMMENT 'Название',
  `description` text COMMENT 'Описание',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_document_template_items`;
CREATE TABLE `customs_document_template_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customs_document_template_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '' COMMENT 'Название',
  `description` text COMMENT 'Описание',
  `file` varchar(255) DEFAULT '' COMMENT 'Путь к файлу',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_orders`;
CREATE TABLE `customs_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Клиент',
  `tl_delivery_proposals_id` int(11) DEFAULT NULL COMMENT 'Заявка на доставку',
  `customs_accounts_id` int(11) DEFAULT NULL COMMENT 'Таможенный счет',
  `from` varchar(255) DEFAULT '' COMMENT 'Откуда',
  `to` varchar(255) DEFAULT '' COMMENT 'Куда',
  `order_number` varchar(255) DEFAULT '' COMMENT 'Номер заказа',
  `status` smallint(6) DEFAULT '0' COMMENT 'Статус',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_order_documents`;
CREATE TABLE `customs_order_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customs_orders_id` int(11) DEFAULT NULL COMMENT 'ID заказа',
  `version` smallint(6) DEFAULT '0' COMMENT 'Версия файла',
  `filename` varchar(256) DEFAULT NULL COMMENT 'Название файла',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customs_transports`;
CREATE TABLE `customs_transports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customs_order_id` int(11) DEFAULT '0' COMMENT 'Заявка',
  `type_id` int(11) DEFAULT '0' COMMENT 'Тип: авто,жд,авиа',
  `avia_order_number` varchar(256) DEFAULT '' COMMENT 'авиа накладная',
  `avia_arrival_departure_time` varchar(256) DEFAULT '' COMMENT 'время вылета и прилета',
  `avia_status_document` int(11) DEFAULT NULL COMMENT 'Статус документов',
  `auto_number` varchar(256) DEFAULT '' COMMENT 'Номер машины',
  `auto_status` int(11) DEFAULT '0' COMMENT 'Статус машины',
  `auto_status_document` int(11) DEFAULT '0' COMMENT 'Статус документов',
  `railroad_number` varchar(256) DEFAULT '' COMMENT 'Номер жд накладной',
  `railroad_status` int(11) DEFAULT '0' COMMENT 'Статус жд',
  `railroad_status_document` int(11) DEFAULT '0' COMMENT 'Статус документов',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `data_matrix`;
CREATE TABLE `data_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_id` int(11) DEFAULT NULL COMMENT 'ИД приходной накладной',
  `inbound_item_id` int(11) DEFAULT NULL COMMENT 'ИД строки в приходной накладной',
  `outbound_id` int(11) DEFAULT NULL COMMENT 'ИД расходной накладной',
  `outbound_item_id` int(11) DEFAULT NULL COMMENT 'ИД строки в расходной накладной',
  `product_barcode` varchar(36) DEFAULT '' COMMENT 'Шк товара',
  `product_model` varchar(36) DEFAULT '' COMMENT 'Модель товара',
  `in_data_matrix_code` text COMMENT 'код дата матрицы при входе',
  `out_data_matrix_code` text COMMENT 'код дата матрицы при выходе',
  `status` varchar(256) DEFAULT 'not-scanned' COMMENT 'scanned',
  `print_status` varchar(255) DEFAULT 'no' COMMENT 'распечатали или нет',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `delivery_proposal_registry`;
CREATE TABLE `delivery_proposal_registry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dp_list` varchar(255) NOT NULL COMMENT 'Proposals IDs',
  `registry_type` int(11) DEFAULT NULL COMMENT 'Type of registry',
  `status` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_api_inbound_log`;
CREATE TABLE `ecommerce_api_inbound_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT '0' COMMENT 'Our inbound id',
  `our_inbound_item_id` int(11) DEFAULT '0' COMMENT 'Our inbound item id',
  `method_name` varchar(256) DEFAULT '' COMMENT 'Method name',
  `request_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `response_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `request_data` longtext COMMENT 'Request data',
  `response_data` longtext COMMENT 'Response data',
  `request_error_message` text COMMENT 'Request error message',
  `response_error_message` text COMMENT 'Response error message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_api_other_log`;
CREATE TABLE `ecommerce_api_other_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method_name` varchar(256) DEFAULT '' COMMENT 'Method name',
  `request_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `response_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `request_data` longtext COMMENT 'Request data',
  `response_data` longtext COMMENT 'Response data',
  `request_error_message` text COMMENT 'Request error message',
  `response_error_message` text COMMENT 'Response error message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_api_outbound_log`;
CREATE TABLE `ecommerce_api_outbound_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) DEFAULT '0' COMMENT 'Our outbound id',
  `our_outbound_item_id` int(11) DEFAULT '0' COMMENT 'Our outbound item id',
  `method_name` varchar(256) DEFAULT '' COMMENT 'Method name',
  `request_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `response_is_success` smallint(6) DEFAULT '0' COMMENT 'Response is success',
  `request_data` longtext COMMENT 'Request data',
  `response_data` longtext COMMENT 'Response data',
  `request_error_message` text COMMENT 'Request error message',
  `response_error_message` text COMMENT 'Response error message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `method_name` (`method_name`(255)),
  KEY `our_outbound_id` (`our_outbound_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_barcode_manager`;
CREATE TABLE `ecommerce_barcode_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode_prefix` varchar(5) DEFAULT '' COMMENT 'Barcode prefix',
  `title` varchar(256) DEFAULT '' COMMENT 'Title',
  `counter` int(11) DEFAULT '0' COMMENT 'Counter',
  `status` smallint(6) DEFAULT '1' COMMENT 'Status',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_cancel_by_client_outbound`;
CREATE TABLE `ecommerce_cancel_by_client_outbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0',
  `outbound_id` int(11) DEFAULT '0',
  `cancel_key` varchar(36) DEFAULT '',
  `order_number` varchar(36) DEFAULT '',
  `outbound_box` varchar(36) DEFAULT '',
  `client_OrderSource` varchar(36) DEFAULT '',
  `new_box_address` varchar(36) DEFAULT '',
  `status` varchar(36) DEFAULT '' COMMENT 'Статус',
  `api_status` varchar(36) DEFAULT '' COMMENT 'API cтатус',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `date_confirm` int(11) DEFAULT NULL COMMENT 'Confirm datetime',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_cancel_by_client_outbound_items`;
CREATE TABLE `ecommerce_cancel_by_client_outbound_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cancel_by_client_outbound_id` int(11) DEFAULT '0',
  `outbound_id` int(11) DEFAULT '0',
  `outbound_item_id` int(11) DEFAULT '0',
  `stock_id` int(11) DEFAULT '0',
  `client_SkuId` int(11) DEFAULT '0',
  `product_barcode` varchar(36) DEFAULT '',
  `old_box_address` varchar(36) DEFAULT '',
  `old_place_address` varchar(36) DEFAULT '',
  `new_box_address` varchar(36) DEFAULT '',
  `status` varchar(36) DEFAULT '' COMMENT 'Статус',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_cancel_shipment_request`;
CREATE TABLE `ecommerce_cancel_shipment_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) DEFAULT NULL,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `ShipmentId` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_cancel_shipment_response`;
CREATE TABLE `ecommerce_cancel_shipment_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cancel_shipment_request_id` int(11) DEFAULT NULL,
  `our_outbound_id` int(11) DEFAULT NULL,
  `IsSuccess` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_change_address_place`;
CREATE TABLE `ecommerce_change_address_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_barcode` varchar(16) DEFAULT '' COMMENT 'Address/Box barcode',
  `to_barcode` varchar(16) DEFAULT '' COMMENT 'Address/Box barcode',
  `product_barcode` varchar(16) DEFAULT '' COMMENT 'Product barcode',
  `product_qty` int(11) DEFAULT '0' COMMENT 'Product qty ',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_check_box`;
CREATE TABLE `ecommerce_check_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse id',
  `employee_id` int(11) DEFAULT '0' COMMENT 'Employee id',
  `inventory_id` int(11) DEFAULT '0',
  `box_barcode` varchar(15) DEFAULT '' COMMENT 'Box barcode',
  `place_address` varchar(15) DEFAULT '' COMMENT 'Place address barcode',
  `place_address_part1` varchar(5) DEFAULT '' COMMENT 'Place address floor',
  `place_address_part2` varchar(5) DEFAULT '' COMMENT 'Place address box',
  `place_address_part3` varchar(5) DEFAULT '' COMMENT 'Place address place',
  `place_address_part4` varchar(5) DEFAULT '' COMMENT 'Place address level',
  `place_address_part5` varchar(5) DEFAULT '' COMMENT 'Place address other',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected Quantity',
  `scanned_qty` int(11) DEFAULT '0' COMMENT 'Scanned Quantity',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `box_barcode` (`box_barcode`),
  KEY `place_address` (`place_address`),
  KEY `inventory_id` (`inventory_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_check_box_inventory`;
CREATE TABLE `ecommerce_check_box_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_key` varchar(36) DEFAULT '' COMMENT 'Inventory key',
  `status` varchar(36) DEFAULT '' COMMENT 'Статус',
  `expected_product_qty` int(11) DEFAULT '0' COMMENT 'Expected product qty',
  `scanned_product_qty` int(11) DEFAULT '0' COMMENT 'Scanned product qty',
  `expected_box_qty` int(11) DEFAULT '0' COMMENT 'Expected box qty',
  `scanned_box_qty` int(11) DEFAULT '0' COMMENT 'Scanned box qty',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `complete_date` int(11) DEFAULT NULL COMMENT 'Packing date',
  `description` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_check_box_stock`;
CREATE TABLE `ecommerce_check_box_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT '0',
  `check_box_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `stock_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse id',
  `box_barcode` varchar(15) DEFAULT '' COMMENT 'Box barcode',
  `place_address` varchar(15) DEFAULT '' COMMENT 'Place address barcode',
  `stock_inbound_id` int(11) DEFAULT '0' COMMENT 'stock inbound id',
  `stock_inbound_item_id` int(11) DEFAULT '0' COMMENT 'stock inbound item id',
  `stock_outbound_id` int(11) DEFAULT '0' COMMENT 'stock outbound id',
  `stock_outbound_item_id` int(11) DEFAULT '0' COMMENT 'stock outbound item id',
  `stock_status_availability` int(11) DEFAULT '0' COMMENT 'stock status availability',
  `stock_client_product_sku` varchar(14) DEFAULT '' COMMENT 'Stock client product sku',
  `stock_inbound_status` smallint(6) DEFAULT '0' COMMENT 'Status inbound',
  `stock_outbound_status` smallint(6) DEFAULT '0' COMMENT 'Status outbound',
  `stock_condition_type` smallint(6) DEFAULT '0' COMMENT 'Condition type',
  `stock_transfer_id` varchar(18) DEFAULT '',
  `stock_status_transfer` varchar(18) DEFAULT '',
  `stock_transfer_outbound_box` varchar(18) DEFAULT '',
  `product_barcode` varchar(14) DEFAULT '' COMMENT 'Product Barcode',
  `serialized_data_stock` text COMMENT 'Serialized data stock',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `scanned_datetime` int(11) DEFAULT NULL COMMENT 'Scanned datetime',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `check_box_id` (`check_box_id`),
  KEY `box_barcode` (`box_barcode`),
  KEY `place_address` (`place_address`),
  KEY `product_barcode` (`product_barcode`),
  KEY `status` (`status`),
  KEY `stock_id` (`stock_id`),
  KEY `inventory_id` (`inventory_id`),
  KEY `stock_status_availability` (`stock_status_availability`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_client`;
CREATE TABLE `ecommerce_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `legal_company_name` varchar(64) DEFAULT '' COMMENT 'legal company name',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_employees`;
CREATE TABLE `ecommerce_employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(64) DEFAULT '' COMMENT 'Barcode',
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_cargo_label_request`;
CREATE TABLE `ecommerce_get_cargo_label_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) DEFAULT NULL,
  `ExternalShipmentId` varchar(64) DEFAULT '',
  `CargoCompany` varchar(64) DEFAULT NULL,
  `VolumetricWeight` varchar(64) DEFAULT '',
  `PackageId` varchar(64) DEFAULT '',
  `SkuId` varchar(64) DEFAULT '',
  `Quantity` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_cargo_label_response`;
CREATE TABLE `ecommerce_get_cargo_label_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cargo_label_request_id` int(11) NOT NULL,
  `our_outbound_id` int(11) DEFAULT NULL,
  `ExternalShipmentId` varchar(512) DEFAULT '',
  `ShipmentId` varchar(512) DEFAULT '',
  `FileExtension` varchar(512) DEFAULT '',
  `FileData` text,
  `TrackingNumber` varchar(512) DEFAULT '',
  `TrackingUrl` varchar(512) DEFAULT '',
  `ReferenceNumber` varchar(512) DEFAULT '',
  `PageSize` varchar(512) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_inbound_data_request`;
CREATE TABLE `ecommerce_get_inbound_data_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `LcBarcode` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_inbound_data_response`;
CREATE TABLE `ecommerce_get_inbound_data_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `get_inbound_data_id` int(11) DEFAULT NULL,
  `InboundId` varchar(64) DEFAULT '',
  `FromBusinessUnitId` varchar(64) DEFAULT '',
  `LcOrCartonLabel` varchar(64) DEFAULT '',
  `NumberOfCartons` varchar(64) DEFAULT '',
  `SkuId` varchar(64) DEFAULT '',
  `LotOrSingleBarcode` varchar(64) DEFAULT '',
  `LotOrSingleQuantity` varchar(64) DEFAULT '',
  `Status` varchar(64) DEFAULT '',
  `ToBusinessUnitId` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_lot_content_request`;
CREATE TABLE `ecommerce_get_lot_content_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `LotBarcode` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_lot_content_response`;
CREATE TABLE `ecommerce_get_lot_content_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `get_lot_content_id` int(11) DEFAULT NULL,
  `LotBarcode` varchar(64) DEFAULT '',
  `ProductBarcode` varchar(64) DEFAULT '',
  `Quantity` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_shipments_request`;
CREATE TABLE `ecommerce_get_shipments_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `OrderQuantity` int(11) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_get_shipments_response`;
CREATE TABLE `ecommerce_get_shipments_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `get_shipments_request_id` int(11) DEFAULT '0',
  `ShipmentId` varchar(64) DEFAULT '',
  `ExternalShipmentNo` varchar(64) DEFAULT '',
  `ShipmentType` varchar(64) DEFAULT '',
  `ShipmentSource` varchar(64) DEFAULT '',
  `ShipmentDate` varchar(64) DEFAULT '',
  `Priority` varchar(64) DEFAULT '',
  `CustomerName` varchar(512) DEFAULT '',
  `ShippingAddress` varchar(512) DEFAULT '',
  `ShippingCountryCode` varchar(512) DEFAULT '',
  `ShippingCity` varchar(512) DEFAULT '',
  `ShippingCounty` varchar(512) DEFAULT '',
  `ShippingZipCode` varchar(512) DEFAULT '',
  `ShippingEmail` varchar(512) DEFAULT '',
  `ShippingPhone` varchar(512) DEFAULT '',
  `Destination` varchar(512) DEFAULT '',
  `CourierCompany` varchar(512) DEFAULT '',
  `FromBusinessUnitId` varchar(64) DEFAULT '',
  `CacStoreID` varchar(64) DEFAULT '',
  `PartyApprovalId` varchar(64) DEFAULT '',
  `PackMessage` varchar(512) DEFAULT '',
  `IsGiftWrapping` varchar(64) DEFAULT '',
  `GiftWrappingMessage` varchar(512) DEFAULT '',
  `Ek1` varchar(64) DEFAULT '',
  `Ek2` varchar(64) DEFAULT '',
  `Ek3` varchar(64) DEFAULT '',
  `B2CShipmentDetailList` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_inbound`;
CREATE TABLE `ecommerce_inbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `party_number` varchar(36) DEFAULT '' COMMENT 'Party number',
  `order_number` varchar(36) DEFAULT '' COMMENT 'Order number',
  `expected_box_qty` int(11) DEFAULT '0' COMMENT 'Expected box qty',
  `accepted_box_qty` int(11) DEFAULT '0' COMMENT 'Accepted box qty',
  `expected_lot_qty` int(11) DEFAULT '0' COMMENT 'Expected lot qty',
  `accepted_lot_qty` int(11) DEFAULT '0' COMMENT 'Accepted lot qty',
  `expected_product_qty` int(11) DEFAULT '0' COMMENT 'Expected product qty',
  `accepted_product_qty` int(11) DEFAULT '0' COMMENT 'Accepted product qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `date_confirm` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_inbound_items`;
CREATE TABLE `ecommerce_inbound_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_id` int(11) DEFAULT '0' COMMENT 'Inbound id',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `client_box_barcode` varchar(18) DEFAULT '' COMMENT 'Короб клиента',
  `client_inbound_id` varchar(18) DEFAULT '' COMMENT 'inbound id client',
  `client_lot_sku` varchar(18) DEFAULT '' COMMENT 'SKU лота клинта',
  `client_product_sku` varchar(18) DEFAULT NULL,
  `our_box_barcode` varchar(18) DEFAULT '' COMMENT 'Наш короб',
  `lot_barcode` varchar(18) DEFAULT '' COMMENT 'Шк лота',
  `product_barcode` varchar(18) DEFAULT '' COMMENT 'Шк товара',
  `product_name` varchar(255) DEFAULT '' COMMENT 'Product brand',
  `product_brand` varchar(255) DEFAULT '' COMMENT 'Product brand',
  `product_color` varchar(255) DEFAULT '' COMMENT 'Product color',
  `product_model` varchar(255) DEFAULT '' COMMENT 'Product color',
  `product_expected_qty` int(11) DEFAULT '0' COMMENT 'Product Expected qty',
  `product_accepted_qty` int(11) DEFAULT '0' COMMENT 'Product Accepted qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `api_status` smallint(6) DEFAULT '0' COMMENT 'API status',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_inbound_place_barcode`;
CREATE TABLE `ecommerce_inbound_place_barcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(16) DEFAULT '' COMMENT 'Place barcode',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_inventory`;
CREATE TABLE `ecommerce_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client id',
  `order_number` varchar(54) DEFAULT '' COMMENT 'Inventory number',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_inventory_rows`;
CREATE TABLE `ecommerce_inventory_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL COMMENT 'Inventory id',
  `column_number` int(11) DEFAULT '0' COMMENT 'Column number',
  `row_number` varchar(28) DEFAULT '',
  `level_number` int(11) NOT NULL DEFAULT '0',
  `floor_number` smallint(6) DEFAULT '0',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_outbound`;
CREATE TABLE `ecommerce_outbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `responsible_delivery_id` int(11) DEFAULT '0' COMMENT 'Ответственный за доставку',
  `order_number` varchar(36) DEFAULT '' COMMENT 'Order number',
  `external_order_number` varchar(36) DEFAULT NULL,
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `place_expected_qty` int(11) DEFAULT '0' COMMENT 'Place expected qty',
  `place_accepted_qty` int(11) DEFAULT '0' COMMENT 'Place accepted qty',
  `total_price` varchar(36) DEFAULT '' COMMENT 'Total price',
  `total_price_discount` varchar(36) DEFAULT '' COMMENT 'Total price tax',
  `total_price_tax` varchar(36) DEFAULT '' COMMENT 'Total price tax',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Mc',
  `kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Kg',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `api_status` smallint(6) DEFAULT '0' COMMENT 'API status',
  `first_name` varchar(128) DEFAULT '' COMMENT 'first_name',
  `middle_name` varchar(128) DEFAULT '' COMMENT 'middle_name',
  `last_name` varchar(128) DEFAULT '' COMMENT 'last_name',
  `customer_name` varchar(256) DEFAULT NULL,
  `phone_mobile1` varchar(128) DEFAULT '' COMMENT 'Phone mobile 1',
  `phone_mobile2` varchar(128) DEFAULT '' COMMENT 'Phone mobile 2',
  `email` varchar(128) DEFAULT '' COMMENT 'email',
  `country` varchar(128) DEFAULT '' COMMENT 'country',
  `region` varchar(128) DEFAULT '' COMMENT 'region',
  `city` varchar(128) DEFAULT '' COMMENT 'city',
  `zip_code` varchar(128) DEFAULT '' COMMENT 'zip_code',
  `street` varchar(128) DEFAULT '' COMMENT 'street',
  `house` varchar(6) DEFAULT '' COMMENT 'house',
  `building` varchar(6) DEFAULT '' COMMENT 'Корпус',
  `entrance` varchar(6) DEFAULT '' COMMENT 'Подъезд',
  `flat` varchar(6) DEFAULT '' COMMENT 'Номер квартиры',
  `intercom` varchar(6) DEFAULT '' COMMENT 'Домофон',
  `floor` varchar(6) DEFAULT '' COMMENT 'Этаж',
  `elevator` smallint(1) DEFAULT '0' COMMENT 'Лифт',
  `customer_address` varchar(512) DEFAULT NULL,
  `customer_comment` text COMMENT 'Комментарий покупателя',
  `ttn` text COMMENT 'Номер транспортной накладной',
  `payment_method` smallint(6) DEFAULT '0' COMMENT 'Метод оплаты',
  `payment_status` smallint(6) DEFAULT '0' COMMENT 'Статус оплаты',
  `data_created_on_client` int(11) DEFAULT NULL COMMENT 'Data created on client',
  `print_picking_list_date` int(11) DEFAULT NULL COMMENT 'Print picking list date',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `packing_date` int(11) DEFAULT NULL COMMENT 'Packing date',
  `package_type` varchar(3) DEFAULT '' COMMENT 'Package type',
  `date_left_warehouse` int(11) DEFAULT NULL COMMENT 'Date left warehouse',
  `date_delivered_to_customer` int(11) DEFAULT NULL COMMENT 'Date delivered to customer',
  `path_to_cargo_label_file` varchar(512) DEFAULT '' COMMENT 'Path to cargo label',
  `path_to_order_doc` varchar(512) DEFAULT '' COMMENT 'path_to_order_doc',
  `client_TrackingNumber` varchar(255) DEFAULT '' COMMENT 'Cargo company TrackingNumber',
  `client_TrackingUrl` varchar(255) DEFAULT '' COMMENT 'Cargo company TrackingUrl',
  `client_ReferenceNumber` varchar(255) DEFAULT '' COMMENT 'Cargo company ReferenceNumber',
  `client_CancelReason` varchar(1024) DEFAULT '' COMMENT 'Cargo company ReferenceNumber',
  `client_CargoCompany` varchar(256) DEFAULT NULL,
  `client_Priority` int(11) DEFAULT NULL,
  `client_ShippingCountryCode` varchar(24) DEFAULT NULL,
  `client_ShippingCity` varchar(64) DEFAULT NULL,
  `client_PackMessage` text,
  `client_GiftWrappingMessage` text,
  `client_StoreName` varchar(36) DEFAULT '' COMMENT 'client Store Name',
  `client_ShipmentSource` varchar(255) DEFAULT '' COMMENT 'Shipment Source',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `order_number` (`order_number`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_outbound_items`;
CREATE TABLE `ecommerce_outbound_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_sku` varchar(64) DEFAULT '' COMMENT 'Product sku',
  `product_name` varchar(64) DEFAULT '' COMMENT 'Product name',
  `product_model` varchar(64) DEFAULT '' COMMENT 'Product model',
  `product_barcode` varchar(18) DEFAULT '' COMMENT 'Product Barcode',
  `product_brand` varchar(255) DEFAULT '' COMMENT 'Product brand',
  `product_color` varchar(255) DEFAULT '' COMMENT 'Product color',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'Begin datetime',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'End datetime',
  `product_price` varchar(24) DEFAULT '0' COMMENT 'Unit Product price',
  `price_tax` varchar(24) DEFAULT '0' COMMENT 'Price Unit Tax',
  `price_discount` varchar(24) DEFAULT '0' COMMENT 'Price Unit Discount',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `cancel_qty` int(11) DEFAULT '0' COMMENT 'отмененное количество',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `comment_message` varchar(512) DEFAULT '' COMMENT 'Comment message',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `outbound_id` (`outbound_id`),
  KEY `product_sku` (`product_sku`),
  KEY `product_barcode` (`product_barcode`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_outbound_list`;
CREATE TABLE `ecommerce_outbound_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) DEFAULT '0' COMMENT 'Our outbound id',
  `client_order_number` varchar(256) DEFAULT '' COMMENT 'Client order number',
  `ttn_delivery_company` varchar(256) DEFAULT '' COMMENT 'Client order number',
  `list_title` varchar(36) DEFAULT '' COMMENT 'List title',
  `package_barcode` varchar(36) DEFAULT '' COMMENT 'Package barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `courier_company` varchar(255) DEFAULT '' COMMENT 'Courier company',
  `cargo_company_ttn` varchar(36) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_outbound_package_barcode`;
CREATE TABLE `ecommerce_outbound_package_barcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(16) DEFAULT '' COMMENT 'Package barcode',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_picking_list`;
CREATE TABLE `ecommerce_picking_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `employee_id` int(11) DEFAULT '0' COMMENT 'Employee id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound  id',
  `page_number` int(11) DEFAULT '0' COMMENT 'Page number',
  `page_total` int(11) DEFAULT '0' COMMENT 'Page total',
  `status` int(11) DEFAULT '0' COMMENT 'Status',
  `barcode` varchar(64) DEFAULT '' COMMENT 'List barcode',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the picking order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the picking order',
  `client_Priority` int(11) DEFAULT NULL,
  `client_ShippingCountryCode` varchar(24) DEFAULT NULL,
  `client_ShippingCity` varchar(64) DEFAULT NULL,
  `client_PackMessage` text,
  `client_GiftWrappingMessage` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_product`;
CREATE TABLE `ecommerce_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Inbound id',
  `product_on_client_id` varchar(64) DEFAULT '' COMMENT 'Product on client id',
  `product_sku` varchar(64) DEFAULT '' COMMENT 'Product sku',
  `product_name` varchar(64) DEFAULT '' COMMENT 'Product name',
  `product_model` varchar(64) DEFAULT '' COMMENT 'Product model',
  `product_barcode` varchar(18) DEFAULT '' COMMENT 'Product model',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_return`;
CREATE TABLE `ecommerce_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `order_number` varchar(36) DEFAULT '' COMMENT 'Order number',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product qty',
  `customer_name` varchar(256) DEFAULT '' COMMENT 'Customer full name',
  `city` varchar(128) DEFAULT '' COMMENT 'city',
  `customer_address` varchar(512) DEFAULT '' COMMENT 'Адрес',
  `client_ReferenceNumber` varchar(128) DEFAULT '' COMMENT 'Cargo company ReferenceNumber',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `outbound_box` varchar(18) DEFAULT '',
  `client_ExternalShipmentId` varchar(28) DEFAULT '',
  `client_ExternalOrderId` varchar(28) DEFAULT '',
  `client_OrderSource` varchar(28) DEFAULT '',
  `client_CargoReturnCode` varchar(28) DEFAULT '',
  `client_IsRefundable` varchar(8) DEFAULT '',
  `client_RefundableMessage` text,
  `return_reason` varchar(8) DEFAULT '',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `date_confirm` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_return_items`;
CREATE TABLE `ecommerce_return_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) DEFAULT '0' COMMENT 'Return id',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_barcode` varchar(18) DEFAULT '' COMMENT 'Шк товара',
  `product_barcode1` varchar(18) DEFAULT '',
  `product_barcode2` varchar(18) DEFAULT '',
  `product_barcode3` varchar(18) DEFAULT '',
  `product_barcode4` varchar(18) DEFAULT '',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Product Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Product Accepted qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `client_SkuId` varchar(16) DEFAULT '',
  `client_ImageUrl` text,
  `client_UnitPrice` varchar(16) DEFAULT '',
  `client_UnitDiscount` varchar(16) DEFAULT '',
  `client_SalesQuantity` int(11) DEFAULT '0',
  `client_ReturnedQuantity` int(11) DEFAULT '0',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_send_inbound_feedback_data_request`;
CREATE TABLE `ecommerce_send_inbound_feedback_data_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `InboundId` varchar(64) DEFAULT '',
  `LcOrCartonBarcode` varchar(64) DEFAULT '',
  `ProductBarcode` varchar(64) DEFAULT '',
  `ProductQuantity` varchar(64) DEFAULT '',
  `ProductDamaged` smallint(1) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_send_inbound_feedback_data_response`;
CREATE TABLE `ecommerce_send_inbound_feedback_data_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_inbound_id` int(11) DEFAULT NULL,
  `send_inbound_feedback_data_id` int(11) DEFAULT NULL,
  `IsSuccess` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_send_shipment_feedback_request`;
CREATE TABLE `ecommerce_send_shipment_feedback_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) NOT NULL,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `ShipmentId` varchar(64) DEFAULT '',
  `SkuId` varchar(64) DEFAULT '',
  `SkuBarcode` varchar(64) DEFAULT '',
  `Quantity` varchar(64) DEFAULT '',
  `WaybillSerial` varchar(64) DEFAULT '',
  `WaybillNumber` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_send_shipment_feedback_response`;
CREATE TABLE `ecommerce_send_shipment_feedback_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_outbound_id` int(11) NOT NULL,
  `send_shipment_feedback_id` int(11) DEFAULT NULL,
  `IsSuccess` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_stock`;
CREATE TABLE `ecommerce_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse id',
  `scan_in_employee_id` int(11) DEFAULT '0' COMMENT 'Scan inbound employee id',
  `scan_out_employee_id` int(11) DEFAULT '0' COMMENT 'Scan outbound employee id',
  `inbound_id` int(11) DEFAULT '0' COMMENT 'Inbound id',
  `inbound_item_id` int(11) DEFAULT '0' COMMENT 'Inbound item id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `return_id` int(11) DEFAULT '0' COMMENT 'Return id',
  `stock_adjustment_id` int(11) DEFAULT '0' COMMENT 'stock adjustment id',
  `stock_adjustment_status` int(11) DEFAULT '0' COMMENT 'stock adjustment status',
  `return_item_id` int(11) DEFAULT '0' COMMENT 'Return item id',
  `outbound_item_id` int(11) DEFAULT '0' COMMENT 'Outbound item id',
  `transfer_id` int(11) DEFAULT '0' COMMENT 'Transfer id',
  `transfer_item_id` int(11) DEFAULT '0' COMMENT 'Transfer item id',
  `status_transfer` int(11) DEFAULT '0' COMMENT 'Transfer status',
  `transfer_box_check_step` varchar(16) DEFAULT '' COMMENT 'Transfer box check step',
  `transfer_outbound_box` varchar(16) DEFAULT '' COMMENT 'Transfer outbound box',
  `client_box_barcode` varchar(18) DEFAULT '0' COMMENT 'Шк приходного короба клиента',
  `client_inbound_id` varchar(18) DEFAULT '' COMMENT 'inbound id client',
  `client_lot_sku` varchar(18) DEFAULT '' COMMENT 'SKU лота клинта',
  `client_product_sku` varchar(18) DEFAULT NULL,
  `lot_barcode` varchar(18) DEFAULT '0' COMMENT 'Шк лота',
  `product_barcode` varchar(64) DEFAULT '' COMMENT 'Шк товара',
  `product_qrcode` varchar(1024) NOT NULL COMMENT 'qr code обуви',
  `product_brand` varchar(255) DEFAULT '' COMMENT 'Product brand',
  `product_color` varchar(255) DEFAULT '' COMMENT 'Product color',
  `inbound_datamatrix_id` varchar(255) DEFAULT '' COMMENT 'Inbound datamatrix id',
  `inbound_datamatrix_code` varchar(255) DEFAULT '' COMMENT 'Inbound datamatrix code',
  `box_address_barcode` varchar(18) DEFAULT '0' COMMENT 'Адрес короба',
  `place_address_barcode` varchar(18) DEFAULT '0' COMMENT 'Адрес полки',
  `place_address_sort1` int(11) DEFAULT '0' COMMENT 'Для сортировки 1',
  `place_address_sort2` int(11) DEFAULT '0' COMMENT 'Для сортировки 2',
  `place_address_sort3` int(11) DEFAULT '0' COMMENT 'Для сортировки 3',
  `place_address_sort4` int(11) DEFAULT '0' COMMENT 'Для сортировки 4',
  `outbound_box` varchar(18) DEFAULT '0' COMMENT 'Шк короба в котором отгружаем',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `status_inbound` smallint(6) DEFAULT '0' COMMENT 'Status inbound',
  `status_outbound` smallint(6) DEFAULT '0' COMMENT 'Status outbound',
  `status_return` int(11) DEFAULT '0' COMMENT 'Return status',
  `api_status` smallint(6) DEFAULT '0' COMMENT 'API status',
  `status_availability` smallint(6) DEFAULT '0' COMMENT 'Доступен для резервировани или нет',
  `condition_type` smallint(6) DEFAULT '0' COMMENT 'Состояние товара: норм, брак, частичный брак',
  `reason_re_reserved` varchar(64) DEFAULT '' COMMENT 'Причина перерезерва',
  `order_re_reserved` varchar(34) DEFAULT '' COMMENT 'В каком заказе перерезерв',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_sku` varchar(64) DEFAULT '' COMMENT 'Product sku',
  `product_name` varchar(64) DEFAULT '' COMMENT 'Product name',
  `product_model` varchar(64) DEFAULT '' COMMENT 'Product model',
  `product_price` varchar(11) DEFAULT '0' COMMENT 'Product price',
  `product_season` varchar(16) DEFAULT '' COMMENT 'Product season',
  `product_season_year` varchar(4) DEFAULT '' COMMENT 'Product season year',
  `product_season_full` varchar(32) DEFAULT '' COMMENT 'Product season full',
  `scan_out_datetime` int(11) DEFAULT '0' COMMENT 'Scan outbound datetime',
  `scan_in_datetime` int(11) DEFAULT '0' COMMENT 'Scan inbound datetime',
  `scan_reserved_datetime` int(11) DEFAULT '0' COMMENT 'Reserved inbound datetime',
  `address_sort_order` int(11) DEFAULT NULL,
  `system_message` varchar(256) DEFAULT NULL COMMENT 'системный комментарий',
  `note_message1` varchar(256) DEFAULT NULL COMMENT 'Мои заметки',
  `note_message2` varchar(256) DEFAULT NULL COMMENT 'Мои заметки',
  `inventory_id` int(11) DEFAULT '0',
  `status_inventory` smallint(6) DEFAULT '0',
  `inventory_box_address_barcode` varchar(25) DEFAULT '' COMMENT 'старый шк короба',
  `inventory_place_address_barcode` varchar(25) DEFAULT '' COMMENT 'старый адрес места',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `inbound_id` (`inbound_id`),
  KEY `client_box_barcode` (`client_box_barcode`),
  KEY `product_barcode` (`product_barcode`),
  KEY `status_inbound` (`status_inbound`),
  KEY `status_outbound` (`status_outbound`),
  KEY `status_availability` (`status_availability`),
  KEY `box_address_barcode` (`box_address_barcode`),
  KEY `place_address_barcode` (`place_address_barcode`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_item_id` (`transfer_item_id`),
  KEY `status_transfer` (`status_transfer`),
  KEY `outbound_id` (`outbound_id`),
  KEY `client_product_sku` (`client_product_sku`),
  KEY `inventory_id` (`inventory_id`),
  KEY `status_inventory` (`status_inventory`),
  KEY `outbound_box` (`outbound_box`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_stock_adjustment`;
CREATE TABLE `ecommerce_stock_adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_barcode` varchar(36) DEFAULT '' COMMENT 'Шк товара',
  `product_quantity` smallint(6) DEFAULT '0' COMMENT 'Количество',
  `product_operator` varchar(1) DEFAULT '' COMMENT 'Оператор +-',
  `reason` text COMMENT 'Причина',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_stock_adjustment_request`;
CREATE TABLE `ecommerce_stock_adjustment_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `BusinessUnitId` varchar(64) DEFAULT '',
  `LotOrSingleBarcode` varchar(64) DEFAULT '',
  `Quantity` varchar(64) DEFAULT '',
  `Operator` varchar(64) DEFAULT '',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_stock_adjustment_response`;
CREATE TABLE `ecommerce_stock_adjustment_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_adjustment_request_id` int(11) DEFAULT NULL,
  `IsSuccess` varchar(64) DEFAULT '',
  `error_message` text,
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_transfer`;
CREATE TABLE `ecommerce_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `client_BatchId` varchar(18) DEFAULT '' COMMENT 'Номер партии клиента',
  `client_Status` varchar(36) DEFAULT '' COMMENT 'Статус клиента',
  `client_LcBarcode` varchar(18) DEFAULT '' COMMENT 'Короб клиента',
  `client_ToBusinessUnitId` int(11) DEFAULT '0' COMMENT 'Client store code',
  `expected_box_qty` int(11) DEFAULT '0' COMMENT 'Expected box qty',
  `status` varchar(36) DEFAULT '' COMMENT 'Статус',
  `api_status` varchar(36) DEFAULT '' COMMENT 'API cтатус',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `print_picking_list_date` int(11) DEFAULT NULL COMMENT 'Print picking list date',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'Begin scanning datetime',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'End scanning datetime',
  `packing_date` int(11) DEFAULT NULL COMMENT 'Packing date',
  `date_left_warehouse` int(11) DEFAULT NULL COMMENT 'Date left warehouse',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `client_BatchId` (`client_BatchId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ecommerce_transfer_items`;
CREATE TABLE `ecommerce_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `client_BatchId` varchar(18) DEFAULT '' COMMENT '????? ?????? ???????',
  `client_OutboundId` varchar(18) DEFAULT '' COMMENT '????? ???????? ???????',
  `client_SkuId` varchar(18) DEFAULT '' COMMENT 'SKU ID ???????',
  `client_Quantity` int(11) DEFAULT '0' COMMENT '???-?? ??????? ???????',
  `client_Status` varchar(36) DEFAULT '' COMMENT '?????? ???????',
  `client_ToBusinessUnitId` int(11) DEFAULT '0' COMMENT 'Client store code',
  `expected_box_qty` int(11) DEFAULT NULL,
  `status` varchar(36) DEFAULT '' COMMENT '??????',
  `api_status` varchar(36) DEFAULT '' COMMENT 'API c?????',
  `product_sku` varchar(64) DEFAULT '' COMMENT 'Product sku',
  `product_name` varchar(64) DEFAULT '' COMMENT 'Product name',
  `product_model` varchar(64) DEFAULT '' COMMENT 'Product model',
  `product_barcode` varchar(18) DEFAULT '' COMMENT 'Product Barcode',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'Begin datetime',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'End datetime',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `product_barcode` (`product_barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(128) DEFAULT '',
  `password` varchar(64) DEFAULT NULL COMMENT 'Password',
  `title` varchar(128) DEFAULT '',
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `barcode` varchar(32) DEFAULT '' COMMENT 'Barcode',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `manager_type` smallint(6) DEFAULT '0' COMMENT 'Manager type: Director, simple manager, etc ...',
  `department` smallint(6) DEFAULT '0' COMMENT 'Department: Stock, office, etc ...',
  `status` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `external_client_lead`;
CREATE TABLE `external_client_lead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `client_type` int(11) DEFAULT '0',
  `username` varchar(255) DEFAULT NULL,
  `legal_company_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_data_matrix`;
CREATE TABLE `inbound_data_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_id` varchar(36) DEFAULT '' COMMENT 'ИД приходной накладной',
  `inbound_item_id` varchar(36) DEFAULT '' COMMENT 'ИД строки в приходной накладной',
  `product_barcode` varchar(36) DEFAULT '' COMMENT 'Шк товара',
  `product_model` varchar(36) DEFAULT '' COMMENT 'Модель товара',
  `data_matrix_code` varchar(256) DEFAULT NULL COMMENT 'код дата матрицы',
  `status` varchar(256) DEFAULT 'not-scanned' COMMENT 'scanned',
  `print_status` varchar(255) DEFAULT 'no' COMMENT 'распечатали или нет',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `inbound_id` (`inbound_id`),
  KEY `product_barcode` (`product_barcode`),
  KEY `status` (`status`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_data_matrix2`;
CREATE TABLE `inbound_data_matrix2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_id` varchar(36) DEFAULT '' COMMENT 'ИД приходной накладной',
  `inbound_item_id` varchar(36) DEFAULT '' COMMENT 'ИД строки в приходной накладной',
  `product_barcode` varchar(36) DEFAULT '' COMMENT 'Шк товара',
  `product_model` varchar(36) DEFAULT '' COMMENT 'Модель товара',
  `data_matrix_code` text COMMENT 'код дата матрицы',
  `status` varchar(256) DEFAULT 'not-scanned' COMMENT 'scanned',
  `print_status` varchar(255) DEFAULT 'no' COMMENT 'распечатали или нет',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_orders`;
CREATE TABLE `inbound_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_order_id` varchar(64) DEFAULT NULL COMMENT 'ID client order',
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `supplier_id` int(11) DEFAULT NULL COMMENT 'Supplier store id',
  `warehouse_id` int(11) DEFAULT NULL COMMENT 'Warehouse store id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal to point id',
  `from_point_title` varchar(255) DEFAULT '' COMMENT 'Internal from point title',
  `to_point_title` varchar(255) DEFAULT '' COMMENT 'Internal to point title',
  `order_number` varchar(64) DEFAULT NULL COMMENT 'Order number, received from the client',
  `parent_order_number` varchar(64) DEFAULT '' COMMENT 'Parent order number',
  `client_box_barcode` varchar(128) DEFAULT '' COMMENT 'Client barcode box',
  `consignment_inbound_order_id` int(11) DEFAULT '0' COMMENT 'Consignment order internal id',
  `order_type` int(11) DEFAULT NULL COMMENT 'Order type: stock, cross-doc, etc',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `cargo_status` smallint(6) DEFAULT '0',
  `expected_qty` int(11) DEFAULT NULL COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT NULL COMMENT 'Accepted product quantity in order',
  `allocated_qty` int(11) DEFAULT '0',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in order',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in order',
  `zone` smallint(6) DEFAULT '0' COMMENT 'Zone inbound: good, bad, defect',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `date_confirm` int(11) DEFAULT NULL COMMENT 'Confirmation timestamp',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `data_created_on_client` int(11) DEFAULT '0' COMMENT 'Date time created order on client system',
  `comments` text COMMENT 'Comments',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`),
  KEY `status` (`status`),
  KEY `order_number` (`order_number`),
  KEY `client_box_barcode` (`client_box_barcode`),
  KEY `consignment_inbound_order_id` (`consignment_inbound_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_orders_audit`;
CREATE TABLE `inbound_orders_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_order_items`;
CREATE TABLE `inbound_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_order_id` int(11) NOT NULL COMMENT 'Internal outbound order id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_color` varchar(255) DEFAULT '',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `product_size` varchar(1024) DEFAULT '',
  `product_brand` varchar(1024) DEFAULT '',
  `product_category` varchar(255) DEFAULT '' COMMENT 'T-shirt,shoes',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted number places quantity in order',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected number places quantity in order',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `inbound_order_id` (`inbound_order_id`),
  KEY `status` (`status`),
  KEY `box_barcode` (`box_barcode`),
  KEY `product_barcode` (`product_barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_order_items_audit`;
CREATE TABLE `inbound_order_items_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_order_items_process`;
CREATE TABLE `inbound_order_items_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inbound_order_id` int(11) NOT NULL COMMENT 'Internal outbound order id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_order_sync_values`;
CREATE TABLE `inbound_order_sync_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `inbound_id` int(11) DEFAULT '0' COMMENT 'Inbound id',
  `inbound_client_id` varchar(128) DEFAULT '' COMMENT 'Client inbound id',
  `status_our` smallint(6) DEFAULT NULL COMMENT 'Status our',
  `status_client` smallint(6) DEFAULT NULL COMMENT 'Status client',
  `zone_our` varchar(64) DEFAULT '' COMMENT 'Zone our',
  `zone_client` varchar(64) DEFAULT '' COMMENT 'Zone client',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_unit_address`;
CREATE TABLE `inbound_unit_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` smallint(6) DEFAULT '0' COMMENT 'Warehouse id',
  `zone_id` int(11) DEFAULT '0' COMMENT 'Zone id',
  `inbound_order_id` int(11) DEFAULT '0' COMMENT 'Inbound order id',
  `code_book_id` int(11) DEFAULT '0' COMMENT 'Code book id',
  `to_rack_address` varchar(23) DEFAULT '' COMMENT 'To rack address barcode',
  `to_pallet_address` varchar(23) DEFAULT '' COMMENT 'To pallet address barcode',
  `to_box_address` varchar(23) DEFAULT '' COMMENT 'To box address barcode',
  `transfer_rack_address` varchar(23) DEFAULT '' COMMENT 'Transfer rack address barcode',
  `transfer_pallet_address` varchar(23) DEFAULT '' COMMENT 'Transfer pallet address barcode',
  `transfer_box_address` varchar(23) DEFAULT '' COMMENT 'Transfer box address barcode',
  `our_barcode` varchar(23) DEFAULT '' COMMENT 'Our Unit barcode',
  `client_barcode` varchar(23) DEFAULT '' COMMENT 'Client Unit barcode',
  `status` smallint(6) DEFAULT '1' COMMENT 'Status:',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inbound_upload_log`;
CREATE TABLE `inbound_upload_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `unique_key` varchar(34) DEFAULT '' COMMENT 'Unique key if update exist order',
  `order_number` varchar(34) DEFAULT '' COMMENT 'Inbound order number',
  `product_barcode` varchar(34) DEFAULT '' COMMENT 'Product barcode',
  `product_model` varchar(34) DEFAULT '' COMMENT 'Product model',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `order_type` smallint(1) DEFAULT '0' COMMENT 'Type: from stock, cross-dock',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client id',
  `order_number` varchar(54) DEFAULT '' COMMENT 'Inventory number',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `inventory_rows`;
CREATE TABLE `inventory_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL COMMENT 'Inventory id',
  `column_number` int(11) DEFAULT '0' COMMENT 'Column number',
  `row_number` varchar(28) DEFAULT '',
  `level_number` int(11) NOT NULL DEFAULT '0',
  `floor_number` smallint(6) DEFAULT '0',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kpi_setting`;
CREATE TABLE `kpi_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'ID in Client table',
  `operation_type` int(11) DEFAULT NULL COMMENT 'Type: picking, scanning, etc',
  `one_item_time` int(11) DEFAULT '0' COMMENT 'Time second by one operation',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movements`;
CREATE TABLE `movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_order_id` varchar(128) DEFAULT '' COMMENT 'Client order number id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `order_number` varchar(128) DEFAULT '' COMMENT 'Order number',
  `parent_order_number` varchar(128) DEFAULT '' COMMENT 'Parent order number',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `comments` text COMMENT 'Comments',
  `extra_fields` text COMMENT 'Extra fields',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `from_zone` smallint(6) DEFAULT NULL COMMENT 'From our zone',
  `to_zone` smallint(6) DEFAULT NULL COMMENT 'To our zone',
  `client_datetime` varchar(128) DEFAULT '' COMMENT 'Client datetime',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movement_history`;
CREATE TABLE `movement_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_order_id` varchar(128) DEFAULT '' COMMENT 'Client order id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `stock_id` int(11) DEFAULT '0' COMMENT 'Stock id',
  `inbound_id` int(11) DEFAULT '0' COMMENT 'Inbound id',
  `movement_id` int(11) DEFAULT '0' COMMENT 'Movement id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `from_zone_id` int(11) DEFAULT '0' COMMENT 'From zone id',
  `to_zone_id` int(11) DEFAULT '0' COMMENT 'To zone id',
  `product_barcode` varchar(128) DEFAULT '' COMMENT 'Product barcode',
  `product_model` varchar(128) DEFAULT '' COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT '' COMMENT 'Product sku',
  `product_qty` int(11) DEFAULT '0' COMMENT 'Product quantity',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movement_items`;
CREATE TABLE `movement_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movement_id` int(11) DEFAULT '0' COMMENT 'Movement id',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_name` varchar(128) DEFAULT '' COMMENT 'Product name',
  `product_model` varchar(128) DEFAULT '' COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT '' COMMENT 'Product sku',
  `product_description` text COMMENT 'Product description',
  `product_barcode` varchar(128) DEFAULT '' COMMENT 'Product barcode',
  `product_serialize_data` text COMMENT 'Client Product data',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `expected_qty` smallint(6) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` smallint(6) DEFAULT '0' COMMENT 'Accepted qty',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocated qty',
  `comments` text COMMENT 'comments',
  `from_zone` smallint(6) DEFAULT NULL COMMENT 'From zone',
  `to_zone` smallint(6) DEFAULT NULL COMMENT 'To zone',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movement_order_sync_values`;
CREATE TABLE `movement_order_sync_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `movement_id` int(11) DEFAULT '0' COMMENT 'Movement id',
  `movement_client_id` varchar(128) DEFAULT '' COMMENT 'Client movement id',
  `status_our` smallint(6) DEFAULT NULL COMMENT 'Status our',
  `status_client` smallint(6) DEFAULT NULL COMMENT 'Status client',
  `zone_our` varchar(64) DEFAULT '' COMMENT 'Zone our',
  `zone_client` varchar(64) DEFAULT '' COMMENT 'Zone client',
  `from_zone` smallint(6) DEFAULT NULL COMMENT 'From our zone',
  `to_zone` smallint(6) DEFAULT NULL COMMENT 'To our zone',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movement_pick_lists`;
CREATE TABLE `movement_pick_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `employee_id` int(11) DEFAULT '0' COMMENT 'Employee id',
  `order_id` int(11) DEFAULT '0' COMMENT 'Movement id',
  `page_number` int(11) DEFAULT '0' COMMENT 'Page number',
  `page_total` int(11) DEFAULT '0' COMMENT 'Page total',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `barcode` varchar(128) DEFAULT '' COMMENT 'List barcode',
  `employee_barcode` varchar(32) DEFAULT '' COMMENT 'Barcode: employee',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'Start time of the picking order',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'End time of the picking order',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `movement_pick_list_stock`;
CREATE TABLE `movement_pick_list_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movement_id` int(11) DEFAULT '0' COMMENT 'Movement id',
  `movement_pick_id` int(11) DEFAULT '0' COMMENT 'Movement pick id',
  `product_name` varchar(128) DEFAULT '0' COMMENT 'Product name',
  `product_barcode` varchar(64) DEFAULT '0' COMMENT 'Product barcode',
  `product_model` varchar(64) DEFAULT '0' COMMENT 'Product model',
  `product_sku` varchar(64) DEFAULT '0' COMMENT 'Product sku',
  `stock_id` int(11) DEFAULT '0' COMMENT 'Stock id',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `from_box` varchar(64) DEFAULT '' COMMENT 'From Box',
  `to_box` varchar(64) DEFAULT '' COMMENT 'To Box',
  `from_address` varchar(64) DEFAULT '' COMMENT 'From address',
  `to_address` varchar(64) DEFAULT '' COMMENT 'To address',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `order_process`;
CREATE TABLE `order_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL COMMENT 'Internal client id',
  `store_id` int(11) NOT NULL COMMENT 'Internal store id',
  `product_id` int(11) NOT NULL COMMENT 'Product id from table product',
  `product_price` decimal(26,6) DEFAULT NULL COMMENT 'Product price',
  `product_sku` varchar(64) NOT NULL COMMENT 'Product sku',
  `product_name` varchar(256) NOT NULL COMMENT 'Product name',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Status new, done, etc',
  `box_barcode` varchar(28) NOT NULL COMMENT 'Barcode box into which scan the goods',
  `product_barcode` varchar(28) NOT NULL COMMENT 'Product barcode from table product_barcode. This barcode is scanned item in the box',
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_boxes`;
CREATE TABLE `outbound_boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `our_box` varchar(13) DEFAULT '' COMMENT 'Our box',
  `client_box` varchar(16) DEFAULT '' COMMENT 'Client box',
  `client_extra_json` text COMMENT 'Client extra json data',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`),
  KEY `our_box` (`our_box`),
  KEY `client_box` (`client_box`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_box_labels`;
CREATE TABLE `outbound_box_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `outbound_order_id` int(11) DEFAULT NULL COMMENT 'Outbound Order id',
  `return_order_id` int(11) DEFAULT NULL,
  `outbound_order_number` varchar(255) DEFAULT NULL COMMENT 'Outbound Order Number',
  `box_label_url` varchar(255) DEFAULT NULL COMMENT 'url',
  `filename` varchar(255) DEFAULT NULL COMMENT 'file name',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_orders`;
CREATE TABLE `outbound_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_registry_id` int(11) DEFAULT '0' COMMENT 'Outbound registry id',
  `client_order_id` varchar(64) DEFAULT NULL COMMENT 'ID client order',
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `supplier_id` int(11) DEFAULT NULL COMMENT 'Supplier store id',
  `warehouse_id` int(11) DEFAULT NULL COMMENT 'Warehouse store id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal to point id',
  `to_point_title` varchar(256) DEFAULT '' COMMENT 'Internal from point text value',
  `from_point_title` varchar(256) DEFAULT '' COMMENT 'Internal from point text value',
  `order_number` varchar(255) DEFAULT NULL,
  `parent_order_number` varchar(255) DEFAULT NULL,
  `zone` smallint(6) DEFAULT '0' COMMENT 'Zone outbound: good, bad, defect',
  `consignment_outbound_order_id` int(11) DEFAULT '0' COMMENT 'Consignment outbound order id',
  `order_type` int(11) DEFAULT NULL COMMENT 'Order type: stock, cross-doc, etc',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `print_outbound_status` varchar(255) DEFAULT 'no' COMMENT 'print out status',
  `cargo_status` smallint(6) DEFAULT '0',
  `extra_status` varchar(256) DEFAULT '' COMMENT 'Специальный статус',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Volume',
  `kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Weight',
  `expected_qty` int(11) DEFAULT NULL COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT NULL COMMENT 'Accepted product quantity in order',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocate quantity',
  `accepted_number_places_qty` int(11) DEFAULT NULL COMMENT 'Accepted number places quantity in order',
  `expected_number_places_qty` int(11) DEFAULT NULL COMMENT 'Expected number places quantity in order',
  `allocated_number_places_qty` int(11) DEFAULT '0' COMMENT 'Allocate number places quantity',
  `expected_datetime` int(11) DEFAULT NULL COMMENT 'The expected date of delivery in stock',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `date_confirm` int(11) DEFAULT NULL COMMENT 'Confirmation timestamp',
  `data_created_on_client` int(11) DEFAULT NULL COMMENT 'Client order creation ts',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `packing_date` int(11) DEFAULT NULL COMMENT 'Print label ts',
  `date_left_warehouse` int(11) DEFAULT NULL COMMENT 'Print TTN ts',
  `date_delivered` int(11) DEFAULT NULL COMMENT 'Delivery ts',
  `fail_delivery_status` text COMMENT 'Fail delivery status',
  `api_send_data` mediumtext COMMENT 'Данне которые отправляем по апи',
  `api_complete_status` varchar(255) DEFAULT 'no' COMMENT 'Накладная закрыта по апи',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`),
  KEY `status` (`status`),
  KEY `order_number` (`order_number`),
  KEY `consignment_outbound_order_id` (`consignment_outbound_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_orders_audit`;
CREATE TABLE `outbound_orders_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_order_items`;
CREATE TABLE `outbound_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_order_id` int(11) NOT NULL COMMENT 'Internal outbound order id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_color` varchar(255) DEFAULT '',
  `product_brand` varchar(255) DEFAULT '',
  `product_category` varchar(255) DEFAULT '' COMMENT 'T-shirt,shoes',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `allocated_qty` int(11) DEFAULT '0' COMMENT 'Allocate number places quantity',
  `expected_number_places_qty` int(11) DEFAULT '0' COMMENT 'Expected number places',
  `accepted_number_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted number places quantity',
  `allocated_number_places_qty` int(11) DEFAULT '0' COMMENT 'Allocate number places quantity',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `outbound_order_id` (`outbound_order_id`),
  KEY `status` (`status`),
  KEY `box_barcode` (`box_barcode`),
  KEY `product_barcode` (`product_barcode`),
  KEY `product_model` (`product_model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_order_items_audit`;
CREATE TABLE `outbound_order_items_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_order_sync_values`;
CREATE TABLE `outbound_order_sync_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `outbound_client_id` varchar(128) DEFAULT '' COMMENT 'Client outbound id',
  `status_our` smallint(6) DEFAULT NULL COMMENT 'Status our',
  `status_client` smallint(6) DEFAULT NULL COMMENT 'Status client',
  `zone_our` varchar(64) DEFAULT '' COMMENT 'Zone our',
  `zone_client` varchar(64) DEFAULT '' COMMENT 'Zone client',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_picking_lists`;
CREATE TABLE `outbound_picking_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'client id',
  `outbound_order_id` int(11) DEFAULT '0',
  `employee_id` int(11) NOT NULL COMMENT 'Employee id',
  `barcode` varchar(32) DEFAULT NULL COMMENT 'barcode',
  `employee_barcode` varchar(32) DEFAULT NULL COMMENT 'Barcode: employee ',
  `page_number` int(11) DEFAULT '0',
  `page_total` int(11) DEFAULT '0',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status: print, begin, end ',
  `status_scan` smallint(6) DEFAULT '1' COMMENT 'Scan status',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the picking order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the picking order',
  `kpi_value` varchar(512) DEFAULT '' COMMENT 'КПЭ для сотрудника',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_picking_lists_audit`;
CREATE TABLE `outbound_picking_lists_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_separator`;
CREATE TABLE `outbound_separator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(256) DEFAULT '0' COMMENT 'Order number',
  `comments` varchar(1024) DEFAULT '0' COMMENT 'Comments',
  `status` varchar(256) DEFAULT '' COMMENT 'new,scanned,done',
  `path_to_file` text COMMENT 'Путь к файлу',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_separator_items`;
CREATE TABLE `outbound_separator_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_separator_id` int(11) DEFAULT '0' COMMENT 'OutboundSeparator id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `order_number` varchar(256) DEFAULT '0' COMMENT 'Order number',
  `out_box_barcode` varchar(256) DEFAULT '',
  `product_barcode` varchar(256) DEFAULT '',
  `status` varchar(256) DEFAULT '' COMMENT 'new,scanned',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_separator_stock`;
CREATE TABLE `outbound_separator_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_separator_id` int(11) DEFAULT '0' COMMENT 'OutboundSeparator id',
  `stock_id` int(11) DEFAULT '0' COMMENT 'stock id',
  `outbound_id` int(11) DEFAULT '0' COMMENT 'Outbound id',
  `order_number` varchar(256) DEFAULT '0' COMMENT 'Order number',
  `out_box_barcode` varchar(256) DEFAULT '',
  `in_box_barcode` varchar(256) DEFAULT NULL,
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_sku` varchar(256) DEFAULT '' COMMENT 'Product sku',
  `product_barcode` varchar(256) DEFAULT '' COMMENT 'Product barcode',
  `status` varchar(256) DEFAULT '' COMMENT 'new,scanned',
  `status_to_out` varchar(256) DEFAULT '' COMMENT 'Не отгружать',
  `stock_data` text COMMENT 'JSON stock data',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_unit_address`;
CREATE TABLE `outbound_unit_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `warehouse_id` smallint(6) DEFAULT '0' COMMENT 'Warehouse id',
  `zone_id` int(11) DEFAULT '0' COMMENT 'Zone id',
  `outbound_order_id` int(11) DEFAULT '0' COMMENT 'Outbound order id',
  `code_book_id` int(11) DEFAULT '0' COMMENT 'Code book id',
  `from_rack_address` varchar(23) DEFAULT '' COMMENT 'From rack address barcode',
  `from_pallet_address` varchar(23) DEFAULT '' COMMENT 'From pallet address barcode',
  `from_box_address` varchar(23) DEFAULT '' COMMENT 'From box address barcode',
  `our_barcode` varchar(23) DEFAULT '' COMMENT 'Our Unit barcode',
  `client_barcode` varchar(23) DEFAULT '' COMMENT 'Client Unit barcode',
  `status` smallint(6) DEFAULT '1' COMMENT 'Status:',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_upload_items_log`;
CREATE TABLE `outbound_upload_items_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_upload_id` int(11) NOT NULL COMMENT 'Internal outbound order upload id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `outbound_upload_log`;
CREATE TABLE `outbound_upload_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `unique_key` varchar(34) DEFAULT '' COMMENT 'Unique key if update exist order',
  `party_number` varchar(34) DEFAULT '' COMMENT 'Outbound party number',
  `order_number` varchar(34) DEFAULT '' COMMENT 'Outbound order number',
  `product_barcode` varchar(34) DEFAULT '' COMMENT 'Product barcode',
  `product_model` varchar(34) DEFAULT '' COMMENT 'Product model',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Store (point) id',
  `to_point_title` varchar(34) DEFAULT '' COMMENT 'Product model',
  `from_point_title` varchar(34) DEFAULT '' COMMENT 'Product model',
  `order_type` smallint(1) DEFAULT '0' COMMENT 'Type: from stock, cross-dock',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'CROSS-DOCK, RPT, etc ... ',
  `data_created_on_client` varchar(64) DEFAULT '' COMMENT 'Date time created order on client',
  `extra_fields` text COMMENT 'Example JSON: order_number, who received order, etc ...',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `placement_unit`;
CREATE TABLE `placement_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `zone_id` int(11) DEFAULT '0' COMMENT 'Zone id',
  `count_unit` int(11) DEFAULT '0' COMMENT 'Count unit',
  `type_inout` smallint(6) DEFAULT '0' COMMENT 'Type inbound or outbound, mix',
  `barcode` varchar(23) DEFAULT '' COMMENT 'Placement unit barcode',
  `status` smallint(6) DEFAULT '1' COMMENT 'Status: free, work, close',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `placement_unit_flow`;
CREATE TABLE `placement_unit_flow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `count_unit` int(11) DEFAULT '0' COMMENT 'Count unit',
  `client_id` int(11) DEFAULT '0' COMMENT 'client id',
  `stock_id` int(11) DEFAULT '0' COMMENT 'Stock id',
  `zone_id` int(11) DEFAULT '0' COMMENT 'Zone id',
  `inbound_order_id` int(11) DEFAULT '0' COMMENT 'Inbound order id',
  `inbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Inbound order item id',
  `outbound_order_id` int(11) DEFAULT '0' COMMENT 'Outbound order id',
  `outbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Outbound order item id',
  `placement_unit_barcode_id` int(11) DEFAULT '0' COMMENT 'Placement unit id',
  `placement_unit_barcode` varchar(23) DEFAULT '' COMMENT 'Placement unit barcode',
  `product_id` int(11) DEFAULT '0' COMMENT 'Product id',
  `product_barcode` varchar(23) DEFAULT '' COMMENT 'Product barcode',
  `product_model` varchar(64) DEFAULT '' COMMENT 'Product model',
  `product_name` varchar(256) DEFAULT '' COMMENT 'Product name',
  `product_sku` varchar(64) DEFAULT '' COMMENT 'Product sku',
  `product_qty` int(11) DEFAULT '0' COMMENT 'Product quantity',
  `status` smallint(6) DEFAULT '1' COMMENT 'Status: free, work, close',
  `to_rack_address` varchar(23) DEFAULT '' COMMENT 'To rack address barcode',
  `to_pallet_address` varchar(23) DEFAULT '' COMMENT 'To pallet address barcode',
  `to_box_address` varchar(23) DEFAULT '' COMMENT 'To box address barcode',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `client_product_id` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `model` varchar(256) DEFAULT '',
  `color` varchar(256) DEFAULT NULL,
  `size` varchar(256) DEFAULT NULL,
  `season` varchar(256) DEFAULT NULL,
  `made_in` varchar(256) DEFAULT NULL,
  `composition` varchar(256) DEFAULT NULL,
  `category` varchar(256) DEFAULT NULL,
  `gender` varchar(256) DEFAULT NULL,
  `sku` varchar(256) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `price` decimal(26,6) DEFAULT NULL COMMENT 'Product price',
  `weight_brutto` decimal(9,3) DEFAULT '0.000' COMMENT 'Weight brutto',
  `weight_netto` decimal(9,3) DEFAULT '0.000' COMMENT 'Weight netto',
  `m3` decimal(9,3) DEFAULT '0.000' COMMENT 'The Value',
  `length` decimal(9,3) DEFAULT '0.000' COMMENT 'Length',
  `width` decimal(9,3) DEFAULT '0.000' COMMENT 'Width',
  `height` decimal(9,3) DEFAULT '0.000' COMMENT 'Height',
  `barcode` varchar(128) DEFAULT '' COMMENT 'Barcode',
  `field_extra1` varchar(256) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(256) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(512) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `field_extra6` text COMMENT 'Extra field 6',
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `client_product_id` (`client_product_id`(255)),
  KEY `barcode` (`barcode`),
  KEY `model` (`model`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `product_barcodes`;
CREATE TABLE `product_barcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `barcode` varchar(24) NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `barcode` (`barcode`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `public_email` varchar(255) DEFAULT NULL,
  `gravatar_email` varchar(255) DEFAULT NULL,
  `gravatar_id` varchar(32) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `rack_address`;
CREATE TABLE `rack_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `zone_id` int(11) DEFAULT '0' COMMENT 'Zone id',
  `warehouse_id` smallint(6) DEFAULT '0' COMMENT 'warehouse_id',
  `address` varchar(255) DEFAULT NULL COMMENT 'Location coordinates',
  `sort_order` int(11) DEFAULT '0' COMMENT 'Sort order',
  `address_unit1` varchar(4) DEFAULT '' COMMENT 'Address unit 1',
  `address_unit2` varchar(4) DEFAULT '' COMMENT 'Address unit 2',
  `address_unit3` varchar(4) DEFAULT '' COMMENT 'Address unit 3',
  `address_unit4` varchar(4) DEFAULT '' COMMENT 'Address unit 4',
  `address_unit5` varchar(4) DEFAULT '' COMMENT 'Address unit 5',
  `address_unit6` varchar(4) DEFAULT '' COMMENT 'Address unit 6',
  `is_printed` smallint(6) DEFAULT '0' COMMENT 'Flag print or not',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL COMMENT 'Country',
  `name` varchar(64) DEFAULT NULL COMMENT 'Region name',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `return_orders`;
CREATE TABLE `return_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client store id',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Warehouse store id',
  `party_number` varchar(64) DEFAULT '0' COMMENT 'Party number',
  `order_number` varchar(28) DEFAULT '',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, in process, complete, etc',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in return',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in return',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `extra_fields` text COMMENT 'Example JSON: order_number,\nwho received order, etc ... ',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `return_order_items`;
CREATE TABLE `return_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_order_id` int(11) DEFAULT '0' COMMENT 'Internal inbound order id',
  `delivery_proposal_id` int(11) DEFAULT '0' COMMENT 'TTN',
  `product_id` int(11) DEFAULT '0' COMMENT 'Internal product id',
  `product_barcode` varchar(54) DEFAULT '' COMMENT 'Scanned product barcode',
  `product_model` varchar(128) DEFAULT '' COMMENT 'Product model (article)',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `box_barcode` varchar(64) DEFAULT '' COMMENT 'Box barcode',
  `client_box_barcode` varchar(64) DEFAULT '' COMMENT 'Client box barcode',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Our from shop id',
  `from_point_client_id` varchar(64) DEFAULT '' COMMENT 'Client from shop code',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Our to shop id',
  `to_point_client_id` varchar(64) DEFAULT '' COMMENT 'Client to shop code',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `begin_datetime` int(11) DEFAULT '0' COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT '0' COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_barcode` (`product_barcode`),
  KEY `client_box_barcode` (`client_box_barcode`),
  KEY `status` (`status`),
  KEY `return_order_id` (`return_order_id`),
  KEY `delivery_proposal_id` (`delivery_proposal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `return_order_item_products`;
CREATE TABLE `return_order_item_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_order_id` int(11) NOT NULL COMMENT 'Internal return order id',
  `return_order_item_id` int(11) NOT NULL COMMENT 'Internal return order item id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_price` decimal(16,3) DEFAULT NULL COMMENT 'Product price',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `product_madein` varchar(128) DEFAULT NULL COMMENT 'Product made in',
  `product_composition` varchar(128) DEFAULT NULL COMMENT 'Product composition',
  `product_exporter` text COMMENT 'Product exporter',
  `product_importer` text COMMENT 'Product importer',
  `product_description` text COMMENT 'Product importer',
  `product_serialize_data` text COMMENT 'Product serialize data',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `client_box_barcode` varchar(54) DEFAULT NULL COMMENT 'client Box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected product quantity in order',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted product quantity in order',
  `begin_datetime` int(11) DEFAULT NULL COMMENT 'The start time of the scan order',
  `end_datetime` int(11) DEFAULT NULL COMMENT 'The end time of the scan order',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `return_order_id` (`return_order_id`),
  KEY `return_order_item_id` (`return_order_item_id`),
  KEY `product_barcode` (`product_barcode`),
  KEY `client_box_barcode` (`client_box_barcode`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `return_tmp_orders`;
CREATE TABLE `return_tmp_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT '0' COMMENT 'Client',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `from_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'Internal from point id ',
  `to_point_client_id` varchar(128) DEFAULT '0' COMMENT 'Client from point id ',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `ttn` varchar(128) DEFAULT '' COMMENT 'Ttn number',
  `party_number` varchar(128) DEFAULT '' COMMENT 'Party number',
  `order_number` varchar(128) DEFAULT '' COMMENT 'Order number',
  `our_box_inbound_barcode` varchar(16) DEFAULT '' COMMENT 'Our box inbound barcode',
  `our_box_to_stock_barcode` varchar(16) DEFAULT '' COMMENT 'Our box to stock barcode',
  `client_box_barcode` varchar(16) DEFAULT '' COMMENT 'Client box barcode',
  `primary_address` varchar(28) DEFAULT '' COMMENT 'Primary address',
  `secondary_address` varchar(28) DEFAULT '' COMMENT 'Secondary address',
  `created_user_id` int(11) DEFAULT '0' COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT '0' COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `route_directions`;
CREATE TABLE `route_directions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT '0' COMMENT 'Direction name',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status active, no active',
  `base_type` int(11) DEFAULT '0' COMMENT 'Type: base,custom',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `route_direction_to_city`;
CREATE TABLE `route_direction_to_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_direction_id` int(11) DEFAULT '0' COMMENT 'Direction id',
  `city_id` int(11) DEFAULT '0' COMMENT 'City id',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sheep_shipment`;
CREATE TABLE `sheep_shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_address` varchar(64) DEFAULT '' COMMENT 'Place address',
  `box_barcode` varchar(64) DEFAULT '' COMMENT 'Box barcode',
  `created_user_id` int(11) DEFAULT '0' COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT '0' COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sheep_shipment_place_address`;
CREATE TABLE `sheep_shipment_place_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(128) DEFAULT '' COMMENT 'Place address',
  `created_user_id` int(11) DEFAULT '0' COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT '0' COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `social_account`;
CREATE TABLE `social_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `provider` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_unique` (`provider`,`client_id`),
  KEY `fk_user_account` (`user_id`),
  CONSTRAINT `fk_user_account` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scan_in_employee_id` int(11) DEFAULT '0' COMMENT 'scanning inbound employee id',
  `scan_out_employee_id` int(11) DEFAULT '0' COMMENT 'scanning outbound employee id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `ecom_outbound_id` int(11) DEFAULT '0' COMMENT 'Ecom outbound id',
  `ecom_outbound_items_id` int(11) DEFAULT '0' COMMENT 'Ecom outbound item id',
  `inbound_order_id` int(11) DEFAULT '0' COMMENT 'Internal inbound order id',
  `consignment_inbound_id` int(11) DEFAULT '0' COMMENT 'Consignment inbound id',
  `inbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Inbound order item id',
  `inbound_order_number` varchar(32) DEFAULT '' COMMENT 'Inbound order number',
  `outbound_order_id` int(11) DEFAULT '0' COMMENT 'Internal outbound order id',
  `consignment_outbound_id` int(11) DEFAULT '0' COMMENT 'Consignment outbound id',
  `outbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Outbound order item id',
  `outbound_picking_list_id` int(11) DEFAULT '0' COMMENT 'Internal outbound picking list id',
  `outbound_picking_list_barcode` varchar(32) DEFAULT '' COMMENT 'Internal outbound picking list barcode',
  `stock_adjustment_id` int(11) DEFAULT '0' COMMENT 'stock adjustment id',
  `stock_adjustment_status` int(11) DEFAULT '0' COMMENT 'stock adjustment status',
  `outbound_order_number` varchar(32) DEFAULT '' COMMENT 'Outbound order number',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Internal warehouse order id',
  `zone` smallint(6) DEFAULT '0' COMMENT 'Zone: good, bad, defect',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `our_product_barcode` varchar(24) DEFAULT NULL COMMENT 'Our product barcode',
  `bind_qr_code` varchar(512) DEFAULT NULL COMMENT 'Bind QR code',
  `product_color` varchar(255) DEFAULT '',
  `product_brand` varchar(255) DEFAULT '',
  `product_category` varchar(255) DEFAULT '' COMMENT 'T-shirt,shoes',
  `pallet_address` varchar(255) DEFAULT '' COMMENT 'qr code',
  `product_qrcode` varchar(255) DEFAULT NULL COMMENT 'qr code',
  `inbound_datamatrix_id` varchar(255) DEFAULT '' COMMENT 'Inbound datamatrix id',
  `inbound_datamatrix_code` varchar(255) DEFAULT '' COMMENT 'Inbound datamatrix code',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `is_product_type` int(11) DEFAULT '0' COMMENT 'Product type return or one lot box',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `box_size_barcode` varchar(32) DEFAULT NULL COMMENT 'Box size barcode',
  `box_size_m3` varchar(32) DEFAULT '0' COMMENT 'Box size m3',
  `box_kg` varchar(32) DEFAULT '' COMMENT 'kg box',
  `condition_type` smallint(6) DEFAULT '0' COMMENT '1=good,2=totally damaged, 3=partially damaged, 4 = lost item, 5 = par lost',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `pick_list_status` smallint(6) DEFAULT '1' COMMENT 'Pick list scan status',
  `status_availability` tinyint(2) DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `status_lost` int(11) DEFAULT '0' COMMENT 'Lost status',
  `inventory_id` int(11) DEFAULT '0',
  `inventory_primary_address` varchar(25) DEFAULT '' COMMENT 'старый шк короба',
  `inventory_secondary_address` varchar(24) DEFAULT '',
  `status_inventory` smallint(6) DEFAULT '0',
  `primary_address` varchar(25) DEFAULT '' COMMENT 'Box or pallet',
  `secondary_address` varchar(25) DEFAULT '' COMMENT 'Polka',
  `address_pallet_qty` smallint(6) DEFAULT '1' COMMENT 'Address pallet qty',
  `address_sort_order` int(11) DEFAULT '0' COMMENT 'Address sort order',
  `kpi_value` varchar(512) DEFAULT '' COMMENT 'kpi value',
  `scan_out_datetime` int(11) DEFAULT '0' COMMENT 'datetime scanning outbound',
  `scan_in_datetime` int(11) DEFAULT '0' COMMENT ' datetime scanning inbound',
  `inbound_client_box` varchar(32) DEFAULT '' COMMENT 'Короб в котором тавор прибыл к нас на склад от клиента',
  `system_status` varchar(32) DEFAULT '' COMMENT 'Системный статус: используется только для бизнес логики',
  `system_status_description` text COMMENT 'Описание системных статусов',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `outbound_separator_stock` varchar(255) DEFAULT '_no',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `inbound_order_id` (`inbound_order_id`),
  KEY `outbound_order_id` (`outbound_order_id`),
  KEY `product_id` (`product_id`),
  KEY `status` (`status`),
  KEY `box_barcode` (`box_barcode`),
  KEY `secondary_address` (`secondary_address`),
  KEY `outbound_picking_list_id` (`outbound_picking_list_id`),
  KEY `client_id` (`client_id`),
  KEY `product_barcode` (`product_barcode`),
  KEY `primary_address` (`primary_address`),
  KEY `scan_in_datetime` (`scan_in_datetime`),
  KEY `scan_out_datetime` (`scan_out_datetime`),
  KEY `is_product_type` (`is_product_type`),
  KEY `inventory_primary_address` (`inventory_primary_address`),
  KEY `inventory_secondary_address` (`inventory_secondary_address`),
  KEY `outbound_order_item_id` (`outbound_order_item_id`),
  KEY `inbound_client_box` (`inbound_client_box`),
  KEY `client_id_field_extra1_deleted` (`client_id`,`field_extra1`,`deleted`),
  KEY `field_extra1` (`field_extra1`),
  KEY `outbound_picking_list_barcode` (`outbound_picking_list_barcode`),
  KEY `status_availability` (`status_availability`),
  KEY `product_qrcode` (`product_qrcode`),
  KEY `ecom_outbound_id` (`ecom_outbound_id`),
  KEY `product_sku` (`product_sku`),
  KEY `our_product_barcode` (`our_product_barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_adjustment`;
CREATE TABLE `stock_adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_box_barcode` varchar(36) DEFAULT '' COMMENT 'Шк короба',
  `product_barcode` varchar(36) DEFAULT '' COMMENT 'Шк товара',
  `product_quantity` smallint(6) DEFAULT '0' COMMENT 'Количество',
  `product_operator` varchar(1) DEFAULT '' COMMENT 'Оператор +-',
  `reason` text COMMENT 'Причина',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_audit`;
CREATE TABLE `stock_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_extra_fields`;
CREATE TABLE `stock_extra_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0' COMMENT 'Stock id',
  `field_name` varchar(128) DEFAULT '0' COMMENT 'Field name',
  `field_value` varchar(256) DEFAULT '0' COMMENT 'Field value',
  `date_created` int(11) DEFAULT '0' COMMENT 'Date created',
  `created_by` int(11) DEFAULT '0' COMMENT 'Created by',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_history`;
CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scan_in_employee_id` int(11) DEFAULT '0' COMMENT 'scanning inbound employee id',
  `scan_out_employee_id` int(11) DEFAULT '0' COMMENT 'scanning outbound employee id',
  `client_id` int(11) DEFAULT '0' COMMENT 'Client id',
  `inbound_order_id` int(11) DEFAULT '0' COMMENT 'Internal inbound order id',
  `consignment_inbound_id` int(11) DEFAULT '0' COMMENT 'Consignment inbound id',
  `inbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Inbound order item id',
  `inbound_order_number` varchar(32) DEFAULT '' COMMENT 'Inbound order number',
  `outbound_order_id` int(11) DEFAULT '0' COMMENT 'Internal outbound order id',
  `consignment_outbound_id` int(11) DEFAULT '0' COMMENT 'Consignment outbound id',
  `outbound_order_item_id` int(11) DEFAULT '0' COMMENT 'Outbound order item id',
  `outbound_picking_list_id` int(11) DEFAULT '0' COMMENT 'Internal outbound picking list id',
  `outbound_picking_list_barcode` varchar(32) DEFAULT '' COMMENT 'Internal outbound picking list barcode',
  `outbound_order_number` varchar(32) DEFAULT '' COMMENT 'Outbound order number',
  `warehouse_id` int(11) DEFAULT '0' COMMENT 'Internal warehouse order id',
  `zone` smallint(6) DEFAULT '0' COMMENT 'Zone: good, bad, defect',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `product_name` varchar(128) DEFAULT NULL COMMENT 'Scanned product name',
  `product_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned product barcode',
  `product_model` varchar(128) DEFAULT NULL COMMENT 'Product model',
  `product_sku` varchar(128) DEFAULT NULL COMMENT 'Product sku',
  `is_product_type` int(11) DEFAULT '0' COMMENT 'Product type return or one lot box',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Box barcode',
  `box_size_barcode` varchar(32) DEFAULT NULL COMMENT 'Box size barcode',
  `box_size_m3` varchar(32) DEFAULT '0' COMMENT 'Box size m3',
  `box_kg` varchar(32) DEFAULT '' COMMENT 'kg box',
  `condition_type` smallint(6) DEFAULT '0' COMMENT '1=good,2=totally damaged, 3=partially damaged, 4 = lost item, 5 = par lost',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `pick_list_status` smallint(6) DEFAULT '1' COMMENT 'Pick list scan status',
  `status_availability` tinyint(2) DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `status_lost` int(11) DEFAULT '0' COMMENT 'Lost status',
  `inventory_id` int(11) DEFAULT '0',
  `inventory_primary_address` varchar(25) DEFAULT '' COMMENT 'старый шк короба',
  `inventory_secondary_address` varchar(24) DEFAULT '',
  `status_inventory` smallint(6) DEFAULT '0',
  `primary_address` varchar(25) DEFAULT '' COMMENT 'Box or pallet',
  `secondary_address` varchar(25) DEFAULT '' COMMENT 'Polka',
  `address_pallet_qty` smallint(6) DEFAULT '1' COMMENT 'Address pallet qty',
  `address_sort_order` int(11) DEFAULT '0' COMMENT 'Address sort order',
  `kpi_value` varchar(512) DEFAULT '' COMMENT 'kpi value',
  `scan_out_datetime` int(11) DEFAULT '0' COMMENT 'datetime scanning outbound',
  `scan_in_datetime` int(11) DEFAULT '0' COMMENT ' datetime scanning inbound',
  `inbound_client_box` varchar(32) DEFAULT '' COMMENT 'Короб в котором тавор прибыл к нас на склад от клиента',
  `system_status` varchar(32) DEFAULT '' COMMENT 'Системный статус: используется только для бизнес логики',
  `system_status_description` text COMMENT 'Описание системных статусов',
  `field_extra1` varchar(64) DEFAULT '' COMMENT 'Extra field 1',
  `field_extra2` varchar(128) DEFAULT '' COMMENT 'Extra field 2',
  `field_extra3` varchar(256) DEFAULT '' COMMENT 'Extra field 3',
  `field_extra4` text COMMENT 'Extra field 4',
  `field_extra5` text COMMENT 'Extra field 5',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `inbound_order_id` (`inbound_order_id`),
  KEY `outbound_order_id` (`outbound_order_id`),
  KEY `product_id` (`product_id`),
  KEY `status` (`status`),
  KEY `box_barcode` (`box_barcode`),
  KEY `secondary_address` (`secondary_address`),
  KEY `outbound_picking_list_id` (`outbound_picking_list_id`),
  KEY `client_id` (`client_id`),
  KEY `product_barcode` (`product_barcode`),
  KEY `primary_address` (`primary_address`),
  KEY `scan_in_datetime` (`scan_in_datetime`),
  KEY `scan_out_datetime` (`scan_out_datetime`),
  KEY `is_product_type` (`is_product_type`),
  KEY `inventory_primary_address` (`inventory_primary_address`),
  KEY `inventory_secondary_address` (`inventory_secondary_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_photos`;
CREATE TABLE `stock_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT '0' COMMENT 'Stock id',
  `is_type` int(11) DEFAULT '0' COMMENT 'Type product image, damage image',
  `path_to_photo` varchar(512) DEFAULT '' COMMENT 'Path to image',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stock_zone`;
CREATE TABLE `stock_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '' COMMENT 'Name',
  `address_begin` varchar(64) DEFAULT '' COMMENT 'Address begin',
  `address_end` varchar(64) DEFAULT '' COMMENT 'Address end',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  `created_at` int(11) DEFAULT NULL COMMENT 'Created at',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Updated at',
  `deleted` smallint(6) DEFAULT '0' COMMENT 'Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_barcode` varchar(128) DEFAULT NULL COMMENT 'Our barcode',
  `country_id` int(11) DEFAULT NULL COMMENT 'Country',
  `region_id` int(11) DEFAULT NULL COMMENT 'Region',
  `client_id` int(11) NOT NULL COMMENT 'Internal client id',
  `type_use` smallint(6) DEFAULT NULL COMMENT 'Type: store, stock, etc..',
  `owner_type` int(11) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `legal_point_name` varchar(128) DEFAULT NULL COMMENT 'Legal point name',
  `shopping_center_name` varchar(128) NOT NULL COMMENT 'Shopping center name. Example: Master ',
  `shopping_center_name_lat` varchar(255) DEFAULT NULL COMMENT 'Shop center name on roman alphabet',
  `contact_full_name` varchar(255) DEFAULT NULL,
  `contact_first_name` varchar(64) NOT NULL COMMENT 'Contact first name',
  `contact_middle_name` varchar(64) NOT NULL COMMENT 'Contact middle name',
  `contact_last_name` varchar(64) NOT NULL COMMENT 'Contact last name',
  `contact_first_name2` varchar(64) NOT NULL COMMENT 'Contact first name',
  `contact_middle_name2` varchar(64) NOT NULL COMMENT 'Contact middle name',
  `contact_last_name2` varchar(64) NOT NULL COMMENT 'Contact last name',
  `email` varchar(64) NOT NULL COMMENT 'Store email',
  `phone` varchar(64) NOT NULL COMMENT 'Store phone',
  `phone_mobile` varchar(64) NOT NULL COMMENT 'Store phone mobile',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_type` smallint(6) NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `city_id` varchar(128) NOT NULL,
  `city_lat` varchar(255) DEFAULT NULL COMMENT 'City name on roman alphabet',
  `zip_code` varchar(9) NOT NULL,
  `street` varchar(128) NOT NULL,
  `house` varchar(6) NOT NULL,
  `entrance` varchar(6) NOT NULL,
  `flat` varchar(6) NOT NULL,
  `intercom` smallint(6) NOT NULL,
  `floor` smallint(6) DEFAULT NULL,
  `elevator` smallint(6) NOT NULL,
  `comment` text NOT NULL,
  `shop_code` varchar(128) DEFAULT '0' COMMENT 'External shop code 2',
  `shop_code2` varchar(128) DEFAULT '0' COMMENT 'External shop code 2',
  `shop_code3` varchar(64) DEFAULT '' COMMENT 'client shop code 3',
  `internal_code` int(11) DEFAULT '0' COMMENT 'Internal incremental code for Colins Code',
  `city_prefix` varchar(4) DEFAULT '' COMMENT 'перфикс к городу на этикетки коробов',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `store_audit`;
CREATE TABLE `store_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `store_reviews`;
CREATE TABLE `store_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `store_id` int(11) DEFAULT NULL COMMENT 'Store id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'Delivery proposal id',
  `delivery_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `delivery_code` varchar(255) DEFAULT '' COMMENT 'Delivery secret code',
  `number_of_places` smallint(6) DEFAULT NULL,
  `rate` smallint(6) DEFAULT NULL COMMENT 'Rate, 1-star,2-star ... 5-star ',
  `comment` varchar(255) DEFAULT NULL COMMENT 'Review text',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `store_reviews_audit`;
CREATE TABLE `store_reviews_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sync_products`;
CREATE TABLE `sync_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(26,6) DEFAULT NULL COMMENT 'Internal product id',
  `product_id` int(11) DEFAULT NULL COMMENT 'Internal product id',
  `client_id` int(11) NOT NULL,
  `client_product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(24) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `article` varchar(64) NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `modified_user_id` int(11) NOT NULL,
  `sync_file_datetime` varchar(64) DEFAULT NULL COMMENT 'Last datetime update file',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents`;
CREATE TABLE `tl_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_barcode` varchar(128) DEFAULT NULL COMMENT 'Our barcode',
  `country_id` int(11) DEFAULT NULL COMMENT 'Country',
  `region_id` int(11) DEFAULT NULL COMMENT 'Region',
  `city_id` int(11) DEFAULT NULL COMMENT 'City',
  `name` varchar(128) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL COMMENT 'Main phone',
  `phone_mobile` varchar(64) DEFAULT NULL COMMENT 'Mobile phone',
  `description` text,
  `status` smallint(6) DEFAULT '0',
  `payment_period` smallint(6) DEFAULT '1',
  `flag_nds` smallint(6) DEFAULT '0' COMMENT 'NDS flag',
  `contact_first_name` varchar(64) DEFAULT NULL COMMENT 'Contact first name',
  `contact_middle_name` varchar(64) DEFAULT NULL COMMENT 'Contact middle name',
  `contact_last_name` varchar(64) DEFAULT NULL COMMENT 'Contact last name',
  `contact_phone` varchar(64) DEFAULT NULL COMMENT 'Phone contact ',
  `contact_phone_mobile` varchar(64) DEFAULT NULL COMMENT ' Mobile phone contact ',
  `contact_first_name2` varchar(64) DEFAULT NULL COMMENT 'Contact first name 2',
  `contact_middle_name2` varchar(64) DEFAULT NULL COMMENT 'Contact middle name 2',
  `contact_last_name2` varchar(64) DEFAULT NULL COMMENT 'Contact last name 2',
  `contact_phone2` varchar(64) DEFAULT NULL COMMENT 'Phone contact 2',
  `contact_phone_mobile2` varchar(64) DEFAULT NULL COMMENT 'Mobile phone contact 2',
  `address_title` varchar(256) DEFAULT NULL,
  `zip_code` varchar(9) DEFAULT NULL,
  `street` varchar(128) DEFAULT NULL,
  `house` varchar(6) DEFAULT NULL,
  `entrance` varchar(6) DEFAULT NULL,
  `flat` varchar(6) DEFAULT NULL,
  `intercom` smallint(6) DEFAULT NULL,
  `floor` smallint(6) DEFAULT NULL,
  `comment` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL COMMENT 'Created user id',
  `updated_user_id` int(11) DEFAULT NULL COMMENT 'Updated user id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_audit`;
CREATE TABLE `tl_agents_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_billing`;
CREATE TABLE `tl_agents_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL COMMENT 'Agent id',
  `status` smallint(6) DEFAULT '0',
  `cash_no` smallint(6) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_billing_audit`;
CREATE TABLE `tl_agents_billing_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_billing_conditions`;
CREATE TABLE `tl_agents_billing_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `tl_agents_billing_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `formula_tariff` text COMMENT 'Formula for tariff',
  `rule_type` smallint(32) DEFAULT '0' COMMENT 'rule type',
  `transport_type` smallint(32) DEFAULT '0',
  `route_from` int(11) DEFAULT NULL,
  `route_to` int(11) DEFAULT NULL,
  `price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Sale for client',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `price_kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Price kg without NDS',
  `price_kg_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price kg with NDS',
  `price_mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Price mc without NDS',
  `price_mc_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price mc with NDS',
  `price_pl` decimal(26,3) DEFAULT '0.000' COMMENT 'Price pl without NDS',
  `price_pl_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price pl with NDS',
  `status` smallint(6) DEFAULT '0',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_billing_conditions_audit`;
CREATE TABLE `tl_agents_billing_conditions_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agents_bookkeeper`;
CREATE TABLE `tl_agents_bookkeeper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT '0' COMMENT 'Заявка на доставку',
  `name` varchar(128) DEFAULT '' COMMENT 'Название поставщика',
  `description` varchar(228) DEFAULT '' COMMENT 'Описание засхода',
  `invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Сумма счета',
  `month_from` varchar(64) DEFAULT '' COMMENT 'Счет с',
  `month_to` varchar(64) DEFAULT '' COMMENT 'Счет по',
  `status` smallint(6) DEFAULT '0' COMMENT 'Счет веставлен, счет оплачен',
  `date_of_invoice` int(11) DEFAULT '0' COMMENT 'Дата выставления счета',
  `payment_date_invoice` int(11) DEFAULT '0' COMMENT 'Дата оплаты счета',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_agent_employees`;
CREATE TABLE `tl_agent_employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_agent_id` int(11) NOT NULL COMMENT 'Tl Agent ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(128) DEFAULT '',
  `first_name` varchar(64) DEFAULT '' COMMENT 'First name',
  `middle_name` varchar(64) DEFAULT '' COMMENT 'Middle name',
  `last_name` varchar(64) DEFAULT '' COMMENT 'Last name',
  `phone` varchar(64) DEFAULT '' COMMENT 'Phone',
  `phone_mobile` varchar(64) DEFAULT '' COMMENT 'Phone mobile',
  `email` varchar(64) DEFAULT '' COMMENT 'email',
  `manager_type` smallint(6) DEFAULT '0' COMMENT 'Manager type: Director, simple manager, etc ...',
  `status` smallint(6) DEFAULT '0',
  `password` varchar(128) DEFAULT NULL COMMENT 'Password',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_cars`;
CREATE TABLE `tl_cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL COMMENT 'Agent',
  `title` varchar(128) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `description` text,
  `status` smallint(6) DEFAULT '0',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Kilogram',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposals`;
CREATE TABLE `tl_delivery_proposals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `external_client_lead_id` int(11) DEFAULT NULL,
  `transportation_order_lead_id` int(11) DEFAULT NULL,
  `transport_type_loading` int(11) DEFAULT '0' COMMENT 'Метод погрузки',
  `transport_who_pays` smallint(2) DEFAULT '0' COMMENT 'Кто платит',
  `is_client_confirmed` smallint(6) DEFAULT NULL COMMENT 'If dp created our operator',
  `ready_to_invoicing` smallint(6) DEFAULT '0',
  `source` smallint(6) DEFAULT NULL COMMENT 'Source: client, agent, api, etc... ',
  `route_from` int(11) DEFAULT NULL COMMENT 'Example: DC-APORT',
  `route_to` int(11) DEFAULT NULL COMMENT 'Example: DC-APORT',
  `sender_contact` varchar(512) DEFAULT '',
  `sender_contact_id` int(11) DEFAULT '0',
  `recipient_contact` varchar(512) DEFAULT '',
  `recipient_contact_id` int(11) DEFAULT '0',
  `company_transporter` smallint(6) DEFAULT '0' COMMENT 'Nomadex, RLC',
  `seal` varchar(255) DEFAULT NULL COMMENT 'Plomba',
  `change_price` int(11) DEFAULT '1' COMMENT 'Change price If price not empty',
  `change_mckgnp` int(11) DEFAULT '1' COMMENT 'Change mc, kg, np  If not empty',
  `delivery_type` int(11) DEFAULT '0' COMMENT 'Type: Transfer, Simple',
  `delivery_method` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT '0' COMMENT 'Internal car id',
  `agent_id` int(11) DEFAULT '0' COMMENT 'Internal agent id',
  `driver_name` varchar(128) DEFAULT '' COMMENT 'Driver name',
  `driver_phone` varchar(128) DEFAULT '' COMMENT 'Driver phone',
  `driver_auto_number` varchar(64) DEFAULT '' COMMENT 'Driver auto number',
  `car_price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Car price',
  `car_price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Car price with NDS',
  `delivery_date` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `expected_delivery_date` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `shipped_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `accepted_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `mc_actual` decimal(26,3) DEFAULT NULL,
  `kg` decimal(26,3) DEFAULT '0.000',
  `kg_actual` decimal(26,3) DEFAULT NULL,
  `volumetric_weight` decimal(26,3) DEFAULT '0.000' COMMENT 'объемный вес',
  `number_places` int(11) DEFAULT '0' COMMENT 'Estimated number palaces',
  `number_places_actual` int(11) DEFAULT '0' COMMENT 'Real number palaces',
  `declared_value` decimal(26,2) DEFAULT '0.00' COMMENT 'Declared value of shipment',
  `shipment_description` varchar(255) DEFAULT NULL,
  `cash_no` smallint(6) DEFAULT '0' COMMENT 'nal/bez',
  `price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Sale for client',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `price_expenses_total` decimal(26,3) DEFAULT NULL COMMENT 'Price expenses',
  `price_expenses_cache` decimal(26,3) DEFAULT NULL COMMENT 'Price expenses cache ',
  `price_expenses_with_vat` decimal(26,3) DEFAULT NULL COMMENT 'Price expenses with vat',
  `price_our_profit` decimal(26,3) DEFAULT NULL COMMENT 'Our price',
  `status` smallint(6) DEFAULT '0',
  `status_invoice` smallint(6) DEFAULT '0' COMMENT 'Invoice status: invoice not set, invoice set, invoice paid',
  `fail_delivery_status` text COMMENT 'Fail delivery status',
  `comment` text,
  `bl_data` text COMMENT 'Example: last change data,\ndatetime set status on route, etc ... ',
  `extra_fields` text COMMENT 'Example JSON: order_number,\nwho received order, etc ... ',
  `client_ttn` varchar(16) DEFAULT '' COMMENT 'Client TTN',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`),
  KEY `route_from` (`route_from`),
  KEY `route_to` (`route_to`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposals_audit`;
CREATE TABLE `tl_delivery_proposals_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_billing`;
CREATE TABLE `tl_delivery_proposal_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `from_country_id` int(11) DEFAULT '0',
  `from_region_id` int(11) DEFAULT '0',
  `from_city_id` int(11) DEFAULT '0',
  `to_country_id` smallint(6) DEFAULT '0',
  `to_region_id` smallint(6) DEFAULT '0',
  `to_city_id` smallint(6) DEFAULT '0',
  `route_from` int(11) DEFAULT '0' COMMENT 'Example: DC-APORT',
  `route_to` int(11) DEFAULT '0' COMMENT 'Example: DC-APORT',
  `rule_type` smallint(6) DEFAULT '0' COMMENT 'By mc, kg, condition',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Kilogram',
  `number_places` int(11) DEFAULT '0' COMMENT 'Estimated number palaces',
  `price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Sale for client',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `price_invoice_kg` decimal(26,2) DEFAULT '0.00' COMMENT 'Price by kg',
  `price_invoice_kg_with_vat` decimal(26,2) DEFAULT '0.00' COMMENT 'Price by kg with vat',
  `price_invoice_mc` decimal(26,2) DEFAULT '0.00' COMMENT 'Price by mc',
  `price_invoice_mc_with_vat` decimal(26,2) DEFAULT '0.00' COMMENT 'Price by mc with vat',
  `formula_tariff` text COMMENT 'Formula for tariff',
  `status` smallint(6) DEFAULT '0',
  `delivery_term` varchar(255) DEFAULT NULL,
  `delivery_term_from` int(11) DEFAULT NULL,
  `delivery_term_to` int(11) DEFAULT NULL,
  `tariff_type` smallint(6) DEFAULT '0' COMMENT 'default etc',
  `cooperation_type` smallint(6) DEFAULT '0' COMMENT 'one-time, full freight etc',
  `delivery_type` smallint(6) DEFAULT '0' COMMENT 'warhouse-warhouse, door-door etc',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_billing_audit`;
CREATE TABLE `tl_delivery_proposal_billing_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_billing_conditions`;
CREATE TABLE `tl_delivery_proposal_billing_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_billing_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'Sale for client',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `formula_tariff` text COMMENT 'Formula for tariff',
  `status` smallint(6) DEFAULT '0',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `delivery_type` smallint(6) DEFAULT '0',
  `sort_order` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_billing_conditions_audit`;
CREATE TABLE `tl_delivery_proposal_billing_conditions_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_default_routes`;
CREATE TABLE `tl_delivery_proposal_default_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'From point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'To point id',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_default_sub_routes`;
CREATE TABLE `tl_delivery_proposal_default_sub_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_default_route_id` int(11) DEFAULT NULL COMMENT 'DP route id',
  `client_id` int(11) DEFAULT NULL COMMENT 'Client store id',
  `agent_id` smallint(32) DEFAULT NULL,
  `car_id` smallint(32) DEFAULT NULL,
  `transport_type` smallint(32) DEFAULT '0',
  `from_point_id` int(11) DEFAULT '0' COMMENT 'From point id',
  `to_point_id` int(11) DEFAULT '0' COMMENT 'To point id',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_default_unforeseen_expenses`;
CREATE TABLE `tl_delivery_proposal_default_unforeseen_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_default_route_id` int(11) DEFAULT NULL COMMENT 'Delivery proposal route id',
  `tl_delivery_proposal_default_sub_route_id` int(11) DEFAULT NULL COMMENT 'Delivery proposal sub route id',
  `type_id` int(11) DEFAULT '0' COMMENT 'Type unforeseen expenses id',
  `name` varchar(255) DEFAULT NULL COMMENT 'Name',
  `who_pays` smallint(6) DEFAULT NULL COMMENT 'Who pays',
  `price_cache` decimal(26,3) DEFAULT '0.000' COMMENT 'Price expenses',
  `cash_no` smallint(6) DEFAULT '0' COMMENT 'Nal/bez',
  `price_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'C NDS',
  `comment` text COMMENT 'Comment',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_lead`;
CREATE TABLE `tl_delivery_proposal_lead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'Delivery proposal id',
  `from_city_id` int(11) DEFAULT NULL COMMENT 'From city',
  `to_city_id` int(11) DEFAULT NULL COMMENT 'To city',
  `status` smallint(6) DEFAULT NULL COMMENT 'Status',
  `price` decimal(26,3) DEFAULT '0.000' COMMENT 'Price',
  `m3` decimal(26,3) DEFAULT '0.000' COMMENT 'M3',
  `kg` decimal(26,3) DEFAULT '0.000' COMMENT 'Kg',
  `name` varchar(128) DEFAULT '' COMMENT 'Name',
  `phone` varchar(128) DEFAULT '' COMMENT 'Name',
  `comment` text COMMENT 'Comment',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_orders`;
CREATE TABLE `tl_delivery_proposal_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `status` smallint(6) DEFAULT NULL COMMENT 'Status: new, scanned, packed, etc...',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'Internal tl_delivery_proposal id',
  `order_type` int(11) DEFAULT NULL COMMENT 'Order type inbound or outbound',
  `delivery_type` smallint(2) DEFAULT '0' COMMENT 'Type: RPT or Cross-dock',
  `order_id` int(11) DEFAULT NULL COMMENT 'Order id',
  `order_number` varchar(128) DEFAULT NULL COMMENT 'Order number',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `number_places` int(11) DEFAULT NULL COMMENT 'Number places',
  `mc` decimal(26,3) DEFAULT NULL COMMENT 'M3',
  `mc_actual` decimal(26,3) DEFAULT NULL COMMENT 'Mc actual',
  `kg` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram',
  `kg_actual` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram actual',
  `number_places_actual` int(11) DEFAULT NULL COMMENT 'Actual number places',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_orders_audit`;
CREATE TABLE `tl_delivery_proposal_orders_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_order_boxes`;
CREATE TABLE `tl_delivery_proposal_order_boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'DP id',
  `box_barcode` varchar(255) DEFAULT '' COMMENT 'Шк короба клиента',
  `employee_name` varchar(255) DEFAULT '' COMMENT 'Имя сканирующего',
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_order_extras`;
CREATE TABLE `tl_delivery_proposal_order_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL COMMENT 'Example: DeFacto. Internal client id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'DP id',
  `tl_delivery_route_id` int(11) DEFAULT NULL COMMENT 'DP route id',
  `tl_delivery_proposal_order_id` int(11) DEFAULT NULL COMMENT 'DP route order id',
  `name` varchar(255) DEFAULT NULL,
  `number_places` int(11) DEFAULT '0' COMMENT 'Estimated number palaces',
  `comment` text,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_routes`;
CREATE TABLE `tl_delivery_proposal_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_route_car_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL,
  `route_from` int(11) DEFAULT NULL COMMENT 'Example: DC-APORT',
  `route_to` int(11) DEFAULT NULL COMMENT 'Example: DC-APORT',
  `transportation_type` int(11) DEFAULT NULL COMMENT 'Type of transportation',
  `delivery_date` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `shipped_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `accepted_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `mc_actual` decimal(26,3) DEFAULT NULL,
  `kg` decimal(26,3) DEFAULT '0.000',
  `kg_actual` decimal(26,3) DEFAULT '0.000',
  `number_places` int(11) DEFAULT '0' COMMENT 'Estimated number palaces',
  `number_places_actual` int(11) DEFAULT '0' COMMENT 'Real number palaces',
  `cash_no` smallint(6) DEFAULT '0' COMMENT 'nal/bez',
  `price_invoice` decimal(26,3) DEFAULT '0.000',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `status` smallint(6) DEFAULT '0',
  `status_invoice` smallint(6) DEFAULT '0' COMMENT 'Invoice status: invoice not set, invoice set, invoice paid',
  `comment` text,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_routes_audit`;
CREATE TABLE `tl_delivery_proposal_routes_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_routes_car_audit`;
CREATE TABLE `tl_delivery_proposal_routes_car_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_cars`;
CREATE TABLE `tl_delivery_proposal_route_cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_city_from` int(11) DEFAULT NULL COMMENT 'Example: Astana',
  `route_city_to` int(11) DEFAULT NULL COMMENT 'Example: Astana',
  `delivery_date` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `shipped_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `accepted_datetime` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `driver_name` varchar(128) DEFAULT NULL COMMENT 'Driver name',
  `driver_phone` varchar(128) DEFAULT NULL COMMENT 'Driver phone',
  `driver_auto_number` varchar(64) DEFAULT NULL COMMENT 'Auto number',
  `mc_filled` decimal(26,3) DEFAULT '0.000' COMMENT 'Filled meters cubic',
  `kg_filled` decimal(26,3) DEFAULT '0.000' COMMENT 'Filled kilograms',
  `agent_id` int(11) DEFAULT NULL COMMENT 'Agent',
  `car_id` int(11) DEFAULT NULL COMMENT 'Agent',
  `grzch` int(11) DEFAULT '0' COMMENT 'Грзч',
  `cash_no` smallint(6) DEFAULT '0' COMMENT 'nal/bez',
  `price_invoice` decimal(26,3) DEFAULT '0.000',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'Price invoice with NDS',
  `status` smallint(6) DEFAULT '0',
  `status_invoice` smallint(6) DEFAULT '0' COMMENT 'Invoice status: invoice not set, invoice set, invoice paid',
  `comment` text,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_cars_audit`;
CREATE TABLE `tl_delivery_proposal_route_cars_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_orders`;
CREATE TABLE `tl_delivery_proposal_route_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_route_id` int(11) DEFAULT NULL,
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'Internal tl_delivery_proposal id',
  `client_id` int(11) DEFAULT NULL COMMENT 'Example: DeTacty. Internal client id',
  `status` smallint(6) DEFAULT NULL COMMENT 'Status: new, scanned, packed, etc...',
  `order_type` int(11) DEFAULT NULL COMMENT 'Order type inbound or outbound',
  `delivery_type` smallint(2) DEFAULT '0' COMMENT 'Type: RPT or Cross-dock',
  `order_id` int(11) DEFAULT NULL COMMENT 'Order id',
  `order_number` varchar(128) DEFAULT NULL COMMENT 'Order number',
  `number_places` int(11) DEFAULT NULL COMMENT 'Number places',
  `mc` decimal(26,3) DEFAULT NULL COMMENT 'M3',
  `mc_actual` decimal(26,3) DEFAULT NULL COMMENT 'Mc actual',
  `kg` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram',
  `kg_actual` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram actual',
  `number_places_actual` int(11) DEFAULT NULL COMMENT 'Actual number places',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_transport`;
CREATE TABLE `tl_delivery_proposal_route_transport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_delivery_proposal_route_id` int(11) DEFAULT NULL COMMENT 'Dp route id',
  `tl_delivery_proposal_route_cars_id` int(11) DEFAULT NULL COMMENT 'Dp route car id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'Delivery proposal id',
  `order_number` varchar(128) DEFAULT NULL COMMENT 'Order number',
  `number_places` int(11) DEFAULT NULL COMMENT 'Number places',
  `mc` decimal(26,3) DEFAULT NULL COMMENT 'M3',
  `mc_actual` decimal(26,3) DEFAULT NULL COMMENT 'Mc actual',
  `kg` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram',
  `kg_actual` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram actual',
  `number_places_actual` int(11) DEFAULT NULL COMMENT 'Actual number places',
  `order_id` int(11) DEFAULT NULL COMMENT 'Order id',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_unforeseen_expenses`;
CREATE TABLE `tl_delivery_proposal_route_unforeseen_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL COMMENT 'Example: DeFacto. Internal client id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'DP id',
  `tl_delivery_route_id` int(11) DEFAULT NULL COMMENT 'DP route id',
  `type_id` int(11) DEFAULT '0' COMMENT 'Type unforeseen expenses id',
  `name` varchar(255) DEFAULT NULL,
  `delivery_date` int(11) DEFAULT NULL COMMENT 'Delivery datetime ts',
  `price_cache` decimal(26,3) DEFAULT '0.000' COMMENT 'Price expenses',
  `cash_no` smallint(6) DEFAULT '0' COMMENT 'nal/bez',
  `price_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'C NDS',
  `status` smallint(6) DEFAULT '0',
  `comment` text,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  `who_pays` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_unforeseen_expenses_audit`;
CREATE TABLE `tl_delivery_proposal_route_unforeseen_expenses_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_delivery_proposal_route_unforeseen_expenses_type`;
CREATE TABLE `tl_delivery_proposal_route_unforeseen_expenses_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT '' COMMENT 'Name',
  `status` smallint(6) DEFAULT '0' COMMENT 'show, hide',
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_order`;
CREATE TABLE `tl_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL COMMENT 'Example: DeTacty. Internal client id',
  `route_from` int(11) NOT NULL COMMENT 'Example: DC-APORT',
  `route_to` int(11) NOT NULL COMMENT 'Example: DC-APORT',
  `delivery_date` datetime DEFAULT NULL,
  `mc` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `mc_actual` decimal(26,3) NOT NULL COMMENT 'Kilogram',
  `kg` decimal(26,3) DEFAULT NULL COMMENT 'Kilogram',
  `kg_actual` decimal(26,3) NOT NULL COMMENT 'Kilogram',
  `number_places` int(11) DEFAULT NULL COMMENT 'Number of places',
  `number_places_scanned` int(11) NOT NULL COMMENT 'Scanned number of places',
  `cross_doc` int(11) NOT NULL DEFAULT '0' COMMENT 'Cross-doc',
  `dc` int(11) DEFAULT '0',
  `hangers` int(11) DEFAULT '0',
  `other` int(11) DEFAULT '0',
  `auto_type` int(11) NOT NULL DEFAULT '0' COMMENT 'Auto type: GAZ,Iveco',
  `angar` int(11) DEFAULT '0',
  `grzch` int(11) NOT NULL DEFAULT '0' COMMENT 'Грзч',
  `total_qty` int(11) DEFAULT '0',
  `price_square_meters` int(11) DEFAULT '0',
  `price_total` int(11) DEFAULT '0',
  `costs_region` int(11) DEFAULT '0',
  `agent_id` int(11) NOT NULL COMMENT 'Agent',
  `cash_no` int(11) DEFAULT '0',
  `sale_for_client` int(11) DEFAULT '0',
  `our_profit` decimal(26,3) NOT NULL DEFAULT '0.000' COMMENT 'Our profit',
  `costs_cache` decimal(26,3) NOT NULL DEFAULT '0.000' COMMENT 'Expenses cash',
  `with_vat` decimal(26,3) NOT NULL DEFAULT '0.000' COMMENT 'C NDS',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_order_items`;
CREATE TABLE `tl_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_order_id` int(11) DEFAULT NULL COMMENT 'Internal transport logistic order id',
  `box_barcode` varchar(54) DEFAULT NULL COMMENT 'Scanned box barcode',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_outbound_registry`;
CREATE TABLE `tl_outbound_registry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL COMMENT 'Agent id',
  `car_id` int(11) DEFAULT NULL COMMENT 'Car id',
  `driver_name` varchar(255) DEFAULT NULL COMMENT 'Driver name',
  `driver_phone` varchar(255) DEFAULT NULL COMMENT 'Driver phone',
  `driver_auto_number` varchar(255) DEFAULT NULL COMMENT 'Auto number',
  `weight` decimal(26,3) DEFAULT '0.000' COMMENT 'kg',
  `volume` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `places` int(11) DEFAULT '0' COMMENT 'Places',
  `extra_fields` text,
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  `price_invoice` decimal(26,3) DEFAULT '0.000' COMMENT 'car price',
  `price_invoice_with_vat` decimal(26,3) DEFAULT '0.000' COMMENT 'car price',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_outbound_registry_audit`;
CREATE TABLE `tl_outbound_registry_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_outbound_registry_items`;
CREATE TABLE `tl_outbound_registry_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_outbound_registry_id` int(11) DEFAULT NULL COMMENT 'registry id',
  `tl_delivery_proposal_id` int(11) DEFAULT NULL COMMENT 'dp id',
  `route_from` int(11) DEFAULT NULL COMMENT 'store id from',
  `route_to` int(11) DEFAULT NULL COMMENT 'store id to',
  `weight` decimal(26,3) DEFAULT '0.000' COMMENT 'kg',
  `volume` decimal(26,3) DEFAULT '0.000' COMMENT 'Meters cubic',
  `places` int(11) DEFAULT '0' COMMENT 'Places',
  `extra_fields` text,
  `created_user_id` int(11) DEFAULT '0',
  `updated_user_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tl_outbound_registry_items_audit`;
CREATE TABLE `tl_outbound_registry_items_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Modified object id',
  `date_created` datetime DEFAULT NULL COMMENT 'Modification timestamp',
  `created_by` int(11) DEFAULT NULL COMMENT 'Modified user_id',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Modified object attribute name',
  `before_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute before modification',
  `after_value_text` varchar(255) DEFAULT NULL COMMENT 'Value of attribute after modification',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `user_id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  UNIQUE KEY `token_unique` (`user_id`,`code`,`type`),
  CONSTRAINT `fk_user_token` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `transportation_order_lead`;
CREATE TABLE `transportation_order_lead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `delivery_type` int(11) DEFAULT NULL,
  `delivery_method` smallint(6) DEFAULT NULL,
  `from_city_id` smallint(6) DEFAULT '0' COMMENT 'City from',
  `customer_name` varchar(128) DEFAULT NULL COMMENT 'Contact name',
  `customer_phone` varchar(128) DEFAULT NULL COMMENT 'Phone number',
  `customer_phone_2` varchar(128) DEFAULT NULL,
  `customer_street` varchar(128) DEFAULT NULL COMMENT 'Address',
  `customer_house` varchar(255) DEFAULT NULL,
  `customer_apartment` varchar(255) DEFAULT NULL,
  `customer_floor` varchar(255) DEFAULT NULL,
  `to_city_id` smallint(6) DEFAULT '0' COMMENT 'City to',
  `recipient_name` varchar(128) DEFAULT NULL COMMENT 'Recipient name',
  `recipient_name_2` varchar(255) DEFAULT NULL,
  `recipient_phone` varchar(128) DEFAULT NULL COMMENT 'Recipient phone number',
  `recipient_phone_2` varchar(255) DEFAULT NULL,
  `recipient_street` varchar(128) DEFAULT NULL COMMENT 'Recipient address',
  `recipient_house` varchar(255) DEFAULT NULL,
  `recipient_apartment` varchar(255) DEFAULT NULL,
  `recipient_floor` varchar(255) DEFAULT NULL,
  `places` smallint(128) DEFAULT NULL COMMENT 'Number of places',
  `customer_comment` varchar(255) DEFAULT NULL COMMENT 'Comment',
  `weight` decimal(26,3) DEFAULT NULL,
  `volume` decimal(26,3) DEFAULT NULL,
  `declared_value` varchar(128) DEFAULT NULL,
  `cost` decimal(26,2) DEFAULT '0.00' COMMENT 'Pre cost of delivery',
  `cost_vat` decimal(26,2) DEFAULT '0.00' COMMENT 'Pre cost of delivery with vat',
  `package_description` varchar(128) DEFAULT NULL,
  `status` smallint(6) DEFAULT '1' COMMENT 'Status',
  `source` int(11) DEFAULT NULL,
  `order_number` varchar(255) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `transportation_tariff_company_lead`;
CREATE TABLE `transportation_tariff_company_lead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(128) DEFAULT NULL COMMENT 'Contact name',
  `customer_company_name` varchar(128) DEFAULT NULL COMMENT 'Company name',
  `customer_position` varchar(128) DEFAULT NULL COMMENT 'Position',
  `customer_phone` varchar(128) DEFAULT NULL COMMENT 'Phone number',
  `customer_email` varchar(128) DEFAULT NULL COMMENT 'Email',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status',
  `cooperation_type_1` smallint(6) DEFAULT '0' COMMENT 'Type: one-time',
  `cooperation_type_2` smallint(6) DEFAULT '0' COMMENT 'Type: contract-based full',
  `cooperation_type_3` smallint(6) DEFAULT '0' COMMENT 'Type: contract-based',
  `customer_comment` varchar(255) DEFAULT NULL COMMENT 'Comment',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` smallint(4) DEFAULT NULL COMMENT 'User type: operator, client, shop owner, etc ',
  `username` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique_email` (`email`),
  UNIQUE KEY `user_unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE `warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL COMMENT 'Country',
  `region_id` int(11) DEFAULT NULL COMMENT 'Region',
  `city_id` int(11) DEFAULT NULL COMMENT 'City',
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `zip_code` varchar(9) NOT NULL,
  `street` varchar(128) NOT NULL,
  `house` varchar(6) NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `user_type`, `username`, `email`, `password_hash`, `auth_key`, `confirmed_at`, `unconfirmed_email`, `blocked_at`, `role`, `registration_ip`, `created_at`, `updated_at`, `flags`) VALUES
(2,	2,	'test01',	'kitavrus@ya.ru',	'1$2y$12$YjlNSFFYuWyH20jHT4eMz.XFt1.P7V9cVHTVhu1rZBX6ssJ.U8sL',	'v9nkPSZs9ABZM2VsbrRXpIaTxZFXOHPO',	1406365392,	'',	NULL,	'',	'127.0.0.1',	1406365133,	1442165992,	0),
(3,	2,	'Aualiev',	'aualiev@nomadex.kz',	'$2y$12$AFHlAYrIF3M2sCUm/CPlce6kJanLFzj/WwkRrW9Fb.5oCIxU2iizy',	'rIAB0YKk01Dvljzh8MFf3L9zimV9oMy6',	1411532599,	'',	NULL,	'',	'',	1411532599,	1528874749,	0),
(4,	2,	'Tmadaliev',	'tmadaliev@nomadex.kz',	'1$2y$12$7HBN/JMdPNQjjJcIjDY69.712oovWUz7XCEcbP..EJI8QeTAXv4d',	'kSOKD6Txr5E-__AVKjddYMsAxRtNRTzF',	1411532834,	'',	1463451783,	'',	'',	1411532834,	1463451783,	0),
(5,	1,	'test-defacto',	'azamat.zholdasbekov@gmail.com',	'$2y$12$ZxMD3kw2C6EL6kyrWsuFd.ej7tskhbjeWxQI.IGkIJjz2bmYjsDMG',	'r2eU3SLgbJHNTN2Ua9nwZOxu6hPHB95L',	1415125759,	NULL,	NULL,	NULL,	'91.200.234.60',	1412694666,	1442165991,	0),
(6,	2,	'Snurgalieva',	'finance@nomadex.kz',	'$2y$12$BeHTr5PGmr2J4J7om.R8NeHW9XsUiMIeyQRaY09SlbBoWAzizrvsm',	'GDs0NmqkZT4nHfelVSAG2mQJXpyB3O6b',	1412830065,	'',	NULL,	'',	'92.47.206.194',	1412830066,	1473739881,	0),
(7,	1,	'Colins_NOT_USED',	'mussinzhanat@apis.kz',	'-123',	'6x',	1413865746,	NULL,	1430791849,	NULL,	'92.47.253.129',	1413865746,	1444065242,	0),
(8,	1,	'Azamat',	'azamat.zholdasbekov@defacto.com.tr',	'$2y$12$nSUCdiSYdkod/BJzpXkcRueAXeF9wrNk3qgUFV3i/DYe9xow/lJZC',	'nymJwdG8vwRi4FRn3m-Kfn3SdUWyIEUJ',	1413865790,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865791,	1444065179,	0),
(9,	1,	'Nomadex',	'Nomadex_@ya.ru',	'$2y$12$DME4PdJr5aibH8i3f6nBeOBhUOCTe0kTdgaGye5KJJxCJd6m2ltqa',	'YDxHGFi7_NpEymOyBc8SGI4GrW6LxjNc',	1413865914,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865915,	1442165991,	0),
(10,	1,	'Sharuakaz',	'Sharuakaz_@ya.ru',	'$2y$12$/f9UjRTNUAjCqE4kyoM.gOcHpm2aiH77UQ9nxxKIKk5kqaheeyn7y',	'sHgXce3o4OIuJJ6elGuZADVRDZj3d2gn',	1413865955,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865956,	1454324471,	0),
(11,	1,	'Integra',	'Integra_@ya.ru',	'$2y$12$b2cF0pYU933qTv/GQBSynuxBAJN7bVcrXqNK.MZGJ.oYGxi.HfjHm',	'TFoMUw4l8nxkBkOkCmZuVzRt51B3HH2r',	1413865997,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865998,	1442165991,	0),
(12,	1,	'almaty-aport-mall',	'Magaza.Kzk_Almaata@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'GdKUhQzEzYlHvKIRaVOzVqNPBzZ-2Mko',	1414058692,	NULL,	NULL,	NULL,	'92.47.217.170',	1414058692,	1533332191,	0),
(13,	1,	'karaganda-bukhar-zhyrau-str',	'Magaza.Kzk_KaragandaCadde@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'BrqdXJ5QnTjlkIPC_B8TdhQ9RfJ8vygE',	1414576908,	NULL,	NULL,	NULL,	'193.193.241.238',	1414576909,	1533332196,	0),
(14,	1,	'almaty-mart-mall',	'Magaza.Kzk_Mart@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'varPoj65NakZ8Vvv50ZrQs6Z0iAOuXD4',	1414648550,	NULL,	NULL,	NULL,	'92.47.250.76',	1414648551,	1538739063,	0),
(15,	1,	'almaty-adk-mall',	'magaza.Kzk_ADK@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'b_iVJvI9vnECT-4kCualkXntBA2IiiuK',	1415124854,	NULL,	NULL,	NULL,	'37.99.53.197',	1415124855,	1542124498,	0),
(16,	1,	'Anuar',	'iCgB9w_q@old-demo-mail.kz',	'$2y$12$X7bs5VYVGWGOj8lLT2jLYO2eJFenEtzrgRh6e2M/kjFU/iO8gomWe',	'BKeg5hOrsDdI9pmLwxdNVoK6jNx0ESbr',	1415124990,	NULL,	1440518038,	NULL,	'37.99.53.197',	1415124991,	1533332188,	0),
(17,	1,	'almaty-mega-mall',	'Magaza.Kzk_Mega2@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'e0TwSbC3vpey_hkDP36rLCtQORC6BxGc',	1415125305,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125305,	1533332190,	0),
(18,	1,	'shymkent-bayansulu-mall',	'Magaza.Kzk_Cimkent@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'BLbjZjVFw0LT5qQmFfWGFA9tyt4QiWHM',	1415125548,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125549,	1544175483,	0),
(19,	1,	'oskemen-adk-mall',	'Magaza.Kzk_Oskemen@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'KqW-QZpTP16VrCvVnNgzdI5d6A8oum_M',	1415125587,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125588,	1533348316,	0),
(20,	1,	'karaganda-tair-mall',	'magaza.kzk_karaganda@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'NoyoNV_mBWwRASUwbdQwmw9vVny_U0y2',	1415125643,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125644,	1539775184,	0),
(21,	1,	'aktau-aktau-mall',	'Magaza.Kzk_Aktau@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'wFMYylyPcv78BLf4yaeyHWe5Pfy6skXI',	1415125651,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125651,	1545025140,	0),
(22,	1,	'atyrau-atyrau-mall',	'Magaza.Kzk_Atrau@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'zNFUgfvbpuem-xdWPRs8Ps9BMRvicEr_',	1415125657,	NULL,	NULL,	NULL,	'37.99.53.197',	1415125658,	1537502997,	0),
(23,	1,	'NursultanY',	'5kP-w5Tp@old-demo-mail.kz',	'$2y$12$3fdXwE9se.2RnaXvcr.9.uw1FUoGZcpRU/SD9kh69aEK4lmsf174C',	'xxUkue44mCuZF5pLBsieJWG19pIDn-K-',	1415167498,	NULL,	NULL,	NULL,	'213.157.54.78',	1415167499,	1533332188,	0),
(24,	1,	'Dana',	'_O1ZrSOF@old-demo-mail.kz',	'$2y$12$4qPb6J9Y8SaTxF5RfyKeAuJADUctRambrnAyJaSXyMNyUkzJmmFA6',	'TEqEBYGzn6t_Z33DpVhwt-sy4DHIojys',	1415168086,	NULL,	1424788808,	NULL,	'213.157.54.78',	1415168087,	1533332188,	0),
(25,	1,	'Kuanysh',	'1cY8FcJa@old-demo-mail.kz',	'$2y$12$Jss8Nzm5zICsrUKnNB9FguBABPNNKr53TUPYUWfR8S6Oc7i5vL4Wu',	'rhkpyozrpDj51GBkVG2Hy-f3ysAkF3d4',	1415180041,	NULL,	1416127241,	NULL,	'89.218.61.218',	1415180041,	1533332188,	0),
(26,	1,	'Nishan',	'Xjbay8He@old-demo-mail.kz',	'$2y$12$Qkfp6oLeoC9PACyI2YbJ1.bww3xdfiC.6xGABIuYlww7P1JZRS842',	'dkc34OSdOmWHgd0olNi-iPMzPBa3BIbT',	1415183192,	NULL,	1421077902,	NULL,	'91.185.31.76',	1415183193,	1533332188,	0),
(27,	1,	'almaty-mega-park-mall',	'Magaza.Kzk_Mega3@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'5fsRHZjsjcnAAMgF6ogFKBpn5JdKxvGr',	1415185007,	NULL,	NULL,	NULL,	'91.185.31.76',	1415185008,	1533332189,	0),
(28,	1,	'MEGA2',	'SWTg08Vw@old-demo-mail.kz',	'$2y$12$s4bQuw/nvRXnq.gschfrx.gpEUJockrR7YMYD2GbZMLW7ID7EjwAG',	'QjrDdoMCji2PXEg_eMWNvMWoPMWoO8EC',	1415194134,	NULL,	1533332190,	NULL,	'80.241.41.138',	1415194135,	1533332190,	0),
(29,	1,	'Bake',	'aE4RioDd@old-demo-mail.kz',	'$2y$12$Zg1j4nRkAeQeLemqGDKjP.1AyUbNDBb24VhgsOp59SmhtfokGLes2',	'9E_QNs0QRqkzpBGDPwG4TNz98qPwr05Q',	1415276857,	NULL,	1476097393,	NULL,	'80.241.41.138',	1415276858,	1533332188,	0),
(30,	1,	'Eomurzakov-Blocked',	'eomurzakov@nomadex.kz',	'123-',	'-123',	NULL,	NULL,	NULL,	NULL,	'178.214.195.190',	1415479556,	1442165992,	0),
(31,	1,	'observer',	'observer-test@ya.ru',	'$2y$12$rwJfhyqqC5euvJ9Pe1WBV.BA9DHxlAHpQGi/uMY7yxBtCQfmxFwkC',	'XsSSufH2fj5TEQjkz8Arp28xH3naQ21p',	1415768433,	NULL,	NULL,	NULL,	'37.150.8.91',	1415768433,	1442165991,	0),
(32,	1,	'Rauana',	'JQt0Riyl@old-demo-mail.kz',	'$2y$12$O5KZ7.i91U2FXs/mKeq2ZujdX3WuU3cmMTV/YvMuamh13U6Jft/D6',	'9Nf3xU-U2FT6ZwWmozjL1nhrdtAOtvp7',	1415784568,	NULL,	1491814437,	NULL,	'178.89.233.138',	1415784569,	1533332188,	0),
(33,	1,	'TlekSh',	'_Av6_fSh@old-demo-mail.kz',	'$2y$12$FZ2m6U5A72JFqFb.xsQH/eM0ipMTcbx3y5qeXZyXY9SDHpFlMMnIe',	'eWYQs5IA3KZcMIQUf9sJrxPghEWXxC5r',	1415802457,	NULL,	1533332197,	NULL,	'91.185.31.76',	1415802457,	1533332197,	0),
(34,	1,	'Alfiya',	'JXODREXU@old-demo-mail.kz',	'$2y$12$BBJtm7INUdiu37s5mC88PuKdmIofgMQYbHARIWo2tXwgmcxFOEawa',	'B85XolUYlaAqZdKRyYn4quwTqO2Ej9IF',	1415878502,	NULL,	1443507408,	NULL,	'178.89.233.138',	1415878503,	1533332188,	0),
(35,	1,	'Olzhas',	'wr9VfBoy@old-demo-mail.kz',	'$2y$12$ORGgIb144IlNmBHm48FMeuvUxyzxZbB.NDTXHed5Y0ThfLEXz0iSu',	'EItyPxgvk_9CUD4NkDbL5oZXdtmpDsW0',	1415939253,	NULL,	1454055523,	NULL,	'92.47.33.66',	1415939254,	1533332188,	0),
(36,	1,	'Adlet',	'srBS1H0_@old-demo-mail.kz',	'$2y$12$3oD5FmaDy76KkxemR.2ww.7wPEp8X8cWGwMbVlk5SMlSc7pZ7aLoi',	'fUCpDIbFMmpIx5DVqP3Oi1hbu5VEKiky',	1415942439,	NULL,	1421062907,	NULL,	'31.31.219.110',	1415942440,	1533332188,	0),
(37,	1,	'Malika',	'cGvWwLam@old-demo-mail.kz',	'$2y$12$NOLDLq2RUtfLczpqtaSB.uBI3bd82Tk0Oixa4FXwZ5XaoBW.GX8A.',	'xhwpolwieFaEq9UQYyuLaXvgqiGHfG1N',	1416127328,	NULL,	1420298054,	NULL,	'89.218.61.218',	1416127328,	1533332188,	0),
(38,	1,	'Alfiya2110',	'RTPoXA0k@old-demo-mail.kz',	'$2y$12$V6g1O2zDOVyld24wD2y9OeC2ZvAd3RmTajwIrSm4/AjIyomoSF0hi',	'j9uB1jpsOuJv0-iCwcTkLtaiLhr22laB',	1416375319,	NULL,	1422891501,	NULL,	'89.218.61.218',	1416375319,	1533332188,	0),
(39,	1,	'Madiyar',	'nMk4YaNt@old-demo-mail.kz',	'$2y$12$hQ0U7vtmLuv43qIFfFiFU.fpZkdPW2vIo6yCuLWZhIjIYpaW8MiPa',	'MlOkTN4QgL-EcKy5tlhS1rXwPNXAPvHu',	1416714239,	NULL,	1419059185,	NULL,	'91.185.31.76',	1416714240,	1533332188,	0),
(40,	1,	'Syrtan',	'syrttan1@hotmail.com',	'$2y$12$JJ8Lr7A/R3z31AgrchVba.fHIfUR6sj88iza.nyEYYynBmd5aVDqm',	'UgbnshOHggluVDRL7-qW9Gmnwg9Wki5F',	1416800773,	NULL,	NULL,	NULL,	'193.193.241.238',	1416800773,	1419391143,	0),
(41,	3,	'scaana-base',	'scaana-base@ya.ru',	'$2y$12$BrFTXIBH29IWo0uMRy9BwOsrM.vEfWGP0gDgiMqxuaR50SaPIQRN2',	'lAzhJAecI1Seo61ug1hHgXBrVL_KMvPW',	1416896669,	NULL,	NULL,	NULL,	'37.150.8.91',	1416896670,	1416896670,	0),
(42,	1,	'ADIL',	'ADIL@NOMADEX.KZ',	'$2y$12$2NnpknCVD3oRHtZV697TgOi.VtGEGwClKzrsB5BDEz6m0qS4fKCCi',	'YgrgGqehhxCMlFzHT3srpNVcnxyJ9Mpo',	1416912038,	NULL,	NULL,	NULL,	'193.193.241.238',	1416912039,	1416912039,	0),
(43,	1,	'Bank',	'BANK@NOMADEX.KZ',	'$2y$12$zH7ZXjJg4JgV6wj0hhCX1ObSz3q.LmfeaR9/CCh09TRr3rH18BsuW',	'hEt1s04xmefH0FuS4r9lHwDp6Z9UVlVN',	1416912670,	NULL,	NULL,	NULL,	'193.193.241.238',	1416912670,	1442202458,	0),
(44,	1,	'zhassulan',	'C2fRmEoQ@old-demo-mail.kz',	'$2y$12$SvkcFRbwzZGM6vL9JCFy.OjSYTFu2pkg5c9W2pp/3upu9404HJXWa',	'ibQNx1SYY6FKjZI9KBindwR9UJ5RaVh1',	1417774309,	NULL,	1443507473,	NULL,	'88.204.247.134',	1417774310,	1533332188,	0),
(45,	1,	'pavlodar-batyr-mall',	'Magaza.Kzk_Pavlodar@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'uPx_JyKxO1maf7nVKdgNlO2dSAaFGPqx',	1418040856,	NULL,	NULL,	NULL,	'84.240.240.230',	1418040856,	1533391631,	0),
(46,	1,	'Lena',	'S4rHOsEE@old-demo-mail.kz',	'$2y$12$ZwdSC5XED54.jl/klsbJqetA0T.WJ.jEZXl5pcWmNuiLoI3Woi8k.',	'y1B_jAVZxRKZ7PdcL1vC1-6LvC_MQm0l',	1418119610,	NULL,	1440306527,	NULL,	'91.185.31.76',	1418119610,	1533332188,	0),
(47,	1,	'Nurlan',	'BBDcz4iP@old-demo-mail.kz',	'$2y$12$IOCdcgiezBXpsHKT.J4BTO2nq8H1BvqjY1cSBaf.zk4y9k464aFYq',	'uyi-1zAng0sDwdD8RwWBtrm46_-pqifc',	1418218784,	NULL,	1424699487,	NULL,	'31.31.219.110',	1418218785,	1533332188,	0),
(48,	1,	'Bakytzhan',	'3FzL2bhW@old-demo-mail.kz',	'$2y$12$xdARi.gDrsp3UdGyxCev4eN5cGtH1xipaY.9e2szve9gL76Ek67Iy',	'Tg-mU2_LpzaF8GYHYZhaztL_Em-6agYb',	1418218860,	NULL,	1432482473,	NULL,	'37.150.213.30',	1418218860,	1533332188,	0),
(49,	1,	'Erlan',	'Me4Db0v6@old-demo-mail.kz',	'$2y$12$rYhSGxcXwb9jax8OdFP6TOjguymwB/j4qI2PQA.9dzdQh1mhvtuVa',	'IuGvUl5IwETEq3h3ZGcij_XTNcC75YPM',	1418284680,	NULL,	1421077905,	NULL,	'91.185.31.76',	1418284681,	1533332188,	0),
(50,	1,	'ObserverN',	'observer-n@ya.ru',	'$2y$12$ISlUx55H4YYHsxL44LDupOJn6vtirqSS5kyhn.3gvl.vfeYepyTE2',	'VEl7GsGU2RklHbVFQyAq8LMZE52AAeBo',	1418697954,	NULL,	NULL,	NULL,	'85.29.184.65',	1418697954,	1442165991,	0),
(51,	1,	'Bztaie',	'Bzt@WMS.KZ',	'$2y$12$1huNhyscE.FVgY0EPbu3vumQ3sG/dG25hbE4a8TKte0.iPvnO9f42',	'HRQk6Bbxa0t1otn0W6-TpFgyhJ-0Jv1T',	1419226219,	NULL,	NULL,	NULL,	'37.150.162.150',	1419226219,	1442202473,	0),
(52,	1,	'A-sysIngeering',	'A-sysIngeering@WMS.KZ',	'$2y$12$Sne34EUgXLueKk4MwMUY0Oe8daNQKuDTiKk3cuvjilViE9jbTEo3u',	'MrCpjsWEbtxxhZjVK2wP-anpDijAjUwD',	1419226434,	NULL,	NULL,	NULL,	'37.150.162.150',	1419226435,	1419391292,	0),
(53,	1,	'BankRBK',	'BankRBK@WMS.KZ',	'$2y$12$Y.pu9hX7XBOedySUxqONi.s9QpMUU8dzxI8W8Vs6fFhYGWzTHPI12',	'OWhjSEeZ9i8RAVYCILq1OfNaamAIX-ei',	1419227570,	NULL,	NULL,	NULL,	'193.193.241.238',	1419227570,	1442202487,	0),
(54,	1,	'CAN_BESBICAK',	'demo-besbicak@mail.com',	'$2y$12$eIkd3l6DSBbmbr/ekW.Bfu.6Ox2F/LJ42E1iKT4nT1zTSD3q1APuy',	'IFMlCVoc9YwzvUFs8PeOfXPwBI2gqs7V',	1419351774,	NULL,	NULL,	NULL,	'5.34.92.5',	1419351775,	1419351775,	0),
(55,	1,	'Kcell',	'kcell@wms.kz',	'$2y$12$r9NAmORj6YZYPQ.M00D9LObAIysXXrMGwMBxjVznFTbcbsID0Hquq',	'Iu_4Czxf-AwErTNZBC1vUVsWOgdDZ_w2',	1419945303,	NULL,	NULL,	NULL,	'193.193.241.238',	1419945303,	1419946487,	0),
(56,	1,	'nizamov',	'OWif-qBk@old-demo-mail.kz',	'$2y$12$rTXhyT7swbLX78muiocEkuACOLksbjXp65oevXc5NTV7iVm1wbb9i',	'ZyQWa5ZI5Yz7gpKBazBHOMhRT1e0GNdG',	1420178925,	NULL,	1420714402,	NULL,	'84.240.240.230',	1420178926,	1533332188,	0),
(57,	1,	'MusaDemirok',	'musa.demirok@defacto.com.tr',	'$2y$12$s.hO8LhELUv4.fzHmqYRwetlJkug09On4LKsFbdFoHBIimK3Q0BFe',	'kMzIqn8RDlPRIrSEOCIj1bs481uiOFBC',	1420525911,	NULL,	NULL,	NULL,	'37.150.8.91',	1420525912,	1442165991,	0),
(58,	1,	'Anara',	'koM4yaSR@old-demo-mail.kz',	'$2y$12$MG983OACIeMDfXw8NnDJwuf6TXaKc9dRO7nS1o2xlu9njNvKKF3v6',	'-YduxwzaGKTUtPWLAmC4cZ6VSukd4wjN',	1421077993,	NULL,	1426683950,	NULL,	'91.185.31.76',	1421077993,	1533332188,	0),
(59,	1,	'astana-asiapark-mall',	'Magaza.Kzk_AsiaPark@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'QlM57wi6oK-SEA8AAVzuHd3ab36ljIk6',	1421144277,	NULL,	NULL,	NULL,	'217.76.74.212',	1421144277,	1533332188,	0),
(60,	1,	'iskender',	'sno4SBGo@old-demo-mail.kz',	'$2y$12$gMjIbSogJ/o3uFJcOMIM2uycADr1XHfcuP0kWVXxIWVjeuVTWFgTi',	'qeIDVyA5_FoVPfk8glqCLnv2sHr2LLjO',	1421487723,	NULL,	1425552049,	NULL,	'91.185.31.76',	1421487723,	1533332188,	0),
(61,	1,	'Mete',	'9VWjUBb_@old-demo-mail.kz',	'$2y$12$beXPd5QnWH7yAT0rmK0Cm.4sH2I2NkumTZ9yYySfIPaxP1KgNe0IC',	'd1HHcLld0f1VuKeOKW4aLz5on4r_Do6k',	1421746830,	NULL,	1422899011,	NULL,	'31.31.219.110',	1421746830,	1533332188,	0),
(62,	1,	'almaata-sputnik-mall',	'Magaza.Kzk_Sputnik@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'V7HpVMr-ej8Bxv5uvCureeRACY5o8we2',	1421750255,	NULL,	NULL,	NULL,	'80.241.41.138',	1421750256,	1547541700,	0),
(63,	1,	'DEFAULT_COMPANY',	'tese323t@test.com',	'$2y$12$KZDm9L9mRyB.JptCWqhMfen8Dfkw0VdlG0PE.0batxAkSotfpBcxq',	'COLq9-ZVqH_WLQT1Kw27nE8ooc3W9rL1',	1421951782,	NULL,	NULL,	NULL,	'91.200.234.60',	1421951782,	1442165991,	0),
(64,	1,	'Ernaz',	'LA7vKtFo@old-demo-mail.kz',	'$2y$12$t/ZZXjgMGqr3hZCMosSuC.CEXCZGnIT3pwhHrR4vsmf800gJtqQ2y',	'XV56EKujoQhnFS91y6NLArrhP_fCY2Jc',	1423997475,	NULL,	1523099548,	NULL,	'88.204.254.190',	1423997476,	1533332188,	0),
(65,	1,	'Ozer',	'jQj28q8Y@old-demo-mail.kz',	'$2y$12$x0hQ9WQWLZrujIiJ9NNtXubqlqI/vu0YYjuvrY9VLELAZaYv8HTZS',	'_6SFAldXQ8VnC5m1yrkDj4jZ59dVsz5M',	1424788953,	NULL,	1448202380,	NULL,	'213.157.54.78',	1424788954,	1533332188,	0),
(66,	1,	'MeteSevim',	'DDOUMb2r@old-demo-mail.kz',	'$2y$12$oDJioBqd7lIjLVku.DsLw.TzR5MslJcPtrGs61CrPbrCM7.rgbRNi',	'oZmNhasFrkd-j9eDo3xLc-TQ-zcSKCxE',	1425559167,	NULL,	1459436791,	NULL,	'193.193.241.238',	1425559168,	1533332188,	0),
(67,	1,	'DEFAULT_PERSON',	'DEFAULT_PERSON@dummy.com',	'$2y$12$bE.MXrC8nmOnnze3uU3nPOVgrsUyWPktcAGSxS.biWCGz2clnYjMC',	'EpbVT7qvqo2H_v5xWY53gIBb84s-hab-',	1425913529,	NULL,	NULL,	NULL,	'91.200.234.60',	1425913530,	1442165991,	0),
(68,	1,	'0932939050',	'0932939050@dummy.com',	'$2y$12$KrLUZmxg0PdwdOmrF3br9utYn1kqoh047khChZMmoO9rqwJ1enKOC',	'kq1cE8AhqiXzXEEvxaHE_ER-51JJMsd6',	1425915360,	NULL,	NULL,	NULL,	'91.200.234.60',	1425915361,	1442165991,	0),
(69,	5,	'point_operator',	'point_operator@mail.ru',	'$2y$12$n64Lti3qAeEXy6GSh5z2iezlOFcpUN7m83lkX0MK5xQMDcu.l7MQ1',	'bb_tKJqyXM9XOdIjyYEuDqbcZEeEhNSE',	1425916390,	NULL,	NULL,	NULL,	'91.200.234.60',	1425916391,	1442165991,	0),
(70,	1,	'0932939051',	'0932939051@dummy.com',	'$2y$12$xf5O4IZj83.woUnbpasezun2aAzvuHfxU1Uhlx4BTsKIQxqMABPHe',	'rYqiZL66r6g8l6u8ryjpWltHTWqERmJJ',	1425916738,	NULL,	NULL,	NULL,	'91.200.234.60',	1425916738,	1442165991,	0),
(71,	1,	'0932939062',	'0932939062@dummy.com',	'$2y$12$qco0l4i811DludX3XFYGUORwlNCvgEmzcBTRoE7C7zrj77S/Pr7NC',	'XcSUT1UcL6qFZcCydRR__CnX5q3QrkEV',	1425917738,	NULL,	NULL,	NULL,	'91.200.234.60',	1425917739,	1442165991,	0),
(72,	1,	'atyrau-kulmanova-str',	'Magaza.Kzk_AtyrauCadde@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'ChALT14RuB0KpMdiy_wn62jEXhm6AfYg',	1426164049,	NULL,	NULL,	NULL,	'91.185.31.76',	1426164049,	1533332194,	0),
(73,	2,	'Asanov',	'test@01.ru',	'1$2y$12$qZCP/Ii1vGUwsJyH/6Ow1eKxW7/46Uzti01exnbSCI8Uuv6GVh23',	'XKqwbHUgV4WH-Rh-_luqMhqjlesz37KU',	1426478456,	'',	NULL,	'',	'193.193.241.238',	1426478457,	1442165991,	0),
(74,	2,	'Alimbetov',	'test@02.ru',	'1$2y$12$45nmoD/9f6pg..OD7pi4muSDU.fPeC2WYdg74WtKHN1NWmPpqo.s',	'rv4vvu8-a53WHs18xXLNZQf1gLIyv4l8',	1426478922,	'',	NULL,	'',	'193.193.241.238',	1426478923,	1442165991,	0),
(75,	2,	'Baimuhashev',	'test@03.ru',	'1$2y$12$ISKivl.eSSqFs17fEgzUMesna3DB3TB49fJeyfdzZA.x9vnJQs5Q',	'8uRIBevEIDeeYCpYm2g89tP8vs4PaBWt',	1426479128,	'',	NULL,	'',	'193.193.241.238',	1426479129,	1442165991,	0),
(76,	2,	'Andabaev',	'test@04.ru',	'1$2y$12$7ihRHCuUnZvuU/UANRZ6j.yDrdX4Iy276ZmZvcOzDRL5ZiMouHxm',	'QqDM4myz_hIzlS5t2h11ThwWbpP-QpbD',	1426479258,	'',	NULL,	'',	'193.193.241.238',	1426479258,	1507802705,	0),
(77,	2,	'Sydykbekov',	'test@05.ru',	'1$2y$12$0TEbxmmizevVE0q3hdW7WepyRORhpAYQYHUkMVQMEBA6U6/n3NVF',	'E-QF7tK4Dje6t_vw-ZyFkdhuMfvTNUP9',	1426479427,	'',	NULL,	'',	'193.193.241.238',	1426479427,	1462252932,	0),
(78,	2,	'Kapalbaev',	'test@06.ru',	'1$2y$12$XlUcDp2OF3doY1RS2JQqfenSGcJJAunDyqYtxFbf9bxq4r7Ah1nU',	'FxHvVu0g8ASGEtzAoMczvr4w_Q0PJBsg',	1426479597,	'',	NULL,	'',	'193.193.241.238',	1426479598,	1442165991,	0),
(79,	2,	'Sapiev',	'test@07.ru',	'1$2y$12$I9Lnjj/hHl7ftfrdS./EbOztadjveOJOHD0rmeFRmlujwYJmWJYo',	'DWGnJUMKiWGjkB6U_nQu8REQZOnCc0zr',	1426479746,	'',	NULL,	'',	'193.193.241.238',	1426479746,	1442165991,	0),
(80,	2,	'Orazbaev',	'test@08.ru',	'1$2y$12$VqpRioBdeZIHceXzP4Q6guPxDtnV1kO0VyesQRoBuZGtUA1c5ILe',	'3X7RfQKfk_zNa_lhvpMgllI9DBAMIGSC',	1426479926,	'',	NULL,	'',	'193.193.241.238',	1426479927,	1442165991,	0),
(81,	2,	'Kapalbaeva',	'test@09.ru',	'1$2y$12$37C1TJbaP.UhPXiyMDmozOViBTLULA119330bhYHL0SD.I1vpReE',	'h5MOhju3Bjf3SmAnfHpWZK4DZ4tvV5Mo',	1426480104,	'',	NULL,	'',	'193.193.241.238',	1426480105,	1507802912,	0),
(82,	2,	'Kudaibergenov',	'test@10.ru',	'1$2y$12$OkX9QviwdDUW1CDwJzWNl.eP2FU12raQjjbH9GQJoTuzNf1n8ryR',	'Lv5cA1pxkDm5iCowIZY9U_6HUk3y9Woc',	1426480275,	'',	NULL,	'',	'193.193.241.238',	1426480276,	1487304217,	0),
(83,	1,	'aktobe-keruen-city-mall',	'Magaza.Kzk_Aktobe@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'BFXIPzWJ9iZIZTGCLqLd98CTL1BZMbW9',	1426494274,	NULL,	NULL,	NULL,	'193.193.241.238',	1426494275,	1537168036,	0),
(84,	1,	'Tlek',	'PdgFnDHE@old-demo-mail.kz',	'$2y$12$Cq3RLkJMo0cPdRFjECDfpO9shmTlVYB7J2Fa/DLDCrNjG2mH6kTHS',	'y6GymaApfFxT-_-mj37Suio6HCKFLfRA',	1427361691,	NULL,	1453432394,	NULL,	'37.150.213.30',	1427361691,	1533332188,	0),
(85,	1,	'GGkazakhstan',	'Tmadaliev1@nomadex.kz',	'$2y$12$Xzn7iN9Dw9c/U1sq8rdXKuuwU7WRFvVdLcIJ8BVQLtWRlCxXL5HIK',	'e1fjHfPm9XS3kgzXiMLFcWOTElVKeswS',	1427690061,	NULL,	NULL,	NULL,	'193.193.241.238',	1427690062,	1442165991,	0),
(86,	1,	'HorozElektrik',	'Horoz@nomadex.kz',	'$2y$12$qC5tidX/KOG4vNCzpUMQSOSkO47iGMwk8BBd5TBLuE0Lj9zkgGtLy',	'4__bOCmdZfNGJGFJ8bNUxc3fw2gGKbN_',	1428375233,	NULL,	NULL,	NULL,	'217.76.76.87',	1428375234,	1442165991,	0),
(88,	1,	'Baurzhan',	'uiM9nPMy@old-demo-mail.kz',	'$2y$12$eIyPlWGdUsTyXZLNittT.eBCiNjxzgMDUGChTF0ZncVBfGnmAutwS',	'cHt7ksHwSCMxPRpQoxYIQH6i886gJdiZ',	1429529811,	NULL,	1452854246,	NULL,	'91.185.31.76',	1429529811,	1533332188,	0),
(89,	1,	'keremet',	'Keremet@Nomadex.kz',	'$2y$12$a/UfWvrplBdf41oO/NYRfO3EoNd4/fFWgI1WaUP.KLvHMyrQOJbfG',	'XnGBhmx21P8fCKHhDmWG6DBFu7-9VfKF',	1430211684,	NULL,	NULL,	NULL,	'193.193.241.238',	1430211684,	1442165991,	0),
(91,	1,	'0637843987',	'0637843987@dummy.com',	'$2y$12$sjlnBChGXEbxOq0RB.Zd4uzLBRWomIikzVs9D59TJt0dQJhJZSC8G',	'UqBWdLkF9aN7R-23c-pg4ntIaBsA8nqc',	1431534453,	NULL,	NULL,	NULL,	'195.66.141.35',	1431534454,	1442165991,	0),
(92,	1,	'AdletA',	'4Bm0Ya3r@old-demo-mail.kz',	'$2y$12$lIvvu.hLxJ50TEGmciERVu4e546dFDNd3n8OLP0k9sSvfMAUO2SSS',	'EQiwjso9cT13HvobwOL4S2nDx1TVRiAE',	1432207548,	NULL,	1481265481,	NULL,	'37.150.213.30',	1432207549,	1533332188,	0),
(94,	1,	'semey-bauirzhan-momyshuly-str',	'Magaza.Kzk_Semey@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'sPDhktgEItOoLyJCtbxT4dciJZhq7qCb',	1434364782,	NULL,	NULL,	NULL,	'92.46.21.127',	1434364783,	1533332195,	0),
(95,	1,	'AtyrauAvm',	'kzaTJi1S@old-demo-mail.kz',	'$2y$12$brxbqeKwtScRK20q89wpvOJg0.yBgshRwBX5qY47fo/WrTphgQdwa',	'54g5TPN7iiKQyFJOdjdxkDCmuV_h_opV',	1434894387,	NULL,	1533332193,	NULL,	'84.240.240.230',	1434894387,	1533332193,	0),
(96,	1,	'SBI',	'SBI@nomadex.kz',	'$2y$12$Gw8DPZaud5lmKM35BjOLiuyK.uD2fXss5dksJsl4Lxue5qwBT3FTe',	'EWzV5PvJSBAjjPAtf5bjo6Upal-fGRqd',	1434965306,	NULL,	NULL,	NULL,	'193.193.241.238',	1434965306,	1442165991,	0),
(97,	1,	'123456789',	'123456789@dummy.com',	'$2y$12$Rlxd6qNvLnVE5E6GaHYVXO2rhaHsTuXZYFHmIfgb1ys/yJDZMQfdq',	'mNl5hKi-9IgXYQcbFwYowY9bDZknbVPc',	1434990567,	NULL,	NULL,	NULL,	'37.99.26.117',	1434990567,	1442165991,	0),
(98,	1,	'11223',	'11223@dummy.com',	'$2y$12$oikMb68RT1SaFAiAHwkxEOUaqO5STdWTcXJ0TLlbQwpcfScGCxO5q',	'V66Uu1_VfmdC_ck1FsWig1o6V8UvfW2G',	1434996246,	NULL,	NULL,	NULL,	'37.99.26.117',	1434996246,	1442165991,	0),
(99,	1,	'123456',	'123456@dummy.com',	'$2y$12$YzERTEfGsIFMH2KrDdJBvuyFiaiLO0Ifosze6gsCqIOkQs5xknLo2',	'4XaVt0jW1i-STQ2RHhCCCm0VMqz2KnhB',	1434996573,	NULL,	NULL,	NULL,	'37.99.26.117',	1434996573,	1442165991,	0),
(100,	1,	'34343434',	'34343434@dummy.com',	'$2y$12$adGI9OqYkeF6umG2w6iFbOpbTi5DEf467L/z8jX7Oc0912gIiK8sq',	'dk7Ld-vylUUGvBscR_Y3p1Qx1eg4MoHR',	1435050956,	NULL,	NULL,	NULL,	'193.193.241.238',	1435050957,	1442165991,	0),
(101,	1,	'6666666',	'6666666@dummy.com',	'$2y$12$kGydGGKJepTd/3FDKLYoUO7miGMAIzmnF87c/0xKgLg72nB7M3uBG',	'xpNyGGa5_m93fsMGerrtUpFlAZfngz-L',	1435073813,	NULL,	NULL,	NULL,	'91.200.234.60',	1435073813,	1442165991,	0),
(102,	1,	'87017164255',	'87017164255@dummy.com',	'$2y$12$rVy0XLFduav9q8meMNs2.usD.JtpN4WJ8bnuSs7.pnBURNMho/Tju',	'n6r-XxwMeQ01jbxoK2_X4BZpuiZYz9lV',	1435135242,	NULL,	NULL,	NULL,	'37.150.163.127',	1435135242,	1442165991,	0),
(103,	1,	'43434343',	'43434343@dummy.com',	'$2y$12$3VNsQL4l38Wk.hmhqdgZxuUvrQmpOdaGNg4ts0nMzYnLwYj2WNbcm',	'YyNXEHtbafyZ04Rju0UTqZln7XisIDcu',	1435136193,	NULL,	NULL,	NULL,	'37.150.163.127',	1435136194,	1442165991,	0),
(104,	1,	'90152121323',	'90152121323@dummy.com',	'$2y$12$c2p8L5aNx6KWXkAgQvPDs.qTUgd.m9vcpF8lLSDPwUMBokyk20o0u',	'JanXUGrklBqw11wm_fxjob9nwYNXv8IY',	1435136409,	NULL,	NULL,	NULL,	'37.150.163.127',	1435136410,	1442165991,	0),
(105,	1,	'8701716425509',	'8701716425509@dummy.com',	'$2y$12$VoyLcWjYoecR9KFKYR50XO/BzpVPobRdRXkpXQGeyU5xYxXSwXYzq',	'xfFhzBI25dHkfC8j634cBnf9kMdvIMLF',	1435137349,	NULL,	NULL,	NULL,	'37.150.160.150',	1435137350,	1442165991,	0),
(106,	1,	'87072525736',	'87072525736@dummy.com',	'$2y$12$JYLcvXHm9O4n1g6ZNb40Xe2GmMFSy0H.QOav2tD96YsfircD454kK',	'kneXa_luT-5hZ9G-HQfj71Xqvxhf1UyM',	1435139533,	NULL,	NULL,	NULL,	'37.150.160.150',	1435139533,	1442165991,	0),
(107,	1,	'87017164251',	'87017164251@dummy.com',	'$2y$12$DcavhfDmn.PzcA36GWDgmOc1JWuRU.gnn0DXHjxyqTFkUw8DB0Iwi',	'_4BTJRwWoe7wYMtmi3jY85KTm1iYTLpk',	1435146989,	NULL,	NULL,	NULL,	'37.150.160.150',	1435146989,	1442165991,	0),
(108,	1,	'87017164250',	'87017164250@dummy.com',	'$2y$12$/fs9zQ19RvSBvpEoVgxmBuR/nJbfghkIVvFjaxQz9XT2XhcoBdR.m',	'w_f9sCIDyb3K1Zh8mgPdeDJB2A7fXnfu',	1435147116,	NULL,	NULL,	NULL,	'37.150.160.150',	1435147116,	1442165991,	0),
(109,	1,	'87017164254',	'87017164254@dummy.com',	'$2y$12$b8S6/hinVwBbfZd8fJmzdeJ8czzir3zov4JbOM3JJubwV69m1Lhjq',	'wQdPYnqrEHVWIleBV407fNfrUmp_iko7',	1435147184,	NULL,	NULL,	NULL,	'37.150.160.150',	1435147184,	1442165991,	0),
(110,	1,	'87015164257',	'87015164257@dummy.com',	'$2y$12$8l5yZxVx84QA6/yfA/Ri8u8daEUObkgjij4sC5D5EnZ202vJbN9Rm',	'Vme9dsD_KDT2Rw0__nAf430wOO00NT7X',	1435148050,	NULL,	NULL,	NULL,	'37.150.167.120',	1435148051,	1442165991,	0),
(111,	1,	'0936666666',	'0936666666@dummy.com',	'$2y$12$bhmBXQSRN2WvHA8fKWL4peKJee66KFxgxdzJgTbBLvA//PEsGMbVe',	'R9yL-l4dB134ZfGZFcuSZVTSSY66zunu',	1435158602,	NULL,	NULL,	NULL,	'91.200.234.60',	1435158602,	1442165991,	0),
(112,	1,	'870171642545',	'870171642545@dummy.com',	'$2y$12$/i1/zFqYuYFFjA3TwSOT/OsqBsWY9XUdUxwcWAQaZF78tZOVWEyLy',	'H-GYums5KggG9yLW413VNxF5iABr-Bj3',	1435210065,	NULL,	NULL,	NULL,	'37.150.163.62',	1435210066,	1442165991,	0),
(113,	1,	'3434343434',	'3434343434@dummy.com',	'$2y$12$zabNhfC1LgoJdhutRawJzOfo0Pw9Sk5yPu0ZBGiTSVpLJIgBMxU6O',	'z1FFJV65QqxVwpMVBj8KdxoWOKGsCxCW',	1435649601,	NULL,	NULL,	NULL,	'185.57.72.202',	1435649601,	1442165991,	0),
(114,	1,	'87077434703',	'87077434703@dummy.com',	'$2y$12$4ryo7BE4pK7HB7HBx4PoqeelF7pPwqeizjcY6nXpOo.Hzo25coBp.',	'iLuBec24DbwE_BQ0pUj4soZGURNOS4Dd',	1436259903,	NULL,	NULL,	NULL,	'37.150.163.156',	1436259903,	1442165991,	0),
(115,	1,	'87027773850',	'87027773850@dummy.com',	'$2y$12$0cyqE.If7te3gtGSWrxFDO.SJeu3h4zvBr.KpBiqrNNit/m5Kclpe',	'chhZZJJmYnd402bG2PTqhw0b15EwLsoE',	1436324856,	NULL,	NULL,	NULL,	'37.150.165.97',	1436324856,	1442165991,	0),
(116,	1,	'87678878',	'87678878@dummy.com',	'$2y$12$BBGdf7.lDFgwOfmGgwyVFOpZm1iS8RKlcQ3FVwmbz4Gr9ziLjN/CC',	'aeUnHg7p_bNILJibUfpcGVQa0KfuTJlZ',	1436325798,	NULL,	NULL,	NULL,	'37.150.165.97',	1436325799,	1442165991,	0),
(117,	1,	'44411',	'44411@dummy.com',	'$2y$12$QVfuxJLvuMU1Aw1m.nLpAu1Hwm.VVQQ//.MlLiqwUfWTJ2xupFqIi',	'5L2O0yKXcRFaRVg6Gsox-56tKV9w_51V',	1436326079,	NULL,	NULL,	NULL,	'37.150.165.97',	1436326080,	1442165991,	0),
(118,	1,	'87013449984',	'87013449984@dummy.com',	'$2y$12$7YW.K9.WNlZlOT/HM3O0su7nGDQCcJMM8aV0hmW/w.7ZwYs9uFRma',	'Yl5Ah1Ql5VtB6XvQIaFTZI2zjR_30N69',	1436508222,	NULL,	NULL,	NULL,	'37.150.163.15',	1436508223,	1442165991,	0),
(119,	1,	'Shurik',	'C0px3Cqc@old-demo-mail.kz',	'$2y$12$LPdc3lutiEeo4sA3AG6io.tT79OvVZrWoYJc2RQVVElC34nbL6GQG',	'mg-WM60bHF5P92NNBy6n4ic6ISHnDKLE',	1436599520,	NULL,	1444224620,	NULL,	'89.218.178.156',	1436599520,	1533332188,	0),
(120,	1,	'KARACAHOME',	'KARACAHOME@NMDX.KZ',	'$2y$12$MsRwUGbAz95.DK1vnr7MbufqvcX/2wCXL5XKVUh7BpCUBfWQbFhC6',	'_0daKw3_OZPawlcj0hvcaQnzYU64T9Zm',	1437362082,	NULL,	NULL,	NULL,	'193.193.241.238',	1437362083,	1442165991,	0),
(121,	1,	'87772393431',	'87772393431@dummy.com',	'$2y$12$QA4./DH87bu0Sde4tCjV7OE9DanzpCwMpVLxckuLKwgXR4Y3UJABS',	'PkhQajk0ypfhjGsGowU-rLhnQ7EvTO_V',	1437708001,	NULL,	NULL,	NULL,	'217.76.74.154',	1437708002,	1442165991,	0),
(122,	1,	'87017993749',	'87017993749@dummy.com',	'$2y$12$JHVdDo7SMZKJ6tWJR28E3uykcTKjDA4aYTl2mUmI68mMKAQ3kpDP.',	'ZPWY9qCYLZwgiPwIfmOKdqYUIPvyha3_',	1437711230,	NULL,	NULL,	NULL,	'217.76.74.154',	1437711231,	1442165991,	0),
(123,	1,	'Myrat',	'2Ql-zgAe@old-demo-mail.kz',	'$2y$12$1Cej90gI8WJyYsvopvnmTex5778HvF/.AulYt7U4bdaJHFad7cxoS',	'brNe_vtAJixPGH-kH706ZFeuv9p-5lXG',	1438318187,	NULL,	1533332191,	NULL,	'88.204.247.134',	1438318187,	1533332191,	0),
(124,	1,	'87010480800',	'87010480800@dummy.com',	'$2y$12$ExVHMiUKzV6dYorjyPDXQekiSFODpS0LZKhRB226xk/VdTwSBIleW',	'Sgz9HFCVMTbnHJERe-NiPVxfbl3Qm7x8',	1438337466,	NULL,	NULL,	NULL,	'85.117.114.97',	1438337467,	1442165991,	0),
(125,	1,	'87077210362',	'87077210362@dummy.com',	'$2y$12$uDWllkAs4uDP/Qbu839Dq.KLOLPVDDheBdDYDGljGKN9UKv/VO78e',	'rRUZIQ0bYhIJsD4j4VBsEJk4cS8c6uOT',	1438931298,	NULL,	NULL,	NULL,	'92.46.21.188',	1438931299,	1442165991,	0),
(126,	1,	'87052641629',	'87052641629@dummy.com',	'$2y$12$OsNWnZfC2OXzEQRTQHZ6JuUWAKVvaAWQheZ2wKRbV9klu3QLt4fua',	'9fjtBZ1QFgmcOIoe_wxVqUHHWVT0sQfE',	1438935363,	NULL,	NULL,	NULL,	'92.46.21.188',	1438935364,	1442165991,	0),
(127,	1,	'87055019909',	'87055019909@dummy.com',	'$2y$12$pebBeitdjkzL0V./IuJtY.AhOnF3OOT6mMmj1n0sAHFNX4yJ5Bsri',	'cgiz6_7uaxkX9Ny5l3Ukp-KxsoxlDC_p',	1438937125,	NULL,	NULL,	NULL,	'92.46.21.188',	1438937126,	1442165991,	0),
(128,	1,	'kizilorda-zhibek-zholy-mall',	'Magaza.Kzk_Kizilorda@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'E0_rUIMCbvLQ3-kKqgqOV55sO63m7sgI',	1439041228,	NULL,	NULL,	NULL,	'37.99.51.65',	1439041228,	1533332192,	0),
(129,	1,	'87770626339',	'87770626339@dummy.com',	'$2y$12$kIBgJ0tVpDmFrYVdHx98x.zh9Q250PgW5YLnfdTpSwr5tyRYQSCQO',	'zza4bgNjVDU2cfVh2By7DiwaOSt_ojNR',	1439289880,	NULL,	NULL,	NULL,	'37.150.166.185',	1439289881,	1442165991,	0),
(130,	1,	'87751638339',	'87751638339@dummy.com',	'$2y$12$hDkKwhJpmKKeUfzleEqjvOuwh.UQ48ghXUIY/IUPnmgBUD8ne4cv2',	'iJmmQYVhUszIz_8N2tJmr-YIXdN1MBe4',	1439885120,	NULL,	NULL,	NULL,	'217.76.76.199',	1439885121,	1442165991,	0),
(131,	1,	'87012838931',	'87012838931@dummy.com',	'$2y$12$wprWKI4UmWZYlfWOb2zRYOSFZSf/pe5ipMinjajfaQ1ijR/R/O/ru',	'XrJ_KSI9TGLsgy5VO_hdJlli-AnsaThw',	1439892877,	NULL,	NULL,	NULL,	'37.150.166.26',	1439892877,	1442165991,	0),
(132,	1,	'87009001999',	'87009001999@dummy.com',	'$2y$12$jwIsmurTQdGe272EJnRXuOudYgOJFH7AzHf7QJGxckZWa5xh8LvqS',	'aXbiGRUbx8KazLxAwZ1kdk2kZqfKYo0H',	1439893071,	NULL,	NULL,	NULL,	'37.150.166.26',	1439893072,	1442165991,	0),
(133,	1,	'87016764097',	'87016764097@dummy.com',	'$2y$12$uhNH6B9DG0nMTDTRfq/tN.hxKsYqgTevmTPpJW5whXWpa40fvPFbe',	'V7cNlEO3k9pdH-5MdEcQF7SIEf3ZezOd',	1439970180,	NULL,	NULL,	NULL,	'85.117.97.210',	1439970180,	1442165991,	0),
(134,	1,	'87764077777',	'87764077777@dummy.com',	'$2y$12$fmh8xunM97jSHMIiHKTxGe9usyRyebD2h/sN/WEEd2/lAGEpljVX2',	'gBcjYHXdrpF2PQfSHeFhq5mDyHGu5AUy',	1439978409,	NULL,	NULL,	NULL,	'85.117.97.210',	1439978410,	1442165991,	0),
(135,	1,	'zhanaozen-zhanaozen-mall',	'Magaza.Kzk_Zhanaozen@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'UOFTLD_gmdyAr3uRN1X1oy1-1bpkFCcV',	1440057816,	NULL,	NULL,	NULL,	'5.34.21.109',	1440057816,	1533332197,	0),
(136,	1,	'AAlfiya',	'WF5nXS77@old-demo-mail.kz',	'$2y$12$unX6aL4zbAZQHrZH2KGXzOE3DzmwxG7ev6/5NJ/UL2xY68pU1ftkm',	'NndkZi0b1dx3YutLQ36UAi0NYxToIvAQ',	1440057906,	NULL,	1533332190,	NULL,	'5.34.21.109',	1440057906,	1533332190,	0),
(137,	1,	'87087686979',	'87087686979@dummy.com',	'$2y$12$zIJH9xGpqy4wvMwRXmO.buIxZj0.637vTrsbUl/IIUHFKGxaEFsEa',	'r5o75FUFOQdrwp1_EABRVOEyEodq0R_h',	1440065126,	NULL,	NULL,	NULL,	'37.150.165.25',	1440065127,	1442165991,	0),
(138,	1,	'Valera',	'okunevv2@gmail.ru',	'$2y$12$Y8./G5L5yRzWBkuFz3cS4O2gc672bMnHyk69uWIi9rdPgJAl80eVK',	'n1DpQ85HCYdMsbj0qKWfo99P85micTD6',	1440144107,	NULL,	NULL,	NULL,	'88.204.226.238',	1440144108,	1478965253,	0),
(139,	1,	'87074368840',	'87074368840@dummy.com',	'$2y$12$aGk6s2n/8M5zgNYpQ2R/J.FR7s1S1z5jA7rvMUgIsvf7/1JIeWKjC',	'HCbziw_yziZ4CIUOAwW9xXrCbQwCgU6-',	1440145366,	NULL,	NULL,	NULL,	'37.150.164.205',	1440145367,	1442165991,	0),
(140,	1,	'87022918950',	'87022918950@dummy.com',	'$2y$12$Nfk3i9tCGw2y9ZTBWDBfaua.ssV1AyIU7OvscttbHg44H2khLWQka',	'jXiWtXrd3faaUTHaDWLIKIp0pJhpJsZo',	1440154456,	NULL,	NULL,	NULL,	'37.150.164.205',	1440154456,	1442165991,	0),
(141,	1,	'Zhadyra',	'qbbnhGWA@old-demo-mail.kz',	'$2y$12$vRG.vmxXgwTJrbkEnzlKeezwmjMtsXyeRntgpjwjAPK9q76RDUR/a',	'YJW7VDUVuc538JUkz9NSBTeh-eGJ6Z4l',	1440306742,	NULL,	1533332190,	NULL,	'91.185.31.76',	1440306743,	1533332190,	0),
(142,	1,	'Shukhrat',	'htd-Uekx@old-demo-mail.kz',	'$2y$12$qE8O6UeMra8tBAnJSfSwE.ivMmDS8dt9z0FvZWwR5yYK5zuzq3gUG',	'UJi1SKx14EvpOpkyzvMW7Fce5ieYODuA',	1440518130,	NULL,	1444581745,	NULL,	'213.157.54.78',	1440518130,	1533332188,	0),
(143,	1,	'Abdrakhmanov',	'Uv2vDNjY@old-demo-mail.kz',	'$2y$12$s.JRL7G7N1Ise7Jqro69V.g/Ae6XY.oqkWhZaxEWz54yuJZ.2bkfG',	'hs5o5Ihih8lEHXo1pvlR03BHpLtma9n1',	1441617530,	NULL,	1533332194,	NULL,	'89.218.126.242',	1441617530,	1533332194,	0),
(144,	1,	'Abilkaip',	'jHeQFbX8@old-demo-mail.kz',	'$2y$12$l/CnDkpkd1PJBDbUf35u1.sFE/A16G0PM5cvsz4ybyacYY9zPuPDq',	'dInlWHcBPbCep6CrQGWMfB4SHME2h_OH',	1441705773,	NULL,	1476090677,	NULL,	'91.185.31.76',	1441705773,	1533332188,	0),
(146,	1,	'kokshetau-tsum-mall',	'Magaza.Kzk_Kokshetau@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'yTnkc5LrMD4H7apqgWw3c-2qh8SH915H',	1442831381,	NULL,	NULL,	NULL,	'193.193.241.238',	1442831382,	1539693078,	0),
(147,	2,	'-BLOCKE-Omurzakov',	'eomurzakov@nmdx.kz',	'-123',	'-147',	1443154198,	NULL,	1443155275,	NULL,	'37.99.86.36',	1443154199,	1443155275,	0),
(148,	1,	'armanzhunusov',	'pAwt0nIR@old-demo-mail.kz',	'$2y$12$0HXt9T7p1dCnF3FOkI.v9.XzcC6za2qRsnJ0UMKYUDclPgU5SvxXK',	'v-0PEmaSjSqGQadBOr_c9VsRamqn5Th5',	1443677002,	NULL,	1461753339,	NULL,	'92.47.163.182',	1443677002,	1533332188,	0),
(149,	1,	'baytakdala',	'gm@baytakdala.kz',	'$2y$12$cxcYbGA1GHsUu777t3gUz.y./1NjRlLBE6Jjyu2yHdTxJ3wQocifG',	'Dn4OHXcn3eQfTM9URgVE3pgvJXoMc7pY',	1444819695,	NULL,	NULL,	NULL,	'193.193.241.238',	1444819696,	1444821599,	0),
(150,	1,	'Dinara',	'Dinara@mail.ru',	'$2y$12$XzC2CvZuCWXCwSS.Yn5CHuKzZLsBjh.TieyLOPQQ5hB/r0tCENvHC',	'gMhBr5fjSKQ7-dF8iWMkiNDJoSeDrIsA',	1444893952,	NULL,	NULL,	NULL,	'193.193.241.238',	1444893953,	1444893953,	0),
(151,	1,	'gallyamov',	'_x-BC1d3@old-demo-mail.kz',	'$2y$12$/pCDQAiIkq9n694cQO3Zbu8ccLNZU784AMCYB8arWHQHns7Ppv4nW',	'ZNPV64WPnKAa4NghgsMoH1-_RgV50nYo',	1445679001,	NULL,	1452833939,	NULL,	'89.218.178.156',	1445679001,	1533332188,	0),
(152,	1,	'aport',	'NNkdz4XY@old-demo-mail.kz',	'$2y$12$gAiicDtW.21AGYF4hPgEmelo.B2uGUF6kOsU.XVE6WaJE/2aOyUPi',	'sFEV27fHEz_suYnnjd44wE5myv3-kG_a',	1447657547,	NULL,	1448202023,	NULL,	'213.157.54.78',	1447657547,	1533332188,	0),
(153,	1,	'GULNUR2017',	'Fh-wLowY@old-demo-mail.kz',	'$2y$12$VCUUDX4M1BAs39.09OZ6RekXXX8Zp3s0wb8V6e.f84NT00OnifR6u',	'Fagovf4Z0JwgQDemjpGkOOxrw62OuS4A',	1448202330,	NULL,	1533332190,	NULL,	'213.157.54.78',	1448202331,	1533332190,	0),
(154,	1,	'AlmasB',	'923pRKnD@old-demo-mail.kz',	'$2y$12$9/AyLN/QBQz4LxXfoxAbHOLFFmCHqFRvoCzDPWUAO65DFD766tpwW',	'X9K6_vqJA_y7UHxwNXlajlpi-gF2qb0a',	1448426940,	NULL,	1533332190,	NULL,	'193.193.241.238',	1448426941,	1533332190,	0),
(155,	1,	'DariaKoshetova',	'Magaza.Rus_TeplyStan@defacto.com.tr',	'$2y$12$mEJIdLgigSX8gOSXA/nu1.rM7ZpZyWzyPd/LId14kGT4M6E6MNjQ.',	'ClMmSJqfpJJOOetOjJeOVHI6MZDUnSGq',	1448429339,	NULL,	NULL,	NULL,	'193.193.241.238',	1448429339,	1448429339,	0),
(156,	1,	'Vasiliy',	'Magaza.Rus_OmskMall@defacto.com.tr',	'$2y$12$vksvYlvIYlHN/AUbWc04iuUWQVjQCOvtsBtI4kpgulaNcGK62.m/W',	'bTp8taQLV6ES5TZWnZndYJyeYkt6RwLf',	1448429655,	NULL,	NULL,	NULL,	'193.193.241.238',	1448429655,	1473261862,	0),
(157,	1,	'EkaterinaDvorishenko',	'Magaza.Rus_KazanMega@defacto.com.tr',	'$2y$12$zBLaTTf4bdGbDvzaq9oliOPlnaVvRgwHOF16771CvAkHsqxKeO7kq',	'gljhA5jF9PgkDO3HN4xFGAB_juF8lBtv',	1448429861,	NULL,	NULL,	NULL,	'193.193.241.238',	1448429861,	1448429861,	0),
(158,	1,	'shymkent-plaza-avm',	'Magaza.Kzk_ShymkentAvm@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'mq2eNXNpNddTSVFTWqps4LO9q0tKrz7A',	1448613947,	NULL,	NULL,	NULL,	'193.193.241.238',	1448613948,	1546939010,	0),
(159,	1,	'Shimkent',	'abualiev-demo@nomadex.kz',	'$2y$12$LD.FXrXtv4Q09db5Drm1V.QtcNM4vuNCKRAjGPUyXXQihOYAlOhdG',	'pQEYySZ8_ewAI7_TTCmXNU25uR5MchUW',	1450758729,	NULL,	NULL,	NULL,	'193.193.241.238',	1450758730,	1450758730,	0),
(160,	1,	'Almaty',	'ualiev@nomadex.kz',	'$2y$12$uSwZQEX.rB7aji6F8LgVKu5E3.zqdDdz3AZvxByj7S513aOPHr3I.',	'uRr60H9Qkw-nvm4-9fItpWPF6Xu5NVET',	1450758911,	NULL,	NULL,	NULL,	'193.193.241.238',	1450758912,	1450758912,	0),
(161,	1,	'Kizilorda',	'aliev@nomadex.kz',	'$2y$12$SjBv6FOkcC8Q/HxCrWRR6O8A9umQonNS12KDk3W8JzUGw2chpX.xG',	'56PSkXgzqvmkjSH3JV7g0dotXj9Ft0v9',	1450762288,	NULL,	NULL,	NULL,	'193.193.241.238',	1450762288,	1450762288,	0),
(162,	1,	'AESH',	'market@electro.com.kz',	'$2y$12$m1t8pCRuonAOgx6FaZGODOCQCj6UmfFZ60Q2IOlzCjKUP4MK.0twu',	'hKJe665kD8nSkp9mNEU_4o9SbgdfThpM',	1452083033,	NULL,	NULL,	NULL,	'193.193.241.238',	1452083034,	1452083034,	0),
(163,	1,	'axe',	'QROOYcgk@old-demo-mail.kz',	'$2y$12$6jfDJXTh6AbMpBaxg4iQROegLqTiC8lW.vVK/FsJ982C5c87Ut446',	'-SNovlqkhqFLqMPDEyncKER6DxGI1qwK',	1452523219,	NULL,	1476090682,	NULL,	'91.185.31.76',	1452523219,	1533332188,	0),
(164,	1,	'zhanar',	'e7FgKHEf@old-demo-mail.kz',	'$2y$12$RlP.uC2u6zhxWBICLSgXVOzQTowiIXSzysILCMLfjs2Rj.yf9b/Ya',	'5_xkuRblPKLRugtUprO3EezwLIOovaw4',	1452853921,	NULL,	1453432398,	NULL,	'37.150.213.30',	1452853922,	1533332188,	0),
(165,	1,	'ekibastus-maxi-mall',	'Magaza.Kzk_EkiBastus@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'iaKfKe2bYPEcvziU4POMMEfhYcJIBgk_',	1453281862,	NULL,	NULL,	NULL,	'193.193.241.238',	1453281862,	1533332195,	0),
(166,	1,	'NurzhanZeynelov',	'7LL4DJoQ@old-demo-mail.kz',	'$2y$12$tOM3lKrNa4GBJzpeZdwjv.3uvmlto7MnPbQNqd//7oA/iTMzUS6Eq',	'OeTn70hJNiMR3sylfyZ8uIPBj3-WyGss',	1453432956,	NULL,	1497628672,	NULL,	'193.193.241.238',	1453432957,	1533332188,	0),
(167,	1,	'turkistan-tauke-khana-str',	'Magaza.Kzk_Turkistan@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'VNzc3Kd0uce9wNyD_4URtmm7-AQ4SG0J',	1453436880,	NULL,	NULL,	NULL,	'193.193.241.238',	1453436881,	1533332192,	0),
(168,	1,	'DELLA',	'DELLE@Nomadex.kz',	'$2y$12$rSV7DBtiMzvms.6VO/ffV.ehI2d4CVd1gLBa5sxpu/alMKXbmCV5.',	'5btp-tRYM6ukJdhuXX2zEVcezedxPdCU',	1453873789,	NULL,	NULL,	NULL,	'37.99.55.66',	1453873789,	1453873789,	0),
(169,	1,	'taraz-abaya-str',	'Magaza.Kzk_Taraz@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'ZJD3A5jn4H3S1RNFz66Md9LUO1zKjmuh',	1453990648,	NULL,	NULL,	NULL,	'5.63.90.38',	1453990649,	1548757222,	0),
(170,	1,	'Russia-Defacto',	'Russia-Defacto@nmdx.kz',	'$2y$12$fkoQi8J30DURrx.jp9pFVeSpFn/eknm2hCdbgyg6vXE5e8rJE5NLG',	'gTLbHFsp5QHFPqlTRK3XcbmqjqnM-ynZ',	1454270116,	NULL,	NULL,	NULL,	'37.99.100.40',	1454270117,	1454302186,	0),
(171,	999,	'Mjalilov',	'mjalilov@nomadex.kz',	'$2y$12$Ic17Lh8AEzI6HyfGkj/WsuwKewuaZRwi7tdt.ooEfbovE7TfHAo8C',	'9SZ8UoglP017-VZXCDFql5xTR_JXxAYK',	1454323911,	NULL,	NULL,	NULL,	'37.99.47.79',	1454323911,	1454323911,	0),
(172,	999,	'Atoxanov',	'atoxanov@nomadex.kz',	'$2y$12$tYwkUXzsw/p0e2VwpSXzpubP76JnRELVMJBtlwvTqQPzthSjnVoAy',	'TjfDP5t8hD9mHeAWt9Gtmg0iya5v8tzj',	1454323970,	NULL,	NULL,	NULL,	'37.99.47.79',	1454323971,	1454323971,	0),
(173,	1,	'Belarus-Defacto',	'Belarus-Defacto@nmdx.kz',	'$2y$12$DREHlcKWjMk16aQxG0bm5.EwB8Li0/nEJub2yL8o20uWeLdw2Fuye',	'UkauOMs_PY7UKCkUljEHkGreYOXuHQnt',	1454349387,	NULL,	NULL,	NULL,	'37.99.47.79',	1454349387,	1454349469,	0),
(174,	1,	'NNishan',	'yh_wZ3pw@old-demo-mail.kz',	'$2y$12$1rkaMwCLUh10s6HkiX.F0.MlgIznt.EIbG78yFFCwsipxLOVtcmv6',	'lN1FJWU_hIMtjIczDvMBAxNz70MGHshO',	1454556792,	NULL,	1533332190,	NULL,	'193.193.241.238',	1454556793,	1533332190,	0),
(175,	1,	'astana-khan-shatyr-mall',	'Magaza.KzksKhanShatyr@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'1tHduWGXn9TQz4f-wW7F5TKc7mmcQ_Gv',	1455441538,	NULL,	NULL,	NULL,	'92.47.206.30',	1455441539,	1534662167,	0),
(176,	1,	'U-Partners',	'test@mail.ru',	'$2y$12$f.fkUIaouvgkb2YIxYtbH.vpXWQtY1L1ozSwRaRJM9G1RIVfrUjU2',	'jHuEWtatX1tNe2cqhuKYjrPyRUEFewna',	1455539196,	NULL,	NULL,	NULL,	'193.193.241.238',	1455539196,	1455539196,	0),
(177,	1,	'---',	'demo-truckway@nmdx.kz',	'$2y$12$0lAVR0/6fkI6I4td5B.Ymu.hM39TqbtOp8olX4SOlVncXe7xzlALm',	'_YYTMpeEL9YMna3EAT9i-Rn3YIlD8PZe',	1456206681,	NULL,	NULL,	NULL,	'193.193.241.238',	1456206682,	1456206682,	0),
(178,	1,	'Kundyz',	'pCIQ8KpA@old-demo-mail.kz',	'$2y$12$t6D7o7gIoSaIy.GB/6hd0uZpGrqGZ8UNbzH5uuKKcngBhPk/6/0IW',	'ru__627ngVU9ms2q8p2T9fQDpx3xnc45',	1456219870,	NULL,	1476108407,	NULL,	'5.76.198.36',	1456219871,	1533332188,	0),
(179,	1,	'Muhamed',	'vZFCvhgR@old-demo-mail.kz',	'$2y$12$CmaIeLA5krrKF8q5bcdsluSXiT1FPbjPuI0aQxHmyHYOyD67UvjJi',	've5siw5eLQbwIJpU5J1rx_OWlvlqCFnb',	1456220729,	NULL,	1533332191,	NULL,	'88.204.254.190',	1456220729,	1533332191,	0),
(180,	1,	'almaty-asia-park-outlet-mall',	'Store.Kaz_AsiaParkOutlet@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'aMYiEIhVLvHfH5L511BRQcpyaRCttXfj',	1456670001,	NULL,	NULL,	NULL,	'193.193.244.30',	1456670002,	1533332198,	0),
(181,	1,	'Tupperware',	'tupperware-demoo@nmdx.kz',	'$2y$12$TGe2WZWCEvNGaQfIik.08emMpoEEbVbzqMotwlwXkGSYYkgMenB0W',	'7gaq0Md98PHI130k-OFOMRSHeTGaSuUw',	1456676057,	NULL,	NULL,	NULL,	'5.34.22.133',	1456676058,	1456677244,	0),
(182,	1,	'Elena',	'cwTeN-Ec@old-demo-mail.kz',	'$2y$12$LC2ficP3i9uVQAMznt5CDOy20MahBsRRI99DBZ/9RsrS.VjGxugmi',	'2Xv2sN7UAs4TXYlEmviGnEAD-UJt_n7l',	1456741837,	NULL,	1476090684,	NULL,	'193.193.241.238',	1456741838,	1533332188,	0),
(183,	2,	'iPotema',	'ipotema@nmdx.kz',	'$2y$12$2FKAlccJiIFhUohfGYytOuwHFKnIyeT4kbXdpLHsclMEyMuEY3cG2',	'W9r3F4DBhBWPyJBveatvFTWw0LEsmh3v',	1457096147,	NULL,	NULL,	NULL,	'37.99.35.23',	1457096148,	1463941611,	0),
(184,	1,	'530_Zhumagalieva',	'530_Zhumagalieva@tupperware.su',	'$2y$12$SZV6Am4M9nElzal/iWrKxOLSPJn481IOPbKiAn6mALI/HIoY1cOU6',	'nWVSviazjPAoiUPUQbMWKftYLNdefI_O',	1457199225,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199225,	1457199225,	0),
(185,	1,	'562_Ulykpanova',	'562_Ulykpanova@tupperware.su',	'$2y$12$wcagC5aymJiOrVA4HaiV3.yqrUl1LOTpZNcT1W22ftJ.ZjtZpWMpK',	'bGYyI_8iwyOqHyzCRpHFqgyT-ADScOeX',	1457199393,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199394,	1457199394,	0),
(186,	1,	'286_ahmetova',	'286_ahmetova@tupperware.su',	'$2y$12$ELZBRSy9lqIOFhRCbrzyUukkSVUDDexfS/dx7ZXbwKg7tVnkI0GHq',	'Iz_YfXAH1qEoJOGMNIWU8zaRe79-spv-',	1457199479,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199480,	1457199480,	0),
(187,	1,	'401_baiysbayeva',	'401_baiysbayeva@tupperware.su',	'$2y$12$upe1Ug3JMIbGIuH7lhyx5e6G8urpCcUWiPht1p/8Z7mhhaTYGXGKe',	'z1l-YqDJLLoAG7SQkl3K-efDEI-TwHUQ',	1457199500,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199501,	1457199501,	0),
(188,	1,	'255_abisheva',	'255_abisheva@tupperware.su',	'$2y$12$2cqwQvBpc81wlV2FeCl6XeOcmXuNOaEkGFddSCMx7IiLKR2ouCA4S',	'5Z8N07Y6WTFRi0ptyXGujwPBDlF9jDo0',	1457199584,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199585,	1457199585,	0),
(189,	1,	'566_Belova',	'566_Belova@tupperware.su',	'$2y$12$EuwcjUqb7QI0hx2l87sHCunbKhAPaQ1gvbnntbsS0zVLjsFkdeyIS',	'pyQzs0xxXq7J7vYKqlIh4sBb003D_RoO',	1457199620,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199620,	1457199620,	0),
(190,	1,	'595_Kazbayeva',	'595_Kazbayeva@tupperware.su',	'$2y$12$Wdi6s23nyQ7XM4tdgXSiZ.KbULsrx4g.ruhREUs/suT/F8IiI6KRy',	'BgIHjsGu5_28rGPuDIO0ESBh1lmueZTB',	1457199668,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199669,	1457199669,	0),
(191,	1,	'594_bogunova',	'594_bogunova@tupperware.su',	'$2y$12$nh0IGlCM.OE7RTfoa.NIxO47/xLuSuLmcxfqIaDGu/IxdVfzueEra',	'kbjLnK5IGdyJH6HcUeGd5dj4hqGSlMA5',	1457199722,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199723,	1457199723,	0),
(192,	1,	'576_kasimanova',	'576_kasimanova@tupperware.su',	'$2y$12$Ep9lCLm8zDe95nfsOH1Eceju2ZApCDy0Fn9p4sVuDf0f6ACR7Eohq',	'ZfIG-YfxtLnEn6zg-374f_R_-DfzF6cw',	1457199770,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199771,	1457199771,	0),
(193,	1,	'561_Davletaliyeva',	'561_Davletaliyeva@tupperware.su',	'$2y$12$2tKpaPoMcdmMyEpFnxEhX.CeR70eptY7y16.MsSYROt.xJ7Q3U1my',	'2iAUo6TQQbaZlfUkGE94FBlONOQtkBlm',	1457199829,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199830,	1457199830,	0),
(194,	1,	'230_tarasenko',	'230_tarasenko@tupperware.su',	'$2y$12$pZiFSTSZsR0GHJABI/IU6eP6OLyB9sw65CV.2uWsYF7pkHwDdPzEG',	'co-6jYWAfxo3Fl0226sSKZfGNsEh9BR4',	1457199862,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199862,	1457199862,	0),
(195,	1,	'172_dzhetibaeva',	'172_dzhetibaeva@tupperware.su',	'$2y$12$8rgEonRtKqzcJkqJfRDRs.iRBKYrmpp6vrhFSf369hbDVcN/Gt4E6',	'uGx2DylKgmOAAb93NEqpRtN69r6FO35_',	1457199910,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199911,	1457199911,	0),
(196,	1,	'572_tutanova',	'572_tutanova@tupperware.su',	'$2y$12$zMQNFht48ozL4saRsXqZBu5zxilkBTvoilk1I7DwGCvtrPao75t5i',	'fVN9kggpQKhtN4cqKbvA2oCDf97sfxba',	1457199956,	NULL,	NULL,	NULL,	'37.99.43.210',	1457199957,	1457199957,	0),
(197,	1,	'523_Doskhozhaeva',	'523_Doskhozhaeva@tupperware.su',	'$2y$12$R2hHvlT1T.D0uQ4mVB9t9ur2LBjubCZGPLm5158vm/kiZSbL.skf.',	'ubj7-7gWO5i_P-mkp_KkJaOJymvpj2Gz',	1457200065,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200065,	1457200065,	0),
(198,	1,	'259_turalina',	'259_turalina@tupperware.su',	'$2y$12$q.bMQVo3NZa894anIPwbJuMsBNWCUyEAKezgX7XSgCfYI6Ez9fwWm',	'50oorneeeBiKgJHtzVdAeEtY9g-fBR2o',	1457200066,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200066,	1457200066,	0),
(199,	1,	'098_elemesova',	'098_elemesova@tupperware.su',	'$2y$12$PjbjytOPQZXQHNXpTtwsReR1/rnpTWHG/JdTXZoxM16GG.ENmtA4W',	'aD3LuzMQHVr8TbNG7dErwdyEFTqhMuVS',	1457200152,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200153,	1457200153,	0),
(200,	1,	'260_sarybaeva',	'260_sarybaeva@tupperware.su',	'$2y$12$Uhq2OW7CZJUxhHm9sWafH.i9S/rWxPOLCOo/2JA/8Lnc33AvtiKcm',	'D_o4rXu4IXuuK6GSd4VoRXYR7QeRhN_t',	1457200168,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200168,	1457200168,	0),
(201,	1,	'041_iskakova',	'041_iskakova@tupperware.su',	'$2y$12$gGKBonfR3T8DbVkAQYtxNu.BU902rPzGZLfLbxBzC1YIaoIG6rf8a',	'fBvMQK-molJZTp9MWBwb9COhSo237oyr',	1457200243,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200243,	1457200243,	0),
(202,	1,	'302_romanyukina',	'302_romanyukina@tupperware.su',	'$2y$12$rie2aU4y9SnUTxV73IBA4.9Sctb7/sGi5XLPe1j50KDxomxaBaF.2',	'SsnL904-l3AkN219_cLD4Wq_ycMqnnkS',	1457200254,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200254,	1457200254,	0),
(203,	1,	'118_kamenova',	'118_kamenova@tupperware.su',	'$2y$12$HMaJv1oF/ayoh3ALMFdMF.C9AV6u9isau9SzdabMfWWTgaRgt5wHm',	'_titWM2yao9jNZGaqBWF_JdcuA-QEHNH',	1457200329,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200329,	1457200329,	0),
(204,	1,	'263_Malyutina',	'263_Malyutina@tupperware.su',	'$2y$12$Rsb7hTOVN/6Ijaos4GV7yu/XM1zbup2VmnqdrmYLz843ZIpkN2V.q',	'XRPnOrZtHIXiijfAwkNIyaMuyy_LFy83',	1457200330,	NULL,	NULL,	NULL,	'37.99.43.210',	1457200331,	1457200331,	0),
(205,	1,	'TimurK',	'4dXL6UHz@old-demo-mail.kz',	'$2y$12$Oinwu4LgbvrdbvVpwAZPFeFQemjByOA.KjqnrQ3E1lZnO9VQ43pga',	'xonvBI2cvfY69FhF_vOYvMuYl6eLY3w9',	1458815797,	NULL,	1496126676,	NULL,	'193.193.241.238',	1458815797,	1533332188,	0),
(206,	2,	'Bermet',	'bmambetsadykova@nomadex.kz',	'$2y$12$v3XyLCdH3H/9JfahKhcS6.pB.VftxRnpD.DhvmWCl2/I2IxJJt6GC',	'NswVHtqGVvhrWUi49iz2mQNNV8wTBook',	1458882613,	NULL,	NULL,	NULL,	'5.34.91.25',	1458882614,	1533659398,	0),
(207,	1,	'Diar',	'RH5KQbF-@old-demo-mail.kz',	'$2y$12$ZoCDOjy4GhPV5u98uazBCebHIM34YYMyuTNIujfKAm.BmAK/WkhCO',	'7-Icc9GHw5IRZ6qpa28PfqWTjT2MoLmG',	1458902029,	NULL,	1533332194,	NULL,	'88.204.247.134',	1458902029,	1533332194,	0),
(208,	2,	'Omirbek',	'test@14.ru',	'$2y$12$8yM5m.AqWf7SLw21orFH2.ZvMrpNMwy/wz9G0DAcs3dIaoP3MQjWq',	'hLpaSgJ3iUiTljXNSM2ckR5qklNgNgf9',	1459331805,	NULL,	NULL,	NULL,	'193.193.241.238',	1459331806,	1459332052,	0),
(209,	2,	'Erbolatov',	'test@15.ru',	'$2y$12$ZVjd/dv6WAmcGPl4PUdVr.DQeF5LiQ6jB.UNrECn.pEougNt1pBtC',	'_lZHJiIBICvh52ypGbepGiWKk7c_Ewtm',	1459332211,	NULL,	NULL,	NULL,	'193.193.241.238',	1459332212,	1459332212,	0),
(210,	1,	'TupperwareStock',	'TupperwareStock-demo@nmdx.kz',	'$2y$12$F3k44mBNLMs9eZkajbKakOiWas20TB8/hkbOV8goa5xGzL1yNT9jq',	'ARz68H1YGRTU1oemBU4aqbx2aOEyOJCV',	1459794179,	NULL,	NULL,	NULL,	'87.247.47.233',	1459794179,	1459794179,	0),
(211,	1,	'Ansar',	'Vr7g_VVz@old-demo-mail.kz',	'$2y$12$dkjSlkbV9VlQ20zwGXgNQeZTEy.So9aBou.UvkkAa/O4VHV/yz/oe',	'zVWrkVpeeGkSFmqCNYVEoalO22I6s8rd',	1460992339,	NULL,	1499326009,	NULL,	'91.185.31.76',	1460992340,	1533332188,	0),
(212,	1,	'NurzhanK',	'HtSxRmPh@old-demo-mail.kz',	'$2y$12$RGN7yYCPwp8/jhyu.CKpB.IOneKPLgMT7ByCz7oLPDD.5airFYpVK',	'4dCueg-j182H1c_dXn_W5hnbyjuDno58',	1461146625,	NULL,	1533332195,	NULL,	'193.193.241.238',	1461146626,	1533332195,	0),
(213,	1,	'AyatM',	'DwYV9LZy@old-demo-mail.kz',	'$2y$12$4CKAxXMh47OIeHDWM0Q/4eEW.VpGy49Gjukl.PFeY5ATo/okX.e/K',	'_9H1NQQ5vtH8IsB1vm5-eNx4UIDg_DiW',	1461753187,	NULL,	1533332188,	NULL,	'193.193.241.238',	1461753187,	1533332188,	0),
(214,	1,	'Ruslan',	'kkgg6FkR@old-demo-mail.kz',	'$2y$12$wi2GvFArvbBP5YvV2KcXJ.3mrHoJihWLoppwxh1X8IRaCRcGNbkz.',	'hk5pmlqDP_myJtM-_2b11WvMqYlGdTel',	1462959030,	NULL,	1533332190,	NULL,	'89.218.178.156',	1462959031,	1533332190,	0),
(215,	1,	'Shag-Plus',	'shag-plus.info1@yandex.ru',	'$2y$12$MgEnVL8zLSiQJ.F7qzmhTupLGybuVS3Zyd3AWxt7FLF9gU0wj8byi',	'zNRHoSJBQteJm3jsvZ_TVhvAQ1nUkTnU',	1463113849,	NULL,	NULL,	NULL,	'193.193.241.238',	1463113850,	1463113850,	0),
(216,	2,	'Astana01',	'Astana01-test@nmdx.kz',	'$2y$12$6TmrtmTMISiP2FalMDw4q.tBZTvPu0Iu2ANEny7/YLjyr9XuA4GRK',	'Ok7dzpFVZs3SnEsnBRvR3wCF3dt9R5UI',	1464251568,	NULL,	NULL,	NULL,	'193.193.241.238',	1464251569,	1540145589,	0),
(217,	2,	'StockMan',	'StockMan-demo@nmdx.kz',	'$2y$12$R0RVBk5qxk5RsKV9WsEYuuOPH/AqUM6AU5tb37YufW1uluPJ9zHgC',	'Dhjg9CNrYZpfIctwJfm6faJWOcDZitD8',	1464252446,	NULL,	NULL,	NULL,	'193.193.241.238',	1464252446,	1528874947,	0),
(218,	1,	'astana-mega-silk-way-mall',	'Magaza.Kzk_MegaSilkWay@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'jzA3Pd9msxdREPl64ioqroDlK-3J4O3H',	1464611233,	NULL,	NULL,	NULL,	'89.218.71.50',	1464611233,	1533332189,	0),
(219,	1,	'uldanov',	'thzTZRFq@old-demo-mail.kz',	'$2y$12$hRU42Y0J3s7rnDYumgvAw.SwuRZUC0LFTR1kM4.rt4PLuvdkJAGzi',	'BOgH87QDqTaELF6kKopKZO_9lDXgCOhQ',	1464612735,	NULL,	1473325048,	NULL,	'89.218.184.78',	1464612736,	1533332188,	0),
(220,	2,	'Abualiev',	'abualiev@nomadex.kz',	'$2y$12$OKgeN5W4hc8wFYYthxo1M.wxuoBZtNZb4vJb3K.JJbFARXXSZvyHW',	'kWe0R6UWvFvtzKoDJgeAqny-8KN7X5MK',	1465358545,	NULL,	NULL,	NULL,	'5.34.85.213',	1465358546,	1559374445,	0),
(221,	1,	'619_Shevchenko',	'619_Shevchenko@tupperware.su',	'$2y$12$cslxWGlHkXyPb.xcehjyv.Zu2iheCW9nFfGo1JhXYzYGXbo/W9WKu',	'GAW59TJXrdDHNDxYe1nKemKqnoJivdqR',	1465450461,	NULL,	NULL,	NULL,	'87.247.33.92',	1465450462,	1465450462,	0),
(222,	1,	'620_baubekova',	'620_baubekova@tupperware.su',	'$2y$12$mkTIdcRC..d1FnBCCeuY7.1nlqveCMU1f66lBf.7bngzSoVJQfcgG',	'8Zr3ZYid_SIz5NBxUVpfajtX3-O7dQE_',	1465450532,	NULL,	NULL,	NULL,	'87.247.33.92',	1465450532,	1465450532,	0),
(223,	1,	'Arenes-engineering',	'test@mail.bk',	'$2y$12$AA69lLqvU4op0sVWYaQA9OOFuxBa3bCmQKJUf8tvFiY/Ml0.WOkQe',	'UiRSJVcV_yDLZ9aIjVt8V_xOHjdQAZP1',	1466001431,	NULL,	NULL,	NULL,	'193.193.241.238',	1466001432,	1466001432,	0),
(224,	1,	'621_aytpaeva',	'621_aytpaeva@tupperware.su',	'$2y$12$wbrmT14X/zGyj39Ewb7mR.iwTRym24kWy6V3J/D7ySwYMzgtF.1Pi',	'Q6Uh4sz9j5sG8k_V3b1T6puXEr6ci_Ib',	1466412662,	NULL,	NULL,	NULL,	'87.240.8.85',	1466412663,	1466412663,	0),
(225,	1,	'TAT-SERVIS',	'yskakt@mail.ru',	'$2y$12$7d3f4sAPLe7.uvjr1XPKbuNGYTi1jy8XpEsl0BIEUMadokmooDyLi',	'hAuXGpZvkfJ6G0luVZ_V92OliGyk3Nhb',	1466760496,	NULL,	NULL,	NULL,	'193.193.241.238',	1466760497,	1466760497,	0),
(226,	1,	'Translain',	't@mail.ru',	'$2y$12$F8MgcevKXUubQzdc.XV67ezwJLEMsWplm.bc91tdCRoxwAWKdmQMm',	'3xQF63l9w89320ets7xmLy-WA3RA6q1q',	1468485293,	NULL,	NULL,	NULL,	'193.193.241.238',	1468485294,	1468485294,	0),
(227,	1,	'Ponomarv',	'vasiliy.a.ponomarchuk@gmail.com',	'$2y$12$LxChFVHetRzHt9y.ZsENyu5EbPlB15B0xNLGWTvihxygJ1sVB/kEy',	'uH1xXja-17wVDaDHvX_R56QqqqvPRRty',	1472994106,	NULL,	NULL,	NULL,	'141.101.81.74',	1472994106,	1472994106,	0),
(228,	1,	'ZhdanovM',	'3W7glNVZ@old-demo-mail.kz',	'$2y$12$4qIkXXleD79HO6QDRmZL3.u3p4tN1Jha7UBnd6lFemqi1QGH3Ttt.',	'ITlFZEZ-fuHQ4WwPyEShR7BMLZQSo8jw',	1473325004,	NULL,	1481188614,	NULL,	'172.68.10.153',	1473325005,	1533332188,	0),
(229,	1,	'634_Larina',	'634_Larina@tupperware.su',	'$2y$12$cRo9m25BiATY5cE9.KsGB.veCVuS9dtu22BWzEr8LaBtDyi2dxe/O',	'be8od9kciTIbtNIh9fJqonBBfjiWXXbW',	1475486730,	NULL,	NULL,	NULL,	'91.77.235.223',	1475486731,	1475486731,	0),
(230,	1,	'LLena',	'lG3o-7iY@old-demo-mail.kz',	'$2y$12$/oNb9COBR6eMgIE7HkRgCu7UBs3mGB48Lq8gGJFUpk4zZQF3xc5gS',	'OUjw0ey4iLB0UosAXBKxHFBPEHhihxzp',	1476099620,	NULL,	1483979331,	NULL,	'141.101.80.165',	1476099621,	1533332188,	0),
(231,	1,	'Kuanish',	'9Z23tFis@old-demo-mail.kz',	'$2y$12$gtzlJWvVYLCz2w81FasUKeMMpO7.3Dimsal/owrR14Vp9wD.S/t86',	'-YJ9w5Bzz2NMBWCisJfKPJY-CuNKLrao',	1476113326,	NULL,	1499779841,	NULL,	'141.101.81.77',	1476113326,	1533332188,	0),
(232,	1,	'zhezqazgan-seifullin-str',	'Magaza.Kzk_Zhazkazgan@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'H71KQE-I-WQNLDyx17o1lqZEqK9-0vZP',	1476434092,	NULL,	NULL,	NULL,	'172.68.11.164',	1476434092,	1533332197,	0),
(233,	1,	'aktau-first-president-str',	'Magaza.Kzk_AktauCadde@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'jHKWT0q4HBCkHvOgkVYBUfXGdhQrtE3J',	1476436059,	NULL,	NULL,	NULL,	'172.68.11.164',	1476436060,	1542882613,	0),
(234,	1,	'TDMKazarstan',	'k.aitkazin@tdm-kz.kz',	'$2y$12$Z/cUOPGlWszI6BPGvOGpmuRTvSUdjuX5D1sqgfQRLRZpWfYPIPqy6',	'BCFXhgYXkmKWBNizUl3Pkjqik5x6SASx',	1478674407,	NULL,	NULL,	NULL,	'193.193.241.238',	1478674408,	1480996992,	0),
(235,	1,	'TyresTrade',	'tyres.almaty@gmail.com',	'$2y$12$a5cJ5zfkY313lY/SlUqfSu9lxICGZeVF0Qpgx7uaDmzdjQGlv4NX2',	'iKKI9Sjx9hRFP7ZJqVTWKnOh7QRJ002h',	1479282243,	NULL,	NULL,	NULL,	'193.193.241.238',	1479282244,	1479282244,	0),
(236,	1,	'gulnur',	'gulnur9103@mail.ru',	'$2y$12$VJ9t2hJ/tJu2iJnLkB.xX.7xY9Up1A1Z0Eh9DmJ1Ex26iaO9Sjjw.',	'CLQqCanEQCwgGblyHoFKstScNJyq30ct',	1479470161,	NULL,	NULL,	NULL,	'162.158.91.88',	1479470162,	1484315222,	0),
(237,	1,	'ImageHause',	'123@mail.ru',	'$2y$12$xgTjeC37n9m7PFu/53WDuusN5o.AAz4MKzJdP6DL4/JQWroAAP3Mq',	'_xvtVTTeQTpAZg8vyykmzZNU70yQ690C',	1480486213,	NULL,	NULL,	NULL,	'193.193.241.238',	1480486213,	1480486213,	0),
(238,	1,	'Omarhanova',	'R2TdnjtS@old-demo-mail.kz',	'$2y$12$IGbBTKE9nrImrrMo7NUVReB8S8viQ9uBm0n4DpvLTeI.uqbbLqL5S',	'H9-Ssa1Nf8GP6q8-05v9Y8C_-BGU4bTt',	1480929392,	NULL,	1533332195,	NULL,	'172.68.10.153',	1480929393,	1533332195,	0),
(239,	1,	'MuratZh',	'uv68FZSu@old-demo-mail.kz',	'$2y$12$jQ9EKVI.NYcivtuIZTyZV.6FiLrf1UhYYrvdBeqKw5eRvrvKQ3dci',	'nNqkIEsyuW_9fv5AZcKyNMXjKwb5TN8N',	1481188816,	NULL,	1494744949,	NULL,	'172.68.10.161',	1481188816,	1533332188,	0),
(240,	1,	'nazym',	'm36EtcyV@old-demo-mail.kz',	'$2y$12$JMU3HFnsrsj0hGSCNFBpA.Oq6nF7SzkR1Bt4b4lLlmgoil.jzRmnO',	'omRaTu2Fve0gtMa4Tl0x8BIg5b-W4k5n',	1483255672,	NULL,	1497628656,	NULL,	'89.218.61.218',	1483255673,	1533332188,	0),
(241,	1,	'assylzatB',	'GS257mYP@old-demo-mail.kz',	'$2y$12$muvylK36xjQXxaPF09oANei4ncazueqqz2FxdMMdmB4o16EhIN52W',	'pA1lJ9c4RtTyaKb78GNff4PJDBKP7KcW',	1483979221,	NULL,	1525317012,	NULL,	'213.157.54.78',	1483979222,	1533332188,	0),
(242,	1,	'TK-TKAZAKHSTAN',	'123456@mail.ru',	'$2y$12$L1nr0EWSJq0jA8DS.fkxa.uYMovvJFX1An5slNJOyq/oNciQD7lme',	'-OdoFJTliHCEKJW-4n5-Qa9hmzotANZv',	1484199228,	NULL,	NULL,	NULL,	'193.193.241.238',	1484199228,	1484199228,	0),
(243,	1,	'Erasyl95',	'9ZA1wgOD@old-demo-mail.kz',	'$2y$12$e.9P6Bi.82OtX5YcXHtcDebGCwAkp5FV01vM8MDE8JmGWgB9b6kkq',	'7wSaIob6ZpadMc5JhWdNG2oekZf8MujX',	1484921868,	NULL,	1503552402,	NULL,	'5.76.223.30',	1484921869,	1533332188,	0),
(244,	1,	'U-FUTURE',	'12345@mail.ru',	'$2y$12$EpyMtXuMPq5PNbCH7wCJ9uujqE3friSfea93ackeh7VCT9q9VgxK2',	'utES9lx0xYwlW9qG3NzNB56MWV6lZAhr',	1486030818,	NULL,	NULL,	NULL,	'193.193.241.238',	1486030819,	1486030819,	0),
(245,	1,	'Duman1',	'1uWvWwxj@old-demo-mail.kz',	'$2y$12$Mw1KxlBbU9DqMwm8t.SX8eCRZKmcB.X3DN77Z2H4.1RblAJbmxQl2',	'wgzq-yBB5aP2fmuEwLyUL233Jva_69un',	1488436738,	NULL,	1533332195,	NULL,	'92.46.127.7',	1488436739,	1533332195,	0),
(246,	1,	'uldanove',	'VF_WiEds@old-demo-mail.kz',	'$2y$12$NTHRf.8CG7vT2x3cx9x47eT1BMTX0uHL/qhKyYDU6F0FdRewIFzwC',	'AJaIdpxHPl168-PuX2yPdY6gwC9wSDnU',	1491189682,	NULL,	1533332196,	NULL,	'193.193.241.238',	1491189683,	1533332196,	0),
(247,	1,	'Kuralbaev',	't0p44g6b@old-demo-mail.kz',	'$2y$12$OgtHS0AADZdHwE8Mb44l1.9nNg3ZDfSOPfvw4LKwDJ0syGOSP/uqW',	'0myel_jRRHef1u4UWiTyRYH0_TDbwJ-u',	1491813660,	NULL,	1533332193,	NULL,	'193.193.241.238',	1491813661,	1533332193,	0),
(248,	1,	'tassenovaaidana',	'nypNghW8@old-demo-mail.kz',	'$2y$12$JX.OC6ikhq7Z6dQhQdvTYe.ZQJm1y2zaSdV5MhjFJ6tpu1ISYqOR6',	'nsfdEpa_eINaqc33918ve-dPeYeJlKfg',	1492703827,	NULL,	1493820411,	NULL,	'46.8.252.147',	1492703827,	1533332188,	0),
(249,	1,	'assylzatBek',	'f2GkpF2e@old-demo-mail.kz',	'$2y$12$yRqnnQ/0WevBzj5codrr2..D4Ru.m9rNoNEf1p47Z83NMkS7fFvA.',	'EM638fJREyFFiQsgVVGUEVvG20ZfCcfq',	1493820241,	NULL,	1499230653,	NULL,	'46.8.252.147',	1493820242,	1533332188,	0),
(250,	1,	'KKB_BTA',	'KKB_BTA-test@test.kz',	'$2y$12$lMmRlkQVD4AnMeOGec25u.8o8ftxao/pYRniFx.zH/UVblHFRU4ZO',	'cSppA1Y8AKvtYqCqkQDWzuibx7v_5jsZ',	1495116305,	NULL,	NULL,	NULL,	'91.79.80.179',	1495116305,	1495116305,	0),
(251,	1,	'uralsk-city-center-mall',	'Magaza.Kzk_Uralsk@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'cXevv6P01W14GnFXYXSo-YTFq64DfX9S',	1496126767,	NULL,	NULL,	NULL,	'5.34.107.159',	1496126768,	1543662354,	0),
(252,	1,	'atrau_ars',	'vXvySWgz@old-demo-mail.kz',	'$2y$12$GbXFJnelHGX2vDI6QDGpou6Yk3rmPRh5DCFf9n40xn.KIy2JeJCLy',	'IkW_wZ4yaUcXbuRxflMvMqPBoHsxRGHT',	1497256443,	NULL,	1533332193,	NULL,	'84.240.240.230',	1497256443,	1533332193,	0),
(253,	1,	'656_dyo',	'656_dyo@tupperware.su',	'$2y$12$rPraGZs3K0JTahHqm0Db4exJVJUQ4OtDBviVNAbVo91W.hDM/M7o2',	'zf8tEVuwWfERqnoKrdzaD3lz7EGqR6s9',	1497590848,	NULL,	NULL,	NULL,	'95.165.112.247',	1497590848,	1497590848,	0),
(254,	1,	'aymeken',	'LAVAocSk@old-demo-mail.kz',	'$2y$12$/GIumjtQyMeTFhzUALmxiendEQNC9aGVUz2K8m/.UJkIN94/k2m3S',	'yb60F56e8_nbSy7wFVMQnGkOhZyoi_GZ',	1497628885,	NULL,	1533332191,	NULL,	'89.218.61.218',	1497628885,	1533332191,	0),
(255,	1,	'MAyat',	'G4p-zjJP@old-demo-mail.kz',	'$2y$12$MIRhRUfMKt9DkyyGh.CNNOMVhgMRdcsJ.3FSTFDVbgYZPtTSoH5g.',	'j7KQXUwMAAagW-ppgMMcQaeivTTQ2Rzq',	1498734126,	NULL,	1532684755,	NULL,	'87.247.24.227',	1498734126,	1533332188,	0),
(256,	1,	'Miele',	'Miele-demo@demo.com',	'$2y$12$nSUCdiSYdkod/BJzpXkcRueAXeF9wrNk3qgUFV3i/DYe9xow/lJZC',	'nymJwdG8vwRi4FRn3m-Kfn3SdUWyIEU1',	1413865790,	NULL,	NULL,	NULL,	'92.47.253.129',	1491494400,	1491494400,	0),
(257,	1,	'Rulan',	'3qOFmq88@old-demo-mail.kz',	'$2y$12$pKr89acr/NmPngpCd.jDUeazUVHqWbxaSExrqVGp3qFLcURqSkXC2',	'x761XetkY5rqKeYDxwv2aaFerDxyxHKm',	1499230627,	NULL,	1533332196,	NULL,	'46.8.252.147',	1499230627,	1533332196,	0),
(258,	1,	'Yersultan',	'w4eaiqTr@old-demo-mail.kz',	'$2y$12$B4D1ezbUExTfj/LU4AsMve/AxT6MTTlx8hrbxJGJv/94P8tm4Rjba',	'vwVV4FSv3Yl-fNTKIJD7zlkiZ7RMltbA',	1499326089,	NULL,	1504942764,	NULL,	'92.47.163.182',	1499326089,	1533332188,	0),
(259,	1,	'BelarusDC',	'BelarusDC-test@nmdx.kz',	'$2y$12$HT7mFkdi2xIHCYoEI0QI6.FNjq9jUBbFL6iQtXEciGfQE9m.eBG9u',	'GPSlLSrTANy-EA5P5pHDMzPO1JZNrDfP',	1500283362,	NULL,	NULL,	NULL,	'46.138.11.29',	1500283363,	1500283363,	0),
(260,	1,	'Nurzhan11',	'cngyLPjV@old-demo-mail.kz',	'$2y$12$B/PHs4linZueIp2eaXZf9.e3Le//2bz31NjkwtLcknoVv/hBnAr/W',	'BYpsfQgP9HeSOd2FvOaS-8qSHstiFi2F',	1500288428,	NULL,	1533332189,	NULL,	'193.193.241.238',	1500288428,	1533332189,	0),
(261,	1,	'Dinagul1995',	'_pete2BV@old-demo-mail.kz',	'$2y$12$qa7OE8T179tyyujJeFXs3uKUYcxm9JQfLNPKMNbBmfpWOBHNoCVim',	'SRH2GnuHaU9-88tcf7IIvuPAkno8FQFX',	1503552555,	NULL,	1533332189,	NULL,	'178.88.99.32',	1503552555,	1533332189,	0),
(262,	1,	'HyundaiAuto',	'hyundai-auto@test.ru',	'$2y$12$0eSRiG4txxP4U/3QRWwZE.gR8wFxbBhg2rCne4B7RQayAEp1hUVje',	'I-H_wE3kywZbp5Tc8_5WHJXgUfO0CRQl',	1506657335,	NULL,	NULL,	NULL,	'185.57.72.111',	1506657336,	1510566208,	0),
(263,	1,	'HyundaiTruck',	'Hyundai-Truck-demo@nmdx.kz',	'$2y$12$bS8dJg60jWJqh2AXykRK6.yPm0SPtNwTlGDm.6aXhoKd8jwctr6dC',	'tiYsHjU1NzIXHgtvx_CiQ22Cr0acehrx',	1507261160,	NULL,	NULL,	NULL,	'213.157.58.23',	1507261160,	1511410330,	0),
(264,	1,	'SubaruAuto',	'Subaru-Auto-demo@nmdx.kz',	'$2y$12$eyCfHdg9h794E97JfzqKneiErMPZ0RqvtqfSEq3Psw0bp3UhILUZ.',	'U_ab9r2ANvxVwNj1IyH1OMW87QV7KSJY',	1507261234,	NULL,	NULL,	NULL,	'213.157.58.23',	1507261234,	1509476462,	0),
(265,	2,	'Abdukarimov',	'11123@mail.ru',	'$2y$12$QUyaPT15OmAI1hsOJ/dce.lBD1/YSKMzAEzoYGwSs.Ac2j6UJYlz2',	'BczSFtgV9Xm1_-nv9hnlGTfq8Vpex4xk',	1507803359,	NULL,	NULL,	NULL,	'213.157.58.23',	1507803359,	1507803359,	0),
(266,	2,	'Mukan',	'12123@mail.ru',	'$2y$12$HTc7Tpkm.kBFag6.07wqU.CUb2xTLJsFpRtVUdMdzz1Cra.GexJ3y',	'gb0TpXY03kAtqHRKvtdMQ7AyRpF7MKpu',	1507803495,	NULL,	NULL,	NULL,	'213.157.58.23',	1507803495,	1507803495,	0),
(267,	2,	'Baykinov',	'11323@mail.ru',	'$2y$12$2katBFv6jwUHbJAQyBPIGuKemHmiigFTf5CokttniAPUNRYnoSNHa',	'k6Fa_FoakloNXUA8gyamYKm4orZdzl6c',	1507803648,	NULL,	NULL,	NULL,	'213.157.58.23',	1507803648,	1507803648,	0),
(268,	2,	'Kaliev',	'11423@mail.ru',	'$2y$12$nsHtvHjEo.P0Kx2N2dabdutzQ7VDz0.IMAhyqg6gq5QijHN1QwjoG',	'cF46fmBCTrVMN95vluKustRvOkdSsgS0',	1507804178,	NULL,	NULL,	NULL,	'213.157.58.23',	1507804179,	1507804179,	0),
(269,	2,	'Makei',	'11523@mail.ru',	'$2y$12$Ow4dRZ64kEb9owEyhVXpYO8WjJh5opf2ZT26iTspKcwLEIpSBhbyq',	'aMAW0XZ44yykYrxapP1DMrS5wB9gTBip',	1507804337,	NULL,	NULL,	NULL,	'213.157.58.23',	1507804338,	1507804338,	0),
(270,	2,	'Kustaev',	'11623@mail.ru',	'$2y$12$vXmY3EpsYYvSVlsxPt33Q.7o43e4EpTXDZt8r.VVZhYcWNdjxj81e',	'yZpNuXuIfodtKr8Wj4Bz_4PwgEmApgRz',	1507804464,	NULL,	NULL,	NULL,	'213.157.58.23',	1507804464,	1507804464,	0),
(271,	2,	'Nuraliev',	'11723@mail.ru',	'$2y$12$a8KaFO6UY1fVvban3lL1YurdzLkG9KxIK.y7llXHNyIP1hlky2NLi',	'OwWDGsGklD-6IoYY6WZF4DA03396gjCV',	1507804588,	NULL,	NULL,	NULL,	'213.157.58.23',	1507804589,	1507804589,	0),
(272,	2,	'Kurkebaev',	'11823@mail.ru',	'$2y$12$SvEQgm3mWZjvma.Ga0DmQefltEFDGlm.cNEW0aR8Axqg3uFlHLjN2',	'gwVGTWdeEUoR4zsHK11IkBRaam85PLYu',	1507804683,	NULL,	NULL,	NULL,	'213.157.58.23',	1507804683,	1507804683,	0),
(273,	1,	'AmanbekAbylai',	'tFDOonG9@old-demo-mail.kz',	'$2y$12$mlXGGxAS7OpCBX0o2Kj6d.4ptJhJmdMWqI6Dzg/EdcVpe3MXcpDpu',	'kGOokIYXaSDvZcRysZ0qDyRMjcadTenn',	1507855431,	NULL,	1525273067,	NULL,	'92.46.127.7',	1507855431,	1533332188,	0),
(274,	1,	'SUB01-ALA',	'8-701-801-17-06@nmdx-test.kz',	'$2y$12$Sa1G2A2HAPJouBOu6rBAMO.JUa1Nx6YDw5cXoeOCDA2jAC8GM9t4q',	'p_kAF0AQpZGkS6RBK26sTbGowLgoSPi2',	1509619201,	NULL,	NULL,	NULL,	'213.157.58.23',	1509619202,	1509619202,	0),
(275,	1,	'SUB02-TSE',	'8-701-512-57-06@nmdx-test.kz',	'$2y$12$g8Gzph5hDuaaTxqVTUF3F.YMUb2HEr4oPBGbmbztALdPUKeN/c71e',	'_LxpnNLVMidtoOGX2f1lKxVlFCy2_v7U',	1509701830,	NULL,	NULL,	NULL,	'213.157.58.23',	1509701830,	1509701830,	0),
(276,	1,	'OFursova',	'_YZkVLma@old-demo-mail.kz',	'$2y$12$yh78XAHwED30PvHmiwBEdelKmbxXQcaQmiDWILcIIVCf1LaOKHdmS',	'qbrK3Zz2EjKDEBSynSta75k12zQ8gT30',	1511091146,	NULL,	1528813649,	NULL,	'92.47.163.182',	1511091146,	1533332188,	0),
(277,	1,	'SubaruAuto2',	'SubaruAuto2@nmdx.kz',	'$2y$12$nx86CFOahDybJoPYcm.eNuaPPgRsYe.LGKuRGnm4IUUnackeL0Dq.',	'3h9qG4ZQyTrz6OMJMh2xQayh7f3I2he4',	1511411274,	NULL,	NULL,	NULL,	'91.79.151.34',	1511411275,	1513022021,	0),
(278,	1,	'SUB13-KZO',	'mr.manar.71@mail.ru',	'$2y$12$Oi/hbqbXX5FL.cxkdmDM9uWIcLbqWhqEq2ZFvB8ZUzfMc8UiqE4jW',	'vcBjliV7VNif16XBfRryKhS6k16hEt31',	1512035583,	NULL,	NULL,	NULL,	'82.200.156.197',	1512035583,	1524884439,	0),
(279,	1,	'SUB10-KSN',	'oorlova@autodomkst.kz',	'$2y$12$7st3FcXQXht/wn/74ISfn.PrIVm8V697wr.XNV3r4MM3QAb0/JVLG',	'tGIcCQ7r9Y8tQ5Qi074TgPe8b6ptwYo-',	1512357017,	NULL,	NULL,	NULL,	'82.200.156.197',	1512357017,	1517971977,	0),
(280,	1,	'SUB03-UKK',	'krivolapov@tengri-auto.kz',	'$2y$12$HdUiLitlQCJNXfMBUqPHA.wFciyJEdyL3nDkIj.Stn7VBQgRexbjq',	'wAQZbzWxDgjOpRQSAjg-NeXHjvsEqB0d',	1517801929,	NULL,	NULL,	NULL,	'82.200.156.197',	1517801929,	1517801952,	0),
(281,	1,	'SUB14-URA',	'a.sinel\'nikov@zhayik-motors.kz',	'$2y$12$utrGnO4Q0tYUJS52antB4.KrB2TUmbfX.oNRoBu62hDUuLjIOo0R2',	'RylvSDDiI6G9cTV9QgxPasVahDhUN20p',	1519811036,	NULL,	NULL,	NULL,	'82.200.156.197',	1519811036,	1519811059,	0),
(282,	1,	'SUB04-PWQ',	'a.lavrentiy@kamkor.kz',	'$2y$12$uE136yY.fkJhno4QtZge9.quK6PhkgT7SQXa9N3jVvzx2kKZgpf4O',	'dfv9MX8YrHVbns_-zAQyoNI3j3ObuUiK',	1519976140,	NULL,	NULL,	NULL,	'82.200.156.195',	1519976141,	1519976157,	0),
(283,	1,	'Kuat_os',	'UGfPUzff@old-demo-mail.kz',	'$2y$12$nsJSLDi7NClq/Ok8BiveqemWsfrpmHH1UAdZhOVNrRkBlQjqtreTi',	'10AE9ThWaE_Z-ZDur9ZmN0tGz95iHdyK',	1522035569,	NULL,	1533332189,	NULL,	'213.157.58.23',	1522035569,	1533332189,	0),
(284,	1,	'ZHANARO',	'F0llpmW3@old-demo-mail.kz',	'$2y$12$Dd0N1vyOmaRUwRUFxDDOmOqnlgHsUO6ygApmh.hngHRoolVmW3wdG',	'2Urdj5Y2bNM2tkDOW2Q4euJIoukLq4RJ',	1524566623,	NULL,	1533332194,	NULL,	'178.89.233.138',	1524566624,	1533332194,	0),
(285,	1,	'Alexandr',	'NcXwUORJ@old-demo-mail.kz',	'$2y$12$vhsoPNq2PzF2nOI08tR.YeIVLXJKhiHeayKBPAlQsVnxGqGECXjk6',	'kQ493nWIcszsxDIKYCPOAavoVviEjD1J',	1524804787,	NULL,	1533332191,	NULL,	'88.204.254.190',	1524804787,	1533332191,	0),
(286,	1,	'yyy',	'hjYUkFu5@old-demo-mail.kz',	'$2y$12$jTvChwgYeWb3dUP6OZZeGeTDlkcrzDw8IelxGZRvisGDAdb/n2oGm',	'vhz5wNyk3EbyhHjxI59NCfPKxjNRrByL',	1525411934,	NULL,	1528441012,	NULL,	'89.40.194.225',	1525411934,	1533332188,	0),
(287,	1,	'AltynbekP',	'EgSz3-Vl@old-demo-mail.kz',	'$2y$12$6Qd2NjrjJ1H7fzzkZyDEzOp.z8k0XspNYuwROxskrTNEG8fijh2uu',	'FLVvFiZ0C--y81jbNh736UHdVvVv7UU6',	1525421082,	NULL,	1533332188,	NULL,	'92.46.127.7',	1525421082,	1533332188,	0),
(288,	1,	'SrazhdinovA',	'Ph0nym2o@old-demo-mail.kz',	'$2y$12$saPIVfnuiPEwXgpjySooJuXSDeRgZFMOWT610rZYKXjLZEfzKnNCe',	'uf0CmvFW4VKSoD9M3c-csI3ntJRQnOSF',	1525431068,	NULL,	1533332191,	NULL,	'5.63.90.38',	1525431068,	1533332191,	0),
(289,	1,	'atyrau-limon-vokzal-mall-outlet',	'Store.Kaz_limonVokzalMall@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'78q_sJ1kkqnYyz0BJyTiKbSVj_v0vdAw',	1528354517,	NULL,	NULL,	NULL,	'213.157.58.23',	1528354517,	1533332197,	0),
(290,	1,	'daulet97',	'woeCIkok@old-demo-mail.kz',	'$2y$12$CVSkbuFBb6ZR.WK6zQciYeCu11nGJU1QXtn3BTkz8ctq9m7bui.9S',	'O7kMmf9jErFNSe8UgpkL0FhLL1JAyHv0',	1528440513,	NULL,	1533332196,	NULL,	'90.143.36.216',	1528440513,	1533332196,	0),
(291,	1,	'aitbekt',	'Ah7Zx6SU@old-demo-mail.kz',	'$2y$12$1D534Dce4kmRc36cFcIiU.egXJXNdvCVqUOvwIpQKnrzLdUym0Vuy',	'59PEbbVHSanoVuJfKlqu-IOEBoSfoD-k',	1528813747,	NULL,	1533332196,	NULL,	'92.47.163.182',	1528813748,	1533332196,	0),
(292,	1,	'OlgaFursova',	'-etDgdmI@old-demo-mail.kz',	'$2y$12$Qeu1wsoPCO1FWx1049n2A.fJrmcTFGS9sBg/XhQjfB5TtTgUBMa1i',	'J3-_SRIktJOQ7Jtm53ZuL7snaW4yOzu0',	1529301662,	NULL,	1533332189,	NULL,	'213.157.58.23',	1529301662,	1533332189,	0),
(293,	1,	'NizhanNizamov',	'kAt0ehn3@old-demo-mail.kz',	'$2y$12$DIlU.TLc1SOrs9hPBxTlLeh./d0gNkmK8yxiqhm.rjh7nm3UiVs5S',	'b0uen-S_EsnwVvG8TtlJ0iE7vXtWBqXR',	1529901577,	NULL,	1533332190,	NULL,	'213.157.58.23',	1529901578,	1533332190,	0),
(294,	1,	'RauanaDanabayeva',	'Rauana.Danabayeva@defacto.com.tr',	'$2y$12$PWjgYtHdodxqUHkRb.kEZ.Q7GQvFN6jMo2BBZ.XeAFC1Gcibs//uC',	'jT2owvcrtC8OCq93B4ogzDXejD00-f83',	1529901979,	NULL,	NULL,	NULL,	'213.157.58.23',	1529901979,	1529901979,	0),
(295,	1,	'zhuldyzS',	'5CVFKWtl@old-demo-mail.kz',	'$2y$12$h7IJOiJfnnVM1Ovdbz0Xyul1Z8v1VOt.1NDtEh8QXXajMG2AodbKa',	'Fy4NIfqLSXvpDfTW07FzG3huVlEfqla2',	1532684783,	NULL,	1533332188,	NULL,	'89.218.71.50',	1532684784,	1533332188,	0),
(296,	1,	'EDUTOVA',	'Xnej1_bM@old-demo-mail.kz',	'$2y$12$FK7eCzY/ui19LM7gzLLIy.FyuUOQmTaZJMYg72iSnV.vv6JxXBL9e',	'9fh1Ern_boyWdQizswEoWdRV3ak9nw8P',	1532687618,	NULL,	1533332188,	NULL,	'92.46.127.7',	1532687618,	1533332188,	0),
(297,	1,	'ZhMurat',	'3tqmhT5P@old-demo-mail.kz',	'$2y$12$77hIxKXOscKSMjdbHAZg8OM.bknhwZFJQnSuQOvIdUmoxbeOca1Ou',	'DL-NW4SMKzm_wAv9ULFmNvlPXm4H7KAj',	1532933826,	NULL,	1533332192,	NULL,	'213.157.58.23',	1532933826,	1533332192,	0),
(298,	1,	'atakent-outlet',	'Store.kaz_atakentoutlet@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'W7FQzTDRMhZmyXJ0U9q2MgqUeoPzsnFU',	1536834991,	NULL,	NULL,	NULL,	'217.23.3.92',	1536834991,	1536834991,	0),
(299,	1,	'125edu',	'125edu-demo@nmdx.kz',	'$2y$12$29rmJl6zLi9i9nOgaGADquT4ANKAPXrBOwaEvk/VALgeoQY8GjoXS',	'FW_4r9uJDnaEvwZwWYjY_C3w2j9d8UaZ',	1539077459,	NULL,	NULL,	NULL,	'109.201.133.30',	1539077460,	1539077460,	0),
(300,	1,	'12345',	'1234@Mail.ru',	'$2y$12$T3pwGCsVXKpE40zurklH3ehd3/S3vtnXIGByg53bakparIvZx.dG6',	'PuCMtqUcU091L8EOlnd3x3_4lXBJz4g-',	1541499037,	NULL,	NULL,	NULL,	'213.157.58.23',	1541499038,	1541499038,	0),
(301,	1,	'FLO',	'flo-test@nmdx.kz',	'$2y$12$SHYIGls9gJzdjRG6A4jv3.I6pr7hzNrN.yvP7Ac4GnS9n9Xu.UYcW',	'559uetc5_DwJBEiaFfhXtfWIazILUy1m',	1556093187,	NULL,	NULL,	NULL,	'89.39.107.193',	1556093188,	1556093188,	0),
(302,	1,	'YigitalpTurgutalp',	'yigitalp.turgutalp@defacto.com.tr',	'$2y$12$mzcKEl9RMZSKSkN6uQoadeS6eFszGad8QGrHj9fuStuLp2adlJTdu',	'mzcKEl9RMZSKSkN6uQoadeS6eFszGad8',	1558321662,	NULL,	NULL,	NULL,	'',	1558321662,	1558321662,	0),
(303,	1,	'Nur',	'aamankeldy@nomadex.kz',	'$2y$12$tNPR0KnDmtSBYjPz9NfIsuJQPqrtBMy.1AdIfR602I0i56sz3iBoq',	'HXQxPgklhkoKJN0Laa71AeDS8xM9y5Is',	1574676827,	NULL,	NULL,	NULL,	'2.78.57.219',	1574676827,	1574676827,	0),
(304,	1,	'OskemenAdkRiver',	'store.kaz_oskemenrivermall@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'LRidC601Qxi5pHaPAbc5u7mqosvUDmZx',	1590578971,	NULL,	NULL,	NULL,	'149.56.28.113',	1590578971,	1590578971,	0),
(305,	1,	'astana-aruzhan-mall',	'Store.Kaz_AstanaAruzhanMall@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'jBFUOmWk-hVA2LQNGkyTNXRyUzg1R0E0',	1592295254,	NULL,	NULL,	NULL,	'213.157.58.23',	1592295254,	1592295254,	0),
(306,	1,	'taraz-mart',	'Store.Kaz_TarazMartMall@defacto.com.tr',	'$2y$12$SyzitHxXs.WDdVGmiS7e8ObHemA/gvlvJcYuY08wA8MuOvjBldpP2',	'neTrOYK-0C6LWf0D8E9xnayqMogV8p15',	1607589378,	NULL,	NULL,	NULL,	'176.64.34.19',	1607589379,	1607589379,	0),
(307,	1,	'Azamat123',	'aaaa@mail.ru',	'$2y$12$LXkwUCqP6bjfJVETGM5LJuamGISHFWFTszzCE3ndC53KpXDMCOKRK',	'h5OJ7q4ZAmMjIItrwwzuGxE5QBnDgUSM',	1676306317,	NULL,	NULL,	NULL,	'188.246.241.38',	1676306317,	1676306317,	0),
(308,	1,	'eren_retail',	'eren_retail-test@test.test',	'$2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w97',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(309,	1,	'Botagoz',	'Botagoz-test@test.test',	'$2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w98',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(310,	1,	'aktobe-keruen-city',	'aktobe-keruen-city@test.test',	'$2y$12$DRv1Sx2ye4ukoqsOkw6Tuejfy4aj1CEEhNPWlm8Fi9LdQ6Lzja.8m',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w98',	1699291560,	'!@#-aktobe-123',	NULL,	'1',	'46.138.146.169',	1699291560,	1699291560,	0),
(311,	1,	'almaty-esentai',	'almaty-esentai@test.test',	'$2y$12$cy5A6123tnxW07nbJS5mp.Z8JRkkMnkbMoEUGUlz6R.2L4/wKFbO2',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w90',	1699291560,	'!@#-almaty-123',	NULL,	'1',	'46.138.146.169',	1699291560,	1699291560,	0),
(312,	1,	'almaty-aport-east',	'almaty-aport-east@test.test',	'$2y$12$f3oJrIeOTR/B0AMeXtRZiuWiMyzIA7a.BwfJ1cJwJch/N/nnm/byG',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w91',	1699291560,	'!@#AlmatyAp-123',	NULL,	'1',	'46.138.146.169',	1699291560,	1699291560,	0),
(313,	1,	'almaty-superstep-ae',	'almaty-superstep-ae@test.test',	'$2y$12$BaL.Z35Xy0cO0.MQth47ief21i45.M.0m9dflXh0kpmDjpyAHzVSC',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w92',	1699291560,	'!@#AlmatyApAE-123',	NULL,	'1',	'46.138.146.169',	1699291560,	1699291560,	0),
(314,	1,	'Logistic',	'Logistic-test@test.test',	'$2y$12$OL6rAR83oWUImpYndSbs5u56rbtYCSw1lHs3.y1JwAt6.2bFMHu6m',	'pYndSbs5u56rbtYCSw1lHs3.y1JwAt6.',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(315,	1,	'Oxana',	'oxana.garmasheva@erenretail.kz',	'$2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w91',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(316,	1,	'Makhinur',	'Makhinur-test@test.test',	'$2y$12$oVo1lvN8q/TZDdqfMRfLFO2NYovPeD4cc7Eyyv4qVGMOHD9kb.ORi',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w96',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(317,	1,	'AzamatIntermode',	'azamat.zholdasbekov@erenretail.kz',	'$2y$12$nSUCdiSYdkod/BJzpXkcRueAXeF9wrNk3qgUFV3i/DYe9xow/lJZC',	'nymJwdG8vwRi4FRn3m-Kfn3SdUWyIEUJ',	1413865790,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865791,	1444065179,	0),
(318,	1,	'Ulan',	'rustem.ulan@defacto.com',	'$2y$12$5inm4nGAm8ycZ3h9CBcwl.mJanDrGal4saUJVNR08rJXNwXJ3lD42',	'nymJwdG8vwRi4FRn3m-Kfn3SdUWyIEUJ',	1413865790,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865791,	1444065179,	0),
(319,	1,	'Arlan',	'test@defacto.com.tr',	'$2y$12$IKNuv9XtQ9HzJez7yW2B5eF0D0ODJwVaFAjLnV3qnhKMrse0emxeO',	'nymJwdG8vwRi4FRn3m-Kfn3SdUWyIEUo',	1413865790,	NULL,	NULL,	NULL,	'92.47.253.129',	1413865791,	1444065179,	0),
(320,	1,	'aport-mall-east-almaty',	'aport-mall-east-almaty@erenretail.kz',	'$2y$12$EloxEuBy2aSpbnb1.Q6E4eD.2apgAYeqHJa3oLLVWRkK7rvDz8soa',	'aport-mall-east-almaty',	1413865790,	'#user%0',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(321,	1,	'super-kids-khan-shatyr-astana',	'super-kids-khan-shatyr-astana@erenretail.kz',	'$2y$12$jNG9HrYonUqxgU1EF7GGNuTadkRXKmYzxMGxxomZcXpevVrWO2oBa',	'super-kids-khan-shatyr-astana',	1413865790,	'#user%1',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(322,	1,	'superstep-dostyk-plaza-almaty',	'superstep-dostyk-plaza-almaty@erenretail.kz',	'$2y$12$jpcOvIhnRZBQI8xYUmxdYuCZREhMNUtiRJ5hvxK7mVsbYlgnn8HGK',	'superstep-dostyk-plaza-almaty',	1413865790,	'#user%2',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(323,	1,	'converse-mega-center-almaty',	'converse-mega-center-almaty@erenretail.kz',	'$2y$12$gyGaeWfHGMNizWgCSgEV8Oa/q9ImLpI3N1eNKkJ3bRpUC4XVFADZq',	'converse-mega-center-almaty',	1413865790,	'#user%3',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(324,	1,	'superstep-shymkent-plaza-shimkent',	'superstep-shymkent-plaza-shimkent@erenretail.kz',	'$2y$12$r2iPp5l79t9yPS4qOXHJ0OFaiFFLY7RvKfmz7s5WMMAf19yjQkPja',	'superstep-shymkent-plaza-shimken',	1413865790,	'#user%4',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(325,	1,	'lacoste-shymkent-plaza-shimkent',	'lacoste-shymkent-plaza-shimkent@erenretail.kz',	'$2y$12$jGMUxObCHi2MHVaAuFb4BO6r0JAZZHTTsyL.HwAvlpFXPvPhFsPiW',	'lacoste-shymkent-plaza-shimkent',	1413865790,	'#user%5',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(326,	1,	'superstep-infinity-mall-atyrau',	'superstep-infinity-mall-atyrau@erenretail.kz',	'$2y$12$7NWYptwB18IlvKZTb6/mgOGpktyCias/g3rOgS5Xzqe/DXlYK8.xe',	'superstep-infinity-mall-atyrau',	1413865790,	'#user%6',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(327,	1,	'lacoste-infinity-mall-atyrau',	'lacoste-infinity-mall-atyrau@erenretail.kz',	'$2y$12$GvIzIbCV7ycXWYGrJFqI1eKqrfXy7CXki7UObJWcWaAu/oZEv.tq2',	'lacoste-infinity-mall-atyrau',	1413865790,	'#user%7',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(328,	1,	'hoss-mega-center-almaty',	'hoss-mega-center-almaty@erenretail.kz',	'$2y$12$sECR4jbPMVCD.wMC/jdVy.yi3RN9N2obB4.zGhFtazHMPWW53boli',	'hoss-mega-center-almaty',	1413865790,	'#user%8',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(329,	1,	'superkids-silk-way-astana',	'superkids-silk-way-astana@erenretail.kz',	'$2y$12$GywYKuL1A.Gs9i5sBaOaI.BThnKcWzAcU45669OjH89Hhf7HNO/fS',	'superkids-silk-way-astana',	1413865790,	'#user%9',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(330,	1,	'lacoste-mega-center-almaty',	'lacoste-mega-center-almaty@erenretail.kz',	'$2y$12$PfgS9Ud7Y48/yvwIB4yIieRWuRWuj7L/vZGzFFKow9Tbv/1UWyxgW',	'lacoste-mega-center-almaty',	1413865790,	'#user%10',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(331,	1,	'converse-forum-almaty',	'converse-forum-almaty@erenretail.kz',	'$2y$12$fTWHi8R9KE19/zdSJ4FD9.2.98cPOB320TcnoK4OZeQrMsce762pO',	'converse-forum-almaty',	1413865790,	'#user%11',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(332,	1,	'converse-khan-shatyr-astana',	'converse-khan-shatyr-astana@erenretail.kz',	'$2y$12$Uv9d37ohRNPohyaTtuTs.uTHbqc.yrwydrDgnFpXlx14iBGSKySnS',	'converse-khan-shatyr-astana',	1413865790,	'#user%12',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(333,	1,	'lacoste-mega-silk-way-astana',	'lacoste-mega-silk-way-astana@erenretail.kz',	'$2y$12$6A8fRMLo1s8i.PzjoHwVE.Q/gkl7mWYgQbmhXF6A0MrPsP6tFBU5O',	'lacoste-mega-silk-way-astana',	1413865790,	'#user%13',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(334,	1,	'superstep-forum-almaty',	'superstep-forum-almaty@erenretail.kz',	'$2y$12$85Exp/b7BiXlA2DeKJzd/e5EoZ5wNbdrGyMFMMZcnt8B22SpD2f0e',	'superstep-forum-almaty',	1413865790,	'#user%14',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(335,	1,	'lacoste-forum-almaty',	'lacoste-forum-almaty@erenretail.kz',	'$2y$12$3ojg2CjQ.eyiS1nK7LBSNe1PeYOJQEPReVwfyBCQxedUWLrd9ALq6',	'lacoste-forum-almaty',	1413865790,	'#user%15',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(336,	1,	'hoss-mega-astana',	'hoss-mega-astana@erenretail.kz',	'$2y$12$6Wy9tGUHPjVDntcGLARoCO7nH0duVUG1e08qLLRPnJopGcqCAIYRW',	'hoss-mega-astana',	1413865790,	'#user%16',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(337,	1,	'superstep-karavan-saray-turkestan',	'superstep-karavan-saray-turkestan@erenretail.kz',	'$2y$12$wgi1Fz/79nDk.27w9lko9.DOie1kCIaR2MGheKcWrGmkCTS.xVD.a',	'superstep-karavan-saray-turkesta',	1413865790,	'#user%17',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(338,	1,	'superstep-khan-shatyr-astana',	'superstep-khan-shatyr-astana@erenretail.kz',	'$2y$12$rIq5/tLwbUcQo/2CxDH57.fipnXK4mAYimRBPraKMDnp0vyrkiUkq',	'superstep-khan-shatyr-astana',	1413865790,	'#user%18',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(339,	1,	'converse-mega-silk-way-astana',	'converse-mega-silk-way-astana@erenretail.kz',	'$2y$12$ydCMgMRojK6I0m76c2C2FueorFV65lGmlCYqSfdXItFC.tfoJtSPm',	'converse-mega-silk-way-astana',	1413865790,	'#user%19',	NULL,	'1',	'92.47.253.129',	1413865791,	1444065179,	0),
(340,	1,	'Mariya.miller',	'mariya.miller@erenretail.kz',	'$2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2',	'JtAIo6JcUHqfkDF1od33HS6R2vSS7w98',	1699291560,	NULL,	NULL,	NULL,	'46.138.146.169',	1699291560,	1699291560,	0),
(341,	2,	'Intermode1C',	'Intermode1C@teset.test',	'$2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2',	'GDs0NmqkZT4nHfelVSAG2mQJXpyB3O6',	1411532599,	'',	NULL,	'',	'',	1411532599,	1528874749,	0),
(342,	2,	'Warr1or',	'nur1k.1995@yandex.kz',	'$2y$12$uRvvs3haSp8gpDVzSxtxHO6hP7A5B90Hig.9o/6nGM9bB00aR6Hdm',	'iDXdCRdVaZTCrcl81jOd-HMplGJv2eqy',	1752056858,	NULL,	1752056936,	NULL,	'151.236.194.69',	1752056859,	1752056859,	0);


-- 2025-11-05 14:08:40
