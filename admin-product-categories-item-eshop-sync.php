<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$product_category_id=0; if (isset($_POST['product_category_id'])) $product_category_id=intval($_POST['product_category_id']);
if ($product_category_id<=0) {
  debug_mail(false,'the product_category_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' product_category_id.'));
  echo json_encode($return); die();  } 

$eshop_id=0; if (isset($_POST['eshop_id'])) $eshop_id=intval($_POST['eshop_id']);
if ($eshop_id<=0) {
  debug_mail(false,'the eshop_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' eshop_id.'));
  echo json_encode($return); die();  } 
  
$my_page_title=gks_lang('Συγχρονισμός κατηγορίας με ID').': '.$product_category_id.' '.gks_lang('με το eshop').': '.$eshop_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops','edit',$eshop_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="select remote_category_id from gks_woo_categories where product_category_id=".$product_category_id." and eshop_id=".$eshop_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 
if ($result->num_rows==0) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η καταχώρηση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();  }   


$row = $result->fetch_assoc();
$remote_category_id=$row['remote_category_id'];
//echo '<pre>'.$remote_category_id;die();



$ret = gks_woo_get_eshop($eshop_id);
//print '<pre>';print_r($ret);die();
if ($ret['success']==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  } 
  
$eshop=$ret['eshop'];





//********************       categories start       *****************

$data = array(
	'cmd'=>'get_categories',
	'catid'=>0, //$remote_categories_id,
	'woosettings' => false,
);
$ret=gks_woo_post($eshop, $data);
//print '<pre>';print_r($ret);die();

if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  }  

if (isset($ret['response_array'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (11)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  } 

$response_array=$ret['response_array'];
if ($response_array['success']==false) {
  $return = array('success' => false, 'message' => $response_array['message']);
  echo json_encode($return); die();  } 

if (isset($response_array['categories_data'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (13)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  } 

$categories_data=$response_array['categories_data'];
//print '<pre>';print_r($categories_data);die();

$ret=gks_woo_product_categories_update_local_from_woo($eshop,$categories_data);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  } 

//********************       categories end       *****************





$return = array('success' => true, 'message' => base64_encode('ok'),'save_but_message' => '');
echo json_encode($return); die();

