<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

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




debug_mail(false,'profile-cv-upload.php exec');


error_reporting(E_ALL | E_STRICT);
require('UploadHandler_profile-cv-upload.php');



$user_id=$my_wp_user_id;
if (isset($_GET['user_id']) and intval($_GET['user_id'])>0 ) $user_id=intval($_GET['user_id']);




$show_on_user_profile=1;
//if ($user_id != $my_wp_user_id) $show_on_user_profile=0;

if (isset($_GET['show_on_user_profile']) and intval($_GET['show_on_user_profile'])== 0) $show_on_user_profile=0;
  
$mydir1=intval($user_id).'/'; //.date('Y/m/d').'/';
$upload_dir = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/users-cv/'.$mydir1;
$upload_url='/my/uploads/users-cv/'.$mydir1;


if (file_exists($upload_dir) == false) {
  if (@mkdir($upload_dir , 0755, true) == false ) {
    debug_mail(false,'profile-cv-upload.php can not create dir: ',$upload_dir);
    die('error');
  }
}


//echo $user_id;
//die();

$arrr=[];foreach (GKS_IMAGE_EXTENSION as $value) $arrr[]=substr($value,1);
$upload_handler = new UploadHandler(array(
                        'max_file_size' => gks_get_max_upload_file_size(true),
                        'image_file_types' => '/\.('.implode('|',$arrr).')$/i',
                        'accept_file_types' => '/\.(pdf|zip|rar|txt|doc|docx|docm|wps|htm|html|odt|sxw|rtf|jpg|jpeg|jpe|jif|jfif|jfi|png|gif|bmp|webp)$/i',
                        'upload_dir' => $upload_dir,
                        'upload_url' => $upload_url,
                        'user_id' => intval($user_id),
                        'show_on_user_profile' => $show_on_user_profile,
                        ));
                        

$my_page_title=gks_lang('Ανέβασμα αρχείου βιογραφικού σε προφίλ');
db_open();
stat_record();



if (!isset($upload_handler->response['files'][0]->name)) {
  die('error'); 
}


if ($my_wp_user_id == $user_id) {
  $sql="update ".GKS_WP_TABLE_PREFIX."users set 
  gks_last_update=now(),
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."' 
  where id=".$user_id." limit 1";
  $result = $db_link->query($sql); 
  if (!$result) {
    die('error sql');
  }
} else {
  $sql="update ".GKS_WP_TABLE_PREFIX."users set 
  user_id_edit=".$user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."' 
  where id=".$user_id." limit 1";
  $result = $db_link->query($sql); 
  if (!$result) {
    die('error sql');
  }
  
}

$calc = calc_profilepososto($user_id,false);


  


