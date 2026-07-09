<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);

include_once('functions.php');



// https://www.easyfilesselection.com/my/paypal-success.php?token=8KY63865A8781563C&PayerID=ZTPD9XAM746S4



$token = trim_gks($_GET['token']);
if (strlen($token)<10) {
  debug_mail(false,'paypal error token:',$token);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα Paypal, δεν βρέθηκε το token');
  header('Location: /payment'.gks_set_lang_url()); die();      
}
$PayerID= trim_gks($_GET['PayerID']);
if (strlen($PayerID)<5) {
  debug_mail(false,'paypal error PayerID:',$PayerID);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα Paypal, δεν βρέθηκε το PayerID');
  header('Location: /payment'.gks_set_lang_url()); die();       
}
$my_page_title=gks_lang('Επιτυχής πληρωμή μέσω Paypal');
db_open();
stat_record();

debug_mail(false,'paypal success 1/3 '.$_gks_id_session , '');




$sql="SELECT id_payment_paypal, id_payment,gks_payments.order_id, gks_orders.gks_price_total, gks_orders.kostos_apostolis, gks_orders.kostos_pliromis,gks_orders.user_email
FROM (gks_payments_paypal 
LEFT JOIN gks_payments ON gks_payments_paypal.id_payment_paypal = gks_payments.table_id) 
LEFT JOIN gks_orders ON gks_payments.order_id = gks_orders.id_order
WHERE (((gks_payments_paypal.token)='".$db_link->escape_string($token)."') 
AND ((gks_payments.table_name)='gks_payments_paypal'))";
//debug_mail(false,'sql 1',$sql);

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();
}  
if ($result->num_rows == 0) {
  debug_mail(false,'paypal error need num_rows == 0 ',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Δεν βρέθηκε η εγγραφή εντολής');
  header('Location: /payment'.gks_set_lang_url()); die();  
}

$row = $result->fetch_assoc();
$id_order = $row['order_id'];
$id_payment = $row['id_payment'];
$id_payment_paypal = $row['id_payment_paypal'];
$pliroteo=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];
$user_email=$row['user_email'];



$sql="update gks_payments_paypal set status ='try 1',
PayerID='".$db_link->escape_string($PayerID)."'
where token='".$db_link->escape_string($token)."'";
//debug_mail(false,'sql 2',$sql);

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();
}  

$paypal_client_id_secret=$GKS_PAYPAL_REAL_CLIENT_ID.':'.$GKS_PAYPAL_REAL_SECRET;
if ($GKS_PAYPAL_SANDBOX) $paypal_client_id_secret=$GKS_PAYPAL_SAND_CLIENT_ID.':'.$GKS_PAYPAL_SAND_SECRET;

//Get an access token
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.".($GKS_PAYPAL_SANDBOX ? 'sandbox.' : '')."paypal.com/v1/oauth2/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "grant_type=client_credentials&",
  CURLOPT_HTTPHEADER => array(
    'accept: application/json',
    'accept-language: en_US',
    'content-type: application/x-www-form-urlencoded',
  ),
  CURLOPT_USERPWD => $paypal_client_id_secret, //  "$username:$password",
  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
));

// "client_id:secret"

$response_access_token = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    debug_mail(false,'paypal 4/5 error access_token',$response_access_token.' '.$err);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα δεδομένων').' Paypal (1)<br>'.gks_lang('Παρακαλώ ξαναδοκιμάστε');
    header('Location: /payment'.gks_set_lang_url()); die(); 
} else {
  $response_access_token=json_decode($response_access_token,true);
}  
$paypal_access_token=$response_access_token['access_token'];
debug_mail(false,'paypal 4/5 access_token '.$_gks_id_session , print_r($response_access_token,true));




// capture order
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.".($GKS_PAYPAL_SANDBOX ? 'sandbox.' : '')."paypal.com/v2/checkout/orders/".$token."/capture",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    'authorization: Bearer '.$paypal_access_token,
    'content-type: application/json'
  ),
));

$response_capture_order = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
debug_mail(false,'paypal 4/5 error access_token',$response_capture_order.' '.$err);

if ($err) {
    debug_mail(false,'paypal 4/5 error capture_order',$response_capture_order.' '.$err);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα δεδομένων').' Paypal (1)<br>'.gks_lang('Παρακαλώ ξαναδοκιμάστε');
    header('Location: /payment'.gks_set_lang_url()); die(); 
} else {
  $response_capture_order=json_decode($response_capture_order,true);
}



