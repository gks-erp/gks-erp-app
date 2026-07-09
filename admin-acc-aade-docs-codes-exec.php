<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Αποθήκευση Barcodes από έγγραφο myData');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_barcodes','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']),'html'=>base64_encode($perm_ret['message']));echo json_encode($return); die();}

$epafi_id=0; if (isset($_POST['epafi_id'])) $epafi_id=intval($_POST['epafi_id']);
$mark=''; if (isset($_POST['mark'])) $mark=trim_gks($_POST['mark']);
$products_str = trim_gks(base64_decode($_POST['products_str']));
$products = json_decode($products_str, true);
if ($products === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error products',$_POST['products_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

foreach ($products as $myp) {
  $myp['itemCode']=trim_gks($myp['itemCode']);
  $myp['product_id']=intval($myp['product_id']);
  if ($myp['itemCode']!='' and $myp['product_id']>0) {
    $sql="select * from gks_barcodes 
    where barcode='".$db_link->escape_string($myp['itemCode'])."' 
    and product_id=".$myp['product_id']." 
    and user_id in (0,".$epafi_id.")
    order by user_id desc";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows>=1) {
      //$row = $result->fetch_assoc(); 
      //min kaneis tipota         
    } else {
      $sql="insert into gks_barcodes (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      barcode,barcode_descr,product_id,user_id,disable_barcode,comments
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($myp['itemCode'])."',
      '".$db_link->escape_string($myp['itemDescr'])."',
      ".$myp['product_id'].",
      ".$epafi_id.",0,'".$db_link->escape_string(gks_lang('Εισαγωγή από myData').', '.gks_lang('ΜΑΡΚ').' '.$mark)."')";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      
      
    }
  }
} 

$return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτηχής αποθήκευση/ενημέρωση barcodes')));
echo json_encode($return); die();  

echo '<pre>sssss '.$epafi_id.' ';print_r($products);die();
