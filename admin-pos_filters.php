<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



$gks_custom_prepare = gks_custom_table_item_prepare('gks_pos',['from'=>'list']);


$filters = array();





//print '<pre>';print_r($user_companys);die();

//if (count($user_companys)>1) {
  $vals=array();
  foreach ($user_companys as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_pos.pos_company_id=".$value['id_company']." and gks_pos.pos_company_sub_id=".$value['id_company_sub']);
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
//}

$filters[] = array(
  'name' => 'fjournal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ημερολόγιο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_pos.pos_journal_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_pos.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_pos.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr
FROM gks_pos LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal
WHERE (((gks_acc_journal.id_acc_journal) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
  'field'  => "gks_pos.pos_seira_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_pos.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_pos.user_id<>0"),
  ),
  'sql' => "SELECT gks_acc_seires.id_acc_seira AS id, gks_acc_seires.seira_descr AS descr
FROM gks_pos LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira
WHERE (((gks_acc_seires.id_acc_seira) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
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
  'field'  => "gks_pos.def_user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_pos.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_pos.user_id<>0"),
  ),
  'sql' => "SELECT gks_pos.def_user_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
FROM gks_pos LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."

GROUP BY gks_pos.def_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",    
);









$filters[] = array(
  'name' => 'fdwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Αποστολής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_pos.def_tropos_apostolis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_pos.def_tropos_apostolis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_pos.def_tropos_apostolis<>0"),
  ),
  'sql' => "SELECT gks_delivery_methods.id_delivery_method AS id, gks_delivery_methods.delivery_method_name AS descr
  FROM gks_pos 
  LEFT JOIN gks_delivery_methods ON gks_pos.def_tropos_apostolis = gks_delivery_methods.id_delivery_method
  WHERE gks_pos.def_tropos_apostolis>0 AND gks_delivery_methods.id_delivery_method>0
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."

  GROUP BY gks_delivery_methods.id_delivery_method, gks_delivery_methods.delivery_method_name, gks_delivery_methods.mysortorder",    
);

$filters[] = array(
  'name' => 'fpwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Πληρωμής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_pos.def_tropos_pliromis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_pos.def_tropos_pliromis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_pos.def_tropos_pliromis<>0"),
  ),
  'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name AS descr
  FROM gks_pos LEFT JOIN gks_payment_acquirers ON gks_pos.def_tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  where gks_pos.def_tropos_pliromis>0 and gks_payment_acquirers.id_payment_acquirer > 0
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."

  GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
  ORDER BY gks_payment_acquirers.mysortorder",    
);




$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_pos.def_assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_pos LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
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
      'field'  => "gks_pos.def_crm_channel_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
      FROM gks_pos LEFT JOIN gks_crm_channel_sale ON gks_pos.def_crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
      WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
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
      'field'  => "gks_pos.def_crm_channel_contact_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_pos LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
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
      'field'  => "gks_pos.def_crm_channel_campain_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
      FROM gks_pos 
      LEFT JOIN gks_ads_campain ON gks_pos.def_crm_channel_campain_id = gks_ads_campain.id_ads_campain
      WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
".(count($perm_id_company_ids)>0 ? " and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
      ORDER BY gks_ads_campain.ads_campain_name",
  );
}

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_pos.id_pos'),
  array('name' => 'soname', 'field' => 'gks_pos.pos_name'),
  array('name' => 'sodescr', 'field' => 'gks_pos.pos_descr'),
  array('name' => 'socompany', 'field' => 'gks_company.company_title, gks_company_subs.company_sub_title'),
  array('name' => 'sojournal', 'field' => 'gks_acc_journal.acc_journal_descr'),
  array('name' => 'soseira', 'field' => 'gks_acc_seires.seira_code'),
  array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  array('name' => 'somaxa', 'field' => 'gks_pos.pos_max_ammount'),
  array('name' => 'soprice', 'field' => 'gks_price_net'),
  array('name' => 'sonetfpa', 'field' => 'gks_price_netfpa'),
  array('name' => 'sowithheld', 'field' => 'totalWithheldAmount'),
  //array('name' => 'sobalance', 'field' => 'gks_pos.inv_state, affect_balance,affect_balance_poso*affect_balance_pros'),
  array('name' => 'sobalance', 'field' => 'affect_balance_calc'),
  
  array('name' => 'sopa', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  array('name' => 'sode', 'field' => 'gks_delivery_methods.delivery_method_name'),
  
  array('name' => 'soocc', 'field' => 'gks_occasion_types.occasion_type_descr'),
  array('name' => 'souedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
	array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
	array('name' => 'sochannel', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
	array('name' => 'sochcontact', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
	array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
	array('name' => 'socrmcode', 'field' => 'gks_pos.def_crm_channel_code'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);




$search_fields = array(
'gks_pos.pos_name',
'gks_pos.pos_descr',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.display_name',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
'gks_users.eponimia',
'gks_users.title',
'gks_users.afm',
'gks_users.epaggelma',
'gks_users.ma_odos',
'gks_users.ma_perioxi',
'gks_users.ma_poli',
'gks_users.ma_tk',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_payment_acquirers.payment_acquirer_name',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',
'gks_pos.def_crm_channel_code',
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



$sql="SELECT SQL_CALC_FOUND_ROWS gks_pos.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add,
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
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
myfirst_name,mylast_name,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((((((((((((((((((((((gks_pos
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
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id

";

//echo '<pre>';echo $sql;die();


$sql.= " where 1=1 ";
$sql.=$where . $search_where;

if (count($perm_id_pos_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_ids).")";

if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_pos.id_pos desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
//echo '<pre>';print $sql;die();
