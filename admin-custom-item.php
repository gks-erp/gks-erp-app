<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$nav_active_array=array('manage','manage_settings','manage_custom');
db_open();
$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_custom_table',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_custom_table']=-1;
  $row['custom_table_descr']='';
  $row['custom_table_name']='';
  $row['field_name_id_parent']='';
  $row['field_name_id_current']='';
  $row['custom_table_disabled']=0;
  $row['custom_priv']='';
  $row['custom_sortorder']=0;
  $row['num_columns']=0;

  $row['erp_app_id']=0;
  $row['erp_app_dest']='printer';
  $row['erp_app_dest_printer_method']=1;
  $row['erp_app_dest_printer']='';
  $row['erp_app_dest_printer_lpr_ip']='';
  $row['erp_app_dest_printer_copies']=1;
  $row['erp_app_dest_folder']='';
  $row['erp_app_filter']='';
    
  $my_page_title=gks_lang('Νέο αντικείμενο');
} else {


  $sql ="SELECT gks_custom_table.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit  
  FROM (gks_custom_table
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_custom_table.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_custom_table.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where ((custom_table_disabled=0 and id_custom_table<10000) or (id_custom_table>=10000))
  and id_custom_table = ".$id;
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
}
$id_custom_table=$row['id_custom_table'];
$custom_table_name=$row['custom_table_name'];
$card_name_settings=[];
$temp=trim_gks($row['card_name_settings']);
if ($temp!='') $card_name_settings=json_decode($temp,true);
$unique_names=[];
foreach ($card_name_settings as $value) $unique_names[$value['dbname']]=$value['name'];

//print '<pre>';print_r($unique_names);print_r($card_name_settings);die();


$my_page_title=gks_lang('Προσαρμογή αντικειμένου').': '.$row['custom_table_descr'];
$object_title=$row['custom_table_descr'];

$sql_fields="SELECT gks_custom_field.id_custom_field, gks_custom_field.field_label, 
gks_custom_field.field_type_id, gks_custom_field_type.field_type_sql, gks_custom_field_type.field_type_collate, gks_custom_field_type.field_type_index,
gks_custom_field.field_default_value, gks_custom_field.field_default_value as field_default_value_db,
gks_custom_field.field_allow_null, gks_custom_field.field_allow_null as field_allow_null_db,
gks_custom_field.field_attr,
gks_custom_field.field_card_name,
gks_custom_field.field_show_on_list
FROM gks_custom_field LEFT JOIN gks_custom_field_type ON gks_custom_field.field_type_id = gks_custom_field_type.id_custom_field_type
WHERE gks_custom_field.custom_table_id=".$id_custom_table." 
AND gks_custom_field.field_disabled=0 
AND gks_custom_field_type.id_custom_field_type Is Not Null
AND gks_custom_field_type.field_type_notdevyet=0
order by gks_custom_field.field_card_name,gks_custom_field.field_sortorder";
$result_fields = $db_link->query($sql_fields);        
if (!$result_fields) { 
  debug_mail(false,'error sql',$sql_fields);
  echo 'sql error';die();}
$fields=array();
while ($row_fields = $result_fields->fetch_assoc()) {
  if (trim_gks($row_fields['field_attr'])) {
    $row_fields['field_attr']=unserialize($row_fields['field_attr']);
  } else {
    $row_fields['field_attr']=array();
  }
  $row_fields['field_card_name']=trim_gks($row_fields['field_card_name']);
  if ($row_fields['field_card_name']=='') $row_fields['field_card_name']=gks_lang('Προσαρμοσμένα');
  
  if (isset($unique_names[$row_fields['field_card_name']])) {
    $row_fields['field_card_name']=$unique_names[$row_fields['field_card_name']];
  } 
  
  
  $fields[$row_fields['field_card_name']][]=$row_fields;
}
//echo '<pre>';print_r($fields);die();

$sql_ftypes="select id_custom_field_type as id,field_type_name as descr,field_type_group as myg
from gks_custom_field_type 
where field_type_notdevyet=0
order by field_type_group,field_type_sortorder";
$result_ftypes = $db_link->query($sql_ftypes);        
if (!$result_ftypes) { 
  debug_mail(false,'error sql',$sql_ftypes);
  echo 'sql error';die();}
$ftypes=array();
$prev_myg=0;
while ($row_ftypes = $result_ftypes->fetch_assoc()) {
  $row_ftypes['descr']=gks_lang($row_ftypes['descr'],'part4','field_type_name');
  
  if (isset($ftypes[$row_ftypes['myg']])==false) {
    $gdescr='';
    if ($row_ftypes['myg']==1)        $gdescr=gks_lang('Απλά πεδία'); 
    else if ($row_ftypes['myg']==20)  $gdescr=gks_lang('web πεδία'); 
    else if ($row_ftypes['myg']==50)  $gdescr=gks_lang('Πεδία επιλογών'); 
    else if ($row_ftypes['myg']==100) $gdescr=gks_lang('Αντικείμενα - Επιλογή ενός'); 
    else if ($row_ftypes['myg']==200) $gdescr=gks_lang('Αντικείμενα - Επιλογή πολλών'); 
    
    
    $ftypes[$row_ftypes['myg']]=array('gdescr'=>$gdescr,'ft'=>array());
  }
  
  $ftypes[$row_ftypes['myg']]['ft'][]=$row_ftypes;
}
//print '<pre>';print_r($ftypes);print '</pre>';die();
  
stat_record();

$class_container_above1000='';
if ($id==-1 or $id>=10000) {
  $class_container_above1000='gks_container_above1000';
}

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Προσαρμογή αντικειμένου');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Προσαρμογή αντικειμένου');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid <?php echo $class_container_above1000;?>" id="mypostform">
  <div class="row">
    <div class="col-md-6">
       
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 
    
          <div class="form-group row">
            <label for="custom_table_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="custom_table_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['custom_table_descr']);?>" <?php if ($id<10000 and $id!=-1) echo 'disabled';?>>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="num_columns" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Στήλες');?>:</label>
            <div class="col-md-8">
              <?php if ($id<10000 and $id!=-1) $row['num_columns']=1;?>
              <select id="num_columns" class="form-control form-control-sm myneedsave" <?php if ($id<10000 and $id!=-1) echo 'disabled';?> style="max-width:100px;">
                <option value="1" <?php if ($row['num_columns']==1) echo 'selected';?>>1</option>
                <option value="2" <?php if ($row['num_columns']==2) echo 'selected';?>>2</option>
                <option value="3" <?php if ($row['num_columns']==3) echo 'selected';?>>3</option>
                <option value="4" <?php if ($row['num_columns']==4) echo 'selected';?>>4</option>
                <option value="6" <?php if ($row['num_columns']==6) echo 'selected';?>>6</option>
              </select>
              <small class="form-text text-muted"><?php echo gks_lang('Αφορά την προβολή της εγγραφής');?></small>
            </div>
          </div>          
          <div class="form-group row">
            <label for="custom_table_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="custom_table_disabled" value="1" <?php if ($row['custom_table_disabled']==0) echo ' checked '; ?> class="switchery1_this" <?php if ($id<10000 and $id!=-1) echo 'disabled';?>>
            </div>
          </div>
                        
        </div>
      </div>
      
      <?php if ($id==-1 or $id>=10000) {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('gks ERP App Desktop');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpapp');?>> 

          <div class="form-group row">
            <label for="erp_app_id_check" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή στην gks ERP App Desktop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_id_check" value="1" <?php if ($row['erp_app_id']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <?php
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
          <!-- fixme the filters-->
          <div class="form-group row div_erp_app_id_check_only" style="display:none;<?php if (!($row['erp_app_id']>0)) echo 'display:none;';?>">
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
              <input id="erp_app_dest_printer_lpr_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_printer_lpr_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.70">
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
              <input id="erp_app_dest_folder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_dest_folder']);?>" placeholder="<?php echo gks_lang('π.χ.');?> c:\printer\folder\">
            </div>
          </div>

        </div>
      </div>
      <?php } ?>      
      
    </div> 
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κάρτες και Πεδία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('fields');?>> 
<?php 


