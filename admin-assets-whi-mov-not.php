<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Πάγια που δεν έχουν απογραφεί');
$nav_active_array=array('assets','assets_whi_mov_not');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_whi_mov','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_assets_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets','delete',0);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets',['from'=>'list']);

$filters = array();

$filters[] = array(
	'name' => 'fmydate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Απογραφής'),
	'has_custom_date' => true,
	'field' => 'gks_assets_whi_mov.mydate',
	'has_custom_default' => 18,
	'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_assets_whi_mov.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

//echo $today;
//die();


$filters[] = array(
    'name' => 'fasset_type',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
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
    'name' => 'fasset_last_warehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_warehouse_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        
    ),
    'sql' => "SELECT gks_assets.asset_last_warehouse_id as id, gks_warehouses.warehouse_name as descr
    FROM gks_assets LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse
    WHERE (((gks_warehouses.id_warehouse)>0)) 
    GROUP BY gks_assets.asset_last_warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name;",
);







$filters[] = array(
    'name' => 'flast_user_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Συνεργάτης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_user_id = %V%",
    'vals' => array(
        //array('value' => -1,  'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => -3,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.asset_last_user_id =0 or gks_assets.asset_last_user_id is null)"),
        array('value' => -4,  'text' => gks_lang('Ανατεθειμένα'),     'sql' =>  "gks_assets.asset_last_user_id >0"),
    ),
    'sql' => "SELECT gks_assets.asset_last_user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_assets LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID)>0)) 
    GROUP BY gks_assets.asset_last_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);




$filters[] = array(
    'name' => 'fcompany_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.asset_last_company_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.asset_last_company_id =0 or gks_assets.asset_last_company_id is null)"),
        
    ),
    'sql' => "SELECT gks_assets.asset_last_company_id as id, gks_company.company_title as descr
    FROM gks_assets LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company
    WHERE (((gks_company.id_company) Is Not Null))
    GROUP BY gks_assets.asset_last_company_id, gks_company.company_title
    ORDER BY gks_company.company_title;",
);
$filters[] = array(
    'name' => 'fbank_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τράπεζα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets.bank_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 0,  'text' => gks_lang('Μη ανατεθειμένα'),  'sql' => "(gks_assets.bank_id =0 or gks_assets.bank_id is null)"),
        
    ),
    'sql' => "SELECT gks_banks.id_bank as id, gks_banks.bank_descr as descr
FROM gks_assets LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank
WHERE (((gks_banks.id_bank) Is Not Null))
GROUP BY gks_banks.id_bank, gks_banks.bank_descr
ORDER BY gks_banks.bank_descr;",
);

$filters[] = array(
	'name' => 'fdate_agoras',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Ενεργοποίησης'),
	'has_custom_date' => true,
	'field' => 'asset_date_activate',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'asset_date_activate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);
$filters[] = array(
	'name' => 'fdate_diakopis',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Απόσυρσης'),
	'has_custom_date' => true,
	'field' => 'asset_date_aposirsi',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'asset_date_aposirsi','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
    'name' => 'fis_fotografou',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είναι του συνεργάτη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),  'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "gks_assets.is_fotografou <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "gks_assets.is_fotografou = 0"),
    ),
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "asset_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργό'),  'sql' => "asset_disable <> 0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_assets.id_asset'),
  						array('name' => 'sophoto', 'field' => 'gks_assets.asset_photo'),
  						array('name' => 'sotitle', 'field' => 'gks_assets.asset_title'),
  						array('name' => 'socode', 'field' => 'gks_assets.asset_code'),
  						array('name' => 'soagoras', 'field' => 'gks_assets.asset_date_activate'),
  						array('name' => 'sodiakopis', 'field' => 'gks_assets.asset_date_aposirsi'),
  						array('name' => 'sodisable', 'field' => 'gks_assets.asset_disable'),
  						array('name' => 'sowarehouse_id', 'field' => 'gks_warehouses.warehouse_name'),
  						array('name' => 'souser_id', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'soserialnumber', 'field' => 'gks_assets.asset_serialnumber'),
  						array('name' => 'sotype', 'field' => 'gks_assets_type.asset_type_descr'),
  						array('name' => 'sois_fotografou', 'field' => 'gks_assets.is_fotografou'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'sobank_descr', 'field' => 'gks_banks.bank_descr'),
  						array('name' => 'soxreosi_val', 'field' => 'gks_assets.xreosi_val'),
  						array('name' => 'soxreosi_type', 'field' => 'gks_assets.xreosi_type'),
  						array('name' => 'soelastika', 'field' => 'gks_assets.oxima_elastika'),
  						array('name' => 'sokm', 'field' => 'gks_assets.oxima_km'),
  						array('name' => 'sonextskm', 'field' => 'gks_assets.oxima_next_service_km'),
  						array('name' => 'sokteo', 'field' => 'gks_assets.oxima_next_kteo'),
  						array('name' => 'soasf', 'field' => 'gks_assets.oxima_liji_asfaleia'),
  						array('name' => 'solastaction', 'field' => 'gks_assets.last_action_date'),
  						array('name' => 'somac', 'field' => 'gks_assets.mac_address'),
  						array('name' => 'sothesi', 'field' => 'gks_assets.asset_thesi'),
  						array('name' => 'soviva', 'field' => 'gks_assets.viva_terminal_id'),
  						
   						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_assets.asset_title',
