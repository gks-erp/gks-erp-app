<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Ημερολόγια');
$nav_active_array=array('manage','accounting_journal');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_journal','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



//for ($i = 12647; $i <= 12647; $i++) $res=gks_eftpos_set_payment_via_iris($i); 
//echo '<pre>';print_r($res);die();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_journal',['from'=>'list']);
//print '<pre>';print_r($gks_custom_prepare);die();

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
FROM gks_acc_journal LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company
WHERE (((gks_company.id_company) Is Not Null))
GROUP BY gks_company.id_company, gks_company.company_title
ORDER BY gks_company.company_sortorder, gks_company.company_title;",
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
FROM gks_acc_journal LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub
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
    'sql' => "SELECT gks_acc_journal.acc_eidos_parastatikou_id as id, gks_acc_eidi_parastatikon.eidos_parastatikou_descr as descr
FROM gks_acc_journal LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) Is Not Null))
GROUP BY gks_acc_journal.acc_eidos_parastatikou_id, gks_acc_eidi_parastatikon.eidos_parastatikou_descr
ORDER BY gks_acc_eidi_parastatikon.sortorder"
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
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "is_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Μη ενεργό'),  'sql' => "is_disable <> 0"),
    ),
);


$filters[] = array(
    'name' => 'fdap',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Δελτίο Αποστολής/Παραλαβής').'">'.gks_lang('ΔΑΠ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "acc_eidos_parastatikou_whi_id<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "acc_eidos_parastatikou_whi_id= 0"),
    ),
);
$filters[] = array(
    'name' => 'flsa',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ').'">'.gks_lang('ΛΣΑ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "acc_eidos_parastatikou_other_entity<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "acc_eidos_parastatikou_other_entity= 0"),
    ),
);

$filters[] = array(
    'name' => 'fsp',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Συσχετιζόμενα Παραστατικά').'">'.gks_lang('ΣΠ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "journal_has_correlated_invoices<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "journal_has_correlated_invoices= 0"),
    ),
);
$filters[] = array(
    'name' => 'fmcm',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ').'">'.gks_lang('ΠΣΜ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "journal_has_multiple_connected_marks<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "journal_has_multiple_connected_marks= 0"),
    ),
);
$filters[] = array(
    'name' => 'fpd',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => '<span class="tooltipster" title="'.gks_lang('Πληροφορίες Συσκευασίας Διακίνησης').'">'.gks_lang('ΠΣΔ').'</span>',
    'has_custom_default' => -1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),  'sql' => "journal_has_packings_declarations<>0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),  'sql' => "journal_has_packings_declarations= 0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_acc_journal.id_acc_journal'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title'),
  						array('name' => 'socompany_sub', 'field' => 'gks_company_subs.company_sub_title'),
  						array('name' => 'socode', 'field' => 'gks_acc_journal.acc_journal_code'),
  						array('name' => 'soname', 'field' => 'gks_acc_journal.acc_journal_descr'),
  						array('name' => 'sotype', 'field' => 'gks_acc_eidi_parastatikon.eidos_parastatikou_descr'),
  						array('name' => 'soseira', 'field' => 'cc_seira'),
  						array('name' => 'soinv', 'field' => 'cc_inv'),
  						array('name' => 'sosotorder', 'field' => 'gks_acc_journal.sortorder'),
  						array('name' => 'sodisable', 'field' => 'gks_acc_journal.is_disable'),
  						array('name' => 'sodap', 'field' => 'gks_acc_journal.acc_eidos_parastatikou_whi_id'),
  						array('name' => 'solsa', 'field' => 'gks_acc_journal.acc_eidos_parastatikou_other_entity'),
  						array('name' => 'sosp',  'field' => 'gks_acc_journal.journal_has_correlated_invoices'),
  						array('name' => 'somcm', 'field' => 'gks_acc_journal.journal_has_multiple_connected_marks'),
  						array('name' => 'sopd',  'field' => 'gks_acc_journal.journal_has_packings_declarations'),
  						
  						
  						

            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_company.company_title',
