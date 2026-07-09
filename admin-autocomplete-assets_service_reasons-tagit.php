<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('texnikos',$my_wp_user_info->roles))  $userrole='texnikos';
  if (in_array('logistis',$my_wp_user_info->roles))  $userrole='logistis';
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {die(); }



$is_equal=false;
if (isset($_GET['equal']) and $_GET['equal'] =='1') $is_equal = true;


if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
//if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση κατάσταση λόγου service');
db_open();
stat_record();


$sql="SELECT id_assets_service_reasons, reasons_descr
FROM gks_assets_service_reasons
where reasons_descr ";
if ($is_equal) {
  $sql.=" = '".$db_link->escape_string($term)."'";
} else {
  $sql.=" like '%".$db_link->escape_string($term)."%'";
}
$sql.=" 
order by reasons_descr
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
  $out[] = array('id' => $row['id_assets_service_reasons'], 'value' => $row['reasons_descr']);
}

if ($is_equal ) {
  $return = array('success' => $fount_count > 0, 'message' => base64_encode('not found'),'out' => $out);
  echo json_encode($return); die();  
} else {
  echo json_encode($out); die();  
}

