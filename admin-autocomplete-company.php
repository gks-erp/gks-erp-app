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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση εταιρείας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');




$sql="SELECT id_company,company_title,company_color FROM gks_company
where company_disable=0 and 
(
company_title like '%".$db_link->escape_string($term)."%' or
company_eponimia like '%".$db_link->escape_string($term)."%' or
company_afm like '%".$db_link->escape_string($term)."%' or
company_epaggelma like '%".$db_link->escape_string($term)."%' or
company_phone like '%".$db_link->escape_string($term)."%' or
company_email like '%".$db_link->escape_string($term)."%' or
company_odos like '%".$db_link->escape_string($term)."%'
)";
if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
$sql.=" 
order by company_sortorder,company_title
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
  $out[] = array('id' => $row['id_company'], 'value' => $row['company_title'],'color'=>$row['company_color']);
}


$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



