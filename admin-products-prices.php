<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Τιμές Ειδών');
$nav_active_array=array('manage','manage_menu_product','manage_product_prices');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$sql_plist="select id_pricelist,pricelist_descr
from gks_eshop_pricelist
where pricelist_disable=0
and id_pricelist>10000
order by sortorder";
$result_plist = $db_link->query($sql_plist);        
if (!$result_plist) debug_mail(false,'error sql',$sql_plist);
if (!$result_plist) die('sql error');
$plist=[];
$gks_eshop_products_extra_sqls=[
  'select'=> '',
  'from'=> '',
  'left_join'=> '',
  'so'=>[],
];
  
while ($row_plist = $result_plist->fetch_assoc()) {
  $plid=intval($row_plist['id_pricelist']);
  
  $gks_eshop_products_extra_sqls['select'].=
  'plist_table_'.$plid.'.price_'.$plid.','.
  'plist_table_'.$plid.'.price_sale_'.$plid.','.
  'plist_table_'.$plid.'.include_vat_'.$plid.',';
  
  $gks_eshop_products_extra_sqls['from'].='(';
  $gks_eshop_products_extra_sqls['left_join'].='
  LEFT JOIN (
    SELECT product_id, 
    product_price_plist AS price_'.$plid.', 
    product_price_plist_sale AS price_sale_'.$plid.', 
    product_price_plist_include_vat AS include_vat_'.$plid.'
    FROM gks_eshop_products_prices
    WHERE pricelist_id='.$plid.'
  ) AS plist_table_'.$plid.' ON gks_eshop_products.id_product = plist_table_'.$plid.'.product_id)
  ';

  $gks_eshop_products_extra_sqls['so'][]=array('name' => 'soplist_'.$plid, 'field' => 'plist_table_'.$plid.'.price_'.$plid);
  $gks_eshop_products_extra_sqls['so'][]=array('name' => 'soplist_sale_'.$plid, 'field' => 'plist_table_'.$plid.'.price_sale_'.$plid);
  $gks_eshop_products_extra_sqls['so'][]=array('name' => 'soplist_vat_'.$plid, 'field' => 'plist_table_'.$plid.'.include_vat_'.$plid);
  
  
  $plist[]=$row_plist;
}

if (count($plist)>0) {
  $gks_eshop_products_extra_sqls['select']=
    ','.
    substr($gks_eshop_products_extra_sqls['select'],
           0,
           strlen($gks_eshop_products_extra_sqls['select'])-1);
}

//print '<pre>';print_r($plist);print_r($gks_eshop_products_extra_sqls);die();



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
.gks_input_kostos,
.gks_input_price_yperx,
.gks_input_price_yperx_sale,
.gks_input_price,
.gks_input_price_sale,
.gks_input_price_retail,
.gks_input_price_retail_sale,
.gks_input_price_plist,
.gks_input_price_plist_sale {
  min-width:80px;
  text-align1:right;
  padding:0px 4px;
}
.gks_input_price_yperx_include_vat,
.gks_input_price_include_vat,
.gks_input_price_retail_include_vat, 
.gks_input_price_plist_include_vat {
  vertical-align: middle;
}

