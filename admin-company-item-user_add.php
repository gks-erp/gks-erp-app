<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Προσθήκη υπαλλήλου σε εταιρεία');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$company_sub_id=0; if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);

if ($company_sub_id>0) {
  $sql="select company_id from gks_company_subs where id_company_sub=".$company_sub_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows==1) {  
    $row = $result->fetch_assoc();
    $company_id=$row['company_id'];
  }
}

if ($company_id<=0) {
  debug_mail(false,'the company_id company_id not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εταιρεία')));
  echo json_encode($return); die();}

$user_id=0;
if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
if ($user_id<=0) {
  debug_mail(false,'the user_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί υπάλληλος')));
  echo json_encode($return); die();}

$date_hire='';
if (isset($_POST['date_hire'])) {
  if ($_POST['date_hire'] == '__/__/____ __:__') $_POST['date_hire']='';
  if ($_POST['date_hire'] == '__/__/____') $_POST['date_hire']='';
  $date_hire=trim_gks(stripslashes(urldecode($_POST['date_hire'])));
  if ($date_hire!='') $date_hire = date('Y-m-d', gks_myFormatDate($date_hire));
}  
if ($date_hire=='') {
  debug_mail(false,'the date_hire is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η ημερομηνία Πρόσληψης')));
  echo json_encode($return); die();}
$sxolio=''; if (isset($_POST['sxolio'])) $sxolio=trim_gks(base64_decode($_POST['sxolio']));




$sql="SELECT ID FROM ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities not like '".$db_link->escape_string('a:1:{s:10:"subscriber";b:1;}')."' and ID = ".$user_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε η επαφή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η επαφή')));
  echo json_encode($return); die();}  


$sql="SELECT id_company FROM gks_company where id_company = ".$company_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε η εταιρεία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εταιρεία')));
  echo json_encode($return); die();}  

if ($company_sub_id !=0) {
  
  $sql="SELECT id_company_sub FROM gks_company_subs where company_id = ".$company_id." and id_company_sub=".$company_sub_id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows == 0) {
    debug_mail(false,'empty',gks_lang('Δεν βρέθηκε το υποκατάστημα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το υποκατάστημα')));
    echo json_encode($return); die();}  
}

$sql="SELECT * FROM gks_company_users where user_id = ".$user_id." and company_id=".$company_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty',gks_lang('Η επαφή έχει οριστεί ήδη'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η επαφή έχει οριστεί ήδη')));
  echo json_encode($return); die();}  




$sql="insert into gks_company_users (date_hire,company_id,company_sub_id,user_id,add_date,add_user_id,myip,sxolio) values (
'".$date_hire."',
".$company_id.",
".$company_sub_id.",
".$user_id.",
NOW(),
".$my_wp_user_id.",
'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($sxolio)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$sql="insert into gks_log_company_users (action_date,action_user_id,action_type,action_myip,company_id,company_sub_id,user_id,hire_exit_date) values(
NOW(),
".$my_wp_user_id.",
'add',
'".$db_link->escape_string($gkIP)."',
".$company_id.",
".$company_sub_id.",
".$user_id.",
'".$date_hire."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}



$return = array('success' => true, 'message' => base64_encode(gks_lang('OK')));
echo json_encode($return); die();