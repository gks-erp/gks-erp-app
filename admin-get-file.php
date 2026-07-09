<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

//$userrole='';
//if (isset($my_wp_user_info->roles)) {
//  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
//  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
//}
//if ($userrole=='') {
//  debug_mail(false,'security hack',''); 
//  die();
//}
db_open();
stat_record();


if (!isset($_GET['fs']) or !isset($_GET['file'])) {
  debug_mail(false,'security hack - empty file 1',''); 
  die();
}  
/*
fs=fileservers
fs=tmp
*/


$myfilepath=trim_gks(rawurldecode($_GET['file']));
//$myfilepath=trim_gks(urldecode($_GET['file']));
if ($myfilepath=='') {
  debug_mail(false,'security hack - empty file 2',''); 
  die();  
}
$myfs=trim_gks(rawurldecode($_GET['fs']));
//$myfs=trim_gks(urldecode($_GET['fs']));
if ($myfs=='') {
  debug_mail(false,'security hack - empty file 3',''); 
  die();  
}



$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__get_file','view',0,false);

if ($perm_ret['success']==false) {
  debug_mail(false,$perm_ret['message'],'');
  $return = array('success' => false, 'message' => base64_encode($perm_ret['message']));
  echo json_encode($return); die();}






if ($myfs=='tmp') $myfilepath = GKS_SITE_PATH.'tmp/'.$myfilepath;
else $myfilepath = GKS_FileServerShare.$myfilepath;

//echo $myfilepath; die();

$dir_path=dirname($myfilepath);
//echo $myfilepath; die();

if (endwith(strtolower($dir_path),'/thumbnail')) {
  //die($dir_path);
  $original_file=substr($dir_path, 0, strlen($dir_path)-9).mb_basename($myfilepath);
  //die($original_file);
  if (file_exists($original_file) == false) {
    debug_mail(false,'security hack - file not found',$original_file); 
    die();
  }  
  //die($myfilepath);
  if (file_exists($myfilepath) == false) {
    //die($myfilepath);
    if (file_exists($dir_path) == false) {
      //die($dir_path);
      @mkdir($dir_path , 0755, true);
      if (file_exists($dir_path) == false) {
        debug_mail(false,'can not create dir: ',$dir_path);
        die();
      }
    }
  
    //makeThumbnails_normal($original_file, $myfilepath, 300,300, false);
    makeThumbnails_square($original_file, $myfilepath, 300, false);
  }
  //echo $dir_path;
}



if (file_exists($myfilepath) == false) {
  //debug_mail(false,'security hack - file not found',$myfilepath); 
  die();
}



$finfo = new finfo();
$fileinfo = $finfo->file($myfilepath, FILEINFO_MIME);

//$info = getimagesize($myfilepath);
//var_dump($fileinfo);
//die();  

  
header('Content-Type: '.$fileinfo);
if (isset($_GET['download'])) {
  header('Content-Disposition: attachment; filename="'.mb_basename($myfilepath).'"');
} else {
  header('Content-Disposition: inline; filename="'.mb_basename($myfilepath).'"'); 
}

$offset = 60 * 60 * 24; //24 ores
//if (getenv('ENV') == 'DEVELOPMENT') $offset = 60;
  
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
header("Cache-Control: max-age=$offset, must-revalidate"); 
header("Pragma: private");

header("gks_read_file: run");
readfile($myfilepath);

//echo $myfilepath;

