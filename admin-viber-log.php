<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Καταγραφές Viber');
$nav_active_array=array('crm','manage_viber','manage_viberlog');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_viber_msgs','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



if (!isset($_GET) or count($_GET)==0) {
  $sql="UPDATE gks_viber_msgs 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viber_msgs.sender_id = ".GKS_WP_TABLE_PREFIX."users.viber_id 
  SET gks_viber_msgs.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_viber_msgs.sender_id<>''
  AND gks_viber_msgs.user_id=0 
  AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null;";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');

  $sql="UPDATE gks_viber_msgs 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viber_msgs.receiver_id = ".GKS_WP_TABLE_PREFIX."users.viber_id 
  SET gks_viber_msgs.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_viber_msgs.receiver_id<>''
  AND gks_viber_msgs.user_id=0 
  AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null;";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
}



$filters = array();
$filters[] = array(
	'name' => 'fdate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_viber_msgs.mydate',
	'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_viber_msgs.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fuser_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρήστης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "(gks_viber_msgs.user_id = %V% or gks_viber_msgs.other_user_id = '%V%')",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100,  'text' => gks_lang('Μη συνδεδεμένος'),  'sql' => "gks_viber_msgs.user_id=0"),
    ),
    'sql' => "SELECT gks_viber_msgs.user_id as id , ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_viber_msgs LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viber_msgs.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((gks_viber_msgs.user_id)>0))
    GROUP BY gks_viber_msgs.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
);

$filters[] = array(
    'name' => 'fcmd_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εντολή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_viber_msgs.action_cmd_part1 = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        //array('value' => -100,  'text' => gks_lang('Κεντρικά εργαστήρια'),  'sql' => "(ergastirio_onlyone_magazi_id =0 and id_ergastirio not in (21,63,17))"),
        //array('value' => -101,  'text' => gks_lang('Εργαστήρια σε Μαγαζιά'),  'sql' => "(ergastirio_onlyone_magazi_id <> 0 and id_ergastirio not in (21,63,17))"),
        
    ),
    'sql' => "SELECT viber_cmd as id, gks_viber_cmds.viber_descr as descr
    FROM gks_viber_msgs LEFT JOIN gks_viber_cmds ON gks_viber_msgs.action_cmd_part1 = gks_viber_cmds.viber_cmd
    WHERE (((gks_viber_cmds.id_viber_cmds) Is Not Null))
    GROUP BY gks_viber_msgs.action_cmd_part1, gks_viber_cmds.viber_descr
    ORDER BY gks_viber_cmds.viber_descr;",
);


$filters[] = array(
    'name' => 'model',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μοντέλο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field' => "gks_viber_msgs.model = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
    ),
    'sql' => "SELECT model as descr, model as id FROM gks_viber_msgs where model<>'' GROUP BY model ORDER BY model",
    
);



$sortable = array(
  						array('name' => 'somydate', 'field' => 'gks_viber_msgs.mydate'),
  						array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'souname', 'field' => 'gks_viber_msgs.sender_name'),
  						array('name' => 'soulang', 'field' => 'gks_viber_msgs.sender_language'),
  						array('name' => 'soucountry', 'field' => 'gks_viber_msgs.sender_country'),
  						array('name' => 'socmdp', 'field' => 'gks_viber_msgs.action_cmd'),
  						array('name' => 'socmd', 'field' => 'gks_viber_cmds.viber_descr'),
  						array('name' => 'sopar2', 'field' => 'gks_viber_msgs.action_cmd_part2'),
  						array('name' => 'sopar3', 'field' => 'gks_viber_msgs.action_cmd_part3'),
  						array('name' => 'sotext', 'field' => 'gks_viber_msgs.message'),
  						array('name' => 'sootheruser', 'field' => GKS_WP_TABLE_PREFIX.'users_other.gks_nickname'),
  						array('name' => 'sodire', 'field' => 'gks_viber_msgs.sender_id,gks_viber_msgs.receiver_id'),
              array('name' => 'somodel', 'field' => 'gks_sms.model'),
            );



