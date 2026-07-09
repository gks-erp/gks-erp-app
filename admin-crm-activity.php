<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Δραστηριότητα');
$nav_active_array=array('crm','crm_activity');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_activity','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


gks_get_leads_status($leads_status,$leads_status_styles);

$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);

 



$filters = array();
if ($id==0) {
$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => 1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Νέα'),     'sql' => "activity_status='050new'"),
        array('value' => 2, 'text' => gks_lang('Έγινε'),   'sql' => "activity_status='100done'"),
        array('value' => 3, 'text' => gks_lang('Άκυρο'),   'sql' => "activity_status='200cancel'"),
    ),
);
if (ur_ad()) {
$filters[] = array(
    'name' => 'fcity',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ποιος'),
    'has_custom_default' => -2,
    'multiselect' => true,
    'field'  => "gks_crm_activity.activity_user_id= %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -2, 'text' => gks_lang('Δικά μου'),          'sql' => "gks_crm_activity.activity_user_id=".$my_wp_user_id),
    ),
    'sql' => "SELECT gks_crm_activity.activity_user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_crm_activity LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_crm_activity.activity_user_id<>".$my_wp_user_id." AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
    GROUP BY gks_crm_activity.activity_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);
}

$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τι'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_crm_activity.activity_type_id= %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_crm_activity_type as id, crm_activity_type_descr as descr
    FROM gks_crm_activity_types
    ORDER BY crm_activity_type_descr",
);

$filters[] = array(
  'name' => 'fduedate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Έως πότε'),
  'has_custom_date' => true,
  'field' => 'gks_crm_activity.activity_duedate', 
  'has_custom_default' => 19,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_crm_activity.activity_duedate','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia, 'extra10' => array(
    array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),     'sql' => "gks_crm_activity.activity_duedate is null"),
    array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),         'sql' => "gks_crm_activity.activity_duedate is not null"),
  )]),
 
);

$filters[] = array(
    'name' => 'fobject',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αντικείμενο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "id_crm_activity_object= %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -2, 'text' => gks_lang('Χωρίς αντικείμενο'), 'sql' => "(activity_model is null or activity_model='')"),
    ),
    'sql' => "SELECT gks_crm_activity_objects.id_crm_activity_object AS id, gks_crm_activity_objects.crm_activity_object_descr AS descr
    FROM gks_crm_activity LEFT JOIN gks_crm_activity_objects ON gks_crm_activity.activity_model = gks_crm_activity_objects.crm_activity_object_code
    WHERE (((gks_crm_activity_objects.id_crm_activity_object) Is Not Null))
    GROUP BY gks_crm_activity_objects.id_crm_activity_object, gks_crm_activity_objects.crm_activity_object_descr
    ORDER BY gks_crm_activity_objects.crm_activity_object_descr;",
);

}




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_crm_activity.id_crm_activity'),
  						array('name' => 'sostatus', 'field' => 'gks_crm_activity.activity_status'),
  						array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'sotype', 'field' => 'gks_crm_activity_types.crm_activity_type_descr'),
  						array('name' => 'soduedate', 'field' => 'gks_crm_activity.activity_duedate'),
  						array('name' => 'sosubject', 'field' => 'gks_crm_activity.activity_subject'),
  						array('name' => 'somodel', 'field' => 'gks_crm_activity_objects.crm_activity_object_descr'),
  						
            );

