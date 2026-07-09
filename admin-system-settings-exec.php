<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Αποθήκευση Ρυθμίσεων Εφαρμογής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


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


$user_roles_new=array();
foreach ($_POST as $key => $value) {
  if (startwith($key,'role_') and intval($value)!=0) {
    $role_id=substr($key, 5);
    if ($role_id!='administrator' and $role_id!='adminmy' and $role_id!='subscriber') {
      $user_roles_new[]=$role_id;
    }
  }
}
//if (count($user_roles_new)==0) $user_roles_new[]='subscriber';
//print '<pre>';print_r($user_roles_new);die();
$temp=serialize($user_roles_new);
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_USERS_ACCESS_ROLES','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp='';  if (isset($_POST['GKS_SITE_HUMAN_NAME']))  $temp=trim_gks(base64_decode($_POST['GKS_SITE_HUMAN_NAME']));
if ($temp=='') $temp='My App';
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SITE_HUMAN_NAME','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_OFFICIAL_SITE_URL']))  $temp=trim_gks(base64_decode($_POST['GKS_OFFICIAL_SITE_URL']));
if ($temp=='') {
  debug_mail(false,'set GKS_OFFICIAL_SITE_URL','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον επίσημο ιστότοπο')));
  echo json_encode($return); die();}
  
if (startwith($temp,'http://')==false and startwith($temp,'https://')==false) {
  debug_mail(false,'eror on GKS_OFFICIAL_SITE_URL','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Επίσημος ιστότοπος θα πρέπει να ξεκινά από<br>http://<br>ή<br>https://')));
  echo json_encode($return); die();}
if (endwith($temp,'/')) $temp=substr($temp, 0, strlen($temp)-1);
if (endwith($temp,'/')) $temp=substr($temp, 0, strlen($temp)-1);
if (endwith($temp,'/')) $temp=substr($temp, 0, strlen($temp)-1);
if (endwith($temp,'/')) $temp=substr($temp, 0, strlen($temp)-1);

//echo time().'|'.$temp; die();  
//if ($temp=='') $temp='My App';
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_OFFICIAL_SITE_URL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_SITE_NAME']))  $temp=trim_gks(base64_decode($_POST['GKS_SITE_NAME']));
if ($temp=='') {
  debug_mail(false,'set GKS_SITE_NAME','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το επίσημο όνομα ιστότοπου')));
  echo json_encode($return); die();}
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SITE_NAME','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }





