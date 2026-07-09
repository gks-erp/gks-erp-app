<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Διαθεσιμότητα και Τιμές Data');



db_open();
stat_record();
$perm_a_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_availability','view',0);
$perm_p_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_price','view',0);
if ($perm_a_ret['success']==false and $perm_p_ret['success']==false) {
  if ($perm_a_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_a_ret['message']));echo json_encode($return); die();}
  if ($perm_p_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_p_ret['message']));echo json_encode($return); die();}
}

$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$user_hotels=gks_get_hotels_list();
$hotel_ids = array_keys($user_hotels);

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_POST,true)));echo json_encode($return); die();
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($hotel_ids,true)));echo json_encode($return); die();
//print '<pre>';print_r($_POST);die();


$hotel_id=0; if (isset($_POST['hotel_id'])) $hotel_id = intval($_POST['hotel_id']);
$room_id=0; if (isset($_POST['room_id'])) $room_id = intval($_POST['room_id']);
$room_type_id=0; if (isset($_POST['room_type_id'])) $room_type_id = intval($_POST['room_type_id']);

if (in_array($hotel_id,$hotel_ids)==false) {
  $return = array('success' => true, 'message' => base64_encode('hotel_id is zero'));
  echo json_encode($return); die();}
  
if ($room_id == 0 and $room_type_id == 0) {
  $return = array('success' => true, 'message' => base64_encode('room_id and room_type_id is zero'));
  echo json_encode($return); die();}
$date_from = '';
if (isset($_POST['date_from'])) $date_from = trim_gks(stripslashes(urldecode($_POST['date_from'])));
$v1=explode('-',$date_from);
$date_from='';
$date_from_time=0;
if (count($v1)==3) {
  $date_from_time=strtotime($v1[0].'-'.$v1[1].'-'.$v1[2]);
  $date_from=date('Y-m-d',$date_from_time);
}



$date_to = '';
if (isset($_POST['date_to'])) $date_to = trim_gks(stripslashes(urldecode($_POST['date_to'])));
$v1=explode('-',$date_to);
$date_to='';
$date_to_time=0;
if (count($v1)==3) {
  $date_to_time=strtotime($v1[0].'-'.$v1[1].'-'.$v1[2]);
  $date_to=date('Y-m-d',$date_to_time);
}

if ($date_from == '' or $date_to == '') {
  $return = array('success' => true, 'message' => base64_encode('empty dates'));
  echo json_encode($return); die();
} 


$hotel_params=gks_hotel_get_params($hotel_id);

$room_status='';
if ($room_id > 0) {
  $sql="select hotel_room_type_id,room_status from gks_hotel_room where id_hotel_room=".$room_id;
  if (count($perm_id_hotel_ids)>0) $sql.=' and hotel_id in ('.implode(',',$perm_id_hotel_ids).')';
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows>=1) {  
    $row = $result->fetch_assoc();
    $room_type_id = $row['hotel_room_type_id'];
    $room_status = $row['room_status'];
  }
}

$price_def = myCurrencyFormat(floatval($hotel_params['hotel_default_price']));
$fromp_def= gks_lang('Προεπιλεγμένη τιμή ξενοδοχείου');

$room_type_status='';
$sql="select room_type_status,room_type_price from gks_hotel_room_type where id_hotel_room_type=".$room_type_id;
 if (count($perm_id_hotel_ids)>0) $sql.=' and hotel_id in ('.implode(',',$perm_id_hotel_ids).')';


$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $room_type_status = $row['room_type_status'];
  if ($row['room_type_price']>0) {
    $price_def =  myCurrencyFormat($row['room_type_price']);
    $fromp_def= gks_lang('Προεπιλεγμένη τιμή από τον τύπου δωματίου');
  }
}




$gks_hotel_date_open_time=0;
$gks_hotel_date_close_time=0;
if (trim_gks($hotel_params['hotel_date_open']) !='') $gks_hotel_date_open_time = strtotime(trim_gks($hotel_params['hotel_date_open']));
if (trim_gks($hotel_params['hotel_date_close'])!='') $gks_hotel_date_close_time= strtotime(trim_gks($hotel_params['hotel_date_close']));


