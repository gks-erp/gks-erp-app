<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('nexi Log');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_nexi_transaction','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$userrole='';

print '<pre>';

$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$mtid=0;if (isset($_GET['mtid'])) $mtid=intval($_GET['mtid']);
$myjson_send=null; $myjson_response=null;
$id_eftpos_transaction=0;
$xxx_transaction_id=0;
if ($id>0) {
  $sql="select id_eftpos_transaction,xxx_transaction_id,send_array,response_array from gks_eftpos_transaction where id_eftpos_transaction=".$id." and payment_acquirer_with_id=7";
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows==0) {
    echo 'rec not found';die();
  } 
  $row = $result->fetch_assoc();
  $id_eftpos_transaction=intval($row['id_eftpos_transaction']);
  $xxx_transaction_id=intval($row['xxx_transaction_id']);
  $myjson_send=json_decode($row['send_array'],true);
  $myjson_response=json_decode($row['response_array'],true);
} else if ($mtid>0){
  $xxx_transaction_id=$mtid;
  
  $sql="select id_eftpos_transaction,xxx_transaction_id,send_array,response_array from gks_eftpos_transaction where xxx_transaction_id=".$mtid." and payment_acquirer_with_id=7";
  //echo $sql;die();
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows==0) {
    echo 'parent record not found'."\n";die();
  } else {
    $row = $result->fetch_assoc();
    $id_eftpos_transaction=intval($row['id_eftpos_transaction']);
    $xxx_transaction_id=intval($row['xxx_transaction_id']);    $myjson_send=json_decode($row['send_array'],true);
    $myjson_response=json_decode($row['response_array'],true);
  }
  
  
} else {
  echo 'id or mtid is not set';die();
}
echo 'eftpos: '.$id_eftpos_transaction."\n";
echo 'xxxpos: '.$xxx_transaction_id."\n";
print "\n";

if ($myjson_send!==null) {
  echo 'Intent Transaction Send: '."\n";
  print_r($myjson_send);
  print "\n\n";
}
if ($myjson_response!==null) {
  echo 'Intent Transaction Response: '."\n";
  print_r($myjson_response);
  print "\n\n";
}


if ($xxx_transaction_id>0) {
  $sql="select myjson from gks_nexi_transaction where id_nexi_transaction=".$xxx_transaction_id;
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows==0) {
    echo 'other record not found with ID '.$xxx_transaction_id;
  } else {
    $row = $result->fetch_assoc();
    $myjson=json_decode($row['myjson'],true);
    echo 'Transaction:'."\n";
    print_r($myjson);
  }  
}
