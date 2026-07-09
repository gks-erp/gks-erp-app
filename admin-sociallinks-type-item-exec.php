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

$my_page_title=gks_lang('Αποθήκευση Τύπου Συνδέσμων Κοινωνικών Δικτύων').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sociallinks_type',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





if ($id>0) {
  $sql ="SELECT * FROM gks_sociallinks_type where id_sociallinks_type = ".$id;
 
  
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




$sociallinks_type_descr=''; if (isset($_POST['sociallinks_type_descr'])) $sociallinks_type_descr=trim_gks(base64_decode($_POST['sociallinks_type_descr']));
$sociallinks_type_icon=''; if (isset($_POST['sociallinks_type_icon'])) $sociallinks_type_icon=trim_gks(base64_decode($_POST['sociallinks_type_icon']));
$sociallinks_type_icon_email=''; if (isset($_POST['sociallinks_type_icon_email'])) $sociallinks_type_icon_email=trim_gks(base64_decode($_POST['sociallinks_type_icon_email']));
$sociallinks_type_comments=''; if (isset($_POST['sociallinks_type_comments'])) $sociallinks_type_comments=trim_gks(stripslashes(urldecode($_POST['sociallinks_type_comments'])));
$sociallinks_type_disable=0; if (isset($_POST['sociallinks_type_disable'])) $sociallinks_type_disable=intval($_POST['sociallinks_type_disable']);



if ($sociallinks_type_descr=='') {debug_mail(false,'sociallinks_type_descr',$sociallinks_type_descr);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή')));
  echo json_encode($return); die(); }



$sql="select * from gks_sociallinks_type where sociallinks_type_descr like '".$db_link->escape_string($sociallinks_type_descr)."' and id_sociallinks_type<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Ο τύπος με περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-sociallinks-type-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$sociallinks_type_descr,$message);
  $message=str_replace('[2]',$row['id_sociallinks_type'],$message);

  debug_mail(false,'sociallinks-type exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}








$redirect='';
if ($id==-1) {
  $sql="insert into gks_sociallinks_type (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-sociallinks-type-item.php?id='.$id); 
}

  
$sql="update gks_sociallinks_type set 
sociallinks_type_descr='".$db_link->escape_string($sociallinks_type_descr)."',
sociallinks_type_icon='".$db_link->escape_string($sociallinks_type_icon)."',
sociallinks_type_icon_email='".$db_link->escape_string($sociallinks_type_icon_email)."',
sociallinks_type_comments=". ($sociallinks_type_comments =='' ? 'null' : "'".$db_link->escape_string($sociallinks_type_comments)."'").",

sociallinks_type_disable=".$sociallinks_type_disable.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_sociallinks_type = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  






$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

