<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$posto_id=0;
if (isset($_GET['id'])) $posto_id=intval($_GET['id']);
if ($posto_id<=0) {header('Location: admin-production-posto-select.php'); die(); }

$my_page_title=gks_lang('Εργασίες πόστου');
$nav_active_array=array('production','production_posto_select');


db_open();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta_run','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

//$perm_production_posta_run_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run','view',0);
//$perm_production_posta_run_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run','edit',0);
//$perm_production_posta_run_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run','add',0);
$perm_production_posta_run_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run','delete',0);

//$perm_production_posta_run_time_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run_time','view',0);
//$perm_production_posta_run_time_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run_time','edit',0);
//$perm_production_posta_run_time_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run_time','add',0);
//$perm_production_posta_run_time_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_run_time','delete',0);







$sql="select * from gks_production_posta where id_production_posto=".$posto_id;
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
if ($result->num_rows!=1) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Δεν βρέθηκε το πόστο'));
}
$row_post = $result->fetch_assoc();
$production_posto_descr=$row_post['production_posto_descr'];
$my_page_title=gks_lang('Εργασίες πόστου').': '.$production_posto_descr;

stat_record();




$today_for_ddate = date('Y-m-d', _time_user(time(), 1));
$today_for_ddate = strtotime($today);
//echo $today_for_ddate; die();

$today_for_ddate_ayrio=$today_for_ddate+24*60*60;
$today_for_ddate_xthes=$today_for_ddate-24*60*60;




$filters = array();



$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_production_line.id_production_line'),
  						array('name' => 'soddate', 'field' => 'gks_orders.ddate'),
  						array('name' => 'sopriority', 'field' => 'gks_orders.order_priority'),
  						array('name' => 'soorderidp', 'field' => 'gks_production_line.order_id'),
  						array('name' => 'soorderid', 'field' => 'gks_production_line.order_id'),
  						array('name' => 'socustomer', 'field' => GKS_WP_TABLE_PREFIX.'users_pelatis.gks_nickname'),
  						array('name' => 'soeidos', 'field' => 'gks_eshop_products.product_descr'),
  						array('name' => 'sosheets', 'field' => 'gks_orders_products.product_sheets'),
  						array('name' => 'soqua', 'field' => 'gks_orders_products.product_quantity'),
  						array('name' => 'soergasia', 'field' => 'gks_production_ergasies.production_ergasia_descr'),
  						array('name' => 'sopl_state', 'field' => 'gks_production_line.pl_state'),
  						array('name' => 'souseredit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  						array('name' => 'soset', 'field' => 'gks_production_line.set_id'),
           );

