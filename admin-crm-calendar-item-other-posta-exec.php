<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Επεξεργασία ημερολογίου άλλου πόστου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar_other_posta','edit',0);

$other_posta_id=0; if (isset($_POST['other_posta_id'])) $other_posta_id=intval($_POST['other_posta_id']);
if ($other_posta_id>0) {
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
}





$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd!='add' && $cmd!='remove' && $cmd!='color' && $cmd!='visible') {
  debug_mail(false,'cmd is not good','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εντολή δεν είναι σωστή').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}

$myobj='';if (isset($_POST['myobj'])) $myobj=trim_gks($_POST['myobj']);
if ($myobj=='') $myobj='cal';


$other_visible=0; if (isset($_POST['visible'])) $other_visible=intval($_POST['visible']); 
if ($other_visible!=1) $other_visible=0;

if ( ($other_posta_id<=0 and ($cmd=='add' or $cmd=='remove')) or ($other_posta_id<0 and $cmd=='color')  ) {  
  debug_mail(false,'the other_posta_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το πόστο').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}


$production_posto_descr='';

if ($cmd=='remove') {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar_other_posta','delete',0);
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

  $sql="delete from gks_calendar_other_posta where this_user_id=".$my_wp_user_id." and other_posta_id=".$other_posta_id." and other_myobj='".$db_link->escape_string($myobj)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}

} else if ($cmd=='add') {
  
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar_other_posta','add',0);
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

  $sql="select * from gks_calendar_other_posta where this_user_id=".$my_wp_user_id." and other_posta_id=".$other_posta_id." and other_myobj='".$db_link->escape_string($myobj)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows>=1) {    debug_mail(false,                 gks_lang('Αυτή η επαφή υπάρχει ήδη στο ημερολόγιό σας').'<br>'.gks_lang('Ανανεώστε την σελίδα'),$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η επαφή υπάρχει ήδη στο ημερολόγιό σας').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

  $sql="select production_posto_descr from gks_production_posta where id_production_posto=".$other_posta_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==0) {    debug_mail(false,'user not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η επαφή')));
    echo json_encode($return); die();}
  
  $row=$result->fetch_assoc();
  $production_posto_descr=$row['production_posto_descr'];
  

  $sql="insert into gks_calendar_other_posta (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
	this_user_id,other_posta_id,other_myobj,other_posta_color
	) values (
	now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
	".$my_wp_user_id.",".$other_posta_id.",
	'".$db_link->escape_string($myobj)."',
	''
	)";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
} else if ($cmd=='color') {
  if ($other_posta_id>0) {
    $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar_other_posta','edit',0);
    if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
  }
  
  $color=''; if (isset($_POST['color'])) $color=trim_gks(base64_decode($_POST['color']));
  if (strlen($color)!=7) $color=''; //'#3788d8';


  $sql="update gks_calendar_other_posta set other_posta_color='".$db_link->escape_string($color)."'
  where this_user_id=".$my_wp_user_id." and other_posta_id=".$other_posta_id." and other_myobj='".$db_link->escape_string($myobj)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
      
} else if ($cmd=='visible') {
  if ($other_posta_id>0) {
    $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar_other_posta','edit',0);
    if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
  }

  //-1 oloi (kai o idios kai oi alloi)
  $sql="update gks_calendar_other_posta set other_visible=".$other_visible."
  where this_user_id=".$my_wp_user_id."
  and other_myobj='".$db_link->escape_string($myobj)."'";
  if ($other_posta_id>0) {
    $sql.=" and other_posta_id=".$other_posta_id;
  } else {
    $sql.=" and other_posta_id>0";
  }
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
}




$return = array('success' => true, 'message' => base64_encode('OK'), 'other_posta_id'=>$other_posta_id, 'production_posto_descr' => base64_encode($production_posto_descr));
echo json_encode($return); die();
