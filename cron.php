<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
/*
https://test.easyfilesselection.com/my/cron.php

*/

ini_set('max_execution_time', 600);
set_time_limit(600);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');



$my_page_title='cron.php';
$my_wp_user_id=2;
$gkIP='127.0.0.1';
db_open();
//stat_record();

$diafora=time()-$GKS_ERP_CRON_LAST_RUN;
//echo '<pre>cron-cron'."\r\n".$diafora;

if (isset($_GET['force'])==false) {
  if (abs($diafora < 60)) die(); //kathe 60 seconds
}

$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ERP_CRON_LAST_RUN','".time()."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

$sql="select id_cron,every_seconds,fetch_url 
from gks_crons
where disable_cron=0
and (next_run<='".date('Y-m-d H:i:s')."' or next_run is null)
and every_seconds>0";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$mycrons=[];
while ($row = $result->fetch_assoc()) {
  $row['fetch_url']=trim_gks($row['fetch_url']);
  if ($row['fetch_url']<>'') {
    if (substr($row['fetch_url'], 0, 4)=='/my/' or $row['fetch_url']=='/wp-cron.php') {
      $row['fetch_url']=GKS_SITE_URL.substr($row['fetch_url'], 1);
    }
    if (filter_var($row['fetch_url'], FILTER_VALIDATE_URL)) {
      $mycrons[]=$row;
    }
  }
}

foreach ($mycrons as $mycron) {
  $sql="update gks_crons set 
  last_run=now(),
  next_run=date_add(now(), interval ".$mycron['every_seconds']." second),
  num_runs=num_runs+1
  where id_cron=".$mycron['id_cron'];
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
}
foreach ($mycrons as $mycron) {
  gks_curl_post_async($mycron['fetch_url'],[]);
  sleep(3);
}

//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/cron_'.time().'_'.rand(10000,99999).'.txt',print_r($mycrons,true));


//echo '<pre>';print_r($mycrons);


