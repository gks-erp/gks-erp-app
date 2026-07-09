<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$myid=0;
if (isset($_POST['myid'])) $myid=intval($_POST['myid']);
if ($myid<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Συναλλαγή Viva Reload Transaction ID').': '.$myid;
db_open();
stat_record();
$perm_ret_edit=gks_permission_user_can_action($my_wp_user_id, 'gks_viva_transaction','edit',0);
if ($perm_ret_edit['success']==false) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die(); }




$sql="SELECT TransactionId FROM gks_viva_transaction WHERE id_viva_transaction=".$myid." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}

$row = $result->fetch_assoc();
$TransactionId=trim_gks($row['TransactionId']);
if ($TransactionId=='') {
  debug_mail(false,'TransactionId is empty',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η εγγραφή δεν είναι συναλλαγή')));
  echo json_encode($return); die();   
}



//include_once('admin-viva-functions.php');

gks_viva_api_get_transactions(0,0,$TransactionId);

$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();  
