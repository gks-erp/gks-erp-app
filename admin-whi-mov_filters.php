<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


$gks_custom_prepare = gks_custom_table_item_prepare('gks_whi_mov',['from'=>'list']);

$today_vardia_this = date('Y-m-d',_time_user(time(), 1));

$filters = array();

$filters[] = array(
  'name' => 'fstate',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_whi_mov.mov_state = '%V%'",
  'vals' => array(
    array('value' => 111, 'text' => getWhiMovStateDescr('010draft'),      'sql' => "gks_whi_mov.mov_state='010draft'"),
    array('value' => 100, 'text' => getWhiMovStateDescr('040cancelled'),  'sql' => "gks_whi_mov.mov_state='040cancelled'"),
    array('value' => 102, 'text' => getWhiMovStateDescr('080listing'),    'sql' => "gks_whi_mov.mov_state='080listing'"),
    array('value' => 103, 'text' => getWhiMovStateDescr('090ekdosi'),     'sql' => "gks_whi_mov.mov_state='090ekdosi'"),
    array('value' => 109, 'text' => getWhiMovStateDescr('100payment'),    'sql' => "gks_whi_mov.mov_state='100payment'"),
  ),
);

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_whi_mov.mov_date', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_whi_mov.mov_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);

//print '<pre>';print_r($user_companys);die();

if (count($user_companys)>1) {
  $vals=array();
  foreach ($user_companys as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_whi_mov.company_id=".$value['id_company']." and gks_whi_mov.company_sub_id=".$value['id_company_sub']);
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
  'field'  => "gks_whi_mov.mov_whi_journal_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_whi_mov.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_whi_mov.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr
FROM gks_whi_mov LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal
WHERE (((gks_acc_journal.id_acc_journal) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."

GROUP BY gks_acc_journal.id_acc_journal, gks_acc_journal.acc_journal_descr
ORDER BY gks_acc_journal.acc_journal_descr;",    
);

$filters[] = array(
  'name' => 'fseira',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Σειρά'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_whi_mov.mov_whi_seira_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_whi_mov.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_whi_mov.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_seires.id_acc_seira AS id, gks_acc_seires.seira_descr AS descr
FROM gks_whi_mov LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
WHERE (((gks_acc_seires.id_acc_seira) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_acc_seires.id_acc_seira, gks_acc_seires.seira_descr
ORDER BY gks_acc_seires.seira_descr;",    
);


$filters[] = array(
  'name' => 'fprint_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Εκτύπωση'),
  'has_custom_date' => true,
  'field' => 'gks_whi_mov.print_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_whi_mov.print_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia, 'extra10'=> array(
  	array('value' => 102,
  				'text' => gks_lang('Δεν έχει ορισθεί'),
  				'sql' => "gks_whi_mov.print_date is null"),
  	array('value' => 103,
  				'text' => gks_lang('Έχει ορισθεί'),
  				'sql' => "gks_whi_mov.print_date is not null"),
  )]),
);

$filters[] = array(
  'name' => 'faade_send_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Αποστολή ΑΑΔΕ'),
  'has_custom_date' => true,
  'field' => 'gks_whi_mov.aade_send_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_whi_mov.aade_send_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "gks_whi_mov.aade_send_date is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "gks_whi_mov.aade_send_date is not null"),
    ),
  ]),
);

$filters[] = array(
  'name' => 'faade_mark',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('ΜΑΡΚ'),
  'has_custom_date' => true,
  'field' => 'gks_whi_mov.aade_invoicemark', 
  'has_custom_default' => -1,
  'multiselect' => true,
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "(gks_whi_mov.aade_invoicemark is null or gks_whi_mov.aade_invoicemark='')"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "(gks_whi_mov.aade_invoicemark <>'')"),
  ),
);
$filters[] = array(
  'name' => 'fcustomer',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Επαφή'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_whi_mov.user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_whi_mov.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_whi_mov.user_id<>0"),
  ),
  'sql' => "SELECT gks_whi_mov.user_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
FROM gks_whi_mov LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
GROUP BY gks_whi_mov.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",    
);

$filters[] = array(
    'name' => 'fpoli',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_whi_mov.ma_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ma_poli as id, ma_poli as descr FROM gks_whi_mov 
    where ma_poli<>'' 
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    GROUP BY ma_poli ORDER BY ma_poli;",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_whi_mov.ma_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_nomoi.id_nomos as id, gks_nomoi.nomos_descr as descr
    FROM gks_whi_mov LEFT JOIN gks_nomoi ON gks_whi_mov.ma_nomos_id = gks_nomoi.id_nomos
    WHERE (((gks_nomoi.id_nomos) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
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
    'field'  => "gks_whi_mov.ma_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_country.id_country as id, gks_country.country_name as descr
    FROM gks_whi_mov LEFT JOIN gks_country ON gks_whi_mov.ma_country_id = gks_country.id_country
    WHERE (((gks_country.id_country) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    GROUP BY gks_country.id_country, gks_country.country_name",
);




$filters[] = array(
  'name' => 'fdwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Αποστολής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_whi_mov.tropos_apostolis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_whi_mov.tropos_apostolis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_whi_mov.tropos_apostolis<>0"),
  ),
  'sql' => "SELECT gks_delivery_methods.id_delivery_method AS id, gks_delivery_methods.delivery_method_name AS descr
  FROM gks_whi_mov LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method
  WHERE gks_whi_mov.tropos_apostolis>0 AND gks_delivery_methods.id_delivery_method>0
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
  GROUP BY gks_delivery_methods.id_delivery_method, gks_delivery_methods.delivery_method_name, gks_delivery_methods.mysortorder",    
);


$filters[] = array(
  'name' => 'fdispatch_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Αποστολή'),
  'has_custom_date' => true,
  'field' => 'gks_whi_mov.dispatch_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_whi_mov.dispatch_date','future'=>false,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this,'set_vardia'=>false,'local_time'=>true, 'extra10'=> array(
  	array('value' => 102,
  				'text' => gks_lang('Δεν έχει ορισθεί'),
  				'sql' => "gks_whi_mov.dispatch_date is null"),
  	array('value' => 103,
  				'text' => gks_lang('Έχει ορισθεί'),
  				'sql' => "gks_whi_mov.dispatch_date is not null"),
  )]),

);


