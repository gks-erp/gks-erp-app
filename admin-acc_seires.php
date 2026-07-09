<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Σειρές');
$nav_active_array=array('manage','accounting_seires');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_seires','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_company_subs_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','edit',0);







$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_seires',['from'=>'list']);

$filters = array();

$filters[] = array(
    'name' => 'fcompany',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Εταιρεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_journal.company_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_company.id_company AS id, gks_company.company_title AS descr
    FROM (gks_acc_seires LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company
    WHERE (((gks_company.id_company) Is Not Null))
    GROUP BY gks_company.id_company, gks_company.company_title
    ORDER BY gks_company.company_sortorder, gks_company.company_title",
);
$filters[] = array(
    'name' => 'fcompanysub',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Υποκατάστημα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_journal.company_sub_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -2, 'text' => gks_lang('Κεντρικό'),       'sql' => "gks_acc_journal.company_sub_id=0"),
    ),
    'sql' => "SELECT gks_company_subs.id_company_sub AS id, gks_company_subs.company_sub_title AS descr
    FROM (gks_acc_seires LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub
    WHERE (((gks_company_subs.id_company_sub) Is Not Null))
    GROUP BY gks_company_subs.id_company_sub, gks_company_subs.company_sub_title
    ORDER BY gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title",
);



$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Παραστατικού'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_acc_journal.acc_eidos_parastatikou_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS id, gks_acc_eidi_parastatikon.eidos_parastatikou_descr AS descr
    FROM (gks_acc_seires LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) Is Not Null))
    GROUP BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr"
);



$filters[] = array(
    'name' => 'fsend_mydata',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Αποστολή myData-Πάροχο').'">'.gks_lang('myData-Πάροχο').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('myData'),   'sql' => "gks_acc_seires.send_mydata <> 0"),
        array('value' => 3, 'text' => gks_lang('Πάροχο'),   'sql' => "gks_acc_seires.send_paroxos <> 0"),
        array('value' => 2, 'text' => gks_lang('Πουθενά'),  'sql' => "gks_acc_seires.send_mydata=0 and gks_acc_seires.send_paroxos=0"),
    ),
);

$filters[] = array(
    'name' => 'faade_lock_send_numbers',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Αυστηρή σειρά αποστολής').'">'.gks_lang('ΑΣΑ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.aade_lock_send_numbers <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.aade_lock_send_numbers = 0"),
    ),
);

$filters[] = array(
    'name' => 'fsns',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Απαιτείται υπογραφή από πάροχο').'">'.gks_lang('Υπογραφή').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "gks_acc_seires.seira_need_signature <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "gks_acc_seires.seira_need_signature =  0"),
    ),
);
$filters[] = array(
    'name' => 'fsidn',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ').' '.gks_lang('π.χ. δελτίο αποστολής').'">'.gks_lang('Δελτίο').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.seira_isdeliverynote <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.seira_isdeliverynote = 0"),
    ),
);
$filters[] = array(
    'name' => 'frdn',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αντίστροφη Διακίνηση'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.seira_is_reverse_delivery_note <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.seira_is_reverse_delivery_note = 0"),
    ),
);
$filters[] = array(
    'name' => 'fsp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αυτοτιμολόγηση'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.seira_is_self_pricing <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.seira_is_self_pricing = 0"),
    ),
);
$filters[] = array(
    'name' => 'fvps',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Αναστολή Καταβολής ΦΠΑ').'">'.gks_lang('ΑΚΦ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.seira_is_vat_payment_suspension <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.seira_is_vat_payment_suspension = 0"),
    ),
);

$filters[] = array(
    'name' => 'ferp_app',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Αποστολή gks ERP App Desktop').'">'.gks_lang('gks ERP App Desktop').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),   'sql' => "gks_acc_seires.erp_app_id <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),   'sql' => "gks_acc_seires.erp_app_id = 0"),
    ),
);



$filters[] = array(
    'name' => 'fis_xeirografi',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Χειρόγραφη Σειρά').'">'.gks_lang('Χειρόγραφη').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "gks_acc_seires.is_xeirografi <> 0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "gks_acc_seires.is_xeirografi =  0"),
    ),
);
$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα').',        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),     'sql' => "gks_acc_seires.is_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργή'),  'sql' => "gks_acc_seires.is_disable <> 0"),
    ),
);