if (isset($response_capture_order['name']) and $response_capture_order['name'] == 'UNPROCESSABLE_ENTITY') {
  debug_mail(false,'paypal 5/5 error capture_order',$response_capture_order.' '.$err);
  if (isset($response_capture_order['details'][0]['issue']) and $response_capture_order['details'][0]['issue'] == 'ORDER_NOT_APPROVED') {
    $_gks_session['gks']['payment_error'] = 'Payment is not completed. Please retry.';
    header('Location: /payment'.gks_set_lang_url()); die();
  } else if  (isset($response_capture_order['details'][0]['issue']) and $response_capture_order['details'][0]['issue'] == 'ORDER_ALREADY_CAPTURED') {  
    $_gks_session['gks']['payment_error'] = 'Payment already completed.';
    header('Location: /payment'.gks_set_lang_url()); die();
  } else {
    $_gks_session['gks']['payment_error'] = 'Payment error. Please retry.';
    header('Location: /payment'.gks_set_lang_url()); die();
  }
}

if (isset($response_capture_order['status']) == false or $response_capture_order['status']!='COMPLETED' or 
    isset($response_capture_order['id']) == false or $response_capture_order['id']!= $token) {
  debug_mail(false,'paypal error','Payment error. Please retry.');
  $_gks_session['gks']['payment_error'] = 'Payment error. Please retry.';
  header('Location: /payment'.gks_set_lang_url()); die();}  
  
  

debug_mail(false,'paypal 5/5 capture_order '.$_gks_id_session , print_r($response_capture_order,true));


$sql="update gks_payments_paypal set status ='try 2',
details_json='".$db_link->escape_string(json_encode($response_capture_order))."',
PayerID='".$db_link->escape_string($response_capture_order['payer']['payer_id'])."',
EMAIL='".$db_link->escape_string($response_capture_order['payer']['email_address'])."',
FIRSTNAME='".$db_link->escape_string($response_capture_order['payer']['name']['given_name'])."',
LASTNAME='".$db_link->escape_string($response_capture_order['payer']['name']['surname'])."',
COUNTRYCODE='".$db_link->escape_string($response_capture_order['payer']['address']['country_code'])."',
CURRENCYCODE='".$db_link->escape_string($response_capture_order['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'])."',
TRANSACTIONID='".$db_link->escape_string($response_capture_order['purchase_units'][0]['payments']['captures'][0]['id'])."'
where token='".$db_link->escape_string($token)."'";
$result = $db_link->query($sql);


if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();
}  







$paypal_poso=0;
$paypal_fee=0;

if (isset($response_capture_order['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) 
  $paypal_poso = floatval($response_capture_order['purchase_units'][0]['payments']['captures'][0]['amount']['value']);
if (isset($response_capture_order['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value'])) 
  $paypal_fee = floatval($response_capture_order['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value']);

if (abs($paypal_poso - $pliroteo) > 0.01) {
  debug_mail(false,'paypal error','paypal_poso pliroteo');
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα δεδομένων').' Paypal (3)<br>'.gks_lang('Ασυμφωνία ποσού πληρωμής').'<br>'.gks_lang('Ανανεώστε την σελίδα');
  header('Location: /payment'.gks_set_lang_url()); die();     
}
$paypal_currency_code='';
if (isset($response_capture_order['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'])) 
  $paypal_currency_code = $response_capture_order['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
  
  
if ($paypal_currency_code!='EUR') {
  debug_mail(false,'paypal error','paypal_currency_code');
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα δεδομένων').' Paypal (4)<br>'.gks_lang('Ανανεώστε την σελίδα');
  header('Location: /payment'.gks_set_lang_url()); die();    
}








$sql="update gks_payments_paypal set status ='success',ACK='Success',payment_fee=".$paypal_fee.",mytype=1
where token='".$db_link->escape_string($token)."'";

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();  
}




$sql="update gks_orders set mdate_payment=NOW(), order_state='095execute' where id_order = ".$id_order." and order_state='005prodraft'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();  
}  

$sql="update gks_payments set payment_status='success',status_message='Success', payment_fee=".$paypal_fee." where id_payment = ".$id_payment." and payment_status!='success'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'paypal error sql',$sql);
  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
  header('Location: /payment'.gks_set_lang_url()); die();  
}  



$_gks_session['gks']['confirm']['id_object'] = $id_order;
$_gks_session['gks']['confirm']['payment_acquirer_type']='web';
$_gks_session['gks']['confirm']['poso']=$pliroteo;
$_gks_session['gks']['confirm']['bank9digit']='';  





$myreturn= order_execute_download($_gks_session['gks']['confirm']['id_object']);

if ($myreturn['code']=='OK') {
  gks_send_email_order_execute($_gks_session['gks']['confirm']['id_object']);
} else if ($myreturn['code']=='has_more_than_download') {
  gks_send_email_order_partial($_gks_session['gks']['confirm']['id_object']);
} else {
  gks_send_email_order_receive($_gks_session['gks']['confirm']['id_object']);
}



header('Location: /confirm'.gks_set_lang_url());
die();