.gks_input_checkbox_span {
  display: flex;
  padding: 8px;
  margin: 0px;
  background-color1: red;
  flex-direction: row;
  justify-content: center;
  border-radius: 0.2rem;
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
      <?php echo $filter['html']; ?>
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

        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_yperx_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην ΥπερΧονδρική').'">'.gks_lang('ΤΦYX').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricey', '<span class="tooltipster" title="'.gks_lang('Τιμή ΥπερΧονδρικής').'">'.gks_lang('ΤιμήΥΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopriceys', '<span class="tooltipster" title="'.gks_lang('Τιμή Προσφοράς ΥπερΧονδρικής').'">'.gks_lang('ΤιμήΠΥΧ').'</span>'); ?></th>        

        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην Χονδρική').'">'.gks_lang('ΤΦΧ').'</span>'); ?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', '<span class="tooltipster" title="'.gks_lang('Τιμή Χονδρικής').'">'.gks_lang('ΤιμήΧ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprices', '<span class="tooltipster" title="'.gks_lang('Τιμή Προσφοράς Χονδρικής').'">'.gks_lang('ΤιμήΠΧ').'</span>'); ?></th> 

        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice_retail_include_vat', '<span class="tooltipster" title="'.gks_lang('Η τιμή περιέχει ΦΠΑ στην Λιανική').'">'.gks_lang('ΤΦΛ').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricer', '<span class="tooltipster" title="'.gks_lang('Τιμή Λιανικής').'">'.gks_lang('ΤιμήΛ').'</span>'); ?></th>    
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricers', '<span class="tooltipster" title="'.gks_lang('Τιμή Προσφοράς Λιανικής').'">'.gks_lang('ΤιμήΠΛ').'</span>'); ?></th>             
<?php
foreach ($plist as $row_plist) {?>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soplist_vat_'.$row_plist['id_pricelist'], '<span class="tooltipster" title="'.$row_plist['pricelist_descr'].': '.gks_lang('Η τιμή περιέχει ΦΠΑ').'">'.gks_lang('ΦΠΑ').'<br>'.$row_plist['pricelist_descr'].'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soplist_'.$row_plist['id_pricelist'],     '<span class="tooltipster" title="'.$row_plist['pricelist_descr'].'">'.$row_plist['pricelist_descr'].'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soplist_sale_'.$row_plist['id_pricelist'],'<span class="tooltipster" title="'.$row_plist['pricelist_descr'].': '.gks_lang('Τιμή Προσφοράς').'">'.gks_lang('Προσφορά').'<br>'.$row_plist['pricelist_descr'].'</span>'); ?></th>        
<?php } 

?>

      
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-aa="<?php echo $i;?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-products-item.php?id=<?php echo $row['id_product'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_product'];?></td>
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

    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_kostos form-control form-control-sm" 
      data-cid="1"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_kostos'];?>"></td>

    <td nowrap class="mytdcm"><span class="gks_input_checkbox_span"><input type="checkbox"
      class="gks_input_price_yperx_include_vat"
      data-cid="2"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      <?php if ($row['product_price_yperx_include_vat']==1) echo 'checked';?>></span></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price_yperx form-control form-control-sm" 
      data-cid="3"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price_yperx'];?>"></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price_yperx_sale form-control form-control-sm" 
      data-cid="4"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price_yperx_sale'];?>"></td>

    <td nowrap class="mytdcm"><span class="gks_input_checkbox_span"><input type="checkbox"
      class="gks_input_price_include_vat"
      data-cid="5"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      <?php if ($row['product_price_include_vat']==1) echo 'checked';?>></span></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price form-control form-control-sm" 
      data-cid="6"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price'];?>"></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price_sale form-control form-control-sm" 
      data-cid="7"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price_sale'];?>"></td>

    <td nowrap class="mytdcm"><span class="gks_input_checkbox_span"><input type="checkbox"
      class="gks_input_price_retail_include_vat"
      data-cid="8"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      <?php if ($row['product_price_retail_include_vat']==1) echo 'checked';?>></span></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price_retail form-control form-control-sm" 
      data-cid="9"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price_retail'];?>"></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_input_price_retail_sale form-control form-control-sm" 
      data-cid="10"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['product_price_retail_sale'];?>"></td>

<?php
$cid=10;
foreach ($plist as $row_plist) {
  $plid=intval($row_plist['id_pricelist']);
  ?>
    <td nowrap class="mytdcm"><span class="gks_input_checkbox_span"><input type="checkbox"
      class="gks_lpist1_<?php echo $plid;?> gks_input_price_plist_include_vat"
      data-cid="<?php echo $cid+1;?>"
      data-plid="<?php echo $plid;?>"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      <?php if ($row['include_vat_'.$plid]==1) echo 'checked';?>></span></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_lpist2_<?php echo $plid;?> gks_input_price_plist form-control form-control-sm" 
      data-cid="<?php echo $cid+2;?>"
      data-plid="<?php echo $plid;?>"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['price_'.$plid];?>"></td>
    <td nowrap class="mytdcm"><input type="number" 
      class="gks_lpist3_<?php echo $plid;?> gks_input_price_plist_sale form-control form-control-sm" 
      data-cid="<?php echo $cid+3;?>"
      data-plid="<?php echo $plid;?>"
      data-aa="<?php echo $i;?>"
      data-id="<?php echo $row['id_product'];?>"
      value="<?php echo $row['price_sale_'.$plid];?>"></td>


<?php 
  $cid+=3;
} 

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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

var from_php_max_cid=<?php echo 10+count($plist)*3;?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>




});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-products-prices.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
//db_close();
include_once('_my_footer_admin.php');


