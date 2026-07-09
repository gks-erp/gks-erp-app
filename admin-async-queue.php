<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//admin-async-queue.php

$my_page_title=gks_lang('Ασύγχρονη Ουρά Εντολών');
$nav_active_array=array('manage','manage_settings','manage_system_async_queue');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();


$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
	'title' => gks_lang('Προσθήκη'),
	'has_custom_date' => true,
	'field' => 'gks_async_queue.mydate_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_async_queue.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);
$filters[] = array(
	'name' => 'fstart',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
	'title' => gks_lang('Έναρξη'),
	'has_custom_date' => true,
	'field' => 'gks_async_queue.start',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_async_queue.start','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);
$filters[] = array(
	'name' => 'fend',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
	'title' => gks_lang('Λήξη'),
	'has_custom_date' => true,
	'field' => 'gks_async_queue.end',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_async_queue.end','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
    'name' => 'fmytype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_async_queue.mytype = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "select mytype as id, mytype as descr
    from gks_async_queue
    where mytype<>'' 
    group by mytype
    order by mytype"
);
$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_async_queue.status = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('finish'),  'sql' => "gks_async_queue.status='finish'"),
        array('value' => 2, 'text' => gks_lang('errors'),  'sql' => "gks_async_queue.status='errors'"),
        array('value' => 3, 'text' => gks_lang('pending'), 'sql' => "gks_async_queue.status='pending'"),
        array('value' => 4, 'text' => gks_lang('running'), 'sql' => "gks_async_queue.status='running'"),
    ),
    'sql' => "select status as id, status as descr
    from gks_async_queue
    where status<>'' and status not in ('finish','errors','pending','running')
    group by status
    order by status"
);

$filters[] = array(
    'name' => 'fcmd',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εντολή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_async_queue.cmd = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "select cmd as id, cmd as descr
    from gks_async_queue
    where cmd<>''
    group by cmd
    order by cmd"
);

$filters[] = array(
    'name' => 'fresult',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποτέλεσμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_async_queue.result = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('ΟΚ'), 'sql' => "gks_async_queue.result=1"),
        array('value' => 2, 'text' => gks_lang('Σφάλμα'), 'sql' => "gks_async_queue.result=0"),
        
    ),
);



	

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_async_queue.id_async_queue'),
  						array('name' => 'somydate_add', 'field' => 'gks_async_queue.mydate_add'),
  						array('name' => 'sostart', 'field' => 'gks_async_queue.start'),
  						array('name' => 'soend', 'field' => 'gks_async_queue.end'),
  						array('name' => 'sodiarkia', 'field' => 'diarkia'),
  						
  						
  						
  						array('name' => 'soguid', 'field' => 'gks_async_queue.guid'),
  						array('name' => 'somytype', 'field' => 'gks_async_queue.mytype'),
  						array('name' => 'sostatus', 'field' => 'gks_async_queue.status'),
  						array('name' => 'socmd', 'field' => 'gks_async_queue.cmd'),
  						array('name' => 'soresult', 'field' => 'gks_async_queue.result'),
  						array('name' => 'soresult_message', 'field' => 'gks_async_queue.result_message'),
  						array('name' => 'soparam1', 'field' => 'gks_async_queue.param1'),
  						array('name' => 'soparam2', 'field' => 'gks_async_queue.param2'),
  						array('name' => 'soparam3', 'field' => 'gks_async_queue.param3'),
  						array('name' => 'soparam4', 'field' => 'gks_async_queue.param4'),
  						array('name' => 'soparam5', 'field' => 'gks_async_queue.param5'),
  						array('name' => 'soparam6', 'field' => 'gks_async_queue.param6'),
  						array('name' => 'soparam7', 'field' => 'gks_async_queue.param7'),
  						array('name' => 'soparam8', 'field' => 'gks_async_queue.param8'),
  						array('name' => 'soparam9', 'field' => 'gks_async_queue.param9'),

            );

