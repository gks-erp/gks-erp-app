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


$my_page_title=gks_lang('Προσθήκη τραπεζικού λογαριασμού σε προφίλ');
db_open();
stat_record();

$sql="select * from ".GKS_WP_TABLE_PREFIX."users where id=".$my_wp_user_id." limit 1";
$result_users = $db_link->query($sql);        
if (!$result_users) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result_users->num_rows!=1) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  }
$row_wp_user = $result_users->fetch_assoc();


$iban=trim_gks(stripslashes(urldecode($_POST['iban'])));
$bank_id=intval($_POST['bank_id']);
$dikaiouxos=trim_gks(stripslashes(urldecode($_POST['dikaiouxos'])));

$iban = iban_to_machine_format($iban);



if ($iban=='') {debug_mail(false,'emptyl', 'iban can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ΙΒΑΝ δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($bank_id <= 0) {debug_mail(false,'emptyl', 'iban can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια Τράπεζα')));
  echo json_encode($return); die(); }
  
if ($dikaiouxos=='') {debug_mail(false,'emptyl', 'dikaiouxos can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Δικαιούχος δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

//
if(verify_iban($iban) == false) {debug_mail(false,'emptyl', 'verify_iban false');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ΙΒΑΝ είναι λανθασμένο')));
  echo json_encode($return); die(); }

$sql="select IBAN from gks_bank_accounts where deleted_from_user=0 and user_id=".$my_wp_user_id." and IBAN='".$db_link->escape_string($iban)."'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Υπάρχει ήδη μια καταχώρηση για αυτό το IBAN')));
  echo json_encode($return); die(); }

$sql="select id_bank_account,IBAN from gks_bank_accounts where deleted_from_user<>0 and user_id=".$my_wp_user_id." and IBAN='".$db_link->escape_string($iban)."'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
if ($result->num_rows>=1) {
  $row_ba = $result->fetch_assoc();
  $id=$row_ba['id_bank_account'];
  
  $sql="update gks_bank_accounts set date_edit=now(), deleted_from_user=0,bank_id=".$bank_id.",account_dikaiouxos='".$db_link->escape_string($dikaiouxos)."' 
  where user_id=".$my_wp_user_id." and IBAN='".$db_link->escape_string($iban)."'";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
    
} else {  
  $sql="insert into gks_bank_accounts (date_add,date_edit,user_id,IBAN,bank_id,account_descr,account_dikaiouxos) values (
  now(),now(),
  ".$my_wp_user_id.",
  '".$db_link->escape_string($iban)."',
  ".$bank_id.",
  'user account',
  '".$db_link->escape_string($dikaiouxos)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $id = $db_link->insert_id;
  
}

$sql="SELECT gks_bank_accounts.*, gks_banks.bank_descr
FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
WHERE gks_bank_accounts.id_bank_account=".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$row_bank_accounts = $result->fetch_assoc();

$html_out='';
$html_out.='<tr id="rowbankaccount-'.$row_bank_accounts['id_bank_account'].'" style="background-color: white;">
  <td style="text-align: center;">
    <i id="delrec-'.$row_bank_accounts['id_bank_account'].'" class="mybankaccountdelete fas fa-trash-alt" style="cursor: pointer;text-align: center; color: #ff0000; font-size: 100%;" title="'.gks_lang('Διαγραφή').'"></i>
  </td>
  <td style="text-align: left;">IBAN: '.iban_to_human_format($row_bank_accounts['IBAN']).'<br>
    '.gks_lang('Τράπεζα').': '.$row_bank_accounts['bank_descr'].'<br>
    '.gks_lang('Δικαιούχος').': '.$row_bank_accounts['account_dikaiouxos'].'
  </td>                            
</tr>';




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



$return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html_out),'profilepososto_user' => $calc['user'],'profilepososto_job' => $calc['job']);
echo json_encode($return); die();
