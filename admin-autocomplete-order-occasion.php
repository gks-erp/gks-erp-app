<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

//debug_mail(false,'gks_orders_occasion','');

if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση περίστασης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders_occasion','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT id_order_occasion,
gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, 
gks_payment_acquirers.payment_acquirer_name, gks_orders_occasion.mydate_add as occasion_mydate_add
FROM (gks_orders_occasion 
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type) 
LEFT JOIN gks_payment_acquirers ON gks_orders_occasion.pay_method_id = gks_payment_acquirers.id_payment_acquirer ";

$sql.= " where gks_orders_occasion.title like '%".$db_link->escape_string($term)."%' ";
if (isset($_GET['user_id'])) $sql.=" and gks_orders_occasion.user_id=".intval($_GET['user_id']);

$sql.= " order by gks_orders_occasion.mydate_add desc, gks_orders_occasion.title
limit 1000";
//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$allfields=false; if (isset($_GET['allfields']) and intval($_GET['allfields'])!=0) $allfields=true;

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $occasion_title=$row['occasion_title'];
  if ($allfields) {
    $occasion_title = '';
    $temp = trim_gks($row['occasion_title']);      if ($temp!='') $occasion_title.=$temp.' / ';
    $temp = trim_gks($row['occasion_type_descr']);         if ($temp!='') $occasion_title.=$temp.' / ';
    //$temp =  trim_gks($row['payment_acquirer_name']); if ($temp!='') $occasion_title.=$temp.' / ';
    $temp = trim_gks($row['occasion_mydate_add']);   if ($temp!='') $occasion_title.=showDate(strtotime($temp), 'd/m/Y H:i', 1) .' / ';
    if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);
  }
  
  $out[] = array('id' => $row['id_order_occasion'], 'value' => $occasion_title);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



