<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$page='';if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page']));
$transaction_type=''; if (isset($_POST['transaction_type'])) $transaction_type=trim_gks(base64_decode($_POST['transaction_type']));
$doc_id=0;if (isset($_POST['doc_id'])) $doc_id=intval($_POST['doc_id']);
$sessionId=''; if (isset($_POST['sessionId'])) $sessionId=trim_gks(base64_decode($_POST['sessionId']));
$id_eftpos_transaction=''; if (isset($_POST['id_eftpos_transaction'])) $id_eftpos_transaction=intval($_POST['id_eftpos_transaction']);

$doc_table='';
if ($page=='/my/admin-pos-run.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-eftpos-transaction.php') $doc_table='gks_eftpos_transaction';
if ($page=='/my/admin-acc-inv-item.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-acc-pay-item.php') $doc_table='gks_acc_pay';

if (in_array($transaction_type,['sale','fullvoid','fullvoiderp','refund','refunderp'])==false) {
  debug_mail(false,'the transaction_type is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' transaction_type.'));
  echo json_encode($return); die();}

if (in_array($transaction_type,['sale'])) {
  if ($doc_id<=0) {
    debug_mail(false,'the id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();}
}

$my_page_title=gks_lang('Αναμονή εκτέλεσης συναλλαγής EFT-POS για το παραστατικού').': '.$transaction_type.' '.$doc_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $doc_table,($doc_id==-1 ? 'add':'edit'),$doc_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


if ($sessionId=='' or $id_eftpos_transaction<=0) {
  debug_mail(false,'data error',                                 gks_lang('Σφάλμα δεδομένων').' (1)');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (1)'));
  echo json_encode($return); die();}

$sql="SELECT gks_eftpos_transaction.*
FROM gks_eftpos_transaction 
where id_eftpos_transaction=".$id_eftpos_transaction."
and sessionId='".$db_link->escape_string($sessionId)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

if ($result->num_rows!=1) {
  debug_mail(false,'transaction not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής με για αυτό το παραστατικό')));
  echo json_encode($return); die();}    
$row_tra = $result->fetch_assoc();  

$transaction_status=$row_tra['transaction_status'];
$payment_acquirer_with_id=intval($row_tra['payment_acquirer_with_id']);
$company_id=intval($row_tra['company_id']);


$sql="select * from gks_company where id_company=".$company_id;
//debug_mail(false,'error sql',$sql);
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'compant not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$row_inv['company_id'],gks_lang('Δεν βρέθηκε η εταιρεία με ID [1]'))));
  echo json_encode($return); die();}
$row_company = $result->fetch_assoc();

$worldline_implementation='';
if ($payment_acquirer_with_id==6) {//worldline
  $sql_wl="select worldline_implementation from gks_worldline_transaction where eftpos_transaction_id=".$id_eftpos_transaction;
  $result_wl = $db_link->query($sql_wl);        
  if (!$result_wl) {
    debug_mail(false,'error sql',$sql_wl);
    $return_wl = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_wl->num_rows==1) {
    $row_wl = $result_wl->fetch_assoc();
    $worldline_implementation=trim_gks($row_wl['worldline_implementation']);
  }
}

if ($worldline_implementation=='app2app') {
  $access_token='';
} else {
  $ret=gks_eftpos_get_token($payment_acquirer_with_id,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
}

$data=array(
  'sessionId' => $sessionId,
  'access_token' => $access_token,
  'payment_acquirer_with_id' => $payment_acquirer_with_id,
  'row_company' => $row_company,
  'row_tra' => $row_tra,
);

//echo '<pre>sssssssssss '.$company_id;print_r($row_tra);die();


//debug_mail(false,'kkkkkkkkk 1',print_r($data,true));

$ret=gks_eftpos_sales_request_get_status($payment_acquirer_with_id,$data);
if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}




$return=array();
$return['success']=true;
$return['message']=base64_encode($ret['message']);
$return['data']=array(
  'sessionId' => $sessionId,
  'id_eftpos_transaction' => $id_eftpos_transaction,
  'payment_acquirer_with_id' => $payment_acquirer_with_id,
  //'access_token' => $access_token,
  'status' => (isset($ret['status']) ? $ret['status'] : '['.print_r($ret,true).']'),
  'transaction' => array('html'=> gks_lang('Σφάλμα').' '.gks_lang('Δεν βρέθηκε η συναλλαγή'),'id_acc_xxx_payment' => 0),
);




