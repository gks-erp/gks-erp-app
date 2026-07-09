<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_erp_sales_order_online_run($id_eshop) {
/*
Array
(
    [url] => https://test.easyfilesselection.com/my/api.v1.php
    [rnd] => 7382817-6594327-6536155-3674383-7637493
    [token] => 2599cc3f436b69cc9cd503711b3dd82b
    [online_order_guid] => efba64cc9e97d78196d1bc7b1cfb4a57
    [action] => get_html
    [lang] => el-GR
)
*/
  
  $guid=''; if (isset($_POST['online_order_guid'])) $guid=trim($_POST['online_order_guid']);
  if (strlen($guid)!=32) {echo '<div class="gks_message_error">'.gks_lang('Δεν έχει ορισθεί το guid').'</div>'; die();}

  $action=''; if (isset($_POST['action'])) $action=trim($_POST['action']);
  if (strlen($action)=='') {echo '<div class="gks_message_error">'.gks_lang('Δεν έχει ορισθεί το action').'</div>';die();}
  
  switch ($action) {   
    case 'get_html':
      gks_erp_sales_order_online_get_html($guid,$id_eshop);
      break;  
    case 'wp_ajax':
      gks_erp_sales_order_online_wp_ajax($guid);
      break;
    default:
      $return = array('success' => false, 'message' =>gks_lang('Δεν έχει υλοποιηθεί το action').' '.$action);
      echo json_encode($return);die();
      
  }
  die();
  //echo '<pre>';print_r($_POST);die();
}


