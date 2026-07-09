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

$my_page_title=gks_lang('Αποθήκευση Τύπου Δωματίου').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room_type',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');


$product_id=0;
$is_new_rec= true;
if ($id>0) {
  $is_new_rec= false;
  $sql ="SELECT * FROM gks_hotel_room_type where id_hotel_room_type = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")";

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
$room_type_descr=''; if (isset($_POST['room_type_descr'])) $room_type_descr=trim_gks(stripslashes(urldecode($_POST['room_type_descr'])));
$room_type_status=''; if (isset($_POST['room_type_status'])) $room_type_status=trim_gks(stripslashes(urldecode($_POST['room_type_status'])));
$hotel_room_type_fix_id=0; if (isset($_POST['hotel_room_type_fix_id'])) $hotel_room_type_fix_id=intval($_POST['hotel_room_type_fix_id']);
$room_type_price=0; if (isset($_POST['room_type_price'])) $room_type_price=floatval(trim_gks(stripslashes(urldecode($_POST['room_type_price']))));
$room_type_embado=0; if (isset($_POST['room_type_embado'])) $room_type_embado=floatval(trim_gks(stripslashes(urldecode($_POST['room_type_embado']))));
$room_type_visitors=1; if (isset($_POST['room_type_visitors'])) $room_type_visitors=intval($_POST['room_type_visitors']);
$room_type_visitors_childs=0; if (isset($_POST['room_type_visitors_childs'])) $room_type_visitors_childs=intval($_POST['room_type_visitors_childs']);
$room_type_visitors_max=1; if (isset($_POST['room_type_visitors_max'])) $room_type_visitors_max=intval($_POST['room_type_visitors_max']);
$room_type_bedrooms=1; if (isset($_POST['room_type_bedrooms'])) $room_type_bedrooms=intval($_POST['room_type_bedrooms']);
$room_type_living_rooms=1; if (isset($_POST['room_type_living_rooms'])) $room_type_living_rooms=intval($_POST['room_type_living_rooms']);
$room_type_bathrooms=1; if (isset($_POST['room_type_bathrooms'])) $room_type_bathrooms=intval($_POST['room_type_bathrooms']);
$myroomdata=''; $myroomdata=trim_gks(stripslashes(urldecode($_POST['myroomdata'])));
$myamenity=''; $myamenity=trim_gks(stripslashes(urldecode($_POST['myamenity'])));
$product_id=0; if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);


if ($room_type_visitors_childs<=0) $room_type_visitors_childs=0;
if ($room_type_visitors_max < $room_type_visitors) $room_type_visitors_max = $room_type_visitors; 
if ($room_type_visitors_max > $room_type_visitors + $room_type_visitors_childs) $room_type_visitors_max = $room_type_visitors + $room_type_visitors_childs;

$room_type_child_kounies=0; if (isset($_POST['room_type_child_kounies'])) $room_type_child_kounies=intval($_POST['room_type_child_kounies']);
$room_type_extra_beds=0; if (isset($_POST['room_type_extra_beds'])) $room_type_extra_beds=intval($_POST['room_type_extra_beds']);

if ($room_type_child_kounies < 0 or $room_type_child_kounies > 9) $room_type_child_kounies=0;
if ($room_type_child_kounies > $room_type_visitors_childs) $room_type_child_kounies=$room_type_visitors_childs;
if ($room_type_extra_beds < 0 or $room_type_extra_beds > 9) $room_type_extra_beds=0;


if ($myroomdata=='') {debug_mail(false,'myroomdata',$myroomdata);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν δεδομένα κρεβατιών')));
  echo json_encode($return); die(); }
$myroomdata =json_decode($myroomdata, true);
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myroomdata,true)));
//echo json_encode($return); die();
if ($myamenity=='') {debug_mail(false,'myamenity',$myamenity);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν δεδομένα παροχών')));
  echo json_encode($return); die(); }
$myamenity =json_decode($myamenity, true);
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myamenity,true)));
//echo json_encode($return); die();

