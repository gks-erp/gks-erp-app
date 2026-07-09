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

$my_page_title=gks_lang('Αποθήκευση Είδους Παραστατικών').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou = ".$id;
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


$eidos_parastatikou_descr=''; if (isset($_POST['eidos_parastatikou_descr'])) $eidos_parastatikou_descr=trim_gks(base64_decode($_POST['eidos_parastatikou_descr']));
$eidos_parastatikou_aade_code=''; if (isset($_POST['eidos_parastatikou_aade_code'])) $eidos_parastatikou_aade_code=trim_gks(base64_decode($_POST['eidos_parastatikou_aade_code']));
$sortorder=''; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);
$aade_disable=''; if (isset($_POST['aade_disable'])) $aade_disable=intval($_POST['aade_disable']);
$peppol_code=0; if (isset($_POST['peppol_code'])) $peppol_code=intval($_POST['peppol_code']);

$parent_id=0; if (isset($_POST['parent_id'])) $parent_id=intval($_POST['parent_id']);
$eidos_parastatikou_type_id=0; if (isset($_POST['eidos_parastatikou_type_id'])) $eidos_parastatikou_type_id=intval($_POST['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=0; if (isset($_POST['eidos_parastatikou_need_prev'])) $eidos_parastatikou_need_prev=intval($_POST['eidos_parastatikou_need_prev']);
$eidos_parastatikou_has_fpa=0; if (isset($_POST['eidos_parastatikou_has_fpa'])) $eidos_parastatikou_has_fpa=intval($_POST['eidos_parastatikou_has_fpa']);
$eidos_parastatikou_has_posotita=0; if (isset($_POST['eidos_parastatikou_has_posotita'])) $eidos_parastatikou_has_posotita=intval($_POST['eidos_parastatikou_has_posotita']);
$eidos_parastatikou_has_othertaxes_wh=0; if (isset($_POST['eidos_parastatikou_has_othertaxes_wh'])) $eidos_parastatikou_has_othertaxes_wh=intval($_POST['eidos_parastatikou_has_othertaxes_wh']);
$eidos_parastatikou_has_othertaxes_ot=0; if (isset($_POST['eidos_parastatikou_has_othertaxes_ot'])) $eidos_parastatikou_has_othertaxes_ot=intval($_POST['eidos_parastatikou_has_othertaxes_ot']);
$eidos_parastatikou_has_othertaxes_sd=0; if (isset($_POST['eidos_parastatikou_has_othertaxes_sd'])) $eidos_parastatikou_has_othertaxes_sd=intval($_POST['eidos_parastatikou_has_othertaxes_sd']);
$eidos_parastatikou_has_othertaxes_fe=0; if (isset($_POST['eidos_parastatikou_has_othertaxes_fe'])) $eidos_parastatikou_has_othertaxes_fe=intval($_POST['eidos_parastatikou_has_othertaxes_fe']);
$eidos_parastatikou_has_othertaxes_dd=0; if (isset($_POST['eidos_parastatikou_has_othertaxes_dd'])) $eidos_parastatikou_has_othertaxes_dd=intval($_POST['eidos_parastatikou_has_othertaxes_dd']);
$eidos_parastatikou_has_othertaxes=[];
if ($eidos_parastatikou_has_othertaxes_wh==1) $eidos_parastatikou_has_othertaxes[]='wh';
if ($eidos_parastatikou_has_othertaxes_ot==1) $eidos_parastatikou_has_othertaxes[]='ot';
if ($eidos_parastatikou_has_othertaxes_sd==1) $eidos_parastatikou_has_othertaxes[]='sd';
if ($eidos_parastatikou_has_othertaxes_fe==1) $eidos_parastatikou_has_othertaxes[]='fe';
if ($eidos_parastatikou_has_othertaxes_dd==1) $eidos_parastatikou_has_othertaxes[]='dd';
$eidos_parastatikou_has_othertaxes=implode(',',$eidos_parastatikou_has_othertaxes);
$eidos_parastatikou_has_esoda=0; if (isset($_POST['eidos_parastatikou_has_esoda'])) $eidos_parastatikou_has_esoda=intval($_POST['eidos_parastatikou_has_esoda']);
$eidos_parastatikou_has_eksoda=0; if (isset($_POST['eidos_parastatikou_has_eksoda'])) $eidos_parastatikou_has_eksoda=intval($_POST['eidos_parastatikou_has_eksoda']);
$eidos_parastatikou_need_afm=0; if (isset($_POST['eidos_parastatikou_need_afm'])) $eidos_parastatikou_need_afm=intval($_POST['eidos_parastatikou_need_afm']);
$eidos_parastatikou_balance_pros=0; if (isset($_POST['eidos_parastatikou_balance_pros'])) $eidos_parastatikou_balance_pros=intval($_POST['eidos_parastatikou_balance_pros']);
$eidos_parastatikou_stock_pros=0; if (isset($_POST['eidos_parastatikou_stock_pros'])) $eidos_parastatikou_stock_pros=intval($_POST['eidos_parastatikou_stock_pros']);
$eidos_parastatikou_whi_type_id=0; if (isset($_POST['eidos_parastatikou_whi_type_id'])) $eidos_parastatikou_whi_type_id=intval($_POST['eidos_parastatikou_whi_type_id']);
$eidos_parastatikou_other_entity=0; if (isset($_POST['eidos_parastatikou_other_entity'])) $eidos_parastatikou_other_entity=intval($_POST['eidos_parastatikou_other_entity']);
$eidos_parastatikou_correlated_invoices=0; if (isset($_POST['eidos_parastatikou_correlated_invoices'])) $eidos_parastatikou_correlated_invoices=intval($_POST['eidos_parastatikou_correlated_invoices']);
$eidos_parastatikou_multiple_connected_marks=0; if (isset($_POST['eidos_parastatikou_multiple_connected_marks'])) $eidos_parastatikou_multiple_connected_marks=intval($_POST['eidos_parastatikou_multiple_connected_marks']);
$eidos_parastatikou_packings_declarations=0; if (isset($_POST['eidos_parastatikou_packings_declarations'])) $eidos_parastatikou_packings_declarations=intval($_POST['eidos_parastatikou_packings_declarations']);
$is_selectable=0; if (isset($_POST['is_selectable'])) $is_selectable=intval($_POST['is_selectable']);
$credit_acc_eidos_parastatikou_id=0; if (isset($_POST['credit_acc_eidos_parastatikou_id'])) $credit_acc_eidos_parastatikou_id=intval($_POST['credit_acc_eidos_parastatikou_id']);
$import_apo_allon=''; if (isset($_POST['import_apo_allon'])) $import_apo_allon=trim_gks(base64_decode($_POST['import_apo_allon']));






if ($eidos_parastatikou_descr=='') {debug_mail(false,'emptyl',gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }



$redirect='';
if ($id==-1) {
  $sql="insert into gks_acc_eidi_parastatikon (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-aade-eidi-parastatikon-item.php?id='.$id); 
}



$sql="update gks_acc_eidi_parastatikon set 
eidos_parastatikou_descr='".$db_link->escape_string($eidos_parastatikou_descr)."',
eidos_parastatikou_aade_code='".$db_link->escape_string($eidos_parastatikou_aade_code)."',
sortorder=".$sortorder.",
aade_disable=".$aade_disable.",
peppol_code=".$peppol_code.",

parent_id=".$parent_id.",
eidos_parastatikou_type_id=".$eidos_parastatikou_type_id.",

eidos_parastatikou_need_prev=".$eidos_parastatikou_need_prev.",
eidos_parastatikou_has_fpa=".$eidos_parastatikou_has_fpa.",
eidos_parastatikou_has_posotita=".$eidos_parastatikou_has_posotita.",
eidos_parastatikou_has_othertaxes='".$db_link->escape_string($eidos_parastatikou_has_othertaxes)."',
eidos_parastatikou_has_esoda=".$eidos_parastatikou_has_esoda.",
eidos_parastatikou_has_eksoda=".$eidos_parastatikou_has_eksoda.",
eidos_parastatikou_need_afm=".$eidos_parastatikou_need_afm.",
eidos_parastatikou_balance_pros=".$eidos_parastatikou_balance_pros.",
eidos_parastatikou_stock_pros=".$eidos_parastatikou_stock_pros.",
eidos_parastatikou_whi_type_id=".$eidos_parastatikou_whi_type_id.",
eidos_parastatikou_other_entity=".$eidos_parastatikou_other_entity.",
eidos_parastatikou_correlated_invoices=".$eidos_parastatikou_correlated_invoices.",
eidos_parastatikou_multiple_connected_marks=".$eidos_parastatikou_multiple_connected_marks.",
eidos_parastatikou_packings_declarations=".$eidos_parastatikou_packings_declarations.",
is_selectable=".$is_selectable.",
credit_acc_eidos_parastatikou_id=".$credit_acc_eidos_parastatikou_id.",
import_apo_allon='".$db_link->escape_string($import_apo_allon)."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_acc_eidos_parastatikou = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


gks_lang_data_obj_save_exec_php('gks_acc_eidi_parastatikon',$id);
 
gks_cache_update_menu_version(-1);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

