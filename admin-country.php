<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr


admin-mass-messages.php header me einonidia-> keno sto custom
;otan mpainei neo paidio apo custom, den ;exei pl;atos


*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Χώρες');
$nav_active_array=array('manage','manage_country');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_country','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_country.id_country'),
  						array('name' => 'socountry_name', 'field' => 'gks_country.country_name'),
  						array('name' => 'socountry_initials', 'field' => 'gks_country.country_initials'),
  						array('name' => 'socountry_initials3', 'field' => 'gks_country.country_initials3'),
  						array('name' => 'socountry_ISO_3166_1', 'field' => 'gks_country.country_ISO_3166_1'),
  						array('name' => 'socountry_lang', 'field' => 'gks_country.country_lang'),
  						array('name' => 'sophone_code', 'field' => 'gks_country.phone_code'),
  						array('name' => 'soccn', 'field' => 'ccn'),
            );

$search_fields = array(
'country_name',
'country_initials',
'country_initials3',
'country_lang',
'phone_code',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_country.*, tablen.ccn
FROM gks_country LEFT JOIN (
  SELECT gks_nomoi.country_id, Count(gks_nomoi.id_nomos) AS ccn
  FROM gks_nomoi
  GROUP BY gks_nomoi.country_id
)  AS tablen ON gks_country.id_country = tablen.country_id

where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_country.country_name";
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

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-country-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας χώρας');?></a>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry_name', gks_lang('Χώρα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry_initials', gks_lang('Αρχικά (2χαρ)')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry_initials3', gks_lang('Αρχικά (3χαρ)')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry_ISO_3166_1', gks_lang('ISO 3166-1')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry_lang', gks_lang('Γλώσσα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone_code', '<span title="'.gks_lang('Κωδικός τηλεφώνων').'" class="tooltipster">'.gks_lang('Κωδ Τηλ').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccn', gks_lang('Πλήθος Νομών')); ?></th>        
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-country-item.php?id=<?php echo $row['id_country'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_country'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_country'];?>" data-model="gks_country"></i></td>
        </tr>      
      </table>
    </td>
        
    <td class="mytdcml"><?php echo $row['country_name'];?></td>
    <td class="mytdcm"><?php echo $row['country_initials'];?></td>
    <td class="mytdcm"><?php echo $row['country_initials3'];?></td>
    <td class="mytdcm"><?php echo $row['country_ISO_3166_1'];?></td>
    <td class="mytdcm"><?php echo $row['country_lang'];?></td>
    <td class="mytdcm"><?php echo $row['phone_code'];?></td>
    <td nowrap align="right"><?php if (isset($row["ccn"]) and $row["ccn"]>0) echo myNumberFormat($row["ccn"], 0);?></td>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_country','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_country','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_country','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php

include_once('_my_footer_admin.php');


