<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Δωματία');
$nav_active_array=array('hotel','hotel_rooms');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_room','edit',0);


$user_hotels=gks_get_hotels_list();




$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_room',['from'=>'list']);


$filters = array();

if (count($user_hotels)>=1) {
  $vals=array();
  foreach ($user_hotels as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_hotel_room.hotel_id=".$value['id']);
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


$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_room.room_status = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -12, 'text' => getHotelRoomTypeStatusDescr('disable'),      'sql' => "gks_hotel_room.room_status='disable'"),
        array('value' => -11, 'text' => getHotelRoomTypeStatusDescr('available'),    'sql' => "gks_hotel_room.room_status='available'"),
        array('value' => -13, 'text' => getHotelRoomTypeStatusDescr('renovation'),   'sql' => "gks_hotel_room.room_status='renovation'"),
    ),
    'sql' => "SELECT room_status as descr, room_status as id 
    FROM gks_hotel_room where room_status is not null and room_status not in ('available','disable','renovation') 
    GROUP BY room_status ORDER BY room_status",
);
$filters[] = array(
    'name' => 'ffloor',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Όροφος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_room.hotel_floor_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hotel_room.hotel_floor_id as id , gks_hotel_floor.floor_descr as descr
    FROM gks_hotel_room LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor
    WHERE (((gks_hotel_floor.id_hotel_floor) Is Not Null))
    ".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_room.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : '')."
    GROUP BY gks_hotel_room.hotel_floor_id, gks_hotel_floor.floor_descr
    ORDER BY gks_hotel_floor.sort_order,gks_hotel_floor.floor_descr;",
);



$filters[] = array(
    'name' => 'froom_type_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_room.hotel_room_type_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hotel_room.hotel_room_type_id as id, gks_hotel_room_type.room_type_descr as descr
    FROM gks_hotel_room LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
    WHERE (((gks_hotel_room_type.id_hotel_room_type) Is Not Null))
    ".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : '')."
    GROUP BY gks_hotel_room.hotel_room_type_id, gks_hotel_room_type.room_type_descr
    ORDER BY gks_hotel_room_type.room_type_sortorder, gks_hotel_room_type.room_type_descr",
);

$filters[] = array(
    'name' => 'froom_type_fix_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ομάδα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_room_type.hotel_room_type_fix_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hotel_room_type.hotel_room_type_fix_id as id, gks_hotel_room_type_fix.room_type_fix_descr as descr
    FROM (gks_hotel_room LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix
    WHERE (((gks_hotel_room_type_fix.id_hotel_room_type_fix) Is Not Null))
    ".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_room.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : '')."
    GROUP BY gks_hotel_room_type.hotel_room_type_fix_id, gks_hotel_room_type_fix.room_type_fix_descr
    ORDER BY gks_hotel_room_type_fix.room_type_fix_descr;",
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);



$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_hotel_room.id_hotel_room'),
  						array('name' => 'sostatus', 'field' => 'gks_hotel_room.room_status'),
  						array('name' => 'sodescr', 'field' => 'gks_hotel_room.room_descr'),
  						array('name' => 'sohotel', 'field' => 'gks_hotel.hotel_title'),
  						array('name' => 'soprice', 'field' => 'gks_hotel_room_type.room_type_price'),
  						array('name' => 'sotype', 'field' => 'gks_hotel_room_type.room_type_descr'),
  						array('name' => 'sogroup', 'field' => 'gks_hotel_room_type_fix.room_type_fix_descr'),
  						array('name' => 'sofloor', 'field' => 'gks_hotel_floor.floor_descr'),
              array('name' => 'sototal_visitors', 'field' => 'room_type_visitors_max'),
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hotel_room.room_descr',
'gks_hotel.hotel_title',
'gks_hotel_room_type.room_type_descr',
'gks_hotel_room_type_fix.room_type_fix_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_hotel_floor.floor_descr',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hotel_room.*,
gks_hotel_room_type.room_type_descr, gks_hotel_room_type_fix.room_type_fix_descr, gks_hotel_room_type.room_type_price,
room_type_visitors,room_type_visitors_childs,room_type_visitors_max,
gks_hotel_floor.floor_descr,
gks_hotel.hotel_title,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((gks_hotel_room 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_hotel ON gks_hotel_room.hotel_id = gks_hotel.id_hotel)
LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_room.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_room.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix)
LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor
where 1=1 ".$where . $search_where;
if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room.hotel_id in (".implode(',',$perm_id_hotel_ids).")";      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_hotel_room.room_sortorder, gks_hotel_room.room_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-hotel-room-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου δωματίου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_hotel_room">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohotel', gks_lang('Ξενοδοχείο')); ?></th>  
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="12%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofloor', gks_lang('Όροφος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Τιμή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sototal_visitors', '<span class="tooltipster" title="'.gks_lang('Επισκέπτες ανά δωμάτιο: Ενήλικες / Παιδιά / Μέγιστος αριθμός').'">'.gks_lang('Επισκέπτες δωματίου').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogroup', gks_lang('Ομάδα')); ?></th>        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_hotel_room'];?>">
    <th scope="row" nowrap class="mytdcm p-0"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-hotel-room-item.php?id=<?php echo $row['id_hotel_room'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_hotel_room'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_hotel_room'];?>" data-model="gks_hotel_room"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcm"><span class="room_status_<?php echo $row['room_status'];?>"><?php echo getHotelRoomTypeStatusDescr($row['room_status']);?></span></td>
    <td class="mytdcml"><?php echo $row['hotel_title'];?></td>
    <td ><?php echo $row['room_descr'];?></td>
    <td ><?php echo $row['floor_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo myCurrencyFormat($row['room_type_price']);?></td>

    <td nowrap class="mytdcm"><?php 
      $out='';
      if ($row['room_type_visitors']>0) $out.= $row['room_type_visitors'].'x<i class="fa fa-male"></i>'. ' / ';
      if ($row['room_type_visitors_childs']>0) $out.= $row['room_type_visitors_childs'].'x<i class="fa fa-child" style="font-size:70%;"></i>'. ' / ';
      if ($row['room_type_visitors_max']>0 and $row['room_type_visitors_childs']>0) $out.= $row['room_type_visitors_max'] .' / ';
      if ($out!='') $out=substr($out, 0, strlen($out)-3);
      echo $out;
      ?></td>
    
    <td class="mytdcml"><?php echo $row['room_type_descr'];?></td>
    <td class="mytdcml"><?php echo $row['room_type_fix_descr'];?></td>

<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['room_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['room_sortorder'];?></span>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','delete',0);?>;

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

  
  $('#table_gks_hotel_room > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_hotel_room',mylist,'#table_gks_hotel_room > tbody');
    }
  }); 
  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


