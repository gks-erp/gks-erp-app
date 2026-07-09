<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Τύποι Δωματίων');
$nav_active_array=array('hotel','hotel_room_types');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room_type','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_room_type','edit',0);

$user_hotels=gks_get_hotels_list();



$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_room_type',['from'=>'list']);


$filters = array();

if (count($user_hotels)>=1) {
  $vals=array();
  foreach ($user_hotels as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_hotel_room_type.hotel_id=".$value['id']);
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
    'field'  => "gks_hotel_room_type.room_type_status = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -12, 'text' => getHotelRoomTypeStatusDescr('disable'),      'sql' => "gks_hotel_room_type.room_type_status='disable'"),
        array('value' => -11, 'text' => getHotelRoomTypeStatusDescr('available'),     'sql' => "gks_hotel_room_type.room_type_status='available'"),
        array('value' => -13, 'text' => getHotelRoomTypeStatusDescr('renovation'),    'sql' => "gks_hotel_room_type.room_type_status='renovation'"),
    ),
    'sql' => "SELECT room_type_status as descr, room_type_status as id 
    FROM gks_hotel_room_type where room_type_status is not null and room_type_status not in ('available','disable','renovation') 
    GROUP BY room_type_status ORDER BY room_type_status",
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
    'sql' => "SELECT gks_hotel_room_type_fix.id_hotel_room_type_fix as id, gks_hotel_room_type_fix.room_type_fix_descr as descr
    FROM gks_hotel_room_type LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix
    WHERE (((gks_hotel_room_type_fix.id_hotel_room_type_fix) Is Not Null))
    ".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : '')."
    GROUP BY gks_hotel_room_type_fix.id_hotel_room_type_fix, gks_hotel_room_type_fix.room_type_fix_descr
    ORDER BY gks_hotel_room_type_fix.room_type_fix_descr;",
);



$filters[] = array(
    'name' => 'feidos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είδος'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "gks_hotel_room_type.product_id = %V%",
    'vals' => array(
      array('value' => -100, 'text' => gks_lang('Χωρίς είδος'),     'sql' => "gks_hotel_room_type.product_id = 0"),
    ),
    'sql' =>'SELECT gks_eshop_products.id_product as id, gks_eshop_products.product_descr as descr
    FROM gks_hotel_room_type LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
    WHERE (((gks_eshop_products.id_product) Is Not Null))
    GROUP BY gks_eshop_products.id_product, gks_eshop_products.product_descr
    ORDER BY gks_eshop_products.product_descr',
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);



$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_hotel_room_type.id_hotel_room_type'),
  						array('name' => 'sostatus', 'field' => 'gks_hotel_room_type.room_type_status'),
  						array('name' => 'sodescr', 'field' => 'gks_hotel_room_type.room_type_descr'),
  						array('name' => 'sohotel', 'field' => 'gks_hotel.hotel_title'),
  						array('name' => 'soprice', 'field' => 'gks_hotel_room_type.room_type_price'),
  						array('name' => 'sotype', 'field' => 'gks_hotel_room_type_fix.room_type_fix_descr'),
  						array('name' => 'socc', 'field' => 'cc_rooms.cc'),
  						array('name' => 'sototal_visitors', 'field' => 'room_type_visitors_max'),
  						array('name' => 'sototal_visitors_rooms', 'field' => 'total_visitors_max'),
  						array('name' => 'soeidos', 'field' => 'gks_eshop_products.product_descr'),
  						

            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hotel_room_type.room_type_descr',
