<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_payment_acquirers',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_payment_acquirers_add=gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','add',0);






if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

$gks_custom_prepare = gks_custom_table_item_prepare('gks_payment_acquirers',['from'=>'item']);

$base_sql ="SELECT gks_payment_acquirers.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
FROM (gks_payment_acquirers 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_payment_acquirers.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_payment_acquirers.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
";


$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {

  if ($template_id>0) {
    $sql=$base_sql."where id_payment_acquirer = ".$template_id;
    $result = $db_link->query($sql);        
    if (!$result) debug_mail(false,'error sql',$sql);
    if (!$result) die('sql error');
    if ($result->num_rows!=1) {
      debug_mail(false,'record not found sql tempate',$sql); 
      die('no record found (tempate)');
    } 
    $row = $result->fetch_assoc();
    //$row['id_payment_acquirer']=-1; //gia na doulecei to custom
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0; 
    $row['myip']='';
    $row['payment_acquirer_name'].=' draft '.rand(1000,9999);
    $row['payment_acquirer_html'].=' draft '.rand(1000,9999);
    
    $my_page_title=gks_lang('Νέος Τρόπος Πληρωμής από το πρότυπο').' #'.$template_id;   
  }
  if ($template_id==0) {

    $row=array();
    $row['user_id_add'] =0;
    $row['user_id_edit'] =0;
    $row['gks_nickname_add'] ='';
    $row['gks_nickname_edit'] ='';
    $row['myip'] ='';
    $row['id_payment_acquirer']=-1;
    $row['payment_acquirer_name']='';
    $row['payment_acquirer_table_name']='';
    $row['payment_acquirer_type']='';
    $row['payment_acquirer_type_dm']='';
    $row['payment_acquirer_html']='';
    $row['payment_acquirer_button_html']='';
    $row['payment_acquirer_sxolio']='';
    $row['payment_acquirer_tooltip']='';
    $row['payment_acquirer_env_test']=1;
    $row['payment_acquirer_fees_enabled']=0;
    $row['pa_fees_domestic_fixed']=0;
    $row['pa_fees_domestic_percent']=0;
    $row['pa_fees_international_fixed']=0;
    $row['pa_fees_international_percent']=0;
    $row['payment_acquirer_php_function_isok']='';
    $row['payment_acquirer_php_function_calculate']='';
    $row['mysortorder']=1000;
    $row['aade_tropos_pliromis_id']=0;
    $row['payment_acquirer_with_id']=0;
    $row['show_acc_pay']=0;
    $row['show_eshop']=0;
    $row['payment_acquirer_disabled']=0;
    
    $my_page_title=gks_lang('Νέος Τρόπος Πληρωμής');
  }
} else {
  $sql=$base_sql."where id_payment_acquirer = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Τρόπος Πληρωμής').': '.$row['payment_acquirer_name'];
  $object_title=$row['payment_acquirer_name'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('manage','manage_p');


$lang_data_obj=gks_lang_data_obj_prepare('gks_payment_acquirers','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Τρόπος Πληρωμής');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Τρόπος Πληρωμής');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέος');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>         
          <div class="form-group row">
            <label for="payment_acquirer_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τρόπος Πληρωμής');?>:</label>
            <div class="col-sm-8">
              <input id="payment_acquirer_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_name']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('payment_acquirer_name'));
          ?>          
          <div class="form-group row">
            <label for="payment_acquirer_table_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχετικός Πίνακας');?>:</label>
            <div class="col-sm-8">
              <input id="payment_acquirer_table_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_table_name']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_type" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-sm-8">
              <select id="payment_acquirer_type" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <option value="none"     <?php if ($row['payment_acquirer_type']=='none')     echo 'selected';?>>none</option>
                <option value="bank"     <?php if ($row['payment_acquirer_type']=='bank')     echo 'selected';?>>bank</option>
                <option value="delivery" <?php if ($row['payment_acquirer_type']=='delivery') echo 'selected';?>>delivery</option>
                <option value="web"      <?php if ($row['payment_acquirer_type']=='web')      echo 'selected';?>>web</option>
                <option value="store"    <?php if ($row['payment_acquirer_type']=='store')    echo 'selected';?>>store</option>
                <option value="cash"     <?php if ($row['payment_acquirer_type']=='cash')     echo 'selected';?>>cash</option>
                <option value="credit"   <?php if ($row['payment_acquirer_type']=='credit')   echo 'selected';?>>credit</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_type_dm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχετικοί Τύποι Αποστολής');?>:</label>
            <div class="col-sm-8" style="font-size: 0.875rem;">
              <?php 
              $paa=array();
              $pa=$row['payment_acquirer_type_dm'].'';
              if ($pa!='') {
                if (substr($pa,0,1) =='[') $pa=substr($pa,1);
                if (substr($pa,strlen($pa)-1,1) ==']') $pa=substr($pa,0,strlen($pa)-1);
                $paa=explode('][',$pa);
              }
              $sql="SELECT delivery_method_type FROM gks_delivery_methods where delivery_method_type<>'' GROUP BY delivery_method_type ORDER BY delivery_method_type;";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) { ?>
                <input type="checkbox" name="payment_acquirer_type_dm[]" class="myneedsave"
                id="payment_acquirer_type_dm_<?php echo $row_select['delivery_method_type'];?>" 
                value="<?php echo $row_select['delivery_method_type'];?>" 
                <?php if (in_array($row_select['delivery_method_type'],$paa)) echo ' checked '; ?> >
                <?php 
                echo '<label for="payment_acquirer_type_dm_'.$row_select['delivery_method_type'].'" style="display: inline;">'.$row_select['delivery_method_type'].' (';
                
                $sql="SELECT delivery_method_name FROM gks_delivery_methods WHERE delivery_method_type='".$row_select['delivery_method_type']."' ORDER BY mysortorder,delivery_method_name;";
                $result_select2 = $db_link->query($sql);        
                if (!$result_select2) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                $other_html='';
                while ($row_select2 = $result_select2->fetch_assoc()) {
                  $other_html.= $row_select2['delivery_method_name'].', ';
                }
                if ($other_html!='') $other_html=substr($other_html,0,strlen($other_html)-2);
                
                echo $other_html.')</span><br>';
                
              } ?>
                            
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_html" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('HTML (FrontEnd)');?>:</label>
            <div class="col-sm-8">
              <input id="payment_acquirer_html" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_html']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('payment_acquirer_html'));
          ?>          
          
          
          <div class="form-group row">
            <label for="payment_acquirer_button_html" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κουμπί (FrontEnd)');?>:</label>
            <div class="col-sm-8">
              <input id="payment_acquirer_button_html" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_button_html']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('payment_acquirer_button_html'));
          ?>  
                    
          <div class="form-group row">
            <label for="payment_acquirer_sxolio" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-sm-8">
              <textarea id="payment_acquirer_sxolio" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['payment_acquirer_sxolio']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('payment_acquirer_sxolio'));
          ?>  

          <div class="form-group row">
            <label for="payment_acquirer_tooltip" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξήγηση');?>:</label>
            <div class="col-sm-8">
              <textarea id="payment_acquirer_tooltip" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['payment_acquirer_tooltip']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('payment_acquirer_tooltip'));
          ?>  
          <div class="form-group row">
            <label for="payment_acquirer_env_test" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δοκιμαστικό Περιβάλλον');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="payment_acquirer_env_test" value="1" <?php if ($row['payment_acquirer_env_test']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_fees_enabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργοποίηση Κόστους');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="payment_acquirer_fees_enabled" value="1" <?php if ($row['payment_acquirer_fees_enabled']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="pa_fees_domestic_fixed" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό κόστος εσωτερικού');?>:</label>
            <div class="col-md-8">
              <input id="pa_fees_domestic_fixed" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['pa_fees_domestic_fixed'];?>" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="pa_fees_domestic_percent" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ποσοστό εσωτερικού');?>:</label>
            <div class="col-md-8">
              <input id="pa_fees_domestic_percent" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['pa_fees_domestic_percent'];?>" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="pa_fees_international_fixed" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό κόστος εξωτερικού');?>:</label>
            <div class="col-md-8">
              <input id="pa_fees_international_fixed" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['pa_fees_international_fixed'];?>" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="pa_fees_international_percent" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ποσοστό εξωτερικού');?>:</label>
            <div class="col-md-8">
              <input id="pa_fees_international_percent" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['pa_fees_international_percent'];?>" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="payment_acquirer_php_function_isok" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνάρτηση ενεργοποίησης');?>:</label>
            <div class="col-md-8">
              <input id="payment_acquirer_php_function_isok" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_php_function_isok']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_php_function_calculate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνάρτηση υπολογισμού');?>:</label>
            <div class="col-md-8">
              <input id="payment_acquirer_php_function_calculate" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['payment_acquirer_php_function_calculate']);?>">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="aade_tropos_pliromis_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΑΔΕ');?>:</label>
            <div class="col-sm-8">
              <select id="aade_tropos_pliromis_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT * FROM gks_aade_tropoi_pliromis order by sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id_aade_tropos_pliromis']==$row['aade_tropos_pliromis_id'] ? 'selected' : '').' value="'.$row_list['id_aade_tropos_pliromis'].'">'.$row_list['aade_tropos_pliromis_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="payment_acquirer_with_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πληρωμή μέσω');?>:</label>
            <div class="col-sm-8">
              <select id="payment_acquirer_with_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT * FROM gks_payment_acquirer_with where payment_paroxos_implemented=1 order by payment_paroxos_sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id_payment_acquirer_with']==$row['payment_acquirer_with_id'] ? 'selected' : '').' value="'.$row_list['id_payment_acquirer_with'].'">'.$row_list['payment_paroxos_name'].'</option>';
                }?>
              </select>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="mysortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="mysortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['mysortorder']);?>" min="1" strep="1">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="show_acc_pay" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εμφάνιση στις Πληρωμές');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="show_acc_pay" value="1" <?php if ($row['show_acc_pay']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="show_eshop" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εμφάνιση στο eshop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="show_eshop" value="1" <?php if ($row['show_eshop']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="payment_acquirer_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργός');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="payment_acquirer_disabled" value="1" <?php if ($row['payment_acquirer_disabled']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>

        </div>
      </div>




    


    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($id>0) echo $id;?></span></div>
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




