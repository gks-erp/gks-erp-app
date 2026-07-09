<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Αναφορά Συντήρησης Οχημάτων');
$nav_active_array=array('assets','assets_oximata_sintirisi_report');



$id_asset=0;
if (isset($_GET['id_asset'])) $id_asset = intval($_GET['id_asset']);

$mydaydif=0;
if (isset($_GET['day'])) $mydaydif=intval($_GET['day']);


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$filters=array();

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
    WHERE gks_assets.asset_type=26 and gks_warehouses.id_warehouse>0
    GROUP BY gks_assets.asset_last_warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name;",
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => 1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "asset_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργό'),  'sql' => "asset_disable <> 0"),
    ),
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
    and asset_type=26 
    GROUP BY gks_assets.asset_last_company_id, gks_company.company_title
    ORDER BY gks_company.company_title;",
);
$sortable = array(
	array('name' => 'soid', 'field' => 'gks_assets.id_asset'),
	array('name' => 'sophoto', 'field' => 'gks_assets.asset_photo'),
	array('name' => 'socode', 'field' => 'gks_assets.asset_code'),
	array('name' => 'sotitle', 'field' => 'gks_assets.asset_title'),
	array('name' => 'soserialnumber', 'field' => 'gks_assets.asset_serialnumber'),
	array('name' => 'sowarehouse_id', 'field' => 'warehouse_name'),
	array('name' => 'socompany', 'field' => 'company_title'),
	array('name' => 'sotires', 'field' => 'oxima_elastika'),
	array('name' => 'sooxima_km', 'field' => 'oxima_km'),
	array('name' => 'sooxima_km_date', 'field' => 'oxima_km_date'),
	array('name' => 'sooxima_next_service_km', 'field' => 'oxima_next_service_km'),
	array('name' => 'sooxima_next_kteo', 'field' => 'oxima_next_kteo'),
	array('name' => 'sooxima_liji_asfaleia', 'field' => 'oxima_liji_asfaleia'),
);

$search_fields = array(
  'gks_assets.asset_title',
  'gks_assets.asset_code',
  'gks_assets.asset_serialnumber',
  'gks_assets.asset_sxolio',
  'gks_company.company_title',
  'warehouse_name',
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
											

$rows_per_page = 1000000;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;












$sql="SELECT SQL_CALC_FOUND_ROWS gks_assets.*, gks_warehouses.warehouse_name,gks_company.company_title
from (gks_assets 
LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company)
LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse
where asset_type=26 
and (asset_date_aposirsi is null or asset_date_aposirsi < now()) 
";

$sql.=" ".$where . $search_where;

if (empty($sorted['sql'])) {
	$sql.=" ORDER BY asset_code";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}


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



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  die('sql error');
}

$oximata=array();
while ($row = $result->fetch_assoc()) {
  $oximata[$row['id_asset']] = array(
    'id_asset' => $row['id_asset'],
    'asset_photo' => $row['asset_photo'],
    'asset_code' => $row['asset_code'],
    'asset_serialnumber' => $row['asset_serialnumber'],
    'asset_title' => $row['asset_title'],
    'oxima_elastika' => $row['oxima_elastika'],
    'oxima_km' => $row['oxima_km'],
    'oxima_km_date' => $row['oxima_km_date'],
    'oxima_km_now' => 0,
    'oxima_next_kteo' => $row['oxima_next_kteo'],
    'oxima_next_service_km' => $row['oxima_next_service_km'],
    'oxima_liji_asfaleia' => $row['oxima_liji_asfaleia'],
    'asset_last_warehouse_id' => $row['asset_last_warehouse_id'],
    'warehouse_name' => $row['warehouse_name'],
    'asset_last_company_id' => $row['asset_last_company_id'],
    'company_title' => $row['company_title'],
    'for_email' => false,
  );
}

$sql="SELECT maxtable.asset_id, gks_assets_oximata_km.mydateadd, gks_assets_oximata_km.km
FROM (
  SELECT gks_assets_oximata_km.asset_id, max(gks_assets_oximata_km.id_assets_oximata_km) AS maxid
  FROM gks_assets_oximata_km
  GROUP BY gks_assets_oximata_km.asset_id
) AS maxtable 
LEFT JOIN gks_assets_oximata_km ON maxtable.maxid = gks_assets_oximata_km.id_assets_oximata_km
where maxtable.asset_id>0 and id_assets_oximata_km is not null";
$result = $db_link->query($sql);
if (!$result) {  debug_mail(false,'error sql',$sql); die('sql error');}

