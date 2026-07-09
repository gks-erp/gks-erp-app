<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Υπολογισμός Πληρωμής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$mydata_str = trim_gks(base64_decode($_POST['mydata_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$mydata = json_decode($mydata_str, true);
if ($mydata === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mydata_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}


if (isset($mydata['gks_lock']) and $mydata['gks_lock']==true) {
  
  $sql=select_gks_acc_pay()." where gks_acc_pay.id_acc_pay = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row = $result->fetch_assoc();
  

  
  unset($mybasketarray);
  gks_mybasketarray_create($mybasketarray);
  $mybasketarray['from']='acc_pay';
  $mybasketarray['id_object'] = $id;
  $mybasketarray['company_id']= $row['company_id'];
  $mybasketarray['company_sub_id']= $row['company_sub_id'];
  
  $mybasketarray['user']['user_id']=$row['user_id'];
  $mybasketarray['user']['afm']=$row['afm'];
  $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
  $mybasketarray['parastatiko']= 1;
  
  

  $pliroteo = 0;

  $return = array('success' => true, 'message' => base64_encode('OK'),
    //'eidi_array' => $eidi_array,
    'pliroteo'    => (myCurrencyFormat($pliroteo ,true,true)),
    'pliroteo_val'    => $pliroteo,
  );
  echo json_encode($return); die();


  
}


//print '<pre>';print_r($mydata);die();

$eidi_array=$mydata['eidi_array'];


$mycmd='';if (isset($mydata['mycmd'])) $mycmd=trim_gks($mydata['mycmd']);
$myfile='';if (isset($mydata['myfile'])) $myfile=trim_gks($mydata['myfile']);

$cmd_is_for_coupon=false;
if ($mycmd=='couponadd' or $mycmd=='coupondelete') {
  $cmd_is_for_coupon=true;
  $mycoupon=$myfile;
}
//$return = array('success' => false, 'message' => base64_encode($mycmd.' '.$mycoupon));
//echo json_encode($return); die();


unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='acc_pay';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($mydata['company_id']);
$mybasketarray['company_sub_id']=intval($mydata['company_sub_id']);

$mybasketarray['user']['user_id']=$mydata['user_id'];
$mybasketarray['user']['afm']=$mydata['afm'];
$mybasketarray['user']['ma_country_id']=$mydata['ma_country_id'];
$mybasketarray['parastatiko']=1;




if ($id>0) {
  
  
  $sql=select_gks_acc_pay()." where gks_acc_pay.id_acc_pay = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row = $result->fetch_assoc();
  
  $credit_memo_for_acc_pay_id=$row['credit_memo_for_acc_pay_id'];
  if ($credit_memo_for_acc_pay_id!=0) {
    $mybasketarray['company_id']= $row['company_id'];
    $mybasketarray['company_sub_id']= $row['company_sub_id'];
    
    $mybasketarray['user']['user_id']=$row['user_id'];
    $mybasketarray['user']['afm']=$row['afm'];
    $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
    
    
    
    $sql=select_gks_acc_pay()." where gks_acc_pay.id_acc_pay = ".$credit_memo_for_acc_pay_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows!=1) {
      debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}
    $row = $result->fetch_assoc();
    //$mybasketarray['pay_acc_journal_id']=intval($row['pay_acc_journal_id']);
    //$mybasketarray['pay_acc_seira_id']=intval($row['pay_acc_seira_id']);
    //$mybasketarray['parastatiko']= 1; //$row['eidos_parastatikou_need_afm'];
    
  }
}


$gks_price_total=0;
foreach ($eidi_array as &$value) {

  $gks_price_total+=$value['paymethod_total'];

}
unset($value);

gks_CheckAFM_Live($mybasketarray);


//echo '<pre>'; print_r($mybasketarray); die();


$return = array('success' => true, 'message' => base64_encode('OK'),
  'gks_price_total'    => (myCurrencyFormat($gks_price_total ,true,true)),
  'gks_price_total_val'    => $gks_price_total,

  'check_vies' => $mybasketarray['check_vies'],
);
echo json_encode($return); die();

