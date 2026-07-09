<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

// https://test.easyfilesselection.com/my/admin-vies-check.php?country_ee=de#createfromafm=065938168

$my_page_title=gks_lang('VIES ΕΕ Επαλήθευση αριθ. ΦΠΑ');
$nav_active_array=array('accounting','accounting_vies_check');

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_vies_check','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_vies_check_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','view',0);
$perm_vies_check_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','edit',0);
$perm_vies_check_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','add',0);
$perm_vies_check_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','delete',0);
$perm_vies_check_autocomplete=gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','autocomplete',0);

$perm_wp_users_add   =gks_permission_user_can_action_php($my_wp_user_id,'wp_users','add',0);


$user_companys=gks_get_companys_list();
//print '<pre>';print_r($user_companys);die();

//var_dump($today);die();



$filters = array();

$filters[] = array(
  'name' => 'fdate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_vies_check.mydate', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_vies_check.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fvalid',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποτέλεσμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_vies_check.response_valid = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => "OK",            'sql' => "gks_vies_check.response_valid='1'"),
        array('value' => 2, 'text' => gks_lang('Σφάλμα'),        'sql' => "gks_vies_check.response_valid='0' and (gks_vies_check.error_text is null or gks_vies_check.error_text='')"),
        array('value' => 3, 'text' => gks_lang('Προειδοποίηση'), 'sql' => "gks_vies_check.response_valid='0' and gks_vies_check.error_text<>''"),
    ),
);


$filters[] = array(
  'name' => 'fcountry',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Χώρα'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_vies_check.country_ee = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT gks_vies_check.country_ee AS id, gks_country.country_name AS descr
FROM gks_vies_check LEFT JOIN gks_country ON gks_vies_check.country_ee = gks_country.country_ee
WHERE (((gks_country.country_ee) Is Not Null))
GROUP BY gks_vies_check.country_ee, gks_country.country_name
ORDER BY gks_country.country_name"
);
$filters[] = array(
  'name' => 'ftype',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τύπος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_vies_check.response_traderCompanyType = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_traderCompanyType as id,response_traderCompanyType as descr FROM gks_vies_check where response_traderCompanyType<>'' GROUP BY response_traderCompanyType order by response_traderCompanyType",    
);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_vies_check.id_vies_check'),
  array('name' => 'sodate', 'field' => 'gks_vies_check.mydate'),
  array('name' => 'soafm', 'field' => 'gks_vies_check.afm'),
  array('name' => 'soresult', 'field' => 'gks_vies_check.response_valid, gks_vies_check.error_text'),
  array('name' => 'soerror', 'field' => 'gks_vies_check.error_text'),
  
  array('name' => 'socc_ee', 'field' => 'gks_vies_check.country_ee'),
  array('name' => 'socountry', 'field' => 'gks_country.country_name'),
  array('name' => 'sotitle', 'field' => 'gks_vies_check.response_traderName'),
  array('name' => 'soadress', 'field' => 'gks_vies_check.response_traderAddress'),
  array('name' => 'sotype', 'field' => 'gks_vies_check.response_traderCompanyType'),
  array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
);


