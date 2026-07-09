<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση ομάδας χρηστών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_groups','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="select gks_users_groups.id_users_group as id, 
CONCAT_WS('\\\\',
                ug10.group_title,
                ug9.group_title,
                ug8.group_title,
                ug7.group_title,
                ug6.group_title,
                ug5.group_title,
                ug4.group_title,
                ug3.group_title,
                ug2.group_title,
                gks_users_groups.group_title) as descr
FROM ((((((((gks_users_groups
LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group

WHERE gks_users_groups.group_disable=0    
and (
gks_users_groups.group_title like '%".$db_link->escape_string($term)."%' or 
ug2.group_title like '%".$db_link->escape_string($term)."%' or 
ug3.group_title like '%".$db_link->escape_string($term)."%' or 
ug4.group_title like '%".$db_link->escape_string($term)."%' or 
ug5.group_title like '%".$db_link->escape_string($term)."%' or 
ug6.group_title like '%".$db_link->escape_string($term)."%' or 
ug7.group_title like '%".$db_link->escape_string($term)."%' or 
ug8.group_title like '%".$db_link->escape_string($term)."%' or 
ug9.group_title like '%".$db_link->escape_string($term)."%' or 
ug10.group_title like '%".$db_link->escape_string($term)."%' 
)
ORDER BY descr
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
  $out[] = array('id' => $row['id'], 'value' => $row['descr']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



