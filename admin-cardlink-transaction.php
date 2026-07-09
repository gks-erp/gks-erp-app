<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-cardlink-transactions.php

$my_page_title=gks_lang('Συναλλαγές Cardlink');
$nav_active_array=array('accounting','accounting_eftpos','accounting_eftpos_cardlink');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_cardlink_transaction','view',0);
$perm_ret_add=gks_permission_user_can_action($my_wp_user_id, 'gks_cardlink_transaction','add',0);
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
	'field' => 'gks_cardlink_transaction.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_cardlink_transaction.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'finstalments',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Δόσεις'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.numberOfInstallments = %V%",
    'vals' => array(
        array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -2, 'text' => gks_lang('Με δόσεις'),    'sql' => "(gks_cardlink_transaction.numberOfInstallments is not null)"),
        array('value' => -3, 'text' => gks_lang('Χωρίς δόσεις'), 'sql' => "(gks_cardlink_transaction.numberOfInstallments is null)"),
    ),
    'sql' => "SELECT numberOfInstallments AS id, numberOfInstallments as descr
    FROM gks_cardlink_transaction
    where numberOfInstallments<>'' 
    GROUP BY numberOfInstallments
    order by numberOfInstallments",
);

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Status',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.trans_status = '%V%'",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_has_transaction_status_cardlink('draft'),'sql' => "gks_cardlink_transaction.trans_status='draft'"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_status_cardlink('send'), 'sql' => "gks_cardlink_transaction.trans_status='send'"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_status_cardlink('fail'), 'sql' => "gks_cardlink_transaction.trans_status='fail'"),
        array('value' => 4, 'text' => gks_eftpos_has_transaction_status_cardlink('done'), 'sql' => "gks_cardlink_transaction.trans_status='done'"),
    ),
);
$filters[] = array(
    'name' => 'fresult',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Result',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.myResult = %V%",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_has_transaction_result_cardlink('00'),'sql' => "gks_cardlink_transaction.responseCode='00'"),
        array('value' => 2, 'text' => gks_eftpos_has_transaction_result_cardlink('51'),'sql' => "gks_cardlink_transaction.responseCode='51'"),
        array('value' => 3, 'text' => gks_eftpos_has_transaction_result_cardlink('DC'),'sql' => "gks_cardlink_transaction.responseCode='DC'"),
        array('value' => 4, 'text' => gks_eftpos_has_transaction_result_cardlink('UD'),'sql' => "gks_cardlink_transaction.responseCode='UD'"),
        array('value' => 5, 'text' => gks_eftpos_has_transaction_result_cardlink('UC'),'sql' => "gks_cardlink_transaction.responseCode='UC'"),
        array('value' => 6, 'text' => gks_eftpos_has_transaction_result_cardlink('LC'),'sql' => "gks_cardlink_transaction.responseCode='LC'"),
        array('value' => 7, 'text' => gks_eftpos_has_transaction_result_cardlink('TO'),'sql' => "gks_cardlink_transaction.responseCode='TO'"),
        array('value' => 8, 'text' => gks_eftpos_has_transaction_result_cardlink('CE'),'sql' => "gks_cardlink_transaction.responseCode='CE'"),
        array('value' => 9, 'text' => gks_eftpos_has_transaction_result_cardlink('ND'),'sql' => "gks_cardlink_transaction.responseCode='ND'"),
        array('value' =>10, 'text' => gks_eftpos_has_transaction_result_cardlink('NA'),'sql' => "gks_cardlink_transaction.responseCode='NA'"),
        array('value' =>11, 'text' => gks_eftpos_has_transaction_result_cardlink('IM'),'sql' => "gks_cardlink_transaction.responseCode='IM'"),
        array('value' =>12, 'text' => gks_eftpos_has_transaction_result_cardlink('JF'),'sql' => "gks_cardlink_transaction.responseCode='JF'"),
        array('value' =>13, 'text' => gks_eftpos_has_transaction_result_cardlink('UN'),'sql' => "gks_cardlink_transaction.responseCode='UN'"),
        array('value' =>14, 'text' => gks_eftpos_has_transaction_result_cardlink('WP'),'sql' => "gks_cardlink_transaction.responseCode='WP'"),
        array('value' =>15, 'text' => gks_eftpos_has_transaction_result_cardlink('IS'),'sql' => "gks_cardlink_transaction.responseCode='IS'"),
        array('value' =>16, 'text' => gks_eftpos_has_transaction_result_cardlink('ID'),'sql' => "gks_cardlink_transaction.responseCode='ID'"),
        array('value' =>17, 'text' => gks_eftpos_has_transaction_result_cardlink('IB'),'sql' => "gks_cardlink_transaction.responseCode='IB'"),
        array('value' =>18, 'text' => gks_eftpos_has_transaction_result_cardlink('EC'),'sql' => "gks_cardlink_transaction.responseCode='EC'"),
        array('value' =>19, 'text' => gks_eftpos_has_transaction_result_cardlink('WC'),'sql' => "gks_cardlink_transaction.responseCode='WC'"),
        array('value' =>20, 'text' => gks_eftpos_has_transaction_result_cardlink('VT'),'sql' => "gks_cardlink_transaction.responseCode='VT'"),
        array('value' =>21, 'text' => gks_eftpos_has_transaction_result_cardlink('XC'),'sql' => "gks_cardlink_transaction.responseCode='XC'"),
        array('value' =>22, 'text' => gks_eftpos_has_transaction_result_cardlink('RC'),'sql' => "gks_cardlink_transaction.responseCode='RC'"),
        array('value' =>23, 'text' => gks_eftpos_has_transaction_result_cardlink('CL'),'sql' => "gks_cardlink_transaction.responseCode='CL'"),
        array('value' =>24, 'text' => gks_eftpos_has_transaction_result_cardlink('Y1'),'sql' => "gks_cardlink_transaction.responseCode='Y1'"),
        array('value' =>25, 'text' => gks_eftpos_has_transaction_result_cardlink('Y2'),'sql' => "gks_cardlink_transaction.responseCode='Y2'"),
        array('value' =>26, 'text' => gks_eftpos_has_transaction_result_cardlink('Y3'),'sql' => "gks_cardlink_transaction.responseCode='Y3'"),
        array('value' =>27, 'text' => gks_eftpos_has_transaction_result_cardlink('Z1'),'sql' => "gks_cardlink_transaction.responseCode='Z1'"),
        array('value' =>28, 'text' => gks_eftpos_has_transaction_result_cardlink('Z2'),'sql' => "gks_cardlink_transaction.responseCode='Z2'"),
        array('value' =>29, 'text' => gks_eftpos_has_transaction_result_cardlink('Z3'),'sql' => "gks_cardlink_transaction.responseCode='Z3'"),
        array('value' =>30, 'text' => gks_eftpos_has_transaction_result_cardlink('EA'),'sql' => "gks_cardlink_transaction.responseCode='EA'"),
        array('value' =>31, 'text' => gks_eftpos_has_transaction_result_cardlink('NT'),'sql' => "gks_cardlink_transaction.responseCode='NT'"),
    ),
);

