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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hr_program_vardia',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




if ($id>0) {
  $sql ="SELECT * FROM gks_hr_program_vardia where id_hr_program_vardia = ".$id;
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



$hr_program_vardia_descr=''; if (isset($_POST['hr_program_vardia_descr'])) $hr_program_vardia_descr=trim_gks(base64_decode($_POST['hr_program_vardia_descr']));
$hr_program_vardia_color=''; if (isset($_POST['hr_program_vardia_color'])) $hr_program_vardia_color=trim_gks(base64_decode($_POST['hr_program_vardia_color']));
$hr_program_vardia_colorf=''; if (isset($_POST['hr_program_vardia_colorf'])) $hr_program_vardia_colorf=trim_gks(base64_decode($_POST['hr_program_vardia_colorf']));
$hr_program_vardia_colorcss=''; if (isset($_POST['hr_program_vardia_colorcss'])) $hr_program_vardia_colorcss=trim_gks(base64_decode($_POST['hr_program_vardia_colorcss']));
$hr_program_vardia_sortorder=0; if (isset($_POST['hr_program_vardia_sortorder'])) $hr_program_vardia_sortorder=intval(stripslashes(urldecode($_POST['hr_program_vardia_sortorder'])));
$hr_program_vardia_disabled=0; if (isset($_POST['hr_program_vardia_disabled'])) $hr_program_vardia_disabled=intval($_POST['hr_program_vardia_disabled']);
$hr_program_vardia_time_start=''; if (isset($_POST['hr_program_vardia_time_start'])) $hr_program_vardia_time_start=trim_gks(base64_decode($_POST['hr_program_vardia_time_start']));
$hr_program_vardia_time_end=''; if (isset($_POST['hr_program_vardia_time_end'])) $hr_program_vardia_time_end=trim_gks(base64_decode($_POST['hr_program_vardia_time_end']));
$hr_program_vardia_weekday1=0; if (isset($_POST['hr_program_vardia_weekday1'])) $hr_program_vardia_weekday1=intval($_POST['hr_program_vardia_weekday1']);
$hr_program_vardia_weekday2=0; if (isset($_POST['hr_program_vardia_weekday2'])) $hr_program_vardia_weekday2=intval($_POST['hr_program_vardia_weekday2']);
$hr_program_vardia_weekday3=0; if (isset($_POST['hr_program_vardia_weekday3'])) $hr_program_vardia_weekday3=intval($_POST['hr_program_vardia_weekday3']);
$hr_program_vardia_weekday4=0; if (isset($_POST['hr_program_vardia_weekday4'])) $hr_program_vardia_weekday4=intval($_POST['hr_program_vardia_weekday4']);
$hr_program_vardia_weekday5=0; if (isset($_POST['hr_program_vardia_weekday5'])) $hr_program_vardia_weekday5=intval($_POST['hr_program_vardia_weekday5']);
$hr_program_vardia_weekday6=0; if (isset($_POST['hr_program_vardia_weekday6'])) $hr_program_vardia_weekday6=intval($_POST['hr_program_vardia_weekday6']);
$hr_program_vardia_weekday0=0; if (isset($_POST['hr_program_vardia_weekday0'])) $hr_program_vardia_weekday0=intval($_POST['hr_program_vardia_weekday0']);
$hr_program_vardia_is_ergasia=0; if (isset($_POST['hr_program_vardia_is_ergasia'])) $hr_program_vardia_is_ergasia=intval($_POST['hr_program_vardia_is_ergasia']);




if ($hr_program_vardia_descr=='') {debug_mail(false,'hr_program_vardia_descr',$hr_program_vardia_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή'))); 
  echo json_encode($return); die(); }

$parts=explode(':',$hr_program_vardia_time_start);
$hr_program_vardia_time_start='';
$hr_program_vardia_time_start_int=0;
if (count($parts)==2 and 
    strlen($parts[0])==2 and strlen($parts[1])==2 and 
    intval($parts[0])>=0 and intval($parts[0])<=23 and
    intval($parts[1])>=0 and intval($parts[1])<=59) {
  $pp0=intval($parts[0]);$pp1=intval($parts[1]);
  $hr_program_vardia_time_start=($pp0<10?'0':'').$pp0.':'.($pp1<10?'0':'').$pp1;
  $hr_program_vardia_time_start_int=100*$pp0+$pp1;
}
$parts=explode(':',$hr_program_vardia_time_end);
$hr_program_vardia_time_end='';
$hr_program_vardia_time_end_int=0;
if (count($parts)==2 and 
    strlen($parts[0])==2 and strlen($parts[1])==2 and 
    intval($parts[0])>=0 and intval($parts[0])<=24 and
    intval($parts[1])>=0 and intval($parts[1])<=59) {
  $pp0=intval($parts[0]);$pp1=intval($parts[1]);
  $hr_program_vardia_time_end=($pp0<10?'0':'').$pp0.':'.($pp1<10?'0':'').$pp1;
  $hr_program_vardia_time_end_int=100*$pp0+$pp1;
}
//echo '<pre>'.$hr_program_vardia_time_start_int.'|'.$hr_program_vardia_time_end_int;die();

if ($hr_program_vardia_time_start=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Έναρξη'))); 
  echo json_encode($return); die(); }
  
if ($hr_program_vardia_time_end=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Λήξη'))); 
  echo json_encode($return); die(); }
  
//if ($hr_program_vardia_time_end_int <= $hr_program_vardia_time_start_int and $hr_program_vardia_time_end_int<>0) {
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('H Λήξη πρέπει να είναι μεγαλύτερη από την Έναρξη'))); 
//  echo json_encode($return); die(); } 

