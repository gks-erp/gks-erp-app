<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Ψηφιακές υπογραφές από πάροχο. Raw');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_paroxos_signature','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$userrole='';

print '<pre>';

$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$myjson_send=null; $myjson_response=null;
if ($id>0) {
  $sql="select response from gks_paroxos_signature where id_paroxos_signature=".$id;
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows==0) {
    echo 'rec not found';die();
  } 
  $row = $result->fetch_assoc();
  $myjson_response=unserialize($row['response']);
} else {
  echo 'id is not set';die();
}
echo 'id: '.$id."\n";
print "\n";

if ($myjson_send!==null) {
  echo 'Signature Send: '."\n";
  print_r($myjson_send);
  print "\n\n";
}
if ($myjson_response!==null) {
  echo 'Signature Response: '."\n";
  print_r($myjson_response);
  print "\n\n";
}
