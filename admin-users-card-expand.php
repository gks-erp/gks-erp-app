<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$state=0; if (isset($_POST['s'])) $state=intval($_POST['s']);
$item=''; if (isset($_POST['i'])) $item=trim_gks(base64_decode($_POST['i']));
$url=''; if (isset($_POST['u'])) $url=trim_gks(base64_decode($_POST['u']));
$parts=@parse_url($url);
$url='';
if (isset($parts['path'])) $url=basename($parts['path']);
if ($state!=0 and $state!=1) die();
if ($item=='') die();
if ($url=='') die();
if ($url=='my') $url='index.php';

//$return = array('success' => false, 'message' => base64_encode('<pre>state:'.$state.' item:'.$item.' url:'.$url.' gg:'.print_r($parts,true)));
//echo json_encode($return); die();


db_open();

$sql="replace into gks_users_card_expand (
user_id,url,item,state
) values (
".$my_wp_user_id.",
'".$db_link->escape_string($url)."',
'".$db_link->escape_string($item)."',
".$state."
)";
//debug_mail(false,'error sql',$sql);
$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die(); }
  
$return = array('success' => true, 'message' => base64_encode('ok'));
echo json_encode($return); die();

