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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση εργασίας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT id_production_ergasia, production_ergasia_descr
FROM gks_production_ergasies
where production_ergasia_descr like '%".$db_link->escape_string($term)."%'";
if (isset($_GET['notin'])) $sql.=" and id_production_ergasia not in (".intval($_GET['notin']).")";
$sql.=" 
order by production_ergasia_sortorder, production_ergasia_descr
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
  $out[] = array('id' => $row['id_production_ergasia'], 'value' => $row['production_ergasia_descr']);
}


$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



