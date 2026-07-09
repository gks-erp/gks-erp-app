<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-viva-transactions.php

$my_page_title=gks_lang('Ψηφιακές υπογραφές από πάροχο');
$nav_active_array=array('accounting','accounting_eftpos','accounting_paroxos_signature');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_paroxos_signature','view',0);
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
	'field' => 'gks_paroxos_signature.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_paroxos_signature.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_paroxos_signature.signature_status = '%V%'",
    'vals' => array(
        array('value' => 1, 'text' => gks_paroxos_signature_status_descr('draft'),    'sql' => "gks_paroxos_signature.signature_status='draft'"),
        array('value' => 2, 'text' => gks_paroxos_signature_status_descr('assign'),   'sql' => "gks_paroxos_signature.signature_status='assign'"),
        array('value' => 3, 'text' => gks_paroxos_signature_status_descr('canreuse'), 'sql' => "gks_paroxos_signature.signature_status='canreuse'"),
        array('value' => 4, 'text' => gks_paroxos_signature_status_descr('used'),     'sql' => "gks_paroxos_signature.signature_status='used'"),
        array('value' => 5, 'text' => gks_paroxos_signature_status_descr('send'),     'sql' => "gks_paroxos_signature.signature_status='send'"),
        array('value' => 6, 'text' => gks_paroxos_signature_status_descr('canceled'), 'sql' => "gks_paroxos_signature.signature_status='canceled'"),
    ),
);


$filters[] = array(
    'name' => 'fps',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πάροχος Υπογραφών'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_paroxos_signature.aade_paroxos_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Πάροχο Υπογραφών'),          'sql' => "(gks_paroxos_signature.aade_paroxos_id=0 or gks_paroxos_signature.aade_paroxos_id is null)"),
    ),
    'sql' => "SELECT gks_aade_paroxos.id_aade_paroxos as id, gks_aade_paroxos.paroxos_name as descr
    FROM gks_paroxos_signature 
    LEFT JOIN gks_aade_paroxos ON gks_paroxos_signature.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
    WHERE (((gks_aade_paroxos.id_aade_paroxos) Is Not Null))
    GROUP BY gks_aade_paroxos.id_aade_paroxos, gks_aade_paroxos.paroxos_name, gks_aade_paroxos.paroxos_sortorder
    ORDER BY gks_aade_paroxos.paroxos_sortorder;",
);

$filters[] = array(
    'name' => 'fpp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πάροχος Πληρωμών'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_paroxos_signature.payment_acquirer_with_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Πάροχο Πληρωμών'),          'sql' => "(gks_paroxos_signature.payment_acquirer_with_id=0 or gks_paroxos_signature.payment_acquirer_with_id is null)"),
    ),
    'sql' => "SELECT gks_payment_acquirer_with.id_payment_acquirer_with as id, gks_payment_acquirer_with.payment_paroxos_name as descr
    FROM gks_paroxos_signature 
    LEFT JOIN gks_payment_acquirer_with ON gks_paroxos_signature.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with
    WHERE (((gks_payment_acquirer_with.id_payment_acquirer_with) Is Not Null))
    GROUP BY gks_payment_acquirer_with.id_payment_acquirer_with, gks_payment_acquirer_with.payment_paroxos_name, gks_payment_acquirer_with.payment_paroxos_sortorder
    ORDER BY gks_payment_acquirer_with.payment_paroxos_sortorder;",
);

$filters[] = array(
    'name' => 'fpaid',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τρόπος Πληρωμής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_paroxos_signature.payment_acquirer_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τρόπο πληρωμής'),          'sql' => "(gks_paroxos_signature.payment_acquirer_id=0 or gks_paroxos_signature.payment_acquirer_id is null)"),
    ),
    'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name as descr
    FROM gks_paroxos_signature 
    LEFT JOIN gks_payment_acquirers ON gks_paroxos_signature.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer
    WHERE (((gks_payment_acquirers.id_payment_acquirer) Is Not Null))
    GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
    ORDER BY gks_payment_acquirers.mysortorder;",
);



