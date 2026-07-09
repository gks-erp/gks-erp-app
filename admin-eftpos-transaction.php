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

$my_page_title=gks_lang('Συναλλαγές EFT/POS');
$nav_active_array=array('accounting','accounting_eftpos','accounting_eftpos_eftpos');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eftpos_transaction','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

if (1==2) {
  $sql="UPDATE (gks_eftpos_transaction 
  LEFT JOIN gks_viva_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_viva_transaction.id_viva_transaction) 
  LEFT JOIN gks_company ON gks_viva_transaction.MerchantId = gks_company.viva_merchant_id 
  SET gks_eftpos_transaction.company_id = gks_company.id_company
  WHERE gks_eftpos_transaction.company_id=0
  AND gks_eftpos_transaction.payment_acquirer_with_id=1
  AND gks_eftpos_transaction.xxx_transaction_id>0
  AND gks_viva_transaction.MerchantId<>''
  AND gks_company.id_company Is Not Null";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql="UPDATE gks_eftpos_transaction 
  LEFT JOIN gks_viva_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_viva_transaction.id_viva_transaction 
  SET gks_eftpos_transaction.xeiristis_id = gks_viva_transaction.xeiristis_id
  WHERE gks_eftpos_transaction.xeiristis_id=0 
  AND gks_viva_transaction.xeiristis_id>0 
  AND gks_eftpos_transaction.payment_acquirer_with_id=1
  AND gks_eftpos_transaction.xxx_transaction_id>0";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql="UPDATE (gks_eftpos_transaction 
  LEFT JOIN gks_mellon_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_mellon_transaction.id_mellon_transaction) 
  LEFT JOIN gks_company ON gks_mellon_transaction.MID = gks_company.mellon_mid 
  SET gks_eftpos_transaction.company_id = gks_company.id_company
  WHERE gks_eftpos_transaction.company_id=0
  AND gks_eftpos_transaction.payment_acquirer_with_id=3
  AND gks_eftpos_transaction.xxx_transaction_id>0
  AND gks_mellon_transaction.mid<>''
  AND gks_company.id_company Is Not Null";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql="UPDATE gks_eftpos_transaction 
  LEFT JOIN gks_mellon_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_mellon_transaction.id_mellon_transaction 
  SET gks_eftpos_transaction.xeiristis_id = gks_mellon_transaction.xeiristis_id
  WHERE gks_eftpos_transaction.xeiristis_id=0 
  AND gks_mellon_transaction.xeiristis_id>0 
  AND gks_eftpos_transaction.payment_acquirer_with_id=3
  AND gks_eftpos_transaction.xxx_transaction_id>0";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  
  $sql="UPDATE (gks_eftpos_transaction 
  LEFT JOIN gks_cardlink_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_cardlink_transaction.id_cardlink_transaction) 
  LEFT JOIN gks_company ON gks_cardlink_transaction.mid = gks_company.cardlink_mid 
  SET gks_eftpos_transaction.company_id = gks_company.id_company
  WHERE gks_eftpos_transaction.company_id=0
  AND gks_eftpos_transaction.payment_acquirer_with_id=4
  AND gks_eftpos_transaction.xxx_transaction_id>0
  AND gks_cardlink_transaction.mid<>''
  AND gks_company.id_company Is Not Null";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql="UPDATE gks_eftpos_transaction 
  LEFT JOIN gks_cardlink_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_cardlink_transaction.id_cardlink_transaction 
  SET gks_eftpos_transaction.xeiristis_id = gks_cardlink_transaction.xeiristis_id
  WHERE gks_eftpos_transaction.xeiristis_id=0 
  AND gks_cardlink_transaction.xeiristis_id>0 
  AND gks_eftpos_transaction.payment_acquirer_with_id=4
  AND gks_eftpos_transaction.xxx_transaction_id>0";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  }


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
	'field' => 'gks_eftpos_transaction.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_eftpos_transaction.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
  'name' => 'fterminal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τερματικό'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_eftpos_transaction.terminalId='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_eftpos_transaction.transaction_status='')"),
  ),
  'sql' => "SELECT terminalId as id, terminalId as descr
  FROM gks_eftpos_transaction
  WHERE terminalId<>''
  GROUP BY terminalId
  order by terminalId"
);


