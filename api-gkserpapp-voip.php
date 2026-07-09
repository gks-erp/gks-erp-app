<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');


//debug_mail(false,'api-gkserpapp-eftpos.php','');

//$gks_debug=false;
//$mytime = time();
//if ($gks_debug) $mytime -= 2*24*60*60; //remove me

$my_wp_user_id=2;

$rnd1s='';
if (isset($_GET['rnd1s'])) $rnd1s=trim($_GET['rnd1s']);

$send1='';
if (isset($_GET['send1'])) $send1=trim($_GET['send1']);

$id_erp_app=0;
if (isset($_GET['id'])) $id_erp_app = intval($_GET['id']);

$data_read = file_get_contents( 'php://input' );
//if ($ergastirio_id == 84) {
//  debug_mail(false,'api-ergastirio-printerinfo2',$data_read);
//}
//$data = json_encode($data_read,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
$data = json_decode($data_read, true,512,JSON_INVALID_UTF8_IGNORE);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'empty RAW',$data_read);
  die();
}
//debug_mail(false,'data',$data_read."\r\n".print_r($data,true));
//echo $data_read;print_r($data);  die();

$my_page_title=gks_lang('gks ERP App - voip');     
db_open();
stat_record();

if ($rnd1s=='' or $send1=='' or $id_erp_app<=0 or $data==='') {
  debug_mail(false,'empty data','');
  die();  
}
$sql="SELECT * from gks_erp_app
where erp_app_disabled=0 and erp_app_token<> '' and id_erp_app=".$id_erp_app;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  echo 'error:sql error';die();}
if ($result->num_rows != 1) {
  debug_mail(false,'error gks ERP App','');
  echo 'error:'.gks_lang('Το κλειδί είναι λάθος'); die();}

$row_erp_app=$result->fetch_assoc();
$erp_app_token=$row_erp_app['erp_app_token'];
$erp_app_secret=$row_erp_app['erp_app_secret'];

$send1_calc= md5($rnd1s . $rnd1s . $id_erp_app . $erp_app_token . $rnd1s .$erp_app_secret.  GKS_ERP_HASHMD5KEY13);

if ($send1 != $send1_calc) {
  debug_mail(false,'security error','');
  echo 'error';
  die(); 
}

$responseok = md5($rnd1s . $id_erp_app . $erp_app_token . $erp_app_secret .  GKS_ERP_HASHMD5KEY15);






$gks_event=''; if (isset($data['Event'])) $gks_event=$data['Event'];
$channel=''; if (isset($data['Channel'])) $channel=$data['Channel'];
$src=''; if (isset($data['CallerIDNum'])) $src=$data['CallerIDNum'];
$clid=''; if (isset($data['CallerIDName'])) $clid=$data['CallerIDName'];
$caller_name=$clid;
$dst=''; if (isset($data['Exten'])) $dst=$data['Exten'];
$uniqueid=''; if (isset($data['Uniqueid'])) $uniqueid=$data['Uniqueid'];

$gks_user_id=0;


if ($gks_event=='Newchannel') {

  if (strlen($src)>=10) {
    $sql="SELECT user_id
    FROM gks_users_communication
    WHERE phone_fix like '%".$db_link->escape_string($src)."'
    AND comm_type='phone'
    ORDER BY gks_users_communication.user_id DESC limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}
    if ($result->num_rows>0) {
      $row= $result->fetch_assoc();
      $gks_user_id=$row['user_id'];
    }
  }  
  
  $sql="insert into gks_voip_calls (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    gks_primary_rec,
    erp_app_id,
    gks_user_id,
    gks_event,
    channel,src,dst,clid,caller_name,uniqueid
  ) values (
    now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
    1,".$id_erp_app.",".$gks_user_id.",
    '".$db_link->escape_string($gks_event)."',
    '".$db_link->escape_string($channel)."',
    '".$db_link->escape_string($src)."',
    '".$db_link->escape_string($dst)."',
    '".$db_link->escape_string($clid)."',
    '".$db_link->escape_string($caller_name)."',
    '".$db_link->escape_string($uniqueid)."'
  )";
  //echo $sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error'; die();}
  //debug_mail(false,'sql 1',$sql);
} else if ($gks_event=='NewCallerid') {
  $sql="update gks_voip_calls set 
  clid='".$db_link->escape_string($clid)."',
  caller_name='".$db_link->escape_string($caller_name)."',
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where erp_app_id=".$id_erp_app."
  and uniqueid='".$db_link->escape_string($uniqueid)."'
  and gks_primary_rec=1
  and gks_event='Newchannel'
  and mydate_add>=date_sub(now(), interval 60 second)";
  //echo $sql;print_r($data);print $data_read;die();  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error'; die();}
  
  //debug_mail(false,'sql 2',$sql);
}




echo $responseok;
die();
