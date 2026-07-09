<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αναφορά - Pivot Table - Παραστατικά - Λήψη δεδομένων').'...';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv_pivot4','view',0);
if ($perm_ret['success']==false) {
  die();
}



$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_inv',['from'=>'pivot']);
$gks_custom_header_fields = gks_custom_table_pivot_header($gks_custom_prepare);




if (isset($_gks_session['temp']['wherepivot4']) and $_gks_session['temp']['wherepivot4']!='') {
  $sql=$_gks_session['temp']['wherepivot4'];
} else {
  die();  
}

$sql=str_replace('SQL_CALC_FOUND_ROWS','',$sql);

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die();
}

$dir_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';
if (file_exists($dir_path) == false) mkdir($dir_path); 
$file_path='pivot4_'.$my_wp_user_id.'_'.rand(100000,999999).'.csv';
$mypath=$dir_path.$file_path;
if (file_exists($mypath)) unlink($mypath);

$myfile = fopen($mypath, "a");

$first_line='"'.gks_lang('Έτος').'","'.gks_lang('Μήνας').'","'.gks_lang('Ημερομηνία').'","'.gks_lang('Ημέρα').'","'.gks_lang('Ώρα').'","'.gks_lang('Κατάσταση').'","'.gks_lang('Εταιρεία').'","'.gks_lang('Υποκατάστημα').'","'.gks_lang('Ημερολόγιο').'","'.gks_lang('Σειρά').'","'.gks_lang('Σκοπός Διακίνησης').'","'.gks_lang('Φορολογική Θέση').'","'.gks_lang('Τιμοκατάλογος').'",'.
'"'.gks_lang('Επαφή').'","'.gks_lang('Πόλη').'","'.gks_lang('Νομός').'","'.gks_lang('Χώρα').'","'.gks_lang('Παραστατικό').'",'.
'"'.gks_lang('Τρόπος Αποστολής').'","'.gks_lang('Τρόπος Πληρωμής').'",'.
'"'.gks_lang('Αξία','part2').'","'.gks_lang('ΦΠΑ').'","'.gks_lang('Σύνολο').'","'.gks_lang('Ανάθεση').'","'.gks_lang('Εντατική Λιανική').'","'.gks_lang('App Mobile').'",';
if ($GKS_CRM_ENABLE) $first_line.='"'.gks_lang('Κανάλι').'","'.gks_lang('Επαφή Π').'","'.gks_lang('Καμπάνια').'",';
$first_line.='"'.gks_lang('Χρήστης').'",';

foreach ($gks_custom_header_fields as $value) {
  $first_line.='"'.$value.'",';
}  

$first_line=substr($first_line, 0, strlen($first_line)-1);
fwrite($myfile, $first_line."\n");



while ($row = $result->fetch_assoc()) {
  
  $ctitle=$row['company_title']; 
  $csubtitle=gks_lang('Κεντρικό'); 
  if (isset($row['company_sub_title'])) $csubtitle=$row['company_sub_title'];
  
  $line=
    showDate(strtotime($row['inv_date']), 'Y', 1).','.
    showDate(strtotime($row['inv_date']), 'm', 1).','.
    showDate(strtotime($row['inv_date']), 'd', 1).','.
    '"'.gks_csv_txt(getWeekDayName(date('w', strtotime($row['inv_date']) - GKS_ERP_START_VARDIA*60*60))).'",'.
    showDate(strtotime($row['inv_date']), 'H', 1).':00'.','.
    '"'.gks_csv_txt(getAccInvStateDescr($row['inv_state'])).'",'.
    '"'.gks_csv_txt($ctitle).'",'.
    '"'.gks_csv_txt($csubtitle).'",'.
    '"'.gks_csv_txt($row['acc_journal_descr']).'",'.
    '"'.gks_csv_txt($row['seira_code']).'",'.
    '"'.gks_csv_txt($row['aade_skopos_diakinisis_descr']).'",'.
    '"'.gks_csv_txt($row['fiscal_position_descr']).'",'.
    '"'.gks_csv_txt($row['pricelist_descr']).'",'.
    '"'.gks_csv_txt($row['gks_nickname']).'",'.
    '"'.gks_csv_txt($row['ma_poli']).'",'.
    '"'.gks_csv_txt($row['nomos_descr']).'",'.
    '"'.gks_csv_txt($row['country_name']).'",'.
    '1,'.
    '"'.gks_csv_txt($row['delivery_method_name']).'",'.
    '"'.gks_csv_txt($row['payment_acquirer_name']).'",'.
    $row['gks_price_net'].','.
    $row['gks_price_fpa'].','.
    $row['gks_price_total'].','.
    '"'.gks_csv_txt($row['gks_nickname_assigned']).'",'.
    '"'.gks_csv_txt($row['pos_name']).'",'.
    '"'.gks_csv_txt($row['erp_app_mobile_name']).'",'
    ;

  if ($GKS_CRM_ENABLE) $line.='"'.gks_csv_txt($row['crm_channel_sale_descr']).'",'.
                              '"'.gks_csv_txt($row['crm_channel_contact_gks_nickname']).'",'.
                              '"'.gks_csv_txt($row['ads_campain_name']).'",';
    
  $line.='"'.gks_csv_txt($row['gks_nickname_edit']).'",';
    
    
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