$filters[] = array(
  'name' => 'fasset',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πάγιο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_eftpos_transaction.asset_id=%V%",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_eftpos_transaction.transaction_status='')"),
  ),
  'sql' => "SELECT gks_assets.id_asset as id, gks_assets.asset_title as descr
  FROM gks_eftpos_transaction LEFT JOIN gks_assets ON gks_eftpos_transaction.asset_id = gks_assets.id_asset
  WHERE (((gks_assets.id_asset) Is Not Null))
  GROUP BY gks_assets.id_asset, gks_assets.asset_title
  ORDER BY gks_assets.asset_title;"
); 

$filters[] = array(
    'name' => 'fxeiristis',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χειριστής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.xeiristis_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Χειριστή'),          'sql' => "(gks_eftpos_transaction.xeiristis_id=0 or gks_eftpos_transaction.xeiristis_id is null)"),
    ),
    'sql' => "SELECT gks_eftpos_transaction.xeiristis_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_eftpos_transaction 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_eftpos_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_eftpos_transaction.xeiristis_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
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
    'field'  => "gks_eftpos_transaction.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_eftpos_transaction.transaction_status='')"),
    ),
    'sql' => "
    SELECT gks_eftpos_transaction.company_id as id, gks_company.company_title as descr
    FROM gks_eftpos_transaction 
    LEFT JOIN gks_company ON gks_eftpos_transaction.company_id = gks_company.id_company
    WHERE gks_eftpos_transaction.company_id>0
    and (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)
    GROUP BY gks_eftpos_transaction.company_id, gks_company.id_company, gks_company.company_title
    order by company_sortorder"
    
  );  
}

