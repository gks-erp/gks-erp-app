<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets',['from'=>'list']);


$today_vardia_this = date('Y-m-d',_time_user(time(), 1));

$filters = array();

$filters[] = array(
    'name' => 'fasset_type',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_type = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ορισμένο'),  'sql' => "(gks_assets.asset_type =0 or gks_assets.asset_type is null)"),
    ),
    'sql' => "SELECT gks_assets_type.id_asset_type as id, gks_assets_type.asset_type_descr as descr
    FROM gks_assets_type ORDER BY asset_type_sortorder;",
);


$filters[] = array(
    'name' => 'fasset_last_warehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_warehouse_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        
    ),
    'sql' => "SELECT gks_assets.asset_last_warehouse_id as id, gks_warehouses.warehouse_name as descr
    FROM gks_assets LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse
    WHERE (((gks_warehouses.id_warehouse)>0)) 
    GROUP BY gks_assets.asset_last_warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name;",
);


$filters[] = array(
    'name' => 'flast_user_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Συνεργάτης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_user_id = %V%",
    'vals' => array(
        //array('value' => -1,  'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => -3,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.asset_last_user_id =0 or gks_assets.asset_last_user_id is null)"),
        array('value' => -4,  'text' => gks_lang('Ανατεθειμένα'),     'sql' =>  "gks_assets.asset_last_user_id >0"),
    ),
    'sql' => "SELECT gks_assets.asset_last_user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_assets LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID)>0)) 
    GROUP BY gks_assets.asset_last_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);
$filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_company_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.asset_last_company_id =0 or gks_assets.asset_last_company_id is null)"),
        
    ),
    'sql' => "SELECT gks_assets.asset_last_company_id as id, gks_company.company_title as descr
    FROM gks_assets LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company
    WHERE (((gks_company.id_company) Is Not Null))
    GROUP BY gks_assets.asset_last_company_id, gks_company.company_title
    ORDER BY gks_company.company_title;",
);

$filters[] = array(
    'name' => 'fbank_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τράπεζα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.bank_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.bank_id =0 or gks_assets.bank_id is null)"),
        
    ),
    'sql' => "SELECT gks_banks.id_bank as id, gks_banks.bank_descr as descr
FROM gks_assets LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank
WHERE (((gks_banks.id_bank) Is Not Null))
GROUP BY gks_banks.id_bank, gks_banks.bank_descr
ORDER BY gks_banks.bank_descr;",
);


$filters[] = array(
	'name' => 'fdate_agoras',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Ενεργοποίησης'),
	'has_custom_date' => true,
	'field' => 'asset_date_activate',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'asset_date_activate','future'=>false,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this,'set_vardia'=>false,'local_time'=>true]),
);
$filters[] = array(
	'name' => 'fdate_diakopis',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Απόσυρσης'),
	'has_custom_date' => true,
	'field' => 'asset_date_aposirsi',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'asset_date_aposirsi','future'=>false,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this,'set_vardia'=>false,'local_time'=>true]),
);

$filters[] = array(
	'name' => 'fmax_apografi_date',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Απογραφής'),
	'has_custom_date' => true,
	'field' => 'max_apografi_date',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'max_apografi_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fis_fotografou',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είναι του συνεργάτη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),  'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "gks_assets.is_fotografou <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "gks_assets.is_fotografou = 0"),
    ),
);

//
//if (GKS_TRANSFER) {
//$filters[] = array(
//    'name' => 'ftransfer_oxima_type_id',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Τύπος Οχήματος Transfer'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_assets.transfer_oxima_type_id = %V%",
//    'vals' => array(
//        array('value' => -2, 'text' => gks_lang('Δεν έχει ορισθεί'),  'sql' => "gks_assets.transfer_oxima_type_id=0"),
//        array('value' => -3, 'text' => gks_lang('Έχει ορισθεί'),      'sql' => "gks_assets.transfer_oxima_type_id<>0"),
//        
//    ),
//    'sql' => "SELECT gks_assets.transfer_oxima_type_id as id, gks_transfer_oxima_type.transfer_oxima_type_descr as descr
//    FROM gks_assets LEFT JOIN gks_transfer_oxima_type ON gks_assets.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
//    WHERE (((gks_transfer_oxima_type.id_transfer_oxima_type) Is Not Null))
//    GROUP BY gks_assets.transfer_oxima_type_id, gks_transfer_oxima_type.transfer_oxima_type_descr
//    ORDER BY gks_transfer_oxima_type.transfer_oxima_type_descr",
//);
//}

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "asset_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργό'),  'sql' => "asset_disable <> 0"),
    ),
);



