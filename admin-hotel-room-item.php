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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');


$user_hotels=gks_get_hotels_list();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_room',['from'=>'item']);

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['id_hotel_room']=-1;
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['hotel_id']=0;
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {$row['hotel_id']=$value['id']; break;}
  $row['room_descr']='';
  $row['hotel_room_type_id']=0;
  $row['room_status']='disable';
  $row['hotel_floor_id']=0;

  $row['room_photo']='';
  
  
  $my_page_title=gks_lang('Νέο δωμάτιο');
} else {

  $sql ="SELECT gks_hotel_room.*,
  gks_hotel_room_type.room_type_descr, gks_hotel_room_type_fix.room_type_fix_descr, gks_hotel_room_type.room_type_price,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM (((gks_hotel_room 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_room.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_room.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix
  where id_hotel_room = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")";

  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();

  $my_page_title=gks_lang('Δωμάτιο').': '.$row['room_descr'];
  $object_title=$row['room_descr'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('hotel','hotel_rooms');

$lang_data_obj=gks_lang_data_obj_prepare('gks_hotel_room','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Δωμάτιο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Δωμάτιο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="room_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δωμάτιο');?>:</label>
            <div class="col-md-8">
              <input id="room_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['room_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('room_descr'));
          ?>
                    
          <div class="form-group row">
            <label for="room_status" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8">
              <select name="room_status" id="room_status"  class="form-control form-control-sm myneedsave">
                <option value="disable"    <?php echo ($row['room_status']=='disable' ? ' selected ':'');?>    ><?php echo getHotelRoomTypeStatusDescr('disable');?></option>
                <option value="available"  <?php echo ($row['room_status']=='available' ? ' selected ':'');?>  ><?php echo getHotelRoomTypeStatusDescr('available');?></option>
                <option value="renovation" <?php echo ($row['room_status']=='renovation' ? ' selected ':'');?> ><?php echo getHotelRoomTypeStatusDescr('renovation');?></option>
              </select>
            </div>
          </div>         
          <div class="form-group row">
            <label for="hotel_room_type_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <select name="hotel_room_type_id" id="hotel_room_type_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_hotel_room_type ORDER BY room_type_descr";
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
            <label for="hotel_floor_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <select name="hotel_floor_id" id="hotel_floor_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_hotel_floor ORDER BY sort_order,floor_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_hotel_floor'].'" '.
                'data-hotel_id="'.$row_select['hotel_id'].'" ';
                if ($row_select['id_hotel_floor']==$row['hotel_floor_id']) echo ' selected ';
                echo '>'.$row_select['floor_descr'].'</option>';
              }?></select>
            </div>
          </div>


        </div>
      </div>
            
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φωτογραφίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('photo');?>>       
          <div class="row">
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία του δωματίου');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              $user_photo_value="";
              $myimgurl = $row['room_photo']; 
              //echo $myimgurl;
              if ($myimgurl.'' == '') {
                $myimgurl="/my/img/product.png";
              } else {
                $user_photo_value = $myimgurl;
              }
              ?>
              <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_room_photo_img"/><br>
              
              <a href="" id="reset_profile_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                if ($user_photo_value == '') {
                  echo ' style="display:none" ';
                }
                ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
              <br><input type="hidden" id="form_room_photo" name="form_room_photo" value="<?php echo $user_photo_value;?>" />
            </div>                     
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες του δωματίου');?></div>
            
            <form role="form" method="post" action="admin-hotel-room-item-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="hotel_room_id" id="hotel_room_id" value="<?php echo $id;?>">
              <div id="lightgallery_user">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_hotel_room_photo where hotel_room_id=".$id." and filesobjectlist=0 order by id_hotel_room_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_hotel_room_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_hotel_room_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('gks_hotel_room',$id);?>
            </form>                      
            
            
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hotel_room'];?>" data-model="gks_hotel_room" data-backurl="admin-hotel-room.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">

    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_hotel_room',$id);
      echo getActivityObjectTable('gks_hotel_room',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel_room','id'=>$id));
      echo $obj_fileslist['html'];
      ?>      
    </div>
    <div class="col-md-6">
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_hotel_room']>0) echo $row['id_hotel_room'];?></span></div>
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


var from_php_dialog_object_rel_curr='gks_hotel_room';
var from_php_activity_model='gks_hotel_room';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room','delete',$id);?>;


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
    datasend+='&hotel_id='  + encodeURIComponent($("#mypostform #hotel_id").val().trim());
    datasend+='&room_descr='  + encodeURI($("#mypostform #room_descr").val().trim());
    datasend+='&room_status='  + encodeURI($("#mypostform #room_status").val().trim());
    datasend+='&hotel_room_type_id='  + encodeURI($("#mypostform #hotel_room_type_id").val().trim());
    datasend+='&hotel_floor_id='  + encodeURI($("#mypostform #hotel_floor_id").val().trim());
    
    datasend+='&form_room_photo='  + encodeURI($("#form_room_photo").val().trim());
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-room-item-exec.php?id=' + <?php echo $id;?>,
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
  
  function hotel_id_change() {
    var hotel_id=parseInt($('#hotel_id').val()); if (isNaN(hotel_id)) hotel_id=0;
    
    curval=parseInt($('#hotel_room_type_id').val()); if (isNaN(curval)) curval=0;
    $('#hotel_room_type_id option').each(function() {
      dhid=parseInt($(this).attr('data-hotel_id')); if (isNaN(dhid)) dhid=0;
      if (dhid>0) if (dhid==hotel_id) $(this).show(); else $(this).hide();
    });
    if ($('#hotel_room_type_id option[value=' + curval + ']').css('display')=='none') $('#hotel_room_type_id').val('0');

    curval=parseInt($('#hotel_floor_id').val()); if (isNaN(curval)) curval=0;
    $('#hotel_floor_id option').each(function() {
      dhid=parseInt($(this).attr('data-hotel_id')); if (isNaN(dhid)) dhid=0;
      if (dhid>0) if (dhid==hotel_id) $(this).show(); else $(this).hide();
    });
    if ($('#hotel_floor_id option[value=' + curval + ']').css('display')=='none') $('#hotel_floor_id').val('0');
  }
  $('#hotel_id').change(hotel_id_change);
  hotel_id_change();
  
  
  
  var file_cc=0;
    
  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα το δωμάτιο'));
          }
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png','.webp'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("<br>"));
          } else {
        
            file_cc++;
            data.mycc=file_cc;

            data.submit();
            $('#progress-bar_photo').show();
            $('#progress-extended_photo').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              
              
              myhtmlimg='';
              myhtmlimg+='<div id="item_upload_photo_' + file.insert_id + '" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">';
              myhtmlimg+='  <a class="lightgalleryitem_user" href="' + file.url + '" data-download-url="' + file.url + '">';
              myhtmlimg+='    <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="' + file.url_thumb + '">';
              myhtmlimg+='  </a>';
              myhtmlimg+='  <br>';
              myhtmlimg+='  <div style="padding-top:4px">';
              myhtmlimg+='      <a href="" class="set_profile_photo"   data-url="' + file.url_thumb + '" title="' + gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία') + '"><img src="/my/img/icons/photo.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="' + gks_lang('Διαγραφή') + '"><img src="/my/img/0.png" border="0" width="16"></a>';
              myhtmlimg+='  </div>';
              myhtmlimg+='</div>';


              $('#imagelist_photo').append(myhtmlimg);
              $('#item_upload_photo_' + file.insert_id + ' .delete_upload_photo').click(delete_upload_click_photo);
              $('#item_upload_photo_' + file.insert_id + ' .set_profile_photo').click(set_profile_photo);
              
             
            
              $("#lightgallery_user").data('lightGallery').destroy(true);
              $("#lightgallery_user").lightGallery({
              	selector: '.lightgalleryitem_user',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              if ($('#form_room_photo').val() == '') {
                $('#form_room_photo').val(file.url_thumb);
                $('#form_room_photo_img').attr("src",file.url_thumb);  
                $('#reset_profile_photo').show(); 
                need_save=true;         
              }
            }
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-bar_photo .bar_photo').css(
            'width',
            progress + '%'
        );
        $('#progress-extended_photo').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε')+'<br>' + data.jqXHR.responseText);
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progressfile_photo' + data.mycc + ' .bar_photo').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#progress-bar_photo').hide();
        $('#progress-extended_photo').hide();
      },
      
  });
      
	delete_upload_click_photo = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-hotel-room-item-photo-delete.php?id=' + uid,
			myuid: uid,
			type: 'POST',
			cache: false,
			dataType: 'json',
			mydata_url:data_url,
			data: '',
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
  					$('#item_upload_photo_' + this.myuid).remove();
  					$('#myfileid_photo_' + this.myuid).remove();
  					
  					if (this.mydata_url == $('#form_room_photo').val()) {
    					need_save=true;
    					if ($(".set_profile_photo").length == 0) {
    					  
                $('#form_room_photo').val('');
                $('#form_room_photo_img').attr("src",'/my/img/product.png');
                $('#reset_profile_photo').hide();
              } else {
                
                $(".set_profile_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_room_photo').val(data_url);
                  $('#form_room_photo_img').attr("src",data_url);
                  $('#reset_profile_photo').show();
                  return;
                });  					
      				}
            }
            
            $("#lightgallery_user").data('lightGallery').destroy(true);
            $("#lightgallery_user").lightGallery({
            	selector: '.lightgalleryitem_user',
            	thumbnail:true,
            	hideBarsDelay:1000,
            }); 
					  
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_photo').click(delete_upload_click_photo);

	set_profile_photo = function(event){	
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το δωμάτιο')); return;}	  
    need_save=true;      
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_room_photo').val(data_url);
    $('#form_room_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το δωμάτιο')); return;}	  
    need_save=true;
    $('#form_room_photo').val('');
    $('#form_room_photo_img').attr("src",'/my/img/product.png');   
    $('#reset_profile_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
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
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


