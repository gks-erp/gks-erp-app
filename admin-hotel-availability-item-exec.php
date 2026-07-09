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
if ($id<=0 and $id != -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Διαθεσιμότητας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_availability',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$availability_from_old='';
$availability_to_old='';

if ($id>0) {
  $sql ="SELECT * FROM gks_hotel_availability where id_hotel_availability = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and hotel_id in (".implode(',',$perm_id_hotel_ids).")";
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
  
  if (isset($row['availability_from'])) $availability_from_old = $row['availability_from'];
  if (isset($row['availability_to'])) $availability_to_old = $row['availability_to'];
  
}



$hotel_room_id=0; if (isset($_POST['hotel_room_id'])) $hotel_room_id=intval($_POST['hotel_room_id']);
if ($hotel_room_id==0 and isset($_POST['hotel_room_id'])) {
  debug_mail(false,'hotel_room_id',$hotel_room_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Δωμάτιο')));
  echo json_encode($return); die();}

$hotel_room_type_id=0; if (isset($_POST['hotel_room_type_id'])) $hotel_room_type_id=intval($_POST['hotel_room_type_id']);
if ($hotel_room_type_id==0 and isset($_POST['hotel_room_type_id'])) {
  debug_mail(false,'hotel_room_type_id',$hotel_room_type_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τύπο Δωματίου')));
  echo json_encode($return); die();}

if ($hotel_room_id>0) $hotel_room_type_id=0;
if ($hotel_room_type_id>0) $hotel_room_id=0;

if ($hotel_room_id==0 and $hotel_room_type_id==0) {
  debug_mail(false,'hotel_room_id hotel_room_type_id',$hotel_room_id.'-'.$hotel_room_type_id );
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Δωμάτιο ή τον Τύπο Δωματίου')));
  echo json_encode($return); die();}

$hotel_id=0;
if ($hotel_room_id>0) {
  $sql ="SELECT hotel_id FROM gks_hotel_room where id_hotel_room = ".$hotel_room_id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  //echo $sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε το δωμάτιο'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το δωμάτιο')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();  
  $hotel_id=$row['hotel_id'];
}
if ($hotel_room_type_id>0) {
  $sql ="SELECT hotel_id FROM gks_hotel_room_type where id_hotel_room_type = ".$hotel_room_type_id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  //echo $sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε o Τύπος Δωματίου'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε o Τύπος Δωματίου')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();  
  $hotel_id=$row['hotel_id'];
}
  
if ($_POST['availability_from'] == '__/__/____') $_POST['availability_from']='';
$availability_from=trim_gks(stripslashes(urldecode($_POST['availability_from'])));
if ($availability_from!='') {
  $availability_from = mystrtodb_s($availability_from.' 00:00:00');
} else {
  debug_mail(false,'availability_from',$availability_from);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Από')));
  echo json_encode($return); die();      
}

if ($_POST['availability_to'] == '__/__/____') $_POST['availability_to']='';
$availability_to=trim_gks(stripslashes(urldecode($_POST['availability_to'])));
if ($availability_to!='') {
  $availability_to = mystrtodb_s($availability_to.' 00:00:00');

  if (strtotime($availability_to) < strtotime($availability_from)) {
    debug_mail(false,'availability_from',$availability_from);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Έως πρέπει να είναι μεγαλύτερη ή ίση από την Ημερομηνία Από')));
    echo json_encode($return); die();      
    
  }
}


$availability_descr=''; if (isset($_POST['availability_descr'])) $availability_descr=trim_gks(base64_decode($_POST['availability_descr']));
$availability_status=0; if (isset($_POST['availability_status'])) $availability_status=intval($_POST['availability_status']);

if ($availability_status!=0) $availability_status=1;

$availability_seldays1=0; if (isset($_POST['availability_seldays1'])) $availability_seldays1=intval($_POST['availability_seldays1']);
$avail_weekday_de=0; if (isset($_POST['avail_weekday_de'])) $avail_weekday_de=intval($_POST['avail_weekday_de']);
$avail_weekday_tr=0; if (isset($_POST['avail_weekday_tr'])) $avail_weekday_tr=intval($_POST['avail_weekday_tr']);
$avail_weekday_te=0; if (isset($_POST['avail_weekday_te'])) $avail_weekday_te=intval($_POST['avail_weekday_te']);
$avail_weekday_pe=0; if (isset($_POST['avail_weekday_pe'])) $avail_weekday_pe=intval($_POST['avail_weekday_pe']);
$avail_weekday_pa=0; if (isset($_POST['avail_weekday_pa'])) $avail_weekday_pa=intval($_POST['avail_weekday_pa']);
$avail_weekday_sa=0; if (isset($_POST['avail_weekday_sa'])) $avail_weekday_sa=intval($_POST['avail_weekday_sa']);
$avail_weekday_ky=0; if (isset($_POST['avail_weekday_ky'])) $avail_weekday_ky=intval($_POST['avail_weekday_ky']);
if ($availability_seldays1!=0) $availability_seldays1=1;
if ($availability_seldays1==1) {
  $avail_weekday_de=1;
  $avail_weekday_tr=1;
  $avail_weekday_te=1;
  $avail_weekday_pe=1;
  $avail_weekday_pa=1;
  $avail_weekday_sa=1;
  $avail_weekday_ky=1;
} else {
  if ($avail_weekday_de!=0) $avail_weekday_de=1;
  if ($avail_weekday_tr!=0) $avail_weekday_tr=1;
  if ($avail_weekday_te!=0) $avail_weekday_te=1;
  if ($avail_weekday_pe!=0) $avail_weekday_pe=1;
  if ($avail_weekday_pa!=0) $avail_weekday_pa=1;
  if ($avail_weekday_sa!=0) $avail_weekday_sa=1;
  if ($avail_weekday_ky!=0) $avail_weekday_ky=1;
  
  if ($avail_weekday_de == 0 and $avail_weekday_tr == 0 and $avail_weekday_te == 0 and $avail_weekday_pe == 0 and 
      $avail_weekday_pa == 0 and $avail_weekday_sa == 0 and $avail_weekday_ky == 0) {
        
        debug_mail(false,'avail_weekday_all days','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τουλάχιστον μία ημέρα')));
        echo json_encode($return); die();           
      }
}


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_availability');

$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel_availability (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-availability-item.php?id='.$id); 
}
  
$sql="update gks_hotel_availability set 
hotel_id=".$hotel_id.",
hotel_room_type_id=".$hotel_room_type_id.",
hotel_room_id=".$hotel_room_id.",

availability_from=".($availability_from == '' ? 'null' : "'".$db_link->escape_string($availability_from)."'") .", 
availability_to=".($availability_to == '' ? 'null' : "'".$db_link->escape_string($availability_to)."'") .", 
availability_descr='".$db_link->escape_string($availability_descr)."',
availability_status=".$availability_status.",
avail_weekday_de=".$avail_weekday_de.",
avail_weekday_tr=".$avail_weekday_tr.",
avail_weekday_te=".$avail_weekday_te.",
avail_weekday_pe=".$avail_weekday_pe.",
avail_weekday_pa=".$avail_weekday_pa.",
avail_weekday_sa=".$avail_weekday_sa.",
avail_weekday_ky=".$avail_weekday_ky.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_availability = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

if ($availability_from_old!='' and strtotime($availability_from_old) < strtotime($availability_from)) {
  $availability_from = date('Y-m-d',strtotime($availability_from_old));
}
if ($availability_to=='' or $availability_to_old=='') {
  $availability_to='';
} else {
  if (strtotime($availability_to_old) > strtotime($availability_to)) $availability_to = date('Y-m-d',strtotime($availability_to_old));
}
//$return = array('success' => false, 'message' => base64_encode('-'.$availability_from.'-'));
//echo json_encode($return); die();


calc_availability_day($hotel_room_type_id, $hotel_room_id,$availability_from, $availability_to);

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

