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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_barcodes',($id==-1 ? 'add' : 'view'),$id);
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
  $row['id_barcode']=-1;
  $row['barcode']='';
  $row['barcode_descr']='';
  $row['barcode_type_id']=0;
  
  $row['product_id']=0;
  $row['product_descr_p']='';
  $row['user_id']=0;
  $row['gks_nickname']='';
  $row['disable_barcode']=0;
  $row['comments']='';
  $my_page_title=gks_lang('Νέο Barcode');


} else {
 $sql ="SELECT gks_barcodes.*, 
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_photo<>'' THEN
        gks_eshop_products.product_photo
      ELSE
        gks_eshop_products_parent.product_photo
    END
  ELSE gks_eshop_products.product_photo

END as product_photo_p,
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
gks_eshop_products.product_descr, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
FROM ((((gks_barcodes 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_barcodes.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_barcodes.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_eshop_products ON gks_barcodes.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_barcodes.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where id_barcode = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Barcode').': '.$row['barcode'];
  $object_title=$row['barcode'];
}

stat_record();
$nav_active_array=array('manage','manage_menu_product','manage_product_barcodes');

//$GKS_LANG_DATA_ENABLED=array('el-GR','en-US','de-DE');

//print '<pre>';print_r($GKS_LANG_DATA_ENABLED); echo serialize($GKS_LANG_DATA_ENABLED); die();




include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3>Barcode: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3>Barcode: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="barcode" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Barcode');?>:</label>
            <div class="col-sm-8">
              <input id="barcode" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['barcode']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="barcode_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input id="barcode_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['barcode_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="product_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Είδος');?>:</label>
            <div class="col-sm-8">
              <input id="product_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['product_id'];?>">
              <a id="autocomplete_product_id" tabindex="-1" href="admin-products-item.php?id=<?php echo $row['product_id'];?>" style="<?php if ($row['product_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
            </div>
          </div>
          <div class="form-group row">
            <label for="user_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-8">
              <input id="user_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['user_id'];?>">
              <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
            </div>
          </div>
        
     
          <div class="form-group row">
            <label for="comments" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-sm-8">
              <textarea id="comments" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" ><?php echo htmlspecialchars_gks($row['comments']);?></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="disable_barcode" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="disable_barcode" value="1" <?php if ($row['disable_barcode']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>


        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Barcode');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       
          <div class="form-group row">
            <label for="barcode_type_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-sm-8">
              <select id="barcode_type_id" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $sql="select id_barcode_type as id,barcode_type_descr as descr,barcode_type_ds as ds
                FROM gks_barcodes_types 
                ORDER BY id_barcode_type";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
                $barcodetypes=[];
                while ($row_select = $result_select->fetch_assoc()) {
                  $barcodetypes[]=$row_select;
                }
                echo '<optgroup label="'.gks_lang('Γραμμικά').'">';
                foreach ($barcodetypes as $btype) {
                  if ($btype['ds']=='linear') {
                    echo '<option value="'.$btype['id'].'" ';
                    if ($btype['id']==$row['barcode_type_id']) echo ' selected ';
                    echo '>'.$btype['descr'].'</option>';                    
                  }
                } 
                echo '</optgroup>';
                echo '<optgroup label="'.gks_lang('Τετράγωνα').'">';
                foreach ($barcodetypes as $btype) {
                  if ($btype['ds']=='square') {
                    echo '<option value="'.$btype['id'].'" ';
                    if ($btype['id']==$row['barcode_type_id']) echo ' selected ';
                    echo '>'.$btype['descr'].'</option>';                    
                  }
                } 
                echo '</optgroup>';                
                ?>
              </select>
            </div>
          </div>
          
					<div class="form-group row">
          	<div class="col-sm-12" id="qr_result" style="text-align:center;">
          		
          	</div>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_barcode'];?>" data-model="gks_barcodes" data-backurl="admin-barcodes.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_barcode']>0) echo $row['id_barcode'];?></span></div>
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

 
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','delete',$id);?>;

  
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



  function comments_change() {gks_resize_textarea($(this));}
  $('#comments').on(mychange, comments_change);
  gks_resize_textarea($('#comments'));
    
  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';

    datasend+='&barcode='  +  encodeURIComponent($.base64.encode($("#mypostform #barcode").val().trim()));
    datasend+='&barcode_type_id='  +  encodeURIComponent($("#mypostform #barcode_type_id").val().trim());
    datasend+='&barcode_descr='  +  encodeURIComponent($.base64.encode($("#mypostform #barcode_descr").val().trim()));
    datasend+='&product_id='  +  encodeURIComponent($("#mypostform #product_id").attr('data-id').trim());
    datasend+='&user_id='  +  encodeURIComponent($("#mypostform #user_id").attr('data-id').trim());
    datasend+='&comments='  +  encodeURIComponent($.base64.encode($("#mypostform #comments").val().trim()));
    datasend+='&disable_barcode=' + (($('#disable_barcode').is(':checked')) ? '0':'1');
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-barcodes-item-exec.php?id=' + <?php echo $id;?>,
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

  $('#product_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:1,
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
    delay: 300, //default
    autoFocus: true,
    select: function( event, ui ) {
      $("#product_id").attr('data-id',ui.item.id);
      $('#autocomplete_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_product_id').show();
      need_save=true;
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#product_id").val('').attr('data-id','0');
        $('#autocomplete_product_id').hide(); 
      }
    },
  });

  $('#user_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        //eml: 1,
      };
      $.ajax({
        url: 'admin-autocomplete-user.php',
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
    delay: 300, //default
    select: function( event, ui ) {
      $("#user_id").attr('data-id',ui.item.id);
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();
      need_save=true;
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#user_id").val('').attr('data-id','0');
        $('#autocomplete_user_id').hide(); 
      }
    }
  });
  
  
  function generate_barcode() {
  	url=$('#barcode').val().trim();
  	//console.log(url);
  	if (url=='') {$('#qr_result').html('');return;}
    $('#qr_result').html('<img src="/my/img/wait.gif">');
    
  	datasend='';
  	datasend+='&cmd=' + encodeURIComponent($.base64.encode('createqr'));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(url));
  	datasend+='&barcode_type_id=' + encodeURIComponent($('#barcode_type_id').val());

    $.ajax({
			url: '/my/admin-qrcode-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+'. '+ jqXHR.responseText + '</div>')
			},				
			success: function(data) {
				if (!data) {
					$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+'. Παρακαλώ δοκιμάστε αργότερα</div>');
				} else {
					if (data.success == true) {
						$('#qr_result').html($.base64.decode(data.qrhtml));
						
					} else {
						$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+'. ' + $.base64.decode(data.message) + '</div>');
					}
				}
			}
		
    });      
    
  }
  $('#barcode').on(mychange,generate_barcode);
  $('#barcode_type_id').on(mychange,generate_barcode);
  
  generate_barcode();
  
  
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