if (count($fields)==0) {
  $fields[gks_lang('Προσαρμοσμένα')]=array();
}

$cc=0;
$aa=0;
foreach ($fields as $mycard_name => $mycard_fields) {
  $cc++;
  
  echo 
          '<div class="card gks_card_expand gks_card_group_fields" data-cc="'.$cc.'">'.
            '<div class="card-header" style="text-align:center">'.
              '<input id="field_card_name_'.$cc.'" data-cc="'.$cc.'" type="text" class="gks_div_custom_field_card_name gks_stoppropagation form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($mycard_name).'">'.
            '</div>'.
            '<div class="card-body gks_section_title" '.gks_card_body('cf_c_'.$cc).'>';
  
  echo    '<div class="gks_section_settings">'.
            '<div class="form-group row">'.
               '<label for="field_card_name_sortorder'.$cc.'" class="col-lg-2 col-form-label form-control-sm text-lg-right">'.gks_lang('Σειρά').':</label>'.
               '<div class="col-lg-4">'.
                 '<input id="field_card_name_sortorder'.$cc.'" value="'.($cc*10).'"  data-cc="'.$cc.'" type="number" class="gks_div_custom_field_card_name_sortorder form-control form-control-sm myneedsave" min="1">'.
               '</div>'.
               '<label for="field_card_name_width'.$cc.'" class="col-lg-2 col-form-label form-control-sm text-lg-right">'.gks_lang('Πλάτος').':</label>'.
               '<div class="col-lg-4">'.
                 '<select id="field_card_name_width'.$cc.'" data-cc="'.$cc.'" type="number" class="gks_div_custom_field_card_name_width form-control form-control-sm myneedsave">';
  $temp=6;
  foreach ($card_name_settings as $vset) {
    if ($vset['name']==$mycard_name) {
      $temp=$vset['width'];
      break;
    }
  } 
  //echo '<pre>sssssss ';print_r($card_name_settings);die();
  for ($bsw=1;$bsw<=12;$bsw++) {
    echo '<option value="'.$bsw.'" '.($bsw==$temp ? 'selected' : '').'>'.$bsw.' / 12</option>';
  }                
  echo           '</select>'.
               '</div>'.
            '</div>'.
          '</div>';
  
  
  echo        '<div class="connectedSortable">';
  

  
  
  foreach ($mycard_fields as $myf) {
    $aa++;
          echo 
                '<div 
                class="gks_div_custom_field"
                data-aa="'.$aa.'" 
                data-rec-id="'.$myf['id_custom_field'].'"
                >'.
                  '<div class="gks_div_custom_field_handle" data-aa="'.$aa.'"><i class="fas fa-arrows-alt-v"></i></div>'.
                  '<div class="gks_div_custom_field_text"   data-aa="'.$aa.'">'.
                    '<input id="field_label_'.$aa.'" data-aa="'.$aa.'" type="text" class="gks_div_custom_field_label form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($myf['field_label']).'">'.
                  '</div>'.
                  
                  '<div class="gks_div_custom_field_expand" data-aa="'.$aa.'"><i class="fas fa-angle-double-down gks_div_custom_field_expand_icon" style="transform: rotate(0deg);"></i></div>'.
                  '<div class="gks_div_custom_field_remove" data-aa="'.$aa.'"><i class="fas fa-trash-alt         gks_div_custom_field_remove_icon"></i></div>'.
                  '<div class="gks_div_custom_field_add"    data-aa="'.$aa.'"><i class="fas fa-plus-circle       gks_div_custom_field_add_icon"   ></i></div>'.
                  '<div class="gks_div_custom_field_properties" data-aa="'.$aa.'" style="display:none;">';

    echo            '<div class="form-group row">'.
                      '<label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('DB Field Name').':</label>'.
                      '<label class="col-md-8 col-form-label form-control-sm text-md-right1">'.
                      'cf'.$myf['id_custom_field'].
                      '</label>'.
                    '</div>';
                  
    echo            '<div class="form-group row">'.
                      '<label for="field_type_id_'.$aa.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Τύπος').':</label>'.
                      '<div class="col-md-8">'.
                        '<select id="field_type_id_'.$aa.'" data-aa="'.$aa.'" class="gks_div_custom_field_type_id form-control form-control-sm myneedsave" >'.
                          '<option value="0"></option>';
                          $temp='';
                          foreach ($ftypes as $myg) {
                            $temp.='<optgroup label="'.$myg['gdescr'].'">';
                            foreach ($myg['ft'] as $myt) {
                              $temp.='<option value="'.$myt['id'].'"'.
                              ($myt['id']==$myf['field_type_id'] ? ' selected ' : '').
                              '>'.$myt['descr'].'</option>';
                            } 
                            $temp.='</optgroup>';
                          } 
                          echo $temp;
    echo                '</select>'.                  
                      '</div>'.
                    '</div>';                  

                    
                    
    echo            '<div class="form-group row" style="'.
                      (($myf['field_type_id']==501 or $myf['field_type_id']==502) ? '' : 'display:none;').
                      '">'.
                      '<label for="field_attr_options_'.$aa.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Τιμές').':</label>'.
                      '<div class="col-md-8">'.
                      
                        '<table data-aa="'.$aa.'" class="gks_div_custom_field_attr_options table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="field_attr_options_'.$aa.'">'.
                          '<thead>'.
                            '<tr>'.
                              '<th class="table-dark" scope="col" width="0%" nowrap="">#</th>'.
                              '<th class="table-dark gks_div_custom_field_attr_options_th_value" scope="col" nowrap="" '.
                              'style="width:50%;'.($myf['field_type_id']==502 ? 'display:none' : '').'" '.
                              '>'.gks_lang('Τιμή').'</th>'.
                              '<th class="table-dark gks_div_custom_field_attr_options_th_text" scope="col" style="width:'.
                              ($myf['field_type_id']==502 ? '100' : '50').
                              '%;" nowrap="">'.gks_lang('Περιγραφή').'</th>'.
                              '<th class="table-dark" scope="col" width="0%" nowrap="">'.gks_lang('Ενέργεια').'</th>'.
                            '</tr>'.
                          '</thead>'.
                          '<tbody>';
                          
                      if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options']) and count($myf['field_attr']['options'])>0) {
                        $occ=0;
                        foreach ($myf['field_attr']['options'] as $myopt) {
                          //501 epilogi enos apo lista
                          //502 epilogi pollon apo lista
                          $occ++;
    echo                    '<tr>'.
                              '<th scope="row" nowrap="" class="gks_div_custom_field_attr_options_td_ii">'.$occ.'</th>'.
                              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_value" style="'.($myf['field_type_id']==502 ? 'display:none' : '').'">'.
                                '<input type="text" class="form-control form-control-sm myneedsave" value="'.
                                  ($myf['field_type_id']==501 ? $myopt['value'] : '').
                                '"/>'.
                              '</td>'.
                              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_text">'.
                                '<input type="text" class="form-control form-control-sm myneedsave" value="'.
                                  htmlspecialchars_gks($myf['field_type_id']==501 ? $myopt['text'] : $myopt).
                                '"/>'.
                              '</td>'.
                              '<td nowrap class="mytdcm">'.
                                '<i class="fas fa-trash-alt     gks_div_custom_field_attr_options_td_value_remove"></i>'.
                                '<i class="fas fa-plus-circle   gks_div_custom_field_attr_options_td_value_add"   ></i>'.
                              '</td>'.
                            '</tr>';
                        }
                      }        

    echo                  '</tbody>'.   
                        '</table>'.                      
                      '</div>'.
                    '</div>';

    echo            '<div class="form-group row">'.
                      '<label for="field_allow_null_'.$aa.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Απαιτείται').':</label>'.
                      '<div class="col-md-8">'.
                        '<input '.($myf['field_allow_null']==0 ? 'checked ' : '').' id="field_allow_null_'.$aa.'" data-aa="'.$aa.'" type="checkbox" class="gks_div_custom_field_allow_null switchery1_this" value="1">'.
                      '</div>'.
                    '</div>';

    echo            '<div class="form-group row">'.
                      '<label for="field_default_value_'.$aa.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Προεπιλεγμένη τιμή').':</label>'.
                      '<div class="col-md-8">'.
                        '<input id="field_default_value_'.$aa.'" data-aa="'.$aa.'" type="text" class="gks_div_custom_field_default_value form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($myf['field_default_value']).'">'.
                      '</div>'.
                    '</div>';



    echo            '<div class="form-group row">'.
                      '<label for="field_show_on_list_'.$aa.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Εμφάνιση στην προβολή λίστας').':</label>'.
                      '<div class="col-md-8">'.
                        '<input '.($myf['field_show_on_list']!=0 ? 'checked ' : '').' id="field_show_on_list_'.$aa.'" data-aa="'.$aa.'" type="checkbox" class="gks_div_custom_field_show_on_list switchery1_this" value="1">'.
                      '</div>'.
                    '</div>';


                    
    echo          '</div>'.
                   
                '</div>';
  }
  

          
  echo        '</div>';
  
  //if (count($mycard_fields)==0) {
    echo '<div style="text-align:center;"><i class="fas fa-plus-circle gks_div_card_add_field"></i></div>';
    
  //}  
  echo      '</div>'.
          '</div>';
}

    echo '<div style="text-align:center;"><i class="fas fa-plus-circle gks_div_card_add_card"></i></div>';

