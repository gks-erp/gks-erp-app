<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

$sql="select * from gks_permission_object where id_permission_object=733";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (733,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Άλλα',0,'gks__filesexplore','Εξερεύνηση αρχείων',90009)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",733,1,1,1,1,1)");
  }

}


$sql="select * from gks_eshop_fpa_base where id_fpa_base=1005";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_eshop_fpa_base` (`id_fpa_base`,`fpa_base_descr`,`fpa_base_sortorder`,`fpa_base_disable`) VALUES 
   (1005,'Υπερ-υπερμειωμένος',5,0)");

  gks_run_sql("update gks_eshop_fpa_base set fpa_base_sortorder=2 where id_fpa_base=1001");
  gks_run_sql("update gks_eshop_fpa_base set fpa_base_sortorder=3 where id_fpa_base=1002");
  gks_run_sql("update gks_eshop_fpa_base set fpa_base_sortorder=4 where id_fpa_base=1003");
  gks_run_sql("update gks_eshop_fpa_base set fpa_base_sortorder=6 where id_fpa_base=1004");
  
}
gks_run_sql("delete from gks_eshop_fpa_base where id_fpa_base=1006");

$sql="select * from gks_eshop_fpa where id_fpa=3011";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO gks_eshop_fpa 
  (id_fpa,fpa_descr,fpa_descr_short,fpa_descr_print,fpa_pososto,fpa_sortorder,fpa_disable,aade_katigoria_fpa_id) 
  VALUES 
  (3011,'ΦΠΑ συντελεστής 19%','ΓΦ19%','19%',0.19,3011,0,0),
  (3013,'ΦΠΑ συντελεστής 5%', 'ΓΦ5%', '5%', 0.05,3013,0,0),
  (3014,'ΦΠΑ συντελεστής 3%', 'ΓΦ3%', '3%', 0.03,3014,0,0)");


  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3001 where id_fpa=3001");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3010 where id_fpa=3011");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3020 where id_fpa=3004");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3030 where id_fpa=3002");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3040 where id_fpa=3005");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3050 where id_fpa=3003");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3060 where id_fpa=3013");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3070 where id_fpa=3006");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3080 where id_fpa=3014");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3301 where id_fpa=3010");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3302 where id_fpa=3009");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3400 where id_fpa=3007");
  gks_run_sql("update gks_eshop_fpa set fpa_sortorder=3500 where id_fpa=3008");

}
//company_fpa_type_id
//https://www.mof.gov.cy/mof/tax/taxdep.nsf/All/A0559F53CE375AC7C2258251002BA874?OpenDocument
//19% 9% 5% 3%

$sql="select * from gks_aade_katigoria_fpa where id_aade_katigoria_fpa=3011";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_fpa` DROP INDEX `aade_katigoria_fpa_code`;");
  
  gks_run_sql("INSERT INTO gks_aade_katigoria_fpa 
  (id_aade_katigoria_fpa,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_fpa_code,aade_katigoria_fpa_descr,aade_katigoria_fpa_pososto,
  sortorder,fpa_base_id,direct_fpa_id)
  VALUES 
  (3011,'2024-01-01','2024-01-01',2,2,'127.0.0.1',0,'ΦΠΑ συντελεστής 19%',0.19,20,1001,3011),
  (3013,'2024-01-01','2024-01-01',2,2,'127.0.0.1',0,'ΦΠΑ συντελεστής 5%', 0.05,70,1003,3013),
  (3014,'2024-01-01','2024-01-01',2,2,'127.0.0.1',0,'ΦΠΑ συντελεστής 3%', 0.03,90,1005,3014)");


  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=10 where id_aade_katigoria_fpa=1");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=40 where id_aade_katigoria_fpa=2");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=60 where id_aade_katigoria_fpa=3");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=30 where id_aade_katigoria_fpa=4");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=50 where id_aade_katigoria_fpa=5");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=80 where id_aade_katigoria_fpa=6");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=310 where id_aade_katigoria_fpa=7");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=410 where id_aade_katigoria_fpa=8");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=220 where id_aade_katigoria_fpa=9");
  gks_run_sql("update gks_aade_katigoria_fpa set sortorder=210 where id_aade_katigoria_fpa=10");
  
  
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_basefpa` (
  `id_company_basefpa` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `fpa_base_id` int(11) NOT NULL DEFAULT 0,
  `fpa_id` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_company_basefpa`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `fpa_base_id` (`fpa_base_id`) USING BTREE,
  KEY `fpa_id` (`fpa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_subs_basefpa` (
  `id_company_sub_basefpa` int(11) NOT NULL AUTO_INCREMENT,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `fpa_base_id` int(11) NOT NULL DEFAULT 0,
  `fpa_id` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_company_sub_basefpa`) USING BTREE,
  KEY `company_id` (`company_sub_id`) USING BTREE,
  KEY `fpa_base_id` (`fpa_base_id`) USING BTREE,
  KEY `fpa_id` (`fpa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_fpa` (
  `id_company_fpa` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `fpa_base_id` int(11) NOT NULL DEFAULT 0,
  `fpa_id` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_company_fpa`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `fiscal_position_id` (`fiscal_position_id`) USING BTREE,
  KEY `fpa_base_id` (`fpa_base_id`) USING BTREE,
  KEY `fpa_id` (`fpa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_subs_fpa` (
  `id_company_sub_fpa` int(11) NOT NULL AUTO_INCREMENT,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `fpa_base_id` int(11) NOT NULL DEFAULT 0,
  `fpa_id` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_company_sub_fpa`) USING BTREE,
  KEY `company_id` (`company_sub_id`) USING BTREE,
  KEY `fiscal_position_id` (`fiscal_position_id`) USING BTREE,
  KEY `fpa_base_id` (`fpa_base_id`) USING BTREE,
  KEY `fpa_id` (`fpa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select can_select from gks_eshop_fpa limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("alter table gks_eshop_fpa
	add column can_select tinyint(4) NOT NULL DEFAULT 0,
	add index can_select (can_select)");
	
	gks_run_sql("update gks_eshop_fpa set can_select=1 
	where id_fpa>=3001 and id_fpa<=3014 and id_fpa not in(3008,3009,3010)");
	
	
	gks_run_sql("drop table gks_eshop_fpa_base_company_fpa_type");
	gks_run_sql("drop table gks_eshop_fiscal_fpa");


}

$sql="select * from gks_company_fpa_type limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
  gks_run_sql("drop table gks_company_fpa_type");
  gks_run_sql("ALTER TABLE gks_company DROP COLUMN company_fpa_type_id");
  gks_run_sql("ALTER TABLE gks_company_subs DROP COLUMN company_sub_fpa_type_id");
}

$sql="select tax_class_yperypermeiomenos from gks_eshops limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshops` 
  ADD COLUMN `tax_class_yperypermeiomenos` VARCHAR(64) DEFAULT NULL AFTER `tax_class_ypermeiomenos`;");
}









gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_correlated_invoices=1 where id_acc_eidos_parastatikou in (903,913)");

gks_run_sql("update gks_permission_object set sortorder=3010 where id_permission_object=150");


$sql="select * from gks_permission_object where id_permission_object=151";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (151,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_banks','Τράπεζες',3007)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",151,1,1,1,1,1)");
  }

}



$sql="select mydate_add from gks_banks limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_banks` 
  ADD COLUMN `mydate_add` datetime DEFAULT NULL,
  ADD COLUMN `mydate_edit` datetime DEFAULT NULL,
  ADD COLUMN `user_id_add` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `user_id_edit` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `myip` varchar(48) DEFAULT NULL,
  ADD COLUMN `bank_disable` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `bank_sortorder` int(11) NOT NULL DEFAULT 1000,
  add index `mydate_edit` (`mydate_edit`),
  add index `user_id_edit` (`user_id_edit`),
  add index `bank_disable` (`bank_disable`),
  add index `bank_sortorder` (`bank_sortorder`);");
  
  gks_run_sql("ALTER TABLE `gks_banks` 
  MODIFY COLUMN `bank_descr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
  
  gks_run_sql("ALTER TABLE `gks_banks` AUTO_INCREMENT = 10001;");


}

$sql="select * from gks_banks_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("CREATE TABLE `gks_banks_lang` (
    `id_bank_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `bank_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `bank_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_bank_lang`),
    UNIQUE INDEX `myunique`(`bank_id`, `lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `bank_id` (`bank_id`),
    KEY `lang_code` (`lang_code`),
    KEY `bank_descr` (`bank_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


}



$sql="select mydate_add from gks_bank_accounts limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_bank_accounts` 
  CHANGE COLUMN `date_add` `mydate_add` DATETIME DEFAULT NULL,
  CHANGE COLUMN `date_edit` `mydate_edit` DATETIME DEFAULT NULL,
  DROP INDEX `date_add`,
  ADD INDEX `mydate_add` USING BTREE(`mydate_add`),
  DROP INDEX `date_edit`,
  ADD INDEX `mydate_edit` USING BTREE(`mydate_edit`),
  ADD COLUMN `user_id_add` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `user_id_edit` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `myip` varchar(48) DEFAULT NULL,
  add index `user_id_edit` (`user_id_edit`),
  MODIFY COLUMN `bank_id` INT(11) NOT NULL DEFAULT 0,
  ADD COLUMN `bank_account_disable`  tinyint(4) NOT NULL DEFAULT 0,
  add index `bank_account_disable` (`bank_account_disable`)");
}

$sql="select * from gks_custom_table where id_custom_table=60";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`,obj_url) VALUES 
   (60,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - Τραπεζικοί λογαριασμοί','gks_bank_accounts','id_bank_account','bank_account_id',0,'base',85,'admin-bank_accounts.php')");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_bank_accounts_photo` (
  `id_bank_account_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(255) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_bank_account_photo`) USING BTREE,
  KEY `bank_account_id` (`bank_account_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=55";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (55,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_bank_accounts','Τραπεζικός λογαριασμός',56,0,'admin-bank_accounts-item.php?id=%s')");
}

$sql="select company_sub_related_user_id from gks_company_subs limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company_subs` 
  add COLUMN `company_sub_related_user_id` INT(11) NOT NULL DEFAULT 0,
  add index `company_sub_related_user_id` (`company_sub_related_user_id`)");
}
  

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_product_lots_photo` (
  `id_lot_product_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lot_product_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(255) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_lot_product_photo`) USING BTREE,
  KEY `lot_product_id` (`lot_product_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=56";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (56,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_eshop_product_lots','Παρτίδα-Serial Number',57,0,'admin-products-lots-item.php?id=%s')");
}



$sql="select aade_disable from gks_aade_skopos_diakinisis limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_skopos_diakinisis` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}

$sql="select aade_disable from gks_acc_eidi_parastatikon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_skopos_diakinisis_lang` (
  `id_aade_skopos_diakinisis_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `aade_skopos_diakinisis_id` int(11) NOT NULL DEFAULT 0,
  `lang_code` varchar(32) DEFAULT NULL,
  `aade_skopos_diakinisis_descr` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_aade_skopos_diakinisis_lang`),
  UNIQUE KEY `myunique` (`aade_skopos_diakinisis_id`,`lang_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `aade_skopos_diakinisis_id` (`aade_skopos_diakinisis_id`),
  KEY `lang_code` (`lang_code`),
  KEY `aade_skopos_diakinisis_descr` (`aade_skopos_diakinisis_descr`(250))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select * from gks_aade_katigoria_fpa_ejeresi_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_fpa_ejeresi` DROP INDEX `aade_katigoria_fpa_ejeresi_code`;");

  gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_katigoria_fpa_ejeresi_lang` (
    `id_aade_katigoria_fpa_ejeresi_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `aade_katigoria_fpa_ejeresi_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `aade_katigoria_fpa_ejeresi_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_aade_katigoria_fpa_ejeresi_lang`),
    UNIQUE KEY `myunique` (`aade_katigoria_fpa_ejeresi_id`,`lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `aade_katigoria_fpa_ejeresi_id` (`aade_katigoria_fpa_ejeresi_id`),
    KEY `lang_code` (`lang_code`),
    KEY `aade_katigoria_fpa_ejeresi_descr` (`aade_katigoria_fpa_ejeresi_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
}


$sql="select * from gks_aade_katigoria_parakratoumemenon_foron_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_parakratoumemenon_foron` DROP INDEX `aade_katigoria_parakratoumemenon_foron_code`;");

  gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_katigoria_parakratoumemenon_foron_lang` (
    `id_aade_katigoria_parakratoumemenon_foron_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `aade_katigoria_parakratoumemenon_foron_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `aade_katigoria_parakratoumemenon_foron_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_aade_katigoria_parakratoumemenon_foron_lang`),
    UNIQUE KEY `myunique` (`aade_katigoria_parakratoumemenon_foron_id`,`lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `aade_katigoria_parakratoumemenon_foron_id` (`aade_katigoria_parakratoumemenon_foron_id`),
    KEY `lang_code` (`lang_code`),
    KEY `aade_katigoria_parakratoumemenon_foron_descr` (`aade_katigoria_parakratoumemenon_foron_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
}

$sql="select * from gks_aade_katigoria_loipon_foron_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_loipon_foron` DROP INDEX `aade_katigoria_loipon_foron_code`;");

  gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_katigoria_loipon_foron_lang` (
    `id_aade_katigoria_loipon_foron_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `aade_katigoria_loipon_foron_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `aade_katigoria_loipon_foron_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_aade_katigoria_loipon_foron_lang`),
    UNIQUE KEY `myunique` (`aade_katigoria_loipon_foron_id`,`lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `aade_katigoria_loipon_foron_id` (`aade_katigoria_loipon_foron_id`),
    KEY `lang_code` (`lang_code`),
    KEY `aade_katigoria_loipon_foron_descr` (`aade_katigoria_loipon_foron_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
}

$sql="select * from gks_aade_katigoria_xartosimou_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_xartosimou` DROP INDEX `aade_katigoria_xartosimou_code`;");

  gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_katigoria_xartosimou_lang` (
    `id_aade_katigoria_xartosimou_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `aade_katigoria_xartosimou_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `aade_katigoria_xartosimou_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_aade_katigoria_xartosimou_lang`),
    UNIQUE KEY `myunique` (`aade_katigoria_xartosimou_id`,`lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `aade_katigoria_xartosimou_id` (`aade_katigoria_xartosimou_id`),
    KEY `lang_code` (`lang_code`),
    KEY `aade_katigoria_xartosimou_descr` (`aade_katigoria_xartosimou_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
}

$sql="select * from gks_aade_katigoria_telon_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_telon` DROP INDEX `aade_katigoria_telon_code`;");

  gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_katigoria_telon_lang` (
    `id_aade_katigoria_telon_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `mydate_add` datetime DEFAULT NULL,
    `mydate_edit` datetime DEFAULT NULL,
    `user_id_add` int(11) NOT NULL DEFAULT 0,
    `user_id_edit` int(11) NOT NULL DEFAULT 0,
    `myip` varchar(48) DEFAULT NULL,
    `aade_katigoria_telon_id` int(11) NOT NULL DEFAULT 0,
    `lang_code` varchar(32) DEFAULT NULL,
    `aade_katigoria_telon_descr` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_aade_katigoria_telon_lang`),
    UNIQUE KEY `myunique` (`aade_katigoria_telon_id`,`lang_code`),
    KEY `mydate_edit` (`mydate_edit`),
    KEY `user_id_edit` (`user_id_edit`),
    KEY `aade_katigoria_telon_id` (`aade_katigoria_telon_id`),
    KEY `lang_code` (`lang_code`),
    KEY `aade_katigoria_telon_descr` (`aade_katigoria_telon_descr`(250))
  ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
}

$sql="select warehouse_topos_fortosis from gks_warehouses limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_warehouses` 
  ADD COLUMN `warehouse_topos_fortosis` VARCHAR(255) AFTER `warehouse_name`;");

  gks_run_sql("ALTER TABLE `gks_warehouses_lang` 
  ADD COLUMN `warehouse_topos_fortosis` VARCHAR(255) AFTER `warehouse_name`;");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_users_filters_expand` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `url` varchar(200) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`url`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_GURU_LOG') !== false) {
  $read_file=str_replace("define('GKS_GURU_LOG',true);", '', $read_file);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file);
}




$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_ERP_HASHMD5KEY') === false) {
  echo '_current/_config.php file not contains GKS_ERP_HASHMD5KEY<br>';die();}



if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/plugins/prolab/functions.php')==false) {
  gks_run_sql("delete from gks_notification_type where id_notification_type in (3010,3020,3040)");
  gks_run_sql("delete from gks_permission_object where table_name in ('gks_orders_pivot')");
  gks_run_sql("delete from gks_permission_user where permission_object_id in (560)");
}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_ORDERS_PROLAB_TYPE') !== false) {
  $read_file=str_replace("define('GKS_ORDERS_PROLAB_TYPE',false);", '', $read_file);
  $read_file=str_replace("define('GKS_ORDERS_PROLAB_TYPE',true);", '', $read_file);
  $read_file=str_replace("and prolab", '', $read_file);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file);
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_users_messages` (
  `id_users_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userfor_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `userfor_message` text DEFAULT NULL,
  `email_id` int(11) NOT NULL DEFAULT 0,
  `sms_id` int(11) NOT NULL DEFAULT 0,
  `connect_id` int(11) NOT NULL DEFAULT 0,
  `woo_eshop_id` int(11) NOT NULL DEFAULT 0,
  `woo_comment_id` int(11) NOT NULL DEFAULT 0,
  `woo_author` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_users_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `userfor_id` (`userfor_id`),
  KEY `user_id` (`user_id`),
  KEY `userfor_message` (`userfor_message`(250)),
  KEY `email_id` (`email_id`),
  KEY `connect_id` (`connect_id`),
  KEY `woo_eshop_id` (`woo_eshop_id`),
  KEY `woo_comment_id` (`woo_comment_id`),
  KEY `sms_id` (`sms_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");



$sql="select * from gks_email_template_object where id_email_template_object=10";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_email_template_object` (`id_email_template_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`object_name`,`object_descr`) VALUES 
   (10,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','wp_users','Επαφές')");
}

$sql="select attachments from gks_email_template limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_email_template` 
  ADD COLUMN `attachments` text AFTER `other_fields`;");
}

$sql="select * from gks_email_template limit 1";
$result = $db_link->query($sql);
if ($result->num_rows==0) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (1,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:02','default','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n      \n     \n     \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        \n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n \n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του<br/>\r\n[[GKS_SITE_HUMAN_NAME]]',0,1,'el-GR',0,NULL,1000001,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (2,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:02','default.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]--> \n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n     \n               \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr>   \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Dear customer,<br/>\r\n<br/>\r\nSincerely, the [[GKS_SITE_HUMAN_NAME]] team',0,2,'en-US',0,NULL,1000001,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (3,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:01','empty','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      \n      <p>[[message]]</p>      \n      \n      <p>&nbsp;</p>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n  </tr>\n</table>\n<p>&nbsp;</p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του<br/>\r\n[[GKS_SITE_HUMAN_NAME]]',0,3,'el-GR',0,NULL,1000003,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (4,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:00','empty.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      \n      <p>[[message]]</p>      \n      \n      <p>&nbsp;</p>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n  </tr>\n</table>\n<p>&nbsp;</p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Dear customer,<br/>\r\n<br/>\r\nSincerely, the [[GKS_SITE_HUMAN_NAME]] team',0,4,'en-US',0,NULL,1000003,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (5,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:26:26','calendar_notification','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px 0px 20px; margin: 0px; font-weight: bold; text-align: center;\">Συμβάν Ημερολογίου</p>\n<p style=\"margin-top: 40px;\"><strong>[[is_oloimero]]</strong><br /><strong>Από:</strong> [[apo]]<br /><strong>Έως:</strong> [[eos]]<br /><strong>Περιγραφή:</strong> [[perigrafi]]<br /><strong>Τοποθεσία:</strong> [[topothesia]]</p>\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Συνάντηση #[[id]]','Με εκτίμηση, η ομάδα του<br/> [[GKS_SITE_HUMAN_NAME]]',0,5,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"is_oloimero\",\r\n        \"id\": \"email_param_is_oloimero\",\r\n        \"px\": \"Ολοήμερο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"apo\",\r\n        \"id\": \"email_param_apo\",\r\n        \"px\": \"31/12/2021\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"eos\",\r\n        \"id\": \"email_param_eos\",\r\n        \"px\": \"31/12/2021\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"perigrafi\",\r\n        \"id\": \"email_param_perigrafi\",\r\n        \"px\": \"Κείμενο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"topothesia\",\r\n        \"id\": \"email_param_topothesia\",\r\n        \"px\": \"Θεσσαλονίκη\"\r\n    }\r\n]',1000005,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (7,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:07','new_user','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      <p  style=\"margin-top: 40px;\">\n      Γειά σας,\n      </p>\n      <p>[[message]]</p>\n      <p>Έχει δημιουργηθεί για εσάς ένας νέος λογαριασμός στο <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]</a></p>\n      <p>\n      Τα στοιχεία σύνδεσης είναι τα παρακάτω:\n      <br/>\n      Όνομα χρήστη: <b>[[username]]</b>\n      <br/>\n      Συνθηματικό: <b>[[password]]</b>\n      <br/>\n      Email: <b><a href=\"mailto:[[email]]\">[[email]]</a></b>\n      </p><p>\n      Για να συνδεθείτε μεταβείτε στην σελίδα:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/wp-login.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/wp-login.php</a>\n      </p><p>\n      Για να αλλάξετε τα στοιχεία σας μεταβείτε στην σελίδα:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/my/profile.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/my/profile.php</a>\n      </p><p>\n      Μην διστάσετε να επικοινωνήσετε μαζί μας.<br/>\n      Μέσω email: <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a><br/>\n      <br/>\n      Θα χαρούμε να σας εξυπηρετήσουμε.\n\n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]\n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n             \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n            \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,7,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"username\",\r\n        \"id\": \"email_param_username\",\r\n        \"px\": \"username\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"password\",\r\n        \"id\": \"email_param_password\",\r\n        \"px\": \"12345678\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"email\",\r\n        \"id\": \"email_param_email\",\r\n        \"px\": \"username@example.com\"\r\n    }\r\n]',1000007,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (8,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:11','new_user.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      <p  style=\"margin-top: 40px;\">\n      Hello,\n      </p>\n      <p>[[message]]</p>\n      <p>A new account has been created for you <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]</a></p>\n      <p>\n      The login details are as follows:\n      <br/>\n      Username: <b>[[username]]</b>\n      <br/>\n      Password: <b>[[password]]</b>\n      <br/>\n      Email: <b><a href=\"mailto:[[email]]\">[[email]]</a></b>\n      </p><p>\n      To log in go to page:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/wp-login.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/wp-login.php</a>\n      </p><p>\n      To change your details go to the page:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/my/profile.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/my/profile.php</a>\n      </p><p>\n      Feel free to contact us.<br/>\n      By email: <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a><br/>\n     \n      <br/>\n      We will be happy to assist you.\n      </p>\n\n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team\n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]            \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]]\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\r              \n              Please consider the environment before printing this e-mail !\r            \n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\r             \n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,8,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"username\",\r\n        \"id\": \"email_param_username\",\r\n        \"px\": \"username\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"password\",\r\n        \"id\": \"email_param_password\",\r\n        \"px\": \"12345678\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"email\",\r\n        \"id\": \"email_param_email\",\r\n        \"px\": \"username@example.com\"\r\n    }\r\n]',1000007,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (9,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:16','order_execute','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Εκτέλεση παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία σας με αριθμό [[id_order]] έχει εκτελεσθεί επιτυχώς.',0,9,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000009,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (10,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:17','order_execute.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order execution [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nYour order with number [[id_order]] has been successfully executed.',0,10,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000009,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (11,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:20','order_execute_partial','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Μερική εκτέλεση παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία σας με αριθμό [[id_order]] έχει εκτελεστεί μερικώς.\r\n<br/>\nΘα ενημερωθείτε με άλλο μήνυμα για την εξέλιξη της παραγγελίας.',0,11,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000011,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (12,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:21','order_execute_partial.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Partial order fulfillment [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nYour order with [[id_order]] has been partially executed.<br/>\r\nYou will be notified with another message about how the order will progress.',0,12,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000011,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (13,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:25','order_invoice','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραστατικό της παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΣας αποστέλλουμε το παραστατικό για την παραγγελία με [[id_order]] ως συνημμένο.',0,13,'el-GR',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000013,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (14,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:27','order_invoice.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order invoice [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nWe send you the invoice for order [[id_order]] as an attachment.',0,14,'en-US',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000013,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (15,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:47:14','order_receive','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nέχουμε λάβει την παραγγελία σας με αριθμό [[id_order]]',0,15,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000015,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (16,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:47:14','order_receive.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nwe have received your order with number [[id_order]]',0,16,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000015,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (17,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:11:29','wait_bank_payment','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΓια να προχωρήσει η παραγγελία σας θα πρέπει να κάνετε κατάθεση του ποσού <b>[[poso]]</b> σε έναν από τους παρακάτω τραπεζικούς λογαριασμούς:<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nΚατά την διαδικασία της κατάθεσης, ορίστε στην Αιτιολογία τον αριθμό <b>[[bank_deposit_9digit]]</b> έτσι ώστε να μπορέσουμε να ταυτοποιήσουμε την κατάθεση με την συγκεκριμένη παραγγελία.<br/>\r\n<br/>\r\nΕάν υπάρχουν τυχόν έξοδα για την μεταφορά των χρημάτων, θα πρέπει να τα επιβαρυνθείτε εσείς.<br/>\r\n<br/>\r\nΔεν θα εκτελεστεί η παραγγελία σας εάν δεν συμφωνεί το τελικό ποσό.<br/>\r\n<br/>\r\nΣτείλτε μας το αποδεικτικό κατάθεσης με email στο <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\n<br/>\r\nΘα ενημερωθείτε με email ή/και με SMS για την εξέλιξη της παραγγελίας.<br/>\r\n<br/>',0,17,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000017,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (18,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:13:11','wait_bank_payment.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nTo advance your order you will need to deposit the amount <b>[[poso]]</b> in one of the following bank accounts:<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nWhen submitting a deposit, specify the number in the Reason <b>[[bank_deposit_9digit]]</b> so that we can identify the deposit with that particular order.<br/>\r\n<br/>\r\nIf there are any costs involved in transferring the money, you will have to bear it.<br/>\r\n<br/>\r\nYour order will not be executed if the final amount does not match.<br/>\r\n<br/>\r\nEmail us your proof of deposit at <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\n<br/>\r\nYou will be informed by email and/or SMS about the progress of the order.<br/>\r\n<br/>',0,18,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000017,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (19,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:26:16','Έτοιμη η παραγγελία','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία είναι έτοιμη, μπορείτε να την παραλάβετε.<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]',0,19,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000019,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (21,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:11:12','Κράτηση αναμονή πληρωμής','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px 0px 20px 0px; margin: 0px; font-weight: bold;\">Αριθμός Κράτησης: [[id_hotel_reservation]]</p>\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"padding: 0px 0px 20px 0px; margin: 0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></p>\n<p style=\"padding: 0px 0px 20px 0px; margin: 0px;\">Σας ευχαριστούμε</p>\n<p style=\"margin-top: 40px;\" align=\"left\">Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Κράτηση [[id_hotel_reservation]]','Αγαπητέ επισκέπτη,<br/>\r\n<br/>\r\nΑυτή είναι η επιβεβαίωση για την κράτηση δωματίου στο [[GKS_SITE_HUMAN_NAME]].<br/>\r\nΕκ μέρους του ξενοδοχείου, θα θέλαμε να σας ευχαριστήσουμε που επιλέξατε τις υπηρεσίες μας.<br/>\r\nΠαρακαλούμε βρείτε όλες τις λεπτομέρειες σχετικά με την επιβεβαίωση της κράτησής σας που παρατίθενται παρακάτω:<br/>\r\n<br/>\r\n<b>Λεπτομέρειες :</b><br/>\r\n[[get_list_reservation_rooms]]<br/>\r\n<br/>\r\nΣύνολο: <b>[[poso]]</b><br/>\r\n<br/>\r\nΘα το εκτιμούσαμε πολύ αν μπορείτε να μας ενημερώσετε για τυχόν αλλαγές στο χρονοδιάγραμμα ή στο πρόγραμμά σας.<br/>\r\nΣε περίπτωση που απαιτείται να γίνει check-in νωρίτερα λόγω αλλαγών στην πτήση σας ή για οποιοδήποτε άλλο λόγο, παρακαλούμε ενημερώστε μας.<br/>\r\nΣύμφωνα με την Πολιτική Κρατήσεων & Ακυρώσεων λόγω μεγάλης ζήτησης στα δωμάτια μας, παρακαλούμε να μας καταθέσετε μέσω εμβάσματος το 30% του συνολικού ποσού πληρωμής σας ως εγγύηση της κράτησή σας.<br/>\r\nΤο έμβασμα μπορεί να γίνει σε έναν από τους παρακάτω λογαριασμούς τουλάχιστον μέχρι και 3 εβδομάδες πριν την άφιξή σας στις εγκαταστάσεις μας:<br/>\r\n<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nΑιτιολογία εμβάσματος: Αριθμός κράτησης: RSRV[[id_hotel_reservation]]/[[bank_deposit_9digit]]<br/>\r\nΠοσό: <b>[[poso_pososto_30]]</b><br/>\r\n<br/>\r\nΕάν υπάρχουν τυχόν έξοδα για την μεταφορά των χρημάτων, θα πρέπει να τα επιβαρυνθείτε εσείς.<br/>\r\nΔεν θα προχωρήσει η κράτησή σας εάν δεν συμφωνεί το τελικό ποσό.<br/>\r\nΣτείλτε μας το αποδεικτικό κατάθεσης με email στο <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\nΘα ενημερωθείτε με email ή/και με SMS για την εξέλιξη της κράτησης.<br/>\r\n<br/>\r\nΕίμαστε βέβαιοι ότι θα βρείτε όλες τις υπηρεσίες μας ικανοποιητικές.<br/>\r\nΣε περίπτωση που θα πρέπει να ακυρώσετε την κράτηση σας, παρακαλούμε ενημερώστε μας τουλάχιστον 24 ώρες πριν την αναμενόμενη άφιξή σας.<br/>\r\nΑνυπομονούμε να σας προσφέρουμε ποιοτικές υπηρεσίες στο ξενοδοχείο μας.<br/>',0,21,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_hotel_reservation\",\r\n        \"id\": \"email_param_id_hotel_reservation\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso_pososto_30\",\r\n        \"id\": \"email_param_poso_pososto_30\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_reservation_rooms\",\r\n        \"id\": \"email_param_get_list_reservation_rooms\",\r\n        \"px\": \"\",\r\n        \"icon\": \"<i class=\'fa fa-hotel set_def_list_reservation_rooms tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000021,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (23,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:30:55','Ραντεβού','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px; margin: 0px; font-weight: bold; text-align: center;\">Ραντεβού</p>\n<p style=\"margin-top: 20px;\"><strong>Από:</strong> [[apo]]<br /><strong>Έως:</strong> [[eos]]<br /><strong>Περιγραφή:</strong> [[perigrafi]]<br /><strong>Τοποθεσία:</strong> [[topothesia]]</p>\n<p style=\"margin-top: 20px;\">[[message]]</p>\n<p style=\"margin-top: 20px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Ραντεβού #[[id]]','Αγαπητέ κύριε κυρία [[pelatis_name]],<br/>\r\n<br/>\r\nτο ραντεβού σας με την εταιρεία μας έχει επιβεβαιωθεί με τα παραπάνω στοιχεία.<br/>\r\n<br/>\r\nΕάν προκύψει κάποια αλλαγή στο πρόγραμμά σας παρακαλούμε να μας ενημερώσετε έγκαιρα είτε τηλεφωνικά είτε με email.<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του <br/>[[GKS_SITE_HUMAN_NAME]]',0,23,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"apo\",\r\n        \"id\": \"email_param_apo\",\r\n        \"px\": \"31/12/2021 10:00\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"eos\",\r\n        \"id\": \"email_param_eos\",\r\n        \"px\": \"31/12/2021 11:00\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"perigrafi\",\r\n        \"id\": \"email_param_perigrafi\",\r\n        \"px\": \"Κείμενο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"topothesia\",\r\n        \"id\": \"email_param_topothesia\",\r\n        \"px\": \"Θεσσαλονίκη\"\r\n    }\r\n]',1000023,'html')");


          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (27,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 00:24:38','Τιμολόγιο συνημμένο','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]<br /><br /><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]] Παραστατικό','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΤο παραστατικό είναι συνημμένο.\r\n<br/>\r\n<br/>\r\nΣτο υποσέλιδο που παραστατικού υπάρχει ο τραπεζικός λογαριασμός.\r\n<br/>\r\n<br/>\r\nΓια οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email [[GKS_SITE_EMAIL]]\r\n<br/>\r\n<br/>\r\nΜε εκτίμηση,\r\n<br/>\r\nη ομάδα του [[GKS_SITE_HUMAN_NAME]]',0,27,'el-GR',1,NULL,1000027,'html')");
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (28,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 22:01:28','Τιμολόγιο συνημμένο.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]<br /><br /><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">This e-mail cannot be considered spam as long as the sender\'s details and unlisting procedures are indicated and it meets the requirements of the European legislation on advertising messages: \"Each message should clearly state the sender\'s full details and must give the recipient the option to delete. Directive2002/58/EC\' of the European Parliament, Relative as A5-270/2001 of the European Parliament.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">You can unsubscribe from the list by clicking on this <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> and send the email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Consider the environment before printing this e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Confidentiality Warning - Disclaimer: This email contains information intended only for the individual or entity to whom it is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for its delivery to the intended recipient, any dissemination, copying or other use or taking of any action based on this e-mail is strictly prohibited . The sender is not responsible for any loss, interruption or damage to your data or computer system that may occur when using data contained in, or transmitted by, this e-mail. If you received this e-mail in error, please notify the sender immediately by returning e-mail and delete the material from any computer. Any opinions expressed are personal unless otherwise stated. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]] invoice','Dear customer,<br/>\r\n<br/>\r\nThe invoice is attached.\r\n<br/>\r\n<br/>\r\nIn the footer of that document there is the bank account.\r\n<br/>\r\n<br/>\r\nFor any information, contact us at [[GKS_SITE_EMAIL]]\r\n<br/>\r\n<br/>\r\nYours sincerely,\r\n<br/>\r\nthe [[GKS_SITE_HUMAN_NAME]] team',0,28,'en-US',1,NULL,1000027,'html')");


}




