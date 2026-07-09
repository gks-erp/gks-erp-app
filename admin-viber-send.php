<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Αποστολή Viber Μήνυμα');
$nav_active_array=array('crm','manage_viber','manage_vibersend');
db_open();
stat_record();

$perm_email_add  =gks_permission_user_can_action_php($my_wp_user_id,'gks_viber_msgs','add',0);
if ($perm_email_add==false) {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση στην αποστολή viber'))); die();}



include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-lg-6 offset-lg-3 ">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Νέο Viber μήνυμα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('new');?>> 


          <div class="form-group row">
            <label for="viber_to" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προς');?>:</label>
            <div class="col-sm-8">
              <input id="viber_to" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" data-user_id="0">
            </div>
          </div>          
          <div class="form-group row">
            <label for="viber_template" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πρότυπο');?>:</label>
            <div class="col-sm-8">
              <select id="viber_template"  class="form-control form-control-sm" style="max-width:300px;">
              <option value="0" data-text=""></option>
              <?php
              $viber_template='';
              if (isset($tmp_user_settings['viber_template'])) $viber_template=intval($tmp_user_settings['viber_template']);
              $sql="select id_sms_viber_template,sms_viber_template_name, sms_viber_template_text FROM gks_sms_viber_template where sms_viber_template_disabled=0 and viber_enabled<>0 order by sms_viber_template_sortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_sms_viber_template'].'" '.
                'data-text="'.base64_encode($row_select['sms_viber_template_text']).'"';
                if ($viber_template == $row_select['id_sms_viber_template']) echo ' selected ';
                echo '>'.$row_select['sms_viber_template_name'].'</option>';
              }
              ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="viber_message" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-8">
              <textarea id="viber_message" type="text" class="form-control form-control-sm myneedsave" value="" 
              placeholder="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="min-height:200px;"></textarea>   
              <small class="form-text text-muted" id="viber_format">
              <?php echo gks_lang('Έντονη γραφή: μέσα σε');?> <b><span class="gks_viber_sc">*</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">*</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">*</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <b><?php echo gks_lang('φίλε');?></b> gks<br>
              <?php echo gks_lang('Πλάγια γραφή: μέσα σε');?> <b><span class="gks_viber_sc">_</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">_</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">_</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <i><?php echo gks_lang('φίλε');?></i> gks<br>
              <?php echo gks_lang('Διακριτή γραφή: μέσα σε');?> <b><span class="gks_viber_sc">~</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">~</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">~</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <span style="text-decoration: line-through;"><?php echo gks_lang('φίλε');?></span> gks<br>
              <?php echo gks_lang('Monospace: μέσα σε');?> <b><span class="gks_viber_sc">```</span></b> <?php echo gks_lang('π.χ. Γεια σου');?> <span class="gks_viber_sc">```</span><?php echo gks_lang('φίλε');?><span class="gks_viber_sc">```</span> gks =&gt; <?php echo gks_lang('Γεια σου');?> <span style="font-family: monospace;"><?php echo gks_lang('φίλε');?></span> gks<br>
              </small>           
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
      <button type="button" class="btn btn-primary" id="mybuttonviber"><?php echo gks_lang('Αποστολή','part2');?></button>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<?php include_once('_dialogs.php');?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var timestamp = new Date().getTime();

  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>



  function viber_template_change() {
    data_text=$('#viber_template option:selected').attr('data-text').trim();
    if (data_text=='') return;
    data_text=$.base64.decode(data_text);
    $('#viber_message').val(data_text);
    //messagesms_change('viber_message','sms_chars');
    gks_resize_textarea($('#viber_message'));
  }
  $('#viber_template').change(viber_template_change);
  
  
  $("#viber_message").on('change keyup paste', function() {
    gks_resize_textarea($(this));
  });       
  gks_resize_textarea($('#viber_message'));    
  
    
     
        
  $("#mybuttonviber").click(function() {
    if (parseInt($("#viber_to").attr('data-user_id').trim()) < 1) {
      myalert('error:'+gks_lang('Επιλέξτε κάποιον αποδέκτη'));
      return false;
    }
    if ($("#viber_message").val().trim().length < 2) {
      myalert('error:'+gks_lang('Εισάγετε κάποιο μήνυμα'));
      return false;
    }
    
    var mydatasend='&to=' + encodeURIComponent($("#viber_to").attr('data-user_id').trim());
    mydatasend+='&message=' + encodeURIComponent($.base64.encode($("#viber_message").val().trim()));
    
    
    $("body").addClass("myloading");
	  $.ajax({
			url: '/my/admin-viber-send-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: mydatasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
				  $("body").removeClass("myloading");
				  myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
          $("body").removeClass("myloading");
  				if (data.success == false){
  					if (data.message.length > 0){
  						myalert('error:' + $.base64.decode(data.message));
  						return;
  					} else {
  					  myalert('error:'+gks_lang('Σφάλμα')+' '+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  					  return;
  					}
  				} else {
  				  myalert('ok:' + $.base64.decode(data.message));
  				  return;
  				}
				
		
  			}
			}
			
		});          
		
    
  });
  
  $('#viber_to').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        viber: 1,
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
      $('#viber_to').attr('data-user_id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#viber_to').val('').attr('data-user_id','0');
        }
    },
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
  });
  

  
});    
  
</script>
  
  


<?php
//db_close();
include_once('_my_footer_admin.php');

