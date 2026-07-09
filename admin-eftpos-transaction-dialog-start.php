<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$gks_session_pos='';if (isset($_POST['gks_session_pos'])) $gks_session_pos=trim_gks(base64_decode($_POST['gks_session_pos']));
$gks_erp_cookie_id='';if (isset($_POST['gks_erp_cookie_id'])) $gks_erp_cookie_id=trim_gks(base64_decode($_POST['gks_erp_cookie_id']));

$page='';if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page']));
$transaction_type=''; if (isset($_POST['transaction_type'])) $transaction_type=trim_gks(base64_decode($_POST['transaction_type']));
$doc_id=0;if (isset($_POST['doc_id'])) $doc_id=intval($_POST['doc_id']);
$prev_eftpos_id=0;if (isset($_POST['prev_eftpos_id'])) $prev_eftpos_id=intval($_POST['prev_eftpos_id']);
$pp_type=''; if (isset($_POST['pp_type'])) $pp_type=trim_gks(base64_decode($_POST['pp_type']));
$asset_id=0; if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
$asset_title=''; if (isset($_POST['asset_title'])) $asset_title=trim_gks(base64_decode($_POST['asset_title']));
$pp=0; if (isset($_POST['pp'])) $pp=intval($_POST['pp']);
$pp_price=0; if (isset($_POST['pp_price'])) $pp_price=floatval($_POST['pp_price']);
$tipAmount=0; if (isset($_POST['tipAmount'])) $tipAmount=floatval($_POST['tipAmount']);
$installments=0; if (isset($_POST['installments'])) $installments=intval($_POST['installments']);
$refund_val=0; if (isset($_POST['refund_val'])) $refund_val=floatval($_POST['refund_val']);
$preferred_payment_method='tap';if (isset($_POST['preferred_payment_method'])) $preferred_payment_method=trim_gks($_POST['preferred_payment_method']);

$id_acc_xxx_payment=0; 
if (isset($_POST['id_acc_xxx_payment'])) $id_acc_xxx_payment=intval($_POST['id_acc_xxx_payment']);

//echo '<pre>sssssssss'."\n".$doc_id;die();
//echo '<pre>sssssssss'."\n".$gks_session_pos."\n".$gks_erp_cookie_id;die();

$doc_table='';$prev_doc_table='';
if ($page=='/my/admin-pos-run.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-eftpos-transaction.php') $doc_table='gks_eftpos_transaction';
if ($page=='/my/admin-acc-inv-item.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-acc-pay-item.php') $doc_table='gks_acc_pay';
//echo '<pre>'.$doc_table;die();

$t_gks_acc_xxx_payment='';
$f_acc_xxx_id='';
$f_acc_xxx_payment_id='';
$f_id_acc_xxx_payment='';
if ($doc_table=='gks_acc_inv') {
  $t_gks_acc_xxx_payment='gks_acc_inv_payment';
  $f_acc_xxx_id='acc_inv_id';
  $f_acc_xxx_payment_id='acc_inv_payment_id';
  $f_id_acc_xxx_payment='id_acc_inv_payment';
} else if ($doc_table=='gks_acc_pay') {
  $t_gks_acc_xxx_payment='gks_acc_pay_payment';
  $f_acc_xxx_id='acc_pay_id';
  $f_acc_xxx_payment_id='acc_pay_payment_id';
  $f_id_acc_xxx_payment='id_acc_pay_payment';
} else if ($doc_table=='gks_eftpos_transaction') {
  $sql="select * from gks_eftpos_transaction where id_eftpos_transaction=".$prev_eftpos_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}    

  
  
  $row_vvv_eftpos = $result->fetch_assoc();
  $sql_doc='';
  if ($row_vvv_eftpos['acc_inv_payment_id']>0) {
    $sql_doc="select id_acc_inv_payment as id_acc_xxx_payment, acc_inv_id as doc_id from gks_acc_inv_payment where id_acc_inv_payment=".$row_vvv_eftpos['acc_inv_payment_id'];
    $doc_table='gks_acc_inv';
    $t_gks_acc_xxx_payment='gks_acc_inv_payment';
    $f_acc_xxx_id='acc_inv_id';
    $f_acc_xxx_payment_id='acc_inv_payment_id';
    $f_id_acc_xxx_payment='id_acc_inv_payment';
  } else if ($row_vvv_eftpos['acc_pay_payment_id']>0) {
    $sql_doc="select id_acc_pay_payment as id_acc_xxx_payment,acc_pay_id as doc_id from gks_acc_pay_payment where id_acc_pay_payment=".$row_vvv_eftpos['acc_pay_payment_id'];
    $doc_table='gks_acc_pay';
    $t_gks_acc_xxx_payment='gks_acc_pay_payment';
    $f_acc_xxx_id='acc_pay_id';
    $f_acc_xxx_payment_id='acc_pay_payment_id';
    $f_id_acc_xxx_payment='id_acc_pay_payment';
  }
  //echo '<pre>qqqq1 '.$doc_id.' '.$sql_doc;die();
  if ($sql_doc!='') {
    $result = $db_link->query($sql_doc);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows==1) {
      $row_dddd= $result->fetch_assoc();
      //$doc_id=$row_dddd['doc_id'];
      $id_acc_xxx_payment=$row_dddd['id_acc_xxx_payment'];
    }
    //echo '<pre>qqqq2 '.$doc_id.' '.$sql_doc;die();
    
  }
  //echo '<pre>'.$sql;die();
}

