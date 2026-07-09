<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
ini_set('max_execution_time', 600);
set_time_limit(600);

putenv("ENV=PRODUCTION");
define('SECURE', 1);





require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');


if (isset($gks_user_settings)==false) $gks_user_settings=array();
//$my_wp_user_id=2;

db_open();

$return = array('success' => false, 'message' => base64_encode('generic api transfer error'),'data' => false);

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
  $return['message']=base64_encode('json decode error');debug_mail(false,'json decode Error','');echo json_encode($return); die();
}


//debug_mail(false,'api.transfer.v1.php','');
if (isset($input_data['cmd'])==false or trim_gks($input_data['cmd'])=='')         {$return['message']=base64_encode('cmd is not set');debug_mail(false,'cmd is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['rnd'])==false or trim_gks($input_data['rnd'])=='')         {$return['message']=base64_encode('rnd is not set');debug_mail(false,'rnd is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['token'])==false or trim_gks($input_data['token'])=='')     {$return['message']=base64_encode('token is not set');debug_mail(false,'token is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['user_ip'])==false or trim_gks($input_data['user_ip'])=='') {$return['message']=base64_encode('user_ip is not set');debug_mail(false,'user_ip is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}


$sql="select * from gks_transfer where transfer_disable=0 and transfer_website_key<>''";
$result = $db_link->query($sql);
if (!$result) {$return['message']=base64_encode('sql error');debug_mail(false,'sql error',$sql); echo json_encode($return); die();}

$id_transfer=0;$row_transfer=array();
while ($row = $result->fetch_assoc()) {
  $token=md5($input_data['rnd'].$row['transfer_website_key'].$input_data['rnd']);
  if ($token == $input_data['token']) {
    $id_transfer=intval($row['id_transfer']);
    $row_transfer=$row;
    break;
  }
}  
if ($id_transfer<=0) {$return['message']=base64_encode('transfer not found');debug_mail(false,'transfer not found',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}

//$return['message']=base64_encode('dddd');echo json_encode($return); die();

//echo '<pre>'.$input_data['cmd'];die();
//echo '<pre>'; print_r($input_data);die();

switch ($input_data['cmd']) {   
  
  case 'transfer_reservation_form':
    include_once('api.transfer.v1.cmd_transfer_reservation_form.php');
    $return['data'] = gks_api_transfer_cmd_transfer_reservation_form($id_transfer,$row_transfer,$input_data); 
    break;  
  case 'transfer_reservation_search';
    include_once('api.transfer.v1.cmd_transfer_reservation_search.php');
    $return['data'] = gks_api_transfer_cmd_transfer_reservation_search($id_transfer,$row_transfer,$input_data); 
    break;  
  case 'transfer_destinations';
    include_once('api.transfer.v1.cmd_transfer_destinations.php');
    $return['data'] = gks_api_transfer_cmd_transfer_destinations($id_transfer,$row_transfer,$input_data); 
    break;  

  
//  case 'transfer_reservation':
//    include_once('api.transfer.v1.cmd_transfer_reservation.php');
//    $return['data'] = gks_api_transfer_cmd_transfer_reservation($id_transfer,$row_transfer,$input_data); 
//    break;  
//  case 'transfer_reservation_search_calc';
//    include_once('api.transfer.v1.cmd_transfer_reservation_search_calc.php');
//    $return['data'] = gks_api_transfer_cmd_transfer_reservation_search_calc($id_transfer,$row_transfer,$input_data); 
//    break;  
// case 'transfer_reservation_basket_add':
//    include_once('api.transfer.v1.cmd_transfer_reservation_basket_add.php');
//    $return['data'] = gks_api_transfer_cmd_transfer_reservation_basket_add($id_transfer,$row_transfer,$input_data); 
//    break;  
//  case 'transfer_reservation_basket':
//    include_once('api.transfer.v1.cmd_transfer_reservation_basket.php');
//    $return['data'] = gks_api_transfer_cmd_transfer_reservation_basket($id_transfer,$row_transfer,$input_data); 
//    break;  
// case 'basket_edit':
//    include_once('api.v1.cmd_basket_edit.php');
//    $return['data'] = gks_api_cmd_basket_edit($id_transfer,$row_transfer,$input_data); 
//    break;
// case 'checkout':
//    include_once('api.v1.cmd_checkout.php');
//    $return['data'] = gks_api_cmd_checkout($id_transfer,$row_transfer,$input_data); 
//    break;
// case 'checkout_edit':
//    include_once('api.v1.cmd_checkout_edit.php');
//    $return['data'] = gks_api_cmd_checkout_edit($id_transfer,$row_transfer,$input_data); 
//    break;
// case 'get_nomoi':
//    $return['data'] = gks_api_cmd_get_nomoi($input_data); 
//    break;
// case 'payment':
//    include_once('api.v1.cmd_payment.php');
//    $return['data'] = gks_api_cmd_payment($id_transfer,$row_transfer,$input_data); 
//    break;
// case 'payment_edit':
//    include_once('api.v1.cmd_payment_edit.php');
//    $return['data'] = gks_api_cmd_payment_edit($id_transfer,$row_transfer,$input_data); 
//    break;
// case 'confirm':
//    include_once('api.v1.cmd_confirm.php');
//    $return['data'] = gks_api_cmd_confirm($id_transfer,$row_transfer,$input_data); 
//    break;
//  case 'woo_add_to_basket':
//    include_once('api.v1.cmd_woo_add_to_basket.php');
//    $return['data'] = gks_api_cmd_woo_add_to_basket($id_transfer,$row_transfer,$input_data); 
//    break;
//  
//  case 'transfer_reservation_empty_basket':
//    include_once('api.transfer.v1.cmd_transfer_reservation_empty_basket.php');
//    $return['data'] = gks_api_transfer_cmd_transfer_reservation_empty_basket($input_data); 
//    break;
  
  case 'transfer_country':
    $return['data']=gks_api_transfer_cmd_transfer_country();
    break;
  
  case 'transfer_reservation_my_transfer':
    $return['data']=transfer_reservation_my_transfer($input_data);
    break;
    
  case 'transfer_reservation_my_transfer_item':
    include_once('api.transfer.v1.cmd_transfer_reservation_my_transfer_item.php');
    $return['data']=gks_api_transfer_cmd_transfer_reservation_my_transfer_item($id_transfer,$row_transfer,$input_data);
    break;

  case 'transfer_reservation_my_transfer_item_cancel':
    include_once('api.transfer.v1.cmd_transfer_reservation_my_transfer_item_cancel.php');
    $return['data']=gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel($id_transfer,$row_transfer,$input_data);
    break;

  case 'transfer_reservation_my_transfer_item_cancel_confirm': 
    $return['data']=gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm($id_transfer,$row_transfer,$input_data);
    break;
  case 'transfer_reservation_my_transfer_item_cancel_confirm_exec': 
    $return['data']=gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm_exec($id_transfer,$row_transfer,$input_data);
    break;
    
  default:
    $return['message']=base64_encode('cmd not found: '.$input_data['cmd']);debug_mail(false,'cmd not found',$input_data['cmd']); echo json_encode($return); die();
}

$return['message']='OK';
$return['success']=true;
echo json_encode($return); die();


$return['message']=base64_encode('<pre>'.$id_transfer."\r\n".print_r($input_data,true)."\r\n".print_r($row_transfer,true).'</pre>'); echo json_encode($return); die();




function gks_api_cmd_get_nomoi($input_data) {
  global $db_link;

  $_POST=$input_data['post'];
  

  if (!isset($_POST['id'])) {
    debug_mail(false,'error on id');
    $return = array('success' => false, 'message' => base64_encode('error on id<br>'.gks_lang('Ανανεώστε την σελίδα')));
    return $return;
  }
  $id=intval($_POST['id']);
  
  if ($id<0) {
    debug_mail(false,'error on id (2):'.$id);
    $return = array('success' => false, 'message' => base64_encode('error on id<br>'.gks_lang('Ανανεώστε την σελίδα')));
    return $return;  
  }
  
  $my_page_title=gks_lang('Λήψη λίστας νομών');
  //db_open();
  //stat_record();
  //$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_nomoi','autocomplete',0);
  //if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));return $return;}
  
  $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
  gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));  
  
  $out=array();
  
  $sql="SELECT id_nomos, ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
  FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
  ".$lang_prepare_gks_nomoi['sql']['from2']."
  WHERE country_id=".$id." 
  ORDER BY ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi,'',true);
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; 
  }
  
  while ($row = $result->fetch_assoc()) {  
    $out[]=array('id' => $row['id_nomos'], 'descr'=> $row['nomos_descr']);
  }
    
  $return = array('success' => true, 'message' => base64_encode('ok'),'out' => $out,);
  return $return;  
  
  
  
}

