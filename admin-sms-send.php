<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Αποστολή SMS');
$nav_active_array=array('crm','manage_sms','manage_smssend');
db_open();
stat_record();

$perm_email_add  =gks_permission_user_can_action_php($my_wp_user_id,'gks_sms','add',0);
if ($perm_email_add==false) {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση στην αποστολή sms'))); die();}



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
          <?php echo gks_lang('Νέο SMS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('new');?>> 
          <div class="form-group row">
            <label for="sms_from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από');?>:</label>
            <div class="col-sm-8">
              <select id="sms_from" class="form-control form-control-sm myneedsave" style="width:unset;">
                <?php 
                $sql="SELECT gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name, 
                gks_erp_app_mobile.erp_app_mobile_phonenumber, gks_erp_app_mobile_ping.mydate
                FROM gks_erp_app_mobile 
                LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
                WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
                and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
                ORDER BY gks_erp_app_mobile.erp_app_mobile_sortorder;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'].'" '.
                  'data-provider="gks_erp_app_mobile" '.
                  'data-sender="'.$row_select['id_erp_app_mobile'].'" ';
                  $is_offline='';
                  if (empty($row_select['mydate'])==false and strtotime($row_select['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
                    $is_offline='';
                  } else {
                    $is_offline='disabled';
                  }            
                  echo $is_offline.'>App: '.$row_select['erp_app_mobile_name'].' '.$row_select['erp_app_mobile_phonenumber'];
                  if ($is_offline!='') echo ' - '.gks_lang('ανενεργό');
                  echo '</option>';
                }  
                $parts=explode(',',$GKS_SMS_SENDER);
                foreach ($parts as $value) {
                  $value=trim_gks($value);
                  if ($value!='') {
                    echo '<option value=smsapi:'.$value.' '.
                    'data-provider="smsapi" '.
                    'data-sender="'.$value.'" '.
                    '>smsapi: '.$value.'</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="sms_to" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προς');?>:</label>
            <div class="col-sm-8">
              <input id="sms_to" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" data-user_id="0">
            </div>
          </div>          
          <div class="form-group row">
            <label for="sms_template" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πρότυπο');?>:</label>
            <div class="col-sm-8">
              <select id="sms_template"  class="form-control form-control-sm" style="max-width:300px;">
              <option value="0" data-text=""></option>
              <?php
              $sms_template='';
              if (isset($tmp_user_settings['sms_template'])) $sms_template=intval($tmp_user_settings['sms_template']);
              $sql="select id_sms_viber_template,sms_viber_template_name, sms_viber_template_text FROM gks_sms_viber_template where sms_viber_template_disabled=0 and sms_enabled<>0 order by sms_viber_template_sortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_sms_viber_template'].'" '.
                'data-text="'.base64_encode($row_select['sms_viber_template_text']).'"';
                if ($sms_template == $row_select['id_sms_viber_template']) echo ' selected ';
                echo '>'.$row_select['sms_viber_template_name'].'</option>';
              }
              ?>
              </select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="sms_message" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-8">
              <textarea id="sms_message" type="text" class="form-control form-control-sm myneedsave" value="" 
              placeholder="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="min-height:200px;"></textarea>              
              <small class="form-text text-muted" id="sms_chars"></small>
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
      <button type="button" class="btn btn-primary" id="mybuttonsms"><?php echo gks_lang('Αποστολή');?></button>
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



 

  function sms_template_change() {
    data_text=$('#sms_template option:selected').attr('data-text').trim();
    if (data_text=='') return;
    data_text=$.base64.decode(data_text);
    $('#sms_message').val(data_text);
    messagesms_change('sms_message','sms_chars');
    gks_resize_textarea($('#sms_message'));
  }
  $('#sms_template').change(sms_template_change);
  

  
  $("#sms_message").on('change keyup paste', function() {
    messagesms_change('sms_message','sms_chars');
    gks_resize_textarea($(this));
  });       
  messagesms_change('sms_message','sms_chars');
  gks_resize_textarea($('#sms_message'));    
  
        
  $("#mybuttonsms").click(function() {
    
    
    if ($("#sms_to").val().trim().length < 10) {
      myalert('error:'+gks_lang('Εισάγετε κάποιον αποδέκτη'));
      return false;
    }
    if ($("#sms_message").val().trim().length < 2) {
      myalert('error:'+gks_lang('Εισάγετε κάποιο μήνυμα'));
      return false;
    }
    
    smslen=messagesms_countchars( $("#sms_message").val() );
    if (smslen> 918) {
      myalert('error:'+gks_lang('Πολύ μεγάλο μέγεθος κειμένου'));
      return false;
    }  
    
    
    var mydatasend='from=' + encodeURIComponent($.base64.encode($("#sms_from").val().trim()));
    mydatasend+='&sender_sms_provider='  + encodeURIComponent($.base64.encode($('#sms_from option:selected').attr('data-provider')));
    mydatasend+='&sender_sms_sender='  + encodeURIComponent($.base64.encode($('#sms_from option:selected').attr('data-sender')));
    mydatasend+='&to=' + encodeURIComponent($.base64.encode($("#sms_to").val().trim()));
    mydatasend+='&message=' + encodeURIComponent($.base64.encode($("#sms_message").val().trim()));
    
    
    $("body").addClass("myloading");
	  $.ajax({
			url: '/my/admin-sms-send-exec.php',
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
  					  myalert('error:'+gks_lang('Σφάλμα, παρακαλώ δοκιμάστε αργότερα'));
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
  

  $('#sms_to').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        comm_type:'phone',
        //mobile: 1,
      };
      $.ajax({
        url: 'admin-autocomplete-comm.php',
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
      $('#sms_to').attr('data-user_id',ui.item.user_id);
    },
    change: function (event, ui) {
        if(!ui.item){
        //  $('#dialog_item_message_to_sms').val('').attr('data-user_id','0');
          $('#sms_to').attr('data-user_id','0');
        }
    },
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + (item.descr + ' ' + item.user).trim() + '</span>')
          .appendTo(ul);
      };
    },
    
  });
  
});    
  
</script>
  
  


<?php
include_once('_my_footer_admin.php');

