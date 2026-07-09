/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


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

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

jQuery(document).ready(function($) {
  
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
  
  $('#submit_button_ok_custom').click(function(event) {mysubmit(); return false;});
    
  $('#lead_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#birthday').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));


  $('#lead_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });  
 
  $('#country_id').change(function() {
    var v=$(this).val();
    nomos_fill('nomos_id',v,0);
  });  
  
//  if (from_php_id==-1) {
//    v=parseInt($('#country_id').val());
//    if (isNaN()) v=0;
//    if (v>0) nomos_fill('nomos_id',v,0);
//  }  

  function mysubmit() {
    
    datasend='';
    datasend+='&lead_date=' + encodeURIComponent($("#lead_date").val().trim());
    //datasend+='&lead_status_id='  + encodeURIComponent($("#mypostform #lead_status_id").val().trim());

    datasend+='&lead_status_id=' + $('.lead_status_selected').attr('data-id');


    datasend+='&subject='  + encodeURIComponent($.base64.encode($("#mypostform #subject").val().trim()));
    datasend+='&message='  + encodeURIComponent($.base64.encode($("#mypostform #message").val().trim()));
    datasend+='&esoda=' + $('#mypostform #esoda').val();
    datasend+='&lead_color='  + encodeURIComponent($.base64.encode($("#mypostform #lead_color").val().trim()));
    datasend+='&internal_note='  + encodeURIComponent($.base64.encode($("#mypostform #internal_note").val().trim()));
    
    
    datasend+='&user_id=' + encodeURIComponent($("#user_id").val().trim());

    datasend+='&first_name='  + encodeURIComponent($.base64.encode($("#mypostform #first_name").val().trim()));
    datasend+='&last_name='  + encodeURIComponent($.base64.encode($("#mypostform #last_name").val().trim()));
    datasend+='&email='  + encodeURIComponent($.base64.encode($("#mypostform #email").val().trim()));
    datasend+='&mobile='  + encodeURIComponent($.base64.encode($("#mypostform #mobile").val().trim()));
    datasend+='&phone='  + encodeURIComponent($.base64.encode($("#mypostform #phone").val().trim()));
    datasend+='&web='  + encodeURIComponent($.base64.encode($("#mypostform #web").val().trim()));
    datasend+='&user_lang='  + encodeURIComponent($.base64.encode($("#mypostform #user_lang").val().trim()));
    datasend+='&birthday=' + encodeURIComponent($("#birthday").val().trim());
    
    
    datasend+='&form_select_apostoli=' +  encodeURIComponent($('#form_select_apostoli').val().trim());
    datasend+='&form_ea_name='  + encodeURIComponent($.base64.encode($("#mypostform #form_ea_name").val().trim()));
    datasend+='&form_ea_phone='  + encodeURIComponent($.base64.encode($("#mypostform #form_ea_phone").val().trim()));
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#mypostform #odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#mypostform #orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#mypostform #poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#mypostform #tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent(($("#mypostform #country_id").val().trim()));
    datasend+='&nomos_id='  + encodeURIComponent(($("#mypostform #nomos_id").val().trim()));
    datasend+='&map_latitude='  + encodeURIComponent(($("#mypostform #map_latitude").val().trim()));
    datasend+='&map_longitude='  + encodeURIComponent(($("#mypostform #map_longitude").val().trim()));
    datasend+='&company_id=' + encodeURIComponent($("#mypostform #company_id").val().trim());
    datasend+='&company_sub_id=' + encodeURIComponent($("#mypostform #company_sub_id").val().trim());

    datasend+='&eponimia='  + encodeURIComponent($.base64.encode($("#mypostform #eponimia").val().trim()));
    datasend+='&title='  + encodeURIComponent($.base64.encode($("#mypostform #title").val().trim()));
    datasend+='&afm='  + encodeURIComponent($.base64.encode($("#mypostform #afm").val().trim()));
    datasend+='&doy='  + encodeURIComponent($.base64.encode($("#mypostform #doy").val().trim()));
    datasend+='&epaggelma='  + encodeURIComponent($.base64.encode($("#mypostform #epaggelma").val().trim()));
    datasend+='&fiscal_position_id='  + encodeURIComponent(($("#mypostform #fiscal_position_id").val().trim()));
    datasend+='&pricelist_id='  + encodeURIComponent(($("#mypostform #pricelist_id").val().trim()));

    datasend+='&assigned_id='  + $("#mypostform #assigned_id").attr('data-id');
    datasend+='&crm_channel_id='  + $("#mypostform #crm_channel_id").val();
    datasend+='&crm_channel_contact_id='  + $("#mypostform #crm_channel_contact_id").attr('data-id');
    datasend+='&crm_channel_campain_id='  + $("#mypostform #crm_channel_campain_id").attr('data-id');
    datasend+='&crm_channel_url='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_url").val().trim()));
    datasend+='&crm_channel_code='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_code").val().trim()));
    datasend+='&crm_channel_text='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_text").val().trim()));


    datasend+=gks_custom_datasend();
        
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crm-lead-item-exec.php?id=' + from_php_id,
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

  $('#company').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-company.php',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $('#company_id').val(ui.item.id);
      $('#company_sub_title').val(gks_lang('Κεντρικό'));
      $('#company_sub_id').val('0'); 
      //console.log(ui.item);     
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company').val('');
          $('#company_id').val('');
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
    }
  });  
  
  $('#company_sub_title').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        company_id: $('#company_id').val(),
        and_kentriko:1,        
      };
      $.ajax({
        url: 'admin-autocomplete-company-sub.php',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $('#company_sub_id').val(ui.item.id);
            
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
    }
  });

  $('#user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
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
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      need_save=true;
      old_val=$("#user_id").val();
      $("#user_id").val(ui.item.id);
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();
      $('#user_save').hide();

      
      
      gks_admin_get_user_data(ui.item.id, false);
    
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#user").val('');
        $("#user_id").val('');
        $('#autocomplete_user_id').hide(); 
        $('#user_save').show();
        $('#div_pelati_sxolio').hide('fade', 'slow');
        $('#text_pelati_sxolio').html('');
                        
        $('#div_order_sxolio').hide('fade', 'slow');
        $('#text_order_sxolio').html('');   
           
			  $('#form_ea_name_div').hide();
			  $('#form_ea_phone_div').hide();
        $('#first_name').val('');
        $('#last_name').val('');
        $('#email').val('');
        $('#mobile').val('');
        $('#phone').val('');
        $('#web').val('');
        $('#user_lang').val('el-GR');
        $('#odos').val('');
        $('#arithmos').val('');
        $('#orofos').val('');
        $('#perioxi').val('');
        $('#poli').val('');
        $('#tk').val('');
  
        $('#country_id').val(91);
        $('#nomos_id').val('0');

        $('#form_select_apostoli option').each(function() { 
          $(this).remove();
        }); 
			  $('#form_select_apostoli').append('<option value="-1">'+gks_lang('Βασική διεύθυνση')+'</option>');
			  //$('#form_select_apostoli').append('<option value="0">'+gks_lang('Δημιουργία νέας διεύθυνσης')+'</option>');
        $('#map_latitude').val('');
        $('#map_longitude').val('');
        map_set_point();
                    
        $('#eponimia').val('');
        $('#title').val('');
        $('#afm').val('');
        $('#doy').val('');
        $('#epaggelma').val('');
       
        $('#fiscal_position_id').val(1);
        $('#pricelist_id').val(1);

        $('#birthday').val('');
        


     
        gks_myscroll(); 
            
      }
    }
  });

  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
      
    datasend='cmd=get&id=' + user_id + '&lead_id=' + from_php_id;
    $.ajax({
			url: 'admin-get-user-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_dialog_gsis_result:dialog_gsis_result,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log('gks_admin_get_user_data res'); 
					  //console.log(data);
					  //console.log(this.gks_dialog_gsis_result);
					  
            if (data.pelati_sxolio=='') {
              $('#div_pelati_sxolio').hide('fade', 'slow');
              $('#text_pelati_sxolio').html('');
            } else {
              $('#div_pelati_sxolio').show('fade', 'slow');
              $('#text_pelati_sxolio').html(data.pelati_sxolio);
            }
            if (data.order_sxolio=='') {
              $('#div_order_sxolio').hide('fade', 'slow');
              $('#text_order_sxolio').html('');
            } else {
              $('#div_order_sxolio').show('fade', 'slow');
              $('#text_order_sxolio').html(data.order_sxolio);
              
              mytext=$('#text_order_sxolio').text();
              exit_text=$('#note_production').val();
              if (exit_text!='') exit_text+="\r\n";
              exit_text+=mytext;
              $('#note_production').val(exit_text);
              //$('#note_production').focus();
            }
            
            $('#first_name').val(data.first_name);
            $('#last_name').val(data.last_name);
            $('#email').val(data.email);
            $('#mobile').val(data.mobile);
            $('#phone').val(data.phone_home);
            $('#web').val(data.user_url);
            
            $('#user_lang').val(data.lang);
            
            $('#fiscal_position_id').val(data.fiscal_position_id);
            $('#pricelist_id').val(data.pricelist_id);
            

            $('#form_select_apostoli option').each(function() { 
              $(this).remove();
            }); 
            for (i = 0; i < data.extra_address.length; i++) {
  				    $('#form_select_apostoli').append('<option value="' + data.extra_address[i].id + '">' + data.extra_address[i].descr + '</option>');
  				  }
  				  $('#form_ea_name_div').hide();
  				  $('#form_ea_phone_div').hide();
  				                
            $('#map_latitude').val(data.ma_latitude);
            $('#map_longitude').val(data.ma_longitude);
            map_set_point();
            
            $('#birthday').val(data.genisi_date);
                                    
            if (this.gks_dialog_gsis_result === false) {
              //console.log('gks_dialog_gsis_result false');
              $('#odos').val(data.ma_odos);
              $('#arithmos').val(data.ma_arithmos);
              $('#orofos').val(data.ma_orofos);
              $('#perioxi').val(data.ma_perioxi);
              $('#poli').val(data.ma_poli);
              $('#tk').val(data.ma_tk);
              $('#country_id').val(data.ma_country_id);
              nomos_fill('nomos_id',data.ma_country_id,data.ma_nomos_id);
              country_id_change();

              $('#eponimia').val(data.eponimia);
              $('#title').val(data.title);
              $('#afm').val(data.afm);
              $('#doy').val(data.doy);
              $('#epaggelma').val(data.epaggelma);
                    
              $('#fiscal_position_id').val(data.fiscal_position_id);
                   
            } else {
              //console.log('gks_dialog_gsis_result true');
      				mynymber=this.gks_dialog_gsis_result.basic_rec.postal_address_no.trim();
      				if (mynymber=='0') mynymber='';
      				              
              $('#odos').val((this.gks_dialog_gsis_result.basic_rec.postal_address).trim());
              $('#arithmos').val(mynymber);
              $('#poli').val(this.gks_dialog_gsis_result.basic_rec.postal_area_description);
              $('#tk').val(this.gks_dialog_gsis_result.basic_rec.postal_zip_code);
              $('#country_id').val(91);
              nomos_fill('nomos_id',91,data.ma_nomos_id);
              country_id_change();
              
              $('#eponimia').val(this.gks_dialog_gsis_result.basic_rec.onomasia);
              $('#title').val(this.gks_dialog_gsis_result.basic_rec.commer_title);
              $('#afm').val(this.gks_dialog_gsis_result.basic_rec.afm);
              $('#doy').val(this.gks_dialog_gsis_result.basic_rec.doy_descr);
              $('#epaggelma').val('');
              for (i=0;i < this.gks_dialog_gsis_result.firm_act_tab.length; i++) {
                if (this.gks_dialog_gsis_result.firm_act_tab[i].kind=='1') {
                  $('#epaggelma').val(this.gks_dialog_gsis_result.firm_act_tab[i].cdescr);
                  break;
                }
              }

              if (this.gks_dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
                $('#fiscal_position_id').val(11);
              } else {
                $('#fiscal_position_id').val(1);
              }
                            
            }
            
            calc_pliroteo();
            gks_myscroll();
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
  } 

  function message_change() {gks_resize_textarea($(this));}
  $('#message').on('change keyup paste', message_change);
  gks_resize_textarea($('#message'));

  function internal_note_change() {gks_resize_textarea($(this));}
  $('#internal_note').on('change keyup paste', internal_note_change);
  gks_resize_textarea($('#internal_note'));

  $('#copy_text_pelati_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_pelati_sxolio').text();
    exit_text=$('#internal_note').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#internal_note').val(exit_text);
    $('#internal_note').focus();
    gks_resize_textarea($('#internal_note'));
  });  

  $('#copy_text_order_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_order_sxolio').text();
    exit_text=$('#internal_note').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#internal_note').val(exit_text);
    $('#internal_note').focus();
    gks_resize_textarea($('#internal_note'));
  });
  


  var dialog_gsis;
  var dialog_gsis_result=false;
  dialog_gsis = $( "#dialog_gsis" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_gsis_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Σύστασης'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          
          
          if (dialog_gsis_result.user_id>0) {
            $('#user_id').val(dialog_gsis_result.user_id);
            $('#user').val(dialog_gsis_result.gks_nickname);
            $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + dialog_gsis_result.user_id);
            $('#user_save').hide();
            
            gks_admin_get_user_data(dialog_gsis_result.user_id, dialog_gsis_result);
            
          } else {

            if (dialog_gsis_result.basic_rec.i_ni_flag_descr =='ΦΠ') {
              onomasia_parts = dialog_gsis_result.basic_rec.onomasia.split(' ');
              if (onomasia_parts.length>=2) {
                if ($('#first_name').val()=='') $('#first_name').val(onomasia_parts[1].trim());
                if ($('#last_name').val()=='')  $('#last_name').val(onomasia_parts[0].trim());
              }
            }
            //$('#email').val('');
            //$('#mobile').val('');
            if ($('#user_lang').val()=='') $('#user_lang').val('el-GR');
  
                       
  
    				$('#eponimia').val(dialog_gsis_result.basic_rec.onomasia);
    				$('#title').val(dialog_gsis_result.basic_rec.commer_title);
    				$('#afm').val(dialog_gsis_result.basic_rec.afm);
    				$('#doy').val(dialog_gsis_result.basic_rec.doy_descr);
            //$('#epaggelma').val('');
            for (i=0;i < dialog_gsis_result.firm_act_tab.length; i++) {
              if (dialog_gsis_result.firm_act_tab[i].kind=='1') {
                if ($('#epaggelma').val()=='') $('#epaggelma').val(dialog_gsis_result.firm_act_tab[i].cdescr);
                break;
              }
            }
    				mynymber=dialog_gsis_result.basic_rec.postal_address_no.trim();
    				if (mynymber=='0') mynymber='';
    				if ($('#odos').val()=='') $('#odos').val(dialog_gsis_result.basic_rec.postal_address.trim());
    				if ($('#arithmos').val()=='') $('#arithmos').val(mynymber);
            //$('#perioxi').val('');
    				if ($('#poli').val()=='') $('#poli').val(dialog_gsis_result.basic_rec.postal_area_description);
    				if ($('#tk').val()=='') $('#tk').val(dialog_gsis_result.basic_rec.postal_zip_code);
            if ($('#country_id').val()=='0') $('#country_id').val(91);
            if ($('#nomos_id').val()=='0') $('#nomos_id').val('0');
            country_id_change();
            $('#map_latitude').val('');
            $('#map_longitude').val('');
            map_set_point();
            
            
            $('#form_select_apostoli option').each(function() { 
              if ($(this).attr('value') > 0 ) {
                $(this).remove();
              }
            });
            //$('#form_select_apostoli').val(-1);
            //extra_address_select(-1);
            //if ($('#pricelist_id').val()=='0') $('#pricelist_id').val(1);
            //$('#def_ekptosi').val(0);
            if (dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
              if ($('#fiscal_position_id').val()=='0' || $('#fiscal_position_id').val()=='1') $('#fiscal_position_id').val(11);
              $('#pricelist_id').val(2);
              //$('#form_parastatiko_timologio').click();
            } else {
              if ($('#fiscal_position_id').val()=='0') $('#fiscal_position_id').val(1);
              $('#pricelist_id').val(1);
              //$('#form_parastatiko_apodiji').click();
            }
            //$('#order_occasion').val('');
            //$('#order_occasion_id').val(0);
            
            

            gks_myscroll();
            calc_pliroteo();
          }
          
          $( this ).dialog( "close" );
        }
        //showText: false
      },
      {
        id: "dialog_gsis_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
        

  });


  
  $('#btn_gsis_get').click(function() {
    $('#dialog_gsis_afm').val($('#afm').val());
    $('#dialog_gsis_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_gsis.dialog('option', 'width', dwidth);
	  dialog_gsis.dialog('option', 'height', dheight);
	  $('#dialog_gsis').parent().css({position:'fixed'});     
    dialog_gsis.dialog('open');    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    
  });  
  
  $('#dialog_gsis_run').click(function() {
    //console.log('dialog_gsis_run');
    dialog_gsis_result=false;
    
    dialog_gsis_afm=$('#dialog_gsis_afm').val().trim();
    if (dialog_gsis_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    $('#dialog_gsis_html').html('');
    
    
    company_id=0;
    company_sub_id=0;
    v=$('#company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
        
    datasend='afm=' + dialog_gsis_afm + '&company_id=' + company_id + '&force=1';;

    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-get-gisis.php',
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
				//console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  dialog_gsis_result=data.out;
					  
  					outhtml='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
  					if (dialog_gsis_result.valid==1) { //true
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:green;color:white;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else if (dialog_gsis_result.valid==2) { //wait
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:yellow;color:yellow;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else {
  				    outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:red;color:white;">' + dialog_gsis_result.error_text + '</div>';
  				  }
  					
  					
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Πεδίο')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Τιμή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΑΦΜ')+':</td><td>' + data.out.basic_rec.afm + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ ID')+':</td><td>' + data.out.basic_rec.doy + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ')+':</td><td>' + data.out.basic_rec.doy_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο')+':</td><td>' + data.out.basic_rec.i_ni_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ο Α.Φ.Μ. είναι ενεργός ή απενεργοποιημένος')+':</td><td>' + data.out.basic_rec.deactivation_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επιτηδευματίας, μη επιτηδευματίας ή πρώην επιτηδευματίας')+':</td><td>' + data.out.basic_rec.firm_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επωνυμία Επιχείρησης')+':</td><td>' + data.out.basic_rec.onomasia + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Τίτλος Επιχείρησης')+':</td><td>' + data.out.basic_rec.commer_title + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιγραφή μορφής Νομικού Προσώπου / Νομικής Οντότητας')+':</td><td>' + data.out.basic_rec.legal_status_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Διεύθυνση Έδρας Επιχείρησης')+':</td><td>' + data.out.basic_rec.postal_address + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Αριθμός')+':</td><td>' + data.out.basic_rec.postal_address_no + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΤΚ')+':</td><td nowrap>' + data.out.basic_rec.postal_zip_code + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιοχή')+':</td><td>' + data.out.basic_rec.postal_area_description + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Έναρξης')+':</td><td>' + data.out.basic_rec.regist_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Διακοπής')+':</td><td>' + data.out.basic_rec.stop_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α')+':</td><td>' + data.out.basic_rec.normal_vat_system_flag + '</td></tr>';
  					outhtml+='</tbody></table>';
  					
            outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Δραστηριότητες Επιχείρησης')+'</p>';
              
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:30%">'+gks_lang('Τύπος')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:20%">'+gks_lang('Κωδικός')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Περιγραφή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					for (i=0;i < data.out.firm_act_tab.length; i++) {
  					  outhtml+='<tr><td scope="row" style="text-align: center !important;">' + data.out.firm_act_tab[i].kdescr + '</td><td style="text-align: center !important;">' + data.out.firm_act_tab[i].code + '</td><td>' + data.out.firm_act_tab[i].cdescr+ '</td></tr>';
  					}
  					outhtml+='</tbody></table>';
  					$('#dialog_gsis_html').html(outhtml);
  					$('#dialog_gsis_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
		    
    
    
  }); 

  var dialog_user_save;
  dialog_user_save = $( "#dialog_user_save" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_user_save_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Προσθήκη ή επιλογή χρήστη'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          
          sel_elem=$('input[name=dialog_user_save_radio]:checked');
          if (sel_elem.length==0) {
            myalert('error:'+gks_lang('Επιλέξτε ή την προσθήκη νέου χρήστη ή έναν υπάρχον χρήστη'));
            return;  
          }
          select_user_id=sel_elem.val();
          //console.log(select_user_id);
          datasend=dialog_user_save.datasend;
          datasend+='&force=1&select_user_id=' + select_user_id;
          //console.log(datasend);
          
          $('body').addClass("myloading");
          $.ajax({
      			url: '/my/admin-users-add-exec.php',
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
                  $('#user_id').val(data.user_id);
                  $('#user').val(data.gks_nickname);
                  $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
                  $('#user_save').hide();
                  dialog_user_save.dialog( "close" );     					  
      					} else {
      					  myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      		});
      					            
          
          //$(this).dialog( "close" );
        }
        //showText: false
      },
      {
        id: "dialog_user_save_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
        

  });
  
  $('#user_save').click(function() {
    
    datasend='';    
    datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($("#first_name").val().trim()));
    datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($("#last_name").val().trim()));
    datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($("#email").val().trim()));
    datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($("#mobile").val().trim()));
    datasend+='&dr_user_phone='  + encodeURIComponent($.base64.encode($("#phone").val().trim()));
    datasend+='&genisi_date='  + encodeURIComponent($("#birthday").val().trim());
    datasend+='&user_url='  + encodeURIComponent($.base64.encode($("#web").val().trim()));
    datasend+='&ma_latitude='  + encodeURIComponent($("#map_latitude").val().trim());
    datasend+='&ma_longitude='  + encodeURIComponent($("#map_longitude").val().trim());
   
    datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($("#user_lang").val().trim()));
    
    datasend+='&form_select_apostoli=' +  encodeURIComponent($('#form_select_apostoli').val().trim());
    datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($("#odos").val().trim()));
    datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($("#arithmos").val().trim()));
    datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($("#orofos").val().trim()));
    datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($("#perioxi").val().trim()));
    datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($("#poli").val().trim()));
    datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($("#tk").val().trim()));
    datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#country_id").val().trim());
    datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#nomos_id").val().trim());
    
    //datasend+='&form_parastatiko=' +      encodeURI($('input[name=form_parastatiko]:checked').val());
    datasend+='&dr_user_eponimia='  + encodeURIComponent($.base64.encode($("#eponimia").val().trim()));
    datasend+='&dr_user_title='  + encodeURIComponent($.base64.encode($("#title").val().trim()));
    datasend+='&dr_user_afm='  + encodeURIComponent($.base64.encode($("#afm").val().trim()));
    datasend+='&dr_user_doy='  + encodeURIComponent($.base64.encode($("#doy").val().trim()));
    datasend+='&dr_user_epaggelma='  + encodeURIComponent($.base64.encode($("#epaggelma").val().trim()));
    datasend+='&ma_latitude='  + encodeURIComponent(($("#mypostform #map_latitude").val().trim()));
    datasend+='&ma_longitude='  + encodeURIComponent(($("#mypostform #map_longitude").val().trim()));


