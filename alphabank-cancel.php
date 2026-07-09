<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');

//if ($my_wp_user_id == 0) {die();}
debug_mail(false,'alphabank cancel '.$_gks_id_session , '');

if (!isset($_gks_session['gks']['alphabank']['id_object']) or 
    !isset($_gks_session['gks']['alphabank']['order_guid']) or 
    !isset($_gks_session['gks']['alphabank']['pliroteo']) ) {
  header('Location: /basket'); die();      
}

$my_page_title=gks_lang('Ακύρωση πληρωμής μέσω τράπεζας');
db_open();
stat_record();


$sql="update gks_payments_alphabank set status ='cancel',";
if (isset($_POST['status'])) {
  $sql.=" status_bank ='".$db_link->escape_string($_POST['status'])."',";
}
if (isset($_POST['txId'])) {
  $sql.=" txId ='".$db_link->escape_string($_POST['txId'])."',";
}
if (isset($_POST['payMethod'])) {
  $sql.=" payMethod ='".$db_link->escape_string($_POST['payMethod'])."',";
}
if (isset($_POST['currency'])) {
  $sql.=" currency ='".$db_link->escape_string($_POST['currency'])."',";
}
if (isset($_POST['currency'])) {
  $sql.=" currency ='".$db_link->escape_string($_POST['currency'])."',";
}
if (isset($_POST['message'])) {
  $sql.=" message ='".$db_link->escape_string($_POST['message'])."',";
}
if (isset($_POST['riskScore'])) {
  $sql.=" riskScore ='".$db_link->escape_string($_POST['riskScore'])."',";
}
if (isset($_POST['digest'])) {
  $sql.=" digest2 ='".$db_link->escape_string($_POST['digest'])."',";
}
$sql.=" checkout_json='".$db_link->escape_string(json_encode($_POST))."'
where id_payment_alphabank = ".$_gks_session['gks']['basket']['payment']['table_id'];
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'alphabank-cancel.php error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }
  
$message='';
if (isset($_POST['message'])) $message = $_POST['message'];
else if (isset($_POST['status'])) $message = $_POST['status'];
  
$sql="update gks_payments set payment_status ='cancel',status_message='".$db_link->escape_string($message)."'
where id_payment = ".$_gks_session['gks']['basket']['payment']['id_payment'];
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'alphabank-cancel.php error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }
  

  
//print '<pre>';
//print_r($_POST);
//die();  



$_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα κατά την επεξεργασία της συναλλαγής');
$_gks_session['gks']['confirm']['id_object'] = $_gks_session['gks']['alphabank']['id_object'];
$_gks_session['gks']['confirm']['payment_acquirer_type']='web';
$_gks_session['gks']['confirm']['poso']=$_gks_session['gks']['alphabank']['pliroteo'];
$_gks_session['gks']['confirm']['bank9digit']='';  

if (isset($_POST['message'])) {
  $_gks_session['gks']['payment_error'].= '<br>'.$_POST['message'];
}
if (isset($_POST['status'])) {
  $_gks_session['gks']['payment_error'].= '<br>'.gks_lang('Κατάσταση').': '.$_POST['status'];
}

unset($_gks_session['gks']['alphabank']['pliroteo']);
gks_erp_cookie_save();

header('Location: /payment'); die();



//Array
//(
//    [mid] => 0022000581
//    [orderid] => f048527a2a304d0616d038bbbdbc98b2
//    [status] => CANCELED
//    [orderAmount] => 2.02
//    [currency] => EUR
//    [paymentTotal] => 2.02
//    [message] => Failed, user canceled
//    [riskScore] => 
//    [txId] => 925243801
//    [digest] => vIjBK/cOanBwXeg7VazPlwJ1twY=
//)
//Array
//(
//    [mid] => 0022000581
//    [orderid] => 1x969aa03c1dc497068b65815fe5c9170a
//    [status] => REFUSEDRISK
//    [orderAmount] => 14.21
//    [currency] => EUR
//    [paymentTotal] => 14.21
//    [riskScore] => 1000
//    [txId] => 927632781
//    [digest] => owDIok8g/obKrD4fgRosNfRXkQI=
//    [woocommerce-login-nonce] => 
//    [_wpnonce] => 
//)
