<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


$plugin_sql_from_1='';
$plugin_sql_from_2='';
$plugin_sql_from_3='';
$plugin_filters=array();
$plugin_sortable=array();
$plugin_search_fields=array();
$plugin_js_date_filters='';

gks_plugins_functions_run('admin_orders_filters_step1',array(
  'sql_from_1' => &$plugin_sql_from_1,
  'sql_from_2' => &$plugin_sql_from_2,
  'sql_from_3' => &$plugin_sql_from_3,
  'filters' => &$plugin_filters,
  'sortable'=> &$plugin_sortable,
  'search_fields'=> &$plugin_search_fields,
  'js_date_filters'=> &$plugin_js_date_filters,
));


$gks_custom_prepare = gks_custom_table_item_prepare('gks_orders',['from'=>'list']);


$filters = array();

$filters[] = array(
  'name' => 'fstate',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.order_state = '%V%'",
  'vals' => array(
    array('value' => 111, 'text' => getOrderStateDescr('005prodraft'),      'sql' => "gks_orders.order_state='005prodraft'"),
    array('value' => 100, 'text' => getOrderStateDescr('010draft'),      'sql' => "gks_orders.order_state='010draft'"),
    array('value' => 101, 'text' => getOrderStateDescr('020pending'),      'sql' => "gks_orders.order_state='020pending'"),
    array('value' => 114, 'text' => getOrderStateDescr('025offer'),      'sql' => "gks_orders.order_state='025offer'"),
    array('value' => 102, 'text' => getOrderStateDescr('030forcancellation'),      'sql' => "gks_orders.order_state='030forcancellation'"),
    array('value' => 103, 'text' => getOrderStateDescr('040cancelled'),      'sql' => "gks_orders.order_state='040cancelled'"),
    array('value' => 104, 'text' => getOrderStateDescr('050rejected'),      'sql' => "gks_orders.order_state='050rejected'"),
    array('value' => 112, 'text' => getOrderStateDescr('055wait_payment'),      'sql' => "gks_orders.order_state='055wait_payment'"),
    array('value' => 105, 'text' => getOrderStateDescr('060registered'),      'sql' => "gks_orders.order_state='060registered'"),
    array('value' => 106, 'text' => getOrderStateDescr('070inproduction'),      'sql' => "gks_orders.order_state='070inproduction'"),
    array('value' => 107, 'text' => getOrderStateDescr('080failed'),      'sql' => "gks_orders.order_state='080failed'"),
    array('value' => 108, 'text' => getOrderStateDescr('090indelivery'),      'sql' => "gks_orders.order_state='090indelivery'"),
    array('value' => 110, 'text' => getOrderStateDescr('095execute'),      'sql' => "gks_orders.order_state='095execute'"),
    array('value' => 109, 'text' => getOrderStateDescr('100completed'),      'sql' => "gks_orders.order_state='100completed'"),
    array('value' => 113, 'text' => getOrderStateDescr('110payment'),      'sql' => "gks_orders.order_state='110payment'"),
  
  ),
);

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_orders.order_date', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.order_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

//print '<pre>';print_r($user_companys);die();

if (count($user_companys)>1) {
  $vals=array();
  foreach ($user_companys as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_orders.company_id=".$value['id_company']." and gks_orders.company_sub_id=".$value['id_company_sub']);
  } 
  $filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => $vals,
  );  
}

