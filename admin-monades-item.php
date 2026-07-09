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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_monades_metrisis',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







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
  $row['id_monada']=-1;
  $row['monada_descr']='';
  $row['monada_symbol']='';
  $row['monada_parent_id']=0;
  $row['monada_parent_epi']=0;
  $row['monada_sortorder']=1000;
  $row['aade_eidos_posotitas_id']=0;
  $row['monada_peppol_code']='';
  
  $my_page_title=gks_lang('Νέα Μονάδα Μέτρησης');
} else {
  $sql ="SELECT gks_monades_metrisis.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_monades_metrisis 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_monades_metrisis.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_monades_metrisis.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_monada = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Μονάδα Μέτρησης').': '.$row['monada_descr'];
  $object_title=$row['monada_descr'];
}


stat_record();

$nav_active_array=array('manage','manage_monades');

$lang_data_obj=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Μονάδα Μέτρησης');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Μονάδα Μέτρησης');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="monada_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μονάδα Μέρησης');?>:</label>
            <div class="col-sm-8">
              <input id="monada_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['monada_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('monada_descr'));
          ?>
          <div class="form-group row">
            <label for="monada_symbol" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σύμβολο');?>:</label>
            <div class="col-sm-8">
              <input id="monada_symbol" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['monada_symbol']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('monada_symbol'));
          ?>
          <div class="form-group row">
            <label for="monada_parent_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μετατροπή');?>:</label>
            <div class="col-sm-8">
              <select name="monada_parent_id" id="monada_parent_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_monades_metrisis where id_monada<>".$id." ORDER BY monada_sortorder,monada_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_monada'].'" ';
                if ($row_select['id_monada']==$row['monada_parent_id']) echo ' selected ';
                echo '>'.$row_select['monada_descr'].'</option>';
              }?></select>
            </div>
          </div>
          <div class="form-group row" id="div_monada_parent_epi" style="<?php if ($row['monada_parent_id']==0) echo 'display:none;';?>">
            <label for="monada_parent_epi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Συντελεστής');?>:</label>
            <div class="col-sm-8">
              <input id="monada_parent_epi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['monada_parent_epi']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="monada_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="monada_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['monada_sortorder']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="aade_eidos_posotitas_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μονάδα Μέτρησης για ΑΑΔΕ');?>:</label>
            <div class="col-sm-8">
              <select name="aade_eidos_posotitas_id" id="aade_eidos_posotitas_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_eidos_posotitas ORDER BY sortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_eidos_posotitas'].'" ';
                if ($row_select['id_aade_eidos_posotitas']==$row['aade_eidos_posotitas_id']) echo ' selected ';
                echo '>'.$row_select['aade_eidos_posotitas_descr'].'</option>';
              }?></select>
              
            </div>
          </div>
          <div class="form-group row">
            <label for="monada_peppol_code" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κωδικός PEPPOL');?>:</label>
            <div class="col-sm-8">
              <input id="monada_peppol_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['monada_peppol_code']);?>" placeholder="<?php echo gks_lang('π.χ.');?> H87">
              <small class="form-text text-muted"><?php echo gks_lang('Δείτε');?> 
                <a href="https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec20/" target="_blank"><?php echo gks_lang('εδώ');?></a> 
                <?php echo gks_lang('για διεθνές εμπόριο και');?> 
                <a href="https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec21/" target="_blank"><?php echo gks_lang('εδώ');?></a> 
                <?php echo gks_lang('για επιβάτες, τύποι φορτίου, δεμάτων και υλικών συσκευασίας');?></small>
            </div>
          </div>
          
          
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_monada'];?>" data-model="gks_monades_metrisis" data-backurl="admin-monades.php"><?php echo gks_lang('Διαγραφή');?></button>
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_monada']>0) echo $row['id_monada'];?></span></div>
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
          <?php echo gks_lang('Μετατροπές');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('convv');?>>   
          <div style="text-align:left;font-size: 150%;font-weight: bold;color: blue;margin-bottom: 12px;">1 <i class="far fa-times-circle"></i> <?php echo $row['monada_descr'];?> = :</div>
          <hr>
          <?php   

              
              $sql_conv="select * from gks_monades_metrisis where id_monada<>".$id." order by monada_sortorder,monada_descr";
              $result_conv = $db_link->query($sql_conv);        
              if (!$result_conv) {
                debug_mail(false,'error sql',$sql_conv);
                die('sql error');
              }
              while ($row_conv = $result_conv->fetch_assoc()) {
                
                $monada_convert=array();
                gks_monada_convert($id, $row_conv['id_monada'], $monada_convert,array());
                
                $out_epi='--';
                if ($monada_convert['ok'] and $monada_convert['epi']!=0) {
                  //$quantity_mm=$quantity / $monada_convert['epi'];
                  $out_epi= myNumberFormatNo0Local($monada_convert['epi'],true);
                } 
                echo '<div>'.
                ($out_epi=='--' ? '-- <span>' : '<span style="color:blue"><b>'.$out_epi.'</b> <i class="far fa-times-circle"></i> ').
                '<a href="admin-monades-item.php?id='.$row_conv['id_monada'].'" style="color:black;">'.$row_conv['monada_descr'].'</a></span></div>';
             
              }

?>                      
              
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','delete',$id);?>;


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
      

  $('#monada_parent_id').change(function() {
    if ($(this).val() == '0') $('#div_monada_parent_epi').hide(); else $('#div_monada_parent_epi').show();
  });
  
 
    
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';


    datasend+='&monada_descr='  + encodeURIComponent($.base64.encode($("#mypostform #monada_descr").val().trim()));
    datasend+='&monada_symbol='  + encodeURIComponent($.base64.encode($("#mypostform #monada_symbol").val().trim()));
    datasend+='&monada_parent_id='  + encodeURIComponent(($("#mypostform #monada_parent_id").val().trim()));
    datasend+='&monada_parent_epi='  + encodeURIComponent(($("#mypostform #monada_parent_epi").val().trim()));
    datasend+='&monada_sortorder='  + encodeURIComponent(($("#mypostform #monada_sortorder").val().trim()));
    datasend+='&aade_eidos_posotitas_id='  + encodeURIComponent(($("#mypostform #aade_eidos_posotitas_id").val().trim()));
    datasend+='&monada_peppol_code='  + encodeURIComponent($.base64.encode($("#mypostform #monada_peppol_code").val().trim()));
    
    
    
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-monades-item-exec.php?id=' + <?php echo $id;?>,
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


