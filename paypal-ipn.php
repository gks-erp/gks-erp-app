<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');

debug_mail(false,'paypal ipn','');

$my_page_title=gks_lang('Paypal IPN');
db_open();
stat_record();

if(isset($_POST['txn_id']) and trim_gks(stripslashes(urldecode($_POST['txn_id'])))!='') {
  
  $sql="update gks_payments_paypal set ipn_json ='".$db_link->escape_string(json_encode($_POST))."'
  where TRANSACTIONID='".$db_link->escape_string($_POST['txn_id'])."'
  and ipn_json is null limit 1";
  debug_mail(false,'paypal ipn sql',$sql);


  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'paypal-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    gks_erp_cookie_save();
   // header('Location: /my/payment.php'); 
   die();
  }  

}
echo 'OK';
