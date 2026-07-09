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

$my_page_title=gks_lang('Συναλλαγές Viva');
$nav_active_array=array('accounting','accounting_eftpos','accounting_eftpos_viva');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_viva_transaction','view',0);
$perm_ret_add=gks_permission_user_can_action($my_wp_user_id, 'gks_viva_transaction','add',0);
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
	'field' => 'gks_viva_transaction.add_date',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_viva_transaction.add_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

if (count($user_companys)>1) {
  $filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.MerchantId='%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)"),
    ),
    'sql' => "
    SELECT gks_viva_transaction.MerchantId as id, gks_company.company_title as descr
    FROM gks_viva_transaction 
    LEFT JOIN gks_company ON gks_viva_transaction.MerchantId = gks_company.viva_merchant_id
    WHERE gks_viva_transaction.MerchantId<>''
    and (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
    GROUP BY gks_viva_transaction.MerchantId, gks_company.id_company, gks_company.company_title
    order by company_sortorder"
    
  );  
}

$filters[] = array(
    'name' => 'fStatusId',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'StatusId',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.StatusId = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Κενό StatusId'),          'sql' => "(gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)"),
    ),
    'sql' => "SELECT StatusId as id,StatusId as descr
    FROM gks_viva_transaction 
    WHERE StatusId<>''
    GROUP BY StatusId
    order by StatusId",
);


$filters[] = array(
    'name' => 'fistra',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.TransactionTypeId = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -10, 'text' => gks_lang('Συναλλαγή'), 'sql' => "gks_viva_transaction.TransactionId is not null"),
        array('value' => -20, 'text' => "Events",    'sql' => "gks_viva_transaction.TransactionTypeName is null"),
        array('value' => -30, 'text' => "Event",     'sql' => "gks_viva_transaction.TransactionId is not null and gks_viva_transaction.TransactionTypeName is null"),
    ),
    'sql' => "SELECT TransactionTypeId AS id, TransactionTypeName as descr
    FROM gks_viva_transaction 
    WHERE TransactionTypeName <>''
    GROUP BY TransactionTypeId, TransactionTypeName
    order by TransactionTypeId",
);

$filters[] = array(
    'name' => 'fterminal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χειριστής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.xeiristis_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_viva_transaction.xeiristis_id=0 or gks_viva_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT gks_viva_transaction.xeiristis_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_viva_transaction LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viva_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_viva_transaction.xeiristis_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

$filters[] = array(
    'name' => 'fCardTypeName',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Κάρτας'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.CardTypeName = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τύπο κάρτας'),          'sql' => "(gks_viva_transaction.CardTypeName='' or gks_viva_transaction.CardTypeName is null)"),
    ),
    'sql' => "SELECT CardTypeName AS id, CardTypeName as descr
    FROM gks_viva_transaction 
    WHERE CardTypeName Is Not Null
    GROUP BY CardTypeName
    order by CardTypeName",
);

