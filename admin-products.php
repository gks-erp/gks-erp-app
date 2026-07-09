<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Είδη');
$nav_active_array=array('manage','manage_menu_product','manage_product');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');



include_once('admin-products_filters.php');


$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>';
//echo $where1;
//echo $where2;
//echo "\r\n";
//echo $sql;
//echo '</pre>';
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


if ($datecheck!='' or $quantitycheck>0 or $sheetscheck>0) $url.='&datecheck='.urlencode(showDate(strtotime($datecheck), 'd/m/Y H:i', 1)).'&quantitycheck='.$quantitycheck.'&sheetscheck='.$sheetscheck;
//echo $url;

pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?';
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

if ($datecheck!='' or $quantitycheck>0 or $sheetscheck>0) $sortable_url.='&datecheck='.urlencode(showDate(strtotime($datecheck), 'd/m/Y H:i', 1)).'&quantitycheck='.$quantitycheck.'&sheetscheck='.$sheetscheck;

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}


$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
//echo '<pre>';
//echo $sql;
//echo '</pre>';
?>
<style>
#lightgallery_user > tbody > tr > td {
  vertical-align: middle;
}  
#lightgallery_user > tbody > tr > th {
  vertical-align: middle;
}  
#lightgallery_user > tbody > tr > .tdimg {
  padding:0px;
}  
#dialog_print_zoom_slider_handle {
  width: 50px;
  height: 30px;
  top: 50%;
  margin-top: -15px;
  text-align: center;
  line-height: 1.6em;
  padding: 5px 5px;
  margin-left: -25px;
  cursor: pointer;
}

</style>
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
      <a class="btn btn-primary gks_add_new_record" href="admin-products-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου είδους');?></a>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>





<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php if ($datecheck!='') echo '<input style="display:none;" type="text" name="datecheck" value="'.showDate(strtotime($datecheck), 'd/m/Y H:i', 1).'" />'; ?>
      <?php if ($quantitycheck>0) echo '<input style="display:none;" type="text" name="quantitycheck" value="'.$quantitycheck.'" />'; ?>
      <?php if ($sheetscheck>0) echo '<input style="display:none;" type="text" name="sheetscheck" value="'.$sheetscheck.'" />'; ?>
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>
<table border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td style="text-align: center;">
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form2" id="filter-form2">
      <?php echo gks_lang('Ημερομηνία');?>:
      <input type="text" name="datecheck" value="<?php if ($datecheck!='') echo showDate(strtotime($datecheck), 'd/m/Y H:i', 1);?>" class="form-control form-control-sm"
        style="width: 150px;display: inline;vertical-align: middle;margin-right: 30px;" />
      
      

      <?php echo gks_lang('Τεμάχια');?>: 
      <input type="text" name="quantitycheck" value="<?php if ($quantitycheck>0) echo $quantitycheck;?>" class="form-control form-control-sm"
        style="width: 150px;display: inline;vertical-align: middle;margin-right: 30px;" />
<?php if ($GKS_ORDERS_SETS) { ?>
      <?php echo gks_lang('Φύλλα-Σελίδες ανά τεμάχιο');?>:   
      <input type="text" name="sheetscheck" value="<?php if ($sheetscheck>0) echo $sheetscheck;?>" class="form-control form-control-sm"
        style="width: 150px;display: inline;vertical-align: middle;margin-right: 30px;" />
<?php } ?>        
<?php 
  foreach ($_GET as $mygetkey => $mygetval) {
    if ($mygetkey!='datecheck' and $mygetkey!='quantitycheck' and $mygetkey!='sheetscheck') {
      echo '<input style="display:none;" type="text" name="'.$mygetkey.'" value="'.$mygetval.'" />';
    }
  }   
  
