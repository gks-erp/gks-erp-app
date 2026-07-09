<!-- 
Κώστας Γουτούδης
gks ERP
www.gks.gr
-->


<!-- _dialogs -->
<div id="dialog_email" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display:none">
  <div id="dialog_email_headers">
    
  </div>
</div>
<div id="dialog_sms" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display:none">
  <div id="dialog_sms_headers">
    
  </div>
</div>
<div id="dialog_message" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <table style="width:100%" cellpadding="10">
    <tr>
      <td style="width:1%;vertical-align:top">
        <i id="dialog_message_ok"      class="fas fa-check-circle"         style="color:#00e220;font-size: 500%;"></i>
        <i id="dialog_message_error"   class="fas fa-exclamation-triangle" style="color:#cb0000;font-size: 500%;"></i>
        <i id="dialog_message_info"    class="fas fa-info-circle"          style="color:#007bff;font-size: 500%;"></i>
        <i id="dialog_message_warning" class="fas fa-exclamation-circle" style="color:#0033cc;font-size: 500%;"></i>
      </td>
      <td style="width:99%;vertical-align:top;padding-top:20px;">
        <span id="dialog_message_message" style="font-size:100%;"></span>
      </td>
    </tr> 
  </table>
</div>  
<div id="dialog_big_message" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <span id="dialog_big_message_message"></span>
</div>
<div id="dialog_confirm" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <table style="width:100%" cellpadding="10">
    <tr>
      <td style="width:1%;vertical-align:top">
        <i class="fas fa-question-circle" style = "color: #dca327;font-size: 500%;"></i>
        
      </td>
      <td style="width:99%;vertical-align:top;padding-top:20px;">
        <span id="dialog_confirm_message" style="font-size:100%;"></span>
      </td>
    </tr> 
  </table>  
</div>

<div id="dialog_notification" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div style="text-align:center;font-weight: bold;font-size: 130%;margin-bottom: 6px;"><?php echo gks_lang('Αποστολή Ειδοποίησης');?></div>
  <div id="dialog_notification_options1">
    <span id=dialog_notification_options1_span"><?php echo gks_lang('Προς');?>: </span>
    <input type="radio" class="col-form-label form-control-sm" name="dialog_notification_to" id="dialog_notification_to_me"    value="0"        >
      <label for="dialog_notification_to_me"><?php echo gks_lang('Εμένα');?></label>
    <input type="radio" class="col-form-label form-control-sm" name="dialog_notification_to" id="dialog_notification_to_other" value="1" checked>
      <label for="dialog_notification_to_other"><?php echo gks_lang('Άλλον');?></label>
    <input type="text"  class="form-control form-control-sm" id="dialog_notification_user" data-id="0" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"> 
  </div>
  <div style="margin-bottom: 6px;">
    <textarea type="text" class="form-control form-control-sm" id="dialog_notification_message" style="height:100px;" placeholder="<?php echo gks_lang('Γράψτε εδώ το μήνυμά σας');?>"></textarea>
  </div>
  <div>
    <input type="checkbox" class="form-control form-control-sm switchery1_sel" id="dialog_notification_currlink" value="1" checked>
    <label style="display: unset;" for="dialog_notification_currlink"><?php echo gks_lang('Να συμπεριληφθεί και ο σύνδεσμος της τρέχουσας σελίδας');?></label>
  </div>
</div>