$filters[] = array(
    'name' => 'fistra',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.trans_type = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      array('value' =>10, 'text' => gks_eftpos_transaction_type_descr('control'), 'sql' => "gks_cardlink_transaction.trans_type='control'"),
      array('value' =>11, 'text' => gks_eftpos_transaction_type_descr('echo'), 'sql' => "gks_cardlink_transaction.trans_type='echo'"),
      array('value' =>12, 'text' => gks_eftpos_transaction_type_descr('fullvoid'), 'sql' => "gks_cardlink_transaction.trans_type='fullvoid'"),
      array('value' =>13, 'text' => gks_eftpos_transaction_type_descr('fullvoidecrtoken'), 'sql' => "gks_cardlink_transaction.trans_type='fullvoidecrtoken'"),
      array('value' =>14, 'text' => gks_eftpos_transaction_type_descr('fullvoiderp'), 'sql' => "gks_cardlink_transaction.trans_type='fullvoiderp'"),
      array('value' =>15, 'text' => gks_eftpos_transaction_type_descr('merchantinfo'), 'sql' => "gks_cardlink_transaction.trans_type='merchantinfo'"),
      array('value' =>16, 'text' => gks_eftpos_transaction_type_descr('preauthcompletion'), 'sql' => "gks_cardlink_transaction.trans_type='preauthcompletion'"),
      array('value' =>17, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionecrtoken'), 'sql' => "gks_cardlink_transaction.trans_type='preauthcompletionecrtoken'"),
      array('value' =>18, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionerp'), 'sql' => "gks_cardlink_transaction.trans_type='preauthcompletionerp'"),
      array('value' =>19, 'text' => gks_eftpos_transaction_type_descr('preauthnormal'), 'sql' => "gks_cardlink_transaction.trans_type='preauthnormal'"),
      array('value' =>20, 'text' => gks_eftpos_transaction_type_descr('preauthonetap'), 'sql' => "gks_cardlink_transaction.trans_type='preauthonetap'"),
      array('value' =>21, 'text' => gks_eftpos_transaction_type_descr('preauthonetapcompletion'), 'sql' => "gks_cardlink_transaction.trans_type='preauthonetapcompletion'"),
      array('value' =>22, 'text' => gks_eftpos_transaction_type_descr('reconciliation'), 'sql' => "gks_cardlink_transaction.trans_type='reconciliation'"),
      array('value' =>23, 'text' => gks_eftpos_transaction_type_descr('refund'), 'sql' => "gks_cardlink_transaction.trans_type='refund'"),
      array('value' =>24, 'text' => gks_eftpos_transaction_type_descr('refundecrtoken'), 'sql' => "gks_cardlink_transaction.trans_type='refundecrtoken'"),
      array('value' =>25, 'text' => gks_eftpos_transaction_type_descr('refundecrtokenfree'), 'sql' => "gks_cardlink_transaction.trans_type='refundecrtokenfree'"),
      array('value' =>26, 'text' => gks_eftpos_transaction_type_descr('refunderp'), 'sql' => "gks_cardlink_transaction.trans_type='refunderp'"),
      array('value' =>27, 'text' => gks_eftpos_transaction_type_descr('refunderpfree'), 'sql' => "gks_cardlink_transaction.trans_type='refunderpfree'"),
      array('value' =>28, 'text' => gks_eftpos_transaction_type_descr('refundfree'), 'sql' => "gks_cardlink_transaction.trans_type='refundfree'"),
      array('value' =>29, 'text' => gks_eftpos_transaction_type_descr('regreceiptecrtoken'), 'sql' => "gks_cardlink_transaction.trans_type='regreceiptecrtoken'"),
      array('value' =>30, 'text' => gks_eftpos_transaction_type_descr('regreceipterp'), 'sql' => "gks_cardlink_transaction.trans_type='regreceipterp'"),
      array('value' =>31, 'text' => gks_eftpos_transaction_type_descr('resendallfinish'), 'sql' => "gks_cardlink_transaction.trans_type='resendallfinish'"),
      array('value' =>32, 'text' => gks_eftpos_transaction_type_descr('resendallnext'), 'sql' => "gks_cardlink_transaction.trans_type='resendallnext'"),
      array('value' =>33, 'text' => gks_eftpos_transaction_type_descr('resendallstart'), 'sql' => "gks_cardlink_transaction.trans_type='resendallstart'"),
      array('value' =>34, 'text' => gks_eftpos_transaction_type_descr('sale'), 'sql' => "gks_cardlink_transaction.trans_type='sale'"),
      array('value' =>35, 'text' => gks_eftpos_transaction_type_descr('saleecrtoken'), 'sql' => "gks_cardlink_transaction.trans_type='saleecrtoken'"),
      array('value' =>36, 'text' => gks_eftpos_transaction_type_descr('saleerp'), 'sql' => "gks_cardlink_transaction.trans_type='saleerp'"),
      array('value' =>37, 'text' => gks_eftpos_transaction_type_descr('transactiondetails'), 'sql' => "gks_cardlink_transaction.trans_type='transactiondetails'"),
        
    ),
);


$filters[] = array(
    'name' => 'fterminal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χειριστής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.xeiristis_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_cardlink_transaction.xeiristis_id=0 or gks_cardlink_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT gks_cardlink_transaction.xeiristis_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_cardlink_transaction LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_cardlink_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_cardlink_transaction.xeiristis_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
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
    'field'  => "gks_cardlink_transaction.MID='%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_cardlink_transaction.StatusId='' or gks_cardlink_transaction.StatusId is null)"),
    ),
    'sql' => "
    SELECT gks_cardlink_transaction.MID as id, gks_company.company_title as descr
    FROM gks_cardlink_transaction 
    LEFT JOIN gks_company ON gks_cardlink_transaction.mid = gks_company.cardlink_mid
    WHERE gks_cardlink_transaction.mid<>''
    and (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
    GROUP BY gks_cardlink_transaction.MID, gks_company.id_company, gks_company.company_title
    order by company_sortorder"
    
  );  
}

