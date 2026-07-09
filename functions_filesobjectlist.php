<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_FilesObjectList_obj_list() {
  
  $list=[
    'gks_acc_inv',
    'gks_acc_pay',
    'gks_acc_journal',
    'gks_acc_seires',
    'gks_ads_campain',
    'gks_assets',
    'gks_assets_service',
    'gks_assets_service_reasons',
    'gks_assets_type',
    'gks_assets_whi_mov',
    'gks_bank_accounts',
    'gks_company',
    'gks_company_subs',
    'gks_crm_channel_sale',
    'gks_crm_leads',
    'gks_crm_leads_status',
    'gks_crm_machine',
    'gks_crm_tasks',
    'gks_crm_tasks_status',
    'gks_custom_table',
    'gks_eshops',
    'gks_eshop_pricelist',
    'gks_eshop_pricelist_items',
    'gks_eshop_product_lots',
    'gks_eshop_products',
    'gks_eshop_products_categories',
    'gks_eshop_products_brands',
    'gks_hotel',
    'gks_hotel_availability',
    'gks_hotel_floor',
    'gks_hotel_reservation',
    'gks_hotel_price',
    'gks_hotel_room',
    'gks_hotel_room_type',
    'gks_orders',
    'gks_poi',
    'gks_poi_diadromes',
    'gks_poi_type',
    'gks_pos',
    'gks_print_forms',
    'gks_production_bom',
    'gks_production_ergasies',
    'gks_production_posta',
    'gks_template_html',
    'gks_transfer',
    'gks_transfer_area',
    'gks_transfer_oxima_type',
    'gks_transfer_pricelist',
    'gks_transfer_reservation',
    'gks_users_groups',
    'gks_warehouses',
    'gks_whi_mov',
    'wp_users',
    'gks_lang'];

  global $db_link;
  $sql="select custom_table_name from gks_custom_table where id_custom_table>=10000";
  $result = gks_run_sql($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');}
  while ($row = $result->fetch_assoc()) {
    $list[]=$row['custom_table_name'];
  }
          
  return $list;
  
}

function gks_FilesObjectList_map($objname) {
  switch ($objname) {   
    
    case 'gks_acc_inv':
      $ret=array('table' => 'gks_acc_inv_photo', 'tid' => 'id_acc_inv_photo','pid' => 'acc_inv_id', 'path'=>'acc/inv/');
      break;
    case 'gks_acc_pay':
      $ret=array('table' => 'gks_acc_pay_photo', 'tid' => 'id_acc_pay_photo','pid' => 'acc_pay_id', 'path'=>'acc/pay/');
      break;
    
    case 'gks_acc_journal':
      $ret=array('table' => 'gks_acc_journal_photo', 'tid' => 'id_acc_journal_photo','pid' => 'acc_journal_id', 'path'=>'acc/journal/');
      break;
    case 'gks_acc_seires':
      $ret=array('table' => 'gks_acc_seires_photo', 'tid' => 'id_acc_seira_photo','pid' => 'acc_seira_id', 'path'=>'acc/seira/');
      break;
    case 'gks_ads_campain':
      $ret=array('table' => 'gks_ads_campain_photo', 'tid' => 'id_ads_campain_photo','pid' => 'ads_campain_id', 'path'=>'crm/campain/');
      break;
    
    
    
    
    case 'gks_assets':
      $ret=array('table' => 'gks_assets_photo', 'tid' => 'id_asset_photo','pid' => 'asset_id', 'path'=>'assets/assets/');
      break;
    case 'gks_assets_service':
      $ret=array('table' => 'gks_assets_service_photo', 'tid' => 'id_asset_service_photo','pid' => 'assets_service_id', 'path'=>'assets/service/');
      break;
    case 'gks_assets_service_reasons':
      $ret=array('table' => 'gks_assets_service_reasons_photo', 'tid' => 'id_assets_service_reasons_photo','pid' => 'assets_service_reasons_id', 'path'=>'assets/service_reasons/');
      break;
    case 'gks_assets_type':
      $ret=array('table' => 'gks_assets_type_photo', 'tid' => 'id_asset_type_photo','pid' => 'asset_type_id', 'path'=>'assets/type/');
      break;
      
    case 'gks_assets_whi_mov':
      $ret=array('table' => 'gks_assets_whi_mov_photo', 'tid' => 'id_assets_whi_mov_photo','pid' => 'assets_whi_mov_id', 'path'=>'assets/whi_mov/');
      break;
    case 'gks_bank_accounts':
      $ret=array('table' => 'gks_bank_accounts_photo', 'tid' => 'id_bank_account_photo','pid' => 'bank_account_id', 'path'=>'base/bank_accounts/');
      break;
    case 'gks_company':
      $ret=array('table' => 'gks_company_photo', 'tid' => 'id_company_photo','pid' => 'company_id', 'path'=>'base/company/');
      break;
    case 'gks_company_subs':
      $ret=array('table' => 'gks_company_subs_photo', 'tid' => 'id_company_sub_photo','pid' => 'company_sub_id', 'path'=>'base/company_sub/');
      break;
    case 'gks_crm_channel_sale':
      $ret=array('table' => 'gks_crm_channel_sale_photo', 'tid' => 'id_crm_channel_sale_photo','pid' => 'crm_channel_sale_id', 'path'=>'crm/channel_sale/');
      break;
    case 'gks_crm_leads':
      $ret=array('table' => 'gks_crm_leads_photo', 'tid' => 'id_crm_leads_photo','pid' => 'crm_lead_id', 'path'=>'crm/lead/');
      break;
    case 'gks_crm_leads_status':
      $ret=array('table' => 'gks_crm_leads_status_photo', 'tid' => 'id_crm_lead_status_photo','pid' => 'crm_lead_status_id', 'path'=>'crm/lead_status/');
      break;
    case 'gks_crm_machine':
      $ret=array('table' => 'gks_crm_machine_photo', 'tid' => 'id_crm_machine_photo','pid' => 'crm_machine_id', 'path'=>'crm/machine/');
      break;
    case 'gks_crm_tasks':
      $ret=array('table' => 'gks_crm_tasks_photo', 'tid' => 'id_crm_tasks_photo','pid' => 'crm_task_id', 'path'=>'crm/task/');
      break;
    case 'gks_crm_tasks_status':
      $ret=array('table' => 'gks_crm_tasks_status_photo', 'tid' => 'id_crm_task_status_photo','pid' => 'crm_task_status_id', 'path'=>'crm/task_status/');
      break;

    case 'gks_custom_table':
      $ret=array('table' => 'gks_custom_table_photo', 'tid' => 'id_custom_table_photo','pid' => 'custom_table_id', 'path'=>'base/custom/');
      break;

    case 'gks_email_template':
      $ret=array('table' => 'gks_email_template_photo', 'tid' => 'id_email_template_photo','pid' => 'email_template_id', 'path'=>'crm/email_template/');
      break;
      
    case 'gks_eshops':
      $ret=array('table' => 'gks_eshops_photo', 'tid' => 'id_eshop_photo','pid' => 'eshop_id', 'path'=>'base/eshop/');
      break;
      
    case 'gks_eshop_pricelist':
      $ret=array('table' => 'gks_eshop_pricelist_photo', 'tid' => 'id_pricelist_photo','pid' => 'pricelist_id', 'path'=>'base/pricelist/');
      break;
    case 'gks_eshop_pricelist_items':
      $ret=array('table' => 'gks_eshop_pricelist_items_photo', 'tid' => 'id_pricelist_item_photo','pid' => 'pricelist_item_id', 'path'=>'base/pricelistitem/');
      break;
    case 'gks_eshop_product_lots':
      $ret=array('table' => 'gks_eshop_product_lots_photo', 'tid' => 'id_lot_product_photo','pid' => 'lot_product_id', 'path'=>'base/poduct_lot/');
      break;
    case 'gks_eshop_products':
      $ret=array('table' => 'gks_eshop_products_photo', 'tid' => 'id_product_photo','pid' => 'product_id', 'path'=>'base/poducts/');
      break;
    case 'gks_eshop_products_categories':
      $ret=array('table' => 'gks_eshop_products_categories_photo', 'tid' => 'id_eshop_products_categories_photo','pid' => 'product_category_id', 'path'=>'base/poducts_categories/');
      break;
    case 'gks_eshop_products_brands':
      $ret=array('table' => 'gks_eshop_products_brands_photo', 'tid' => 'id_eshop_products_brands_photo','pid' => 'product_brand_id', 'path'=>'base/poducts_brands/');
      break;

      
    case 'gks_hotel':
      $ret=array('table' => 'gks_hotel_photo', 'tid' => 'id_hotel_photo','pid' => 'hotel_id', 'path'=>'hotel/hotel/');
      break;
    case 'gks_hotel_availability':
      $ret=array('table' => 'gks_hotel_availability_photo', 'tid' => 'id_hotel_availability_photo','pid' => 'hotel_availability_id', 'path'=>'hotel/availability/');
      break;
      
    case 'gks_hotel_floor':
      $ret=array('table' => 'gks_hotel_floor_photo', 'tid' => 'id_hotel_floor_photo','pid' => 'hotel_floor_id', 'path'=>'hotel/floor/');
      break;
    case 'gks_hotel_reservation':
      $ret=array('table' => 'gks_hotel_reservation_photo', 'tid' => 'id_hotel_reservation_photo','pid' => 'hotel_reservation_id', 'path'=>'hotel/reservation/');
      break;
    case 'gks_hotel_price':
      $ret=array('table' => 'gks_hotel_price_photo', 'tid' => 'id_hotel_price_photo','pid' => 'hotel_price_id', 'path'=>'hotel/price/');
      break;
    case 'gks_hotel_room':
      $ret=array('table' => 'gks_hotel_room_photo', 'tid' => 'id_hotel_room_photo','pid' => 'hotel_room_id', 'path'=>'hotel/room/');
      break;
    case 'gks_hotel_room_type':
      $ret=array('table' => 'gks_hotel_room_type_photo', 'tid' => 'id_hotel_room_type_photo','pid' => 'hotel_room_type_id', 'path'=>'hotel/room_type/');
      break;

    case 'gks_orders':
      $ret=array('table' => 'gks_orders_photo', 'tid' => 'id_orders_photo','pid' => 'order_id', 'path'=>'order/');
      break;

    
    case 'gks_poi':
      $ret=array('table' => 'gks_poi_photo', 'tid' => 'id_poi_photo','pid' => 'poi_id', 'path'=>'poi/poi/');
      break;

    case 'gks_poi_diadromes':
      $ret=array('table' => 'gks_poi_diadromes_photo', 'tid' => 'id_poi_diadromes_photo','pid' => 'poi_diadromes_id', 'path'=>'poi/diadromes/');
      break;
    case 'gks_poi_type':
      $ret=array('table' => 'gks_poi_type_photo', 'tid' => 'id_poi_type_photo','pid' => 'poi_type_id', 'path'=>'poi/poi_type/');
      break;
      
    case 'gks_pos':
      $ret=array('table' => 'gks_pos_photo', 'tid' => 'id_pos_photo','pid' => 'pos_id', 'path'=>'pos/pos/');
      break;
    case 'gks_print_forms':
      $ret=array('table' => 'gks_print_forms_photo', 'tid' => 'id_print_form_photo','pid' => 'print_form_id', 'path'=>'base/print_form/');
      break;
    case 'gks_production_bom':
      $ret=array('table' => 'gks_production_bom_photo', 'tid' => 'id_production_bom_photo','pid' => 'production_bom_id', 'path'=>'production/bom/');
      break;
    case 'gks_production_ergasies':
      $ret=array('table' => 'gks_production_ergasies_photo', 'tid' => 'id_production_ergasia_photo','pid' => 'production_ergasia_id', 'path'=>'production/ergasia/');
      break;
    case 'gks_production_posta':
      $ret=array('table' => 'gks_production_posta_photo', 'tid' => 'id_production_posto_photo','pid' => 'production_posto_id', 'path'=>'production/posto/');
      break;

    case 'gks_template_html':
      $ret=array('table' => 'gks_template_html_photo', 'tid' => 'id_template_html_photo','pid' => 'template_html_id', 'path'=>'base/template_html/');
      break;

    case 'gks_transfer':
      $ret=array('table' => 'gks_transfer_photo', 'tid' => 'id_transfer_photo','pid' => 'transfer_id', 'path'=>'transfer/transfer/');
      break;
    case 'gks_transfer_area':
      $ret=array('table' => 'gks_transfer_area_photo', 'tid' => 'id_transfer_area_photo','pid' => 'transfer_area_id', 'path'=>'transfer/area/');
      break;
    case 'gks_transfer_oxima_type':
      $ret=array('table' => 'gks_transfer_oxima_type_photo', 'tid' => 'id_transfer_oxima_type_photo','pid' => 'transfer_oxima_type_id', 'path'=>'transfer/oxima_type/');
      break;

    case 'gks_transfer_pricelist':
      $ret=array('table' => 'gks_transfer_pricelist_photo', 'tid' => 'id_transfer_pricelist_photo','pid' => 'transfer_pricelist_id', 'path'=>'transfer/oxima_type/');
      break;
    case 'gks_transfer_reservation':
      $ret=array('table' => 'gks_transfer_reservation_photo', 'tid' => 'id_transfer_reservation_photo','pid' => 'transfer_reservation_id', 'path'=>'transfer/reservation/');
      break;
      
    case 'gks_users_groups':
      $ret=array('table' => 'gks_users_groups_photo', 'tid' => 'id_users_group_photo','pid' => 'users_group_id', 'path'=>'base/users_group/');
      break;
    case 'gks_warehouses':
      $ret=array('table' => 'gks_warehouses_photo', 'tid' => 'id_warehouse_photo','pid' => 'warehouse_id', 'path'=>'base/warehouse/');
      break;
      
    case 'gks_whi_mov':
      $ret=array('table' => 'gks_whi_mov_photo', 'tid' => 'id_whi_mov_photo','pid' => 'whi_mov_id', 'path'=>'whi/mov/');
      break;
      
    case 'wp_users':
      $ret=array('table' => 'gks_users_photo', 'tid' => 'id_user_photo','pid' => 'user_id', 'path'=>'base/users/');
      break;
      
    case 'gks_lang':
      $ret=array('table' => 'gks_lang_photo', 'tid' => 'id_lang_photo','pid' => 'lang_idd', 'path'=>'base/lang/');
      break;
      
      
    default:
      $found_params=false;
      if (startwith($objname,'gks_ct_')) {
        $ctid=intval(substr($objname, 7));
        if ($ctid>=10000) {
          $ret=array('table' => 'gks_customt_gks_ct_'.$ctid.'_photo', 'tid' => 'id_gks_customt_gks_ct_'.$ctid.'_photo','pid' => 'gks_customt_gks_ct_'.$ctid.'_id', 'path'=>'customtable/'.$ctid.'/');
          $found_params=true;
        }
      }
      if ($found_params==false) {
      
        debug_mail(false,'error on gks_FilesObjectList_map',$objname);
        echo '<pre>error on gks_FilesObjectList_map. Object '.$objname.' not supported</pre>';
        die();
      }
    
  }
  $ret['shortcode_prefix']='xxx';
  //echo 1/0;
  global $db_link;
  //if (startwith($objname,'gks_ct_')) 
  $sql="select shortcode_prefix 
  from gks_custom_table 
  where custom_table_name='".$db_link->escape_string($objname)."'
  and shortcode_prefix<>''";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo '<pre>sql error on gks_FilesObjectList_map. Object '.$objname.'</pre>';
    die();}
     
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $ret['shortcode_prefix']=$row['shortcode_prefix'];      
  } else {
    
    if ($objname=='gks_assets_service_reasons') {
      $ret['shortcode_prefix']='s01';
    } else if ($objname=='gks_assets_type') {
      $ret['shortcode_prefix']='s02';
    } else if ($objname=='gks_crm_channel_sale') {
      $ret['shortcode_prefix']='s03';
    } else if ($objname=='gks_crm_leads_status') {
      $ret['shortcode_prefix']='s04';
    } else if ($objname=='gks_crm_tasks_status') {
      $ret['shortcode_prefix']='s05';
    } else if ($objname=='gks_lang') {
      $ret['shortcode_prefix']='s06';
    } else if ($objname=='gks_custom_table') {
      $ret['shortcode_prefix']='s07';
      
    } else {
      debug_mail(false,'shortcode_prefix not found',$sql);
      echo '<pre>sql error on gks_FilesObjectList_map. Object '.$objname.'</pre>';
      die();
    }
  }
  
  
  
  return $ret;
}


