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

$my_page_title=gks_lang('Αποθήκευση Κανάλι πωλήσεων').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_channel_sale',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_crm_channel_sale where id_crm_channel_sale = ".$id;
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



$crm_channel_sale_descr=''; if (isset($_POST['crm_channel_sale_descr'])) $crm_channel_sale_descr=trim_gks(base64_decode($_POST['crm_channel_sale_descr']));
$crm_channel_sale_sortorder=0; if (isset($_POST['crm_channel_sale_sortorder'])) $crm_channel_sale_sortorder=intval(stripslashes(urldecode($_POST['crm_channel_sale_sortorder'])));
$crm_channel_sale_disabled=0; if (isset($_POST['crm_channel_sale_disabled'])) $crm_channel_sale_disabled=intval($_POST['crm_channel_sale_disabled']);
$crm_channel_has_contact=0; if (isset($_POST['crm_channel_has_contact'])) $crm_channel_has_contact=intval($_POST['crm_channel_has_contact']);
$crm_channel_has_contact_filter=''; if (isset($_POST['crm_channel_has_contact_filter'])) $crm_channel_has_contact_filter=trim_gks(base64_decode($_POST['crm_channel_has_contact_filter']));
$crm_channel_has_campain=0; if (isset($_POST['crm_channel_has_campain'])) $crm_channel_has_campain=intval($_POST['crm_channel_has_campain']);
$crm_channel_has_url=0; if (isset($_POST['crm_channel_has_url'])) $crm_channel_has_url=intval($_POST['crm_channel_has_url']);
$crm_channel_has_text=0; if (isset($_POST['crm_channel_has_text'])) $crm_channel_has_text=intval($_POST['crm_channel_has_text']);
$crm_channel_has_code=0; if (isset($_POST['crm_channel_has_code'])) $crm_channel_has_code=intval($_POST['crm_channel_has_code']);


if ($crm_channel_has_contact==0) $crm_channel_has_contact_filter='';

if ($crm_channel_sale_descr=='') {debug_mail(false,'crm_channel_sale_descr',$crm_channel_sale_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }

$sql="select * from gks_crm_channel_sale where crm_channel_sale_descr like '".$db_link->escape_string($crm_channel_sale_descr)."' and id_crm_channel_sale<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το κανάλι πωλήσεων με περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-crm-channel-sale-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$crm_channel_sale_descr,$message);
  $message=str_replace('[2]',$row['id_crm_channel_sale'],$message);
  debug_mail(false,'hotel-room-type exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}






$redirect='';
if ($id==-1) {
  $sql="insert into gks_crm_channel_sale (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-crm-channel-sale-item.php?id='.$id); 
}

  
$sql="update gks_crm_channel_sale set 
crm_channel_sale_descr='".$db_link->escape_string($crm_channel_sale_descr)."',
crm_channel_sale_sortorder=".$crm_channel_sale_sortorder.",
crm_channel_sale_disabled=".$crm_channel_sale_disabled.",
crm_channel_has_contact=".$crm_channel_has_contact.",
crm_channel_has_contact_filter='".$db_link->escape_string($crm_channel_has_contact_filter)."',
crm_channel_has_campain=".$crm_channel_has_campain.",
crm_channel_has_url=".$crm_channel_has_url.",
crm_channel_has_text=".$crm_channel_has_text.",
crm_channel_has_code=".$crm_channel_has_code.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_crm_channel_sale = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

