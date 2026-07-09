<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();
if ($my_wp_user_id <= 0) {
  die();
}


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}

$my_page_title=gks_lang('Αφαίρεση φωτογραφίας από μάρκα');
db_open();
stat_record();

$sql="select * from gks_eshop_products_brands_photo where id_eshop_products_brands_photo=".$id;
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

if (substr($row['photo_url'], 0,1) =='/') { //einai topiko arxio kai oxi url
  $file_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$row['photo_url'];
  if (file_exists($file_path)) {
    if (rename($file_path, $file_path.'.delete') == false) {
      debug_mail(false,'rename file error',$file_path);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα διαγραφής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();
    }
  }
  $photo_url_thumb = dirname($file_path).'/thumbnail/'.mb_basename($file_path);
  
  //debug_mail(false,'photo_url_thumb',$photo_url_thumb);
  
  if (file_exists($photo_url_thumb)) {
    if (rename($photo_url_thumb, $photo_url_thumb.'.delete') == false) {
      debug_mail(false,'rename file error',$photo_url_thumb);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα διαγραφής').' (2)<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();     
    }
  }
  
}


$sql="delete from gks_eshop_products_brands_photo where id_eshop_products_brands_photo=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }

    
$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();  
