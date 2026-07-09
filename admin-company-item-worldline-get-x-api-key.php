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
$my_page_title=gks_lang('Λήψη X Api Key από').' Worldline';
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
$row_company = $result->fetch_assoc();
$worldline_username=trim_gks($row_company['worldline_username']);
$worldline_password=trim_gks($row_company['worldline_password']);
$worldline_authorization_code=trim_gks($row_company['worldline_authorization_code']);
  

if ($worldline_username=='' or $worldline_password=='' or $worldline_authorization_code=='') {
  debug_mail(false,'set viva paramss',gks_lang('Ορίστε και το <b>Όνομα χρήστη</b> και το <b>Κωδικός πρόσβασης</b> και το <b>Authorization Code</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε και το <b>Όνομα χρήστη</b> και το <b>Κωδικός πρόσβασης</b> και το <b>Authorization Code</b>')));
  echo json_encode($return); die();}

//worldline=6
$ret=gks_eftpos_get_token(6,$row_company);
//echo '<pre>token ggggggggg ... ';print_r($ret);die();
if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
$access_token=$ret['data']['access_token'];

//echo '<pre>';echo $access_token;die();

$url=GKS_WORLDLINE_COM_API.'/authorization/redeem/';

$headers = array(
  'Content-Type: application/json',
  'Accept: application/json',
  'User-Agent: gks ERP/2024',
  'Authorization: Bearer '. $access_token,
);

$mypost=array();
$mypost['Type']='webecr';
$mypost['Code']=$worldline_authorization_code;
$mypostdata=json_encode($mypost);  


//echo '<pre>ssssssssss '."\n".$access_token."\n".$url."\n".$mypostdata;die();

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
$myresponse = curl_exec($ch);
$gks_curl_errno=curl_errno($ch);
$gks_curl_info = curl_getinfo($ch);
curl_close($ch);

if ($myresponse=='') {
  debug_mail(false,'worldline error connection',gks_lang('Σφάλμα κατά την επικοινωνία με το').': '.GKS_WORLDLINE_COM_API);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με το').': '.GKS_WORLDLINE_COM_API));
  echo json_encode($return); die();}

  
  
$mydata=json_decode($myresponse,true);

/*Array
(
    [Id] => 9ocuFREXR9i1ZgvHH8qhSQ
    [Type] => webecr
)*/
//echo '<pre>ssssssssss '."\n";print_r($mydata);die();

if (isset($mydata['Id']) and isset($mydata['Type']) and $mydata['Type']=='webecr') {
  //debug_mail(false,'worldline OK response',$myresponse);
  $sql="update gks_company set worldline_x_api_key='".$db_link->escape_string($mydata['Id'])."' where id_company=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Η διαδικασία έγινε επιτυχώς').'<br>'.gks_lang('Το X Api Key είναι το').':<br>'.$mydata['Id']), 'x_api_key' => base64_encode($mydata['Id']));
  echo json_encode($return); die();
}



$return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').' '.$myresponse));
echo json_encode($return); die();
 