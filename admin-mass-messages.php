<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Μαζική Αποστολή SMS-Viber-email');
$nav_active_array=array('crm','manage_sms','manage_mass_messages');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_mass_messages','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_sms','edit',0);



$today_vardia_this = date('Y-m-d',_time_user(time(), 1));



$filters = array();

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_mass_messages.date_send_start', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_mass_messages.date_send_start','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);





$sortable = array(
	array('name' => 'soid', 'field' => 'gks_mass_messages.id_mass_message'),
	array('name' => 'sodate_send_start', 'field' => 'gks_mass_messages.date_send_start'),
	array('name' => 'sogks_nickname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
	array('name' => 'somyfrom', 'field' => 'gks_mass_messages.sender_sms_provider,gks_mass_messages.sender_sms_sender'),
	array('name' => 'somymessage', 'field' => 'gks_mass_messages.mymessage'),
	array('name' => 'sosend_with_viber', 'field' => 'send_with_viber'),
	array('name' => 'sosend_with_sms', 'field' => 'send_with_sms'),
	array('name' => 'sosend_with_email', 'field' => 'send_with_email'),
	array('name' => 'soemail_subject', 'field' => 'email_subject'),
	array('name' => 'socc_all', 'field' => 'cc_all'),
	array('name' => 'socc_viber', 'field' => 'cc_viber'),
	array('name' => 'socc_sms', 'field' => 'cc_sms'),
	array('name' => 'socc_email', 'field' => 'cc_email'),
	array('name' => 'socc_none', 'field' => 'cc_none'),

	array('name' => 'soip', 'field' => 'gks_mass_messages.myip'),
		
  						
);



$search_fields = array(
  GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  'gks_mass_messages.mymessage',
  'gks_mass_messages.email_subject',
 
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

//SELECT SQL_CALC_FOUND_ROWS gks_urlshort.*, 
//other.shorturl AS other_descr
//FROM gks_urlshort 
//LEFT JOIN gks_urlshort AS other ON gks_urlshort.monada_parent_id = other.id_urlshort

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_mass_messages.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM gks_mass_messages 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_mass_messages.user_id_add =".GKS_WP_TABLE_PREFIX."users.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_mass_messages.id_mass_message desc";
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

<style>
.mydivexpand {
  max-height: 18px;
}  
.mydivexpand_on {
  max-height: unset;  
}  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-mass-messages-new.php"><?php echo gks_lang('Νέα Μαζική Αποστολή');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_urlshort">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_send_start', gks_lang('Ημερομηνία<br>Αποστολής')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogks_nickname', gks_lang('Χρήστης')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyfrom', gks_lang('Από')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somymessage', gks_lang('Μήνυμα')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosend_with_viber', '<img src="img/viber.png" style="width:24px;">'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosend_with_sms', '<img src="img/sms.png" style="width:24px;">'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosend_with_email', '<img src="img/email2.png" style="width:24px;">'); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail_subject', gks_lang('Θέμα email')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_all', gks_lang('Όλα')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_viber', gks_lang('με viber')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_sms', gks_lang('με sms')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_email', gks_lang('με email')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="5%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_none', gks_lang('τίποτα')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soip', 'IP'); ?></th>        
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>     
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-mass-messages-item.php?id=<?php echo $row['id_mass_message'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_mass_message'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_mass_message'];?>" data-model="gks_mass_messages"></i></td>
          <td>&nbsp;<a href="admin-mass-messages-new.php?template_id=<?php echo $row['id_mass_message'];?>"><i class="fas fa-clone"></i></a></td>
        </tr>      
      </table>
    </td>

    
    <td class="mytdcml" nowrap><?php echo showDate(strtotime($row['date_send_start']), 'd/m/Y H:i:s', 1);?></td>   
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['user_id_add'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td class="mytdcml"><?php 
      //echo $row['myfrom'];
    ?></td>
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">
    <?php echo nl2br(htmlspecialchars($row['mymessage']));?>  
    </div></div></td>    

    <td class="mytdcm"><img src="img/<?php echo $row['send_with_viber'];?>.png" border="0" width="16"></td>
    <td class="mytdcm"><img src="img/<?php echo $row['send_with_sms'];?>.png" border="0" width="16"></td>
    <td class="mytdcm"><img src="img/<?php echo $row['send_with_email'];?>.png" border="0" width="16"></td>
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">
    <?php echo nl2br(htmlspecialchars($row['email_subject']));?>  
    </div></div></td>    
    <td class="mytdcm"><?php if ($row['cc_all']!=0)   echo $row['cc_all'];?></td>
    <td class="mytdcm"><?php if ($row['cc_viber']!=0) echo $row['cc_viber'];?></td>
    <td class="mytdcm"><?php if ($row['cc_sms']!=0)   echo $row['cc_sms'];?></td>
    <td class="mytdcm"><?php if ($row['cc_email']!=0) echo $row['cc_email'];?></td>
    <td class="mytdcm"><?php if ($row['cc_none']!=0)  echo $row['cc_none'];?></td>

    <td class="mytdcml"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>">V</a>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdate_add') {
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


