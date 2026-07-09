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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_orders_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','view',0);
$perm_gks_orders_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','edit',0);
$perm_gks_orders_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','add',0);
$perm_gks_orders_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','delete',0);

$gks_voip_params=gks_voip_user_params();
$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');



//$res=gks_order_recalc_from_db($id);echo '<pre>';print_r($res);die();


if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $nav_active_array=array('sales','sales_new_order');  
} else {
  $nav_active_array=array('sales','sales_orders');
}

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}


$lang_prepare_gks_monades_metrisis=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_monades_metrisis, array('monada_descr','monada_symbol'));
$sql="SELECT gks_monades_metrisis.id_monada,".
gks_lang_sql_field('monada_descr',$lang_prepare_gks_monades_metrisis).",".
gks_lang_sql_field('monada_symbol',$lang_prepare_gks_monades_metrisis)."
FROM ".$lang_prepare_gks_monades_metrisis['sql']['from1']." gks_monades_metrisis
".$lang_prepare_gks_monades_metrisis['sql']['from2']."
order by monada_sortorder,monada_descr";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$monades=array(); 
while ($row = $result->fetch_assoc()) {
  $monades[]=array(
    'id' => $row['id_monada'],
    'descr' => $row['monada_descr'],
    'symbol' => $row['monada_symbol'],
  );
}


$sql="SELECT id_fpa_base,fpa_base_descr
FROM gks_eshop_fpa_base
WHERE fpa_base_disable=0
ORDER BY fpa_base_sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$fpa_list=array(); 
while ($row = $result->fetch_assoc()) {
  $fpa_list[]=array(
    'id' => $row['id_fpa_base'],
    'descr' => $row['fpa_base_descr'],
  );
}

$sql="SELECT id_aade_katigoria_fpa, aade_katigoria_fpa_descr
FROM gks_aade_katigoria_fpa
where id_aade_katigoria_fpa not in (7,8)
ORDER BY sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$fpa_aade=array(); 
while ($row = $result->fetch_assoc()) {
  $fpa_aade[]=array(
    'id' => $row['id_aade_katigoria_fpa'],
    'descr' => $row['aade_katigoria_fpa_descr'],
  );
}

//print '<pre>';
//print_r($user_companys);
//die();

$has_production_line_or_sintagi=false;

$gks_custom_prepare = gks_custom_table_item_prepare('gks_orders',['from'=>'item']);



$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {
  
  if ($template_id>0) {

    $sql=select_gks_orders($id)." where gks_orders.id_order = ".$template_id;
    
    if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
    if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
    if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

    //print '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);die('sql error');
    }
    
    if ($result->num_rows!=1) {
      $template_id=0;
      debug_mail(false,'record not found sql tempate',$sql); 
      die('no record found (tempate)');
    } else {
      $row = $result->fetch_assoc();

      $row['id_order']=-1;
      $row['order_guid']='';
      $row['order_ref_number']='';
      $row['order_date']=date('Y-m-d H:i:s');
      $row['mydate_add']=null;
      $row['mydate_edit']=null;
      $row['user_id_add']=0;
      $row['user_id_edit']=0;
      $row['gks_nickname_add']='';
      $row['gks_nickname_edit']='';
      $row['myip']='';
    
      $row['order_state']='010draft';

      
                    
      $row['order_number_int']=0;
      $row['order_number_str']='';
  
      $row['print_date']='';
      $row['print_file_name']='';
      $row['print_file_url']='';
      $row['print_user_id']='';
      $row['print_order_state']='';

      $row['bank_deposit_9digit']='';
      unset($row['mdate_expire']);
      //$row['online_enable']=0;
      //$row['online_password']='';
    
      $row['gks_base_template_id']=$template_id;
      $my_page_title=gks_lang('Νέα Παραγγελία από το πρότυπο').' #'.$template_id;
      
      gks_plugins_functions_run('admin_orders_item_new_rec',array(
        'id'=>&$id,
        'row'=>&$row,
        'template_id' => $template_id,
      ));
            
    }
      
            
  }
  //echo '<pre>';print $template_id;die();
  
  if ($template_id==0) {
  
    
    $my_page_title=gks_lang('Νέα Παραγγελία');
    $row=array();
  
    $row['id_order']=-1;
    $row['order_guid']='';
    $row['order_ref_number']='';
    $row['order_date']=date('Y-m-d H:i:s');
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0;
    $row['gks_nickname_add']='';
    $row['gks_nickname_edit']='';
    $row['myip']='';
  
    $row['order_state']='010draft';
    $row['order_priority']=3;
    $row['order_occasion_id']=0;
    $row['user_id']=0;
    $row['gks_nickname']='';
    $row['user_first_name']='';
    $row['user_last_name']='';
    $row['user_email']='';
    $row['user_mobile']='';
    $row['user_lang']='el-GR';
    $row['parastatiko']=0;
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
    $row['ma_country_id']=91;
    $row['ma_nomos_id']=26;
    $row['address_extra']=-1;
    
      
    $row['occasion_title']='';
    $row['occasion_type_descr']='';
    $row['occasion_mydate_add']='';




    
    $row['note_doc']='';
    $row['note_logistirio']='';
    $row['note_production']='';
  
    
    $row['gks_price_net']=0;
    $row['gks_price_fpa']=0;
    $row['gks_price_netfpa']=0;
    $row['gks_price_total']=0;
    $row['products_posotita']=0;
    $row['products_varos']=0;
    $row['products_ogos']=0;
    $row['products_ogos_max_x']=0;
    $row['products_ogos_max_y']=0;
    $row['products_ogos_max_z']=0;
    $row['products_need_apostoli']=0;
    $row['products_need_pliromi']=0;
    
    $row['tropos_apostolis']=1;
    $row['tropos_pliromis']=1;
    $row['kostos_apostolis']=0;
    $row['kostos_pliromis']=0;
    $row['destination_data_name']='';
    $row['destination_data_phone']='';
    $row['destination_data_odos']='';
    $row['destination_data_arithmos']='';
    $row['destination_data_orofos']='';
    $row['destination_data_perioxi']='';
    $row['destination_data_poli']='';
    $row['destination_data_tk']='';
    $row['destination_data_country_id']=0;
    $row['destination_data_nomos_id']=0;
    
    $row['fiscal_position_id']=1;
    $row['pricelist_id']=1;
    
    $row['pelati_sxolio']='';
    $row['order_sxolio']='';
    
    $row['delivery_id_8']=1;
    $row['delivery_number']='';
    $row['vehicle_number']='';
    $row['dispatch_date']='';
    $row['dispatch_time']='';
  
    $row['coupons']='';
    $row['def_ekptosi']=0;
    
  
    $row['company_id']=0;
    $row['company_sub_id']=0;
    if (count($user_companys)>=1) {
      foreach ($user_companys as $value) {
        $row['company_id']=$value['id_company'];
        $row['company_sub_id']=$value['id_company_sub'];
        break;
      } 
    }
  
    $row['order_journal_id']=0;
    $row['order_seira_id']=0;
    $row['order_seira_code']='';
    $row['order_number_int']=0;
    $row['order_number_str']='';
    $row['is_xeirografi']=0;
    
    $row['acc_eidos_parastatikou_id']=0;
    $row['eidos_parastatikou_type_id']=0;
    $row['antisimvalomenos_label']=gks_lang('Πελάτης');
    $row['eidos_parastatikou_need_prev']=0;
    $row['eidos_parastatikou_has_fpa']=0;
    $row['eidos_parastatikou_need_afm']=1;
    $row['eidos_parastatikou_balance_pros']=0;
    $row['whi_eidos_parastatikou_stock_pros']=0;
    $row['whi_eidos_parastatikou_type_id']=0;
    
      
    $row['totalWithheldAmount']=0;
    $row['totalOtherTaxesAmount']=0;
    $row['totalStampDutyamount']=0;
    $row['totalFeesAmount']=0;
   
    $row['print_date']='';
    $row['print_file_name']='';
    $row['print_file_url']='';
    $row['print_user_id']='';
    $row['print_order_state']=''; 
  
    $row['affect_balance']=1;
    $row['affect_balance_all_poso']=1;
    $row['affect_balance_all_poso_type']='price_net';
    $row['affect_balance_poso']=0;
  
    $row['assigned_id']=0;
    $row['gks_nickname_assigned']='';
    $row['crm_channel_id']=0;
    $row['crm_channel_sale_descr']='';
    $row['crm_channel_contact_id']=0;
    $row['crm_channel_contact_gks_nickname']='';
    $row['crm_channel_campain_id']=0;
    $row['ads_campain_name']='';
    $row['crm_channel_url']='';
    $row['crm_channel_code']='';
    $row['crm_channel_text']='';
  
    $row['warehouses_id_from']=0;
    $row['warehouse_name_from']='';
    $row['warehouses_id_to']=0;
    $row['warehouse_name_to']='';
  
  
    $row['production_ergasies_total']=0;
    $row['production_sum_time']=0;
    $row['production_pososto']=0;
    $row['production_sintagi_total']=0;
    $row['production_kostos']=null;
    
    $row['bank_deposit_9digit']='';
    //$row['mdate_expire']='';
    $row['online_enable']=0;
    $row['online_password']='';
    $row['online_template_html_id']=0;
    

    gks_plugins_functions_run('admin_orders_item_new_rec',array(
      'id'=>&$id,
      'row'=>&$row,
      'template_id' => 0,
    ));
        
  }
  
  //print '<pre>';print_r($gks_user_settings);
  if (isset($gks_user_settings['gks_orders']['def_values'])) {
    $def_values=unserialize($gks_user_settings['gks_orders']['def_values']);  
    //print '<pre>';print_r($def_values);
    foreach ($def_values as $dkey => $dvalue) {
      if (isset($row[$dkey])) $row[$dkey]=$dvalue;
    }
  }
  
} else {

  if ($GKS_ORDERS_PRODUCTION) {
    $sql="SELECT id_production_line FROM gks_production_line WHERE order_id=".$id." limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);die('sql error');
    }
    if ($result->num_rows==1) {
      $has_production_line_or_sintagi=true;
    } else {
      $sql="SELECT id_production_sintagi FROM gks_production_sintagi WHERE order_id=".$id." limit 1";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);die('sql error');
      }
      if ($result->num_rows==1) {
        $has_production_line_or_sintagi=true;
      }
       
    }  
  }
  
  $sql=select_gks_orders($id)." where gks_orders.id_order = ".$id;
  
  if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Παραγγελία').': #'.$id;
  
  if ($row['order_date']=='') $row['order_date']=$row['mydate_add'];
}


$order_seira_id=$row['order_seira_id'];


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if (in_array($row['order_state'], array(
      '030forcancellation','040cancelled','050rejected','060registered','070inproduction','080failed',
      '060registered','070inproduction','080failed','090indelivery','095execute','100completed','110payment'))) {
  $gks_lock=true;
} else {
  if ($row['order_number_int'] > 0 and $row['is_xeirografi']==0 and 
    in_array($row['order_state'],array(
      '005prodraft','010draft','020pending','025offer','055wait_payment'))) { 
    $gks_number_lock=true;
  }
}

$antisimvalomenos_label=$row['antisimvalomenos_label'];
$acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
$eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=intval($row['eidos_parastatikou_need_prev']);
$eidos_parastatikou_has_fpa=intval($row['eidos_parastatikou_has_fpa']);

$eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
$eidos_parastatikou_balance_pros=intval($row['eidos_parastatikou_balance_pros']);
$whi_eidos_parastatikou_stock_pros=intval($row['whi_eidos_parastatikou_stock_pros']);
$whi_eidos_parastatikou_type_id=intval($row['whi_eidos_parastatikou_type_id']);

$antisimvalomenos_label_org=$antisimvalomenos_label;
$acc_eidos_parastatikou_id_org=$acc_eidos_parastatikou_id;
$eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;
$whi_eidos_parastatikou_stock_pros_org=$whi_eidos_parastatikou_stock_pros;
$whi_eidos_parastatikou_type_id_org=$whi_eidos_parastatikou_type_id;

//$connect_txt_id=(isset($row['connect_txt_id']) ? $row['connect_txt_id'] : '');
//echo $row['ma_country_id'];
//die();

//if ($id>0) {
//  
//  $mybasketarray['user']['first_name'] = trim_gks($row['user_first_name']);
//  $mybasketarray['user']['last_name'] = trim_gks($row['user_last_name']);
//  $mybasketarray['user']['email'] = trim_gks($row['user_email']);
//  $mybasketarray['user']['mobile'] = trim_gks($row['user_mobile']);
//  $mybasketarray['user']['lang'] = trim_gks($row['user_lang']);
//  $mybasketarray['user']['ma_odos'] = trim_gks($row['ma_odos']);
//  $mybasketarray['user']['ma_perioxi'] = trim_gks($row['ma_perioxi']);
//  $mybasketarray['user']['ma_poli'] = trim_gks($row['ma_poli']);
//  $mybasketarray['user']['ma_tk'] = trim_gks($row['ma_tk']);
//  $mybasketarray['user']['ma_country_id'] = intval($row['ma_country_id']);
//  $mybasketarray['user']['ma_nomos_id'] = intval($row['ma_nomos_id']);
//  
//  $mybasketarray['user']['eponimia'] = trim_gks($row['eponimia']);
//  $mybasketarray['user']['title'] = trim_gks($row['title']);
//  $mybasketarray['user']['afm'] = trim_gks($row['afm']);
//  $mybasketarray['user']['doy'] = trim_gks($row['doy']);
//  $mybasketarray['user']['epaggelma'] = trim_gks($row['epaggelma']);
//  
//
//  $mybasketarray['user_other']['first_name'] = trim_gks($row['other_first_name']);
//  $mybasketarray['user_other']['last_name'] = trim_gks($row['other_last_name']);
//  $mybasketarray['user_other']['email'] = trim_gks($row['other_email']);
//  $mybasketarray['user_other']['mobile'] = trim_gks($row['other_mobile']);
//  $mybasketarray['user_other']['lang'] = trim_gks($row['other_lang']);
//  $mybasketarray['user_other']['ma_odos'] = trim_gks($row['other_ma_odos']);
//  $mybasketarray['user_other']['ma_perioxi'] = trim_gks($row['other_ma_perioxi']);
//  $mybasketarray['user_other']['ma_poli'] = trim_gks($row['other_ma_poli']);
//  $mybasketarray['user_other']['ma_tk'] = trim_gks($row['other_ma_tk']);
//  $mybasketarray['user_other']['ma_country_id'] = intval($row['other_ma_country_id']);
//  $mybasketarray['user_other']['ma_nomos_id'] = intval($row['other_ma_nomos_id']);
//  
//  

//
//}


$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);
//$connect_txt_id=''; if (isset($row['connect_txt_id'])) $connect_txt_id=trim_gks($row['connect_txt_id']);

//$addressL1=trim_gks((empty($row['ma_odos']) ? '' : $row['ma_odos'].', ').$row['ma_perioxi']);
//if (endwith($addressL1,',')) $addressL1=substr($addressL1, 0, strlen($addressL1)-1);
//$addressL2=trim_gks((empty($row['ma_poli']) ? '' : $row['ma_poli'].', ').
//                (empty($row['nomos_descr']) ? '' : $row['nomos_descr'].', ').
//                $row['ma_tk']);
//if (endwith($addressL2,',')) $addressL1=substr($addressL2, 0, strlen($addressL2)-1);
//$addressL3=trim_gks($row['country_name']);
//
//$address='';
//if ($addressL1!='') $address.=$addressL1.'<br>';
//if ($addressL2!='') $address.=$addressL2.'<br>';
//$address.=$addressL3;
//if (endwith($address,'<br>')) $address=substr($address, 0, strlen($address)-4);  


$row['ma_country_id']=intval($row['ma_country_id']);
$row['ma_poli']=trim_gks($row['ma_poli']);

if (isset($row['ddate']) == false or empty($row['ddate'])) {
  
  $ddate_plus=time() + 10*24*60*60;
  $ddate_w=showDate($ddate_plus, 'w', 1);
  if ($ddate_w == 0 or $ddate_w==6) {
    $ddate_plus+=24*60*60;
    $ddate_w=showDate($ddate_plus, 'w', 1);
    if ($ddate_w == 0 or $ddate_w==6) {
      $ddate_plus+=24*60*60;
      $ddate_w=showDate($ddate_plus, 'w', 1);
      if ($ddate_w == 0 or $ddate_w==6) {
        $ddate_plus+=24*60*60;
        $ddate_w=showDate($ddate_plus, 'w', 1);
        if ($ddate_w == 0 or $ddate_w==6) {
          $ddate_plus+=24*60*60;
          $ddate_w=showDate($ddate_plus, 'w', 1);
        }
      }
    }
  }
  
  $row['ddate']=date('Y-m-d H:i:s',$ddate_plus);
  
}

