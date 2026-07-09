<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


 


$title=base64_decode($_POST['title']);
if ($title=='') {
  debug_mail(false,'favorites title is empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Όνομα σελίδας δεν μπορεί να είναι κενό')));
  echo json_encode($return); die();
}

$url=base64_decode($_POST['url']);
if ($url=='') {
  debug_mail(false,'favorites url is empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Σύνδεσμος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die();
}

$my_page_title=gks_lang('Προσθήκη νέου Αγαπημένου');
db_open();
stat_record();

if ($my_wp_user_id<=0) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
  
}

$sql="insert into gks_users_favorites (descr,url,user_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
'".$db_link->escape_string($title)."',
'".$db_link->escape_string($url)."',
".$my_wp_user_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

gks_cache_update_menu_version();

$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