function gks_FilesObjectList($params) {
  global $db_link;
  global $gks_cache_version;
	global $GKS_ORDERS_AWS;
	global $GKS_SITE_HUMAN_NAME;
	
  global $gks_FilesObjectList_scandir_echo;   // string
  global $gks_FilesObjectList_show_print;         // array
  global $gks_FilesObjectList_scandir_path;      // array
  global $gks_FilesObjectList_scandir_path_keys; // array
  global $gks_FilesObjectList_shortcode_prefix; //string
 
  
  $objname=$params['objname'];
  $id=$params['id'];
	$aws_folder=''; if (isset($params['aws_folder'])) $aws_folder=$params['aws_folder'];

  $object_map=gks_FilesObjectList_map($objname);
  $object_path=$object_map['path'];
  $object_table=$object_map['table'];
  $object_tid=$object_map['tid'];
  $object_pid=$object_map['pid'];
  
 
  $html='';
  $html.=
  '<div class="card gks_card_expand">
    <div class="card-header" style="text-align:center">
      <span style="vertical-align: middle;">'.gks_lang('Αρχεία').'</span>
      
    </div>
    <div class="card-body" '.gks_card_body('flsobjlst').'>

      <form role="form" method="post" action="admin-filesobjectlist-photo-upload.php" id="filesobjectlist_form" enctype="multipart/form-data" style="width: 100%;">
        <input type="hidden" name="object_id" id="object_id" value="'.$id.'">
        <input type="hidden" name="object_name" id="object_name" value="'.$objname.'">

        <div id="lightgallery_user">
          <div class="form-group" id="imagelist_photo">';
          

  $sql="select * from ".$object_table." where ".$object_pid."=".$id." order by ".$object_tid;
  $result_select = $db_link->query($sql);
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die('sql error');}
  $gks_FilesObjectList_show_print=array();
  while ($row_select = $result_select->fetch_assoc()) {
    $gks_FilesObjectList_show_print[$row_select['photo_url']]=$row_select;
  }
  //print '<pre>';print_r($gks_FilesObjectList_show_print);die();
  


  $mydir=GKS_FileServerShare.$object_path.$id.'/';                
  $gks_FilesObjectList_scandir_echo='';
  $gks_FilesObjectList_scandir_path=array();
  $gks_FilesObjectList_scandir_path_keys=array();
  $gks_FilesObjectList_shortcode_prefix=$object_map['shortcode_prefix'];
  //echo $mydir;
  //die();
  
  gks_FilesObjectList_scandir($mydir);



  $html.='

            <table id="filesobjectlist_table_imagelist_photo" class="table table-sm table-responsive table-striped table-bordered gkstable100" 
            border="0" cellspacing="0" cellpadding="5" align="center" 
            style="'.($gks_FilesObjectList_scandir_echo!='' ? '' : 'display:none;').'">
            <thead>
            <tr>	
            <th class="table-dark" scope="col" style="text-align: left !important;" width="0%">#</th> 
            <th class="table-dark fol_th_name" scope="col" style="text-align: left !important;" width="60%">'.gks_lang('Όνομα').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="40%">'.gks_lang('Περιγραφή').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Φωτό').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Προσθήκη').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Μέγεθος').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Λήψη').'</th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-print tooltipster" style="color: #35dc35;font-size: 120%;" title="'.gks_lang('Να συμπεριλαμβάνεται στις εκτυπώσεις').'"></i></th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-unlock-alt tooltipster" style="color: #0dcaf0;font-size: 120%;" title="'.gks_lang('Δημόσιο').'"></i></th> 
            </tr>
            </thead>
            <tbody>
            '.$gks_FilesObjectList_scandir_echo.'
            </tbody>
            </table>

          </div>
        </div>
        <div style="clear: both;"></div>
         
        <div id="filesobjectlist_f_button_add_files_photo" class="fileinput-button"  href="#"     data-options="thumbnail: \'\'" style="padding-top:10px;width: 100%;text-align: center;">
          <div id="filesobjectlist_f_button_add_files_photo_buttons">
            <span>
              <button type="submit" class="btn btn-sm btn-primary" id="filesobjectlist_myuploadbutton"><i class="fas fa-upload"></i> '.gks_lang('Μεταφόρτωση αρχείων').'</button>
              <input type="file" name="files[]" multiple>
            </span> 
            <span class="btn btn-sm btn-primary" id="gks_filesexplore_start_from_item_list" data-def_folder="/'.$object_path.$id.'"><i class="fa-solid fa-file-lines"></i> '.gks_lang('Εξερεύνηση αρχείων').'</span>
          </div>
          <div class="filesobjectlist_f_button_add_files_photo_info">
            '.gks_lang('Μέγιστο μέγεθος').' '.gks_get_max_upload_file_size().'
          </div>
        </div>
        
        <div id="filesobjectlist_progress_bar_photo" style="margin-top:10px; display:none;background: rgb(230,230,230);">
          <div class="filesobjectlist_bar_photo" style="padding-top:0px;padding-bottom:0px;width: 0%;height: 20px;background: green;"></div>
        </div>
        <div id="filesobjectlist_progress_extended_photo" style="display:none;">&nbsp;</div>
      </form>';      

