<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//admin-production-posto-change-order-state.php

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$from_order_state='090indelivery';
//if (isset($_GET['from_order_state'])) $from_order_state=trim_gks($_GET['from_order_state']);

$to_order_state='100completed';
//if (isset($_GET['to_order_state'])) $to_order_state=trim_gks($_GET['to_order_state']);



$my_page_title=gks_lang('Αλλαγή κατάσταση παραγγελίας');
$nav_active_array=array('production','production_posto_select');


db_open();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_orders_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','view',0);
$perm_orders_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','edit',0);
$perm_orders_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','add',0);
$perm_orders_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','delete',0);


stat_record();




$today_for_ddate = date('Y-m-d', _time_user(time(), 1));
$today_for_ddate = strtotime($today);
//echo $today_for_ddate; die();

$today_for_ddate_ayrio=$today_for_ddate+24*60*60;
$today_for_ddate_xthes=$today_for_ddate-24*60*60;




$filters = array();

$filters[] = array(
  'name' => 'fddate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Παράδοσης'),
  'has_custom_date' => true,
  'field' => 'gks_orders.ddate', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.ddate','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10'=>array(
    array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),     'sql' => "gks_orders.ddate is null"),
    array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),         'sql' => "gks_orders.ddate is not null"),
  
  )]),


);

$filters[] = array(
  'name' => 'fpriority',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Προτεραιότητα'),
  'multiselect' => true,
  'field'  => "gks_orders.order_priority = %V%",
  'has_custom_default' => -1,
  //		'mywherepos'=>1,
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.order_priority is null"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.order_priority is not null"),
      array('value' => 1, 'text' => '1',                        'sql' => "gks_orders.order_priority=1"),
      array('value' => 2, 'text' => '2',                        'sql' => "gks_orders.order_priority=2"),
      array('value' => 3, 'text' => '3',                        'sql' => "gks_orders.order_priority=3"),
      array('value' => 4, 'text' => '4',                        'sql' => "gks_orders.order_priority=4"),
      array('value' => 5, 'text' => '5',                        'sql' => "gks_orders.order_priority=5"),
  ),


);



$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_orders.order_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.order_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
  'name' => 'fppososto',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Ποσοστό'),
  'multiselect' => true,
  'field'  => "production_pososto = %V%",
  'has_custom_default' => -1,
  //		'mywherepos'=>1,
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "production_pososto is null"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "production_pososto is not null"),
      array('value' => 1, 'text' => '0',                        'sql' => "production_pososto=0"),
      array('value' => 2, 'text' => '1-25',                     'sql' => "(production_pososto>0  and production_pososto<=25)"),
      array('value' => 3, 'text' => '26-50',                    'sql' => "(production_pososto>25 and production_pososto<=50)"),
      array('value' => 4, 'text' => '51-75',                    'sql' => "(production_pososto>50 and production_pososto<=75)"),
      array('value' => 5, 'text' => '76-99',                    'sql' => "(production_pososto>75 and production_pososto<100)"),
      array('value' => 6, 'text' => '100',                      'sql' => "production_pososto=100"),
  ),


);
$filters[] = array(
  'name' => 'fptime',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Χρόνος'),
  'multiselect' => true,
  'field'  => "production_sum_time = %V%",
  'has_custom_default' => -1,
  //		'mywherepos'=>1,
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "production_sum_time is null"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "production_sum_time is not null"),
      array('value' => 1, 'text' => '00:00',                    'sql' => "production_sum_time=0"),
      array('value' => 2, 'text' => '00:00-01:00',              'sql' => "(production_sum_time>0  and production_sum_time<=60)"),
      array('value' => 3, 'text' => '01:00-05:00',              'sql' => "(production_sum_time>60 and production_sum_time<=300)"),
      array('value' => 4, 'text' => '05:00-10:00',              'sql' => "(production_sum_time>300 and production_sum_time<=600)"),
      array('value' => 5, 'text' => '10:00-15:00',              'sql' => "(production_sum_time>600 and production_sum_time<900)"),
      array('value' => 6, 'text' => '15:00-',                   'sql' => "production_sum_time>=900"),
  ),


);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_orders.id_order'),
  						array('name' => 'soddate', 'field' => 'gks_orders.ddate'),
  						array('name' => 'sopriority', 'field' => 'gks_orders.order_priority'),
  						array('name' => 'sood', 'field' => 'gks_orders.order_date'),
  						array('name' => 'soorderidp', 'field' => 'gks_orders.id_order'),
  						array('name' => 'socustomer', 'field' => GKS_WP_TABLE_PREFIX.'users_pelatis.gks_nickname'),
  						array('name' => 'sostate', 'field' => 'gks_orders.order_state'),
  						array('name' => 'sopososto', 'field' => 'gks_orders.production_pososto, gks_orders.production_ergasies_total'),
  						array('name' => 'sotime', 'field' => 'gks_orders.production_sum_time'),
  						array('name' => 'souedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  						
           );