function gks_erp_sales_order_online_sql_order($guid) {
  global $db_link;
  $sql="SELECT gks_orders.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
  ".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
  gks_company.company_title,
  company_tagline,
  company_epaggelma,
  company_phone,
  company_email,
  company_url,
  company_odos,
  company_arithmos,
  company_orofos,
  company_perioxi,
  company_poli,
  company_tk,
  company_nomos_id,gks_nomoi_company.nomos_descr as nomos_descr_company,
  company_country_id,gks_country_company.country_name as country_name_company,
  
  company_eponimia,
  company_afm,
  company_doy,
  
  gks_company_subs.company_sub_title,
  company_sub_tagline,
  company_sub_eponimia,
  company_sub_phone,
  company_sub_email,
  company_sub_url,
  company_sub_odos,
  company_sub_arithmos,
  company_sub_orofos,
  company_sub_perioxi,
  company_sub_poli,
  company_sub_tk,
  company_sub_nomos_id,gks_nomoi_company_sub.nomos_descr as nomos_descr_company_sub,
  company_sub_country_id,gks_country_company_sub.country_name as country_name_company_sub,
  
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
  ".GKS_WP_TABLE_PREFIX."users_assigned.display_name AS display_name_assigned, 
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_wsl_current_user_image as gks_wsl_current_user_image_assigned,
  gksusers_assigned.job_title as job_title_assigned,
  gks_crm_channel_sale.crm_channel_sale_descr, 
  ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
  gks_ads_campain.ads_campain_name,
  gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to,
  gks_prod_warehouses_from.warehouse_name AS prod_warehouse_name_from, gks_prod_warehouses_to.warehouse_name AS prod_warehouse_name_to,
  
  gks_template_html.orders_online_url,
  gks_template_html.orders_online_sms_sender,
  gks_template_html.html_part_1,
  gks_template_html.html_part_2,
  gks_template_html.html_part_3,
  gks_template_html.html_part_4,
  gks_template_html.html_part_5,
  gks_template_html.html_part_6,
  custom_css,
  custom_javascript

  
  FROM ((((((((((((((((((((((((((((((((gks_orders 
  LEFT JOIN gks_template_html on gks_orders.online_template_html_id=gks_template_html.id_template_html)
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
  LEFT JOIN gks_country as gks_country_company ON gks_company.company_country_id = gks_country_company.id_country) 
  LEFT JOIN gks_country as gks_country_company_sub ON gks_company_subs.company_sub_country_id = gks_country_company_sub.id_country) 
  LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_nomoi ON gks_orders.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_nomoi as gks_nomoi_company ON gks_company.company_nomos_id = gks_nomoi_company.id_nomos)
  LEFT JOIN gks_nomoi as gks_nomoi_company_sub ON gks_company_subs.company_sub_nomos_id = gks_nomoi_company_sub.id_nomos)
  LEFT JOIN gks_lang ON gks_orders.user_lang = gks_lang.id_lang)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_orders.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
  LEFT JOIN gks_users AS gksusers_assigned ON gks_orders.assigned_id = gksusers_assigned.user_id) 
  LEFT JOIN gks_crm_channel_sale ON gks_orders.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_orders.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
  LEFT JOIN gks_ads_campain ON gks_orders.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
  LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_orders.warehouses_id_from = gks_warehouses_from.id_warehouse) 
  LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_orders.warehouses_id_to = gks_warehouses_to.id_warehouse)
  LEFT JOIN gks_warehouses AS gks_prod_warehouses_from ON gks_orders.prod_warehouses_id_from = gks_prod_warehouses_from.id_warehouse) 
  LEFT JOIN gks_warehouses AS gks_prod_warehouses_to ON gks_orders.prod_warehouses_id_to = gks_prod_warehouses_to.id_warehouse
  
  where gks_orders.order_guid='".$db_link->escape_string($guid)."'
  and gks_orders.online_enable=1"; 
  return $sql;
}
function gks_erp_sales_order_online_sql_order_products($id_order) {
  $sql="SELECT gks_orders_products.*, 
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
  
  WHERE gks_orders_products.order_id=".$id_order."
  ORDER BY gks_orders_products.product_aa;"; 
  return $sql; 
}
function gks_erp_sales_order_online_get_html($guid,$id_eshop) {
  global $db_link;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $gks_cache_version;
 
  $is_employee=false;
  if (isset($_POST['cwp_user_roles']) and is_array($_POST['cwp_user_roles']) and
      (in_array('employee',$_POST['cwp_user_roles']) or 
       in_array('administrator',$_POST['cwp_user_roles']) or 
       in_array('adminmy',$_POST['cwp_user_roles']))) {
    $is_employee=true;  
  }
  $cwp_user_id=0;if (isset($_POST['cwp_user_id'])) $cwp_user_id=intval($_POST['cwp_user_id']);
  $cwp_user_display_name=0;if (isset($_POST['cwp_user_display_name'])) $cwp_user_display_name=trim_gks($_POST['cwp_user_display_name']);
  $cwp_url=0;if (isset($_POST['cwp_url'])) $cwp_url=trim_gks($_POST['cwp_url']);

  $cwp_is_erp_user=false;
  if ($is_employee and $cwp_user_id>0 and $cwp_url.'/'==GKS_SITE_URL) {
    $cwp_is_erp_user=true;
  }
  $apantisi_os='';
  if ($cwp_is_erp_user) $apantisi_os=$cwp_user_display_name;
  
  //echo '<pre>sss ['.$is_employee.'|'.$cwp_user_id.'|'.$cwp_user_display_name.'|'.$cwp_url.'|'.$cwp_is_erp_user.']';die();
  $cwp_page=$_POST['cwp_url'].$_POST['cwp_page'];
  
  //echo '<pre>hhhhhhhhhh ';print_r($_POST);die();
  //echo '<pre>hhhhhhhhhh '.$cwp_page;die();
  

  $sql=gks_erp_sales_order_online_sql_order($guid);
  //echo '<pre>';echo $sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);
    echo '<div class="gks_message_error">'.gks_lang('sql error').'</div>';die();}
  if ($result->num_rows!=1) {debug_mail(false,'order not found',$sql);
    echo '<div class="gks_message_error">'.gks_lang('Δεν βρέθηκε η προσφορά').'</div>';die();}
  $row_order=$result->fetch_assoc();
  $id_order=intval($row_order['id_order']);
  $mdate_expire=''; if (isset($row_order['mdate_expire'])) $mdate_expire=$row_order['mdate_expire'];
  $online_password=$row_order['online_password'];
  $order_state=$row_order['order_state'];
  
  //echo '<pre>|'.$cwp_is_erp_user.'|';die();
  $prosfora_page=$row_order['orders_online_url'].'?guid='.$row_order['order_guid'];
  if ($prosfora_page!=$cwp_page) {
    echo '<script>
    window.location.href = "'.$prosfora_page.'";
    </script>';
    die();
  }
  
  //echo '<pre>hhhhhhhhhh '.$prosfora_page.'|'.$cwp_page;die();
  $html_part_1=$row_order['html_part_1'];
  $html_part_2=$row_order['html_part_2'];
  $html_part_3=$row_order['html_part_3'];
  $html_part_4=$row_order['html_part_4'];
  $html_part_5=$row_order['html_part_5'];
  $html_part_6=$row_order['html_part_6'];
  
  $custom_css=$row_order['custom_css'];
  $custom_javascript=$row_order['custom_javascript'];
  
  $html='<style>
  '.$custom_css.'
  </style>
  '.$html_part_2.'
  <script>
  '.$custom_javascript.'
  </script>';
  
  
  if ($cwp_is_erp_user==false and $online_password!='') {
    $cookie_user_pass='';if (isset($_POST['cookie_user_pass'])) $cookie_user_pass=trim_gks($_POST['cookie_user_pass']);
    //echo '<pre>'.$guid.'|'.$cookie_user_pass;die();
    
    if ($online_password!=$cookie_user_pass) {
      $html='<style>
      '.$custom_css.'
      </style>
      '.$html_part_1.'
      <script>
      '.$custom_javascript.'
      </script>';
      $html=str_replace('[[guid]]',$guid,$html);
      echo $html;die();
    }
  }
  
  if (in_array($order_state,['025offer','040cancelled','050rejected','055wait_payment','060registered'])==false) {
    echo '<div class="gks_message_error">'.gks_lang('Η προσφορά δεν είναι σε κατάσταση Online').'</div>';die();}
  $order_state_text=getOrderStateDescr($order_state);
  

  $html=str_replace('[[guid]]',$guid,$html);
  
  $html=str_replace('[[apantisi_os]]',gks_lang('Απάντηση ως').' '.$apantisi_os,$html);
  $html=str_replace('[[new_message_apantisi_display]]',($apantisi_os=='' ? 'display:none;' : ''),$html);

            


  $mdate_expire_time=0;
  $mdate_expire_text='';
  if ($mdate_expire!='') {
    $mdate_expire_time=strtotime($mdate_expire);
    if ($mdate_expire_time < time()) {
      echo '<div class="gks_message_error">'.gks_lang('Η προσφορά έχει λήξει').'</div>'; die();
    }
    
    $mdate_expire_text=showDate($mdate_expire_time,'d/m/Y H:i',1);
  }
    
  $order_status_html='<div><span class="order_state_'.$order_state.'">'.$order_state_text.'</span></div>';
  $html=str_replace('[[order_status]]',$order_status_html,$html);
  
  $html=str_replace('[[order_expire]]',$mdate_expire_text,$html);
  $html=str_replace('[[order_expire_display]]',($mdate_expire_text=='' ? 'display:none;' : ''),$html);
  

  $html=str_replace('[[order_title]]',$row_order['id_order'],$html);

  $assigned_id=intval($row_order['assigned_id']);
  $contact_image='';
  $contact='';
  $contact_phone='';
  $contact_email='';
  $contact_display='';
  $contact_job_title=trim_gks($row_order['job_title_assigned']);
  if (isset($row_order['display_name_assigned'])) $contact=trim($row_order['display_name_assigned']); 
  if (isset($row_order['gks_wsl_current_user_image_assigned'])) $contact_image=trim($row_order['gks_wsl_current_user_image_assigned']); 
  if ($contact_image!='') {
    $contact_image='<div class="contact_image" style="background-image: url(\''.$contact_image.'\');"></div>';
  }
  
  
  if ($assigned_id>0) {
    $sql_assigned="SELECT * FROM gks_users_communication 
    WHERE user_id=".$assigned_id."
    order by comm_primary desc";
    $result_assigned = $db_link->query($sql_assigned);        
    if (!$result_assigned) {debug_mail(false,'sql error',$sql_assigned);
      echo '<div class="gks_message_error">sql error</div>';die();}    
    
    while ($row_assigned = $result_assigned->fetch_assoc()) {
      if ($row_assigned['comm_type']=='phone' and $row_assigned['comm_descr']==gks_lang('Εργασία')) {
        $contact_phone='<i class="fas fa-phone-square-alt"></i> '.$row_assigned['comm_value'];
      }
      if ($row_assigned['comm_type']=='email' and $row_assigned['comm_descr']==gks_lang('Εργασίας')) {
        $contact_email='<i class="fas fa-envelope"></i> '.$row_assigned['comm_value'];
      }
      //echo $row_assigned['comm_type'].$row_assigned['comm_descr'];die();
    }


  }
  
  if ($contact=='') $contact_display='display:none;';

  $html=str_replace('[[contact_image]]',$contact_image,$html);
  $html=str_replace('[[contact]]',$contact,$html);
  $html=str_replace('[[contact_phone]]',$contact_phone,$html);
  $html=str_replace('[[contact_email]]',$contact_email,$html);
  $html=str_replace('[[contact_display]]',$contact_display,$html);
  $html=str_replace('[[contact_title]]',$contact_job_title,$html);


  
  $sub='';if ($row_order['company_sub_id']>0) $sub='_sub';
  $company_data=[];

  $temp=trim($row_order['company'.$sub.'_title']);if ($temp!='') $company_data[]='<b>'.$temp.'</b>';
  $temp=trim($row_order['company'.$sub.'_tagline']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company'.$sub.'_eponimia']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company_epaggelma']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company'.$sub.'_odos'].' '.$row_order['company'.$sub.'_arithmos']);if ($temp!='') $company_data[]='<i class="fas fa-map-marker-alt"></i> '.$temp;
  $temp=trim($row_order['company'.$sub.'_orofos'].' '.$row_order['company'.$sub.'_perioxi']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company'.$sub.'_tk'].' '.$row_order['company'.$sub.'_poli']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['nomos_descr_company'.$sub].' '.$row_order['country_name_company'.$sub]);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company_afm'].' '.$row_order['company_doy']);if ($temp!='') $company_data[]=$temp;
  $temp=trim($row_order['company'.$sub.'_phone']);if ($temp!='') $company_data[]='<i class="fas fa-phone-square-alt"></i> '.$temp;
  $temp=trim($row_order['company'.$sub.'_email']);if ($temp!='') $company_data[]='<i class="fas fa-envelope"></i> '.$temp;
  $temp=trim($row_order['company'.$sub.'_url']);if ($temp!='') $company_data[]='<i class="fas fa-globe"></i> '.$temp;
  $html=str_replace('[[company_data]]',implode('<br>',$company_data),$html);


  $user_data=[];
  $temp=trim($row_order['user_last_name'].' '.$row_order['user_first_name']);if ($temp!='') $user_data[]='<b>'.$temp.'</b>';
  $temp=trim($row_order['ma_odos'].' '.$row_order['ma_arithmos']);  if ($temp!='') $user_data[]='<i class="fas fa-map-marker-alt"></i> '.$temp;
  $temp=trim($row_order['ma_orofos'].' '.$row_order['ma_perioxi']);  if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['ma_tk'].' '.$row_order['ma_poli']);  if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['nomos_descr'].' '.$row_order['country_name']);  if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['title']);if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['eponimia']); if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['epaggelma']);  if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['afm'].' '.$row_order['doy']);  if ($temp!='') $user_data[]=$temp;
  $temp=trim($row_order['user_mobile']);if ($temp!='') $user_data[]='<i class="fas fa-phone-square-alt"></i> '.$temp;
  $temp=trim($row_order['user_email']);if ($temp!='') $user_data[]='<i class="fas fa-envelope"></i> '.$temp;
  $html=str_replace('[[user_data]]',implode('<br>',$user_data),$html);


  $sql_eidi=gks_erp_sales_order_online_sql_order_products($id_order);
  //gks_orders_products.product_set
  $result_eidi = $db_link->query($sql_eidi);        
  if (!$result_eidi) {debug_mail(false,'sql error',$sql_eidi);
      echo '<div class="gks_message_error">sql error</div>';die();}
      
  $eidos_array = array();
  $products_sets=array();
  $products_count=0;
  
  $product_id_array=[];
  while ($eidos = $result_eidi->fetch_assoc()) {
    $eidos_array[]=$eidos;
    $products_count++;
    if ($eidos['product_id']>2 and in_array($eidos['product_id'],$product_id_array)==false) {
      $product_id_array[]=$eidos['product_id'];
    }
    $parts=explode(',',trim($eidos['product_set']));
    foreach ($parts as $myset) {
      $myset=trim($myset);
      if ($myset!='') {
        if (isset($products_sets[$myset])==false) $products_sets[$myset]=array();
        $products_sets[$myset][]= $eidos['id_order_product'];
      }
    }
  }
  $woo_id_array=[];
  if ($id_eshop>0 and count($product_id_array)>0) {
    //echo '<pre>';print_r($product_id_array);die();  
    $sql_woo="SELECT product_id, remote_product_id, remote_lang
    FROM gks_woo_product
    WHERE eshop_id=".$id_eshop."
    AND product_id In (".implode(',',$product_id_array).")
    ORDER BY id_woo_product";
    $result_woo = $db_link->query($sql_woo);        
    if (!$result_woo) {debug_mail(false,'sql error',$sql_woo);
        echo '<div class="gks_message_error">sql error</div>';die();}
    while ($woop = $result_woo->fetch_assoc()) {
      $woo_id_array[$woop['product_id']]=$woop;
    }
    //echo '<pre>';print_r($woo_id_array);die();  
  }
  

  $pos1=strpos($html_part_3,'<tbody>');
  $pos2=strpos($html_part_3,'</tbody>');
  if ($pos1===false or $pos2===false or $pos1 > $pos2) {
    $ptr=$html_part_3;
    $table_products_template='[[ptrs]]';
  } else {
    $ptr=substr($html_part_3, $pos1+7,$pos2-$pos1-7);
    $table_products_template=str_replace($ptr, '[[ptrs]]', $html_part_3);
  }
  //echo '<pre>ssssa ';echo htmlspecialchars($ptr);die();
  //echo '<pre>ssssa ';echo htmlspecialchars($table_products_template);die();
    
      
  $products=[];
  $products_optional=[];
  $aa_p = 0;$aa_o = 0;
  $product_is_optional_enable=false;
  
  $eidi_sum_quantity=0;
  $eidi_sum_price_net=0;  
  
  $from_eidi_products_total=0;
  $from_eidi_products_netvalue=0;
  $from_eidi_products_fpa=0;
  $from_eidi_totalWithheldAmount=0;
  $from_eidi_totalOtherTaxesAmount=0;
  $from_eidi_totalStampDutyamount=0;
  $from_eidi_totalFeesAmount=0;
  $from_eidi_totalDeductionsAmount=0;


  
  foreach ($eidos_array as $eidos) {
    
    if (in_array($eidos['product_is_optional'],['0','2'])) {
      $from_eidi_products_total+=
        floatval($eidos['product_price_final_all_net'])
        + floatval($eidos['product_price_final_all_fpa'])
        - floatval($eidos['product_withheldAmount'])
        + floatval($eidos['product_otherTaxesAmount'])
        + floatval($eidos['product_stampDutyAmount'])
        + floatval($eidos['product_feesAmount'])
        - 0;
      
      $from_eidi_products_netvalue+=floatval($eidos['product_price_final_all_net']);
      $from_eidi_products_fpa+=floatval($eidos['product_price_final_all_fpa']);
      $from_eidi_totalWithheldAmount+=floatval($eidos['product_withheldAmount']);;
      $from_eidi_totalOtherTaxesAmount+=floatval($eidos['product_otherTaxesAmount']);
      $from_eidi_totalStampDutyamount+=floatval($eidos['product_stampDutyAmount']);
      $from_eidi_totalFeesAmount+=floatval($eidos['product_feesAmount']);
      //$from_eidi_totalDeductionsAmount+=0;
    }
    $eidi_sum_quantity+=$eidos['product_quantity'];
    $eidi_sum_price_net+=$eidos['product_price_final_all_net'];

    $ekptosi_poso_html='';
    $ekptosi_poso = $eidos['product_price_start_all_net']-$eidos['product_price_final_all_net'];
    if (abs($ekptosi_poso) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_html= myCurrencyFormat($ekptosi_poso);

    $ekptosi_poso_netfpa_html='';
    $ekptosi_poso_netfpa = $eidos['product_price_start_all_net']+$eidos['product_price_start_all_fpa']-$eidos['product_price_final_all_net']-$eidos['product_price_final_all_fpa'];
    if (abs($ekptosi_poso_netfpa) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_netfpa_html= myCurrencyFormat($ekptosi_poso_netfpa);

    $iurl='';
    $thump_url=trim($eidos['product_photo_p'].'');
    if ($thump_url == '') {
      $thump_url=GKS_SITE_URL.'my/img/product.png';
      $photo_url=$thump_url;
    } else {
      $mydir = dirname($thump_url);
      if (substr($mydir, strlen($mydir)-10,10)=='/thumbnail') {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($thump_url);
      } else {
        $photo_url=$thump_url;
      }
      $photo_url=substr(GKS_SITE_URL, 0,strlen(GKS_SITE_URL)-1).$photo_url;
      $thump_url=substr(GKS_SITE_URL, 0,strlen(GKS_SITE_URL)-1).$thump_url;
      
    }
    $iurl= '<a class="gks_photo_link" tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'" target="_blank"><img class="gks_img" src="'.$thump_url.'"></a>';
    
    $woo_id_purl=$photo_url;
    $woo_id_id='';
    if (isset($woo_id_array[$eidos['product_id']])) {
      $woo_id_purl='[[woo,purl,'.$woo_id_array[$eidos['product_id']]['remote_product_id'].']]';
      $woo_id_id  ='[[woo,id,'.  $woo_id_array[$eidos['product_id']]['remote_product_id'].']]';
    }
    
    if (in_array($eidos['product_is_optional'],['0','2'])) {
      $aa_p++;$aa=$aa_p;
    } else {
      $aa_o++;$aa=$aa_o;
    }
    if ($eidos['product_is_optional']!=0) $product_is_optional_enable=true;
    
    


    $tr=$ptr;
    $tr=str_replace('[[aa]]', $aa, $tr);
    $tr=str_replace('[[id_order_product]]', $eidos['id_order_product'], $tr);
    if ($eidos['product_is_optional']=='0') {
      $tr=str_replace('[[product_is_optional_icon]]', 'fix', $tr);
      $tr=str_replace('[[product_is_optional_icon_fa]]', 'fa-check-circle', $tr);
      $tr=str_replace('[[product_is_optional_icon_title]]', gks_lang('Δεν μπορεί να αφαιρεθεί από την προσφορά'), $tr);
    } else if ($eidos['product_is_optional']=='1') {
      $tr=str_replace('[[product_is_optional_icon]]', 'add', $tr);
      $tr=str_replace('[[product_is_optional_icon_fa]]', 'fa-plus-circle', $tr);
      $tr=str_replace('[[product_is_optional_icon_title]]', gks_lang('Προσθήκη'), $tr);
    } else if ($eidos['product_is_optional']=='2') {
      $tr=str_replace('[[product_is_optional_icon]]', 'remove', $tr);
      $tr=str_replace('[[product_is_optional_icon_fa]]', 'fa-minus-circle', $tr);
      $tr=str_replace('[[product_is_optional_icon_title]]', gks_lang('Αφαίρεση'), $tr);
    }
    $tr=str_replace('[[iurl]]', $iurl, $tr);
    $tr=str_replace('[[photo_url]]', $photo_url, $tr);
    $tr=str_replace('[[thump_url]]', $thump_url, $tr);
    $tr=str_replace('[[woo_id_purl]]', $woo_id_purl, $tr);
    $tr=str_replace('[[woo_id_id]]',   $woo_id_id, $tr);
    
    $tr=str_replace('[[product_descr]]', $eidos['product_descr'], $tr);
    $tr=str_replace('[[product_code]]', $eidos['product_code'], $tr);


    $product_comments=''; 
    if (!empty($eidos['product_comments'])) $product_comments=trim($eidos['product_comments']);
    $tr=str_replace('[[product_comments]]', $product_comments, $tr);
    $tr=str_replace('[[product_quantity]]', $eidos['product_quantity'], $tr);
    $tr=str_replace('[[product_price_final_all_net]]', myCurrencyFormat($eidos['product_price_final_all_net']), $tr);
    $tr=str_replace('[[product_price_final_all_fpa]]', myCurrencyFormat($eidos['product_price_final_all_fpa']), $tr);
    $tr=str_replace('[[product_price_final_all_total]]', myCurrencyFormat($eidos['product_price_final_all_total']), $tr);
   
    //0-> metraei sto sinolo - to default
    //1-> mporei na to prosuesei o pelatis
    //2-> to prosthese o pelatis
    if (in_array($eidos['product_is_optional'],['0','2'])) {
      $products[]=$tr;
    } else {
      $products_optional[]=$tr;
    }
    
  }
   
  if (count($products)>0) {
    //echo '<pre>';print_r($products);die();
    $temp=$table_products_template;
    $temp=str_replace('[[ptrs]]', implode("\r\n",$products), $temp);
    $temp=str_replace('[[add_remove]]', 'remove', $temp);
    $products=$temp;
  } else {
    $products='';
  }
  $html=str_replace('[[products]]',$products,$html);
  
  if (count($products_optional)>0 or $product_is_optional_enable) {
    //echo '<pre>';print_r($products);die();
    $temp=$table_products_template;
    $temp=str_replace('[[ptrs]]', implode("\r\n",$products_optional), $temp);
    $temp=str_replace('[[add_remove]]', 'add', $temp);
    $products_optional=$temp;
  } else {
    $products_optional='';
  }
  $html=str_replace('[[products_optional]]',$products_optional,$html);  
  
  if ($product_is_optional_enable and $order_state=='025offer') {
    $html=str_replace('[[products_optional_display]]','',$html);
  } else {
    $html=str_replace('[[products_optional_display]]','display:none',$html);
    
  }
  $from_eidi_pliroteo=
    $from_eidi_products_netvalue 
    + $from_eidi_products_fpa
    - $from_eidi_totalWithheldAmount
    + $from_eidi_totalOtherTaxesAmount
    + $from_eidi_totalStampDutyamount
    + $from_eidi_totalFeesAmount
    - $from_eidi_totalDeductionsAmount;  
  
//  $from_eidi_products_total=0;


  $pts='db';if ($product_is_optional_enable and $aa_o>0) $pts='calc';
  //$pts='calc';

  $totals=$html_part_4;
  if ($pts=='db') {
    $totals=str_replace('[[gks_price_net]]',myCurrencyFormat($row_order['gks_price_net']),$totals);
    $totals=str_replace('[[gks_price_net_hidezero]]',($row_order['gks_price_net']==0 ? 'display:none;' : ''),$totals);
    
    $totals=str_replace('[[gks_price_fpa]]',myCurrencyFormat($row_order['gks_price_fpa']),$totals);
    $totals=str_replace('[[gks_price_fpa_hidezero]]',($row_order['gks_price_fpa']==0 ? 'display:none;' : ''),$totals);
    
    $totals=str_replace('[[gks_price_netfpa]]',myCurrencyFormat($row_order['gks_price_netfpa']),$totals);
    $totals=str_replace('[[gks_price_netfpa_hidezero]]',($row_order['gks_price_netfpa']==0 ? 'display:none;' : ''),$totals);
    
    $totals=str_replace('[[totalWithheldAmount]]',myCurrencyFormat($row_order['totalWithheldAmount']),$totals);
    $totals=str_replace('[[totalWithheldAmount_hidezero]]',($row_order['totalWithheldAmount']==0 ? 'display:none;' : ''),$totals);
      
    $totals=str_replace('[[totalOtherTaxesAmount]]',myCurrencyFormat($row_order['totalOtherTaxesAmount']),$totals);
    $totals=str_replace('[[totalOtherTaxesAmount_hidezero]]',($row_order['totalOtherTaxesAmount']==0 ? 'display:none;' : ''),$totals);
      
    $totals=str_replace('[[totalStampDutyamount]]',myCurrencyFormat($row_order['totalStampDutyamount']),$totals);
    $totals=str_replace('[[totalStampDutyamount_hidezero]]',($row_order['totalStampDutyamount']==0 ? 'display:none;' : ''),$totals);
      
    $totals=str_replace('[[totalFeesAmount]]',myCurrencyFormat($row_order['totalFeesAmount']),$totals);
    $totals=str_replace('[[totalFeesAmount_hidezero]]',($row_order['totalFeesAmount']==0 ? 'display:none;' : ''),$totals);
      
    $totals=str_replace('[[gks_price_total]]',myCurrencyFormat($row_order['gks_price_total']),$totals);
    $totals=str_replace('[[gks_price_total_hidezero]]',($row_order['gks_price_total']==0 ? 'display:none;' : ''),$totals);
      
  } else {
    $totals=str_replace('[[gks_price_net]]',myCurrencyFormat($from_eidi_products_netvalue),$totals);
    $totals=str_replace('[[gks_price_net_hidezero]]',($from_eidi_products_netvalue==0 ? 'display:none;' : ''),$totals);
    
    $totals=str_replace('[[gks_price_fpa]]',myCurrencyFormat($from_eidi_products_fpa),$totals);
    $totals=str_replace('[[gks_price_fpa_hidezero]]',($from_eidi_products_fpa==0 ? 'display:none;' : ''),$totals);
    
    $totals=str_replace('[[gks_price_netfpa]]',myCurrencyFormat($from_eidi_products_netvalue + $from_eidi_products_fpa),$totals);
    $totals=str_replace('[[gks_price_netfpa_hidezero]]',($from_eidi_products_netvalue + $from_eidi_products_fpa==0 ? 'display:none;' : ''),$totals);

    $totals=str_replace('[[totalWithheldAmount]]',myCurrencyFormat($from_eidi_totalOtherTaxesAmount),$totals);
    $totals=str_replace('[[totalWithheldAmount_hidezero]]',($from_eidi_totalOtherTaxesAmount==0 ? 'display:none;' : ''),$totals);

    $totals=str_replace('[[totalOtherTaxesAmount]]',myCurrencyFormat($from_eidi_totalOtherTaxesAmount),$totals);
    $totals=str_replace('[[totalOtherTaxesAmount_hidezero]]',($from_eidi_totalOtherTaxesAmount==0 ? 'display:none;' : ''),$totals);

    $totals=str_replace('[[totalStampDutyamount]]',myCurrencyFormat($from_eidi_totalStampDutyamount),$totals);
    $totals=str_replace('[[totalStampDutyamount_hidezero]]',($from_eidi_totalStampDutyamount==0 ? 'display:none;' : ''),$totals);

    $totals=str_replace('[[totalFeesAmount]]',myCurrencyFormat($from_eidi_totalFeesAmount),$totals);
    $totals=str_replace('[[totalFeesAmount_hidezero]]',($from_eidi_totalFeesAmount==0 ? 'display:none;' : ''),$totals);

    $totals=str_replace('[[gks_price_total]]',myCurrencyFormat($from_eidi_pliroteo),$totals);
    $totals=str_replace('[[gks_price_total_hidezero]]',($from_eidi_pliroteo==0 ? 'display:none;' : ''),$totals);

  }

  $totals=str_replace('[[pts]]',$pts,$totals);
  $totals=str_replace('[[from_eidi_products_total]]',$from_eidi_products_total,$totals);

  $html=str_replace('[[totals]]',$totals,$html);

  $gks_price_total_html=myCurrencyFormat($row_order['gks_price_total']);
  if ($pts=='calc') $gks_price_total_html=myCurrencyFormat($from_eidi_pliroteo);
  
  $html=str_replace('[[sum]]',$gks_price_total_html,$html);
 
  $sxolio=''; if (!empty($row_order['note_doc'])) $sxolio=nl2br(trim($row_order['note_doc']));
  $html=str_replace('[[sxolio]]',$sxolio,$html);
  $sxolio_display='';if ($sxolio=='') $sxolio_display='display:none;';
  $html=str_replace('[[sxolio_display]]',$sxolio_display,$html);


  if (in_array($order_state,['025offer'])) {
    $html=str_replace('[[buttons]]',$html_part_5,$html);
    $html=str_replace('[[buttons_display]]','',$html);
  } else {
    $html=str_replace('[[buttons]]','',$html);
    $html=str_replace('[[buttons_display]]','display:none',$html);
  }
  


  $sql_log="SELECT gks_orders_log.*, udname.meta_value as display_name
  FROM gks_orders_log LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='display_name'
  )  AS udname ON gks_orders_log.user_id = udname.user_id
  WHERE gks_orders_log.order_id=".$id_order."
  and gks_orders_log.from_online=1
  ORDER BY gks_orders_log.id_gks_orders_log ASC;";
  $result_log = $db_link->query($sql_log);        
  if (!$result_log) debug_mail(false,'error sql',$sql_log);
  if (!$result_log) die('sql error');
  
  $messages='';
  $j = 0;$max_id_gks_orders_log=0;
  while ($row_log = $result_log->fetch_assoc()) {
    $j++;
    if ($row_log['id_gks_orders_log']>$max_id_gks_orders_log) $max_id_gks_orders_log=$row_log['id_gks_orders_log'];
    $mitem=$html_part_6;
    $mitem=str_replace('[[id_gks_orders_log]]', $row_log['id_gks_orders_log'], $mitem);
    $mitem=str_replace('[[aa]]', $j, $mitem);
    $mitem=str_replace('[[add_date]]', showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1), $mitem);
    $mitem=str_replace('[[display_name]]', $row_log['display_name'], $mitem);
    $mitem=str_replace('[[sxolio]]', str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']), $mitem);
    $messages.=$mitem;
  }
  $messages='<input type="hidden" id="max_id_gks_orders_log" value="'.$max_id_gks_orders_log.'">'.$messages;
  
  $html=str_replace('[[messages]]',$messages,$html);

    
  echo $html;
  //echo '<pre>';print_r($_POST);die();
  
}

function gks_erp_sales_order_online_wp_ajax($guid) {
/* POST Array
(
    [url] => https://test.easyfilesselection.com/my/api.v1.php
    [rnd] => 5474188-8837658-2681943-1825806-7517843
    [token] => d149158ae482f9b730f4ac8da70c39bc
    [online_order_guid] => efba64cc9e97d78196d1bc7b1cfb4a57
    [action] => wp_ajax
    [lang] => el-GR
    [data_post] => {\"cmd\":\"accept\",\"data\":\"dfsdfaf\",\"guid\":\"efba64cc9e97d78196d1bc7b1cfb4a57\"}
)
*/
  global $db_link;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_ORDERS_ONLINE_URL;
  global $GKS_ORDERS_ONLINE_SMS_SENDER;
  
  //$return = array('success' => false, 'message' =>print_r($_POST,true));
  //echo json_encode($return);die();


  $return = array('success' => false, 'message' =>'generic error');

  $data_post_str='';if (isset($_POST['data_post'])) $data_post_str=$_POST['data_post'];
  $data_post_str=stripslashes($data_post_str);
  $data_post=json_decode($data_post_str,true);
  
  $cmd=''; if (isset($data_post['cmd'])) $cmd=$data_post['cmd'];
  $cmd_data=''; if (isset($data_post['data'])) $cmd_data=$data_post['data'];
  
  if (is_array($data_post)==false) {$return['message']='data_post is empty';echo json_encode($return);die();}
  if ($cmd=='') {$return['message']='cmd is empty';echo json_encode($return);die();}
  
  $sql=gks_erp_sales_order_online_sql_order($guid);
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);
    $return['message']='sql error';echo json_encode($return);die();} 
  if ($result->num_rows!=1) {debug_mail(false,'order not found',$sql);
    $return['message']=gks_lang('Δεν βρέθηκε η προσφορά');echo json_encode($return);die();} 
  $row_order=$result->fetch_assoc();
  $id_order=intval($row_order['id_order']);
  $user_id=intval($row_order['user_id']);
  $user_email=trim_gks($row_order['user_email']);
  $user_mobile=trim_gks($row_order['user_mobile']);
  $ma_country_id=intval($row_order['ma_country_id']);
  
//  $html_part_1=$row_order['html_part_1'];
//  $html_part_2=$row_order['html_part_2'];
//  $html_part_3=$row_order['html_part_3'];
//  $html_part_4=$row_order['html_part_4'];
//  $html_part_5=$row_order['html_part_5'];
  $html_part_6=$row_order['html_part_6'];
    
  $mdate_expire=''; if (isset($row_order['mdate_expire'])) $mdate_expire=$row_order['mdate_expire'];
  $online_password=$row_order['online_password'];
  $order_state=$row_order['order_state'];
  if (in_array($order_state,['025offer','040cancelled','050rejected','055wait_payment','060registered'])==false) {
    $return['message']=gks_lang('Η προσφορά δεν είναι σε κατάσταση Online');echo json_encode($return);die();} 
  
  if ($mdate_expire!='') {
    $mdate_expire_time=strtotime($mdate_expire);
    if ($mdate_expire_time < time()) {
      $return['message']=gks_lang('Η προσφορά έχει λήξει');echo json_encode($return);die();} 
  }

  $messages_staff_notif=[];
  $messages_staff_viber=[];
  $messages_staff_email_subject=[];
  $messages_staff_email_body=[];
  
  
  $messages_customer_sms_to=[];
  $messages_customer_sms_text=[];
  $messages_customer_email_to=[];
  $messages_customer_email_subject=[];
  $messages_customer_email_body=[];
  
  switch ($cmd) {
    case 'accept':
      if ($order_state!='025offer') {
        $return['message']=gks_lang('Η προσφορά δεν είναι σε κατάσταση <span class="order_state_025offer">'.getOrderStateDescr('025offer').'</span>').'<br>'.gks_lang('Ανανεώστε την σελίδα');
        echo json_encode($return);die();
      }
            
      $signature_data='';$signature_ext='';
      if (substr($cmd_data, 0,22)=='data:image/png;base64,') {
        $signature_data=substr($cmd_data,22);
        $signature_ext='.png';    
      } else if (substr($cmd_data, 0,23)=='data:image/jpeg;base64,') {
        $signature_data=substr($cmd_data,23); 
        $signature_ext='.jpg';                  
      }
      if ($signature_data=='') {
        $return['message']=gks_lang('Ζωγραφίστε την υπογραφή σας στο παραπάνω πλαίσιο');echo json_encode($return);die();
      }
       
      $upload_dir = GKS_FileServerShare.'order/'.$id_order.'/';
      if (file_exists($upload_dir) == false) {
        if (@mkdir($upload_dir , 0777, true) == false ) {
          debug_mail(false,'can not create dir: ',$upload_dir);
          $return['message']=gks_lang('Εσωτερικό σφάλμα').' 923871027<br>'.gks_lang('Παρακαλώ ξαναδοκιμάστε αργότερα');echo json_encode($return);die();
        }
      }
      $signature_data=base64_decode($signature_data);
      $signature_filename=gks_lang('signature').'_'.showDate(time(),'Y_m_d_H_i_s',1).'_'.rand(10000,99999).$signature_ext;
      $signature_fullpath=$upload_dir.$signature_filename;
      @file_put_contents($signature_fullpath,$signature_data);
      if (file_exists($signature_fullpath)==false) {
        debug_mail(false,'can not create file: ',$signature_fullpath);
        $return['message']=gks_lang('Εσωτερικό σφάλμα').' 923871028<br>'.gks_lang('Παρακαλώ ξαναδοκιμάστε αργότερα');echo json_encode($return);die();
      }
      
      $signature_url='/my/admin-get-file.php?fs=fileservers&file=order%2F'.$id_order.'%2F'.$signature_filename;
      $sxolio_log=gks_lang('Υπογραφή προσφοράς από πελάτη').'<br><a href="'.$signature_url.'" target="_blank"><img src="'.$signature_url.'" class="gks_signature_order_online"/></a>';
      $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
      ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',0)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
       
      //$return['message']='the offer xaxax';echo json_encode($return);die();
      
      $sql="select count(*) as cc from gks_orders_products
      where order_id=".$id_order."
      and product_is_optional=1";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      $row=$result->fetch_assoc();
      $need_recal=intval($row['cc'])>0;
      if ($need_recal) {
        $row_old=array();
        $products_old=array();
        $extra_address_old=array();
        
        $sql=select_gks_orders($id_order)." where id_order=".$id_order;
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 
        if ($result->num_rows!=1) {debug_mail(false,'order not found',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          $return['message']=gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
          echo json_encode($return); die();}
        $row_old = $result->fetch_assoc();
      

        $sql="SELECT gks_orders_products.*, gks_monades_metrisis.monada_descr, 
        gks_eshop_fpa_base.fpa_base_descr,
        gks_aade_katigoria_fpa.aade_katigoria_fpa_descr
        FROM ((gks_orders_products 
        LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada)
        LEFT JOIN gks_eshop_fpa_base ON gks_orders_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
        LEFT JOIN gks_aade_katigoria_fpa ON gks_orders_products.product_fpa_aade_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa
        WHERE gks_orders_products.order_id=".$id_order."
        ORDER BY gks_orders_products.product_aa;";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 
        while ($row = $result->fetch_assoc()) {
          $products_old[]=$row;
        }

        if ($row_old['address_extra']>0) {
          $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
          FROM (gks_users_extra_address 
          LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
          LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
          WHERE gks_users_extra_address.id_users_extra_address=".$row_old['address_extra'];
          $result_select = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'sql error',$sql);
            $return['message']='sql error';echo json_encode($return);die();} 
          if ($result_select->num_rows==1) {
            $extra_address_old = $result_select->fetch_assoc();
          }
        }
                
        $sql="delete from gks_orders_products 
        where order_id=".$id_order."
        and product_is_optional=1";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 
          
        $gks_custom_prepare=gks_custom_table_item_prepare('gks_orders',['from'=>'item']);
        $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
              
      }

      
      $sql="update gks_orders set order_state='060registered' where id_order=".$id_order." limit 1";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
      

      
      $my_wp_user_id=2;
      $sxolio_log=gks_lang('Αποδοχή προσφοράς από πελάτη').'<br>'.gks_lang('Αλλαγή κατάστασης προσφοράς σε').' <span class="order_state_060registered">'.getOrderStateDescr('060registered').'</span>';
      $messages_staff_notif[]=$sxolio_log.'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
      $messages_staff_viber[]=gks_lang('Αποδοχή προσφοράς από πελάτη')."\r\n".GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
      $messages_staff_email_subject[]=gks_lang('Αποδοχή προσφοράς από πελάτη').' #'.$id_order;
      $messages_staff_email_body[]=gks_lang('Αποδοχή προσφοράς από πελάτη').'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';

      $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
      ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',1)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
      if ($need_recal) {
        $sql="select * from gks_orders_products where order_id=".$id_order;
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 

        $gks_price_original_net=0;
        $gks_price_net=0;
        $gks_price_fpa=0;
        $gks_price_netfpa=0;
        $gks_price_total=0;
        
        $totalWithheldAmount=0;
        $totalOtherTaxesAmount=0;
        $totalStampDutyamount=0;
        $totalFeesAmount=0;
        $totalDeductionsAmount=0;
        $products_posotita=0;
        $products_varos=0;
        $products_ogos_max_x=0;
        $products_ogos_max_y=0;
        $products_ogos_max_z=0;
//        products_need_apostoli
//        products_need_pliromi
//        kostos_apostolis
//        tropos_apostolis
//        tropos_apostolis_json
//        kostos_pliromis
//        tropos_pliromis
//        kostos_pliromis_json
        
        while ($row = $result->fetch_assoc()) {
          $gks_price_original_net+=$row['product_price_start_all_net'];
          $gks_price_net+=$row['product_price_final_all_net'];
          $gks_price_fpa+=$row['product_price_final_all_fpa'];
          $gks_price_netfpa+=$row['product_price_final_all_net']+$row['product_price_final_all_fpa'];
          $gks_price_total+=$row['product_price_final_all_total'];
        
          $totalWithheldAmount+=$row['product_withheldAmount'];
          $totalOtherTaxesAmount+=$row['product_otherTaxesAmount'];
          $totalStampDutyamount+=$row['product_stampDutyAmount'];
          $totalFeesAmount+=$row['product_feesAmount'];

          $products_posotita+=floatval($row['product_quantity']);
          $products_varos+=floatval($row['product_quantity'])*floatval($row['product_varos']);
          
          if (floatval($row['product_ogos_x']) > $products_ogos_max_x) $products_ogos_max_x=floatval($row['product_ogos_x']);
          if (floatval($row['product_ogos_y']) > $products_ogos_max_y) $products_ogos_max_y=floatval($row['product_ogos_y']);
          $monada_convert_epi=floatval($row['monada_convert_epi']);
          $products_ogos_max_z+=$row['product_quantity'] * floatval($row['product_ogos_z']) / $monada_convert_epi;


        }
        $gks_price_total=
           $gks_price_net 
            + $gks_price_fpa
            - $totalWithheldAmount
            + $totalOtherTaxesAmount
            + $totalStampDutyamount
            + $totalFeesAmount
            - $totalDeductionsAmount;
      
/*
        kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",  
        tropos_apostolis=".$tropos_apostolis.",
        tropos_apostolis_json='".$db_link->escape_string($tropos_apostolis_json)."',
        
        kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
        tropos_pliromis=".$tropos_pliromis.",
        kostos_pliromis_json='".$db_link->escape_string($kostos_pliromis_json)."',

*/
        
        $sql="UPDATE gks_orders set
        products_posotita=".$products_posotita.",
        products_need_pliromi=".($gks_price_total==0 ? '0':'1').",
        products_varos=".$products_varos.",
        products_ogos_max_x=".$products_ogos_max_x.",
        products_ogos_max_y=".$products_ogos_max_y.",
        products_ogos_max_z=".$products_ogos_max_z.",

        gks_price_original_net=".number_format($gks_price_original_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
        gks_price_net=".number_format($gks_price_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
        gks_price_fpa=".number_format($gks_price_fpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
        gks_price_netfpa=".number_format($gks_price_netfpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
        gks_price_total=".number_format($gks_price_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
        
        totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
        totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
        totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
        totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", ";
        
        $affect_balance=$row_old['affect_balance'];
        $affect_balance_all_poso=$row_old['affect_balance_all_poso'];
        $affect_balance_all_poso_type=$row_old['affect_balance_all_poso_type'];
        $affect_balance_poso=$row_old['affect_balance_poso'];
        
        $sql.="
        affect_balance= ".$affect_balance.",
        affect_balance_all_poso=".$affect_balance_all_poso.",
        affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";
        
        if ($affect_balance == 0) {
          $affect_balance_poso=0;
        } else {
          if ($affect_balance_all_poso==1) {
            switch ($affect_balance_all_poso_type) {    
              case 'price_net':
                $affect_balance_poso=$gks_price_net;
                break;  
              case 'price_netfpa':
                $affect_balance_poso=$gks_price_netfpa;
                break;  
              case 'price_total':
                $affect_balance_poso=$gks_price_total;
                break;  
              case 'pliroteo':
                $affect_balance_poso=$gks_price_total; // + $kostos_pliromis; // + $kostos_apostolis
                break;  
              default:     
              
            }
          } else {
            //$affect_balance_poso=$affect_balance_poso;
          }
        }
        $sql.="affect_balance_poso=".number_format($affect_balance_poso, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",";
        $affect_balance_pros=1; //$row_old['eidos_parastatikou_balance_pros'];
        if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
          $affect_balance_pros=0;
        }  
        $sql.="affect_balance_pros=".$affect_balance_pros;
        $sql.=" where id_order = ".$id_order." limit 1";        
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 
        
        
        gks_order_sxolio_log($id_order,$row_old,$products_old,$extra_address_old,'',$gks_custom_row_old);
      }

      
      $return['success']=true;
      $return['message']=gks_lang('Επιτυχής αποδοχή της προσφοράς');
      break;  
    case 'reject':
      if ($order_state!='025offer') {
        $return['message']=gks_lang('Η προσφορά δεν είναι σε κατάσταση <span class="order_state_025offer">'.getOrderStateDescr('025offer').'</span>').'<br>'.gks_lang('Ανανεώστε την σελίδα');
        echo json_encode($return);die();
      }
      //$return['message']='sql error111';echo json_encode($return);die();
      $sql="update gks_orders set order_state='050rejected' where id_order=".$id_order." limit 1";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
      
      $sxolio_log=gks_lang('Απόρριψη προσφοράς από πελάτη').'<br>'.gks_lang('Αλλαγή κατάστασης προσφοράς σε').' <span class="order_state_050rejected">'.getOrderStateDescr('050rejected').'</span>';
      $messages_staff_notif[]=$sxolio_log.'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
      $messages_staff_viber[]=gks_lang('Απόρριψη προσφοράς από πελάτη')."\r\n".GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
      $messages_staff_email_subject[]=gks_lang('Απόρριψη προσφοράς από πελάτη').' #'.$id_order;
      $messages_staff_email_body[]=gks_lang('Απόρριψη προσφοράς από πελάτη').'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
      
      $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
      ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',1)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      $return['success']=true;
      $return['message']=gks_lang('Επιτυχής απόρριψη της προσφοράς');

      break;
    
    case 'timer_get_log':
      
      $max_id_gks_orders_log=intval($cmd_data);
      
      $sql_log="SELECT gks_orders_log.*, udname.meta_value as display_name
      FROM gks_orders_log LEFT JOIN (
        SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value
        FROM ".GKS_WP_TABLE_PREFIX."usermeta
        WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='display_name'
      )  AS udname ON gks_orders_log.user_id = udname.user_id
      WHERE gks_orders_log.order_id=".$id_order."
      and gks_orders_log.id_gks_orders_log>".$max_id_gks_orders_log."
      and gks_orders_log.from_online=1
      ORDER BY gks_orders_log.id_gks_orders_log ASC;";
      $result_log = $db_link->query($sql_log);        
      if (!$result_log) debug_mail(false,'error sql',$sql_log);
      if (!$result_log) die('sql error');
      
      $messages=[];
      $max_id_gks_orders_log=0;
      while ($row_log = $result_log->fetch_assoc()) {

        if ($row_log['id_gks_orders_log']>$max_id_gks_orders_log) $max_id_gks_orders_log=$row_log['id_gks_orders_log'];

        $mitem=$html_part_6;
        $mitem=str_replace('[[id_gks_orders_log]]', $row_log['id_gks_orders_log'], $mitem);
        $mitem=str_replace('[[aa]]', '--', $mitem);
        $mitem=str_replace('[[add_date]]', showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1), $mitem);
        $mitem=str_replace('[[display_name]]', $row_log['display_name'], $mitem);
        $mitem=str_replace('[[sxolio]]', str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']), $mitem);
        
        $messages[]=['id' => $row_log['id_gks_orders_log'], 'html'=>$mitem];
      }
      $return['messages']=$messages;
      $return['max_id_gks_orders_log']=intval($max_id_gks_orders_log);
      $return['message']='OK';
      $return['success']=true;
      echo json_encode($return);die();
      break;
    case 'message_send':
      //$return['message']='<pre>sss '.print_r($_POST,true);echo json_encode($return);die();
      
      $is_employee=false;
      if (isset($_POST['cwp_user_roles']) and is_array($_POST['cwp_user_roles']) and
          (in_array('employee',$_POST['cwp_user_roles']) or 
           in_array('administrator',$_POST['cwp_user_roles']) or 
           in_array('adminmy',$_POST['cwp_user_roles']))) {
        $is_employee=true;  
      }
      $cwp_user_id=0;if (isset($_POST['cwp_user_id'])) $cwp_user_id=intval($_POST['cwp_user_id']);
      $cwp_user_display_name=0;if (isset($_POST['cwp_user_display_name'])) $cwp_user_display_name=trim_gks($_POST['cwp_user_display_name']);
      $cwp_url=0;if (isset($_POST['cwp_url'])) $cwp_url=trim_gks($_POST['cwp_url']);

      $cwp_is_erp_user=false;
      if ($is_employee and $cwp_user_id>0 and $cwp_url.'/'==GKS_SITE_URL) {
        $cwp_is_erp_user=true;
      }
      //$return['message']='<pre>sss ['.$is_employee.'|'.$cwp_user_id.'|'.$cwp_user_display_name.'|'.$cwp_url.'|'.$cwp_is_erp_user.']';echo json_encode($return);die();

       
      if ($cwp_is_erp_user) {
        $sxolio_log=$cmd_data;
        $sxolio_log=str_replace("\r\n",'<br>',$sxolio_log);
        $sxolio_log=str_replace("\n",'<br>',$sxolio_log);
        
        $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
        ".$id_order.",now(),".$cwp_user_id.",'".$db_link->escape_string($sxolio_log)."',1)";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 
        
        if ($GKS_ORDERS_ONLINE_SMS_SENDER!='') {
          if ($ma_country_id==91 and (startwith($user_mobile,'69') or startwith($user_mobile,'+3069'))) {
            $messages_customer_sms_to[]=$user_mobile;
            $messages_customer_sms_text[]=gks_lang('Μήνυμα από Online Προσφορά')."\r\n".$cmd_data."\r\n".$GKS_ORDERS_ONLINE_URL.'?guid='.$guid;
            //$return['message']='<pre>sms sender '.$GKS_ORDERS_ONLINE_SMS_SENDER;echo json_encode($return);die();
          }
        }
        if ($user_email!='' and filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
          $messages_customer_email_to[]=$user_email;
          $messages_customer_email_subject[]=gks_lang('Μήνυμα από Online Προσφορά').' #'.$id_order;
          $messages_customer_email_body[]=gks_lang('Μήνυμα από Online Προσφορά').'<br>'.$sxolio_log.'<br><a href="'.$GKS_ORDERS_ONLINE_URL.'?guid='.$guid.'">#'.$id_order.'</a>';
        }
        
        $return['message']=gks_lang('Το μήνυμα έχει καταχωρηθεί επιτυχώς');
        if (count($messages_customer_sms_to)>0)  {
          $return['message'].='<br>'.gks_lang('Έχει αποσταλεί SMS στο').' <b>'.$user_mobile.'</b>';
        } else {
          $return['message'].='<br>'.gks_lang('Δεν έχει αποσταλεί SMS');
        }
        if (count($messages_customer_email_subject)>0)  {
          $return['message'].='<br>'.gks_lang('Έχει αποσταλεί email στο').' <b>'.$user_email.'</b>';
        } else {
          $return['message'].='<br>'.gks_lang('Δεν έχει αποσταλεί email');
        }
        $return['success']=true;
      } else {
        $sxolio_log=$cmd_data;
        $sxolio_log=str_replace("\r\n",'<br>',$sxolio_log);
        $sxolio_log=str_replace("\n",'<br>',$sxolio_log);
        
        $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
        ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',1)";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 

        $messages_staff_notif[]=gks_lang('Μήνυμα από Online Προσφορά').'<br>'.$sxolio_log.'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
        $messages_staff_viber[]=gks_lang('Μήνυμα από Online Προσφορά')."\r\n".$cmd_data."\r\n".GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
        $messages_staff_email_subject[]=gks_lang('Μήνυμα από Online Προσφορά').' #'.$id_order;
        $messages_staff_email_body[]=gks_lang('Μήνυμα από Online Προσφορά').'<br>'.$sxolio_log.'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
        $return['message']=gks_lang('Το μήνυμα έχει αποσταλεί επιτυχώς');
        $return['success']=true;
      }
      
      
      
      break;
    case 'pass_check':
      if ($online_password==$cmd_data) {
        $return['message']=gks_lang('Ο κωδικός είναι σωστός').'<br>'.gks_lang('Παρακαλώ περιμένετε...');
        $return['success']=true;
        $return['userpass']=$cmd_data;
        $return['guid']=$guid;

        $sxolio_log=gks_lang('Ο πελάτης συνδέθηκε στην Online Προσφορά');
        
        $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
        ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',0)";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);
          $return['message']='sql error';echo json_encode($return);die();} 

        
        $messages_staff_notif[]=gks_lang('Ο πελάτης συνδέθηκε στην Online Προσφορά').'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
        $messages_staff_viber[]=gks_lang('Ο πελάτης συνδέθηκε στην Online Προσφορά')."\r\n".GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
        $messages_staff_email_subject[]=gks_lang('Ο πελάτης συνδέθηκε στην Online Προσφορά').' #'.$id_order;
        $messages_staff_email_body[]=gks_lang('Ο πελάτης συνδέθηκε στην Online Προσφορά').'<br><a href="'.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order.'">#'.$id_order.'</a>';
        //oxi die, na steilei ta minimata
      } else {
        $return['message']=gks_lang('Ο κωδικός είναι λάθος');
        $return['success']=false;
        echo json_encode($return);die();
      }
      
      
      break;
      
    case 'product_optional_add':
    case 'product_optional_remove':
      if ($order_state!='025offer') {
        $return['message']=gks_lang('Η προσφορά δεν είναι σε κατάσταση <span class="order_state_025offer">'.getOrderStateDescr('025offer').'</span>').'<br>'.gks_lang('Ανανεώστε την σελίδα');
        echo json_encode($return);die();
      }
      $id_order_product=intval($cmd_data);
      if ($id_order_product<=0) {
        $return['message']=gks_lang('Δεν έχει ορισθεί το recid').'<br>'.gks_lang('Παρακαλώ ξαναδοκιμάστε αργότερα');
        echo json_encode($return);die();
      }

      $sql="select * 
      from gks_orders_products
      where id_order_product=".$id_order_product."
      and order_id=".$id_order."
      and product_is_optional in (1,2)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      if ($result->num_rows!=1) {debug_mail(false,'gks_orders_products not found',$sql);
        $return['message']=gks_lang('Δεν βρέθηκε το είδος').'<br>'.gks_lang('Παρακαλώ ανανεώστε την σελίδα');echo json_encode($return);die();} 
      
      $row_product=$result->fetch_assoc();
      $product_descr=$row_product['product_descr'];
      
      $new_val=1;
      if ($cmd=='product_optional_add') {
        $new_val=2;
        $sxolio_log='<i class="product_is_optional_icon_add fas fa-plus-circle"></i>'.
        ' '.str_replace('[1]', $product_descr, gks_lang('Το είδος <b>[1]</b> προστέθηκε στην Online Προσφορά'));
      }
      if ($cmd=='product_optional_remove') {
        $new_val=1;
        $sxolio_log='<i class="product_is_optional_icon_remove fas fa-minus-circle"></i>'.
        ' '.str_replace('[1]', $product_descr, gks_lang('Το είδος <b>[1]</b> αφαιρέθηκε στην Online Προσφορά'));
      }
      
            
      $sql="update gks_orders_products set
      product_is_optional=".$new_val."
      where id_order_product=".$id_order_product."
      and order_id=".$id_order."
      and product_is_optional in (1,2)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 

      $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
      ".$id_order.",now(),".$user_id.",'".$db_link->escape_string($sxolio_log)."',0)";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
      
      $return['message']=gks_lang('Η αλλαγή έγινε επιτυχώς.').' '.gks_lang('Παρακαλώ περιμένετε...');
      $return['success']=true;
      echo json_encode($return);die();
      break;  




    default:
      $return['message']=str_replace('[1]',$cmd,gks_lang('Η εντολή [1] δεν έχει ακόμα υλοποιηθεί'));
      echo json_encode($return);die();
      
  }
  
  //$return['message']=$cmd.' '.$mdate_expire.' '.$mdate_expire_time.' '.date('d/m/Y H:i:s',time());
  
  foreach ($messages_staff_notif as $index => $message) {
    $sql="insert into gks_notification (
    message,for_user_id,`date_add`,for_date,has_ok,model,model_id
    )
    select
    '".$db_link->escape_string($message)."' as message,
    user_id as for_user_id,
    now() as `date_add`,
    now() as `for_date`,
    0 as has_ok,'";
    $sql.='orders';
    $sql.="' as model,
    ".$id_order." as model_id
    from gks_notification_userperm where notification_type_id=";
    $sql.='4010';
    $sql.=" and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
    //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%ordermanager%' or gks_wp_capabilities like '%adminmy%';";
    $result_insert = $db_link->query($sql);
    if (!$result_insert) debug_mail(false,'notification error sql',$sql);

    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
    FROM gks_notification_userperm 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
    AND gks_notification_userperm.notification_type_id=";
    $sql.='4010';
    $sql.=" AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
    //debug_mail(false,'sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
    } else {
      $replaces=array();
      $replaces[] = array('[[message]]', $messages_staff_email_body[$index]);
      
      while ($row = $result->fetch_assoc()) {
        $params=array(
          'model'=>'order',
          'model_id'=>$id_order,
          'to'=>$row['user_email'],
          'subject'=>$messages_staff_email_subject[$index],
          'template'=>3, //'empty.html',
          'replaces'=>$replaces,
        );
            
        $send_email_res = gks_mymail_template($params);
        
      }
    }
    
    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
    FROM gks_notification_userperm 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
    AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
    AND gks_notification_userperm.notification_type_id=";
    $sql.='4010';
    $sql.=" AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
    //debug_mail(false,'sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
    } else { 
      $send_viber=array();
      while ($row = $result->fetch_assoc()) {
        $send_viber[]=$row['viber_id'];
      }
      foreach ($send_viber as $value) {
        gks_viber_send('orders',$id_order ,$value,$messages_staff_viber[$index]);
      } 
    }
  }

  $gkIP='127.0.0.1'; 
  if (isset($_POST['gkIP'])) $gkIP=$_POST['gkIP'];
  global $gks_mymail_last_email;
  global $gks_sms_send_insert_id;
  foreach ($messages_customer_email_to as $index => $message) {
    $replaces=array();
    $replaces[] = array('[[message]]', $messages_customer_email_body[$index]);

    $params=array(
      'model'=>'order',
      'model_id'=>$id_order,
      'to'=>$messages_customer_email_to[$index],
      'subject'=>$messages_customer_email_subject[$index],
      'template'=>3, //'empty.html',
      'replaces'=>$replaces,
    );
    $gks_mymail_last_email=0;
    $send_email_res = gks_mymail_template($params);

    $sql="insert into gks_orders_messages (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    order_id,user_id,order_message,email_id,sms_id
    ) values (
    now(),now(),".$cwp_user_id.",".$cwp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id_order.",
    ".$cwp_user_id.",
    '".$db_link->escape_string($messages_customer_email_body[$index])."',
    ".$gks_mymail_last_email.",
    0
    )";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);
      $return['message']='sql error';echo json_encode($return);die();} 
  }
  
  
  foreach ($messages_customer_sms_to as $index => $message) {
    $parts=explode(':',$GKS_ORDERS_ONLINE_SMS_SENDER);
    if (count($parts)==2) {
      $sender_sms_sender=$parts[1];
      $sender_sms_provider=$parts[0];
      
      $gks_sms_send_insert_id=0;
      gks_sms_send('order',$id_order,
        $sender_sms_sender,
        $messages_customer_sms_to[$index],
        $messages_customer_sms_text[$index],
        $sender_sms_provider);
        
      $sql="insert into gks_orders_messages (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      order_id,user_id,order_message,email_id,sms_id
      ) values (
      now(),now(),".$cwp_user_id.",".$cwp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id_order.",
      ".$cwp_user_id.",
      '".$db_link->escape_string($messages_customer_sms_text[$index])."',
      0,
      ".$gks_sms_send_insert_id."
      )";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);
        $return['message']='sql error';echo json_encode($return);die();} 
      
    }
  }
  
   
  echo json_encode($return);die();
}