$html.='
      <div id="gks_webcam_div">
      	<span class="btn btn-primary btn-sm" id="gks_webcam_start"><i class="fa-solid fa-camera"></i> '.gks_lang('Λήψη από web camera').'</span>
      </div>
      <div id="gks_webcam_panel">
      	<div id="gks_webcam_panel_video">
      		<video id="gks_webcam_video" muted="true" playsinline></video>
      		<canvas id="gks_webcam_canvas"></canvas>
      		<img id="gks_webcam_img" />
      		<div id="gks_webcam_message_ok"></div>
      	</div>
      	<div id="gks_webcam_panel_buttons">
	      	<span id="gks_webcam_pixels"></span>
	      	<span class="btn btn-primary btn-sm" id="gks_webcam_permissions">'.gks_lang('Ζητήστε δικαιώματα για την web cam').'</span>
	      	<select id="gks_webcam_select" class="form-control form-control-sm">
	      		<option>1</option>
	      		<option>1</option>
	      	</select>
	      	<span class="btn btn-primary btn-sm" id="gks_webcam_capture">'.gks_lang('Λήψη Φωτογραφίας').'</span>
	      	<span class="btn btn-primary btn-sm" id="gks_webcam_cancel_capture">'.gks_lang('Δεν μου αρέσει').'</span>
	      	<span class="btn btn-primary btn-sm" id="gks_webcam_save">'.gks_lang('Αποθήκευση').'<span id="gks_webcam_size"></span></span>
	      	<span class="btn btn-primary btn-sm" id="gks_webcam_stop">'.gks_lang('Τέλος').'</span>
	      	<audio id="audio_camera_click" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/my/audio/camera_click.mp3" type="audio/mpeg"></audio>
	      	    
	      	
	      	
	      </div>
	      
      </div>
      ';			
			
      if ($GKS_ORDERS_AWS && $aws_folder!='') {
$html.='      	
      <div id="aws_button" data-folder="'.$aws_folder.'">
      <img src="img/wait.gif" border="0">
      </div>';
      }
