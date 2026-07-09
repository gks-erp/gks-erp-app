<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Τραπεζικοί λογαριασμοί');
$nav_active_array=array('manage','manage_bank_accounts');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_bank_accounts','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$gks_custom_prepare = gks_custom_table_item_prepare('gks_bank_accounts',['from'=>'list']);


$filters = array();

$filters[] = array(
  'name' => 'fbank',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τράπεζα'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_bank_accounts.bank_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_bank_accounts.bank_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_bank_accounts.bank_id<>0"),
  ),
  'sql' => "SELECT gks_banks.id_bank AS id, gks_banks.bank_descr AS descr
FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
WHERE (((gks_banks.id_bank) Is Not Null))
GROUP BY gks_banks.id_bank, gks_banks.bank_descr
ORDER BY gks_banks.bank_descr;",    
);

$filters[] = array(
  'name' => 'ftype',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τύπος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_bank_accounts.account_type = '%V%'",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "(gks_bank_accounts.account_type is null or gks_bank_accounts.account_type='')"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_bank_accounts.account_type<>''"),
  ),
  'sql' => "SELECT account_type AS id, account_type AS descr
FROM gks_bank_accounts 
WHERE account_type<>''
GROUP BY account_type
ORDER BY account_type",    
);



$filters[] = array(
  'name' => 'fuser',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Επαφή'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_bank_accounts.user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_bank_accounts.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_bank_accounts.user_id<>0"),
  ),
  'sql' => "SELECT gks_bank_accounts.user_id AS id, gks_nickname AS descr
FROM gks_bank_accounts 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_bank_accounts.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
GROUP BY gks_bank_accounts.user_id, gks_nickname
ORDER BY gks_nickname",    
);



$filters[] = array(
    'name' => 'fse',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Προβολή στο eshop'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_bank_accounts.show_eshop = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),   'sql' => "gks_bank_accounts.show_eshop=1"),
        array('value' => 101, 'text' => gks_lang('Όχι'),   'sql' => "gks_bank_accounts.show_eshop<>1"),
    ),
);
$filters[] = array(
    'name' => 'fdfu',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Διαγραφή από χρήστη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_bank_accounts.deleted_from_user = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),   'sql' => "gks_bank_accounts.deleted_from_user=1"),
        array('value' => 101, 'text' => gks_lang('Όχι'),   'sql' => "gks_bank_accounts.deleted_from_user<>1"),
    ),
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_bank_accounts.bank_account_disable = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ενεργός'),      'sql' => "gks_bank_accounts.bank_account_disable=0"),
        array('value' => 101, 'text' => gks_lang('Μη ενεργός'),   'sql' => "gks_bank_accounts.bank_account_disable<>0"),
    ),
);


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
	array('name' => 'soid', 'field' => 'gks_bank_accounts.id_bank_account'),
	array('name' => 'sodescr', 'field' => 'gks_bank_accounts.account_descr'),
	array('name' => 'soiban', 'field' => 'gks_bank_accounts.IBAN'),
	array('name' => 'sonumber', 'field' => 'gks_bank_accounts.account_number'),
	array('name' => 'sobank', 'field' => 'gks_banks.bank_descr'),
	array('name' => 'sotype', 'field' => 'gks_bank_accounts.account_type'),
	array('name' => 'sodikaio', 'field' => 'gks_bank_accounts.account_dikaiouxos'),
	array('name' => 'souser', 'field' => 'gks_nickname'),
	array('name' => 'sose', 'field' => 'gks_bank_accounts.show_eshop'),
	array('name' => 'sodfu', 'field' => 'gks_bank_accounts.deleted_from_user'),
	array('name' => 'sodisable', 'field' => 'gks_bank_accounts.bank_account_disable'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

            
$search_fields = array(
'gks_bank_accounts.account_descr',
'gks_bank_accounts.IBAN',
'gks_bank_accounts.account_number',
'gks_banks.bank_descr',
'gks_bank_accounts.account_type',
'gks_bank_accounts.account_dikaiouxos',
'gks_nickname',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_bank_accounts.*,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_banks.bank_descr
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']."  (gks_bank_accounts 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_bank_accounts.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank

where 1=1 ".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_bank_account desc";
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
<style class="gks_customtableview_style" data-index="2">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,2);?>
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-bank_accounts-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου Τραπεζικού λογαριασμού');?></a>
     <a class="btn btn-primary" href="admin-bank-accounts-export-excel.php"><?php echo gks_lang('Εξαγωγή σε Excel');?></a>

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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soiban', gks_lang('ΙΒΑΝ')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber', gks_lang('Αριθμός')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobank', gks_lang('Τράπεζα')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th> 
     
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodikaio', gks_lang('Δικαιούχος')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Επαφή')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sose', '<span class="tooltipster" title="'.gks_lang('Προβολή στο eshop').'">'.gks_lang('Πe').'</span>'); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodfu', '<span class="tooltipster" title="'.gks_lang('Διαγραφή από χρήστη').'">'.gks_lang('ΔαΧ').'</span>'); ?></th> 
    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργός')); ?></th> 
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>
  </tr>
</thead>
<tbody> 
<?php 
$i=0;
while ($line = $result->fetch_assoc()) {

	$i++;
?>

  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-bank_accounts-item.php?id=<?php echo $line['id_bank_account'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $line['id_bank_account'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $line['id_bank_account'];?>" data-model="gks_bank_accounts"></i></td>
        </tr>      
      </table>
    </td>
    <td nowrap class="mytdcml"><?php echo $line["account_descr"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["IBAN"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["account_number"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["bank_descr"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["account_type"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["account_dikaiouxos"];?></td>
    <td nowrap class="mytdcml"><?php echo $line["gks_nickname"];?></td>
    <td nowrap class="mytdcm"><?php echo myimg010($line['show_eshop']);?></td>
    <td nowrap class="mytdcm"><?php echo myimg010($line['deleted_from_user']);?></td>

    <td class="mytdcm"><?php echo myimg010r($line['bank_account_disable']);?></td>
<?php
  echo gks_custom_table_list_rows($gks_custom_prepare,$line);
?>     
  </tr>
  
<?php 

} ?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
});

 
 
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>
  
<?php

include_once('_my_footer_admin.php');

