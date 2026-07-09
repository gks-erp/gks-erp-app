<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Υποκαταστήματα');
$nav_active_array=array('manage','manage_company_subs');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_subs','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
//print '<pre>';print_r($perm_company_ids);die();


$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_company_subs','edit',0);





$gks_custom_prepare = gks_custom_table_item_prepare('gks_company_subs',['from'=>'list']);


$filters = array();


$filters[] = array(
    'name' => 'fcompany',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_company_subs.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_company.id_company AS id, gks_company.company_title AS descr
FROM gks_company_subs LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
WHERE (((gks_company.id_company) Is Not Null))
GROUP BY gks_company.id_company, gks_company.company_title
ORDER BY gks_company.company_sortorder, gks_company.company_title;",
);

$filters[] = array(
    'name' => 'fcity',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Πόλη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_company_subs.company_sub_poli like '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT company_sub_poli as descr, company_sub_poli as id FROM gks_company_subs WHERE company_sub_poli<>'' GROUP BY company_sub_poli ORDER BY company_sub_poli",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_company_subs.company_sub_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_company_subs.company_sub_nomos_id as id, gks_nomoi.nomos_descr as descr
FROM gks_company_subs LEFT JOIN gks_nomoi ON gks_company_subs.company_sub_nomos_id = gks_nomoi.id_nomos
WHERE (((gks_nomoi.id_nomos) Is Not Null))
GROUP BY gks_company_subs.company_sub_nomos_id, gks_nomoi.nomos_descr
ORDER BY gks_nomoi.nomos_descr"
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_company_subs.company_sub_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_company_subs.company_sub_country_id as id, gks_country.country_name as descr
FROM gks_company_subs LEFT JOIN gks_country ON gks_company_subs.company_sub_country_id = gks_country.id_country
WHERE (((gks_country.id_country) Is Not Null))
GROUP BY gks_company_subs.company_sub_country_id, gks_country.country_name
ORDER BY gks_country.country_name"
);


$filters[] = array(
    'name' => 'fpoint',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Στίγμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Έχει στίγμα'),     'sql' => "company_sub_map_latitude <> 0 or company_sub_map_longitude <>0"),
        array('value' => 2, 'text' => gks_lang('Δεν έχει στίγμα'), 'sql' => "company_sub_map_latitude = 0 and company_sub_map_longitude = 0"),
    ),
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),     'sql' => "company_sub_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργή'),  'sql' => "company_sub_disable <> 0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_company_subs.id_company_sub'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'sotitle', 'field' => 'gks_company_subs.company_sub_title'),
  						array('name' => 'soccs', 'field' => 'numsubs.ccc_subs'),
  						array('name' => 'soccu', 'field' => 'numusers.ccc_users'),
  						array('name' => 'soccw', 'field' => 'numwares.ccc_ware'),
  						array('name' => 'soeponimia', 'field' => 'gks_company_subs.company_sub_eponimia'),
  						array('name' => 'soafm', 'field' => 'gks_company_subs.company_sub_afm'),
  						array('name' => 'sodoy', 'field' => 'gks_company_subs.company_sub_doy'),
  						array('name' => 'sodrast', 'field' => 'gks_company_subs.company_sub_epaggelma'),
  						array('name' => 'sophone', 'field' => 'gks_company_subs.company_sub_phone'),
  						array('name' => 'soemail', 'field' => 'gks_company_subs.company_sub_email'),
  						array('name' => 'soodos', 'field' => 'gks_company_subs.company_sub_odos'),
  						array('name' => 'soperioxi', 'field' => 'gks_company_subs.company_sub_perioxi'),
  						array('name' => 'sopoli', 'field' => 'gks_company_subs.company_sub_poli'),
  						array('name' => 'sotk', 'field' => 'gks_company_subs.company_sub_tk'),
  						array('name' => 'socountry', 'field' => 'gks_country.country_name'),
  						array('name' => 'sonomos', 'field' => 'gks_nomoi.nomos_descr'),
  						array('name' => 'sodisable', 'field' => 'gks_company_subs.company_sub_disable'),
              array('name' => 'sosort', 'field' => 'gks_company_subs.company_sub_sortorder'),  						
              
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_company.company_title',
'gks_company.company_eponimia',
'gks_company_subs.company_sub_title',
'gks_company_subs.company_sub_eponimia',
'gks_company_subs.company_sub_phone',
'gks_company_subs.company_sub_email',
'gks_company_subs.company_sub_odos',
'gks_company_subs.company_sub_perioxi',
'gks_company_subs.company_sub_poli',
'gks_company_subs.company_sub_tk',
'gks_country.country_name',
'gks_nomoi.nomos_descr',
);
$search_fields=array_merge($search_fields,$gks_custom_prepare['sql_search_fields']);



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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_company_subs.*, gks_country.country_name, gks_nomoi.nomos_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
company_title,company_eponimia, 
numusers.ccc_users,numwares.ccc_ware
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((gks_company_subs 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company) 
LEFT JOIN gks_country ON gks_company_subs.company_sub_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_company_subs.company_sub_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_company_subs.company_sub_related_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN (
  SELECT company_sub_id, Count(id_company_users) AS ccc_users FROM gks_company_users GROUP BY company_sub_id
) as numusers on gks_company_subs.id_company_sub= numusers.company_sub_id)
LEFT JOIN (
  SELECT company_sub_id, Count(id_warehouse) AS ccc_ware FROM gks_warehouses GROUP BY company_sub_id
) as numwares on gks_company_subs.id_company_sub= numwares.company_sub_id
where 1=1 " .$where . $search_where;

