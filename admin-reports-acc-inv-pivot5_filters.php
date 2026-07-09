<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/





$gks_custom_acc_inv_prepare = gks_custom_table_item_prepare('gks_acc_inv',['from'=>'pivot']);
$gks_custom_eidi_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'pivot']);

$user_companys=gks_get_companys_list();

$today_vardia_this = date('Y-m-d',_time_user(time(), 1));


$filters = array();
$filters[] = array(
  'name' => 'fstate',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_acc_inv.inv_state = '%V%'",
  'vals' => array(
    array('value' => 111, 'text' => getAccInvStateDescr('010draft'),      'sql' => "gks_acc_inv.inv_state='010draft'"),
    array('value' => 100, 'text' => getAccInvStateDescr('040cancelled'),  'sql' => "gks_acc_inv.inv_state='040cancelled'"),
    array('value' => 101, 'text' => getAccInvStateDescr('050proinvoice'), 'sql' => "gks_acc_inv.inv_state='050proinvoice'"),
    array('value' => 102, 'text' => getAccInvStateDescr('080listing'),    'sql' => "gks_acc_inv.inv_state='080listing'"),
    array('value' => 103, 'text' => getAccInvStateDescr('090ekdosi'),     'sql' => "gks_acc_inv.inv_state='090ekdosi'"),
    array('value' => 109, 'text' => getAccInvStateDescr('100payment'),    'sql' => "gks_acc_inv.inv_state='100payment'"),
  
  ),
);

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_acc_inv.inv_date', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_acc_inv.inv_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

//print '<pre>';print_r($user_companys);die();

if (count($user_companys)>1) {
  $vals=array();
  foreach ($user_companys as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_acc_inv.company_id=".$value['id_company']." and gks_acc_inv.company_sub_id=".$value['id_company_sub']);
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
  'field'  => "gks_acc_inv.inv_acc_journal_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr
FROM gks_acc_inv LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal
WHERE (((gks_acc_journal.id_acc_journal) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
  'field'  => "gks_acc_inv.inv_acc_seira_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_seires.id_acc_seira AS id, gks_acc_seires.seira_descr AS descr
FROM gks_acc_inv LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
WHERE (((gks_acc_seires.id_acc_seira) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_acc_seires.id_acc_seira, gks_acc_seires.seira_descr
ORDER BY gks_acc_seires.sortorder,gks_acc_seires.seira_descr;",    
);

$filters[] = array(
  'name' => 'fprint_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Εκτύπωση'),
  'has_custom_date' => true,
  'field' => 'gks_acc_inv.print_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_acc_inv.print_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "gks_acc_inv.print_date is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "gks_acc_inv.print_date is not null"),
    ),
  ]),
    

);

$filters[] = array(
  'name' => 'faade_send_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Αποστολή ΑΑΔΕ'),
  'has_custom_date' => true,
  'field' => 'gks_acc_inv.aade_send_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_acc_inv.aade_send_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "gks_acc_inv.aade_send_date is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "gks_acc_inv.aade_send_date is not null"),
    ),
  ]),

);

$filters[] = array(
  'name' => 'fcustomer',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Επαφή'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_acc_inv.user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_inv.user_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
FROM gks_acc_inv LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_acc_inv.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",    
);

