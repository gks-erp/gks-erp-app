<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
if (isset($gks_from_pivot)==false) $gks_from_pivot=false;
if ($gks_from_pivot) {
  if (isset($_GET['fstatus'])==false) $_GET['fstatus']='-1';
  if (isset($_GET['flead_date'])==false) $_GET['flead_date']='18';
  
} else {
  //if (isset($_GET['fstatus'])==false) $_GET['fstatus']='1,20,50';
}

gks_get_leads_status($leads_status,$leads_status_styles);

//echo '<pre>';print_r($leads_status);echo $leads_status_styles;die();


  
$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_leads',['from'=>'list']);

$filters = array();

$filters[] = array(
  'name' => 'flead_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_crm_leads.lead_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_crm_leads.lead_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);



$vals=array();
foreach ($leads_status as $value) {
  if ($value['lead_status_disabled']==0) {
    $vals[]=array('value' => $value['id_crm_lead_status'], 'text' => $value['lead_status_descr'],'sql' => "gks_crm_leads.lead_status_id=".$value['id_crm_lead_status']);
  }
} 
$filters[] = array(
  'name' => 'fstatus',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_crm_leads.lead_status_id = %V%",
  'vals' => $vals,
);

$filters[] = array(
    'name' => 'fuser_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Επαφή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_leads.user_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_crm_leads LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

$filters[] = array(
    'name' => 'fcity',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_leads.poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT poli as descr, poli as id FROM gks_crm_leads WHERE poli<>'' GROUP BY poli ORDER BY poli",
);

//$filters[] = array(
//    'name' => 'fnomos',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Νομός'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_crm_leads.nomos_id = %V%",
//    'vals' => array(
//        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
//    ),
//    'sql' => "SELECT gks_crm_leads.nomos_id as id, gks_nomoi.nomos_descr as descr
//FROM gks_crm_leads LEFT JOIN gks_nomoi ON gks_crm_leads.nomos_id = gks_nomoi.id_nomos
//WHERE (((gks_nomoi.id_nomos) Is Not Null))
//GROUP BY gks_crm_leads.nomos_id, gks_nomoi.nomos_descr
//ORDER BY gks_nomoi.nomos_descr"
//);
//
//$filters[] = array(
//    'name' => 'fxora',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Χώρα'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_crm_leads.country_id = %V%",
//    'vals' => array(
//        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
//    ),
//    'sql' => "SELECT gks_crm_leads.country_id as id, gks_country.country_name as descr
//FROM gks_crm_leads LEFT JOIN gks_country ON gks_crm_leads.country_id = gks_country.id_country
//WHERE (((gks_country.id_country) Is Not Null))
//GROUP BY gks_crm_leads.country_id, gks_country.country_name
//ORDER BY gks_country.country_name"
//);


$filters[] = array(
    'name' => 'fpoint',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Στίγμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Έχει στίγμα'),     'sql' => "map_latitude <> 0 or map_longitude <>0"),
        array('value' => 2, 'text' => gks_lang('Δεν έχει στίγμα'), 'sql' => "map_latitude = 0 and map_longitude = 0"),
    ),
);
$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_leads.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_crm_leads LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
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
    'field'  => "gks_crm_leads.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_company as id, company_title as descr FROM gks_company order by company_sortorder,company_title",
);
$filters[] = array(
    'name' => 'fcompanysub',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Υποκατάστημα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_leads.company_sub_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_company_sub as id, company_sub_title as descr FROM gks_company_subs 
    order by gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title",
);


$filters[] = array(
    'name' => 'fchannel',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κανάλι Πωλήσεων'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_leads.crm_channel_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
    FROM gks_crm_leads LEFT JOIN gks_crm_channel_sale ON gks_crm_leads.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
    WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
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
    'field'  => "gks_crm_leads.crm_channel_contact_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_crm_leads LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
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
    'field'  => "gks_crm_leads.crm_channel_campain_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
    FROM gks_crm_leads 
    LEFT JOIN gks_ads_campain ON gks_crm_leads.crm_channel_campain_id = gks_ads_campain.id_ads_campain
    WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
    GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
    ORDER BY gks_ads_campain.ads_campain_name",
);


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);



$sortable = array(
	array('name' => 'soid', 'field' => 'gks_crm_leads.id_crm_lead'),
	array('name' => 'sodate', 'field' => 'gks_crm_leads.lead_date'),
	array('name' => 'sostatus', 'field' => 'gks_crm_leads_status.lead_status_sortorder'),
	array('name' => 'sosubject', 'field' => 'gks_crm_leads.subject'),
	array('name' => 'soesoda', 'field' => 'gks_crm_leads.esoda'),
	array('name' => 'soname', 'field' => 'gks_crm_leads.last_name,gks_crm_leads.first_name'),
	array('name' => 'somobile', 'field' => 'gks_crm_leads.mobile'),
	array('name' => 'sophone', 'field' => 'gks_crm_leads.phone'),
	array('name' => 'soemail', 'field' => 'gks_crm_leads.email'),
	array('name' => 'sopoli', 'field' => 'gks_crm_leads.poli'),
	array('name' => 'socompany', 'field' => 'gks_company.company_title'),
	array('name' => 'socompany_sub', 'field' => 'gks_company_subs.company_sub_title'),
	array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
	array('name' => 'sochannel', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
	array('name' => 'sochcontact', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
	array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
  array('name' => 'socrmcode', 'field' => 'gks_crm_leads.crm_channel_code'),
  						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_crm_leads_status.lead_status_descr',
'gks_company.company_title',
'gks_company_subs.company_sub_title',
'gks_crm_leads.form_name',
'gks_crm_leads.post_name',
'gks_crm_leads.first_name',
'gks_crm_leads.last_name',
'gks_crm_leads.email',
'gks_crm_leads.mobile',
'gks_crm_leads.phone',
'gks_crm_leads.web',
'gks_crm_leads.odos',
'gks_crm_leads.perioxi',
'gks_crm_leads.poli',
'gks_crm_leads.tk',
'gks_crm_leads.nomos',
'gks_nomoi.nomos_descr',
'gks_crm_leads.country',
'gks_country.country_name',
'gks_crm_leads.subject',
'gks_crm_leads.message',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',
'gks_crm_leads.crm_channel_code',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_crm_leads.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_crm_leads_status.lead_status_descr, gks_crm_leads_status.lead_status_color, gks_crm_leads_status.lead_status_sortorder,
gks_company.company_title, gks_company_subs.company_sub_title,
gks_country.country_name, gks_nomoi.nomos_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((((((((gks_crm_leads
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_leads.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_leads.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_crm_leads_status ON gks_crm_leads.lead_status_id = gks_crm_leads_status.id_crm_lead_status)
LEFT JOIN gks_company ON gks_crm_leads.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_crm_leads.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_country ON gks_crm_leads.country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_crm_leads.nomos_id = gks_nomoi.id_nomos)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_crm_leads.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_crm_leads.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_crm_leads.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_crm_leads.crm_channel_campain_id = gks_ads_campain.id_ads_campain

where 1=1 " .$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_crm_leads.lead_date desc, gks_crm_leads.id_crm_lead desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
