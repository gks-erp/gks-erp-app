<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Οι Ρυθμίσεις μου');
$nav_active_array=array('manage','manage_settings','manage_user_settings');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings_users','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



//print '<pre>';echo wp_enqueue_script('my-upload');die();

include_once('_my_header_admin.php');
//print '<pre>';print_r($gks_user_settings);print '</pre>';

?>
<style>
#zoom_slider_handle {
  width: 50px;
  height: 30px;
  top: 50%;
  margin-top: -15px;
  text-align: center;
  line-height: 1.6em;
  padding: 5px 5px;
  margin-left: -25px;
  cursor: pointer;
  font-size: 80%;
}  
.odigies img {
border: 2px solid gray;
  border-radius: 10px;
  box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%), 0 6px 20px 0 rgb(0 0 0 / 19%);
  max-width:100%;
  width:250px;
}

.gks_eidos_label {
  font-size:0.8rem;
  /* font-weight: bold; */
  padding: 5px 0px 5px 0px;
  border-radius: 10px;
  text-align: center;
  margin-top: 2px;
}
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>



<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Διευθύνσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('autoaddress');?>>   
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυτόματη συμπλήρωση Διευθύνσεων');?>:</label>
            <div class="col-md-8">
              <?php
              $autocomplete_address_val='from_googlemaps';
              $sql="select myvalue from gks_settings_users where user_id=".$my_wp_user_id." and myobject='autocomplete' and mysubobject='address'";
              $result_select = $db_link->query($sql);
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('error sql');} 
              if ($result_select->num_rows==1) {
                $row_select = $result_select->fetch_assoc();
                $autocomplete_address_val=trim_gks($row_select['myvalue']);
              }
              
              ?>
              <input type="radio" name="autocomplete_address" id="autocomplete_address_none" value="none" style="cursor: pointer;" <?php if ($autocomplete_address_val=='none') echo 'checked';?>>  
              <label class="form-control-sm" for="autocomplete_address_none" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Ανενεργό');?></label>
              <br>
              <input type="radio" name="autocomplete_address" id="autocomplete_address_from_db" value="from_db" style="cursor: pointer;" <?php if ($autocomplete_address_val=='from_db') echo 'checked';?>>  
              <label class="form-control-sm" for="autocomplete_address_from_db" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Βάση Δεδομένων');?></label>
              <br>
              <input type="radio" name="autocomplete_address" id="autocomplete_address_from_googlemaps" value="from_googlemaps" style="cursor: pointer;" <?php if ($autocomplete_address_val=='from_googlemaps') echo 'checked';?>>  
              <label class="form-control-sm" for="autocomplete_address_from_googlemaps" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Google Maps');?>
              </label>
              <?php if ($GKS_GOOGLE_MAPS_API_KEY=='') echo '<small class="form-text text-muted">'.gks_lang('Για να λειτουργήσει η αναζήτηση μέσω τoυ Google Maps θα πρέπει στις ρυθμίσεις της εφαρμογής να ορίσετε το <a href="admin-system-settings.php#google_keys"><b>Google Maps Api Key (client)</b></a></small>');?>
              

            </div>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλογές Εκτυπώσης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('print');?>>   
          
          
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="file_type" id="file_type_pdf" value="pdf" style="cursor: pointer;" <?php if ($gks_user_settings['print']['file_type']=='pdf') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
                <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:150%"></i>
                </label>
              <input type="radio" name="file_type" id="file_type_html"  value="html" style="cursor: pointer;" <?php if ($gks_user_settings['print']['file_type']=='html') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
                <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>
                </label>
              <input type="radio" name="file_type" id="file_type_jpg"  value="jpg" style="cursor: pointer;" <?php if ($gks_user_settings['print']['file_type']=='jpg') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
                <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:21px;vertical-align: top;"></i>
                </label>
            </div>
          </div>           

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσανατολισμός');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="is_landscape" id="is_landscape_off" value="1" style="cursor: pointer;" <?php if ($gks_user_settings['print']['landscape']=='false') echo 'checked';?>>  
                <label class="form-control-sm" for="is_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
                <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:150%"></i>
                </label>
              <input type="radio" name="is_landscape" id="is_landscape_on"  value="2" style="cursor: pointer;" <?php if ($gks_user_settings['print']['landscape']!='false') echo 'checked';?>>  
                <label class="form-control-sm" for="is_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
                <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:150%"></i>
                </label>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="grayscale" id="grayscale_off" value="1" style="cursor: pointer;" <?php if ($gks_user_settings['print']['grayscale']=='false') echo 'checked';?>>  
                <label class="form-control-sm" for="grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
                <img src="img/palette-color.png" border="0" width="16">
                </label>
              <input type="radio" name="grayscale" id="grayscale_on"  value="2" style="cursor: pointer;" <?php if ($gks_user_settings['print']['grayscale']!='false') echo 'checked';?>>  
                <label class="form-control-sm" for="grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
                <img src="img/palette-gray.png" border="0" width="16">
                </label>
            </div>
          </div>
          

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μεγέθυνση');?>:</label>
            <div class="col-md-8">
              <div id="zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
                <div id="zoom_slider_handle" class="ui-slider-handle"></div>
              </div>
            </div>
          </div>
          <?php if ($GKS_WARE_HOUSE_ENABLE) {?>
          <div class="form-group row">
            <label for="print_form_id_whi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης δελτίων');?>:</label>
            <div class="col-md-6">
              <select id="print_form_id_whi"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              if (isset($gks_user_settings['print']['form_id_whi'])==false) $gks_user_settings['print']['form_id_whi']=0;
              $sql="SELECT id_print_form, print_form_descr
              FROM gks_print_forms
              order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_print_form'].'" ';
                if ($row_select['id_print_form']==$gks_user_settings['print']['form_id_whi']) echo ' selected ';
                echo '>'.$row_select['print_form_descr'].'</option>';
              }?></select>
            </div>
          </div>
          <?php } ?>          
          <?php if ($GKS_ORDERS_ENABLE) {?>
          <div class="form-group row">
            <label for="print_form_id_order" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης παραγγελιών');?>:</label>
            <div class="col-md-6">
              <select id="print_form_id_order"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              if (isset($gks_user_settings['print']['form_id_order'])==false) $gks_user_settings['print']['form_id_order']=0;
              $sql="SELECT id_print_form, print_form_descr
              FROM gks_print_forms
              order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_print_form'].'" ';
                if ($row_select['id_print_form']==$gks_user_settings['print']['form_id_order']) echo ' selected ';
                echo '>'.$row_select['print_form_descr'].'</option>';
              }?></select>
            </div>
          </div> 
          <?php }?>
          
          
          <?php if ($GKS_ACC_ENABLE) {?>      
          <div class="form-group row">
            <label for="print_form_id_inv" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης παραστατικών');?>:</label>
            <div class="col-md-6">
              <select id="print_form_id_inv"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              if (isset($gks_user_settings['print']['form_id_inv'])==false) $gks_user_settings['print']['form_id_inv']=0;
              $sql="SELECT id_print_form, print_form_descr
              FROM gks_print_forms
              order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_print_form'].'" ';
                if ($row_select['id_print_form']==$gks_user_settings['print']['form_id_inv']) echo ' selected ';
                echo '>'.$row_select['print_form_descr'].'</option>';
              }?></select>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="print_form_id_pay" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόρμα εκτύπωσης πληρωμών');?>:</label>
            <div class="col-md-6">
              <select id="print_form_id_pay"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              if (isset($gks_user_settings['print']['form_id_pay'])==false) $gks_user_settings['print']['form_id_pay']=0;
              $sql="SELECT id_print_form, print_form_descr
              FROM gks_print_forms
              order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_print_form'].'" ';
                if ($row_select['id_print_form']==$gks_user_settings['print']['form_id_pay']) echo ' selected ';
                echo '>'.$row_select['print_form_descr'].'</option>';
              }?></select>
            </div>
          </div> 
          <?php }?>
          
        </div>
      </div>

<?php if ($GKS_CRM_TASKS_ENABLE) { ?>

<?
      $def_duration_minutes=60;
      if (isset($gks_user_settings['gks_crm_tasks']['def_duration_minutes'])) {
        $def_duration_minutes=intval($gks_user_settings['gks_crm_tasks']['def_duration_minutes']);
      }
      $duration='';
      if ($def_duration_minutes < 24*60*60) { //kat apo mia 1 imera
        $duration=date('H:i',$def_duration_minutes*60);    
      }

              
?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpapptasks');?>>         
          <div class="form-group row">
            <label for="gks_crm_tasks_def_duration" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένη διάρκεια');?>:</label>
            <div class="col-md-8">
              <input id="gks_crm_tasks_def_duration" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $duration;?>" placeholder="" style="max-width:169px;display: inline-block;">
            </div>
          </div>
        </div>
      </div>
<?
      $settings_user_print_erp_app_id=0;
      if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_id'])) {
        $settings_user_print_erp_app_id=intval($gks_user_settings['gks_crm_tasks']['print_erp_app_id']);
      }
