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

//echo '<pre>'.time();die();


$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks(base64_decode($_POST['cmd']));
if ($cmd=='') {
  debug_mail(false,'the cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή')));
  echo json_encode($return); die();}

$group_cmd=''; if (isset($_POST['group_cmd'])) $group_cmd=trim_gks(base64_decode($_POST['group_cmd']));
if ($group_cmd=='') {
  debug_mail(false,'the group_cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η ομάδα εντολής')));
  echo json_encode($return); die();}

$company_id=0;if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$api_call='';if (isset($_POST['api_call'])) $api_call=trim_gks(base64_decode($_POST['api_call']));

$asset_id=0; if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);

$my_page_title= gks_lang('Εκτέλεση εντολής από πάγιο [1] για την εταιρεία id [2]');
$my_page_title=str_replace('[1]',$asset_id,$my_page_title);
$my_page_title=str_replace('[2]',$company_id,$my_page_title);

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company','view',$company_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','view',$asset_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($company_id<=0) {
  debug_mail(false,'the company_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εταιρεία')));
  echo json_encode($return); die();}

$sql="select * from gks_company where id_company=".$company_id;
//if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
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
$mellon_username=trim_gks($row_company['mellon_username']);
$mellon_password=trim_gks($row_company['mellon_password']);
$mellon_authorization_code=trim_gks($row_company['mellon_authorization_code']);
 
$epay_username=trim_gks($row_company['epay_username']);
$epay_password=trim_gks($row_company['epay_password']);
$epay_authorization_code=trim_gks($row_company['epay_authorization_code']);
 
$worldline_username=trim_gks($row_company['worldline_username']);
$worldline_password=trim_gks($row_company['worldline_password']);
$worldline_authorization_code=trim_gks($row_company['worldline_authorization_code']);

$nexi_username=trim_gks($row_company['nexi_username']);
$nexi_password=trim_gks($row_company['nexi_password']);
$nexi_authorization_code=trim_gks($row_company['nexi_authorization_code']);

