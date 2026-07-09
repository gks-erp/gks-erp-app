<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Pings');
$nav_active_array=array('license','license_efs_license_log');


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
  'name' => 'fmydate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_efs_license_log.mydate', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//  'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_efs_license_log.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),


);

$filters[] = array(
    'name' => 'fwev',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Wait Email Confirmation',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_efs_license_log.IsWaitEmailConfirmation = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),                'sql' => "1=1"),
        array('value' => -11, 'text' => gks_lang('Ναι'),     'sql' => "gks_efs_license_log.IsWaitEmailConfirmation<>0"),
        array('value' => -12, 'text' => gks_lang('Όχι'),     'sql' => "gks_efs_license_log.IsWaitEmailConfirmation=0"),
    ),
);

$filters[] = array(
    'name' => 'ftrial',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Trial',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_efs_license_log.isTrial = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),                'sql' => "1=1"),
        array('value' => -11, 'text' => gks_lang('Ναι'),     'sql' => "gks_efs_license_log.isTrial<>0"),
        array('value' => -12, 'text' => gks_lang('Όχι'),     'sql' => "gks_efs_license_log.isTrial=0"),
    ),
);
$filters[] = array(
    'name' => 'funregister',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'UnRegister',
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_efs_license_log.IsUnRegister = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),                'sql' => "1=1"),
        array('value' => -11, 'text' => gks_lang('Ναι'),     'sql' => "gks_efs_license_log.IsUnRegister<>0"),
        array('value' => -12, 'text' => gks_lang('Όχι'),     'sql' => "gks_efs_license_log.IsUnRegister=0"),
    ),
);
$filters[] = array(
    'name' => 'fversion',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Έκδοση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_efs_license_log.product_version = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),                'sql' => "1=1"),

    ),
    'sql' => "select product_version as id, product_version as descr from gks_efs_license_log where product_version<>'' group by product_version order by product_version",
);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_efs_license_log.id'),
  						array('name' => 'somydate', 'field' => 'gks_efs_license_log.mydate'),
  						array('name' => 'soemail', 'field' => 'gks_efs_license_log.email'),
  						array('name' => 'sowec', 'field' => 'gks_efs_license_log.IsWaitEmailConfirmation'),
  						array('name' => 'soisTrial', 'field' => 'gks_efs_license_log.isTrial'),
  						array('name' => 'soIsUnRegister', 'field' => 'gks_efs_license_log.IsUnRegister'),
  						array('name' => 'soRunsCount', 'field' => 'gks_efs_license_log.RunsCount'),
  						array('name' => 'soproduct_version', 'field' => 'gks_efs_license_log.product_version'),
  						array('name' => 'soapp_lang', 'field' => 'gks_efs_license_log.app_lang'),
  						array('name' => 'sosys_lang', 'field' => 'gks_efs_license_log.sys_lang'),
  						array('name' => 'sowinver', 'field' => 'gks_efs_license_log.winver'),
  						array('name' => 'sobitver', 'field' => 'gks_efs_license_log.bitver'),
  						array('name' => 'sopctime', 'field' => 'gks_efs_license_log.pctime'),
  						array('name' => 'sousername', 'field' => 'gks_efs_license_log.username'),
  						array('name' => 'somachine_name', 'field' => 'gks_efs_license_log.machine_name'),
  						array('name' => 'somyip', 'field' => 'gks_efs_license_log.myip'),
             );