$html.='
    </div>
  </div>';  
  
$html.='
<div id="dialog_filesobjectlist_set_public_file" title="'.$GKS_SITE_HUMAN_NAME.'" style="display: none;">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div id="dialog_filesobjectlist_set_public_file_title">
          '.gks_lang('Ενεργοποίηση δημόσιου συνδέσμου για το αρχείο').'
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div class="alert alert-warning" role="alert" id="dialog_filesobjectlist_set_public_file_alert">
          <i class="fas fa-exclamation-triangle"></i> '.gks_lang('Προσοχή ! Το αρχείο θα είναι προσβάσιμο από το internet χωρίς περιορισμούς').'
        </div>
      </div>
    </div>
    <div class="form-group row">
      <label for="dialog_filesobjectlist_set_public_file_enable" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Ενεργοποίηση').':</label>
      <div class="col-md-8">
        <input type="checkbox" id="dialog_filesobjectlist_set_public_file_enable" value="1">
      </div>
    </div>
    <div class="form-group row" id="dialog_filesobjectlist_set_public_file_date_div">
      <label for="dialog_filesobjectlist_set_public_file_date" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Ημερομηνία λήξης').':</label>
      <div class="col-md-8">
        <input tabindex="4" id="dialog_filesobjectlist_set_public_file_date" type="text" class="form-control form-control-sm" value="" autocomplete="off" style="max-width:200px">
      </div>
    </div>
    <div class="form-group row">
      <label for="dialog_filesobjectlist_set_public_file_url" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Σύνδεσμος').':</label>
      <div class="col-md-8" id="dialog_filesobjectlist_set_public_file_url">
        <span id="dialog_filesobjectlist_set_public_file_url" class="form-control-plaintext form-control-sm"></span>
      </div>
    </div>
    <div class="form-group row">
      <label for="dialog_filesobjectlist_set_public_file_myopencount" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Προβολές').':</label>
      <div class="col-md-8">
        <span id="dialog_filesobjectlist_set_public_file_myopencount" class="form-control-plaintext form-control-sm"></span>
      </div>
    </div>
  </div>
