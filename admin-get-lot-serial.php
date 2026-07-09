<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων παρτίδας-serial number');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


//echo '<pre>dddddddddddd';die();



$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);
$aa=0; if (isset($_POST['aa'])) $aa=intval($_POST['aa']);
$ls=0; if (isset($_POST['ls'])) $ls=intval($_POST['ls']);


$sql="select * from gks_eshop_product_lots where id_lot_product=".$id." limit 1";
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


$row = $result->fetch_assoc();
$return = array('success' => true, 'message' => base64_encode('OK'), 
  'id'=>$id, 
  'aa'=>$aa, 
  'ls'=>$ls, 
  'lotproduct_id' => intval($row['lotproduct_id']),
  'lot_name' => trim_gks($row['lot_name']),
  'lot_descr' => trim_gks($row['lot_descr']),
  'lot_date_production' => (trim_gks($row['lot_date_production']) =='' ? '' : showDate(strtotime($row['lot_date_production']), 'd/m/Y', 1)),
  'lot_date_expire' => (trim_gks($row['lot_date_expire']) =='' ? '' : showDate(strtotime($row['lot_date_expire']), 'd/m/Y', 1)),
);



echo json_encode($return); die();



