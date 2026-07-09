<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','accounting_seires');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_seires',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_seires',['from'=>'item']);




if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_acc_seira']=-1;
  $row['acc_journal_id']=0; if (isset($_GET['acc_journal_id'])) $row['acc_journal_id']=intval($_GET['acc_journal_id']);
  $row['seira_code']='';
  $row['seira_descr']='';
  $row['seira_comments']='';
  $row['prefix']='';
  $row['suffix']='';
  $row['number_size']=6;
  $row['number_step']=1;
  $row['next_number']=1;
  $row['sortorder']=1000;
  $row['send_mydata']=0;
  $row['send_paroxos']=0;
  $row['seira_need_signature']=0;
  $row['seira_isdeliverynote']=0;
  $row['seira_is_reverse_delivery_note']=0;
  $row['seira_is_self_pricing']=0;
  $row['seira_is_vat_payment_suspension']=0;
  
  
  $row['aade_lock_send_numbers']=0;
  $row['is_xeirografi']=0;
  $row['is_disable']=0;
  
  
  $row['erp_app_id']=0;
  $row['erp_app_dest']='printer';
  $row['erp_app_dest_printer_method']=1;
  $row['erp_app_dest_printer']='';
  $row['erp_app_dest_printer_lpr_ip']='';
  $row['erp_app_dest_printer_copies']=1;
  $row['erp_app_dest_folder']='';
  $row['erp_app_filter']='';

  $row['acc_eidos_parastatikou_whi_id']=0;
  
  $my_page_title=gks_lang('Νέα Σειρά');

  
  

} else {
  $sql ="SELECT gks_acc_seires.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  gks_acc_journal.acc_journal_descr, 
  gks_acc_journal.company_id, gks_company.company_title, 
  gks_acc_journal.company_sub_id, gks_company_subs.company_sub_title, 
  gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_journal.acc_eidos_parastatikou_whi_id,
  gks_acc_eidi_parastatikon.eidos_parastatikou_descr

  FROM (((((gks_acc_seires 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_seires.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_seires.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou

  where id_acc_seira = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Σειρά').': '.$row['seira_descr'];
  $object_title=$row['seira_descr'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$lang_data_obj=gks_lang_data_obj_prepare('gks_acc_seires','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>
<style>

#send_mydata1, #send_paroxos1, .gks_payacq_span {
  font-size: 0.875rem;vertical-align: middle;
}
.div_gks_payacq_sw {
  padding:4px 0px;  
}  
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Σειρά');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Σειρά');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σειρά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 

          <div class="form-group row">
            <label for="acc_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <select id="acc_journal_id" class="form-control form-control-sm myneedsave">
                <option value="0" data-id_acc_eidos="0" data-aade_code="" data-whi_id="0"></option>
                <?php
                $sql="SELECT gks_acc_journal.id_acc_journal, 
                gks_acc_journal.acc_journal_descr, 
                gks_acc_journal.company_id, 
                gks_acc_journal.company_sub_id, 
                gks_acc_journal.acc_eidos_parastatikou_whi_id,
                gks_company.company_title, 
                gks_company_subs.company_sub_title, 
                gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,
                gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code
                
                FROM ((gks_acc_journal LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
                LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub) 
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                ORDER BY gks_company.company_sortorder, 
                gks_company.company_title, 
                gks_company_subs.company_sub_sortorder,
                gks_company_subs.company_sub_title, 
                gks_acc_journal.acc_journal_descr;";

                $id_acc_eidos_parastatikou=0;
                $eidos_parastatikou_aade_code='';
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_journal'].'" 
                  data-id_acc_eidos="'.trim_gks($row_select['id_acc_eidos_parastatikou']).'"
                  data-aade_code="'.trim_gks($row_select['eidos_parastatikou_aade_code']).'"
                  data-whi_id="'.intval($row_select['acc_eidos_parastatikou_whi_id']).'"
                  ';
                  if ($row_select['id_acc_journal']==$row['acc_journal_id']) {
                    echo ' selected ';
                    $id_acc_eidos_parastatikou=intval($row_select['id_acc_eidos_parastatikou']);
                    $eidos_parastatikou_aade_code=trim_gks($row_select['eidos_parastatikou_aade_code']);
                  }
                  echo '>'.$row_select['company_title'].($row_select['company_sub_id']==0 ? '/'.gks_lang('Κεντρικό') : '/'.$row_select['company_sub_title']).' | '.$row_select['acc_journal_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 

          <div class="form-group row">
            <label for="seira_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="seira_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['seira_code']);?>" placeholder="<?php echo gks_lang('π.χ. Α');?>">
            </div>
          </div>                    
          <div class="form-group row">
            <label for="seira_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="seira_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['seira_descr']);?>" placeholder="<?php echo gks_lang('π.χ. Σειρά Α');?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('seira_descr'));
          ?>
          <div class="form-group row">
            <label for="seira_comments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="seira_comments" rows="5" class="form-control form-control-sm myneedsave" ><?php echo $row['seira_comments'];?></textarea>
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('seira_comments'));
          ?>          
          <div class="form-group row">
            <label for="prefix" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρόθεμα');?>:</label>
            <div class="col-md-8">
              <input id="prefix" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['prefix']);?>" placeholder="<?php echo gks_lang('π.χ. Τιμ/#');?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="suffix" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επίθεμα');?>:</label>
            <div class="col-md-8">
              <input id="suffix" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['suffix']);?>" placeholder="<?php echo gks_lang('π.χ. #');?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="number_size" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος ψηφίων');?>:</label>
            <div class="col-md-8">
              <input id="number_size" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['number_size'];?>" placeholder="<?php echo gks_lang('π.χ. 6');?>" min="0" max="10" step="1" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="number_step" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βήμα');?>:</label>
            <div class="col-md-8">
              <input id="number_step" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['number_step'];?>" placeholder="<?php echo gks_lang('π.χ. 1');?>" min="1" step="1" style="max-width:200px">
            </div>
          </div> 

          <div class="form-group row">
            <label for="next_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επόμενος Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="next_number" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['next_number'];?>" placeholder="<?php echo gks_lang('π.χ. 1');?>" min="1" step="1" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="sample_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δείγμα επόμενου αριθμού');?>:</label>
            <div class="col-md-8">
              <span id="sample_number" style="font-size: 0.875rem;font-weight: bold;"></span>
            </div>
          </div> 

          <div class="form-group row">
            <label for="sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['sortorder'];?>" min="0" step="1" style="max-width:200px">
            </div>
          </div>

          <div class="form-group row">
            <label for="send_mydata" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή σε myData');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="send_mydata" value="1" <?php if ($row['send_mydata']!=0) echo ' checked '; ?> <?php if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) echo ' disabled '; ?>>
              <span id="send_mydata1" style="<?php if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) echo 'display:none;'?>"><?php echo gks_lang('Κωδικός αποστολής myData');?>: <span id="send_mydata2"><?php echo (in_array($id_acc_eidos_parastatikou,[702,703,704]) ? gks_lang('Σχετικός κωδικός') : $eidos_parastatikou_aade_code);?></span></span>
            </div>
          </div>
          <div class="form-group row">
            <label for="send_paroxos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή σε Πάροχο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="send_paroxos" value="1" <?php if ($row['send_paroxos']!=0) echo ' checked '; ?> <?php if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) echo ' disabled '; ?>>
              <span id="send_paroxos1" style="<?php if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) echo 'display:none;'?>"><?php echo gks_lang('Κωδικός αποστολής myData');?>: <span id="send_paroxos2"><?php echo (in_array($id_acc_eidos_parastatikou,[702,703,704]) ? gks_lang('Σχετικός κωδικός') : $eidos_parastatikou_aade_code);?></span></span>
            </div>
          </div>
          <div class="form-group row" id="div_aade_lock_send_numbers">
            <label for="aade_lock_send_numbers" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυστηρή σειρά αποστολής');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="aade_lock_send_numbers" value="1" <?php if ($row['aade_lock_send_numbers']!=0) echo ' checked '; ?> <?php if ($row['send_mydata']==0 and $row['send_paroxos']==0) echo ' disabled '; ?>>
              <small class="form-text text-muted">
                <?php echo gks_lang('Για να αποσταλεί ένα παραστατικό στην ΑΑΔΕ/πάροχο θα πρέπει το παραστατικό με τον προηγούμενο αριθμό της ίδιας σειράς, του ίδου ημερολογίου, στην ίδια εταιρεία να έχει ήδη αποσταλεί');?>
              </small>
            </div>
          </div>          
          <div class="form-group row">
            <label for="seira_need_signature" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απαιτείται υπογραφή από πάροχο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="seira_need_signature" value="1" <?php if ($row['seira_need_signature']!=0) echo ' checked '; ?> <?php if ($row['send_paroxos']==0) echo ' disabled '; ?>>
            </div>
          </div>


          
          <div class="form-group row" id="div_seira_need_signature" style="<?php if ($row['seira_need_signature']==0) echo 'display:none;';?>">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οι τρόποι πληρωμής που απαιτούν υπογραφή');?>:</label>
            <div class="col-md-8">
              <?php
              $sql="select payment_acquirer_id from gks_acc_seires_paymentacquirers where acc_seira_id=".$id;
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'admin-users-item.php error sql',$sql);
                die('sql error');}
              $payment_acquirer_id_ids=array();
              while ($row_select = $result_select->fetch_assoc()) {
                $payment_acquirer_id_ids[]=intval($row_select['payment_acquirer_id']);
              }
                
              $sql="SELECT id_payment_acquirer, payment_acquirer_name
              FROM gks_payment_acquirers
              WHERE aade_tropos_pliromis_id=7 OR payment_acquirer_with_id>0
              ORDER BY mysortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'admin-users-item.php error sql',$sql);
                die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<div class="div_gks_payacq_sw">'.
                  '<input class="gks_payacq_sw" name="gks_payacq_sw" type="checkbox" id="payacq'.$row_select['id_payment_acquirer'].'" value="'.$row_select['id_payment_acquirer'].'" '.
                  (in_array(intval($row_select['id_payment_acquirer']),$payment_acquirer_id_ids) ? 'checked' : '').'>'.
                  ' <span class="gks_payacq_span" style="">'.$row_select['payment_acquirer_name'].'</span>'.
                '</div>';
              }?>                            
            </div>
          </div>          

          <?php 
          //echo $row['acc_eidos_parastatikou_whi_id'].'|'.$eidos_parastatikou_aade_code;
          ?>
          <div class="form-group row" id="div_seira_isdeliverynote" style="">
            <label for="seira_isdeliverynote" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ');?><br><?php echo gks_lang('π.χ. δελτίο αποστολής');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="seira_isdeliverynote" value="1" <?php 
                if ($row['seira_isdeliverynote']!=0) echo ' checked '; 
              ?>>
            </div>
          </div>
          <div class="form-group row" id="div_seira_is_reverse_delivery_note" style="">
            <label for="seira_is_reverse_delivery_note" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντίστροφη Διακίνηση');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="seira_is_reverse_delivery_note" value="1" <?php 
                if ($row['seira_is_reverse_delivery_note']!=0) echo ' checked '; 
              ?>>
            </div>
          </div>
          <div class="form-group row" id="div_seira_is_self_pricing" style="">
            <label for="seira_is_self_pricing" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυτοτιμολόγηση');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="seira_is_self_pricing" value="1" <?php 
                if ($row['seira_is_self_pricing']!=0) echo ' checked '; 
              ?>>
            </div>
          </div>
          <div class="form-group row" id="div_seira_is_vat_payment_suspension" style="">
            <label for="seira_is_vat_payment_suspension" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναστολή Καταβολής ΦΠΑ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="seira_is_vat_payment_suspension" value="1" <?php 
                if ($row['seira_is_vat_payment_suspension']!=0) echo ' checked '; 
              ?>>
            </div>
          </div>
          

                              
          <div class="form-group row">
            <label for="is_xeirografi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χειρόγραφη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_xeirografi" value="1" <?php if ($row['is_xeirografi']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          
          
          
          <div class="form-group row">
            <label for="is_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_disable" value="1" <?php if ($row['is_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
                    
          

 
          

          

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('gks ERP App Desktop');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpapp');?>> 

          <div class="form-group row">
            <label for="erp_app_id_check" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή στην gks ERP App Desktop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_id_check" value="1" <?php if ($row['erp_app_id']!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted">
                <?php echo gks_lang('Υπερισχύει η ρύθμιση της εντατικής λιανικής σε σχέση με την σειρά');?>
              </small>
            </div>
          </div>
          
          <?php
          
          //print '<pre>';print_r($row);die();
          
          $row['erp_app_id']=intval($row['erp_app_id']);
          $row['erp_app_dest']=trim_gks($row['erp_app_dest']);
          if ($row['erp_app_dest']=='') $row['erp_app_dest']='printer';
          $row['erp_app_dest_printer']=trim_gks($row['erp_app_dest_printer']);
          $row['erp_app_dest_printer_method']=intval($row['erp_app_dest_printer_method']);
          $row['erp_app_dest_printer_lpr_ip']=trim_gks($row['erp_app_dest_printer_lpr_ip']);
          $row['erp_app_dest_printer_copies']=intval($row['erp_app_dest_printer_copies']);
          $row['erp_app_dest_folder']=trim_gks($row['erp_app_dest_folder']);
          $row['erp_app_filter']=trim_gks($row['erp_app_filter']);
          $erp_app_filter=array();
          if ($row['erp_app_filter']!='') $erp_app_filter=json_decode($row['erp_app_filter'],true);
          
          ?>
          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_filter" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φίλτρο');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_computer" value="webpage_computer" <?php if (in_array('webpage_computer',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_computer"><?php echo gks_lang('Από web σελίδα Η/Υ');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_tablet" value="webpage_tablet" <?php if (in_array('webpage_tablet',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_tablet"><?php echo gks_lang('Από web σελίδα tablet');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_webpage_mobile" value="webpage_mobile" <?php if (in_array('webpage_mobile',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_webpage_mobile"><?php echo gks_lang('Από web σελίδα κινητού');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_app_with_thermal" value="app_with_thermal" <?php if (in_array('app_with_thermal',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_app_with_thermal"><?php echo gks_lang('Από gks ERP App Mobile με θερμικό εκτυπωτή');?></label>
                <br>
                <input type="checkbox" name="erp_app_filter" id="erp_app_filter_val_app_no_thermal" value="app_no_thermal" <?php if (in_array('app_no_thermal',$erp_app_filter)) echo 'checked';?>>
                  <label for="erp_app_filter_val_app_no_thermal"><?php echo gks_lang('Από gks ERP App Mobile χωρίς θερμικό εκτυπωτή');?></label>
              </div> 
            </div>
          </div>
          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('gks ERP App Desktop');?>:</label>
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
                  if ($row_select['id_erp_app']==$row['erp_app_id']) {
                    echo ' selected ';
                    $erp_app_local_printers=trim_gks($row_select['erp_app_local_printers']);
                  }
                  echo '>'.$row_select['erp_app_name'].'</option>';
                }?>
              </select>
            </div>
          </div> 

          <div class="form-group row div_erp_app_id_check_only" style="<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
            <label for="erp_app_dest_val_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προορισμός');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_printer" value="printer" <?php if ($row['erp_app_dest']=='printer') echo 'checked';?>>
                  <label for="erp_app_dest_val_printer"><?php echo gks_lang('Εκτυπωτής');?></label>
                <br>
                <input type="radio" name="erp_app_dest" id="erp_app_dest_val_folder" value="folder" <?php if ($row['erp_app_dest']=='folder') echo 'checked';?>>
                  <label for="erp_app_dest_val_folder"><?php echo gks_lang('Φάκελος');?></label>
              </div>  
            </div>            
          </div>
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_method" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέθοδος');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_method" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($row['erp_app_dest_printer_method']==1) echo 'selected';?> value="1"><?php echo erp_app_dest_printer_method_descr(1);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==0) echo 'selected';?> value="0"><?php echo erp_app_dest_printer_method_descr(0);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==2) echo 'selected';?> value="2"><?php echo erp_app_dest_printer_method_descr(2);?></option>
                <option <?php if ($row['erp_app_dest_printer_method']==3) echo 'selected';?> value="3"><?php echo erp_app_dest_printer_method_descr(3);?></option>

              </select>
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id01" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and in_array($row['erp_app_dest_printer_method'],[0,1]))) echo 'display:none;';?>">
            <label for="erp_app_dest_printer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer" class="form-control form-control-sm myneedsave">
                <option></option>
                <?php
                if ($erp_app_local_printers!='') {
                  $temp=unserialize($erp_app_local_printers);  
                  if (is_array($temp) and count($temp)>0) {
                    foreach ($temp as $value) {
                      echo '<option '.($value==$row['erp_app_dest_printer'] ? 'selected' : '').'>'.$value.'</option>';
                    }
                  }
                }  
                ?>              
              </select>    
            </div>
          </div>

          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id2" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and $row['erp_app_dest_printer_method']==2)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερή IP εκτυπωτή');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_printer_lpr_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_printer_lpr_ip']);?>" placeholder="<?php echo gks_lang('π.χ. 192.168.1.70');?>">
            </div>
          </div>          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer_id3" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer' and $row['erp_app_dest_printer_method']==3)) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_lpr_ip" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εκτυπωτής');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <?php echo gks_lang('Στον προεπιλεγμένο εκτυπωτή του H/Y');?>
              </div>
            </div>
          </div> 
                    
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_printer" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='printer')) echo 'display:none;';?>">
            <label for="erp_app_dest_printer_copies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντίτυπα');?>:</label>
            <div class="col-md-8">
              <select id="erp_app_dest_printer_copies" class="form-control form-control-sm myneedsave" style="width:unset;">
                <option <?php if ($row['erp_app_dest_printer_copies']==1) echo 'selected';?>>1</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==2) echo 'selected';?>>2</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==3) echo 'selected';?>>3</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==4) echo 'selected';?>>4</option>
                <option <?php if ($row['erp_app_dest_printer_copies']==5) echo 'selected';?>>5</option>
              </select>
            </div>
          </div> 
          
          <div class="form-group row div_erp_app_id_check div_erp_app_id_check_folder" style="<?php if (!($row['erp_app_id']>0 and $row['erp_app_dest']=='folder')) echo 'display:none;';?>">
            <label for="erp_app_dest_folder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φάκελος');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_dest_folder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_folder']);?>" placeholder="<?php echo gks_lang('π.χ. c:\printer\folder');?>">
            </div>
          </div>

        </div>
      </div>



<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>      
      

      
    </div>
  </div>
</div>
          

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_acc_seira'];?>" data-model="gks_acc_seires" data-backurl="admin-acc_seires.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <?php
      echo getObjectRels('gks_acc_seires',$id);
      echo getActivityObjectTable('gks_acc_seires',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_acc_seires','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

    
    </div>
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_acc_seira']>0) echo $row['id_acc_seira'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  




<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



var from_php_dialog_object_rel_curr='gks_acc_seires';
var from_php_activity_model='gks_acc_seires';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_seires','delete',$id);?>;


tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,
  
  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});
  
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

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';
    datasend+='&acc_journal_id='  + encodeURIComponent(($("#mypostform #acc_journal_id").val().trim()));
    datasend+='&seira_code='  + encodeURIComponent($.base64.encode($("#mypostform #seira_code").val().trim()));
    datasend+='&seira_descr='  + encodeURIComponent($.base64.encode($("#mypostform #seira_descr").val().trim()));
    datasend+='&seira_comments='  + encodeURIComponent($.base64.encode($("#mypostform #seira_comments").val().trim()));
    datasend+='&prefix='  + encodeURIComponent($.base64.encode($("#mypostform #prefix").val().trim()));
    datasend+='&suffix='  + encodeURIComponent($.base64.encode($("#mypostform #suffix").val().trim()));
    datasend+='&number_size='  + encodeURIComponent(($("#mypostform #number_size").val().trim()));
    datasend+='&number_step='  + encodeURIComponent(($("#mypostform #number_step").val().trim()));
    datasend+='&next_number='  + encodeURIComponent(($("#mypostform #next_number").val().trim()));
    datasend+='&sortorder='  + encodeURIComponent(($("#mypostform #sortorder").val().trim()));
    datasend+='&send_mydata=' + (($('#send_mydata').is(':checked')) ? '1':'0');
    datasend+='&send_paroxos=' + (($('#send_paroxos').is(':checked')) ? '1':'0');
    datasend+='&seira_need_signature=' + (($('#seira_need_signature').is(':checked')) ? '1':'0');
    if ($('#div_seira_isdeliverynote').css('display')!='none') {
      datasend+='&seira_isdeliverynote=' + (($('#seira_isdeliverynote').is(':checked')) ? '1':'0');
    }
    if ($('#div_seira_is_reverse_delivery_note').css('display')!='none') {
      datasend+='&seira_is_reverse_delivery_note=' + (($('#seira_is_reverse_delivery_note').is(':checked')) ? '1':'0');
    }
    if ($('#div_seira_is_self_pricing').css('display')!='none') {
      datasend+='&seira_is_self_pricing=' + (($('#seira_is_self_pricing').is(':checked')) ? '1':'0');
    }
    if ($('#div_seira_is_vat_payment_suspension').css('display')!='none') {
      datasend+='&seira_is_vat_payment_suspension=' + (($('#seira_is_vat_payment_suspension').is(':checked')) ? '1':'0');
    }
    
    var payacq=[];
    if ($('#seira_need_signature').is(':checked')) {
      $('input[name=gks_payacq_sw]').each(function() {
        if ($(this).is(':checked')) {
          tt=parseInt($(this).val()); if (isNaN(tt)) tt=0;
          payacq.push(tt);
        }
      });
    }
    payacq_str = encodeURIComponent($.base64.encode(JSON.stringify(payacq)));
    datasend+='&payacq_str=' + payacq_str;

    //console.log(payacq,payacq_str);
    //return;
    
    datasend+='&aade_lock_send_numbers='  + (($('#aade_lock_send_numbers').is(':checked')) ? '1':'0');
    datasend+='&is_xeirografi=' + (($('#is_xeirografi').is(':checked')) ? '1':'0');
    datasend+='&is_disable=' + (($('#is_disable').is(':checked')) ? '0':'1');
    
    
    
    datasend+='&erp_app_id_check=' + (($('#erp_app_id_check').is(':checked')) ? '1':'0');
    datasend+='&erp_app_filter_val_webpage_computer=' + (($('#erp_app_filter_val_webpage_computer').is(':checked')) ? '1':'0');
    datasend+='&erp_app_filter_val_webpage_tablet=' + (($('#erp_app_filter_val_webpage_tablet').is(':checked')) ? '1':'0');
    datasend+='&erp_app_filter_val_webpage_mobile=' + (($('#erp_app_filter_val_webpage_mobile').is(':checked')) ? '1':'0');
    datasend+='&erp_app_filter_val_app_with_thermal=' + (($('#erp_app_filter_val_app_with_thermal').is(':checked')) ? '1':'0');
    datasend+='&erp_app_filter_val_app_no_thermal=' + (($('#erp_app_filter_val_app_no_thermal').is(':checked')) ? '1':'0');
    datasend+='&erp_app_id=' + encodeURIComponent(($("#mypostform #erp_app_id").val().trim()));
    datasend+='&erp_app_dest=' + encodeURIComponent($.base64.encode($('input[name=erp_app_dest]:checked').val()));
    datasend+='&erp_app_dest_printer='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer").val().trim()));
    datasend+='&erp_app_dest_printer_method='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_method").val().trim()));
    datasend+='&erp_app_dest_printer_lpr_ip='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer_lpr_ip").val().trim()));
    datasend+='&erp_app_dest_printer_copies='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_copies").val().trim()));
    datasend+='&erp_app_dest_folder='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_folder").val().trim()));
    
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-acc_seires-item-exec.php?id=' + <?php echo $id;?>,
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
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }

  function sample_number_calc() {
    sample_number='';
    sample_number+=$('#prefix').val().trim();
    val = parseInt($('#next_number').val());
    if (isNaN(val)) val=0;
    size=parseInt($('#number_size').val());
    if (isNaN(size)) size=0;
    sample_number+=pad(val, size);
    sample_number+=$('#suffix').val().trim();
    $('#sample_number').html(sample_number);
  }
  mychange = 'change keyup paste';
  $('#prefix').on(mychange, sample_number_calc);
  $('#suffix').on(mychange, sample_number_calc);
  $('#number_size').on(mychange, sample_number_calc);
  $('#next_number').on(mychange, sample_number_calc);
  sample_number_calc();
  


      


  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
  var send_mydata_sw = new Switchery(document.querySelector('#send_mydata'),gks_switchery_defaults());
  var send_paroxos_sw = new Switchery(document.querySelector('#send_paroxos'),gks_switchery_defaults());
  var seira_need_signature_sw = new Switchery(document.querySelector('#seira_need_signature'),gks_switchery_defaults());
  var seira_isdeliverynote_sw = new Switchery(document.querySelector('#seira_isdeliverynote'),gks_switchery_defaults());
  var seira_is_reverse_delivery_note_sw = new Switchery(document.querySelector('#seira_is_reverse_delivery_note'),gks_switchery_defaults());
  var seira_is_self_pricing_sw = new Switchery(document.querySelector('#seira_is_self_pricing'),gks_switchery_defaults());
  var seira_is_vat_payment_suspension_sw = new Switchery(document.querySelector('#seira_is_vat_payment_suspension'),gks_switchery_defaults());
  var aade_lock_send_numbers_sw = new Switchery(document.querySelector('#aade_lock_send_numbers'),gks_switchery_defaults());
  
  var payacq_sw = Array.prototype.slice.call(document.querySelectorAll('.gks_payacq_sw'));
  payacq_sw.forEach(function(html) {
    var switchery4 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });

  
  
  document.querySelector('#send_mydata').onchange = function() {
    need_save=true;
    if ($('#send_mydata').is(':checked') && $('#send_paroxos').is(':checked')) $('#send_paroxos').click();
    if ($('#send_paroxos').is(':checked')==false && $('#seira_need_signature').is(':checked')) $('#seira_need_signature').click();
    if ($('#send_paroxos').is(':checked')) seira_need_signature_sw.enable(); else seira_need_signature_sw.disable();
    
    if ($('#send_mydata').is(':checked') || $('#send_paroxos').is(':checked')) {
      seira_isdeliverynote_sw.enable();
      aade_lock_send_numbers_sw.enable();
    } else {
      if ($('#seira_isdeliverynote').is(':checked')) $('#seira_isdeliverynote').click();
      seira_isdeliverynote_sw.disable();
      if ($('#aade_lock_send_numbers').is(':checked')) $('#aade_lock_send_numbers').click();
      aade_lock_send_numbers_sw.disable();
    }
    
    
  };
  document.querySelector('#send_paroxos').onchange = function() {
    need_save=true;
    if ($('#send_mydata').is(':checked') && $('#send_paroxos').is(':checked')) $('#send_mydata').click();
    if ($('#send_paroxos').is(':checked')==false && $('#seira_need_signature').is(':checked')) $('#seira_need_signature').click();
    if ($('#send_paroxos').is(':checked')) seira_need_signature_sw.enable(); else seira_need_signature_sw.disable();
    
    if ($('#send_mydata').is(':checked') || $('#send_paroxos').is(':checked')) {
      seira_isdeliverynote_sw.enable();
      aade_lock_send_numbers_sw.enable();
    } else {
      if ($('#seira_isdeliverynote').is(':checked')) $('#seira_isdeliverynote').click();
      seira_isdeliverynote_sw.disable();
      if ($('#aade_lock_send_numbers').is(':checked')) $('#aade_lock_send_numbers').click();
      aade_lock_send_numbers_sw.disable();
    }
      
  };
  document.querySelector('#seira_need_signature').onchange = function() {
    need_save=true;
    if ($('#seira_need_signature').is(':checked')) $('#div_seira_need_signature').show(); else $('#div_seira_need_signature').hide();
    gks_myscroll();
  };
  
  
  
    
  function acc_journal_id_change() {
    id_acc_eidos=parseInt($('#acc_journal_id option:selected').attr('data-id_acc_eidos').trim());
    if (isNaN(id_acc_eidos)) id_acc_eidos=0;
    aade_code=$('#acc_journal_id option:selected').attr('data-aade_code').trim();
    if (aade_code=='' && [702,703,704].includes(id_acc_eidos)==false) {
      $('#send_mydata1').hide();
      $('#send_mydata2').html('');
      if ($('#send_mydata').is(':checked')) $('#send_mydata').click();
      send_mydata_sw.disable();
    } else {
      $('#send_mydata1').show();
      $('#send_mydata2').html([702,703,704].includes(id_acc_eidos) ? gks_lang('Σχετικός κωδικός') : aade_code);
      send_mydata_sw.enable();
    }

    if (aade_code=='' && [702,703,704].includes(id_acc_eidos)==false) {
      $('#send_paroxos1').hide();
      $('#send_paroxos2').html('');
      if ($('#send_paroxos').is(':checked')) $('#send_paroxos').click();
      send_paroxos_sw.disable();
    } else {
      $('#send_paroxos1').show();
      $('#send_paroxos2').html([702,703,704].includes(id_acc_eidos) ? gks_lang('Σχετικός κωδικός') : aade_code);
      send_paroxos_sw.enable();
    }
    

    whi_id=parseInt($('#acc_journal_id option:selected').attr('data-whi_id').trim());
    if (isNaN(whi_id)) whi_id=0;
    if (whi_id<=0 && ['9.1','9.2','9.3'].includes(aade_code)==false) {
      seira_isdeliverynote_sw.enable();
      if ($('#seira_isdeliverynote').is(':checked')) $('#seira_isdeliverynote').click();
      $('#div_seira_isdeliverynote').hide();
    } else {
      $('#div_seira_isdeliverynote').show();
      if ($('#send_mydata').is(':checked') || $('#send_paroxos').is(':checked')) {
        seira_isdeliverynote_sw.enable();
      } else {
        if ($('#seira_isdeliverynote').is(':checked')) $('#seira_isdeliverynote').click();
        seira_isdeliverynote_sw.disable();
      }
    }
    
    if (['9.3'].includes(aade_code)==false) {
      seira_is_reverse_delivery_note_sw.enable();
      if ($('#seira_is_reverse_delivery_note').is(':checked')) $('#seira_is_reverse_delivery_note').click();
      $('#div_seira_is_reverse_delivery_note').hide();
    } else {
      $('#div_seira_is_reverse_delivery_note').show();
      seira_is_reverse_delivery_note_sw.enable();
    }
    if (['1.1','1.4','1.5','2.1','5.2'].includes(aade_code)==false) {
      seira_is_self_pricing_sw.enable();
      if ($('#seira_is_self_pricing').is(':checked')) $('#seira_is_self_pricing').click();
      $('#div_seira_is_self_pricing').hide();
    } else {
      $('#div_seira_is_self_pricing').show();
      seira_is_self_pricing_sw.enable();
    }    
    
    if (['1.1','1.2','1.3','1.4','1.5','2.1','2.2','2.3','3.1','3.2',
    '5.2','6.1','6.2','7.1','8.1','8.2',
    '11.1','11.2','11.3','11.4','11.5','13.1','13.2','13.3','13.30','13.31','13.4',
    '14.1','14.2','14.3','14.30','14.31','14.4','14.5','15.1','16.1',
    '17.1','17.2','17.3','17.4','17.5','17.6'].includes(aade_code)==false) {
      seira_is_vat_payment_suspension_sw.enable();
      if ($('#seira_is_vat_payment_suspension').is(':checked')) $('#seira_is_vat_payment_suspension').click();
      $('#div_seira_is_vat_payment_suspension').hide();
    } else {
      $('#div_seira_is_vat_payment_suspension').show();
      seira_is_vat_payment_suspension_sw.enable();
    }    
    
    
  }
  $('#acc_journal_id').change(acc_journal_id_change);
  
  


  
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

  // last of all
  //if (from_php_id==-1) {
    acc_journal_id_change();
  //}

  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;    
  
});



 
</script>




<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