$filters[] = array(
    'name' => 'fCardTypeName',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Κάρτας'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.cardType = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τύπο κάρτας'),          'sql' => "(gks_cardlink_transaction.cardType='' or gks_cardlink_transaction.cardType is null)"),
    ),
    'sql' => "SELECT cardType AS id, cardType as descr
    FROM gks_cardlink_transaction 
    WHERE cardType <>''
    GROUP BY cardType
    order by cardType",
);

$filters[] = array(
    'name' => 'fCardIssuingBank',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Acquirer',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_cardlink_transaction.acquirerName = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Acquirer'),          'sql' => "(gks_cardlink_transaction.acquirerName='' or gks_cardlink_transaction.acquirerName is null)"),
    ),
    'sql' => "SELECT acquirerName AS id, acquirerName as descr
    FROM gks_cardlink_transaction 
    WHERE acquirerName <>''
    GROUP BY acquirerName
    order by acquirerName",
);



	

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_cardlink_transaction.id_cardlink_transaction'),
	array('name' => 'somydate', 'field' => 'gks_cardlink_transaction.mydate_add'),
	array('name' => 'soammount', 'field' => 'gks_cardlink_transaction.Amount'),
	array('name' => 'soamountpayable', 'field' => 'gks_cardlink_transaction.amountPayable'),
	array('name' => 'soinstallments', 'field' => 'gks_cardlink_transaction.numberOfInstallments'),
	array('name' => 'sostatus', 'field' => 'gks_cardlink_transaction.trans_status'),
	array('name' => 'soresult', 'field' => 'gks_cardlink_transaction.responseCode'),
	array('name' => 'somyerror', 'field' => 'gks_cardlink_transaction.myerror'),
	array('name' => 'somymessage', 'field' => 'gks_cardlink_transaction.mymessage'),
	array('name' => 'sotype', 'field' => 'gks_cardlink_transaction.transactionType'),
	array('name' => 'soterminal', 'field' => 'gks_cardlink_transaction.eftTerminalId'),
	array('name' => 'sopos_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	
	array('name' => 'soreference', 'field' => 'gks_cardlink_transaction.referenceNumber'),
	array('name' => 'socompany', 'field' => 'gks_cardlink_transaction.mid'),
	
	array('name' => 'soacquirer', 'field' => 'gks_cardlink_transaction.acquirerName'),
	array('name' => 'sobankId', 'field' => 'gks_cardlink_transaction.bankId'),
	array('name' => 'soaccount', 'field' => 'gks_cardlink_transaction.accountNumber'),
	array('name' => 'soapp', 'field' => 'gks_cardlink_transaction.applicationName'),
	array('name' => 'soexpiry', 'field' => 'gks_cardlink_transaction.cardExpiryDate'),
	array('name' => 'socardtype', 'field' => 'gks_cardlink_transaction.cardType'),
	
	array('name' => 'sosn', 'field' => 'gks_cardlink_transaction.sn'),
	array('name' => 'soacode', 'field' => 'gks_cardlink_transaction.authorizationCode'),
	array('name' => 'somsgcode', 'field' => 'gks_cardlink_transaction.msgCode'),
	array('name' => 'somsgtype', 'field' => 'gks_cardlink_transaction.msgType'),
	array('name' => 'somsgoptions', 'field' => 'gks_cardlink_transaction.msgOptions'),
	array('name' => 'sopaymentspecs', 'field' => 'gks_cardlink_transaction.paymentSpecs'),
	array('name' => 'sophone', 'field' => 'gks_cardlink_transaction.phone'),
	array('name' => 'socity', 'field' => 'gks_cardlink_transaction.city'),
	array('name' => 'soaddress', 'field' => 'gks_cardlink_transaction.address'),
	array('name' => 'sotxntime', 'field' => 'gks_cardlink_transaction.txnDateTime'),
	array('name' => 'soupid', 'field' => 'gks_cardlink_transaction.uniquePaymentId'),
	array('name' => 'soupidecr', 'field' => 'gks_cardlink_transaction.uniquePaymentIdECR'),
	array('name' => 'sobatchnumber', 'field' => 'gks_cardlink_transaction.batchNumber'),
	array('name' => 'solength', 'field' => 'gks_cardlink_transaction.length'),
	array('name' => 'sosessionId', 'field' => 'gks_cardlink_transaction.sessionId'),
	array('name' => 'sonpdi', 'field' => 'gks_cardlink_transaction.numberOfPostdatedInstallments'),
	array('name' => 'sotoken', 'field' => 'gks_cardlink_transaction.token'),
	array('name' => 'sotc', 'field' => 'gks_cardlink_transaction.tc'),
	array('name' => 'sogo4more', 'field' => 'gks_cardlink_transaction.go4moreProducts'),
	array('name' => 'sotlvdata', 'field' => 'gks_cardlink_transaction.tlvData'),
	array('name' => 'soposver', 'field' => 'gks_cardlink_transaction.posTerminalVersion'),
	array('name' => 'soaid', 'field' => 'gks_cardlink_transaction.aid'),
);
     
   