$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_ACC_INV_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_ACC_INV_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
}

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_ACC_PAY_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_ACC_PAY_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
}

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_WHI_MOV_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_WHI_MOV_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
}

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_TRANSFER_RESERVATION_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_TRANSFER_RESERVATION_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_RESERVATION_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_RESERVATION_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
}

$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, "define('GKS_ORDERS_STATUS_BUTTONS'") !== false) {
  $pos1=strpos($read_file, "define('GKS_ORDERS_STATUS_BUTTONS'");
  $pos2=strpos($read_file,'));',$pos1+10);
  $read_file2=substr($read_file, 0,$pos1);
  $read_file2.=substr($read_file, $pos2+3);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file2);
  
  
}
$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
$read_file=str_replace('// https://docs.woocommerce.com/document/managing-orders/','',$read_file);
for ($iii=1;$iii<=10;$iii++) {
  $read_file=str_replace("\r\n\r\n\r\n","\r\n\r\n",$read_file);
}
file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file);

$sql="select * from gks_print_objects where id_print_object=8";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_print_objects (
  id_print_object,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_descr
  ) values (
  8,now(),now(),2,2,'127.0.0.1',
  'gks_eshop_products','Είδη')");
}

gks_run_sql("update gks_custom_table set custom_table_descr='Λογιστική - Εντατική Λιανική' where id_custom_table=37");


$sql="select preferred_payment_method from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  ADD COLUMN `preferred_payment_method` varchar(16) DEFAULT NULL AFTER `transaction_type`,
  add index `preferred_payment_method` (`preferred_payment_method`)");
}



$sql="select viva_preferred_payment_methods from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `viva_preferred_payment_methods` text DEFAULT NULL 
  AFTER `viva_pos_client_secret`");
  gks_run_sql("update gks_company set viva_preferred_payment_methods='[\"tap\",\"iris\"]';");
  
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `mellon_preferred_payment_methods` text DEFAULT NULL 
  AFTER `mellon_mid`");
  gks_run_sql("update gks_company set mellon_preferred_payment_methods='[\"tap\",\"iris\"]';");

  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `cardlink_preferred_payment_methods` text DEFAULT NULL 
  AFTER `cardlink_mid`");
  gks_run_sql("update gks_company set cardlink_preferred_payment_methods='[\"tap\",\"iris\"]';");

  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `epay_preferred_payment_methods` text DEFAULT NULL 
  AFTER `epay_x_api_key`");
  gks_run_sql("update gks_company set epay_preferred_payment_methods='[\"tap\",\"iris\"]';");

  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `worldline_preferred_payment_methods` text DEFAULT NULL 
  AFTER `worldline_x_api_key`");
  gks_run_sql("update gks_company set worldline_preferred_payment_methods='[\"tap\"]';");
}

