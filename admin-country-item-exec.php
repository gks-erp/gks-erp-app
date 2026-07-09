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

$my_page_title=gks_lang('Αποθήκευση Χώρας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_country',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_country where id_country = ".$id;
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


$country_name=''; if (isset($_POST['country_name'])) $country_name=trim_gks(base64_decode($_POST['country_name']));
$country_initials=''; if (isset($_POST['country_initials'])) $country_initials=trim_gks(base64_decode($_POST['country_initials']));
$country_initials3=''; if (isset($_POST['country_initials3'])) $country_initials3=trim_gks(base64_decode($_POST['country_initials3']));
$country_ISO_3166_1=''; if (isset($_POST['country_ISO_3166_1'])) $country_ISO_3166_1=intval($_POST['country_ISO_3166_1']);
$country_lang=''; if (isset($_POST['country_lang'])) $country_lang=trim_gks(base64_decode($_POST['country_lang']));
$phone_code=''; if (isset($_POST['phone_code'])) $phone_code=trim_gks(base64_decode($_POST['phone_code']));


if ($country_name=='') {debug_mail(false,'emptyl',               gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }



$redirect='';
if ($id==-1) {
  $sql="insert into gks_country (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-country-item.php?id='.$id); 
}



$sql="update gks_country set 
country_name='".$db_link->escape_string($country_name)."',
country_initials='".$db_link->escape_string($country_initials)."',
country_initials3='".$db_link->escape_string($country_initials3)."',
country_ISO_3166_1=".$country_ISO_3166_1.",
country_lang='".$db_link->escape_string($country_lang)."',
phone_code='".$db_link->escape_string($phone_code)."',
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_country = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


gks_lang_data_obj_save_exec_php('gks_country',$id);
 

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

