<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);


//Test Case 11: APPROVED TRANSACTION (MAESTRO) leie oti i karta einai lathos
//Test Case 12: APPROVED TRANSACTION (DINERS) to idio
//Test Case 13: APPROVED TRANSACTION (DISCOVER)
//Test Case 14: APPROVED TRANSACTION (AMERICAN EXPRESS)

include_once('functions.php');

//if ($my_wp_user_id == 0) {die();}
debug_mail(false,'piraeusbank success '.$_gks_id_session , '');

if (!isset($_gks_session['gks']['piraeusbank']['id_object']) or 
    !isset($_gks_session['gks']['piraeusbank']['order_guid']) or 
    !isset($_gks_session['gks']['piraeusbank']['pliroteo']) ) {
  header('Location: /basket'.gks_set_lang_url()); die();      
}

if (count($_POST) < 11 or 
  !isset($_POST['SupportReferenceID']) or !isset($_POST['ResultCode']) or !isset($_POST['StatusFlag']) or !isset($_POST['ResponseCode']) or 
  !isset($_POST['MerchantReference']) or !isset($_POST['TransactionId']) or !isset($_POST['PackageNo']) or !isset($_POST['ApprovalCode']) or 
  !isset($_POST['AuthStatus']) or !isset($_POST['Parameters']) or !isset($_POST['HashKey']) ) {
    
  header('Location: /basket'.gks_set_lang_url()); die();      
}





//echo '---'.$final_check.'--'.$mydigest.'----'.$_POST['digest'];
//die();

$my_page_title=gks_lang('Επιτυχής πληρωμή μέσω τράπεζας piraeusbank');
db_open();
stat_record();

/*
Array
(
    [SupportReferenceID] => 260028470
    [ResultCode] => 0
    [StatusFlag] => Success
    [ResponseCode] => 00
    [MerchantReference] => 10008xfb39c31f5fce3d51daef8528ed0af5cb_8866
    [TransactionId] => 139748193
    [PackageNo] => 1
    [ApprovalCode] => 442879
    [AuthStatus] => 03
    [Parameters] => 10008
    [HashKey] => 59468D77F2A3EE6EB624F42A5E68E63C24B9ABBF119D78D8DC5A33FCCD298766
    [ButtonSubmit] => Submit
)
PaymentMethod
TraceID

debug url= https://www.easyfilesselection.com/my/piraeusbank-success.php?SupportReferenceID=260028470&ResultCode=0&StatusFlag=Success&ResponseCode=00&MerchantReference=10008xfb39c31f5fce3d51daef8528ed0af5cb_8866&TransactionId=139748193&PackageNo=1&ApprovalCode=442879&AuthStatus=03&Parameters=10008&HashKey=59468D77F2A3EE6EB624F42A5E68E63C24B9ABBF119D78D8DC5A33FCCD298766&ButtonSubmit=submit111

MerchantReference error
https://www.easyfilesselection.com/my/piraeusbank-success.php?SupportReferenceID=260028470&ResultCode=0&StatusFlag=Success&ResponseCode=00&MerchantReference=10008xfb39c31f5fce3d51daef8528ed0af5cb_8866&TransactionId=139748193&PackageNo=1&ApprovalCode=442879&AuthStatus=03&Parameters=10008&HashKey=59468D77F2A3EE6EB624F42A5E68E63C24B9ABBF119D78D8DC5A33FCCD298766

https://www.easyfilesselection.com/my/piraeusbank-success.php?StatusFlag=Success&MerchantReference=10008x00ca25959693a470f74cee5bdbd1d60e_8989&HashKey=B54688C3FAFE1B7DBD2EB7FEC8C2CBC7C86DE60B0B596D222CD7A933304E4406&Parameters=10008&ResultCode=0&TransactionId=139749617&SupportReferenceID=260031490&ApprovalCode=816594&ResponseCode=00&PackageNo=1&AuthStatus=03&ButtonSubmit=%CE%A5%CF%80%CE%BF%CE%B2%CE%BF%CE%BB%CE%AE


*/


$id_payment_piraeusbank=$_gks_session['gks']['basket']['payment']['table_id'];
$TranTicket='';
if (isset($_POST['MerchantReference'])) {
  $sql="select * from gks_payments_piraeusbank where MerchantReference='".$db_link->escape_string($_POST['MerchantReference'])."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'piraeusbank-cancel.php error sql',$sql); header('Location: /basket'.gks_set_lang_url()); die();}
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if ($row['status']=='draft') {
      $id_payment_piraeusbank = $row['id_payment_piraeusbank'];
      $TranTicket=$row['TranTicket'];
    }
    //remove me
    $TranTicket=$row['TranTicket'];
    
  } 
}





$sql="update gks_payments_piraeusbank set status ='try 2',";
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
where id_payment_piraeusbank = ".$id_payment_piraeusbank;
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'piraeusbank-cancel.php error sql',$sql); header('Location: /basket'.gks_set_lang_url()); die();}
  
