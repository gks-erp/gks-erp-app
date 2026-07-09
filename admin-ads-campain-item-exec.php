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


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση Καμπάνιας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_ads_campain',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_ads_campain where id_ads_campain = ".$id;
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

$ads_campain_name=''; if (isset($_POST['ads_campain_name'])) $ads_campain_name=trim_gks(base64_decode($_POST['ads_campain_name']));
$ads_campain_descr=''; if (isset($_POST['ads_campain_descr'])) $ads_campain_descr=trim_gks(base64_decode($_POST['ads_campain_descr']));
$ads_campain_sortorder=0; if (isset($_POST['ads_campain_sortorder'])) $ads_campain_sortorder=intval($_POST['ads_campain_sortorder']);
$ads_campain_disabled=0; if (isset($_POST['ads_campain_disabled'])) $ads_campain_disabled=intval($_POST['ads_campain_disabled']);


if ($ads_campain_name=='') {debug_mail(false,'emptyl',           gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }





$sql="select * from gks_ads_campain where ads_campain_name like '".$db_link->escape_string($ads_campain_name)."' and id_ads_campain<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη').
  '<br><a href="admin-ads-campain-item.php?id='.$row['id_ads_campain'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  $message=str_replace('[1]',$ads_campain_descr,$message);
  debug_mail(false,'monada metrisis exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_ads_campain');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_ads_campain (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-ads-campain-item.php?id='.$id); 
}

$sql="update gks_ads_campain set 
ads_campain_descr='".$db_link->escape_string($ads_campain_descr)."',
ads_campain_name='".$db_link->escape_string($ads_campain_name)."',
ads_campain_sortorder=".$ads_campain_sortorder.",
ads_campain_disabled=".$ads_campain_disabled.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_ads_campain = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