if ($ret['status']=='done') {
  $return['data']['aaaaaaaa']='1';
  if ($doc_table=='gks_acc_pay' and 
      in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
    $sql="select acc_pay_id,acc_pay_method_id from gks_acc_pay_payment where transaction_id=".$id_eftpos_transaction;
    $return['data']['aaaaaaaa']='2 '.$sql;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows==1) {
      $return['data']['aaaaaaaa']='3 '.$sql;
      $row_acc_pay_payment = $result->fetch_assoc();
      $acc_pay_id=intval($row_acc_pay_payment['acc_pay_id']);
      $acc_pay_method_id=intval($row_acc_pay_payment['acc_pay_method_id']);
      if ($acc_pay_id>0 and $acc_pay_method_id>0) {
        $return['data']['aaaaaaaa']='4 '.$sql;
        $sql="SELECT Sum(poso) AS mysum
        FROM gks_acc_pay_payment 
        LEFT JOIN gks_eftpos_transaction ON gks_acc_pay_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
        WHERE gks_eftpos_transaction.transaction_status='done'
        AND gks_acc_pay_payment.acc_pay_method_id=".$acc_pay_method_id."
        AND gks_acc_pay_payment.acc_pay_id=".$acc_pay_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        if ($result->num_rows==1) {
          $return['data']['aaaaaaaa']='5 '.$sql;
          $row_sum_poso = $result->fetch_assoc();
          $return['data']['new_sum_poso']=round(floatval($row_sum_poso['mysum']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $return['data']['new_sum_poso_html']=myCurrencyFormat($return['data']['new_sum_poso']);
          $return['data']['new_sum_row_id']=$acc_pay_method_id;
          
          $sql="update gks_acc_pay_method set 
          paymethod_total=".$return['data']['new_sum_poso']."
          where id_acc_pay_method=".$acc_pay_method_id;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          $sql="select sum(paymethod_total) as mysum from gks_acc_pay_method where acc_pay_id=".$acc_pay_id;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          $row_sum = $result->fetch_assoc();
          $gks_price_total=round(floatval($row_sum['mysum']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          
          $sql="select * from gks_acc_pay where id_acc_pay=".$acc_pay_id;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          $row_pay=$result->fetch_assoc();
          $user_id=intval($row_pay['user_id']);
          $affect_balance=intval($row_pay['affect_balance']);
          $affect_balance_all_poso=intval($row_pay['affect_balance_all_poso']);
          $affect_balance_all_poso_type=trim_gks($row_pay['affect_balance_all_poso_type']);
          $affect_balance_pros=intval($row_pay['affect_balance_pros']);
          
          if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
            $affect_balance_pros=0;
          } 
          
          if ($affect_balance == 0) {
            $affect_balance_poso=0;
          } else {
            if ($affect_balance_all_poso==1) {
              switch ($affect_balance_all_poso_type) {
                case 'price_total':
                  $affect_balance_poso=$gks_price_total;
                  break;  
           
                default:     
                
              }
            } else {
              //$affect_balance_poso=$affect_balance_poso;
            }
          }
                   
          
          $sql="update gks_acc_pay set 
          gks_price_total=".$gks_price_total.",
          affect_balance_poso=".$affect_balance_poso.",
          user_id_edit=".$my_wp_user_id.",
          mydate_edit=now(),
          myip='".$db_link->escape_string($gkIP)."'
          where id_acc_pay=".$acc_pay_id;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          
          $myarray_new=array();
          $myarray_line_new=array();
          $idiotites_new=get_acc_pay_details_txt($acc_pay_id, $myarray_new, $myarray_line_new); 
          
          $sql="update gks_acc_pay set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_acc_pay = ".$acc_pay_id." limit 1";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); } 
  
          $balance_user=gks_balance_calc(['id' => $user_id]);

                    
          
        }
        
        
        
  
        
        
      }
      
    }
    
    
  }

  
  
  
  $ret=gks_eftpos_get_transaction_html(['sessionId' => $sessionId]);
  
  $return['data']['transaction']=$ret['transaction'];
  
}


echo json_encode($return); die();



//if ($transaction_status=='draft') {
//  $return = array('success' => true, 'message' => base64_encode('OK'));
//  echo json_encode($return); die();}


$return['success']=true;
$return['message']='OK';
$return['data']=array(
  'sessionId' => $sessionId,
  'id_eftpos_transaction' => $id_eftpos_transaction,
  'payment_acquirer_with_id' => $payment_acquirer_with_id,
  'access_token' => $access_token,
);
echo json_encode($return); die();









