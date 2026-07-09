<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων αντικειμένου xxx-xxx');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_ret_acc_inv=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','autocomplete',0);
$perm_ret_acc_pay=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','autocomplete',0);
$perm_ret_whi_mov=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','autocomplete',0);
if ($perm_ret_acc_inv['success']==false and 
    $perm_ret_acc_pay['success']==false and 
    $perm_ret_whi_mov['success']==false) {
  $return = array('success' => false, 'message' => base64_encode($perm_ret_acc_inv['message']));echo json_encode($return); die();
}


$ret_data=[];

if (isset($_POST['coi_mark'])) {
  $coi_mark=''; if (isset($_POST['coi_mark'])) $coi_mark=trim_gks(base64_decode($_POST['coi_mark']));
  $coi_acc_inv_id=0; if (isset($_POST['coi_acc_inv_id'])) $coi_acc_inv_id=intval($_POST['coi_acc_inv_id']);
  $coi_acc_pay_id=0; if (isset($_POST['coi_acc_pay_id'])) $coi_acc_pay_id=intval($_POST['coi_acc_pay_id']);
  $coi_whi_mov_id=0; if (isset($_POST['coi_whi_mov_id'])) $coi_whi_mov_id=intval($_POST['coi_whi_mov_id']);
  
  if ($perm_ret_acc_inv['success']==false) $coi_acc_inv_id=0;
  if ($perm_ret_acc_pay['success']==false) $coi_acc_pay_id=0;
  if ($perm_ret_whi_mov['success']==false) $coi_whi_mov_id=0;
  
  
  $coiitem_ret=gks_correlated_invoices_get_data($coi_mark,$coi_acc_inv_id,$coi_acc_pay_id,$coi_whi_mov_id);
  $ret_data=$coiitem_ret;
  
} else if (isset($_POST['mcm_mark'])) {
  $mcm_mark=''; if (isset($_POST['mcm_mark'])) $mcm_mark=trim_gks(base64_decode($_POST['mcm_mark']));
  $mcm_acc_inv_id=0; if (isset($_POST['mcm_acc_inv_id'])) $mcm_acc_inv_id=intval($_POST['mcm_acc_inv_id']);
  $mcm_acc_pay_id=0; if (isset($_POST['mcm_acc_pay_id'])) $mcm_acc_pay_id=intval($_POST['mcm_acc_pay_id']);
  $mcm_whi_mov_id=0; if (isset($_POST['mcm_whi_mov_id'])) $mcm_whi_mov_id=intval($_POST['mcm_whi_mov_id']);
  
  if ($perm_ret_acc_inv['success']==false) $mcm_acc_inv_id=0;
  if ($perm_ret_acc_pay['success']==false) $mcm_acc_pay_id=0;
  if ($perm_ret_whi_mov['success']==false) $mcm_whi_mov_id=0;
  
  
  $mcmitem_ret=gks_multiple_connected_marks_get_data($mcm_mark,$mcm_acc_inv_id,$mcm_acc_pay_id,$mcm_whi_mov_id);
  $ret_data=$mcmitem_ret;
}


$return = array(
  'success' => true, 
  'message' => base64_encode('OK'),
  'data' => $ret_data,
);
echo json_encode($return); die();


$return = array('success' => false, 'message' => base64_encode('data |'.$coi_mark.'|'.$coi_acc_inv_id.'|'.$coi_acc_pay_id.'|'.$coi_whi_mov_id));
echo json_encode($return); die();

echo json_encode($return); die();
