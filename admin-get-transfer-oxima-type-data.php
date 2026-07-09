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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_transfer_oxima_type','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);


$sql="SELECT gks_transfer_oxima_type.*
FROM gks_transfer_oxima_type 
where gks_transfer_oxima_type.id_transfer_oxima_type=".$id." limit 1";
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


$row_transfer_oxima_type = $result->fetch_assoc();

  
$myimgurl=trim_gks($row_transfer_oxima_type['transfer_oxima_type_photo'].'');
$photo_url='';
if ($myimgurl != '') {
  $mydir = dirname($myimgurl);
  if (endwith($mydir,'/thumbnail')) {
    $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
  } else {
    $photo_url=$myimgurl;
  }
}


$data_transfer_id=array();

$sql_oximatype2transfer="SELECT transfer_oxima_type_id, transfer_id
FROM gks_transfer_oximatype2transfer
WHERE transfer_oxima_type_id=".$id."
AND transfer_id>0;";
$result_oximatype2transfer = $db_link->query($sql_oximatype2transfer);        
if (!$result_oximatype2transfer) {
  debug_mail(false,'error sql',$sql_oximatype2transfer);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
while ($row_oximatype2transfer = $result_oximatype2transfer->fetch_assoc()) {
  $data_transfer_id[]= $row_oximatype2transfer['transfer_id']; 
}
      
$return = array('success' => true, 'message' => base64_encode('OK'), 
  'id'=>$id, 
  'transfer_oxima_type_descr' => trim_gks($row_transfer_oxima_type['transfer_oxima_type_descr']), 
  'transfer_oxima_type_photo'=> trim_gks($row_transfer_oxima_type['transfer_oxima_type_photo']),
  'photo_url'=>$photo_url, 
  'transfer_oxima_type_site_text'=> trim_gks($row_transfer_oxima_type['transfer_oxima_type_site_text']), 
  'transfer_oxima_type_comments' => nl2br_gks($row_transfer_oxima_type['transfer_oxima_type_comments']), 
  'transfer_oxima_type_max_epivates' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_epivates'])), 
  'transfer_oxima_type_max_suitcases' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_suitcases'])), 
  'transfer_oxima_type_max_booster' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_booster'])), 
  'transfer_oxima_type_max_kareklakia' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_kareklakia'])), 
  'transfer_oxima_type_max_amajidia' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_amajidia'])), 
  'transfer_oxima_type_max_golfbag' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_golfbag'])), 
  'transfer_oxima_type_max_skis' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_skis'])), 
  'transfer_oxima_type_max_5minstop' => intval(trim_gks($row_transfer_oxima_type['transfer_oxima_type_max_5minstop'])), 
  
  
  'transfer_oxima_type_service_free_wifi' =>      intval($row_transfer_oxima_type['transfer_oxima_type_service_free_wifi']),
  'transfer_oxima_type_service_bottled_water'=>   intval($row_transfer_oxima_type['transfer_oxima_type_service_bottled_water']),
  'transfer_oxima_type_service_door_to_door'=>    intval($row_transfer_oxima_type['transfer_oxima_type_service_door_to_door']),
  'transfer_oxima_type_service_porter'=>          intval($row_transfer_oxima_type['transfer_oxima_type_service_porter']),
  'transfer_oxima_type_service_treat_yourself'=>  intval($row_transfer_oxima_type['transfer_oxima_type_service_treat_yourself']),
  
  'transfer_oxima_type_price_booster'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_booster']),
  'transfer_oxima_type_price_kareklakia'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_kareklakia']),
  'transfer_oxima_type_price_amajidia'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_amajidia']),
  'transfer_oxima_type_price_golfbag'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_golfbag']),
  'transfer_oxima_type_price_skis'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_skis']),
  'transfer_oxima_type_price_5minstop'=> myCurrencyFormat($row_transfer_oxima_type['transfer_oxima_type_price_5minstop']),
  'data_transfer_id' => implode(',',$data_transfer_id),
);



echo json_encode($return); die();



