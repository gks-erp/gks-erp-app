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

$my_page_title=gks_lang('Αποθήκευση Απογραφή Παγίων').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_whi_mov',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_assets_whi_mov where id_assets_whi_mov = ".$id;
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


$assets_whi_mov_status=''; if (isset($_POST['assets_whi_mov_status'])) $assets_whi_mov_status=trim_gks($_POST['assets_whi_mov_status']);
if ($assets_whi_mov_status!='00draft' and $assets_whi_mov_status!='99complete') {
  debug_mail(false,'assets_whi_mov_status',$assets_whi_mov_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την κατάσταση')));
  echo json_encode($return); die(); }

if ($_POST['mydate'] == '__/__/____ __:__') $_POST['mydate']='';
$mydate=trim_gks(stripslashes(urldecode($_POST['mydate'])));
if ($mydate!='') {
  $mydate = mystrtodb($mydate);
} else {
  debug_mail(false,'mydate',$mydate);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία')));
  echo json_encode($return); die();}

$warehouse_id = intval($_POST['warehouse_id']);
if ($warehouse_id<=0) {
  debug_mail(false,'warehouse_id',$warehouse_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την αποθήκη')));
  echo json_encode($return); die(); }  

$whi_mov_sxolio='';if (isset($_POST['whi_mov_sxolio'])) $whi_mov_sxolio=trim_gks(base64_decode($_POST['whi_mov_sxolio']));

$assets_str=''; if (isset($_POST['assets_str'])) $assets_str=trim_gks(base64_decode($_POST['assets_str']));




$assets_array = json_decode($assets_str, true);
if ($assets_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['assets_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  
if (count($assets_array)==0 and ($assets_whi_mov_status=='99complete')) {
  debug_mail(false,'assets_array zero',print_r($assets_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει πάγια')));
  echo json_encode($return); die();}



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_assets_whi_mov');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_assets_whi_mov (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-assets-whi-mov-item.php?id='.$id); 
}


  
$sql="update gks_assets_whi_mov set 
mydate =". ($mydate=='' ? 'NOW()' : "'".$db_link->escape_string($mydate)."'"). ",
warehouse_id=".$warehouse_id.",
assets_whi_mov_status='".$db_link->escape_string($assets_whi_mov_status)."',
whi_mov_sxolio='".$db_link->escape_string($whi_mov_sxolio)."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_assets_whi_mov = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$gks_custom_save_run=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

$recs_array=array();
foreach ($assets_array as $value) {
  $rec=intval($value['rec']);
  $asset_id=intval($value['asset_id']);
  $found='null';
  if (trim_gks($value['found'])=='') $found='null';
  else if ($value['found']=='1') $found='1';
  else if ($value['found']=='0') $found='0';
  
  $theori='null';
  if (trim_gks($value['theori'])=='') $theori='null';
  else if ($value['theori']=='1') $theori='1';
  else if ($value['theori']=='0') $theori='0';
  
  $sxolio=trim_gks($value['sxolio']);
  
  if ($rec<=0) {
    $sql="insert into gks_assets_whi_mov_assets (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      assets_whi_mov_id,asset_id,posotita_theori,posotita_found,posotita_sxolio
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",".$asset_id.",".$theori.",".$found.",'".$db_link->escape_string($sxolio)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    $recs_array[]=$db_link->insert_id;
  } else {
    $sql="update gks_assets_whi_mov_assets set 
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($sxolio)."',
    asset_id=".$asset_id.",
    posotita_theori=".$theori.",
    posotita_found=".$found.",
    posotita_sxolio='".$db_link->escape_string($sxolio)."'
    where id_assets_whi_mov_assets=".$rec." and assets_whi_mov_id=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    $recs_array[]=$rec;
    
    
  }
  
}

$sql="delete from gks_assets_whi_mov_assets where assets_whi_mov_id=".$id;
if (count($recs_array)>0) $sql.=" and id_assets_whi_mov_assets not in (".implode(',',$recs_array).")";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();} 

  


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