$temp=',';  if (isset($_POST['GKS_NUMBER_FORMAT_DECIMAL']))  $temp=trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_DECIMAL']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_DECIMAL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='.';  if (isset($_POST['GKS_NUMBER_FORMAT_THOUSAND']))  $temp=trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_THOUSAND']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_THOUSAND','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=2; if (isset($_POST['GKS_NUMBER_FORMAT_CURRENCY_DECIMAL']))  $temp=intval($_POST['GKS_NUMBER_FORMAT_CURRENCY_DECIMAL']);
if ($temp<0) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_CURRENCY_DECIMAL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='&euro;';  if (isset($_POST['GKS_NUMBER_FORMAT_CURRENCY_SYMBOL']))  $temp=htmlentities(trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_CURRENCY_SYMBOL'])),ENT_QUOTES);
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_CURRENCY_SYMBOL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW']))  $temp=trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW']));
if ($temp!='' and $temp!='after' and $temp!='before') $temp='after';
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_NUMBER_FORMAT_DATE']))  $temp=trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_DATE']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_DATE','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_NUMBER_FORMAT_TIME']))  $temp=trim_gks(base64_decode($_POST['GKS_NUMBER_FORMAT_TIME']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NUMBER_FORMAT_TIME','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_PRODUCT_DESCR_SMALL']))  $temp=intval($_POST['GKS_PRODUCT_DESCR_SMALL']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_PRODUCT_DESCR_SMALL','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_PRODUCT_DESCR_BIG']))  $temp=intval($_POST['GKS_PRODUCT_DESCR_BIG']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_PRODUCT_DESCR_BIG','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_HOTEL_BACKEND']))  $temp=intval($_POST['GKS_HOTEL_BACKEND']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_HOTEL_BACKEND','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
if (!($temp!=0)) {
  $_POST['GKS_HOTEL_RESERVATIONS_ONLINE']='0';
}

$temp=0; if (isset($_POST['GKS_HOTEL_RESERVATIONS_ONLINE']))  $temp=intval($_POST['GKS_HOTEL_RESERVATIONS_ONLINE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_HOTEL_RESERVATIONS_ONLINE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }



$temp=0; if (isset($_POST['GKS_CRM_ENABLE']))  $temp=intval($_POST['GKS_CRM_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_CRM_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
if (!($temp!=0)) {
  $_POST['GKS_CRM_LEADS_ENABLE']='0';
  $_POST['GKS_CRM_TASKS_ENABLE']='0';
  $_POST['GKS_CRM_MACHINE_ENABLE']='0';
}


$temp=0; if (isset($_POST['GKS_CRM_LEADS_ENABLE']))  $temp=intval($_POST['GKS_CRM_LEADS_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_CRM_LEADS_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_CRM_TASKS_ENABLE']))  $temp=intval($_POST['GKS_CRM_TASKS_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_CRM_TASKS_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_CRM_MACHINE_ENABLE']))  $temp=intval($_POST['GKS_CRM_MACHINE_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_CRM_MACHINE_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_WARE_HOUSE_ENABLE']))  $temp=intval($_POST['GKS_WARE_HOUSE_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_WARE_HOUSE_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_PRODUCT_LOTS_SERIALS']))  $temp=intval($_POST['GKS_PRODUCT_LOTS_SERIALS']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_PRODUCT_LOTS_SERIALS','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }




$temp=0; if (isset($_POST['GKS_ORDER_DEFAULT_DELIVERY']))  $temp=intval($_POST['GKS_ORDER_DEFAULT_DELIVERY']);
if ($temp<0) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDER_DEFAULT_DELIVERY','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ORDER_DEFAULT_PAYMENT']))  $temp=intval($_POST['GKS_ORDER_DEFAULT_PAYMENT']);
if ($temp<0) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDER_DEFAULT_PAYMENT','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }




