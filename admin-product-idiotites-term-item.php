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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites_terms',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $idiotita_id=0; if (isset($_GET['idiotita_id'])) $idiotita_id=intval($_GET['idiotita_id']);
  $idiotita_type='';
  $sql="select id_product_idiotita,idiotita_type from gks_product_idiotites where id_product_idiotita=".$idiotita_id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $idiotita_id=$row['id_product_idiotita'];
    $idiotita_type=$row['idiotita_type'];
  } else {
    $idiotita_id=0;
  }



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_product_idiotita_term']=-1;
  $row['idiotita_id']=$idiotita_id;
  $row['idiotita_type']=$idiotita_type;
  $row['idiotita_term_name']='';
  $row['idiotita_term_descr']='';
  $row['idiotita_term_button']='';
  $row['idiotita_term_color']='';
  $row['idiotita_term_image']='';
  $row['idiotita_term_sortorder']=1000;
  
  $my_page_title=gks_lang('Νέος Όρος Ιδιότητας');
} else {
  $sql ="SELECT gks_product_idiotites_terms.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_product_idiotites.idiotita_name, gks_product_idiotites.idiotita_type
  FROM ((gks_product_idiotites_terms 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_product_idiotites_terms.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_product_idiotites_terms.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita
  where id_product_idiotita_term = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Όρος Ιδιότητας').': '.$row['idiotita_term_name'];
  $object_title=$row['idiotita_term_name'];
}


stat_record();

$nav_active_array=array('manage','manage_menu_product','manage_product_idiotites_terms');

$lang_data_obj=gks_lang_data_obj_prepare('gks_product_idiotites_terms','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Όρος Ιδιότητας');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Όρος Ιδιότητας');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="idiotita_id" class="col-sm-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιδιότητα');?>:</label>
            <div class="col-sm-8">
              <select name="idiotita_id" id="idiotita_id"  class="form-control form-control-sm myneedsave" style="width:calc(100% - 22px);display: inline;">
              <option value="0" data-type=""></option>
              <?php
              $sql="select id_product_idiotita,idiotita_name,idiotita_type FROM gks_product_idiotites ORDER BY idiotita_sortorder,idiotita_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_product_idiotita'].'" '.
                'data-type="'.$row_select['idiotita_type'].'" ';
                if ($row_select['id_product_idiotita']==$row['idiotita_id']) echo ' selected ';
                echo '>'.$row_select['idiotita_name'].'</option>';
              }?></select>
              
              <a id="jump_idiotita_id" tabindex="-1" href="<?php 
                if ($row['idiotita_id']>0) echo 'admin-product-idiotites-item.php?id='.$row['idiotita_id'];
                else echo '#';
                ?>" style=""><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή Ιδιότητας');?>"></i></a>
                            
            </div>
          </div>
          <div class="form-group row">
            <label for="idiotita_term_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα Όρου');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_term_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_term_name']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('idiotita_term_name'));
          ?>          
          <div class="form-group row">
            <label for="idiotita_term_descr" class="col-sm-12 col-form-label form-control-sm text-sm-right1 "><?php echo gks_lang('Περιγραφή Όρου');?>:</label>
            <div class="col-sm-12">
              <textarea id="idiotita_term_descr" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="height:200px;"><?php echo htmlspecialchars_gks($row['idiotita_term_descr']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('idiotita_term_descr'));
          ?>
          <div class="form-group row" id="div_idiotita_term_button" style="<?php if ($row['idiotita_type']!='10button') echo 'display:none;'; ?>">
            <label for="idiotita_term_button" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κουμπί');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_term_button" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_term_button']);?>">
            </div>
          </div>
          <div class="form-group row" id="div_idiotita_term_color" style="<?php if ($row['idiotita_type']!='20color') echo 'display:none;'; ?>">
            <label for="idiotita_term_color" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_term_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_term_color']);?>"
              style="max-width: 200px;">
            </div>
          </div>
          <div class="form-group row" id="div_idiotita_term_image" style="<?php if ($row['idiotita_type']!='30image') echo 'display:none;'; ?>">
            <label for="idiotita_term_image" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Φωτογραφία');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_term_image" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_term_image']);?>">
            </div>
          </div>




          <div class="form-group row">
            <label for="idiotita_term_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_term_sortorder" type="number" min="0"  class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_term_sortorder']);?>">
            </div>
          </div>


          
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
              
              <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_product_idiotita_term'];?>" data-model="gks_product_idiotites_terms" data-backurl="admin-product-idiotites-term.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>            
  </div>            