</div>';  
  
  
  
  
  
  $fileupload_files='
  <link rel="stylesheet" href="/my/css/_gks_filesobjectlist.css?v='.$gks_cache_version.'" type="text/css">    
  <link rel="stylesheet" href="/my/js/jquery.fileupload/jquery.fileupload.css?v='.$gks_cache_version.'" type="text/css">    
  <script src="/my/js/jquery.fileupload/vendor/jquery.ui.widget.js?v='.$gks_cache_version.'"></script>
  <script src="/my/js/jquery.fileupload/jquery.iframe-transport.js?v='.$gks_cache_version.'"></script>
  <script src="/my/js/jquery.fileupload/jquery.fileupload.js?v='.$gks_cache_version.'"></script> 
  <script src="/my/js/jquery.fileupload/jquery.fileupload-process.js?v='.$gks_cache_version.'"></script> 
  <script src="/my/js/jquery.fileupload/jquery.fileupload-validate.js?v='.$gks_cache_version.'"></script> 
  ';
  
  $js_files='
  <script src="js/_gks_filesobjectlist.js?v='.$gks_cache_version.'"></script>
  ';

  $vars='
  <script> 
  var from_php_filesobjectlist_max_upload_file_size='.gks_get_max_upload_file_size(true).';
  var from_php_filesobjectlist_object_name=$.base64.decode(\''.base64_encode($objname).'\');
  </script> 
  ';
  
  
  return array('html' => $html,'fileupload_files'=>$fileupload_files, 'js_files'=> $js_files,'vars'=>$vars);
  
  
}


