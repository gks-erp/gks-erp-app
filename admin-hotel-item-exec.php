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
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση ξενοδοχείου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

if ($id>0) {
  $sql="select * from gks_hotel where id_hotel=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}

$company_id=0;
$company_sub_id=0;
$user_companys=gks_get_companys_list();
if (count($user_companys)==1) {
  foreach ($user_companys as $value) {
    $company_id=$value['id_company'];
    $company_sub_id=$value['id_company_sub'];
    break;
  }    
} else {
  $company_id_sub_id=''; if (isset($_POST['company_id_sub_id'])) $company_id_sub_id=trim_gks(base64_decode($_POST['company_id_sub_id']));
  if ($company_id_sub_id!='') {
    $parts=explode('|',$company_id_sub_id);
    if (count($parts)==2) {
      $company_id=intval($parts[0]);
      $company_sub_id=intval($parts[1]);
      $found=false;
      foreach ($user_companys as $value) {
        if ($value['id_company'] == $company_id and $value['id_company_sub'] == $company_sub_id) {
          $found=true;
          break;
        }
      }
      if ($found==false) {$company_id=0;$company_sub_id=0;}
    }
  }
}

if ($company_id<=0) {
  debug_mail(false,'company_id is not found',$company_id.' '.$company_sub_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία/υποκατάστημα')));
  echo json_encode($return); die();}  
  

$hotel_title=''; if (isset($_POST['hotel_title'])) $hotel_title=trim_gks(base64_decode($_POST['hotel_title']));
$hotel_phone=''; if (isset($_POST['hotel_phone'])) $hotel_phone=trim_gks(base64_decode($_POST['hotel_phone']));
$hotel_email=''; if (isset($_POST['hotel_email'])) $hotel_email=trim_gks(base64_decode($_POST['hotel_email']));
$hotel_website=''; if (isset($_POST['hotel_website'])) $hotel_website=trim_gks(base64_decode($_POST['hotel_website']));
$hotel_odos=''; if (isset($_POST['hotel_odos'])) $hotel_odos=trim_gks(base64_decode($_POST['hotel_odos']));
$hotel_arithmos=''; if (isset($_POST['hotel_arithmos'])) $hotel_arithmos=trim_gks(base64_decode($_POST['hotel_arithmos']));
$hotel_orofos=''; if (isset($_POST['hotel_orofos'])) $hotel_orofos=trim_gks(base64_decode($_POST['hotel_orofos']));
$hotel_perioxi=''; if (isset($_POST['hotel_perioxi'])) $hotel_perioxi=trim_gks(base64_decode($_POST['hotel_perioxi']));
$hotel_poli=''; if (isset($_POST['hotel_poli'])) $hotel_poli=trim_gks(base64_decode($_POST['hotel_poli']));
$hotel_tk=''; if (isset($_POST['hotel_tk'])) $hotel_tk=trim_gks(base64_decode($_POST['hotel_tk']));
$hotel_country_id=0; if (isset($_POST['hotel_country_id'])) $hotel_country_id=intval($_POST['hotel_country_id']);
$hotel_nomos_id=0; if (isset($_POST['hotel_nomos_id'])) $hotel_nomos_id=intval($_POST['hotel_nomos_id']);
$hotel_map_latitude=0; if (isset($_POST['hotel_map_latitude'])) $hotel_map_latitude=floatval(str_replace(',','.', $_POST['hotel_map_latitude']));
$hotel_map_longitude=0; if (isset($_POST['hotel_map_longitude'])) $hotel_map_longitude=floatval(str_replace(',','.', $_POST['hotel_map_longitude']));
$hotel_disable=0; if (isset($_POST['hotel_disable'])) $hotel_disable=intval($_POST['hotel_disable']);
$hotel_color=''; if (isset($_POST['hotel_color'])) $hotel_color=trim_gks(base64_decode($_POST['hotel_color']));

$hotel_booking_number_prefix=''; if (isset($_POST['hotel_booking_number_prefix'])) $hotel_booking_number_prefix=trim_gks(base64_decode($_POST['hotel_booking_number_prefix']));


$hotel_template_eidos_descr=''; if (isset($_POST['hotel_template_eidos_descr'])) $hotel_template_eidos_descr=trim_gks(base64_decode($_POST['hotel_template_eidos_descr']));
$hotel_template_efd_descr='';   if (isset($_POST['hotel_template_efd_descr']))   $hotel_template_efd_descr  =trim_gks(base64_decode($_POST['hotel_template_efd_descr']));
$hotel_template_woo_descr='';   if (isset($_POST['hotel_template_woo_descr']))   $hotel_template_woo_descr  =trim_gks(base64_decode($_POST['hotel_template_woo_descr']));

$hotel_website_key='';   if (isset($_POST['hotel_website_key']))   $hotel_website_key  =trim_gks(base64_decode($_POST['hotel_website_key']));
$hotel_use_checkout_system='';   if (isset($_POST['hotel_use_checkout_system']))   $hotel_use_checkout_system  =trim_gks(base64_decode($_POST['hotel_use_checkout_system']));


$hotel_id_booking='';   if (isset($_POST['hotel_id_booking']))   $hotel_id_booking  =trim_gks(base64_decode($_POST['hotel_id_booking']));
$hotel_id_airbnb='';   if (isset($_POST['hotel_id_airbnb']))   $hotel_id_airbnb  =trim_gks(base64_decode($_POST['hotel_id_airbnb']));


$default_eshop_hotel=0; if (isset($_POST['default_eshop_hotel'])) $default_eshop_hotel=intval($_POST['default_eshop_hotel']);

//settings
$child_data_str = trim_gks(base64_decode($_POST['child_data_str']));
$child_data_tmp = json_decode($child_data_str, true);
if ($child_data_tmp === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['child_data_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

$child_data=array();
foreach ($child_data_tmp as $item) {
  $item['aa']=intval($item['aa']);
  $item['from']=intval($item['from']);
  $item['to']=intval($item['to']);
  if ($item['to'] < $item['from']) $item['to']=$item['from'];
  $item['price']=floatval($item['price']);
  $item['type']=trim_gks($item['type']);
  
  $child_data[]=$item;
}
usort($child_data, "GKS_HOTEL_CHILD_AGE_PRICE_SORT");
//print '<pre>';
//print_r($child_data);
//die();

$extra_beds_data_str = trim_gks(base64_decode($_POST['extra_beds_data_str']));
$extra_beds_data_tmp = json_decode($extra_beds_data_str, true);
if ($extra_beds_data_tmp === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['extra_beds_data_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

$extra_beds_enabled=0; if (isset($_POST['extra_beds_enabled'])) $extra_beds_enabled=intval($_POST['extra_beds_enabled']);
$gks_hotel_extra_beds=array(
  'enabled' => ($extra_beds_enabled ==0 ? false : true),
  'beds' => array(),
);
if ($extra_beds_enabled!=0) {
  foreach ($extra_beds_data_tmp as $item) {
    
    $item['aa']=intval($item['aa']);
    $item['from']=intval($item['from']);
    $item['to']=intval($item['to']);
    if ($item['to'] < $item['from']) $item['to']=$item['from'];
    $item['price']=floatval($item['price']);
    $item['type']=trim_gks($item['type']);
    
    $gks_hotel_extra_beds['beds'][]=$item;
  }
  usort($gks_hotel_extra_beds['beds'], "GKS_HOTEL_EXTRA_BEDS_SORT");
}
//print '<pre>';
//print_r($extra_beds_data_tmp);
//print_r($gks_hotel_extra_beds);
//die();
$gks_hotel_default_availability=0; if (isset($_POST['gks_hotel_default_availability'])) $gks_hotel_default_availability=intval($_POST['gks_hotel_default_availability']);
if ($_POST['gks_hotel_date_open'] == '__/__/____') $_POST['gks_hotel_date_open']='';
$gks_hotel_date_open=trim_gks(stripslashes(urldecode($_POST['gks_hotel_date_open'])));
if ($gks_hotel_date_open!='') {
  $gks_hotel_date_open = mystrtodb_s($gks_hotel_date_open.' 00:00:00');
}
if ($_POST['gks_hotel_date_close'] == '__/__/____') $_POST['gks_hotel_date_close']='';
$gks_hotel_date_close=trim_gks(stripslashes(urldecode($_POST['gks_hotel_date_close'])));
if ($gks_hotel_date_close!='') {
  $gks_hotel_date_close = mystrtodb_s($gks_hotel_date_close.' 00:00:00');
}
$gks_hotel_default_checkin='14:00';  if (isset($_POST['gks_hotel_default_checkin'])  and strlen($_POST['gks_hotel_default_checkin'])==5  and substr($_POST['gks_hotel_default_checkin'], 2,1)==':')  $gks_hotel_default_checkin=trim_gks($_POST['gks_hotel_default_checkin']);
$gks_hotel_default_checkout='12:00'; if (isset($_POST['gks_hotel_default_checkout']) and strlen($_POST['gks_hotel_default_checkout'])==5 and substr($_POST['gks_hotel_default_checkout'], 2,1)==':') $gks_hotel_default_checkout=trim_gks($_POST['gks_hotel_default_checkout']);
$gks_hotel_default_price=0; if (isset($_POST['gks_hotel_default_price'])) $gks_hotel_default_price=floatval(base64_decode($_POST['gks_hotel_default_price']));
$gks_hotel_reservation_can_select_room=0; if (isset($_POST['gks_hotel_reservation_can_select_room'])) $gks_hotel_reservation_can_select_room=intval($_POST['gks_hotel_reservation_can_select_room']);
$hotel_efd_product_id=0; if (isset($_POST['hotel_efd_product_id'])) $hotel_efd_product_id=intval($_POST['hotel_efd_product_id']);
$gks_hotel_reservation_days_future=0; if (isset($_POST['gks_hotel_reservation_days_future'])) $gks_hotel_reservation_days_future=intval($_POST['gks_hotel_reservation_days_future']);
$gks_hotel_reservation_min_days_online=0; if (isset($_POST['gks_hotel_reservation_min_days_online'])) $gks_hotel_reservation_min_days_online=intval($_POST['gks_hotel_reservation_min_days_online']);
$gks_hotel_reservation_max_days_online=0; if (isset($_POST['gks_hotel_reservation_max_days_online'])) $gks_hotel_reservation_max_days_online=intval($_POST['gks_hotel_reservation_max_days_online']);
$gks_hotel_child_accept=0; if (isset($_POST['gks_hotel_child_accept'])) $gks_hotel_child_accept=intval($_POST['gks_hotel_child_accept']);
$gks_hotel_child_accept_above_age=0; if (isset($_POST['gks_hotel_child_accept_above_age'])) $gks_hotel_child_accept_above_age=intval($_POST['gks_hotel_child_accept_above_age']);



if ($hotel_title=='') {debug_mail(false,'emptyl',                gks_lang('O Διακριτικός Τίτλος ΔΕΝ μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('O Διακριτικός Τίτλος ΔΕΝ μπορεί να είναι κενός')));
  echo json_encode($return); die();}


if ($hotel_efd_product_id<=0) {debug_mail(false,'emptyl',        gks_lang('Ορίστε το <b>Είδος για Είσπραξη Φόρου Διαμονής</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το <b>Είδος για Είσπραξη Φόρου Διαμονής</b>')));
  echo json_encode($return); die();}
  
  

$sql="select * from gks_hotel where hotel_title like '".$db_link->escape_string($hotel_title)."' and id_hotel<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το ξενοδοχείο με τίτλο <b>[1]</b> υπάρχει ήδη').': ';
  $message=str_replace('[1]',$hotel_title,$message);
  $message.='<br><a href="admin-hotel-item.php?id='.$row['id_hotel'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'hotel exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($hotel_email != '' and !filter_var($hotel_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,                                              gks_lang('To email δεν είναι σωστό').' : '.$hotel_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

//if ($hotel_phone != '' and (strlen($hotel_phone) != 10 or substr($hotel_phone,0,1) != '2') ) {
//  debug_mail(false,gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό').' : '.$hotel_phone);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

  
if ($hotel_country_id==0) {debug_mail(false,'emptyl',            gks_lang('Επιλέξτε μία χώρα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
  echo json_encode($return); die();}

//if ($hotel_nomos_id==0) {debug_mail(false,'emptyl',              gks_lang('Επιλέξτε έναν νομό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}

if ($gks_hotel_reservation_days_future < 1) $gks_hotel_reservation_days_future=1;
if ($gks_hotel_reservation_min_days_online < 1) $gks_hotel_reservation_min_days_online=1;
if ($gks_hotel_reservation_max_days_online < 1) $gks_hotel_reservation_max_days_online=1;

if ($gks_hotel_reservation_max_days_online < $gks_hotel_reservation_min_days_online) $gks_hotel_reservation_max_days_online=$gks_hotel_reservation_min_days_online;

if ($hotel_website_key!='') {

  if (strlen($hotel_website_key)<20) {
    $message=gks_lang('Το κλειδί θα πρέπει να είναι τουλάχιστον 20 χαρακτήρες');
    debug_mail(false,'hotel exist symbol',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
    
  $sql="select * from gks_hotel where hotel_website_key like '".$db_link->escape_string($hotel_website_key)."' and id_hotel<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Το κλειδί <b>[1]</b> υπάρχει ήδη στο ξενοδοχείο με τίτλο').':';
    $message=str_replace('[1]',$hotel_website_key,$message);
    $message.='<br><a href="admin-hotel-item.php?id='.$row['id_hotel'].'" class="gks_link">'.$row['hotel_title'].'</a>';
    debug_mail(false,'hotel exist symbol',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
}



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_hotel (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hotel-item.php?id='.$id); 
}

if ($gks_hotel_child_accept==0 or $gks_hotel_child_accept_above_age >= 6) {
  $gks_hotel_child_kounies=array('enable' => false, 'from' => 0, 'to' => 0, 'price' => 0, 'type' => 'night');
} else {
  $gks_hotel_child_kounies_enable=0; if (isset($_POST['gks_hotel_child_kounies_enable'])) $gks_hotel_child_kounies_enable=intval($_POST['gks_hotel_child_kounies_enable']);
  $gks_hotel_child_kounies_from=0; if (isset($_POST['gks_hotel_child_kounies_from'])) $gks_hotel_child_kounies_from=intval($_POST['gks_hotel_child_kounies_from']);
  $gks_hotel_child_kounies_to=0; if (isset($_POST['gks_hotel_child_kounies_to'])) $gks_hotel_child_kounies_to=intval($_POST['gks_hotel_child_kounies_to']);
  $gks_hotel_child_kounies_price=0; if (isset($_POST['gks_hotel_child_kounies_price'])) $gks_hotel_child_kounies_price=floatval($_POST['gks_hotel_child_kounies_price']);
  $gks_hotel_child_kounies_type=''; if (isset($_POST['gks_hotel_child_kounies_type'])) $gks_hotel_child_kounies_type=trim_gks($_POST['gks_hotel_child_kounies_type']);
  
  if ($gks_hotel_child_kounies_type!='night' and $gks_hotel_child_kounies_type!='stay') $gks_hotel_child_kounies_type!='stay';
  
  $gks_hotel_child_kounies=array(
    'enable' => ($gks_hotel_child_kounies_enable==1 ? true : false), 
    'from' => $gks_hotel_child_kounies_from, 
    'to' => $gks_hotel_child_kounies_to, 
    'price' => $gks_hotel_child_kounies_price, 
    'type' => $gks_hotel_child_kounies_type,
  );
}

$sql="update gks_hotel set 
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
hotel_title='".$db_link->escape_string($hotel_title)."',
hotel_phone=". ($hotel_phone =='' ? 'null' : "'".$db_link->escape_string($hotel_phone)."'").",
hotel_email=". ($hotel_email =='' ? 'null' : "'".$db_link->escape_string($hotel_email)."'").",
hotel_website=". ($hotel_email =='' ? 'null' : "'".$db_link->escape_string($hotel_website)."'").",
hotel_odos=". ($hotel_odos =='' ? 'null' : "'".$db_link->escape_string($hotel_odos)."'").",
hotel_arithmos=". ($hotel_arithmos =='' ? 'null' : "'".$db_link->escape_string($hotel_arithmos)."'").",
hotel_orofos=". ($hotel_orofos =='' ? 'null' : "'".$db_link->escape_string($hotel_orofos)."'").",
hotel_perioxi=". ($hotel_perioxi =='' ? 'null' : "'".$db_link->escape_string($hotel_perioxi)."'").",
hotel_poli=". ($hotel_poli =='' ? 'null' : "'".$db_link->escape_string($hotel_poli)."'").",
hotel_tk=". ($hotel_tk =='' ? 'null' : "'".$db_link->escape_string($hotel_tk)."'").",
hotel_country_id=".$hotel_country_id.",
hotel_nomos_id=".$hotel_nomos_id.",

hotel_map_latitude='".number_format($hotel_map_latitude,16,'.','')."',
hotel_map_longitude='".number_format($hotel_map_longitude,16,'.','')."',

hotel_disable=".$hotel_disable.",
hotel_color=". ($hotel_color =='' ? 'null' : "'".$db_link->escape_string($hotel_color)."'").",

default_eshop_hotel=".$default_eshop_hotel.",


hotel_booking_number_prefix='".$db_link->escape_string($hotel_booking_number_prefix)."',

hotel_default_availability=".$gks_hotel_default_availability.",
hotel_date_open='".$db_link->escape_string($gks_hotel_date_open)."',
hotel_date_close='".$db_link->escape_string($gks_hotel_date_close)."',
hotel_default_checkin='".$db_link->escape_string($gks_hotel_default_checkin)."',
hotel_default_checkout='".$db_link->escape_string($gks_hotel_default_checkout)."',
hotel_default_price='".number_format($gks_hotel_default_price,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."',
hotel_reservation_can_select_room=".$gks_hotel_reservation_can_select_room.",
hotel_efd_product_id=".$hotel_efd_product_id.",
hotel_reservation_days_future=".$gks_hotel_reservation_days_future.",
hotel_reservation_min_days_online=".$gks_hotel_reservation_min_days_online.",
hotel_reservation_max_days_online=".$gks_hotel_reservation_max_days_online.",
hotel_child_accept=".$gks_hotel_child_accept.",
hotel_child_accept_above_age=".$gks_hotel_child_accept_above_age.",
hotel_child_age_price='".$db_link->escape_string(json_encode($child_data))."',
hotel_extra_beds='".$db_link->escape_string(json_encode($gks_hotel_extra_beds))."',
hotel_child_kounies='".$db_link->escape_string(json_encode($gks_hotel_child_kounies))."',

hotel_template_eidos_descr='".      $db_link->escape_string($hotel_template_eidos_descr)."',
hotel_template_efd_descr='".        $db_link->escape_string($hotel_template_efd_descr)."',
hotel_template_woo_descr='".        $db_link->escape_string($hotel_template_woo_descr)."',

hotel_website_key='".$db_link->escape_string($hotel_website_key)."',
hotel_use_checkout_system='".$db_link->escape_string($hotel_use_checkout_system)."',
hotel_id_booking='".$db_link->escape_string($hotel_id_booking)."',
hotel_id_airbnb='".$db_link->escape_string($hotel_id_airbnb)."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_hotel = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


if ($default_eshop_hotel!=0) {
  $sql="update gks_hotel set default_eshop_hotel=0 where id_hotel<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

}
 

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_hotel',$id);

$ret_run=gks_sociallinks_item_save($_POST,'gks_hotel',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







