<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_basket_add($id_hotel,$row_hotel,$input_data) {
  global $db_link;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  //print '<pre>|'.$gks_erp_cookie_id.'|';
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  if (isset($input_data['post']['ui_lang']) and trim_gks($input_data['post']['ui_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['post']['ui_lang']);
  }
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);

  
  
  $data=$input_data['post'];
  
//  $return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_gks_session,true).'</pre>'));
//  return $return;
  
$check_in=''; if (isset($data['check_in'])) $check_in=trim_gks($data['check_in']);
$check_out=''; if (isset($data['check_out'])) $check_out=trim_gks($data['check_out']);
$adults=0; if (isset($data['adults'])) $adults=intval($data['adults']);
$childs=0; if (isset($data['childs'])) $childs=intval($data['childs']);
$calc_persons=0; if (isset($data['calc_persons'])) $calc_persons=intval($data['calc_persons']);
$rooms=0; if (isset($data['rooms'])) $rooms=intval($data['rooms']);
$calc_rooms=0; if (isset($data['calc_rooms'])) $calc_rooms=intval($data['calc_rooms']);
$num_days=0; if (isset($data['num_days'])) $num_days=intval($data['num_days']);
$selrooms=array(); if (isset($data['selrooms'])) $selrooms=$data['selrooms'];



//echo '<pre>';
//print_r($data);
//die();

if ($check_in=='') {
  debug_mail(false,'check_in',$check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί η ημερομηνία άφιξης').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($check_out=='') {
  debug_mail(false,'check_out',$check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί η ημερομηνία αναχώρησης').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($adults==0) {
  debug_mail(false,'adults',$adults);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί το πλήθος των ενηλίκων').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($rooms==0) {
  debug_mail(false,'rooms',$rooms);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί το πλήθος των δωματίων').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($calc_persons==0) {
  debug_mail(false,'calc_persons',$calc_persons);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί το πλήθος των επισκεπτών').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($calc_rooms==0) {
  debug_mail(false,'calc_rooms',$calc_rooms);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει βρεθεί το πλήθος των δωματίων').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($num_days==0) {
  debug_mail(false,'num_days',$num_days);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει βρεθεί το πλήθος των ημερών').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if (count($selrooms)==0) {
  debug_mail(false,'selrooms','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχουν ορισθεί τα επιλεγμένα δωμάτια').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}

  



foreach ($selrooms as $k => $sr) {
	$rt_id = $selrooms[$k]['roomtype']['id'];
	$sql="SELECT product_id FROM gks_hotel_room_type where id_hotel_room_type=".$rt_id;
	$result = $db_link->query($sql);
  if (!$result) {
	  debug_mail(false,'sql error', $sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  return $return;}
	if ($result->num_rows!=1) {
	  debug_mail(false,'record not found',$sql);
	  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
	  return $return;  }  
	
	$row = $result->fetch_assoc();
	$selrooms[$k]['roomtype']['product_id'] = $row['product_id'];
	
	//echo 'ssss '.$rt_id;
	//die();
	
}





$_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'][] = array(
  'guid' => guid_for_reservation(),
  'other_rsrv_time_overlap' => array(),
  'check_in' => $check_in,
  'check_out' => $check_out,
  'adults' => $adults,
  'childs' => $childs,
  'calc_persons' => $calc_persons,
  'rooms' => $rooms,
  'calc_rooms' => $calc_rooms,
  'num_days' => $num_days,
  'selrooms' => $selrooms,
);

//print '<pre>';print_r($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations']);die();

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_gks_session,true).'</pre>'));
//return $return;

//echo '<pre>';echo 'ggggggggggg'; die();

$elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
$myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, false);
$_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_gks_session,true).'</pre>'));
//return $return;


//print 'xxxxxxx<pre>';
//print_r($selrooms);
//die();


gks_basket_recalc($_gks_session['gks']['basket'], array(), array());

gks_erp_cookie_save($gks_erp_cookie_id);


//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_gks_session,true).'</pre>'));
//return $return;

$return = array('success' => true, 'message' => base64_encode('OK'));


return $return;
  
}

