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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crons',($id==-1 ? 'add' : 'view'),$id);
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
  $row['id_cron']=-1;
  $row['disable_cron']=0;
  $row['every_seconds']=60;
  $row['fetch_url'] ='';
  //$row['last_run']='';
  //$row['next_run']='';
  $row['comments']='';
  $row['num_runs']=0;
  $my_page_title=gks_lang('Νέος χρονοπρογραμματισμός εργασίας');


} else {
 $sql ="SELECT gks_crons.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_crons
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_crons.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_crons.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_cron = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Χρονοπρογραμματισμός Εργασίας').': '.$row['fetch_url'];
  $object_title=$row['fetch_url'];
}

stat_record();
$nav_active_array=array('manage','manage_settings','manage_system_crons');

//$GKS_LANG_DATA_ENABLED=array('el-GR','en-US','de-DE');

//print '<pre>';print_r($GKS_LANG_DATA_ENABLED); echo serialize($GKS_LANG_DATA_ENABLED); die();




include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Χρονοπρογραμματισμός Εργασίας');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php //echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Χρονοπρογραμματισμός Εργασίας');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="fetch_url"><?php echo gks_lang('URL');?>:</label>
            <div class="col-sm-8">
              <textarea id="fetch_url" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" ><?php echo htmlspecialchars_gks($row['fetch_url']);?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="every_seconds" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εκτέλεση κάθε:<br>σε δευτερόλεπτα');?></label>
            <div class="col-sm-8">
              <input id="every_seconds" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['every_seconds'];?>" min="60" step="1" style="max-width:200px">
              <small id="every_seconds_txt"></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="last_run" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προηγούμενη εκτέλεση');?>:</label>
            <div class="col-sm-8">
              <div style="font-size: 0.875rem;padding: 5px 0px 5px 0px;">
              <?php if (isset($row['last_run'])) echo showDate(strtotime($row['last_run']),'d/m/Y H:i:s',1);?>
              </div>
            </div>
          </div>          
          
          <div class="form-group row">
            <label for="next_run" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επόμενη εκτέλεση');?>:</label>
            <div class="col-sm-8">
              <input id="next_run" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['next_run'])) echo showDate(strtotime($row['next_run']),'d/m/Y H:i',1);?>" style="max-width:200px" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="comments" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-sm-8">
              <textarea id="comments" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" ><?php echo htmlspecialchars_gks($row['comments']);?></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="disable_cron" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="disable_cron" value="1" <?php if ($row['disable_cron']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>

                    
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_cron'];?>" data-model="gks_crons" data-backurl="admin-crons.php"><?php echo gks_lang('Διαγραφή');?></button>
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_cron']>0) echo $row['id_cron'];?></span></div>
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

 
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crons','delete',$id);?>;

  
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
  $('#next_run').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  })); 

  
  function fetch_url_change() {gks_resize_textarea($(this));}
  $('#fetch_url').on(mychange, fetch_url_change);
  gks_resize_textarea($('#fetch_url'));
    
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

    datasend+='&fetch_url='  +  encodeURIComponent($.base64.encode($("#mypostform #fetch_url").val().trim()));
    datasend+='&every_seconds='  +  encodeURIComponent($("#mypostform #every_seconds").val().trim());
    datasend+='&next_run='  +  encodeURIComponent($("#mypostform #next_run").val().trim());
    datasend+='&comments='  +  encodeURIComponent($.base64.encode($("#mypostform #comments").val().trim()));
    datasend+='&disable_cron=' + (($('#disable_cron').is(':checked')) ? '0':'1');
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crons-item-exec.php?id=' + <?php echo $id;?>,
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

  function every_seconds_change() {
    myseconds=parseInt($('#every_seconds').val());
    if (isNaN(myseconds)) val=0;
    rest=myseconds;
    days=Math.floor(rest/(24*60*60));
    rest=rest-days*(24*60*60);
    hours=Math.floor(rest/(60*60));
    rest=rest-hours*(60*60);
    minutes=Math.floor(rest/(60));
    rest=rest-minutes*(60);
    seconds=rest;
    
    ret=[];
    if (days>0) ret.push(days+' '+gks_lang('ημέρες'));
    if (hours>0) ret.push(hours+' '+gks_lang('ώρες'));
    if (minutes>0) ret.push(minutes+' '+gks_lang('λεπτά'));
    if (seconds>0) ret.push(seconds+' '+gks_lang('δευτερόλεπτα'));
  
    $('#every_seconds_txt').html(ret.join(', ')); 
  }
  $('#every_seconds').on(mychange,every_seconds_change);
  every_seconds_change();

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


