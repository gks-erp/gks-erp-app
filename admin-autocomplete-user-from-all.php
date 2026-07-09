<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_fullname
FROM ".GKS_WP_TABLE_PREFIX."users
where 
gks_fullname like '%".$db_link->escape_string($term)."%' or 
gks_nickname like '%".$db_link->escape_string($term)."%' or 
gks_mobile like '%".$db_link->escape_string($term)."%' or 
user_email like '%".$db_link->escape_string($term)."%'

order by ".GKS_WP_TABLE_PREFIX."users.gks_fullname
limit 1000";

//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array('id' => $row['ID'], 'value' => $row['gks_fullname']);
}


$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



