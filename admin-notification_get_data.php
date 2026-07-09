<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

db_open();
$sql="select id_notification,sender_id,message,date_add,playsound,".GKS_WP_TABLE_PREFIX."users.gks_nickname
from gks_notification 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification.sender_id = ".GKS_WP_TABLE_PREFIX."users.ID
where for_date<=now() and for_user_id=".$my_wp_user_id." and has_ok=0 order by id_notification";
$result = $db_link->query($sql);     
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$data=array();
$update_sound_ids=array();
$playsound=false;
while ($row = $result->fetch_assoc()) {
  if ($row['playsound']==0) {
    $playsound=true;
    $update_sound_ids[]=$row['id_notification'];
  }
  $message=$row['message'];
  if ($row['sender_id']==$my_wp_user_id) {
    $message=gks_lang('Εγώ').': '.$message;
  } else if ($row['sender_id'] == 2) {
    $message=gks_lang('System').': '.$message;
  } else if ($row['sender_id']>0 && trim_gks($row['gks_nickname'])!='') {
    $message=gks_lang('Από').' <b>'.trim_gks($row['gks_nickname']).'</b>: '.$message;
  }
  
  $data[]=array(
    'id' => intval($row['id_notification']),
    'text' => $message,
    'ago' => secondsago(strtotime($row['date_add'])),
  );
}

if (count($update_sound_ids)>0) {
  $sql="update gks_notification set playsound=1 where id_notification in (".implode(',',$update_sound_ids).')';
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
}


$diafora=time()-$GKS_ERP_CRON_LAST_RUN;
if (abs($diafora > 60)) {
  gks_curl_post_async(GKS_SITE_URL.'my/cron.php',[]);
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'data'=>$data, 'ps' => $playsound);
echo json_encode($return); die();
