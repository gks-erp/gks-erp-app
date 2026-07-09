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




//debug_mail(false,'admin-products-item-photo-upload.php exec');



error_reporting(E_ALL | E_STRICT);
require('UploadHandler_admin-users-item-photo-upload.php');


  
$mydir1=intval($_POST['user_id']).'/'; //.date('Y/m/d').'/';
$upload_dir = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/users-photo/'.$mydir1;
$upload_url='/my/uploads/users-photo/'.$mydir1;



if (file_exists($upload_dir) == false) {
  if (@mkdir($upload_dir , 0755, true) == false ) {
    debug_mail(false,'can not create dir: ',$upload_dir);
    die('error');
  }
}
$arrr=[];foreach (GKS_IMAGE_EXTENSION as $value) $arrr[]=substr($value,1);
$upload_handler = new UploadHandler(array(
                        'max_file_size' => gks_get_max_upload_file_size(true),
                        'image_file_types' => '/\.('.implode('|',$arrr).')$/i',
                        'accept_file_types' => '/\.('.implode('|',$arrr).')$/i',
                        'upload_dir' => $upload_dir,
                        'upload_url' => $upload_url,
                        'thumbnail' => array('max_width' => 92,'max_height' => 92),
                        'user_id' => intval($my_wp_user_id),
                        'for_user_id' => intval($_POST['user_id']),
                        ));
                        

$my_page_title=gks_lang('Μεταφόρτωση φωτογραφίας σε επαφή');
db_open();
stat_record();


if (!isset($upload_handler->response['files'][0]->name)) {
  die('error'); 
}

//$url=get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
//if ($url=='') {
//  update_user_meta( $my_wp_user_id, 'wsl_current_user_image', GKS_SITE_URL . $upload_url.'thumbnail/'.$upload_handler->response['files'][0]->name);  
//  
//}
//
//
//$calc = calc_profilepososto($my_wp_user_id);
//
//$sql="update ".GKS_WP_TABLE_PREFIX."users set gks_last_update=now() where id=".$my_wp_user_id." limit 1";
//$result = $db_link->query($sql); 
//if (!$result) {
//  die('error sql');
//}
  