<div id="dialog_activity" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <h5 align="center" style="padding-top:0px;"><?php echo gks_lang('Δραστηριότητα');?></h5>
  <div class="container-fluid" id="dialog_activity_area">
    <div class="row">
      <div class="col-md-12">

        <div class="form-group row">
          <label for="dialog_activity_user_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
          <div class="col-md-8" style="padding-top: 3px;">
            <span data-id="050new"    class="activity_status_this activity_status_050new"   ><?php echo gks_lang('Νέα');?></span>
            <span data-id="100done"   class="activity_status_this activity_status_100done"  ><?php echo gks_lang('Έγινε');?></span>
            <span data-id="200cancel" class="activity_status_this activity_status_200cancel"><?php echo gks_lang('Άκυρο');?></span>
          </div>
        </div>


        <div class="form-group row">
          <label for="dialog_activity_user_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
          <div class="col-md-8">
            <input id="dialog_activity_user_id" data-id="0" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
          </div>
        </div>
        
        
        
        <div class="form-group row">
          <label for="dialog_activity_type_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
          <div class="col-md-8">
            <select id="dialog_activity_type_id"  class="form-control form-control-sm"  >
            <option value="0"></option>
            <?php
            $sql="SELECT * FROM gks_crm_activity_types where crm_activity_type_disabled =0 ORDER BY crm_activity_type_sortorder";
            $result_select = $db_link->query($sql);        
            if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
            while ($row_select = $result_select->fetch_assoc()) {
              echo '<option value="'.$row_select['id_crm_activity_type'].'" ';
              echo '>'.$row_select['crm_activity_type_descr'].'</option>';
            }?></select>
          </div>
        </div>
        
        
        
        <div class="form-group row">
          <label for="dialog_activity_notification" class="col-md-4 col-form-label form-control-sm text-md-right" id="dialog_activity_notification_label"><?php echo gks_lang('Ειδοποιήση');?>:</label>
          <div class="col-md-8">
            <input id="dialog_activity_notification" type="checkbox" value="1" class="form-control form-control-sm">
          </div>
        </div>
        <div class="form-group row">
          <label for="dialog_activity_duedate" class="col-md-4 col-form-label form-control-sm text-md-right" id="dialog_activity_duedate_label"><?php echo gks_lang('Έως πότε');?>:</label>
          <div class="col-md-8">
            <input id="dialog_activity_duedate" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
          </div>
        </div>
        <div class="form-group row" id="dialog_activity_diarkeia_div">
          <label for="dialog_activity_diarkeia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διάρκεια');?>:</label>
          <div class="col-md-8">
            <select id="dialog_activity_diarkeia"  class="form-control form-control-sm" style="width: unset;">
            <option value="15">0:15</option>
            <option value="30">0:30</option>
            <option value="45">0:45</option>
            <option value="60">1:00</option>
            <option value="90">1:30</option>
            <option value="120">2:00</option>
            <option value="150">2:30</option>
            <option value="180">3:00</option>
            <option value="210">3:30</option>
            <option value="240">4:00</option>
            <option value="270">4:30</option>
            <option value="300">5:00</option>
            <option value="330">5:30</option>
            <option value="360">6:00</option>
            <option value="390">6:30</option>
            <option value="420">7:00</option>
            <option value="450">7:30</option>
            <option value="480">8:00</option>
            </select>
          </div>
        </div>
        
        
        <div class="form-group row">
          <label for="dialog_activity_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
          <div class="col-md-8">
            <input id="dialog_activity_color" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px;">
          </div>
        </div>
        <div class="form-group row">
          <label for="dialog_activity_subject" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Θέμα');?>:</label>
          <div class="col-md-8">
            <input id="dialog_activity_subject" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
          </div>
        </div>
        <div class="form-group row" style="margin-bottom:0px;">
          <label for="dialog_activity_message" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
          <div class="col-md-8">
            <textarea id="dialog_activity_message" type="text" class="form-control form-control-sm" style="min-height:100px;height:100px;" ></textarea>
          </div>
        </div>
          

       
      </div>
    </div>
  </div>
</div>

<div id="dialog_object_rel" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <h5 align="center" style="padding-top:0px;"><?php echo gks_lang('Σύνδεση σχετικού αντικειμένου');?></h5>
  <div class="container-fluid" id="dialog_object_rel_area">
    
    <div class="form-group  row">
      <label for="dialog_object_rel_obj" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αντικείμενο');?>:</label>
      <div class="col-sm-8">
        <select name="dialog_object_rel_obj" id="dialog_object_rel_obj" class="form-control form-control-sm">
          <optgroup label="<?php echo gks_lang('Διαχείριση');?>">
            <option value="wp_users"><?php echo gks_lang('Επαφή');?></option>  
            <option value="gks_users_groups"><?php echo gks_lang('Ομάδα Επαφών');?></option>
            <option value="gks_eshop_products"><?php echo gks_lang('Είδος');?></option>
            <option value="gks_eshop_products_categories"><?php echo gks_lang('Κατηγορία Είδους');?></option>
            <option value="gks_eshop_products_brands"><?php echo gks_lang('Μάρκα');?></option>
            
            <option value="gks_eshop_pricelist"><?php echo gks_lang('Τιμοκατάλογος');?></option>
            <option value="gks_eshop_pricelist_items"><?php echo gks_lang('Στοιχείο Τιμοκαταλόγου-Κουπόνι');?></option>
            <option value="gks_lang"><?php echo gks_lang('Γλώσσα');?></option>
            
            
            
            <option value="gks_bank_accounts"><?php echo gks_lang('Τραπεζικός λογαριασμός');?></option>
            <option value="gks_sociallinks_type"><?php echo gks_lang('Τύπος Συνδέσμων Κοινωνικών Δικτύων');?></option>
            
            <option value="gks_company"><?php echo gks_lang('Εταιρεία');?></option>
            <option value="gks_company_subs"><?php echo gks_lang('Υποκατάστημα');?></option>
            <option value="gks_warehouses"><?php echo gks_lang('Αποθήκη');?></option>
            
            <option value="gks_print_forms"><?php echo gks_lang('Φόρμα Εκτύπωσης');?></option>
            <option value="gks_template_html"><?php echo gks_lang('Πρότυπο HTML');?></option>
            <option value="gks_eshops"><?php echo gks_lang('eshop');?></option>
            <option value="gks_custom_table"><?php echo gks_lang('Προσαρμογή');?></option>
          </optgroup>
          
