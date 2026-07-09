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


$my_page_title=gks_lang('Αποθήκευση Τραπεζικού λογαριασμού').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_bank_accounts',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




if ($id>0) {
  $sql ="SELECT * FROM gks_bank_accounts where id_bank_account = ".$id;
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

$account_descr=''; if (isset($_POST['account_number'])) $account_descr=trim_gks(base64_decode($_POST['account_descr']));
$IBAN=''; if (isset($_POST['IBAN'])) $IBAN=trim_gks(base64_decode($_POST['IBAN']));
$account_number=''; if (isset($_POST['account_number'])) $account_number=trim_gks(base64_decode($_POST['account_number']));
$bank_id=0; if (isset($_POST['bank_id'])) $bank_id=intval($_POST['bank_id']);
$account_type=''; if (isset($_POST['account_type'])) $account_type=trim_gks(base64_decode($_POST['account_type']));
$account_dikaiouxos=''; if (isset($_POST['account_dikaiouxos'])) $account_dikaiouxos=trim_gks(base64_decode($_POST['account_dikaiouxos']));
$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$show_eshop=0; if (isset($_POST['show_eshop'])) $show_eshop=intval($_POST['show_eshop']);
$deleted_from_user=0; if (isset($_POST['deleted_from_user'])) $deleted_from_user=intval($_POST['deleted_from_user']);
$bank_account_disable=0; if (isset($_POST['bank_account_disable'])) $bank_account_disable=intval($_POST['bank_account_disable']);


if ($account_descr=='') {debug_mail(false,'emptyl',              gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

$iban_db=$IBAN;
$iban = iban_to_machine_format($iban_db);
$iban_is_verify=false;
if(verify_iban($iban)) {
  $iban_is_verify=true;
  $iban_db=iban_to_human_format($iban);
}

$sql="select id_bank_account from gks_bank_accounts where IBAN='".$db_link->escape_string($iban_db)."' and id_bank_account <> ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {debug_mail(false,'emptyl',            'the IBAN '.$IBAN.' exist to other account');
  $message=gks_lang('Το IBAN <br><b>[1]</b><br>υπάρχει ήδη σε άλλο λογαριασμό');
  $message=str_replace('[1]',$IBAN,$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();} 
  

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_bank_accounts');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_bank_accounts (mydate_add,mydate_edit,user_id_add,user_id_edit,myip) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-bank_accounts-item.php?id='.$id); 
}

$sql="update gks_bank_accounts set 
account_descr='".$db_link->escape_string($account_descr)."',
IBAN='".$db_link->escape_string($iban_db)."',
account_number='".$db_link->escape_string($account_number)."',
bank_id=".$bank_id.",
account_type='".$db_link->escape_string($account_type)."',
account_dikaiouxos='".$db_link->escape_string($account_dikaiouxos)."',
user_id=".$user_id.",
show_eshop=".$show_eshop.",
deleted_from_user=".$deleted_from_user.",
bank_account_disable=".$bank_account_disable.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_bank_account = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

