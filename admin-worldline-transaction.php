<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-worldline-transactions.php

$my_page_title=gks_lang('Συναλλαγές Worldline');
$nav_active_array=array('accounting','accounting_eftpos','accounting_eftpos_worldline');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_worldline_transaction','view',0);
$perm_ret_add=gks_permission_user_can_action($my_wp_user_id, 'gks_worldline_transaction','add',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$user_companys=gks_get_companys_list();
$only_id_company=array();
foreach ($user_companys as $value) {
  if ($value['id_company_sub']==0) $only_id_company[]=$value['id_company'];
} 
if (count($only_id_company)==0) $only_id_company[]=0;
//print '<pre>';print_r($only_id_company);print_r($user_companys);die();

$filters = array();


$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_worldline_transaction.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_worldline_transaction.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'finstalments',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Δόσεις'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.Instalments = %V%",
    'vals' => array(
        //array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_worldline_transaction.xeiristis_id=0 or gks_worldline_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT Instalments AS id, Instalments as descr
    FROM gks_worldline_transaction 
    GROUP BY Instalments
    order by Instalments",
);

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Status',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.myStatus = %V%",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_has_transaction_status_worldline(1),'sql' => "gks_worldline_transaction.myStatus=1"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_status_worldline(2),'sql' => "gks_worldline_transaction.myStatus=2"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_status_worldline(3),'sql' => "gks_worldline_transaction.myStatus=3"),
    ),
);
$filters[] = array(
    'name' => 'fresult',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Result',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.myResult = %V%",
    'vals' => array(
        //array('value' => 1, 'text' => gks_eftpos_has_transaction_result_worldline(1),'sql' => "gks_worldline_transaction.myResult=1"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_result_worldline(2),'sql' => "gks_worldline_transaction.myResult=2"),
        //array('value' => 3, 'text' => gks_eftpos_has_transaction_result_worldline(3),'sql' => "gks_worldline_transaction.myResult=3"),
        //array('value' => 4, 'text' => gks_eftpos_has_transaction_result_worldline(4),'sql' => "gks_worldline_transaction.myResult=4"),
        array('value' => 5, 'text' => gks_eftpos_has_transaction_result_worldline(5),'sql' => "gks_worldline_transaction.myResult=5"),
        array('value' => 6, 'text' => gks_eftpos_has_transaction_result_worldline(6),'sql' => "gks_worldline_transaction.myResult=6"),
        //array('value' => 7, 'text' => gks_eftpos_has_transaction_result_worldline(7),'sql' => "gks_worldline_transaction.myResult=7"),
        array('value' => 9, 'text' => gks_eftpos_has_transaction_result_worldline(9),'sql' => "gks_worldline_transaction.myResult=9"),
    ),
);


$filters[] = array(
    'name' => 'fapproved',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Approved',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.Approved = %V%",
    'vals' => array(
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "gks_worldline_transaction.Approved=1"),
        array('value' => 0, 'text' => gks_lang('Όχι'),          'sql' => "gks_worldline_transaction.Approved=0"),
    ),
);
$filters[] = array(
    'name' => 'fvoided',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Voided',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.Voided = %V%",
    'vals' => array(
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "gks_worldline_transaction.Voided=1"),
        array('value' => 0, 'text' => gks_lang('Όχι'),          'sql' => "gks_worldline_transaction.Voided=0"),
    ),
);

//$filters[] = array(
//    'name' => 'fStatusId',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => 'StatusId',
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_worldline_transaction.StatusId = '%V%'",
//    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Κενό StatusId'),          'sql' => "(gks_worldline_transaction.StatusId='' or gks_worldline_transaction.StatusId is null)"),
//    ),
//    'sql' => "SELECT StatusId as id,StatusId as descr
//    FROM gks_worldline_transaction 
//    WHERE StatusId<>''
//    GROUP BY StatusId
//    order by StatusId",
//);


