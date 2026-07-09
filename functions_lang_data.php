<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



function gks_sqlfl($field,$asname='',$notas=false) {
//  global $_gks_session;
//  $load_lang='el-GR';
//  if (isset($_gks_session['gks']['ui_lang'])) $load_lang = gks_erp_supperted_lang($_gks_session['gks']['ui_lang']);

  global $gks_user_settings;
  global $GKS_LANG_DEFAULT;
  $load_lang=$GKS_LANG_DEFAULT;
  if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);

  
  $load_lang=str_replace('-', '_',$load_lang);
  if ($load_lang == 'el_GR') {
    $ret= $field;
    if ($notas==false) $ret.=" as ".($asname!='' ? $asname : $field);
    return $ret;
  }
  
  $ret= "IF(".$field."_".$load_lang." is not null and ".$field."_".$load_lang."!='',".$field."_".$load_lang.",".$field.")";
  if ($notas==false) $ret.=" as ".($asname!='' ? $asname : $field);
  return $ret;
  
}


/*

  LEFT JOIN (
    SELECT country_id, country_name as country_name_en_US FROM gks_country_lang WHERE lang_code='en-US'
  ) AS gks_country_en_US ON gks_country.id_country = gks_country_en_US.country_id)  

  LEFT JOIN (
    SELECT nomos_id, nomos_descr as nomos_descr_en_US FROM gks_nomoi_lang WHERE lang_code='en-US'
  ) AS gks_nomoi_en_US ON gks_nomoi.id_nomos = gks_nomoi_en_US.nomos_id)  


  LEFT JOIN (
    SELECT hotel_floor_id, floor_descr as floor_descr_en_US FROM gks_hotel_floor_lang WHERE lang_code='en-US'
  ) AS gks_hotel_floor_en_US ON gks_hotel_floor.id_hotel_floor = gks_hotel_floor_en_US.hotel_floor_id  

  LEFT JOIN (
    SELECT hotel_id, 
    hotel_template_eidos_descr as hotel_template_eidos_descr_en_US,
    hotel_template_efd_descr as hotel_template_efd_descr_en_US,
    hotel_template_woo_descr as hotel_template_woo_descr_en_US
    FROM gks_hotel_lang WHERE lang_code='en-US'
  ) AS gks_hotel_en_US ON gks_hotel.id_hotel = gks_hotel_en_US.hotel_id  

  LEFT JOIN (
    SELECT hotel_room_id, room_descr as room_descr_en_US FROM gks_hotel_room_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id  

  LEFT JOIN (
    SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id  

  LEFT JOIN (
    SELECT hotel_room_type_subroom_id, subroom_descr as subroom_descr_en_US FROM gks_hotel_room_type_subroom_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_type_subroom_en_US ON gks_hotel_room_type_subroom.id_hotel_room_type_subroom = gks_hotel_room_type_subroom_en_US.hotel_room_type_subroom_id  

  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id

  LEFT JOIN (
    SELECT poi_type_id, poi_type_descr as poi_type_descr_en_US FROM gks_poi_type_lang WHERE lang_code='en-US'
  ) AS gks_poi_type_en_US ON gks_poi_type.id_poi_type = gks_poi_type_en_US.poi_type_id

  LEFT JOIN (
    SELECT transfer_oxima_type_id, 
    transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
    transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
    FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
  ) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id

  
  LEFT JOIN (
    SELECT lang_idd, lang_name as lang_name_en_US FROM gks_lang_lang WHERE lang_code='en-US'
  ) AS gks_lang_en_US ON gks_lang.idd_lang = gks_lang_en_US.lang_idd)


if ($transfer_area_descr_en_US=='') {debug_mail(false,'transfer_area_descr_en_US',$transfer_area_descr_en_US);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή (Αγγλικά)')));
  echo json_encode($return); die(); }

$sql="select * from gks_transfer_area where transfer_area_descr_en_US like '".$db_link->escape_string($transfer_area_descr_en_US)."' and id_transfer_area<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η περιοχή Transfer με περιγραφή (Αγγλικά) <b>[1]</b> υπάρχει ήδη').':';
  $message=str_replace('[1]',$transfer_area_descr_en_US,$message);
  $message.='<br><a href="admin-transfer-area-item.php?id='.$row['id_transfer_area'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  
  debug_mail(false,'transfer-area exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


SELECT table_name,column_name FROM information_schema.`COLUMNS`
where table_schema='test_easyfilesselection_com'
and column_name like '%en_US'
order by table_name,column_name;

function gks_sqlfl 

    'gks_country', 'country_name_en_US'
    'gks_nomoi', 'nomos_descr_en_US'
    'gks_perifereies', 'perifereia_descr_en_US'
    'gks_delivery_methods', 'delivery_method_html_en_US'
    'gks_delivery_methods', 'delivery_method_name_en_US'
    'gks_delivery_methods', 'delivery_method_sxolio_en_US'
    'gks_delivery_methods', 'delivery_method_tooltip_en_US'
    'gks_payment_acquirers', 'payment_acquirer_button_html_en_US'
    'gks_payment_acquirers', 'payment_acquirer_html_en_US'
    'gks_payment_acquirers', 'payment_acquirer_name_en_US'
    'gks_payment_acquirers', 'payment_acquirer_sxolio_en_US'
    'gks_payment_acquirers', 'payment_acquirer_tooltip_en_US'
    'gks_hotel', 'hotel_template_efd_descr_en_US'
    'gks_hotel', 'hotel_template_eidos_descr_en_US'
    'gks_hotel', 'hotel_template_woo_descr_en_US'
    'gks_hotel_floor', 'floor_descr_en_US'
    'gks_hotel_room', 'room_descr_en_US'
    'gks_hotel_room_type', 'room_type_descr_en_US'
    'gks_hotel_room_type_subroom', 'subroom_descr_en_US'
    'gks_newsletter_lists', 'newsletter_list_title_en_US'
    'gks_poi', 'poi_descr_en_US'
    'gks_poi_type', 'poi_type_descr_en_US'
    'gks_transfer_area', 'transfer_area_descr_en_US'

    'gks_transfer_oxima_type', 'transfer_oxima_type_descr_en_US'
    'gks_transfer_oxima_type', 'transfer_oxima_type_site_text_en_US'

    'gks_lang', 'lang_name_en_US'
    
gks_eshop_products


*/


$GKS_LANG_DATA_ARRAY=false;

function gks_build_GKS_LANG_DATA_ARRAY() {
  global $GKS_LANG_DATA_ENABLED;
  global $GKS_LANG_DATA_ARRAY;
  global $GKS_LANG_DEFAULT;
  global $db_link;
  
  if ($GKS_LANG_DATA_ARRAY===false) {
    //echo 'run only once';
    $sql_in=[];//$sql_in['el-GR']="'el-GR'";
    foreach ($GKS_LANG_DATA_ENABLED as $value) {
      if ($value!=$GKS_LANG_DEFAULT) {
        if (isset($sql_in[$value])==false) $sql_in[$value]="'".$value."'";
      }
    } 
    $GKS_LANG_DATA_ARRAY=[];
    if (count($sql_in)>0) {
      $sql="select * from gks_lang where id_lang in (".implode(',',$sql_in).") and lang_on_backend=1 order by lang_sortorder";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $ret['message']='sql error'; return $ret;}
      
      while ($row = $result->fetch_assoc()) {
        $GKS_LANG_DATA_ARRAY[]=array(
          'id_lang' => $row['id_lang'],
          'lang_name' => $row['lang_name'],
        );
      }
    }
    //print '<pre>'.$sql;print_r($sql_in);print_r($GKS_LANG_DATA_ARRAY);die();
  }  
}

