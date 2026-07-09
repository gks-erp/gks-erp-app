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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση πρότυπου email');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email_template','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
  }
} 
//print '<pre>';
//print_r($term_array);
$anddisabled=0;
if (isset($_GET['anddisabled']) and $_GET['anddisabled']=='1') $anddisabled=1;
$notid=-1;
if (isset($_GET['notid'])) $notid=intval($_GET['notid']);
$lang_id='';
if (isset($_GET['lang_id']) and $_GET['lang_id']!='') $lang_id=trim_gks($_GET['lang_id']);


$sql="SELECT id_email_template,email_template_descr FROM gks_email_template
where 
".($anddisabled==1 ? '' : "is_disable=0 and ")." 
".($notid<=0 ? '' : "id_email_template<>".$notid." and ")." 
".($lang_id=='' ? '' : "gks_lang like '".$db_link->escape_string($lang_id)."' and ")." 
(
";
$mywhere='';
foreach ($term_array as $value) {
  $value_en = greekkeybord($value);
  $mywhere.=" (email_template_descr like '%".$db_link->escape_string($value)."%' or email_template_descr like '%".$db_link->escape_string($value_en)."%') and ";
} 
if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
$sql.=$mywhere.")  
order by gks_email_template.sortorder,gks_email_template.email_template_descr
limit 1000";  
  


//print '<pre>';
//echo $sql;
//echo "\r\n";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array('id' => $row['id_email_template'], 'value' => $row['email_template_descr']);
}

//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