$filters[] = array(
  'name' => 'fjournal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ημερολόγιο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.order_journal_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr
FROM gks_orders LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal
WHERE (((gks_acc_journal.id_acc_journal) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_acc_journal.id_acc_journal, gks_acc_journal.acc_journal_descr
ORDER BY gks_acc_journal.sortorder,gks_acc_journal.acc_journal_descr;",    
);

$filters[] = array(
  'name' => 'fseira',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Σειρά'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.order_seira_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_seires.id_acc_seira AS id, gks_acc_seires.seira_descr AS descr
FROM gks_orders LEFT JOIN gks_acc_seires ON gks_orders.order_seira_id = gks_acc_seires.id_acc_seira
WHERE (((gks_acc_seires.id_acc_seira) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_acc_seires.id_acc_seira, gks_acc_seires.seira_descr
ORDER BY gks_acc_seires.sortorder,gks_acc_seires.seira_descr;",    
);



$filters[] = array(
  'name' => 'fcustomer',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Επαφή'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.user_id<>0"),
  ),
  'sql' => "SELECT gks_orders.user_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
FROM gks_orders LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",    
);

$filters[] = array(
    'name' => 'fpoli',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_orders.ma_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ma_poli as id, ma_poli as descr FROM gks_orders where ma_poli<>'' 
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    GROUP BY ma_poli ORDER BY ma_poli;",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_orders.ma_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_nomoi.id_nomos as id, gks_nomoi.nomos_descr as descr
    FROM gks_orders LEFT JOIN gks_nomoi ON gks_orders.ma_nomos_id = gks_nomoi.id_nomos
    WHERE (((gks_nomoi.id_nomos) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY gks_nomoi.id_nomos, gks_nomoi.nomos_descr
    ORDER BY gks_nomoi.nomos_descr;",
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true, 
    'field'  => "gks_orders.ma_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_country.id_country as id, gks_country.country_name as descr
    FROM gks_orders LEFT JOIN gks_country ON gks_orders.ma_country_id = gks_country.id_country
    WHERE (((gks_country.id_country) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY gks_country.id_country, gks_country.country_name",
);




$filters[] = array(
  'name' => 'fdwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Αποστολής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.tropos_apostolis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.tropos_apostolis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.tropos_apostolis<>0"),
  ),
  'sql' => "SELECT gks_delivery_methods.id_delivery_method AS id, gks_delivery_methods.delivery_method_name AS descr
  FROM gks_orders LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method
  WHERE gks_orders.tropos_apostolis>0 AND gks_delivery_methods.id_delivery_method>0
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
  
  GROUP BY gks_delivery_methods.id_delivery_method, gks_delivery_methods.delivery_method_name, gks_delivery_methods.mysortorder",    
);

$filters[] = array(
  'name' => 'fpwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Πληρωμής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.tropos_pliromis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.tropos_pliromis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.tropos_pliromis<>0"),
  ),
  'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name AS descr
  FROM gks_orders LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  where gks_orders.tropos_pliromis>0 and gks_payment_acquirers.id_payment_acquirer > 0
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
  
  GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
  ORDER BY gks_payment_acquirers.mysortorder",    
);

$filters[] = array(
  'name' => 'fddate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Παράδοσης'),
  'has_custom_date' => true,
  'field' => 'gks_orders.ddate', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.ddate','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10'=>array(
    array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),     'sql' => "gks_orders.ddate is null"),
    array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),         'sql' => "gks_orders.ddate is not null"),
  
  )]),


);

if  ($GKS_ORDERS_OCCASION) {
$filters[] = array(
    'name' => 'focc',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Περίσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_orders_occasion.occasion_id = %V%",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_occasion_types.id_occasion_type Is Null"),
        array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_occasion_types.id_occasion_type<>0"),
    ),
    'sql' => "SELECT gks_orders_occasion.occasion_id as id, gks_occasion_types.occasion_type_descr as descr
FROM (gks_orders LEFT JOIN gks_orders_occasion ON gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion) 
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
WHERE (((gks_occasion_types.id_occasion_type) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."

GROUP BY gks_orders_occasion.occasion_id, gks_occasion_types.occasion_type_descr
ORDER BY gks_occasion_types.occasion_type_descr",    
);
}

$filters[] = array(
    'name' => 'fabalance',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τιμή για υπόλοιπο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "affect_balance<>0",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Έχει'),      'sql' => "order_state in      ('060registered','070inproduction','090indelivery','095execute','100completed','110payment') and affect_balance=1 and affect_balance_poso<>0"),
        array('value' => -101, 'text' => gks_lang('Δεν έχει'),  'sql' => "not (order_state in ('060registered','070inproduction','090indelivery','095execute','100completed','110payment') and affect_balance=1 and affect_balance_poso<>0)"),
    ),
);

$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_orders.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_orders LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

if ($GKS_CRM_ENABLE) {
  $filters[] = array(
      'name' => 'fchannel',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Κανάλι Πωλήσεων'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_orders.crm_channel_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
      FROM gks_orders LEFT JOIN gks_crm_channel_sale ON gks_orders.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
      WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_crm_channel_sale.id_crm_channel_sale, gks_crm_channel_sale.crm_channel_sale_descr, gks_crm_channel_sale.crm_channel_sale_sortorder
      ORDER BY gks_crm_channel_sale.crm_channel_sale_sortorder",
  );
  $filters[] = array(
      'name' => 'fchcontact',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Επαφή Πωλήσεων'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_orders.crm_channel_contact_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_orders LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
  );
  $filters[] = array(
      'name' => 'fcampain',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Καμπάνια'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_orders.crm_channel_campain_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
      FROM gks_orders 
      LEFT JOIN gks_ads_campain ON gks_orders.crm_channel_campain_id = gks_ads_campain.id_ads_campain
      WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
      ORDER BY gks_ads_campain.ads_campain_name",
  );
}

$filters=array_merge($filters,$plugin_filters);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);
  




