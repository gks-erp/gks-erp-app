<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$gks_debug=false;

$mytime = time();
if ($gks_debug) $mytime -= 2*24*60*60; //remove me




$my_page_title=gks_lang('gks ERP App Desktop - Λήψη Δεδομένων');     
db_open();
stat_record();

//debug_mail(false,'api-ergastirio-get-data2','');





$time_vardia=_time_user($mytime, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia_time);



//$gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
if (isset($_POST['ti'])==false) $_POST['ti']='';
if (isset($_POST['u'])==false) $_POST['u']='';
if (isset($_POST['m'])==false) $_POST['m']='';
if (isset($_POST['r'])==false) $_POST['r']='';
if (isset($_POST['t'])==false) $_POST['t']='';
if (isset($_POST['w'])==false) $_POST['w']='';
if (isset($_POST['v'])==false) $_POST['v']='';
if (isset($_POST['sc'])==false) $_POST['sc']='';
if (isset($_POST['screw'])==false) $_POST['screw']='';
if (isset($_POST['screh'])==false) $_POST['screh']='';
if (isset($_POST['arc'])==false) $_POST['arc']='';
if (isset($_POST['lanips'])==false) $_POST['lanips']='';
if (isset($_POST['hdwd'])==false) $_POST['hdwd']='';
if (isset($_POST['local_printers'])==false) $_POST['local_printers']='';


$myParameters='';
$myParameters.='ti='.urldecode($_POST['ti']);
$myParameters.='&u='.urldecode($_POST['u']);
$myParameters.='&m='.urldecode($_POST['m']);
$myParameters.='&r='.urldecode($_POST['r']);
$myParameters.='&t='.urldecode($_POST['t']);
$myParameters.='&w='.urldecode($_POST['w']);
$myParameters.='&v='.urldecode($_POST['v']);
$myParameters.='&sc='.urldecode($_POST['sc']);
$myParameters.='&screw='.urldecode($_POST['screw']);
$myParameters.='&screh='.urldecode($_POST['screh']);

if (isset($_POST['arc'])) {
  $myParameters.='&arc='.urldecode($_POST['arc']);
}


$myParameters=str_replace(' ', '%20', $myParameters);
$myParameters=str_replace(':', '%3A', $myParameters);


$lanips=''; if (isset($_POST['lanips'])) $lanips= urldecode($_POST['lanips']);
$hdwd=0; if (isset($_POST['hdwd'])) $hdwd= intval($_POST['hdwd']);


//echo $myParameters;
//die();



$local_printers=base64_decode(urldecode($_POST['local_printers']));
//print $local_printers;die();
$erp_app_local_printers=json_decode($local_printers,true);
if ($erp_app_local_printers === null && json_last_error() !== JSON_ERROR_NONE) {
  $erp_app_local_printers=false;
}
//print_r($erp_app_local_printers);die();
$token=urldecode($_POST['sc']);

$sql="SELECT * from gks_erp_app
where erp_app_disabled=0 and erp_app_token<> '' and erp_app_secret<>'' and erp_app_token='".$db_link->escape_string($token)."' limit 1";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  echo 'error:sql error';
  die();
}

if ($result->num_rows != 1) {
  debug_mail(false,'error login gks ERP App Desktop',urldecode($_POST['sc']));
  echo 'error:'.gks_lang('Το κλειδί είναι λάθος');
  die();  
}
$row= $result->fetch_assoc();

$id_erp_app=$row['id_erp_app'];
$erp_app_name = $row['erp_app_name'];
$erp_app_token = $row['erp_app_token'];
$erp_app_secret = $row['erp_app_secret'];
$erp_app_url = trim_gks($row['erp_app_url']);
$erp_app_port = $row['erp_app_port'];
$voip_localdb = trim_gks($row['voip_localdb']);
//$voip_localdb='server=192.168.1.111; database=gks_voip;port=3307; user=gks_voip;password=gks_voip236712ffsdf89766;charset=utf8;';
$voip_ip = trim_gks($row['voip_ip']);
$voip_AIM_port = intval($row['voip_AIM_port']);
$voip_AIM_username = trim_gks($row['voip_AIM_username']);
$voip_AIM_password = trim_gks($row['voip_AIM_password']);
$voip_call_originate = intval($row['voip_call_originate'])==1;
$voip_call_monitoring = intval($row['voip_call_monitoring'])==1;


$rnd1s='';if (isset($_POST['r'])) $rnd1s= urldecode($_POST['r']);
$send1=''; if (isset($_POST['send1'])) $send1= urldecode($_POST['send1']);

$send1_calc= md5($rnd1s . $rnd1s . $erp_app_token . $rnd1s .$erp_app_secret.  GKS_ERP_HASHMD5KEY09);

$is_first_login=false;
if ($send1 != $send1_calc) {
  $send1_calc= md5($rnd1s . $rnd1s . $erp_app_token . $rnd1s .$erp_app_secret.  '');
  
  //mipos einai i proti fora kai ara ta keys einai kena
  if ($send1 != $send1_calc) {
  
    debug_mail(false,'security error','');
    echo 'error:'.gks_lang('Το δημόσιο ή/και το ιδιωτικό κλειδί είναι λάθος');
    die(); 
  } else {
    $is_first_login=true;  
  }
}
if ($is_first_login) {
  $responseok = md5($rnd1s . $erp_app_token . $rnd1s . $erp_app_secret);
  $myfiles=[];
} else {
  $responseok = md5($rnd1s . $erp_app_token . $rnd1s . $erp_app_secret .  GKS_ERP_HASHMD5KEY10);
  $myfiles=file_get_contents('https://tools.gks.gr/gks_erp_app/exe_files.php');
  $myfiles=json_decode($myfiles,true);
}