//echo '<pre>'.$doc_id.'|'.$id_acc_xxx_payment;die();

if (in_array($transaction_type,['sale','fullvoid','fullvoiderp','refund','refunderp'])==false) {
  debug_mail(false,'the transaction_type is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' transaction_type.<br>'.gks_lang('Η τιμή που στάλθηκε είναι η').': <b>'.$transaction_type.'</b>'));
  echo json_encode($return); die();}

if (in_array($transaction_type,['sale'])) {
  if ($doc_id<=0) {
    debug_mail(false,'the id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();}
}

$worldline_implementation=''; 
if (isset($_POST['worldline_implementation'])) $worldline_implementation=trim_gks($_POST['worldline_implementation']); 




$my_page_title=gks_lang('Εκτέλεση συναλλαγής EFT-POS').': '.$transaction_type.' '.$doc_id.' '.$prev_eftpos_id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $doc_table,($doc_id==-1 ? 'add':'edit'),$doc_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');



if (in_array($transaction_type,['sale'])) {
  if ($pp_type!='one' && $pp_type!='multi') {
    debug_mail(false,'data error',                                 gks_lang('Λάθος κατάσταση τρόπων πληρωμής'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση τρόπων πληρωμής')));
    echo json_encode($return); die();}
  if ($asset_id<=0) {
    debug_mail(false,'data error',                                 gks_lang('Δεν ορίστηκε το τερματικό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ορίστηκε το τερματικό')));
    echo json_encode($return); die();}
  if ($pp_price<=0) {
    debug_mail(false,'data error',                                 gks_lang('Δεν ορίστηκε το ποσό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ορίστηκε το ποσό')));
    echo json_encode($return); die();}
  if ($pp_type=='multi' and $pp<=0) {
    debug_mail(false,'data error',                                 gks_lang('Δεν ορίστηκε η ΑΑ της εγγραφής πληρωμής'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ορίστηκε η ΑΑ της εγγραφής πληρωμής')));
    echo json_encode($return); die();}
  if ($pp_type=='multi' and $id_acc_xxx_payment<=0) {
    debug_mail(false,'data error',                                 gks_lang('Δεν ορίστηκε η εγγραφή πληρωμής'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ορίστηκε η εγγραφή πληρωμής')));
    echo json_encode($return); die();}

  //echo '<pre>aaaaaaaaaaaa '.$pp_type;die();
  if ($pp_type=='one') {
    //to   id_acc_xxx_payment einai -1
    $sql="select ".$f_id_acc_xxx_payment.",payment_acquirer_id,transaction_id from ".$t_gks_acc_xxx_payment." where ".$f_acc_xxx_id."=".$doc_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows!=1) {
      debug_mail(false,'asset error',                                gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής για αυτό το παραστατικό'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής για αυτό το παραστατικό')));
      echo json_encode($return); die();}
    $row_temp = $result->fetch_assoc();
    $id_acc_xxx_payment=intval($row_temp[$f_id_acc_xxx_payment]);
    $payment_acquirer_id=intval($row_temp['payment_acquirer_id']);
      
    if ($page=='/my/admin-pos-run.php') {
      if (intval($row_temp['transaction_id'])==0 and
          isset($_POST['id_payment_acquirer']) and 
          intval($_POST['id_payment_acquirer'])>0) {
        
        
        
        $sql="update ".$t_gks_acc_xxx_payment." set 
        payment_acquirer_id=".intval($_POST['id_payment_acquirer']).",
        asset_id=".intval($_POST['asset_id'])."
        where ".$f_id_acc_xxx_payment."=".$id_acc_xxx_payment."
        limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        
        $sql="update gks_acc_inv set 
        tropos_pliromis=".intval($_POST['id_payment_acquirer'])."
        where id_acc_inv=".$doc_id."
        and pos_step='20ekdosi_end'";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
          
        $sql="select gks_price_total from gks_acc_inv where id_acc_inv=".$doc_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        $row_temp = $result->fetch_assoc();
        
        $pp_price=floatval($row_temp['gks_price_total']);
        
      }
      
    }
    //die('<pre>ssssssssssssssss '.$id_acc_xxx_payment);  
    //echo '<pre>'; die('<pre>ssssssssssssssss '.$id_acc_xxx_payment);  
    
  }
}


if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
  
  if ($prev_eftpos_id<=0) {
    if ($doc_table=='gks_acc_pay') {
      $sql="select credit_memo_for_acc_pay_id from gks_acc_pay where id_acc_pay=".$doc_id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      if ($result->num_rows==1) {

        $row_temp = $result->fetch_assoc();
        $credit_memo_for_acc_pay_id=intval($row_temp['credit_memo_for_acc_pay_id']);
        if ($credit_memo_for_acc_pay_id>0) {
          $sql="select transaction_id 
          from gks_acc_pay_payment
          where acc_pay_id=".$credit_memo_for_acc_pay_id."
          and transaction_id>0 and poso>0";
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          if ($result->num_rows>=1) {
            $row_temp = $result->fetch_assoc();
            $prev_eftpos_id=$row_temp['transaction_id'];
          }
          
          
        }
        
        //echo '<pre>ssssssss '.$credit_memo_for_acc_pay_id.'|'.$prev_eftpos_id;die();
      }
      
    }
  }
  
  
  if ($prev_eftpos_id<=0) {
    debug_mail(false,'the prev_eftpos_id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' prev_eftpos_id.'));
    echo json_encode($return); die();}
  
}


if (in_array($transaction_type,['refund','refunderp'])) {
  if ($refund_val<=0) {
    debug_mail(false,'data error',                                 gks_lang('Δεν ορίστηκε το ποσό επιστροφής'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ορίστηκε το ποσό επιστροφής')));
    echo json_encode($return); die();}
  
  $pp_price=$refund_val;
}

  

$sql="select * from gks_assets where id_asset=".$asset_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'asset error',                                gks_lang('Δεν βρέθηκε το τερματικό με κωδικό παγίου').' '.$asset_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το τερματικό με κωδικό παγίου').' '.$asset_id));
  echo json_encode($return); die();}
$row_asset = $result->fetch_assoc();

//echo '<pre>dddddddddddddd'.$id_acc_xxx_payment;die();


if (in_array($transaction_type,['sale'])) {
  
  $sql="select * from ".$t_gks_acc_xxx_payment." where ".$f_acc_xxx_id."=".$doc_id." and ".$f_id_acc_xxx_payment."=".$id_acc_xxx_payment;
  
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'asset error',                                gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής με').' id '.$id_acc_xxx_payment);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής με').' id '.$id_acc_xxx_payment));
    echo json_encode($return); die();}
  $row_payment = $result->fetch_assoc();
  if ($row_payment['transaction_id']>0) {
    debug_mail(false,'transaction_id error',                       str_replace('[1]',$row_payment['transaction_id'],gks_lang('Αυτή η εγγραφή πληρωμής με id [1] έχει γίνει ήδη ή βρίσκεται σε εξέλιξη')));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$row_payment['transaction_id'],gks_lang('Αυτή η εγγραφή πληρωμής με id [1] έχει γίνει ήδη ή βρίσκεται σε εξέλιξη'))));
    echo json_encode($return); die();}
  
}

