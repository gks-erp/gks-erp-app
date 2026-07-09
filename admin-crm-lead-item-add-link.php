<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');



$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$link='';if (isset($_POST['link'])) $link=trim_gks(base64_decode($_POST['link']));
if ($link=='') {
  debug_mail(false,'the link is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε πρώτα την διεύθυνση')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη συνδέσμου σε ευκαιρία');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="insert into gks_crm_leads_links (
crm_lead_id,url,mydate,ip,user_id
) values (
".$id.",'".$db_link->escape_string($link)."',now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id."
)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$id = $db_link->insert_id;

$html='<tr id="tr_links_url_'.$id.'">
  <th scope="row" nowrap align="right" class="links_aa">*</td>       
  <td nowrap align="center">
    <i class="fas fa-trash-alt deleterow" data-deleteafter="gks_fnc_links_delete_after|'.$id.'" data-id="'.$id.'" data-model="gks_crm_leads_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>
  </td>
  <td nowrap><a href="admin-users-item.php?id='.$my_wp_user_id.'">'.wp_get_current_user()->gks_nickname.'</a></td>  
  <td nowrap>'.showDate(time(), 'd/m/Y H:i', 1).'</td>   
  <td style="word-break: break-word;"><div>';
$temp=trim_gks($link);
if ($temp!='' and startwith($temp,'http')) {
  $temp='<a href="'.$temp.'" target="_blank">'.(strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp).'</a>';
} else {
  $temp = (strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp);
}
$html.=$temp.'</div>'.
'<div class="progress download-perc" data-id="'.$id.'" style="display:none;">'.
'<div class="download-perc-bar progress-bar progress-bar-striped" data-id="'.$id.'" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>'.
'</div>'.
'</td>'.
'<td nowrap class="download_size_until_now" data-id="'.$id.'" style="text-align:right;vertical-align:middle;"></td>'.
'<td nowrap class="download_file_td" data-id="'.$id.'" style="text-align:center;vertical-align: middle;">
  <i class="fas fa-file-download download_action_start" data-id="'.$id.'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>
</td>
</tr>';


$return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html),'trid' => $id);
echo json_encode($return); die();

