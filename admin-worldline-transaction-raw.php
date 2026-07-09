<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('worldline Log');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_worldline_transaction','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$userrole='';

print '<pre>';

$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$mtid=0;if (isset($_GET['mtid'])) $mtid=intval($_GET['mtid']);
$myjson_send=null; $myjson_response=null;
$id_eftpos_transaction=0;
$xxx_transaction_id=0;
if ($id>0) {
  $sql="select id_eftpos_transaction,xxx_transaction_id,send_array,response_array from gks_eftpos_transaction where id_eftpos_transaction=".$id." and payment_acquirer_with_id=6 order by id_eftpos_transaction desc limit 1";
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
  
  $sql="select id_eftpos_transaction,xxx_transaction_id,send_array,response_array from gks_eftpos_transaction where xxx_transaction_id=".$mtid." and payment_acquirer_with_id=6 order by id_eftpos_transaction desc limit 1";
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
  if (isset($myjson_response['info_data'])) $myjson_response['info_data']=json_decode($myjson_response['info_data'],true);
  print_r($myjson_response);
  print "\n\n";
}

$trn_token='';
if ($xxx_transaction_id>0) {
  $sql="select trn_token,myjson from gks_worldline_transaction where id_worldline_transaction=".$xxx_transaction_id." order by id_worldline_transaction desc limit 1";
  //echo $sql;
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows==0) {
    echo 'other record not found with ID '.$xxx_transaction_id;
  } else {
    $row = $result->fetch_assoc();
    $trn_token=trim_gks($row['trn_token']);
    $myjson=json_decode($row['myjson'],true);
    if (is_string($myjson)) { 
      $myjson = json_decode($myjson, true);//diplo gia na figei to escape
    }
    
    echo 'Transaction:'."\n";
    if (isset($myjson['info_data'])) $myjson['info_data']=json_decode($myjson['info_data'],true);
    if (isset($myjson['receipt'])) $myjson['receipt']=json_decode($myjson['receipt'],true);
    print_r($myjson);
  }  
}

if ($trn_token<>'') {
  echo "\n\n";
  echo 'Token: '.$trn_token."\n";
  $sql="select * from gks_worldline_transaction_app2app_res where res_token='".$db_link->escape_string($trn_token)."'";
  
  $result = $db_link->query($sql);
  if (!$result) { debug_mail(false,'error sql',$sql);die('sql error');}  
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    foreach ($row as $key => $value) {
      if ($key=='res_trn_receipt') {
        $myreceipt=json_decode($value,true);
        echo $key.': '.print_r($myreceipt,true)."\n";
//      } else if ($key=='info_data') {
//        $info_data=json_decode($value,true);
//        echo $key.': '.print_r($info_data,true)."\n";
      } else {
        echo $key.': '.$value."\n";
      }
    } 
    
  }
  
  
  
  
  
}