$sql="select paymenttype from gks_mellon_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
  ADD COLUMN `PaymentType` tinyint(4) NULL,
  ADD INDEX PaymentType (PaymentType)");
}
$sql="select paymenttype from gks_epay_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_epay_transaction` 
  ADD COLUMN `PaymentType` tinyint(4) NULL,
  ADD INDEX PaymentType (PaymentType)");
}


$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with=7";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
   (7,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','NEXI',0,7)");
}


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_NEXI_COM_API') === false) {
  $read_file=str_replace("define('GKS_ESHOP_BRANDS_TAXONOMY'", 
"define('GKS_NEXI_COM_API','https://ecr-sandbox.prd.api-fintechiq.com/v1');
//define('GKS_NEXI_COM_API','https://ecr.prd.api-fintechiq.com/v1');

define('GKS_ESHOP_BRANDS_TAXONOMY'", $read_file);
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php',$read_file);
}

$sql="select nexi_mid from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `nexi_mid` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_username` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_password` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_authorization_code` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_x_api_key` varchar(512) DEFAULT NULL,
  ADD COLUMN `nexi_preferred_payment_methods` text DEFAULT NULL");
  
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  MODIFY COLUMN `remote_id` VARCHAR(190) DEFAULT NULL;");

  gks_run_sql("update `gks_eftpos_transaction` 
  set `remote_id`=null where remote_id='0';");
}

gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
MODIFY COLUMN `intentId` VARCHAR(190) DEFAULT NULL;");

gks_run_sql("update `gks_mellon_transaction` 
set `intentId`=null where intentId='0';");

gks_run_sql("ALTER TABLE `gks_epay_transaction` 
MODIFY COLUMN `intentId` VARCHAR(190) DEFAULT NULL;");

gks_run_sql("update `gks_epay_transaction` 
set `intentId`=null where intentId='0';");


$sql="select nexi_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `nexi_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_terminal_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `nexi_company_id` int(11) NOT NULL DEFAULT 0");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_nexi_transaction` (
  `id_nexi_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eftpos_transaction_id` int(11) DEFAULT NULL,
  `intentId` varchar(190) DEFAULT NULL,
  `myStatus` tinyint(4) NOT NULL DEFAULT 0,
  `myResult` tinyint(4) NOT NULL DEFAULT 0,
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,
  `TransactionId_org` varchar(190) DEFAULT NULL,
  `Id` varchar(190) DEFAULT NULL,
  `ExternalId` varchar(190) DEFAULT NULL,
  `TxnType` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT NULL,
  `VoidTimestamp` datetime DEFAULT NULL,
  `CardPAN` varchar(190) DEFAULT NULL,
  `CardHash` varchar(190) DEFAULT NULL,
  `Approved` tinyint(4) NOT NULL DEFAULT 0,
  `Voided` tinyint(4) NOT NULL DEFAULT 0,
  `STAN` int(11) DEFAULT NULL,
  `BatchNumber` int(11) DEFAULT NULL,
  `Batch` varchar(190) DEFAULT NULL,
  `Acquirer` varchar(190) DEFAULT NULL,
  `TID` varchar(190) DEFAULT NULL,
  `MID` varchar(190) DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `DccAmount` double DEFAULT NULL,
  `TipAmount` double DEFAULT NULL,
  `CashbackAmount` double DEFAULT NULL,
  `LoyaltyRedemptionAmount` double DEFAULT NULL,
  `Instalments` int(11) DEFAULT NULL,
  `PosEntryMode` int(11) DEFAULT NULL,
  `Cryptogram` varchar(190) DEFAULT NULL,
  `HostResponseCode` varchar(190) DEFAULT NULL,
  `RRN` varchar(190) DEFAULT NULL,
  `AuthCode` varchar(190) DEFAULT NULL,
  `OriginalRRN` varchar(190) DEFAULT NULL,
  `OriginalAuthCode` varchar(190) DEFAULT NULL,
  `CurrencyCode` int(11) DEFAULT NULL,
  `DccCurrencyCode` varchar(190) DEFAULT NULL,
  `CustomerReference` varchar(190) DEFAULT NULL,
  `myerror` text DEFAULT NULL,
  `myjson` text DEFAULT NULL,
  `CustomerEmail` varchar(190) DEFAULT NULL,
  `CustomerPhone` varchar(190) DEFAULT NULL,
  `aadeTransactionId` varchar(190) DEFAULT NULL,
  `PaymentType` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id_nexi_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `Id` (`Id`),
  KEY `TID` (`TID`),
  KEY `MID` (`MID`),
  KEY `RRN` (`RRN`),
  KEY `OriginalRRN` (`OriginalRRN`),
  KEY `amount` (`Amount`),
  KEY `eftpos_transaction_id` (`eftpos_transaction_id`),
  KEY `myStatus` (`myStatus`),
  KEY `myResult` (`myResult`),
  KEY `intentId` (`intentId`),
  KEY `aadeTransactionId` (`aadeTransactionId`),
  KEY `myerror` (`myerror`(190)),
  KEY `PaymentType` (`PaymentType`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=857";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (857,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_nexi_transaction','Συναλλαγές NEXI',8552)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",857,1,1,1,1,1)");
  }
}


$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with=100";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
   (100,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','IRIS',0,100)");
}

$sql="select tropos_pliromis_via from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `tropos_pliromis_via` varchar(128) DEFAULT NULL after tropos_pliromis_one_multi,
  ADD index `tropos_pliromis_via`(tropos_pliromis_via)");
}

$sql="select payment_acquirer_via from gks_acc_inv_payment limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv_payment` 
  ADD COLUMN `payment_acquirer_via` varchar(128) DEFAULT NULL after payment_acquirer_id,
  ADD index `payment_acquirer_via`(payment_acquirer_via)");
}

$sql="select payment_acquirer_via from gks_acc_pay_payment limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_pay_payment` 
  ADD COLUMN `payment_acquirer_via` varchar(128) DEFAULT NULL after payment_acquirer_id,
  ADD index `payment_acquirer_via`(payment_acquirer_via)");
}

$sql="select payment_acquirer_via from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  ADD COLUMN `payment_acquirer_via` varchar(128) DEFAULT NULL after payment_acquirer_id,
  ADD index `payment_acquirer_via`(payment_acquirer_via)");
}

$sql="select CardType from gks_epay_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_epay_transaction` 
  ADD COLUMN `CardType` varchar(64) DEFAULT NULL,
  ADD index `CardType`(CardType)");
}

$sql="select CardType from gks_mellon_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
  ADD COLUMN `CardType` varchar(64) DEFAULT NULL,
  ADD index `CardType`(CardType)");
}



gks_run_sql("UPDATE gks_acc_eidi_parastatikon SET peppol_code = 380
WHERE eidos_parastatikou_aade_code='9.3'
and peppol_code=0;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_delivery_note` (
  `id_aade_delivery_note` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mark` varchar(64) DEFAULT NULL,
  `qrUrl` varchar(512) DEFAULT NULL,
  `vat_issuer` varchar(64) DEFAULT NULL,
  `vat_customer` varchar(64) DEFAULT NULL,
  `last_state` varchar(64) DEFAULT NULL,
  `last_date_get_data` datetime DEFAULT NULL,
  `last_raw_data` text DEFAULT NULL,
  PRIMARY KEY (`id_aade_delivery_note`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `mark` (`mark`),
  KEY `qrUrl` (`qrUrl`(250)),
  KEY `last_state` (`last_state`),
  KEY `last_date_get_data` (`last_date_get_data`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select vat_issuer from gks_aade_delivery_note limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_delivery_note` 
  ADD COLUMN `vat_issuer` varchar(64) DEFAULT NULL after qrUrl,
  ADD COLUMN `vat_customer` varchar(64) DEFAULT NULL after vat_issuer");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_delivery_note_log` (
  `id_aade_delivery_note_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aade_delivery_note_id` int(11) NOT NULL DEFAULT 0,
  `add_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `sxolio` text NOT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_aade_delivery_note_log`) USING BTREE,
  KEY `aade_delivery_note_id` (`aade_delivery_note_id`),
  KEY `user_id` (`user_id`),
  KEY `add_date` (`add_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_delivery_note_role` (
  `id_aade_delivery_note_role` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aade_delivery_note_id` int(11) NOT NULL DEFAULT 0,
  `role` varchar(32) DEFAULT NULL,
  `doc_table` varchar(32) DEFAULT NULL,
  `xxx_xxx_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_aade_delivery_note_role`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `aade_delivery_note_id` (`aade_delivery_note_id`),
  KEY `doc_table` (`doc_table`),
  KEY `xxx_xxx_id` (`xxx_xxx_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=504";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (504,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_aade_delivery_note','ΑΑΔΕ Ψηφιακό δελτίο αποστολής',5050)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",504,1,1,1,1,1)");
  }
}


$sql="select pos_print_x_form_id from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_print_x_form_id` int(11) NOT NULL DEFAULT 0 after pos_thermal_form_id");
}

$sql="select perm_int_cond02 from gks_permission_user limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_permission_user` 
  ADD COLUMN `perm_int_cond02` tinyint(4) NOT NULL DEFAULT 0 after perm_int_cond01");
}

$sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=921";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO gks_acc_eidi_parastatikon (
  id_acc_eidos_parastatikou,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  parent_id,eidos_parastatikou_type_id,eidos_parastatikou_need_prev,
  eidos_parastatikou_has_fpa,eidos_parastatikou_has_posotita,eidos_parastatikou_has_othertaxes,
  eidos_parastatikou_has_esoda,eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
  eidos_parastatikou_aade_code,eidos_parastatikou_descr,eidos_parastatikou_balance_pros,
  eidos_parastatikou_stock_pros,eidos_parastatikou_whi_type_id,eidos_parastatikou_other_entity,
  eidos_parastatikou_correlated_invoices,def_prefix,def_suffix,
  sortorder,is_selectable,credit_acc_eidos_parastatikou_id,
  import_apo_allon,peppol_code
  ) VALUES 
   (921,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
   901,21,0,
   0,1,null,
   0,0,1,
   '9.2','Συγκεντρωτικό Δελτίο Αποστολής',0,
   -1,0,1,
   0,null,null,
   3591,1,903,
   null,380),
   (922,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
   901,21,0,
   0,1,null,
   0,0,1,
   '9.1','Δελτίο Αποστολής Συσχετιζόμενο με Συγκεντρωτικό',0,
   -1,0,1,
   1,null,null,
   3592,1,903,
   null,380),
   (970,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
   0,0,0,
   0,1,null,
   0,0,1,
   null,'Δελτίο Ποσοτικής Παραλαβής',0,
   0,0,0,
   0,null,null,
   3700,0,0,
   null,0),
   (971,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
   970,22,0,
   0,1,null,
   0,0,1,
   '10.1','Δελτίο Ποσοτικής Παραλαβής Συσχετιζόμενο',0,
   1,0,1,
   1,null,null,
   3701,1,0,
   null,380),
   (972,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
   970,22,0,
   0,1,null,
   0,0,1,
   '10.2','Δελτίο Ποσοτικής Παραλαβής Μη Συσχετιζόμενο',0,
   1,0,1,
   0,null,null,
   3702,1,0,
   null,380)");
}


$sql="select * from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=24";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO gks_aade_skopos_diakinisis (
  id_aade_skopos_diakinisis,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_skopos_diakinisis_code,aade_skopos_diakinisis_descr,sortorder,aade_disable
  ) values (
  24,'2021-01-01','2021-01-01',2,2,'127.0.0.1',
  0,'Ποσοτική Παραλαβή',17,0
  )");
  
  gks_run_sql("update gks_aade_skopos_diakinisis set aade_skopos_diakinisis_descr='Επεξεργασία - Συναρμολόγηση' where id_aade_skopos_diakinisis=7");
  gks_run_sql("update gks_aade_skopos_diakinisis set aade_skopos_diakinisis_descr='Μεταξύ Εγκαταστάσεων Οντότητας' where id_aade_skopos_diakinisis=8");
  
  
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 2 και 3 του Κώδικα ΦΠΑ')."' where aade_katigoria_fpa_ejeresi_code=1");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 5 του Κώδικα ΦΠΑ')."' where aade_katigoria_fpa_ejeresi_code=2");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 17 του Κώδικα ΦΠΑ (πρώην άρθρο 13)')."' where aade_katigoria_fpa_ejeresi_code=3");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 18 του Κώδικα ΦΠΑ (πρώην άρθρο 14)')."' where aade_katigoria_fpa_ejeresi_code=4");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 21 του Κώδικα ΦΠΑ (πρώην άρθρο 16)')."' where aade_katigoria_fpa_ejeresi_code=5");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 24 του Κώδικα ΦΠΑ (πρώην άρθρο 19)')."' where aade_katigoria_fpa_ejeresi_code=6");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 27 του Κώδικα ΦΠΑ (πρώην άρθρο 22)')."' where aade_katigoria_fpa_ejeresi_code=7");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 29 του Κώδικα ΦΠΑ (πρώην άρθρο 24)')."' where aade_katigoria_fpa_ejeresi_code=8");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 30 του Κώδικα ΦΠΑ (πρώην άρθρο 25)')."' where aade_katigoria_fpa_ejeresi_code=9");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 31 του Κώδικα ΦΠΑ (πρώην άρθρο 26)')."' where aade_katigoria_fpa_ejeresi_code=10");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 32 του Κώδικα ΦΠΑ (πρώην άρθρο 27)')."' where aade_katigoria_fpa_ejeresi_code=11");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 32 του Κώδικα ΦΠΑ - Πλοία Ανοικτής Θαλάσσης του Κώδικα ΦΠΑ (πρώην άρθρο 27)')."' where aade_katigoria_fpa_ejeresi_code=12");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 32 .1.γ. του Κώδικα ΦΠΑ - Πλοία Ανοικτής Θαλάσσης του Κώδικα ΦΠΑ (πρώην άρθρο 27.1.γ.)')."' where aade_katigoria_fpa_ejeresi_code=13");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 33 του Κώδικα ΦΠΑ (πρώην άρθρο 28)')."' where aade_katigoria_fpa_ejeresi_code=14");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 44 του Κώδικα ΦΠΑ (πρώην άρθρο 39)')."' where aade_katigoria_fpa_ejeresi_code=15");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 45 του Κώδικα ΦΠΑ (πρώην άρθρο 39α)')."' where aade_katigoria_fpa_ejeresi_code=16");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 47 του Κώδικα ΦΠΑ (πρώην άρθρο 40)')."' where aade_katigoria_fpa_ejeresi_code=17");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 48 του Κώδικα ΦΠΑ (πρώην άρθρο 41)')."' where aade_katigoria_fpa_ejeresi_code=18");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 54 του Κώδικα ΦΠΑ (πρώην άρθρο 47)')."' where aade_katigoria_fpa_ejeresi_code=19");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('ΦΠΑ εμπεριεχόμενος - άρθρο 50 του Κώδικα ΦΠΑ (πρώην άρθρο 43)')."' where aade_katigoria_fpa_ejeresi_code=20");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('ΦΠΑ εμπεριεχόμενος - άρθρο 51 του Κώδικα ΦΠΑ (πρώην άρθρο 44)')."' where aade_katigoria_fpa_ejeresi_code=21");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('ΦΠΑ εμπεριεχόμενος - άρθρο 52 του Κώδικα ΦΠΑ (πρώην άρθρο 45)')."' where aade_katigoria_fpa_ejeresi_code=22");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('ΦΠΑ εμπεριεχόμενος - άρθρο 53 του Κώδικα ΦΠΑ (πρώην άρθρο 46)')."' where aade_katigoria_fpa_ejeresi_code=23");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - άρθρο 8 του Κώδικα ΦΠΑ (πρώην άρθρο 6)')."' where aade_katigoria_fpa_ejeresi_code=24");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - ΠΟΛ.1029/1995')."' where aade_katigoria_fpa_ejeresi_code=25");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ - ΠΟΛ.1167/2015')."' where aade_katigoria_fpa_ejeresi_code=26");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Λοιπές Εξαιρέσεις ΦΠΑ')."' where aade_katigoria_fpa_ejeresi_code=27");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ – άρθρο 29 περ. β παρ.1 του Κώδικα ΦΠΑ (Tax Free) (πρώην άρθρο 24 περ. β παρ.1)')."' where aade_katigoria_fpa_ejeresi_code=28");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ – άρθρο 56 του Κώδικα ΦΠΑ (OSS_μη ενωσιακό καθεστώς) (πρώην άρθρο 47β)')."' where aade_katigoria_fpa_ejeresi_code=29");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ – άρθρο 57 του Κώδικα ΦΠΑ (OSS_ενωσιακό καθεστώς) (πρώην άρθρο 47γ)')."' where aade_katigoria_fpa_ejeresi_code=30");
  gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set aade_katigoria_fpa_ejeresi_descr='".$db_link->escape_string('Χωρίς ΦΠΑ – άρθρο 58 του Κώδικα ΦΠΑ (IOSS) (πρώην άρθρο 47δ)')."' where aade_katigoria_fpa_ejeresi_code=31");


  gks_run_sql("update gks_aade_katigoria_xartosimou set aade_katigoria_xartosimou_descr='Λοιπές περιπτώσεις' where aade_katigoria_xartosimou_code=4");
}