$filters[] = array(
    'name' => 'fCardIssuingBank',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τράπεζα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viva_transaction.CardIssuingBank = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τράπεζα'),          'sql' => "(gks_viva_transaction.CardIssuingBank='' or gks_viva_transaction.CardIssuingBank is null)"),
    ),
    'sql' => "SELECT CardIssuingBank AS id, CardIssuingBank as descr
    FROM gks_viva_transaction 
    WHERE CardIssuingBank Is Not Null
    GROUP BY CardIssuingBank
    order by CardIssuingBank",
);



	

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_viva_transaction.id_viva_transaction'),
  						array('name' => 'somydate', 'field' => 'gks_viva_transaction.add_date'),
  						array('name' => 'soammount', 'field' => 'gks_viva_transaction.Amount'),
  						array('name' => 'sotip', 'field' => 'gks_viva_transaction.TipAmount'),
  						array('name' => 'soinstallments', 'field' => 'gks_viva_transaction.installments'),
  						
  						array('name' => 'socommission', 'field' => 'gks_viva_transaction.TotalCommission'),
  						array('name' => 'sofee', 'field' => 'gks_viva_transaction.TotalFee'),
  						array('name' => 'sotype', 'field' => 'gks_viva_transaction.TransactionTypeName'),
  						array('name' => 'soterminal', 'field' => 'gks_viva_transaction.TerminalId'),
  						array('name' => 'somagazi', 'field' => 'gks_magazia.magazi_title'),
  						array('name' => 'sofotografos', 'field' => GKS_WP_TABLE_PREFIX.'users_fotografos.gks_nickname'),
  						array('name' => 'sopos_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'sotraid', 'field' => 'gks_viva_transaction.TransactionId'),
  						array('name' => 'soaadeid', 'field' => 'gks_viva_transaction.aadeTransactionId'),
  						array('name' => 'soorder', 'field' => 'gks_viva_transaction.OrderCode'),
  						array('name' => 'socardtype', 'field' => 'gks_viva_transaction.CardTypeName'),
  						array('name' => 'sobankid', 'field' => 'gks_viva_transaction.BankId'),
  						array('name' => 'socard', 'field' => 'gks_viva_transaction.CardNumber'),
  						array('name' => 'sobank', 'field' => 'gks_viva_transaction.CardIssuingBank'),
  						array('name' => 'soname', 'field' => 'gks_viva_transaction.FullName'),
  						array('name' => 'soemail', 'field' => 'gks_viva_transaction.Email'),
  						array('name' => 'sophone', 'field' => 'gks_viva_transaction.Phone'),
  						array('name' => 'soref', 'field' => 'gks_viva_transaction.MerchantTrns'),
  						array('name' => 'soStatusId', 'field' => 'gks_viva_transaction.StatusId'),
  						array('name' => 'somymessage', 'field' => 'gks_viva_transaction.mymessage'),
  						array('name' => 'sodescription', 'field' => 'gks_viva_transaction.Description'),
  						array('name' => 'socompany', 'field' => 'gks_viva_transaction.MerchantId'),
  						array('name' => 'sotransactionTypeId', 'field' => 'gks_viva_transaction.TransactionTypeId'),
  						

            );

