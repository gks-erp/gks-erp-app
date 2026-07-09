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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_acc_eidos_parastatikou']=-1;
  $row['eidos_parastatikou_aade_code']='';
  $row['eidos_parastatikou_descr'] ='';
  $row['sortorder'] =20000;
  $row['aade_disable'] =0;
  $row['peppol_code']=0;
  
  $row['parent_id']=0;
  $row['eidos_parastatikou_type_id']=0;
  $row['eidos_parastatikou_need_prev']=0;
  $row['eidos_parastatikou_has_fpa']=0;
  $row['eidos_parastatikou_has_posotita']=0;
  $row['eidos_parastatikou_has_othertaxes']='';
  $row['eidos_parastatikou_has_esoda']=0;
  $row['eidos_parastatikou_has_eksoda']=0;
  $row['eidos_parastatikou_need_afm']=0;
  $row['eidos_parastatikou_balance_pros']=0;
  $row['eidos_parastatikou_stock_pros']=0;
  $row['eidos_parastatikou_whi_type_id']=0;
  $row['eidos_parastatikou_other_entity']=0;
  $row['eidos_parastatikou_correlated_invoices']=0;
  $row['eidos_parastatikou_multiple_connected_marks']=0;
  $row['eidos_parastatikou_packings_declarations']=0;
  $row['is_selectable']=1;
  $row['credit_acc_eidos_parastatikou_id']=0;
  $row['import_apo_allon']='';






  $my_page_title=gks_lang('Είδος Παραστατικών');

} else {
 $sql ="SELECT gks_acc_eidi_parastatikon.*,
 ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_acc_eidi_parastatikon
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_acc_eidi_parastatikon.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_acc_eidi_parastatikon.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_acc_eidos_parastatikou = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Είδος Παραστατικών').': '.$row['eidos_parastatikou_descr'];
  $object_title=$row['eidos_parastatikou_descr'];
}

stat_record();
$nav_active_array=array('manage','manage_aade','manage_aade_eidi_parastatikon');



