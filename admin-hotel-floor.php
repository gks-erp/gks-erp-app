<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

gks_permission_user_must_login_page();



$my_page_title=gks_lang('Όροφοι');
$nav_active_array=array('hotel','hotel_floors');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_floor','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_floor','edit',0);

$user_hotels=gks_get_hotels_list();

//print '<pre>';print_r($user_hotels);//die();

//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/zodomus_2023_09_25_14_10_32_1695651032-1.txt');

//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/stage_new_d.json');
//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/stage_modified1_d.json');
//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/stage_modified2_d.json');
//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/stage_cancelled_d.json');
//$temp=file_get_contents('/var/www/php/test.easyfilesselection.com/stage_mob3_d.json');

//$params=array('channelId' => 1,'propertyId' => '951');
//$ret = gks_hotel_cm_reservation_parse($temp, $params);
//print '<pre>';print_r($ret);die();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_floor',['from'=>'list']);


$filters = array();

if (count($user_hotels)>=1) {
  $vals=array();
  foreach ($user_hotels as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_hotel_floor.hotel_id=".$value['id']);
  } 
  $filters[] = array(
    'name' => 'fhotel_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ξενοδοχείο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => $vals,
  );  
}

         
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_hotel_floor.id_hotel_floor'),
  						array('name' => 'sohotel', 'field' => 'gks_hotel.hotel_title'),
  						array('name' => 'sodescr', 'field' => 'gks_hotel_floor.floor_descr'),
  						array('name' => 'socc', 'field' => 'cc_rooms.cc'),
  						array('name' => 'soccv', 'field' => 'cc_visitors.ccv'),
  						array('name' => 'sosort', 'field' => 'gks_hotel_floor.sort_order'),
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hotel_floor.floor_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_hotel.hotel_title',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hotel_floor.*,cc_rooms.cc,cc_visitors.ccv,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_hotel.hotel_title
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((gks_hotel_floor 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_floor.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_floor.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_hotel ON gks_hotel_floor.hotel_id = gks_hotel.id_hotel)
LEFT JOIN (
  SELECT gks_hotel_room.hotel_floor_id, Count(*) AS cc 
  FROM gks_hotel_room GROUP BY gks_hotel_room.hotel_floor_id
) AS cc_rooms ON gks_hotel_floor.id_hotel_floor = cc_rooms.hotel_floor_id)
LEFT JOIN (
  SELECT gks_hotel_room.hotel_floor_id, Sum(gks_hotel_room_type.room_type_visitors) AS ccv
  FROM gks_hotel_room LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
  GROUP BY gks_hotel_room.hotel_floor_id
) as cc_visitors ON gks_hotel_floor.id_hotel_floor = cc_visitors.hotel_floor_id
where 1=1 ".$where . $search_where;
if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_floor.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_hotel_floor.sort_order, gks_hotel_floor.floor_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-hotel-floor-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου ορόφου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_hotel_floor">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohotel', gks_lang('Ξενοδοχείο')); ?></th>  
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>  
        <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc', gks_lang('Δωμάτια')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccv', gks_lang('Επισκέπτες')); ?></th>        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_hotel_floor'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-hotel-floor-item.php?id=<?php echo $row['id_hotel_floor'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_hotel_floor'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_hotel_floor'];?>" data-model="gks_hotel_floor"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['hotel_title'];?></td>
    <td class="mytdcml"><?php echo $row['floor_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['cc'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['ccv'];?></td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sort_order'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sort_order'];?></span>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_floor','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_floor','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_floor','delete',0);?>;

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
  
  $('#table_gks_hotel_floor > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_hotel_floor',mylist,'#table_gks_hotel_floor > tbody');
    }
  }); 

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