$search_fields = array(
GKS_WP_TABLE_PREFIX.'users_pelatis.gks_nickname',

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
 




$sql="select SQL_CALC_FOUND_ROWS gks_orders.*,
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users_pelatis.gks_nickname AS gks_nickname_pelatis
from (gks_orders
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_pelatis ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users_pelatis.ID
where order_state='".$db_link->escape_string($from_order_state)."' " .
$where . $search_where;




if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_orders.ddate, gks_orders.order_priority desc, gks_orders.id_order";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
$res_numrows = $db_link->query($sql_numrows);
$row_numrows = $res_numrows->fetch_assoc();
$total_records = $row_numrows['found_rows'];

$pages = ceil($total_records / $rows_per_page) - 1;

$paging = array('records' => '', 'total' => '', 'pages' => '');
$url = $_SERVER['SCRIPT_NAME'].'?from_order_state='.$from_order_state;
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);




pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?from_order_state='.$from_order_state;
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}



include_once('_my_header_admin.php');

$row_array=array();
while ($row = $result->fetch_assoc()) {
  $row_array[]=$row;
}


?>

<link href="css/admin-production-posto-change-order-state.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

 
<div style="width:calc(100% - 0px);height:10px;background-color:lightblue;margin:0px 0px 20px 0px">
  <div id="psososto_refresh" style="width:100%;height:10px;background-color:darkblue;"></div>
</div>


<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-2" style="text-align:center">
      <a href="admin-production-posto-select.php">
        <button type="button" class="btn btn-primary" data-id="9"><?php echo gks_lang('Επιλογή Πόστου');?></button>
      </a>      
    </div>
    <div class="col-sm-8" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div>
        <?php echo gks_lang('Αναζήτηση ID Παραγγελίας');?>:  <input type="text" class="form-control form-control-sm" id="gks_filter_id" style="display: inline;max-width: 100px;"/>
      </div>
      <?php
      gks_plugins_functions_run('admin_production_posto_change_order_state_header',array(
      ));
      ?>
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
    <tr>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><a href="?from_order_state=<?php echo $from_order_state;?>">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap>X</th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'soddate', gks_lang('Παράδοση')).'<br>'; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopriority', '<span class="tooltipster" title="'.gks_lang('Προτεραιότητα').'">'.gks_lang('Προτερ.').'</span>'); 
        ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sood', gks_lang('Ημερομηνία')); ?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'soorderidp', '<span class="tooltipster" title="'.gks_lang('Παραγωγή Παραγγελίας').'">'.gks_lang('Π.Παρ.','part2').'</span>'); 
        ?></th> 
               
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socustomer', gks_lang('Πελάτης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Σχόλιο από Παραγγελία');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Σχόλιο Παραγωγής');?></th>
        
        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostate', gks_lang('Κατάσταση')); ?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo gks_lang('Ενέργεια');?></th>
        

        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopososto', '<span title="'.gks_lang('Ποσοστό Παραγωγής').'">'.gks_lang('Ποσοστό').'</span>').'<br>'; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sotime', gks_lang('Χρόνος')); 
          
        ?></th>
                  
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souedit', gks_lang('Χρήστης')); ?></th>    
        
        

           
    </tr>
