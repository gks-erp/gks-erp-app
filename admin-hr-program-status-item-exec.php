<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Κατάσταση Προγράμματος').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hr_program_status',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




if ($id>0) {
  $sql ="SELECT * FROM gks_hr_program_status where id_hr_program_status = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}



$hr_program_status_descr=''; if (isset($_POST['hr_program_status_descr'])) $hr_program_status_descr=trim_gks(base64_decode($_POST['hr_program_status_descr']));
$hr_program_status_color=''; if (isset($_POST['hr_program_status_color'])) $hr_program_status_color=trim_gks(base64_decode($_POST['hr_program_status_color']));
$hr_program_status_colorf=''; if (isset($_POST['hr_program_status_colorf'])) $hr_program_status_colorf=trim_gks(base64_decode($_POST['hr_program_status_colorf']));
$hr_program_status_colorcss=''; if (isset($_POST['hr_program_status_colorcss'])) $hr_program_status_colorcss=trim_gks(base64_decode($_POST['hr_program_status_colorcss']));
$hr_program_status_sortorder=0; if (isset($_POST['hr_program_status_sortorder'])) $hr_program_status_sortorder=intval(stripslashes(urldecode($_POST['hr_program_status_sortorder'])));
$hr_program_status_disabled=0; if (isset($_POST['hr_program_status_disabled'])) $hr_program_status_disabled=intval($_POST['hr_program_status_disabled']);

if ($hr_program_status_descr=='') {debug_mail(false,'hr_program_status_descr',$hr_program_status_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }

if ($hr_program_status_color=='') {debug_mail(false,'hr_program_status_color','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Χρώμα φόντου'))); 
  echo json_encode($return); die(); }

if ($hr_program_status_colorf=='') {debug_mail(false,'hr_program_status_colorf','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Χρώμα κειμένου'))); 
  echo json_encode($return); die(); }




$sql="select * from gks_hr_program_status where hr_program_status_descr like '".$db_link->escape_string($hr_program_status_descr)."' and id_hr_program_status<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η κατάσταση με περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-hr-program-status-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$hr_program_status_descr,$message);
  $message=str_replace('[2]',$row['id_hr_program_status'],$message);  
  debug_mail(false,'exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}






$redirect='';
if ($id==-1) {
  $sql="insert into gks_hr_program_status (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hr-program-status-item.php?id='.$id); 
}

  
$sql="update gks_hr_program_status set 
hr_program_status_descr='".$db_link->escape_string($hr_program_status_descr)."',
hr_program_status_color=". ($hr_program_status_color =='' ? 'null' : "'".$db_link->escape_string($hr_program_status_color)."'").",
hr_program_status_colorf=". ($hr_program_status_colorf =='' ? 'null' : "'".$db_link->escape_string($hr_program_status_colorf)."'").",
hr_program_status_colorcss=". ($hr_program_status_colorcss =='' ? 'null' : "'".$db_link->escape_string($hr_program_status_colorcss)."'").",
hr_program_status_sortorder=".$hr_program_status_sortorder.",
hr_program_status_disabled=".$hr_program_status_disabled.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hr_program_status = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

