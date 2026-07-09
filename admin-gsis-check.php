<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


// https://test.easyfilesselection.com/my/admin-gsis-check.php?company_id=1#createfromafm=065938168

$my_page_title=gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');
$nav_active_array=array('accounting','accounting_gsis_check');

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_gsis_check','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gsis_check_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','view',0);
$perm_gsis_check_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','edit',0);
$perm_gsis_check_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','add',0);
$perm_gsis_check_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','delete',0);
$perm_gsis_check_autocomplete=gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','autocomplete',0);

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
  'field' => 'gks_gsis_check.mydate', 
  'has_custom_default' => (GKS_ERP_START_VARDIA==0 ? 6 : 5),
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_gsis_check.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
  
);

$filters[] = array(
    'name' => 'fvalid',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποτέλεσμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_gsis_check.valid = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => "OK",            'sql' => "gks_gsis_check.valid='1'"),
        array('value' => 2, 'text' => gks_lang('Σφάλμα'),        'sql' => "gks_gsis_check.valid='0' and (gks_gsis_check.error_text is null or gks_gsis_check.error_text='')"),
        array('value' => 3, 'text' => gks_lang('Προειδοποίηση'), 'sql' => "gks_gsis_check.valid='0' and gks_gsis_check.error_text<>''"),
    ),
);
$filters[] = array(
  'name' => 'fdoy',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('ΔΟΥ'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_doy_descr = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_doy_descr as id,response_doy_descr as descr FROM gks_gsis_check where response_doy_descr<>'' GROUP BY response_doy_descr order by response_doy_descr",    
);
$filters[] = array(
  'name' => 'ffp',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => '<span title="'.gks_lang('Ένδειξη εάν πρόκειται για Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο').'" class="tooltipster">'.gks_lang('ΦΠ').'</span>',
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_i_ni_flag_descr = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_i_ni_flag_descr as id,response_i_ni_flag_descr as descr FROM gks_gsis_check where response_i_ni_flag_descr<>'' GROUP BY response_i_ni_flag_descr order by response_i_ni_flag_descr",    
);

$filters[] = array(
  'name' => 'factive',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => '<span class="tooltipster" title="'.gks_lang('Ένδειξη εάν ο Α.Φ.Μ. είναι ενεργός ή απενεργοποιημένος').'">'.gks_lang('Ενεργός').'</span>',
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_deactivation_flag_descr = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_deactivation_flag_descr as id,response_deactivation_flag_descr as descr FROM gks_gsis_check where response_deactivation_flag_descr<>'' GROUP BY response_deactivation_flag_descr order by response_deactivation_flag_descr",    
);

$filters[] = array(
  'name' => 'ffirm',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => '<span class="tooltipster" title="'.gks_lang('Ένδειξη εάν πρόκειται για επιτηδευματία, μη επιτηδευματία ή πρώην επιτηδευματία').'">'.gks_lang('Επιτηδευματίας').'</span>',
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_firm_flag_descr = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_firm_flag_descr as id,response_firm_flag_descr as descr FROM gks_gsis_check where response_firm_flag_descr<>'' GROUP BY response_firm_flag_descr order by response_firm_flag_descr",    
);

$filters[] = array(
  'name' => 'flegal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => '<span class="tooltipster" title="'.gks_lang('Περιγραφή μορφής Νομικού Προσώπου / Νομικής Οντότητας').'">'.gks_lang('Μορφή').'</span>',
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_legal_status_descr = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_legal_status_descr as id,response_legal_status_descr as descr FROM gks_gsis_check where response_legal_status_descr<>'' GROUP BY response_legal_status_descr order by response_legal_status_descr",    
);

$filters[] = array(
  'name' => 'ffpa',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => '<span class="tooltipster" title="'.gks_lang('Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α.').'">'.gks_lang('Φ.Π.Α.').'</span>',
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_gsis_check.response_normal_vat_system_flag = '%V%'",
  'vals' => array(
  ),
  'sql' => "
SELECT response_normal_vat_system_flag as id,response_normal_vat_system_flag as descr FROM gks_gsis_check where response_normal_vat_system_flag<>'' GROUP BY response_normal_vat_system_flag order by response_normal_vat_system_flag",    
);

$sortable = array(
  array('name' => 'soid', 'field' => 'gks_gsis_check.id_gsis_check'),
  array('name' => 'sodate', 'field' => 'gks_gsis_check.mydate'),
  array('name' => 'soafm', 'field' => 'gks_gsis_check.afm'),
  
  
  
  array('name' => 'soconn', 'field' => 'gks_gsis_check.connection_ok'),
  array('name' => 'soresult', 'field' => 'gks_gsis_check.valid, gks_gsis_check.error_text'),
  array('name' => 'soerror', 'field' => 'gks_gsis_check.error_text'),
  array('name' => 'sodoy', 'field' => 'gks_gsis_check.response_doy_descr'),
  array('name' => 'sofp', 'field' => 'gks_gsis_check.response_i_ni_flag_descr'),
  array('name' => 'soactive', 'field' => 'gks_gsis_check.response_deactivation_flag_descr'),
  array('name' => 'sofirm', 'field' => 'gks_gsis_check.response_firm_flag_descr'),
  array('name' => 'socomany', 'field' => 'gks_gsis_check.response_onomasia'),
  array('name' => 'sotitle', 'field' => 'gks_gsis_check.response_commer_title'),
  array('name' => 'solegal', 'field' => 'gks_gsis_check.response_legal_status_descr'),
  array('name' => 'soodos', 'field' => 'gks_gsis_check.response_postal_address,gks_gsis_check.response_postal_address_no'),
  array('name' => 'sotk', 'field' => 'gks_gsis_check.response_postal_zip_code'),
  array('name' => 'soarea', 'field' => 'gks_gsis_check.response_postal_area_description'),
  array('name' => 'sostart', 'field' => 'gks_gsis_check.response_regist_date'),
  array('name' => 'soend', 'field' => 'gks_gsis_check.response_stop_date'),
  array('name' => 'sofpa', 'field' => 'gks_gsis_check.response_normal_vat_system_flag'),
  array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
);


$search_fields = array(
'gks_gsis_check.afm',
'gks_gsis_check.error_text',
'gks_gsis_check.response_doy_descr',
'gks_gsis_check.response_deactivation_flag_descr',
'gks_gsis_check.response_firm_flag_descr',
'gks_gsis_check.response_onomasia',
'gks_gsis_check.response_commer_title',
'gks_gsis_check.response_postal_address',
'gks_gsis_check.response_postal_zip_code',
'gks_gsis_check.response_postal_area_description',
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

//SELECT SQL_CALC_FOUND_ROWS gks_gsis_check.*, 
//other.lot_descr AS other_descr
//FROM gks_gsis_check 
//LEFT JOIN gks_gsis_check AS other ON gks_gsis_check.monada_parent_id = other.id_gsis_check

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_gsis_check.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM gks_gsis_check 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_gsis_check.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_gsis_check.id_gsis_check DESC";
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
  $sql="SELECT id_company, company_title, company_eponimia, company_afm 
  FROM gks_company 
  WHERE company_afm In (".implode(',',$afms).")
  order by id_company";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

  while ($row = $result->fetch_assoc()) {
    if (isset($afms_company[$row['company_afm']])==false) {
      $title=trim_gks($row['company_title']);
      if ($title=='') $title=trim_gks($row['company_eponimia']);
      if ($title=='') $title='id: '.$row['id_company'];
      $afms_company[$row['company_afm']]=array(
        'id' => $row['id_company'],
        'title' => $title,
      );
    }
  }
  
  //print '<pre>';print_r($afms_company);die();
  
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, gks_users.eponimia, gks_users.title, gks_users.afm
  FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm In (".implode(',',$afms).")
  order by ".GKS_WP_TABLE_PREFIX."users.ID;";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

  while ($row = $result->fetch_assoc()) {
    if (isset($afms_user[$row['afm']])==false) {
      $title=trim_gks($row['title']);
      if ($title=='') $title=trim_gks($row['eponimia']);
      if ($title=='') $title='id: '.$row['ID'];
      
      $afms_user[$row['afm']]=array(
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
    
    
    <?php if ($perm_gsis_check_add) {?>
    <div class="col-sm-12" style="text-align:center">

      <label for="company_id_sub_id" class="form-control1 form-control-sm" style="width: unset;display: inline-block;"><?php echo gks_lang('Εταιρεία');?>:</label>            
      <select id="company_id_sub_id" class="form-control  form-control-sm" style="width: unset;display: inline-block;">
        <option value="0|0">-- (<?php echo gks_lang('από όποια εταιρεία υπάρχουν κωδικοί');?>)</option>
        <?php
        $company_id=0;if (isset($_GET['company_id'])) $company_id=intval($_GET['company_id']);
        $count_company=0;
        foreach ($user_companys as $row_select) {
          if ($row_select['id_company_sub']==0) $count_company++;
        }
        foreach ($user_companys as $row_select) {
          if ($row_select['id_company_sub']==0) {
            echo '<option value="'.$row_select['id'].'" ' .
            ' data-afm="'.$row_select['company_afm'].'" ';
            if ($row_select['id_company']==$company_id or $count_company==1) echo ' selected ';
            echo '>'.$row_select['company_title'].'</option>';
          }
        }?>
        
      </select>
      <button class="btn btn-primary" id="btn_gsis_get" style="margin-left: 10px;"><?php echo gks_lang('Νέα αναζήτηση');?></button>

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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="12%" nowrap><?php echo gks_lang('Επαφή');?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soconn', '<span title="'.gks_lang('Σύνδεση με τον server').'" class="tooltipster">'.gks_lang('Συνδ').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soresult', '<span title="'.gks_lang('Αποτέλεσμα').'" class="tooltipster">'.gks_lang('Αποτ').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="8%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soerror', gks_lang('Σφάλμα')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodoy', gks_lang('ΔΟΥ')); ?></th>        

        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofp', '<span title="'.gks_lang('Ένδειξη εάν πρόκειται για Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο').'" class="tooltipster">'.gks_lang('ΦΠ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soactive', '<span class="tooltipster" title="'.gks_lang('Ένδειξη εάν ο Α.Φ.Μ. είναι ενεργός ή απενεργοποιημένος').'">'.gks_lang('Ενεργός').'</span>'); ?></th> 
               
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofirm', '<span class="tooltipster" title="'.gks_lang('Ένδειξη εάν πρόκειται για επιτηδευματία, μη επιτηδευματία ή πρώην επιτηδευματία').'">'.gks_lang('Επιτηδευματίας').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socomany', '<span class="tooltipster" title="'.gks_lang('Επωνυμία Επιχείρησης').'">'.gks_lang('Επωνυμία').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', '<span class="tooltipster" title="'.gks_lang('Τίτλος Επιχείρησης').'">'.gks_lang('Τίτλος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solegal', '<span class="tooltipster" title="'.gks_lang('Περιγραφή μορφής Νομικού Προσώπου / Νομικής Οντότητας').'">'.gks_lang('Μορφή').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', '<span class="tooltipster" title="'.gks_lang('Οδός και αριθμός').'">'.gks_lang('Οδός').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotk', 'ΤΚ'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soarea', gks_lang('Περιοχή')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostart', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Έναρξης Εργασιών της Επιχείρησης').'">'.gks_lang('Έναρξη').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soend', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Διακοπής Εργασιών της Επιχείρησης').'">'.gks_lang('Διακοπή').'</span>'); ?></th> 

        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofpa', '<span class="tooltipster" title="'.gks_lang('Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α.').'">'.gks_lang('Φ.Π.Α.').'</span>'); ?></th> 
        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"><span class="tooltipster" title="<?php echo gks_lang('Κωδικός Αριθμός Δραστηριότητας');?>"><?php echo gks_lang('ΚΑΔ');?></span></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="8%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Χρήστης')); ?></th> 


   
        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    //while ($row = $result->fetch_assoc()) {
    foreach ($data as $row) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_gsis_check'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <?php echo $row['id_gsis_check'];?>
      <?php if ($perm_gsis_check_delete){?>
      <br>
      <i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_gsis_check'];?>" data-model="gks_gsis_check"></i>
      <?php }?>
    </td>
    

    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php echo $row['afm'];?></td>
<?php
      $afm=$row['afm'];
      $afm_html='';
      if (isset($afms_company[$afm])) 
        $afm_html=' <a href="admin-company-item.php?id='.$afms_company[$afm]['id'].'">'.$afms_company[$afm]['title'].'</a>';
      else if (isset($afms_user[$afm])) 
        $afm_html=' <a href="admin-users-item.php?id='.$afms_user[$afm]['id'].'">'.$afms_user[$afm]['title'].'</a>';
      else if ($afm!='')
        if ($perm_wp_users_add and $row['valid']==1) 
          $afm_html=' <i class="fas fa-save user_create" data-val="'.$afm.'" title="'.gks_lang('Δημιουργία επαφής').'"></i>';
?>
    <td        class="mytdcml"><?php echo $afm_html;?></td>
    
    <td nowrap class="mytdcm p-0"><img src="img/<?php 
      if ($row['connection_ok']==1) echo '1.png';
      else if ($row['connection_ok']==0) echo '0.png';
      else echo 'null.png'
      ?>" class="gks_icon"></td>    
    
    
    
    <td nowrap class="mytdcm p-0"><img src="img/<?php 
      if (trim_gks($row['error_text'])!='') echo 'warning.gif';
      else if ($row['valid']==1) echo '1.png';
      else if ($row['valid']==0) echo '0.png';
      else echo 'null.png'
      ?>" class="gks_icon"></td>    
    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      echo $row['error_text'];
    ?></div></div></td> 
    <td        class="mytdcml"><?php echo $row['response_doy_descr'];?></td>
    <td nowrap class="mytdcm" ><?php echo $row['response_i_ni_flag_descr'];?></td>
    <td        class="mytdcml"><?php echo $row['response_deactivation_flag_descr'];?></td>
    <td        class="mytdcml"><?php echo $row['response_firm_flag_descr'];?></td>
    <td        class="mytdcml"><?php echo $row['response_onomasia'];?></td>
    <td        class="mytdcml"><?php echo $row['response_commer_title'];?></td>
    <td        class="mytdcml"><?php echo $row['response_legal_status_descr'];?></td>
    <td        class="mytdcml"><?php echo $row['response_postal_address'].' '.$row['response_postal_address_no'];?></td>
    <td nowrap class="mytdcml"><?php echo $row['response_postal_zip_code'];?></td>
    <td        class="mytdcml"><?php echo $row['response_postal_area_description'];?></td>
    <td nowrap class="mytdcm" ><?php echo $row['response_regist_date'];?></td>
    <td nowrap class="mytdcm" ><?php echo $row['response_stop_date'];?></td>
    <td nowrap class="mytdcm" ><?php echo $row['response_normal_vat_system_flag'];?></td>
    
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $kad='';
      $temp=trim_gks($row['response_firm_act_tab']);
      
      if ($temp!='') {
        $temp=unserialize($temp);
        if (is_array($temp)) {
          //var_dump($temp);
          foreach ($temp as $value) {
//            $kad.='<tr>'.
//            '<td>'.$value['code'].'</td>'.
//            '<td>'.$value['cdescr'].'</td>'.
//            '<td>'.$value['kdescr'].'</td>'.
//            '</tr>';  
            $kad.='<div class="gks_kad" style="">'.$value['kdescr'].' '.$value['code'].'<br>'.$value['cdescr'].'</div>';
 
             
          }  
        }
        if ($kad!='') {
          //$kad='<table>'.$kad.'</table>';
        }
      }
      echo $kad;
    ?></div></div></td> 
    
    
    <td        class="mytdcml" ><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    
     
  </tr>
<?php    
    }
?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

 
<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_gsis_check','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_gsis_check','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_gsis_check','delete',0);?>;

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


    
  var dialog_gsis_reload=false;
  
  var dialog_gsis;
  var dialog_gsis_result=false;
  dialog_gsis = $( "#dialog_gsis" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_gsis_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'), 
        //icon: "ui-icon-cancel",
        click: function() {

          if (dialog_gsis_reload) {
            rrr=new Date().getTime();
            window.location.href='admin-gsis-check.php?reload=' + rrr;
            //$( this ).dialog( "close" );
          } else {
            $( this ).dialog( "close" );
          }
        }
        //showText: false
      },
    ]
        

  });
  
  function btn_gsis_get_click() {
    if (from_php_perm_ret_add==false) return;
    $('#dialog_gsis_afm').val($('#afm').val());
    $('#dialog_gsis_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_gsis.dialog('option', 'width', dwidth);
	  dialog_gsis.dialog('option', 'height', dheight);
	  $('#dialog_gsis').parent().css({position:'fixed'});     
    dialog_gsis.dialog('open');    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    if (myafm_from_hash!='') {
      $('#dialog_gsis_afm').val(myafm_from_hash);
      myafm_from_hash=''; 
      dialog_gsis_run_click();
    }
  }
  $('#btn_gsis_get').click(btn_gsis_get_click);
 
  
  function dialog_gsis_run_click() {
    //console.log('dialog_gsis_run');
    dialog_gsis_reload=true;
    dialog_gsis_result=false;
    
    dialog_gsis_afm=$('#dialog_gsis_afm').val().trim();
    if (dialog_gsis_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    $('#dialog_gsis_html').html('');
    
    company_id=0;
    company_sub_id=0;
    v=$('#company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
       
    datasend='afm=' + dialog_gsis_afm + '&company_id=' + company_id + '&force=1';
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-get-gisis.php',
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
					  dialog_gsis_result=data.out;
					  //console.log(dialog_gsis_result);
					  //console.log(dialog_gsis_result.user_id);
					  
					  
					  outhtml='';
					  if (dialog_gsis_result.user_id>0) {
					    outhtml+='<div class="alert alert-danger" role="alert">';  
					    outhtml+=gks_lang('ΠΡΟΣΟΧΗ: Υπάρχει ήδη επαφή με αυτό το ΑΦΜ με όνομα')+' ' + dialog_gsis_result.gks_nickname + '<br>';  
					    outhtml+='<a href="admin-users-item.php?id=' + dialog_gsis_result.user_id + '" target="_blank" class="gks_link">'+gks_lang('Προβολή της επαφής')+'</a>';  
					    outhtml+='</div>';  
					  }
					  
					  
					  
  					outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
  					if (dialog_gsis_result.valid==1) { //true
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:green;color:white;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else if (dialog_gsis_result.valid==2) { //wait
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:yellow;color:yellow;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else {
  				    outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:red;color:white;">' + dialog_gsis_result.error_text + '</div>';
  				  }
  					
  					
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Πεδίο')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Τιμή','part2')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΑΦΜ')+':</td><td>' + data.out.basic_rec.afm + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ ID')+':</td><td>' + data.out.basic_rec.doy + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ')+':</td><td>' + data.out.basic_rec.doy_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο')+':</td><td>' + data.out.basic_rec.i_ni_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ο Α.Φ.Μ. είναι ενεργός ή απενεργοποιημένος')+':</td><td>' + data.out.basic_rec.deactivation_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επιτηδευματίας, μη επιτηδευματίας ή πρώην επιτηδευματίας')+':</td><td>' + data.out.basic_rec.firm_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επωνυμία Επιχείρησης')+':</td><td>' + data.out.basic_rec.onomasia + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Τίτλος Επιχείρησης')+':</td><td>' + data.out.basic_rec.commer_title + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιγραφή μορφής Νομικού Προσώπου / Νομικής Οντότητας')+':</td><td>' + data.out.basic_rec.legal_status_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Διεύθυνση Έδρας Επιχείρησης')+':</td><td>' + data.out.basic_rec.postal_address + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Αριθμός')+':</td><td>' + data.out.basic_rec.postal_address_no + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΤΚ')+':</td><td nowrap>' + data.out.basic_rec.postal_zip_code + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιοχή')+':</td><td>' + data.out.basic_rec.postal_area_description + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Έναρξης')+':</td><td>' + data.out.basic_rec.regist_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Διακοπής')+':</td><td>' + data.out.basic_rec.stop_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α')+':</td><td>' + data.out.basic_rec.normal_vat_system_flag + '</td></tr>';
  					outhtml+='</tbody></table>';
  					
            outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Δραστηριότητες Επιχείρησης')+'</p>';
              
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkssubtable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:30%">'+gks_lang('Τύπος')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:20%">'+gks_lang('Κωδικός')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Περιγραφή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					for (i=0;i < data.out.firm_act_tab.length; i++) {
  					  outhtml+='<tr><td scope="row" style="text-align: center !important;">' + data.out.firm_act_tab[i].kdescr + '</td><td style="text-align: center !important;">' + data.out.firm_act_tab[i].code + '</td><td>' + data.out.firm_act_tab[i].cdescr+ '</td></tr>';
  					}
  					outhtml+='</tbody></table>';
  					$('#dialog_gsis_html').html(outhtml);
  					$('#dialog_gsis_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }
  
  $('#dialog_gsis_run').click(dialog_gsis_run_click);  

  var myafm_from_hash='';

  myhash=window.location.hash;
  if (myhash.length>=4 && myhash.startsWith('#createfromafm=')) {
    myafm=myhash.substring(15);
    if (myafm.length>=9) {
      parts=myafm.split('|');
      myafm=parts[0];
      myafm_from_hash=myafm;
      $('#btn_gsis_get').click();
    }
  }


  function user_create_click() {
    afm=$(this).attr('data-val').trim();
    if (afm=='') return;


    
    url='admin-users-item.php?id=-1#createfromafm=' + afm;
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


