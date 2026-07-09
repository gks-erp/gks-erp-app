<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Καταγραφές Μικρό URL');
$nav_active_array=array('crm','crm_urlshort_hit');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_urlshort_hit','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_urlshort_hit','edit',0);





$filters = array();

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Προσθήκης'),
  'has_custom_date' => true,
  'field' => 'gks_urlshort_hit.mydate_add', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_urlshort_hit.mydate_add','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_country.id_country=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_country.id_country as id, gks_country.country_name as descr
    FROM (gks_urlshort_hit 
    LEFT JOIN gks_stat_ips ON gks_urlshort_hit.myip = gks_stat_ips.ip) 
    LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
    WHERE (((gks_country.id_country) Is Not Null))
    GROUP BY gks_country.id_country, gks_country.country_name
    ORDER BY gks_country.country_name;",
);

$filters[] = array(
    'name' => 'furlshort_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μικρό URL'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_urlshort_hit.urlshort_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_urlshort_hit.urlshort_id as id, concat(gks_urlshort.shorturl,' (',urlsort_descr,')') as descr
    FROM gks_urlshort_hit LEFT JOIN gks_urlshort ON gks_urlshort_hit.urlshort_id = gks_urlshort.id_urlshort
    WHERE (((gks_urlshort.id_urlshort) Is Not Null))
    GROUP BY gks_urlshort_hit.urlshort_id, gks_urlshort.shorturl
    ORDER BY gks_urlshort.shorturl,urlsort_descr",
);

