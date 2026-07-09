<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αναφορά - Pivot Table - Ευκαιρίες - Λήψη δεδομένων').'...';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads_pivot10','view',0);
if ($perm_ret['success']==false) {
  die();
}

gks_get_leads_status($leads_status,$leads_status_styles);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_leads',['from'=>'pivot']);
$gks_custom_header_fields = gks_custom_table_pivot_header($gks_custom_prepare);




if (isset($_gks_session['temp']['where_crm_leads_pivot10']) and $_gks_session['temp']['where_crm_leads_pivot10']!='') {
  $sql=$_gks_session['temp']['where_crm_leads_pivot10'];
} else {
  die();  
}

$sql=str_replace('SQL_CALC_FOUND_ROWS','',$sql);
//echo $sql; die();

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die();
}




$dir_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';
if (file_exists($dir_path) == false) mkdir($dir_path); 
$file_path='crm_leads_pivot10_'.$my_wp_user_id.'_'.rand(100000,999999).'.csv';
$mypath=$dir_path.$file_path;
if (file_exists($mypath)) unlink($mypath);

$myfile = fopen($mypath, "a");

$first_line='"'.gks_lang('Έτος').'","'.gks_lang('Μήνας').'","'.gks_lang('Ημερομηνία').'","'.gks_lang('Ημέρα').'","'.gks_lang('Ώρα').'","'.gks_lang('Κατάσταση').'",';
$first_line.='"'.gks_lang('Εταιρεία').'","'.gks_lang('Υποκατάστημα').'","'.gks_lang('Επαφή').'","'.gks_lang('Πόλη').'","'.gks_lang('Νομός').'","'.gks_lang('Χώρα').'",';

$first_line.='"'.gks_lang('Ευκαιρίες').'","'.gks_lang('Αναμενόμενα έσοδα').'",';
$first_line.='"'.gks_lang('Κανάλι πωλήσεων').'","'.gks_lang('Καμπάνια').'","'.gks_lang('Ανάθεση').'","'.gks_lang('Χρήστης').'",';





foreach ($gks_custom_header_fields as $value) {
  $first_line.='"'.$value.'",';
}  

$first_line=substr($first_line, 0, strlen($first_line)-1);
fwrite($myfile, $first_line."\n");



while ($row = $result->fetch_assoc()) {
  

  
  $ctitle=$row['company_title']; 
  $csubtitle=gks_lang('Κεντρικό'); 
  if (isset($row['company_sub_title'])) $csubtitle=$row['company_sub_title'];

  $lead_status_descr='--'; if (isset($leads_status[$row['lead_status_id']])) $lead_status_descr=$leads_status[$row['lead_status_id']]['lead_status_descr'];

  $line=
    showDate(strtotime($row['lead_date']), 'Y', 1).','.
    showDate(strtotime($row['lead_date']), 'm', 1).','.
    showDate(strtotime($row['lead_date']), 'd', 1).','.
    '"'.gks_csv_txt(getWeekDayName(date('w', strtotime($row['lead_date']) - GKS_ERP_START_VARDIA*60*60))).'",'.
    showDate(strtotime($row['lead_date']), 'H', 1).':00'.','.
    

    '"'.gks_csv_txt($lead_status_descr).'",'.
    '"'.gks_csv_txt($ctitle).'",'.
    '"'.gks_csv_txt($csubtitle).'",'.
    '"'.gks_csv_txt($row['gks_nickname']).'",'.
    '"'.gks_csv_txt($row['poli']).'",'.
    '"'.gks_csv_txt($row['nomos_descr']).'",'.
    '"'.gks_csv_txt($row['country_name']).'",'.

    '1,'.
    
    

    $row['esoda'].','.
    '';
    


  $line.=
  
    '"'.gks_csv_txt($row['crm_channel_sale_descr']).'",'.
    '"'.gks_csv_txt($row['ads_campain_name']).'",'.
    '"'.gks_csv_txt($row['gks_nickname_assigned']).'",'.
    '"'.gks_csv_txt($row['gks_nickname_edit']).'",';
    
    
  $line_extra= gks_custom_table_pivot_rows($gks_custom_prepare,$row);

  //print $line_extra; die();
  $line.=$line_extra;
  
  $line=substr($line, 0, strlen($line)-1);
  
  
  fwrite($myfile, $line."\n");
    
}  

fclose($myfile);

$return = array('success' => true, 'message' => base64_encode('OK'),'url' => '/my/temp/'.$file_path);
echo json_encode($return); die();


$offset = 0; // 60 * 60 * 24; //24 ores
//$info = getimagesize($mypath);
//header('Content-Type: '.$info['mime']);
header('Content-Disposition: attachment; filename="'.basename($mypath).'"');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
header("Cache-Control: max-age=$offset, must-revalidate"); 
header("Pragma: private");

readfile($mypath);
die();


