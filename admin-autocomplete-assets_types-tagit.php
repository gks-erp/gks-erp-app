<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$is_equal=false;
if (isset($_GET['equal']) and $_GET['equal'] =='1') $is_equal = true;


if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
//if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση τύπου παγίου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_type','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="SELECT id_asset_type, asset_type_descr
FROM gks_assets_type
where asset_type_descr ";
if ($is_equal) {
  $sql.=" = '".$db_link->escape_string($term)."'";
} else {
  $sql.=" like '%".$db_link->escape_string($term)."%'";
}
$sql.=" 
order by asset_type_sortorder,asset_type_descr
limit 1000";
//echo $sql;
//debug_mail(false,'error sql',$sql);

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die();
}

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array('id' => $row['id_asset_type'], 'value' => $row['asset_type_descr']);
}

if ($is_equal ) {
  $return = array('success' => $fount_count > 0, 'message' => base64_encode('not found'),'out' => $out);
  echo json_encode($return); die();  
} else {
  $return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
  echo json_encode($return); die();
  //echo json_encode($out); die();  
}

//$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
//echo json_encode($return); die();
