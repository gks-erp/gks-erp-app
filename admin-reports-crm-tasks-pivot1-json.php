<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αναφορά - Pivot Table - Εργασίες - Λήψη δεδομένων').'...';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks_pivot1','view',0);
if ($perm_ret['success']==false) {
  die();
}

gks_get_tasks_status($tasks_status,$tasks_status_styles);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_tasks',['from'=>'pivot']);
$gks_custom_header_fields = gks_custom_table_pivot_header($gks_custom_prepare);




if (isset($_gks_session['temp']['where_crm_tasks_pivot1']) and $_gks_session['temp']['where_crm_tasks_pivot1']!='') {
  $sql=$_gks_session['temp']['where_crm_tasks_pivot1'];
} else {
  die();  
}

$sql=str_replace('SQL_CALC_FOUND_ROWS','',$sql);
//echo $sql; die();

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die();
}


$sql_employee="SELECT gks_crm_tasks_employee.crm_task_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM gks_crm_tasks_employee 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID
where gks_crm_tasks_employee.crm_task_id in (
  select id_crm_task
  from (
    ".$sql."
  ) as gksmytaskssel
)
order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
//echo $sql_employee;die();
$result_employee = $db_link->query($sql_employee);        
if (!$result_employee) {
  debug_mail(false,'error sql',$sql_employee);die();
}

$employee_array=array();
while ($row_employee = $result_employee->fetch_assoc()) {
  if (isset($employee_array[$row_employee['crm_task_id']])==false) 
    $employee_array[$row_employee['crm_task_id']]=[];
  $row_employee['gks_nickname']=trim_gks($row_employee['gks_nickname']);
  if ($row_employee['gks_nickname']=='') 
    $row_employee['gks_nickname']='--';
  if (in_array($row_employee['gks_nickname'],$employee_array[$row_employee['crm_task_id']])==false) 
    $employee_array[$row_employee['crm_task_id']][]=$row_employee['gks_nickname'];
}


$sql_machine="SELECT gks_crm_tasks_machine.crm_task_id, crm_machine_name
FROM gks_crm_tasks_machine 
LEFT JOIN gks_crm_machine ON gks_crm_tasks_machine.crm_task_machine_id = gks_crm_machine.id_crm_machine
where gks_crm_tasks_machine.crm_task_id in (
  select id_crm_task
  from (
    ".$sql."
  ) as gksmytaskssel
)
ORDER BY crm_machine_name";
//echo $sql_machine;die();
$result_machine = $db_link->query($sql_machine);        
if (!$result_machine) {
  debug_mail(false,'error sql',$sql_machine);die();
}
$machine_array=array();
while ($row_machine = $result_machine->fetch_assoc()) {
  if (isset($machine_array[$row_machine['crm_task_id']])==false) 
    $machine_array[$row_machine['crm_task_id']]=[];
  $row_machine['crm_machine_name']=trim_gks($row_machine['crm_machine_name']);
  if ($row_machine['crm_machine_name']=='') 
    $row_machine['crm_machine_name']='--';
  if (in_array($row_machine['crm_machine_name'],$machine_array[$row_machine['crm_task_id']])==false) 
    $machine_array[$row_machine['crm_task_id']][]=$row_machine['crm_machine_name'];
}

$sql_brand="SELECT gks_crm_tasks_machine.crm_task_id, gks_eshop_products_brands.product_brand_descr
FROM (gks_crm_tasks_machine 
LEFT JOIN gks_crm_machine ON gks_crm_tasks_machine.crm_task_machine_id = gks_crm_machine.id_crm_machine) 
LEFT JOIN gks_eshop_products_brands ON gks_crm_machine.crm_machine_brand_id = gks_eshop_products_brands.id_product_brand
where gks_crm_tasks_machine.crm_task_id in (
  select id_crm_task
  from (
    ".$sql."
  ) as gksmytaskssel
)
and gks_crm_machine.crm_machine_brand_id>0
ORDER BY gks_eshop_products_brands.product_brand_descr";
//echo $sql_brand;die();
$result_brand = $db_link->query($sql_brand);        
if (!$result_brand) {
  debug_mail(false,'error sql',$sql_brand);die();
}
$brand_array=array();
while ($row_brand = $result_brand->fetch_assoc()) {
  if (isset($brand_array[$row_brand['crm_task_id']])==false) 
    $brand_array[$row_brand['crm_task_id']]=[];
  $row_brand['product_brand_descr']=trim_gks($row_brand['product_brand_descr']);
  if ($row_brand['product_brand_descr']=='') 
    $row_brand['product_brand_descr']='--';
  if (in_array($row_brand['product_brand_descr'],$brand_array[$row_brand['crm_task_id']])==false) 
    $brand_array[$row_brand['crm_task_id']][]=$row_brand['product_brand_descr'];
}
//echo '<pre>';print_r($brand_array);die();



$sql_product="SELECT gks_crm_tasks_machine.crm_task_id,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr<>'' THEN
        gks_eshop_products.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr
        END
    END
  ELSE gks_eshop_products.product_descr
END as product_descr_p

