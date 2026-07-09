<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



$sql="select * from gks_permission_object where id_permission_object=275";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (275,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_tk','Διευθύνσεις',275);");

  gks_run_sql("insert into gks_permission_user (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  permission_object_id,user_id,
  perm_autocomplete
  )
  SELECT
  now() as mydate_add,
  now() as mydate_edit,
  2 as user_id_add,
  2 as user_id_edit,
  '127.0.0.1' as myip,
  275 as permission_object_id,
  user_id,
  1 as perm_autocomplete
  FROM gks_permission_user
  WHERE permission_object_id In (280,270)
  and user_id not in (
    SELECT user_id
    FROM gks_permission_user
    WHERE permission_object_id=275
  )
  GROUP BY gks_permission_user.user_id;");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_geocode` (
  `id_geocode` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address` varchar(1024) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `lat` double NOT NULL DEFAULT '0',
  `lng` double NOT NULL DEFAULT '0',
  `response` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id_geocode`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `address` (`address`(190))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_stat_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `myvalues` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select user_id from gks_stat_online limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error

  gks_run_sql("DROP TABLE IF EXISTS `gks_stat_online`;");
  
  gks_run_sql("CREATE TABLE  `gks_stat_online` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `timevisit` datetime DEFAULT NULL,
    `session` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `user_id` int(11) NOT NULL DEFAULT '0',
    `username` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `lasturl` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `query_string` varchar(1024) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `host` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `visitor` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    `pagetitle` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `session` (`session`(250)),
    KEY `user_id` (`user_id`),
    KEY `username` (`username`(250)),
    KEY `lasturl` (`lasturl`(250)),
    KEY `host` (`host`(250)),
    KEY `visitor` (`visitor`(250)),
    KEY `timevisit` (`timevisit`)
  ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");
  
}


$must_run14_tk=false;
$sql="select * from gks_tk where id_tk=1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  $must_run14_tk=true;
} else {
  $sql="SELECT count(*) as cc FROM gks_tk where nomos_id=5 having cc=0";
  $result = gks_run_sql($sql);
  if ($result->num_rows==1) {
    $must_run14_tk=true;
    $sql="truncate gks_tk";
    $result = gks_run_sql($sql);
    
  }
  
}

if ($must_run14_tk) {  
  require_once('_system_update14_tk.php');
  gks_run_sql("update gks_tk set tk=replace(tk,' ','')");
  
  gks_run_sql("ALTER TABLE `gks_tk` AUTO_INCREMENT = 100001;");
}

gks_run_sql("update gks_acc_eidi_parastatikon 
set credit_acc_eidos_parastatikou_id=114
where id_acc_eidos_parastatikou in (111,112)
and credit_acc_eidos_parastatikou_id<>114");





$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_ERP_START_VARDIA') === false) {
  echo '_current/_config.php file not contains GKS_ERP_START_VARDIA<br>';die();}

if (strpos($read_file, '$startvardia') !== false) {
  echo '_current/_config.php file contains $startvardia<br>';die();}

//$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-config.php');
//if (strpos($read_file, 'gks_stat_queue') === false) {
//  echo 'wp-config.php file not contains gks_stat_queue<br>';die();}


$sql="select * from gks_aade_tropoi_pliromis where id_aade_tropos_pliromis in (6,7)";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into `gks_aade_tropoi_pliromis` (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  id_aade_tropos_pliromis,aade_tropos_pliromis_code,aade_tropos_pliromis_descr,sortorder
  ) values 
  ('2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',
  6,6, 'Web Banking',6 ),
  ('2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',
  7,7, 'POS / e-POS',7);");
}
