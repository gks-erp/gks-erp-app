<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Άδειες Χρήσης');
$nav_active_array=array('license','license_efs_license');


db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }



$filters = array();

$filters[] = array(
			'name' => 'fmydate_add',
			'class' => 'filterselectbox ui-state-default ui-corner-all',
			'style' => '',
		  'title' => gks_lang('Προσθήκη'),
			'has_custom_date' => true,
			'field' => 'gks_efs_license.mydate_add', 
			'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
      'vals' => gks_filter_date_vals(['field'=>'gks_efs_license.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);

$filters[] = array(
			'name' => 'flast_ping',
			'class' => 'filterselectbox ui-state-default ui-corner-all',
			'style' => '',
			'title' => 'Last Ping',
			'has_custom_date' => true,
			'field' => 'gks_efs_license.last_ping', 
			'has_custom_default' => 1,
//		'mywherepos'=>1,
      'vals' => gks_filter_date_vals(['field'=>'gks_efs_license.last_ping','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_efs_license.id_lic'),
  						array('name' => 'somydate_add', 'field' => 'gks_efs_license.mydate_add'),
  						array('name' => 'soemail', 'field' => 'gks_efs_license.email'),
  						array('name' => 'soquantity', 'field' => 'gks_efs_license.quantity'),
  						array('name' => 'solast_ping', 'field' => 'gks_efs_license.last_ping'),
  						array('name' => 'souser_id_edit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
            );

$search_fields = array(
  GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
  GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
  'gks_efs_license.email',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_efs_license.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
FROM (gks_efs_license 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_efs_license.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_efs_license.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_efs_license.mydate_add DESC";
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



include_once('_my_header_admin.php');
?>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary" href="admin-efs-license-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας άδειας χρήσης');?></a>
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
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_add', gks_lang('Προσθήκη')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', 'email'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soquantity', gks_lang('Ποσότητα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solast_ping', 'Last Ping'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_id_edit', gks_lang('Χρήστης')); ?></th>        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-efs-license-item.php?id=<?php echo $row['id_lic'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_lic'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_lic'];?>" data-model="gks_efs_license"></i></td>
        </tr>      
      </table>
    </td>
    <td class="mytdcm" nowrap ><?php if (isset($row['mydate_add'])) echo mb_substr( getWeekDayName(showDate(strtotime($row['mydate_add']), 'w', 1)),0,2).' '. showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap style="font-weight: bold;"><?php echo $row['email'];?></td>
    <td class="mytdcm" nowrap style="text-align: center !important;"><?php echo $row['quantity'];?></td>
    <td class="mytdcm" nowrap ><?php if (isset($row['last_ping'])) echo mb_substr( getWeekDayName(showDate(strtotime($row['last_ping']), 'w', 1)),0,2).' '. showDate(strtotime($row['last_ping']), 'd/m/Y H:i:s',1);?></td>
    <td class="mytdcm"><a href="/wp-admin/user-edit.php?user_id=<?php echo $row['user_id_edit'];?>"><?php echo $row['gks_nickname_edit'];?></td>
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

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#fmydate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#flast_ping-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#flast_ping-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      
      if (v==-2) { //is_custom_date
        if (sname == 'fmydate_add' || sname=='flast_ping') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fmydate_add' || sname=='flast_ping') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });



});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


