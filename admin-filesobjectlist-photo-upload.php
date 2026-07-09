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




error_reporting(E_ALL | E_STRICT);
require('UploadHandler_admin-filesobjectlist-photo-upload.php');


  
$mydir1=intval($_POST['object_id']).'/';
$object_map=gks_FilesObjectList_map($_POST['object_name']);
$object_path=$object_map['path'];




$upload_dir= GKS_FileServerShare.$object_path.$mydir1;
$upload_url='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($object_path.$mydir1);



if (file_exists($upload_dir) == false) {
  if (@mkdir($upload_dir , 0755, true) == false ) {
    debug_mail(false,'can not create dir: ',$upload_dir);
    die('error');
  }
}


$gks_FilesObjectList_scandir_echo='';
$gks_FilesObjectList_show_print=array();
$gks_FilesObjectList_scandir_path=array();
$gks_FilesObjectList_scandir_path_keys=array();
$gks_FilesObjectList_shortcode_prefix=$object_map['shortcode_prefix'];;

$arrr=[];foreach (GKS_IMAGE_EXTENSION as $value) $arrr[]=substr($value,1);
$upload_handler = new UploadHandler(array(
                        'max_file_size' => gks_get_max_upload_file_size(true),
                        'image_file_types' => '/\.('.implode('|',$arrr).')$/i',
//                      'accept_file_types' => '/\.('.implode('|',$arrr).')$/i',
                        'upload_dir' => $upload_dir,
                        'upload_url' => $upload_url,
                        'user_id' => intval($my_wp_user_id),
                        'object_id' => intval($_POST['object_id']),
                        'object_name' => trim_gks($_POST['object_name']),
                        
                        ));
                        

$my_page_title=gks_lang('Μεταφόρτωση αρχείου σε αντικείμενο');
db_open();
stat_record();


if (!isset($upload_handler->response['files'][0]->name)) {
  die('error'); 
}
