<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

die('fix me kostas ... ');

define('SECURE', 1);

include_once('functions.php');




if (!isset($_GET['guid'])) {die();}
$guid=trim_gks(strtolower($_GET['guid']));
if (strlen($guid) != 32) {
  debug_mail(false,'f.php error on guid: '.$guid);
  die();
}

if (!isset($_GET['d'])) {die();}
$subdir=trim_gks(strtolower($_GET['d']));
if (strlen($subdir) != 3) {
  debug_mail(false,'f.php error on subdir: '.$subdir);
  die();
}

//org original
//ort original_thumbs
//pre preview
//wpr watermark_preview
//wth watermark_thumbs


$sub_directory='';
switch ($subdir) {
  case 'org':
    $sub_directory = 'original';
    break;
  case 'ort':
    $sub_directory = 'original_thumbs';
    break;
  case 'pre':
    $sub_directory = 'preview';
    break;
  case 'wpr':
    $sub_directory = 'watermark_preview';
    break;
  case 'wth':
    $sub_directory = 'watermark_thumbs';
    break;
}

if ($sub_directory == '') {
  debug_mail(false,'f.php error on sub_directory: '.$subdir.'-'.$sub_directory);
  die();
}

db_open();
//stat_record();

//echo 'sss'.$my_wp_user_id.'--'.$my_is_global_admin;
//die();

$sql = "SELECT gks_file.*, gks_event.event_path,gks_event.id_event,gks_event.event_type
FROM gks_file LEFT JOIN gks_event ON gks_file.event_id = gks_event.id_event
where gks_file.file_guid='".$db_link->escape_string($guid)."'
and gks_event.id_event is not null";
$result = $db_link->query($sql);
if ($result->num_rows == 0) {
  debug_mail(false,'file id not found: '.$guid);
  die();
}

$row_file=$result->fetch_assoc();
$id_file=$row_file['id_file'];
$id_event=$row_file['id_event'];
$row_event['id_event']=$row_file['id_event'];
$row_event['event_type']=$row_file['event_type'];
$row_photos['id_file']=$id_file;

$canview=false;
$event_perm=array();
get_event_perm($row_event,$canview,$event_perm);

if (ur_ad() or ur_em()) {
  $canview = true;
}
if ($canview == false) {
    debug_mail(false,'warning on f.php no rights to view - id: '.$id_event);
    die(); 
}


$file_can_download=array();
$sql="SELECT gks_file.id_file
FROM gks_file LEFT JOIN gks_file_perm ON gks_file.id_file = gks_file_perm.file_id
WHERE gks_file_perm.can_download<>0
and 1=1
and gks_file_perm.user_id=0
and gks_file.event_id=".$id_event."
and gks_file.id_file=".$id_file;
if ($row_event['event_type'] == 1) { //public
  $sql.=" and (user_id=".$my_wp_user_id." or user_id=0)";
} else if ($row_event['event_type'] == 2) { //private
  //$sql.=" and (user_id=".$my_wp_user_id." or user_id=0)';
}
$result_can_download = $db_link->query($sql);
while ($row_can_download = $result_can_download->fetch_assoc()) {
  $file_can_download[] = $row_can_download['id_file'];
}

$file_can_download_dika_tou=array();
if ($my_wp_user_id > 0) {
  $sql="SELECT gks_file.id_file
  FROM gks_file LEFT JOIN gks_file_perm ON gks_file.id_file = gks_file_perm.file_id
  WHERE gks_file_perm.can_download<>0
  and gks_file.event_id=".$id_event."
  and gks_file.id_file=".$id_file."
  and user_id=".$my_wp_user_id;
  
  $result_can_download_dika_tou = $db_link->query($sql);
  while ($row_can_download_dika_tou = $result_can_download_dika_tou->fetch_assoc()) {
    $file_can_download_dika_tou[] = $row_can_download_dika_tou['id_file'];
  }
}
//debug_mail(false,'file_can_download: ',print_r($file_can_download,true) .' ---- sql '.$sql,'ssssssssss');

$can_download=false;
if (ur_ad() or ur_em()) {
  $can_download=true;
}
if ($can_download == false and isset($event_perm['is_admin']) and $event_perm['is_admin']) {
  $can_download=true;
}

//private 
if ($can_download == false && $row_event['event_type'] == 2 && $event_perm['can_download'] && in_array($row_photos['id_file'], $file_can_download)) {
  $can_download=true;
}
//public 
if ($can_download == false && $row_event['event_type'] == 1 && in_array($row_photos['id_file'], $file_can_download)) {
  $can_download=true;
}
//dika tou
if ($can_download == false && in_array($row_photos['id_file'], $file_can_download_dika_tou)) {
  $can_download=true;
}


//print '<pre>';
//print_r($event_perm);
//echo 'can_download:'.$can_download;
//die();

//if ($gkIP == '87.202.133.179') {
//  print '<pre>';
//  print 'can_download:'.$can_download.'</br>';
//  print_r($event_perm);
//  print_r($file_can_download);
//  echo 'dtime='.(microtime(true) - $dev_page_starttime). ' secs';
//  die();
//}



switch ($subdir) {
  case 'org': //    'original';
    if ($can_download == false) {
      debug_mail(false,'warning on f.php original: '.$id_file);
      die(); 
    }
    break;
  case 'ort': //    'original_thumbs';
    if (!(isset($_GET['forcover']) &&  $_GET['forcover']=='1')) {
      if ($can_download == false) {
        debug_mail(false,'warning on f.php original_thumbs: '.$id_file);
        die(); 
      }
    }
    break;
  case 'pre': //    'preview';
    if ($can_download == false) {
      debug_mail(false,'warning on f.php preview: '.$id_file);
      die(); 
    }  
    break;
  case 'wpr': //    'watermark_preview';
    if ($event_perm['can_view'] == false) {
      debug_mail(false,'warning on f.php watermark_preview: '.$id_file);
      die(); 
    }  
    break;
  case 'wth': //    'watermark_thumbs';
    if ($event_perm['can_view'] == false) {
      debug_mail(false,'warning on f.php watermark_preview: '.$id_file);
      die(); 
    }  
    break;
}		   
												   


$mypath=GKS_DATA.$row_file["event_path"];
$filepath= $mypath.'/'.$sub_directory.'/'.$row_file["file_name"];
if (file_exists($filepath) == false) {
  die(); 
}


$info = getimagesize($filepath);
//var_dump($info);
  

  
header('Content-Type: '.$info['mime']);
if (isset($_GET['download'])) {
  header('Content-Disposition: attachment; filename="'.$row_file["file_name"].'"');
}

$offset = 60 * 60 * 24; //24 ores
//if (getenv('ENV') == 'DEVELOPMENT') $offset = 60;
  
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
header("Cache-Control: max-age=$offset, must-revalidate"); 
header("Pragma: private");


//debug_mail(false,'ssss7'.$filepath);

readfile($filepath);


