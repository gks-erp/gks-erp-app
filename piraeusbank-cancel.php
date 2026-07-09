<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');

//if ($my_wp_user_id == 0) {die();}
debug_mail(false,'piraeusbank cancel '.$_gks_id_session , '');

if (!isset($_gks_session['gks']['piraeusbank']['id_object']) or 
    !isset($_gks_session['gks']['piraeusbank']['order_guid']) or 
    !isset($_gks_session['gks']['piraeusbank']['pliroteo']) ) {
  header('Location: /basket'.gks_set_lang_url()); die();      
}

$_gks_session['gks']['payment_error'] = 'Payment Canceled'; 


$my_page_title=gks_lang('Ακύρωση πληρωμής μέσω τράπεζας piraeusbank');
$payment_status='cancel';
if (isset($_POST['StatusFlag'])) {
  $my_page_title.=' '.$_POST['StatusFlag'];
  $_gks_session['gks']['payment_error'] = 'Payment Failure'; 
  $payment_status='failure';
}
db_open();
stat_record();


$sql="update gks_payments_piraeusbank set status ='".$payment_status."',";
if (isset($_POST['SupportReferenceID']))   $sql.=" SupportReferenceID ='".$db_link->escape_string($_POST['SupportReferenceID'])."',";
if (isset($_POST['ResultCode']))   $sql.=" ResultCode ='".$db_link->escape_string($_POST['ResultCode'])."',";
if (isset($_POST['StatusFlag']))   $sql.=" StatusFlag ='".$db_link->escape_string($_POST['StatusFlag'])."',";
if (isset($_POST['ResponseCode']))   $sql.=" ResponseCode ='".$db_link->escape_string($_POST['ResponseCode'])."',";
if (isset($_POST['MerchantReference']))   $sql.=" MerchantReference_response ='".$db_link->escape_string($_POST['MerchantReference'])."',";
if (isset($_POST['TransactionId']))   $sql.=" TransactionId ='".$db_link->escape_string($_POST['TransactionId'])."',";
if (isset($_POST['PackageNo']))   $sql.=" PackageNo ='".$db_link->escape_string($_POST['PackageNo'])."',";
if (isset($_POST['ApprovalCode']))   $sql.=" ApprovalCode ='".$db_link->escape_string($_POST['ApprovalCode'])."',";
if (isset($_POST['AuthStatus']))   $sql.=" AuthStatus ='".$db_link->escape_string($_POST['AuthStatus'])."',";
if (isset($_POST['Parameters']))   $sql.=" Parameters ='".$db_link->escape_string($_POST['Parameters'])."',";
if (isset($_POST['HashKey']))   $sql.=" HashKey ='".$db_link->escape_string($_POST['HashKey'])."',";
if (isset($_POST['ButtonSubmit']))   $sql.=" ButtonSubmit ='".$db_link->escape_string($_POST['ButtonSubmit'])."',";

if (isset($_POST['TransactionDateTime']))   $sql.=" TransactionDateTime ='".$db_link->escape_string($_POST['TransactionDateTime'])."',";
if (isset($_POST['ResponseDescription']))   $sql.=" ResponseDescription ='".$db_link->escape_string($_POST['ResponseDescription'])."',";
if (isset($_POST['RetrievalRef']))   $sql.=" RetrievalRef ='".$db_link->escape_string($_POST['RetrievalRef'])."',";
if (isset($_POST['ResultDescription']))   $sql.=" ResultDescription ='".$db_link->escape_string($_POST['ResultDescription'])."',";
if (isset($_POST['CardType']))   $sql.=" CardType ='".$db_link->escape_string($_POST['CardType'])."',";
if (isset($_POST['LanguageCode']))   $sql.=" LanguageCode ='".$db_link->escape_string($_POST['LanguageCode'])."',";
if (isset($_POST['PaymentMethod']))   $sql.=" PaymentMethod ='".$db_link->escape_string($_POST['PaymentMethod'])."',";
if (isset($_POST['TraceID']))   $sql.=" TraceID ='".$db_link->escape_string($_POST['TraceID'])."',";


$sql.=" checkout_json='".$db_link->escape_string(json_encode($_POST))."'
where id_payment_piraeusbank = ".$_gks_session['gks']['basket']['payment']['table_id'];
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'piraeusbank-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
$message='';
if (isset($_POST['StatusFlag'])) $message = $_POST['StatusFlag'];

  
$sql="update gks_payments set payment_status ='".$payment_status."',status_message='".$db_link->escape_string($message)."'
where id_payment = ".$_gks_session['gks']['basket']['payment']['id_payment'];
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'piraeusbank-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  

  
//print '<pre>';
//print_r($_POST);
//die();  




$_gks_session['gks']['confirm']['id_object'] = $_gks_session['gks']['piraeusbank']['id_object'];
$_gks_session['gks']['confirm']['payment_acquirer_type']='web';
$_gks_session['gks']['confirm']['poso']=$_gks_session['gks']['piraeusbank']['pliroteo'];
$_gks_session['gks']['confirm']['bank9digit']='';  



unset($_gks_session['gks']['piraeusbank']['pliroteo']);
  
header('Location: /payment'.gks_set_lang_url()); die();