FROM ((gks_crm_tasks_machine 
LEFT JOIN gks_crm_machine ON gks_crm_tasks_machine.crm_task_machine_id = gks_crm_machine.id_crm_machine) 
LEFT JOIN gks_eshop_products ON gks_crm_machine.crm_machine_product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
where gks_crm_tasks_machine.crm_task_id in (
  select id_crm_task
  from (
    ".$sql."
  ) as gksmytaskssel
)
and gks_eshop_products.id_product>0
ORDER BY product_descr_p";
//echo $sql_machine;die();
$result_product = $db_link->query($sql_product);        
if (!$result_product) {
  debug_mail(false,'error sql',$sql_product);die();
}
$product_array=array();
while ($row_product = $result_product->fetch_assoc()) {
  if (isset($product_array[$row_product['crm_task_id']])==false) 
    $product_array[$row_product['crm_task_id']]=[];
  $row_product['product_descr_p']=trim_gks($row_product['product_descr_p']);
  if ($row_product['product_descr_p']=='') 
    $row_product['product_descr_p']='--';
  if (in_array($row_product['product_descr_p'],$product_array[$row_product['crm_task_id']])==false) 
    $product_array[$row_product['crm_task_id']][]=$row_product['product_descr_p'];
}


$dir_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';
if (file_exists($dir_path) == false) mkdir($dir_path); 
$file_path='crm_tasks_pivot1_'.$my_wp_user_id.'_'.rand(100000,999999).'.csv';
$mypath=$dir_path.$file_path;
if (file_exists($mypath)) unlink($mypath);

$myfile = fopen($mypath, "a");

$first_line='"'.gks_lang('Έτος').'","'.gks_lang('Μήνας').'","'.gks_lang('Ημερομηνία').'","'.gks_lang('Ημέρα').'","'.gks_lang('Ώρα').'","'.gks_lang('Διάρκεια (σε λεπτά)').'","'.gks_lang('Κατάσταση').'",';
$first_line.='"'.gks_lang('Εταιρεία').'","'.gks_lang('Υποκατάστημα').'","'.gks_lang('Επαφή').'","'.gks_lang('Πόλη').'","'.gks_lang('Νομός').'","'.gks_lang('Χώρα').'",';

$first_line.='"'.gks_lang('Εργασίες').'","'.gks_lang('Υπάλληλος').'","'.gks_lang('Συσκευή').'","'.gks_lang('Μάρκα').'","'.gks_lang('Προϊόν').'","'.gks_lang('Αναμενόμενα έσοδα').'",';
$first_line.='"'.gks_lang('Κανάλι πωλήσεων').'","'.gks_lang('Καμπάνια').'","'.gks_lang('Ανάθεση').'","'.gks_lang('Χρήστης').'",';





foreach ($gks_custom_header_fields as $value) {
  $first_line.='"'.$value.'",';
}  

$first_line=substr($first_line, 0, strlen($first_line)-1);
fwrite($myfile, $first_line."\n");



while ($row = $result->fetch_assoc()) {
  
  $id_crm_task=$row['id_crm_task'];
  
  $ctitle=$row['company_title']; 
  $csubtitle=gks_lang('Κεντρικό'); 
  if (isset($row['company_sub_title'])) $csubtitle=$row['company_sub_title'];
  $diarkeia=intval(strtotime($row['task_planned_date_to'])-strtotime($row['task_planned_date_from']))/60;
  $employee=''; if (isset($employee_array[$id_crm_task])) $employee=implode(', ',$employee_array[$id_crm_task]);
  $machine=''; if (isset($machine_array[$id_crm_task])) $machine=implode(', ',$machine_array[$id_crm_task]);
  $brand=''; if (isset($brand_array[$id_crm_task])) $brand=implode(', ',$brand_array[$id_crm_task]);
  $product=''; if (isset($product_array[$id_crm_task])) $product=implode(', ',$product_array[$id_crm_task]);
  
  $task_status_descr='--'; if (isset($tasks_status[$row['task_status_id']])) $task_status_descr=$tasks_status[$row['task_status_id']]['task_status_descr'];
  $line=
    showDate(strtotime($row['task_planned_date_from']), 'Y', 1).','.
    showDate(strtotime($row['task_planned_date_from']), 'm', 1).','.
    showDate(strtotime($row['task_planned_date_from']), 'd', 1).','.
    '"'.gks_csv_txt(getWeekDayName(date('w', strtotime($row['task_planned_date_from']) - GKS_ERP_START_VARDIA*60*60))).'",'.
    showDate(strtotime($row['task_planned_date_from']), 'H', 1).':00'.','.
    
    $diarkeia.','.

    '"'.gks_csv_txt($task_status_descr).'",'.
    '"'.gks_csv_txt($ctitle).'",'.
    '"'.gks_csv_txt($csubtitle).'",'.
    '"'.gks_csv_txt($row['gks_nickname']).'",'.
    '"'.gks_csv_txt($row['poli']).'",'.
    '"'.gks_csv_txt($row['nomos_descr']).'",'.
    '"'.gks_csv_txt($row['country_name']).'",'.

    '1,'.
    
    
    '"'.gks_csv_txt($employee).'",'.
    '"'.gks_csv_txt($machine).'",'.
    '"'.gks_csv_txt($brand).'",'.
    '"'.gks_csv_txt($product).'",'.
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


