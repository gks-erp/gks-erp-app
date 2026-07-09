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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, 
".GKS_WP_TABLE_PREFIX."users.user_email, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities,
".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image,
".GKS_WP_TABLE_PREFIX."users.viber_id,
".GKS_WP_TABLE_PREFIX."users.viber_subscribed,
gks_users.afm
FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id ";

$return_vat_only=false;
if (isset($_GET['return_vat_only']) and $_GET['return_vat_only']=='1') {
  $sql.=" where gks_users.afm <>'' and ";
  $return_vat_only=true;
} else if (isset($_GET['pro']) and $_GET['pro']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('promitheutis')."%' and ";
} else if (isset($_GET['viber']) and $_GET['viber']=='1') {
  $sql.=" where viber_id<>'' and viber_subscribed<>0 and ";
} else if (isset($_GET['all']) and $_GET['all']=='1') {
  $sql.=" where 1=1 and ";
} else if (isset($_GET['hr']) and $_GET['hr']=='1') {
  $sql.=" where (gks_wp_capabilities like '%".$db_link->escape_string('hrmanager')."%' or gks_wp_capabilities like '%".$db_link->escape_string('omadarxis')."%') and ";
} else if (isset($_GET['driver']) and $_GET['driver']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('driver')."%' and ";
} else if (isset($_GET['externalpartner']) and $_GET['externalpartner']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('externalpartner')."%' and ";
} else if (isset($_GET['promitheutis']) and $_GET['promitheutis']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('promitheutis')."%' and ";
} else if (isset($_GET['eml']) and $_GET['eml']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('employee')."%' and ";
} else if (isset($_GET['salesman']) and $_GET['salesman']=='1') {
  $sql.=" where gks_wp_capabilities like '%".$db_link->escape_string('salesman')."%' and ";
} else if (isset($_GET['company']) and $_GET['company']=='1') {
  $sql.=" where (gks_wp_capabilities like '%".$db_link->escape_string('administrator')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('adminmy')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('logistis')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('hrmanager')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('texnikos')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('apothikarios')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('omadarxis')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('ipethinosperioxis')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('xiristismixanimaton')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('findphotos')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('babys')."%' or
                 gks_wp_capabilities like '%".$db_link->escape_string('ordermanager')."%' or 
                 gks_wp_capabilities like '%".$db_link->escape_string('employee')."%' ) and ";
} else if (isset($_GET['other_calendar']) and $_GET['other_calendar']=='1') {
  $sql.=" where ".GKS_WP_TABLE_PREFIX."users.ID in (select other_user_id from gks_calendar_other_users where this_user_id=".$my_wp_user_id.") and ";
} else {
  //$sql.=" where gks_wp_capabilities not like '".$db_link->escape_string('a:1:{s:10:"subscriber";b:1;}')."' and ";
  $sql.=" where 1=1 and ";
}

if (isset($_GET['notme']) and $_GET['notme']=='1') $sql.=" ".GKS_WP_TABLE_PREFIX."users.ID<>".$my_wp_user_id." and ";

if (isset($_GET['notids'])) {
  //print '<pre>';print $_GET['notids'];
  //print '<pre>';print rawurldecode($_GET['notids']);
  $notids = trim_gks(base64_decode(rawurldecode($_GET['notids'])));
  //print '<pre>';print $notids;die();
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
	      //print '<pre>';print_r($fix);die();
	      $sql.=" ".GKS_WP_TABLE_PREFIX."users.ID not in (".implode(',',$fix).") and ";
	    }
	  }
	}
}


$sql.=" (
".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($term)."%' or 
".GKS_WP_TABLE_PREFIX."users.user_email like '%".$db_link->escape_string($term)."%' or 
".GKS_WP_TABLE_PREFIX."users.gks_fullname like '%".$db_link->escape_string($term)."%' or 
".GKS_WP_TABLE_PREFIX."users.comm_search like '%".$db_link->escape_string($term)."%' or
".GKS_WP_TABLE_PREFIX."users.gks_mobile like '%".$db_link->escape_string($term)."%' or
gks_users.phone_home like '%".$db_link->escape_string($term)."%' or
gks_users.eponimia like '%".$db_link->escape_string($term)."%' or
gks_users.afm like '%".$db_link->escape_string($term)."%' or
gks_users.doy like '%".$db_link->escape_string($term)."%'

)
order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname
limit 1000";
//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
  
$photo=false;
if (isset($_GET['photo']) and intval($_GET['photo'])!=0) $photo=true;
$photo_size=64;
if (isset($_GET['photo_size']) and intval($_GET['photo_size'])!=0) $photo_size=intval($_GET['photo_size']);
$fromtagit=false;
if (isset($_GET['fromtagit']) and intval($_GET['fromtagit'])!=0) $fromtagit=true;


$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $has_viber=false;
  if ($row['viber_id'].''<>'' and $row['viber_subscribed'] <>0) $has_viber=true;

  if ($return_vat_only) {
    $out[] = array(
      'id' => $row['ID'],
      'value' => trim_gks($row['afm']),
      'descr' => trim_gks($row['gks_nickname']),
    );    
  } else if ($fromtagit) {
    $out[] = array(
      'value' => trim_gks($row['gks_nickname']) .' | (#'.$row['ID'].')'
    );
  } else {
      
    if ($photo) {
      $out[] = array('id' => $row['ID'], 'value' => $row['gks_nickname'], 'has_viber' => $has_viber, 'photo' => getUserPhoto($row['ID'],$row['gks_wsl_current_user_image'],$photo_size));
    } else {
      $out[] = array('id' => $row['ID'], 'value' => $row['gks_nickname'], 'has_viber' => $has_viber);
    }
  }
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



