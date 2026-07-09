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
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση διεύθυνσης επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_extra_address',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql="select * from gks_users_extra_address where id_users_extra_address=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}

$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$ea_name=''; if (isset($_POST['ea_name'])) $ea_name=trim_gks(base64_decode($_POST['ea_name']));
$ea_phone=''; if (isset($_POST['ea_phone'])) $ea_phone=trim_gks(base64_decode($_POST['ea_phone']));
$ea_branch=''; if (isset($_POST['ea_branch'])) $ea_branch=trim_gks(base64_decode($_POST['ea_branch']));
if ($ea_branch!='') $ea_branch=intval($ea_branch).'';
$ea_odos=''; if (isset($_POST['ea_odos'])) $ea_odos=trim_gks(base64_decode($_POST['ea_odos']));
$ea_arithmos=''; if (isset($_POST['ea_arithmos'])) $ea_arithmos=trim_gks(base64_decode($_POST['ea_arithmos']));
$ea_orofos=''; if (isset($_POST['ea_orofos'])) $ea_orofos=trim_gks(base64_decode($_POST['ea_orofos']));
$ea_perioxi=''; if (isset($_POST['ea_perioxi'])) $ea_perioxi=trim_gks(base64_decode($_POST['ea_perioxi']));
$ea_poli=''; if (isset($_POST['ea_poli'])) $ea_poli=trim_gks(base64_decode($_POST['ea_poli']));
$ea_tk=''; if (isset($_POST['ea_tk'])) $ea_tk=trim_gks(base64_decode($_POST['ea_tk']));
$ea_country_id=0; if (isset($_POST['ea_country_id'])) $ea_country_id=intval($_POST['ea_country_id']);
$ea_nomos_id=0; if (isset($_POST['ea_nomos_id'])) $ea_nomos_id=intval($_POST['ea_nomos_id']);
$ea_latitude=0; if (isset($_POST['ea_latitude'])) $ea_latitude=floatval(str_replace(',','.', $_POST['ea_latitude']));
$ea_longitude=0; if (isset($_POST['ea_longitude'])) $ea_longitude=floatval(str_replace(',','.', $_POST['ea_longitude']));


if ($user_id<=0) {debug_mail(false,'emptyl',                     gks_lang('Επιλέξτε μία επαφή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία επαφή')));
  echo json_encode($return); die();}

if ($ea_country_id<=0) {debug_mail(false,'emptyl',               gks_lang('Επιλέξτε την χώρα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την χώρα')));
  echo json_encode($return); die();}

$redirect='';
if ($id==-1) {
  $sql="insert into gks_users_extra_address (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-users-extra_address-item.php?id='.$id); 
}

$sql="update gks_users_extra_address set 
user_id=".$user_id.",
ea_name='".$db_link->escape_string($ea_name)."', 
ea_phone='".$db_link->escape_string($ea_phone)."', 
ea_branch = ".($ea_branch=='' ? 'null' : $ea_branch).",
ea_odos='".$db_link->escape_string($ea_odos)."', 
ea_arithmos='".$db_link->escape_string($ea_arithmos)."', 
ea_orofos='".$db_link->escape_string($ea_orofos)."', 
ea_perioxi='".$db_link->escape_string($ea_perioxi)."', 
ea_poli='".$db_link->escape_string($ea_poli)."', 
ea_tk='".$db_link->escape_string($ea_tk)."', 
ea_country_id=".$ea_country_id.",
ea_nomos_id=".$ea_nomos_id.",
ea_latitude='".number_format($ea_latitude,16,'.','')."',
ea_longitude='".number_format($ea_longitude,16,'.','')."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_users_extra_address = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

calc_profilepososto($user_id,false);
  


$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect);
echo json_encode($return); die();







