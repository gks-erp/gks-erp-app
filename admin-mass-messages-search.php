<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Μαζική Αποστολή Viber-SMS-email - Αναζήτηση');
 
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_mass_messages','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$one_user_id=0;if (isset($_POST['one_user_id'])) $one_user_id=intval($_POST['one_user_id']);

$sql_one_user_id='';
$sql_roles='';
$sql_groups='';
$sql_not_work_date='';
$sql_work_date='';

if ($one_user_id>0) {
  $sql_one_user_id=" and ".GKS_WP_TABLE_PREFIX."users.ID=".$one_user_id;
} else {
  $myroles_str=trim(base64_decode($_POST['myroles_str']));
  $myroles_array = json_decode($myroles_str, true);
  if ($myroles_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['myroles_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $mygroups_str=trim(base64_decode($_POST['mygroups_str']));
  $mygroups_array = json_decode($mygroups_str, true);
  if ($mygroups_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['mygroups_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $temp=[];
  foreach ($myroles_array as $value) {
    $temp[]=GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities like '%".$db_link->escape_string($value)."%'";
  } 
  if (count($temp)>0) {
    $sql_roles=" and (".implode(' or ',$temp).") ";
  }
  
  
  if (count($mygroups_array)>0) {
    $sql="SELECT 
    gu1.id_users_group AS gid1, 
    gu2.id_users_group AS gid2, 
    gu3.id_users_group AS gid3, 
    gu4.id_users_group AS gid4, 
    gu5.id_users_group AS gid5,
    gu6.id_users_group AS gid6,
    gu7.id_users_group AS gid7,
    gu8.id_users_group AS gid8,
    gu9.id_users_group AS gid9,
    gu10.id_users_group AS gid10
    FROM ((((((((gks_users_groups AS gu1 
    LEFT JOIN gks_users_groups AS gu2  ON gu1.id_users_group = gu2.group_parent_id) 
    LEFT JOIN gks_users_groups AS gu3  ON gu2.id_users_group = gu3.group_parent_id) 
    LEFT JOIN gks_users_groups AS gu4  ON gu3.id_users_group = gu4.group_parent_id) 
    LEFT JOIN gks_users_groups AS gu5  ON gu4.id_users_group = gu5.group_parent_id)
    LEFT JOIN gks_users_groups AS gu6  ON gu5.id_users_group = gu6.group_parent_id)
    LEFT JOIN gks_users_groups AS gu7  ON gu6.id_users_group = gu7.group_parent_id)
    LEFT JOIN gks_users_groups AS gu8  ON gu7.id_users_group = gu8.group_parent_id)
    LEFT JOIN gks_users_groups AS gu9  ON gu8.id_users_group = gu9.group_parent_id)
    LEFT JOIN gks_users_groups AS gu10 ON gu9.id_users_group = gu10.group_parent_id
    
    where gu1.id_users_group in (".implode(',',$mygroups_array).")";
    //echo $sql;
    $result_gu = $db_link->query($sql);        
    if (!$result_gu) {
      debug_mail(false,'error sql',$sql);
      die('sql error');
    }
    $gu_in=[];
    
    while ($row_gu = $result_gu->fetch_assoc()) {
      if (isset($row_gu['gid1'])  and in_array($row_gu['gid1'], $gu_in)==false) $gu_in[]=$row_gu['gid1'];
      if (isset($row_gu['gid2'])  and in_array($row_gu['gid2'], $gu_in)==false) $gu_in[]=$row_gu['gid2'];
      if (isset($row_gu['gid3'])  and in_array($row_gu['gid3'], $gu_in)==false) $gu_in[]=$row_gu['gid3'];
      if (isset($row_gu['gid4'])  and in_array($row_gu['gid4'], $gu_in)==false) $gu_in[]=$row_gu['gid4'];
      if (isset($row_gu['gid5'])  and in_array($row_gu['gid5'], $gu_in)==false) $gu_in[]=$row_gu['gid5'];
      if (isset($row_gu['gid6'])  and in_array($row_gu['gid6'], $gu_in)==false) $gu_in[]=$row_gu['gid6'];
      if (isset($row_gu['gid7'])  and in_array($row_gu['gid7'], $gu_in)==false) $gu_in[]=$row_gu['gid7'];
      if (isset($row_gu['gid8'])  and in_array($row_gu['gid8'], $gu_in)==false) $gu_in[]=$row_gu['gid8'];
      if (isset($row_gu['gid9'])  and in_array($row_gu['gid9'], $gu_in)==false) $gu_in[]=$row_gu['gid9'];
      if (isset($row_gu['gid10']) and in_array($row_gu['gid10'],$gu_in)==false) $gu_in[]=$row_gu['gid10'];
    }
    if (count($gu_in)>0) {
      $sql_groups=" and ".GKS_WP_TABLE_PREFIX."users.ID in (
        SELECT user_id
        FROM gks_users_groups_users
        WHERE group_id In (".implode(',',$gu_in).")
        GROUP BY user_id
      )";
      
    }
    //echo '<pre>'.$sql_groups;die();
  }
  
  



  
}

$sql="SELECT ID as i, 
gks_nickname as n, 
user_email as e1,
gks_mobile as m1, 
viber_id as v1, 
viber_subscribed as v2
FROM ".GKS_WP_TABLE_PREFIX."users
where 1=1".
$sql_one_user_id.
$sql_roles.
$sql_groups.
$sql_not_work_date.
$sql_work_date.

" ORDER BY gks_nickname";
//$return = array('success' => false, 'message' => base64_encode('<pre>'.$sql));
//echo json_encode($return); die();

$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$data=[];
while ($row = $result->fetch_assoc()) {
  $row['i']=intval($row['i']);
  $row['v2']=intval($row['v2']);
  if ($row['v2']!=0 and $row['v1']!='') {
    $row['v']=1;
  } else {
    $row['v']=0;  
  }
  unset($row['v1']);
  unset($row['v2']);
  
  if (trim($row['m1'])!='') {
    $row['m']=1;
  } else {
    $row['m']=0;
  }
  unset($row['m1']);
  
  if (trim($row['e1'])!='') {
    $row['e']=1;
  } else {
    $row['e']=0;
  }
  unset($row['e1']);
  if ($row['v']==1 or $row['m']==1 or $row['e']==1) {
    $data[]=$row;
  }
}
$return = array('success' => true, 'message' => base64_encode('OK'), 'data' => $data);
echo json_encode($return); die();


$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myroles_array,true).print_r($mygroups_array,true)));
echo json_encode($return); die();
