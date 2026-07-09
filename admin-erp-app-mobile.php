<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('gks ERP App Mobile');
$nav_active_array=array('manage','manage_erp_app_mobile');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app_mobile','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_erp_app_mobile','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_erp_app_mobile',['from'=>'list']);

$filters = array();
$filters[] = array(
    'name' => 'fenable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_erp_app_mobile.erp_app_mobile_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),    'sql' => "gks_erp_app_mobile.erp_app_mobile_disabled=0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργή'), 'sql' => "gks_erp_app_mobile.erp_app_mobile_disabled<>0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_erp_app_mobile.id_erp_app_mobile'),
  array('name' => 'soname', 'field' => 'gks_erp_app_mobile.erp_app_mobile_name'),
  array('name' => 'soerp_number', 'field' => 'gks_erp_app_mobile.erp_app_mobile_phonenumber'),
  array('name' => 'sosms', 'field' => 'gks_erp_app_mobile.erp_app_mobile_cost_per_sms'),
  array('name' => 'sodescr', 'field' => 'gks_erp_app_mobile.erp_app_mobile_descr'),
  array('name' => 'sotoken', 'field' => 'gks_erp_app_mobile.erp_app_mobile_token'),
  array('name' => 'sourl', 'field' => 'gks_erp_app_mobile.erp_app_mobile_url'),
  array('name' => 'soport', 'field' => 'gks_erp_app_mobile.erp_app_mobile_port'),
  
  
  array('name' => 'sosort', 'field' => 'gks_erp_app_mobile.erp_app_mobile_sortorder'),
  array('name' => 'sodisable', 'field' => 'gks_erp_app_mobile.erp_app_mobile_disabled'),
  array('name' => 'sopurl', 'field' => 'gks_erp_app_mobile.erp_app_mobile_url,gks_erp_app_mobile.erp_app_mobile_port'),
  array('name' => 'solan', 'field' => 'gks_erp_app_mobile.erp_app_mobile_lan_ip'),
  array('name' => 'sowan', 'field' => 'gks_erp_app_mobile.erp_app_mobile_wan_ip'),
  array('name' => 'solast', 'field' => 'gks_erp_app_mobile.erp_app_mobile_last_ping'),
  array('name' => 'sover', 'field' => 'gks_erp_app_mobile_ping.appver'),
  array('name' => 'soostime', 'field' => 'gks_erp_app_mobile_ping.ostime'),
  array('name' => 'sopersonname', 'field' => 'gks_erp_app_mobile_ping.personname'),
  array('name' => 'sophonenumber', 'field' => 'gks_erp_app_mobile_ping.phonenumber'),
  array('name' => 'soosver', 'field' => 'gks_erp_app_mobile_ping.osver'),

  array('name' => 'sodisk', 'field' => 'gks_erp_app_mobile_ping.hdwd'),
  array('name' => 'soscreen', 'field' => 'gks_erp_app_mobile_ping.screw,gks_erp_app_mobile_ping.screh'),
  array('name' => 'somac', 'field' => 'gks_erp_app_mobile_ping.mac'),
  						
  						

  						
  						
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array(
'gks_erp_app_mobile.erp_app_mobile_name',
'gks_erp_app_mobile.erp_app_mobile_phonenumber',
'gks_erp_app_mobile.erp_app_mobile_descr',
'gks_erp_app_mobile.erp_app_mobile_token',
'gks_erp_app_mobile.erp_app_mobile_url',
'gks_erp_app_mobile.erp_app_mobile_lan_ip',
'gks_erp_app_mobile.erp_app_mobile_wan_ip',
'gks_erp_app_mobile_ping.appver',
'gks_erp_app_mobile_ping.personname',
'gks_erp_app_mobile_ping.phonenumber',
'gks_erp_app_mobile_ping.osver',
'gks_erp_app_mobile_ping.mac',
'user_on_app.gks_nickname',

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

//SELECT SQL_CALC_FOUND_ROWS gks_erp_app_mobile.*, 
//other.erp_app_mobile_descr AS other_descr
//FROM gks_erp_app_mobile 
//LEFT JOIN gks_erp_app_mobile AS other ON gks_erp_app_mobile.monada_parent_id = other.id_erp_app_mobile

$sql = "select SQL_CALC_FOUND_ROWS gks_erp_app_mobile.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_erp_app_mobile_ping.ostime, gks_erp_app_mobile_ping.personname, gks_erp_app_mobile_ping.phonenumber, gks_erp_app_mobile_ping.osver, gks_erp_app_mobile_ping.appver, 
gks_erp_app_mobile_ping.hdwd, gks_erp_app_mobile_ping.screw, gks_erp_app_mobile_ping.screh, gks_erp_app_mobile_ping.mac,
user_on_app.gks_nickname as gks_nickname_user_on_app

".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((gks_erp_app_mobile
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_erp_app_mobile.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_erp_app_mobile.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as user_on_app ON gks_erp_app_mobile.erp_app_mobile_user_id = user_on_app.ID
where 1=1 ".$where . $search_where;

      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY erp_app_mobile_last_ping desc,erp_app_mobile_sortorder,erp_app_mobile_name";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-erp-app-mobile-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας εφαρμογής');?></a>
      <a class="btn btn-primary" href="https://tools.gks.gr/download/gks_ERP_App_Mobile.apk" target="_blank"><?php echo gks_lang('Λήψη');?> <i class="fas fa-download"></i></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_erp_app_mobile">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soerp_number', '<span class="tooltipster" title="'.gks_lang('Αριθμός Κινητού').'">'.gks_lang('Αριθμός').'</span'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosms', '<span class="tooltipster" title="'.gks_lang('Κόστος ανά SMS').'">'.gks_lang('SMS').'</span'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotoken', gks_lang('Κλειδί')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sourl', gks_lang('URL')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soport', gks_lang('Πόρτα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_on_app', gks_lang('Χρήστης App')); ?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopurl', gks_lang('Public URL')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solan', gks_lang('Lan IP')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowan', gks_lang('WAN IP')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solast', '<span class="tooltipster" title="'.gks_lang('Τελευταία σύνδεση').'">'.gks_lang('Σύνδεση').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sover', '<span class="tooltipster" title="'.gks_lang('Έκδοση gks ERP App Mobile').'">'.gks_lang('Ver').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soostime', '<span class="tooltipster" title="'.gks_lang('Ώρα Κινητού').'">'.gks_lang('Ώρα').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopersonname', '<span class="tooltipster" title="'.gks_lang('Όνομα χρήστη').'">'.gks_lang('Χρήστης').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophonenumber', '<span class="tooltipster" title="'.gks_lang('Αριθμός κινητού').'">'.gks_lang('Αριθμός').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soosver', '<span class="tooltipster" title="'.gks_lang('Έκδοση OS').'">'.gks_lang('OS').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisk', '<span class="tooltipster" title="'.gks_lang('Ελεύθερος χώρος').'">'.gks_lang('Δίσκος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soscreen', gks_lang('Οθόνη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somac', '<span class="tooltipster" title="'.gks_lang('Mac Address').'">'.gks_lang('Mac').'</span>'); ?></th>        
      



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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_erp_app_mobile'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-erp-app-mobile-item.php?id=<?php echo $row['id_erp_app_mobile'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_erp_app_mobile'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_erp_app_mobile'];?>" data-model="gks_erp_app_mobile"></i></td>
        </tr>      
      </table>
    </td>
    <td nowrap class="mytdcml"><?php echo $row['erp_app_mobile_name'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['erp_app_mobile_country'].$row['erp_app_mobile_phonenumber'];?></td>
    <td nowrap class="mytdcm"><?php echo myNumberFormatNo0Local($row['erp_app_mobile_cost_per_sms'],true);?></td>
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php echo $row['erp_app_mobile_descr'];?></div></div></td>
    <td class="mytdcml"><?php echo $row['erp_app_mobile_token'];?></td>
    
    
    <td class="mytdcm"><?php echo $row['erp_app_mobile_url'];?></td>
    <td class="mytdcm"><?php echo $row['erp_app_mobile_port'];?></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['erp_app_mobile_user_id'];?>"><?php echo $row['gks_nickname_user_on_app'];?></a></td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['erp_app_mobile_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['erp_app_mobile_sortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><img src="img/<?php echo $row['erp_app_mobile_disabled']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    
    
    <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $public_url='';
      if ($row['erp_app_mobile_url']=='frp') {
        if (trim_gks($row['erp_app_mobile_token'])!='') {
          $public_url='http://'.$row['erp_app_mobile_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
        }
      } else {
        if (trim_gks($row['erp_app_mobile_url'])!='' and $row['erp_app_mobile_port']>0) {
          $public_url='http://'.$row['erp_app_mobile_url'].':'.$row['erp_app_mobile_port'];
        }
      }
      if ($public_url!='') echo '<a href="'.$public_url.'" target="_blank">'.$public_url.'</a>';      
    ?></div></div></td>
    <td class="mytdcm" nowrap><?php echo $row['erp_app_mobile_lan_ip'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['erp_app_mobile_wan_ip'];?></td>
    <td class="mytdcm" nowrap><?php
      //if (isset($row['erp_app_mobile_last_ping'])) echo showDate(strtotime($row['erp_app_mobile_last_ping']), 'd/m/Y H:i:s', 1);
  
      if (isset($row['erp_app_mobile_last_ping'])) {
        if (strtotime($row['erp_app_mobile_last_ping']) > time()-60*60) //mia ora, to elaxisto einai 15 lepta
          echo '<span class="gks_erp_app_alive">'.secondsago(strtotime($row['erp_app_mobile_last_ping'])).'</span>';
        else 
          echo '<span class="gks_erp_app_not_alive">'.secondsago(strtotime($row['erp_app_mobile_last_ping'])).'</span>';
      }
    ?></td>
    
    
    
    <td class="mytdcm" nowrap><?php echo $row['appver'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['ostime'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['personname'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['phonenumber'];?></td>
 
                    
    <td class="mytdcm" nowrap><?php echo $row['osver'];?></td>
    <td class="mytdcm" nowrap><?php if ($row['hdwd']>0) echo number_format($row['hdwd']/1024,2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).' GB';?></td>
    <td class="mytdcm" nowrap><?php if ($row['screw']>0) echo $row['screw'].'x'.$row['screh'];;?></td>
    <td class="mytdcml" nowrap><?php 
      if (trim_gks($row['mac'])!='') {
        $parts=explode('|',$row['mac']);
        echo implode('<br>', $parts);} 
    ?></td>
    
    
    

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#table_gks_erp_app_mobile > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_erp_app_mobile',mylist,'#table_gks_erp_app_mobile > tbody');
    }
  });  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