'gks_company_subs.company_sub_title',
'gks_acc_journal.acc_journal_code',
'gks_acc_journal.acc_journal_descr',
'gks_acc_eidi_parastatikon.eidos_parastatikou_descr',
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


$query = "SELECT SQL_CALC_FOUND_ROWS gks_acc_journal.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_company.company_title, gks_company_subs.company_sub_title, gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
tbl_seires.cc_seira,tbl_inv.cc_inv
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((gks_acc_journal 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_journal.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_journal.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN (
  SELECT acc_journal_id, Count(id_acc_seira) AS cc_seira FROM gks_acc_seires GROUP BY acc_journal_id
) as tbl_seires on gks_acc_journal.id_acc_journal = tbl_seires.acc_journal_id)
LEFT JOIN (
  SELECT inv_acc_journal_id, Count(id_acc_inv) AS cc_inv FROM gks_acc_inv GROUP BY inv_acc_journal_id
) as tbl_inv on gks_acc_journal.id_acc_journal =tbl_inv.inv_acc_journal_id

where 1=1 " .$where . $search_where;

if (empty($sorted['sql'])) {
	$query .= " ORDER BY gks_acc_journal.sortorder,gks_acc_journal.acc_journal_descr";
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

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-acc_journal-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου ημερολογίου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_journals">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany_sub', gks_lang('Υποκατάστημα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος Παραστατικού')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseira', '<span class="tooltipster" title="'.gks_lang('Πλήθος Σειρών').'">'.gks_lang('Σειρές').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soinv', '<span class="tooltipster" title="'.gks_lang('Πλήθος Παραστατικών').'">'.gks_lang('Π.Παρ.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosotorder', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodap', '<span class="tooltipster" title="'.gks_lang('Δελτίο Αποστολής/Παραλαβής').'">'.gks_lang('ΔΑΠ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solsa', '<span class="tooltipster" title="'.gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ').'">'.gks_lang('ΛΣΑ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosp',  '<span class="tooltipster" title="'.gks_lang('Συσχετιζόμενα Παραστατικά').'">'.gks_lang('ΣΠ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somcm', '<span class="tooltipster" title="'.gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ').'">'.gks_lang('ΠΣΜ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopd',  '<span class="tooltipster" title="'.gks_lang('Πληροφορίες Συσκευασίας Διακίνησης').'">'.gks_lang('ΠΣΔ').'</span>'); ?></th>   

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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_acc_journal'];?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-acc_journal-item.php?id=<?php echo $row['id_acc_journal'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_acc_journal'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_acc_journal'];?>" data-model="gks_acc_journal"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['acc_journal_code'];?></td>
    <td class="mytdcml"><?php echo $row['acc_journal_descr'];?></td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td class="mytdcml"><a href="admin-company-sub-item.php?id=<?php echo $row['company_sub_id'];?>"><?php echo $row['company_sub_title'];?></a></td>
    <td class="mytdcml"><?php echo $row['eidos_parastatikou_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['cc_seira'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['cc_inv'];?></td>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sortorder'];?></span>
    </td>
    
    <td class="mytdcm"><?php echo myimg010r($row['is_disable']);?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['acc_eidos_parastatikou_whi_id']!=0) echo '<img src="img/1.png" border="0" width="16">';
    ?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['acc_eidos_parastatikou_other_entity']!=0) echo '<img src="img/1.png" border="0" width="16">';
    ?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['journal_has_correlated_invoices']!=0) echo '<img src="img/1.png" border="0" width="16">';
    ?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['journal_has_multiple_connected_marks']!=0) echo '<img src="img/1.png" border="0" width="16">';
    ?></td>
    <td nowrap class="mytdcm"><?php
      if ($row['journal_has_packings_declarations']!=0) echo '<img src="img/1.png" border="0" width="16">';
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','delete',0);?>;

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


  $('#table_journals > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_acc_journal',mylist,'#table_journals > tbody');
    }
  });
  
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