//    datasend+='&form_ea_name=' +          encodeURIComponent($.base64.encode($('#form_ea_name').val().trim()));
//    datasend+='&form_ea_phone=' +         encodeURIComponent($.base64.encode($('#form_ea_phone').val().trim()));
//    datasend+='&form_ea_odos=' +          encodeURIComponent($.base64.encode($('#form_ea_odos').val().trim()));
//    datasend+='&form_ea_perioxi=' +       encodeURIComponent($.base64.encode($('#form_ea_perioxi').val().trim()));
//    datasend+='&form_ea_poli=' +          encodeURIComponent($.base64.encode($('#form_ea_poli').val().trim()));
//    datasend+='&form_ea_tk=' +            encodeURIComponent($.base64.encode($('#form_ea_tk').val().trim()));
//    if ($('#form_ea_country_id').val()==null)$('#form_ea_country_id').val(0);
//    if ($('#form_ea_nomos_id').val()==null)$('#form_ea_nomos_id').val(0);
//    datasend+='&form_ea_country_id=' +    encodeURIComponent($('#form_ea_country_id').val().trim());
//    datasend+='&form_ea_nomos_id=' +      encodeURIComponent($('#form_ea_nomos_id').val().trim());
    datasend+='&fiscal_position_id=' +      encodeURIComponent($('#fiscal_position_id').val().trim());
    datasend+='&pricelist_id=' +      encodeURIComponent($('#pricelist_id').val().trim());
    datasend+='&def_ekptosi=' + $('#def_ekptosi').val();

    datasend+='&crm_lead_id=' + from_php_id;

    
    //console.log('user_save');
    //console.log(datasend);
    
    dialog_user_save.datasend=datasend;
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-users-add-exec.php',
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
				  
				    if (data.ask_user) {
				      //console.log(data.exist_rows);
    				      
    				  outhtml='';
    					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
    					outhtml+='<thead><tr>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:0%">'+gks_lang('Α/Α')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Επιλογή')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:30%">'+gks_lang('Υποκοριστικό')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:60%">'+gks_lang('Αναζήτηση')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Προβολή')+'</th>';
    					outhtml+='</tr></thead><tbody>';
  
    					for (i=0;i < data.exist_rows.length; i++) {
    					  outhtml+='<tr>' + 
    					  '<td scope="row" style="text-align: center !important;" nowrap>' + (i + 1) + '</td>' +
    					  '<td style="text-align: center !important;">' + '<input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_' + data.exist_rows[i].ID + '" value="' + data.exist_rows[i].ID + '"  title="'+gks_lang('Επιλογή χρήστη')+'"></td>' +
    					  '<td><label class="gks_label" for="dialog_user_save_radio_' + data.exist_rows[i].ID + '" style="vertical-align: middle;">' + data.exist_rows[i].gks_nickname + '</label></td>' + 
    					  '<td>' + data.exist_rows[i].descrs.join('<br>') + '</td>' +
    					  '<td style="text-align: center !important;"><a href="admin-users-item.php?id=' + data.exist_rows[i].ID + '" target="_blank"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή χρήστη')+'"></i></a></td>' + 
    					  '</tr>';
    					}
    					outhtml+='</tbody></table>';
  					  
  					  $('#dialog_user_save_html').html(outhtml);
          	  dwidth=$(window).width() * 0.96;
              dheight=$(window).height() * 0.96;
          	  if (dwidth> 850) dwidth=850;
          	  //if (dheight> 600) dheight=600;
          	  $('#dialog_user_save_radio_new').prop('disabled', false);
          	  dialog_user_save.dialog('option', 'width', dwidth);
          	  dialog_user_save.dialog('option', 'height', dheight);
          	  $('#dialog_user_save').parent().css({position:'fixed'});
              dialog_user_save.dialog('open');    
  					  			      
				    } else {
              $('#user_id').val(data.user_id);
              $('#user').val(data.gks_nickname);
              $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
              $('#user_save').hide();				      
				    }
				    
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}				
	  });    
    
  });
  
  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
      place_map_latitude = parseFloat(jQuery('#map_latitude').val()); if (isNaN(place_map_latitude)) place_map_latitude=0; 
      place_map_longitude = parseFloat(jQuery('#map_longitude').val()); if (isNaN(place_map_longitude)) place_map_longitude=0;
      $('#map').parent().css('height','500px').css('margin-top','10px');
      
      showmap_run();
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map_pos, #geocode_pos').prop('disabled',false);
    } else {
      
      if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
        $('#map_pos, #geocode_pos').prop('disabled',true);
        $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
        $('#map').parent().hide();
      } else {
        map_set_point();
        
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map').parent().show();
      }
    }
    gks_myscroll();
  
  });

  
  function map_set_point() {
    if (map_is_open==false) return;
    
    place_map_latitude = parseFloat(jQuery('#map_latitude').val()); if (isNaN(place_map_latitude)) place_map_latitude=0; 
    place_map_longitude = parseFloat(jQuery('#map_longitude').val()); if (isNaN(place_map_longitude)) place_map_longitude=0;
    var pos = {
      lat: place_map_latitude,
      lng: place_map_longitude
    };    
    map.setCenter(pos);
    marker.position=pos;
    
  }

  
  $('#map_pos').click(function(event){
    if (infoWindow_userpos==null) infoWindow_userpos = new google.maps.InfoWindow({map: map});
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
  
        
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        map.setCenter(pos);
        
          
        marker.position=pos;
        place_map_latitude = marker.position.lat;
        place_map_longitude = marker.position.lng;
        infoWindow_userpos.open(map, marker);
        map.setZoom(17);
      
          
        $('#map_latitude').val(place_map_latitude);
        $('#map_longitude').val(place_map_longitude);
        need_save=true;
        
      }, function() {
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });  
  

  $('#geocode_pos').tooltipster();
  $('#geocode_pos').click(function() {
    
    datasend='';
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#nomos_id").val().trim());
    
    $('#geocode_pos').prop('disabled',true);
    $('#geocode_pos_icon').html('<i class="fas fa-hourglass"></i>');
    //console.log(datasend);
    $.ajax({
			url: '/my/admin-get-geocode_pos.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#geocode_pos').prop('disabled',false);
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#map_latitude' ).val(data.pos.lat);
					  $('#map_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title','GEO:' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  });
  


  $('#country_id').change(function() {
    country_id_change();
    v=parseInt($('#country_id').val());
    if (isNaN(v)) v=0;
    //console.log(v);
    nomos_fill('nomos_id',v,0);
    calc_pliroteo();    
  });
  
  
  function country_id_change() {
    v=$('#country_id').val();
    
    data_ee=$('#country_id').find('OPTION[value=' + v + ']').attr('data-ee');
    $('#dr_user_afm_views_run').hide();
    if (data_ee=='') {
      $('#dr_user_afm_ee_initials').hide().html('');
      $('#afm').css('width','100%').removeClass('dr_user_afm_views');
    } else {
      $('#dr_user_afm_ee_initials').show().html(data_ee);
      $('#afm').css('width','calc(100% - 75px)').addClass('dr_user_afm_views');
    }
  }

  $('#doy').autocomplete({
    source: "doy-autocomplete.php",
    minLength: 1,
    autoFocus: true,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $("#doy").val(ui.item.value);
    },
  });

  $('#afm').change(function() {
    calc_pliroteo();
  });

  $('#afm').on('input keyup paste', function() {
    $('#dr_user_afm_views_run').hide();
  });
  
  
  var calc_pliroteo_xhr;
  var calc_pliroteo_timer=null;
  function calc_pliroteo() {
    //console.log('calc_pliroteo');
    need_save=true;
    
    check_vies_valid_wait_timer_stop();
    
    if(calc_pliroteo_xhr && calc_pliroteo_xhr.readyState != 4){
      calc_pliroteo_xhr.abort();
    }
    if (calc_pliroteo_timer!=null) clearTimeout(calc_pliroteo_timer);
    calc_pliroteo_timer=setTimeout(calc_pliroteo_run,400);
  }
  function calc_pliroteo_run() {
    $('#calc_hourglass').show();    
    
    company_id=parseInt($('#company_id').val());
    if (isNaN(company_id)) company_id=0;
    company_sub_id=parseInt($('#company_sub_id').val());
    if (isNaN(company_sub_id)) company_sub_id=0;
    
    
    mydata={};
    mydata.company_id=company_id;
    mydata.company_sub_id=company_sub_id;
    mydata.user_id=$('#user_id').val();
    mydata.afm = $('#afm').val();
    mydata.ma_country_id = $('#country_id').val();

    mydata_str = encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
    datasend='&mydata_str=' + mydata_str;
        
    calc_pliroteo_xhr = $.ajax({
			url: '/my/admin-crm-lead-item-calc-basket.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				if (textStatus != 'abort') myalert('error:' + jqXHR.responseText);
				$('#calc_hourglass').hide();
			},				
			success: function(data) {
			  need_save=true;
			  $('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  if (data.check_vies.views_run_img!='') {
              $('#dr_user_afm_views_run').html(data.check_vies.views_run_img).show();
              $('#dr_user_afm_views_run .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
              if (data.check_vies.valid==2) check_vies_valid_wait_timer_restart();
            } else {
              $('#dr_user_afm_views_run').hide();
            }
            
  					gks_myscroll();
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
    
  }
  
  
  $('.lead_status_this').click(function() {
    if (from_php_perm_ret_edit==false) return;
    $('.lead_status_this').each(function() {
      $(this).removeClass('lead_status_selected');
    });
    $(this).addClass('lead_status_selected');
  });
  
  
  $('#activity_add').click(function() {
    event.stopPropagation();
    
  });
  

  $('#form_select_apostoli').change(function() {
    v=$(this).val();
    extra_address_select(v);
    gks_myscroll();
    calc_pliroteo();
  });

  function extra_address_select(v) {
    
    
    if (v ==-1) {
      $('#form_ea_name_div').slideUp();
      $('#form_ea_phone_div').slideUp();
    } else {
      $('#form_ea_name_div').slideDown();
      $('#form_ea_phone_div').slideDown();
    }
    if (v==0) {
      $('#form_ea_name').val('');
      $('#form_ea_phone').val('');
      $('#odos').val('');
      $('#arithmos').val('');
      $('#orofos').val('');
      $('#perioxi').val('');
      $('#poli').val('');
      $('#tk').val('');
      $('#map_latitude').val('');
      $('#map_longitude').val('');
      map_set_point();
      
      v1 = 0; //parseInt($('#dr_user_ma_country_id').val());
      if (isNaN(v1)) v1=0;
      v2 = 0; //parseInt($('#dr_user_ma_nomos_id').val());
      if (isNaN(v2)) v2=0;
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
    } else {
      v1=0;
      v2=0;
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
      mydata = 'aid=' + v + '&user_id=' + $('#user_id').val();
      //console.log(mydata);
      
      $.ajax({
          url: "/my/admin-get-address.php",
          type: 'POST',
          cache: false,
          dataType: "json",
          data:mydata,
          error : function(jqXHR ,textStatus,  errorThrown) {
  				  myalert('error:' + jqXHR.responseText);
  			  },
          success: function(data) {
            if (!data) {
    					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    				} else {
              if (data.success == true) {
                //console.log(data);

                $('#form_ea_name').val(data.data.ea_name);
                $('#form_ea_phone').val(data.data.ea_phone);
                $('#odos').val(data.data.ea_odos);
                $('#arithmos').val(data.data.ea_arithmos);
                $('#orofos').val(data.data.ea_orofos);
                $('#perioxi').val(data.data.ea_perioxi);
                $('#poli').val(data.data.ea_poli);
                $('#tk').val(data.data.ea_tk);
                $('#country_id').val(data.data.ea_country_id);
                $('#map_latitude').val(data.data.ea_latitude);
                $('#map_longitude').val(data.data.ea_longitude);
                nomos_fill('nomos_id',data.data.ea_country_id,data.data.ea_nomos_id);
                map_set_point();
              } else {
                myalert('error:' + $.base64.decode(data.message));
              }
            }
          }
      });
    }
  }  
  
  
  
  


  
  $('#add_links_url').click(function(event) {  
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την ευκαιρία'));
      return;
    }
    
    links_url=$("#links_url").val().trim();
    if (links_url=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε πρώτα την διεύθυνση'));
      return;
    }
    
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&link='  + encodeURIComponent($.base64.encode(links_url));    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crm-lead-item-add-link.php',
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
  					//myalert('ok:' + 'OK');
  					myhtml=$.base64.decode(data.html);
  					//console.log(myhtml);
  					$('#tr_new_links_url').before(myhtml);
            
            $('#links_url').val('');
            $('#tr_links_url_' + data.trid).find('.deleterow').click(deleterow_click); 
  					$('#tr_links_url_' + data.trid).find('.download_action_start').click(download_action_start_click);

            var links_aa=0;
            $('#links_table .links_aa').each(function () {
              links_aa++;
              $(this).html(links_aa);  
            }); 
        					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  });










  



   
  
  
  function download_action_start_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'start');
  }
  $('.download_action_start').click(download_action_start_click);
  
  function download_action_stop_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'stop');
  }
  $('.download_action_stop').click(download_action_stop_click);

  function download_action_reset_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'reset');
  }
  $('.download_action_reset').click(download_action_reset_click);

  $('.download_action_complete').click(download_action_reset_click);
  
  
  function download_action_func(data_id, myaction) {
    //console.log(data_id);
    //console.log(myaction);

    datasend='&id=' + data_id + '&action=' + myaction;
    
    $.ajax({
			url: '/my/admin-crm-lead-item-link-action.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					//console.log(data);
  					if (data.action=='start') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-stop-circle download_action_stop" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>'); 
  					  $('.download_action_stop[data-id=' + data.id + ']').click(download_action_stop_click);
  					  $('.download-perc[data-id=' + data.id + ']').show();
  					  
  					  download_timer_start();
  					  
  					} else if (data.action=='stop') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
  					  $('.download_action_reset[data-id=' + data.id + ']').click(download_action_reset_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').html(gks_lang('Ακυρώθηκε από τον χρήστη')).show();
  					  download_timer_start();
  					} else if (data.action=='reset') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-file-download download_action_start" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>'); 
  					  $('.download_action_start[data-id=' + data.id + ']').click(download_action_start_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').hide();
  					  download_timer_start();
  					  
  					  
  					}
  					
  					
  					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  }
  
  var download_timer=null;
  
  
  function download_timer_run() {
    fff=new Date();
    //console.log(fff);
    $.ajax({
			url: '/my/admin-crm-lead-item-link-timer.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: '',
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				download_timer = setTimeout(download_timer_run, 2000);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					download_timer = setTimeout(download_timer_run, 2000);
				} else {
				  
					if (data.success == true) {
  					//console.log(data);
            for(i=0;i<data.data.length;i++) {
              $('.download_size_until_now[data-id=' + data.data[i].id + ']').html(data.data[i].now);
              if (data.data[i].status==2) { //ok exei teleiosei
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
                  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
                  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-check-circle download_action_complete" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>');
      					  $('.download-message[data-id=' + data.data[i].id + ']').hide();
      					  $('.download_action_complete[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
                }
              } else if (data.data[i].status==1) { //status =1
                $('.download-perc-bar[data-id=' + data.data[i].id + ']').attr('aria-valuenow', data.data[i].per).css('width', data.data[i].per + '%');
              } else if (data.data[i].status==3) { 
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
      					  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
      					  $('.download_action_reset[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
      					  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
      					  $('.download-message[data-id=' + data.data[i].id + ']').show();  
      					  $('.download-message[data-id=' + data.data[i].id + ']').html(data.data[i].msg);
      					}
              }
            }

  					
  					
  					//for(i=0;i<data.complete_td.length;i++) {
  					for(i=0;i<data.complete_td.length;i++) {
  					  if ($('.tddd[data-path="' + data.complete_td[i].relpath + '"]').length==0) {
  					    //console.log('|' + data.complete_td[i].relpath + '|');
  					    $('#filesobjectlist_table_imagelist_photo').show();
                //$('#filesobjectlist_table_imagelist_photo tr:last').after(data.complete_td[i].td);
                if ($('#filesobjectlist_table_imagelist_photo tbody tr').length==0) {
                  $('#filesobjectlist_table_imagelist_photo tbody').append(data.complete_td[i].td);
                } else {
                  $('#filesobjectlist_table_imagelist_photo tbody tr:last').after(data.complete_td[i].td);
                }                
                
                $('.filesobjectlist_delete_upload_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_delete_upload_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] td.tdimg_descr').click(filesobjectlist_edit_descr_click);
                $('.filesobjectlist_set_print_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_set_print_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
  
                $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
                $("#filesobjectlist_table_imagelist_photo").lightGallery({
                	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
                	thumbnail:true,
                	hideBarsDelay:1000,
                });   					    
  					  }		  
  					}

  					if (data.data.length<=0) {
  					  download_timer_stop();
  					} else {
  					  //setTimeout(, 3000);
  					  download_timer = setTimeout(download_timer_run, 2000);
  					}  					  					
  					//console.log(data);
  					  
					} else {
					  download_timer = setTimeout(download_timer_run, 2000);
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  
  function download_timer_stop() {
    clearTimeout(download_timer);
    download_timer=null;
  }
  function download_timer_start() {
    if (download_timer!=null) return;
    //download_timer = setInterval(download_timer_run, 1000);
    download_timer = setTimeout(download_timer_run, 2000);
    
  }
  if (from_php_need_download_timer==1) {
    download_timer_start();
  }


  window.gks_fnc_links_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('#tr_links_url_' + myargs[0]).hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var links_aa=0;
      $('#links_table .links_aa').each(function () {
        links_aa++;
        $(this).html(links_aa);  
      });    
    });
  }

  $('#assigned_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $('#assigned_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#assigned_id').val('').attr('data-id','0');
        }
    }
  });

  
  $('#crm_channel_id').change(function() {
    has_contact = parseInt($('#crm_channel_id option:selected').attr('data-contact'));
    has_campain = parseInt($('#crm_channel_id option:selected').attr('data-campain'));
    has_url = parseInt($('#crm_channel_id option:selected').attr('data-url'));
    has_code = parseInt($('#crm_channel_id option:selected').attr('data-code'));
    has_text = parseInt($('#crm_channel_id option:selected').attr('data-text'));
    //console.log(has_text,has_contact,contact_filter,has_campain,has_url);
    if (has_contact==0) $('#crm_channel_contact_id_div').slideUp(); else $('#crm_channel_contact_id_div').slideDown();
    if (has_campain==0) $('#crm_channel_campain_id_div').slideUp(); else $('#crm_channel_campain_id_div').slideDown();
    if (has_url==0) $('#crm_channel_url_div').slideUp(); else $('#crm_channel_url_div').slideDown();
    if (has_code==0) $('#crm_channel_code_div').slideUp(); else $('#crm_channel_code_div').slideDown();
    if (has_text==0) $('#crm_channel_text_div').slideUp(); else $('#crm_channel_text_div').slideDown();
    
  });
  


  $('#crm_channel_contact_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      contact_filter = $.base64.decode($('#crm_channel_id option:selected').attr('data-contact_filter'));
      if (contact_filter!='') {
        parts=contact_filter.split('&');
        for (i=0;i<parts.length;i++) {
          parts2=parts[i].split('=');
          if (parts2.length==2) {
            mydata[parts2[0]]=parts2[1];
          }
        }
      }
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      need_save=true;
      $('#crm_channel_contact_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_contact_id').val('').attr('data-id','0');
      }
    }
  });
  
  $('#crm_channel_campain_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-campain.php',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      need_save=true;
      $('#crm_channel_campain_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_campain_id').val('').attr('data-id','0');
      }
    }
  });

  function crm_channel_text_change() {gks_resize_textarea($(this));}
  $('#crm_channel_text').on('change keyup paste', crm_channel_text_change);
  gks_resize_textarea($('#crm_channel_text'));



  gks_address_autocomplete('odos','arithmos','orofos','perioxi','poli','tk','nomos_id','country_id','map_latitude','map_longitude',true);

  $('#map_latitude, #map_longitude').on(mychange,function() {
    lat=parseFloat($('#map_latitude').val());
    lng=parseFloat($('#map_longitude').val());
    gks_this_map_set_pos(lat,lng);
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


var map;
var marker;
var place_map_latitude = parseFloat(jQuery('#map_latitude').val()); if (isNaN(place_map_latitude)) place_map_latitude=0; 
var place_map_longitude = parseFloat(jQuery('#map_longitude').val()); if (isNaN(place_map_longitude)) place_map_longitude=0;
var myLatLng;
var infoWindow_userpos=null;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 17,
    mapId: "gks1234567890",
  });
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title: gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  });
  
}

