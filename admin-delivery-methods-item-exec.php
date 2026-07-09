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


$my_page_title=gks_lang('Αποθήκευση Τρόπου Αποστολής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_delivery_methods',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_delivery_methods where id_delivery_method = ".$id;
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

$delivery_method_name=''; if (isset($_POST['delivery_method_name'])) $delivery_method_name=trim_gks(base64_decode($_POST['delivery_method_name']));
$delivery_method_type=''; if (isset($_POST['delivery_method_type'])) $delivery_method_type=trim_gks(base64_decode($_POST['delivery_method_type']));
$delivery_method_type_pa=''; if (isset($_POST['delivery_method_type_pa'])) $delivery_method_type_pa=trim_gks(base64_decode($_POST['delivery_method_type_pa']));
$delivery_method_html=''; if (isset($_POST['delivery_method_html'])) $delivery_method_html=trim_gks(base64_decode($_POST['delivery_method_html']));
$delivery_method_sxolio=''; if (isset($_POST['delivery_method_sxolio'])) $delivery_method_sxolio=trim_gks(base64_decode($_POST['delivery_method_sxolio']));
$delivery_method_tooltip=''; if (isset($_POST['delivery_method_tooltip'])) $delivery_method_tooltip=trim_gks(base64_decode($_POST['delivery_method_tooltip']));
$delivery_method_php_function_isok=''; if (isset($_POST['delivery_method_php_function_isok'])) $delivery_method_php_function_isok=trim_gks(base64_decode($_POST['delivery_method_php_function_isok']));
$delivery_method_php_function_calculate=''; if (isset($_POST['delivery_method_php_function_calculate'])) $delivery_method_php_function_calculate=trim_gks(base64_decode($_POST['delivery_method_php_function_calculate']));

$mysortorder=0; if (isset($_POST['mysortorder'])) $mysortorder=intval($_POST['mysortorder']);
$delivery_method_env_test=0; if (isset($_POST['delivery_method_env_test'])) $delivery_method_env_test=intval($_POST['delivery_method_env_test']);
$delivery_method_fees_enabled=0; if (isset($_POST['delivery_method_fees_enabled'])) $delivery_method_fees_enabled=intval($_POST['delivery_method_fees_enabled']);
$delivery_method_disabled=0; if (isset($_POST['delivery_method_disabled'])) $delivery_method_disabled=intval($_POST['delivery_method_disabled']);

$dm_fees_price=0; if (isset($_POST['dm_fees_price'])) $dm_fees_price=floatval(str_replace(',','.', $_POST['dm_fees_price']));
$dm_fees_free_if_greater_than=0; if (isset($_POST['dm_fees_free_if_greater_than'])) $dm_fees_free_if_greater_than=floatval(str_replace(',','.', $_POST['dm_fees_free_if_greater_than']));
$dm_fees_international_fixed=0; if (isset($_POST['dm_fees_international_fixed'])) $dm_fees_international_fixed=floatval(str_replace(',','.', $_POST['dm_fees_international_fixed']));



if ($delivery_method_name=='') {debug_mail(false,'emptyl',       gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$sql="select * from gks_delivery_methods where delivery_method_name like '".$db_link->escape_string($delivery_method_name)."' and id_delivery_method<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]', $delivery_method_name, $message);
  $message.='<br><a href="admin-delivery-methods-item.php?id='.$row['id_delivery_method'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'delivery-methods exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($delivery_method_type=='') {debug_mail(false,'emptyl',       gks_lang('Ο τύπος ΔΕΝ μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τύπος ΔΕΝ μπορεί να είναι κενός')));
  echo json_encode($return); die(); }
  
if ($delivery_method_type_pa=='') {debug_mail(false,'emptyl',    gks_lang('Οι σχετικοί τύποι πληρωμής ΔΕΝ μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Οι σχετικοί τύποι πληρωμής ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($delivery_method_html=='') {debug_mail(false,'emptyl',       gks_lang('Η HTML (FrontEnd) ΔΕΝ μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η HTML (FrontEnd) ΔΕΝ μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


$sql="select * from gks_delivery_methods where delivery_method_html like '".$db_link->escape_string($delivery_method_html)."' and id_delivery_method<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η HTML (FrontEnd) <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$delivery_method_html,$message);
  $message.='<br><a href="admin-delivery-methods-item.php?id='.$row['id_delivery_method'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'delivery-methods exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}



if ($delivery_method_php_function_isok != '') {
  if (substr($delivery_method_php_function_isok, 0,28)  != 'gks_calculate_isok_delivery_' ) {
    debug_mail(false,'emptyl',                                     gks_lang('Η Συνάρτηση ενεργοποίησης θα μπορεί να ξεκινά από <b>gks_calculate_isok_delivery_</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Συνάρτηση ενεργοποίησης θα μπορεί να ξεκινά από <b>gks_calculate_isok_delivery_</b>')));
    echo json_encode($return); die();    
  } else {
    if (function_exists($delivery_method_php_function_isok) ==false ) {
      $message=gks_lang('Δεν βρέθηκε η συνάρτηση <b>[1]</b>');
      $message=str_replace('[1]',$delivery_method_php_function_isok,$message);
      debug_mail(false,'emptyl',$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();       
    }
  }
}

if ($delivery_method_php_function_calculate != '') {
  if (substr($delivery_method_php_function_calculate, 0,30)  != 'gks_calculate_kostos_delivery_' ) {
    debug_mail(false,'emptyl',                                     gks_lang('Η Συνάρτηση υπολογισμού θα μπορεί να ξεκινά από <b>gks_calculate_isok_delivery_</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Συνάρτηση υπολογισμού θα μπορεί να ξεκινά από <b>gks_calculate_isok_delivery_</b>')));
    echo json_encode($return); die();    
  } else {
    if (function_exists($delivery_method_php_function_calculate) ==false ) {
      $message=gks_lang('Δεν βρέθηκε η συνάρτηση <b>[1]</b>');
      $message=str_replace('[1]',$delivery_method_php_function_calculate,$message);
      debug_mail(false,'emptyl',$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();       
    }
  }
}







$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_delivery_methods');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_delivery_methods (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-delivery-methods-item.php?id='.$id); 
}

$sql="update gks_delivery_methods set 
delivery_method_name='".$db_link->escape_string($delivery_method_name)."',
delivery_method_type='".$db_link->escape_string($delivery_method_type)."',
delivery_method_type_pa='".$db_link->escape_string($delivery_method_type_pa)."',
delivery_method_html='".$db_link->escape_string($delivery_method_html)."',
delivery_method_sxolio='".$db_link->escape_string($delivery_method_sxolio)."',
delivery_method_tooltip='".$db_link->escape_string($delivery_method_tooltip)."',
delivery_method_php_function_isok='".$db_link->escape_string($delivery_method_php_function_isok)."',
delivery_method_php_function_calculate='".$db_link->escape_string($delivery_method_php_function_calculate)."',
delivery_method_env_test=".$delivery_method_env_test.",
delivery_method_fees_enabled=".$delivery_method_fees_enabled.",
dm_fees_price=".$dm_fees_price.",
dm_fees_free_if_greater_than=".$dm_fees_free_if_greater_than.",
dm_fees_international_fixed=".$dm_fees_international_fixed.",

mysortorder=".$mysortorder.",
delivery_method_disabled=".$delivery_method_disabled.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_delivery_method = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_delivery_methods',$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

