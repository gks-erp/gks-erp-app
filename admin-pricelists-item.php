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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_pricelist',['from'=>'item']);


$perm_item_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','view',0);
$perm_item_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','edit',0);
$perm_item_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','add',0);
$perm_item_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','delete',0);



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
  $row['id_pricelist']=-1;
  $row['based_pricelist_id']=0;
  $row['pricelist_descr']='';
  $row['pricelist_disable']=0;
  $row['price_is_xondriki']=0;

  $my_page_title=gks_lang('Νέος Τιμοκατάλογος');



} else {
 $sql ="SELECT gks_eshop_pricelist.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_eshop_pricelist
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_eshop_pricelist.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_eshop_pricelist.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_pricelist = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Τιμοκατάλογος').': '.$row['pricelist_descr'];
  $object_title=$row['pricelist_descr'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);



stat_record();
$nav_active_array=array('manage','manage_pricelist');

$lang_data_obj=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Τιμοκατάλογος');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Τιμοκατάλογος');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέος');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="pricelist_descr"  value="<?php echo htmlspecialchars_gks($row['pricelist_descr']);?>">
            </div>
          </div>

          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('pricelist_descr'));
          ?>
          
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="radio" name="price_is_xondriki" id="price_is_xondriki0" value="0" <?php if ($row['price_is_xondriki']==0) echo ' checked '; ?>>
                  <label for="price_is_xondriki0"><?php echo gks_lang('Λιανικής');?></label>
                <br>
                <input type="radio" name="price_is_xondriki" id="price_is_xondriki1" value="1" <?php if ($row['price_is_xondriki']==1) echo ' checked '; ?>>
                  <label for="price_is_xondriki1"><?php echo gks_lang('Χονδρικής');?></label>
                <br>
                <input type="radio" name="price_is_xondriki" id="price_is_xondriki2" value="2" <?php if ($row['price_is_xondriki']==2) echo ' checked '; ?>>
                  <label for="price_is_xondriki2"><?php echo gks_lang('ΥπερΧονδρικής');?></label>
                <br>
                <input type="radio" name="price_is_xondriki" id="price_is_xondriki3" value="3" <?php if ($row['price_is_xondriki']==3) echo ' checked '; ?>>
                  <label for="price_is_xondriki3"><?php echo gks_lang('Αγοράς');?></label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργός');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="pricelist_disable" value="1" <?php if ($row['pricelist_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>                    


        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row);print '</pre>';
?>


    </div>
  </div>
</div>
      
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_pricelist'];?>" data-model="gks_eshop_pricelist" data-backurl="admin-pricelists.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


    

