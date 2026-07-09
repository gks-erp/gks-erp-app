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
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Barcode').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_barcodes',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_barcodes where id_barcode = ".$id;
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


$barcode=''; if (isset($_POST['barcode'])) $barcode=trim_gks(base64_decode($_POST['barcode']));
$barcode_type_id=0; if (isset($_POST['barcode_type_id'])) $barcode_type_id=intval($_POST['barcode_type_id']);
$barcode_descr=''; if (isset($_POST['barcode_descr'])) $barcode_descr=trim_gks(base64_decode($_POST['barcode_descr']));
$product_id=''; if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);
$user_id=''; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$comments=''; if (isset($_POST['comments'])) $comments=trim_gks(base64_decode($_POST['comments']));
$disable_barcode=''; if (isset($_POST['disable_barcode'])) $disable_barcode=intval($_POST['disable_barcode']);
if ($disable_barcode!=1) $disable_barcode=0;



if ($barcode=='') {debug_mail(false,'emptyl',                    gks_lang('Το Barcode δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Barcode δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }





$redirect='';
if ($id==-1) {
  $sql="insert into gks_barcodes (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-barcodes-item.php?id='.$id); 
}



$sql="update gks_barcodes set 
barcode='".$db_link->escape_string($barcode)."',
barcode_type_id=".$barcode_type_id.",
barcode_descr='".$db_link->escape_string($barcode_descr)."',
product_id=".$product_id.",
user_id=".$user_id.",
comments='".$db_link->escape_string($comments)."',
disable_barcode=".$disable_barcode.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_barcode = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  





$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

