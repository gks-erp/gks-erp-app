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

$my_page_title=gks_lang('Διαγραφή φωτογραφίας από εγγραφή');
db_open();
stat_record();

$data_path=''; if (isset($_POST['data_path'])) $data_path=trim_gks(base64_decode($_POST['data_path']));
$object_name=''; if (isset($_POST['object_name'])) $object_name=trim_gks(base64_decode($_POST['object_name']));

$object_map=gks_FilesObjectList_map($object_name);
$object_path=$object_map['path'];
$object_table=$object_map['table'];
$object_tid=$object_map['tid'];
$object_pid=$object_map['pid'];

//echo '<pre>'.$object_name; die();


$sql="delete from ".$object_table." where ".$object_pid."=".$id." and photo_url like '".$db_link->escape_string($data_path)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}
  

$file_path=GKS_FileServerShare.$data_path;
if (file_exists($file_path)) {
  
  $file_thump=dirname($file_path) .'/thumbnail/'.mb_basename($file_path);
  if (file_exists($file_thump)) {
    unlink($file_thump);
  }
  
  if (rename($file_path, $file_path.'.delete') == false) {
    debug_mail(false,'rename file error',$file_path);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα διαγραφής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();     
  }
  
  
} 

   
$return = array('success' => true, 'message' => base64_encode('OK'), 'data_path'=> $data_path);
echo json_encode($return); die();  



die();

