<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();
//die();

//$dev_page_starttime11=microtime(true);

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Service Παγίου').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_assets_service');


$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  
  $sql_rec ="SELECT gks_assets_service.*, gks_assets.asset_code, gks_assets.asset_title, gks_assets.asset_serialnumber, gks_assets.asset_type, 
  gks_assets_service_reasons.reasons_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_warehouses.warehouse_name, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_add,
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM ((((gks_assets_service 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons) 
  LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_assets_service.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_assets_service.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where gks_assets_service.id_assets_service = ".$id;
  
  $sql_rec.=" limit 1";
  $result = $db_link->query($sql_rec);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_rec);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_old = $result->fetch_assoc();
}


$mydate_send=''; if (isset($_POST['mydate_send'])) {
  $mydate_send = trim_gks(base64_decode($_POST['mydate_send'])); 
  if ($mydate_send == '__/__/____ __:__') $mydate_send=''; 
  if ($mydate_send!='') $mydate_send = mystrtodb($mydate_send);}
$asset_id=0; if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
$warehouse_id=0; if (isset($_POST['warehouse_id'])) $warehouse_id=intval($_POST['warehouse_id']);
$reason_id=0; if (isset($_POST['reason_id'])) $reason_id=intval($_POST['reason_id']);
$aitiolog=''; if (isset($_POST['aitiolog'])) $aitiolog=trim_gks(base64_decode($_POST['aitiolog']));
$mixanikos_id=0; if (isset($_POST['mixanikos_id'])) $mixanikos_id=intval($_POST['mixanikos_id']);
$mydate_return=''; if (isset($_POST['mydate_return'])) {
  $mydate_return = trim_gks(base64_decode($_POST['mydate_return'])); 
  if ($mydate_return == '__/__/____ __:__') $mydate_return=''; 
  if ($mydate_return!='') $mydate_return = mystrtodb($mydate_return);}
$aitiolog2=''; if (isset($_POST['aitiolog2'])) $aitiolog2=trim_gks(base64_decode($_POST['aitiolog2']));
$ajia=0; if (isset($_POST['ajia'])) $ajia=floatval(str_replace(',','.', $_POST['ajia']));
$isconfirm=0; if (isset($_POST['isconfirm'])) $isconfirm=intval($_POST['isconfirm']);

if ($mydate_send=='') {
  debug_mail(false,'mydate_send empty',$mydate_send);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Αποστολής')));
  echo json_encode($return); die();      
}
if (strtotime($mydate_send) < time()-2000*24*60*60) {
  debug_mail(false,'mydate_send small',$mydate_send);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πολύ μικρή ημερομηνία αποστολής')));
  echo json_encode($return); die();      
}

if ($asset_id<=0) {
  debug_mail(false,'asset_id zero',$asset_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το πάγιο')));
  echo json_encode($return); die();      
}
$sql="select id_asset from gks_assets where id_asset=".$asset_id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {debug_mail(false,'asset not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πάγιο')));
  echo json_encode($return); die();}  

$sql="select id_assets_service from gks_assets_service where id_assets_service<>".$id." and asset_id=".$asset_id." and mydate_return is null limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {debug_mail(false,'asset is on service');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το πάγιο είναι ήδη για Service')));
  echo json_encode($return); die();}  

$sql="select id_asset, asset_last_warehouse_id,asset_type from gks_assets where asset_disable=0 and asset_last_user_id=0 and asset_last_mixani_id=0 and id_asset=".$asset_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {debug_mail(false,'asset is in ...', $sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Είτε δεν βρέθηκε το πάγιο').'<br>'.
    gks_lang('Είτε το πάγιο είναι ανενεργό').'<br>'.
    gks_lang('Είτε το πάγιο έχει δοθεί σε κάποιον συνεργάτη').'<br>'.
    gks_lang('Είτε το πάγιο ανήκει σε κάποιο σετ')));
  echo json_encode($return); die();}  
  
$row = $result->fetch_assoc();
$asset_type=$row['asset_type'];

if ($warehouse_id<=0) {
  debug_mail(false,'warehouse_id zero',$warehouse_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την αποθήκη')));
  echo json_encode($return); die();      
}
$sql="select id_warehouse from gks_warehouses where id_warehouse=".$warehouse_id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {debug_mail(false,'warehouse_id not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη')));
  echo json_encode($return); die();}  

//if ($reason_id<=0) {
//  debug_mail(false,'reason_id not found',$reason_id);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Αιτία')));
//  echo json_encode($return); die();      
//}


if ($mixanikos_id<=0) {
  debug_mail(false,'mixanikos_id zero',$mixanikos_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τεχνικό')));
  echo json_encode($return); die();      
}


if ($mydate_return!='') {
  if (strtotime($mydate_send) > strtotime($mydate_return)) {
    debug_mail(false,'date_start > date_end',$mydate_send.' -- '.$mydate_return);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Επιστροφής πρέπει να είναι μεγαλύτερη από την Ημερομηνία Αποστολής')));
    echo json_encode($return); die();     
  }

  if (strtotime($mydate_return) > time()+1*24*60*60) {
    debug_mail(false,'mydate_return big',$mydate_return);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Πολύ μεγάλη ημερομηνία Επιστροφής')));
    echo json_encode($return); die();      
  }
}
if ($isconfirm !=0) {
  if ($mydate_return=='') {
    debug_mail(false,'mydate_return zero',$mydate_return);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Επιστροφής')));
    echo json_encode($return); die();      
  }  
 
}




$redirect='';
if ($id==-1) {
  $sql="insert into gks_assets_service (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  

  $redirect=base64_encode('admin-assets-service-item.php?id='.$id);   
}


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_assets_service');

$sql="update gks_assets_service set 
asset_id=".$asset_id.",
mydate_send= ". ($mydate_send=='' ? 'null' : "'".$db_link->escape_string($mydate_send)."'"). ",
mydate_return= ". ($mydate_return=='' ? 'null' : "'".$db_link->escape_string($mydate_return)."'"). ",
reason_id=".$reason_id.",
aitiolog='".$db_link->escape_string($aitiolog)."',
aitiolog2='".$db_link->escape_string($aitiolog2)."',
mixanikos_id=".$mixanikos_id.",
warehouse_id=".$warehouse_id.",
asset_id=".$asset_id.",
ajia='".number_format($ajia,16,'.','')."',
user_id_edit=".$my_wp_user_id .",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_assets_service = ".$id." limit 1";


$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_run=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

if ($isconfirm != 0) {
    $sql="update gks_assets_service set 
    isconfirm=".$isconfirm."
    where id_assets_service = ".$id." limit 1";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
}


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







