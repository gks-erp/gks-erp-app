<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση αντικειμένου xxx-xxx');
db_open();
stat_record();
$perm_ret_acc_inv=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','autocomplete',0);
$perm_ret_acc_pay=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','autocomplete',0);
$perm_ret_whi_mov=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','autocomplete',0);
if ($perm_ret_acc_inv['success']==false and 
    $perm_ret_acc_pay['success']==false and 
    $perm_ret_whi_mov['success']==false) {
  $return = array('success' => false, 'message' => base64_encode($perm_ret_acc_inv['message']));echo json_encode($return); die();
}
     


$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
} 

$mywhere_acc_inv='';
$mywhere_acc_pay='';
$mywhere_whi_mov='';
foreach ($term_array as $value) {
  if ($value!='#' and $value!='@') {
    if (strlen($value)>=2 and startwith($value,'#') and ctype_digit(substr($value,1))) {
      $value=substr($value,1);
      $mywhere_acc_inv.=" (
        gks_acc_inv.id_acc_inv=".$value." or 
        gks_acc_inv.inv_acc_number_int=".$value."
      ) and ";  
      $mywhere_acc_pay.=" (
        gks_acc_pay.id_acc_pay=".$value." or 
        gks_acc_pay.pay_acc_number_int=".$value."
      ) and ";  
      $mywhere_whi_mov.=" (
        gks_whi_mov.id_whi_mov=".$value." or 
        gks_whi_mov.mov_whi_number_int=".$value."
      ) and ";  
    } else if (strlen($value)>=9 and startwith($value,'acc_inv#') and ctype_digit(substr($value,8))) {
      $value=substr($value,8);
      $mywhere_acc_inv.=" (
        gks_acc_inv.id_acc_inv=".$value." 
      ) and ";  
      $mywhere_acc_pay.=" (
        1=2
      ) and ";  
      $mywhere_whi_mov.=" (
        1=2
      ) and ";  
    } else if (strlen($value)>=9 and startwith($value,'acc_pay#') and ctype_digit(substr($value,8))) {
      $value=substr($value,8);
      $mywhere_acc_inv.=" (
        1=2
      ) and ";  
      $mywhere_acc_pay.=" (
        gks_acc_pay.id_acc_pay=".$value." 
      ) and ";  
      $mywhere_whi_mov.=" (
        1=2
      ) and ";  
    } else if (strlen($value)>=9 and startwith($value,'whi_mov#') and ctype_digit(substr($value,8))) {
      $value=substr($value,8);
      $mywhere_acc_inv.=" (
        1=2
      ) and ";  
      $mywhere_acc_pay.=" (
        1=2
      ) and ";  
      $mywhere_whi_mov.=" (
        gks_whi_mov.id_whi_mov=".$value." 
      ) and ";  
    
    } else if (strlen($value)==11 and startwith($value,'@') and 
      substr($value,3,1)=='/' and substr($value,6,1)=='/' and
      ctype_digit(substr($value,1,2)) and
      ctype_digit(substr($value,4,2)) and
      ctype_digit(substr($value,7,4))) { // 31/12/2024
      $mydate=substr($value,7,4).'-'.substr($value,4,2).'-'.substr($value,1,2);
      $mydate=_time_user(strtotime($mydate),-1);
      $mydate1=date('Y-m-d H:i:s',$mydate);
      $mydate2=date('Y-m-d H:i:s',$mydate+24*60*60);
      
      $mywhere_acc_inv.=" (
        gks_acc_inv.inv_date>='".$mydate1."' and 
        gks_acc_inv.inv_date< '".$mydate2."' 
      ) and ";  
      $mywhere_acc_pay.=" (
        gks_acc_pay.pay_date>='".$mydate1."' and 
        gks_acc_pay.pay_date< '".$mydate2."' 
      ) and ";  
      $mywhere_whi_mov.=" (
        gks_whi_mov.mov_date>='".$mydate1."' and 
        gks_whi_mov.mov_date< '".$mydate2."' 
      ) and ";  
      
      
      //echo 'ggggggggggg '.$mydate1.' '.$mydate2;die();
    } else {
    
    
      $value_en = greekkeybord($value);
    
      $mywhere_acc_inv.=" (
      gks_acc_inv.aade_invoicemark like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.inv_acc_seira_code like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.user_first_name like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.user_last_name like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.eponimia like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.title like '%".$db_link->escape_string($value)."%' or
      gks_acc_inv.afm like '%".$db_link->escape_string($value)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value)."%' or
      
      gks_acc_inv.aade_invoicemark like '%".$db_link->escape_string($value_en)."%' or
      gks_acc_inv.user_first_name like '%".$db_link->escape_string($value_en)."%' or
      gks_acc_inv.user_last_name like '%".$db_link->escape_string($value_en)."%' or
      gks_acc_inv.eponimia like '%".$db_link->escape_string($value_en)."%' or
      gks_acc_inv.title like '%".$db_link->escape_string($value_en)."%' or
      gks_acc_inv.afm like '%".$db_link->escape_string($value_en)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value_en)."%'
      ) and ";

      $mywhere_acc_pay.=" (
      gks_acc_pay.aade_invoicemark like '%".$db_link->escape_string($value)."%' or
      gks_acc_pay.pay_acc_seira_code like '%".$db_link->escape_string($value)."%' or
      gks__first_name.first_name like '%".$db_link->escape_string($value)."%' or
      gks__last_name.last_name like '%".$db_link->escape_string($value)."%' or
      gks_users.eponimia like '%".$db_link->escape_string($value)."%' or
      gks_users.title like '%".$db_link->escape_string($value)."%' or
      gks_users.afm like '%".$db_link->escape_string($value)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value)."%' or
      
      gks_acc_pay.aade_invoicemark like '%".$db_link->escape_string($value_en)."%' or
      gks__first_name.first_name like '%".$db_link->escape_string($value_en)."%' or
      gks__last_name.last_name like '%".$db_link->escape_string($value_en)."%' or
      gks_users.eponimia like '%".$db_link->escape_string($value_en)."%' or
      gks_users.title like '%".$db_link->escape_string($value_en)."%' or
      gks_users.afm like '%".$db_link->escape_string($value_en)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value_en)."%'
      ) and ";

      
      $mywhere_whi_mov.=" (
      gks_whi_mov.aade_invoicemark like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.mov_whi_seira_code like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.user_first_name like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.user_last_name like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.eponimia like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.title like '%".$db_link->escape_string($value)."%' or
      gks_whi_mov.afm like '%".$db_link->escape_string($value)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value)."%' or
      
      gks_whi_mov.aade_invoicemark like '%".$db_link->escape_string($value_en)."%' or
      gks_whi_mov.user_first_name like '%".$db_link->escape_string($value_en)."%' or
      gks_whi_mov.user_last_name like '%".$db_link->escape_string($value_en)."%' or
      gks_whi_mov.eponimia like '%".$db_link->escape_string($value_en)."%' or
      gks_whi_mov.title like '%".$db_link->escape_string($value_en)."%' or
      gks_whi_mov.afm like '%".$db_link->escape_string($value_en)."%' or
      ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value_en)."%'
      ) and ";  
      
    }
  }
} 
if (strlen($mywhere_acc_inv)>5) $mywhere_acc_inv=substr($mywhere_acc_inv, 0, strlen($mywhere_acc_inv)-5);
if (strlen($mywhere_acc_pay)>5) $mywhere_acc_pay=substr($mywhere_acc_pay, 0, strlen($mywhere_acc_pay)-5);
if (strlen($mywhere_whi_mov)>5) $mywhere_whi_mov=substr($mywhere_whi_mov, 0, strlen($mywhere_whi_mov)-5);



