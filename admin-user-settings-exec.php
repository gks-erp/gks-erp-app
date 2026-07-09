<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Αποθήκευση Οι Ρυθμίσεις μου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings_users','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('subscriber',$my_wp_user_info->roles))  $userrole='subscriber';
}
if ($userrole!='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}

if (isset($_POST['enter_order_gks_whi_mov_str'])) {
  $enter_order_gks_whi_mov_str = trim_gks(base64_decode($_POST['enter_order_gks_whi_mov_str']));
  $enter_order_gks_whi_mov = json_decode($enter_order_gks_whi_mov_str, true);
  if ($enter_order_gks_whi_mov === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['enter_order_gks_whi_mov_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $temp=serialize($enter_order_gks_whi_mov);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_whi_mov','enter_order')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}

if (isset($_POST['enter_order_paraggelia_str'])) {
  $enter_order_paraggelia_str = trim_gks(base64_decode($_POST['enter_order_paraggelia_str']));
  $enter_order_paraggelia = json_decode($enter_order_paraggelia_str, true);
  if ($enter_order_paraggelia === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['enter_order_paraggelia_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $temp=serialize($enter_order_paraggelia);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_orders','enter_order')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


if (isset($_POST['enter_order_parastatiko_str'])) {
  $enter_order_parastatiko_str = trim_gks(base64_decode($_POST['enter_order_parastatiko_str']));
  $enter_order_parastatiko = json_decode($enter_order_parastatiko_str, true);
  if ($enter_order_parastatiko === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['enter_order_parastatiko_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $temp=serialize($enter_order_parastatiko);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_acc_inv','enter_order')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


  
if (isset($_POST['gks_whi_mov_tropos_apostolis'])) {
  $temp=intval($_POST['gks_whi_mov_tropos_apostolis']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_whi_mov','tropos_apostolis')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}
  
if (isset($_POST['gks_orders_tropos_apostolis'])) {
  $temp=intval($_POST['gks_orders_tropos_apostolis']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_orders','tropos_apostolis')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


if (isset($_POST['gks_orders_tropos_pliromis'])) {
  $temp=intval($_POST['gks_orders_tropos_pliromis']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_orders','tropos_pliromis')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}



if (isset($_POST['gks_acc_inv_tropos_apostolis'])) {
  $temp=intval($_POST['gks_acc_inv_tropos_apostolis']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_acc_inv','tropos_apostolis')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


if (isset($_POST['gks_acc_inv_tropos_pliromis'])) {
  $temp=intval($_POST['gks_acc_inv_tropos_pliromis']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','gks_acc_inv','tropos_pliromis')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}






$temp=''; if (isset($_POST['file_type'])) $temp=trim_gks(base64_decode($_POST['file_type']));
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','file_type')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp='false'; if (isset($_POST['is_landscape'])) $temp=(intval($_POST['is_landscape'])==0 ? 'false' : 'true');
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','landscape')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }



$temp='false'; if (isset($_POST['grayscale'])) $temp=(intval($_POST['grayscale'])==0 ? 'false' : 'true');
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','grayscale')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=100; if (isset($_POST['zoom'])) $temp=floatval($_POST['zoom']);
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','zoom')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

if (isset($_POST['print_form_id_order'])) {
  $temp=intval($_POST['print_form_id_order']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','form_id_order')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


if (isset($_POST['print_form_id_inv'])) {
  $temp=intval($_POST['print_form_id_inv']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','form_id_inv')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}

if (isset($_POST['print_form_id_pay'])) {
  $temp=0; if (isset($_POST['print_form_id_pay'])) $temp=intval($_POST['print_form_id_pay']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','form_id_pay')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}

if (isset($_POST['print_form_id_whi'])) {
  $temp=intval($_POST['print_form_id_whi']);
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','print','form_id_whi')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}



$temp='el-GR';  if (isset($_POST['user_lang_backend']))  $temp=trim_gks($_POST['user_lang_backend']);
//if ($temp!='el-GR' and $temp!='en-US') $temp='el-GR';
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','lang','backend')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='none';  if (isset($_POST['autocomplete_address']))  $temp=trim_gks($_POST['autocomplete_address']);
if ($temp!='from_db' and $temp!='from_googlemaps') $temp='none';
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','autocomplete','address')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['menu_pos']))  $temp=trim_gks($_POST['menu_pos']);
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','menu','pos')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0;  if (isset($_POST['menu_sticky_top']))  $temp=intval($_POST['menu_sticky_top']);
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','menu','sticky-top')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0;  if (isset($_POST['menu_hover']))  $temp=intval($_POST['menu_hover']);
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','menu','hover')";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$voip_extensions='';if (isset($_POST['voip_extensions'])) $voip_extensions=trim_gks(base64_decode($_POST['voip_extensions'])); 
$voip_extension_def='';if (isset($_POST['voip_extension_def'])) $voip_extension_def=trim_gks(base64_decode($_POST['voip_extension_def'])); 
if ($voip_extensions=='') $voip_extension_def='';
if ($voip_extensions!='') {
  $temp=explode(',',$voip_extensions);
  if (count($temp)==1) {
    $voip_extension_def=$voip_extensions;
  } else {
    if ($voip_extension_def=='') {
      $voip_extension_def=$temp[0];
    } else {
      if (in_array($voip_extension_def,$temp)==false) {
        $return = array('success' => false, 'message' => base64_encode(gks_lang('To Εσωτερικό τηλέφωνο για αυτόν τον φυλλομετρητή θα πρέπει να υπάρχει στην λίστα Εσωτερικό τηλέφωνο')));
        echo json_encode($return); die();         
      }
    }
  }
}

$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($voip_extensions)."','voip','extensions')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

//$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
//".$my_wp_user_id.",'".$db_link->escape_string($voip_extension_def)."','voip','extension_def')";
//$result = $db_link->query($sql);        
//if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }



if (isset($_POST['dav_password'])) {
  $temp='';  if (isset($_POST['dav_password']))  $temp=trim_gks(base64_decode($_POST['dav_password'])); 
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($temp)."','dav','password')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}


$notif_str = trim_gks(base64_decode($_POST['notif']));
$notif_array = json_decode($notif_str, true);
if ($notif_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error notif',$_POST['notif']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  
//print '<pre>';print_r($notif_array);die();

$notif=array();
foreach ($notif_array as $value) {
  $nid=intval($value['nid']);
  if ($nid>0) {
    $admin=0;if (isset($value['admin'])) $admin=(intval($value['admin'])==1 ? 1 : 0);
    $user=0;if (isset($value['user'])) $user=(intval($value['user'])==1 ? 1 : 0);
    $email=0;if (isset($value['email'])) $email=(intval($value['email'])==1 ? 1 : 0);
    $viber=0;if (isset($value['viber'])) $viber=(intval($value['viber'])==1 ? 1 : 0);
    $notif[$nid]=array(
      'nid'=> $nid,
      'user'=> $user,
      'email'=> $email,
      'viber'=> $viber,
    );
  }
}

foreach ($notif as $value) {
  $sql="update gks_notification_userperm set
  from_user=".$value['user'].",
  to_email=".$value['email'].",
  to_viber=".$value['viber'].",
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where user_id=".$my_wp_user_id." and notification_type_id=".$value['nid']." and from_admin=1";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
} 



$custom_css_user = trim_gks(base64_decode($_POST['custom_css_user']));
//echo '<pre>';echo  $custom_css_user;die(); 
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($custom_css_user)."','css','user')";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


if ($GKS_CRM_TASKS_ENABLE) { 

  $def_duration_minutes=0;
  $gks_crm_tasks_def_duration=''; if (isset($_POST['gks_crm_tasks_def_duration'])) $gks_crm_tasks_def_duration=trim_gks(base64_decode($_POST['gks_crm_tasks_def_duration']));
  if (strlen($gks_crm_tasks_def_duration)==5 and substr($gks_crm_tasks_def_duration,2,1)==':') {
    $hours=intval(substr($gks_crm_tasks_def_duration,0,2));
    $minutes=intval(substr($gks_crm_tasks_def_duration,3,2));
    $def_duration_minutes=$hours*60+$minutes;
  }
  if ($def_duration_minutes<=0) $def_duration_minutes=60;
  $sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
  ".$my_wp_user_id.",'".$db_link->escape_string($def_duration_minutes)."','gks_crm_tasks','def_duration_minutes')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  

  $erp_app_id_check=0; if (isset($_POST['erp_app_id_check'])) $erp_app_id_check=intval($_POST['erp_app_id_check']);
  $erp_app_id=0; if (isset($_POST['erp_app_id'])) $erp_app_id=intval($_POST['erp_app_id']);
  $erp_app_dest=''; if (isset($_POST['erp_app_dest'])) $erp_app_dest=trim_gks(base64_decode($_POST['erp_app_dest']));
  $erp_app_dest_printer=''; if (isset($_POST['erp_app_dest_printer'])) $erp_app_dest_printer=trim_gks(base64_decode($_POST['erp_app_dest_printer']));
  $erp_app_dest_printer_method=0; if (isset($_POST['erp_app_dest_printer_method'])) $erp_app_dest_printer_method=intval($_POST['erp_app_dest_printer_method']);
  $erp_app_dest_printer_lpr_ip=''; if (isset($_POST['erp_app_dest_printer_lpr_ip'])) $erp_app_dest_printer_lpr_ip=trim_gks(base64_decode($_POST['erp_app_dest_printer_lpr_ip']));
  $erp_app_dest_printer_copies=0; if (isset($_POST['erp_app_dest_printer_copies'])) $erp_app_dest_printer_copies=intval($_POST['erp_app_dest_printer_copies']);
  $erp_app_dest_folder=''; if (isset($_POST['erp_app_dest_folder'])) $erp_app_dest_folder=trim_gks(base64_decode($_POST['erp_app_dest_folder']));

  if ($erp_app_id_check!=0) $erp_app_id_check=1;
  if ($erp_app_id_check==0) {
    $erp_app_id=0;  
    $erp_app_dest='';
    $erp_app_dest_printer='';
    $erp_app_dest_printer_method=0;
    $erp_app_dest_printer_lpr_ip='';
    $erp_app_dest_printer_copies=0;
    $erp_app_dest_folder='';
    $erp_app_filter='';
  } else {
    if ($erp_app_id<1) {
      debug_mail(false,'erp_app_id is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την εφαρμογή gks ERP App Desktop')));
      echo json_encode($return); die(); } 
    
 
    
    $sql="select * from gks_erp_app where id_erp_app=".$erp_app_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows<=0) {
      debug_mail(false,'erp_app_id not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η gks ERP App Desktop')));
      echo json_encode($return); die(); } 
  
    if ($erp_app_dest!='printer' and $erp_app_dest!='folder') $erp_app_dest='';
    if ($erp_app_dest=='') {
      debug_mail(false,'erp_app_dest is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Προορισμός')));
      echo json_encode($return); die(); }
    
    if ($erp_app_dest=='printer') {
      $erp_app_dest_folder='';
      
      if ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1) $erp_app_dest_printer_lpr_ip='';
      if ($erp_app_dest_printer_method==2) $erp_app_dest_printer='';
      if ($erp_app_dest_printer_method==3) {$erp_app_dest_printer_lpr_ip=''; $erp_app_dest_printer=''; }
      
  
      if ($erp_app_dest_printer_method < 0 or $erp_app_dest_printer_method > 3) {
        debug_mail(false,'erp_app_dest_printer_method is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Η μέθοδος πρέπει να είναι 0,1,2 ή 3')));
        echo json_encode($return); die(); } 
      
      if ($erp_app_dest_printer=='' and ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1)) {
        debug_mail(false,'erp_app_dest_printer is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Εκτυπωτή')));
        echo json_encode($return); die(); } 
      if ($erp_app_dest_printer_lpr_ip=='' and $erp_app_dest_printer_method==2) {
        debug_mail(false,'erp_app_dest_printer_lpr_ip is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε την IP του εκτυπωτή')));
        echo json_encode($return); die(); } 
        
        
      if ($erp_app_dest_printer_copies < 1 and $erp_app_dest_printer_copies > 5) {
        debug_mail(false,'erp_app_dest_printer_copies is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Τα αντίτυπα πρέπει να είναι 1,2,3,4 ή 5')));
        echo json_encode($return); die(); } 
      
      //echo '<pre>'. $erp_app_dest_printer;die();    
      
    } else if ($erp_app_dest=='folder') {
      $erp_app_dest_printer='';
      $erp_app_dest_printer_method=0;
      $erp_app_dest_printer_lpr_ip='';
      $erp_app_dest_printer_copies=0;
      
      if ($erp_app_dest_folder=='') {
        debug_mail(false,'erp_app_dest_folder is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον φάκελο αποστολής')));
        echo json_encode($return); die(); } 
      
      if (endwith($erp_app_dest_folder,'\\')==false) $erp_app_dest_folder.='\\';
      
      $params=array(
        'id' => $erp_app_id,
        'cmd' => 'run_command_folder_exist',
        'postdata' => array (
          'folder' => $erp_app_dest_folder,
          'and_writable' => true,
        ),
      );
      if (false) {
      $gks_erp_run_result=gks_erp_app_run_command($params);
  
      if ($gks_erp_run_result['success']==false) {
        $return = array('success' => false, 'message' => base64_encode($gks_erp_run_result['message']));
        echo json_encode($return); die(); }
      
      }
  
              
      //print '<pre>wwwwwwwwwwwww';print_r($gks_erp_run_result);die();
      
    }
  }
  

      
  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_id','".$erp_app_id."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest','".$db_link->escape_string($erp_app_dest)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest_printer','".$db_link->escape_string($erp_app_dest_printer)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest_printer_method','".$db_link->escape_string($erp_app_dest_printer_method)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest_folder','".$db_link->escape_string($erp_app_dest_folder)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest_printer_copies','".$db_link->escape_string($erp_app_dest_printer_copies)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $sql="replace into gks_settings_users (user_id,myobject,mysubobject,myvalue) values (
  ".$my_wp_user_id.",'gks_crm_tasks','print_erp_app_dest_printer_lpr_ip','".$db_link->escape_string($erp_app_dest_printer_lpr_ip)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  
}

$temp='';  if (isset($_POST['htmlcss_font_size']))  $temp=trim_gks(base64_decode($_POST['htmlcss_font_size'])); 
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','htmlcss','font_size')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$htmlcss_font_family='';  if (isset($_POST['htmlcss_font_family']))  $htmlcss_font_family=trim_gks(base64_decode($_POST['htmlcss_font_family'])); 

$htmlcss_font_family_group='';
$htmlcss_font_family_link='';
if ($htmlcss_font_family!='') {
  $temp=explode('|||',$htmlcss_font_family);
  if (count($temp)==2) {
    $htmlcss_font_family_group=$temp[0];
    $htmlcss_font_family=$temp[1];
  
  
    if ($htmlcss_font_family_group=='gl_fonts') {
      switch ($htmlcss_font_family) {   
        case 'Advent Pro':
          $htmlcss_font_family_link= '<link href="css/font-advent-pro.css?v=[[[gks_cache_version]]]" rel="stylesheet">';
          break;
        case 'Inter':
          $htmlcss_font_family_link= '<link href="css/font-inter.css?v=[[[gks_cache_version]]]" rel="stylesheet">';
          break;
        case 'Open Sans':
          $htmlcss_font_family_link= '<link href="css/font-open-sans.css?v=[[[gks_cache_version]]]" rel="stylesheet">';
          break;
        case 'Roboto':
          $htmlcss_font_family_link= '<link href="css/font-roboto.css?v=[[[gks_cache_version]]]" rel="stylesheet">';
          break;
        case 'Roboto Condensed':
          $htmlcss_font_family_link= '<link href="css/font-roboto-condensed.css?v=[[[gks_cache_version]]]" rel="stylesheet">';
          break;
      }
    } else if ($htmlcss_font_family_group=='glcdn_fonts') {
      $temp='';
      switch ($htmlcss_font_family) {   
        case 'Advent Pro': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Alegreya': $temp='ital,wght@0,400..900;1,400..900';break;
        case 'Alegreya SC': $temp='ital,wght@0,400;0,500;0,700;0,800;0,900;1,400;1,500;1,700;1,800;1,900';break;
        case 'Alegreya Sans SC': $temp='ital,wght@0,100;0,300;0,400;0,500;0,700;0,800;0,900;1,100;1,300;1,400;1,500;1,700;1,800;1,900';break;
        case 'Alegreya Sans': $temp='ital,wght@0,100;0,300;0,400;0,500;0,700;0,800;0,900;1,100;1,300;1,400;1,500;1,700;1,800;1,900';break;
        case 'Anonymous Pro': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Arima': $temp='wght@100..700';break;
        case 'Arimo': $temp='ital,wght@0,400..700;1,400..700';break;
        case 'Bona Nova SC': $temp='ital,wght@0,400;0,700;1,400';break;
        case 'Bona Nova': $temp='ital,wght@0,400;0,700;1,400';break;
        case 'Brygada 1918': $temp='ital,wght@0,400..700;1,400..700';break;
        case 'Cardo': $temp='ital,wght@0,400;0,700;1,400';break;
        case 'Carlito': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Cascadia Code': $temp='ital,wght@0,200..700;1,200..700';break;
        case 'Cascadia Mono': $temp='ital,wght@0,200..700;1,200..700';break;
        case 'Caudex': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Chiron Hei HK': $temp='ital,wght@0,200..900;1,200..900';break;
        case 'Chiron Sung HK': $temp='ital,wght@0,200..900;1,200..900';break;
        case 'Comfortaa': $temp='wght@300..700';break;
        case 'Comic Relief': $temp='wght@400;700';break;
        case 'Commissioner': $temp='wght@100..900';break;
        case 'Cousine': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Dela Gothic One';break;
        case 'Didact Gothic';break;
        case 'EB Garamond': $temp='ital,wght@0,400..800;1,400..800';break;
        case 'Eczar': $temp='wght@400..800';break;
        case 'Fira Code': $temp='wght@300..700';break;
        case 'Fira Mono': $temp='wght@400;500;700';break;
        case 'Fira Sans Condensed': $temp='ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';break;
        case 'Fira Sans Extra Condensed': $temp='ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';break;
        case 'Fira Sans': $temp='ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';break;
        case 'GFS Didot': $temp='';break;
        case 'GFS Neohellenic': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Gentium Book Plus': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Gentium Plus': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Geologica': $temp='wght@100..900';break;
        case 'Gidole': $temp='';break;
        case 'Gothic A1': $temp='';break;
        case 'Handjet': $temp='wght@100..900';break;
        case 'IBM Plex Sans': $temp='ital,wght@0,100..700;1,100..700';break;
        case 'Inter': $temp='ital,opsz,wght@0,14..32,100..900;1,14..32,100..900';break;
        case 'Inter Tight': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'JetBrains Mono': $temp='ital,wght@0,100..800;1,100..800';break;
        case 'Jura': $temp='wght@300..700';break;
        case 'LXGW Marker Gothic': $temp='';break;
        case 'LXGW WenKai Mono TC': $temp='';break;
        case 'LXGW WenKai TC': $temp='';break;
        case 'Libertinus Math': $temp='';break;
        case 'Libertinus Sans': $temp='ital,wght@0,400;0,700;1,400';break;
        case 'Libertinus Serif Display': $temp='';break;
        case 'Libertinus Serif': $temp='ital,wght@0,400;0,600;0,700;1,400;1,600;1,700';break;
        case 'Literata': $temp='ital,opsz,wght@0,7..72,200..900;1,7..72,200..900';break;
        case 'M PLUS 1p': $temp='';break;
        case 'M PLUS Rounded 1c': $temp='';break;
        case 'Manrope': $temp='wght@200..800';break;
        case 'Mansalva': $temp='';break;
        case 'Moderustic': $temp='wght@300..800';break;
        case 'Murecho': $temp='wght@100..900';break;
        case 'Mynerve': $temp='';break;
        case 'News Cycle': $temp='wght@400;700';break;
        case 'Noto Sans Display': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Noto Sans Mono': $temp='wght@100..900';break;
        case 'Noto Sans': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Noto Serif Display': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Noto Serif': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Nova Mono': $temp='';break;
        case 'Oi': $temp='';break;
        case 'Open Sans': $temp='ital,wght@0,300..800;1,300..800';break;
        case 'Piazzolla': $temp='ital,opsz,wght@0,8..30,100..900;1,8..30,100..900';break;
        case 'Play': $temp='wght@400;700';break;
        case 'Playpen Sans': $temp='wght@100..800';break;
        case 'Press Start 2P': $temp='';break;
        case 'Roboto Condensed': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'Roboto Flex': $temp='opsz,wght@8..144,100..1000';break;
        case 'Roboto Mono': $temp='ital,wght@0,100..700;1,100..700';break;
        case 'Roboto Slab': $temp='wght@100..900';break;
        case 'Roboto': $temp='ital,wght@0,100..900;1,100..900';break;
        case 'STIX Two Text': $temp='ital,wght@0,400..700;1,400..700';break;
        case 'Sansation': $temp='ital,wght@0,300;0,400;0,700;1,300;1,400;1,700';break;
        case 'Sofia Sans Condensed': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Sofia Sans Extra Condensed': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Sofia Sans Semi Condensed': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Sofia Sans': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Source Code Pro': $temp='ital,wght@0,200..900;1,200..900';break;
        case 'Source Sans 3': $temp='ital,wght@0,200..900;1,200..900';break;
        case 'Source Serif 4': $temp='ital,opsz,wght@0,8..60,200..900;1,8..60,200..900';break;
        case 'Syne': $temp='wght@400..800';break;
        case 'Tektur': $temp='wght@400..900';break;
        case 'TikTok Sans': $temp='opsz,wght@12..36,300..900';break;
        case 'Tinos': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Tiny5': $temp='';break;
        case 'Tuffy': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Ubuntu Condensed': $temp='';break;
        case 'Ubuntu Mono': $temp='ital,wght@0,400;0,700;1,400;1,700';break;
        case 'Ubuntu Sans Mono': $temp='ital,wght@0,400..700;1,400..700';break;
        case 'Ubuntu Sans': $temp='ital,wght@0,100..800;1,100..800';break;
        case 'Ubuntu': $temp='ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700';break;
        case 'UNAL Ancizar Sans': $temp='ital,wght@0,100..1000;1,100..1000';break;
        case 'UNAL Ancizar Serif': $temp='ital,wght@0,300..900;1,300..900';break;
        case 'Victor Mono': $temp='ital,wght@0,100..700;1,100..700';break;
        case 'Vollkorn': $temp='ital,wght@0,400..900;1,400..900';break;
        case 'Ysabeau Infant': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Ysabeau Office': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Ysabeau SC': $temp='wght@1..1000';break;
        case 'Ysabeau': $temp='ital,wght@0,1..1000;1,1..1000';break;
        case 'Zen Antique': $temp='';break;
        case 'Zen Antique Soft': $temp='';break;
        case 'Zen Kurenaido': $temp='';break;
        case 'Zen Maru Gothic': $temp='';break;
        case 'Zen Old Mincho': $temp='';break;
        
        
      }
      if ($temp!='') {
        $temp=':'.$temp;
      }
      $htmlcss_font_family_conv=$htmlcss_font_family;
      $htmlcss_font_family_conv=str_replace(' ', '+', $htmlcss_font_family_conv);
      $htmlcss_font_family_conv=str_replace('UNAL+Ancizar+', 'Ancizar+', $htmlcss_font_family_conv);
      
      $htmlcss_font_family_link='<link rel="preconnect" href="https://fonts.googleapis.com">'."\n";
      $htmlcss_font_family_link.='<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'."\n";
      $htmlcss_font_family_link.='<link href="https://fonts.googleapis.com/css2?family='.
      $htmlcss_font_family_conv.
      $temp.'&display=swap" rel="stylesheet">'."\n";
  
    }
  }
}
$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($htmlcss_font_family)."','htmlcss','font_family')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($htmlcss_font_family_group)."','htmlcss','font_family_group')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($htmlcss_font_family_link)."','htmlcss','font_family_link')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  
gks_cache_update_menu_version();



$return = array('success' => true, 'message' => base64_encode('OK'),'voip_extension_def'=>$voip_extension_def);
echo json_encode($return); die();