$filters[] = array(
    'name' => 'fistra',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.trn_type = '%V%'",
    'vals' => array(
        array('value' => 0, 'text' => gks_eftpos_has_transaction_type_worldline('100000001'), 'sql' => "gks_worldline_transaction.trn_type='100000001'"),
        array('value' => 1, 'text' => gks_eftpos_has_transaction_type_worldline('100000002'), 'sql' => "gks_worldline_transaction.trn_type='100000002'"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_type_worldline('200000001'), 'sql' => "gks_worldline_transaction.trn_type='200000001'"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_type_worldline('200000003'), 'sql' => "gks_worldline_transaction.trn_type='200000003'"),
        array('value' => 4, 'text' => gks_eftpos_has_transaction_type_worldline('200000004'), 'sql' => "gks_worldline_transaction.trn_type='200000004'"),
        array('value' => 5, 'text' => gks_eftpos_has_transaction_type_worldline('200000005'), 'sql' => "gks_worldline_transaction.trn_type='200000005'"),
        array('value' => 6, 'text' => gks_eftpos_has_transaction_type_worldline('300000004'), 'sql' => "gks_worldline_transaction.trn_type='300000004'"),
        array('value' => 7, 'text' => gks_eftpos_has_transaction_type_worldline('300000005'), 'sql' => "gks_worldline_transaction.trn_type='300000005'"),
        array('value' => 8, 'text' => gks_eftpos_has_transaction_type_worldline('300000007'), 'sql' => "gks_worldline_transaction.trn_type='300000007'"),
        array('value' => 9, 'text' => gks_eftpos_has_transaction_type_worldline('300000008'), 'sql' => "gks_worldline_transaction.trn_type='300000008'"),
        array('value' => 10, 'text' => gks_eftpos_has_transaction_type_worldline('300000009'), 'sql' => "gks_worldline_transaction.trn_type='300000009'"),
    
    ),
);


$filters[] = array(
    'name' => 'fterminal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χειριστής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.xeiristis_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_worldline_transaction.xeiristis_id=0 or gks_worldline_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT gks_worldline_transaction.xeiristis_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_worldline_transaction LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_worldline_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_worldline_transaction.xeiristis_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

if (count($user_companys)>1) {
  $filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_worldline_transaction.merchant_id='%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_worldline_transaction.StatusId='' or gks_worldline_transaction.StatusId is null)"),
    ),
    'sql' => "
    SELECT gks_worldline_transaction.merchant_id as id, gks_company.company_title as descr
    FROM gks_worldline_transaction 
    LEFT JOIN gks_company ON gks_worldline_transaction.merchant_id = gks_company.worldline_mid
    WHERE gks_worldline_transaction.merchant_id<>''
    and (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
    GROUP BY gks_worldline_transaction.merchant_id, gks_company.id_company, gks_company.company_title
    order by company_sortorder"
    
  );  
}

//$filters[] = array(
//    'name' => 'fCardTypeName',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Τύπος Κάρτας'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_worldline_transaction.CardTypeName = '%V%'",
//    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Χωρίς Τύπο κάρτας'),          'sql' => "(gks_worldline_transaction.CardTypeName='' or gks_worldline_transaction.CardTypeName is null)"),
//    ),
//    'sql' => "SELECT CardTypeName AS id, CardTypeName as descr
//    FROM gks_worldline_transaction 
//    WHERE CardTypeName Is Not Null
//    GROUP BY CardTypeName
//    order by CardTypeName",
//);