$sql="select * from gks_aade_katigoria_loipon_foron where id_aade_katigoria_loipon_foron=20";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_aade_katigoria_loipon_foron
  (id_aade_katigoria_loipon_foron,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_katigoria_loipon_foron_code,aade_katigoria_loipon_foron_descr,
  aade_katigoria_loipon_foron_type,aade_katigoria_loipon_foron_pososto,
  aade_katigoria_loipon_foron_poso_fn,aade_katigoria_loipon_foron_poso_fix,
  sortorder,aade_disable,loipon_foron_peppol_code)
  values 
  (20,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  20,'Ξενοδοχεία 1-2 αστέρων 1,50€ (ανά Δωμ./Διαμ.)',
  'free',null,
  null,null,
  20,0,'AEF'),
  (21,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  21,'Ξενοδοχεία 3 αστέρων 3,00€ (ανά Δωμ./Διαμ.)',
  'free',null,
  null,null,
  21,0,'AEF'),
  (22,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  22,'Ξενοδοχεία 4 αστέρων 7,00€ (ανά Δωμ./Διαμ.)',
  'free',null,
  null,null,
  22,0,'AEF'),
  (23,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  23,'Ξενοδοχεία 5 αστέρων 10,00€ (ανά Δωμ./Διαμ.)',
  'free',null,
  null,null,
  23,0,'AEF'),
  (24,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  24,'Ενοικιαζόμενα επιπλωμένα δωμάτια – διαμερίσματα 1,50€ (ανά Δωμ./Διαμ.)',
  'free',null,
  null,null,
  24,0,'AEF'),
  (25,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  25,'Ακίνητα βραχυχρόνιας μίσθωσης 1,50€',
  'free',null,
  null,null,
  25,0,'AEF'),
  (26,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  26,'Ακίνητα βραχυχρόνιας μίσθωσης μονοκατοικίες άνω των 80 τ.μ. 10,00€',
  'free',null,
  null,null,
  26,0,'AEF'),
  (27,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  27,'Αυτοεξυπηρετούμενα καταλύματα – τουριστικές επιπλωμένες επαύλεις (βίλες) 10,00€',
  'free',null,
  null,null,
  27,0,'AEF'),
  (28,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  28,'Ακίνητα βραχυχρόνιας μίσθωσης 0,50€',
  'free',null,
  null,null,
  28,0,'AEF'),
  (29,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  29,'Ακίνητα βραχυχρόνιας μίσθωσης μονοκατοικίες άνω των 80 τ.μ. 4,00€',
  'free',null,
  null,null,
  29,0,'AEF'),
  (30,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  30,'Αυτοεξυπηρετούμενα καταλύματα – τουριστικές επιπλωμένες επαύλεις (βίλες) 4,00€',
  'free',null,
  null,null,
  30,0,'AEF');");
}


$sql="select * from gks_permission_object where id_permission_object=781";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (781,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks__paroxos_overview_ilyda','Επισκόπηση Παρόχου ΙΛΥΔΑ',8810)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",781,1,1,1,1,1)");
  }

}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_paroxos_overview_ilyda_invoice_pending` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `afm` varchar(190) DEFAULT NULL,
  `invoiceNumber` varchar(190) DEFAULT NULL,
  `uid` varchar(190) DEFAULT NULL,
  `sellerVatIdentifier` varchar(64) DEFAULT NULL,
  `seriesNumber` varchar(64) DEFAULT NULL,
  `serialNumber` varchar(64) DEFAULT NULL,
  `invoiceId` varchar(190) DEFAULT NULL,
  `mark` varchar(64) DEFAULT NULL,
  `verificationHash` varchar(190) DEFAULT NULL,
  `invoiceIssueDate` datetime DEFAULT NULL,
  `invoiceState` varchar(190) DEFAULT NULL,
  `errorsJson` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mydate_add` (`mydate_add`),
  KEY `user_id_add` (`user_id_add`),
  KEY `afm` (`afm`),
  KEY `invoiceNumber` (`invoiceNumber`),
  KEY `uid` (`uid`),
  KEY `sellerVatIdentifier` (`sellerVatIdentifier`),
  KEY `seriesNumber` (`seriesNumber`),
  KEY `serialNumber` (`serialNumber`),
  KEY `invoiceId` (`invoiceId`),
  KEY `mark` (`mark`),
  KEY `invoiceIssueDate` (`invoiceIssueDate`),
  KEY `invoiceState` (`invoiceState`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS gks_paroxos_tf1_keys (
  `id_paroxos_tf1_keys` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `local_status` varchar(32) DEFAULT NULL,
  `paroxos_id` int(11) NOT NULL DEFAULT 0,
  `afm` varchar(190) DEFAULT NULL,
  `secret` varchar(190) DEFAULT NULL,
  `keyIdentifier` varchar(190) DEFAULT NULL,
  `keyVersion` int(11) NOT NULL DEFAULT 0,
  `algorithm` varchar(64) DEFAULT NULL,
  `purpose` varchar(64) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `issuedAt` datetime DEFAULT NULL,
  `revokedAt` datetime DEFAULT NULL,
  `validFrom` datetime DEFAULT NULL,
  `validTo` datetime DEFAULT NULL,
  `installationVerifiedAt` datetime DEFAULT NULL,
  `revokeReason` varchar(190) DEFAULT NULL,
  `linkBaseUrl` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_paroxos_tf1_keys`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `local_status` (`local_status`),
  KEY `paroxos_id` (`paroxos_id`),
  KEY `afm` (`afm`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select paroxos_tf1_url from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `paroxos_tf1_url` text DEFAULT NULL after paroxos_send_pdf_url,
  ADD COLUMN `paroxos_tf1_url_has` tinyint(4) NOT NULL DEFAULT 0 after paroxos_tf1_url,
  add index paroxos_tf1_url_has (paroxos_tf1_url_has)");
}
$sql="select paroxos_tf1_url from gks_acc_pay limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_pay` 
  ADD COLUMN `paroxos_tf1_url` text DEFAULT NULL after paroxos_send_pdf_url,
  ADD COLUMN `paroxos_tf1_url_has` tinyint(4) NOT NULL DEFAULT 0 after paroxos_tf1_url,
  add index paroxos_tf1_url_has (paroxos_tf1_url_has)");
}
$sql="select paroxos_tf1_url from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov` 
  ADD COLUMN `paroxos_tf1_url` text DEFAULT NULL after paroxos_send_pdf_url,
  ADD COLUMN `paroxos_tf1_url_has` tinyint(4) NOT NULL DEFAULT 0 after paroxos_tf1_url,
  add index paroxos_tf1_url_has (paroxos_tf1_url_has)");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS gks_crons (
  `id_cron` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `disable_cron` tinyint(4) NOT NULL DEFAULT 0,
  `every_seconds` int(11) NOT NULL DEFAULT 0,
  `fetch_url` text DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `next_run` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `num_runs` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_cron`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `disable_cron` (`disable_cron`),
  KEY `every_seconds` (`every_seconds`),
  KEY `last_run` (`last_run`),
  KEY `next_run` (`next_run`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_crons where id_cron<=100 limit 1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crons` (`id_cron`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `disable_cron`,`every_seconds`,`fetch_url`,`last_run`,`next_run`,`comments`) VALUES 
  (1,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,240,  '/my/cron_async_queue.php?guid=resume',null,null,''),
  (2,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,300,  '/my/cron_crm_calendar.php',null,null,''),
  (3,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,14400,'/my/cron_ips.php',null,null,''),
  (4,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',1,120,  '/my/cron_orders_links.php',null,null,''),
  (5,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',1,43200,'/my/cron_paroxos.php?get_keys=8',null,null,''),
  (6,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,3600, '/my/cron_delete_tmp_files.php?folder=tmp&minutes=120',null,null,''),
  (7,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,3600, '/my/cron_delete_tmp_files.php?folder=cache&minutes=120',null,null,''),
  (100,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',1,360,  '/wp-cron.php',null,null,'')");
  
  $afms=gks_paroxos_overview_get_afms(8); //ilyda
  if (count($afms)>0) {
    gks_run_sql("update gks_crons set disable_cron=0 where id_cron=5");    
  }


}


$sql="select * from gks_permission_object where id_permission_object=734";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (734,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_crons','Χρονοπρογραμματισμός εργασιών',3880)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",734,1,1,1,1,1)");
  }

}


$sql="select * from gks_notification_type where id_notification_type=600";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_notification_type` (`id_notification_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `notification_type_descr`,`notification_type_sortorder`,`notification_type_disabled`) VALUES 
   (600,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',600,0)");
   
  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_notification_userperm
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_notification_userperm` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,notification_type_id,from_admin,from_user,to_email,to_viber) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",600,1,1,1,1)");
  }
}

$sql="select fav_sortorder from gks_users_favorites limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users_favorites` 
  ADD COLUMN `fav_sortorder` int(11) NOT NULL DEFAULT 1000,
  add index fav_sortorder (fav_sortorder)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS gks_barcodes (
  `id_barcode` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `barcode` VARCHAR(64) DEFAULT NULL,
  `barcode_descr` VARCHAR(190) DEFAULT NULL,
  `barcode_type_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `disable_barcode` tinyint(4) NOT NULL DEFAULT 0,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id_barcode`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `barcode` (`barcode`),
  KEY `barcode_descr` (`barcode_descr`),
  KEY `barcode_type_id` (`barcode_type_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `disable_barcode` (`disable_barcode`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_permission_object where id_permission_object=202";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (202,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_barcodes','Barcodes Ειδών',2050)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",202,1,1,1,1,1)");
  }

  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_code,id_product,'Code' as comments
  from gks_eshop_products where product_code<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_sku,id_product, 'SKU' as comments
  from gks_eshop_products where product_sku<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_gtin,id_product,'GTIN' as comments
  from gks_eshop_products where product_gtin<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_upc,id_product,'UPC' as comments
  from gks_eshop_products where product_upc<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_ean,id_product,'EAN' as comments
  from gks_eshop_products where product_ean<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_isbn,id_product,'ISBN' as comments
  from gks_eshop_products where product_isbn<>'' and product_parent_old_id=0");
   
  gks_run_sql("insert into gks_barcodes (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments) 
  select now() as mydate_add,now() as mydate_edit, 2 as user_id_add, 2 as user_id_edit,'127.0.0.1' as myip,
  product_taric,id_product,'Taric' as comments
  from gks_eshop_products where product_taric<>'' and product_parent_old_id=0");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS gks_barcodes_types (
  `id_barcode_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `barcode_type_ds` VARCHAR(32) DEFAULT NULL,
  `barcode_type_code` VARCHAR(64) DEFAULT NULL,
  `barcode_type_descr` VARCHAR(190) DEFAULT NULL,
  PRIMARY KEY (`id_barcode_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `barcode_type_ds` (`barcode_type_ds`),
  KEY `barcode_type_code` (`barcode_type_code`),
  KEY `barcode_type_descr` (`barcode_type_descr`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_barcodes_types where id_barcode_type<1000";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
    gks_run_sql("INSERT INTO `gks_barcodes_types` 
    (id_barcode_type,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    barcode_type_ds,barcode_type_code,barcode_type_descr) values 

    (1, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C128A'      ,'CODE 128 A'),
    (2, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C128B'      ,'CODE 128 B'),
    (3, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C128C'      ,'CODE 128 C'),
    (4, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C128'       ,'CODE 128'),
    (5, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C39E+'      ,'CODE 39 EXTENDED + CHECKSUM'),
    (6, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C39E'       ,'CODE 39 EXTENDED'),
    (7, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C39+'       ,'CODE 39 + CHECKSUM'),
    (8, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C39'        ,'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9'),
    (9, '2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','C93'        ,'CODE 93 - USS-93'),
    (10,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','CODABAR'    ,'CODABAR'),
    (11,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','CODE11'     ,'CODE 11'),
    (12,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','EAN13'      ,'EAN 13'),
    (13,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','EAN2'       ,'EAN 2-Digits UPC-Based Extension'),
    (14,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','EAN5'       ,'EAN 5-Digits UPC-Based Extension'),
    (15,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','EAN8'       ,'EAN 8'),
    (16,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','I25+'       ,'Interleaved 2 of 5 + CHECKSUM'),
    (17,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','I25'        ,'Interleaved 2 of 5'),
    (18,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','IMB'        ,'IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200'),
    (19,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','IMBPRE'     ,'IMB pre-processed'),
    (20,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','KIX'        ,'KIX (Klant index - Customer index)'),
    (21,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','MSI+'       ,'MSI + CHECKSUM (modulo 11)'),
    (22,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','MSI'        ,'MSI (Variation of Plessey code)'),
    (23,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','PHARMA2T'   ,'PHARMACODE TWO-TRACKS'),
    (24,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','PHARMA'     ,'PHARMACODE'),
    (25,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','PLANET'     ,'PLANET'),
    (26,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','POSTNET'    ,'POSTNET'),
    (27,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','RMS4CC'     ,'RMS4CC (Royal Mail 4-state Customer Bar Code)'),
    (28,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','S25+'       ,'Standard 2 of 5 + CHECKSUM'),
    (29,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','S25'        ,'Standard 2 of 5'),
    (30,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','UPCA'       ,'UPC-A'),
    (31,'2021-01-01','2021-01-01',2,2,'127.0.0.1','linear','UPCE'       ,'UPC-E'),      
  
    (51,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','LRAW'             ,'1D RAW MODE (comma-separated rows of 01 strings)'),
    (52,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','SRAW'             ,'2D RAW MODE (comma-separated rows of 01 strings)'),
    (53,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','AZTEC'            ,'AZTEC (ISO/IEC 24778:2008)'),
    (54,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','AZTEC,50,A,A'     ,'AZTEC (ISO/IEC 24778:2008)-50aa'),
    (55,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','PDF417'           ,'PDF417 (ISO/IEC 15438:2006)'),
    (56,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','QRCODE'           ,'QR-CODE'),
    (57,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','QRCODE,H,ST,0,0'  ,'QR-CODE WITH PARAMETERS'),
    (58,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','DATAMATRIX'       ,'DATAMATRIX (ISO/IEC 16022) SQUARE'),
    (59,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','DATAMATRIX,R'     ,'DATAMATRIX Rectangular (ISO/IEC 16022) RECTANGULAR'),
    (60,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','DATAMATRIX,S,GS1' ,'GS1 DATAMATRIX (ISO/IEC 16022) SQUARE GS1'),
    (61,'2021-01-01','2021-01-01',2,2,'127.0.0.1','square','DATAMATRIX,R,GS1' ,'GS1 DATAMATRIX (ISO/IEC 16022) RECTANGULAR GS1');
  
  ");
}

$sql="select viva_action_after from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `viva_action_after` tinyint(4) NOT NULL DEFAULT 0 after viva_terminal_code");
}

gks_run_sql("update gks_custom_table set custom_table_descr='Transfer - Περιοχές' where id_custom_table=39");
gks_run_sql("update gks_crm_activity_objects set crm_activity_object_descr='Περιοχές' where id_crm_activity_object=36");
  

$sql="select userAgent from gks_stat_online limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_stat_online` 
  ADD COLUMN `userAgent` text DEFAULT NULL");
}

$sql="select voip_localdb from gks_erp_app limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app` 
  ADD COLUMN `voip_localdb` text DEFAULT NULL,
  add index voip_localdb (voip_localdb(190))");
}


$sql="select * from gks_crons where id_cron=8 limit 1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crons` (`id_cron`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `disable_cron`,`every_seconds`,`fetch_url`,`last_run`,`next_run`,`comments`) VALUES 
  (8,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,21600,  '/my/cron_voip.php',null,null,'')");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS gks_voip_calls (
  `id_voip_call` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  gks_primary_rec tinyint(4) NOT NULL DEFAULT 0,
  erp_app_id int(11) NOT NULL DEFAULT 0,
  gks_user_id int(11) NOT NULL DEFAULT 0,
  gks_event varchar(64) DEFAULT NULL,
  
  AcctId int(11) NOT NULL DEFAULT 0,
  src varchar(64) DEFAULT NULL,
  dst varchar(64) DEFAULT NULL,
  dcontext varchar(64) DEFAULT NULL,
  clid  varchar(190) DEFAULT NULL,
  channel varchar(190) DEFAULT NULL,
  dstchannel varchar(190) DEFAULT NULL,
  lastapp varchar(64) DEFAULT NULL,
  lastdata text DEFAULT NULL,
  start datetime DEFAULT NULL,
  answer datetime DEFAULT NULL,
  end  datetime DEFAULT NULL,
  duration int(11) NOT NULL DEFAULT 0,
  billsec int(11) NOT NULL DEFAULT 0,
  disposition varchar(64) DEFAULT NULL,
  amaflags varchar(64) DEFAULT NULL,
  uniqueid varchar(64) DEFAULT NULL,
  userfield varchar(64) DEFAULT NULL,
  channel_ext varchar(64) DEFAULT NULL,
  dstchannel_ext varchar(64) DEFAULT NULL,
  service varchar(64) DEFAULT NULL,
  caller_name varchar(190) DEFAULT NULL,
  dstanswer varchar(64) DEFAULT NULL,
  recordfiles text DEFAULT NULL,
  session varchar(190) DEFAULT NULL,
  action_owner varchar(64) DEFAULT NULL,
  action_type varchar(64) DEFAULT NULL,
  src_trunk_name varchar(64) DEFAULT NULL,
  dst_trunk_name varchar(64) DEFAULT NULL,
  new_src varchar(64) DEFAULT NULL,
  reason varchar(64) DEFAULT NULL,
  sn varchar(64) DEFAULT NULL,

  PRIMARY KEY (`id_voip_call`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `gks_primary_rec` (`gks_primary_rec`),
  KEY `erp_app_id` (`erp_app_id`),
  KEY `gks_user_id` (`gks_user_id`),
  KEY `gks_event` (`gks_event`),
  
  KEY `AcctId` (`AcctId`),
  KEY `src` (`src`),
  KEY `dst` (`dst`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select voip_ip from gks_erp_app limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app` 
  ADD COLUMN `voip_ip` varchar(64) DEFAULT NULL,
  ADD COLUMN `voip_AIM_port` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `voip_AIM_username` varchar(64) DEFAULT NULL,
  ADD COLUMN `voip_AIM_password` varchar(64) DEFAULT NULL,
  add index voip_ip (voip_ip),
  add index voip_AIM_port (voip_AIM_port),
  add index voip_AIM_username (voip_AIM_username),
  add index voip_AIM_password (voip_AIM_password)");
}

$sql="select voip_call_originate from gks_erp_app limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app` 
  ADD COLUMN `voip_call_originate` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `voip_call_monitoring` tinyint(4) NOT NULL DEFAULT 0,
  add index voip_call_originate (voip_call_originate),
  add index voip_call_monitoring (voip_call_monitoring)");
}


$sql="select * from gks_permission_object where id_permission_object=268";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (268,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_voip_calls','Καταγραφές Τηλεφώνων',4980)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",268,1,1,1,1,1)");
  }

}



gks_run_sql("CREATE TABLE IF NOT EXISTS gks_voip_favorites (
  `id_voip_favorite` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  user_id int(11) NOT NULL DEFAULT 0,
  phone varchar(64) DEFAULT NULL,
  nickname varchar(64) DEFAULT NULL,
  mysortorder int(11) NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`id_voip_favorite`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `user_id` (`user_id`),
  KEY `mysortorder` (`mysortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select product_deductionsSelection from gks_acc_inv_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv_products` 
  ADD COLUMN `product_deductionsSelection` varchar(190) DEFAULT NULL after product_otherTaxesAmount");
}


$sql="select product_price_yperx from gks_eshop_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products`
  ADD COLUMN `product_price_yperx` double DEFAULT NULL AFTER `product_price_quantity_formula`,
  ADD COLUMN `product_price_yperx_sale` double NOT NULL DEFAULT 0 AFTER `product_price_yperx`,
  ADD COLUMN `product_price_yperx_sale_from` datetime DEFAULT NULL AFTER `product_price_yperx_sale`,
  ADD COLUMN `product_price_yperx_sale_to` datetime DEFAULT NULL AFTER `product_price_yperx_sale_from`,
  ADD COLUMN `product_price_yperx_sheets_formula` varchar(255) DEFAULT NULL AFTER `product_price_yperx_sale_to`,
  ADD COLUMN `product_price_yperx_quantity_formula` varchar(255) DEFAULT NULL AFTER `product_price_yperx_sheets_formula`,
  ADD COLUMN `product_price_yperx_include_vat` tinyint(4) NOT NULL DEFAULT 0  AFTER `product_price_include_vat`,
  ADD COLUMN `quantitycheck_price_yperx` double DEFAULT NULL AFTER `quantitycheck_price_retail`");
}


$sql="select * from gks_eshop_pricelist where id_pricelist=3";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO gks_eshop_pricelist 
  (id_pricelist,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  based_pricelist_id,pricelist_descr,pricelist_disable,price_is_xondriki) VALUES 
  (3,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',
  0, 'ΥπερΧονδρικής',0,2)");

  gks_run_sql("ALTER TABLE `gks_eshop_products` 
  MODIFY COLUMN `product_code` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS gks_eshop_products_prices (
  `id_product_price` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pricelist_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `product_price_plist` double DEFAULT NULL,
  `product_price_plist_sale` double NOT NULL DEFAULT 0,
  `product_price_plist_sale_from` datetime DEFAULT NULL,
  `product_price_plist_sale_to` datetime DEFAULT NULL,
  `product_price_plist_sheets_formula` varchar(255) DEFAULT NULL,
  `product_price_plist_quantity_formula` varchar(255) DEFAULT NULL,
  `product_price_plist_include_vat` tinyint(4) NOT NULL DEFAULT 0,
  `quantitycheck_price_plist` double DEFAULT NULL,
  PRIMARY KEY (`id_product_price`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `pricelist_id` (`pricelist_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

  
$sql="select sortorder from gks_eshop_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist`
  ADD COLUMN `sortorder` int(4) NOT NULL DEFAULT 1000,
  ADD KEY `sortorder` (`sortorder`)");
}  

$sql="select * from gks_custom_field_type where id_custom_field_type=1026 and field_type_notdevyet=1";//Εργασία CRM	int(11)
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("update gks_custom_field_type set field_type_notdevyet=0 where id_custom_field_type in (1026,1006)");
}


$sql="select ma_orofos from gks_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos");
}
$sql="select ea_orofos from gks_users_extra_address limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users_extra_address`
  ADD COLUMN `ea_orofos` varchar(128) DEFAULT NULL after ea_arithmos");
}
$sql="select company_orofos from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company`
  ADD COLUMN `company_orofos` varchar(128) DEFAULT NULL after company_arithmos");
  gks_run_sql("ALTER TABLE `gks_company_lang`
  ADD COLUMN `company_orofos` varchar(128) DEFAULT NULL after company_arithmos");
  gks_run_sql("ALTER TABLE `gks_company_subs`
  ADD COLUMN `company_sub_orofos` varchar(128) DEFAULT NULL after company_sub_arithmos");
  gks_run_sql("ALTER TABLE `gks_company_subs_lang`
  ADD COLUMN `company_sub_orofos` varchar(128) DEFAULT NULL after company_sub_arithmos");
  gks_run_sql("ALTER TABLE `gks_warehouses`
  ADD COLUMN `warehouse_orofos` varchar(128) DEFAULT NULL after warehouse_arithmos");
  gks_run_sql("ALTER TABLE `gks_warehouses_lang`
  ADD COLUMN `warehouse_orofos` varchar(128) DEFAULT NULL after warehouse_arithmos");
}
$sql="select hotel_orofos from gks_hotel limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel`
  ADD COLUMN `hotel_orofos` varchar(128) DEFAULT NULL after hotel_arithmos");
  gks_run_sql("ALTER TABLE `gks_transfer`
  ADD COLUMN `transfer_orofos` varchar(128) DEFAULT NULL after transfer_arithmos");
}
$sql="select ma_orofos from gks_hotel_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_hotel_reservation`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos,
  ADD COLUMN `other_ma_orofos` varchar(128) DEFAULT NULL after other_ma_arithmos");
  gks_run_sql("ALTER TABLE `gks_hotel_reservation_room`
  ADD COLUMN `ruser_ma_orofos` varchar(128) DEFAULT NULL after ruser_ma_arithmos");
}
$sql="select ma_orofos from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos,
  ADD COLUMN `other_ma_orofos` varchar(128) DEFAULT NULL after other_ma_arithmos");
  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata`
  ADD COLUMN `ruser_ma_orofos` varchar(128) DEFAULT NULL after ruser_ma_arithmos");
}
$sql="select poi_orofos from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_poi`
  ADD COLUMN `poi_orofos` varchar(128) DEFAULT NULL after poi_arithmos");
}
$sql="select transfer_area_orofos from gks_transfer_area limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer_area`
  ADD COLUMN `transfer_area_orofos` varchar(128) DEFAULT NULL after transfer_area_arithmos");
}
$sql="select orofos from gks_crm_leads limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_crm_leads`
  ADD COLUMN `orofos` varchar(128) DEFAULT NULL after arithmos");
}
$sql="select orofos from gks_crm_tasks limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_crm_tasks`
  ADD COLUMN `orofos` varchar(128) DEFAULT NULL after arithmos");
}
$sql="select ma_orofos from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos,
  ADD COLUMN `destination_data_orofos` varchar(128) DEFAULT NULL after destination_data_arithmos,
  ADD COLUMN `other_ma_orofos` varchar(128) DEFAULT NULL after other_ma_arithmos,
  ADD COLUMN `load_orofos` varchar(128) DEFAULT NULL after load_arithmos,
  ADD COLUMN `deli_orofos` varchar(128) DEFAULT NULL after deli_arithmos");
}
$sql="select entity_orofos from gks_whi_mov_other_entity limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov_other_entity`
  ADD COLUMN `entity_orofos` varchar(128) DEFAULT NULL after entity_arithmos");
}
$sql="select ma_orofos from gks_orders limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos,
  ADD COLUMN `destination_data_orofos` varchar(128) DEFAULT NULL after destination_data_arithmos,
  ADD COLUMN `other_ma_orofos` varchar(128) DEFAULT NULL after other_ma_arithmos");
}  
$sql="select ma_orofos from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  ADD COLUMN `ma_orofos` varchar(128) DEFAULT NULL after ma_arithmos,
  ADD COLUMN `destination_data_orofos` varchar(128) DEFAULT NULL after destination_data_arithmos,
  ADD COLUMN `other_ma_orofos` varchar(128) DEFAULT NULL after other_ma_arithmos,
  ADD COLUMN `load_orofos` varchar(128) DEFAULT NULL after load_arithmos,
  ADD COLUMN `deli_orofos` varchar(128) DEFAULT NULL after deli_arithmos");
}
$sql="select entity_orofos from gks_acc_inv_other_entity limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv_other_entity`
  ADD COLUMN `entity_orofos` varchar(128) DEFAULT NULL after entity_arithmos");
}
$sql="select calendar_orofos from gks_calendar limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_calendar`
  ADD COLUMN `calendar_orofos` varchar(128) DEFAULT NULL after calendar_arithmos");
}

