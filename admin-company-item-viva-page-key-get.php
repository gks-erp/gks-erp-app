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
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Λήψη Page Key από').' Viva';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company',('edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');


$sql="select * from gks_company where id_company=".$id;
if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
$sql.=" limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}
$row = $result->fetch_assoc();
$viva_merchant_id=trim_gks($row['viva_merchant_id']);
$viva_api_key=trim_gks($row['viva_api_key']);
  

if ($viva_merchant_id=='' or $viva_api_key=='') {
  debug_mail(false,'set viva paramss',gks_lang('Ορίστε και το <b>Merchant ID</b> και το <b>API Key</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε και το <b>Merchant ID</b> και το <b>API Key</b>')));
  echo json_encode($return); die();}

//echo '<pre>'.GKS_VIVA_URL_WWW;die();

$headers = array(
  'Content-Type:application/json',
  'Authorization: Basic '. base64_encode($viva_merchant_id.':'.$viva_api_key)
);
$ch = curl_init(GKS_VIVA_URL_WWW.'/api/messages/config/token');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 300);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
$myresponse = curl_exec($ch);
$gks_curl_errno=curl_errno($ch);
$gks_curl_info = curl_getinfo($ch);
curl_close($ch);

if ($myresponse=='') {
  debug_mail(false,'viva error connection',gks_lang('Σφάλμα κατά την επικοινωνία με το').': '.GKS_VIVA_URL_WWW);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με το').': '.GKS_VIVA_URL_WWW));
  echo json_encode($return); die();}
  
  
$mydata=json_decode($myresponse,true);

if (isset($mydata['Key'])) {
  debug_mail(false,'viva OK response',$myresponse);
  $sql="update gks_company set viva_verify_webhook_page_key='".$db_link->escape_string($mydata['Key'])."' where id_company=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Η διαδικασία έγινε επιτυχώς').'<br>'.gks_lang('Το Page Key είναι το').':<br>'.$mydata['Key']), 'page_key' => base64_encode($mydata['Key']));
  echo json_encode($return); die();}

if (isset($mydata['Message'])) {
  debug_mail(false,'viva error response',$myresponse);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').' '.$mydata['Message']));
  echo json_encode($return); die();}

//59399BF397FAB287EFFEEB49F82732B058D2210F

$return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').' '.$myresponse));
echo json_encode($return); die();
 