<?php if ($GKS_HOTEL_BACKEND) {?>          
          <optgroup label="<?php echo gks_lang('Ξενοδοχείο');?>">
            <option value="gks_hotel_reservation"><?php echo gks_lang('Κράτηση');?></option>
            <option value="gks_hotel_availability"><?php echo gks_lang('Διαθεσιμότητα');?></option>
            <option value="gks_hotel_price"><?php echo gks_lang('Τιμή δωματίου');?></option>
            <option value="gks_hotel_room_type"><?php echo gks_lang('Τύπος δωματίου');?></option>
            <option value="gks_hotel_room"><?php echo gks_lang('Δωμάτιο');?></option>
            <option value="gks_hotel_floor"><?php echo gks_lang('Όροφος');?></option>
            <option value="gks_hotel"><?php echo gks_lang('Ξενοδοχείο');?></option>
          </optgroup>
<?php } ?>
<?php if (GKS_TRANSFER) {?>          
          <optgroup label="<?php echo gks_lang('Transfer');?>">

            <option value="gks_transfer_reservation"><?php echo gks_lang('Κράτηση');?></option>
            <option value="gks_transfer_pricelist"><?php echo gks_lang('Καταχώρηση Τιμοκαταλόγου');?></option>
            <option value="gks_poi_diadromes"><?php echo gks_lang('Διαδρομή');?></option>
            <option value="gks_transfer_oxima_type"><?php echo gks_lang('Τύπος Οχήματος');?></option>
            <option value="gks_poi"><?php echo gks_lang('Σημείο Ενδιαφέροντος');?></option>
            <option value="gks_poi_type"><?php echo gks_lang('Τύπος Σημείων Ενδιαφέροντος');?></option>
            <option value="gks_transfer_area"><?php echo gks_lang('Περιοχή');?></option>
            <option value="gks_transfer"><?php echo gks_lang('Κανάλι Transfer');?></option>
          </optgroup>
<?php } ?>

<?php if ($GKS_CRM_ENABLE) {?>
          <optgroup label="<?php echo gks_lang('CRM');?>">
<?php if ($GKS_CRM_LEADS_ENABLE) {?>
            <option value="gks_crm_leads"><?php echo gks_lang('Ευκαιρία');?></option> 
<?php } ?>
            <option value="gks_calendar"><?php echo gks_lang('Ημερολόγιο');?></option>
<?php if ($GKS_CRM_TASKS_ENABLE) {?>
            <option value="gks_crm_tasks"><?php echo gks_lang('Εργασία');?></option>
<?php } ?>
<?php if ($GKS_CRM_MACHINE_ENABLE) {?>
            <option value="gks_crm_machine"><?php echo gks_lang('Συσκευή');?></option>
<?php } ?>
            <option value="gks_crm_channel_sale"><?php echo gks_lang('Κανάλι πωλήσεων');?></option>
<?php if ($GKS_CRM_LEADS_ENABLE) {?>
            <option value="gks_crm_leads_status"><?php echo gks_lang('Κατάσταση Ευκαιριών');?></option>
<?php } ?>
<?php if ($GKS_CRM_TASKS_ENABLE) {?>
            <option value="gks_crm_tasks_status"><?php echo gks_lang('Κατάσταση Εργασιών');?></option>
<?php } ?>
          </optgroup>
<?php } ?>
          <optgroup label="<?php echo gks_lang('Αποθήκη');?>">
            <option value="gks_eshop_product_lots"><?php echo gks_lang('Παρτίδα-Serial Number');?></option>
          </optgroup>          
