<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$myto=0;if (isset($_POST['to'])) $myto=intval($_POST['to']);
if ($myto<=0) $myto=0;
if ($myto==0) $myto=$my_wp_user_id;
$mycl=0;if (isset($_POST['cl'])) $mycl=intval($_POST['cl']);
if ($mycl!=0) $mycl=1;



$my_page_title=gks_lang('Νέα Ειδοποίηση από').':'.$my_wp_user_id.' '.gks_lang('προς').': '.$myto;

db_open();
stat_record();

$message=''; if (isset($_POST['message'])) $message=trim_gks(base64_decode($_POST['message']));
$mylink='';  if (isset($_POST['mylink']))  $mylink=trim_gks(base64_decode($_POST['mylink']));
$mytitle=''; if (isset($_POST['mytitle'])) $mytitle=trim_gks(base64_decode($_POST['mytitle']));

$message=nl2br_gks($message);
if ($mycl!=0 && $mylink!='') {
  $mytitle_parts=explode(' | ',$mytitle);
  if (count($mytitle_parts)>=2) $mytitle=trim_gks($mytitle_parts[0]);
  if ($mytitle == '') $mytitle='link';
  $message.=' '.gks_lang('Σύνδεσμος').': <a href="'.$mylink.'" target="_blank">'.$mytitle.'</a>';
}

$id_notification=0;
$sql="select user_id from gks_notification_userperm where user_id=".$myto." and notification_type_id=10 and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>0) {
  $sql="insert into gks_notification (
  message,sender_id,for_user_id,date_add,for_date
  ) values (
  '".$db_link->escape_string($message)."',
  ".$my_wp_user_id.",
  ".$myto.",
  now(),now())";
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_notification=$db_link->insert_id;
} else {
  debug_mail(false,'notification is disabled',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι ενεργοποιημένες οι ειδοποιήσεις <b>Καταχώρηση από χρήστη</b> σε αυτόν τον χρήστη')));
  echo json_encode($return); die();  
  
}

$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
FROM gks_notification_userperm 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
AND ".GKS_WP_TABLE_PREFIX."users.ID=".$myto."
AND gks_notification_userperm.notification_type_id=10 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>0) {
  $message=''; if (isset($_POST['message'])) $message=trim_gks(base64_decode($_POST['message']));
  $mylink='';  if (isset($_POST['mylink']))  $mylink=trim_gks(base64_decode($_POST['mylink']));
  $mytitle=''; if (isset($_POST['mytitle'])) $mytitle=trim_gks(base64_decode($_POST['mytitle']));
  
  if ($my_wp_user_id==$myto) {
    $message=gks_lang('Από εμένα').':'."\n".$message;
  } else {
    $message=gks_lang('Από').' '.$my_wp_user_info->data->display_name.':'."\n".$message;
  }
  
  if ($mycl!=0 && $mylink!='') {
    //$mytitle_parts=explode(' | ',$mytitle);
    //if (count($mytitle_parts)>=2) $mytitle=trim_gks($mytitle_parts[0]);
    //if ($mytitle == '') $mytitle='link';
    $message.="\n".gks_lang('Σύνδεσμος').':'."\n".$mylink;
  }
  $row = $result->fetch_assoc();
  gks_viber_send('notification', $id_notification, $row['viber_id'],$message);
}


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
