<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();
//die();

//$dev_page_starttime11=microtime(true);

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Παγίου id').':' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_assets');

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $old_megeftpos_pos_id='';
} else {
  

  $sql_rec="SELECT gks_assets.*, gks_warehouses.warehouse_name, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_company.company_title, gks_banks.bank_descr, gks_assets_type.asset_type_descr
  FROM ((((gks_assets 
  LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company) 
  LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank) 
  LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type
  where gks_assets.id_asset = ".$id;
  
  $sql_rec.=" limit 1";
  $result = $db_link->query($sql_rec);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_rec);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_old = $result->fetch_assoc();
  $old_megeftpos_pos_id=trim_gks($row_old['megeftpos_pos_id']);
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_assets',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
}


  
$asset_code=''; if (isset($_POST['asset_code'])) $asset_code=trim_gks(base64_decode($_POST['asset_code']));
$asset_title=''; if (isset($_POST['asset_title'])) $asset_title=trim_gks(base64_decode($_POST['asset_title']));
$asset_serialnumber=''; if (isset($_POST['asset_serialnumber'])) $asset_serialnumber=trim_gks(base64_decode($_POST['asset_serialnumber']));
$asset_sxolio=''; if (isset($_POST['asset_sxolio'])) $asset_sxolio=trim_gks(base64_decode($_POST['asset_sxolio']));
$asset_type=0; if (isset($_POST['asset_type'])) $asset_type=intval($_POST['asset_type']);
$is_fotografou=0; if (isset($_POST['is_fotografou'])) $is_fotografou=intval($_POST['is_fotografou']);
$asset_disable=0; if (isset($_POST['asset_disable'])) $asset_disable=intval($_POST['asset_disable']);

$asset_date_activate=trim_gks(base64_decode($_POST['asset_date_activate']));
$asset_date_aposirsi=trim_gks(base64_decode($_POST['asset_date_aposirsi']));

if ($asset_type==24 or $asset_type==25) {
  $bank_id=0; if (isset($_POST['bank_id'])) $bank_id=intval($_POST['bank_id']);
  $xreosi_val=0; if (isset($_POST['xreosi_val'])) $xreosi_val=floatval(str_replace(',', '.', str_replace('.', '', $_POST['xreosi_val'])));
  $xreosi_type=0; if (isset($_POST['xreosi_type'])) $xreosi_type=intval($_POST['xreosi_type']);
} else {
  $bank_id=0;
  $xreosi_val=0;
  $xreosi_type=0;
}


$oxima_elastika=''; if (isset($_POST['oxima_elastika'])) $oxima_elastika=trim_gks(base64_decode($_POST['oxima_elastika']));

$oxima_km=0; if (isset($_POST['oxima_km'])) $oxima_km=trim_gks($_POST['oxima_km']);
$oxima_km=str_replace('.', '', $oxima_km);
$oxima_km=str_replace(',', '', $oxima_km);
$oxima_km=intval($oxima_km);

$oxima_next_service_km=0; if (isset($_POST['oxima_next_service_km'])) $oxima_next_service_km=trim_gks($_POST['oxima_next_service_km']);
$oxima_next_service_km=str_replace('.', '', $oxima_next_service_km);
$oxima_next_service_km=str_replace(',', '', $oxima_next_service_km);
$oxima_next_service_km=intval($oxima_next_service_km);

$oxima_next_kteo=''; if (isset($_POST['oxima_next_kteo'])) $oxima_next_kteo=trim_gks(base64_decode($_POST['oxima_next_kteo']));
$oxima_liji_asfaleia=''; if (isset($_POST['oxima_liji_asfaleia'])) $oxima_liji_asfaleia=trim_gks(base64_decode($_POST['oxima_liji_asfaleia']));

$mixani_esn='0'; if (isset($_POST['mixani_esn'])) $mixani_esn=trim_gks($_POST['mixani_esn']);
$mixani_esn=str_replace('.', '', $mixani_esn);
$mixani_esn=str_replace(',', '', $mixani_esn);
$mixani_esn=intval($mixani_esn);



$asset_thesi=''; if (isset($_POST['asset_thesi'])) $asset_thesi=trim_gks(base64_decode($_POST['asset_thesi']));
$mac_address=''; if (isset($_POST['mac_address'])) $mac_address=trim_gks(base64_decode($_POST['mac_address']));
$viva_company_id=0; if (isset($_POST['viva_company_id'])) $viva_company_id=intval($_POST['viva_company_id']);
$viva_terminal_id=''; if (isset($_POST['viva_terminal_id'])) $viva_terminal_id=trim_gks(base64_decode($_POST['viva_terminal_id']));
$viva_terminal_code=''; if (isset($_POST['viva_terminal_code'])) $viva_terminal_code=trim_gks(base64_decode($_POST['viva_terminal_code']));
$viva_action_after=0; if (isset($_POST['viva_action_after'])) $viva_action_after=intval($_POST['viva_action_after']);
if ($viva_action_after<0 or $viva_action_after>6) $viva_action_after=0;
$viva_def_ref_pliromis=''; if (isset($_POST['viva_def_ref_pliromis'])) $viva_def_ref_pliromis=trim_gks(base64_decode($_POST['viva_def_ref_pliromis']));

$megeftpos_company_id=0; if (isset($_POST['megeftpos_company_id'])) $megeftpos_company_id=intval($_POST['megeftpos_company_id']);
$megeftpos_terminal_id=''; if (isset($_POST['megeftpos_terminal_id'])) $megeftpos_terminal_id=trim_gks(base64_decode($_POST['megeftpos_terminal_id']));
$megeftpos_static_ip=''; if (isset($_POST['megeftpos_static_ip'])) $megeftpos_static_ip=trim_gks(base64_decode($_POST['megeftpos_static_ip']));
$megeftpos_port=0; if (isset($_POST['megeftpos_port'])) $megeftpos_port=intval($_POST['megeftpos_port']);
$megeftpos_protocol=0; if (isset($_POST['megeftpos_protocol'])) $megeftpos_protocol=intval($_POST['megeftpos_protocol']);
$megeftpos_erp_app_id=0; if (isset($_POST['megeftpos_erp_app_id'])) $megeftpos_erp_app_id=intval($_POST['megeftpos_erp_app_id']);
$megeftpos_api_key=''; if (isset($_POST['megeftpos_api_key'])) $megeftpos_api_key=trim_gks(base64_decode($_POST['megeftpos_api_key']));

$mellon_company_id=0; if (isset($_POST['mellon_company_id'])) $mellon_company_id=intval($_POST['mellon_company_id']);
$mellon_id=''; if (isset($_POST['mellon_id'])) $mellon_id=trim_gks(base64_decode($_POST['mellon_id']));
$mellon_terminal_id=''; if (isset($_POST['mellon_terminal_id'])) $mellon_terminal_id=trim_gks(base64_decode($_POST['mellon_terminal_id']));

$cardlink_company_id=0; if (isset($_POST['cardlink_company_id'])) $cardlink_company_id=intval($_POST['cardlink_company_id']);
$cardlink_terminal_id=''; if (isset($_POST['cardlink_terminal_id'])) $cardlink_terminal_id=trim_gks(base64_decode($_POST['cardlink_terminal_id']));
$cardlink_static_ip=''; if (isset($_POST['cardlink_static_ip'])) $cardlink_static_ip=trim_gks(base64_decode($_POST['cardlink_static_ip']));
$cardlink_port=0; if (isset($_POST['cardlink_port'])) $cardlink_port=intval($_POST['cardlink_port']);
$cardlink_ecr2eftweb_erp_app_id=0; if (isset($_POST['cardlink_ecr2eftweb_erp_app_id'])) $cardlink_ecr2eftweb_erp_app_id=intval($_POST['cardlink_ecr2eftweb_erp_app_id']);
$cardlink_ecr2eftweb_service_url=''; if (isset($_POST['cardlink_ecr2eftweb_service_url'])) $cardlink_ecr2eftweb_service_url=trim_gks(base64_decode($_POST['cardlink_ecr2eftweb_service_url']));

$epay_company_id=0; if (isset($_POST['epay_company_id'])) $epay_company_id=intval($_POST['epay_company_id']);
$epay_id=''; if (isset($_POST['epay_id'])) $epay_id=trim_gks(base64_decode($_POST['epay_id']));
$epay_terminal_id=''; if (isset($_POST['epay_terminal_id'])) $epay_terminal_id=trim_gks(base64_decode($_POST['epay_terminal_id']));

$worldline_company_id=0; if (isset($_POST['worldline_company_id'])) $worldline_company_id=intval($_POST['worldline_company_id']);
$worldline_id=''; if (isset($_POST['worldline_id'])) $worldline_id=trim_gks(base64_decode($_POST['worldline_id']));
$worldline_terminal_id=''; if (isset($_POST['worldline_terminal_id'])) $worldline_terminal_id=trim_gks(base64_decode($_POST['worldline_terminal_id']));

$nexi_company_id=0; if (isset($_POST['nexi_company_id'])) $nexi_company_id=intval($_POST['nexi_company_id']);
$nexi_id=''; if (isset($_POST['nexi_id'])) $nexi_id=trim_gks(base64_decode($_POST['nexi_id']));
$nexi_terminal_id=''; if (isset($_POST['nexi_terminal_id'])) $nexi_terminal_id=trim_gks(base64_decode($_POST['nexi_terminal_id']));

//echo '<pre>'.$cardlink_ecr2eftweb_erp_app_id;die();


if (!($asset_type==1)) $mixani_esn=0;

if (!($asset_type==24 or $asset_type==25)) { //pos
  $bank_id=0;
  $xreosi_val=0;
  $xreosi_type=0;  
}

if ($asset_type!=26) { // not oximata
  $oxima_elastika='';
  $oxima_km=0;
  $oxima_next_service_km=0;
  $oxima_next_kteo='';
  $oxima_liji_asfaleia='';
}

if (!($asset_type==13)) { // PCs
  $asset_thesi='';
}
if (!($asset_type==13 or $asset_type==14)) { // Pcs, Laptop
  $mac_address='';
}
if (!($asset_type==23 or $asset_type==24 or $asset_type==25 or $asset_type==27)) { // Tablets, POS Wireless, POS wired
  $viva_company_id=0;
  $viva_terminal_id='';
  $viva_terminal_code='';
  $viva_action_after=0;
  $viva_def_ref_pliromis='';
  
  $megeftpos_company_id=0;
  $megeftpos_terminal_id='';
  $megeftpos_static_ip='';
  $megeftpos_port=0;
  $megeftpos_protocol=0;
  $megeftpos_erp_app_id=0;
  $megeftpos_api_key='';
  
  $mellon_company_id=0;
  $mellon_id='';
  $mellon_terminal_id='';
  
  $cardlink_company_id=0;
  $cardlink_terminal_id='';
  $cardlink_static_ip='';
  $cardlink_port=0;
  $cardlink_ecr2eftweb_erp_app_id=0;
  $cardlink_ecr2eftweb_service_url='';
  
  $epay_company_id=0;
  $epay_id='';
  $epay_terminal_id='';
  
  $worldline_company_id=0;
  $worldline_id='';
  $worldline_terminal_id='';
  
  $nexi_company_id=0;
  $nexi_id='';
  $nexi_terminal_id='';
  
}





$form_asset_photo=trim_gks(stripslashes(urldecode($_POST['form_asset_photo'])));


if (strlen($asset_code)<=0 ) {debug_mail(false,'empty code','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο κωδικός δεν μπορεί να είναι κενός')));
  echo json_encode($return); die();}