$search_fields = array(
'gks_crm_activity.activity_subject',
'gks_crm_activity.activity_message',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_crm_activity.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_crm_activity_types.crm_activity_type_descr, gks_crm_activity_types.crm_activity_type_icon,
gks_crm_activity_objects.crm_activity_object_descr,gks_crm_activity_objects.crm_activity_object_page
FROM ((((gks_crm_activity 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_activity.mydate_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_activity.mydate_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type) 
LEFT JOIN gks_crm_activity_objects ON gks_crm_activity.activity_model = gks_crm_activity_objects.crm_activity_object_code

where ";
if ($id==0) {
  $sql.="1=1 ".$where . $search_where;
} else {
  $sql.= "gks_crm_activity.id_crm_activity=".$id;
}

if (ur_ad()==false) $sql.=" and gks_crm_activity.activity_user_id=".$my_wp_user_id;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_crm_activity.activity_duedate DESC , gks_crm_activity.id_crm_activity DESC";
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

$rows_array=array();
$objects=array();
while ($row = $result->fetch_assoc()) {
  $rows_array[]=$row;
  if (empty($row['activity_model'])== false and $row['activity_model_id']>0) {
    if (isset($objects[$row['activity_model']])==false) $objects[$row['activity_model']]=array();
    
    if (isset($objects[$row['activity_model']][$row['activity_model_id']])==false) {
      $objects[$row['activity_model']][$row['activity_model_id']]=array();
    }
  }
}

//print '<pre>';print_r($objects);die();
gks_get_activity_objects($objects);
//print '<pre>';print_r($objects);die();

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
      <button style="justify-content: center!important;" type="button" class="btn btn-primary gks_add_new_record" id="new_record"><?php echo gks_lang('Προσθήκη νέας δραστηριότητας');?></button>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>
<?php if ($id==0) {?>
<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>
<?php } else { ?>
<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <a class="btn btn-primary" href="?"><?php echo gks_lang('Αφαίρεση φίλτρου');?></a>
    </div>
  </div>
</div>

<?php } ?>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Ποιος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τι')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soduedate', gks_lang('Έως πότε')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosubject', gks_lang('Θέμα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><?php echo gks_lang('Σχόλιο');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodel', gks_lang('Αντικείμενο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Επαφή');?></th>        
        <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"  nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Αναμενόμενα έσοδα');?>"><?php echo gks_lang('Α.Έσοδα');?></span></th>        
    </tr>
</thead>
<tbody>


    <?php
    //$week_date_ranges=gks_week_date_ranges(true);
    $i = 0;
    //while ($row = $result->fetch_assoc()) {
    foreach ($rows_array as $row) {
         
      $row['contact_name']='ssss';
      $row['contact_id']=1;
      $row['esoda']=1;
      
	$i++;
              $type_icon='';
              if (!empty($row['crm_activity_type_icon'])) {
                $type_icon=$row['crm_activity_type_icon'];
                if (trim_gks($row['activity_color'])!='') {
                  $type_icon=str_replace(' class="', ' style="color:'.$row['activity_color'].'" class="', $type_icon);
                }
                $type_icon.=' ';
              } 	
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> activity_tr_exist" data-id="<?php echo $row['id_crm_activity'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><i class="activity_edit_list enterrow fas fa-pen" 
            data-id="<?php echo $row['id_crm_activity'];?>" 
            data-model="<?php echo $row['activity_model'];?>" 
            data-model_id="<?php echo $row['activity_model_id'];?>"
            title="<?php echo gks_lang('Προβολή');?>"></i></td>
          <td><?php echo $row['id_crm_activity'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" 
            data-deleteafter="gks_fnc_activity_delete_after|<?php echo $row['id_crm_activity'];?>" 
            data-id="<?php echo $row['id_crm_activity'];?>" 
            data-model="gks_crm_activity" data-model="gks_crm_activity"></i></td>
        </tr>      
      </table>
    </td>




    <td nowrap class="mytdcm"><span class="activity_status_<?php echo $row['activity_status'];?>"><?php echo getActivityStatusDescr($row['activity_status']);?></span></td>  
    <td        class="mytdcml"><?php echo $row['gks_nickname'];?></td>  
    <td        class="mytdcml"><?php 
      if ($row['activity_type_id']==4 and $row['calendar_id']>0) { //meeting
        echo $type_icon.'<a href="admin-crm-calendar.php?id='.$row['calendar_id'].'">'.$row['crm_activity_type_descr'].'</a>';
      } else {
        echo $type_icon.$row['crm_activity_type_descr'];
      }      
    ?></td>    
    <td        class="mytdcml"><?php 
      if ($row['activity_notification']==1) {
        echo '<i class="activity_notification_bell fas fa-bell"></i> ';
      }
      if (!empty($row['activity_duedate'])) {
        echo '<span title="'.showDate(strtotime($row['activity_duedate']),'d/m/Y H:i:s',1).'">'.
        secondsago(strtotime($row['activity_duedate'])).
        '</span>';

//        echo getActivityduedateDescr($row['activity_duedate'],$row['activity_status'],$week_date_ranges);
//        if ($row['activity_type_id']==4) { //meeting
//          if ($row['calendar_id']>0) {
//            echo '<br><a href="admin-crm-calendar.php?id='.$row['calendar_id'].'">'.date('H:i', strtotime($row['activity_duedate'])).'</a>';
//          } else {
//            echo '<br>'.date('H:i', strtotime($row['activity_duedate']));
//          }
//        }        
      }
                //echo '<br>'.$row['activity_duedate'];
    ?></td>  
    <td <?php 
      if (trim_gks($row['activity_color'])!='') {
        echo ' style="background-color:'.$row['activity_color'].'"';  
      }
      ?>><?php echo $row['activity_subject'];?></td>    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo nl2br_gks($row['activity_message']);
    ?></div></div></td> 
    <td        class="mytdcml">
      
      <?php 
      //print '<pre>';print_r($objects[$row['activity_model']]);die();
      $obj_name='';
      if (isset($objects[$row['activity_model']][$row['activity_model_id']]['obj_name'])) {
        $obj_name=$objects[$row['activity_model']][$row['activity_model_id']]['obj_name'];
      } else if (isset($row['crm_activity_object_descr']) and $row['activity_model_id']>0) { 
        $obj_name=$row['crm_activity_object_descr'].' id:'.$row['activity_model_id'];
      } else if (isset($row['crm_activity_object_descr'])) {
        $obj_name=$row['crm_activity_object_descr'];
      } else {
        $obj_name=$row['activity_model'];
      }
      
      $obj_link='';
      if (isset($row['crm_activity_object_page']) and $row['activity_model_id']>0) {
        $obj_link=str_replace('%s',$row['activity_model_id'],$row['crm_activity_object_page']);
      }
      
      if ($obj_link!='') {
        echo '<a href="'.$obj_link.'">'.$obj_name.'</a>';
      } else {
        echo $obj_name;
      }
      

      ?>
    </td>  
    <td        class="mytdcml"><?php 
      if (isset($objects[$row['activity_model']][$row['activity_model_id']]['contact_name'])) {
        if ($objects[$row['activity_model']][$row['activity_model_id']]['contact_id']>0) {
          echo '<a href="admin-users-item.php?id='.$objects[$row['activity_model']][$row['activity_model_id']]['contact_id'].'">'.
          $objects[$row['activity_model']][$row['activity_model_id']]['contact_name'].'</a>';
        } else {
          echo $objects[$row['activity_model']][$row['activity_model_id']]['contact_name'];
        }
      } 
    ?></td>  
    <td class="mytdcmr"><?php 
      if (isset($objects[$row['activity_model']][$row['activity_model_id']]['esoda']) and $objects[$row['activity_model']][$row['activity_model_id']]['esoda']!=0) {
        echo myCurrencyFormat($objects[$row['activity_model']][$row['activity_model_id']]['esoda']);
      }
    ?></td>  
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

var from_php_activity_model='';
var from_php_activity_model_id=0;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=-2;
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_activity','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_activity','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_activity','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  function activity_edit_list_click() {
    activity_id=parseInt($(this).attr('data-id'));
    if (isNaN(activity_id)) activity_id=0;
    if (activity_id<=0) return;
    
    from_php_activity_model=($(this).attr('data-model'));

    from_php_activity_model_id=parseInt($(this).attr('data-model_id'));
    if (isNaN(from_php_activity_model_id)) from_php_activity_model_id=0;
    //if (from_php_activity_model_id<=0) return;
    
    activity_add_click(activity_id);
    
  }
  
  $('.activity_edit_list').click(activity_edit_list_click);

  $('#new_record').click(function() {
    from_php_activity_model='';
    from_php_activity_model_id=0;
    activity_add_click(-1);
    return false;
  });
  
  $('#fduedate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fduedate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fduedate' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fduedate' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });

  <?php if ($id>0) {?>
  $('i.activity_edit_list[data-id="<?php echo $id;?>"]').click();  
    
  <?php } ?>

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

