<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Επεξεργασία καταχώρησης ημερολογίου').' - '.gks_lang('Εργασία');
db_open();
stat_record();
$id=0;if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η προσθήκη εργασίας μέσα από το ημερολόγιο')));echo json_encode($return); die();}

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd!='resize' && $cmd!='move') {
  debug_mail(false,'cmd is not good','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εντολή δεν είναι σωστή για την εργασία').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}



if ($id<=0) {  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}


$allday=0; //if (isset($_POST['allday'])) $allday=(intval($_POST['allday']) == 0 ? 0 : 1);

$start='';
if (isset($_POST['start'])) {
  $start=trim_gks($_POST['start']);
  if ($allday==1 and strlen($start)==19) {  // 2021-01-14 09:30:00 -> 2021-01-14 00:00:00
    $start=substr($start, 0, 11).' 00:00:00';
    //echo $start; die();
  }
  $start=_time_user(strtotime($start),-1);
  $start=date('Y-m-d H:i:s',$start);
}

$end='';
if (isset($_POST['end'])) {
  $end=trim_gks($_POST['end']);
  $end=_time_user(strtotime($end),-1);
  $end=date('Y-m-d H:i:s',$end);
} else {
  if (showDate(strtotime($start),"H:i:s",1)=='00:00:00') {
    $end = $start;
  } else {
    $end = date('Y-m-d H:i:s', strtotime($start) + 1*60*60);
  }
}



$is_new_rec=false;

$sql=gks_crm_tasks_sql_event("id_crm_task=".$id, '');
$sql.=" limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}
$row_old = $result->fetch_assoc();

  


$sql="update gks_crm_tasks set
task_planned_date_from ='".$start."',
task_planned_date_to ='".$end."',
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_crm_task=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$params=array();
$params['id_crm_task']=$id;

//$event=gks_calendar_get_events("gks_calendar.id_calendar=".$id);
$event=gks_calendar_get_events($params);
$event=$event[0];

gks_calendar_event_update_dav_task($id,false);



$return = array('success' => true, 'message' => base64_encode('OK'), 'event' => $event);
echo json_encode($return); die();  



//function gks_activity_update($id,$calendar_start) {
//  global $db_link;
//  global $my_wp_user_id;
//  global $gkIP;
//  
//  if ($id<=0) return;
//  $sql="select * from gks_crm_activity where calendar_id=".$id;
//	$result = $db_link->query($sql);  
//	if (!$result) {
//	  debug_mail(false,'error sql',$sql);
//	  $return = array('success' => false, 'message' => base64_encode('sql error'));
//	  echo json_encode($return); die(); }  
//  
//  if ($result->num_rows==1) {
//    $row = $result->fetch_assoc();
//    $id_crm_activity=intval($row['id_crm_activity']);
//    
//    $activity_duedate=date('Y-m-d H:i:s',_time_user(strtotime($calendar_start),1));
//    $sql="update gks_crm_activity set 
//    activity_duedate='".$activity_duedate."', 
//    mydate_edit=now(),
//    user_id_edit=".$my_wp_user_id.",
//    myip='".$db_link->escape_string($gkIP)."'
//    where id_crm_activity=".$id_crm_activity;
//  	$result = $db_link->query($sql);  
//  	if (!$result) {
//  	  debug_mail(false,'error sql',$sql);
//  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  	  echo json_encode($return); die(); }  
//    
//    
//  }
//  
//  
//  
//}