<?php if ($GKS_ORDERS_ENABLE) {?>
          <optgroup label="<?php echo gks_lang('Πωλήσεις');?>">
            <option value="gks_orders"><?php echo gks_lang('Παραγγελία');?></option>
<?php if ($GKS_ORDERS_OCCASION) {?>   <option value="gks_orders_occasion"><?php echo gks_lang('Περίσταση');?></option><?php } ?>
          </optgroup>
<?php } ?>

<?php if ($GKS_ORDERS_PRODUCTION) {?>
          <optgroup label="<?php echo gks_lang('Παραγωγή');?>">
            <option value="gks_production_ergasies"><?php echo gks_lang('Εργασία');?></option>
            <option value="gks_production_posta"><?php echo gks_lang('Πόστο');?></option>
            <option value="gks_production_bom"><?php echo gks_lang('Συνταγή');?></option>
          </optgroup>
<?php } ?>

<?php if ($GKS_ACC_ENABLE) {?>          
          <optgroup label="<?php echo gks_lang('Λογιστική');?>">
            <option value="gks_acc_journal"><?php echo gks_lang('Ημερολόγιο');?></option>
            <option value="gks_acc_inv"><?php echo gks_lang('Παραστατικό');?></option>
            <option value="gks_acc_pay"><?php echo gks_lang('Πληρωμή');?></option>
            <option value="gks_acc_seires"><?php echo gks_lang('Σειρά');?></option>
          </optgroup>
<?php } ?>
<?php if ($GKS_ASSETS_ENABLE) {?>          
          <optgroup label="<?php echo gks_lang('Πάγια');?>">
            <option value="gks_assets"><?php echo gks_lang('Πάγια');?></option>
            <option value="gks_assets_moves"><?php echo gks_lang('Κινήσεις Παγίων');?></option>
            <option value="gks_assets_service"><?php echo gks_lang('Service');?></option>
            <option value="gks_assets_type"><?php echo gks_lang('Τύπος Παγίου');?></option>
            <option value="gks_assets_service_reasons"><?php echo gks_lang('Αιτία Service Παγίου');?></option>
            <option value="gks_assets_whi_mov"><?php echo gks_lang('Απογραφές');?></option>
          </optgroup>
<?php } ?>
<?php 

    $sql_dikamouobj="SELECT gks_custom_table.id_custom_table, gks_custom_table.custom_table_name, gks_custom_table.custom_table_descr, gks_custom_table.obj_url, gks_custom_table.custom_sortorder
    FROM (gks_custom_table 
    LEFT JOIN gks_permission_object ON gks_custom_table.custom_table_name = gks_permission_object.table_name) 
    LEFT JOIN gks_permission_user ON gks_permission_object.id_permission_object = gks_permission_user.permission_object_id
    WHERE gks_custom_table.custom_table_disabled=0
    AND gks_permission_user.user_id=".$my_wp_user_id."
    AND gks_permission_user.perm_view=1 
    AND gks_custom_table.id_custom_table>=10000
    GROUP BY gks_custom_table.custom_table_descr, gks_custom_table.id_custom_table, gks_custom_table.obj_url, gks_custom_table.custom_sortorder
    ORDER BY gks_custom_table.custom_sortorder;";
    $result_dikamouobj = $db_link->query($sql_dikamouobj);      
    if (!$result_dikamouobj) {
      debug_mail(false,'error sql',$sql_dikamouobj);
      die();
    }
    $option_items=array();
    while ($row_dikamouobj = $result_dikamouobj->fetch_assoc()) {
      $option_items[]='<option value="'.$row_dikamouobj['custom_table_name'].'">'.$row_dikamouobj['custom_table_descr'].'</option>';
    }
    if (count($option_items)>0) {
        echo '<optgroup label="'.gks_lang('Δικά μου αντικείμενα').'">';
        echo implode("\r\n",$option_items);
        echo '</optgroup>';
      
    }

?>


            
        </select>
       
      </div>
    </div>  
    <div class="form-group row">
      <label for="dialog_object_rel_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
      <div class="col-md-8">
        <input type="text" id="dialog_object_rel_id" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε κάτι');?> ...">
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-12" id="dialog_object_rel_list">
        
      </div>
    </div>    
  </div>
</div>

    
<!-- _dialogs -->