?>

      <input class="btn btn-primary btn-sm" type="submit" value="<?php echo gks_lang('Έλεγχος Τιμών');?>">
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="lightgallery_user">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sokostos', '<span class="tooltipster" title="'.gks_lang('Τιμή Αγοράς χωρίς ΦΠΑ').'">'.gks_lang('Αγορά').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricey', '<span class="tooltipster" title="'.gks_lang('Τιμή ΥπερΧονδρικής').'">'.gks_lang('ΤιμήΥΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_yperx_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην ΥπερΧονδρική').'">'.gks_lang('ΤΦYX').'</span>'); ?></th>        
        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', '<span class="tooltipster" title="'.gks_lang('Τιμή Χονδρικής').'">'.gks_lang('ΤιμήΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην Χονδρική').'">'.gks_lang('ΤΦΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricer', '<span class="tooltipster" title="'.gks_lang('Τιμή Λιανικής').'">'.gks_lang('ΤιμήΛ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_retail_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην Λιανική').'">'.gks_lang('ΤΦΛ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofpa_base_descr', gks_lang('ΦΠΑ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', '<span class="tooltipster" title="'.gks_lang('Ενεργό').'">'.gks_lang('Εν').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socansell', '<span class="tooltipster" title="'.gks_lang('Μπορεί να πωληθεί').'">'.gks_lang('Πω').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socanbuy', '<span class="tooltipster" title="'.gks_lang('Μπορεί να αγορασθεί').'">'.gks_lang('Αγ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somm', '<span class="tooltipster" title="'.gks_lang('Μονάδα Μέτρησης').'">'.gks_lang('ΜΜ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobtype', gks_lang('Τύπος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soneed_apostoli', '<span class="tooltipster" title="'.gks_lang('Χρειάζεται αποστολή').'">'.gks_lang('ΧΑ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sovaros', '<span class="tooltipster" title="'.gks_lang('Βάρος').'">'.gks_lang('gr').'</span>'); ?></th>        
        
        
        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soogos', '<span class="tooltipster" title="'.gks_lang('Όγκος').'">'.gks_lang('cm').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sois_digital', '<span class="tooltipster" title="'.gks_lang('Ψηφιακό').'">'.gks_lang('Ψη').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sois_simple_download', '<span class="tooltipster" title="'.gks_lang('Απλό Download').'">'.gks_lang('SD').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soneed_multi_files', '<span class="tooltipster" title="'.gks_lang('Απαιτούνται αρχεία').'">'.gks_lang('ΑΑ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socount_var', '<span class="tooltipster" title="'.gks_lang('Παραλλαγές').'">'.gks_lang('Παρ').'</span>'); ?></th>        

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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-products-item.php?id=<?php echo $row['id_product'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_product'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_product'];?>" data-model="gks_eshop_products"></i></td>
        </tr>      
      </table>
    </td>

    
    <td class="tdimg"><?php 
    $myimgurl=trim_gks($row['product_photo_p'].'');
    if ($myimgurl == '') {
      $myimgurl="/my/img/product.png";
      echo '<img src="/my/img/product.png" border="0" style="max-width:64px;max-height:64px;"/>';
    } else {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      echo '<a class="lightgalleryitem_user" href="'.$photo_url.'" data-sub-html="'.$row['product_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>
    <td nowrap class="mytdcml"><?php echo $row['product_code'];?></td>
    <td ><?php echo $row['product_descr_p'];?></td>

    <td nowrap class="mytdcm"><?php echo myCurrencyFormat(($quantitycheck>0 ? $quantitycheck*$row['product_kostos']: $row['product_kostos']));?></td>

    <td nowrap class="mytdcm"><?php echo myCurrencyFormat((($quantitycheck>0 or $sheetscheck>0) ? $row['quantitycheck_price_yperx'] : $row['product_price_yperx_calc']));?></td>
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_price_yperx_include_vat']);?></td>
    
    <td nowrap class="mytdcm"><?php echo myCurrencyFormat((($quantitycheck>0 or $sheetscheck>0) ? $row['quantitycheck_price'] : $row['product_price_calc']));?></td>
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_price_include_vat']);?></td>

    <td nowrap class="mytdcm"><?php echo myCurrencyFormat((($quantitycheck>0 or $sheetscheck>0) ? $row['quantitycheck_price_retail'] : $row['product_price_retail_calc']));?></td>
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_price_retail_include_vat']);?></td>

    <td        class="mytdcm"><?php echo $row['fpa_base_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo myimg010r($row['product_disable']);?></td> 
    
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_can_sell']);?></td> 
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_can_buy']);?></td> 
    <td nowrap class="mytdcm"><?php echo $row['monada_symbol'];?></td> 
    <td nowrap class="mytdcm"><?php echo gks_product_base_type_descr($row['product_base_type']);?></td>     
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_need_apostoli']);?></td>     
    <td nowrap class="mytdcm"><?php if ($row['product_varos']!=0) echo number_format($row['product_varos'],0,',','.');?></td>
    <td nowrap class="mytdcm"><?php 
      $out='';
      if ($row['product_ogos_x']!=0) $out.=number_format($row['product_ogos_x'],2,',','.').'<br>';
      if ($row['product_ogos_y']!=0) $out.=number_format($row['product_ogos_y'],2,',','.').'<br>';
      if ($row['product_ogos_z']!=0) $out.=number_format($row['product_ogos_z'],2,',','.').'<br>';
      if ($out!='') $out=substr($out, 0, strlen($out)-4);
      echo $out;
      ?></td>
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_is_digital']);?></td> 
    <td nowrap class="mytdcm"><?php echo myimg01($row['product_is_simple_download']);?></td> 
    <td nowrap class="mytdcm"><?php 
      echo myimg01($row['product_need_multi_files']);
      if ($row['product_need_multi_files']) echo '<br>['. $row['product_need_multi_files_min'].'-'.$row['product_need_multi_files_max'].']';      ?>
    </td>    
    <td nowrap class="mytdcm"><?php if (isset($row['count_var']) and $row['count_var']!=0) echo $row['count_var'];?></td> 
    
    
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

<div class="container-fluid" id="gks_rsrv_f_static">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print"><?php echo gks_lang('Εκτύπωση');?> <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button>
    </div>
  </div>
</div> 


<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group1 row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Εκτύπωσης');?></div>
    </div>
        
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_pdf"  value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
          <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_html" value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
          <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_jpg" value="3" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
          <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:15px;vertical-align: top;"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Προσανατολισμός');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
          <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
          <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:120%"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
          <img src="img/palette-color.png" border="0" width="15" style="vertical-align: top;">
          </label>
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
          <img src="img/palette-gray.png" border="0" width="15" style="vertical-align: top;">
          </label>
      </div>
    </div>    

    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Μεγέθυνση');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left">
        <div id="dialog_print_zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
          <div id="dialog_print_zoom_slider_handle" class="ui-slider-handle"></div>
        </div>
      </div>
    </div>

    
    <div class="row" >
      <div class="gks_print_thump_container">
<?php
  $user_def_form_id=0;
  if (isset($gks_user_settings['print']['form_id_products'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_products']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_eshop_products'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    
    $perm_company_ids=trim_gks($row_print_forms['perm_company_ids']);
    $perm_acc_journal_ids=trim_gks($row_print_forms['perm_acc_journal_ids']);
    $perm_acc_seires_ids=trim_gks($row_print_forms['perm_acc_seires_ids']);

    $temp=array('id'=>intval($row_print_forms['id_print_form']));
    if ($perm_company_ids!='') $temp['perm_company_ids']=unserialize($perm_company_ids);
    if ($perm_acc_journal_ids!='') $temp['perm_acc_journal_ids']=unserialize($perm_acc_journal_ids);
    if ($perm_acc_seires_ids!='') $temp['perm_acc_seires_ids']=unserialize($perm_acc_seires_ids);
    $perm_print_forms[]=$temp;
    
    $div_form='<div class="gks_print_thump_div '.
      ($user_def_form_id==$row_print_forms['id_print_form'] ? 'gks_print_thump_div_selected' : '').
      '" data-form_id="'.$row_print_forms['id_print_form'].'" '.
      'data-lang="'.$row_print_forms['gks_lang'].'" '.
      'data-file_type="'.$row_print_forms['file_type'].'" '.
      'data-landscape="'.$row_print_forms['is_landscape'].'" '.
      'data-grayscale="'.$row_print_forms['grayscale'].'" '.
      'data-zoom="'.intval($row_print_forms['zoom']*100).'" '.
      '>';
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>';
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>';
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>';
      
    
    $div_form.='</div>';
    echo $div_form;
  }
  
  $div_form='<div id="gks_print_thump_more_div">';
    $div_form.='<div id="gks_print_thump_more_text"><i class="fas fa-plus-circle" style="font-size:200%;color:#35dc35;"></i><br>'.gks_lang('Εμφάνιση όλων').'</div>';
  $div_form.='</div>';
  echo $div_form;
  

?>      
      </div>
    </div>
<?php
  

  

  
?>    
  


  </div>  
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_products']) ? $gks_user_settings['print']['form_id_products'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_products']));?>'));

var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>




});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-products.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
//db_close();
include_once('_my_footer_admin.php');