$lanips=''; if (isset($_POST['lanips'])) $lanips= urldecode($_POST['lanips']);
$hdwd=0; if (isset($_POST['hdwd'])) $hdwd= intval($_POST['hdwd']);

$mac_address=trim_gks(urldecode($_POST['mac']));
$mac_array=array();
if ($mac_address!='') {
  $temp_macs=explode('|',$mac_address); 
  foreach ($temp_macs as $value) {
    if (strlen($value)==12 and $value!='00000000000000E0') {
      $mac_array[]=$value;
    }
  }
}


$sql="insert into gks_erp_app_ping (erp_app_id,mydate,myip,pctime,pcusername,pcname,rand1,ticks,winver,appver,lanips,hdwd,screw,screh,mac,arc) values (
".$id_erp_app.",
now(),
'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string(urldecode($_POST['ti']))."',
'".$db_link->escape_string(urldecode($_POST['u']))."',
'".$db_link->escape_string(urldecode($_POST['m']))."',
'".$db_link->escape_string(urldecode($_POST['r']))."',
'".$db_link->escape_string(urldecode($_POST['t']))."',
'".$db_link->escape_string(urldecode($_POST['w']))."',
'".$db_link->escape_string(urldecode($_POST['v']))."',
'".$db_link->escape_string($lanips)."',
".$hdwd.",

".(isset($_POST['screw']) ? intval($_POST['screw']) : '0').",
".(isset($_POST['screh']) ? intval($_POST['screh']) : '0').",
'".(count($mac_array)>0 ? $db_link->escape_string(implode('|',$mac_array)) : '')."',
'".$db_link->escape_string(urldecode($_POST['arc']))."'
)";

$myrun = $db_link->query($sql);
if (!$myrun) {
  debug_mail(false,'error sql',$sql);
  echo 'error:sql error';
  die();  
}
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


$sql="update gks_erp_app set 
".($this_lanip!='' ? "erp_app_lan_ip='".$db_link->escape_string($this_lanip)."'," : '')."
erp_app_wan_ip ='".$db_link->escape_string($gkIP)."',
erp_app_last_ping=now(),
last_ping_id=".$last_ping_id."
where id_erp_app=".$id_erp_app;
$myrun = $db_link->query($sql);
if (!$myrun) {
  debug_mail(false,'error sql',$sql);
  echo 'error:sql error';
  die();   
}


if ($erp_app_local_printers!==false and is_array($erp_app_local_printers)) {
  $sql="update gks_erp_app set 
  erp_app_local_printers ='".$db_link->escape_string(serialize($erp_app_local_printers))."'
  where id_erp_app=".$id_erp_app;
  $myrun = $db_link->query($sql);
  if (!$myrun) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error';
    die();   
  }
}


//echo 'error:ggggggggggsss';die();




$frpc_enable=false;
if ($erp_app_url=='frp') $frpc_enable=true;

$frpc_ini='[common]
server_addr = '.GKS_PROXY['SERVER'].'
server_port = '.GKS_PROXY['PORT'].'

admin_addr = '.GKS_PROXY['LWS']['addr'].'
admin_port = '.GKS_PROXY['LWS']['port'].'
admin_user = '.GKS_PROXY['LWS']['user'].'
admin_pwd = '.GKS_PROXY['LWS']['pass'].'

token='.GKS_PROXY['TOKEN'].'

# console or real logFile path like ./frpc.log
log_file = ./frpc.log
# trace, debug, info, warn, error
log_level = debug
log_max_days = 3

[web_gks_erp_app_'.$erp_app_token.'_'.trim_gks(str_replace(' ', '_', greeklish($erp_app_name))).']
type = http
local_port = '.$erp_app_port.'
custom_domains = '.$erp_app_token.GKS_PROXY['DOMAIN_BASE_NAME'].'
use_compression = true
header_X-From-Where = frp

';



$return = array(
'myreturnmd5' => $responseok, 
'erp_app_id' => $id_erp_app,
'erp_app_site_url' =>  GKS_SITE_URL,
'erp_app_token' =>  $erp_app_token,
'erp_app_name' =>  $erp_app_name,
'erp_app_url' =>  $erp_app_url,
'erp_app_port' =>  $erp_app_port,
'exe_files' => $myfiles,
'frpc_enable' => $frpc_enable,
'frpc_ini' => $frpc_ini,
'update_me_url' => 'https://tools.gks.gr/gks_erp_app/files/',
'voip_localdb'=>$voip_localdb,
'voip_ip'=>$voip_ip,
'voip_AIM_port'=>$voip_AIM_port,
'voip_AIM_username'=>$voip_AIM_username,
'voip_AIM_password'=>$voip_AIM_password,
'voip_call_originate'=>$voip_call_originate,
'voip_call_monitoring'=>$voip_call_monitoring,

);

if ($is_first_login) {
  $return['GKS_ERP_HASHMD5KEY09']=GKS_ERP_HASHMD5KEY09;
  $return['GKS_ERP_HASHMD5KEY10']=GKS_ERP_HASHMD5KEY10;
  $return['GKS_ERP_HASHMD5KEY13']=GKS_ERP_HASHMD5KEY13;
  $return['GKS_ERP_HASHMD5KEY15']=GKS_ERP_HASHMD5KEY15;
}

//'SplitJobTimeGap_Minutes' => 0,
//'SplitJobFilesLength_Count' => 0,

echo json_encode($return); 



die(); 