function gks_api_transfer_cmd_transfer_country() {
  global $db_link;  
  
  $out=array();
  
  $sql="SELECT * FROM gks_country 
  WHERE phone_code<> '' 
  ORDER BY country_name_en_US";
  
  $sql="SELECT id_country, country_initials,phone_code, country_name_en_US
  FROM gks_country 
  LEFT JOIN (
    SELECT country_id, country_name as country_name_en_US FROM gks_country_lang WHERE lang_code='en-US'
  ) AS gks_country_en_US ON gks_country.id_country = gks_country_en_US.country_id
  WHERE gks_country.phone_code<> '' 
  ORDER BY country_name_en_US";

  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; 
  }
  
  while ($row = $result->fetch_assoc()) {  
    $out[]=array('id' => $row['id_country'], 'two' => $row['country_initials'],'phone_code' => $row['phone_code'], 'name'=> $row['country_name_en_US']);
  }
  
  return $out;
}


function transfer_reservation_my_transfer($input_data) {
  global $db_link;  
  
  $my_prefix=trim_gks($input_data['get_data']['my_prefix']);
  $my_number=trim_gks($input_data['get_data']['my_number']);
  $my_email =trim_gks($input_data['get_data']['my_email']);
  
  
  $out=array(
    'success'=>false,
    'message' => 'generic error at my transfer func',
    'my_prefix' => $my_prefix,
    'my_number' => $my_number,
    'my_email' => $my_email,
  );

  if ($my_prefix=='') {$out['message']='prefix is not set'; return $out;}
  if ($my_number=='') {$out['message']='number is not set'; return $out;}
  if ($my_email=='')  {$out['message']='email is not set'; return $out;}
  
  $transfer_booking_number=$my_prefix.$my_number;
  $sql_templete="select gks_transfer_reservation.*, ".GKS_WP_TABLE_PREFIX."users.user_email,display_name,gks_nickname
  FROM gks_transfer_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where transfer_booking_number like '[[transfer_booking_number]]'
  and transfer_reservation_status in ('040cancelled','050rejected','070wait_payment','080confirm')
  and transfer_start >= date_sub(now(), interval 12 hour)
  and (
    gks_transfer_reservation.user_email like '".$db_link->escape_string($my_email)."' or 
    gks_transfer_reservation.other_email like '".$db_link->escape_string($my_email)."' or
    ".GKS_WP_TABLE_PREFIX."users.user_email like '".$db_link->escape_string($my_email)."'
  )
  order by id_transfer_reservation desc limit 1";
  
  $my_recs=array();

  $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number),$sql_templete);
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $my_recs[]=$row;
  }
  
  if (count($my_recs)==0) {
    $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number.'-1'),$sql_templete);
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
      return $return; }
    while ($row = $result->fetch_assoc()) {  
      $my_recs[]=$row;
    }
  }