$sql="select mdate_expire from gks_orders limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders`
  ADD COLUMN `mdate_expire` datetime DEFAULT NULL after `mdate_invoice`,
  ADD COLUMN `online_enable` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `online_password` varchar(64) DEFAULT NULL");
}

$sql="select from_online from gks_orders_log limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders_log`
  ADD COLUMN `from_online` tinyint(4) NOT NULL DEFAULT 0,
  ADD KEY `from_online` (from_online)");
}


$sql="select * from gks_notification_type where id_notification_type=4010";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_notification_type (
  id_notification_type,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  notification_type_descr,notification_type_sortorder,notification_type_disabled
  ) values (
  4010,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  'OnLine Προσφορά',4010,0
  )");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_notification_userperm
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_notification_userperm` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,notification_type_id,from_admin,from_user,to_email,to_viber) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",4010,1,1,1,1)");
  }
}

$sql="select * from gks_email_template where id_email_template=101";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`attachments`,`localization_set_id`,`edit_mode`) VALUES 
 (101,'2020-01-01','2020-01-01',2,2,'127.0.0.1','2020-01-01','OnLine Προσφορά','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','OnLine Προσφορά','Αγαπητέ πελάτη,<br /><br />η προσφορά είναι διαθέσιμη OnLine στον παρακάτω σύνδεσμο:<br /><a href=\"[[online_url]]\">[[online_url]]</a><br /><br />Ο κωδικός πρόσβασης είναι:<br /><strong>[[online_pass]]</strong><br /><br />Με εκτίμηση, η ομάδα του<br />[[GKS_SITE_HUMAN_NAME]]',0,1000,'el-GR',0,'[\n    {\n        \"type\": \"text\",\n        \"label\": \"online_url\",\n        \"id\": \"email_param_online_url\",\n        \"px\": \"https://test.gks.gr/offers/?guid=xxxxxxx\",\n        \"jquery_selector\": \"#online_url\"\n    },\n    {\n        \"type\": \"text\",\n        \"label\": \"online_pass\",\n        \"id\": \"email_param_online_pass\",\n        \"px\": \"123456\",\n        \"jquery_selector\": \"#online_password\"\n    }\n]','',1196061,'html');
");

  gks_run_sql("insert into gks_email_template_object_forms (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  email_template_id,email_template_object_id
  ) values (
  '2020-01-01','2020-01-01',2,2,'127.0.0.1',
  101,1
  )");
  
}




$sql="select product_is_optional from gks_orders_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders_products`
  ADD COLUMN `product_is_optional` tinyint(4) NOT NULL DEFAULT 0,
  ADD KEY `product_is_optional` (product_is_optional)");
  //0-> metraei sto sinolo - to default
  //1-> mporei na to prosuesei o pelatis
  //2-> to prosthese o pelatis
}