$lang_data_obj=gks_lang_data_obj_prepare('gks_acc_eidi_parastatikon','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Είδος Παραστατικών');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Είδος Παραστατικών');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="eidos_parastatikou_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="eidos_parastatikou_descr"  value="<?php echo htmlspecialchars_gks($row['eidos_parastatikou_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('eidos_parastatikou_descr'));
          ?>


          <div class="form-group row">
            <label for="eidos_parastatikou_type_id" class="col-sm-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βασικός Τύπος');?>:</label>
            <div class="col-sm-8">
              <select id="eidos_parastatikou_type_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_acc_eidi_parastatikon_types=gks_lang_data_obj_prepare('gks_acc_eidi_parastatikon_types','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_acc_eidi_parastatikon_types, array('acc_eidi_parastatikon_type_descr','antisimvalomenos_label'));
                $sql="select id_acc_eidi_parastatikon_type,".gks_lang_sql_field('acc_eidi_parastatikon_type_descr',$lang_prepare_gks_acc_eidi_parastatikon_types)." ,".gks_lang_sql_field('antisimvalomenos_label',$lang_prepare_gks_acc_eidi_parastatikon_types)." 
                FROM ".$lang_prepare_gks_acc_eidi_parastatikon_types['sql']['from1']." gks_acc_eidi_parastatikon_types 
                ".$lang_prepare_gks_acc_eidi_parastatikon_types['sql']['from2']."
                order by sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_eidi_parastatikon_type'].'" ';
                  if ($row_select['id_acc_eidi_parastatikon_type']==$row['eidos_parastatikou_type_id']) echo ' selected ';
                  echo '>'.$row_select['acc_eidi_parastatikon_type_descr'].' / '.$row_select['antisimvalomenos_label'].'</option>';
                }?>
              </select>    
            </div>
          </div>           
          <div class="form-group row">
            <label for="parent_id" class="col-sm-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γονικό');?>:</label>
            <div class="col-sm-8">
              <select id="parent_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,
                ug2.eidos_parastatikou_descr AS gt2,
                ug3.eidos_parastatikou_descr AS gt3
                FROM (gks_acc_eidi_parastatikon
                LEFT JOIN gks_acc_eidi_parastatikon AS ug2 ON gks_acc_eidi_parastatikon.parent_id = ug2.id_acc_eidos_parastatikou)
                LEFT JOIN gks_acc_eidi_parastatikon AS ug3 ON ug2.parent_id = ug3.id_acc_eidos_parastatikou
                order by gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                $mylevels=[];
                while ($row_select = $result_select->fetch_assoc()) {
                  $mylev=0; 
                  if (!empty($row_select['gt2'])) $mylev=1;
                  if (!empty($row_select['gt3'])) $mylev=2;

                  $mylevels[$row_select['id_acc_eidos_parastatikou']]=$mylev;
                }
                //echo '<pre>';print_r($mylevels);die();
                
                
                $lang_prepare_gks_acc_eidi_parastatikon=gks_lang_data_obj_prepare('gks_acc_eidi_parastatikon','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_acc_eidi_parastatikon, array('eidos_parastatikou_descr'));
                $sql="select id_acc_eidos_parastatikou,".gks_lang_sql_field('eidos_parastatikou_descr',$lang_prepare_gks_acc_eidi_parastatikon)." 
                FROM ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from1']." gks_acc_eidi_parastatikon 
                ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from2']."
                where aade_disable=0 and is_selectable=0 and id_acc_eidos_parastatikou<>".$id."
                order by sortorder";
                //echo $sql;die();
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  $mypad='';
                  if (isset($mylevels[$row_select['id_acc_eidos_parastatikou']])) $mypad=str_replace(' ','&nbsp;',substr('             ',0,4*$mylevels[$row_select['id_acc_eidos_parastatikou']])); 
                  echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" data-mypad="'.$mypad.'" ';
                  if ($row_select['id_acc_eidos_parastatikou']==$row['parent_id']) echo ' selected ';
                  echo '>'.$mypad.$row_select['eidos_parastatikou_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>
          

          <div class="form-group row">
            <label for="eidos_parastatikou_need_prev" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απαιτεί άλλο παραστατικό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_need_prev" value="1" <?php if ($row['eidos_parastatikou_need_prev']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div> 
          <div class="form-group row">
            <label for="eidos_parastatikou_has_fpa" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει ΦΠΑ');?>:</label>
            <div class="col-md-8">
              <select id="eidos_parastatikou_has_fpa" class="form-control form-control-sm myneedsave" style="max-width:150px;">
                <option value="0" <?php if ($row['eidos_parastatikou_has_fpa']==0) echo 'selected';?>><?php echo gks_lang('Όχι');?></option>
                <option value="1" <?php if ($row['eidos_parastatikou_has_fpa']==1) echo 'selected';?>><?php echo gks_lang('Ναι');?></option>
                <option value="2" <?php if ($row['eidos_parastatikou_has_fpa']==2) echo 'selected';?>><?php echo gks_lang('Όχι').' ('.gks_lang('ειδικό');?>)</option>
              </select>
            </div>
          </div>           
          <div class="form-group row">
            <label for="eidos_parastatikou_has_posotita" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει Ποσότητες');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_posotita" value="1" <?php if ($row['eidos_parastatikou_has_posotita']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div> 
          
          <?php
          $temp=trim_gks($row['eidos_parastatikou_has_othertaxes']);
          $othertaxes=explode(',',$temp);
          
          ?>
          <div class="form-group row">
            <label for="eidos_parastatikou_has_othertaxes_wh" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόροι Παρακρατούμενοι');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_othertaxes_wh" value="1" <?php if (in_array('wh',$othertaxes)) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>           
          <div class="form-group row">
            <label for="eidos_parastatikou_has_othertaxes_ot" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Λοιποί Φόροι');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_othertaxes_ot" value="1" <?php if (in_array('ot',$othertaxes)) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          <div class="form-group row">
            <label for="eidos_parastatikou_has_othertaxes_sd" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_othertaxes_sd" value="1" <?php if (in_array('sd',$othertaxes)) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          <div class="form-group row">
            <label for="eidos_parastatikou_has_othertaxes_fe" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τέλη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_othertaxes_fe" value="1" <?php if (in_array('fe',$othertaxes)) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          <div class="form-group row">
            <label for="eidos_parastatikou_has_othertaxes_dd" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κρατήσεις','part2');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_othertaxes_dd" value="1" <?php if (in_array('dd',$othertaxes)) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_has_esoda" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει έσοδα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_esoda" value="1" <?php if ($row['eidos_parastatikou_has_esoda']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_has_eksoda" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει έξοδα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_has_eksoda" value="1" <?php if ($row['eidos_parastatikou_has_eksoda']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_need_afm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απαιτεί ΑΦΜ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_need_afm" value="1" <?php if ($row['eidos_parastatikou_need_afm']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_balance_pros" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρόσημο υπολοίπου επαφής');?>:</label>
            <div class="col-md-8">
              <select id="eidos_parastatikou_balance_pros" class="form-control form-control-sm myneedsave" style="max-width:150px;">
                <option value="0"     <?php if ($row['eidos_parastatikou_balance_pros']==0)     echo 'selected';?>></option>
                <option value="1"     <?php if ($row['eidos_parastatikou_balance_pros']==1)     echo 'selected';?>>+</option>
                <option value="-1"    <?php if ($row['eidos_parastatikou_balance_pros']==-1)    echo 'selected';?>>-</option>
                <option value="-100"  <?php if ($row['eidos_parastatikou_balance_pros']==-100)  echo 'selected';?>><?php echo gks_lang('Ειδικό');?></option>
              </select>
            </div>
          </div>           
          <div class="form-group row">
            <label for="eidos_parastatikou_stock_pros" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρόσημο υπολοίπου είδους');?>:</label>
            <div class="col-md-8">
              <select id="eidos_parastatikou_stock_pros" class="form-control form-control-sm myneedsave" style="max-width:150px;">
                <option value="0"     <?php if ($row['eidos_parastatikou_stock_pros']==0)     echo 'selected';?>></option>
                <option value="1"     <?php if ($row['eidos_parastatikou_stock_pros']==1)     echo 'selected';?>>+</option>
                <option value="-1"    <?php if ($row['eidos_parastatikou_stock_pros']==-1)    echo 'selected';?>>-</option>
              </select>
            </div>
          </div>          
          
          <div class="form-group row">
            <label for="credit_acc_eidos_parastatikou_id" class="col-sm-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Το πιστωτικό του είναι');?>:</label>
            <div class="col-sm-8">
              <select id="credit_acc_eidos_parastatikou_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <option value="-1" <?php if ($row['credit_acc_eidos_parastatikou_id']==-1) echo ' selected ';?>
                >--<?php echo gks_lang('Ειδικό');?>--</option>
                
                <?php
                $lang_prepare_gks_acc_eidi_parastatikon=gks_lang_data_obj_prepare('gks_acc_eidi_parastatikon','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_acc_eidi_parastatikon, array('eidos_parastatikou_descr'));
                $sql="select id_acc_eidos_parastatikou,".gks_lang_sql_field('eidos_parastatikou_descr',$lang_prepare_gks_acc_eidi_parastatikon)." 
                FROM ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from1']." gks_acc_eidi_parastatikon 
                ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from2']."
                where aade_disable=0 and is_selectable=1 and id_acc_eidos_parastatikou<>".$id."
                order by sortorder";
                //echo $sql;die();
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" data-mypad="'.$mypad.'" ';
                  if ($row_select['id_acc_eidos_parastatikou']==$row['credit_acc_eidos_parastatikou_id']) echo ' selected ';
                  echo '>'.$row_select['eidos_parastatikou_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="eidos_parastatikou_whi_type_id" class="col-sm-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Το δελτίο του είναι');?>:</label>
            <div class="col-sm-8">
              <select id="eidos_parastatikou_whi_type_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <option value="-1" <?php if ($row['eidos_parastatikou_whi_type_id']==-1) echo ' selected ';?>
                >--<?php echo gks_lang('Ειδικό');?>--</option>
                
                <?php
                $lang_prepare_gks_acc_eidi_parastatikon=gks_lang_data_obj_prepare('gks_acc_eidi_parastatikon','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_acc_eidi_parastatikon, array('eidos_parastatikou_descr'));
                $sql="select id_acc_eidos_parastatikou,".gks_lang_sql_field('eidos_parastatikou_descr',$lang_prepare_gks_acc_eidi_parastatikon)." 
                FROM ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from1']." gks_acc_eidi_parastatikon 
                ".$lang_prepare_gks_acc_eidi_parastatikon['sql']['from2']."
                where aade_disable=0 and is_selectable=1 and id_acc_eidos_parastatikou<>".$id."
                order by sortorder";
                //echo $sql;die();
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" data-mypad="'.$mypad.'" ';
                  if ($row_select['id_acc_eidos_parastatikou']==$row['eidos_parastatikou_whi_type_id']) echo ' selected ';
                  echo '>'.$row_select['eidos_parastatikou_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>          
          
          <div class="form-group row">
            <label for="eidos_parastatikou_other_entity" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_other_entity" value="1" <?php if ($row['eidos_parastatikou_other_entity']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_correlated_invoices" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συσχετιζόμενα Παραστατικά');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_correlated_invoices" value="1" <?php if ($row['eidos_parastatikou_correlated_invoices']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_multiple_connected_marks" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_multiple_connected_marks" value="1" <?php if ($row['eidos_parastatikou_multiple_connected_marks']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="eidos_parastatikou_packings_declarations" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πληροφορίες Συσκευασίας Διακίνησης');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eidos_parastatikou_packings_declarations" value="1" <?php if ($row['eidos_parastatikou_packings_declarations']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          



          
          <div class="form-group row">
            <label for="is_selectable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μπορεί να επιλεγεί');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_selectable" value="1" <?php if ($row['is_selectable']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="eidos_parastatikou_aade_code"><?php echo gks_lang('Κωδικός ΑΑΔΕ');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="eidos_parastatikou_aade_code"  value="<?php echo htmlspecialchars_gks($row['eidos_parastatikou_aade_code']);?>" style="max-width:150px;">
            </div>
          </div>           
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="import_apo_allon"><?php echo gks_lang('Εισαγωγή από άλλον τύπο ΑΑΔΕ');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="import_apo_allon"  value="<?php echo htmlspecialchars_gks($row['import_apo_allon']);?>"  style="max-width:150px;" placeholder="<?php echo gks_lang('π.χ.');?> [5.1][5.2]">
            </div>
          </div>          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
         
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="peppol_code"><?php echo gks_lang('Peppol');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="peppol_code"  value="<?php echo htmlspecialchars_gks($row['peppol_code']);?>"  style="max-width:150px;">
            </div>
          </div>          
          <div class="form-group row">
            <label for="sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sortorder']);?>" min="1" strep="1"  style="max-width:150px;">
            </div>
          </div>


          <div class="form-group row">
            <label for="aade_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="aade_disable" value="1" <?php if ($row['aade_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>           
          
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_acc_eidos_parastatikou'];?>" data-model="gks_acc_eidi_parastatikon" data-backurl="admin-aade-eidi-parastatikon.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>            
  </div>            
</div>            

            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       


          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_acc_eidos_parastatikou']>0) echo $row['id_acc_eidos_parastatikou'];?></span></div>
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

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<?php include_once('_dialogs.php'); ?>
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_aade','delete',$id);?>;

  
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
    datasend+='&eidos_parastatikou_aade_code='  +  encodeURIComponent($.base64.encode($("#mypostform #eidos_parastatikou_aade_code").val().trim()));
    datasend+='&eidos_parastatikou_descr='  +  encodeURIComponent($.base64.encode($("#mypostform #eidos_parastatikou_descr").val().trim()));
    datasend+='&sortorder='  +  encodeURIComponent($("#mypostform #sortorder").val().trim());
    datasend+='&aade_disable='  +  (($('#mypostform #aade_disable').is(':checked')) ? '0':'1');
    datasend+='&peppol_code='  +  encodeURIComponent($("#mypostform #peppol_code").val().trim());


    datasend+='&parent_id='  +  encodeURIComponent($("#mypostform #parent_id").val().trim());
    datasend+='&eidos_parastatikou_type_id='  +  encodeURIComponent($("#mypostform #eidos_parastatikou_type_id").val().trim());

    datasend+='&eidos_parastatikou_need_prev=' + (($('#mypostform #eidos_parastatikou_need_prev').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_fpa=' + encodeURIComponent($("#mypostform #eidos_parastatikou_has_fpa").val().trim());
    datasend+='&eidos_parastatikou_has_posotita='  +(($('#mypostform #eidos_parastatikou_has_posotita').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_othertaxes_wh='  +  (($('#mypostform #eidos_parastatikou_has_othertaxes_wh').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_othertaxes_ot='  +  (($('#mypostform #eidos_parastatikou_has_othertaxes_ot').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_othertaxes_sd='  +  (($('#mypostform #eidos_parastatikou_has_othertaxes_sd').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_othertaxes_fe='  +  (($('#mypostform #eidos_parastatikou_has_othertaxes_fe').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_othertaxes_dd='  +  (($('#mypostform #eidos_parastatikou_has_othertaxes_dd').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_esoda='  + (($('#mypostform #eidos_parastatikou_has_esoda').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_has_eksoda='  +  (($('#mypostform #eidos_parastatikou_has_eksoda').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_need_afm='  + (($('#mypostform #eidos_parastatikou_need_afm').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_balance_pros='  +  encodeURIComponent($("#mypostform #eidos_parastatikou_balance_pros").val().trim());
    datasend+='&eidos_parastatikou_stock_pros='  +  encodeURIComponent($("#mypostform #eidos_parastatikou_stock_pros").val().trim());
    datasend+='&credit_acc_eidos_parastatikou_id='  +  encodeURIComponent($("#mypostform #credit_acc_eidos_parastatikou_id").val().trim());
    datasend+='&eidos_parastatikou_whi_type_id='  +  encodeURIComponent($("#mypostform #eidos_parastatikou_whi_type_id").val().trim());
    datasend+='&eidos_parastatikou_other_entity='  + (($('#mypostform #eidos_parastatikou_other_entity').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_correlated_invoices='  + (($('#mypostform #eidos_parastatikou_correlated_invoices').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_multiple_connected_marks='  + (($('#mypostform #eidos_parastatikou_multiple_connected_marks').is(':checked')) ? '1':'0');
    datasend+='&eidos_parastatikou_packings_declarations='  + (($('#mypostform #eidos_parastatikou_packings_declarations').is(':checked')) ? '1':'0');
    datasend+='&is_selectable='  + (($('#mypostform #is_selectable').is(':checked')) ? '1':'0');
    datasend+='&import_apo_allon='+encodeURIComponent($.base64.encode($("#mypostform #import_apo_allon").val().trim()));


    
    datasend+=gks_lang_data_obj_input_collect();
    //console.log(datasend);
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-aade-eidi-parastatikon-item-exec.php?id=' + <?php echo $id;?>,
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
  
  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
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