//echo '<pre>dddddddddddddd'.$sql;die();
  
if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) { 
  $sql="select * from gks_eftpos_transaction where id_eftpos_transaction=".$prev_eftpos_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}    
  
  $row_prev_eftpos = $result->fetch_assoc();
  if ($row_prev_eftpos['transaction_status']!=='done') {
    debug_mail(false,'record not found',                           str_replace('[1]',$prev_eftpos_id,gks_lang('Η συναλλαγή με ID [1] δεν έχει ολοκληρωθεί')));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$prev_eftpos_id,gks_lang('Η συναλλαγή με ID [1] δεν έχει ολοκληρωθεί'))));
    echo json_encode($return); die();}   

  $row_payment=array();
  $row_payment['payment_acquirer_id']=$row_prev_eftpos['payment_acquirer_id'];
  //todo    
  //ean exei ginei idi refund i exoyn epistrafei me allon tropo ola ta xrimata
  
  $pp_price=floatval($row_prev_eftpos['amount']);
  if (in_array($transaction_type,['refund','refunderp'])) {
    if ($refund_val>$pp_price) {
      $tmpmsg=gks_lang('Το ποσό επιστροφής [1] δεν μπορεί να είναι μεγαλύτερο από αρχικό ποσό της συναλλαγής [2]');
      $tmpmsg=str_replace('[1]',myCurrencyFormat($refund_val),$tmpmsg);
      $tmpmsg=str_replace('[2]',myCurrencyFormat($pp_price),$tmpmsg);      
      
      debug_mail(false,                                              $tmpmsg,$sql);
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      echo json_encode($return); die();}
    
    $pp_price=$refund_val;
  }
  
  $asset_id=floatval($row_prev_eftpos['asset_id']);
  $prev_eftpos_doc_id=0;

  $prev_eftpos_acc_xxx_payment_id=intval($row_prev_eftpos[$f_acc_xxx_payment_id]);
  $sql="SELECT ".$f_acc_xxx_id." FROM ".$t_gks_acc_xxx_payment." WHERE transaction_id=".$prev_eftpos_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==1) {
    $row_temp = $result->fetch_assoc(); 
    $prev_eftpos_doc_id=intval($row_temp[$f_acc_xxx_id]);
    $prev_eftpos_table=$doc_table;
    $prev_doc_table=$doc_table;

    
  }
  
  //todo gia gks_acc_pay
  
  
  if ($prev_eftpos_doc_id==0) {
    debug_mail(false,                                              gks_lang('Δεν βρέθηκε το σχετικό παραστατικό της συναλλαγής'),$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό παραστατικό της συναλλαγής')));
    echo json_encode($return); die();}
    
  $doc_id=$prev_eftpos_doc_id; //auto einai pleon to doc
  
//  if (in_array($transaction_type,['refund','refunderp'])) {
//    if ($refund_val>$pp_price) {
//      $tmpmsg=gks_lang('Το ποσό επιστροφής [1] δεν μπορεί να είναι μεγαλύτερο από αρχικό ποσό της συναλλαγής [2]');
//      $tmpmsg=str_replace('[1]',myCurrencyFormat($refund_val),$tmpmsg);
//      $tmpmsg=str_replace('[2]',myCurrencyFormat($pp_price),$tmpmsg);
//      debug_mail(false,                                              $tmpmsg,$sql);
//      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
//      echo json_encode($return); die();}
//  }
    
  
//  echo '<pre>ddddddddd|'.
//  $pp_price.'|'.
//  $asset_id.'|'.
//  $prev_eftpos_acc_xxx_payment_id .'|'.
//  $prev_eftpos_table.'|'.
//  $prev_eftpos_doc_id
//  ;
//  die();
}