$sql="select eidoi_optional from gks_print_forms limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_print_forms`
  ADD COLUMN `eidoi_optional` longtext DEFAULT NULL after lots_and_serials_analysis,
  ADD COLUMN `custom_css` longtext DEFAULT NULL,
  ADD COLUMN `custom_javascript` longtext DEFAULT NULL");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_template_html` (
  `id_template_html` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `template_html_descr` varchar(190) DEFAULT NULL,
  `template_html_type` int(11) NOT NULL DEFAULT 0,
  `is_disable` tinyint(4) NOT NULL DEFAULT 0,
  `sortorder` int(11) NOT NULL DEFAULT 1000,
  `gks_lang` varchar(8) NOT NULL DEFAULT 'el-GR',
  `localization_set_id` int(11) NOT NULL DEFAULT 0,
  `edit_mode` varchar(16) DEFAULT NULL,
  `orders_online_url` varchar(190) DEFAULT NULL,
  `orders_online_sms_sender` varchar(190) DEFAULT NULL,
  `html_part_1` longtext DEFAULT NULL,
  `html_part_2` longtext DEFAULT NULL,
  `html_part_3` longtext DEFAULT NULL,
  `html_part_4` longtext DEFAULT NULL,
  `html_part_5` longtext DEFAULT NULL,
  `html_part_6` longtext DEFAULT NULL,
  `html_part_7` longtext DEFAULT NULL,
  `html_part_8` longtext DEFAULT NULL,
  `html_part_9` longtext DEFAULT NULL,
  `custom_css` longtext DEFAULT NULL,
  `custom_javascript` longtext DEFAULT NULL,
  PRIMARY KEY (`id_template_html`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `template_html_descr` (`template_html_descr`),
  KEY `template_html_type` (`template_html_type`),
  KEY `is_disable` (`is_disable`),
  KEY `sortorder` (`sortorder`),
  KEY `gks_lang` (`gks_lang`),
  KEY `localization_set_id` (`localization_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_template_html_photo` (
  `id_template_html_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_html_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  `public_expire_date` datetime DEFAULT NULL,
  `public_shortcode` varchar(64) DEFAULT NULL,
  `public_myopencount` int(11) NOT NULL DEFAULT 0,
  `descr` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_template_html_photo`),
  KEY `template_html_id` (`template_html_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_template_html_type` (
  `id_template_html_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `template_html_type_descr` varchar(190) DEFAULT NULL,
  `type_is_disable` tinyint(4) NOT NULL DEFAULT 0,
  `type_sortorder` int(11) NOT NULL DEFAULT 1000,
  `html_part_1_title` varchar(190) DEFAULT NULL,
  `html_part_2_title` varchar(190) DEFAULT NULL,
  `html_part_3_title` varchar(190) DEFAULT NULL,
  `html_part_4_title` varchar(190) DEFAULT NULL,
  `html_part_5_title` varchar(190) DEFAULT NULL,
  `html_part_6_title` varchar(190) DEFAULT NULL,
  `html_part_7_title` varchar(190) DEFAULT NULL,
  `html_part_8_title` varchar(190) DEFAULT NULL,
  `html_part_9_title` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_template_html_type`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `template_html_type_descr` (`template_html_type_descr`),
  KEY `type_is_disable` (`type_is_disable`),
  KEY `type_sortorder` (`type_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select * from gks_template_html_type where id_template_html_type=1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_template_html_type` (`id_template_html_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `template_html_type_descr`,type_is_disable,type_sortorder,
  html_part_1_title,html_part_2_title,html_part_3_title,html_part_4_title,html_part_5_title,
  html_part_6_title,html_part_7_title,html_part_8_title,html_part_9_title
  ) VALUES 
 (1,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
 'OnLine Προσφορά',0,1,
 'Κωδικός πρόσβασης',
 'Βασικό',
 'Πίνακας ειδών',
 'Σύνολα',
 'Κουμπιά',
 'Μήνυμα',
 '',
 '',
 '')");
}


$sql="select * from gks_template_html where id_template_html=1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  $orders_online_url='';
  $sql="SELECT id_eshop, eshop_url
  FROM gks_eshops
  WHERE eshop_disable=0 and eshop_url<>''
  ORDER by eshop_sortorder limit 1";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) {
    $orders_online_url=$row['eshop_url'].'/offers/';
  }

  $orders_online_sms_sender='';
  $sql="SELECT gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name, 
  gks_erp_app_mobile.erp_app_mobile_phonenumber, gks_erp_app_mobile_ping.mydate
  FROM gks_erp_app_mobile 
  LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
  WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
  and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
  and gks_erp_app_mobile_ping.mydate is not null
  ORDER BY gks_erp_app_mobile_ping.mydate desc,gks_erp_app_mobile.erp_app_mobile_sortorder";
  $result = gks_run_sql($sql);        
  while ($row = $result->fetch_assoc()) {
    if (empty($row['mydate'])==false and strtotime($row['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
      $orders_online_sms_sender='gks_erp_app_mobile:'.$row['id_erp_app_mobile'];
      break;
    }            
  }
  if ($orders_online_sms_sender=='') {
    $parts=explode(',',$GKS_SMS_SENDER);
    foreach ($parts as $value) {
      $value=trim_gks($value);
      if ($value!='') {
        $orders_online_sms_sender='smsapi:'.$value;
        break;
      }
    }
  }
  
  gks_run_sql("INSERT INTO `gks_template_html` (`id_template_html`,
  `mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,
  `template_html_descr`,`template_html_type`,`is_disable`,`sortorder`,
  `gks_lang`,`localization_set_id`,`edit_mode`,
  `orders_online_url`,`orders_online_sms_sender`,
  `html_part_1`,
  `html_part_2`,
  `html_part_3`,
  `html_part_4`,
  `html_part_5`,
  `html_part_6`,
  `html_part_7`,
  `html_part_8`,
  `html_part_9`,
  `custom_css`,
  `custom_javascript`
  ) VALUES 
 (1,'2021-01-01','2021-01-01',1,1,'127.0.0.1','2021-01-01',
 'Πρότυπο OnLine Προσφορά',1,0,1,
 'el-GR',1234567,'raw',
 '".$db_link->escape_string($orders_online_url)."',
 '".$db_link->escape_string($orders_online_sms_sender)."',

'".$db_link->escape_string('<link rel="preload" href="/wp-content/plugins/gks_core/img/progress_bar.gif" as="image" type="image/gif" />
<audio id="audioping" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/wp-content/plugins/gks_core/mp3/notif1.mp3" type="audio/mpeg"></audio> 
<div class="gks_erp_sales_order_online">
  <img src="/wp-content/plugins/gks_core/img/progress_bar.gif" style="display:none;"/>
  <input type="hidden" value="[[guid]]" id="gks_erp_sales_order_online_guid">
  <div class="gks_erp_sales_order_online_needpass">
    <div class="gks_erp_sales_order_online_needpass_title">
      Κωδικός πρόσβασης
    </div>
    <div class="gks_erp_sales_order_online_needpass_1">
      <input type="text" id="order_online_code" autocompete="off">
    </div>
    <div class="gks_erp_sales_order_online_needpass_2">
      <span id="button_needpass_check" class="gks_noselect"><i class="fas fa-check"></i> Καταχώρηση</span> 
    </div>
    <div id="button_needpass_check_response" class="gks_noselect" data-run="0" style="display:none;">
    </div> 
  </div>
</div>')."',
'".$db_link->escape_string('<link rel="preload" href="/wp-content/plugins/gks_core/img/progress_bar.gif" as="image" type="image/gif" />
<audio id="audioping" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/wp-content/plugins/gks_core/mp3/notif1.mp3" type="audio/mpeg"></audio>
<div class="gks_erp_sales_order_online">
  <img src="/wp-content/plugins/gks_core/img/progress_bar.gif" style="display:none;"/>
  <input type="hidden" value="[[guid]]" id="gks_erp_sales_order_online_guid"/>
  <div class="gks_erp_sales_order_online_col1">
    <div class="gks_erp_sales_order_online_col1_1">
      <div class="gks_price_total">[[sum]]</div>
    </div>
    <div class="gks_erp_sales_order_online_col1_2">
      <div class="gks_erp_sales_order_online_order_title3">
        Κατάσταση
      </div>
      <div class="gks_erp_sales_order_online_order_status">
        [[order_status]]
      </div>
      <div class="gks_erp_sales_order_online_order_expire" style="[[order_expire_display]]">
        <div>Λήξη στις: [[order_expire]]</div>
      </div>
    </div>
    <div class="gks_erp_sales_order_online_col1_3" style="[[contact_display]]">
      <div class="gks_erp_sales_order_online_order_title3">
        Για την εταιρεία
      </div>
      <div class="gks_erp_sales_order_online_salesman">
        <div class="gks_erp_sales_order_online_salesman_image">[[contact_image]]</div>
        <div class="gks_erp_sales_order_online_salesman_name" >[[contact]]</div>
        <div class="gks_erp_sales_order_online_salesman_title">[[contact_title]]</div>
        <div class="gks_erp_sales_order_online_salesman_phone">[[contact_phone]]</div>
        <div class="gks_erp_sales_order_online_salesman_email">[[contact_email]]</div>
      </div>
    </div>
  </div>
  <div class="gks_erp_sales_order_online_col2">
    <div class="gks_erp_sales_order_online_order_title">
      Προσφορά #<span>[[order_title]]</span>
    </div>
    <div class="gks_erp_sales_order_online_col2_1">
      <div class="gks_erp_sales_order_online_col2_1_1">
        <div class="gks_erp_sales_order_online_order_title2">
          Εταιρεία
        </div>
        <div class="gks_erp_sales_order_online_order_company_dataa">
          [[company_data]]
        </div>
      </div>
      <div class="gks_erp_sales_order_online_col2_1_2">
        <div class="gks_erp_sales_order_online_order_title2">
          Πελάτης
        </div>
        <div class="gks_erp_sales_order_online_order_user_data">
          [[user_data]]
        </div>
      </div>
    </div>
    <div class="gks_erp_sales_order_online_col2_2">
      <div class="gks_erp_sales_order_online_order_title2">
        Προϊόντα / Υπηρεσίες
      </div>
      <div class="gks_erp_sales_order_online_order_subtitle">
        Μπορείτε να αφαιρέσετε προϊόντα από την προσφορά με το κουμπί
        <i class="product_is_optional_icon_remove fas fa-minus-circle"></i>
        <br>Τα προϊόντα με το
        <i data-recid="0" data-run="0" class="product_is_optional_icon_fix fas fa-check-circle"></i>
        δεν μπορούν να αφαιρεθούν από την προσφορά.
      </div>
      <div class="gks_erp_sales_order_online_order_products table-2">
        [[products]]
      </div>
    </div>
    <div class="gks_erp_sales_order_online_col2_3">
      <div class="gks_erp_sales_order_online_order_totals">
        [[totals]]
      </div>
    </div>
    <div class="gks_erp_sales_order_online_col2_4" style="[[products_optional_display]]">
      <div class="gks_erp_sales_order_online_order_title2">
        Συνδυαστικά Προϊόντα &amp; Υπηρεσίες
      </div>
      <div class="gks_erp_sales_order_online_order_subtitle">
        Μπορείτε να προσθέσετε προϊόντα στην προσφορά με το κουμπί
        <i class="product_is_optional_icon_add fas fa-plus-circle" title="Προσθήκη"></i>
      </div>
      <div class="gks_erp_sales_order_online_order_products table-2">
        [[products_optional]]
      </div>
    </div>
    <div class="gks_erp_sales_order_online_col2_5" style="[[sxolio_display]]">
      <div class="gks_erp_sales_order_online_order_title2">
        Σχόλια Προσφοράς
      </div>
      <div class="gks_erp_sales_order_online_order_sxolio">
        [[sxolio]]
      </div>
    </div>
    [[buttons]]
    <div class="gks_erp_sales_order_online_col2_9">
      <div class="gks_erp_sales_order_online_order_title2">
        Συζήτηση Online
      </div>
      <div class="gks_erp_sales_order_online_order_messages">
        [[messages]]
      </div>
      <div class="gks_erp_sales_order_online_order_new_message">
        <div class="gks_erp_sales_order_online_order_new_message_title">Νέο μήνυμα</div>
        <div class="gks_erp_sales_order_online_order_new_message_textarea">
          <textarea id="gks_erp_sales_order_online_order_new_message_textarea_obj"></textarea>
        </div>
        <div class="gks_erp_sales_order_online_order_new_message_button">
          <span id="button_message_send" class="gks_noselect" data-run="0"><i class="fas fa-paper-plane"></i> Αποστολή</span>
        </div>
        <div class="gks_erp_sales_order_online_order_new_message_apantisi" style="[[new_message_apantisi_display]]">
          [[apantisi_os]]
        </div>
        <div id="message_send_response" style="display:none;">
        </div>
      </div>
    </div>
  </div>
</div>')."',
'".$db_link->escape_string('<table class="table_products table table-sm table-responsive table-striped table-bordered gkstable100">
  <thead>
    <tr class="product_th">
      <th class="product_th_aa">#</th>
      <th class="product_th_img">Φωτο</th>
      <th class="product_th_descr">Περιγραφή</th>
      <th class="product_th_quantity">Τεμ<span>άχια</span></th>
      <th class="product_th_itemprice">Τιμή</th>
      <th class="product_th_fpa">ΦΠΑ</th>
      <th class="product_th_total">Ποσό</th>
    </tr>
  </thead>
  <tbody>
    <tr class="product_row">
      <td class="product_row_aa">
        [[aa]]
        <br>
        <i data-recid=[[id_order_product]]" data-run="0" class="product_is_optional_icon_[[product_is_optional_icon]] fas [[product_is_optional_icon_fa]]" title="[[product_is_optional_icon_title]]"></i>
      </td>
      <td class="product_row_img">
        <a class="gks_photo_link" tabIndex="-1" href="[[woo_id_purl]]" data-sub-html="[[product_code]]" target="_blank"><img class="gks_img" src="[[thump_url]]"/></a>
      </td>
      <td class="product_row_descr">
        <div>[[product_descr]]</div>
        <small>[[product_comments]]</small>
      </td>
      <td class="product_row_quantity">
        [[product_quantity]]
      </td>
      <td class="product_row_itemprice">
        [[product_price_final_all_net]]
      </td>
      <td class="product_row_fpa">
        [[product_price_final_all_fpa]]
      </td>
      <td class="product_row_total">
        [[product_price_final_all_total]]
      </td>
    </tr>
  </tbody>
</table>
<div id="product_optional_[[add_remove]]_response" style="display:none;"></div>')."',
'".$db_link->escape_string('<table class="table_totals table table-sm table-responsive table-striped table-bordered gkstable100">
  <tbody>
    <tr class="mytr myss1" style="[[gks_price_net_hidezero]]">
      <td class="mytdtitle">Υποσύνολο</td>
      <td class="mytdnumber">[[gks_price_net]]</td>
    </tr>
    <tr class="mytr myss2" style="[[gks_price_fpa_hidezero]]">
      <td class="mytdtitle">ΦΠΑ</td>
      <td class="mytdnumber">[[gks_price_fpa]]</td>
    </tr>
    <tr class="mytr myss3" style="[[gks_price_netfpa_hidezero]]">
      <td class="mytdtitle">Μικτό σύνολο</td>
      <td class="mytdnumber">[[gks_price_netfpa]]</td>
    </tr>
    <tr class="mytr myss4" style="[[totalWithheldAmount_hidezero]]">
      <td class="mytdtitle">Φόροι Παρακρατούμενοι</td>
      <td class="mytdnumber">[[totalWithheldAmount]]</td>
    </tr>
    <tr class="mytr myss5" style="[[totalOtherTaxesAmount_hidezero]]">
      <td class="mytdtitle">Λοιποί Φόροι</td>
      <td class="mytdnumber">[[totalOtherTaxesAmount]]</td>
    </tr>
    <tr class="mytr myss6" style="[[totalStampDutyamount_hidezero]]">
      <td class="mytdtitle">Ψηφιακό Τέλος συναλλαγής</td>
      <td class="mytdnumber">[[totalStampDutyamount]]</td>
    </tr>
    <tr class="mytr myss7" style="[[totalFeesAmount_hidezero]]">
      <td class="mytdtitle">Τέλη</td>
      <td class="mytdnumber">[[totalFeesAmount]]</td>
    </tr>
    <tr class="mytr myss8">
      <td class="mytdtitle">Σύνολο</td>
      <td class="mytdnumber" data-ptd="[[pts]]" data-tt="[[from_eidi_products_total]]">[[gks_price_total]]</td>
    </tr>
  </tbody>
</table>')."',
'".$db_link->escape_string('<div class="gks_erp_sales_order_online_col2_6" id="gks_erp_sales_order_online_buttons_div" style="[[buttons_display]]">
  <div id="gks_erp_sales_order_buttons_up">
    Μπορείτε να αποδεχτείτε την προσφορά εντός 30 ημερών.
  </div>
  <div id="gks_erp_sales_order_online_buttons">
    <span id="button_accept" class="gks_noselect"><i class="fas fa-check"></i> Αποδοχή</span> 
    <span id="button_reject" data-run="0" class="gks_noselect"><i class="fas fa-times"></i> Απόρριψη</span>
  </div>
  <div id="gks_erp_sales_order_buttons_down">
    <i class="fas fa-check"></i> Χωρίς δέσμευση
    •
    <i class="fas fa-check"></i> Γρήγορη διαδικασία
  </div>
  <div id="gks_erp_sales_order_online_buttons_signature" style="display:none;">
    Υπογράψτε την προσφορά και πατήστε το κουμπί <b>Αποστολή</b>
  </div>
  <div id="button_response" style="display:none;"></div>
</div>
<div class="gks_erp_sales_order_online_col2_7" id="gks_signature_div" style="display:none">
  <div class="gks_erp_sales_order_online_order_title2">
    Υπογραφή
  </div>
  <div class="gks_signature_card" id="gks_signature_card_div">
  </div>
</div>
<div class="gks_erp_sales_order_online_col2_8" id="gks_erp_sales_order_online_buttons2_div" style="display:none;">
  <div id="gks_erp_sales_order_online_buttons2">
    <span id="button_accept2" data-run="0" class="gks_noselect"><i class="fas fa-check"></i> Αποστολή</span>
  </div>
  <div id="button_response2" style="display:none;"></div>
</div>')."',
'".$db_link->escape_string('<div class="gks_erp_sales_order_online_message" data_rec_id="[[id_gks_orders_log]]">
  <div class="gks_cc1">[[aa]]</div>
  <div class="gks_cc2">[[add_date]]</div>
  <div class="gks_cc3">[[display_name]]</div>
  <div class="gks_cc4">[[sxolio]]</div>
</div>')."',
'',
'',
'',
'".$db_link->escape_string('.gks_erp_sales_order_online {
  display: flex;
  flex-direction: row; 
  align-items:flex-start;
}
.gks_erp_sales_order_online_col1 {
  flex: 0 0 300px;
  padding: 0px 20px 0px 0px;
}
.gks_erp_sales_order_online_col2 {
  flex-grow: 1;
  padding: 0px 0px 0px 20px;
  border-left: 1px solid lightgray;
  width:calc(100% - 300px);
}
.gks_erp_sales_order_online_col2_1 {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}
.gks_erp_sales_order_online_col2_1_1 {
  flex: 1 0 50%;
}
.gks_erp_sales_order_online_col2_1_2 {
  flex: 1 0 50%;
}
.gks_erp_sales_order_online_order_title {
  font-size: 40px;
  font-weight: normal;
  text-align: left;   
}
.gks_erp_sales_order_online_order_title2 {
  font-size: 26px;
  font-weight: normal;
  text-align: left; 
  border-bottom: 1px solid lightgray; 
}
.gks_erp_sales_order_online_order_title3 {
  font-size: 26px;
  font-weight: normal;
  text-align: left; 
  border-bottom: 1px solid lightgray; 
  margin-bottom: 10px;
}
.gks_erp_sales_order_online_order_subtitle {
  font-size: 16px;
  font-weight: normal;
  text-align: left; 
  margin: 20px 0px;
}


.gks_erp_sales_order_online_col1_2,
.gks_erp_sales_order_online_col1_3 {
  margin-top: 20px;
  padding-top: 20px;  
  border-top: 1px solid lightgray;
}


.gks_erp_sales_order_online_col2_2,
.gks_erp_sales_order_online_col2_4,
.gks_erp_sales_order_online_col2_5,
.gks_erp_sales_order_online_col2_6,
.gks_erp_sales_order_online_col2_7,
.gks_erp_sales_order_online_col2_8,
.gks_erp_sales_order_online_col2_9 {
  margin-top: 20px;
  padding-top: 20px;  
  border-top: 1px solid lightgray;
}
.gks_erp_sales_order_online_col2_3 {

}
#gks_erp_sales_order_online_buttons_div {
  text-align:center;  
}
#gks_erp_sales_order_online_buttons2_div {
  text-align:center;  
}
#gks_erp_sales_order_online_buttons,#gks_erp_sales_order_online_buttons2 {
  padding: 12px;
}
#gks_erp_sales_order_online_buttons_signature {
  padding: 15px;
  background-color: #ffffaa;
  border-radius: 20px;  
}
.gks_erp_sales_order_online_order_title span {
  font-style: italic;
}
.gks_erp_sales_order_online .gks_price_total {
  font-size: 40px;
  font-weight: bold;
  text-align: center;    
}
.product_th {
  
}
.product_row {
  
}
.product_th_aa, .product_row_aa {
  text-align:center;
}
.product_th_img, .product_row_img {
  
}
.product_th_quantity, .product_row_quantity {
  text-align:center;
}
.product_th_itemprice, .product_row_itemprice,
.product_th_fpa, .product_row_fpa,
.product_th_total, .product_row_total {
  text-align:right;
}
.gks_erp_sales_order_online .product_row_img .gks_img {
  max-width:64px;
}
.gks_erp_sales_order_online_order_totals {
  display: flex;
  flex-direction: row;
  justify-content1: end;
}
.table_totals {
  width: 10%;
}
.table_totals .mytdtitle {
  text-wrap-mode: nowrap;
  text-align: right;
}
.table_totals .mytdnumber {
  text-wrap-mode: nowrap;
  text-align: right;
}
.myss8 {
  font-weight:bold;
}
.gks_erp_sales_order_online_salesman .contact_image {
  width:32px;
  height:32px;
  background-size: cover;
  background-position: 50% 50%;
  border-radius: 50%;    
  
}
.order_state_025offer           {border-radius: 10px; background: #5bc0de; padding:0px 10px 0px 10px; border: 1px solid #4896ad; color:#ffffff;white-space: nowrap;}
.order_state_040cancelled       {border-radius: 10px; background: #ff0000; padding:0px 10px 0px 10px; border: 1px solid #c30000; color:#ffffff;white-space: nowrap;}
.order_state_050rejected        {border-radius: 10px; background: #d2322d; padding:0px 10px 0px 10px; border: 1px solid #962420; color:#ffffff;white-space: nowrap;}
.order_state_055wait_payment    {border-radius: 10px; background: #518df1; padding:0px 10px 0px 10px; border: 1px solid #000000; color:#ffffff;white-space: nowrap;}
.order_state_060registered      {border-radius: 10px; background: #337AB7; padding:0px 10px 0px 10px; border: 1px solid #245580; color:#ffffff;white-space: nowrap;}

.gks_noselect {
  -webkit-touch-callout: none;
    -webkit-user-select: none;
     -khtml-user-select: none;
       -moz-user-select: none;
        -ms-user-select: none;
            user-select: none;
}

#button_accept, #button_accept2, #button_reject, #button_message_send, #button_needpass_check {
  cursor:pointer;
  color:white;
  padding:10px 20px;
  margin:4px 10px;
  border-radius:20px;
  font-size: 120%;
  font-weight: bold;
  border:2px solid transparent;  
  white-space: nowrap;
  text-wrap-mode: nowrap;
  transition: color 0.25s ease-in-out, background-color 0.25s ease-in-out, border-color 0.25s ease-in-out, box-shadow 0.25s ease-in-out;  
}
#button_accept, #button_accept2, #button_needpass_check {
  background-color:#68bd46;
}
#button_reject {
  background-color:#ee4837;
}
#button_message_send {
  background-color:#007bff;
}

#button_accept:hover, #button_accept2:hover, #button_needpass_check:hover {
  background-color:#4a8732;
  border:2px solid #386726;
}
#button_reject:hover {
  background-color:#ab3428;
  border:2px solid #85291f;
}
#button_message_send:hover {
  background-color:#0056b3;
}

#button_accept i, #button_accept2 i, #button_reject i, #button_message_send i, #button_needpass_check i {
  margin-right:10px;
}
#button_response {
  
}
#button_response div img {
  margin:24px 10px 18px 10px;
}
.button_response_error {
  background-color: #ee9d94;
  margin: 5px 20px 25px 20px;
  border-radius: 10px;
  padding: 10px;
  color: black;
}
.button_response_ok {
  background-color: #b1ffaf;
  margin: 5px 20px 25px 20px;
  border-radius: 10px;
  padding: 10px;
  color: black;
}
.gks_erp_sales_order_online_order_messages {
  font-size: 14px;
  line-height: 1.4;
}
.gks_erp_sales_order_online_message {
  display: flex;
  flex-direction: row;  
  gap: 10px 10px;
  align-items: center;
  padding: 20px 10px;
  border-bottom: 1px solid lightgray;
      
}
.gks_erp_sales_order_online_message .gks_cc1 {
  flex: 0 0 50px;
  text-align: center;
}
.gks_erp_sales_order_online_message .gks_cc2 {
  flex: 0 0 100px;
  text-align: center;
}
.gks_erp_sales_order_online_message .gks_cc3 {
  flex: 1 0;
}
.gks_erp_sales_order_online_message .gks_cc4 {
  flex: 3 0;
}
.gks_erp_sales_order_online_message:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}
.gks_erp_sales_order_online_order_new_message {
  margin-top: 40px;
}
.gks_erp_sales_order_online_order_new_message_title {
  font-weight: bold;
  font-size: 18px;
}
.gks_erp_sales_order_online_order_new_message_textarea {
  margin-top:20px;
  margin-bottom:20px;
}
#gks_erp_sales_order_online_order_new_message_textarea_obj {
  width: 100%;
  min-height: 150px;
  font-size: 16px;
  padding: 10px;
  border: 1px solid lightgray;
  border-radius: 20px;  
}
#gks_erp_sales_order_online_order_new_message_textarea_obj:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  transition: box-shadow 0.25s ease-in-out;
}

.gks_erp_sales_order_online_order_new_message_button {
  padding: 8px;
  text-align: center;
}
#message_send_response {
  text-align:center;
}