$filters[] = array(
    'name' => 'fpoli',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_inv.ma_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ma_poli as id, ma_poli as descr FROM gks_acc_inv where ma_poli<>'' 
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    GROUP BY ma_poli ORDER BY ma_poli;",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_inv.ma_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_nomoi.id_nomos as id, gks_nomoi.nomos_descr as descr
    FROM gks_acc_inv LEFT JOIN gks_nomoi ON gks_acc_inv.ma_nomos_id = gks_nomoi.id_nomos
    WHERE (((gks_nomoi.id_nomos) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
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
    'field'  => "gks_acc_inv.ma_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_country.id_country as id, gks_country.country_name as descr
    FROM gks_acc_inv 
    LEFT JOIN gks_country ON gks_acc_inv.ma_country_id = gks_country.id_country
    WHERE (((gks_country.id_country) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY gks_country.id_country, gks_country.country_name",
);




$filters[] = array(
  'name' => 'fdwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Αποστολής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_acc_inv.tropos_apostolis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.tropos_apostolis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.tropos_apostolis<>0"),
  ),
  'sql' => "SELECT gks_delivery_methods.id_delivery_method AS id, gks_delivery_methods.delivery_method_name AS descr
  FROM gks_acc_inv 
  LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method
  WHERE gks_acc_inv.tropos_apostolis>0 AND gks_delivery_methods.id_delivery_method>0
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
  
  GROUP BY gks_delivery_methods.id_delivery_method, gks_delivery_methods.delivery_method_name, gks_delivery_methods.mysortorder",    
);

$filters[] = array(
  'name' => 'fpwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Πληρωμής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_acc_inv.tropos_pliromis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.tropos_pliromis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.tropos_pliromis<>0"),
  ),
  'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name AS descr
  FROM gks_acc_inv LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  where gks_acc_inv.tropos_pliromis>0 and gks_payment_acquirers.id_payment_acquirer > 0
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
  
  GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
  ORDER BY gks_payment_acquirers.mysortorder",    
);

$filters[] = array(
  'name' => 'fdispatch_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Αποστολή'),
  'has_custom_date' => true,
  'field' => 'gks_acc_inv.dispatch_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_acc_inv.dispatch_date','future'=>false,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this,'set_vardia'=>false,'local_time'=>true,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "gks_acc_inv.dispatch_date is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "gks_acc_inv.dispatch_date is not null"),
    ),
  ]),
);



$filters[] = array(
    'name' => 'fabalance',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τιμή για υπόλοιπο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "affect_balance<>0",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Έχει'),      'sql' => "inv_state in ('080listing','090ekdosi','100payment') and affect_balance=1 and affect_balance_poso<>0"),
        array('value' => -101, 'text' => gks_lang('Δεν έχει'),  'sql' => "not (inv_state in ('080listing','090ekdosi','100payment') and affect_balance=1 and affect_balance_poso<>0)"),
    ),
);

$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_inv.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_acc_inv LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);



$filters[] = array(
    'name' => 'fpos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εντατική Λιανική'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_inv.pos_id=%V%",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Έχει'),      'sql' => "gks_acc_inv.pos_id<>0"),
        array('value' => -101, 'text' => gks_lang('Δεν έχει'),  'sql' => "gks_acc_inv.pos_id=0"),
    ),
    'sql' => "SELECT id_pos as id, pos_name as descr
    FROM gks_acc_inv LEFT JOIN gks_pos ON gks_acc_inv.pos_id = gks_pos.id_pos
    WHERE gks_pos.id_pos Is Not Null
    GROUP BY gks_pos.id_pos, gks_pos.pos_name
    ORDER BY gks_pos.pos_name",
);

$filters[] = array(
    'name' => 'fappmobile',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('App Mobile'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_inv.erp_app_mobile_id=%V%",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Έχει'),      'sql' => "gks_acc_inv.erp_app_mobile_id<>0"),
        array('value' => -101, 'text' => gks_lang('Δεν έχει'),  'sql' => "gks_acc_inv.erp_app_mobile_id=0"),
    ),
    'sql' => "SELECT id_erp_app_mobile as id, erp_app_mobile_name as descr
FROM gks_acc_inv LEFT JOIN gks_erp_app_mobile ON gks_acc_inv.erp_app_mobile_id = gks_erp_app_mobile.id_erp_app_mobile
WHERE (((gks_erp_app_mobile.id_erp_app_mobile) Is Not Null))
GROUP BY gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name;",
);

