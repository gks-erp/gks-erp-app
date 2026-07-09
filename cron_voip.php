<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// /my/cron_voip.php?user_id=204487
 
ini_set('max_execution_time', 5);
set_time_limit(5);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

$user_id=0; if (isset($_GET['user_id'])) $user_id=intval($_GET['user_id']);


$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($db_link->connect_error) {
  debug_mail(false,'DB error',$db_link->connect_errno . '-'.$db_link->connect_error);
  die();
}
$db_link->set_charset('utf8'); 



$sql="select * 
from gks_erp_app 
where erp_app_disabled=0 
and erp_app_last_ping>date_sub(now(), interval 15 minute)
and voip_localdb<>''";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$db_link->errno . '-'.$db_link->error); die();}
if ($result->num_rows == 0) die(); //den iparxei kapoio poy na trexei
$apps=[];
while ($row = $result->fetch_assoc()) {
  $apps[]=$row;
}
foreach ($apps as $myapp) {
  $params=array(
    'id' => $myapp['id_erp_app'],
    'cmd' => 'run_command_voiplocaldbphonebook',
    'asset_id' => 0,
    'api_call' => '',
    'user_ids'=>intval($user_id),
  );
  $gks_erp_run_result=gks_erp_app_run_command($params);
  //echo '<pre>';
  //print_r($gks_erp_run_result);
  //echo '</pre>';
}

//echo '<pre>'.$user_id."\r\n";print_r($apps);die();

