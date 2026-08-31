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


  function mysubmit() {
    
    datasend='';
    datasend+='&company_id=' + encodeURIComponent($("#mypostform #company_id").val().trim());
    datasend+='&company_sub_id=' + encodeURIComponent($("#mypostform #company_sub_id").val().trim());
    //hr_program_is_ergasia
    datasend+='&hr_program_date=' + encodeURIComponent($("#mypostform #hr_program_date").val().trim());
    datasend+='&hr_program_status_id=' + $('.hr_program_status_selected').attr('data-id');
    datasend+='&hr_program_vardia_id=' + $('.hr_program_vardia_selected').attr('data-id');
    datasend+='&hr_program_date_from=' + encodeURIComponent($("#mypostform #hr_program_date_from").val().trim());
    datasend+='&hr_program_date_to=' + encodeURIComponent($("#mypostform #hr_program_date_to").val().trim());
    datasend+='&hr_program_color='  + encodeURIComponent($.base64.encode($("#mypostform #hr_program_color").val().trim()));
    datasend+='&hr_program_name='  +  encodeURIComponent($.base64.encode($("#mypostform #hr_program_name").val().trim()));
    datasend+='&hr_program_descr='  + encodeURIComponent($.base64.encode(tinyMCE.get('hr_program_descr').getContent()));
    datasend+='&hr_program_user_id='  +  $("#mypostform #hr_program_user_id").val().trim();
    datasend+='&hr_program_posto_id='  +  $("#mypostform #hr_program_posto_id").val().trim();
    datasend+='&internal_note='  + encodeURIComponent($.base64.encode($("#mypostform #internal_note").val().trim()));
    datasend+='&assigned_id='  + $("#mypostform #assigned_id").attr('data-id');

    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hr-program-item-exec.php?id=' + from_php_id,
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
  
  function hr_program_descr_change() {gks_resize_textarea($(this));}
  $('#hr_program_descr').on('change keyup paste', hr_program_descr_change);
  gks_resize_textarea($('#hr_program_descr'));

  
  
  

  $('#hr_program_user').autocomplete({
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
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      old_val=$("#hr_program_user_id").val();
      $("#hr_program_user_id").val(ui.item.id);
      $('#autocomplete_hr_program_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_hr_program_user_id').show();
      
      gks_admin_get_user_data(ui.item.id, false);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
      	
      	
        $("#hr_program_user").val('');
        $("#hr_program_user_id").val('');
        $('#autocomplete_hr_program_user_id').hide(); 
        $('#div_pelati_sxolio').hide('fade', 'slow');
        $('#text_pelati_sxolio').html('');
                        
        $('#div_order_sxolio').hide('fade', 'slow');
        $('#text_order_sxolio').html('');   
           
        $('#dr_user_first_name').html('');
        $('#dr_user_last_name').html('');
        $('#dr_user_email').html('');
        $('#dr_user_mobile').html('');
        //$('#phone').val('');
        //$('#web').val('');
        $('#dr_user_lang').html('').attr('data-val','');
        
        $('#dr_user_eponimia').html('');
        $('#dr_user_title').html('');
        $('#dr_user_afm_ee_initial_static').html('');
        $('#dr_user_afm').html('');
        $('#dr_user_afm_views_run_static').html('');
        $('#dr_user_doy').html('');
        $('#dr_user_epaggelma').html('');
        
        $('#users_extra_address_id option').each(function() { 
          $(this).remove();
        }); 
			  $('#users_extra_address_id').append('<option value="-1">'+gks_lang('Βασική διεύθυνση')+'</option>');        
        
			  $('#dr_ea_').hide();
        $('#dr_ea_name').html('');
        $('#dr_ea_phone').html('');
        $('#dr_user_ma_odos').html('');
        $('#dr_user_ma_arithmos').html('');
        $('#dr_user_ma_orofos').html('');
        $('#dr_user_ma_perioxi').html('');
        $('#dr_user_ma_poli').html('');
        $('#dr_user_ma_tk').html('');
        $('#dr_user_ma_country_id').html('');
        $('#dr_user_ma_nomos_id').html('');

        
        gks_myscroll(); 
      }
    }
  });    

  $('#hr_program_posto').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-posto.php',
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
    select: function( event, ui ) {
      need_save=true;
      old_val=$("#hr_program_posto_id").val();
      $("#hr_program_posto_id").val(ui.item.id);
      $('#autocomplete_hr_program_posto_id').attr('href', 'admin-production-posta-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_hr_program_posto_id').show();
      
      //gks_admin_get_posto_data(ui.item.id, false);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#hr_program_posto").val('');
        $("#hr_program_posto_id").val('');
        $('#autocomplete_hr_program_posto_id').hide(); 
        gks_myscroll(); 
      }
    }
  });



  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    

      
    datasend='cmd=get&id=' + user_id + '&hr_program_id=' + from_php_id;
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
            }
            
            $('#dr_user_first_name').html(data.first_name);
            $('#dr_user_last_name').html(data.last_name);
            $('#dr_user_email').html(data.user_comms_email);
            $('#dr_user_mobile').html(data.user_comms_phone);
            $('#dr_user_mobile .gks_voip_originate_after_span').click(gks_voip_originate_click);
            //$('#phone').html(data.phone_home);
            //$('#web').html(data.user_url);
            
            $('#dr_user_lang').html(data.lang_name).attr('data-val',data.lang);
            
            $('#dr_user_eponimia').html(data.eponimia);
            $('#dr_user_title').html(data.title);
            if (data.country_ee=='') $('#dr_user_afm_ee_initial_static').hide(); else $('#dr_user_afm_ee_initial_static').show();
            $('#dr_user_afm_ee_initial_static').html(data.country_ee);
            $('#dr_user_afm').html(data.afm);
            $('#dr_user_afm_views_run_static').html('');
            $('#dr_user_doy').html(data.doy);
            $('#dr_user_epaggelma').html(data.epaggelma);

            $('#users_extra_address_id option').each(function() { 
              $(this).remove();
            }); 
            for (i = 0; i < data.extra_address.length; i++) {
  				    $('#users_extra_address_id').append('<option value="' + data.extra_address[i].id + '">' + data.extra_address[i].descr + '</option>');
  				  }
    			  $('#dr_ea_').hide();
            $('#dr_ea_name').html('');
            $('#dr_ea_phone').html('');
            $('#dr_user_ma_odos').html(data.ma_odos);
            $('#dr_user_ma_arithmos').html(data.ma_arithmos);
            $('#dr_user_ma_orofos').html(data.ma_orofos);
            $('#dr_user_ma_perioxi').html(data.ma_perioxi);
            $('#dr_user_ma_poli').html(data.ma_poli);
            $('#dr_user_ma_tk').html(data.ma_tk);
            $('#dr_user_ma_country_id').html(data.country_name);
            $('#dr_user_ma_nomos_id').html(data.nomos_descr);
              				  
            //calc_pliroteo();
            gks_myscroll();
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
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


  $('#hr_program_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));

  $('.hr_program_status_this').click(function() {
    if (from_php_perm_ret_edit==false) return;
    $('.hr_program_status_this').each(function() {
      $(this).removeClass('hr_program_status_selected');
    });
    $(this).addClass('hr_program_status_selected');
    need_save=true;
  });
  $('.hr_program_vardia_this').click(function() {
    if (from_php_perm_ret_edit==false) return;
    $('.hr_program_vardia_this').each(function() {
      $(this).removeClass('hr_program_vardia_selected');
    });
    $(this).addClass('hr_program_vardia_selected');
    is_ergasia=parseInt($(this).attr('data-is_ergasia'));if (isNaN(is_ergasia)) is_ergasia=-1;
    time_start=$(this).attr('data-time_start');
    time_end=$(this).attr('data-time_end');
    console.log(is_ergasia,time_start,time_end);
    if (is_ergasia==1) {
      $('#hr_program_posto_div').show();
    } else {
      $('#hr_program_posto_div').hide();
    }
    date_from=$('#hr_program_date_from').datetimepicker('getValue');
    date_to  =$('#hr_program_date_to').datetimepicker('getValue');
    date_from_new=new Date(date_from.getFullYear()+'-'+(date_from.getMonth()+1)+'-'+date_from.getDate()+' '+time_start+':00');
    //if (is_ergasia==1 || 1==1) {
      if (time_end>time_start) {
        date_to_new  =new Date(date_from.getFullYear()+'-'+(date_from.getMonth()+1)+'-'+date_from.getDate()+' '+time_end+':00');
      } else {
        date_to_new  =new Date(date_from.getFullYear()+'-'+(date_from.getMonth()+1)+'-'+(date_from.getDate()+1)+' '+time_end+':00');
      }
    /* } else {
      if (time_end>time_start) {
        date_to_new  =new Date(date_to.getFullYear()+'-'+(date_to.getMonth()+1)+'-'+date_to.getDate()+' '+time_end+':00');
      } else {
        date_to_new  =new Date(date_to.getFullYear()+'-'+(date_to.getMonth()+1)+'-'+(date_to.getDate()+1)+' '+time_end+':00');
      }
    } */
    
    $('#hr_program_date_from').datetimepicker('setOptions', {value:date_from_new});
    $('#hr_program_date_to').datetimepicker('setOptions', {value:date_to_new});

    can_change_duration=false;
    difsecs =Math.round((date_to_new.getTime() - date_from_new.getTime())/1000);
    difmins=Math.round(difsecs/60);
    difhour=Math.floor(difmins/60);
    difmins=difmins-difhour*60;
    if (difhour<0) {
      $('#hr_program_date_duration_days').val('');
      $('#hr_program_date_duration ').val('');
      $('#hr_program_date_duration').TimePicker('setValue','');
    } else {
      days=Math.floor(difhour/24);
      difhour=difhour-(days*24);
      if (difhour<=9) vval='0' + difhour+':'; else vval=difhour+':';
      if (difmins<=9) vval+='0' + difmins; else vval+=difmins+'';
      $('#hr_program_date_duration_days').val(days);
      $('#hr_program_date_duration').val(vval);
      $('#hr_program_date_duration').TimePicker('setValue',vval);
    }
    can_change_duration=true;  
    need_save=true;
  });
  
  $('#hr_program_date_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,step:30,onChangeDateTime:
    function(ct,$i){
      need_save=true;
      $('#hr_program_date_to').datetimepicker('setOptions',{minDate:$('#hr_program_date_from').datetimepicker('getValue')});
      date_from=$('#hr_program_date_from').datetimepicker('getValue');
      tdays=parseInt($('#hr_program_date_duration_days').val()); if (isNaN(tdays)) tdays=0;
      tval=$('#hr_program_date_duration').TimePicker('getValue');
      if (tval.length==5) {
        thour=parseInt(tval.substr(0,2)); if (isNaN(thour)) return;  
        tminu=parseInt(tval.substr(3,2)); if (isNaN(tminu)) return; 
        tval=thour*60+ tminu;
        date_from=$('#hr_program_date_from').datetimepicker('getValue');
        dto = new Date(date_from);
        dto.setMinutes(dto.getMinutes() + tval+tdays*(24*60));
        $('#hr_program_date_to').datetimepicker('setOptions', {value:dto});
      } else {
        date_to  =$('#hr_program_date_to').datetimepicker('getValue');
        if (date_from>date_to) {
          $('#hr_program_date_to').datetimepicker('setOptions', {value:date_from});
        }        
      }
    }
  }));
  $('#hr_program_date_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,step:30,onChangeDateTime:
    function(ct,$i){
      need_save=true;
      date_from=$('#hr_program_date_from').datetimepicker('getValue');
      if (date_from == null) return;
      date_to  =$('#hr_program_date_to').datetimepicker('getValue');
      if (date_to == null) return;
      difsecs =Math.round((date_to.getTime() - date_from.getTime())/1000);
      difmins=Math.round(difsecs/60);
      difhour=Math.floor(difmins/60);
      difmins=difmins-difhour*60;
      if (difhour<0) {
        $('#hr_program_date_duration_days').val('');
        $('#hr_program_date_duration').val('');
        $('#hr_program_date_duration').TimePicker('setValue','');
      } else {
        days=Math.floor(difhour/24);
        difhour=difhour-(days*24);        
        if (difhour<=9) vval='0' + difhour+':'; else vval=difhour+':';
        if (difmins<=9) vval+='0' + difmins; else vval+=difmins+'';
        $('#hr_program_date_duration_days').val(days);
        $('#hr_program_date_duration').val(vval);
        $('#hr_program_date_duration').TimePicker('setValue',vval);
      }
    }
  }));
  
  function hr_program_date_duration_change() {
    if (can_change_duration==false) return;
    need_save=true;
    tdays=parseInt($('#hr_program_date_duration_days').val()); if (isNaN(tdays)) tdays=0;
    tval=$('#hr_program_date_duration').TimePicker('getValue');
    if (tval.length==5) {
      thour=parseInt(tval.substr(0,2)); if (isNaN(thour)) return;  
      tminu=parseInt(tval.substr(3,2)); if (isNaN(tminu)) return; 
      tval=thour*60+ tminu;
      date_from=$('#hr_program_date_from').datetimepicker('getValue');
      dto = new Date(date_from);
      dto.setMinutes(dto.getMinutes() + tval+tdays*(24*60));
      $('#hr_program_date_to').datetimepicker('setOptions', {value:dto});
    }
  }
  
  var can_change_duration=true;
  $('#hr_program_date_duration').TimePickerAlone({mask:'29:59',dragAndDrop:true,mouseWheel:true,twelveHoursFormat:false,seconds:false,ampm:false,saveOnChange:true,defaultTime:'',inputFormat:'HH:mm',onChange:
    function(ct,$i){
      hr_program_date_duration_change();
    }
  });
  $('#hr_program_date_duration_days').on(mychange, function() {
    hr_program_date_duration_change();
  }) 

  function internal_note_change() {gks_resize_textarea($(this));}
  $('#internal_note').on('change keyup paste', internal_note_change);
  gks_resize_textarea($('#internal_note'));


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
    select: function( event, ui ) {
      $('#assigned_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#assigned_id').val('').attr('data-id','0');
        }
    }
  });


  $('#hr_program_color').spectrum({
    type: "component",
    locale:from_php_gks_spectrum_locale,
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
    change: function(color) {need_save=true;}
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

