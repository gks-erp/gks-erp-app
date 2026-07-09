<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

    
    $sql="select ads_campain_sortorder from gks_ads_campain limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_ads_campain`
      ADD COLUMN `ads_campain_sortorder` int(11) NOT NULL DEFAULT '1000',
      ADD INDEX `ads_campain_sortorder`(`ads_campain_sortorder`);");
    }

    $sql="select urlsort_disabled from gks_urlshort limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_urlshort`
      ADD COLUMN `urlsort_disabled` TINYINT NOT NULL DEFAULT 0 AFTER `shorturl`,
      ADD INDEX `urlsort_disabled`(`urlsort_disabled`),
      ADD COLUMN `urlsort_sortorder` int(11) NOT NULL DEFAULT '1000' AFTER `urlsort_disabled`, 
      ADD INDEX `urlsort_sortorder`(`urlsort_sortorder`),
      ADD COLUMN `urlsort_descr` VARCHAR(250) DEFAULT NULL AFTER `odbc`,
      ADD INDEX `urlsort_descr`(`urlsort_descr`);");
    }
  
    $sql="select * from gks_custom_table where id_custom_table=30";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
     (30,'2020-01-01 00:00:00','2021-10-31 15:53:13',2,2,'127.0.0.1','CRM - Καμπάνιες','gks_ads_campain','id_ads_campain','ads_campain_id',0,'crm',1040),
     (31,'2020-01-01 00:00:00','2021-10-31 17:16:53',2,2,'127.0.0.1','CRM -Μικρό URL','gks_urlshort','id_urlshort','urlshort_id',0,'crm',1050);");
    }
   
    
    $sql="select * from gks_permission_object where id_permission_object=495";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
      (495,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_urlshort','Μικρό URL',495),
      (496,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_urlshort_hit','Καταγραφές Μικρό URL',496);");
    }
  
    $sql="select company_sortorder from gks_company limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_company` 
      ADD COLUMN `company_sortorder` INTEGER NOT NULL DEFAULT 1000 AFTER `default_eshop_company`,
      ADD INDEX `company_sortorder`(`company_sortorder`);");
    }
    
  
    $sql="select company_sub_sortorder from gks_company_subs limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_company_subs` 
      ADD COLUMN `company_sub_sortorder` INTEGER NOT NULL DEFAULT 1000 AFTER `default_eshop_company`,
      ADD INDEX `company_sub_sortorder`(`company_sub_sortorder`);");
    }
    
  
    $sql="select warehouse_sortorder from gks_warehouses limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_warehouses` 
      ADD COLUMN `warehouse_sortorder` INTEGER NOT NULL DEFAULT 1000 AFTER `warehouse_color`,
      ADD INDEX `warehouse_sortorder`(`warehouse_sortorder`);");
    }
    
  
    if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/async.php')==false) {
      echo 'copy file \wp-content\plugins\gks_core\async.php'; die(); }
    
    //if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/_current/_config.php')==false) {
    //  echo 'edit file \wp-content\plugins\gks_core\_current/_config.php'; die(); }
 
