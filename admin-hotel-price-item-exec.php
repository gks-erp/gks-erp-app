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

$my_page_title=gks_lang('Αποθήκευση Τιμής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_price',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');




$price_from_old='';
$price_to_old='';
if ($id>0) {
  $sql ="SELECT * FROM gks_hotel_price where id_hotel_price = ".$id;
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

  if (isset($row['price_from'])) $price_from_old = $row['price_from'];
  if (isset($row['price_to'])) $price_to_old = $row['price_to'];
}






$hotel_room_type_id=0; if (isset($_POST['hotel_room_type_id'])) $hotel_room_type_id=intval($_POST['hotel_room_type_id']);
if ($hotel_room_type_id==0 and isset($_POST['hotel_room_type_id'])) {
  debug_mail(false,'hotel_room_type_id',$hotel_room_type_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τύπο Δωματίου')));
  echo json_encode($return); die();}



if ($hotel_room_type_id==0) {
  debug_mail(false,'hotel_room_type_id',$hotel_room_type_id );
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τύπο Δωματίου')));
  echo json_encode($return); die();}

$hotel_id=0;
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
  
if ($_POST['price_from'] == '__/__/____') $_POST['price_from']='';
$price_from=trim_gks(stripslashes(urldecode($_POST['price_from'])));
if ($price_from!='') {
  $price_from = mystrtodb_s($price_from.' 00:00:00');
} else {
  debug_mail(false,'price_from',$price_from);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Από')));
  echo json_encode($return); die();      
}

if ($_POST['price_to'] == '__/__/____') $_POST['price_to']='';
$price_to=trim_gks(stripslashes(urldecode($_POST['price_to'])));
if ($price_to!='') {
  $price_to = mystrtodb_s($price_to.' 00:00:00');

  if (strtotime($price_to) < strtotime($price_from)) {
    debug_mail(false,'price_from',$price_from);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Έως πρέπει να είναι μεγαλύτερη ή ίση από την Ημερομηνία Από')));
    echo json_encode($return); die();      
    
  }
}


$price_descr=''; if (isset($_POST['price_descr'])) $price_descr=trim_gks(base64_decode($_POST['price_descr']));

$price=0; if (isset($_POST['price'])) $price=floatval($_POST['price']);

if ($price<=0) {debug_mail(false,'price',$price);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Τιμή')));
  echo json_encode($return); die(); }
  
$price_seldays1=0; if (isset($_POST['price_seldays1'])) $price_seldays1=intval($_POST['price_seldays1']);
$price_weekday_de=0; if (isset($_POST['price_weekday_de'])) $price_weekday_de=intval($_POST['price_weekday_de']);
$price_weekday_tr=0; if (isset($_POST['price_weekday_tr'])) $price_weekday_tr=intval($_POST['price_weekday_tr']);
$price_weekday_te=0; if (isset($_POST['price_weekday_te'])) $price_weekday_te=intval($_POST['price_weekday_te']);
$price_weekday_pe=0; if (isset($_POST['price_weekday_pe'])) $price_weekday_pe=intval($_POST['price_weekday_pe']);
$price_weekday_pa=0; if (isset($_POST['price_weekday_pa'])) $price_weekday_pa=intval($_POST['price_weekday_pa']);
$price_weekday_sa=0; if (isset($_POST['price_weekday_sa'])) $price_weekday_sa=intval($_POST['price_weekday_sa']);
$price_weekday_ky=0; if (isset($_POST['price_weekday_ky'])) $price_weekday_ky=intval($_POST['price_weekday_ky']);
if ($price_seldays1!=0) $price_seldays1=1;
if ($price_seldays1==1) {
  $price_weekday_de=1;
  $price_weekday_tr=1;
  $price_weekday_te=1;
  $price_weekday_pe=1;
  $price_weekday_pa=1;
  $price_weekday_sa=1;
  $price_weekday_ky=1;
} else {
  if ($price_weekday_de!=0) $price_weekday_de=1;
  if ($price_weekday_tr!=0) $price_weekday_tr=1;
  if ($price_weekday_te!=0) $price_weekday_te=1;
  if ($price_weekday_pe!=0) $price_weekday_pe=1;
  if ($price_weekday_pa!=0) $price_weekday_pa=1;
  if ($price_weekday_sa!=0) $price_weekday_sa=1;
  if ($price_weekday_ky!=0) $price_weekday_ky=1;
  
  if ($price_weekday_de == 0 and $price_weekday_tr == 0 and $price_weekday_te == 0 and $price_weekday_pe == 0 and 
      $price_weekday_pa == 0 and $price_weekday_sa == 0 and $price_weekday_ky == 0) {
        
        debug_mail(false,'avail_weekday_all days','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τουλάχιστον μία ημέρα')));
        echo json_encode($return); die();           
      }
}



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_price');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel_price (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-price-item.php?id='.$id); 
}
  
$sql="update gks_hotel_price set 
hotel_id=".$hotel_id.",
hotel_room_type_id=".$hotel_room_type_id.",

price_from=".($price_from == '' ? 'null' : "'".$db_link->escape_string($price_from)."'") .", 
price_to=".($price_to == '' ? 'null' : "'".$db_link->escape_string($price_to)."'") .", 
price_descr='".$db_link->escape_string($price_descr)."',
price=".number_format($price, 8, '.', '').",
price_weekday_de=".$price_weekday_de.",
price_weekday_tr=".$price_weekday_tr.",
price_weekday_te=".$price_weekday_te.",
price_weekday_pe=".$price_weekday_pe.",
price_weekday_pa=".$price_weekday_pa.",
price_weekday_sa=".$price_weekday_sa.",
price_weekday_ky=".$price_weekday_ky.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_price = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

if ($price_from_old!='' and strtotime($price_from_old) < strtotime($price_from)) {
  $price_from = date('Y-m-d',strtotime($price_from_old));
}
if ($price_to=='' or $price_to_old=='') {
  $price_to='';
} else {
  if (strtotime($price_to_old) > strtotime($price_to)) $price_to = date('Y-m-d',strtotime($price_to_old));
}
//$return = array('success' => false, 'message' => base64_encode('-'.$price_from.'-'));
//echo json_encode($return); die();


calc_price_day($hotel_room_type_id,$price_from, $price_to);

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

