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

gks_get_hr_program_status($hr_program_status,$hr_program_status_styles);
gks_get_hr_program_vardia($hr_program_vardia,$hr_program_vardia_styles);

$plugin_sql_from_1='';
$plugin_sql_from_2='';
$plugin_sql_from_3='';
$plugin_filters=array();
$plugin_sortable=array();
$plugin_js_date_filters='';

gks_plugins_functions_run('admin_hr_program_filters_step1',array(
  'sql_from_1' => &$plugin_sql_from_1,
  'sql_from_2' => &$plugin_sql_from_2,
  'sql_from_3' => &$plugin_sql_from_3,
  'filters' => &$plugin_filters,
  'sortable'=> &$plugin_sortable,
  'js_date_filters'=> &$plugin_js_date_filters,
));


  
$gks_custom_prepare = gks_custom_table_item_prepare('gks_hr_program',['from'=>'list']);

$filters = array();

$filters[] = array(
  'name' => 'fdate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Καταχώρηση'),
  'has_custom_date' => true,
  'field' => 'gks_hr_program.hr_program_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_hr_program.hr_program_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$vals=array();
foreach ($hr_program_status as $value) {
  if ($value['hr_program_status_disabled']==0) {
    $vals[]=array('value' => $value['id_hr_program_status'], 'text' => $value['hr_program_status_descr'],'sql' => "gks_hr_program.hr_program_status_id=".$value['id_hr_program_status']);
  }
} 
$filters[] = array(
  'name' => 'fstatus',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_hr_program.hr_program_status_id = %V%",
  'vals' => $vals,
);



$filters[] = array(
  'name' => 'fuser',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Υπάλληλος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_hr_program.hr_program_user_id = %V%",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      array('value' => -100, 'text' => gks_lang('Χωρίς πελάτη'),  'sql' => "gks_hr_program.hr_program_user_id=0"),
  ),
  //'mywherepos' =>2,
  'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
  FROM gks_hr_program LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program.hr_program_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
  GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);

$filters[] = array(
  'name' => 'fposto',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πόστο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_hr_program.hr_program_posto_id = %V%",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      array('value' => -100, 'text' => gks_lang('Χωρίς πόστο'),  'sql' => "gks_hr_program.hr_program_posto_id=0"),
  ),
  //'mywherepos' =>2,
  'sql' => "SELECT hr_program_posto_id as id, production_posto_descr as descr
  FROM gks_hr_program 
  LEFT JOIN gks_production_posta ON gks_hr_program.hr_program_posto_id = gks_production_posta.id_production_posto
  WHERE gks_production_posta.id_production_posto Is Not Null
  GROUP BY id_production_posto, production_posto_descr
  ORDER BY production_posto_sortorder;",
);

$vals=array();
foreach ($hr_program_vardia as $value) {
  if ($value['hr_program_vardia_disabled']==0) {
    $vals[]=array('value' => $value['id_hr_program_vardia'], 'text' => $value['hr_program_vardia_descr'],'sql' => "gks_hr_program.hr_program_vardia_id=".$value['id_hr_program_vardia']);
  }
} 
$filters[] = array(
  'name' => 'fvardia',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Βάρδια'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_hr_program.hr_program_vardia_id = %V%",
  'vals' => $vals,
);


