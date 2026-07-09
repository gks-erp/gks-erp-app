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


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Σύνδεση ως άλλος χρήστης').': '.$id;

db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'hack login other user','');
  header('Content-Type: text/html; charset=utf-8');
  echo gks_lang('Δεν επιτρέπεται η πρόσβαση');
  die();
}
  
if ($userrole=='logistis') {
  $sql="SELECT user_id, meta_value AS mywp_capabilities FROM ".GKS_WP_TABLE_PREFIX."usermeta WHERE meta_key='".GKS_WP_TABLE_PREFIX."capabilities' and user_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'sql error';
    die();
  }
  if ($result->num_rows<=0) {
    echo 'user not found';
    die();
  }
  $row = $result->fetch_assoc();  
  
  $user_roles=array();
  if (isset($row['mywp_capabilities']) and $row['mywp_capabilities']!='') {
    $user_roles = unserialize($row['mywp_capabilities']);
  }
  
  if (isset($user_roles['administrator']) or 
      isset($user_roles['adminmy']) or 
      isset($user_roles['editor']) or
      isset($user_roles['contributor']) or
      isset($user_roles['author']) or
      isset($user_roles['logistis']) or 
      isset($user_roles['texnikos']) or 
      isset($user_roles['hrmanager'])) {
    debug_mail(false,'hack login admin user','');
    header('Content-Type: text/html; charset=utf-8');
    echo gks_lang('Δεν επιτρέπεται η πρόσβαση');
    die();    
  }
}

//echo $id;
//die();

wp_set_current_user($id);
wp_set_auth_cookie($id);

header('location: /my/');
