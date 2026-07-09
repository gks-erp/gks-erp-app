<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Χρονοπρογραμματισμός εργασιών');
$nav_active_array=array('manage','manage_settings','manage_system_crons');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crons','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();
$filters[] = array(
    'name' => 'fdisable_cron',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "disable_cron=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "gks_crons.disable_cron=0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "gks_crons.disable_cron=1"),
    ),
    
);

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_crons.id_cron'),
	array('name' => 'sofetch_url', 'field' => 'gks_crons.fetch_url'),
	array('name' => 'soevery_seconds', 'field' => 'gks_crons.every_seconds'),
	array('name' => 'solast_run', 'field' => 'gks_crons.last_run'),
	array('name' => 'sonext_run', 'field' => 'gks_crons.next_run'),
	array('name' => 'sonum_runs', 'field' => 'gks_crons.num_runs'),
	array('name' => 'sodisable_cron', 'field' => 'gks_crons.disable_cron'),
);



$search_fields = array(
'gks_crons.fetch_url',
'gks_crons.comments'
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_crons.*
FROM gks_crons
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_crons.id_cron";
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
.gks_open_fetch_url_td {
  padding: 0px !important;
}
.gks_open_fetch_url {
  color: #007bff;
  font-size: 24px;
}


</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-crons-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου χρονοπρογραμματισμού εργασίας');?></a>
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
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="70%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofetch_url', gks_lang('URL')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Εκτέλεση<br>χειροκίνητα');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soevery_seconds', gks_lang('Εκτέλεση κάθε<br>(δευτερόλεπτα)')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solast_run', gks_lang('Προηγούμενη<br>εκτέλεση')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonext_run', gks_lang('Επόμενη<br>εκτέλεση')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" ><?php echo gks_lang('Σχόλιο');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonum_runs', gks_lang('Εκτελέσεις')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable_cron', gks_lang('Ενεργή')); ?></th>
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
          <td><a href="admin-crons-item.php?id=<?php echo $row['id_cron'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_cron'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_cron'];?>" data-model="gks_crons"></i></td>
        </tr>      
      </table>
    </td>
        
    <td class="mytdcml"><?php echo $row['fetch_url'];?></td>
    <td class="mytdcm gks_open_fetch_url_td"><?php 
    $fetch_url=trim_gks($row['fetch_url']);
    if ($fetch_url<>'') {
      if (substr($fetch_url, 0, 4)=='/my/' or $fetch_url=='/wp-cron.php') {
        $fetch_url=GKS_SITE_URL.substr($fetch_url, 1);
      }
      if (filter_var($fetch_url, FILTER_VALIDATE_URL)) {      
        echo '<a href="'.$fetch_url.'" target="_blank"><i class="gks_open_fetch_url fas fa-external-link-square-alt"></i></a>';
      }
    }
    ?></td>
    
    <td class="mytdcm" nowrap><?php  
      $every_seconds=intval($row['every_seconds']);
      if ($every_seconds>0) {
        echo gks_seconds2hoursminsecs($every_seconds).' ('.myNumberFormat($every_seconds).' '.gks_lang('δευτ').')';
      }?></td>
    <td class="mytdcm" nowrap><?php if (isset($row['last_run'])) echo showDate(strtotime($row['last_run']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm" nowrap><?php if (isset($row['next_run'])) echo showDate(strtotime($row['next_run']),'d/m/Y H:i:s',1);?></td>
    <td class="mytdcm"><?php echo nl2br_gks($row['comments']);?></td>
    <td class="mytdcm" nowrap><?php echo $row['num_runs'];?></td>
    <td class="mytdcm" nowrap><?php echo myimg010r($row['disable_cron']);?></td> 
  </tr>
<?php    
    }
?>
</tbody>
</table>


<?php mytablepages($paging, $total_records); ?>

<div class="container-fluid" style="margin-top:50px">
  <div class="row justify-content-md-center">
    <div class="col-md-8">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ρύθμιση server');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('info');?>>  


          <div class="form-group row">
            <div class="col-sm-12">
              <div style="font-size: 0.875rem;padding: 5px 0px 5px 0px;">
              <?php echo gks_lang('Είναι απαραίτητο να μπει ένας χρονοπρογραμματισμός στο server που να εκτελεί την σελίδα');?>
              <br>
              <pre class="gks_precode"><?php echo GKS_SITE_URL;?>my/cron.php</pre>
              <br><?php echo gks_lang('κάθε 1 λεπτό ή το πολύ κάθε 5 λεπτά');?>
              <br>
              <br><?php echo gks_lang('Εάν ο server είναι Linux τότε μπορείτε να προσθέσετε την παρακάτω γραμμή στο αρχείο του crontab');?>
              <pre class="gks_precode">*/5 * * * * root wget --delete-after --no-check-certificate "<?php echo GKS_SITE_URL;?>my/cron.php" > /dev/null 2>&amp;1</pre>
              <br><?php echo gks_lang('Για Windows θα πρέπει να γίνει το αντίστοιχο με την βοήθεια του Task Scheduler');?>
              <br>
              <br><?php echo gks_lang('Εάν δεν υπάρχει αυτός ο χρονοπρογραμματισμός, τότε οι εργασίες δεν θα εκτελούνται αυτόματα εάν δεν είναι κανένας χρήστης συνδεδεμένος');?>
              
              
              </div>
            </div>
          </div>          
          
          
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php

include_once('_my_footer_admin.php');


