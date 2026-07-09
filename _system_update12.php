<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


    
    $sql="select def_supplier from gks_eshop_products limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_eshop_products`
      ADD COLUMN `def_supplier` INTEGER NOT NULL DEFAULT 0 AFTER `product_kostos`,
      ADD COLUMN `min_quantity_alert` DOUBLE NOT NULL DEFAULT 0 AFTER `def_supplier`,
      ADD COLUMN `internal_note` TEXT DEFAULT NULL AFTER `min_quantity_alert`,
      ADD INDEX `def_supplier`(`def_supplier`);");
    }

    $sql="select * from gks_permission_object where id_permission_object=731";
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
       (731,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_settings','Ρυθμίσεις Εφαρμογής',382),
       (732,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_settings_users','Οι Ρυθμίσεις μου',383);");
    }


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_users_recommendation` (
      `id_users_recommendation` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  
      `user_id` int(11) NOT NULL DEFAULT '0',
      `from_user_id` int(11) NOT NULL DEFAULT '0',
      `mydate` datetime DEFAULT NULL,
      `sxolio` text COLLATE utf8mb4_unicode_520_ci,
      PRIMARY KEY (`id_users_recommendation`) USING BTREE,
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `user_id` (`user_id`),
      KEY `from_user_id` (`from_user_id`),
      KEY `mydate` (`mydate`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


    $sql="select bank_deposit_9digit from gks_acc_pay limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_acc_pay`
      ADD COLUMN `bank_deposit_9digit` VARCHAR(32) DEFAULT NULL,
      ADD INDEX `bank_deposit_9digit`(`bank_deposit_9digit`);");
      
      gks_run_sql("ALTER TABLE `gks_orders`  ADD INDEX `bank_deposit_9digit`(`bank_deposit_9digit`);");
      gks_run_sql("ALTER TABLE `gks_acc_inv` ADD INDEX `bank_deposit_9digit`(`bank_deposit_9digit`);");
      gks_run_sql("ALTER TABLE `gks_whi_mov` ADD INDEX `bank_deposit_9digit`(`bank_deposit_9digit`);");
    }



    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_messages` (
      `id_acc_inv_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `acc_inv_id` int(11) NOT NULL DEFAULT '0',
      `user_id` int(11) NOT NULL DEFAULT '0',
      `acc_inv_message` text COLLATE utf8mb4_unicode_520_ci,
      `email_id` int(11) NOT NULL DEFAULT '0',
      `connect_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_acc_inv_message`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `acc_inv_id` (`acc_inv_id`),
      KEY `user_id` (`user_id`),
      KEY `acc_inv_message` (`acc_inv_message`(250)),
      KEY `email_id` (`email_id`),
      KEY `connect_id` (`connect_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_pay_messages` (
      `id_acc_pay_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `acc_pay_id` int(11) NOT NULL DEFAULT '0',
      `user_id` int(11) NOT NULL DEFAULT '0',
      `acc_pay_message` text COLLATE utf8mb4_unicode_520_ci,
      `email_id` int(11) NOT NULL DEFAULT '0',
      `connect_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_acc_pay_message`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `acc_pay_id` (`acc_pay_id`),
      KEY `user_id` (`user_id`),
      KEY `acc_pay_message` (`acc_pay_message`(250)),
      KEY `email_id` (`email_id`),
      KEY `connect_id` (`connect_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_messages` (
      `id_whi_mov_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `whi_mov_id` int(11) NOT NULL DEFAULT '0',
      `user_id` int(11) NOT NULL DEFAULT '0',
      `whi_mov_message` text COLLATE utf8mb4_unicode_520_ci,
      `email_id` int(11) NOT NULL DEFAULT '0',
      `connect_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_whi_mov_message`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `whi_mov_id` (`whi_mov_id`),
      KEY `user_id` (`user_id`),
      KEY `whi_mov_message` (`whi_mov_message`(250)),
      KEY `email_id` (`email_id`),
      KEY `connect_id` (`connect_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_urlshort` (
      `id_urlshort` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `mydate_edit` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `longurl` varchar(512) DEFAULT NULL,
      `shorturl` varchar(255) DEFAULT NULL,
      `crm_channel_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_contact_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_campain_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_text` text COLLATE utf8mb4_unicode_520_ci,
      `assigned_id` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_urlshort`),
      KEY `mydate_edit` (`mydate_edit`),
      KEY `user_id_edit` (`user_id_edit`),
      KEY `longurl` (`longurl`(250)),
      KEY `shorturl` (`shorturl`(250)),
      KEY `crm_channel_id` (`crm_channel_id`),
      KEY `crm_channel_contact_id` (`crm_channel_contact_id`),
      KEY `crm_channel_campain_id` (`crm_channel_campain_id`),
      KEY `crm_channel_text` (`crm_channel_text`(250)),
      KEY `assigned_id` (`assigned_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;");
    
    gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_urlshort_hit` (
      `id_urlshort_hit` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `mydate_add` datetime DEFAULT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `urlshort_hit_guid` varchar(63) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `urlshort_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_contact_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_campain_id` int(11) NOT NULL DEFAULT '0',
      `crm_channel_text` text COLLATE utf8mb4_unicode_520_ci,
      `assigned_id` int(11) NOT NULL DEFAULT '0',
      
      `sessionid` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `pageurl` varchar(1024) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `query_string` varchar(1024) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `host` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `userAgent` text COLLATE utf8mb4_unicode_520_ci,
      `referer` text COLLATE utf8mb4_unicode_520_ci,
        
      PRIMARY KEY (`id_urlshort_hit`),
      KEY `mydate_add` (`mydate_add`),
      KEY `user_id_add` (`user_id_add`),
      KEY `myip` (`myip`),
      KEY `urlshort_hit_guid` (`urlshort_hit_guid`),
      KEY `urlshort_id` (`urlshort_id`),
      KEY `crm_channel_id` (`crm_channel_id`),
      KEY `crm_channel_contact_id` (`crm_channel_contact_id`),
      KEY `crm_channel_campain_id` (`crm_channel_campain_id`),
      KEY `crm_channel_text` (`crm_channel_text`(250)),
      KEY `assigned_id` (`assigned_id`),
      KEY `sessionid` (`sessionid`),
      KEY `pageurl` (`pageurl`(250)),
      KEY `query_string` (`query_string`(250)),
      KEY `host` (`host`(250)),
      KEY `userAgent` (`userAgent`(250)),
      KEY `referer` (`referer`(250))
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;");    
    
    
    $sql="select urlshort_id from gks_crm_leads limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_crm_leads`
      ADD COLUMN `urlshort_id` INTEGER NOT NULL DEFAULT 0 AFTER `crm_channel_text`,
      ADD COLUMN `urlshort_hit_id` INTEGER NOT NULL DEFAULT 0 AFTER `urlshort_id`,
      ADD INDEX `urlshort_id`(`urlshort_id`),
      ADD INDEX `urlshort_hit_id`(`urlshort_hit_id`);");
    }
 
     
    //ALTER TABLE `gks_acc_inv`
    //ADD COLUMN `balance_user_before` DOUBLE DEFAULT NULL AFTER `affect_balance_pros`,
    //ADD COLUMN `balance_user_after` DOUBLE DEFAULT NULL AFTER `balance_user_before`;
  

