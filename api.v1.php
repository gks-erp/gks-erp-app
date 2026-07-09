<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

ini_set('max_execution_time', 600);
set_time_limit(600);

putenv("ENV=PRODUCTION");
define('SECURE', 1);


  

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

if (isset($gks_user_settings)==false) $gks_user_settings=array();


$my_wp_user_id=2;
db_open();

//print 'ggggggggg <pre>';print_r($_POST);echo '</pre>';die();

if (isset($_POST['rnd'])==false or isset($_POST['token'])==false) {debug_mail(false,'api error 1','');die('api error 1');}
$value_rnd=trim_gks($_POST['rnd']);
$value_token=trim_gks($_POST['token']);
$value_pid=0; if (isset($_POST['pid'])) $value_pid=intval($_POST['pid']);
$value_oid=0; if (isset($_POST['oid'])) $value_oid=intval($_POST['oid']);
$value_coid=0; if (isset($_POST['coid'])) $value_coid=intval($_POST['coid']);
$value_couponid=0; if (isset($_POST['couponid'])) $value_couponid=intval($_POST['couponid']);
$value_force=0; if (isset($_POST['force'])) $value_force=intval($_POST['force']);
$online_order_guid='';if (isset($_POST['online_order_guid'])) $online_order_guid=trim_gks($_POST['online_order_guid']);
//echo '<pre>';print_r($_POST);die();


if ($value_pid<=0 and $value_oid<=0 and $value_coid<=0 and $value_couponid<=0 and strlen($online_order_guid)<>32) {debug_mail(false,'empty value_pid and value_oid and value_coid and value_couponid','');die('api error 2a1');}


$sql="select id_eshop, eshop_key FROM gks_eshops WHERE eshop_disable=0";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('api error 3');}
if ($result->num_rows <= 0) {debug_mail(false,'eshops not found',$sql);die('api error 4');}

$id_eshop=0;
while ($row = $result->fetch_assoc()) {
  $token_local=md5($value_rnd.$row['eshop_key']);
  if ($token_local == $value_token) {
    $id_eshop=intval($row['id_eshop']);
    break;
  }
}  
if ($id_eshop<=0) {debug_mail(false,'eshops not found 2',$sql);die('api error 5');}

if ($value_pid > 0) {
  $cmd='get_product';
  $value_id=$value_pid;
} else if ($value_oid>0) {
  $cmd='get_order';
  $value_id=$value_oid;
} else if ($value_coid>0) {
  $cmd='get_comments_order';
  $value_id=$value_coid;
} else if ($value_couponid>0) {
  $cmd='get_coupon';
  $value_id=$value_couponid;
} else if (strlen($online_order_guid)==32) {

  include_once('functions_sales_order_online.php');
  gks_erp_sales_order_online_run($id_eshop);
  
  die();
} else {
  die();  
}

$guid = guid_for_async_queue();
$sql="insert into gks_async_queue (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
guid,mytype,status,cmd,param1,param2,param3
) values (
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($guid)."','woo','pending','".$cmd."','".$db_link->escape_string($id_eshop)."','".$db_link->escape_string($value_id)."',
'".$value_force."'
)";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('api error 6');}

gks_curl_post_async(GKS_SITE_URL.'my/cron_async_queue.php',array('guid' => $guid));



echo 'gks_OK';

//echo 'remote'."\n";
//echo 'id_eshop: '.$id_eshop."\n";
//echo 'sql: '.$sql."\n";

//file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/api_'.rand(1000,9999).'.txt',print_r($_POST,true));

