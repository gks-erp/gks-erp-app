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
  die();}


$my_page_title=gks_lang('Λήψη κατάστασης των λήψεων');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks','view',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="select * from gks_crm_tasks_links where crm_task_id=".$id." and (download_status=1 or (download_status in (2,3) and download_end > date_sub(now(), interval 1 minute)))";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
$data=array();  
$complete_td=array();
while ($row = $result->fetch_assoc()) {
  $relative_path=trim_gks($row['relative_path']);
  $download_size_until_now=intval($row['download_size_until_now']);
  if ($download_size_until_now==0 and $relative_path!= '') {
    $path_scan=GKS_FileServerShare.'crm/task/'.$id.'/'.$relative_path;
    if (file_exists($path_scan) and is_dir($path_scan)) {
      $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path_scan));
      $download_size_until_now=0;
      $zip_files=array();
      foreach ($rii as $file) {
        if ($file->isDir()){ 
          continue;
        }
        $download_size_until_now+=filesize($file->getPathname());
      }  
    }
  } 
    
  
  
  $data[] =array(
    'id' => intval($row['id_crm_tasks_links']),
    'now' => ($download_size_until_now==0 ? '': number_format($download_size_until_now/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB'),
    'per' => floatval($row['download_pososto']),
    'status' =>intval($row['download_status']), 
    'msg' =>trim_gks($row['download_message']), 
    
  );
  
   
  $html_tds = trim_gks($row['html_tds']);
  if ($html_tds!='') {
    $html_tds_array=json_decode($html_tds, true);
    foreach ($html_tds_array as $value) {
      $complete_td[] = array(
        'relpath' => $value['path'],
        'td' => $value['html'],
      );          
    } 
  }  
}  

$return = array('success' => true, 'message' => base64_encode('OK'), 'data'=> $data, 'complete_td' => $complete_td,);
echo json_encode($return); die();