$search_fields = array(
//'gks_eshop_products.product_descr',
//'gks_production_ergasies.production_ergasia_descr',
//GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
//GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
//'product_comments',
//'prod_comments',

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
											


$rows_per_page = 1000; //$_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;
 

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_production_posta_ergasies.production_posto_id, gks_production_line.id_production_line, gks_production_line.user_id_edit, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_production_line.pl_state, gks_production_line.order_id, gks_orders.ddate, gks_orders.order_state, gks_orders.order_priority,
gks_orders.user_id, gks_orders.note_production, ".GKS_WP_TABLE_PREFIX."users_pelatis.gks_nickname AS gks_nickname_pelatis, 
gks_production_line.ergasia_id, gks_production_ergasies.production_ergasia_descr, gks_production_line.prod_comments,
gks_production_line.set_id
FROM (((((gks_production_posta_ergasies 
LEFT JOIN gks_production_line ON gks_production_posta_ergasies.production_ergasia_id = gks_production_line.ergasia_id) 
LEFT JOIN gks_orders ON gks_production_line.order_id = gks_orders.id_order) 
LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_production_line.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_pelatis ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users_pelatis.ID)
LEFT JOIN gks_production_posta ON gks_production_posta_ergasies.production_posto_id = gks_production_posta.id_production_posto
WHERE gks_production_posta_ergasies.production_posto_id=".$posto_id." 
AND (
  gks_production_line.pl_state In ('040ready','060pause') or 
  (gks_production_line.pl_state In ('050processing') and gks_production_line.last_user_id_production=".$my_wp_user_id." and gks_production_posta.all_users=0) or 
  (gks_production_line.pl_state In ('050processing') and gks_production_posta.all_users<>0)
  
)" .$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_orders.ddate, gks_orders.order_priority desc, gks_production_line.order_id, gks_production_line.id_production_line";
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
$url = $_SERVER['SCRIPT_NAME'].'?id='.$posto_id;
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);




pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?id='.$posto_id;
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
<link href="css/admin-production-posto-run.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>
 
<div style="width:calc(100% - 0px);height:10px;background-color:lightblue;margin:0px 0px 0px 0px">
  <div id="psososto_refresh" style="width:100%;height:10px;background-color:darkblue;"></div>
</div>
<?php
$row_array=array();
$row_ids=array();
while ($row = $result->fetch_assoc()) {
  $row_array[]=$row;
  $row_ids[]=$row['id_production_line'];
}

if (count($row_ids)>0) {
  $sql="SELECT * FROM gks_production_line_time WHERE production_line_id In (".implode(',',$row_ids).")";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $pid_time=array();
  while ($row = $result->fetch_assoc()) {
    if (isset($pid_time[$row['production_line_id']]) == false) {
      $pid_time[$row['production_line_id']] = array(
        'recs'=>0,
        'secs'=>0,
        'start_at_time'=>0,
      );
    }
    $pid_time[$row['production_line_id']]['recs']++;
    
    if ($row['time_end']==null) {
      $time_start=strtotime($row['time_start']);
      $row['duration_secs']=time()-$time_start;
      if ($time_start > $pid_time[$row['production_line_id']]['start_at_time']) $pid_time[$row['production_line_id']]['start_at_time'] = $time_start;
    }
    $pid_time[$row['production_line_id']]['secs']+=$row['duration_secs'];
  }
//  print '<pre>';   
//  print_r($pid_time);
//  print '</pre>'; 
  
  
  
     
}


?>




<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-2" style="text-align:center">
      <a href="admin-production-posto-select.php">
        <button type="button" class="btn btn-primary" data-id="9"><?php echo gks_lang('Επιλογή Πόστου');?></button>
      </a>      
    </div>
    <div class="col-sm-8" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-2" style="text-align:center">
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
        <?php echo gks_lang('Αναζήτηση ID Παραγγελίας');?>:  <input type="text" class="form-control form-control-sm" id="gks_filter_id" style="display: inline;max-width: 100px;"/>
    </div>
  </div>
</div>

<!--
<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <input style="display:none;" type="text" name="id" value="<?php echo $posto_id;?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>
-->
<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<div class="gks_multiselect_div">
  
  <i data-all="0" id="gks_multiselect_all" class="gks_multiselectcheck far fa-circle tooltipster" title="<?php echo gks_lang('Επιλογή/Αποεπιλογή όλων');?>"></i></span>  
  <span id="gks_multiselect_cc"><?php echo gks_lang('Επιλεγμένα');?>: <span>0</span></span>
  <span id="gks_multiselect_cmd"><?php echo gks_lang('Ενέργεια');?>: </span>

  <div id="gks_multiselect_mybtn">
      <i class="tooltipster fas fa-play-circle  mybtn_processing  mybtn_processing_disable" title="<?php echo getProductionLineStateDescr('050processing');?>"></i>
      <i class="tooltipster fas fa-pause-circle mybtn_pause       mybtn_pause_disable"      title="<?php echo getProductionLineStateDescr('060pause');?>"></i>
      <i class="tooltipster fas fa-check-circle mybtn_completed   mybtn_completed_disable"  title="<?php echo getProductionLineStateDescr('100completed');?>"></i>
      <i class="tooltipster fas fa-minus-circle mybtn_cancelled   mybtn_cancelled_disable"  title="<?php echo getProductionLineStateDescr('020cancelled');?>"></i>
      <i class="tooltipster fas fa-times-circle mybtn_failed      mybtn_failed_disable"     title="<?php echo getProductionLineStateDescr('070failed');?>"></i>
  </div>
</div>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><span class="tooltipster" title="<?php echo gks_lang('Επιλογή');?>"><?php echo gks_lang('Ε');?></span></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><a href="?id=<?php echo $posto_id;?>">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'soddate', gks_lang('Παράδοση')).'<br>'; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopriority', '<span class="tooltipster" title="'.gks_lang('Προτεραιότητα').'">'.gks_lang('Προτερ.').'</span>'); 
        ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'soorderidp', '<span class="tooltipster" title="'.gks_lang('Παραγωγή Παραγγελίας').'">'.gks_lang('Π.Παρ.','part2').'</span>').'<br>'; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'soorderid', '<span class="tooltipster" title="'.gks_lang('Παραγγελία').'">'.gks_lang('Παρ','part2').'</span>'); 
          
        ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socustomer', gks_lang('Πελάτης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo gks_lang('Σχόλιο από Παραγγελία');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soset', 'Set'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo gks_lang('Είδος');?></th>        
        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soergasia', gks_lang('Εργασία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopl_state', gks_lang('Κατάσταση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Σχόλιο Παραγωγής');?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo gks_lang('Ενέργεια');?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Χρόνος');?></th>
           
    </tr>