//echo '<pre>ddddddddd';die();
 
//echo '<pre>ppppppaaaaaggggeee '.$doc_table.'|'.$prev_doc_table.'|'.$doc_id.'|'.$page;die();

//echo '<pre>dsssssssss|'.$page.'|'.$doc_id;die();

if ($doc_table=='gks_acc_inv' or $prev_doc_table=='gks_acc_inv') {
  
  
  $sql=select_gks_acc_inv()." where id_acc_inv=".$doc_id; 
  
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  $sql.=" limit 1";
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_inv = $result->fetch_assoc();
  
  if ($row_inv['inv_state']!='070ypoekdosi' and $row_inv['inv_state']!='090ekdosi') {
    $tmpmsg=gks_lang('Το παραστατικό θα πρέπει να είναι σε κατάσταση<br><span class="acc_inv_state_070ypoekdosi">[1]</span> ή <span class="acc_inv_state_090ekdosi">[2]</span>');
    $tmpmsg=str_replace('[1]',getAccInvStateDescr('070ypoekdosi'),$tmpmsg);
    $tmpmsg=str_replace('[2]',getAccInvStateDescr('090ekdosi'),$tmpmsg);    
    debug_mail(false,'acc inv not 070ypoekdosi or 090ekdosi',$tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}
  
  $gks_price_total=floatval($row_inv['gks_price_total']);
  if ($pp_price > $gks_price_total) {
    $tmpmsg=gks_lang('Το ποσό πληρωμής [1] δεν μπορεί να είναι μεγαλύτερο από σύνολο του παραστατικού [2]');
    $tmpmsg=str_replace('[1]',myCurrencyFormat($pp_price),$tmpmsg);
    $tmpmsg=str_replace('[2]',myCurrencyFormat($gks_price_total),$tmpmsg);    
    debug_mail(false,'acc inv not 090ekdosi',                      $tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}

} else if ($doc_table=='gks_acc_pay' or $prev_doc_table=='gks_acc_pay') {
  
  
  $sql=select_gks_acc_pay()." where id_acc_pay=".$doc_id; 
  
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_pay.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_pay.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_pay.pay_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_pay.pay_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  $sql.=" limit 1";
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_inv = $result->fetch_assoc();
  
  if ($row_inv['pay_state']!='090ekdosi') {
    $tmpmsg=gks_lang('Το παραστατικό θα πρέπει να είναι σε κατάσταση <span class="acc_pay_state_090ekdosi">[1]</span>');
    $tmpmsg=str_replace('[1]',getAccPayStateDescr('090ekdosi'),$tmpmsg);
    debug_mail(false,'acc pay not 090ekdosi',$tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}
  
  $gks_price_total=floatval($row_inv['gks_price_total']);
  if ($pp_price > $gks_price_total) {
    $tmpmsg=gks_lang('Το ποσό πληρωμής [1] δεν μπορεί να είναι μεγαλύτερο από σύνολο του παραστατικού [2]');
    $tmpmsg=str_replace('[1]',myCurrencyFormat($pp_price),$tmpmsg);
    $tmpmsg=str_replace('[2]',myCurrencyFormat($gks_price_total),$tmpmsg);    
    debug_mail(false,'acc pay not 090ekdosi',                      $tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}


} else {
  
  echo '<pre>check page and doc_table ...';die();
  
    
}


if ($doc_table=='gks_acc_inv' and in_array($transaction_type,['sale'])) {
  $sql="select sum(poso) as mysum from gks_acc_inv_payment where acc_inv_id=".$doc_id." and transaction_id>0";  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $row_temp = $result->fetch_assoc();
  //echo '<pre>'.$has_done_poso; die();
  
  $has_done_poso=floatval($row_temp['mysum']);
  if (($has_done_poso + $pp_price) > $gks_price_total) {
    $diafora=$gks_price_total-$has_done_poso;
    $tmpmsg=gks_lang('Το ποσό πληρωμής [1] δεν μπορεί να είναι μεγαλύτερο από [2] διότι έχουν γίνει ήδη κάποιες συναλλαγές με σύνολο [3]');
    $tmpmsg=str_replace('[1]',myCurrencyFormat($pp_price),$tmpmsg);
    $tmpmsg=str_replace('[2]',myCurrencyFormat($diafora),$tmpmsg);    
    $tmpmsg=str_replace('[3]',myCurrencyFormat($has_done_poso),$tmpmsg);    
    debug_mail(false,'acc inv not 090ekdosi',                      $tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}  
}

$sql="SELECT gks_payment_acquirers.*, gks_payment_acquirer_with.payment_paroxos_name
FROM gks_payment_acquirers 
LEFT JOIN gks_payment_acquirer_with ON gks_payment_acquirers.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with
WHERE gks_payment_acquirers.id_payment_acquirer=".$row_payment['payment_acquirer_id']." 
AND gks_payment_acquirers.id_payment_acquirer>0
AND gks_payment_acquirers.payment_acquirer_disabled=0;";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found',                           gks_lang('Δεν βρέθηκε o τρόπος πληρωμής με').' ID '.$row_payment['payment_acquirer_id']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε o τρόπος πληρωμής με').' ID '.$row_payment['payment_acquirer_id']));
  echo json_encode($return); die();}
$row_pacq = $result->fetch_assoc();
$id_payment_acquirer=intval($row_pacq['id_payment_acquirer']);
$aade_tropos_pliromis_id=intval($row_pacq['aade_tropos_pliromis_id']);
$payment_acquirer_with_id=intval($row_pacq['payment_acquirer_with_id']);


//echo '<pre>11111 '.$payment_acquirer_with_id;die();

if (gks_eftpos_is_terminal_valid_for_this_payment_acquirer_with($payment_acquirer_with_id,$row_asset)==false) {
  $tmpmsg=gks_lang('Το πάγιο <b>[1]</b> δεν έχει κωδικό τερματικού για τον πάροχο πληρωμής <b>[2]</b>');
  $tmpmsg=str_replace('[1]',$row_asset['asset_title'],$tmpmsg);
  $tmpmsg=str_replace('[2]',$row_pacq['payment_paroxos_name'],$tmpmsg);  
  debug_mail(false,'terminal_id error',                          $tmpmsg);
  $return = array('success' => false, 'message' => base64_encode($tmpmsg));
  echo json_encode($return); die();}
  
$my_terminal_id=gks_eftpos_get_terminal_id($payment_acquirer_with_id,$row_asset);


//echo '<pre>11111 '.$my_terminal_id;die();

$sql="select * from gks_company where id_company=".$row_inv['company_id'];
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found',                           gks_lang('Δεν βρέθηκε η εταιρεία με').' ID '.$row_inv['company_id']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εταιρεία με').' ID '.$row_inv['company_id']));
  echo json_encode($return); die();}