//$filters[] = array(
//    'name' => 'fCardIssuingBank',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => 'Acquirer',
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_worldline_transaction.Acquirer = '%V%'",
//    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Χωρίς Acquirer'),          'sql' => "(gks_worldline_transaction.Acquirer='' or gks_worldline_transaction.Acquirer is null)"),
//    ),
//    'sql' => "SELECT Acquirer AS id, Acquirer as descr
//    FROM gks_worldline_transaction 
//    WHERE Acquirer Is Not Null
//    GROUP BY Acquirer
//    order by Acquirer",
//);



	

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_worldline_transaction.id_worldline_transaction'),
	array('name' => 'somydate', 'field' => 'gks_worldline_transaction.mydate_add'),
	array('name' => 'soammount', 'field' => 'gks_worldline_transaction.trn_amount'),
	array('name' => 'socurr', 'field' => 'gks_worldline_transaction.trn_currency'),
	array('name' => 'soinstalments', 'field' => 'gks_worldline_transaction.Instalments'),
	array('name' => 'sotip', 'field' => 'gks_worldline_transaction.TipAmount'),
	array('name' => 'sostatus', 'field' => 'gks_worldline_transaction.myStatus'),
	array('name' => 'soresult', 'field' => 'gks_worldline_transaction.myResult'),
	array('name' => 'soapproved', 'field' => 'gks_worldline_transaction.Approved'),
	array('name' => 'sovoided', 'field' => 'gks_worldline_transaction.Voided'),
	array('name' => 'somyerror', 'field' => 'gks_worldline_transaction.myerror'),
	array('name' => 'sotxntype', 'field' => 'gks_worldline_transaction.trn_type'),
	array('name' => 'sotrn_type_name', 'field' => 'gks_worldline_transaction.trn_type_name'),
	array('name' => 'soterminal', 'field' => 'gks_worldline_transaction.merchant_terminal_id'),
	array('name' => 'sopos_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	array('name' => 'soref', 'field' => 'gks_worldline_transaction.trn_reference_label'),
	array('name' => 'socompany', 'field' => 'gks_company.company_title'),
	array('name' => 'somerchant_id', 'field' => 'gks_worldline_transaction.merchant_id'),
	array('name' => 'somerchant_name', 'field' => 'gks_worldline_transaction.merchant_name'),
	array('name' => 'somerchant_taxpayer_id', 'field' => 'gks_worldline_transaction.merchant_taxpayer_id'),
	array('name' => 'somerchant_bank_id', 'field' => 'gks_worldline_transaction.merchant_bank_id'),
	array('name' => 'somessage_type', 'field' => 'gks_worldline_transaction.message_type'),
	array('name' => 'soemail', 'field' => 'gks_worldline_transaction.CustomerEmail'),
	array('name' => 'sophone', 'field' => 'gks_worldline_transaction.CustomerPhone'),
	array('name' => 'sorrn', 'field' => 'gks_worldline_transaction.trn_host_rrn'),
	array('name' => 'soauthcode', 'field' => 'gks_worldline_transaction.trn_host_auth_code'),
	array('name' => 'sottid', 'field' => 'gks_worldline_transaction.TransactionId_org'),
	array('name' => 'sotime', 'field' => 'gks_worldline_transaction.trn_date_time'),
	array('name' => 'sostan', 'field' => 'gks_worldline_transaction.trn_stan'),
	array('name' => 'sobatch', 'field' => 'gks_worldline_transaction.trn_batch'),

	
	


	


	array('name' => 'sotrn_token', 'field' => 'gks_worldline_transaction.trn_token'),
	array('name' => 'sotrn_status_code', 'field' => 'gks_worldline_transaction.trn_status_code'),
	array('name' => 'sotrn_host_resp_code', 'field' => 'gks_worldline_transaction.trn_host_resp_code'),
	array('name' => 'sotrn_host_error_desc', 'field' => 'gks_worldline_transaction.trn_host_error_desc'),
	array('name' => 'sotrn_info_data', 'field' => 'gks_worldline_transaction.trn_info_data'),
	array('name' => 'somerchant_terminal_description', 'field' => 'gks_worldline_transaction.merchant_terminal_description'),
	array('name' => 'somerchant_terminal_location', 'field' => 'gks_worldline_transaction.merchant_terminal_location'),
	array('name' => 'somerchant_terminal_city', 'field' => 'gks_worldline_transaction.merchant_terminal_city'),
	array('name' => 'somerchant_terminal_state', 'field' => 'gks_worldline_transaction.merchant_terminal_state'),
	array('name' => 'somerchant_terminal_country', 'field' => 'gks_worldline_transaction.merchant_terminal_country'),
	array('name' => 'somerchant_device_id', 'field' => 'gks_worldline_transaction.merchant_device_id'),
	array('name' => 'sotrn_id', 'field' => 'gks_worldline_transaction.trn_id'),
	array('name' => 'sotrn_debit_id', 'field' => 'gks_worldline_transaction.trn_debit_id'),
	array('name' => 'sotrn_id_gr_aade', 'field' => 'gks_worldline_transaction.trn_id_gr_aade'),
	array('name' => 'socard_pan', 'field' => 'gks_worldline_transaction.card_pan'),
	array('name' => 'socard_aid', 'field' => 'gks_worldline_transaction.card_aid'),
	array('name' => 'socard_label', 'field' => 'gks_worldline_transaction.card_label'),
	array('name' => 'socard_cvm_pin', 'field' => 'gks_worldline_transaction.card_cvm_pin'),
	array('name' => 'socard_cvm_cdcvm', 'field' => 'gks_worldline_transaction.card_cvm_cdcvm'),
	array('name' => 'socard_tvr', 'field' => 'gks_worldline_transaction.card_tvr'),
	array('name' => 'socard_kvr', 'field' => 'gks_worldline_transaction.card_kvr'),
	array('name' => 'socard_cda_res', 'field' => 'gks_worldline_transaction.card_cda_res'),



);
     
   


