<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Τρόποι Πληρωμής');
$nav_active_array=array('manage','manage_p');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_payment_acquirers','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_payment_acquirers','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_payment_acquirers',['from'=>'list']);

$filters = array();


$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.payment_acquirer_type = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
    ),
    'sql' => "select payment_acquirer_type as id, payment_acquirer_type as descr 
    from gks_payment_acquirers 
    where payment_acquirer_type<>''
    group by payment_acquirer_type order by payment_acquirer_type",
    
);
$filters[] = array(
    'name' => 'fdev',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Δοκιμαστικό Περιβάλλον'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.payment_acquirer_env_test = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_payment_acquirers.payment_acquirer_env_test<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_payment_acquirers.payment_acquirer_env_test=0"),
    ),
);

$filters[] = array(
    'name' => 'fmethod',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μέθοδος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.payment_acquirer_method = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
    ),
    'sql' => "select payment_acquirer_method as id, payment_acquirer_method as descr 
    from gks_payment_acquirers 
    where payment_acquirer_method<>''
    group by payment_acquirer_method order by payment_acquirer_method",
    
);

$filters[] = array(
    'name' => 'ffee',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργοποίηση Κόστους'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.payment_acquirer_fees_enabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_payment_acquirers.payment_acquirer_fees_enabled<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_payment_acquirers.payment_acquirer_fees_enabled=0"),
    ),
);

$filters[] = array(
    'name' => 'faade',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('ΑΑΔΕ'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.aade_tropos_pliromis_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
    ),
    'sql' => "select id_aade_tropos_pliromis as id, aade_tropos_pliromis_descr as descr 
    from gks_aade_tropoi_pliromis
    order by sortorder",
    
);

$filters[] = array(
    'name' => 'fpay',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εμφάνιση στις Πληρωμές'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.show_acc_pay = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_payment_acquirers.show_acc_pay<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_payment_acquirers.show_acc_pay=0"),
    ),
);

$filters[] = array(
    'name' => 'feshop',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εμφάνιση στο eshop'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.show_eshop = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "gks_payment_acquirers.show_eshop<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "gks_payment_acquirers.show_eshop=0"),
    ),
);

      

