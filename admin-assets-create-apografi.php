<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Δημιουργία Απογραφής Παγίων');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_whi_mov','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$newaddid=''; if (isset($_POST['newaddid'])) $newaddid=trim_gks($_POST['newaddid']);
if ($newaddid=='') {
  debug_mail(false,'assets not found 01',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν πάγια').'<br>'.gks_lang('Ανανεώστε την σελίδα των παγίων')));
  echo json_encode($return); die();}
//echo '<pre>'.$newaddid;die();

$temp_sql_file=GKS_SITE_PATH.'tmp/'.$newaddid.'.sql';
if (file_exists($temp_sql_file)==false) {
  debug_mail(false,'assets not found 02',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν πάγια').'<br>'.gks_lang('Ανανεώστε την σελίδα των παγίων')));
  echo json_encode($return); die();}
  
//echo '<pre>'.$temp_sql_file;die();


$sql=file_get_contents($temp_sql_file);
//echo '<pre>';echo $sql;die();

if ($sql=='') {
  debug_mail(false,'assets not found 03',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν πάγια').'<br>'.gks_lang('Ανανεώστε την σελίδα των παγίων')));
  echo json_encode($return); die();}  
  



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
$asset_ids=array();
$warehouse_id=0;
while ($row = $result->fetch_assoc()) {
  $asset_ids[]=$row;
  if ($warehouse_id==0) $warehouse_id=intval($row['asset_last_warehouse_id']);
}

if (count($asset_ids)<=0) {
  debug_mail(false,'assets not found 04',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν πάγια').'<br>'.gks_lang('Αλλάξτε τα φίλτρα')));
  echo json_encode($return); die();} 
  
//echo '<pre>';print_r($asset_ids);die();

$sql="insert into gks_assets_whi_mov (mydate_add, mydate_edit, user_id_add, user_id_edit, myip,warehouse_id) 
values (NOW(),NOW(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$warehouse_id.")";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$id = $db_link->insert_id;
$redirect=base64_encode('admin-assets-whi-mov-item.php?id='.$id); 
  

$sql="update gks_assets_whi_mov set 
mydate =NOW(),
warehouse_id=".$warehouse_id.",
assets_whi_mov_status='00draft',
whi_mov_sxolio='',
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_assets_whi_mov=".$id." limit 1";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

foreach ($asset_ids as $row) {
  
  $theori='null';
  if ($row['asset_last_warehouse_id']==$warehouse_id and $row['is_fotografou']==0) $theori='1'; else $theori='0';
  $found='null';

  $sql="insert into gks_assets_whi_mov_assets (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    assets_whi_mov_id,asset_id,posotita_theori,posotita_found,posotita_sxolio
  ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id.",".$row['id_asset'].",".$theori.",".$found.",''
  )";  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
  
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect,);
echo json_encode($return); die(); 
