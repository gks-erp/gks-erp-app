<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$page='';if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page']));
$transaction_type=''; if (isset($_POST['transaction_type'])) $transaction_type=trim_gks(base64_decode($_POST['transaction_type']));
$doc_id=0;if (isset($_POST['doc_id'])) $doc_id=intval($_POST['doc_id']);
$sessionId=''; if (isset($_POST['sessionId'])) $sessionId=trim_gks(base64_decode($_POST['sessionId']));
$id_eftpos_transaction=''; if (isset($_POST['id_eftpos_transaction'])) $id_eftpos_transaction=intval($_POST['id_eftpos_transaction']);

$doc_table='';
if ($page=='/my/admin-eftpos-transaction.php') $doc_table='gks_eftpos_transaction';
if ($page=='/my/admin-acc-inv-item.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-pos-run.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-acc-pay-item.php') $doc_table='gks_acc_pay';

if (in_array($transaction_type,['sale','saleerp','fullvoid','fullvoiderp','refund','refunderp'])==false) {
  debug_mail(false,'the transaction_type is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' transaction_type<br>'.gks_lang('Η τιμή που στάλθηκε είναι η').': <b>'.$transaction_type.'</b>'));
  echo json_encode($return); die();}

if (in_array($transaction_type,['saleerp'])) {
  if ($doc_id<=0) {
    debug_mail(false,'the id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();}
}

//echo '<pre>'.$page.'|'.$doc_table;die();

$my_page_title=gks_lang('Ακύρωση εκτέλεσης συναλλαγής EFT-POS για το παραστατικό').': '.$transaction_type.' '.$doc_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $doc_table,($doc_id==-1 ? 'add':'edit'),$doc_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$sessionId=''; if (isset($_POST['sessionId'])) $sessionId=trim_gks(base64_decode($_POST['sessionId']));
$id_eftpos_transaction=''; if (isset($_POST['id_eftpos_transaction'])) $id_eftpos_transaction=intval($_POST['id_eftpos_transaction']);

if ($sessionId=='' or $id_eftpos_transaction<=0) {
  debug_mail(false,'data error',                                 gks_lang('Σφάλμα δεδομένων').' (1)');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (1)'));
  echo json_encode($return); die();}

$sql="SELECT gks_eftpos_transaction.*
FROM gks_eftpos_transaction 
where id_eftpos_transaction=".$id_eftpos_transaction."
and sessionId='".$db_link->escape_string($sessionId)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

if ($result->num_rows!=1) {
  debug_mail(false,'asset error',                                gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής με για αυτό το παραστατικό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής με για αυτό το παραστατικό')));
  echo json_encode($return); die();}    
$row_tra = $result->fetch_assoc();  

$transaction_status=$row_tra['transaction_status'];
$payment_acquirer_with_id=intval($row_tra['payment_acquirer_with_id']);
$company_id=intval($row_tra['company_id']);
$cashRegisterId=$row_tra['cashRegisterId'];

if (in_array($transaction_status,['done','canceled','abort'])) {
  debug_mail(false,'record not found',                           gks_lang('Δεν μπορεί να ακυρωθεί η συναλλαγή γιατί είναι σε κατάσταση').' '.$transaction_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να ακυρωθεί η συναλλαγή γιατί είναι σε κατάσταση').' '.$transaction_status));
  echo json_encode($return); die();}
  
  
  

$sql="select * from gks_company where id_company=".$company_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found',                           gks_lang('Δεν βρέθηκε η εταιρεία με ID').' '.$row_inv['company_id']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εταιρεία με ID').' '.$row_inv['company_id']));
  echo json_encode($return); die();}
$row_company = $result->fetch_assoc();

switch ($payment_acquirer_with_id) {   
  case 1: //viva
    $ret=gks_eftpos_get_token_viva($row_company);
    break;
  case 6: //worldline
    $ret=gks_eftpos_get_token_worldline($row_company);
    break;
    
    
  default:
    debug_mail(false,'eftpos error',                               gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$payment_acquirer_with_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$payment_acquirer_with_id));
    echo json_encode($return); die();
}
if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

$access_token=$ret['data']['access_token'];

$data=array(
  'sessionId' => $sessionId,
  'access_token' => $access_token,
  'cashRegisterId' => $cashRegisterId,
);

$pawid_descr='xxx';
switch ($payment_acquirer_with_id) {   
  case 1: //viva
    $pawid_descr='Viva Terminal';
    $ret=gks_eftpos_sales_request_abort_viva($data);
    break;  
  case 6: //worldline
    $pawid_descr='Worldline Terminal';
    $ret=gks_eftpos_sales_request_abort_worldline($data);
    break;  
    
  default:      
    debug_mail(false,'eftpos error',                               gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$payment_acquirer_with_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$payment_acquirer_with_id));
    echo json_encode($return); die();
}

 //{"detail":"That session was already marked for abort process"}
if ($payment_acquirer_with_id==1 and //viva
    $ret['success']==false and 
    strpos($ret['message'], 'for abort process') !== false) {
  debug_mail(false,'viva abort error, step 2',$ret['message']);
  if ($doc_table=='gks_acc_inv') {
    $sql="update gks_acc_inv_payment set
    transaction_id=0
    where acc_inv_id=".$doc_id."
    and transaction_id=".$id_eftpos_transaction."
    limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
}


if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

$return=array();
$return['success']=true;
$return['message']='OK';
$return['pawid']=$payment_acquirer_with_id;
$return['pawid_descr']=$pawid_descr;

echo json_encode($return); die();
