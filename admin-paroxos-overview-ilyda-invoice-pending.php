<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Παραστατικά σε αναμονή για αποστολή στο myData από ΥΛΙΔΑ');
$nav_active_array=array('accounting','accounting_paroxos_overview','accounting_paroxos_overview_ilyda');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__paroxos_overview_ilyda','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







$filters = array();
$filters[] = array(
  'name' => 'finvoiceIssueDate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'invoiceIssueDate', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'invoiceIssueDate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10' =>
    array(
    	array('value' => 102,
    				'text' => gks_lang('Δεν έχει ορισθεί'),
    				'sql' => "invoiceIssueDate is null"),
    	array('value' => 103,
    				'text' => gks_lang('Έχει ορισθεί'),
    				'sql' => "invoiceIssueDate is not null"),
    ),
  ]),
);
$filters[] = array(
  'name' => 'finvoiceState',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "invoiceState = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "mark is null or mark=''"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "mark<>''"),
  ),
  'sql' => "SELECT invoiceState as id, invoiceState as descr
FROM gks_paroxos_overview_ilyda_invoice_pending
WHERE invoiceState<>'' GROUP BY invoiceState ORDER BY invoiceState;",    
);
$filters[] = array(
  'name' => 'fafm',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('ΑΦΜ'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "afm = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT afm as id, afm as descr
FROM gks_paroxos_overview_ilyda_invoice_pending
WHERE afm<>'' GROUP BY afm ORDER BY afm;",    
);
$filters[] = array(
  'name' => 'fsellerVatIdentifier',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πωλητής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "sellerVatIdentifier = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT sellerVatIdentifier as id, sellerVatIdentifier as descr
FROM gks_paroxos_overview_ilyda_invoice_pending
WHERE sellerVatIdentifier<>'' GROUP BY sellerVatIdentifier ORDER BY sellerVatIdentifier;",    
);
$filters[] = array(
  'name' => 'fseriesNumber',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Σειρά'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "seriesNumber = '%V%'",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_pay.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_pay.user_id<>0"),
  ),
  'sql' => "SELECT seriesNumber as id, seriesNumber as descr
FROM gks_paroxos_overview_ilyda_invoice_pending
WHERE seriesNumber<>'' GROUP BY seriesNumber ORDER BY seriesNumber;",    
);

$filters[] = array(
  'name' => 'fmark',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('ΜΑΡΚ'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "mark = '%V%'",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "mark is null or mark=''"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "mark<>''"),
  ),
  'sql' => "SELECT mark as id, mark as descr
FROM gks_paroxos_overview_ilyda_invoice_pending
WHERE mark<>'' GROUP BY mark ORDER BY mark;",    
);




$sortable = array(
  array('name' => 'soid', 'field' => 'id'),
  array('name' => 'soafm', 'field' => 'afm'),
  array('name' => 'soinvoiceNumber', 'field' => 'invoiceNumber'),
  array('name' => 'souid', 'field' => 'uid'),
  array('name' => 'sosellerVatIdentifier', 'field' => 'sellerVatIdentifier'),
  array('name' => 'soseriesNumber', 'field' => 'seriesNumber'),
  array('name' => 'soserialNumber', 'field' => 'serialNumber'),
  array('name' => 'soinvoiceId', 'field' => 'invoiceId'),
  array('name' => 'somark', 'field' => 'mark'),
  array('name' => 'soverificationHash', 'field' => 'verificationHash'),
  array('name' => 'soinvoiceIssueDate', 'field' => 'invoiceIssueDate'),
  array('name' => 'soinvoiceState', 'field' => 'invoiceState'),

);

$search_fields = array(
'afm',
'invoiceNumber',
'uid',
'sellerVatIdentifier',
'seriesNumber',
'serialNumber',
'invoiceId',
'mark',
'invoiceState',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_paroxos_overview_ilyda_invoice_pending.*
FROM gks_paroxos_overview_ilyda_invoice_pending 
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY invoiceIssueDate desc,id";
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvoiceIssueDate', gks_lang('Ημερομηνία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvoiceState', gks_lang('Κατάσταση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soafm', gks_lang('ΑΦΜ')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvoiceNumber', gks_lang('Παραστατικό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souid', 'uid'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosellerVatIdentifier', gks_lang('Πωλητής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseriesNumber', gks_lang('Σειρά')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserialNumber', gks_lang('Αριθμός')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinvoiceId', 'invoiceId'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somark', gks_lang('ΜΑΡΚ')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soverificationHash', 'Hash'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="40%"><?php echo gks_lang('Σφάλματα');?></th>
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

    <td class="mytdcm" nowrap><?php if (!empty($row['invoiceIssueDate'])) echo showDate(strtotime($row['invoiceIssueDate']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" ><?php echo $row['invoiceState'];?></td>
    <td class="mytdcm"><?php echo $row['afm'];?></td>
    <td class="mytdcml"><?php echo $row['invoiceNumber'];?></td>
    <td class="mytdcml"><?php echo $row['uid'];?></td>
    <td class="mytdcm" ><?php echo $row['sellerVatIdentifier'];?></td>
    <td class="mytdcm" ><?php echo $row['seriesNumber'];?></td>
    <td class="mytdcm" ><?php echo $row['serialNumber'];?></td>
    <td class="mytdcm" ><?php echo $row['invoiceId'];?></td>
    <td class="mytdcm" ><?php echo $row['mark'];?></td>
    <td class="mytdcm" ><?php echo $row['verificationHash'];?></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      if (!empty($row['errorsJson'])) {
        $temp=json_decode($row['errorsJson'],true);
        echo '<pre>';
        echo json_encode($temp,JSON_PRETTY_PRINT);
        echo '</pre>';
      }
    ?></div></div></td> 


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

  $('#finvoiceIssueDate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#finvoiceIssueDate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'finvoiceIssueDate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'finvoiceIssueDate' || gks_custom_filters_date_elems.includes(sname)) {
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


