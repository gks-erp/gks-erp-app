<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');

//if ($my_wp_user_id == 0) {die();}
debug_mail(false,'alphabank success '.$_gks_id_session , '');

if (!isset($_gks_session['gks']['alphabank']['id_object']) or 
    !isset($_gks_session['gks']['alphabank']['order_guid']) or 
    !isset($_gks_session['gks']['alphabank']['pliroteo']) ) {
  header('Location: /basket'); die();      
}

if (count($_POST) != 12 or 
  !isset($_POST['mid']) or !isset($_POST['orderid']) or !isset($_POST['status']) or !isset($_POST['orderAmount']) or 
  !isset($_POST['currency']) or !isset($_POST['paymentTotal']) or !isset($_POST['message']) or !isset($_POST['riskScore']) or 
  !isset($_POST['payMethod']) or !isset($_POST['txId']) or !isset($_POST['paymentRef']) ) {
    
  header('Location: /basket'); die();      
}


$my_alpha=my_alphabank_settings();

$mystr=$_POST['mid'].$_POST['orderid'].$_POST['status'].$_POST['orderAmount'].
      $_POST['currency'].$_POST['paymentTotal'].$_POST['message'].$_POST['riskScore'].
      $_POST['payMethod'].$_POST['txId'].$_POST['paymentRef'].
      $my_alpha['shared_secret_key'];
      
$mydigest= base64_encode(sha1($mystr,true));

$final_check = trim_gks(strtolower($mydigest)) == trim_gks(strtolower($_POST['digest']));


//echo '---'.$final_check.'--'.$mydigest.'----'.$_POST['digest'];
//die();

$my_page_title=gks_lang('Επιτυχής πληρωμή μέσω τράπεζας');
db_open();
stat_record();




$sql="update gks_payments_alphabank set status ='try 2',";
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
if (isset($_POST['paymentRef'])) {
  $sql.=" paymentRef ='".$db_link->escape_string($_POST['paymentRef'])."',";
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
  
  
  
  


  
if ($final_check) {  
  $_gks_session['gks']['confirm']['id_object'] = $_gks_session['gks']['alphabank']['id_object'];
  $_gks_session['gks']['confirm']['payment_acquirer_type']='web';
  $_gks_session['gks']['confirm']['poso']=$_gks_session['gks']['alphabank']['pliroteo'];
  $_gks_session['gks']['confirm']['bank9digit']='';  
  
  
  gks_send_email_order_receive($_gks_session['gks']['confirm']['id_object']);


  $sql="update gks_orders set mdate_payment=NOW(), order_state='draft' where id_order = ".$_gks_session['gks']['confirm']['id_object'] ;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'alphabank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'); die();  
  }  
  
  $sql="update gks_payments set status_message='ok',status_message='".$db_link->escape_string($_POST['status'])."', payment_status='success' where id_payment = ".$_gks_session['gks']['basket']['payment']['id_payment'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'alphabank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'); die();  
  }  
  
  $sql="update gks_payments_alphabank set status='success' where id_payment_alphabank = ".$_gks_session['gks']['basket']['payment']['table_id'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'alphabank-success.php error sql',$sql);
    $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά');
    header('Location: /payment'); die();  
  }  
  
  $myreturn= order_execute_download($_gks_session['gks']['confirm']['id_object']);
  if ($myreturn['code']=='OK') {
    $mymessage=gks_lang('Γειά σας').',<br>
    <br>
    <br>
    '.str_replace('[1]',$_gks_session['gks']['confirm']['id_object'],gks_lang('Η παραγγελία σας με αριθμό [1] έχει εκτελεστεί επιτυχώς')).'<br>';
  
    $replaces=array();
    $replaces[] = array('[[id_order]]',$_gks_session['gks']['confirm']['id_object']);
    $replaces[] = array('[[message]]',$mymessage);

    $params=array(
      'model'=>'order',
      'model_id'=>$_gks_session['gks']['confirm']['id_object'],
      'to'=>$my_wp_user_info->data->user_email,
      'subject'=>gks_lang('Εκτέλεση της παραγγελίας').' '.$_gks_session['gks']['confirm']['id_object'],
      'template'=>9, //'order_execute.html',
      'replaces'=>$replaces,
    );
        
    $send_email_res = gks_mymail_template($params);
  
  }
  if ($myreturn['code']=='has_more_than_download') {
    $mymessage=gks_lang('Γειά σας').',<br>
    <br>
    <br>
    '.str_replace('[1]',$_gks_session['gks']['confirm']['id_object'],gks_lang('Η παραγγελία σας με αριθμό [1] έχει εκτελεστεί μερικώς')).'<br>
    '.gks_lang('Θα ενημερωθείτε με άλλο μήνυμα για την εξέλιξη της παραγγελίας').'<br>';
  
    $replaces=array();
    $replaces[] = array('[[id_order]]',$_gks_session['gks']['confirm']['id_object']);
    $replaces[] = array('[[message]]',$mymessage);

    $params=array(
      'model'=>'order',
      'model_id'=>$_gks_session['gks']['confirm']['id_object'],
      'to'=>$my_wp_user_info->data->user_email,
      'subject'=>gks_lang('Μερική εκτέλεση της παραγγελίας').' '.$_gks_session['gks']['confirm']['id_object'],
      'template'=>'order_execute_partial.html',
      'replaces'=>$replaces,
    );
        
    $send_email_res = gks_mymail_template($params);
  
  }
  


  unset($_gks_session['gks']['payment_error']);
  unset($_gks_session['gks']['alphabank']);  
  
  header('Location: /confirm'); die();  
  
} else {

  debug_mail(false,'alphabank success -> fail '.$_gks_id_session , '');

  $_gks_session['gks']['payment_error'] = gks_lang('Σφάλμα κατά την επεξεργασία της συναλλαγής').' (326785).';
  unset($_gks_session['gks']['alphabank']);
  
  header('Location: /payment'); die();   
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
