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
if ($id<=0 and $id!=-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Αγαπημένου').' id: '.$id;
db_open();
stat_record();

if ($my_wp_user_id<=0) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}

if ($id>0) {
  $sql="select * from gks_users_favorites where user_id=".$my_wp_user_id." and id_favorites=".$id." limit 1";
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
}



$descr = base64_decode($_POST['descr']);
$url   = base64_decode($_POST['url']);



if ($descr == '') {
  debug_mail(false,'empty descr','descr is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Όνομα σελίδας δεν μπορεί να είναι κενό')));
  echo json_encode($return); die();  
}
if ($url == '') {
  debug_mail(false,'empty url','url is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Σύνδεσμος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die();  
}


$redirect='';
if ($id==-1) {
  $sql="insert into gks_users_favorites (mydate_add,user_id_add,myip,user_id) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.");";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-favorites-item.php?id='.$id); 
}

$sql="update gks_users_favorites set 
descr='".$db_link->escape_string($descr)."',
url='".$db_link->escape_string($url)."',
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_favorites = ".$id." and user_id=".$my_wp_user_id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

gks_cache_update_menu_version();

$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=>$redirect);
echo json_encode($return); die();