$row_company = $result->fetch_assoc();


$sql="select * from gks_acc_seires_paymentacquirers 
where acc_seira_id=";
if ($doc_table=='gks_acc_inv') {
  $sql.=$row_inv['inv_acc_seira_id'];
} else if ($doc_table=='gks_acc_pay') {
  $sql.=$row_inv['pay_acc_seira_id'];
}
$sql.="
and payment_acquirer_id=".$id_payment_acquirer;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$seira_need_signature=false;
if ($result->num_rows==1) {
  $seira_need_signature=true;
}
//echo '<pre>sssssssss '.$seira_need_signature;die();

$signature_data=array(
  'id_paroxos_signature'=>0,
  'id_aade_paroxos' => 0,
  'signature'=>array(),
  'seira_need_signature' => $seira_need_signature
);


//echo '<pre>ssssssss ';print_r($signature_data);die();



$struct_data=array();
$struct_data['row']=$row_inv;
$struct_data['doc_table']=$doc_table;
if ($doc_table=='gks_acc_inv') {
  $gks_price_net=$struct_data['row']['gks_price_net'];
  $gks_price_fpa=$struct_data['row']['gks_price_fpa'];
  $gks_price_total=$struct_data['row']['gks_price_total'];
} else if ($doc_table=='gks_acc_pay') {
  $gks_price_net=$struct_data['row']['gks_price_total'];
  $gks_price_fpa=0;
  $gks_price_total=$struct_data['row']['gks_price_total'];
}

