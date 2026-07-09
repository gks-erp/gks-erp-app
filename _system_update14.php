<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



    
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_notification_userperm` (
  `id_notification_userperm` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `notification_type_id` int(11) NOT NULL DEFAULT '0',
  `from_admin` tinyint(4) NOT NULL DEFAULT '0',
  `from_user` tinyint(4) NOT NULL DEFAULT '0',
  `to_email` TINYINT NOT NULL DEFAULT '0',
  `to_viber` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_notification_userperm`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `user_id` (`user_id`),
  KEY `notification_type_id` (`notification_type_id`),
  KEY `from_admin` (`from_admin`),
  KEY `from_user` (`from_user`),
  KEY `to_email` (`to_email`),
  KEY `to_viber` (`to_viber`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=265";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (265,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_sms','SMS',265);");
}

gks_run_sql("update gks_permission_object set object_name='Viber' where id_permission_object=270 limit 1");

$sql="select model from gks_viber_msgs limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_viber_msgs`
  ADD COLUMN `model` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `model_id` int(11) NOT NULL DEFAULT '0',
  ADD INDEX `model`(`model`),
  ADD INDEX `model_id`(`model_id`);");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_user_carddav` (
  `ID` int(10) unsigned NOT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `caldav_synctoken` int(10) unsigned NOT NULL DEFAULT '1',
  `carddav_synctoken` int(10) unsigned NOT NULL DEFAULT '1',
  `carddata` mediumtext COLLATE utf8mb4_unicode_520_ci,
  `uri` varbinary(200) DEFAULT NULL,
  `etag` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");    

$sql="select ID from gks_user_carddav limit 1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_user_carddav` (
  ID,mydate_edit,myip,
  caldav_synctoken,carddav_synctoken,carddata,
  uri,etag,size,uid)
  select ID,mydate_edit,myip,
  caldav_synctoken,carddav_synctoken,carddata,
  uri,etag,size,uid
  from ".GKS_WP_TABLE_PREFIX."users");
}

  
//echo 'pppp111';

$sql="select caldav_synctoken from ".GKS_WP_TABLE_PREFIX."users limit 1";
$result = $db_link->query($sql);
if ($result) { //must return OK
  gks_run_sql("ALTER TABLE ".GKS_WP_TABLE_PREFIX."users
  DROP COLUMN caldav_synctoken,
  DROP COLUMN carddav_synctoken,
  DROP COLUMN carddata,
  DROP COLUMN uri,
  DROP COLUMN etag,
  DROP COLUMN size,
  DROP COLUMN uid;");
}

//echo 'pppp222';

$sql="select * from gks_crm_channel_sale where id_crm_channel_sale=20";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_channel_sale` (`id_crm_channel_sale`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_channel_sale_descr`,
  `crm_channel_sale_sortorder`,`crm_channel_sale_disabled`,`crm_channel_has_text`,`crm_channel_has_contact`,
  `crm_channel_has_contact_filter`,`crm_channel_has_campain`,`crm_channel_has_url`) VALUES 
 (20,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Πρακτορείο',20,0,1,1,'promitheutis=1',0,0),
 (21,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Booking',21,0,1,0,'',0,0),
 (22,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Airbnb',22,0,1,0,'',0,0);");
}

$sql="select hotel_plan_price_avg from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel`
  ADD COLUMN `hotel_plan_price_avg` TINYINT NOT NULL DEFAULT '1';");
}    

gks_run_sql("update gks_lang set id_lang='en-US' where id_lang='en-GB'");
gks_run_sql("update ".GKS_WP_TABLE_PREFIX."users set gks_lang='en-US' where gks_lang='en-GB'");
gks_run_sql("update gks_hotel_reservation set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_hotel_reservation set other_lang='en-US' where other_lang='en-GB'");
gks_run_sql("update gks_hotel_reservation_room set ruser_lang='en-US' where ruser_lang='en-GB'");
gks_run_sql("update gks_acc_inv set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_acc_inv set other_lang='en-US' where other_lang='en-GB'");
gks_run_sql("update gks_orders set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_orders set other_lang='en-US' where other_lang='en-GB'");
gks_run_sql("update gks_whi_mov set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_whi_mov set other_lang='en-US' where other_lang='en-GB'");
gks_run_sql("update gks_crm_tasks set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_crm_leads set user_lang='en-US' where user_lang='en-GB'");
gks_run_sql("update gks_print_forms set gks_lang='en-US' where gks_lang='en-GB'");
gks_run_sql("update gks_eshops set order_meta_user_lang='def:en-US' where order_meta_user_lang='def:en-GB'");




$sql="select crm_channel_code from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`           ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_acc_pay`           ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_crm_leads`         ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_crm_tasks`         ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_hotel_reservation` ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_orders`            ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_urlshort`          ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_urlshort_hit`      ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
  gks_run_sql("ALTER TABLE `gks_whi_mov`           ADD COLUMN `crm_channel_code` VARCHAR(190) DEFAULT NULL AFTER `crm_channel_text`, ADD INDEX `crm_channel_code`(`crm_channel_code`);");
}

