<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Καταγραφές emails');
$nav_active_array=array('crm','manage_email','manage_emaillog');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$filters = array();
$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_email.date_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_email.date_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
    'name' => 'user',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρήστης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_email.user_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλοι'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_email.user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_email LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_email.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
      GROUP BY gks_email.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

$filters[] = array(
    'name' => 'myfrom',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Από'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_email.myfrom = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT myfrom as descr, myfrom as id FROM gks_email where myfrom<>'' GROUP BY myfrom ORDER BY myfrom",
);

$filters[] = array(
    'name' => 'myto',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Προς'),
    'field' => 'myto',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
        array('value' => 2, 'text' => $GKS_SITE_EMAIL,     'sql' => "myto='".$GKS_SITE_EMAIL."'"),
        
    )
);

$filters[] = array(
    'name' => 'myret',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => 'Res',
    'field' => 'myret',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
        array('value' => 2, 'text' => gks_lang('Επιτυχία'),     'sql' => "myret<>0"),
        array('value' => 3, 'text' => gks_lang('Αποτυχία'),     'sql' => "myret=0"),
    )
);
$filters[] = array(
    'name' => 'model',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μοντέλο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field' => "gks_email.model = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT model as descr, model as id FROM gks_email GROUP BY model ORDER BY model",
    
);


$sortable = array(
  						array('name' => 'sodate_add', 'field' => 'gks_email.date_add'),
  						array('name' => 'sogks_nickname', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'somyfrom', 'field' => 'gks_email.myfrom'),
  						array('name' => 'somyfrom_name', 'field' => 'gks_email.myfrom_name'),
  						array('name' => 'soreplyto', 'field' => 'gks_email.replyto'),
  						array('name' => 'sosender', 'field' => 'gks_email.sender'),
  						array('name' => 'somyto', 'field' => 'gks_email.myto'),
  						array('name' => 'somyto_name', 'field' => 'gks_email.myto_name'),
  						array('name' => 'somyret', 'field' => 'gks_email.myret'),
  						array('name' => 'somodel', 'field' => 'gks_email.model'),
  						array('name' => 'sodate_view', 'field' => 'gks_email.date_view'),
            );

$search_fields = array('gks_email.myfrom','gks_email.myfrom_name','gks_email.replyto',
'gks_email.sender','gks_email.myto','gks_email.myto_name',
'gks_email.subject','gks_email.body','gks_email.Attachments',
'gks_email.EmbeddedImages','gks_email.model');


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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_email.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM gks_email 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_email.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where 1=1 ".$where . $search_where;




if (empty($sorted['sql'])) {
	$query .= " ORDER BY date_add desc";
} else {
	$query .= " ORDER BY " . $sorted['sql'];
}
$query .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $query;
//die();
	
$result = $db_link->query($query);        
if (!$result) debug_mail(false,'admin-log-emails.php error sql',$query);
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

//print '<pre>';print_r($paging);print '</pre>';

//print '<pre>';
//print_r($sortable);
//echo '<br>';
//echo $sortable_url;
//die();

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
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_add', gks_lang('Ημερομηνία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogks_nickname', gks_lang('Χρήστης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyfrom', gks_lang('Από')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyfrom_name', gks_lang('Από Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreplyto', gks_lang('Απάντηση σε')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosender', gks_lang('Αποστολέας')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyto', gks_lang('Προς')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyto_name', gks_lang('Προς Όνομα')); ?></th>        
        <th class="table-dark" scope="col" nowrap='nowrap' width='0%'><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
        <th class="table-dark" scope="col" nowrap='nowrap' width='40%'><?php echo gks_lang('Θέμα');?></th>
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyret', '<span class="tooltipster" title="'.gks_lang('Αποτέλεσμα αποστολής').'">'.gks_lang('Αποτ.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodel', gks_lang('Μοντέλο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_view', gks_lang('Προβλήθηκε')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Προβολές');?></th>        
        <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('IPs');?></th>        
      
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
    <td nowrap class="mytdcml"><?php echo showDate(strtotime($row['date_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcml"><?php echo $row['gks_nickname'];?></td>      
    <td nowrap class="mytdcml"><?php echo $row['myfrom'];?></td>      
    <td nowrap class="mytdcml"><?php echo $row['myfrom_name'];?></td>      
    <td nowrap class="mytdcml"><?php echo $row['replyto'];?></td>      
    <td nowrap class="mytdcml"><?php echo $row['sender'];?></td>      
    <td class="mytdcml" style="word-break: break-all;"><?php echo $row['myto'];?></td>      
    <td class="mytdcml" style="word-break: break-all;"><?php echo $row['myto_name'];?></td>      
    <td nowrap class="mytdcm">
      <i class="fas fa-envelope gks_email_view" data-id="<?php echo $row['id'];?>"></i>
    </td>      
    <td       class="mytdcml" ><?php echo $row['subject'];?></td>      
    <td nowrap class="mytdcm"><img src="img/<?php echo $row['myret'];?>.png" border="0" width="16"></td>      
    <td nowrap class="mytdcml"><?php echo $row['model'];?></td>      
    <td nowrap class="mytdcml"><?php if (isset($row['date_view'])) echo showDate(strtotime($row['date_view']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php if ($row['views_count']>0) echo $row['views_count'];?></td>      
    <td nowrap class="mytdcml"><?php echo nl2br_gks($row['views_ips']);?></td>
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
  
var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate') {
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


