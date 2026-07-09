<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$ergasia_id=0;
if (isset($_POST['ergasia_id'])) $ergasia_id=intval($_POST['ergasia_id']);
if ($ergasia_id<=0) {
  debug_mail(false,'the ergasia_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εργασία')));
  echo json_encode($return); die();}

$ergasia_mustdone_id=0;
if (isset($_POST['ergasia_mustdone_id'])) $ergasia_mustdone_id=intval($_POST['ergasia_mustdone_id']);
if ($ergasia_mustdone_id<=0) {
  debug_mail(false,'the ergasia_mustdone_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εργασία')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη εργασία σε εργασία');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies','edit',$ergasia_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$sql="SELECT id_production_ergasia FROM gks_production_ergasies where id_production_ergasia = ".$ergasia_mustdone_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'task not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εργασία').' (1)'));
  echo json_encode($return); die();}  

$sql="SELECT id_production_ergasia FROM gks_production_ergasies where id_production_ergasia = ".$ergasia_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'task not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εργασία').' (2)'));
  echo json_encode($return); die();}  




$sql="SELECT * FROM gks_production_ergasies_mustdone where ergasia_id = ".$ergasia_id." and ergasia_mustdone_id=".$ergasia_mustdone_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty','ergasia_id - ergasia_mustdone_id not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία - εργασία υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}  

$sql="SELECT * FROM gks_production_ergasies_mustdone where ergasia_mustdone_id = ".$ergasia_id." and ergasia_id=".$ergasia_mustdone_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'ergasia_id ergasia_mustdone_id exists',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Υπάρχει ήδη η αντίστροφη σχέση (κυκλική αναφορά).')));
  echo json_encode($return); die();}  





$sql="SELECT gks_production_ergasies_mustdone.ergasia_id, gks_production_ergasies_mustdone.ergasia_mustdone_id
FROM (gks_production_ergasies_mustdone 
LEFT JOIN gks_production_ergasies AS gks_production_ergasies_n ON gks_production_ergasies_mustdone.ergasia_id = gks_production_ergasies_n.id_production_ergasia) 
LEFT JOIN gks_production_ergasies AS gks_production_ergasies_m ON gks_production_ergasies_mustdone.ergasia_mustdone_id = gks_production_ergasies_m.id_production_ergasia
WHERE (((gks_production_ergasies_n.id_production_ergasia) Is Not Null) AND ((gks_production_ergasies_m.id_production_ergasia) Is Not Null))
order by id_production_ergasia_mustdone";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$mustodone=array();
while ($row = $result->fetch_assoc()) {
  $mustodone[]=array('n'=> $row['ergasia_id'], 'm' => $row['ergasia_mustdone_id']);
}
//prosthiki kai autou
$mustodone[]=array('n'=> $ergasia_id, 'm' => $ergasia_mustdone_id);


function mustodone_check($id) {
  global $mustodone;
  global $check_val;
  global $check_is_ok;
  
  //echo 'mustodone_check: '.$id."\r\n";
  foreach ($mustodone as $key => $value) {
    if ($value['not_check']==false) {
      //$mustodone[$key]['not_check']=true;
      //print_r($mustodone);
      //die();
      //echo 'if1: '.$value['n'].'|'.$value['m'].'|'.$id."\r\n";
      if ($value['n'] == $id) {
        //echo 'if2: '.$value['n'].'|'.$value['m'].'|'.$check_val."\r\n";
        if ($value['m'] == $check_val) {
          //echo 'bingo '.$value['n'].'|'.$value['m']."\r\n";
          $check_is_ok=false;
          return;
        } else {
          mustodone_check($value['m']);
          if ($check_is_ok==false) return;
        }
      }
      
    }
  } 
}

//print '<pre>';

$check_is_ok=true;
foreach ($mustodone as $keyr => $valuer) {
  $check_val=$valuer['n'];
  $check_is_ok=true;
  //echo $valuer['n'].' | '. $valuer['m']."\r\n";
  foreach ($mustodone as $key => $value) {
    $mustodone[$key]['not_check']=false;
  }
  $mustodone[$keyr]['not_check']=true;
  
  //print_r($mustodone);
  mustodone_check($valuer['m']+0);
  
  
  //echo 'result '.$check_is_ok."\r\n\r\n\r\n";
  if ($check_is_ok==false) break;
  
} 


if ($check_is_ok==false) {
  debug_mail(false,'kikliki anafora',print_r($mustodone,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να αποθηκευτεί η εγγραφή διότι θα δημιουργηθεί κυκλική αναφορά')));
  echo json_encode($return); die();}
  













$sql="insert into gks_production_ergasies_mustdone (ergasia_mustdone_id,ergasia_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$ergasia_mustdone_id.",
".$ergasia_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}




$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();