$sql="select crm_channel_has_code from gks_crm_channel_sale limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_crm_channel_sale` 
  ADD COLUMN `crm_channel_has_code` TINYINT NOT NULL DEFAULT 0 AFTER `crm_channel_has_url`;");
  gks_run_sql("update gks_crm_channel_sale set crm_channel_has_code=1 where id_crm_channel_sale in (21,22)");
  
}

$sql="select hotel_id_booking from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel`
  ADD COLUMN `hotel_id_booking` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `hotel_id_airbnb` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
}
    

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_room_type_channel_name` (
  `id_hotel_room_type_channel_name` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_room_type_id` int(11) NOT NULL DEFAULT '0',
  `channel` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `room_type_descr_channel_name` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id_hotel_room_type_channel_name`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `hotel_room_type_id` (`hotel_room_type_id`),
  KEY `room_type_descr_channel_name` (`room_type_descr_channel_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    


    
    
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_perifereies` (
  `id_perifereia` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `perifereia_descr` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `perifereia_descr_en_US` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `woo_id` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id_perifereia`),
  KEY `country_id` (`country_id`),
  KEY `perifereia_descr` (`perifereia_descr`),
  KEY `perifereia_descr_en_US` (`perifereia_descr_en_US`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `woo_id` (`woo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_perifereies";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
      
  gks_run_sql("INSERT INTO `gks_perifereies` (`id_perifereia`,`country_id`,`perifereia_descr`,`perifereia_descr_en_US`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`woo_id`) VALUES 
  (3,91,'Δυτική Ελλάδα',NULL,'2022-05-03 13:13:08','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','G'),
  (2,91,'Κρήτη',NULL,'2022-05-03 13:13:08','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','M'),
  (1,91,'Αττική',NULL,'2022-05-03 13:13:08','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','I'),
  (4,91,'Πελοπόννησος',NULL,'2022-05-03 13:13:07','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','J'),
  (5,91,'Ήπειρος',NULL,'2022-05-03 13:13:07','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','D'),
  (6,91,'Στερεά Ελλάδα',NULL,'2022-05-03 13:13:06','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','H'),
  (7,91,'Δυτική Μακεδονία',NULL,'2022-05-03 13:13:06','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','C'),
  (8,91,'Ανατολική Μακεδονία και Θράκη',NULL,'2022-05-03 13:13:06','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','A'),
  (9,91,'Νότιο Αιγαίο',NULL,'2022-05-03 13:13:05','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','L'),
  (10,91,'Ιόνια νησιά',NULL,'2022-05-03 13:13:05','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','F'),
  (11,91,'Κεντρική Μακεδονία',NULL,'2022-05-03 13:13:05','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','B'),
  (12,91,'Θεσσαλία',NULL,'2022-05-03 13:13:04','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','E'),
  (13,91,'Βόρειο Αιγαίο',NULL,'2022-05-03 13:13:03','2000-01-01 00:00:00','2000-01-01 00:00:00',2,2,'127.0.0.1','K');");
}
 

$sql="select * from gks_nomoi where id_nomos=59";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_nomoi` (`id_nomos`,`country_id`,`nomos_ISO_3166_2`,`nomos_descr`,`nomos_descr_en_US`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`) VALUES 
  (59,91,NULL,'Άγιο Όρος','Mount Athos','2022-05-03 13:50:16','2020-01-01 00:00:00','2020-01-01 00:00:00',1,1,'127.0.0.1')");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_tk` (
  `id_tk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `odos` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `arithmos` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `poli` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `perioxi` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `tk` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `nomos_id` int(11) NOT NULL DEFAULT '0',
  `perifereia_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tk`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `odos` (`odos`),
  KEY `poli` (`poli`),
  KEY `perioxi` (`perioxi`),
  KEY `tk` (`tk`),
  KEY `nomos_id` (`nomos_id`),
  KEY `perifereia_id` (`perifereia_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");






gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_pos` (
  `id_pos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pos_guid` varchar(63) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `pos_name` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `pos_descr` text COLLATE utf8mb4_unicode_520_ci,
  `pos_disable` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pos_company_id` int(11) NOT NULL DEFAULT '0',
  `pos_company_sub_id` int(11) NOT NULL DEFAULT '0',
  `pos_journal_id` int(11) NOT NULL DEFAULT '0',
  `pos_seira_id` int(11) NOT NULL DEFAULT '0',
  `def_aade_skopos_diakinisis_id` int(11) NOT NULL DEFAULT '0',
  `def_user_id` int(11) NOT NULL DEFAULT '0',
  `def_user_lang` varchar(8) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `def_fiscal_position_id` int(11) NOT NULL DEFAULT '0',
  `def_pricelist_id` int(11) NOT NULL DEFAULT '0',
  `def_tropos_apostolis` int(11) NOT NULL DEFAULT '0',
  `def_tropos_pliromis` int(11) NOT NULL DEFAULT '0',
  `def_delivery_id_8` int(11) NOT NULL DEFAULT '0',
  `def_affect_balance` tinyint(4) NOT NULL DEFAULT '0',
  `def_affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT '1',
  `def_affect_balance_all_poso_type` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'pliroteo',
  `def_affect_balance_pros` tinyint(4) NOT NULL DEFAULT '0',
  `def_assigned_id` int(11) NOT NULL DEFAULT '0',
  `def_crm_channel_id` int(11) NOT NULL DEFAULT '0',
  `def_crm_channel_contact_id` int(11) NOT NULL DEFAULT '0',
  `def_crm_channel_campain_id` int(11) NOT NULL DEFAULT '0',
  `def_crm_channel_url` text COLLATE utf8mb4_unicode_520_ci,
  `def_crm_channel_text` text COLLATE utf8mb4_unicode_520_ci,
  `def_crm_channel_code` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `pos_warehouses_id_from` int(11) NOT NULL DEFAULT '0',
  `pos_warehouses_id_to` int(11) NOT NULL DEFAULT '0',
  `def_products` longtext COLLATE utf8mb4_unicode_520_ci,
  `pos_print_file_type` varchar(16) COLLATE utf8mb4_unicode_520_ci DEFAULT 'pdf',
  `pos_print_grayscale` tinyint(4) NOT NULL DEFAULT '0',
  `pos_print_landscape` tinyint(4) NOT NULL DEFAULT '0',
  `pos_print_zoom` int(11) NOT NULL DEFAULT '0',
  `pos_print_form_id` int(11) NOT NULL DEFAULT '0',
  `pos_aade_mydata_live` tinyint(4) NOT NULL DEFAULT '0',
  `pos_max_ammount` double NOT NULL DEFAULT '100',
  PRIMARY KEY (`id_pos`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `pos_guid` (`pos_guid`),
  KEY `pos_seira_id` (`pos_seira_id`),
  KEY `def_user_id` (`def_user_id`),
  KEY `def_fiscal_position_id` (`def_fiscal_position_id`),
  KEY `def_pricelist_id` (`def_pricelist_id`),
  KEY `def_tropos_apostolis` (`def_tropos_apostolis`),
  KEY `def_tropos_pliromis` (`def_tropos_pliromis`),
  KEY `def_affect_balance` (`def_affect_balance`),
  KEY `def_assigned_id` (`def_assigned_id`),
  KEY `def_crm_channel_id` (`def_crm_channel_id`) USING BTREE,
  KEY `def_crm_channel_contact_id` (`def_crm_channel_contact_id`) USING BTREE,
  KEY `def_crm_channel_campain_id` (`def_crm_channel_campain_id`),
  KEY `def_crm_channel_text` (`def_crm_channel_text`(240)),
  KEY `def_crm_channel_code` (`def_crm_channel_code`),
  KEY `pos_name` (`pos_name`),
  KEY `pos_warehouses_id_from` (`pos_warehouses_id_from`) USING BTREE,
  KEY `pos_warehouses_id_to` (`pos_warehouses_id_to`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=683";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (683,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_pos','POS Διαχείριση',683),
   (684,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_pos_run','POS Λειτουργία',684);");
}


$sql="select pos_step from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` ADD COLUMN `pos_step` VARCHAR(16) DEFAULT NULL;");
  
}

$sql="select * from gks_custom_table where id_custom_table=37";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
   (37,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική - POS','gks_pos','id_pos','pos_id',0,'acc',4025);");
}

$sql="select other_myobj from gks_calendar_other_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE  `gks_calendar_other_users` 
  ADD COLUMN `other_myobj` VARCHAR(45) NOT NULL DEFAULT 'cal',
  ADD INDEX `other_myobj`(`other_myobj`);");
  
}


$sql="select uid from gks_crm_tasks limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("ALTER TABLE `gks_crm_tasks`
  ADD COLUMN `calendardata` text COLLATE utf8mb4_unicode_520_ci,
  ADD COLUMN `uri` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `etag` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `size` int(11) unsigned NOT NULL DEFAULT '0',
  ADD COLUMN `componenttype` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `uid` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD INDEX `uid`(`uid`)");
}


$sql="select * from gks_calendar_dav_calendars limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("CREATE TABLE `gks_calendar_dav_calendars` (
    `id_dav_calendar` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` VARCHAR(45) NOT NULL,
    `other_myobj` VARCHAR(45) NOT NULL,
    `caldav_synctoken` INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY (`id_dav_calendar`),
    KEY `user_id` (`user_id`) USING BTREE,
    KEY `other_myobj` (`other_myobj`) USING BTREE,
    KEY `caldav_synctoken` (`caldav_synctoken`) USING BTREE
  )
  ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
  
  gks_run_sql("insert into gks_calendar_dav_calendars (user_id,other_myobj,caldav_synctoken)
  select ID,'cal' as other_myobj, caldav_synctoken
  from gks_user_carddav
  where caldav_synctoken>1");
  
  gks_run_sql("alter table gks_user_carddav drop column caldav_synctoken");
  
  gks_run_sql("UPDATE gks_calendar_dav_changes
  LEFT JOIN gks_calendar_dav_calendars ON gks_calendar_dav_changes.calendarid = gks_calendar_dav_calendars.user_id
  SET gks_calendar_dav_changes.calendarid = id_dav_calendar
  WHERE gks_calendar_dav_calendars.user_id Is Not Null");


  $sql="select id_crm_task from gks_crm_tasks order by id_crm_task";
  $result = $db_link->query($sql);  
  if (!$result) die('sql error');
  $ids=array();
  while ($row = $result->fetch_assoc()) {
    $ids[]=$row['id_crm_task'];
  }
  //print '<pre>';print_r($ids);
  foreach ($ids as $id) {
    gks_calendar_event_update_dav_task($id,false);
  }

  
}


gks_run_sql("ALTER TABLE `gks_pos` MODIFY COLUMN `pos_print_zoom` DOUBLE NOT NULL DEFAULT 1;");

$sql="select def_tropos_pliromis_array from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("ALTER TABLE `gks_pos` ADD COLUMN `def_tropos_pliromis_array` TEXT DEFAULT NULL AFTER `def_tropos_pliromis`;");
}

gks_run_sql("update gks_banks set bank_descr='ALPHA BANK' where id_bank=2;");
 

$sql="select pos_multi_copies from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("ALTER TABLE `gks_pos` ADD COLUMN `pos_multi_copies` int(11) unsigned NOT NULL DEFAULT '0';");
}