$filters[] = array(
  'name' => 'fdatefrom',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία-Από'),
  'has_custom_date' => true,
  'field' => 'gks_hr_program.hr_program_date_from', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,

  'vals' => gks_filter_date_vals(['field'=>'gks_hr_program.hr_program_date_from','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),

);
$filters[] = array(
  'name' => 'fdateto',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία-Έως'),
  'has_custom_date' => true,
  'field' => 'gks_hr_program.hr_program_date_to', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,

  'vals' => gks_filter_date_vals(['field'=>'gks_hr_program.hr_program_date_to','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),

);


$filters[] = array(
  'name' => 'fassigned',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ανάθεση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_hr_program.assigned_id=%V%",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
  ),
  'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
  FROM gks_hr_program LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
  ".($perm_mono_dika_mou==0 ? '' : "and gks_hr_program.id_crm_task in (select crm_task_id from gks_hr_program_employee where crm_task_employee_id=".$my_wp_user_id." group by crm_task_id)")."
  GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);


$filters[] = array(
    'name' => 'fcompany',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hr_program.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_company.id_company AS id, gks_company.company_title AS descr
    FROM gks_hr_program LEFT JOIN gks_company ON gks_hr_program.company_id = gks_company.id_company
    ".($perm_mono_dika_mou==0 ? '' : "where gks_hr_program.id_crm_task in (select crm_task_id from gks_hr_program_employee where crm_task_employee_id=".$my_wp_user_id." group by crm_task_id)")."
    GROUP BY gks_company.id_company, gks_company.company_title
    order by company_sortorder,company_title",
);
$filters[] = array(
    'name' => 'fcompanysub',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Υποκατάστημα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hr_program.company_sub_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hr_program.company_sub_id AS id, if(company_sub_id=0, '".$db_link->escape_string(gks_lang('Κεντρικό'))."', gks_company_subs.company_sub_title) AS descr
    FROM gks_hr_program LEFT JOIN gks_company_subs ON gks_hr_program.company_sub_id = gks_company_subs.id_company_sub
    ".($perm_mono_dika_mou==0 ? '' : "where gks_hr_program.id_crm_task in (select crm_task_id from gks_hr_program_employee where crm_task_employee_id=".$my_wp_user_id." group by crm_task_id)")."
    GROUP BY gks_hr_program.company_sub_id, gks_company_subs.company_sub_title
    order by gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title",
);

$filters=array_merge($filters,$plugin_filters);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_hr_program.id_hr_program'),
  						array('name' => 'sodate', 'field' => 'gks_hr_program.hr_program_date'),
  						array('name' => 'sostatus', 'field' => 'gks_hr_program_status.hr_program_status_sortorder'),
  						array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'soposto', 'field' =>'gks_production_posta.production_posto_sortorder'),
  						array('name' => 'sovardia', 'field' => 'gks_hr_program_vardia.hr_program_vardia_sortorder'),
              array('name' => 'sodatefrom', 'field' => 'gks_hr_program.hr_program_date_from'),
  						array('name' => 'soname', 'field' => 'gks_hr_program.hr_program_name'),
              array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
              array('name' => 'socompany', 'field' => 'gks_company.company_title'),
              array('name' => 'socompany_sub', 'field' => 'gks_company_subs.company_sub_title'),
              


            );
            
            
$sortable=array_merge($sortable,$plugin_sortable);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hr_program_status.hr_program_status_descr',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
'gks_production_posta.production_posto_descr',
'gks_hr_program_vardia.hr_program_vardia_descr',
'hr_program_name',
GKS_WP_TABLE_PREFIX.'users.comm_search',
'gks_users.ma_odos', 
'gks_users.ma_perioxi',
'gks_users.ma_tk',
'gks_users.ma_poli', 
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_company.company_title',
'gks_company_subs.company_sub_title',

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

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hr_program.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
gks_users.ma_country_id,gks_country.country_name,
gks_users.ma_nomos_id,gks_nomoi.nomos_descr,
gks_company.company_title, gks_company_subs.company_sub_title,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned,
gks_production_posta.production_posto_descr
".$gks_custom_prepare['sql_all_list_sele']."
".$plugin_sql_from_1."
FROM ".$gks_custom_prepare['sql_all_list_from']." ".$plugin_sql_from_2." ((((((((((((((gks_hr_program
".$gks_custom_prepare['sql_all_list_left']."
".$plugin_sql_from_3."

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_hr_program.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_hr_program.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_company ON gks_hr_program.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_hr_program.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program.hr_program_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_hr_program_status ON gks_hr_program.hr_program_status_id = gks_hr_program_status.id_hr_program_status)
LEFT JOIN gks_hr_program_vardia ON gks_hr_program.hr_program_vardia_id = gks_hr_program_vardia.id_hr_program_vardia)
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
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hr_program.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID)
LEFT JOIN gks_production_posta ON gks_hr_program.hr_program_posto_id = gks_production_posta.id_production_posto
where 1=1 ".$where . $search_where;
    

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_hr_program.id_hr_program desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}

//echo '<pre>'.$sql; die(); 