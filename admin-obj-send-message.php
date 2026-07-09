<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//ginetai include allou

if (defined('SECURE')==false) die();


$temp=explode('/',trim_gks($_SERVER['SCRIPT_NAME']));
$temp_script_name=$temp[count($temp)-1];
//echo $temp_script_name;
//echo '<pre>';print_r($_SERVER);die();
$tmp_user_settings=array();

if      ($temp_script_name=='admin-orders-item.php'  and isset($gks_user_settings['gks_orders']))  $tmp_user_settings=$gks_user_settings['gks_orders'];
else if ($temp_script_name=='admin-acc-inv-item.php' and isset($gks_user_settings['gks_acc_inv'])) $tmp_user_settings=$gks_user_settings['gks_acc_inv'];
else if ($temp_script_name=='admin-acc-pay-item.php' and isset($gks_user_settings['gks_acc_pay'])) $tmp_user_settings=$gks_user_settings['gks_acc_pay'];
else if ($temp_script_name=='admin-whi-mov-item.php' and isset($gks_user_settings['gks_whi_mov'])) $tmp_user_settings=$gks_user_settings['gks_whi_mov'];
else if ($temp_script_name=='admin-hotel-reservation-item.php' and isset($gks_user_settings['gks_hotel_reservation'])) $tmp_user_settings=$gks_user_settings['gks_hotel_reservation'];
else if ($temp_script_name=='admin-transfer-reservation-item.php' and isset($gks_user_settings['gks_transfer_reservation'])) $tmp_user_settings=$gks_user_settings['gks_transfer_reservation'];
else if ($temp_script_name=='admin-crm-task-item.php' and isset($gks_user_settings['gks_crm_tasks'])) $tmp_user_settings=$gks_user_settings['gks_crm_tasks'];
else if ($temp_script_name=='admin-crm-lead-item.php' and isset($gks_user_settings['gks_crm_leads'])) $tmp_user_settings=$gks_user_settings['gks_crm_leads'];
else if ($temp_script_name=='admin-crm-machine-item.php' and isset($gks_user_settings['gks_crm_machine'])) $tmp_user_settings=$gks_user_settings['gks_crm_machine'];
else if ($temp_script_name=='admin-users-item.php' and isset($gks_user_settings['wp_users'])) $tmp_user_settings=$gks_user_settings['wp_users'];
else if ($temp_script_name=='admin-ct-item.php' and 
         isset($custom_table_name) and 
         isset($gks_user_settings[$custom_table_name])) {
   $tmp_user_settings=$gks_user_settings[$custom_table_name];
}

$id_email_template_object=0;
if      ($temp_script_name=='admin-orders-item.php') $id_email_template_object=1;
else if ($temp_script_name=='admin-acc-inv-item.php') $id_email_template_object=2;
else if ($temp_script_name=='admin-acc-pay-item.php') $id_email_template_object=3;
else if ($temp_script_name=='admin-whi-mov-item.php') $id_email_template_object=4;
else if ($temp_script_name=='admin-hotel-reservation-item.php') $id_email_template_object=5;
else if ($temp_script_name=='admin-transfer-reservation-item.php') $id_email_template_object=6;
else if ($temp_script_name=='admin-crm-task-item.php') $id_email_template_object=7;
else if ($temp_script_name=='admin-crm-lead-item.php') $id_email_template_object=8;
else if ($temp_script_name=='admin-crm-machine-item.php') $id_email_template_object=9;
else if ($temp_script_name=='admin-users-item.php') $id_email_template_object=10;
else if ($temp_script_name=='admin-ct-item.php') {
  $id_email_template_object=$ctid;
}