function gks_FilesObjectList_scandir($root, $depth=0,$only_one_file='') {
  global $gks_FilesObjectList_scandir_echo;   // string
  global $gks_FilesObjectList_show_print;         // array
  global $gks_FilesObjectList_scandir_path;      // array
  global $gks_FilesObjectList_scandir_path_keys; // array
  global $gks_FilesObjectList_shortcode_prefix;//string 
  
  
    // if root is a file
    if (is_file($root)) {

      return;
    }

    if (!is_dir($root)) {
      return;
    }

    $dirs = scandir($root,SCANDIR_SORT_ASCENDING);
    
    $dirs_array = array();
    foreach ($dirs as $dir) {
      $path = $root . '/' . $dir;
      $dirs_array[] = array('val' => $dir, 'is_dir' => is_dir($path));
    }
    
    usort($dirs_array, "order_item_sort_dirs_files");
    
    foreach ($dirs_array as $dirV) {
      $dir=$dirV['val'];
      if ($dir == '.' || $dir == '..' || strtolower($dir) == 'thumbnail') {
        continue;
      }

      $path = $root . $dir;
      if (is_file($path)) {
        //debug_mail(false,'path','<br>'.$path.'<br>'.$only_one_file);
        
        if (endwith(strtolower($path),'.delete') == false) {
          if ($only_one_file=='' or $only_one_file==$path) {
            // if file, create list item tag, and done.
            $relative_path=substr($path, strlen(GKS_FileServerShare));
            //echo $relative_path;
            //die();
            $gks_FilesObjectList_scandir_path[]=$relative_path;
            $url_file='admin-get-file.php?fs=fileservers&file='.rawurlencode($relative_path);
            
            $img_thump='';
            $select_for_print='';
            $show_print=0;
            $img_descr='';

            
            
            
            
            
            if (isset($gks_FilesObjectList_show_print[$relative_path])) {
              $show_print=intval($gks_FilesObjectList_show_print[$relative_path]['show_print']);  
              $img_descr=trim_gks($gks_FilesObjectList_show_print[$relative_path]['descr']);
            }

  
            $out_name_html='<a href="'.$url_file.'"  target="_blank">'.$dir.'</a>';
            $fileext = strtolower(pathinfo($dir, PATHINFO_EXTENSION));
            if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
              $thump_file=$root . 'thumbnail/' . $dir;
              //if (file_exists($thump_file)) {
                $img_thump=$thump_file;
                $url_thump='admin-get-file.php?fs=fileservers&file='.rawurlencode(substr($thump_file, strlen(GKS_FileServerShare)));
                $img_thump='<a class="filesobjectlist_lightgallery_gks_fileserver_item" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                            '<img style="max-width:96px;max-height:96px;" src="'.$url_thump.'">'.
                           '</a>';
              //}
            

              
              if ($show_print==1) {
                $select_for_print='<img class="filesobjectlist_set_print_photo" data-value=1 data-path="'.$relative_path.'" src="img/1.png">';
              } else {
                $select_for_print='<img class="filesobjectlist_set_print_photo" data-value=0 data-path="'.$relative_path.'" src="img/0b.png">';
              }
            }
            
            
            $public_file=gks_fileserver_item_render_public_expire_date($gks_FilesObjectList_show_print,$relative_path);
            
            $temp_item=
              '<tr class="tddd" data-path="'.$relative_path.'">'.
                '<th class="mytdcm" scope="row" nowrap>'.
                 '<i class="fas fa-trash-alt filesobjectlist_delete_upload_photo" data-path="'.$relative_path.'"></i>'.
                '</th>'.
                '<td class="mytdcml fol_td_name" style="padding-left:'.($depth * 10).'px;">'.$out_name_html.'</td>'.
                '<td class="mytdcm tdimg_descr">'.$img_descr.'</td>'.
                '<td class="mytdcm tdimg">'.$img_thump.'</td>'.
                '<td class="mytdcm fol_td_date">'.secondsago(filemtime($path)).'</td>'.
                '<td class="mytdcmr" nowrap>'. number_format((filesize($path)/1024/1024),2,',','.').' MB</td>'.
                '<td class="mytdcm" nowrap>'.
                 '<a href="'.$url_file.'&download=1">'.
                  '<i class="fas fa-download fol_td_download"></i>'.
                 '</a>'.
                '</td>'.
                '<td class="mytdcm fol_selprint"  nowrap>'.$select_for_print.'</td>'.
                '<td class="mytdcm fol_selpublic" nowrap>'.$public_file.'</td>'.
                
                
              '</tr>';
                   
            $gks_FilesObjectList_scandir_echo.=$temp_item;
            $gks_FilesObjectList_scandir_path_keys[]=array('path' => $relative_path, 'html' => $temp_item);
            
            //echo '<li>' . $dir .' '.$depth. ' (file)</li>';
          }
        }
      } else if (is_dir($path) and $only_one_file=='') {
        // if dir, create list item with sub ul tag
        $relative_path=substr($path, strlen(GKS_FileServerShare));
        //echo $relative_path.'<br>'.$path;
        //die();        
        $temp_item= '<tr class="tddd" data-path="'.$relative_path.'"><td scope="row"></td><td scope="row" style="padding-left:'.($depth * 10).'px;font-style: italic;" colspan=8>' . $dir.'</td></tr>';
        $gks_FilesObjectList_scandir_echo.=$temp_item;
        $gks_FilesObjectList_scandir_path_keys[]=array('path' => $relative_path, 'html' => $temp_item);
        
        //echo '<li>';
        //echo '<label>' . $dir . ' '.$depth .'(dir)</label>';
        //echo '<ul>';
        
        gks_FilesObjectList_scandir($path.'/',$depth + 1); // <--- then recursion
        //echo '</ul>';
        //echo '</li>';
      }
    }
} 


