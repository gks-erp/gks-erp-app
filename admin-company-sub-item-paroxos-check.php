<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$company_sub_id=0;
if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
if ($company_sub_id<=0) {
  debug_mail(false,'the company_sub_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Δοκιμή σύνδεσης με πάροχο company_sub_id').' '.$company_sub_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_subs',('edit'),$company_sub_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');



$sql="select * from gks_company_subs where id_company_sub=".$company_sub_id;
if (count($perm_id_company_ids)>0) $sql.=" and company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
$sql.=" limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε η εγγραφή').' (1)<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').' (1)<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}


$paroxos_send=0;
$aade_paroxos_id=0;
$paroxos_mydata_live=0;
$pc_username='';
$pc_password='';
$pc_key='';

$sql_paroxos="select * from gks_company_paroxos where company_sub_id=".$company_sub_id;
$result_paroxos = $db_link->query($sql_paroxos); 
if (!$result_paroxos) {
  debug_mail(false,'error sql',$sql_paroxos);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

if ($result_paroxos->num_rows==0) {
  debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε η εγγραφή').' (2)<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').' (2)<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}


$row_paroxos = $result_paroxos->fetch_assoc();
$id_company_paroxos=intval($row_paroxos['id_company_paroxos']);
$aade_paroxos_id=intval($row_paroxos['aade_paroxos_id']);
$paroxos_send=intval($row_paroxos['paroxos_send']);
$paroxos_mydata_live=intval($row_paroxos['paroxos_mydata_live'])==1;
$sandbox =''; if ($paroxos_mydata_live==false) $sandbox='sandbox_';
$pc_username=trim_gks($row_paroxos[$sandbox.'pc_username']);
$pc_password=trim_gks($row_paroxos[$sandbox.'pc_password']);
$pc_key=trim_gks($row_paroxos[$sandbox.'pc_key']);

$params=array(
  'id_company_paroxos' => $id_company_paroxos,
  'aade_paroxos_id' => $aade_paroxos_id,
  'paroxos_mydata_live' => $paroxos_mydata_live,
  'pc_username' => $pc_username,
  'pc_password' => $pc_password,
  'pc_key' => $pc_key,
);
//echo '<pre>';print_r($params);die();
$ret=gks_paroxos_loginToSubscription($params);

if ($ret['success']==false) {
  debug_mail(false,'error gks_paroxos_loginToSubscription',print_r($ret,true));
  $return = array('success' => false, 'message' => base64_encode($ret['message']));
  echo json_encode($return); die();}
  

$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