$search_fields = array(
'gks_vies_check.afm',
'gks_vies_check.error_text',
'gks_country.country_name',
'gks_vies_check.response_traderName',
'gks_vies_check.response_traderAddress',
'gks_vies_check.response_traderCompanyType',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
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

//SELECT SQL_CALC_FOUND_ROWS gks_vies_check.*, 
//other.lot_descr AS other_descr
//FROM gks_vies_check 
//LEFT JOIN gks_vies_check AS other ON gks_vies_check.monada_parent_id = other.id_vies_check



$sql = "SELECT SQL_CALC_FOUND_ROWS gks_vies_check.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_country.country_name
FROM (gks_vies_check 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_vies_check.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_country ON gks_vies_check.country_ee = gks_country.country_ee
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_vies_check.id_vies_check DESC";
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

$data=array();
$afms=array();
while ($row = $result->fetch_assoc()) {
  $data[]=$row;
  $afm="'".$db_link->escape_string($row['afm'])."'";
  if (in_array($afm,$afms)==false) $afms[]=$afm;
}

$afms_company=array();
$afms_user=array();
if (count($afms)>0) {
//  $sql="SELECT id_company, company_title, company_eponimia, company_afm 
//  FROM gks_company 
//  WHERE company_afm In (".implode(',',$afms).")
//  order by id_company";
//  $result = $db_link->query($sql);  
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); } 
//
//  while ($row = $result->fetch_assoc()) {
//    if (isset($afms_company[$row['company_afm']])==false) {
//      $title=trim_gks($row['company_title']);
//      if ($title=='') $title=trim_gks($row['company_eponimia']);
//      if ($title=='') $title='id: '.$row['id_company'];
//      $afms_company[$row['company_afm']]=array(
//        'id' => $row['id_company'],
//        'title' => $title,
//      );
//    }
//  }
  
  //print '<pre>';print_r($afms_company);die();
  
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, gks_users.eponimia, gks_users.title, gks_users.afm,gks_country.country_ee
  FROM (gks_users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm In (".implode(',',$afms).")
  and gks_country.country_ee<>''
  order by ".GKS_WP_TABLE_PREFIX."users.ID;";
  //echo '<pre>';echo $sql;die();
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

  while ($row = $result->fetch_assoc()) {
    $key=strtoupper($row['country_ee']).'|'.$row['afm'];
    if (isset($afms_user[$key])==false) {
      $title=trim_gks($row['title']);
      if ($title=='') $title=trim_gks($row['eponimia']);
      if ($title=='') $title='id: '.$row['ID'];
      
      $afms_user[$key]=array(
        'id' => $row['ID'],
        'title' => $title,
      );
    }
  }
  
  //print '<pre>';print_r($afms_user);die();
}


$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<style>
.gks_icon {
  width:24px;
}  
.gks_kad { 
  padding:6px;
  border-bottom:1px solid gray;
}
.user_create {
  color: #35dc35;
  cursor: pointer;
  vertical-align11: middle;
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
    <div class="col-sm-12" style="text-align:center;padding-top:10px;padding-bottom:10px;">
      <a href="https://ec.europa.eu/taxation_customs/vies/" target="_blank">https://ec.europa.eu/taxation_customs/vies/</a>
    </div>
    
    <?php if ($perm_vies_check_add) {?>
    <div class="col-sm-12" style="text-align:center">

      <button class="btn btn-primary" id="btn_vies_get" style="margin-left: 10px;"><?php echo gks_lang('Νέα αναζήτηση');?></button>

    </div>
    <?php } ?>
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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate', gks_lang('Ημερομηνία')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soafm', gks_lang('ΑΦΜ')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap><?php echo gks_lang('Επαφή');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult', '<span title="'.gks_lang('Αποτέλεσμα').'" class="tooltipster">'.gks_lang('Αποτ').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soerror', gks_lang('Σφάλμα')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc_ee', '<span title="'.gks_lang('Κωδικός χώρας').'" class="tooltipster">'.gks_lang('ΚΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry', gks_lang('Χώρα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Όνομα Επιχείρησης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadress', gks_lang('Διεύθυνση')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th> 
        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Χρήστης')); ?></th> 


   
        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    //while ($row = $result->fetch_assoc()) {
    foreach ($data as $row) {
      
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_vies_check'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <?php echo $row['id_vies_check'];?>
      <?php if ($perm_vies_check_delete){?>
      <i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_vies_check'];?>" data-model="gks_vies_check"></i>
      <?php }?>
    </td>
    

    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate']), 'd/m/Y H:i:s', 1);?></td>   
    
    <td nowrap class="mytdcm"><?php echo $row['afm'];?></td>
    <?php
      $key=strtoupper($row['country_ee']).'|'.$row['afm'];
      $afm_html='';
      if (isset($afms_company[$key])) 
        $afm_html=' <a href="admin-company-item.php?id='.$afms_company[$key]['id'].'">'.$afms_company[$key]['title'].'</a>';
      else if (isset($afms_user[$key])) 
        $afm_html=' <a href="admin-users-item.php?id='.$afms_user[$key]['id'].'">'.$afms_user[$key]['title'].'</a>';
      else if ($key!='')
        if ($perm_wp_users_add and $row['response_valid']==1) 
          $afm_html=' <i class="fas fa-save user_create" data-val="'.$key.'" title="'.gks_lang('Δημιουργία επαφής').'"></i>';
    
    ?>
    <td        class="mytdcml"><?php echo $afm_html;?></td>

    <td nowrap class="mytdcm p-0"><img src="img/<?php 
      if (trim_gks($row['error_text'])!='') echo 'warning.gif';
      else if ($row['response_valid']==1) echo '1.png';
      else if ($row['response_valid']==0) echo '0.png';
      else echo 'null.png'
      ?>" class="gks_icon"></td>    
    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['error_text'];
    ?></div></div></td> 
    <td nowrap class="mytdcm" ><?php echo $row['country_ee'];?></td>
    <td        class="mytdcml"><?php echo $row['country_name'];?></td>
    <td        class="mytdcml"><?php echo $row['response_traderName'];?></td>
    <td        class="mytdcml"><?php echo $row['response_traderAddress'];?></td>
    <td        class="mytdcml"><?php echo $row['response_traderCompanyType'];?></td>
    

    
    <td        class="mytdcml" ><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    
     
  </tr>
<?php    
    }
?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

 
<div id="dialog_vies" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('VIES ΕΕ Επαλήθευση αριθ. ΦΠΑ');?></div>
    </div>
    <div class="form-group row">  
      <label for="dialog_vies_country_ee" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
      <div class="col-sm-4">
        <?php
        $sql_select="select country_ee,country_name, country_initials from gks_country where country_ee<>'' order by country_name";
        $result_select = $db_link->query($sql_select);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql_select);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        ?>
        <select id="dialog_vies_country_ee" class="form-control  form-control-sm" style="width1: unset;display1: inline-block;">
          <option value=""></option>
          <?php
          $country_ee='';if (isset($_GET['country_ee'])) $country_ee=trim_gks($_GET['country_ee']);
          while ($row_select = $result_select->fetch_assoc()) {
            echo '<option value="'.$row_select['country_ee'].'" data-ci="'.$row_select['country_initials'].'" '; 
            if ($row_select['country_ee']==$country_ee) echo ' selected ';
            echo '>'.$row_select['country_name'].'</option>';
          }
          ?>
        </select>
      </div>
    </div>
    <div class="form-group row">  
      <label for="dialog_vies_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_vies_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_vies_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_vies_html">
      </div>
    </div>
  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_vies_check','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_vies_check','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_vies_check','delete',0);?>;

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
        if (sname == 'fdate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });  


    
  var dialog_vies_reload=false;
  
  var dialog_vies;
  var dialog_vies_result=false;
  dialog_vies = $( "#dialog_vies" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_vies_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'), 
        //icon: "ui-icon-cancel",
        click: function() {

          if (dialog_vies_reload) {
            rrr=new Date().getTime();
            window.location.href='admin-vies-check.php?reload=' + rrr;
            //$( this ).dialog( "close" );
          } else {
            $( this ).dialog( "close" );
          }
        }
        //showText: false
      },
    ]
        

  });
  
  function btn_vies_get_click() {
    
    
    if (from_php_perm_ret_add==false) return;
    

    $('#dialog_vies_afm').val($('#afm').val());
    $('#dialog_vies_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_vies.dialog('option', 'width', dwidth);
	  dialog_vies.dialog('option', 'height', dheight);
	  $('#dialog_vies').parent().css({position:'fixed'});
    dialog_vies.dialog('open');    
    $('#dialog_vies_ok').button( "option", "disabled", true);
    if (myafm_from_hash!='') {
      $('#dialog_vies_afm').val(myafm_from_hash);
      myafm_from_hash=''; 
      dialog_vies_run_click();
    }
  }
  $('#btn_vies_get').click(btn_vies_get_click);
 
  
  function dialog_vies_run_click() {
    //console.log('dialog_vies_run');
    dialog_vies_reload=true;
    dialog_vies_result=false;
    
    if ($('#dialog_vies_country_ee').val().trim()=='') {
      myalert('error:'+gks_lang('Επιλέξτε πρώτα την χώρα'));
      return;  
    }
        
    dialog_vies_afm=$('#dialog_vies_afm').val().trim();
    if (dialog_vies_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_vies_ok').button( "option", "disabled", true);
    $('#dialog_vies_html').html('');
    

    country_ee=$('#dialog_vies_country_ee').val();
    if (country_ee === undefined || country_ee === null) country_ee='';
    
       
    datasend='afm=' + dialog_vies_afm + '&country_ee=' + country_ee;
    datasend+='&force=1';
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-get-vies.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				//console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  dialog_vies_result=data.out;
					  //console.log(dialog_vies_result);
					  //console.log(dialog_vies_result.user_id);
					  
					  
					  outhtml='';
					  if (dialog_vies_result.user_id>0) {
					    outhtml+='<div class="alert alert-danger" role="alert">';  
					    outhtml+=gks_lang('ΠΡΟΣΟΧΗ: Υπάρχει ήδη επαφή με αυτό το ΑΦΜ με όνομα')+' ' + dialog_vies_result.gks_nickname + '<br>';  
					    outhtml+='<a href="admin-users-item.php?id=' + dialog_vies_result.user_id + '" target="_blank" class="gks_link">'+gks_lang('Προβολή της επαφής')+'</a>';  
					    outhtml+='</div>';  
					  }
					  
					  
					  
  					outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
  					if (dialog_vies_result.valid) {
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:green;color:white;">'+gks_lang('Έγκυρο')+'</div>';
  				  } else { //if (dialog_vies_result.basic_rec.normal_vat_system_flag=='N') {
  				    outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:red;color:white;">'+gks_lang('Μη Έγκυρο')+'</div>';
  				  //} else {
  				  //  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:yellow;color:black;">'+gks_lang('Κανονικό Καθεστώς Φ.Π.Α.: Άγνωστο')+'</div>';
  				  }
  					
  					
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Πεδίο')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Τιμή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΑΦΜ')+':</td><td>' + data.out.vatNumber + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Χώρα')+':</td><td>' + data.out.countryCode + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Όνομα Επιχείρησης')+':</td><td>' + data.out.traderName + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Διεύθυνση')+':</td><td>' + data.out.traderAddress + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Τύπος')+':</td><td>' + data.out.traderCompanyType + '</td></tr>';

  					outhtml+='</tbody></table>';

  					$('#dialog_vies_html').html(outhtml);
  					$('#dialog_vies_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  
  $('#dialog_vies_run').click(dialog_vies_run_click);  

  var myafm_from_hash='';
  myhash=window.location.hash;
  if (myhash.length>=4 && myhash.startsWith('#createfromafm=')) {
    myafm=myhash.substring(15);
    if (myafm.length>=9) {
      parts=myafm.split('|');
      myafm=parts[0];
      myafm_from_hash=myafm;
      $('#btn_vies_get').click();
    }
  }

  function user_create_click() {
    afm=$(this).attr('data-val').trim();
    if (afm=='') return;
    
    url='admin-users-item.php?id=-1#createfromvies=' + afm;
    var win = window.open(url, '_blank');
    win.focus();
  
  }
  $('.user_create').click(user_create_click);

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


