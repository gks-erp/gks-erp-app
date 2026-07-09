<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Εργασίες');
$nav_active_array=array('production','production_ergasies');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_production_ergasies_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_ergasies','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_production_ergasies',['from'=>'list']);


$filters = array();
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
	array('name' => 'soid', 'field' => 'gks_production_ergasies.id_production_ergasia'),
	array('name' => 'soproduction_ergasia_descr', 'field' => 'gks_production_ergasies.production_ergasia_descr'),
	array('name' => 'socce', 'field' => 'postacount.cce'),
	array('name' => 'socc_n', 'field' => 'ergasies_n.cc_n'),
	array('name' => 'socc_mustdone', 'field' => 'ergasies_mustdone.cc_mustdone'),
	array('name' => 'socat', 'field' => 'ergasies_cateidos.cc_cateidos'),
	array('name' => 'soeidos', 'field' => 'ergasies_eidos.cc_eidos'),
	array('name' => 'sosort', 'field' => 'gks_production_ergasies.production_ergasia_sortorder'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'production_ergasia_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_production_ergasies.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
postacount.cce, ergasies_n.cc_n, ergasies_mustdone.cc_mustdone,
ergasies_cateidos.cc_cateidos,ergasies_eidos.cc_eidos
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((gks_production_ergasies
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_production_ergasies.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_production_ergasies.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN (
  SELECT gks_production_posta_ergasies.production_ergasia_id, Count(gks_production_posta_ergasies.production_posto_id) AS cce
  FROM gks_production_posta_ergasies 
  LEFT JOIN gks_production_posta ON gks_production_posta_ergasies.production_posto_id = gks_production_posta.id_production_posto
  WHERE gks_production_posta.id_production_posto Is Not Null
  GROUP BY gks_production_posta_ergasies.production_ergasia_id
) as postacount on gks_production_ergasies.id_production_ergasia = postacount.production_ergasia_id)
LEFT JOIN (
  SELECT ergasia_id, Count(id_production_ergasia_mustdone) AS cc_n
  FROM gks_production_ergasies_mustdone
  GROUP BY ergasia_id
) as ergasies_n on gks_production_ergasies.id_production_ergasia = ergasies_n.ergasia_id)
LEFT JOIN (
  SELECT ergasia_mustdone_id, Count(id_production_ergasia_mustdone) AS cc_mustdone
  FROM gks_production_ergasies_mustdone
  GROUP BY ergasia_mustdone_id
) as ergasies_mustdone on gks_production_ergasies.id_production_ergasia = ergasies_mustdone.ergasia_mustdone_id)
LEFT JOIN (
  SELECT production_ergasia_id, Count(id_production_ergasies_eidoscat) AS cc_cateidos
  FROM gks_production_ergasies_eidoscat
  GROUP BY production_ergasia_id
) as ergasies_cateidos on gks_production_ergasies.id_production_ergasia = ergasies_cateidos.production_ergasia_id)
LEFT JOIN (
  SELECT production_ergasia_id, Count(id_production_ergasies_eidos) AS cc_eidos
  FROM gks_production_ergasies_eidos
  GROUP BY production_ergasia_id
) as ergasies_eidos on gks_production_ergasies.id_production_ergasia = ergasies_eidos.production_ergasia_id



where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-production-ergasies-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας εργασίας');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_production_ergasies">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduction_ergasia_descr', gks_lang('Πόστο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_n', gks_lang('Προαπαιτούμενες')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_mustdone', gks_lang('Εξαρτώμενες')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socce', gks_lang('Πόστα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socat', gks_lang('Κατηγορίες<br>Ειδών')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeidos', gks_lang('Είδη')); ?></th>        
    <?php if ($perm_production_ergasies_edit) {?>
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_production_ergasia'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-production-ergasies-item.php?id=<?php echo $row['id_production_ergasia'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_production_ergasia'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_production_ergasia'];?>" data-model="gks_production_ergasies"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['production_ergasia_descr'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['cc_n'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['cc_mustdone'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['cce'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['cc_cateidos'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['cc_eidos'];?></td>
    <?php if ($perm_production_ergasies_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['production_ergasia_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['production_ergasia_sortorder'];?></span>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#table_gks_production_ergasies > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_production_ergasies',mylist,'#table_gks_production_ergasies > tbody');
    }
  });
    
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

  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


