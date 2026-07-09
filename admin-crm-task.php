<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Εργασίες');
$nav_active_array=array('crm','crm_tasks');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_mono_dika_mou=gks_permission_user_int_cond($my_wp_user_id,'gks_crm_tasks','01');
//echo $mono_dika_mou;die();

include_once('admin-crm-tasks_filters.php');

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

$data=array();
$id_crm_task_ids=array();
while ($row = $result->fetch_assoc()) {
  $row['machine']=array();
  $row['employee']=array();
  $data[$row['id_crm_task']]=$row;
  $id_crm_task_ids[]=$row['id_crm_task'];
}
if (count($id_crm_task_ids)>0) {
  $sql="SELECT gks_crm_tasks_employee.crm_task_id, gks_crm_tasks_employee.crm_task_employee_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_crm_tasks_employee 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_crm_tasks_employee.crm_task_id in (".implode(',',$id_crm_task_ids).")
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  while ($row = $result->fetch_assoc()) {
    $data[$row['crm_task_id']]['employee'][]='<a href="admin-users-item.php?id='.$row['crm_task_employee_id'].'">'.$row['gks_nickname'].'</a>';
  }
  if ($GKS_CRM_MACHINE_ENABLE) {
    $sql="SELECT gks_crm_tasks_machine.crm_task_id, gks_crm_tasks_machine.crm_task_machine_id, gks_crm_machine.crm_machine_name,gks_crm_machine.crm_machine_serial_number
    FROM gks_crm_tasks_machine
    LEFT JOIN gks_crm_machine ON gks_crm_machine.id_crm_machine = gks_crm_tasks_machine.crm_task_machine_id
    WHERE gks_crm_tasks_machine.crm_task_id in (".implode(',',$id_crm_task_ids).")
    ORDER BY gks_crm_machine.crm_machine_name;";
    $result = $db_link->query($sql);        
    if (!$result) debug_mail(false,'error sql',$sql);
    if (!$result) die('sql error');
    while ($row = $result->fetch_assoc()) {
      $data[$row['crm_task_id']]['machine'][]='<a href="admin-crm-machine-item.php?id='.$row['crm_task_machine_id'].'">'.$row['crm_machine_name'].'</a>';
    }
  }
  


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
      <a class="btn btn-primary gks_add_new_record" href="admin-crm-task-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας εργασίας');?></a>
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

<?php 
if ($GKS_CRM_MACHINE_ENABLE) {
  $tr_width1='10%';
} else {
  $tr_width1='11%';
}?>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate', gks_lang('Ημερομηνία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosubject', gks_lang('Εργασία')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soesoda', '<span class="tooltipster" title="'.gks_lang('Αναμενόμενα έσοδα').'">'.gks_lang('Α.Έσοδα').'</span>'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soplanned', gks_lang('Προγραμματισμός')); ?></th>
<?php if ($GKS_CRM_MACHINE_ENABLE) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo gks_lang('Συσκευές');?></th>
<?php } ?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo gks_lang('Υπάλληλοι');?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Επαφή')); ?></th>        


    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somobile', gks_lang('Κινητό')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', gks_lang('email')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Στίγμα');?></th>        

    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soassigned', gks_lang('Ανάθεση')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo $tr_width1;?>"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany_sub', gks_lang('Υποκατάστημα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochannel', gks_lang('Κανάλι')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochcontact', '<span class="tooltipster" title="'.gks_lang('Επαφή Πωλήσεων').'">'.gks_lang('Επαφή Π').'</span>'); ?></th>                
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socampain', gks_lang('Καμπάνια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socrmcode', '<span class="tooltipster" title="'.gks_lang('Κωδικός CRM').'">'.gks_lang('Κωδικός').'</span>'); ?></th>        

<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>

  </tr>
</thead>
<tbody>


    <?php
    $i = 0;
    //while ($row = $result->fetch_assoc()) {
    foreach ($data as $row) {
       
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> crm_task_tr" data-id="<?php echo $row['id_crm_task'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-crm-task-item.php?id=<?php echo $row['id_crm_task'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_crm_task'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-deleteafter="gks_fnc_crm_task_delete_after|<?php echo $row['id_crm_task'];?>" data-id="<?php echo $row['id_crm_task'];?>" data-model="gks_crm_tasks"></i></td>
        </tr>      
      </table>
    </td>

    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['task_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><span class="task_status_<?php echo $row['task_status_id'];?>"><?php if (isset($tasks_status[$row['task_status_id']])) echo $tasks_status[$row['task_status_id']]['task_status_descr'];?></span></td>

    <td class="mytdcml" <?php if (trim_gks($row['task_color'])!='') echo 'style="background-color:'.$row['task_color'].'"';?>><?php echo $row['subject'];?></td>
    <td class="mytdcmr"><?php if ($row['esoda']!=0) echo myCurrencyFormat($row['esoda']);?></td>
    <td nowrap class="mytdcm"><?php 
      if (isset($row['task_planned_date_from'])) echo showDate(strtotime($row['task_planned_date_from']), 'd/m/Y H:i', 1);
      echo '<br>';
      if (isset($row['task_planned_date_to'])) echo showDate(strtotime($row['task_planned_date_to']), 'd/m/Y H:i', 1);
    ?></td>
<?php if ($GKS_CRM_MACHINE_ENABLE) {?>
    <td class="mytdcml"><?php
      echo implode('<br>',$row['machine']);
    ?></td>
<?php }?>
    <td class="mytdcml"><?php
      echo implode('<br>',$row['employee']);
    ?></td>
    <td class="mytdcml"><?php 
    $taskuname=trim_gks(trim_gks($row['last_name']).' '.trim_gks($row['first_name']));
    if ($row['user_id']>0) {
      echo '<a href="admin-users-item.php?id='.$row['user_id'].'">'.($taskuname!='' ? $taskuname : $row['gks_nickname']).'</a>';
    } else {
      echo $taskuname;
    }?></td>
    
    
    <td class="mytdcml"><?php echo $row['mobile'];?></td>
    <td class="mytdcml"><?php echo $row['phone'];?></td>
    <td class="mytdcml"><?php echo $row['email'];?></td>
    <td class="mytdcml"><?php echo $row['poli'];?></td>
    <td class="mytdcm"><?php if ($row['map_latitude']==0 and $row['map_longitude']==0) {
        $pos_task=0;
      } else {
        $pos_task=1;
      }?>
      <img src="img/<?php echo $pos_task;?>.png" border="0" width="16"></td>
    </td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['assigned_id'];?>"><?php echo $row['gks_nickname_assigned'];?></a></td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row['company_id'];?>"><?php echo $row['company_title'];?></a></td>
    <td class="mytdcml"><a href="admin-company-sub-item.php?id=<?php echo $row['company_sub_id'];?>"><?php echo $row['company_sub_title'];?></a></td>
    <td class="mytdcml"><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['crm_channel_id'];?>"><?php echo $row['crm_channel_sale_descr'];?></a></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['crm_channel_contact_id'];?>"><?php echo $row['crm_channel_contact_gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-ads-campain-item.php?id=<?php echo $row['crm_channel_campain_id'];?>"><?php echo $row['ads_campain_name'];?></a></td>
    <td class="mytdcm"><?php echo $row['crm_channel_code'];?></a></td>
    
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#ftask_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#ftask_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fplanned-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fplanned-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));




  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'ftask_date' || sname=='fplanned' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'ftask_date' || sname=='fplanned' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });

  window.gks_fnc_crm_task_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.crm_task_tr[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
    });
  }  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

