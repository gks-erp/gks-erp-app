<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');

$roomid=0;
if (isset($_POST['roomid'])) $roomid=intval($_POST['roomid']);
if ($roomid<=0 and $roomid!=-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}
  
$my_page_title=gks_lang('Εύρεση λεπτομερειών δωματίου').' id: '.$roomid;
db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}




if ($_POST['check_in'] == '__/__/____ __:__') $_POST['check_in']='';
$check_in=trim_gks(stripslashes(urldecode($_POST['check_in'])));
if ($check_in!='') {
  $check_in = mystrtodb_st($check_in.':00');
}
if ($_POST['check_out'] == '__/__/____ __:__') $_POST['check_out']='';
$check_out=trim_gks(stripslashes(urldecode($_POST['check_out'])));
if ($check_out!='') {
  $check_out = mystrtodb_st($check_out.':00');
}



$id_hotel = 0; if (isset($_POST['id_hotel'])) $id_hotel = intval($_POST['id_hotel']);
$id_hotel_reservation = 0; if (isset($_POST['rsvid'])) $id_hotel_reservation = intval($_POST['rsvid']);
$rnum_adults = 0; if (isset($_POST['rnum_adults'])) $rnum_adults = intval($_POST['rnum_adults']);
$rnum_childs = 0; if (isset($_POST['rnum_childs'])) $rnum_childs = intval($_POST['rnum_childs']);
$rchilds_ages_list=array(); if (isset($_POST['rchilds_ages_list'])) $rchilds_ages_list = json_decode(base64_decode($_POST['rchilds_ages_list']),true);

$days_round=hotel_round_days($id_hotel, $check_in, $check_out);
$check_in  = $days_round['check_in_round'];
$check_out = $days_round['check_out_round'];


//print '<pre>';
//print_r($rchilds_ages_list);
//die();

$get_availability_rooms_imput=array(
  'id_hotel' => $id_hotel,
  'date_from' => $check_in,
  'date_to' => $check_out,
  'alldata' => true,
  'id_hotel_room' => $roomid,
  'id_hotel_room_type' => 0,
  'not_id_hotel_reservation' => $id_hotel_reservation,
  'not_id_hotel_folio' => 0,
  'not_id_hotel_room' => array(),
  'rnum_adults' => $rnum_adults,
  'rnum_childs' => $rnum_childs,
  'rchilds_ages_list' => $rchilds_ages_list,
  'rnum_child_kounies' =>0,
  'rnum_extra_beds' =>0,
);
$roomaf=get_availability_rooms($get_availability_rooms_imput);
if (isset($roomaf['rooms'][$roomid]) == false) {
  debug_mail(false,'roomid',$roomid);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το δωμάτιο')));
  echo json_encode($return); die();}

if (isset($roomaf['rooms'][$roomid]['room_type_visitors']) == false or $roomaf['rooms'][$roomid]['room_type_visitors'] <=0) {
  debug_mail(false,'room_type_visitors',$room_type_visitors);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πλήθος των επισκεπτών του δωματίου')));
  echo json_encode($return); die();}  
    
$room_type_visitors=intval($roomaf['rooms'][$roomid]['room_type_visitors']);
$room_type_visitors_childs=intval($roomaf['rooms'][$roomid]['room_type_visitors_childs']);
$room_type_visitors_max=intval($roomaf['rooms'][$roomid]['room_type_visitors_max']);

//print '<pre>';print_r($roomaf['rooms'][$roomid]);die();

$room_type_child_kounies = intval($roomaf['rooms'][$roomid]['room_type_child_kounies']);
$room_type_extra_beds = intval($roomaf['rooms'][$roomid]['room_type_extra_beds']);


$msg_price_out='';
$msg_aval_not_out='';
$price_array = array();
$ajia_total_out = 0;
$roomaf_array=array();
$roomaf_html='';
//$roomaf_index=0;
if (isset($roomaf['rooms'][$roomid]))  {
  foreach ($roomaf['rooms'][$roomid]['days'] as $myday => $day) {
    $roomaf_array[]=$day;
    
    if ($day['val1']==0) {
      $msg_aval_not_out.=date('d/m', strtotime($myday)).'<br>';
    }
    if (isset($price_array[$day['price']]) == false) {
      $price_array[$day['price']] = array();
    }
    $price_array[$day['price']][] = $myday;
    $ajia_total_out+= $day['price'];
    
    //$roomaf_index++;
    //$roomaf_html.='<tr><th scope="row" nowrap style="text-align:center;">'.$roomaf_index.'</th><td nowrap align="right">'.myDateFormatw(strtotime($myday)).'</td><td nowrap align="right">'.myCurrencyFormat($day['price']).'</td></tr>';
  }
  
  $msg_price_out=$roomaf['rooms'][$roomid]['room_ajia_table']['msg_price'];
  $roomaf_html = $roomaf['rooms'][$roomid]['room_ajia_table']['roomaf_html'];
  $roomaf_array = $roomaf['rooms'][$roomid]['room_ajia_table']['roomaf_array'];
}




$return = array(
  'success' => true, 
  'message' => base64_encode('OK'),
  'room_id' => intval($roomaf['rooms'][$roomid]['id_hotel_room']),
  'room_descr' => base64_encode($roomaf['rooms'][$roomid]['room_descr']),
  'room_type_descr' => base64_encode($roomaf['rooms'][$roomid]['room_type_descr']),
  'visitors' => intval($room_type_visitors),
  'visitors_childs' => intval($room_type_visitors_childs),
  'visitors_max' => intval($room_type_visitors_max),
  'room_type_child_kounies' => intval($room_type_child_kounies),
  'room_type_extra_beds' => intval($room_type_extra_beds),
  
  
  'ajia_total_val' => round($ajia_total_out,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
  'ajia_total' => base64_encode(myCurrencyFormat($ajia_total_out,false)),
  'msg_price'=> base64_encode($msg_price_out),
  'msg_aval'=> base64_encode($msg_aval_not_out),
  'roomaf_array' => $roomaf_array,
  'roomaf_html' => $roomaf_html,
);
echo json_encode($return); die();



$return = array('success' => false, 'message' => base64_encode(
  $roomid.'<br>'.$check_in.'<br>'.$check_out.'<br>'.$id_hotel_reservation.'<br>'.
  $ajia_total_out.'<br>'.$msg_price_out.'<br>'.$msg_aval_not_out)
);
echo json_encode($return); die();