while ($row = $result->fetch_assoc()) {
  if (isset($oximata[$row['asset_id']])) {
    $oximata[$row['asset_id']]['oxima_km'] = $row['km'];
    $oximata[$row['asset_id']]['oxima_km_date'] = $row['mydateadd'];
  }
}


foreach ($oximata as &$value) {
  if (isset($value['oxima_km']) and $value['oxima_km']>0 and isset($value['oxima_km_date'])) {
    $diff=(time() - strtotime($value['oxima_km_date'])) / (24*60*60); //diafora se imeres
    $diff=intval($diff);
    $extra_km = $diff * 50;
    $value['oxima_km_now'] = $value['oxima_km'] + $extra_km;
  }
} 
unset($value);

       





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
    <div class="col-sm-6 hide_on_print" style="text-align:center">
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Περιγραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserialnumber', 'Serial Number'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse_id', '<span class="tooltipster" title="'.gks_lang('Η αποθήκη στην οποία είναι χρεωμένο').'">'.gks_lang('Αποθήκη').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotires', gks_lang('Ελαστικά')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooxima_km_date', gks_lang('Km<br>στις')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooxima_km', gks_lang('Km<br>ήταν')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo gks_lang('Km<br>σήμερα');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooxima_next_service_km', gks_lang('Επόμενο<br>Service<br>σε Km')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooxima_next_kteo', gks_lang('Ημερομηνία<br>Επόμενου<br>ΚΤΕΟ')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sooxima_liji_asfaleia', gks_lang('Ημερομηνία<br>Λήξης<br>Ασφάλειας')); ?></th>
 
  </tr>
</thead>
<tbody>
  
<?php

$i = 0;
foreach ($oximata as $row) {
  
  $for_email=false;
  $service_red=false;
  $kteo_red=false;   
  $asfaleia_red=false;
  
  if (isset($row['oxima_km'])==false or isset($row['oxima_km_date'])==false or isset($row['oxima_km_now'])==false or 
      $row['oxima_km']<0 or $row['oxima_km_now']<0 or $row['oxima_km_now'] > $row['oxima_next_service_km']) {
    $service_red=true;        
  }
  if (isset($row['oxima_next_kteo']) ==false or     time() >= strtotime($row['oxima_next_kteo'])     - 10*24*60*60) $kteo_red=true;   
  if (isset($row['oxima_liji_asfaleia']) ==false or time() >= strtotime($row['oxima_liji_asfaleia']) - 10*24*60*60) $asfaleia_red=true;   
  
  if ($service_red or $kteo_red or $asfaleia_red) $row['for_email']=true;

	$i++;
  ?>
  <tr>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm">
      <?php echo $row['id_asset'];?><br>
      <a href="admin-assets-item.php?id=<?php echo $row['id_asset'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>      
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
    <td class="mytdcml"><a href="admin-warehouse-item.php?id=<?php echo $row['asset_last_warehouse_id'];?>"><?php echo $row['warehouse_name'];?></a></td>   
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['asset_last_company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td class="mytdcm" nowrap><?php echo $row['oxima_elastika']; ?></td>

    <td class="mytdcm" nowrap><?php echo (isset($row['oxima_km_date']) ? date('d/m/Y' , strtotime($row['oxima_km_date'])) : '');?></td>
    <td class="mytdcm" nowrap><?php echo number_format($row['oxima_km'],0,'','.');?></td>
    <td class="mytdcm" nowrap><?php echo number_format($row['oxima_km_now'],0,'','.');?></td>
    <td class="mytdcm" nowrap <?php echo ($service_red ? 'bgcolor="#ff0000"' : '');?>><?php echo number_format($row['oxima_next_service_km'],0,'','.');?></td>
    <td class="mytdcm" nowrap <?php echo ($kteo_red ? 'bgcolor="#ff0000"' : '');?>><?php echo (isset($row['oxima_next_kteo']) ? date('d/m/Y' , strtotime($row['oxima_next_kteo'])) : '');?></td>
    <td class="mytdcm" nowrap <?php echo ($asfaleia_red ? 'bgcolor="#ff0000"' : '');?>><?php echo (isset($row['oxima_liji_asfaleia']) ? date('d/m/Y' , strtotime($row['oxima_liji_asfaleia'])) : '');?></td>
    
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


