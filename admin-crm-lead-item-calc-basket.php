<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Υπολογισμός dp Ευκαιρίας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$mydata_str = trim_gks(base64_decode($_POST['mydata_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$mydata = json_decode($mydata_str, true);
if ($mydata === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mydata_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}



unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='crm_lead';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= intval($mydata['company_id']);
$mybasketarray['company_sub_id']= intval($mydata['company_sub_id']);
$mybasketarray['user']['user_id']=$mydata['user_id'];
$mybasketarray['user']['afm']=$mydata['afm'];
$mybasketarray['user']['ma_country_id']=$mydata['ma_country_id'];
$mybasketarray['parastatiko']=1; //parastatiko

gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];




$return = array('success' => true, 'message' => base64_encode('OK'),
  'check_vies' => $mybasketarray['check_vies'],
);
echo json_encode($return); die();

