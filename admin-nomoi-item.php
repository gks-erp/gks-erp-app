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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_nomoi',($id==-1 ? 'add' : 'view'),$id);
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
  $row['id_nomos']=-1;
  $row['country_id']=0;
  $row['nomos_ISO_3166_2'] ='';
  $row['nomos_descr'] ='';

  
  $my_page_title=gks_lang('Νέος Νομός');
  
} else {
   $sql ="SELECT gks_nomoi.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (gks_nomoi 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_nomoi.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_nomoi.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_nomos = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Νομός').': '.$row['nomos_descr'];
  $object_title=$row['nomos_descr'];
}


stat_record();
$nav_active_array=array('manage','manage_nomoi');

$lang_data_obj=gks_lang_data_obj_prepare('gks_nomoi','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);
//print '<pre>';print_r($lang_data_obj); die();

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Νομός');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Νομός');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέος');?></span></h3>
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
            <label for="nomos_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-sm-8">
              <input id="nomos_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['nomos_descr']);?>">
            </div>
          </div>

          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('nomos_descr'));
          //echo '<pre>';print_r($lang_data_obj); echo '</pre>';
          ?>

          
          <div class="form-group row">
            <label for="country_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-sm-8">
              <select name="country_id" id="country_id"  class="form-control form-control-sm">
              <option value="0"></option>
              <?php
              $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
              $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
              FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
              ".$lang_prepare_gks_country['sql']['from2']."
              ORDER BY country_name";


              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                if ($row_select['id_country']==$row['country_id']) echo ' selected ';
                echo '>'.$row_select['country_name'].'</option>';
              }?></select>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
              
              
              <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_nomos'];?>" data-model="gks_nomoi" data-backurl="admin-nomoi.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>    
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_nomos']>0) echo $row['id_nomos'];?></span></div>
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

 
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_nomoi','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_nomoi','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_nomoi','delete',$id);?>;


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


    datasend+='&nomos_descr='  + encodeURIComponent($.base64.encode($("#mypostform #nomos_descr").val().trim()));
    datasend+='&country_id='  + encodeURIComponent(($("#mypostform #country_id").val().trim()));
    
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-nomoi-item-exec.php?id=' + <?php echo $id;?>,
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