if ($GKS_CRM_ENABLE) {
  $filters[] = array(
      'name' => 'fchannel',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Κανάλι Πωλήσεων'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_acc_inv.crm_channel_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
      FROM gks_acc_inv LEFT JOIN gks_crm_channel_sale ON gks_acc_inv.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
      WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
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
      'field'  => "gks_acc_inv.crm_channel_contact_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_acc_inv LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
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
      'field'  => "gks_acc_inv.crm_channel_campain_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
      FROM gks_acc_inv 
      LEFT JOIN gks_ads_campain ON gks_acc_inv.crm_channel_campain_id = gks_ads_campain.id_ads_campain
      WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
      ORDER BY gks_ads_campain.ads_campain_name",
  );
}



$filters=array_merge($filters,$gks_custom_acc_inv_prepare['sql_filters']);


$filters[] = array(
    'name' => 'fclass',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Βασικός τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_class = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Απλό'),       'sql' => "gks_eshop_products.product_class='simple'"),
        array('value' => 101, 'text' => gks_lang('Μεταβλητό'),  'sql' => "gks_eshop_products.product_class='variable'"),
        array('value' => 102, 'text' => gks_lang('Παραλλαγή'),  'sql' => "gks_eshop_products.product_class='variable_item'"),
    ),
);

