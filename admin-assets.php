<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Πάγια');
$nav_active_array=array('assets','assets_assets');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_assets_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets','delete',0);



include_once('admin-assets_filters.php');

if (isset($_GET['mass_message']) and intval($_GET['mass_message'])==1) {
  $result = $db_link->query($sql); 
  if (!$result) debug_mail(false,'error sql mm',$sql);
  if (!$result) die('sql error mm');
  
  $ids=[];
  while ($row = $result->fetch_assoc()) {
    if (!empty($row['asset_last_user_id'])) {
      if (in_array($row['asset_last_user_id'],$ids)==false) {
        $ids[]=$row['asset_last_user_id']; 
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




$temp_sql_file = date('Y_m_d_H_i_s').'_'.rand(1000,9999).rand(1000,9999).rand(1000,9999);
file_put_contents(GKS_SITE_PATH.'tmp/'.$temp_sql_file.'.sql',str_replace(' SQL_CALC_FOUND_ROWS ',' ',$sql));
//setcookie('gks_assets_select_apografi_id', $temp_sql_file, time()+24*3600);  /* expire in 24 hour */



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
      <a class="btn btn-primary gks_add_new_record" href="admin-assets-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου παγίου');?></a>
      
      <button class="btn btn-primary tooltipster" id="create_apografi" data-id="<?php echo $temp_sql_file;?>" title="<?php echo gks_lang('Kάνοντας κλικ στο κουμπί <b>Δημιουργία απογραφής</b><br>θα δημιουργηθεί μία νέα απογραφή με τα επιλεγμένα πάγια.');?>"><?php echo gks_lang('Δημιουργία απογραφής');?></button>
      
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
<?php
    $show_pos=false;
    $show_is_fotografou=false;
    $show_oximata=false;
    $show_pcs=false;
    $show_thesi=false;
    $show_terminalid=false;
    $show_ds40_ds620=false;


    
    $row_array=array();
    while ($row = $result->fetch_assoc()) {
      $row_array[]=$row;

      if ($row['asset_type']==24 or $row['asset_type']==25) //POS apisrmato, POS ensirmato
        $show_pos=true;
      if ($row['asset_type']==26) //Oximata
        $show_oximata=true;
      if ($row['asset_type']==13 or $row['asset_type']==14) ////statheres monades, Laptop
        $show_pcs=true;
      if ($row['asset_type']==13) ////statheres monades PCs
        $show_thesi=true;
      if ($row['asset_type']==23 or $row['asset_type']==24 or $row['asset_type']==25 or $row['asset_type']==27) ////Tablets, mobile, POS wireless, POS wire
        $show_terminalid=true;
      if ($row['asset_type']==5 or $row['asset_type']==8) //foritoi ektipotes DS-40 DS-620
        $show_ds40_ds620=true;
    }
?>      

  
  <tr>	
    <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Περιγραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soserialnumber', 'Serial Number'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse_id', '<span class="tooltipster" title="'.gks_lang('Η Αποθήκη στην οποία είναι χρεωμένο').'">'.gks_lang('Αποθήκη').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser_id', '<span class="tooltipster" title="'.gks_lang('Ο Συνεργάτης στον οποίο είναι χρεωμένο').'">'.gks_lang('Συνεργάτης').'</span>'); ?></th>                
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>   
  <?php if ($show_pos) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobank_descr', gks_lang('Τράπεζα')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soxreosi_val', gks_lang('Χρέωση')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soxreosi_type', gks_lang('Τύπος χρέωσης')); ?></th>   
  <?php } ?>
           
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soagoras', gks_lang('Ημερομηνία<br>Ενεργοποίησης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodiakopis', gks_lang('Ημερομηνία<br>Απόσυρσης')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soapografi_date', gks_lang('Ημερομηνία<br>Απογραφής')); ?></th>        
    
    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo gks_lang('Σχόλιο');?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sois_fotografou', gks_lang('Είναι του<br>Συνεργάτη')); ?></th>   
  <?php if ($show_ds40_ds620) {?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solifecount', 'Life Count'); ?></th>   
  <?php } ?>
  <?php if ($show_oximata) {?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soelastika', gks_lang('Ελαστικά')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sokm', '<span class="tooltipster" title="'.gks_lang('Χιλιόμετρα τώρα').'">Km</span>'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonextskm', '<span class="tooltipster" title="'.gks_lang('Επόμενο Service σε Km').'">'.gks_lang('Service').'</span>'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sokteo', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Επόμενου ΚΤΕΟ').' ('.gks_lang('Κέντρο Τεχνικού Ελέγχου Οχημάτων').')'.'">'.gks_lang('ΚΤΕΟ').'</span>'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soasf', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Λήξης Ασφάλειας').'">'.gks_lang('Ασφάλιση').'</span>'); ?></th>   

  <?php } ?>
  <?php if ($show_thesi) {?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sothesi', gks_lang('Θέση')); ?></th>   
  <?php } ?>
  <?php if ($show_pcs) {?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somac', 'Mac Address'); ?></th>   
  <?php } ?>
   
  <?php if ($show_terminalid) {?>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soviva', 'Viva'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somegeftpos', 'Meg'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somellon', 'Mellon'); ?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socardlink', 'Cardlink'); ?></th>   
  <?php } ?>

<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>

  
  </tr>        
</thead>
<tbody>
  
    <?php
    $i = 0;
    foreach ($row_array as $row) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-assets-item.php?id=<?php echo $row['id_asset'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_asset'];?></td>
          <?php if ($perm_gks_assets_delete) {?>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_asset'];?>" data-model="gks_assets"></i></td>
          <?php } ?>
        </tr>      
      </table>
    </td>
        
    
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
        
  
    
    <td class="mytdcml" nowrap><?php echo $row['asset_code'];?></td>
    <td class="mytdcml"><?php echo $row['asset_title'];?></td>
    <td class="mytdcml" nowrap><?php echo $row['asset_serialnumber']; ?></td>
    <td class="mytdcml"><?php echo $row['asset_type_descr'];?></td>

    
    <td class="mytdcml"><a href="admin-warehouse-item.php?id=<?php echo $row['asset_last_warehouse_id'];?>"><?php echo $row['warehouse_name'];?></a></td>   
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['asset_last_user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['asset_last_company_id'];?>"><?php echo $row['company_title'];?></a></td>
  <?php if ($show_pos) {?>
    <td class="mytdcml" nowrap><a href="admin-banks-item.php?id=<?php echo $row['bank_id'];?>"><?php echo $row['bank_descr'];?></a></td>
    <td class="mytdcm" nowrap><?php if ($row['xreosi_val']!=0) echo number_format($row['xreosi_val'],2,',','.');?></td>
    <td class="mytdcm" nowrap><?php
      if ($row['xreosi_type']==1) echo 'FREE';
      else if ($row['xreosi_type']==2) echo gks_lang('ΑΝΑ ΜΗΝΑ');
    ?></td>
  <?php } ?>
    
    <td class="mytdcm" nowrap><?php if (isset($row['asset_date_activate'])) echo showDate(strtotime($row['asset_date_activate']), 'd/m/Y', 1);?></td>   
    <td class="mytdcm" nowrap><?php if (isset($row['asset_date_aposirsi'])) echo showDate(strtotime($row['asset_date_aposirsi']), 'd/m/Y', 1);?></td> 
    <td class="mytdcm" nowrap><?php if (isset($row['max_apografi_date'])) echo showDate(strtotime($row['max_apografi_date']), 'd/m/Y', 1);?></td> 
    
      
    <td class="mytdcml"><?php echo $row['asset_sxolio']; ?></td>
    <td class="mytdcm"><?php echo myimg010r($row['asset_disable']);?></td>
    
    <td class="mytdcm" nowrap><img src="img/<?php echo $row['is_fotografou']==0 ? "0" :"1";  ?>.png" border="0" width="16"></td>
  <?php if ($show_ds40_ds620) {?>
    <td class="mytdcm" nowrap><?php if ($row['ds620_40_lifecount']>0) echo number_format($row['ds620_40_lifecount'],0,'','.'); ?></td>
  <?php } ?>
  
  
  
  <?php if ($show_oximata) {?>
    <td class="mytdcm" nowrap><?php echo $row['oxima_elastika']; ?></td>
    <td class="mytdcm" nowrap><?php if ($row['oxima_km']<>0) echo number_format($row['oxima_km'],0,'','.'); ?></td>
    <td class="mytdcm" nowrap><?php if ($row['oxima_next_service_km']<>0) echo number_format($row['oxima_next_service_km'],0,'','.'); ?></td>
    <td class="mytdcm" nowrap><?php if (isset($row['oxima_next_kteo'])) echo date('d/m/Y', strtotime($row['oxima_next_kteo']));?></td>   
    <td class="mytdcm" nowrap><?php if (isset($row['oxima_liji_asfaleia'])) echo date('d/m/Y', strtotime($row['oxima_liji_asfaleia']));?></td>   


  <?php } ?>
  <?php if ($show_thesi) {?>
    <td class="mytdcml" nowrap><?php 
      echo $row['asset_thesi']; ?></td>
  <?php } ?>
  <?php if ($show_pcs) {?>
    <td class="mytdcm" nowrap><?php 
      echo str_replace(',','<br>',$row['mac_address']); ?></td>
  <?php } ?>
  <?php if ($show_terminalid) {?>
    <td class="mytdcml" nowrap><?php 
      if (empty($row['viva_terminal_id'])==false) 
      echo '<img src="img/1.png" style="width:24px;">'; ?></td>
    <td class="mytdcml" nowrap><?php 
      if (empty($row['megeftpos_terminal_id'])==false) 
      echo '<img src="img/1.png" style="width:24px;">'; ?></td>
    <td class="mytdcml" nowrap><?php 
      if (empty($row['mellon_terminal_id'])==false) 
      echo '<img src="img/1.png" style="width:24px;">'; ?></td>
    <td class="mytdcml" nowrap><?php 
      if (empty($row['cardlink_terminal_id'])==false) 
      echo '<img src="img/1.png" style="width:24px;">'; ?></td>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('#fdate_agoras-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_agoras-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdate_diakopis-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_diakopis-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fmax_apografi_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmax_apografi_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  


  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
            
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_agoras' || sname=='fdate_diakopis' || sname=='fmax_apografi_date') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_agoras' || sname=='fdate_diakopis' || sname=='fmax_apografi_date') {
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


  
  $('#create_apografi').click(function() {
    
    newaddid=$('#create_apografi').attr('data-id');
    //console.log(newaddid);
    datasend='newaddid=' + newaddid;
    
    $('body').addClass("myloading");  
    $.ajax({
			url: '/my/admin-assets-create-apografi.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
				  $("body").removeClass("myloading");
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					window.location.href = $.base64.decode(data.redirect);
					} else {
					  $("body").removeClass("myloading");
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});      
    
  });
    
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