$sql="select id_asset from gks_assets where asset_code='".$db_link->escape_string($asset_code)."' and id_asset <> ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {debug_mail(false,'code exist',$asset_code);
  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$asset_code,gks_lang('Ο κωδικός [1] υπάρχει ήδη σε άλλο πάγιο'))));
  echo json_encode($return); die();}  

if ($asset_serialnumber<>'') {
  $sql="select id_asset from gks_assets where asset_serialnumber='".$db_link->escape_string($asset_serialnumber)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'serial number exist',$asset_serialnumber);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$asset_serialnumber,gks_lang('Το serial number [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}



if ($asset_title=='') {debug_mail(false,'empty name');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die();}




$asset_date_activate_time=0;
if (strlen($asset_date_activate) >= 2 and substr($asset_date_activate,0,2) =='__') $asset_date_activate='';
if ($asset_date_activate != '') {
  $limit_asset_date_activate=time(); // tha prepei na einai toylaxiston simera
  $asset_date_activate_time = gks_myFormatDate($asset_date_activate);
  //echo '<pre>'.base64_decode($_POST['asset_date_activate']).'|'.date('Y-m-d H:i:s',$asset_date_activate_time).'|'.date('Y-m-d H:i:s',$limit_asset_date_activate);die();
  if ($asset_date_activate_time > $limit_asset_date_activate) {
    debug_mail(false,'asset_date_activate',$asset_date_activate);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Ενεργοποίησης είναι στο μέλλον')));
    echo json_encode($return); die(); 
  }
  $asset_date_activate = "'".date('Y-m-d',gks_myFormatDate($asset_date_activate))."'";
} else {
  $asset_date_activate = 'null';
}

$asset_date_aposirsi_time=0;
if (strlen($asset_date_aposirsi) >= 2 and substr($asset_date_aposirsi,0,2) =='__') $asset_date_aposirsi='';
if ($asset_date_aposirsi != '') {
  $limit_asset_date_aposirsi=time(); // tha prepei na einai toylaxiston 15 eton
  $asset_date_aposirsi_time=gks_myFormatDate($asset_date_aposirsi);

  $asset_date_aposirsi = "'".date('Y-m-d',gks_myFormatDate($asset_date_aposirsi))."'";
} else {
  $asset_date_aposirsi = 'null';
}

    

if ($asset_date_activate_time > 0 and $asset_date_aposirsi_time > 0) {
  if ($asset_date_activate_time > $asset_date_aposirsi_time) {
    debug_mail(false,'emptyl', 'asset_date_activate <  asset_date_aposirsi '. $asset_date_activate.' '.$asset_date_aposirsi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Απόσυρσης δεν μπορεί να είναι μεγαλύτερη από την Ημερομηνία Ενεργοποίησης')));
    echo json_encode($return); die();     
  }
}


$oxima_next_kteo_time=0;
if (strlen($oxima_next_kteo) >= 2 and substr($oxima_next_kteo,0,2) =='__') $oxima_next_kteo='';
if ($oxima_next_kteo != '') {
  $limit_oxima_next_kteo=time(); // tha prepei na einai toylaxiston 15 eton
  $oxima_next_kteo_time=gks_myFormatDate($oxima_next_kteo);

  $oxima_next_kteo = "'".date('Y-m-d',gks_myFormatDate($oxima_next_kteo))."'";
} else {
  $oxima_next_kteo = 'null';
}

$oxima_liji_asfaleia_time=0;
if (strlen($oxima_liji_asfaleia) >= 2 and substr($oxima_liji_asfaleia,0,2) =='__') $oxima_liji_asfaleia='';
if ($oxima_liji_asfaleia != '') {
  $limit_oxima_liji_asfaleia=time(); // tha prepei na einai toylaxiston 15 eton
  $oxima_liji_asfaleia_time=gks_myFormatDate($oxima_liji_asfaleia);

  $oxima_liji_asfaleia = "'".date('Y-m-d',gks_myFormatDate($oxima_liji_asfaleia))."'";
} else {
  $oxima_liji_asfaleia = 'null';
}


if ($mac_address!='') {
  $mac_parts=explode(',',$mac_address);
  foreach ($mac_parts as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      $sql="select * from gks_assets where mac_address like '%".$db_link->escape_string($value)."%' and id_asset<>".$id;
      
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        
        $tmpmsg=gks_lang('Η Mac Address <b>[1]</b> υπάρχει ήδη στο πάγιο');
        $tmpmsg=str_replace('[1]'.$value,$tmpmsg);
        $tmpmsg.='<br>';
        $tmpmsg.='<a href="admin-assets-item.php?id='.$row['id_asset'].'" target="_blank"><b>'.$row['asset_code'].' '. $row['asset_title'].'</b></a>';
        debug_mail(false,'mac address exist '.$value,$tmpmsg);
        $return = array('success' => false, 'message' => base64_encode($tmpmsg));
        echo json_encode($return); die();  
      }  
  
    }
  }
  
}



