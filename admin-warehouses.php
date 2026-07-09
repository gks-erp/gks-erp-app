<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Αποθήκες');
$nav_active_array=array('manage','manage_warehouse');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_warehouses','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_warehouse_ids=gks_permission_user_condition($my_wp_user_id,'gks_warehouses','01');

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_warehouses','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_warehouses',['from'=>'list']);


$filters = array();

$filters[] = array(
    'name' => 'fcompany',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_warehouses.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_company as id, company_title as descr FROM gks_company order by company_sortorder,company_title",
);
$filters[] = array(
    'name' => 'fcompanysub',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Υποκατάστημα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_warehouses.company_sub_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_company_sub as id, company_sub_title as descr FROM gks_company_subs 
    order by gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title",
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),     'sql' => "warehouse_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργή'),  'sql' => "warehouse_disable <> 0"),
    ),
);

$filters[] = array(
    'name' => 'fppp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span title="'.gks_lang('Μπορεί ο πελάτης να παραλάβει προϊόντα').'">'.gks_lang('ΠΠΠ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "warehouse_can_pelatis_paralavei <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "warehouse_can_pelatis_paralavei = 0"),
    ),
);



$filters[] = array(
    'name' => 'fiscp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Διαφορετικός χώρος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'), 'sql' => "warehouse_is_company_place = 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'), 'sql' => "warehouse_is_company_place <> 0"),
    ),
);

$filters[] = array(
    'name' => 'fcity',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_warehouses.warehouse_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT warehouse_poli as descr, warehouse_poli as id FROM gks_warehouses WHERE warehouse_poli<>'' GROUP BY warehouse_poli ORDER BY warehouse_poli",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_warehouses.warehouse_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_warehouses.warehouse_nomos_id as id, gks_nomoi.nomos_descr as descr
FROM gks_warehouses LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos
WHERE (((gks_nomoi.id_nomos) Is Not Null))
GROUP BY gks_warehouses.warehouse_nomos_id, gks_nomoi.nomos_descr
ORDER BY gks_nomoi.nomos_descr"
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_warehouses.warehouse_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_warehouses.warehouse_country_id as id, gks_country.country_name as descr
FROM gks_warehouses LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country
WHERE (((gks_country.id_country) Is Not Null))
GROUP BY gks_warehouses.warehouse_country_id, gks_country.country_name
ORDER BY gks_country.country_name"
);