$sortable = array(
  array('name' => 'soid', 'field' => 'gks_orders.id_order'),
  array('name' => 'sood', 'field' => 'gks_orders.order_date'),
  array('name' => 'socompany', 'field' => 'gks_company.company_title, gks_company_subs.company_sub_title'),
  array('name' => 'soexec', 'field' => 'gks_orders.mdate_execute'),
  array('name' => 'sopay', 'field' => 'gks_orders.mdate_payment'),
  array('name' => 'sostate', 'field' => 'gks_orders.order_state'),
  array('name' => 'souser', 'field' => 'gks_orders.user_email, '.GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  array('name' => 'soposotita', 'field' => 'gks_orders.products_posotita'),
  array('name' => 'soprice', 'field' => 'gks_price_net'),
  array('name' => 'sobalance', 'field' => 'affect_balance_calc'),
  array('name' => 'sopa', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  array('name' => 'sode', 'field' => 'gks_delivery_methods.delivery_method_name'),
  
  array('name' => 'soddate', 'field' => 'gks_orders.ddate'),
  array('name' => 'soocc', 'field' => 'gks_occasion_types.occasion_type_descr'),
  array('name' => 'souedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  array('name' => 'sotime', 'field' => 'gks_orders.production_sum_time'),
  array('name' => 'sopoli', 'field' => 'gks_orders.ma_poli'),
	array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
	array('name' => 'sochannel', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
	array('name' => 'sochcontact', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
	array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
	array('name' => 'sokostos', 'field' => 'gks_orders.production_kostos'),
  array('name' => 'socrmcode', 'field' => 'gks_orders.crm_channel_code'),
  
);


if ($GKS_ORDERS_PRODUCTION) {
  $sortable[] = array('name' => 'sopososto', 'field' => 'gks_orders.production_pososto, gks_orders.production_ergasies_total');
}

$sortable=array_merge($sortable,$plugin_sortable);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

gks_plugins_functions_run('admin_orders_filters_sortable_edit',array(
  'sortable' => &$sortable,
));


$search_fields = array(
'gks_orders.order_state',
'gks_orders.order_ref_number',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.display_name',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
'gks_orders.eponimia',
'gks_orders.title',
'gks_orders.afm',
'gks_orders.epaggelma',
'gks_orders.ma_odos',
'gks_orders.ma_perioxi',
'gks_orders.ma_poli',
'gks_orders.ma_tk',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_payment_acquirers.payment_acquirer_name',
'gks_orders.note_production',
'gks_orders.note_logistirio',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',
'gks_orders.crm_channel_code',
);

if ($GKS_ORDERS_OCCASION) {
  $search_fields[] = 'gks_orders_occasion.title';
  $search_fields[] = 'gks_occasion_types.occasion_type_descr';
  $search_fields[] = 'gks_orders_occasion.title';
}

$search_fields=array_merge($search_fields,$plugin_search_fields);
$search_fields=array_merge($search_fields,$gks_custom_prepare['sql_search_fields']);

$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);

$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();

//$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
//echo $rows_per_page;die();
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;





$sql = "SELECT SQL_CALC_FOUND_ROWS gks_orders.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_title, gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,

gks_nomoi.nomos_descr, gks_country.country_name,gks_country.country_ee,
gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, gks_orders_occasion.mydate_add as occasion_mydate_add,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_journal.acc_eidos_parastatikou_id,
eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
gks_lang.lang_name,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_country_dest.country_name as country_name_dest,
gks_nomoi_dest.nomos_descr as nomos_descr_dest,
CASE
  WHEN (order_state='060registered' or order_state='070inproduction' or 
       order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
    THEN affect_balance_pros * affect_balance_poso
  ELSE 0
END as affect_balance_calc,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name
".$gks_custom_prepare['sql_all_list_sele']."
".$plugin_sql_from_1."
FROM ".$gks_custom_prepare['sql_all_list_from']." ".$plugin_sql_from_2." ((((((((((((((((((((((((gks_orders 
".$gks_custom_prepare['sql_all_list_left']." 
".$plugin_sql_from_3."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_orders.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang) 
LEFT JOIN gks_company ON gks_orders.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_orders.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_orders.order_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_orders_occasion on gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion)
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type) 
LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method)
LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_orders.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_orders.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_country as gks_country_dest ON gks_orders.destination_data_country_id = gks_country_dest.id_country) 
LEFT JOIN gks_nomoi as gks_nomoi_dest ON gks_orders.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_orders.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_orders.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_orders.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_orders.crm_channel_campain_id = gks_ads_campain.id_ads_campain

";  
  
//echo '<pre>';echo $sql;die();


$sql.= " where 1=1 ";
$sql.=$where . $search_where;

if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_orders.order_date desc, gks_orders.id_order desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
