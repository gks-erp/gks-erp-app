<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Αποθήκευση Ρυθμίσεων Εφαρμογής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}



$temp='';  if (isset($_POST['GKS_LANG_DEFAULT']))  $temp=trim_gks($_POST['GKS_LANG_DEFAULT']);
if ($temp=='') {
  debug_mail(false,'set GKS_LANG_DEFAULT','');
  $return = array('success' => false, 'message' => base64_encode('Select a language'));
  echo json_encode($return); die();}

if ($temp!='el-GR' and $temp!='en-US') $temp='en-US';


$sql="replace into gks_settings_users (user_id,myvalue,myobject,mysubobject) values (
".$my_wp_user_id.",'".$db_link->escape_string($temp)."','lang','backend')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
//echo $temp;die();

if ($temp!='el-GR')  {
  $rrr=gks_lang_data_swap('el-GR',$temp);
  if ($rrr['success']==false) {
    gks_cache_update_menu_version(-1);
    $return = array('success' => false, 'message' => base64_encode($rrr['message']));
    echo json_encode($return); die();
  }
  
  if ($temp=='en-US') {
    $mysqls=[
//"update gks_eshop_pricelist set pricelist_descr='Retail' where id_pricelist=1",
//"update gks_eshop_pricelist set pricelist_descr='Wholesale' where id_pricelist=2",
//"update gks_eshop_pricelist set pricelist_descr='Super Wholesale' where id_pricelist=3",
//"update gks_eshop_pricelist set pricelist_descr='Purchase' where id_pricelist=4",
    

"update gks_print_forms set print_form_descr='Documents & Orders el-GR' where id_print_form=1",
"update gks_print_forms set print_form_descr='Receipts - Payments el-GR' where id_print_form=3",
"update gks_print_forms set print_form_descr='Delivery note el-GR' where id_print_form=4",
"update gks_print_forms set print_form_descr='Documents & Orders en-US' where id_print_form=51",
"update gks_print_forms set print_form_descr='Receipts - Payments en-US' where id_print_form=53",
"update gks_print_forms set print_form_descr='Delivery note en-US' where id_print_form=54",
"update gks_print_forms set print_form_descr='Accommodation Tax Receipt el-GR' where id_print_form=100",
"update gks_print_forms set print_form_descr='Reservations el-GR' where id_print_form=2001",

"update gks_template_html set template_html_descr='Template OnLine Offer el-GR' where id_template_html=1",

"update gks_hotel set hotel_title='My Hotel',hotel_phone='00302310000000',hotel_odos='my street',hotel_perioxi='my area',hotel_poli='my city' where id_hotel=1",

"update gks_crm_channel_sale set crm_channel_sale_descr='Already a customer' where id_crm_channel_sale=1",
"update gks_crm_channel_sale set crm_channel_sale_descr='From an employee' where id_crm_channel_sale=2",
"update gks_crm_channel_sale set crm_channel_sale_descr='From an internal salesperson' where id_crm_channel_sale=3",
"update gks_crm_channel_sale set crm_channel_sale_descr='From an external salesperson' where id_crm_channel_sale=4",
"update gks_crm_channel_sale set crm_channel_sale_descr='From an existing customer' where id_crm_channel_sale=5",
"update gks_crm_channel_sale set crm_channel_sale_descr='From a friend' where id_crm_channel_sale=6",
"update gks_crm_channel_sale set crm_channel_sale_descr='Website' where id_crm_channel_sale=7",
"update gks_crm_channel_sale set crm_channel_sale_descr='Web search' where id_crm_channel_sale=8",
"update gks_crm_channel_sale set crm_channel_sale_descr='Facebook Page' where id_crm_channel_sale=9",
"update gks_crm_channel_sale set crm_channel_sale_descr='Facebook Ads' where id_crm_channel_sale=10",
"update gks_crm_channel_sale set crm_channel_sale_descr='Instagram Ads' where id_crm_channel_sale=12",
"update gks_crm_channel_sale set crm_channel_sale_descr='Google Ads' where id_crm_channel_sale=13",
"update gks_crm_channel_sale set crm_channel_sale_descr='Flyer distribution' where id_crm_channel_sale=16",
"update gks_crm_channel_sale set crm_channel_sale_descr='Local Guide' where id_crm_channel_sale=17",
"update gks_crm_channel_sale set crm_channel_sale_descr='Yellow Pages' where id_crm_channel_sale=18",
"update gks_crm_channel_sale set crm_channel_sale_descr='Agency' where id_crm_channel_sale=20",

"update gks_crm_leads_status set lead_status_descr='New' where id_crm_lead_status=1",
"update gks_crm_leads_status set lead_status_descr='Evaluation' where id_crm_lead_status=20",
"update gks_crm_leads_status set lead_status_descr='Proposal' where id_crm_lead_status=50",
"update gks_crm_leads_status set lead_status_descr='Won' where id_crm_lead_status=100",
"update gks_crm_leads_status set lead_status_descr='Lost' where id_crm_lead_status=200",

"update gks_crm_tasks_status set task_status_descr='Draft' where id_crm_task_status=1",
"update gks_crm_tasks_status set task_status_descr='To be done' where id_crm_task_status=20",
"update gks_crm_tasks_status set task_status_descr='Not to be done' where id_crm_task_status=30",
"update gks_crm_tasks_status set task_status_descr='In progress' where id_crm_task_status=50",
"update gks_crm_tasks_status set task_status_descr='Completed' where id_crm_task_status=100",
"update gks_crm_tasks_status set task_status_descr='Failure' where id_crm_task_status=200",

"update gks_assets_type set asset_type_descr='Camera' where id_asset_type=1",
"update gks_assets_type set asset_type_descr='Portable Printers' where id_asset_type=2",
"update gks_assets_type set asset_type_descr='Lenses' where id_asset_type=6",
"update gks_assets_type set asset_type_descr='Flashes' where id_asset_type=7",
"update gks_assets_type set asset_type_descr='Mini Lab Printers' where id_asset_type=9",
"update gks_assets_type set asset_type_descr='Monitors' where id_asset_type=12",
"update gks_assets_type set asset_type_descr='PCs' where id_asset_type=13",
"update gks_assets_type set asset_type_descr='Laptop' where id_asset_type=14",
"update gks_assets_type set asset_type_descr='Cash Registers' where id_asset_type=16",
"update gks_assets_type set asset_type_descr='Antennas' where id_asset_type=17",
"update gks_assets_type set asset_type_descr='Switch' where id_asset_type=18",
"update gks_assets_type set asset_type_descr='Access Point' where id_asset_type=19",
"update gks_assets_type set asset_type_descr='Routers' where id_asset_type=20",
"update gks_assets_type set asset_type_descr='IP Cameras' where id_asset_type=21",
"update gks_assets_type set asset_type_descr='CCTV Recorders' where id_asset_type=22",
"update gks_assets_type set asset_type_descr='Tablets' where id_asset_type=23",
"update gks_assets_type set asset_type_descr='Wireless POS' where id_asset_type=24",
"update gks_assets_type set asset_type_descr='Wired POS' where id_asset_type=25",
"update gks_assets_type set asset_type_descr='Vehicles' where id_asset_type=26",
"update gks_assets_type set asset_type_descr='Mobile' where id_asset_type=27",

"update gks_assets_service_reasons set reasons_descr='Oil Change' where id_assets_service_reasons=39",
"update gks_assets_service_reasons set reasons_descr='Small Service' where id_assets_service_reasons=40",
"update gks_assets_service_reasons set reasons_descr='Large Service' where id_assets_service_reasons=41",
"update gks_assets_service_reasons set reasons_descr='Tire Change' where id_assets_service_reasons=42",
"update gks_assets_service_reasons set reasons_descr='Immobilization' where id_assets_service_reasons=43",
"update gks_assets_service_reasons set reasons_descr='VTCC' where id_assets_service_reasons=44",
"update gks_assets_service_reasons set reasons_descr='Insurance Renewal' where id_assets_service_reasons=45",
"update gks_assets_service_reasons set reasons_descr='Transfer' where id_assets_service_reasons=46",



    ];
        
    foreach ($mysqls as $sql) {
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $rrr['error_sqls'][]=$sql;
      }      
    }
    
    //echo '<pre>ssss'.$temp;die();
    
  }
  
}

$sql="update gks_lang set lang_on_backend=0";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $rrr['error_sqls'][]=$sql;
}
$sql="update gks_lang set lang_on_backend=1 where id_lang='".$db_link->escape_string($temp)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $rrr['error_sqls'][]=$sql;
}


$sql="replace into gks_settings (mykey,myvalue) values ('GKS_LANG_DEFAULT','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $rrr['error_sqls'][]=$sql;
}

gks_cache_update_menu_version(-1);


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
