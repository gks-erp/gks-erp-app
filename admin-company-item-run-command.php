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

$company_id=0;if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$api_call='';if (isset($_POST['api_call'])) $api_call=trim_gks(base64_decode($_POST['api_call']));

$my_page_title=gks_lang('Εκτέλεση εντολής [1] για την εταιρεία id').': '.$company_id;
$my_page_title=str_replace('[1]','Viva/Mellon/ePay/Worldline/NEXI',$my_page_title);

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company','view',$company_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');

if ($company_id<=0) {
  debug_mail(false,'the company_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εταιρεία')));
  echo json_encode($return); die();}

$sql="select * from gks_company where id_company=".$company_id;
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

$viva_merchant_id=trim_gks($row_company['viva_merchant_id']);
$viva_api_key=trim_gks($row_company['viva_api_key']);
$viva_pos_client_id=trim_gks($row_company['viva_pos_client_id']);
$viva_pos_client_secret=trim_gks($row_company['viva_pos_client_secret']);

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
 

if ($cmd=='viva_run_command') {
  $ret=gks_eftpos_has_credentials_viva($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

  $ret=gks_eftpos_get_token(1,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];

  if ($api_call=='terminal_list') {
    //echo '<pre>'.$access_token;die();
    
    $url=gks_eftpos_get_base_url_api_viva().'/ecr/v1/devices:search';
    //echo $url;die();
    $headers = array(
      'Content-Type:application/json',
      'Authorization: Bearer '. $access_token
    );
    
    $mypost=array(
      'statusId' => 1,
    );
    $mypostdata=json_encode($mypost);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $response = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

    $response_array=json_decode($response,true);
    if (is_array($response_array) and count($response_array)>0 and isset($response_array[0]['statusId'])) {
      $html='<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: left !important;" width="0%">#</th>
    <th class="table-dark" scope="col" style="text-align: left !important;" width="20%">'.gks_lang('Terminal ID').'</th>         
    <th class="table-dark" scope="col" style="text-align: left !important;" width="30%">'.gks_lang('Virtual Terminal ID').'</th>         
    <th class="table-dark" scope="col" style="text-align: left !important;" width="10%">'.gks_lang('Source Code').'</th>         
    <th class="table-dark" scope="col" style="text-align: left !important;" width="40%">'.gks_lang('Πάγιο').'</th>         
  </tr>
</thead>
<tbody>';
      
      $sql="SELECT id_asset, asset_title, viva_terminal_id, viva_company_id
      FROM gks_assets
      WHERE gks_assets.viva_terminal_id<>''";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      $viva_terminals=[];
      while ($row = $result->fetch_assoc()) {
        $viva_terminals[$row['viva_terminal_id']]=array(
          'company_id'=>$row['viva_company_id'],
          'id_asset'=>$row['id_asset'],
          'asset_title'=>$row['asset_title'],
        );
      }

      $cc=0;
      foreach ($response_array as $myterminal) {
        $cc++;
        $asset_html='';$asset_background='';
        if (isset($viva_terminals[$myterminal['terminalId']])) {
          $asset_html='<a href="admin-assets-item.php?id='.$viva_terminals[$myterminal['terminalId']]['id_asset'].'">'.$viva_terminals[$myterminal['terminalId']]['asset_title'].'</a>';
          if ($company_id!=$viva_terminals[$myterminal['terminalId']]['company_id']) {
            $asset_background=' style="background-color: #ffaaaa;" title="'.gks_lang('Σε άλλη εταιρεία').'"';
          }
        }
        
    $html.='<tr>
      <th scope="row" class="mytdcm">'.$cc.'</th>
      <td class="mytdcml">'.$myterminal['terminalId'].'</td>
      <td class="mytdcml">'.$myterminal['virtualTerminalId'].'</td>
      <td class="mytdcml">'.$myterminal['sourceCode'].'</td>
      <td class="mytdcml" '.$asset_background.'>'.$asset_html.'</td>
    </tr>';
           
      } 

      $html.='</tbody>
</table>';

    } else {
      
      $html='<pre>'.json_encode(json_decode($response,true),JSON_PRETTY_PRINT).'</pre>';
    }
    
    
    
    $return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html));
    echo json_encode($return); die();
  }
    
  echo '<pre>';print_r($ret);die();
  
}

 
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
  $ret=gks_eftpos_has_credentials_worldline($row_company);
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}

  $ret=gks_eftpos_get_token(6,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  
  
  if ($api_call=='terminal_list') {
    //echo '<pre>'.$access_token;die();
    $url=GKS_WORLDLINE_COM_API.'/terminal/';
    //echo $url;die();
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $access_token,
      'X-Api-Key: '.$row_company['worldline_x_api_key'],
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
