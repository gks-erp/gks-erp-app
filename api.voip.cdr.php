<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// /my/api.voip.cdr.php
 
ini_set('max_execution_time', 5);
set_time_limit(5);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);

//$HTTP_RAW_POST_DATA='{ "AcctId": "3030", "src": "2310690797", "dst": "331", "dcontext": "from-internal", "clid": "\"2310690797\" <2310690797>", "channel": "PJSIP\/trunk_1-00000a4a", "dstchannel": "PJSIP\/331-00000a52", "lastapp": "Dial", "lastdata": "PJSIP\/111\/sip:111@192.168.1.9:49383&PJSIP\/115&PJSIP\/125\/sip:125@192.168.1.17:40886&PJSIP\/221\/sip:221@192.168.1.13:5060&PJSIP\/222\/sip:222@192.168.1.13:5062&PJSIP\/331\/sip:331@127.0.0.1:47022;transport=TLS&PJSIP\/399\/sip:399@127.0.0.1:47024;transport=TLS,60,i", "start": "2026-01-26 12:07:08", "answer": "2026-01-26 12:07:12", "end": "2026-01-26 12:07:12", "duration": "3", "billsec": "0", "disposition": "NO ANSWER", "amaflags": "DOCUMENTATION", "uniqueid": "1769422021.5655", "userfield": "Inbound", "channel_ext": "trunk_1", "dstchannel_ext": "331", "service": "s", "caller_name": "2310690797", "session": "1769422021553915-2310690797", "action_owner": "2310690797", "action_type": "RINGGROUP[6400]", "src_trunk_name": "Intertelecom", "dst_trunk_name": "", "new_src": "2310690797", "reason": "", "sn": "35100PE5FE" }';

$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
  $return['message']=base64_encode('json decode error');debug_mail(false,'json decode Error','');echo json_encode($return); die();
}
//debug_mail(false,'api.voip.cdr',print_r($input_data,true));

if (is_array($input_data)==false) die();

$myf=[]; $myv=[];$not_in_list=[];
$myf[]='mydate_add';  $myv[]='now()';
$myf[]='mydate_edit'; $myv[]='now()';
$myf[]='user_id_add'; $myv[]='2';
$myf[]='user_id_edit';$myv[]='2';
$myf[]='myip';        $myv[]="'".$db_link->escape_string($gkIP)."'";

foreach ($input_data as $key => $val) {
  
  switch ($key) {   
    case 'AcctId':        $myf[]=$key;$myv[]=intval($val);break;  
    case 'src':           $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'dst':           $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'dcontext':      $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'clid':          $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,190))."'";break;  
    case 'channel':       $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,190))."'";break;  
    case 'dstchannel':    $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,190))."'";break;  
    case 'lastapp':       $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'lastdata':      $myf[]=$key;$myv[]="'".$db_link->escape_string($val)."'";break;  
    case 'start':         $myf[]=$key;$myv[]="'".$db_link->escape_string($val)."'";break;  
    case 'answer':        $myf[]=$key;$myv[]="'".$db_link->escape_string($val)."'";break;  
    case 'end':           $myf[]=$key;$myv[]="'".$db_link->escape_string($val)."'";break;  
    case 'duration':      $myf[]=$key;$myv[]=intval($val);break;    
    case 'billsec':       $myf[]=$key;$myv[]=intval($val);break;    
    case 'disposition':   $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'amaflags':      $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'uniqueid':      $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'userfield':     $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'channel_ext':   $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'dstchannel_ext':$myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'service':       $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'caller_name':   $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,190))."'";break;  
    case 'dstanswer':     $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'recordfiles':   $myf[]=$key;$myv[]="'".$db_link->escape_string($val)."'";break;  
    case 'session':       $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,190))."'";break;  
    case 'action_owner':  $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'action_type':   $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'src_trunk_name':$myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'dst_trunk_name':$myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'new_src':       $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'reason':        $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    case 'sn':            $myf[]=$key;$myv[]="'".$db_link->escape_string(substr(trim_gks($val),0,64))."'";break;  
    
    default:
      $not_in_list[]=$key;
  }
} 

$sql="insert into gks_voip_calls (".
implode(',',$myf).
") values (".
implode(',',$myv).
")";

$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$db_link->errno . '-'.$db_link->error); die();}

if (count($not_in_list)>0) {
  debug_mail(false,'api.voip.cdr not in list',print_r($not_in_list,true));
}
echo time();
die();
