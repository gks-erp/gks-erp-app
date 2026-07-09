<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Ομάδες Επαφών');
$nav_active_array=array('manage','manage_users_groups');

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_groups','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






$gks_custom_prepare = gks_custom_table_item_prepare('gks_users_groups',['from'=>'list']);

$filters = array();


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
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "gks_users_groups.group_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργό'),  'sql' => "gks_users_groups.group_disable <> 0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_users_groups.id_users_group'),
  						array('name' => 'sotitle', 'field' => 'gks_users_groups.group_title'),
  						array('name' => 'soparent', 'field' => 'fullpath'),
  						array('name' => 'soadd', 'field' => 'gks_users_groups.group_date_add'),
  						array('name' => 'soccc', 'field' => 'ccusers.ccc'),
  						array('name' => 'sodisable', 'field' => 'gks_users_groups.group_disable'),
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
  'gks_users_groups.group_title',
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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_users_groups.*, ccusers.ccc,
ug2.group_title AS gt2, 
ug3.group_title AS gt3, 
ug4.group_title AS gt4, 
ug5.group_title AS gt5, 
ug6.group_title AS gt6, 
ug7.group_title AS gt7, 
ug8.group_title AS gt8, 
ug9.group_title AS gt9, 
ug10.group_title AS gt10, 


ug2.id_users_group AS id2, 
ug3.id_users_group AS id3, 
ug4.id_users_group AS id4, 
ug5.id_users_group AS id5,
ug6.id_users_group AS id6,
ug7.id_users_group AS id7,
ug8.id_users_group AS id8,
ug9.id_users_group AS id9,
ug10.id_users_group AS id10,

CONCAT_WS('\\\\',
                 ug10.group_title,
                 ug9.group_title,
                 ug8.group_title,
                 ug7.group_title,
                 ug6.group_title,
                 ug5.group_title,
                 ug4.group_title,
                 ug3.group_title,
                 ug2.group_title,
                 gks_users_groups.group_title) as fullpath,
CONCAT_WS('\\\\',
                 ug10.group_title,
                 ug9.group_title,
                 ug8.group_title,
                 ug7.group_title,
                 ug6.group_title,
                 ug5.group_title,
                 ug4.group_title,
                 ug3.group_title,
                 ug2.group_title) as dirpath
