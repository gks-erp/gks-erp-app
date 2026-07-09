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


//if (isset($gks_user_settings)==false) $gks_user_settings=array();
//$my_wp_user_id=2;

db_open();

$return = array('success' => false, 'message' => base64_encode('generic api erp error'),'data' => false);

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
  $return['message']=base64_encode('json decode error');
  //debug_mail(false,'json decode Error','');
  //echo json_encode($return); 
  die();
}


//debug_mail(false,'api.transfer.v1.php','');
if (isset($input_data['cmd'])==false or trim_gks($input_data['cmd'])=='')         {
  $return['message']=base64_encode('cmd is not set');
  debug_mail(false,'cmd is not set',$HTTP_RAW_POST_DATA); 
  //echo json_encode($return); 
  die();}
if (isset($input_data['rand1'])==false or trim_gks($input_data['rand1'])=='')     {
  $return['message']=base64_encode('rnd is not set');
  debug_mail(false,'rand1 is not set',$HTTP_RAW_POST_DATA); 
  //echo json_encode($return); 
  die();}
if (isset($input_data['semd5'])==false or trim_gks($input_data['semd5'])=='')     {
  $return['message']=base64_encode('semd5 is not set');
  debug_mail(false,'semd5 is not set',$HTTP_RAW_POST_DATA); 
  //echo json_encode($return); 
  die();}

$cmd=''; if (isset($input_data['cmd'])) $cmd=$input_data['cmd'];
$myrand1='';if (isset($input_data['rand1'])) $myrand1=$input_data['rand1'];
$mysemd5='';if (isset($input_data['semd5'])) $mysemd5=$input_data['semd5'];
$register_url='';if (isset($input_data['register_url'])) $register_url=$input_data['register_url'];

$mysemd5_calc=md5($myrand1. GKS_ERP_HASHMD5KEY02.$myrand1. GKS_ERP_HASHMD5KEY02.$myrand1. GKS_ERP_HASHMD5KEY04);

if ($mysemd5!=$mysemd5_calc)  {
  debug_mail(false,'security Error','');
  die();
}
if ($cmd=='gks_license_get_status') {
  //echo $register_url;die();
  $res=gks_license_get_status();
  
  $return['success']=true;
  $return['message']=base64_encode('OK');
  $return['data']=$res;
  echo json_encode($return); die();
}

debug_mail(false,'cmd error','');
die();

$return['message'] = base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
echo json_encode($return); die(); 
