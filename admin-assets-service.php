<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Service Παγίου');
$nav_active_array=array('assets','assets_service');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_assets_service_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_service','delete',0);

include_once('admin-assets-service_filters.php');

if (isset($_GET['mass_message']) and intval($_GET['mass_message'])==1) {
  $result = $db_link->query($sql); 
  if (!$result) debug_mail(false,'error sql mm',$sql);
  if (!$result) die('sql error mm');
  
  $ids=[];
  while ($row = $result->fetch_assoc()) {
    if (!empty($row['mixanikos_id'])) {
      if (in_array($row['mixanikos_id'],$ids)==false) {
        $ids[]=$row['mixanikos_id']; 
      }
    }
  }
  //echo '<pre>';print_r($ids);die();
  $filename=date('YmdHis').rand(1000,9999).rand(1000,9999).rand(1000,9999);
  $filepath=GKS_SITE_PATH.'tmp/mass_message_'.$filename.'.json';
  $ret=@file_put_contents($filepath,json_encode($ids));
  if ($ret==false) {
    debug_mail(false,'error write file',$filepath);    
    echo '<pre>Error write file '.$filepath.'</pre>';
    die();}
  
  header('Location: admin-mass-messages-new.php?list='.$filename);
  die();
  
  
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

<style>
#lightgallery_user > tbody > tr > .tdimg {
    padding: 0px;
}  
  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-assets-service-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου service παγίου');?></a>
      <?php
      $mass_url=''; 
      if (isset($_SERVER['QUERY_STRING'])) $mass_url=$_SERVER['QUERY_STRING'];
      //$parts=explode('#',
      $mass_url.='&mass_message=1';
      $mass_url=$_SERVER['SCRIPT_NAME'].'?'.$mass_url;
      ?>
      <a class="btn btn-primary gks_add_new_mass_message" href="<?php echo $mass_url;?>"><?php echo gks_lang('Μαζική αποστολή');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="lightgallery_user">
<thead>
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap ><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_send', gks_lang('Ημερομηνία<br>Αποστολής')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasset_code', gks_lang('Πάγιο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse_name', gks_lang('Αποθήκη')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soreasons_descr', gks_lang('Αιτία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaitiolog', gks_lang('Σχόλιο<br>Αποστολής')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogks_nickname', gks_lang('Τεχνικός')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_return', gks_lang('Ημερομηνία<br>Επιστροφής')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaitiolog2', gks_lang('Σχόλιο<br>Επιστροφής')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soajia', gks_lang('Αξία')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soisconfirm', '<span class="tooltipster" title="'.gks_lang('Επιβεβαιωμένο').'">'.gks_lang('Επιβ.').'</span>'); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souseredit', gks_lang('Χρήστης')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soip_edit', 'IP'); ?></th>

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
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_assets_service'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-assets-service-item.php?id=<?php echo $row['id_assets_service'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_assets_service_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_assets_service'];?>" data-model="gks_assets_service"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td class="mytdcm" nowrap><?php echo showDate(strtotime($row['mydate_send']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    
    <td class="tdimg mytdcm"><?php 
    $myimgurl=trim_gks($row['asset_photo'].'');
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
      echo '<a class="lightgalleryitem_user" href="'.$photo_url.'" data-sub-html="'.$row['asset_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>
        
  

    <td class="mytdcml"><a href="admin-assets-item.php?id=<?php echo $row['asset_id'];?>"><?php if (isset($row['id_asset'])) echo $row['asset_code'].' - '.$row['asset_title'].' - '.$row['asset_serialnumber'];?></a></td>
    <td class="mytdcml"><?php echo '<a href="admin-warehouses-item.php?id='.$row['warehouse_id'].'">'.$row['warehouse_name'].'</a>';?></td>  
    <td class="mytdcml"><?php echo $row['reasons_descr']; ?></td>
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog'])); ?></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['mixanikos_id'].'">'.$row['gks_nickname'].'</a>';?></td>  
    <td class="mytdcm" nowrap><?php if (isset($row['mydate_return'])) echo showDate(strtotime($row['mydate_return']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog2'])); ?></td>
    <td class="mytdcmr" nowrap><?php if ($row['ajia']>0) echo number_format($row['ajia'],2,',','.'); ?></td>
    <td class="mytdcm" nowrap><img src="img/<?php echo $row['isconfirm']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['useredit'].'</a>';?></td>  
    
    

    <td class="mytdcml"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>">V</a> 




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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  $('#fmydate_send-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate_send-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate_return-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate_return-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));



  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
            
      if (v==-2) { //is_custom_date
        if (sname == 'fmydate_send' || sname=='fmydate_return') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fmydate_send' || sname=='fmydate_return') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true
  });  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


