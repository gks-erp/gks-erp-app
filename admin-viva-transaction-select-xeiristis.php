<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$myid=0;
if (isset($_POST['myid'])) $myid=intval($_POST['myid']);
if ($myid<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Συναλλαγές Viva Επιλογή Χειριστή').': '.$myid;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_viva_transaction','edit',0);
if ($perm_ret['success']==false) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();}


$sql="SELECT add_date,xeiristis_id FROM gks_viva_transaction WHERE id_viva_transaction=".$myid." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}
$row = $result->fetch_assoc();
$add_date=$row['add_date'];
$xeiristis_id=intval($row['xeiristis_id']);

$time_vardia=_time_user(strtotime($add_date), 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

$sql="SELECT ID, gks_nickname
FROM ".GKS_WP_TABLE_PREFIX."users
where ID in (
  SELECT gks_assets_moves.user_id
  FROM gks_assets_moves LEFT JOIN gks_assets ON gks_assets_moves.asset_id = gks_assets.id_asset
  WHERE gks_assets_moves.mydate < date_add('".$today_vardia."', interval 1 DAY)
  AND gks_assets.asset_type=27
  AND gks_assets_moves.user_id>0
  GROUP BY gks_assets_moves.user_id
) or ID in (
  SELECT asset_last_user_id
  FROM gks_assets
  WHERE asset_type=27
  and asset_last_user_id>0
  GROUP BY asset_last_user_id
)
ORDER BY gks_nickname";

$result = $db_link->query($sql);  
if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  

$html='';
$i=1;
while ($row = $result->fetch_assoc()) {
  $i++;
  $html.='<tr>'.
    '<td align="center"><input type="radio" name="selraf" value="'.$row['ID'].'" '.
    ($xeiristis_id==$row['ID'] ? 'checked' : '').
    '></td>'.
    '<td>'.$row['gks_nickname'].'</td>'.
  '</tr>';
}


if ($html=='') {
  debug_mail(false,'xeiristes not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν χειριστές').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}

$html='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">'.
'<thead><tr>'.
'<th class="table-dark" scope="col" width="0%">'.gks_lang('Επιλογή').'</th>'.
'<th class="table-dark" scope="col" width="100%">'.gks_lang('Χειριστής').'</th>'.
'</tr></thead><tbody>'.

'<tr>'.
  '<td align="center"><input type="radio" name="selraf" value="0" '.
  ($xeiristis_id==0 ? 'checked' : '').
  '></td>'.
  '<td>'.gks_lang('Κανείς').'</td>'.
'</tr>'.
  
$html.
'</tbody></table>';

$return = array('success' => true, 'message' => base64_encode($html));
echo json_encode($return); die();  
