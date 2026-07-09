<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$narrow=0; if (isset($_POST['narrow'])) $narrow=intval($_POST['narrow']);
if ($narrow<>1) $narrow=0;
//$return = array('success' => false, 'message' => base64_encode('<pre>state:'.$state.' item:'.$item.' url:'.$url.' gg:'.print_r($parts,true)));
//echo json_encode($return); die();

$my_page_title=gks_lang('menu narrow togle');

db_open();
stat_record();

$sql="replace into gks_settings_users (
user_id,myobject,mysubobject,myvalue
) values (
".$my_wp_user_id.",
'menu',
'narrow',
'".$narrow."'
)";
//debug_mail(false,'error sql',$sql);
$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die(); }
  
$return = array('success' => true, 'message' => base64_encode('ok'));
echo json_encode($return); die();