$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_whi_mov.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_whi_mov LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
      'field'  => "gks_whi_mov.crm_channel_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
      FROM gks_whi_mov LEFT JOIN gks_crm_channel_sale ON gks_whi_mov.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
      WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
      'field'  => "gks_whi_mov.crm_channel_contact_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_whi_mov LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
      'field'  => "gks_whi_mov.crm_channel_campain_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
      FROM gks_whi_mov 
      LEFT JOIN gks_ads_campain ON gks_whi_mov.crm_channel_campain_id = gks_ads_campain.id_ads_campain
      WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
      ORDER BY gks_ads_campain.ads_campain_name",
  );
}

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_whi_mov.id_whi_mov'),
  array('name' => 'sood', 'field' => 'gks_whi_mov.mov_date'),
  array('name' => 'socompany', 'field' => 'gks_company.company_title, gks_company_subs.company_sub_title'),
  array('name' => 'sojournal', 'field' => 'gks_acc_journal.acc_journal_descr'),
  array('name' => 'soseira', 'field' => 'gks_acc_seires.seira_code'),
  array('name' => 'sonumber', 'field' => 'gks_whi_mov.mov_whi_number_int'),
  
  
  
  array('name' => 'soprint_date', 'field' => 'gks_whi_mov.print_date'),
  array('name' => 'soaade_send_date', 'field' => 'gks_whi_mov.aade_send_date'),
  array('name' => 'sostate', 'field' => 'gks_whi_mov.mov_state'),
  array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  array('name' => 'soposotita', 'field' => 'gks_whi_mov.products_posotita'),

  
  array('name' => 'sode', 'field' => 'gks_delivery_methods.delivery_method_name'),
  
  array('name' => 'sodispatch_date', 'field' => 'gks_whi_mov.dispatch_date'),
  array('name' => 'soocc', 'field' => 'gks_occasion_types.occasion_type_descr'),
  array('name' => 'souedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  array('name' => 'sopoli', 'field' => 'gks_whi_mov.ma_poli'),
	array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
	array('name' => 'sochannel', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
	array('name' => 'sochcontact', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
	array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
  array('name' => 'socrmcode', 'field' => 'gks_whi_mov.crm_channel_code'),
	
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);




$search_fields = array(
'gks_whi_mov.mov_state',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.display_name',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
'gks_whi_mov.eponimia',
'gks_whi_mov.title',
'gks_whi_mov.afm',
'gks_whi_mov.epaggelma',
'gks_whi_mov.ma_odos',
'gks_whi_mov.ma_perioxi',
'gks_whi_mov.ma_poli',
'gks_whi_mov.ma_tk',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',

'gks_whi_mov.note_logistirio',
'gks_whi_mov.note_doc',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',
'gks_whi_mov.crm_channel_code',
'gks_whi_mov.aade_invoicemark',
);
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
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;





$sql = "SELECT SQL_CALC_FOUND_ROWS gks_whi_mov.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_journal.acc_eidos_parastatikou_id,
eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev,
gks_lang.lang_name,gks_country.country_name,gks_country.country_ee,
gks_nomoi.nomos_descr,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
gks_country_dest.country_name as country_name_dest,
gks_nomoi_dest.nomos_descr as nomos_descr_dest,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((((((((((((((((((((gks_whi_mov
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_whi_mov.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_whi_mov.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_whi_mov.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_whi_mov.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_whi_mov.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_whi_mov.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_country as gks_country_dest ON gks_whi_mov.destination_data_country_id = gks_country_dest.id_country) 
LEFT JOIN gks_nomoi as gks_nomoi_dest ON gks_whi_mov.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
LEFT JOIN gks_lang ON gks_whi_mov.user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_whi_mov.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_whi_mov.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_whi_mov.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_whi_mov.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_whi_mov.crm_channel_campain_id = gks_ads_campain.id_ads_campain

";

  
//echo '<pre>';echo $sql;die();


$sql.= " where 1=1 ";
if (isset($_GET['fparoxos'])) {
  $sql.= " and gks_whi_mov.aade_paroxos_id=".intval($_GET['fparoxos'])." ";
}
if (isset($_GET['paroxos_tf1_url_has'])) {
  $sql.= " and gks_whi_mov.paroxos_tf1_url_has=".intval($_GET['paroxos_tf1_url_has'])." ";
}
$sql.=$where . $search_where;

if (count($perm_id_company_ids)>0) $sql.=" and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_whi_mov.mov_date desc, gks_whi_mov.id_whi_mov desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
