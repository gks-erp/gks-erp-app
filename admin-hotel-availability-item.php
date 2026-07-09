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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_availability',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$user_hotels=gks_get_hotels_list();

$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_availability',['from'=>'item']);



if ($id==-1) {
  

  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_hotel_availability']=-1;
  $row['hotel_id']=0;
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {$row['hotel_id']=$value['id']; break;}
  
  $row['hotel_room_type_id']=0;
  $row['hotel_room_id']=0;
  //$row['availability_from']='';
  //$row['availability_to']='';
  $row['availability_descr']='';
  $row['availability_status']=1;
  $row['avail_weekday_de']=1;
  $row['avail_weekday_tr']=1;
  $row['avail_weekday_te']=1;
  $row['avail_weekday_pe']=1;
  $row['avail_weekday_pa']=1;
  $row['avail_weekday_sa']=1;
  $row['avail_weekday_ky']=1;
  
  //echo '<pre>';print_r($user_hotels);print_r($row);die();
  
  $my_page_title=gks_lang('Νέα Διαθεσιμότητα');
} else {
  $sql ="SELECT gks_hotel_availability.*, 
  gks_hotel_room_type.room_type_descr, gks_hotel_room.room_descr, 
  IFNULL(IFNULL(gks_hotel_room_type.room_type_descr, gks_hotel_room.room_descr),'hotel') as mydescr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM ((((gks_hotel_availability 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_availability.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
  LEFT JOIN gks_hotel_room ON gks_hotel_availability.hotel_room_id = gks_hotel_room.id_hotel_room) 
  LEFT JOIN gks_hotel_room_type as room_type_from_room ON gks_hotel_room.hotel_room_type_id = room_type_from_room.id_hotel_room_type) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_availability.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_availability.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_hotel_availability = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_availability.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Διαθεσιμότητα').': '.$row['mydescr'].((isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') ? ' - '. $row['availability_descr'] : '');
  $object_title=$row['mydescr'].((isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') ? ' - '. $row['availability_descr'] : '');
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('hotel','hotel_availability');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Διαθεσιμότητα');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Διαθεσιμότητα');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
        <div class="card-body"  <?php echo gks_card_body('bas');?>>         

          <div class="form-group row">
            <label for="hotel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ξενοδοχείο');?>:</label>
            <div class="col-md-8">
              <select id="hotel_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                foreach ($user_hotels as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ';
                  if ($row_select['id']==$row['hotel_id']) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>
                  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιλογή');?>:</label>
            <div class="col-md-8">
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype2" value="1" <?php if ($row['hotel_room_id']==0) echo ' checked '; ?> >
              <label class="form-check-label" for="selecttype2"><?php echo gks_lang('Τύπος Δωματίου');?></label>
              <br>
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype1" value="0" <?php if ($row['hotel_room_id']!=0) echo ' checked '; ?> >
              <label class="form-check-label" for="selecttype1"><?php echo gks_lang('Δωμάτιο');?></label>
            </div>
          </div>
          
          <div class="form-group row" id="selecttypediv1" style="<?php if ($row['hotel_room_id']==0) echo 'display:none;'; ?>">
            <label for="hotel_room_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δωμάτιο');?>:</label>
            <div class="col-md-8">
              <select name="hotel_room_id" id="hotel_room_id"  class="form-control form-control-sm">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_hotel_room ".
              (count($perm_id_hotel_ids)>0 ? ' where hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '').
              " ORDER BY room_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_hotel_room'].'" '.
                'data-hotel_id="'.$row_select['hotel_id'].'" ';
                if ($row_select['id_hotel_room']==$row['hotel_room_id']) echo ' selected ';
                echo '>'.$row_select['room_descr'].'</option>';
              }?></select>
            </div>
          </div>    
  
          <div class="form-group row" id="selecttypediv2" style="<?php if ($row['hotel_room_id']!=0) echo 'display:none;'; ?>">
            <label for="hotel_room_type_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος Δωματίου');?>:</label>
            <div class="col-md-8">
              <select name="hotel_room_type_id" id="hotel_room_type_id"  class="form-control form-control-sm">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_hotel_room_type ".
              (count($perm_id_hotel_ids)>0 ? ' where hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '').
              " ORDER BY room_type_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_hotel_room_type'].'" '.
                'data-hotel_id="'.$row_select['hotel_id'].'" ';
                if ($row_select['id_hotel_room_type']==$row['hotel_room_type_id']) echo ' selected ';
                echo '>'.$row_select['room_type_descr'].'</option>';
              }?></select>
            </div>
          </div>        
          <div class="form-group row">
            <label for="availability_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
            <div class="col-md-8">
              <input id="availability_from" type="text" class="form-control form-control-sm" value="<?php if (isset($row['availability_from'])) echo date('d/m/Y', strtotime($row['availability_from']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>        
          <div class="form-group row">
            <label for="availability_to" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
            <div class="col-md-8">
              <input id="availability_to" type="text" class="form-control form-control-sm" value="<?php if (isset($row['availability_to'])) echo date('d/m/Y', strtotime($row['availability_to']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>        
  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημέρες');?>:</label>
            <div class="col-md-8">
              <?php
              $seldays1=false;
              if ($row['avail_weekday_de']!=0 and $row['avail_weekday_tr']!=0 and $row['avail_weekday_te']!=0 and $row['avail_weekday_pe']!=0 and 
                  $row['avail_weekday_pa']!=0 and $row['avail_weekday_sa']!=0 and $row['avail_weekday_ky']!=0) $seldays1=true;
              ?>
              <div class="form-check">
              <input class="form-check-input" type="radio" name="availability_seldays" id="availability_seldays1" value="1" <?php if ($seldays1) echo ' checked '; ?> >
              <label class="form-check-label" for="availability_seldays1"><?php echo gks_lang('Όλες οι ημέρες');?></label>
              <br>
              <input class="form-check-input" type="radio" name="availability_seldays" id="availability_seldays2" value="2" <?php if ($seldays1 == false) echo ' checked '; ?> >
              <label class="form-check-label" for="availability_seldays2"><?php echo gks_lang('Κάποιες ημέρες');?></label>
              </div>
              <div class="form-check form-check-inline" id="availability_seldays2div" style="<?php if ($seldays1) echo 'display:none;'; ?>">
                <input type="checkbox" name="avail_weekday_de" id="avail_weekday_de" value="1" style="margin-left: 10px;" <?php if ($row['avail_weekday_de']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_de"><?php echo gks_lang('Δε','part3');?></label>
                <input type="checkbox" name="avail_weekday_tr" id="avail_weekday_tr" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_tr']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_tr"><?php echo gks_lang('Τρ','part3');?></label>
                <input type="checkbox" name="avail_weekday_te" id="avail_weekday_te" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_te']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_te"><?php echo gks_lang('Τε','part3');?></label>
                <input type="checkbox" name="avail_weekday_pe" id="avail_weekday_pe" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_pe']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_pe"><?php echo gks_lang('Πε','part3');?></label>
                <input type="checkbox" name="avail_weekday_pa" id="avail_weekday_pa" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_pa']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_pa"><?php echo gks_lang('Πα','part3');?></label>
                <input type="checkbox" name="avail_weekday_sa" id="avail_weekday_sa" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_sa']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_sa"><?php echo gks_lang('Σα','part3');?></label>
                <input type="checkbox" name="avail_weekday_ky" id="avail_weekday_ky" value="1" style="margin-left: 5px;"  <?php if ($row['avail_weekday_ky']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="avail_weekday_ky"><?php echo gks_lang('Κυ','part3');?></label>
                
              </div>
              
              
            </div>
          </div>
  
  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8">
              <input class="form-check-input111" type="radio" name="availability_status" id="availability_status0" value="0" <?php if ($row['availability_status']==0) echo ' checked '; ?> >
              <label class="form-check-label" for="availability_status0"><span class="hotel_availability_0"><?php echo gks_lang('Κλειστό');?></span></label>
              <br>
              <input class="form-check-input111" type="radio" name="availability_status" id="availability_status1" value="1" <?php if ($row['availability_status']!=0) echo ' checked '; ?> >
              <label class="form-check-label" for="availability_status1"><span class="hotel_availability_1"><?php echo gks_lang('Ανοιχτό');?></span></label>
            </div>
          </div>
            
          <div class="form-group row">
            <label for="availability_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <input id="availability_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['availability_descr']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
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
    </div>
  </div>
</div>

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hotel_availability'];?>" data-model="gks_hotel_availability" data-backurl="admin-hotel-availability.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_hotel_availability',$id);  
      echo getActivityObjectTable('gks_hotel_availability',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel_availability','id'=>$id));
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
            <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_hotel_availability'];?>"></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
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

var from_php_dialog_object_rel_curr='gks_hotel_availability';
var from_php_activity_model='gks_hotel_availability';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_availability','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_availability','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_availability','delete',$id);?>;


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
  
  
  $('#availability_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#availability_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  
 
    
  function mysubmit() {
    datasend='';
    datasend+='&hotel_id='  + encodeURIComponent($("#mypostform #hotel_id").val().trim());
    if ($('#mypostform #selecttype1').is(':checked')) {
      datasend+='&hotel_room_id='  + encodeURIComponent($("#mypostform #hotel_room_id").val().trim());
    } else {
      datasend+='&hotel_room_type_id='  + encodeURIComponent($("#mypostform #hotel_room_type_id").val().trim());
    }
   
    datasend+='&availability_from='  + encodeURIComponent($("#mypostform #availability_from").val().trim());
    datasend+='&availability_to='  + encodeURIComponent($("#mypostform #availability_to").val().trim());
    datasend+='&availability_descr='  + encodeURIComponent($.base64.encode($("#mypostform #availability_descr").val().trim()));
    datasend+='&availability_status='  + (($('#mypostform #availability_status1').is(':checked')) ? '1':'0');
    
    datasend+='&availability_seldays1='  + (($('#mypostform #availability_seldays1').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_de='  + (($('#mypostform #avail_weekday_de').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_tr='  + (($('#mypostform #avail_weekday_tr').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_te='  + (($('#mypostform #avail_weekday_te').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_pe='  + (($('#mypostform #avail_weekday_pe').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_pa='  + (($('#mypostform #avail_weekday_pa').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_sa='  + (($('#mypostform #avail_weekday_sa').is(':checked')) ? '1':'0');
    datasend+='&avail_weekday_ky='  + (($('#mypostform #avail_weekday_ky').is(':checked')) ? '1':'0');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-availability-item-exec.php?id=' + <?php echo $id;?>,
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
  
  $('#selecttype1').change(function() {
    $('#selecttypediv1').show();
    $('#selecttypediv2').hide();
  });
  $('#selecttype2').change(function() {
    $('#selecttypediv1').hide();
    $('#selecttypediv2').show();
  });
  
  $('#availability_seldays1').change(function() {
    $('#availability_seldays2div').hide();
  });
  $('#availability_seldays2').change(function() {
    $('#availability_seldays2div').show();
  });
  
  function hotel_id_change() {
    var hotel_id=parseInt($('#hotel_id').val());
    if (isNaN(hotel_id)) hotel_id=0;
    
    $('#hotel_room_type_id option').each(function() {
      val=parseInt($(this).val()); if (isNaN(val)) val=0;
      if (val!=0) { 
        val=parseInt($(this).attr('data-hotel_id'));if (isNaN(val)) val=0;
        if (val==hotel_id) $(this).show(); else $(this).hide(); 
      }    
    });
    if ($('#hotel_room_type_id option:selected').css('display') == 'none') $('#hotel_room_type_id').val('0');
    
    $('#hotel_room_id option').each(function() {
      val=parseInt($(this).val()); if (isNaN(val)) val=0;
      if (val!=0) { 
        val=parseInt($(this).attr('data-hotel_id'));if (isNaN(val)) val=0;
        if (val==hotel_id) $(this).show(); else $(this).hide(); 
      }    
    });
    if ($('#hotel_room_id option:selected').css('display') == 'none') $('#hotel_room_id').val('0');
    
  }
  $('#hotel_id').change(hotel_id_change);
  hotel_id_change();
  

  
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


