<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Αποστολή Viber Test');

db_open();
stat_record();

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('hrmanager',$my_wp_user_info->roles))  $userrole='hrmanager';
  if (in_array('logistis',$my_wp_user_info->roles))  $userrole='logistis';
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}


if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();}

$user_id=0;
if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
if ($user_id<=0) {
  debug_mail(false,'the user_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' user_id.'));
  echo json_encode($return); die();}

//$sql="select viber_id from ".GKS_WP_TABLE_PREFIX."users where ID=".$user_id;
//$result = $db_link->query($sql);        
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die();}   
//if ($result->num_rows != 1) {
//  debug_mail(false,'user not found',$sql);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο χρήστης')));
//  echo json_encode($return); die();}  
//
//$row = $result->fetch_assoc();
//$viber_id=trim_gks($row['viber_id']);
//if ($viber_id=='') {
//  debug_mail(false,'user not found',$sql);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' Viber ID.'));
//  echo json_encode($return); die();} 

$viber_id=base64_decode(trim_gks(stripslashes(urldecode($_POST['viber_id']))));

//echo '<pre>';echo $viber_id;die();

$res = gks_viber_send('user', $user_id, $viber_id, gks_lang('Δοκιμαστικό μήνυμα. Γράψτε και στείλτε το όνομά σας'));
if (is_array($res) and isset($res['status']) and $res['status']==0) {
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής αποστολή')));
  echo json_encode($return); die();  
} 

$return = array('success' => false, 'message' => base64_encode(str_replace('[1]',print_r($res,true), gks_lang('Σφάλμα. Απάντηση από Viber Server:<br><small><pre>[1]</pre></small>'))));
echo json_encode($return); die();