$filters[] = array(
  'name' => 'fasset',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πάγιο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.asset_id=%V%",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.signature_status='')"),
  ),
  'sql' => "SELECT gks_assets.id_asset as id, gks_assets.asset_title as descr
  FROM gks_paroxos_signature LEFT JOIN gks_assets ON gks_paroxos_signature.asset_id = gks_assets.id_asset
  WHERE (((gks_assets.id_asset) Is Not Null))
  GROUP BY gks_assets.id_asset, gks_assets.asset_title
  ORDER BY gks_assets.asset_title;"
); 


$filters[] = array(
  'name' => 'fterminal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τερματικό'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_terminalId='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_terminalId as id, r_terminalId as descr
  FROM gks_paroxos_signature
  WHERE r_terminalId<>''
  GROUP BY r_terminalId
  order by r_terminalId"
);


$filters[] = array(
  'name' => 'fprotocol',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πρωτόκολλο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_nspProtocol='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_nspProtocol as id, r_nspProtocol as descr
  FROM gks_paroxos_signature
  WHERE r_nspProtocol<>''
  GROUP BY r_nspProtocol
  order by r_nspProtocol"
);
  

$filters[] = array(
  'name' => 'fuser',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Χειριστής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.user_id_add=%V%",
  'vals' => array(
      array('value' => -10, 'text' => gks_lang('Χωρίς χειριστή'),          'sql' => "gks_paroxos_signature.user_id_add=0"),
      array('value' => -11, 'text' => gks_lang('Με χειριστή'),          'sql' => "gks_paroxos_signature.user_id_add>0"),
  ),
  'sql' => "SELECT gks_paroxos_signature.user_id_add AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
  FROM gks_paroxos_signature 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_paroxos_signature.user_id_add = ".GKS_WP_TABLE_PREFIX."users.ID
  GROUP BY gks_paroxos_signature.user_id_add
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;"
);


$filters[] = array(
  'name' => 'fsellervat',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Εταιρεία'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_sellerVat='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_sellerVat as id, r_sellerVat as descr
  FROM gks_paroxos_signature
  WHERE r_sellerVat<>''
  GROUP BY r_sellerVat
  order by r_sellerVat"
);

$filters[] = array(
  'name' => 'fsellerbranch',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Αριθμός Εγκατάστασης'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_sellerBranch='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_sellerBranch as id, r_sellerBranch as descr
  FROM gks_paroxos_signature
  GROUP BY r_sellerBranch
  order by r_sellerBranch"
);

$filters[] = array(
  'name' => 'finvoicetypecode',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τύπος Παραστατικού'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_invoiceTypeCode='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_invoiceTypeCode as id, r_invoiceTypeCode as descr
  FROM gks_paroxos_signature
  GROUP BY r_invoiceTypeCode
  order by r_invoiceTypeCode"
);