if ($TranTicket=='') {
  debug_mail(false,'piraeusbank success -> fail '.$_gks_id_session , '');
  $_gks_session['gks']['payment_error'] = 'Error processing transaction (326781).';
  unset($_gks_session['gks']['piraeusbank']);
  header('Location: /payment'.gks_set_lang_url()); die();   
}

$MerchantReference=''; if (isset($_POST['MerchantReference'])) $MerchantReference=$_POST['MerchantReference'];
$ApprovalCode=''; if (isset($_POST['ApprovalCode'])) $ApprovalCode=$_POST['ApprovalCode'];
$Parameters = ''; if (isset($_POST['Parameters'])) $Parameters=$_POST['Parameters'];
$ResponseCode = ''; if (isset($_POST['ResponseCode'])) $ResponseCode=$_POST['ResponseCode'];
$SupportReferenceID = ''; if (isset($_POST['SupportReferenceID'])) $SupportReferenceID=$_POST['SupportReferenceID'];
$AuthStatus = ''; if (isset($_POST['AuthStatus'])) $AuthStatus=$_POST['AuthStatus'];
$PackageNo = ''; if (isset($_POST['PackageNo'])) $PackageNo=$_POST['PackageNo'];
$StatusFlag = ''; if (isset($_POST['StatusFlag'])) $StatusFlag=$_POST['StatusFlag'];


$myhashdata=$TranTicket .';'.
($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_PosID : $GKS_PIRAEUSBANK_REAL_PosID) .';'.
($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_AcquirerID : $GKS_PIRAEUSBANK_REAL_AcquirerID) .';'.
$MerchantReference.';'.
$ApprovalCode.';'.
$Parameters.';'.
$ResponseCode.';'.
$SupportReferenceID.';'.
$AuthStatus.';'.
$PackageNo.';'.
$StatusFlag;

$HashKey = ''; if (isset($_POST['HashKey'])) $HashKey=$_POST['HashKey'];


$myHashKey = strtoupper(hash_hmac('sha256', $myhashdata, $TranTicket, false));

//echo time().' '.$id_payment_piraeusbank;
//echo '<br>';
//echo $myhashdata;
//
//echo '<br>';
//echo $HashKey;
//echo '<br>';
//echo $myHashKey;
//
//die();  


  
if ($myHashKey == $HashKey and strlen($HashKey)>10) {  
  
  debug_mail(false,'piraeusbank success Done'.$_gks_id_session , '');
  
  $_gks_session['gks']['confirm']['id_object'] = $_gks_session['gks']['piraeusbank']['id_object'];
  $_gks_session['gks']['confirm']['payment_acquirer_type']='web';
  $_gks_session['gks']['confirm']['poso']=$_gks_session['gks']['piraeusbank']['pliroteo'];
  $_gks_session['gks']['confirm']['bank9digit']='';  
  
  


  $sql="update gks_orders set mdate_payment=NOW(), order_state='draft' where id_order = ".$_gks_session['gks']['confirm']['id_object'] ;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'piraeusbank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'.gks_set_lang_url()); die();  
  }  
  
  $sql="update gks_payments set status_message='ok',status_message='".$db_link->escape_string($_POST['StatusFlag'])."', payment_status='success' where id_payment = ".$_gks_session['gks']['basket']['payment']['id_payment'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'piraeusbank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'.gks_set_lang_url()); die();  
  }  
  
  $sql="update gks_payments_piraeusbank set status='success' where id_payment_piraeusbank = ".$id_payment_piraeusbank;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'piraeusbank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'.gks_set_lang_url()); die();  
  }  
  



  unset($_gks_session['gks']['payment_error']);
  unset($_gks_session['gks']['piraeusbank']);  


  $myreturn= order_execute_download($_gks_session['gks']['confirm']['id_object']);
  
  if ($myreturn['code']=='OK') {
    gks_send_email_order_execute($_gks_session['gks']['confirm']['id_object']);
  } else if ($myreturn['code']=='has_more_than_download') {
    gks_send_email_order_partial($_gks_session['gks']['confirm']['id_object']);
  } else {
    gks_send_email_order_receive($_gks_session['gks']['confirm']['id_object']);
  }
    
  header('Location: /confirm'.gks_set_lang_url()); die();  
  
} else {

  debug_mail(false,'piraeusbank success -> fail '.$_gks_id_session , '');

  $_gks_session['gks']['payment_error'] = 'Error processing transaction (326785).';
  unset($_gks_session['gks']['piraeusbank']);
  
  header('Location: /payment'.gks_set_lang_url()); die();   
} 


  
//Array
//(
//    [mid] => 0022000581
//    [orderid] => bb5246018b2f35789b218112ca528336
//    [status] => CAPTURED
//    [orderAmount] => 2.02
//    [currency] => EUR
//    [paymentTotal] => 2.02
//    [message] => OK, 00 - Approved
//    [riskScore] => 0
//    [payMethod] => amex
//    [txId] => 925243821
//    [paymentRef] => 531743
//    [digest] => r/1tHv4/4YQ07yTmPxl5jwQWU7I=
//)