$sql="
SELECT 
'gks_acc_inv' as doc_table,
gks_acc_inv.id_acc_inv as id, 
gks_acc_inv.inv_guid as guid, 
gks_acc_inv.aade_invoicemark, 
gks_acc_inv.inv_date as xxx_date, 
gks_acc_inv.inv_acc_seira_code as xxx_seira_code, 
gks_acc_inv.inv_acc_number_int as xxx_number_int, 
gks_acc_inv.inv_state as xxx_state,
gks_acc_inv.user_first_name, 
gks_acc_inv.user_last_name, 
gks_acc_inv.eponimia, 
gks_acc_inv.title, 
gks_acc_inv.afm, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_acc_inv.gks_price_net, 
gks_acc_inv.gks_price_total,
gks_acc_inv.products_posotita,
gks_acc_journal.acc_journal_descr
FROM (gks_acc_inv
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal
where ".($perm_ret_acc_inv['success']==false ? '1=2 and ' : '')."
(".$mywhere_acc_inv.")

union

SELECT 
'gks_acc_pay' as doc_table,
gks_acc_pay.id_acc_pay as id, 
gks_acc_pay.pay_guid as guid, 
gks_acc_pay.aade_invoicemark, 
gks_acc_pay.pay_date as xxx_date, 
gks_acc_pay.pay_acc_seira_code as xxx_seira_code, 
gks_acc_pay.pay_acc_number_int as xxx_number_int, 
gks_acc_pay.pay_state as xxx_state,
gks__first_name.first_name as user_first_name, 
gks__last_name.last_name as user_last_name, 
gks_users.eponimia, 
gks_users.title, 
gks_users.afm, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_acc_pay.gks_price_total as gks_price_net, 
gks_acc_pay.gks_price_total,
0 as products_posotita,
gks_acc_journal.acc_journal_descr
FROM ((((gks_acc_pay
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON gks_acc_pay.user_id = gks_users.user_id)
LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal)
LEFT JOIN (
  SELECT user_id, meta_value AS first_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE meta_key='first_name'
) AS gks__first_name ON gks_acc_pay.user_id = gks__first_name.user_id)
LEFT JOIN (
  SELECT user_id, meta_value AS last_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE meta_key='last_name'
) AS gks__last_name ON gks_acc_pay.user_id = gks__last_name.user_id


where ".($perm_ret_acc_pay['success']==false ? '1=2 and ' : '')."
(".$mywhere_acc_pay.")

union

SELECT
'gks_whi_mov' as doc_table,
gks_whi_mov.id_whi_mov as id, 
gks_whi_mov.mov_guid as guid, 
gks_whi_mov.aade_invoicemark, 
gks_whi_mov.mov_date as xxx_date, 
gks_whi_mov.mov_whi_seira_code as xxx_seira_code, 
gks_whi_mov.mov_whi_number_int as xxx_number_int, 
gks_whi_mov.mov_state as xxx_state, 
gks_whi_mov.user_first_name, 
gks_whi_mov.user_last_name, 
gks_whi_mov.eponimia, 
gks_whi_mov.title, 
gks_whi_mov.afm, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
null as gks_price_net, 
null as gks_price_total,
gks_whi_mov.products_posotita,
gks_acc_journal.acc_journal_descr
FROM (gks_whi_mov
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal
where ".($perm_ret_whi_mov['success']==false ? '1=2 and ' : '')."
(".$mywhere_whi_mov.")


order by xxx_date desc
limit 100";


//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
  

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $value=trim_gks($row['aade_invoicemark']);
  if ($value=='') {
    if ($row['doc_table']=='gks_acc_inv') {
      $value='acc_inv#'.$row['id'];
    } else if ($row['doc_table']=='gks_acc_pay') {
      $value='acc_pay#'.$row['id'];
    } else if ($row['doc_table']=='gks_whi_mov') {
      $value='whi_mov#'.$row['id'];
    }
  }
  
  $text=[];
  if (isset($row['id'])) $text[]='#'.$row['id'];
  if (isset($row['xxx_date'])) $text[]=showDate(strtotime($row['xxx_date']), 'd/m/Y H:i', 1);
  if (isset($row['acc_journal_descr'])) $text[]=$row['acc_journal_descr'];
  if (isset($row['xxx_seira_code'])) $text[]=$row['xxx_seira_code'];
  if (isset($row['xxx_number_int'])) $text[]=$row['xxx_number_int'];
  if (isset($row['xxx_state'])) {
    if ($row['doc_table']=='gks_acc_inv') {
      $text[]=getAccInvStateDescr($row['xxx_state']);
    } else if ($row['doc_table']=='gks_acc_pay') {
      $text[]=getAccPayStateDescr($row['xxx_state']);
    } else if ($row['doc_table']=='gks_whi_mov') {
      $text[]=getWhiMovStateDescr($row['xxx_state']);
    }
  }
  

  if (!empty($row['gks_price_net'])) $text[]=myCurrencyFormat($row['gks_price_net']);
  if (!empty($row['gks_price_total'])) $text[]=myCurrencyFormat($row['gks_price_total']);
  if (!empty($row['products_posotita'])) $text[]=myCurrencyFormat($row['products_posotita']);

  
  if (isset($row['gks_nickname'])) $text[]=$row['gks_nickname'];
  if (isset($row['afm'])) $text[]=$row['afm'];
//  if (isset($row['eponimia'])) $text[]=$row['eponimia'];
//  if (isset($row['title'])) $text[]=$row['title'];
//  if (isset($row['user_first_name'])) $text[]=$row['user_first_name'];
//  if (isset($row['user_last_name'])) $text[]=$row['user_last_name'];
  
  
  $mark=trim_gks($row['aade_invoicemark']);
  $coi_acc_inv_id=0;
  $coi_acc_pay_id=0;
  $coi_whi_mov_id=0;
  if ($row['doc_table']=='gks_acc_inv') {
    $coi_acc_inv_id=intval($row['id']);
  } else if ($row['doc_table']=='gks_acc_pay') {
    $coi_acc_pay_id=intval($row['id']);
  } else if ($row['doc_table']=='gks_whi_mov') {
    $coi_whi_mov_id=intval($row['id']);
  }
  
  $out[] = array(
    'value' => $value, 
    'descr'=> implode(', ',$text),
    'mark' => $mark, 
    'coi_acc_inv_id' => $coi_acc_inv_id, 
    'coi_acc_pay_id' => $coi_acc_pay_id, 
    'coi_whi_mov_id' => $coi_whi_mov_id, 
    'mcm_acc_inv_id' => $coi_acc_inv_id, 
    'mcm_acc_pay_id' => $coi_acc_pay_id, 
    'mcm_whi_mov_id' => $coi_whi_mov_id, 
    
  );
  
  
  
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



