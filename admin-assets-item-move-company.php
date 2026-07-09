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


$asset_id=0;
if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
if ($asset_id<=0) {
  debug_mail(false,'the asset_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί πάγιο')));
  echo json_encode($return); die();}

$company_id=0;
if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
if ($company_id<=0) {
  debug_mail(false,'the company_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εταιρεία')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Μεταφορά παγίου σε εταιρεία');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_moves','add',-1);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$sql="SELECT id_asset,asset_last_company_id,asset_code,asset_title,asset_serialnumber FROM gks_assets where id_asset = ".$asset_id;
$sql.=" limit 1";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}

$row=$result->fetch_assoc();
$asset_code=$row['asset_code'];
$asset_title=$row['asset_title'];
$asset_serialnumber=$row['asset_serialnumber'];

$sql="SELECT * FROM gks_company where id_company = ".$company_id." and company_disable=0";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εταιρεία').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εταιρεία').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}

$row=$result->fetch_assoc();
$id_company=$row['id_company'];
$company_title=$row['company_title'];




$sql="insert into gks_assets_moves (asset_id,company_id,mydate,user_id_add,action_myip) values (
".$asset_id.",
".$company_id.",
now(),
".$my_wp_user_id.",
'".$gkIP."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$sql="update gks_assets set asset_last_company_id=".$company_id." where id_asset=".$asset_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


//$headers = 'MIME-Version: 1.0' . "\r\n";
//$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
//$headers .= 'From: debug@gks.gr' . "\r\n";
//$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
//
//$send_email='Το πάγιο:<br><a href="'.GKS_SITE_URL.'/my/admin-assets-item.php?id='.$asset_id.'">'.$asset_code.' - '.$asset_title.' - '.$asset_serialnumber.'</a><br>'.gks_lang('μεταφέρθηκε στην εταιρεία').':<br>'.
//'<a href="'.GKS_SITE_URL.'my/admin-company-item.php?id='.$company_id.'">'.$company_title.'</a><br>'.
//gks_lang('Από').': '.get_currentuserinfo()->display_name;  
//mail('kostas@gks.gr' , 'Asset Move company' , $send_email,$headers);  


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();




