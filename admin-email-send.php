<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Αποστολή email');
$nav_active_array=array('crm','manage_email','manage_emailsend');
db_open();
stat_record();

$perm_email_add  =gks_permission_user_can_action_php($my_wp_user_id,'gks_email','add',0);
if ($perm_email_add==false) {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση στην αποστολή email'))); die();}




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
    <div class="col-lg-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Νέο email');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('new');?>>         

          <div class="form-group row">
            <label for="fromemail" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από');?>:</label>
            <div class="col-sm-8">
              <input id="fromemail" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SITE_EMAIL;?>" 
              placeholder="<?php echo gks_lang('π.χ. info');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              
            </div>
          </div>
          <div class="form-group row">
            <label for="toemail" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προς');?>:</label>
            <div class="col-sm-8">
              <input id="toemail" type="text" class="form-control form-control-sm myneedsave" value="" 
              placeholder="<?php echo gks_lang('π.χ. info@gks.gr');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πρότυπο');?>:</label>
            <div class="col-sm-8" style="font-size:0.875rem;">
            <?php 
            $sql_emailt="select id_email_template as id,email_template_descr as descr from gks_email_template where is_disable>=0 order by sortorder";
            $result_emailt = $db_link->query($sql_emailt); 
            if (!$result_emailt) {debug_mail(false,'error sql',$sql_emailt);die('sql error');} 
            $mytemplates=array();
            while ($row_emailt = $result_emailt->fetch_assoc()) {
              $mytemplates[]=$row_emailt;
            }
            

            $fisrt_name='';$fisrt_id=0;$fisrt_message='';
            foreach ($mytemplates as $onlyname) { 
              if ($fisrt_name=='') {
                $fisrt_name=$onlyname['descr']; 
                $fisrt_id=$onlyname['id'];
              }
              echo '<input type="radio" name="mytemplate" value="'.$onlyname['id'].'" id="template_'.$onlyname['id'].'" '.($onlyname['descr']==$fisrt_name ? 'checked': '' ).'> <a href="admin-email-templates-item.php?id='.$onlyname['id'].'" target="_blank">'.$onlyname['descr'].'</a><br/>';
            } ?>
              
            </div>
          </div>
          <div class="form-group row">
            <label for="subject" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Θέμα');?>:</label>
            <div class="col-sm-8">
              <input id="subject" type="text" class="form-control form-control-sm myneedsave" value="" 
              placeholder="<?php echo gks_lang('π.χ. Νέες υπηρεσίες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="messageemail" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-8">
              <textarea id="messageemail" type="text" class="gks_tinymce form-control form-control-sm myneedsave" value="" 
              placeholder="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="width:100%;height:400px;"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="email_params" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παράμετροι');?>:</label>
            <div class="col-sm-8">
              <div id="email_params" style="font-size:0.875rem;"></div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
    


    
    <div class="col-lg-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπισκόπηση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('preview');?>>  
          <div class="form-group row" id="preview_subject_wait" style="display:none;">
            <div class="col-sm-12">
              <?php echo gks_lang('Παρακαλώ περιμένετε');?> ...
            </div>
          </div>
          <div class="form-group row" id="preview_subject_div" style="display:none;">
            <label class="col-sm-12 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Θέμα');?>:</label>
            <div class="col-sm-12">
              <span class="form-control-sm" id="preview_subject_text" style="font-weight:bold;"></span>
            </div>
          </div>
          <div class="form-group row" id="preview_body_div" style="display:none;">
            <label class="col-sm-12 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-12" id="div_preview"></div>
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
      <button type="button" class="btn btn-primary" id="mybuttonemail"><?php echo gks_lang('Αποστολή','part2');?></button>
      <button type="button" class="btn btn-primary" id="mybuttonpreview"><?php echo gks_lang('Προεπισκόπηση');?></button>
      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>
            
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

  


  $('input[name=mytemplate]').click(function() {
    tid=parseInt($(this).val()); if (isNaN(tid)) tid=0;
    show_email_params(tid);
  });
  var prev_vals={};
  
  function show_email_params(template_id) {

    $("[id^=email_param_]").each(function( index ) {
      prev_vals[$(this).attr('id')]=$(this).val();
    });
    //console.log(prev_vals);
        
    //console.log(template_id);
    datasend='&id=' + template_id;
    datasend+='&cmd=getparams';
    //$('body').addClass('myloading');
	  $.ajax({
			url: '/my/admin-email-templates-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass('myloading');
				if (!data) {
				  myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
  				if (data.success==true){
  				  
  				  $('#email_params').html($.base64.decode(data.email_params));

            $("[id^=email_param_]").each(function( index ) {
              eid=$(this).attr('id');
              if (typeof prev_vals[eid] != 'undefined') {
                $('#' + eid).val(prev_vals[eid]);
              }
            });

            if (data.email_subject!='') {
              var_email_param_subject=$.base64.decode(data.email_subject);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_HUMAN_NAME]]',from_php_GKS_SITE_HUMAN_NAME);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_URL]]',from_php_GKS_SITE_URL);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_OFFICIAL_SITE_URL]]',from_php_GKS_OFFICIAL_SITE_URL);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_EMAIL]]',from_php_GKS_SITE_EMAIL);
              $("#subject").val(var_email_param_subject);
            }
            if (data.email_message!='') {
              var_email_param_message=$.base64.decode(data.email_message);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_HUMAN_NAME]]',from_php_GKS_SITE_HUMAN_NAME);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_URL]]',from_php_GKS_SITE_URL);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_OFFICIAL_SITE_URL]]',from_php_GKS_OFFICIAL_SITE_URL);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_EMAIL]]',from_php_GKS_SITE_EMAIL);
              //$("#messageemail").val(var_email_param_message);
              tinyMCE.get('messageemail').setContent(var_email_param_message);
            }
        	  
            $('#preview_subject_wait').hide();
            $('#preview_subject_div').hide();
            $('#preview_body_div').hide();
            $('#preview_subject_text').html('');
            $('#div_preview').html('');
            $('.set_def_bank_accounts').click(set_def_bank_accounts);
            $('.tooltipster_params').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;

        	  gks_myscroll();
        	  
  				} else {
  				  myalert('error:' + $.base64.decode(data.message));
  				}
  			}
			}
		});

  }
  <?php if ($fisrt_id>0) {
  echo '  setTimeout(function() {show_email_params('.$fisrt_id.');},2000);';
  
  }?>
  
  function mysubmit_send(ispreview) {
    if (ispreview==false && $("#fromemail").val().trim().length < 1) {
      myalert('error:'+gks_lang('Εισάγετε κάποιον αποστολέα'));
      return false;
    }
    if (ispreview==false && $("#toemail").val().trim().length < 1) {
      myalert('error:'+gks_lang('Εισάγετε κάποιον αποδέκτη'));
      return false;
    }
    if (ispreview==false && $("#subject").val().trim().length < 1) {
      myalert('error:'+gks_lang('Εισάγετε κάποιο θέμα'));
      return false;
    }
    checkmessage=true;
    if (typeof var_email_param_need_message !== 'undefined') {
      checkmessage=var_email_param_need_message;
    }
    //if (checkmessage && $("#messageemail").val().trim().length < 1) {
      //myalert('error:'+gks_lang('Εισάγετε κάποιο μήνυμα'));
      //return false;
    //}

    var email_param='';
    var each_result=true;
    $("[id^=email_param_]").each(function( index ) {
      if (ispreview==false && $(this).val().trim() == '') {
        
        
        myalert('error:'+gks_lang('Η παράμετρος <b>[1]</b> είναι κενή').replaceAll('[1]',$(this).attr('id').substring(12)));
        each_result=false;
        return false;
      }
      myeval=$(this).val().trim();
      if (myeval=='') myeval='[[' + $(this).attr('id').substr(12) + ']]';
      email_param+='&' + $(this).attr('id') + '=' + encodeURIComponent($.base64.encode(myeval.replaceAll("\n",'<br>')));
    });
    if (each_result == false) return false;    

    datasend='';
    datasend+='&ispreview='   + (ispreview ? '1' : '0');
    datasend+='&from='        + encodeURIComponent($.base64.encode($("#fromemail").val().trim()));
    datasend+='&to='          + encodeURIComponent($.base64.encode($("#toemail").val().trim()));
    datasend+='&mytemplate='  + encodeURIComponent($('input[name=mytemplate]:checked').val());
    datasend+='&subject='     + encodeURIComponent($.base64.encode($("#subject").val().trim()));
    datasend+='&message='     + encodeURIComponent($.base64.encode(tinyMCE.get('messageemail').getContent()));
    datasend+=email_param;

    if (ispreview) {
      $('#preview_subject_wait').show();
      $('#preview_subject_div').hide();
      $('#preview_body_div').hide();
    } else {
      $("body").addClass("myloading");
    }
    
    gks_myscroll();
    
    $('#calc_hourglass').show();
	  $.ajax({
			url: '/my/admin-email-send-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			thisispreview:ispreview,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
				$('#calc_hourglass').hide();
				if (this.thisispreview) {
			    $('#preview_subject_wait').hide();
				  $('#preview_subject_text').html(gks_lang('Σφάλμα'));
			    $('#preview_subject_div').show();
				  $('#div_preview').html(gks_lang('Σφάλμα'));
			    $('#preview_body_div').show();
				}
			},				
			success: function(data) {
			  $('#calc_hourglass').hide();
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
  				  if (this.thisispreview) {
			        $('#preview_subject_wait').hide();
  				    $('#preview_subject_text').html($.base64.decode(data.subject));
  				    $('#preview_subject_div').show();
  				    $('#div_preview').html('<iframe id="preview_iframe" src="' + $.base64.decode(data.preview_url) + '" style="width:100%;border:0px;height:300px;"></iframe>');
  				    $('#preview_body_div').show();
  				    gks_myscroll();
  				    $('#preview_iframe').on('load',  preview_iframe);
  				  } else {
    				  myalert('ok:' + $.base64.decode(data.message));
    				  return;
    				}
  				}
  			}
			}
		});          
  }
  
  
  $("#mybuttonemail").click(function() {
    mysubmit_send(false);    
  });

  $('#mybuttonpreview').click(function() {
    mysubmit_send(true);    
  });
   
   
  function preview_iframe() {
    myscrollHeight=this.contentWindow.document.body.scrollHeight;
    myscrollHeight+=50;
    this.style.height = myscrollHeight + 'px';
    gks_myscroll();
  }
  
  function set_def_bank_accounts() {
    $('#email_param_get_list_bank_accounts').val(from_php_get_list_bank_accounts.replaceAll('<br>',"\n"));
    
  }
  
 
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
    //editor.on('Change', function(e) {
    //  need_save=true;
    //});
    //editor.execCommand('mceAutoResize');
    
  },
  readonly : 0,
});

</script>

<?php
//db_close();
include_once('_my_footer_admin.php');

