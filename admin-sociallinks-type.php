<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Τύποι Συνδέσμων Κοινωνικών Δικτύων');
$nav_active_array=array('manage','manage_email','manage_sociallinks_type');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sociallinks_type','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_sociallinks_type','edit',0);






$filters = array();



$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργός'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργός'),     'sql' => "gks_sociallinks_type.sociallinks_type_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργός'),  'sql' => "gks_sociallinks_type.sociallinks_type_disable <> 0"),
    ),
);
         





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_sociallinks_type.id_sociallinks_type'),
  						array('name' => 'sodescr', 'field' => 'gks_sociallinks_type.sociallinks_type_descr'),
  						array('name' => 'soicon', 'field' => 'gks_sociallinks_type.sociallinks_type_icon'),
  						array('name' => 'soiconemail', 'field' => 'gks_sociallinks_type.sociallinks_type_icon_email'),
  						array('name' => 'sosort', 'field' => 'gks_sociallinks_type.sociallinks_type_sortorder'),
  						array('name' => 'sodisable', 'field' => 'gks_sociallinks_type.sociallinks_type_disable'),

            );

$search_fields = array(
'gks_sociallinks_type.sociallinks_type_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'sociallinks_type_comments',
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
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;


$sql ="SELECT SQL_CALC_FOUND_ROWS gks_sociallinks_type.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
                

FROM (gks_sociallinks_type 

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_sociallinks_type.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_sociallinks_type.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID


where 1=1 ".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_sociallinks_type.sociallinks_type_sortorder, gks_sociallinks_type.sociallinks_type_descr";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql;die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$row_array=array();
$ids=array();
while ($row = $result->fetch_assoc()) {
  $row_array[]=$row;
  $ids[]=$row['id_sociallinks_type'];
}

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


//print '<pre>';print_r($list1);print_r($list2);die();
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
      <a class="btn btn-primary gks_add_new_record" href="admin-sociallinks-type-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τύπου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_sociallinks_type">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soicon', gks_lang('Icon')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soiconemail', gks_lang('Icon<br>email')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργός')); ?></th>   

<?php if ($perm_edit) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
           
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    
    foreach ($row_array as $row) {
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_sociallinks_type'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-sociallinks-type-item.php?id=<?php echo $row['id_sociallinks_type'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_sociallinks_type'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_sociallinks_type'];?>" data-model="gks_sociallinks_type"></i></td>
        </tr>      
      </table>
    </td>
    <td class="mytdcml"><div_gks><?php echo $row['sociallinks_type_descr'];?></div_gks></td>
    <td class="mytdcm"><?php echo $row['sociallinks_type_icon'];?></td>
    <td class="mytdcm"><?php echo $row['sociallinks_type_icon_email'];?></td>
    <td class="mytdcm"><?php echo myimg010r($row['sociallinks_type_disable']);?></td> 
    
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sociallinks_type_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sociallinks_type_sortorder'];?></span>
    </td>
<?php } ?>

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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sociallinks_type','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sociallinks_type','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sociallinks_type','delete',0);?>;

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
  
  $('#table_gks_sociallinks_type > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_sociallinks_type',mylist,'#table_gks_sociallinks_type > tbody');
    }
  }); 
  
  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


