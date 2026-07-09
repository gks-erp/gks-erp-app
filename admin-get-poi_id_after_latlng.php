<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$myi='';if (isset($_POST['myi'])) $myi=trim($_POST['myi']);
$val_place_lat='';if (isset($_POST['lat'])) $val_place_lat=floatval($_POST['lat']);
$val_place_lng='';if (isset($_POST['lng'])) $val_place_lng=floatval($_POST['lng']);
if (($myi!='from' and $myi!='to') or ($val_place_lat==0 and $val_place_lng==0)) {
  debug_mail(false,'the myi is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί o τύπος του σημείου (from/to) ή το lat/lng')));
  echo json_encode($return); die();
}


$my_page_title=gks_lang('Αναζήτηση σημείου με βάση το lat,lng');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi','autocomplete',-1);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="SELECT mypoienabled.poi_id, gks_poi.id_poi, gks_poi.poi_descr, gks_poi.poi_map_latitude, gks_poi.poi_map_longitude, gks_poi.poi_areas
FROM (
  select DISTINCTROW poi_id
  from (
    SELECT DISTINCTROW poi_id_from as  poi_id
    FROM gks_transfer_pricelist
    WHERE transfer_pricelist_disable=0
    union
    SELECT DISTINCTROW poi_id_to as  poi_id
    FROM gks_transfer_pricelist
    WHERE transfer_pricelist_disable=0
  ) as mypoi_enabled
)  AS mypoienabled 
LEFT JOIN gks_poi ON mypoienabled.poi_id = gks_poi.id_poi
WHERE gks_poi.poi_disable=0 and
".$val_place_lat." >=gks_poi.poi_bound_south and
".$val_place_lat." <=gks_poi.poi_bound_north and
".$val_place_lng." >=gks_poi.poi_bound_west and
".$val_place_lng." <=gks_poi.poi_bound_east";

$sql="SELECT gks_poi.id_poi, gks_poi.poi_descr, gks_poi.poi_map_latitude, gks_poi.poi_map_longitude, gks_poi.poi_areas
FROM gks_poi
WHERE gks_poi.poi_disable=0 and
".$val_place_lat." >=gks_poi.poi_bound_south and
".$val_place_lat." <=gks_poi.poi_bound_north and
".$val_place_lng." >=gks_poi.poi_bound_west and
".$val_place_lng." <=gks_poi.poi_bound_east";

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql."\n".$db_link->errno . '-'.$db_link->error);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

$geo_points=[];
while ($row = $result->fetch_assoc()) {
  $geo_points[$row['id_poi']]=$row;  
}

//print '<pre>';print_r($geo_points);die();

$found_id_poi=0;
$found_poi_descr='';
$found_poi_map_latitude=0;
$found_poi_map_longitude=0;


$point_is_inside=false;
foreach ($geo_points as $id_poi => $row) {
  $poi_areas=unserialize($row['poi_areas']);
  
  if (isset($poi_areas['polygons']) and is_array($poi_areas['polygons']) and count($poi_areas['polygons'])>0) {
    //$return['message']=base64_encode('<pre>'.print_r($poi_areas['polygons'],true));return $return;
    foreach ($poi_areas['polygons'] as $polygon) {
      //$return['message']=base64_encode('<pre>'.print_r($polygon['points'],true));return $return;
      
      $point_is_inside=gks_transfer_is_in_polygon($val_place_lng,$val_place_lat,$polygon['points']);
      //echo '<pre>'.$val_place_lng.'|'.$val_place_lat.print_r($polygon['points']);
      
      //$return['message']=base64_encode('<pre>|'.$point_is_inside."|\n".print_r($polygon['points'],true));return $return;

      if ($point_is_inside) {
        $found_id_poi=intval($id_poi);
        $found_poi_descr=$row['poi_descr'];
        $found_poi_map_latitude=$row['poi_map_latitude'];
        $found_poi_map_longitude=$row['poi_map_longitude'];
        
        break;
      }
    } 
  }
  if ($point_is_inside) break; 
}
  
$return = array(
  'success' => true, 
  'message' => base64_encode('OK'),
  'id_poi' => $found_id_poi,
  'poi_descr' => $found_poi_descr,
  'lat' => $found_poi_map_latitude,
  'lng' => $found_poi_map_longitude,
);
echo json_encode($return); die();



$return = array('success' => false, 'message' => base64_encode('<pre>σσσσσσσσσσσσσσσσσ '.$found_id_poi.'|'.$found_poi_descr.'|'.$found_poi_map_latitude.'|'.$found_poi_map_longitude.'|'));
echo json_encode($return); die();

