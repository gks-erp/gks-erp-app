<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-paroxos-overview-ilyda-tf1-keys.php

$my_page_title=gks_lang('Κλειδιά για σφάλμα TF1 μετάδοσης παραστατικών σε πάροχο ΥΛΙΔΑ');
$nav_active_array=array('accounting','accounting_paroxos_overview','accounting_paroxos_overview_ilyda');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__paroxos_overview_ilyda','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







$filters = array();
$filters[] = array(
  'name' => 'fissuedAt',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Έκδοση'),
  'has_custom_date' => true,
  'field' => 'issuedAt', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'issuedAt','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "issuedAt is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "issuedAt is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'fafm',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('ΑΦΜ'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "invoiceState = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "mark is null or mark=''"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "mark<>''"),
  ),
  'sql' => "SELECT afm as id, afm as descr
FROM gks_paroxos_tf1_keys
WHERE afm<>'' GROUP BY afm ORDER BY afm;",    
);
$filters[] = array(
  'name' => 'flocal_status',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση Τοπικά'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "local_status = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT local_status as id, local_status as descr
FROM gks_paroxos_tf1_keys
WHERE local_status<>'' GROUP BY local_status ORDER BY afm;",    
);
$filters[] = array(
  'name' => 'fstatus',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση Παρόχου'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "status = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT status as id, status as descr
FROM gks_paroxos_tf1_keys
WHERE status<>'' GROUP BY status ORDER BY afm;",    
);
$filters[] = array(
  'name' => 'falgorithm',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Αλγόριθμος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "algorithm = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT algorithm as id, algorithm as descr
FROM gks_paroxos_tf1_keys
WHERE algorithm<>'' GROUP BY algorithm ORDER BY afm;",    
);
$filters[] = array(
  'name' => 'fpurpose',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Σκοπός'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "purpose = '%V%'",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "(purpose is null or purpose='')"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "purpose<>''"),
  ),
  'sql' => "SELECT purpose as id, purpose as descr
FROM gks_paroxos_tf1_keys
WHERE purpose<>'' GROUP BY purpose ORDER BY afm;",    
);

$filters[] = array(
  'name' => 'fvalidFrom',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Από'),
  'has_custom_date' => true,
  'field' => 'validFrom', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'validFrom','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "validFrom is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "validFrom is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'fvalidTo',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Έως'),
  'has_custom_date' => true,
  'field' => 'validTo', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'validTo','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "validTo is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "validTo is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'finstallationVerifiedAt',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Επαλήθευση'),
  'has_custom_date' => true,
  'field' => 'installationVerifiedAt', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'installationVerifiedAt','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "installationVerifiedAt is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "installationVerifiedAt is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'frevokedAt',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ανάκληση'),
  'has_custom_date' => true,
  'field' => 'revokedAt', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'revokedAt','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "revokedAt is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "revokedAt is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'frevokeReason',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Λόγος ανάκλησης'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "revokeReason = '%V%'",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "(revokeReason is null or revokeReason='')"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "revokeReason<>''"),
  ),
  'sql' => "SELECT revokeReason as id, revokeReason as descr
FROM gks_paroxos_tf1_keys
WHERE revokeReason<>'' GROUP BY revokeReason ORDER BY afm;",    
);

//$filters[] = array(
//  'name' => 'fsellerVatIdentifier',
//  'class' => 'filterselectbox',
//  'style' => '',
//  'title' => gks_lang('Πωλητής'),
//  'has_custom_default' => -1,
//  'multiselect' => true,
//  'field'  => "sellerVatIdentifier = '%V%'",
//  'vals' => array(
//      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
//      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
//  ),
//  'sql' => "SELECT sellerVatIdentifier as id, sellerVatIdentifier as descr
//FROM gks_paroxos_overview_ilyda_invoice_pending
//WHERE sellerVatIdentifier<>'' GROUP BY sellerVatIdentifier ORDER BY sellerVatIdentifier;",    
//);
//$filters[] = array(
//  'name' => 'fseriesNumber',
//  'class' => 'filterselectbox',
//  'style' => '',
//  'title' => gks_lang('Σειρά'),
//  'has_custom_default' => -1,
//  'multiselect' => true,
//  'field'  => "seriesNumber = '%V%'",
//  'vals' => array(
//      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
//      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
//  ),
//  'sql' => "SELECT seriesNumber as id, seriesNumber as descr
//FROM gks_paroxos_overview_ilyda_invoice_pending
//WHERE seriesNumber<>'' GROUP BY seriesNumber ORDER BY seriesNumber;",    
//);
//
//$filters[] = array(
//  'name' => 'fmark',
//  'class' => 'filterselectbox',
//  'style' => '',
//  'title' => gks_lang('ΜΑΡΚ'),
//  'has_custom_default' => -1,
//  'multiselect' => true,
//  'field'  => "mark = '%V%'",
//  'vals' => array(
//      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "mark is null or mark=''"),
//      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "mark<>''"),
//  ),
//  'sql' => "SELECT mark as id, mark as descr
//FROM gks_paroxos_overview_ilyda_invoice_pending
//WHERE mark<>'' GROUP BY mark ORDER BY mark;",    
//);




