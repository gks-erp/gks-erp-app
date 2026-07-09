<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');

$product_id=0; if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);
if ($product_id<=0) {
  debug_mail(false,'the product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' product_id.'));
  echo json_encode($return); die();  } 

$id_woo_product=0; if (isset($_POST['id_woo_product'])) $id_woo_product=intval($_POST['id_woo_product']);
if ($id_woo_product<=0) {
  debug_mail(false,'the id_woo_product is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' id_woo_product.'));
  echo json_encode($return); die();  } 
  
$my_page_title=gks_lang('Συγχρονισμός είδους με ID: [1] με το id_woo_product: [2]');
$my_page_title=str_replace('[1]', $product_id, $my_page_title);
$my_page_title=str_replace('[2]', $id_woo_product, $my_page_title);



db_open();
stat_record();





$sql="select * from gks_woo_product where product_id=".$product_id." and id_woo_product=".$id_woo_product;
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
$eshop_id=$row['eshop_id'];
$remote_product_id=$row['remote_product_id'];
//echo '<pre>'.$remote_product_id;die();



$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops','edit',$eshop_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$ret = gks_woo_get_eshop($eshop_id);
//print '<pre>';print_r($ret);die();
if ($ret['success']==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  } 
  
$eshop=$ret['eshop'];

//********************       brands start       *****************

$data = array(
	'cmd'=>'get_brands',
	'brandid'=>0, //$remote_categories_id,
  'woosettings' => false,
);
$ret=gks_woo_post($eshop, $data);
//print '<pre>';print_r($ret);die();

if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  }  
//print '<pre>';print_r($ret);die();
if (isset($ret['response_array'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (21)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  } 

$response_array=$ret['response_array'];
if ($response_array['success']==false) {
  $return = array('success' => false, 'message' => $response_array['message']);
  echo json_encode($return); die();  } 
  
if (isset($response_array['brands_plugins'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (23)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  } 
//print '<pre>';print_r($response_array);die();

if ($response_array['brands_plugins']['berocket']['active']) {
  $ret=gks_woo_product_brand_update_local_from_woo($eshop,$response_array['brands_plugins']['berocket']['data'],'berocket');
  if ($ret['success']==false) {
    $return = array('success' => false, 'message' => $ret['message']);
    echo json_encode($return); die();  } 
}
//print '<pre>';print_r($response_array['brands_plugins']);die();

if ($response_array['brands_plugins']['woocommercebrand']['active']) {
  $ret=gks_woo_product_brand_update_local_from_woo($eshop,$response_array['brands_plugins']['woocommercebrand']['data'],'woocommercebrand');
  if ($ret['success']==false) {
    $return = array('success' => false, 'message' => $ret['message']);
    echo json_encode($return); die();  } 
}
//print '<pre>';print_r($response_array['brands_plugins']);die();

foreach (GKS_ESHOP_BRANDS_TAXONOMY as $brand_as_idiotita) {
  if ($response_array['brands_plugins']['gks-bai-'.$brand_as_idiotita['taxonomy']]['active']) {
    $ret=gks_woo_product_brand_update_local_from_woo($eshop,$response_array['brands_plugins']['gks-bai-'.$brand_as_idiotita['taxonomy']]['data'],'gks-bai-'.$brand_as_idiotita['taxonomy']);
    if ($ret['success']==false) {
      $return = array('success' => false, 'message' => $ret['message']);
      echo json_encode($return); die();  } 
  }
}

//********************       brands end       *****************



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


  

//********************       products start       *****************
//echo '<pre>';echo 'hhhhhhhhhhh';die();

$data = array(
	'cmd'=>'get_product',
	'pid'=>$remote_product_id,
	'woosettings' => true,
);
$ret=gks_woo_post($eshop, $data);
//print '<pre>';print_r($ret);die();

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
if ($response_array['success']==false) {
  $return = array('success' => false, 'message' => $response_array['message']);
  echo json_encode($return); die();  } 

if (isset($response_array['woo_settings'])==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  }  

$woo_settings=$response_array['woo_settings'];

if (isset($response_array['product'])==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (3)<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();  }  

$product=$response_array['product'];

//print '<pre>';print_r($product);die();
  
$ret=gks_woo_product_update_local_from_woo($eshop,$product,$woo_settings);
if ($ret['success']==false) {
  //debug_mail(false,'gks_woo_get_eshop error id:'.$eshop_id,print_r($ret,true)); den xreiazete, stlnei email i sinartisi
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  }  
          
//********************       products end       *****************


$return = array('success' => true, 'message' => base64_encode('ok'),'save_but_message' => $ret['save_but_message']);
echo json_encode($return); die();

