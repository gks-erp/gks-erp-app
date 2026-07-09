<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


//die();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση Τρόπου Πληρωμής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_payment_acquirers',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_payment_acquirers where id_payment_acquirer = ".$id;
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

$payment_acquirer_name=''; if (isset($_POST['payment_acquirer_name'])) $payment_acquirer_name=trim_gks(base64_decode($_POST['payment_acquirer_name']));
$payment_acquirer_table_name=''; if (isset($_POST['payment_acquirer_table_name'])) $payment_acquirer_table_name=trim_gks(base64_decode($_POST['payment_acquirer_table_name']));
$payment_acquirer_type=''; if (isset($_POST['payment_acquirer_type'])) $payment_acquirer_type=trim_gks(base64_decode($_POST['payment_acquirer_type']));
$payment_acquirer_type_dm=''; if (isset($_POST['payment_acquirer_type_dm'])) $payment_acquirer_type_dm=trim_gks(base64_decode($_POST['payment_acquirer_type_dm']));
$payment_acquirer_html=''; if (isset($_POST['payment_acquirer_html'])) $payment_acquirer_html=trim_gks(base64_decode($_POST['payment_acquirer_html']));
$payment_acquirer_button_html=''; if (isset($_POST['payment_acquirer_button_html'])) $payment_acquirer_button_html=trim_gks(base64_decode($_POST['payment_acquirer_button_html']));
$payment_acquirer_sxolio=''; if (isset($_POST['payment_acquirer_sxolio'])) $payment_acquirer_sxolio=trim_gks(base64_decode($_POST['payment_acquirer_sxolio']));
$payment_acquirer_tooltip=''; if (isset($_POST['payment_acquirer_tooltip'])) $payment_acquirer_tooltip=trim_gks(base64_decode($_POST['payment_acquirer_tooltip']));
$payment_acquirer_php_function_isok=''; if (isset($_POST['payment_acquirer_php_function_isok'])) $payment_acquirer_php_function_isok=trim_gks(base64_decode($_POST['payment_acquirer_php_function_isok']));
$payment_acquirer_php_function_calculate=''; if (isset($_POST['payment_acquirer_php_function_calculate'])) $payment_acquirer_php_function_calculate=trim_gks(base64_decode($_POST['payment_acquirer_php_function_calculate']));

$mysortorder=0; if (isset($_POST['mysortorder'])) $mysortorder=intval($_POST['mysortorder']);
$payment_acquirer_env_test=0; if (isset($_POST['payment_acquirer_env_test'])) $payment_acquirer_env_test=intval($_POST['payment_acquirer_env_test']);
$payment_acquirer_fees_enabled=0; if (isset($_POST['payment_acquirer_fees_enabled'])) $payment_acquirer_fees_enabled=intval($_POST['payment_acquirer_fees_enabled']);
$payment_acquirer_disabled=0; if (isset($_POST['payment_acquirer_disabled'])) $payment_acquirer_disabled=intval($_POST['payment_acquirer_disabled']);

$pa_fees_domestic_fixed=0; if (isset($_POST['pa_fees_domestic_fixed'])) $pa_fees_domestic_fixed=floatval(str_replace(',','.', $_POST['pa_fees_domestic_fixed']));
$pa_fees_domestic_percent=0; if (isset($_POST['pa_fees_domestic_percent'])) $pa_fees_domestic_percent=floatval(str_replace(',','.', $_POST['pa_fees_domestic_percent']));
$pa_fees_international_fixed=0; if (isset($_POST['pa_fees_international_fixed'])) $pa_fees_international_fixed=floatval(str_replace(',','.', $_POST['pa_fees_international_fixed']));
$pa_fees_international_percent=0; if (isset($_POST['pa_fees_international_percent'])) $pa_fees_international_percent=floatval(str_replace(',','.', $_POST['pa_fees_international_percent']));

$aade_tropos_pliromis_id=0; if (isset($_POST['aade_tropos_pliromis_id'])) $aade_tropos_pliromis_id=intval($_POST['aade_tropos_pliromis_id']);
$payment_acquirer_with_id=0; if (isset($_POST['payment_acquirer_with_id'])) $payment_acquirer_with_id=intval($_POST['payment_acquirer_with_id']);
$show_acc_pay=0; if (isset($_POST['show_acc_pay'])) $show_acc_pay=intval($_POST['show_acc_pay']);
$show_eshop=0; if (isset($_POST['show_eshop'])) $show_eshop=intval($_POST['show_eshop']);


if ($payment_acquirer_name=='') {debug_mail(false,'emptyl',      gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$sql="select * from gks_payment_acquirers where payment_acquirer_name like '".$db_link->escape_string($payment_acquirer_name)."' and id_payment_acquirer<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη:<br><a href="admin-payment-acquirers-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$payment_acquirer_name,$message);
  $message=str_replace('[2]',$row['id_payment_acquirer'],$message);
  debug_mail(false,'delivery-methods exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($payment_acquirer_type=='') {debug_mail(false,'emptyl',      gks_lang('Ο τύπος ΔΕΝ μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τύπος ΔΕΝ μπορεί να είναι κενός')));
  echo json_encode($return); die(); }

if ($payment_acquirer_table_name != '') {
  if (substr($payment_acquirer_table_name, 0,13)  != 'gks_payments_' ) {
    debug_mail(false,'emptyl',                                     gks_lang('Ο Σχετικός Πίνακας θα μπορεί να ξεκινά από <b>gks_payments_</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Σχετικός Πίνακας θα μπορεί να ξεκινά από <b>gks_payments_</b>')));
    echo json_encode($return); die();    
  } else {
    $sql="show tables like '".$db_link->escape_string($payment_acquirer_table_name)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result->num_rows!=1) {
      debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε ο πίνακας').'<br>'.$payment_acquirer_table_name);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο πίνακας').'<br>'.$payment_acquirer_table_name));
      echo json_encode($return); die();       
    }
  }
}


