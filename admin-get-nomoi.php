<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


if (!isset($_POST['id'])) {
  debug_mail(false,'error on id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ID είναι λάθος').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();
}
$id=intval($_POST['id']);

if ($id<0) {
  debug_mail(false,'error on id (2):'.$id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ID είναι λάθος').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();  
}
$search_string=''; if (isset($_POST['search_string'])) $search_string=trim_gks(base64_decode($_POST['search_string']));
$search_obj=array();if ($search_string!='') $search_obj=json_decode($search_string,true);
//print '<pre>';print_r($search_obj);die();


$my_page_title=gks_lang('Λήψη λίστας νομών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_nomoi','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$out=array();


$lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
if ($lang_prepare_gks_nomoi['success']==false) die($lang_prepare_gks_nomoi['message']);
gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));

$sql="SELECT id_nomos, ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi
".$lang_prepare_gks_nomoi['sql']['from2']."
WHERE country_id=".$id." 
ORDER BY ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi,'',true);
//echo '<pre>';echo $sql;die();

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); 
}



$odos='';if (isset($search_obj['odos'])) $odos=mb_strtoupper(trim_gks(cleartonous_php($search_obj['odos'])));
$tk='';if (isset($search_obj['tk'])) $tk=mb_strtoupper(trim_gks(cleartonous_php($search_obj['tk'])));
$premise='';if (isset($search_obj['premise'])) $premise=mb_strtoupper(trim_gks(cleartonous_php($search_obj['premise'])));
$neighborhood='';if (isset($search_obj['neighborhood'])) $neighborhood=mb_strtoupper(trim_gks(cleartonous_php($search_obj['neighborhood'])));
$locality='';if (isset($search_obj['locality'])) $locality=mb_strtoupper(trim_gks(cleartonous_php($search_obj['locality'])));
$sublevel_1='';if (isset($search_obj['sublevel_1'])) $sublevel_1=mb_strtoupper(trim_gks(cleartonous_php($search_obj['sublevel_1'])));
$level_1='';if (isset($search_obj['level_1'])) $level_1=mb_strtoupper(trim_gks(cleartonous_php($search_obj['level_1'])));
$level_2='';if (isset($search_obj['level_2'])) $level_2=mb_strtoupper(trim_gks(cleartonous_php($search_obj['level_2'])));
$level_3='';if (isset($search_obj['level_3'])) $level_3=mb_strtoupper(trim_gks(cleartonous_php($search_obj['level_3'])));

$selected_id_pithano1=0;
$selected_id_pithano2=0;
$selected_id_pithano3=0;
while ($row = $result->fetch_assoc()) {  
  $out[]=array('id' => $row['id_nomos'], 'descr'=> $row['nomos_descr']);
  
  $nomos_descr=mb_strtoupper(trim_gks(cleartonous_php($row['nomos_descr'])));
  $nomos_descr2=$nomos_descr;
  if (endwith($nomos_descr,'Σ')) {
    $nomos_descr2=substr($nomos_descr, 0, strlen($nomos_descr)-2);
    //echo '<pre>';echo '|'.$nomos_descr.'|'.$nomos_descr2.'|';
  }
  if ($selected_id_pithano1==0 and $level_1!='' and ($level_1==$nomos_descr or $level_1==$nomos_descr2)) $selected_id_pithano1=intval($row['id_nomos']);
  if ($selected_id_pithano2==0 and $level_2!='' and ($level_2==$nomos_descr or $level_2==$nomos_descr2)) $selected_id_pithano2=intval($row['id_nomos']);
  if ($selected_id_pithano3==0 and $level_3!='' and ($level_3==$nomos_descr or $level_3==$nomos_descr2)) $selected_id_pithano3=intval($row['id_nomos']);
  
}

$selected_id=0;
if ($id==91) { //mono ellada
  if ($tk!='') {
    $sql="SELECT nomos_id FROM gks_tk WHERE tk='".$db_link->escape_string($tk)."' GROUP BY nomos_id";
    //echo '<pre>';print $sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }    
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $selected_id=intval($row['nomos_id']);}
  }
  //if ($selected_id_pithano==0) {
    //echo '<pre>';print $locality;die();
  //}
}

if ($selected_id==0 and $selected_id_pithano1>0) $selected_id=$selected_id_pithano1;
if ($selected_id==0 and $selected_id_pithano2>0) $selected_id=$selected_id_pithano2;
if ($selected_id==0 and $selected_id_pithano3>0) $selected_id=$selected_id_pithano3;


  
$return = array('success' => true, 'message' => base64_encode('ok'),'out' => $out,'selected_id' => $selected_id);
echo json_encode($return); die();  
