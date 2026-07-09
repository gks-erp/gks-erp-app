<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Λήψη δεδομένων transfer τύπου οχήματος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);


$sql="SELECT gks_poi.*, gks_poi_en_US.poi_descr_en_US,gks_country.country_name, gks_nomoi.nomos_descr,
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

WHERE gks_poi.id_poi=".$id." limit 1";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}


$row_poi = $result->fetch_assoc();


$return = array('success' => true, 'message' => base64_encode('OK'), 
  'id'=>$id, 
  'poi_parent_id' => intval($row_poi['poi_descr']),
  'poi_type_id' => intval($row_poi['poi_type_id']),
  'poi_type_descr' => trim_gks($row_poi['poi_type_descr']),
  'poi_descr' => trim_gks($row_poi['poi_descr']), 
  'poi_descr_en_US' => trim_gks($row_poi['poi_descr_en_US']),
  'poi_phone' => trim_gks($row_poi['poi_phone']), 
  'poi_email' => trim_gks($row_poi['poi_email']), 
  'poi_odos' => trim_gks($row_poi['poi_odos']), 
  'poi_arithmos' => trim_gks($row_poi['poi_arithmos']), 
  'poi_orofos' => trim_gks($row_poi['poi_orofos']), 
  'poi_perioxi' => trim_gks($row_poi['poi_perioxi']), 
  'poi_poli' => trim_gks($row_poi['poi_poli']), 
  'poi_tk' => trim_gks($row_poi['poi_tk']), 
  'poi_nomos_id' => intval($row_poi['poi_nomos_id']), 
  'nomos_descr' => trim_gks($row_poi['nomos_descr']),
  'poi_country_id' => intval($row_poi['poi_country_id']), 
  'country_name' => trim_gks($row_poi['country_name']),
  'poi_map_latitude' => floatval($row_poi['poi_map_latitude']), 
  'poi_map_longitude' => floatval($row_poi['poi_map_longitude']), 
  'poi_disable' => intval($row_poi['poi_disable']), 
  'poi_color' => trim_gks($row_poi['poi_color']), 
  'poi_sortorder' => trim_gks($row_poi['poi_sortorder']), 
  'poi_comments' => trim_gks($row_poi['poi_comments']), 


  'ccc' => intval($row_poi['ccc']),

  'dirpath' => trim_gks($row_poi['dirpath']), 
  'fullpath' => trim_gks($row_poi['fullpath']), 
  
  
);



echo json_encode($return); die();



