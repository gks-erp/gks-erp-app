<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('Λήψη καταχωρήσεων ημερολογίου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar','view',0);
if ($perm_ret['success']==false) {echo json_encode(array()); die();}





$hard=0;
if (!isset($_POST['start'])) {
  $hard=1;
  
  $_POST['start'] = '2017-01-01';
  $_POST['end'] = '2017-02-01';
}

$users=array();
$users['cal']=array();
$users['task']=array();
$users['activ']=array();
$users['hr_program']=array();
$users['cal'][]=$my_wp_user_id;
$users['task'][]=$my_wp_user_id;
$users['activ'][]=$my_wp_user_id;
$users['hr_program'][]=$my_wp_user_id;

$sql="SELECT other_myobj,other_user_id
FROM gks_calendar_other_users 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_other_users.other_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE gks_calendar_other_users.this_user_id=".$my_wp_user_id." and ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
while ($row = $result->fetch_assoc()) {
  $users[$row['other_myobj']][]=$row['other_user_id'];
}

$posta=[];
$posta['hr_program']=array();
$sql="SELECT other_myobj,other_posta_id
FROM gks_calendar_other_posta 
LEFT JOIN gks_production_posta ON gks_calendar_other_posta.other_posta_id = gks_production_posta.id_production_posto
WHERE gks_calendar_other_posta.this_user_id=".$my_wp_user_id." and gks_production_posta.id_production_posto Is Not Null";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
while ($row = $result->fetch_assoc()) {
  $posta[$row['other_myobj']][]=$row['other_posta_id'];
}

$range_start = date('Y-m-d H:i:s', strtotime($_POST['start']));
$range_end = date('Y-m-d H:i:s', strtotime($_POST['end'])); 



$params=array();
$params['range_start']=$range_start;
$params['range_end']=$range_end;
$params['users']=$users;
$params['posta']=$posta;



//print '<pre>';print_r($params);die();

$myout=gks_calendar_get_events($params);

if ($hard==1) {
  echo '<pre>';
  echo json_encode($myout,JSON_PRETTY_PRINT);
  
} else {

  $return = array('success' => true, 'message' => base64_encode('OK'),'mylist'=>$myout);

  
  echo json_encode($return);
}
die();