function handleEvent_Marker(event) {
    document.getElementById('map_latitude').value = event.latLng.lat();
    document.getElementById('map_longitude').value = event.latLng.lng();
}
 
var map_is_open=false;
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
    //place_map_latitude  = 40.6444460;
    //place_map_longitude = 22.914514;
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };      
          place_map_latitude = position.coords.latitude;
          place_map_longitude = position.coords.longitude;
          myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
          marker.position=pos;
          map.setOptions({center: pos});
          map.setOptions({zoom: 17});
          
          jQuery('#map_latitude').val(place_map_latitude);
          jQuery('#map_longitude').val(place_map_longitude);
            
          need_save=true;
          
          //console.log('2' + myLatLng);
      }, function() {
        
      });
    } 
  }      

  myLatLng = {lat: place_map_latitude, lng: place_map_longitude};

  initMap();
  marker.addListener('drag', handleEvent_Marker);
  marker.addListener('dragend', handleEvent_Marker);
  map_is_open=true;
}

window.gks_this_map_set_pos = function(lat,lng) {
  place_map_latitude=lat;
  place_map_longitude=lng;
  
  myLatLng = {lat: lat, lng: lng};
  if (typeof marker != 'undefined') marker.position=myLatLng;
  if (typeof marker != 'undefined') map.setOptions({center: myLatLng});
  //map.setOptions({zoom: 17});
}
