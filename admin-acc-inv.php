<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Παραστατικά');
$nav_active_array=array('accounting','accounting_inv');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_acc_inv_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0);
$perm_gks_acc_inv_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','edit',0);
$perm_gks_acc_inv_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0);
$perm_gks_acc_inv_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','delete',0);


//gks_whi_mov_lots_serials_balance_calc([12117,12293],'2022-02-28 00:00:00'); //die();
//$ff=gks_whi_mov_all_products_for_balance_extend([12110,12293]); //die();
//print '<pre>';print_r($ff);die();








include_once('admin-acc-inv_filters.php');

if (isset($_GET['mass_message']) and intval($_GET['mass_message'])==1) {
  $result = $db_link->query($sql); 
  if (!$result) debug_mail(false,'error sql mm',$sql);
  if (!$result) die('sql error mm');
  
  $ids=[];
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['user_id'],$ids)==false) {
      $ids[]=$row['user_id']; 
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
<link href="css/admin-acc-inv.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
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
      <a class="btn btn-primary gks_add_new_record" href="admin-acc-inv-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου παραστατικού');?></a>
      <?php
      $sql_export_excel="SELECT id_export_excel, export_excel_descr FROM gks_export_excel WHERE export_excel_object='gks_acc_inv' and export_excel_disable=0 ORDER BY export_excel_descr;";
      $result_export_excel = $db_link->query($sql_export_excel);        
      if (!$result_export_excel) {debug_mail(false,'error sql',$sql_export_excel); die('sql error');}
      $export_excel=array();
      while ($row_export_excel = $result_export_excel->fetch_assoc()) {
        $export_excel[]=$row_export_excel;
      }
      if (count($export_excel)>0) { ?>
      <div class="btn-group" >
        <button type="button" class="btn btn-primary dropdown-toggle" style="justify-content: center!important;" 
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo gks_lang('Εξαγωγή σε Excel');?></button>
        <div class="dropdown-menu" style="background-color: #fafcff;box-shadow: 0px 4px 24px 12px rgb(0 0 0 / 20%), 0px 6px 20px 0px rgb(0 0 0 / 19%);">
          <?php
          foreach ($export_excel as $itemee) {
            echo '<a class="dropdown-item" href="admin-acc-inv-export-excel.php?'.$_SERVER['QUERY_STRING'].'&id_export_excel='.$itemee['id_export_excel'].'" download>'.$itemee['export_excel_descr'].'</a>';
          }?>
        </div>  
      </div> 
      <?php } ?>
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
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostate', gks_lang('Κατάσταση')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sood', gks_lang('Ημερομηνία')); ?></th>        
<?php if (count($user_companys)>1) {?>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
<?php }?>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sojournal', gks_lang('Ημερολόγιο')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseira', gks_lang('Σειρά')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber', gks_lang('Αριθμός')); ?></th>        


    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprint_date', gks_lang('Εκτύπωση')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soaade_send_date', gks_lang('ΑΑΔΕ')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Έπαφή')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soposotita', gks_lang('Ποσότητα')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Τιμή')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonetfpa', gks_lang('Μικτό')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowithheld', '<span class="tooltipster" title="'.gks_lang('Φόροι Παρακρατούμενοι').'">'.gks_lang('Παρακρ.').'</span>'); ?></th>        
    
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobalance', gks_lang('Τιμή για<br>υπόλοιπο')); ?></th>        

    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodispatch_date', gks_lang('Αποστολή')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sode', gks_lang('Τρόπος<br>Αποστολής')); ?></th>  

    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopa', gks_lang('Τρόπος<br>Πληρωμής')); ?></th>  
         
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="50%" ><?php echo gks_lang('Ιδιότητες');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="50%" ><?php echo gks_lang('Σχόλια');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soassigned', gks_lang('Ανάθεση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somerchantref', gks_lang('Αναφορά')); ?></th>    
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopos', gks_lang('Εντατική Λιανική')); ?></th>    
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soappmobile', gks_lang('App Mobile')); ?></th>    
        
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

     
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-acc-inv-item.php?id=<?php echo $row['id_acc_inv'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_acc_inv'];?></td>
          <?php if ($perm_gks_acc_inv_delete) {?>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_acc_inv'];?>" data-model="gks_acc_inv"></i></td>
          <?php } ?>
        </tr>      
      </table>
         
      <?php if (!empty($row['acc_inv_ref_number'])) {?>
      <table cellpadding=0 cellspacing=0 class="ref_number_table">
        <tr class="ref_number_table_tr">
          <td class="ref_number_table_td">
            <span class="ref_number_table_td_span"><?php echo $row['acc_inv_ref_number']?></span>
          </td>
        </tr>  
      </table>
      <?php } ?>
     
     
    </td>
    

    <td nowrap class="mytdcm"><span class="acc_inv_state_<?php echo $row['inv_state'];?>"><?php echo getAccInvStateDescr($row['inv_state']);?></span></td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
<?php if (count($user_companys)>1) {?>
    <td        class="mytdcm"><?php echo $row['company_title']; if (isset($row['company_sub_title'])) echo '<br>'.$row['company_sub_title'];?></td> 
<?php } ?>

    <td        class="mytdcm"><?php echo $row['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['inv_acc_number_int']<>0) echo $row['inv_acc_number_int'];?></td>


    <td nowrap class="mytdcm"><?php if (isset($row['print_date'])) echo showDate(strtotime($row['print_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php if (isset($row['aade_send_date'])) echo showDate(strtotime($row['aade_send_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td        class="mytdcm"><?php if ($row['user_id']==0) echo $row['user_email']; echo '<a href="admin-users-item.php?id='.$row['user_id'].'">'.$row['gks_nickname'].'</a>';?></td>
    <td        class="mytdcm"><?php echo $row['ma_poli'];?></td>


    <td nowrap class="mytdcm"><?php if ($row['products_posotita']!=0) echo $row['products_posotita'];?></td>
    
    <td nowrap class="mytdcm" ><?php 
      if ($row['gks_price_net']!=0) echo '<b>'.myCurrencyFormat($row['gks_price_net']).'</b>';
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row['gks_price_netfpa']!=0) echo myCurrencyFormat($row['gks_price_netfpa']);
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row['totalWithheldAmount']!=0) echo myCurrencyFormat($row['totalWithheldAmount']);
    ?></td>
    
    
    <td nowrap class="mytdcm" ><?php 
      if ($row['affect_balance_calc']!=0) echo myCurrencyFormat($row['affect_balance_calc']);
    ?></td>
    

    <td nowrap class="mytdcm"><?php 
      if (isset($row['dispatch_date'])) echo showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0);
      if (isset($row['dispatch_time'])) echo ' '.showDate(strtotime($row['dispatch_time']), 'H:i', 0);
      
    ?></td>   
    <td        class="mytdcm"><?php echo $row['delivery_method_name'];?></td>
    <td        class="mytdcm"><?php 
      $payment_acquirer_name=$row['payment_acquirer_name'];
      if (trim_gks($row['tropos_pliromis_via'])!='') {
        $payment_acquirer_name=$row['tropos_pliromis_via'].' via '.$payment_acquirer_name;
      }
      echo $payment_acquirer_name;
    ?></td>
      

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp = trim_gks($row['idiotites']);
      if ($temp!='') {
        $myarray = json_decode($temp, true);
        $temp='';
        foreach ($myarray as $value) {
          $temp.=$value[0].': <b>'.$value[1].'</b><br>';
        } 
        if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
        echo $temp;
      }
    
    ?></div></div></td>  
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp='';
      if (!empty($row['notes'])) $temp.=gks_lang('Πελάτης').': <b>'.nl2br_gks($row['notes']).'</b><br>';
      if (!empty($row['subnotes'])) $temp.=gks_lang('Πελάτης (Συν)').': <b>'.nl2br_gks($row['subnotes']).'</b><br>';
      if (!empty($row['note_production'])) $temp.=gks_lang('Παραγωγή').': <b>'.nl2br_gks($row['note_production']).'</b><br>';
      if (!empty($row['note_logistirio'])) $temp.=gks_lang('Λογιστήριο').': <b>'.nl2br_gks($row['note_logistirio']).'</b><br>';
      
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
      echo $temp;
    ?></div></div></td>   

    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['assigned_id'];?>"><?php echo $row['gks_nickname_assigned'];?></a></td>
    <td class="mytdcml"><?php echo $row['merchant_ref_trns'];?></td>
    <td class="mytdcml"><a href="admin-pos-item.php?id=<?php echo $row['pos_id'];?>"><?php echo $row['pos_name'];?></a></td>
    <td class="mytdcml"><a href="admin-erp-app-mobile-item.php?id=<?php echo $row['erp_app_mobile_id'];?>"><?php echo $row['erp_app_mobile_name'];?></a></td>
    
<?php if ($GKS_CRM_ENABLE) {?>    
    <td class="mytdcml"><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['crm_channel_id'];?>"><?php echo $row['crm_channel_sale_descr'];?></a></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['crm_channel_contact_id'];?>"><?php echo $row['crm_channel_contact_gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-ads-campain-item.php?id=<?php echo $row['crm_channel_campain_id'];?>"><?php echo $row['ads_campain_name'];?></a></td>
    <td class="mytdcm"><?php echo $row['crm_channel_code'];?></a></td>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fprint_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fprint_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#faade_send_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#faade_send_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fdispatch_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdispatch_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname=='fprint_date' || sname=='faade_send_date' || sname=='fdispatch_date' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdate_add' || sname=='fprint_date' || sname=='faade_send_date' || sname=='fdispatch_date' || gks_custom_filters_date_elems.includes(sname)) {
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