$sql_asset="select * from gks_assets where id_asset=".$asset_id;
$result_asset = $db_link->query($sql_asset);  
if (!$result_asset) {
  debug_mail(false,'error sql',$sql_asset);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result_asset->num_rows!=1) {
  debug_mail(false,'asset not found',$sql_asset);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πάγιο').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}
$row_asset = $result_asset->fetch_assoc();

//echo '<pre>hhhhhhhhhhhhh '.$cmd;die();
 
if ($cmd=='mellon_run_command') {
  $ret=gks_eftpos_has_credentials_mellon($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

  $ret=gks_eftpos_get_token(3,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  
  
  if ($api_call=='terminal_list') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_MELLONGROUP_COM_API.'/api/v2.1/terminal/';
    //echo $url;die();
    $headers = array(
      'Content-Type:application/json',
      'Authorization: Bearer '. $access_token,
      'X-Api-Key: '.$row_company['mellon_x_api_key'],
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
} 


if ($cmd=='epay_run_command') {
  $ret=gks_eftpos_has_credentials_epay($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

  $ret=gks_eftpos_get_token(5,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  
  
  if ($api_call=='terminal_list') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_EPAY_COM_API.'/terminal/';
    //echo $url;die();
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $access_token,
      'X-Api-Key: '.$row_company['epay_x_api_key'],
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
}

if ($cmd=='worldline_run_command') {
  //echo '<pre>token ggggggggg ... ';print_r($row_company);die();
  $ret=gks_eftpos_has_credentials_worldline($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();

  $ret=gks_eftpos_get_token(6,$row_company);
  
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  
  if (empty($row_asset['worldline_terminal_id'])) {
    debug_mail(false,'Terminal ID Worldline not set');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το Terminal ID της Worldline στο πάγιο')));
    echo json_encode($return); die();}
    
    
  
  if ($api_call=='terminal_link') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_WORLDLINE_COM_API.'/v1/payment-terminal/connection-intent';
    //echo $url;die();
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $access_token,
      //'X-Api-Key: '.$row_company['worldline_x_api_key'],
    );
    
    $mypost=array(
      'merchant_bank_id'=> GKS_WORLDLINE_COM_API_BANK_ID,
      'merchant_id_type'=> 'merchant_email',
      'merchant_id'=> $row_company['worldline_username'],
      'app_language_code'=> 'en',
      'merchant_terminal_id'=> $row_asset['worldline_terminal_id'],
      'merchant_device_id'=> '',
      'action'=> 'LINK',
    );
    $mypostdata=json_encode($mypost);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
    
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_terminal_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_terminal_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


    $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
    
  if ($api_call=='terminal_status') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_WORLDLINE_COM_API.'/v1/ped/status';
    //echo $url;die();
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $access_token,
      //'X-Api-Key: '.$row_company['worldline_x_api_key'],
    );

    $mypost=array(
      'merchant_bank_id'=> GKS_WORLDLINE_COM_API_BANK_ID,
      'merchant_id_type'=> 'merchant_email',
      'merchant_id' => $row_company['worldline_username'],
      'terminal_id_type'=> 'merchant_terminal_id',
      'terminal_id'=> $row_asset['worldline_terminal_id'],
    );
    $mypostdata=json_encode($mypost);
    
    //echo '<pre>ggggggg';print_r($mypost);die();
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);    
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_terminalstatus_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_terminalstatus_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


    $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    
$html.='<table class="table table-sm table-responsive table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="2" align="center" style="margin-top:40px;border-collapse: collapse;">
<thead>
<tr>
<th class="table-dark" scope="col" width="0%">Status<br>Code</th>
<th class="table-dark" scope="col" width="40%">Message</th>
<th class="table-dark" scope="col" width="60%">Advice</th>
</tr>
</thead>
<tbody>
<tr><td class="mytdcm">1</td><td class="mytdcml">Requires device setup</td><td class="mytdcml">Include in next intent call Payment token and Connection token</td></tr>
<tr><td class="mytdcm">2</td><td class="mytdcml">Requires device setup</td><td class="mytdcml">Include in next intent call Payment token and Connection token</td></tr>
<tr><td class="mytdcm">3</td><td class="mytdcml">Device activated</td><td class="mytdcml">Ready to accept payments using Payment tokens</td></tr>
<tr><td class="mytdcm">4</td><td class="mytdcml">Device disabled</td><td class="mytdcml">Merchant or you should find out the reasons from the acquiring service provider</td></tr>
<tr><td class="mytdcm">5</td><td class="mytdcml">Device locked and disabled</td><td class="mytdcml">Merchant or you should find out the reasons from the acquiring service provider</td></tr>
<tr><td class="mytdcm">6</td><td class="mytdcml">Device locked</td><td class="mytdcml">Merchant or you should find out the reasons from the acquiring service provider</td></tr>
<tr><td class="mytdcm">7</td><td class="mytdcml">Requires device setup</td><td class="mytdcml">Include in next intent call Payment token and Connection token</td></tr>
<tr><td class="mytdcm">8</td><td class="mytdcml">Requires device setup</td><td class="mytdcml">Include in next intent call Payment token and Connection token</td></tr>
</tbody>
</table>';   
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
}

if ($cmd=='nexi_run_command') {
  $ret=gks_eftpos_has_credentials_nexi($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

  $ret=gks_eftpos_get_token(7,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  
  
  if ($api_call=='terminal_list') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_NEXI_COM_API.'/terminal/';
    //echo $url;die();
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $access_token,
      'X-Api-Key: '.$row_company['nexi_x_api_key'],
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
}

$return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος εντολή')),'html' => 'error');
echo json_encode($return); die();
    
echo '<pre>';echo $fileurl.'|'.$file;die();