$filters[] = array(
  'name' => 'fseries',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Σειρά'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_paroxos_signature.r_series='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_paroxos_signature.transaction_status='')"),
  ),
  'sql' => "SELECT r_series as id, r_series as descr
  FROM gks_paroxos_signature
  GROUP BY r_series
  order by r_series"
);









	

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_paroxos_signature.id_paroxos_signature'),
  						array('name' => 'somydate', 'field' => 'gks_paroxos_signature.mydate_add'),
  						array('name' => 'sostatus', 'field' => 'gks_paroxos_signature.signature_status'),
  						array('name' => 'socstp', 'field' => 'gks_paroxos_signature.count_send_to_pos'),
  						array('name' => 'sosignedat', 'field' => 'gks_paroxos_signature.r_signedAt'),
  						array('name' => 'soexpire', 'field' => 'gks_paroxos_signature.r_signatureExpirationDate'),
  						array('name' => 'sopn', 'field' => 'gks_aade_paroxos.paroxos_name'),
  						array('name' => 'soppn', 'field' => 'gks_payment_acquirer_with.payment_paroxos_name'),
  						array('name' => 'sopan', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  						array('name' => 'soasset', 'field' => 'gks_assets.asset_title'),
  						array('name' => 'soterminal', 'field' => 'gks_paroxos_signature.r_terminalId'),
  						array('name' => 'soprotocol', 'field' => 'gks_paroxos_signature.r_nspProtocol'),
  						
  						array('name' => 'soammount', 'field' => 'gks_paroxos_signature.r_amount'),
  						array('name' => 'sonetamount', 'field' => 'gks_paroxos_signature.r_netAmount'),
  						array('name' => 'sovatrate', 'field' => 'gks_paroxos_signature.r_vatRate'),
  						array('name' => 'sovatamount', 'field' => 'gks_paroxos_signature.r_vatAmount'),
  						array('name' => 'sogrossamount', 'field' => 'gks_paroxos_signature.r_grossAmount'),
  						array('name' => 'soadd_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),

  						array('name' => 'socompany', 'field' => 'gks_paroxos_signature.r_sellerVat'),
  						array('name' => 'sobranch', 'field' => 'gks_paroxos_signature.r_sellerBranch'),
  						array('name' => 'soinvtype', 'field' => 'gks_paroxos_signature.r_invoiceTypeCode'),
  						array('name' => 'soseries', 'field' => 'gks_paroxos_signature.r_series'),
  						array('name' => 'socontent', 'field' => 'gks_paroxos_signature.r_signedContent'),
  						array('name' => 'sosignature', 'field' => 'gks_paroxos_signature.r_signature'),
  						array('name' => 'souid', 'field' => 'gks_paroxos_signature.r_uid'),
  						array('name' => 'soserial', 'field' => 'gks_paroxos_signature.r_serial'),
  						array('name' => 'souidhash', 'field' => 'gks_paroxos_signature.r_uidHash'),


            );

$search_fields = array(
'gks_aade_paroxos.paroxos_name',
'gks_payment_acquirer_with.payment_paroxos_name',
'gks_payment_acquirers.payment_acquirer_name',
'gks_assets.asset_title',
'gks_paroxos_signature.r_terminalId',
'gks_paroxos_signature.r_nspProtocol',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
'gks_paroxos_signature.r_sellerVat',
'gks_paroxos_signature.r_series',
'gks_paroxos_signature.r_signedContent',
'gks_paroxos_signature.r_signature',
'gks_paroxos_signature.r_uid',
'gks_paroxos_signature.r_serial',
'gks_paroxos_signature.r_uidHash',

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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_paroxos_signature.*,
".GKS_WP_TABLE_PREFIX."users.ID AS add_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS add_gks_nickname, 
gks_aade_paroxos.paroxos_name, 
gks_payment_acquirer_with.payment_paroxos_name, 
gks_payment_acquirers.payment_acquirer_name, 
gks_assets.asset_title
FROM ((((gks_paroxos_signature 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_paroxos_signature.user_id_add = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_aade_paroxos ON gks_paroxos_signature.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos) 
LEFT JOIN gks_payment_acquirer_with ON gks_paroxos_signature.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with) 
LEFT JOIN gks_payment_acquirers ON gks_paroxos_signature.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_assets ON gks_paroxos_signature.asset_id = gks_assets.id_asset

where 1=1

".$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_paroxos_signature.id_paroxos_signature DESC";
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">RAW</th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socstp', '<span class="tooltipster" title="'.gks_lang('Αποστολές σε POS').'">'.gks_lang('Απστ.').'</span>'); ?></th>  

    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosignedat', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία υπογραφής').'">'.gks_lang('Στις').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soexpire', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία λήξης της υπογραφής').'">'.gks_lang('Λήξη').'</span>'); ?></th> 
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopn', gks_lang('Πάροχος υπογραφών')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soppn', gks_lang('Πάροχος πληρωμών')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopan', gks_lang('Τρόπος πληρωμής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset', gks_lang('Πάγιο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>           
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprotocol', gks_lang('Πρωτόκολλο')); ?></th>        
       

    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soammount', gks_lang('Ποσό')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonetamount', gks_lang('Καθαρή αξία')); ?></th>           
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovatrate', gks_lang('%ΦΠΑ')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovatamount', gks_lang('ΦΠΑ')); ?></th> 

    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogrossamount', gks_lang('Σύνολο')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadd_user', gks_lang('Χειριστής')); ?></th>        
    
    
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobranch', '<span class="tooltipster" title="'.gks_lang('Αριθμός Εγκατάστασης').'">'.gks_lang('Εγκα').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvtype', '<span class="tooltipster" title="'.gks_lang('Τύπος Παραστατικού').'">'.gks_lang('ΤΠ').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseries', gks_lang('Σειρά')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socontent', '<span class="tooltipster" title="'.gks_lang('Τα περιεχόμενα που κρυπτογραφήθηκαν').'">'.gks_lang('Περιεχόμενα').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosignature', gks_lang('Υπογραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souid', 'UID'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserial', 'Serial'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souidhash', 'UID Hash'); ?></th>        
     
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">IP</th>


    
  </tr>