$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_urlshort_hit.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_urlshort_hit LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_urlshort_hit.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);
$filters[] = array(
    'name' => 'fchannel',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κανάλι Πωλήσεων'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_urlshort_hit.crm_channel_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
    FROM gks_urlshort_hit LEFT JOIN gks_crm_channel_sale ON gks_urlshort_hit.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
    WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
    GROUP BY gks_crm_channel_sale.id_crm_channel_sale, gks_crm_channel_sale.crm_channel_sale_descr, gks_crm_channel_sale.crm_channel_sale_sortorder
    ORDER BY gks_crm_channel_sale.crm_channel_sale_sortorder",
);
$filters[] = array(
    'name' => 'fchcontact',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Επαφή Πωλήσεων'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_urlshort_hit.crm_channel_contact_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM gks_urlshort_hit LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_urlshort_hit.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);
$filters[] = array(
    'name' => 'fcampain',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Καμπάνια'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_urlshort_hit.crm_channel_campain_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
    FROM gks_urlshort_hit 
    LEFT JOIN gks_ads_campain ON gks_urlshort_hit.crm_channel_campain_id = gks_ads_campain.id_ads_campain
    WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
    GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
    ORDER BY gks_ads_campain.ads_campain_name",
);




$sortable = array(
		array('name' => 'soid', 'field' => 'gks_urlshort_hit.id_urlshort_hit'),
		array('name' => 'sodate', 'field' => 'gks_urlshort_hit.mydate_add'),
		array('name' => 'soxora', 'field' => 'gks_country.country_name'),
		array('name' => 'soip', 'field' => 'gks_urlshort_hit.myip'),
		array('name' => 'sosid', 'field' => 'gks_urlshort.shorturl'),
		array('name' => 'sosession', 'field' => 'gks_urlshort_hit.sessionid'),
		array('name' => 'sopage', 'field' => 'gks_urlshort_hit.pageurl'),
		array('name' => 'soqs', 'field' => 'gks_urlshort_hit.query_string'),
		array('name' => 'soagent', 'field' => 'gks_urlshort_hit.userAgent'),
		array('name' => 'soref', 'field' => 'gks_urlshort_hit.referer'),
		array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users_add.gks_nickname'),
		
		
		array('name' => 'solong', 'field' => 'gks_urlshort.longurl'),
		array('name' => 'soassid', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
		array('name' => 'sosalescha', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
		array('name' => 'sosalescon', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
		array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
		array('name' => 'socrmcode', 'field' => 'gks_urlshort_hit.crm_channel_code'),
  						
);

$search_fields = array(
'gks_urlshort_hit.myip',
'gks_urlshort.shorturl',
'gks_urlshort_hit.sessionid',
'gks_urlshort_hit.pageurl',
'gks_urlshort_hit.query_string',
'gks_urlshort_hit.userAgent',
'gks_urlshort_hit.referer',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',

'gks_urlshort.longurl',
GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname',
'gks_crm_channel_sale.crm_channel_sale_descr',
GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname',
'gks_ads_campain.ads_campain_name',
'gks_urlshort_hit.crm_channel_code',



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

//SELECT SQL_CALC_FOUND_ROWS gks_urlshort_hit.*, 
//other.shorturl AS other_descr
//FROM gks_urlshort_hit 
//LEFT JOIN gks_urlshort_hit AS other ON gks_urlshort_hit.monada_parent_id = other.id_urlshort

$sql = "select SQL_CALC_FOUND_ROWS gks_urlshort_hit.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_urlshort.urlsort_descr,gks_urlshort.shorturl,gks_urlshort.longurl,
gks_stat_ips.dns_name, gks_stat_ips.country_initials, 
gks_country.country_name
FROM (((((((gks_urlshort_hit

 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_urlshort_hit.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_urlshort_hit.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_urlshort_hit.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_urlshort_hit.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_urlshort_hit.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_urlshort ON gks_urlshort_hit.urlshort_id = gks_urlshort.id_urlshort)
LEFT JOIN gks_stat_ips ON gks_urlshort_hit.myip = gks_stat_ips.ip) 
LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
  

where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_urlshort_hit desc";
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


//echo $url;die();

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

$exview=0;
if (isset($_GET['exview'])) $exview = intval($_GET['exview']);
$sortable_url.='&exview='.$exview;

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<style>
.gks_break_any {
  word-wrap1: break-word;
  overflow-wrap: anywhere;
}  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
        <button class="btn btn-primary" id="idexview" data-val="<?php echo ($exview ? '1' : '0');?>"><?php
          if ($exview) echo gks_lang('Απλή προβολή');
          else echo gks_lang('Εκτεταμένη προβολή');
        ?></button>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>





<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <input style="display:none;" type="text" name="exview" id="exview" value="<?php echo $exview;?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_urlshort_hit">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate', gks_lang('Πότε')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soxora', gks_lang('Χώρα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soip', 'IP'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosid', '<span class="tooltipster" title="'.gks_lang('Μικρό URL').'">'.gks_lang('μ.URL').'</span>'); ?></th>        
    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($exview ? '7.5' : '15');?>%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosession', 'session'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($exview ? '10' : '20');?>%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopage', gks_lang('Σελίδα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($exview ? '10' : '20');?>%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soqs', gks_lang('Παράμετροι')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($exview ? '12.5' : '25');?>%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soagent', '<span class="tooltipster" title="'.gks_lang('Πρόγραμμα περιήγησης').'">'.gks_lang('Agent').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($exview ? '10' : '20');?>%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soref', '<span class="tooltipster" title="'.gks_lang('Σελίδα από την οποία ήρθε στη τρέχουσα').'">'.gks_lang('Από').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', '<span class="tooltipster" title="'.gks_lang('Συνδεδεμένος χρήστης').'">'.gks_lang('Σ.Χρήστης').'</span>'); ?></th>        
    
  <?php if ($exview) { ?>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solong', gks_lang('URL')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soassid', gks_lang('Ανάθεση σε')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosalescha', gks_lang('Κανάλι πωλήσεων')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosalescon', gks_lang('Επαφή Πωλήσεων')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socampain', gks_lang('Καμπάνια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socrmcode', '<span class="tooltipster" title="'.gks_lang('Κωδικός CRM').'">'.gks_lang('Κωδικός').'</span>'); ?></th>        
  
  <?php } ?>        

     
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_urlshort_hit'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-urlshort-item.php?id=<?php echo $row['id_urlshort_hit'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_urlshort_hit'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_urlshort_hit'];?>" data-model="gks_urlshort_hit"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcml" nowrap><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td> 
  	<td nowrap class="mytdcm">
  	  
  	  <?php 
  	  if (isset($row['dns_name'])==false or $row['dns_name']=='' or $row['dns_name']==$row['myip']) $row['dns_name']='<i class="far fa-dot-circle"></i>';
  	  echo gks_stat_country_icon($row); 
  	  ?>
  	  
    	
  	</td>      
    <td nowrap class="mytdcml">
      <a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a>
      <br>
      <a target="_blank" href="https://apps.db.ripe.net/db-web-ui/query?bflag=false&dflag=false&rflag=true&searchtext=<?php print $row['myip']?>&source=RIPE"><?php print $row['dns_name']?></a>  
    </td>
    
    <td nowrap class="mytdcml"><a href="admin-urlshort-item.php?id=<?php echo $row['urlshort_id'];?>"><?php 
      //echo $row['shorturl'];
      echo $row['urlsort_descr'];
      
    ?></a></td>


    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['sessionid'];
    ?></div></div></td> 
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['pageurl'];
    ?></div></div></td> 
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['query_string'];
    ?></div></div></td> 
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['userAgent'];
    ?></div></div></td> 
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['referer'];
    ?></div></div></td> 
    
    
    
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['user_id_add'];?>"><?php echo $row['gks_nickname_add'];?></a></td>

<?php if ($exview) { ?>    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><a href="<?php echo $row['longurl'];?>"><?php echo nl2br_gks($row['longurl']);?></a></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><a href="admin-users-item.php?id=<?php echo $row['assigned_id'];?>"><?php echo nl2br_gks($row['gks_nickname_assigned']);?></a></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['crm_channel_id'];?>"><?php echo nl2br_gks($row['crm_channel_sale_descr']);?></a></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><a href="admin-users-item.php?id=<?php echo $row['crm_channel_contact_id'];?>"><?php echo nl2br_gks($row['crm_channel_contact_gks_nickname']);?></a></div></div></td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><a href="admin-ads-campain-item.php?id=<?php echo $row['crm_channel_campain_id'];?>"><?php echo nl2br_gks($row['ads_campain_name']);?></a></div></div></td>
    <td class="mytdcm"><?php echo $row['crm_channel_code'];?></a></td>

<?php } ?>

    
    

    

   
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort_hit','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort_hit','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_urlshort_hit','delete',0);?>;

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


  $('#idexview').click(function() {
    if ($(this).attr('data-val') == '0') {
      $('#exview').val('1');
    } else {
      $('#exview').val('0');
    }
    $('#filter-form').submit();
  });

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


