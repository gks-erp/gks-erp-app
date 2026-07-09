<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('gks ERP App Mobile');

db_open();
//stat_record();

//debug_mail(false,'api-gkserpapp-mobile.php '.microtime(true),'');
$return = array(
  'success' => false, 
  'message' => 'Δεν έχει ορισθεί το ID.',
  'frpc_enable' => false,
  'frpc_ini' => '',
  'port'=>54678,
);

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);

//echo '<pre>'.$HTTP_RAW_POST_DATA;die();
//echo '<pre>';print_r($input_data);die();

if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
	debug_mail(false,'json decode error',$HTTP_RAW_POST_DATA);
	$return['message']='json decode error';
	echo json_encode($return); die();}

	
if (isset($input_data['cmd'])==false or isset($input_data['token'])==false or isset($input_data['rand1'])==false or isset($input_data['semd5'])==false) {
	debug_mail(false,'parameters error',print_r($input_data, true));
	$return['message']='parameters error';echo json_encode($return); die();}

$cmd=trim_gks($input_data['cmd']);
$token=trim_gks($input_data['token']);
$rand1=trim_gks($input_data['rand1']);
$semd5=trim_gks($input_data['semd5']);

$sql="select * from gks_erp_app_mobile
where erp_app_mobile_token='".$db_link->escape_string($token)."'
and erp_app_mobile_disabled=0";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return['message']='sql error';echo json_encode($return); die();} 
if ($result->num_rows<>1) {	
  debug_mail(false,'token error',$sql);
  $return['message']='Το κλειδί είναι λάθος';echo json_encode($return); die();}
  
  
$row_app = $result->fetch_assoc();
$id_erp_app_mobile=intval($row_app['id_erp_app_mobile']);
$erp_app_mobile_name=$row_app['erp_app_mobile_name'];
$erp_app_mobile_url=trim_gks($row_app['erp_app_mobile_url']);
$erp_app_mobile_token=trim_gks($row_app['erp_app_mobile_token']);
$erp_app_mobile_secret=trim_gks($row_app['erp_app_mobile_secret']);
$erp_app_mobile_name=trim_gks($row_app['erp_app_mobile_name']);
$erp_app_mobile_port=intval($row_app['erp_app_mobile_port']);

$semd5_this=md5($rand1.$erp_app_mobile_secret);
if ($semd5_this!=$semd5) {
  debug_mail(false,'security error gks ERP App mobile','');
  $return['message']='Το κλειδί είναι λάθος';echo json_encode($return); die();}
  


  
//$return['message']=$erp_app_mobile_secret.'| '.$rand1.'| '.$semd5.'| '.$semd5_this; echo json_encode($return); die();

$frpc_enable=false;
if ($erp_app_mobile_url=='frp') $frpc_enable=true;

if (in_array($input_data['appver'],['1.0','1.1','1.2'])) {
$frpc_ini='[common]
server_addr = '.GKS_PROXY['SERVER'].'
server_port = '.GKS_PROXY['PORT'].'

admin_addr = 127.0.0.1
admin_port = 7400
admin_user = admin
admin_pwd = 6971881406

token='.GKS_PROXY['TOKEN'].'

# console or real logFile path like ./frpc.log
log_file = ./frpc.log
# trace, debug, info, warn, error
log_level = debug
log_max_days = 30

['.GKS_PROXY['HTTP_PREFIX'].$erp_app_mobile_token.'_'.trim_gks(str_replace(' ', '_', greeklish($erp_app_mobile_name))).']
type = http
local_port = '.$erp_app_mobile_port.'
custom_domains = '.$erp_app_mobile_token.GKS_PROXY['DOMAIN_BASE_NAME'].'
use_compression = true
header_X-From-Where = frp
';
} else {
$frpc_ini='serverAddr = "'.GKS_PROXY['SERVER'].'"
serverPort = '.GKS_PROXY['PORT'].'

webServer.addr = "'.GKS_PROXY['LWS']['addr'].'"
webServer.port = '.GKS_PROXY['LWS']['port'].'
webServer.user = "'.GKS_PROXY['LWS']['user'].'"
webServer.password = "'.GKS_PROXY['LWS']['pass'].'"

auth.method = "token"
auth.token="'.GKS_PROXY['TOKEN'].'"

# console or real logFile path like ./frpc.log
log.to = "./frpc.log"
# trace, debug, info, warn, error
log.level = "debug"
log.maxDays = 30

[[proxies]]
name="'.GKS_PROXY['HTTP_PREFIX'].$erp_app_mobile_token.'_'.trim_gks(str_replace(' ', '_', greeklish($erp_app_mobile_name))).'"
type = "http"
localPort = '.$erp_app_mobile_port.'
customDomains = ["'.$erp_app_mobile_token.GKS_PROXY['DOMAIN_BASE_NAME'].'"]
transport.useCompression = true
requestHeaders.set.x-from-where = "frp"
';  
  
}