</thead>
<tbody>
  
    <?php
    

    
    $i = 0;
    foreach ($row_array as &$row) {

     
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> order_table_tr" data-order-id="<?php echo $row['id_order'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm">

      <table cellpadding=0 cellspacing=0 style="width:100%;padding: 0px;margin: 0px;border-width: 0px;">
        <tr style="background-color: transparent;">
          <td style="width:33%;text-align:left;   border-width: 0px;padding: 0px 0px 0px 0px;margin: 0px;"  ><a href="admin-orders-item.php?id=<?php echo $row['id_order'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td style="width:34%;text-align:center; border-width: 0px;padding: 0px 4px 0px 4px;margin: 0px;"><?php echo $row['id_order'];?></td>
          <?php if ($perm_orders_delete) {?>
          <td style="width:33%;text-align:right;  border-width: 0px;padding: 0px 0px 0px 0px;margin: 0px;" ><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_order'];?>" data-model="gks_orders"></i></td>
          <?php } ?>
        </tr>  
      </table>
      <?php
      gks_plugins_functions_run('admin_production_posto_change_order_state_td_id',array(
        'row'=>&$row,
      ));
      ?>
           
   
    </td>
    <td nowrap class="mytdcm">
      <input id="checkbox<?php echo $row['id_order'];?>" type="checkbox" value="<?php echo $row['id_order'];?>" class="myccheckbox"/>
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
      <div class="order_priority" data-rateyo-rating="<?php if (isset($row['order_priority'])) echo $row['order_priority'];?>"></div>
      
      
    </td>   
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['order_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>  


    <td class="mytdcm">
      <a href="admin-production-item.php?id=<?php echo $row['id_order'];?>" title="<?php echo gks_lang('Παραγωγή Παραγγελίας');?>"><?php echo $row['id_order'];?></a>
      
    </td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname_pelatis'];?></a></td>

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp='';
      if (!empty($row['notes'])) $temp.=gks_lang('Πελάτης').': <b>'.nl2br_gks($row['notes']).'</b><br>';
      if (!empty($row['subnotes'])) $temp.=gks_lang('Πελάτης (Συν)').': <b>'.nl2br_gks($row['subnotes']).'</b><br>';
      if (!empty($row['note_production'])) $temp.=gks_lang('Παραγωγή').': <b>'.nl2br_gks($row['note_production']).'</b><br>';
      if (!empty($row['note_logistirio'])) $temp.=gks_lang('Λογιστήριο').': <b>'.nl2br_gks($row['note_logistirio']).'</b><br>';
      
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
      echo $temp;
    ?></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $sql_comments="SELECT prod_comments FROM gks_production_line WHERE order_id=".$row['id_order']." and prod_comments<>'' ORDER BY user_id_edit;";
      //echo $sql_comments;
      $result_comments = $db_link->query($sql_comments); 
      if (!$result_comments) {debug_mail(false,'error sql',$sql_comments); die('sql error');}
      $temp=array();
      while ($row_comments = $result_comments->fetch_assoc()) {
        $temp[]=nl2br_gks($row_comments['prod_comments']);
      }
      echo implode('<br>',$temp);
    ?></div></div></td>     
    
    
    
    <td nowrap class="mytdcm"><span class="order_state_<?php echo $row['order_state'];?>"><?php echo getOrderStateDescr($row['order_state']);?></span></td>
    <td  class="mytdcm">
      <i class="fas fa-check-circle mybtn_change_order_state" data-id="<?php echo $row['id_order'];?>" ></i>
    </td>

   
    <td class="mytdcm"> 
    <?php if ($row['production_ergasies_total']>0) {?>
      <a href="admin-production-item.php?id=<?php echo $row['id_order'];?>">
      <div class="progress" style="background-color: darkgray;">
        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo number_format($row['production_pososto'],2,'.','');?>%" aria-valuenow="<?php echo number_format($row['production_pososto'],2,'.','');?>" aria-valuemin="0" aria-valuemax="100">
          <?php echo number_format($row['production_pososto'],2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);?>%
        </div>
      </div>  
    
      </a>               
    <?php } 
    if ($row['production_sum_time']>0) {
      echo '<div>'.time_duration_format($row['production_sum_time']).'</div>';
    }
    ?>
    </td>

        
    <td class="mytdcm" style="font-size:0.8rem"><a href="admin-users-item.php?id=<?php echo $row['user_id_edit'];?>"><?php echo $row['gks_nickname_edit'];?></a></td>

        

    

  </tr>
<?php    
    }
?>
</tbody>
<tfoot>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th class="table-secondary" scope="col" nowrap class="mytdcm" colspan="2"></td>
    </th>
    <th class="table-secondary" scope="col" nowrap class="mytdcm" style="vertical-align: middle;">
      <input id="checkboxall" type="checkbox" value="0" class="myccheckboxall"/>
    </th>
    <th class="table-secondary" scope="col" nowrap class="mytdcm" colspan=100>
      <?php echo gks_lang('Με τα επιλεγμένα');?>: 
      <span id="checkboxall_run" class="btn btn-primary btn-sm"><?php echo gks_lang('Ορισμός κατάστασης σε');?> <span class="order_state_<?echo $to_order_state;?>"><?php echo getOrderStateDescr($to_order_state);?></span></span>

    </th>
    
  </tr>


</tfoot>
</table>


<?php mytablepages($paging, $total_records); ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;
 

var from_php_from_order_state='<?php echo $from_order_state;?>';
var from_php_to_order_state='<?php echo $to_order_state;?>';

  
var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

var gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend=[];
var gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend_mass=[];


  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
 
  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fddate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fddate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname=='fddate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdate_add' || sname=='fddate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  }); 

  
  
});
</script>

<script src="js/admin-production-posto-change-order-state.js?v=<?php echo $gks_cache_version;?>"></script>


<?php

gks_plugins_functions_run('admin_production_posto_change_order_state_scripts_before_footer',array(
  'id'=>&$id,
));

//db_close();
include_once('_my_footer_admin.php');