$order_state=$row['order_state'];
$products_posotita=$row['products_posotita'];
$products_varos=$row['products_varos'];
$products_ogos=$row['products_ogos'];;
$products_ogos_max_x=$row['products_ogos_max_x'];
$products_ogos_max_y=$row['products_ogos_max_y'];
$products_ogos_max_z=$row['products_ogos_max_z'];
$products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
$products_need_pliromi=$row['products_need_pliromi']==0 ? false : true;

$pliroteo=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];

unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='order';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($row['company_id']);
$mybasketarray['company_sub_id']=intval($row['company_sub_id']);


$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']=$row['parastatiko'];

$mybasketarray['products_varos']= $products_varos;
$mybasketarray['products_ogos']= $products_ogos;
$mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
$mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
$mybasketarray['products_ogos_max_z']= $products_ogos_max_z;
$mybasketarray['products_need_apostoli']=$products_need_apostoli;
$mybasketarray['products_need_pliromi']=$products_need_pliromi;
$mybasketarray['destination_data']['name'] = trim_gks($row['destination_data_name']);
$mybasketarray['destination_data']['phone'] = trim_gks($row['destination_data_phone']);
$mybasketarray['destination_data']['odos'] = trim_gks($row['destination_data_odos']);
$mybasketarray['destination_data']['arithmos'] = trim_gks($row['destination_data_arithmos']);
$mybasketarray['destination_data']['orofos'] = trim_gks($row['destination_data_orofos']);
$mybasketarray['destination_data']['perioxi'] = trim_gks($row['destination_data_perioxi']);
$mybasketarray['destination_data']['poli'] = trim_gks($row['destination_data_poli']);
$mybasketarray['destination_data']['tk'] = trim_gks($row['destination_data_tk']);
$mybasketarray['destination_data']['country_id'] = intval($row['destination_data_country_id']);
$mybasketarray['destination_data']['nomos_id'] = intval($row['destination_data_nomos_id']);
$mybasketarray['tropos_apostolis'] = $row['tropos_apostolis'];
$mybasketarray['tropos_pliromis'] = $row['tropos_pliromis'];
$mybasketarray['products_total'] = $row['gks_price_total'];

$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray,-1);

//print '<pre>';
//print_r($mybasketarray);
//print_r($mybasketarray['tropoi_pliromis_all']);
//die();


if ($row['tropos_apostolis']>0 and isset($mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']])) $mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']]['dm_calc_kostos']= $row['kostos_apostolis'];
if ($row['tropos_pliromis']>0 and isset($mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']])) $mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']]['pa_calc_kostos']= $row['kostos_pliromis'];


$mybasketarray['coupons']=array();
$coupons = trim_gks($row['coupons']);
$coupons_parts=explode('|',$coupons);
foreach ($coupons_parts as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    $mybasketarray['coupons'][$value]=$value;
    $sql_coupon="SELECT pricelist_item_descr
    FROM gks_eshop_pricelist_items
    WHERE pricelist_item_coupon='".$db_link->escape_string($value)."'
    AND pricelist_id=".$row['pricelist_id'];
    $result_coupon = $db_link->query($sql_coupon);        
    if (!$result_coupon) {
      debug_mail(false,'error sql',$sql_coupon);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result_coupon->num_rows==1) {
      $row_coupon = $result_coupon->fetch_assoc();
      $mybasketarray['coupons'][$value]=$row_coupon['pricelist_item_descr'];
    }
    
  }
} 

//$mybasketarray['coupons']['Meion20']='Meion 20%';
//$mybasketarray['coupons']['Meion21']='Meion 21%';
//$mybasketarray['coupons']['Meion22']='Meion 22%';
//$mybasketarray['coupons']['Meion23']='Meion 23%';
//$mybasketarray['coupons']['Meion24']='Meion 24%';
//$mybasketarray['coupons']['Meion25']='Meion 25%';


//gks_orders_products.product_withheldPercentCategory,
//gks_orders_products.product_withheldAmount,
//gks_orders_products.product_otherTaxesPercentCategory,
//gks_orders_products.product_otherTaxesAmount,
//gks_orders_products.product_stampDutyPercentCategory,
//gks_orders_products.product_stampDutyAmount,
//gks_orders_products.product_feesPercentCategory,
//gks_orders_products.product_feesAmount,

$sql_eidi="SELECT gks_orders_products.*, 
gks_eshop_products.product_code, gks_eshop_products.product_descr_big, 
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
      WHEN gks_eshop_products.product_descr_small<>'' THEN
        gks_eshop_products.product_descr_small
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr_small
        END
    END
  ELSE gks_eshop_products.product_descr_small
END as product_descr_small_p,
gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
gks_eshop_pricelist.pricelist_descr,

gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr, 
gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr, 
gks_aade_katigoria_telon.aade_katigoria_telon_descr, 
gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
gks_eshop_products.product_lot_serial
FROM ((((((((gks_orders_products 
LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada) 
LEFT JOIN gks_eshop_fpa ON gks_orders_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
LEFT JOIN gks_eshop_pricelist ON gks_orders_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_orders_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
LEFT JOIN gks_aade_katigoria_xartosimou ON gks_orders_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou) 
LEFT JOIN gks_aade_katigoria_telon ON gks_orders_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_orders_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron

WHERE gks_orders_products.order_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1'))."
ORDER BY gks_orders_products.product_aa;";
//gks_orders_products.product_set
$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();
$products_sets=array();
$products_count=0;

$id_order_product_array=array();

while ($eidos = $result_eidi->fetch_assoc()) {
  $eidos_array[]=$eidos;
  $products_count++;
  $id_order_product_array[]=$eidos['id_order_product'];
  $parts=explode(',',trim_gks($eidos['product_set']));
  foreach ($parts as $myset) {
    $myset=trim_gks($myset);
    if ($myset!='') {
      if (isset($products_sets[$myset])==false) $products_sets[$myset]=array();
      $products_sets[$myset][]= $eidos['id_order_product'];
    }
  }
}


gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];
//print '<pre>';
//print_r($mybasketarray['check_vies']);
//die();




gks_cache_admin_orders_item();



$products_lots_serials=array();
if (count($id_order_product_array) > 0) {
  
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql_lots_serials="SELECT 
    gks_orders_products_lots.lot_product_id,
    order_product_id as id, 
    lot_product_quantity,
    gks_eshop_product_lots.lot_name, 
    gks_eshop_product_lots.lot_descr, 
    gks_eshop_product_lots.lot_date_production, 
    gks_eshop_product_lots.lot_date_expire, 
    gks_eshop_product_lots.lot_disabled
    FROM gks_orders_products_lots
    LEFT JOIN gks_eshop_product_lots ON gks_orders_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
    WHERE gks_orders_products_lots.order_product_id In (".implode(',',$id_order_product_array).")
    ORDER BY gks_orders_products_lots.id_order_product_lots";
    $result_lots_serials = $db_link->query($sql_lots_serials);        
    if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
    while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
      $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
    }
    
    //echo '<pre>';print_r($products_lots_serials);die();
    
  }
  
}


stat_record();

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';


?>

<link href="css/admin-orders-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-6" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3>
          <?php echo gks_lang('Παραγγελία');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span>
          <?php if (!empty($row['order_ref_number'])) {?>
          <?php echo gks_lang('Ref');?>: <span class="order_ref_number_head"><?php echo $row['order_ref_number'];?></span>
          <?php } ?>
        </h3>
      <?php } else { ?>
        <h3>
          <?php echo gks_lang('Παραγγελία');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span>
        </h3>
      <?php }?>
    </div>
    <div class="col-md-6" style="text-align:center">
      <?php if ($has_production_line_or_sintagi) {?>
      <a href="admin-production-item.php?id=<?php echo $id;?>">
        <button type="button" class="btn btn-primary"><?php echo gks_lang('Παραγωγή Παραγγελίας');?></button>
      </a>
      <?php } ?>
    </div>
  </div>
</div>


<div id="mypostform">
<div class="container-fluid" >
  <div class="row">
    
    <div class="col-md-4">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>
          
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="company_id_sub_id" type="hidden" value="'.$row['company_id'].'|'.$row['company_sub_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['company_title'];
                  if (isset($row['company_sub_title'])) echo ' \ '.$row['company_sub_title'];
                echo '</div>';
              } else {?>              
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value=""></option>
                <?php
                $company_id_sub_id=$row['company_id'].'|'.$row['company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ';
                  if ($row_select['id']==$company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
              <?php } ?>
            </div>
          </div> 
          

          <div class="form-group row">
            <label for="order_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="order_journal_id" type="hidden" value="'.$row['order_journal_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['acc_journal_descr'];
                echo '</div>';
              } else {?>
              <select id="order_journal_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id
                FROM (gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (31,32) and gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou not in (702,703,704)
                and is_disable=0 and company_id=".$row['company_id']." AND company_sub_id=".$row['company_sub_id'];
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
                  'data-need_afm="'.$row_select['eidos_parastatikou_need_afm'].'" '.
                  'data-balance_pros="'.$row_select['eidos_parastatikou_balance_pros'].'" '.
                  'data-whi_stock_pros="'.intval($row_select['whi_eidos_parastatikou_stock_pros']).'" '. // intval kanei to null se 0
                  'data-whi_type_id="'.intval($row_select['whi_eidos_parastatikou_type_id']).'" ';       // intval kanei to null se 0
                  
                  if ($row['order_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="order_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="order_seira_id" type="hidden" value="'.$row['order_seira_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['seira_code'].' - '.$row['seira_descr'];
                echo '</div>';
              } else {?>
              <select id="order_seira_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,is_xeirografi 
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['order_journal_id'];
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
                  if ($row['order_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="order_number_int" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['order_number_int'];
                echo '</div>';
              } else {?>
              <input id="order_number_int" class="form-control form-control-sm myneedsave" type="number" 
              value="<?php if ($row['order_number_int']>0) echo $row['order_number_int'];?>" style="max-width:100px;" 
              placeholder="" min="0" step="1"
              <?php if ($gks_number_lock or $row['is_xeirografi']==0) echo 'disabled';?>>
              <?php } ?>
            </div>
          </div> 
                                                            
          <div class="form-group row">
            <label for="order_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['order_date'])) echo  showDate(strtotime($row['order_date']), 'd/m/Y H:i', 1);
                echo '</div>';
              } else {?>
              <input id="order_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['order_date'])) echo  showDate(strtotime($row['order_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
              <?php } ?>
            </div>
          </div> 


          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8">
              <div class="form-control-sm gks_flock">
                <span class="order_state_<?php echo $row['order_state'];?>"><?php echo getOrderStateDescr($row['order_state']);?></span>
              </div>
            </div>
          </div> 
          

          
<?php  if ($GKS_ORDERS_PRODUCTION and ($row['production_ergasies_total']>0 or $row['production_sum_time']>0)) {?>   
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παραγωγή');?>:</label>
            <div class="col-sm-8 form-control-sm" style="text-align: center !important;vertical-align: middle !important;">
            <?php if ($row['production_ergasies_total']>0) {?>
              <a href="admin-production-item.php?id=<?php echo $row['id_order'];?>">
              <div class="progress" style="background-color: darkgray;">
                <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo number_format($row['production_pososto'],2,'.','');?>%" aria-valuenow="<?php echo number_format($row['production_pososto'],2,'.','');?>" aria-valuemin="0" aria-valuemax="100">
                  <?php echo number_format($row['production_pososto'],2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);?>%
                </div>
              </div>  
            
              </a>               
            <?php } 
            if ($row['production_sum_time']>0) {
              echo '<div>'.time_duration_format($row['production_sum_time']).'</div>';
            }
            ?>
            </div>
          </div>
<?php } ?> 
<?php  if ($GKS_ORDERS_PRODUCTION and $row['production_sintagi_total']>0 and isset($row['production_kostos'])) {?>   
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κόστος Παραγγελίας');?>:</label>
            <div class="col-sm-8">
              <div class="form-control-sm gks_flock">
                <span id="production_kostos"><?php if (isset($row['production_kostos'])) echo myCurrencyFormat($row['production_kostos']);?></span>
              </div>
            </div>
          </div> 
<?php } ?> 
          
          
          <?php 
          $aws_folder='';
          gks_plugins_functions_run('admin_orders_item_div1',array(
            'id'=>&$id,
            'row'=>&$row,
            'aws_folder' => &$aws_folder,
          ));

          if ($GKS_ORDERS_OCCASION) { ?>
          <div class="form-group row">
            <label for="order_occasion" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περίσταση');?>:</label>
            <div class="col-sm-8">
              <?php
              $occasion_title = '';
              $temp = trim_gks($row['occasion_title']);      if ($temp!='') $occasion_title.=$temp.' / ';
              $temp = trim_gks($row['occasion_type_descr']);         if ($temp!='') $occasion_title.=$temp.' / ';
              
              $temp =  trim_gks($row['occasion_mydate_add']);   if ($temp!='') $occasion_title.=showDate(strtotime($row['occasion_mydate_add']), 'd/m/Y H:i', 1) .' / ';
              if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);
              
              if ($gks_lock) {
                if ($row['order_occasion_id']>0) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo '<a href="admin-orders-occasion-item.php?id='.$row['order_occasion_id'].'">'.$occasion_title.'</a>';
                  echo '</div>';
                }
              } else {
              ?>
              <input id="order_occasion" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($occasion_title);?>" style="width:calc(98% - 22px);display:inline;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
              <input id="order_occasion_id" type="hidden" value="<?php echo $row['order_occasion_id'];?>" class="myneedsave">
              <?php if ($perm_gks_orders_edit) {?>
              <a id="autocomplete_order_occasion_id" tabindex="-1" href="admin-orders-occasion-item.php?id=<?php echo $row['order_occasion_id'];?>"  style="<?php if ($row['order_occasion_id']==0) echo 'display:none';?>" target="_blank"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
              <a id="add_order_occasion_id"          tabindex="-1" href="admin-orders-occasion-item.php?id=-1&user_id=<?php echo $row['user_id'];?>" style="<?php if ($row['order_occasion_id']>0 ) echo 'display:none';?>" target="_blank"><i class="fas fa-plus-circle" style="font-size:140%;vertical-align: middle;"></i></a>
              <?php }} ?>
            </div>
          </div> 
         <?php } ?>  
         
     
          <div class="form-group row">
            <label for="fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['fiscal_position_descr'];
                echo '</div>';
              } else {?>              
              <select id="fiscal_position_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
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
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['pricelist_descr'];
                echo '</div>';
              } else {?>
              <select id="pricelist_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
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
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div>  
          <div class="form-group row">
            <label for="def_ekptosi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένη έκπτωση');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo myNumberFormatNo0Local($row['def_ekptosi']).'%';
                echo '</div>';
              } else {?>
              <input id="def_ekptosi" type="number" class="form-control form-control-sm myneedsave" value="<?php echo number_format($row['def_ekptosi'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="max-width:100px;display: inline-block;" placeholder="" min="0" step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"> %
              <button class="btn btn-sm btn-primary" id="def_ekptosi_set"><?php echo gks_lang('Εφαρμογή');?></button>
              <?php } ?>
            </div>
          </div>               
          
          <div class="form-group row">
            <label for="assigned_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-sm-8">
              <input id="assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['assigned_id'];?>">
            </div>
          </div>

          
                                  

        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κουπόνια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('koup');?>>
          <?php if ($gks_lock==false) {?>
          <div style="text-align: center;">
            <input type="text" class="form-control form-control-sm myneedsave" value="" style="max-width:180px;text-align:left;display: inline;vertical-align: middle;" id="input_coupon" />
            <span style="" id="coupon_use" class="btn btn-sm btn-primary"><?php echo gks_lang('Προσθήκη Κουπονιού');?></span>
          </div>
          <?php } ?>
          <div id="coupons_html" class="form-control-sm">   
<?php
            $coupons_html='';
            foreach ($mybasketarray['coupons'] as $key => $coupon) {
               $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
               <span class="coupons text-sm">'.$key.
               ($gks_lock ? '' : ' <i class="coupon_delete fas fa-trash-alt gks_order_delete_icon" data-coupon="'.$key.'" style=""></i>').
               '</span></span> ';
            } 
            if ($coupons_html!='') {
              $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
              echo $coupons_html;
            }
?>            
          </div>         
        </div>
      </div>
    </div>
    
    
    <div class="col-md-8">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" id="antisimvalomenos_label">
          <?php echo $antisimvalomenos_label;?> 
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>

          <div class="form-group row">
            <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-4">
            <?php if ($gks_lock) {
              echo '<div class="gks_flock form-control-sm">';
                if ($row['user_id']>0)
                  echo '<a href="admin-users-item.php?id='.$row['user_id'].'" class="email_contact_name">'.$row['gks_nickname'].'</a>';
                else
                   echo '<span class="email_contact_name">'.$row['gks_nickname'].'</span>';
                echo '<input type="hidden" id="user_id" value="'.$row['user_id'].'">';
              echo '</div>';                
            } else {?>
                  <input id="user" type="text" class="form-control form-control-sm myneedsave email_contact_name" <?php if ($gks_user_lock) echo 'disabled';?>
                  value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
                  style="width:calc(98% - 22px);display:inline;" 
                  placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
                  <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
                  <?php if ($perm_gks_orders_edit) {?>
                  <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                  <i id="user_save" class="fas fa-save" style="<?php if ($row['user_id']>0) echo 'display:none';?>;color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
                  <?php } ?>
            <?php } ?>
            </div>
            <?php if ($gks_lock==false) {?>
            <label for="" class="col-sm-2 col-form-label form-control-sm text-sm-right"><a class="tooltipster" title="<?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?>" href="https://www.aade.gr/epiheiriseis/forologikes-ypiresies/mitroo/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon" target="_blank">aade.gr</a>:</label>
            <div class="col-sm-4">
              <button style="" id="btn_gsis_get" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση με το ΑΦΜ');?></button>
            </div>
            <?php } ?>
          </div>


          <div class="form-group row" style="margin-bottom: 0px;">
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
                <div style="text-align:right;width:100%;margin-bottom: 10px;">
<?php if ($GKS_ORDERS_PRODUCTION) {?>                  
                  <i id="copy_text_pelati_sxolio_to_production" class="fas fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για παραγωγή</b>');?>"></i>
<?php } ?>
                  <i id="copy_text_pelati_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
                </div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
                <div style="text-align:right;width:100%;margin-bottom: 10px;">
<?php if ($GKS_ORDERS_PRODUCTION) {?>                  
                  <i id="copy_text_order_sxolio_to_production" class="fas fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για παραγωγή</b>');?>"></i>
<?php } ?>
                  <i id="copy_text_order_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
                </div>
              </div>               
            </div>
          </div>









          <div class="form-group row">
            <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['user_first_name'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_first_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_first_name']);?>">
              <?php } ?>
            </div>
            <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['user_last_name'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_last_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_last_name']);?>">
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['user_email'])) echo '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a>';
                echo '</div>'; 
                echo '<input id="dr_user_email" type="hidden" value="'.htmlspecialchars_gks($row['user_email']).'">';               
              } else {?>
              <input id="dr_user_email" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_email']);?>">
              <?php } ?>
            </div>
            <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm" id="dr_user_mobile">';
                  if (isset($row['user_mobile'])) echo '<a href="tel:'.$row['user_mobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row['user_mobile'].'</a>';
                  echo $gks_voip_params['html_after_span'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_mobile" type="text" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_mobile']);?>">
              <?php echo $gks_voip_params['html_after_input'];?>
              <?php } ?>
            </div>
          </div>
              
          <div class="form-group row">
            <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['lang_name'];
                echo '</div>';  
                echo '<input type="hidden" id="dr_user_lang" value="'.$row['user_lang'].'">';              
              } else {?>
              <select id="dr_user_lang" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
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
                  if ($row['user_lang'] == $row_select['id_lang']) echo ' selected ';
                  echo '>'.$row_select['lang_name'].'</option>';
                }
                ?>
              </select>                  
              <?php } ?>
            </div>
          </div>
          

          <div class="row">  
            <div class="col-md-12">
              <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Τύπος Παραστατικού');?></div>
            </div>
          </div>   
          <div class="row">  
            <div class="col-md-12 form-control-sm text-sm-center">
              <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="0" id="form_parastatiko_apodiji"   <?php echo ($row['parastatiko'] == 0 ? ' checked ' : '');?> <?php if ($gks_lock) echo 'disabled';?>> <label class="gks_label" for="form_parastatiko_apodiji" style="display:inline;padding-right:18px" ><?php echo gks_lang('Απόδειξη');?></label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="1" id="form_parastatiko_timologio" <?php echo ($row['parastatiko'] == 1 ? ' checked ' : '');?> <?php if ($gks_lock) echo 'disabled';?>> <label class="gks_label" for="form_parastatiko_timologio" style="display:inline"><?php echo gks_lang('Τιμολόγιο');?></label></span>
            </div>
          </div>   
          <div id="div_parastatiko_timologio" <?php echo ($row['parastatiko'] == 0 ? ' style="display: none;" ' : '');?>>
            <div class="form-group row">
              <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['eponimia'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_eponimia" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['eponimia']);?>" >
              <?php } ?>
              </div>
              <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['title'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_title" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['title']);?>" >
              <?php } ?>
              </div>
            </div>
            <?php
            $ee_initials='';
            $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
            gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
            $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
            FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
            ".$lang_prepare_gks_country['sql']['from2']."
            where id_country=".$row['ma_country_id'];
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
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    if ($ee_initials!='') echo $ee_initials.' ';
                    echo $row['afm'];
                    echo '<input type="hidden" id="dr_user_afm" value="'.$row['afm'].'">';
                    echo '<span 
                      id="dr_user_afm_views_run" style="'.($check_vies['run'] ? 'display:inline-block;' : 'display:none;').'padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';

                    //if ($check_vies['run']) echo '<span style="display:inline-block;padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';
                  echo '</div>';                
                } else {?>
                <span id="dr_user_afm_ee_initials" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><input 
                  style="display: inline-block;max-width:100%;text-align:left;vertical-align: middle;<?php echo ($ee_initials=='' ? 'width:100%;' : 'width:calc(100% - 75px);');?>"
                  id="dr_user_afm" type="text" class="form-control form-control-sm myneedsave <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['afm']);?>" ><span 
                  id="dr_user_afm_views_run" style="<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>
                <?php
                //$out.='<span id="dr_user_afm_ee_initials" style="'.($ee_initials!='' ? '' : 'display:none;').';">'.$ee_initials.'</span>';
                //$out.='<span id="dr_user_afm_views_run" style="'.($check_vies['run'] ? '' : 'display:none;').'">'.$views_run_img.'</span>';
                ?>
                <?php } ?>
              </div>
              <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['doy'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_doy" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['doy']);?>" >
                <?php } ?>
              </div>
            </div>


            <div class="form-group row">
              <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
              <div class="col-sm-10">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['epaggelma'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_epaggelma" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['epaggelma']);?>" >
                <?php } ?>
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
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_odos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_odos']);?>" >
              <small class="form-text text-muted auto_googlemaps" id="dr_user_ma_odos_auto_googlemaps"></small>
              <?php } ?>
            </div>

            <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_arithmos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_arithmos']);?>" >
              <?php } ?>
            </div>            
          </div>
          <div class="form-group row">
            <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_orofos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_orofos']);?>" >
              <?php } ?>
            </div>            
            <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_perioxi'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_perioxi']);?>" >
              <?php } ?>
            </div>
            
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_poli'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_poli']);?>" >
              <?php } ?>
            </div>
            <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_tk'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_tk']);?>" >
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                  echo '<input type="hidden" id="dr_user_ma_country_id_h" value="'.$row['ma_country_id'].'">';
                echo '</div>';                
              } else {?>
              <select data-dbval="<?php echo $row['ma_country_id'];?>" id="dr_user_ma_country_id" <?php if ($gks_user_lock) echo 'disabled';?> class="form-control form-control-sm myneedsave">
              </select> 
              <?php } ?>
            </div>
            <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                echo '</div>';
              } else {?>
              <select id="dr_user_ma_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['ma_country_id']." ORDER BY nomos_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row['ma_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }
                ?>
              </select> 
              <?php } ?>
            </div>
          </div>   
          
          
          






          <div id="div_select_apostoli">
            <div class="row">  
              <div class="col-md-12">
                <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Αποστολή');?></div>
              </div>
            </div> 

            <?php 
            $selected_ea=array();
            $selected_ea['ea_name']='';
            $selected_ea['ea_phone']='';
            $selected_ea['ea_odos']='';
            $selected_ea['ea_arithmos']='';
            $selected_ea['ea_orofos']='';
            $selected_ea['ea_perioxi']='';
            $selected_ea['ea_poli']='';
            $selected_ea['ea_tk']='';
            $selected_ea['ea_country_id']=0;
            $selected_ea['country_name']='';
            $selected_ea['ea_nomos_id']=0;
            $selected_ea['nomos_descr']='';
            
            $selected_ea_option='';              
            
            $sql="SELECT gks_users_extra_address.*, country_name,nomos_descr
            FROM (gks_users_extra_address 
            LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
            LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
            WHERE (gks_users_extra_address.user_id=".$row['user_id']." and gks_users_extra_address.user_id>0)
            or (gks_users_extra_address.order_id=".$id.")
            
            ORDER BY gks_users_extra_address.id_users_extra_address";
            $result_select = $db_link->query($sql);        
            if (!$result_select) {
              debug_mail(false,'error sql',$sql);
              die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
            }

            while ($row_select = $result_select->fetch_assoc()) {
              $row_select['country_name']=gks_lang_data_trans($row_select['country_name'],$row_select['ea_country_id'],'gks_country','country_name');
              $row_select['nomos_descr']=gks_lang_data_trans($row_select['nomos_descr'],$row_select['ea_nomos_id'],'gks_nomoi','nomos_descr');
              
              $address_name=$row_select['ea_name'].', '.trim_gks($row_select['ea_odos'].' '.$row_select['ea_arithmos']).', '.$row_select['ea_orofos'].', '.$row_select['ea_perioxi'].', '.$row_select['ea_poli'].', '.$row_select['ea_tk'].', '.$row_select['country_name'].', '.$row_select['nomos_descr'];
            
              $address_name=str_replace(', , ', ', ', $address_name);
              $address_name=str_replace(', , ', ', ', $address_name);
              $address_name=str_replace(', , ', ', ', $address_name);
              $address_name=str_replace(', , ', ', ', $address_name);
              
              if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
              if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
              if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
            
              if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
              if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
              if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
              
              
              $selected_ea_option.= '<option value="'.$row_select['id_users_extra_address'].'" ';
              if ($row['address_extra'] == $row_select['id_users_extra_address']) {
                $selected_ea_option.= ' selected ';
                $selected_ea=$row_select;
              }
              $selected_ea_option.= '>'.$address_name.'</option>';
            }
                        
          
            if ($gks_lock) {
              if ($row['address_extra']==-1) {
                echo '<div class="form-group row"><div class="col-sm-12 text-center">';
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang('Αποστολή στην ίδια διεύθυνση');
                  //echo $selected_ea['ea_name'];
                echo '</div></div></div>';
              }
            } else {?>            
            <div class="form-group row">
              <div class="col-sm-8 offset-sm-2">
                <select id="form_select_apostoli" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                  <option value="-1" <?php echo ($row['address_extra']==-1 ? ' selected ' : '');?>><?php echo gks_lang('Αποστολή στην ίδια διεύθυνση');?></option>
                  <?php
                  echo $selected_ea_option;
                  ?>
                  <option value="0" <?php echo ($row['address_extra']==0 ? ' selected ' : '');?>>-- <?php echo gks_lang('Δημιουργία νέας διεύθυνσης');?> --</option>
                </select> 
              </div>
            </div>
            <?php } ?>      
                  
          
            <div id="div_extra_address" style="<?php echo ($row['address_extra']==-1 ? 'display: none;' : '');?>">
              <?php if ($gks_lock==false) { ?>
              <div class="row">  
                <div class="col-md-12">
                  <div class="text-sm-center" style="font-weight111: bold;"><?php echo gks_lang('Πληροφορίες αποστολής');?></div>
                </div>
              </div> 
              <?php } ?>              
                            
              <div class="form-group row">
                <label for="form_ea_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_name'].'ddd';
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_name']);?>" >
                  <?php } ?>
                </div>
                <label for="form_ea_phone" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo '<a href="tel:'.$selected_ea['ea_phone'].'">'.$selected_ea['ea_phone'].'</a>';
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_phone" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_phone']);?>" >
                  <?php } ?>
                </div>
              </div>              
              <div class="form-group row">
                <label for="form_ea_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_odos'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_odos']);?>" >
                  <small class="form-text text-muted auto_googlemaps" id="form_ea_odos_auto_googlemaps"></small>
                  <?php } ?>
                </div>
                
                <label for="form_ea_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_arithmos'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_arithmos']);?>" >
                  <?php } ?>
                </div>                
              </div>              
              <div class="form-group row">
                <label for="form_ea_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_orofos'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_orofos']);?>" >
                  <?php } ?>
                </div>
                <label for="form_ea_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_perioxi'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_perioxi']);?>" >
                  <?php } ?>
                </div>
                
              </div>              
              <div class="form-group row">
                <label for="form_ea_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_poli'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_poli']);?>" >
                  <?php } ?>
                </div>
                <label for="form_ea_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['ea_tk'];
                    echo '</div>';
                  } else {?>
                  <input id="form_ea_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_tk']);?>" >
                  <?php } ?>
                </div>
              </div>              

              <div class="form-group row">
                <label for="form_ea_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['country_name'];
                      
                    echo '</div>';
                  } else {?>
                  <select data-dbval="<?php echo $selected_ea['ea_country_id'];?>" id="form_ea_country_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                  </select> 
                  <?php } ?>
                </div>
                <label for="form_ea_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $selected_ea['nomos_descr'];
                    echo '</div>';
                  } else {?>
                  <select id="form_ea_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                    <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                    <?php
                    $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                    $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                    FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                    ".$lang_prepare_gks_nomos['sql']['from2']."
                    where country_id=".$selected_ea['ea_country_id']." ORDER BY nomos_descr";

                    $result_select = $db_link->query($sql);        
                    if (!$result_select) {
                      debug_mail(false,'error sql',$sql);
                      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                    }
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_nomos'].'" ';
                      if ($selected_ea['ea_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                      echo '>'.$row_select['nomos_descr'].'</option>';
                    }
                    ?>
                  </select> 
                  <?php } ?>
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
        if ($row['warehouses_id_from']==1 or $row['warehouses_id_from']==2) {$row['warehouses_id_from']=0;$row['warehouse_name_from']='';}
        if ($row['warehouses_id_to']==1   or $row['warehouses_id_to']==2)   {$row['warehouses_id_to']=0;$row['warehouse_name_to']='';}
        
        
        
        $warehouses_id_from_elem_display='';
        $warehouses_id_to_elem_display='';
        $div_show_user_display='';
        if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
          $div_show_user_display='none';
          $warehouses_id_from_elem_display='none';
        } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
          $div_show_user_display='none';
        } else {
          if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
            $warehouses_id_from_elem_display='none';
          } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
            $warehouses_id_to_elem_display='none';
          }
        }
        //echo '|'.$eidos_parastatikou_stock_pros_org.'|'.$warehouses_id_from_elem_display.'|'.$warehouses_id_to_elem_display.'|';  die();
        ?>
        
        <div class="card-body" <?php echo gks_card_body('warehouse');?>>
          
          <div class="form-group row">
            
            <label for="warehouses_id_from" class="col-sm-2 col-form-label form-control-sm text-sm-right">
              <span class="warehouses_id_from_elem" style="display:<?php echo $warehouses_id_from_elem_display;?>"><?php echo gks_lang('Από');?>:</span>
            </label>
            <div class="col-sm-4">
            <?php if ($gks_lock) {
              if ($warehouses_id_from_elem_display=='') {
                echo '<div class="gks_flock form-control-sm">';
                  echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_from'].'">'.$row['warehouse_name_from'].'</a>';
                echo '</div>'; 
              }               
            } else {?>              
              <input id="warehouses_id_from" type="text" class="form-control form-control-sm myneedsave warehouses_id_from_elem" 
              value="<?php echo htmlspecialchars_gks($row['warehouse_name_from']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['warehouses_id_from'];?>"
              style="display:<?php echo $warehouses_id_from_elem_display;?>">
            <?php } ?>
            </div>

            <label for="warehouses_id_to" class="col-sm-2 col-form-label form-control-sm text-sm-right">
              <span class="warehouses_id_to_elem" style="display:<?php echo $warehouses_id_to_elem_display;?>"><?php
                if ($eidos_parastatikou_type_id_org==24) { //apografi
                  echo gks_lang('Αφορά').':';
                } else {
                  echo gks_lang('Προς').':'; 
                }
                ?></span>
            </label>
            <div class="col-sm-4">
            <?php if ($gks_lock) {
              if ($warehouses_id_to_elem_display=='') {
                echo '<div class="gks_flock form-control-sm">';
                  echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_to'].'">'.$row['warehouse_name_to'].'</a>';
                echo '</div>';  
              }              
            } else {?> 
              <input id="warehouses_id_to" type="text" class="form-control form-control-sm myneedsave warehouses_id_to_elem" 
              value="<?php echo htmlspecialchars_gks($row['warehouse_name_to']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['warehouses_id_to'];?>"
              style="display:<?php echo $warehouses_id_to_elem_display;?>">
            <?php } ?>
            </div>
            
          </div>      
        </div>
      </div>      
       
    </div>
  </div>
