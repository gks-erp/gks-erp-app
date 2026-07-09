<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Συζήτηση SMS');
$nav_active_array=array('crm','manage_sms','manage_smschat');


db_open();
stat_record();
$phone='';if (isset($_GET['phone'])) $phone=trim_gks($_GET['phone']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms_chat','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



//print '<pre>';echo count($mychats)."\n";print_r($mychats);die();


include_once('_my_header_admin.php');
?>
<link href="css/admin-sms-chat.css?v=a7<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>




<div id="gks_main_panel">
  <div id="gks_main_panel_users">
    <div id="gks_main_panel_users_title">
      <?php echo gks_lang('Συνομιλίες');?>
    </div>
    <div id="gks_main_panel_users_search">
      <input class="form-control form-control-sm" name="search_user" id="search_user" type="search" value="" placeholder="<?php echo gks_lang('Αναζήτηση');?>">
    </div>
    
    <div id="gks_main_panel_users_list">
      

    </div>   
  </div>
  <div id="gks_main_panel_chat">
    <div id="gks_main_panel_chat_title">
      <span id="count_msgs" class="tooltipster" title="<?php echo gks_lang('Πλήθος μηνυμάτων');?>"></span> 
      <?php echo gks_lang('Μηνύματα');?> 
    </div>
    <div id="gks_main_panel_chat_list">
      <div id="gks_main_panel_chat_list2">
      </div>
    </div>
    <div id="gks_main_panel_chat_new">

      <div id="gks_main_panel_chat_new2">
        <span id="sms_from_label"><?php echo gks_lang('Από');?>:</span>
        <select id="sms_from" class="form-control form-control-sm myneedsave tooltipster" title="<?php echo gks_lang('Αποστολή Από');?>">
        <?php 
        $sql="SELECT gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name, 
        gks_erp_app_mobile.erp_app_mobile_phonenumber, gks_erp_app_mobile_ping.mydate
        FROM gks_erp_app_mobile 
        LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
        WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
        and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
        ORDER BY gks_erp_app_mobile.erp_app_mobile_sortorder;";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'].'" '.
          'data-provider="gks_erp_app_mobile" '.
          'data-sender="'.$row_select['id_erp_app_mobile'].'" ';
          $is_offline='';
          if (empty($row_select['mydate'])==false and strtotime($row_select['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
            $is_offline='';
          } else {
            $is_offline='disabled';
          }            
          echo $is_offline.'>App: '.$row_select['erp_app_mobile_name'].' '.$row_select['erp_app_mobile_phonenumber'];
          if ($is_offline!='') echo ' - '.gks_lang('ανενεργό');
          echo '</option>';
        }  
        $parts=explode(',',$GKS_SMS_SENDER);
        foreach ($parts as $value) {
          $value=trim_gks($value);
          if ($value!='') {
            echo '<option value=smsapi:'.$value.' '.
            'data-provider="smsapi" '.
            'data-sender="'.$value.'" '.
            '>smsapi: '.$value.'</option>';
          }
        }
        ?>
        </select>
        
        <select id="sms_template"  class="form-control form-control-sm tooltipster" title="<?php echo gks_lang('Πρότυπο');?>" style="max-width:300px;">
        <option value="0" data-text=""></option>
        <?php
        $sms_template='';
        if (isset($tmp_user_settings['sms_template'])) $sms_template=intval($tmp_user_settings['sms_template']);
        $sql="select id_sms_viber_template,sms_viber_template_name, sms_viber_template_text FROM gks_sms_viber_template where sms_viber_template_disabled=0 and sms_enabled<>0 order by sms_viber_template_sortorder";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="'.$row_select['id_sms_viber_template'].'" '.
          'data-text="'.base64_encode($row_select['sms_viber_template_text']).'"';
          if ($sms_template == $row_select['id_sms_viber_template']) echo ' selected ';
          echo '>'.$row_select['sms_viber_template_name'].'</option>';
        }
        ?>
        </select>
      </div>

      <div id="gks_main_panel_chat_new3">
        <textarea class="form-control form-control-sm myneedsave" 
          name="new_message" id="new_message"
          style="min-height: 32px; height: 32px;"></textarea>
      </div>
      <div id="gks_main_panel_chat_new4">
        <span class="btn btn-primary btn-sm1" id="send_message"><i class="fas fa-paper-plane"></i></span>
      </div>
      
      
    </div>
 
  
  </div>
  <div id="gks_main_panel_details">
    <div id="gks_main_panel_details_title">
      <?php echo gks_lang('Λεπτομέρειες Επαφής');?>
    </div>
    <div id="gks_main_panel_details_list">
      
    </div>
  
  </div>
</div>




<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>



});
</script>

<script src="js/admin-sms-chat.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
//db_close();
include_once('_my_footer_admin.php');



