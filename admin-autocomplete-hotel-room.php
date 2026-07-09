<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// admin-autocomplete-hotel-room.php

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση δωματίου');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$id_hotel = 0; if (isset($_GET['id_hotel'])) $id_hotel = intval($_GET['id_hotel']);
$id_hotel_reservation = 0; if (isset($_GET['rsvid'])) $id_hotel_reservation = intval($_GET['rsvid']);
$showtype=0; if (isset($_GET['showtype']) and $_GET['showtype']=='1') $showtype=1;
$showfloor=0; if (isset($_GET['showfloor']) and $_GET['showfloor']=='1') $showfloor=1;
$only_all=false; if (isset($_GET['all']) and $_GET['all']=='1') $only_all=true;
$only_available=false; if (isset($_GET['available']) and $_GET['available']=='1') $only_available=true;
$only_free=false; if (isset($_GET['free']) and $_GET['free']=='1') $only_free=true;


if (isset($_GET['check_in'])) {
  if ($_GET['check_in'] == '__/__/____ __:__') $_GET['check_in']='';
  $check_in=trim_gks(stripslashes(urldecode($_GET['check_in'])));
  if ($check_in!='') {
    $check_in = mystrtodb_st($check_in.':00');
  }
  if ($_GET['check_out'] == '__/__/____ __:__') $_GET['check_out']='';
  $check_out=trim_gks(stripslashes(urldecode($_GET['check_out'])));
  if ($check_out!='') {
    $check_out = mystrtodb_st($check_out.':00');
  }
  
  $days_round=hotel_round_days($id_hotel, $check_in, $check_out);
  $check_in  = $days_round['check_in_round'];
  $check_out = $days_round['check_out_round'];

}
//echo '<pre>'.$id_hotel;die();

$mynotin='';if (isset($_GET['mynotin'])) $mynotin = trim_gks($_GET['mynotin']);
$mynotin_array=array();
if ($mynotin!='') {
  $var = explode('|',$mynotin);
  foreach ($var as $value) {
    $myint= intval($value);
    if ($myint>0) $mynotin_array[] = $value;
  } 
}
//echo $notin;
//die();
  


//echo '<pre>';echo print_r($_GET,true);die();



$sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.room_descr, gks_hotel_room_type.room_type_descr, gks_hotel_floor.floor_descr
FROM (gks_hotel_room 
LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor
where 1=1 " ;
if ($id_hotel>0) $sql.=" and gks_hotel_room.hotel_id=".$id_hotel;

if (count($mynotin_array)>0) {
  $sql.=" and gks_hotel_room.id_hotel_room not in (".implode(',',$mynotin_array).") ";
  
}

if ($only_all) {
  //$sql.=" 1=1 and ";
} else if ($only_available) {
  $sql.=" and gks_hotel_room.room_status='available' and gks_hotel_room_type.room_type_status='available' ";
} else if ($only_free) {
  //$sql.=" 1=1 and ";
} else {
  //$sql.=" 1=1 and ";
}

$sql.=" and (
gks_hotel_room.room_descr like '%".$db_link->escape_string($term)."%' or 
gks_hotel_room_type.room_type_descr like '%".$db_link->escape_string($term)."%' or 
gks_hotel_floor.floor_descr like '%".$db_link->escape_string($term)."%' 
)
ORDER BY gks_hotel_room.room_sortorder, gks_hotel_room.room_descr
limit 1000";

//echo '<pre>';echo $sql;die();

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
//
//print '<pre>';
//print base64_decode($_GET['rchilds_ages_list']);
//die();

//$rchilds_ages_list=array(); if (isset($_GET['rchilds_ages_list'])) $rchilds_ages_list = json_decode(base64_decode($_GET['rchilds_ages_list']),true);
//print '<pre>';
//print_r($rchilds_ages_list);
//die();

if ($only_free) {
  //echo '<pre>only_free';die();
  $get_availability_rooms_imput=array(
    'id_hotel' => $id_hotel,
    'date_from' => $check_in,
    'date_to' => $check_out,
    'alldata' => false,
    'id_hotel_room' => 0,
    'id_hotel_room_type' => 0,
    'not_id_hotel_reservation' => 0,
    'not_id_hotel_folio' => 0,
    'not_id_hotel_room' => array(),
    'rnum_adults' => 0,
    'rnum_childs' => 0,
    'rchilds_ages_list' => array(),
    'rnum_child_kounies' =>0,
    'rnum_extra_beds' =>0,
  );
  //print '<pre>';print_r($get_availability_rooms_imput);die();
  
  $rooms_array = get_availability_rooms($get_availability_rooms_imput);
  //print '<pre>';print_r($rooms_array);die();
}
$fount_count=0;
$out=array();


//$out[] = array('id' => -1, 'value' => $check_in);
//$out[] = array('id' => -2, 'value' => $check_out);
//echo '<pre>';print_r($rooms_array);die();

while ($row = $result->fetch_assoc()) {
  $addthis=true;
  if ($only_free) {
    if (isset($rooms_array['rooms'][$row['id_hotel_room']]['is_avl_state_folio']) and $rooms_array['rooms'][$row['id_hotel_room']]['is_avl_state_folio'] == true) {
      
    } else {
      $addthis=false;
    }
  }
  
  if ($addthis) {
    $fount_count++;
    $value=$row['room_descr'];
  
    if ($showtype==1 and isset($row['room_type_descr'])) $value.=' / '.$row['room_type_descr'];
    if ($showfloor==1 and isset($row['floor_descr'])) $value.=' / '.$row['floor_descr'];
    
    $out[] = array('id' => $row['id_hotel_room'], 'value' => $value,'room_type_descr'=>$row['room_type_descr']);
  }
}

//echo '<pre>';print_r($out);die();

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



