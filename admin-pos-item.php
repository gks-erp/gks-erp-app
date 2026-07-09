<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_pos_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos','01');

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_pos_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','view',0);
$perm_gks_pos_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','edit',0);
$perm_gks_pos_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','add',0);
$perm_gks_pos_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','delete',0);

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$gks_voip_params=gks_voip_user_params();

//die();

//print '<pre>';
//$out=array();
//gks_monada_convert(6, 8, $out,array());
//print_r($out);
//die();










if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

$nav_active_array=array('accounting','accounting_pos');  

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}




//print '<pre>';
//print_r($user_companys);
//die();

//print '<pre>';print_r($gks_user_settings['print']);die();

$gks_custom_prepare = gks_custom_table_item_prepare('gks_pos',['from'=>'item']);


$base_sql="SELECT gks_pos.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add,
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, 
gks_users.epaggelma, gks_users.ma_odos, gks_users.ma_arithmos, 
gks_users.ma_orofos,gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id,gks_users.ma_nomos_id,
".GKS_WP_TABLE_PREFIX."users.generic_ekprosi,
".GKS_WP_TABLE_PREFIX."users.user_email,
".GKS_WP_TABLE_PREFIX."users.gks_mobile,


gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,gks_country.country_name,gks_nomoi.nomos_descr,
table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile,

gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to,
user_app_mobile_userlogin.gks_nickname as gks_nickname_app_mobile_userlogin
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((((((((((((((((((((((((gks_pos
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_pos.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_pos.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company on gks_pos.pos_company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_pos.pos_company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_pos.def_tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_pos.def_tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_pos.def_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_pos.def_pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_pos.def_user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_pos.def_aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_pos.def_assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_pos.def_crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_pos.def_crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_pos.def_crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_pos.pos_warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_pos.pos_warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
)  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS user_app_mobile_userlogin ON gks_pos.app_mobile_userlogin_id = user_app_mobile_userlogin.ID
";



$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {

  if ($template_id>0) {
    $sql=$base_sql."where id_pos = ".$template_id;
    if (count($perm_id_pos_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_ids).")";
    if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
    if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
    if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
    
    $result = $db_link->query($sql);        
    if (!$result) debug_mail(false,'error sql',$sql);
    if (!$result) die('sql error');
    if ($result->num_rows!=1) {
      debug_mail(false,'record not found sql tempate',$sql); 
      die('no record found (tempate)');
    } 
    $row = $result->fetch_assoc();
    //$row['id_pos']=-1; //gia na doulecei to custom
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0; 
    $row['myip']='';
    $row['pos_name'].=' draft '.rand(1000,9999);
    $row['pos_guid']='';
    
    $my_page_title=gks_lang('Νέο Εντατική Λιανική από το πρότυπο').' #'.$template_id;   
  }
  if ($template_id==0) {
     
    $row=array();
  
    $row['id_pos']=-1;
    $row['pos_guid']='';
    $row['def_aade_skopos_diakinisis_id']=0;
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0;
    $row['gks_nickname_add']='';
    $row['gks_nickname_edit']='';
    $row['myip']='';
  
    $row['pos_name']='';
    $row['pos_descr']='';
    $row['pos_disable']=0;
    $row['pos_max_ammount']=0;
    $row['pos_aade_mydata_live']=0;
    $row['def_user_id']=0;
    $row['gks_nickname']='';
    $row['myfirst_name']='';
    $row['mylast_name']='';
    $row['user_email']='';
    $row['mymoobile']='';
    $row['def_user_lang']='el-GR';
    $row['lang_name']='';
    
    $row['eponimia']='';
    $row['title']='';
    $row['afm']='';
    $row['doy']='';
    $row['epaggelma']='';
    $row['ma_odos']='';
    $row['ma_arithmos']='';
    $row['ma_orofos']='';
    $row['ma_perioxi']='';
    $row['ma_poli']='';
    $row['ma_tk']='';
    $row['ma_country_id']=0;
    $row['ma_nomos_id']=0;
    $row['country_name']='';
    $row['nomos_descr']='';  
  
    
    $row['def_tropos_apostolis']=1;
    $row['def_tropos_pliromis']=1;
    $row['kostos_apostolis']=0;
    $row['kostos_pliromis']=0;
  
    
    $row['def_fiscal_position_id']=1;
    $row['def_pricelist_id']=1;
    
    $row['pelati_sxolio']='';
    $row['order_sxolio']='';
    
    $row['def_delivery_id_8']=0;
  
  
  
    $row['company_afm']='';
    $row['pos_company_id']=0;
    $row['pos_company_sub_id']=0;
    if (count($user_companys)>=1) {
      foreach ($user_companys as $value) {
        $row['pos_company_id']=$value['id_company'];
        $row['pos_company_sub_id']=$value['id_company_sub'];
        $row['company_afm']=$value['company_afm'];
        break;
      } 
    }
    $row['pos_journal_id']=0;
    $row['pos_seira_id']=0;
    $row['pos_user_can_change_prices']=0;
  
    
    $row['acc_eidos_parastatikou_id']=0;
    $row['eidos_parastatikou_type_id']=0;
    $row['antisimvalomenos_label']=gks_lang('Πελάτης');
    $row['eidos_parastatikou_need_prev']=0;
    $row['eidos_parastatikou_has_fpa']=0;
    $row['eidos_parastatikou_has_othertaxes']='';
    $row['eidos_parastatikou_has_esoda']=0;
    $row['eidos_parastatikou_has_eksoda']=0;
    $row['eidos_parastatikou_need_afm']=1;
    $row['eidos_parastatikou_balance_pros']=0;
    $row['whi_eidos_parastatikou_stock_pros']=0;
    $row['whi_eidos_parastatikou_type_id']=0;
    
  
   
  
  
    $row['def_affect_balance']=1;
    $row['def_affect_balance_all_poso']=1;
    $row['def_affect_balance_all_poso_type']='pliroteo';
     
    $row['def_assigned_id']=0;
    $row['gks_nickname_assigned']='';
    $row['def_crm_channel_id']=0;
    $row['crm_channel_sale_descr']='';
    $row['def_crm_channel_contact_id']=0;
    $row['crm_channel_contact_gks_nickname']='';
    $row['def_crm_channel_campain_id']=0;
    $row['ads_campain_name']='';
    $row['def_crm_channel_url']='';
    $row['def_crm_channel_code']='';
    $row['def_crm_channel_text']=''; 
    
    $row['pos_warehouses_id_from']=0;
    $row['warehouse_name_from']='';
    $row['pos_warehouses_id_to']=0;
    $row['warehouse_name_to']='';
  
    $row['pos_print_enable']=0;
    $row['pos_print_file_type']='pdf';
    $row['pos_print_grayscale']=0;
    $row['pos_print_landscape']=0;
    $row['pos_print_zoom']=1;
    $row['pos_print_form_id']=0;
    $row['pos_thermal_form_id']=0;
    $row['pos_print_x_form_id']=0;
    $row['pos_paroxos_send_pdf']=0;
    
    $row['def_products']='';
    $row['def_tropos_pliromis_array']='';
    $row['pos_multi_copies']=0;
    $row['pos_installments']=0;
    $row['pos_tip']=1;
    $row['app_mobile_userlogin_id']=0;
    $row['gks_nickname_app_mobile_userlogin']='';
    
    $row['pos_sms_erp_app_mobile_id_code']='';
    $row['pos_sms_template_text']='';
    $row['pos_can_search_products']=0;
    $row['pos_indexeddb']=0;
    $row['pos_auto_click_start_at_paywith']=0;
    
    $row['erp_app_id']=0;
    $row['erp_app_dest']='printer';
    $row['erp_app_dest_printer_method']=1;
    $row['erp_app_dest_printer']='';
    $row['erp_app_dest_printer_lpr_ip']='';
    $row['erp_app_dest_printer_copies']=1;
    $row['erp_app_dest_folder']='';
    $row['erp_app_filter']='';
  
    
    if (isset($gks_user_settings['gks_pos']['def_values'])) {
      $def_values=unserialize($gks_user_settings['gks_pos']['def_values']);  
      //print '<pre>';print_r($def_values);
      foreach ($def_values as $dkey => $dvalue) {
        if (isset($row[$dkey])) $row[$dkey]=$dvalue;
      }
    } 
    $my_page_title=gks_lang('Νέο Εντατική Λιανική');
  }
} else {

  $sql=$base_sql."where gks_pos.id_pos = ".$id;
  
  if (count($perm_id_pos_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_ids).")";
  if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  
  //print '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Εντατική Λιανική').': #'.$id.' '.$row['pos_name'];
  
}

$pos_seira_id=$row['pos_seira_id'];


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();




$antisimvalomenos_label=$row['antisimvalomenos_label'];
$acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
$eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=intval($row['eidos_parastatikou_need_prev']);
$eidos_parastatikou_has_fpa=intval($row['eidos_parastatikou_has_fpa']);
$eidos_parastatikou_has_othertaxes=trim_gks($row['eidos_parastatikou_has_othertaxes']);
$eidos_parastatikou_has_esoda=intval($row['eidos_parastatikou_has_esoda']);
$eidos_parastatikou_has_eksoda=intval($row['eidos_parastatikou_has_eksoda']);
$eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
$eidos_parastatikou_balance_pros=intval($row['eidos_parastatikou_balance_pros']);
$whi_eidos_parastatikou_stock_pros=intval($row['whi_eidos_parastatikou_stock_pros']);
$whi_eidos_parastatikou_type_id=intval($row['whi_eidos_parastatikou_type_id']);

$antisimvalomenos_label_org=$antisimvalomenos_label;
$acc_eidos_parastatikou_id_org=$acc_eidos_parastatikou_id;
$eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;
$whi_eidos_parastatikou_stock_pros_org=$whi_eidos_parastatikou_stock_pros;
$whi_eidos_parastatikou_type_id_org=$whi_eidos_parastatikou_type_id;









$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);



$row['ma_country_id']=intval($row['ma_country_id']);
$row['ma_poli']=trim_gks($row['ma_poli']);






unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='pos';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $row['pos_company_id'];
$mybasketarray['company_sub_id']= $row['pos_company_sub_id'];
$mybasketarray['user']['user_id']=$row['def_user_id'];
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']=1;
gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];








gks_cache_admin_pos_item();







stat_record();

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';

?>

<link href="css/admin-pos-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/admin-eftpos-transaction-dialog.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-6" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Εντατική Λιανική');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['pos_name'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Εντατική Λιανική');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
    <div class="col-md-6" style="text-align:center">
      <?php if ($id>0) {?>
      <a class="btn btn-primary" href="admin-pos-run.php?id=<?php echo $id;?>"><?php echo gks_lang('Προβολή');?></a>
      <?php } ?>
    </div>  
  </div>
</div>






<div id="mypostform">
  
  
<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-6">
  

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('bas');?>>
          
          <div class="form-group row">
            <label for="pos_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-8">
              <input id="pos_name" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['pos_name']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="pos_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <textarea id="pos_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['pos_descr']);?></textarea>
            </div>
          </div>                    
          <div class="form-group row">
            <label for="pos_user_can_change_prices" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ο χειριστής μπορεί να αλλάξει τις τιμές');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_user_can_change_prices" value="1" <?php if ($row['pos_user_can_change_prices']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="pos_max_ammount" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μέγιστο Ποσό');?>:</label>
            <div class="col-sm-8">
              <input id="pos_max_ammount" type="number" class="form-control form-control-sm myneedsave" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
              value="<?php if ($row['pos_max_ammount']>0) echo $row['pos_max_ammount'];?>" style="max-width:200px;">
            </div>
          </div>

          <div class="form-group row">
            <label for="pos_aade_mydata_live" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική Αποστολή ΑΑΔΕ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_aade_mydata_live" value="1" <?php if ($row['pos_aade_mydata_live']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          

          <div class="form-group row">
            <label for="pos_multi_copies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πολλαπλά αντίγραφα έως');?>:</label>
            <div class="col-md-8">
              <select id="pos_multi_copies" class="form-control form-control-sm myneedsave" style="width:unset;" >
                <option value="0"><?php echo gks_lang('Ανενεργό');?></option>
                <?php for($copies=2;$copies<=20;$copies++) {
                  echo '<option value="'.$copies.'"'.
                  ($row['pos_multi_copies']==$copies ? ' selected' : '').
                  '>'.$copies.'</option>';
                }?>
              </select>              
            </div>
          </div>
          <div class="form-group row">
            <label for="pos_installments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστες Δόσεις');?>:</label>
            <div class="col-md-8">
              <select id="pos_installments" class="form-control form-control-sm myneedsave" style="width:unset;" >
                <option value="0"><?php echo gks_lang('Χωρίς δόσεις');?></option>
                <?php for($installments=2;$installments<=60;$installments++) {
                  echo '<option value="'.$installments.'"'.
                  ($row['pos_installments']==$installments ? ' selected' : '').
                  '>'.$installments.'</option>';
                }?>
              </select>              
            </div>
          </div>          
          <div class="form-group row">
            <label for="pos_tip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φιλοδώρημα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_tip" value="1" <?php if ($row['pos_tip']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="pos_can_search_products" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναζήτηση στα είδη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_can_search_products" value="1" <?php if ($row['pos_can_search_products']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="pos_indexeddb" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρήση προσωρινής μνήμης ειδών');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_indexeddb" value="1" <?php if ($row['pos_indexeddb']!=0) echo ' checked '; ?> class="switchery1_sel">
              <small class="form-text text-muted"><?php echo gks_lang('Προτείνεται εάν έχετε πολλά είδη, π.χ. πάνω από 500');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="pos_auto_click_start_at_paywith" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυτόματη έναρξη πληρωμής με κάρτα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_auto_click_start_at_paywith" value="1" <?php if ($row['pos_auto_click_start_at_paywith']!=0) echo ' checked '; ?> class="switchery1_sel">
              <small class="form-text text-muted"><?php echo gks_lang('Εφόσον δεν είναι ενεργοποιημένο το φιλοδώρημα, οι δόσεις, είναι πώληση και ο χρήστης έχει ενεργοποιημένη την ρύθμιση ελάχιστα clicks');?></small>
            </div>
          </div>
          


          
          <div class="form-group row">
            <label for="pos_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_disable" value="1" <?php if ($row['pos_disable']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>

        </div>
      </div> 
    </div> 
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εκτύπωση');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('print');?>>

          <div class="form-group row">
            <label for="pos_print_enable" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pos_print_enable" value="1" <?php if ($row['pos_print_enable']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div id="div_pos_print_enable" style="<?php if ($row['pos_print_enable']=='0') echo 'display:none;';?>">
            <div class="form-group row">
              <label for="pos_print_form_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης');?>:</label>
              <div class="col-md-8">
                <select id="pos_print_form_id" class="form-control form-control-sm myneedsave" >
                  <option value="0"></option>
                  <?php
                  $sql="SELECT id_print_form, print_form_descr FROM gks_print_forms 
                  WHERE is_disable=0 and file_type<>'raw' ORDER BY sortorder,print_form_descr;";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_print_form'].'" ';
                    if ($row['pos_print_form_id'] == $row_select['id_print_form']) echo ' selected ';
                    echo '>'.$row_select['print_form_descr'].'</option>';
                  }
                  ?>                
                </select>    
              </div>
            </div>
                      
            <div class="form-group row">
              <label for="file_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
              <div class="col-md-8">
                <input type="radio" name="file_type" id="file_type_pdf" value="pdf" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_file_type']=='pdf') echo 'checked';?>>  
                  <label class="form-control-sm" for="file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
                  <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:150%"></i>
                  </label>
                <input type="radio" name="file_type" id="file_type_html"  value="html" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_file_type']=='html') echo 'checked';?>>  
                  <label class="form-control-sm" for="file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
                  <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>
                  </label>
                <input type="radio" name="file_type" id="file_type_jpg"  value="jpg" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_file_type']=='jpg') echo 'checked';?>>  
                  <label class="form-control-sm" for="file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
                  <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:21px;vertical-align: top;">
                  </label>
              </div>
            </div>           
  
            <div class="form-group row">
              <label for="is_landscape" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσανατολισμός');?>:</label>
              <div class="col-md-8">
                <input type="radio" name="is_landscape" id="is_landscape_off" value="1" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_landscape']==0) echo 'checked';?>>  
                  <label class="form-control-sm" for="is_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
                  <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:150%"></i>
                  </label>
                <input type="radio" name="is_landscape" id="is_landscape_on"  value="2" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_landscape']!=0) echo 'checked';?>>  
                  <label class="form-control-sm" for="is_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
                  <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:150%"></i>
                  </label>
              </div>
            </div>
            <div class="form-group row">
              <label for="grayscale" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
              <div class="col-md-8">
                <input type="radio" name="grayscale" id="grayscale_off" value="1" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_grayscale']==0) echo 'checked';?>>  
                  <label class="form-control-sm" for="grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
                  <img src="img/palette-color.png" border="0" width="16">
                  </label>
                <input type="radio" name="grayscale" id="grayscale_on"  value="2" class="myneedsave" style="cursor: pointer;" <?php if ($row['pos_print_grayscale']!=0) echo 'checked';?>>  
                  <label class="form-control-sm" for="grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
                  <img src="img/palette-gray.png" border="0" width="16">
                  </label>
              </div>
            </div>
            
  
            <div class="form-group row">
              <label for="zoom" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μεγέθυνση');?>:</label>
              <div class="col-md-8">
                <div id="zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
                  <div id="zoom_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div> 

            <div class="form-group row" id="div_pos_paroxos_send_pdf">
              <label for="pos_paroxos_send_pdf" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Αποστολή εκτύπωσης σε πάροχο');?>:</label>
              <div class="col-md-8">
                <input type="checkbox" id="pos_paroxos_send_pdf" value="1" <?php if ($row['pos_paroxos_send_pdf']!=0) echo ' checked '; ?> class="switchery1_sel">
                <small class="form-text text-muted"><?php echo gks_lang('Εφόσον η σειρά είναι προς πάροχο');?></small>
              </div>
            </div>            

            <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
            <div class="form-group row">
              <label for="pos_thermal_form_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης σε θερμικό');?>:</label>
              <div class="col-md-8">
                <select id="pos_thermal_form_id" class="form-control form-control-sm myneedsave" >
                  <option value="0"></option>
                  <?php
                  $sql="SELECT id_print_form, print_form_descr FROM gks_print_forms 
                  WHERE is_disable=0 and file_type='raw' ORDER BY sortorder,print_form_descr;";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_print_form'].'" ';
                    if ($row['pos_thermal_form_id'] == $row_select['id_print_form']) echo ' selected ';
                    echo '>'.$row_select['print_form_descr'].'</option>';
                  }
                  ?>                
                </select> 
                <small class="form-text text-muted">
                <?php echo gks_lang('Είναι η ειδική εκτύπωση τύπου <b>raw</b> η οποία θα χρησιμοποιηθεί στον ενσωματωμένο εκτυπωτή της συσκευής');?>
                </small>  
              </div>
            </div>

            <div class="form-group row">
              <label for="pos_print_x_form_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης Χ');?>:</label>
              <div class="col-md-8">
                <select id="pos_print_x_form_id" class="form-control form-control-sm myneedsave" >
                  <option value="0"></option>
                  <?php
                  $sql="SELECT id_print_form, print_form_descr FROM gks_print_forms 
                  WHERE is_disable=0 and file_type='raw' ORDER BY sortorder,print_form_descr;";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_print_form'].'" ';
                    if ($row['pos_print_x_form_id'] == $row_select['id_print_form']) echo ' selected ';
                    echo '>'.$row_select['print_form_descr'].'</option>';
                  }
                  ?>                
                </select> 
                <small class="form-text text-muted">
                <?php echo gks_lang('Είναι η ειδική εκτύπωση τύπου <b>raw</b> η οποία θα χρησιμοποιηθεί στον ενσωματωμένο εκτυπωτή της συσκευής αλλά και στην προβολή με QRCode');?>
                </small>  
              </div>
            </div>
                                    
          </div>



                    
        </div>
      </div> 

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('gks ERP App Desktop');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpapp');?>> 

          <div class="form-group row">
            <label for="erp_app_id_check" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή στην gks ERP App Desktop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_id_check" value="1" <?php if ($row['erp_app_id']!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted">
                <?php echo gks_lang('Υπερισχύει αυτή η ρύθμιση σε σχέση με την σειρά');?>
              </small>
            </div>
          </div>
          <?php
          $row['erp_app_id']=intval($row['erp_app_id']);
          $row['erp_app_dest']=trim_gks($row['erp_app_dest']);
          if ($row['erp_app_dest']=='') $row['erp_app_dest']='printer';
          $row['erp_app_dest_printer']=trim_gks($row['erp_app_dest_printer']);
          $row['erp_app_dest_printer_method']=intval($row['erp_app_dest_printer_method']);
          $row['erp_app_dest_printer_lpr_ip']=trim_gks($row['erp_app_dest_printer_lpr_ip']);
          $row['erp_app_dest_printer_copies']=intval($row['erp_app_dest_printer_copies']);
          $row['erp_app_dest_folder']=trim_gks($row['erp_app_dest_folder']);
          $row['erp_app_filter']=trim_gks($row['erp_app_filter']);
          $erp_app_filter=array();
          if ($row['erp_app_filter']!='') $erp_app_filter=json_decode($row['erp_app_filter'],true);
          ?>
          
          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_filter" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φίλτρο');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_computer" value="webpage_computer" <?php if (in_array('webpage_computer',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_computer"><?php echo gks_lang('Από web σελίδα Η/Υ');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_tablet" value="webpage_tablet" <?php if (in_array('webpage_tablet',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_tablet"><?php echo gks_lang('Από web σελίδα tablet');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_mobile" value="webpage_mobile" <?php if (in_array('webpage_mobile',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_mobile"><?php echo gks_lang('Από web σελίδα κινητού');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_app_with_thermal" value="app_with_thermal" <?php if (in_array('app_with_thermal',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_app_with_thermal"><?php echo gks_lang('Από gks ERP App Mobile με θερμικό εκτυπωτή');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_app_no_thermal" value="app_no_thermal" <?php if (in_array('app_no_thermal',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_app_no_thermal"><?php echo gks_lang('Από gks ERP App Mobile χωρίς θερμικό εκτυπωτή');?></label>
              </div> 
            </div>
          </div>
          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_id" class="col-md-4 col-form-label form-control-sm text-md-right">gks ERP App Desktop:</label>
            <div class="col-md-8">
              <select id="erp_app_id" class="form-control form-control-sm myneedsave">
                <option value="0" data-local-printers=""></option>
                <?php
                $erp_app_local_printers='';
                $sql="SELECT * from gks_erp_app where erp_app_disabled=0 order by erp_app_sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_erp_app'].'" '.
                  'data-local-printers="';
                  if (trim_gks($row_select['erp_app_local_printers'])!='') {
                    $temp=unserialize($row_select['erp_app_local_printers']); 
                    if (is_array($temp) and count($temp)>0) {
                      echo base64_encode(json_encode($temp));
                    }
                  }
                  echo '"';
                  if ($row_select['id_erp_app']==$row['erp_app_id']) {
                    echo ' selected ';
                    $erp_app_local_printers=trim_gks($row_select['erp_app_local_printers']);
                  }
                  echo '>'.$row_select['erp_app_name'].'</option>';
                }?>
              </select>
            </div>
          </div> 

          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_dest_val_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προορισμός');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_printer" value="printer" <?php if ($row['erp_app_dest']=='printer') echo 'checked';?>>
                  <label for="erp_app_dest_val_printer"><?php echo gks_lang('Εκτυπωτής');?></label>
                <br>
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_folder" value="folder" <?php if ($row['erp_app_dest']=='folder') echo 'checked';?>>
                  <label for="erp_app_dest_val_folder"><?php echo gks_lang('Φάκελος');?></label>
              </div>  
            </div>            
          </div>
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_method" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέθοδος');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_method" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($row['erp_app_dest_printer_method']==1) echo 'selected';?> value="1"><?php echo erp_app_dest_printer_method_descr(1);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==0) echo 'selected';?> value="0"><?php echo erp_app_dest_printer_method_descr(0);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==2) echo 'selected';?> value="2"><?php echo erp_app_dest_printer_method_descr(2);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==3) echo 'selected';?> value="3"><?php echo erp_app_dest_printer_method_descr(3);?></option>

              </select>
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id01" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and in_array($row['erp_app_dest_printer_method'],[0,1]))) echo 'display:none;';?>">
            <label for="erp_app_dest_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer" class="form-control form-control-sm myneedsave">
                <option></option>
                <?php
                if ($erp_app_local_printers!='') {
                  $temp=unserialize($erp_app_local_printers);  
                  if (is_array($temp) and count($temp)>0) {
                    foreach ($temp as $value) {
                      echo '<option '.($value==$row['erp_app_dest_printer'] ? 'selected' : '').'>'.$value.'</option>';
                    }
                  }
                }  
                ?>              
              </select>    
            </div>
          </div>

          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id2" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and $row['erp_app_dest_printer_method']==2)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερή IP εκτυπωτή');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_printer_lpr_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_printer_lpr_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.70">
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id3" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and $row['erp_app_dest_printer_method']==3)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <?php echo gks_lang('Στον προεπιλεγμένο εκτυπωτή του H/Y');?>
              </div>
            </div>
          </div> 
                    
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_copies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντίτυπα');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_copies" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($row['erp_app_dest_printer_copies']==1) echo 'selected';?>>1</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==2) echo 'selected';?>>2</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==3) echo 'selected';?>>3</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==4) echo 'selected';?>>4</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==5) echo 'selected';?>>5</option>
              </select>
            </div>
          </div> 
          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_folder" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='folder')) echo 'display:none;';?>">
            <label for="erp_app_dest_folder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φάκελος');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_folder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_folder']);?>" placeholder="<?php echo gks_lang('π.χ.');?> c:\printer\folder\">
            </div>
          </div>

        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          gks ERP App Mobile
        </div>
       
        <div class="card-body" <?php echo gks_card_body('mobile');?>>

          <div class="form-group row">
            <label for="app_mobile_userlogin_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρήστης');?>:</label>
            <div class="col-md-8">
              <input id="app_mobile_userlogin_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_app_mobile_userlogin']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_pos_edit) echo 'readonly';?>
              data-user_id="<?php echo $row['app_mobile_userlogin_id'];?>"
              >
              <?php if ($perm_gks_pos_edit) {?>
              <a id="autocomplete_app_mobile_userlogin_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['app_mobile_userlogin_id'];?>" style="<?php if ($row['app_mobile_userlogin_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
              <?php } ?>
              <small class="form-text text-muted">
                <?php echo gks_lang('Θα γίνει αυτόματα σύνδεση με αυτόν τον χρήστη όταν επιλεγεί το συγκεκριμένο σημείο Εντατικής Λιανικής στην εφαρμογή <a href="admin-erp-app-mobile.php">gks ERP App Mobile</a>.');?>
                <br>
                <?php echo gks_lang('Αυτός o χρήστης θα πρέπει να έχει τα σχετικά δικαιώματα.');?>
              </small>

            </div>
          </div>            

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('SMS μέσω gks ERP App Mobile');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('sms');?>>

          <div class="form-group row">
            <label for="pos_sms_erp_app_mobile_id_code" class="col-md-4 col-form-label form-control-sm text-md-right">gks ERP App Mobile:</label>
            <div class="col-md-8">
              <select id="pos_sms_erp_app_mobile_id_code" class="form-control form-control-sm myneedsave" >
                <option value=""></option>
                
                
                <?php
//                $sql="SELECT id_erp_app_mobile, erp_app_mobile_name 
//                FROM gks_erp_app_mobile
//                WHERE erp_app_mobile_disabled=0 
//                and erp_app_mobile_can_sms<>0
//                ORDER BY erp_app_mobile_sortorder,erp_app_mobile_name;";
//                $result_select = $db_link->query($sql);        
//                if (!$result_select) {
//                  debug_mail(false,'error sql',$sql);
//                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
//                }
//                while ($row_select = $result_select->fetch_assoc()) {
//                  echo '<option value="'.$row_select['id_erp_app_mobile'].'" ';
//                  if ($row['pos_sms_erp_app_mobile_id'] == $row_select['id_erp_app_mobile']) echo ' selected ';
//                  echo '>'.$row_select['erp_app_mobile_name'].'</option>';
//                }


                $sql="SELECT gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name, 
                gks_erp_app_mobile.erp_app_mobile_phonenumber, gks_erp_app_mobile_ping.mydate
                FROM gks_erp_app_mobile 
                LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
                WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
                and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
                ORDER BY gks_erp_app_mobile.erp_app_mobile_sortorder;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'].'" '.
                  'data-provider="gks_erp_app_mobile" '.
                  'data-sender="'.$row_select['id_erp_app_mobile'].'" ';
                  $is_offline='';
                  if (empty($row_select['mydate'])==false and strtotime($row_select['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
                    $is_offline='';
                  } else {
                    $is_offline='disabled';
                  }
                  if ($row['pos_sms_erp_app_mobile_id_code']=='gks_erp_app_mobile:'.$row_select['id_erp_app_mobile']) {
                    echo ' selected ';  
                  }
                  
                  echo $is_offline.'>App: '.$row_select['erp_app_mobile_name'].' '.$row_select['erp_app_mobile_phonenumber'];
                  if ($is_offline!='') echo ' - '.gks_lang('ανενεργό');
                  echo '</option>';
                }  
                $parts=explode(',',$GKS_SMS_SENDER);
                foreach ($parts as $value) {
                  $value=trim_gks($value);
                  if ($value!='') {
                    echo '<option value=smsapi:'.$value.' '.
                    'data-provider="smsapi" '.
                    'data-sender="'.$value.'" ';
                    if ($row['pos_sms_erp_app_mobile_id_code']=='smsapi:'.$value) {
                      echo ' selected ';  
                    }
                  
                    echo '>smsapi: '.$value.'</option>';
                  }
                }
                

                ?>                
              </select>      
              <small class="form-text text-muted">
                <?php echo gks_lang('Το gks ERP App Mobile θα πρέπει να έχει ενεργοποιημένη την δυνατότητα αποστολής και λήψης SMS');?>
              </small>        
            </div>
          </div> 
                     
          <div class="form-group row">
            <label for="pos_sms_template_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρότυπο κείμενο SMS');?>:</label>
            <div class="col-md-8">
              <textarea id="pos_sms_template_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['pos_sms_template_text']);?></textarea>
              <small class="form-text text-muted">
                <?php echo gks_lang('Εάν θέλετε να περιλαμβάνει και τον δημόσιο σύνδεσμο της εκτύπωσης, συνήθως το αρχείο pdf, θα πρέπει να προσθέσετε και το κείμενο <b>[url]</b>');?>
                <br>
                <?php echo gks_lang('Το <b>[url]</b> θα αντικατασταθεί με τον εκάστοτε δημόσιο σύνδεσμο.');?>
                <br>
                <?php echo gks_lang('π.χ.');?>:<br>
                <span style="font-style:italic;"><?php echo gks_lang('gks: Ευχαριστούμε για την προτίμηση. Η απόδειξή σας είναι εδώ: [url]');?></span>
              </small>
            </div>
          </div>             

        </div>
      </div>
      
       
    </div>
  </div>
</div>
  
<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-4">


      

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('account');?>>
                    
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">

              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave" >
                <option value="0|0"></option>
                <?php
                $company_id_sub_id=$row['pos_company_id'].'|'.$row['pos_company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>

            </div>
          </div> 
          <div class="form-group row">
            <label for="pos_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <select id="pos_journal_id" class="form-control form-control-sm myneedsave" >
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id
                FROM (gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (1,2,5) and gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou not in (702,703,704)
                and is_disable=0 and company_id=".$row['pos_company_id']." AND company_sub_id=".$row['pos_company_sub_id'];
                if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";
                $sql.=" ORDER BY gks_acc_journal.sortorder,gks_acc_journal.acc_journal_descr;";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_journal'].'" '.
                  'data-eidi_id="'.$row_select['acc_eidos_parastatikou_id'].'" '.
                  'data-type_id="'.$row_select['eidos_parastatikou_type_id'].'" '.
                  'data-need_prev="'.$row_select['eidos_parastatikou_need_prev'].'" '.
                  'data-fpa="'.$row_select['eidos_parastatikou_has_fpa'].'" '.
                  'data-othertaxes="'.$row_select['eidos_parastatikou_has_othertaxes'].'" '.
                  'data-esoda="'.$row_select['eidos_parastatikou_has_esoda'].'" '.
                  'data-eksoda="'.$row_select['eidos_parastatikou_has_eksoda'].'" '.
                  'data-need_afm="'.$row_select['eidos_parastatikou_need_afm'].'" '.
                  'data-balance_pros="'.$row_select['eidos_parastatikou_balance_pros'].'" '.
                  'data-whi_stock_pros="'.intval($row_select['whi_eidos_parastatikou_stock_pros']).'" '. // intval kanei to null se 0
                  'data-whi_type_id="'.intval($row_select['whi_eidos_parastatikou_type_id']).'" ';       // intval kanei to null se 0
                  
                  if ($row['pos_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    

            </div>
          </div> 
          
          <div class="form-group row">
            <label for="pos_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <select id="pos_seira_id" class="form-control form-control-sm myneedsave" >
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,is_xeirografi
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['pos_journal_id'];
                if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";
                $sql.=" ORDER BY sortorder,seira_code;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_seira'].'" '.
                  'data-is_xeirografi="'.$row_select['is_xeirografi'].'" ';
                  if ($row['pos_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
            </div>
          </div> 

 

          <div class="form-group row">
            <label for="def_aade_skopos_diakinisis_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σκοπός Διακίνησης');?>:</label>
            <div class="col-md-8">
              <select id="def_aade_skopos_diakinisis_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_aade_skopos_diakinisis=gks_lang_data_obj_prepare('gks_aade_skopos_diakinisis','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_aade_skopos_diakinisis, array('aade_skopos_diakinisis_descr'));
                $sql="select id_aade_skopos_diakinisis,".gks_lang_sql_field('aade_skopos_diakinisis_descr',$lang_prepare_gks_aade_skopos_diakinisis)." 
                FROM ".$lang_prepare_gks_aade_skopos_diakinisis['sql']['from1']." gks_aade_skopos_diakinisis 
                ".$lang_prepare_gks_aade_skopos_diakinisis['sql']['from2']."
                ORDER BY sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_aade_skopos_diakinisis'].'" ';
                  if ($row_select['id_aade_skopos_diakinisis']==$row['def_aade_skopos_diakinisis_id']) echo ' selected ';
                  echo '>'.$row_select['aade_skopos_diakinisis_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
                              





              

 
         
     
          <div class="form-group row">
            <label for="def_fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <select id="def_fiscal_position_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                where fiscal_position_disable=0 
                order by fiscal_position_sortorder,fiscal_position_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['def_fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="def_pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <select id="def_pricelist_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['def_pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>  
               
          <div class="form-group row">
            <label for="def_assigned_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-sm-8">
              <input id="def_assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['def_assigned_id'];?>">
            </div>
          </div>          
                                  

        </div>
      </div>



    </div>
    
    
    <div class="col-md-8">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" id="antisimvalomenos_label">
          <?php echo $antisimvalomenos_label;?>
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>

          <div class="form-group row" id="div_form_idio_afm" style="<?php echo ($row['eidos_parastatikou_need_afm']==-1 ? '' : 'display:none;') ?>">  
            <div class="col-md-12 form-control-sm text-sm-center">

              <span style="white-space: nowrap;"><input type="radio" name="form_idio_afm" value="0" id="form_idio_afm_nai" <?php echo ($row['company_afm'] == $row['afm'] ? ' checked ' : '');?>> <label class="gks_label" for="form_idio_afm_nai" style="display:inline;padding-right:18px" ><?php echo gks_lang('Εμάς');?></label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="form_idio_afm" value="1" id="form_idio_afm_oxi" <?php echo ($row['company_afm'] == $row['afm'] ? '  ' : 'checked');?>> <label class="gks_label" for="form_idio_afm_oxi" style="display:inline"><?php echo gks_lang('Άλλος');?></label></span>
            </div>
          </div> 
          <div id="div_show_user" style="<?php echo (($row['eidos_parastatikou_need_afm']==-1 and $row['afm']==$row['company_afm']) ? 'display:none;' : '') ?>">
          
            <div class="form-group row">
              <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
              <div class="col-sm-4">
                    <input id="user" type="text" class="form-control form-control-sm myneedsave" 
                    value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
                    style="width:calc(98% - 22px);display:inline;" 
                    placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_pos_edit) echo 'readonly';?>>
                    <input id="user_id" type="hidden" value="<?php echo $row['def_user_id'];?>" class="myneedsave">
                    <?php if ($perm_gks_pos_edit) {?>
                    <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['def_user_id'];?>" style="<?php if ($row['def_user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                    <?php } ?>
              </div>

            </div>
  
  
            <div class="form-group row" style="margin-bottom: 0px;">
              <div class="col-sm-6">
                <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                  <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
                </div>
                              
              </div>
              
              <div class="col-sm-6">
                <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                  <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
                </div>               
              </div>
            </div>
  
  
  
  
  
  
  
  
  
            <div class="form-group row">
              <label for="dr_myfirst_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_myfirst_name">
                  <?php echo $row['myfirst_name'];?>
                </div>               
              </div>
              <label for="dr_mylast_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_mylast_name">
                  <?php echo $row['mylast_name'];?>
                </div>               
              </div>
            </div>
            <div class="form-group row">
              <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
              <div class="col-sm-4">
                 <div class="gks_flock form-control-sm" id="dr_user_email">
                    <?php echo '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a>';?>
                 </div>
              </div>
              <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
              <div class="col-sm-4">
                 <div class="gks_flock form-control-sm" id="dr_user_mobile">
                    <?php echo '<a href="tel:'.$row['mymoobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row['mymoobile'].'</a>';
                    echo $gks_voip_params['html_after_span'];
                    ?>
                 </div>               
              </div>
            </div>
                
            <div class="form-group row">
              <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
              <div class="col-sm-4">

                <select id="def_user_lang" class="form-control form-control-sm myneedsave" 
                  <option value=""></option>
                  <?php
                  $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                  $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                  FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                  ".$lang_prepare_gks_lang['sql']['from2']."
                  ORDER BY lang_sortorder,lang_name";                  
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_lang'].'" ';
                    if ($row['def_user_lang'] == $row_select['id_lang']) echo ' selected ';
                    echo '>'.$row_select['lang_name'].'</option>';
                  }
                  ?>
                </select>                  
              </div>
            </div>
            
  
    
   
            <div id="div_parastatiko_timologio" >
              <div class="form-group row">
                <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
                <div class="col-sm-4">
                  <div class="gks_flock form-control-sm" id="dr_user_eponimia">
                    <?php echo $row['eponimia'];?>
                  </div>                
                </div>
                <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
                <div class="col-sm-4">
                  <div class="gks_flock form-control-sm" id="dr_user_title">
                    <?php echo $row['title']; ?>
                  </div>
                </div>
              </div>
              <?php
              $ee_initials='';
              $sql="select id_country,country_ee,country_name,country_initials 
              FROM gks_country where id_country=".$row['ma_country_id'];
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              if ($result_select->num_rows==1) {
                $row_select = $result_select->fetch_assoc();
                $ee_initials=trim_gks($row_select['country_ee']);
              }
     
              ?>
              
              
              <div class="form-group row">
                <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
                <div class="col-sm-4">
                  <div class="form-control-sm gks_unset_height">
                    <span id="dr_user_afm_ee_initial_static" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><span 
                      style="display: inline-block;text-align:left;vertical-align: middle;"
                      id="dr_user_afm" class=" <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>"><?php echo htmlspecialchars_gks($row['afm']);?></span><span 
                      id="dr_user_afm_views_run_static" style="height:25px;<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>



                  </div>
                </div>
                <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
                <div class="col-sm-4">
                  <div class="gks_flock form-control-sm" id="dr_user_doy">
                    <?php echo $row['doy'];?>
                  </div>
                </div>
              </div>
  
  
              <div class="form-group row">
                <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
                <div class="col-sm-10">
                  <div class="gks_flock form-control-sm" id="dr_user_epaggelma">
                    <?php echo $row['epaggelma']; ?>
                  </div>               
                </div>
  
              </div>
            </div>
                        
            <div class="row">  
              <div class="col-md-12">
                <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Διεύθυνση Τιμολόγησης');?></div>
              </div>
            </div>  
            <div class="form-group row">
              <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_odos">
                  <?php echo $row['ma_odos']; ?>
                </div>                
              </div>
              <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_arithmos">
                  <?php echo $row['ma_arithmos']; ?>
                </div>                
              </div>
              
              
            </div>
            <div class="form-group row">
              <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_orofos">
                  <?php echo $row['ma_orofos'];?>
                </div>            
              </div>
              <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_perioxi">
                  <?php echo $row['ma_perioxi'];?>
                </div>            
              </div>
            </div>
  
            <div class="form-group row">
              <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_poli">
                    <?php echo $row['ma_poli']; ?>
                </div>               
              </div>
              <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_tk">
                  <?php echo $row['ma_tk']; ?>
                </div>               
              </div>
            </div>
  
            <div class="form-group row">
              <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_country_id">
                  <?php 
                  echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                  ?>
                </div>                
              </div>
              <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
              <div class="col-sm-4">
                <div class="gks_flock form-control-sm" id="dr_user_ma_nomos_id">
                    <?php 
                    echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                    ?>
                </div>
              </div>
            </div>   
            
          </div>
                                 

        </div>
      </div>  
      
      
      <div class="card gks_card_expand" id="div_warehouses" style="<?php if ($whi_eidos_parastatikou_type_id_org==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποθήκη');?>
        </div>
      
        <?php
        if ($row['pos_warehouses_id_from']==1 or $row['pos_warehouses_id_from']==2) {$row['pos_warehouses_id_from']=0;$row['warehouse_name_from']='';}
        if ($row['pos_warehouses_id_to']==1   or $row['pos_warehouses_id_to']==2)   {$row['pos_warehouses_id_to']=0;$row['warehouse_name_to']='';}
        
        
        
        $pos_warehouses_id_from_elem_display='';
        $pos_warehouses_id_to_elem_display='';
        $div_show_user_display='';
        if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
          $div_show_user_display='none';
          $pos_warehouses_id_from_elem_display='none';
        } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
          $div_show_user_display='none';
        } else {
          if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
            $pos_warehouses_id_from_elem_display='none';
          } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
            $pos_warehouses_id_to_elem_display='none';
          }
        }
        //echo '|'.$eidos_parastatikou_stock_pros_org.'|'.$pos_warehouses_id_from_elem_display.'|'.$pos_warehouses_id_to_elem_display.'|';  die();
        ?>
        
        <div class="card-body" <?php echo gks_card_body('warehouse');?>>
          
          <div class="form-group row">
            
            <label for="pos_warehouses_id_from" class="col-sm-2 col-form-label form-control-sm text-sm-right">
              <span class="pos_warehouses_id_from_elem" style="display:<?php echo $pos_warehouses_id_from_elem_display;?>"><?php echo gks_lang('Από');?>:</span>
            </label>
            <div class="col-sm-4">
              
              <input id="pos_warehouses_id_from" type="text" class="form-control form-control-sm myneedsave pos_warehouses_id_from_elem" 
              value="<?php echo htmlspecialchars_gks($row['warehouse_name_from']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['pos_warehouses_id_from'];?>"
              style="display:<?php echo $pos_warehouses_id_from_elem_display;?>">

            </div>

            <label for="pos_warehouses_id_to" class="col-sm-2 col-form-label form-control-sm text-sm-right">
              <span class="pos_warehouses_id_to_elem" style="display:<?php echo $pos_warehouses_id_to_elem_display;?>"><?php
                if ($eidos_parastatikou_type_id_org==24) { //apografi
                  echo gks_lang('Αφορά').':';
                } else {
                  echo gks_lang('Προς').':';
                }
                ?></span>
            </label>
            <div class="col-sm-4">
 
              <input id="pos_warehouses_id_to" type="text" class="form-control form-control-sm myneedsave pos_warehouses_id_to_elem" 
              value="<?php echo htmlspecialchars_gks($row['warehouse_name_to']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['pos_warehouses_id_to'];?>"
              style="display:<?php echo $pos_warehouses_id_to_elem_display;?>">

            </div>
            
          </div>      
        </div>
      </div>
            
      

    </div>
    
  </div>
</div>

<div id="test"></div>



<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-md-6">

      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποστολή - Πληρωμή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('delivery');?>> 


          <div class="row">
            <div class="col-lg-12 col-xl-6" style="margin-bottom:24px;">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι αποστολής');?>:</div>
              <?php

                  $sql="select * FROM gks_delivery_methods where delivery_method_disabled=0 ORDER BY mysortorder";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_apostoli = $result_select->fetch_assoc()) {
                    

              ?>
              <div style="white-space: nowrap1;">
                <input class="myneedsave" type="radio" name="radio_delivery_way" value="<?php echo $row_apostoli['id_delivery_method'];?>" id="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>"  
                data-type="<?php echo $row_apostoli['delivery_method_type'];?>" data-type-o="<?php echo $row_apostoli['delivery_method_type_pa'];?>"
                data-sxolio="<?php echo base64_encode($row_apostoli['delivery_method_sxolio']);?>"
                <?php if ($row['def_tropos_apostolis'] == $row_apostoli['id_delivery_method']) echo ' checked ';?>
                <?php if (!$perm_gks_pos_edit) echo 'disabled';?>> 
                
                <label for="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_apostoli['delivery_method_tooltip'];?>"><?php echo $row_apostoli['delivery_method_name'];?>

                </label>
                <?php
                if ($row_apostoli['id_delivery_method'] == 8) { ?>
                  <span id="span_delivery_id_8" style="<?php echo ($row['def_tropos_apostolis']==8 ? '' : 'display:none;');?>">
                  <br>
                  <select id="delivery_id_8" name="delivery_id_8" style="width11:90%;" class="form-control form-control-sm myneedsave" <?php if (!$perm_gks_pos_edit) echo 'disabled';?>>
                      <option value="0"><?php echo gks_lang('Επιλέξτε κατάστημα');?></option>
                      
                      <?php
                      $sql="SELECT id_warehouse,warehouse_name FROM gks_warehouses
                        WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0
                        ORDER BY gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name";
                      $result_select = $db_link->query($sql);        
                      if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error2');}
                      while ($row_select = $result_select->fetch_assoc()) {
                        echo '<option value="'.$row_select['id_warehouse'].'" ';
                        if ($row_select['id_warehouse']==$row['def_delivery_id_8']) echo ' selected ';
                        echo '>'.$row_select['warehouse_name'].'</option>';
                      }
                      ?>
                      
                  </select>
                  </span>

                <?php } ?>
              </div>
              <?php } ?>               
              
              <div id="delivery_method_sxolio" class="form-text text-muted" style="font-size:80%"></div>

                                
            </div>
            <div class="col-lg-12 col-xl-6">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι πληρωμής');?>:</div>
              <?php

              $def_tropos_pliromis_array=array();
              $temp=trim_gks($row['def_tropos_pliromis_array']);
              if ($temp!='') {
                $temp=json_decode($temp,true);
                if (is_array($temp)) $def_tropos_pliromis_array=$temp;
              }
              unset($temp);
              $temp2=[];$asset_ids=[];
              foreach ($def_tropos_pliromis_array as $value) {
                $value['asset_title']='';
                $temp2[$value['id']]=$value;
                $asset_ids[]=$value['asset_id'];
              } 
              $def_tropos_pliromis_array=$temp2;
              unset($temp2);
              //echo '<pre>';print_r($def_tropos_pliromis_array);die();
              
              if (count($asset_ids)>0) {
                $sql="select id_asset,asset_title 
                from gks_assets 
                where id_asset in (".implode(',',$asset_ids).")
                and asset_disable=0";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
                while ($row_select = $result_select->fetch_assoc()) {                
                  foreach ($def_tropos_pliromis_array as &$def_value) {
                    if ($def_value['asset_id']==$row_select['id_asset']) {
                      $def_value['asset_title']=$row_select['asset_title'];
                    }
                  }
                  unset($def_value);
                  
                }
              
              }
              
              
              $sql="select * FROM gks_payment_acquirers where payment_acquirer_disabled=0 ORDER BY mysortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
              while ($row_pliromi = $result_select->fetch_assoc()) {                
                
                  
              ?>
              <div style="white-space: nowrap1;">
                <input class="myneedsave" type="radio" name="radio_payment_way" value="<?php echo $row_pliromi['id_payment_acquirer'];?>" id="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" 
                data-type="<?php echo $row_pliromi['payment_acquirer_type'];?>" data-type-o="<?php echo $row_pliromi['payment_acquirer_type_dm'];?>" 
                data-sxolio="<?php echo base64_encode($row_pliromi['payment_acquirer_sxolio']);?>"
                data-button-html="<?php echo base64_encode($row_pliromi['payment_acquirer_button_html']);?>"
                data-aade_id="<?php echo $row_pliromi['aade_tropos_pliromis_id'];?>"
                data-payment_acquirer_with_id="<?php echo $row_pliromi['payment_acquirer_with_id'];?>"
                
                <?php if ($row['def_tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) echo ' checked ';?>
                <?php if (!$perm_gks_pos_edit) echo 'disabled';?>> 
                <input class="myneedsave" type="checkbox" name="radio_payment_way_check" value="<?php echo $row_pliromi['id_payment_acquirer'];?>"
                <?php if (isset($def_tropos_pliromis_array[$row_pliromi['id_payment_acquirer']])) echo 'checked';?> 
                <?php if (!$perm_gks_pos_edit) echo 'disabled';?>> 

                <label for="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_pliromi['payment_acquirer_tooltip'];?>">
                  <span class="gks_span_text">
                  <?php echo $row_pliromi['payment_acquirer_name'];?>
                  </span>
                </label>
                
                <?php if ($row_pliromi['payment_acquirer_with_id']>0) { ?>
                <div class="div_payment_one_terminal" style="<?php 
                  if (isset($def_tropos_pliromis_array[$row_pliromi['id_payment_acquirer']])==false) echo 'display:none;';
                ?>" data-one_pway="<?php echo $row_pliromi['id_payment_acquirer'];?>">

                  <button class="div_payment_one_terminal_start_css btn btn-sm btn-primary"><?php echo gks_lang('Πληρωμή με');?>:</button>
                  <input data-pp="-1000" data-pawid="<?php echo $row_pliromi['payment_acquirer_with_id'];?>" class="div_payment_one_terminal_terminal form-control form-control-sm" type="text" placeholder="<?php echo gks_lang('Τερματικό');?>"
                  data-asset_id="<?php
                  $asset_title='';
                  if (isset($def_tropos_pliromis_array[$row_pliromi['id_payment_acquirer']])) {
                    echo $def_tropos_pliromis_array[$row_pliromi['id_payment_acquirer']]['asset_id'];  
                    $asset_title=$def_tropos_pliromis_array[$row_pliromi['id_payment_acquirer']]['asset_title'];
                  }
                  ?>"
                  value="<?php echo $asset_title;?>">
                  
                </div>
                <?php } ?>                
                
                
              </div>
              <?php } ?>                              
                
                
                                
              <div id="payment_acquirer_sxolio" class="form-text text-muted" style="font-size:80%"></div>
              <div class="" style="display:none"><span id="button_html"><?php echo gks_lang('Πληρωμή τώρα');?></span></div>
            </div>
            
          </div>
          

                    
                  


                   
         
        </div>
      </div>



                   

<?php if ($GKS_CRM_ENABLE) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          CRM
        </div>
        <div class="card-body" <?php echo gks_card_body('crm_channel');?>> 
          <div class="form-group row">
            <label for="def_crm_channel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κανάλι πωλήσεων');?>:</label>
            <div class="col-md-8">
              <select id="def_crm_channel_id" class="form-control form-control-sm myneedsave" >
                <option value="0" data-contact="0" data-contact_filter="" data-campain="0" data-url="0" data-code="0" data-text="0"></option>
                <?php
                $sql_channel_sale="SELECT *
                FROM gks_crm_channel_sale
                WHERE crm_channel_sale_disabled=0
                ORDER BY crm_channel_sale_sortorder";
                $result_channel_sale = $db_link->query($sql_channel_sale);        
                if (!$result_channel_sale) {
                  debug_mail(false,'error sql',$sql_channel_sale);
                  die('sql error');
                }
                $row_channel_sale_selected=array(
                  'crm_channel_has_contact'=>0,
                  'crm_channel_has_contact_filter'=>'',
                  'crm_channel_has_campain'=>0,
                  'crm_channel_has_url'=>0,
                  'crm_channel_has_code'=>0,
                  'crm_channel_has_text'=>0,
                );
                
                while ($row_channel_sale = $result_channel_sale->fetch_assoc()) {
                  echo '<option value="'.$row_channel_sale['id_crm_channel_sale'].'" '.
                  'data-contact="'.intval($row_channel_sale['crm_channel_has_contact']).'" '.
                  'data-contact_filter="'.base64_encode(trim_gks($row_channel_sale['crm_channel_has_contact_filter'])).'" '.
                  'data-campain="'.intval($row_channel_sale['crm_channel_has_campain']).'" '.
                  'data-url="'.intval($row_channel_sale['crm_channel_has_url']).'" '.
                  'data-code="'.intval($row_channel_sale['crm_channel_has_code']).'" '.
                  'data-text="'.intval($row_channel_sale['crm_channel_has_text']).'" ';
                  if ($row_channel_sale['id_crm_channel_sale']==$row['def_crm_channel_id']) {
                    echo ' selected ';
                    $row_channel_sale_selected=$row_channel_sale;
                  }
                  echo '>'.$row_channel_sale['crm_channel_sale_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>



          <div class="form-group row" id="def_crm_channel_contact_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_contact']==0) echo 'display:none;';?>">
            <label for="def_crm_channel_contact_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή Πωλήσεων');?>:</label>
            <div class="col-md-8">
              <input id="def_crm_channel_contact_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['crm_channel_contact_gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['def_crm_channel_contact_id'];?>">
            </div>
          </div>


          <div class="form-group row" id="def_crm_channel_campain_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_campain']==0) echo 'display:none;';?>">
            <label for="def_crm_channel_campain_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καμπάνια');?>:</label>
            <div class="col-md-8">
              <input id="def_crm_channel_campain_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['ads_campain_name']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['def_crm_channel_campain_id'];?>">
            </div>
          </div>

          <div class="form-group row" id="def_crm_channel_url_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_url']==0) echo 'display:none;';?>">
            <label for="def_crm_channel_url" class="col-md-4 col-form-label form-control-sm text-md-right">URL:</label>
            <div class="col-md-8">
              <input id="def_crm_channel_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['def_crm_channel_url']);?>">
            </div>
          </div>
          <div class="form-group row" id="def_crm_channel_code_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_code']==0) echo 'display:none;';?>">
            <label for="def_crm_channel_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="def_crm_channel_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['def_crm_channel_code']);?>">
            </div>
          </div>
          
          <div class="form-group row" id="def_crm_channel_text_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_text']==0) echo 'display:none;';?>">
            <label for="def_crm_channel_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="def_crm_channel_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['def_crm_channel_text']);?></textarea>
            </div>
          </div>   


        </div>
      </div>

<?php } ?>

            


      
    </div>
                      
    <div class="col-md-6">
      
     
      
      <?php if (in_array($acc_eidos_parastatikou_id_org,[702,703,704])==false) { /*akurotiko */ ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπόλοιπο Επαφής');?>         
        </div>

        <div class="card-body" <?php echo gks_card_body('aff_bal');?>>  

          
          <div class="form-group row" id="div_def_affect_balance">
            <label for="def_affect_balance" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επηρεάζει το υπόλοιπο της επαφής');?>:</label>
            <div class="col-md-8">
              <input id="def_affect_balance" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['def_affect_balance']!=0) echo ' checked ';?> <?php if (!$perm_gks_pos_edit) echo 'disabled';?>>
              <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί η ρύθμιση όταν η κατάσταση του παραστατικού θα είναι μία από τις παρακάτω');?>:<br>
                <span style="line-height: 1.8;">
                <span class="acc_inv_state_080listing"><?php echo getAccInvStateDescr('080listing');?></span>
                <span class="acc_inv_state_090ekdosi"><?php echo getAccInvStateDescr('090ekdosi');?></span>
                <span class="acc_inv_state_100payment"><?php echo getAccInvStateDescr('100payment');?></span>
                </span>
              </small>
            </div>
          </div> 

          
          
          <div class="form-group row" id="div_def_affect_balance_all_poso" style="<?php if ($row['def_affect_balance']==0) echo 'display:none;';?>">
            <label for="def_affect_balance_all_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολόκληρο το ποσό');?>:</label>
            <div class="col-md-8">
              <input id="def_affect_balance_all_poso" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['def_affect_balance_all_poso']!=0) echo ' checked ';?> <?php if (!$perm_gks_pos_edit) echo 'disabled';?>>
              <small class="form-text text-muted" id="small_def_affect_balance_all_poso" style="<?php if (!($row['def_affect_balance']==0 or $row['def_affect_balance_all_poso']!=0)) echo 'display:none;';?>">
                <input type="radio" name="def_affect_balance_all_poso_type" value="price_net" id="def_affect_balance_all_poso_type_price_net" <?php
                if ($row['def_affect_balance_all_poso_type']=='price_net') echo ' checked';?>>
                  <label for="def_affect_balance_all_poso_type_price_net"    style="margin-bottom: 0px;"><?php echo gks_lang('Υποσύνολο');?></label><br>
                <input type="radio" name="def_affect_balance_all_poso_type" value="price_netfpa" id="def_affect_balance_all_poso_type_price_netfpa" <?php
                if ($row['def_affect_balance_all_poso_type']=='price_netfpa') echo ' checked';?>>
                  <label for="def_affect_balance_all_poso_type_price_netfpa" style="margin-bottom: 0px;"><?php echo gks_lang('Μικτό σύνολο');?></label><br>
                <input type="radio" name="def_affect_balance_all_poso_type" value="price_total" id="def_affect_balance_all_poso_type_price_total" <?php
                if ($row['def_affect_balance_all_poso_type']=='price_total') echo ' checked';?>>
                  <label for="def_affect_balance_all_poso_type_price_total" style="margin-bottom: 0px;"><?php echo gks_lang('Σύνολο');?></label><br>
                <input type="radio" name="def_affect_balance_all_poso_type" value="pliroteo" id="def_affect_balance_all_poso_type_pliroteo" <?php
                if ($row['def_affect_balance_all_poso_type']=='pliroteo') echo ' checked';?>>
                  <label for="def_affect_balance_all_poso_type_pliroteo" style="margin-bottom: 0px;"><?php echo gks_lang('Πληρωτέο');?></label>

              </small>
            </div>
          </div> 

          
                    
        </div>
      </div>
      <?php } ?>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>  

      
    </div>
                  
    
  </div>
</div>

</div>

<?php

$def_products_text='';
$temp=trim_gks($row['def_products']);
if ($temp!='') {
  $def_products=json_decode($temp,true);
  
  if (isset($def_products['text'])) $def_products_text=implode("\r\n",$def_products['text']);
}

?>

<div class="container-fluid" style="padding-top:0px">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλεγμένα Είδη');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('products');?>>
          <div class="row">

            <div class="col-md-6">
              <div class="card gks_card_expand">
                <div class="card-header" style="text-align:center">
                  <?php echo gks_lang('Συγκεκριμένα Είδη');?>
                </div>
               
                <div class="card-body" <?php echo gks_card_body('products1');?>>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <small class="form-text text-muted">
                        <?php echo gks_lang('Επιλέξτε συγκεκριμένα είδη.');?>
                        <br>
                        <?php echo gks_lang('Χρησιμοπιήστε το παρακάτω πλαίσιο για να εντοπίσετε τα είδη που θέλετε να συμπεριλάβετε');?>
                      </small>  
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <input id="def_products_ids_search" type="text" class="form-control form-control-sm myneedsave ui-autocomplete-input" value="" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <div>
                        
                        <table id="def_products_ids_table" class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
                        <thead>
                          <tr>	
                            <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
                            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
                            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
                            <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κωδικός');?></th> 
                            <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Προϊόν');?></th> 
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        
                        if (isset($def_products['ids']) and count($def_products['ids'])>0) {
                        
                        $sql="SELECT 
gks_eshop_products.id_product,
gks_eshop_products.product_code,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_photo<>'' THEN
        gks_eshop_products.product_photo
      ELSE
        gks_eshop_products_parent.product_photo
    END
  ELSE gks_eshop_products.product_photo

END as product_photo_p,
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
FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

where gks_eshop_products.product_disable=0 
and gks_eshop_products.product_class<>'variable'
and gks_eshop_products.id_product in(".implode(',',$def_products['ids']).")
order by gks_eshop_products.product_descr,gks_eshop_products.product_descr_variable";
                        
                        
                        $result_list = $db_link->query($sql); 
                        if (!$result_list) debug_mail(false,'error sql',$sql);
                        if (!$result_list) die('sql error');
                                                
                        $i = 0;
                        while ($row_list = $result_list->fetch_assoc()) {
                        
                          $i++;
                          ?>
                          <tr class="product_tr" data-id="<?php echo $row_list['id_product'];?>">
                            <th scope="row" nowrap class="mytdcm product_aa"><?php echo ($i);?></td>       
                            <td nowrap="" class="mytdcm">
                              <img src="img/delete.png" border="0" width="16" class="product_tr_delete" data-id="<?php echo $row_list['id_product'];?>">            
                            </td>
                            <td class="mytdcm"><?php echo getProductPhoto($row_list['id_product'],$row_list['product_photo_p'],32);?></td>
                            <td class="mytdcml" nowrap><?php echo $row_list['product_code'];?></td>  
                            <td class="mytdcml"><?php echo '<a href="admin-products-item.php?id='.$row_list['id_product'].'">'.$row_list['product_descr_p'].'</a>';?></td>  
                            
                          </tr>
                        <?php 
                        } 
                        
                        }?>
              
              
              
                        </tbody>
                        </table> 
                                                
                        
                      </div>
                    </div>
                  </div>                  
                  
                </div>
              </div>  
            </div>
<!--            
            <div class="col-md-6">
              <div class="card gks_card_expand">
                <div class="card-header" style="text-align:center">
                  <?php echo gks_lang('Κατηγορίες');?>
                </div>
               
                <div class="card-body" <?php echo gks_card_body('products2');?>>
                           test     
                </div>
              </div>  
            </div>
            
            <div class="col-md-6">
              <div class="card gks_card_expand">
                <div class="card-header" style="text-align:center">
                  <?php echo gks_lang('Μάρκες');?>
                </div>
               
                <div class="card-body" <?php echo gks_card_body('products3');?>>
                           test     
                </div>
              </div>  
            </div>
-->
            <div class="col-md-6">
              <div class="card gks_card_expand">
                <div class="card-header" style="text-align:center">
                  <?php echo gks_lang('Λέξεις Αναζήτησης');?>
                </div>
                <div class="card-body" <?php echo gks_card_body('products4');?>>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <small class="form-text text-muted">
                        <?php echo gks_lang('Εισάγετε τις λέξεις-φράσεις με τις οποίες θα γίνει αναζήτηση στα είδη για τον εντοπισμό τους.');?>
                        <br>
                        <?php echo gks_lang('Μία λέξη-φράση ανά γραμμή.');?>
                      </small>  
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <textarea id="def_products_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($def_products_text);?></textarea>
                    </div>
                  </div>
                              
                </div>
              </div>  
            </div>


            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">


     
<?php

  if ($perm_gks_pos_edit) {
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom">'.gks_lang('Αποθήκευση').'</button> ';
  }
  if ($perm_gks_pos_delete and $id>0) {
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger thisdeleterowbtn" data-id="'.($row['id_pos']>0 ? $row['id_pos'] : '').'" data-model="gks_pos" data-backurl="admin-pos.php">'.gks_lang('Διαγραφή').'</button> ';
  }

  if ($id>0 and $perm_gks_pos_add) {
    echo '<a href="admin-pos-item.php?id=-1&template_id='.$id.'" style="margin-bottom:10px;" '.
      'class="btn btn-primary tooltipster" '.
      'id="submit_button_template" '.
      'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'</div>">'.
      '<i class="fas fa-copy" style="font-size: 120%;"></i>'.
    '</a> ';
  }
 ?>     
      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-xl-6">
      <?php
      echo getObjectRels('gks_pos',$id);
      echo getActivityObjectTable('gks_pos',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_pos','id'=>$id));
      echo $obj_fileslist['html'];      
      ?>
    </div>

    
    
    <div class="col-xl-6">

    
      

      
      
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?> >       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($id>0) echo $id;?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">GUID:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['pos_guid'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>
        </div>      
      </div>              


    
    </div>
  </div>
</div>





<?php include_once 'admin-obj-send-message.php'; ?>




 
    


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_dialog_object_rel_curr='gks_pos';
var from_php_activity_model='gks_pos';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;
var from_php_template_id=<?php echo $template_id;?>;



var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 


var from_php_GKS_ACC_INV_COL_ITEMPRICE=<?php echo ($GKS_ACC_INV_COL_ITEMPRICE? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=<?php echo ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_COL_FPA=<?php echo ($GKS_ACC_INV_COL_FPA? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_EXTRA_OPEN=<?php echo ($GKS_ACC_INV_EXTRA_OPEN? 'true' : 'false')?>;



var from_php_acc_eidos_parastatikou_id=<?php echo $acc_eidos_parastatikou_id;?>;
var from_php_eidos_parastatikou_type_id=<?php echo $eidos_parastatikou_type_id;?>;
var from_php_eidos_parastatikou_need_prev=<?php echo $eidos_parastatikou_need_prev;?>;
var from_php_eidos_parastatikou_has_fpa=<?php echo $eidos_parastatikou_has_fpa;?>;
var from_php_eidos_parastatikou_has_othertaxes='<?php echo $eidos_parastatikou_has_othertaxes;?>';
var from_php_eidos_parastatikou_has_esoda=<?php echo $eidos_parastatikou_has_esoda;?>;
var from_php_eidos_parastatikou_has_eksoda=<?php echo $eidos_parastatikou_has_eksoda;?>;
var from_php_eidos_parastatikou_need_afm=<?php echo $eidos_parastatikou_need_afm;?>;
var from_php_eidos_parastatikou_balance_pros=<?php echo $eidos_parastatikou_balance_pros;?>;
var from_php_whi_eidos_parastatikou_stock_pros=<?php echo $whi_eidos_parastatikou_stock_pros_org;?>;
var from_php_whi_eidos_parastatikou_type_id=<?php echo $whi_eidos_parastatikou_type_id_org;?>;




var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','delete',$id);?>;



var from_php_pos_print_zoom=<?php echo intval($row['pos_print_zoom']*100);?>;

  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>

<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-pos-item.js"></script>
<script src="js/admin-pos-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-eftpos-transaction-dialog.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