if ($hotel_id<=0) {debug_mail(false,'hotel_id',$hotel_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το ξενοδοχείο')));
  echo json_encode($return); die(); }


$hotel_params=gks_hotel_get_params($hotel_id);  

if ($room_type_descr=='') {debug_mail(false,'room_type_descr',$room_type_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }


if ($room_type_status!='disable' and $room_type_status!='available' and $room_type_status!='renovation') {debug_mail(false,'room_type_status',$room_type_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Κατάσταση')));
  echo json_encode($return); die(); }

if ($hotel_room_type_fix_id<=0) {debug_mail(false,'hotel_room_type_fix_id',$hotel_room_type_fix_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ομάδα')));
  echo json_encode($return); die(); }


if ($room_type_price < 0 ) {debug_mail(false,'room_type_price',$room_type_price);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Τιμή')));
  echo json_encode($return); die(); }


if ($room_type_embado < 0) {debug_mail(false,'room_type_embado',$room_type_embado);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Εμβαδό')));
  echo json_encode($return); die(); }

if ($room_type_visitors<=0) {debug_mail(false,'room_type_visitors',$room_type_visitors);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πλήθος των επισκεπτών')));
  echo json_encode($return); die(); }
  
if ($room_type_bedrooms<=0) {debug_mail(false,'room_type_bedrooms',$room_type_bedrooms);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πλήθος των Υπνοδωματίων')));
  echo json_encode($return); die(); }

if ($room_type_living_rooms < 0 or $room_type_living_rooms > 20) {debug_mail(false,'room_type_living_rooms',$room_type_living_rooms);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πλήθος των Σαλονιών')));
  echo json_encode($return); die(); }

if ($room_type_bathrooms < 0 or $room_type_bathrooms > 20) {debug_mail(false,'room_type_bathrooms',$room_type_bathrooms);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πλήθος των Μπάνιων')));
  echo json_encode($return); die(); }


if ($product_id <=0 and $is_new_rec==false) {debug_mail(false,'product_id',$product_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Είδος για τιμολόγηση')));
  echo json_encode($return); die(); }
  
  


//$return = array('success' => false, 'message' => base64_encode($room_type_price));
//echo json_encode($return); die();

$sql="SELECT id_hotel_room_type_subroom FROM gks_hotel_room_type_subroom WHERE hotel_room_type_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$del_in=array();
while ($row = $result->fetch_assoc()) {
  $del_in[] = $row['id_hotel_room_type_subroom'];
}

if (count($del_in)>0) {
  $sql="delete from gks_hotel_room_type_subroom_bed where hotel_room_type_subroom_id in (".implode(',',$del_in).")";  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
}
$sql="delete from gks_hotel_room_type_subroom where hotel_room_type_id =".$id;  
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$sql="select * from gks_hotel_room_type where room_type_descr like '".$db_link->escape_string($room_type_descr)."' and id_hotel_room_type<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Ο τύπος δωματίου με περιγραφή <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$room_type_descr,$message);
  $message.='<br><a href="admin-hotel-room-type-item.php?id=[2]" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'hotel-room-type exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}



$form_room_type_photo=trim_gks(stripslashes(urldecode($_POST['form_room_type_photo'])));
  
$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_room_type');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel_room_type (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-room-type-item.php?id='.$id); 
}
  
$sql="update gks_hotel_room_type set 
hotel_id=".$hotel_id.",
room_type_descr='".$db_link->escape_string($room_type_descr)."',
room_type_status='".$db_link->escape_string($room_type_status)."',
hotel_room_type_fix_id=".$hotel_room_type_fix_id.",
room_type_price=".number_format($room_type_price, 8, '.', '').",
room_type_embado=".number_format($room_type_embado, 8, '.', '').",
room_type_visitors=".$room_type_visitors.",
room_type_visitors_childs=".$room_type_visitors_childs.",
room_type_visitors_max=".$room_type_visitors_max.",
room_type_bedrooms=".$room_type_bedrooms.",
room_type_living_rooms=".$room_type_living_rooms.",
room_type_bathrooms=".$room_type_bathrooms.",
room_type_child_kounies=".$room_type_child_kounies.",
room_type_extra_beds=".$room_type_extra_beds.",
product_id=".$product_id.",
room_type_photo='".$db_link->escape_string($form_room_type_photo)."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_room_type = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

foreach ($myroomdata as $val) {
  
  if ($val['type'] != 'bedroom') $val['bath']=0;
  
  $sql="insert into gks_hotel_room_type_subroom (hotel_room_type_id,subroom_type,subroom_descr,subroom_visitors,subroom_private_bath,    
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip
  ) values (
  ".$id.",
  '". $db_link->escape_string($val['type']) ."',
  '". $db_link->escape_string($val['name']) ."',

  ".intval($val['visitors']).",
  ".($val['bath'] == '1' ? '1' : '0').",
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $id_hotel_room_type_subroom = $db_link->insert_id;
  
  $sql="insert into gks_hotel_room_type_subroom_lang (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  lang_code,hotel_room_type_subroom_id,subroom_descr
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string('en-US')."',
  ".$id_hotel_room_type_subroom.",
  '". $db_link->escape_string($val['name_en_US']) ."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  
  
  foreach ($val['rooms'] as $bed) {
    $sql="insert into gks_hotel_room_type_subroom_bed (hotel_room_type_subroom_id,hotel_bed_type_fix_id,subroom_bed_plithos,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
    ".$id_hotel_room_type_subroom.",
    ".intval($bed['type']).",
    ".intval($bed['num']).",
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }    
  } 
} 

$sql="delete from gks_hotel_room_type_amenity where hotel_room_type_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($myamenity as $aval) {
  $sql="insert into gks_hotel_room_type_amenity (hotel_room_type_id,hotel_room_amenity_type_fix_id,    
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip
  ) values (
  ".$id.",
  ".intval($aval).",
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }


}
  
