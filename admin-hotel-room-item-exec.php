<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Δωματίου').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');




if ($id>0) {
  $sql ="SELECT * FROM gks_hotel_room where id_hotel_room = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room.hotel_id in (".implode(',',$perm_id_hotel_ids).")";

  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}




$hotel_id=''; if (isset($_POST['hotel_id'])) $hotel_id=intval($_POST['hotel_id']);
$room_descr=''; if (isset($_POST['room_descr'])) $room_descr=trim_gks(stripslashes(urldecode($_POST['room_descr'])));
$room_status=''; if (isset($_POST['room_status'])) $room_status=trim_gks(stripslashes(urldecode($_POST['room_status'])));
$hotel_room_type_id=0; if (isset($_POST['hotel_room_type_id'])) $hotel_room_type_id=intval($_POST['hotel_room_type_id']);
$hotel_floor_id=0; if (isset($_POST['hotel_floor_id'])) $hotel_floor_id=intval($_POST['hotel_floor_id']);


if ($hotel_id<=0) {debug_mail(false,'hotel_id',$hotel_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το ξενοδοχείο')));
  echo json_encode($return); die(); }



if ($room_descr=='') {debug_mail(false,'room_descr',$room_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }


if ($room_status!='disable' and $room_status!='available' and $room_status!='renovation') {debug_mail(false,'room_status',$room_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Κατάσταση')));
  echo json_encode($return); die(); }

if ($hotel_room_type_id<=0) {debug_mail(false,'hotel_room_type_id',$hotel_room_type_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τύπο')));
  echo json_encode($return); die(); }

$sql="select id_hotel_room_type from gks_hotel_room_type where id_hotel_room_type=".$hotel_room_type_id." and hotel_id=".$hotel_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows==0) {
  debug_mail(false,'hotel-room exist',gks_lang('Δεν βρέθηκε ο τύπος δωματίου'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο τύπος δωματίου')));
  echo json_encode($return); die();}
  

if ($hotel_floor_id<=0) {debug_mail(false,'hotel_floor_id',$hotel_floor_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Όροφο')));
  echo json_encode($return); die(); }

$sql="select id_hotel_floor from gks_hotel_floor where id_hotel_floor=".$hotel_floor_id." and hotel_id=".$hotel_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows==0) {
  debug_mail(false,'hotel-room exist',gks_lang('Δεν βρέθηκε ο όροφος'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο όροφος')));
  echo json_encode($return); die();}


$sql="select * from gks_hotel_room where room_descr like '".$db_link->escape_string($room_descr)."' and id_hotel_room<>".$id." and hotel_id=".$hotel_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=str_replace('[1]',$room_descr,gks_lang('Το δωμάτιο με περιγραφή [1] υπάρχει ήδη')).':'.
  '<br><a href="admin-hotel-room-item.php?id='.$row['id_hotel_room'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'hotel-room exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}





$form_room_photo=trim_gks(stripslashes(urldecode($_POST['form_room_photo'])));


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_room');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel_room (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-room-item.php?id='.$id); 
}
  
  
$sql="update gks_hotel_room set 
hotel_id=".$hotel_id.",
room_descr='".$db_link->escape_string($room_descr)."',
room_status='".$db_link->escape_string($room_status)."',
hotel_room_type_id=".$hotel_room_type_id.",
hotel_floor_id=".$hotel_floor_id.",
room_photo='".$db_link->escape_string($form_room_photo)."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_room = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_hotel_room',$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

