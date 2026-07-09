<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Εντατική Λιανική');
$nav_active_array=array('accounting','accounting_pos');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_pos_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos','01');

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_pos_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','view',0);
$perm_gks_pos_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','edit',0);
$perm_gks_pos_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','add',0);
$perm_gks_pos_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','delete',0);





$user_companys=gks_get_companys_list();

include_once('admin-pos_filters.php');

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
<link href="css/admin-pos.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
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
      <a class="btn btn-primary gks_add_new_record" href="admin-pos-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου σημείου Εντατικής Λιανικής');?></a>
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
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όνομα')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th> 

    
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sojournal', gks_lang('Ημερολόγιο')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseira', gks_lang('Σειρά')); ?></th>        


    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Έπαφή')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somaxa', gks_lang('Μέγ. Ποσό')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sode', gks_lang('Τρόπος<br>Αποστολής')); ?></th>  

    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopa', gks_lang('Τρόπος<br>Πληρωμής')); ?></th>  
         
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soassigned', gks_lang('Ανάθεση')); ?></th>        
<?php if ($GKS_CRM_ENABLE) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochannel', gks_lang('Κανάλι')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochcontact', '<span class="tooltipster" title="'.gks_lang('Επαφή Πωλήσεων').'">'.gks_lang('Επαφή Π').'</span>'); ?></th>                
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socampain', gks_lang('Καμπάνια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socrmcode', '<span class="tooltipster" title="'.gks_lang('Κωδικός CRM').'">'.gks_lang('Κωδικός').'</span>'); ?></th>        
<?php } ?>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souedit', gks_lang('Χρήστης')); ?></th>    
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
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_pos'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-pos-item.php?id=<?php echo $row['id_pos'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_pos_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_pos'];?>" data-model="gks_pos"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td        class="mytdcml"><?php echo $row['pos_name'];?></td>
    <td        class="mytdcml"><?php echo $row['pos_descr'];?></td>
    

    <td        class="mytdcm"><?php echo $row['company_title']; if (isset($row['company_sub_title'])) echo '<br>'.$row['company_sub_title'];?></td> 

    <td        class="mytdcm"><?php echo $row['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>


    <td        class="mytdcm"><?php if ($row['def_user_id']==0) echo $row['user_email']; echo '<a href="admin-users-item.php?id='.$row['def_user_id'].'">'.$row['gks_nickname'].'</a>';?></td>


    
    <td nowrap class="mytdcm" ><?php 
      if ($row['pos_max_ammount']!=0) echo '<b>'.myCurrencyFormat($row['pos_max_ammount']).'</b>';
    ?></td>
    
    <td        class="mytdcm"><?php echo $row['delivery_method_name'];?></a></td>
    <td        class="mytdcm"><?php echo $row['payment_acquirer_name'];?></a></td>
      

    

    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['def_assigned_id'];?>"><?php echo $row['gks_nickname_assigned'];?></a></td>
<?php if ($GKS_CRM_ENABLE) {?>    
    <td class="mytdcml"><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['def_crm_channel_id'];?>"><?php echo $row['crm_channel_sale_descr'];?></a></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['def_crm_channel_contact_id'];?>"><?php echo $row['crm_channel_contact_gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-ads-campain-item.php?id=<?php echo $row['def_crm_channel_campain_id'];?>"><?php echo $row['ads_campain_name'];?></a></td>
    <td class="mytdcm"><?php echo $row['def_crm_channel_code'];?></a></td>
<?php } ?>
    
    <td class="mytdcm gks_td08"><a href="admin-users-item.php?id=<?php echo $row['user_id_edit'];?>"><?php echo $row['gks_nickname_edit'];?></a></td>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  //$('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  //$('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  //$('#fprint_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  //$('#fprint_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  //$('#faade_send_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  //$('#faade_send_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


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
  
  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