$filters[] = array(
    'name' => 'fenable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_payment_acquirers.payment_acquirer_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργός'),    'sql' => "gks_payment_acquirers.payment_acquirer_disabled=0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργός'), 'sql' => "gks_payment_acquirers.payment_acquirer_disabled<>0"),
    ),
);


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_payment_acquirers.id_payment_acquirer'),
  						array('name' => 'soname', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  						array('name' => 'sodescr', 'field' => 'gks_payment_acquirers.payment_acquirer_html'),
  						array('name' => 'sobutton', 'field' => 'gks_payment_acquirers.payment_acquirer_button_html'),
  						array('name' => 'sotable', 'field' => 'gks_payment_acquirers.payment_acquirer_table_name'),
  						array('name' => 'sotype', 'field' => 'gks_payment_acquirers.payment_acquirer_type'),
  						array('name' => 'sotypedm', 'field' => 'gks_payment_acquirers.payment_acquirer_type_dm'),
  						array('name' => 'sodev', 'field' => 'gks_payment_acquirers.payment_acquirer_env_test'),
  						array('name' => 'somethod', 'field' => 'gks_payment_acquirers.payment_acquirer_method'),
  						array('name' => 'sofees', 'field' => 'gks_payment_acquirers.payment_acquirer_fees_enabled'),
  						array('name' => 'soaade', 'field' => 'gks_aade_tropoi_pliromis.aade_tropos_pliromis_descr'),
  						array('name' => 'sosort', 'field' => 'gks_payment_acquirers.mysortorder'),
  						array('name' => 'sopay', 'field' => 'gks_payment_acquirers.show_acc_pay'),
  						array('name' => 'soeshop', 'field' => 'gks_payment_acquirers.show_eshop'),
  						array('name' => 'sodisable', 'field' => 'gks_payment_acquirers.payment_acquirer_disabled'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array(
'gks_payment_acquirers.payment_acquirer_name',
'gks_payment_acquirers.payment_acquirer_table_name',
'gks_payment_acquirers.payment_acquirer_html',
'gks_payment_acquirers.payment_acquirer_button_html',
'gks_payment_acquirers.payment_acquirer_sxolio',
'gks_payment_acquirers.payment_acquirer_tooltip',
'payment_acquirer_php_function_isok',
'payment_acquirer_php_function_calculate',
'gks_aade_tropoi_pliromis.aade_tropos_pliromis_descr',

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

//SELECT SQL_CALC_FOUND_ROWS gks_payment_acquirers.*, 
//other.payment_acquirer_html AS other_descr
//FROM gks_payment_acquirers 
//LEFT JOIN gks_payment_acquirers AS other ON gks_payment_acquirers.monada_parent_id = other.id_payment_acquirer

$sql = "select SQL_CALC_FOUND_ROWS gks_payment_acquirers.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_aade_tropoi_pliromis.aade_tropos_pliromis_descr
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((gks_payment_acquirers
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_payment_acquirers.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_payment_acquirers.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_aade_tropoi_pliromis on gks_payment_acquirers.aade_tropos_pliromis_id=gks_aade_tropoi_pliromis.id_aade_tropos_pliromis
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY mysortorder,payment_acquirer_name";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-payment-acquirers-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τρόπου πληρωμής');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_payment_acquirers">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('HTML')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobutton', gks_lang('Κουμπί')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotable', gks_lang('Σχετικός<br>Πίνακας')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotypedm', '<span title="'.gks_lang('Σχετικοί Τύποι Αποστολής').'" class="tooltipster">'.gks_lang('Σx.Τύπο.Απο.').'</span>'); ?></th>        
        
        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodev', '<span title="'.gks_lang('Δοκιμαστικό Περιβάλλον').'" class="tooltipster">'.gks_lang('Δ.Π.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somethod', gks_lang('Μέθοδος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofees', '<span title="'.gks_lang('Ενεργοποίηση Κόστους').'" class="tooltipster">'.gks_lang('Κόστος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo gks_lang('Υπολογισμός');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaade', gks_lang('ΑΑΔΕ')); ?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'" class="tooltipster">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopay', '<span title="'.gks_lang('Εμφάνιση στις Πληρωμές').'" class="tooltipster">'.gks_lang('Πλη').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeshop', '<span title="'.gks_lang('Εμφάνιση στο eshop').'" class="tooltipster">'.gks_lang('eshop').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργός')); ?></th>   
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_payment_acquirer'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-payment-acquirers-item.php?id=<?php echo $row['id_payment_acquirer'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_payment_acquirer'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_payment_acquirer'];?>" data-model="gks_payment_acquirers"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['payment_acquirer_name'];?></td>
    <td class="mytdcml"><?php echo nl2br_gks($row['payment_acquirer_html']);?></td>
    <td class="mytdcml"><?php echo nl2br_gks($row['payment_acquirer_button_html']);?></td>
    
    <td nowrap class="mytdcml"><?php 
      $temp=trim_gks($row['payment_acquirer_table_name']);
      if (startwith($temp,'gks_payments_')) $temp=substr($temp, 13);
      echo $temp;
    ?></td>
    
    <td class="mytdcml"><?php echo $row['payment_acquirer_type'];?></td>
    <td class="mytdcml"><?php echo $row['payment_acquirer_type_dm'];?></td>
    
    
    <td class="mytdcm"><img src="img/<?php echo $row['payment_acquirer_env_test']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>    
    <td nowrap class="mytdcm"><?php echo $row['payment_acquirer_method'];?></td>
    <td class="mytdcm"><img src="img/<?php echo $row['payment_acquirer_fees_enabled']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>    
    <td class="mytdcml"><?php 
      $temp='';
      if ($row['pa_fees_domestic_fixed']!=0) $temp.= $row['pa_fees_domestic_fixed'].' | ';
      if ($row['pa_fees_domestic_percent']!=0) $temp.= $row['pa_fees_domestic_percent'].' | ';
      if ($row['pa_fees_international_fixed']!=0) $temp.= $row['pa_fees_international_fixed'].' | ';
      if ($row['pa_fees_international_percent']!=0) $temp.= $row['pa_fees_international_percent'].' | ';
      if (trim_gks($row['payment_acquirer_php_function_isok'])!='') $temp.= $row['payment_acquirer_php_function_isok'].' | ';
      if (trim_gks($row['payment_acquirer_php_function_calculate'])!='') $temp.= $row['payment_acquirer_php_function_calculate'].' | ';
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-3);
      echo $temp;
   ?></td>    
    <td class="mytdcml"><?php echo $row['aade_tropos_pliromis_descr'];?></td>    

    
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['mysortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['mysortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><img src="img/<?php echo $row['show_acc_pay']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    
    <td class="mytdcm"><img src="img/<?php echo $row['show_eshop']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    
    <td class="mytdcm"><img src="img/<?php echo $row['payment_acquirer_disabled']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#table_gks_payment_acquirers > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_payment_acquirers',mylist,'#table_gks_payment_acquirers > tbody');
    }
  });  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