?>  
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('gks ERP App Desktop για τις Εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpapptask');?>> 

          <div class="form-group row">
            <label for="erp_app_id_check" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή στην gks ERP App Desktop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_id_check" value="1" <?php if ($settings_user_print_erp_app_id!=0) echo ' checked '; ?> class="switchery">
            </div>
          </div>
          
          <?php
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_id'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_id']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']='';
          if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder'])==false) $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder']='';

          $gks_user_settings['gks_crm_tasks']['print_erp_app_id']=intval($gks_user_settings['gks_crm_tasks']['print_erp_app_id']);
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']);
          if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='') $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']='printer';
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer']=trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer']);
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']=intval($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']);
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip']=trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip']);
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']=intval($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']);
          $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder']=trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder']);
          
          ?>
          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($settings_user_print_erp_app_id>0)) echo 'display:none;';?>">
            <label for="erp_app_id" class="col-md-4 col-form-label form-control-sm text-md-right">gks ERP App Desktop:</label>
            <div class="col-md-8">
              <select id="erp_app_id" class="form-control form-control-sm myneedsave">
                <option value="0" data-local-printers=""></option>
                <?php
                
                $erp_app_local_printers='';
                $sql="SELECT * from gks_erp_app where erp_app_disabled=0 order by erp_app_sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_erp_app'].'" '.
                  'data-local-printers="';
                  if (trim_gks($row_select['erp_app_local_printers'])!='') {
                    $temp=unserialize($row_select['erp_app_local_printers']); 
                    if (is_array($temp) and count($temp)>0) {
                      echo base64_encode(json_encode($temp));
                    }
                  }
                  echo '"';
                  if ($row_select['id_erp_app']==$settings_user_print_erp_app_id) {
                    echo ' selected ';
                    $erp_app_local_printers=trim_gks($row_select['erp_app_local_printers']);
                  }
                  echo '>'.$row_select['erp_app_name'].'</option>';
                }?>
              </select>
            </div>
          </div> 

          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($settings_user_print_erp_app_id>0)) echo 'display:none;';?>">
            <label for="erp_app_dest_val_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προορισμός');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_printer" value="printer" <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer') echo 'checked';?>>
                  <label for="erp_app_dest_val_printer"><?php echo gks_lang('Εκτυπωτής');?></label>
                <br>
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_folder" value="folder" <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='folder') echo 'checked';?>>
                  <label for="erp_app_dest_val_folder"><?php echo gks_lang('Φάκελος');?></label>
              </div>  
            </div>            
          </div>
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_method" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέθοδος');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_method" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==1) echo 'selected';?> value="1"><?php echo erp_app_dest_printer_method_descr(1);?></option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==0) echo 'selected';?> value="0"><?php echo erp_app_dest_printer_method_descr(0);?></option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==2) echo 'selected';?> value="2"><?php echo erp_app_dest_printer_method_descr(2);?></option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==3) echo 'selected';?> value="3"><?php echo erp_app_dest_printer_method_descr(3);?></option>

              </select>
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id01" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer' and in_array($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method'],[0,1]))) echo 'display:none;';?>">
            <label for="erp_app_dest_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer" class="form-control form-control-sm myneedsave">
                <option></option>
                <?php
                if ($erp_app_local_printers!='') {
                  $temp=unserialize($erp_app_local_printers);  
                  if (is_array($temp) and count($temp)>0) {
                    foreach ($temp as $value) {
                      echo '<option '.($value==$gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer'] ? 'selected' : '').'>'.$value.'</option>';
                    }
                  }
                }  
                ?>              
              </select>    
            </div>
          </div>

          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id2" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer' and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==2)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερή IP εκτυπωτή');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_printer_lpr_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.70">
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id3" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer' and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']==3)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <?php echo gks_lang('Στον προεπιλεγμένο εκτυπωτή του H/Y');?>
              </div>
            </div>
          </div> 
                    
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_copies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντίτυπα');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_copies" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']==1) echo 'selected';?>>1</option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']==2) echo 'selected';?>>2</option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']==3) echo 'selected';?>>3</option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']==4) echo 'selected';?>>4</option>
                <option <?php if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']==5) echo 'selected';?>>5</option>
              </select>
            </div>
          </div> 
          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_folder" style="<?php if (!($settings_user_print_erp_app_id>0 and $gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='folder')) echo 'display:none;';?>">
            <label for="erp_app_dest_folder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φάκελος');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_folder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder']);?>" placeholder="<?php echo gks_lang('π.χ.');?> c:\printer\folder\">
            </div>
          </div>

        </div>
      </div>


      
<?php } ?>
            
<?php if ($GKS_WARE_HOUSE_ENABLE) {?>      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλογές Αποθήκης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('whidelivery');?>>         
          <div class="form-group row">
            <label for="gks_whi_mov_tropos_apostolis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τρόπος αποστολής');?>:</label>
            <div class="col-md-8">
              <select id="gks_whi_mov_tropos_apostolis"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_delivery_methods ORDER BY mysortorder,delivery_method_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_delivery_method'].'" ';
                if ($row_select['id_delivery_method']==$gks_user_settings['gks_whi_mov']['tropos_apostolis']) echo ' selected ';
                echo '>'.$row_select['delivery_method_name'].'</option>';
              }?></select>
            </div>
          </div> 
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σειρά πεδίων κατά το πάτημα του πλήκτρου Enter σε Δελτίο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('enterwhi_mov');?>>         
          
          <?php
          $system_fields=array();
          $system_fields['gks_code']=gks_lang('Κωδικός');
          $system_fields['gks_descr']=gks_lang('Περιγραφή');
          $system_fields['gks_comments']=gks_lang('Παρατηρήσεις');
          $system_fields['gks_quantity']=gks_lang('Ποσότητα');
          $system_fields['new_row']=gks_lang('Νέα γραμμή');
          
          $user_enter_order=array();
          if (isset($gks_user_settings['gks_whi_mov']['enter_order']) and is_array($gks_user_settings['gks_whi_mov']['enter_order'])) {
            $user_enter_order= $gks_user_settings['gks_whi_mov']['enter_order'];
          }
          //echo '<pre>'; print_r($gks_user_settings); echo '</pre>';
          
          echo '<div id="enter_order_gks_whi_mov">';
          
          foreach ($user_enter_order as $item) {
            foreach ($system_fields as $kitem => $sitem) {
              if ($item == $kitem) {
                if ($sitem!='') {
                  echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
                  $system_fields[$kitem]='';
                }
              }
            }
          } 
          foreach ($system_fields as $kitem => $sitem) {
            if ($sitem!='') {
              echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
            }
          }
          echo '</div>';
          ?>

               
        </div>
      </div>
<?php } ?>

<?php if ($GKS_ORDERS_ENABLE) {?>      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλογές Παραγγελιών');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('previw');?>>         
          <div class="form-group row">
            <label for="gks_orders_tropos_apostolis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τρόπος αποστολής');?>:</label>
            <div class="col-md-8">
              <select id="gks_orders_tropos_apostolis"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_delivery_methods ORDER BY mysortorder,delivery_method_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_delivery_method'].'" ';
                if ($row_select['id_delivery_method']==$gks_user_settings['gks_orders']['tropos_apostolis']) echo ' selected ';
                echo '>'.$row_select['delivery_method_name'].'</option>';
              }?></select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="gks_orders_tropos_pliromis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τρόπος πληρωμής');?>:</label>
            <div class="col-md-8">
              <select id="gks_orders_tropos_pliromis"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_payment_acquirers ORDER BY mysortorder,payment_acquirer_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_payment_acquirer'].'" ';
                if ($row_select['id_payment_acquirer']==$gks_user_settings['gks_orders']['tropos_pliromis']) echo ' selected ';
                echo '>'.$row_select['payment_acquirer_name'].'</option>';
              }?></select>
            </div>
          </div> 

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σειρά πεδίων κατά το πάτημα του πλήκτρου Enter σε Παραγγελία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('enterpar');?>>         
          
          <?php
          $system_fields=array();
          if ($GKS_ORDERS_SETS) $system_fields['gks_set']=gks_lang('Σετ');
          $system_fields['gks_code']=gks_lang('Κωδικός');
          $system_fields['gks_descr']=gks_lang('Περιγραφή');
          $system_fields['gks_comments']=gks_lang('Παρατηρήσεις');
          if ($GKS_ORDERS_SHEETS) $system_fields['gks_sheets']=gks_lang('Σελίδες');
          $system_fields['gks_quantity']=gks_lang('Ποσότητα');
          if ($GKS_ORDERS_COL_ITEMPRICE) $system_fields['gks_peritem_net']=gks_lang('Τιμή');
          $system_fields['gks_ekptosi_pososto']=gks_lang('Έκπτωση %');
          $system_fields['gks_price']=gks_lang('Σύνολο');
          $system_fields['new_row']=gks_lang('Νέα γραμμή');

          
          $user_enter_order=array();
          if (isset($gks_user_settings['gks_orders']['enter_order']) and is_array($gks_user_settings['gks_orders']['enter_order'])) {
            $user_enter_order= $gks_user_settings['gks_orders']['enter_order'];
          }
          //echo '<pre>'; print_r($gks_user_settings); echo '</pre>';
          
          echo '<div id="enter_order_paraggelia">';
          
          foreach ($user_enter_order as $item) {
            foreach ($system_fields as $kitem => $sitem) {
              if ($item == $kitem) {
                if ($sitem!='') {
                  echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
                  $system_fields[$kitem]='';
                }
              }
            }
          } 
          foreach ($system_fields as $kitem => $sitem) {
            if ($sitem!='') {
              echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
            }
          }
          echo '</div>';
          ?>

               
        </div>
      </div>
<?php } ?>

<?php if ($GKS_ACC_ENABLE) {?>      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλογές Παραστατικών');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('parast');?>>         
          <div class="form-group row">
            <label for="gks_acc_inv_tropos_apostolis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τρόπος αποστολής');?>:</label>
            <div class="col-md-8">
              <select id="gks_acc_inv_tropos_apostolis"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_delivery_methods ORDER BY mysortorder,delivery_method_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_delivery_method'].'" ';
                if ($row_select['id_delivery_method']==$gks_user_settings['gks_acc_inv']['tropos_apostolis']) echo ' selected ';
                echo '>'.$row_select['delivery_method_name'].'</option>';
              }?></select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="gks_acc_inv_tropos_pliromis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τρόπος πληρωμής');?>:</label>
            <div class="col-md-8">
              <select id="gks_acc_inv_tropos_pliromis"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_payment_acquirers ORDER BY mysortorder,payment_acquirer_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_payment_acquirer'].'" ';
                if ($row_select['id_payment_acquirer']==$gks_user_settings['gks_acc_inv']['tropos_pliromis']) echo ' selected ';
                echo '>'.$row_select['payment_acquirer_name'].'</option>';
              }?></select>
            </div>
          </div> 

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σειρά πεδίων κατά το πάτημα του πλήκτρου Enter σε Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('enterprs');?>>         
          
          <?php
          $system_fields=array();
          $system_fields['gks_code']=gks_lang('Κωδικός');
          $system_fields['gks_descr']=gks_lang('Περιγραφή');
          $system_fields['gks_comments']=gks_lang('Παρατηρήσεις');
          $system_fields['gks_quantity']=gks_lang('Ποσότητα');
          if ($GKS_ORDERS_COL_ITEMPRICE) $system_fields['gks_peritem_net']=gks_lang('Τιμή');
          $system_fields['gks_ekptosi_pososto']=gks_lang('Έκπτωση %');
          $system_fields['gks_price']=gks_lang('Σύνολο');
          $system_fields['new_row']=gks_lang('Νέα γραμμή');

          
          $user_enter_order=array();
          if (isset($gks_user_settings['gks_acc_inv']['enter_order']) and is_array($gks_user_settings['gks_acc_inv']['enter_order'])) {
            $user_enter_order= $gks_user_settings['gks_acc_inv']['enter_order'];
          }
          //echo '<pre>'; print_r($gks_user_settings); echo '</pre>';
          
          echo '<div id="enter_order_parastatiko">';
          
          foreach ($user_enter_order as $item) {
            foreach ($system_fields as $kitem => $sitem) {
              if ($item == $kitem) {
                if ($sitem!='') {
                  echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
                  $system_fields[$kitem]='';
                }
              }
            }
          } 
          foreach ($system_fields as $kitem => $sitem) {
            if ($sitem!='') {
              echo '<div class="gks_jui_sortable_item" id="'.$kitem.'">'.$sitem.'</div>';
            }
          }
          echo '</div>';
          ?>

               
        </div>
      </div>

