<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

$sql="select show_print from gks_assets_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_service_photo` (
  `id_asset_service_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assets_service_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_asset_service_photo`),
  KEY `assets_service_id` (`assets_service_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_whi_mov_photo` (
  `id_assets_whi_mov_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assets_whi_mov_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_assets_whi_mov_photo`),
  KEY `assets_whi_mov_id` (`assets_whi_mov_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select show_print from gks_transfer_oxima_type_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}


$sql="select show_print from gks_eshop_products_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}


$sql="select show_print from gks_hotel_room_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_room_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}

$sql="select show_print from gks_hotel_room_type_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_room_type_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}

$sql="select show_print from gks_eshop_products_brands_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products_brands_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
  
  gks_run_sql("ALTER TABLE `gks_eshop_products_brands_photo` 
  CHANGE COLUMN `brand_photo_url` `photo_url` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  DROP INDEX `brand_photo_url`,
  ADD INDEX `photo_url` USING BTREE(`photo_url`(190));");
  
}

$sql="select show_print from gks_eshop_products_categories_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products_categories_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
  
  gks_run_sql("ALTER TABLE `gks_eshop_products_categories_photo` 
  CHANGE COLUMN `category_photo_url` `photo_url` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  DROP INDEX `category_photo_url`,
  ADD INDEX `photo_url` USING BTREE(`photo_url`(190));");
  
}

$sql="select show_print from gks_users_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users_photo` 
  ADD COLUMN `show_print` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX show_print (show_print),
  ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX filesobjectlist (filesobjectlist);");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi_photo` (
  `id_poi_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poi_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_poi_photo`),
  KEY `poi_id` (`poi_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_production_ergasies_photo` (
  `id_production_ergasia_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `production_ergasia_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_production_ergasia_photo`),
  KEY `production_ergasia_id` (`production_ergasia_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_warehouses_photo` (
  `id_warehouse_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_warehouse_photo`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_pricelist_photo` (
  `id_transfer_pricelist_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_pricelist_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_pricelist_photo`),
  KEY `transfer_pricelist_id` (`transfer_pricelist_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");





gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_pos_photo` (
  `id_pos_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pos_photo`),
  KEY `pos_id` (`pos_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_photo` (
  `id_company_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_company_photo`),
  KEY `company_id` (`company_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_subs_photo` (
  `id_company_sub_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_company_sub_photo`),
  KEY `company_sub_id` (`company_sub_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_service_reasons_photo` (
  `id_assets_service_reasons_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assets_service_reasons_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_assets_service_reasons_photo`),
  KEY `assets_service_reasons_id` (`assets_service_reasons_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_journal_photo` (
  `id_acc_journal_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acc_journal_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_journal_photo`),
  KEY `acc_journal_id` (`acc_journal_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_photo` (
  `id_transfer_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_photo`),
  KEY `transfer_id` (`transfer_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_channel_sale_photo` (
  `id_crm_channel_sale_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `crm_channel_sale_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_crm_channel_sale_photo`),
  KEY `crm_channel_sale_id` (`crm_channel_sale_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_production_bom_photo` (
  `id_production_bom_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `production_bom_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_production_bom_photo`),
  KEY `production_bom_id` (`production_bom_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_leads_status_photo` (
  `id_crm_lead_status_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `crm_lead_status_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_crm_lead_status_photo`),
  KEY `crm_lead_status_id` (`crm_lead_status_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_custom_table_photo` (
  `id_custom_table_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `custom_table_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_custom_table_photo`),
  KEY `custom_table_id` (`custom_table_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_price_photo` (
  `id_hotel_price_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_price_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_hotel_price_photo`),
  KEY `hotel_price_id` (`hotel_price_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_users_groups_photo` (
  `id_users_group_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_group_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_users_group_photo`),
  KEY `users_group_id` (`users_group_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_area_photo` (
  `id_transfer_area_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_area_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_area_photo`),
  KEY `transfer_area_id` (`transfer_area_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_availability_photo` (
  `id_hotel_availability_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_availability_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_hotel_availability_photo`),
  KEY `hotel_availability_id` (`hotel_availability_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_production_posta_photo` (
  `id_production_posto_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `production_posto_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_production_posto_photo`),
  KEY `production_posto_id` (`production_posto_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_print_forms_photo` (
  `id_print_form_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `print_form_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_print_form_photo`),
  KEY `print_form_id` (`print_form_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_tasks_status_photo` (
  `id_crm_task_status_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `crm_task_status_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_crm_task_status_photo`),
  KEY `crm_task_status_id` (`crm_task_status_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_type_photo` (
  `id_asset_type_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_asset_type_photo`),
  KEY `asset_type_id` (`asset_type_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_floor_photo` (
  `id_hotel_floor_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_floor_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_hotel_floor_photo`),
  KEY `hotel_floor_id` (`hotel_floor_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi_type_photo` (
  `id_poi_type_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poi_type_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_poi_type_photo`),
  KEY `poi_type_id` (`poi_type_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_photo` (
  `id_hotel_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_hotel_photo`),
  KEY `hotel_id` (`hotel_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_seires_photo` (
  `id_acc_seira_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acc_seira_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_seira_photo`),
  KEY `acc_seira_id` (`acc_seira_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi_diadromes_photo` (
  `id_poi_diadromes_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poi_diadromes_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_poi_diadromes_photo`),
  KEY `poi_diadromes_id` (`poi_diadromes_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshops_photo` (
  `id_eshop_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eshop_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_eshop_photo`),
  KEY `eshop_id` (`eshop_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select booking_reservation_id from gks_hotel_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
  ADD COLUMN `booking_reservation_id` varchar(128) DEFAULT NULL,
  ADD INDEX booking_reservation_id (booking_reservation_id);");
}

$sql="select cm_roomReservationId from gks_hotel_reservation_room limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation_room` 
  ADD COLUMN `cm_roomReservationId` varchar(128) DEFAULT NULL,
  ADD INDEX cm_roomReservationId (cm_roomReservationId);");
}

$sql="select * from gks_crm_channel_sale where id_crm_channel_sale=23";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_channel_sale` (`id_crm_channel_sale`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_channel_sale_descr`,
  `crm_channel_sale_sortorder`,`crm_channel_sale_disabled`,`crm_channel_has_text`,`crm_channel_has_contact`,
  `crm_channel_has_contact_filter`,`crm_channel_has_campain`,`crm_channel_has_url`,crm_channel_has_code) VALUES 
 (23,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Expedia',23,0,1,0,'',0,0,1),
 (24,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Agoda',  24,0,1,0,'',0,0,1);");
}


$sql="select booking_room_type_id from gks_hotel_room_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_room_type` 
  ADD COLUMN `booking_room_type_id` varchar(128) DEFAULT NULL,
  ADD INDEX booking_room_type_id (booking_room_type_id),
  ADD COLUMN `expedia_room_type_id` varchar(128) DEFAULT NULL,
  ADD INDEX expedia_room_type_id (expedia_room_type_id),
  ADD COLUMN `airbnb_room_type_id` varchar(128) DEFAULT NULL,
  ADD INDEX airbnb_room_type_id (airbnb_room_type_id),
  ADD COLUMN `agoda_room_type_id` varchar(128) DEFAULT NULL,
  ADD INDEX agoda_room_type_id (agoda_room_type_id);");
}

$sql="select hotel_id_expedia from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel` 
  ADD COLUMN `hotel_id_expedia` varchar(48) DEFAULT NULL,
  ADD INDEX hotel_id_expedia (hotel_id_expedia),
  ADD COLUMN `hotel_id_agoda` varchar(48) DEFAULT NULL,
  ADD INDEX hotel_id_agoda (hotel_id_agoda);");
}



$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_ZODOMUS_MODE_LIVE') === false) {
  echo '_current/_config.php file not contains GKS_ZODOMUS_MODE_LIVE<br>';die();}

if (strpos($read_file, 'mydataapidev') === false) {
  echo '_current/_config.php file not contains define(\'GKS_AADE_MYDATA_URL_TEST\',\'https://mydataapidev.aade.gr/\');<br>';die();}

if (strpos($read_file, 'mydata-dev.azure-api.net') !== false) {
  echo '_current/_config.php file contains mydata-dev.azure-api.net';die();}


$sql="select hotel_reservation_min_days_online from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel` 
  ADD COLUMN `hotel_reservation_min_days_online` int(11) NOT NULL DEFAULT 1 after hotel_reservation_days_future,
  ADD COLUMN `hotel_reservation_max_days_online` int(11) NOT NULL DEFAULT 365 after hotel_reservation_min_days_online,
  ADD COLUMN `hotel_template_eidos_descr_en_US` text DEFAULT NULL after hotel_template_eidos_descr,
  ADD COLUMN `hotel_template_woo_descr_en_US` text DEFAULT NULL after hotel_template_woo_descr,
  ADD COLUMN `hotel_template_efd_descr_en_US` text DEFAULT NULL after hotel_template_efd_descr,
  ADD COLUMN `hotel_booking_number_prefix` varchar(32) DEFAULT NULL");
}


gks_run_sql("update gks_lang set id_lang='sr-RS' where id_lang='sr_RS';");

$sql="select hotel_booking_number from gks_hotel_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
  ADD COLUMN `hotel_booking_number` varchar(64) DEFAULT NULL after reservation_guid,
  ADD INDEX hotel_booking_number (hotel_booking_number)");
}


$read_file_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_hotel/_current/_config.php';
if (file_exists($read_file_path)) {
  $read_file=file_get_contents($read_file_path);
  
  if (strpos($read_file, 'gks_hotel_api_booking_cache_version') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_hotel_api_booking_cache_version<br>';die();}
    
  if (strpos($read_file, '$gks_api_hotel_page_reservation_search=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_api_hotel_page_reservation_search=array(<br>';die();}
    
  if (strpos($read_file, '$gks_api_hotel_page_reservation_basket=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_api_hotel_page_reservation_basket=array(<br>';die();}
    
  if (strpos($read_file, '$gks_api_page_checkout=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_api_page_checkout=array(<br>';die();}
    
  if (strpos($read_file, '$gks_hotel_api_page_my_booking_form=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_hotel_api_page_my_booking_form=array(<br>';die();}
    
  if (strpos($read_file, '$gks_hotel_api_page_my_booking_item=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_hotel_api_page_my_booking_item=array(<br>';die();}
    
  if (strpos($read_file, '$gks_hotel_api_page_my_booking_hash=array(') === false) {
    echo 'wp-content/plugins/gks_hotel/_current/_config.php file not contains $gks_hotel_api_page_my_booking_hash=array(<br>';die();}
    

    
}

$sql="select * from gks_aade_xarakt_sindiasmoi_eksodon 
WHERE id_aade_xarakt_sindiasmoi_eksodon=206
AND eidos_parastatikou_aade_code='3.1'
AND aade_katigoria_xarakt_eksodon_code='category2_1'
AND aade_typos_xarakt_eksodon_code='E3_102_001'";
$result = gks_run_sql($sql);
if ($result->num_rows==1) {
  gks_run_sql("update gks_aade_xarakt_sindiasmoi_eksodon set
  aade_typos_xarakt_eksodon_code='E3_102_006'
  where id_aade_xarakt_sindiasmoi_eksodon=206");
}
  


$sql="select cancel_hash from gks_hotel_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
  ADD COLUMN cancel_hash varchar(190) DEFAULT NULL,
  ADD COLUMN cancel_until datetime DEFAULT NULL;");
}


gks_run_sql("ALTER TABLE `gks_crm_leads` MODIFY COLUMN `form_id` BIGINT NOT NULL DEFAULT 0;");
gks_run_sql("ALTER TABLE `gks_crm_tasks` MODIFY COLUMN `form_id` BIGINT NOT NULL DEFAULT 0;");


$sql="select transfer_reservation_min_hours_to_book_group_multi from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_reservation_min_hours_to_book_group_multi` INTEGER NOT NULL DEFAULT 0 after transfer_reservation_min_hours_to_book,
  ADD COLUMN transfer_reservation_group_multi_date_range text DEFAULT NULL after transfer_reservation_min_hours_to_book_group_multi;");
}


$sql="select * from gks_permission_object where id_permission_object=497";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (497,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_crm_tasks_pivot1','Pivot Table - Εργασίες',497)");
}

$sql="select * from gks_permission_object where id_permission_object=498";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("update gks_permission_object set sortorder=sortorder*10");
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (498,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_crm_leads_pivot10','Pivot Table - Ευκαιρίες',4965)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_leads_messages` (
  `id_crm_leads_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `crm_leads_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `crm_leads_message` text DEFAULT NULL,
  `email_id` int(11) NOT NULL DEFAULT 0,
  `connect_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_crm_leads_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `crm_leads_id` (`crm_leads_id`),
  KEY `user_id` (`user_id`),
  KEY `crm_leads_message` (`crm_leads_message`(250)),
  KEY `email_id` (`email_id`),
  KEY `connect_id` (`connect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select * from gks_aade_xarakt_sindiasmoi_eksodon 
WHERE id_aade_xarakt_sindiasmoi_eksodon=231
AND eidos_parastatikou_aade_code='3.1'
AND aade_katigoria_xarakt_eksodon_code='category2_7'
AND aade_typos_xarakt_eksodon_code='E3_882_001'";
$result = gks_run_sql($sql);
if ($result->num_rows==1) {
  gks_run_sql("update gks_aade_xarakt_sindiasmoi_eksodon set
  aade_typos_xarakt_eksodon_code='E3_882_002'
  where id_aade_xarakt_sindiasmoi_eksodon=231");
  
  gks_run_sql("update gks_aade_xarakt_sindiasmoi_eksodon set
  aade_typos_xarakt_eksodon_code='E3_883_002'
  where id_aade_xarakt_sindiasmoi_eksodon=232");
}



$sql="select * from gks_crm_activity_objects where id_crm_activity_object=49";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (49,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_ads_campain','Καμπάνια',49,0,'admin-ads-campain-item.php?id=%s')");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_ads_campain_photo` (
  `id_ads_campain_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ads_campain_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_ads_campain_photo`),
  KEY `ads_campain_id` (`ads_campain_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select obj_url from gks_custom_table limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_custom_table` 
  ADD COLUMN `obj_url` varchar(128) DEFAULT NULL");
  $mya=[];
  $mya[]=array(1,'admin-acc-inv.php');
  $mya[]=array(2,'admin-acc_journal.php');
  $mya[]=array(3,'admin-acc_seires.php');
  $mya[]=array(4,'admin-company.php');
  $mya[]=array(6,'admin-crm-lead.php');
  $mya[]=array(7,'admin-products.php');
  $mya[]=array(8,'admin-product-categories.php');
  $mya[]=array(9,'admin-hotel.php');
  $mya[]=array(10,'admin-hotel-availability.php');
  $mya[]=array(11,'admin-hotel-floor.php');
  $mya[]=array(12,'admin-hotel-price.php');
  $mya[]=array(13,'admin-hotel-reservation.php');
  $mya[]=array(14,'admin-hotel-room.php');
  $mya[]=array(15,'admin-hotel-room-type.php');
  $mya[]=array(16,'admin-orders.php');
  $mya[]=array(17,'admin-print_forms.php');
  $mya[]=array(18,'admin-production-ergasies.php');
  $mya[]=array(19,'admin-production-posta.php');
  $mya[]=array(20,'admin-usersgroups.php');
  $mya[]=array(21,'admin-warehouses.php');
  $mya[]=array(22,'admin-users.php');
  $mya[]=array(23,'admin-acc-pay.php');
  $mya[]=array(24,'admin-eshop.php');
  $mya[]=array(25,'admin-product-brands.php');
  $mya[]=array(26,'admin-crm-task.php');
  $mya[]=array(27,'admin-crm-machine.php');
  $mya[]=array(28,'admin-whi-mov.php');
  $mya[]=array(29,'admin-production-bom.php');
  $mya[]=array(30,'admin-ads-campain.php');
  $mya[]=array(31,'admin-urlshort.php');
  $mya[]=array(32,'admin-delivery-methods.php');
  $mya[]=array(33,'admin-payment-acquirers.php');
  $mya[]=array(35,'admin-products-lots.php');
  $mya[]=array(36,'admin-erp-app.php');
  $mya[]=array(37,'admin-pos.php');
  $mya[]=array(38,'admin-transfer.php');
  $mya[]=array(39,'admin-transfer-area.php');
  $mya[]=array(40,'admin-poi.php');
  $mya[]=array(41,'admin-poi-type.php');
  $mya[]=array(42,'admin-transfer-oxima-type.php');
  $mya[]=array(43,'admin-transfer-pricelist.php');
  $mya[]=array(44,'admin-poi-diadromes.php');
  $mya[]=array(45,'admin-transfer-reservation.php');
  $mya[]=array(46,'admin-assets.php');
  $mya[]=array(48,'admin-assets-service.php');
  $mya[]=array(49,'admin-assets-whi-mov.php');
  
  foreach ($mya as $value) {
    $sql="update gks_custom_table set obj_url='".$value[1]."' where id_custom_table=".$value[0]." limit 1";
    gks_run_sql($sql);
  }
  
  
}



$sql="select connection_ok from gks_gsis_check limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_gsis_check` 
  ADD COLUMN `connection_ok` TINYINT NOT NULL DEFAULT 0 AFTER `afm`,
  ADD INDEX `connection_ok`(`connection_ok`);");
  
  gks_run_sql("update gks_gsis_check set connection_ok=1 where valid=1 or response_call_seq_id<>''");
}



$sql="select wpml_enable from gks_eshops limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshops` 
  ADD COLUMN `wpml_enable` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `wpml_icl_language_code` VARCHAR(45) DEFAULT NULL ,
  ADD COLUMN `wpml_default_lang` VARCHAR(190) DEFAULT NULL,
  ADD COLUMN `wpml_default_lang_code` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `wpml_languages` text DEFAULT NULL,
  ADD COLUMN `woo_version` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `woo_currency` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `woo_weight_unit` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `woo_dimension_unit` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `woo_calc_taxes` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `woo_prices_include_tax` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `woo_manage_stock` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `woo_taxes` text DEFAULT NULL");
}

$sql="select remote_lang from gks_woo_product limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_woo_product` 
  ADD COLUMN `remote_lang` VARCHAR(45) DEFAULT NULL AFTER `last_update_date`,
  ADD INDEX `remote_lang`(`remote_lang`)");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_products_lang` (
  `id_product_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `product_descr` varchar(255) DEFAULT NULL,
  `product_descr_variable` varchar(250) DEFAULT NULL,
  `product_descr_small` longtext DEFAULT NULL,
  `product_descr_big` longtext DEFAULT NULL,
  PRIMARY KEY (`id_product_lang`),
  UNIQUE INDEX `myunique`(`product_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_id` (`product_id`),
  KEY `lang_code` (`lang_code`),
  KEY `product_descr` (`product_descr`(250)) USING BTREE,
  KEY `product_descr_small` (`product_descr_small`(250)) USING BTREE,
  KEY `product_descr_big` (`product_descr_big`(250)) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select * from gks_country_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_country_lang` (
    `id_country_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `country_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `country_name` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_country_lang`),
    UNIQUE INDEX `myunique`(`country_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `country_id` (`country_id`),
    KEY `lang_code` (`lang_code`),
    KEY `country_name` (`country_name`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_country_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  country_id,lang_code,country_name)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_country as country_id, 'en-US' as lang_code, country_name_en_US as country_name
  from  gks_country
  where country_name_en_US<>''");
  
  gks_run_sql("ALTER TABLE `gks_country` 
  CHANGE COLUMN `country_name_en_US` `country_name_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `country_name_en_US`");
}

$sql="select * from gks_nomoi_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_nomoi_lang` (
    `id_nomos_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `nomos_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `nomos_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_nomos_lang`),
    UNIQUE INDEX `myunique`(`nomos_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `nomos_id` (`nomos_id`),
    KEY `lang_code` (`lang_code`),
    KEY `nomos_descr` (`nomos_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_nomoi_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  nomos_id,lang_code,nomos_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_nomos as nomos_id, 'en-US' as lang_code, nomos_descr_en_US as nomos_descr
  from  gks_nomoi
  where nomos_descr_en_US<>''");
  
  gks_run_sql("ALTER TABLE `gks_nomoi` 
  CHANGE COLUMN `nomos_descr_en_US` `nomos_descr_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL");
}



$sql="select * from gks_perifereies_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_perifereies_lang` (
    `id_perifereia_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `perifereia_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `perifereia_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_perifereia_lang`),
    UNIQUE INDEX `myunique`(`perifereia_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `perifereia_id` (`perifereia_id`),
    KEY `lang_code` (`lang_code`),
    KEY `perifereia_descr` (`perifereia_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_perifereies_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  perifereia_id,lang_code,perifereia_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_perifereia as perifereia_id, 'en-US' as lang_code, perifereia_descr_en_US as perifereia_descr
  from  gks_perifereies
  where perifereia_descr_en_US<>''");
  
  gks_run_sql("ALTER TABLE `gks_perifereies` 
  CHANGE COLUMN `perifereia_descr_en_US` `perifereia_descr_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX perifereia_descr_en_US");
}


$sql="select * from gks_hotel_floor_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_hotel_floor_lang` (
    `id_hotel_floor_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `hotel_floor_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `floor_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_hotel_floor_lang`),
    UNIQUE INDEX `myunique`(`hotel_floor_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `hotel_floor_id` (`hotel_floor_id`),
    KEY `lang_code` (`lang_code`),
    KEY `floor_descr` (`floor_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_hotel_floor_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  hotel_floor_id,lang_code,floor_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_hotel_floor as hotel_floor_id, 'en-US' as lang_code, floor_descr_en_US as floor_descr
  from  gks_hotel_floor
  where floor_descr_en_US<>''");
  
  gks_run_sql("ALTER TABLE `gks_hotel_floor` 
  CHANGE COLUMN `floor_descr_en_US` `floor_descr_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL");
}




$sql="select * from gks_delivery_methods_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_delivery_methods_lang` (
    `id_delivery_method_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `delivery_method_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `delivery_method_name` varchar(255) DEFAULT NULL,
    `delivery_method_html` varchar(255) DEFAULT NULL,
    `delivery_method_sxolio` varchar(255) DEFAULT NULL,
    `delivery_method_tooltip` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_delivery_method_lang`),
    UNIQUE INDEX `myunique`(`delivery_method_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `delivery_method_id` (`delivery_method_id`),
    KEY `lang_code` (`lang_code`),
    KEY `delivery_method_name` (`delivery_method_name`(250)),
    KEY `delivery_method_html` (`delivery_method_html`(250)),
    KEY `delivery_method_sxolio` (`delivery_method_sxolio`(250)),
    KEY `delivery_method_tooltip` (`delivery_method_tooltip`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_delivery_methods_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  delivery_method_id,lang_code,delivery_method_name,delivery_method_html,delivery_method_sxolio,delivery_method_tooltip)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_delivery_method as delivery_method_id, 'en-US' as lang_code, 
  delivery_method_name_en_US as delivery_method_name,
  delivery_method_html_en_US as delivery_method_html,
  delivery_method_sxolio_en_US as delivery_method_sxolio,
  delivery_method_tooltip_en_US as delivery_method_tooltip
  
  from  gks_delivery_methods
  where delivery_method_name_en_US<>'' or delivery_method_html_en_US<>'' or delivery_method_sxolio_en_US<>'' or delivery_method_tooltip_en_US<>''");
  
  gks_run_sql("update gks_delivery_methods_lang set delivery_method_name='' where delivery_method_name is null");
  gks_run_sql("update gks_delivery_methods_lang set delivery_method_html='' where  delivery_method_html is null");
  gks_run_sql("update gks_delivery_methods_lang set delivery_method_sxolio='' where delivery_method_sxolio is null");
  gks_run_sql("update gks_delivery_methods_lang set delivery_method_tooltip='' where delivery_method_tooltip is null");
  
  
  gks_run_sql("ALTER TABLE `gks_delivery_methods` 
  CHANGE COLUMN `delivery_method_name_en_US` `delivery_method_name_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `delivery_method_html_en_US` `delivery_method_html_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `delivery_method_sxolio_en_US` `delivery_method_sxolio_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `delivery_method_tooltip_en_US` `delivery_method_tooltip_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL 
  ");
}

$sql="select * from gks_payment_acquirers_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_payment_acquirers_lang` (
    `id_payment_acquirer_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `payment_acquirer_name` varchar(255) DEFAULT NULL,
    `payment_acquirer_html` varchar(511) DEFAULT NULL,
    `payment_acquirer_button_html` varchar(511) DEFAULT NULL,
    `payment_acquirer_sxolio` varchar(255) DEFAULT NULL,
    `payment_acquirer_tooltip` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_payment_acquirer_lang`),
    UNIQUE INDEX `myunique`(`payment_acquirer_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `payment_acquirer_id` (`payment_acquirer_id`),
    KEY `lang_code` (`lang_code`),
    KEY `payment_acquirer_name` (`payment_acquirer_name`(250)),
    KEY `payment_acquirer_html` (`payment_acquirer_html`(250)),
    KEY `payment_acquirer_button_html` (`payment_acquirer_button_html`(250)),
    KEY `payment_acquirer_sxolio` (`payment_acquirer_sxolio`(250)),
    KEY `payment_acquirer_tooltip` (`payment_acquirer_tooltip`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_payment_acquirers_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  payment_acquirer_id,lang_code,payment_acquirer_name,payment_acquirer_html,payment_acquirer_button_html,payment_acquirer_sxolio,payment_acquirer_tooltip)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_payment_acquirer as payment_acquirer_id, 'en-US' as lang_code, 
  payment_acquirer_name_en_US as payment_acquirer_name,
  payment_acquirer_html_en_US as payment_acquirer_html,
  payment_acquirer_button_html_en_US as payment_acquirer_button_html,
  payment_acquirer_sxolio_en_US as payment_acquirer_sxolio,
  payment_acquirer_tooltip_en_US as payment_acquirer_tooltip
  
  from  gks_payment_acquirers
  where payment_acquirer_name_en_US<>'' or payment_acquirer_html_en_US<>'' or payment_acquirer_button_html_en_US<>'' or payment_acquirer_sxolio_en_US<>'' or payment_acquirer_tooltip_en_US<>''");
  
  gks_run_sql("update gks_payment_acquirers_lang set payment_acquirer_name='' where payment_acquirer_name is null");
  gks_run_sql("update gks_payment_acquirers_lang set payment_acquirer_html='' where  payment_acquirer_html is null");
  gks_run_sql("update gks_payment_acquirers_lang set payment_acquirer_button_html='' where  payment_acquirer_button_html is null");
  gks_run_sql("update gks_payment_acquirers_lang set payment_acquirer_sxolio='' where payment_acquirer_sxolio is null");
  gks_run_sql("update gks_payment_acquirers_lang set payment_acquirer_tooltip='' where payment_acquirer_tooltip is null");
  
  
  gks_run_sql("ALTER TABLE `gks_payment_acquirers` 
  CHANGE COLUMN `payment_acquirer_name_en_US` `payment_acquirer_name_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `payment_acquirer_html_en_US` `payment_acquirer_html_en_US_old` VARCHAR(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `payment_acquirer_button_html_en_US` `payment_acquirer_button_html_en_US_old` VARCHAR(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `payment_acquirer_sxolio_en_US` `payment_acquirer_sxolio_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `payment_acquirer_tooltip_en_US` `payment_acquirer_tooltip_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL 
  ");
}

$sql="select * from gks_hotel_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_hotel_lang` (
    `id_hotel_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `hotel_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `hotel_template_eidos_descr` text DEFAULT NULL,
    `hotel_template_woo_descr` text DEFAULT NULL,
    `hotel_template_efd_descr` text DEFAULT NULL,

    PRIMARY KEY (`id_hotel_lang`),
    UNIQUE INDEX `myunique`(`hotel_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `hotel_id` (`hotel_id`),
    KEY `lang_code` (`lang_code`),
    KEY `hotel_template_eidos_descr` (`hotel_template_eidos_descr`(250)),
    KEY `hotel_template_woo_descr` (`hotel_template_woo_descr`(250)),
    KEY `hotel_template_efd_descr` (`hotel_template_efd_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_hotel_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  hotel_id,lang_code,hotel_template_eidos_descr,hotel_template_woo_descr,hotel_template_efd_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_hotel as hotel_id, 'en-US' as lang_code, 
  hotel_template_eidos_descr_en_US as hotel_template_eidos_descr,
  hotel_template_woo_descr_en_US as hotel_template_woo_descr,
  hotel_template_efd_descr_en_US as hotel_template_efd_descr
  
  from  gks_hotel
  where hotel_template_eidos_descr_en_US<>'' or hotel_template_woo_descr_en_US<>'' or hotel_template_efd_descr_en_US<>''");
  
  gks_run_sql("update gks_hotel_lang set hotel_template_eidos_descr='' where hotel_template_eidos_descr is null");
  gks_run_sql("update gks_hotel_lang set hotel_template_woo_descr='' where  hotel_template_woo_descr is null");
  gks_run_sql("update gks_hotel_lang set hotel_template_efd_descr='' where  hotel_template_efd_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_hotel` 
  CHANGE COLUMN `hotel_template_eidos_descr_en_US` `hotel_template_eidos_descr_en_US_old` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `hotel_template_woo_descr_en_US` `hotel_template_woo_descr_en_US_old` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `hotel_template_efd_descr_en_US` `hotel_template_efd_descr_en_US_old` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
  ");
}

$sql="select * from gks_hotel_room_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_hotel_room_lang` (
    `id_hotel_room_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `hotel_room_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `room_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_hotel_room_lang`),
    UNIQUE INDEX `myunique`(`hotel_room_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `hotel_room_id` (`hotel_room_id`),
    KEY `lang_code` (`lang_code`),
    KEY `room_descr` (`room_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_hotel_room_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  hotel_room_id,lang_code,room_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_hotel_room as hotel_room_id, 'en-US' as lang_code, 
  room_descr_en_US as room_descr
  
  from  gks_hotel_room
  where room_descr_en_US<>''");
  
  gks_run_sql("update gks_hotel_room_lang set room_descr='' where room_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_hotel_room` 
  CHANGE COLUMN `room_descr_en_US` `room_descr_en_US_old` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
  ");
}


$sql="select * from gks_hotel_room_type_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_hotel_room_type_lang` (
    `id_hotel_room_type_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `hotel_room_type_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `room_type_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_hotel_room_type_lang`),
    UNIQUE INDEX `myunique`(`hotel_room_type_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `hotel_room_type_id` (`hotel_room_type_id`),
    KEY `lang_code` (`lang_code`),
    KEY `room_type_descr` (`room_type_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_hotel_room_type_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  hotel_room_type_id,lang_code,room_type_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_hotel_room_type as hotel_room_type_id, 'en-US' as lang_code, 
  room_type_descr_en_US as room_type_descr
  
  from  gks_hotel_room_type
  where room_type_descr_en_US<>''");
  
  gks_run_sql("update gks_hotel_room_type_lang set room_type_descr='' where room_type_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_hotel_room_type` 
  CHANGE COLUMN `room_type_descr_en_US` `room_type_descr_en_US_old` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
  ");
  
  gks_run_sql("ALTER TABLE `gks_hotel_room_type` 
  MODIFY COLUMN `room_type_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `room_type_descr`,
  ADD INDEX `room_type_descr` USING BTREE(`room_type_descr`(250));");

}

$sql="select * from gks_hotel_room_type_subroom_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_hotel_room_type_subroom_lang` (
    `id_hotel_room_type_subroom_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `hotel_room_type_subroom_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `subroom_descr` varchar(255) DEFAULT NULL,


    PRIMARY KEY (`id_hotel_room_type_subroom_lang`),
    UNIQUE INDEX `myunique`(`hotel_room_type_subroom_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `hotel_room_type_subroom_id` (`hotel_room_type_subroom_id`),
    KEY `lang_code` (`lang_code`),
    KEY `subroom_descr` (`subroom_descr`(190))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_hotel_room_type_subroom_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  hotel_room_type_subroom_id,lang_code,subroom_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_hotel_room_type_subroom as hotel_room_type_subroom_id, 'en-US' as lang_code, 
  subroom_descr_en_US as subroom_descr
  
  from  gks_hotel_room_type_subroom
  where subroom_descr_en_US<>''");
  
  gks_run_sql("update gks_hotel_room_type_subroom_lang set subroom_descr='' where subroom_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_hotel_room_type_subroom` 
  CHANGE COLUMN `subroom_descr_en_US` `subroom_descr_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
  ");
  
  gks_run_sql("ALTER TABLE `gks_hotel_room_type_subroom` 
  MODIFY COLUMN `subroom_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `subroom_descr`,
  ADD INDEX `subroom_descr` USING BTREE(`subroom_descr`(250));");
  
}


$sql="select * from gks_newsletter_lists_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_newsletter_lists_lang` (
    `id_newsletter_list_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `newsletter_list_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `newsletter_list_title` varchar(255) DEFAULT NULL,


    PRIMARY KEY (`id_newsletter_list_lang`),
    UNIQUE INDEX `myunique`(`newsletter_list_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `newsletter_list_id` (`newsletter_list_id`),
    KEY `lang_code` (`lang_code`),
    KEY `newsletter_list_title` (`newsletter_list_title`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_newsletter_lists_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  newsletter_list_id,lang_code,newsletter_list_title)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_newsletter_list as newsletter_list_id, 'en-US' as lang_code, 
  newsletter_list_title_en_US as newsletter_list_title
  
  from  gks_newsletter_lists
  where newsletter_list_title_en_US<>''");
  
  gks_run_sql("update gks_newsletter_lists_lang set newsletter_list_title='' where newsletter_list_title is null");
  
  
  gks_run_sql("ALTER TABLE `gks_newsletter_lists` 
  CHANGE COLUMN `newsletter_list_title_en_US` `newsletter_list_title_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  drop index newsletter_list_title_en
  ");
  
  gks_run_sql("ALTER TABLE `gks_newsletter_lists` 
  MODIFY COLUMN `newsletter_list_title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `newsletter_list_title`,
  ADD INDEX `newsletter_list_title` USING BTREE(`newsletter_list_title`(250));");

}

$sql="select mydate_add from gks_newsletter_lists limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("alter TABLE `gks_newsletter_lists`
  ADD COLUMN `mydate_add` datetime DEFAULT NULL,
  ADD COLUMN `mydate_edit` datetime DEFAULT NULL,
  ADD COLUMN `user_id_add` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `user_id_edit` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `myip` varchar(48) DEFAULT NULL,
  ADD index mydate_edit (mydate_edit),
  ADD index user_id_edit (user_id_edit)");
  
  gks_run_sql("update gks_newsletter_lists set mydate_add=now(),mydate_edit=now(),user_id_add=2,user_id_edit=2,myip='127.0.0.1';");
}

$sql="select * from gks_poi_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_poi_lang` (
    `id_poi_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `poi_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `poi_descr` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_poi_lang`),
    UNIQUE INDEX `myunique`(`poi_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `poi_id` (`poi_id`),
    KEY `lang_code` (`lang_code`),
    KEY `poi_descr` (`poi_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_poi_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  poi_id,lang_code,poi_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_poi as poi_id, 'en-US' as lang_code, 
  poi_descr_en_US as poi_descr
  
  from  gks_poi
  where poi_descr_en_US<>''");
  
  gks_run_sql("update gks_poi_lang set poi_descr='' where poi_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_poi` 
  CHANGE COLUMN `poi_descr_en_US` `poi_descr_en_US_old` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  drop index poi_descr_en_US
  ");
}

$sql="select * from gks_poi_type_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_poi_type_lang` (
    `id_poi_type_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `poi_type_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `poi_type_descr` varchar(255) DEFAULT NULL,


    PRIMARY KEY (`id_poi_type_lang`),
    UNIQUE INDEX `myunique`(`poi_type_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `poi_type_id` (`poi_type_id`),
    KEY `lang_code` (`lang_code`),
    KEY `poi_type_descr` (`poi_type_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_poi_type_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  poi_type_id,lang_code,poi_type_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_poi_type as poi_type_id, 'en-US' as lang_code, 
  poi_type_descr_en_US as poi_type_descr
  
  from  gks_poi_type
  where poi_type_descr_en_US<>''");
  
  gks_run_sql("update gks_poi_type_lang set poi_type_descr='' where poi_type_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_poi_type` 
  CHANGE COLUMN `poi_type_descr_en_US` `poi_type_descr_en_US_old` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  drop index poi_type_descr_en_US
  ");
}


$sql="select * from gks_transfer_area_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_transfer_area_lang` (
    `id_transfer_area_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `transfer_area_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `transfer_area_descr` varchar(255) DEFAULT NULL,


    PRIMARY KEY (`id_transfer_area_lang`),
    UNIQUE INDEX `myunique`(`transfer_area_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `transfer_area_id` (`transfer_area_id`),
    KEY `lang_code` (`lang_code`),
    KEY `transfer_area_descr` (`transfer_area_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_transfer_area_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  transfer_area_id,lang_code,transfer_area_descr)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_transfer_area as transfer_area_id, 'en-US' as lang_code, 
  transfer_area_descr_en_US as transfer_area_descr
  
  from  gks_transfer_area
  where transfer_area_descr_en_US<>''");
  
  gks_run_sql("update gks_transfer_area_lang set transfer_area_descr='' where transfer_area_descr is null");
  
  
  gks_run_sql("ALTER TABLE `gks_transfer_area` 
  CHANGE COLUMN `transfer_area_descr_en_US` `transfer_area_descr_en_US_old` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  drop index transfer_area_descr_en_US
  ");
  
  gks_run_sql("ALTER TABLE `gks_transfer_area` 
  MODIFY COLUMN `transfer_area_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `transfer_area_descr`,
  ADD INDEX `transfer_area_descr` USING BTREE(`transfer_area_descr`(250));");

  
}

$sql="select * from gks_transfer_oxima_type_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_transfer_oxima_type_lang` (
    `id_transfer_oxima_type_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `transfer_oxima_type_descr` varchar(255) DEFAULT NULL,
    `transfer_oxima_type_site_text` text DEFAULT NULL,

    PRIMARY KEY (`id_transfer_oxima_type_lang`),
    UNIQUE INDEX `myunique`(`transfer_oxima_type_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
    KEY `lang_code` (`lang_code`),
    KEY `transfer_oxima_type_descr` (`transfer_oxima_type_descr`(250)),
    KEY `transfer_oxima_type_site_text` (`transfer_oxima_type_site_text`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_transfer_oxima_type_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  transfer_oxima_type_id,lang_code,transfer_oxima_type_descr,transfer_oxima_type_site_text)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  id_transfer_oxima_type as transfer_oxima_type_id, 'en-US' as lang_code, 
  transfer_oxima_type_descr_en_US as transfer_oxima_type_descr,
  transfer_oxima_type_site_text_en_US as transfer_oxima_type_site_text
  
  from  gks_transfer_oxima_type
  where transfer_oxima_type_descr_en_US<>'' or transfer_oxima_type_site_text_en_US<>''");
  
  gks_run_sql("update gks_transfer_oxima_type_lang set transfer_oxima_type_descr='' where transfer_oxima_type_descr is null");
  gks_run_sql("update gks_transfer_oxima_type_lang set transfer_oxima_type_site_text='' where transfer_oxima_type_site_text is null");
  
  
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type` 
  CHANGE COLUMN `transfer_oxima_type_descr_en_US` `transfer_oxima_type_descr_en_US_old` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  CHANGE COLUMN `transfer_oxima_type_site_text_en_US` `transfer_oxima_type_site_text_en_US_old` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  drop index transfer_oxima_type_descr_en_US,
  drop index transfer_oxima_type_site_text_en_US
  ");
  
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type` 
  MODIFY COLUMN `transfer_oxima_type_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `transfer_oxima_type_descr`,
  ADD INDEX `transfer_oxima_type_descr` USING BTREE(`transfer_oxima_type_descr`(250));");
  
}


  
$sql="select * from gks_lang_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_lang_lang` (
    `id_lang_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `lang_idd` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `lang_name` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_lang_lang`),
    UNIQUE INDEX `myunique`(`lang_idd`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `lang_idd` (`lang_idd`),
    KEY `lang_code` (`lang_code`),
    KEY `lang_name` (`lang_name`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  gks_run_sql("insert into gks_lang_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  lang_idd,lang_code,lang_name)
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  idd_lang as lang_idd, 'en-US' as lang_code, 
  lang_name_en_US as lang_name
  
  from  gks_lang
  where lang_name_en_US<>''");
  
  gks_run_sql("update gks_lang_lang set lang_name='' where lang_name is null");
  
  
  gks_run_sql("ALTER TABLE `gks_lang` 
  CHANGE COLUMN `lang_name_en_US` `lang_name_en_US_old` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
  ");
 
}


$sql="select * from gks_permission_object where id_permission_object=277";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (277,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_lang','Γλώσσες',2770)");

  gks_run_sql("ALTER TABLE `gks_eshop_products_lang` 
  ADD INDEX `product_descr_variable`(`product_descr_variable`);");

}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_product_idiotites_lang` (
  `id_product_idiotita_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `product_idiotita_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `idiotita_name` varchar(250) DEFAULT NULL,
  `idiotita_descr` text DEFAULT NULL,

  PRIMARY KEY (`id_product_idiotita_lang`),
  UNIQUE INDEX `myunique`(`product_idiotita_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_idiotita_id` (`product_idiotita_id`),
  KEY `lang_code` (`lang_code`),
  KEY `idiotita_name` (`idiotita_name`),
  KEY `idiotita_descr` (`idiotita_descr`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_product_idiotites_terms_lang` (
  `id_product_idiotita_term_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `product_idiotita_term_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `idiotita_term_name` varchar(250) DEFAULT NULL,
  `idiotita_term_descr` text DEFAULT NULL,

  PRIMARY KEY (`id_product_idiotita_term_lang`),
  UNIQUE INDEX `myunique`(`product_idiotita_term_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_idiotita_term_id` (`product_idiotita_term_id`),
  KEY `lang_code` (`lang_code`),
  KEY `idiotita_term_name` (`idiotita_term_name`),
  KEY `idiotita_term_descr` (`idiotita_term_descr`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_products_categories_lang` (
  `id_product_category_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `product_category_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `product_category_descr` varchar(255) DEFAULT NULL,
  `category_comments` text DEFAULT NULL,

  PRIMARY KEY (`id_product_category_lang`),
  UNIQUE INDEX `myunique`(`product_category_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_category_id` (`product_category_id`),
  KEY `lang_code` (`lang_code`),
  KEY `product_category_descr` (`product_category_descr`(250)),
  KEY `category_comments` (`category_comments`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_products_brands_lang` (
  `id_product_brand_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `product_brand_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `product_brand_descr` varchar(255) DEFAULT NULL,
  `brand_comments` text DEFAULT NULL,

  PRIMARY KEY (`id_product_brand_lang`),
  UNIQUE INDEX `myunique`(`product_brand_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_brand_id` (`product_brand_id`),
  KEY `lang_code` (`lang_code`),
  KEY `product_brand_descr` (`product_brand_descr`(250)),
  KEY `brand_comments` (`brand_comments`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_journal_lang` (
  `id_acc_journal_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `acc_journal_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `acc_journal_descr` varchar(200) DEFAULT NULL,

  PRIMARY KEY (`id_acc_journal_lang`),
  UNIQUE INDEX `myunique`(`acc_journal_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_brand_id` (`acc_journal_id`),
  KEY `lang_code` (`lang_code`),
  KEY `acc_journal_descr` (`acc_journal_descr`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_seires_lang` (
  `id_acc_seira_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `acc_seira_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `seira_descr` varchar(200) DEFAULT NULL,
  `seira_comments` text DEFAULT NULL,

  PRIMARY KEY (`id_acc_seira_lang`),
  UNIQUE INDEX `myunique`(`acc_seira_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_brand_id` (`acc_seira_id`),
  KEY `lang_code` (`lang_code`),
  KEY `seira_descr` (`seira_descr`),
  KEY `seira_comments` (`seira_comments`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_lang` (
  `id_company_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `company_title` varchar(255) DEFAULT NULL,
  `company_tagline` varchar(200) DEFAULT NULL,
  `company_eponimia` varchar(255) DEFAULT NULL,
  `company_doy` varchar(255) DEFAULT NULL,
  `company_epaggelma` varchar(1024) DEFAULT NULL,
  `company_phone` varchar(64) DEFAULT NULL,
  `company_odos` varchar(255) DEFAULT NULL,
  `company_perioxi` varchar(255) DEFAULT NULL,
  `company_poli` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`id_company_lang`),
  UNIQUE INDEX `myunique`(`company_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_brand_id` (`company_id`),
  KEY `lang_code` (`lang_code`),
  KEY `company_title` (`company_title`(250)),
  KEY `company_tagline` (`company_tagline`),
  KEY `company_eponimia` (`company_eponimia`(250)),
  KEY `company_doy` (`company_doy`(250)),
  KEY `company_epaggelma` (`company_epaggelma`(250)),
  KEY `company_phone` (`company_phone`),
  KEY `company_odos` (`company_odos`(250)),
  KEY `company_perioxi` (`company_perioxi`(250)),
  KEY `company_poli` (`company_poli`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_subs_lang` (
  `id_company_sub_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `company_sub_title` varchar(255) DEFAULT NULL,
  `company_sub_tagline` varchar(200) DEFAULT NULL,
  `company_sub_eponimia` varchar(255) DEFAULT NULL,
  `company_sub_phone` varchar(64) DEFAULT NULL,
  `company_sub_odos` varchar(255) DEFAULT NULL,
  `company_sub_perioxi` varchar(255) DEFAULT NULL,
  `company_sub_poli` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`id_company_sub_lang`),
  UNIQUE INDEX `myunique`(`company_sub_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `product_brand_id` (`company_sub_id`),
  KEY `lang_code` (`lang_code`),
  KEY `company_sub_title` (`company_sub_title`(250)),
  KEY `company_sub_tagline` (`company_sub_tagline`),
  KEY `company_sub_eponimia` (`company_sub_eponimia`(250)),
  KEY `company_sub_phone` (`company_sub_phone`),
  KEY `company_sub_odos` (`company_sub_odos`(250)),
  KEY `company_sub_perioxi` (`company_sub_perioxi`(250)),
  KEY `company_sub_poli` (`company_sub_poli`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_warehouses_lang` (
  `id_warehouse_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `warehouse_name` varchar(255) DEFAULT NULL,
  `warehouse_phone` varchar(64) DEFAULT NULL,
  `warehouse_odos` varchar(255) DEFAULT NULL,
  `warehouse_perioxi` varchar(255) DEFAULT NULL,
  `warehouse_poli` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`id_warehouse_lang`),
  UNIQUE INDEX `myunique`(`warehouse_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `lang_code` (`lang_code`),
  KEY `warehouse_name` (`warehouse_name`(250)),
  KEY `warehouse_phone` (`warehouse_phone`),
  KEY `warehouse_odos` (`warehouse_odos`(250)),
  KEY `warehouse_perioxi` (`warehouse_perioxi`(250)),
  KEY `warehouse_poli` (`warehouse_poli`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_monades_metrisis_lang` (
  `id_monada_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `monada_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `monada_descr` varchar(255) DEFAULT NULL,
  `monada_symbol` varchar(16) DEFAULT NULL,

  PRIMARY KEY (`id_monada_lang`),
  UNIQUE INDEX `myunique`(`monada_id`, `lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `monada_id` (`monada_id`),
  KEY `lang_code` (`lang_code`),
  KEY `monada_descr` (`monada_descr`(250)),
  KEY `monada_symbol` (`monada_symbol`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");










$sql="select aade_qrurl from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `aade_qrurl` VARCHAR(256) DEFAULT NULL AFTER `aade_invoicemark`;");
}

gks_run_sql("ALTER TABLE `gks_crm_machine` 
MODIFY COLUMN `crm_machine_serial_number` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


$sql="select * from gks_print_objects where id_print_object=6";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_print_objects` (`id_print_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`object_name`,`object_descr`) VALUES 
 (6,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer_reservation','Transfer');");
}


$sql="select * from gks_transfer_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_transfer_lang` (
    `id_transfer_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `transfer_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `transfer_template_eidos_descr` text DEFAULT NULL,
    `transfer_template_woo_descr` text DEFAULT NULL,

    PRIMARY KEY (`id_transfer_lang`),
    UNIQUE INDEX `myunique`(`transfer_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `transfer_id` (`transfer_id`),
    KEY `lang_code` (`lang_code`),
    KEY `transfer_template_eidos_descr` (`transfer_template_eidos_descr`(250)),
    KEY `transfer_template_woo_descr` (`transfer_template_woo_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

}



$sql="select transfer_parastatiko_auto from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_parastatiko_auto` TINYINT NOT NULL DEFAULT 0,

  ADD COLUMN `transfer_parastatiko_apodiji_journal_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_seira_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_aade_mydata_live` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_fiscal_position_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_pricelist_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_print_file_type` varchar(16) DEFAULT 'pdf',
  ADD COLUMN `transfer_parastatiko_apodiji_print_grayscale` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_print_landscape` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_print_zoom` double NOT NULL DEFAULT 1,
  ADD COLUMN `transfer_parastatiko_apodiji_print_form_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_max_ammount` double NOT NULL DEFAULT 100,
  ADD COLUMN `transfer_parastatiko_apodiji_assigned_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_affect_balance` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_apodiji_affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT 1,
  ADD COLUMN `transfer_parastatiko_apodiji_affect_balance_all_poso_type` varchar(32) DEFAULT 'pliroteo',
  ADD COLUMN `transfer_parastatiko_apodiji_affect_balance_pros` tinyint(4) NOT NULL DEFAULT 0,
  
  ADD COLUMN `transfer_parastatiko_timologio_journal_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_seira_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_aade_mydata_live` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_fiscal_position_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_pricelist_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_print_file_type` varchar(16) DEFAULT 'pdf',
  ADD COLUMN `transfer_parastatiko_timologio_print_grayscale` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_print_landscape` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_print_zoom` double NOT NULL DEFAULT 1,
  ADD COLUMN `transfer_parastatiko_timologio_print_form_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_max_ammount` double NOT NULL DEFAULT 100,
  ADD COLUMN `transfer_parastatiko_timologio_assigned_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_affect_balance` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_parastatiko_timologio_affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT 1,
  ADD COLUMN `transfer_parastatiko_timologio_affect_balance_all_poso_type` varchar(32) DEFAULT 'pliroteo',
  ADD COLUMN `transfer_parastatiko_timologio_affect_balance_pros` tinyint(4) NOT NULL DEFAULT 0
  
  ");

}


$sql="select * from gks_assets_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_assets_lang` (
    `id_asset_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `asset_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `asset_title` varchar(255) DEFAULT NULL,

    PRIMARY KEY (`id_asset_lang`),
    UNIQUE INDEX `myunique`(`asset_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `asset_id` (`asset_id`),
    KEY `lang_code` (`lang_code`),
    KEY `asset_title` (`asset_title`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

}

$sql="select transfer_parastatiko_apodiji_email_template from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_parastatiko_apodiji_email_template` varchar(190) DEFAULT NULL,
  ADD COLUMN `transfer_parastatiko_apodiji_email_subject` varchar(190) DEFAULT NULL,
  ADD COLUMN `transfer_parastatiko_apodiji_email_from` varchar(190) DEFAULT NULL,
  
  
  ADD COLUMN `transfer_parastatiko_timologio_email_template` varchar(190) DEFAULT NULL,
  ADD COLUMN `transfer_parastatiko_timologio_email_subject` varchar(190) DEFAULT NULL,
  ADD COLUMN `transfer_parastatiko_timologio_email_from` varchar(190) DEFAULT NULL
  
  ");

}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_TRANSFER_AUTO_MYDATA_EMAIL') === false) {
  echo '_current/_config.php file not contains GKS_TRANSFER_AUTO_MYDATA_EMAIL<br>';die();}

$sql="select poi_website from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi` 
  ADD COLUMN `poi_website` VARCHAR(255) DEFAULT NULL AFTER `poi_email`;");
}

$sql="select warehouse_website from gks_warehouses limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_warehouses` 
  ADD COLUMN `warehouse_website` VARCHAR(255) DEFAULT NULL AFTER `warehouse_email`;");
}

$sql="select hotel_website from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel` 
  ADD COLUMN `hotel_website` VARCHAR(255) DEFAULT NULL AFTER `hotel_email`;");
}

$sql="select transfer_website from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_website` VARCHAR(255) DEFAULT NULL AFTER `transfer_email`;");
}

$sql="select airline_website from gks_airline limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_airline` 
  ADD COLUMN airline_iata_prefix varchar(32) DEFAULT NULL,
  ADD COLUMN airline_iata_accounting varchar(32) DEFAULT NULL,
  ADD COLUMN airline_callsign varchar(32) DEFAULT NULL,
  ADD COLUMN airline_country_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN airline_is_international tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN airline_is_cargo tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN airline_is_scheduled tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN airline_website VARCHAR(255) DEFAULT NULL,
  ADD COLUMN airline_disable tinyint(4) NOT NULL DEFAULT 0,
  
  ADD INDEX airline_iata_prefix (airline_iata_prefix),
  ADD INDEX airline_iata_accounting (airline_iata_accounting),
  ADD INDEX airline_callsign (airline_callsign),
  ADD INDEX airline_country_id (airline_country_id),
  ADD INDEX airline_is_international (airline_is_international),
  ADD INDEX airline_is_cargo (airline_is_cargo),
  ADD INDEX airline_is_scheduled (airline_is_scheduled),
  ADD INDEX airline_disable (airline_disable)
  ");
  
}


$sql="select mydate_add from gks_eshop_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist` 
  
  ADD COLUMN `mydate_add` datetime DEFAULT NULL after id_pricelist,
  ADD COLUMN `mydate_edit` datetime DEFAULT NULL after mydate_add,
  ADD COLUMN `user_id_add` int(11) NOT NULL DEFAULT 0 after mydate_edit,
  ADD COLUMN `user_id_edit` int(11) NOT NULL DEFAULT 0 after user_id_add,
  ADD COLUMN `myip` varchar(48) DEFAULT NULL after user_id_edit,

  ADD INDEX mydate_edit (mydate_edit),
  ADD INDEX user_id_edit (user_id_edit)
  ");
  gks_run_sql("update gks_eshop_pricelist set 
  mydate_add='2020-01-01 00:00:00',mydate_edit='2020-01-01 00:00:00',user_id_add=2,user_id_edit=2,myip='127.0.0.1'");
  
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist` 
  MODIFY COLUMN `pricelist_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");

  gks_run_sql("update gks_eshop_pricelist set based_pricelist_id=0");

}

$sql="select mydate_add from gks_eshop_pricelist_items limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist_items` 
  
  ADD COLUMN `mydate_add` datetime DEFAULT NULL after id_pricelist_item,
  ADD COLUMN `mydate_edit` datetime DEFAULT NULL after mydate_add,
  ADD COLUMN `user_id_add` int(11) NOT NULL DEFAULT 0 after mydate_edit,
  ADD COLUMN `user_id_edit` int(11) NOT NULL DEFAULT 0 after user_id_add,
  ADD COLUMN `myip` varchar(48) DEFAULT NULL after user_id_edit,

  ADD INDEX mydate_edit (mydate_edit),
  ADD INDEX user_id_edit (user_id_edit)
  ");
  gks_run_sql("update gks_eshop_pricelist_items set 
  mydate_add='2020-01-01 00:00:00',mydate_edit='2020-01-01 00:00:00',user_id_add=2,user_id_edit=2,myip='127.0.0.1'");
  
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist_items` 
  MODIFY COLUMN `pricelist_item_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
  
  
  
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_sociallinks_type` (
  `id_sociallinks_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `sociallinks_type_descr` varchar(190) DEFAULT NULL,
  `sociallinks_type_icon` varchar(512) DEFAULT NULL,
  `sociallinks_type_icon_email` varchar(512) DEFAULT NULL,
  `sociallinks_type_sortorder` int(11) NOT NULL DEFAULT 1000,
  `sociallinks_type_disable` tinyint(4) NOT NULL DEFAULT 0,
  `sociallinks_type_comments` text DEFAULT NULL,

  PRIMARY KEY (`id_sociallinks_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `sociallinks_type_descr` (`sociallinks_type_descr`),
  KEY `sociallinks_type_sortorder` (`sociallinks_type_sortorder`),
  KEY `sociallinks_type_disable` (`sociallinks_type_disable`)

) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_sociallinks_type limit 1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("
  INSERT INTO `gks_sociallinks_type` (`id_sociallinks_type`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`sociallinks_type_descr`,`sociallinks_type_icon`,`sociallinks_type_icon_email`,`sociallinks_type_sortorder`,`sociallinks_type_disable`,`sociallinks_type_comments`) VALUES 
 (16,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Blogger','<img src=\"/my/img/sociallinks/blogger.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/blogger.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',16,0,NULL),
 (25,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Dropbox','<img src=\"/my/img/sociallinks/dropbox.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/dropbox.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',26,0,NULL),
 (20,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Deviantart','<img src=\"/my/img/sociallinks/deviantart.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/deviantart.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',21,0,NULL),
 (14,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Discord','<img src=\"/my/img/sociallinks/discord.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/discord.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',14,0,NULL),
 (15,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Digg','<img src=\"/my/img/sociallinks/digg.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/digg.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',15,0,NULL),
 (1,'2024-02-08 17:52:03','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Facebook','<img src=\"/my/img/sociallinks/facebook.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/facebook.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',1,0,NULL),
 (8,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Dribbble','<img src=\"/my/img/sociallinks/dribbble.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/dribbble.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',8,0,NULL),
 (11,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Flickr','<img src=\"/my/img/sociallinks/flickr.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/flickr.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',11,0,NULL),
 (23,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Forrst','<img src=\"/my/img/sociallinks/forrst.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/forrst.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',24,0,NULL),
 (5,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Instagram','<img src=\"/my/img/sociallinks/instagram.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/instagram.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',5,0,NULL),
 (7,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','LinkedIn','<img src=\"/my/img/sociallinks/linkedin.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/linkedin.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',7,0,NULL),
 (19,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Myspace','<img src=\"/my/img/sociallinks/myspace.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/myspace.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',20,0,NULL),
 (24,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','PayPal','<img src=\"/my/img/sociallinks/paypal.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/paypal.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',25,0,NULL),
 (10,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Pinterest','<img src=\"/my/img/sociallinks/pinterest.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/pinterest.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',10,0,NULL),
 (22,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Reddit','<img src=\"/my/img/sociallinks/reddit.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/reddit.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',23,0,NULL),
 (9,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','RSS','<img src=\"/my/img/sociallinks/rss.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/rss.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',9,0,NULL),
 (17,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Skype','<img src=\"/my/img/sociallinks/skype.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/skype.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',17,0,NULL),
 (34,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Snapchat','<img src=\"/my/img/sociallinks/snapchat.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/snapchat.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',18,0,NULL),
 (26,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','SoundCloud','<img src=\"/my/img/sociallinks/soundcloud.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/soundcloud.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',27,0,NULL),
 (33,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Spotify','<img src=\"/my/img/sociallinks/spotify.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/spotify.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',34,0,NULL),
 (18,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Teams','<img src=\"/my/img/sociallinks/teams.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/teams.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',19,0,NULL),
 (30,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Telegram','<img src=\"/my/img/sociallinks/telegram.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/telegram.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',31,0,NULL),
 (3,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Tiktok','<img src=\"/my/img/sociallinks/tiktok.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/tiktok.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',3,0,NULL),
 (13,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Tumblr','<img src=\"/my/img/sociallinks/tumblr.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/tumblr.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',13,0,NULL),
 (2,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Twitch','<img src=\"/my/img/sociallinks/twitch.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/twitch.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',2,0,NULL),
 (4,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Twitter','<img src=\"/my/img/sociallinks/twitter.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/twitter.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',4,0,NULL),
 (12,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Vimeo','<img src=\"/my/img/sociallinks/vimeo.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/vimeo.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',12,0,NULL),
 (27,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','VK','<img src=\"/my/img/sociallinks/vk.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/vk.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',28,0,NULL),
 (28,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','WeChat','<img src=\"/my/img/sociallinks/wechat.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/wechat.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',29,0,NULL),
 (29,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','WhatsApp','<img src=\"/my/img/sociallinks/whatsapp.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/whatsapp.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',30,0,NULL),
 (31,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Xing','<img src=\"/my/img/sociallinks/xing.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/xing.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',32,0,NULL),
 (21,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Yahoo','<img src=\"/my/img/sociallinks/yahoo.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/yahoo.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',22,0,NULL),
 (32,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Yelp','<img src=\"/my/img/sociallinks/yelp.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/yelp.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',33,0,NULL),
 (6,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Youtube','<img src=\"/my/img/sociallinks/youtube.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/youtube.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',6,0,NULL),
 (35,'2024-02-08 17:41:26','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Google+','<img src=\"/my/img/sociallinks/googleplus.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/googleplus.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',35,1,NULL);");
 
 
 

}


  
$sql="select * from gks_permission_object where id_permission_object=272";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (272,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_sociallinks_type','Τύποι Συνδέσμων Κοινωνικών Δικτύων',2702),
   (273,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_sociallinks','Σύνδεσμοι Κοινωνικών Δικτύων',2703)");
}

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=51";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (50,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_lang','Γλώσσα',50,0,'admin-lang-item.php?id=%s'),
   (51,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_sociallinks_type','Τύπος Συνδέσμων Κοινωνικών Δικτύων',51,0,'admin-sociallinks-type-item.php?id=%s')");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_lang_photo` (
  `id_lang_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_idd` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_lang_photo`),
  KEY `lang_idd` (`lang_idd`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_sociallinks` (
  `id_sociallinks` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `object_name` varchar(190) DEFAULT NULL,
  `object_id` int(11) NOT NULL DEFAULT 0,
  `sociallinks_type_id` int(11) NOT NULL DEFAULT 0,
  `url` text DEFAULT NULL,

  PRIMARY KEY (`id_sociallinks`),
  UNIQUE KEY `myunique` (`object_name`,`object_id`,`sociallinks_type_id`) USING BTREE,
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `object_name` (`object_name`),
  KEY `object_id` (`object_id`),
  KEY `sociallinks_type_id` (`sociallinks_type_id`)

) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("ALTER TABLE `gks_sociallinks` 
MODIFY COLUMN `object_id` INT(11) NOT NULL DEFAULT 0,
MODIFY COLUMN `sociallinks_type_id` INT(11) NOT NULL DEFAULT 0;");




$sql="select * from gks_sociallinks ";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'wp_users',user_id,1,meta_value
  from ".GKS_WP_TABLE_PREFIX."usermeta
  where meta_value<>'' and meta_key='facebook'");
  gks_run_sql("delete from ".GKS_WP_TABLE_PREFIX."usermeta where meta_key='facebook'");
  
  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'wp_users',user_id,4,meta_value
  from ".GKS_WP_TABLE_PREFIX."usermeta
  where meta_value<>'' and meta_key='twitter'");
  gks_run_sql("delete from ".GKS_WP_TABLE_PREFIX."usermeta where meta_key='twitter'");
  
  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'wp_users',user_id,35,meta_value
  from ".GKS_WP_TABLE_PREFIX."usermeta
  where meta_value<>'' and meta_key='googleplus'");
  gks_run_sql("delete from ".GKS_WP_TABLE_PREFIX."usermeta where meta_key='googleplus'");
  
  
  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'gks_settings',1,1,myvalue
  from gks_settings
  where mykey='GKS_PAGE_FACEBOOK' and mykey<>''");
  gks_run_sql("delete from gks_settings where mykey='GKS_PAGE_FACEBOOK'");

  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'gks_settings',1,4,myvalue
  from gks_settings
  where mykey='GKS_PAGE_TWITTER' and mykey<>''");
  gks_run_sql("delete from gks_settings where mykey='GKS_PAGE_TWITTER'");

  gks_run_sql("insert into gks_sociallinks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_id,sociallinks_type_id,url  
  ) 
  select now() as mydate_add,now() as mydate_edit,2 as user_id_add,2 as user_id_edit,'127.0.0.1' as myip,
  'gks_settings',1,5,myvalue
  from gks_settings
  where mykey='GKS_PAGE_INSTAGRAM' and mykey<>''");
  gks_run_sql("delete from gks_settings where mykey='GKS_PAGE_INSTAGRAM'");

}

$folder_thump_old=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/img/_print_forms/';
$folder_thump_new=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/print_forms_preview/';
      
if (file_exists($folder_thump_old) and file_exists($folder_thump_new) == false) {
  echo 'Try move _print_forms folder<br><br>';
  if (@mkdir($folder_thump_new , 0777, true) == false ) {
    echo 'Δεν μπορεί να δημιουργηθεί ο φάκελος <b>print_forms_preview/</b> στον φάκελο uploads.';
    die();
  }
  
  $thump_files = array_diff(scandir($folder_thump_old), array('..', '.'));
  //echo '<pre>';print_r($thump_files);die();
  foreach ($thump_files as $myfile) {
    $file_old=$folder_thump_old.$myfile;
    $file_new=$folder_thump_new.$myfile;
    $ret=copy($file_old,$file_new);
    if ($ret===false) die('can not copy file '.$file_old.' to '.$file_new);
    $ret=unlink($file_old);
    if ($ret===false) die('can not delete file '.$file_old);
  } 
  $ret=rmdir($folder_thump_old);
  if ($ret===false) die('can not delete directory '.$folder_thump_old);
  
  $sql="select id_print_form,file_thump_url from gks_print_forms where file_thump_url<>'' order by id_print_form";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error);die('sql error '.$sql.' '.$db_link->errno . '-'.$db_link->error);}
  $myforms=[];
  while ($row = $result->fetch_assoc()) {
    $myforms[]=$row;
  }
  foreach ($myforms as $value) {
    $file_thump_url=$value['file_thump_url'];
    $file_thump_url=str_replace('/my/img/_print_forms/','/my/uploads/print_forms_preview/',$file_thump_url);
    $sql="update gks_print_forms set file_thump_url='".$db_link->escape_string($file_thump_url)."' where id_print_form=".$value['id_print_form'];
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error);die('sql error '.$sql.' '.$db_link->errno . '-'.$db_link->error);}
  } 
  
  echo 'Move _print_forms folder: OK<br><br>';
  
}
      

$sql="select * from gks_permission_object where id_permission_object=399";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (399,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_map','Χάρτης',3999)");
}


$sql="select * from gks_export_excel where id_export_excel=301";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {       
  

  

  gks_run_sql("INSERT INTO `gks_export_excel` 
  (`id_export_excel`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `export_excel_object`,`export_excel_descr`,`export_excel_disable`,
  `export_excel_filename_prefix`,`export_excel_start_col`,`export_excel_start_row`,
  `export_excel_data`
  ) VALUES (
  301,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
  'gks_transfer_reservation','Όλα τα πεδία',0,
  'Transfer',1,2,
  '".$db_link->escape_string(
  'a:3:{s:6:"static";a:1:{i:0;a:2:{s:1:"c";s:2:"A1";s:1:"v";s:3:"now";}}s:4:"main";a:60:{i:0;a:2:{s:1:"h";s:2:"ID";s:1:"f";s:25:"[id_transfer_reservation]";}'.
  'i:1;a:2:{s:1:"h";s:4:"guid";s:1:"f";s:27:"[transfer_reservation_guid]";}i:2;a:2:{s:1:"h";s:3:"Ref";s:1:"f";s:25:"[transfer_booking_number]";}i:3;a:2:{s:1:'.
  '"h";s:16:"Εταιρεία";s:1:"f";s:15:"[company_title]";}i:4;a:2:{s:1:"h";s:24:"Υποκατάστημα";s:1:"f";s:19:"[company_sub_title]";}i:5;a:2:{s:1:"h";s:20:"Ημερολ'.
  'όγιο";s:1:"f";s:19:"[acc_journal_descr]";}i:6;a:2:{s:1:"h";s:10:"Σειρά";s:1:"f";s:28:"[seira_code] - [seira_descr]";}i:7;a:2:{s:1:"h";s:14:"Αριθμός";s:1:"'.
  'f";s:33:"[transfer_reservation_number_int]";}i:8;a:4:{s:1:"h";s:37:"Ημερομηνία Κράτησης";s:1:"f";s:27:"[transfer_reservation_date]";s:5:"_time";i:1;s:6:"f'.
  'ormat";s:15:"dd/mm/yyyy h:mm";}i:9;a:2:{s:1:"h";s:18:"Κατάσταση";s:1:"f";s:29:"[transfer_reservation_status]";}i:10;a:4:{s:1:"h";s:33:"Ημερομηνία Έναρξη";'.
  's:1:"f";s:16:"[transfer_start]";s:5:"_time";i:1;s:6:"format";s:15:"dd/mm/yyyy h:mm";}i:11;a:4:{s:1:"h";s:29:"Ημερομηνία Λήξη";s:1:"f";s:14:"[transfer_end]'.
  '";s:5:"_time";i:1;s:6:"format";s:15:"dd/mm/yyyy h:mm";}i:12;a:2:{s:1:"h";s:21:"Διάρκεια secs";s:1:"f";s:15:"[duration_secs]";}i:13;a:2:{s:1:"h";s:16:"Ενήλ'.
  'ικες";s:1:"f";s:12:"[num_adults]";}i:14;a:2:{s:1:"h";s:12:"Παιδιά";s:1:"f";s:12:"[num_childs]";}i:15;a:2:{s:1:"h";s:10:"Βρέφη";s:1:"f";s:11:"[num_babys]";'.
  '}i:16;a:2:{s:1:"h";s:29:"Φορολογική Θέση";s:1:"f";s:23:"[fiscal_position_descr]";}i:17;a:2:{s:1:"h";s:26:"Τιμοκατάλογος";s:1:"f";s:17:"[pricelist_descr]";'.
  '}i:18;a:2:{s:1:"h";s:41:"Προεπιλεγμένη έκπτωση";s:1:"f";s:13:"[def_ekptosi]";}i:19;a:2:{s:1:"h";s:16:"Κουπόνια";s:1:"f";s:9:"[coupons]";}i:20;a:2:{s:1:"h"'.
  ';s:14:"Πελάτης";s:1:"f";s:14:"[gks_nickname]";}i:21;a:2:{s:1:"h";s:32:"Σχόλιο για πελάτη";s:1:"f";s:15:"[pelati_sxolio]";}i:22;a:2:{s:1:"h";s:40:"Σχόλιο γ'.
  'ια παραγγελία";s:1:"f";s:14:"[order_sxolio]";}i:23;a:2:{s:1:"h";s:10:"Όνομα";s:1:"f";s:17:"[user_first_name]";}i:24;a:2:{s:1:"h";s:14:"Επίθετο";s:1:"f";s:'.
  '16:"[user_last_name]";}i:25;a:2:{s:1:"h";s:5:"email";s:1:"f";s:12:"[user_email]";}i:26;a:2:{s:1:"h";s:16:"Τηλέφωνο";s:1:"f";s:13:"[user_mobile]";}i:27;a:2'.
  ':{s:1:"h";s:12:"Γλώσσα";s:1:"f";s:11:"[lang_name]";}i:28;a:2:{s:1:"h";s:35:"Τύπος παραστατικού";s:1:"f";s:13:"[parastatiko]";}i:29;a:2:{s:1:"h";s:16:"Επων'.
  'υμία";s:1:"f";s:10:"[eponimia]";}i:30;a:2:{s:1:"h";s:12:"Τίτλος";s:1:"f";s:7:"[title]";}i:31;a:2:{s:1:"h";s:6:"ΑΦΜ";s:1:"f";s:5:"[afm]";}i:32;a:2:{s:1:"h"'.
  ';s:27:"Αρχικά Xώρας ΕΕ";s:1:"f";s:12:"[country_ee]";}i:33;a:2:{s:1:"h";s:6:"ΔΟΥ";s:1:"f";s:5:"[doy]";}i:34;a:2:{s:1:"h";s:18:"Επάγγελμα";s:1:"f";s:11:"[ep'.
  'aggelma]";}i:35;a:2:{s:1:"h";s:18:"Διεύθυνση";s:1:"f";s:9:"[ma_odos]";}i:36;a:2:{s:1:"h";s:14:"Περιοχή";s:1:"f";s:12:"[ma_perioxi]";}i:37;a:2:{s:1:"h";s:8'.
  ':"Πόλη";s:1:"f";s:9:"[ma_poli]";}i:38;a:2:{s:1:"h";s:2:"TK";s:1:"f";s:7:"[ma_tk]";}i:39;a:2:{s:1:"h";s:10:"Νομός";s:1:"f";s:13:"[nomos_descr]";}i:40;a:2:{'.
  's:1:"h";s:8:"Χώρα";s:1:"f";s:14:"[country_name]";}i:41;a:2:{s:1:"h";s:19:"Από Σημείο";s:1:"f";s:16:"[poi_descr_from]";}i:42;a:2:{s:1:"h";s:17:"Σε Σημείο";'.
  's:1:"f";s:14:"[poi_descr_to]";}i:43;a:2:{s:1:"h";s:32:"Απόσταση σε μέτρα";s:1:"f";s:19:"[apostasi_se_metra]";}i:44;a:2:{s:1:"h";s:32:"Διάρκεια σε λεπτά";s'.
  ':1:"f";s:19:"[diarkeia_se_lepta]";}i:45;a:2:{s:1:"h";s:14:"Οχήματα";s:1:"f";s:19:"[products_posotita]";}i:46;a:2:{s:1:"h";s:18:"Υποσύνολο";s:1:"f";s:15:"['.
  'gks_price_net]";}i:47;a:2:{s:1:"h";s:6:"ΦΠΑ";s:1:"f";s:15:"[gks_price_fpa]";}i:48;a:2:{s:1:"h";s:23:"Μικτό σύνολο";s:1:"f";s:18:"[gks_price_netfpa]";}i:49'.
  ';a:2:{s:1:"h";s:24:"Φόροι Παρακρ.";s:1:"f";s:21:"[totalWithheldAmount]";}i:50;a:2:{s:1:"h";s:23:"Λοιποί Φόροι";s:1:"f";s:23:"[totalOtherTaxesAmount]";}i:5'.
  '1;a:2:{s:1:"h";s:48:"Ψηφιακό Τέλος συναλλαγής";s:1:"f";s:22:"[totalStampDutyamount]";}i:52;a:2:{s:1:"h";s:8:"Τέλη";s:1:"f";s:17:"[totalFeesAmount]";}i:53;a:2:{s:1:"h";s:'.
  '12:"Σύνολο";s:1:"f";s:17:"[gks_price_total]";}i:54;a:2:{s:1:"h";s:29:"Κόστος πληρωμής";s:1:"f";s:17:"[kostos_pliromis]";}i:55;a:2:{s:1:"h";s:16:"Πληρωτέο"'.
  ';s:1:"f";s:10:"[pliroteo]";}i:56;a:2:{s:1:"h";s:29:"Τρόπος πληρωμής";s:1:"f";s:23:"[payment_acquirer_name]";}i:57;a:3:{s:1:"h";s:32:"Σχόλιο από πελάτη";s:'.
  '1:"f";s:8:"[sxolio]";s:9:"max_width";i:50;}i:58;a:3:{s:1:"h";s:29:"Σχόλιο κράτησης";s:1:"f";s:8:"[sxolio]";s:9:"max_width";i:50;}i:59;a:3:{s:1:"h";s:63:"Ε'.
  'σωτερική σημείωση για λογιστήριο";s:1:"f";s:17:"[note_logistirio]";s:9:"max_width";i:50;}}s:4:"eidi";a:0:{}}'
  )."'
  )");

}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_paroxos` (
  `id_aade_paroxos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  
  `paroxos_name` varchar(190) DEFAULT NULL,
  `paroxos_url` varchar(190) DEFAULT NULL,
  `paroxos_implemented` tinyint(4) NOT NULL DEFAULT 0,
  `paroxos_sortorder` int(11) NOT NULL DEFAULT 1000,
  `paroxos_need_username` tinyint(4) NOT NULL DEFAULT 0,
  `paroxos_need_password` tinyint(4) NOT NULL DEFAULT 0,
  `paroxos_need_key` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_aade_paroxos`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `paroxos_name` (`paroxos_name`),
  KEY `paroxos_implemented` (`paroxos_implemented`),
  KEY `paroxos_sortorder` (`paroxos_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_aade_paroxos where id_aade_paroxos=20";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO `gks_aade_paroxos` 
  (`id_aade_paroxos`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `paroxos_name`,`paroxos_url`,paroxos_implemented,paroxos_sortorder,paroxos_need_username,paroxos_need_password,paroxos_need_key) 
  VALUES 
  (1,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','EPSILONDIGITAL','https://www.epsilondigital.gr/',0,3,0,0,0),
  (2,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','MYDATA CONNECT','https://mydata.wedoconnect.com/',0,4,0,0,0),
  (3,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','SoftOne EINVOICΙNG','https://www.einvoicing.gr/',0,5,0,0,0),
  (4,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Entersoft e-Invoicing','https://www.e-invoicing.gr/',0,6,0,0,0),
  (5,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Oxygen-Pelatologio','https://www.pelatologio.gr/',0,7,0,0,0),
  (6,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','timologisi.online','https://timologisi.online/',0,8,0,0,0),
  (7,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Primer MyData','http://www.primer.gr/',0,9,0,0,0),
  (8,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Meg myData ','https://www.ilyda.com/',0,10,0,0,0),
  (9,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','PROSVASISGO eInvoicing','https://go.prosvasis.com/',0,11,0,0,0),
  (10,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','MAT','https://www.rapidsign.gr/',0,2,0,0,0),
  (11,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','SIMPLY','https://www.simplypos.com/el/',0,12,0,0,0),
  (12,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','KAPPA DEVELOPMENT','https://www.kappadevelopment.gr/',0,13,0,0,0),
  (13,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','FREEEXTRA I K E','https://www.sbzsystems.com/',0,14,0,0,0),
  (14,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ONESYS IT SOLUTIONS','https://onesys.gr/',0,15,0,0,0),
  (15,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ORIAN ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ','https://orian.gr/',0,16,0,0,0),
  (16,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ΤΕΣΑΕ Α.Τ.Ε.','https://e-invoicing.pegcloud.io/',0,17,0,0,1),
  (17,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Karpodinis Software ΑΕ','https://www.karpodinis.gr/',0,18,0,0,0),
  (18,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Information systems IMPACT Μον. AE','https://einvoicing.gr/',0,19,0,0,0),
  (19,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ΤΣΟΥΚΑΚΗΣ ΜΟΝ. ΙΚΕ','https://cloudt.gr/',0,20,0,0,0),
  (20,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάροχος Λύσεων Πληροφορικής Α.Ε.','https://www.parochos.gr/',0,1,1,1,1);");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_paroxos` (
  `id_company_paroxos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `aade_paroxos_id` int(11) NOT NULL DEFAULT 0,
  `paroxos_send` tinyint(4) NOT NULL DEFAULT 0,
  `paroxos_mydata_live` tinyint(4) NOT NULL DEFAULT 0,
  `paroxos_branch` int(11) NOT NULL DEFAULT '-1',
  `pc_username` varchar(190) DEFAULT NULL,
  `pc_password` varchar(190) DEFAULT NULL,
  `pc_key` varchar(190) DEFAULT NULL,
  `pc_token_id` text DEFAULT NULL,
  `pc_token_expiration` datetime DEFAULT NULL,
  `pc_refresh_token_id` text DEFAULT NULL,
  `pc_refresh_token_expiration` datetime DEFAULT NULL,
  `pc_item_identifier` varchar(190) DEFAULT NULL,
  `pc_item_family_identifier` varchar(190) DEFAULT NULL,
  `pc_app_identifier` varchar(190) DEFAULT NULL,
  `pc_url1` varchar(190) DEFAULT NULL,
  `pc_url2` varchar(190) DEFAULT NULL,

  `sandbox_pc_username` varchar(190) DEFAULT NULL,
  `sandbox_pc_password` varchar(190) DEFAULT NULL,
  `sandbox_pc_key` varchar(190) DEFAULT NULL,
  `sandbox_pc_token_id` text DEFAULT NULL,
  `sandbox_pc_token_expiration` datetime DEFAULT NULL,
  `sandbox_pc_refresh_token_id` text DEFAULT NULL,
  `sandbox_pc_refresh_token_expiration` datetime DEFAULT NULL,
  `sandbox_pc_item_identifier` varchar(190) DEFAULT NULL,
  `sandbox_pc_item_family_identifier` varchar(190) DEFAULT NULL,
  `sandbox_pc_app_identifier` varchar(190) DEFAULT NULL,
  `sandbox_pc_url1` varchar(190) DEFAULT NULL,
  `sandbox_pc_url2` varchar(190) DEFAULT NULL,
  
  PRIMARY KEY (`id_company_paroxos`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `company_id` (`company_id`),
  KEY `company_sub_id` (`company_sub_id`),
  KEY `aade_paroxos_id` (`aade_paroxos_id`),
  KEY `paroxos_send` (`paroxos_send`),
  KEY `paroxos_mydata_live` (`paroxos_mydata_live`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
 

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_TESAE_GR_MODE_TEST_API') === false) {
  echo '_current/_config.php file not contains GKS_TESAE_GR_MODE_TEST_API<br>';die();}
if (strpos($read_file, 'GKS_TESAE_GR_MODE_LIVE_API') === false) {
  echo '_current/_config.php file not contains GKS_TESAE_GR_MODE_LIVE_API<br>';die();}
if (strpos($read_file, 'GKS_PAROCHOS_GR_MODE_TEST_ACCOUNT') === false) {
  echo '_current/_config.php file not contains GKS_PAROCHOS_GR_MODE_TEST_ACCOUNT<br>';die();}
if (strpos($read_file, 'GKS_PAROCHOS_GR_MODE_TEST_API') === false) {
  echo '_current/_config.php file not contains GKS_PAROCHOS_GR_MODE_TEST_API<br>';die();}
if (strpos($read_file, 'GKS_PAROCHOS_GR_MODE_LIVE_ACCOUNT') === false) {
  echo '_current/_config.php file not contains GKS_PAROCHOS_GR_MODE_LIVE_ACCOUNT<br>';die();}
if (strpos($read_file, 'GKS_PAROCHOS_GR_MODE_LIVE_API') === false) {
  echo '_current/_config.php file not contains GKS_PAROCHOS_GR_MODE_LIVE_API<br>';die();}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_paroxos_log` (
  `id_company_paroxos_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `company_paroxos_id` int(11) NOT NULL DEFAULT 0,
  `p_send` longtext DEFAULT NULL,
  `p_response` longtext DEFAULT NULL,
  PRIMARY KEY (`id_company_paroxos_log`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `company_paroxos_id` (`company_paroxos_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select send_paroxos from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_seires`
  ADD COLUMN `send_paroxos` TINYINT NOT NULL DEFAULT 0 AFTER `send_mydata`,
  ADD INDEX `send_paroxos`(`send_paroxos`);");
}

$sql="select paroxos_processId from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `aade_paroxos_id` int(11) NOT NULL DEFAULT 0,
	ADD COLUMN `paroxos_processId` VARCHAR(64) DEFAULT NULL,
	ADD COLUMN `paroxos_last_response` TEXT DEFAULT NULL,
  ADD COLUMN `paroxos_status` TINYINT NOT NULL DEFAULT '-1',
	ADD COLUMN `paroxos_authenticationCode` VARCHAR(64) DEFAULT NULL,
	ADD COLUMN `paroxos_user_send` int(11) NOT NULL DEFAULT 0,
	ADD COLUMN `paroxos_date_send` datetime DEFAULT NULL,
	ADD COLUMN `paroxos_get_files` datetime DEFAULT NULL,
	ADD COLUMN `paroxos_send_pdf` datetime DEFAULT NULL,
	ADD COLUMN `paroxos_send_pdf_name` varchar(255) DEFAULT NULL,
	ADD COLUMN `paroxos_send_pdf_url` varchar(1024) DEFAULT NULL,
	ADD INDEX `aade_paroxos_id`(`aade_paroxos_id`),
	ADD INDEX `paroxos_processId`(`paroxos_processId`),
	ADD INDEX `paroxos_status`(`paroxos_status`),
	ADD INDEX `paroxos_date_send`(`paroxos_date_send`),
	ADD INDEX `paroxos_get_files`(`paroxos_get_files`),
	ADD INDEX `paroxos_send_pdf`(`paroxos_send_pdf`),
	ADD INDEX `print_file_name`(`print_file_name`(190))");
	
}

$sql="select pos_user_can_change_prices from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_user_can_change_prices` TINYINT NOT NULL DEFAULT 0 after pos_aade_mydata_live");
}

$sql="select * from gks_aade_katigoria_fpa where id_aade_katigoria_fpa=9";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_fpa (
  id_aade_katigoria_fpa,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_fpa_code,aade_katigoria_fpa_descr,aade_katigoria_fpa_pososto,sortorder,fpa_base_id
  ) values 
  (9,'2020-01-01','2020-01-01',2,2,'127.0.0.1',9,'ΦΠΑ συντελεστής 3%',0.03,9,0);");
}

$sql="select * from gks_aade_katigoria_fpa_ejeresi where id_aade_katigoria_fpa_ejeresi=25";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_fpa_ejeresi (
  id_aade_katigoria_fpa_ejeresi,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_fpa_ejeresi_code,aade_katigoria_fpa_ejeresi_descr,sortorder
  ) values 
  (25,'2020-01-01','2020-01-01',2,2,'127.0.0.1',25,'Χωρίς ΦΠΑ - ΠΟΛ.1029/1995',25),
  (26,'2020-01-01','2020-01-01',2,2,'127.0.0.1',26,'Χωρίς ΦΠΑ - ΠΟΛ.1167/2015',26),
  (27,'2020-01-01','2020-01-01',2,2,'127.0.0.1',27,'Λοιπές Εξαιρέσεις ΦΠΑ',27),
  (28,'2020-01-01','2020-01-01',2,2,'127.0.0.1',28,'Χωρίς ΦΠΑ – άρθρο 24 περ. β\' παρ.1 του Κώδικα ΦΠΑ, (Tax Free)',28),
  (29,'2020-01-01','2020-01-01',2,2,'127.0.0.1',29,'Χωρίς ΦΠΑ – άρθρο 47β, του Κώδικα ΦΠΑ (OSS μη ενωσιακό καθεστώς)',29),
  (30,'2020-01-01','2020-01-01',2,2,'127.0.0.1',30,'Χωρίς ΦΠΑ – άρθρο 47γ, του Κώδικα ΦΠΑ (OSS ενωσιακό καθεστώς)',30),
  (31,'2020-01-01','2020-01-01',2,2,'127.0.0.1',31,'Χωρίς ΦΠΑ – άρθρο 47δ του Κώδικα ΦΠΑ (IOSS)',31);");
}


$sql="select * from gks_aade_katigoria_parakratoumemenon_foron where id_aade_katigoria_parakratoumemenon_foron=16";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_parakratoumemenon_foron (
  id_aade_katigoria_parakratoumemenon_foron,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_parakratoumemenon_foron_code,aade_katigoria_parakratoumemenon_foron_descr,
  aade_katigoria_parakratoumemenon_foron_type,aade_katigoria_parakratoumemenon_foron_pososto,
  aade_katigoria_parakratoumemenon_foron_poso_fn,aade_katigoria_parakratoumemenon_foron_poso_fix,
  sortorder
  ) values 
  (16,'2020-01-01','2020-01-01',2,2,'127.0.0.1',16,'Παρακρατήσεις συναλλαγών αλλοδαπής βάσει συμβάσεων αποφυγής διπλής φορολογίας (Σ.Α.Δ.Φ.)','free',null,null,null,16),
  (17,'2020-01-01','2020-01-01',2,2,'127.0.0.1',17,'Λοιπές Παρακρατήσεις Φόρου','free',null,null,null,17),
  (18,'2020-01-01','2020-01-01',2,2,'127.0.0.1',18,'Παρακράτηση Φόρου Μερίσματα περ.α παρ. 1 αρ. 64 ν. 4172/2013 5%','pososto',5,null,null,18)");
}

$sql="select * from gks_aade_katigoria_loipon_foron where id_aade_katigoria_loipon_foron=15";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_loipon_foron (
  id_aade_katigoria_loipon_foron,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_loipon_foron_code,aade_katigoria_loipon_foron_descr,
  aade_katigoria_loipon_foron_type,aade_katigoria_loipon_foron_pososto,
  aade_katigoria_loipon_foron_poso_fn,aade_katigoria_loipon_foron_poso_fix,
  sortorder
  ) values 
  (15,'2020-01-01','2020-01-01',2,2,'127.0.0.1',15,'Ασφάλιστρα κλάδου πυρός 20%','pososto',20,null,null,15),
  (16,'2020-01-01','2020-01-01',2,2,'127.0.0.1',16,'Λοιποί Τελωνειακοί Δασμοί-Φόροι','free',null,null,null,16),
  (17,'2020-01-01','2020-01-01',2,2,'127.0.0.1',17,'Λοιποί Φόροι','free',null,null,null,17),
  (18,'2020-01-01','2020-01-01',2,2,'127.0.0.1',18,'Επιβαρύνσεις Λοιπών Φόρων','free',null,null,null,18),
  (19,'2020-01-01','2020-01-01',2,2,'127.0.0.1',19,'ΕΦΚ','free',null,null,null,19)");
}

$sql="select aade_disable from gks_aade_katigoria_loipon_foron limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_loipon_foron` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
 
  gks_run_sql("update gks_aade_katigoria_loipon_foron set aade_disable=1 where id_aade_katigoria_loipon_foron in (1,2);");
}


$sql="select aade_katigoria_xartosimou_type from gks_aade_katigoria_xartosimou limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_xartosimou` 
  ADD COLUMN `aade_katigoria_xartosimou_type` varchar(16) DEFAULT NULL AFTER `aade_katigoria_xartosimou_descr`,
  ADD COLUMN `aade_katigoria_xartosimou_poso_fn` varchar(200) DEFAULT NULL AFTER `aade_katigoria_xartosimou_pososto`,
  ADD COLUMN `aade_katigoria_xartosimou_poso_fix` double DEFAULT NULL AFTER `aade_katigoria_xartosimou_poso_fn`,
  ADD INDEX  `aade_katigoria_xartosimou_type`(`aade_katigoria_xartosimou_type`);");
  
  gks_run_sql("update gks_aade_katigoria_xartosimou set aade_katigoria_xartosimou_type='pososto' where id_aade_katigoria_xartosimou in (1,2,3)");
}

$sql="select * from gks_aade_katigoria_xartosimou where id_aade_katigoria_xartosimou=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_xartosimou (
  id_aade_katigoria_xartosimou,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_xartosimou_code,aade_katigoria_xartosimou_descr,
  aade_katigoria_xartosimou_type,aade_katigoria_xartosimou_pososto,
  aade_katigoria_xartosimou_poso_fn,aade_katigoria_xartosimou_poso_fix,
  sortorder
  ) values 
  (4,'2020-01-01','2020-01-01',2,2,'127.0.0.1',4,'Λοιπές περιπτώσεις Χαρτοσήμου','free',null,null,null,4);");
}

$sql="select * from gks_aade_katigoria_telon where id_aade_katigoria_telon=10";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_telon (
  id_aade_katigoria_telon,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_telon_code,aade_katigoria_telon_descr,
  aade_katigoria_telon_type,aade_katigoria_telon_pososto,
  aade_katigoria_telon_poso_fn,aade_katigoria_telon_poso_fix,
  sortorder
  ) values 
  (10,'2020-01-01','2020-01-01',2,2,'127.0.0.1',10,'Λοιπά τέλη','free',null,null,null,10),
  (11,'2020-01-01','2020-01-01',2,2,'127.0.0.1',11,'Τέλη Λοιπών Φόρων','free',null,null,null,11),
  (12,'2020-01-01','2020-01-01',2,2,'127.0.0.1',12,'Εισφορά δακοκτονίας (ποσό)','free',null,null,null,12),
  (13,'2020-01-01','2020-01-01',2,2,'127.0.0.1',13,'Για μηνιαίο λογαριασμό κάθε σύνδεσης (10%)','pososto',10,null,null,13),
  (14,'2020-01-01','2020-01-01',2,2,'127.0.0.1',14,'Τέλος καρτοκινητής επί της αξίας του χρόνου ομιλίας (10%)','pososto',10,null,null,14),
  (15,'2020-01-01','2020-01-01',2,2,'127.0.0.1',15,'Τέλος κινητής και καρτοκινητής για φυσικά πρόσωπα ηλικίας 15 έως και 29 ετών (0%)','pososto',0,null,null,15),
  (16,'2020-01-01','2020-01-01',2,2,'127.0.0.1',16,'Εισφορά προστασίας περιβάλλοντος πλαστικών προϊόντων 0,04 λεπτά ανά τεμάχιο [άρθρο 4 ν. 4736/2020]','function',null,'gks_user_call_fees_plastic_products',null,16),
  (17,'2020-01-01','2020-01-01',2,2,'127.0.0.1',17,'Τέλος ανακύκλωσης 0,08 λεπτά ανά τεμάχιο [άρθρο 80 ν. 4819/2021]','function',null,'gks_user_call_fees_anakiklosi',null,17),
  (18,'2020-01-01','2020-01-01',2,2,'127.0.0.1',18,'Τέλος διαμονής παρεπιδημούντων','free',null,null,null,18),
  (19,'2020-01-01','2020-01-01',2,2,'127.0.0.1',19,'Τέλος επί των ακαθάριστων εσόδων των εστιατορίων και συναφών καταστημάτων','free',null,null,null,19),
  (20,'2020-01-01','2020-01-01',2,2,'127.0.0.1',20,'Τέλος επί των ακαθάριστων εσόδων των κέντρων διασκέδασης','free',null,null,null,20),
  (21,'2020-01-01','2020-01-01',2,2,'127.0.0.1',21,'Τέλος επί των ακαθάριστων εσόδων των καζίνο','free',null,null,null,21),
  (22,'2020-01-01','2020-01-01',2,2,'127.0.0.1',22,'Λοιπά τέλη επί των ακαθάριστων εσόδων','free',null,null,null,22);");
}

$sql="select * from gks_aade_katigoria_xarakt_esodon where id_aade_katigoria_xarakt_esodon=12";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_katigoria_xarakt_esodon (
  id_aade_katigoria_xarakt_esodon,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_xarakt_esodon_code,aade_katigoria_xarakt_esodon_descr,sortorder
  ) values 
  (12,'2020-01-01','2020-01-01',2,2,'127.0.0.1','category3','Διακίνηση',100);");
}

$sql="select * from gks_aade_typos_xarakt_esodon where id_aade_typos_xarakt_esodon=33";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_typos_xarakt_esodon (
  id_aade_typos_xarakt_esodon,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_typos_xarakt_esodon_code,aade_typos_xarakt_esodon_descr,sortorder
  ) values 
  (33,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_598_001','Πωλήσεις αγαθών που υπάγονται σε ΕΦΚ',33),
  (34,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_598_003','Πωλήσεις για λογαριασμό αγροτών μέσω αγροτικού συνεταιρισμού κλπ',34);");
}


$sql="select * from gks_aade_typos_xarakt_eksodon where id_aade_typos_xarakt_eksodon=74";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_typos_xarakt_eksodon (
  id_aade_typos_xarakt_eksodon,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_typos_xarakt_eksodon_code,aade_typos_xarakt_eksodon_descr,sortorder
  ) values 
  (74,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_585_017','Διάφορα λειτουργικά έξοδα Ζ2',56),
  (75,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_598_002','Αγορές αγαθών που υπάγονται σε ΕΦΚ',61),

  (76,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_103','Απομείωση εμπορευμάτων',76),
  (77,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_203','Απομείωση πρώτων υλών και υλικών',77),
  (78,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_303','Απομείωση πρώτων υλών και υλικών',78),
  (79,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_208','Απομείωση προϊόντων και παραγωγής σε εξέλιξη',79),
  (80,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_308','Απομείωση προϊόντων και παραγωγής σε εξέλιξη',80),
  (81,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_314','Απομείωση ζώων - φυτών – εμπορευμάτων',81),
  (82,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_106','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',82),
  (83,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_205','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',83),
  (84,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_305','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',84),
  (85,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_210','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',85),
  (86,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_310','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',86),
  (87,'2020-01-01','2020-01-01',2,2,'127.0.0.1','E3_318','Ιδιοπαραγωγή παγίων - Αυτοπαραδόσεις - Καταστροφές αποθεμάτων',87);");

  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=1 where id_aade_typos_xarakt_eksodon=1;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=2 where id_aade_typos_xarakt_eksodon=2;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=3 where id_aade_typos_xarakt_eksodon=3;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=4 where id_aade_typos_xarakt_eksodon=4;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=5 where id_aade_typos_xarakt_eksodon=5;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=6 where id_aade_typos_xarakt_eksodon=6;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=7 where id_aade_typos_xarakt_eksodon=7;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=8 where id_aade_typos_xarakt_eksodon=76;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=9 where id_aade_typos_xarakt_eksodon=8;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=10 where id_aade_typos_xarakt_eksodon=82;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=11 where id_aade_typos_xarakt_eksodon=9;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=12 where id_aade_typos_xarakt_eksodon=10;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=13 where id_aade_typos_xarakt_eksodon=11;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=14 where id_aade_typos_xarakt_eksodon=12;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=15 where id_aade_typos_xarakt_eksodon=13;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=16 where id_aade_typos_xarakt_eksodon=14;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=17 where id_aade_typos_xarakt_eksodon=77;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=18 where id_aade_typos_xarakt_eksodon=15;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=19 where id_aade_typos_xarakt_eksodon=83;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=20 where id_aade_typos_xarakt_eksodon=16;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=21 where id_aade_typos_xarakt_eksodon=79;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=22 where id_aade_typos_xarakt_eksodon=17;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=23 where id_aade_typos_xarakt_eksodon=85;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=24 where id_aade_typos_xarakt_eksodon=18;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=25 where id_aade_typos_xarakt_eksodon=19;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=26 where id_aade_typos_xarakt_eksodon=20;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=27 where id_aade_typos_xarakt_eksodon=21;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=28 where id_aade_typos_xarakt_eksodon=22;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=29 where id_aade_typos_xarakt_eksodon=23;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=30 where id_aade_typos_xarakt_eksodon=78;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=31 where id_aade_typos_xarakt_eksodon=24;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=32 where id_aade_typos_xarakt_eksodon=84;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=33 where id_aade_typos_xarakt_eksodon=25;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=34 where id_aade_typos_xarakt_eksodon=80;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=35 where id_aade_typos_xarakt_eksodon=26;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=36 where id_aade_typos_xarakt_eksodon=86;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=37 where id_aade_typos_xarakt_eksodon=27;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=38 where id_aade_typos_xarakt_eksodon=28;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=39 where id_aade_typos_xarakt_eksodon=29;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=40 where id_aade_typos_xarakt_eksodon=30;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=41 where id_aade_typos_xarakt_eksodon=31;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=42 where id_aade_typos_xarakt_eksodon=32;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=43 where id_aade_typos_xarakt_eksodon=81;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=44 where id_aade_typos_xarakt_eksodon=33;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=45 where id_aade_typos_xarakt_eksodon=87;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=46 where id_aade_typos_xarakt_eksodon=34;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=47 where id_aade_typos_xarakt_eksodon=35;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=48 where id_aade_typos_xarakt_eksodon=36;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=49 where id_aade_typos_xarakt_eksodon=37;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=50 where id_aade_typos_xarakt_eksodon=38;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=51 where id_aade_typos_xarakt_eksodon=39;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=52 where id_aade_typos_xarakt_eksodon=40;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=53 where id_aade_typos_xarakt_eksodon=41;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=54 where id_aade_typos_xarakt_eksodon=42;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=55 where id_aade_typos_xarakt_eksodon=43;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=56 where id_aade_typos_xarakt_eksodon=44;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=57 where id_aade_typos_xarakt_eksodon=45;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=58 where id_aade_typos_xarakt_eksodon=46;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=59 where id_aade_typos_xarakt_eksodon=47;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=60 where id_aade_typos_xarakt_eksodon=48;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=61 where id_aade_typos_xarakt_eksodon=49;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=62 where id_aade_typos_xarakt_eksodon=50;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=63 where id_aade_typos_xarakt_eksodon=51;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=64 where id_aade_typos_xarakt_eksodon=52;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=65 where id_aade_typos_xarakt_eksodon=53;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=66 where id_aade_typos_xarakt_eksodon=54;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=67 where id_aade_typos_xarakt_eksodon=55;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=68 where id_aade_typos_xarakt_eksodon=74;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=69 where id_aade_typos_xarakt_eksodon=56;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=70 where id_aade_typos_xarakt_eksodon=57;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=71 where id_aade_typos_xarakt_eksodon=58;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=72 where id_aade_typos_xarakt_eksodon=59;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=73 where id_aade_typos_xarakt_eksodon=75;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=74 where id_aade_typos_xarakt_eksodon=60;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=75 where id_aade_typos_xarakt_eksodon=61;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=76 where id_aade_typos_xarakt_eksodon=62;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=77 where id_aade_typos_xarakt_eksodon=63;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=78 where id_aade_typos_xarakt_eksodon=64;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=79 where id_aade_typos_xarakt_eksodon=65;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=80 where id_aade_typos_xarakt_eksodon=66;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=81 where id_aade_typos_xarakt_eksodon=67;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=82 where id_aade_typos_xarakt_eksodon=68;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=83 where id_aade_typos_xarakt_eksodon=69;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=84 where id_aade_typos_xarakt_eksodon=70;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=85 where id_aade_typos_xarakt_eksodon=71;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=86 where id_aade_typos_xarakt_eksodon=72;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=87 where id_aade_typos_xarakt_eksodon=73;");
  gks_run_sql("update gks_aade_typos_xarakt_eksodon set sortorder=1000 where id_aade_typos_xarakt_eksodon=100;");

  
}


$sql="select * from gks_aade_eidos_posotitas where id_aade_eidos_posotitas=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_eidos_posotitas (
  id_aade_eidos_posotitas,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_eidos_posotitas_code,aade_eidos_posotitas_descr,sortorder
  ) values 
  (4,'2020-01-01','2020-01-01',2,2,'127.0.0.1',4,'Μέτρα',4),
  (5,'2020-01-01','2020-01-01',2,2,'127.0.0.1',5,'Τετραγωνικά Μέτρα',5),
  (6,'2020-01-01','2020-01-01',2,2,'127.0.0.1',6,'Κυβικά Μέτρα',6),
  (7,'2020-01-01','2020-01-01',2,2,'127.0.0.1',7,'Τεμάχια Λοιπές Περιπτώσεις',7);");
}

gks_run_sql("update gks_monades_metrisis set aade_eidos_posotitas_id=4 where aade_eidos_posotitas_id=0 and id_monada=20");
gks_run_sql("update gks_monades_metrisis set aade_eidos_posotitas_id=5 where aade_eidos_posotitas_id=0 and id_monada=30");
gks_run_sql("update gks_monades_metrisis set aade_eidos_posotitas_id=6 where aade_eidos_posotitas_id=0 and id_monada=40");

$sql="select * from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=12";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO gks_aade_skopos_diakinisis (
  id_aade_skopos_diakinisis,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_skopos_diakinisis_code,aade_skopos_diakinisis_descr,sortorder
  ) values 
  (12,'2020-01-01','2020-01-01',2,2,'127.0.0.1',9,'Αγορά',9),
  (13,'2020-01-01','2020-01-01',2,2,'127.0.0.1',10,'Εφοδιασμός πλοίων και αεροσκαφών',10),
  (14,'2020-01-01','2020-01-01',2,2,'127.0.0.1',11,'Δωρεάν διάθεση',11),
  (15,'2020-01-01','2020-01-01',2,2,'127.0.0.1',12,'Εγγύηση',12),
  (16,'2020-01-01','2020-01-01',2,2,'127.0.0.1',13,'Χρησιδανεισμός',13),
  (17,'2020-01-01','2020-01-01',2,2,'127.0.0.1',14,'Αποθήκευση σε Τρίτους',14),
  (18,'2020-01-01','2020-01-01',2,2,'127.0.0.1',15,'Επιστροφή από Φύλαξη',15),
  (19,'2020-01-01','2020-01-01',2,2,'127.0.0.1',16,'Ανακύκλωση',16),
  (20,'2020-01-01','2020-01-01',2,2,'127.0.0.1',17,'Καταστροφή άχρηστου υλικού',17),
  (21,'2020-01-01','2020-01-01',2,2,'127.0.0.1',18,'Διακίνηση Παγίων (Ενδοδιακίνηση)',18),
  (22,'2020-01-01','2020-01-01',2,2,'127.0.0.1',19,'Λοιπές Διακινήσεις',19)");
  
  gks_run_sql("update gks_aade_skopos_diakinisis set sortorder=101 where id_aade_skopos_diakinisis=9");
  gks_run_sql("update gks_aade_skopos_diakinisis set sortorder=102 where id_aade_skopos_diakinisis=10");
  gks_run_sql("update gks_aade_skopos_diakinisis set sortorder=103 where id_aade_skopos_diakinisis=11");
  
  
  //Χρησιδάνειο
  $cc_exist='';
  $sql="select * from gks_acc_inv where aade_skopos_diakinisis_id=10 limit 1";
  $result = gks_run_sql($sql);
  if ($result->num_rows>0) $cc_exist.='gks_acc_inv ';

  $sql="select * from gks_whi_mov where aade_skopos_diakinisis_id=10 limit 1";
  $result = gks_run_sql($sql);
  if ($result->num_rows>0) $cc_exist.='gks_whi_mov ';

  if ($cc_exist=='') gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=10");
  
  //Αντικατάσταση
  $cc_exist='';
  $sql="select * from gks_acc_inv where aade_skopos_diakinisis_id=11 limit 1";
  $result = gks_run_sql($sql);
  if ($result->num_rows>0) $cc_exist.='gks_acc_inv ';

  $sql="select * from gks_whi_mov where aade_skopos_diakinisis_id=11 limit 1";
  $result = gks_run_sql($sql);
  if ($result->num_rows>0) $cc_exist.='gks_whi_mov ';

  if ($cc_exist=='') gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=11");
  
   
  
}

$sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=551";
$result = gks_run_sql($sql);
if ($result->num_rows==0) { 
  gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon` (`id_acc_eidos_parastatikou`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`parent_id`,`eidos_parastatikou_type_id`,`eidos_parastatikou_need_prev`,`eidos_parastatikou_has_fpa`,`eidos_parastatikou_has_posotita`,`eidos_parastatikou_has_othertaxes`,`eidos_parastatikou_has_esoda`,`eidos_parastatikou_has_eksoda`,`eidos_parastatikou_need_afm`,`eidos_parastatikou_aade_code`,`eidos_parastatikou_descr`,`eidos_parastatikou_balance_pros`,`eidos_parastatikou_stock_pros`,`eidos_parastatikou_whi_type_id`,`def_prefix`,`def_suffix`,`sortorder`,`is_selectable`,`credit_acc_eidos_parastatikou_id`,`rbs_code_a`,`import_apo_allon`) VALUES 
 (551,'2202-01-01','2202-01-01',2,2,'127.0.0.1',501,2,0,1,1,NULL,0,1,0,NULL,'Καταχώρηση ΑΛΠ ως έξοδο (από ΑΛΠ)',0,0,912,NULL,NULL,1012,1,0,0,'[11.1]'),
 (552,'2202-01-01','2202-01-01',2,2,'127.0.0.1',501,2,0,1,1,NULL,0,1,0,NULL,'Καταχώρηση ΑΠΥ ως έξοδο (από ΑΠΥ)',0,0,0,NULL,NULL,1013,1,0,0,'[11.2]');");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_qrcode_scan` (
  `id_qrcode_scan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `app_id` varchar(64) DEFAULT NULL,
  `mytext` text DEFAULT NULL,
  `format` varchar(32) DEFAULT NULL,
  `result` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_qrcode_scan`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `app_id` (`app_id`),
  KEY `format` (`format`),
  KEY `result` (`result`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$tempdir = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';  
if (file_exists($tempdir)==false) mkdir($tempdir);


$tempfile = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/temp.txt';  
$ret=@file_put_contents($tempfile,'test');
if ($ret===false) {
	echo '<div style="color: #721c24;background-color: #f8d7da;border-color: #f5c6cb;">Δεν έχει δικαιώματα εγγραφής στον φάκελο /my/temp
	<br>
	chown -R www-data:www-data '.$tempdir.'<br>
	chmod -R 777 '.$tempdir.'
	</div>';	
	
	die();
}
 
$sql="select connection_ok from gks_vies_check limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_vies_check` 
	ADD COLUMN `connection_ok` TINYINT NOT NULL DEFAULT 0 AFTER `afm`,
	ADD INDEX `connection_ok`(`connection_ok`);");
}


$sql="select filesobjectlist from gks_acc_inv_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_acc_inv_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	
	gks_run_sql("update gks_acc_inv_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_acc_pay_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_acc_pay_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_acc_pay_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_crm_leads_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_crm_leads_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_crm_leads_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_crm_tasks_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_crm_tasks_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_crm_tasks_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_whi_mov_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_whi_mov_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_whi_mov_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_hotel_reservation_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_hotel_reservation_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_hotel_reservation_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_transfer_reservation_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_transfer_reservation_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_transfer_reservation_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_crm_machine_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_crm_machine_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_crm_machine_photo set filesobjectlist=1");
}
$sql="select filesobjectlist from gks_orders_photo limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_orders_photo` 
	ADD COLUMN `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
	ADD INDEX `filesobjectlist`(`filesobjectlist`);");
	gks_run_sql("update gks_orders_photo set filesobjectlist=1");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_machine_messages` (
  `id_crm_machine_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `crm_machine_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `crm_machine_message` text DEFAULT NULL,
  `email_id` int(11) NOT NULL DEFAULT 0,
  `connect_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_crm_machine_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `crm_machine_id` (`crm_machine_id`),
  KEY `user_id` (`user_id`),
  KEY `crm_machine_message` (`crm_machine_message`(250)),
  KEY `email_id` (`email_id`),
  KEY `connect_id` (`connect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_sms_viber_template` (
  `id_sms_viber_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `sms_viber_template_name` varchar(250) DEFAULT NULL,
  `sms_viber_template_text` text DEFAULT NULL,
  `sms_viber_template_disabled` tinyint(4) NOT NULL DEFAULT 0,
  `sms_viber_template_sortorder` int(11) NOT NULL DEFAULT 1000,
  `sms_enabled` tinyint(4) NOT NULL DEFAULT 1,
  `viber_enabled` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_sms_viber_template`) USING BTREE,
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `sms_viber_template_name` (`sms_viber_template_name`),
  KEY `sms_viber_template_text` (`sms_viber_template_text`(250)),
  KEY `sms_viber_template_disabled` (`sms_viber_template_disabled`),
  KEY `sms_viber_template_sortorder` (`sms_viber_template_sortorder`),
  KEY `sms_enabled` (`sms_enabled`),
  KEY `viber_enabled` (`viber_enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");  



$sql="select * from gks_permission_object where id_permission_object=271";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (271,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_sms_viber_template','Πρότυπα SMS-Viber',2701)");
   
   
}








gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_sms_viber_template` (
  `id_sms_viber_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `sms_viber_template_name` varchar(250) DEFAULT NULL,
  `sms_viber_template_text` text DEFAULT NULL,
  `sms_viber_template_disabled` tinyint(4) NOT NULL DEFAULT 0,
  `sms_viber_template_sortorder` int(11) NOT NULL DEFAULT 1000,
  `sms_enabled` tinyint(4) NOT NULL DEFAULT 1,
  `viber_enabled` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_sms_viber_template`) USING BTREE,
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `sms_viber_template_name` (`sms_viber_template_name`),
  KEY `sms_viber_template_text` (`sms_viber_template_text`(250)),
  KEY `sms_viber_template_disabled` (`sms_viber_template_disabled`),
  KEY `sms_viber_template_sortorder` (`sms_viber_template_sortorder`),
  KEY `sms_enabled` (`sms_enabled`),
  KEY `viber_enabled` (`viber_enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;"); 






gks_run_sql("ALTER TABLE `gks_qrcode_scan` 
MODIFY COLUMN `app_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


$sql="select erp_app_mobile_id from gks_qrcode_scan limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_qrcode_scan` 
	ADD COLUMN `erp_app_mobile_id` int(11) NOT NULL DEFAULT 0,
	ADD INDEX `erp_app_mobile_id`(`erp_app_mobile_id`);");
}

$sql="select sms_provider from gks_sms limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  //sms_folder: inbox , sent , draft , outbox , failed , queued
  // 
	gks_run_sql("ALTER TABLE `gks_sms` 
	ADD COLUMN `sms_provider` varchar(64) DEFAULT NULL after id,
	ADD COLUMN `erp_app_mobile_id` int(11) NOT NULL DEFAULT 0 after sms_provider,
	ADD COLUMN `sms_mms_type` varchar(64) DEFAULT NULL after erp_app_mobile_id,
	ADD COLUMN `sms_folder` varchar(64) DEFAULT NULL after sms_mms_type,
	ADD COLUMN `sms_mobile_db_id`  int(11) NOT NULL DEFAULT 0 after sms_folder,
	ADD COLUMN `sms_for_send` tinyint(4) NOT NULL DEFAULT 0 after sms_mobile_db_id,
	ADD COLUMN `sms_try_send_date` datetime DEFAULT NULL after sms_for_send,
	ADD COLUMN `format` varchar(64) DEFAULT NULL after sms_try_send_date,
	ADD INDEX `sms_provider`(`sms_provider`),
	ADD INDEX `erp_app_mobile_id`(`erp_app_mobile_id`),
	ADD INDEX `sms_mms_type`(`sms_mms_type`),
	ADD INDEX `sms_folder`(`sms_folder`),
	ADD INDEX `sms_mobile_db_id`(`sms_mobile_db_id`),
	ADD INDEX `sms_for_send`(`sms_for_send`),
	ADD INDEX `sms_try_send_date`(`sms_try_send_date`)
	
	;");
	gks_run_sql("update gks_sms set sms_provider='smsapi' where sms_provider is null or sms_provider=''");
}


gks_run_sql("ALTER TABLE `gks_sms` 
MODIFY COLUMN `Message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
MODIFY COLUMN `Message_post` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app_mobile` (
  `id_erp_app_mobile` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `erp_app_mobile_name` varchar(128) DEFAULT NULL,
  `erp_app_mobile_phonenumber` varchar(128) DEFAULT NULL,
  `erp_app_mobile_cost_per_sms` double NOT NULL DEFAULT 0,
  `erp_app_mobile_descr` text DEFAULT NULL,
  `erp_app_mobile_token` varchar(128) DEFAULT NULL,
  `erp_app_mobile_secret` varchar(128) DEFAULT NULL,
  `erp_app_mobile_disabled` tinyint(4) NOT NULL DEFAULT 0,
  `erp_app_mobile_url` varchar(128) DEFAULT NULL,
  `erp_app_mobile_url2ip` varchar(128) DEFAULT NULL,
  `erp_app_mobile_port` int(11) NOT NULL DEFAULT 55555,
  `erp_app_mobile_lan_ip` varchar(128) DEFAULT NULL,
  `erp_app_mobile_wan_ip` varchar(128) DEFAULT NULL,
  `erp_app_mobile_last_ping` datetime DEFAULT NULL,
  `erp_app_mobile_sortorder` int(11) NOT NULL DEFAULT 1000,
  `mobile_last_ping_id` int(11) NOT NULL DEFAULT 0,
   PRIMARY KEY (`id_erp_app_mobile`) USING BTREE,
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `erp_app_mobile_name` (`erp_app_mobile_name`),
  KEY `erp_app_mobile_phonenumber` (`erp_app_mobile_phonenumber`),
  KEY `erp_app_mobile_descr` (`erp_app_mobile_descr`(190)),
  KEY `erp_app_mobile_token` (`erp_app_mobile_token`),
  KEY `erp_app_mobile_secret` (`erp_app_mobile_secret`),
  KEY `erp_app_mobile_disabled` (`erp_app_mobile_disabled`),
  KEY `erp_app_mobile_url` (`erp_app_mobile_url`),
  KEY `erp_app_mobile_port` (`erp_app_mobile_port`),
  KEY `erp_app_mobile_lan_ip` (`erp_app_mobile_lan_ip`),
  KEY `erp_app_mobile_wan_ip` (`erp_app_mobile_wan_ip`),
  KEY `erp_app_mobile_last_ping` (`erp_app_mobile_last_ping`),
  KEY `erp_app_mobile_sortorder` (`erp_app_mobile_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select erp_app_mobile_secret from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
	ADD COLUMN `erp_app_mobile_secret` varchar(128) DEFAULT NULL AFTER erp_app_mobile_token,
	add index erp_app_mobile_secret (erp_app_mobile_secret)");
}

$sql="select erp_app_mobile_cost_per_sms from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
	ADD COLUMN `erp_app_mobile_cost_per_sms` double NOT NULL DEFAULT 0;");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app_mobile_ping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `erp_app_mobile_id` int(11) NOT NULL DEFAULT 0,
  `mydate` datetime DEFAULT NULL,
  `myip` varchar(48) DEFAULT NULL,
  `ostime` datetime DEFAULT NULL,
  `imei` varchar(190) DEFAULT NULL,
  `phonenumber` varchar(190) DEFAULT NULL,
  `personname` varchar(190) DEFAULT NULL,
  `rand1` varchar(190) DEFAULT NULL,
  `ticks` varchar(190) DEFAULT NULL,
  `osver` varchar(190) DEFAULT NULL,
  `appver` varchar(190) DEFAULT NULL,
  `lanips` varchar(190) DEFAULT NULL,
  `hdwd` int(11) NOT NULL DEFAULT 0,
  `screw` int(11) NOT NULL DEFAULT 0,
  `screh` int(11) NOT NULL DEFAULT 0,
  `mac` text DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mydate` (`mydate`),
  KEY `imei` (`imei`),
  KEY `phonenumber` (`phonenumber`),
  KEY `personname` (`personname`),
  KEY `myip` (`myip`),
  KEY `erp_app_mobile_id` (`erp_app_mobile_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app_mobile_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate` datetime NOT NULL,
  `erp_app_mobile_id` int(11) NOT NULL DEFAULT 0,
  `mygroup` varchar(190) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ip` varchar(48) NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mydate` (`mydate`),
  KEY `erp_app_mobile_id` (`erp_app_mobile_id`),
  KEY `mygroup` (`mygroup`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select * from gks_permission_object where id_permission_object=385";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (385,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_erp_app_mobile','gks ERP App Mobile',3815)");
}
$sql="select * from gks_custom_table where id_custom_table=50";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`,obj_url) VALUES 
   (50,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - gks ERP App Mobile','gks_erp_app_mobile','id_erp_app_mobile','erp_app_mobile_id',0,'base',160,'admin-erp-app-mobile.php')");
}




$sql="select phone_fix from gks_users_communication limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_users_communication` 
	ADD COLUMN `phone_fix` varchar(250) DEFAULT NULL,
	ADD INDEX phone_fix (phone_fix)");
	
	$sql="SELECT gks_users_communication.user_id as id
  FROM gks_users_communication
  WHERE comm_type='phone' AND comm_value<>''
  GROUP BY gks_users_communication.user_id;";
  
  $result = $db_link->query($sql);
  if (!$result) {echo 'error sql '.$sql;die();}
	$data=array();
  while ($row = $result->fetch_assoc()) {
    $data[]=$row['id'];
  }
  foreach ($data as $value) {
     gks_user_fix_phone_numbers($value);
  } 
	
	
}

$sql="select * from gks_notification_type where id_notification_type=150";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_notification_type` (`id_notification_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`notification_type_descr`,`notification_type_sortorder`,`notification_type_disabled`) VALUES 
  (150,  '2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Εισερχόμενα SMS μέσω App',150,0)");
}



 
 
 