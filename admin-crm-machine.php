<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Συσκευές');
$nav_active_array=array('crm','crm_machine');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_machine','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


include_once('admin-crm-machine_filters.php');


if (isset($_GET['mass_message']) and intval($_GET['mass_message'])==1) {
  $result = $db_link->query($sql); 
  if (!$result) debug_mail(false,'error sql mm',$sql);
  if (!$result) die('sql error mm');
  
  $ids=[];
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['crm_machine_user_id'],$ids)==false) {
      $ids[]=$row['crm_machine_user_id']; 
    }
  }
  //echo '<pre>';print_r($ids);die();
  $filename=date('YmdHis').rand(1000,9999).rand(1000,9999).rand(1000,9999);
  $filepath=GKS_SITE_PATH.'tmp/mass_message_'.$filename.'.json';
  $ret=@file_put_contents($filepath,json_encode($ids));
  if ($ret==false) {
    debug_mail(false,'error write file',$filepath);    
    echo '<pre>Error write file '.$filepath.'</pre>';
    die();}
  
  header('Location: admin-mass-messages-new.php?list='.$filename);
  die();
  
  
}

$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $sql; die();
	
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
      <a class="btn btn-primary gks_add_new_record" href="admin-crm-machine-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας συσκευής');?></a>
      <?php
      $mass_url=''; 
      if (isset($_SERVER['QUERY_STRING'])) $mass_url=$_SERVER['QUERY_STRING'];
      //$parts=explode('#',
      $mass_url.='&mass_message=1';
      $mass_url=$_SERVER['SCRIPT_NAME'].'?'.$mass_url;
      ?>
      <a class="btn btn-primary gks_add_new_mass_message" href="<?php echo $mass_url;?>"><?php echo gks_lang('Μαζική αποστολή');?></a>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserial', 'Serial Number'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct', gks_lang('Είδος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobrand', gks_lang('Μάρκα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Πελάτης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', gks_lang('Οδός')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaddress', gks_lang('Τόπος')); ?></th>        
  
<?php 
if ($plugin_sql_from_1!='') {
  $plugin_table_th='';
  gks_plugins_functions_run('admin_crm_machine_filters_step2',array(
    'table_th' => &$plugin_table_th,
  ));
  echo $plugin_table_th;
}
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
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-crm-machine-item.php?id=<?php echo $row['id_crm_machine'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_crm_machine'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_crm_machine'];?>" data-model="gks_crm_machine"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['crm_machine_name'];?></td>
    <td class="mytdcml"><?php echo $row['crm_machine_serial_number'];?></td>
    <td class="mytdcml"><a href="admin-products-item.php?id=<?php echo $row['crm_machine_product_id'];?>"><?php echo $row['product_descr_p'];?></a></td>
    <td class="mytdcml"><a href="admin-product-brands-item.php?id=<?php echo $row['crm_machine_brand_id'];?>"><?php echo $row['brand_fullpath'];?></a></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['crm_machine_user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td class="mytdcml"><?php echo $row['showodos'].' '.$row['showarithmos'].' '.$row['showperioxi'];?></td>
    <td class="mytdcml"><?php echo $row['showpoli'].' '.$row['showtk'];?></td>
    <td class="mytdcml"><?php 
      if ($row['users_extra_address_id']==-1) 
        echo gks_lang('Βασική');
      else if ($row['users_extra_address_id']==0)
        echo '';
      else if ($row['users_extra_address_id']>0)
        echo '<a href="admin-users-extra_address-item.php?id='.$row['users_extra_address_id'].'">'.$row['ea_name'].'</a>';
    ?></td>
    
<?php
  if ($plugin_sql_from_1!='') {
    $plugin_table_td='';
    gks_plugins_functions_run('admin_crm_machine_filters_step3',array(
      'table_td' => &$plugin_table_td,
      'row' => &$row,
    ));
    echo $plugin_table_td;
  }
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fmydate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  from_plugin_date_filters=[];
  <?php 
  if ($plugin_sql_from_1!='') {
    echo $plugin_js_date_filters;
  }
  ?>
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fmydate_add' || gks_custom_filters_date_elems.includes(sname) || from_plugin_date_filters.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fmydate_add' || gks_custom_filters_date_elems.includes(sname)|| from_plugin_date_filters.includes(sname)) {
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