$sortable = array(
  array('name' => 'soid', 'field' => 'id_paroxos_tf1_keys'),
  array('name' => 'soissuedAt', 'field' => 'issuedAt'),
  array('name' => 'soafm', 'field' => 'afm'),
  array('name' => 'solocal_status', 'field' => 'local_status'),
  array('name' => 'sostatus', 'field' => 'status'),
  array('name' => 'sokeyIdentifier', 'field' => 'keyIdentifier'),
  array('name' => 'sosecret', 'field' => 'secret'),
  array('name' => 'sokeyVersion', 'field' => 'keyVersion'),
  array('name' => 'soalgorithm', 'field' => 'algorithm'),
  array('name' => 'sopurpose', 'field' => 'purpose'),
  array('name' => 'sovalidFrom', 'field' => 'validFrom'),
  array('name' => 'sovalidTo', 'field' => 'validTo'),
  array('name' => 'soinstallationVerifiedAt', 'field' => 'installationVerifiedAt'),
  array('name' => 'sorevokedAt', 'field' => 'revokedAt'),
  array('name' => 'sorevokeReason', 'field' => 'revokeReason'),
  array('name' => 'solinkBaseUrl', 'field' => 'linkBaseUrl'),
  array('name' => 'somydate_edit', 'field' => 'mydate_edit'),
    
    
);

$search_fields = array(
'local_status',
'afm',
'keyIdentifier',
'algorithm',
'purpose',
'status',
'revokeReason',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_paroxos_tf1_keys.*
FROM gks_paroxos_tf1_keys 
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY issuedAt desc,id_paroxos_tf1_keys desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $sql;
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


$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>
<style>
.mydivexpand {
  max-height: 18px; 
}  
.mydivexpand_on {
  max-height: unset;
}
.tf1_local_ARCHIVE   {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: #C109E1;color: white;font-weight: bold;}
.tf1_local_NOT_VALID {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: red;color: white;font-weight: bold;}
.tf1_local_VALID     {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.tf1_local_ACTIVE    {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: green;color: white;font-weight: bold;}

.tf1_remote_ISSUED   {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.tf1_remote_VERIFIED {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: green;color: white;font-weight: bold;}
.tf1_remote_REVOKED  {padding: 2px 10px;border-radius:15px;border-width: 0px;background-color: red;color: white;font-weight: bold;}

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
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', 'ID'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soissuedAt', gks_lang('Έκδοση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soafm', gks_lang('ΑΦΜ')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solocal_status', gks_lang('Κατάσταση Τοπικά')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση Παρόχου')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sokeyIdentifier', gks_lang('Κλειδί')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosecret', gks_lang('Μυστικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sokeyVersion', gks_lang('Έκδοση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soalgorithm', gks_lang('Αλγόριθμος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopurpose', gks_lang('Σκοπός')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovalidFrom', gks_lang('Από')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovalidTo', gks_lang('Έως')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstallationVerifiedAt', gks_lang('Επαλήθευση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorevokedAt', gks_lang('Ανάκληση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorevokeReason', gks_lang('Λόγος ανάκλησης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solinkBaseUrl', gks_lang('Βασικό URL')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_edit', gks_lang('Ενημέρωση')); ?></th>        
    
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>


    <td class="mytdcm"><?php echo $row['id_paroxos_tf1_keys'];?></td>
    <td class="mytdcm" nowrap><?php if (!empty($row['issuedAt'])) echo showDate(strtotime($row['issuedAt']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap><?php echo $row['afm'];?></td>
    <td class="mytdcm" nowrap><span class="tf1_local_<?php echo $row['local_status'];?>"><?php echo $row['local_status'];?></span></td>
    <td class="mytdcm" nowrap><span class="tf1_remote_<?php echo $row['status'];?>"><?php echo $row['status'];?></span></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
        echo $row['keyIdentifier'];
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
        echo $row['secret'];
    ?></div></div></td>
    <td class="mytdcm" nowrap><?php echo $row['keyVersion'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['algorithm'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['purpose'];?></td>
    <td class="mytdcm" nowrap><?php if (!empty($row['validFrom'])) echo showDate(strtotime($row['validFrom']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap><?php if (!empty($row['validTo'])) echo showDate(strtotime($row['validTo']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap><?php if (!empty($row['installationVerifiedAt'])) echo showDate(strtotime($row['installationVerifiedAt']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap><?php if (!empty($row['revokedAt'])) echo showDate(strtotime($row['revokedAt']),'d/m/Y H:i:s',1);?></td>
    
    <td class="mytdcm"><?php echo $row['revokeReason'];?></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
        echo $row['linkBaseUrl'];
    ?></div></div></td>
        
    <td class="mytdcm" nowrap><?php if (!empty($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']),'d/m/Y H:i:s',1);?></td>





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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__paroxos_overview_ilyda','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__paroxos_overview_ilyda','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__paroxos_overview_ilyda','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fissuedAt-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fissuedAt-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fvalidFrom-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fvalidFrom-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fvalidTo-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fvalidTo-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#finstallationVerifiedAt-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#finstallationVerifiedAt-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#frevokedAt-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#frevokedAt-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fissuedAt' || sname == 'fvalidFrom' || sname == 'fvalidTo' || sname == 'finstallationVerifiedAt' || sname == 'frevokedAt' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fissuedAt' || sname == 'fvalidFrom' || sname == 'fvalidTo' || sname == 'finstallationVerifiedAt' || sname == 'frevokedAt' || gks_custom_filters_date_elems.includes(sname)) {
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


