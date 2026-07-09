<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-megeftpos-transactions.php

$my_page_title=gks_lang('Συναλλαγές Meg EFT/POS Driver');
$nav_active_array=array('accounting','accounting_eftpos','accounting_eftpos_megeftpos',);


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_megeftpos_transaction','view',0);
$perm_ret_add=gks_permission_user_can_action($my_wp_user_id, 'gks_megeftpos_transaction','add',0);
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
	'field' => 'gks_megeftpos_transaction.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_megeftpos_transaction.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'finstalments',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Δόσεις'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.installments = %V%",
    'vals' => array(
        array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -2, 'text' => gks_lang('Με δόσεις'),    'sql' => "(gks_megeftpos_transaction.installments is not null)"),
        array('value' => -3, 'text' => gks_lang('Χωρίς δόσεις'), 'sql' => "(gks_megeftpos_transaction.installments is null)"),
    ),
    'sql' => "SELECT installments AS id, installments as descr
    FROM gks_megeftpos_transaction
    where installments<>'' 
    GROUP BY installments
    order by installments",
);

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Status',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.trans_status = '%V%'",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_has_transaction_status_megeftpos('draft'),'sql' => "gks_megeftpos_transaction.trans_status='draft'"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_status_megeftpos('send'), 'sql' => "gks_megeftpos_transaction.trans_status='send'"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_status_megeftpos('fail'), 'sql' => "gks_megeftpos_transaction.trans_status='fail'"),
        array('value' => 4, 'text' => gks_eftpos_has_transaction_status_megeftpos('done'), 'sql' => "gks_megeftpos_transaction.trans_status='done'"),
    ),
);
$filters[] = array(
    'name' => 'fresult',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Result',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.responseCode = %V%",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_has_transaction_result_megeftpos(0),'sql' => "gks_megeftpos_transaction.responseCode=0"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_result_megeftpos(1),'sql' => "gks_megeftpos_transaction.responseCode=1"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_result_megeftpos(2),'sql' => "gks_megeftpos_transaction.responseCode=2"),
        array('value' => 4, 'text' => gks_eftpos_has_transaction_result_megeftpos(3),'sql' => "gks_megeftpos_transaction.responseCode=3"),
        array('value' => 5, 'text' => gks_eftpos_has_transaction_result_megeftpos(4),'sql' => "gks_megeftpos_transaction.responseCode=4"),
        array('value' => 6, 'text' => gks_eftpos_has_transaction_result_megeftpos(5),'sql' => "gks_megeftpos_transaction.responseCode=5"),
        array('value' => 7, 'text' => gks_eftpos_has_transaction_result_megeftpos(6),'sql' => "gks_megeftpos_transaction.responseCode=6"),
        array('value' => 8, 'text' => gks_eftpos_has_transaction_result_megeftpos(7),'sql' => "gks_megeftpos_transaction.responseCode=7"),
        array('value' => 9, 'text' => gks_eftpos_has_transaction_result_megeftpos(8),'sql' => "gks_megeftpos_transaction.responseCode=8"),
    ),
);