//debug_mail(false,'frpc_ini',$frpc_ini);

switch ($cmd) {   
  case 'test_conn':
    $return['frpc_enable']=false;
    $return['frpc_ini']='';
    $return['port']=54678;
        

      
    $appver=''; if (isset($input_data['appver'])) $appver=trim_gks($input_data['appver']);
    $osver=''; if (isset($input_data['osver'])) $osver=trim_gks($input_data['osver']);
    $rand1=''; if (isset($input_data['rand1'])) $rand1=trim_gks($input_data['rand1']);
    $ostime=''; if (isset($input_data['ostime'])) $ostime=trim_gks($input_data['ostime']);
    $lanips=''; if (isset($input_data['lanips'])) $lanips=trim_gks($input_data['lanips']);
    $phonenumber=''; if (isset($input_data['phonenumber'])) $phonenumber=trim_gks($input_data['phonenumber']);
    $screw=0; if (isset($input_data['screw'])) $screw=intval($input_data['screw']);
    $screh=0; if (isset($input_data['screh'])) $screh=intval($input_data['screh']);
    
    $sql="insert into gks_erp_app_mobile_ping (
    erp_app_mobile_id,mydate,myip,
    appver,osver,rand1,ostime,lanips,phonenumber,
    screw,screh
    ) values (
    
    ".$id_erp_app_mobile.",
    now(),
    '".$db_link->escape_string($gkIP)."',
    '".$db_link->escape_string($appver)."',
    '".$db_link->escape_string($osver)."',
    '".$db_link->escape_string($rand1)."',
    '".$db_link->escape_string($ostime)."',
    '".$db_link->escape_string($lanips)."',
    '".$db_link->escape_string($phonenumber)."',
    ".$screw.",
    ".$screh."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return['message']='sql error';echo json_encode($return); die();} 

    $last_ping_id = $db_link->insert_id; 
    
    $this_lanip = $lanips;
    $parts=explode(',',$lanips);
    if (count($parts) <= 1) {
      $this_lanip = $lanips;    
    } else {
      $this_lanip='';
      foreach ($parts as $value) {
        if ($value!='192.168.137.1') {
          if (startwith($value,'192.168.1.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.2.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.3.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.4.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.5.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.10.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.20.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.30.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.40.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.43.')) {$this_lanip = $value; break;} 
          if (startwith($value,'10.10.')) {$this_lanip = $value; break;} 
          if (startwith($value,'192.168.0.')) {$this_lanip = $value; break;} 
        }
      } 
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'192.168.') and $value!='192.168.137.1') {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'10.10.')) {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'10.5.')) {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'10.')) {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'172.20.')) {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'172.30.')) {$this_lanip = $value; break;} 
        } 
      }
      if ($this_lanip == '') {
        foreach ($parts as $value) {
          if (startwith($value,'172.')) {$this_lanip = $value; break;} 
        } 
      }
      
      if ($this_lanip == '') $this_lanip = $parts[0];
      
    }
    
    $sql="update gks_erp_app_mobile set 
    ".($this_lanip!='' ? "erp_app_mobile_lan_ip='".$db_link->escape_string($this_lanip)."'," : '')."
    erp_app_mobile_wan_ip ='".$db_link->escape_string($gkIP)."',
    erp_app_mobile_last_ping=now(),
    mobile_last_ping_id=".$last_ping_id."
    where id_erp_app_mobile=".$id_erp_app_mobile;    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return['message']='sql error';echo json_encode($return); die();} 
    
    //$return['success']=false;$return['message']=print_r($input_data, true);echo json_encode($return); die();  
  
    $return['frpc_enable']=$frpc_enable;
    $return['frpc_ini']=$frpc_ini;
    $return['port']=$erp_app_mobile_port;
  
    $return['success']=true;$return['message']='Επιτυχής σύνδεση';echo json_encode($return); die();  
    break;  
  case 'scan':
    $content=''; if (isset($input_data['content'])) $content=trim_gks($input_data['content']);
    $format=''; if (isset($input_data['format'])) $format=trim_gks($input_data['format']);
    if ($content=='') {
      debug_mail(false,'empty content',$cmd);
      $return['message']='Κενό κείμενο. Ξαναδοκιμάστε';echo json_encode($return); die(); }

    $user_id=2;
    $sql="insert into gks_qrcode_scan (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    erp_app_mobile_id,app_id,mytext,format,result
    ) values (
    now(),now(),".$user_id.",".$user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id_erp_app_mobile.",
    '".$db_link->escape_string($erp_app_mobile_name)."',
    '".$db_link->escape_string($content)."',
    '".$db_link->escape_string($format)."',
    'new'
    )";    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return['message']='sql error';echo json_encode($return); die();} 
    $return['success']=true;$return['message']='Επιτυχής αποστολή';echo json_encode($return); die();  
    break;
  case 'sms_receive':
    $myto=$row_app['erp_app_mobile_phonenumber'];
    
    $sms_list=array();
    if (isset($input_data['sms_list'])) {
      $sms_list=$input_data['sms_list'];
    }
    if (count($sms_list)==0) {
      debug_mail(false,'sms_list is empty','');
      $return['message']='Δεν έχουν σταλεί τα δεδομένα των SMS που λήφθηκαν';echo json_encode($return); die();} 
      
    foreach ($sms_list as $mysms) {

      $format=''; if (isset($mysms['format'])) $format=trim_gks($mysms['format']);
      $message_id=''; if (isset($mysms['message_id'])) $message_id=trim_gks($mysms['message_id']);
      $myfrom=''; if (isset($mysms['myfrom'])) $myfrom=trim_gks($mysms['myfrom']);
      if (startwith($myfrom,'+')) $myfrom=substr($myfrom,1);
      $message='';if (isset($mysms['message'])) $message=trim_gks($mysms['message']);
      $my_Length=mb_strlen($message);
      $my_Smscount=1;
      $my_Parts=ceil($my_Length/160);
      $donedate=time();//if (isset($mysms['donedate'])) $donedate=intval($mysms['donedate']); 
      $donedate_date=date('Y-m-d H:i:s',$donedate);
      $my_status=1000;
      $my_status_name='Λήφθηκε';
      
      $my_cost=0; //$my_Parts*floatval($row_app['erp_app_mobile_cost_per_sms']);
      $my_points=0;  //$my_cost;
      $my_mcc=0;
      $my_mnc=0;
      $user_id=0;
      $my_myret=1;
      $my_sms_result='';
      
      
      
      
      $sql="insert into gks_sms (
      sms_provider,erp_app_mobile_id,
      sms_mms_type,sms_folder,
      sms_mobile_db_id,
      format,
      message_id,myfrom,myto,
      Message,Message_post,
      Length,Smscount,
      Parts,OK,
      donedate,donedate_date,
      status,status_name,
      cost,points,
      mcc,mnc,
      date_add,
      user_id,
      myret,
      sms_result
      ) values (
      'gks_erp_app_mobile',".$id_erp_app_mobile.",
      'sms','inbox',
      0,
      '".$db_link->escape_string($format)."',
      '".$db_link->escape_string($message_id)."','".$db_link->escape_string($myfrom)."','".$db_link->escape_string($myto)."',
      '".$db_link->escape_string($message)."','".$db_link->escape_string($message)."',
      ".$my_Length.",".$my_Smscount.",
      ".$my_Parts.",'ΟΚ:',
      ".$donedate.",'".$donedate_date."',
      ".$my_status.",'".$my_status_name."',
      ".$my_cost.",".$my_points.",
      ".$my_mcc.",".$my_mnc.",
      now(),
      ".$user_id.",
      ".$my_myret.",
      '".$db_link->escape_string($my_sms_result)."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
        $return['message']='sql error';echo json_encode($return); die();} 
    }
    
    $return['success']=true;$return['message']='Επιτυχής καταχώρηση';echo json_encode($return); die(); 
  
    break;  
    
    
  case 'msg_list':
    $return['cc_insert']=0;
    $return['cc_update']=0;
    $return['cc_change']=0;
    
    //from_recieve from_sent all
    $my_call_from='';if (isset($input_data['my_call_from'])) $my_call_from=$input_data['my_call_from'];
    
    $msg_list=array();
    if (isset($input_data['msg_list'])) {
      $msg_list=$input_data['msg_list'];
    }
    if (count($msg_list)==0) {
      debug_mail(false,'msg_list is empty','');
      $return['message']='Δεν έχουν σταλεί τα δεδομένα των SMS';echo json_encode($return); die();} 
    
    $cc_insert=0;
    $cc_update=0;
    $cc_change=0;
    foreach ($msg_list as $mysms) {

      $folder=''; 
      $sms_mms_type='sms'; 
      $mytype=-1; if (isset($mysms['type'])) $mytype=intval($mysms['type']); 
      switch ($mytype) {   
        case 1: $folder='inbox'; break;  
        case 2: $folder='sent'; break;  
        case 3: $folder='draft'; break;  
        case 4: $folder='outbox'; break;  
        case 5: $folder='failed'; break;  
 
        default:
          $folder='unknown';
      }
      
      $format=''; if (isset($mysms['format'])) $format=trim_gks($mysms['format']);
      $message_id=''; if (isset($mysms['message_id'])) $message_id=trim_gks($mysms['message_id']);
      $myfrom=''; if (isset($mysms['myfrom'])) $myfrom=trim_gks($mysms['myfrom']);
      if (startwith($myfrom,'+')) $myfrom=substr($myfrom,1);
      
      $message='';if (isset($mysms['message'])) $message=trim_gks($mysms['message']);
      $my_Length=mb_strlen($message);
      $my_Smscount=1;
      $my_Parts=ceil($my_Length/160);
      
      $date_add='';
      $donedate=0;
      $donedate_date='null';
      
      
      $status=-1; if (isset($mysms['status'])) $status=intval($mysms['status']); 
      $mydate='';if (isset($mysms['mydate'])) $mydate=trim_gks($mysms['mydate']); 
      $mydate_send='';if (isset($mysms['mydate_send'])) $mydate_send=trim_gks($mysms['mydate_send']); 
      
      if ($mydate!='') {
        $date_add=date('Y-m-d H:i:s',strtotime($mydate));
        //$donedate=strtotime($date_add);
        //$donedate_date=$date_add;
      }
      if ($mydate_send!='') {
        $donedate=strtotime($mydate_send);
        $donedate_date="'".date('Y-m-d H:i:s',$donedate)."'";
      }
      if ($folder=='inbox') {
        if ($donedate==0) {
          $donedate=strtotime($mydate);
          $donedate_date="'".date('Y-m-d H:i:s',$donedate)."'";
        } 
      } else if ($status==0 and $folder=='sent') {
        if ($donedate==0) {
          $donedate=time();
          $donedate_date="'".date('Y-m-d H:i:s',$donedate)."'";          
        } 
      }
      
      //$message='['.$mydate.'] ['.$mydate_send.'] ['.$myfrom.'] '.$message;
      
      $my_status=0;
      $my_status_name='';
      
      if ($folder=='inbox') {
        $my_status=1000;
        $my_status_name='Λήφθηκε';
      } else if ($folder=='outbox') {
        $my_status=409;
        $my_status_name='Σε ουρά';
      } else if ($folder=='draft' or $folder=='drafts') {
        $my_status=-100;
        $my_status_name='Πρόχειρο';        
      } else {
        
        switch ($status) {   //TP-Status} value for the message, or -1 if no status has been received.
          case -1: //STATUS_NONE
            $my_status=403;
            $my_status_name='Στάλθηκε';
            break;
          case 0: //STATUS_COMPLETE
            $my_status=404;
            $my_status_name='Παραδόθηκε';
            break;
          case 32: //STATUS_PENDING
            $my_status=409;
            $my_status_name='Σε ουρά';
            break;
          case 64: //STATUS_FAILED
            $my_status=406;
            $my_status_name='Αποτυχία';
            break;
          
          
          default:
            //$my_status=  $status;
            //$my_status_name='Άγνωστο';
        }        
      }
      $sms_mobile_db_id=0; if (isset($mysms['rec_id'])) $sms_mobile_db_id=intval($mysms['rec_id']);
      
      
      $my_cost=$my_Parts*floatval($row_app['erp_app_mobile_cost_per_sms']);
      $my_points=$my_cost;
      $my_mcc=0;
      $my_mnc=0;
      $my_myret=1;
      $my_sms_result='';
      
      $myto=$row_app['erp_app_mobile_phonenumber'];
      if ($folder=='inbox') {
        //$myto=$myfrom;
        //$myfrom=$row_app['erp_app_mobile_phonenumber'];
      } else { //sent draft outbox
        $myto=$myfrom;//.'-aaaa';
        $myfrom=$row_app['erp_app_mobile_phonenumber'];//.'-bbb';
      }
      if ($folder=='inbox' or $folder=='draft' or $folder=='drafts' or $folder=='outbox') {
        $my_cost=0;$my_points=0;
      }
      
      
      
      $status_old=0;
      
      $sql="select id,status,donedate,sms_folder
      from gks_sms
      where sms_provider='gks_erp_app_mobile'
      and erp_app_mobile_id=".$id_erp_app_mobile."
      and sms_mms_type='".$db_link->escape_string($sms_mms_type)."'
      and sms_mobile_db_id=".$sms_mobile_db_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
        $return['message']='sql error';echo json_encode($return); die();} 
      
      $id=0;
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $id=$row['id'];
        $status_old=intval($row['status']);
        $donedate_old=intval($row['donedate']);
        $sms_folder_old=$row['sms_folder'];
      }
      
      //mazi me message
      if ($id==0 && $folder=='sent') {
        $date_add_bottom=date('Y-m-d H:i:s',strtotime($date_add) + 10);
        
        $sql="select id,status,donedate,sms_folder
        from gks_sms
        where sms_provider='gks_erp_app_mobile'
        and erp_app_mobile_id=".$id_erp_app_mobile."
        and sms_mms_type='".$db_link->escape_string($sms_mms_type)."'
        and sms_folder='".$db_link->escape_string($folder)."'
        and sms_mobile_db_id=0
        and myfrom='".$db_link->escape_string($myfrom)."'
        and myto='".$db_link->escape_string($myto)."'
        and date_add<='".$date_add."'
        and date_add<='".$date_add_bottom."'
        and myret=1
        and Message_post='".$db_link->escape_string($message)."'
        order by date_add 
        ".($my_call_from=='all' ? 'asc' : 'desc' )."
        limit 1";
        //debug_mail(false,'sql',$sql);
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
          $return['message']='sql error';echo json_encode($return); die();} 
        

        if ($result->num_rows>0) {
          $row = $result->fetch_assoc();
          $id=$row['id'];
          $status_old=intval($row['status']);
          $donedate_old=intval($row['donedate']);
          $sms_folder_old=$row['sms_folder'];
          $sql="update gks_sms set
          sms_mobile_db_id=".$sms_mobile_db_id."
          where id=".$id;
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
            $return['message']='sql error';echo json_encode($return); die();} 
        }        
      }
      
      
      //xoris message
      if ($id==0 && $folder=='sent') {
        $date_add_bottom=date('Y-m-d H:i:s',strtotime($date_add) + 10);
        
        $sql="select id,status,donedate,sms_folder
        from gks_sms
        where sms_provider='gks_erp_app_mobile'
        and erp_app_mobile_id=".$id_erp_app_mobile."
        and sms_mms_type='".$db_link->escape_string($sms_mms_type)."'
        and sms_folder='".$db_link->escape_string($folder)."'
        and sms_mobile_db_id=0
        and myfrom='".$db_link->escape_string($myfrom)."'
        and myto='".$db_link->escape_string($myto)."'
        and date_add<='".$date_add."'
        and date_add<='".$date_add_bottom."'
        and myret=1
        order by date_add 
        ".($my_call_from=='all' ? 'asc' : 'desc' )."
        limit 1";
        //debug_mail(false,'sql',$sql);
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
          $return['message']='sql error';echo json_encode($return); die();} 
        

        if ($result->num_rows>0) {
          $row = $result->fetch_assoc();
          $id=$row['id'];
          $status_old=intval($row['status']);
          $donedate_old=intval($row['donedate']);
          $sms_folder_old=$row['sms_folder'];
          $sql="update gks_sms set
          sms_mobile_db_id=".$sms_mobile_db_id."
          where id=".$id;
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
            $return['message']='sql error';echo json_encode($return); die();} 
        }        
      }
      
      
      if ($id>0) {
        if ($sms_folder_old<>'inbox' and $status_old<>404) {
          
          $sql_status='';
          
          if ($status_old==403 and $my_status==409) {
            //na min allazei to  Στάλθηκε se Σε ουρά
          } else if ($my_status!=0)  {
            $sql_status="status=".$my_status.",status_name='".$my_status_name."',";
          }
          
          $sql_donedate='';
          if ($donedate_old==0 and $status==0) {
            if ($donedate>0) {
              $sql_donedate="donedate=".$donedate.",donedate_date='".date('Y-m-d H:i:s',$donedate)."',";
            } else {
              $sql_donedate="donedate=".time().",donedate_date='".date('Y-m-d H:i:s')."',";
            }
          }
          
          
          if ($sql_status!='' or $sql_donedate!='') {
            $sql="update gks_sms set
            ".$sql_status."
            ".$sql_donedate."
            sms_folder='".$db_link->escape_string($folder)."'
            where id=".$id." limit 1";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
              $return['message']='sql error';echo json_encode($return); die();} 
              
            $cc_change+=$db_link->affected_rows;
            $cc_update++;  
          }
          
        }
        
        
      } else {
        
        $user_id=0;if (isset($mysms['user_id'])) $user_id=intval($mysms['user_id']);
        $model="";if (isset($mysms['model'])) $model=trim_gks($mysms['model']);
        $model_id=0;if (isset($mysms['model_id'])) $model_id=intval($mysms['model_id']);
        
        
        $sql="insert into gks_sms (
        sms_provider,erp_app_mobile_id,
        sms_mms_type,sms_folder,
        sms_mobile_db_id,
        format,
        message_id,
        myfrom,myto,
        Message,Message_post,
        Length,Smscount,
        Parts,OK,
        donedate,donedate_date,
        status,status_name,
        cost,points,
        mcc,mnc,
        date_add,
        myret,
        sms_result,
        
        user_id,
        model,
        model_id
        ) values (
        'gks_erp_app_mobile',".$id_erp_app_mobile.",
        '".$db_link->escape_string($sms_mms_type)."','".$db_link->escape_string($folder)."',
        ".$sms_mobile_db_id.",
        '".$db_link->escape_string($format)."',
        '".$db_link->escape_string($message_id)."',
        '".$db_link->escape_string($myfrom)."','".$db_link->escape_string($myto)."',
        '".$db_link->escape_string($message)."','".$db_link->escape_string($message)."',
        ".$my_Length.",".$my_Smscount.",
        ".$my_Parts.",'ΟΚ:',
        ".$donedate.",".$donedate_date.",
        ".$my_status.",'".$my_status_name."',
        ".$my_cost.",".$my_points.",
        ".$my_mcc.",".$my_mnc.",
        '".$date_add."',
        ".$my_myret.",
        '".$db_link->escape_string($my_sms_result)."',
        
        ".$user_id.",
         '".$db_link->escape_string($model)."',
        ".$model_id."
        
        )";
        $result = $db_link->query($sql);  
        if (!$result) {
          //file_put_contents('../../tmp/sql.sql',$sql);
          debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
          $return['message']='sql error';echo json_encode($return); die();} 
        
        $sms_id = $db_link->insert_id; 
        
        $cc_insert++;
        
        
        
        
        //notification
        if ($my_call_from!='all' and $folder=='inbox' and strtotime($date_add) > (time()-24*60*60)) {
          
          $mysubject='Νέο SMS από: '.$myfrom;
          $message_email='Νέο SMS από: <a href="'.GKS_SITE_URL.'my/admin-sms-chat.php#number='.$myfrom.'">#'.$myfrom.'</a><br>'.$message;
          
          $replaces=array();
          $replaces[] = array('[[message]]', $message_email);
          
          $params=array(
            'model'=>'sms',
            'model_id'=>$sms_id,
            'to'=>$GKS_SITE_EMAIL,
            'subject'=>$mysubject,
            'template'=>3, //'empty.html',
            'replaces'=>$replaces,
          );
          
          $send_email_res = gks_mymail_template($params);
          
          $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
          FROM gks_notification_userperm 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
          AND gks_notification_userperm.notification_type_id=150 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
          //debug_mail(false,'sql',$sql);
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
            echo json_encode($return); die(); }  
          $send_viber=array();
          while ($row = $result->fetch_assoc()) {
            if ($row['user_email']!=$GKS_SITE_EMAIL) {
              $params=array(
                'model'=>'sms',
                'model_id'=>$sms_id,
                'to'=>$row['user_email'],
                'subject'=>$mysubject,
                'template'=>3, //'empty.html',
                'replaces'=>$replaces,
              );
                  
              $send_email_res = gks_mymail_template($params);
            }
          }


          $message_notification='Νέο SMS από: <a href="'.GKS_SITE_URL.'my/admin-sms-chat.php#number='.$myfrom.'">#'.$myfrom.'</a><br>'.$message;
          
          $sql="insert into gks_notification (
          message,for_user_id,`date_add`,for_date,has_ok,model,model_id
          )
          select
          '".$db_link->escape_string($message_notification)."' as message,
          user_id as for_user_id,
          now() as `date_add`,
          now() as `for_date`,
          0 as has_ok,
          'sms' as model,
          ".$sms_id." as model_id
          from gks_notification_userperm where notification_type_id=150 and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
          //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%administrator%' or gks_wp_capabilities like '%adminmy%';";
          
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
            $return['message']='sql error';echo json_encode($return); die();}           
          
          
          $message_viber='Νέο SMS από: '.$myfrom."\n".$message."\n".GKS_SITE_URL.'my/admin-sms-chat.php#number='.$myfrom;
          
          $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
          FROM gks_notification_userperm 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
          AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
          AND gks_notification_userperm.notification_type_id=150 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
          //debug_mail(false,'sql',$sql);
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
            echo json_encode($return); die(); }  
          $send_viber=array();
          while ($row = $result->fetch_assoc()) {
            $send_viber[]=$row['viber_id'];
          }
          foreach ($send_viber as $value) {
            $subject = (isset($_POST['subject']) ? "\nΘέμα: ".trim_gks($_POST['subject']) : '');
            gks_viber_send('sms',$sms_id,$value,$message_viber);
          }          
          
          
          
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
      }
    }
  
    $return['cc_insert']=$cc_insert;
    $return['cc_update']=$cc_update;
    $return['cc_change']=$cc_change;
    
    $return['success']=true;$return['message']='Επιτυχής καταχώρηση';echo json_encode($return); die(); 
    
    break;

  case 'sms_send':
    $rec_id=0; if (isset($input_data['rec_id'])) $rec_id=intval($input_data['rec_id']);
    $resultCode=-100; if (isset($input_data['resultCode'])) $resultCode=intval($input_data['resultCode']);
    $messageInfo=''; if (isset($input_data['messageInfo'])) $messageInfo=trim_gks($input_data['messageInfo']);

    switch ($resultCode) {   //TP-Status} value for the message, or -1 if no status has been received.
      case -1: //RESULT_OK
        $my_status=403;
        $my_status_name='Στάλθηκε';
        break;
      default:
        $my_status=406;
        $my_status_name=$messageInfo;        
        break;
    }
    
    
    if ($rec_id>0) {
      $sql="update gks_sms set 
      status=".$my_status.",
      status_name='".$db_link->escape_string($my_status_name)."',
      myret=1
      where id=".$rec_id;
      $result = $db_link->query($sql);
      if (!$result) {
        //file_put_contents('../../tmp/sql.sql',$sql);
        debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
        $return['message']='sql error';echo json_encode($return); die();} 
      
      echo 'OK'; die();
    
    }
    echo 'error';die();
    
    break;   
    
       
  case 'sms_delivery':
    $rec_id=0; if (isset($input_data['rec_id'])) $rec_id=intval($input_data['rec_id']);
    $resultCode=-100; if (isset($input_data['resultCode'])) $resultCode=intval($input_data['resultCode']);
    $status=-100; if (isset($input_data['status'])) $status=intval($input_data['status']);
    $messageInfo=''; if (isset($input_data['messageInfo'])) $messageInfo=trim_gks($input_data['messageInfo']);

    switch ($status) {   //TP-Status} value for the message, or -1 if no status has been received.
      case 0: //STATUS_COMPLETE
        $my_status=404;
        $my_status_name='Παραδόθηκε';
        break;
      case 32: //STATUS_PENDING
        $my_status=409;
        $my_status_name='Σε ουρά';
        break;
      case 64: //STATUS_FAILED
        $my_status=406;
        $my_status_name='Αποτυχία';
        break;

      default:
        $my_status=406;
        $my_status_name=$messageInfo;        
        break;
    }
    
    
    if ($rec_id>0) {
      $donedate=time();//if (isset($mysms['donedate'])) $donedate=intval($mysms['donedate']); 
      $donedate_date=date('Y-m-d H:i:s',$donedate);
      $sql="update gks_sms set 
      status=".$my_status.",
      status_name='".$db_link->escape_string($my_status_name)."',
      myret=1,
      donedate=".$donedate.",
      donedate_date='".$donedate_date."'
      
      where id=".$rec_id." and status<>".$my_status;
      $result = $db_link->query($sql);
      if (!$result) {
        //file_put_contents('../../tmp/sql.sql',$sql);
        debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
        $return['message']='sql error';echo json_encode($return); die();} 
      
      echo 'OK'; die();
    
    }
    echo 'error';die();
    
    break;      

  case 'sms_failed':
    $rec_id=0; if (isset($input_data['rec_id'])) $rec_id=intval($input_data['rec_id']);
    $resultCode=-100; if (isset($input_data['resultCode'])) $resultCode=intval($input_data['resultCode']);
    $messageInfo=''; if (isset($input_data['messageInfo'])) $messageInfo=trim_gks($input_data['messageInfo']);

    $my_status=406;
    $my_status_name=$messageInfo;        
    
    if ($rec_id>0) {
      $sql="update gks_sms set 
      status=".$my_status.",
      status_name='".$db_link->escape_string($my_status_name)."',
      myret=0
      where id=".$rec_id;
      $result = $db_link->query($sql);
      if (!$result) {
        //file_put_contents('../../tmp/sql.sql',$sql);
        debug_mail(false,'error sql',$sql.'<br>'.$db_link->errno . '-'.$db_link->error);
        $return['message']='sql error';echo json_encode($return); die();} 
      
      echo 'OK'; die();
    
    }
    echo 'error';die();
    
    break;   
  default:
    debug_mail(false,'cmd error',$cmd);
    $return['message']='Λάθος εντολή';echo json_encode($return); die();  
    break;      
}
  
echo '<pre>';print_r($input_data);die();
	
	




$sql="insert into gks_qrcode_scan (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
app_id,mytext,format,result
) values (
now(),now(),".$user_id.",".$user_id.",'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($input_data['id'])."',
'".$db_link->escape_string($input_data['url'])."',
'',
'new'
)";
$result = $db_link->query($sql);  
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); } 

echo 'OK'; die();
