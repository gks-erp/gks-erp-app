<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Στοιχεία Τιμοκαταλόγου-Κουπόνια');
$nav_active_array=array('manage','manage_pricelist_items');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist_items','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_pricelist_items',['from'=>'list']);


$filters = array();
$filters[] = array(
    'name' => 'fpricelist_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τιμοκατάλογος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist_items.pricelist_id = '%V%'",
    'vals' => array(
    ),
    'sql' => "SELECT id_pricelist as id,pricelist_descr as descr
              FROM gks_eshop_pricelist
              ORDER BY sortorder,id_pricelist",    
);

$filters[] = array(
  'name' => 'fdate_from',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Από Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_eshop_pricelist_items.pricelist_item_date_from', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_eshop_pricelist_items.pricelist_item_date_from','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),
);
$filters[] = array(
  'name' => 'fdate_to',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Έως Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_eshop_pricelist_items.pricelist_item_date_to', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_eshop_pricelist_items.pricelist_item_date_to','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'famx',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Αποκλειστικά μεμονωμένη χρήση').'">'.gks_lang('ΑΜΧ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist_items.pricelist_item_individual_use = '%V%'",
    'vals' => array(
        array('value' => 100, 'text' => gks_lang('Ναι'),   'sql' => "gks_eshop_pricelist_items.pricelist_item_individual_use<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),   'sql' => "gks_eshop_pricelist_items.pricelist_item_individual_use=0"),
    ),
);
$filters[] = array(
    'name' => 'fepp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Εξαίρεση των προϊόντων σε προσφορά').'">'.gks_lang('ΕΠσΠ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist_items.pricelist_item_exclude_sale_items = '%V%'",
    'vals' => array(
        array('value' => 100, 'text' => gks_lang('Ναι'),   'sql' => "gks_eshop_pricelist_items.pricelist_item_exclude_sale_items<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),   'sql' => "gks_eshop_pricelist_items.pricelist_item_exclude_sale_items=0"),
    ),
);



$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist_items.pricelist_disable = '%V%'",
    'vals' => array(
        array('value' => 100, 'text' => gks_lang('Ενεργό'),      'sql' => "gks_eshop_pricelist_items.pricelist_item_disable=0"),
        array('value' => 101, 'text' => gks_lang('Μη ενεργό'),   'sql' => "gks_eshop_pricelist_items.pricelist_item_disable<>0"),
    ),
);


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(

	array('name' => 'soid', 'field' => 'gks_eshop_pricelist_items.id_pricelist_item'),
	array('name' => 'sodescr', 'field' => 'gks_eshop_pricelist_items.pricelist_item_descr'),
	array('name' => 'solist', 'field' => 'gks_eshop_pricelist.pricelist_descr'),
	array('name' => 'socoupon', 'field' => 'gks_eshop_pricelist_items.pricelist_item_coupon'),
	array('name' => 'sosequence', 'field' => 'gks_eshop_pricelist_items.pricelist_item_sequence'),
	array('name' => 'soeval', 'field' => 'gks_eshop_pricelist_items.pricelist_item_price_epi,gks_eshop_pricelist_items.pricelist_item_price_plus,gks_eshop_pricelist_items.pricelist_item_price_eval'),
	array('name' => 'sodate_from', 'field' => 'gks_eshop_pricelist_items.pricelist_item_date_from'),
	array('name' => 'sodate_to', 'field' => 'gks_eshop_pricelist_items.pricelist_item_date_to'),
	array('name' => 'somin_posotita', 'field' => 'gks_eshop_pricelist_items.pricelist_item_min_posotita'),
	array('name' => 'somin_price', 'field' => 'gks_eshop_pricelist_items.pricelist_item_min_price'),
	array('name' => 'somax_price', 'field' => 'gks_eshop_pricelist_items.pricelist_item_max_price'),
	array('name' => 'soamx', 'field' => 'gks_eshop_pricelist_items.pricelist_item_individual_use'),
	array('name' => 'soepp', 'field' => 'gks_eshop_pricelist_items.pricelist_item_exclude_sale_items'),
	array('name' => 'soemail', 'field' => 'gks_eshop_pricelist_items.pricelist_item_users_emails'),
	array('name' => 'solpc', 'field' => 'gks_eshop_pricelist_items.pricelist_item_usage_limit'),
	array('name' => 'solpp', 'field' => 'gks_eshop_pricelist_items.pricelist_item_limit_usage_to_x_items'),
	array('name' => 'solpu', 'field' => 'gks_eshop_pricelist_items.pricelist_item_usage_limit_per_user'),
	array('name' => 'soenable', 'field' => 'gks_eshop_pricelist_items.pricelist_item_disable'),
);

            
            
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

            
$search_fields = array(
'gks_eshop_pricelist_items.pricelist_item_descr',
'gks_eshop_pricelist_items.pricelist_item_coupon',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_eshop_pricelist_items.*,
gks_eshop_pricelist.pricelist_descr
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." gks_eshop_pricelist_items 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_eshop_pricelist ON gks_eshop_pricelist_items.pricelist_id = gks_eshop_pricelist.id_pricelist

where 1=1 ".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY pricelist_item_sequence,id_pricelist_item";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-pricelists-items-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου στοιχείου τιμοκαταλόγου-κουπόνι');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_eshop_pricelists_items">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solist', gks_lang('Τιμοκατάλογος')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socoupon', gks_lang('Κουπόνι')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosequence', gks_lang('Σειρά')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeval', gks_lang('Τύπος')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('π.χ.');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_from', '<span class="tooltipster" title="'.gks_lang('Από Ημερομηνία').'">'.gks_lang('Από').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_to', '<span class="tooltipster" title="'.gks_lang('Έως Ημερομηνία').'">'.gks_lang('Έως').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somin_posotita', '<span class="tooltipster" title="'.gks_lang('Ελάχιστη Ποσότητα').'">'.gks_lang('Ελ.Π.').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somin_price', '<span class="tooltipster" title="'.gks_lang('Ελαχιστο Ποσό').'">'.gks_lang('Ε.Π.').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somax_price', '<span class="tooltipster" title="'.gks_lang('Μέγιστο Ποσό').'">'.gks_lang('Μ.Π.').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soamx', '<span class="tooltipster" title="'.gks_lang('Αποκλειστικά μεμονωμένη χρήση').'">'.gks_lang('ΑΜΧ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soepp', '<span class="tooltipster" title="'.gks_lang('Εξαίρεση των προϊόντων σε προσφορά').'">'.gks_lang('ΕΠσΠ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', '<span class="tooltipster" title="'.gks_lang('Επιτρεπόμενες διευθύνσεις email').'">'.gks_lang('email').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solpc', '<span class="tooltipster" title="'.gks_lang('Όριο χρήσης ανά κουπόνι').'">'.gks_lang('ΟΧαΚ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solpp', '<span class="tooltipster" title="'.gks_lang('Περιορισμός χρήσης σε X προϊόντα').'">'.gks_lang('ΠΧσΧΠ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solpu', '<span class="tooltipster" title="'.gks_lang('Όριο χρήσης ανά χρήστη').'">'.gks_lang('ΟΧαΧ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="14%" nowrap><?php echo gks_lang('Είδη');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="13%" nowrap><?php echo gks_lang('Κατηγορία');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="13%" nowrap><?php echo gks_lang('Μάρκα');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soenable', gks_lang('Ενεργό')); ?></th>

<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>
  </tr>
</thead>
<tbody> 
<?php 
$i=0;
while ($row = $result->fetch_assoc()) {

	$i++;
?>

  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_pricelist_item'];?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-pricelists-items-item.php?id=<?php echo $row['id_pricelist_item'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_pricelist_item'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_pricelist_item'];?>" data-model="gks_eshop_pricelist_items"></i></td>
        </tr>      
      </table>
    </td>
    
    <td class="mytdcml"><?php echo $row["pricelist_item_descr"];?></td>
    <td nowrap class="mytdcml"><?php echo $row["pricelist_descr"];?></td>

    <td nowrap class="mytdcm"><?php echo $row["pricelist_item_coupon"];?></td>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['pricelist_item_sequence'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['pricelist_item_sequence'];?></span>
    </td>
        
    <td nowrap class="mytdcm"><?php echo gks_lang('Τιμή');?>*(1+<?php echo $row['pricelist_item_price_epi']; ?>) + <?php echo $row['pricelist_item_price_plus'];?></td>
    <td nowrap class="mytdcm">100.00 --> <?php 
      $new_price =(100*(1+$row['pricelist_item_price_epi']) + $row['pricelist_item_price_plus']);
      echo number_format($new_price, 2, ',', '.');
      echo ' ('. number_format(($new_price - 100)*100 /100,2,',', '.').'%)';
      ?>
    </td>



    
    <td nowrap class="mytdcml"><?php if (isset($row['pricelist_item_date_from'])) echo showDate(strtotime($row['pricelist_item_date_from']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcml"><?php if (isset($row['pricelist_item_date_to'])) echo showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_min_posotita']!=0) echo myNumberFormat($row['pricelist_item_min_posotita'],0);?></td>
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_min_price']!=0) echo myCurrencyFormat($row['pricelist_item_min_price']);?></td>
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_max_price']!=0) echo myCurrencyFormat($row['pricelist_item_max_price']);?></td>
    <td nowrap class="mytdcm"><?php echo myimg010($row['pricelist_item_individual_use']);?></td>
    <td nowrap class="mytdcm"><?php echo myimg010($row['pricelist_item_exclude_sale_items']);?></td>
    <td class="mytdcm"><?php echo $row['pricelist_item_users_emails'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_max_price']!=0) echo myNumberFormat($row['pricelist_item_usage_limit'],0);?></td>
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_max_price']!=0) echo myNumberFormat($row['pricelist_item_limit_usage_to_x_items'],0);?></td>
    <td nowrap class="mytdcm"><?php if ($row['pricelist_item_max_price']!=0) echo myNumberFormat($row['pricelist_item_usage_limit_per_user'],0);?></td>
    
    
    <td  class="mytdcml">
    
    </td>
    <td  class="mytdcml">
      
    </td>
    <td  class="mytdcml">
      
    </td>


    <td class="mytdcm"><?php echo myimg010r($row['pricelist_item_disable']);?></td>
    
<?php
  echo gks_custom_table_list_rows($gks_custom_prepare,$row);
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdate_from-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_from-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fdate_to-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_to-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_from' || sname == 'fdate_to' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_from' || sname == 'fdate_to' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  
  $('#table_eshop_pricelists_items > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_eshop_pricelist_items',mylist,'#table_eshop_pricelists_items > tbody');
    }
  });
    
  
});

 
 
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>
  
<?php

include_once('_my_footer_admin.php');