if ($viva_terminal_id<>'') {
  $sql="select id_asset from gks_assets where viva_terminal_id like '".$db_link->escape_string($viva_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Viva Terminal ID exist',$viva_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$viva_terminal_id,gks_lang('Το Viva Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}

if ($viva_terminal_code<>'') {
  $sql="select id_asset from gks_assets where viva_terminal_code like '".$db_link->escape_string($viva_terminal_code)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Virtual Viva Terminal ID exist',$viva_terminal_code);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$viva_terminal_code,gks_lang('Το Virtual Viva Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}

if ($megeftpos_port<0 or $megeftpos_port>65530) $megeftpos_port=0;
if ($megeftpos_protocol <0 or $megeftpos_protocol>11) $megeftpos_protocol=0;
if ($megeftpos_terminal_id<>'') {
  if ($megeftpos_protocol==0) {
    $megeftpos_static_ip='';
    $megeftpos_port=0;
    $megeftpos_api_key='';
  }
  if (in_array($megeftpos_protocol,[1,2])) { //EDPS_JSON,CARDLINK_DLL
    $megeftpos_api_key='';
  }
  if (in_array($megeftpos_protocol,[3,4,8])) { //MELLON_WEB_ECR,EPAY_WEB_ECR,WORLDLINE_WEB_ECR
    $megeftpos_static_ip='';
    $megeftpos_port=0;
  }
  
  
  $sql="select id_asset from gks_assets where megeftpos_terminal_id like '".$db_link->escape_string($megeftpos_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Meg EFT/POS Driver Terminal ID exist',$megeftpos_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$megeftpos_terminal_id,gks_lang('Το Meg EFT/POS Driver Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}  
    
  if (in_array($megeftpos_protocol,[1,2]) and $megeftpos_static_ip=='') {
    debug_mail(false,'empty Meg EFT/POS Driver Static IP');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('H Meg EFT/POS Driver Static IP ΔΕΝ μπορεί να είναι κενή')));
    echo json_encode($return); die();}
  if (in_array($megeftpos_protocol,[1,2]) and ($megeftpos_port<1 or $megeftpos_port>65530)) {
    debug_mail(false,'empty Meg EFT/POS Driver Port 1 to 65530');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('H Meg EFT/POS Driver Port πρέπει να είναι από 1 έως 65530')));
    echo json_encode($return); die();}
  if (in_array($megeftpos_protocol,[3,4,8]) and $megeftpos_api_key=='') {
    debug_mail(false,'empty Meg EFT/POS Driver Api Key');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Meg EFT/POS Driver Api Key ΔΕΝ μπορεί να είναι κενό')));
    echo json_encode($return); die();}
  if ($megeftpos_erp_app_id==0) {
    debug_mail(false,'empty Meg EFT/POS Driver gks ERP App Desktop');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('To Meg EFT/POS Driver gks ERP App Desktop ΔΕΝ μπορεί να είναι κενό')));
    echo json_encode($return); die();}
     
}