if ($hr_program_vardia_color=='') {debug_mail(false,'hr_program_vardia_color','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Χρώμα φόντου'))); 
  echo json_encode($return); die(); }

if ($hr_program_vardia_colorf=='') {debug_mail(false,'hr_program_vardia_colorf','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Χρώμα κειμένου'))); 
  echo json_encode($return); die(); }

if ($hr_program_vardia_weekday1!=0) $hr_program_vardia_weekday1=1;
if ($hr_program_vardia_weekday2!=0) $hr_program_vardia_weekday2=1;
if ($hr_program_vardia_weekday3!=0) $hr_program_vardia_weekday3=1;
if ($hr_program_vardia_weekday4!=0) $hr_program_vardia_weekday4=1;
if ($hr_program_vardia_weekday5!=0) $hr_program_vardia_weekday5=1;
if ($hr_program_vardia_weekday6!=0) $hr_program_vardia_weekday6=1;
if ($hr_program_vardia_weekday0!=0) $hr_program_vardia_weekday0=1;
if ($hr_program_vardia_is_ergasia!=0) $hr_program_vardia_is_ergasia=1;


$sql="select * from gks_hr_program_vardia where hr_program_vardia_descr like '".$db_link->escape_string($hr_program_vardia_descr)."' and id_hr_program_vardia<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η κατάσταση με περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-hr-program-vardia-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$hr_program_vardia_descr,$message);
  $message=str_replace('[2]',$row['id_hr_program_vardia'],$message);  
  debug_mail(false,'exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}






$redirect='';
if ($id==-1) {
  $sql="insert into gks_hr_program_vardia (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hr-program-vardia-item.php?id='.$id); 
}

  
$sql="update gks_hr_program_vardia set 
hr_program_vardia_descr='".$db_link->escape_string($hr_program_vardia_descr)."',
hr_program_vardia_color=". ($hr_program_vardia_color =='' ? 'null' : "'".$db_link->escape_string($hr_program_vardia_color)."'").",
hr_program_vardia_colorf=". ($hr_program_vardia_colorf =='' ? 'null' : "'".$db_link->escape_string($hr_program_vardia_colorf)."'").",
hr_program_vardia_colorcss=". ($hr_program_vardia_colorcss =='' ? 'null' : "'".$db_link->escape_string($hr_program_vardia_colorcss)."'").",
hr_program_vardia_sortorder=".$hr_program_vardia_sortorder.",
hr_program_vardia_disabled=".$hr_program_vardia_disabled.",
hr_program_vardia_time_start='".$db_link->escape_string($hr_program_vardia_time_start)."',
hr_program_vardia_time_end='".$db_link->escape_string($hr_program_vardia_time_end)."',
hr_program_vardia_weekday1=".$hr_program_vardia_weekday1.",
hr_program_vardia_weekday2=".$hr_program_vardia_weekday2.",
hr_program_vardia_weekday3=".$hr_program_vardia_weekday3.",
hr_program_vardia_weekday4=".$hr_program_vardia_weekday4.",
hr_program_vardia_weekday5=".$hr_program_vardia_weekday5.",
hr_program_vardia_weekday6=".$hr_program_vardia_weekday6.",
hr_program_vardia_weekday0=".$hr_program_vardia_weekday0.",
hr_program_vardia_is_ergasia=".$hr_program_vardia_is_ergasia.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hr_program_vardia = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