function gks_lang_data_obj_prepare($table_name, $html_type, $fixed_lang='') {
  global $GKS_LANG_DATA_ARRAY;
  global $db_link;
  global $GKS_PRODUCT_DESCR_BIG;
  global $GKS_PRODUCT_DESCR_SMALL;
  
  $ret=array(
    'success' => false, 
    'message' => 'generic error gks_lang_data_obj_prepare',
    'table_name'=>$table_name,
    'table_name_lang'=>$table_name,
    'html_type'=>$html_type,
    'html_template'=>'',
  );
    

  if ($fixed_lang=='') gks_build_GKS_LANG_DATA_ARRAY();
 
  if (in_array($table_name,gks_lang_data_tables())==false) {
    echo '<pre>add table '.$table_name.'to gks_lang_data_tables function';die();
  }

  switch ($table_name) { 
    case 'gks_aade_skopos_diakinisis':
      $ret['table_name_lang']='gks_aade_skopos_diakinisis_lang';
      $ret['id_table']='id_aade_skopos_diakinisis_lang';
      $ret['rec_id']='aade_skopos_diakinisis_id';
      $ret['id_parent']='id_aade_skopos_diakinisis';
      $ret['fields_array']=array(
        array('field_name' => 'aade_skopos_diakinisis_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_aade_katigoria_fpa_ejeresi':
      $ret['table_name_lang']='gks_aade_katigoria_fpa_ejeresi_lang';
      $ret['id_table']='id_aade_katigoria_fpa_ejeresi_lang';
      $ret['rec_id']='aade_katigoria_fpa_ejeresi_id';
      $ret['id_parent']='id_aade_katigoria_fpa_ejeresi';
      $ret['fields_array']=array(
        array('field_name' => 'aade_katigoria_fpa_ejeresi_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_aade_katigoria_parakratoumemenon_foron':
      $ret['table_name_lang']='gks_aade_katigoria_parakratoumemenon_foron_lang';
      $ret['id_table']='id_aade_katigoria_parakratoumemenon_foron_lang';
      $ret['rec_id']='aade_katigoria_parakratoumemenon_foron_id';
      $ret['id_parent']='id_aade_katigoria_parakratoumemenon_foron';
      $ret['fields_array']=array(
        array('field_name' => 'aade_katigoria_parakratoumemenon_foron_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_aade_katigoria_loipon_foron':
      $ret['table_name_lang']='gks_aade_katigoria_loipon_foron_lang';
      $ret['id_table']='id_aade_katigoria_loipon_foron_lang';
      $ret['rec_id']='aade_katigoria_loipon_foron_id';
      $ret['id_parent']='id_aade_katigoria_loipon_foron';
      $ret['fields_array']=array(
        array('field_name' => 'aade_katigoria_loipon_foron_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_aade_katigoria_xartosimou':
      $ret['table_name_lang']='gks_aade_katigoria_xartosimou_lang';
      $ret['id_table']='id_aade_katigoria_xartosimou_lang';
      $ret['rec_id']='aade_katigoria_xartosimou_id';
      $ret['id_parent']='id_aade_katigoria_xartosimou';
      $ret['fields_array']=array(
        array('field_name' => 'aade_katigoria_xartosimou_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_aade_katigoria_telon':
      $ret['table_name_lang']='gks_aade_katigoria_telon_lang';
      $ret['id_table']='id_aade_katigoria_telon_lang';
      $ret['rec_id']='aade_katigoria_telon_id';
      $ret['id_parent']='id_aade_katigoria_telon';
      $ret['fields_array']=array(
        array('field_name' => 'aade_katigoria_telon_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;      
    case 'gks_acc_eidi_parastatikon':
      $ret['table_name_lang']='gks_acc_eidi_parastatikon_lang';
      $ret['id_table']='id_acc_eidos_parastatikou_lang';
      $ret['rec_id']='acc_eidos_parastatikou_id';
      $ret['id_parent']='id_acc_eidos_parastatikou';
      $ret['fields_array']=array(
        array('field_name' => 'eidos_parastatikou_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;    
    case 'gks_acc_eidi_parastatikon_types':
      $ret['table_name_lang']='gks_acc_eidi_parastatikon_types_lang';
      $ret['id_table']='id_acc_eidi_parastatikon_type_lang';
      $ret['rec_id']='acc_eidi_parastatikon_type_id';
      $ret['id_parent']='id_acc_eidi_parastatikon_type';
      $ret['fields_array']=array(
        array('field_name' => 'acc_eidi_parastatikon_type_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
        array('field_name' => 'antisimvalomenos_label', 'field_label'=>gks_lang('Ετικέτα'), 'field_type'=> 'text'),
      );
      break;      
    case 'gks_assets':
      $ret['table_name_lang']='gks_assets_lang';
      $ret['id_table']='id_asset_lang';
      $ret['rec_id']='asset_id';
      $ret['id_parent']='id_asset';
      $ret['fields_array']=array(
        array('field_name' => 'asset_title', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break;  
    case 'gks_banks':
      $ret['table_name_lang']='gks_banks_lang';
      $ret['id_table']='id_bank_lang';
      $ret['rec_id']='bank_id';
      $ret['id_parent']='id_bank';
      $ret['fields_array']=array(
        array('field_name' => 'bank_descr', 'field_label'=>gks_lang('Τράπεζα'), 'field_type'=> 'text'),
      );
      break;
    case 'gks_country':
      $ret['table_name_lang']='gks_country_lang';
      $ret['id_table']='id_country_lang';
      $ret['rec_id']='country_id';
      $ret['id_parent']='id_country';
      $ret['fields_array']=array(
        array('field_name' => 'country_name', 'field_label'=>gks_lang('Χώρα'), 'field_type'=> 'text'),
      );
      break;
      
    case 'gks_eshop_fiscal_position':
      $ret['table_name_lang']='gks_eshop_fiscal_position_lang';
      $ret['id_table']='id_fiscal_position_lang';
      $ret['rec_id']='fiscal_position_id';
      $ret['id_parent']='id_fiscal_position';
      $ret['fields_array']=array(
        array('field_name' => 'fiscal_position_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break; 

    case 'gks_eshop_pricelist':
      $ret['table_name_lang']='gks_eshop_pricelist_lang';
      $ret['id_table']='id_pricelist_lang';
      $ret['rec_id']='pricelist_id';
      $ret['id_parent']='id_pricelist';
      $ret['fields_array']=array(
        array('field_name' => 'pricelist_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'),
      );
      break; 
      
    case 'gks_nomoi':
      $ret['table_name_lang']='gks_nomoi_lang';
      $ret['id_table']='id_nomos_lang';
      $ret['rec_id']='nomos_id';
      $ret['id_parent']='id_nomos';
      $ret['fields_array']=array(
        array('field_name' => 'nomos_descr', 'field_label'=>gks_lang('Νομός'), 'field_type'=> 'text'), 
      );
      
      break;
      

    case 'gks_delivery_methods':
      $ret['table_name_lang']='gks_delivery_methods_lang';
      $ret['id_table']='id_delivery_method_lang';
      $ret['rec_id']='delivery_method_id';
      $ret['id_parent']='id_delivery_method';
      $ret['fields_array']=array(
        array('field_name' => 'delivery_method_name', 'field_label'=>gks_lang('Τρόπος Αποστολής'), 'field_type'=> 'text'),
        array('field_name' => 'delivery_method_html', 'field_label'=>gks_lang('HTML (FrontEnd)'), 'field_type'=> 'text'),
        array('field_name' => 'delivery_method_sxolio', 'field_label'=>gks_lang('Σχόλιο'), 'field_type'=> 'textarea'),
        array('field_name' => 'delivery_method_tooltip', 'field_label'=>gks_lang('Επεξήγηση'), 'field_type'=> 'textarea'),
      );
      break;
    case 'gks_payment_acquirers':
      $ret['table_name_lang']='gks_payment_acquirers_lang';
      $ret['id_table']='id_payment_acquirer_lang';
      $ret['rec_id']='payment_acquirer_id';
      $ret['id_parent']='id_payment_acquirer';
      $ret['fields_array']=array(
        array('field_name' => 'payment_acquirer_name', 'field_label'=>gks_lang('Τρόπος Πληρωμής'), 'field_type'=> 'text'),
        array('field_name' => 'payment_acquirer_html', 'field_label'=>gks_lang('HTML (FrontEnd)'), 'field_type'=> 'text'),
        array('field_name' => 'payment_acquirer_button_html', 'field_label'=>gks_lang('Κουμπί (FrontEnd)'), 'field_type'=> 'text'),
        array('field_name' => 'payment_acquirer_sxolio', 'field_label'=>gks_lang('Σχόλιο'), 'field_type'=> 'textarea'),
        array('field_name' => 'payment_acquirer_tooltip', 'field_label'=>gks_lang('Επεξήγηση'), 'field_type'=> 'textarea'),
      );
      break;

    case 'gks_hotel':
      $ret['table_name_lang']='gks_hotel_lang';
      $ret['id_table']='id_hotel_lang';
      $ret['rec_id']='hotel_id';
      $ret['id_parent']='id_hotel';
      $ret['fields_array']=array(
        array('field_name' => 'hotel_template_eidos_descr', 'field_label'=>gks_lang('Περιγραφή είδους για παραστατικό'), 'field_type'=> 'textarea'),
        array('field_name' => 'hotel_template_woo_descr', 'field_label'=>gks_lang('Περιγραφή είδους για WooCommerce'), 'field_type'=> 'textarea'),
        array('field_name' => 'hotel_template_efd_descr', 'field_label'=>gks_lang('Περιγραφή είδους για φόρο διαμονής'), 'field_type'=> 'textarea'),
      );
      break;



      
    case 'gks_hotel_floor':
      $ret['table_name_lang']='gks_hotel_floor_lang';
      $ret['id_table']='id_hotel_floor_lang';
      $ret['rec_id']='hotel_floor_id';
      $ret['id_parent']='id_hotel_floor';
      $ret['fields_array']=array(
        array('field_name' => 'floor_descr', 'field_label'=>gks_lang('Όροφος'), 'field_type'=> 'text'), 
      );
      break;
    case 'gks_hotel_room':
      $ret['table_name_lang']='gks_hotel_room_lang';
      $ret['id_table']='id_hotel_room_lang';
      $ret['rec_id']='hotel_room_id';
      $ret['id_parent']='id_hotel_room';
      $ret['fields_array']=array(
        array('field_name' => 'room_descr', 'field_label'=>gks_lang('Δωμάτιο'), 'field_type'=> 'text'), 
      );
      break;
    case 'gks_hotel_room_type':
      $ret['table_name_lang']='gks_hotel_room_type_lang';
      $ret['id_table']='id_hotel_room_type_lang';
      $ret['rec_id']='hotel_room_type_id';
      $ret['id_parent']='id_hotel_room_type';
      $ret['fields_array']=array(
        array('field_name' => 'room_type_descr', 'field_label'=>gks_lang('Τύπος δωματίου'), 'field_type'=> 'text'), 
      );
      break;
    case 'gks_hotel_room_type_subroom':
      $ret['table_name_lang']='gks_hotel_room_type_subroom_lang';
      $ret['id_table']='id_hotel_room_type_subroom_lang';
      $ret['rec_id']='hotel_room_type_subroom_id';
      $ret['id_parent']='id_hotel_room_type_subroom';
      $ret['fields_array']=array(
        array('field_name' => 'subroom_descr', 'field_label'=>gks_lang('Όνομα δωματίου'), 'field_type'=> 'text'), 
      );
      break;
    case 'gks_newsletter_lists':
      $ret['table_name_lang']='gks_newsletter_lists_lang';
      $ret['id_table']='id_newsletter_list_lang';
      $ret['rec_id']='newsletter_list_id';
      $ret['id_parent']='id_newsletter_list';
      $ret['fields_array']=array(
        array('field_name' => 'newsletter_list_title', 'field_label'=>gks_lang('Λίστα'), 'field_type'=> 'text'), 
      );
      break;
    case 'gks_poi':
      $ret['table_name_lang']='gks_poi_lang';
      $ret['id_table']='id_poi_lang';
      $ret['rec_id']='poi_id';
      $ret['id_parent']='id_poi';
      $ret['fields_array']=array(
        array('field_name' => 'poi_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'), 
      );
      break;      
    case 'gks_poi_type':
      $ret['table_name_lang']='gks_poi_type_lang';
      $ret['id_table']='id_poi_type_lang';
      $ret['rec_id']='poi_type_id';
      $ret['id_parent']='id_poi_type';
      $ret['fields_array']=array(
        array('field_name' => 'poi_type_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'), 
      );
      break;   
      
    case 'gks_transfer':
      $ret['table_name_lang']='gks_transfer_lang';
      $ret['id_table']='id_transfer_lang';
      $ret['rec_id']='transfer_id';
      $ret['id_parent']='id_transfer';
      $ret['fields_array']=array(
        array('field_name' => 'transfer_template_eidos_descr', 'field_label'=>gks_lang('Περιγραφή είδους για παραστατικό'), 'field_type'=> 'textarea'),
        array('field_name' => 'transfer_template_woo_descr', 'field_label'=>gks_lang('Περιγραφή είδους για WooCommerce'), 'field_type'=> 'textarea'),
      );
      break;
               
    case 'gks_transfer_area':
      $ret['table_name_lang']='gks_transfer_area_lang';
      $ret['id_table']='id_transfer_area_lang';
      $ret['rec_id']='transfer_area_id';
      $ret['id_parent']='id_transfer_area';
      $ret['fields_array']=array(
        array('field_name' => 'transfer_area_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'), 
      );
      break;      
    case 'gks_transfer_oxima_type':
      $ret['table_name_lang']='gks_transfer_oxima_type_lang';
      $ret['id_table']='id_transfer_oxima_type_lang';
      $ret['rec_id']='transfer_oxima_type_id';
      $ret['id_parent']='id_transfer_oxima_type';
      $ret['fields_array']=array(
        array('field_name' => 'transfer_oxima_type_descr', 'field_label'=>gks_lang('Περιγραφή'), 'field_type'=> 'text'), 
        array('field_name' => 'transfer_oxima_type_site_text',  'field_label'=>gks_lang('Σχόλιο για site'), 'field_type'=> 'tinymce'), 
      );
      break; 

    case 'gks_lang':
      $ret['table_name_lang']='gks_lang_lang';
      $ret['id_table']='id_lang_lang';
      $ret['rec_id']='lang_idd';
      $ret['id_parent']='idd_lang';
      $ret['fields_array']=array(
        array('field_name' => 'lang_name', 'field_label'=>gks_lang('Γλώσσα'), 'field_type'=> 'text'), 
      );
      break; 

    case 'gks_eshop_products':
      $ret['table_name_lang']='gks_eshop_products_lang';
      $ret['id_table']='id_product_lang';
      $ret['rec_id']='product_id';
      $ret['id_parent']='id_product';
      $ret['fields_array']=array(
        array('field_name' => 'product_descr',           'field_label'=>gks_lang('Περιγραφή'),        'field_type'=> 'text'), 
        array('field_name' => 'product_descr_variable',  'field_label'=>gks_lang('Παραλλαγή'),        'field_type'=> 'text'), 
        array('field_name' => 'product_def_comments',    'field_label'=>gks_lang('Σχόλιο για παραγγελία, παραστατικό, δελτίο'), 'field_type'=> 'textarea'), 
      );
      if ($GKS_PRODUCT_DESCR_BIG) 
        $ret['fields_array'][]=array('field_name' => 'product_descr_big',       'field_label'=>gks_lang('Μεγάλη Περιγραφή'), 'field_type'=> 'tinymce');
      if ($GKS_PRODUCT_DESCR_SMALL)
        $ret['fields_array'][]=array('field_name' => 'product_descr_small',     'field_label'=>gks_lang('Μικρή Περιγραφή'),  'field_type'=> 'tinymce');

      break; 
      
    case 'gks_product_idiotites':
      $ret['table_name_lang']='gks_product_idiotites_lang';
      $ret['id_table']='id_product_idiotita_lang';
      $ret['rec_id']='product_idiotita_id';
      $ret['id_parent']='id_product_idiotita';
      $ret['fields_array']=array(
        array('field_name' => 'idiotita_name',   'field_label'=>gks_lang('Όνομα Ιδιότητας'),        'field_type'=> 'text'), 
        array('field_name' => 'idiotita_descr',  'field_label'=>gks_lang('Περιγραφή Ιδιότητας'),    'field_type'=> 'tinymce'), 
      );
      break; 
    case 'gks_product_idiotites_terms':
      $ret['table_name_lang']='gks_product_idiotites_terms_lang';
      $ret['id_table']='id_product_idiotita_term_lang';
      $ret['rec_id']='product_idiotita_term_id';
      $ret['id_parent']='id_product_idiotita_term';
      $ret['fields_array']=array(
        array('field_name' => 'idiotita_term_name',   'field_label'=>gks_lang('Όνομα Όρου'),        'field_type'=> 'text'), 
        array('field_name' => 'idiotita_term_descr',  'field_label'=>gks_lang('Περιγραφή Όρου'),    'field_type'=> 'tinymce'), 
      );
      break; 
    case 'gks_eshop_products_categories':
      $ret['table_name_lang']='gks_eshop_products_categories_lang';
      $ret['id_table']='id_product_category_lang';
      $ret['rec_id']='product_category_id';
      $ret['id_parent']='id_product_category';
      $ret['fields_array']=array(
        array('field_name' => 'product_category_descr',   'field_label'=>gks_lang('Κατηγορία'),        'field_type'=> 'text'), 
        array('field_name' => 'category_comments',        'field_label'=>gks_lang('Περιγραφή'),    'field_type'=> 'tinymce'), 
      );
      break; 
    case 'gks_eshop_products_brands':
      $ret['table_name_lang']='gks_eshop_products_brands_lang';
      $ret['id_table']='id_product_brand_lang';
      $ret['rec_id']='product_brand_id';
      $ret['id_parent']='id_product_brand';
      $ret['fields_array']=array(
        array('field_name' => 'product_brand_descr',   'field_label'=>gks_lang('Μάρκα'),        'field_type'=> 'text'), 
        array('field_name' => 'brand_comments',        'field_label'=>gks_lang('Περιγραφή'),    'field_type'=> 'tinymce'), 
      );
      break; 
    case 'gks_acc_journal':
      $ret['table_name_lang']='gks_acc_journal_lang';
      $ret['id_table']='id_acc_journal_lang';
      $ret['rec_id']='acc_journal_id';
      $ret['id_parent']='id_acc_journal';
      $ret['fields_array']=array(
        array('field_name' => 'acc_journal_descr',   'field_label'=>gks_lang('Περιγραφή'),        'field_type'=> 'text'), 
      );
      break; 
    case 'gks_acc_seires':
      $ret['table_name_lang']='gks_acc_seires_lang';
      $ret['id_table']='id_acc_seira_lang';
      $ret['rec_id']='acc_seira_id';
      $ret['id_parent']='id_acc_seira';
      $ret['fields_array']=array(
        array('field_name' => 'seira_descr',   'field_label'=>gks_lang('Περιγραφή'),        'field_type'=> 'text'), 
        array('field_name' => 'seira_comments','field_label'=>gks_lang('Σχόλιο'),           'field_type'=> 'textarea'), 
      );
      break; 
    case 'gks_company':
      $ret['table_name_lang']='gks_company_lang';
      $ret['id_table']='id_company_lang';
      $ret['rec_id']='company_id';
      $ret['id_parent']='id_company';
      $ret['fields_array']=array(
        array('field_name' => 'company_title',   'field_label'=>gks_lang('Διακριτικός Τίτλος'),        'field_type'=> 'text'), 
        array('field_name' => 'company_tagline','field_label'=>gks_lang('Το μότο μου'),           'field_type'=> 'text'), 
        array('field_name' => 'company_eponimia','field_label'=>gks_lang('Επωνυμία'),           'field_type'=> 'text'), 
        array('field_name' => 'company_doy','field_label'=>gks_lang('ΔΟΥ'),           'field_type'=> 'text'), 
        array('field_name' => 'company_epaggelma','field_label'=>gks_lang('Επάγγελμα'),           'field_type'=> 'text'), 
        array('field_name' => 'company_phone','field_label'=>gks_lang('Σταθερό Τηλέφωνο'),           'field_type'=> 'text'), 
        array('field_name' => 'company_odos','field_label'=>gks_lang('Οδός'),           'field_type'=> 'text'), 
        array('field_name' => 'company_arithmos','field_label'=>gks_lang('Αριθμός'),           'field_type'=> 'text'), 
        array('field_name' => 'company_orofos','field_label'=>gks_lang('Όροφος'),           'field_type'=> 'text'), 
        array('field_name' => 'company_perioxi','field_label'=>gks_lang('Περιοχή'),           'field_type'=> 'text'), 
        array('field_name' => 'company_poli','field_label'=>gks_lang('Πόλη'),           'field_type'=> 'text'), 
      );
      break; 
    case 'gks_company_subs':
      $ret['table_name_lang']='gks_company_subs_lang';
      $ret['id_table']='id_company_sub_lang';
      $ret['rec_id']='company_sub_id';
      $ret['id_parent']='id_company_sub';
      $ret['fields_array']=array(
        array('field_name' => 'company_sub_title',   'field_label'=>gks_lang('Διακριτικός Τίτλος'),        'field_type'=> 'text'), 
        array('field_name' => 'company_sub_tagline','field_label'=>gks_lang('Το μότο μου'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_eponimia','field_label'=>gks_lang('Επωνυμία'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_phone','field_label'=>gks_lang('Σταθερό Τηλέφωνο'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_odos','field_label'=>gks_lang('Οδός'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_arithmos','field_label'=>gks_lang('Αριθμός'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_orofos','field_label'=>gks_lang('Όροφος'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_perioxi','field_label'=>gks_lang('Περιοχή'),           'field_type'=> 'text'), 
        array('field_name' => 'company_sub_poli','field_label'=>gks_lang('Πόλη'),           'field_type'=> 'text'), 
      );
      break; 
    case 'gks_warehouses':
      $ret['table_name_lang']='gks_warehouses_lang';
      $ret['id_table']='id_warehouse_lang';
      $ret['rec_id']='warehouse_id';
      $ret['id_parent']='id_warehouse';
      $ret['fields_array']=array(
        array('field_name' => 'warehouse_name',    'field_label'=>gks_lang('Τίτλος'),        'field_type'=> 'text'), 
        array('field_name' => 'warehouse_topos_fortosis','field_label'=>gks_lang('ως Τόπος Φόρτωσης'),'field_type'=> 'text'), 
        array('field_name' => 'warehouse_phone',   'field_label'=>gks_lang('Σταθερό Τηλέφωνο'),           'field_type'=> 'text'), 
        array('field_name' => 'warehouse_odos',    'field_label'=>gks_lang('Οδός'),           'field_type'=> 'text'), 
        array('field_name' => 'warehouse_arithmos','field_label'=>gks_lang('Αριθμός'),           'field_type'=> 'text'), 
        array('field_name' => 'warehouse_orofos',  'field_label'=>gks_lang('Όροφος'),           'field_type'=> 'text'), 
        array('field_name' => 'warehouse_perioxi', 'field_label'=>gks_lang('Περιοχή'),           'field_type'=> 'text'), 
        array('field_name' => 'warehouse_poli',    'field_label'=>gks_lang('Πόλη'),           'field_type'=> 'text'), 
      );
      break; 
    case 'gks_monades_metrisis':
      $ret['table_name_lang']='gks_monades_metrisis_lang';
      $ret['id_table']='id_monada_lang';
      $ret['rec_id']='monada_id';
      $ret['id_parent']='id_monada';
      $ret['fields_array']=array(
        array('field_name' => 'monada_descr',  'field_label'=>gks_lang('Μονάδα Μέρησης'),    'field_type'=> 'text'), 
        array('field_name' => 'monada_symbol', 'field_label'=>gks_lang('Σύμβολο'),           'field_type'=> 'text'), 
      );
      break; 


      
      
    default:
      $ret['message']='gks_lang_data_obj_prepare table_name not found';
      debug_mail(false,$ret['message'],'');
      return $ret;
  }

  if ($fixed_lang=='') {
    switch ($html_type) {   
      case 'none':
        $ret['html_template']='';
        break;
      case 'default':
        $ret['html_template']=
        '<div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="[[field_name]]_[[lang_code]][[suffix_id]]">[[field_label]] ([[lang_name]]):</label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input[[suffix_class]]" id="[[field_name]]_[[lang_code]][[suffix_id]]"  value="[[value]]">
          </div>
        </div>';
        $ret['html_template_textarea']=
        '<div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="[[field_name]]_[[lang_code]][[suffix_id]]">[[field_label]] ([[lang_name]]):</label>
          <div class="col-sm-8">
            <textarea type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input_textarea[[suffix_class]] " id="[[field_name]]_[[lang_code]][[suffix_id]]"  style="min-height:100px;height:100px;" >[[value]]</textarea>
          </div>
        </div>';
        $ret['html_template_tinymce']=
        '<div class="form-group row">
          <label class="col-sm-12 col-form-label form-control-sm text-sm-right1" for="[[field_name]]_[[lang_code]][[suffix_id]]">[[field_label]] ([[lang_name]]):</label>
          <div class="col-sm-12">
            <textarea type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input_tinymce[[suffix_class]] gks_tinymce" id="[[field_name]]_[[lang_code]][[suffix_id]]"  style="height:200px;" >[[value]]</textarea>
          </div>
        </div>';
        
        break;
      default:
        $ret['message']='gks_lang_data_obj_prepare html_type not found';
        debug_mail(false,$ret['message'],'');
        return $ret;
    }
  }
  $ret['success']=true;
  $ret['message']='OK';
  
  return $ret;
}

function gks_lang_data_obj_render_html(&$prepare, $row, $render_fields, $force_read_from_db=false, $suffix_id='',$suffix_class='') {
  global $GKS_LANG_DATA_ARRAY;
  global $db_link;
  //echo '<pre>';print_r($prepare['data']); echo '</pre>';
  
  if (isset($prepare['data'])==false or $force_read_from_db==true) {
    //echo 'ffffff ';
    $sql="select * from ".$prepare['table_name_lang']." where lang_code<>'' and ".$prepare['rec_id']."=".$row[$prepare['id_parent']];
    //$html.=$sql;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql); return 'sql error';}
    
    $data=[];
    while ($row = $result->fetch_assoc()) {
      $data[$row['lang_code']]=$row;
    }
    $prepare['data']=$data;
  } else {
    $data=$prepare['data'];
  }
  //print '<pre>';print_r($data);die();
  //print '<pre>';print_r($GKS_LANG_DATA_ARRAY);die();

  $html='';
  if ($prepare['html_template']!='') {
    foreach ($prepare['fields_array'] as $field_item) {
      if (count($render_fields)==0 or in_array($field_item['field_name'],$render_fields)) {
        foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
          $field_name='';
          //print '<pre>';print_r($GKS_LANG_DATA_ARRAY);die();
          $html_template='';
          if ($field_item['field_type']=='text') $html_template=$prepare['html_template'];
          else if ($field_item['field_type']=='textarea') $html_template=$prepare['html_template_textarea'];
          else if ($field_item['field_type']=='tinymce') $html_template=$prepare['html_template_tinymce'];
          
          $html_template=str_replace('[[suffix_class]]',$suffix_class, $html_template);
          $html_template=str_replace('[[suffix_id]]',$suffix_id, $html_template);
          $html_template=str_replace('[[lang_name]]',$lang_item['lang_name'], $html_template);
          $html_template=str_replace('[[lang_code]]', $lang_item['id_lang'], $html_template);
          $html_template=str_replace('[[field_name]]', $field_item['field_name'], $html_template);
          $html_template=str_replace('[[field_label]]', $field_item['field_label'], $html_template);
          
          if (isset($data[$lang_item['id_lang']])) { //iparxei eggrafi gia aytin thn glossa
            $html_template=str_replace('[[value]]', htmlspecialchars_gks($data[$lang_item['id_lang']][ $field_item['field_name'] ]), $html_template);
            
          } else {//den iparxei, vale kena
            $html_template=str_replace('[[value]]', '', $html_template);
            
          }
          $html.=$html_template;
          
          //$html.=$lang_item['id_lang'].'|'.$field_item['field_name'].'|'.print_r($data[$lang_item['id_lang']],true);
          
        }
      }
    }
  }
  
  return $html;
}

function gks_lang_data_obj_save(&$prepare,$id,$mypost) {
  global $GKS_LANG_DATA_ARRAY;
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $ret=array(
    'success' => false, 
    'message' => 'generic error gks_lang_data_obj_save',
  ); 
   
  //print '<pre>';print_r($GKS_LANG_DATA_ARRAY);die();
  //print '<pre>';print_r($mypost);die();
  
  foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
    $val_array=array();
    foreach ($prepare['fields_array'] as $field_item) {
      $post_key=$field_item['field_name'].'_'.$lang_item['id_lang'];
      $post_value='';if (isset($mypost[$post_key])) $post_value=trim_gks(base64_decode($mypost[$post_key]));
      $val_array[]=array(
        'name' => $field_item['field_name'],
        'value' => $post_value,
      );
    }
    //echo '<pre>';print_r($val_array);die();
    
    $sql="select * from ".$prepare['table_name_lang']." where lang_code='".$db_link->escape_string($lang_item['id_lang'])."' and ".$prepare['rec_id']."=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $ret['message']='sql error'; return $ret;}
    if ($result->num_rows==0) {
      $sqlF='';$sqlV='';
      foreach ($val_array as $val_item) {
        $sqlF.=$val_item['name'].",";
        $sqlV.="'".$db_link->escape_string($val_item['value'])."',";
      } 
      $sqlF=substr($sqlF, 0, strlen($sqlF)-1);
      $sqlV=substr($sqlV, 0, strlen($sqlV)-1);
      
      $sql="insert into ".$prepare['table_name_lang']." (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      lang_code,
      ".$prepare['rec_id'].",
      ".$sqlF."
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($lang_item['id_lang'])."',
      ".$id.",
      ".$sqlV."
      )";
      //print '<pre>'.$sql;die();
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $ret['message']='sql error'; return $ret;}
    } else {
      //echo '<pre>';print_r($val_array);die();
      $sqlFV='';
      foreach ($val_array as $val_item) {
        $sqlFV.=$val_item['name']."='".$db_link->escape_string($val_item['value'])."',";
      } 
      $sql="update ".$prepare['table_name_lang']." set 
      ".$sqlFV."
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where lang_code='".$db_link->escape_string($lang_item['id_lang'])."' and ".$prepare['rec_id']."=".$id;
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $ret['message']='sql error'; return $ret;}
      //echo '<pre>'.$sql;die();
    }
    //print '<pre>';print_r($val_array);die();
  }
  
  
  $ret['success']=true;
  $ret['message']='OK';
  
  return $ret;
}


/*
  en_US
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'],               'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    array('id' => $row['destination_data_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s_dest'),
  ));
  print '<pre>';print_r($row);die();
*/

$gks_lang_data_trans_cache=[];
function gks_lang_data_trans($def_val,$recid,$table_name,$field_name,$load_lang='') {
  global $db_link;
  global $GKS_LANG_DATA_ARRAY;
  global $GKS_LANG_DEFAULT;
  global $gks_user_settings;
  global $gks_lang_data_trans_cache;
  $def_val=trim_gks($def_val);
  if ($def_val=='') return $def_val;
  if ($table_name=='') return $def_val;
  
  if ($recid<=0) return $def_val;

  
  //echo '<pre>'.'|'.$def_val.'|'.$recid.'|'.$table_name.'|'.$field_name.'|'.$load_lang.'</pre>';
  
  if ($load_lang=='') {
    $load_lang=$GKS_LANG_DEFAULT;
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  $key=$table_name.'|'.$field_name.'|'.$recid.'|'.$load_lang;
  if (isset($gks_lang_data_trans_cache[$key])) return $gks_lang_data_trans_cache[$key];
  
  $prepare=gks_lang_data_obj_prepare($table_name,'none');
  if ($prepare['success']==false) {return $def_val;}
  
  $new_val=$def_val;
  //echo $load_lang;
  $sql="select ".$field_name." as myval 
  from ".$prepare['table_name_lang']."
  where ".$prepare['rec_id']."=".$recid."
  and lang_code='".$load_lang."'
  and ".$field_name."<>''";
  //echo $sql;die();
  $result = $db_link->query($sql); 
  if (!$result) { debug_mail(false,'error sql',$sql); return $def_val;}   
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $new_val=trim_gks($row['myval']);
  }
  //print '<pre>';print_r($prepare);die();
  $gks_lang_data_trans_cache[$key]=$new_val;
  //print '<pre>';print_r($gks_lang_data_trans_cache);print '</pre>';
  return $new_val;
}


function gks_lang_data_obj_insert_to_row(&$row,$table_name,$fields_out) {
  global $GKS_LANG_DATA_ARRAY;
  global $db_link;
  $ret=array('success' => false, 'message' => 'gks_lang_data_obj_insert_to_row generic error');
  
  $prepare=gks_lang_data_obj_prepare($table_name,'none');
  if ($prepare['success']==false) {$ret['message']=base64_encode($prepare['message']); echo json_encode($ret); die();}
  foreach ($fields_out as $f_item) {
    $sql_fd="select * from ".$prepare['table_name_lang']." where lang_code<>'' and ".$prepare['rec_id']."=".$f_item['id'];
    $result_fd = $db_link->query($sql_fd);        
    if (!$result_fd) {debug_mail(false,'error sql',$sql_fd); $ret['message']=base64_encode('error sql'); echo json_encode($ret); die();}
    $data=[];
    while ($row_fd = $result_fd->fetch_assoc()) {
      $data[$row_fd['lang_code']]=$row_fd;
    }
    //print '<pre>';print_r($data);die();
    
    foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
      $mykey=$f_item['new_field'];
      $mykey=str_replace('%s',str_replace('-','_', $lang_item['id_lang']),$mykey);
      $myval='';
      if (isset($data[$lang_item['id_lang']])) { //iparxei sxetiki eggrafh
        if (isset($data[$lang_item['id_lang']][$f_item['filename']])) { 
          $myval=trim_gks($data[$lang_item['id_lang']][$f_item['filename']]);
        }
      } 
      $row[$mykey]=$myval;
    }
  }
}


function gks_lang_data_obj_save_exec_php($table_name,$id) {
  $lang_data_obj=gks_lang_data_obj_prepare($table_name,'default');
  if ($lang_data_obj['success']==false) {
    debug_mail(false,'gks_lang_data_obj_prepare', print_r($lang_data_obj,true));
    $return = array('success' => false, 'message' => base64_encode($lang_data_obj['message']));
    echo json_encode($return); die(); }
  //print '<pre>';print_r($lang_data_obj);die();
  $lang_data_save=gks_lang_data_obj_save($lang_data_obj,$id,$_POST);
  if ($lang_data_save['success']==false) {
    debug_mail(false,'gks_lang_data_obj_save', print_r($lang_data_save,true));
    $return = array('success' => false, 'message' => base64_encode($lang_data_save['message']));
    echo json_encode($return); die(); }
  //print '<pre>';print_r($lang_data_save);die();  
}


function gks_lang_data_obj_sql_prepare(&$prepare, $fields) {
//  global $GKS_LANG_DATA_ARRAY;
//  global $db_link;
//  global $my_wp_user_id;
//  global $gkIP;

  global $gks_user_settings;
  global $GKS_LANG_DEFAULT;
  
  
  //print '<pre>';print_r($gks_user_settings);die();
  
  $load_lang=$GKS_LANG_DEFAULT;
  if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);

  //echo $load_lang;die();
  //$load_lang='en-US';
  

  //$load_lang='de-DE'; //debug delete me
  
  $load_lang_db=str_replace('-', '_',$load_lang);
  
   
  $prepare['lang_backend']=$load_lang;
  $prepare['lang_backend_db']=$load_lang_db;
  

  if (isset($prepare['sql'])==false) {
    //echo 'calc sql ';
    $prepare['sql']=array();
  }
  if ($load_lang==$GKS_LANG_DEFAULT) {
    $prepare['sql']=array();
    $prepare['sql']['from1']='';
    $prepare['sql']['from2']='';
    return ;
  }
  
  
  
  $fieldsa_in=array();
  foreach ($fields as $myf) {
    $field_name=$myf;
    $field_in= $myf.'_'.$load_lang_db;
    $fieldsa_in[]=$field_name.' as '.$field_in;
  }
  
  $prepare['sql']['fields']=$fields;
  $prepare['sql']['fieldsa_in']=$fieldsa_in;

  
  $prepare['sql']['from1']='(';
  $prepare['sql']['from2']="
  LEFT JOIN (
    SELECT ".$prepare['rec_id'].", 
    ".implode(',',$prepare['sql']['fieldsa_in'])." 
    FROM ".$prepare['table_name_lang']." 
    WHERE lang_code='".$load_lang."'
  ) AS ".$prepare['table_name']."_".$load_lang_db." ON ".$prepare['table_name'].".".$prepare['id_parent']." = ".$prepare['table_name']."_".$load_lang_db.".".$prepare['rec_id'].")";
  
  //echo '<pre>sdsssssss'.$load_lang;die();
//  if (1==2 and $load_lang!='en-US') {
//    $fieldsa_in=array();
//    foreach ($fields as $myf) {
//      $field_name=$myf;
//      $field_in= $myf.'_en_US';
//      $fieldsa_in[]=$field_name.' as '.$field_in;
//    }      
//    $prepare['sql']['fieldsa_in_en_US']=$fieldsa_in;
//    $prepare['sql']['from1'].='(';
//    $prepare['sql']['from2'].="
//    LEFT JOIN (
//      SELECT ".$prepare['rec_id'].", 
//      ".implode(',',$prepare['sql']['fieldsa_in_en_US'])." 
//      FROM ".$prepare['table_name_lang']." 
//      WHERE lang_code='en-US'
//    ) AS ".$prepare['table_name']."_en_US ON ".$prepare['table_name'].".".$prepare['id_parent']." = ".$prepare['table_name']."_en_US.".$prepare['rec_id'].")";
//  }
  //echo '<pre>';print_r($prepare);die();
  
  
  //return $ret;
}

function gks_lang_sql_field($field, &$prepare, $asname='',$notas=false) {
//  global $_gks_session;
//  $load_lang='el-GR';
//  if (isset($_gks_session['gks']['ui_lang'])) $load_lang = gks_erp_supperted_lang($_gks_session['gks']['ui_lang']);
  global $GKS_LANG_DEFAULT_DB;
  
  $load_lang=$prepare['lang_backend_db'];

  if ($load_lang == $GKS_LANG_DEFAULT_DB) {
    $ret= $field;
    if ($notas==false) $ret.=" as ".($asname!='' ? $asname : $field);
    return $ret;
  }
//  if ($load_lang == 'en_US') {
//    $ret= "IF(".$field."_".$load_lang." is not null and ".$field."_".$load_lang."!='',".$field."_".$load_lang.",".$field.")";
//    if ($notas==false) $ret.=" as ".($asname!='' ? $asname : $field);
//    return $ret;
//  }

  $ret= "IF(".
        $field."_".$load_lang." is not null and ".$field."_".$load_lang."!='',".$field."_".$load_lang.",".
        "IF(".$field."_en_US is not null and ".$field."_en_US!='',".$field."_en_US ,".$field.")".
        ")";

  $ret= "IF(".
        $field."_".$load_lang." is not null and ".$field."_".$load_lang."!='',
        ".$field."_".$load_lang.",
        ".$field."
        )";
        
  //echo $ret;die();      
  if ($notas==false) $ret.=" as ".($asname!='' ? $asname : $field);
  return $ret;
  
}
$gks_lang_pft_data=false;

function gks_lang_pft($lang,$table_name,$field_name,$rec_id,$def_value) {//print_field_translation
  global $db_link;
  global $gks_lang_pft_data;
  global $GKS_LANG_DEFAULT;
  if ($lang==$GKS_LANG_DEFAULT) return $def_value; //'el-GR'
  
  $ret_value=$def_value;
  if ($gks_lang_pft_data===false) $gks_lang_pft_data=[];
  
  $key_en=$table_name.'_'.$rec_id.'_en-US';
  if (isset($gks_lang_pft_data[$key_en])==false) {
    $obj=gks_lang_data_obj_prepare($table_name,'',$lang);
    //echo '<pre>';print_r($obj);die();
    
    $sql="select * from ".$obj['table_name_lang']." where lang_code='en-US' and ".$obj['rec_id']."=".$rec_id;
    $result = $db_link->query($sql); 
    if (!$result) { debug_mail(false,'error sql',$sql); return $ret_value;}   
    //echo '<pre>';print_r($sql);//die();
    $row=false;
    if ($result->num_rows>0) $row = $result->fetch_assoc();
    
    $gks_lang_pft_data[$key_en]=$row;
  }
  
  if (is_array($gks_lang_pft_data[$key_en]) and 
      isset($gks_lang_pft_data[$key_en][$field_name]) and
      trim_gks($gks_lang_pft_data[$key_en][$field_name])!='') {
    $ret_value=$gks_lang_pft_data[$key_en][$field_name];  
  }
  
  if ($lang=='en-US') return $ret_value;
  
  $key_other=$table_name.'_'.$rec_id.'_'.$lang;
  if (isset($gks_lang_pft_data[$key_other])==false) {
    $obj=gks_lang_data_obj_prepare($table_name,'',$lang);
    //echo '<pre>';print_r($obj);die();
    
    $sql="select * from ".$obj['table_name_lang']." where lang_code='".$db_link->escape_string($lang)."' and ".$obj['rec_id']."=".$rec_id;
    $result = $db_link->query($sql); 
    if (!$result) { debug_mail(false,'error sql',$sql); return $ret_value;}   
    //echo '<pre>';print_r($sql);//die();
    $row=false;
    if ($result->num_rows>0) $row = $result->fetch_assoc();
    
    $gks_lang_pft_data[$key_other]=$row;
  }
  
  if (is_array($gks_lang_pft_data[$key_other]) and 
      isset($gks_lang_pft_data[$key_other][$field_name]) and
      trim_gks($gks_lang_pft_data[$key_other][$field_name])!='') {
    $ret_value=$gks_lang_pft_data[$key_other][$field_name];  
  }
  
  
  return $ret_value;
}

function gks_lang_data_tables() {
return array(
'gks_aade_skopos_diakinisis',
'gks_aade_katigoria_fpa_ejeresi',
'gks_aade_katigoria_parakratoumemenon_foron',
'gks_aade_katigoria_loipon_foron',
'gks_aade_katigoria_xartosimou',
'gks_aade_katigoria_telon',
'gks_acc_eidi_parastatikon',
'gks_acc_eidi_parastatikon_types',
'gks_assets',
'gks_banks',
'gks_country',
'gks_eshop_fiscal_position',
'gks_eshop_pricelist',
'gks_nomoi',
'gks_delivery_methods',
'gks_payment_acquirers',
'gks_hotel',
'gks_hotel_floor',
'gks_hotel_room',
'gks_hotel_room_type',
'gks_hotel_room_type_subroom',
'gks_newsletter_lists',
'gks_poi',
'gks_poi_type',
'gks_transfer',
'gks_transfer_area',
'gks_transfer_oxima_type',
'gks_lang',
'gks_eshop_products',
'gks_product_idiotites',
'gks_product_idiotites_terms',
'gks_eshop_products_categories',
'gks_eshop_products_brands',
'gks_acc_journal',
'gks_acc_seires',
'gks_company',
'gks_company_subs',
'gks_warehouses',
'gks_monades_metrisis',
);  
  
}


function gks_lang_data_swap($old_lang,$new_lang) { //en-GR en-US
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_PRODUCT_DESCR_BIG;
  global $GKS_PRODUCT_DESCR_SMALL;

  $rrr=array(
    'success' => false, 
    'message' => 'generic error gks_lang_data_obj_save',
    'error_sqls'=>[],
  );
      
  if ($old_lang=='' or $new_lang=='' or $old_lang==$new_lang) {
    $rrr['success']=true;$rrr['message']='OK';return $rrr;}
  
  $gks_lang_data_swap_data=[];
  $sql="select myvalue from gks_settings where mykey='GKS_LANG_DATA_SWAP'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $rrr['error_sqls'][]=$sql;
  } else {
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();  
      $temp=trim_gks($row['myvalue']);
      if ($temp!='') {
        $gks_lang_data_swap_data=json_decode($temp,true);
      }
    }  
  }  
  //echo '<pre>';print_r($gks_lang_data_swap_data);die();
  
  
  $temp_GKS_PRODUCT_DESCR_BIG=$GKS_PRODUCT_DESCR_BIG;
  $temp_GKS_PRODUCT_DESCR_SMALL=$GKS_PRODUCT_DESCR_SMALL;
  
  $mytables=gks_lang_data_tables();
  
  //delete me
  //$mytables=['gks_country'];
  
  foreach ($mytables as $myt) {
    if (isset($gks_lang_data_swap_data[$myt])==false) {
      $gks_lang_data_swap_data[$myt]=array(
        'curr_lang'=>$old_lang,
        'time_update'=>0,
        'errors'=>0,
        'fields_update'=>[],
      );
    }
    
    if ($gks_lang_data_swap_data[$myt]['curr_lang']==$new_lang) continue;
    //die('sssss');
    $ret=gks_lang_data_obj_prepare($myt, 'none', 'none');

    $sql="INSERT INTO ".$ret['table_name_lang']." (
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip,
    ".$ret['rec_id'].", lang_code)
    SELECT Now() as mydate_add, Now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip,
    ".$myt.".".$ret['id_parent'].", '".$old_lang."' AS lang 
    FROM ".$myt." LEFT JOIN (
      SELECT ".$ret['table_name_lang'].".".$ret['rec_id'].", ".$ret['table_name_lang'].".lang_code
      FROM ".$ret['table_name_lang']."
      WHERE ".$ret['table_name_lang'].".lang_code='".$old_lang."'
    ) AS table_exist_recs ON ".$myt.".".$ret['id_parent']." = table_exist_recs.".$ret['rec_id']."
    WHERE table_exist_recs.".$ret['rec_id']." Is Null";
    //print '<pre>σσσσσσσσσσ '.$sql;die(); 
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $rrr['error_sqls'][]=$sql;
    }
    
    $myfs=[];
    foreach ($ret['fields_array'] as $myf) {
      $myfs[]=$ret['table_name_lang'].".".$myf['field_name']." = ".$myt.".".$myf['field_name'];
    } 
    if (count($myfs)>0) {
      $sql="UPDATE ".$ret['table_name_lang']." 
      LEFT JOIN ".$myt." ON ".$ret['table_name_lang'].".".$ret['rec_id']."  = ".$myt.".".$ret['id_parent']." 
      SET 
      ".implode(',',$myfs)."
      WHERE ".$ret['table_name_lang'].".lang_code='".$old_lang."'
      AND ".$myt.".".$ret['id_parent']." Is Not Null";
      //print '<pre>σσσσσσσσσσ '.$sql;die(); 
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $rrr['error_sqls'][]=$sql;
      }
    }
    
    $this_table_errors=0;
    $fields_update_ok=[];
    $fields_update_error=[];
    foreach ($ret['fields_array'] as $myf) {
      //$myf['field_name']
      $sql="UPDATE ".$myt." 
      LEFT JOIN (
        SELECT ".$ret['table_name_lang'].".".$ret['rec_id'].", 
        ".$ret['table_name_lang'].".".$myf['field_name']."
        FROM ".$ret['table_name_lang']."
        WHERE ".$ret['table_name_lang'].".lang_code='".$new_lang."'
        and ".$myf['field_name']."<>''  
      ) AS table_exist_recs ON ".$myt.".".$ret['id_parent']." = table_exist_recs.".$ret['rec_id']." 
      SET ".$myt.".".$myf['field_name']." = table_exist_recs.".$myf['field_name']."
      WHERE table_exist_recs.".$ret['rec_id']." Is Not Null;";
      //print '<pre>σσσσσσσσσσ '.$sql;die(); 
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $rrr['error_sqls'][]=$sql;
        $this_table_errors++;
        $fields_update_error[]=$myf['field_name'];
      } else {
        $fields_update_ok[]=$myf['field_name'];
      }
    }
    
    $gks_lang_data_swap_data[$myt]=array(
      'curr_lang'=>$new_lang,
      'time_update'=>time(),
      'errors'=>$this_table_errors,
      'fields_update_ok'=>$fields_update_ok,
      'fields_update_error'=>$fields_update_error,
    );
    
    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_LANG_DATA_SWAP','".$db_link->escape_string(json_encode($gks_lang_data_swap_data))."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $rrr['error_sqls'][]=$sql;
    }

    //print '<pre>';print_r($myfs);print_r($ret);die();   
  } 
  //print '<pre>fffffffff ';print_r($rrr);die();


  $GKS_PRODUCT_DESCR_BIG=$temp_GKS_PRODUCT_DESCR_BIG;
  $GKS_PRODUCT_DESCR_SMALL=$temp_GKS_PRODUCT_DESCR_SMALL;

  $sql="replace into gks_settings (mykey,myvalue) values ('GKS_LANG_DEFAULT','".$db_link->escape_string($new_lang)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $rrr['error_sqls'][]=$sql;
  }

  
  $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_menu_version=".time()." where gks_menu_version>0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $rrr['error_sqls'][]=$sql;
  }
  
  
  $rrr['success']=count($rrr['error_sqls'])==0 ? true : false;
  $rrr['message']='OK';
  if (count($rrr['error_sqls'])>0) $rrr['message'].=' with '.count($rrr['error_sqls']).' sqls errors';
  return $rrr;
}