$search_fields = array(
  'gks_viber_msgs.sender_id',
  'gks_viber_msgs.receiver_id',
  'gks_viber_msgs.other_viber_id',
  'gks_viber_cmds.viber_descr',
  GKS_WP_TABLE_PREFIX.'users.gks_nickname',
  GKS_WP_TABLE_PREFIX.'users_other.gks_nickname',
  'gks_viber_msgs.message',
  'gks_viber_msgs.other_message',
  'gks_viber_msgs.sender_name',
  'gks_viber_msgs.sender_language',
  'gks_viber_msgs.sender_country',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_viber_msgs.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users_other.gks_nickname AS other_gks_nickname, 
gks_viber_cmds.viber_descr
FROM ((gks_viber_msgs 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_viber_msgs.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_other ON gks_viber_msgs.other_user_id = ".GKS_WP_TABLE_PREFIX."users_other.ID) 
LEFT JOIN gks_viber_cmds ON gks_viber_msgs.action_cmd_part1 = gks_viber_cmds.viber_cmd
where 1=1 
".$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_viber_msgs desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $query;
//die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'admin-log-emails.php error sql',$sql);
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
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate', gks_lang('Ημερομηνία')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodire', gks_lang('Απο/Προς')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Χρήστης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souname', gks_lang('Όνομα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Εικόνα');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soulang', gks_lang('Γλώσσα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soucountry', gks_lang('Χώρα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socmdp', gks_lang('Εντολή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socmd', gks_lang('Παρ0')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopar2', gks_lang('Παρ1')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopar3', gks_lang('Παρ2')); ?></th>        
     
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Μήνυμα');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodelivery', gks_lang('Παραδόθηκε')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseen', gks_lang('Προβλήθηκε')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sootheruser', gks_lang('Άλλος Χρήστης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Άλλο Μήνυμα');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodel', gks_lang('Μοντέλο')); ?></th>        

    
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
    <td nowrap class="mytdcml"><?php echo showDate(strtotime($row['mydate']), 'd/m/Y H:i:s', 1);?></td>   
    <?php 
      if (trim_gks($row['sender_id'])!='') 
        echo '<td nowrap style="vertical-align:middle;text-align:left;" title="'.gks_lang('Από επαφή').'"><i class="fas fa-sign-in-alt gks_viber_fa-sign-in-alt"></i></td>'; 
      else if (trim_gks($row['receiver_id'])!='') 
        echo '<td nowrap style="vertical-align:middle;text-align:right;"  title="'.gks_lang('Προς επαφή').'"><i class="fas fa-sign-out-alt gks_viber_fa-sign-out-alt"></i></td>';
      else 
        echo '<td nowrap></td>';
    ?> 
    <td nowrap class="mytdcml"><?php 
      if ($row['user_id']>0) 
        echo '<a href="admin-users-item.php?id='.$row['user_id'].'">'.$row['gks_nickname'].'</a>'; 
      else if (trim_gks($row['sender_id'])!='') 
        echo htmlspecialchars_gks($row['sender_id']);
      else if (trim_gks($row['receiver_id'])!='') 
        echo htmlspecialchars_gks($row['receiver_id']);
      
    ?></td>  
    <td nowrap class="mytdcml"><?php if (isset($row['sender_name'])) echo $row['sender_name']; ?></td>  
    <td nowrap class="mytdcm p-0"><?php if (isset($row['sender_avatar'])) echo '<img src="'.$row['sender_avatar'].'" style="max-height:50px;">';?></td>  
    <td nowrap class="mytdcm"><?php if (isset($row['sender_language'])) echo $row['sender_language']; ?></td>  
    <td nowrap class="mytdcm"><?php if (isset($row['sender_country'])) echo $row['sender_country']; ?></td>  
    <td nowrap class="mytdcml"><?php if (isset($row['action_cmd'])) echo $row['action_cmd'];?></td>
    <td nowrap class="mytdcml"><?php if (isset($row['viber_descr'])) echo $row['viber_descr']; ?></td>  
    <td nowrap class="mytdcm"><?php if (isset($row['action_cmd_part2'])) echo $row['action_cmd_part2'];?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['action_cmd_part3'])) echo $row['action_cmd_part3'];?></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php echo nl2br_gks($row['message']);?></div></div></td>  
    <td nowrap class="mytdcm"><?php 
      if (isset($row['delivered'])) 
        echo '<i class="fas fa-check-circle gks_viber_fa-check-circle" title="'.showDate(strtotime($row['delivered']), 'd/m/Y H:i:s', 1).'">';?>
    </td>  
    <td nowrap class="mytdcm"><?php 
      if (isset($row['seen'])) 
        echo '<i class="fas fa-check-circle gks_viber_fa-check-circle" title="'.showDate(strtotime($row['seen']), 'd/m/Y H:i:s', 1).'">';?>
    </td>  
    <td nowrap class="mytdcml"><?php if ($row['other_user_id']>0) echo '<a href="admin-users-item.php?id='.$row['other_user_id'].'">'.$row['other_gks_nickname'].'</a>';?></td>  
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php echo nl2br_gks($row['other_message']);?></div></div></td>  
    <td nowrap class="mytdcml"><?php echo $row['model'];?></td>      
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


