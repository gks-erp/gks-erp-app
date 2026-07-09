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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση εργασίας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT gks_crm_tasks.*
FROM gks_crm_tasks
where 
(subject like '%".$db_link->escape_string($term)."%' or
 message like '%".$db_link->escape_string($term)."%')
";


if (isset($_GET['notids'])) {
  $notids = trim_gks(base64_decode(rawurldecode($_GET['notids'])));
	$notids = json_decode($notids, true);
	if (!($notids === null && json_last_error() !== JSON_ERROR_NONE)) {
	  if (is_array($notids) and count($notids)>0) {
	    $fix=array();
	    foreach ($notids as $value) {
        $value=intval($value);
        if ($value>0) {
          $fix[]=$value;
        }
      } 
      if (count($fix)>0) {
	      $sql.=" and id_crm_task not in (".implode(',',$fix).") ";
	    }
	  }
	}
}


$sql.=" order by subject
limit 1000";
//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}


$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array('id' => $row['id_crm_task'], 'value' => $row['subject']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