$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_assets.id_asset'),
  						array('name' => 'sophoto', 'field' => 'gks_assets.asset_photo'),
  						array('name' => 'sotitle', 'field' => 'gks_assets.asset_title'),
  						array('name' => 'socode', 'field' => 'gks_assets.asset_code'),
  						array('name' => 'soagoras', 'field' => 'gks_assets.asset_date_activate'),
  						array('name' => 'sodiakopis', 'field' => 'gks_assets.asset_date_aposirsi'),
  						array('name' => 'sodisable', 'field' => 'gks_assets.asset_disable'),
  						array('name' => 'sowarehouse_id', 'field' => 'gks_warehouses.warehouse_name'),
  						array('name' => 'souser_id', 'field' => GKS_WP_TABLE_PREFIX."users.gks_nickname"),
  						array('name' => 'soserialnumber', 'field' => 'gks_assets.asset_serialnumber'),
  						array('name' => 'sotype', 'field' => 'gks_assets_type.asset_type_descr'),
  						array('name' => 'sois_fotografou', 'field' => 'gks_assets.is_fotografou'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'sobank_descr', 'field' => 'gks_banks.bank_descr'),
  						array('name' => 'soxreosi_val', 'field' => 'gks_assets.xreosi_val'),
  						array('name' => 'soxreosi_type', 'field' => 'gks_assets.xreosi_type'),
  						array('name' => 'soelastika', 'field' => 'gks_assets.oxima_elastika'),
  						array('name' => 'sokm', 'field' => 'gks_assets.oxima_km'),
  						array('name' => 'sonextskm', 'field' => 'gks_assets.oxima_next_service_km'),
  						array('name' => 'sokteo', 'field' => 'gks_assets.oxima_next_kteo'),
  						array('name' => 'soasf', 'field' => 'gks_assets.oxima_liji_asfaleia'),
  						//array('name' => 'sotransfer_oxima_type_id', 'field' => 'gks_transfer_oxima_type.transfer_oxima_type_descr'),
  						
  						
  						array('name' => 'solastaction', 'field' => 'gks_assets.last_action_date'),
  						array('name' => 'somac', 'field' => 'gks_assets.mac_address'),
  						array('name' => 'sothesi', 'field' => 'gks_assets.asset_thesi'),
  						array('name' => 'soviva', 'field' => 'gks_assets.viva_terminal_id'),
  						array('name' => 'somegeftpos', 'field' => 'gks_assets.megeftpos_terminal_id'),
  						array('name' => 'somellon', 'field' => 'gks_assets.mellon_terminal_id'),
  						array('name' => 'socardlink', 'field' => 'gks_assets.cardlink_terminal_id'),
  						array('name' => 'soapografi_date', 'field' => 'max_apografi_date'),
  						array('name' => 'solifecount', 'field' => 'ds620_40_lifecount'),
  						



   						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_assets.asset_title',
'gks_assets.asset_code',
'gks_assets.asset_serialnumber',
'gks_assets.asset_sxolio',
'gks_assets_type.asset_type_descr',
GKS_WP_TABLE_PREFIX."users.gks_nickname",
'gks_warehouses.warehouse_name',

'gks_assets_type.asset_type_descr',
'gks_company.company_title',
'gks_assets.mac_address',
'gks_assets.asset_thesi',
'gks_assets.viva_terminal_id',
'gks_assets.megeftpos_terminal_id',
'gks_assets.cardlink_terminal_id',
'gks_assets.mellon_terminal_id',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_assets.*, gks_assets_type.asset_type_descr,  gks_warehouses.warehouse_name, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_company.company_title,
gks_banks.bank_descr,
lastactionergas.warehouse_name as last_action_warehouse_name,
max_apografi_date
".$gks_custom_prepare['sql_all_list_sele']."  
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((gks_assets 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type) 
LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company)
LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank)
LEFT JOIN gks_warehouses as lastactionergas ON gks_assets.last_action_warehouse_id = lastactionergas.id_warehouse)
LEFT JOIN (
  SELECT gks_assets_whi_mov_assets.asset_id, max(gks_assets_whi_mov.mydate) AS max_apografi_date
  FROM gks_assets_whi_mov_assets LEFT JOIN gks_assets_whi_mov ON gks_assets_whi_mov_assets.assets_whi_mov_id = gks_assets_whi_mov.id_assets_whi_mov
  WHERE gks_assets_whi_mov_assets.posotita_found>=1
  AND gks_assets_whi_mov.assets_whi_mov_status='99complete'
  GROUP BY gks_assets_whi_mov_assets.asset_id
) as table_max_apografi_date ON gks_assets.id_asset = table_max_apografi_date.asset_id



where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_assets.id_asset desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
//echo '<pre>'.$sql.'</pre>';//die();

