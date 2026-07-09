<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



function getOrderStateDescr($mystate,$load_lang='') {
  global $gks_user_settings;
  if ($load_lang=='') {
    $load_lang='el-GR';
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  if ($load_lang=='el-GR') {  

    switch ($mystate) {
      case '005prodraft': return gks_lang('Σε καλάθι','part4','getOrderStateDescr'); break; 
      case '010draft': return gks_lang('Πρόχειρη','part4','getOrderStateDescr'); break; 
      case '020pending': return gks_lang('Σε Αναμονή','part4','getOrderStateDescr'); break; 
      case '025offer': return gks_lang('Προσφορά','part4','getOrderStateDescr'); break; 
      case '030forcancellation': return gks_lang('Προς Ακύρωση','part4','getOrderStateDescr'); break; 
      case '040cancelled': return gks_lang('Ακυρωμένη','part4','getOrderStateDescr'); break; 
      case '050rejected': return gks_lang('Απορρίφθηκε','part4','getOrderStateDescr'); break; 
      case '055wait_payment': return gks_lang('Αναμονή Πληρωμής','part4','getOrderStateDescr'); break; 
      case '060registered': return gks_lang('Καταχωρημένη','part4','getOrderStateDescr'); break; 
      case '070inproduction': return gks_lang('Σε παραγωγή','part4','getOrderStateDescr'); break; 
      case '080failed': return gks_lang('Απέτυχε','part4','getOrderStateDescr'); break; 
      case '090indelivery': return gks_lang('Προς Παράδοση','part4','getOrderStateDescr'); break; 
      case '095execute': return gks_lang('Εκτελέστηκε','part4','getOrderStateDescr'); break; 
      case '100completed': return gks_lang('Ολοκληρωμένη','part4','getOrderStateDescr'); break; 
      case '110payment': return gks_lang('Εξοφλημένη','part4','getOrderStateDescr'); break;
  
      default: return $mystate; break; 
    } 
  } else {
    switch ($mystate) {
      case '005prodraft': return 'Cart'; break; 
      case '010draft': return 'Draft'; break; 
      case '020pending': return 'Pending'; break; 
      case '025offer': return 'Offer'; break; 
      case '030forcancellation': return 'For Cancellation'; break; 
      case '040cancelled': return 'Cancelled'; break; 
      case '050rejected': return 'Rejected'; break; 
      case '055wait_payment': return 'Wait Payment'; break; 
      case '060registered': return 'Registered'; break; 
      case '070inproduction': return 'In Production'; break; 
      case '080failed': return 'Failed'; break; 
      case '090indelivery': return 'In Delivery'; break; 
      case '095execute': return 'Execute'; break; 
      case '100completed': return 'Completed'; break; 
      case '110payment': return 'Payment'; break;
  
      default: return $mystate; break; 
    }     
  }
}



function select_gks_orders($only_one_id=0) {
  global $GKS_ORDERS_OCCASION;


$sql="SELECT gks_orders.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name,
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,
gks_nomoi.nomos_descr, gks_country.country_name,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to,
gks_prod_warehouses_from.warehouse_name AS prod_warehouse_name_from, gks_prod_warehouses_to.warehouse_name AS prod_warehouse_name_to

";

if ($GKS_ORDERS_OCCASION) $sql.= ",gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, gks_orders_occasion.mydate_add as occasion_mydate_add";

$ret_plugin_sql='';
gks_plugins_functions_run('functions_orders_select_gks_orders_select',array(
  'ret_plugin_sql'=>&$ret_plugin_sql,
));
$sql.=$ret_plugin_sql;



$sql.= "
FROM ";

$ret_plugin_sql='';
gks_plugins_functions_run('functions_orders_select_gks_orders_from1',array(
  'ret_plugin_sql'=>&$ret_plugin_sql,
));
$sql.=" ".$ret_plugin_sql;



if ($GKS_ORDERS_OCCASION) $sql.= " ((";

$sql.= "
((((((((((((((((((((((((((gks_orders ";

$ret_plugin_sql='';
gks_plugins_functions_run('functions_orders_select_gks_orders_from2',array(
  'ret_plugin_sql'=>&$ret_plugin_sql,
  'only_one_id' => $only_one_id,
));
$sql.=$ret_plugin_sql;




if ($GKS_ORDERS_OCCASION) {
$sql.= "
LEFT JOIN gks_orders_occasion on gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion)
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type) 
";
}

$sql.= "
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_orders.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_orders.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_company on gks_orders.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_orders.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_orders.order_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_country ON gks_orders.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_nomoi ON gks_orders.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_orders.user_lang = gks_lang.id_lang)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_orders.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_orders.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_orders.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_orders.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_orders.warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_orders.warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN gks_warehouses AS gks_prod_warehouses_from ON gks_orders.prod_warehouses_id_from = gks_prod_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_prod_warehouses_to ON gks_orders.prod_warehouses_id_to = gks_prod_warehouses_to.id_warehouse


";
//echo '<pre>';echo $sql; die();

return $sql;
  
}

function get_order_details_txt($id, &$myarray=array(),&$myarray_line=array()) {
  global $db_link;
  $myarray=array();
  
  gks_plugins_functions_run('functions_orders_get_order_details_txt',array(
    'id'=>&$id,
    'myarray'=>&$myarray,
  ));

    
  $sql="SELECT gks_orders_products.*, 
  gks_eshop_products.product_code, gks_eshop_products.product_photo, gks_eshop_products.product_descr, gks_eshop_products.product_descr_small, gks_eshop_products.product_descr_big, 
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
  gks_eshop_pricelist.pricelist_descr
  FROM ((gks_orders_products 
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_fpa ON gks_orders_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
  LEFT JOIN gks_eshop_pricelist ON gks_orders_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist
  WHERE gks_orders_products.order_id=".$id."
  ORDER BY gks_orders_products.id_order_product;";
  //gks_orders_products.product_set
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return 'sql error';}
  while ($eidos = $result->fetch_assoc()) {
    $myarray[] = array($eidos['product_descr'], $eidos['product_quantity']);
    
    
  }
  

  
 
  $ret='';
  $myarray_line=array();
  foreach ($myarray as $value) {
    $ret.=$value[0].': '.$value[1].'<br>';
    $myarray_line[]=trim_gks($value[0].': '.$value[1]);
  } 
  
  if ($ret!='') $ret=substr($ret, 0, strlen($ret)-4);
  
  
  
  
  return $ret;
}  

function gks_order_sxolio_log($id,$row_old,$products_old,$extra_address_old,$sxolio_log_start,$gks_custom_row_old) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_CRM_ENABLE;
  global $GKS_ORDERS_OCCASION;
  
  $sql=select_gks_orders($id)." where id_order=".$id." limit 1"; 

  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_new = $result->fetch_assoc();
  
  $sql="SELECT gks_orders_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
  FROM (gks_orders_products 
  LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_orders_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
  WHERE gks_orders_products.order_id=".$id."
  ORDER BY gks_orders_products.product_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $products_new=array();
  while ($row = $result->fetch_assoc()) {
    $products_new[]=$row;
  }
    
  
  $sxolio_log=$sxolio_log_start;
  if (trim_gks($row_old['company_title']) != trim_gks($row_new['company_title'])) 
    $sxolio_log.=gks_lang('Εταιρεία').': <b>'.$row_old['company_title'].'</b> [[-r]] <b>'.$row_new['company_title'].'</b>'.'<br>';
  
  if (trim_gks($row_old['company_sub_title']) != trim_gks($row_new['company_sub_title'])) 
    $sxolio_log.=gks_lang('Υποκατάστημα').': <b>'.$row_old['company_sub_title'].'</b> [[-r]] <b>'.$row_new['company_sub_title'].'</b>'.'<br>';

  if (trim_gks($row_old['acc_journal_descr']) != trim_gks($row_new['acc_journal_descr'])) 
    $sxolio_log.=gks_lang('Ημερολόγιο').': <b>'.$row_old['acc_journal_descr'].'</b> [[-r]] <b>'.$row_new['acc_journal_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['seira_code'].' - '.$row_old['seira_descr']) != trim_gks($row_new['seira_code'].' - '.$row_new['seira_descr'])) 
    $sxolio_log.=gks_lang('Σειρά').': <b>'.$row_old['seira_code'].' - '.$row_old['seira_descr'].'</b> [[-r]] <b>'.$row_new['seira_code'].' - '.$row_new['seira_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['order_number_int']) != trim_gks($row_new['order_number_int'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['order_number_int'].'</b> [[-r]] <b>'.$row_new['order_number_int'].'</b>'.'<br>';

  if (showDate(strtotime($row_old['order_date']), 'd/m/Y H:i', 1) != showDate(strtotime($row_new['order_date']), 'd/m/Y H:i', 1)) 
    $sxolio_log.=gks_lang('Ημερομηνία').': <b>'.showDate(strtotime($row_old['order_date']), 'd/m/Y H:i', 1).'</b> [[-r]] <b>'.showDate(strtotime($row_new['order_date']), 'd/m/Y H:i', 1).'</b>'.'<br>';

  if ($row_old['order_state'].'' != $row_new['order_state'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': <span class="order_state_'.$row_old['order_state'].'">'.getOrderStateDescr($row_old['order_state']).'</span> [[-r]] '.
    '<span class="order_state_'.$row_new['order_state'].'">'.getOrderStateDescr($row_new['order_state']).'</span>'.'<br>';

  if ($GKS_ORDERS_OCCASION) {
    $occasion_title_old = '';
    $temp = trim_gks($row_old['occasion_type_descr']);    if ($temp!='') $occasion_title_old.=$temp.' / ';
    $temp = trim_gks($row_old['occasion_title']);         if ($temp!='') $occasion_title_old.=$temp.' / ';
    //$temp =  trim_gks($row_old['payment_acquirer_name']); if ($temp!='') $occasion_title_old.=$temp.' / ';
    $temp =  trim_gks($row_old['occasion_mydate_add']);   if ($temp!='') $occasion_title_old.=showDate(strtotime($row_old['occasion_mydate_add']), 'd/m/Y H:i', 1) .' / ';
    if ($occasion_title_old!='') $occasion_title_old=substr($occasion_title_old, 0, strlen($occasion_title_old) - 3);
  
    $occasion_title_new = '';
    $temp = trim_gks($row_new['occasion_type_descr']); if ($temp!='') $occasion_title_new.=$temp.' / ';
    $temp = trim_gks($row_new['occasion_title']);      if ($temp!='') $occasion_title_new.=$temp.' / ';
    //$temp =  trim_gks($row_new['payment_acquirer_name']); if ($temp!='') $occasion_title_new.=$temp.' / ';
    $temp =  trim_gks($row_new['occasion_mydate_add']);   if ($temp!='') $occasion_title_new.=showDate(strtotime($row_new['occasion_mydate_add']), 'd/m/Y H:i', 1) .' / ';
    if ($occasion_title_new!='') $occasion_title_new=substr($occasion_title_new, 0, strlen($occasion_title_new) - 3);
  
    if ($occasion_title_old != $occasion_title_new) 
      $sxolio_log.=gks_lang('Περίσταση').': <b>'.$occasion_title_old.'</b> [[-r]] '.
      '<b>'.$occasion_title_new.'</b>'.'<br>';
  }
  
  if (trim_gks($row_old['fiscal_position_descr']) != trim_gks($row_new['fiscal_position_descr'])) 
    $sxolio_log.=gks_lang('Φορολογική Θέση').': <b>'.$row_old['fiscal_position_descr'].'</b> [[-r]] <b>'.$row_new['fiscal_position_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['pricelist_descr']) != trim_gks($row_new['pricelist_descr'])) 
    $sxolio_log.=gks_lang('Τιμοκατάλογος').': <b>'.$row_old['pricelist_descr'].'</b> [[-r]] <b>'.$row_new['pricelist_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['def_ekptosi']) != trim_gks($row_new['def_ekptosi'])) 
    $sxolio_log.=gks_lang('Προεπιλεγμένη έκπτωση').': <b>'.myNumberFormatNo0Local($row_old['def_ekptosi']).'%'.'</b> [[-r]] <b>'.myNumberFormatNo0Local($row_new['def_ekptosi']).'%'.'</b>'.'<br>';
  
  if (trim_gks($row_old['coupons']) != trim_gks($row_new['coupons'])) {
    $coupons_old='';
    $coupons_parts=explode('|',trim_gks($row_old['coupons']));
    foreach ($coupons_parts as $value) {
      $value=trim_gks($value);
      if ($value!='') $coupons_old.='<span class="coupons_span"><span class="coupons text-sm">'.$value.'</span></span> ';
    }
    $coupons_new='';
    $coupons_parts=explode('|',trim_gks($row_new['coupons']));
    foreach ($coupons_parts as $value) {
      $value=trim_gks($value);
      if ($value!='') $coupons_new.='<span class="coupons_span"><span class="coupons text-sm">'.$value.'</span></span> ';
    }
    $sxolio_log.=gks_lang('Κουπόνια').': '.$coupons_old.' [[-r]] '.$coupons_new.'<br>';
  }



  //echo '<pre>'.$row_new['company_title']; die();
  
  
  
  if ((isset($row_old['gks_nickname']) and isset($row_old['gks_nickname']) == false) or 
      (isset($row_old['gks_nickname']) == false and isset($row_old['gks_nickname'])) or 
      $row_old['gks_nickname'] != $row_new['gks_nickname']) 
    $sxolio_log.=gks_lang('Επαφή').': <b>'.(isset($row_old['gks_nickname']) ? $row_old['gks_nickname'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['gks_nickname']) ? $row_new['gks_nickname'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['user_first_name']) != trim_gks($row_new['user_first_name'])) 
    $sxolio_log.=gks_lang('Όνομα').': <b>'.$row_old['user_first_name'].'</b> [[-r]] <b>'.$row_new['user_first_name'].'</b>'.'<br>';
  
  if (trim_gks($row_old['user_last_name']) != trim_gks($row_new['user_last_name'])) 
    $sxolio_log.=gks_lang('Επώνυμο').': <b>'.$row_old['user_last_name'].'</b> [[-r]] <b>'.$row_new['user_last_name'].'</b>'.'<br>';

  if (trim_gks($row_old['user_email']) != trim_gks($row_new['user_email'])) 
    $sxolio_log.=gks_lang('email').': <b>'.$row_old['user_email'].'</b> [[-r]] <b>'.$row_new['user_email'].'</b>'.'<br>';

  if (trim_gks($row_old['user_mobile']) != trim_gks($row_new['user_mobile'])) 
    $sxolio_log.=gks_lang('Τηλέφωνο').': <b>'.$row_old['user_mobile'].'</b> [[-r]] <b>'.$row_new['user_mobile'].'</b>'.'<br>';

  if (trim_gks($row_old['lang_name']) != trim_gks($row_new['lang_name'])) 
    $sxolio_log.=gks_lang('Γλώσσα').': <b>'.$row_old['lang_name'].'</b> [[-r]] <b>'.$row_new['lang_name'].'</b>'.'<br>';

  if (trim_gks($row_old['eponimia']) != trim_gks($row_new['eponimia'])) 
    $sxolio_log.=gks_lang('Επωνυμία').': <b>'.$row_old['eponimia'].'</b> [[-r]] <b>'.$row_new['eponimia'].'</b>'.'<br>';

  if (trim_gks($row_old['title']) != trim_gks($row_new['title'])) 
    $sxolio_log.=gks_lang('Τίτλος').': <b>'.$row_old['title'].'</b> [[-r]] <b>'.$row_new['title'].'</b>'.'<br>';



  $ee_initials_old='';
  $sql="select id_country,country_ee,country_name,country_initials 
  FROM gks_country where id_country=".$row_old['ma_country_id']." ORDER BY country_name";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $ee_initials_old=trim_gks($row_select['country_ee']);
  }
  $ee_initials_new='';
  $sql="select id_country,country_ee,country_name,country_initials 
  FROM gks_country where id_country=".$row_new['ma_country_id']." ORDER BY country_name";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $ee_initials_new=trim_gks($row_select['country_ee']);
  }
  
  if (trim_gks($ee_initials_old.$row_old['afm']) != trim_gks($ee_initials_new.$row_new['afm'])) {
    $sxolio_log.=gks_lang('ΑΦΜ').': <b>'.$ee_initials_old.' '.$row_old['afm'].'</b> [[-r]] <b>'.$ee_initials_new.' '.$row_new['afm'].'</b>'.'<br>';
  }

  if (trim_gks($row_old['doy']) != trim_gks($row_new['doy'])) 
    $sxolio_log.=gks_lang('ΔΟΥ').': <b>'.$row_old['doy'].'</b> [[-r]] <b>'.$row_new['doy'].'</b>'.'<br>';

  if (trim_gks($row_old['epaggelma']) != trim_gks($row_new['epaggelma'])) 
    $sxolio_log.=gks_lang('Επάγγελμα').': <b>'.$row_old['epaggelma'].'</b> [[-r]] <b>'.$row_new['epaggelma'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_odos']) != trim_gks($row_new['ma_odos'])) 
    $sxolio_log.=gks_lang('Οδός').': <b>'.$row_old['ma_odos'].'</b> [[-r]] <b>'.$row_new['ma_odos'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_arithmos']) != trim_gks($row_new['ma_arithmos'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['ma_arithmos'].'</b> [[-r]] <b>'.$row_new['ma_arithmos'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_orofos']) != trim_gks($row_new['ma_orofos'])) 
    $sxolio_log.=gks_lang('Όροφος').': <b>'.$row_old['ma_orofos'].'</b> [[-r]] <b>'.$row_new['ma_orofos'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_perioxi']) != trim_gks($row_new['ma_perioxi'])) 
    $sxolio_log.=gks_lang('Περιοχή').': <b>'.$row_old['ma_perioxi'].'</b> [[-r]] <b>'.$row_new['ma_perioxi'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_poli']) != trim_gks($row_new['ma_poli'])) 
    $sxolio_log.=gks_lang('Πόλη').': <b>'.$row_old['ma_poli'].'</b> [[-r]] <b>'.$row_new['ma_poli'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_tk']) != trim_gks($row_new['ma_tk'])) 
    $sxolio_log.=gks_lang('TK').': <b>'.$row_old['ma_tk'].'</b> [[-r]] <b>'.$row_new['ma_tk'].'</b>'.'<br>';

  if (trim_gks($row_old['country_name']) != trim_gks($row_new['country_name'])) 
    $sxolio_log.=gks_lang('Χώρα').': <b>'.$row_old['country_name'].'</b> [[-r]] <b>'.$row_new['country_name'].'</b>'.'<br>';

  if (trim_gks($row_old['nomos_descr']) != trim_gks($row_new['nomos_descr'])) 
    $sxolio_log.=gks_lang('Νομός').': <b>'.$row_old['nomos_descr'].'</b> [[-r]] <b>'.$row_new['nomos_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['address_extra']) != trim_gks($row_new['address_extra'])) {
    
    $temp_old='';
    if ($row_old['address_extra']==-1) $temp_old=gks_lang('Αποστολή στην ίδια διεύθυνση');
    else {
      $sql="select ea_name from gks_users_extra_address where id_users_extra_address=".$row_old['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $row_select = $result_select->fetch_assoc();
        $temp_old=$row_select['ea_name'];
      }
    }
    
    $temp_new='';
    if ($row_new['address_extra']==-1) $temp_new=gks_lang('Αποστολή στην ίδια διεύθυνση');
    else {
      $sql="select ea_name from gks_users_extra_address where id_users_extra_address=".$row_new['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $row_select = $result_select->fetch_assoc();
        $temp_new=$row_select['ea_name'];
      }
    }
    $sxolio_log.=gks_lang('Αποστολή').': <b>'.$temp_old.'</b> [[-r]] <b>'.$temp_new.'</b>'.'<br>';
  }
  
  $check_ea=false;
  if ($row_old['address_extra']==-1 and $row_new['address_extra']>0) $check_ea=true;
  else if ($row_old['address_extra']>0 and $row_new['address_extra']>0) $check_ea=true;
  
  
  if ($check_ea) {
    if (isset($extra_address_old['ea_name'])==false) { //den exei oristei
      $extra_address_old=array('ea_name'=>'', 'ea_phone'=>'','ea_odos'=>'','ea_arithmos'=>'','ea_orofos'=>'','ea_perioxi'=>'','ea_poli'=>'','ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
    }
    
    $extra_address_new=array('ea_name'=>'', 'ea_phone'=>'','ea_odos'=>'','ea_arithmos'=>'','ea_orofos'=>'','ea_perioxi'=>'','ea_poli'=>'','ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
    if ($row_new['address_extra']>0) {
      $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
      FROM (gks_users_extra_address 
      LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
      LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
      WHERE gks_users_extra_address.id_users_extra_address=".$row_new['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $extra_address_new = $result_select->fetch_assoc();
      }    
    }
  
    if (trim_gks($extra_address_old['ea_name']) != trim_gks($extra_address_new['ea_name'])) 
      $sxolio_log.=gks_lang('Όνομα Αποστολής').': <b>'.$extra_address_old['ea_name'].'</b> [[-r]] <b>'.$extra_address_new['ea_name'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_phone']) != trim_gks($extra_address_new['ea_phone'])) 
      $sxolio_log.=gks_lang('Τηλέφωνο Αποστολής').': <b>'.$extra_address_old['ea_phone'].'</b> [[-r]] <b>'.$extra_address_new['ea_phone'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_odos']) != trim_gks($extra_address_new['ea_odos'])) 
      $sxolio_log.=gks_lang('Οδός Αποστολής').': <b>'.$extra_address_old['ea_odos'].'</b> [[-r]] <b>'.$extra_address_new['ea_odos'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_arithmos']) != trim_gks($extra_address_new['ea_arithmos'])) 
      $sxolio_log.=gks_lang('Αριθμός οδού Αποστολής').': <b>'.$extra_address_old['ea_arithmos'].'</b> [[-r]] <b>'.$extra_address_new['ea_arithmos'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_orofos']) != trim_gks($extra_address_new['ea_orofos'])) 
      $sxolio_log.=gks_lang('Όροφος Αποστολής').': <b>'.$extra_address_old['ea_orofos'].'</b> [[-r]] <b>'.$extra_address_new['ea_orofos'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_perioxi']) != trim_gks($extra_address_new['ea_perioxi'])) 
      $sxolio_log.=gks_lang('Περιοχή Αποστολής').': <b>'.$extra_address_old['ea_perioxi'].'</b> [[-r]] <b>'.$extra_address_new['ea_perioxi'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_poli']) != trim_gks($extra_address_new['ea_poli'])) 
      $sxolio_log.=gks_lang('Πόλη Αποστολής').': <b>'.$extra_address_old['ea_poli'].'</b> [[-r]] <b>'.$extra_address_new['ea_poli'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_tk']) != trim_gks($extra_address_new['ea_tk'])) 
      $sxolio_log.=gks_lang('TK Αποστολής').': <b>'.$extra_address_old['ea_tk'].'</b> [[-r]] <b>'.$extra_address_new['ea_tk'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['country_name']) != trim_gks($extra_address_new['country_name'])) 
      $sxolio_log.=gks_lang('Χώρα Αποστολής').': <b>'.$extra_address_old['country_name'].'</b> [[-r]] <b>'.$extra_address_new['country_name'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['nomos_descr']) != trim_gks($extra_address_new['nomos_descr'])) 
      $sxolio_log.=gks_lang('Νομός Αποστολής').': <b>'.$extra_address_old['nomos_descr'].'</b> [[-r]] <b>'.$extra_address_new['nomos_descr'].'</b>'.'<br>';
  }


  
  
  
    
  //compare products_old products_new
  foreach ($products_old as &$pitem_old) {
    $pitem_old['del']=false;
    $pitem_old['k']=-1;
  }
  unset($pitem_old);
  foreach ($products_new as &$pitem_new) {
    $pitem_new['del']=false;
    $pitem_new['k']=-1;
  }
  unset($pitem_new);

  foreach ($products_old as $key_old => &$pitem_old) {
    $found=-1;
    foreach ($products_new as $key_new =>&$pitem_new) {
      if ($pitem_old['id_order_product'] == $pitem_new['id_order_product']) {
        $pitem_old['k'] = $key_new;
        $pitem_new['k'] = $key_old;
        $found = $key_new;
        break;
      }
    }
    unset($pitem_new);
    if ($found==-1) $pitem_old['del']=true;
  }  
  unset($pitem_old);

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/compare.txt',print_r($products_old,true).' '.print_r($products_new,true));

  
  
  
  $sxolio_eidi_log='';
  foreach ($products_old as $pitem_old) {
    if ($pitem_old['del']) {
      $sxolio_eidi_log.=gks_lang('Αφαιρέθηκε το είδος').': <b>'.$pitem_old['product_descr'].'</b><br>';
    }
  }
  
  foreach ($products_new as $key_new =>&$pitem_new) {
    if ($pitem_new['del'] == false and $pitem_new['k'] == -1) {
      $sxolio_eidi_log.=gks_lang('Προστέθηκε το είδος').': <b>'.$pitem_new['product_descr'].'</b> '.
      gks_lang('Ποσότητα').': <b>'.$pitem_new['product_quantity'].'</b> '.
      gks_lang('Αξία').': <b>'.number_format($pitem_new['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).'</b> '.
      ($pitem_new['product_price_ekptosi_pososto'] == 0 ? '' : ' '.gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($pitem_new['product_price_ekptosi_pososto']).'%</b>').
      '<br>';
    }
  }
  
   // $sxolio_log.=gks_lang('Εσωτερική σημείωση για λογιστήριο').':<br><b>'.(isset($row_old['note_logistirio']) ? $row_old['note_logistirio'] : '').'</b> [[-r]] '.
   // '<b>'.(isset($row_new['note_logistirio']) ? $row_new['note_logistirio'] : '').'</b>'.'<br>';
  
  $pn=$products_new;
  foreach ($products_old as $p) {
    if ($p['k']>=0) {
      $item_descr_change='';
      if ($p['product_descr'] != $pn[$p['k']]['product_descr']) 
        $item_descr_change.='<b>'.$p['product_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_descr'].'</b>';
      
      $item_txt='';
      
      
      if ($p['product_set'] != $pn[$p['k']]['product_set']) 
        $item_txt.=gks_lang('Σετ').': <b>'.$p['product_set'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_set'].'</b> ';
      if ($p['product_comments'] != $pn[$p['k']]['product_comments']) 
        $item_txt.=gks_lang('Παρατηρήσεις').': <b>'.$p['product_comments'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_comments'].'</b> ';
      if ($p['product_sheets'] != $pn[$p['k']]['product_sheets']) 
        $item_txt.=gks_lang('Σελίδες').': <b>'.$p['product_sheets'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_sheets'].'</b> ';
      if ($p['product_quantity'] != $pn[$p['k']]['product_quantity']) 
        $item_txt.=gks_lang('Ποσότητα').': <b>'.$p['product_quantity'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_quantity'].'</b> ';
      if ($p['monada_descr'] != $pn[$p['k']]['monada_descr']) 
        $item_txt.=gks_lang('Μονάδα μέτρησης').': <b>'.$p['monada_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['monada_descr'].'</b> ';
      
      if ($p['product_price_ekptosi_pososto'] != $pn[$p['k']]['product_price_ekptosi_pososto']) 
        $item_txt.=gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($p['product_price_ekptosi_pososto']).'%</b> [[-r]] <b>'.myNumberFormatNo0Local($pn[$p['k']]['product_price_ekptosi_pososto']).'%</b> ';
      if ($p['product_price_final_all_net'] != $pn[$p['k']]['product_price_final_all_net']) 
        $item_txt.=gks_lang('Τιμή').': <b>'.myCurrencyFormat($p['product_price_final_all_net'], false).'</b> [[-r]] <b>'.myCurrencyFormat($pn[$p['k']]['product_price_final_all_net'], false).'</b> ';
      
      if ($p['fpa_base_descr']!=$pn[$p['k']]['fpa_base_descr'])
        $item_txt.=gks_lang('ΦΠΑ').': <b>'.$p['fpa_base_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['fpa_base_descr'].'</b> ';
      
      
        
        
        
      if ($item_txt != '') {
        $item_txt=trim_gks($item_txt);
        if ($item_descr_change!='') $item_txt=$item_descr_change.': '.$item_txt;
        else $item_txt=$p['product_descr'].': '.$item_txt;
        
        $sxolio_eidi_log.=$item_txt.'<br>';
        
      }
    }
  }  
  
  $sxolio_log.=$sxolio_eidi_log;


  if ($row_old['gks_price_net'] != $row_new['gks_price_net']) 
    $sxolio_log.=gks_lang('Υποσύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_net']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_net']).'</b>'.'<br>';
  
  if ($row_old['gks_price_fpa'] != $row_new['gks_price_fpa']) 
    $sxolio_log.=gks_lang('ΦΠΑ').': <b>'.myCurrencyFormat($row_old['gks_price_fpa']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_fpa']).'</b>'.'<br>';
  
  if ($row_old['gks_price_netfpa'] != $row_new['gks_price_netfpa']) 
    $sxolio_log.=gks_lang('Μικτό σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_netfpa']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_netfpa']).'</b>'.'<br>';
  
//  if ($row_old['totalWithheldAmount'] != $row_new['totalWithheldAmount']) 
//    $sxolio_log.=gks_lang('Φόροι Παρακρατούμενοι').': <b>'.myCurrencyFormat($row_old['totalWithheldAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalWithheldAmount']).'</b>'.'<br>';
//  
//  if ($row_old['totalOtherTaxesAmount'] != $row_new['totalOtherTaxesAmount']) 
//    $sxolio_log.=gks_lang('Λοιποί Φόροι').': <b>'.myCurrencyFormat($row_old['totalOtherTaxesAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalOtherTaxesAmount']).'</b>'.'<br>';
//  
//  if ($row_old['totalStampDutyamount'] != $row_new['totalStampDutyamount']) 
//    $sxolio_log.=gks_lang('Ψηφιακό Τέλος συναλλαγής').': <b>'.myCurrencyFormat($row_old['totalStampDutyamount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalStampDutyamount']).'</b>'.'<br>';
//  
//  if ($row_old['totalFeesAmount'] != $row_new['totalFeesAmount']) 
//    $sxolio_log.=gks_lang('Τέλη').': <b>'.myCurrencyFormat($row_old['totalFeesAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalFeesAmount']).'</b>'.'<br>';
//  
////  if ($row_old['totalDeductionsAmount'] != $row_new['totalDeductionsAmount']) 
////    $sxolio_log.=gks_lang('Κρατήσεις').': <b>'.myCurrencyFormat($row_old['totalDeductionsAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalDeductionsAmount']).'</b>'.'<br>';

  if (trim_gks($row_old['kostos_apostolis']) != trim_gks($row_new['kostos_apostolis'])) 
    $sxolio_log.=gks_lang('Κόστος αποστολής').': <b>'.myCurrencyFormat($row_old['kostos_apostolis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_apostolis']).'</b>'.'<br>';

  if (trim_gks($row_old['kostos_pliromis']) != trim_gks($row_new['kostos_pliromis'])) 
    $sxolio_log.=gks_lang('Κόστος πληρωμής').': <b>'.myCurrencyFormat($row_old['kostos_pliromis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_pliromis']).'</b>'.'<br>';


  if ($row_old['gks_price_total'] != $row_new['gks_price_total']) 
    $sxolio_log.=gks_lang('Σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_total']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_total']).'</b>'.'<br>';
  

  $pliroteo_old=$row_old['gks_price_total'] + $row_old['kostos_apostolis'] + $row_old['kostos_pliromis'];
  $pliroteo_new=$row_new['gks_price_total'] + $row_new['kostos_apostolis'] + $row_new['kostos_pliromis'];
  if ($pliroteo_old != $pliroteo_new) 
    $sxolio_log.=gks_lang('Πληρωτέο').': <b>'.myCurrencyFormat($pliroteo_old).'</b> [[-r]] '.'<b>'.myCurrencyFormat($pliroteo_new).'</b>'.'<br>';


  if ((isset($row_old['ddate']) and isset($row_new['ddate']) == false) or 
      (isset($row_old['ddate']) == false and isset($row_new['ddate'])) or 
      $row_old['ddate'] != $row_new['ddate']) 
    $sxolio_log.=gks_lang('Ημερομηνία Παράδοσης').': <b>'.(isset($row_old['ddate']) ? showDate(strtotime($row_old['ddate']), 'd/m/Y', 1) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['ddate']) ? showDate(strtotime($row_new['ddate']), 'd/m/Y', 1) : '').'</b>'.'<br>';


  if (trim_gks($row_old['delivery_method_name']) != trim_gks($row_new['delivery_method_name'])) 
    $sxolio_log.=gks_lang('Τρόπος αποστολής').': <b>'.$row_old['delivery_method_name'].'</b> [[-r]] <b>'.$row_new['delivery_method_name'].'</b>'.'<br>';

  $delivery_id_8_old_desc='';
  $delivery_id_8_new_desc='';
  
  if ($row_old['delivery_id_8']>0) {
    $sql="SELECT warehouse_name FROM gks_warehouses
    WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0 and id_warehouse=".$row_old['delivery_id_8'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $row_select = $result_select->fetch_assoc();
      $delivery_id_8_old_desc=$row_select['warehouse_name'];
    }
  }
  if ($row_new['delivery_id_8']>0) {
    $sql="SELECT warehouse_name FROM gks_warehouses
    WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0 and id_warehouse=".$row_new['delivery_id_8'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $row_select = $result_select->fetch_assoc();
      $delivery_id_8_new_desc=$row_select['warehouse_name'];
    }
  }

  if (trim_gks($delivery_id_8_old_desc) != trim_gks($delivery_id_8_new_desc)) 
    $sxolio_log.=gks_lang('Παραλαβή από την επιχείρηση').': <b>'.$delivery_id_8_old_desc.'</b> [[-r]] <b>'.$delivery_id_8_new_desc.'</b>'.'<br>';


  if (trim_gks($row_old['delivery_number']) != trim_gks($row_new['delivery_number'])) 
    $sxolio_log.=gks_lang('Αριθμός Αποστολής').': <b>'.$row_old['delivery_number'].'</b> [[-r]] <b>'.$row_new['delivery_number'].'</b>'.'<br>';

  if (trim_gks($row_old['vehicle_number']) != trim_gks($row_new['vehicle_number'])) 
    $sxolio_log.=gks_lang('Αριθμός Μεταφορικού Μέσου').': <b>'.$row_old['vehicle_number'].'</b> [[-r]] <b>'.$row_new['vehicle_number'].'</b>'.'<br>';

  if (trim_gks($row_old['dispatch_date']) != trim_gks($row_new['dispatch_date'])) 
    $sxolio_log.=gks_lang('Ημέρα Έναρξης Αποστολής').': '.
                 (isset($row_old['dispatch_date']) ? '<b>'.showDate(strtotime($row_old['dispatch_date']), 'd/m/Y', 0).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['dispatch_date']) ? '<b>'.showDate(strtotime($row_new['dispatch_date']), 'd/m/Y', 0).'</b>' : '').
                 '<br>';

  if (trim_gks($row_old['dispatch_time']) != trim_gks($row_new['dispatch_time'])) 
    $sxolio_log.=gks_lang('Ώρα Έναρξης Αποστολής').': '.
                 (isset($row_old['dispatch_time']) ? '<b>'.showDate(strtotime($row_old['dispatch_time']), 'H:i', 0).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['dispatch_time']) ? '<b>'.showDate(strtotime($row_new['dispatch_time']), 'H:i', 0).'</b>' : '').
                 '<br>';

  //if (trim_gks($row_old['aade_skopos_diakinisis_descr']) != trim_gks($row_new['aade_skopos_diakinisis_descr'])) 
  //  $sxolio_log.=gks_lang('Σκοπός διακίνησης').': <b>'.$row_old['aade_skopos_diakinisis_descr'].'</b> [[-r]] <b>'.$row_new['aade_skopos_diakinisis_descr'].'</b>'.'<br>';
  

  if (trim_gks($row_old['payment_acquirer_name']) != trim_gks($row_new['payment_acquirer_name'])) 
    $sxolio_log.=gks_lang('Τρόπος πληρωμής').': <b>'.$row_old['payment_acquirer_name'].'</b> [[-r]] <b>'.$row_new['payment_acquirer_name'].'</b>'.'<br>';

  if (trim_gks($row_old['mdate_expire']) != trim_gks($row_new['mdate_expire'])) 
    $sxolio_log.=gks_lang('Ημερομηνία λήξης προσφοράς').': '.
                 (isset($row_old['mdate_expire']) ? '<b>'.showDate(strtotime($row_old['mdate_expire']), 'd/m/Y H:i', 1).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['mdate_expire']) ? '<b>'.showDate(strtotime($row_new['mdate_expire']), 'd/m/Y H:i', 1).'</b>' : '').
                 '<br>';


  if ((isset($row_old['note_doc']) and isset($row_old['note_doc']) == false) or 
      (isset($row_old['note_doc']) == false and isset($row_old['note_doc'])) or 
      $row_old['note_doc'] != $row_new['note_doc']) 
    $sxolio_log.=gks_lang('Σχόλια παραγγελίας').':<br><b>'.(isset($row_old['note_doc']) ? $row_old['note_doc'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['note_doc']) ? $row_new['note_doc'] : '').'</b>'.'<br>';

  if ((isset($row_old['note_logistirio']) and isset($row_old['note_logistirio']) == false) or 
      (isset($row_old['note_logistirio']) == false and isset($row_old['note_logistirio'])) or 
      $row_old['note_logistirio'] != $row_new['note_logistirio']) 
    $sxolio_log.=gks_lang('Εσωτερική σημείωση για λογιστήριο').':<br><b>'.(isset($row_old['note_logistirio']) ? $row_old['note_logistirio'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['note_logistirio']) ? $row_new['note_logistirio'] : '').'</b>'.'<br>';

  if ((isset($row_old['order_priority']) and isset($row_old['order_priority']) == false) or 
      (isset($row_old['order_priority']) == false and isset($row_old['order_priority'])) or 
      $row_old['order_priority'] != $row_new['order_priority']) 
    $sxolio_log.=gks_lang('Προτεραιότητα').':<br><b>'.(isset($row_old['order_priority']) ? $row_old['order_priority'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['order_priority']) ? $row_new['order_priority'] : '').'</b>'.'<br>';



  if (trim_gks($row_old['note_production']) != trim_gks($row_new['note_production']))
    $sxolio_log.=gks_lang('Εσωτερική σημείωση για παραγωγή').':<br><b>'.(isset($row_old['note_production']) ? nl2br_gks($row_old['note_production']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['note_production']) ? nl2br_gks($row_new['note_production']) : '').'</b>'.'<br>';


  if (intval($row_old['assigned_id']) != intval($row_new['assigned_id']))
    $sxolio_log.=gks_lang('Ανάθεση σε').': <b>'.trim_gks($row_old['gks_nickname_assigned']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['gks_nickname_assigned']).'</b>'.'<br>';

  if ($GKS_CRM_ENABLE) {
    if (intval($row_old['crm_channel_id']) != intval($row_new['crm_channel_id']))
      $sxolio_log.=gks_lang('Κανάλι πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_sale_descr']).'</b> [[-r]] '.
      '<b>'.trim_gks($row_new['crm_channel_sale_descr']).'</b>'.'<br>';
  
    if (intval($row_old['crm_channel_contact_id']) != intval($row_new['crm_channel_contact_id']))
      $sxolio_log.=gks_lang('Επαφή Πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_contact_gks_nickname']).'</b> [[-r]] '.
      '<b>'.trim_gks($row_new['crm_channel_contact_gks_nickname']).'</b>'.'<br>';
  
    if (intval($row_old['crm_channel_campain_id']) != intval($row_new['crm_channel_campain_id']))
      $sxolio_log.=gks_lang('Καμπάνια').': <b>'.trim_gks($row_old['ads_campain_name']).'</b> [[-r]] '.
      '<b>'.trim_gks($row_new['ads_campain_name']).'</b>'.'<br>';
  
    if (trim_gks($row_old['crm_channel_url']) != trim_gks($row_new['crm_channel_url'])) 
      $sxolio_log.=gks_lang('URL').':<br><b>'.(isset($row_old['crm_channel_url']) ? nl2br_gks($row_old['crm_channel_url']) : '').'</b><br>[[-r]]<br>'.
      '<b>'.(isset($row_new['crm_channel_url']) ? nl2br_gks($row_new['crm_channel_url']) : '').'</b>'.'<br>';
  
    if (trim_gks($row_old['crm_channel_code']) != trim_gks($row_new['crm_channel_code'])) 
      $sxolio_log.=gks_lang('Κωδικός CRM').':<br><b>'.(isset($row_old['crm_channel_code']) ? nl2br_gks($row_old['crm_channel_code']) : '').'</b><br>[[-r]]<br>'.
      '<b>'.(isset($row_new['crm_channel_code']) ? nl2br_gks($row_new['crm_channel_code']) : '').'</b>'.'<br>';
  
    if (trim_gks($row_old['crm_channel_text']) != trim_gks($row_new['crm_channel_text'])) 
      $sxolio_log.=gks_lang('Σχόλιο').':<br><b>'.(isset($row_old['crm_channel_text']) ? nl2br_gks($row_old['crm_channel_text']) : '').'</b><br>[[-r]]<br>'.
      '<b>'.(isset($row_new['crm_channel_text']) ? nl2br_gks($row_new['crm_channel_text']) : '').'</b>'.'<br>';
  }
 

  if (intval($row_old['online_enable']) != intval($row_new['online_enable']))
    $sxolio_log.=gks_lang('OnLine Ενεργοποίηση').': <b>'.($row_old['online_enable']==1 ? gks_lang('Ναι') : gks_lang('Όχι')).'</b> [[-r]] '.
    '<b>'.($row_new['online_enable']==1 ? gks_lang('Ναι') : gks_lang('Όχι')).'</b>'.'<br>';
  
  if (trim_gks($row_old['online_password']) != trim_gks($row_new['online_password'])) 
    $sxolio_log.=gks_lang('OnLine Κωδικός').': <b>'.(isset($row_old['online_password']) ? ($row_old['online_password']) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['online_password']) ? ($row_new['online_password']) : '').'</b>'.'<br>';
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_orders',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;

  
  
  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    
    //$return = array('success' => false, 'message' => base64_encode($sql));
    //echo json_encode($return); die();  
     
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
  }
}


function gks_orders_create_acc_inv($old_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if ($old_id<=0) {
    debug_mail(false,'id is zero',$old_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα την παραγγελία')));
    echo json_encode($return); die(); }   
  

  
  $sql="SELECT gks_orders.*, gks_company.company_title, gks_company_subs.company_sub_title
  FROM (gks_orders 
  LEFT JOIN gks_company ON gks_orders.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_orders.company_sub_id = gks_company_subs.id_company_sub
  where id_order=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'gks_orders_create_acc_inv',                  gks_lang('Δεν βρέθηκε η παραγγελία').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η παραγγελία').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();

  $order_state=$old_row['order_state'];
  if ($order_state!='020pending' and $order_state!='055wait_payment' and $order_state!='060registered' and 
      $order_state!='070inproduction' and $order_state!='090indelivery' and $order_state!='095execute' and $order_state!='100completed' and $order_state!='110payment') {
    $message=gks_lang('Η κατάσταση της παραγγελίας είναι').':<br>'.
    '<span class="order_state_'.$order_state.'">'.getOrderStateDescr($order_state).'</span><br>'.
    gks_lang('ενώ θα πρέπει να είναι').':<br>'.
    '<span class="order_state_020pending">'.getOrderStateDescr('020pending').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_055wait_payment">'.getOrderStateDescr('055wait_payment').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_060registered">'.getOrderStateDescr('060registered').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_070inproduction">'.getOrderStateDescr('070inproduction').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_090indelivery">'.getOrderStateDescr('090indelivery').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_095execute">'.getOrderStateDescr('095execute').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_100completed">'.getOrderStateDescr('100completed').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_110payment">'.getOrderStateDescr('110payment').'</span><br>'.
    gks_lang('για να δημιουργηθεί το σχετικό παραστατικό');
    
    debug_mail(false,'gks_orders_create_acc_inv',                  $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  
  //echo '<pre>';echo $old_id;die();
    
  $company_id=$old_row['company_id'];
  $company_title=trim_gks($old_row['company_title']);
  $company_sub_id=$old_row['company_sub_id'];
  $company_sub_title=trim_gks($old_row['company_sub_title']);
  if ($company_sub_title=='') $company_sub_title=gks_lang('Κεντρικό');
  $fiscal_position_id=$old_row['fiscal_position_id'];
  $parastatiko=$old_row['parastatiko'];
  
  $sql_eidi="SELECT gks_eshop_products.product_base_type, Count(gks_eshop_products.id_product) AS cc
  FROM gks_orders_products 
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE gks_orders_products.order_id=".$old_id."
  GROUP BY gks_eshop_products.product_base_type;";
  $result_eidi = $db_link->query($sql_eidi);  
  if (!$result_eidi) {
    debug_mail(false,'error sql',$sql_eidi);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $pbasetypes=array();
  $pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
  $pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //ypiresia
  $total_eidi=0;
  while ($row_eidi= $result_eidi->fetch_assoc()) { 
    $total_eidi+=$row_eidi['cc'];
    if ($row_eidi['product_base_type']==0 or $row_eidi['product_base_type']==1) $pbasetypes[0]['cc']+=$row_eidi['cc'];
    if ($row_eidi['product_base_type']==2) $pbasetypes[2]['cc']+=$row_eidi['cc'];
  }  
  if ($total_eidi==0) {
    debug_mail(false,'total_eidi is zero',$total_eidi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη στην παραγγελία')));
    echo json_encode($return); die(); } 
    
  //print '<pre>';print_r($pbasetypes);die();
  
  //print '<pre>';print $parastatiko.'|'.$fiscal_position_id;die();
  
  $fiscal_position_id_new=$fiscal_position_id;

  
  if ($parastatiko==0) { //apodiji
    if ($pbasetypes[0]['cc']>0) {
      $pbasetypes[0]['id_acc_eidos_parastatikou']=111; //ALP
    }
    if ($pbasetypes[2]['cc']>0) {
      $pbasetypes[2]['id_acc_eidos_parastatikou']=112; //APY  
    }
    switch ($fiscal_position_id) {
      case 1:	break; // Lianikis Esoterikou
      case 2:	break; // Lianikis Endokoinotikes
      case 3:	break; // Lianikis Trites Chores
      case 11:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou
      case 12:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
      case 21:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Meiomeno
      case 22:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
      case 31:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Apallagis
      case 32:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
      case 41:	$fiscal_position_id_new=2; break; //	Chondrikis Endokoinotikes
      case 42:	$fiscal_position_id_new=2; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
      case 51:	$fiscal_position_id_new=3; break; //	Chondrikis Trites Chores
      case 52:	$fiscal_position_id_new=3; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
    }
  
  } else { //timologio
    
    if ($pbasetypes[0]['cc']>0) {
      switch ($fiscal_position_id) {
        case 1:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //Lianikis Esoterikou
        case 2:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=12; break; //Lianikis Endokoinotikes
        case 3:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=13; break; //Lianikis Trites Chores
        case 11: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou
        case 12: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
        case 21: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Meiomeno
        case 22: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
        case 31: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Apallagis
        case 32: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
        case 41: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Endokoinotikes
        case 42: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
        case 51: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Trites Chores
        case 52: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
      }    
    }
    if ($pbasetypes[2]['cc']>0) {
      switch ($fiscal_position_id) {
        case 1:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //Lianikis Esoterikou
        case 2:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //Lianikis Endokoinotikes
        case 3:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //Lianikis Trites Chores
        case 11: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou
        case 12: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
        case 21: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Meiomeno
        case 22: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
        case 31: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Apallagis
        case 32: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
        case 41: $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //	Chondrikis Endokoinotikes
        case 42: $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
        case 51: $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //	Chondrikis Trites Chores
        case 52: $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
      }    

    }    

    switch ($fiscal_position_id) {
      case 1:	$fiscal_position_id_new=11; break; //Lianikis Esoterikou
      case 2:	$fiscal_position_id_new=11; break; //Lianikis Endokoinotikes
      case 3:	$fiscal_position_id_new=51; break; //Lianikis Trites Chores
      case 11:	break; //	Chondrikis Esoterikou
      case 12:	break; //	Chondrikis Esoterikou (syndedemenes ontotites)
      case 21:	break; //	Chondrikis Esoterikou Meiomeno
      case 22:	break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
      case 31:	break; //	Chondrikis Esoterikou Apallagis
      case 32:	break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
      case 41:	break; //	Chondrikis Endokoinotikes
      case 42:	break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
      case 51:	break; //	Chondrikis Trites Chores
      case 52:	break; //	Chondrikis Trites Chores (syndedemenes ontotites)
    }    
  }
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
        $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda
        FROM (gks_acc_journal 
        LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou=".$pb['id_acc_eidos_parastatikou']."
        and gks_acc_journal.id_acc_journal>0
        and gks_acc_journal.is_disable=0
        and gks_acc_seires.id_acc_seira>0
        and gks_acc_seires.is_xeirografi=0
        and gks_acc_seires.is_disable=0
        AND gks_acc_journal.company_id=".$company_id." 
        AND gks_acc_journal.company_sub_id=".$company_sub_id;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows==0) {
          $sql="SELECT eidos_parastatikou_descr FROM gks_acc_eidi_parastatikon WHERE id_acc_eidos_parastatikou=".$db_link->escape_string($pb['id_acc_eidos_parastatikou']);
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          if ($result->num_rows>0) {
            $row=$result->fetch_assoc();
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με τύπο παραστατικού').':<br><b>'.$row['eidos_parastatikou_descr'].'</b>';
          } else {
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με ID τύπου παραστατικού').':<br><b>'.$pb['id_acc_eidos_parastatikou'].'</b>';
          }
        } else {
          $row=$result->fetch_assoc();
          $pbasetypes[$i]['new_inv_acc_journal_id']=$row['id_acc_journal'];
          $pbasetypes[$i]['new_inv_acc_seira_id']=$row['id_acc_seira'];
          $pbasetypes[$i]['new_inv_acc_seira_code']=$row['seira_code'];
          $pbasetypes[$i]['has_esoda']=$row['eidos_parastatikou_has_esoda'];
          $pbasetypes[$i]['has_eksoda']=$row['eidos_parastatikou_has_eksoda'];
          
          
        }
      } else {
        $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε ποιο παραστατικό θα πρέπει να χρησιμοποιηθεί για αυτήν την λειτουργία');
      }
    }
  }
  
  $errors='';
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['error']!='') {
      $errors.=$pb['error'].'<br><br>';
    }
  } 
  if ($errors!='') {
    $errors=substr($errors, 0, strlen($errors)-8);
    debug_mail(false,'errors',                                     $errors);
    $return = array('success' => false, 'message' => base64_encode($errors));
    echo json_encode($return); die();}
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();
  
  //echo $new_inv_acc_journal_id.'|'.$new_inv_acc_seira_id.'|'.$new_inv_acc_seira_code."\n"; die();
  //foreach ($pbasetypes as $i => $pb) {
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
            
        $new_inv_guid=guid_for_acc_inv();
        //echo $new_inv_guid."\n"; die();
        
        $sql="INSERT INTO gks_acc_inv (inv_guid, inv_date, mydate_add, mydate_edit, 
        user_id_add, user_id_edit, myip, 
        inv_acc_journal_id, inv_acc_seira_id, 
        inv_acc_seira_code, inv_state, 
        
        company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
        ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
        address_extra, destination_data_name, destination_data_phone, destination_data_odos,  destination_data_arithmos, destination_data_orofos,destination_data_perioxi, destination_data_poli, destination_data_tk,
        destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, 
        fiscal_position_id, pricelist_id, is_other, other_first_name,
        other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
        other_ma_nomos_id, 
        products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
        kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
        note_logistirio,
        delivery_number,vehicle_number,dispatch_date,
        warehouses_id_from,warehouses_id_to
        )
        SELECT '".$new_inv_guid."' as inv_guid, now() as inv_date, now() as mydate_add, now() as mydate_edit,
        ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
        ".$pbasetypes[$i]['new_inv_acc_journal_id']." as inv_acc_journal_id, ".$pbasetypes[$i]['new_inv_acc_seira_id']." as inv_acc_seira_id, 
        '".$db_link->escape_string($pbasetypes[$i]['new_inv_acc_seira_code'])."' as inv_acc_seira_code, '010draft' as inv_state, 
        
        company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
        ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
        address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
        destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, 
        fiscal_position_id, pricelist_id, is_other, other_first_name,
        other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
        other_ma_nomos_id, 
        products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
        kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
        note_logistirio,
        delivery_number,vehicle_number,dispatch_date,
        warehouses_id_from,warehouses_id_to
        FROM gks_orders
        WHERE id_order=".$old_id;
        
        /*
        idiotites,
        products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
        gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, 
        totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount,
        
        
        idiotites,
        products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
        gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, 
        totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, 0 as totalDeductionsAmount,
        
        */
        
        //echo '<pre>';echo $sql;die();
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_id = $db_link->insert_id;  
        //echo $new_id."\n";die();
        
        
        $sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.warehouses_id_from, gks_acc_inv.warehouses_id_to, 
        gks_acc_journal.acc_eidos_parastatikou_whi_id, gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
        gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros,
        gks_acc_inv.company_id,gks_acc_inv.company_sub_id
        FROM (gks_acc_inv 
        LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        where gks_acc_inv.id_acc_inv=".$new_id;
        
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        $added_row = $result->fetch_assoc();
        $change_warehouses=false;
        if (($added_row['warehouses_id_from']==0 or $added_row['warehouses_id_to']==0) and $added_row['acc_eidos_parastatikou_whi_id']>0) {
          
          $whi_eidos_parastatikou_type_id_org=intval($added_row['eidos_parastatikou_type_id']);
          $whi_eidos_parastatikou_stock_pros_org=intval($added_row['eidos_parastatikou_stock_pros']);
          $warehouses_id_from=$added_row['warehouses_id_from'];
          $warehouses_id_to=  $added_row['warehouses_id_to'];
          if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
            $warehouses_id_from=0;  
        //    $aade_skopos_diakinisis_id=0;
        //    $pricelist_id=0;
        //    $fiscal_position_id=0;
        //    $tropos_apostolis=1; //no need to send
            
          } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
            
          } else {
            if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
              if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
                $warehouses_id_from=1; //virtual warehouse pelates
                $warehouses_id_from_is_virtual=true;
              } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
                $warehouses_id_from=2; //virtual warehouse promitheutes
                $warehouses_id_from_is_virtual=true;
              }
            } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
              if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
                $warehouses_id_to=1; //virtual warehouse pelates
                $warehouses_id_to_is_virtual=true;
              } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
                $warehouses_id_to=2; //virtual warehouse promitheutes
                $warehouses_id_to_is_virtual=true;
              }
            }
          }
          
          //echo '<pre>'.$sql."\n".$warehouses_id_from."\n".$warehouses_id_to;die();
          
          if ($warehouses_id_from==0 or $warehouses_id_to==0) {
            $sql="select id_warehouse from gks_warehouses where is_virtual=0 and warehouse_disable=0
            and company_id=".$added_row['company_id']."
            and company_sub_id=".$added_row['company_sub_id']."
            order by warehouse_sortorder limit 1";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }  
            if ($result->num_rows==1) {
              $row = $result->fetch_assoc();
              if ($warehouses_id_from==0) $warehouses_id_from=$row['id_warehouse'];
              if ($warehouses_id_to==0)   $warehouses_id_to=$row['id_warehouse'];
              $change_warehouses=true;
              
              $sql="update gks_acc_inv set 
              warehouses_id_from=".$warehouses_id_from.",
              warehouses_id_to=".$warehouses_id_to."
              where gks_acc_inv.id_acc_inv=".$new_id;
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
              
            }
          }
            
          //echo '<pre>'.$sql."\n".$warehouses_id_from."\n".$warehouses_id_to;die();
          
          
          //$return = array('success' => false, 'message' => base64_encode('|'.$change_warehouses.'|'.$warehouses_id_from.'|'.$warehouses_id_to.'|'));
          //echo json_encode($return); die();
          
        }
        
        
        
        $sql="select id_order_product,product_base_type,id_product,product_parent_id,product_class
        from gks_orders_products 
        LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
        where order_id=".$old_id." order by id_order_product";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        $old_product_ids=array();
        while ($row = $result->fetch_assoc()) {  
          $old_product_ids[]=array(
            'id' => $row['id_order_product'], 
            'type' => $row['product_base_type'],
            'id_product' => $row['id_product'],
            'product_parent_id' => $row['product_parent_id'],
            'product_class' => $row['product_class'],
          );
        }
        
        $map_products=array();
        foreach ($old_product_ids as $vid) {
          if ( (($vid['type']==0 or $vid['type']==1) and ($i==0 or $i==1)) or 
               ($vid['type']==2 and $i==2)    ) {
          
            $sql="INSERT INTO gks_acc_inv_products ( 
            mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
            acc_inv_id, 
            product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
            product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
            product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
            product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_check_fpa,product_price_include_vat, product_price_start_peritem_db,
            product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
            product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
            product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
            product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
            product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
            product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
            product_deductionsAmount, aade_lineComments,
            p_warehouses_id_from,p_warehouses_id_to
            )
            SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
            '".$db_link->escape_string($gkIP)."' as myip,
            ".$new_id." as acc_inv_id,
            product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
            product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
            product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
            product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_check_fpa,product_price_include_vat, product_price_start_peritem_db,
            product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
            product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
            product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
            product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
            product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
            product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
            0 as product_deductionsAmount, '' as aade_lineComments,
            p_warehouses_id_from,p_warehouses_id_to
            FROM gks_orders_products
            where id_order_product=".$vid['id'];
            
            
            //echo '<pre>';echo $sql;die();
            
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }  
            

            
            $new_product_id = $db_link->insert_id;  
            $map_products[]=array(
              'old' => $vid['id'], 
              'new' => $new_product_id,
              'type' => $vid['type'],
              'id_product' => $vid['id_product'],
              'product_parent_id' => $vid['product_parent_id'],
              'product_class' => $vid['product_class'],
            );
          }
        }
        
        if ($change_warehouses) {
          $sql="update gks_acc_inv_products set 
          p_warehouses_id_from=".$warehouses_id_from.",
          p_warehouses_id_to=".$warehouses_id_to."
          where gks_acc_inv_products.acc_inv_id=".$new_id;
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
        }
        
        //echo '<pre>'.print_r($map_products,true)."\n";die();
        
        $sql="UPDATE gks_acc_inv LEFT JOIN (
          
          SELECT 
          ".$new_id." as nv_id_acc_inv,
          Sum(product_quantity) AS nv_products_posotita,
          Sum(product_varos*product_quantity) AS nv_products_varos,
          Sum(product_ogos_x*product_ogos_y*product_ogos_z*product_quantity) AS nv_products_ogos,
          Max(product_ogos_x) AS nv_products_ogos_max_x,
          Max(product_ogos_y) AS nv_products_ogos_max_y,
          Max(product_ogos_z*product_quantity) AS nv_products_ogos_max_z,
          Sum(product_price_start_all_net) as nv_gks_price_original_net,
          sum(product_price_final_all_net) as nv_gks_price_net,
          sum(product_price_final_all_fpa) as nv_gks_price_fpa,
          sum(product_price_final_all_net+product_price_final_all_fpa) as nv_gks_price_netfpa,
          
          sum(product_withheldAmount) as nv_totalWithheldAmount,
          sum(product_otherTaxesAmount) as nv_totalOtherTaxesAmount,
          sum(product_stampDutyAmount) as nv_totalStampDutyamount,
          sum(product_feesAmount) as nv_totalFeesAmount,
          sum(product_deductionsAmount) as nv_totalDeductionsAmount
          FROM gks_acc_inv_products
          WHERE acc_inv_id=".$new_id."        
        ) AS sum_vals ON gks_acc_inv.id_acc_inv = sum_vals.nv_id_acc_inv 
        SET 
        products_posotita = nv_products_posotita,
        products_varos=nv_products_varos,
        products_ogos=nv_products_ogos,
        products_ogos_max_x=nv_products_ogos_max_x,
        products_ogos_max_y=nv_products_ogos_max_y,
        products_ogos_max_z=nv_products_ogos_max_z,
        gks_price_original_net=nv_gks_price_original_net,
        gks_price_net=nv_gks_price_net,
        gks_price_fpa=nv_gks_price_fpa,
        gks_price_netfpa=nv_gks_price_netfpa,
        totalWithheldAmount=nv_totalWithheldAmount,
        totalOtherTaxesAmount=nv_totalOtherTaxesAmount,
        totalStampDutyamount=nv_totalStampDutyamount,
        totalFeesAmount=nv_totalFeesAmount,
        totalDeductionsAmount=nv_totalDeductionsAmount
        WHERE id_acc_inv=".$new_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        
        $myarray_new=array();
        $myarray_line_new=array();        
        $idiotites_new=get_acc_inv_details_txt($new_id, $myarray_new, $myarray_line_new); 


        $sql="UPDATE gks_acc_inv SET 
        idiotites='".$db_link->escape_string(json_encode($myarray_new))."',
        gks_price_total = gks_price_net 
                        + gks_price_fpa
                        - totalWithheldAmount
                        + totalOtherTaxesAmount
                        + totalStampDutyamount
                        + totalFeesAmount
                        - totalDeductionsAmount
        WHERE id_acc_inv=".$new_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        


        
        
        foreach ($map_products as $map_product) {
          if ($pb['has_esoda']!=0) {
            $sql="SELECT product_price_final_all_net
            FROM gks_acc_inv_products
            WHERE id_acc_inv_product=".$map_product['new'];
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
            $row = $result->fetch_assoc();
            $product_price_final_all_net=floatval($row['product_price_final_all_net']);
            

            $xarakt_product_id=$map_product['id_product'];
            if ($map_product['product_class']=='variable_item') {
              $xarakt_product_id=$map_product['product_parent_id'];
            }
            $sql="SELECT aade_typos_xarakt_esodon_id AS typos_id, 
            aade_katigoria_xarakt_esodon_id AS cat_id, 
            acc_inv_product_income_pososto AS pososto
            FROM gks_eshop_products_income
            WHERE product_id=".$xarakt_product_id."
            ORDER BY id_product_income;";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
                          
            $final_all_net=0;
            $out_xarakt_esoda=array();
            $poso_sum=0;
            while ($row = $result->fetch_assoc()) {
              $final_all_net=$product_price_final_all_net; 
              if (empty($row['typos_id']) == false or empty($row['cat_id'])==false) {
                $poso=round(floatval($row['pososto'])/100 * $final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                $poso_sum+=$poso;
                $out_xarakt_esoda[]=array(
                  'typos_id'=> intval($row['typos_id']),
                  'cat_id'=> intval($row['cat_id']),
                  'pososto'=> floatval($row['pososto']),
                  'poso' => $poso,
                );
              }
            }
            $diafora=$final_all_net-$poso_sum;
            if ($diafora!=0 and count($out_xarakt_esoda)>0) $out_xarakt_esoda[count($out_xarakt_esoda)-1]['poso']+=round($diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            
            foreach ($out_xarakt_esoda as $val) {
              $sql="insert into gks_acc_inv_products_income (
              acc_inv_product_id,aade_typos_xarakt_esodon_id,aade_katigoria_xarakt_esodon_id,acc_inv_product_income_ammount
              ) values (
              ".$map_product['new'].",
              ".$val['typos_id'].",
              ".$val['cat_id'].",
              ".number_format($val['poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
            }
            //print '<pre>';print_r($map_products);print_r($out_xarakt_esoda);print $final_all_net.'|'.$diafora;die();          
          }
          
          if ($pb['has_eksoda']!=0) {
            $sql="SELECT product_price_final_all_net
            FROM gks_acc_inv_products
            WHERE id_acc_inv_product=".$map_product['new'];
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
            $row = $result->fetch_assoc();
            $product_price_final_all_net=floatval($row['product_price_final_all_net']);
            
            $xarakt_product_id=$map_product['id_product'];
            if ($map_product['product_class']=='variable_item') {
              $xarakt_product_id=$map_product['product_parent_id'];
            }
            $sql="SELECT aade_typos_xarakt_eksodon_id AS typos_id, 
            aade_katigoria_xarakt_eksodon_id AS cat_id, 
            acc_inv_product_expenses_pososto AS pososto
            FROM gks_eshop_products_expenses
            WHERE product_id=".$xarakt_product_id."
            ORDER BY id_product_expenses;";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
                          
            $final_all_net=0;
            $out_xarakt_eksoda=array();
            $poso_sum=0;
            while ($row = $result->fetch_assoc()) {
              $final_all_net=$product_price_final_all_net;
              if (empty($row['typos_id']) == false or empty($row['cat_id'])==false) {
                $poso=round(floatval($row['pososto'])/100 * $final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                $poso_sum+=$poso;
                $out_xarakt_eksoda[]=array(
                  'typos_id'=> intval($row['typos_id']),
                  'cat_id'=> intval($row['cat_id']),
                  'pososto'=> floatval($row['pososto']),
                  'poso' => $poso,
                );
              }
            }
            $diafora=$final_all_net-$poso_sum;
            if ($diafora!=0 and count($out_xarakt_eksoda)>0) $out_xarakt_eksoda[count($out_xarakt_eksoda)-1]['poso']+=round($diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            
            foreach ($out_xarakt_eksoda as $val) {
              $sql="insert into gks_acc_inv_products_expenses (
              acc_inv_product_id,aade_typos_xarakt_eksodon_id,aade_katigoria_xarakt_eksodon_id,acc_inv_product_expenses_ammount
              ) values (
              ".$map_product['new'].",
              ".$val['typos_id'].",
              ".$val['cat_id'].",
              ".number_format($val['poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
            }
            //print '<pre>';print_r($map_products);print_r($out_xarakt_eksoda);print $final_all_net.'|'.$diafora;die();          


           
          }
        }
        
        $sxolio=gks_lang('Προσθήκη από backend, δημιουργία από παραγγελία με ID').' #<a href="admin-orders-item.php?id='.$old_id.'">'.$old_id.'</a>'; 
        $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
        ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
     
        
        $pbasetypes[$i]['new_id']=$new_id;
        
        $sql="insert into gks_object_rel (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name1,object_id1,object_name2,object_id2
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_orders',".$old_id.",'gks_acc_inv',".$new_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        
      }
    } 
  }
  
  $ret=array();
  foreach ($pbasetypes as $i => $pb) {
    if (isset($pb['new_id']) and $pb['new_id']>0) $ret[]=$pb['new_id'];
  } 
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  return $ret;
  
}
function gks_orders_create_acc_pay($old_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if ($old_id<=0) {
    debug_mail(false,'id is zero',$old_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα την παραγγελία')));
    echo json_encode($return); die(); }   
  
  

  
  $sql="SELECT gks_orders.*, gks_company.company_title, gks_company_subs.company_sub_title,gks_payment_acquirers.payment_acquirer_name,gks_payment_acquirers.show_acc_pay
  FROM ((gks_orders 
  LEFT JOIN gks_company ON gks_orders.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_orders.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  where id_order=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'gks_orders_create_acc_pay',                  gks_lang('Δεν βρέθηκε η παραγγελία').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η παραγγελία').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();

  $order_state=$old_row['order_state'];
  if ($order_state!='025offer' and $order_state!='055wait_payment' and $order_state!='060registered' and 
      $order_state!='070inproduction' and $order_state!='090indelivery' and $order_state!='095execute' and $order_state!='100completed' and $order_state!='110payment') {
    $message=gks_lang('Η κατάσταση της παραγγελίας είναι').':<br>'.
    '<span class="order_state_'.$order_state.'">'.getOrderStateDescr($order_state).'</span><br>'.
    gks_lang('ενώ θα πρέπει να είναι').':<br>'.
    '<span class="order_state_025offer">'.getOrderStateDescr('025offer').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_055wait_payment">'.getOrderStateDescr('055wait_payment').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_060registered">'.getOrderStateDescr('060registered').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_070inproduction">'.getOrderStateDescr('070inproduction').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_090indelivery">'.getOrderStateDescr('090indelivery').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_095execute">'.getOrderStateDescr('095execute').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_100completed">'.getOrderStateDescr('100completed').'</span> '.gks_lang('ή').'<br>'.
    '<span class="order_state_110payment">'.getOrderStateDescr('110payment').'</span><br>'.
    gks_lang('για να δημιουργηθεί η σχετική πληρωμή');
    
    debug_mail(false,'gks_orders_create_acc_pay',                  $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  
  //echo '<pre>';echo $old_id;die();
    
  $company_id=$old_row['company_id'];
  $company_title=trim_gks($old_row['company_title']);
  $company_sub_id=$old_row['company_sub_id'];
  $company_sub_title=trim_gks($old_row['company_sub_title']);
  if ($company_sub_title=='') $company_sub_title=gks_lang('Κεντρικό');
  $fiscal_position_id=$old_row['fiscal_position_id'];
  $parastatiko=$old_row['parastatiko'];
  $tropos_pliromis=intval($old_row['tropos_pliromis']);
  $payment_acquirer_name=trim_gks($old_row['payment_acquirer_name']);
  $gks_price_total=$old_row['gks_price_total'];
  $affect_balance_poso=$old_row['affect_balance_poso'];
  $show_acc_pay=$old_row['show_acc_pay'];
  
  if ($show_acc_pay==0) {
    $tropos_pliromis=9;
    $payment_acquirer_name=gks_lang('Μετρητά');
    
  }
  
  
  $sql_eidi="SELECT gks_eshop_products.product_base_type, Count(gks_eshop_products.id_product) AS cc
  FROM gks_orders_products 
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE gks_orders_products.order_id=".$old_id."
  GROUP BY gks_eshop_products.product_base_type;";
  $result_eidi = $db_link->query($sql_eidi);  
  if (!$result_eidi) {
    debug_mail(false,'error sql',$sql_eidi);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $pbasetypes=array();
  $pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
  //$pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //ypiresia
  $total_eidi=0;
  while ($row_eidi= $result_eidi->fetch_assoc()) { 
    $total_eidi+=$row_eidi['cc'];
    $pbasetypes[0]['cc']+=$row_eidi['cc'];
  }  
  if ($total_eidi==0) {
    debug_mail(false,'total_eidi is zero',$total_eidi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη στην παραγγελία')));
    echo json_encode($return); die(); } 
    
  //print '<pre>';print_r($pbasetypes);die();
  
  //print '<pre>';print $parastatiko.'|'.$fiscal_position_id;die();
  
  

  $pbasetypes[0]['id_acc_eidos_parastatikou']=802; //Eispraxeis apo pelates
  
  
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
        $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda
        FROM (gks_acc_journal 
        LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou=".$pb['id_acc_eidos_parastatikou']."
        and gks_acc_journal.id_acc_journal>0
        and gks_acc_journal.is_disable=0
        and gks_acc_seires.id_acc_seira>0
        and gks_acc_seires.is_xeirografi=0
        and gks_acc_seires.is_disable=0
        AND gks_acc_journal.company_id=".$company_id." 
        AND gks_acc_journal.company_sub_id=".$company_sub_id;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows==0) {
          $sql="SELECT eidos_parastatikou_descr FROM gks_acc_eidi_parastatikon WHERE id_acc_eidos_parastatikou=".$db_link->escape_string($pb['id_acc_eidos_parastatikou']);
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          if ($result->num_rows>0) {
            $row=$result->fetch_assoc();
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με τύπο παραστατικού').':<br><b>'.$row['eidos_parastatikou_descr'].'</b>';
          } else {
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με ID τύπου παραστατικού').':<br><b>'.$pb['id_acc_eidos_parastatikou'].'</b>';
          }
        } else {
          $row=$result->fetch_assoc();
          $pbasetypes[$i]['new_pay_acc_journal_id']=$row['id_acc_journal'];
          $pbasetypes[$i]['new_pay_acc_seira_id']=$row['id_acc_seira'];
          $pbasetypes[$i]['new_pay_acc_seira_code']=$row['seira_code'];
          $pbasetypes[$i]['has_esoda']=$row['eidos_parastatikou_has_esoda'];
          $pbasetypes[$i]['has_eksoda']=$row['eidos_parastatikou_has_eksoda'];
          
          
        }
      } else {
        $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε ποιο ημερολόγιο θα πρέπει να χρησιμοποιηθεί για αυτήν την λειτουργία');
      }
    }
  }
  
  $errors='';
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['error']!='') {
      $errors.=$pb['error'].'<br><br>';
    }
  } 
  if ($errors!='') {
    $errors=substr($errors, 0, strlen($errors)-8);
    debug_mail(false,'errors',                                     $errors);
    $return = array('success' => false, 'message' => base64_encode($errors));
    echo json_encode($return); die();}
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();
  

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
            
        $new_pay_guid=guid_for_acc_pay();
        //echo $new_pay_guid."\n"; die();
        
        $pay_poso=array();
        $pay_poso[]=array(
          'i'=>$old_id,
          'f'=>'order',
          'v'=>$affect_balance_poso,
        );
        $pay_poso_str=serialize($pay_poso);
        
        $sql="INSERT INTO gks_acc_pay (pay_guid, pay_date, mydate_add, mydate_edit, 
        user_id_add, user_id_edit, myip, 
        pay_acc_journal_id, pay_acc_seira_id, 
        pay_acc_seira_code, pay_state, 
        
        company_id, company_sub_id, user_id,user_notes,note_logistirio,
        affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_poso,
        gks_price_total,pay_poso_str
        )
        SELECT '".$new_pay_guid."' as pay_guid, now() as pay_date, now() as mydate_add, now() as mydate_edit,
        ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
        ".$pbasetypes[$i]['new_pay_acc_journal_id']." as pay_acc_journal_id, ".$pbasetypes[$i]['new_pay_acc_seira_id']." as pay_acc_seira_id, 
        '".$db_link->escape_string($pbasetypes[$i]['new_pay_acc_seira_code'])."' as pay_acc_seira_code, '010draft' as pay_state, 
        
        company_id, company_sub_id, user_id,user_notes,note_logistirio,
        1 as affect_balance,1 as affect_balance_all_poso,'price_total' as affect_balance_all_poso_type,affect_balance_poso,
        ".number_format($affect_balance_poso,10, '.','').",
        '".$db_link->escape_string($pay_poso_str)."'
        FROM gks_orders
        WHERE id_order=".$old_id;
        
         
        
        //echo '<pre>';echo $sql;die();
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_id = $db_link->insert_id;  
        //echo $new_id."\n";die();
        
        

        $sql="insert into gks_acc_pay_method (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          acc_pay_id,paymethod_aa,paymethod_id,
          paymethod_total,paymethod_descr,paymethod_comments
        ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          ".$new_id.",
          1,
          ".$tropos_pliromis.",
          ".number_format($affect_balance_poso,10, '.','').",
          '".$db_link->escape_string($payment_acquirer_name)."',
          ''
        )";
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_product_id = $db_link->insert_id;
        
                    //echo print_r($map_products,true)."\n";die();
        
        
        
        
        
        
        $sxolio=gks_lang('Προσθήκη από backend, δημιουργία από παραγγελία με ID').' #<a href="admin-orders-item.php?id='.$old_id.'">'.$old_id.'</a>'; 
        $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
        ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
     
        
        $pbasetypes[$i]['new_id']=$new_id;
        
        $sql="insert into gks_object_rel (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name1,object_id1,object_name2,object_id2
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_orders',".$old_id.",'gks_acc_pay',".$new_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        
      }
    } 
  }
  
  $ret=array();
  foreach ($pbasetypes as $i => $pb) {
    if (isset($pb['new_id']) and $pb['new_id']>0) $ret[]=$pb['new_id'];
  } 
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  return $ret;
  
}



function gks_order_to_draft($id) {
  
  global $db_link;


  //die('<pre>ssss');
  
  $sql="select * from gks_orders where id_order=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_order = $result->fetch_assoc();
  $order_seira_id=$row_order['order_seira_id'];
  $order_number_int_old=$row_order['order_number_int'];
  
  $sql="select * from gks_acc_seires where id_acc_seira=".$order_seira_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_seira = $result->fetch_assoc();
  $prev_number=$row_seira['next_number']-$row_seira['number_step'];
  
  
  $warning_message='';
  if ($prev_number!=$order_number_int_old) {
    $warning_message=
          gks_lang('Επόμενος αριθμός σειράς').': <b>'.$row_seira['next_number'].'</b><br>'.
          gks_lang('Βήμα σειράς').': <b>'.$row_seira['number_step'].'</b><br>'.
          gks_lang('Τρέχον αριθμός παραστατικού').': <b>'.$order_number_int_old.'</b> (<>'.
          $row_seira['next_number'].'-'.$row_seira['number_step'].')';
          
    debug_mail(false,'prev_number is not equal order_number_int_old',$prev_number.' != '.$order_number_int_old.' '.$warning_message);

  } else {  
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="update gks_acc_seires set next_number=next_number-number_step where id_acc_seira=".$order_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="unlock tables;";       
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
      
    $sql_auto_number="update gks_acc_seires_auto_numbers set disabled_date = now()
    where acc_seira_id=".$order_seira_id." and order_id=".$id." and disabled_date is null";
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    

  }
  //$return = array('success' => false, 'message' => base64_encode('sssssss'));
  //echo json_encode($return); die();
  
  if ($prev_number==$order_number_int_old) {
    $sql="update gks_orders set order_state='010draft', order_number_int=0, order_number_str=null,order_ekdosi_date=null where id_order=".$id;
  } else {
    $sql="update gks_orders set order_state='010draft' where id_order=".$id;
  }
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
  return $warning_message;
}


function gks_order_get_ekdosi_numbers() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $order_number_int_old;
  global $order_number_int_new;
  global $order_number_str_new;
  global $order_seira_code_new;
  global $order_seira_id;
  global $has_ekdosi;
  global $save_but_message;
  global $id;
  global $order_state;
  
  //die('<pre>order_number_int_old:'.$order_number_int_old);
  if ($order_number_int_old>0) {
    $sql_auto_number="select auto_number from gks_acc_seires_auto_numbers where disabled_date is null and acc_seira_id=".$order_seira_id." and order_id=".$id;
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_auto_number->num_rows>=1) {
      $row_auto_number = $result_auto_number->fetch_assoc();    
      $order_number_int_old=$row_auto_number['auto_number'];
      $order_number_int_new=$row_auto_number['auto_number'];

      $sql="select * from gks_acc_seires where id_acc_seira=".$order_seira_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $row_seira = $result->fetch_assoc();
      $order_seira_code_new=trim_gks($row_seira['seira_code']);
      $seires_prefix=trim_gks($row_seira['prefix']);
      $seires_suffix=trim_gks($row_seira['suffix']);
      $seires_number_size=$row_seira['number_size'];
      $order_number_str_new=$seires_prefix.str_pad($order_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
      $has_ekdosi=true;
    }
  }
  
  if ($order_number_int_old==0) {
    $order_state='';
    
    
    
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select * from gks_acc_seires where id_acc_seira=".$order_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $row_seira = $result->fetch_assoc();
    $order_seira_code_new=trim_gks($row_seira['seira_code']);
    $seires_prefix=trim_gks($row_seira['prefix']);
    $seires_suffix=trim_gks($row_seira['suffix']);
    $seires_number_size=$row_seira['number_size'];
    $order_number_int_new=$row_seira['next_number'];
    $order_number_str_new=$seires_prefix.str_pad($order_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
    //$save_but_message='<pre>'.$order_number_str_new;
    
    $sql="update gks_acc_seires set next_number=next_number+number_step where id_acc_seira=".$order_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="unlock tables;";       
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  
    $order_state='060registered';
    $has_ekdosi=true;
    if ($save_but_message!='') {
      $save_but_message=gks_lang('Η παραγγελία έχει αποθηκευτεί αλλά δεν έχει εκδοθεί διότι').':<br>'.$save_but_message;
    }
    
    $sql="insert into gks_acc_seires_auto_numbers (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_seira_id,order_id,auto_number
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$order_seira_id.",".$id.",".$order_number_int_new."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
}