if ($payment_acquirer_type_dm=='') {debug_mail(false,'emptyl',   gks_lang('Οι σχετικοί τύποι αποστολής ΔΕΝ μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Οι σχετικοί τύποι αποστολής ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($payment_acquirer_html=='') {debug_mail(false,'emptyl',      gks_lang('Η HTML (FrontEnd) ΔΕΝ μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η HTML (FrontEnd) ΔΕΝ μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


$sql="select * from gks_payment_acquirers where payment_acquirer_html like '".$db_link->escape_string($payment_acquirer_html)."' and id_payment_acquirer<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η HTML (FrontEnd) <b>[1]</b> υπάρχει ήδη:<br><a href="admin-payment-acquirers-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$payment_acquirer_html,$message);
  $message=str_replace('[2]',$row['id_payment_acquirer'],$message);
  debug_mail(false,'delivery-methods exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}



if ($payment_acquirer_php_function_isok != '') {
  if (substr($payment_acquirer_php_function_isok, 0,27)  != 'gks_calculate_isok_payment_' ) {
    debug_mail(false,'emptyl',                                     gks_lang('Η Συνάρτηση ενεργοποίησης θα μπορεί να ξεκινά από <b>gks_calculate_isok_payment_</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Συνάρτηση ενεργοποίησης θα μπορεί να ξεκινά από <b>gks_calculate_isok_payment_</b>')));
    echo json_encode($return); die();    
  } else {
    if (function_exists($payment_acquirer_php_function_isok) ==false ) {
      debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε η συνάρτηση').'<br>'.$payment_acquirer_php_function_isok);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η συνάρτηση').'<br>'.$payment_acquirer_php_function_isok));
      echo json_encode($return); die();       
    }
  }
}

if ($payment_acquirer_php_function_calculate != '') {
  if (substr($payment_acquirer_php_function_calculate, 0,30)  != 'gks_calculate_kostos_pliromis_' ) {
    debug_mail(false,'emptyl',                                     gks_lang('Η Συνάρτηση υπολογισμού θα μπορεί να ξεκινά από <b>gks_calculate_kostos_pliromis_</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Συνάρτηση υπολογισμού θα μπορεί να ξεκινά από <b>gks_calculate_kostos_pliromis_</b>')));
    echo json_encode($return); die();    
  } else {
    if (function_exists($payment_acquirer_php_function_calculate) ==false ) {
      debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε η συνάρτηση').'<br>'.$payment_acquirer_php_function_calculate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η συνάρτηση').'<br>'.$payment_acquirer_php_function_calculate));
      echo json_encode($return); die();       
    }
  }
}







$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_payment_acquirers');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_payment_acquirers (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-payment-acquirers-item.php?id='.$id); 
}

$sql="update gks_payment_acquirers set 
payment_acquirer_name='".$db_link->escape_string($payment_acquirer_name)."',
payment_acquirer_type='".$db_link->escape_string($payment_acquirer_type)."',
payment_acquirer_type_dm='".$db_link->escape_string($payment_acquirer_type_dm)."',
payment_acquirer_html='".$db_link->escape_string($payment_acquirer_html)."',
payment_acquirer_button_html='".$db_link->escape_string($payment_acquirer_button_html)."',
payment_acquirer_sxolio='".$db_link->escape_string($payment_acquirer_sxolio)."',
payment_acquirer_tooltip='".$db_link->escape_string($payment_acquirer_tooltip)."',
payment_acquirer_php_function_isok='".$db_link->escape_string($payment_acquirer_php_function_isok)."',
payment_acquirer_php_function_calculate='".$db_link->escape_string($payment_acquirer_php_function_calculate)."',
payment_acquirer_env_test=".$payment_acquirer_env_test.",
payment_acquirer_fees_enabled=".$payment_acquirer_fees_enabled.",
pa_fees_domestic_fixed=".$pa_fees_domestic_fixed.",
pa_fees_domestic_percent=".$pa_fees_domestic_percent.",
pa_fees_international_fixed=".$pa_fees_international_fixed.",
pa_fees_international_percent=".$pa_fees_international_percent.",

mysortorder=".$mysortorder.",
payment_acquirer_disabled=".$payment_acquirer_disabled.",
aade_tropos_pliromis_id=".$aade_tropos_pliromis_id.",
payment_acquirer_with_id=".$payment_acquirer_with_id.",
show_acc_pay=".$show_acc_pay.",
show_eshop=".$show_eshop.",




user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_payment_acquirer = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_payment_acquirers',$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