".$gks_custom_prepare['sql_all_list_sele']."                 
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((((((gks_users_groups 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN (
  SELECT gks_users_groups_users.group_id, Count(gks_users_groups_users.user_id) AS ccc
  FROM gks_users_groups_users
  GROUP BY gks_users_groups_users.group_id
) AS ccusers ON gks_users_groups.id_users_group = ccusers.group_id)
LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group) 
LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group

where 1=1 " .$where . $search_where;

if (empty($sorted['sql'])) {
	$query .= " ORDER BY fullpath";
} else {
	$query .= " ORDER BY " . $sorted['sql'];
}
$query .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $query;
//die();
	
$result = $db_link->query($query);        
if (!$result) debug_mail(false,'error sql',$query);
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
      <a class="btn btn-primary gks_add_new_record" href="admin-usersgroups-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας ομάδας επαφών');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparent', gks_lang('Πλήρης διαδρομή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccc', gks_lang('Πλήθος<br>Επαφών')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo gks_lang('Συνολικό<br>Πλήθος<br>Επαφών');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"><?php echo gks_lang('Σχόλιο');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadd', gks_lang('Ημερομηνία<br>Προσθήκης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-usersgroups-item.php?id=<?php echo $row['id_users_group'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_users_group'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_users_group'];?>" data-model="gks_users_groups"></i></td>
        </tr>      
      </table>
    </td>
    
    <td class="mytdcml"><?php echo $row['fullpath'];?></td>
    <td class="mytdcml"><?php echo $row['group_title'];?></td>
    <td class="mytdcm" nowrap><?php if ($row['ccc']>0) echo number_format($row['ccc'], 0, ',', '.');?></td>  
    <td class="mytdcm" nowrap><?php
      $gu_num=0;
      
      $sql="SELECT 
      ug1.id_users_group AS gid1, 
      ug2.id_users_group AS gid2, 
      ug3.id_users_group AS gid3, 
      ug4.id_users_group AS gid4, 
      ug5.id_users_group AS gid5,
      ug6.id_users_group AS gid6,
      ug7.id_users_group AS gid7,
      ug8.id_users_group AS gid8,
      ug9.id_users_group AS gid9,
      ug10.id_users_group AS gid10
      FROM ((((((((gks_users_groups AS ug1 
      LEFT JOIN gks_users_groups AS ug2 ON ug1.id_users_group = ug2.group_parent_id) 
      LEFT JOIN gks_users_groups AS ug3 ON ug2.id_users_group = ug3.group_parent_id) 
      LEFT JOIN gks_users_groups AS ug4 ON ug3.id_users_group = ug4.group_parent_id) 
      LEFT JOIN gks_users_groups AS ug5 ON ug4.id_users_group = ug5.group_parent_id)
      LEFT JOIN gks_users_groups AS ug6 ON ug5.id_users_group = ug6.group_parent_id)
      LEFT JOIN gks_users_groups AS ug7 ON ug6.id_users_group = ug7.group_parent_id)
      LEFT JOIN gks_users_groups AS ug8 ON ug7.id_users_group = ug8.group_parent_id)
      LEFT JOIN gks_users_groups AS ug9 ON ug8.id_users_group = ug9.group_parent_id)
      LEFT JOIN gks_users_groups AS ug10 ON ug9.id_users_group = ug10.group_parent_id
      
      
      where ug1.id_users_group=".$row['id_users_group'];
      $result_gu = $db_link->query($sql);        
      if (!$result_gu) {
        debug_mail(false,'error sql',$sql);
        die('sql error');
      }
      $gu_in='';
      
      while ($row_gu = $result_gu->fetch_assoc()) {
        if (isset($row_gu['gid1'])) $gu_in.=$row_gu['gid1'].',';
        if (isset($row_gu['gid2'])) $gu_in.=$row_gu['gid2'].',';
        if (isset($row_gu['gid3'])) $gu_in.=$row_gu['gid3'].',';
        if (isset($row_gu['gid4'])) $gu_in.=$row_gu['gid4'].',';
        if (isset($row_gu['gid5'])) $gu_in.=$row_gu['gid5'].',';
        if (isset($row_gu['gid6'])) $gu_in.=$row_gu['gid6'].',';
        if (isset($row_gu['gid7'])) $gu_in.=$row_gu['gid7'].',';
        if (isset($row_gu['gid8'])) $gu_in.=$row_gu['gid8'].',';
        if (isset($row_gu['gid9'])) $gu_in.=$row_gu['gid9'].',';
        if (isset($row_gu['gid10'])) $gu_in.=$row_gu['gid10'].',';
      }
      if (strlen($gu_in)>0) $gu_in=substr($gu_in, 0, strlen($gu_in)-1);
      if (strlen($gu_in)>0) {
        $sql="SELECT count(Distinct user_id) as ccc2 FROM gks_users_groups_users WHERE group_id In (".$gu_in.")";
        $result_gu = $db_link->query($sql);        
        if (!$result_gu) {
          debug_mail(false,'error sql',$sql);
          die('sql error');
        }
        $row_gu = $result_gu->fetch_assoc();
        $gu_num = $row_gu['ccc2'];
      }
      if ($gu_num>0) echo number_format($gu_num, 0, ',', '.');
    ?></td>  
    <td class="mytdcml"><?php echo nl2br_gks($row['group_comments']);?></td>

    <td class="mytdcm"><?php if (isset($row['group_date_add'])) echo showDate(strtotime($row['group_date_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td class="mytdcm"><?php echo myimg010r($row['group_disable']);?></td>

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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','delete',0);?>;

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

});
</script>
   
<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

