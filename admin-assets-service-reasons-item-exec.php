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

$my_page_title=gks_lang('Αποθήκευση Αιτίας Service Παγίου').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service_reasons',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




if ($id>0) {
  $sql ="SELECT * FROM gks_assets_service_reasons where id_assets_service_reasons = ".$id;
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



$reasons_descr=''; if (isset($_POST['reasons_descr'])) $reasons_descr=trim_gks(base64_decode($_POST['reasons_descr']));
$assets_types=trim_gks(base64_decode($_POST['assets_types']));
$assets_service_reason_sortorder=0; if (isset($_POST['assets_service_reason_sortorder'])) $assets_service_reason_sortorder=intval(stripslashes(urldecode($_POST['assets_service_reason_sortorder'])));
$assets_service_reason_disable=0; if (isset($_POST['assets_service_reason_disable'])) $assets_service_reason_disable=intval($_POST['assets_service_reason_disable']);




if ($reasons_descr=='') {debug_mail(false,'reasons_descr',$reasons_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }

$assets_types_array=array();
if ($assets_types!='') {
  $temp =explode(']][[', $assets_types);
  $assets_types=array();
  foreach ($temp as $value) {
   $value=trim_gks($value);
   if ($value!='') $assets_types[] = "'".$value."'";
  } 
  if (count($assets_types) >0) {
    $sql_lista="SELECT id_asset_type from gks_assets_type where asset_type_descr in (".implode(',',$assets_types).')';
    $result_lista = $db_link->query($sql_lista);        
    if (!$result_lista) {debug_mail(false,'error sql',$sql_lista);die('sql error');}
    while ($row_lista = $result_lista->fetch_assoc()) {
      $assets_types_array[]=$row_lista['id_asset_type'];
    }
  }
}

$sql="select * from gks_assets_service_reasons where reasons_descr like '".$db_link->escape_string($reasons_descr)."' and id_assets_service_reasons<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η αιτία με περιγραφή <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$reasons_descr,$message);
  $message.='<br><a href="admin-assets-service-reasons-item.php?id='.$row['id_assets_service_reasons'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'admin-assets-service-reasons exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}






$redirect='';
if ($id==-1) {
  $sql="insert into gks_assets_service_reasons (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-assets-service-reasons-item.php?id='.$id); 
}

  
$sql="update gks_assets_service_reasons set 
reasons_descr='".$db_link->escape_string($reasons_descr)."',
assets_service_reason_sortorder=".$assets_service_reason_sortorder.",
assets_service_reason_disable=".$assets_service_reason_disable.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_assets_service_reasons = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

if (count($assets_types_array)==0) {
  $sql="delete from gks_assets_service_reasons_types where reasons_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql);  $return = array('success' => false, 'message' => base64_encode('sql error'));  echo json_encode($return); die(); }
} else {
  $sql="delete from gks_assets_service_reasons_types where reasons_id=".$id." and type_id not in (".implode(',',$assets_types_array).")";
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql);  $return = array('success' => false, 'message' => base64_encode('sql error'));  echo json_encode($return); die(); }
  $sql="insert into gks_assets_service_reasons_types 
  (type_id,reasons_id) 
  SELECT id_asset_type, ".$id." as reasons_id
  FROM gks_assets_type LEFT JOIN (
    SELECT type_id FROM gks_assets_service_reasons_types WHERE reasons_id=".$id."
  )  AS myexistrecs ON gks_assets_type.id_asset_type = myexistrecs.type_id
  WHERE gks_assets_type.id_asset_type In (".implode(',',$assets_types_array).") AND myexistrecs.type_id Is Null;";
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql);  $return = array('success' => false, 'message' => base64_encode('sql error'));  echo json_encode($return); die(); }
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

