<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Καταγραφές Τηλεφώνων');
$nav_active_array=array('crm','manage_phones','manage_phoneslog');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_voip_calls','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_voip_params=gks_voip_user_params();


$myview=0;
if (isset($_GET['myview'])) $myview=intval($_GET['myview']);


$filters = array();

$filters[] = array(
    'name' => 'myview',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Προβολή'),
    'field' => 'gks_voip_calls.gks_primary_rec',
    'has_custom_default' => 1,
    'multiselect' => false,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Απλή'),        'sql' => "gks_voip_calls.gks_primary_rec=1"),
        array('value' => 2, 'text' => gks_lang('Εκτεταμένη'),  'sql' => "gks_voip_calls.gks_primary_rec>=0"),
    )
);

$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_voip_calls.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_voip_calls.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);




$sortable = array(
	array('name' => 'soid', 'field' => 'gks_voip_calls.id_voip_call'),
	array('name' => 'somydate_add', 'field' => 'gks_voip_calls.mydate_add'),
	array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	array('name' => 'somyfrom', 'field' => 'gks_voip_calls.src'),
	array('name' => 'somyto', 'field' => 'gks_voip_calls.dst'),
	array('name' => 'soclid', 'field' => 'gks_voip_calls.clid'),
	array('name' => 'socaller_name', 'field' => 'gks_voip_calls.caller_name'),

  array('name' => 'soerpapp', 'field' => 'gks_erp_app.erp_app_name'),
  array('name' => 'soAcctId', 'field' => 'AcctId'),
  array('name' => 'sodcontext', 'field' => 'dcontext'),
  array('name' => 'sochannel', 'field' => 'channel'),
  array('name' => 'sodstchannel', 'field' => 'dstchannel'),
  array('name' => 'solastapp', 'field' => 'lastapp'),
  array('name' => 'solastdata', 'field' => 'lastdata'),
  array('name' => 'sostart', 'field' => 'start'),
  array('name' => 'soanswer', 'field' => 'answer'),
  array('name' => 'soend', 'field' => 'end'),
  array('name' => 'soduration', 'field' => 'duration'),
  array('name' => 'sobillsec', 'field' => 'billsec'),
  array('name' => 'sodisposition', 'field' => 'disposition'),
  array('name' => 'soamaflags', 'field' => 'amaflags'),
  array('name' => 'souniqueid', 'field' => 'uniqueid'),
  array('name' => 'souserfield', 'field' => 'userfield'),
  array('name' => 'sochannel_ext', 'field' => 'channel_ext'),
  array('name' => 'sodstchannel_ext', 'field' => 'dstchannel_ext'),
  array('name' => 'soservice', 'field' => 'service'),
  array('name' => 'sodstanswer', 'field' => 'dstanswer'),
  array('name' => 'sorecordfiles', 'field' => 'recordfiles'),
  array('name' => 'sosession', 'field' => 'session'),
  array('name' => 'soaction_owner', 'field' => 'action_owner'),
  array('name' => 'soaction_type', 'field' => 'action_type'),
  array('name' => 'sosrc_trunk_name', 'field' => 'src_trunk_name'),
  array('name' => 'sodst_trunk_name', 'field' => 'dst_trunk_name'),
  array('name' => 'sonew_src', 'field' => 'new_src'),
  array('name' => 'soreason', 'field' => 'reason'),
  array('name' => 'sosn', 'field' => 'sn'),

);