<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_payment_acquirer'];?>" data-model="gks_payment_acquirers" data-backurl="admin-payment-acquirers.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php } ?>
      <?php 
      if ($id>0 and $perm_gks_payment_acquirers_add) {
        echo '<a href="admin-payment-acquirers-item.php?id=-1&template_id='.$id.'" style="margin-bottom:0px;" '.
          'class="btn btn-primary tooltipster" '.
          'id="submit_button_template" '.
          'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'</div>">'.
          '<i class="fas fa-copy" style="font-size: 120%;"></i>'.
        '</a> ';
      }?>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_payment_acquirers','delete',$id);?>;

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
      //console.log(event.ctrlKey);
      //console.log(event.which);
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

    var mypa='';
    $("input[name='payment_acquirer_type_dm[]']").each( function () {
      if ($(this).is(':checked')) {
        mypa+='[' + $(this).val() + ']';
      }
    });
    datasend+='&payment_acquirer_name='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_name").val().trim()));
    datasend+='&payment_acquirer_table_name='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_table_name").val().trim()));
    datasend+='&payment_acquirer_type='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_type").val().trim()));
    datasend+='&payment_acquirer_type_dm='  + encodeURIComponent($.base64.encode(mypa.trim()));
    datasend+='&payment_acquirer_html='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_html").val().trim()));
    datasend+='&payment_acquirer_button_html='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_button_html").val().trim()));
    datasend+='&payment_acquirer_sxolio='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_sxolio").val().trim()));
    datasend+='&payment_acquirer_tooltip='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_tooltip").val().trim()));
    datasend+='&payment_acquirer_env_test=' + (($('#payment_acquirer_env_test').is(':checked')) ? '1':'0');
    datasend+='&payment_acquirer_fees_enabled=' + (($('#payment_acquirer_fees_enabled').is(':checked')) ? '1':'0');
    datasend+='&pa_fees_domestic_fixed='  + encodeURIComponent($("#mypostform #pa_fees_domestic_fixed").val().trim());
    datasend+='&pa_fees_domestic_percent='  + encodeURIComponent($("#mypostform #pa_fees_domestic_percent").val().trim());
    datasend+='&pa_fees_international_fixed='  + encodeURIComponent($("#mypostform #pa_fees_international_fixed").val().trim());
    datasend+='&pa_fees_international_percent='  + encodeURIComponent($("#mypostform #pa_fees_international_percent").val().trim());
    datasend+='&payment_acquirer_php_function_isok='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_php_function_isok").val().trim()));
    datasend+='&payment_acquirer_php_function_calculate='  + encodeURIComponent($.base64.encode($("#mypostform #payment_acquirer_php_function_calculate").val().trim()));
    datasend+='&aade_tropos_pliromis_id='  + encodeURIComponent(($("#mypostform #aade_tropos_pliromis_id").val().trim()));
    datasend+='&payment_acquirer_with_id='  + encodeURIComponent(($("#mypostform #payment_acquirer_with_id").val().trim()));
    datasend+='&mysortorder='  + encodeURIComponent(($("#mypostform #mysortorder").val().trim()));
    datasend+='&show_acc_pay=' + (($('#show_acc_pay').is(':checked')) ? '1':'0');
    datasend+='&show_eshop=' + (($('#show_eshop').is(':checked')) ? '1':'0');
    datasend+='&payment_acquirer_disabled=' + (($('#payment_acquirer_disabled').is(':checked')) ? '0':'1');
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-payment-acquirers-item-exec.php?id=' + <?php echo $id;?>,
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
  
  function payment_acquirer_sxolio_change() {gks_resize_textarea($(this));}
  $('#payment_acquirer_sxolio').on('change keyup paste', payment_acquirer_sxolio_change);
  gks_resize_textarea($('#payment_acquirer_sxolio'));
  
  function payment_acquirer_tooltip_change() {gks_resize_textarea($(this));}
  $('#payment_acquirer_tooltip').on('change keyup paste', payment_acquirer_tooltip_change);
  gks_resize_textarea($('#payment_acquirer_tooltip'));
  

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    

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
//db_close();
include_once('_my_footer_admin.php');


