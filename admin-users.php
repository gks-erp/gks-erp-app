<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Επαφές');
$nav_active_array=array('manage','manage_users');

db_open();
stat_record();





$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_wp_users_delete=gks_permission_user_can_action_php($my_wp_user_id,'wp_users','delete',0);

//print '<pre>';print_r(gks_permission_user_can_action($my_wp_user_id, 'wp_users','delete',0));die();
//$perm_company_subs_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','view',0);
//$perm_company_subs_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','edit',0);
//$perm_company_subs_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','add',0);
//$perm_company_subs_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','delete',0);

//readonly : (from_php_perm_ret_edit ? 0 : 1),
//if (from_php_perm_ret_edit==false) $('#task_planned_date_from').periodpicker('disable');
//print '<pre>';print_r($perm_ret);die();
//$is_whi_mov_manager

// submit_button_print ??? ti tha ginei me ta dikaiomata ektiposis ?
//export to excel permitions







include_once('admin-users_filters.php');

if (isset($_GET['mass_message']) and intval($_GET['mass_message'])==1) {
  $result = $db_link->query($sql); 
  if (!$result) debug_mail(false,'error sql mm',$sql);
  if (!$result) die('sql error mm');
  
  $ids=[];
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['id'],$ids)==false) {
      $ids[]=$row['id']; 
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

$show_pososta=false;
if (ur_ad() or ur_hr() or ur_lo()) {
  $show_pososta = true;  
}
$showadminlohr=false;
if (ur_ad() or ur_hr() or ur_lo()) {
  $showadminlohr = true;
} 
//if (ur_ad()) echo 'hhh';
//echo $showadminlohr;die();

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();
 
include_once('_my_header_admin.php');
?>
<style>
.fa-list-alt {
  font-size:18px; 
  vertical-align:middle;
}  
.fa-file-word {
  font-size:16px; 
  vertical-align:middle;
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
      
      <a class="btn btn-primary gks_add_new_record" href="admin-users-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας επαφής');?></a>
      <?php if (ur_ad() or ur_hr() or ur_lo()) { 
      
        $sql_export_excel="SELECT id_export_excel, export_excel_descr FROM gks_export_excel WHERE export_excel_object='wp_users' and export_excel_disable=0 ORDER BY export_excel_descr;";
        $result_export_excel = $db_link->query($sql_export_excel);        
        if (!$result_export_excel) {debug_mail(false,'error sql',$sql_export_excel); die('sql error');}
        $export_excel=array();
        while ($row_export_excel = $result_export_excel->fetch_assoc()) {
          $export_excel[]=$row_export_excel;
        }
        if (count($export_excel)>0) { ?>
        <div class="btn-group" >
          <button type="button" class="btn btn-primary dropdown-toggle gks_export_excel" style="justify-content: center!important;" 
          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo gks_lang('Εξαγωγή σε Excel');?></button>
          <div class="dropdown-menu" style="background-color: #fafcff;box-shadow: 0px 4px 24px 12px rgb(0 0 0 / 20%), 0px 6px 20px 0px rgb(0 0 0 / 19%);">
            <?php
            foreach ($export_excel as $itemee) {
              echo '<a class="dropdown-item" href="admin-users-export-excel.php?'.$_SERVER['QUERY_STRING'].'&id_export_excel='.$itemee['id_export_excel'].'" download>'.$itemee['export_excel_descr'].'</a>';
            }?>
          </div>  
        </div>      
<?php   }
      } ?>    

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

<table class="table table-sm table-responsive11 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	 
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  nowrap><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopuser', '<span class="tooltipster" title="'.gks_lang('Ποσοστό συμπλήρωσης του προφίλ ως επισκέπτης').'">'.gks_lang('ΠΧ').'</span>').'<br>'; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopjob', '<span class="tooltipster" title="'.gks_lang('Ποσοστό συμπλήρωσης του προφίλ ως συνεργάτης').'">'.gks_lang('ΠΣ').'</span>'); 
          ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  nowrap><?php echo gks_lang('Φωτό');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadddate', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία προσθήκης').'">'.gks_lang('Ημερ.<br>Προσθ.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodname', '<span class="tooltipster" title="'.gks_lang('Εμφανιζόμενο Όνομα').'">'.gks_lang('Όνομα').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonickname', '<span class="tooltipster" title="'.gks_lang('Υποκοριστικό').'">'.gks_lang('Υποκοριστικό').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', gks_lang('email')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somobile', gks_lang('Τηλέφωνο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotziros', gks_lang('Τζίρος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobalance', gks_lang('Υπόλοιπο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soroles', gks_lang('Ρόλοι')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofiscal', '<span class="tooltipster" title="'.gks_lang('Φορολογική Θέση').'">'.gks_lang('Φορ. Θέση').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricelist', '<span class="tooltipster" title="'.gks_lang('Τιμοκατάλογος').'">'.gks_lang('Τιμοκ.').'</span>').'<br>'. 
               makeSortLink($sortable, $sortable_url, $_GET, 'sogekprosi', '<span class="tooltipster" title="'.gks_lang('Γενική Έκπτωση').'">'.gks_lang('Γ.Εκπτ').'</span>'); ?></th>   
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soafm', gks_lang('ΑΦΜ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', '<span class="tooltipster" title="'.gks_lang('Τίτλος Εταιρείας').'">'.gks_lang('Τίτλος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soeponimia', '<span class="tooltipster" title="'.gks_lang('Επωνυμία Εταιρείας').'">'.gks_lang('Επωνυμία').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>        

        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', gks_lang('Οδός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotk', gks_lang('ΤΚ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonomos', gks_lang('Νομός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soxora', gks_lang('Χώρα')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sogenisi_date', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Γέννησης').'">'.gks_lang('Ημ.Γεν.').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sousername', '<span class="tooltipster" title="'.gks_lang('Όνομα χρήστη').'">'.gks_lang('Ον. Χρήστη').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solastupdate', '<span class="tooltipster" title="'.gks_lang('Τελευταία ενημέρωση από τον χρήστη στις').'">'.gks_lang('ΤΕΧ').'</span>'); ?></th> 


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
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" style="width:33%;"><a href="admin-users-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td class="gks_ttd1" style="width:34%;"><?php echo $row['id'];?></td>
          <?php if ($perm_wp_users_delete) {?>
          <td class="gks_ttd1" style="width:33%;" ><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id'];?>" data-model="wp_users"></i></td>
          <?php } ?>
        </tr>
      </table>
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" style="width:33%;">
            <a href="admin-users-item-card.php?id=<?php echo $row['id'];?>"><i class="fas fa-list-alt gks_user_item_card" title="<?php echo gks_lang('Οικονομική Καρτέλα');?>"></i></a>
          </td>
          <td class="gks_ttd1" style="text-align:center;"  >
            <a href="admin-users-item-overview.php?id=<?php echo $row['id'];?>"><i class="fas fa-list-alt gks_user_item_overview" title="<?php echo gks_lang('Επισκόπηση');?>"></i></a>
          </td>
          <?php if ($showadminlohr) { ?>   
          <td class="gks_ttd1" style="text-align:right;"  >
            <a href="admin-users-item-export-logistis.php?id=<?php echo $row['id'];?>"><i class="fas fa-file-word" title="<?php echo gks_lang('Εξαγωγή σε Word');?>"></i></a>
          </td>
          <?php } ?>      
          
        </tr>
      </table>
      
    </td>
     
    <td class="mytdcm" nowrap><?php echo $row['profilepososto_user'];?><br><?php echo $row['profilepososto_job'];?></td>
    <td class="p-0"><?php echo getUserPhoto($row['id'],$row['gks_wsl_current_user_image'],64);?></td>
    
    <td class="mytdcm" nowrap><?php echo showDate(strtotime($row['user_registered']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td class="mytdcml"      ><?php echo $row['gks_fullname'];?></td>
    <td class="mytdcml"      ><?php echo $row['gks_nickname'];?></td>
    <td class="mytdcml" style="word-break: break-all;min-width:150px;"><?php echo $row['user_email'];?></td>
    <td class="mytdcml"      ><?php echo $row['gks_mobile'].' '.$row['phone_home'];?></td>
    <td class="mytdcm" nowrap><?php if (isset($row['user_tziros'])) echo myCurrencyFormat($row['user_tziros']) ;?></td>
    <td class="mytdcm" nowrap><?php if ($row['gks_balance']!=0) echo myCurrencyFormat($row['gks_balance']) ;?></td>
    <td class="mytdcml"      ><?php echo getUserRoleDescr($row['id']);?></td>
    <td class="mytdcml"      ><?php echo $row['fiscal_position_descr'];?></td>
    <td class="mytdcml"      ><?php echo $row['pricelist_descr'];
      if (abs($row['generic_ekprosi'])>=0.01) echo '<br>'.myNumberFormatNo0($row['generic_ekprosi'],true).'%';
      ?></td>
    <td class="mytdcml" nowrap><?php echo $row['afm'];?></td>
    <td class="mytdcml"       ><?php echo $row['title'];?></td>
    <td class="mytdcml"       ><?php echo $row['eponimia'];?></td>
    <td class="mytdcml" nowrap><?php echo $row['ma_poli'];?></td>
    <td class="mytdcml"       ><?php echo $row['ma_odos'];?></td>
    <td class="mytdcml" nowrap><?php echo $row['ma_tk'];?></td>
    <td class="mytdcml"       ><?php echo $row['nomos_descr'];?></td>
    <td class="mytdcml"       ><?php echo str_replace('Greece / Ελλάδα','Ελλάδα', $row['country_name']);?></td>
    <td class="mytdcml" nowrap><?php if (isset($row['genisi_date'])) echo date('d/m/Y',strtotime($row['genisi_date'])) ;?></td>
    <td class="mytdcml" nowrap><?php echo $row['user_login'];?></td>
    <td class="mytdcm"  nowrap><?php if (isset($row['gks_last_update'])) echo showDate(strtotime($row['gks_last_update']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>

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

<p>&nbsp;</p>





<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdonedate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdonedate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  


  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname=='fdonedate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_add' || sname=='fdonedate' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  



  
});

 
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