$filters[] = array(
    'name' => 'fistra',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.trans_type = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      array('value' =>10, 'text' => gks_eftpos_transaction_type_descr('control'), 'sql' => "gks_megeftpos_transaction.trans_type='control'"),
      array('value' =>11, 'text' => gks_eftpos_transaction_type_descr('echo'), 'sql' => "gks_megeftpos_transaction.trans_type='echo'"),
      array('value' =>12, 'text' => gks_eftpos_transaction_type_descr('fullvoid'), 'sql' => "gks_megeftpos_transaction.trans_type='fullvoid'"),
      array('value' =>13, 'text' => gks_eftpos_transaction_type_descr('fullvoidecrtoken'), 'sql' => "gks_megeftpos_transaction.trans_type='fullvoidecrtoken'"),
      array('value' =>14, 'text' => gks_eftpos_transaction_type_descr('fullvoiderp'), 'sql' => "gks_megeftpos_transaction.trans_type='fullvoiderp'"),
      array('value' =>15, 'text' => gks_eftpos_transaction_type_descr('merchantinfo'), 'sql' => "gks_megeftpos_transaction.trans_type='merchantinfo'"),
      array('value' =>16, 'text' => gks_eftpos_transaction_type_descr('preauthcompletion'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthcompletion'"),
      array('value' =>17, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionecrtoken'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthcompletionecrtoken'"),
      array('value' =>18, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionerp'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthcompletionerp'"),
      array('value' =>19, 'text' => gks_eftpos_transaction_type_descr('preauthnormal'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthnormal'"),
      array('value' =>20, 'text' => gks_eftpos_transaction_type_descr('preauthonetap'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthonetap'"),
      array('value' =>21, 'text' => gks_eftpos_transaction_type_descr('preauthonetapcompletion'), 'sql' => "gks_megeftpos_transaction.trans_type='preauthonetapcompletion'"),
      array('value' =>22, 'text' => gks_eftpos_transaction_type_descr('reconciliation'), 'sql' => "gks_megeftpos_transaction.trans_type='reconciliation'"),
      array('value' =>23, 'text' => gks_eftpos_transaction_type_descr('refund'), 'sql' => "gks_megeftpos_transaction.trans_type='refund'"),
      array('value' =>24, 'text' => gks_eftpos_transaction_type_descr('refundecrtoken'), 'sql' => "gks_megeftpos_transaction.trans_type='refundecrtoken'"),
      array('value' =>25, 'text' => gks_eftpos_transaction_type_descr('refundecrtokenfree'), 'sql' => "gks_megeftpos_transaction.trans_type='refundecrtokenfree'"),
      array('value' =>26, 'text' => gks_eftpos_transaction_type_descr('refunderp'), 'sql' => "gks_megeftpos_transaction.trans_type='refunderp'"),
      array('value' =>27, 'text' => gks_eftpos_transaction_type_descr('refunderpfree'), 'sql' => "gks_megeftpos_transaction.trans_type='refunderpfree'"),
      array('value' =>28, 'text' => gks_eftpos_transaction_type_descr('refundfree'), 'sql' => "gks_megeftpos_transaction.trans_type='refundfree'"),
      array('value' =>29, 'text' => gks_eftpos_transaction_type_descr('regreceiptecrtoken'), 'sql' => "gks_megeftpos_transaction.trans_type='regreceiptecrtoken'"),
      array('value' =>30, 'text' => gks_eftpos_transaction_type_descr('regreceipterp'), 'sql' => "gks_megeftpos_transaction.trans_type='regreceipterp'"),
      array('value' =>31, 'text' => gks_eftpos_transaction_type_descr('resendallfinish'), 'sql' => "gks_megeftpos_transaction.trans_type='resendallfinish'"),
      array('value' =>32, 'text' => gks_eftpos_transaction_type_descr('resendallnext'), 'sql' => "gks_megeftpos_transaction.trans_type='resendallnext'"),
      array('value' =>33, 'text' => gks_eftpos_transaction_type_descr('resendallstart'), 'sql' => "gks_megeftpos_transaction.trans_type='resendallstart'"),
      array('value' =>34, 'text' => gks_eftpos_transaction_type_descr('sale'), 'sql' => "gks_megeftpos_transaction.trans_type='sale'"),
      array('value' =>35, 'text' => gks_eftpos_transaction_type_descr('saleecrtoken'), 'sql' => "gks_megeftpos_transaction.trans_type='saleecrtoken'"),
      array('value' =>36, 'text' => gks_eftpos_transaction_type_descr('saleerp'), 'sql' => "gks_megeftpos_transaction.trans_type='saleerp'"),
      array('value' =>37, 'text' => gks_eftpos_transaction_type_descr('transactiondetails'), 'sql' => "gks_megeftpos_transaction.trans_type='transactiondetails'"),
        
    ),
);


$filters[] = array(
    'name' => 'fterminal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χειριστής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.xeiristis_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_megeftpos_transaction.xeiristis_id=0 or gks_megeftpos_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT gks_megeftpos_transaction.xeiristis_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_megeftpos_transaction LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_megeftpos_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_megeftpos_transaction.xeiristis_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

//if (count($user_companys)>1) {
//  $filters[] = array(
//    'name' => 'fcompany_id',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Εταιρεία'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_megeftpos_transaction.MID='%V%'",
//    'vals' => array(
//        //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_megeftpos_transaction.StatusId='' or gks_megeftpos_transaction.StatusId is null)"),
//    ),
//    'sql' => "
//    SELECT gks_megeftpos_transaction.MID as id, gks_company.company_title as descr
//    FROM gks_megeftpos_transaction 
//    LEFT JOIN gks_company ON gks_megeftpos_transaction.mid = gks_company.megeftpos_mid
//    WHERE gks_megeftpos_transaction.mid<>''
//    and (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
//    GROUP BY gks_megeftpos_transaction.MID, gks_company.id_company, gks_company.company_title
//    order by company_sortorder"
//    
//  );  
//}

$filters[] = array(
    'name' => 'fCardTypeName',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Κάρτας'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_megeftpos_transaction.cardType = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τύπο κάρτας'),          'sql' => "(gks_megeftpos_transaction.cardType='' or gks_megeftpos_transaction.cardType is null)"),
    ),
    'sql' => "SELECT cardType AS id, cardType as descr
    FROM gks_megeftpos_transaction 
    WHERE cardType <>''
    GROUP BY cardType
    order by cardType",
);

//$filters[] = array(
//    'name' => 'fCardIssuingBank',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => 'Acquirer',
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_megeftpos_transaction.acquirerName = '%V%'",
//    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Χωρίς Acquirer'),          'sql' => "(gks_megeftpos_transaction.acquirerName='' or gks_megeftpos_transaction.acquirerName is null)"),
//    ),
//    'sql' => "SELECT acquirerName AS id, acquirerName as descr
//    FROM gks_megeftpos_transaction 
//    WHERE acquirerName <>''
//    GROUP BY acquirerName
//    order by acquirerName",
//);



	

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_megeftpos_transaction.id_megeftpos_transaction'),
	array('name' => 'somydate', 'field' => 'gks_megeftpos_transaction.mydate_add'),
	array('name' => 'sopaidAmount', 'field' => 'gks_megeftpos_transaction.paidAmount'),
	array('name' => 'sotipAmount', 'field' => 'gks_megeftpos_transaction.tipAmount'),
	array('name' => 'sooriginalAmount', 'field' => 'gks_megeftpos_transaction.originalAmount'),
	array('name' => 'soinvoiceAmount', 'field' => 'gks_megeftpos_transaction.invoiceAmount'),
	array('name' => 'soloyaltyAmount', 'field' => 'gks_megeftpos_transaction.loyaltyAmount'),
	array('name' => 'soinstallments', 'field' => 'gks_megeftpos_transaction.installments'),
	array('name' => 'sostatus', 'field' => 'gks_megeftpos_transaction.trans_status'),
	array('name' => 'soresponseCode', 'field' => 'gks_megeftpos_transaction.responseCode'),
	array('name' => 'sotype', 'field' => 'gks_megeftpos_transaction.trans_type'),
	array('name' => 'somyerror', 'field' => 'gks_megeftpos_transaction.myerror'),
	array('name' => 'somymessage', 'field' => 'gks_megeftpos_transaction.mymessage'),
	array('name' => 'soterminal', 'field' => 'gks_megeftpos_transaction.eftTerminalId'),
	array('name' => 'sopos_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	
	array('name' => 'sonspReferenceNumber', 'field' => 'gks_megeftpos_transaction.nspReferenceNumber'),
	array('name' => 'soecrReferenceNumber', 'field' => 'gks_megeftpos_transaction.ecrReferenceNumber'),
	array('name' => 'sonspResponseCode', 'field' => 'gks_megeftpos_transaction.nspResponseCode'),
	array('name' => 'sonspResponseCodeDescription', 'field' => 'gks_megeftpos_transaction.nspResponseCodeDescription'),
	array('name' => 'soreceiptNumber', 'field' => 'gks_megeftpos_transaction.receiptNumber'),
	array('name' => 'sotransactionTimestamp', 'field' => 'gks_megeftpos_transaction.transactionTimestamp'),
	array('name' => 'sobankAuthorizationCode', 'field' => 'gks_megeftpos_transaction.bankAuthorizationCode'),
	array('name' => 'sobankCode', 'field' => 'gks_megeftpos_transaction.bankCode'),
	
	array('name' => 'socardNumber', 'field' => 'gks_megeftpos_transaction.cardNumber'),
	array('name' => 'socardType', 'field' => 'gks_megeftpos_transaction.cardType'),
	array('name' => 'socardHolder', 'field' => 'gks_megeftpos_transaction.cardHolder'),

);
     
   


$search_fields = array(
  
  'gks_megeftpos_transaction.myerror',
  'gks_megeftpos_transaction.mymessage',
  'gks_megeftpos_transaction.eftTerminalId',
  GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  'gks_megeftpos_transaction.nspReferenceNumber',
  'gks_megeftpos_transaction.ecrReferenceNumber',
  'gks_megeftpos_transaction.nspResponseCode',
  'gks_megeftpos_transaction.nspResponseCodeDescription',
  'gks_megeftpos_transaction.bankAuthorizationCode',
  'gks_megeftpos_transaction.bankCode',
  'gks_megeftpos_transaction.cardNumber',
  'gks_megeftpos_transaction.cardType',
  'gks_megeftpos_transaction.cardHolder',

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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_megeftpos_transaction.*, 
".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname

FROM gks_megeftpos_transaction
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_megeftpos_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
where 1=1

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_megeftpos_transaction DESC";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql;die();
	
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
.megeftpos_reload {
  cursor: pointer;  
}
.megeftpos_reload:before {
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

.megeftpos_status_draft {border-width: 0px;background-color: #999999;color: white;font-weight: bold;}
.megeftpos_status_send {border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.megeftpos_status_fail {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.megeftpos_status_done {border-width: 0px;background-color: green;color: white;font-weight: bold;}

.megeftpos_responseCode_-1 {border-width: 0px;background-color: black;color: white;font-weight: bold;}
.megeftpos_responseCode_0  {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.megeftpos_responseCode_1  {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.megeftpos_responseCode_2  {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.megeftpos_responseCode_3  {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
.megeftpos_responseCode_4  {border-width: 0px;background-color: #C109E1;color: white;font-weight: bold;}
.megeftpos_responseCode_5  {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
.megeftpos_responseCode_6  {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
.megeftpos_responseCode_7  {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
.megeftpos_responseCode_8  {border-width: 0px;background-color: #A30000;color: white;font-weight: bold;}
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

<?php if (1==2 and $perm_ret_add['success']) {?>
<div class="container-fluid">
  <div style="text-align: center"><a href="admin-megeftpos-refresh.php?today=1" target="_blank" style="
     background-color: #2cadbf;
      color: white;
      padding: 20px;
      margin-bottom: 10px;
      border-radius: 10px;
      font-size: 150%;
      border: 1px solid #165861;
      display: inline-block;
  "><?php echo gks_lang('Λήψη ξανά των συναλλαγών από megeftpos για σήμερα');?></a></div>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">RAW</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopaidAmount', gks_lang('Πληρωτέο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotipAmount', gks_lang('Φιλο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstallments', gks_lang('Δόσεις')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooriginalAmount', 'originalAmount'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvoiceAmount', 'invoiceAmount'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soloyaltyAmount', 'loyaltyAmount'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', 'Status'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresponseCode', 'Result'); ?></th>       
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
     
    <th class="table-dark" scope="col" style="text-align: center !important;min-width:150px;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyerror', gks_lang('Σφάλμα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;min-width:150px;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somymessage', gks_lang('Μήνυμα')); ?></th>        
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos_user', gks_lang('Χειριστής')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonspReferenceNumber', 'nspReferenceNumber'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soecrReferenceNumber', 'ecrReferenceNumber'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonspResponseCode', 'nspResponseCode'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonspResponseCodeDescription', 'nspResponseCodeDescription'); ?></th>
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreceiptNumber', 'receiptNumber'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotransactionTimestamp', 'transactionTimestamp'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobankAuthorizationCode', 'bankAuthorizationCode'); ?></th>        
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobankCode', 'bankCode'); ?></th>        
          
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardNumber', 'cardNumber'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardType', 'cardType'); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardHolder', 'cardHolder'); ?></th>  
          


       
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
    <td nowrap class="mytdcml"><?php echo $row['id_megeftpos_transaction'];?></td>   

    <td class="mytdcm"><a href="admin-megeftpos-transaction-raw.php?mtid=<?php echo $row['id_megeftpos_transaction'];?>" target="_blank">R</a></td>
      
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>  
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['paidAmount']) and $row['paidAmount']!=0) echo number_format($row['paidAmount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['tipAmount']) and $row['tipAmount']!=0) echo number_format($row['tipAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['installments']) and $row['installments']!=0) echo $row['installments'];?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['originalAmount']) and $row['originalAmount']!=0) echo number_format($row['originalAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['invoiceAmount']) and $row['invoiceAmount']!=0) echo number_format($row['invoiceAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['loyaltyAmount']) and $row['loyaltyAmount']!=0) echo number_format($row['loyaltyAmount'], 2, ',', '.');?></td>   
    


    <td nowrap class="mytdcm megeftpos_status_<?php echo $row['trans_status'];?>"><?php echo gks_eftpos_has_transaction_status_megeftpos($row['trans_status']);?></td>
    <td nowrap class="mytdcm megeftpos_responseCode_<?php echo $row['responseCode'];?>"><?php 
      echo gks_eftpos_has_transaction_result_megeftpos($row['responseCode']);?></td>
    
    <td nowrap class="mytdcm"><?php echo gks_eftpos_transaction_type_descr($row['trans_type']);?></td>
    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      if ($row['myerror']!=='null') echo $row['myerror'];
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      if ($row['myerror']!=='null') echo $row['mymessage'];
    ?></div></div></td>
    
 

  
 
    <td nowrap class="mytdcm"><?php echo $row['eftTerminalId'];?></td>   
    <td nowrap class="mytdcml div_xeiristis" data-id="<?php echo $row['id_megeftpos_transaction'];?>">
      <a href="admin-users-item.php?id=<?php echo $row['pos_user_id'];?>"><?php echo $row['pos_gks_nickname'];?></a>
    </td>
    <td nowrap class="mytdcm"><?php echo $row['nspReferenceNumber'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['ecrReferenceNumber'];?></td>   
    
    <td nowrap class="mytdcm"><?php echo $row['nspResponseCode'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['nspResponseCodeDescription'];?></td>   


    
    
    
  
    <td nowrap class="mytdcm"><?php echo $row['receiptNumber'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['transactionTimestamp'];?></td> 
    <td nowrap class="mytdcm"><?php echo $row['bankAuthorizationCode'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['bankCode'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['cardNumber'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['cardType'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['cardHolder'];?></td>   

    
    
    
    <td class="mytdcm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php if (!empty($row['myip'])) echo 'V';?></a></td>
    
  </tr>
<?php    
    }
?>
</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>
    



<?php if (ur_lo() or ur_ad()) {?>
<hr>
<h2 align="center"><?php echo gks_lang('Σύνολα σύμφωνα με τα παραπάνω φίλτρα');?></h2>
<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><?php echo gks_lang('A/A');?></th>
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap"><?php echo gks_lang('Πλήθος');?></th>   
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap">invoiceAmount</th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap">originalAmount</th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap">paidAmount</th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap">loyaltyAmount</th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="20%"  nowrap="nowrap">tipAmount</th>        
  </tr>


</thead>
<tbody>
      
<?php
$sql = "select 
count(*) as countid, 
sum(invoiceAmount) as sum_invoiceAmount, 
sum(originalAmount) as sum_originalAmount, 
sum(paidAmount) as sum_paidAmount, 
sum(loyaltyAmount) as loyaltyAmount, 
sum(tipAmount) as sum_tipAmount
from (
  SELECT gks_megeftpos_transaction.*, 
  ".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname
  FROM gks_megeftpos_transaction 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_megeftpos_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
  
  where 1=1
  ".$where . $search_where;


$sql.="
) AS mydata

";
 


$result_rep = $db_link->query($sql);        
if (!$result_rep) debug_mail(false,'error sql',$sql);
if (!$result_rep) die('sql error');





$i = 0;
while ($row_rep = $result_rep->fetch_assoc()) {
  $i++;
  
  
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['countid']) and $row_rep['countid']!=0) echo $row_rep['countid'];?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_invoiceAmount']) and $row_rep['sum_invoiceAmount']!=0) echo number_format($row_rep['sum_invoiceAmount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_originalAmount']) and $row_rep['sum_originalAmount']!=0) echo number_format($row_rep['sum_originalAmount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_paidAmount']) and $row_rep['sum_paidAmount']!=0) echo number_format($row_rep['sum_paidAmount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_loyaltyAmount']) and $row_rep['sum_loyaltyAmount']!=0) echo number_format($row_rep['sum_loyaltyAmount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_tipAmount']) and $row_rep['sum_tipAmount']!=0) echo number_format($row_rep['sum_tipAmount'],2,',','.');?></td>  
  </tr>
<?php

} 
?>
</tbody>

</table>

<?php } ?>



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


  $('.megeftpos_reload').click(function() {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    //console.log(myid);
    
    $('body').addClass("myloading");
    datasend='myid=' + myid
    $.ajax({
			url: '/my/admin-megeftpos-transaction-reload.php',
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
      			url: '/my/admin-megeftpos-transaction-select-xeiristis-exec.php',
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
			url: '/my/admin-megeftpos-transaction-select-xeiristis.php',
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