$search_fields = array(
  'gks_worldline_transaction.intentId',
  'gks_worldline_transaction.message_type',
  'gks_worldline_transaction.TransactionId_org',
  'gks_worldline_transaction.card_pan',
  'gks_worldline_transaction.card_label',
  'gks_worldline_transaction.trn_id',
  'gks_worldline_transaction.trn_token',
  'gks_worldline_transaction.trn_type',
  'gks_worldline_transaction.trn_type_name',
  'gks_worldline_transaction.trn_reference_label',
  'gks_worldline_transaction.merchant_terminal_id',
  'gks_worldline_transaction.merchant_terminal_description',
  'gks_worldline_transaction.merchant_terminal_location',
  'gks_worldline_transaction.merchant_terminal_city',
  'gks_worldline_transaction.merchant_terminal_state',
  'gks_worldline_transaction.merchant_id',
  'gks_worldline_transaction.merchant_name',
  'gks_worldline_transaction.merchant_taxpayer_id',
  'gks_worldline_transaction.merchant_device_id',
  'gks_worldline_transaction.trn_id_gr_aade',
  'gks_worldline_transaction.trn_host_rrn',
  'gks_worldline_transaction.trn_host_auth_code',
  'gks_worldline_transaction.merchant_bank_id',
  'gks_worldline_transaction.myerror',
  'gks_worldline_transaction.CustomerEmail',
  'gks_worldline_transaction.CustomerPhone',
);






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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_worldline_transaction.*, 
".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
gks_company.company_title
FROM (gks_worldline_transaction
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_worldline_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_company ON gks_worldline_transaction.merchant_id = gks_company.worldline_mid
where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_worldline_transaction DESC";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;




//echo $query;
//die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
$res_numrows = $db_link->query($sql_numrows);
$row_numrows = $res_numrows->fetch_assoc();
$total_records = $row_numrows['found_rows'];

$pages = ceil($total_records / $rows_per_page) - 1;

$paging = array('records' => '', 'total' => '', 'pages' => '');
$url = $_SERVER['SCRIPT_NAME'].'?';
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);




pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?';
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}

//print '<pre>';print_r($paging);print '</pre>';

//print '<pre>';
//print_r($sortable);
//echo '<br>';
//echo $sortable_url;
//die();

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>
<style>
.gks_td08 {
  min-width:150px;  
}  
.mydivexpand {    
  max-height: 19px;
}
.mydivexpand_on {
    max-height: unset;
}  
.worldline_reload {
  cursor: pointer;  
}
.worldline_reload:before {
  background-color: #32bea6;
  color: white;
  padding: 6px;
  border-radius: 50%;
}
.gks_marker_gps {
  color:#ae0008;
  font-size: 16px;
}

.div_xeiristis {
  background-image:unset;
  cursor:pointer;
  padding-right: 20px !important;
}   
.div_xeiristis:hover {
  background-image:url('/my/img/pencil-16.png');
  background-repeat: no-repeat;
  background-position: right;
  font-weight1: bold;
}