$filters[] = array(
    'name' => 'fpoint',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Στίγμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Έχει στίγμα'),     'sql' => "warehouse_map_latitude <> 0 or warehouse_map_longitude <>0"),
        array('value' => 2, 'text' => gks_lang('Δεν έχει στίγμα'), 'sql' => "warehouse_map_latitude = 0 and warehouse_map_longitude = 0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);







$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_warehouses.id_warehouse'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'socompany_sub', 'field' => 'gks_company_subs.company_sub_title'),
  						array('name' => 'soname', 'field' => 'gks_warehouses.warehouse_name'),
  						array('name' => 'sophone', 'field' => 'gks_warehouses.warehouse_phone'),
  						array('name' => 'soemail', 'field' => 'gks_warehouses.warehouse_email'),
  						array('name' => 'soodos', 'field' => 'gks_warehouses.warehouse_odos'),
  						array('name' => 'soperioxi', 'field' => 'gks_warehouses.warehouse_perioxi'),
  						array('name' => 'sopoli', 'field' => 'gks_warehouses.warehouse_poli'),
  						array('name' => 'sotk', 'field' => 'gks_warehouses.warehouse_tk'),
  						array('name' => 'socountry', 'field' => 'gks_country.country_name'),
  						array('name' => 'sonomos', 'field' => 'gks_nomoi.nomos_descr'),
  						array('name' => 'sodisable', 'field' => 'gks_warehouses.warehouse_disable'),
  						array('name' => 'soppp', 'field' => 'gks_warehouses.warehouse_can_pelatis_paralavei'),
              array('name' => 'sosort', 'field' => 'gks_warehouses.warehouse_sortorder'),   						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_company.company_title',
'gks_company_subs.company_sub_title',
'gks_warehouses.warehouse_name',
'gks_warehouses.warehouse_phone',
'gks_warehouses.warehouse_email',
'gks_warehouses.warehouse_odos',
'gks_warehouses.warehouse_perioxi',
'gks_warehouses.warehouse_poli',
'gks_warehouses.warehouse_tk',
'gks_country.country_name',
'gks_nomoi.nomos_descr',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_warehouses.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_company.company_title, gks_company_subs.company_sub_title, 
gks_country.country_name, gks_nomoi.nomos_descr
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((gks_warehouses 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_warehouses.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_warehouses.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_company ON gks_warehouses.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_warehouses.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos

where gks_warehouses.is_virtual=0 " .$where . $search_where;
if (count($perm_id_warehouse_ids)>0) $sql.=" and gks_warehouses.id_warehouse in (".implode(',',$perm_id_warehouse_ids).")";

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-warehouses-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας αποθήκης');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_warehouses">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Τίτλος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany_sub', gks_lang('Υποκατάστημα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soppp', '<span class="tooltipster" title="'.gks_lang('Μπορεί ο πελάτης να παραλάβει προϊόντα').'">'.gks_lang('ΠΠΠ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Χρώμα');?></th>        

        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soiscplace', '<span class="tooltipster" title="'.gks_lang('Διαφορετικός χώρος').'">'.gks_lang('ΔΧ').'</span>'); ?></th>        


        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', gks_lang('email')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', gks_lang('Οδός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soperioxi', gks_lang('Περιοχή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotk', gks_lang('ΤΚ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonomos', gks_lang('Νομός')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="7%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry', gks_lang('Χώρα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Στίγμα');?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>        

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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_warehouse'];?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-warehouses-item.php?id=<?php echo $row['id_warehouse'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_warehouse'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_warehouse'];?>" data-model="gks_warehouses"></i></td>
        </tr>      
      </table>
    </td>


    <td class="mytdcml"><?php echo $row['warehouse_name'];?></td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td class="mytdcml"><a href="admin-company-sub-item.php?id=<?php echo $row['company_sub_id'];?>"><?php echo $row['company_sub_title'];?></a></td>
    <td class="mytdcm"><?php echo myimg010r($row['warehouse_disable']);?></td> 
    <td nowrap class="mytdcm"><img src="img/<?php echo $row['warehouse_can_pelatis_paralavei']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>
    <td nowrap style="background-color: <?php echo $row['warehouse_color'];?>"></td>


    <td nowrap class="mytdcm"><img src="img/<?php echo $row['warehouse_is_company_place']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>
    
    
    <td class="mytdcml"><?php echo $row['warehouse_phone'];?></td>
    <td class="mytdcml"><?php echo $row['warehouse_email'];?></td>
    <td class="mytdcml"><?php echo $row['warehouse_odos'].' '.$row['warehouse_arithmos'];?></td>
    <td class="mytdcml"><?php echo $row['warehouse_perioxi'];?></td>
    <td class="mytdcml"><?php echo $row['warehouse_poli'];?></td>
    <td class="mytdcml"><?php echo $row['warehouse_tk'];?></td>
    <td class="mytdcml"><?php echo $row['nomos_descr'];?></td>
    <td class="mytdcml"><?php echo $row['country_name'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['warehouse_map_latitude']==0 and $row['warehouse_map_longitude']==0) {
        $pos_warehouse=0;
      } else {
        $pos_warehouse=1;
      }?>
      <img src="img/<?php echo $pos_warehouse;?>.png" border="0" width="16"></td>
      </td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['warehouse_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['warehouse_sortorder'];?></span>
    </td>
<?php } ?>

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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });

  $('#table_gks_warehouses > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_warehouses',mylist,'#table_gks_warehouses > tbody');
    }
  });
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

