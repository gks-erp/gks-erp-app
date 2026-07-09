<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id_user_cv=0;
if (isset($_POST['id_user_cv'])) $id_user_cv=intval($_POST['id_user_cv']);
if ($id_user_cv<=0) {
  debug_mail(false,'the kliniki is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η κλινική')));
  echo json_encode($return); die();}




$myval='';
if (isset($_POST['myval'])) $myval=trim_gks($_POST['myval']); 

//$return = array('success' => false, 'message' => base64_encode($myval));
//echo json_encode($return); die();


$my_page_title=gks_lang('Αυτόματη Αποθήκευση περιγραφής συνημμένου στο προφίλ').' id:'.$id_user_cv;
db_open();
stat_record();


//$userrole='';
//if (isset($my_wp_user_info->roles)) {
//  if (in_array('logistis',$my_wp_user_info->roles))  $userrole='logistis';
//  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
//  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
//}
if ($my_wp_user_id<=0) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}



$sql="update gks_users_cv set file_descr ='".$db_link->escape_string($myval)."' where id_user_cv=".$id_user_cv." and user_id=".$my_wp_user_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  




$return = array('success' => true, 'message' => base64_encode('OK'), 'id_user_cv' => $id_user_cv, 'val' => base64_encode($myval));
echo json_encode($return); die();