if ($mellon_id<>'') {
  $sql="select id_asset from gks_assets where mellon_id like '".$db_link->escape_string($mellon_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Mellon ID exist',$mellon_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$mellon_id,gks_lang('Το Mellon ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}
if ($mellon_terminal_id<>'') {
  $sql="select id_asset from gks_assets where mellon_terminal_id like '".$db_link->escape_string($mellon_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Mellon Terminal ID exist',$mellon_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$mellon_terminal_id,gks_lang('Το Mellon Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}


if ($cardlink_port<0 or $cardlink_port>65530) $cardlink_port=0;
if ($cardlink_terminal_id<>'') {
  $sql="select id_asset from gks_assets where cardlink_terminal_id like '".$db_link->escape_string($cardlink_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'Cardlink Terminal ID exist',$cardlink_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$cardlink_terminal_id,gks_lang('Το Cardlink Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}  
    
  if ($cardlink_static_ip=='') {
    debug_mail(false,'empty Cardlink Static IP');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('H Cardlink Static IP ΔΕΝ μπορεί να είναι κενή')));
    echo json_encode($return); die();}
  if ($cardlink_port<1 or $cardlink_port>65530) {debug_mail(false,'Cardlink Port 1 to 65530',$cardlink_port);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('H Cardlink Port πρέπει να είναι από 1 έως 65530')));
    echo json_encode($return); die();}
  if ($cardlink_ecr2eftweb_erp_app_id==0) {
    debug_mail(false,'empty Cardlink gks ERP App Desktop');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('To Cardlink gks ERP App Desktop ΔΕΝ μπορεί να είναι κενό')));
    echo json_encode($return); die();}
  if ($cardlink_ecr2eftweb_service_url=='') {
    debug_mail(false,'empty Cardlink Ecr2EftWEB Service Url');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('To Cardlink Ecr2EftWEB Service Url ΔΕΝ μπορεί να είναι κενό')));
    echo json_encode($return); die();}
      
}