<?php } ?>


      
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εμφάνιση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('menu');?>>    
          
          <div class="form-group row">
            <label for="user_lang_backend_el-GR" class="col-sm-6 col-form-label form-control-sm text-sm-right" ><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-6" style="font-size: 0.875rem;">
              <?php
              $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
          
              $sql_lang="select gks_lang.*,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
              FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
              ".$lang_prepare_gks_lang['sql']['from2']."
              where lang_on_backend=1
              ORDER BY lang_sortorder";
              //echo '<pre>';echo $sql_lang."\r\n\r\n";die();
              
              $result_lang = $db_link->query($sql_lang);
              if (!$result_lang) {debug_mail(false,'error sql',$sql_lang);  die('sql error');}        
              
              $temp_lang_array=array();
              while ($row_lang = $result_lang->fetch_assoc()) {
                $temp_lang_array[]='<input type="radio" name="user_lang_backend" id="user_lang_backend_'.$row_lang['id_lang'].'" value="'.$row_lang['id_lang'].'" '.($gks_user_settings['lang']['backend']==$row_lang['id_lang'] ? 'checked' : '').'>'.
                ' <label for="user_lang_backend_'.$row_lang['id_lang'].'">'.$row_lang['lang_name'].'</label>';
              }
              echo implode('<br>',$temp_lang_array);
              
              ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="menu_pos_top" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Θέση μενού');?>:</label>
            <div class="col-sm-6" style="font-size: 0.875rem;">
              <input type="radio" name="menu_pos" id="menu_pos_top" value="el-GR" <?php if ($gks_user_settings['menu']['pos']=='') echo ' checked ';?> >
              <label for="menu_pos_top"><?php echo gks_lang('Επάνω');?></label>
              <br>
              <input type="radio" name="menu_pos" id="menu_pos_left" value="en-US" <?php if ($gks_user_settings['menu']['pos']=='left') echo ' checked ';?> >
              <label for="menu_pos_left"><?php echo gks_lang('Αριστερά');?></label>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="menu_sticky_top" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Στατικό Μενού');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="menu_sticky_top" class="switchery_menu_sticky_top" <?php if ($gks_user_settings['menu']['sticky-top']=='1') echo ' checked ';?>>
              <small class="form-text text-muted"><?php echo gks_lang('Σε κινητό ή tablet το μενού θα είναι πάντη μη στατικό');?></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="menu_hover" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αυτόματη Εμφάνιση Υπομενού');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="menu_hover" class="switchery" <?php if ($gks_user_settings['menu']['hover']=='1') echo ' checked ';?> >
              <small class="form-text text-muted"><?php echo gks_lang('Αυτόματη εμφάνιση του μενού όταν ο κερσορας βρίσκεται επάνω του. Ισχύει μόνο για το 1ο επίπεδο');?></small>
            </div>
          </div> 
             
          <div class="form-group row">
            <label for="htmlcss_font_family" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οικογένεια Γραμματοσειράς');?>:</label>
            <div class="col-sm-6">
              <select id="htmlcss_font_family"  class="form-control form-control-sm myneedsave"  >
                <option value=""><?php echo gks_lang('Προεπιλογή');?></option>
                <optgroup label="<?php echo gks_lang('Τυπικές γραμματοσειρές');?>">
                <?php
                $merge_font=$gks_user_settings['htmlcss']['font_family_group'].'|||'.$gks_user_settings['htmlcss']['font_family'];
                
                $myaaa=array();
                $myaaa[]="Arial, Helvetica, sans-serif";
                $myaaa[]="'Arial Black', Gadget, sans-serif";
                $myaaa[]="'Bookman Old Style', serif";
                $myaaa[]="'Comic Sans MS', cursive";
                $myaaa[]="Courier, monospace";
                $myaaa[]="Garamond, serif";
                $myaaa[]="Georgia, serif";
                $myaaa[]="Impact, Charcoal, sans-serif";
                $myaaa[]="'Lucida Console', Monaco, monospace";
                $myaaa[]="'Lucida Sans Unicode', 'Lucida Grande', sans-serif";
                $myaaa[]="'MS Sans Serif', Geneva, sans-serif";
                $myaaa[]="'MS Serif', 'New York', sans-serif";
                $myaaa[]="'Palatino Linotype', 'Book Antiqua', Palatino, serif";
                $myaaa[]="Tahoma,Geneva, sans-serif";
                $myaaa[]="'Times New Roman', Times,serif";
                $myaaa[]="'Trebuchet MS', Helvetica, sans-serif";

                foreach ($myaaa as $vaaa) {
                  $vbbb='st_fonts|||'.$vaaa;
                  $saaa='';
                  if ($merge_font==$vbbb) $saaa='selected';
                  echo '<option value="'.$vbbb.'" '.$saaa.'>'.$vaaa.'</option>';
                } 
                ?>
                </optgroup>
                
                <optgroup label="<?php echo gks_lang('Google Τοπικά');?>">
                <?php
                $myaaa=array();
                $myaaa[]="Advent Pro";
                $myaaa[]="Inter";
                $myaaa[]="Open Sans";
                $myaaa[]="Roboto";
                $myaaa[]="Roboto Condensed";    
                foreach ($myaaa as $vaaa) {
                  $vbbb='gl_fonts|||'.$vaaa;
                  $saaa='';
                  if ($merge_font==$vbbb) $saaa='selected';
                  echo '<option value="'.$vbbb.'" '.$saaa.'>'.$vaaa.'</option>';
                }
                ?>
                </optgroup>
                <optgroup label="<?php echo gks_lang('Google CDN');?>">
                <?php
                $myaaa=array();
                $myaaa[]="Advent Pro";
                $myaaa[]="Alegreya";
                $myaaa[]='Alegreya SC';
                $myaaa[]='Alegreya Sans SC';
                $myaaa[]='Alegreya Sans';
                $myaaa[]='Anonymous Pro';
                $myaaa[]='Arima';
                $myaaa[]='Arimo';
                $myaaa[]='Bona Nova SC';
                $myaaa[]='Bona Nova';
                $myaaa[]='Brygada 1918';
                $myaaa[]='Cardo';
                $myaaa[]='Carlito';
                $myaaa[]='Cascadia Code';
                $myaaa[]='Cascadia Mono';
                $myaaa[]='Caudex';
                $myaaa[]='Chiron Hei HK';
                $myaaa[]='Chiron Sung HK';
                $myaaa[]='Comfortaa';
                $myaaa[]='Comic Relief';
                $myaaa[]='Commissioner';
                $myaaa[]='Cousine';
                $myaaa[]='Dela Gothic One';
                $myaaa[]='Didact Gothic';
                $myaaa[]='EB Garamond';
                $myaaa[]='Eczar';
                $myaaa[]='Fira Code';
                $myaaa[]='Fira Mono';
                $myaaa[]='Fira Sans Condensed';
                $myaaa[]='Fira Sans Extra Condensed';
                $myaaa[]='Fira Sans';
                $myaaa[]='Gentium Book Plus';
                $myaaa[]='Gentium Plus';
                $myaaa[]='Geologica';
                $myaaa[]='GFS Didot';
                $myaaa[]='GFS Neohellenic';
                $myaaa[]='Gidole';
                $myaaa[]='Gothic A1';
                $myaaa[]='Handjet';
                $myaaa[]='IBM Plex Sans';
                $myaaa[]='Inter';
                $myaaa[]='Inter Tight';
                $myaaa[]='JetBrains Mono';
                $myaaa[]='Jura';
                $myaaa[]='Libertinus Math';
                $myaaa[]='Libertinus Sans';
                $myaaa[]='Libertinus Serif';
                $myaaa[]='Libertinus Serif Display';
                $myaaa[]='Literata';
                $myaaa[]='LXGW Marker Gothic';
                $myaaa[]='LXGW WenKai Mono TC';
                $myaaa[]='LXGW WenKai TC';
                $myaaa[]='M PLUS 1p';
                $myaaa[]='M PLUS Rounded 1c';
                $myaaa[]='Manrope';
                $myaaa[]='Mansalva';
                $myaaa[]='Moderustic';
                $myaaa[]='Murecho';
                $myaaa[]='Mynerve';
                $myaaa[]='News Cycle';
                $myaaa[]='Noto Sans Display';
                $myaaa[]='Noto Sans Mono';
                $myaaa[]='Noto Sans';
                $myaaa[]='Noto Serif Display';
                $myaaa[]='Noto Serif';
                $myaaa[]='Nova Mono';
                $myaaa[]='Oi';     
                $myaaa[]='Open Sans';
                $myaaa[]='Piazzolla';
                $myaaa[]='Play';
                $myaaa[]='Playpen Sans';
                $myaaa[]='Press Start 2P';
                $myaaa[]='Roboto';
                $myaaa[]='Roboto Condensed';
                $myaaa[]='Roboto Flex';
                $myaaa[]='Roboto Mono';
                $myaaa[]='Roboto Slab';
                $myaaa[]='Sansation';
                $myaaa[]='Sofia Sans';
                $myaaa[]='Sofia Sans Condensed';
                $myaaa[]='Sofia Sans Extra Condensed';
                $myaaa[]='Sofia Sans Semi Condensed';
                $myaaa[]='Source Code Pro';
                $myaaa[]='Source Sans 3';
                $myaaa[]='Source Serif 4';
                $myaaa[]='STIX Two Text';
                $myaaa[]='Syne';
                $myaaa[]='Tektur';
                $myaaa[]='TikTok Sans';
                $myaaa[]='Tinos';
                $myaaa[]='Tiny5';
                $myaaa[]='Tuffy';
                $myaaa[]='Ubuntu';
                $myaaa[]='Ubuntu Condensed';
                $myaaa[]='Ubuntu Mono';
                $myaaa[]='Ubuntu Sans';
                $myaaa[]='Ubuntu Sans Mono';
                $myaaa[]='UNAL Ancizar Sans';
                $myaaa[]='UNAL Ancizar Serif';
                $myaaa[]='Victor Mono';
                $myaaa[]='Vollkorn';
                $myaaa[]='Ysabeau';
                $myaaa[]='Ysabeau Infant';
                $myaaa[]='Ysabeau Office';
                $myaaa[]='Ysabeau SC';
                $myaaa[]='Zen Antique';
                $myaaa[]='Zen Antique Soft';
                $myaaa[]='Zen Kurenaido';
                $myaaa[]='Zen Maru Gothic';
                $myaaa[]='Zen Old Mincho';

                foreach ($myaaa as $vaaa) {
                  $vbbb='glcdn_fonts|||'.$vaaa;
                  $saaa='';
                  if ($merge_font==$vbbb) $saaa='selected';
                  echo '<option value="'.$vbbb.'" '.$saaa.'>'.$vaaa.'</option>';
                }
                ?>
                </optgroup>
                
              </select>
            </div>
          </div>             
          <div class="form-group row">
            <label for="htmlcss_font_size" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μέγεθος Γραμματοσειράς');?>:</label>
            <div class="col-sm-6">
              <select id="htmlcss_font_size"  class="form-control form-control-sm myneedsave"  >
                <option value=""><?php echo gks_lang('Προεπιλογή');?></option>
                <?php for ($ifs = 6; $ifs <= 30; $ifs++) {
                  echo '<option value="'.$ifs.'px" '.($gks_user_settings['htmlcss']['font_size']==$ifs.'px' ? 'selected' : '').'>'.$ifs.'px</option>';
                }?>
              </select>
            </div>
          </div>             
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ειδοποιήσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cardnotf');?>>

          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button type="button" class="btn btn-primary notif_all"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-primary notif_none"><?php echo gks_lang('Καμία');?></button>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12 col-md-6">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Αντικείμενο');?>
              </div>
            </div>
            <div class="col-2 col-md-2 text-md-center">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Ειδοποίηση');?>
              </div>
            </div>
            <div class="col-2 col-md-2 text-md-center" "="">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('email');?>
              </div>
            </div>
            <div class="col-2 col-md-2 text-md-center" "="">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Viber');?>
              </div>
            </div>
          </div>
          
          <div class="form-group row">
            <div class="col-12 col-md-6">
              
            </div>
            <div class="col-2 col-md-2 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_user_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_user_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
            <div class="col-2 col-md-2 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_to_email_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_to_email_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
            <div class="col-2 col-md-2 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_to_viber_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_to_viber_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
          </div>

          <?php
          $sql_notif_type="SELECT id_notification_type,notification_type_descr FROM gks_notification_type WHERE notification_type_disabled=0";
          if ($GKS_HOTEL_BACKEND==false and GKS_TRANSFER==false) $sql_notif_type.=" and id_notification_type not in (1010)";
          if ($GKS_ORDERS_PRODUCTION==false) $sql_notif_type.=" and id_notification_type not in (510)";
          if ($GKS_CRM_ENABLE==false) $sql_notif_type.=" and id_notification_type not in (50)";
          $sql_notif_type.=" ORDER BY notification_type_sortorder;";
          $result_notif_type = $db_link->query($sql_notif_type);
          if (!$result_notif_type) {debug_mail(false,'error sql',$sql_notif_type);  die('sql error');}
          $notifs_array=array();
          while ($row_notif_type = $result_notif_type->fetch_assoc()) {
            $row_notif_type['admin']=false;
            $row_notif_type['user']=false;
            $row_notif_type['email']=false;
            $row_notif_type['viber']=false;
            $notifs_array[$row_notif_type['id_notification_type']]=$row_notif_type;
          }
               
          $sql_notif_type="select * from gks_notification_userperm where user_id=".$my_wp_user_id;
          $result_notif_type = $db_link->query($sql_notif_type);
          if (!$result_notif_type) {debug_mail(false,'error sql',$sql_notif_type);  die('sql error');}
          while ($row_notif_type = $result_notif_type->fetch_assoc()) {
            if (isset($notifs_array[$row_notif_type['notification_type_id']])) {
              $notifs_array[$row_notif_type['notification_type_id']]['admin']=intval($row_notif_type['from_admin'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['user']=intval($row_notif_type['from_user'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['email']=intval($row_notif_type['to_email'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['viber']=intval($row_notif_type['to_viber'])!=0;
              if ($notifs_array[$row_notif_type['notification_type_id']]['admin']==false) {
                $notifs_array[$row_notif_type['notification_type_id']]['user']=false;
                $notifs_array[$row_notif_type['notification_type_id']]['email']=false;
                $notifs_array[$row_notif_type['notification_type_id']]['viber']=false;
              }
            }
          }
          
          foreach ($notifs_array as $nid => $notif) {
            if ($notif['admin']==1) {
            ?>                    
          <div class="form-group row">
            <label class="col-12 col-md-6 col-form-label form-control-sm text-md-center1 gks_flock row_notif" data-nid="<?php echo $nid;?>"><?php echo $notif['notification_type_descr'];?></label>
            
            <div class="col-2 col-md-2 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['user'])  echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_user_item" data-nid="<?php echo $nid;?>">
            </div>
            <div class="col-2 col-md-2 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['email']) echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_to_email_item" data-nid="<?php echo $nid;?>">
            </div>
            <div class="col-2 col-md-2 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['viber']) echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_to_viber_item" data-nid="<?php echo $nid;?>">
            </div>

          </div>
                    
          <?php }} ?>
          
        </div>    
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τηλεφωνικό κέντρο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('voip');?>>
          <div class="form-group row">
            <label for="voip_extensions" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερικό τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="voip_extensions" type="text" class="form-control form-control-sm myneedsave" value="<?php echo implode(',',$gks_user_settings['voip']['extensions']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('π.χ.');?> 111">
              <small class="form-text text-muted"><?php echo gks_lang('Εάν έχετε πάνω από ένα, καταχωρήστε τα διαχωρισμένα με κόμμα π.χ. 111,112,113');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="voip_extension_def" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερικό τηλέφωνο για αυτόν τον φυλλομετρητή');?>:</label>
            <div class="col-md-8">
              <input id="voip_extension_def" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('π.χ.');?> 111">
              <small class="form-text text-muted"><?php echo gks_lang('Μπορεί να είναι διαφορετικό ανά φυλλομετρητή ή συσκευή π.χ. το 111 στον Η/Υ και το 112 στο κινητό.');?></small>
            </div>
          </div>
          
        </div>    
      </div>

      
      <?php
      $perm_ret_dav_card=gks_permission_user_can_action($my_wp_user_id, 'dav_card','view',0);
      $perm_ret_dav_cal=gks_permission_user_can_action($my_wp_user_id, 'dav_cal','view',0);
      //var_dump($perm_ret_dav_card);
      //var_dump($perm_ret_dav_cal);
      
      if ($perm_ret_dav_card['success'] or $perm_ret_dav_cal['success']) {
      ?>
            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php 
          $temp=[];
          if ($perm_ret_dav_card['success'])$temp[]='CardDAV';
          if ($perm_ret_dav_cal['success'])$temp[]='CalDav';
          echo implode(' &amp; ',$temp);
          ?>
        </div>
        <div class="card-body" <?php echo gks_card_body('dav');?>>    
          <div class="form-group row">
            <label for="dav_url_frontend" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url FrontEnd');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <a href="<?php echo GKS_SITE_URL.'my/dav';?>" target="_blank"
                ><?php echo GKS_SITE_URL.'my/dav';?></a>
              </div>
            </div>
          </div>
          
          <?php
          $gks_webdav_folders_local=[];
          $_dav_db_php_path='_current/_dav_db.php';
          if (file_exists($_dav_db_php_path)) {
            $code=file_get_contents($_dav_db_php_path);
            $safeCode = preg_replace(
                "/define\s*\(\s*(['\"](.*?)['\"])\s*,/",
                "if(!defined($1xxxxxxxx)) define($1xxxxxxxx,",
                $code
            );
            $safeCode=str_replace('\'xxxxxxxx', 'xxxxxxxx\'', $safeCode);
            $safeCode=str_replace('<?php', '', $safeCode);
            $safeCode=str_replace('?>', '', $safeCode);
            //echo '<pre>';echo $safeCode;die();
            try {
              $res=eval($safeCode);
            } catch (ParseError  $e) {
            } catch(ArithmeticError $e){
            } catch(DivisionByZeroError $e){
            } catch(TypeError $e){
            } catch(AssertionError $e){
            } catch(Exception  $e){
            }
            //print '<pre>';print_r(GKS_WEBDAV_FOLDERSxxxxxxxx);die();
            if (defined('GKS_WEBDAV_FOLDERSxxxxxxxx') and is_array(GKS_WEBDAV_FOLDERSxxxxxxxx) and count(GKS_WEBDAV_FOLDERSxxxxxxxx)>=2) {
              if (isset(GKS_WEBDAV_FOLDERSxxxxxxxx['tmp'])) {
                $gks_webdav_folders_local=GKS_WEBDAV_FOLDERSxxxxxxxx;
              }
            }
            //print '<pre>';print_r($gks_webdav_folders_local);die();
          }
          ?>
          <?php if (count($gks_webdav_folders_local)>=2) {?>
          <div class="form-group row">
            <label for="dav_url_webdav" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url WebDav');?>:</label>
            <div class="col-md-8">
              <input id="dav_url_webdav" type="text" class="form-control form-control-sm myneedsave" value="<?php echo GKS_SITE_URL.'my/dav/';?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
              <small class="form-text text-muted">
                <?php 
                $temp_davs=[];
                foreach ($gks_webdav_folders_local as $key => $value) {
                  if ($key!='tmp') {
                    $temp_davs[]='<a href="'.
                    GKS_SITE_URL.'my/dav/'.basename($value).
                    '" target="_blank">'.basename($value).'</a>';
                  }
                } 
                echo implode(', ',$temp_davs);
                ?>
              </small>
            </div>
          </div>                    
          <?php } ?>
        
          <?php if ($perm_ret_dav_card['success']) {?>   
          <div class="form-group row">
            <label for="dav_url_carddav" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url CardDav');?>:</label>
            <div class="col-md-8">
              <input id="dav_url_carddav" type="text" class="form-control form-control-sm myneedsave" value="<?php echo GKS_SITE_URL.'my/dav/addressbooks/'.$my_wp_user_info->user_login.'/default/';?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
            </div>
          </div>
          <?php } ?>

          <?php if ($perm_ret_dav_cal['success']) {?>   
          <div class="form-group row">
            <label for="dav_url_caldav" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url CalDav');?>:</label>
            <div class="col-md-8">
              <input id="dav_url_caldav" type="text" class="form-control form-control-sm myneedsave" value="<?php echo GKS_SITE_URL.'my/dav/calendars/'.$my_wp_user_info->user_login.'/default/';?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
            </div>
          </div>
          <?php } ?>

          <div class="form-group row">
            <label for="dav_username" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα χρήστη');?>:</label>
            <div class="col-md-8">
              <input id="dav_username" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $my_wp_user_info->user_login;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
            </div>
          </div>
          <div class="form-group row">
            <label for="dav_password" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός πρόσβασης','part2');?>:</label>
            <div class="col-md-8">
              <input id="dav_password" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $gks_user_settings['dav']['password'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
<?php
          $sql_user_dav="select * from ".GKS_WP_TABLE_PREFIX."users where ID=".$my_wp_user_id;
          $result_user_da = $db_link->query($sql_user_dav); 
          if (!$result_user_da) {debug_mail(false,'error sql',$sql_user_dav);die('sql error');}
          $row_user_dav = $result_user_da->fetch_assoc();
          $my_warnings=array();
          if (trim_gks($row_user_dav['user_login'])=='') $my_warnings[]=gks_lang('Δεν έχετε ορίσει το όνομα χρήστη');
          if (trim_gks($row_user_dav['user_email'])=='') $my_warnings[]=gks_lang('Δεν έχετε ορίσει το email σας');
          if (trim_gks($row_user_dav['gks_nickname'])=='') $my_warnings[]=gks_lang('Δεν έχετε ορίσει το υποκοριστικό σας');
          if (trim_gks($row_user_dav['gks_wp_capabilities'])=='' and (strpos(trim_gks($row_user_dav['gks_wp_capabilities']), 'subscriber')===false)) $my_warnings[]=gks_lang('Δεν θα πρέπει να έχετε τον ρόλο του συνδρομητή ή κενό ρόλο');
          
          if (count($my_warnings)>0) {
            echo '<div class="alert alert-danger" role="alert">
              '.implode('<br>',$my_warnings).'
            </div>';
          }
?>          
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
          
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <?php echo gks_lang('Ρυθμίσεις για Android');?>
            </div>
          </div>
          
          <?php if ($perm_ret_dav_card['success']) {?>  
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;font-size:0.9rem;">
              <span class="btn btn-sm btn-primary" id="android_carddav"><?php echo gks_lang('CardDAV - Επαφές - Οδηγίες');?></span>
            </div>
          </div>
          
          <div class="form-group row" id="android_carddav_div" style="display:none;">
            <div class="odigies col-md-12" style="font-size:0.8rem;">
              
              <p><?php echo gks_lang('Στο κινητό σας θα πρέπει να κάνετε εγκατάσταση την εφαρμογή');?> <a href="https://play.google.com/store/apps/details?id=org.dmfs.carddav.Sync" target="_blank">CardDAV-Sync</a><br>
              <?php echo gks_lang('Ανοίξτε την εφαρμογή από το εικονίδιο');?>:</p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/carddav_icon.png" style="width:100px;"/></p>
              <p><?php echo gks_lang('Θα πρέπει να προσθέσετε έναν νέο λογαριασμό');?><br>
              <?php echo gks_lang('Ο λογαριασμός θα είναι τύπου <b>CardDAV</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step1.jpg"/></p>
              <p><?php echo gks_lang('Στο πεδίο <b>Server name or URL</b> εισάγετε το');?>:<br>
              <b><?php echo GKS_SITE_URL.'my/dav/addressbooks/'.$my_wp_user_info->user_login.'/default/';?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Username</b> εισάγετε το:');?><br>
              <b><?php echo $my_wp_user_info->user_login;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Password</b> εισάγετε το:');?><br>
              <b><?php echo $gks_user_settings['dav']['password'];?></b><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>NEXT</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step2.jpg" /></p>
              <p><?php echo gks_lang('Εάν εμφανιστεί το παρακάτω μήνυμα, απλά κάντε κλικ στο κουμπί <b>ΟΚ</b>');?></p>
              
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step3.jpg"/></p>
              <p><?php echo gks_lang('Στο πεδίο <b>Account Name</b> ορίστε ένα όνομα για αυτόν τον λογαριασμό');?><br>
              <?php echo gks_lang('Ενεργοποιήστε το <b>Sync from server to phone only</b>');?>
              <br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>FINISH</b>');?></p>
              
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step4.jpg"/></p>
              <p><?php echo gks_lang('Ο λογαριασμός έχει προστεθεί');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>DONE</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step5.jpg"/></p>
              <p><?php echo gks_lang('Μπορείτε να κάνετε επιπλέον ρυθμίσεις για αυτό τον λογαριασμό κάνοντας κλικ στο όνομά του');?><br>
              <?php echo gks_lang('π.χ. κάθε πότε να γίνεται ο συγχρονισμός');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step6.jpg"/></p>
              <p><?php echo gks_lang('Μπορείτε να κάνετε άμεσα συγχρονισμό:');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step7.jpg"/></p>
              <p><?php echo gks_lang('Ανοίξτε την εφαρμογή <b>Επαφές</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/contacts.jpg" style="width:100px;"/></p>  
              
              <p><?php echo gks_lang('για να δείτε πόσες επαφές είναι από τον συγκεκριμένο λογαριασμό');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step8.jpg"/></p>
              <p><?php echo gks_lang('π.χ. 815');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CardDav/step9.jpg"/></p>

              
            </div>
          </div>
          <?php } ?>

          
          <?php if ($perm_ret_dav_cal['success']) {?>    
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;font-size:0.9rem;">
              <span class="btn btn-sm btn-primary" id="android_caldav"><?php echo gks_lang('CalDAV - Ημερολόγιο - Οδηγίες');?></span>
            </div>
          </div>
          <div class="form-group row" id="android_caldav_div" style="display:none;">
            <div class="odigies col-md-12" style="font-size:0.8rem;">

              <p><?php echo gks_lang('Στο κινητό σας θα πρέπει να κάνετε εγκατάσταση την εφαρμογή');?> <a href="https://play.google.com/store/apps/details?id=org.dmfs.caldav.lib" target="_blank">CalDAV-Sync</a><br>
              <?php echo gks_lang('Ανοίξτε την εφαρμογή από το εικονίδιο:');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/caldav_icon.png" style="width:100px;"/></p>
              <p><?php echo gks_lang('Θα πρέπει να προσθέσετε έναν νέο λογαριασμό');?><br>
              <?php echo gks_lang('Ο λογαριασμός θα είναι τύπου <b>CalDAV</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step1.jpg"/></p>
              <p><?php echo gks_lang('Στο πεδίο <b>Server name or URL</b> εισάγετε το:');?><br>
              <b><?php echo GKS_SITE_URL.'my/dav/calendars/'.$my_wp_user_info->user_login.'/default/';?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Username</b> εισάγετε το:');?><br>
              <b><?php echo $my_wp_user_info->user_login;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Password</b> εισάγετε το:');?><br>
              <b><?php echo $gks_user_settings['dav']['password'];?></b><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>NEXT</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step2.jpg"/></p>
              <p><?php echo gks_lang('Εάν εμφανιστεί το παρακάτω μήνυμα, απλά κάντε κλικ στο κουμπί <b>ΟΚ</b>');?></p>
              
              <p style="text-align:center"v><img class="img-fluid" src="img/Android/CalDav/step3.jpg"/></p>
              
              <p><?php echo gks_lang('Θα εμφανιστεί το διαθέσιμο ημερολόγιο');?><br>
              <?php echo gks_lang('Βεβαιωθείτε ότι είναι επιλεγμένο και κάντε κλικ στο κουμπί <b>NEXT</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step4.jpg"/></p>
              
              
              <p><?php echo gks_lang('Στο πεδίο <b>Account Name</b> ορίστε ένα όνομα για αυτόν τον λογαριασμό');?><br>
              <?php echo gks_lang('Μην ενεργοποιήστε το <b>Sync from server to phone only</b>');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>FINISH</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step5.jpg"/></p>
              
              <p><?php echo gks_lang('Ο λογαριασμός έχει προστεθεί');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>DONE</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step6.jpg"/></p>
              <p><?php echo gks_lang('Μπορείτε να κάνετε επιπλέον ρυθμίσεις για αυτό τον λογαριασμό κάνοντας κλικ στο όνομά του');?><br>
              <?php echo gks_lang('π.χ. κάθε πότε να γίνεται ο συγχρονισμός');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step7.jpg"/></p>
              <p><?php echo gks_lang('Μπορείτε να κάνετε άμεσα συγχρονισμό:');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step8.jpg"/></p>
              <p><?php echo gks_lang('Ανοίξτε την εφαρμογή <b>Ημερολόγιο</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/calendar.jpg" style="width:100px;"/></p>  
                
              <p><?php echo gks_lang('Βεβαιωθείτε ότι το συγκεκριμένο ημερολόγιο θα εμφανίζεται');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step9.jpg"/></p>
              <p><?php echo gks_lang('π.χ.');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step10.jpg"/></p>
              <p><?php echo gks_lang('Όταν προσθέτετε ένα νέο συμβάν, εάν θέλετε να καταχωρηθεί στο συγκεκριμένο ημερολόγιο, θα πρέπει να το επιλέξετε:');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/Android/CalDav/step11.jpg"/></p>
            </div>
          </div>
          <?php } ?>
  

          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <?php echo gks_lang('Ρυθμίσεις για iPhone');?>
            </div>
          </div>
          
          <?php if ($perm_ret_dav_card['success']) {?>  
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;font-size:0.9rem;">
              <span class="btn btn-sm btn-primary" id="iphone_carddav"><?php echo gks_lang('CardDAV - Επαφές - Οδηγίες');?></span>
            </div>
          </div>
          <div class="form-group row" id="iphone_carddav_div" style="display:none;">
            <div class="odigies col-md-12" style="font-size:0.8rem;">
              
              <p><?php echo gks_lang('Ανοίξτε την εφαρμογή <b>Ρυθμίσεις</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/settings.png" style="width:100px;"/></p>
              <p><?php echo gks_lang('Θα πρέπει να προσθέσετε έναν νέο λογαριασμό στις Επαφές');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step1.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Προσθήκη λογαριασμού</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step2.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Άλλος</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step3.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Προσθήκη λογαριασμού CardDav</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step4.png"/></p>
              
              <p><?php echo gks_lang('Στο πεδίο <b>Διακομιστής</b> εισάγετε το:');?><br>
              <b><?php echo GKS_SITE_URL;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Όνομα χρήστη</b> εισάγετε το:');?><br>
              <b><?php echo $my_wp_user_info->user_login;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Συνθηματικό</b> εισάγετε το:');?><br>
              <b><?php echo $gks_user_settings['dav']['password'];?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Περιγραφή</b> ορίστε ένα όνομα για αυτόν τον λογαριασμό');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>Επόμενο</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step5.png"/></p>
              
              
              <p><?php echo gks_lang('Ο λογαριασμός θα προστεθεί');?></p>
              <p><?php echo gks_lang('Ανοίξτε την εφαρμογή <b>Επαφές</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/contacts.png" style="width:100px;"/></p>  
              <p><?php echo gks_lang('Κάντε κλικ στο κουμπί <b>Ομάδες</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step6.png"/></p>
              <p><?php echo gks_lang('Βεβαιωθείτε ότι η νέα ομάδα επαφών εμφανίζεται και είναι ενεργή');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step7.png"/></p>
              
               
            </div>
          </div>
          <?php } ?>

          <?php if ($perm_ret_dav_cal['success']) {?> 
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;font-size:0.9rem;">
              <span class="btn btn-sm btn-primary" id="iphone_caldav"><?php echo gks_lang('CalDAV - Ημερολόγιο - Οδηγίες');?></span>
            </div>
          </div>
          <div class="form-group row" id="iphone_caldav_div" style="display:none;">
            <div class="odigies col-md-12" style="font-size:0.8rem;">

              <p>Ανοίξτε την εφαρμογή <b><?php echo gks_lang('Ρυθμίσεις');?></b></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/settings.png" style="width:100px;"/></p>
              <p><?php echo gks_lang('Θα πρέπει να προσθέσετε έναν νέο λογαριασμό στo Ημερολόγιο');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step1.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Προσθήκη λογαριασμού</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step2.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Άλλος</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CardDav/step3.png"/></p>
              <p><?php echo gks_lang('Επιλέγουμε <b>Προσθήκη λογαριασμού CalDav</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step4.png"/></p>
              
              <p><?php echo gks_lang('Στο πεδίο <b>Διακομιστής</b> εισάγετε το:');?><br>
              <b><?php echo GKS_SITE_URL;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Όνομα χρήστη</b> εισάγετε το:');?><br>
              <b><?php echo $my_wp_user_info->user_login;?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Συνθηματικό</b> εισάγετε το:');?><br>
              <b><?php echo $gks_user_settings['dav']['password'];?></b><br>
              <?php echo gks_lang('Στο πεδίο <b>Περιγραφή</b> ορίστε ένα όνομα για αυτόν τον λογαριασμό');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>Επόμενο</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step5.png"/></p>
              <p><?php echo gks_lang('Ενεργοποιήστε την επιλογή <b>Ημερολόγια</b>');?><br>
              <?php echo gks_lang('Απενεργοποιήστε την επιλογή <b>Υπομνήσεις</b>');?><br>
              <?php echo gks_lang('Κάντε κλικ στο κουμπί <b>Αποθήκευση</b>');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step6.png"/></p>
              <p><?php echo gks_lang('Ο λογαριασμός θα προστεθεί');?></p>
              
              
              <p><?php echo gks_lang('Ανοίξτε την εφαρμογή <b>Ημερολόγιο</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/calendar.png" style="width:100px;"/></p>  
              <p><?php echo gks_lang('Κάντε κλικ στο κουμπί <b>Ημερολόγια</b>');?></p> 
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step7.png"/></p>
              
              <p><?php echo gks_lang('Βεβαιωθείτε ότι το νέο ημερολόγιο εμφανίζεται και είναι ενεργό');?></p> 
              
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step8.png"/></p>
              <p><?php echo gks_lang('Όταν προσθέτετε ένα νέο συμβάν, εάν θέλετε να καταχωρηθεί στο συγκεκριμένο ημερολόγιο, θα πρέπει να το επιλέξετε:');?></p>
              <p style="text-align:center"><img class="img-fluid" src="img/iPhone/CalDav/step9.png"/></p>

            </div>
          </div> 
          <?php } ?>
        
        </div>
      </div>
      
      <?php } ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('email');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('email');?>>       

          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <?php echo gks_lang('Ρυθμίσεις για Android');?>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;font-size:0.9rem;">
              <span class="btn btn-sm btn-primary" id="gks_android_gmail"><?php echo gks_lang('Gmail app - Οδηγίες');?></span>
            </div>
          </div>
          <div class="form-group row" id="gks_android_gmail_div" style="display:none;">
            <div class="odigies col-md-12" style="font-size:0.8rem;">

              <p><?php echo gks_lang('Για να βλέπουμε και να στέλνουμε email από το κινητό μας, όταν έχουμε λογαριασμό στον διακομιστή');?> <strong>www.gks.gr</strong> <?php echo gks_lang('οι ρυθμίσεις είναι οι παρακάτω:');?></p>
              
              <p><?php echo gks_lang('Έστω ότι το email μας είναι το <strong>kostas@ksgks.gr</strong> και γνωρίζουμε τον κωδικό πρόσβασης');?></p>
              
              <p><?php echo gks_lang('Ανοίγουμε την εφαρμογή <strong>Gmail</strong> στο κινητό μας:');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gmail_app.jpg" /></p>
              
              <p><?php echo gks_lang('Στην εφαρμογή πιθανόν να έχουμε ήδη έναν λογαριασμό email τύπου gmail');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο εικονίδιό μας');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step1.jpg" /></p>
              <p><?php echo gks_lang('Επιλέγουμε την εντολή <strong>Προσθήκη άλλου λογαριασμού</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step2.jpg" /></p>
              
              
              <p><?php echo gks_lang('Επιλέγουμε <strong>Άλλος</strong> ως τύπο');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step3.jpg" /></p>
              
              <p><?php echo gks_lang('Πληκτρολογούμε το email μας');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο <strong>ΜΗ ΑΥΤΟΜΑΤΗ ΡΥΘΜΙΣΗ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step6.jpg" /></p>
              
              <p><?php echo gks_lang('Επιλέγουμε ως τύπο λογαριασμού το <strong>IMAP</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step7.jpg" /></p>
              <p><?php echo gks_lang('Πληκτρολογούμε τον κωδικό του email μας');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο κουμπί <strong>ΕΠΟΜΕΝΟ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step8.jpg" /></p>
              
              <p><?php echo gks_lang('Στο πεδίο <strong>Διακομιστής </strong>πληκτρολογούμε το <strong>www.gks.gr</strong>');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο κουμπί <strong>ΕΠΟΜΕΝΟ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step9.jpg" /></p>
              
              <p><?php echo gks_lang('Η επιλογή <strong>Να απαιτείται σύνδεση</strong> να είνα ενεργοποιημένη');?></p>
              <p><?php echo gks_lang('Στο πεδίο <strong>Διακομιστής SMTP</strong> πληκτρολογούμε το <strong>www.gks.gr</strong>');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο κουμπί <strong>ΕΠΟΜΕΝΟ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step11.jpg" /></p>
              
              
              <p><?php echo gks_lang('Ρυθμίζουμε κάθε πότε θέλουμε να γίνεται έλεγχος για νέα emails. Τα 15 λεπτά είναι μια καλή επιλογή');?></p>
              <p><?php echo gks_lang('Οι επόμενες 3 επιλογές να είναι ενεργοποιημένες');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο κουμπί <strong>ΕΠΟΜΕΝΟ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step12.jpg" /></p>
              
              <p><?php echo gks_lang('Πληκτρολογούμε το όνομά μας');?></p>
              <p><?php echo gks_lang('Κάνουμε κλικ στο κουμπί <strong>ΕΠΟΜΕΝΟ</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step13.jpg" /></p>
              
              <p><?php echo gks_lang('Ο λογαριασμός έχει προστεθεί');?></p>
              <p><?php echo gks_lang('Για να δούμε τα emails από αυτόν τον λογαριασμό, από το εικονίδιο που βρίσκεται επάνω αριστερά, επιλέγω τον νέο λογαριασμό που μόλις πρόσθεσα');?></p>
              <p><?php echo gks_lang('Αυτός είναι άλλωστε και ο τρόπος για την εναλλαγή μεταξύ των λογαριασμών');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step14.jpg" /></p>
              
              <p><?php echo gks_lang('Η εφαρμογή Gmail έχει την δυνατότητα να βλέπουμε όλα τα εισερχόμενα από όλους τους λογαριασμούς');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step23.jpg" /></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step24.jpg" /></p>
              
              
              <p><?php echo gks_lang('Όταν θέλω να στείλω ένα email από αυτόν τον λογαριασμό, θα πρέπει να τον επιλέξω');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step15.jpg" /></p>
              
              <p><?php echo gks_lang('Για να δοκιμάστε εάν μπορείτε να στείλετε και να λάβετε emails μέσω αυτού του λογαριασμού, απλά στείλτε ένα email στο ίδιο λογαριασμό,');?> 
              <br><?php echo gks_lang('π.χ. από <strong>kostas@ksgks.gr</strong> προς <strong>kostas@ksgks.gr</strong>');?></p>
              
              <p><?php echo gks_lang('Εάν εμφανιστεί κάποιο πρόβλημα κατά την αποστολή ή λήψη των emails, βεβαιωθείτε ότι οι ρυθμίσεις του λογαριασμού είναι οι παρακάτω');?></p>
              <p><?php echo gks_lang('Για να δούμε τις ρυθμίσεις του συγκεκριμένου λογαριασμού, θα πρέπει πρώτα να μεταβούμε στις ρυθμίσεις των λογαριασμών');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step16.jpg" /></p>
              <p><?php echo gks_lang('Να επιλέξουμε τον συγκεκριμένο λογαριασμό');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step17.jpg" /></p>
              <p><?php echo gks_lang('Επιλέγουμε <strong>Ρυθμίσεις λογαριασμού</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step18.jpg" /></p>
              
              
              <p><?php echo gks_lang('Για να δούμε τις ρυθμίσεις των εισερχομένων, κάνουμε κλικ στη αντίστοιχη επιλογή');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step19.jpg" /></p>
              <p><?php echo gks_lang('Ο <strong>Διακομιστής </strong>θα πρέπει να είναι ο <strong>www.gks.gr</strong>');?></p>
              <p><?php echo gks_lang('Η <strong>Θύρα </strong>θα πρέπει να είναι η <strong>993</strong>');?></p>
              <p><?php echo gks_lang('Ο <strong>Τύπος ασφάλειας</strong> θα πρέπει να είναι ο <strong>SSL/TLS</strong>');?></p>
              
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step20.jpg" /></p>
              
              <p><?php echo gks_lang('Για να δούμε τις ρυθμίσεις των εξερχομένων, κάνουμε κλικ στη αντίστοιχη επιλογή');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step19b.jpg" /></p>
              <p><?php echo gks_lang('Η επιλογή <strong>Να απαιτείται σύνδεση</strong> να είνα ενεργοποιημένη');?></p>
              <p><?php echo gks_lang('Ο <strong>Διακομιστής SMTP</strong>θα πρέπει να είναι ο <strong>www.gks.gr</strong>');?></p>
              <p><?php echo gks_lang('Η <strong>Θύρα </strong>θα πρέπει να είναι η <strong>465</strong>');?></p>
              <p><?php echo gks_lang('Ο <strong>Τύπος ασφάλειας</strong> θα πρέπει να είναι ο <strong>SSL/TLS</strong>');?></p>
              <p style="text-align: center;"><img src="/my/img/gks_email_gmail/gks_gmail_step21.jpg" /></p>
              

            </div>
          </div>

        </div>
      </div>
      
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πρότυπα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('templates');?>><?php
          
          
          
          $query = "SELECT * from gks_users_templates WHERE user_id=".$my_wp_user_id." ORDER BY object_name desc,template_name";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="templates_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('Αντικείμενο');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><?php echo gks_lang('Έγγραφο');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%"><?php echo gks_lang('Όνομα');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><?php echo gks_lang('Εκτέλεση');?></th>        

            </tr>
          </thead>
          <tbody> 
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr id="tr_templates_<?php echo $row_list['id_users_template'];?>">
              <th scope="row" nowrap  class="mytdcm templates_aa"><?php echo ($i);?></td>       
              <td class="mytdcm" nowrap>
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_users_template'];?>" data-deleteafter="gks_fnc_templates_delete_after|<?php echo $row_list['id_users_template'];?>" data-model="gks_users_templates" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

              </td>
              <?php
              $temp1='';
              $temp2='';
              $temp3='';
              $temp4='';
              
              switch ($row_list['object_name']) {   
                case 'gks_acc_inv':       
                  $temp1=gks_lang('Παραστατικό');
                  $temp2='admin-acc-inv-item.php?id='.$row_list['template_id'];
                  $temp3='admin-acc-inv-item.php?id=-1&template_id='.$row_list['template_id'];
                  break;  
                case 'gks_acc_pay':       
                  $temp1=gks_lang('Πληρωμή');
                  $temp2='admin-acc-pay-item.php?id='.$row_list['template_id'];
                  $temp3='admin-acc-pay-item.php?id=-1&template_id='.$row_list['template_id'];
                  break;  
                case 'gks_whi_mov':       
                  $temp1=gks_lang('Δελτίο Αποστολής');
                  $temp2='admin-whi-mov-item.php?id='.$row_list['template_id'];
                  $temp3='admin-whi-mov-item.php?id=-1&template_id='.$row_list['template_id'];
                  break;  
                case 'gks_orders':       
                  $temp1=gks_lang('Παραγγελία');
                  $temp2='admin-orders-item.php?id='.$row_list['template_id'];
                  $temp3='admin-orders-item.php?id=-1&template_id='.$row_list['template_id'];
                  break;  
                default:
                
              }
              ?>
              <td class="mytdcml" nowrap><?php echo $temp1;?></td>  
              <td class="mytdcm" nowrap><?php echo '<a href="'.$temp2.'">'.$row_list['template_id'].'</a>';?></td>  
              <td class="mytdcml" ><?php echo $row_list['template_name'];?></td>
              <td class="mytdcm" nowrap>
                <a href="<?php echo $temp3;?>" 
                  class="btn btn-sm btn-primary"><i class="fas fa-copy" style="font-size: 100%;margin-bottom:0px;"></i></a>
              </td>  

              
            </tr>
          <?php } ?>


      
          </tbody>
          </table> 
          
          <div class="alert alert-warning" role="alert" style="margin-top: 20px;display:none;" id="templates_alert">
            <?php echo gks_lang('Το μενού θα ενημερωθεί στην επόμενη σελίδα που θα επισκεφθείτε');?>
          </div>
        </div>
      </div>

      <div class="card gks_card_expand" id="custom_css">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Το δικό μου CSS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('css');?>>
          
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <?php
              $custom_css_user_val='';
              $sql="select myvalue from gks_settings_users where user_id=".$my_wp_user_id." and myobject='css' and mysubobject='user'";
              $result_select = $db_link->query($sql);
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('error sql');} 
              if ($result_select->num_rows==1) {
                $row_select = $result_select->fetch_assoc();
                $custom_css_user_val=trim_gks($row_select['myvalue']);
              }
              ?>               
              <textarea id="custom_css_user" style="border:1px solid black;"><?php echo $custom_css_user_val;?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
              <small class="form-text text-muted"><?php echo gks_lang('Αυτή η ρύθμιση αφορά τις δικές προσαρμογές.<br>Εάν θέλετε να ρυθμίσετε να ορίσετε τις προσαρμογές που αφορούν όλους τους χρήστες μεταβείτε στις <a href="admin-system-settings.php#custom_css">Ρυθμίσεις Εφαρμογής.</a>');?></small>
            </div>
          </div>
        
        </div>      
      </div>
      
      
            
    </div>
  </div>
</div>


<?php 
//var_dump($my_wp_user_info->user_login);
?>



<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button style="justify-content: center!important;" type="button" class="btn btn-danger submit_button_back" id="goback"><?php echo gks_lang('Ακύρωση');?></button>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>






              

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var need_save=false;
var gks_page_loading=true;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','delete',0);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  }); 

  voip_extension_def=gks_getCookie('voip_extension_def');
  if (voip_extension_def==null || voip_extension_def=='') {
    voip_extension_def='';
    if (from_php_gks_voip_params.extensions.length==1) voip_extension_def=from_php_gks_voip_params.extensions[0];
  }
  //console.log(voip_extension_def);
  $('#voip_extension_def').val(voip_extension_def);

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});  
  function mysubmit() {

    

    
    datasend='';
    
    
    if ($("#gks_whi_mov_tropos_apostolis").length>0) datasend+='&gks_whi_mov_tropos_apostolis='  + encodeURIComponent($("#gks_whi_mov_tropos_apostolis").val().trim());
    if ($("#gks_orders_tropos_apostolis").length>0)  datasend+='&gks_orders_tropos_apostolis='   + encodeURIComponent($("#gks_orders_tropos_apostolis").val().trim());
    if ($("#gks_orders_tropos_pliromis").length>0)   datasend+='&gks_orders_tropos_pliromis='    + encodeURIComponent($("#gks_orders_tropos_pliromis").val().trim());
    if ($("#gks_acc_inv_tropos_apostolis").length>0) datasend+='&gks_acc_inv_tropos_apostolis='  + encodeURIComponent($("#gks_acc_inv_tropos_apostolis").val().trim());
    if ($("#gks_acc_inv_tropos_pliromis").length>0)  datasend+='&gks_acc_inv_tropos_pliromis='   + encodeURIComponent($("#gks_acc_inv_tropos_pliromis").val().trim());
    
    datasend+='&gks_crm_tasks_def_duration='   + encodeURIComponent($.base64.encode($("#gks_crm_tasks_def_duration").val().trim()));
    
    
    if ($('#file_type_pdf').prop('checked'))       datasend+='&file_type=' + encodeURIComponent($.base64.encode('pdf'));
    else if ($('#file_type_html').prop('checked')) datasend+='&file_type=' + encodeURIComponent($.base64.encode('html'));
    else if ($('#file_type_jpg').prop('checked'))  datasend+='&file_type=' + encodeURIComponent($.base64.encode('jpg'));
    else datasend+='&file_type=';
    
    datasend+='&is_landscape='  + ($('#is_landscape_on').prop('checked') ? '1' : '0');
    datasend+='&grayscale='  + ($('#grayscale_on').prop('checked') ? '1' : '0');
    zoom_slider=parseInt($('#zoom_slider').slider('value'));
    if (isNaN(zoom_slider)) zoom_slider=100;
    datasend+='&zoom='  + encodeURIComponent(zoom_slider);
    
    
    if ($("#print_form_id_order").length>0) datasend+='&print_form_id_order=' + encodeURIComponent($("#print_form_id_order").val().trim());
    if ($("#print_form_id_inv").length>0)   datasend+='&print_form_id_inv='   + encodeURIComponent($("#print_form_id_inv").val().trim());
    if ($("#print_form_id_pay").length>0)   datasend+='&print_form_id_pay='   + encodeURIComponent($("#print_form_id_pay").val().trim());
    if ($("#print_form_id_whi").length>0)   datasend+='&print_form_id_whi='   + encodeURIComponent($("#print_form_id_whi").val().trim());

    
    
    datasend+='&user_lang_backend=' + $('input[name=user_lang_backend]:checked').val();
    datasend+='&autocomplete_address=' + $('input[name=autocomplete_address]:checked').val();
    //console.log(datasend);
    

    datasend+='&menu_pos='  + ($('#menu_pos_top').prop('checked') ? '' : 'left');
    datasend+='&menu_sticky_top='  + ($('#menu_sticky_top').prop('checked') ? '1' : '0');
    datasend+='&menu_hover='  + ($('#menu_hover').prop('checked') ? '1' : '0');
    
    datasend+='&htmlcss_font_family=' + encodeURIComponent($.base64.encode($('#htmlcss_font_family').val()));
    datasend+='&htmlcss_font_size=' + encodeURIComponent($.base64.encode($('#htmlcss_font_size').val()));
    
    datasend+='&voip_extensions='  + encodeURIComponent($.base64.encode($("#voip_extensions").val().trim()));
    datasend+='&voip_extension_def='  + encodeURIComponent($.base64.encode($("#voip_extension_def").val().trim()));


    if ($("#dav_password").length>0) datasend+='&dav_password='  + encodeURIComponent($.base64.encode($("#dav_password").val().trim()));
    
    

    if ($('#enter_order_gks_whi_mov').length>0) datasend+='&enter_order_gks_whi_mov_str=' + encodeURIComponent($.base64.encode(JSON.stringify($('#enter_order_gks_whi_mov').sortable('toArray'))));
    if ($('#enter_order_paraggelia').length>0)  datasend+='&enter_order_paraggelia_str=' + encodeURIComponent($.base64.encode(JSON.stringify($('#enter_order_paraggelia').sortable('toArray'))));
    if ($('#enter_order_parastatiko').length>0) datasend+='&enter_order_parastatiko_str=' + encodeURIComponent($.base64.encode(JSON.stringify($('#enter_order_parastatiko').sortable('toArray'))));


    notif=[];
    $('.notif_user_item').each(function() {
      nid=$(this).attr('data-nid');
      item={};
      item.nid=nid;
      item.user=($(this).is(':checked') ? '1':'0');
      item.email=($('.notif_to_email_item[data-nid=' + nid + ']').is(':checked') ? '1':'0');
      item.viber=($('.notif_to_viber_item[data-nid=' + nid + ']').is(':checked') ? '1':'0');
      
      notif.push(item);
    });
    datasend+='&notif=' + encodeURIComponent($.base64.encode(JSON.stringify(notif)));

    datasend+='&custom_css_user=' + encodeURIComponent($.base64.encode(custom_css_user_editor.getValue()));

<?php if ($GKS_CRM_TASKS_ENABLE) { ?>
    datasend+='&erp_app_id_check=' + (($('#erp_app_id_check').is(':checked')) ? '1':'0');
    datasend+='&erp_app_id=' + encodeURIComponent(($("#mypostform #erp_app_id").val().trim()));
    datasend+='&erp_app_dest=' + encodeURIComponent($.base64.encode($('input[name=erp_app_dest]:checked').val()));
    datasend+='&erp_app_dest_printer='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer").val().trim()));
    datasend+='&erp_app_dest_printer_method='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_method").val().trim()));
    datasend+='&erp_app_dest_printer_lpr_ip='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer_lpr_ip").val().trim()));
    datasend+='&erp_app_dest_printer_copies='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_copies").val().trim()));
    datasend+='&erp_app_dest_folder='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_folder").val().trim()));
<?php } ?>



    //console.log(custom_css_user_editor.getValue());


    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-user-settings-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					need_save=false;
  					gks_setCookie('voip_extension_def',data.voip_extension_def);
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }   

  $('#goback').click(function() {
    window.location.href='/my';  
  });

  var myswitchery = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
  myswitchery.forEach(function(html) {
    var switchery = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });    
  
  
  var elem_menu_sticky_top=document.querySelector('.switchery_menu_sticky_top');
  var switchery_menu_sticky_top = new Switchery(elem_menu_sticky_top,gks_switchery_defaults());
  
 
  
  $('input[name=file_type]').change(function() {
    need_save=true; 
  });
  $('input[name=is_landscape]').change(function() {
    need_save=true; 
  });
  $('input[name=grayscale]').change(function() {
    need_save=true; 
  });
  
  
  var zoom_slider_handle = $('#zoom_slider_handle');
  $('#zoom_slider').slider({
    min: 10,
    max: 200,
    value: <?php echo intval($gks_user_settings['print']['zoom']);?>,
    create: function() {
      zoom_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      zoom_slider_handle.text( ui.value + '%' );
      need_save=true;  
    }
  });


  $('#enter_order_gks_whi_mov').sortable({
    placeholder: 'gks_jui_sortable_item_highlight',
    change: function(event,ui) {
      need_save=true;  
    },
  });
  $('#enter_order_gks_whi_mov').disableSelection();
       
  

  $('#enter_order_paraggelia').sortable({
    placeholder: 'gks_jui_sortable_item_highlight',
    change: function(event,ui) {
      need_save=true;  
    },
  });
  $('#enter_order_paraggelia').disableSelection();
       
  $('#enter_order_parastatiko').sortable({
    placeholder: 'gks_jui_sortable_item_highlight',
    change: function(event,ui) {
      need_save=true;  
    },
  });
  $('#enter_order_parastatiko').disableSelection();
  
  
  $('#android_carddav').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα τις ρυθμίσεις'));
      return;
    }
    if ($('#android_carddav_div').is(":visible")) {
      $('#android_carddav_div').slideUp();
    } else {
      $('#android_carddav_div').slideDown();
    }  
  });
  $('#android_caldav').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα τις ρυθμίσεις'));
      return;
    }
    if ($('#android_caldav_div').is(":visible")) {
      $('#android_caldav_div').slideUp();
    } else {
      $('#android_caldav_div').slideDown();
    }  
  });
  $('#iphone_carddav').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα τις ρυθμίσεις'));
      return;
    }
    if ($('#iphone_carddav_div').is(":visible")) {
      $('#iphone_carddav_div').slideUp();
    } else {
      $('#iphone_carddav_div').slideDown();
    }  
  });  
  $('#iphone_caldav').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα τις ρυθμίσεις'));
      return;
    }
    if ($('#iphone_caldav_div').is(":visible")) {
      $('#iphone_caldav_div').slideUp();
    } else {
      $('#iphone_caldav_div').slideDown();
    }  
  });  
  $('#gks_android_gmail').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα τις ρυθμίσεις'));
      return;
    }
    if ($('#gks_android_gmail_div').is(":visible")) {
      $('#gks_android_gmail_div').slideUp();
    } else {
      $('#gks_android_gmail_div').slideDown();
    }  
  });  
  
  
  window.gks_fnc_templates_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('#tr_templates_' + myargs[0]).hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var links_aa=0;
      $('#templates_table .templates_aa').each(function () {
        links_aa++;
        $(this).html(links_aa);  
      });    
    });
    $('#templates_alert').show('fade', {}, 500,);
  }   
  
  
  notif_user_item_array=[];
  var notif_user_item = Array.prototype.slice.call(document.querySelectorAll('.notif_user_item'));
  notif_user_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_user_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });  
  notif_to_email_item_array=[];
  var notif_to_email_item = Array.prototype.slice.call(document.querySelectorAll('.notif_to_email_item'));
  notif_to_email_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_to_email_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });  
  notif_to_viber_item_array=[];
  var notif_to_viber_item = Array.prototype.slice.call(document.querySelectorAll('.notif_to_viber_item'));
  notif_to_viber_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_to_viber_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });  
  
  
  $('.notif_all').click(function() {
    $('.notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_none').click(function() {
    $('.notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });

  $('.notif_user_all').click(function() {
    $('.notif_user_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_user_none').click(function() {
    $('.notif_user_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  

  $('.notif_to_email_all').click(function() {
    $('.notif_to_email_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_to_email_none').click(function() {
    $('.notif_to_email_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  
  $('.notif_to_viber_all').click(function() {
    $('.notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_to_viber_none').click(function() {
    $('.notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  
  
//  $('input[name=menu_pos]').change(function() {
//    if ($('#menu_pos_top').prop('checked')) {
//      switchery_menu_sticky_top.enable();
//    } else {
//      switchery_menu_sticky_top.disable();
//    }
//    //console.log('menu_pos');  
//  });
  



  var custom_css_user_editor = CodeMirror.fromTextArea(document.getElementById("custom_css_user"), {
    lineNumbers: true,
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
      },
      "F11": function(cm) {
        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
      },
      "Esc": function(cm) {
        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
      }      
    },
    mode: {name: "css",globalVars: true }, //
    lineNumbers: true,
    spellcheck: true,
    autocorrect: true,
    autocapitalize: true,
    indentUnit:2,
    tabSize: 2,
    indentWithTabs:false,
    smartIndent:true,
    autoCloseBrackets: true,
    styleActiveLine: true,
    lineWrapping: true,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    //viewportMargin: 20, //Infinity
  });
  

  $('#erp_app_id').change(function() {
    
    erp_app_id=parseInt($('#erp_app_id').val());
    if (isNaN(erp_app_id)) erp_app_id=0;
    $('#erp_app_dest_printer option').each(function() { 
      if ($(this).text() !='') {
        $(this).remove();
      }
    });
    
    if (erp_app_id>0) {
      local_printers=$('#erp_app_id option:selected').attr('data-local-printers').trim();
      //console.log(local_printers);
      if (local_printers!='') {
        local_printers=JSON.parse($.base64.decode(local_printers));
        //console.log(local_printers);
        for(i=0; i<local_printers.length;i++) {
          $('#erp_app_dest_printer').append('<option>' + local_printers[i] + '</option>');
        }
      }
    }
    
  });
  
  $('#erp_app_id_check').change(erp_app_dest_visible);
  $('input[name=erp_app_dest]').change(erp_app_dest_visible);
  $('#erp_app_dest_printer_method').change(erp_app_dest_visible);
  
  function erp_app_dest_visible() {
    need_save=true;
    if ($('#erp_app_id_check').is(':checked')) {
      $('.div_erp_app_id_check_only').slideDown();
      val=$('input[name=erp_app_dest]:checked').val();
      if (val=='printer') {
        $('.div_erp_app_id_check_printer').slideDown();
        $('.div_erp_app_id_check_folder').slideUp(); 
        erp_app_dest_printer_method = $('#erp_app_dest_printer_method').val();
        if (erp_app_dest_printer_method==2) { //2 lpr
          $('.div_erp_app_id_check_printer_id01').slideUp();
          $('.div_erp_app_id_check_printer_id2').slideDown();
          $('.div_erp_app_id_check_printer_id3').slideUp();
        } else if (erp_app_dest_printer_method==3) { //3 html
          $('.div_erp_app_id_check_printer_id01').slideUp();
          $('.div_erp_app_id_check_printer_id2').slideUp();
          $('.div_erp_app_id_check_printer_id3').slideDown();
          
          
        } else { //0 PDFium (pdf), 1 Adobe Acrobat Reader 
          $('.div_erp_app_id_check_printer_id01').slideDown();
          $('.div_erp_app_id_check_printer_id2').slideUp();
          $('.div_erp_app_id_check_printer_id3').slideUp();
          
        }
      } else if (val=='folder') {
        $('.div_erp_app_id_check_printer').slideUp();
        $('.div_erp_app_id_check_printer_id01').slideUp();
        $('.div_erp_app_id_check_printer_id2').slideUp();
        $('.div_erp_app_id_check_printer_id3').slideUp();         
        $('.div_erp_app_id_check_folder').slideDown(); 
      }
    } else {
      $('.div_erp_app_id_check').slideUp();
      $('.div_erp_app_id_check_only').slideUp();
    }
  }


  $('#gks_crm_tasks_def_duration').TimePickerAlone({mask:'29:59',dragAndDrop:true,mouseWheel:true,twelveHoursFormat:false,seconds:false,ampm:false,saveOnChange:true,defaultTime:'',inputFormat:'HH:mm',onChange:
    function(ct,$i){
      need_save=true;
    }
  });
  
    
  
  //generic
  gks_page_loading=false;
  

  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });
  
  window.onbeforeunload = function() {
    //if (from_php_is_acc_inv_manager==false) return;
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };
    
  need_save=false;
});
</script>

<link rel="stylesheet" href="/my/js/codemirror-5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/hint/show-hint.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/fold/foldgutter.css" />
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/display/fullscreen.css">
<script src="/my/js/codemirror-5.65.16/lib/codemirror.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/css/css.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/show-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/css-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/edit/closebrackets.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/selection/active-line.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldcode.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldgutter.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/brace-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/comment-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/display/fullscreen.js"></script>


  
<?php
//db_close();
include_once('_my_footer_admin.php');