.gks_erp_sales_order_online_needpass {
    width: 400px;
    max-width: 100%;
    margin: auto; 
    text-align: center; 
}
.gks_erp_sales_order_online_needpass_title {
  font-size: 26px;
  font-weight: normal;
  text-align: center;
  border-bottom: 1px solid lightgray;  
}
#order_online_code {
  width: 100%;
  max-width:200px;
  font-size: 16px;
  padding: 10px;
  border: 1px solid lightgray;
  border-radius: 20px; 
  min-height: 51px; 
}
#order_online_code:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  transition: box-shadow 0.25s ease-in-out;
}
.gks_erp_sales_order_online_needpass_1 {
  margin: 20px 10px;
}
.gks_erp_sales_order_online_needpass_2 {
  line-height: 60px;
}
#button_needpass_check_response {
  text-align:center;
  margin-top:20px;
}
.product_is_optional_icon_fix {
  font-size:150%;
  color:black;
  vertical-align: middle;
}
.product_is_optional_icon_add {
  cursor:pointer;
  font-size:150%;
  color:green;
  vertical-align: middle;
}
.product_is_optional_icon_remove {
  cursor:pointer;
  font-size:150%;
  color:red;  
  vertical-align: middle;
}

.gks_erp_sales_order_online_order_subtitle > i.product_is_optional_icon_add,
.gks_erp_sales_order_online_order_subtitle > i.product_is_optional_icon_remove {
 cursor:unset;
}

.gks_erp_sales_order_online_order_new_message_apantisi {
  text-align:center;
  font-size:12px;
}
  
@media only screen and (max-width: 992px) {
  .gks_erp_sales_order_online {
    flex-direction: column;
  }
  .gks_erp_sales_order_online_col1 {
    flex: 100%;
    width: 100%;
  }
  .gks_erp_sales_order_online_col2 {
    border-left:unset;
    padding: 0px 0px 0px 0px; 
    margin-top: 50px; 
    width: 100%;
  }
  .gks_erp_sales_order_online_col2_1 {
    flex-direction: column;
  }
  .gks_erp_sales_order_online_col2_1_1 {
    flex: 100%;
    width: 100%;
  }
  .gks_erp_sales_order_online_col2_1_2 {
    flex: 100%;
    width: 100%;
    margin-top: 40px;
  }

  .gks_erp_sales_order_online_message {
    flex-wrap: wrap;  
  }
  .gks_erp_sales_order_online_message .gks_cc2 {
    flex: 0 0 180px;
  }
  .gks_erp_sales_order_online_message .gks_cc3 {
    flex: 1 0 auto;
  }
  .gks_erp_sales_order_online_message .gks_cc4 {
    flex: 1 0 100%;
  }
  table.table_products>thead>tr>th,
  table.table_products>tbody>tr>td {
    white-space: wrap;
    padding:4px;
    font-size:80%;
    vertical-align: middle;
  }
  .gks_erp_sales_order_online .product_row_img .gks_img {
    max-width: 32px;
  }
  #gks_erp_sales_order_online_buttons, #gks_erp_sales_order_online_buttons2 {
    line-height: 70px;
  }
}
@media only screen and (max-width: 576px) {
  
  /* table flex */
  .product_th {
    line-break: anywhere;
  }
  .product_th th {
    max-height: 40px;
  }
  .product_th, .product_row {
    display: flex;
    gap: 0px;
    flex-direction: row;
    flex-wrap:wrap;
    border-bottom: 1px solid lightgray !important;
  }
  .product_th th, .product_row td {
    border: 1px solid transparent !important;
    padding:10px 10px !important;
  }
  
  .product_th_aa, .product_row_aa {
    width: 50px;
    flex: 0 0 auto;
    
  }
  .product_th_img, .product_row_img {
    width: 60px;
    flex: 0 0 auto;
  }
  .product_th_descr, .product_row_descr {
    width:calc(100% - 110px);
  
    flex: 1 1 auto;
  }
  .product_th_quantity, .product_row_quantity {
    flex: 0 0 50px;
  }
  .product_th_itemprice, .product_row_itemprice {
    width1: 100px;
    flex: 1 1 20%;
  }
  .product_th_fpa, .product_row_fpa {
    width1: 30px;
    flex: 1 1 25%;
  }
  .product_th_total, .product_row_total {
    width1: 30px;
    flex: 1 1 25%;
  } 
  .product_th_quantity, .product_th_itemprice, .product_th_fpa, .product_th_total { 
    background-color:rgba(0,0,0,0.2);
    overflow: hidden;
  }
  .product_row_quantity, .product_row_itemprice, .product_row_fpa, .product_row_total { 
    background-color:#eeeeee;
  }
  .product_th_quantity > span {
    display:none;  
  }
  .table_products thead {
    border: 0px !important;
  }
}

#button_reject:hover {
    background-color: #5A6268 !important;
    border: 2px solid #5A6268 !important;
}
#gks_erp_sales_order_buttons_up {
  margin-bottom:20px;  
}
#gks_erp_sales_order_buttons_down {
  margin-top:20px; 
}
#gks_erp_sales_order_buttons_down > i {
  color: #00ab4e;
  font-size:120%;
  
}