$search_fields = array(
  'gks_async_queue.guid',
  'gks_async_queue.mytype',
  'gks_async_queue.status',
  'gks_async_queue.cmd',
  'gks_async_queue.result_message',

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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_async_queue.*,
TIMEDIFF(gks_async_queue.end, gks_async_queue.start) as diarkia
FROM gks_async_queue

where 1=1

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_async_queue.id_async_queue DESC";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;




//echo '<pre>'.$sql;die();
	
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
  
.icon_result {
    font-size: 160%;
    color: green;
}
.status_finish  {background: #47a447; border: 1px solid #2e6b2e; color:#ffffff; padding:0px 10px 0px 10px;border-radius: 10px;}
.status_errors  {background: #ff0000; border: 1px solid #c30000; color:#ffffff; padding:0px 10px 0px 10px;border-radius: 10px;}
.status_pending {background: #518df1; border: 1px solid #000000; color:#ffffff; padding:0px 10px 0px 10px;border-radius: 10px;}
.status_running {background: #8261a7; border: 1px solid #584272; color:#ffffff; padding:0px 10px 0px 10px;border-radius: 10px;}
.status_abort   {background: #000000; border: 1px solid #000000; color:#ffffff; padding:0px 10px 0px 10px;border-radius: 10px;}

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
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_add', gks_lang('Προσθήκη')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostart', gks_lang('Έναρξη')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soend', gks_lang('Λήξη')); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodiarkia', '<span class="tooltipster" title="'.gks_lang('Διάρκεια εκτέλεσης σε δευτερόλεπτα').'">'.gks_lang('Διάρκεια').'</span>'); ?></th>    
    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soguid', gks_lang('guid')); ?></th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somytype', gks_lang('Τύπος')); ?></th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socmd', gks_lang('Εντολή')); ?></th>    
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult', '<span class="tooltipster" title="'.gks_lang('Αποτέλεσμα').'">'.gks_lang('Α').'</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult_message', '<span class="tooltipster" title="'.gks_lang('Κείμενο Αποτελέσματος').'">'.gks_lang('Κείμενο').'</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam1', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 1">'.gks_lang('Π').'1</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam2', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 2">'.gks_lang('Π').'2</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam3', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 3">'.gks_lang('Π').'3</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam4', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 4">'.gks_lang('Π').'4</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam5', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 5">'.gks_lang('Π').'5</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam6', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 6">'.gks_lang('Π').'6</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam7', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 7">'.gks_lang('Π').'7</span>'); ?></th>    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam8', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 8">'.gks_lang('Π').'8</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparam9', '<span class="tooltipster" title="'.gks_lang('Παράμετρος').' 9">'.gks_lang('Π').'9</span>'); ?></th>

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
    <td nowrap class="mytdcml"><?php echo $row['id_async_queue'];?></td>   
    <td nowrap class="mytdcml"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>  
    <td nowrap class="mytdcml"><?php if (isset($row['start'])) echo showDate(strtotime($row['start']), 'd/m/Y H:i:s', 1);?></td>  
    <td nowrap class="mytdcml"><?php if (isset($row['end'])) echo showDate(strtotime($row['end']), 'd/m/Y H:i:s', 1);?></td>  
    <td nowrap class="mytdcm"><?php 
      
      //if (isset($row['start']) and isset($row['end'])) {
      //  echo strtotime($row['end'])-strtotime($row['start']);
      //}
      echo $row['diarkia'];
      ?></td>  
    <td nowrap class="mytdcml"><?php echo $row['guid'];?></td>   
    <td nowrap class="mytdcm"><?php echo $row['mytype'];?></td>
    <td nowrap class="mytdcm"><span class="status_<?php echo $row['status'];?>"><?php echo $row['status'];?></span></td>
    <td nowrap class="mytdcml"><?php echo $row['cmd'];?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['result']==1) echo '<i class="fas fa-check-circle icon_result" title="1"></i>';
      else echo $row['result'];
    ?></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php echo nl2br_gks($row['result_message']);?></div></div></td> 
    <td nowrap class="mytdcml"><?php echo $row['param1'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param2'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param3'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param4'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param5'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param6'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param7'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param8'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['param9'];?></td>
 
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
  $('#fstart-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fstart-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fend-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fend-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate' || sname == 'fstart' || sname == 'fend') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate' || sname == 'fstart' || sname == 'fend') {
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


