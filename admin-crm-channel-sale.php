<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Κανάλια πωλήσεων');
$nav_active_array=array('crm','crm_channel_sale');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_channel_sale','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_crm_channel_sale_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_channel_sale','edit',0);








$filters = array();

$filters[] = array(
    'name' => 'fcontact',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει επαφή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_channel_has_contact=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "crm_channel_has_contact=1"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "crm_channel_has_contact=0"),
    ),
    
);
$filters[] = array(
    'name' => 'fcampain',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει καμπάνια'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_channel_has_campain=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "crm_channel_has_campain=1"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "crm_channel_has_campain=0"),
    ),
    
);
$filters[] = array(
    'name' => 'furl',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει URL'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_channel_has_url=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "crm_channel_has_url=1"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "crm_channel_has_url=0"),
    ),
    
);

$filters[] = array(
    'name' => 'fcode',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει κωδικό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_channel_has_code=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "crm_channel_has_code=1"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "crm_channel_has_code=0"),
    ),
    
);

$filters[] = array(
    'name' => 'ftext',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει σχόλιο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "crm_channel_has_text=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "crm_channel_has_text=1"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "crm_channel_has_text=0"),
    ),
    
);


$sortable = array(
	array('name' => 'soid',    'field' => 'gks_crm_channel_sale.id_crm_channel_sale'),
	array('name' => 'sodescr', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
	array('name' => 'socontact', 'field' => 'gks_crm_channel_sale.crm_channel_has_contact'),
	array('name' => 'socontactfilter', 'field' => 'gks_crm_channel_sale.crm_channel_has_contact_filter'),
	array('name' => 'socampain', 'field' => 'gks_crm_channel_sale.crm_channel_has_campain'),
	array('name' => 'sourl', 'field' => 'gks_crm_channel_sale.crm_channel_has_url'),
	array('name' => 'sotext', 'field' => 'gks_crm_channel_sale.crm_channel_has_text'),
	array('name' => 'socode', 'field' => 'gks_crm_channel_sale.crm_channel_has_code'),
    	
	array('name' => 'sosort', 'field' => 'gks_crm_channel_sale.crm_channel_sale_sortorder'),
	array('name' => 'sodisabled', 'field' => 'gks_crm_channel_sale.crm_channel_sale_disabled'),
);
$search_fields = array(
'gks_crm_channel_sale.crm_channel_sale_descr',
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




$sql = "SELECT SQL_CALC_FOUND_ROWS gks_crm_channel_sale.* 
FROM gks_crm_channel_sale
";  
//echo '<pre>';echo $sql;die();
$sql.= " where 1=1 ";
$sql.=$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY crm_channel_sale_sortorder";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-crm-channel-sale-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου καναλιού');?></a>
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
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_crm_channel_sale">
<thead>
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socontact', gks_lang('Έχει επαφή')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socontactφιλτερ', gks_lang('Φίλτρο επαφών')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socampain', gks_lang('Έχει καμπάνια')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sourl', gks_lang('Έχει URL')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Έχει κωδικό')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotext', gks_lang('Έχει σχόλιο')); ?></th>        
    <?php if ($perm_crm_channel_sale_edit) {?>
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
    <?php } ?>
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisabled', gks_lang('Ενεργό')); ?></th> 
    
           
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_crm_channel_sale'];?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['id_crm_channel_sale'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_crm_channel_sale'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_crm_channel_sale'];?>" data-model="gks_crm_channel_sale"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcml"><?php echo $row['crm_channel_sale_descr'];?></td>
    
    
    <td nowrap class="mytdcm"><?php echo myimg010($row['crm_channel_has_contact']);?></td> 
    <td nowrap class="mytdcm"><?php echo $row['crm_channel_has_contact_filter'];?></td> 
    <td nowrap class="mytdcm"><?php echo myimg010($row['crm_channel_has_campain']);?></td> 
    <td nowrap class="mytdcm"><?php echo myimg010($row['crm_channel_has_url']);?></td> 
    <td nowrap class="mytdcm"><?php echo myimg010($row['crm_channel_has_code']);?></td> 
    <td nowrap class="mytdcm"><?php echo myimg010($row['crm_channel_has_text']);?></td> 
    <?php if ($perm_crm_channel_sale_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['crm_channel_sale_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['crm_channel_sale_sortorder'];?></span>
    </td>
    <?php } ?>
    <td nowrap class="mytdcm"><?php echo myimg010r($row['crm_channel_sale_disabled']);?></td> 
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#table_gks_crm_channel_sale > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_crm_channel_sale',mylist,'#table_gks_crm_channel_sale > tbody');
    }
  });
    
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


