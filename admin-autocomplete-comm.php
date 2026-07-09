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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση στοιχείου επικοινωνίας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="SELECT gks_users_communication.id_user_communication, 
gks_users_communication.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_users_communication.comm_type, gks_users_communication.comm_value, 
gks_users_communication.comm_descr, gks_users_communication.comm_primary,
gks_users_communication.phone_fix,
gks_wsl_current_user_image
FROM gks_users_communication 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
AND gks_users_communication.comm_value<>''
AND (
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($term)."%' or 
  gks_users_communication.comm_value like '%".$db_link->escape_string($term)."%' or
  gks_users_communication.comm_descr like '%".$db_link->escape_string($term)."%' or
  gks_users_communication.phone_fix like '%".$db_link->escape_string($term)."%' 
) ";


if (isset($_GET['comm_type']) and $_GET['comm_type']!='') {
  $sql.=" AND comm_type like '".$db_link->escape_string($_GET['comm_type'])."'";
}
$sql.=" 
ORDER BY gks_users_communication.comm_value, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
limit 1000";



//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
  
$photo=false;
if (isset($_GET['photo']) and intval($_GET['photo'])!=0) $photo=true;
$fromtagit=false;
if (isset($_GET['fromtagit']) and intval($_GET['fromtagit'])!=0) $fromtagit=true;

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  if ($fromtagit) {
    
    $item = array(
      'value' => $row['comm_value'].' | '.trim_gks($row['gks_nickname']) .' | (#'.$row['user_id'].')'
    );
  } else {
    $item = array(
      'value' => $row['comm_value'], 
      'descr' => $row['comm_descr'],
      'user_id' => $row['user_id'], 
      'user' => trim_gks($row['gks_nickname']), 
      'pr' => $row['comm_primary'],
      'phone_fix' => $row['phone_fix'],
    );
    if ($photo) {
      $item['photo']=getUserPhoto($row['user_id'],$row['gks_wsl_current_user_image'],64);
      
    }
  }
  
  
  $out[]=$item;
  
  
  
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



