<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


$gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'list']);


$filters = array();


 
$filters[] = array(
	'name' => 'fdate_add',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
	'title' => gks_lang('Ημερομηνία Προσθήκης'),
	'has_custom_date' => true,
	'field' => GKS_WP_TABLE_PREFIX.'users.user_registered', 
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>GKS_WP_TABLE_PREFIX.'users.user_registered','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);



$filters[] = array(
    'name' => 'frole',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ρόλος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities like '%V%'",
    'vals' => getUserRolesArray(GKS_WP_TABLE_PREFIX.'users.gks_wp_capabilities'),
);




$filters[] = array(
    'name' => 'fgroup',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ομάδα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Ομάδα'),          'sql' => "gks_users_groups_users.group_id is null"),
    ),
    'mywherepos' =>1,
    'sql' => "select gks_users_groups.id_users_group as id, 
    CONCAT_WS('\\\\',
                     ug10.group_title,
                     ug9.group_title,
                     ug8.group_title,
                     ug7.group_title,
                     ug6.group_title,
                     ug5.group_title,
                     ug4.group_title,
                     ug3.group_title,
                     ug2.group_title,
                     gks_users_groups.group_title) as descr
    FROM ((((((((gks_users_groups
    LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
    LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
    LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
    LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
    LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
    LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
    LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
    LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
    LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group
    ORDER BY descr",
);



$filters[] = array(
    'name' => 'ffiscal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Φορολογική Θέση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => GKS_WP_TABLE_PREFIX."users.fiscal_position_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_eshop_fiscal_position.id_fiscal_position as id, gks_eshop_fiscal_position.fiscal_position_descr as descr
    FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position
    WHERE (((gks_eshop_fiscal_position.id_fiscal_position) Is Not Null))
    GROUP BY gks_eshop_fiscal_position.id_fiscal_position
    ORDER BY gks_eshop_fiscal_position.fiscal_position_sortorder",
);

$filters[] = array(
    'name' => 'fpricelist',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τιμοκατάλογος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => GKS_WP_TABLE_PREFIX."users.pricelist_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_eshop_pricelist.id_pricelist as id, gks_eshop_pricelist.pricelist_descr as descr
    FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist
    WHERE (((gks_eshop_pricelist.id_pricelist) Is Not Null))
    GROUP BY gks_eshop_pricelist.id_pricelist, gks_eshop_pricelist.pricelist_descr",
);
$filters[] = array(
    'name' => 'fpoli',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_users.ma_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ma_poli as id, ma_poli as descr FROM gks_users where ma_poli<>'' GROUP BY ma_poli ORDER BY ma_poli;",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_users.ma_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_nomoi.id_nomos as id, gks_nomoi.nomos_descr as descr
    FROM (".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos
    WHERE (((gks_nomoi.id_nomos) Is Not Null))
    GROUP BY gks_nomoi.id_nomos, gks_nomoi.nomos_descr
    ORDER BY gks_nomoi.nomos_descr",
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true, 
    'field'  => "gks_users.ma_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς χώρα'),          'sql' => "(gks_users.ma_country_id is null or gks_users.ma_country_id=0)"),
    ),
    'sql' => "SELECT gks_country.id_country as id, gks_country.country_name as descr
    FROM (".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country
    WHERE (((gks_country.id_country) Is Not Null))
    GROUP BY gks_country.id_country, gks_country.country_name
    ORDER BY gks_country.country_name",
);



$filters[] = array(
    'name' => 'fmymoobile',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κινητό'),
    'has_custom_default' => -1,
    'multiselect' => true, 
    'field'  => GKS_WP_TABLE_PREFIX."users.gks_mobile = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),                   'sql' => "1=1"),
        array('value' => 1,  'text' => gks_lang('Έχει ορισθεί'),          'sql' => GKS_WP_TABLE_PREFIX."users.gks_mobile is not null"),
        array('value' => 2,  'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => GKS_WP_TABLE_PREFIX."users.gks_mobile is null"),
    ),

);
$filters[] = array(
    'name' => 'fgenisi_date',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ημερ. Γέννησης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_users.genisi_date = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),                   'sql' => "1=1"),
        array('value' => 1,  'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_users.genisi_date is not null"),
        array('value' => 2,  'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_users.genisi_date is null"),
    ),
);


$filters[] = array(
    'name' => 'fviber_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Viber'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "".GKS_WP_TABLE_PREFIX."users.viber_id = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),                   'sql' => "1=1"),
        array('value' => 1,  'text' => gks_lang('Έχει ορισθεί'),          'sql' => GKS_WP_TABLE_PREFIX."users.viber_id <>''"),
        array('value' => 2,  'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "(".GKS_WP_TABLE_PREFIX."users.viber_id is not null or ".GKS_WP_TABLE_PREFIX."users.viber_id ='')"),
    ),
);

$filters[] = array(
    'name' => 'fbalance',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Υπόλοιπο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => GKS_WP_TABLE_PREFIX."users.gks_balance = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),                   'sql' => "1=1"),
        array('value' => 1,  'text' => gks_lang('Έχει'),          'sql' => GKS_WP_TABLE_PREFIX."users.gks_balance<>0"),
        array('value' => 2,  'text' => gks_lang('Δεν έχει'),      'sql' => GKS_WP_TABLE_PREFIX."users.gks_balance=0"),
        array('value' => 3,  'text' => gks_lang('Θετικό'),        'sql' => GKS_WP_TABLE_PREFIX."users.gks_balance>0"),
        array('value' => 4,  'text' => gks_lang('Αρνητικό'),      'sql' => GKS_WP_TABLE_PREFIX."users.gks_balance<0"),
        
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);

$sortable = array(
  						array('name' => 'soid', 'field' => GKS_WP_TABLE_PREFIX.'users.id'),
  						array('name' => 'soadddate', 'field' => GKS_WP_TABLE_PREFIX.'users.user_registered'),
  						array('name' => 'sousername', 'field' => GKS_WP_TABLE_PREFIX.'users.user_login'),
  						array('name' => 'sodname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_fullname'),
  						array('name' => 'sonickname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'soemail', 'field' => GKS_WP_TABLE_PREFIX.'users.user_email'),
  						array('name' => 'somobile', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_mobile'),
  						array('name' => 'soroles', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_wp_capabilities'),
  						array('name' => 'sofiscal', 'field' => 'gks_eshop_fiscal_position.fiscal_position_descr'),
  						array('name' => 'sopricelist', 'field' => 'gks_eshop_pricelist.pricelist_descr'),
  						array('name' => 'soafm', 'field' => 'gks_users.afm'),
  						array('name' => 'sotitle', 'field' => 'gks_users.title'),
  						array('name' => 'soeponimia', 'field' => 'gks_users.eponimia'),
  						array('name' => 'soodos', 'field' => 'gks_users.ma_odos'),
  						array('name' => 'sopoli', 'field' => 'gks_users.ma_poli'),
  						array('name' => 'sotk', 'field' => 'gks_users.ma_tk'),
  						array('name' => 'sonomos', 'field' => 'gks_nomoi.nomos_descr'),
  						array('name' => 'soxora', 'field' => 'gks_country.country_name'),
  						array('name' => 'sopuser', 'field' => 'gks_users.profilepososto_user'),
  						array('name' => 'sopjob', 'field' => 'gks_users.profilepososto_job'),
  						array('name' => 'solastupdate', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_last_update'),
  						array('name' => 'sogenisi_date', 'field' => 'gks_users.genisi_date'),
              array('name' => 'soso', 'field' => GKS_WP_TABLE_PREFIX.'users.usr_sintelestes_pososto0,".GKS_WP_TABLE_PREFIX."users.usr_sintelestes_poso1,".GKS_WP_TABLE_PREFIX."users.usr_sintelestes_pososto1'),
              array('name' => 'sodfepidotisi_eisprajis', 'field' => 'dfepidotisi_eisprajis'),
              array('name' => 'sogekprosi', 'field' => GKS_WP_TABLE_PREFIX.'users.generic_ekprosi'),
              array('name' => 'sotziros', 'field' => 'tbl_tziros.user_tziros'),
              array('name' => 'sobalance', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_balance'),
              
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
GKS_WP_TABLE_PREFIX.'users.user_login',
GKS_WP_TABLE_PREFIX.'users.user_nicename',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.user_url',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_pass_pure',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
//'gks_eshop_fiscal_position.fiscal_position_descr',
//'gks_eshop_pricelist.pricelist_descr',
'gks_users.eponimia',
'gks_users.title',
'gks_users.afm',
'gks_users.doy',
'gks_users.epaggelma',
'gks_users.ma_odos',
'gks_users.ma_poli',
'gks_users.ma_tk',
'gks_nomoi.nomos_descr',
'gks_country.country_name',
GKS_WP_TABLE_PREFIX.'users.gks_wp_capabilities',
'gks_users.arithmos_tautoitas',
'gks_users.arxi_ekdosis',
'gks_users.onoma_patera',
'gks_users.onoma_miteras',
'gks_users.amka',
'gks_users.ama_eam',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users.viber_id',
GKS_WP_TABLE_PREFIX.'users.comm_search',
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
$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';


$where1_calc='';
if ($where1!='') {
  //print  $where1;
  //die();
  //AND (gks_users_groups_users.group_id is null or 38, or 37,)
  //AND (35, or 33, or 27,)
  
  $fgroup_100=false;
  if (strpos($where1, 'gks_users_groups_users.group_id is null') !== false) $fgroup_100=true;
  $where1=str_replace('gks_users_groups_users.group_id is null or ', '', $where1);
  
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
    $sql_gu="SELECT ug1.id_users_group AS gid1, 
                 ug2.id_users_group AS gid2, 
                 ug3.id_users_group AS gid3, 
                 ug4.id_users_group AS gid4, 
                 ug5.id_users_group AS gid5,
                 ug6.id_users_group AS gid6,
                 ug7.id_users_group AS gid7,
                 ug8.id_users_group AS gid8,
                 ug9.id_users_group AS gid9,
                 ug10.id_users_group AS gid10
    FROM ((((((((gks_users_groups as ug1
    LEFT JOIN gks_users_groups AS ug2 ON ug1.id_users_group = ug2.group_parent_id)
    LEFT JOIN gks_users_groups AS ug3 ON ug2.id_users_group = ug3.group_parent_id)
    LEFT JOIN gks_users_groups AS ug4 ON ug3.id_users_group = ug4.group_parent_id)
    LEFT JOIN gks_users_groups AS ug5 ON ug4.id_users_group = ug5.group_parent_id)
    LEFT JOIN gks_users_groups AS ug6 ON ug5.id_users_group = ug6.group_parent_id)
    LEFT JOIN gks_users_groups AS ug7 ON ug6.id_users_group = ug7.group_parent_id)
    LEFT JOIN gks_users_groups AS ug8 ON ug7.id_users_group = ug8.group_parent_id)
    LEFT JOIN gks_users_groups AS ug9 ON ug8.id_users_group = ug9.group_parent_id)
    LEFT JOIN gks_users_groups AS ug10 ON ug9.id_users_group = ug10.group_parent_id
    where ug1.id_users_group=".$value;
    
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
      $where1_calc= ' and (gks_users_groups_users.group_id in ('.implode(',',$group_ids).') or gks_users_groups_users.group_id is null) ';  
    } else {
      $where1_calc= ' and gks_users_groups_users.group_id in ('.implode(',',$group_ids).') ';  
    }
  } else {
    if ($fgroup_100) {
      $where1_calc=' and gks_users_groups_users.group_id is null ';
    }
  }

  
  //print '<pre>';
  //print_r($vals);
  //print_r($vals_array);
  //print_r($group_ids);
  //print '</pre>';  
  //print $where1_calc;
  //echo $where1;
  //echo '</pre>';
  //die();

} 

$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;

$sql_field_ajia='gks_orders.gks_price_net';
gks_plugins_functions_run('admin_users_field_user_tziros',array(
  'sql_field_ajia' => &$sql_field_ajia,
));


$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT  ".GKS_WP_TABLE_PREFIX."users.*, ".GKS_WP_TABLE_PREFIX."users.ID as id , 
gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_perioxi,gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id, gks_users.ma_nomos_id, gks_country.country_name, gks_nomoi.nomos_descr,
gks_users.ma_latitude,gks_users.ma_longitude, 
gks_users.profilepososto_user,gks_users.profilepososto_job,
gks_users.user_HumanInitial,
gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id,
gks_users.genisi_date,onoma_miteras,onoma_patera,ama_eam,amka, ethnikotita,arithmos_tautoitas,cv_spoydes,
myfirst_name,mylast_name,gks_users.phone_home,
tbl_tziros.user_tziros,
gks_lang.lang_name,

gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.arxi_ekdosis,gks_users.oikogeniaki_katastasti_paidia,
gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
table_description.short_cv,
gks_users.alli_apasxolisi,gks_users.cv_proipiresia,
gks_users.sistasi_from
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((((((((".GKS_WP_TABLE_PREFIX."users 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id)
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

LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS short_cv
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='description'))
)  AS table_description ON ".GKS_WP_TABLE_PREFIX."users.ID = table_description.user_id) 


LEFT JOIN gks_users_groups_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users_groups_users.user_id)
LEFT JOIN (
  select user_id, sum(".$sql_field_ajia.") as user_tziros
  from gks_orders
  group by user_id
) as tbl_tziros  ON ".GKS_WP_TABLE_PREFIX."users.ID = tbl_tziros.user_id

where 1=1 " .$where . $where1_calc . $search_where;


if (ur_ad() == false) {
  $sql.= " and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%administrator%')."'
           and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%adminmy%')."'";
}

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY ".GKS_WP_TABLE_PREFIX."users.id desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
//echo '<pre>'.$sql;die();
