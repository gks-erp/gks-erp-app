<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$myobject='';    if (isset($_POST['o'])) $myobject   =trim_gks(base64_decode($_POST['o']));
$mysubobject=''; if (isset($_POST['s'])) $mysubobject=trim_gks(base64_decode($_POST['s']));
$myvalue='';     if (isset($_POST['v'])) $myvalue    =trim_gks(base64_decode($_POST['v']));

if ($myobject=='') die();
if ($mysubobject=='') die();



db_open();

$sql="replace into gks_settings_users (
user_id,myobject,mysubobject,myvalue
) values (
".$my_wp_user_id.",
'".$db_link->escape_string($myobject)."',
'".$db_link->escape_string($mysubobject)."',
'".$db_link->escape_string($myvalue)."'
)";
$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die(); }
  
$return = array('success' => true, 'message' => base64_encode('ok'));
echo json_encode($return); die();