if ($epay_id<>'') {
  $sql="select id_asset from gks_assets where epay_id like '".$db_link->escape_string($epay_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'epay ID exist',$epay_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$epay_id,gks_lang('Το ePay ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}
if ($epay_terminal_id<>'') {
  $sql="select id_asset from gks_assets where epay_terminal_id like '".$db_link->escape_string($epay_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'epay Terminal ID',$epay_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$epay_terminal_id,gks_lang('Το ePay Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}


if ($worldline_id<>'') {
  $sql="select id_asset from gks_assets where worldline_id like '".$db_link->escape_string($worldline_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'worldline Mobile device exist',$worldline_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$worldline_id,gks_lang('Το Worldline Mobile device [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();} 
}
if ($worldline_terminal_id<>'') {
  $sql="select id_asset from gks_assets where worldline_terminal_id like '".$db_link->escape_string($worldline_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'worldline Terminal ID exist',$worldline_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$worldline_terminal_id,gks_lang('Το Worldline Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}

if ($nexi_id<>'') {
  $sql="select id_asset from gks_assets where nexi_id like '".$db_link->escape_string($nexi_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'nexi ID exist',$nexi_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$nexi_id,gks_lang('Το Nexi ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}
if ($nexi_terminal_id<>'') {
  $sql="select id_asset from gks_assets where nexi_terminal_id like '".$db_link->escape_string($nexi_terminal_id)."' and id_asset <> ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {debug_mail(false,'nexi Terminal ID exist',$nexi_terminal_id);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$nexi_terminal_id,gks_lang('Το Nexi Terminal ID [1] υπάρχει ήδη σε άλλο πάγιο'))));
    echo json_encode($return); die();}    
}



$redirect='';
if ($id==-1) {
  $myguid=guid_for_megeftpos_pos_id();
  $old_megeftpos_pos_id=$myguid;
  $sql="insert into gks_assets (mydate_add,user_id_add,myip,megeftpos_pos_id) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."','".$myguid."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  

  $redirect=base64_encode('admin-assets-item.php?id='.$id);   
}




$sql="update gks_assets set 
asset_title='".$db_link->escape_string($asset_title)."',
asset_code='".$db_link->escape_string($asset_code)."',
asset_serialnumber='".$db_link->escape_string($asset_serialnumber)."',
asset_sxolio='".$db_link->escape_string($asset_sxolio)."',
asset_date_activate = ".$asset_date_activate.",
asset_date_aposirsi = ".$asset_date_aposirsi.",
asset_type=".$asset_type.",
is_fotografou=".$is_fotografou.",
asset_disable=".$asset_disable.",
bank_id=".$bank_id.",
xreosi_val=".number_format($xreosi_val, 2, '.', '').",
xreosi_type=".$xreosi_type.",
oxima_elastika='".$db_link->escape_string($oxima_elastika)."',
oxima_km=".$oxima_km.",
oxima_km_date=now(),
oxima_next_service_km=".$oxima_next_service_km.",
oxima_next_kteo=".$oxima_next_kteo.",
oxima_liji_asfaleia=".$oxima_liji_asfaleia.",
asset_thesi='".$db_link->escape_string($asset_thesi)."',
mac_address='".$db_link->escape_string($mac_address)."',
mixani_esn=".$mixani_esn.",

asset_photo='".$db_link->escape_string($form_asset_photo)."',

viva_company_id=".$viva_company_id.",
viva_terminal_id='".$db_link->escape_string($viva_terminal_id)."',
viva_terminal_code='".$db_link->escape_string($viva_terminal_code)."',
viva_action_after=".$viva_action_after.",
viva_def_ref_pliromis='".$db_link->escape_string($viva_def_ref_pliromis)."',

megeftpos_company_id=".$megeftpos_company_id.",
megeftpos_terminal_id='".$db_link->escape_string($megeftpos_terminal_id)."',
megeftpos_static_ip='".$db_link->escape_string($megeftpos_static_ip)."',
megeftpos_port=".$megeftpos_port.",
megeftpos_protocol=".$megeftpos_protocol.",
megeftpos_erp_app_id=".$megeftpos_erp_app_id.",
megeftpos_api_key='".$db_link->escape_string($megeftpos_api_key)."',
";
if ($old_megeftpos_pos_id=='') {
  $myguid=guid_for_megeftpos_pos_id();
  $sql.="megeftpos_pos_id='".$db_link->escape_string($myguid)."',";
}
$sql.="
mellon_company_id=".$mellon_company_id.",
mellon_id='".$db_link->escape_string($mellon_id)."',
mellon_terminal_id='".$db_link->escape_string($mellon_terminal_id)."',

cardlink_company_id=".$cardlink_company_id.",
cardlink_terminal_id='".$db_link->escape_string($cardlink_terminal_id)."',
cardlink_static_ip='".$db_link->escape_string($cardlink_static_ip)."',
cardlink_port=".$cardlink_port.",
cardlink_ecr2eftweb_erp_app_id=".$cardlink_ecr2eftweb_erp_app_id.",
cardlink_ecr2eftweb_service_url='".$db_link->escape_string($cardlink_ecr2eftweb_service_url)."',

epay_company_id=".$epay_company_id.",
epay_id='".$db_link->escape_string($epay_id)."',
epay_terminal_id='".$db_link->escape_string($epay_terminal_id)."',

worldline_company_id=".$worldline_company_id.",
worldline_id='".$db_link->escape_string($worldline_id)."',
worldline_terminal_id='".$db_link->escape_string($worldline_terminal_id)."',

nexi_company_id=".$nexi_company_id.",
nexi_id='".$db_link->escape_string($nexi_id)."',
nexi_terminal_id='".$db_link->escape_string($nexi_terminal_id)."',

";



$sql.=" mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'

where id_asset = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_run=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_assets',$id);



$mytimenow=time();
$time_vardia=_time_user($mytimenow, 1);

$time_vardia-= GKS_ERP_START_VARDIA*60*60;

$today_vardia = date('Y-m-d',$time_vardia);


$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia_midle=$today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

    


    



if ($is_new_rec) {


  $sxolio_log =gks_lang('Προσθήκη από backend');
  $sql="insert into gks_assets_log (asset_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sxolio_log);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }  
  
    
} else {
  $result = $db_link->query($sql_rec);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_rec);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_new = $result->fetch_assoc();
  
  $sxolio_log=''; 
  
  
  if ($row_old['asset_code'] != $row_new['asset_code'])
    $sxolio_log.=gks_lang('Κωδικός').':<br><b>'.$row_old['asset_code'].'</b> -&gt; <b>'.$row_new['asset_code'].'</b>'.'<br>';
  
  if ($row_old['asset_title'] != $row_new['asset_title'])
    $sxolio_log.=gks_lang('Περιγραφή').':<br><b>'.$row_old['asset_title'].'</b> -&gt; <b>'.$row_new['asset_title'].'</b>'.'<br>';
  
  if ($row_old['asset_type_descr'] != $row_new['asset_type_descr'])
    $sxolio_log.=gks_lang('Τύπος').':<br><b>'.$row_old['asset_type_descr'].'</b> -&gt; <b>'.$row_new['asset_type_descr'].'</b>'.'<br>';
  
  if ($row_old['asset_serialnumber'] != $row_new['asset_serialnumber'])
    $sxolio_log.=($row_new['asset_type']==26 ? gks_lang('Αριθμός Πλαισίου') : gks_lang('Serial Number')).':<br><b>'.$row_old['asset_serialnumber'].'</b> -&gt; <b>'.$row_new['asset_serialnumber'].'</b>'.'<br>';
  
  if ($row_old['asset_date_activate'] != $row_new['asset_date_activate']) 
    $sxolio_log.=gks_lang('Ημερομηνία Ενεργοποίησης').':<br><b>'.(isset($row_old['asset_date_activate']) ? showDate(strtotime($row_old['asset_date_activate']), 'd/m/Y', 1) : '').'</b> -&gt; '.
    '<b>'.(isset($row_new['asset_date_activate']) ? showDate(strtotime($row_new['asset_date_activate']), 'd/m/Y', 1) : '').'</b>'.'<br>';
  
  if ($row_old['asset_date_aposirsi'] != $row_new['asset_date_aposirsi']) 
    $sxolio_log.=gks_lang('Ημερομηνία Απόσυρσης').':<br><b>'.(isset($row_old['asset_date_aposirsi']) ? showDate(strtotime($row_old['asset_date_aposirsi']), 'd/m/Y', 1) : '').'</b> -&gt; '.
    '<b>'.(isset($row_new['asset_date_aposirsi']) ? showDate(strtotime($row_new['asset_date_aposirsi']), 'd/m/Y', 1) : '').'</b>'.'<br>';
  
  if ($row_old['asset_sxolio'] != $row_new['asset_sxolio'])
    $sxolio_log.=gks_lang('Σχόλιο').':<br><b>'.($row_old['asset_sxolio']!='' ? nl2br_gks($row_old['asset_sxolio']).'<br>' : '').'</b> -&gt; <br>'.
    '<b>'.($row_new['asset_sxolio']!= '' ? nl2br_gks($row_new['asset_sxolio']).'<br>' : '').'</b>'.'<br>';
  
  if ($row_old['asset_disable'] != $row_new['asset_disable'])
    $sxolio_log.=gks_lang('Ανενεργό').':<br><b>'.($row_old['asset_disable']==0 ? 'Όχι':'Ναι').'</b> -&gt; <b>'.($row_new['asset_disable']==0 ? 'Όχι':'Ναι').'</b>'.'<br>';
  
  if ($row_old['is_fotografou'] != $row_new['is_fotografou'])
    $sxolio_log.=gks_lang('Είναι του συνεργάτη').':<br><b>'.($row_old['is_fotografou']==0 ? 'Όχι':'Ναι').'</b> -&gt; <b>'.($row_new['is_fotografou']==0 ? 'Όχι':'Ναι').'</b>'.'<br>';
  
  if ($row_old['bank_descr'] != $row_new['bank_descr'])
    $sxolio_log.=gks_lang('Τράπεζα').':<br><b>'.$row_old['bank_descr'].'</b> -&gt; <b>'.$row_new['bank_descr'].'</b>'.'<br>';
  
  if ($row_old['xreosi_val'] != $row_new['xreosi_val'])
    $sxolio_log.=gks_lang('Χρέωση').':<br><b>'.$row_old['xreosi_val'].'</b> -&gt; <b>'.$row_new['xreosi_val'].'</b>'.'<br>';
  
  $old_xreosi_type='';
  if ($row_old['xreosi_type']==1) $old_xreosi_type=gks_lang('ΔΩΡΕΑΝ');
  else if ($row_old['xreosi_type']==2) $old_xreosi_type=gks_lang('ΑΝΑ ΜΗΝΑ/ ΠΛΕΟΝ ΦΠΑ');
  $new_xreosi_type='';
  if ($row_new['xreosi_type']==1) $new_xreosi_type=gks_lang('ΔΩΡΕΑΝ');
  else if ($row_new['xreosi_type']==2) $new_xreosi_type=gks_lang('ΑΝΑ ΜΗΝΑ/ ΠΛΕΟΝ ΦΠΑ');
  if ($row_old['xreosi_type'] != $row_new['xreosi_type'])
    $sxolio_log.=gks_lang('Τύπος χρέωσης').':<br><b>'.$old_xreosi_type.'</b> -&gt; <b>'.$new_xreosi_type.'</b>'.'<br>';
  
  
  
  if ($row_old['mixani_esn'] != $row_new['mixani_esn']) {
    $sxolio_log.=gks_lang('Clicks τώρα').':<br><b>'.$row_old['mixani_esn'].'</b> -&gt; <b>'.$row_new['mixani_esn'].'</b>'.'<br>';
    
    if ($asset_type==1) { // mixani
      $sql="insert into gks_assets_mixani_esn (
      asset_id,mydateadd,user_id_add,esn,myip
      ) values (
      ".$id.",
      now(),
      ".$my_wp_user_id.",
      ".$row_new['mixani_esn'].",
      '".$db_link->escape_string($gkIP)."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sxolio_log);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }    
    }
  }
  
  if ($row_old['oxima_elastika'] != $row_new['oxima_elastika'])
    $sxolio_log.=gks_lang('Ελαστικά').':<br><b>'.$row_old['oxima_elastika'].'</b> -&gt; <b>'.$row_new['oxima_elastika'].'</b>'.'<br>';
    
    
    
  
  if ($row_old['oxima_km'] != $row_new['oxima_km']) {
    $sxolio_log.=gks_lang('Χιλιόμετρα τώρα').':<br><b>'.$row_old['oxima_km'].'</b> -&gt; <b>'.$row_new['oxima_km'].'</b>'.'<br>';
    
    if ($asset_type==26) { // oximata
      $sql="insert into gks_assets_oximata_km (
      asset_id,mydateadd,user_id_add,km,myip
      ) values (
      ".$id.",
      now(),
      ".$my_wp_user_id.",
      ".$row_new['oxima_km'].",
      '".$db_link->escape_string($gkIP)."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sxolio_log);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }    
    }
  }
  
  
  if ($row_old['oxima_next_service_km'] != $row_new['oxima_next_service_km'])
    $sxolio_log.=gks_lang('Επόμενο Service σε Km').':<br><b>'.number_format($row_old['oxima_next_service_km'],0,'','.').'</b> -&gt; <b>'.number_format($row_new['oxima_next_service_km'],0,'','.').'</b>'.'<br>';
  
  if ($row_old['oxima_next_kteo'] != $row_new['oxima_next_kteo']) 
    $sxolio_log.=gks_lang('Ημερομηνία Επόμενου ΚΤΕΟ').':<br><b>'.(isset($row_old['oxima_next_kteo']) ? showDate(strtotime($row_old['oxima_next_kteo']), 'd/m/Y', 1) : '').'</b> -&gt; '.
    '<b>'.(isset($row_new['oxima_next_kteo']) ? showDate(strtotime($row_new['oxima_next_kteo']), 'd/m/Y', 1) : '').'</b>'.'<br>';
  
  if ($row_old['oxima_liji_asfaleia'] != $row_new['oxima_liji_asfaleia']) 
    $sxolio_log.=gks_lang('Ημερομηνία Λήξης Ασφάλειας').':<br><b>'.(isset($row_old['oxima_liji_asfaleia']) ? showDate(strtotime($row_old['oxima_liji_asfaleia']), 'd/m/Y', 1) : '').'</b> -&gt; '.
    '<b>'.(isset($row_new['oxima_liji_asfaleia']) ? showDate(strtotime($row_new['oxima_liji_asfaleia']), 'd/m/Y', 1) : '').'</b>'.'<br>';
  
  if ($row_old['asset_thesi'] != $row_new['asset_thesi'])
    $sxolio_log.=gks_lang('Θέση').':<br><b>'.$row_old['asset_thesi'].'</b> -&gt; <b>'.$row_new['asset_thesi'].'</b>'.'<br>';
    
  if ($row_old['mac_address'] != $row_new['mac_address'])
    $sxolio_log.=gks_lang('Mac Address').':<br><b>'.$row_old['mac_address'].'</b> -&gt; <b>'.$row_new['mac_address'].'</b>'.'<br>';
  
  
  if ($row_old['asset_photo'] != $row_new['asset_photo'])
    $sxolio_log.=gks_lang('Φωτό').':<br><b>'.$row_old['asset_photo'].'</b> -&gt; <b>'.$row_new['asset_photo'].'</b>'.'<br>';
  
  if ($row_old['viva_terminal_id'] != $row_new['viva_terminal_id'])
    $sxolio_log.='Viva Terminal ID:<br><b>'.$row_old['viva_terminal_id'].'</b> -&gt; <b>'.$row_new['viva_terminal_id'].'</b>'.'<br>';
  
  if ($row_old['viva_terminal_code'] != $row_new['viva_terminal_code'])
    $sxolio_log.='Viva Virtual Terminal ID:<br><b>'.$row_old['viva_terminal_code'].'</b> -&gt; <b>'.$row_new['viva_terminal_code'].'</b>'.'<br>';
  
  if ($row_old['viva_action_after'] != $row_new['viva_action_after']) {
    $temp1='Αυτόματο';$temp2=gks_lang('Αυτόματο');
    switch ($row_old['viva_action_after']) {   
      case 1:$temp1=gks_lang('Απόκρυψη Viva');break;  
      case 2:$temp1=gks_lang('Εμφάνιση gks ERP App Mobile');break;  
      case 3:$temp1=gks_lang('Εμφάνιση Chrome');break;  
      case 4:$temp1=gks_lang('Εμφάνιση Safari');break;  
    }
    switch ($row_new['viva_action_after']) {   
      case 1:$temp2=gks_lang('Απόκρυψη Viva');break;  
      case 2:$temp2=gks_lang('Εμφάνιση gks ERP App Mobile');break;  
      case 3:$temp2=gks_lang('Εμφάνιση Chrome');break;  
      case 4:$temp2=gks_lang('Εμφάνιση Safari');break;  
    }
    $sxolio_log.=gks_lang('Viva Μετά την συναλλαγή').':<br><b>'.$temp1.'</b> -&gt; <b>'.$temp2.'</b>'.'<br>';
  }
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_assets',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_assets_log (asset_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sxolio_log);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
  }
}




$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







