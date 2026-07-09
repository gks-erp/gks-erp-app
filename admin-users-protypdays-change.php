<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
if (isset($_POST['protypdays']) == false) {
  debug_mail(false,'the protypdays is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' protypdays.'));
  echo json_encode($return); die();  
}

$my_page_title=gks_lang('Αποθήκευση Πρότυπες Ημέρες Ασφάλισης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$sql="delete from gks_users_protypdays where user_id=".$id;
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$protypdays=explode(',',trim_gks(stripslashes(urldecode($_POST['protypdays']))));
foreach ($protypdays as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    $cc=explode('|', $value);
    $v=explode('_', $cc[1]);
    if (count($v) == 3 and $v[0] == 'pd') {
      $sql="insert into gks_users_protypdays (company_id,user_id,ord_mwday,ord_day) values (
      ".intval($cc[0]).",
      ".$id.",
      ".intval($v[1]).",
      ".intval($v[2]).")";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      
    }
  }
} 

$protypdays_campanys_array = array();
$sql_companys = "SELECT gks_company.id_company, gks_company.company_title, gks_company.company_color
FROM gks_company_users LEFT JOIN gks_company ON gks_company_users.company_id = gks_company.id_company
WHERE gks_company_users.user_id=".$id."
order by company_sortorder,company_title";
$result_companys = $db_link->query($sql_companys);        
if (!$result_companys) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
while ($row_companys = $result_companys->fetch_assoc()) {
  $protypdays_campanys_array[$row_companys['id_company']]= array($row_companys['company_title'], $row_companys['company_color']);
}


$protypdays_array = array();
$descr = get_user_protypdays_descr($id,$protypdays_array,'<br>');
$return = array(
  'success' => true, 
  'message' => base64_encode('sss'), 
  'descr' => base64_encode($descr), 
  'array' => base64_encode(json_encode($protypdays_array)),
  'protypdays_campanys_array' => base64_encode(json_encode($protypdays_campanys_array)),
  );
echo json_encode($return); die();