//print '<pre>';print_r($tmp_user_settings);print '</pre>';
?>
<div id="dialog_item_message" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid">
    <div class="form-group row">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Νέο μήνυμα');?></div>
    </div>
    <div class="form-group row">
      <label for="dialog_item_message_type" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-md-9 col-lg-10 gks_flock" style="padding: 0.25rem 15px;">
        <span style="white-space: nowrap;">
          <input id="dialog_item_message_type1" name="dialog_item_message_type" type="radio" value="1" <?php
          if (isset($tmp_user_settings['message_type'])==false) echo 'checked';
          if (isset($tmp_user_settings['message_type']) and $tmp_user_settings['message_type']==1) echo 'checked';?>>
          <label for="dialog_item_message_type1" style="padding-right: 20px;margin:0px;"><?php echo gks_lang('Εσωτερική σημείωση');?></label>
        </span>
        <span style="white-space: nowrap;">
          <input id="dialog_item_message_type2" name="dialog_item_message_type" type="radio" value="2" <?php
          if (isset($tmp_user_settings['message_type']) and $tmp_user_settings['message_type']==2) echo 'checked';?>>
          <label for="dialog_item_message_type2" style="padding-right: 20px;margin:0px;"><?php echo gks_lang('email');?></label>
        </span>
        <span style="white-space: nowrap;">
          <input id="dialog_item_message_type3" name="dialog_item_message_type" type="radio" value="3" <?php
          if (isset($tmp_user_settings['message_type']) and $tmp_user_settings['message_type']==3) echo 'checked';?>>
          <label for="dialog_item_message_type3" style="padding-right: 20px;margin:0px;"><?php echo gks_lang('SMS');?></label>
        </span>
        <span style="white-space: nowrap;">
          <input id="dialog_item_message_type4" name="dialog_item_message_type" type="radio" value="4" <?php
          if (isset($tmp_user_settings['message_type']) and $tmp_user_settings['message_type']==4) echo 'checked';?>>
          <label for="dialog_item_message_type4" style="padding-right: 20px;margin:0px;"><?php echo gks_lang('Viber');?></label>
        </span>
      </div>
    </div>    
    

    <div class="form-group row" id="dialog_item_message_email_from_div">
      <label for="dialog_item_message_email_from" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_email_from" type="email" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
      </div>
    </div>
    <div class="form-group row" id="dialog_item_message_email_to_div">
      <label for="dialog_item_message_email_to" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προς');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_email_to" type="email" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
      </div>
    </div>

    <div class="form-group row" id="dialog_item_message_sender_sms_div">
      <label for="dialog_item_message_sender_sms" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
      <div class="col-md-9 col-lg-10">
        <?php
        $dialog_item_message_sender_sms_def='';
        ?>
        <select id="dialog_item_message_sender_sms" class="form-control form-control-sm myneedsave" style="width:unset;">
          <?php 
          $def_sms_sender='';
          if (isset($tmp_user_settings['sms_sender'])) $def_sms_sender=trim_gks($tmp_user_settings['sms_sender']);
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
              if ($def_sms_sender == 'gks_erp_app_mobile:'.$row_select['id_erp_app_mobile']) {
                echo ' selected ';
                $dialog_item_message_sender_sms_def='gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'];
              }
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
              'data-sender="'.$value.'" ';
              if ($def_sms_sender == 'smsapi:'.$value) {
                echo ' selected ';
                $dialog_item_message_sender_sms_def='smsapi:'.$value;
              }
              echo '>smsapi: '.$value.'</option>';
            }
          }
          ?>
        </select>
        <script>
        var from_php_dialog_item_message_sender_sms_def='<?php echo $dialog_item_message_sender_sms_def;?>';
        <?php
        
        
        ?>
        </script>
      </div>
    </div>
    <div class="form-group row" id="dialog_item_message_to_sms_div">
      <label for="dialog_item_message_to_sms" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προς');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_to_sms" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" data-user_id="0">
      </div>
    </div>
    <div class="form-group row" id="dialog_item_message_to_viber_div">
      <label for="dialog_item_message_to_viber" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προς');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_to_viber" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" data-user_id="0">
      </div>
    </div>


        
    <div class="form-group row" id="dialog_item_message_email_template_div">
      <label for="dialog_item_message_email_template" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρότυπο');?>:</label>
      <div class="col-md-9 col-lg-10">
        
        <select id="dialog_item_message_email_template"  class="form-control form-control-sm" style="max-width:300px;">
        <?php
        $email_template='';
        if (isset($tmp_user_settings['email_template'])) $email_template=intval($tmp_user_settings['email_template']);

        
        $sql_emailt="SELECT gks_email_template.id_email_template AS id, gks_email_template.email_template_descr AS descr
        FROM (gks_email_template 
        LEFT JOIN (
          SELECT email_template_id
          FROM gks_email_template_object_forms
          group by email_template_id
        ) AS table_some ON gks_email_template.id_email_template = table_some.email_template_id )
        LEFT JOIN (
          SELECT email_template_id
          FROM gks_email_template_object_forms
          WHERE email_template_object_id=".$id_email_template_object."
          group by email_template_id
        ) AS table_currsel ON gks_email_template.id_email_template = table_currsel.email_template_id
        WHERE gks_email_template.is_disable=0 
        AND (table_some.email_template_id Is Null OR table_currsel.email_template_id Is Not Null)
        ORDER BY gks_email_template.sortorder";
        $result_emailt = $db_link->query($sql_emailt); 
        if (!$result_emailt) {debug_mail(false,'error sql',$sql_emailt);die('sql error');} 
        $mytemplates=array();
        while ($row_emailt = $result_emailt->fetch_assoc()) {
          $mytemplates[]=$row_emailt;
        }
            


        foreach ($mytemplates as $onlyname) { 
          echo '<option value="'.$onlyname['id'].'" '.($onlyname['id']==$email_template ? 'selected' : '').'>'.$onlyname['descr'].'</option>';
        }
        
        ?>
        </select>
      </div>
    </div>    
    
    <div class="form-group row" id="dialog_item_message_sms_template_div">
      <label for="dialog_item_message_sms_template" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρότυπο');?>:</label>
      <div class="col-md-9 col-lg-10">
        <select id="dialog_item_message_sms_template"  class="form-control form-control-sm" style="max-width:300px;">
        <option value="0" data-text=""></option>
        <?php
        $sms_template='';
        if (isset($tmp_user_settings['sms_template'])) $sms_template=intval($tmp_user_settings['sms_template']);
        $sql="SELECT gks_sms_viber_template.id_sms_viber_template AS id, 
        gks_sms_viber_template.sms_viber_template_name AS descr
        FROM (gks_sms_viber_template 
        LEFT JOIN (
          SELECT sms_viber_template_id
          FROM gks_sms_viber_template_object_forms
          group by sms_viber_template_id
        ) AS table_some ON gks_sms_viber_template.id_sms_viber_template = table_some.sms_viber_template_id )
        LEFT JOIN (
          SELECT sms_viber_template_id
          FROM gks_sms_viber_template_object_forms
          WHERE sms_viber_template_object_id=".$id_email_template_object."
          group by sms_viber_template_id
        ) AS table_currsel ON gks_sms_viber_template.id_sms_viber_template = table_currsel.sms_viber_template_id
        WHERE gks_sms_viber_template.sms_viber_template_disabled=0 
        AND (table_some.sms_viber_template_id Is Null OR table_currsel.sms_viber_template_id Is Not Null)
        AND sms_enabled<>0
        ORDER BY gks_sms_viber_template.sms_viber_template_sortorder";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="'.$row_select['id'].'" ';
          if ($sms_template == $row_select['id']) echo ' selected ';
          echo '>'.$row_select['descr'].'</option>';
        }
        ?>
        </select>
      </div>
    </div>    
    
    <div class="form-group row" id="dialog_item_message_viber_template_div">
      <label for="dialog_item_message_viber_template" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρότυπο');?>:</label>
      <div class="col-md-9 col-lg-10">
        <select id="dialog_item_message_viber_template"  class="form-control form-control-sm" style="max-width:300px;">
        <option value="0" data-text=""></option>
        <?php
        $viber_template='';
        if (isset($tmp_user_settings['viber_template'])) $viber_template=intval($tmp_user_settings['viber_template']);
        $sql="SELECT gks_sms_viber_template.id_sms_viber_template AS id, 
        gks_sms_viber_template.sms_viber_template_name AS descr
        FROM (gks_sms_viber_template 
        LEFT JOIN (
          SELECT sms_viber_template_id
          FROM gks_sms_viber_template_object_forms
          group by sms_viber_template_id
        ) AS table_some ON gks_sms_viber_template.id_sms_viber_template = table_some.sms_viber_template_id )
        LEFT JOIN (
          SELECT sms_viber_template_id
          FROM gks_sms_viber_template_object_forms
          WHERE sms_viber_template_object_id=".$id_email_template_object."
          group by sms_viber_template_id
        ) AS table_currsel ON gks_sms_viber_template.id_sms_viber_template = table_currsel.sms_viber_template_id
        WHERE gks_sms_viber_template.sms_viber_template_disabled=0 
        AND (table_some.sms_viber_template_id Is Null OR table_currsel.sms_viber_template_id Is Not Null)
        AND viber_enabled<>0
        ORDER BY gks_sms_viber_template.sms_viber_template_sortorder";
        
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="'.$row_select['id'].'" ';
          if ($viber_template == $row_select['id']) echo ' selected ';
          echo '>'.$row_select['descr'].'</option>';
        }
        ?>
        </select>
      </div>
    </div> 
        
    <div class="form-group row" id="dialog_item_message_email_subject_div">
      <label for="dialog_item_message_email_subject" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Θέμα');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_email_subject" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
      </div>
    </div>

