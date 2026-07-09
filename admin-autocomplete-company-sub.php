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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση υποκαταστήματος εταιρείας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_subs','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');



$sql="SELECT gks_company_subs.id_company_sub, gks_company_subs.company_sub_title, gks_company_subs.company_sub_color, gks_company.company_color
FROM gks_company_subs
LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
where 1=1 ";
if (isset($_GET['company_id'])) $sql.=" and company_id=".intval($_GET['company_id']);

$sql.=" and (
company_sub_title like '%".$db_link->escape_string($term)."%' or
company_sub_phone like '%".$db_link->escape_string($term)."%' or
company_sub_email like '%".$db_link->escape_string($term)."%' or
company_sub_odos like '%".$db_link->escape_string($term)."%'
)";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
$sql.=" 
order by gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title
limit 1000";
//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$out=array();

if (isset($_GET['company_id']) and intval($_GET['company_id'])>0) {
  if (isset($_GET['and_kentriko']) and $_GET['and_kentriko']=='1') {
    $company_color='#000000';
    $sql="SELECT company_color from gks_company where id_company=".intval($_GET['company_id']);
    $result_color = $db_link->query($sql);
    if (!$result_color) {
      debug_mail(false,'error sql',$sql);
      die();
    }
    if ($result_color->num_rows>=1) {
      $row_color = $result_color->fetch_assoc();
      $company_color=$row_color['company_color'];
    }
    if (count($perm_id_company_sub_ids)==0 or in_array(0,$perm_id_company_sub_ids)) {
      $out[] = array('id' => '0', 'value' => gks_lang('Κεντρικό'),'color'=>$company_color);
    }
  }
} else {
  if (isset($_GET['and_kentriko']) and $_GET['and_kentriko']=='1') {
    if (count($perm_id_company_sub_ids)==0 or in_array(0,$perm_id_company_sub_ids)) {
      $out[] = array('id' => '0', 'value' => gks_lang('Κεντρικό'),'color'=>'#ffffff');
    }
  }
}


while ($row = $result->fetch_assoc()) {
  $out[] = array('id' => $row['id_company_sub'], 'value' => $row['company_sub_title'],'color'=>$row['company_sub_color']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