</div>            


            </div>
            
          </div>
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_product_idiotita_term']>0) echo $row['id_product_idiotita_term'];?></span></div>
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

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      

      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('alleidi');?>>        
          <?php
          $sql="SELECT gks_eshop_products_idiotites.product_id, gks_eshop_products.product_descr, gks_eshop_products.product_photo, gks_eshop_products.product_code
          FROM (gks_eshop_products_idiotites_terms 
          LEFT JOIN gks_eshop_products_idiotites ON gks_eshop_products_idiotites_terms.eshop_products_idiotites_id = gks_eshop_products_idiotites.id_eshop_products_idiotites) 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_idiotites.product_id = gks_eshop_products.id_product
          WHERE gks_eshop_products_idiotites_terms.product_idiotita_term_id=".$id."
          ORDER BY product_code,gks_eshop_products.product_descr";
          
          //echo $sql;
          
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>                
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κωδικός');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Προϊόν');?></th> 

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td><?php echo getProductPhoto($row_list['product_id'],$row_list['product_photo'],32);?></td>
              <td nowrap><?php echo $row_list['product_code'];?></td>  
              <td nowrap><?php echo '<a href="admin-products-item.php?id='.$row_list['product_id'].'">'.$row_list['product_descr'].'</a>';?></td>  
            </tr>
          <?php } ?>



          </tbody>
          </table>  
        </div>
      </div>      
      
    </div>
  </div>
</div>


<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
  
var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','delete',$id);?>;
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  var control_enter_active=false;
  
  
  $('#idiotita_term_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: gks_lang('OK'),
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
    
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
    

  $('#idiotita_id').change(function() {
    itype=$(this).find('option:selected').attr('data-type').trim();
    //console.log(itype);
    if (itype=='10button') {
      $('#div_idiotita_term_button').show();
      $('#div_idiotita_term_color').hide();
      $('#div_idiotita_term_image').hide();
    } else if (itype=='20color') {
      $('#div_idiotita_term_button').hide();
      $('#div_idiotita_term_color').show();
      $('#div_idiotita_term_image').hide();
      
    } else if (itype=='30image') {
      $('#div_idiotita_term_button').hide();
      $('#div_idiotita_term_color').hide();
      $('#div_idiotita_term_image').show();
    } else {
      $('#div_idiotita_term_button').hide();
      $('#div_idiotita_term_color').hide();
      $('#div_idiotita_term_image').hide();
    }
    
  
    temp=parseInt($(this).val());
    if (temp>0) {
      $('#jump_idiotita_id').attr('href','admin-product-idiotites-item.php?id='+ temp);
    } else {
      $('#jump_idiotita_id').attr('href','#');
    }

        
  });
  
 
    
  function mysubmit() {
    
    datasend='';

    datasend+='&idiotita_id='  + encodeURIComponent($("#mypostform #idiotita_id").val().trim());
    datasend+='&idiotita_term_name='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_term_name").val().trim()));
    datasend+='&idiotita_term_descr='  + encodeURIComponent($.base64.encode(tinyMCE.get('idiotita_term_descr').getContent()));
    datasend+='&idiotita_term_button='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_term_button").val().trim()));
    datasend+='&idiotita_term_color='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_term_color").val().trim()));
    datasend+='&idiotita_term_image='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_term_image").val().trim()));
    datasend+='&idiotita_term_sortorder='  + encodeURIComponent(($("#mypostform #idiotita_term_sortorder").val().trim()));
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-product-idiotites-term-item-exec.php?id=' + <?php echo $id;?>,
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


<script src='/my/js/tinymce/tinymce.min.js'></script>
<script>
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
</script>
<?php
//db_close();
include_once('_my_footer_admin.php');