$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_acc_seires.id_acc_seira'),
  						array('name' => 'socode', 'field' => 'gks_acc_seires.seira_code'),
  						array('name' => 'soname', 'field' => 'gks_acc_seires.seira_descr'),
  						array('name' => 'sojournal', 'field' => 'gks_acc_journal.acc_journal_descr'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'socompany_sub', 'field' => 'gks_company_subs.company_sub_title'),
  						array('name' => 'soname', 'field' => 'gks_acc_seires.seira_descr'),
  						array('name' => 'sotype', 'field' => 'gks_acc_eidi_parastatikon.eidos_parastatikou_descr'),
  						array('name' => 'soprefix', 'field' => 'gks_acc_seires.prefix'),
  						array('name' => 'sosuffix', 'field' => 'gks_acc_seires.suffix'),
  						array('name' => 'sonumber_size', 'field' => 'gks_acc_seires.number_size'),
  						array('name' => 'sonumber_step', 'field' => 'gks_acc_seires.number_step'),
  						array('name' => 'sonext_number', 'field' => 'gks_acc_seires.next_number'),
  						array('name' => 'ssend_mydata', 'field' => 'gks_acc_seires.send_mydata'),
  						array('name' => 'ssend_paroxo', 'field' => 'gks_acc_seires.send_paroxos'),
  						array('name' => 'saade_lock_send_numbers', 'field' => 'gks_acc_seires.aade_lock_send_numbers'),
  						
  						
  						array('name' => 'ssns', 'field' => 'gks_acc_seires.seira_need_signature'),
  						array('name' => 'ssidn', 'field' => 'gks_acc_seires.seira_isdeliverynote'),
  						array('name' => 'srdn', 'field' => 'gks_acc_seires.seira_is_reverse_delivery_note'),
  						array('name' => 'ssp', 'field' => 'gks_acc_seires.seira_is_self_pricing'),
  						array('name' => 'svps', 'field' => 'gks_acc_seires.seira_is_vat_payment_suspension'),
  						
  						
  						array('name' => 'ssend_erpapp', 'field' => 'gks_acc_seires.erp_app_id'),
  						array('name' => 'sois_xeirografi', 'field' => 'gks_acc_seires.is_xeirografi'),
  						array('name' => 'soinv', 'field' => 'cc_inv'),
  						array('name' => 'sosotorder', 'field' => 'gks_acc_seires.sortorder'),
  						array('name' => 'sodisable', 'field' => 'gks_acc_seires.is_disable'),
						
						
						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_acc_seires.seira_code',