$struct_data['xml']=array();
$struct_data['sign']['amount']=$pp_price;
$struct_data['sign']['tipAmount']=$tipAmount;
$struct_data['sign']['installments']=$installments;
$struct_data['sign']['refund_val']=$refund_val;
$struct_data['sign']['netAmount']=$gks_price_net;
$struct_data['sign']['vatAmount']=$gks_price_fpa;
$struct_data['sign']['grossAmount']=$gks_price_total;
$struct_data['sign']['terminalId']=$my_terminal_id; 
$struct_data['sign']['acc_xxx_payment_id']=$id_acc_xxx_payment;
$struct_data['sign']['payment_acquirer_with_id']=$payment_acquirer_with_id;
$struct_data['sign']['payment_acquirer_id']=$row_payment['payment_acquirer_id'];
$struct_data['sign']['asset_id']=$asset_id;
$struct_data['sign']['megeftpos_protocol']=$row_asset['megeftpos_protocol'];


if ($signature_data['seira_need_signature']) {
  //die('<pre>ddddsss'.$my_terminal_id.'sss');
  $force_options=[];
  $ret_params=gks_paroxos_load_params($row_inv['company_id'],$row_inv['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode($ret_params['message']));
    echo json_encode($return); die();}

  $paroxos_params=$ret_params['paroxos_params'];
  
  //echo '<pre>sssssssss ';print_r($paroxos_params);die();

  
  //echo '<pre>get sing from... ';print_r($struct_data['sign']);die();
  
 
  $ret=gks_paroxos_payment_sign($doc_id,$paroxos_params,$struct_data);
  
  //echo '<pre>get sing from... ';print_r($ret);die();
  if ($ret['success']==false) {
    $return = array('success' => false, 'message' => base64_encode($ret['message']));
    echo json_encode($return); die();}

  
  $signature_data['id_paroxos_signature']=$ret['id_paroxos_signature'];
  $signature_data['id_aade_paroxos']=$ret['id_aade_paroxos'];
  $signature_data['signature']=$ret['response']['invoiceSignatures'][0];

  //echo '<pre>signature_data... ';print_r($signature_data);die();
  
}

//echo '<pre>signature_data... ';print_r($signature_data);die();

//echo '<pre>row_company... ';print_r($row_company);die();

$ret=gks_eftpos_has_credentials($payment_acquirer_with_id,$row_company);
//echo '<pre>credentials ggggggggg ... ';print_r($ret);die();
if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}


//echo '<pre>credentials ggggggggg ... ';print_r($ret);die();