<?php if ($perm_item_view) { ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('items');?>> 


    
<table style="width:calc(100% - 10px);" class="table table-sm table-responsive1 table-striped table-bordered gkstable" 
  border="0" cellspacing="0" cellpadding="5" align="center" id="table_eshop_pricelists_items">
  <tr>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('A/A');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap colspan="<?php echo ($perm_item_delete ? 3: 2);?>"><?php echo gks_lang('ID');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Ενεργό');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="55%" nowrap ><?php echo gks_lang('Περιγραφή');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Σειρά');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Κουπόνι');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Από');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Έως');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="25%" nowrap ><?php echo gks_lang('Προϊόν');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap ><?php echo gks_lang('Κατηγορία');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Ελαχ.Ποσότητα');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Τύπος');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap ><?php echo gks_lang('Π.χ.');?></th>
  </tr>      

      
  <?php
  
    $query2 = "SELECT gks_eshop_pricelist_items.*
    FROM gks_eshop_pricelist_items 
    WHERE gks_eshop_pricelist_items.pricelist_id=".$id."
    ORDER BY gks_eshop_pricelist_items.pricelist_item_sequence,id_pricelist_item";
    $result2 = $db_link->query($query2);        
    if (!$result2) debug_mail(false,'error stat_sql',$query2);
    if (!$result2) die('dddddd2');      
    $j=0;
    while ($line2 = $result2->fetch_assoc()) {
      $j++;
      ?>
      
    <tr class="<?php echo ($j % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $line2['id_pricelist_item'];?>">

    <th scope="row" class="mytdcm"><?php echo ($j);?></th>
    <td nowrap class="mytdcm p-0"><a href="admin-pricelists-items-item.php?id=<?php echo $line2['id_pricelist_item'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
    <td nowrap class="mytdcm p-0"><?php echo $line2['id_pricelist_item'];?></td>
    <?php if ($perm_item_delete) { ?> 
    <td nowrap class="mytdcm p-0"><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $line2['id_pricelist_item'];?>" data-model="gks_eshop_pricelist_items"></i></td>
    <?php } ?>

          
              
              <td class="mytdcm"><?php echo myimg010r($line2['pricelist_item_disable']);?></td>
              <td class="mytdcml"><?php echo $line2["pricelist_item_descr"];?></td>
              
              <td nowrap class="mytdcm sortorder_handle" title="<?php echo $line2['pricelist_item_sequence'];?>">
                <i class="fas fa-arrows-alt-v"></i>
                <span><?php echo $line2['pricelist_item_sequence'];?></span>
              </td>
              <td nowrap class="mytdcml"><?php echo $line2["pricelist_item_coupon"];?></td>
              <td nowrap class="mytdcml"><?php if (isset($line2['pricelist_item_date_from'])) echo showDate(strtotime($line2['pricelist_item_date_from']), 'd/m/Y H:i:s', 1);?></td>   
              <td nowrap class="mytdcml"><?php if (isset($line2['pricelist_item_date_to'])) echo showDate(strtotime($line2['pricelist_item_date_to']), 'd/m/Y H:i:s', 1);?></td>   
              <td  class="mytdcml">
                
              </td>
              <td  class="mytdcml">
                
              </td>
              <td nowrap  class="mytdcm"><?php echo $line2["pricelist_item_min_posotita"];?></td>
              <td nowrap class="mytdcm"><?php echo gks_lang('Τιμή');?>*(1+<?php echo $line2['pricelist_item_price_epi']; ?>) + <?php echo $line2['pricelist_item_price_plus'];?></td>
              <td nowrap class="mytdcm">100.00 --> <?php 
                $new_price =(100*(1+$line2['pricelist_item_price_epi']) + $line2['pricelist_item_price_plus']);
                echo number_format($new_price, 2, ',', '.');
                echo ' ('. number_format(($new_price - 100)*100 /100,2,',', '.').'%)';
                ?>
              </td>
            </tr>	
                  
          
      
      
    <?php }
  
?>
</table>
<?php if ($perm_item_add) { ?>
<br>
<p align="center">
  <a class="btn btn-primary" href="admin-pricelists-items-item.php?id=-1&pricelist_id=<?php echo $id;?>"><?php echo gks_lang('Νέο στοιχείο τιμοκαταλόγου');?></a>
</p>
<?php } ?>





        </div>
      </div>

    </div>
  </div>
</div>
<?php } ?>


<div class="container-fluid">
  <div class="row">
      
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_eshop_pricelist',$id);
      echo getActivityObjectTable('gks_eshop_pricelist',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshop_pricelist','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_pricelist']>0) echo $row['id_pricelist'];?></span></div>
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

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



var from_php_dialog_object_rel_curr='gks_eshop_pricelist';
var from_php_activity_model='gks_eshop_pricelist';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','delete',$id);?>;




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

    datasend+='&pricelist_descr='  + encodeURIComponent($.base64.encode($("#mypostform #pricelist_descr").val().trim()));
    datasend+='&pricelist_disable=' + (($('#mypostform #pricelist_disable').is(':checked')) ? '0':'1');
    datasend+='&price_is_xondriki=' + $('input[name=price_is_xondriki]:checked').val();
    
    datasend+=gks_lang_data_obj_input_collect();
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-pricelists-item-exec.php?id=' + <?php echo $id;?>,
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
  
  $('#table_eshop_pricelists_items > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_eshop_pricelist_items',mylist,'#table_eshop_pricelists_items > tbody');
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


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


