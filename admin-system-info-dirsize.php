<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$mydir=''; if (isset($_POST['mydir'])) $mydir=trim_gks(base64_decode($_POST['mydir']));
if ($mydir=='') {
  debug_mail(false,'the mydir is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' mydir'));
  echo json_encode($return); die();}

if (in_array($mydir,['database','gkserp','website','erplo','erpfi','erpul','erpdl','wordpress','wodpr','total'])==false) {
  debug_mail(false,'the mydir is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' mydir (2) ').' '.$mydir);
  echo json_encode($return); die();}
  


$my_page_title=gks_lang('Υπολογισμός μεγέθους φακέλου').': '.$mydir;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$fullpath=[];
switch ($mydir) {
  
  case 'database': break;
  case 'erpfi':    $fullpath[]=GKS_FileServerShare; break;
  case 'website':  $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS; break;

  case 'gkserp':   $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/';break;
  case 'erplo':    $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site/';break;
  case 'erpul':    $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/'; break;
  case 'erpdl':    $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install/'; break;
  case 'wordpress':$fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-admin/';
                   $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/'; 
                   $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-includes/'; 
                   break;
  case 'wodpr':    $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/uploads/';break;
  case 'total':    $fullpath[]=GKS_FileServerShare;
                   $fullpath[]=GKS_SITE_PATH.GKS_SITE_HTTPDOCS;
                   ;break;
}
$mybytes=0;
foreach ($fullpath as $value) {
  $mybytes+=   floatval(gks_get_directory_size($value));
} 
if ($mydir=='wordpress') {
  $files = scandir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS);    
  foreach($files as $file) {
    if (!in_array($file,array(".",".."))) { 
      $mybytes+=filesize(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/'.$file);
    }
  }  
}
if ($mydir=='database' or $mydir=='total') {
  //
  $sql="SELECT SUM(data_length + index_length + data_free) as cc
  FROM information_schema.tables
  where table_schema='".DB_NAME."'
  GROUP BY table_schema";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  $row=$result->fetch_assoc();
  $mybytes+=intval($row['cc']);
}


$myhuman='0 bytes';
if ($mybytes <= 1000) {
  $myhuman=$mybytes.' bytes';
} else if ($mybytes <= (1000*1000)) {
  $myhuman=number_format($mybytes/floatval(1024),2,',','.').' Kbytes';
} else if ($mybytes <= (1000*1000*1000)) {
  $myhuman=number_format($mybytes/floatval(1024*1024),2,',','.').' Mbytes';
} else if ($mybytes <= (1000*1000*1000*1000)) {
  $myhuman=number_format($mybytes/floatval(1024*1024*1024),2,',','.').' Gbytes';
} else {
  $myhuman=number_format($mybytes/floatval(1024*1024*1024*1024),2,',','.').' Tbytes';
}


$return = array('success' => true, 'message' => base64_encode('OK'), 
  'mybytes'=> $mybytes,
  'myhuman'=> $myhuman,
);

echo json_encode($return); die();