$search_fields = array(
  'gks_cardlink_transaction.authorizationCode',
  'gks_cardlink_transaction.city',
  'gks_cardlink_transaction.responseCodeMessage',
  'gks_cardlink_transaction.merchantName',
  'gks_cardlink_transaction.uniquePaymentIdECR',
  'gks_cardlink_transaction.referenceNumber',
  'gks_cardlink_transaction.sn',
  'gks_cardlink_transaction.acquirerName',
  'gks_cardlink_transaction.applicationName',
  'gks_cardlink_transaction.address',
  'gks_cardlink_transaction.eftTerminalId',
  'gks_cardlink_transaction.cardType',
  'gks_cardlink_transaction.accountNumber',
  'gks_cardlink_transaction.tc',
  'gks_cardlink_transaction.transactionType',
  'gks_cardlink_transaction.bankId',
  'gks_cardlink_transaction.uniquePaymentId',
  'gks_cardlink_transaction.myerror',
  'gks_cardlink_transaction.mymessage',

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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_cardlink_transaction.*, 
".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
gks_company.company_title
FROM (gks_cardlink_transaction
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_cardlink_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_company ON gks_cardlink_transaction.mid = gks_company.cardlink_mid
where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_cardlink_transaction DESC";
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
.gks_td08 {
  min-width:150px;  
}  
.mydivexpand {    
  max-height: 19px;
}
.mydivexpand_on {
    max-height: unset;
}
.cardlink_reload {
  cursor: pointer;  
}
.cardlink_reload:before {
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

.cardlink_status_draft {border-width: 0px;background-color: #999999;color: white;font-weight: bold;}
.cardlink_status_send {border-width: 0px;background-color: #cdb635;color: white;font-weight: bold;}
.cardlink_status_fail {border-width: 0px;background-color: red;color: white;font-weight: bold;}
.cardlink_status_done {border-width: 0px;background-color: green;color: white;font-weight: bold;}

.cardlink_result_00 {border-width: 0px;background-color: green;color: white;font-weight: bold;}
.cardlink_result_error {border-width: 0px;background-color: red;color: white;font-weight: bold;}
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
  <div style="text-align: center"><a href="admin-cardlink-refresh.php?today=1" target="_blank" style="
     background-color: #2cadbf;
      color: white;
      padding: 20px;
      margin-bottom: 10px;
      border-radius: 10px;
      font-size: 150%;
      border: 1px solid #165861;
      display: inline-block;
  "><?php echo gks_lang('Λήψη ξανά των συναλλαγών από cardlink για σήμερα');?></a></div>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">RAW</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">Re</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soammount', gks_lang('Ποσό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soamountpayable', gks_lang('Πληρωτέο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstallments', gks_lang('Δόσεις')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', 'Status'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult', 'Result'); ?></th>       
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
     
    <th class="table-dark" scope="col" style="text-align: center !important;min-width:150px;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyerror', gks_lang('Σφάλμα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;min-width:150px;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somymessage', gks_lang('Μήνυμα')); ?></th>        
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos_user', gks_lang('Χειριστής')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreference', gks_lang('Κ. Αναφοράς')); ?></th>
    
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soacquirer', 'Acquirer'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobankId', 'Bank'); ?></th>        
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaccount', gks_lang('Κάρτα')); ?></th>        
          
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soapp', gks_lang('Εφαρμογή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soexpiry', gks_lang('Λήξη')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardtype', gks_lang('Τύπος')); ?></th>  
          
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosn', 'SN'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soacode', 'ACode'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somsgcode', 'msgCode'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somsgtype', 'msgType'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somsgoptions', 'msgOptions');?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopaymentspecs', 'paymentSpecs'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socity', 'city'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaddress', 'address'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotxntime', 'txnDateTime'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soupid', 'uniquePaymentId'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soupidecr', 'uniquePaymentIdECR'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobatchnumber', 'batchNumber'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solength', 'length'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosessionId', 'sessionId'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonpdi', 'numberOfPostdatedInstallments'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotoken', 'token'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotc', 'tc'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogo4more', 'go4moreProducts'); ?></th>     
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotlvdata', 'tlvData'); ?></th>     
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soposver', 'posTerminalVersion'); ?></th>     
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaid', 'aid'); ?></th>                    
    

       
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
    <td nowrap class="mytdcml"><?php echo $row['id_cardlink_transaction'];?></td>   

    <td class="mytdcm"><a href="admin-cardlink-transaction-raw.php?mtid=<?php echo $row['id_cardlink_transaction'];?>" target="_blank">R</a></td>
    <td class="mytdcm">
        <i class="fas fa-sync cardlink_reload" data-id="<?php echo $row['id_cardlink_transaction'];?>"></i>
    </td>  
      
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>  
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['amount']) and $row['amount']!=0) echo number_format($row['amount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['amountPayable']) and $row['amountPayable']!=0) echo number_format($row['amountPayable'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['numberOfInstallments']) and $row['numberOfInstallments']!=0) echo $row['numberOfInstallments'];?></td>   
    


    <td nowrap class="mytdcm cardlink_status_<?php echo $row['trans_status'];?>"><?php echo gks_eftpos_has_transaction_status_cardlink($row['trans_status']);?></td>
    <td nowrap class="mytdcm cardlink_result_<?php 
      if ($row['responseCode']=='00') echo '00';
      else if (trim_gks($row['responseCode'])!='') echo 'error';?>
      "><?php 
      //echo $row['responseCode'];
      //echo $row['responseCodeMessage'];
      echo gks_eftpos_has_transaction_result_cardlink($row['responseCode'],$row['responseCodeMessage']);?></td>
    
    <td nowrap class="mytdcm"><?php echo gks_eftpos_transaction_type_descr($row['trans_type']);?></td>
    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      if ($row['myerror']!=='null') echo $row['myerror'];
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['mymessage'];
    ?></div></div></td>    
 

  
 
    <td nowrap class="mytdcm"><?php echo $row['eftTerminalId'];?></td>   
    <td nowrap class="mytdcml div_xeiristis" data-id="<?php echo $row['id_cardlink_transaction'];?>">
      <a href="admin-users-item.php?id=<?php echo $row['pos_user_id'];?>"><?php echo $row['pos_gks_nickname'];?></a>
    </td>
    <td nowrap class="mytdcm"><?php echo $row['referenceNumber'];?></td>   
    
    <td nowrap class="mytdcm"><?php 
      if (!empty($row['company_title'])) {
        echo $row['company_title'];
      } else {
        echo '<span title="'.$row['mid'].'">'.$row['merchantName'].'</span>';
      }
    ?></td>   
    
    <td nowrap class="mytdcm"><?php echo $row['acquirerName'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['bankId'];?></td>   


    
    
    
  
    <td nowrap class="mytdcm"><?php echo $row['accountNumber'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['applicationName'];?></td> 
    <td nowrap class="mytdcm"><?php echo $row['cardExpiryDate'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['cardType'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['sn'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['authorizationCode'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['msgCode'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['msgType'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['msgOptions'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['paymentSpecs'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['phone'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['city'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['address'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['txnDateTime'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['uniquePaymentId'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['uniquePaymentIdECR'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['batchNumber'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['length'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['sessionId'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['numberOfPostdatedInstallments'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['token'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['tc'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['go4moreProducts'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['tlvData'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['posTerminalVersion'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['aid'];?></td>   
    
    
    
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
    <th class="table-dark" scope="col" style="text-align: left !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Εταιρεία');?></th> 
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Πλήθος');?></th>   
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Ποσό');?></th>        
    <th class="table-dark" scope="col" style="text-align: right !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Πληρωτέο');?></th>        
  </tr>


</thead>
<tbody>
      
<?php
$sql = "select mid, company_title,company_sortorder,
count(*) as countid, sum(amount) as sum_amount, sum(amountPayable) as sum_amountPayable
from (
  SELECT gks_cardlink_transaction.*, 
  ".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname,
  gks_company.company_title,
  gks_company.company_sortorder
  FROM (gks_cardlink_transaction 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_cardlink_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_company ON gks_cardlink_transaction.mid = gks_company.cardlink_mid
  where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
  ".$where . $search_where;


$sql.="
) AS mydata
group by mid, company_title, company_sortorder
order by company_sortorder
";
 


$result_rep = $db_link->query($sql);        
if (!$result_rep) debug_mail(false,'error sql',$sql);
if (!$result_rep) die('sql error');


$sum_countid=0;
$sum_sum_amount=0;
$sum_sum_amountPayable=0;


$i = 0;
while ($row_rep = $result_rep->fetch_assoc()) {
  $i++;
  $sum_countid+=$row_rep['countid'];
  $sum_sum_amount+=$row_rep['sum_amount'];
  $sum_sum_amountPayable+=$row_rep['sum_amountPayable'];
  
  
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td class="mytdcml"><?php if (!empty($row_rep['company_title'])) {
        echo $row_rep['company_title'];
      } else {
        echo '<span title="'.$row_rep['mid'].'">'.$row_rep['mid'].'</span>';
      }
      ?></td> 

 
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['countid']) and $row_rep['countid']!=0) echo $row_rep['countid'];?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_amount']) and $row_rep['sum_amount']!=0) echo number_format($row_rep['sum_amount'],2,',','.');?></td>  
    <td nowrap class="mytdcmr"><?php if (isset($row_rep['sum_amountPayable']) and $row_rep['sum_amountPayable']!=0) echo number_format($row_rep['sum_amountPayable'],2,',','.');?></td>  
  </tr>
<?php

} 
?>
</tbody>
<tfoot>

  <tr class="table-warning">
    <td class="bottomsums" colspan="2" nowrap><?php echo gks_lang('Σύνολα');?></td>
    <td class="bottomsums" nowrap align="right"><?php echo $sum_countid;?></td>  
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_amount,2,',','.');?></td>  
    <td class="bottomsums" nowrap align="right"><?php echo number_format($sum_sum_amountPayable,2,',','.');?></td>  
  </tr>
</tfoot>
</table>

<?php } ?>

<!--
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;margin-top:40px;border-collapse: collapse;">
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


<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;margin-top:40px;border-collapse: collapse;">
<tbody>
  <tr>
    <th class="table-dark" scope="col" width="20%" style="text-align:center;">Result</th>
    <th class="table-dark" scope="col" width="80%">Description</th>
  </tr>
  <tr>
    <td class="mytdcm">APPROVED</td>
    <td class="mytdcml">The transaction has been completed and approved by the authorization system</td>
  </tr>
  <tr>
    <td class="mytdcm">DECLINED</td>
    <td class="mytdcml">The transaction has been completed and declined by the authorization system</td>
  </tr>
  <tr>
    <td class="mytdcm">CANCELLED</td>
    <td class="mytdcml">The transaction has been cancelled by the POS user before reaching completion</td>
  </tr>
  <tr>
    <td class="mytdcm">FAILED</td>
    <td class="mytdcml">The transaciton has failed to complete</td>
  </tr>
  <tr>
    <td class="mytdcm">UNKNOWN</td>
    <td class="mytdcml">The transaction result is unknown. Only possible if the device hasn't responded with results</td>
  </tr>
  <tr>
    <td class="mytdcm">BUSY</td>
    <td class="mytdcml">The transaction has failed because the POS is currently unavailable for transactions (either processing another transaction or under maintenance)</td>
  </tr>
  <tr>
    <td class="mytdcm">MAX_TRANSACTIONS</td>
    <td class="mytdcml">The POS device has reached its transaction limit for the specific batch. Batch closing should be performed on the device before continuing transactions</td>
  </tr>

</tbody>
</table>
-->

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


  $('.cardlink_reload').click(function() {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    //console.log(myid);
    
    $('body').addClass("myloading");
    datasend='myid=' + myid
    $.ajax({
			url: '/my/admin-cardlink-transaction-reload.php',
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
      			url: '/my/admin-cardlink-transaction-select-xeiristis-exec.php',
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
			url: '/my/admin-cardlink-transaction-select-xeiristis.php',
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