if ($worldline_implementation=='app2app') {
  $access_token='';
} else {
  $ret=gks_eftpos_get_token($payment_acquirer_with_id,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
}

//echo '<pre>access_token ggggggggg ... '."\n".$access_token."\n";print_r($ret);die();
 
$my_uniqueTxnId=guid_for_eftpos_my_uniqueTxnId();
$sessionId=guid_for_eftpos_sessionId();
$merchantReference='gks';
$aade_paroxos_id=$signature_data['id_aade_paroxos'];
$transaction_status='draft';
$cashRegisterId='cashreg'.$my_wp_user_id;
$customerTrns=$row_company['company_title'];



$aadeProviderId='';
$aadeProviderSignatureData='';
$aadeProviderSignature='';
$aadeSignatureTimestamp='';
$aadeSignatureUID='';

if ($signature_data['seira_need_signature']) {
  
  $ret=gks_paroxos_get_signature_data($signature_data);
  //echo '<pre>get signature 1111111111 data ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  
  $aadeProviderId=$ret['data']['aadeProviderId'];
  $aadeProviderSignatureData=$ret['data']['aadeProviderSignatureData'];
  $aadeProviderSignature=$ret['data']['aadeProviderSignature'];
  $aadeSignatureTimestamp=$ret['data']['aadeSignatureTimestamp'];
  $aadeSignatureUID=$ret['data']['aadeSignatureUID'];

}

if ($signature_data['seira_need_signature']) {
  if ($transaction_type=='sale') $transaction_type='saleerp';
  
  
}


//echo '<pre>signature_data... ';print_r($signature_data);die();

//echo '<pre>get signature 222222222 data ... ';print_r($ret);die();

$sql="insert into gks_eftpos_transaction (
user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
transaction_type,
preferred_payment_method,
".$f_acc_xxx_payment_id.",
payment_acquirer_with_id,
payment_acquirer_id,
aade_paroxos_id,
asset_id,
terminalId,
transaction_status,
my_uniqueTxnId,
amount,
sessionId,
cashRegisterId,
merchantReference,
customerTrns,
tipAmount,
installments,
refund_val,
aadeProviderId,
aadeProviderSignatureData,
aadeProviderSignature,
company_id,
xeiristis_id,
paroxos_signature_id
) values (
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($transaction_type)."',
'".$db_link->escape_string($preferred_payment_method)."',
".$id_acc_xxx_payment.",
".$payment_acquirer_with_id.",
".$row_payment['payment_acquirer_id'].",
".$aade_paroxos_id.",
".$asset_id.",
'".$db_link->escape_string($my_terminal_id)."',
'".$db_link->escape_string($transaction_status)."',
'".$db_link->escape_string($my_uniqueTxnId)."',
".number_format($pp_price,8, '.','').",
'".$db_link->escape_string($sessionId)."',
'".$db_link->escape_string($cashRegisterId)."',
'".$db_link->escape_string($merchantReference)."',
'".$db_link->escape_string($customerTrns)."',
".number_format($tipAmount,8, '.','').",
".$installments.",
".number_format($refund_val,8, '.','').",
'".$db_link->escape_string($aadeProviderId)."',
'".$db_link->escape_string($aadeProviderSignatureData)."',
'".$db_link->escape_string($aadeProviderSignature)."',
".$row_company['id_company'].",
".$my_wp_user_id.",
".$signature_data['id_paroxos_signature']."
)";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

$id_eftpos_transaction = $db_link->insert_id;  

if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
  $sql="insert into gks_eftpos_transaction_thisisfor (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  my_this,my_is,my_for
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id_eftpos_transaction.",
  '".$db_link->escape_string($transaction_type)."',
  ".$prev_eftpos_id."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  
}




$merchantReference='gks|'.$my_wp_user_id.'|'.$doc_id.'|'.$id_acc_xxx_payment.'|'.$id_eftpos_transaction.'|'.$row_payment['payment_acquirer_id'].'|'.$sessionId.'|gks';

$sql="update gks_eftpos_transaction set 
merchantReference='".$db_link->escape_string($merchantReference)."'
where id_eftpos_transaction=".$id_eftpos_transaction;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  


if ($doc_table=='gks_acc_inv') {
  $gks_price_net=$struct_data['row']['gks_price_net'];
  $gks_price_fpa=$struct_data['row']['gks_price_fpa'];
  $gks_price_total=$struct_data['row']['gks_price_total'];
} else if ($doc_table=='gks_acc_pay') {
  $gks_price_net=$struct_data['row']['gks_price_total'];
  $gks_price_fpa=0;
  $gks_price_total=$struct_data['row']['gks_price_total'];
}
  
$data=array(
  'id_eftpos_transaction' => $id_eftpos_transaction,
  'payment_acquirer_with_id' => $payment_acquirer_with_id,
  'access_token' => $access_token,
  'id_acc_inv' => $doc_id,
  'transaction_type' => $transaction_type,
  'preferred_payment_method' => $preferred_payment_method,
  'aade_paroxos_id' => $aade_paroxos_id,
  'terminalId' => $my_terminal_id,
  'sessionId' => $sessionId,
  'my_uniqueTxnId' => $my_uniqueTxnId,
  'cashRegisterId' => $cashRegisterId,
  'merchantReference' => $merchantReference,
  'customerTrns' => $customerTrns,
  'amount' => $pp_price,
  'tipAmount' => $tipAmount,
  'installments' => $installments,
  'refund_val' => $refund_val,
  'row_company' => $row_company,
  'row_asset'=> $row_asset,
  'row_inv' => $row_inv,
  'netAmount'=>$gks_price_net,
  'vatAmount'=>$gks_price_fpa,
  'grossAmount'=>$gks_price_total,
  'terminalId'=>$my_terminal_id, 

  'mellon_x_api_key'=> $row_company['mellon_x_api_key'],
  'epay_x_api_key'=> $row_company['epay_x_api_key'],
  'worldline_x_api_key'=> $row_company['worldline_x_api_key'],
  'nexi_x_api_key'=> $row_company['nexi_x_api_key'],
  'seira_need_signature' => $signature_data['seira_need_signature'],
  
  'worldline_implementation'=>$worldline_implementation, 

);

