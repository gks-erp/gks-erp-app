<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//https://test.easyfilesselection.com/my/admin-assets-item.php?id=-1
$nav_active_array=array('assets','assets_service');

db_open();
$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_assets_service_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_service','delete',0);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets_service',['from'=>'item']);
if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  
  $row['mydate_send']=date('Y-m-d H:i');
  $row['asset_id']=0;
  $row['asset_code']='';
  $row['asset_title']='';
  $row['asset_serialnumber']='';
  $row['warehouse_id']=0;
  $row['warehouse_name']='';
  $row['reason_id']=0;
  $row['reasons_descr']='';
  $row['aitiolog']='';
  $row['mixanikos_id']=0;
  $row['gks_nickname']='';
  //$row['mydate_return']=null;
  $row['aitiolog2']='';
  $row['ajia']=0;
  $row['isconfirm']=0;
  $row['asset_type']=0;
  
  if (isset($_GET['asset_id'])) {
    $temp_id=intval($_GET['asset_id']); 
    if ($temp_id>0) {
      $sql_template ="SELECT gks_assets.*, gks_assets_type.asset_type_descr,gks_warehouses.warehouse_name
      FROM (gks_assets 
      LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type)
      LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse
      where gks_assets.id_asset = ".$temp_id;
      $result_template = $db_link->query($sql_template);        
      if (!$result_template) {debug_mail(false,'error sql',$sql_template);die('sql error');}
      if ($result_template->num_rows==1) {
        $row_template = $result_template->fetch_assoc();    
        $row['asset_id']=$row_template['id_asset'];
        $row['asset_code']=$row_template['asset_code'];
        $row['asset_title']=$row_template['asset_title'];
        $row['asset_serialnumber']=$row_template['asset_serialnumber'];
        $row['warehouse_id']=$row_template['asset_last_warehouse_id'];
        $row['warehouse_name']=$row_template['warehouse_name'];
        $row['asset_type']=$row_template['asset_type'];
        
      }  
    }
  }
  $my_page_title=gks_lang('Νέο Service Παγίου');   
} else {
  $sql ="SELECT gks_assets_service.*, gks_assets.asset_code, gks_assets.asset_title, gks_assets.asset_serialnumber, gks_assets.asset_type, 
  gks_assets_service_reasons.reasons_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_warehouses.warehouse_name, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_add,
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM ((((gks_assets_service 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons) 
  LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_assets_service.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_assets_service.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where gks_assets_service.id_assets_service = ".$id;
  
  //echo '<pre>'.$sql;die();
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
  $my_page_title=gks_lang('Service Παγίου').': '.$row['asset_title'];  
}
stat_record();

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$isconfirm=$row['isconfirm'];
$asset_id = $row['asset_id'];
$prev_asset_type= intval($row['asset_type']);



include_once('_my_header_admin.php');
?>



<link href="css/admin-assets-service-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Service Παγίου');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['asset_title'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Service Παγίου');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>



<?php 
$gks_flock='';
if ($isconfirm!=0) $gks_flock='form-control-sm gks_flock';
?>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 


          <div class="form-group row">
            <label for="mydate_send" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Αποστολής');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              if (isset($row['mydate_send'])) echo showDate(strtotime($row['mydate_send']), 'd/m/Y H:i:s', 1);
            } else { ?>
              <input id="mydate_send" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['mydate_send'])) echo  showDate(strtotime($row['mydate_send']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πάγιο');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              if ($row['asset_id']>0) echo '<a href="admin-assets-item.php?id='.$row['asset_id'].'">'.htmlspecialchars_gks($row['asset_code'].' - '.$row['asset_title'].' - '.$row['asset_serialnumber']).'</a>';
            } else { ?>
              <input id="asset_id" data-id="<?php echo $row['asset_id'];?>" type="text" class="form-control form-control-sm myneedsave" value="<?php if ($row['asset_id']>0) echo $row['asset_code'].' - '.$row['asset_title'].' - '.$row['asset_serialnumber']?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="warehouse_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποθήκη');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              if ($row['warehouse_id']>0) echo '<a href="admin-warehouse-item.php?id='.$row['warehouse_id'].'">'.htmlspecialchars_gks($row['warehouse_name']).'</a>';
            } else { ?>
              <input id="warehouse_id" data-id="<?php echo $row['warehouse_id'];?>" type="text" class="form-control form-control-sm myneedsave" value="<?php if ($row['warehouse_id']>0) echo $row['warehouse_name'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτία');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
                echo htmlspecialchars_gks($row['reasons_descr']);
            } else { ?>
              <select name="reason_id" id="reason_id" class="form-control form-control-sm myneedsave gks_select2">
              <option value="0"></option>
              <?php
              
              $sql="select reasons_id,type_id from gks_assets_service_reasons_types order by reasons_id,type_id";
               $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              $array_r_t=array();
              while ($row_select = $result_select->fetch_assoc()) {
                $array_r_t[$row_select['reasons_id']][]=$row_select['type_id'];
              }
             
              
              $sql="select * FROM gks_assets_service_reasons where assets_service_reason_disable=0 ORDER BY assets_service_reason_sortorder,reasons_descr ";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_assets_service_reasons'].'" data-types="';
                if (isset($array_r_t[$row_select['id_assets_service_reasons']])) {
                  foreach ($array_r_t[$row_select['id_assets_service_reasons']] as $value) {
                     echo '['.$value.']';
                  }   
                  
                }
                echo '"';
                if ($row_select['id_assets_service_reasons']==$row['reason_id']) echo ' selected ';
                echo '>'.$row_select['reasons_descr'].'</option>';
              }?></select>
              <?php } ?> 
                            
              
            </div>
          </div>                    
          <div class="form-group row">
            <label for="aitiolog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο Αποστολής');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
                echo nl2br_gks(htmlspecialchars_gks($row['aitiolog']));
            } else { ?>
              <textarea id="aitiolog" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php echo $row['aitiolog'];?></textarea>
            <?php } ?>
            </div>
          </div>
        
                  
        



        </div>
      </div>
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επιστροφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 

          <div class="form-group row">
            <label for="mixanikos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τεχνικός');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              echo htmlspecialchars_gks($row['gks_nickname']);
            } else { ?>
              <input id="mixanikos_id" data-id="<?php echo $row['mixanikos_id'];?>" type="text" class="form-control form-control-sm myneedsave" value="<?php if ($row['mixanikos_id']>0) echo $row['gks_nickname'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="mydate_return" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Επιστροφής');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              if (isset($row['mydate_return'])) echo showDate(strtotime($row['mydate_return']), 'd/m/Y H:i:s', 1);
            } else { ?>
              <input id="mydate_return" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['mydate_return'])) echo  showDate(strtotime($row['mydate_return']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="aitiolog2" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο Επιστροφής');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
                echo nl2br_gks(htmlspecialchars_gks($row['aitiolog2']));
            } else { ?>
              <textarea id="aitiolog2" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php echo $row['aitiolog2'];?></textarea>
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="ajia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αξία');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { 
              echo number_format($row['ajia'],2,',','.').'&euro;';
            } else { ?>
              <input id="ajia" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['ajia']>0) echo $row['ajia'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            <?php } ?>
            </div>
          </div>


          <div class="form-group row">
            <label for="isconfirm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιβεβαιωμένο');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($isconfirm != 0) { ?>
              <img src="img/<?php echo $row['isconfirm']!=0 ? "1" :"0";  ?>.png" border="0" width="16">
            <?php } else { ?>
              <input type="checkbox" id="isconfirm" value="1" <?php if ($row['isconfirm']!=0) echo ' checked '; ?> class="switchery1_this">
            <?php } ?>  
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

