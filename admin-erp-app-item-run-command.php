<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

//die();


$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η gks ERP App Desktop')));
  echo json_encode($return); die();}

$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks(base64_decode($_POST['cmd']));
if ($cmd=='') {
  debug_mail(false,'the cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή')));
  echo json_encode($return); die();}

$asset_id=0;if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
$api_call='';if (isset($_POST['api_call'])) $api_call=trim_gks(base64_decode($_POST['api_call']));

$my_page_title=gks_lang('Εκτέλεση εντολής gks ERP App').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$params=array(
  'id' => $id,
  'cmd' => $cmd,
  'asset_id' => $asset_id,
  'api_call' => $api_call,
);

if ($cmd=='run_command_voipaimoriginatecall') {
  $params['extension']='';if (isset($_POST['extension'])) $params['extension']=trim_gks(base64_decode($_POST['extension']));
  $params['phone']='';    if (isset($_POST['phone']))     $params['phone']=trim_gks(base64_decode($_POST['phone']));
}  
$gks_erp_run_result=gks_erp_app_run_command($params);

//print '<pre>';print_r($gks_erp_run_result);die();

if ($gks_erp_run_result['success']==false) {
  $return = array('success' => false, 'message' => base64_encode($gks_erp_run_result['message']),'html' => '');
  echo json_encode($return); die(); } 
  
  
$file=''; if (isset($gks_erp_run_result['data'])) $file = $gks_erp_run_result['data'];


if ($api_call=='ping_service') {
  //einai idi aplo keimeno
} else if ($api_call=='merchantinfo' or $api_call=='reconciliation') {
  $file='<pre>'.json_encode(json_decode($file,true),JSON_PRETTY_PRINT).'</pre>';
}

$return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($file));
echo json_encode($return); die();
    
echo '<pre>';echo $fileurl.'|'.$file;die();
