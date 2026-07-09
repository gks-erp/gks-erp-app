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
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$fromadmin=0;
if (isset($_GET['fromadmin'])) $fromadmin=intval($_GET['fromadmin']);

$user_id=0;
if (isset($_GET['user_id'])) $user_id=intval($_GET['user_id']);

$hr_user_id=0;
if (isset($_GET['hr_user_id'])) $hr_user_id=intval($_GET['hr_user_id']);


$my_page_title=gks_lang('Διαγραφή αρχείου βιογραφικού σε προφίλ');
db_open();
stat_record();

if ($hr_user_id > 0 and $id > 0) {
  $sql="delete from gks_hr_user_cvs where hr_user_id=".$hr_user_id." and user_cv_id=".$id;
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="update gks_users_cv set show_on_user_profile =0, session_id='' where id_user_cv=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  

  $return = array('success' => true, 'message' => base64_encode('OK'),'profilepososto_user' => 0,'profilepososto_job' => 0);
  echo json_encode($return); die();  
  
}


if ($fromadmin == 0) {
  
  $sql="update gks_users_cv set show_on_user_profile =0, session_id='' where id_user_cv=".$id." and user_id=".$my_wp_user_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
    
  if ($my_wp_user_id>0) {
    
    $sql="update ".GKS_WP_TABLE_PREFIX."users set 
    gks_last_update=now(),
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."' 
    where id=".$my_wp_user_id." limit 1";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }

    $calc = calc_profilepososto($my_wp_user_id,false);
    
  }
  
  
  
} else if ($my_wp_user_id>0) {
    
  $sql="select * from gks_users_cv where id_user_cv=".$id." and user_id=".$user_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  }
  
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
  
  if (substr($row['cv_url'], 0,1) =='/') { //einai topiko arxio kai oxi url
    $file_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$row['cv_url'];
    if (file_exists($file_path)) {
      if (rename($file_path, $file_path.'.delete') == false) {
        debug_mail(false,'rename file error',$file_path);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα διαγραφής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        echo json_encode($return); die();     
      }
    }
  }
  
  
  $sql="delete from gks_users_cv where id_user_cv=".$id." and user_id=".$user_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
    
  if ($user_id>0) {
    $calc = calc_profilepososto($user_id,false);
  }
  
//  $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_last_update=now() where id=".$user_id." limit 1";
//  $result = $db_link->query($sql); 
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }

  $sql="delete from gks_hr_user_cvs where user_cv_id=".$id;
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
}


    
$return = array('success' => true, 'message' => base64_encode('OK'),'profilepososto_user' => (isset($calc['user']) ? $calc['user'] : ''),'profilepososto_job' => (isset($calc['job']) ? $calc['job'] : ''));
echo json_encode($return); die();  
