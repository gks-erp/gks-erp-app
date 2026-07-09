<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/




$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_TRANSFER') === false) {
  echo '_current/_config.php file not contains GKS_TRANSFER<br>';die();}
  
  

$sql="select * from gks_monades_metrisis where id_monada=200";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_monades_metrisis` (`id_monada`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`monada_descr`,`monada_symbol`,`monada_parent_id`,`monada_parent_epi`,`monada_sortorder`,`aade_eidos_posotitas_id`) VALUES 
   (200,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαδρομή','Route',0,0,200,1)");

}

$sql="select * from gks_permission_object where id_permission_object=2200";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2200,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer','Κανάλια Transfer',2200),
   (2180,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_area','Ομάδες Σημείων',2180),
   (2181,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_poi','Σημεία Ενδιαφέροντος',2181),
   (2182,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_poi_type','Τύποι Σημείων Ενδιαφέροντος',2182),
   (2183,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_oxima_type','Τύποι Οχημάτων',2183),
   (2184,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_pricelist','Τιμοκατάλογος',2184),

   (2120,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_booking','Transfer Booking',2120)");

}
gks_run_sql("update gks_permission_object set object_name='Ομάδες Σημείων' where id_permission_object=2180");


$sql="select * from gks_custom_table where id_custom_table=38";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
   (38,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Κανάλια','gks_transfer','id_transfer','transfer_id',0,'transfer',670),
   (39,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Ομάδες Σημείων','gks_transfer_area','id_transfer_area','transfer_area_id',0,'transfer',640),
   (40,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Σημεία Ενδιαφέροντος','gks_poi','id_poi','poi_id',0,'transfer',641),
   (41,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Τύποι Σημείων Ενδιαφέροντος','gks_poi_type','id_poi_type','poi_type_id',0,'transfer',641),
   (42,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Τύποι Οχημάτων','gks_transfer_oxima_type','id_transfer_oxima_type','transfer_oxima_type_id',0,'transfer',642),
   (43,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Τιμοκατάλογος','gks_transfer_pricelist','id_transfer_pricelist','transfer_pricelist_id',0,'transfer',643)");
}
gks_run_sql("update gks_custom_table set custom_table_descr='Transfer - Ομάδες Σημείων' where id_custom_table=39");


$sql="select * from gks_crm_activity_objects where id_crm_activity_object=34";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (34,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_pos','POS',34,0,'admin-pos-item.php?id=%s'),
   (35,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer','Κανάλι Transfer',35,0,'admin-transfer-item.php?id=%s'),
   (36,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer_area','Ομάδες Σημείων',36,0,'admin-transfer-area-item.php?id=%s'),
   (37,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_poi','Σημείο Ενδιαφέροντος',37,0,'admin-poi-item.php?id=%s'),
   (38,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_poi_type','Τύπος Σημείων Ενδιαφέροντος',38,0,'admin-poi-type-item.php?id=%s'),
   (39,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer_oxima_type','Τύποι Οχημάτων',39,0,'admin-transfer-oxima-type-item.php?id=%s'),
   (40,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_whi_mov','Αποθήκη - Δελτίο Αποστολής',2,0,'admin-whi-mov-item.php?id=%s'),
   (41,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer_pricelist','Τιμοκατάλογος',40,0,'admin-transfer-pricelist-item.php?id=%s')");
}
gks_run_sql("update gks_crm_activity_objects set crm_activity_object_descr='Ομάδες Σημείων' where id_crm_activity_object=36");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer` (
  `id_transfer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  `company_sub_id` int(11) NOT NULL DEFAULT '0',
  `transfer_title` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_phone` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_email` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_odos` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_perioxi` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_poli` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_tk` varchar(24) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_nomos_id` int(11) NOT NULL DEFAULT '0',
  `transfer_country_id` int(11) NOT NULL DEFAULT '0',
  `transfer_map_latitude` double NOT NULL DEFAULT '0',
  `transfer_map_longitude` double NOT NULL DEFAULT '0',
  `transfer_disable` tinyint(4) NOT NULL DEFAULT '0',
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `transfer_color` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `default_eshop_transfer` tinyint(4) NOT NULL DEFAULT '0',
  `transfer_default_availability` tinyint(4) NOT NULL DEFAULT '0',
  `transfer_date_open` varchar(24) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_date_close` varchar(24) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_default_price` double NOT NULL DEFAULT '0',
  `transfer_reservation_can_select_oxima` tinyint(4) NOT NULL DEFAULT '0',
  `transfer_reservation_days_future` int(11) NOT NULL DEFAULT '0',
  `transfer_sortorder` int(11) NOT NULL DEFAULT '1000',
  `transfer_template_eidos_descr` text COLLATE utf8mb4_unicode_520_ci,
  `transfer_template_woo_descr` text COLLATE utf8mb4_unicode_520_ci,
  `transfer_website_key` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_use_checkout_system` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_plan_price_avg` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_transfer`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `company_id` (`company_id`),
  KEY `company_sub_id` (`company_sub_id`),
  KEY `transfer_title` (`transfer_title`(250)),
  KEY `transfer_odos` (`transfer_odos`(250)),
  KEY `transfer_tk` (`transfer_tk`),
  KEY `transfer_nomos_id` (`transfer_nomos_id`),
  KEY `transfer_disable` (`transfer_disable`),
  KEY `transfer_phone` (`transfer_phone`),
  KEY `transfer_email` (`transfer_email`),
  KEY `transfer_poli` (`transfer_poli`(250)),
  KEY `transfer_country_id` (`transfer_country_id`),
  KEY `default_eshop_transfer` (`default_eshop_transfer`),
  KEY `transfer_sortorder` (`transfer_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_area` (
  `id_transfer_area` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_area_descr` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_area_descr_en_US` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_area_parent_id` int(11) NOT NULL DEFAULT '0',
  `transfer_area_comments` text COLLATE utf8mb4_unicode_520_ci,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '1000',
  `transfer_area_disable` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_area`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_area_descr` (`transfer_area_descr`),
  KEY `transfer_area_descr_en_US` (`transfer_area_descr_en_US`),
  KEY `sort_order` (`sort_order`),
  KEY `transfer_area_disable` (`transfer_area_disable`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_area2poi` (
  `id_transfer_area2poi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_id` int(11) NOT NULL DEFAULT '0',
  `transfer_area_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_area2poi`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `poi_id` (`poi_id`),
  KEY `transfer_area_id` (`transfer_area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_area2transfer` (
  `id_transfer_area2transfer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_id` int(11) NOT NULL DEFAULT '0',
  `transfer_area_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_area2transfer`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_area_id` (`transfer_area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi` (
  `id_poi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `poi_parent_id` int(11) NOT NULL DEFAULT '0',
  `poi_type_id` int(11) NOT NULL DEFAULT '0',
  `poi_descr` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_descr_en_US` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_phone` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_email` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_odos` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_perioxi` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_poli` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_tk` varchar(24) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_nomos_id` int(11) NOT NULL DEFAULT '0',
  `poi_country_id` int(11) NOT NULL DEFAULT '0',
  `poi_map_latitude` double NOT NULL DEFAULT '0',
  `poi_map_longitude` double NOT NULL DEFAULT '0',
  `poi_disable` tinyint(4) NOT NULL DEFAULT '0',
  `poi_color` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_sortorder` int(11) NOT NULL DEFAULT '1000',
  `poi_comments` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id_poi`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `poi_parent_id` (`poi_parent_id`),
  KEY `poi_type_id` (`poi_type_id`),
  KEY `poi_descr` (`poi_descr`(250)),
  KEY `poi_descr_en_US` (`poi_descr_en_US`(250)),
  KEY `poi_odos` (`poi_odos`(250)),
  KEY `poi_tk` (`poi_tk`),
  KEY `poi_nomos_id` (`poi_nomos_id`),
  KEY `poi_disable` (`poi_disable`),
  KEY `poi_phone` (`poi_phone`),
  KEY `poi_email` (`poi_email`),
  KEY `poi_poli` (`poi_poli`(250)),
  KEY `poi_country_id` (`poi_country_id`),
  KEY `poi_sortorder` (`poi_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi_type` (
  `id_poi_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `poi_type_descr` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_type_descr_en_US` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_type_sortorder` int(11) NOT NULL DEFAULT '1000',
  `poi_type_comments` text COLLATE utf8mb4_unicode_520_ci,
  `poi_type_disable` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_poi_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `poi_type_descr` (`poi_type_descr`(250)),
  KEY `poi_type_descr_en_US` (`poi_type_descr_en_US`(250)),
  KEY `poi_type_sortorder` (`poi_type_sortorder`),
  KEY `poi_type_disable` (`poi_type_disable`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");





gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_oxima_type` (
  `id_transfer_oxima_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_descr` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_oxima_type_descr_en_US` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_oxima_type_photo` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_oxima_type_def_price_per_km` double NOT NULL DEFAULT '0',
  `transfer_oxima_type_disable` tinyint(4) NOT NULL DEFAULT '0',
  `transfer_oxima_type_max_epivates` int(11) NOT NULL DEFAULT '1',
  `transfer_oxima_type_max_kareklakia` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_max_amajidia` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_comments` text COLLATE utf8mb4_unicode_520_ci,
  `sort_order` int(11) NOT NULL DEFAULT '1000',
  PRIMARY KEY (`id_transfer_oxima_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_oxima_type_descr` (`transfer_oxima_type_descr`),
  KEY `transfer_oxima_type_descr_en_US` (`transfer_oxima_type_descr_en_US`),
  KEY `transfer_oxima_type_def_price_per_km` (`transfer_oxima_type_def_price_per_km`),
  KEY `transfer_oxima_type_disable` (`transfer_oxima_type_disable`),
  KEY `transfer_oxima_type_max_epivates` (`transfer_oxima_type_max_epivates`),
  KEY `transfer_oxima_type_max_kareklakia` (`transfer_oxima_type_max_kareklakia`),
  KEY `transfer_oxima_type_max_amajidia` (`transfer_oxima_type_max_amajidia`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_oximatype2transfer` (
  `id_transfer_oximatype2transfer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_id` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_oximatype2transfer`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_oxima2type2transfer` (
  `id_transfer_oxima2type2transfer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT '0',
  `transfer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_oxima2type2transfer`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `asset_id` (`asset_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_oxima_type_photo` (
  `id_transfer_oxima_type_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT '0',
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT '0',
  `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_add_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_oxima_type_photo`),
  KEY `product_id` (`transfer_oxima_type_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_acc_inv' where crm_activity_object_code='acc_inv';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_acc_inv' where activity_model          ='acc_inv';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_acc_journal' where crm_activity_object_code='acc_journal';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_acc_journal' where activity_model          ='acc_journal';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_acc_pay' where crm_activity_object_code='acc_pay';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_acc_pay' where activity_model          ='acc_pay';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_acc_seires' where crm_activity_object_code='acc_seires';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_acc_seires' where activity_model          ='acc_seires';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_company' where crm_activity_object_code='company';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_company' where activity_model          ='company';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_company_subs' where crm_activity_object_code='company_subs';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_company_subs' where activity_model          ='company_subs';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_crm_leads' where crm_activity_object_code='crm_lead';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_crm_leads' where activity_model          ='crm_lead';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_crm_machine' where crm_activity_object_code='crm_machine';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_crm_machine' where activity_model          ='crm_machine';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_crm_tasks' where crm_activity_object_code='crm_task';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_crm_tasks' where activity_model          ='crm_task';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_eshops' where crm_activity_object_code='eshop';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_eshops' where activity_model          ='eshop';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_eshop_products' where crm_activity_object_code='eshop_products';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_eshop_products' where activity_model          ='eshop_products';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_eshop_products_brands' where crm_activity_object_code='eshop_products_brands';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_eshop_products_brands' where activity_model          ='eshop_products_brands';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_eshop_products_categories' where crm_activity_object_code='eshop_products_categories';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_eshop_products_categories' where activity_model          ='eshop_products_categories';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel' where crm_activity_object_code='hotel';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel' where activity_model          ='hotel';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_availability' where crm_activity_object_code='hotel_availability';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_availability' where activity_model          ='hotel_availability';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_floor' where crm_activity_object_code='hotel_floor';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_floor' where activity_model          ='hotel_floor';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_price' where crm_activity_object_code='hotel_price';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_price' where activity_model          ='hotel_price';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_reservation' where crm_activity_object_code='hotel_reservation';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_reservation' where activity_model          ='hotel_reservation';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_room' where crm_activity_object_code='hotel_room';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_room' where activity_model          ='hotel_room';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_hotel_room_type' where crm_activity_object_code='hotel_room_type';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_hotel_room_type' where activity_model          ='hotel_room_type';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_orders' where crm_activity_object_code='orders';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_orders' where activity_model          ='orders';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_poi' where crm_activity_object_code='poi';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_poi' where activity_model          ='poi';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_poi_type' where crm_activity_object_code='poi_type';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_poi_type' where activity_model          ='poi_type';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_print_forms' where crm_activity_object_code='print_forms';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_print_forms' where activity_model          ='print_forms';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_production_ergasies' where crm_activity_object_code='production_ergasies';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_production_ergasies' where activity_model          ='production_ergasies';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_production_posta' where crm_activity_object_code='production_posta';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_production_posta' where activity_model          ='production_posta';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_transfer' where crm_activity_object_code='transfer';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_transfer' where activity_model          ='transfer';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_transfer_area' where crm_activity_object_code='transfer_area';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_transfer_area' where activity_model          ='transfer_area';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_users_groups' where crm_activity_object_code='users_groups';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_users_groups' where activity_model          ='users_groups';");

gks_run_sql("update gks_crm_activity_objects set crm_activity_object_code='gks_warehouses' where crm_activity_object_code='warehouses';");
gks_run_sql("update gks_crm_activity         set activity_model          ='gks_warehouses' where activity_model          ='warehouses';");



gks_run_sql("delete from gks_nomoi where id_nomos in (
272,285,322,330,1735,2147,2267,2670,2959,3086,4307,4309,
4510,4512,4513,4514,4515,4516,4517,4518,4519,4520,4521,4522,4523,4524,4525,4526,4528,4529,
4531,4532,4533,4534,4535,4536,4537,4538,4539,4540,4541,4542,4543,4545,4546,4547,4548,4549,
4550,4552,4553,4554,4555,4556,4557,4559,4560,4561,4562,4563,4564,4565,4566,

4591);");

gks_run_sql("update gks_nomoi set nomos_descr='Jan Mayen (Arctic Region)', nomos_descr_en_US='Jan Mayen (Arctic Region)' where id_nomos=3177;");
gks_run_sql("update gks_nomoi set nomos_descr='Svalbard (Arctic Region)', nomos_descr_en_US='Svalbard (Arctic Region)' where id_nomos=3178;");
gks_run_sql("update gks_nomoi set nomos_descr='Puerto Rico', nomos_descr_en_US='Puerto Rico' where id_nomos=4511;");
gks_run_sql("update gks_nomoi set nomos_descr='Virgin Islands of the U.S.', nomos_descr_en_US='Virgin Islands of the U.S.' where id_nomos=4530;");
gks_run_sql("update gks_nomoi set nomos_descr='American Samoa', nomos_descr_en_US='American Samoa' where id_nomos=4544;");
gks_run_sql("update gks_nomoi set nomos_descr='Guam', nomos_descr_en_US='Guam' where id_nomos=4551;");
gks_run_sql("update gks_nomoi set nomos_descr='Northern Mariana Islands', nomos_descr_en_US='Northern Mariana Islands' where id_nomos=4558;");


gks_run_sql("update gks_nomoi set nomos_descr=trim(nomos_descr) where nomos_descr like '% ';");
gks_run_sql("update gks_nomoi set nomos_descr_en_US=trim(nomos_descr_en_US) where nomos_descr_en_US like '% ';");
gks_run_sql("update gks_nomoi set nomos_ISO_3166_2=trim(nomos_ISO_3166_2) where nomos_ISO_3166_2 like '% ';");
gks_run_sql("update gks_doy set doy_odos=trim(doy_odos) where doy_odos like '% ';");


$sql="select transfer_oxima_type_max_booster from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type` 
  ADD COLUMN `transfer_oxima_type_site_text` TEXT DEFAULT NULL AFTER `transfer_oxima_type_descr_en_US`,
  ADD COLUMN `transfer_oxima_type_site_text_en_US` TEXT DEFAULT NULL AFTER `transfer_oxima_type_site_text`,
  ADD COLUMN `transfer_oxima_type_max_booster` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_max_epivates`,
  MODIFY COLUMN `transfer_oxima_type_max_kareklakia` INT(11) NOT NULL DEFAULT 0,
  ADD INDEX transfer_oxima_type_site_text (`transfer_oxima_type_site_text`(190)),
  ADD INDEX transfer_oxima_type_site_text_en_US (transfer_oxima_type_site_text_en_US(190)),
  ADD INDEX transfer_oxima_type_comments(transfer_oxima_type_comments(190)),
  ADD INDEX transfer_oxima_type_max_booster (transfer_oxima_type_max_booster),
  
  ADD COLUMN `transfer_oxima_type_roure_group_one` TINYINT NOT NULL DEFAULT 0 AFTER `sort_order`,
  ADD COLUMN `transfer_oxima_type_roure_group_multi` TINYINT NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_roure_group_one`,
  ADD INDEX transfer_oxima_type_roure_group_one (transfer_oxima_type_roure_group_one),
  ADD INDEX transfer_oxima_type_roure_group_multi (transfer_oxima_type_roure_group_multi),
  
  ADD COLUMN `transfer_oxima_type_price_booster` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_booster,
  ADD INDEX transfer_oxima_type_price_booster (transfer_oxima_type_price_booster),
  ADD COLUMN `transfer_oxima_type_price_kareklakia` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_kareklakia,
  ADD INDEX transfer_oxima_type_price_kareklakia (transfer_oxima_type_price_kareklakia),
  ADD COLUMN `transfer_oxima_type_price_amajidia` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_amajidia,
  ADD INDEX transfer_oxima_type_price_amajidia (transfer_oxima_type_price_amajidia),

  ADD COLUMN `transfer_oxima_type_price_min_per_transfer` double NOT NULL DEFAULT '0' after transfer_oxima_type_def_price_per_km,
  ADD INDEX transfer_oxima_type_price_min_per_transfer (transfer_oxima_type_price_min_per_transfer),
  ADD COLUMN `transfer_oxima_type_price_min_per_person` double NOT NULL DEFAULT '0' after transfer_oxima_type_price_min_per_transfer,
  ADD INDEX transfer_oxima_type_price_min_per_person (transfer_oxima_type_price_min_per_person);
  ");
}


$sql="select * from gks_poi_type where id_poi_type<=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_poi_type` (`id_poi_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`poi_type_descr`,`poi_type_descr_en_US`,`poi_type_sortorder`,`poi_type_comments`,`poi_type_disable`) VALUES 
  (1,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Τοποθεσία','Location',1,'',0),
  (2,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Αεροδρόμιο','Airport',2,'',0),
  (3,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Λιμάνι','Port',3,'',0),
  (4,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Σιδηροδρομικός Σταθμός','Train Station',4,'',0),
  (101,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Ξενοδοχείο','Hotel',101,'',0),
  (102,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Αξιοθέατα','Sights',102,'',0);");

}
 
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_pricelist` (
  `id_transfer_pricelist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `poi_id_from` int(11) NOT NULL DEFAULT '1',
  `poi_id_to` int(11) NOT NULL DEFAULT '0',
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT '0',
  `transfer_pricelist_mydate_start` datetime DEFAULT NULL,
  `transfer_pricelist_mydate_end` datetime DEFAULT NULL,
  `transfer_pricelist_disable` tinyint(4) NOT NULL DEFAULT '0',
  `transfer_pricelist_comments` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_pricelist_price_per_transfer` double NOT NULL DEFAULT '0',
  `transfer_pricelist_price_per_person` double NOT NULL DEFAULT '0',
  
  PRIMARY KEY (`id_transfer_pricelist`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `poi_id_from` (`poi_id_from`),
  KEY `poi_id_to` (`poi_id_to`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `transfer_pricelist_mydate_start` (`transfer_pricelist_mydate_start`),
  KEY `transfer_pricelist_mydate_end` (`transfer_pricelist_mydate_end`),
  KEY `transfer_pricelist_disable` (`transfer_pricelist_disable`),
  KEY `transfer_pricelist_comments` (`transfer_pricelist_comments`),
  KEY `transfer_pricelist_price_per_transfer` (`transfer_pricelist_price_per_transfer`),
  KEY `transfer_pricelist_price_per_person` (`transfer_pricelist_price_per_person`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_pricelist2transfer` (
  `id_transfer_pricelist2transfer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `transfer_id` int(11) NOT NULL DEFAULT '0',
  `transfer_pricelist_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_transfer_pricelist2transfer`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_pricelist_id` (`transfer_pricelist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select transfer_and_aller_retour from gks_transfer_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
gks_run_sql("ALTER TABLE `gks_transfer_pricelist` 
  ADD COLUMN `transfer_pricelist_price_per_transfer_offer` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_transfer`,
  ADD COLUMN `transfer_pricelist_price_per_person_offer` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person`,
  ADD COLUMN `transfer_and_aller_retour` TINYINT NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person_offer`,
  ADD INDEX `transfer_and_aller_retour`(`transfer_and_aller_retour`);");
}


$sql="select transfer_oxima_type_max_suitcases from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type` 
  ADD COLUMN `transfer_oxima_type_max_suitcases` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_max_epivates`,
  ADD COLUMN `transfer_oxima_type_service_free_wifi` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_oxima_type_service_bottled_water` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_oxima_type_service_door_to_door` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_oxima_type_service_porter` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_oxima_type_service_treat_yourself` TINYINT NOT NULL DEFAULT 0,
  ADD INDEX transfer_oxima_type_max_suitcases (`transfer_oxima_type_max_suitcases`);");
}


$sql="select poi_iata_code from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi` 
    ADD COLUMN `poi_locode` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after `poi_type_id`,
    ADD INDEX poi_locode (`poi_locode`(190)),
    ADD COLUMN `poi_iata_code` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after `poi_locode`,
    ADD INDEX poi_iata_code (`poi_iata_code`(190))");
}

$sql="select poi_icao_code from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi` 
    ADD COLUMN `poi_icao_code` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after `poi_iata_code`,
    ADD INDEX poi_icao_code (`poi_icao_code`(190))");
}


$sql="select poi_diadromes_id from gks_transfer_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_pricelist` 
  ADD COLUMN `transfer_pricelist_apostasi_se_metra` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_and_aller_retour`,
  ADD COLUMN `transfer_pricelist_diarkeia_se_lepta` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_pricelist_apostasi_se_metra`,
  ADD COLUMN `poi_diadromes_id` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_pricelist_diarkeia_se_lepta`,
  ADD INDEX `poi_diadromes_id`(`poi_diadromes_id`);");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_poi_diadromes` (
  `id_poi_diadromes` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `poi_id_from` int(11) NOT NULL DEFAULT '1',
  `poi_id_to` int(11) NOT NULL DEFAULT '0',
  `poi_diadromes_disable` tinyint(4) NOT NULL DEFAULT '0',
  `poi_diadromes_comments` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poi_diadromes_apostasi_se_metra` int(11) NOT NULL DEFAULT '0',
  `poi_diadromes_diarkeia_se_lepta` int(11) NOT NULL DEFAULT '0',
  `poi_diadromes_directions` LONGTEXT DEFAULT NULL,
  PRIMARY KEY (`id_poi_diadromes`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `poi_id_from` (`poi_id_from`),
  KEY `poi_id_to` (`poi_id_to`),
  KEY `poi_diadromes_disable` (`poi_diadromes_disable`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=2185";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2185,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_poi_diadromes','Διαδρομές',2185)");
}
$sql="select * from gks_custom_table where id_custom_table=44";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
   (44,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Διαδρομές','gks_poi_diadromes','id_poi_diadromes','poi_diadromes_id',0,'transfer',644)");
}
$sql="select * from gks_crm_activity_objects where id_crm_activity_object=42";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (42,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_poi_diadromes','Διαδρομές',41,0,'admin-poi-diadromes-item.php?id=%s')");
}

$sql="select transfer_reservation_min_hours_to_book from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_reservation_min_hours_to_book` INTEGER NOT NULL DEFAULT 0;");
}

$sql="select perm_int_cond01 from gks_permission_user limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_permission_user` 
  ADD COLUMN `perm_int_cond01` tinyint(4) NOT NULL DEFAULT '0'");
}

gks_run_sql("update gks_permission_object set sortorder=9001 where id_permission_object=720");
gks_run_sql("update gks_permission_object set sortorder=9002 where id_permission_object=730");



$sql="select other_visible from gks_calendar_other_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_calendar_other_users` 
  ADD COLUMN `other_visible` tinyint(4) NOT NULL DEFAULT '1'");
}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_dav_db.php');
if (strpos($read_file, 'GKS_SITE_PATH') === false) {
  echo '/my/_current/_dav_db.php file not nontains GKS_SITE_PATH<br>'; die();}
  
$sql="select * from gks_permission_object where id_permission_object=115";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (115,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'dav_card','CardDav',115)");
}
$sql="select * from gks_permission_object where id_permission_object=425";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (425,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'dav_cal','CalDav',425)");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_crm_tasks_messages` (
  `id_crm_tasks_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `crm_tasks_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `crm_tasks_message` text COLLATE utf8mb4_unicode_520_ci,
  `email_id` int(11) NOT NULL DEFAULT '0',
  `connect_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_crm_tasks_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `crm_tasks_id` (`crm_tasks_id`),
  KEY `user_id` (`user_id`),
  KEY `crm_tasks_message` (`crm_tasks_message`(250)),
  KEY `email_id` (`email_id`),
  KEY `connect_id` (`connect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



//$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
//if (strpos($read_file, 'GKS_ERP_APP_CODES') === false) {
//  echo '_current/_config.php file not contains GKS_ERP_APP_CODES<br>';die();}
  
$sql="select * from gks_permission_object where id_permission_object=395";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (395,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_app_info','Πληροφορίες Εφαρμογής',395)");
}


$sql="select messageid from gks_email limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_email` 
	ADD COLUMN `messageid` VARCHAR(64) DEFAULT NULL AFTER `views_ips`,
	ADD INDEX `messageid`(`messageid`);");
}

$sql="select transfer_oxima_type_max_golfbag from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_oxima_type` 
  ADD COLUMN `transfer_oxima_type_max_golfbag` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_price_amajidia`,
  ADD COLUMN `transfer_oxima_type_price_golfbag` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_golfbag,
  ADD INDEX transfer_oxima_type_price_golfbag (transfer_oxima_type_price_golfbag),

  ADD COLUMN `transfer_oxima_type_max_skis` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_price_golfbag`,
  ADD COLUMN `transfer_oxima_type_price_skis` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_skis,
  ADD INDEX transfer_oxima_type_price_skis (transfer_oxima_type_price_skis),

  ADD COLUMN `transfer_oxima_type_max_5minstop` INTEGER NOT NULL DEFAULT 0 AFTER `transfer_oxima_type_price_skis`,
  ADD COLUMN `transfer_oxima_type_price_5minstop` double NOT NULL DEFAULT '0' after transfer_oxima_type_max_5minstop,
  ADD INDEX transfer_oxima_type_price_5minstop (transfer_oxima_type_price_5minstop);");
}

$sql="select * from gks_payment_acquirers where id_payment_acquirer=11";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {       
  gks_run_sql("INSERT INTO `gks_payment_acquirers` (`id_payment_acquirer`,`payment_acquirer_name`,`payment_acquirer_name_en_US`,`payment_acquirer_table_name`,`payment_acquirer_type`,`payment_acquirer_type_dm`,`payment_acquirer_html`,`payment_acquirer_html_en_US`,`payment_acquirer_button_html`,`payment_acquirer_button_html_en_US`,`payment_acquirer_sxolio`,`payment_acquirer_sxolio_en_US`,`payment_acquirer_tooltip`,`payment_acquirer_tooltip_en_US`,`payment_acquirer_disabled`,`payment_acquirer_env_test`,`payment_acquirer_method`,`payment_acquirer_fees_enabled`,`pa_fees_domestic_fixed`,`pa_fees_domestic_percent`,`pa_fees_international_fixed`,`pa_fees_international_percent`,`payment_acquirer_php_function_isok`,`payment_acquirer_php_function_calculate`,`mysortorder`,`connect_id`,`aade_tropos_pliromis_id`,`show_acc_pay`,`show_eshop`) VALUES 
  (11,'POS','POS',NULL,'web','[delivery][none][pelatis][post][store]','POS','POS','POS','POS',NULL,NULL,'POS','POS',0,0,'manual',0,0,0,0,0,NULL,NULL,11,0,7,1,1);");
}

 
gks_run_sql("ALTER TABLE `gks_email` MODIFY COLUMN `messageid` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");

 
$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_dav_db.php');
if (strpos($read_file, 'GKS_SITE_URL') === false) {
  echo '/my/_current/_dav_db.php file not nontains GKS_SITE_URL<br>'; die();}



gks_run_sql("update gks_permission_object set table_name='gks_transfer_reservation', object_name='Transfer Κρατήσεις' where id_permission_object=2120");


$sql="select * from gks_custom_table where id_custom_table=45";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
   (45,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer - Κρατήσεις','gks_transfer_reservation','id_transfer_reservation','transfer_reservation_id',0,'transfer',630)");
}

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=43";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (43,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_transfer_reservation','Κρατήσεις',42,0,'admin-transfer-reservation-item.php?id=%s')");
}


//ta index einai ola ??
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation` (
  `id_transfer_reservation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_guid` varchar(63) DEFAULT NULL,
  `transfer_reservation_date` datetime DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `is_return_transfer_for_id` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_journal_id` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_seira_id` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_seira_code` varchar(48) DEFAULT NULL,
  `transfer_reservation_number_int` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_number_str` varchar(200) DEFAULT NULL,
  `transfer_reservation_ekdosi_date` datetime DEFAULT NULL,
  `transfer_reservation_status` varchar(32) NOT NULL DEFAULT '010draft',
  `transfer_start` datetime DEFAULT NULL,
  `transfer_end` datetime DEFAULT NULL,
  `duration_secs` int(11) NOT NULL DEFAULT 0,
  `num_adults` int(11) NOT NULL DEFAULT 0,
  `num_childs` int(11) NOT NULL DEFAULT 0,
  `num_babys` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_email` varchar(190) DEFAULT NULL,
  `user_first_name` varchar(255) DEFAULT NULL,
  `user_last_name` varchar(255) DEFAULT NULL,
  `user_mobile` varchar(255) DEFAULT NULL,
  `user_lang` varchar(8) DEFAULT NULL,
  `parastatiko` int(11) NOT NULL DEFAULT 0,
  `eponimia` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `afm` varchar(255) DEFAULT NULL,
  `doy` varchar(255) DEFAULT NULL,
  `epaggelma` varchar(1024) DEFAULT NULL,
  `ma_odos` varchar(255) DEFAULT NULL,
  `ma_perioxi` varchar(255) DEFAULT NULL,
  `ma_poli` varchar(255) DEFAULT NULL,
  `ma_tk` varchar(255) DEFAULT NULL,
  `ma_country_id` int(11) NOT NULL DEFAULT 0,
  `ma_nomos_id` int(11) NOT NULL DEFAULT 0,
  `ma_apostoli_number` varchar(63) DEFAULT NULL,
  `sxolio` text DEFAULT NULL,
  `user_notes` text DEFAULT NULL,
  `idiotites` text DEFAULT NULL,
  `note_logistirio` text DEFAULT NULL,
  `gks_price_original_net` double NOT NULL DEFAULT 0,
  `gks_price_net` double NOT NULL DEFAULT 0,
  `gks_price_fpa` double NOT NULL DEFAULT 0,
  `gks_price_netfpa` double NOT NULL DEFAULT 0,
  `gks_price_total` double NOT NULL DEFAULT 0,
  `fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `pricelist_id` int(11) NOT NULL DEFAULT 0,
  `def_ekptosi` double NOT NULL DEFAULT 0,
  `is_other` tinyint(4) NOT NULL DEFAULT 0,
  `other_first_name` varchar(255) DEFAULT NULL,
  `other_last_name` varchar(255) DEFAULT NULL,
  `other_email` varchar(255) DEFAULT NULL,
  `other_mobile` varchar(255) DEFAULT NULL,
  `other_lang` varchar(8) DEFAULT NULL,
  `other_ma_odos` varchar(255) DEFAULT NULL,
  `other_ma_perioxi` varchar(255) DEFAULT NULL,
  `other_ma_poli` varchar(255) DEFAULT NULL,
  `other_ma_tk` varchar(255) DEFAULT NULL,
  `other_ma_country_id` int(11) NOT NULL DEFAULT 0,
  `other_ma_nomos_id` int(11) NOT NULL DEFAULT 0,
  `products_posotita` double NOT NULL DEFAULT 0,
  `products_varos` double NOT NULL DEFAULT 0,
  `products_ogos` double NOT NULL DEFAULT 0,
  `products_ogos_max_x` double NOT NULL DEFAULT 0,
  `products_ogos_max_y` double NOT NULL DEFAULT 0,
  `products_ogos_max_z` double NOT NULL DEFAULT 0,
  `products_need_apostoli` tinyint(4) NOT NULL DEFAULT 0,
  `products_need_pliromi` tinyint(4) NOT NULL DEFAULT 0,
  `kostos_apostolis` double NOT NULL DEFAULT 0,
  `tropos_apostolis` int(11) NOT NULL DEFAULT 0,
  `tropos_apostolis_json` text DEFAULT NULL,
  `kostos_pliromis` double NOT NULL DEFAULT 0,
  `tropos_pliromis` int(11) NOT NULL DEFAULT 0,
  `kostos_pliromis_json` text DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `session_basket` text DEFAULT NULL,
  `bank_deposit_9digit` varchar(32) DEFAULT NULL,
  `delivery_id_8` int(11) NOT NULL DEFAULT 0,
  `delivery_number` varchar(64) DEFAULT NULL,
  `mdate_payment` datetime DEFAULT NULL,
  `mdate_execute` datetime DEFAULT NULL,
  `mdate_send` datetime DEFAULT NULL,
  `mdate_invoice` datetime DEFAULT NULL,
  `coupons` text DEFAULT NULL,
  `totalWithheldAmount` double NOT NULL DEFAULT 0,
  `totalOtherTaxesAmount` double NOT NULL DEFAULT 0,
  `totalStampDutyamount` double NOT NULL DEFAULT 0,
  `totalFeesAmount` double NOT NULL DEFAULT 0,
  `print_date` datetime DEFAULT NULL,
  `print_file_name` varchar(255) DEFAULT NULL,
  `print_file_url` varchar(255) DEFAULT NULL,
  `print_user_id` int(11) NOT NULL DEFAULT 0,
  `print_transfer_reservation_status` varchar(32) DEFAULT NULL,
  `affect_balance` tinyint(4) NOT NULL DEFAULT 0,
  `affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT 1,
  `affect_balance_all_poso_type` varchar(32) DEFAULT 'price_net',
  `affect_balance_poso` double NOT NULL DEFAULT 0,
  `affect_balance_pros` tinyint(4) NOT NULL DEFAULT 0,
  `assigned_id` int(11) NOT NULL DEFAULT 0,
  `crm_channel_id` int(11) NOT NULL DEFAULT 0,
  `crm_channel_contact_id` int(11) NOT NULL DEFAULT 0,
  `crm_channel_campain_id` int(11) NOT NULL DEFAULT 0,
  `crm_channel_url` text DEFAULT NULL,
  `crm_channel_text` text DEFAULT NULL,
  `crm_channel_code` varchar(190) DEFAULT NULL,
  `woo_eshop_id` int(11) NOT NULL DEFAULT 0,
  `woo_order_id` int(11) NOT NULL DEFAULT 0,
  `woo_guid` varchar(64) DEFAULT NULL,
  `poi_id_from` int(11) NOT NULL DEFAULT 0,
  `poi_id_to` int(11) NOT NULL DEFAULT 0,
  `poi_diadromes_id` int(11) NOT NULL DEFAULT 0,
  `direction` varchar(32) NOT NULL DEFAULT 'tori',
  `apostasi_se_metra` int(11) NOT NULL DEFAULT 0,
  `diarkeia_se_lepta` int(11) NOT NULL DEFAULT 0,
  `directions` longtext DEFAULT NULL,
  PRIMARY KEY (`id_transfer_reservation`),
  KEY `transfer_reservation_date` (`transfer_reservation_date`),
  KEY `transfer_reservation_status` (`transfer_reservation_status`),
  KEY `transfer_reservation_guid` (`transfer_reservation_guid`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `user_id` (`user_id`),
  KEY `user_first_name` (`user_first_name`(190)),
  KEY `user_last_name` (`user_last_name`(190)),
  KEY `user_lang` (`user_lang`),
  KEY `user_email` (`user_email`),
  KEY `user_mobile` (`user_mobile`(190)),
  KEY `ma_country_id` (`ma_country_id`),
  KEY `mdate_payment` (`mdate_payment`),
  KEY `mdate_execute` (`mdate_execute`),
  KEY `mdate_send` (`mdate_send`),
  KEY `mdate_invoice` (`mdate_invoice`),
  KEY `fiscal_position_id` (`fiscal_position_id`),
  KEY `pricelist_id` (`pricelist_id`),
  KEY `order_id` (`order_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `affect_balance` (`affect_balance`),
  KEY `assigned_id` (`assigned_id`),
  KEY `crm_channel_id` (`crm_channel_id`),
  KEY `crm_channel_contact_id` (`crm_channel_contact_id`),
  KEY `crm_channel_campain_id` (`crm_channel_campain_id`),
  KEY `crm_channel_text` (`crm_channel_text`(240)),
  KEY `crm_channel_url` (`crm_channel_url`(240)),
  KEY `woo_eshop_id` (`woo_eshop_id`),
  KEY `woo_order_id` (`woo_order_id`),
  KEY `woo_guid` (`woo_guid`),
  KEY `crm_channel_code` (`crm_channel_code`),
  KEY `poi_id_from` (`poi_id_from`),
  KEY `poi_id_to` (`poi_id_to`),
  KEY `poi_diadromes_id` (`poi_diadromes_id`),
  KEY `direction` (`direction`),
  key is_return_transfer_for_id (is_return_transfer_for_id)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_links` (
  `id_transfer_reservation_links` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `url` text DEFAULT NULL,
  `relative_path` text DEFAULT NULL,
  `mydate` datetime DEFAULT NULL,
  `ip` varchar(48) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `download_status` tinyint(4) NOT NULL DEFAULT 0,
  `download_start` datetime DEFAULT NULL,
  `download_end` datetime DEFAULT NULL,
  `download_pososto` double NOT NULL DEFAULT 0,
  `download_size_until_now` bigint(20) NOT NULL DEFAULT 0,
  `download_size_total` bigint(20) NOT NULL DEFAULT 0,
  `download_message` text DEFAULT NULL,
  `html_tds` longtext DEFAULT NULL,
  PRIMARY KEY (`id_transfer_reservation_links`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `mydate` (`mydate`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_log` (
  `id_transfer_reservation_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `add_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `sxolio` text NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_transfer_reservation_log`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `user_id` (`user_id`),
  KEY `add_date` (`add_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_messages` (
  `id_transfer_reservation_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_message` text DEFAULT NULL,
  `email_id` int(11) NOT NULL DEFAULT 0,
  `connect_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_reservation_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `user_id` (`user_id`),
  KEY `transfer_reservation_message` (`transfer_reservation_message`(250)),
  KEY `email_id` (`email_id`),
  KEY `connect_id` (`connect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_photo` (
  `id_transfer_reservation_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(255) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_reservation_photo`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=2100";
$result = gks_run_sql($sql);
if ($result && $result->num_rows==0) {
  
  gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon` (`id_acc_eidos_parastatikou`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`parent_id`,`eidos_parastatikou_type_id`,`eidos_parastatikou_need_prev`,`eidos_parastatikou_has_fpa`,`eidos_parastatikou_has_posotita`,`eidos_parastatikou_has_othertaxes`,`eidos_parastatikou_has_esoda`,`eidos_parastatikou_has_eksoda`,`eidos_parastatikou_need_afm`,`eidos_parastatikou_aade_code`,`eidos_parastatikou_descr`,`eidos_parastatikou_balance_pros`,`eidos_parastatikou_stock_pros`,`eidos_parastatikou_whi_type_id`,`def_prefix`,`def_suffix`,`sortorder`,`is_selectable`,`odbc`,`credit_acc_eidos_parastatikou_id`,`rbs_code_a`,`import_apo_allon`) VALUES 
 (2100,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',0,0,0,0,0,NULL,0,0,0,NULL,'Transfer',0,0,0,NULL,NULL,12100,0,'2020-01-01 00:00:00',0,0,NULL),
 (2200,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',2100,2100,0,1,1,'wh,ot,sd,fe,dd',0,0,1,NULL,'Κρατήσεις Transfer',1,0,0,NULL,NULL,12200,1,'2020-01-01 00:00:00',0,0,NULL);");

} 

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets` (
  `id_asset` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `asset_code` varchar(32) DEFAULT NULL,
  `asset_photo` varchar(255) DEFAULT NULL,
  `asset_title` varchar(255) DEFAULT NULL,
  `asset_serialnumber` varchar(255) DEFAULT NULL,
  `asset_type` int(11) NOT NULL DEFAULT 0,
  `asset_date_activate` datetime DEFAULT NULL,
  `asset_date_aposirsi` datetime DEFAULT NULL,
  `asset_sxolio` text DEFAULT NULL,
  `old_charnum` varchar(255) DEFAULT NULL,
  `old_id` int(11) NOT NULL DEFAULT 0,
  `asset_last_warehouse_id` int(11) NOT NULL DEFAULT 0,
  `asset_last_user_id` int(11) NOT NULL DEFAULT 0,
  `asset_last_company_id` int(11) NOT NULL DEFAULT 0,
  `asset_last_mixani_id` int(11) NOT NULL DEFAULT 0,
  `old_laborid` varchar(16) DEFAULT NULL,
  `asset_disable` tinyint(4) NOT NULL DEFAULT 0,
  `is_fotografou` tinyint(4) NOT NULL DEFAULT 0,
  `bank_id` int(11) NOT NULL DEFAULT 0,
  `xreosi_val` double NOT NULL DEFAULT 0,
  `xreosi_type` int(11) NOT NULL DEFAULT 0,
  `oxima_km` int(11) NOT NULL DEFAULT 0,
  `oxima_next_kteo` datetime DEFAULT NULL,
  `oxima_next_service_km` int(11) DEFAULT 0,
  `oxima_liji_asfaleia` datetime DEFAULT NULL,
  `oxima_elastika` varchar(255) DEFAULT NULL,
  `service_frontier_text` text DEFAULT NULL,
  `oxima_km_date` datetime DEFAULT NULL,
  `last_action_date` datetime DEFAULT NULL,
  `last_action_warehouse_id` int(11) NOT NULL DEFAULT 0,
  `last_action_source` text DEFAULT NULL,
  `last_action_ip` varchar(48) DEFAULT NULL,
  `mac_address` varchar(255) DEFAULT NULL,
  `mixani_esn` int(11) NOT NULL DEFAULT 0,
  `asset_thesi` varchar(200) DEFAULT NULL,
  `viva_terminal_id` varchar(200) DEFAULT NULL,
  `viva_terminal_code` varchar(200) DEFAULT NULL,
  `viva_company_id` int(11) NOT NULL DEFAULT 0,
  `viva_def_ref_pliromis` varchar(16) DEFAULT NULL,
  `ds620_40_lifecount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_asset`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `asset_code` (`asset_code`),
  KEY `asset_type` (`asset_type`),
  KEY `old_id` (`old_id`),
  KEY `asset_last_warehouse_id` (`asset_last_warehouse_id`),
  KEY `asset_date_activate` (`asset_date_activate`),
  KEY `asset_date_aposirsi` (`asset_date_aposirsi`),
  KEY `asset_disable` (`asset_disable`),
  KEY `asset_last_mixani_id` (`asset_last_mixani_id`),
  KEY `asset_last_user_id` (`asset_last_user_id`),
  KEY `is_fotografou` (`is_fotografou`),
  KEY `asset_last_company_id` (`asset_last_company_id`),
  KEY `bank_id` (`bank_id`),
  KEY `xreosi_type` (`xreosi_type`),
  KEY `oxima_km_date` (`oxima_km_date`),
  KEY `last_action_date` (`last_action_date`),
  KEY `mixani_esn` (`mixani_esn`),
  KEY `asset_thesi` (`asset_thesi`),
  KEY `viva_terminal_id` (`viva_terminal_id`),
  KEY `viva_terminal_code` (`viva_terminal_code`),
  KEY `ds620_40_lifecount` (`ds620_40_lifecount`),
  KEY `asset_title` (`asset_title`(190)),
  KEY `asset_serialnumber` (`asset_serialnumber`(190)),
  KEY `mac_address` (`mac_address`(190))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_log` (
  `id_assets_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `add_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `sxolio` mediumtext NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_assets_log`) USING BTREE,
  KEY `asset_id` (`asset_id`),
  KEY `user_id` (`user_id`),
  KEY `add_date` (`add_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_oximata_km` (
  `id_assets_oximata_km` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `mydateadd` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `km` int(11) NOT NULL DEFAULT '0',
  `elastika_change` tinyint(4) NOT NULL DEFAULT '0',
  `myip` varchar(48) NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_assets_oximata_km`),
  KEY `asset_id` (`asset_id`),
  KEY `mydateadd` (`mydateadd`),
  KEY `user_id_add` (`user_id_add`),
  KEY `elastika_change` (`elastika_change`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_photo` (
  `id_asset_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT '0',
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_add_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_asset_photo`),
  KEY `asset_id` (`asset_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_print_forms where id_print_form=51";
$result = gks_run_sql($sql);
if ($result && $result->num_rows==0) {
  
  gks_run_sql("INSERT INTO `gks_print_forms` (`id_print_form`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_descr`,`is_disable`,`sortorder`,`gks_lang`,`file_type`,`grayscale`,`zoom`,`dpi`,`size_name`,`width_cm`,`height_cm`,`is_landscape`,`margin_cm_left`,`margin_cm_right`,`margin_cm_top`,`margin_cm_bottom`,`logo_url`,`page_header`,`page_footer`,`page_background_url`,`page_background_opacity`,`form_header`,`form_footer`,`details_header`,`details_body`,`details_footer`,`fpa_analysis`,`foroi_analysis`,`lots_and_serials_analysis`,`file_thump_url`,`localization_set_id`,`perm_company_ids`,`perm_acc_journal_ids`,`perm_acc_seires_ids`) VALUES
  
 (51,NULL,'2023-03-09 18:14:22',0,1,'192.168.1.202','2023-03-09 18:14:22','Παραστατικά & Παραγγελίες en-US',0,6,'en-US','pdf',0,1,600,'A4',21,29.7,0,2,2,3,3,'','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 22pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 30%; vertical-align: bottom;\">{logo_url [<a href=\"{site_url}\" target=\"_blank\" rel=\"noopener\"><img style=\"max-height: 100%; height: 50px;\" src=\"%%\" border=\"0\" /></a>]}</td>\n<td style=\"width: 70%; vertical-align: bottom; text-align: right;\">{company_tagline}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\">Bank Account:<br />{company_url [ <em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a>]} {company_phone [ <em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {company_email [ <em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a>]} <br />Page {page} from {pages}<br />Powered by <a href=\"https://www.gks.gr\" target=\"_blank\" rel=\"noopener\">www.gks.gr</a></td>\n</tr>\n</tbody>\n</table>','',1,'<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">Issuer<br />{company_title [<strong>%%</strong><br />]} {company_eponimia [%%<br />]} {company_epaggelma [%%<br />]} {company_odos [Address: %%{company_tk [, %%]}<br />]} {company_poli [%%]}{company_nomos_descr [, %%]}{company_country_name [, %%<br />]} {company_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {company_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {company_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {company_afm [VAT: {company_country_ee}%%<br />]} {company_doy [TAX Office: %%<br />]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{person_label [%%<br />]} {person_title [<strong>%%</strong><br />]} {person_eponimia [%%<br />]} {person_epaggelma [%%<br />]} {person_odos [Address: %%{person_tk [, %%]}<br />]} {person_poli [%%]}{person_nomos_descr [, %%]}{person_country_name [, %%<br />]} {person_mobile [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {person_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {person_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {person_afm [VAT: {person_country_ee}%%<br />]} {person_doy [TAX Office: %%<br />]}\n<div>{hide} <strong>Delivery location</strong><br />{dest_name [Name: %%<br />]} {dest_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {dest_odos [Address: %%{dest_tk [, %%]}<br />]} {dest_poli [%%]}{dest_nomos_descr [, %%]}{dest_country_name [, %%<br />]}</div>\n</td>\n</tr>\n</tbody>\n</table>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div>{doc_note_doc}</div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 50%; vertical-align: top; text-align: center;\">{person_label [%%<br />]}</td>\n<td style=\"width: 50%; vertical-align: top; text-align: center; min-height: 100px;\">Issuer</td>\n</tr>\n</tbody>\n</table>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"font-family: Arial; font-size: 16pt; text-align: center; font-weight: bold;\">{doc_title_pre [%% : ]}{doc_title}</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_date [Date: %%<br />]} {doc_seira [Series: %%<br />]} {doc_number [hide:zero][Number: %%<br />]} {doc_mark [MARK: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_skopos_diakinisis [Purpose of Delivery: %%<br />]} {doc_tropos_pliromis [Payment Method: %%<br />]} {doc_tropos_apostolis [Shipping Method: %%<br />]} {doc_arithmos_aposolis [Shipping number: %%<br />]} {doc_arithmos_oximatos [Transport Number: %%<br />]} {doc_enarji_apostolis [Shipping Start Time: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_canceled_display};\">The cancellation document concerns the document with the following information:</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_canceled_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_date [Date: %%<br />]} {doc_canceled_seira [Series: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_number [hide:zero][Number: %%<br />]} {doc_canceled_mark [MARK: %%]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_credit_display};\">The credit document refers to the document with the following information:</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_credit_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_date [Date: %%<br />]} {doc_credit_seira [Series: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_number [hide:zero][Number: %%<br />]} {doc_credit_mark [MARK: %%]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 30%; text-align: center;\" nowrap=\"nowrap\">Code</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 70%; text-align: center;\" nowrap=\"nowrap\">Description</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">Meas<br />unit</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0.625391%; text-align: center;\" nowrap=\"nowrap\">Quantity</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Unit Net<br />price</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">VAT<br />Total</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">VAT<br />Amount<br />μονάδας</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Unit Total<br />Price</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Net<br />Amount</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">VAT<br />Amount</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Total Amount</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_aa}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 30%;\" nowrap=\"nowrap\">{eidos_code}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 70%;\">{eidos_photo_url [<img style=\"float: left; padding: 0pt 5pt 5pt 0pt; max-width: 30pt; max-height: 30pt;\" src=\"%%\" />]}{eidos_descr}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_mm}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0.625391%;\" nowrap=\"nowrap\">{eidos_quantity}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{eidos_priceitem}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_fpa_rate}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_fpa_amount}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_priceitem_total}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_priceall}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_fpa_amount_total}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_priceall_total}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid gray; text-align: left; width: 100%;\" colspan=\"6\">{hide}{eidos_comments}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 0%;\" colspan=\"6\">{eidos_ejeresi_fpa [Αιτία Εξαίρεσης ΦΠΑ: %% <br />]} {eidos_parakratisi_descr [Φόροι Παρακρ.: %%]}{eidos_parakratisi_poso [hide:zero][: %%<br />][format:cs]} {eidos_loipoi_foroi_descr [Λοιποί Φόροι: %%]}{eidos_loipoi_foroi_poso [hide:zero][: %%<br />][format:cs]} {eidos_xartosimo_descr [Ψηφιακό Τέλος συναλλαγής: %%]} {eidos_xartosimo_poso [hide:zero][: %%<br />][format:cs]} {eidos_teloi_descr [Τέλη: %%]} {eidos_teloi_poso [hide:zero][: %%<br />][format:cs]} {eidos_kratiseis_poso [hide:zero][Κρατήσεις: %%][format:cs]}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left; width: 100%;\" colspan=\"4\" nowrap=\"nowrap\"><strong>Totals</strong></td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0.625391%;\" nowrap=\"nowrap\">{doc_posotita}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" colspan=\"4\" nowrap=\"nowrap\"><br /></td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fpa_amount_total}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall_total}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; height: 20px; width: 100%;\" colspan=\"6\" nowrap=\"nowrap\"><br /></td>\n<td style=\"border-right: 1px solid white; text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\"><br /></td>\n<td style=\"border-right: 1px solid white; text-align: right; width: 0%;\" nowrap=\"nowrap\"><br /></td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; text-align: right; vertical-align: top; padding-left: 0px; padding-top: 0px; padding-right: 20px; width: 100%;\" colspan=\"6\" rowspan=\"9\" nowrap=\"nowrap\">{fpa_analysis}<br />{foroi_analysis}</td>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Subtotal: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall [%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right;\" colspan=\"5\" nowrap=\"nowrap\">VAT: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fpa_amount_total [%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\">Gross Total: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%; font-size: 150%;\"><strong><span style=\"white-space: nowrap;\">{doc_netfpa_amount_total [%%][format:cs]}</span></strong><br /></td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Withheld Taxes.: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_withheld [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Other Taxes: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_othertaxes [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Stampduty: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_stampduty [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Fees: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fees [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\">Deductions: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_deductions [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"5\" nowrap=\"nowrap\"><strong>Total: {hide}</strong></td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%; font-size: 120%;\" nowrap=\"nowrap\"><strong> {doc_priceall_total [%%][format:cs]} </strong></td>\n</tr>\n</tfoot>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">#</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: center;\" nowrap=\"nowrap\">% VAT</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Net Amount</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">VAT Amount.</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Total</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{fpa_aa}</td>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{fpa_pososto}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_net}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_fpa}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_total}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left;\" colspan=\"2\" nowrap=\"nowrap\"><strong>Σύνολα</strong></td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_net}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_fpa}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_total}</td>\n</tr>\n</tfoot>\n</table>','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 50%; text-align: left;\" nowrap=\"nowrap\">Τύπος φόρου</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Αξία υποκείμενη σε φόρο</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Φόρος</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{foroi_aa}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 25%;\" nowrap=\"nowrap\">{foroi_descr}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{foroi_net}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{foroi_foros}</td>\n</tr>\n</tbody>\n</table>','','',1096490,'','',''),
 (53,'2021-03-26 01:23:09','2023-03-09 18:18:38',1,1,'192.168.1.202','2023-03-09 18:18:38','Εισπράξεις - Πληρωμές en-US',0,4,'en-US','pdf',0,1,600,'A4',21,29.7,0,2,2,3,3,'','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 22pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 30%; vertical-align: bottom;\">{logo_url [<a href=\"{site_url}\" target=\"_blank\" rel=\"noopener\"><img style=\"max-height: 100%; height: 50px;\" src=\"%%\" border=\"0\" /></a>]}</td>\n<td style=\"width: 70%; vertical-align: bottom; text-align: right;\">{company_tagline}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\">Bank Account: <br />{company_url [ <em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a>]} {company_phone [ <em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {company_email [ <em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a>]} <br />Page {page} from {pages}<br />Powered by <a style=\"font-size: 18.6667px;\" href=\"https://www.gks.gr\" target=\"_blank\" rel=\"noopener\">www.gks.gr</a><br /></td>\n</tr>\n</tbody>\n</table>','',1,'<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">Issuer<br />{company_title [<strong>%%</strong><br />]} {company_eponimia [%%<br />]} {company_epaggelma [%%<br />]} {company_odos [Address: %%{company_tk [, %%]}<br />]} {company_poli [%%]}{company_nomos_descr [, %%]}{company_country_name [, %%<br />]} {company_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {company_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {company_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {company_afm [VAT: {company_country_ee}%%<br />]} {company_doy [TAX Office: %%<br />]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{person_label [%%<br />]} {person_title [<strong>%%</strong><br />]} {person_eponimia [%%<br />]} {person_epaggelma [%%<br />]} {person_odos [Address: %%{person_tk [, %%]}<br />]} {person_poli [%%]}{person_nomos_descr [, %%]}{person_country_name [, %%<br />]} {person_mobile [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {person_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {person_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {person_afm [VAT: {person_country_ee}%%<br />]} {person_doy [TAX Office: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div>{doc_note_doc}</div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 50%; vertical-align: top; text-align: center;\">{person_label [%%<br />]}</td>\n<td style=\"width: 50%; vertical-align: top; text-align: center; min-height: 100px;\">Issuer</td>\n</tr>\n</tbody>\n</table>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"font-family: Arial; font-size: 16pt; text-align: center; font-weight: bold;\">{doc_title_pre [%% : ]}{doc_title}</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 100%; padding-right: 20px; vertical-align: top;\">{doc_date [Date: %%<br />]} {doc_seira [Series: %%<br />]} {doc_number [hide:zero][Number: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 30%; text-align: center;\" nowrap=\"nowrap\">Type</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 50%; text-align: center;\" nowrap=\"nowrap\">Description<br /></th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 20%; text-align: right;\" nowrap=\"nowrap\">Amount</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{eidos_aa}</td>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{eidos_code}</td>\n<td style=\"border: 1px solid gray; text-align: left;\">{eidos_descr}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{eidos_priceall_total}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid gray; text-align: left;\" colspan=\"5\">{hide}{eidos_comments}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr>\n<td style=\"text-align: right;\" colspan=\"3\" nowrap=\"nowrap\"><strong>Total: {hide}</strong></td>\n<td style=\"border: 1px solid gray; text-align: right; font-size: 120%;\" nowrap=\"nowrap\"><strong> {doc_priceall_total [%%][format:cs]} </strong></td>\n</tr>\n</tfoot>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','','','','','',1650735,'','',''),
 (54,'2021-07-09 15:32:43','2023-03-09 18:23:51',1,1,'192.168.1.202','2023-03-09 18:23:51','Δελτία en-US',0,3,'en-US','pdf',0,1,600,'A4',21,29.7,0,2,2,3,3,'','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 22pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 30%; vertical-align: bottom;\">{logo_url [<a href=\"{site_url}\" target=\"_blank\" rel=\"noopener\"><img style=\"max-height: 100%; height: 50px;\" src=\"%%\" border=\"0\" /></a>]}</td>\n<td style=\"width: 70%; vertical-align: bottom; text-align: right;\">{company_tagline}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\">Bank Account:<br />{company_url [ <em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a>]} {company_phone [ <em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {company_email [ <em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a>]} <br />Page {page} from {pages}<br />Powered by <a href=\"https://www.gks.gr\" target=\"_blank\" rel=\"noopener\">www.gks.gr</a></td>\n</tr>\n</tbody>\n</table>','',1,'<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">\n<div>Issuer<br />{company_title [<strong>%%</strong><br />]} {company_eponimia [%%<br />]} {company_epaggelma [%%<br />]} {company_odos [Address: %%{company_tk [, %%]}<br />]} {company_poli [%%]}{company_nomos_descr [, %%]}{company_country_name [, %%<br />]} {company_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {company_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {company_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {company_afm [VAT: {company_country_ee}%%<br />]} {company_doy [TAX Office: %%<br />]}</div>\n<div>{hide} <strong>From</strong><br />{warehouse_from_name [Όνομα: %%<br />]} {warehouse_from_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {warehouse_from_odos [Διεύθυνση: %%{warehouse_from_tk [, %%]}<br />]} {warehouse_from_poli [%%]}{warehouse_from_nomos_descr [, %%]}{warehouse_from_country_name [, %%<br />]}</div>\n</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">\n<div>{person_label [%%<br />]} {person_title [<strong>%%</strong><br />]} {person_eponimia [%%<br />]} {person_epaggelma [%%<br />]} {person_odos [Address: %%{person_tk [, %%]}<br />]} {person_poli [%%]}{person_nomos_descr [, %%]}{person_country_name [, %%<br />]} {person_mobile [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {person_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {person_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {person_afm [VAT: {person_country_ee}%%<br />]} {person_doy [TAX Office: %%<br />]}</div>\n<div>{hide} <strong>Delivery location</strong><br />{dest_name [Name: %%<br />]} {dest_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {dest_odos [Address: %%{dest_tk [, %%]}<br />]} {dest_poli [%%]}{dest_nomos_descr [, %%]}{dest_country_name [, %%<br />]}</div>\n<div>{hide} <strong>To</strong><br />{warehouse_to_name [Name: %%<br />]} {warehouse_to_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {warehouse_to_odos [Address: %%{warehouse_to_tk [, %%]}<br />]} {warehouse_to_poli [%%]}{warehouse_to_nomos_descr [, %%]}{warehouse_to_country_name [, %%<br />]}</div>\n</td>\n</tr>\n</tbody>\n</table>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div>{doc_note_doc}</div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 50%; vertical-align: top; text-align: center;\">{person_label [%%<br />]}</td>\n<td style=\"width: 50%; vertical-align: top; text-align: center; min-height: 100px;\">Issuer</td>\n</tr>\n</tbody>\n</table>','<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"font-family: Arial; font-size: 16pt; text-align: center; font-weight: bold;\">{doc_title_pre [%% : ]}{doc_title}</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_date [Date: %%<br />]} {doc_seira [Series: %%<br />]} {doc_number [hide:zero][Number: %%<br />]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_skopos_diakinisis [Purpose of Delivery: %%<br />]} {doc_tropos_apostolis [Shipping Method: %%<br />]} {doc_arithmos_aposolis [Shipping number: %%<br />]} {doc_arithmos_oximatos [Transport Number: %%<br />]} {doc_enarji_apostolis [Shipping Start Time: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_canceled_display};\"><span style=\"font-family: Arial;\"><span style=\"font-size: 18.6667px;\"><strong>The cancellation shipping note refers to the shipping note with the following information:</strong></span></span><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_canceled_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_date [Date: %%<br />]} {doc_canceled_seira [Series: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_number [hide:zero][Number: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_credit_display};\"><span style=\"font-family: Arial;\"><span style=\"font-size: 18.6667px;\"><strong>The return note refers to the shipping note with the following information:</strong></span></span><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_credit_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_date [Date: %%<br />]} {doc_credit_seira [Series: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_number [hide:zero][Number: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">#</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 10%; text-align: center;\" nowrap=\"nowrap\">Code</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 90%; text-align: center;\" nowrap=\"nowrap\">Description<br /></th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">Meas<br />unit<br /></th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">Quantity<br /></th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_aa}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 10%;\" nowrap=\"nowrap\">{eidos_code}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 90%;\">{eidos_photo_url [<img style=\"float: left; padding: 0pt 5pt 5pt 0pt; max-width: 30pt; max-height: 30pt;\" src=\"%%\" />]}{eidos_descr}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_mm}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_quantity}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid gray; text-align: left; width: 100%;\" colspan=\"5\">{hide}{eidos_comments}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left; width: 100%;\" colspan=\"4\" nowrap=\"nowrap\"><strong>Totals</strong></td>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{doc_posotita}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; height: 20px; width: 100%;\" colspan=\"5\" nowrap=\"nowrap\"><br /></td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; text-align: right; vertical-align: top; padding-left: 0px; padding-top: 0px; padding-right: 20px; width: 101.858%;\" colspan=\"5\" nowrap=\"nowrap\"><br /></td>\n</tr>\n</tfoot>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>','','','','','',1735860,'','','');");

  gks_run_sql("INSERT INTO `gks_print_objects_forms` (`id_print_object_form`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_id`,`print_object_id`) VALUES 
 (5,'2023-03-09 17:54:17','2023-03-09 17:54:17',1,1,'192.168.1.202','2023-03-09 17:54:17',51,1),
 (6,'2023-03-09 17:54:17','2023-03-09 17:54:17',1,1,'192.168.1.202','2023-03-09 17:54:17',51,2),
 (7,'2023-03-09 18:15:31','2023-03-09 18:15:31',1,1,'192.168.1.202','2023-03-09 18:15:31',53,3),
 (8,'2023-03-09 18:19:09','2023-03-09 18:19:09',1,1,'192.168.1.202','2023-03-09 18:19:09',54,4)");
} 


//$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/_current/_config.php');
//if (strpos($read_file, '$gks_api_php_path_exe') !== false) {
//  echo '/wp-content/plugins/gks_core/_current/_config.php file contains $gks_api_php_path_exe. Delete it<br>'; die();}

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_PHP_PATH_EXE') !== false) {
  echo '_current/_config.php file contains GKS_PHP_PATH_EXE. Delete it<br>';$has_error=true;}
    

$sql="select * from gks_poi_type where id_poi_type=10";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_poi_type` (`id_poi_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`poi_type_descr`,`poi_type_descr_en_US`,`poi_type_sortorder`,`poi_type_comments`,`poi_type_disable`) VALUES 
  (10,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Πόλη','City',10,'',0),
  (201,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-01-01 00:00:00','Σημείο Ενδιαφέροντος','Point o Interest',201,'',0);");
}

$sql="select * from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=10";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
gks_run_sql("INSERT INTO `gks_aade_skopos_diakinisis` (`id_aade_skopos_diakinisis`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`aade_skopos_diakinisis_code`,`aade_skopos_diakinisis_descr`,`sortorder`) VALUES 
 (10,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-09-22 11:21:35',0,'Χρησιδάνειο',10),
 (11,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-09-22 11:21:35',0,'Αντικατάσταση',11);");
}


$sql="select transfer_pricelist_offerrundate_start from gks_transfer_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_pricelist` 
  ADD COLUMN `transfer_pricelist_offerrundate_start` datetime DEFAULT NULL AFTER `transfer_oxima_type_id`,
  ADD COLUMN `transfer_pricelist_offerrundate_end` datetime DEFAULT NULL AFTER `transfer_pricelist_offerrundate_start`,
  ADD INDEX transfer_pricelist_offerrundate_start (transfer_pricelist_offerrundate_start),
  ADD INDEX transfer_pricelist_offerrundate_end (transfer_pricelist_offerrundate_end);");
}

$sql="select poi_type_html_icon from gks_poi_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi_type` 
  ADD COLUMN `poi_type_html_icon` TEXT DEFAULT NULL;");
  
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-map-marker-alt\"></i>' where id_poi_type=1");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-plane\"></i>' where id_poi_type=2");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-ship\"></i>' where id_poi_type=3");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-train\"></i>' where id_poi_type=4");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-city\"></i>' where id_poi_type=10");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-hotel\"></i>' where id_poi_type=101");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-eye\"></i>' where id_poi_type=102");
  gks_run_sql("update gks_poi_type set poi_type_html_icon='<i class=\"fas fa-map-marked-alt\"></i>' where id_poi_type=201");

  

}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_oximata` (
  `id_transfer_reservation_oximata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_asset_id` int(11) NOT NULL DEFAULT 0,
  `oximata_aa` int(11) NOT NULL DEFAULT 0,
  `rnum_adults` int(11) NOT NULL DEFAULT 0,
  `rnum_childs` int(11) NOT NULL DEFAULT 0,
  `rnum_babys` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `myip` varchar(48) DEFAULT NULL,
  `ruser_id` int(11) NOT NULL DEFAULT 0,
  `ruser_lang` varchar(8) NOT NULL DEFAULT 'el-GR',
  `ruser_first_name` varchar(128) DEFAULT NULL,
  `ruser_last_name` varchar(128) DEFAULT NULL,
  `ruser_email` varchar(128) DEFAULT NULL,
  `ruser_mobile` varchar(128) DEFAULT NULL,
  `ruser_ma_odos` varchar(128) DEFAULT NULL,
  `ruser_ma_perioxi` varchar(128) DEFAULT NULL,
  `ruser_ma_poli` varchar(128) DEFAULT NULL,
  `ruser_ma_tk` varchar(64) DEFAULT NULL,
  `ruser_ma_country_id` int(11) NOT NULL DEFAULT 0,
  `ruser_ma_nomos_id` int(11) NOT NULL DEFAULT 0,
  `rsxolio` text DEFAULT NULL,
  `ruser_fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `ruser_pricelist_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `product_descr` varchar(255) DEFAULT NULL,
  `product_fpa_base_id` int(11) NOT NULL DEFAULT 0,
  `product_fpa_id` int(11) NOT NULL DEFAULT 0,
  `product_fpa_pososto` double NOT NULL DEFAULT 0,
  `product_fpa_id_json` text DEFAULT NULL,
  `product_quantity` double NOT NULL DEFAULT 0,
  `product_price_include_vat` tinyint(4) NOT NULL DEFAULT 0,
  `product_price_start_peritem_db` double NOT NULL DEFAULT 0,
  `product_price_start_peritem_net` double NOT NULL DEFAULT 0,
  `product_price_start_peritem_fpa` double NOT NULL DEFAULT 0,
  `product_price_start_peritem_total` double NOT NULL DEFAULT 0,
  `product_price_start_all_net` double NOT NULL DEFAULT 0,
  `product_price_start_all_fpa` double NOT NULL DEFAULT 0,
  `product_price_start_all_total` double NOT NULL DEFAULT 0,
  `product_price_final_peritem_db` double NOT NULL DEFAULT 0,
  `product_price_final_peritem_net` double NOT NULL DEFAULT 0,
  `product_price_final_peritem_fpa` double NOT NULL DEFAULT 0,
  `product_price_final_peritem_total` double NOT NULL DEFAULT 0,
  `product_price_final_all_net` double NOT NULL DEFAULT 0,
  `product_price_final_all_fpa` double NOT NULL DEFAULT 0,
  `product_price_final_all_total` double NOT NULL DEFAULT 0,
  `product_price_ekptosi_net` double NOT NULL DEFAULT 0,
  `product_price_ekptosi_pososto` double NOT NULL DEFAULT 0,
  `product_pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `product_pricelist_item_descr` varchar(255) DEFAULT NULL,
  `product_pricelist_item_percent` double NOT NULL DEFAULT 0,
  `product_price_coupon_use` varchar(255) DEFAULT NULL,
  `product_price_coupon_use_disabled` tinyint(4) NOT NULL DEFAULT 0,
  `product_comments` varchar(255) DEFAULT NULL,
  `oximata_ajia_table_math` text DEFAULT NULL,
  `oximata_ajia_table_html` text DEFAULT NULL,
  `oximata_ajia_table_array` text DEFAULT NULL,
  `product_withheldPercentCategory` int(11) NOT NULL DEFAULT 0,
  `product_withheldAmount` double NOT NULL DEFAULT 0,
  `product_stampDutyPercentCategory` int(11) NOT NULL DEFAULT 0,
  `product_stampDutyAmount` double NOT NULL DEFAULT 0,
  `product_feesPercentCategory` int(11) NOT NULL DEFAULT 0,
  `product_feesAmount` double NOT NULL DEFAULT 0,
  `product_otherTaxesPercentCategory` int(11) NOT NULL DEFAULT 0,
  `product_otherTaxesAmount` double NOT NULL DEFAULT 0,
  `woo_item_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_reservation_oximata`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate_add` (`mydate_add`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `myip` (`myip`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `product_quantity` (`product_quantity`),
  KEY `product_price_include_vat` (`product_price_include_vat`),
  KEY `product_pricelist_item_id` (`product_pricelist_item_id`),
  KEY `product_price_coupon_use` (`product_price_coupon_use`(250)),
  KEY `product_fpa_base_id` (`product_fpa_base_id`),
  KEY `woo_item_id` (`woo_item_id`),
  KEY `transfer_oxima_asset_id` (`transfer_oxima_asset_id`),
  KEY `oximata_aa` (`oximata_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_reservation_oximata_day` (
  `id_transfer_reservation_oximata_day` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_reservation_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_reservation_oximata_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `dtransfer_reservation_status` varchar(16) NOT NULL DEFAULT '01draft',
  `transfer_reservation_oximata_day` datetime NOT NULL,
  `priceperday` double NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `mydate_add` datetime NOT NULL,
  `mydate_edit` datetime NOT NULL,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_transfer_reservation_oximata_day`),
  KEY `transfer_reservation_id` (`transfer_reservation_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `transfer_reservation_oximata_id` (`transfer_reservation_oximata_id`),
  KEY `asset_id` (`asset_id`),
  KEY `transfer_reservation_oximata_day` (`transfer_reservation_oximata_day`),
  KEY `priceperday` (`priceperday`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate_add` (`mydate_add`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `myip` (`myip`),
  KEY `dtransfer_reservation_status` (`dtransfer_reservation_status`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select poi_areas from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi` 
  ADD COLUMN `poi_areas` LONGTEXT DEFAULT NULL AFTER `poi_comments`,
  ADD COLUMN `poi_bound_north` double NOT NULL DEFAULT '0' AFTER `poi_areas`,
  ADD COLUMN `poi_bound_south` double NOT NULL DEFAULT '0' AFTER `poi_bound_north`,
  ADD COLUMN `poi_bound_east`  double NOT NULL DEFAULT '0' AFTER `poi_bound_south`,
  ADD COLUMN `poi_bound_west`  double NOT NULL DEFAULT '0' AFTER `poi_bound_east`,
  ADD INDEX poi_bound_north (poi_bound_north),
  ADD INDEX poi_bound_south (poi_bound_south),
  ADD INDEX poi_bound_east (poi_bound_east),
  ADD INDEX poi_bound_west (poi_bound_west)");
  
  gks_run_sql("UPDATE gks_poi SET poi_country_id = 91 WHERE poi_country_id=0;");

}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental` (
  `id_asset_rental` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `asset_rental_guid` varchar(63) DEFAULT NULL,
  `asset_rental_status` varchar(16) NOT NULL DEFAULT '01draft',
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `num_dates` int(11) NOT NULL DEFAULT 0,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `mydate_add` datetime NOT NULL,
  `mydate_edit` datetime NOT NULL,
  `myip` varchar(48) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_first_name` varchar(128) DEFAULT NULL,
  `user_last_name` varchar(128) DEFAULT NULL,
  `user_email` varchar(128) DEFAULT NULL,
  `user_mobile` varchar(128) DEFAULT NULL,
  `user_ma_odos` varchar(128) DEFAULT NULL,
  `user_ma_perioxi` varchar(128) DEFAULT NULL,
  `user_ma_poli` varchar(128) DEFAULT NULL,
  `user_ma_tk` varchar(64) DEFAULT NULL,
  `user_ma_country_id` int(11) NOT NULL DEFAULT 0,
  `user_ma_nomos_id` int(11) NOT NULL DEFAULT 0,
  `fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `pricelist_id` int(11) NOT NULL DEFAULT 0,
  `assets_plithos` double NOT NULL DEFAULT 0,
  `assets_ajia_total` double NOT NULL DEFAULT 0,
  `others_plithos` double NOT NULL DEFAULT 0,
  `others_ajia_total` double NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_asset_rental`),
  KEY `asset_rental_guid` (`asset_rental_guid`),
  KEY `asset_rental_status` (`asset_rental_status`),
  KEY `date_from` (`date_from`),
  KEY `date_to` (`date_to`),
  KEY `num_dates` (`num_dates`),
  KEY `user_id` (`user_id`),
  KEY `user_first_name` (`user_first_name`),
  KEY `user_last_name` (`user_last_name`),
  KEY `user_email` (`user_email`),
  KEY `user_mobile` (`user_mobile`),
  KEY `user_ma_odos` (`user_ma_odos`),
  KEY `user_ma_perioxi` (`user_ma_perioxi`),
  KEY `user_ma_poli` (`user_ma_poli`),
  KEY `user_ma_tk` (`user_ma_tk`),
  KEY `user_ma_country_id` (`user_ma_country_id`),
  KEY `user_ma_nomos_id` (`user_ma_nomos_id`),
  KEY `fiscal_position_id` (`fiscal_position_id`),
  KEY `pricelist_id` (`pricelist_id`),
  KEY `myip` (`myip`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate_add` (`mydate_add`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `company_id` (`company_id`),
  KEY `company_sub_id` (`company_sub_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_asset` (
  `id_asset_rental_asset` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_rental_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL,
  `num_dates` int(11) NOT NULL DEFAULT 0,
  `fnum_adults` int(11) NOT NULL DEFAULT 0,
  `fnum_childs` int(11) NOT NULL DEFAULT 0,
  `ajia_unit` double NOT NULL DEFAULT 0,
  `ajia_total` double NOT NULL DEFAULT 0,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `mydate_add` datetime NOT NULL,
  `mydate_edit` datetime NOT NULL,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fuser_id` int(11) NOT NULL DEFAULT 0,
  `fuser_lang` varchar(8) NOT NULL DEFAULT 'el-GR',
  `fuser_first_name` varchar(128) DEFAULT NULL,
  `fuser_last_name` varchar(128) DEFAULT NULL,
  `fuser_email` varchar(128) DEFAULT NULL,
  `fuser_mobile` varchar(128) DEFAULT NULL,
  `fuser_ma_odos` varchar(128) DEFAULT NULL,
  `fuser_ma_perioxi` varchar(128) DEFAULT NULL,
  `fuser_ma_poli` varchar(128) DEFAULT NULL,
  `fuser_ma_tk` varchar(64) DEFAULT NULL,
  `fuser_ma_country_id` int(11) NOT NULL DEFAULT 0,
  `fuser_ma_nomos_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_asset_rental_asset`),
  KEY `asset_rental_id` (`asset_rental_id`),
  KEY `asset_id` (`asset_id`),
  KEY `check_in` (`check_in`),
  KEY `check_out` (`check_out`),
  KEY `num_dates` (`num_dates`),
  KEY `ajia_unit` (`ajia_unit`),
  KEY `ajia_total` (`ajia_total`),
  KEY `myip` (`myip`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate_add` (`mydate_add`),
  KEY `mydate_edit` (`mydate_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");





gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_asset_day` (
  `id_asset_rental_asset_day` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_rental_id` int(11) NOT NULL DEFAULT 0,
  `asset_rental_asset_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `dasset_rental_status` varchar(16) NOT NULL DEFAULT '01draft',
  `asset_rental_asset_day` datetime NOT NULL,
  `priceperday` double NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `mydate_add` datetime NOT NULL,
  `mydate_edit` datetime NOT NULL,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_asset_rental_asset_day`),
  KEY `asset_rental_id` (`asset_rental_id`),
  KEY `asset_rental_asset_id` (`asset_rental_asset_id`),
  KEY `asset_id` (`asset_id`),
  KEY `dasset_rental_status` (`dasset_rental_status`),
  KEY `asset_rental_asset_day` (`asset_rental_asset_day`),
  KEY `priceperday` (`priceperday`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate_add` (`mydate_add`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `myip` (`myip`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select transfer_oxima_type_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `transfer_oxima_type_id` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `asset_rental_status` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD INDEX transfer_oxima_type_id (transfer_oxima_type_id),
  ADD INDEX asset_rental_status (asset_rental_status)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_availability` (
  `id_assets_rental_availability` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `asset_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `availability_from` datetime DEFAULT NULL,
  `availability_to` datetime DEFAULT NULL,
  `availability_descr` varchar(190) DEFAULT NULL,
  `availability_status` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_de` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_tr` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_te` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_pe` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_pa` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_sa` tinyint(4) NOT NULL DEFAULT 0,
  `avail_weekday_ky` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_assets_rental_availability`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `asset_id` (`asset_id`),
  KEY `asset_type_id` (`asset_type_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `availability_from` (`availability_from`),
  KEY `availability_to` (`availability_to`),
  KEY `availability_descr` (`availability_descr`),
  KEY `availability_status` (`availability_status`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_availability_day` (
  `id_assets_rental_availability_day` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assets_rental_availability_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `asset_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `availability_day` datetime DEFAULT NULL,
  `availability_status` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_assets_rental_availability_day`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  UNIQUE KEY `myuniq` (`asset_id`,`asset_type_id`,`availability_day`),
  KEY `assets_rental_availability_id` (`assets_rental_availability_id`),
  KEY `asset_id` (`asset_id`),
  KEY `asset_type_id` (`asset_type_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `availability_day` (`availability_day`),
  KEY `availability_status` (`availability_status`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_type` (
  `id_asset_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_type_descr` varchar(190) DEFAULT NULL,
  `asset_type_prefix` varchar(190) DEFAULT NULL,
  `asset_type_sortorder` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_asset_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `asset_type_descr` (`asset_type_descr`),
  KEY `asset_type_prefix` (`asset_type_prefix`),
  KEY `asset_type_sortorder` (`asset_type_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select * from gks_assets_type where id_asset_type=1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_assets_type` (`id_asset_type`,`asset_type_descr`,`asset_type_prefix`,`asset_type_sortorder`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`) VALUES 
 (1,'Φωτογραφική Μηχανή','ΦΩΤ',1,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (2,'Φορητοί Εκτυπωτές','ΦΕΚ',10,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (6,'Φακοί','ΦΚΟ',2,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (7,'Φλας','ΦΛΑ',3,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (9,'Εκτυπωτές Mini Lab','MHX',200,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (12,'Οθόνες','ΟΘΟ',500,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (13,'Σταθερές Μονάδες','ΣΜΟ',501,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (14,'Laptop','LAP',502,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (16,'Ταμειακές Μηχανές','TM',600,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (17,'Κεραίες','ΚΕΡ',801,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (18,'Switch','SWI',803,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (19,'Access Point','ACC',802,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (20,'Routers','ROU',804,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (21,'IP Cameras','IPC',850,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (22,'Καταγραφικά CCTV','CCT',851,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (23,'Tablets','TAB',503,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (24,'POS Ασύρματο','POS',601,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (25,'POS Ενσύρματο','POS',602,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (26,'Οχήματα','OXH',900,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1'),
 (27,'Κινητά','KIN',510,'2020-01-01 00:00:00','2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1');");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_price` (
  `id_assets_rental_price` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `price_from` datetime DEFAULT NULL,
  `price_to` datetime DEFAULT NULL,
  `price_descr` varchar(190) DEFAULT NULL,
  `price` double NOT NULL DEFAULT 0,
  `price_weekday_de` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_tr` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_te` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_pe` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_pa` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_sa` tinyint(4) NOT NULL DEFAULT 0,
  `price_weekday_ky` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_assets_rental_price`),
  KEY `asset_type_id` (`asset_type_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `price_from` (`price_from`),
  KEY `price_to` (`price_to`),
  KEY `price_descr` (`price_descr`),
  KEY `price` (`price`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_rental_price_day` (
  `id_assets_rental_price_day` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assets_rental_price_id` int(11) NOT NULL DEFAULT 0,
  `asset_type_id` int(11) NOT NULL DEFAULT 0,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `price_day` datetime DEFAULT NULL,
  `price` double NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id_assets_rental_price_day`),
  UNIQUE KEY `myuniq` (`asset_type_id`,`price_day`),
  KEY `assets_rental_price_id` (`assets_rental_price_id`),
  KEY `asset_type_id` (`asset_type_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `price_day` (`price_day`),
  KEY `price` (`price`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select rsrv_oxima_num_booster from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata`
  ADD COLUMN `rsrv_oxima_num_booster` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_num_kareklakia` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_num_amajidia` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_num_golfbag` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_num_skis` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_num_5minstop` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_oxima_5minstop_descr` TEXT DEFAULT NULL;");
}




$sql="select transfer_sms_text_message_enable from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_sms_text_message_enable` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_sms_text_message_price` DOUBLE NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_cancellation_protection_enable` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_cancellation_protection_price` DOUBLE NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_terms_and_policy_frontend` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_empty_cart_woo` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_product_id_sms_text_message` INTEGER NOT NULL DEFAULT 0,
  ADD COLUMN `transfer_product_id_cancellation_protection` INTEGER NOT NULL DEFAULT 0;");
}


$sql="select rsrv_sms_text_message_enable from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
  ADD COLUMN `rsrv_sms_text_message_enable` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_sms_text_message_price` DOUBLE NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_cancellation_protection_enable` TINYINT NOT NULL DEFAULT 0,
  ADD COLUMN `rsrv_cancellation_protection_price` DOUBLE NOT NULL DEFAULT 0,
  ADD COLUMN rsrv_sms_text_message_data_str LONGTEXT DEFAULT NULL,
  ADD COLUMN rsrv_cancellation_protection_data_str LONGTEXT DEFAULT NULL;");
}




$sql="select outward_from_pick_up_point from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 

	ADD COLUMN `outward_from_pick_up_point` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_from_pick_up_time` datetime DEFAULT NULL,
	ADD COLUMN `outward_from_pick_up_time_max` datetime DEFAULT NULL,
	ADD COLUMN `outward_from_airline` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_from_flight_number` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_from_originating_airport` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_from_flight_arrival_time` datetime DEFAULT NULL,
	
	ADD COLUMN `outward_to_drop_off_point` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_to_departure_airline` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_to_flight_number` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `outward_to_flight_departure_time` datetime DEFAULT NULL,
	
	
	ADD COLUMN `return_from_address_different` TINYINT NOT NULL DEFAULT 0,
	ADD COLUMN `return_from_pick_up_point` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_from_pick_up_time` datetime DEFAULT NULL,
	ADD COLUMN `return_from_pick_up_time_max` datetime DEFAULT NULL,
	ADD COLUMN `return_from_airline` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_from_flight_number` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_from_originating_airport` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_from_flight_arrival_time` datetime DEFAULT NULL,

	ADD COLUMN `return_to_airline` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_to_flight_number` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	ADD COLUMN `return_to_flight_departure_time` datetime DEFAULT NULL,
	ADD COLUMN `return_to_address_different` TINYINT NOT NULL DEFAULT 0,
	ADD COLUMN `return_to_drop_off_point` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
}

$sql="select is_return_transfer_for_id from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
  ADD COLUMN `is_return_transfer_for_id` INTEGER NOT NULL DEFAULT 0 after order_id,
  ADD INDEX is_return_transfer_for_id (is_return_transfer_for_id)");
}

$sql="select is_return_oxima_for_id from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata` 
  ADD COLUMN `is_return_oxima_for_id` INTEGER NOT NULL DEFAULT 0 after transfer_oxima_asset_id,
  ADD INDEX is_return_oxima_for_id (is_return_oxima_for_id)");
}


$sql="select update_state_gks_transfer from gks_eshops limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshops`
  ADD COLUMN `update_state_gks_transfer` TEXT DEFAULT NULL;");
}

//$sql="select guru_update_disable from gks_transfer_reservation limit 1";
//$result = $db_link->query($sql);
//if (!$result) { //must return error
//  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
//  ADD COLUMN `guru_update_disable` tinyint(4) NOT NULL DEFAULT 0");
//}

$sql="select phone_code from gks_country limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_country` 
  ADD COLUMN `phone_code` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD INDEX phone_code (phone_code)");
  
  gks_run_sql("update gks_country set phone_code='+376' where id_country=3;");
  gks_run_sql("update gks_country set phone_code='+971' where id_country=4;");
  gks_run_sql("update gks_country set phone_code='+93' where id_country=5;");
  gks_run_sql("update gks_country set phone_code='+268' where id_country=6;");
  gks_run_sql("update gks_country set phone_code='+264' where id_country=7;");
  gks_run_sql("update gks_country set phone_code='+355' where id_country=8;");
  gks_run_sql("update gks_country set phone_code='+374' where id_country=9;");
  gks_run_sql("update gks_country set phone_code='+244' where id_country=10;");
  gks_run_sql("update gks_country set phone_code='+672' where id_country=12;");
  gks_run_sql("update gks_country set phone_code='+54' where id_country=13;");
  gks_run_sql("update gks_country set phone_code='+684' where id_country=14;");
  gks_run_sql("update gks_country set phone_code='+43' where id_country=15;");
  gks_run_sql("update gks_country set phone_code='+61' where id_country=16;");
  gks_run_sql("update gks_country set phone_code='+297' where id_country=17;");
  gks_run_sql("update gks_country set phone_code='+358' where id_country=18;");
  gks_run_sql("update gks_country set phone_code='+994' where id_country=19;");
  gks_run_sql("update gks_country set phone_code='+387' where id_country=20;");
  gks_run_sql("update gks_country set phone_code='+246' where id_country=21;");
  gks_run_sql("update gks_country set phone_code='+880' where id_country=22;");
  gks_run_sql("update gks_country set phone_code='+32' where id_country=23;");
  gks_run_sql("update gks_country set phone_code='+226' where id_country=24;");
  gks_run_sql("update gks_country set phone_code='+359' where id_country=25;");
  gks_run_sql("update gks_country set phone_code='+973' where id_country=26;");
  gks_run_sql("update gks_country set phone_code='+257' where id_country=27;");
  gks_run_sql("update gks_country set phone_code='+229' where id_country=28;");
  gks_run_sql("update gks_country set phone_code='+590' where id_country=29;");
  gks_run_sql("update gks_country set phone_code='+441' where id_country=30;");
  gks_run_sql("update gks_country set phone_code='+673' where id_country=31;");
  gks_run_sql("update gks_country set phone_code='+591' where id_country=32;");
  gks_run_sql("update gks_country set phone_code='+599' where id_country=33;");
  gks_run_sql("update gks_country set phone_code='+55' where id_country=34;");
  gks_run_sql("update gks_country set phone_code='+242' where id_country=35;");
  gks_run_sql("update gks_country set phone_code='+975' where id_country=36;");
  gks_run_sql("update gks_country set phone_code='+267' where id_country=37;");
  gks_run_sql("update gks_country set phone_code='+375' where id_country=38;");
  gks_run_sql("update gks_country set phone_code='+501' where id_country=39;");
  gks_run_sql("update gks_country set phone_code='+1' where id_country=40;");
  gks_run_sql("update gks_country set phone_code='+61' where id_country=41;");
  gks_run_sql("update gks_country set phone_code='+243' where id_country=42;");
  gks_run_sql("update gks_country set phone_code='+236' where id_country=43;");
  gks_run_sql("update gks_country set phone_code='+243' where id_country=44;");
  gks_run_sql("update gks_country set phone_code='+41' where id_country=45;");
  gks_run_sql("update gks_country set phone_code='+225' where id_country=46;");
  gks_run_sql("update gks_country set phone_code='+682' where id_country=47;");
  gks_run_sql("update gks_country set phone_code='+56' where id_country=48;");
  gks_run_sql("update gks_country set phone_code='+237' where id_country=49;");
  gks_run_sql("update gks_country set phone_code='+86' where id_country=50;");
  gks_run_sql("update gks_country set phone_code='+57' where id_country=51;");
  gks_run_sql("update gks_country set phone_code='+506' where id_country=52;");
  gks_run_sql("update gks_country set phone_code='+53' where id_country=53;");
  gks_run_sql("update gks_country set phone_code='+238' where id_country=54;");
  gks_run_sql("update gks_country set phone_code='+599' where id_country=55;");
  gks_run_sql("update gks_country set phone_code='+53' where id_country=56;");
  gks_run_sql("update gks_country set phone_code='+357' where id_country=57;");
  gks_run_sql("update gks_country set phone_code='+420' where id_country=58;");
  gks_run_sql("update gks_country set phone_code='+49' where id_country=59;");
  gks_run_sql("update gks_country set phone_code='+253' where id_country=60;");
  gks_run_sql("update gks_country set phone_code='+45' where id_country=61;");
  gks_run_sql("update gks_country set phone_code='+767' where id_country=62;");
  gks_run_sql("update gks_country set phone_code='+1829' where id_country=63;");
  gks_run_sql("update gks_country set phone_code='+213' where id_country=64;");
  gks_run_sql("update gks_country set phone_code='+593' where id_country=65;");
  gks_run_sql("update gks_country set phone_code='+372' where id_country=66;");
  gks_run_sql("update gks_country set phone_code='+20' where id_country=67;");
  gks_run_sql("update gks_country set phone_code='+291' where id_country=68;");
  gks_run_sql("update gks_country set phone_code='+34' where id_country=69;");
  gks_run_sql("update gks_country set phone_code='+251' where id_country=70;");
  gks_run_sql("update gks_country set phone_code='+358' where id_country=72;");
  gks_run_sql("update gks_country set phone_code='+679' where id_country=73;");
  gks_run_sql("update gks_country set phone_code='+500' where id_country=74;");
  gks_run_sql("update gks_country set phone_code='+691' where id_country=75;");
  gks_run_sql("update gks_country set phone_code='+298' where id_country=76;");
  gks_run_sql("update gks_country set phone_code='+33' where id_country=77;");
  gks_run_sql("update gks_country set phone_code='+241' where id_country=78;");
  gks_run_sql("update gks_country set phone_code='+44' where id_country=79;");
  gks_run_sql("update gks_country set phone_code='+1473' where id_country=80;");
  gks_run_sql("update gks_country set phone_code='+995' where id_country=81;");
  gks_run_sql("update gks_country set phone_code='+594' where id_country=82;");
  gks_run_sql("update gks_country set phone_code='+44-1534' where id_country=83;");
  gks_run_sql("update gks_country set phone_code='+233' where id_country=84;");
  gks_run_sql("update gks_country set phone_code='+350' where id_country=85;");
  gks_run_sql("update gks_country set phone_code='+299' where id_country=86;");
  gks_run_sql("update gks_country set phone_code='+220' where id_country=87;");
  gks_run_sql("update gks_country set phone_code='+224' where id_country=88;");
  gks_run_sql("update gks_country set phone_code='+590' where id_country=89;");
  gks_run_sql("update gks_country set phone_code='+240' where id_country=90;");
  gks_run_sql("update gks_country set phone_code='+30' where id_country=91;");
  gks_run_sql("update gks_country set phone_code='+998' where id_country=92;");
  gks_run_sql("update gks_country set phone_code='+502' where id_country=93;");
  gks_run_sql("update gks_country set phone_code='+671' where id_country=94;");
  gks_run_sql("update gks_country set phone_code='+245' where id_country=95;");
  gks_run_sql("update gks_country set phone_code='+592' where id_country=96;");
  gks_run_sql("update gks_country set phone_code='+852' where id_country=97;");
  gks_run_sql("update gks_country set phone_code='+504' where id_country=98;");
  gks_run_sql("update gks_country set phone_code='+385' where id_country=99;");
  gks_run_sql("update gks_country set phone_code='+509' where id_country=100;");
  gks_run_sql("update gks_country set phone_code='+36' where id_country=101;");
  gks_run_sql("update gks_country set phone_code='+62' where id_country=102;");
  gks_run_sql("update gks_country set phone_code='+353' where id_country=103;");
  gks_run_sql("update gks_country set phone_code='+972' where id_country=104;");
  gks_run_sql("update gks_country set phone_code='+44' where id_country=105;");
  gks_run_sql("update gks_country set phone_code='+91' where id_country=106;");
  gks_run_sql("update gks_country set phone_code='+246' where id_country=107;");
  gks_run_sql("update gks_country set phone_code='+964' where id_country=108;");
  gks_run_sql("update gks_country set phone_code='+98' where id_country=109;");
  gks_run_sql("update gks_country set phone_code='+354' where id_country=110;");
  gks_run_sql("update gks_country set phone_code='+39' where id_country=111;");
  gks_run_sql("update gks_country set phone_code='+876' where id_country=113;");
  gks_run_sql("update gks_country set phone_code='+962' where id_country=114;");
  gks_run_sql("update gks_country set phone_code='+81' where id_country=115;");
  gks_run_sql("update gks_country set phone_code='+254' where id_country=116;");
  gks_run_sql("update gks_country set phone_code='+996' where id_country=117;");
  gks_run_sql("update gks_country set phone_code='+855' where id_country=118;");
  gks_run_sql("update gks_country set phone_code='+686' where id_country=119;");
  gks_run_sql("update gks_country set phone_code='+269' where id_country=120;");
  gks_run_sql("update gks_country set phone_code='+869' where id_country=121;");
  gks_run_sql("update gks_country set phone_code='+850' where id_country=122;");
  gks_run_sql("update gks_country set phone_code='+82' where id_country=123;");
  gks_run_sql("update gks_country set phone_code='+965' where id_country=124;");
  gks_run_sql("update gks_country set phone_code='+345' where id_country=125;");
  gks_run_sql("update gks_country set phone_code='+7' where id_country=126;");
  gks_run_sql("update gks_country set phone_code='+856' where id_country=127;");
  gks_run_sql("update gks_country set phone_code='+961' where id_country=128;");
  gks_run_sql("update gks_country set phone_code='+758' where id_country=129;");
  gks_run_sql("update gks_country set phone_code='+423' where id_country=130;");
  gks_run_sql("update gks_country set phone_code='+94' where id_country=131;");
  gks_run_sql("update gks_country set phone_code='+231' where id_country=132;");
  gks_run_sql("update gks_country set phone_code='+266' where id_country=133;");
  gks_run_sql("update gks_country set phone_code='+370' where id_country=134;");
  gks_run_sql("update gks_country set phone_code='+352' where id_country=135;");
  gks_run_sql("update gks_country set phone_code='+371' where id_country=136;");
  gks_run_sql("update gks_country set phone_code='+218' where id_country=137;");
  gks_run_sql("update gks_country set phone_code='+212' where id_country=138;");
  gks_run_sql("update gks_country set phone_code='+377' where id_country=139;");
  gks_run_sql("update gks_country set phone_code='+373' where id_country=140;");
  gks_run_sql("update gks_country set phone_code='+382' where id_country=141;");
  gks_run_sql("update gks_country set phone_code='+590' where id_country=142;");
  gks_run_sql("update gks_country set phone_code='+261' where id_country=143;");
  gks_run_sql("update gks_country set phone_code='+692' where id_country=144;");
  gks_run_sql("update gks_country set phone_code='+389' where id_country=145;");
  gks_run_sql("update gks_country set phone_code='+223' where id_country=146;");
  gks_run_sql("update gks_country set phone_code='+95' where id_country=147;");
  gks_run_sql("update gks_country set phone_code='+976' where id_country=148;");
  gks_run_sql("update gks_country set phone_code='+853' where id_country=149;");
  gks_run_sql("update gks_country set phone_code='+670' where id_country=150;");
  gks_run_sql("update gks_country set phone_code='+596' where id_country=151;");
  gks_run_sql("update gks_country set phone_code='+222' where id_country=152;");
  gks_run_sql("update gks_country set phone_code='+664' where id_country=153;");
  gks_run_sql("update gks_country set phone_code='+356' where id_country=154;");
  gks_run_sql("update gks_country set phone_code='+230' where id_country=155;");
  gks_run_sql("update gks_country set phone_code='+960' where id_country=156;");
  gks_run_sql("update gks_country set phone_code='+265' where id_country=157;");
  gks_run_sql("update gks_country set phone_code='+52' where id_country=158;");
  gks_run_sql("update gks_country set phone_code='+60' where id_country=159;");
  gks_run_sql("update gks_country set phone_code='+258' where id_country=160;");
  gks_run_sql("update gks_country set phone_code='+264' where id_country=161;");
  gks_run_sql("update gks_country set phone_code='+687' where id_country=162;");
  gks_run_sql("update gks_country set phone_code='+227' where id_country=163;");
  gks_run_sql("update gks_country set phone_code='+672' where id_country=164;");
  gks_run_sql("update gks_country set phone_code='+234' where id_country=165;");
  gks_run_sql("update gks_country set phone_code='+505' where id_country=166;");
  gks_run_sql("update gks_country set phone_code='+31' where id_country=167;");
  gks_run_sql("update gks_country set phone_code='+47' where id_country=168;");
  gks_run_sql("update gks_country set phone_code='+977' where id_country=169;");
  gks_run_sql("update gks_country set phone_code='+674' where id_country=170;");
  gks_run_sql("update gks_country set phone_code='+683' where id_country=171;");
  gks_run_sql("update gks_country set phone_code='+64' where id_country=172;");
  gks_run_sql("update gks_country set phone_code='+968' where id_country=173;");
  gks_run_sql("update gks_country set phone_code='+507' where id_country=174;");
  gks_run_sql("update gks_country set phone_code='+51' where id_country=175;");
  gks_run_sql("update gks_country set phone_code='+689' where id_country=176;");
  gks_run_sql("update gks_country set phone_code='+675' where id_country=177;");
  gks_run_sql("update gks_country set phone_code='+63' where id_country=178;");
  gks_run_sql("update gks_country set phone_code='+92' where id_country=179;");
  gks_run_sql("update gks_country set phone_code='+48' where id_country=180;");
  gks_run_sql("update gks_country set phone_code='+508' where id_country=181;");
  gks_run_sql("update gks_country set phone_code='+872' where id_country=182;");
  gks_run_sql("update gks_country set phone_code='+1787' where id_country=183;");
  gks_run_sql("update gks_country set phone_code='+970' where id_country=184;");
  gks_run_sql("update gks_country set phone_code='+351' where id_country=185;");
  gks_run_sql("update gks_country set phone_code='+680' where id_country=186;");
  gks_run_sql("update gks_country set phone_code='+595' where id_country=187;");
  gks_run_sql("update gks_country set phone_code='+974' where id_country=188;");
  gks_run_sql("update gks_country set phone_code='+262' where id_country=189;");
  gks_run_sql("update gks_country set phone_code='+40' where id_country=190;");
  gks_run_sql("update gks_country set phone_code='+381' where id_country=191;");
  gks_run_sql("update gks_country set phone_code='+7' where id_country=192;");
  gks_run_sql("update gks_country set phone_code='+250' where id_country=193;");
  gks_run_sql("update gks_country set phone_code='+966' where id_country=194;");
  gks_run_sql("update gks_country set phone_code='+677' where id_country=195;");
  gks_run_sql("update gks_country set phone_code='+248' where id_country=196;");
  gks_run_sql("update gks_country set phone_code='+249' where id_country=197;");
  gks_run_sql("update gks_country set phone_code='+46' where id_country=198;");
  gks_run_sql("update gks_country set phone_code='+65' where id_country=199;");
  gks_run_sql("update gks_country set phone_code='+290' where id_country=200;");
  gks_run_sql("update gks_country set phone_code='+386' where id_country=201;");
  gks_run_sql("update gks_country set phone_code='+79' where id_country=202;");
  gks_run_sql("update gks_country set phone_code='+421' where id_country=203;");
  gks_run_sql("update gks_country set phone_code='+232' where id_country=204;");
  gks_run_sql("update gks_country set phone_code='+378' where id_country=205;");
  gks_run_sql("update gks_country set phone_code='+221' where id_country=206;");
  gks_run_sql("update gks_country set phone_code='+252' where id_country=207;");
  gks_run_sql("update gks_country set phone_code='+597' where id_country=208;");
  gks_run_sql("update gks_country set phone_code='+211' where id_country=209;");
  gks_run_sql("update gks_country set phone_code='+239' where id_country=210;");
  gks_run_sql("update gks_country set phone_code='+503' where id_country=211;");
  gks_run_sql("update gks_country set phone_code='+599' where id_country=212;");
  gks_run_sql("update gks_country set phone_code='+963' where id_country=213;");
  gks_run_sql("update gks_country set phone_code='+268' where id_country=214;");
  gks_run_sql("update gks_country set phone_code='+649' where id_country=215;");
  gks_run_sql("update gks_country set phone_code='+235' where id_country=216;");
  gks_run_sql("update gks_country set phone_code='+262' where id_country=217;");
  gks_run_sql("update gks_country set phone_code='+228' where id_country=218;");
  gks_run_sql("update gks_country set phone_code='+66' where id_country=219;");
  gks_run_sql("update gks_country set phone_code='+992' where id_country=220;");
  gks_run_sql("update gks_country set phone_code='+690' where id_country=221;");
  gks_run_sql("update gks_country set phone_code='+670' where id_country=222;");
  gks_run_sql("update gks_country set phone_code='+993' where id_country=223;");
  gks_run_sql("update gks_country set phone_code='+216' where id_country=224;");
  gks_run_sql("update gks_country set phone_code='+676' where id_country=225;");
  gks_run_sql("update gks_country set phone_code='+90' where id_country=226;");
  gks_run_sql("update gks_country set phone_code='+868' where id_country=227;");
  gks_run_sql("update gks_country set phone_code='+688' where id_country=228;");
  gks_run_sql("update gks_country set phone_code='+886' where id_country=229;");
  gks_run_sql("update gks_country set phone_code='+255' where id_country=230;");
  gks_run_sql("update gks_country set phone_code='+380' where id_country=231;");
  gks_run_sql("update gks_country set phone_code='+256' where id_country=232;");
  gks_run_sql("update gks_country set phone_code='+808' where id_country=233;");
  gks_run_sql("update gks_country set phone_code='+1' where id_country=234;");
  gks_run_sql("update gks_country set phone_code='+598' where id_country=235;");
  gks_run_sql("update gks_country set phone_code='+998' where id_country=236;");
  gks_run_sql("update gks_country set phone_code='+39' where id_country=237;");
  gks_run_sql("update gks_country set phone_code='+784' where id_country=238;");
  gks_run_sql("update gks_country set phone_code='+58' where id_country=239;");
  gks_run_sql("update gks_country set phone_code='+284' where id_country=240;");
  gks_run_sql("update gks_country set phone_code='+340' where id_country=241;");
  gks_run_sql("update gks_country set phone_code='+84' where id_country=242;");
  gks_run_sql("update gks_country set phone_code='+678' where id_country=243;");
  gks_run_sql("update gks_country set phone_code='+681' where id_country=244;");
  gks_run_sql("update gks_country set phone_code='+685' where id_country=245;");
  gks_run_sql("update gks_country set phone_code='+967' where id_country=246;");
  gks_run_sql("update gks_country set phone_code='+269' where id_country=247;");
  gks_run_sql("update gks_country set phone_code='+27' where id_country=248;");
  gks_run_sql("update gks_country set phone_code='+260' where id_country=249;");
  gks_run_sql("update gks_country set phone_code='+263' where id_country=250;");
  gks_run_sql("update gks_country set phone_code='+212' where id_country=251;");
  gks_run_sql("update gks_country set phone_code='+47' where id_country=252;");
  gks_run_sql("update gks_country set phone_code='+672' where id_country=253;");
  gks_run_sql("update gks_country set phone_code='+599' where id_country=254;");
  
  
  gks_run_sql("update gks_country set country_name='Σουαζιλάνδη' where id_country=214");
  gks_run_sql("delete from gks_country where id_country=44");
  
}

$sql="select woo_item_aa from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata` 
  ADD COLUMN `woo_item_aa` INTEGER NOT NULL DEFAULT 0 after woo_item_id,
  ADD INDEX woo_item_aa (woo_item_aa)");
}


$sql="select group_type from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata` 
  ADD COLUMN `group_type` varchar(32) DEFAULT NULL after woo_item_aa,
  ADD INDEX group_type (group_type)");
}

$sql="select transfer_booking_number from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
  ADD COLUMN `transfer_booking_number` varchar(64) DEFAULT NULL after transfer_reservation_guid,
  ADD INDEX transfer_booking_number (transfer_booking_number)");
}

$sql="select woo_start_booking_number from gks_eshops limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshops` 
  ADD COLUMN `woo_start_booking_number` varchar(64) DEFAULT NULL,
  ADD INDEX woo_start_booking_number (woo_start_booking_number)");
}

$sql="select transfer_multi_cars from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_multi_cars` tinyint(4) NOT NULL DEFAULT '0',
  ADD INDEX transfer_multi_cars (transfer_multi_cars)");
}







gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_airline` (
  `id_airline` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `airline_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `airline_iata_code` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `airline_icao_code` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id_airline`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `airline_name` (`airline_name`(250)),
  KEY `airline_iata_code` (`airline_iata_code`),
  KEY `airline_icao_code` (`airline_icao_code`)
  
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select * from gks_permission_object where id_permission_object=2190";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2190,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_airline','Aεροπορικές εταιρείες',2190)");

}

$sql="select * from gks_permission_object where id_permission_object=2191";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2191,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_flights_routes','Πλάνο Πτήσεων',2191),
   (2192,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_flights','Κατάσταση Πτήσεων',2192)");

}


$sql="select transfer_outward_from_pick_up_point from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer`
  ADD COLUMN `transfer_outward_from_airplane_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_outward_from_train_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_outward_from_cruise_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  
  ADD COLUMN `transfer_outward_from_pick_up_point` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable',
  ADD COLUMN `transfer_outward_from_pick_up_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable',
  ADD COLUMN `transfer_outward_from_pick_up_time_start_minutes_airplane` int(11) NOT NULL DEFAULT '120',
  ADD COLUMN `transfer_outward_from_pick_up_time_start_minutes_train` int(11) NOT NULL DEFAULT '30',
  ADD COLUMN `transfer_outward_from_pick_up_time_start_minutes_cruise` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `transfer_outward_from_pick_up_time_text` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_outward_from_flight_arrival_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'lock',

  ADD COLUMN `transfer_outward_to_drop_off_point` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable',
  ADD COLUMN `transfer_outward_to_flight_departure_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'lock',


  ADD COLUMN `transfer_return_from_airplane_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_return_from_train_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_return_from_cruise_message` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,

  ADD COLUMN `transfer_return_from_address_different` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable',
  ADD COLUMN `transfer_return_from_pick_up_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable',
  ADD COLUMN `transfer_return_from_pick_up_time_start_minutes_airplane` int(11) NOT NULL DEFAULT '120',
  ADD COLUMN `transfer_return_from_pick_up_time_start_minutes_train` int(11) NOT NULL DEFAULT '30',
  ADD COLUMN `transfer_return_from_pick_up_time_start_minutes_cruise` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `transfer_return_from_pick_up_time_text` text COLLATE utf8mb4_unicode_520_ci DEFAULT null,
  ADD COLUMN `transfer_return_from_flight_arrival_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'lock',

  ADD COLUMN `transfer_return_to_flight_departure_time` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'lock',
  ADD COLUMN `transfer_return_to_address_different` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'disable';
  
  ");
  //  return_from_pick_up_point <- transfer_return_from_address_different
  //  return_to_drop_off_point  <- return_to_address_different

}


//$sql="select guru_send_status from gks_transfer_reservation limit 1";
//$result = $db_link->query($sql);
//if (!$result) { //must return error
//  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
//  ADD COLUMN `guru_send_status` varchar(64) DEFAULT NULL,
//  ADD COLUMN `guru_send_date` datetime DEFAULT NULL,
//  ADD COLUMN `guru_response` TEXT DEFAULT NULL,
//  ADD COLUMN `guru_remote_id` varchar(190) DEFAULT NULL;");
//}

$sql="select woo_eshop_id from gks_orders_messages limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders_messages` 
  ADD COLUMN woo_eshop_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_comment_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_author varchar(190) DEFAULT NULL,
  ADD INDEX woo_eshop_id (woo_eshop_id),
  ADD INDEX woo_comment_id (woo_comment_id);");
}

$sql="select woo_eshop_id from gks_acc_inv_messages limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv_messages` 
  ADD COLUMN woo_eshop_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_comment_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_author varchar(190) DEFAULT NULL,
  ADD INDEX woo_eshop_id (woo_eshop_id),
  ADD INDEX woo_comment_id (woo_comment_id);");
}

$sql="select woo_eshop_id from gks_hotel_reservation_messages limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation_messages` 
  ADD COLUMN woo_eshop_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_comment_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_author varchar(190) DEFAULT NULL,
  ADD INDEX woo_eshop_id (woo_eshop_id),
  ADD INDEX woo_comment_id (woo_comment_id);");
}

$sql="select woo_eshop_id from gks_transfer_reservation_messages limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_messages` 
  ADD COLUMN woo_eshop_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_comment_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN woo_author varchar(190) DEFAULT NULL,
  ADD INDEX woo_eshop_id (woo_eshop_id),
  ADD INDEX woo_comment_id (woo_comment_id);");
}

$sql="select cancel_hash from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation` 
  ADD COLUMN cancel_hash varchar(190) DEFAULT NULL,
  ADD COLUMN cancel_until datetime DEFAULT NULL;");
}

$sql="select viva_merchant_id from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN viva_merchant_id varchar(240) DEFAULT NULL,
  ADD COLUMN viva_api_key varchar(240) DEFAULT NULL,
  ADD COLUMN viva_verify_webhook_page_key varchar(240) DEFAULT NULL");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_viva_transaction` (
  `id_viva_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `add_date` datetime NOT NULL,
  `edit_date` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(48) DEFAULT NULL,
  `myfrom` varchar(8) DEFAULT NULL,
  `EventTypeId` int(11) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `Amount` double NOT NULL DEFAULT '0',
  `CardNumber` varchar(32) DEFAULT NULL,
  `CardTypeId` int(11) DEFAULT NULL,
  `CardTypeName` varchar(32) DEFAULT NULL,
  `CardHolderName` varchar(240) DEFAULT NULL,
  `CompanyName` varchar(128) DEFAULT NULL,
  `CurrencyCode` varchar(8) DEFAULT NULL,
  `CurrentInstallment` int(11) DEFAULT NULL,
  `CustomerTrns` varchar(64) DEFAULT NULL,
  `Email` varchar(128) DEFAULT NULL,
  `FullName` varchar(128) DEFAULT NULL,
  `InsDate` datetime DEFAULT NULL,
  `MerchantId` varchar(64) DEFAULT NULL,
  `MerchantTrns` varchar(128) DEFAULT NULL,
  `OrderCode` varchar(64) DEFAULT NULL,
  `ParentId` varchar(64) DEFAULT NULL,
  `ResellerCompanyName` varchar(240) DEFAULT NULL,
  `ResellerId` varchar(240) DEFAULT NULL,
  `ResellerSourceAddress` varchar(240) DEFAULT NULL,
  `ResellerSourceCode` varchar(240) DEFAULT NULL,
  `ResellerSourceName` varchar(240) DEFAULT NULL,
  `SourceCode` varchar(64) DEFAULT NULL,
  `StatusId` varchar(8) DEFAULT NULL,
  `TargetPersonId` varchar(240) DEFAULT NULL,
  `TotalCommission` double DEFAULT NULL,
  `TotalFee` double DEFAULT NULL,
  `TotalInstallments` int(11) DEFAULT NULL,
  `TransactionId` varchar(128) DEFAULT NULL,
  `TransactionTypeId` int(11) DEFAULT NULL,
  `TransactionTypeName` varchar(240) DEFAULT NULL,
  `Moto` varchar(240) DEFAULT NULL,
  `Phone` varchar(240) DEFAULT NULL,
  `BankId` varchar(240) DEFAULT NULL,
  `Systemic` varchar(240) DEFAULT NULL,
  `Switching` varchar(240) DEFAULT NULL,
  `ChannelId` varchar(240) DEFAULT NULL,
  `TerminalId` varchar(64) DEFAULT NULL,
  `ProductId` varchar(240) DEFAULT NULL,
  `DualMessage` varchar(240) DEFAULT NULL,
  `CardToken` varchar(240) DEFAULT NULL,
  `TipAmount` double DEFAULT NULL,
  `SourceName` varchar(240) DEFAULT NULL,
  `Latitude` double DEFAULT NULL,
  `Longitude` double DEFAULT NULL,
  `CompanyTitle` varchar(240) DEFAULT NULL,
  `PanEntryMode` varchar(240) DEFAULT NULL,
  `ReferenceNumber` varchar(240) DEFAULT NULL,
  `ResponseCode` varchar(240) DEFAULT NULL,
  `OrderCulture` varchar(240) DEFAULT NULL,
  `IsManualRefund` varchar(240) DEFAULT NULL,
  `TargetWalletId` varchar(240) DEFAULT NULL,
  `LoyaltyTriggered` varchar(240) DEFAULT NULL,
  `CardCountryCode` varchar(240) DEFAULT NULL,
  `CardIssuingBank` varchar(240) DEFAULT NULL,
  `RedeemedAmount` double DEFAULT NULL,
  `ClearanceDate` datetime DEFAULT NULL,
  `BillId` varchar(240) DEFAULT NULL,
  `CardExpirationDate` datetime DEFAULT NULL,
  `RetrievalReferenceNumber` varchar(240) DEFAULT NULL,
  `ResponseEventId` varchar(240) DEFAULT NULL,
  `ElectronicCommerceIndicator` varchar(240) DEFAULT NULL,
  `CorrelationId` varchar(240) DEFAULT NULL,
  `Delay` varchar(240) DEFAULT NULL,
  `MessageId` varchar(240) DEFAULT NULL,
  `RecipientId` varchar(240) DEFAULT NULL,
  `MessageTypeId` varchar(240) DEFAULT NULL,
  `myjson` longtext,
  `CreatedBy` varchar(240) DEFAULT NULL,
  `AcquirerApproved` varchar(240) DEFAULT NULL,
  `AuthorizationId` varchar(240) DEFAULT NULL,
  `OrderChannelId` varchar(240) DEFAULT NULL,
  `OrderResellerId` varchar(240) DEFAULT NULL,
  `OrderSourceCode` varchar(240) DEFAULT NULL,
  `OrderResellerSourceCode` varchar(240) DEFAULT NULL,
  `PaymentChannelId` varchar(240) DEFAULT NULL,
  `PaymentInstallments` int(11) DEFAULT NULL,
  `PaymentRecurringSupport` varchar(240) DEFAULT NULL,
  `PersonId` varchar(240) DEFAULT NULL,
  `WalletId` varchar(240) DEFAULT NULL,
  `IsInternal` varchar(240) DEFAULT NULL,
  `Description` varchar(240) DEFAULT NULL,
  `ValueDate` datetime DEFAULT NULL,
  `BankAccountId` varchar(240) DEFAULT NULL,
  `SaleTransactionId` varchar(240) DEFAULT NULL,
  `WalletTransactionId` varchar(240) DEFAULT NULL,
  `InternalDescription` varchar(240) DEFAULT NULL,
  `TypeId` int(11) DEFAULT NULL,
  `SubTypeId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_viva_transaction`),
  KEY `add_date` (`add_date`),
  KEY `edit_date` (`edit_date`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `EventTypeId` (`EventTypeId`),
  KEY `Created` (`Created`),
  KEY `CardNumber` (`CardNumber`),
  KEY `Email` (`Email`),
  KEY `FullName` (`FullName`),
  KEY `OrderCode` (`OrderCode`),
  KEY `TransactionId` (`TransactionId`),
  KEY `TransactionTypeId` (`TransactionTypeId`),
  KEY `Amount` (`Amount`),
  KEY `TransactionTypeName` (`TransactionTypeName`),
  KEY `TerminalId` (`TerminalId`),
  KEY `CardTypeName` (`CardTypeName`),
  KEY `CardIssuingBank` (`CardIssuingBank`),
  KEY `Phone` (`Phone`),
  KEY `MerchantTrns` (`MerchantTrns`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `StatusId` (`StatusId`),
  KEY `Description` (`Description`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_viva_orders` (
  `id_viva_order` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `add_date` datetime NOT NULL,
  `edit_date` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `viber_msgs_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `mobile` varchar(240) DEFAULT NULL,
  `xeiristis_id` int(11) DEFAULT NULL,
  `order_code` varchar(240) DEFAULT NULL,
  `order_status` varchar(16) DEFAULT NULL,
  `postargs` mediumtext DEFAULT NULL,
  `response` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id_viva_order`),
  KEY `add_date` (`add_date`),
  KEY `edit_date` (`edit_date`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `viber_msgs_id` (`viber_msgs_id`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `order_code` (`order_code`),
  KEY `order_status` (`order_status`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=850";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (850,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_viva_transaction','Συναλλαγές Viva',850)");
}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_VIVA_URL_WWW') === false) {
  echo '_current/_config.php file not contains GKS_VIVA_URL_WWW<br>';die();}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_moves` (
  `id_assets_moves` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  `mydate` datetime NOT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `action_myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_assets_moves`),
  KEY `asset_id` (`asset_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `mydate` (`mydate`),
  KEY `user_id_add` (`user_id_add`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_service` (
  `id_assets_service` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `mydate_send` datetime DEFAULT NULL,
  `mydate_return` datetime DEFAULT NULL,
  `reason_id` int(11) NOT NULL DEFAULT '0',
  `asset_km` int(11) NOT NULL DEFAULT '0',
  `aitiolog` varchar(512) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `aitiolog2` varchar(512) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mixanikos_id` int(11) NOT NULL DEFAULT '0',
  `faut_agentid` int(11) NOT NULL DEFAULT '0',
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `ajia` double NOT NULL DEFAULT '0',
  `isconfirm` tinyint(4) NOT NULL DEFAULT '0',
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cashdesk_move_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_assets_service`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `asset_id` (`asset_id`),
  KEY `mydate_send` (`mydate_send`),
  KEY `mydate_return` (`mydate_return`),
  KEY `reason_id` (`reason_id`),
  KEY `mixanikos_id` (`mixanikos_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `ajia` (`ajia`),
  KEY `isconfirm` (`isconfirm`),
  KEY `cashdesk_move_id` (`cashdesk_move_id`),
  KEY `aitiolog` (`aitiolog`(190)),
  KEY `aitiolog2` (`aitiolog2`(190))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_service_reasons` (
  `id_assets_service_reasons` int(11) NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reasons_descr` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  assets_service_reason_sortorder int(11) NOT NULL DEFAULT 1000,
  assets_service_reason_disable tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_assets_service_reasons`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `reasons_descr` (`reasons_descr`(190)),
  KEY `assets_service_reason_sortorder` (`assets_service_reason_sortorder`),
  KEY `assets_service_reason_disable` (`assets_service_reason_disable`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_service_reasons_types` (
  `id_assets_service_reasons_types` int(11) NOT NULL AUTO_INCREMENT,
  `reasons_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_assets_service_reasons_types`),
  KEY `reasons_id` (`reasons_id`),
  KEY `type_id` (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_assets_service_reasons where id_assets_service_reasons=39";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_assets_service_reasons` (`id_assets_service_reasons`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`reasons_descr`,`assets_service_reason_sortorder`,assets_service_reason_disable) VALUES 
   (39,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αλλαγή λαδιών',50,0),
   (40,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Service Μικρό',60,0),
   (41,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Service Μεγάλο',70,0),
   (42,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αλλαγή Ελαστικών',80,0),
   (43,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ακινητοποίηση',90,0),
   (44,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ΚΤΕΟ',100,0),
   (45,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ανανέωση Ασφάλειας',110,0),
   (46,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Μεταβίβαση',120,0)");

  gks_run_sql("INSERT INTO `gks_assets_service_reasons_types` (`id_assets_service_reasons_types`,`reasons_id`,`type_id`) VALUES 
   (39,39,26),
   (40,40,26),
   (41,41,26),
   (42,42,26),
   (43,43,26),
   (44,44,26),
   (45,45,26),
   (46,46,26);");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_whi_mov` (
  `id_assets_whi_mov` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mydate` datetime DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `assets_whi_mov_status` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT '00draft',
  `whi_mov_sxolio` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id_assets_whi_mov`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mydate` (`mydate`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `assets_whi_mov_status` (`assets_whi_mov_status`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_assets_whi_mov_assets` (
  `id_assets_whi_mov_assets` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `assets_whi_mov_id` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `posotita_theori` tinyint(4) DEFAULT NULL,
  `posotita_found` tinyint(4) DEFAULT NULL,
  `posotita_sxolio` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id_assets_whi_mov_assets`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `assets_whi_mov_id` (`assets_whi_mov_id`),
  KEY `asset_id` (`asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=951";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (951,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets','Πάγια',951),
   (952,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets_type','Τύποι Παγίων',952),
   (953,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets_moves','Κινήσεις Παγίων',953),
   (961,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets_service','Service',961),
   (962,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets_service_reasons','Αιτίες Service Παγίου',962),
   (971,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια',0,'gks_assets_whi_mov','Απογραφές Παγίων',971)");
}


$sql="select * from gks_custom_table where id_custom_table=46";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
   (46,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια - Πάγια','gks_assets','id_asset','asset_id',0,'assets',5010),
   (48,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια - Service','gks_assets_service','id_assets_service','assets_service_id',0,'assets',5030),
   (49,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια - Απογραφές','gks_assets_whi_mov','id_assets_whi_mov','assets_whi_mov_id',0,'assets',5040)");

   //(47,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πάγια - Κινήσεις Παγίων','gks_assets_moves','id_assets_moves','assets_moves_id',0,'assets',5020),

}

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=44";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (44,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_assets','Πάγια',44,0,'admin-assets-item.php?id=%s'),
   (45,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_assets_service','Service Παγίου',45,0,'admin-assets-service-item.php?id=%s'),
   (46,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_assets_service_reasons','Αιτία Service Παγίου',46,0,'admin-assets-service-reasons-item.php?id=%s'),
   (47,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_assets_type','Τύπος Παγίου',47,0,'admin-assets-type-item.php?id=%s'),
   (48,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_assets_whi_mov','Απογραφή Παγίων',48,0,'admin-assets-whi-mov-item.php?id=%s')");
}


$sql="select asset_type_disabled from gks_assets_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets_type` 
  ADD COLUMN `asset_type_disabled` TINYINT NOT NULL DEFAULT 0,
  ADD INDEX asset_type_disabled (asset_type_disabled)");
}

$sql="select * from gks_settings where mykey='GKS_ASSETS_ENABLE'";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_settings` (`mykey`,`myvalue`) VALUES ('GKS_ASSETS_ENABLE','true');");
  
  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",951,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",952,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",953,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",961,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",962,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",971,1,1,1,1,1)");
    
  } 
    
   

}


gks_run_sql("ALTER TABLE `gks_assets_type` 
  MODIFY COLUMN `asset_type_descr` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  MODIFY COLUMN `asset_type_prefix` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


//gks_run_sql("ALTER TABLE `gks_viber_msgs` 
// MODIFY COLUMN `status_message` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `message_token` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `sender_id` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `receiver_id` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `sender_name` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `action_cmd` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `action_cmd_part1` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
// MODIFY COLUMN `other_viber_id` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
 

gks_run_sql("ALTER TABLE `gks_viber_msgs`

 drop index status_message,
 drop index message_token,
 drop index sender_id,
 drop index receiver_id,
 drop index sender_name,
 drop index action_cmd,
 drop index action_cmd_part1,
 drop index other_viber_id,

 MODIFY COLUMN `status_message` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `message_token` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `sender_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `receiver_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `sender_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `action_cmd` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `action_cmd_part1` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
 MODIFY COLUMN `other_viber_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,

ADD INDEX status_message(status_message(190)),
ADD INDEX message_token(message_token(190)),
ADD INDEX sender_id(sender_id(190)),
ADD INDEX receiver_id(receiver_id(190)),
ADD INDEX sender_name(sender_name(190)),
ADD INDEX action_cmd(action_cmd(190)),
ADD INDEX action_cmd_part1(action_cmd_part1(190)),
ADD INDEX other_viber_id(other_viber_id(190))");


/*
SELECT * FROM information_schema.`TABLES`
where table_name like 'gks%'and table_schema='test_easyfilesselection_com' and table_collation<>'utf8mb4_unicode_520_ci'
order by table_name;
*/

gks_run_sql("ALTER table `gks_assets_log` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_moves` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_oximata_km` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_photo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service_reasons` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service_reasons_types` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_type` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_calendar_dav_changes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_urlshort` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_urlshort_hit` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_users_dav_changes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_viber_cmds` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_viber_msgs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");



gks_run_sql("ALTER table `gks_assets_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_moves` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_oximata_km` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_photo` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service_reasons` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_service_reasons_types` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_assets_type` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_calendar_dav_changes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_urlshort` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_urlshort_hit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_users_dav_changes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_viber_cmds` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");
gks_run_sql("ALTER table `gks_viber_msgs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");





gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_flights` (
  `id_flights` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gkskey` varchar(190) DEFAULT NULL,
  `hex` varchar(32) DEFAULT NULL,
  `reg_number` varchar(32) DEFAULT NULL,
  `flag` varchar(32) DEFAULT NULL,
  `lat` double NOT NULL DEFAULT 0,
  `lng` double NOT NULL DEFAULT 0,
  `alt` int(11) NOT NULL DEFAULT 0,
  `dir` int(11) NOT NULL DEFAULT 0,
  `speed` int(11) NOT NULL DEFAULT 0,
  `v_speed` int(11) NOT NULL DEFAULT 0,
  `squawk` int(11) NOT NULL DEFAULT 0,
  `flight_number` varchar(32) DEFAULT NULL,
  `flight_icao` varchar(32) DEFAULT NULL,
  `flight_iata` varchar(32) DEFAULT NULL,
  `dep_icao` varchar(32) DEFAULT NULL,
  `dep_iata` varchar(32) DEFAULT NULL,
  `arr_icao` varchar(32) DEFAULT NULL,
  `arr_iata` varchar(32) DEFAULT NULL,
  `airline_icao` varchar(32) DEFAULT NULL,
  `airline_iata` varchar(32) DEFAULT NULL,
  `aircraft_icao` varchar(32) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `airline_id` int(11) NOT NULL DEFAULT 0,
  `airport_arr_id` int(11) NOT NULL DEFAULT 0,
  `airport_dep_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_flights`),
  KEY `gkskey` (`gkskey`),
  KEY `reg_number` (`reg_number`),
  KEY `flight_number` (`flight_number`),
  KEY `flight_icao` (`flight_icao`),
  KEY `flight_iata` (`flight_iata`),
  KEY `dep_icao` (`dep_icao`),
  KEY `dep_iata` (`dep_iata`),
  KEY `arr_icao` (`arr_icao`),
  KEY `arr_iata` (`arr_iata`),
  KEY `airline_icao` (`airline_icao`),
  KEY `airline_iata` (`airline_iata`),
  KEY `aircraft_icao` (`aircraft_icao`),
  KEY `airline_id` (`airline_id`),
  KEY `airport_arr_id` (`airport_arr_id`),
  KEY `airport_dep_id` (`airport_dep_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_flights_routes` (
  `id_flights_routes` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gkskey` varchar(190) DEFAULT NULL,
  `airline_icao` varchar(32) DEFAULT NULL,
  `airline_iata` varchar(32) DEFAULT NULL,
  `flight_number` varchar(32) DEFAULT NULL,
  `flight_icao` varchar(32) DEFAULT NULL,
  `flight_iata` varchar(32) DEFAULT NULL,
  `cs_airline_iata` varchar(32) DEFAULT NULL,
  `cs_flight_iata` varchar(32) DEFAULT NULL,
  `cs_flight_number` varchar(32) DEFAULT NULL,
  `dep_icao` varchar(32) DEFAULT NULL,
  `dep_iata` varchar(32) DEFAULT NULL,
  `dep_time` varchar(32) DEFAULT NULL,
  `dep_time_utc` varchar(32) DEFAULT NULL,
  `dep_terminals` varchar(32) DEFAULT NULL,
  `arr_icao` varchar(32) DEFAULT NULL,
  `arr_iata` varchar(32) DEFAULT NULL,
  `arr_time` varchar(32) DEFAULT NULL,
  `arr_time_utc` varchar(32) DEFAULT NULL,
  `arr_terminals` varchar(32) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `days` varchar(64) DEFAULT NULL,
  `aircraft_icao` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `airline_id` int(11) NOT NULL DEFAULT 0,
  `airport_arr_id` int(11) NOT NULL DEFAULT 0,
  `airport_dep_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_flights_routes`),
  KEY `gkskey` (`gkskey`),
  KEY `flight_number` (`flight_number`),
  KEY `flight_icao` (`flight_icao`),
  KEY `flight_iata` (`flight_iata`),
  KEY `cs_airline_iata` (`cs_airline_iata`),
  KEY `cs_flight_iata` (`cs_flight_iata`),
  KEY `cs_flight_number` (`cs_flight_number`),
  KEY `dep_icao` (`dep_icao`),
  KEY `dep_iata` (`dep_iata`),
  KEY `arr_icao` (`arr_icao`),
  KEY `arr_iata` (`arr_iata`),
  KEY `airline_icao` (`airline_icao`),
  KEY `airline_iata` (`airline_iata`),
  KEY `aircraft_icao` (`aircraft_icao`),
  KEY `airline_id` (`airline_id`),
  KEY `airport_arr_id` (`airport_arr_id`),
  KEY `airport_dep_id` (`airport_dep_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