'gks_assets.asset_code',
'gks_assets.asset_serialnumber',
'gks_assets.asset_sxolio',
'gks_assets_type.asset_type_descr',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
'gks_warehouses.warehouse_name',
'gks_assets_type.asset_type_descr',
'gks_company.company_title',
'gks_assets.mac_address',
'gks_assets.asset_thesi',
'viva_terminal_id',
'viva_terminal_code',
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
$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_assets.*, gks_assets_type.asset_type_descr, gks_warehouses.warehouse_name, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_company.company_title,
gks_banks.bank_descr,
lastactionergas.warehouse_name as last_action_warehouse_name
".$gks_custom_prepare['sql_all_list_sele']."  
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((gks_assets 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type) 
LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company)
LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank)
LEFT JOIN gks_warehouses as lastactionergas ON gks_assets.last_action_warehouse_id = lastactionergas.id_warehouse

where gks_assets.asset_last_warehouse_id<>3 ";
$sql.=" ".$where . $search_where;

$sql.="\n and gks_assets.id_asset not IN (
  SELECT gks_assets_whi_mov_assets.asset_id
  FROM gks_assets_whi_mov LEFT JOIN gks_assets_whi_mov_assets ON gks_assets_whi_mov.id_assets_whi_mov = gks_assets_whi_mov_assets.assets_whi_mov_id
  WHERE gks_assets_whi_mov.assets_whi_mov_status='99complete' 
  and gks_assets_whi_mov_assets.posotita_found >= 1
  ".$where1."
  GROUP BY gks_assets_whi_mov_assets.asset_id
)";



//$_SESSION['gks']['assets']['select_sql']=str_replace(' SQL_CALC_FOUND_ROWS ',' ',$sql);

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_assets.asset_code";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql; die();
	
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

$row_array=array();
while ($row = $result->fetch_assoc()) {
  $row_array[]=$row;

    
}

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<style>
#lightgallery_user > tbody > tr > .tdimg {
    padding: 0px;
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="lightgallery_user">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><a href="?">A/A</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>  
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>  
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserialnumber', 'Serial Number'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse_id', '<span class="tooltipster" title="'.gks_lang('Η αποθήκη στην οποία είναι χρεωμένο').'">'.gks_lang('Αποθήκη').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_id', '<span class="tooltipster" title="'.gks_lang('Ο Συνεργάτης στον οποίο είναι χρεωμένη').'">'.gks_lang('Συνεργάτης').'</span>'); ?></th>                
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>   
             
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soagoras', gks_lang('Ημερομηνία<br>Ενεργοποίησης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodiakopis', gks_lang('Ημερομηνία<br>Απόσυρσης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo gks_lang('Σχόλιο');?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>   
        
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>               
    </tr>
</thead>
<tbody>
    <?php
    

    
    $i = 0;
    foreach ($row_array as $row) {

	$i++;
?>
  <tr>
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>   
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_asset'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-assets-item.php?id=<?php echo $row['id_asset'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_assets_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_asset'];?>" data-model="gks_assets"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td class="tdimg mytdcm"><?php 
    $myimgurl=trim_gks($row['asset_photo'].'');
    if ($myimgurl == '') {
      $myimgurl="/my/img/product.png";
      echo '<img src="/my/img/product.png" border="0" style="max-width:64px;max-height:64px;"/>';
    } else {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      echo '<a class="lightgalleryitem_user" href="'.$photo_url.'" data-sub-html="'.$row['asset_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>          

    
    <td class="mytdcml" nowrap><?php echo $row['asset_code'];?></td>
    <td class="mytdcml"><?php echo $row['asset_title'];?></td>
    <td class="mytdcml" nowrap><?php echo $row['asset_serialnumber']; ?></td>
    <td class="mytdcml"><?php echo $row['asset_type_descr'];?></td>

    
    <td class="mytdcml"><a href="admin-warehouse-item.php?id=<?php echo $row['asset_last_warehouse_id'];?>"><?php echo $row['warehouse_name'];?></a></td>   
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['asset_last_user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['asset_last_company_id'];?>"><?php echo $row['company_title'];?></a></td>
    
    <td class="mytdcm" nowrap><?php if (isset($row['asset_date_activate'])) echo showDate(strtotime($row['asset_date_activate']), 'd/m/Y', 1);?></td>   
    <td class="mytdcm" nowrap><?php if (isset($row['asset_date_aposirsi'])) echo showDate(strtotime($row['asset_date_aposirsi']), 'd/m/Y', 1);?></td> 
    <td class="mytdcml"><?php echo $row['asset_sxolio']; ?></td>
    <td class="mytdcm"><?php echo myimg010r($row['asset_disable']);?></td>
<?php
    echo gks_custom_table_list_rows($gks_custom_prepare,$row);
?>     
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#fdate_agoras-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_agoras-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdate_diakopis-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_diakopis-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fmydate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  


  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
            
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_agoras' || sname=='fdate_diakopis' || sname=='fmydate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_agoras' || sname=='fdate_diakopis' || sname=='fmydate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true
  });


  
  
      
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