</div>



<div class="container-fluid" >
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?> id="eidi_table"> 
          <?php

          $gkscols1='';
          $gkscols2='';
          $gkscols3='';
          $gkscols4='';
          $gkscols5='';
          $gkscols6='';
          $gkscols7='';
          $gkscols8='';
          $gkscols9='';
          $gkscols10='';

          
//          $perm_gks_orders_edit=false;
//          $GKS_ORDERS_COL_ITEMPRICE=true;
//          $GKS_ORDERS_COL_FPA=true;
//          $GKS_ORDERS_SHEETS=false;
          
          $my_cols='';
          if ($perm_gks_orders_edit) {
            $my_cols.= '1';
            $my_cols.= ($GKS_ORDERS_COL_ITEMPRICE ? '1' : '0');
            $my_cols.= ($GKS_ORDERS_COL_FPA ? '1' : '0');
            $my_cols.= ($GKS_ORDERS_SHEETS ? '1' : '0');
          } else {
            $my_cols.= '0';
            $my_cols.= '0';//GKS_ORDERS_COL_FPA
            $my_cols.= '0';//GKS_ORDERS_COL_FPA
            $my_cols.= ($GKS_ORDERS_SHEETS ? '1' : '0');
          }
          
          switch ($my_cols) {   
            case '1111':     
              $gkscols1 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col'; //codikos
              $gkscols2 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col'; //peigrafi
              $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-1 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //selides
              $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //posotita
              $gkscols6 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //ekptosi
              $gkscols7 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //sinolo
              $gkscols8 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col'; //info
              $gkscols9 ='col-3  col-sm-2  col-md-1  col-lg-1 gks_items_col'; //fpa
              $gkscols10='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //timi
              break;
            case '1110':     
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col'; //codikos     
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-2 gks_items_col'; //peigrafi   
              $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //selides     
              $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //posotita    
              $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //ekptosi                 
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col'; //sinolo                  
              $gkscols8 ='col-6  col-sm-2  col-md-1  col-lg-1 gks_items_col'; //info                    
              $gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //fpa                     
              $gkscols10='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //timi                    
              break;
            case '1100':     
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';                       //codikos      
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-3 gks_items_col';                       //peigrafi    
              $gkscols3 ='col-12 col-sm-4  col-md-3  col-lg-2 gks_items_col';                       //paratiriseis 
              $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';                       //selides      
              $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //posotita     
              $gkscols6 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //ekptosi      
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //sinolo       
              $gkscols8 ='col-6  col-sm-4  col-md-1  col-lg-1 gks_items_col offset-6 offset-sm-0 '; //info                     
              //$gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';                     //fpa          
              $gkscols10='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //timi         
              break;
            case '1000':     
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col'; //codikos     
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-4 gks_items_col'; //peigrafi   
              $gkscols3 ='col-12 col-sm-12 col-md-6  col-lg-2 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //selides     
              $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col'; //posotita    
              $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //ekptosi                 
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col'; //sinolo                  
              $gkscols8 ='col-6  col-sm-1  col-md-1  col-lg-1 gks_items_col'; //info                    
              $gkscols9='';                                                   //fpa         
              $gkscols10='';                                                  //timi        
              break;
            case '1101':     
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col'; //codikos     
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-2 gks_items_col'; //peigrafi   
              $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //selides    11111  
              $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //posotita    
              $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //ekptosi                 
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col'; //sinolo                  
              $gkscols8 ='col-6  col-sm-2  col-md-1  col-lg-1 gks_items_col'; //info                     
              $gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //fpa        11111             
              $gkscols10='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //timi  
              break;
            case '1011':     
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col'; //codikos     
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-2 gks_items_col'; //peigrafi   
              $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //selides      
              $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //posotita    
              $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //ekptosi                 
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col'; //sinolo                  
              $gkscols8 ='col-6  col-sm-2  col-md-1  col-lg-1 gks_items_col'; //info                     
              $gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //fpa        11111             
              $gkscols10='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col'; //timi       11111  
              break;
            case '1010': //case '1100':
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';                       //codikos      
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-3 gks_items_col';                       //peigrafi    
              $gkscols3 ='col-12 col-sm-4  col-md-3  col-lg-2 gks_items_col';                       //paratiriseis 
              $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';                       //selides      
              $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //posotita     
              $gkscols6 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //ekptosi      
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //sinolo       
              $gkscols8 ='col-6  col-sm-4  col-md-1  col-lg-1 gks_items_col offset-6 offset-sm-0 '; //info                     
              $gkscols9 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //fpa           1111
              $gkscols10='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //timi         1111
              break;
            case '1001': 
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';                       //codikos      
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-3 gks_items_col';                       //peigrafi    
              $gkscols3 ='col-12 col-sm-4  col-md-3  col-lg-2 gks_items_col';                       //paratiriseis 
              $gkscols4 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //selides      
              $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //posotita     
              $gkscols6 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //ekptosi      
              $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //sinolo       
              $gkscols8 ='col-6  col-sm-4  col-md-1  col-lg-1 gks_items_col offset-6 offset-sm-0 '; //info                     
              $gkscols9 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //fpa           1111
              $gkscols10='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';                       //timi         1111
              break;


            //$perm_gks_orders_edit == false
            case '0000': 
              $gkscols1 ='col-12 col-sm-4  col-md-4  col-lg-2 gks_items_col';
              $gkscols2 ='col-12 col-sm-8  col-md-8  col-lg-5 gks_items_col';
              $gkscols3 ='col-12 col-sm-8  col-md-8  col-lg-4 gks_items_col';
              $gkscols4 ='col-6  col-sm-6  col-md-3  col-lg-1 gks_items_col';            
              $gkscols5 ='col-12 col-sm-4  col-md-4  col-lg-1 gks_items_col';            
              $gkscols6 ='col-3  col-sm-2  col-md-1  col-lg-1 gks_items_col';            
              $gkscols7 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';            
              $gkscols8 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
              $gkscols9 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
              $gkscols10='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col';   
              break;
            case '0001': 
              $gkscols1 ='col-12 col-sm-6  col-md-6  col-lg-2 gks_items_col'; //codikos     
              $gkscols2 ='col-12 col-sm-6  col-md-6  col-lg-4 gks_items_col'; //peigrafi   
              $gkscols3 ='col-12 col-sm-12 col-md-6  col-lg-4 gks_items_col'; //paratiriseis
              $gkscols4 ='col-6  col-sm-6  col-md-3  col-lg-1 gks_items_col'; //selides     
              $gkscols5 ='col-6  col-sm-6  col-md-3  col-lg-1 gks_items_col'; //posotita    
              $gkscols6 ='col-3  col-sm-2  col-md-1  col-lg-1 gks_items_col'; //ekptosi     
              $gkscols7 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col'; //sinolo      
              $gkscols8 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col'; //info        
              $gkscols9 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col'; //fpa         
              $gkscols10='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col'; //timi        
              break;

            default: 
              debug_mail(false,'error settings my_cols',$my_cols);
              echo gks_lang('Σφάλμα ρυθμίσεων');
              die();
          }
          ?>
          
          
          <div class="form-group row gks_eidos_label">
            <div class="<?php echo $gkscols1;?>">
              <?php if ($GKS_ORDERS_SETS) {?>
                <div class="table-dark gks_eidos_label" style="text-align: left;"><span style="padding-left:10px;"><?php echo gks_lang('Σετ');?></span> <span style="padding-left:26px;"><?php echo gks_lang('Κωδικός');?></span></div>
              <?php } else { ?>
                <div class="table-dark gks_eidos_label"><?php echo gks_lang('Κωδικός');?></div>
              <?php } ?>
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Περιγραφή');?></div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Παρατηρήσεις');?></div>
            </div>
<?php if ($GKS_ORDERS_SHEETS) {?>            
            <div class="<?php echo $gkscols4;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Σελίδες');?></div>
            </div>
<?php } ?> 
            <div class="<?php echo $gkscols5;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Ποσότητα');?></div>
            </div>

<?php if ($perm_gks_orders_edit) {?>
<?php if ($GKS_ORDERS_COL_ITEMPRICE) {?>
            <div class="<?php echo $gkscols10;?>">
              <div class="table-dark gks_eidos_label"><?php if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) echo '<span class="tooltipster" title="'.gks_lang('Περιέχει ΦΠΑ η τιμή μονάδος').'">'.gks_lang('πΦΠΑ').'</span> ';?><?php echo gks_lang('Τιμή');?></div>
            </div>
<?php } ?> 

            <div class="<?php echo $gkscols6;?>">
              <div class="table-dark gks_eidos_label tooltipster" title="<?php echo gks_lang('Έκπτωση');?> %"><?php echo gks_lang('Έκπ');?>%</div>
            </div>
            <div class="<?php echo $gkscols7;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Σύνολο');?></div>
            </div>
<?php if ($GKS_ORDERS_COL_FPA) {?>            
            <div class="<?php echo $gkscols9;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('ΦΠΑ');?></div>
            </div>
<?php } ?> 
            <div class="<?php echo $gkscols8;?>">
              <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 
<?php } ?>               
          </div>           
