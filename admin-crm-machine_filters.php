<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
if (isset($gks_from_pivot)==false) $gks_from_pivot=false;
if ($gks_from_pivot) {
  //if (isset($_GET['fstatus'])==false) $_GET['fstatus']='-1';
  //if (isset($_GET['flead_date'])==false) $_GET['flead_date']='18';
  
} else {
  //if (isset($_GET['fstatus'])==false) $_GET['fstatus']='1,20,50';
}


$plugin_sql_from_1='';
$plugin_sql_from_2='';
$plugin_sql_from_3='';
$plugin_filters=array();
$plugin_sortable=array();
$plugin_js_date_filters='';

gks_plugins_functions_run('admin_crm_machine_filters_step1',array(
  'sql_from_1' => &$plugin_sql_from_1,
  'sql_from_2' => &$plugin_sql_from_2,
  'sql_from_3' => &$plugin_sql_from_3,
  'filters' => &$plugin_filters,
  'sortable'=> &$plugin_sortable,
  'js_date_filters'=> &$plugin_js_date_filters,
));


  
$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_machine',['from'=>'list']);

$filters = array();

$filters[] = array(
  'name' => 'fmydate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Προσθήκη'),
  'has_custom_date' => true,
  'field' => 'gks_crm_machine.mydate_add', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_crm_machine.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fproduct',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είδος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_machine_product_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Είδος'),  'sql' => "crm_machine_product_id=0"),
    ),
    //'mywherepos' =>2,
    'sql' => "SELECT gks_eshop_products.id_product as id, 
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
      END as descr
  
    FROM (gks_crm_machine 
    LEFT JOIN gks_eshop_products ON gks_crm_machine.crm_machine_product_id = gks_eshop_products.id_product)
    LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

    WHERE (((gks_eshop_products.id_product) Is Not Null))
    GROUP BY gks_eshop_products.id_product, descr

    ORDER BY descr",
);

$filters[] = array(
    'name' => 'fbrand',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μάρκα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_machine_brand_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Μάρκα'),  'sql' => "crm_machine_brand_id=0"),
    ),
    //'mywherepos' =>2,
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
    FROM (((((((((gks_eshop_products_brands
    LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand)
    LEFT JOIN gks_crm_machine on gks_eshop_products_brands.id_product_brand=gks_crm_machine.crm_machine_brand_id
    where gks_crm_machine.crm_machine_brand_id is not null
    GROUP BY id, descr
    ORDER BY descr",
);

$filters[] = array(
    'name' => 'fuser',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πελάτης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_machine.crm_machine_user_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς πελάτη'),  'sql' => "gks_crm_machine.crm_machine_user_id=0"),
    ),
    //'mywherepos' =>2,
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_crm_machine LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);

$filters[] = array(
    'name' => 'faddress',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τόπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_users_extra_address.ea_name = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς τόπο'),  'sql' => "gks_crm_machine.users_extra_address_id=0"),
        array('value' => -200, 'text' => gks_lang('Βασική'),  'sql' => "gks_crm_machine.users_extra_address_id=-1"),
    ),
    //'mywherepos' =>2,
    'sql' => "SELECT gks_users_extra_address.ea_name as id, gks_users_extra_address.ea_name as descr
    FROM gks_crm_machine LEFT JOIN gks_users_extra_address ON gks_crm_machine.users_extra_address_id = gks_users_extra_address.id_users_extra_address
    WHERE (((gks_users_extra_address.id_users_extra_address) Is Not Null))
    GROUP BY gks_users_extra_address.ea_name
    ORDER BY gks_users_extra_address.ea_name;",
);


$filters=array_merge($filters,$plugin_filters);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_crm_machine.id_crm_machine'),
  						array('name' => 'soname', 'field' => 'gks_crm_machine.crm_machine_name'),
  						array('name' => 'soserial', 'field' => 'gks_crm_machine.crm_machine_serial_number'),
  						array('name' => 'soproduct', 'field' => 'product_descr_p'),
  						array('name' => 'sobrand', 'field' => 'brand_fullpath'),
  						array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'soodos', 'field' => 'showodos,showperioxi'),
  						array('name' => 'soperioxi', 'field' => 'showperioxi'),
  						array('name' => 'sopoli', 'field' => 'showpoli,showtk'),
  						array('name' => 'sotk', 'field' => 'showtk'),
  						array('name' => 'soaddress', 'field' => 'gks_users_extra_address.ea_name'),

            );
$sortable=array_merge($sortable,$plugin_sortable);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'crm_machine_name',
'crm_machine_serial_number',
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.comm_search',
'gks_users.ma_odos', 
'gks_users.ma_perioxi',
'gks_users.ma_tk',
'gks_users.ma_poli', 
'gks_users_extra_address.ea_name',
'gks_users_extra_address.ea_odos', 
'gks_users_extra_address.ea_perioxi',
'gks_users_extra_address.ea_tk',
'gks_users_extra_address.ea_poli', 




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


//echo '<pre>';var_dump($plugin_sql_from_1);var_dump($plugin_sql_from_2);die();

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_crm_machine.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,

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
".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
gks_users.ma_country_id,gks_country.country_name,
gks_users.ma_nomos_id,gks_nomoi.nomos_descr,

gks_users_extra_address.ea_name,
gks_users_extra_address.ea_poli,
if(users_extra_address_id=-1, gks_users.ma_odos, gks_users_extra_address.ea_odos) as showodos,
if(users_extra_address_id=-1, gks_users.ma_arithmos, gks_users_extra_address.ea_arithmos) as showarithmos,
if(users_extra_address_id=-1, gks_users.ma_orofos, gks_users_extra_address.ea_orofos) as showorofos,
if(users_extra_address_id=-1, gks_users.ma_perioxi, gks_users_extra_address.ea_perioxi) as showperioxi,
if(users_extra_address_id=-1, gks_users.ma_poli, gks_users_extra_address.ea_poli) as showpoli,
if(users_extra_address_id=-1, gks_users.ma_tk, gks_users_extra_address.ea_tk) as showtk,
mybrand.fullpath as brand_fullpath
".$gks_custom_prepare['sql_all_list_sele']."
".$plugin_sql_from_1."
FROM ".$gks_custom_prepare['sql_all_list_from']." ".$plugin_sql_from_2." ((((((((((((gks_crm_machine
".$gks_custom_prepare['sql_all_list_left']."
".$plugin_sql_from_3."

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_crm_machine.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_crm_machine.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_eshop_products ON gks_crm_machine.crm_machine_product_id = gks_eshop_products.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
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
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
LEFT JOIN gks_users_extra_address ON gks_crm_machine.users_extra_address_id = gks_users_extra_address.id_users_extra_address)
LEFT JOIN (
  SELECT gks_eshop_products_brands.id_product_brand,
  gks_eshop_products_brands.product_brand_descr,
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
                  gks_eshop_products_brands.product_brand_descr) as fullpath
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
) as mybrand on gks_crm_machine.crm_machine_brand_id =mybrand.id_product_brand
where 1=1 ".$where . $search_where;
    

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_crm_machine.id_crm_machine desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}

//echo '<pre>'.$sql; die(); 