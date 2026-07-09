<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


die();
 
ini_set('max_execution_time', 600);
set_time_limit(600);


putenv("ENV=PRODUCTION");

define('SECURE', 1);
require_once('_current/_config.php');


require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

$my_wp_user_id=2;
db_open();



$sql="CREATE TABLE IF NOT EXISTS `gks_erp_cookie` (
      `gks_erp_cookie_id` VARCHAR(128) NOT NULL,
      `user_id_add` int(11) NOT NULL DEFAULT '0',
      `user_id_edit` int(11) NOT NULL DEFAULT '0',
      `mydate_add` datetime NOT NULL,
      `mydate_edit` datetime NOT NULL,
      `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `data` LONGTEXT DEFAULT NULL,
      `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`gks_erp_cookie_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
    
$result = $db_link->query($sql);        
if (!$result) {die('sql error');}
echo 'ok';
die();

$sql="show triggers";
$result = $db_link->query($sql);        
if (!$result) {die('sql error');}

echo '<pre>';
while ($row= $result->fetch_assoc()) {
  echo  $row['Trigger']."\r\n";
}

//$result = $db_link->query('drop trigger gks_trigger_fullname1'); 
//$result = $db_link->query('drop trigger gks_trigger_fullname2'); 
//$result = $db_link->query('drop trigger gks_trigger_fullname3'); 
