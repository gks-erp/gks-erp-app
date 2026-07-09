<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_erp_app_mobile_get_later_version() {
  global $db_link;
  $GKS_ERP_APP_MOBILE_VER='';
  $sql_latestappmobilever="select myvalue from gks_settings where mykey='GKS_ERP_APP_MOBILE_VER'";
  $result_latestappmobilever = $db_link->query($sql_latestappmobilever);  
  if (!$result_latestappmobilever) {debug_mail(false,'error sql',$sql);die('sql error');}
  if ($result_latestappmobilever->num_rows==1) {	
    $row_latestappmobilever = $result_latestappmobilever->fetch_assoc();  
    $GKS_ERP_APP_MOBILE_VER=trim_gks($row_latestappmobilever['myvalue']);
  }
  if ($GKS_ERP_APP_MOBILE_VER=='') $GKS_ERP_APP_MOBILE_VER='2.0';
  return $GKS_ERP_APP_MOBILE_VER;
}

function gks_erp_app_mobile_get_firebase_token() {
  global $db_link;

  $return = array(
    'success' => false, 
    'message' => base64_encode('generic error get_firebase_token local'), 
    'token' => ''
  );

  
  $GKS_FIREBASE_TOKEN='';
  $GKS_FIREBASE_TOKEN_EXPIRES_IN=0;
  $sql_settings="select myvalue from gks_settings where mykey='GKS_FIREBASE_TOKEN' and myvalue<>''";
  $result_settings = $db_link->query($sql_settings);
  if (!$result_settings) {
    debug_mail(false,'error sql',$sql_settings);
    $return['message'] = base64_encode('sql error');
    return $return; }
  if ($result_settings->num_rows==1) {
    $row_settings = $result_settings->fetch_assoc();    
    $GKS_FIREBASE_TOKEN= $row_settings['myvalue'];
    
    $sql_settings="select myvalue from gks_settings where mykey='GKS_FIREBASE_TOKEN_EXPIRES_IN' and myvalue<>''";
    $result_settings = $db_link->query($sql_settings);
    if (!$result_settings) {
      debug_mail(false,'error sql',$sql_settings);
      $return['message'] = base64_encode('sql error');
      return $return; }
    if ($result_settings->num_rows==1) {
      $row_settings = $result_settings->fetch_assoc();    
      $GKS_FIREBASE_TOKEN_EXPIRES_IN= intval($row_settings['myvalue']);
    }
    
    if (($GKS_FIREBASE_TOKEN_EXPIRES_IN - 30) < time()) { //30 secs pio prin
      $GKS_FIREBASE_TOKEN='';
      $GKS_FIREBASE_TOKEN_EXPIRES_IN=0;
    }
      
    //$lijeis_se=$GKS_FIREBASE_TOKEN_EXPIRES_IN - time();
    
  }
  
  if ($GKS_FIREBASE_TOKEN=='') {
    $postdata=[];
    $rand1=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
    $semd5=md5($rand1. GKS_ERP_HASHMD5KEY05.$rand1);
    $postdata['rand1']=$rand1;
    $postdata['semd5']=$semd5;
    $postdata['HTTP_HOST']=$_SERVER['HTTP_HOST'];
    $postdata['GKS_SITE_URL']=GKS_SITE_URL;
    $postdata['cmd']='get_token';    
    
    $postdata = json_encode($postdata);
    
    $fileurl='https://tools.gks.gr/firebase/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fileurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $file = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    $extra_error_message='';
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $extra_error_message='HTTP Host not found';
      $file='';
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $extra_error_message='HTTP 404 REQUEST not found';
      $file='';
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $extra_error_message='HTTP 400 BAD_REQUEST';
      $file='';
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $extra_error_message='HTTP 401 UNAUTHORIZED';
      $file='';
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $extra_error_message='Unkown HTTP Code: '.$gks_curl_http_code;
      $file='';
    } 
    
    //echo '<pre>qqqqqqqqqq'.$file;die();
  
    $data=json_decode($file,true);
    
    if ($file =='' or 
        is_array($data)==false or 
        isset($data['success'])==false) {
      //error connection
      debug_mail(false,'gks_erp_app_mobile_get_firebase_token error1', $fileurl.' '.$extra_error_message);
      $return['message'] = base64_encode(gks_lang('Σφάλμα κατά την λήψη του firebase token').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$file.'<br>'.$extra_error_message);
      return $return;}
    
    if ($data['success']==false or 
        isset($data['token'])==false or 
        isset($data['expire'])==false or 
        $data['token']=='' or
        $data['expire']==0) {
      //error connection
      debug_mail(false,'gks_erp_app_mobile_get_firebase_token error1', $fileurl.' '.$extra_error_message);
      $return['message'] = $data['message'];
      return $return;}
    
    $GKS_FIREBASE_TOKEN=$data['token'];
    $GKS_FIREBASE_TOKEN_EXPIRES_IN=$data['expire'];


    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_FIREBASE_TOKEN','".$db_link->escape_string($GKS_FIREBASE_TOKEN)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql_settings);
      $return['message'] = base64_encode('sql error');
      return $return; }

    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_FIREBASE_TOKEN_EXPIRES_IN','".$db_link->escape_string($GKS_FIREBASE_TOKEN_EXPIRES_IN)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql_settings);
      $return['message'] = base64_encode('sql error');
      return $return; }
          

  }
  
  
  if ($GKS_FIREBASE_TOKEN=='') {
    debug_mail(false,'FIREBASE TOKEN', gks_lang('Δεν βρέθηκε το FIREBASE TOKEN της εφαρμογής').'<br>'.
    gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return['message'] = base64_encode(gks_lang('Δεν βρέθηκε το FIREBASE TOKEN της εφαρμογής').'<br>'.
    gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    return $return;  }
  
  $return['success']=true;
  $return['message']=base64_encode('OK');
  $return['token']=$GKS_FIREBASE_TOKEN;
  //echo '<pre>gggggggggggggg';print_r($return);die();

  //print '<pre>'.$GKS_FIREBASE_TOKEN."\r\n".$GKS_FIREBASE_TOKEN_EXPIRES_IN."\r\n".$lijeis_se."\r\n";print_r($row);die();

  return $return;
        
}


function gks_erp_app_mobile_run_command($params) {
  global $db_link;
  
  $return = array('success' => false, 'message' => base64_encode('generic error erp_app_run_command'), 'data' => false);
  
  $id='';if (isset($params['id'])) $id=intval($params['id']); 
  $cmd='';if (isset($params['cmd'])) $cmd=trim_gks($params['cmd']); 

  if ($id <= 0) {
    debug_mail(false,'the id_erp_app is not set','');
    $return['message'] = base64_encode(gks_lang('Δεν έχει ορισθεί εφαρμογή gks ERP App Mobile'));
    return $return;}

  //echo '<pre>'.$cmd; die();
  
  $url_cmd='';
  if ($cmd=='run_command_alive') $url_cmd='/';
  else if ($cmd=='run_command_stats') $url_cmd='/stats2';
  else if ($cmd=='run_command_getdata') $url_cmd='/getdata';
  else if ($cmd=='run_command_settings') $url_cmd='/settings';
  else if ($cmd=='run_command_testsms') $url_cmd='/testsms';
  else if ($cmd=='run_command_readallsms') $url_cmd='/readallsms';
  else if ($cmd=='run_command_gps_curr') $url_cmd='/gps_curr';
  else if ($cmd=='run_command_local_printers') $url_cmd='/local_printers';
  else if ($cmd=='run_command_frpclog') $url_cmd='/frpc_log';

  $push_cmd='';
  if (strlen($cmd)>=16 and substr($cmd, 0,17)=='run_command_push_') { //run_command_push_notifyinfo
    $push_cmd=substr($cmd, 17);
    
    
  }
  //echo '<pre>|'.$url_cmd.'|'.$push_cmd.'|';die();

  
  if ($url_cmd=='' and $push_cmd=='') {
    debug_mail(false,'the url_cmd and push_cmd is not set','');
    $return['message'] = base64_encode(gks_lang('Δεν έχει ορισθεί η σωστή εντολή').' ('.$cmd.')');
    return $return;}
  
  $sql="SELECT * FROM gks_erp_app_mobile where id_erp_app_mobile = ".$id." and erp_app_mobile_disabled=0";
  if ($url_cmd=='/testsms' or $url_cmd=='/readallsms') {
    $sql.=" and erp_app_mobile_can_sms=1";
  }
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return['message'] = base64_encode('sql error');
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εφαρμογή gks ERP App Mobile').'<br>'.
    gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return['message'] = base64_encode(gks_lang('Δεν βρέθηκε η εφαρμογή gks ERP App Mobile').'<br>'.
    gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    return $return;  }
  $row = $result->fetch_assoc();
  
  $erp_app_mobile_secret=$row['erp_app_mobile_secret'];
  $rand1=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
  $semd5=md5($rand1.$erp_app_mobile_secret);
  $params['postdata']['rand1']=$rand1;
  $params['postdata']['semd5']=$semd5;
  
  

  
  if ($url_cmd!='') {
  
    $public_url='';
    if ($row['erp_app_mobile_url']=='frp') {
      if (trim_gks($row['erp_app_mobile_token'])!='') {
        $public_url='http://'.$row['erp_app_mobile_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
      }
    } else {
      if (trim_gks($row['erp_app_mobile_url'])!='' and $row['erp_app_mobile_port']>0) {
        $public_url='http://'.$row['erp_app_mobile_url'].':'.$row['erp_app_mobile_port'];
      }
    }
    if ($public_url=='') {
      debug_mail(false,'the public_url is not set','');
      $return['message'] = base64_encode(gks_lang('Δεν έχει ορισθεί το Public URL'));
      return $return;}  
  
    $fileurl=$public_url.$url_cmd;
    
    //echo '<pre>'.$fileurl; die();
  //  $opts = array(
  //    'http'=>array(
  //      'timeout' => 100,  //10 Seconds  
  //      'method'=>"GET",
  //      'header'=>"Accept-language: en\r\n"
  //    )
  //  );
  //  
  //  $context = stream_context_create($opts);
    
    
    
    //$file = @file_get_contents($fileurl, false, $context);
    
    $postdata='';
    if (isset($params['postdata']) and is_array($params['postdata'])) $postdata = http_build_query($params['postdata']);
    
    //print '<pre>';print_r($params);die();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fileurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($url_cmd=='/readallsms') curl_setopt($ch, CURLOPT_TIMEOUT, 60); //secs
    
    $file = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    
    curl_close ($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    $extra_error_message='';
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $extra_error_message='HTTP Host not found';
      $file='';
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $extra_error_message='HTTP 404 REQUEST not found';
      $file='';
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $extra_error_message='HTTP 400 BAD_REQUEST';
      $file='';
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $extra_error_message='HTTP 401 UNAUTHORIZED';
      $file='';
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $extra_error_message='Unkown HTTP Code: '.$gks_curl_http_code;
      $file='';
    } 
    
    
  
    
    if ($file == '') {
      //error connection
      debug_mail(false,'gks ERP App Mobile error1', $fileurl.' '.$extra_error_message);
      $return['message'] = base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με την εφαρμογή').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$extra_error_message);
      return $return;}
    
    if ($url_cmd=='/stats2') {
      $file=str_replace('<pre>', '', $file);
      $file=str_replace('</pre>', '', $file);
      $file=str_replace("\r\n", '<br>', $file);
    } else if ($url_cmd=='/settings') {
      $file=str_replace('<pre>', '', $file);
      $file=str_replace('</pre>', '', $file);
      $file=str_replace("\r\n", '<br>', $file);
  
    } else if ($url_cmd=='/gps_curr') {
      //$file=json_decode($file,true);
    }
    
    
    $return['data']=$file;
    $return['success']=true;
    $return['message']='OK';
    return $return;
  }



  if ($push_cmd!='') {
    $firebase_token=trim_gks($row['firebase_token']);

    if (strlen($firebase_token)<=10) {
      //error connection
      debug_mail(false,'gks ERP App Mobile firebase token error', print_r($row,true));
      $return['message'] = base64_encode(gks_lang('Δεν έχει στείλει το gks ERP App Mobile ακόμα το firebase token').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      return $return;}
    
    $res=gks_erp_app_mobile_get_firebase_token();
    if ($res['success']==false) {
      debug_mail(false,'gks ERP App Mobile firebase token error', print_r($row,true));
      $return['message'] = $res['message'];
      return $return;}
      
    //echo '<pre>lllllllllllllll';die();  
    $GKS_FIREBASE_TOKEN=$res['token'];


    $mypost=[];
    $mypost['message']=[];
    $mypost['message']['token']=$firebase_token; 
      
    if ($push_cmd=='notifyinfo') {
      $mypost['message']['notification']=[];
      $mypost['message']['notification']['title']='Hello World !';
      $mypost['message']['notification']['body']='Time: '.showDate(time(),'d/m/Y H:i:s',1);
    } else if ($push_cmd=='restartservice' || $push_cmd=='getdata') {
      $mypost['message']['data']=[];
      $mypost['message']['data']['mycmd']=$push_cmd;
      $mypost['message']['data']['myparams']='';      
        
    } else {
      debug_mail(false,'push_cmd error', gks_lang('Δεν έχει ορισθεί η σωστή εντολή').' ('.$push_cmd.')');
      $return['message'] = base64_encode(gks_lang('Δεν έχει ορισθεί η σωστή εντολή').' ('.$push_cmd.')');
      return $return;       
      
    }
    //print '<pre>';print_r($mypost);die();

    $mypostdata=json_encode($mypost);
    $headers = array(
      'Content-Type:application/json; UTF-8',
      'Authorization: Bearer '. $GKS_FIREBASE_TOKEN,
    );

    $fileurl =  'https://fcm.googleapis.com/v1/projects/gks-erp-app/messages:send';
  
    
    $ch = curl_init($fileurl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_USERPWD, $merchant_id.':'.$api_key);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');

    $file = curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    $extra_error_message='';
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $extra_error_message='HTTP Host not found';
      $file='';
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $extra_error_message='HTTP 404 REQUEST not found';
      $file='';
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $extra_error_message='HTTP 400 BAD_REQUEST';
      $file='';
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $extra_error_message='HTTP 401 UNAUTHORIZED';
      $file='';
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $extra_error_message='Unkown HTTP Code: '.$gks_curl_http_code;
      $file='';
    } 
    
    
  
    
    if ($file == '') {
      //error connection
      debug_mail(false,'Firebase notification error', $fileurl.' '.$extra_error_message);
      $return['message'] = base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με το Firebase').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$extra_error_message);
      return $return;}
      
    $response_json=@json_decode($file,true);
    if (is_array($response_json)==false or isset($response_json['name'])==false) {
      debug_mail(false,'Firebase notification error', $fileurl.' '.$extra_error_message);
      $return['message'] = base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με το Firebase').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$file);
      return $return;}      
      
    
    
    $return['data']=gks_lang('Επιτυχής αποστολή στο Firebase').'<br>'.$response_json['name'];//<pre>'.print_r($response_json,true).'</pre>';
    $return['success']=true;
    $return['message']='OK';
    return $return;
      
    
    print '<pre>'.$response;die();
                 
    
  }
    
}
