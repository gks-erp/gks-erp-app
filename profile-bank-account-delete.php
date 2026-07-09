<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

if ($my_wp_user_id <= 0) {
  debug_mail(false,'user not login','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Θα πρέπει πρώτα να συνδεθείτε')),'myreload' => true);
  echo json_encode($return); die();}

if (!isset($_POST['id_rec']) || intval($_POST['id_rec'])<=0) {
  debug_mail(false,'profile-delete-bank-account.php error on id_rec');
  $return = array('success' => false, 'message' => base64_encode('error on id_rec'));
  echo json_encode($return); die();
}
$id_rec=intval($_POST['id_rec']);

$my_page_title=gks_lang('Αφαίρεση τραπεζικού λογαριασμού σε προφίλ');
db_open();
stat_record();

$sql="update gks_bank_accounts set date_edit=now(), deleted_from_user=1 where id_bank_account=".$id_rec." and user_id>0 and user_id=".$my_wp_user_id." limit 1";
$myrun = $db_link->query($sql);
if (!$myrun) {
  debug_mail(false,'warning on profile-delete-bank-account.php error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); 
}


$sql="update ".GKS_WP_TABLE_PREFIX."users set 
gks_last_update=now(),
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."' 
where id=".$my_wp_user_id." limit 1";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$calc = calc_profilepososto($my_wp_user_id,false);


  
$return = array('success' => true, 'message' => base64_encode('OK'),'profilepososto_user' => $calc['user'],'profilepososto_job' => $calc['job']);
echo json_encode($return); die();

  