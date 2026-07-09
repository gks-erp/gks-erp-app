<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
/*
https://test.easyfilesselection.com/my/cron_delete_tmp_files.php?folder=tmp&minutes=120
https://test.easyfilesselection.com/my/cron_delete_tmp_files.php?folder=cache&minutes=120
*/

ini_set('max_execution_time', 600);
set_time_limit(600);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
//require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

$folder='';if (isset($_GET['folder'])) $folder=trim($_GET['folder']);
$minutes=0;if (isset($_GET['minutes'])) $minutes=intval($_GET['minutes']);

if ($folder=='') die();
if ($minutes < 1) die();

if (defined('GKS_SITE_PATH')==false) die();
if (defined('GKS_CACHE')==false) die();

$real_path=GKS_SITE_PATH.$folder;
if ($folder=='cache') $real_path=GKS_CACHE;

if (substr($real_path, strlen($real_path)-1)!='/') $real_path.='/';
if (file_exists($real_path)==false) die();

$files=scandir($real_path);
$files = array_diff($files,['.','..']);
//print_r($files);
$limit_time=time() - $minutes*60;
foreach ($files as $myf) {
  $fpath=$real_path.$myf;
  $mytime=@filemtime($fpath);
  //$mytime=filectime($fpath);
  //echo $mytime.'<br>';
  if ($mytime!==false and $mytime < $limit_time) {
    @unlink($fpath);  
  }
}

//echo '<pre>ssssss '.$real_path;print_r($files);
//die();