<?php 

  



    $aa = 0;
    $eidi_sum_quantity=0;
    $eidi_sum_price_net=0;
    $fields_change=array();
    foreach ($eidos_array as $eidos) {

      $aa++;
      $eidi_sum_quantity+=$eidos['product_quantity'];
      $eidi_sum_price_net+=$eidos['product_price_final_all_net'];
  
      $ekptosi_poso_html='';
      $ekptosi_poso = $eidos['product_price_start_all_net']-$eidos['product_price_final_all_net'];
      if (abs($ekptosi_poso) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_html= myCurrencyFormat($ekptosi_poso,false);

      $ekptosi_poso_netfpa_html='';
      $ekptosi_poso_netfpa = $eidos['product_price_start_all_net']+$eidos['product_price_start_all_fpa']-$eidos['product_price_final_all_net']-$eidos['product_price_final_all_fpa'];
      if (abs($ekptosi_poso_netfpa) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_netfpa_html= myCurrencyFormat($ekptosi_poso_netfpa,false);

      $fields_change[$aa]='';
      if (abs($ekptosi_poso) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
        if ($eidos['product_price_coupon_use']=='') {
          $fields_change[$aa]='gks_price'; //gks_ekptosi
        } else {
          if ($eidos['product_price_coupon_use_disabled']!=0) {
            $fields_change[$aa]='gks_price'; //gks_ekptosi   
          } else {
            $fields_change[$aa]='';
          }
        }
      } else {
        if (1==2 and $row_room['product_price_coupon_use']=='') {
          
        } else {
          $fields_change[$aa]='gks_price';
        }
      } 
      
      
      $other_taxes=array();
      $other_taxes['withheldPercentCategory']=intval($eidos['product_withheldPercentCategory']);
      $other_taxes['withheldAmount']=floatval($eidos['product_withheldAmount']);
      $other_taxes['otherTaxesPercentCategory']=intval($eidos['product_otherTaxesPercentCategory']);
      $other_taxes['otherTaxesAmount']=floatval($eidos['product_otherTaxesAmount']);
      $other_taxes['stampDutyPercentCategory']=intval($eidos['product_stampDutyPercentCategory']);
      $other_taxes['stampDutyAmount']=floatval($eidos['product_stampDutyAmount']);
      $other_taxes['feesPercentCategory']=intval($eidos['product_feesPercentCategory']);
      $other_taxes['feesAmount']=floatval($eidos['product_feesAmount']);
      
      $other_taxes_tooltip='';
      if ($eidos['product_withheldPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Φόροι Παρακρατούμενοι').'</th><td nowrap style="text-align:left;">'.$eidos['aade_katigoria_parakratoumemenon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($eidos['product_withheldAmount']).'</td></tr>';
      }
      if ($eidos['product_otherTaxesPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Λοιποί Φόροι').'</th><td nowrap style="text-align:left;">'.$eidos['aade_katigoria_loipon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($eidos['product_otherTaxesAmount']).'</td></tr>';
      }
      if ($eidos['product_stampDutyPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').'</th><td nowrap style="text-align:left;">'.$eidos['aade_katigoria_xartosimou_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($eidos['product_stampDutyAmount']).'</td></tr>';
      }
      if ($eidos['product_feesPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Τέλη').'</th><td nowrap style="text-align:left;">'.$eidos['aade_katigoria_telon_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($eidos['product_feesAmount']).'</td></tr>';
      }
      
      if ($other_taxes_tooltip!='') {
        $other_taxes_tooltip=
        '<table class="table table-sm table-responsive table-striped table-bordered" style="font-size:0.8rem;width:100px;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">
        <tbody>'.
        $other_taxes_tooltip.
        '</tbody></table>';
      }
      
?>
          <div class="gks_eidos_2divs" data-aa="<?php echo $aa;?>" >

            <div class="form-group row gks_eidos <?php if (trim_gks($eidos['product_lot_serial'])!='') echo 'gks_eidos_radup';?>" data-recid="<?php echo ($template_id==0 ? $eidos['id_order_product'] : '0');?>" data-aa="<?php echo $aa;?>">
              <div class="<?php echo $gkscols1;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock gks_flock_small form-control-sm">';
                    if ($GKS_ORDERS_SETS and trim_gks($eidos['product_set'])!='') echo trim_gks($eidos['product_set']) .'\\';
                    echo $eidos['product_code'];
                  echo '</div>';
                } else {?>
  
                <?php if ($GKS_ORDERS_SETS) {?>
                <input type="text" class="form-control form-control-sm gks_set"   data-aa="<?php echo $aa;?>" value="<?php echo trim_gks($eidos['product_set']);?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Σετ');?>">
                <?php } ?>
                <input type="text" class="form-control form-control-sm gks_code" data-aa="<?php echo $aa;?>" 
                style="<?php if ($GKS_ORDERS_SETS==false) echo 'width:100%;';?>"
                value="<?php echo $eidos['product_code']?>" 
                data-varos="<?php echo $eidos['product_varos'];?>"
                data-ogos_x="<?php echo intval($eidos['product_ogos_x']);?>"
                data-ogos_y="<?php echo intval($eidos['product_ogos_y']);?>"
                data-ogos_z="<?php echo intval($eidos['product_ogos_z']);?>"
                data-need_apostoli="<?php echo intval($eidos['product_need_apostoli']);?>"
                <?php if (!$perm_gks_orders_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Κωδικός');?>">
                <?php } ?>
              </div>
              <div class="<?php echo $gkscols2;?>">
                <div class="text-left"><?php 
                $product_descr_small=trim_gks($eidos['product_descr_small_p']);  
                if ($product_descr_small!='') {
                  $product_descr_small="<table style='max-width:300px' border=0><tr><td>".str_replace('"',"'", $product_descr_small)."</td></tr></table>";
                }
                $myimgurl=trim_gks($eidos['product_photo_p'].'');
                if ($myimgurl == '') {
                  $myimgurl="/my/img/product.png";
                  echo '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png" style="display:none;"><img class="gks_img" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
                } else {
                  $mydir = dirname($myimgurl);
                  if (endwith($mydir,'/thumbnail')) {
                    $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
                  } else {
                    $photo_url=$myimgurl;
                  }
                  echo '<a class="lightgalleryitem_user gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
                }
                if ($perm_gks_orders_edit) echo '<i class="gks_product_zoom enterrow fas fa-pen" data-aa="'.$aa.'" data-id_product="'.$eidos['product_id'].'" title="'.gks_lang('Προβολή Είδους').'"></i>';
                echo '<i class="fas fa-info-circle gks_info_descr '.($product_descr_small!='' ? 'tooltipster' : '').'" data-aa="'.$aa.'" title="'.$product_descr_small.'" '.($product_descr_small=='' ? 'style="display:none;"' : '').'></i>';
                if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_descr">';
                    echo nl2br_gks(htmlspecialchars_gks($eidos['product_descr']));
                  echo '</div>';                
                } else {
                  echo '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="'.$aa.'"   placeholder="'.gks_lang('Περιγραφή').'">'.htmlspecialchars_gks($eidos['product_descr']).'</textarea>';
                }
                ?>
                
                </div>
              </div>
              <div class="<?php echo $gkscols3;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_comments">';
                    echo nl2br_gks(htmlspecialchars_gks($eidos['product_comments']));
                  echo '</div>';
                } else {?>
                <textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="<?php echo $aa;?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Σχόλιο');?>"><?php echo htmlspecialchars_gks($eidos['product_comments']);?></textarea>
                <?php } ?>
              </div>
  <?php if ($GKS_ORDERS_SHEETS) {?>            
              <div class="<?php echo $gkscols4;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_quantity_lock">';
                    if ($eidos['product_sheets']!=0) echo myNumberFormatNo0Local($eidos['product_sheets']);
                  echo '</div>';
                } else {?>
                <input type="number" class="form-control form-control-sm gks_sheets" data-aa="<?php echo $aa;?>" data-prev-value="<?php echo $eidos['product_sheets'];?>" value="<?php 
                if ($eidos['product_sheets']!=0) echo $eidos['product_sheets'];
                ?>" style="text-align:right;"  min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Σελίδες');?>">
                <?php } ?>
              </div>
  <?php } ?>            
  
              <div class="<?php echo $gkscols5;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_quantity_lock">';
                    echo myNumberFormatNo0Local($eidos['product_quantity']);
                  echo '</div>';
                } else {?>
                <input style="text-align:right;" type="number" class="form-control form-control-sm gks_quantity" data-aa="<?php echo $aa;?>" data-prev-value="<?php echo $eidos['product_quantity'];?>" value="<?php if ($eidos['product_quantity']!=0) echo $eidos['product_quantity'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" <?php if (!$perm_gks_orders_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Ποσότητα');?>">
                <?php } ?>
                <span class="gks_monada_span<?php echo ($gks_lock ? '_lock' :'');?>" data-mon-id="<?php echo $eidos['product_monada_id'];?>" data-aa="<?php echo $aa;?>"><?php echo $eidos['monada_symbol'];?></span>
              </div>
  <?php if ($perm_gks_orders_edit) {?>
  <?php if ($GKS_ORDERS_COL_ITEMPRICE) {?>
              <div class="<?php echo $gkscols10;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_peritem_net_lock '.($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA ? 'gks_peritem_net_lock_s': '').'">';
                    if ($eidos['product_price_final_peritem_net']!=0) {
                      
                      if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) {
                        if ($eidos['product_price_check_fpa']==1) {
                          echo '<i class="far fa-check-square"></i> '.myCurrencyFormat($eidos['product_price_final_peritem_net']+$eidos['product_price_final_peritem_fpa'],false);
                        } else {
                          echo '<i class="far fa-square"></i> '.myCurrencyFormat($eidos['product_price_final_peritem_net'],false);
                        }
                      } else {
                        echo myCurrencyFormat($eidos['product_price_final_peritem_net'],false);
                      }
                      
                    }
                  echo '</div>';
                } else {
                  $final_peritem=0;
                  if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) {
                    echo '<input type="checkbox" class="gks_peritem_check_fpa" data-aa="'.$aa.'" '.($eidos['product_price_check_fpa']==1 ? 'checked' : '').'>';
                    if ($eidos['product_price_check_fpa']==1) {
                      $final_peritem=$eidos['product_price_final_peritem_net']+$eidos['product_price_final_peritem_fpa'];
                    } else {
                      $final_peritem=$eidos['product_price_final_peritem_net'];
                    }
                  } else {
                    $final_peritem=$eidos['product_price_final_peritem_net'];
                  }
                
                  
                  
                ?>              
                
                <input type="number" class="form-control form-control-sm gks_peritem_net <?php if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) echo 'gks_peritem_net_s';?>" 
                data-aa="<?php echo $aa;?>" 
                value="<?php 
                $valnotzero='';
                if ($final_peritem!=0) {
                  $valnotzero=myNumberFormatNo0($final_peritem);
                  echo $valnotzero;
                };?>" 
                style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                data-product_price_final_peritem_net="<?php echo number_format($eidos['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_peritem_fpa="<?php echo number_format($eidos['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                
                placeholder="<?php echo gks_lang('Τιμή');?>"
                >
                <?php } ?>
              </div>
              
  <?php } ?>
              <div class="<?php echo $gkscols6;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_ekptosi_pososto_lock">';
                    if ($eidos['product_price_ekptosi_pososto']!=0) echo myNumberFormatNo0Local($eidos['product_price_ekptosi_pososto']);
                  echo '</div>';
                } else {?>
                <input type="number" class="form-control form-control-sm gks_ekptosi_pososto" data-aa="<?php echo $aa;?>" 
                value="<?php 
                $valnotzero='';
                if ($eidos['product_price_ekptosi_pososto']!=0) {
                  $valnotzero=myNumberFormatNo0($eidos['product_price_ekptosi_pososto']);
                  echo $valnotzero;
                };?>" 
                style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"
                placeholder="<?php echo gks_lang('Έκπτωση');?>"
                ><?php }
                $product_price_coupon_use=$eidos['product_price_coupon_use'];
                ?>
                <div class="gks_coupon" data-aa="<?php echo $aa;?>"><div 
                  class="gks_coupon_item <?php if ($eidos['product_price_coupon_use_disabled']!=0) echo 'gks_coupon_item_disabled'.($gks_lock ? '_lock' :'');?>" data-aa="<?php echo $aa;?>" style="<?php echo ($product_price_coupon_use=='' ? 'display:none;' : '');?>"   ><?php echo $product_price_coupon_use;?></div></div>              
              </div>
              <div class="<?php echo $gkscols7;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_price_lock '.($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA ? 'gks_price_lock_s': '').'">';
                    if ($eidos['product_price_final_all_net']!=0) {
                      if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) {
                        if ($eidos['product_price_check_fpa']==1) {
                          echo myCurrencyFormat($eidos['product_price_final_all_net']+ $eidos['product_price_final_all_fpa'],false);
                        } else {
                          echo myCurrencyFormat($eidos['product_price_final_all_net'],false);
                        }
                      } else {
                        echo myCurrencyFormat($eidos['product_price_final_all_net'],false);
                      }
                      
                    }
                  echo '</div>';
                } else {
                  $final_all_net=0;
                  if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) {
                    if ($eidos['product_price_check_fpa']==1) {
                      $final_all_net=$eidos['product_price_final_all_net']+$eidos['product_price_final_all_fpa'];
                    } else {
                      $final_all_net=$eidos['product_price_final_all_net'];
                    }
                  } else {
                    $final_all_net=$eidos['product_price_final_all_net'];
                  }               
                ?>
                <input type="number" class="form-control form-control-sm gks_price" data-aa="<?php echo $aa;?>" 
                value="<?php if ($final_all_net!=0) echo number_format($final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                data-product_price_start_all_net="<?php echo number_format($eidos['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_all_net="<?php echo number_format($eidos['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_all_fpa="<?php echo number_format($eidos['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_fpa_id="<?php echo $eidos['product_fpa_id'];?>" 
                data-product_fpa_pososto="<?php echo number_format($eidos['product_fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-fpa_descr_print="<?php echo $eidos['fpa_descr_print'];?>"
                data-other_taxes="<?php echo base64_encode(json_encode($other_taxes));?>"
                placeholder="<?php echo gks_lang('Σύνολο');?>"
                >
                <?php } ?>
                <div class="gks_ekptosi<?php echo ($gks_lock ? '_lock gks_flock form-control-sm' :'');?>" data-aa="<?php echo $aa;?>"><div 
                  class="gks_ekptosi_poso" data-aa="<?php echo $aa;?>" 
                  style="<?php echo ($ekptosi_poso_html=='' ? 'display:none;' : '');?>"   
                  data-net-poso="<?php echo $ekptosi_poso_html;?>"
                  data-netfpa-poso="<?php echo $ekptosi_poso_netfpa_html;?>"
                  ><?php 
                  if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) {
                    if ($eidos['product_price_check_fpa']==1) {
                      echo $ekptosi_poso_netfpa_html;
                    } else {
                      echo $ekptosi_poso_html;
                    }
                  } else {
                    echo $ekptosi_poso_html;
                  }
                  
                  ?></div></div>
              </div>
  <?php if ($GKS_ORDERS_COL_FPA) {?>            
              <div class="<?php echo $gkscols9;?>" >
                <i class="fas fa-info-circle gks_info_other_taxes" data-aa="<?php echo $aa;?>" data-title="<?php echo base64_encode($other_taxes_tooltip);?>" <?php echo ($other_taxes_tooltip=='' ? 'style="display:none;"' : '');?>></i>
                
                <div class="gks_fpa_div<?php echo ($gks_lock ? '_lock' :'');?> btn btn-primary btn-sm" 
                  data-aa="<?php echo $aa;?>" 
                  data-fpa_base_id="<?php echo $eidos['product_fpa_base_id'];?>"
                  data-fpa_aade_id="<?php echo $eidos['product_fpa_aade_id'];?>"
                  >
                  <?php
                echo $eidos['fpa_descr_print'].
                       ' '.
                       number_format($eidos['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);
                ?></div>              
              </div>
  <?php } ?>            
              
              
              <div class="<?php echo $gkscols8;?>">
                <div class="text-center gks_icons">
                  
                  <?php 
                  $product_is_optional_text='';
                  if ($eidos['product_is_optional']=='0') $product_is_optional_text=gks_lang('Δεν μπορεί να αφαιρεθεί από την προσφορά');
                  else if ($eidos['product_is_optional']=='1') $product_is_optional_text=gks_lang('Μπορεί ο πελάτης να το προσθέσει στην προσφορά');
                  else if ($eidos['product_is_optional']=='2') $product_is_optional_text=gks_lang('Ο πελάτης το έχει προσθέσει στην προσφορά');
                  ?>
                  <div class="gks_product_is_optional_eidos_div" style="<?php if ($row['online_enable']==0) echo 'display:none;';?>">
                    <i class="fas fa-circle gks_product_is_optional_eidos" data-val="<?php echo $eidos['product_is_optional'];?>" data-aa="<?php echo $aa;?>" title="<?php echo $product_is_optional_text;?>"></i>
                  </div>
                  <?php if ($gks_lock==false) {?>
                  <div>
                    <i class="fas fa-trash-alt gks_delete_eidos" data-aa="<?php echo $aa;?>"></i>
                  </div>
                  <div>
                    <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                  </div>
                  <div>
                    <i class="fas fa-plus-circle gks_add_eidos"  data-aa="<?php echo $aa;?>"></i>
                  </div>
                  <?php } ?>
                </div>
              </div>   
  <?php } ?>             
            </div> 



            <?php if ($GKS_PRODUCT_LOTS_SERIALS) { ?>
            <div class="form-group row gks_eidos_lots_serials gks_eidos_raddown gks_eidos_lots_serials_col1" data-aa="<?php echo $aa;?>" style="padding-top: 4px;<?php if (trim_gks($eidos['product_lot_serial'])=='') echo 'display:none;';?>" data-val-lot-serial="<?php echo trim_gks($eidos['product_lot_serial']);?>">

              <div class="col-12 col-sm-12  col-md-11 col-lg-11 col-xl-11 offset-md-1 offset-lg-1 offset-xl-1 gks_eidos_lots_serials_list" data-aa="<?php echo $aa;?>">
                
                <div class="div_eidos_lots_serials" data-aa="<?php echo $aa;?>" style="<?php echo (1==2 ? 'display:none;' : '');?>"> 
                  <?php
                  
                  $eidos_lots_serials=array();
                  if (isset($products_lots_serials[$eidos['id_order_product']])) {
                    $eidos_lots_serials=$products_lots_serials[$eidos['id_order_product']];
                  }
                  ?>
                  <div class="form-group1 row div_add_eidos_lots_serials" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-8 col-sm-11 col-md-11 col-lg-11 gks_items_col text-center gks_eidos_lots_serials_label div_eidos_lots_serials_title">
                      <?php echo gks_lang('Λίστα');?> 
                      <span class="gks_eidos_lots_serials_span"><?php
                      if (trim_gks($eidos['product_lot_serial'])=='lot') echo gks_lang('Παρτίδων');
                      else if (trim_gks($eidos['product_lot_serial'])=='serial') echo gks_lang('Serial Numbers');
                    ?></span> 
                    </div>
                    <div class="col-4 col-sm-1 col-md-1 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_orders_edit) {?>
                      <i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="<?php echo $aa;?>" style="<?php echo (count($eidos_lots_serials) > 0 ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
                  <?php
                  
                  $ls=0;
                  $span_eidos_lots_serials_sum_quantity=0;
                  foreach ($eidos_lots_serials as $lot_serial) { 
                    $span_eidos_lots_serials_sum_quantity+=floatval($lot_serial['lot_product_quantity']);
                  }
                    
                  foreach ($eidos_lots_serials as $lot_serial) { 
                    $ls++;
                    ?>
                    
                  <div class="form-group1 row div_gks_eidos_lots_serials" style="margin: 0px;" data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>">
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          echo $lot_serial['lot_name'];
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_name" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      data-product-id="<?php echo $eidos['product_id'];?>"
                      value="<?php echo $lot_serial['lot_name'];?>"
                      <?php if (!$perm_gks_orders_edit) echo 'readonly';?> 
                      placeholder="<?php echo gks_lang('Παρτίδα/Serial Number');?>"
                      ><a href="admin-products-lots-item.php?id=<?php echo $lot_serial['lot_product_id'];?>" class="gks_eidos_lots_serials_zoom"
                        data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>"
                        ><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή Παρτίδας/Serial Number');?>"></i></a>
                      <?php } ?>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right">';
                          echo myNumberFormatNo0Local($lot_serial['lot_product_quantity'],false);
                        echo '</div>';
                      } else {?>
                      <input style="text-align:right;" type="number" class="form-control form-control-sm gks_eidos_lots_serials_quantity" 
                      data-aa="<?php echo $aa;?>"
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if ($lot_serial['lot_product_quantity']!=0) echo $lot_serial['lot_product_quantity'];?>" 
                      min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" 
                      <?php if (!$perm_gks_orders_edit or trim_gks($eidos['product_lot_serial'])=='serial') echo 'readonly';?> 
                      placeholder="<?php echo gks_lang('Ποσότητα');?>"
                      ><i class="fas fa-boxes gks_eidos_lots_serials_balance"></i>
                      <?php } ?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          echo $lot_serial['lot_descr'];
                        echo '</div>';
                      } else {?>
                      <textarea class="form-control form-control-sm gks_eidos_lots_serials_descr" rows="1"
                        data-aa="<?php echo $aa;?>" 
                        data-ls="<?php echo $ls;?>"
                        readonly
                        placeholder="<?php echo gks_lang('Περιγραφή');?>"
                        ><?php echo htmlspecialchars_gks($lot_serial['lot_descr']);?></textarea>
                      
                      <?php } ?>
                    </div>

                    <div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          if (isset($lot_serial['lot_date_production'])) echo showDate(strtotime($lot_serial['lot_date_production']), 'd/m/Y', 1);
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_production" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if (isset($lot_serial['lot_date_production'])) echo showDate(strtotime($lot_serial['lot_date_production']), 'd/m/Y', 1);?>"
                      readonly
                      placeholder="<?php echo gks_lang('Ημερ. Παραγωγής');?>">
                      <?php } ?>
                    </div>
                    <div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          if (isset($lot_serial['lot_date_expire'])) echo showDate(strtotime($lot_serial['lot_date_expire']), 'd/m/Y', 1);
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_expire" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if (isset($lot_serial['lot_date_expire'])) echo showDate(strtotime($lot_serial['lot_date_expire']), 'd/m/Y', 1);?>"
                      readonly
                      placeholder="<?php echo gks_lang('Ημερ. Λήξης');?>">
                      <?php } ?>
                    </div>
                                        
                    <div class="col-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 gks_items_col text-center offset-sm-4 offset-md-4 offset-lg-0 ">
                      <?php if ($gks_lock==false and $perm_gks_orders_edit) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_lots_serials" data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="<?php echo $aa;?>" style="<?php echo ($ls < count($eidos_lots_serials) ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>                      
                    
    <?php         }    ?>                
                  <div class="form-group1 row div_eidos_lots_serials_sum_quantity" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php echo (count($eidos_lots_serials) == 0 ? 'display:none;' : '');?>">
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-left gks_eidos_lots_serials_label div_eidos_lots_serials_title" style="margin: 0px;">
                      <div class="gks_flock gks_flock_small form-control-sm">
                        <?php echo gks_lang('Άθροισμα');?>:
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-right gks_eidos_lots_serials_label">
                      <div class="gks_flock gks_flock_small form-control-sm">
                        <img src="img/warning.gif" class="img_eidos_lots_serials_sum_quantity" data-aa="<?php echo $aa;?>" style="<?php if (floatval($eidos['product_quantity'])==$span_eidos_lots_serials_sum_quantity) echo 'display:none';?>"/>
                        <span class="span_eidos_lots_serials_sum_quantity <?php
                          if ($gks_lock==false) echo ' span_eidos_lots_serials_sum_quantity_lock ';
                          ?>" data-aa="<?php echo $aa;?>"><?php echo myNumberFormatNo0Local($span_eidos_lots_serials_sum_quantity,false);?></span>
                      </div>
                    </div>
                  </div>
                  
                </div>
                
              </div>
            </div>

            <?php } ?>




          </div>
