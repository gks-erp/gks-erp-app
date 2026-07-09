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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση περιοχής transfer');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_transfer_area','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT gks_transfer_area.*, ccsubpoi.ccc,
ug2.transfer_area_descr AS gt2, 
ug3.transfer_area_descr AS gt3, 
ug4.transfer_area_descr AS gt4, 
ug5.transfer_area_descr AS gt5,
ug6.transfer_area_descr AS gt6,
ug7.transfer_area_descr AS gt7,
ug8.transfer_area_descr AS gt8,
ug9.transfer_area_descr AS gt9,
ug10.transfer_area_descr AS gt10,

ug2.id_transfer_area AS id2, 
ug3.id_transfer_area AS id3, 
ug4.id_transfer_area AS id4, 
ug5.id_transfer_area AS id5,
ug6.id_transfer_area AS id6,
ug7.id_transfer_area AS id7,
ug8.id_transfer_area AS id8,
ug9.id_transfer_area AS id9,
ug10.id_transfer_area AS id10,
CONCAT_WS('\\\\',
                ug10.transfer_area_descr,
                ug9.transfer_area_descr,
                ug8.transfer_area_descr,
                ug7.transfer_area_descr,
                ug6.transfer_area_descr,
                ug5.transfer_area_descr,
                ug4.transfer_area_descr,
                ug3.transfer_area_descr,
                ug2.transfer_area_descr,
                gks_transfer_area.transfer_area_descr) as fullpath,
CONCAT_WS('\\\\',
                ug10.transfer_area_descr,
                ug9.transfer_area_descr,
                ug8.transfer_area_descr,
                ug7.transfer_area_descr,
                ug6.transfer_area_descr,
                ug5.transfer_area_descr,
                ug4.transfer_area_descr,
                ug3.transfer_area_descr,
                ug2.transfer_area_descr) as dirpath
FROM (((((((((gks_transfer_area
LEFT JOIN (
  SELECT transfer_area_id, Count(poi_id) AS ccc
  FROM gks_transfer_area2poi
  GROUP BY transfer_area_id
) AS ccsubpoi ON gks_transfer_area.id_transfer_area = ccsubpoi.transfer_area_id)

LEFT JOIN gks_transfer_area AS ug2  ON gks_transfer_area.transfer_area_parent_id = ug2.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug3  ON ug2.transfer_area_parent_id = ug3.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug4  ON ug3.transfer_area_parent_id = ug4.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug5  ON ug4.transfer_area_parent_id = ug5.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug6  ON ug5.transfer_area_parent_id = ug6.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug7  ON ug6.transfer_area_parent_id = ug7.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug8  ON ug7.transfer_area_parent_id = ug8.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug9  ON ug8.transfer_area_parent_id = ug9.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug10 ON ug9.transfer_area_parent_id = ug10.id_transfer_area
WHERE gks_transfer_area.transfer_area_disable=0    
and (
gks_transfer_area.transfer_area_descr like '%".$db_link->escape_string($term)."%' or 
ug2.transfer_area_descr like '%".$db_link->escape_string($term)."%' or 
ug3.transfer_area_descr like '%".$db_link->escape_string($term)."%' or 
ug4.transfer_area_descr like '%".$db_link->escape_string($term)."%' or 
ug5.transfer_area_descr like '%".$db_link->escape_string($term)."%' or
ug6.transfer_area_descr like '%".$db_link->escape_string($term)."%' or
ug7.transfer_area_descr like '%".$db_link->escape_string($term)."%' or
ug8.transfer_area_descr like '%".$db_link->escape_string($term)."%' or
ug9.transfer_area_descr like '%".$db_link->escape_string($term)."%' or
ug10.transfer_area_descr like '%".$db_link->escape_string($term)."%'

)
ORDER BY fullpath
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
  $out[] = array('id' => $row['id_transfer_area'], 'value' => $row['fullpath']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



