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

$return = array('success' => false, 'message' => base64_encode('generic api hotel error'),'data' => false);

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
  $return['message']=base64_encode('json decode error');debug_mail(false,'json decode Error','');echo json_encode($return); die();
}


//debug_mail(false,'api.hotel.v1.php','');
if (isset($input_data['cmd'])==false or trim_gks($input_data['cmd'])=='')         {$return['message']=base64_encode('cmd is not set');debug_mail(false,'cmd is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['rnd'])==false or trim_gks($input_data['rnd'])=='')         {$return['message']=base64_encode('rnd is not set');debug_mail(false,'rnd is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['token'])==false or trim_gks($input_data['token'])=='')     {$return['message']=base64_encode('token is not set');debug_mail(false,'token is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}
if (isset($input_data['user_ip'])==false or trim_gks($input_data['user_ip'])=='') {$return['message']=base64_encode('user_ip is not set');debug_mail(false,'user_ip is not set',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}


$sql="select * from gks_hotel where hotel_disable=0 and hotel_website_key<>''";
$result = $db_link->query($sql);
if (!$result) {$return['message']=base64_encode('sql error');debug_mail(false,'sql error',$sql); echo json_encode($return); die();}

$id_hotel=0;$row_hotel=array();
while ($row = $result->fetch_assoc()) {
  $token=md5($input_data['rnd'].$row['hotel_website_key'].$input_data['rnd']);
  if ($token == $input_data['token']) {
    $id_hotel=intval($row['id_hotel']);
    $row_hotel=$row;
    break;
  }
}  
if ($id_hotel<=0) {$return['message']=base64_encode('hotel not found');debug_mail(false,'hotel not found',$HTTP_RAW_POST_DATA); echo json_encode($return); die();}

//echo '<pre>'.$input_data['cmd'];die();
//echo '<pre>'; print_r($input_data);die();

switch ($input_data['cmd']) {   
  
  case 'hotel_reservation':
    include_once('api.hotel.v1.cmd_hotel_reservation.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation($id_hotel,$row_hotel,$input_data); 
    break;  
  case 'hotel_reservation_search';
    include_once('api.hotel.v1.cmd_hotel_reservation_search.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation_search($id_hotel,$row_hotel,$input_data); 
    break;  
  case 'hotel_reservation_search_calc';
    include_once('api.hotel.v1.cmd_hotel_reservation_search_calc.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation_search_calc($id_hotel,$row_hotel,$input_data); 
    break;  
 case 'hotel_reservation_basket_add':
    include_once('api.hotel.v1.cmd_hotel_reservation_basket_add.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation_basket_add($id_hotel,$row_hotel,$input_data); 
    break;  
  case 'hotel_reservation_basket':
    include_once('api.hotel.v1.cmd_hotel_reservation_basket.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation_basket($id_hotel,$row_hotel,$input_data); 
    break;  
 case 'basket_edit':
    include_once('api.v1.cmd_basket_edit.php');
    $return['data'] = gks_api_cmd_basket_edit($id_hotel,$row_hotel,$input_data); 
    break;
 case 'checkout':
    include_once('api.v1.cmd_checkout.php');
    $return['data'] = gks_api_cmd_checkout($id_hotel,$row_hotel,$input_data); 
    break;
 case 'checkout_edit':
    include_once('api.v1.cmd_checkout_edit.php');
    $return['data'] = gks_api_cmd_checkout_edit($id_hotel,$row_hotel,$input_data); 
    break;
 case 'get_nomoi':
    $return['data'] = gks_api_cmd_get_nomoi($input_data); 
    break;
 case 'payment':
    include_once('api.v1.cmd_payment.php');
    $return['data'] = gks_api_cmd_payment($id_hotel,$row_hotel,$input_data); 
    break;
 case 'payment_edit':
    include_once('api.v1.cmd_payment_edit.php');
    $return['data'] = gks_api_cmd_payment_edit($id_hotel,$row_hotel,$input_data); 
    break;
 case 'confirm':
    include_once('api.v1.cmd_confirm.php');
    $return['data'] = gks_api_cmd_confirm($id_hotel,$row_hotel,$input_data); 
    break;
  case 'woo_add_to_basket':
    include_once('api.v1.cmd_woo_add_to_basket.php');
    $return['data'] = gks_api_cmd_woo_add_to_basket($id_hotel,$row_hotel,$input_data); 
    break;
  
  case 'hotel_reservation_empty_basket':
    include_once('api.hotel.v1.cmd_hotel_reservation_empty_basket.php');
    $return['data'] = gks_api_hotel_cmd_hotel_reservation_empty_basket($input_data); 
    break;
  
  
  
  
  case 'hotel_reservation_my_booking':
    $return['data'] = hotel_reservation_my_booking($input_data); 
    break;
  case 'hotel_reservation_my_booking_item':
    include_once('api.hotel.v1.cmd_hotel_reservation_my_booking_item.php');
    $return['data']=gks_api_hotel_cmd_hotel_reservation_my_booking_item($id_hotel,$row_hotel,$input_data);
    break;

  case 'hotel_reservation_my_booking_item_cancel':
    include_once('api.hotel.v1.cmd_hotel_reservation_my_booking_item_cancel.php');
    $return['data']=gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel($id_hotel,$row_hotel,$input_data);
    break;

  case 'hotel_reservation_my_booking_item_cancel_confirm': 
    $return['data']=gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm($id_hotel,$row_hotel,$input_data);
    break;
  case 'hotel_reservation_my_booking_item_cancel_confirm_exec': 
    $return['data']=gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm_exec($id_hotel,$row_hotel,$input_data);
    break;

  
  default:
    $return['message']=base64_encode('cmd not found: '.$input_data['cmd']);debug_mail(false,'cmd not found',$input_data['cmd']); echo json_encode($return); die();
}

$return['message']='OK';
$return['success']=true;
echo json_encode($return); die();


$return['message']=base64_encode('<pre>'.$id_hotel."\r\n".print_r($input_data,true)."\r\n".print_r($row_hotel,true).'</pre>'); echo json_encode($return); die();




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

function hotel_reservation_my_booking($input_data) {
  global $db_link;  
  global $_gks_session;
  
  $my_prefix=trim_gks($input_data['get_data']['my_prefix']);
  $my_number=trim_gks($input_data['get_data']['my_number']);
  $my_email =trim_gks($input_data['get_data']['my_email']);
  $my_lang =trim_gks($input_data['get_data']['my_lang']);
  
  
  $out=array(
    'success'=>false,
    'message' => 'generic error at my booking func',
    'my_prefix' => $my_prefix,
    'my_number' => $my_number,
    'my_email' => $my_email,
  );
  
 
  if ($my_lang=='')   {$out['message']= 'lang is not set'; return $out;}

  $_gks_session['gks']['ui_lang']=$my_lang;
  gks_load_lang();

  if ($my_prefix=='') {$out['message']= gks_lang('Δεν έχει οριστεί το πρόθεμα της κράτησης'); return $out;}
  if ($my_number=='') {$out['message']= gks_lang('Δεν έχει οριστεί ο κωδικός κράτησης'); return $out;}
  if ($my_email=='')  {$out['message']= gks_lang('Δεν έχει οριστεί το email κράτησης'); return $out;}
  
  
  
  $hotel_booking_number=$my_prefix.$my_number;
  $sql_templete="select gks_hotel_reservation.*, ".GKS_WP_TABLE_PREFIX."users.user_email,display_name,gks_nickname
  FROM gks_hotel_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where hotel_booking_number like '[[hotel_booking_number]]'
  and reservation_status in ('040cancelled','050rejected','070wait_payment','080confirm')
  and check_in >= date_sub(now(), interval 12 hour)
  and (
    gks_hotel_reservation.user_email like '".$db_link->escape_string($my_email)."' or 
    gks_hotel_reservation.other_email like '".$db_link->escape_string($my_email)."' or
    ".GKS_WP_TABLE_PREFIX."users.user_email like '".$db_link->escape_string($my_email)."'
  )
  order by id_hotel_reservation desc limit 1";
  
  $my_recs=array();

  $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number),$sql_templete);
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $my_recs[]=$row;
  }
  
  if (count($my_recs)==0) {
    $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number.'-1'),$sql_templete);
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      return $return; }
    while ($row = $result->fetch_assoc()) {  
      $my_recs[]=$row;
    }
  }
//  if (count($my_recs)==0) {
//    $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number.'-2'),$sql_templete);
//    $result = $db_link->query($sql);
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
//      return $return; }
//    while ($row = $result->fetch_assoc()) {  
//      $my_recs[]=$row;
//    }
//  }
  
  if (count($my_recs)==0) {
    debug_mail(false,'Booking not found',$sql);
    $return = array('success' => false, 'message' => gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Βεβαιωθείτε ότι οι πληροφορίες που εισαγάγατε είναι σωστές'));
    return $return; }
  
  //$out['message']='|'.count($my_recs).'| '.$sql;return $out;
  $out['hash1']=md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999));
  $out['hash3']=$my_recs[0]['reservation_guid'];
  $out['hash2']=md5($out['hash1'].$out['hash1'].$out['hash3'].$out['hash1'].$out['hash1']);
  
  $out['success']=true;
  $out['message']='OK';
  
  
  return $out;
}


function gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm($id_hotel,$row_hotel,$input_data) {
  global $db_link;  
  global $gkIP;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;



  $from='website';if (isset($input_data['from'])) $from=$input_data['from'];
  
  //return '<pre>ssssssss'.print_r($input_data,true).'</pre>';
  
  $gks_erp_cookie_id='';
  if ($from=='website') {
    if(isset($input_data['gks_erp_cookie_id'])) {
      $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
    }
    $hotel_title=$row_hotel['hotel_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
    //return '<pre>dddddddddd '.print_r($_gks_session,true).'</pre>';
    //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσσσσσσ '.print_r($_gks_session,true)),'data' => false, 'debug'=>'');
    //return $return;
  }
  

  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  gks_load_lang();
  

  $return=array('success' => false, 'message' => base64_encode('generic gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm error'),'data' => false, 'debug'=>'');

  //$return=array('success' => false, 'message' => base64_encode('<pre>222222 '.$_gks_session['gks']['ui_lang'].'</pre>'.gks_lang('5 λεπτά')),'data' => false, 'debug'=>'');
  //return $return;

  
  $guid=trim_gks($input_data['get_data']['guid']);
  $hash=trim_gks($input_data['get_data']['hash']);


  if (strlen($guid)!=32 or strlen($hash)!=32) {
    debug_mail(false,'Not all data set',print_r($input_data,true));
    $return['message']=base64_encode(gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα'));return $return;}
   
  
  $sql="select * from gks_hotel_reservation
  where reservation_guid='".$db_link->escape_string($guid)."'
  and reservation_status in ('070wait_payment','080confirm')
  and check_in >= date_sub(now(), interval 12 hour)";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'Booking not found',$sql);
    $return = array('success' => false, 'message' => base64_encode('Booking not found<br>Make sure the information you entered is correct.'));
    return $return; }  
  
  $mytr = $result->fetch_assoc();
  
  if ($mytr['cancel_hash']!=$hash) {
      $return['message']=base64_encode(gks_lang('Ο σύνδεσμος δεν είναι έγκυρος').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;
  }

  if (time() > strtotime($mytr['cancel_until'])) {
    $return['message']=base64_encode(gks_lang('Ο σύνδεσμος έχει λήξει').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;
  }

  
  
  
  //$return['message']=base64_encode($guid);return $return;


  $html='<div class="gks_hotel_title">'.
  str_replace('%s',$mytr['hotel_booking_number'], gks_lang('Ακύρωση κράτησης #%s')).
  '</div>';
  
  $html.='<div class="gks_hotel_text">'.gks_lang('Είστε σίγουροι ;').'</div>';
  $html.='<div class="gks_hotel_button">';
  
  $html.='<a class="fusion-button button-flat fusion-button-default-size button-default fusion-button-default fusion-button-default-span fusion-button-default-type" '.
  'id="gks_hotel_booking_button_button"><span class="fusion-button-text">'.
  gks_lang('Ναι, να ακυρωθεί').
  '</span></a>';
  $html.='</div>';
  $html.='<input type="hidden" value="'.$guid.'" id="gks_hotel_booking_button_guid">';
  $html.='<input type="hidden" value="'.$hash.'" id="gks_hotel_booking_button_hash">';
  $html.='<input type="hidden" value="'.$_gks_session['gks']['ui_lang'].'" id="gks_hotel_booking_button_lang">';
  $html.='<input type="hidden" value="'.$_gks_session['gks']['ui_lang'].'" id="gks_hotel_booking_button_url_lang">';
  
  $html.="<script type='text/javascript' src='/wp-content/plugins/gks_hotel/js/my-booking-item_hash.js?ver=[[plugin_cache_version]]'></script>";


  $return['success']=true;
  $return['message']=base64_encode($html);
  
  
  return $return;
}

function gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm_exec($id_hotel,$row_hotel,$input_data) {
  global $db_link;  
  global $gkIP;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;

  $from='website';if (isset($input_data['from'])) $from=$input_data['from'];
  
  //return '<pre>ssssssss'.print_r($input_data,true).'</pre>';
  
  $gks_erp_cookie_id='';
  if ($from=='website') {
    if(isset($input_data['gks_erp_cookie_id'])) {
      $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
    }
    $hotel_title=$row_hotel['hotel_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
    //return '<pre>dddddddddd '.print_r($_gks_session,true).'</pre>';
    //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσσσσσσ '.print_r($_gks_session,true)),'data' => false, 'debug'=>'');
    //return $return;
  }
  
  if (isset($input_data['get_data']['my_lang']) and trim_gks($input_data['get_data']['my_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['get_data']['my_lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}


  gks_load_lang();
  
  //$return=array('success' => false, 'message' => base64_encode('<pre>222222 '.$_gks_session['gks']['ui_lang'].'</pre>'.gks_lang('5 λεπτά')),'data' => false, 'debug'=>'');
  //return $return;


  $return=array('success' => false, 'message' => base64_encode('generic gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel_confirm error'),'data' => false, 'debug'=>'');
  
  $guid=trim_gks($input_data['get_data']['my_guid']);
  $hash=trim_gks($input_data['get_data']['my_hash']);

  if (strlen($guid)!=32 or strlen($hash)!=32) {
    debug_mail(false,'Not all data set',print_r($input_data,true));
    $return['message']=base64_encode(gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα'));return $return;}
  
  
  $sql="select * from gks_hotel_reservation
  where reservation_guid='".$db_link->escape_string($guid)."'
  and reservation_status in ('070wait_payment','080confirm')
  and check_in >= date_sub(now(), interval 12 hour)";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'Booking not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Βεβαιωθείτε ότι οι πληροφορίες που εισαγάγατε είναι σωστές')));
    return $return; }  
  
  $mytr = $result->fetch_assoc();
  
  
  $sql="update gks_hotel_reservation set reservation_status='040cancelled'
  where id_hotel_reservation=".$mytr['id_hotel_reservation'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  
  
  

  $html=str_replace('%s',$mytr['hotel_booking_number'], gks_lang('Η κράτηση #%s έχει ακυρωθεί'));
  
  
  //$return['message']=base64_encode($guid);return $return;

  $return['success']=true;
  $return['message']=base64_encode($html);
  
   
  
  
  $return['cancel_ref']=$mytr['hotel_booking_number'];
  $return['cancel_emails']=array();
  if (trim_gks($mytr['user_email'])!='') $return['cancel_emails'][]=trim_gks($mytr['user_email']);
  
  $sql="select ruser_email from gks_hotel_reservation_room where ruser_email<>'' and hotel_reservation_id=".$mytr['id_hotel_reservation']." order by id_hotel_reservation_room";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
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
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $user_id=$row['ID'];
    }
  }
  
  $cancel_text= gks_lang('Έχει αποσταλεί email σε').' '.implode(' , ',$return['cancel_emails']);
  $return['cancel_text']=base64_encode($cancel_text);
  $return['mail_text']=base64_encode(gks_lang('Εάν αυτό είναι λάθος, επικοινωνήστε μαζί μας αμέσως !!!'));
  
  $sxolio=gks_lang('Βήμα 2 για <span class="reservation_status_040cancelled">ακύρωση</span> από').' '.(count($return['cancel_emails'])>0 ? $return['cancel_emails'][0] : '--').'<br>'.
  gks_lang('Κατάσταση').': <span class="reservation_status_'.$mytr['reservation_status'].'">'.getHotelReservationStatusDescr($mytr['reservation_status']).'</span>'.
  ' <i class="fas fa-arrow-alt-circle-right gksvm" style=""></i> '.
  '<span class="reservation_status_040cancelled">'.getHotelReservationStatusDescr('040cancelled').'</span>';
  
  $sql="insert into gks_hotel_reservation_log (
  hotel_reservation_id,add_date,user_id,sxolio
  ) values (
  ".$mytr['id_hotel_reservation'].",
  now(),".$user_id.",'".$db_link->escape_string($sxolio)."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
      
  
  $message=str_replace('[1]',$mytr['id_hotel_reservation'],gks_lang('Ακύρωση κράτησης με αριθμό <a href="/my/admin-hotel-reservation-item.php?id=[1]">#[1]</a>'));

  $sql="insert into gks_notification (
  message,for_user_id,`date_add`,for_date,has_ok,model,model_id
  )
  select
  '".$db_link->escape_string($message)."' as message,
  user_id as for_user_id,
  now() as `date_add`,
  now() as `for_date`,
  0 as has_ok,'reservation' as model,
  ".$mytr['id_hotel_reservation']." as model_id
  from gks_notification_userperm where notification_type_id=1010
  and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
      

  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
  FROM gks_notification_userperm 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
  AND gks_notification_userperm.notification_type_id=1010
  AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
  //debug_mail(false,'sql',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; 
  } else {
    $mysubject=str_replace('[1]',$mytr['id_hotel_reservation'],gks_lang('Ακύρωση κράτησης με αριθμό [1]'));
    $model_name='hotel-reservation';

    $replaces=array();
    $replaces[] = array('[[message]]', $message);
    
    $send_viber=array();
    while ($row = $result->fetch_assoc()) {
      $params=array(
        'model'=>$model_name,
        'model_id'=>$mytr['id_hotel_reservation'],
        'to'=>$row['user_email'],
        'subject'=>$mysubject,
        'template'=>3, //'empty.html',
        'replaces'=>$replaces,
      );
          
      $send_email_res = gks_mymail_template($params);
      
    }
  }

  $message=gks_lang('Ακύρωση κράτησης με αριθμό [1] [2]my/admin-hotel-reservation-item.php?id=[1]');
  $message=str_replace('[1]',$mytr['id_hotel_reservation'],$message);
  $message=str_replace('[2]',GKS_SITE_URL,$message);  
  
        
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
  FROM gks_notification_userperm 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
  AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
  AND gks_notification_userperm.notification_type_id=1010
  AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
  //debug_mail(false,'sql',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; 
  } else {
    $send_viber=array();
    while ($row = $result->fetch_assoc()) {
      $send_viber[]=$row['viber_id'];
    }
    foreach ($send_viber as $value) {
      gks_viber_send('hotel_reservation' ,$mytr['id_hotel_reservation'] ,$value,$message);
    } 
  }
          
  
  return $return;
}
