<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Κινήσεις Παγίων');
$nav_active_array=array('assets','assets_moves');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_moves','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();


$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_assets_moves.mydate',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_assets_moves.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
    'name' => 'fasset_type',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Παγίου'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_type = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ορισμένο'),  'sql' => "(gks_assets.asset_type =0 or gks_assets.asset_type is null)"),
    ),
    'sql' => "SELECT gks_assets_type.id_asset_type as id, gks_assets_type.asset_type_descr as descr
    FROM gks_assets_type ORDER BY asset_type_sortorder;",
);

$filters[] = array(
    'name' => 'fwarehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_moves.warehouse_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_moves.warehouse_id as id, gks_warehouses.warehouse_name as descr
    FROM gks_assets_moves LEFT JOIN gks_warehouses ON gks_assets_moves.warehouse_id = gks_warehouses.id_warehouse
    WHERE (((gks_warehouses.id_warehouse) Is Not Null)) 
    GROUP BY gks_assets_moves.warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name",
);

$filters[] = array(
    'name' => 'fuser_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Συνεργάτης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_moves.user_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_moves.user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_assets_moves LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_moves.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
      GROUP BY gks_assets_moves.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);
$filters[] = array(
    'name' => 'myret',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Κίνησης'),
    'field' => 'gks_assets_moves.user_id = %V%',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 2, 'text' => "-&gt;",  'sql' => "(gks_assets_moves.user_id <>0 or gks_assets_moves.company_id<>0)"),
        array('value' => 3, 'text' => "&lt;-",  'sql' => "(gks_assets_moves.user_id=0 and gks_assets_moves.company_id=0)"),
    )
);
$filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_moves.company_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_moves.company_id as id, gks_company.company_title as descr
    FROM gks_assets_moves LEFT JOIN gks_company ON gks_assets_moves.company_id = gks_company.id_company
    WHERE (((gks_company.id_company) Is Not Null)) 
    GROUP BY gks_assets_moves.company_id, gks_company.company_title
    ORDER BY gks_company.company_title",
);
$filters[] = array(
    'name' => 'fuser_id_add',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρήστης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_moves.user_id_add = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_moves.user_id_add as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_assets_moves 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_moves.user_id_add = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
      GROUP BY gks_assets_moves.user_id_add, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);
	

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_assets_moves.id_assets_moves'),
	array('name' => 'somydate', 'field' => 'gks_assets_moves.mydate'),
	array('name' => 'soasset_title', 'field' => 'asset_title'),
	array('name' => 'soasset_code', 'field' => 'asset_code'),
	array('name' => 'soasset_serialnumber', 'field' => 'asset_serialnumber'),
	array('name' => 'sowarehouse_name', 'field' => 'warehouse_name'),
	array('name' => 'souser_id', 'field' => 'user_id'),
	array('name' => 'sogks_nickname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	array('name' => 'socompanyname', 'field' => 'gks_company.company_title'),
	array('name' => 'souser_add_nickname', 'field' => 'user_add_nickname'),
	array('name' => 'soip', 'field' => 'action_myip'),
);

$search_fields = array(
	'asset_code',
  'asset_title',
  'asset_serialnumber',
  'warehouse_name',
  GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  'wp_users_add.gks_nickname',
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



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_assets_moves.*, 
gks_assets.asset_title, gks_assets.asset_serialnumber, gks_warehouses.warehouse_name, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, wp_users_add.gks_nickname AS user_add_nickname,asset_code,
gks_company.company_title
FROM ((((gks_assets_moves 
LEFT JOIN gks_assets ON gks_assets_moves.asset_id = gks_assets.id_asset) 
LEFT JOIN gks_warehouses ON gks_assets_moves.warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_moves.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company ON gks_assets_moves.company_id = gks_company.id_company)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_add ON gks_assets_moves.user_id_add = wp_users_add.ID

where 1=1

".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_assets_moves.mydate DESC";
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

    <th class="table-dark" scope="col"  style="text-align: center !important;" width="0%"  nowrap ><a href="?">A/A</a></th>
    <th class="table-dark" scope="col"  style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>   
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="5%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="5%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset_code', gks_lang('Κωδικός')); ?></th>        
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset_title', gks_lang('Πάγιο')); ?></th>        
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset_serialnumber', 'Serial Number'); ?></th>        
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse_name', gks_lang('Αποθήκη')); ?></th>        
    <th class="table-dark" scope="col"  style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_id', gks_lang('Τύπος')); ?></th>  
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogks_nickname', gks_lang('Συνεργάτης')); ?></th>
    <th class="table-dark" scope="col"  style="text-align:left    !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompanyname', gks_lang('Εταιρεία')); ?></th>
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_add_nickname', gks_lang('Χρήστης')); ?></th>  
    <th class="table-dark" scope="col"  style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soip', 'IP'); ?></th>


 
    
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
    <td nowrap class="mytdcml"><?php echo $row['id_assets_moves'];?></td>   
    <td class="mytdcml" nowrap><?php echo showDate(strtotime($row['mydate']), 'd/m/Y H:i:s', 1);?></td>   
    <td class="mytdcml" nowrap><a href="admin-assets-item.php?id=<?php echo $row['asset_id'];?>"><?php echo $row['asset_code']; ?></a></td>
    <td class="mytdcml"><a href="admin-assets-item.php?id=<?php echo $row['asset_id'];?>"><?php echo $row['asset_title'];?></a></td>
    <td class="mytdcml" nowrap><?php echo $row['asset_serialnumber']; ?></td>
    <td class="mytdcml"><?php echo '<a href="admin-warehouses-item.php?id='.$row['warehouse_id'].'">'.$row['warehouse_name'].'</a>';?></td>  
    <td class="mytdcm"><?php
    if ($row['user_id']>0 or $row['company_id']>0) {?>
      <i class="fas fa-sign-out-alt assetmovearrowright"></i>
    <?php } else { ?>
      <i class="fas fa-sign-in-alt fa-rotate-180 assetmovearrowleft"></i>
    <?php } ?></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['user_id'].'">'.$row['gks_nickname'].'</a>';?></td>  
    <td class="mytdcml"><?php echo '<a href="admin-company-item.php?id='.$row['company_id'].'">'.$row['company_title'].'</a>';?></td>  
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['user_add_nickname'].'</a>';?></td>  
    <td class="mytdcm"><a href="admin-stat-ip.php?ip=<?php echo $row['action_myip'];?>">V</a>          
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

  

  

  
});


</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


