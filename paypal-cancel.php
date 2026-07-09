<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');


debug_mail(false,'paypal cancel '.$_gks_id_session , '');


//https://www.easyfilesselection.com/my/paypal-cancel.php?token=8PD057781N817943W

$_gks_session['gks']['payment_error'] = 'Payment canceled'; 

$token = trim_gks($_GET['token']);
if (strlen($token)<10) {
  debug_mail(false,'paypal-cancel.php token error:',$token);
  header('Location: /payment'.gks_set_lang_url()); die();      
}
//echo $token;
//die();
$my_page_title=gks_lang('Ακύρωση πληρωμής μέσω Paypal');
db_open();
stat_record();

$sql="SELECT id_payment_paypal FROM gks_payments_paypal where token='".$db_link->escape_string($token)."'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
if ($result->num_rows == 0) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
$row = $result->fetch_assoc();  
$id_payment_paypal=intval($row['id_payment_paypal']);


$sql="update gks_payments_paypal set status ='cancel'
where id_payment_paypal = ".$id_payment_paypal." limit 1";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
$sql="update gks_payments set payment_status ='cancel', status_message='cancel by user' where table_name='gks_payments_paypal' and table_id = ".$id_payment_paypal;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }

  
$sql="SELECT order_id FROM gks_payments where table_name='gks_payments_paypal' and table_id = ".$id_payment_paypal;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
if ($result->num_rows == 0) {
  debug_mail(false,'paypal-cancel.php error sql',$sql);
  header('Location: /payment'.gks_set_lang_url()); die();}
  
$row = $result->fetch_assoc();  
$order_id=$row['order_id'];
 
  
if ($order_id > 0) {
  $sql="update gks_orders set order_state='040cancelled' where id_order=".$order_id." and order_state='005prodraft' limit 1";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'paypal-cancel.php error sql',$sql);
    header('Location: /payment'.gks_set_lang_url()); die();}

}
  
$_gks_session['gks']['payment_error'] = 'Payment canceled';
gks_erp_cookie_save($_gks_id_session);

header('Location: /payment'.gks_set_lang_url()); die();