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

$my_page_title=gks_lang('Αποθήκευση Ορόφου').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');




if ($id>0) {
  $sql ="SELECT * FROM gks_hotel_floor where id_hotel_floor = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_floor.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
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
$floor_descr=''; if (isset($_POST['floor_descr'])) $floor_descr=trim_gks(base64_decode($_POST['floor_descr']));




if ($hotel_id<=0) {debug_mail(false,'hotel_id',$hotel_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το ξενοδοχείο')));
  echo json_encode($return); die(); }


if ($floor_descr=='') {debug_mail(false,'floor_descr',$floor_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }


$sql="select * from gks_hotel_floor where floor_descr like '".$db_link->escape_string($floor_descr)."' and id_hotel_floor<>".$id." and hotel_id=".$hotel_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Ο όροφος με περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-hotel-floor-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$floor_descr,$message);
  $message=str_replace('[2]',$row['id_hotel_floor'],$message);
  debug_mail(false,'hotel-room-type exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}





$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_floor');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel_floor (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-floor-item.php?id='.$id); 
}

  
$sql="update gks_hotel_floor set 
hotel_id=".$hotel_id.",
floor_descr='".$db_link->escape_string($floor_descr)."',


user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_floor = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_hotel_floor',$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

