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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_price',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$user_hotels=gks_get_hotels_list();

$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_price',['from'=>'item']);


if ($id==-1) {
  
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_hotel_price']=-1;
  $row['hotel_id']=0;
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {$row['hotel_id']=$value['id']; break;}
  $hotel_params=gks_hotel_get_params($row['hotel_id']);

  $row['hotel_room_type_id']=0;
  //$row['price_from'] ='';
  //$row['price_to'] ='';
  $row['price_descr'] ='';
  $row['price'] = $hotel_params['hotel_default_price']; //$GKS_HOTEL_DEFAULT_PRICE;
  $row['price_weekday_de']=1;
  $row['price_weekday_tr']=1;
  $row['price_weekday_te']=1;
  $row['price_weekday_pe']=1;
  $row['price_weekday_pa']=1;
  $row['price_weekday_sa']=1;
  $row['price_weekday_ky']=1;

  $my_page_title=gks_lang('Νέα Τιμή');
} else {
  
  $sql ="SELECT gks_hotel_price.*, 
  gks_hotel_room_type.room_type_descr,  
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM ((gks_hotel_price 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_price.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_price.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_price.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_hotel_price = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Τιμή').': '.$row['room_type_descr'] . ((isset($row['price_descr']) and trim_gks($row['price_descr'])!='') ? ' - '. $row['price_descr'] : '');
  $object_title=$row['room_type_descr'] . ((isset($row['price_descr']) and trim_gks($row['price_descr'])!='') ? ' - '. $row['price_descr'] : '');

}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();
$nav_active_array=array('hotel','hotel_price');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Τιμή');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Τιμή');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
          <div class="form-group row" style="">
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
            <label for="price_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
            <div class="col-md-8">
              <input id="price_from" type="text" class="form-control form-control-sm" value="<?php if (isset($row['price_from'])) echo date('d/m/Y', strtotime($row['price_from']));?>">
            </div>
          </div>        
          <div class="form-group row">
            <label for="price_to" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
            <div class="col-md-8">
              <input id="price_to" type="text" class="form-control form-control-sm" value="<?php if (isset($row['price_to'])) echo date('d/m/Y', strtotime($row['price_to']));?>">
            </div>
          </div>        
  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημέρες');?>:</label>
            <div class="col-md-8">
              <?php
              $seldays1=false;
              if ($row['price_weekday_de']!=0 and $row['price_weekday_tr']!=0 and $row['price_weekday_te']!=0 and $row['price_weekday_pe']!=0 and 
                  $row['price_weekday_pa']!=0 and $row['price_weekday_sa']!=0 and $row['price_weekday_ky']!=0) $seldays1=true;
              ?>
              <div class="form-check">
              <input class="form-check-input" type="radio" name="price_seldays" id="price_seldays1" value="1" <?php if ($seldays1) echo ' checked '; ?> >
              <label class="form-check-label" for="price_seldays1"><?php echo gks_lang('Όλες οι ημέρες');?></label>
              <br>
              <input class="form-check-input" type="radio" name="price_seldays" id="price_seldays2" value="2" <?php if ($seldays1 == false) echo ' checked '; ?> >
              <label class="form-check-label" for="price_seldays2"><?php echo gks_lang('Κάποιες ημέρες');?></label>
              </div>
              <div class="form-check form-check-inline" id="price_seldays2div" style="<?php if ($seldays1) echo 'display:none;'; ?>">
                <input type="checkbox" name="price_weekday_de" id="price_weekday_de" value="1" style="margin-left: 10px;" <?php if ($row['price_weekday_de']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_de"><?php echo gks_lang('Δε','part3');?></label>
                <input type="checkbox" name="price_weekday_tr" id="price_weekday_tr" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_tr']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_tr"><?php echo gks_lang('Τρ','part3');?></label>
                <input type="checkbox" name="price_weekday_te" id="price_weekday_te" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_te']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_te"><?php echo gks_lang('Τε','part3');?></label>
                <input type="checkbox" name="price_weekday_pe" id="price_weekday_pe" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_pe']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_pe"><?php echo gks_lang('Πε','part3');?></label>
                <input type="checkbox" name="price_weekday_pa" id="price_weekday_pa" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_pa']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_pa"><?php echo gks_lang('Πα','part3');?></label>
                <input type="checkbox" name="price_weekday_sa" id="price_weekday_sa" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_sa']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_sa"><?php echo gks_lang('Σα','part3');?></label>
                <input type="checkbox" name="price_weekday_ky" id="price_weekday_ky" value="1" style="margin-left: 5px;"  <?php if ($row['price_weekday_ky']!=0) echo ' checked '; ?>>
                <label class="form-check-label" for="price_weekday_ky"><?php echo gks_lang('Κυ','part3');?></label>
                
              </div>
              
              
            </div>
          </div>
  
  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right" for="price"><?php echo gks_lang('Τιμή');?>:</label>
            <div class="col-md-8">
              <input id="price" type="number" class="form-control form-control-sm" value="<?php echo myNumberFormatNo0($row['price']);?>" min="0" step="1">
            </div>
          </div>
            
          <div class="form-group row">
            <label for="price_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <input id="price_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['price_descr']);?>">
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hotel_price'];?>" data-model="gks_hotel_price" data-backurl="admin-hotel-price.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php 
      echo getObjectRels('gks_hotel_price',$id);
      echo getActivityObjectTable('gks_hotel_price',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel_price','id'=>$id));
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
            <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_hotel_price'];?>"></div>
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


var from_php_dialog_object_rel_curr='gks_hotel_price';
var from_php_activity_model='gks_hotel_price';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','delete',$id);?>;


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
  
  
  $('#price_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#price_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


 
    
  function mysubmit() {
    datasend='';
    datasend+='&hotel_room_type_id='  + encodeURI($("#mypostform #hotel_room_type_id").val().trim());

   
    datasend+='&price_from='  + encodeURIComponent($("#mypostform #price_from").val().trim());
    datasend+='&price_to='  + encodeURIComponent($("#mypostform #price_to").val().trim());
    datasend+='&price_descr='  + encodeURIComponent($.base64.encode($("#mypostform #price_descr").val().trim()));
    datasend+='&price='  + encodeURIComponent($("#mypostform #price").val().trim());
    
    datasend+='&price_seldays1='  + (($('#mypostform #price_seldays1').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_de='  + (($('#mypostform #price_weekday_de').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_tr='  + (($('#mypostform #price_weekday_tr').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_te='  + (($('#mypostform #price_weekday_te').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_pe='  + (($('#mypostform #price_weekday_pe').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_pa='  + (($('#mypostform #price_weekday_pa').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_sa='  + (($('#mypostform #price_weekday_sa').is(':checked')) ? '1':'0');
    datasend+='&price_weekday_ky='  + (($('#mypostform #price_weekday_ky').is(':checked')) ? '1':'0');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-price-item-exec.php?id=' + <?php echo $id;?>,
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
  					//myalert('ok:' + 'OK');
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
  

  
  $('#price_seldays1').change(function() {
    $('#price_seldays2div').hide();
  });
  $('#price_seldays2').change(function() {
    $('#price_seldays2div').show();
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


