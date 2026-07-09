<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$crm_task_id=0;
if (isset($_POST['crm_task_id'])) $crm_task_id=intval($_POST['crm_task_id']);
if ($crm_task_id<=0) {
  debug_mail(false,'the crm_task_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εργασία')));
  echo json_encode($return); die();}

  
$crm_task_employee_id=0;
if (isset($_POST['crm_task_employee_id'])) $crm_task_employee_id=intval($_POST['crm_task_employee_id']);
if ($crm_task_employee_id<=0) {
  debug_mail(false,'the crm_task_employee_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο υπάλληλος')));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Προσθήκη υπαλλήλου σε εργασία');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT id_crm_task FROM gks_crm_tasks where id_crm_task = ".$crm_task_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty','task not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εργασία')));
  echo json_encode($return); die();}  


$sql="SELECT ID FROM ".GKS_WP_TABLE_PREFIX."users where ID = ".$crm_task_employee_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty','user employee not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο υπάλληλος')));
  echo json_encode($return); die();}  


$sql="SELECT * FROM gks_crm_tasks_employee where crm_task_id = ".$crm_task_id." and crm_task_employee_id=".$crm_task_employee_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty','task-employee exist');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία - υπάλληλος υπάρχει ήδη')));
  echo json_encode($return); die();}  




$sql="insert into gks_crm_tasks_employee (crm_task_id,crm_task_employee_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$crm_task_id.",
".$crm_task_employee_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$id_crm_task_employee = $db_link->insert_id; 



$row_html='';

$sql_list = "SELECT ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_crm_tasks_employee.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
FROM (gks_crm_tasks_employee 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_employee.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
WHERE id_crm_task_employee=".$id_crm_task_employee."
ORDER BY gks_crm_tasks_employee.id_crm_task_employee;";

$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="employee_tr_new" data-id="'.$row_list['id_crm_task_employee'].'">'.
  '<th scope="row" nowrap align="right" class="mytdcm employee_aa">*</td>'.
  '<td nowrap class="mytdcm">'.
    '<i class="fas fa-trash-alt deleterow" data-id="'.$row_list['id_crm_task_employee'].'" data-deleteafter="gks_fnc_employee_delete_after|'.$row_list['id_crm_task_employee'].'" data-model="gks_crm_tasks_employee" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>'.
  '</td>'.
  '<td>'.getUserPhoto($row_list['crm_task_employee_id'],$row_list['gks_wsl_current_user_image'],32).'</td>'.
  '<td class="mytdcml crm_task_employee_id" data-id="'.$row_list['crm_task_employee_id'].'"><a href="admin-users-item.php?id='.$row_list['crm_task_employee_id'].'">'.$row_list['gks_nickname'].'</a></td>'.
  '<td class="mytdcm" nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.
  '<td class="mytdcml"><a href="admin-users-item.php?id='.$row_list['user_id_edit'].'">'.$row_list['gks_nickname_edit'].'</a></td>'.
'</tr>';

 


gks_calendar_event_update_dav_task($crm_task_id,false);

$return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
echo json_encode($return); die();