//    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/_current/_config.php');
//    if (strpos($read_file, '$gks_api_php_path_exe') === false) {
//      echo '/wp-content/plugins/gks_core/_current/_config.php file not nontains $gks_api_php_path_exe<br>'; die();}
    
    //auto add permitions to admins pou idi exoun dikaiomata gia to gks_urlshort
    

    gks_run_sql("ALTER TABLE `gks_orders_products` MODIFY COLUMN `product_comments` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_acc_inv_products` MODIFY COLUMN `product_comments` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_whi_mov_products` MODIFY COLUMN `product_comments` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_acc_pay_method` MODIFY COLUMN `paymethod_comments` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


    $sql="select last_update_user_id from gks_woo_product limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_woo_product` 
      ADD COLUMN `last_update_user_id` int(11) NOT NULL DEFAULT '0',
      ADD INDEX `last_update_user_id`(`last_update_user_id`),
      ADD COLUMN `last_update_date` DATETIME DEFAULT NULL,
      ADD INDEX `last_update_date`(`last_update_date`);");
    }


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_woo_categories` (
      `id_woo_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `product_category_id` int(11) NOT NULL DEFAULT '0',
      `eshop_id` int(11) NOT NULL DEFAULT '0',
      `remote_category_id` int(11) NOT NULL DEFAULT '0',
      `last_update_user_id` int(11) NOT NULL DEFAULT '0',
      `last_update_date` datetime DEFAULT NULL,
      PRIMARY KEY (`id_woo_category`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `product_category_id` (`product_category_id`),
      KEY `eshop_id` (`eshop_id`),
      KEY `remote_category_id` (`remote_category_id`),
      KEY `last_update_user_id` (`last_update_user_id`),
      KEY `last_update_date` (`last_update_date`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_woo_brands` (
      `id_woo_brand` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `product_brand_id` int(11) NOT NULL DEFAULT '0',
      `eshop_id` int(11) NOT NULL DEFAULT '0',
      `pluginname` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `remote_brand_id` int(11) NOT NULL DEFAULT '0',
      `last_update_user_id` int(11) NOT NULL DEFAULT '0',
      `last_update_date` datetime DEFAULT NULL,
      PRIMARY KEY (`id_woo_brand`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `product_brand_id` (`product_brand_id`),
      KEY `eshop_id` (`eshop_id`),
      KEY `pluginname` (`pluginname`),
      KEY `remote_brand_id` (`remote_brand_id`),
      KEY `last_update_user_id` (`last_update_user_id`),
      KEY `last_update_date` (`last_update_date`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    
    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_dav_db.php');
    if (strpos($read_file, 'GKS_WP_TABLE_PREFIX') === false) {
      echo '/my/_current/_dav_db.php file not nontains GKS_WP_TABLE_PREFIX<br>'; die();}
    
    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
    if (strpos($read_file, 'GKS_MAXIMIND_COM_PATH') === false) {
      echo '_current/_config.php file not nontains GKS_MAXIMIND_COM_PATH<br>';die();}
    
    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
    if (strpos($read_file, 'GKS_PDF_GENERATOR') === false) {
      echo '_current/_config.php file not nontains GKS_PDF_GENERATOR<br>';die();}
    
    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
    if (strpos($read_file, 'GKS_ESHOP_BRANDS_TAXONOMY') === false) {
      echo '_current/_config.php file not nontains GKS_ESHOP_BRANDS_TAXONOMY<br>';die();}

    //$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/_current/_config.php');
    //if (strpos($read_file, '$gks_eshop_brands_taxonomy') === false) {
    //  echo '/wp-content/plugins/gks_core/_current/_config.php file not nontains $gks_eshop_brands_taxonomy<br>'; die();}

    
    $found_triggers=false;
    $sql="show triggers";
    $result = gks_run_sql($sql);
    while ($row = $result->fetch_assoc()) {
      $trigger_name=$row['Trigger'];
      if ($trigger_name=='gks_trigger_fullname1') {
        $found_triggers=true;
        break;
      }
    } 
    if ($found_triggers==false && file_exists('gks_trigger.sql')) {
      $gks_trigger=file_get_contents('gks_trigger.sql');
      $gks_trigger=str_replace('[GKS_WP_TABLE_PREFIX]',GKS_WP_TABLE_PREFIX,$gks_trigger);
      echo '<pre>';    
      echo $gks_trigger;
      die();
    }

    
    
    gks_run_sql("update ".GKS_WP_TABLE_PREFIX."users set user_registered=odbc where user_registered is null");
    gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` MODIFY COLUMN `user_registered` DATETIME DEFAULT NULL;");
    
    gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` 
    MODIFY COLUMN `fiscal_position_id` INT(11) NOT NULL DEFAULT 1,
    MODIFY COLUMN `pricelist_id` INT(11) NOT NULL DEFAULT 1;");
    
    $sql="select is_woo_delivery from gks_users_extra_address limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_users_extra_address` 
      ADD COLUMN `is_woo_delivery` TINYINT NOT NULL DEFAULT 0,
      ADD INDEX `is_woo_delivery`(`is_woo_delivery`);");
    }

    

    //print '<pre>';print_r($myids);die();
    
    $sql="select woo_eshop_id from gks_orders limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_orders` 
      ADD COLUMN `woo_eshop_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `woo_order_id` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `woo_eshop_id`(`woo_eshop_id`),
      ADD INDEX `woo_order_id`(`woo_order_id`);");
    }
    
    $sql="select woo_item_id from gks_orders_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_orders_products` 
      ADD COLUMN `woo_item_id` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `woo_item_id`(`woo_item_id`);");
    }
    
    $sql="select woo_eshop_id from gks_acc_inv limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv` 
      ADD COLUMN `woo_eshop_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `woo_order_id` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `woo_eshop_id`(`woo_eshop_id`),
      ADD INDEX `woo_order_id`(`woo_order_id`);");
    }
    
    $sql="select woo_item_id from gks_acc_inv_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv_products` 
      ADD COLUMN `woo_item_id` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `woo_item_id`(`woo_item_id`);");
    }
    
    $sql="select update_from_gks from gks_acc_inv limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv`
      ADD COLUMN `update_from_gks` TINYINT NOT NULL DEFAULT 0 AFTER `credit_memo_for_acc_inv_id`;");
    }    
    
    $sql="select order_find_user_from from gks_eshops limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshops`
      ADD COLUMN `order_find_user_from` VARCHAR(32) DEFAULT NULL,
      ADD COLUMN `order_meta_user_lang` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_parastatiko` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_eponimia` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_title` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_afm` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_doy` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `order_meta_epaggelma` VARCHAR(255) DEFAULT NULL;");
    }


    $sql="select woo_delivery_to_gks from gks_eshops limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshops`
      ADD COLUMN `woo_delivery_to_gks` TEXT DEFAULT NULL,
      ADD COLUMN `woo_payment_to_gks` TEXT DEFAULT NULL;");
    }
    
    $sql="select import_yes from gks_eshops limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshops`
      ADD COLUMN `import_yes` TINYINT NOT NULL DEFAULT 0,
      ADD COLUMN `import_as` VARCHAR(32) DEFAULT 'order',
      ADD COLUMN `acc_journal_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `acc_seira_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `warehouses_id_from` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `will_update` TINYINT NOT NULL DEFAULT 0,
      ADD COLUMN `update_if_gks_change` TINYINT NOT NULL DEFAULT 0,
      ADD COLUMN `update_state_gks_order` TEXT DEFAULT NULL,
      ADD COLUMN `update_state_gks_acc_inv` TEXT DEFAULT NULL,
      ADD COLUMN `update_state_woo` TEXT DEFAULT NULL;");
    }
    




    $sql="select acc_inv_product_shipping from gks_eshops limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshops` 
      ADD COLUMN `acc_inv_product_shipping` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `acc_inv_product_fees` INTEGER NOT NULL DEFAULT 0;");
    }

    $sql="select product_fpa_ejeresi_id from gks_orders_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_orders_products`
      ADD COLUMN `product_fpa_ejeresi_id` INTEGER NOT NULL DEFAULT 0 AFTER `product_fpa_id`;");
    }
    

    $sql="select * from  gks_custom_table where id_custom_table=32";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
       (32,'2020-01-01','2020-01-01','2020-01-01',2,2,'127.0.0.1','Διαχείριση - Τρόποι Αποστολής','gks_delivery_methods', 'id_delivery_method', 'delivery_method_id', 0,'base',70),
       (33,'2020-01-01','2020-01-01','2020-01-01',2,2,'127.0.0.1','Διαχείριση - Τρόποι Πληρωμής', 'gks_payment_acquirers','id_payment_acquirer','payment_acquirer_id',0,'base',71);");
    }
    $sql="select * from  gks_custom_table where id_custom_table=34";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
       (34,'2020-01-01','2020-01-01','2020-01-01',2,2,'127.0.0.1','Πωλήσεις - Περίσταση','gks_orders_occasion', 'id_order_occasion', 'order_occasion_id', 0,'sales',2020)");
    }
    
    $sql="select mydate_add from gks_delivery_methods limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("alter table gks_delivery_methods
      add column `mydate_add` datetime DEFAULT NULL,
      add column `mydate_edit` datetime DEFAULT NULL,
      add column `user_id_add` int(11) NOT NULL DEFAULT '0',
      add column `user_id_edit` int(11) NOT NULL DEFAULT '0',
      add column `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      ADD INDEX `mydate_edit`(`mydate_edit`),
      ADD INDEX `user_id_edit`(`user_id_edit`)");
      
      gks_run_sql("update gks_delivery_methods set mydate_add='2020-01-01', mydate_edit='2020-01-01',user_id_add=2,user_id_edit=2,myip='127.0.0.1'");
    }
    
    $sql="select mydate_add from gks_payment_acquirers limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("alter table gks_payment_acquirers
      add column `mydate_add` datetime DEFAULT NULL,
      add column `mydate_edit` datetime DEFAULT NULL,
      add column `user_id_add` int(11) NOT NULL DEFAULT '0',
      add column `user_id_edit` int(11) NOT NULL DEFAULT '0',
      add column `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      ADD INDEX `mydate_edit`(`mydate_edit`),
      ADD INDEX `user_id_edit`(`user_id_edit`)");
      
      gks_run_sql("update gks_payment_acquirers set mydate_add='2020-01-01', mydate_edit='2020-01-01',user_id_add=2,user_id_edit=2,myip='127.0.0.1'");
    }




    $sql="select production_posto_sortorder from gks_production_posta limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_production_posta` 
      ADD COLUMN `production_posto_sortorder` INTEGER NOT NULL DEFAULT 1000,
      ADD INDEX `production_posto_sortorder`(`production_posto_sortorder`);");
    }
    
    $sql="select production_ergasia_sortorder from gks_production_ergasies limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_production_ergasies` 
      ADD COLUMN `production_ergasia_sortorder` INTEGER NOT NULL DEFAULT 1000,
      ADD INDEX `production_ergasia_sortorder`(`production_ergasia_sortorder`);");
    }
        
    
    $sql="select odbc from gks_production_line limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_production_line` 
      ADD COLUMN `odbc` TIMESTAMP NOT NULL;");
    }

    $sql="select * from gks_custom_field_type where id_custom_field_type=1222";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_field_type` (`id_custom_field_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`field_type_name`,`field_type_group`,`field_type_sxolio`,`field_type_sortorder`,`field_type_sql`,`field_type_collate`,`field_type_index`,`field_type_notdevyet`) VALUES 
       (1222,'2020-01-01','2020-01-01',2,2,'127.0.0.1','Επαφές',200,NULL,1206,'varchar(250)',1,1,0)");
    }


    $sql="select rbs_code_a from gks_acc_eidi_parastatikon limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon` 
      ADD COLUMN `rbs_code_a` INTEGER NOT NULL DEFAULT 0");
      gks_run_sql("update gks_acc_eidi_parastatikon set rbs_code_a=173 where id_acc_eidos_parastatikou=111");
    }
    
    
    $sql="select send_mydata from gks_acc_seires limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_seires` ADD COLUMN `send_mydata` TINYINT NOT NULL DEFAULT 0,
      ADD INDEX `send_mydata`(`send_mydata`);");
      
      gks_run_sql("UPDATE (gks_acc_seires 
      LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
      LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou 
      SET gks_acc_seires.send_mydata = 1
      WHERE (((gks_acc_seires.send_mydata)=0) 
      AND ((gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code)<>'') 
      AND ((gks_acc_seires.is_xeirografi)=0));");
    }

    
    $sql="select from_aade_import_lock from gks_acc_inv_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv_products` ADD COLUMN `from_aade_import_lock` TINYINT NOT NULL DEFAULT 0;");
    }

    $sql="select from_aade_import_json from gks_acc_inv limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv` ADD COLUMN `from_aade_import_json` TEXT DEFAULT NULL AFTER `from_aade_import`;");
    }


    $sql="select import_apo_allon from gks_acc_eidi_parastatikon limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon` ADD COLUMN `import_apo_allon` VARCHAR(190) DEFAULT NULL;");
    }

    $sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=504";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      gks_run_sql("delete from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou in (502,503,504,505)");
      
      gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon` (`id_acc_eidos_parastatikou`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`parent_id`,`eidos_parastatikou_type_id`,`eidos_parastatikou_need_prev`,`eidos_parastatikou_has_fpa`,`eidos_parastatikou_has_posotita`,`eidos_parastatikou_has_othertaxes`,`eidos_parastatikou_has_esoda`,`eidos_parastatikou_has_eksoda`,`eidos_parastatikou_need_afm`,`eidos_parastatikou_aade_code`,`eidos_parastatikou_descr`,`eidos_parastatikou_balance_pros`,`eidos_parastatikou_stock_pros`,`eidos_parastatikou_whi_type_id`,`def_prefix`,`def_suffix`,`sortorder`,`is_selectable`,`odbc`,`credit_acc_eidos_parastatikou_id`,`rbs_code_a`,`import_apo_allon`) VALUES 
     (502,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',501,2,0,1,1,'wh,ot,sd,fe,dd',0,1,1,NULL,'Τιμολόγιο Αγοράς Ειδών ημεδαπής (από Τιμολόγιο Πώλησης)',-1,0,912,NULL,NULL,1002,1,'2022-01-06 20:28:52',0,0,'[1.1]'),
     (503,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',501,2,0,1,1,'wh,ot,sd,fe,dd',0,1,1,NULL,'Πιστωτικό Τιμολόγιο Αγοράς Ειδών ημεδαπής (από Πιστωτικό Τιμολόγιο)',1,0,913,NULL,NULL,1003,1,'2022-01-06 20:42:01',502,0,'[5.1][5.2]'),
     (504,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',501,2,0,1,1,'wh,ot,sd,fe,dd',0,1,1,NULL,'Τιμολόγιο Αγοράς Υπηρεσιών ημεδαπής (από Τιμολόγιο Παροχής)',-1,0,0,NULL,NULL,1004,1,'2022-01-06 20:28:24',0,0,'[2.1]'),
     (505,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',501,2,0,1,1,'wh,ot,sd,fe,dd',0,1,1,NULL,'Πιστωτικό Τιμολόγιο Αγοράς Υπηρεσιών ημεδαπής (από Πιστωτικό Τιμολόγιο)',1,0,0,NULL,NULL,1005,1,'2022-01-06 20:42:31',504,0,'[5.1][5.2]');");
    
    }


    $sql="select acc_eidos_parastatikou_id from gks_eshop_products_income limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshop_products_income` 
      ADD COLUMN `acc_eidos_parastatikou_id` INTEGER NOT NULL DEFAULT 0 AFTER `product_id`,
      ADD INDEX `acc_eidos_parastatikou_id`(`acc_eidos_parastatikou_id`);");
    }
    $sql="select acc_eidos_parastatikou_id from gks_eshop_products_expenses limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshop_products_expenses` 
      ADD COLUMN `acc_eidos_parastatikou_id` INTEGER NOT NULL DEFAULT 0 AFTER `product_id`,
      ADD INDEX `acc_eidos_parastatikou_id`(`acc_eidos_parastatikou_id`);");
    }
    

    gks_run_sql("update gks_orders set affect_balance_pros=1 where affect_balance_pros=0");
    



    $sql="select perm_company_ids from gks_print_forms limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_print_forms` 
      ADD COLUMN `perm_company_ids` TEXT DEFAULT NULL,
      ADD COLUMN `perm_acc_journal_ids` TEXT DEFAULT NULL,
      ADD COLUMN `perm_acc_seires_ids` TEXT DEFAULT NULL;");
    }
    
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_users_templates` (
      `id_users_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `user_id` int(11) NOT NULL DEFAULT '0',
      `object_name` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `template_id` int(11) NOT NULL DEFAULT '0',
      `template_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      PRIMARY KEY (`id_users_template`) USING BTREE,
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `user_id` (`user_id`),
      KEY `object_name` (`object_name`),
      KEY `template_id` (`template_id`),
      KEY `template_name` (`template_name`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    $sql="select * from gks_permission_object where id_permission_object=575";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
       (575,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Πωλήσεις',0,'gks_orders_pivot3','Αναφορά - Pivot Table - Παραγγελίες με Είδη',575),
       (685,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_acc_inv_pivot4','Αναφορά - Pivot Table - Παραστατικά',685),
       (686,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_acc_inv_pivot5','Αναφορά - Pivot Table - Παραστατικά με Είδη',686),
       (705,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_acc_pay_pivot6','Αναφορά - Pivot Table - Πληρωμές',705),
       (706,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_acc_pay_pivot7','Αναφορά - Pivot Table - Πληρωμές με Είδη',706),
       (505,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_whi_mov_pivot8','Αναφορά - Pivot Table - Δελτία',505),
       (506,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_whi_mov_pivot9','Αναφορά - Pivot Table - Δελτία με Είδη',506);");
    }
    
    
    $sql="select sind_acc_eidos_parastatikou_id from gks_aade_xarakt_sindiasmoi_esodon limit 1";
    $result = $db_link->query($sql);
    if (!$result) {
      gks_run_sql("ALTER TABLE `gks_aade_xarakt_sindiasmoi_esodon` 
      ADD COLUMN `sind_acc_eidos_parastatikou_id` INTEGER NOT NULL DEFAULT 0 AFTER `eidos_parastatikou_aade_code`,
      ADD INDEX `sind_acc_eidos_parastatikou_id`(sind_acc_eidos_parastatikou_id);");
      
      gks_run_sql("ALTER TABLE `gks_aade_xarakt_sindiasmoi_eksodon` 
      ADD COLUMN `sind_acc_eidos_parastatikou_id` INTEGER NOT NULL DEFAULT 0 AFTER `eidos_parastatikou_aade_code`,
      ADD INDEX `sind_acc_eidos_parastatikou_id`(sind_acc_eidos_parastatikou_id);");
      
      gks_run_sql("UPDATE gks_aade_xarakt_sindiasmoi_esodon
      LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_esodon.eidos_parastatikou_aade_code = gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code
      SET gks_aade_xarakt_sindiasmoi_esodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
      WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou>0;");
      
      gks_run_sql("UPDATE gks_aade_xarakt_sindiasmoi_eksodon
      LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_eksodon.eidos_parastatikou_aade_code = gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code
      SET gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
      WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou>0;");
      
      gks_run_sql("UPDATE gks_aade_xarakt_sindiasmoi_eksodon
      SET sind_acc_eidos_parastatikou_id = 502
      WHERE eidos_parastatikou_aade_code='1.1'
      AND sind_acc_eidos_parastatikou_id=11");
      
      gks_run_sql("UPDATE gks_aade_xarakt_sindiasmoi_eksodon
      SET sind_acc_eidos_parastatikou_id = 504
      WHERE eidos_parastatikou_aade_code='2.1'
      AND sind_acc_eidos_parastatikou_id=21");
      
      gks_run_sql("UPDATE gks_aade_xarakt_sindiasmoi_eksodon
      SET sind_acc_eidos_parastatikou_id = 503
      WHERE eidos_parastatikou_aade_code='5.2'
      AND sind_acc_eidos_parastatikou_id=52");
      
      gks_run_sql("INSERT INTO gks_aade_xarakt_sindiasmoi_eksodon (
        id_aade_xarakt_sindiasmoi_eksodon,
        mydate_add,
        mydate_edit,
        user_id_add,
        user_id_edit,
        myip,
        sind_acc_eidos_parastatikou_id,
        aade_katigoria_xarakt_eksodon_code,
        aade_typos_xarakt_eksodon_code, yearprevnext
      )
      SELECT id_aade_xarakt_sindiasmoi_eksodon+3000 AS mynew_id,
        '2020-01-01' AS mydate_add,
        '2020-01-01' AS mydate_edit,
        2 AS user_id_add,
        2 AS user_id_edit,
        '127.0.0.1' AS myip,
        505 AS sind_acc_eidos_parastatikou_id,
        gks_aade_xarakt_sindiasmoi_eksodon.aade_katigoria_xarakt_eksodon_code,
        gks_aade_xarakt_sindiasmoi_eksodon.aade_typos_xarakt_eksodon_code,
        gks_aade_xarakt_sindiasmoi_eksodon.yearprevnext
      FROM gks_aade_xarakt_sindiasmoi_eksodon
      WHERE gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id=503;");

      
      
    }
        
    $sql="select from_aade_import_user_fpa from gks_acc_inv_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      // like from_aade_import_lock
      gks_run_sql("ALTER TABLE `gks_acc_inv_products` ADD COLUMN `from_aade_import_user_fpa` TINYINT NOT NULL DEFAULT 0;");
    }
        
   
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_product_lots` (
      `id_lot_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `lotproduct_id` int(11) NOT NULL DEFAULT '0',
      `lot_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `lot_descr` text COLLATE utf8mb4_unicode_520_ci,
      `lot_date_production` datetime DEFAULT NULL,
      `lot_date_expire` datetime DEFAULT NULL,
      `lot_disabled` tinyint(4) NOT NULL DEFAULT '0',
      `lot_sortorder` int(11) NOT NULL DEFAULT '1000',
      PRIMARY KEY (`id_lot_product`) USING BTREE,
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `lotproduct_id` (`lotproduct_id`),
      KEY `lot_name` (`lot_name`),
      KEY `lot_date_production` (`lot_date_production`),
      KEY `lot_date_expire` (`lot_date_expire`),
      KEY `lot_disabled` (`lot_disabled`),
      KEY `lot_sortorder` (`lot_sortorder`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
      
      
    gks_run_sql("ALTER TABLE `gks_permission_object` 
    MODIFY COLUMN `table_name` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL;");

    
    $sql="select * from gks_permission_object where id_permission_object=535";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
       (535,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_eshop_product_lots','Πατρίδες - Serial Numbers',534),
       (536,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_whi_mov_balance_lots_serials','Υπόλοιπα Παρτίδων-Serial Numbers',535),
       (537,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_whi_mov_balance_lots_serials_history','Ιστορικό Παρτίδων-Serial Numbers',536),
       (538,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη',0,'gks_whi_mov_balance_lots_serials_apografi','Απογραφή Παρτίδων-Serial Numbers',537),
       (711,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_gsis_check','Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων',711),
       (712,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_vies_check','VIES ΕΕ Επαλήθευση αριθ. ΦΠΑ',712),
       (1110,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks__hotel_plan','Πλάνο',1110),
       (1120,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_reservation','Κρατήσεις',1120),
       (1130,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_folio','Καρτέλα',1130),
       (1140,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_availability','Διαθεσιμότητα',1140),
       (1150,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_price','Τιμές',1150),
       (1160,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_room_type','Τύποι Δωματίων',1160),
       (1170,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_room','Δωμάτια',1170),
       (1180,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel_floor','Όροφος',1180),
       (1200,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Ξενοδοχείο',0,'gks_hotel','Ξενοδοχεία',1200);");
    }
    gks_run_sql("update gks_permission_object set sortorder=538 where id_permission_object=505");
    gks_run_sql("update gks_permission_object set sortorder=539 where id_permission_object=506");
    gks_run_sql("delete from gks_permission_object where id_permission_object=130");
    

    $sql="select * from gks_custom_table where id_custom_table=35";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
       (35,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Αποθήκη - Πατρίδες - Serial Numbers','gks_eshop_product_lots','id_lot_product','lot_product_id',0,'whi',1520);");
    }

    
    $sql="select product_lot_serial from gks_eshop_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshop_products` 
      ADD COLUMN `product_lot_serial` VARCHAR(16) DEFAULT NULL,
      ADD INDEX `product_lot_serial`(`product_lot_serial`);");
    }

    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_orders_products_lots` (
      `id_order_product_lots` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `order_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_quantity` double NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_order_product_lots`),
      KEY `order_product_id` (`order_product_id`),
      KEY `lot_product_id` (`lot_product_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_products_lots` (
      `id_acc_inv_product_lots` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `acc_inv_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_quantity` double NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_acc_inv_product_lots`),
      KEY `acc_inv_product_id` (`acc_inv_product_id`),
      KEY `lot_product_id` (`lot_product_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_products_lots` (
      `id_whi_mov_product_lots` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `whi_mov_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_quantity` double NOT NULL DEFAULT '0',
      `apografi_lot_posotitaonhand` double DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_whi_mov_product_lots`),
      KEY `whi_mov_product_id` (`whi_mov_product_id`),
      KEY `lot_product_id` (`lot_product_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_production_sintagi_product_lots_serials` (
      `id_production_sintagi_product_lots` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `production_sintagi_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_id` int(11) NOT NULL DEFAULT '0',
      `lot_product_quantity` double NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_production_sintagi_product_lots`),
      KEY `production_sintagi_product_id` (`production_sintagi_product_id`),
      KEY `lot_product_id` (`lot_product_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_balance_lots_serials` (
      `lot_product_id` int(11) NOT NULL DEFAULT '0',
      `product_monada_id` int(11) NOT NULL DEFAULT '0',
      `total_balance` double NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`lot_product_id`) USING BTREE,
      KEY `product_monada_id` (`product_monada_id`),
      KEY `total_balance` (`total_balance`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



    $sql="select error_text from gks_gsis_check limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_gsis_check` ADD COLUMN `error_text` TEXT DEFAULT NULL AFTER `valid`;");
      
      gks_run_sql("ALTER TABLE `gks_gsis_check`
      ADD INDEX `user_id`(`user_id`),
      ADD INDEX `valid`(`valid`),
      ADD INDEX `error_text`(`error_text`(190)),
      ADD INDEX `response_doy_descr`(`response_doy_descr`(190)),
      ADD INDEX `response_i_ni_flag_descr`(`response_i_ni_flag_descr`),
      ADD INDEX `response_deactivation_flag_descr`(`response_deactivation_flag_descr`(190)),
      ADD INDEX `response_firm_flag_descr`(`response_firm_flag_descr`),
      ADD INDEX `response_onomasia`(`response_onomasia`(190)),
      ADD INDEX `response_commer_title`(`response_commer_title`(190)),
      ADD INDEX `response_legal_status_descr`(`response_legal_status_descr`(190)),
      ADD INDEX `response_postal_address`(`response_postal_address`(190)),
      ADD INDEX `response_postal_address_no`(`response_postal_address_no`),
      ADD INDEX `response_postal_zip_code`(`response_postal_zip_code`),
      ADD INDEX `response_postal_area_description`(`response_postal_area_description`(190)),
      ADD INDEX `response_normal_vat_system_flag`(`response_normal_vat_system_flag`);");
      
    }

    $sql="select error_text from gks_vies_check limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_vies_check` 
      ADD COLUMN `error_text` TEXT DEFAULT NULL AFTER `response_valid`,
      ADD COLUMN `response_raw` TEXT DEFAULT NULL;");


      gks_run_sql("ALTER TABLE `gks_vies_check`
      ADD INDEX `user_id`(`user_id`),
      ADD INDEX `response_valid`(`response_valid`),
      ADD INDEX `error_text`(`error_text`(190)),
      ADD INDEX `response_countryCode`(`response_countryCode`),
      ADD INDEX `response_vatNumber`(`response_vatNumber`),
      ADD INDEX `response_traderName`(`response_traderName`(190)),
      ADD INDEX `response_traderCompanyType`(`response_traderCompanyType`),
      ADD INDEX `response_traderAddress`(`response_traderAddress`(190));");
    }
    
    $sql="select lots_and_serials_analysis from gks_print_forms limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_print_forms` ADD COLUMN `lots_and_serials_analysis` LONGTEXT DEFAULT NULL AFTER `foroi_analysis`;");
    }
          
    
    gks_run_sql("update gks_custom_table set custom_table_descr='Ξενοδοχείο - Κρατήσεις' where id_custom_table=13");
    gks_run_sql("update gks_custom_table set custom_table_descr='Ξενοδοχείο - Δωμάτια' where id_custom_table=14");
     
     
     
    $sql="select hotel_id from gks_hotel_floor limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_floor` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_floor`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }

    $sql="select hotel_sortorder from gks_hotel limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel` ADD COLUMN `hotel_sortorder` INTEGER NOT NULL DEFAULT 1000,
      ADD INDEX `hotel_sortorder`(`hotel_sortorder`);");
    }
    $sql="select hotel_id from gks_hotel_availability limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_availability` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_availability`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }
    $sql="select hotel_id from gks_hotel_availability_day limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_availability_day` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_availability_day`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }


    $sql="select hotel_id from gks_hotel_price limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_price` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_price`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }

    $sql="select hotel_id from gks_hotel_price_day limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_price_day` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_price_day`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }

    $sql="select hotel_id from gks_hotel_room limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_room`,
      ADD INDEX `hotel_id`(`hotel_id`),
      ADD COLUMN `room_sortorder` INTEGER NOT NULL DEFAULT 1000,
      ADD INDEX `room_sortorder`(`room_sortorder`)");
    }
    $sql="select room_type_sortorder from gks_hotel_room_type limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room_type` ADD COLUMN `room_type_sortorder` INTEGER NOT NULL DEFAULT 1000,
      ADD INDEX `room_type_sortorder`(`room_type_sortorder`)");
    }
    
    
    $sql="select hotel_id from gks_hotel_folio limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_folio` ADD COLUMN `hotel_id` INTEGER NOT NULL DEFAULT 0 AFTER `id_hotel_folio`,
      ADD INDEX `hotel_id`(`hotel_id`);");
    }

//    $read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
//    if (strpos($read_file, 'GKS_ERP_APP_PATH_EXE') === false) {
//      echo '_current/_config.php file not nontains GKS_ERP_APP_PATH_EXE<br>';die();}



    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app` (
      `id_erp_app` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_name` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_descr` text COLLATE utf8mb4_unicode_520_ci,
      `erp_app_token` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_disabled` tinyint(4) NOT NULL DEFAULT '0',
      `erp_app_url` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_url2ip` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_port` int(11) NOT NULL DEFAULT '55555',
      `erp_app_lan_ip` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_wan_ip` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `erp_app_last_ping` datetime DEFAULT NULL,
      `erp_app_sortorder` int(11) NOT NULL DEFAULT '1000',
      `last_ping_id` int(11) NOT NULL DEFAULT '0',
      `erp_app_local_printers` longtext COLLATE utf8mb4_unicode_520_ci,
      PRIMARY KEY (`id_erp_app`) USING BTREE,
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `erp_app_name` (`erp_app_name`),
      KEY `erp_app_token` (`erp_app_token`),
      KEY `erp_app_disabled` (`erp_app_disabled`),
      KEY `erp_app_url` (`erp_app_url`),
      KEY `erp_app_port` (`erp_app_port`),
      KEY `erp_app_lan_ip` (`erp_app_lan_ip`),
      KEY `erp_app_wan_ip` (`erp_app_wan_ip`),
      KEY `erp_app_last_ping` (`erp_app_last_ping`),
      KEY `erp_app_sortorder` (`erp_app_sortorder`),
      KEY `erp_app_descr` (`erp_app_descr`(190))
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    



    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app_ping` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `erp_app_id` int(11) NOT NULL DEFAULT '0',
      `mydate` datetime DEFAULT NULL,
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `pctime` datetime DEFAULT NULL,
      `pcusername` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `pcname` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `rand1` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `ticks` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `winver` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `appver` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `lanips` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `hdwd` int(11) NOT NULL DEFAULT '0',
      `screw` int(11) NOT NULL DEFAULT '0',
      `screh` int(11) NOT NULL DEFAULT '0',
      `mac` text COLLATE utf8mb4_unicode_520_ci,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `mydate` (`mydate`),
      KEY `pcname` (`pcname`),
      KEY `pcusername` (`pcusername`),
      KEY `myip` (`myip`),
      KEY `erp_app_id` (`erp_app_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    

    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_app_log` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate` datetime NOT NULL,
      `erp_app_id` int(11) NOT NULL DEFAULT '0',
      `mygroup` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `message` text COLLATE utf8mb4_unicode_520_ci,
      `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `mydate` (`mydate`),
      KEY `erp_app_id` (`erp_app_id`),
      KEY `mygroup` (`mygroup`) USING BTREE
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    

    $sql="select * from gks_permission_object where id_permission_object=381";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
       (381,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_erp_app','gks ERP App Desktop',381);");
    }
    
    $sql="select * from gks_custom_table where id_custom_table=36";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`) VALUES 
       (36,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - gks ERP App Desktop','gks_erp_app','id_erp_app','erp_app_id',0,'base',150);");
    }    

    $sql="select erp_app_id from gks_acc_seires limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_seires` 
      ADD COLUMN `erp_app_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `erp_app_dest` VARCHAR(64) DEFAULT NULL,
      ADD COLUMN `erp_app_dest_printer` VARCHAR(190) DEFAULT NULL,
      ADD COLUMN `erp_app_dest_printer_copies` INTEGER NOT NULL DEFAULT 1,
      ADD COLUMN `erp_app_dest_folder` VARCHAR(190) DEFAULT NULL,
      ADD INDEX `erp_app_id`(`erp_app_id`),
      ADD INDEX `erp_app_dest`(`erp_app_dest`);");
    }



    $sql="select room_type_child_kounies from gks_hotel_room_type limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room_type` 
      ADD COLUMN `room_type_child_kounies` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `room_type_extra_beds` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `room_type_child_kounies`(`room_type_child_kounies`),
      ADD INDEX `room_type_extra_beds`(`room_type_extra_beds`);");
    }


   $sql="select num_child_kounies from gks_hotel_reservation limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation`
      ADD COLUMN `num_child_kounies` INTEGER NOT NULL DEFAULT 0 AFTER `childs_ages_list`,
      ADD COLUMN `child_kounies_ages_list` TEXT DEFAULT NULL AFTER `num_child_kounies`,
      ADD COLUMN `num_extra_beds` INTEGER NOT NULL DEFAULT 0 AFTER `child_kounies_ages_list`,
      ADD COLUMN `extra_beds_ages_list` TEXT DEFAULT NULL AFTER `num_extra_beds`;");
    }
     

    $sql="select rnum_child_kounies from gks_hotel_reservation_room limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation_room`
      ADD COLUMN `rnum_child_kounies` INTEGER NOT NULL DEFAULT 0 AFTER `rchilds_ages_list`,
      ADD COLUMN `rchild_kounies_ages_list` TEXT DEFAULT NULL AFTER `rnum_child_kounies`,
      ADD COLUMN `rnum_extra_beds` INTEGER NOT NULL DEFAULT 0 AFTER `rchild_kounies_ages_list`,
      ADD COLUMN `rextra_beds_ages_list` TEXT DEFAULT NULL AFTER `rnum_extra_beds`;");
    }
      
    gks_run_sql("ALTER TABLE `gks_users_groups` MODIFY COLUMN `group_title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL");
    
    
    gks_run_sql("update gks_acc_eidi_parastatikon set
    eidos_parastatikou_type_id=1,
    eidos_parastatikou_need_prev=1,
    eidos_parastatikou_has_fpa=0,
    eidos_parastatikou_has_othertaxes='ot',
    eidos_parastatikou_has_esoda=1,
    eidos_parastatikou_need_afm=1
    where id_acc_eidos_parastatikou=82");
     
    
    $sql="select dimotikos_foros_for_acc_inv_id from gks_acc_inv limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_inv` 
      ADD COLUMN `dimotikos_foros_for_acc_inv_id` INTEGER NOT NULL DEFAULT 0 AFTER `credit_memo_for_acc_inv_id`,
      ADD INDEX `dimotikos_foros_for_acc_inv_id`(`dimotikos_foros_for_acc_inv_id`);");
    }
    


    $sql="select affect_balance from gks_hotel_reservation limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
      ADD COLUMN `gks_price_netfpa` double NOT NULL DEFAULT '0' after `gks_price_fpa`,
      ADD COLUMN `affect_balance` tinyint(4) NOT NULL DEFAULT '0',
      ADD COLUMN `affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT '1',
      ADD COLUMN `affect_balance_all_poso_type` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT 'price_net',
      ADD COLUMN `affect_balance_poso` double NOT NULL DEFAULT '0',
      ADD COLUMN `affect_balance_pros` tinyint(4) NOT NULL DEFAULT '0',
      ADD COLUMN `assigned_id` int(11) NOT NULL DEFAULT '0',
      ADD COLUMN `crm_channel_id` int(11) NOT NULL DEFAULT '0',
      ADD COLUMN `crm_channel_contact_id` int(11) NOT NULL DEFAULT '0',
      ADD COLUMN `crm_channel_campain_id` int(11) NOT NULL DEFAULT '0',
      ADD COLUMN `crm_channel_url` text COLLATE utf8mb4_unicode_520_ci,
      ADD COLUMN `crm_channel_text` text COLLATE utf8mb4_unicode_520_ci,
      ADD INDEX `affect_balance` (`affect_balance`),
      ADD INDEX `assigned_id` (`assigned_id`),
      ADD INDEX `crm_channel_id` (`crm_channel_id`) USING BTREE,
      ADD INDEX `crm_channel_contact_id` (`crm_channel_contact_id`) USING BTREE,
      ADD INDEX `crm_channel_campain_id` (`crm_channel_campain_id`),
      ADD INDEX `crm_channel_text` (`crm_channel_text`(240)),
      ADD INDEX `crm_channel_url` (`crm_channel_url`(240));");
    }
    

    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_pay_poso_hotel_reservation` (
      `id_acc_pay_method_poso_hotel_reservation` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `acc_pay_id` int(11) NOT NULL DEFAULT '0',
      `hotel_reservation_id` int(11) NOT NULL DEFAULT '0',
      `poso` double NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_acc_pay_method_poso_hotel_reservation`),
      KEY `user_id_add` (`user_id_add`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `acc_pay_id` (`acc_pay_id`),
      KEY `hotel_reservation_id` (`hotel_reservation_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    

    $sql="select reservation_journal_id from gks_hotel_reservation limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation`
      ADD COLUMN `reservation_journal_id` int(11) NOT NULL DEFAULT '0' after order_id,
      ADD COLUMN `reservation_seira_id` int(11) NOT NULL DEFAULT '0' after reservation_journal_id,
      ADD COLUMN `reservation_seira_code` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after reservation_seira_id,
      ADD COLUMN `reservation_number_int` int(11) NOT NULL DEFAULT '0' after reservation_seira_code,
      ADD COLUMN `reservation_number_str` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after reservation_number_int,
      ADD COLUMN `reservation_ekdosi_date` datetime DEFAULT NULL after reservation_number_str,
      ADD INDEX `reservation_journal_id` (`reservation_journal_id`),
      ADD INDEX `reservation_seira_id` (`reservation_seira_id`),
      ADD INDEX `reservation_seira_code` (`reservation_seira_code`),
      ADD INDEX `reservation_number_int` (`reservation_number_int`),
      ADD INDEX `reservation_number_str` (`reservation_number_str`),
      ADD INDEX `reservation_ekdosi_date` (`reservation_ekdosi_date`);");
    }
    
    
    $sql="select hotel_efd_product_id from gks_hotel limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel` 
      ADD COLUMN `hotel_efd_product_id` INTEGER NOT NULL DEFAULT 0 AFTER `hotel_sortorder`,
      ADD INDEX `hotel_efd_product_id`(`hotel_efd_product_id`),
      drop column hotel_telos_dianiktereusis_id;");
    }
    

    $sql="select hotel_template_eidos_descr from gks_hotel limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel` 
      ADD COLUMN `hotel_template_eidos_descr` TEXT DEFAULT NULL AFTER `hotel_efd_product_id`,
      ADD COLUMN `hotel_template_efd_descr` TEXT DEFAULT NULL AFTER `hotel_template_eidos_descr`,
      ADD COLUMN `hotel_template_woo_descr` TEXT DEFAULT NULL AFTER `hotel_template_eidos_descr`;");
      
      $hotel_template_eidos_descr=
      '{room_name [Δωμάτιο: %%'."\r\n".']}'.
      '{room_type [Τύπος δωματίου: %%'."\r\n".']}'.
      '{check_in_dtw [Από: %%'."\r\n".']}'.
      '{check_out_dtw [Έως: %%'."\r\n".']}'.
      '{days [Διανυκτερεύσεις: %%'."\r\n".']}'.
      '{adults [hide:zero][Ενήλικες: %%'."\r\n".']}'.
      '{childs [hide:zero][Παιδιά: %%'."\r\n".']}'.
      '{visitors [hide:zero][Επισκέπτες: %%'."\r\n".']}'.
      '{child_kounies [hide:zero][Βρεφικά κρεβάτια: %%'."\r\n".']}'.
      '{extra_beds [hide:zero][Επιπλέον κρεβάτια: %%]}'; //."\r\n".

      $hotel_template_efd_descr=
      '{room_name [Δωμάτιο: %%'."\r\n".']}'.
      '{room_type [Τύπος δωματίου: %%'."\r\n".']}'.
      '{check_in_dtw [Από: %%'."\r\n".']}'.
      '{check_out_dtw [Έως: %%'."\r\n".']}'.
      '{days [Διανυκτερεύσεις: %%'."\r\n".']}'.
      '{adults [hide:zero][Ενήλικες: %%'."\r\n".']}'.
      '{childs [hide:zero][Παιδιά: %%'."\r\n".']}'.
      '{visitors [hide:zero][Επισκέπτες: %%'."\r\n".']}'.
      '{child_kounies [hide:zero][Βρεφικά κρεβάτια: %%'."\r\n".']}'.
      '{extra_beds [hide:zero][Επιπλέον κρεβάτια: %%]}'; //."\r\n".

      $hotel_template_woo_descr=
      '{room_name [Δωμάτιο: %%'."\r\n".']}'.
      '{room_type [Τύπος δωματίου: %%'."\r\n".']}'.
      '{check_in_dtw [Από: %%'."\r\n".']}'.
      '{check_out_dtw [Έως: %%'."\r\n".']}'.
      '{days [Διανυκτερεύσεις: %%'."\r\n".']}'.
      '{adults [hide:zero][Ενήλικες: %%'."\r\n".']}'.
      '{childs [hide:zero][Παιδιά: %%'."\r\n".']}'.
      '{visitors [hide:zero][Επισκέπτες: %%'."\r\n".']}'.
      '{child_kounies [hide:zero][Βρεφικά κρεβάτια: %%'."\r\n".']}'.
      '{extra_beds [hide:zero][Επιπλέον κρεβάτια: %%]}'; //."\r\n".


      gks_run_sql("update gks_hotel set 
      hotel_template_eidos_descr='".$db_link->escape_string($hotel_template_eidos_descr)."',
      hotel_template_efd_descr='".$db_link->escape_string($hotel_template_efd_descr)."',
      hotel_template_woo_descr='".$db_link->escape_string($hotel_template_woo_descr)."'");
      
    }
    
    $sql="select * from gks_print_forms where id_print_form=100";
    $result = gks_run_sql($sql);
    if ($GKS_HOTEL_BACKEND && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_print_forms` (`id_print_form`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_descr`,`is_disable`,`sortorder`,`gks_lang`,`file_type`,`grayscale`,`zoom`,`dpi`,`size_name`,`width_cm`,`height_cm`,`is_landscape`,`margin_cm_left`,`margin_cm_right`,`margin_cm_top`,`margin_cm_bottom`,`logo_url`,`page_header`,`page_footer`,`page_background_url`,`page_background_opacity`,`form_header`,`form_footer`,`details_header`,`details_body`,`details_footer`,`fpa_analysis`,`foroi_analysis`,`lots_and_serials_analysis`,`file_thump_url`,`localization_set_id`,`perm_company_ids`,`perm_acc_journal_ids`,`perm_acc_seires_ids`) VALUES 
      (100,'2020-01-01 00:00:00','2022-03-03 10:43:47',1,1,'192.168.1.202','2022-03-03 10:43:47','Απόδειξης Είσπραξης Φόρου Διαμονής',0,100,'el-GR','pdf',0,1,600,'A4',21,29.7,0,2,2,2,2,'',
      '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 22pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 30%; vertical-align: bottom;\">{logo_url [<a href=\"{site_url}\" target=\"_blank\" rel=\"noopener\"><img style=\"max-height: 100%; height: 50px;\" src=\"%%\" border=\"0\" /></a>]}</td>\n<td style=\"width: 70%; vertical-align: bottom; text-align: right;\">{company_tagline}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
      '<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\">Τραπεζικός λογαριασμός:<br />{company_url [ <em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a>]} {company_phone [ <em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {company_email [ <em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a>]} <br />Σελίδα {page} από {pages}<br />Powered by <a href=\"https://www.gks.gr\" target=\"_blank\" rel=\"noopener\">www.gks.gr</a></td>\n</tr>\n</tbody>\n</table>',
      '',1,
      '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">Εκδότης<br />{company_title [<strong>%%</strong><br />]} {company_eponimia [%%<br />]} {company_epaggelma [%%<br />]} {company_odos [Διεύθυνση: %%{company_tk [, %%]}<br />]} {company_poli [%%]}{company_nomos_descr [, %%]}{company_country_name [, %%<br />]} {company_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {company_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {company_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {company_afm [ΑΦΜ: {company_country_ee}%%<br />]} {company_doy [ΔΟΥ: %%<br />]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{person_label [%%<br />]} {person_first_name} {person_last_name} <br />{person_mobile [%%<br />]} {person_title [<strong>%%</strong><br />]} {person_eponimia [%%<br />]} {person_epaggelma [%%<br />]} {person_odos [Διεύθυνση: %%{person_tk [, %%]}<br />]} {person_poli [%%]}{person_nomos_descr [, %%]}{person_country_name [, %%<br />]} {person_mobile [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {person_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {person_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {person_afm [ΑΦΜ: {person_country_ee}%%<br />]} {person_doy [ΔΟΥ: %%<br />]}\n<div>{hide} <strong>Τόπος παράδοσης</strong><br />{dest_name [Όνομα: %%<br />]} {dest_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {dest_odos [Διεύθυνση: %%{dest_tk [, %%]}<br />]} {dest_poli [%%]}{dest_nomos_descr [, %%]}{dest_country_name [, %%<br />]}</div>\n</td>\n</tr>\n</tbody>\n</table>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>',
      '<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div>{doc_note_doc}</div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 50%; vertical-align: top; text-align: center;\">{person_label [%%<br />]}</td>\n<td style=\"width: 50%; vertical-align: top; text-align: center; min-height: 100px;\">Εκδότης</td>\n</tr>\n</tbody>\n</table>',
      '<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"font-family: Arial; font-size: 16pt; text-align: center; font-weight: bold;\">{doc_title_pre [%% : ]}{doc_title}</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_date [Ημερομηνία: %%<br />]} {doc_seira [Σειρά: %%<br />]} {doc_number [hide:zero][Αριθμός: %%<br />]} {doc_mark [ΜΑΡΚ: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_skopos_diakinisis [Σκοπός Διακίνησης: %%<br />]} {doc_tropos_pliromis [Τρόπος Πληρωμής: %%<br />]} {doc_tropos_apostolis [Τρόπος Αποστολής: %%<br />]} {doc_arithmos_aposolis [Αριθμός Αποστολής: %%<br />]} {doc_arithmos_oximatos [Αριθμός Μεταφορικού Μέσου: %%<br />]} {doc_enarji_apostolis [Ώρα Έναρξης Αποστολής: %%<br />]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_canceled_display};\">Το ακυρωτικό παραστατικό αφορά το παραστατικό με τα παρακάτω στοιχεία:</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_canceled_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_date [Ημερομηνία: %%<br />]} {doc_canceled_seira [Σειρά: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_canceled_number [hide:zero][Αριθμός: %%<br />]} {doc_canceled_mark [ΜΑΡΚ: %%]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"font-family: Arial; font-size: 14pt; text-align: center; font-weight: bold; display: {doc_credit_display};\">Το πιστωτικό παραστατικό αφορά το παραστατικό με τα παρακάτω στοιχεία:</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3; display: {doc_credit_display};\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_date [Ημερομηνία: %%<br />]} {doc_credit_seira [Σειρά: %%]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_credit_number [hide:zero][Αριθμός: %%<br />]} {doc_credit_mark [ΜΑΡΚ: %%]}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
      '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 50%; text-align: center;\" nowrap=\"nowrap\">Περιγραφή</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 10%; text-align: center;\" nowrap=\"nowrap\">Διανυκτερεύσεις</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 30%; text-align: left;\" nowrap=\"nowrap\">Κατηγορία</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 10%; text-align: center;\" nowrap=\"nowrap\">Ποσό</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_aa}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 50%;\">{eidos_descr}</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 10%;\" nowrap=\"nowrap\">{eidos_quantity}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 30%;\" nowrap=\"nowrap\">\n<p>{eidos_loipoi_foroi_descr}<br />ανά διανυκτέρευση</p>\n</td>\n<td style=\"border: 1px solid gray; text-align: center; width: 10%;\" nowrap=\"nowrap\">{eidos_loipoi_foroi_poso [%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid gray; text-align: left; width: 100%;\" colspan=\"5\">{hide}{eidos_comments}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left; width: 50%;\" colspan=\"2\" nowrap=\"nowrap\"><strong>Σύνολα</strong></td>\n<td style=\"border: 1px solid gray; text-align: center; width: 10%;\" nowrap=\"nowrap\">{doc_posotita}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 40%;\" colspan=\"2\" nowrap=\"nowrap\"><br /></td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; text-align: right; vertical-align: top; padding-left: 0px; padding-top: 0px; padding-right: 20px; width: 60%;\" colspan=\"3\" nowrap=\"nowrap\"><br /></td>\n<td style=\"text-align: right; width: 10%;\" nowrap=\"nowrap\"><strong>Σύνολο:</strong></td>\n<td style=\"text-align: right; width: 30%;\" nowrap=\"nowrap\"><strong>{doc_priceall_total [%%][format:cs]} </strong></td>\n</tr>\n</tfoot>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
      '','','','',NULL,1359209,'','','')");
     
      gks_run_sql("INSERT INTO `gks_print_objects_forms` (`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_id`,`print_object_id`) VALUES 
      ('2020-01-01','2020-01-01',2,2,'127.0.0.1','2020-01-01',100,2)");
    }



    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_reservation_log` (
      `id_hotel_reservation_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `hotel_reservation_id` int(11) NOT NULL DEFAULT '0',
      `add_date` datetime NOT NULL,
      `user_id` int(11) NOT NULL DEFAULT '0',
      `sxolio` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_hotel_reservation_log`) USING BTREE,
      KEY `hotel_reservation_id` (`hotel_reservation_id`),
      KEY `user_id` (`user_id`),
      KEY `add_date` (`add_date`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    

    $sql="select reservation_id from gks_acc_seires_auto_numbers limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_seires_auto_numbers` 
      ADD COLUMN `reservation_id` int(11) NOT NULL DEFAULT '0',
      ADD INDEX `reservation_id`(`reservation_id`);");
    }


    
    $sql="select hotel_website_key from gks_hotel limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel` 
      ADD COLUMN `hotel_website_key` VARCHAR(128) DEFAULT NULL;");
    }
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_erp_cookie` (
      `gks_erp_cookie_id` VARCHAR(128) NOT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `mydate_add` datetime NOT NULL,
      `mydate_edit` datetime NOT NULL,
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `data` LONGTEXT DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`gks_erp_cookie_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
       

    gks_run_sql("ALTER TABLE `gks_acc_inv` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_acc_pay` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_hotel_reservation` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_hr_user` MODIFY COLUMN `candidate_session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_orders` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_users_cv` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_users_extra_address` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");
    gks_run_sql("ALTER TABLE `gks_whi_mov` MODIFY COLUMN `session_id` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


    $sql="select hotel_use_checkout_system from gks_hotel limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel` 
      ADD COLUMN `hotel_use_checkout_system` VARCHAR(32) DEFAULT NULL");
    }
    

    $sql="select update_state_gks_reservation from gks_eshops limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshops`
      ADD COLUMN `update_state_gks_reservation` TEXT DEFAULT NULL;");
    }
    

    $sql="select woo_eshop_id from gks_hotel_reservation limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
      ADD COLUMN `woo_eshop_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `woo_order_id` INTEGER NOT NULL DEFAULT 0,
      ADD COLUMN `woo_guid` VARCHAR(64) DEFAULT NULL,
      ADD INDEX `woo_eshop_id`(`woo_eshop_id`),
      ADD INDEX `woo_order_id`(`woo_order_id`),
      ADD INDEX `woo_guid`(`woo_guid`)");
    }
    
    

    $sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=1100";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      
      gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon` (`id_acc_eidos_parastatikou`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`parent_id`,`eidos_parastatikou_type_id`,`eidos_parastatikou_need_prev`,`eidos_parastatikou_has_fpa`,`eidos_parastatikou_has_posotita`,`eidos_parastatikou_has_othertaxes`,`eidos_parastatikou_has_esoda`,`eidos_parastatikou_has_eksoda`,`eidos_parastatikou_need_afm`,`eidos_parastatikou_aade_code`,`eidos_parastatikou_descr`,`eidos_parastatikou_balance_pros`,`eidos_parastatikou_stock_pros`,`eidos_parastatikou_whi_type_id`,`def_prefix`,`def_suffix`,`sortorder`,`is_selectable`,`odbc`,`credit_acc_eidos_parastatikou_id`,`rbs_code_a`,`import_apo_allon`) VALUES 
     (1100,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',0,0,0,0,0,NULL,0,0,0,NULL,'Ξενοδοχείο',0,0,0,NULL,NULL,11100,0,'2022-03-04 14:36:32',0,0,NULL),
     (1200,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',1100,1100,0,1,1,'wh,ot,sd,fe,dd',0,0,1,NULL,'Κρατήσεις',1,0,0,NULL,NULL,11200,1,'2022-03-04 14:40:25',0,0,NULL),
     (1300,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',1100,1100,0,1,1,'wh,ot,sd,fe,dd',0,0,1,NULL,'Καρτέλα Διαμένοντος',1,0,0,NULL,NULL,11300,1,'2022-03-04 14:40:25',0,0,NULL);");
    
    }    
    $sql="select * from gks_acc_eidi_parastatikon_types where id_acc_eidi_parastatikon_type=1100";
    $result = gks_run_sql($sql);
    if ($result && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon_types` (`id_acc_eidi_parastatikon_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`acc_eidi_parastatikon_type_descr`,`antisimvalomenos_label`,`antisimvalomenos_label_en`,`sortorder`,`odbc`) VALUES 
     (1100,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','Κρατήσεις-Καρτέλες','Πελάτης','Customer',1100,'2022-03-04 14:37:36');");
    }    


    $sql="select woo_item_id from gks_hotel_reservation_room limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation_room` 
      ADD COLUMN `woo_item_id` INTEGER NOT NULL DEFAULT 0,
      ADD INDEX `woo_item_id`(`woo_item_id`);");
    }
    


    $sql="select subroom_descr_en_US from gks_hotel_room_type_subroom limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room_type_subroom` 
      ADD COLUMN `subroom_descr_en_US` VARCHAR(190) DEFAULT NULL AFTER `subroom_descr`;");
      gks_run_sql("update gks_hotel_room_type_subroom set subroom_descr_en_US=subroom_descr");
    }


    $sql="select room_type_photo from gks_hotel_room_type limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room_type` 
      ADD COLUMN `room_type_photo` VARCHAR(255) DEFAULT NULL AFTER `room_type_descr_en_US`;");
    }
      
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_room_type_photo` (
      `id_hotel_room_type_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `hotel_room_type_id` int(11) NOT NULL DEFAULT '0',
      `photo_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `mydate` datetime NOT NULL,
      `mysize` int(11) DEFAULT '0',
      `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `user_add_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_hotel_room_type_photo`) USING BTREE,
      KEY `hotel_room_type_id` (`hotel_room_type_id`),
      KEY `photo_url` (`photo_url`(250)),
      KEY `mydate` (`mydate`),
      KEY `mysize` (`mysize`),
      KEY `ip` (`ip`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


    $sql="select room_photo from gks_hotel_room limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_room` 
      ADD COLUMN `room_photo` VARCHAR(255) DEFAULT NULL AFTER `room_descr_en_US`;");
    }
      
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_room_photo` (
      `id_hotel_room_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `hotel_room_id` int(11) NOT NULL DEFAULT '0',
      `photo_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `mydate` datetime NOT NULL,
      `mysize` int(11) DEFAULT '0',
      `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `user_add_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_hotel_room_photo`) USING BTREE,
      KEY `hotel_room_id` (`hotel_room_id`),
      KEY `photo_url` (`photo_url`(250)),
      KEY `mydate` (`mydate`),
      KEY `mysize` (`mysize`),
      KEY `ip` (`ip`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_reservation_links` (
      `id_hotel_reservation_links` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `hotel_reservation_id` int(11) NOT NULL DEFAULT '0',
      `url` text COLLATE utf8mb4_unicode_520_ci,
      `relative_path` text COLLATE utf8mb4_unicode_520_ci,
      `mydate` datetime DEFAULT NULL,
      `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `user_id` int(11) NOT NULL DEFAULT '0',
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `download_status` tinyint(4) NOT NULL DEFAULT '0',
      `download_start` datetime DEFAULT NULL,
      `download_end` datetime DEFAULT NULL,
      `download_pososto` double NOT NULL DEFAULT '0',
      `download_size_until_now` bigint(20) NOT NULL DEFAULT '0',
      `download_size_total` bigint(20) NOT NULL DEFAULT '0',
      `download_message` text COLLATE utf8mb4_unicode_520_ci,
      `html_tds` longtext COLLATE utf8mb4_unicode_520_ci,
      PRIMARY KEY (`id_hotel_reservation_links`),
      KEY `hotel_reservation_id` (`hotel_reservation_id`),
      KEY `mydate` (`mydate`),
      KEY `user_id` (`user_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
 

    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_reservation_messages` (
      `id_hotel_reservation_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `hotel_reservation_id` int(11) NOT NULL DEFAULT '0',
      `user_id` int(11) NOT NULL DEFAULT '0',
      `hotel_reservation_message` text COLLATE utf8mb4_unicode_520_ci,
      `email_id` int(11) NOT NULL DEFAULT '0',
      `connect_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_hotel_reservation_message`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `hotel_reservation_id` (`hotel_reservation_id`),
      KEY `user_id` (`user_id`),
      KEY `hotel_reservation_message` (`hotel_reservation_message`(250)),
      KEY `email_id` (`email_id`),
      KEY `connect_id` (`connect_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_hotel_reservation_photo` (
      `id_hotel_reservation_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `hotel_reservation_id` int(11) NOT NULL DEFAULT '0',
      `photo_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `mydate` datetime NOT NULL,
      `mysize` int(11) DEFAULT '0',
      `ip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `user_add_id` int(11) NOT NULL DEFAULT '0',
      `show_print` tinyint(4) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_hotel_reservation_photo`) USING BTREE,
      KEY `hotel_reservation_id` (`hotel_reservation_id`),
      KEY `photo_url` (`photo_url`(250)),
      KEY `mydate` (`mydate`),
      KEY `mysize` (`mysize`),
      KEY `ip` (`ip`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    

    
    
    $sql="select print_date from gks_hotel_reservation limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_hotel_reservation` 
      ADD COLUMN `print_date` datetime DEFAULT NULL after totalFeesAmount,
      ADD COLUMN `print_file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after print_date,
      ADD COLUMN `print_file_url` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after print_file_name,
      ADD COLUMN `print_user_id` int(11) NOT NULL DEFAULT '0' after print_file_url,
      ADD COLUMN `print_reservation_status` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after print_user_id;");

    }
    
    $sql="select * from gks_print_objects where id_print_object=5";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) { 
      gks_run_sql("INSERT INTO `gks_print_objects` (`id_print_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`object_name`,`object_descr`) VALUES 
      (5,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2020-11-17 15:21:26','gks_hotel_reservation','Κρατήσεις');");
    }      
    
    $sql="select * from gks_print_forms where id_print_form=2001";
    $result = gks_run_sql($sql);
    if ($GKS_HOTEL_BACKEND && $result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_print_forms` (`id_print_form`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_descr`,`is_disable`,`sortorder`,`gks_lang`,`file_type`,`grayscale`,`zoom`,`dpi`,`size_name`,`width_cm`,`height_cm`,`is_landscape`,`margin_cm_left`,`margin_cm_right`,`margin_cm_top`,`margin_cm_bottom`,`logo_url`,`page_header`,`page_footer`,`page_background_url`,`page_background_opacity`,`form_header`,`form_footer`,`details_header`,`details_body`,`details_footer`,`fpa_analysis`,`foroi_analysis`,`lots_and_serials_analysis`,`file_thump_url`,`localization_set_id`,`perm_company_ids`,`perm_acc_journal_ids`,`perm_acc_seires_ids`) VALUES 
     (2001,'2022-03-27 12:44:38','2022-03-27 16:52:32',1,1,'192.168.1.202','2022-03-27 16:52:32','Κρατήσεις',0,1000,'el-GR','pdf',0,1,600,'A4',21,29.7,0,2,2,3,3,'',
     '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 22pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 30%; vertical-align: bottom;\">{logo_url [<a href=\"{site_url}\" target=\"_blank\" rel=\"noopener\"><img style=\"max-height: 100%; height: 50px;\" src=\"%%\" border=\"0\" /></a>]}</td>\n<td style=\"width: 70%; vertical-align: bottom; text-align: right;\">{company_tagline}</td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
     '<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<div style=\"width: 100%; height: 10px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\">Τραπεζικός λογαριασμός:<br />{company_url [ <em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a>]} {company_phone [ <em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {company_email [ <em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a>]} <br />Σελίδα {page} από {pages}<br />Powered by <a href=\"https://www.gks.gr\" target=\"_blank\" rel=\"noopener\">www.gks.gr</a></td>\n</tr>\n</tbody>\n</table>',
     '',1,'<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">Εκδότης<br />{company_title [<strong>%%</strong><br />]} {company_eponimia [%%<br />]} {company_epaggelma [%%<br />]} {company_odos [Διεύθυνση: %%{company_tk [, %%]}<br />]} {company_poli [%%]}{company_nomos_descr [, %%]}{company_country_name [, %%<br />]} {company_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {company_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {company_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {company_afm [ΑΦΜ: {company_country_ee}%%<br />]} {company_doy [ΔΟΥ: %%<br />]}</td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{person_label [%%<br />]} {person_first_name} {person_last_name} <br />{person_title [<strong>%%</strong><br />]} {person_eponimia [%%<br />]} {person_epaggelma [%%<br />]} {person_odos [Διεύθυνση: %%{person_tk [, %%]}<br />]} {person_poli [%%]}{person_nomos_descr [, %%]}{person_country_name [, %%<br />]} {person_mobile [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a><br />]} {person_email [<em class=\"fas fa-envelope-square\" style=\"font-size: 100%;\"> </em> <a href=\"mailto:%%\">%%</a><br />]} {person_url [<em class=\"fas fa-globe\" style=\"font-size: 100%;\"> </em> <a href=\"%%\" target=\"_blank\" rel=\"noopener\">%%</a><br />]} {person_afm [ΑΦΜ: {person_country_ee}%%<br />]} {person_doy [ΔΟΥ: %%<br />]}\n<div>{hide} <strong>Τόπος παράδοσης</strong><br />{dest_name [Όνομα: %%<br />]} {dest_phone [<em class=\"fas fa-phone-square\" style=\"font-size: 100%;\"> </em> <a href=\"tel:%%\">%%</a>]} {dest_odos [Διεύθυνση: %%{dest_tk [, %%]}<br />]} {dest_poli [%%]}{dest_nomos_descr [, %%]}{dest_country_name [, %%<br />]}</div>\n</td>\n</tr>\n</tbody>\n</table>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>',
     '<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div>{doc_user_notes [%%<br />]}{doc_note_doc [%%<br />]}</div>\n<div style=\"background-color: {company_color}; width: 100%; height: 1px;\"><br /></div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 14pt;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 50%; vertical-align: top; text-align: center;\">{person_label [%%<br />]}</td>\n<td style=\"width: 50%; vertical-align: top; text-align: center; min-height: 100px;\">Εκδότης</td>\n</tr>\n</tbody>\n</table>',
     '<div style=\"width: 100%; height: 10px;\"><br /></div>\n<div style=\"font-family: Arial; font-size: 16pt; text-align: center; font-weight: bold;\">{doc_title_pre [%% : ]}{doc_title}</div>\n<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 12pt; line-height: 1.3;\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_date_dt [Ημερομηνία: %%<br />]} {doc_seira [Σειρά: %%<br />]} {doc_number [hide:zero][Αριθμός: %%<br />]} <br /><br /></td>\n<td style=\"width: 4%; padding-right: 20px; vertical-align: top; position: relative;\">\n<div style=\"width: 1px; height: 80%; top: 10%; border-left: 1px dashed gray; position: absolute; left: 50%;\"><br /></div>\n</td>\n<td style=\"width: 48%; padding-right: 20px; vertical-align: top;\">{doc_check_in [hide:zero][Άφιξη: %%<br />]} {doc_check_out [hide:zero][Αναχώρηση: %%<br />]} {doc_days [hide:zero][Διανυκτερεύσεις: %%<br />]} {doc_rooms [hide:zero][Δωμάτια: %%<br />]} {doc_adults [hide:zero][Ενήλικες: %%<br />]} {doc_childs [hide:zero][Παιδιά: %%<br />]} {doc_visitors [hide:zero][Επισκέπτες: %%<br />]} {doc_child_kounies [hide:zero][Βρεφικά κρεβάτια: %%<br />]} {doc_extra_beds [hide:zero][Επιπλέον κρεβάτια: %%<br />]} {doc_tropos_pliromis [Τρόπος Πληρωμής: %%<br />]} </td>\n</tr>\n</tbody>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
     '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 70%; text-align: center;\" nowrap=\"nowrap\">Δωμάτιο</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Καθαρή αξία</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Αξία ΦΠΑ</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: right;\" nowrap=\"nowrap\">Συνολική αξία</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{eidos_aa}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 70%;\">{room_photo [<img style=\"float: left; padding: 0pt 5pt 5pt 0pt; max-width: 30pt; max-height: 30pt;\" src=\"%%\" />]} {room_type_photo [<img style=\"float: left; padding: 0pt 5pt 5pt 0pt; max-width: 30pt; max-height: 30pt;\" src=\"%%\" />]}{room_descr [Δωμάτιο: %%<br />]}{room_descr_en_US [Room: %%<br />]}{room_type_descr [Τύπος Δωματίου: %%<br />]}{room_type_descr_en_US [Room Type: %%<br />]}{room_adults [hide:zero][Ενήλικες: %%<br />]}{room_childs [hide:zero][Παιδιά: %%<br />]}{room_visitors [Επισκέπτες: %%<br />]}{room_child_kounies [hide:zero][Βρεφικά κρεβάτια: %%<br />]}{room_extra_beds [hide:zero][Επιπλέον κρεβάτια: %%<br />]}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_priceall}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_fpa_amount_total}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{eidos_priceall_total}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid gray; text-align: left; width: 70%;\" colspan=\"2\">{hide}{eidos_comments}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 0%;\" colspan=\"3\">{eidos_ejeresi_fpa [Αιτία Εξαίρεσης ΦΠΑ: %% <br />]} {eidos_parakratisi_descr [Φόροι Παρακρ.: %%]}{eidos_parakratisi_poso [hide:zero][: %%<br />][format:cs]} {eidos_loipoi_foroi_descr [Λοιποί Φόροι: %%]}{eidos_loipoi_foroi_poso [hide:zero][: %%<br />][format:cs]} {eidos_xartosimo_descr [Ψηφιακό Τέλος συναλλαγής: %%]} {eidos_xartosimo_poso [hide:zero][: %%<br />][format:cs]} {eidos_teloi_descr [Τέλη: %%]} {eidos_teloi_poso [hide:zero][: %%<br />][format:cs]} {eidos_kratiseis_poso [hide:zero][Κρατήσεις: %%][format:cs]}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left; width: 70%;\" colspan=\"2\" nowrap=\"nowrap\"><strong>Σύνολα</strong></td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fpa_amount_total}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall_total}</td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; height: 20px; width: 70%;\" colspan=\"2\" nowrap=\"nowrap\"><br /></td>\n<td style=\"border-right: 1px solid white; text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\"><br /></td>\n<td style=\"border-right: 1px solid white; text-align: right; width: 0%;\" nowrap=\"nowrap\"><br /></td>\n</tr>\n<tr>\n<td style=\"border: 1px solid white; text-align: right; vertical-align: top; padding-left: 0px; padding-top: 0px; padding-right: 20px; width: 70%;\" colspan=\"2\" rowspan=\"9\" nowrap=\"nowrap\">{fpa_analysis}<br />{foroi_analysis}</td>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Υποσύνολο: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_priceall [%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">ΦΠΑ: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fpa_amount_total [%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\">Μικτό Σύνολο: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%; font-size: 150%;\"><strong><span style=\"white-space: nowrap;\">{doc_netfpa_amount_total [%%][format:cs]}</span></strong><br /></td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Φόροι Παρακρ.: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_withheld [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Λοιποί Φόροι: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_othertaxes [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Ψηφιακό Τέλος συναλλαγής: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_stampduty [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Τέλη: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_fees [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\">Κρατήσεις: {hide}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%;\" nowrap=\"nowrap\">{doc_deductions [hide:zero][%%][format:cs]}</td>\n</tr>\n<tr>\n<td style=\"text-align: right; width: 0%;\" colspan=\"2\" nowrap=\"nowrap\"><strong>Σύνολο: {hide}</strong></td>\n<td style=\"border: 1px solid gray; text-align: right; width: 0%; font-size: 120%;\" nowrap=\"nowrap\"><strong> {doc_priceall_total [%%][format:cs]} </strong></td>\n</tr>\n</tfoot>\n</table>\n<div style=\"width: 100%; height: 10px;\"><br /></div>',
     '<span style=\"font-family: Arial; font-size: 12pt;\">Προηγούμενο υπόλοιπο: {person_balance_before [%%][format:cs]} Νέο υπόλοιπο:  {person_balance_after [%%][format:cs]}</span>',
     '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: center;\" nowrap=\"nowrap\">% ΦΠΑ</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Αξία υποκείμενη σε ΦΠΑ</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">ΦΠΑ.</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Σύνολο</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{fpa_aa}</td>\n<td style=\"border: 1px solid gray; text-align: center;\" nowrap=\"nowrap\">{fpa_pososto}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_net}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_fpa}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_total}</td>\n</tr>\n</tbody>\n<tfoot>\n<tr style=\"border-top: 2px solid black; border-bottom: 2px solid black;\">\n<td style=\"border: 1px solid gray; text-align: left;\" colspan=\"2\" nowrap=\"nowrap\"><strong>Σύνολα</strong></td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_net}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_fpa}</td>\n<td style=\"border: 1px solid gray; text-align: right;\" nowrap=\"nowrap\">{fpa_sum_total}</td>\n</tr>\n</tfoot>\n</table>',
     '<table style=\"width: 100%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 50%; text-align: left;\" nowrap=\"nowrap\">Τύπος φόρου</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Αξία υποκείμενη σε φόρο</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Φόρος</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{foroi_aa}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 25%;\" nowrap=\"nowrap\">{foroi_descr}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{foroi_net}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{foroi_foros}</td>\n</tr>\n</tbody>\n</table>',
     '<table style=\"width: 50%; border-collapse: collapse; font-family: Arial; font-size: 9pt; border: 0px solid gray;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">\n<thead>\n<tr>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 0%; text-align: center;\" nowrap=\"nowrap\">A/A</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: left;\" nowrap=\"nowrap\">Παρτίδα-Serial</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: left;\">Ποσότητα</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Ημερ. Παραγωγής</th>\n<th style=\"border: 1px solid gray; background-color: #343a40; color: white; font-weight: normal; width: 25%; text-align: right;\" nowrap=\"nowrap\">Ημερ. Λήξης</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td style=\"border: 1px solid gray; text-align: center; width: 0%;\" nowrap=\"nowrap\">{lotserial_aa}<br /></td>\n<td style=\"border: 1px solid gray; text-align: left; width: 25%;\" nowrap=\"nowrap\">{lotserial_name}</td>\n<td style=\"border: 1px solid gray; text-align: left; width: 25%;\">{lotserial_quantity}</td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{lotserial_production}<br /></td>\n<td style=\"border: 1px solid gray; text-align: right; width: 25%;\" nowrap=\"nowrap\">{lotserial_expire}</td>\n</tr>\n</tbody>\n</table>',
     '',1319899,'','','');");
      
     
      gks_run_sql("INSERT INTO `gks_print_objects_forms` (`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`print_form_id`,`print_object_id`) VALUES 
      ('2020-01-01','2020-01-01',2,2,'127.0.0.1','2020-01-01',2001,5)");
    }
    


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_notification_type` (
      `id_notification_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `notification_type_descr` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `notification_type_sortorder` int(11) NOT NULL DEFAULT '1000',
      `notification_type_disabled` tinyint(4) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_notification_type`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `notification_type_descr` (`notification_type_descr`),
      KEY `notification_type_sortorder` (`notification_type_sortorder`),
      KEY `notification_type_disabled` (`notification_type_disabled`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
    
    $sql="select * from gks_notification_type where id_notification_type=10";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {

      gks_run_sql("INSERT INTO `gks_notification_type` (`id_notification_type`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`notification_type_descr`,`notification_type_sortorder`,`notification_type_disabled`) VALUES 
      (10,  '2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Καταχώρηση από χρήστη',10,0),
      (50,  '2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Ημερολόγιο',50,0),
      (100, '2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Φόρμα από ιστότοπο',100,0),
      (510, '2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Παραγωγή - Ολοκλήρωση παραγγελίας',510,0),
      (1010,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','WooCommerce - Νέα κράτηση',1010,0),
      (1020,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','WooCommerce - Νέα Παραγγελία',1020,0),
      (1030,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','WooCommerce - Νέο Παραστατικό',1030,0),
      (3010,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Connect - Νέα Επαφή',3010,0),
      (3020,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Connect - Νέα Παραγγελία',3020,0),
      (3040,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2022-03-29 13:35:02','Connect - Νέο Μήνυμα',3040,0);");
      
    
    }
    