if ($signature_data['seira_need_signature']) {
  $data['aadeProviderId']=$aadeProviderId;
  $data['aadeProviderSignatureData']=$aadeProviderSignatureData;
  $data['aadeProviderSignature']=$aadeProviderSignature;
  $data['aadeSignatureTimestamp']=$aadeSignatureTimestamp;
  $data['aadeSignatureUID']=$aadeSignatureUID;
  
}
//echo '<pre>ddddddddd data ';print_r($data);die();


if (in_array($transaction_type,['sale','saleerp'])) {
  $ret=gks_eftpos_sales_request($data);
} else if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
  $data['row_prev_eftpos']=$row_prev_eftpos;
  $ret=gks_eftpos_fullvoid_request($data);
//  
} else {
  
  echo '<pre>errrrrooooorrrrr transaction_type: '.$transaction_type;die();  
}
//echo '<pre>1111111 ret gks_eftpos_sales_request ';print_r($ret);die();

if ($ret['success']==false) {
  
  if ($signature_data['id_paroxos_signature']>0) {
    $sql="update gks_paroxos_signature set 
    signature_status='canreuse' 
    where id_paroxos_signature=".$signature_data['id_paroxos_signature']."
    and signature_status in ('draft','canreuse')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}

  }


  $ret['message']=base64_encode('check this ' .$ret['message']);
  echo json_encode($ret); die();
  
}

$worldline_app2app_token='aaaaaa';
if ($worldline_implementation=='app2app') {
  if (isset($ret['data']['token'])) {
    $worldline_app2app_token=$ret['data']['token'];
  } 
}

//$remote_id=0; 
//if ($payment_acquirer_with_id==3 and isset($ret['data']['Id'])) { //mellon
//  $remote_id=intval($ret['data']['Id']);
//}
//
//if ($remote_id>0) {
//  $sql="update gks_eftpos_transaction set remote_id=".$remote_id." where id_eftpos_transaction=".$id_eftpos_transaction;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die();}
//}

    
//echo '<pre>ret gks_eftpos_sales_request ';print_r($ret);die();

//if ($payment_acquirer_with_id==4) { //cardlink
//  $response_array=json_decode($ret['data'],true);
//  if (isset($response_array['error']) and $response_array['error']!='' and $response_array['error']!='null') {
//    //echo '<pre>ret response_array ';print_r($response_array);die();
//    
//    $ret['success']=false;
//    $ret['message']=base64_encode($response_array['error']);
//    echo json_encode($ret); die();
//    
//  }
//  //edo einai OK
//  
//}

if ($signature_data['id_paroxos_signature']>0) {
  $sql="update gks_paroxos_signature set 
  signature_status='assign',
  count_send_to_pos=count_send_to_pos+1
  where id_paroxos_signature=".$signature_data['id_paroxos_signature']."
  and signature_status in ('draft','canreuse')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}

}

$sql="update ".$t_gks_acc_xxx_payment." set
transaction_id=".$id_eftpos_transaction.",
transaction_pa_with_id=".$payment_acquirer_with_id."
where ".$f_id_acc_xxx_payment."=".$id_acc_xxx_payment." and transaction_id=0";  
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

//echo '<pre>dddddddd1111 ';print_r($ret); die();

//echo '<pre>dddddddd222222 ';print_r($row_company); die();
  


$return['success']=true;
$return['message']=base64_encode('OK');
$return['data']=array(
  'sessionId' => $sessionId,
  'id_eftpos_transaction' => $id_eftpos_transaction,
  'id_payment_acquirer_with' => $payment_acquirer_with_id,
  'worldline_app2app_token' => $worldline_app2app_token,
);

echo json_encode($return); die();



echo '<pre>aaaaa<br>'.$doc_id.'<br>'.$pp_type.'<br>'.$asset_id.'<br>'.$asset_title.'<br>'.
$pp.'<br>'.$pp_price.'<br>'.$id_acc_xxx_payment.'<br>'.$id_payment_acquirer.'<br>'.
$aade_tropos_pliromis_id.'<br>'.$payment_acquirer_with_id.'<br>'.$id_eftpos_transaction; die();

