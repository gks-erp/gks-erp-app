<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Ειδοποιήσεις');
$nav_active_array=array('user','notification');


db_open();
stat_record();



$filters = array();

$show_all_users=$my_wp_user_id==ur_ad();

if ($show_all_users) {
  $filters[] = array(
    'name' => 'fuuuid',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Του Χρήστη'),
    'has_custom_default' => -101,
    'multiselect' => true,
    'field'  => "(for_user_id=%V% or sender_id=%V%)",
    'vals' => array(
  //      array('value' => -100, 'text' => gks_lang('Εισερχόμενα'),     'sql' => "gks_notification.for_user_id=".$my_wp_user_id),
        array('value' => -101, 'text' => gks_lang('Τα δικά μου'),      'sql' => "(for_user_id=".$my_wp_user_id." or sender_id=".$my_wp_user_id.")"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
  FROM gks_notification LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification.for_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where ".GKS_WP_TABLE_PREFIX."users.ID<>'.$my_wp_user_id.'
  GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",
    
  );
}

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Προσθήκη'),
  'has_custom_date' => true,
  'field' => 'gks_notification.date_add', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_notification.date_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);

$filters[] = array(
  'name' => 'ffor_date',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Πότε'),
  'has_custom_date' => true,
  'field' => 'gks_notification.for_date', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_notification.for_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);


$filters[] = array(
  'name' => 'finout',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Εισερχόμενα/Εξερχόμενα'),
  'has_custom_default' => -100,
  'multiselect' => true,
  'field'  => "gks_orders.base_type_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Εισερχόμενα'),     'sql' => "gks_notification.for_user_id=".$my_wp_user_id),
      array('value' => -101, 'text' => gks_lang('Εξερχόμενα'),      'sql' => "gks_notification.for_user_id<>".$my_wp_user_id),
  ),
);

$filters[] = array(
  'name' => 'fouid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Συνομιλητής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "(gks_notification.for_user_id = %V%  or gks_notification.sender_id = %V%)",
  'vals' => array(
//      array('value' => -100, 'text' => gks_lang('Εισερχόμενα'),     'sql' => "gks_notification.for_user_id=".$my_wp_user_id),
      array('value' => -101, 'text' => gks_lang('Στον εαυτό μου'),      'sql' => '(gks_notification.for_user_id = '.$my_wp_user_id.' and gks_notification.sender_id = '.$my_wp_user_id.')'),
  ),
  'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
FROM (select uid from (
  SELECT sender_id as uid FROM gks_notification WHERE for_user_id='.$my_wp_user_id.' and sender_id<>'.$my_wp_user_id.' GROUP BY sender_id
  union
  SELECT for_user_id as uid FROM gks_notification WHERE sender_id='.$my_wp_user_id.' and for_user_id<>'.$my_wp_user_id.' GROUP BY for_user_id
) as utable group by uid
)  AS tableuu LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON tableuu.uid = ".GKS_WP_TABLE_PREFIX."users.ID
order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
  
);




$filters[] = array(
  'name' => 'fhas_ok',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Αναγνώστηκε'),
  'has_custom_default' => -101,
  'multiselect' => true,
  'field'  => "gks_orders.base_type_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Ναι'),      'sql' => "gks_notification.has_ok<>0"),
      array('value' => -101, 'text' => gks_lang('Όχι'),      'sql' => "gks_notification.has_ok=0"),
  ),
);



$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_notification.id_notification'),
  						array('name' => 'soadddate', 'field' => 'gks_notification.date_add'),
  						array('name' => 'sofordate', 'field' => 'gks_notification.for_date'),
  						array('name' => 'somessage', 'field' => 'gks_notification.message'),
  						array('name' => 'sohas_ok', 'field' => 'gks_notification.has_ok'),
  						array('name' => 'sook_date', 'field' => 'gks_notification.ok_date'),
  						array('name' => 'somodel', 'field' => 'gks_notification.model'),
  						array('name' => 'somodelid', 'field' => 'gks_notification.model_id'),
  						array('name' => 'sofrom', 'field' => GKS_WP_TABLE_PREFIX.'users_from.gks_nickname'),
  						array('name' => 'soto', 'field' => GKS_WP_TABLE_PREFIX.'users_to.gks_nickname'),

            );