if (count($perm_id_company_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_ids).")";

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title";
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

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-company-sub-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου υποκαταστήματος');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_company_subs">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Διακριτικός Τίτλος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeponimia', gks_lang('Επωνυμία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Χρώμα');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccw', '<span class="tooltipster" title="'.gks_lang('Αποθήκες').'">'.gks_lang('Αποθ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccu', '<span class="tooltipster" title="'.gks_lang('Υπάλληλοι').'">'.gks_lang('Υπαλ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap<?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', gks_lang('email')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', gks_lang('Οδός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soperioxi', gks_lang('Περιοχή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotk', gks_lang('ΤΚ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonomos', gks_lang('Νομός')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry', gks_lang('Χώρα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Στίγμα');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreluser', gks_lang('Επαφή')); ?></th>   
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>        

<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>

    </tr>
</thead>
<tbody>


    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_company_sub'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-company-sub-item.php?id=<?php echo $row['id_company_sub'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_company_sub'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_company_sub'];?>" data-model="gks_company_subs"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td class="mytdcml"><?php echo $row['company_sub_title'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_eponimia'];?></td>
    <td style="background-color: <?php echo $row['company_sub_color'];?>"></td>
    <td nowrap class="mytdcm"><?php echo $row['ccc_ware'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['ccc_users'];?></td>

    <td class="mytdcml"><?php echo $row['company_sub_phone'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_email'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_odos'].' '.$row['company_sub_arithmos'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_perioxi'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_poli'];?></td>
    <td class="mytdcml"><?php echo $row['company_sub_tk'];?></td>
    <td class="mytdcml"><?php echo $row['nomos_descr'];?></td>
    <td class="mytdcml"><?php echo $row['country_name'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['company_sub_map_latitude']==0 and $row['company_sub_map_longitude']==0) {
        $pos_company_sub=0;
      } else {
        $pos_company_sub=1;
      }?>
      <img src="img/<?php echo $pos_company_sub;?>.png" border="0" width="16"></td>
      </td>
    
    <td class="mytdcm"><?php echo myimg010r($row['company_sub_disable']);?></td>
    <td ><?php echo '<a href="admin-users-item.php?id='.$row['company_sub_related_user_id'].'">'.$row['gks_nickname'].'</a>';?></td> 
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['company_sub_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['company_sub_sortorder'];?></span>
    </td>
<?php } ?>

<?php
  echo gks_custom_table_list_rows($gks_custom_prepare,$row);
?>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });

  $('#table_gks_company_subs > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_company_subs',mylist,'#table_gks_company_subs > tbody');
    }
  });

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

