<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$all=0; if (isset($_GET['all'])) $all=1;
$snooze=0; if (isset($_GET['snooze'])) $snooze=1;


if ($all==0) {
  $id=0;
  if (isset($_GET['id'])) $id=intval($_GET['id']);
  if ($id<=0) {
    debug_mail(false,'the id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();
  }
  
  $has_ok=1;
  if (isset($_GET['has_ok'])) $has_ok=intval($_GET['has_ok']);
  if ($has_ok==0) $has_ok=0; else $has_ok=1;
  
  $my_page_title=gks_lang('Ειδοποίηση').' id:'.$id." val:".$has_ok;
} else {
  $my_page_title=gks_lang('Ειδοποίησεις-Ορισμός όλων ως αναγνωσμένες');
}
db_open();
stat_record();


if ($snooze>0) {
  $sql="update gks_notification set has_ok=0, ok_date=null,for_date=date_add(now(),interval ".$snooze." HOUR),playsound=0 where id_notification=".$id." and for_user_id=".$my_wp_user_id;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
} else if ($all==0) {
  $sql="update gks_notification set has_ok=".$has_ok.", ok_date=".($has_ok==0 ? 'null' : 'now()')." where id_notification=".$id." and for_user_id=".$my_wp_user_id;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
} else {
  $sql="update gks_notification set has_ok=1, ok_date=now() where for_date<=now() and has_ok=0 and for_user_id=".$my_wp_user_id;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }  
  
}
$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