/*signature  signature  signature  signature  signature  signature  signature*/
.gks_signature_card {
  background: #fff;
  width: 100%;
  margin-top: 10px;
  border1: 1px solid #e0dfd8;
  border-radius1: 14px;
  padding1: 1.5rem;
}
/* -- Tabs -- */
.gks_signature_tabs {
  display: flex;
  border: 1px solid #e0dfd8;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 14px;
}
.gks_signature_tab-btn {
  flex: 1;
  font-size: 13px;
  font-weight: 500;
  padding: 9px;
  border: none;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: background .15s, color .15s;
}
.gks_signature_tab-btn.gks_signature_active {
  background: #fff;
  color: #1a1a1a;
  border-bottom-color: #1a1a1a;
}
.gks_signature_tab-btn:not(.gks_signature_active) {
  background: #f5f5f3;
  color: #888;
}
/* -- Checkerboard (διαφάνεια) -- */
.gks_signature_checker {
  background-image:
    linear-gradient(45deg,  #ccc 25%, transparent 25%),
    linear-gradient(-45deg, #ccc 25%, transparent 25%),
    linear-gradient(45deg,  transparent 75%, #ccc 75%),
    linear-gradient(-45deg, transparent 75%, #ccc 75%);
  background-size: 16px 16px;
  background-position: 0 0, 0 8px, 8px -8px, -8px 0;
  background-color: #fff;
}    
/* -- Canvas -- */
.gks_signature_canvas-wrap {
  border: 1.5px solid #ccc;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
}
#gks_signature_sigCanvas {
  display: block;
  width: 100%;
  height: 200px;
  cursor: crosshair;
  touch-action: none;
}

    
#gks_signature_hint {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  font-size: 13px;
  color: #888;
  pointer-events: none;
  user-select: none;
  text-shadow: 0 0 6px #fff;
  white-space: nowrap;
}

/* -- Draw controls -- */
.gks_signature_draw-controls {
  display: flex;
  gap: 10px;
  margin-top: 10px;
  align-items: center;
  flex-wrap: wrap;
}
.gks_signature_ctrl-label {
  font-size: 13px;
  color: #555;
}
.gks_signature_draw-controls input[type="range"] { width: 80px; }
.gks_signature_draw-controls input[type="color"] {
  width: 32px; height: 32px;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  padding: 2px;
  background: none;
}
.gks_signature_size-out { font-size: 13px; color: #555; min-width: 16px; }

/* ── Drop zone ── */
#gks_signature_dropZone {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 200px;
  border: 1.5px dashed #bbb;
  border-radius: 10px;
  cursor: pointer;
  background: #f9f9f7;
  transition: border-color .15s, background .15s;
}
#gks_signature_dropZone:hover,
#gks_signature_dropZone.drag-over {
  border-color: #777;
  background: #f0f0ec;
}
.gks_signature_dz-icon { font-size: 30px; line-height: 1; display: block; }
.gks_signature_dz-text { font-size: 13px; color: #555; display: block; }
.gks_signature_dz-sub  { font-size: 12px; color: #999; display: block; }



/* ── Buttons ── */
button {
  font-size: 13px;
  padding: 7px 16px;
  border-radius: 8px;
  border: 1px solid #d0cfc8;
  background: transparent;
  color: #1a1a1a;
  cursor: pointer;
  transition: background .12s;
}
button:hover { background: #f0f0ec; }
button.gks_signature_primary { font-weight: 500; }')."',
'".$db_link->escape_string('/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
var max_id_gks_orders_log=0;
jQuery(document).ready(function($) {
  
  function mypostdata(cmd,mydata) {
    guid=$(\'#gks_erp_sales_order_online_guid\').val();
    if (cmd==\'reject\') {
      $(\'#button_reject\').attr(\'data-run\',\'1\');
      $(\'#button_response\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
      $(\'#gks_erp_sales_order_online_buttons\').hide();
    }
    if (cmd==\'accept\') {
      $(\'#button_accept2\').attr(\'data-run\',\'1\');
      $(\'#button_response2\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
      $(\'#gks_erp_sales_order_online_buttons2\').hide();
    }
    if (cmd==\'message_send\') {
      $(\'#button_message_send\').attr(\'data-run\',\'1\');
      $(\'#message_send_response\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
      clearTimeout(timer_get_log_obj);
    }
    if (cmd==\'pass_check\') {
      $(\'#button_needpass_check\').attr(\'data-run\',\'1\');
      $(\'#button_needpass_check_response\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
    }
    if (cmd==\'product_optional_add\') {
      $(\'.product_is_optional_icon_add[data-recid="\'+mydata+\'"]\').attr(\'data-run\',\'1\');
      $(\'#product_optional_add_response\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
    }
    if (cmd==\'product_optional_remove\') {
      $(\'.product_is_optional_icon_remove[data-recid="\'+mydata+\'"]\').attr(\'data-run\',\'1\');
      $(\'#product_optional_remove_response\').html(\'<div style="text-align:center;"><img src="/wp-content/plugins/gks_core/img/progress_bar.gif"></div>\').show();  
    }
     
    datasend={
      cmd:  cmd,
      data: mydata,
      guid: guid,
    };
    $.ajax({
      type: \'post\',
      cache: false,
      dataType: \'json\',
      url: \'/wp-admin/admin-ajax.php\',
      gks_cmd:cmd,
      gks_mydata:mydata,
      data: {
        action: \'gks_erp_sales_order_online_post\',  
        data: datasend,         
      },
      error : function(jqXHR ,textStatus,  errorThrown) {
        //console.log(jqXHR.responseText);
        if (this.gks_cmd==\'reject\') {
          $(\'#button_reject\').attr(\'data-run\',\'0\');
          $(\'#button_response\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
          $(\'#gks_erp_sales_order_online_buttons\').show();
        }
        if (this.gks_cmd==\'accept\') {
          $(\'#button_accept2\').attr(\'data-run\',\'0\');
          $(\'#button_response2\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
          $(\'#gks_erp_sales_order_online_buttons2\').show();
          setTimeout(function() {$(\'#button_response2\').slideUp();},3000);
        }
        if (this.gks_cmd==\'message_send\') {
          $(\'#button_message_send\').attr(\'data-run\',\'0\');
          $(\'#message_send_response\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
        }
        if (this.gks_cmd==\'pass_check\') {
          $(\'#button_needpass_check\').attr(\'data-run\',\'0\');
          $(\'#button_needpass_check_response\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
        }
        if (this.gks_cmd==\'product_optional_add\') {
          $(\'.product_is_optional_icon_add[data-recid="\'+this.gks_mydata+\'"]\').attr(\'data-run\',\'0\');
          $(\'#product_optional_add_response\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
        }
        if (this.gks_cmd==\'product_optional_remove\') {
          $(\'.product_is_optional_icon_remove[data-recid="\'+this.gks_mydata+\'"]\').attr(\'data-run\',\'0\');
          $(\'#product_optional_remove_response\').html(\'<div class="button_response_error">Σφάλμα. Παρακαλώ ξαναδοκιμάστε αργότερα.</div>\').slideDown();  
        }
        
      },
      success: function (data) {
        data2=data.data;
        if (data.data ==undefined) data2={success:false,message:\'server error\'};
        //console.log(data2);
        if (this.gks_cmd==\'reject\') {
          if (data2.success==false) {
            $(\'#button_reject\').attr(\'data-run\',\'0\');
            $(\'#button_response\').html(\'<div class="button_response_error">Σφάλμα \'+data2.message+\'</div>\').slideDown();  
            $(\'#gks_erp_sales_order_online_buttons\').show();
          } else {
            $(\'#button_response\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            setTimeout(function() {
              window.location.reload();
            }, 2000);
            
          }
        }
        if (this.gks_cmd==\'accept\') {
          if (!data2 || data2.success==false) {
            $(\'#button_accept2\').attr(\'data-run\',\'0\');
            $(\'#button_response2\').html(\'<div class="button_response_error">Σφάλμα \'+data2.message+\'</div>\').slideDown();  
            $(\'#gks_erp_sales_order_online_buttons2\').show();
          } else {
            $(\'#button_response2\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            setTimeout(function() {
              window.location.reload();
            }, 2000);
            
          }
        }        
        if (this.gks_cmd==\'timer_get_log\') {
          if (data2.success) {
            if (data2.messages.length>0) {
              for(mm=0;mm<data2.messages.length;mm++) {
                if ($(\'.gks_erp_sales_order_online_message[data_rec_id="\'+data2.messages[mm].id+\'"]\').length==0) {
                  $(\'.gks_erp_sales_order_online_order_messages\').append(data2.messages[mm].html);
                }
              }
              max_id_gks_orders_log=data2.max_id_gks_orders_log;
              var jj=0;
              $(\'.gks_erp_sales_order_online_message .gks_cc1\').each(function() {
                jj++;
                $(this).html(jj+\'.\');  
              });
              document.querySelector(\'#audioping\').play();
            }
          }
          clearTimeout(timer_get_log_obj);
          timer_get_log_obj=setTimeout(timer_get_log, 5000);
        }
        if (this.gks_cmd==\'message_send\') {
          $(\'#button_message_send\').attr(\'data-run\',\'0\');
          if (data2.success==false) {
            $(\'#message_send_response\').html(\'<div class="button_response_error">Σφάλμα \'+data2.message+\'</div>\').slideDown();  
          } else {
            $(\'#message_send_response\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            $(\'#gks_erp_sales_order_online_order_new_message_textarea_obj\').val(\'\');
            clearTimeout(timer_get_log_obj);
            timer_get_log();
          }
        
        }
        if (this.gks_cmd==\'pass_check\') {
          $(\'#button_needpass_check\').attr(\'data-run\',\'0\');
          if (data2.success==false) {
            $(\'#button_needpass_check_response\').html(\'<div class="button_response_error">\'+data2.message+\'</div>\').slideDown();  
          } else {
            $(\'#button_needpass_check_response\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            var date = new Date();
            date.setTime(date.getTime() + ((30*24*60*60)*1000));
            expires = "; expires=" + date.toUTCString();
            document.cookie = \'gks_sales_online_\'+ data2.guid + "=" + data2.userpass + expires + "; path=/";
            window.location.reload();
          }
        }

        if (this.gks_cmd==\'product_optional_add\') {
          $(\'.product_is_optional_icon_add[data-recid="\'+this.gks_mydata+\'"]\').attr(\'data-run\',\'0\');
          if (data2.success==false) {
            $(\'#product_optional_add_response\').html(\'<div class="button_response_error">\'+data2.message+\'</div>\').slideDown();  
          } else {
            $(\'#product_optional_add_response\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            window.location.reload();
          }
        }
        if (this.gks_cmd==\'product_optional_remove\') {
          $(\'.product_is_optional_icon_remove[data-recid="\'+this.gks_mydata+\'"]\').attr(\'data-run\',\'0\');
          if (data2.success==false) {
            $(\'#product_optional_remove_response\').html(\'<div class="button_response_error">\'+data2.message+\'</div>\').slideDown();  
          } else {
            $(\'#product_optional_remove_response\').html(\'<div class="button_response_ok">\'+data2.message+\'</div>\').slideDown();
            window.location.reload();
          }
        }

        
        
      },
    });      
  }

  $(\'#button_reject\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    mypostdata(\'reject\',\'\');
  }); 

  $(\'#button_accept\').click(function() {
    $(\'#gks_erp_sales_order_online_buttons\').hide();
    $(\'#gks_erp_sales_order_online_buttons_signature\').show();

    //document.documentElement.scrollTop+=200;
    $(\'#gks_signature_div\').show(); //slideDown(); //fadeIn(1000);
    gks_signature_init();
    $(\'#gks_erp_sales_order_online_buttons2_div\').show();
          
//    cccc=document.documentElement.scrollTop+200;
//    $([document.documentElement, document.body]).animate({
//        scrollTop: cccc
//    }, 1000,\'swing\', function () {
//      $(\'#gks_signature_div\').fadeIn(1000);
//      gks_signature_init();
//    });
    
  });
  $(\'#button_accept2\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    var b64 = getActiveBase64();
    if (!b64) {
      $(\'#button_response2\').html(\'<div class="button_response_error">Ζωγραφίστε την υπογραφή σας στο παραπάνω πλαίσιο</div>\').slideDown();
      setTimeout(function() {$(\'#button_response2\').slideUp();},3000);
      return;
    }
    //console.log(b64);
    mypostdata(\'accept\',b64);
    
  });   
  
  max_id_gks_orders_log=parseInt($(\'#max_id_gks_orders_log\').val());
  if (isNaN(max_id_gks_orders_log)) max_id_gks_orders_log=0;
  
  //console.log(max_id_gks_orders_log);
  
  
  function timer_get_log() {
    mypostdata(\'timer_get_log\',max_id_gks_orders_log);
  }
  if ($(\'#gks_erp_sales_order_online_order_new_message_textarea_obj\').length==1) {
    var timer_get_log_obj=setTimeout(timer_get_log, 5000);
  }
  
  $(\'#button_message_send\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    mytext=$(\'#gks_erp_sales_order_online_order_new_message_textarea_obj\').val().trim();
    if (mytext==\'\') {
      $(\'#message_send_response\').html(\'<div class="button_response_error">Πληκτρολογήστε κάποιο κείμενο</div>\').slideDown();  
      return;
    }
    mypostdata(\'message_send\',mytext);
  });
  
  $(\'#button_needpass_check\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    userpass=$(\'#order_online_code\').val().trim();
    if (userpass==\'\') {
      $(\'#button_needpass_check_response\').html(\'<div class="button_response_error">Πληκτρολογήστε τον κωδικό πρόσβασης</div>\').slideDown();
      return;  
    }
    mypostdata(\'pass_check\',userpass);
    
  });

  $(\'.product_is_optional_icon_add\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    recid=parseInt($(this).attr(\'data-recid\'));
    if (isNaN(recid)) recid=0;
    if (recid<=0) return;
    mypostdata(\'product_optional_add\',recid);
  });
  $(\'.product_is_optional_icon_remove\').click(function() {
    data_run=$(this).attr(\'data-run\');
    if (data_run==\'1\') return;
    recid=parseInt($(this).attr(\'data-recid\'));
    if (isNaN(recid)) recid=0;
    if (recid<=0) return;
    mypostdata(\'product_optional_remove\',recid);
  });
  
  $(\'td.product_row_img > a.gks_photo_link\').each(function() {
    myhref=$(this).attr(\'href\');
    if (myhref==\'#\') {
      gg=$(this).find(\'img.gks_img\').attr(\'src\');
      $(this).attr(\'href\',gg);
    }
    
  });



  //signature start
  var canvas;
  var ctx;
  var hint;
  var statusEl;
  var dropZone;
  var fileInput;

  var drawing     = false;
  var hasSig      = false;
  var uploadedB64 = null;
  var activeTab   = \'draw\'; 
  var has_upload_file=false;
  
  function gks_signature_init() {
    $(\'#gks_signature_card_div\').html(`<div class="gks_signature_tabs">
            <button class="gks_signature_tab-btn gks_signature_active" id="gks_signature_tabDraw"   >\u270E Ζωγραφική</button>
            <button class="gks_signature_tab-btn"                      id="gks_signature_tabUpload" >\uD83D\uDCC1 Upload PNG</button>
          </div>
        

          <div id="gks_signature_paneDraw">
            <p style="font-size:13px; color:#666; margin-bottom:10px;">Ζωγραφίστε την υπογραφή σας</p>
            <div class="gks_signature_canvas-wrap gks_signature_checker">
              <canvas id="gks_signature_sigCanvas"></canvas>
              <span id="gks_signature_hint">Ξεκινήστε εδώ...</span>
            </div>
            <div class="gks_signature_draw-controls">
              <button id="gks_signature_clearSig">Καθαρισμός</button>
              <button id="gks_signature_downloadSig">Λήψη PNG</button>
        
              <div style="margin-left:auto; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <span class="gks_signature_ctrl-label">Χρώμα:</span>
                <input type="color" id="gks_signature_colorPick" value="#000000" />
                <span class="gks_signature_ctrl-label">Μέγεθος:</span>
                <input type="range" id="gks_signature_sizeRange" min="1" max="10" value="2"/>
                <span class="gks_signature_size-out" id="gks_signature_sizeOut">2</span>
              </div>
            </div>
          </div>
        
          <div id="gks_signature_paneUpload" style="display:none;">
            <p style="font-size:13px; color:#666; margin-bottom:10px;">Επιλέξτε PNG με υπάρχουσα υπογραφή</p>
        
            <div id="gks_signature_dropZone">
              <span class="gks_signature_dz-icon">\uD83D\uDCC1</span>
              <span class="gks_signature_dz-text">Σύρετε το αρχείο εδώ ή κλικ για επιλογή</span>
              <span class="gks_signature_dz-sub">PNG, JPG — διατηρεί διαφάνεια αν υπάρχει</span>
            </div>
            <input type="file" id="gks_signature_fileInput" accept="image/png,image/jpeg" style="display:none;" />
          </div>
          
          <div id="gks_signature_statusMsg"></div>`);
    
    
    www=$(\'#gks_signature_sigCanvas\').width();
    hhh=$(\'#gks_signature_sigCanvas\').height();
    $(\'#gks_signature_sigCanvas\').css(\'width\',www+\'px\')
                   .css(\'height\',hhh+\'px\')
                   .attr(\'width\',www)
                   .attr(\'height\',hhh);
    
    
    $(\'#gks_signature_tabDraw\').click(function() {switchTab(\'draw\');});
    $(\'#gks_signature_tabUpload\').click(function() {switchTab(\'upload\');});
    $(\'#gks_signature_clearSig\').click(function() {clearSig();});
    $(\'#gks_signature_sizeRange\').on(\'change input\',function() {
      $(\'#gks_signature_sizeOut\').html($(\'#gks_signature_sizeRange\').val());
    });
    $(\'#gks_signature_downloadSig\').click(function() {downloadSig();});

    canvas      = document.getElementById(\'gks_signature_sigCanvas\');
    ctx         = canvas.getContext(\'2d\');
    hint        = document.getElementById(\'gks_signature_hint\');
    statusEl    = document.getElementById(\'gks_signature_statusMsg\');
    dropZone    = document.getElementById(\'gks_signature_dropZone\');
    fileInput   = document.getElementById(\'gks_signature_fileInput\');

    canvas.addEventListener(\'mousedown\', function(e) {
      drawing = true;
      ctx.beginPath();
      var p = getPos(e);
      ctx.moveTo(p.x, p.y);
      if (!hasSig) { hint.style.display = \'none\'; hasSig = true; }
    });
    canvas.addEventListener(\'mousemove\', function(e) {
      if (!drawing) return;
      applyStroke(getPos(e));
    });
    canvas.addEventListener(\'mouseup\',    function() { endDraw(); });
    canvas.addEventListener(\'mouseleave\', function() { endDraw(); });
  
    canvas.addEventListener(\'touchstart\', function(e) {
      e.preventDefault();
      drawing = true;
      ctx.beginPath();
      var p = getPos(e);
      ctx.moveTo(p.x, p.y);
      if (!hasSig) { hint.style.display = \'none\'; hasSig = true; }
    }, { passive: false });
    canvas.addEventListener(\'touchmove\', function(e) {
      if (!drawing) return;
      e.preventDefault();
      applyStroke(getPos(e));
    }, { passive: false });
    canvas.addEventListener(\'touchend\', function() { endDraw(); });
  
  
  
    dropZone.addEventListener(\'click\', function() { fileInput.click(); });
    fileInput.addEventListener(\'change\', function() {
      if (fileInput.files[0]) loadFile(fileInput.files[0]);
    });
    dropZone.addEventListener(\'dragover\', function(e) {
      e.preventDefault();
      dropZone.classList.add(\'drag-over\');
    });
    dropZone.addEventListener(\'dragleave\', function() {
      dropZone.classList.remove(\'drag-over\');
    });
    dropZone.addEventListener(\'drop\', function(e) {
      e.preventDefault();
      dropZone.classList.remove(\'drag-over\');
      if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });

  }
  
  function switchTab(tab) {
    activeTab = tab;
    document.getElementById(\'gks_signature_paneDraw\').style.display   = (tab === \'draw\')   ? \'\' : \'none\';
    document.getElementById(\'gks_signature_paneUpload\').style.display = (tab === \'upload\') ? \'\' : \'none\';
    document.getElementById(\'gks_signature_tabDraw\').classList.toggle(\'gks_signature_active\',   tab === \'draw\');
    document.getElementById(\'gks_signature_tabUpload\').classList.toggle(\'gks_signature_active\', tab === \'upload\');
  }
  function getPos(e) {
    var r   = canvas.getBoundingClientRect();
    var src = e.touches ? e.touches[0] : e;
    return {
      x: (src.clientX - r.left) * (canvas.width  / r.width),
      y: (src.clientY - r.top)  * (canvas.height / r.height)
    };
  }
  function applyStroke(p) {
    ctx.lineWidth   = parseInt(document.getElementById(\'gks_signature_sizeRange\').value);
    ctx.lineCap     = \'round\';
    ctx.lineJoin    = \'round\';
    ctx.strokeStyle = document.getElementById(\'gks_signature_colorPick\').value;
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    has_upload_file=false;
  }
  function endDraw() {
    if (!drawing) return;
    drawing = false;
    updatePreview();
    has_upload_file=false;
  }
  function clearSig() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasSig = false;
    hint.style.display = \'\';
    has_upload_file=false;
  }
  function loadFile(file) {
    if (!file.type.startsWith(\'image/\')) {
      $(\'#button_response2\').html(\'<div class="button_response_error">Επιλέξτε ένα έγκυρο αρχείο PNG/JPG.</div>\').slideDown();
      setTimeout(function() {$(\'#button_response2\').slideUp();},3000);
      return;
    }
    var reader = new FileReader();
    reader.onload = function(ev) {
      uploadedB64 = ev.target.result;
      var img = new Image();
      img.onload = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var scale = Math.min(canvas.width / img.width, canvas.height / img.height);
        var w = img.width  * scale;
        var h = img.height * scale;
        var x = (canvas.width  - w) / 2;
        var y = (canvas.height - h) / 2;
        ctx.drawImage(img, x, y, w, h);
        hasSig = true;
        hint.style.display = \'none\';
        switchTab(\'draw\');
        has_upload_file=true;
        updatePreview();
      };
      img.src = uploadedB64;
    };
    reader.readAsDataURL(file);
  }

  function getActiveBase64() {
    if (activeTab === \'upload\') return uploadedB64;
    if (has_upload_file && uploadedB64) return uploadedB64;
    return hasSig ? canvas.toDataURL(\'image/png\') : null;
  }
  function updatePreview() {
    var b64 = getActiveBase64();
    if (!b64) return;
    var payload = { signature: b64, mimeType: \'image/png\', timestamp: new Date().toISOString() };
    var full = JSON.stringify(payload, null, 2);
    //jsonPre.textContent = full.substring(0, 250) + \'\n  \u2026 [base64 \u03c3\u03c5\u03bd\u03b5\u03c7\u03af\u03b6\u03b5\u03c4\u03b1\u03b9]\n}\';
  }
  function downloadSig() {
    var b64 = getActiveBase64();
    if (!b64) { 
      $(\'#button_response2\').html(\'<div class="button_response_error">Δεν υπάρχει υπογραφή για λήψη.</div>\').slideDown();
      setTimeout(function() {$(\'#button_response2\').slideUp();},3000);
      return;
    }
    var a = document.createElement(\'a\');
    a.download = \'signature.png\';
    a.href = b64;
    a.click();
  }
});')."'
);");
}


$sql="select * from gks_permission_object where id_permission_object=371";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (371,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_template_html','Πρότυπα HTML',3710)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",371,1,1,1,1,1)");
  }

}


$sql="select * from gks_custom_table where id_custom_table=61";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`,obj_url) VALUES 
   (61,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - Πρότυπα HTML','gks_template_html','id_template_html','template_html_id',0,'base',131,'admin-template_html.php')");
}
$sql="select * from gks_crm_activity_objects where id_crm_activity_object=57";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (57,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_template_html','Πρότυπο HTML',57,0,'admin-template_html-item.php?id=%s')");
}


$sql="select online_template_html_id from gks_orders limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders`
  ADD COLUMN `online_template_html_id` int(11) NOT NULL DEFAULT 0 after online_password");
}

$sql="select job_title from gks_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users`
  ADD COLUMN `job_title` varchar(190) DEFAULT NULL;");
}

$sql="select product_def_comments from gks_eshop_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products`
  ADD COLUMN `product_def_comments` longtext DEFAULT NULL after product_descr_variable");
  
  gks_run_sql("ALTER TABLE `gks_eshop_products_lang`
  ADD COLUMN `product_def_comments` longtext DEFAULT NULL after product_descr_variable");
}

$sql="select eidos_parastatikou_multiple_connected_marks from gks_acc_eidi_parastatikon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon`
  ADD COLUMN `eidos_parastatikou_multiple_connected_marks` tinyint(4) NOT NULL DEFAULT 0 
    after eidos_parastatikou_correlated_invoices,
  ADD COLUMN `eidos_parastatikou_packings_declarations` tinyint(4) NOT NULL DEFAULT 0 
    after eidos_parastatikou_multiple_connected_marks");
}

$sql="select journal_has_multiple_connected_marks from gks_acc_journal limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_journal`
  ADD COLUMN `journal_has_multiple_connected_marks` tinyint(4) NOT NULL DEFAULT 0 
        after journal_has_correlated_invoices,
  ADD COLUMN `journal_has_packings_declarations` tinyint(4) NOT NULL DEFAULT 0 
        after journal_has_multiple_connected_marks");
}
$sql="select seira_is_reverse_delivery_note from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_seires`
  ADD COLUMN `seira_is_reverse_delivery_note` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `seira_is_self_pricing` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `seira_is_vat_payment_suspension` tinyint(4) NOT NULL DEFAULT 0");
}

//multipleConnectedMarks
//packingsDeclarations
//vatPaymentSuspension
//selfPricing
//reverseDeliveryNote
//
//reverseDeliveryNotePurpose
//PackagingDetailType
//toWeigh
  

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_multiple_connected_marks` (
  `id_acc_inv_multiple_connected_marks` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `mcm_mark` varchar(64) DEFAULT NULL,
  `mcm_acc_inv_id` int(11) DEFAULT NULL,
  `mcm_whi_mov_id` int(11) DEFAULT NULL,
  `mcm_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_inv_multiple_connected_marks`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `mcm_mark` (`mcm_mark`),
  KEY `mcm_acc_inv_id` (`mcm_acc_inv_id`),
  KEY `mcm_whi_mov_id` (`mcm_whi_mov_id`),
  KEY `mcm_aa` (`mcm_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_packings_declarations` (
  `id_acc_inv_packings_declarations` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `packaging_type_id` int(11) DEFAULT NULL,
  `packaging_type_6_descr`varchar(190) DEFAULT NULL,
  `packaging_quantity` int(11) DEFAULT NULL,
  `packaging_aa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_acc_inv_packings_declarations`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `packaging_aa` (`packaging_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_pay_multiple_connected_marks` (
  `id_acc_pay_multiple_connected_marks` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_pay_id` int(11) NOT NULL DEFAULT 0,
  `mcm_mark` varchar(64) DEFAULT NULL,
  `mcm_acc_inv_id` int(11) DEFAULT NULL,
  `mcm_acc_pay_id` int(11) DEFAULT NULL,
  `mcm_whi_mov_id` int(11) DEFAULT NULL,
  `mcm_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_pay_multiple_connected_marks`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_pay_id` (`acc_pay_id`),
  KEY `mcm_mark` (`mcm_mark`),
  KEY `mcm_acc_inv_id` (`mcm_acc_inv_id`),
  KEY `mcm_acc_pay_id` (`mcm_acc_pay_id`),
  KEY `mcm_whi_mov_id` (`mcm_whi_mov_id`),
  KEY `mcm_aa` (`mcm_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_multiple_connected_marks` (
  `id_whi_mov_multiple_connected_marks` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `whi_mov_id` int(11) NOT NULL DEFAULT 0,
  `mcm_mark` varchar(64) DEFAULT NULL,
  `mcm_acc_inv_id` int(11) DEFAULT NULL,
  `mcm_whi_mov_id` int(11) DEFAULT NULL,
  `mcm_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_whi_mov_multiple_connected_marks`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `whi_mov_id` (`whi_mov_id`),
  KEY `mcm_mark` (`mcm_mark`),
  KEY `mcm_acc_inv_id` (`mcm_acc_inv_id`),
  KEY `mcm_whi_mov_id` (`mcm_whi_mov_id`),
  KEY `mcm_aa` (`mcm_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_packings_declarations` (
  `id_whi_mov_packings_declarations` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `whi_mov_id` int(11) NOT NULL DEFAULT 0,
  `packaging_type_id` int(11) DEFAULT NULL,
  `packaging_type_6_descr`varchar(190) DEFAULT NULL,
  `packaging_quantity` int(11) DEFAULT NULL,
  `packaging_aa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_whi_mov_packings_declarations`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `whi_mov_id` (`whi_mov_id`),
  KEY `packaging_aa` (`packaging_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("update gks_acc_eidi_parastatikon set 
eidos_parastatikou_correlated_invoices=1
where id_acc_eidos_parastatikou in (902,912,952)");

 
$sql="select * from gks_crons where id_cron=9 limit 1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crons` (`id_cron`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `disable_cron`,`every_seconds`,`fetch_url`,`last_run`,`next_run`,`comments`) VALUES 
  (9,  '2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',0,21600, '/my/cron_delete_tmp_files.php?folder=sessions&minutes=1440',null,null,'')");
}


$sql="select activity_notification from gks_crm_activity limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_crm_activity`
  ADD COLUMN `activity_notification` tinyint(4) NOT NULL DEFAULT 0,
  ADD INDEX activity_notification (activity_notification),
  ADD COLUMN `activity_notification_send_at` datetime DEFAULT NULL,
  ADD INDEX activity_notification_send_at (activity_notification_send_at)");
}
$sql="select uri from gks_crm_activity limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_crm_activity`
  ADD COLUMN `calendardata` text DEFAULT NULL,
  ADD COLUMN `uri` varchar(200) DEFAULT NULL,
  ADD COLUMN `etag` varchar(32) DEFAULT NULL,
  ADD COLUMN `size` int(11) unsigned NOT NULL DEFAULT 0,
  ADD COLUMN `componenttype` varchar(32) DEFAULT NULL,
  ADD COLUMN `uid` varchar(200) DEFAULT NULL,
  add index uri (uri)");

  gks_run_sql("update gks_crm_activity set mydate_add=activity_duedate where mydate_add is null");
  gks_run_sql("update gks_crm_activity set mydate_edit=activity_duedate where mydate_edit is null");
  
  
  $sql="select id_crm_activity from gks_crm_activity order by id_crm_activity desc";
  $result = $db_link->query($sql);  
  if (!$result) die('sql error');
  $ids=array();
  while ($row = $result->fetch_assoc()) {
    $ids[]=$row['id_crm_activity'];
  }
  //print '<pre>';print_r($ids);
  foreach ($ids as $id) {
    gks_calendar_event_update_dav_activity($id,false);
  }
    
}

$sql="select * from gks_notification_type where id_notification_type=20";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_notification_type (
  id_notification_type,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  notification_type_descr,notification_type_sortorder,notification_type_disabled
  ) values (
  20,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  'Δραστηριότητα',20,0
  )");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_notification_userperm
    GROUP BY user_id
  ) AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_notification_userperm` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,notification_type_id,from_admin,from_user,to_email,to_viber) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",20,1,1,1,1)");
  }
}


$tmp_dav_direcrory=GKS_SITE_PATH.'tmp_dav';
if (file_exists($tmp_dav_direcrory)==false) {
  if (@mkdir($tmp_dav_direcrory , 0755, true) == false ) {
    echo 'can not create dir: '.$upload_dir;die();
  }
}

$sql="select reverse_delivery_purpose from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  ADD COLUMN `reverse_delivery_purpose` INT(11) NOT NULL DEFAULT 0 after mov_whi_number_str");
}

$sql="select * from gks_aade_typos_xarakt_eksodon where id_aade_typos_xarakt_eksodon=101";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_aade_typos_xarakt_eksodon (
  id_aade_typos_xarakt_eksodon,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_typos_xarakt_eksodon_code,aade_typos_xarakt_eksodon_descr,sortorder
  ) values (
  101,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  'NOT_VAT_295','Μη συμμετοχή στο ΦΠΑ (έξοδα – εισροές Φ2)',1001)");
}