$search_fields = array(
'gks_efs_license_log.email',
'gks_efs_license_log.product_version',
'gks_efs_license_log.app_lang',
'gks_efs_license_log.sys_lang',
'gks_efs_license_log.winver',
'gks_efs_license_log.bitver',
'gks_efs_license_log.username',
'gks_efs_license_log.machine_name',
'gks_efs_license_log.macaddress',
'gks_efs_license_log.hdds',
'gks_efs_license_log.myip',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_efs_license_log.*
FROM gks_efs_license_log 
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_efs_license_log.id desc";
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
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', 'email'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowec', '<span class="tooltipster" title="Wait Email Confirmation">WEC</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soisTrial', 'Trial'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soIsUnRegister', 'Un<br>Register'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soRunsCount', gks_lang('Εκτελέσεις')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct_version', gks_lang('Έκδοση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soapp_lang', '<span class="tooltipster" title="'.gks_lang('Γλώσσα Εφαρμογής').'">'.gks_lang('ΓΕ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosys_lang', '<span class="tooltipster" title="'.gks_lang('Γλώσσα Συστήματος').'">'.gks_lang('ΓΣ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowinver', 'Windows'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobitver', 'Bits'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopctime', gks_lang('Ώρα Η/Υ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sousername', gks_lang('Όνομα<br>Χρήστη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somachine_name', gks_lang('Όνομα<br>Η/Υ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%">MacAddress</th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%">HDDs</th>        
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyip', 'IP'); ?></th>        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap style="text-align: center;"><?php echo ($i + $showFrom);?></th>
    <td nowrap style="text-align: center;"><?php echo $row['id'];?></td>
    <td nowrap ><?php echo mb_substr( getWeekDayName(showDate(strtotime($row['mydate']), 'w', 1)),0,2).' '. showDate(strtotime($row['mydate']), 'd/m/Y H:i:s',1);?></td>
    <td nowrap><?php echo $row['email'];?></td>
    <td nowrap style="text-align: center;"><?php if ($row['IsWaitEmailConfirmation']!=0) {?><img src="img/1.png" border="0" width="16"><?php } ?></td>
    <td nowrap style="text-align: center;"><?php if ($row['isTrial']!=0) {?><img src="img/1.png" border="0" width="16"><?php } ?></td>
    <td nowrap style="text-align: center;"><?php if ($row['IsUnRegister']!=0) {?><img src="img/1.png" border="0" width="16"><?php } ?></td>
    <td nowrap style="text-align: center;"><?php echo $row['RunsCount'];?></td>
    <td nowrap style="text-align: center;"><?php echo $row['product_version'];?></td>
    <td nowrap style="text-align: center;"><?php echo $row['app_lang'];?></td>
    <td nowrap style="text-align: center;"><?php echo $row['sys_lang'];?></td>
    <td nowrap style="text-align: center;"><?php echo $row['winver'];?></td>
    <td nowrap style="text-align: center;"><?php echo $row['bitver'];?></td>
    <td nowrap><?php echo showDate(strtotime($row['pctime']), 'd/m/Y H:i:s',1);?></td>
    <td nowrap><?php echo $row['username'];?></td>
    <td nowrap><?php echo $row['machine_name'];?></td>
    <?php 
      $temp1='';
      $temp2='';
      $macaddress_s=trim_gks($row['macaddress'].'');
      $macaddress=unserialize(trim_gks($row['macaddress']));
      if (is_array($macaddress)) {
        $temp2=print_r($macaddress,true);
        foreach ($macaddress as $value) {
           $temp1.=trim_gks($value['GetPhysicalAddress']).' ';
        } 
      }
      echo '<td class="tooltipster" title="<pre>'.str_replace('"'," ",$temp2).'</pre>">'.$temp1.'</td>';
    ?>
    <?php 
      $temp1='';
      $temp2='';
      $hdds=trim_gks($row['hdds'].'');
      $hdds=unserialize(trim_gks($row['hdds']));
      if (is_array($hdds)) {
        $temp2=print_r($hdds,true);
        foreach ($hdds as $value) {
           $temp1.=trim_gks($value['SerialNumber']).' ';
        } 
      }
      echo '<td class="tooltipster" title="<pre>'.str_replace('"'," ",$temp2).'</pre>">'.$temp1.'</td>';
    ?>
    <td nowrap style="text-align: center;"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>">V</a></td>  
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

  
  $('#fmydate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdonedate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdonedate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      
      if (v==-2) { //is_custom_date
        if (sname == 'fmydate' || sname=='fdonedate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fmydate' || sname=='fdonedate') {
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


