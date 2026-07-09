<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('gks ERP App Desktop');
$nav_active_array=array('manage','manage_erp_app');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_erp_app','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_erp_app',['from'=>'list']);

$filters = array();
$filters[] = array(
    'name' => 'fenable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_erp_app.erp_app_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),    'sql' => "gks_erp_app.erp_app_disabled=0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργή'), 'sql' => "gks_erp_app.erp_app_disabled<>0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_erp_app.id_erp_app'),
  array('name' => 'soname', 'field' => 'gks_erp_app.erp_app_name'),
  array('name' => 'sodescr', 'field' => 'gks_erp_app.erp_app_descr'),
  array('name' => 'sotoken', 'field' => 'gks_erp_app.erp_app_token'),
  array('name' => 'sourl', 'field' => 'gks_erp_app.erp_app_url'),
  array('name' => 'soport', 'field' => 'gks_erp_app.erp_app_port'),
  
  
  array('name' => 'sosort', 'field' => 'gks_erp_app.erp_app_sortorder'),
  array('name' => 'sodisable', 'field' => 'gks_erp_app.erp_app_disabled'),
  array('name' => 'sopurl', 'field' => 'gks_erp_app.erp_app_url,gks_erp_app.erp_app_port'),
  array('name' => 'solan', 'field' => 'gks_erp_app.erp_app_lan_ip'),
  array('name' => 'sowan', 'field' => 'gks_erp_app.erp_app_wan_ip'),
  array('name' => 'solast', 'field' => 'gks_erp_app.erp_app_last_ping'),
  array('name' => 'sover', 'field' => 'gks_erp_app_ping.appver'),
  array('name' => 'sowintime', 'field' => 'gks_erp_app_ping.pctime'),
  array('name' => 'sowinuser', 'field' => 'gks_erp_app_ping.pcusername'),
  array('name' => 'sohost', 'field' => 'gks_erp_app_ping.pcname'),
  array('name' => 'sowinver', 'field' => 'gks_erp_app_ping.winver'),

  array('name' => 'sodisk', 'field' => 'gks_erp_app_ping.hdwd'),
  array('name' => 'soscreen', 'field' => 'gks_erp_app_ping.screw,gks_erp_app_ping.screh'),
  array('name' => 'somac', 'field' => 'gks_erp_app_ping.mac'),
  						
  						

  						
  						
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array(
'gks_erp_app.erp_app_name',
'gks_erp_app.erp_app_descr',
'gks_erp_app.erp_app_token',
'gks_erp_app.erp_app_url',
'gks_erp_app.erp_app_lan_ip',
'gks_erp_app.erp_app_wan_ip',
'gks_erp_app_ping.appver',
'gks_erp_app_ping.pcusername',
'gks_erp_app_ping.pcname',
'gks_erp_app_ping.winver',
'gks_erp_app_ping.mac',

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

//SELECT SQL_CALC_FOUND_ROWS gks_erp_app.*, 
//other.erp_app_descr AS other_descr
//FROM gks_erp_app 
//LEFT JOIN gks_erp_app AS other ON gks_erp_app.monada_parent_id = other.id_erp_app

$sql = "select SQL_CALC_FOUND_ROWS gks_erp_app.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_erp_app_ping.pctime, gks_erp_app_ping.pcusername, gks_erp_app_ping.pcname, gks_erp_app_ping.winver, gks_erp_app_ping.appver, 
gks_erp_app_ping.hdwd, gks_erp_app_ping.screw, gks_erp_app_ping.screh, gks_erp_app_ping.mac

".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((gks_erp_app
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_erp_app.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_erp_app.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_erp_app_ping ON gks_erp_app.last_ping_id = gks_erp_app_ping.id
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY erp_app_last_ping desc,erp_app_sortorder,erp_app_name";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-erp-app-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας εφαρμογής');?></a>
      <a class="btn btn-primary" href="https://tools.gks.gr/gks_erp_app/gksErpApp.zip" target="_blank"><?php echo gks_lang('Λήψη');?> <i class="fas fa-download"></i></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_erp_app">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotoken', gks_lang('Κλειδί')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sourl', gks_lang('URL')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soport', gks_lang('Πόρτα')); ?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopurl', gks_lang('Public URL')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solan', gks_lang('Lan IP')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowan', gks_lang('WAN IP')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solast', '<span class="tooltipster" title="'.gks_lang('Τελευταία σύνδεση').'">'.gks_lang('Σύνδεση').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sover', '<span class="tooltipster" title="'.gks_lang('Έκδοση gks ERP App Desktop').'">'.gks_lang('Ver').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowintime', '<span class="tooltipster" title="'.gks_lang('Ώρα Η/Υ').'">'.gks_lang('Ώρα').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowinuser', '<span class="tooltipster" title="'.gks_lang('Όνομα χρήστη').'">'.gks_lang('Χρήστης').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohost', '<span class="tooltipster" title="'.gks_lang('Όνομα Η/Υ').'">'.gks_lang('Η/Υ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowinver', '<span class="tooltipster" title="'.gks_lang('Έκδοση Windows').'">'.gks_lang('Win').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisk', '<span class="tooltipster" title="'.gks_lang('Ελεύθερος χώρος στον δίσκο').'">'.gks_lang('Δίσκος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soscreen', gks_lang('Οθόνη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somac', '<span class="tooltipster" title="'.gks_lang('Mac Address').'">'.gks_lang('Mac').'</span>'); ?></th>        
      



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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_erp_app'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-erp-app-item.php?id=<?php echo $row['id_erp_app'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_erp_app'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_erp_app'];?>" data-model="gks_erp_app"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcml"><?php echo $row['erp_app_name'];?></td>
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php echo $row['erp_app_descr'];?></div></div></td>
    <td class="mytdcml"><?php echo $row['erp_app_token'];?></td>
    <td class="mytdcm"><?php echo $row['erp_app_url'];?></td>
    <td class="mytdcm"><?php echo $row['erp_app_port'];?></td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['erp_app_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['erp_app_sortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><img src="img/<?php echo $row['erp_app_disabled']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    
    
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $public_url='';
      if ($row['erp_app_url']=='frp') {
        if (trim_gks($row['erp_app_token'])!='') {
          $public_url='http://'.$row['erp_app_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
        }
      } else {
        if (trim_gks($row['erp_app_url'])!='' and $row['erp_app_port']>0) {
          $public_url='http://'.$row['erp_app_url'].':'.$row['erp_app_port'];
        }
      }
      if ($public_url!='') echo '<a href="'.$public_url.'" target="_blank">'.$public_url.'</a>';      
    ?></div></div></td>
    <td class="mytdcm" nowrap><?php echo $row['erp_app_lan_ip'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['erp_app_wan_ip'];?></td>
    <td class="mytdcm" nowrap><?php
      //if (isset($row['erp_app_last_ping'])) echo showDate(strtotime($row['erp_app_last_ping']), 'd/m/Y H:i:s', 1);

      if (isset($row['erp_app_last_ping'])) {
        if (strtotime($row['erp_app_last_ping']) > time()-15*60) {
          echo '<span class="gks_erp_app_alive">'.secondsago(strtotime($row['erp_app_last_ping'])).'</span>';
        } else {
          echo '<span class="gks_erp_app_not_alive">'.secondsago(strtotime($row['erp_app_last_ping'])).'</span>';
        }
      }
    ?></td>
    
    
    
    <td class="mytdcm" nowrap><?php echo $row['appver'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['pctime'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['pcusername'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['pcname'];?></td>
 
                    
    <td class="mytdcm" nowrap><?php echo $row['winver'];?></td>
    <td class="mytdcm" nowrap><?php if ($row['hdwd']>0) echo number_format($row['hdwd']/1024,2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).' GB';?></td>
    <td class="mytdcm" nowrap><?php if ($row['screw']>0) echo $row['screw'].'x'.$row['screh'];;?></td>
    <td class="mytdcml" nowrap><?php 
      if (trim_gks($row['mac'])!='') {
        $parts=explode('|',$row['mac']);
        echo implode('<br>', $parts);} 
    ?></td>
    
    
    

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#table_gks_erp_app > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_erp_app',mylist,'#table_gks_erp_app > tbody');
    }
  });  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