<?php } ?>


          <div class="row" id="eidi_footer1">
            <div class="col-sm-4">
              <div class="form-group row total_row">
                <div class="col-12 table-dark1 gks_eidos_label text-left">
                  <button type="button" class="btn btn-sm btn-primary" id="dialog_add_product_variables_button"><?php echo gks_lang('Προσθήκη παραλλαγών είδους');?></button>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Γραμμές');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_count"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo $products_count;?></div>
                </div>
              </div> 
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Ποσότητα');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_posotita"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo $products_posotita;?></div>
                </div>
              </div>              
               
              <?php if ($GKS_ORDERS_SETS) { ?>            
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Σετς');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_sets"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo count($products_sets);?></div>
                </div>
              </div>              
              <?php } ?>              
              

              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Όγκος');?> (cm<sup>3</sup>):
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_ogos"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php
                    if ($products_ogos_max_x>0 or $products_ogos_max_y>0 or $products_ogos_max_z>0) {
                      echo number_format($products_ogos_max_x, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                           number_format($products_ogos_max_y, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                           number_format($products_ogos_max_z, 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
                    }
                    ?></div>
                </div>
              </div>
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Βάρος');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_varos" style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php if ($products_varos>0 or 1==1) echo number_format($products_varos, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'gr';?></div>
                </div>
              </div>              
            </div>
            <?php if ($perm_gks_orders_edit) {?>
            <div class="col-sm-4">
              <div class="form-group row total_row" id="tr_gks_total_price_net">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Υποσύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_total_price_net"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_net']);?></div>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_gks_total_price_fpa" style="<?php if ($row['gks_price_fpa']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('ΦΠΑ');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_total_price_fpa"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_fpa']);?></div>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_gks_total_price_netfpa" style="<?php if ($row['gks_price_netfpa']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Μικτό σύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_total_price_netfpa"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></div>
                </div>
              </div>
                            
              <div class="form-group row total_row" id="tr_totalWithheldAmount" style="<?php if ($row['totalWithheldAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Φόροι Παρακρατούμενοι');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="totalWithheldAmount"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalWithheldAmount']);?></div>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalOtherTaxesAmount" style="<?php if ($row['totalOtherTaxesAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Λοιποί Φόροι');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="totalOtherTaxesAmount"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalOtherTaxesAmount']);?></div>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalStampDutyamount" style="<?php if ($row['totalStampDutyamount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="totalStampDutyamount"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalStampDutyamount']);?></div>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalFeesAmount" style="<?php if ($row['totalFeesAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Τέλη');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="totalFeesAmount"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalFeesAmount']);?></div>
                </div>
              </div>
              
              <div class="form-group row total_row" id="tr_gks_total_price_total">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Σύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_total_price_total"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_total']);?></div>
                </div>
              </div>              
              <div class="form-group row total_row" id="tr_kostos_apostolis">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κόστος αποστολής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <input type="number" id="kostos_apostolis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_apostolis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                </div>
              </div>
              <div class="form-group row total_row" id="tr_kostos_pliromis">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κόστος πληρωμής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <input type="number" id="kostos_pliromis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_pliromis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                </div>
              </div>
              <div class="form-group row total_row" id="tr_pliroteo">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Πληρωτέο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_pliroteo"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($pliroteo);?></div>
                </div>
              </div>
              
            </div>
            <?php } ?>
          </div>
        
        
                    
            


                              
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">

      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποστολή - Πληρωμή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('delivery');?>> 

          <div class="form-group row">
            <label for="ddate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιθυμητή Παράδοση');?>:</label>
            <div class="col-md-8">
              <input id="ddate" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['ddate'])) echo  showDate(strtotime($row['ddate']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div> 
          <div class="row">
            <div class="col-lg-12 col-xl-6" style="margin-bottom:24px;">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι αποστολής');?>:</div>
              <?php

                $div_delivery_number_style ='display:none';
                foreach ($mybasketarray['tropoi_apostolis_all'] as $row_apostoli) {
                  //echo '<pre>';print $row['tropos_apostolis']; print_r($row_apostoli);die();
                  if ($row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) {
                    if ($row_apostoli['delivery_method_type']=='delivery' or 
                        $row_apostoli['delivery_method_type']=='pelatis' or 
                        $row_apostoli['delivery_method_type']=='post') $div_delivery_number_style='';
                  }
              ?>
              <div style="white-space: nowrap1;<?php echo (($row_apostoli['myisok'] or $row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) ? '' : 'display:none;');?>">
                <input class="myneedsave" type="radio" name="radio_delivery_way" value="<?php echo $row_apostoli['id_delivery_method'];?>" id="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>"  
                data-type="<?php echo $row_apostoli['delivery_method_type'];?>" data-type-o="<?php echo $row_apostoli['delivery_method_type_pa'];?>"
                data-sxolio="<?php echo base64_encode($row_apostoli['delivery_method_sxolio']);?>"
                <?php if ($row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) echo ' checked ';?>
                <?php if (!$perm_gks_orders_edit) echo 'disabled';?>> 
                <label for="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_apostoli['delivery_method_tooltip'];?>"><?php echo $row_apostoli['delivery_method_name'];?>
                  <?php if ($row_apostoli['delivery_method_fees_enabled']!=0) {?>
                    <span class="delivery_payment_price" id="price_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" ><?php echo myCurrencyFormat($row_apostoli['dm_calc_kostos'],true,true);?></span>
                  <?php } ?>
                </label>
                <?php
                if ($row_apostoli['id_delivery_method'] == 8) { ?>
                  <span id="span_delivery_id_8" style="<?php echo ($row['tropos_apostolis']==8 ? '' : 'display:none;');?>">
                  <br>
                  <select id="delivery_id_8" name="delivery_id_8" style="width11:90%;" class="form-control form-control-sm myneedsave" <?php if (!$perm_gks_orders_edit) echo 'disabled';?>>
                      <option value="0"><?php echo gks_lang('Επιλέξτε κατάστημα');?></option>
                      
                      <?php
                      $sql="SELECT id_warehouse,warehouse_name FROM gks_warehouses
                        WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0
                        ORDER BY gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name";
                      $result_select = $db_link->query($sql);        
                      if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error2');}
                      while ($row_select = $result_select->fetch_assoc()) {
                        echo '<option value="'.$row_select['id_warehouse'].'" ';
                        if ($row_select['id_warehouse']==$row['delivery_id_8']) echo ' selected ';
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

                
                foreach ($mybasketarray['tropoi_pliromis_all'] as $row_pliromi) {
              ?>
              <div style="white-space: nowrap1;<?php echo (($row_pliromi['myisok'] or $row['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) ? '' : 'display:none;');?>">
                <input class="myneedsave" type="radio" name="radio_payment_way" value="<?php echo $row_pliromi['id_payment_acquirer'];?>" id="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" 
                data-type="<?php echo $row_pliromi['payment_acquirer_type'];?>" data-type-o="<?php echo $row_pliromi['payment_acquirer_type_dm'];?>" 
                data-sxolio="<?php echo base64_encode($row_pliromi['payment_acquirer_sxolio']);?>"
                data-button-html="<?php echo base64_encode($row_pliromi['payment_acquirer_button_html']);?>"
                
                <?php if ($row['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) echo ' checked ';?>
                <?php if (!$perm_gks_orders_edit) echo 'disabled';?>> 
                <label for="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_pliromi['payment_acquirer_tooltip'];?>"><?php echo $row_pliromi['payment_acquirer_name'];?>
                  <?php if ($row_pliromi['payment_acquirer_fees_enabled']!=0 and $row_pliromi['payment_acquirer_type']!='none') {?>
                    <span class="delivery_payment_price" id="price_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" ><?php echo myCurrencyFormat($row_pliromi['pa_calc_kostos'],true,true);?></span>
                  <?php } ?>
                </label>
              </div>
              <?php } ?>                              
                
                
                                
              <div id="payment_acquirer_sxolio" class="form-text text-muted" style="font-size:80%"></div>
              <div class="" style="display:none"><span id="button_html"><?php echo gks_lang('Πληρωμή τώρα');?></span></div>
            </div>
            
          </div>
          
          <div class="form-group row" id="div_bank_deposit_9digit" style="<?php if ($row['tropos_pliromis']!=2) echo 'display:none;';?>">
            <label for="bank_deposit_9digit" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτιολογία κατάθεσης');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm" id="bank_deposit_9digit"><?php echo gks_format_bank_deposit_9digit($row['bank_deposit_9digit'])?></div>
            </div>
          </div>
                    
          <div class="form-group row" id="div_delivery_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="delivery_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Αποστολής');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="delivery_number" type="text" value="<?php echo htmlspecialchars_gks($row['delivery_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div>                   
          <div class="form-group row" id="div_vehicle_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="vehicle_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Μεταφορικού Μέσου');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="vehicle_number" type="text" value="<?php echo htmlspecialchars_gks($row['vehicle_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div>
         
          <div class="form-group row" id="div_dispatch_date" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημέρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_date'])) echo  date('d/m/Y',strtotime($row['dispatch_date']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div> 
          <div class="form-group row" id="div_dispatch_time" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_time" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ώρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_time" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_time'])) echo  date('H:i',strtotime($row['dispatch_time']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div>          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σημειώσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('notes');?>> 
          <div class="form-group row">
            <label for="mdate_expire" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία λήξης προσφοράς');?>:</label>
            <div class="col-md-8">
              <input id="mdate_expire" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['mdate_expire'])) echo showDate(strtotime($row['mdate_expire']),'d/m/Y H:i',1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>>
            </div>
          </div>
          <div class="form-group row">
            <label for="note_doc" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλια παραγγελίας');?>:</label>
            <div class="col-md-8">
              <textarea id="note_doc" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_doc']);?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="note_logistirio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για λογιστήριο');?>:</label>
            <div class="col-md-8">
              <textarea id="note_logistirio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_logistirio']);?></textarea>
            </div>
          </div> 
          <div class="form-group row">
            <label for="order_priority" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προτεραιότητα');?>:</label>
            <div class="col-md-8" style="padding: 0.25rem 0.5rem;">
              <div id="order_priority" data-rateyo-rating="<?php if (isset($row['order_priority'])) echo $row['order_priority'];?>"></div>
            </div>
          </div>
          
<?php if ($GKS_ORDERS_PRODUCTION) {?>                  
          <div class="form-group row">
            <label for="note_production" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για παραγωγή');?>:</label>
            <div class="col-md-8">
              <textarea id="note_production" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_orders_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_production']);?></textarea>
            </div>
          </div> 
<?php } ?>          
        </div>
      </div>

<?php if ($GKS_CRM_ENABLE) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          CRM
        </div>
        <div class="card-body" <?php echo gks_card_body('crm_channel');?>> 
          <div class="form-group row">
            <label for="crm_channel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κανάλι πωλήσεων');?>:</label>
            <div class="col-md-8">
              <select id="crm_channel_id" class="form-control form-control-sm myneedsave" >
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
                  if ($row_channel_sale['id_crm_channel_sale']==$row['crm_channel_id']) {
                    echo ' selected ';
                    $row_channel_sale_selected=$row_channel_sale;
                  }
                  echo '>'.$row_channel_sale['crm_channel_sale_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>



          <div class="form-group row" id="crm_channel_contact_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_contact']==0) echo 'display:none;';?>">
            <label for="crm_channel_contact_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή Πωλήσεων');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_contact_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['crm_channel_contact_gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_contact_id'];?>">
            </div>
          </div>


          <div class="form-group row" id="crm_channel_campain_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_campain']==0) echo 'display:none;';?>">
            <label for="crm_channel_campain_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καμπάνια');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_campain_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['ads_campain_name']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_campain_id'];?>">
            </div>
          </div>

          <div class="form-group row" id="crm_channel_url_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_url']==0) echo 'display:none;';?>">
            <label for="crm_channel_url" class="col-md-4 col-form-label form-control-sm text-md-right">URL:</label>
            <div class="col-md-8">
              <input id="crm_channel_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_url']);?>">
            </div>
          </div>
          <div class="form-group row" id="crm_channel_code_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_code']==0) echo 'display:none;';?>">
            <label for="crm_channel_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_code']);?>">
            </div>
          </div>
          
          <div class="form-group row" id="crm_channel_text_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_text']==0) echo 'display:none;';?>">
            <label for="crm_channel_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="crm_channel_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['crm_channel_text']);?></textarea>
            </div>
          </div>   


        </div>
      </div>

<?php } ?>
            
      
      
    </div>
                      
    <div class="col-md-6">

      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπόλοιπο Επαφής');?>         
        </div>

        <div id="myypoloipoepafis_card" class="<?php
          if ($eidos_parastatikou_balance_pros==0) echo 'myypoloipoepafis_card_notactive';
          ?> card-body" <?php echo gks_card_body('aff_bal');?>>  
          <div id="myypoloipoepafis_not_text" class="form-group row" >
            <div class="col-md-12">
              <div class="alert alert-warning" role="alert">
                <?php echo gks_lang('Αυτό το ημερολόγιο δεν επιτρέπει να επηρεαστεί το υπόλοιπο της επαφής');?>
              </div>
            </div>
          </div>
          <div id="myypoloipoepafis_controls">
            <div class="form-group row">
              <label for="balance_user_before" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προηγούμενο υπόλοιπο');?>:</label>
              <div class="col-md-8">
                <div class="form-control-sm" id="balance_user_before" 
                  <?php
                  $balance_user_before=gks_balance_calc(['id' => $row['user_id'], 'except_id_order' => $id]);
                  echo ' data-val="'.number_format($balance_user_before,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').'">'.myCurrencyFormat($balance_user_before);
                  ?>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="balance_user_after" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νέο υπόλοιπο');?>:</label>
              <div class="col-md-8">
                <div class="form-control-sm" id="balance_user_after" >
                  <?php
                  $balance_user_after=gks_balance_calc(['id' => $row['user_id']]);
                  echo myCurrencyFormat($balance_user_after);
                  ?>
                </div>
              </div>
            </div>
            
            <div class="form-group row" id="div_affect_balance">
              <label for="affect_balance" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επηρεάζει το υπόλοιπο της επαφής');?>:</label>
              <div class="col-md-8">
                <input id="affect_balance" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance']!=0) echo ' checked ';?> <?php if (!$perm_gks_orders_edit) echo 'disabled';?>>
                <?php if (!($row['order_state']=='060registered' or $row['order_state']=='070inproduction' or $row['order_state']=='090indelivery' or 
                $row['order_state']=='095execute' or $row['order_state']=='100completed' or $row['order_state']=='110payment')) {?>
                <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί η ρύθμιση όταν η κατάσταση της παραγγελίας θα είναι μία από τις παρακάτω');?>:<br>
                  <span style="line-height: 1.8;">
                  <span class="order_state_060registered"><?php echo getOrderStateDescr('060registered');?></span>
                  <span class="order_state_070inproduction"><?php echo getOrderStateDescr('070inproduction');?></span>
                  <span class="order_state_090indelivery"><?php echo getOrderStateDescr('090indelivery');?></span>
                  <span class="order_state_095execute"><?php echo getOrderStateDescr('095execute');?></span>
                  <span class="order_state_100completed"><?php echo getOrderStateDescr('100completed');?></span>
                  <span class="order_state_110payment"><?php echo getOrderStateDescr('110payment');?></span>
                  </span>
                </small>
                <?php } ?>
              </div>
            </div> 
            <div class="form-group row" id="div_affect_balance_all_poso" style="<?php if ($row['affect_balance']==0) echo 'display:none;';?>">
              <label for="affect_balance_all_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολόκληρο το ποσό');?>:</label>
              <div class="col-md-8">
                <input id="affect_balance_all_poso" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance_all_poso']!=0) echo ' checked ';?> <?php if (!$perm_gks_orders_edit) echo 'disabled';?>>
                <small class="form-text text-muted" id="small_affect_balance_all_poso" style="<?php if (!($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0)) echo 'display:none;';?>">
                  <input type="radio" name="affect_balance_all_poso_type" value="price_net" id="affect_balance_all_poso_type_price_net" <?php
                  if ($row['affect_balance_all_poso_type']=='price_net') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_net"    style="margin-bottom: 0px;"><?php echo gks_lang('Υποσύνολο');?> (<span
                      id="bal_gks_total_price_net" data-val="<?php echo number_format($eidi_sum_price_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($eidi_sum_price_net);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="price_netfpa" id="affect_balance_all_poso_type_price_netfpa" <?php
                  if ($row['affect_balance_all_poso_type']=='price_netfpa') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_netfpa" style="margin-bottom: 0px;"><?php echo gks_lang('Μικτό σύνολο');?> (<span
                      id="bal_gks_total_price_netfpa" data-val="<?php echo number_format($row['gks_price_netfpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="price_total" id="affect_balance_all_poso_type_price_total" <?php
                  if ($row['affect_balance_all_poso_type']=='price_total') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_total" style="margin-bottom: 0px;"><?php echo gks_lang('Σύνολο');?> (<span
                      id="bal_gks_total_price_total" data-val="<?php echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($row['gks_price_total']);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="pliroteo" id="affect_balance_all_poso_type_pliroteo" <?php
                  if ($row['affect_balance_all_poso_type']=='pliroteo') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_pliroteo" style="margin-bottom: 0px;"><?php echo gks_lang('Πληρωτέο');?> (<span
                      id="bal_gks_pliroteo" data-val="<?php echo number_format($pliroteo,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($pliroteo);?></span>)</label>
  
                </small>
              </div>
            </div> 
            <div class="form-group row" id="div_affect_balance_poso"  style="<?php if ($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0) echo 'display:none;';?>">
              <label for="affect_balance_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ποσό');?>:</label>
              <div class="col-md-8">
                
                <input id="affect_balance_poso" type="number" class="form-control form-control-sm myneedsave" 
                value="<?php 
                $valnotzero='';
                if ($row['affect_balance_poso']!=0) {
                  $valnotzero=myNumberFormatNo0($row['affect_balance_poso']);
                  echo $valnotzero;
                };?>" 
                style="text-align:right;max-width: 100px;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                placeholder="<?php echo gks_lang('Ποσό');?>"
                <?php if (!$perm_gks_orders_edit) echo 'disabled';?> >            
              </div>
            </div> 
          </div>
                    
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('OnLine Προσφορά');?>         
        </div>
        <div class="card-body" <?php echo gks_card_body('online');?>>  
          
          <div class="form-group row">
            <label for="online_enable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργοποίηση');?>:</label>
            <div class="col-md-8">
              <input id="online_enable" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['online_enable']!=0) echo ' checked ';?> <?php if (!$perm_gks_orders_edit) echo 'disabled';?>>
              <small class="form-text text-muted online_enable_show" id="online_enable_info" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
                <?php echo gks_lang('Όταν η παραγγελία είναι σε μία από τις παρακάτω καταστάσεις τότε θα μπορεί να προβληθεί');?>:<br>
                <span style="line-height: 1.8;">
                  <span class="order_state_025offer"><?php echo getOrderStateDescr('025offer');?></span>
                  <span class="order_state_040cancelled"><?php echo getOrderStateDescr('040cancelled');?></span>
                  <span class="order_state_050rejected"><?php echo getOrderStateDescr('050rejected');?></span>
                  <span class="order_state_055wait_payment"><?php echo getOrderStateDescr('055wait_payment');?></span>
                  <span class="order_state_060registered"><?php echo getOrderStateDescr('060registered');?></span>
                  
                  <br>
                  <?php echo gks_lang('Ο πελάτης μπορεί να κάνει Αποδοχή/Απόρριψη όταν η παραγγελία είναι σε κατάσταση');?>:<br>
                  <span style="line-height: 1.8;">
                  <span class="order_state_025offer"><?php echo getOrderStateDescr('025offer');?></span>
                </span>
              </small>
            </div>
          </div>
          <div class="form-group row online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
            <label for="online_password" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="online_password" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['online_password']);?>" style="max-width:200px" placeholder="<?php echo gks_lang('π.χ.').' '.rand(11111,99999);?>">
            </div>
          </div>
          <div class="form-group row online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
            <label for="online_template_html_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρότυπο HTML');?>:</label>
            <div class="col-md-8">
              <select id="online_template_html_id" class="form-control form-control-sm myneedsave" >
                <option value="0"></option>
                <?php
                $sql="SELECT id_template_html,template_html_descr,orders_online_url
                FROM gks_template_html 
                WHERE template_html_type=1 and is_disable=0
                order by sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                $orders_online_url='';
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_template_html'].'" '.
                  'data-orders_online_url="'.base64_encode($row_select['orders_online_url']).'"';
                  
                  if ($row['online_template_html_id'] == $row_select['id_template_html']) {
                    echo ' selected ';
                    $orders_online_url=trim_gks($row_select['orders_online_url']);
                  }
                  echo '>'.$row_select['template_html_descr'].'</option>';
                }
                ?>                
              </select>             
          
            </div>
          </div>          
          


          <div class="form-group row online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('URL');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm" id="gks_orders_online_url_div" style="<?php
                if ($orders_online_url=='' or $id<=0) echo 'display:none;';
                ?>">
              <?php
              $online_url=$orders_online_url.'?guid='.$row['order_guid'];
              echo '<a id="online_url" href="'.$online_url.'" target="_blank">'.$online_url.'</a> <i class="fas fa-copy tooltipster" title="'.gks_lang('Αντιγραφή στο πρόχειρο').'" id="online_url_copy"></i>';
              ?>
              </div>
            </div>
          </div>          
          <div class="form-group row online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
            <div class="col-md-12" style="text-align:center;">
              <button type="button" class="btn btn-sm btn-primary" id="go_gks_order_online_message"><?php echo gks_lang('Αποστολή μηνύματος στο Online Προσφορά');?></button>
            </div>
          </div>



            
        </div>
      </div>
      
      <?php
      echo $gks_custom_row['html'];
      //echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
    
      gks_plugins_functions_run('admin_orders_item_div2',array(
        'id'=>&$id,
        'row'=>&$row,
      ));
      ?>

    </div>
                  
    
  </div>
</div>

</div>


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">


     
<?php
$GKS_ORDERS_STATUS_BUTTONS=array(
  '005prodraft' =>        array(                        'cmdprint','010draft',),
  '010draft' =>           array('cmdupdate','cmddelete','cmdprint',           '020pending','030forcancellation','025offer','055wait_payment','060registered','070inproduction'), // 
  '020pending' =>         array('cmdupdate',            'cmdprint','010draft','025offer','050rejected','055wait_payment','060registered'), // ,wc-pending (1)
  '025offer' =>           array('cmdupdate',            'cmdprint','010draft','030forcancellation','050rejected','055wait_payment','060registered',),

  '030forcancellation' => array('cmdupdate',            'cmdprint','010draft','040cancelled',),  // 
  '040cancelled' =>       array('cmdupdate',            'cmdprint','010draft',), // , wc-cancelled (5)
  '050rejected' =>        array('cmdupdate',            'cmdprint','010draft',), // 
  '055wait_payment' =>    array('cmdupdate',            'cmdprint','010draft','050rejected','060registered',), // wc-on-hold (3)
  
  //apo edo kai kato allazei apothiki (ektos apo 080failed) kai parnei arithmo i seira apo to imerologio
  '060registered' =>      array('cmdupdate',            'cmdprint','010draft','040cancelled','070inproduction','090indelivery','080failed'), // 
  '070inproduction' =>    array('cmdupdate',            'cmdprint','010draft','080failed','090indelivery',), // , wc-processing (2)
  '080failed' =>          array('cmdupdate',            'cmdprint','010draft',), // , wc-failed (7)
  '090indelivery' =>      array('cmdupdate',            'cmdprint','010draft','050rejected','100completed',),  // 
  '095execute' =>         array('cmdupdate',            'cmdprint','010draft','100completed',),
  '100completed' =>       array('cmdupdate',            'cmdprint','010draft',),  // , wc-completed (4)
  '110payment' =>         array('cmdupdate',            'cmdprint','010draft',),
                                                                               // wc-refunded (6)
                                                                                  
);

if (isset($GKS_ORDERS_STATUS_BUTTONS[$order_state])) {
  if ($perm_gks_orders_edit) {
    gks_plugins_functions_run('admin_orders_item_buttons_before',array(
      'id'=>&$id,
      'row'=>&$row,
    ));
    
    
    if (in_array('cmdupdate',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom">'.gks_lang('Αποθήκευση').'</button> ';
    if (in_array('cmddelete',$GKS_ORDERS_STATUS_BUTTONS[$order_state]) and $id>0) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger thisdeleterowbtn" data-id="'.($row['id_order']>0 ? $row['id_order'] : '').'" data-model="gks_orders" data-backurl="admin-orders.php">'.gks_lang('Διαγραφή').'</button> ';
    if (in_array('010draft',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_010draft" id="submit_button_010draft">'.gks_lang('Επαναφορά σε Προσχέδιο').'</button> ';
    if (in_array('020pending',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_020pending" id="submit_button_020pending">'.getOrderStateDescr('020pending').'</button> ';
    if (in_array('025offer',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_025offer" id="submit_button_025offer">'.getOrderStateDescr('025offer').'</button> ';
    if ($id>0 and in_array('030forcancellation',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_030forcancellation" id="submit_button_030forcancellation">'.getOrderStateDescr('030forcancellation').'</button> ';
    if (in_array('040cancelled',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_040cancelled" id="submit_button_040cancelled">'.getOrderStateDescr('040cancelled').'</button> ';
    if (in_array('050rejected',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_050rejected" id="submit_button_050rejected">'.getOrderStateDescr('050rejected').'</button> ';
    if (in_array('055wait_payment',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_055wait_payment" id="submit_button_055wait_payment">'.getOrderStateDescr('055wait_payment').'</button> ';
    if (in_array('060registered',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_060registered" id="submit_button_060registered">'.getOrderStateDescr('060registered').'</button> ';
    if (in_array('080failed',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_080failed" id="submit_button_080failed">'.getOrderStateDescr('080failed').'</button> ';
    if (in_array('070inproduction',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_070inproduction" id="submit_button_070inproduction">'.getOrderStateDescr('070inproduction').'</button> ';
    if (in_array('090indelivery',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_090indelivery" id="submit_button_090indelivery">'.getOrderStateDescr('090indelivery').'</button> ';
    if (in_array('095execute',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_095execute" id="submit_button_095execute">'.getOrderStateDescr('095execute').'</button> ';
    if (in_array('100completed',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_order_state_100completed" id="submit_button_100completed">'.getOrderStateDescr('100completed').'</button> ';
  }
  if (in_array('cmdprint',$GKS_ORDERS_STATUS_BUTTONS[$order_state])) 
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print">'.gks_lang('Εκτύπωση').' <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button> ';

  if ($has_production_line_or_sintagi) {
    echo '<a href="admin-production-item.php?id='.$id.'"><button type="button" style="margin-bottom:10px;" class="btn btn-dark tooltipster" title="'.gks_lang('Παραγωγή Παραγγελίας').'"><i class="fas fa-cogs" style="color: #35dc35;font-size: 120%;"></i></button></a> ';
  }

  if ($id>0 and $perm_gks_orders_add) {
    echo '<a href="admin-orders-item.php?id=-1&template_id='.$id.'" style="margin-bottom:10px;" '.
      'class="btn btn-primary tooltipster" '.
      'id="submit_button_template" '.
      'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'<br>'.gks_lang('ή').'<br>'.
              '<button class=\'btn btn-primary btn-sm\' style=\'margin-top:6px;\' '.
              'onclick=\'submit_button_template_create(4);\' '.
              'id=\'submit_button_template_create\' data-obj=\'gks_orders\''.
              '>'.gks_lang('Ορισμός ως πρότυπο').'</button>'.
             '</div>">'.
      '<i class="fas fa-copy" style="font-size: 120%;"></i>'.
    '</a> ';
  }
    
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

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">

      <?php echo getObjectRels('gks_orders',$id); ?>
          
          
        
      <?php echo getActivityObjectTable('gks_orders',$id); ?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_orders_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_orders_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_orders_messages.order_id=".$id."
            ORDER BY gks_orders_messages.mydate_add DESC, gks_orders_messages.id_order_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_order_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php 
                if (!empty($row_msg['woo_author'])) echo $row_msg['woo_author'];
                else echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['order_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεσμοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('links');?>><?php
          
          $extra_order_id_links=0;
          gks_plugins_functions_run('admin_orders_item_links_before',array(
            'id'=>&$id,
            'row'=>&$row,
            'extra_order_id_links' => &$extra_order_id_links,
          ));
          
          
          
          
          
          $query = "SELECT gks_orders_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_orders_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_orders_links.order_id in (".$id.($extra_order_id_links>0 ? ','.$extra_order_id_links : '').")
          ORDER BY gks_orders_links.mydate, gks_orders_links.id_order_links;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="links_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Χρήστης');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Προσθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Σύνδεσμος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Μέγεθος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          $need_download_timer=0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr id="tr_links_url_<?php echo $row_list['id_order_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_order_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_order_links'];?>" data-model="gks_orders_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

              </td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i', 1);?></td>   
              <td       style="word-break: break-all;">
                <div><?php 
                $temp=trim_gks($row_list['url']);
                if ($temp!='' and startwith($temp,'http')) {
                  $temp='<a href="'.$temp.'" target="_blank">'.(strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp).'</a>';
                  echo $temp;
                } else {
                  echo (strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp);
                }
                ?></div>
                <div class="progress download-perc" data-id="<?php echo $row_list['id_order_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_order_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_order_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_order_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_order_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_order_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_order_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_order_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_order_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
              } 
                
              ?></td>  
            </tr>
          <?php } ?>


            <tr class="" id="tr_new_links_url">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="links_url"    id="links_url"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('π.χ.');?> https://we.tl/...">
              </td>  
            </tr>
            <tr class="" id="tr_new_links_url_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_links_url"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>                       
        </div>
      </div>
              


			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_orders','id'=>$id,'aws_folder' => $aws_folder));
      echo $obj_fileslist['html'];
      ?>

    </div>

    
    
    <div class="col-xl-6">
      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_order_state']) != '') {?>
      
      <div class="card gks_card_expand" id="gks_print">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εκτύπωση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('print');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['print_date'])) echo showDate(strtotime($row['print_date']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εκτύπωση από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['print_user_id'].'">'.$row['gks_nickname_print'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση όταν έγινε η εκτύπωση');?>:</label>
            <div class="col-sm-8"><span class="order_state_<?php echo $row['print_order_state'];?>"><?php echo getOrderStateDescr($row['print_order_state']);?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'order/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=order%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>    
      <?php } 
      

      gks_plugins_functions_run('admin_orders_item_div3',array(
        'id'=>&$id,
        'row'=>&$row,
      ));

      if ($perm_gks_orders_edit) {?>

              
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('hist');?>>
          <!--
          <div class="form-group row online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none;';?>">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποστολή μηνύματος στο Online Προσφορά');?>:</label>
            <div class="col-sm-8">
              <textarea id="gks_order_online_message" class="form-control form-control-sm" style="min-height: 100px; height: 10px;" placeholder="<?php echo gks_lang('Κάποιο μήνυμα...');?>"></textarea>
              <div style="margin-top:10px;">
                <button type="button" class="btn btn-sm btn-primary" id="gks_order_online_message_send"><?php echo gks_lang('Αποστολή');?></button>  
              </div>
            </div>
          </div> 
          -->
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="table_order_log">
          <thead>
            <tr>
              <th class="table-dark" scope="col" width="0%" nowrap>#</th>
              <th class="table-dark" scope="col" width="0%" nowrap style="text-align:center;">
                <div class="tooltipster gks_noselect" id="from_online_td" data-state='all' title="<?php echo gks_lang('Εναλλαγή Φίλτρου για τα OnLine');?>">
                  <?php echo gks_lang('OnLine');?>
                </div>
                <div id="go_gks_order_online_message2_div" class="online_enable_show" style="<?php if ($row['online_enable']==0) echo 'display:none';?>">
                  <i class="fas fa-sms tooltipster" id="go_gks_order_online_message2" title="<?php echo gks_lang('Αποστολή μηνύματος στο Online Προσφορά');?>"></i>
                </div>
              </th>
              <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
              <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>
              <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Τι');?></th>
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_log="SELECT gks_orders_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_orders_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_orders_log.order_id=".$id."
          ORDER BY gks_orders_log.id_gks_orders_log DESC;";
          $result_log = $db_link->query($sql_log);        
          if (!$result_log) debug_mail(false,'error sql',$sql_log);
          if (!$result_log) die('sql error');
          
          $j = 0;
          while ($row_log = $result_log->fetch_assoc()) {
            $j++; ?>
        
          <tr class="online_<?php echo $row_log['from_online'];?>">
            <th scope="row" align="center"><?php echo $j;?></th>
            <td align="center"><?php
            if ($row_log['from_online']==1) echo '<i class="fas fa-globe gks_from_online"></i>';  
            
            ?></td>
            <td align="left"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
            <td align="left"><?php echo $row_log['gks_nickname'];?></td>  
            <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
          </tr>
          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
              
      
      <?php } ?>    
    
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_order']>0) echo $row['id_order'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">GUID:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="gks_order_guid"><?php echo $row['order_guid'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
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


<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid">
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>

<div id="dialog_user_save" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid">
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Προσθήκη ή επιλογή επαφής');?></div>
    </div>
    <div class="form-group row">  
      <div style="font-size: 100%;text-align:center;width: 100%;">
        <?php echo gks_lang('Βρέθηκαν οι παρακάτω επαφές στο σύστημα');?>
        <?php echo gks_lang('Η αναζήτηση έγινε με βάση το σχετικό πεδίο που αναφέρεται στην στήλη <b>Αναζήτηση</b>.');?>
        <?php echo gks_lang('Μήπως η επαφή που θέλετε να προσθέσετε είναι μία από τις παρακάτω;');?>
        <?php echo gks_lang('Εάν <b>ναι</b>, τότε επιλέξτε την.');?>
        <?php echo gks_lang('Εάν <b>όχι</b>, τότε μπορείτε να προσθέσετε την νέα επαφή επιλέγοντας την επιλογή <b>Προσθήκη νέας επαφής</b>');?>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" style="text-align: center !important;">
        <input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_new" value="-1">  <label class="gks_label" for="dialog_user_save_radio_new"><?php echo gks_lang('Προσθήκη νέας επαφής');?>:</label>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_user_save_html">
        
      </div>
    </div>
  
  </div>
</div>

<?php include_once 'admin-obj-send-message.php'; ?>


 


<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group1 row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Εκτύπωσης');?></div>
    </div>
        
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_pdf"  value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
          <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_html" value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
          <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_jpg" value="3" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
          <i src="img/jpg21.png" class="tooltipster" title="jpg" style="height:15px;vertical-align: top;"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Προσανατολισμός');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
          <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
          <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:120%"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
          <img src="img/palette-color.png" border="0" width="15" style="vertical-align: top;">
          </label>
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
          <img src="img/palette-gray.png" border="0" width="15" style="vertical-align: top;">
          </label>
      </div>
    </div>    

    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Μεγέθυνση');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left">
        <div id="dialog_print_zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
          <div id="dialog_print_zoom_slider_handle" class="ui-slider-handle"></div>
        </div>
      </div>
    </div>

    
    <div class="row" >
      <div class="gks_print_thump_container">
<?php
  $user_def_form_id=0;
  if (isset($gks_user_settings['print']['form_id_order'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_order']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_orders'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    
    $perm_company_ids=trim_gks($row_print_forms['perm_company_ids']);
    $perm_acc_journal_ids=trim_gks($row_print_forms['perm_acc_journal_ids']);
    $perm_acc_seires_ids=trim_gks($row_print_forms['perm_acc_seires_ids']);

    $temp=array('id'=>intval($row_print_forms['id_print_form']));
    if ($perm_company_ids!='') $temp['perm_company_ids']=unserialize($perm_company_ids);
    if ($perm_acc_journal_ids!='') $temp['perm_acc_journal_ids']=unserialize($perm_acc_journal_ids);
    if ($perm_acc_seires_ids!='') $temp['perm_acc_seires_ids']=unserialize($perm_acc_seires_ids);
    $perm_print_forms[]=$temp;
    
    $div_form='<div class="gks_print_thump_div '.
      ($user_def_form_id==$row_print_forms['id_print_form'] ? 'gks_print_thump_div_selected' : '').
      '" data-form_id="'.$row_print_forms['id_print_form'].'" '.
      'data-lang="'.$row_print_forms['gks_lang'].'" '.
      'data-file_type="'.$row_print_forms['file_type'].'" '.
      'data-landscape="'.$row_print_forms['is_landscape'].'" '.
      'data-grayscale="'.$row_print_forms['grayscale'].'" '.
      'data-zoom="'.intval($row_print_forms['zoom']*100).'" '.
      '>';
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>';
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>';
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>';
      
    
    $div_form.='</div>';
    echo $div_form;
  }
  
  $div_form='<div id="gks_print_thump_more_div">';
    $div_form.='<div id="gks_print_thump_more_text"><i class="fas fa-plus-circle" style="font-size:200%;color:#35dc35;"></i><br>'.gks_lang('Εμφάνιση όλων').'</div>';
  $div_form.='</div>';
  echo $div_form;
  

?>      
      </div>
    </div>
<?php
  $erp_app_id=0;
  if ($order_seira_id>0) {
    $sql_send_erp_app="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest,  
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_acc_seires 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    where gks_acc_seires.id_acc_seira=".$order_seira_id;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.='gks ERP App Desktop: '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
      if ($row_send_erp_app['erp_app_dest']=='printer') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Εκτυπωτής').'<br>';
        $send_erp_app_tooltip.=gks_lang('Μέθοδος').': '.erp_app_dest_printer_method_descr($row_send_erp_app['erp_app_dest_printer_method']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[0,1])) $send_erp_app_tooltip.=gks_lang('Εκτυπωτής').': '.trim_gks($row_send_erp_app['erp_app_dest_printer']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[2]))   $send_erp_app_tooltip.=gks_lang('IP εκτυπωτή').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']).'<br>';
        $send_erp_app_tooltip.=gks_lang('Αντίτυπα').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_copies']);
        
      } else if ($row_send_erp_app['erp_app_dest']=='folder') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Φάκελος').'<br>';
        $send_erp_app_tooltip.=gks_lang('Φάκελος').': '.trim_gks($row_send_erp_app['erp_app_dest_folder']);
      }     
      $send_erp_app_checkbox_disable=true;
      if (isset($row_send_erp_app['erp_app_last_ping'])) {
        if (strtotime($row_send_erp_app['erp_app_last_ping']) > time()-15*60) {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
          $send_erp_app_checkbox_disable=false;
        } else {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_not_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
        }
      }
      
    }
  }
  if ($erp_app_id>0) {
?>    
    <div class="row">  
      <div class="col-sm-12 form-control-sm text-sm-left">
        <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" <?php if ($send_erp_app_checkbox_disable) echo 'disabled'; else echo 'checked';?>>
        <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> <?php echo gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop');?></label>
        <i class="fas fa-info-circle tooltipster" title="<?php echo $send_erp_app_tooltip;?>" style="font-size: 150%;position: relative;top: 4px;"></i>
      </div>
    </div>    
<?php } ?>    

  </div>  
</div>

<div id="dialog_add_product_variables">
  <div id="dialog_add_product_variables_inner">
    <div id="dialog_add_product_variables_header">
      <span><?php echo gks_lang('Προσθήκη μεταβλητού είδους με ποσότητες');?></span>
    </div>
    <i class="fas fa-window-close" id="dialog_add_product_variables_close"></i>    
    <div id="dialog_add_product_variables_data">
      <div class="dialog_add_product_variables_data2 container-fluid">
        <div class="form-group row row_gks_multi_copies_enable">
          <label for="dialog_add_product_variables_product_id" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Είδος');?>:</label>
          <div class="col-md-2">
            <input id="dialog_add_product_variables_product_id" type="text" class="form-control form-control-sm" value="" 
            style="width:calc(98% - 22px);display:inline;" 
            placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
            data-id="0">
            <a id="autocomplete_dialog_add_product_variables_product_id" tabindex="-1" href="admin-products-item.php?id=0" style="display:none"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
          </div>
          
          <label for="dialog_add_product_row" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Γραμμή');?>:</label>
          <div class="col-md-2">
            <select id="dialog_add_product_row" class="form-control form-control-sm">
              <option value="0"><?php echo gks_lang('Κενό');?></option>
            </select>
          </div>          

          <label for="dialog_add_product_col" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Στήλη');?>:</label>
          <div class="col-md-2">
            <select id="dialog_add_product_col" class="form-control form-control-sm">
              <option value="0"><?php echo gks_lang('Κενό');?></option>
            </select>
          </div>          
        </div>
        <div class="form-group row" id="row_gks_multi_copies_enable_others">
          <label class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Άλλες ιδιότητες');?>:</label>
          <div class="col-md-10" id="dialog_add_product_others">
            other attrs
          </div>          
        </div>
                
        <div class="col-md-12" id="dialog_add_product_variables_table">

         
                  

          
        </div>
      </div>
    
      <div class="dialog_add_product_variables_footer">
        
        <button class="btn btn-primary" id="dialog_add_product_variables_save"><?php echo gks_lang('Εφαρμογή');?></button>
        <button class="btn btn-danger" id="dialog_add_product_variables_cancel"><?php echo gks_lang('Ακύρωση');?></button>
      </div>
    
    </div>    
    
  </div>
</div>

<?php
gks_plugins_functions_run('admin_orders_item_dialogs',array(
  'id'=>&$id,
  'row'=>&$row,
));
?>
    
<?php
//print '<pre>';
//print_r($mybasketarray);
//print '</pre>';

?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_orders';
var from_php_activity_model='gks_orders';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;
var from_php_template_id=<?php echo $template_id;?>;
var from_php_gks_lock=<?php echo ($gks_lock ? 'true' : 'false');?>;
var from_php_number_gks_lock=<?php echo ($gks_number_lock ? 'true' : 'false');?>;
var from_php_user_gks_lock=<?php echo ($gks_user_lock ? 'true' : 'false');?>;




var last_aa=<?php echo $aa;?>;
var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 
var from_php_need_download_timer='<?php echo $need_download_timer;?>';


var from_php_GKS_ORDERS_COL_ITEMPRICE=<?php echo ($GKS_ORDERS_COL_ITEMPRICE? 'true' : 'false')?>;
var from_php_GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA=<?php echo ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA? 'true' : 'false')?>;
var from_php_GKS_ORDERS_COL_FPA=<?php echo ($GKS_ORDERS_COL_FPA? 'true' : 'false')?>;
var from_php_GKS_ORDERS_AWS=<?php echo ($GKS_ORDERS_AWS? 'true' : 'false')?>;
var from_php_GKS_ORDERS_SETS=<?php echo ($GKS_ORDERS_SETS? 'true' : 'false')?>;
var from_php_GKS_ORDERS_SHEETS=<?php echo ($GKS_ORDERS_SHEETS? 'true' : 'false')?>;
var from_php_GKS_ORDERS_OCCASION=<?php echo ($GKS_ORDERS_OCCASION? 'true' : 'false')?>;


var from_php_gkscols1='<?php echo $gkscols1;?>';
var from_php_gkscols2='<?php echo $gkscols2;?>';
var from_php_gkscols3='<?php echo $gkscols3;?>';
var from_php_gkscols4='<?php echo $gkscols4;?>';
var from_php_gkscols5='<?php echo $gkscols5;?>';
var from_php_gkscols6='<?php echo $gkscols6;?>';
var from_php_gkscols7='<?php echo $gkscols7;?>';
var from_php_gkscols8='<?php echo $gkscols8;?>';
var from_php_gkscols9='<?php echo $gkscols9;?>';
var from_php_gkscols10='<?php echo $gkscols10;?>';

var from_php_delivery_way_default=<?php echo $gks_user_settings['gks_orders']['tropos_apostolis'];?>;
var from_php_payment_way_default=<?php echo $gks_user_settings['gks_orders']['tropos_pliromis'];?>;

var kostos_apostolis_mode='<?php if ($id>0) echo "manual";?>';
var kostos_pliromis_mode='<?php if ($id>0) echo "manual";?>';

var coupons_array = JSON.parse('<?php echo json_encode($mybasketarray['coupons']);?>');
//console.log(coupons_array);

var from_php_monades = [];
<?php foreach ($monades as $monada) {
  echo 'from_php_monades.push({id: '.$monada['id'].', descr: $.base64.decode("'.base64_encode($monada['descr']).'"), symbol: $.base64.decode("'.base64_encode($monada['symbol']).'")});'."\n";
}?>
//console.log(from_php_monades);

var from_php_fpa_list = [];
<?php foreach ($fpa_list as $fpa_item) {
  echo 'from_php_fpa_list.push({id: '.$fpa_item['id'].', descr: $.base64.decode("'.base64_encode($fpa_item['descr']).'")});'."\n";
}?>
var from_php_fpa_aade = [];
<?php foreach ($fpa_aade as $fpa_item) {
  echo 'from_php_fpa_aade.push({id: '.$fpa_item['id'].', descr: $.base64.decode("'.base64_encode($fpa_item['descr']).'")});'."\n";
}?>

var fields_change=[];
<?php 
foreach ($fields_change as $field_aa => $field_name) {
  echo "fields_change[".$field_aa."]='".$field_name."';";
} ?>
//console.log(fields_change);

var from_php_order_state='<?php echo $order_state;?>';
var from_php_acc_eidos_parastatikou_id=<?php echo $acc_eidos_parastatikou_id;?>;
var from_php_eidos_parastatikou_type_id=<?php echo $eidos_parastatikou_type_id;?>;
var from_php_eidos_parastatikou_need_prev=<?php echo $eidos_parastatikou_need_prev;?>;
var from_php_eidos_parastatikou_has_fpa=<?php echo $eidos_parastatikou_has_fpa;?>;



var from_php_eidos_parastatikou_need_afm=<?php echo $eidos_parastatikou_need_afm;?>;
var from_php_eidos_parastatikou_balance_pros=<?php echo $eidos_parastatikou_balance_pros;?>;
var from_php_whi_eidos_parastatikou_stock_pros=<?php echo $whi_eidos_parastatikou_stock_pros_org;?>;
var from_php_whi_eidos_parastatikou_type_id=<?php echo $whi_eidos_parastatikou_type_id_org;?>;

var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_order']) ? $gks_user_settings['print']['form_id_order'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_order']));?>'));



var from_php_enter_order=[];
<?php
if (isset($gks_user_settings['gks_orders']['enter_order']) and is_array($gks_user_settings['gks_orders']['enter_order'])) {
  foreach ($gks_user_settings['gks_orders']['enter_order'] as $value) {
    echo 'from_php_enter_order.push(\''.$value.'\');'."\n";
  } 
}
?>


var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders','delete',$id);?>;
  
var from_php_def_sets_vals=[];
<?php
  if ($GKS_ORDERS_SETS_VALS!='') {
    $vals=explode(']][[',$GKS_ORDERS_SETS_VALS);
    foreach ($vals as $val) {
      $val=trim_gks($val);
      if ($val!='') {
        echo 'from_php_def_sets_vals.push($.base64.decode(\'' . base64_encode($val) . '\'));'."\n";  
      }
    } 
  }
?>

var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));
var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;


var gks_plugins_js_admin_orders_item_doc_ready=[];
var gks_plugins_js_admin_orders_item_mysubmit_datasend=[];

<?php
gks_plugins_functions_run('admin_orders_item_root_scripts',array(
  'id'=>&$id,
  'row'=>&$row,
));
?>


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-orders-item.js"></script>
<script src="js/admin-orders-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();

gks_plugins_functions_run('admin_orders_item_scripts_before_footer',array(
  'id'=>&$id,
));


include_once('_my_footer_admin.php');