if ($is_new_rec and $product_id==0) {
  $sql="insert into gks_eshop_products (
    product_code,product_descr,
    product_can_buy,product_can_sell,product_base_type,
    product_class,product_monada_id,product_type,product_object_name,
    product_price_retail,product_price_retail_sheets_formula,product_price_retail_quantity_formula,product_price,product_price_sheets_formula,product_price_quantity_formula,
    product_price_include_vat,product_price_retail_include_vat,product_is_digital,product_is_simple_download,product_need_apostoli,
    product_fpa_base_id,product_normal,
    
    product_need_multi_files,product_need_multi_files_min,product_need_multi_files_max,
    product_varos,product_ogos_x,product_ogos_y,product_ogos_z,
    product_show_on_dialog,product_sortorder,product_disable,product_min_pixels_x,product_min_pixels_y,product_min_pixels_can_rotate,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    
  ) values (
    'rt.".$id."','".$db_link->escape_string($room_type_descr)."',
    0,1,2,
    'simple',100,'normal','',
    ".number_format($room_type_price, 8, '.', '').",'','',".number_format($room_type_price, 8, '.', '').",'','',
    1,1,0,0,0,
    1002,1,
    
    
    0,0,0,
    0,0,0,0,
    0,1000,0,0,0,0,
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'

  )";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  $product_id = $db_link->insert_id; 

  $ret=gks_products_update_barcodes(array($product_id));
  if ($ret['success']==false) {
    $return = array('success' => false, 'message' => base64_encode($ret['message']));
    echo json_encode($return); die();
  }
  
  $sql="insert into gks_eshop_products_income (
  product_id,acc_eidos_parastatikou_id,aade_typos_xarakt_esodon_id,aade_katigoria_xarakt_esodon_id,acc_inv_product_income_pososto
  ) values (
  ".$product_id.",0,9,3,100
  )";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    

  

  
  $sql="update gks_hotel_room_type set product_id=".$product_id." where id_hotel_room_type = ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  
} 

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_hotel_room_type',$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