?>          

        </div>
      </div>

         
      
      
      

      
    </div>

 
  </div>
</div>
          

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid <?php echo $class_container_above1000;?>" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if (!empty($row['obj_url'])) {?>
      <a class="btn button_custom_table_view" href="<?php echo $row['obj_url'];?>"><?php echo gks_lang('Προβολή');?></a>
      <?php } ?>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid <?php echo $class_container_above1000;?>">
  <div class="row">
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_custom_table',$id);
      echo getActivityObjectTable('gks_custom_table',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_custom_table','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_custom_table']>0) echo $row['id_custom_table'];?></span></div>
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
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;


var from_php_dialog_object_rel_curr='gks_custom_table';
var from_php_activity_model='gks_custom_table';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



//var gks_ftypes11=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($ftypes));?>'));
var gks_ftypes=[] ;//JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($ftypes));?>'));

<?php
foreach ($ftypes as $value) {
  echo 'ft=[];'."\n";
  foreach ($value['ft'] as $myft) {
    echo 'ft.push({id:'.$myft['id'].',descr: \''.$myft['descr'].'\'});'."\n";
  } 
  echo 'gks_ftypes.push({gdescr:\''.$value['gdescr'].'\',ft:ft});'."\n";
} 

?>



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','delete',$id);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  var control_enter_active=false;
  
  $(document).on('keypress', function(event) {
    //var tag = e.target.tagName.toLowerCase();
    
    
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      
      elem=$('#submit_button_ok');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });
        



  //generic
  gks_page_loading=false;
  



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


<script src="js/admin-custom-item.js?v=<?php echo $gks_cache_version;?>"></script>
<link href="css/admin-custom-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


