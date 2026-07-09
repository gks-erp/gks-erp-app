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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }



$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_product_lots',['from'=>'item']);


if ($id==-1) {



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_lot_product']=-1;
  $row['lotproduct_id']=0;
  $row['product_descr_p']='';
  $row['product_photo_p']='';
  $row['lot_name']='';
  $row['lot_descr']='';
  $row['lot_date_production']=null;
  $row['lot_date_expire']=null;
  $row['lot_disabled']=0;
  $row['lot_sortorder']=1000;
  
  $my_page_title=gks_lang('Νέα Παρτίδα-Serial Number');
} else {
  $sql ="SELECT gks_eshop_product_lots.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr<>'' THEN
          gks_eshop_products.product_descr
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr
          END
      END
    ELSE gks_eshop_products.product_descr
  END as product_descr_p,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo
  END as product_photo_p 
    
  FROM (((gks_eshop_product_lots 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_eshop_product_lots.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_eshop_product_lots.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_products ON gks_eshop_product_lots.lotproduct_id = gks_eshop_products.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
  
  where id_lot_product = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Παρτίδα-Serial Number').': '.$row['lot_name'];
  $object_title=$row['lot_name'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('warehouse','product_lots');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Παρτίδα-Serial Number');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Παρτίδα-Serial Number');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="lot_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παρτίδα-Serial number');?>:</label>
            <div class="col-sm-8">
              <input id="lot_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['lot_name']);?>">
            </div>
          </div>


          <div class="form-group row">
            <label for="lot_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <textarea id="lot_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['lot_descr']);?></textarea>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="lot_date_production" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία Παραγωγής');?>:</label>
            <div class="col-sm-8">
              <input id="lot_date_production" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['lot_date_production'])) echo showDate(strtotime($row['lot_date_production']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>

          <div class="form-group row">
            <label for="lot_date_expire" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία Λήξης');?>:</label>
            <div class="col-sm-8">
              <input id="lot_date_expire" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['lot_date_expire'])) echo  showDate(strtotime($row['lot_date_expire']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>

          <div class="form-group row">
            <label for="lot_product" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Είδος');?>:</label>
            <div class="col-sm-8">
              <input id="lotproduct_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['lotproduct_id'];?>">
              <a id="autocomplete_lotproduct_id" tabindex="-1" href="admin-products-item.php?id=<?php echo $row['lotproduct_id'];?>" style="<?php if ($row['lotproduct_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>

            </div>
          </div>
          <?php
          $myimgurl=trim_gks($row['product_photo_p'].'');
          
          if ($myimgurl == '') {
            $myimgurl="/my/img/product.png";
            $photo_url='';
          } else {
            $mydir = dirname($myimgurl);
            if (endwith($mydir,'/thumbnail')) {
              $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
            } else {
              $photo_url=$myimgurl;
            }
          }
          ?>
          <div class="form-group row" id="div_product_photo" style="<?php if ($photo_url=='') echo 'display:none;';?>">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="lotproduct_id"><?php echo gks_lang('Φωτογραφία');?>:</label>
            <div class="col-sm-8" id="div_photo"><?php
              echo '<a href="'.$photo_url.'" class="class_a_product_photo"><img id="img_product_photo" src="'.$myimgurl.'" style="max-width:96px;max-height:96px;"></a>';
            ?>
            </div>
          </div>          

          
          <div class="form-group row">
            <label for="lot_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="lot_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['lot_sortorder']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="lot_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="lot_disabled" value="1" <?php if ($row['lot_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_lot_product'];?>" data-model="gks_eshop_product_lots" data-backurl="admin-products-lots.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_eshop_product_lots',$id);
      echo getActivityObjectTable('gks_eshop_product_lots',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshop_product_lots','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_lot_product']>0) echo $row['id_lot_product'];?></span></div>
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



var from_php_dialog_object_rel_curr='gks_eshop_product_lots';
var from_php_activity_model='gks_eshop_product_lots';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','delete',$id);?>;



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
      
  $('#lot_date_production').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));
  $('#lot_date_expire').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));
      
  function mysubmit() {
    
    datasend='';


    datasend+='&lotproduct_id='  + encodeURIComponent($("#mypostform #lotproduct_id").attr('data-id').trim());
    datasend+='&lot_name='  + encodeURIComponent($.base64.encode($("#mypostform #lot_name").val().trim()));
    datasend+='&lot_descr='  + encodeURIComponent($.base64.encode($("#mypostform #lot_descr").val().trim()));
    datasend+='&lot_date_production='  + encodeURIComponent($.base64.encode($("#mypostform #lot_date_production").val().trim()));
    datasend+='&lot_date_expire='  + encodeURIComponent($.base64.encode($("#mypostform #lot_date_expire").val().trim()));
    datasend+='&lot_sortorder='  + encodeURIComponent(($("#mypostform #lot_sortorder").val().trim()));
    datasend+='&lot_disabled=' + (($('#lot_disabled').is(':checked')) ? '0':'1');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-products-lots-item-exec.php?id=' + <?php echo $id;?>,
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
  
  function lot_descr_change() {gks_resize_textarea($(this));}
  $('#lot_descr').on('change keyup paste', lot_descr_change);
  gks_resize_textarea($('#lot_descr'));
  
  

  $('#lotproduct_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode: 'simple',
        //and_variable: 1,    
        base_types:[0,1],
        onlylotserial: ['lot','serial'], 
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      $("#lotproduct_id").attr('data-id',ui.item.id);
      $('#autocomplete_lotproduct_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_lotproduct_id').show();
      need_save=true;
        
      datasend='cmd=get&id=' + ui.item.id + '&aa=1&sheets=0&quantity=1&user_id=0&anddescr=0&mydate=';
      //console.log(datasend);
      
      $.ajax({
  			url: 'admin-get-product-data.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  			  need_save=true;
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
  					  if (data.product_photo=='') {
  					    $('#div_product_photo').hide();
  					  } else {
  					    $('#div_product_photo').show();
  					    $('#img_product_photo').attr('src', data.product_photo).parent().attr('href',data.photo_url);
  					    mylgbase_restart();
  					  }  					  
  					} else {
  					  myalert('error:' + $.base64.decode(data.message));
  					}
  					  
  				}
  			}
  		});
		
		      
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#lotproduct_id").val('').attr('data-id','0');
          
        $('#autocomplete_lotproduct_id').hide(); 
        $('#img_product_photo').attr('src', '/my/img/product.png').parent().attr('href','#');
        $('#div_product_photo').hide();

        
      }
      need_save=true;
    }
 
  });


  var mylgbase = $("#div_photo");
  function mylgbase_restart() {
    if (!(mylgbase.data('lightGallery') === undefined)) {
      mylgbase.data('lightGallery').destroy(true);
    }
    mylgbase.lightGallery({selector: '.class_a_product_photo',thumbnail:true,hideBarsDelay:1000,});
  }
  mylgbase_restart();



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


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


