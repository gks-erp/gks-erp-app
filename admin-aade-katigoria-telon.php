<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Τέλη');
$nav_active_array=array('manage','manage_aade','manage_aade_katigoria_telon');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_aade','edit',0);






$filters = array();

$filters[] = array(
  'name' => 'fdisable',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ενεργό'),
  'has_custom_default' => -1,
  'multiselect' => true,    
  'field'  => "1=1",
  'vals' => array(
      //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
      array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "aade_disable = 0"),
      array('value' => 2, 'text' => gks_lang('Μη ενεργό'),  'sql' => "aade_disable <> 0"),
  ),
);
    


$sortable = array(
		array('name' => 'soid', 'field' => 'id_aade_katigoria_telon'),
		array('name' => 'socode', 'field' => 'aade_katigoria_telon_code'),
		array('name' => 'sodescr', 'field' => 'aade_katigoria_telon_descr'),
		array('name' => 'sotype', 'field' => 'aade_katigoria_telon_type'),
		array('name' => 'sopososto', 'field' => 'aade_katigoria_telon_pososto'),
		array('name' => 'sofn', 'field' => 'aade_katigoria_telon_poso_fn'),
		array('name' => 'sofix', 'field' => 'aade_katigoria_telon_poso_fix'),
		array('name' => 'sosort', 'field' => 'sortorder'),
		array('name' => 'sodisable', 'field' => 'aade_disable'),
		array('name' => 'sopeppol', 'field' => 'telon_peppol_code'),   
);



$search_fields = array(
'aade_katigoria_telon_descr',
'telon_peppol_code',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS *
FROM gks_aade_katigoria_telon 
where 1=1 ".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY sortorder,aade_katigoria_telon_descr,id_aade_katigoria_telon";
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
<style>
<?php if ($perm_edit) {?>  
.div_peppol {
  background-image:unset;
  cursor:pointer;
  padding-right: 20px !important;
  white-space: nowrap;
}   
.div_peppol:hover {
  background-image:url('/my/img/pencil-16.png');
  background-repeat: no-repeat;
  background-position: right;
  font-weight1: bold;
}  
.div_peppol_edit {
  padding-right: 0.3rem !important;
}
.div_peppol_edit:hover {
  background-image:unset;
}

.input_peppol {
  width: 100px;
  display: inline-block;
}
.save_peppol {
  font-size: 31px;
  margin-left: 6px;
  color: #007bff;
  vertical-align: bottom;  
}
.cancel_peppol {
  font-size: 31px;
  margin-left: 6px;
  color: #dc3545;
  vertical-align: bottom;  
  
}
<?php } ?>

</style>
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
      <a class="btn btn-primary gks_add_new_record" href="admin-aade-katigoria-telon-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τέλους');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_aade_katigoria_telon">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός ΑΑΔΕ')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopososto', gks_lang('Ποσοστό')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofn', gks_lang('Συνάρτηση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofix', gks_lang('Σταθερό','part2')); ?></th>        
<?php if ($perm_edit) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopeppol', gks_lang('Peppol')); ?></th>        
    
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_aade_katigoria_telon'];?>">
    <th class="mytdcm" scope="row" nowrap><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-aade-katigoria-telon-item.php?id=<?php echo $row['id_aade_katigoria_telon'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_aade_katigoria_telon'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_aade_katigoria_telon'];?>" data-model="gks_aade_katigoria_telon"></i></td>
        </tr>      
      </table>
    </td>
    <td class="mytdcml"><?php echo $row['aade_katigoria_telon_descr'];?></td>
    <td class="mytdcm"><?php echo $row['aade_katigoria_telon_code'];?></td>
    <td class="mytdcm"><?php echo $row['aade_katigoria_telon_type'];?></td>
    <td class="mytdcm"><?php echo $row['aade_katigoria_telon_pososto'];?></td>
    <td class="mytdcml"><?php echo $row['aade_katigoria_telon_poso_fn'];?></td>
    <td class="mytdcm"><?php echo $row['aade_katigoria_telon_poso_fix'];?></td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><?php echo myimg010r($row['aade_disable']);?></td>
    <td class="mytdcml div_peppol" data-id="<?php echo $row['id_aade_katigoria_telon'];?>"><?php echo $row['telon_peppol_code'];?></td>
    
  </tr>
<?php    
    }
?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

 
<div class="container-fluid gksitemfooter" style="margin-top:50px;">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <p><?php echo gks_lang('Για τους κωδικούς Peppol δείτε τον παρακάτω σύνδεσμο');?>:<br>
        <a href="https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/" target="_blank"
          >https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/</a>
      </p>
    </div>
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#table_gks_aade_katigoria_telon > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_aade_katigoria_telon',mylist,'#table_gks_aade_katigoria_telon > tbody');
    }
  }); 
  <?php if ($perm_edit) {?>
  $('.div_peppol').click(function(event) {
    event.stopPropagation();
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    mytext=$('.div_peppol[data-id='+ myid + ']').html(); 
    if (mytext.startsWith('<input')) return;
    
    if (mytext === undefined) mytext='';
    mytext=mytext.trim();
    $('.div_peppol[data-id='+ myid + ']').attr('data-old-value',mytext);
    
    html='<input data-id="' + myid + '" type="text" value="' + mytext + '" class="input_peppol form-control form-control-sm" >';
    html+='<i data-id="' + myid + '" class="save_peppol fas fa-save"></i>';
    html+='<i data-id="' + myid + '" class="cancel_peppol fas fa-window-close"></i>';
    
    $('.div_peppol[data-id='+ myid + ']').html(html).addClass('div_peppol_edit');
    $('.div_peppol[data-id='+ myid + '] .save_peppol').click(save_peppol_click);
    $('.div_peppol[data-id='+ myid + '] .cancel_peppol').click(cancel_peppol_click);
    $('.div_peppol[data-id='+ myid + '] .input_peppol').focus().select();
  });
  
  function cancel_peppol_click() {
    event.stopPropagation();
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    mytext=$('.div_peppol[data-id='+ myid + ']').attr('data-old-value');
    $('.div_peppol[data-id='+ myid + ']').html(mytext).removeClass('div_peppol_edit');
  }  
  function save_peppol_click() {
    event.stopPropagation();
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    mytext=$('.div_peppol[data-id='+ myid + '] .input_peppol').val();
    gks_field_from_table_save('gks_aade_katigoria_telon',myid,'telon_peppol_code',mytext, false);
  }
  window.gks_field_from_table_save_after=function(mydata) {
    if (mydata.table_name!='gks_aade_katigoria_telon') return;
    if (mydata.table_id<=0) return;
    if (mydata.field_name!='telon_peppol_code') return;
    $('.div_peppol[data-id='+ mydata.table_id + ']').html(mydata.myvalue).attr('data-old-value',mydata.myvalue);
  }
  <?php } ?>
  
    
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