$search_fields = array(
'message',
GKS_WP_TABLE_PREFIX.'users_from.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_to.gks_nickname',


);



$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);




$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';

$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';


$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;
 

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_notification.*, 
".GKS_WP_TABLE_PREFIX."users_from.gks_nickname AS nickname_from, ".GKS_WP_TABLE_PREFIX."users_to.gks_nickname AS nickname_to
FROM (gks_notification 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_from ON gks_notification.sender_id = ".GKS_WP_TABLE_PREFIX."users_from.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_to ON gks_notification.for_user_id = ".GKS_WP_TABLE_PREFIX."users_to.ID

     
where ";
if ($show_all_users== false) $sql.=" (for_user_id=".$my_wp_user_id." or sender_id=".$my_wp_user_id.") ";
else $sql.=" 1=1 ";
$sql.=$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_notification desc";
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
<style>
.myimghas_ok {
  cursor:pointer;  
}
</style>


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
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadddate', gks_lang('Προσθήκη')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofordate', gks_lang('Πότε')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofrom', gks_lang('Από')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soto', gks_lang('Προς')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somessage', gks_lang('Μήνυμα')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohas_ok', gks_lang('Αναγνώστηκε')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sook_date', gks_lang('Αναγνώστηκε Στις')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodel', 'Model'); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somodelid', 'Model ID'); ?></th>        
        
           
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
    <td nowrap class="mytdcm"><?php echo $row['id_notification'];?></td>
    <td nowrap class="mytdcm"><?php echo secondsago(strtotime($row['date_add']));?></td>   
    <td nowrap class="mytdcm"><?php echo secondsago(strtotime($row['for_date']));?></td>   
    <td nowrap class="mytdcm"><?php echo ($my_wp_user_id== $row['sender_id'] ? gks_lang('Εμένα') : $row['nickname_from']);?></td>
    <td nowrap class="mytdcm"><?php echo ($my_wp_user_id== $row['for_user_id'] ? gks_lang('Εμένα') : $row['nickname_to']);?></td>
    
    <td        class="mytdcml"><?php echo $row['message'];?></td>
    <td nowrap class="mytdcm"><?php
      if ($my_wp_user_id== $row['for_user_id']) {
        $myimg='<img class="myimghas_ok" data-id="'.$row['id_notification'].'" data-value="'.(intval($row['has_ok'])==0 ? '0' : '1').'" src="img/';
        if (intval($row['has_ok'])==0) $myimg.='0';
        else $myimg.='1';
        $myimg.='.png" border="0" width="32">';
        echo $myimg;
      } else {
        $myimg='<img src="img/';
        if (intval($row['has_ok'])==0) $myimg.='0';
        else $myimg.='1';
        $myimg.='.png" border="0" width="16" style="opacity:0.5">';
        echo $myimg;
      }
    ?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['ok_date']) and $row['has_ok']!=0) echo showDate(strtotime($row['ok_date']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php echo $row['model'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['model_id']!=0) echo $row['model_id'];?></td>

  </tr>
<?php    
    }
?>

</tbody>
</table>


<?php mytablepages($paging, $total_records); ?>

 
<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <input class="btn btn-primary btn-sm" type="submit" value="<?php echo gks_lang('Ορισμός όλων ως αναγνωσμένων');?>" id="cmdSetAllAsRead">
      
    </div>

  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  

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
  

  $('.myimghas_ok').click(function() {
    
    nid=parseInt($(this).attr('data-id'));
    if (isNaN(nid)) nid=0;
    if (nid<=0) return;
    nval=parseInt($(this).attr('data-value'));
    if (isNaN(nval)) nval=0;
    if (nval==0) nval=1; else nval=0;
      
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-notification_done.php?id=' + nid + '&has_ok=' + nval,
			type: 'POST',
			cache: false,
			dataType: 'json',
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
		    
  });

  $('#cmdSetAllAsRead').click(function() {
    myconfirm(gks_lang('Σίγουρα θέλετε να ορίστε όλα τα μηνύματα ως αναγνωσμένα;'),'notification_set_all_as_read');
    
  });
  
  window.notification_set_all_as_read = function() {
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-notification_done.php?all=1',
			type: 'POST',
			cache: false,
			dataType: 'json',
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});    
    
  }
  
  //myalert()
  //mybigalert('dddd');
  
  

});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