//  if (count($my_recs)==0) {
//    $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number.'-2'),$sql_templete);
//    $result = $db_link->query($sql);
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
//      return $return; }
//    while ($row = $result->fetch_assoc()) {  
//      $my_recs[]=$row;
//    }
//  }
  
  if (count($my_recs)==0) {
    debug_mail(false,'Transfer not found',$sql);
    $return = array('success' => false, 'message' => 'Transfer not found<br>Make sure the information you entered is correct.');
    return $return; }
  
  //$out['message']='|'.count($my_recs).'| '.$sql;return $out;
  $out['hash1']=md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999));
  $out['hash3']=$my_recs[0]['transfer_reservation_guid'];
  $out['hash2']=md5($out['hash1'].$out['hash1'].$out['hash3'].$out['hash1'].$out['hash1']);
  
  $out['success']=true;
  $out['message']='OK';
  
  
  return $out;
}


function gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm($id_transfer,$row_transfer,$input_data) {
  global $db_link;  

  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm error'),'data' => false, 'debug'=>'');
  
  $guid=trim_gks($input_data['get_data']['guid']);
  $hash=trim_gks($input_data['get_data']['hash']);

  if (strlen($guid)!=32 or strlen($hash)!=32) {
    debug_mail(false,'Not all data set',print_r($input_data,true));
    $return['message']=base64_encode('Not all data set');return $return;}
  
  
  $sql="select * from gks_transfer_reservation
  where transfer_reservation_guid='".$db_link->escape_string($guid)."'
  and transfer_reservation_status in ('070wait_payment','080confirm')
  and transfer_start >= date_sub(now(), interval 12 hour)";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'Transfer not found',$sql);
    $return = array('success' => false, 'message' => base64_encode('Transfer not found<br>Make sure the information you entered is correct.'));
    return $return; }  
  
  $mytr = $result->fetch_assoc();
  
  if ($mytr['cancel_hash']!=$hash) {
      $return['message']=base64_encode('The link is not valid. Please try again.');return $return;
  }

  if (time() > strtotime($mytr['cancel_until'])) {
    $return['message']=base64_encode('The link has expire. Please try again.');return $return;
  }

  $html='Cancel Transfer #'.$mytr['transfer_booking_number'];
  
  
  //$return['message']=base64_encode($guid);return $return;

  $return['success']=true;
  $return['message']=base64_encode($html);
  
  
  return $return;
}

function gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm_exec($id_transfer,$row_transfer,$input_data) {
  global $db_link;  

  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel_confirm error'),'data' => false, 'debug'=>'');
  
  $guid=trim_gks($input_data['get_data']['my_guid']);
  $hash=trim_gks($input_data['get_data']['my_hash']);

  if (strlen($guid)!=32 or strlen($hash)!=32) {
    debug_mail(false,'Not all data set',print_r($input_data,true));
    $return['message']=base64_encode('Not all data set');return $return;}
  
  
  $sql="select * from gks_transfer_reservation
  where transfer_reservation_guid='".$db_link->escape_string($guid)."'
  and transfer_reservation_status in ('070wait_payment','080confirm')
  and transfer_start >= date_sub(now(), interval 12 hour)";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'Transfer not found',$sql);
    $return = array('success' => false, 'message' => base64_encode('Transfer not found<br>Make sure the information you entered is correct.'));
    return $return; }  
  
  $mytr = $result->fetch_assoc();
  
  
  $sql="update gks_transfer_reservation set transfer_reservation_status='040cancelled'
  where id_transfer_reservation=".$mytr['id_transfer_reservation'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
  
  
  
  $html='The Transfer #'.$mytr['transfer_booking_number'].' has cancelled';
  
  
  //$return['message']=base64_encode($guid);return $return;

  $return['success']=true;
  $return['message']=base64_encode($html);
  $return['cancel_ref']=$mytr['transfer_booking_number'];
  $return['cancel_emails']=array();
  if (trim_gks($mytr['user_email'])!='') $return['cancel_emails'][]=trim_gks($mytr['user_email']);
  
  $sql="select ruser_email from gks_transfer_reservation_oximata where ruser_email<>'' and transfer_reservation_id=".$mytr['id_transfer_reservation']." order by oximata_aa";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
  
  while ($row = $result->fetch_assoc()) {
    if (in_array(trim_gks($row['ruser_email']),$return['cancel_emails'])==false) $return['cancel_emails'][]=trim_gks($row['ruser_email']);
  }
  
  $user_id=2;
  if (count($return['cancel_emails'])>0) {
    
    $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where user_email like '".$db_link->escape_string($return['cancel_emails'][0])."'";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
      return $return; }
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $user_id=$row['ID'];
    }
  }
  
  $sxolio=gks_lang('Βήμα 2 για <span class="transfer_reservation_status_040cancelled">ακύρωση</span> από').' '.(count($return['cancel_emails'])>0 ? $return['cancel_emails'][0] : '--').'<br>'.
  gks_lang('Κατάσταση').': <span class="transfer_reservation_status_'.$mytr['transfer_reservation_status'].'">'.getTransferReservationStatusDescr($mytr['transfer_reservation_status']).'</span>'.
  ' <i class="fas fa-arrow-alt-circle-right gksvm" style=""></i> '.
  '<span class="transfer_reservation_status_040cancelled">'.getTransferReservationStatusDescr('040cancelled').'</span>';
  
  $sql="insert into gks_transfer_reservation_log (
  transfer_reservation_id,add_date,user_id,sxolio
  ) values (
  ".$mytr['id_transfer_reservation'].",
  now(),".$user_id.",'".$db_link->escape_string($sxolio)."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
      
  $sql="select id_transfer_reservation from gks_transfer_reservation where is_return_transfer_for_id=".$mytr['id_transfer_reservation'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
    return $return; }
  
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $return_id_transfer_reservation=$row['id_transfer_reservation'];
    
    $sql="update gks_transfer_reservation set transfer_reservation_status='040cancelled'
    where id_transfer_reservation=".$return_id_transfer_reservation;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('SQL Error<br>Please retry later.'));
      return $return; }

    
  }
  
  gks_plugins_functions_run('api_transfer_cancel_confirm_exec',array(
    'id'=>&$mytr['id_transfer_reservation'],
  ));
  

  return $return;
}