<?php if ($isconfirm==0) {?>
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_assets_service'];?>" data-model="gks_assets_service" data-backurl="admin-assets-service.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php } ?>
      <?php if (1==2 and $id>0) {?>
      <button type="button" class="btn btn-primary" id="submit_button_copy" onclick="window.location.href='admin-assets-item.php?id=-1&copy=<?php echo $id;?>'"><?php echo gks_lang('Δημιουργία αντιγράφου');?></button>
      <?php } ?>
    </div> 
  </div> 
</div> 
<?php } ?>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">


      
      <?php 
      echo getObjectRels('gks_assets_service',$id);   
      echo getActivityObjectTable('gks_assets_service',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_assets_service','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

      
    </div>
    <div class="col-xl-6">



      
    
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>  


          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['id_assets_service'])) echo $row['id_assets_service'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_add'])) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_edit'])) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['myip'])) echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>


        </div>      
      </div>        
            
    </div>
  </div>
</div>


    


<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Όλα τα service του παγίου');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('allserv');?>> 
<?php

$gks_custom_prepare_allservice = gks_custom_table_item_prepare('gks_assets_service',['from'=>'list']);

$sql_allservice = "SELECT SQL_CALC_FOUND_ROWS gks_assets_service.*,gks_assets.id_asset, gks_assets.asset_code, gks_assets.asset_photo ,
gks_assets.asset_title, gks_assets.asset_serialnumber, 
gks_assets_service_reasons.reasons_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_warehouses.warehouse_name, wp_users_edit.gks_nickname AS useredit
".$gks_custom_prepare_allservice['sql_all_list_sele']."  
FROM ".$gks_custom_prepare_allservice['sql_all_list_from']." ((((gks_assets_service 
".$gks_custom_prepare_allservice['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons) 
LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset) 
LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_edit ON gks_assets_service.user_id_edit = wp_users_edit.ID
where asset_id=".$row['asset_id']."
ORDER BY gks_assets_service.id_assets_service desc";

$result_allservice = $db_link->query($sql_allservice);        
if (!$result_allservice) debug_mail(false,'error sql',$sql_allservice);
if (!$result_allservice) die('sql error');
      
?>        
          <table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('ID');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Ημερομηνία<br>Αποστολής');?></th>   
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Αποθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Αιτία');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Σχόλιο<br>Αποστολής');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Τεχνικός');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Ημερομηνία<br>Επιστροφής');?></th>   
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Σχόλιο<br>Επιστροφής');?></th>  
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Αξία');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><span title="<?php echo gks_lang('Επιβεβαιωμένο');?>"><?php echo gks_lang('Επιβ.');?></span></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Χρήστης');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('IP');?></th>
          
              <?php 
              echo gks_custom_table_list_header($gks_custom_prepare_allservice, true);
              ?>
            
            </tr>        
          </thead>
          <tbody>
    <?php
    $row_rec=$row;
    $i = 0;
    while ($row = $result_allservice->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php if ($row['id_assets_service']==$id) echo 'current_row'?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i );?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_assets_service'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-assets-service-item.php?id=<?php echo $row['id_assets_service'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_assets_service_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_assets_service'];?>" data-model="gks_assets_service"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td class="mytdcm" nowrap><?php echo showDate(strtotime($row['mydate_send']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    
    <td class="mytdcml"><?php echo '<a href="admin-warehouses-item.php?id='.$row['warehouse_id'].'">'.$row['warehouse_name'].'</a>';?></td>  
    <td class="mytdcml"><?php echo $row['reasons_descr']; ?></td>
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog'])); ?></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['mixanikos_id'].'">'.$row['gks_nickname'].'</a>';?></td>  
    <td class="mytdcm" nowrap><?php if (isset($row['mydate_return'])) echo showDate(strtotime($row['mydate_return']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog2'])); ?></td>
    <td class="mytdcmr" nowrap><?php if ($row['ajia']>0) echo number_format($row['ajia'],2,',','.'); ?></td>
    <td class="mytdcm" nowrap><img src="img/<?php echo $row['isconfirm']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['useredit'].'</a>';?></td>  
    
    

    <td class="mytdcml"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>">V</a> 




<?php
    echo gks_custom_table_list_rows($gks_custom_prepare_allservice,$row);
?>  
  </tr>
<?php    
    }
    $row=$row_rec;
?>          
          
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>










 
<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_dialog_object_rel_curr='gks_assets_service';
var from_php_activity_model='gks_assets_service';
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


var from_php_perm_ret_edit  =<?php if ($isconfirm) echo 'false'; else echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service','delete',$id);?>;


var from_php_prev_asset_type=<?php echo $prev_asset_type;?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});

</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>


<script src="js/admin-assets-service-item.js?v=<?php echo $gks_cache_version;?>"></script>


<link rel="stylesheet" href="/my/js/jquery.fileupload/jquery.fileupload.css" type="text/css">    



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');

