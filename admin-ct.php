<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();






db_open();
$ctid=0;if (isset($_GET['ctid'])) $ctid=intval($_GET['ctid']); 
if ($ctid < 10000) {
  $message=gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')';
  debug_mail(false,$message,$message);die($message); 
}
$sql_ct="select * 
from gks_custom_table 
where custom_table_disabled=0
and id_custom_table=".$ctid;
$result_ct = $db_link->query($sql_ct);        
if (!$result_ct) {debug_mail(false,'error sql',$sql_ct);die('sql error');}
if ($result_ct->num_rows!=1) {debug_mail(false,'record not found',$sql_ct);die('custom table not found ('.$ctid.')'); }
$row_ct = $result_ct->fetch_assoc();
$custom_table_descr=$row_ct['custom_table_descr'];
$custom_table_name=$row_ct['custom_table_name'];
$custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
$field_name_id_parent=$row_ct['field_name_id_parent'];
$field_name_id_current=$row_ct['field_name_id_current'];
$field_id='id_gks_customt_gks_ct_'.$ctid;


$my_page_title=$custom_table_descr;
$nav_active_array=array('dikamouobj','dikamouobj_table_'.$ctid);


stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, $custom_table_name,'view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, $custom_table_name,'edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare($custom_table_name,['from'=>'list']);

$filters = array();


$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  						array('name' => 'soid', 'field' => $custom_table_name.'.'.$field_id),

);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array();
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


$sql = "select SQL_CALC_FOUND_ROWS gks_ct_".$ctid.".*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit

".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (".$custom_table_name_real." as gks_ct_".$ctid."
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_ct_".$ctid.".cf_user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_ct_".$ctid.".cf_user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_ct_".$ctid.".".$field_id." desc";
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
    
$sortable_url='?ctid='.$ctid;
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}



include_once('_my_header_admin.php');
?>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-ct-item.php?ctid=<?php echo $ctid;?>&id=-1"><?php echo gks_lang('Προσθήκη νέας');?> <?php echo $custom_table_descr;?></a>

    </div>
  </div>
</div>





<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="ctid" value="<?php echo $ctid;?>"/>
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" id="table_<?php echo $custom_table_name;?>">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?ctid=<?php echo $ctid;?>">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
<?php
echo gks_custom_table_list_header($gks_custom_prepare, false,false);
?>
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row[$field_id];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-ct-item.php?ctid=<?php echo $ctid;?>&id=<?php echo $row[$field_id];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row[$field_id];;?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row[$field_id];?>" data-model="<?php echo $custom_table_name;?>"></i></td>
        </tr>      
      </table>
    </td>

  

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'delete',0);?>;

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

//  $('#table_gks_ads_campain > tbody').sortable({
//    handle: '.sortorder_handle',
//    update: function( event, ui ) {
//      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
//      gks_sortorder_obj('gks_ads_campain',mylist,'#table_gks_ads_campain > tbody');
//    }
//  });  



});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


