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
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση Περίστασης').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders_occasion',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_orders_occasion where id_order_occasion = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$title=''; if (isset($_POST['title'])) $title=trim_gks(base64_decode($_POST['title']));
$occasion_id=0; if (isset($_POST['occasion_id'])) $occasion_id=intval($_POST['occasion_id']);
$notes=''; if (isset($_POST['notes'])) $notes=trim_gks(base64_decode($_POST['notes']));
$pay_method_id=0; if (isset($_POST['pay_method_id'])) $pay_method_id=intval($_POST['pay_method_id']);


if ($user_id<=0) {debug_mail(false,'emptyl','user_id is zero');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή')));
  echo json_encode($return); die(); }

if ($title=='') {debug_mail(false,'emptyl','title is empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τίτλος ΔΕΝ μπορεί να είναι κενός')));
  echo json_encode($return); die(); }

if ($occasion_id<=0) {debug_mail(false,'emptyl','select type of occasion');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιον τύπο για την περίσταση')));
  echo json_encode($return); die(); }





$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_orders_occasion');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_orders_occasion (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-orders-occasion-item.php?id='.$id); 
}

$sql="update gks_orders_occasion set 
user_id=".$user_id.",
title='".$db_link->escape_string($title)."',
occasion_id=".$occasion_id.",
notes='".$db_link->escape_string($notes)."',
pay_method_id=".$pay_method_id.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_order_occasion = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

