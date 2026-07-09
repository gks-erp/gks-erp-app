<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);

if ($id<=0) {
  debug_mail(false,'error on id');
  $return = array('success' => false, 'message' => base64_encode('error on ID<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}

$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
$runguid='';if (isset($_POST['runguid'])) $runguid=trim_gks($_POST['runguid']);

  
$my_page_title=gks_lang('Λήψη ειδών από WooCommerce');
db_open();
stat_record();



if ($cmd=='progress') {
  if ($runguid=='') {
     $return = array('success' => true, 'message' => base64_encode('OK'),'count'=>0,'done' =>0, 'errors' => 0,'pososto' => 100);
    echo json_encode($return); die(); }  

    
  
  $sql="SELECT Count(*) AS cc,
  sum(if(`status`<>'pending' and `status`<>'running',1,0)) as done,
  sum(if(`status`<>'pending' and `status`<>'running' and result=0,1,0)) as errors
  FROM gks_async_queue
  WHERE guid='".$db_link->escape_string($runguid)."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
  $row = $result->fetch_assoc();
  $count=intval($row['cc']);
  $done=intval($row['done']);
  $errors=intval($row['errors']);
  $pososto=0;
  $pososto_str='';
  if ($count>0) {
    $pososto=round(100*floatval($done)/floatval($count),2);
    $pososto_str=number_format($pososto,2,$GKS_NUMBER_FORMAT_DECIMAL,'');
    
  }
  

  $return = array('success' => true, 'message' => base64_encode('OK'),'count'=>$count,'done' =>$done, 'errors' => $errors,'pososto' => $pososto,'pososto_str' => $pososto_str);
  echo json_encode($return); die();    
}


//$return = array('success' => false, 'message' => base64_encode('id:'.$id));
//echo json_encode($return); die();  

$ret = gks_woo_get_eshop($id);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die(); 
}
//$return = array('success' => false, 'message' => base64_encode(print_r($eshop,true)));
//echo json_encode($return); die(); 
$eshop=$ret['eshop'];
//$return = array('success' => false, 'message' => base64_encode(print_r($eshop,true)));
//echo json_encode($return); die(); 


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
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (21)<br>Ξαναδοκιμάστε αργότερα'));
  echo json_encode($return); die();  } 

$response_array=$ret['response_array'];
if ($response_array['success']==false) {
  $return = array('success' => false, 'message' => $response_array['message']);
  echo json_encode($return); die();  } 
  
if (isset($response_array['brands_plugins'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (23)<br>Ξαναδοκιμάστε αργότερα'));
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
	'catid'=>0,
);
$ret=gks_woo_post($eshop, $data);
//print '<pre>';print_r($ret);die();

if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  }  

if (isset($ret['response_array'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (11)<br>Ξαναδοκιμάστε αργότερα'));
  echo json_encode($return); die();  } 

$response_array=$ret['response_array'];

if (isset($response_array['categories_data'])==false) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (13)<br>Ξαναδοκιμάστε αργότερα'));
  echo json_encode($return); die();  } 

$categories_data=$response_array['categories_data'];
//print '<pre>';print_r($categories_data);die();

$ret=gks_woo_product_categories_update_local_from_woo($eshop,$categories_data);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die();  } 

//********************       categories end       *****************


$data = array(
	'cmd'=>'get_products_codes',
);
$ret = gks_woo_post($eshop, $data);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die(); 
}
$response_array=$ret['response_array'];
//echo '<pre>';print_r($ret);die();


$pids=$response_array['pids'];
if (count($pids)==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη')));
  echo json_encode($return); die(); 
}

$guid = guid_for_async_queue();
//echo '<pre>';print_r($pids);die();


$sql_values=[];
foreach ($pids as $value) {

  $sql_values[]= "(
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($guid)."','woo','pending','get_product','".$db_link->escape_string($id)."','".$db_link->escape_string($value)."'
  )";
  if (count($sql_values)>=250) {
    $sql="insert into gks_async_queue (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    guid,mytype,status,cmd,param1,param2
    ) values ".
    implode(',',$sql_values);
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    
    $sql_values=[];
  }
}
if (count($sql_values)>0) {
    $sql="insert into gks_async_queue (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    guid,mytype,status,cmd,param1,param2
    ) values ".
    implode(',',$sql_values);
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
}

//die();

gks_curl_post_async(GKS_SITE_URL.'my/cron_async_queue.php',array('guid' =>$guid));



$return = array('success' => true, 'message' => base64_encode('OK'), 'guid' => $guid, 'count' => count($pids));
echo json_encode($return); die(); 