$search_fields = array(
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
'gks_voip_calls.src',
'gks_voip_calls.dst',
'gks_voip_calls.clid',
'gks_voip_calls.caller_name',

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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_voip_calls.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_erp_app.erp_app_name
FROM (gks_voip_calls 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_voip_calls.gks_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_erp_app ON gks_voip_calls.erp_app_id = gks_erp_app.id_erp_app
where 1=1 ".$where . $search_where;



if (empty($sorted['sql'])) {
	$query .= " ORDER BY gks_voip_calls.mydate_add desc, gks_voip_calls.id_voip_call desc";
} else {
	$query .= " ORDER BY " . $sorted['sql'];
}
$query .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $query;die();
	
$result = $db_link->query($query);        
if (!$result) debug_mail(false,'error sql',$query);
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
.voip_more_data_row {
  color:#0069d9;
  cursor:pointer;
  font-size: 20px;
}  
.p-40 {
  padding-right:30px !important;  
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
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
<?php if ($myview!=2) { ?>    
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"></th>        
<?php } ?>    
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopr', '<span class="tooltipster" title="Primary Rec">PR</span>'); ?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_add', gks_lang('Ημερομηνία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_add', gks_lang('Πότε')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Επαφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyfrom', gks_lang('Από')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyto', gks_lang('Προς')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soclid', gks_lang('clid')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socaller_name', 'Caller Name'); ?></th>        
<?php if ($myview==2) { ?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisposition', 'disposition'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soerpapp', 'Desktop App'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soAcctId', 'AcctId'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodcontext', 'dcontext'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochannel', 'channel'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodstchannel', 'dstchannel'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solastapp', 'lastapp'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solastdata', 'lastdata'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostart', 'start'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soanswer', 'answer'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soend', 'end'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soduration', 'duration'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobillsec', 'billsec'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soamaflags', 'amaflags'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souniqueid', 'uniqueid'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souserfield', 'userfield'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochannel_ext', 'channel_ext'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodstchannel_ext', 'dstchannel_ext'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soservice', 'service'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodstanswer', 'dstanswer'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorecordfiles', 'recordfiles'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosession', 'session'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaction_owner', 'action_owner'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaction_type', 'action_type'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosrc_trunk_name', 'src_trunk_name'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodst_trunk_name', 'dst_trunk_name'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonew_src', 'new_src'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreason', 'reason'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosn', 'sn'); ?></th>        
<?php } ?>
      
    </tr>
</thead>
<tbody>

    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> gks_tr1_uniqueid"  data-tr1-uniqueid="<?php echo $row['uniqueid'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
<?php if ($myview!=2) { ?>    
    <td nowrap class="mytdcm p-0"><i class="fas fa-caret-square-down voip_more_data_row" data-uniqueid="<?php echo $row['uniqueid'];?>" data-status="0"></i></td> 
<?php } ?>
    <td nowrap class="mytdcm"><?php echo $row['id_voip_call'];?></td>  
    <td nowrap class="mytdcm"><?php 
      if ($row['gks_primary_rec']==1) {
        echo '<img src="img/1.png" border="0" width="16">';
      }?></td>  
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php echo secondsago(strtotime($row['mydate_add']));?></td>   
    <td class="mytdcml"><a href="admin-users-item-overview.php?id=<?php echo $row['gks_user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>   
    <td nowrap class="mytdcm p-40">
      <a href="tel:<?php echo $row['src'];?>" class="<?php echo $gks_voip_params['class_span'];?>"><?php echo $row['src'];?></a>
      <?php echo $gks_voip_params['html_after_span'];?>
    </td>   
    <td nowrap class="mytdcm"><?php echo $row['dst'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['clid'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['caller_name'];?></td>   
<?php if ($myview==2) { ?>
    <td nowrap class="mytdcm"><?php echo $row['disposition'];?></td>   
    <td nowrap class="mytdcml"><a href="admin-erp-app-item.php?id=<?php echo $row['erp_app_id'];?>"><?php echo $row['erp_app_name'];?></a></td>   
    <td nowrap class="mytdcm"><?php echo $row['AcctId'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['dcontext'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['channel'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['dstchannel'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['lastapp'];?></td>   
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['lastdata'];
    ?></div></div></td>
    <td nowrap class="mytdcm"><?php echo $row['start'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['answer'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['end'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['duration'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['billsec'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['amaflags'];?></td>   
    <td nowrap class="mytdcml"><?php echo $row['uniqueid'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['userfield'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['channel_ext'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['dstchannel_ext'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['service'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['dstanswer'];?></td>   
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['recordfiles'];
    ?></div></div></td>     
    <td nowrap class="mytdcm"><?php echo $row['session'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['action_owner'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['action_type'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['src_trunk_name'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['dst_trunk_name'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['new_src'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['reason'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['sn'];?></td>   


<?php } ?> 
         
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
 
  $('.voip_more_data_row').click(function() {
    uniqueid=$(this).attr('data-uniqueid').trim();
    if (uniqueid=='') return;
    
    data_status=$(this).attr('data-status');
    if (data_status=='1') {
      $('.gks_tr2_uniqueid[data-tr2-uniqueid="' + uniqueid + '"]').remove();
      $(this).attr('data-status','0');
      $(this).addClass('fa-caret-square-down').removeClass('fa-caret-square-up');
    } else {
      $(this).addClass('fa-caret-square-up').removeClass('fa-caret-square-down');
      $(this).attr('data-status','2');
      datasend='cmd=percall';
      datasend+='&uniqueid='+uniqueid;
      datasend+='&view=long';
      $.ajax({
    		url: 'admin-phone-cmd.php',
    		type: 'POST',
    		cache: false,
    		dataType: 'json',
    		data: datasend,
    		error : function(jqXHR ,textStatus,  errorThrown) {
    			myalert('error:' + jqXHR.responseText);
    		},				
    		success: function(data) {
    			if (!data) {
    				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    			} else {
    				if (data.success == true) {
    					//console.log(data.uniqueid);
    					//console.log(data.html);
    					elemtr=$('.gks_tr1_uniqueid[data-tr1-uniqueid="' + data.uniqueid + '"]');
    					if (elemtr.length!=1) return;
    					htmltr='<tr class="gks_tr2_uniqueid" data-tr2-uniqueid="' + data.uniqueid + '">'+
    					'<td colspan="11">'+
    					data.html+
    					'</td></tr>';
    					elemtr.after(htmltr);
    					$('.voip_more_data_row[data-uniqueid="' + data.uniqueid + '"]').attr('data-status','1');
    					  
    					$('.gks_tr2_uniqueid[data-tr2-uniqueid="'+ data.uniqueid + '"] .mydivexpand').click(gks_mydivexpand_click);
    					
    				} else {
    					myalert('error:' + $.base64.decode(data.message));
    				}
    			}
    		}
    		
    	});
    }
  });
  
});

</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