function gks_fileserver_item_render_public_expire_date($data,$rpath) {
  global $gks_FilesObjectList_shortcode_prefix;//string 
  if (!isset($data[$rpath])) {
    return '<img '.
    'class="filesobjectlist_set_public_file" '.
    'data-path="'.$rpath.'" '.
    'src="img/0bbl.png" '.
    'data-expire_date="" '.
    'data-shortcode_url="" '.
    'data-myopencount="0" '.
    'data-active="0" '.
    '>';
  
  }
  
  $public_expire_date_time=0;
  $public_expire_date_str=''; 
  $public_expire_title=[];
  $public_shortcode_url='';
  $public_myopencount=0;
  if (!empty($data[$rpath]['public_expire_date'])) {
    $public_expire_date_time=strtotime($data[$rpath]['public_expire_date']);
    
    $public_expire_title[]=gks_lang('Ενεργό έως').':';
    //$public_expire_title[]=showDate($public_expire_date_time,'d/m/Y H:i',1);
    $public_expire_title[]=secondsago($public_expire_date_time);
    
    $public_expire_date_str=showDate($public_expire_date_time,'d/m/Y H:i',1);
  }
  
  $public_shortcode=trim_gks($data[$rpath]['public_shortcode']);
  if ($public_shortcode!='') {
    //if (count($public_expire_title)>0) $public_expire_title[]='<br>';
    $public_expire_title[]=gks_lang('Δημόσιος σύνδεσμος').':';
    $public_shortcode_url=$gks_FilesObjectList_shortcode_prefix.$public_shortcode;
    $public_shortcode_url_full='<a href='.GKS_SITE_URL.'s/'.$public_shortcode_url.' target=_blank>'.$public_shortcode_url.'</a>';
    $public_shortcode_url_full_d='<a href='.GKS_SITE_URL.'s/'.$public_shortcode_url.'?d target=_blank><i class=\'fas fa-download\' style=\'color:blue;\'></i></a>';
    
    $public_expire_title[]=$public_shortcode_url_full.' '.$public_shortcode_url_full_d;
    $public_myopencount=intval($data[$rpath]['public_myopencount']);
    $public_expire_title[]=gks_lang('Προβολές').': '.$public_myopencount;
  }
  
  if ($public_expire_date_time > time()) {
    $data_value=1;
    $src_img='img/1bl.png';
  } else {
    $data_value=0;
    $src_img='img/0bbl.png';
  }

  $public_expire_title=implode('<br>',$public_expire_title);
  
  $public_file='<img '.
  'class="filesobjectlist_set_public_file '.($public_expire_title=='' ? '' : 'tooltipster').'" '.
  'data-path="'.$rpath.'" '.
  'src="'.$src_img.'" '.
  ($public_expire_title=='' ? '' : 'title="'.$public_expire_title.'"').
  'data-expire_date="'.$public_expire_date_str.'" '.
  'data-shortcode_url="'.$public_shortcode_url.'" '.
  'data-myopencount="'.$public_myopencount.'" '.
  'data-active="'.$data_value.'"'.
  '>';
  return $public_file;
}





function gks_fileserver_item_scandir_rec($root, $depth=0,$only_one_file='') {
  global $gks_fileserver_item_scandir_rec_echo;   // string
  global $gks_fileserver_item_show_print;         // array
  global $gks_fileserver_item_relative_path;      // array
  global $gks_fileserver_item_relative_path_keys; // array

  
  
    // if root is a file
    if (is_file($root)) {

      return;
    }

    if (!is_dir($root)) {
      return;
    }

    $dirs = scandir($root,SCANDIR_SORT_ASCENDING);
    
    $dirs_array = array();
    foreach ($dirs as $dir) {
      $path = $root . '/' . $dir;
      $dirs_array[] = array('val' => $dir, 'is_dir' => is_dir($path));
    }
    
    usort($dirs_array, "order_item_sort_dirs_files");
    
    foreach ($dirs_array as $dirV) {
      $dir=$dirV['val'];
      if ($dir == '.' || $dir == '..' || strtolower($dir) == 'thumbnail') {
        continue;
      }

      $path = $root . $dir;
      if (is_file($path)) {
        //debug_mail(false,'path','<br>'.$path.'<br>'.$only_one_file);
        
        if (endwith(strtolower($path),'.delete') == false) {
          if ($only_one_file=='' or $only_one_file==$path) {
            // if file, create list item tag, and done.
            $relative_path=substr($path, strlen(GKS_FileServerShare));
            //echo $relative_path;
            //die();
            $gks_fileserver_item_relative_path[]=$relative_path;
            $url_file='admin-get-file.php?fs=fileservers&file='.rawurlencode($relative_path);
            
            $img_thump='';
            $select_for_print='';
            
            $out_name_html='<a href="'.$url_file.'"  target="_blank">'.$dir.'</a>';
            $fileext = strtolower(pathinfo($dir, PATHINFO_EXTENSION));
            if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
              $thump_file=$root . 'thumbnail/' . $dir;
              //if (file_exists($thump_file)) {
                $img_thump=$thump_file;
                $url_thump='admin-get-file.php?fs=fileservers&file='.rawurlencode(substr($thump_file, strlen(GKS_FileServerShare)));
                $img_thump='<a class="filesobjectlist_lightgallery_gks_fileserver_item" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                            '<img src="'.$url_thump.'">'.
                           '</a>';

                                                      
              //}
            
              if (in_array($relative_path,$gks_fileserver_item_show_print)) {
                $select_for_print='<img class="filesobjectlist_set_print_photo" data-value=1 data-path="'.$relative_path.'" src="img/1.png">';
              } else {
                $select_for_print='<img class="filesobjectlist_set_print_photo" data-value=0 data-path="'.$relative_path.'" src="img/0b.png">';
              }
            //} else if (endwith(strtolower($dir),'.xml')) {
            //  $out_name_html='<a href="'.$url_file.'">'.$dir.'</a>';
            }
            
            $public_file=gks_fileserver_item_render_public_expire_date([],$relative_path);
            
            $temp_item='<tr class="tddd" data-path="'.$relative_path.'">'.
                   '<th class="mytdcm" scope="row" nowrap>'.
                     '<i class="fas fa-trash-alt filesobjectlist_delete_upload_photo" data-path="'.$relative_path.'" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>'.
                   '</th>'.
                   '<td class="mytdcml fol_td_name" style="padding-left:'.($depth * 10).'px;">'.$out_name_html.'</td>'.
                   '<td class="mytdcm tdimg_descr"></td>'.
                   '<td class="mytdcm tdimg">'.$img_thump.'</td>'.
                   '<td class="mytdcm fol_td_date">'.secondsago(filemtime($path)).'</td>'.
                   '<td class="mytdcmr" nowrap>'. number_format((filesize($path)/1024/1024),2,',','.').' MB</td>'.
                   '<td class="mytdcm" nowrap>'.
                    '<a href="'.$url_file.'&download=1">'.
                      '<i class="fas fa-download" style="font-size:200%;vertical-align:middle;color:blue;"></i>'.
                    '</a>'.
                   '</td>'.
                   '<td class="mytdcm fol_selprint"  nowrap>'.$select_for_print.'</td>'.
                   '<td class="mytdcm fol_selpublic" nowrap>'.$public_file.'</td>'.
                   '</tr>';
                   
            $gks_fileserver_item_scandir_rec_echo.=$temp_item;
            $gks_fileserver_item_relative_path_keys[]=array('path' => $relative_path, 'html' => $temp_item);
            
            //echo '<li>' . $dir .' '.$depth. ' (file)</li>';
          }
        }
      } else if (is_dir($path) and $only_one_file=='') {
        // if dir, create list item with sub ul tag
        $relative_path=substr($path, strlen(GKS_FileServerShare));
        //echo $relative_path.'<br>'.$path;
        //die();        
        $temp_item= '<tr class="tddd" data-path="'.$relative_path.'"><td scope="row"></td><td scope="row" style="padding-left:'.($depth * 10).'px;font-style: italic;" colspan=8>' . $dir.'</td></tr>';
        $gks_fileserver_item_scandir_rec_echo.=$temp_item;
        $gks_fileserver_item_relative_path_keys[]=array('path' => $relative_path, 'html' => $temp_item);
        
        //echo '<li>';
        //echo '<label>' . $dir . ' '.$depth .'(dir)</label>';
        //echo '<ul>';
        
        gks_fileserver_item_scandir_rec($path.'/',$depth + 1); // <--- then recursion
        //echo '</ul>';
        //echo '</li>';
      }
    }
} 

