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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

//$perm_ret_term=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites_terms','view',0);






if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {


  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_product_idiotita']=-1;
  $row['idiotita_name']='';
  $row['idiotita_descr']='';
  $row['idiotita_type']='';
  $row['idiotita_sortorder']=1000;
  
  $my_page_title=gks_lang('Νέα Ιδιότητα');
} else {
  $sql ="SELECT gks_product_idiotites.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_product_idiotites 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_product_idiotites.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_product_idiotites.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_product_idiotita = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Ιδιότητα').': '.$row['idiotita_name'];
  $object_title=$row['idiotita_name'];
}



stat_record();

$nav_active_array=array('manage','manage_menu_product','manage_product_idiotites');

$idiotita_type=trim_gks($row['idiotita_type']);


$lang_data_obj=gks_lang_data_obj_prepare('gks_product_idiotites','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);



include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Ιδιότητα');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Ιδιότητα');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="idiotita_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα Ιδιότητας');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_name']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('idiotita_name'));
          ?>
                    
          <div class="form-group row">
            <label for="idiotita_descr" class="col-sm-12 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('Περιγραφή Ιδιότητας');?>:</label>
            <div class="col-sm-12">
              <textarea id="idiotita_descr" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="height:200px;"><?php echo htmlspecialchars_gks($row['idiotita_descr']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('idiotita_descr'));
          ?>
          <div class="form-group row">
            <label for="idiotita_type" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τύπος Ιδιότητας');?>:</label>
            <div class="col-sm-8">
              <select name="idiotita_type" id="idiotita_type"  class="form-control form-control-sm myneedsave">
              <option value=""></option>
              <option value="10button" <?php if ($idiotita_type=='10button') echo 'selected';?>><?php echo getIdiotitaTypeDescr('10button') ?></option>
              <option value="20color"  <?php if ($idiotita_type=='20color') echo 'selected';?> ><?php echo getIdiotitaTypeDescr('20color') ?></option>
              <option value="30image"  <?php if ($idiotita_type=='30image') echo 'selected';?> ><?php echo getIdiotitaTypeDescr('30image') ?></option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="idiotita_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="idiotita_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['idiotita_sortorder']);?>">
            </div>
          </div>


          
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              
              <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_product_idiotita'];?>" data-model="gks_product_idiotites" data-backurl="admin-product-idiotites.php"><?php echo gks_lang('Διαγραφή');?></button>
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_product_idiotita']>0) echo $row['id_product_idiotita'];?></span></div>
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
      
      <?php if (gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites_terms','view',0)) {
      $perm_term_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites_terms','edit',0);
      $perm_term_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites_terms','add',0);
      $perm_term_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites_terms','delete',0);
        
      ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Όροι');?></span>
          <?php if ($perm_term_add){ ?>
          <a class="btn btn-sm btn-primary" id="terms_add" style="margin-left: 10px;"
            href="admin-product-idiotites-term-item.php?id=-1&idiotita_id=<?php echo $id;?>"><?php echo gks_lang('Προσθήκη');?></a>
          <?php } ?>
        </div>
        <div class="card-body" <?php echo gks_card_body('terms');?>>
          
          <?php
          $query = "SELECT gks_product_idiotites_terms.*, 
          gks_product_idiotites.idiotita_name, gks_product_idiotites.idiotita_type,
          table_products.products_cc
          FROM (gks_product_idiotites_terms 
          LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita)
          LEFT JOIN (
            select product_idiotita_term_id,count(*) as products_cc from gks_eshop_products_idiotites_terms group by product_idiotita_term_id
          ) as table_products on gks_product_idiotites_terms.id_product_idiotita_term = table_products.product_idiotita_term_id
          where idiotita_id=".$id."
          ORDER BY idiotita_term_sortorder,idiotita_term_name";

          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="terms_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <?php if ($perm_term_delete) {?>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <?php } ?>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap><?php echo gks_lang('Όρος');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap><?php echo gks_lang('Περιγραφή');?></th>        
              <th class="table-dark td10button" scope="col" style="text-align: center !important;<?php if ($idiotita_type!='10button') echo 'display:none;';?>" width="10%" nowrap><?php echo gks_lang('Κουμπί');?></th>        
              <th class="table-dark td20color"  scope="col" style="text-align: center !important;<?php if ($idiotita_type!='20color') echo 'display:none;';?>" width="10%" nowrap><?php echo gks_lang('Χρώμα');?></th>        
              <th class="table-dark td30image"  scope="col" style="text-align: center !important;<?php if ($idiotita_type!='30image') echo 'display:none;';?>" width="10%" nowrap><?php echo gks_lang('Φωτό');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Προϊόντα');?></th>        
              <?php if ($perm_term_edit) {?>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><span title="<?php echo gks_lang('Σειρά Ταξινόμησης');?>"><?php echo gks_lang('ΣειράΤ');?></span></th>        
              <?php } ?>
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="terms_tr_exist" data-id="<?php echo $row_list['id_product_idiotita_term'];?>">
              <th scope="row" nowrap align="right" class="terms_aa"><?php echo ($i);?></td>       
              <?php if ($perm_term_delete) {?>
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_terms_delete_after|<?php echo $row_list['id_product_idiotita_term'];?>" data-id="<?php echo $row_list['id_product_idiotita_term'];?>" data-model="gks_product_idiotites_terms">
              </td>
              <?php } ?>
              <td nowrap align="center"><a href="admin-product-idiotites-term-item.php?id=<?php echo $row_list['id_product_idiotita_term'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td ><?php echo $row_list['idiotita_term_name'];?></td>
              <td ><?php echo $row_list['idiotita_term_descr'];?></td>
              <td nowrap align="center" class="td10button" style="<?php if ($idiotita_type!='10button') echo 'display:none;';?>"><?php if ($row_list['idiotita_type']=='10button') echo $row_list['idiotita_term_button'];?></td>
              <?php
              $bgcolor='';
              if ($row_list['idiotita_type']=='20color' and !empty($row_list['idiotita_term_color'])) $bgcolor=$row_list['idiotita_term_color'];
              ?>
              <td nowrap align="center" class="td20color" style="<?php if ($idiotita_type!='20color') echo 'display:none;';?><?php if ($bgcolor!='') echo 'background-color:'.$bgcolor.';color:'.color_inverse($bgcolor);?>"><?php //echo $bgcolor;?></td>
              <td nowrap align="center" class="td30image" style="<?php if ($idiotita_type!='30image') echo 'display:none;';?>"><?php 
                if ($row_list['idiotita_type']=='30image' and empty($row_list['idiotita_term_image']) == false) 
                 echo '<img src="'.$row_list['idiotita_term_image'].'" style="height:32px;"/>';
               ?></td>
              <td nowrap align="center"><?php echo $row_list['products_cc'];?></td>
              
              <?php if ($perm_term_edit) {?>
              <td nowrap class="mytdcm sortorder_handle_sub" title="<?php echo $row_list['idiotita_term_sortorder'];?>">
                <i class="fas fa-arrows-alt-v"></i>
                <span><?php echo $row_list['idiotita_term_sortorder'];?></span>
              </td>
              <?php } ?>

            </tr>
          <?php } ?>


     
          </tbody>
          </table>      

          
        </div>
      </div>
      <?php } ?>
      

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('alleidi');?>>        
          <?php
          $sql="SELECT gks_eshop_products_idiotites.product_id, gks_eshop_products.product_descr, gks_eshop_products.product_photo, gks_eshop_products.product_code
          FROM gks_eshop_products_idiotites LEFT JOIN gks_eshop_products ON gks_eshop_products_idiotites.product_id = gks_eshop_products.id_product
          WHERE gks_eshop_products_idiotites.product_idiotita_id=".$id."
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites','delete',$id);?>;


  
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
    

  $('#monada_parent_id').change(function() {
    if ($(this).val() == '0') $('#div_monada_parent_epi').hide(); else $('#div_monada_parent_epi').show();
  });
  
 
    
  function mysubmit() {
    
    datasend='';


    datasend+='&idiotita_name='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_name").val().trim()));
    datasend+='&idiotita_descr='  + encodeURIComponent($.base64.encode(tinyMCE.get('idiotita_descr').getContent()));
    datasend+='&idiotita_type='  + encodeURIComponent($.base64.encode($("#mypostform #idiotita_type").val().trim()));
    datasend+='&idiotita_sortorder='  + encodeURIComponent(($("#mypostform #idiotita_sortorder").val().trim()));
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-product-idiotites-item-exec.php?id=' + <?php echo $id;?>,
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
  

  $('#terms_add').click(function() {
    event.stopPropagation();
  });
  
  $('#idiotita_type').change(function() {
    temp=$(this).val();
    if (temp=='10button') {
      $('.td10button').show();
      $('.td20color').hide();
      $('.td30image').hide();
    } else if (temp=='20color') {
      $('.td10button').hide();
      $('.td20color').show();
      $('.td30image').hide();
    } else if (temp=='30image') {
      $('.td10button').hide();
      $('.td20color').hide();
      $('.td30image').show();
    } else {
      $('.td10button').hide();
      $('.td20color').hide();
      $('.td30image').hide();
    }
    
  });
  
  
  
  window.gks_fnc_terms_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.terms_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var terms_aa=0;
      $('#terms_table .terms_aa').each(function () {
        terms_aa++;
        $(this).html(terms_aa);  
      });    
    });
  }
    
  
  $('#terms_table > tbody').sortable({
    handle: '.sortorder_handle_sub',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_product_idiotites_terms',mylist,'#terms_table > tbody');
    }
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