<?php if ($temp_script_name=='admin-orders-item.php') {?>
    <div class="form-group row" id="dialog_item_message_order_online_update">
      <label for="dialog_item_message_order_online_update_add" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενημέρωση OnLine Προσφορά');?>:</label>
      <div class="col-md-9 col-lg-10">
        <input id="dialog_item_message_order_online_update_add" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1">
        <span id="dialog_item_message_online_url"></span>
        
      </div>
    </div>
<?php } ?>

<?php
    gks_plugins_functions_run('admin_obj_send_message_after_subject',array(
      'id'=>&$id,
      'temp_script_name'=>&$temp_script_name,
    ));
?>



    


        
    <div class="form-group row" id="dialog_item_message_message_plain_div">
      <label for="dialog_item_message_message_plain" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μήνυμα');?>:</label>
      <div class="col-md-9 col-lg-10">
        <textarea id="dialog_item_message_message_plain" class="form-control form-control-sm" style="min-height:100px;"></textarea>
        <small class="form-text text-muted" id="dialog_item_message_sms_chars"></small>

        <small class="form-text text-muted" id="dialog_item_message_viber_format">
        <?php echo gks_lang('Έντονη γραφή: μέσα σε');?> <b><span class="gks_viber_sc">*</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">*</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">*</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <b><?php echo gks_lang('φίλε');?></b> gks<br>
        <?php echo gks_lang('Πλάγια γραφή: μέσα σε');?> <b><span class="gks_viber_sc">_</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">_</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">_</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <i><?php echo gks_lang('φίλε');?></i> gks<br>
        <?php echo gks_lang('Διακριτή γραφή: μέσα σε');?> <b><span class="gks_viber_sc">~</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">~</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">~</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <span style="text-decoration: line-through;"><?php echo gks_lang('φίλε');?></span> gks<br>
        <?php echo gks_lang('Monospace: μέσα σε');?> <b><span class="gks_viber_sc">```</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">```</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">```</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <span style="font-family: monospace;"><?php echo gks_lang('φίλε');?></span> gks<br>
        </small>
        
      </div>
    </div>
    <div class="form-group row" id="dialog_item_message_message_mc_div">
      <label for="dialog_item_message_message_mc" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μήνυμα');?>:</label>
      <div class="col-md-9 col-lg-10">
        <textarea id="dialog_item_message_message_mc" class="form-control form-control-sm" style="min-height:100px;"></textarea>
      </div>
    </div>

    <div class="form-group row" id="dialog_item_message_email_params_div">
      <label for="dialog_item_message_email_params" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παράμετροι');?>:</label>
      <div class="col-md-9 col-lg-10">
        <div id="dialog_item_message_email_params" style="font-size:0.875rem;"></div>
      </div>
    </div>

    <div class="form-group row" id="dialog_item_message_sms_params_div">
      <label for="dialog_item_message_sms_params" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παράμετροι');?>:</label>
      <div class="col-md-9 col-lg-10">
        <div id="dialog_item_message_sms_params" style="font-size:0.875rem;"></div>
      </div>
    </div>
    
    <div class="form-group row" id="dialog_item_message_email_attachments_div">
      <label for="dialog_item_message_email_attachments" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνημμένα');?>:</label>
      <div class="col-md-9 col-lg-10 gks_flock" style="padding: 0.25rem 15px;">
        <button type="button" class="btn btn-sm btn-primary" id="dialog_item_message_email_attachments_show_all"><?php echo gks_lang('Εμφάνιση όλων');?></button>
        <div id="dialog_item_message_email_attachments" style="font-size:0.875rem;"></div>
      </div>
    </div>
    <div class="form-group row" id="dialog_item_message_email_ispreview_div">
      <label for="dialog_item_message_email_ispreview" class="col-md-3 col-lg-2 col-form-label form-control-sm text-md-right"></label>
      <div class="col-md-9 col-lg-10 gks_flock" style="padding: 0.25rem 15px;">
        <button class="btn btn-primary" id="dialog_item_message_email_ispreview"><?php echo gks_lang('Προεπισκόπηση');?></button>
      </div>
    </div>



  </div>
</div>


<div id="dialog_item_message_email_preview" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid">
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Προεπισκόπηση');?></div>
    </div>

    <div class="form-group row">
      <label class="col-sm-12 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Θέμα');?>:</label>
      <div class="col-sm-12">
        <span class="form-control-sm" id="dialog_item_message_email_preview_subject_text" style="font-weight:bold;"></span>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-12 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Κείμενο');?>:</label>
      <div class="col-sm-12" id="dialog_item_message_email_preview_preview"></div>
    </div>
  </div>
</div>
