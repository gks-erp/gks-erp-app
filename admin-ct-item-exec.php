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


$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$ctid=0;if (isset($_GET['ctid'])) $ctid=intval($_GET['ctid']); 
if ($ctid < 10000) {
  debug_mail(false,'not set ctid. ('.$ctid.')','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')'));
  echo json_encode($return); die();}



db_open();
$sql_ct="select * 
from gks_custom_table 
where custom_table_disabled=0
and id_custom_table=".$ctid;
$result_ct = $db_link->query($sql_ct);        
if (!$result_ct) {debug_mail(false,'error sql',$sql_ct);die('sql error');}
if ($result_ct->num_rows!=1) {
  debug_mail(false,'record not found',$sql_ct);die('custom table not found ('.$ctid.')'); 
  $return = array('success' => false, 'message' => base64_encode('custom table not found ('.$ctid.')'));
  echo json_encode($return); die();}
$row_ct = $result_ct->fetch_assoc();
$custom_table_descr=$row_ct['custom_table_descr'];
$custom_table_name=$row_ct['custom_table_name'];
$custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
$field_name_id_parent=$row_ct['field_name_id_parent'];
$field_name_id_current=$row_ct['field_name_id_current'];
$field_id='id_gks_customt_gks_ct_'.$ctid;

$my_page_title=gks_lang('Αποθήκευση').' '.$custom_table_descr.' id: '.$id;

stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $custom_table_name,($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$is_new_rec=false;
if ($id>0) {
  $sql_row ="SELECT * FROM ".$custom_table_name_real." where ".$field_id." = ".$id;
  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row_old = $result->fetch_assoc();
  
  $gks_custom_prepare=gks_custom_table_item_prepare($custom_table_name,['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
}


//echo '<pre>dddddd';die();

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,$custom_table_name);



$redirect='';
if ($id==-1) {
  $is_new_rec=true;
  $sql="insert into ".$custom_table_name_real." (cf_mydate_add,cf_user_id_add,cf_myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  
  $sql="update ".$custom_table_name_real." set ".$field_name_id_current."=".$id." where ".$field_id." = ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  
  $redirect=base64_encode('admin-ct-item.php?ctid='.$ctid.'&id='.$id); 

  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into ".$custom_table_name_real."_log (
  gks_customt_gks_".$field_name_id_current.", add_date,user_id,sxolio
  ) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
    
}

$sql="update ".$custom_table_name_real." set 
cf_user_id_edit=".$my_wp_user_id.",
cf_mydate_edit=now(),
cf_myip='".$db_link->escape_string($gkIP)."'
where ".$field_id." = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

if ($is_new_rec == false) {

  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_new = $result->fetch_assoc();
    
  $sxolio_log='';
  
  $gks_custom_prepare=gks_custom_table_item_prepare($custom_table_name,['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;

  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';

  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into ".$custom_table_name_real."_log (
    gks_customt_gks_".$field_name_id_current.", add_date,user_id,sxolio
    ) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    
    //$return = array('success' => false, 'message' => base64_encode($sql));
    //echo json_encode($return); die();  
     
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}  
      
  } 
    
  //echo '<pre>'.$sxolio_log;die();
}


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

