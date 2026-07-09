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

$my_page_title=gks_lang('Αποθήκευση φωτογραφίας από Web cam');
db_open();
stat_record();

$data_picture=''; if (isset($_POST['data_picture'])) $data_picture=trim_gks($_POST['data_picture']);
$object_name=''; if (isset($_POST['object_name'])) $object_name=trim_gks(base64_decode($_POST['object_name']));

if (startwith($data_picture,'data:image/jpeg;base64,')==false) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων φωτογραφίας<br>Ξαναδοκιμάστε')));
  echo json_encode($return); die(); } 

$data_picture=substr($data_picture, 23);
$data_picture=base64_decode($data_picture);




$object_map=gks_FilesObjectList_map($object_name);
$object_path=$object_map['path'];
$object_table=$object_map['table'];
$object_tid=$object_map['tid'];
$object_pid=$object_map['pid'];


$mydir1=intval($id).'/';
$filename='webcam_'.showDate(time(),'Y_m_d_H_i_s',1).'_'.rand(10000,99999).'.jpg';
$upload_dir= GKS_FileServerShare.$object_path.$mydir1;
$upload_file=$upload_dir.$filename;
$upload_url='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($object_path.$mydir1.$filename);
$upload_url_thump='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($object_path.$mydir1.'thumbnail/'.$filename);
$relative_path=$object_path.$mydir1.$filename;

if (file_exists($upload_dir) == false) {
  if (@mkdir($upload_dir , 0755, true) == false ) {
    debug_mail(false,'can not create dir: ',$upload_dir);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα. Δεν μπορεί να δημιουργηθεί ο φάκελος αποθήκευσης')));
    echo json_encode($return); die();
  }
}

$ret = &file_put_contents($upload_file,$data_picture);
if ($ret===false) {
  debug_mail(false,'can not save file',$upload_file);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την αποθήκευση του αρχείου').'<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
  echo json_encode($return); die();}
  
$file_size =filesize($upload_file);


$out_name_html='<a href="'.$upload_url.'" target="_blank">'.$filename.'</a>';

$img_thump='<a class="filesobjectlist_lightgallery_gks_fileserver_item" href="'.$upload_url.'" data-download-url="'.$upload_url.'&download=1">'.
'<img style="max-width:96px;max-height:96px;" src="'.$upload_url_thump.'">'.
'</a>';
$select_for_print='<img class="filesobjectlist_set_print_photo" data-value=0 data-path="'.$relative_path.'" src="img/0b.png">';
$public_file='<img class="filesobjectlist_set_public_file" data-path="'.$relative_path.'" src="img/0bbl.png" data-expire_date="" data-shortcode_url="" data-myopencount="0">';

$scandir_rec_echo= 
'<tr class="webcam_new_add tddd" data-path="'.$relative_path.'">'.
  '<th class="mytdcm" scope="row" nowrap>'.
   '<i class="fas fa-trash-alt filesobjectlist_delete_upload_photo" data-path="'.$relative_path.'"></i>'.
  '</th>'.
  '<td class="mytdcml fol_td_name">'.$out_name_html.'</td>'.
  '<td class="mytdcm tdimg_descr"></td>'.
  '<td class="mytdcm tdimg">'.$img_thump.'</td>'.
  '<td class="mytdcm fol_td_date">'.secondsago(time()).'</td>'.
  '<td class="mytdcmr" nowrap>'. number_format(($file_size/1024/1024),2,',','.').' MB</td>'.
  '<td class="mytdcm" nowrap>'.
   '<a href="'.$upload_url.'&download=1">'.
    '<i class="fas fa-download fol_td_download"></i>'.
   '</a>'.
  '</td>'.
  '<td class="mytdcm fol_selprint"  nowrap>'.$select_for_print.'</td>'.
  '<td class="mytdcm fol_selpublic" nowrap>'.$public_file.'</td>'.
'</tr>';


$sql="insert into ".$object_table." (
  mydate,ip,".$object_pid.",photo_url,mysize,user_add_id,show_print,filesobjectlist
) values (
  NOW(),
  '".$db_link->escape_string($gkIP)."',
  ".$id.",
  '".$db_link->escape_string($relative_path)."',
  ".$file_size.",
  ".$my_wp_user_id.",
  0,
  1
)";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'))));
  echo json_encode($return); die();} 
    
$return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής αποθήκευση')), 'html_tr'=>$scandir_rec_echo);
echo json_encode($return); die();
  
$return = array('success' => false, 'message' => base64_encode(
'<pre>'.
$id."\n".
$upload_dir."\n".
$upload_url."\n".
print_r($object_map,true)

));
echo json_encode($return); die();