</thead>
<tbody>
  
    <?php
    

    
    $i = 0;
    foreach ($row_array as &$row) {

      $pl_state=$row['pl_state'];
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> order_table_tr" data-order-id="<?php echo $row['order_id'];?>">
    <td nowrap class="mytdcm"><i class="gks_multiselectcheck far fa-circle"></i></td>
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 style="width:100%;padding: 0px;margin: 0px;border-width: 0px;">
        <tr style="background-color: transparent;">
          <td colspan="2" style="width:100%;text-align:center;border-width:0px;padding:0px;margin:0px;"><?php echo $row['id_production_line'];?></td>
        </tr>
        <tr style="background-color: transparent;">
          <td style="width:50%;text-align:left;   border-width: 0px;padding:0px;margin:0px;"  ><a href="admin-production-line-item.php?id=<?php echo $row['id_production_line'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_production_posta_run_delete) {?>
          <td style="width:50%;text-align:right;  border-width: 0px;padding:0px;margin:0px;" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_production_line'];?>" data-model="gks_production_line"></i></td>
          <?php } ?>
        </tr>
          
      </table>
    </td>
    <td nowrap class="mytdcm">
      <?php 

      if (isset($row['ddate'])) {
        $curr= _time_user(strtotime($row['ddate']),-1);
        //echo date('Y-m-d H:i:s',$curr).'|'.date('Y-m-d H:i:s',$today_for_ddate);
        if ($curr==$today_for_ddate) {
          echo '<div title="'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'" class="ddate_today">'.gks_lang('Σήμερα').'</div>';
        } else if ($curr==$today_for_ddate_ayrio) {
          echo '<div title="'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'" class="ddate_future">'.gks_lang('Αύριο').'</div>';
        } else if ($curr==$today_for_ddate_xthes) {
          echo '<div title="'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'" class="ddate_past">'.gks_lang('Χθες').'</div>';
        } else if ($curr > $today_for_ddate_ayrio) {
          echo '<div title="'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'" class="ddate_future_more">'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'</div>';
        } else if ($curr < $today_for_ddate_ayrio) {
          echo '<div title="'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'" class="ddate_past_more">'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'</div>';
        } else {
          echo '<div>'.showDate(strtotime($row['ddate']), 'd/m/Y', 1).'</div>'; 
        }
      }?>
      <div class="order_priority" data-rateyo-rating="<?php if (isset($row['order_priority'])) echo $row['order_priority']; else echo '0';?>"></div>
      
      
    </td>   



    <td class="mytdcm">
      <a href="admin-production-item.php?id=<?php echo $row['order_id'];?>" title="<?php echo gks_lang('Παραγωγή Παραγγελίας');?>"><?php echo $row['order_id'];?></a>
      <a href="admin-orders-item.php?id=<?php echo $row['order_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Παραγγελία');?>"></i></a>
    </td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname_pelatis'];?></a></td>
    <td class="mytdcml" style="font-size:0.8rem"><?php echo nl2br_gks(htmlentities($row['note_production']));?></td>
    <td nowrap class="mytdcm"><?php echo $row['set_id'];?></td>

    <td class="mytdcml"><?php 
      $html_eidos='';
      $sql_eidi="SELECT gks_eshop_products.id_product, 
      gks_eshop_products.product_code, 
      gks_orders_products.product_descr, 
      gks_orders_products.product_sheets, 
      gks_orders_products.product_quantity,
      gks_orders_products.product_comments,
      CASE
        WHEN gks_eshop_products.product_class='variable_item' THEN
          CASE
            WHEN gks_eshop_products.product_descr<>'' THEN
              gks_eshop_products.product_descr
            ELSE
              CASE
                WHEN gks_eshop_products.product_descr_variable<>'' THEN
                  CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
                ELSE
                  gks_eshop_products_parent.product_descr
              END
          END
        ELSE gks_eshop_products.product_descr
      END as product_descr_p,
      CASE
        WHEN gks_eshop_products.product_class='variable_item' THEN
          CASE
            WHEN gks_eshop_products.product_descr_small<>'' THEN
              gks_eshop_products.product_descr_small
            ELSE
              CASE
                WHEN gks_eshop_products.product_descr_variable<>'' THEN
                  CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
                ELSE
                  gks_eshop_products_parent.product_descr_small
              END
          END
        ELSE gks_eshop_products.product_descr_small
      END as product_descr_small_p
      FROM ((gks_production_line_pid 
      LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product) 
      LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)
      LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
      WHERE (((gks_production_line_pid.production_line_id)=".$row['id_production_line']."))
      ORDER BY gks_eshop_products.product_code;";
      $result_eidi = $db_link->query($sql_eidi);        
      if (!$result_eidi) debug_mail(false,'error sql',$sql_eidi);
      if (!$result_eidi) die('sql error');
      while ($row_eidi = $result_eidi->fetch_assoc()) {
        
        
        $product_descr_small=trim_gks($row_eidi['product_descr_small_p']);  
        if ($product_descr_small!='') {
          $product_descr_small="<table style='max-width:300px' border=0><tr><td>".str_replace('"',"'", $product_descr_small)."</td></tr></table>";
        }
              
        $html_eidos.='<a href="admin-products-item.php?id='.$row_eidi['id_product'].'">'.
        //(trim_gks($row_eidi['product_code'])!='' ? trim_gks($row_eidi['product_code']) : trim_gks($row_eidi['product_descr_p'])).
        trim_gks($row_eidi['product_descr']).
        '</a>'.
        ($product_descr_small=='' ? '' : ' <i class="fas fa-info-circle gks_info_descr tooltipster" title="'.$product_descr_small.'"></i>').
        ' <span title="'.gks_lang('Σελίδες').'">Σ:<b>'.$row_eidi['product_sheets'].'</b></span> <span title="Ποσότητα">Π:<b>'.$row_eidi['product_quantity'].'</b></span><br>';
        if (!empty($row_eidi['product_comments'])) $html_eidos.=nl2br_gks($row_eidi['product_comments']).'<br>';
      }
      //if ($html_eidos!='') $html_eidos=substr($html_eidos, 0, strlen($html_eidos)-2);
      echo $html_eidos;
      
    ?></td>
    
    
    
    <td class="mytdcml"><a href="admin-production-ergasies-item.php?id=<?php echo $row['ergasia_id'];?>"><?php echo $row['production_ergasia_descr'];?></a></td>
    <td nowrap class="mytdcm"><span class="production_line_state_<?php echo $row['pl_state'];?>"><?php echo getProductionLineStateDescr($row['pl_state']);?></span></td>
    <td class="mytdcml" style="font-size:0.8rem">
      <textarea type="text" class="form-control form-control-sm call_sxolio" name="sxolio" id="sxolio_<?php echo $row['id_production_line'];?>"><?php echo htmlentities($row['prod_comments']);?></textarea>
    </td>
    
    <td  class="mytdcm td_buttons" data-id="<?php echo $row['id_production_line'];?>">
      <?php if ($pl_state=='040ready' or $pl_state=='060pause') { ?>
      <i class="tooltipster fas fa-play-circle mybtn_processing" data-id="<?php echo $row['id_production_line'];?>" title="<?php echo getProductionLineStateDescr('050processing');?>"></i>
      <?php } else { ?>
      <i class="tooltipster fas fa-play-circle mybtn_processing_disable" title="<?php echo getProductionLineStateDescr('050processing');?>"></i>
      <?php }
      if ($pl_state=='050processing') { ?>
      <i class="tooltipster fas fa-pause-circle mybtn_pause" data-id="<?php echo $row['id_production_line'];?>" title="<?php echo getProductionLineStateDescr('060pause');?>"></i>
      <?php } else { ?>
      <i class="tooltipster fas fa-pause-circle mybtn_pause_disable" title="<?php echo getProductionLineStateDescr('060pause');?>"></i>
      <?php }
      if ($pl_state=='040ready' or $pl_state=='060pause' or $pl_state=='050processing') { ?>
      <i class="tooltipster fas fa-check-circle mybtn_completed" data-id="<?php echo $row['id_production_line'];?>" title="<?php echo getProductionLineStateDescr('100completed');?>"></i>
      <?php } else {?>
      <i class="tooltipster fas fa-check-circle mybtn_completed_disable" title="<?php echo getProductionLineStateDescr('100completed');?>"></i>
      <?php }
      if ($pl_state!='050processing') {?>
      <i class="tooltipster fas fa-minus-circle mybtn_cancelled" data-id="<?php echo $row['id_production_line'];?>" title="<?php echo getProductionLineStateDescr('020cancelled');?>"></i>
      <?php } else {?>
      <i class="tooltipster fas fa-minus-circle mybtn_cancelled_disable" title="<?php echo getProductionLineStateDescr('020cancelled');?>"></i>
      <?php }
      if ($pl_state=='050processing') {?>
      <i class="tooltipster fas fa-times-circle mybtn_failed" data-id="<?php echo $row['id_production_line'];?>" title="<?php echo getProductionLineStateDescr('070failed');?>"></i>
      <?php } else {?>
      <i class="tooltipster fas fa-times-circle mybtn_failed_disable" title="<?php echo getProductionLineStateDescr('070failed');?>"></i>
      <?php } ?>

    </td>
    <td nowrap class="mytdcm<?php 
      if (isset($pid_time[$row['id_production_line']]) and $pid_time[$row['id_production_line']]['start_at_time']>0) echo ' tdtime';
      ?>" data-id="<?php echo $row['id_production_line'];?>" data-secs="<?php 
      if (isset($pid_time[$row['id_production_line']])) echo $pid_time[$row['id_production_line']]['secs']; else echo '0';
      ?>">
      <?php
      if (isset($pid_time[$row['id_production_line']]) and $pid_time[$row['id_production_line']]['secs']>0) {
         echo time_duration_format($pid_time[$row['id_production_line']]['secs']);
         
      }
      ?>
      </td>
  </tr>
<?php    
    }
?>

</tbody>
</table>



<?php mytablepages($paging, $total_records); ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;
 

var from_php_posto_id=<?php echo $posto_id;?>;

  
var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
 
 

  
  
});
</script>

<script src="js/admin-production-posto-run.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


