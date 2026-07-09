<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets_service',['from'=>'list']);


$filters = array();

$filters[] = array(
    'name' => 'fonservice',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Σε service'),
    'field' => 'gks_assets_service.mydate_return = %V%',
    'has_custom_default' => 2,
    'multiselect' => true,
    'vals' => array(
        array('value' => -1, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 2, 'text' => gks_lang('Ναι'),  'sql' => "(gks_assets_service.mydate_return is null or (gks_assets_service.mydate_return is not null and gks_assets_service.isconfirm=0))"),
        array('value' => 3, 'text' => gks_lang('Όχι'),  'sql' => "gks_assets_service.mydate_return is not null and gks_assets_service.isconfirm<>0"),
    )
);


$filters[] = array(
	'name' => 'fmydate_send',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Αποστολής'),
	'has_custom_date' => true,
	'field' => 'gks_assets_service.mydate_send',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_assets_service.mydate_send','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
    'name' => 'fasset_type',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_type = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ορισμένο'),  'sql' => "(gks_assets.asset_type =0 or gks_assets.asset_type is null)"),
    ),
    'sql' => "SELECT gks_assets_type.id_asset_type as id, gks_assets_type.asset_type_descr as descr
    FROM gks_assets_type ORDER BY asset_type_sortorder;",
);

$filters[] = array(
    'name' => 'fasset_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πάγιο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_service.asset_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_service.asset_id as id, CONCAT_WS(' - ', gks_assets.asset_code,gks_assets.asset_title, gks_assets.asset_serialnumber) as descr
    FROM gks_assets_service LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset
    WHERE (((gks_assets.id_asset) Is Not Null))
    GROUP BY gks_assets_service.asset_id, gks_assets.asset_code, gks_assets.asset_title, gks_assets.asset_serialnumber
    ORDER BY gks_assets.asset_code;",
);



$filters[] = array(
    'name' => 'fwarehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_service.asset_last_warehouse_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        
    ),
    'sql' => "SELECT gks_assets_service.warehouse_id as id, gks_warehouses.warehouse_name as descr
    FROM gks_assets_service LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse
    WHERE (((gks_warehouses.id_warehouse)>0)) 
    GROUP BY gks_assets_service.warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name;",
);

$filters[] = array(
    'name' => 'freason_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αιτία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_service.reason_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_service.reason_id as id, gks_assets_service_reasons.reasons_descr as descr
FROM gks_assets_service LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons
WHERE (((gks_assets_service_reasons.id_assets_service_reasons) Is Not Null))
GROUP BY gks_assets_service.reason_id, gks_assets_service_reasons.reasons_descr
order by gks_assets_service_reasons.reasons_descr",
);

$filters[] = array(
    'name' => 'fmixanikos_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τεχνικός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_service.mixanikos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_service.mixanikos_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
FROM gks_assets_service LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
GROUP BY gks_assets_service.mixanikos_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);


$filters[] = array(
	'name' => 'fmydate_return',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Επιστροφής'),
	'has_custom_date' => true,
	'field' => 'gks_assets_service.mydate_return',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_assets_service.mydate_return','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fisconfirm',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Επιβεβαιωμένο'),
    'field' => 'gks_assets_service.isconfirm = %V%',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 2, 'text' => gks_lang('Ναι'),  'sql' => "gks_assets_service.isconfirm <>0"),
        array('value' => 3, 'text' => gks_lang('Όχι'),  'sql' => "gks_assets_service.isconfirm =0"),
    )
);

$filters[] = array(
    'name' => 'fajia',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αξία'),
    'field' => 'gks_assets_service.ajia = %V%',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 2, 'text' => gks_lang('Μηδέν'),  'sql' => "gks_assets_service.ajia =0"),
        array('value' => 3, 'text' => "&lt;&gt;0",  'sql' => "gks_assets_service.ajia > 0"),
    )
);

$filters[] = array(
    'name' => 'fuser_id_edit',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρήστης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_service.user_id_edit = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_service.user_id_edit as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
FROM gks_assets_service LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.user_id_edit = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
GROUP BY gks_assets_service.user_id_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);






$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_assets_service.id_assets_service'),
  						array('name' => 'somydate_send', 'field' => 'gks_assets_service.mydate_send'),
  						array('name' => 'soasset_code', 'field' => 'gks_assets.asset_code'),
  						array('name' => 'sowarehouse_name', 'field' => 'gks_warehouses.warehouse_name'),
  						array('name' => 'soreasons_descr', 'field' => 'gks_assets_service_reasons.reasons_descr'),
  						array('name' => 'soaitiolog', 'field' => 'gks_assets_service.aitiolog'),
  						array('name' => 'sogks_nickname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'somydate_return', 'field' => 'gks_assets_service.mydate_return'),
  						array('name' => 'soaitiolog2', 'field' => 'gks_assets_service.aitiolog2'),
  						array('name' => 'soajia', 'field' => 'gks_assets_service.ajia'),
  						array('name' => 'souseredit', 'field' => 'wp_users_edit.gks_nickname'),
  						array('name' => 'soisconfirm', 'field' => 'gks_assets_service.isconfirm'),
  						array('name' => 'soip_edit', 'field' => 'gks_assets_service.ip_edit'),



  						
   						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
  'gks_assets.asset_code',
  'gks_assets.asset_title',
  'gks_assets.asset_serialnumber',
  'gks_warehouses.warehouse_name',
  'gks_assets_service_reasons.reasons_descr',
  'gks_assets_service.aitiolog',
  'gks_assets_service.aitiolog2',
   GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  'wp_users_edit.gks_nickname',

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




$sql = "SELECT SQL_CALC_FOUND_ROWS gks_assets_service.*,gks_assets.id_asset, gks_assets.asset_code, gks_assets.asset_photo ,
gks_assets.asset_title, gks_assets.asset_serialnumber, 
gks_assets_service_reasons.reasons_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_warehouses.warehouse_name, wp_users_edit.gks_nickname AS useredit
".$gks_custom_prepare['sql_all_list_sele']."  
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((gks_assets_service 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons) 
LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset) 
LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_edit ON gks_assets_service.user_id_edit = wp_users_edit.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_assets_service.id_assets_service desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}