function gks_fileserver_item_create_public_shortcode($object_name,$data_path) {
  global $db_link;
  
  $object_map=gks_FilesObjectList_map($object_name);
  $object_path=$object_map['path'];
  $object_table=$object_map['table'];
  $object_tid=$object_map['tid'];
  $object_pid=$object_map['pid'];
  $shortcode_prefix=$object_map['shortcode_prefix'];

  $sql="select public_shortcode from ".$object_table." 
  where photo_url like '".$db_link->escape_string($data_path)."' 
  order by ".$object_tid." desc limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  if (isset($public_shortcode)==false) $public_shortcode='';
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $public_shortcode=trim($row['public_shortcode']);
  }
  if ($public_shortcode!='') {
    return array(
      'action'=>'exist',
      'code'=>$public_shortcode,
      'full' => GKS_SITE_URL.'s/'.$shortcode_prefix.$public_shortcode,
    );
  }
  
  $cc=0;$num_string=5;
  do {
    $cc++;
    $value=gks_random_string($num_string);
    $sql="select public_shortcode from ".$object_table." where public_shortcode like '".$db_link->escape_string($value)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
  
    if ($result->num_rows==0) {
      $public_shortcode= $value;
      break;   
    }
    if ($cc > 10) { //ean pano apo 10 fores den brike kapoio maonadiko, tote na apo 5 chars na ginei 6, kai meta 7 ktl
      $cc=0;
      $num_string++;
    }
  } while (true);
  
  $sql="update ".$object_table." set
  public_shortcode='".$db_link->escape_string($public_shortcode)."'
  where photo_url like '".$db_link->escape_string($data_path)."' limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  
  
  return array(
    'action'=>'create',
    'code'=>$public_shortcode,
    'full' => GKS_SITE_URL.'s/'.$shortcode_prefix.$public_shortcode,
  );
  
  //echo '<pre>'.$object_table.'|'.$data_path;die();
  
}

function gks_f_button_add_files_photo_html($table_name='',$id=0) {
  $def_folder='/';
  switch ($table_name) {   
    case 'gks_assets': $def_folder='/assets/'.$id; break;  
    case 'gks_eshop_products': $def_folder='/products/'.$id; break; 
    case 'gks_eshop_products_brands': $def_folder='/brands/'.$id; break; 
    case 'gks_eshop_products_categories': $def_folder='/categories/'.$id; break; 
    case 'gks_transfer_oxima_type': $def_folder='/oxima_type/'.$id; break; 
    case 'gks_hotel_room': $def_folder='/room/'.$id; break; 
    case 'gks_hotel_room_type': $def_folder='/room_type/'.$id; break; 
    case 'wp_users': $def_folder='/users-photo/'.$id; break; 
  }
?>
<div style="clear: both;"></div>
<div id="f_button_add_files_photo" class="fileinput-button"  href="#" data-options="thumbnail: ''">
  <div id="f_button_add_files_photo_buttons">
    <span>
      <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-upload"></i> <?php echo gks_lang('Μεταφόρτωση αρχείων');?></button>
      <input type="file" name="files[]" multiple>    
    </span>
    <?php if ($table_name!='gks_users'){?>
    <span class="btn btn-sm btn-primary" id="gks_filesexplore_start_from_item_photos" data-def_folder="<?php echo $def_folder;?>"><i class="fa-solid fa-file-lines"></i> <?php echo gks_lang('Εξερεύνηση αρχείων');?></span>
    <?php } ?>
  </div>
  <div id="f_button_add_files_photo_info">
    <?php echo gks_lang('Μέγιστο μέγεθος');?> <?php echo gks_get_max_upload_file_size();?>. <?php echo gks_lang('Τύποι αρχείων');?> <?php echo implode(' ',GKS_IMAGE_EXTENSION);?>
  </div>
</div>
<div id="progress-bar_photo" style="margin-top:10px; display:none;background: rgb(230,230,230);">
  <div class="bar_photo" style="padding-top:0px;padding-bottom:0px;width: 0%;height: 20px;background: green;"></div>
</div>
<div id="progress-extended_photo" style="display:none;">&nbsp;</div>
<?php
}


