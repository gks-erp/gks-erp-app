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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση σημείου ενδιαφέροντος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
}

$sql="SELECT gks_poi.*, gks_country.country_name, gks_nomoi.nomos_descr,
gks_poi_type.poi_type_descr,
ccsubpoi.ccc,

ug2.poi_descr AS gt2, 
ug3.poi_descr AS gt3, 
ug4.poi_descr AS gt4, 
ug5.poi_descr AS gt5, 
ug6.poi_descr AS gt6, 
ug7.poi_descr AS gt7, 
ug8.poi_descr AS gt8, 
ug9.poi_descr AS gt9, 
ug10.poi_descr AS gt10, 


ug2.id_poi AS id2, 
ug3.id_poi AS id3, 
ug4.id_poi AS id4, 
ug5.id_poi AS id5,
ug6.id_poi AS id6,
ug7.id_poi AS id7,
ug8.id_poi AS id8,
ug9.id_poi AS id9,
ug10.id_poi AS id10,

CONCAT_WS('\\\\',
                 ug10.poi_descr,
                 ug9.poi_descr,
                 ug8.poi_descr,
                 ug7.poi_descr,
                 ug6.poi_descr,
                 ug5.poi_descr,
                 ug4.poi_descr,
                 ug3.poi_descr,
                 ug2.poi_descr,
                 gks_poi.poi_descr) as fullpath,
CONCAT_WS('\\\\',
                 ug10.poi_descr,
                 ug9.poi_descr,
                 ug8.poi_descr,
                 ug7.poi_descr,
                 ug6.poi_descr,
                 ug5.poi_descr,
                 ug4.poi_descr,
                 ug3.poi_descr,
                 ug2.poi_descr) as dirpath


FROM (((((((((((((gks_poi 
LEFT JOIN (
  SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id)

LEFT JOIN gks_poi_type ON gks_poi.poi_type_id = gks_poi_type.id_poi_type) 
LEFT JOIN gks_country ON gks_poi.poi_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_poi.poi_nomos_id = gks_nomoi.id_nomos)

LEFT JOIN (
  SELECT gks_poi.poi_parent_id, Count(gks_poi.id_poi) AS ccc
  FROM gks_poi
  GROUP BY gks_poi.poi_parent_id
) AS ccsubpoi ON gks_poi.id_poi = ccsubpoi.poi_parent_id)

LEFT JOIN gks_poi AS ug2 ON gks_poi.poi_parent_id = ug2.id_poi) 
LEFT JOIN gks_poi AS ug3 ON ug2.poi_parent_id = ug3.id_poi)
LEFT JOIN gks_poi AS ug4 ON ug3.poi_parent_id = ug4.id_poi)
LEFT JOIN gks_poi AS ug5 ON ug4.poi_parent_id = ug5.id_poi)
LEFT JOIN gks_poi AS ug6 ON ug5.poi_parent_id = ug6.id_poi)
LEFT JOIN gks_poi AS ug7 ON ug6.poi_parent_id = ug7.id_poi)
LEFT JOIN gks_poi AS ug8 ON ug7.poi_parent_id = ug8.id_poi)
LEFT JOIN gks_poi AS ug9 ON ug8.poi_parent_id = ug9.id_poi)
LEFT JOIN gks_poi AS ug10 ON ug9.poi_parent_id = ug10.id_poi

WHERE gks_poi.poi_disable=0 ";
$poi_type_ids=[];
if (isset($_GET['types'])) {
  $temp=explode(',',$_GET['types']);
  foreach ($temp as $value) {
    $value=intval($value);
    if ($value>0) $poi_type_ids[]= $value;
  } 
}
if (count($poi_type_ids)>0) {
  $sql.=" and gks_poi.poi_type_id in (".implode(',',$poi_type_ids).")";  
}

$sql.=" and (";
 
$mywhere='';
foreach ($term_array as $value) {
  $value_en = greekkeybord($value);
  $mywhere.=" (
  gks_poi.poi_descr like '%".$db_link->escape_string($value)."%' or
  gks_poi_en_US.poi_descr_en_US like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_locode like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_iata_code like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_phone like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_email like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_odos like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_perioxi like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_poli like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_tk like '%".$db_link->escape_string($value)."%' or
  gks_poi.poi_comments like '%".$db_link->escape_string($value)."%' or
  
  CONCAT_WS('\\\\',
   ug10.poi_descr,
   ug9.poi_descr,
   ug8.poi_descr,
   ug7.poi_descr,
   ug6.poi_descr,
   ug5.poi_descr,
   ug4.poi_descr,
   ug3.poi_descr,
   ug2.poi_descr,
   gks_poi.poi_descr) like '%".$db_link->escape_string($value)."%'
 
  ) and ";
} 
  
if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
$sql.=$mywhere.")  order by fullpath
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
  $out[] = array('id' => $row['id_poi'], 'value' => $row['fullpath'], 'type_id' => $row['poi_type_id']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