'gks_acc_seires.seira_descr',
'gks_acc_journal.acc_journal_descr',
'gks_company.company_title',
'gks_company_subs.company_sub_title',
'gks_acc_seires.seira_descr',
'gks_acc_eidi_parastatikon.eidos_parastatikou_descr',
'gks_acc_seires.prefix',
'gks_acc_seires.suffix',
'gks_acc_seires.seira_comments',
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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_acc_seires.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_acc_journal.acc_journal_descr, 
gks_acc_journal.company_id, gks_company.company_title, 
gks_acc_journal.company_sub_id, gks_company_subs.company_sub_title, 
gks_acc_journal.acc_eidos_parastatikou_id, gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
tbl_inv.cc_inv
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((gks_acc_seires 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_seires.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_seires.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN (
  SELECT inv_acc_seira_id, Count(id_acc_inv) AS cc_inv FROM gks_acc_inv GROUP BY inv_acc_seira_id
) as tbl_inv on gks_acc_seires.id_acc_seira =tbl_inv.inv_acc_seira_id

where 1=1 " .$where . $search_where;

if (empty($sorted['sql'])) {
	$query .= " ORDER BY gks_acc_seires.sortorder,gks_acc_seires.seira_descr";
} else {
	$query .= " ORDER BY " . $sorted['sql'];
}
$query .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $query;
//die();
	
$result = $db_link->query($query);        
if (!$result) debug_mail(false,'error sql',$query);
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
      <a class="btn btn-primary gks_add_new_record" href="admin-acc_seires-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας σειράς');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_acc_seires">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sojournal', gks_lang('Ημερολόγιο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany_sub', gks_lang('Υποκατάστημα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος Παραστατικού')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprefix', '<span class="tooltipster" title="'.gks_lang('Πρόθεμα').'">'.gks_lang('Προ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosuffix', '<span class="tooltipster" title="'.gks_lang('Επίθεμα').'">'.gks_lang('Επι').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber_size', '<span class="tooltipster" title="'.gks_lang('Πλήθος Ψηφίων').'">'.gks_lang('ΠΨ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber_step', '<span class="tooltipster" title="'.gks_lang('Βήμα αριθμών').'">'.gks_lang('Βήμα').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonext_number', '<span class="tooltipster" title="'.gks_lang('Επόμενος Αριθμός').'">'.gks_lang('ΕΑ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssend_mydata', '<span class="tooltipster" title="'.gks_lang('Αποστολή myData').'">'.gks_lang('myData').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssend_paroxo', '<span class="tooltipster" title="'.gks_lang('Αποστολή Πάροχο').'">'.gks_lang('Πάροχο').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'saade_lock_send_numbers', '<span class="tooltipster" title="'.gks_lang('Αυστηρή σειρά αποστολής').'">'.gks_lang('ΑΣΑ').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssns', '<span class="tooltipster" title="'.gks_lang('Απαιτείται υπογραφή από πάροχο').'">'.gks_lang('Υπογραφή').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssidn', '<span class="tooltipster" title="'.gks_lang('Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ').' '.gks_lang('π.χ. δελτίο αποστολής').'">'.gks_lang('Δελτίο').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'srdn', '<span class="tooltipster" title="'.gks_lang('Αντίστροφη Διακίνηση').'">'.gks_lang('ΑΔ').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssp', '<span class="tooltipster" title="'.gks_lang('Αυτοτιμολόγηση').'">'.gks_lang('ΑΥΤ').'</span>'); ?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'svps', '<span class="tooltipster" title="'.gks_lang('Αναστολή Καταβολής ΦΠΑ').'">'.gks_lang('ΑΚΦ').'</span>'); ?></th>  


        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'ssend_erpapp', '<span class="tooltipster" title="'.gks_lang('Αποστολή gks ERP App Desktop').'">'.gks_lang('App').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sois_xeirografi', '<span class="tooltipster" title="'.gks_lang('Χειρόγραφη Σειρά').'">'.gks_lang('Χειρ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinv', '<span class="tooltipster" title="'.gks_lang('Πλήθος Παραστατικών').'">'.gks_lang('Π.Παρ.').'</span>'); ?></th>        
        <?php if ($perm_company_subs_edit) { ?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosotorder', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>   
        <?php } ?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>   
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_acc_seira'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-acc_seires-item.php?id=<?php echo $row['id_acc_seira'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_acc_seira'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_acc_seira'];?>" data-model="gks_acc_seires"></i></td>
        </tr>      
      </table>
    </td>
    
    <td nowrap class="mytdcml"><?php echo $row['seira_code'];?></td>
    <td        class="mytdcml"><?php echo $row['seira_descr'];?></td>
    
    
    <td        class="mytdcml"><a href="admin-acc_journal-item.php?id=<?php echo $row['acc_journal_id'];?>"><?php echo $row['acc_journal_descr'];?></a></td>
    
    <td        class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td        class="mytdcml"><a href="admin-company-sub-item.php?id=<?php echo $row['company_sub_id'];?>"><?php echo $row['company_sub_title'];?></a></td>
    <td        class="mytdcml"><?php echo $row['eidos_parastatikou_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['prefix'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['suffix'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['number_size'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['number_step'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['next_number'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['send_mydata']!=0) {?><i class="fas fa-database tooltipster" title="<?php echo gks_lang('Αποστολή myData');?>" style="color:#0094da;"></i> <?php } ?></td>
    <td nowrap class="mytdcm"><?php if ($row['send_paroxos']!=0) {?><i class="fas fa-database tooltipster" title="<?php echo gks_lang('Αποστολή Πάροχο');?>" style="color:#112d63;"></i> <?php } ?></td>
    <td class="mytdcm"><?php echo myimg01($row['aade_lock_send_numbers']);?></td>
    <td nowrap class="mytdcm"><?php if ($row['seira_need_signature']!=0) {?><i class="fas fa-signature tooltipster" title="<?php echo gks_lang('Απαιτείται υπογραφή από πάροχο');?>" style="color:#112d63;"></i> <?php } ?></td>
    
    <td nowrap class="mytdcm"><?php if ($row['seira_isdeliverynote']!=0) {?><i class="fas fa-truck tooltipster" title="<?php echo gks_lang('Ένδειξη Παραστατικού Διακίνησης');?>" style="color:#112d63;"></i> <?php } ?></td>
    <td nowrap class="mytdcm"><?php if ($row['seira_is_reverse_delivery_note']!=0) {?><i class="fas fa-truck fa-flip-horizontal tooltipster" title="<?php echo gks_lang('Αντίστροφη Διακίνηση');?>" style="color:#0094da;"></i> <?php } ?></td>
    <td nowrap class="mytdcm"><?php if ($row['seira_is_self_pricing']!=0) {?><i class="fas fa-circle-notch tooltipster" title="<?php echo gks_lang('Αυτοτιμολόγηση');?>" style="color:#112d63;"></i> <?php } ?></td>
    <td nowrap class="mytdcm"><?php if ($row['seira_is_vat_payment_suspension']!=0) {?><i class="fas fa-moon tooltipster" title="<?php echo gks_lang('Αναστολή Καταβολής ΦΠΑ');?>" style="color:#112d63;"></i> <?php } ?></td>
    

    <td nowrap class="mytdcm"><?php if ($row['erp_app_id']!=0) {?><i class="fas fa-desktop tooltipster" title="<?php echo gks_lang('Αποστολή gks ERP App Desktop');?>" style="color:black;"></i> <?php } ?></td>
    <td nowrap class="mytdcm"><?php if ($row['is_xeirografi']!=0) {?><i class="fas fa-edit tooltipster" title="<?php echo gks_lang('Χειρόγραφη');?>"></i> <?php } ?></td>


    <td nowrap class="mytdcm"><?php echo $row['cc_inv'];?></td>
    <?php if ($perm_company_subs_edit) { ?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sortorder'];?></span>
    </td>
    <?php } ?>
    
    <td class="mytdcm"><?php echo myimg010r($row['is_disable']);?></td>

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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','delete',0);?>;

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

  $('#table_gks_acc_seires > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_acc_seires',mylist,'#table_gks_acc_seires > tbody');
    }
  }); 
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

