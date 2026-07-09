<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Καταγραφές SMS');
$nav_active_array=array('crm','manage_sms','manage_smslog');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$filters = array();
$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_sms.date_add',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_sms.date_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'myfolder',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Φάκελος'),
    'field' => 'gks_sms.sms_folder',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Εισερχόμενα'),  'sql' => "gks_sms.sms_folder='inbox'"),
        array('value' => 2, 'text' => gks_lang('Απεσταλμένα'),  'sql' => "gks_sms.sms_folder='sent'"),
        array('value' => 3, 'text' => gks_lang('Πρόχειρα'),     'sql' => "gks_sms.sms_folder='draft'"),
        array('value' => 4, 'text' => gks_lang('Εξερχόμενα'),   'sql' => "gks_sms.sms_folder='outbox'"),
        array('value' => 5, 'text' => gks_lang('Αποτυχημένα'),  'sql' => "gks_sms.sms_folder='failed'"),
    )
);


$filters[] = array(
    'name' => 'user',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρήστης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_sms.user_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλοι'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_sms.user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM gks_sms LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_sms.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
      GROUP BY gks_sms.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

//$filters[] = array(
//    'name' => 'myfrom',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Από'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_sms.myfrom = '%V%'",
//    'vals' => array(
//        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
//    ),
//    'sql' => "SELECT myfrom as descr, myfrom as id FROM gks_sms where myfrom<>'' GROUP BY myfrom ORDER BY myfrom",
//);

$filters[] = array(
    'name' => 'parts',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κομμάτια'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_sms.parts = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT parts as descr, parts as id FROM gks_sms where parts>=0 GROUP BY parts ORDER BY parts",
);

$filters[] = array(
    'name' => 'myret',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποτέλεσμα'),
    'field' => 'gks_sms.myret',
    'has_custom_default' => -1,
    'multiselect' => true,
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
        array('value' => 2, 'text' => gks_lang('Επιτυχία'),     'sql' => "gks_sms.myret<>0"),
        array('value' => 3, 'text' => gks_lang('Αποτυχία'),     'sql' => "gks_sms.myret=0"),
    )
);

$filters[] = array(
    'name' => 'status_name',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_sms.status_name = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT status_name as descr, status_name as id FROM gks_sms where status_name<>'' GROUP BY status_name ORDER BY status_name",
);


$filters[] = array(
		'name' => 'fdonedate',
		'class' => 'filterselectbox ui-state-default ui-corner-all',
		'style' => '',
  'title' => gks_lang('Ημερομηνία ΑΠ'),
		'has_custom_date' => true,
		'field' => 'gks_sms.donedate_date',
		'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_sms.donedate_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'model',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μοντέλο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field' => "gks_sms.model = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT model as descr, model as id FROM gks_sms where model<>'' GROUP BY model ORDER BY model",
    
);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_sms.id'),
  						array('name' => 'sodate_add', 'field' => 'gks_sms.date_add'),
  						array('name' => 'soprovider', 'field' => 'gks_sms.sms_provider'),
  						array('name' => 'sogks_nickname', 'field' => 'wp_users.gks_nickname'),
  						array('name' => 'somyfrom', 'field' => 'gks_sms.myfrom'),
  						array('name' => 'somyto', 'field' => 'gks_sms.myto'),
  						array('name' => 'sofolder', 'field' => 'gks_sms.sms_folder'),
  						array('name' => 'soparts', 'field' => 'gks_sms.parts'),
  						array('name' => 'socost', 'field' => 'gks_sms.cost'),
  						array('name' => 'somyret', 'field' => 'gks_sms.myret'),
  						array('name' => 'sostatus_name', 'field' => 'gks_sms.status_name'),
  						array('name' => 'sodonedate_date', 'field' => 'gks_sms.donedate_date'),
  						array('name' => 'somodel', 'field' => 'gks_sms.model'),
  						array('name' => 'somodelid', 'field' => 'gks_sms.model_id'),
  						array('name' => 'soerror', 'field' => 'gks_sms.sms_result'),


            );

