<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$cmd='';if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
$id=0;if (isset($_POST['id'])) $id=intval($_POST['id']);


$my_page_title=gks_lang('Ψηφιακές υπογραφές από πάροχο, εντολή').' '.$cmd;

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_paroxos_signature','edit',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$sql="select * from gks_paroxos_signature where id_paroxos_signature=".$id;
$result = $db_link->query($sql);
if (!$result) { debug_mail(false,'error sql',$sql);$return= array('success' => false, 'message' => base64_encode('sql error'));   echo json_encode($return); die();}
if ($result->num_rows==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η υπογραφή')));
  echo json_encode($return); die();} 

  
$row = $result->fetch_assoc();
$signature_status=$row['signature_status'];

if ($cmd=='cancelsign_etimologiera_gr') {
  if (in_array($signature_status,['draft','assign','canreuse','canceled'])==false) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να αλλάξει η υπογραφή εφόσον έχει χρησιμοποιηθεί ή έχει σταλεί στον πάροχο')));
    echo json_encode($return); die(); }
  $aade_paroxos_id=intval($row['aade_paroxos_id']);
  $acc_inv_id=intval($row['acc_inv_id']);
  $acc_pay_id=intval($row['acc_pay_id']);
  $signature=trim_gks($row['r_signature']);
  $r_uid=trim_gks($row['r_uid']);
  $r_uidHash=trim_gks($row['r_uidHash']);
  $company_id=0;
  $company_sub_id=0;
  $pc_username='';
  $pc_password='';
  $paroxos_mydata_live=0;
  $id_company_paroxos=0;
  
  if ($aade_paroxos_id!=21) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η υπογραφή δεν είναι από etimologiera.gr')));
    echo json_encode($return); die(); }
    
  if ($signature=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η υπογραφή είναι κενή')));
    echo json_encode($return); die(); }
  
  if ($acc_inv_id>0) {
    $sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.company_id, gks_acc_inv.company_sub_id
    FROM (gks_acc_inv
    LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs ON gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub
    WHERE gks_acc_inv.id_acc_inv=".$acc_inv_id;
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);
    if (!$result) { debug_mail(false,'error sql',$sql);$return= array('success' => false, 'message' => base64_encode('sql error'));   echo json_encode($return); die();}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $company_id=intval($row['company_id']);
      $company_sub_id=intval($row['company_sub_id']);
    }
  } else if ($acc_pay_id>0) {
    $sql="SELECT gks_acc_pay.id_acc_pay, gks_acc_pay.company_id, gks_acc_pay.company_sub_id
    FROM (gks_acc_pay
    LEFT JOIN gks_company ON gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs ON gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub
    WHERE gks_acc_pay.id_acc_pay=".$acc_pay_id."
    and gks_acc_pay.aade_paroxos_id=21";
    $result = $db_link->query($sql);
    if (!$result) { debug_mail(false,'error sql',$sql);$return= array('success' => false, 'message' => base64_encode('sql error'));   echo json_encode($return); die();} 
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $company_id=intval($row['company_id']);
      $company_sub_id=intval($row['company_sub_id']);
    }
  }    
  if ($company_id==0 and $company_sub_id==0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εταιρεία που ανήκει αυτή η υπογραφή')));
    echo json_encode($return); die(); }
  
  $sql="SELECT id_company_paroxos,pc_username, pc_password,paroxos_mydata_live
  FROM gks_company_paroxos
  WHERE company_id=".$company_id." 
  AND company_sub_id=".$company_sub_id." 
  AND aade_paroxos_id=21 
  AND paroxos_send=1 
  AND paroxos_mydata_live=1;";
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);$return= array('success' => false, 'message' => base64_encode('sql error'));   echo json_encode($return); die();}  
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $pc_username=trim_gks($row['pc_username']);
    $pc_password=trim_gks($row['pc_password']);
    $paroxos_mydata_live=intval($row['paroxos_mydata_live']);
    $id_company_paroxos=intval($row['id_company_paroxos']);
  }
  if ($pc_username=='' or $pc_password=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν τα στοιχεία σύνδεσης για την εταιρεία').' '.$company_id.'|'.$company_sub_id));
    echo json_encode($return); die(); }


  

  $post_data=[];
  $post_data['hSignature']=$r_uidHash;
  $post_data['msgCancel']='Cancel Signature';
  $input=json_encode($post_data);

  $sub_url='/cancelSimSign';
  if ($paroxos_mydata_live==1) {
    $url=GKS_ETIMOLOGIERA_GR_MODE_LIVE_API.$sub_url;
  } else {
    $url='https://einvoicing-dev-api.etimologiera.gr/v4'.$sub_url;
  }
  //echo '<pre>'.$url;die();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  
  curl_setopt($ch, CURLOPT_POST,true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
  $headers=[];
  $headers[]='Content-Type: application/json';
  $headers[]='Authorization: Basic '.base64_encode($pc_username.':'.$pc_password);  
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
    
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close ($ch);

  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_send_'.time().'.json',$url."\n".(is_array($input) ? print_r($input,true) : $input));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$result);

  $p_send=$input;
  $p_response=[];
  $p_response['gks_curl_http_code']=$gks_curl_http_code;
  $p_response['gks_curl_info']=$gks_curl_info;
  $p_response['gks_curl_errno']=$gks_curl_errno;
  $p_response['result']=$result;


  gks_paroxos_log(array($acc_inv_id, $acc_pay_id, $id_company_paroxos, $p_send, $p_response));
  
  if ($gks_curl_http_code==0) { //HTTP Host not found
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Δεν βρέθηκε ο διακομιστής').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode('etimologiera_gr '.gks_lang('Δεν βρέθηκε ο διακομιστής')),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Δεν βρέθηκε το σημείο').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode('etimologiera_gr '.gks_lang('Δεν βρέθηκε το σημείο')),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  } else if ($gks_curl_http_code==400) { 
    $error='etimologiera_gr '.gks_lang('Σφάλμα').' '.gks_lang('Οι παράμετροι είναι λάθος').' (400)';
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode($error),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $error='etimologiera_gr '.gks_lang('Δεν επιτρέπεται η πρόσβαση');
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode($error),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  } else if ($gks_curl_http_code==403) { 
    $error='etimologiera_gr '.gks_lang('Σφάλμα').' (403).';
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result.'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode($error),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    $return=array('success' => false, 'message' => base64_encode('etimologiera_gr '.gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error: '.$gks_curl_http_code),'http_code'=>$gks_curl_http_code);
    echo json_encode($return); die(); 
  }

  $response=trim($result); //trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'etimologiera_gr response error',$response);
    $return=array('success' => false, 'message' => base64_encode('etimologiera_gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result));
    echo json_encode($return); die(); }

  $response_array = json_decode($response, true);
  if (isset($response_array['response']['paroxosError'])) {
    $value=$response_array['response']['paroxosError'];
    $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
    $td_message=[];
    if (isset($value['description'])) $td_message[]= $value['description'];
    if (isset($value['details'])) $td_message[]= $value['details'];

    debug_mail(false,'etimologiera_gr response error',$response);
    $return= array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>Code:'.$td_code.'<br>'.implode('<br>',$td_message)));
    echo json_encode($return); die();   
  }
  
  if (isset($response_array['response']['responses']) and
      is_array($response_array['response']['responses']) and
      count($response_array['response']['responses'])==1 and
      isset($response_array['response']['responses'][0]['invoiceUid'])) {

    $sql="update gks_paroxos_signature set signature_status='canceled',
    cancel_response='".$db_link->escape_string(serialize($response_array))."'
    where id_paroxos_signature=".$id;
    $result = $db_link->query($sql);
    if (!$result) { debug_mail(false,'error sql',$sql);$return= array('success' => false, 'message' => base64_encode('sql error'));   echo json_encode($return); die();}

    $return = array('success' => true, 'message' => base64_encode(gks_lang('OK')));
    echo json_encode($return); die(); 
    
  }

  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').'<br>'.$response));
  echo json_encode($return); die(); 
  
  print '<pre>ffffffffff '.$cmd.'|'.$id.'|'.$signature.'|'.$response;die();

}

$return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος εντολή')));
echo json_encode($return); die(); 