$filters[] = array(
    'name' => 'fistra',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.transaction_type = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      array('value' =>10, 'text' => gks_eftpos_transaction_type_descr('control'), 'sql' => "gks_eftpos_transaction.transaction_type='control'"),
      array('value' =>11, 'text' => gks_eftpos_transaction_type_descr('echo'), 'sql' => "gks_eftpos_transaction.transaction_type='echo'"),
      array('value' =>12, 'text' => gks_eftpos_transaction_type_descr('fullvoid'), 'sql' => "gks_eftpos_transaction.transaction_type='fullvoid'"),
      array('value' =>13, 'text' => gks_eftpos_transaction_type_descr('fullvoidecrtoken'), 'sql' => "gks_eftpos_transaction.transaction_type='fullvoidecrtoken'"),
      array('value' =>14, 'text' => gks_eftpos_transaction_type_descr('fullvoiderp'), 'sql' => "gks_eftpos_transaction.transaction_type='fullvoiderp'"),
      array('value' =>15, 'text' => gks_eftpos_transaction_type_descr('merchantinfo'), 'sql' => "gks_eftpos_transaction.transaction_type='merchantinfo'"),
      array('value' =>16, 'text' => gks_eftpos_transaction_type_descr('preauthcompletion'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthcompletion'"),
      array('value' =>17, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionecrtoken'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthcompletionecrtoken'"),
      array('value' =>18, 'text' => gks_eftpos_transaction_type_descr('preauthcompletionerp'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthcompletionerp'"),
      array('value' =>19, 'text' => gks_eftpos_transaction_type_descr('preauthnormal'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthnormal'"),
      array('value' =>20, 'text' => gks_eftpos_transaction_type_descr('preauthonetap'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthonetap'"),
      array('value' =>21, 'text' => gks_eftpos_transaction_type_descr('preauthonetapcompletion'), 'sql' => "gks_eftpos_transaction.transaction_type='preauthonetapcompletion'"),
      array('value' =>22, 'text' => gks_eftpos_transaction_type_descr('reconciliation'), 'sql' => "gks_eftpos_transaction.transaction_type='reconciliation'"),
      array('value' =>23, 'text' => gks_eftpos_transaction_type_descr('refund'), 'sql' => "gks_eftpos_transaction.transaction_type='refund'"),
      array('value' =>24, 'text' => gks_eftpos_transaction_type_descr('refundecrtoken'), 'sql' => "gks_eftpos_transaction.transaction_type='refundecrtoken'"),
      array('value' =>25, 'text' => gks_eftpos_transaction_type_descr('refundecrtokenfree'), 'sql' => "gks_eftpos_transaction.transaction_type='refundecrtokenfree'"),
      array('value' =>26, 'text' => gks_eftpos_transaction_type_descr('refunderp'), 'sql' => "gks_eftpos_transaction.transaction_type='refunderp'"),
      array('value' =>27, 'text' => gks_eftpos_transaction_type_descr('refunderpfree'), 'sql' => "gks_eftpos_transaction.transaction_type='refunderpfree'"),
      array('value' =>28, 'text' => gks_eftpos_transaction_type_descr('refundfree'), 'sql' => "gks_eftpos_transaction.transaction_type='refundfree'"),
      array('value' =>29, 'text' => gks_eftpos_transaction_type_descr('regreceiptecrtoken'), 'sql' => "gks_eftpos_transaction.transaction_type='regreceiptecrtoken'"),
      array('value' =>30, 'text' => gks_eftpos_transaction_type_descr('regreceipterp'), 'sql' => "gks_eftpos_transaction.transaction_type='regreceipterp'"),
      array('value' =>31, 'text' => gks_eftpos_transaction_type_descr('resendallfinish'), 'sql' => "gks_eftpos_transaction.transaction_type='resendallfinish'"),
      array('value' =>32, 'text' => gks_eftpos_transaction_type_descr('resendallnext'), 'sql' => "gks_eftpos_transaction.transaction_type='resendallnext'"),
      array('value' =>33, 'text' => gks_eftpos_transaction_type_descr('resendallstart'), 'sql' => "gks_eftpos_transaction.transaction_type='resendallstart'"),
      array('value' =>34, 'text' => gks_eftpos_transaction_type_descr('sale'), 'sql' => "gks_eftpos_transaction.transaction_type='sale'"),
      array('value' =>35, 'text' => gks_eftpos_transaction_type_descr('saleecrtoken'), 'sql' => "gks_eftpos_transaction.transaction_type='saleecrtoken'"),
      array('value' =>36, 'text' => gks_eftpos_transaction_type_descr('saleerp'), 'sql' => "gks_eftpos_transaction.transaction_type='saleerp'"),
      array('value' =>37, 'text' => gks_eftpos_transaction_type_descr('transactiondetails'), 'sql' => "gks_eftpos_transaction.transaction_type='transactiondetails'"),
        
    ),
);


$filters[] = array(
    'name' => 'fpaid',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τρόπος Πληρωμής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.payment_acquirer_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Τρόπο πληρωμής'),          'sql' => "(gks_eftpos_transaction.payment_acquirer_id=0 or gks_eftpos_transaction.payment_acquirer_id is null)"),
    ),
    'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name as descr
    FROM gks_eftpos_transaction 
    LEFT JOIN gks_payment_acquirers ON gks_eftpos_transaction.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer
    WHERE (((gks_payment_acquirers.id_payment_acquirer) Is Not Null))
    GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
    ORDER BY gks_payment_acquirers.mysortorder;",
);



$filters[] = array(
    'name' => 'fCardType',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('via Τρόπος Πληρωμής'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.payment_acquirer_via = '%V%'",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς via'),          'sql' => "(gks_eftpos_transaction.payment_acquirer_via='' or gks_eftpos_transaction.payment_acquirer_via is null)"),
    ),
    'sql' => "SELECT payment_acquirer_via AS id, payment_acquirer_via as descr
    FROM gks_eftpos_transaction 
    WHERE payment_acquirer_via <>''
    GROUP BY payment_acquirer_via
    order by payment_acquirer_via",
);

$filters[] = array(
    'name' => 'fpp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πάροχος Πληρωμών'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.payment_acquirer_with_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Πάροχο Πληρωμών'),          'sql' => "(gks_eftpos_transaction.payment_acquirer_with_id=0 or gks_eftpos_transaction.payment_acquirer_with_id is null)"),
    ),
    'sql' => "SELECT gks_payment_acquirer_with.id_payment_acquirer_with as id, gks_payment_acquirer_with.payment_paroxos_name as descr
    FROM gks_eftpos_transaction 
    LEFT JOIN gks_payment_acquirer_with ON gks_eftpos_transaction.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with
    WHERE (((gks_payment_acquirer_with.id_payment_acquirer_with) Is Not Null))
    GROUP BY gks_payment_acquirer_with.id_payment_acquirer_with, gks_payment_acquirer_with.payment_paroxos_name, gks_payment_acquirer_with.payment_paroxos_sortorder
    ORDER BY gks_payment_acquirer_with.payment_paroxos_sortorder;",
);

$filters[] = array(
    'name' => 'fps',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πάροχος Υπογραφών'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.aade_paroxos_id = %V%",
    'vals' => array(
        array('value' => -10, 'text' => gks_lang('Χωρίς Πάροχο Υπογραφών'),          'sql' => "(gks_eftpos_transaction.aade_paroxos_id=0 or gks_eftpos_transaction.aade_paroxos_id is null)"),
    ),
    'sql' => "SELECT gks_aade_paroxos.id_aade_paroxos as id, gks_aade_paroxos.paroxos_name as descr
    FROM gks_eftpos_transaction 
    LEFT JOIN gks_aade_paroxos ON gks_eftpos_transaction.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
    WHERE (((gks_aade_paroxos.id_aade_paroxos) Is Not Null))
    GROUP BY gks_aade_paroxos.id_aade_paroxos, gks_aade_paroxos.paroxos_name, gks_aade_paroxos.paroxos_sortorder
    ORDER BY gks_aade_paroxos.paroxos_sortorder;",
);

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eftpos_transaction.transaction_status = '%V%'",
    'vals' => array(
        array('value' => 1, 'text' => gks_eftpos_transaction_status_descr('draft'),      'sql' => "gks_eftpos_transaction.transaction_status='draft'"),
        array('value' => 2, 'text' => gks_eftpos_transaction_status_descr('async'),      'sql' => "gks_eftpos_transaction.transaction_status='async'"),
        array('value' => 3, 'text' => gks_eftpos_transaction_status_descr('processed'),  'sql' => "gks_eftpos_transaction.transaction_status='processed'"),
        array('value' => 4, 'text' => gks_eftpos_transaction_status_descr('canceled'),   'sql' => "gks_eftpos_transaction.transaction_status='canceled'"),
        array('value' => 5, 'text' => gks_eftpos_transaction_status_descr('abort'),      'sql' => "gks_eftpos_transaction.transaction_status='abort'"),
        array('value' => 6, 'text' => gks_eftpos_transaction_status_descr('done'),       'sql' => "gks_eftpos_transaction.transaction_status='done'"),
        array('value' => 7, 'text' => gks_eftpos_transaction_status_descr('agnosto'),    'sql' => "gks_eftpos_transaction.transaction_status='agnosto'"),
    ),
);

$filters[] = array(
  'name' => 'fcashreg',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ταμίας'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_eftpos_transaction.cashRegisterId='%V%'",
  'vals' => array(
      //array('value' => -1, 'text' => gks_lang('Όλες'),          'sql' => "(gks_eftpos_transaction.transaction_status='')"),
  ),
  'sql' => "SELECT cashRegisterId as id, cashRegisterId as descr
  FROM gks_eftpos_transaction
  WHERE cashRegisterId<>''
  GROUP BY cashRegisterId
  order by cashRegisterId"
);








	

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_eftpos_transaction.id_eftpos_transaction'),
  						array('name' => 'somydate', 'field' => 'gks_eftpos_transaction.mydate_add'),
  						array('name' => 'soammount', 'field' => 'gks_eftpos_transaction.amount'),
  						array('name' => 'sotip', 'field' => 'gks_eftpos_transaction.TipAmount'),
  						array('name' => 'soinstallments', 'field' => 'gks_eftpos_transaction.installments'),
  						array('name' => 'sorefund_val', 'field' => 'gks_eftpos_transaction.refund_val'),
  						
  						
  						array('name' => 'soterminal', 'field' => 'gks_eftpos_transaction.terminalId'),
  						array('name' => 'soasset', 'field' => 'gks_assets.asset_title'),
  						array('name' => 'sopos_user', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'sotype', 'field' => 'gks_eftpos_transaction.transaction_type'),
  						array('name' => 'sopan', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  						array('name' => 'sovia', 'field' => 'gks_eftpos_transaction.payment_acquirer_via'),
  						
  						array('name' => 'soppn', 'field' => 'gks_payment_acquirer_with.payment_paroxos_name'),
  						array('name' => 'sopn', 'field' => 'gks_aade_paroxos.paroxos_name'),
  						array('name' => 'sostatus', 'field' => 'gks_eftpos_transaction.transaction_status'),
  						array('name' => 'sotraid', 'field' => 'gks_eftpos_transaction.transactionId'),
  						array('name' => 'soaadeid', 'field' => 'gks_eftpos_transaction.aadeTransactionId'),
  						array('name' => 'somymsg', 'field' => 'gks_eftpos_transaction.mymessage'),
  						array('name' => 'socashreg', 'field' => 'gks_eftpos_transaction.cashRegisterId'),
  						array('name' => 'souqtxid', 'field' => 'gks_eftpos_transaction.my_uniqueTxnId'),
  						array('name' => 'sosesid', 'field' => 'gks_eftpos_transaction.sessionId'),
  						array('name' => 'somerref', 'field' => 'gks_eftpos_transaction.merchantReference'),
  						array('name' => 'socusref', 'field' => 'gks_eftpos_transaction.customerTrns'),
  						array('name' => 'sosign', 'field' => 'gks_eftpos_transaction.aadeProviderId,gks_eftpos_transaction.aadeProviderSignatureData,gks_eftpos_transaction.aadeProviderSignature'),
  						array('name' => 'soremid', 'field' => 'gks_eftpos_transaction.remote_id'),

            );

$search_fields = array(
  'gks_eftpos_transaction.TerminalId',
  'gks_assets.asset_title',
  GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  'gks_company.company_title',
  'gks_eftpos_transaction.transaction_type',
  'payment_acquirer_name',
  'payment_paroxos_name',
  'paroxos_name',
  'gks_eftpos_transaction.transaction_status',
  'gks_eftpos_transaction.transactionId',
  'gks_eftpos_transaction.mymessage',
  'gks_eftpos_transaction.cashRegisterId',
  'gks_eftpos_transaction.my_uniqueTxnId',
  'gks_eftpos_transaction.sessionId',
  'gks_eftpos_transaction.merchantReference',
  'gks_eftpos_transaction.customerTrns',
  'gks_eftpos_transaction.aadeTransactionId',

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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_eftpos_transaction.*, 
".GKS_WP_TABLE_PREFIX."users.ID AS pos_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS pos_gks_nickname, 
gks_company.company_title, gks_company.company_sortorder,
gks_payment_acquirer_with.payment_paroxos_name, 
gks_payment_acquirers.payment_acquirer_name, 
gks_aade_paroxos.paroxos_name, 
gks_assets.asset_title
FROM (((((gks_eftpos_transaction 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_eftpos_transaction.xeiristis_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company ON gks_eftpos_transaction.company_id = gks_company.id_company) 
LEFT JOIN gks_payment_acquirer_with ON gks_eftpos_transaction.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with) 
LEFT JOIN gks_payment_acquirers ON gks_eftpos_transaction.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_aade_paroxos ON gks_eftpos_transaction.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos) 
LEFT JOIN gks_assets ON gks_eftpos_transaction.asset_id = gks_assets.id_asset
where (gks_company.id_company in (".implode(',',$only_id_company).") or gks_company.id_company is null)

".$where . $search_where;

$base_sql=str_replace('SQL_CALC_FOUND_ROWS','', $sql);

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_eftpos_transaction.id_eftpos_transaction DESC";
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
<link href="css/admin-eftpos-transaction-dialog.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap"><?php echo gks_lang('RAW');?></th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap"><?php echo gks_lang('Ενέργειες');?></th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soammount', gks_lang('Ποσό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotip', '<span class="tooltipster" title="'.gks_lang('Φιλοδώρημα').'">'.gks_lang('Φιλο').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinstallments', gks_lang('Δόσεις')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorefund_val', '<span class="tooltipster" title="'.gks_lang('Ποσό επιστροφής').'">'.gks_lang('Επισ').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soterminal', gks_lang('Τερματικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset', gks_lang('Πάγιο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos_user', gks_lang('Χειριστής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopan', gks_lang('Τρόπος πληρωμής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovia', '<span class="tooltipster" title="'.gks_lang('via Τρόπος Πληρωμής').'">'.gks_lang('via').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soppn', gks_lang('Πάροχος πληρωμών')); ?></th>
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopn', gks_lang('Πάροχος υπογραφών')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotraid', gks_lang('ID πληρωμής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaadeid', '<span class="tooltipster" title="'.gks_lang('aade TransactionId').'">'.gks_lang('ΑΑΔΕ').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somymsg', gks_lang('Μήνυμα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socashreg', gks_lang('Ταμίας')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souqtxid', 'Unique Txn Id'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosesid', 'session Id'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerref', 'Merchant Reference'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socusref', 'Customer Reference'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosign', gks_lang('Υπογραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soremid', 'Rec ID'); ?></th>        
     
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap">IP</th>


    
  </tr>
</thead>
<tbody>

    <?php
    
    $row_list=[];$transaction_id_ids=[];
    while ($row = $result->fetch_assoc()) {
      $transaction_id_ids[]=$row['id_eftpos_transaction'];
      
      $row_list[]=$row;
    }
    
    $transaction_id_ids_array=[];
    if (count($transaction_id_ids)>0) {
      $sql_thisisfor="SELECT id_eftpos_transaction_thisisfor, my_this, my_is, my_for, transaction_status
      FROM gks_eftpos_transaction_thisisfor 
      LEFT JOIN gks_eftpos_transaction ON gks_eftpos_transaction_thisisfor.my_this = gks_eftpos_transaction.id_eftpos_transaction
      WHERE gks_eftpos_transaction.transaction_status='done'
      and (my_this in (".implode(',',$transaction_id_ids).")
          OR my_for in (".implode(',',$transaction_id_ids)."))";
      $result_thisisfor = $db_link->query($sql_thisisfor);        
      if (!$result_thisisfor) {debug_mail(false,'error sql',$sql_thisisfor);die('sql error2');}
      while ($row_thisisfor = $result_thisisfor->fetch_assoc()) {
        $row_thisisfor['my_this']=intval($row_thisisfor['my_this']);
        $row_thisisfor['my_is']=trim($row_thisisfor['my_is']);
        $row_thisisfor['my_for']=intval($row_thisisfor['my_for']);
        
        $transaction_id_ids_array[]=$row_thisisfor;
      }
      //echo '<pre>';print_r($transaction_id_ids_array);die();
    }
    
    
    $i = 0;
    foreach ($row_list as $row) {


	$i++;
	
      $multi_item_extra_class='';
      foreach ($transaction_id_ids_array as $traidida) {
        if ($row['id_eftpos_transaction']==$traidida['my_this']) {
          $multi_item_extra_class.='div_payment_type_multi_item_is_'.$traidida['my_is'].' ';
        }
        if ($row['id_eftpos_transaction']==$traidida['my_for']) {
          $multi_item_extra_class.='div_payment_type_multi_item_has_'.$traidida['my_is'].' ';
        }
      }
	
?>
  <tr class="eftpos_tr <?php echo $multi_item_extra_class;?>" data-id="<?php echo $row['id_eftpos_transaction'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm"><?php echo $row['id_eftpos_transaction'];?></td>   
    <td class="mytdcm"><?php
      switch ($row['payment_acquirer_with_id']) {
        case 1: //viva
          echo '<a href="admin-viva-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 2: //megeftpos
          echo '<a href="admin-megeftpos-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 3: //mellon
          echo '<a href="admin-mellon-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 4: //cardlink
          echo '<a href="admin-cardlink-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 5: //epay
          echo '<a href="admin-epay-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 6: //worldline
          echo '<a href="admin-worldline-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
        case 7: //nexi
          echo '<a href="admin-nexi-transaction-raw.php?id='.$row['id_eftpos_transaction'].'" target="_blank">R</a>';
          break;
      }
    ?></td>
      
    <td nowrap class="mytdcm p-0">
      <button type="button" class="btn btn-sm btn-primary gks_stoppropagation gks_eftpos_transaction_actions" 
        data-id=<?php echo $row['id_eftpos_transaction'];?>
        data-poso="<?php echo $row['amount']?>"
        data-asset_id="<?php echo $row['asset_id']?>"
        data-asset_title="<?php echo base64_encode($row['asset_title']);?>"
        ><?php echo gks_lang('Ενέργειες');?></button>
    </td>
    
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>  
     
    <td nowrap class="mytdcm"><b><?php if (isset($row['amount']) and $row['amount']!=0) echo number_format($row['amount'], 2, ',', '.') ;?></b></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['tipAmount']) and $row['tipAmount']!=0) echo number_format($row['tipAmount'], 2, ',', '.');?></td>   
    <td nowrap class="mytdcm"><?php if ($row['installments']!=0) echo $row['installments'];?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['refund_val']) and $row['refund_val']!=0) echo number_format($row['refund_val'], 2, ',', '.');?></td>   

    
    <td nowrap class="mytdcm"><?php echo $row['terminalId'];?></td>   
    <td nowrap class="mytdcm"><a href="admin-assets-item.php?id=<?php echo $row['asset_id']?>"><?php echo $row['asset_title'];?></a></td>   
    <td nowrap class="mytdcml div_xeiristis" data-id="<?php echo $row['id_eftpos_transaction'];?>">
      <a href="admin-users-item.php?id=<?php echo $row['pos_user_id'];?>"><?php echo $row['pos_gks_nickname'];?></a>
    </td>
    
    <td nowrap class="mytdcm"><?php 
      if (!empty($row['company_title'])) {
        echo $row['company_title'];
      } else {
        if ($row['company_id']!=0) echo $row['company_id'];
      }
    ?></td>   
     
    <td nowrap class="mytdcm"><?php echo gks_eftpos_transaction_type_descr($row['transaction_type']);?></td>   
    <td nowrap class="mytdcm"><?php echo $row['payment_acquirer_name'];?></td> 
      
    <td nowrap class="mytdcm"><?php echo $row['payment_acquirer_via'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['payment_paroxos_name'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['paroxos_name'];?></td>   
    <td nowrap class="mytdcm eftpos_status_<?php echo $row['transaction_status'];?>"><?php echo gks_eftpos_transaction_status_descr($row['transaction_status']);?></td>   
    <td nowrap class="mytdcm"><?php echo $row['transactionId'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['aadeTransactionId'];?></td>   

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['mymessage']);
    ?></div></div></td>
         
    <td nowrap class="mytdcml"><?php echo $row['cashRegisterId'];?></td>   
    
    
    <td nowrap class="mytdcml"><?php echo $row['my_uniqueTxnId'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['sessionId'];?></td>   
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['merchantReference']);
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo trim_gks($row['customerTrns']);
    ?></div></div></td>

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp=[];
      if (!empty($row['aadeProviderId'])) $temp[]='ProviderId: '.$row['aadeProviderId'];
      if (!empty($row['aadeProviderSignatureData'])) $temp[]='SignatureData: '.$row['aadeProviderSignatureData'];
      if (!empty($row['aadeProviderSignature'])) $temp[]='Signature: '.$row['aadeProviderSignature'];
      echo implode('<br>',$temp);
      
    ?></div></div></td>
    <td nowrap class="mytdcm"><?php if (!empty($row['remote_id'])) echo $row['remote_id'];?></td>   
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
$sql = "select company_id, company_title,company_sortorder,
count(*) as countid, sum(amount) as sum_Amount, sum(tipAmount) as sum_TipAmount
from (
  ".$base_sql."
) AS mydata
group by company_id, company_title, company_sortorder
order by company_sortorder
";
 
//echo '<pre>'.$sql;die();

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
 
  
  
?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td class="mytdcml"><?php if (!empty($row_rep['company_title'])) {
        echo $row_rep['company_title'];
      } else {
        echo $row_rep['company_id'];
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



<div id="dialog_select_xeiristis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div style="text-align:center;font-weight:bold;font-size:120%;"><?php echo gks_lang('Επιλογή χειριστή');?></div>
  <div id="dialog_select_xeiristis_html">
    
  </div>
</div>



<?php include_once('admin-eftpos-transaction-dialog.php');

$sql_ppm="SELECT id_company,viva_preferred_payment_methods, mellon_preferred_payment_methods, 
cardlink_preferred_payment_methods, epay_preferred_payment_methods, 
worldline_preferred_payment_methods, nexi_preferred_payment_methods
FROM gks_company 
order by id_company";
$result_ppm = $db_link->query($sql_ppm);        
if (!$result_ppm) {debug_mail(false,'error sql',$sql_ppm);die('sql error');}
$all_comanys_preferred_payment_methods=array(); 
while ($row_ppm = $result_ppm->fetch_assoc()) {
  $item=[];
  $item['id_company']=intval($row_ppm['id_company']);
  $temp=trim_gks($row_ppm['viva_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['viva']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['mellon_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['mellon']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['cardlink_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['cardlink']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['epay_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['epay']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['worldline_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['worldline']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['nexi_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $item['nexi']=json_decode($temp,true);
  
  $all_comanys_preferred_payment_methods[]=$item;
  
  
}
//print '<pre>';print_r($all_comanys_preferred_payment_methods);die();

?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var ftp_pos_running=false;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

var from_php_all_comanys_preferred_payment_methods = JSON.parse('<?php echo json_encode($all_comanys_preferred_payment_methods);?>');

  
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
      			url: '/my/admin-eftpos-transaction-select-xeiristis-exec.php',
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
			url: '/my/admin-eftpos-transaction-select-xeiristis.php',
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

<script src="js/admin-eftpos-transaction-dialog.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