'gks_hotel_room_type_fix.room_type_fix_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
//'gks_hotel_floor.floor_descr',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hotel_room_type.*, cc_rooms.cc, 
cc_rooms.cc * room_type_visitors as total_visitors,
cc_rooms.cc * room_type_visitors_childs as total_visitors_childs,
cc_rooms.cc * room_type_visitors_max as total_visitors_max,
cc_rooms.cc * room_type_child_kounies as total_child_kounies_max,
cc_rooms.cc * room_type_extra_beds as total_extra_beds_max,
gks_hotel.hotel_title,
gks_hotel_room_type_fix.room_type_fix_descr,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_eshop_products.id_product, gks_eshop_products.product_descr
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((gks_hotel_room_type 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN (
  SELECT gks_hotel_room.hotel_room_type_id, Count(*) AS cc 
  FROM gks_hotel_room GROUP BY gks_hotel_room.hotel_room_type_id
)  AS cc_rooms ON gks_hotel_room_type.id_hotel_room_type = cc_rooms.hotel_room_type_id) 
LEFT JOIN gks_hotel ON gks_hotel_room_type.hotel_id = gks_hotel.id_hotel)
LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_room_type.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_room_type.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
where 1=1 ".$where . $search_where;
if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")";      
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_hotel_room_type.room_type_sortorder, gks_hotel_room_type.room_type_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-hotel-room-type-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τύπου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_hotel_room_type">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohotel', gks_lang('Ξενοδοχείο')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Τιμή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Ομάδα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc', gks_lang('Δωμάτια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sototal_visitors', gks_lang('Επισκέπτες δωματίου')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sototal_visitors_rooms', '<span class="tooltipster" title="'.gks_lang('Σύνολο επισκεπτών από όλα τα δωμάτια').'">'.gks_lang('Σύνολο<br>Επισκεπτών').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeidos', gks_lang('Είδος')); ?></th>        

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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_hotel_room_type'];?>">
    <th scope="row" nowrap class="mytdcm p-0"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-hotel-room-type-item.php?id=<?php echo $row['id_hotel_room_type'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_hotel_room_type'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_hotel_room_type'];?>" data-model="gks_hotel_room_type"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcm"><span class="room_type_status_<?php echo $row['room_type_status'];?>"><?php echo getHotelRoomTypeStatusDescr($row['room_type_status']);?></span></td>
    <td        class="mytdcml"><?php echo $row['hotel_title'];?></td>
    <td        class="mytdcml"><?php echo $row['room_type_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo myCurrencyFormat($row['room_type_price']);?></td>
    <td        class="mytdcml"><?php echo $row['room_type_fix_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['cc'];?></td>
    <td nowrap class="mytdcm"><?php 
      $out='';
      if ($row['room_type_visitors']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός ενηλίκων').'">'.$row['room_type_visitors'].'x<i class="fa fa-male"></i></span>'.' / ';
      if ($row['room_type_visitors_childs']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός παιδιών').'">'.$row['room_type_visitors_childs'].'x<i class="fa fa-child" style="font-size:80%;"></i></span>'.' / ';
      if ($row['room_type_visitors_max']>0 and $row['room_type_visitors_childs']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός επισκεπτών').'">'.$row['room_type_visitors_max'].'</span>'.' / ';
      if ($out!='') $out=substr($out, 0, strlen($out)-3);
      
      $second_line='';
      if ($row['room_type_child_kounies']>0) $second_line.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός βρεφικών κρεβατιών').'">'.$row['room_type_child_kounies'] .'x<i class="fa fa-box" style="font-size:90%;"></i></span>';
      if ($row['room_type_extra_beds']>0) $second_line.=($second_line=='' ? '' : ' ').'<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός επιπλέον κρεβατιών').'">'.$row['room_type_extra_beds'].'x<i class="fa fa-bed" title="'.gks_lang('Μέγιστος αριθμός επιπλέον κρεβατιών').'"></i>';
      if ($out!='') $second_line='<br>'.$second_line;
      $out.=$second_line;
       
      echo $out;
      ?></td>
    <td nowrap class="mytdcm"><?php 
      $out='';
      if ($row['total_visitors']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός ενηλίκων').'">'.$row['total_visitors'].'x<i class="fa fa-male"></i></span>'.' / ';
      if ($row['total_visitors_childs']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός παιδιών').'">'.$row['total_visitors_childs'].'x<i class="fa fa-child" style="font-size:70%;"></i></span>'.' / ';
      if ($row['total_visitors_max']>0 and $row['total_visitors_childs']>0) $out.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός επισκεπτών').'">'.$row['total_visitors_max'].'</span>'.' / ';
      if ($out!='') $out=substr($out, 0, strlen($out)-3);
      
      $second_line='';
      if ($row['total_child_kounies_max']>0) $second_line.= '<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός βρεφικών κρεβατιών').'">'.$row['total_child_kounies_max'] .'x<i class="fa fa-box" style="font-size:90%;"></i></span>';
      if ($row['total_extra_beds_max']>0) $second_line.=($second_line=='' ? '' : ' ').'<span class="tooltipster" title="'.gks_lang('Μέγιστος αριθμός επιπλέον κρεβατιών').'">'.$row['total_extra_beds_max'].'x<i class="fa fa-bed" title="'.gks_lang('Μέγιστος αριθμός επιπλέον κρεβατιών').'"></i>';
      if ($out!='') $second_line='<br>'.$second_line;
      $out.=$second_line;
      
      echo $out;
      ?></td>
    <td class="mytdcml"><?php
      if (isset($row['id_product'])) echo '<a href="admin-products-item.php?id='.$row['id_product'].'">'.$row['product_descr'].'</a>';
    ?></td>

<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['room_type_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['room_type_sortorder'];?></span>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','delete',0);?>;

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

  $('#table_gks_hotel_room_type > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_hotel_room_type',mylist,'#table_gks_hotel_room_type > tbody');
    }
  });
  


<?php
	echo $filter['script'];
?> 
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


