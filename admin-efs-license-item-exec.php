<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Άδειας Χρήσης').' id: '.$id;
db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}



$sql="select * from gks_efs_license where id_lic=".$id." limit 1";
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
$row= $result->fetch_assoc();



$email = base64_decode($_POST['email']);
$quantity   = intval(base64_decode($_POST['quantity']));
if ($quantity<0) $quantity=0;


if ($email == '' or filter_var($email, FILTER_VALIDATE_EMAIL)==false) {
  debug_mail(false,'email is not OK',$email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το email δεν είναι σωστό')));
  echo json_encode($return); die();  
}


$sql="select * from gks_efs_license where email like '".$db_link->escape_string($email)."' and id_lic <> ".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows>0) {
  debug_mail(false,'email exist',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το email υπάρχει ήδη σε άλλη εγγραφή')));
  echo json_encode($return); die();  
}




$sql="update gks_efs_license set 
email='".$db_link->escape_string($email)."',
quantity=".$quantity.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_lic = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();







