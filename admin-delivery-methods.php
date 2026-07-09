<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Τρόποι Αποστολής');
$nav_active_array=array('manage','manage_d');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_delivery_methods','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_delivery_methods','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_delivery_methods',['from'=>'list']);

$filters = array();


$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_delivery_methods.delivery_method_type = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
    ),
    'sql' => "select delivery_method_type as id, delivery_method_type as descr 
    from gks_delivery_methods 
    where delivery_method_type<>''
    group by delivery_method_type order by delivery_method_type",
    
);
$filters[] = array(
    'name' => 'fdev',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Δοκιμαστικό Περιβάλλον'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_delivery_methods.delivery_method_env_test = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_delivery_methods.delivery_method_env_test<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_delivery_methods.delivery_method_env_test=0"),
    ),
);
$filters[] = array(
    'name' => 'ffee',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργοποίηση Κόστους'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_delivery_methods.delivery_method_fees_enabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_delivery_methods.delivery_method_fees_enabled<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_delivery_methods.delivery_method_fees_enabled=0"),
    ),
);

$filters[] = array(
    'name' => 'fenable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_delivery_methods.delivery_method_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργός'),    'sql' => "gks_delivery_methods.delivery_method_disabled=0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργός'), 'sql' => "gks_delivery_methods.delivery_method_disabled<>0"),
    ),
);


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_delivery_methods.id_delivery_method'),
  						array('name' => 'soname', 'field' => 'gks_delivery_methods.delivery_method_name'),
  						array('name' => 'sodescr', 'field' => 'gks_delivery_methods.delivery_method_html'),
  						array('name' => 'sotype', 'field' => 'gks_delivery_methods.delivery_method_type'),
  						array('name' => 'sotypepa', 'field' => 'gks_delivery_methods.delivery_method_type_pa'),
  						array('name' => 'sodev', 'field' => 'gks_delivery_methods.delivery_method_env_test'),
  						array('name' => 'sofees', 'field' => 'gks_delivery_methods.delivery_method_fees_enabled'),
  						array('name' => 'sosort', 'field' => 'gks_delivery_methods.mysortorder'),
  						array('name' => 'sodisable', 'field' => 'gks_delivery_methods.delivery_method_disabled'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array(
'gks_delivery_methods.delivery_method_name',
'gks_delivery_methods.delivery_method_html',
'gks_delivery_methods.delivery_method_sxolio',
'gks_delivery_methods.delivery_method_tooltip',
'delivery_method_php_function_isok',
'delivery_method_php_function_calculate',

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

//SELECT SQL_CALC_FOUND_ROWS gks_delivery_methods.*, 
//other.delivery_method_html AS other_descr
//FROM gks_delivery_methods 
//LEFT JOIN gks_delivery_methods AS other ON gks_delivery_methods.monada_parent_id = other.id_delivery_method

$sql = "select SQL_CALC_FOUND_ROWS gks_delivery_methods.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (gks_delivery_methods
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_delivery_methods.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_delivery_methods.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY mysortorder,delivery_method_name";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $sql;die();
	
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
      <a class="btn btn-primary gks_add_new_record" href="admin-delivery-methods-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τρόπου αποστολής');?></a>
      <a class="btn btn-primary gks_add_new_record" href="admin-delivery-payment.php"><?php echo gks_lang('Έλεγχος Τρόπων Αποστολής-Πληρωμής');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_delivery_methods">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" colspan=0><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('HTML (FrontEnd)')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotypepa', '<span title="'.gks_lang('Σχετικοί Τύποι Πληρωμής').'" class="tooltipster">'.gks_lang('Σx.Τύπο.Πλη.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodev', '<span title="'.gks_lang('Δοκιμαστικό Περιβάλλον').'" class="tooltipster">'.gks_lang('Δ.Π.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofees', '<span title="'.gks_lang('Ενεργοποίηση Κόστους').'" class="tooltipster">'.gks_lang('Κόστος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%" nowrap><?php echo gks_lang('Υπολογισμός');?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'" class="tooltipster">ΣειράΤ</span>'); ?></th>        
<?php } ?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργός')); ?></th>   
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_delivery_method'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-delivery-methods-item.php?id=<?php echo $row['id_delivery_method'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_delivery_method'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_delivery_method'];?>" data-model="gks_delivery_methods"></i></td>
        </tr>      
      </table>
    </td>
    
    <td class="mytdcml"><?php echo $row['delivery_method_name'];?></td>
    <td class="mytdcml"><?php echo nl2br_gks($row['delivery_method_html']);?></td>
    
    <td class="mytdcml"><?php echo $row['delivery_method_type'];?></td>
    <td class="mytdcml"><?php echo $row['delivery_method_type_pa'];?></td>
    
    
    <td class="mytdcm"><img src="img/<?php echo $row['delivery_method_env_test']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>    
    <td class="mytdcm"><img src="img/<?php echo $row['delivery_method_fees_enabled']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>    
    <td class="mytdcml"><?php 
      $temp='';
      if ($row['dm_fees_price']!=0) $temp.= $row['dm_fees_price'].' | ';
      if ($row['dm_fees_free_if_greater_than']!=0) $temp.= $row['dm_fees_free_if_greater_than'].' | ';
      if ($row['dm_fees_international_fixed']!=0) $temp.= $row['dm_fees_international_fixed'].' | ';
      if (trim_gks($row['delivery_method_php_function_isok'])!='') $temp.= $row['delivery_method_php_function_isok'].' | ';
      if (trim_gks($row['delivery_method_php_function_calculate'])!='') $temp.= $row['delivery_method_php_function_calculate'].' | ';
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-3);
      echo $temp;
   ?></td>    

    
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['mysortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['mysortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><img src="img/<?php echo $row['delivery_method_disabled']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_delivery_methods','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_delivery_methods','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_delivery_methods','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#table_gks_delivery_methods > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_delivery_methods',mylist,'#table_gks_delivery_methods > tbody');
    }
  });  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


