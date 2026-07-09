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

$my_page_title=gks_lang('Αποθήκευση Τέλους').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_aade_katigoria_telon where id_aade_katigoria_telon = ".$id;
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


$aade_katigoria_telon_descr=''; if (isset($_POST['aade_katigoria_telon_descr'])) $aade_katigoria_telon_descr=trim_gks(base64_decode($_POST['aade_katigoria_telon_descr']));
$aade_katigoria_telon_code=0; if (isset($_POST['aade_katigoria_telon_code'])) $aade_katigoria_telon_code=intval($_POST['aade_katigoria_telon_code']);
$aade_katigoria_telon_type=''; if (isset($_POST['aade_katigoria_telon_type'])) $aade_katigoria_telon_type=trim_gks(base64_decode($_POST['aade_katigoria_telon_type']));
$aade_katigoria_telon_pososto=''; if (isset($_POST['aade_katigoria_telon_pososto'])) $aade_katigoria_telon_pososto=trim_gks(base64_decode($_POST['aade_katigoria_telon_pososto']));
if ($aade_katigoria_telon_pososto!='') $aade_katigoria_telon_pososto=floatval($aade_katigoria_telon_pososto);
$aade_katigoria_telon_poso_fn=''; if (isset($_POST['aade_katigoria_telon_poso_fn'])) $aade_katigoria_telon_poso_fn=trim_gks(base64_decode($_POST['aade_katigoria_telon_poso_fn']));
$aade_katigoria_telon_poso_fix=''; if (isset($_POST['aade_katigoria_telon_poso_fix'])) $aade_katigoria_telon_poso_fix=trim_gks(base64_decode($_POST['aade_katigoria_telon_poso_fix']));
if ($aade_katigoria_telon_poso_fix!='') $aade_katigoria_telon_poso_fix=floatval($aade_katigoria_telon_poso_fix);
$sortorder=''; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);
$aade_disable=''; if (isset($_POST['aade_disable'])) $aade_disable=intval($_POST['aade_disable']);
$telon_peppol_code=''; if (isset($_POST['telon_peppol_code'])) $telon_peppol_code=trim_gks(base64_decode($_POST['telon_peppol_code']));


if ($aade_katigoria_telon_descr=='') {debug_mail(false,'emptyl',gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }



$redirect='';
if ($id==-1) {
  $sql="insert into gks_aade_katigoria_telon (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-aade-katigoria-telon-item.php?id='.$id); 
}



$sql="update gks_aade_katigoria_telon set 
aade_katigoria_telon_descr='".$db_link->escape_string($aade_katigoria_telon_descr)."',
aade_katigoria_telon_code=".$aade_katigoria_telon_code.",
aade_katigoria_telon_type='".$db_link->escape_string($aade_katigoria_telon_type)."',
aade_katigoria_telon_pososto=".($aade_katigoria_telon_pososto==='' ? 'null' : $aade_katigoria_telon_pososto).",
aade_katigoria_telon_poso_fn='".$db_link->escape_string($aade_katigoria_telon_poso_fn)."',
aade_katigoria_telon_poso_fix=".($aade_katigoria_telon_poso_fix==='' ? 'null' : $aade_katigoria_telon_poso_fix).",
sortorder=".$sortorder.",
aade_disable=".$aade_disable.",
telon_peppol_code='".$db_link->escape_string($telon_peppol_code)."',
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_aade_katigoria_telon = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


gks_lang_data_obj_save_exec_php('gks_aade_katigoria_telon',$id);
 
gks_cache_update_menu_version(-1);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