$filters[] = array(
    'name' => 'fgroup',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατηγορία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Κατηγορία'),          'sql' => "nocategory"),
    ),
    'mywherepos' =>1,
    'sql' => "select gks_eshop_products_categories.id_product_category as id,
    CONCAT_WS('\\\\',
                    ug10.product_category_descr,
                    ug9.product_category_descr,
                    ug8.product_category_descr,
                    ug7.product_category_descr,
                    ug6.product_category_descr,
                    ug5.product_category_descr,
                    ug4.product_category_descr,
                    ug3.product_category_descr,
                    ug2.product_category_descr,
                    gks_eshop_products_categories.product_category_descr) as descr
    FROM ((((((((gks_eshop_products_categories
    LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
    ORDER BY descr",
);

$filters[] = array(
    'name' => 'fbrand',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μάρκα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Μάρκα'),  'sql' => "nobrand"),
    ),
    'mywherepos' =>2,
    'sql' => "select gks_eshop_products_brands.id_product_brand as id,
    CONCAT_WS('\\\\',
                    ug10.product_brand_descr,
                    ug9.product_brand_descr,
                    ug8.product_brand_descr,
                    ug7.product_brand_descr,
                    ug6.product_brand_descr,
                    ug5.product_brand_descr,
                    ug4.product_brand_descr,
                    ug3.product_brand_descr,
                    ug2.product_brand_descr,
                    gks_eshop_products_brands.product_brand_descr) as descr
    FROM ((((((((gks_eshop_products_brands
    LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
    ORDER BY descr",
);


$filters[] = array(
    'name' => 'ffpa_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('ΦΠΑ'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_fpa_base_id = '%V%'",
    'vals' => array(
    ),
    'sql' => "SELECT id_fpa_base as id,fpa_base_descr as descr
              FROM gks_eshop_fpa_base
              ORDER BY fpa_base_sortorder",    
);

$filters[] = array(
    'name' => 'fmm',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μονάδα Μέτρησης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_monada_id = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
//        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_price_include_vat<>0"),
//        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_price_include_vat=0"),
    ),
    'sql' => "SELECT id_monada as id,monada_descr as descr
              FROM gks_monades_metrisis
              ORDER BY monada_sortorder,monada_descr",    
);

$filters[] = array(
    'name' => 'fservice',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_base_type = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 0, 'text' => gks_product_base_type_descr(0,true),'sql' => "gks_eshop_products.product_base_type=0"),
        array('value' => 1, 'text' => gks_product_base_type_descr(1,true),'sql' => "gks_eshop_products.product_base_type=1"),
        array('value' => 2, 'text' => gks_product_base_type_descr(2,true),'sql' => "gks_eshop_products.product_base_type=2"),

    ),
);







$filters=array_merge($filters,$gks_custom_eidi_prepare['sql_filters']);
  




$sortable = array(

  
);


//if ($gks_acc_inv_PRODUCTION) {
//  $sortable[] = array('name' => 'sopososto', 'field' => 'gks_acc_inv.production_pososto, gks_acc_inv.production_ergasies_total');
//}
//$sortable=array_merge($sortable,$gks_custom_acc_inv_prepare['sql_sortable']);
//$sortable=array_merge($sortable,$gks_custom_eidi_prepare['sql_sortable']);


$search_fields = array(
'gks_acc_inv.inv_state',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.display_name',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
'gks_acc_inv.eponimia',
'gks_acc_inv.title',
'gks_acc_inv.afm',
'gks_acc_inv.epaggelma',
'gks_acc_inv.ma_odos',
'gks_acc_inv.ma_perioxi',
'gks_acc_inv.ma_poli',
'gks_acc_inv.ma_tk',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_payment_acquirers.payment_acquirer_name',
'gks_acc_inv.note_logistirio',
'gks_acc_inv.note_doc',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',


'gks_eshop_products.product_code',
'gks_eshop_products_parent.product_code',
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
'gks_eshop_products_parent.product_descr',
'gks_eshop_products.product_descr_small',
'gks_eshop_products.product_descr_big',
'gks_eshop_products.product_object_name',
'fpa_base_descr',

);




$search_fields=array_merge($search_fields,$gks_custom_acc_inv_prepare['sql_search_fields']);
$search_fields=array_merge($search_fields,$gks_custom_eidi_prepare['sql_search_fields']);

$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);

$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();


$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';
$where2 = isset($filter['sql2']) ? ' AND '.$filter['sql2'] : '';
$sql_FROM_cat_1='';
$sql_FROM_cat_2='';
$sql_FROM_bra_1='';
$sql_FROM_bra_2='';


$where_cat_calc='';
if ($where1!='') {
  $fgroup_100=false;
  if (strpos($where1, 'nocategory') !== false) $fgroup_100=true;
  $where1=str_replace('nocategory or ', '', $where1);
  
  $where1=trim_gks($where1);
  $where1=substr($where1, 6);
  $where1=substr($where1, 0, strlen($where1) - 2);
  $where1=str_replace(', or ', ',', $where1);
  $vals=explode(',', $where1);
  
  $vals_array=array();
  foreach ($vals as $value) {
    $value=intval($value);
    if ($value>0) {
      if (in_array($value, $vals_array)==false) {
        $vals_array[]=$value;
      }
    }
  } 
  
  //print_r( $vals_array);
  //die();
  
  $group_ids=array();
  foreach ($vals_array as $value) {
    $sql_gu="SELECT ug1.id_product_category AS gid1, 
                 ug2.id_product_category AS gid2, 
                 ug3.id_product_category AS gid3, 
                 ug4.id_product_category AS gid4, 
                 ug5.id_product_category AS gid5,
                 ug6.id_product_category AS gid6,
                 ug7.id_product_category AS gid7,
                 ug8.id_product_category AS gid8,
                 ug9.id_product_category AS gid9,
                 ug10.id_product_category AS gid10
    FROM ((((((((gks_eshop_products_categories AS ug1
    LEFT JOIN gks_eshop_products_categories AS ug2  ON ug1.id_product_category = ug2.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.id_product_category = ug3.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.id_product_category = ug4.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.id_product_category = ug5.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.id_product_category = ug6.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.id_product_category = ug7.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.id_product_category = ug8.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.id_product_category = ug9.product_category_parent_id)
    LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.id_product_category = ug10.product_category_parent_id
    where ug1.id_product_category=".$value;
    
    $result_gu = $db_link->query($sql_gu);        
    if (!$result_gu) {
      debug_mail(false,'error sql',$sql_gu);
      die('sql error');
    }
    $gu_in='';
    
    while ($row_gu = $result_gu->fetch_assoc()) {
      if (isset($row_gu['gid1']))  if (in_array($row_gu['gid1'],  $group_ids)==false) $group_ids[]=$row_gu['gid1'];
      if (isset($row_gu['gid2']))  if (in_array($row_gu['gid2'],  $group_ids)==false) $group_ids[]=$row_gu['gid2'];
      if (isset($row_gu['gid3']))  if (in_array($row_gu['gid3'],  $group_ids)==false) $group_ids[]=$row_gu['gid3'];
      if (isset($row_gu['gid4']))  if (in_array($row_gu['gid4'],  $group_ids)==false) $group_ids[]=$row_gu['gid4'];
      if (isset($row_gu['gid5']))  if (in_array($row_gu['gid5'],  $group_ids)==false) $group_ids[]=$row_gu['gid5'];
      if (isset($row_gu['gid6']))  if (in_array($row_gu['gid6'],  $group_ids)==false) $group_ids[]=$row_gu['gid6'];
      if (isset($row_gu['gid7']))  if (in_array($row_gu['gid7'],  $group_ids)==false) $group_ids[]=$row_gu['gid7'];
      if (isset($row_gu['gid8']))  if (in_array($row_gu['gid8'],  $group_ids)==false) $group_ids[]=$row_gu['gid8'];
      if (isset($row_gu['gid9']))  if (in_array($row_gu['gid9'],  $group_ids)==false) $group_ids[]=$row_gu['gid9'];
      if (isset($row_gu['gid10'])) if (in_array($row_gu['gid10'], $group_ids)==false) $group_ids[]=$row_gu['gid10'];
    }
  } 
  
  if (count($group_ids) >0) {
    
    if ($fgroup_100) {
      $sql_FROM_cat_1='((';
      $sql_FROM_cat_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          WHERE product_category_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_categories_products.product_category_id In ('.implode(',',$group_ids).') 
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pcat1
        GROUP BY product_id   
      ) as products_categories_subq on gks_eshop_products.id_product = products_categories_subq.product_id)
      LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null          
        ) as tabletemp_pcat2        
        GROUP BY product_id  
      ) as products_categories_subq_all on gks_eshop_products.id_product = products_categories_subq_all.product_id)';
      $where_cat_calc= ' and (products_categories_subq.product_id is not null or products_categories_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
    } else {
      $sql_FROM_cat_1='(';
      $sql_FROM_cat_2='LEFT JOIN (
        select product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          WHERE product_category_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_categories_products.product_category_id In ('.implode(',',$group_ids).') 
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pcat
        GROUP BY product_id  
      ) as products_categories_subq on gks_eshop_products.id_product = products_categories_subq.product_id)';
      $where_cat_calc= ' and (products_categories_subq.product_id is not null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
      
    }
  } else {
    if ($fgroup_100) {
      $sql_FROM_cat_1='(';
      $sql_FROM_cat_2='LEFT JOIN (
        select product_id
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null          
        ) as tabletemp_pcat
        GROUP BY product_id  
      ) as products_categories_subq_all on gks_eshop_products.id_product = products_categories_subq_all.product_id)';
      $where_cat_calc= ' and (products_categories_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
    }
  }
  
//  print '<pre>';
//  print_r($vals);
//  print_r($vals_array);
//  print_r($group_ids);
//  //print '</pre>';  
//  print $sql_FROM_bra_1."\n";
//  print $sql_FROM_bra_2."\n";
//  print $where_cat_calc."\n";
//  echo $where1;
//  die();

}  

$where_bra_calc='';
if ($where2!='') {
  $fgroup_100=false;
  if (strpos($where2, 'nobrand') !== false) $fgroup_100=true;
  $where2=str_replace('nobrand or ', '', $where2);
  
  $where2=trim_gks($where2);
  $where2=substr($where2, 6);
  $where2=substr($where2, 0, strlen($where2) - 2);
  $where2=str_replace(', or ', ',', $where2);
  $vals=explode(',', $where2);
  
  $vals_array=array();
  foreach ($vals as $value) {
    $value=intval($value);
    if ($value>0) {
      if (in_array($value, $vals_array)==false) {
        $vals_array[]=$value;
      }
    }
  } 
  
  //print_r( $vals_array);
  //die();
  
  $group_ids=array();
  foreach ($vals_array as $value) {
    $sql_gu="SELECT ug1.id_product_brand AS gid1, 
                 ug2.id_product_brand AS gid2, 
                 ug3.id_product_brand AS gid3, 
                 ug4.id_product_brand AS gid4, 
                 ug5.id_product_brand AS gid5,
                 ug6.id_product_brand AS gid6,
                 ug7.id_product_brand AS gid7,
                 ug8.id_product_brand AS gid8,
                 ug9.id_product_brand AS gid9,
                 ug10.id_product_brand AS gid10
    FROM ((((((((gks_eshop_products_brands AS ug1
    LEFT JOIN gks_eshop_products_brands AS ug2  ON ug1.id_product_brand = ug2.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.id_product_brand = ug3.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.id_product_brand = ug4.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.id_product_brand = ug5.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.id_product_brand = ug6.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.id_product_brand = ug7.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.id_product_brand = ug8.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.id_product_brand = ug9.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.id_product_brand = ug10.product_brand_parent_id
    where ug1.id_product_brand=".$value;
    
    $result_gu = $db_link->query($sql_gu);        
    if (!$result_gu) {
      debug_mail(false,'error sql',$sql_gu);
      die('sql error');
    }
    $gu_in='';
    
    while ($row_gu = $result_gu->fetch_assoc()) {
      if (isset($row_gu['gid1']))  if (in_array($row_gu['gid1'],  $group_ids)==false) $group_ids[]=$row_gu['gid1'];
      if (isset($row_gu['gid2']))  if (in_array($row_gu['gid2'],  $group_ids)==false) $group_ids[]=$row_gu['gid2'];
      if (isset($row_gu['gid3']))  if (in_array($row_gu['gid3'],  $group_ids)==false) $group_ids[]=$row_gu['gid3'];
      if (isset($row_gu['gid4']))  if (in_array($row_gu['gid4'],  $group_ids)==false) $group_ids[]=$row_gu['gid4'];
      if (isset($row_gu['gid5']))  if (in_array($row_gu['gid5'],  $group_ids)==false) $group_ids[]=$row_gu['gid5'];
      if (isset($row_gu['gid6']))  if (in_array($row_gu['gid6'],  $group_ids)==false) $group_ids[]=$row_gu['gid6'];
      if (isset($row_gu['gid7']))  if (in_array($row_gu['gid7'],  $group_ids)==false) $group_ids[]=$row_gu['gid7'];
      if (isset($row_gu['gid8']))  if (in_array($row_gu['gid8'],  $group_ids)==false) $group_ids[]=$row_gu['gid8'];
      if (isset($row_gu['gid9']))  if (in_array($row_gu['gid9'],  $group_ids)==false) $group_ids[]=$row_gu['gid9'];
      if (isset($row_gu['gid10'])) if (in_array($row_gu['gid10'], $group_ids)==false) $group_ids[]=$row_gu['gid10'];
    }
  } 
  
  if (count($group_ids) >0) {
    
    if ($fgroup_100) {
      $sql_FROM_bra_1='((';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id
        from (
          select product_id
          FROM gks_eshop_products_brands_products 
          WHERE product_brand_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_brands_products.product_brand_id In ('.implode(',',$group_ids).')
          AND gks_eshop_products.product_parent_id Is Not Null        
        ) as tabletemp_pbra1
        GROUP BY product_id   
      ) as products_brands_subq on gks_eshop_products.id_product = products_brands_subq.product_id)
      LEFT JOIN (
        SELECT product_id
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra2
        GROUP BY product_id  
      ) as products_brands_subq_all on gks_eshop_products.id_product = products_brands_subq_all.product_id)';
      $where_bra_calc= ' and (products_brands_subq.product_id is not null or products_brands_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
    } else {
      $sql_FROM_bra_1='(';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          WHERE product_brand_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_brands_products.product_brand_id In ('.implode(',',$group_ids).')
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra
        GROUP BY product_id  
      ) as products_brands_subq on gks_eshop_products.id_product = products_brands_subq.product_id)';
      $where_bra_calc= ' and (products_brands_subq.product_id is not null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
      
    }
  } else {
    if ($fgroup_100) {
      $sql_FROM_bra_1='(';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra
        GROUP BY product_id  
      ) as products_brands_subq_all on gks_eshop_products.id_product = products_brands_subq_all.product_id)';
      $where_bra_calc= ' and (products_brands_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
    }
  }
  
  //print '<pre>';
  //print_r($vals);
  //print_r($vals_array);
  //print_r($group_ids);
  //print '</pre>';  
  //print $where_bra_calc;
  //echo $where2;
  //die();

} 




$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;





$sql = "SELECT gks_acc_inv.*,";

$sql.=GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_title, gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,

gks_nomoi.nomos_descr, gks_country.country_name,gks_country.country_ee,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_journal.acc_eidos_parastatikou_id,
eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
gks_lang.lang_name,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
gks_country_dest.country_name as country_name_dest,
gks_nomoi_dest.nomos_descr as nomos_descr_dest,
CASE
  WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
    THEN affect_balance_pros * affect_balance_poso
  ELSE 0
END as affect_balance_calc,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_pos.pos_name,
gks_erp_app_mobile.erp_app_mobile_name,

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
END as product_descr_p,


gks_eshop_products.product_code, 
gks_eshop_products.product_descr, 
gks_acc_inv_products.product_sheets, 
gks_acc_inv_products.product_quantity, 
gks_acc_inv_products.product_price_final_all_net, 
gks_acc_inv_products.product_price_final_all_fpa,
gks_acc_inv_products.product_withheldAmount,
gks_acc_inv_products.product_stampDutyAmount,
gks_acc_inv_products.product_feesAmount,
gks_acc_inv_products.product_otherTaxesAmount,
gks_acc_inv_products.product_deductionsAmount,
gks_acc_inv_products.product_price_final_all_total,

gks_acc_inv_products.product_price_coupon_use, 
gks_acc_inv_products.product_price_coupon_use_disabled


".$gks_custom_acc_inv_prepare['sql_all_list_sele']."
".$gks_custom_eidi_prepare['sql_all_list_sele']."

FROM 
".$gks_custom_acc_inv_prepare['sql_all_list_from']." 
".$gks_custom_eidi_prepare['sql_all_list_from']." 
";


$sql.=" ".$sql_FROM_cat_1." ".$sql_FROM_bra_1." ";

$sql.="((((((((((((((((((((((((((((((  gks_acc_inv_products ";



$sql.="
LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv)
LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product)
";
$sql.=$gks_custom_acc_inv_prepare['sql_all_list_left']." ";
$sql.=$gks_custom_eidi_prepare['sql_all_list_left']." ";

$sql.=" ".$sql_FROM_cat_2." ".$sql_FROM_bra_2." ";


$sql.="
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_inv.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_inv.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang) 
LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method)
LEFT JOIN gks_eshop_fiscal_position ON gks_acc_inv.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_acc_inv.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_acc_inv.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_acc_inv.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_country as gks_country_dest ON gks_acc_inv.destination_data_country_id = gks_country_dest.id_country) 
LEFT JOIN gks_nomoi as gks_nomoi_dest ON gks_acc_inv.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_acc_inv.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_acc_inv.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_acc_inv.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_acc_inv.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_pos ON gks_acc_inv.pos_id=gks_pos.id_pos)
LEFT JOIN gks_erp_app_mobile ON gks_acc_inv.erp_app_mobile_id=gks_erp_app_mobile.id_erp_app_mobile)

";  

$sql.=
"
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_eshop_fpa_base ON gks_eshop_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada
";
//echo '<pre>';echo $sql;die();


$sql.= " where 1=1 ";
$sql.=$where . $where_cat_calc . $where_bra_calc . $search_where;

if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_acc_inv.inv_date desc, gks_acc_inv.id_acc_inv desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
