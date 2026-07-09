<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Φόρμες Εκτύπωσης');
$nav_active_array=array('manage','manage_print_forms');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_print_forms','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_print_forms_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_print_forms','edit',0);







$gks_custom_prepare = gks_custom_table_item_prepare('gks_print_forms',['from'=>'list']);

$filters = array();
$filters[] = array(
    'name' => 'flang',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Γλώσσα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.gks_lang like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_lang as id, lang_name as descr FROM gks_lang order by lang_sortorder,lang_name",
);

$filters[] = array(
    'name' => 'ffile',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.file_type like '%V%'",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('pdf'),          'sql' => "file_type like 'pdf'"),
        array('value' => 11, 'text' => gks_lang('html'),         'sql' => "file_type like 'html'"),
    ),
);

$filters[] = array(
    'name' => 'flandscape',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Προσανατολισμός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.is_landscape =%V%",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('Κατακόρυφος'),        'sql' => "is_landscape=0"),
        array('value' => 11, 'text' => gks_lang('Οριζόντιος'),         'sql' => "is_landscape!=0"),
    ),
);

$filters[] = array(
    'name' => 'fgrayscale',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρώμα ή Γκρι'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.grayscale =%V%",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('Χρώμα'),     'sql' => "grayscale=0"),
        array('value' => 11, 'text' => gks_lang('Γκρι'),      'sql' => "grayscale!=0"),
    ),
);



$filters[] = array(
    'name' => 'fsize_name',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μέγεθος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.size_name='%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT size_name as id, size_name as descr FROM gks_print_forms group by size_name order by size_name",
);

$filters[] = array(
    'name' => 'fdpi',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('dpi'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.dpi=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT dpi as id, dpi as descr FROM gks_print_forms group by dpi order by dpi",
);


$filters[] = array(
    'name' => 'flogo',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει Λογότυπο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.logo_url like '%V%'",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('Ναι'),     'sql' => "logo_url<>''"),
        array('value' => 11, 'text' => gks_lang('Όχι'),     'sql' => "(logo_url is null or logo_url='')"),
    ),
);

$filters[] = array(
    'name' => 'fbackground',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έχει Υδατογράφημα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.page_background_url like '%V%'",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('Ναι'),     'sql' => "page_background_url<>''"),
        array('value' => 11, 'text' => gks_lang('Όχι'),     'sql' => "(page_background_url is null or page_background_url='')"),
    ),
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_print_forms.is_disable like '%V%'",
    'vals' => array(
        array('value' => 10, 'text' => gks_lang('Ναι'),     'sql' => "is_disable=0"),
        array('value' => 11, 'text' => gks_lang('Όχι'),     'sql' => "is_disable!=0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_print_forms.id_print_form'),
  						array('name' => 'sodescr', 'field' => 'gks_print_forms.print_form_descr'),
  						array('name' => 'solang_name', 'field' => 'gks_lang.lang_name'),
  						array('name' => 'sofile_type', 'field' => 'gks_print_forms.file_type'),
  						array('name' => 'solandscape', 'field' => 'gks_print_forms.is_landscape'),
  						array('name' => 'sograyscale', 'field' => 'gks_print_forms.grayscale'),
  						array('name' => 'sozoom', 'field' => 'gks_print_forms.zoom'),
  						array('name' => 'sosize_name', 'field' => 'gks_print_forms.size_name'),
  						array('name' => 'sosize_cm', 'field' => '(width_cm*height_cm)'),
  						array('name' => 'sodpi', 'field' => 'gks_print_forms.dpi'),
  						array('name' => 'sologo', 'field' => 'gks_print_forms.logo_url'),
  						array('name' => 'soback', 'field' => 'gks_print_forms.page_background_url'),
  						array('name' => 'sodisable', 'field' => 'gks_print_forms.is_disable'),
  						array('name' => 'sosort', 'field' => 'gks_print_forms.sortorder'),
  						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(

'gks_print_forms.print_form_descr',
'gks_lang.lang_name',
'gks_print_forms.file_type',
'gks_print_forms.size_name',
'gks_print_forms.logo_url',
'gks_print_forms.page_background_url',
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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_print_forms.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_lang.lang_name
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((gks_print_forms 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_print_forms.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_print_forms.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang

where 1=1 " .$where . $search_where;

if (empty($sorted['sql'])) {
	$query .= " ORDER BY gks_print_forms.sortorder, gks_print_forms.print_form_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-print_forms-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας φόρμας εκτύπωσης');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_print_forms">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solang_name', gks_lang('Γλώσσα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofile_type', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solandscape', gks_lang('Προσανατολισμός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sograyscale', gks_lang('Χρώμα ή γκρι')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sozoom', gks_lang('Μεγέθυνση %')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosize_name', gks_lang('Μέγεθος')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosize_cm', gks_lang('σε cm')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodpi', gks_lang('dpi')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sologo', gks_lang('Λογότυπο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soback', gks_lang('Υδατογράφημα')); ?></th>        
        <?php if ($perm_print_forms_edit) {?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
        <?php } ?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable',  gks_lang('Ενεργή')); ?></th>        
        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_print_form'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-print_forms-item.php?id=<?php echo $row['id_print_form'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_print_form'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_print_form'];?>" data-model="gks_print_forms"></i></td>
        </tr>      
      </table>
    </td>
     

    <td class="mytdcml"><?php echo $row['print_form_descr'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['lang_name'];?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['file_type']=='pdf') echo '<i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:150%"></i>';  
      else if ($row['file_type']=='html') echo '<i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>';
      else echo $row['file_type'];
      ?></td>
    <td nowrap class="mytdcm"><?php  
      if ($row['is_landscape']!=0) echo '<i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:150%"></i>';  
      else echo '<i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:150%"></i>';
      ?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['grayscale']!=0) echo '<img src="img/palette-gray.png" border="0" width="16">';
      else echo '<img src="img/palette-color.png" border="0" width="16">';
    ?></td>
    <td nowrap class="mytdcm"><?php echo myNumberFormatNo0Local($row['zoom']*100);?></td>
    <td nowrap class="mytdcm"><?php echo $row['size_name'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['width_cm'].'x'.$row['height_cm'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['dpi'];?></td>
    <td nowrap class="mytdcm"><?php if (trim_gks($row['logo_url'])!='') echo '<img src="img/1.png" border="0" width="16">';?></td>
    <td nowrap class="mytdcm"><?php if (trim_gks($row['page_background_url'])!='') echo '<img src="img/1.png" border="0" width="16">';?></td>
    
    <?php if ($perm_print_forms_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sortorder'];?></span>
    </td>
    <?php }?>

    
    <td class="mytdcm"><?php echo myimg010r($row['is_disable']);?></td>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','delete',0);?>;

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

  $('#table_gks_print_forms > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_print_forms',mylist,'#table_gks_print_forms > tbody');
    }
  }); 
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