$search_fields = array(
  'gks_viva_transaction.TransactionId',
  'gks_viva_transaction.aadeTransactionId',
  'gks_viva_transaction.TerminalId',
  'gks_viva_transaction.OrderCode',
  'gks_viva_transaction.CardNumber',
  'gks_viva_transaction.FullName',
  'gks_viva_transaction.Email',
  'gks_viva_transaction.Phone',
  'gks_viva_transaction.Description',
  'gks_viva_transaction.CardIssuingBank',
  'gks_viva_transaction.mymessage',
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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_viva_transaction.*, 
".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
gks_company.company_title
FROM (gks_viva_transaction
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viva_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_company ON gks_viva_transaction.MerchantId = gks_company.viva_merchant_id
where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_viva_transaction.add_date DESC";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;




//echo $query;
//die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'admin-log-emails.php error sql',$sql);
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
.viva_reload {
  cursor: pointer;  
}
.viva_reload:before {
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

.StatusId_F {
  border-width: 0px;background-color: green;color: white;font-weight: bold;"
}

.StatusId_E {
  border-width: 0px;background-color: red;color: white;font-weight: bold;
}
.StatusId_X {
  border-width: 0px;background-color: red;color: white;font-weight: bold;
}
.StatusId_R {
  border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;
}

.viva_status_wait {border-width: 0px;background-color: #F4F471;color: black;font-weight: bold;}
.viva_status_draft {border-width: 0px;background-color: #999999;color: white;font-weight: bold;}
.viva_status_processed {border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.viva_status_done {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.viva_status_request {border-width: 0px;background-color: #B6AC77;color: white;font-weight: bold;}
.viva_status_canceled {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.viva_status_abort {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
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
  <div style="text-align: center"><a href="admin-viva-refresh.php?today=1" target="_blank" style="
     background-color: #2cadbf;
      color: white;
      padding: 20px;
      margin-bottom: 10px;
      border-radius: 10px;
      font-size: 150%;
      border: 1px solid #165861;
      display: inline-block;
  "><?php echo gks_lang('Λήψη ξανά των συναλλαγών από Viva για σήμερα');?></a></div>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Αποδεικτικό');?>"><?php echo gks_lang('Α');?></span></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">RAW</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">Re</th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soammount', gks_lang('Ποσό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotip', '<span class="tooltipster" title="'.gks_lang('Φιλοδώρημα').'" >'.gks_lang('Φιλο').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstallments', gks_lang('Δόσεις')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socommission', gks_lang('Προμήθεια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofee', gks_lang('Τέλος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soStatusId', 'StatusId'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somymessage', gks_lang('Μήνυμα')); ?></th>                   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', '<span class="tooltipster" title="'.gks_lang('Τύπος μηνύματος συναλλαγής').'">'.gks_lang('Τύπος Μ').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos_user', gks_lang('Χειριστής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soref', '<span class="tooltipster" title="'.gks_lang('Αναφορά Πληρωμής').'">'.gks_lang('Α.Πλ.').'</span> '); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th> 
    
           
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoint', '<span class="tooltipster" title="'.gks_lang('Στίγμα').'">'.gks_lang('Στ').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soTransactionTypeId', '<span class="tooltipster" title="'.gks_lang('Τύπος συναλλαγής TransactionTypeId').'">'.gks_lang('Τύπος Σ').'</span>'); ?></th> 
    
           
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotraid', '<span class="tooltipster" title="'.gks_lang('TransactionId').'">'.gks_lang('ID Πληρωμής').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaadeid', '<span class="tooltipster" title="'.gks_lang('aade TransactionId').'">'.gks_lang('ΑΑΔΕ').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soorder', '<span class="tooltipster" title="'.gks_lang('Κωδικός Πληρωμής').'">'.gks_lang('Κ.Πληρωμής').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardtype', '<span class="tooltipster" title="'.gks_lang('Τύπος Κάρτας').'">'.gks_lang('Τυ.Κα.').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobankid', 'BankID'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socard', gks_lang('Κάρτα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobank', gks_lang('Τράπεζα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', 'email'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescription', gks_lang('Περιγραφή')); ?></th>        
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
    <td nowrap class="mytdcml"><?php echo $row['id_viva_transaction'];?></td>   

    <td class="mytdcm"><?php
      if (trim_gks($row['TransactionId'])!='') { ?>
      <a href="<?php echo GKS_VIVA_URL_WWW;?>/web/receipt?tid=<?php echo $row['TransactionId'];?>" target="_blank">A</a>
      <?php } ?></td>
    <td class="mytdcm"><a href="admin-viva-transaction-raw.php?mtid=<?php echo $row['id_viva_transaction'];?>" target="_blank">R</a></td>
    <td class="mytdcm"><?php
      if (trim_gks($row['TransactionId'])!='') { ?>
        <i class="fas fa-sync viva_reload" data-id="<?php echo $row['id_viva_transaction'];?>"></i>
      <?php } ?></td>  
      
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['add_date']), 'd/m/Y H:i:s', 1);?></td>  
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['Amount']) and $row['Amount']!=0) echo number_format($row['Amount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['TipAmount']) and $row['TipAmount']!=0) echo number_format($row['TipAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if ($row['installments']!=0) echo $row['installments'];?></td>   

    <td nowrap class="mytdcm"><?php if (isset($row['TotalCommission']) and $row['TotalCommission']!=0) echo number_format($row['TotalCommission'], 2, ',', '.') ;?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['TotalFee']) and $row['TotalFee']!=0) echo number_format($row['TotalFee'], 2, ',', '.') ;?></td>   
    <!--
    <td nowrap class="mytdcm"><?php if (isset($row['RedeemedAmount']) and $row['RedeemedAmount']!=0) echo $row['RedeemedAmount'];?></td>   
    -->
    
    <td nowrap class="mytdcm <?php
      $temp=trim_gks($row['StatusId']);
      $temp_txt=$temp;
      switch ($temp) {   
        case 'F':
        case 'E':
        case 'X':
        case 'R':
          echo 'StatusId_'.$temp;
          break;  
 
        case 'gks_wait':        echo 'viva_status_wait'; $temp_txt=gks_lang('Σε Αναμονή','part4','viva_status_descr'); break;
        case 'gks_processed':   echo 'viva_status_processed'; $temp_txt=gks_lang('Σε εξέλιξη','part4','viva_status_descr'); break;
        case 'gks_done':        echo 'viva_status_done'; $temp_txt=gks_lang('Έγινε','part4','viva_status_descr'); break; 
        case 'gks_request':     echo 'viva_status_request'; $temp_txt=gks_lang('Σε αίτηση','part4','viva_status_descr'); break;
        case 'gks_canceled':    echo 'viva_status_canceled'; $temp_txt=gks_lang('Ακυρώθηκε','part4','viva_status_descr'); break;
        case 'gks_abort':       echo 'viva_status_abort'; $temp_txt=gks_lang('Ματαιώθηκε','part4','viva_status_descr'); break;
          

        
        default:
      }
      
      ?>"><?php echo $temp_txt;?></td> 
      
        
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['mymessage']);
    ?></div></div></td>
    <td nowrap class="mytdcml"><?php 
      if (!empty($row['TransactionTypeName'])) {
        echo $row['TransactionTypeName'];
      } else if (intval($row['EventTypeId'])<>0) {
        echo 'Event ID '.$row['EventTypeId'];  
      } else {
        echo '--';
      }
    ?></td>   
    <td nowrap class="mytdcml"><?php echo $row['TerminalId'];?></td>   
    <td nowrap class="mytdcml div_xeiristis" data-id="<?php echo $row['id_viva_transaction'];?>">
      <a href="admin-users-item.php?id=<?php echo $row['pos_user_id'];?>"><?php echo $row['pos_gks_nickname'];?></a>
    </td>
    <td nowrap class="mytdcml"><?php echo $row['MerchantTrns'];?></td>   
    <td nowrap class="mytdcml"><?php 
      if (!empty($row['company_title'])) {
        echo $row['company_title'];
      } else {
        echo '<span title="'.$row['MerchantId'].'">'.$row['CompanyName'].'</span>';
      }
    ?></td>   
   
    <td nowrap class="mytdcm"><?php 
    if (isset($row['Latitude']) and isset($row['Longitude'])) {
      echo '<a href="https://www.google.com/maps/search/?api=1&query='.$row['Latitude'].','.$row['Longitude'].'" target="_blank"><i class="fas fa-map-marker-alt gks_marker_gps"></i></a>';
    }
    ?></td>
    
    
    <td nowrap class="mytdcml"><?php echo gks_eftpos_has_transaction_type_viva($row['TransactionTypeId']);?></td>   
    <td nowrap class="mytdcml"><?php echo $row['TransactionId'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['aadeTransactionId'];?></td>   
    
        
    <td nowrap class="mytdcml"><?php echo $row['OrderCode'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['CardTypeName'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['BankId'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['CardNumber'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['CardIssuingBank'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['FullName'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['Email'];?></td>   
    
       
    <td nowrap class="mytdcml"><?php echo $row['Phone'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['Description'];?></td>   
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
    <th class="table-dark" scope="col" style="text-align: left !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Εταιρεία');?></th> 
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Πλήθος');?></th>   
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Ποσό');?></th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Φιλοδώρημα');?></th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Προμήθεια');?></th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Τέλος');?></th>        
  </tr>


</thead>
<tbody>
      
<?php
$sql = "select MerchantId, company_title,company_sortorder,
count(*) as countid, sum(Amount) as sum_Amount, sum(TipAmount) as sum_TipAmount,sum(TotalCommission) as sum_TotalCommission, sum(TotalFee) as sum_TotalFee
from (
  SELECT gks_viva_transaction.*, 
  ".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
  gks_company.company_title,
  gks_company.company_sortorder
  FROM (gks_viva_transaction 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viva_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_company ON gks_viva_transaction.MerchantId = gks_company.viva_merchant_id
  where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
  ".$where . $search_where;


$sql.="
) AS mydata
group by MerchantId, company_title, company_sortorder
order by company_sortorder
";
 


$result_rep = $db_link->query($sql);        
if (!$result_rep) debug_mail(false,'error sql',$sql);
if (!$result_rep) die('sql error');


$sum_countid=0;
$sum_sum_Amount=0;
$sum_sum_TipAmount=0;
$sum_sum_TotalCommission=0;
$sum_sum_TotalFee=0;

$i = 0;
while ($row_rep = $result_rep->fetch_assoc()) {
  $i++;
  $sum_countid+=$row_rep['countid'];
  $sum_sum_Amount+=$row_rep['sum_Amount'];
  $sum_sum_TipAmount+=$row_rep['sum_TipAmount'];
  $sum_sum_TotalCommission+=$row_rep['sum_TotalCommission'];
  $sum_sum_TotalFee+=$row_rep['sum_TotalFee'];
  
  
  
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td class="mytdcml"><?php if (!empty($row_rep['company_title'])) {
        echo $row_rep['company_title'];
      } else {
        echo '<span title="'.$row_rep['MerchantId'].'">MID</span>';
      }
      ?></td> 

 
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['countid']) and $row_rep['countid']!=0) echo $row_rep['countid'];?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_Amount']) and $row_rep['sum_Amount']!=0) echo number_format($row_rep['sum_Amount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_TipAmount']) and $row_rep['sum_TipAmount']!=0) echo number_format($row_rep['sum_TipAmount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_TotalCommission']) and $row_rep['sum_TotalCommission']!=0) echo number_format($row_rep['sum_TotalCommission'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_TotalFee']) and $row_rep['sum_TotalFee']!=0) echo number_format($row_rep['sum_TotalFee'],2,',','.');?></td>  
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
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_TotalCommission,2,',','.');?></td>  
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_TotalFee,2,',','.');?></td>  
  </tr>
</tfoot>
</table>

<?php } ?>



<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;margin-top:40px;border-collapse: collapse;">
<tbody>
  <tr>
    <th class="table-dark" scope="col" width="20%" style="text-align:center;">StatusId</th>
    <th class="table-dark" scope="col" width="80%">Description</th>
  </tr>
  <tr>
    <td class="mytdcm">E</td>
    <td class="mytdcml">The transaction was not completed because of an error (PAYMENT UNSUCCESSFUL)</td>
  </tr>
  <tr>
    <td class="mytdcm">A</td>
    <td class="mytdcml">The transaction is in progress (PAYMENT PENDING)</td>
  </tr>
  <tr>
    <td class="mytdcm">M</td>
    <td class="mytdcml">The cardholder has disputed the transaction with the issuing Bank</td>
  </tr>
  <tr>
    <td class="mytdcm">MA</td>
    <td class="mytdcml">Dispute Awaiting Response</td>
  </tr>
  <tr>
    <td class="mytdcm">MI</td>
    <td class="mytdcml">Dispute in Progress</td>
  </tr>
  <tr>
    <td class="mytdcm">ML</td>
    <td class="mytdcml">A disputed transaction has been refunded (Dispute Lost)</td>
  </tr>
  <tr>
    <td class="mytdcm">MW</td>
    <td class="mytdcml">Dispute Won</td>
  </tr>
  <tr>
    <td class="mytdcm">MS</td>
    <td class="mytdcml">Suspected Dispute</td>
  </tr>
  <tr>
    <td class="mytdcm">X</td>
    <td class="mytdcml">The transaction was cancelled by the merchant</td>
  </tr>
  <tr>
    <td class="mytdcm">R</td>
    <td>The transaction has been fully or partially refunded</td>
  </tr>
  <tr>
    <td class="mytdcm">F</td>
    <td class="mytdcml">The transaction has been completed successfully (PAYMENT SUCCESSFUL)</td>
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


  $('.viva_reload').click(function() {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    //console.log(myid);
    
    $('body').addClass("myloading");
    datasend='myid=' + myid
    $.ajax({
			url: '/my/admin-viva-transaction-reload.php',
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
      			url: '/my/admin-viva-transaction-select-xeiristis-exec.php',
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
			url: '/my/admin-viva-transaction-select-xeiristis.php',
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


