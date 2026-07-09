<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


$is_cron=0;
if (isset($argv) and isset($argv[1]) and $argv[1]=='cron') $is_cron=1;

if ($is_cron) {
  $_SERVER['HTTP_HOST']='lolcahost';
  $_SERVER['QUERY_STRING']='';
  $_SERVER['REMOTE_ADDR']='127.0.0.1';
}
define('SECURE', 1);
include_once('functions.php');
//include_once('functions_worldline.php');

//echo '<pre>';
// /my/admin-worldline-refresh.php?today=1
// /my/admin-worldline-refresh.php?days=0

if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
if (isset($gkIP)==false) $gkIP='127.0.0.1';


$my_page_title=gks_lang('worldline Refresh'); 
db_open();
stat_record();

$sql="SELECT id_company
FROM gks_company
WHERE worldline_username<>'' AND worldline_password<>'' AND worldline_authorization_code<>'' and worldline_x_api_key<>'' AND company_disable=0
ORDER BY company_sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}
$id_company_ids=array();
while ($row = $result->fetch_assoc()) {
  $id_company_ids[]=$row['id_company'];
}
//echo '<pre>';print_r($id_company_ids);die();
    
    
if (isset($_GET['today'])) {
  echo '<pre>';
  foreach ($id_company_ids as $id_company) {
    for($i=0;$i<=1;$i++) {
      gks_worldline_api_get_transactions($i,$id_company,'');
      echo 'mydaydif: '.$i.' company:'.$id_company. "\n";
    }
  } 
  die();
}
//echo '<pre>dddd';die();
 
$days=0;if (isset($_GET['days'])) $days=intval($_GET['days']);
$i=$days;
if ($is_cron==1) $i=0;
echo '<pre>';
foreach ($id_company_ids as $id_company) {
  gks_worldline_api_get_transactions($i,$id_company,'');
  echo 'mydaydif: '.$i.' company:'.$id_company. "\n";
}

die();


