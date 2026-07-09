<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

//debug_mail(false,'dim--','start');

if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();
//debug_mail(false,'term',$term);

$my_page_title=gks_lang('Αυτόματη συμπλήρωση οδού-ΤΚ');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_tk','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
} 
//print '<pre>';
//print_r($term_array);

$sql="SELECT gks_tk.odos, gks_tk.arithmos, gks_tk.perioxi, gks_tk.poli, gks_tk.tk, 
gks_tk.nomos_id, gks_nomoi.nomos_descr, 
gks_nomoi.country_id, gks_country.country_name
FROM (gks_tk 
LEFT JOIN gks_nomoi ON gks_tk.nomos_id = gks_nomoi.id_nomos) 
LEFT JOIN gks_country ON gks_nomoi.country_id = gks_country.id_country

where 1=1 and ";

  
$mywhere='';
foreach ($term_array as $value) {
  //$value_en = greekkeybord($value);
  $mywhere.=" (
  gks_tk.odos like '%".$db_link->escape_string($value)."%' or
  gks_tk.poli like '%".$db_link->escape_string($value)."%' or
  gks_tk.perioxi like '%".$db_link->escape_string($value)."%' or
  gks_tk.tk like '%".$db_link->escape_string($value)."%' or
  gks_nomoi.nomos_descr like '%".$db_link->escape_string($value)."%' 
  ) and ";
} 

if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
$sql.=$mywhere."
order by gks_tk.odos,gks_tk.perioxi, gks_tk.poli, gks_tk.tk,gks_nomoi.nomos_descr,gks_country.country_name
limit 1000"; 


//print '<pre>'.$sql;die();


$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$out=array();
while ($row = $result->fetch_assoc()) {
  $out[] = array(
    'value' => trim_gks($row['odos'].' '.$row['arithmos']), 
    'odos' => $row['odos'], 
    'arithmos' => $row['arithmos'], 
    'orofos' => '', 
    'perioxi' => $row['perioxi'], 
    'poli' => $row['poli'], 
    'tk' => $row['tk'],
    'nomos_id' => $row['nomos_id'],
    'nomos_descr' => $row['nomos_descr'],
    'country_id' => $row['country_id'],
    'country_name' => $row['country_name'],
    
  
  );
}


//echo json_encode($out); die();



//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);


