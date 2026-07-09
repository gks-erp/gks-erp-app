<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$eshop_id=0; if (isset($_POST['eshop_id'])) $eshop_id=intval($_POST['eshop_id']);
if ($eshop_id<=0) {
  debug_mail(false,'the eshop_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' eshop_id.'));
  echo json_encode($return); die();  } 
  
$my_page_title=gks_lang('Λήψη προϊόντων από το eshop:').' '.$eshop_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops','edit',$eshop_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$ret = gks_woo_get_eshop($eshop_id);
if ($ret['success']==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  } 
  
$eshop=$ret['eshop'];

//print '<pre>';print_r($eshop);die();

$data = array(
	'cmd'=>'get_products_codes_and_descrs',
);
$ret = gks_woo_post($eshop, $data);
if ($ret['success']==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  }  
//print '<pre>';print_r($ret);die();

if (isset($ret['response_array'])==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  }  

$response_array=$ret['response_array'];

if (isset($response_array['plist'])==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  }  

$plist=$response_array['plist'];

//print '<pre>';print_r($plist);die();

$return = array('success' => true, 'message' => base64_encode('OK'),'plist' => $plist);
echo json_encode($return); die();