$temp=0; if (isset($_POST['GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK']))  $temp=intval($_POST['GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK']))  $temp=intval($_POST['GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_BASKET_ROUND_DIAFORA_001']))  $temp=intval($_POST['GKS_BASKET_ROUND_DIAFORA_001']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_BASKET_ROUND_DIAFORA_001','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI']))  $temp=intval($_POST['GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI']))  $temp=intval($_POST['GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_INPUT_STEP_AJIA']))  $temp=trim_gks(base64_decode($_POST['GKS_INPUT_STEP_AJIA']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_INPUT_STEP_AJIA','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_INPUT_STEP_POSOTITA']))  $temp=trim_gks(base64_decode($_POST['GKS_INPUT_STEP_POSOTITA']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_INPUT_STEP_POSOTITA','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_INPUT_STEP_POSOSTO']))  $temp=trim_gks(base64_decode($_POST['GKS_INPUT_STEP_POSOSTO']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_INPUT_STEP_POSOSTO','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp=2;  if (isset($_POST['GKS_BASKET_CALC_ITEM_DECIMAL']))  $temp=intval($_POST['GKS_BASKET_CALC_ITEM_DECIMAL']);
if ($temp<0 or $temp>15) $temp=2;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_BASKET_CALC_ITEM_DECIMAL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp=2;  if (isset($_POST['GKS_BASKET_CALC_EKPTOSI_DECIMAL']))  $temp=intval($_POST['GKS_BASKET_CALC_EKPTOSI_DECIMAL']);
if ($temp<0 or $temp>15) $temp=2;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_BASKET_CALC_EKPTOSI_DECIMAL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
  
  

$temp=0; if (isset($_POST['GKS_ORDERS_COL_ITEMPRICE']))  $temp=intval($_POST['GKS_ORDERS_COL_ITEMPRICE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_COL_ITEMPRICE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA']))  $temp=intval($_POST['GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }



$temp=0; if (isset($_POST['GKS_ORDERS_COL_FPA']))  $temp=intval($_POST['GKS_ORDERS_COL_FPA']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_COL_FPA','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ORDERS_SETS']))  $temp=intval($_POST['GKS_ORDERS_SETS']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_SETS','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_ORDERS_SETS_VALS']))  $temp=trim_gks(base64_decode($_POST['GKS_ORDERS_SETS_VALS']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_SETS_VALS','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ORDERS_SHEETS']))  $temp=intval($_POST['GKS_ORDERS_SHEETS']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_SHEETS','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }




$temp=0; if (isset($_POST['GKS_ORDERS_ENABLE']))  $temp=intval($_POST['GKS_ORDERS_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_ORDERS_OCCASION']))  $temp=intval($_POST['GKS_ORDERS_OCCASION']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_OCCASION','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_ORDERS_PRODUCTION']))  $temp=intval($_POST['GKS_ORDERS_PRODUCTION']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ORDERS_PRODUCTION','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp=0; if (isset($_POST['GKS_ACC_ENABLE']))  $temp=intval($_POST['GKS_ACC_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ACC_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }



$temp=0; if (isset($_POST['GKS_ACC_INV_COL_ITEMPRICE']))  $temp=intval($_POST['GKS_ACC_INV_COL_ITEMPRICE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ACC_INV_COL_ITEMPRICE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA']))  $temp=intval($_POST['GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ACC_INV_COL_FPA']))  $temp=intval($_POST['GKS_ACC_INV_COL_FPA']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ACC_INV_COL_FPA','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ACC_INV_EXTRA_OPEN']))  $temp=intval($_POST['GKS_ACC_INV_EXTRA_OPEN']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ACC_INV_EXTRA_OPEN','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ASSETS_ENABLE']))  $temp=intval($_POST['GKS_ASSETS_ENABLE']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ASSETS_ENABLE','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_ERP_APP_MOBILE_VER'])) $temp=trim_gks($_POST['GKS_ERP_APP_MOBILE_VER']);
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ERP_APP_MOBILE_VER','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }







$temp='';  if (isset($_POST['GKS_SITE_EMAIL']))  $temp=trim_gks(base64_decode($_POST['GKS_SITE_EMAIL']));
if ($temp=='') $temp='info@'.$_SERVER['HTTP_HOST'];
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SITE_EMAIL','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$temp='';  if (isset($_POST['GKS_EMAIL_HOST']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_HOST']));
if ($temp=='') $temp=$_SERVER['HTTP_HOST'];
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_HOST','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_PORT']))  $temp=intval($_POST['GKS_EMAIL_PORT']);
if ($temp<=0) $temp=587;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_PORT','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp=0; if (isset($_POST['GKS_EMAIL_SMTPAUTH']))  $temp=intval($_POST['GKS_EMAIL_SMTPAUTH']);
if ($temp!=0 and $temp!=1) $temp=0;
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_SMTPAUTH','".($temp!=0 ? 'true' : 'false')."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_USERNAME']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_USERNAME']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_USERNAME','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_PASSWORD']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_PASSWORD']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_PASSWORD','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_BCC1']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_BCC1']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_BCC1','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_BCC2']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_BCC2']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_BCC2','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_EMAIL_BCC3']))  $temp=trim_gks(base64_decode($_POST['GKS_EMAIL_BCC3']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_EMAIL_BCC3','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_SMS_SENDER']))  $temp=trim_gks(base64_decode($_POST['GKS_SMS_SENDER']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SMS_SENDER','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_SMS_TOKEN']))  $temp=trim_gks(base64_decode($_POST['GKS_SMS_TOKEN']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SMS_TOKEN','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_VIBER_URI']))  $temp=trim_gks(base64_decode($_POST['GKS_VIBER_URI']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_VIBER_URI','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_VIBER_TOKEN']))  $temp=trim_gks(base64_decode($_POST['GKS_VIBER_TOKEN']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_VIBER_TOKEN','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }





$temp='';  if (isset($_POST['GKS_GOOGLE_MAPS_API_KEY']))  $temp=trim_gks(base64_decode($_POST['GKS_GOOGLE_MAPS_API_KEY']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_GOOGLE_MAPS_API_KEY','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp='';  if (isset($_POST['GKS_GOOGLE_MAPS_API_KEY_SERVER']))  $temp=trim_gks(base64_decode($_POST['GKS_GOOGLE_MAPS_API_KEY_SERVER']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_GOOGLE_MAPS_API_KEY_SERVER','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
  
  
  
$temp='';  if (isset($_POST['GKS_AWS_BUCKET']))  $temp=trim_gks(base64_decode($_POST['GKS_AWS_BUCKET']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AWS_BUCKET','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp='';  if (isset($_POST['GKS_AWS_KEY']))  $temp=trim_gks(base64_decode($_POST['GKS_AWS_KEY']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AWS_KEY','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp='';  if (isset($_POST['GKS_AWS_SECRET']))  $temp=trim_gks(base64_decode($_POST['GKS_AWS_SECRET']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AWS_SECRET','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
$temp='';  if (isset($_POST['GKS_AWS_FOLDER']))  $temp=trim_gks(base64_decode($_POST['GKS_AWS_FOLDER']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AWS_FOLDER','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_SEND_ANYWHERE_API_KEY']))  $temp=trim_gks(base64_decode($_POST['GKS_SEND_ANYWHERE_API_KEY']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_SEND_ANYWHERE_API_KEY','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_AADE_MYDATA_SANDBOX_AFM']))  $temp=trim_gks(base64_decode($_POST['GKS_AADE_MYDATA_SANDBOX_AFM']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AADE_MYDATA_SANDBOX_AFM','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_AADE_MYDATA_SANDBOX_BRANCE']))  $temp=intval($_POST['GKS_AADE_MYDATA_SANDBOX_BRANCE']);
if ($temp<0) $temp=0;

$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AADE_MYDATA_SANDBOX_BRANCE','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_AADE_MYDATA_SANDBOX_USER_ID']))  $temp=trim_gks(base64_decode($_POST['GKS_AADE_MYDATA_SANDBOX_USER_ID']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AADE_MYDATA_SANDBOX_USER_ID','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$temp='';  if (isset($_POST['GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY']))  $temp=trim_gks(base64_decode($_POST['GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY']));
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }


$custom_css_global = trim_gks(base64_decode($_POST['custom_css_global']));
//echo '<pre>';echo  $custom_css_global;die(); 
$sql="replace into gks_settings (mykey,myvalue) values ('custom_css_global','".$db_link->escape_string($custom_css_global)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

$ret_run=gks_sociallinks_item_save($_POST,'gks_settings',1);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}

$temp='';  if (isset($_POST['GKS_LANG_DEFAULT']))  $temp=trim_gks(base64_decode($_POST['GKS_LANG_DEFAULT']));
if ($temp=='') {
  debug_mail(false,'set GKS_LANG_DEFAULT','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Προεπιλεγμένη γλώσσα δεδομένων')));
  echo json_encode($return); die();}
if ($temp!=$GKS_LANG_DEFAULT)  {
  $rrr=gks_lang_data_swap($GKS_LANG_DEFAULT,$temp);
  if ($rrr['success']==false) {
    gks_cache_update_menu_version(-1);
    
    $return = array('success' => false, 'message' => base64_encode($rrr['message']));
    echo json_encode($return); die();
  
  
  }
  
}


gks_cache_update_menu_version(-1);


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
