<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αναφορά - Pivot Table - Συσκευές - Λήψη δεδομένων').'...';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_machine_pivot11','view',0);
if ($perm_ret['success']==false) {
  die();
}

gks_get_leads_status($leads_status,$leads_status_styles);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_machine',['from'=>'pivot']);
$gks_custom_header_fields = gks_custom_table_pivot_header($gks_custom_prepare);




if (isset($_gks_session['temp']['where_crm_machine_pivot11']) and $_gks_session['temp']['where_crm_machine_pivot11']!='') {
  $sql=$_gks_session['temp']['where_crm_machine_pivot11'];
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
$file_path='crm_machine_pivot11_'.$my_wp_user_id.'_'.rand(100000,999999).'.csv';
$mypath=$dir_path.$file_path;
if (file_exists($mypath)) unlink($mypath);

$myfile = fopen($mypath, "a");

$first_line='"'.gks_lang('Έτος').'","'.gks_lang('Μήνας').'","'.gks_lang('Ημερομηνία').'","'.gks_lang('Ημέρα').'","'.gks_lang('Ώρα').'",';
$first_line.='"'.gks_lang('Όνομα').'","'.gks_lang('Serial number').'","'.gks_lang('Είδος').'","'.gks_lang('Μάρκα').'","'.gks_lang('Πελάτης').'",';

$first_line.='"'.gks_lang('Συσκευές').'",';





foreach ($gks_custom_header_fields as $value) {
  $first_line.='"'.$value.'",';
}  

gks_plugins_functions_run('admin_reports_crm_machine_pivot11_json_step1',array('first_line'=>&$first_line));

$first_line=substr($first_line, 0, strlen($first_line)-1);
fwrite($myfile, $first_line."\n");


$myrows=[];
while ($row = $result->fetch_assoc()) {
  $myrows[$row['id_crm_machine']]=$row;

}
gks_plugins_functions_run('admin_reports_crm_machine_pivot11_json_step2',array('myrows'=>&$myrows));




foreach ($myrows as $row) {

  $line=
    showDate(strtotime($row['mydate_add']), 'Y', 1).','.
    showDate(strtotime($row['mydate_add']), 'm', 1).','.
    showDate(strtotime($row['mydate_add']), 'd', 1).','.
    '"'.gks_csv_txt(getWeekDayName(date('w', strtotime($row['mydate_add']) - GKS_ERP_START_VARDIA*60*60))).'",'.
    showDate(strtotime($row['mydate_add']), 'H', 1).':00'.','.
    
    

    '"'.gks_csv_txt($row['crm_machine_name']).'",'.
    '"'.gks_csv_txt($row['crm_machine_serial_number']).'",'.
    '"'.gks_csv_txt($row['product_descr_p']).'",'.
    '"'.gks_csv_txt($row['brand_fullpath']).'",'.
    '"'.gks_csv_txt($row['gks_nickname']).'",'.


    '1,';

    
   
    
    
  $line_extra= gks_custom_table_pivot_rows($gks_custom_prepare,$row);

  //print $line_extra; die();
  $line.=$line_extra;

  
  gks_plugins_functions_run('admin_reports_crm_machine_pivot11_json_step3',array('row'=>&$row,'line'=>&$line));
  

  
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