$search_fields = array('gks_sms.myfrom','gks_sms.myto','gks_sms.message','gks_sms.message_post','gks_sms.sms_result','gks_sms.status_name','gks_sms.model');


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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_sms.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_erp_app_mobile.erp_app_mobile_name
FROM (gks_sms 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_sms.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_erp_app_mobile ON gks_sms.erp_app_mobile_id = gks_erp_app_mobile.id_erp_app_mobile
where 1=1 ".$where . $search_where;




if (empty($sorted['sql'])) {
	$query .= " ORDER BY gks_sms.date_add desc, gks_sms.id desc";
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

<style>
.tdtext {
  overflow-wrap: anywhere; 
}  
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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate_add', gks_lang('Ημερομηνία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprovider', gks_lang('Μέσω')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogks_nickname', gks_lang('Χρήστης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyfrom', gks_lang('Από')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyto', gks_lang('Προς')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" nowrap="nowrap"><?php echo gks_lang('Μήνυμα');?></th>
        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofolder', gks_lang('Φάκελος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparts', 'Parts'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socost', gks_lang('Κόστος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somyret', '<span title="'.gks_lang('Αποτέλεσμα αποστολής').'">'.gks_lang('Αποτ.').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soerror', gks_lang('Σφάλμα')); ?></th>  
        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus_name', gks_lang('Κατάσταση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Ενέργεια');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodonedate_date', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία αναφοράς παράδοσης').'">'.gks_lang('Ημερομηνία ΑΠ').'</span>');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodel', gks_lang('Μοντέλο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodelid', '<span class="tooltipster" title="'.gks_lang('Model ID').'">'.gks_lang('mID').'</span>'); ?></th>        

      
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
    <td nowrap class="mytdcm"><?php echo $row['id'];?></td>   
    <td nowrap class="mytdcml"><?php echo showDate(strtotime($row['date_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcml"><?php
      if ($row['sms_provider']=='smsapi') echo 'smsapi';
      else if ($row['sms_provider']=='gks_erp_app_mobile') {
        echo $row['erp_app_mobile_name'];
      }
    ?></td>  
    <td nowrap class="mytdcml"><?php echo $row['gks_nickname'];?></td>  
    <td nowrap class="mytdcml"><?php echo $row['myfrom'];?></td>      
    <td nowrap class="mytdcml"><?php echo $row['myto'];?></td>      
    <td        class="mytdcml tdtext"><?php echo ($row['Message'] != '' ? nl2br_gks($row['Message']) : nl2br_gks($row['Message_post']));?></td>      
    <td nowrap class="mytdcm"><?php echo $row['sms_folder'];?></td>      
    <td nowrap class="mytdcm"><?php echo $row['Parts'];?></td>      
    <td nowrap class="mytdcm"><?php if (isset($row['cost']) and $row['cost']<>0) echo $row['cost'];?></td>      
    <td nowrap class="mytdcm"><img src="img/<?php echo $row['myret'];?>.png" border="0" width="16"></td>    
    <td nowrap class="mytdcm"><?php if ($row['myret']==0) {
      $sms_result=trim_gks($row['sms_result']);
      if (0 === strpos($sms_result, 'ERROR:'))
        echo substr($sms_result,6);
      else
        echo $sms_result;
    } ?></td>      
    <td        class="mytdcm"><span class="sms_status sms_status_<?php echo $row['status'];?>"><?php echo $row['status_name'];?></span> </td>
    <td        class="mytdcm"><?php
      if (gks_sms_can_resend_status($row['status'],$row['model'])) {
        echo '<i class="gks_sms_command_resend fas fa-sync-alt tooltipster" title="'.gks_lang('Επαναποστολή').'" data-id="'.$row['id'].'"></i>';  
      }
    ?></td>
    <td nowrap class="mytdcml"><?php if (isset($row['donedate_date'])) echo showDate(strtotime($row['donedate_date']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php echo $row['model'];?></td>      
    <td nowrap class="mytdcm"><?php echo $row['model_id'];?></td>      
  </tr>
<?php    
    }
?>
</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

    
<div class="container-fluid" style="margin-top:100px;">
  <div class="row">
    <div class="col-md-12">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κωδικοί σφαλμάτων για SMSAPI');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>  

<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable" border="0" cellspacing="0" cellpadding="5" align="center">

<thead>
<tr>
<th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='10%'>ERROR</th>
<th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='90%'>Description</th>
</tr>
</thead><tbody>
<tr>
<td style="text-align: left">7</td>
<td style="text-align: left">Short links disabled on account</td>
</tr>
<tr>
<td style="text-align: left">8</td>
<td style="text-align: left">Error in request (Please report)</td>
</tr>
<tr>
<td style="text-align: left">11</td>
<td style="text-align: left">The message is too long or there is no message or parameter: nounicode is set and special characters (including Polish characters) are used</td>
</tr>
<tr>
<td style="text-align: left">12</td>
<td style="text-align: left">The message has more parts than defined in &amp;max_parts parameter.</td>
</tr>
<tr>
<td style="text-align: left">13</td>
<td style="text-align: left">Lack of valid phone numbers (invalid or blacklisted numbers)</td>
</tr>
<tr>
<td style="text-align: left">14</td>
<td style="text-align: left">Wrong sender name</td>
</tr>
<tr>
<td style="text-align: left">17</td>
<td style="text-align: left">FLASH message cannot contain special characters</td>
</tr>
<tr>
<td style="text-align: left">18</td>
<td style="text-align: left">Invalid number of parameters</td>
</tr>
<tr>
<td style="text-align: left">19</td>
<td style="text-align: left">Too many messages in one request (number of messages in on request exceeded, when short link is used, limit is 100)</td>
</tr>
<tr>
<td style="text-align: left">20</td>
<td style="text-align: left">Invalid number of IDX parameters</td>
</tr>
<tr>
<td style="text-align: left">25</td>
<td style="text-align: left">Parameters &amp;normalize and &amp;datacoding mustn't appear in the same request.</td>
</tr>
<tr>
<td style="text-align: left">27</td>
<td style="text-align: left">Too long IDX parameter. Maximum 255 chars.</td>
</tr>
<tr>
<td style="text-align: left">28</td>
<td style="text-align: left">Invalid time_restriction parameter value. Available values are: follow, ignore or nearest_available</td>
</tr>
<tr>
<td style="text-align: left">30</td>
<td style="text-align: left">Wrong UDH parameter when &amp;datacoding=bin</td>
</tr>
<tr>
<td style="text-align: left">40</td>
<td style="text-align: left">No group with given name in contacts database</td>
</tr>
<tr>
<td style="text-align: left">41</td>
<td style="text-align: left">Chosen group is empty</td>
</tr>
<tr>
<td style="text-align: left">50</td>
<td style="text-align: left">Messages may be scheduled up to 3 months in the future</td>
</tr>
<tr>
<td style="text-align: left">52</td>
<td style="text-align: left">Too many attempts of sending messages to one number (maximum 10 attempts within 60s)</td>
</tr>
<tr>
<td style="text-align: left">53</td>
<td style="text-align: left">Not unique idx parameter, message with the same idx has been already sent and &amp;check_idx=1.</td>
</tr>
<tr>
<td style="text-align: left">54</td>
<td style="text-align: left">Wrong date - (only unix timestamp and ISO 8601)</td>
</tr>
<tr>
<td style="text-align: left">56</td>
<td style="text-align: left">The difference between date sent and expiration date can't be less than 1 and more than 72 hours.</td>
</tr>
<tr>
<td style="text-align: left">57</td>
<td style="text-align: left">The number is blacklisted for this user.</td>
</tr>
<tr>
<td style="text-align: left">59</td>
<td style="text-align: left">Number is added to OptOut list</td>
</tr>
<tr>
<td style="text-align: left">70</td>
<td style="text-align: left">Invalid URL in notify_url parameter.</td>
</tr>
<tr>
<td style="text-align: left">74</td>
<td style="text-align: left">Sending date doesn't match date sent restrictions set for the account.</td>
</tr>
<tr>
<td style="text-align: left">76</td>
<td style="text-align: left">Invalid characters in request parameters</td>
</tr>
<tr>
<td style="text-align: left">93</td>
<td style="text-align: left">Message parameters and group parameter cannot be used simultaneously.</td>
</tr>
<tr>
<td style="text-align: left">94</td>
<td style="text-align: left">Not allowed to send messages with link.</td>
</tr>
<tr>
<td style="text-align: left">98</td>
<td style="text-align: left">Your account is restricted. You can send only to number used in account registration process.</td>
</tr>
<tr>
<td style="text-align: left">101</td>
<td style="text-align: left">Invalid authorization info</td>
</tr>
<tr>
<td style="text-align: left">102</td>
<td style="text-align: left">Invalid username or password</td>
</tr>
<tr>
<td style="text-align: left">103</td>
<td style="text-align: left">Insufficient credits on Your account</td>
</tr>
<tr>
<td style="text-align: left">104</td>
<td style="text-align: left">No such template</td>
</tr>
<tr>
<td style="text-align: left">105</td>
<td style="text-align: left">Wrong IP address (for IP filter turned on)</td>
</tr>
<tr>
<td style="text-align: left">106</td>
<td style="text-align: left">Invalid cut.li link</td>
</tr>
<tr>
<td style="text-align: left">110</td>
<td style="text-align: left">Action not allowed for your account</td>
</tr>
<tr>
<td style="text-align: left">112</td>
<td style="text-align: left">Sending messages to phone numbers from this country is restricted on your account</td>
</tr>
<tr>
<td style="text-align: left">200</td>
<td style="text-align: left">Unsuccessful message submission</td>
</tr>
<tr>
<td style="text-align: left">201</td>
<td style="text-align: left">System internal error (please report)</td>
</tr>
<tr>
<td style="text-align: left">202</td>
<td style="text-align: left">Too many simultaneous request, the message won't be sent</td>
</tr>
<tr>
<td style="text-align: left">203</td>
<td style="text-align: left">Too many requests. Please try again later. Refers to <code>https://api.smsapi.pl/subusers</code></td>
</tr>
<tr>
<td style="text-align: left">301</td>
<td style="text-align: left">ID of messages doesn't exist</td>
</tr>
<tr>
<td style="text-align: left">400</td>
<td style="text-align: left">Invalid message ID of a status response</td>
</tr>
<tr>
<td style="text-align: left">401</td>
<td style="text-align: left">Token don't have permissions for this action</td>
</tr>
<tr>
<td style="text-align: left">409</td>
<td style="text-align: left">Value already exists</td>
</tr>
<tr>
<td style="text-align: left">997</td>
<td style="text-align: left">HTTP requests have been disabled for your account, please use secure connection (HTTPS)</td>
</tr>
<tr>
<td style="text-align: left">998</td>
<td style="text-align: left">Short url service is unavailable</td>
</tr>
<tr>
<td style="text-align: left">999</td>
<td style="text-align: left">System internal error (please report)</td>
</tr>
<tr>
<td style="text-align: left">1000</td>
<td style="text-align: left">Action available only for the main user</td>
</tr>
<tr>
<td style="text-align: left">1001</td>
<td style="text-align: left">Invalid action (expected one of following parameters: add_user, set_user, get_user, credits)</td>
</tr>
<tr>
<td style="text-align: left">1010</td>
<td style="text-align: left">Sub-user-s adding error</td>
</tr>
<tr>
<td style="text-align: left">1020</td>
<td style="text-align: left">Sub-user-s editing error</td>
</tr>
<tr>
<td style="text-align: left">1021</td>
<td style="text-align: left">No data to edit, at least one parameter has to be edited</td>
</tr>
<tr>
<td style="text-align: left">1030</td>
<td style="text-align: left">Checking user's data error</td>
</tr>
<tr>
<td style="text-align: left">1032</td>
<td style="text-align: left">Sub-user doesn't exist for this main user. This error may also occur when trying to get a subuser which does not contain main account username prefix and not using without_prefix parameter.</td>
</tr>
<tr>
<td style="text-align: left">1100</td>
<td style="text-align: left">Sub-user's data error</td>
</tr>
<tr>
<td style="text-align: left">1110</td>
<td style="text-align: left">Invalid new sub-user's name</td>
</tr>
<tr>
<td style="text-align: left">1111</td>
<td style="text-align: left">New sub-user's name is missing</td>
</tr>
<tr>
<td style="text-align: left">1112</td>
<td style="text-align: left">Too short new sub-user's name, it has to contain minimum 3 characters</td>
</tr>
<tr>
<td style="text-align: left">1113</td>
<td style="text-align: left">Too long new sub-user's name, sub-user's name with main user's prefix may contain maximum 32 characters</td>
</tr>
<tr>
<td style="text-align: left">1114</td>
<td style="text-align: left">Not allowed characters occurred in sub-user's name, following are allowed: letters [A – Z], digits [0 – 9] and following others @, -, _  and .</td>
</tr>
<tr>
<td style="text-align: left">1115</td>
<td style="text-align: left">Another user with the same name exists</td>
</tr>
<tr>
<td style="text-align: left">1120</td>
<td style="text-align: left">New sub-user's password error</td>
</tr>
<tr>
<td style="text-align: left">1121</td>
<td style="text-align: left">Password too short</td>
</tr>
<tr>
<td style="text-align: left">1122</td>
<td style="text-align: left">Password too long</td>
</tr>
<tr>
<td style="text-align: left">1123</td>
<td style="text-align: left">Password should be hashed with MD5</td>
</tr>
<tr>
<td style="text-align: left">1130</td>
<td style="text-align: left">Credit limit error</td>
</tr>
<tr>
<td style="text-align: left">1131</td>
<td style="text-align: left">Parameter limit ought to be a number</td>
</tr>
<tr>
<td style="text-align: left">1140</td>
<td style="text-align: left">Month limit error</td>
</tr>
<tr>
<td style="text-align: left">1141</td>
<td style="text-align: left">Parameter month_limit ought to be a number</td>
</tr>
<tr>
<td style="text-align: left">1150</td>
<td style="text-align: left">Wrong senders parameter value, binary 0 and 1 values allowed</td>
</tr>
<tr>
<td style="text-align: left">1160</td>
<td style="text-align: left">Wrong phonebook parameter value, binary 0 and 1 values allowed</td>
</tr>
<tr>
<td style="text-align: left">1170</td>
<td style="text-align: left">Wrong active parameter value, binary 0 and 1 values allowed</td>
</tr>
<tr>
<td style="text-align: left">1180</td>
<td style="text-align: left">Parameter info error</td>
</tr>
<tr>
<td style="text-align: left">1183</td>
<td style="text-align: left">Parameter info is too long</td>
</tr>
<tr>
<td style="text-align: left">1190</td>
<td style="text-align: left">API password for sub-user's account error</td>
</tr>
<tr>
<td style="text-align: left">1192</td>
<td style="text-align: left">Wrong API password length (password hashed with MD5 should have 32 chars)</td>
</tr>
<tr>
<td style="text-align: left">1193</td>
<td style="text-align: left">API password should be hashed with MD5</td>
</tr>
<tr>
<td style="text-align: left">2001</td>
<td style="text-align: left">Invalid action (parameter add, status, delete or list expected)</td>
</tr>
<tr>
<td style="text-align: left">2010</td>
<td style="text-align: left">New sender name adding error</td>
</tr>
<tr>
<td style="text-align: left">2030</td>
<td style="text-align: left">Sender name's status checking error</td>
</tr>
<tr>
<td style="text-align: left">2031</td>
<td style="text-align: left">Such sender name doesn't exist</td>
</tr>
<tr>
<td style="text-align: left">2060</td>
<td style="text-align: left">Default sender name error</td>
</tr>
<tr>
<td style="text-align: left">2061</td>
<td style="text-align: left">Sender name has to be active for setting it as default</td>
</tr>
<tr>
<td style="text-align: left">2062</td>
<td style="text-align: left">This sender name is already set as default</td>
</tr>
<tr>
<td style="text-align: left">2100</td>
<td style="text-align: left">Data error</td>
</tr>
<tr>
<td style="text-align: left">2110</td>
<td style="text-align: left">Sender name error</td>
</tr>
<tr>
<td style="text-align: left">2111</td>
<td style="text-align: left">Sender name is missing for adding new sender name action (parameter &amp;add is empty)</td>
</tr>
<tr>
<td style="text-align: left">2112</td>
<td style="text-align: left">Invalid Sender Name's name (i.e. Name containing special chars or name too long),  sender name may contain up to 11 chars, chars allowed: a-z A-Z 0-9 - . [space]</td>
</tr>
<tr>
<td style="text-align: left">2115</td>
<td style="text-align: left">Sender name already exists</td>
</tr>
<tr>
<td style="text-align: left">4000</td>
<td style="text-align: left">General contacts database error.</td>
</tr>
<tr>
<td style="text-align: left">4001</td>
<td style="text-align: left">Action not available for this account.</td>
</tr>
<tr>
<td style="text-align: left">4002</td>
<td style="text-align: left">Invalid action.</td>
</tr>
<tr>
<td style="text-align: left">4003</td>
<td style="text-align: left">Invalid parameter usage.</td>
</tr>
<tr>
<td style="text-align: left">4004</td>
<td style="text-align: left">Too large limit parameter value (i.e. for list_contacts action maximum value is 200).</td>
</tr>
<tr>
<td style="text-align: left">4100</td>
<td style="text-align: left">General groups' action error.</td>
</tr>
<tr>
<td style="text-align: left">4101</td>
<td style="text-align: left">Group not found.</td>
</tr>
<tr>
<td style="text-align: left">4110</td>
<td style="text-align: left">General group's name error.</td>
</tr>
<tr>
<td style="text-align: left">4111</td>
<td style="text-align: left">Invalid group's name.</td>
</tr>
<tr>
<td style="text-align: left">4112</td>
<td style="text-align: left">Group's name cannot not be empty.</td>
</tr>
<tr>
<td style="text-align: left">4113</td>
<td style="text-align: left">Group's name too short (min 2 chars).</td>
</tr>
<tr>
<td style="text-align: left">4114</td>
<td style="text-align: left">Group's name too long (max 32 chars).</td>
</tr>
<tr>
<td style="text-align: left">4115</td>
<td style="text-align: left">Forbidden chars appeared in group's name.</td>
</tr>
<tr>
<td style="text-align: left">4116</td>
<td style="text-align: left">Group already exists.</td>
</tr>
<tr>
<td style="text-align: left">4121</td>
<td style="text-align: left">Invalid Info field value for groups.</td>
</tr>
<tr>
<td style="text-align: left">4122</td>
<td style="text-align: left">Too long Info field value for contact (max 200 chars).</td>
</tr>
<tr>
<td style="text-align: left">4200</td>
<td style="text-align: left">General contact error.</td>
</tr>
<tr>
<td style="text-align: left">4201</td>
<td style="text-align: left">Contact not found.</td>
</tr>
<tr>
<td style="text-align: left">4210</td>
<td style="text-align: left">General phone number error.</td>
</tr>
<tr>
<td style="text-align: left">4211</td>
<td style="text-align: left">Invalid phone number.</td>
</tr>
<tr>
<td style="text-align: left">4212</td>
<td style="text-align: left">Contact has to contain a phone number.</td>
</tr>
<tr>
<td style="text-align: left">4213</td>
<td style="text-align: left">Phone number is too short.</td>
</tr>
<tr>
<td style="text-align: left">4214</td>
<td style="text-align: left">Phone number is too long.</td>
</tr>
<tr>
<td style="text-align: left">4220</td>
<td style="text-align: left">First name error.</td>
</tr>
<tr>
<td style="text-align: left">4221</td>
<td style="text-align: left">First name too short (min 2 chars).</td>
</tr>
<tr>
<td style="text-align: left">4222</td>
<td style="text-align: left">First name too long (max 100 chars).</td>
</tr>
<tr>
<td style="text-align: left">4230</td>
<td style="text-align: left">Last name error.</td>
</tr>
<tr>
<td style="text-align: left">4231</td>
<td style="text-align: left">Last name too long (min2 chars).</td>
</tr>
<tr>
<td style="text-align: left">4232</td>
<td style="text-align: left">Last name too long (max 100 chars).</td>
</tr>
<tr>
<td style="text-align: left">4240</td>
<td style="text-align: left">Contact Info field error.</td>
</tr>
<tr>
<td style="text-align: left">4241</td>
<td style="text-align: left">Too long Info field value for contact (max 200 chars).</td>
</tr>
<tr>
<td style="text-align: left">4250</td>
<td style="text-align: left">E-mail address error for this contact.</td>
</tr>
<tr>
<td style="text-align: left">4260</td>
<td style="text-align: left">Birthdate error of this contact.</td>
</tr>
<tr>
<td style="text-align: left">4270</td>
<td style="text-align: left">Group error for this contact.</td>
</tr>
<tr>
<td style="text-align: left">4271</td>
<td style="text-align: left">Group not found.</td>
</tr>
<tr>
<td style="text-align: left">4272</td>
<td style="text-align: left">Group name is necessary for group actions.</td>
</tr>
<tr>
<td style="text-align: left">4280</td>
<td style="text-align: left">Gender error.</td>
</tr>
</tbody></table>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdonedate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdonedate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate' || sname=='fdonedate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate' || sname=='fdonedate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  }); 
 
  $('.gks_sms_command_resend').click(gks_sms_command_resend_click);
  
  
});

</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