.worldline_status_1 {border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.worldline_status_2 {border-width: 0px;background-color: #978626;color: white;font-weight: bold;}
.worldline_status_3 {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.worldline_status_101 {border-width: 0px;background-color: #660000;color: white;font-weight: bold;}

.worldline_result_2 {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.worldline_result_5 {border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.worldline_result_6 {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.worldline_result_9 {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.worldline_result_101 {border-width: 0px;background-color: #660000;color: white;font-weight: bold;}
.worldline_result_103 {border-width: 0px;background-color: red;color: white;font-weight: bold;}

.worldline_approved_1 {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.worldline_voided_1 {border-width: 0px;background-color: green;color: white;font-weight: bold;}
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>
<?php if ($perm_ret_add['success']) {?>
<div class="container-fluid">
  <div style="text-align: center"><a href="admin-worldline-refresh.php?today=1" target="_blank" style="
     background-color: #2cadbf;
      color: white;
      padding: 20px;
      margin-bottom: 10px;
      border-radius: 10px;
      font-size: 150%;
      border: 1px solid #165861;
      display: inline-block;
  "><?php echo gks_lang('Λήψη ξανά των συναλλαγών από worldline για σήμερα');?></a></div>
</div>
<?php } ?>

<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>


<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>	

    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap"><span title="<?php echo gks_lang('Αποδεικτικό');?>">Α</span></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">RAW</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">Re</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soammount', gks_lang('Ποσό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socurr', '<span class="tooltipster" title="CurrencyCode">Curr</span>'); ?></th>    

    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstalments', gks_lang('Δόσεις')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotip', '<span title="'.gks_lang('Φιλοδώρημα').'" class="tooltipster" >'.gks_lang('Φιλο').'</span>'); ?></th>        
       
    
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', 'Status'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult', 'Result'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soapproved', 'Approved'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovoided', 'Voided'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyerror', gks_lang('Μήνυμα')); ?></th>        
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotxntype', '<span title="TxnType" class="tooltipster" >'.gks_lang('Τύπος').'</span>'); ?></th>        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_type_name', '<span title="trn_type_name" class="tooltipster" >trn_type_name</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_label', 'Card Label'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos_user', gks_lang('Χειριστής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soref', '<span class="tooltipster" title="'.gks_lang('Αναφορά Πληρωμής').'">'.gks_lang('Α.Πλ.').'</span> '); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_id', 'merchant_id'); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_name', 'merchant_name'); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_taxpayer_id', 'merchant_taxpayer_id'); ?></th>  


    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_bank_id', 'merchant_bank_id'); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somessage_type', 'message_type'); ?></th>  
          
        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', 'email'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorrn', '<span class="tooltipster" title="'.gks_lang('RRN Κωδικός Πληρωμής').'">RRN</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soauthcode', '<span class="tooltipster" title="AuthCode">AuthCode</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sottid', '<span class="tooltipster" title="Start TransactionId">STID</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotime', '<span class="tooltipster" title="Timestamp">Time</span>'); ?></th>        

    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostan', '<span class="tooltipster" title="STAN">STAN</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobatch', '<span class="tooltipster" title="Batch">Batch</span>'); ?></th>        


    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_token', 'trn_token'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_status_code', 'trn_status_code'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_host_resp_code', 'trn_host_resp_code'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_host_error_desc', 'trn_host_error_desc'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_info_data', 'trn_info_data'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_terminal_description', 'merchant_terminal_description'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_terminal_location', 'merchant_terminal_location'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_terminal_city', 'merchant_terminal_city'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_terminal_state', 'merchant_terminal_state'); ?></th>

    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_terminal_country', 'merchant_terminal_country'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchant_device_id', 'merchant_device_id'); ?></th>
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_id', 'trn_id'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_debit_id', 'trn_debit_id'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotrn_id_gr_aade', 'trn_id_gr_aade'); ?></th>
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_pan', gks_lang('Κάρτα')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_aid', 'card_aid'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_cvm_pin', 'card_cvm_pin'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_cvm_cdcvm', 'card_cvm_cdcvm'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_tvr', 'card_tvr'); ?></th>
        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_kvr', '<span class="tooltipster" title="card_kvr">card_kvr</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard_cda_res', '<span class="tooltipster" title="card_cda_res">card_cda_res</span>'); ?></th>        
    

       
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">IP</th>


    
  </tr>
</thead>
<tbody>

    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcml"><?php echo $row['id_worldline_transaction'];?></td>   
<!--
https://cabinet3-test.tapxphone.com/index.php?r=mpos-transactions%2Freceipt-view&id=34107057
https://cabinet3-test.tapxphone.com/index.php?r=mpos-transactions%2Freversal-receipt-view&id=34107057
https://cabinet3-test.tapxphone.com/index.php?r=guest%2Freceipt-view&token=Yjg0OGJlODAyZjBiNDVjNWI1NmQ2OWJlODdlYTZhNDc.xEVUKZBN_vqzQSuhUA8uaGfslN_aB-A-Bu-4QPVTHNc
https://cabinet3-test.tapxphone.com/index.php?r=guest%2Freversal-receipt-view&id=34107057
https://cabinet3-test.tapxphone.com/index.php?r=guest%2Freceipt-view&id=34107057

-->
    <td class="mytdcm"><?php
      if (trim_gks($row['trn_host_rrn'])!='') { ?>
      <a href="https://cabinet3-test.tapxphone.com/index.php?r=mpos-transactions%2Freceipt-view&id=<?php echo $row['trn_host_rrn'];?>" target="_blank">A</a>
      <?php } ?></td>
    <td class="mytdcm"><a href="admin-worldline-transaction-raw.php?mtid=<?php echo $row['id_worldline_transaction'];?>" target="_blank">R</a></td>
    <td class="mytdcm">
        <i class="fas fa-sync worldline_reload" data-id="<?php echo $row['id_worldline_transaction'];?>"></i>
    </td>  
      
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>  
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['trn_amount']) and $row['trn_amount']!=0) echo number_format($row['trn_amount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_currency'];?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['Instalments']) and $row['Instalments']!=0) echo $row['Instalments'];?></td>   
    
    
    <td nowrap class="mytdcm"><?php if (isset($row['TipAmount']) and $row['TipAmount']!=0) echo number_format($row['TipAmount'], 2, ',', '.');?></td>   



    <td nowrap class="mytdcm worldline_status_<?php echo $row['myStatus'];?>"><?php echo gks_eftpos_has_transaction_status_worldline($row['myStatus']);?></td>
    <td nowrap class="mytdcm worldline_result_<?php echo $row['myResult'];?>"><?php echo gks_eftpos_has_transaction_result_worldline($row['myResult']);?></td>
    <td nowrap class="mytdcm worldline_approved_<?php echo $row['Approved'];?>"><?php echo myimg010($row['Approved']);?></td>
    <td nowrap class="mytdcm worldline_voided_<?php echo $row['Voided'];?>"><?php echo myimg01($row['Voided']);?></td>
     
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['myerror']);
    ?></div></div></td>
    <td nowrap class="mytdcm"><?php echo gks_eftpos_has_transaction_type_worldline($row['trn_type']);?></td>
    <td nowrap class="mytdcm"><?php echo $row['trn_type'].' '.$row['trn_type_name'];?></td>
  
    <td nowrap class="mytdcm"><?php echo $row['card_label'];?></td>
 
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_id'];?></td>   
    <td nowrap class="mytdcml div_xeiristis" data-id="<?php echo $row['id_worldline_transaction'];?>">
      <a href="admin-users-item.php?id=<?php echo $row['pos_user_id'];?>"><?php echo $row['pos_gks_nickname'];?></a>
    </td>
    <td nowrap class="mytdcml"><?php echo $row['trn_reference_label'];?></td>   
    <td nowrap class="mytdcm"><?php 
      if (!empty($row['company_title'])) {
        echo $row['company_title'];
      }
    ?></td> 
    <td nowrap class="mytdcm"><?php echo $row['merchant_id'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['merchant_name'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['merchant_taxpayer_id'];?></td>
    
    
    <td nowrap class="mytdcm"><?php echo $row['merchant_bank_id'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['message_type'];?></td>   
   

    
    
    
  
    <td nowrap class="mytdcm"><?php
      //echo $row['CustomerEmail'];
      ?></td>   
    <td nowrap class="mytdcm"><?php 
      //echo $row['CustomerPhone'];
      ?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_host_rrn'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_host_auth_code'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['TransactionId_org'];?></td>   
    <td nowrap class="mytdcm"><?php if (!empty($row['trn_date_time'])) echo showDate(strtotime($row['trn_date_time']),'d/m/Y H:i:s',1);?></td>   

    <td nowrap class="mytdcm"><?php echo $row['trn_stan'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_batch'];?></td>   



    
    
    
    
    
    <td nowrap class="mytdcm"><?php echo $row['trn_token'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_status_code'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_host_resp_code'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_host_error_desc'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_info_data'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_description'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_location'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_city'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_state'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_terminal_country'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['merchant_device_id'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_id'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['trn_debit_id'];?></td>   
    
    <td nowrap class="mytdcm"><?php echo $row['trn_id_gr_aade'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['card_pan'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['card_aid'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['card_cvm_pin'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['card_cvm_cdcvm'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['card_tvr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['card_kvr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['card_cda_res'];?></td>
    
       
    <td class="mytdcm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php if (!empty($row['myip'])) echo 'V';?></a></td>
    
  </tr>
<?php    
    }
?>
</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>
    

<style>



</style>

<?php if (ur_lo() or ur_ad()) {?>
<hr>
<h2 align="center"><?php echo gks_lang('Σύνολα σύμφωνα με τα παραπάνω φίλτρα');?></h2>
<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><?php echo gks_lang('A/A');?></th>
    <th class="table-dark" scope="col" style="text-align: left !important;" width="55%"  nowrap="nowrap"><?php echo gks_lang('Εταιρεία');?></th> 
    <th class="table-dark" scope="col" style="text-align: right !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Πλήθος');?></th>   
    <th class="table-dark" scope="col" style="text-align: right !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Ποσό');?></th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Φιλοδώρημα');?></th>        
  </tr>


</thead>
<tbody>
      
<?php
$sql = "select merchant_id, company_title,company_sortorder,
count(*) as countid, sum(trn_amount) as sum_Amount, sum(TipAmount) as sum_TipAmount
from (
  SELECT gks_worldline_transaction.*, 
  ".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
  gks_company.company_title,
  gks_company.company_sortorder
  FROM (gks_worldline_transaction 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_worldline_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_company ON gks_worldline_transaction.merchant_id = gks_company.worldline_mid
  where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
  ".$where . $search_where;


$sql.="
) AS mydata
group by merchant_id, company_title, company_sortorder
order by company_sortorder
";
 


$result_rep = $db_link->query($sql);        
if (!$result_rep) debug_mail(false,'error sql',$sql);
if (!$result_rep) die('sql error');


$sum_countid=0;
$sum_sum_Amount=0;
$sum_sum_TipAmount=0;

$i = 0;
while ($row_rep = $result_rep->fetch_assoc()) {
  $i++;
  $sum_countid+=$row_rep['countid'];
  $sum_sum_Amount+=$row_rep['sum_Amount'];
  $sum_sum_TipAmount+=$row_rep['sum_TipAmount'];
  
  
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td class="mytdcml"><?php if (!empty($row_rep['company_title'])) {
        echo $row_rep['company_title'];
      } else {
        echo '<span title="'.$row_rep['merchant_id'].'">'.$row_rep['merchant_id'].'</span>';
      }
      ?></td> 

 
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['countid']) and $row_rep['countid']!=0) echo $row_rep['countid'];?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_Amount']) and $row_rep['sum_Amount']!=0) echo number_format($row_rep['sum_Amount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_TipAmount']) and $row_rep['sum_TipAmount']!=0) echo number_format($row_rep['sum_TipAmount'],2,',','.');?></td>  
  </tr>
<?php

} 
?>
</tbody>
<tfoot>

  <tr class="table-warning">
    <td class="bottomsums" colspan="2" nowrap><?php echo gks_lang('Σύνολα');?></td>
    <td class="bottomsums" nowrap align="right"><?php echo $sum_countid;?></td>  
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_Amount,2,',','.');?></td>  
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_TipAmount,2,',','.');?></td>  
  </tr>
</tfoot>
</table>

<?php } ?>


<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;margin-top:40px;border-collapse: collapse;">
<tbody>
  <tr>
    <th class="table-dark" scope="col" width="20%" style="text-align:center;">Status</th>
    <th class="table-dark" scope="col" width="80%">Description</th>
  </tr>
  <tr>
    <td class="mytdcm">PENDING</td>
    <td class="mytdcml">Intent has been registered to the backend and is pending to be sent to the device</td>
  </tr>
  <tr>
    <td class="mytdcm">SENT</td>
    <td class="mytdcml">Intent has been sent to the device</td>
  </tr>
  <tr>
    <td class="mytdcm">COMPLETED</td>
    <td class="mytdcml">Intent has been successfully completed by the device and has registered the results</td>
  </tr>

</tbody>
</table>


<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;margin-top:40px;border-collapse: collapse;">
<tbody>
  <tr>
    <th class="table-dark" scope="col" width="20%" style="text-align:center;">Result</th>
    <th class="table-dark" scope="col" width="80%">Description</th>
  </tr>
  <tr>
    <td class="mytdcm">APPROVED</td>
    <td class="mytdcml">Transaction was successfully completed by acquirer</td>
  </tr>
  <tr>
    <td class="mytdcm">REJECTED</td>
    <td class="mytdcml">Transaction was declined (rejected) by acquirer</td>
  </tr>
  <tr>
    <td class="mytdcm">ERROR</td>
    <td class="mytdcml">Transaction processing error</td>
  </tr>


</tbody>
</table>

<div id="dialog_select_xeiristis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div style="text-align:center;font-weight:bold;font-size:120%;"><?php echo gks_lang('Επιλογή χειριστή');?></div>
  <div id="dialog_select_xeiristis_html">
    
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  }); 


  $('.worldline_reload').click(function() {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    //console.log(myid);
    
    $('body').addClass("myloading");
    datasend='myid=' + myid
    $.ajax({
			url: '/my/admin-worldline-transaction-reload.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		}); 
		    
  });  

  dialog_select_xeiristis = $( "#dialog_select_xeiristis" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_select_xeiristis_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('OK'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          if ($('input[name=selraf]:checked').length<=0) {
            myalert('error:'+gks_lang('Κάντε μια επιλογή'));
            return;  
          }
          sel_id=parseInt($('input[name=selraf]:checked').val());
          if (isNaN(sel_id)) sel_id=-1;
          if (sel_id<=-1) return;
          //console.log(sel_id);
          
          $('body').addClass("myloading");
          datasend='myid=' + dialog_select_xeiristis.myid + '&fid=' + sel_id;
          $.ajax({
      			url: '/my/admin-worldline-transaction-select-xeiristis-exec.php',
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			error : function(jqXHR ,textStatus,  errorThrown) {
      			  $("body").removeClass("myloading");
      				myalert('error:' + jqXHR.responseText);
      			},				
      			success: function(data) {
      				$("body").removeClass("myloading");
      				if (!data) {
      					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
      					if (data.success == true) {
        					window.location.reload();
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		});			
		    }	
      },
      {
        id: "dialog_select_xeiristis_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }			
      },      
    ]
        

  });
  $('.div_xeiristis > a').click(function(event) {
    event.stopPropagation();
  });

  $('.div_xeiristis').click(function(event) {
    event.stopPropagation();
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    //console.log(myid);
    dialog_select_xeiristis.myid=myid;
    
    $("#dialog_select_xeiristis_html").html('');
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 400) dwidth=400;
	  if (dheight> 800) dheight=800;
	  dialog_select_xeiristis.dialog('option', 'width', dwidth);
	  dialog_select_xeiristis.dialog('option', 'height', dheight)      
    dialog_select_xeiristis.dialog('open');
    
    
    $('body').addClass("myloading");
    datasend='myid=' + myid
    $.ajax({
			url: '/my/admin-worldline-transaction-select-xeiristis.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
				dialog_select_xeiristis.dialog('close');
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					dialog_select_xeiristis.dialog('close');
				} else {
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					$("#dialog_select_xeiristis_html").html($.base64.decode(data.message));
  					if ($('input[name=selraf]').length>0) $('input[name=selraf]')[0].focus();
  					if ($('input[name=selraf]:checked').length>0)  {
  					  $('input[name=selraf]:checked')[0].focus(); //.scrollIntoView(); 
  					  
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						dialog_select_xeiristis.dialog('close');
					}
				}
			}
			
		}); 
		  
  });
  
});


</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