$outdata=array();
for ($i=$date_from_time; $i<=$date_to_time; $i+=24*60*60) {
  $dateval=date('Y-m-d', $i);
  $outdata[$dateval] = array( 'froma'=> gks_lang('Διαθέσιμο'), 'val1' => 1,'fromp'=> $fromp_def, 'price' => $price_def); 
  if (intval($hotel_params['hotel_default_availability'])==0) {$outdata[$dateval]['val1']=0;$outdata[$dateval]['froma']=gks_lang('Το ξενοδοχείο είναι κλειστό');}

  if ($outdata[$dateval]['val1'] == 1) {
    if ($gks_hotel_date_open_time > 0  and $i < $gks_hotel_date_open_time)  {$outdata[$dateval]['val1'] = 0; $outdata[$dateval]['froma']=gks_lang('Ημερομηνίες λειτουργίας του ξενοδοχείου');}
    if ($gks_hotel_date_close_time > 0 and $i > $gks_hotel_date_close_time) {$outdata[$dateval]['val1'] = 0; $outdata[$dateval]['froma']=gks_lang('Ημερομηνίες λειτουργίας του ξενοδοχείου');}
  }
  if ($outdata[$dateval]['val1'] == 1) {
    if ($room_type_status  != 'available') {$outdata[$dateval]['val1']=0; $outdata[$dateval]['froma']=gks_lang('Ο τύπος δωματίου δεν είναι "Διαθέσιμο"');}
  }
  if ($room_id>0 and $outdata[$dateval]['val1'] == 1) {
    if ($room_status != 'available') {$outdata[$dateval]['val1']=0; $outdata[$dateval]['froma']=gks_lang('Το δωμάτιο είναι ανενεργό');}
  }
}




if ($room_type_status == 'available') {
  $sql="SELECT gks_hotel_availability_day.availability_day, gks_hotel_availability_day.availability_status, gks_hotel_availability.availability_descr
  FROM gks_hotel_availability_day LEFT JOIN gks_hotel_availability ON gks_hotel_availability_day.hotel_availability_id = gks_hotel_availability.id_hotel_availability
  where gks_hotel_availability_day.hotel_room_type_id=".$room_type_id." 
  and gks_hotel_availability_day.availability_day>='". $db_link->escape_string($date_from)."'
  and gks_hotel_availability_day.availability_day<='". $db_link->escape_string($date_to)."'
  order by gks_hotel_availability_day.availability_day";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    $dateval=strtotime($row['availability_day']);
    $dateval=date('Y-m-d', $dateval);
    if ($outdata[$dateval]['val1'] == 1) {
      $outdata[$dateval]['froma'] = gks_lang('Διαθεσιμότητα από τον τύπο δωματίου');
      $outdata[$dateval]['val1'] = $row['availability_status'];
      if (isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') {
        $outdata[$dateval]['descra']=trim_gks($row['availability_descr']);
      }
    }
  }
}

if ($room_status == 'available' && $room_id>0) {
  $sql="SELECT gks_hotel_availability_day.availability_day, gks_hotel_availability_day.availability_status, gks_hotel_availability.availability_descr
  FROM gks_hotel_availability_day LEFT JOIN gks_hotel_availability ON gks_hotel_availability_day.hotel_availability_id = gks_hotel_availability.id_hotel_availability
  where gks_hotel_availability_day.hotel_room_id=".$room_id." 
  and gks_hotel_availability_day.availability_day>='". $db_link->escape_string($date_from)."'
  and gks_hotel_availability_day.availability_day<='". $db_link->escape_string($date_to)."'
  order by gks_hotel_availability_day.availability_day";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) { 
    $dateval=strtotime($row['availability_day']);
    $dateval=date('Y-m-d', $dateval);
    if ($outdata[$dateval]['val1'] == 1) {
      $outdata[$dateval]['froma'] = gks_lang('Διαθεσιμότητα από το δωμάτιο');
      $outdata[$dateval]['val1'] = intval($row['availability_status']);
      if (isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') {
        $outdata[$dateval]['descr']=trim_gks($row['availability_descr']);
      }
    }
  }
}







//price
$sql="SELECT gks_hotel_price_day.price_day, gks_hotel_price_day.price, gks_hotel_price.price_descr
FROM gks_hotel_price_day LEFT JOIN gks_hotel_price ON gks_hotel_price_day.hotel_price_id = gks_hotel_price.id_hotel_price
where gks_hotel_price_day.hotel_room_type_id=".$room_type_id." 
and gks_hotel_price_day.price_day>='". $db_link->escape_string($date_from)."'
and gks_hotel_price_day.price_day<='". $db_link->escape_string($date_to)."'
order by gks_hotel_price_day.price_day";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

while ($row = $result->fetch_assoc()) { 
  $dateval=strtotime($row['price_day']);
  $dateval=date('Y-m-d', $dateval);
  $outdata[$dateval]['fromp'] = gks_lang('Τιμή από τον τύπου δωματίου');
  $outdata[$dateval]['price'] = myCurrencyFormat($row['price']); 
  if (isset($row['price_descr']) and trim_gks($row['price_descr'])!='') {
    $outdata[$dateval]['descrp']=trim_gks($row['price_descr']);
  }
}




//$return = array('success' => false, 'message' => base64_encode($room_id.'|'.$room_type_id.'|'.$date_from.'|'.$date_to));
//echo json_encode($return); die();
//file_put_contents('/var/www/php/my-rooms.gks.gr/tmp/data1.txt',print_r($outdata,true));

$return = array('success' => true, 'message' => base64_encode('OK'), 'outdata' =>  $outdata);
echo json_encode($return); die();