</thead>
<tbody>

    <?php
    
    $row_list=[];$transaction_id_ids=[];
    while ($row = $result->fetch_assoc()) {
      $transaction_id_ids[]=$row['id_paroxos_signature'];
      
      $row_list[]=$row;
    }
    

    
    
    $i = 0;
    foreach ($row_list as $row) {


	$i++;
	

	
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm"><?php echo $row['id_paroxos_signature'];?></td>   
    <td class="mytdcm"><?php
          echo '<a href="admin-paroxos-signature-raw.php?id='.$row['id_paroxos_signature'].'" target="_blank">R</a>';
    ?></td>
      

    
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>
    <td nowrap class="mytdcm eftpos_sign_status_<?php echo $row['signature_status'];?>"><?php echo gks_paroxos_signature_status_descr($row['signature_status']);?></td>   
    
    
    <td nowrap class="mytdcm"><?php echo $row['count_send_to_pos'];?></td>   
    <td nowrap class="mytdcm"><?php echo showDate($row['r_signedAt']/1000, 'd/m/Y H:i:s', 1);?></td> 
    <td nowrap class="mytdcm"><?php echo showDate($row['r_signatureExpirationDate']/1000, 'd/m/Y H:i:s', 1);?></td> 
    <td nowrap class="mytdcm"><?php echo $row['paroxos_name'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['payment_paroxos_name'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['payment_acquirer_name'];?></td>   
    <td nowrap class="mytdcm"><a href="admin-assets-item.php?id=<?php echo $row['asset_id']?>"><?php echo $row['asset_title'];?></a></td>
    <td nowrap class="mytdcm"><?php echo $row['r_terminalId'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['r_nspProtocol'];?></td>   
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['r_amount']) and $row['r_amount']!=0) echo number_format($row['r_amount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['r_netAmount']) and $row['r_netAmount']!=0) echo number_format($row['r_netAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['r_vatRate']) and $row['r_vatRate']!=0) echo number_format($row['r_vatRate'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['r_vatAmount']) and $row['r_vatAmount']!=0) echo number_format($row['r_vatAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['r_grossAmount']) and $row['r_grossAmount']!=0) echo number_format($row['r_grossAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcml">
      <a href="admin-users-item.php?id=<?php echo $row['add_user_id'];?>"><?php echo $row['add_gks_nickname'];?></a>
    </td> 
 
    <td nowrap class="mytdcm"><?php echo $row['r_sellerVat'];?></td> 
    <td nowrap class="mytdcm"><?php echo $row['r_sellerBranch'];?></td> 
    <td nowrap class="mytdcm"><?php echo $row['r_invoiceTypeCode'];?></td> 
    <td nowrap class="mytdcm"><?php echo $row['r_series'];?></td> 
    

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['r_signedContent']);
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['r_signature']);
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['r_uid']);
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['r_serial']);
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['r_uidHash']);
    ?></div></div></td>
         
    <td class="mytdcm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php if (!empty($row['myip'])) echo 'V';?></a></td>
        
    
  </tr>
<?php    
    }
?>
</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>
   

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


 

  
  
});


</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


