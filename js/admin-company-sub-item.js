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

  
  $('#date_hire').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#exit_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


  $('#company_sub_color').spectrum({
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
  
  dialog_exit_date = $( "#dialog_exit_date" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: [
    {
      text: gks_lang('OK'),
      icon: "ui-icon-circle-check",
      click: function() {
        var datasend='mymodel=gks_company_users&myid=' + dialog_exit_date.id_company_users + '&exit_date=' + $('#exit_date').val();
        
        $('body').addClass("myloading");  
        $.ajax({
      		url: '/my/admin-deleterow.php',
      		type: 'POST',
      		cache: false,
      		dataType: 'json',
      		data: datasend,
      		error : function(jqXHR ,textStatus,  errorThrown) {
      		  $("body").removeClass("myloading");
      			myalert('error:' + jqXHR.responseText);
      		},				
      		success: function(data) {
      			if (!data) {
      			  $("body").removeClass("myloading");
      				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      			} else {
      				if (data.success == true) {
      				  need_save=false;
      				  window.location.reload();
      				  
      				} else {
      				  $("body").removeClass("myloading");
      					myalert('error:' + $.base64.decode(data.message));
      				}
      			}
      		}
      	});               
      
      }
    },
    {
      text: gks_lang('Άκυρο'),
      iicon: "ui-icon-cancel",
      click: function() {
        $(this).dialog('close');
      
      }
    }    
      
        
        

    ]
  });
  
  
  $('.delete_company_user').click(function() {
    id_company_users=$(this).attr('data-id');
    dialog_exit_date.id_company_users=id_company_users;
    
	  dwidth=$(window).width() * 0.96;
	  dheight=$(window).height() * 0.96;
	  if (dwidth> 500) dwidth=500;
	  if (dheight> 500) dheight=500;
	  dialog_exit_date.dialog('option', 'width', dwidth);
	  dialog_exit_date.dialog('option', 'height', dheight);
	  $('#dialog_exit_date').parent().css({position:'fixed'});      
    dialog_exit_date.dialog('open');
  }); 
  
  $('#company_sub_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('company_sub_nomos_id',v,0);
  });  
  
  if (from_php_id==-1) {
    v=parseInt($('#company_sub_country_id').val());
    if (isNaN()) v=0;
    if (v>0) nomos_fill('company_sub_nomos_id',v,0);
  }

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    var datasend='';

    datasend+='&company_id='  + encodeURIComponent(($("#mypostform #company_id").val().trim()));
    datasend+='&company_sub_title='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_title").val().trim()));
    datasend+='&company_sub_tagline='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_tagline").val().trim()));
    datasend+='&company_sub_eponimia='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_eponimia").val().trim()));
    datasend+='&company_sub_phone='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_phone").val().trim()));
    datasend+='&company_sub_email='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_email").val().trim()));
    datasend+='&company_sub_url='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_url").val().trim()));
    datasend+='&company_sub_odos='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_odos").val().trim()));
    datasend+='&company_sub_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_arithmos").val().trim()));
    datasend+='&company_sub_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_orofos").val().trim()));
    datasend+='&company_sub_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_perioxi").val().trim()));
    datasend+='&company_sub_poli='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_poli").val().trim()));
    datasend+='&company_sub_tk='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_tk").val().trim()));
    datasend+='&company_sub_country_id='  + encodeURIComponent(($("#mypostform #company_sub_country_id").val().trim()));
    datasend+='&company_sub_nomos_id='  + encodeURIComponent(($("#mypostform #company_sub_nomos_id").val().trim()));
    datasend+='&company_sub_map_latitude='  + encodeURIComponent(($("#mypostform #company_sub_map_latitude").val().trim()));
    datasend+='&company_sub_map_longitude='  + encodeURIComponent(($("#mypostform #company_sub_map_longitude").val().trim()));
    datasend+='&company_sub_disable=' + (($('#company_sub_disable').is(':checked')) ? '0':'1');
    datasend+='&company_sub_related_user_id='  + encodeURIComponent(($("#mypostform #company_sub_related_user_id").val().trim()));
    datasend+='&company_sub_color='  + encodeURIComponent($.base64.encode($("#mypostform #company_sub_color").val().trim()));

    datasend+='&aade_send_sub=' + (($('#mypostform #aade_send_sub').is(':checked')) ? '1':'0');
    datasend+='&aade_branch_sub='  + encodeURIComponent(($("#mypostform #aade_branch_sub").val().trim()));
    datasend+='&aade_mydata_user_id_sub='  + encodeURIComponent($.base64.encode($("#mypostform #aade_mydata_user_id_sub").val().trim()));
    datasend+='&aade_mydata_subscription_key_sub='  + encodeURIComponent($.base64.encode($("#mypostform #aade_mydata_subscription_key_sub").val().trim()));
    datasend+='&aade_mydata_live_sub=' + (($('#aade_mydata_live_sub').is(':checked')) ? '1':'0');
    datasend+='&company_sub_sortorder='  + encodeURIComponent(($("#mypostform #company_sub_sortorder").val().trim()));

    datasend+='&paroxos_send=' + (($('#mypostform #paroxos_send').is(':checked')) ? '1':'0');
    datasend+='&paroxos_mydata_live=' + (($('#mypostform #paroxos_mydata_live').is(':checked')) ? '1':'0');
    datasend+='&aade_paroxos_id='  + encodeURIComponent(($("#mypostform #aade_paroxos_id").val().trim()));
    datasend+='&paroxos_branch='  + encodeURIComponent(($("#mypostform #paroxos_branch").val().trim()));
    datasend+='&pc_username='  + encodeURIComponent($.base64.encode($("#mypostform #pc_username").val().trim()));
    datasend+='&pc_password='  + encodeURIComponent($.base64.encode($("#mypostform #pc_password").val().trim()));
    datasend+='&pc_key='  + encodeURIComponent($.base64.encode($("#mypostform #pc_key").val().trim()));

          
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    
    var fpa_base=[];
    $('.gks_mybasefpa').each(function() {
      base_id=parseInt($(this).attr('data-base_id'));if (isNaN(base_id)) base_id=0;
      base_val=parseInt($(this).val());if (isNaN(base_val)) base_val=0;
      if (base_id>0 && base_val>0) {
        item={};item.base_id=base_id;item.base_val=base_val;
        fpa_base.push(item);
      }
    });
    //console.log(fpa_base);
    datasend+='&fpa_base_str=' + encodeURIComponent($.base64.encode(JSON.stringify(fpa_base)));
    
    var fpa_fiscals=[];
    $('.gks_myfpa').each(function() {
      fiscal_id=parseInt($(this).attr('data-fiscal_id'));if (isNaN(fiscal_id)) fiscal_id=0;
      base_id=parseInt($(this).attr('data-base_id'));if (isNaN(base_id)) base_id=0;
      base_val=parseInt($(this).val());if (isNaN(base_val)) base_val=0;
      if (base_id>0 && base_val>0) {
        item={};item.fiscal_id=fiscal_id;item.base_id=base_id;item.base_val=base_val;
        fpa_fiscals.push(item);
      }
    });    
    //console.log(fpa_fiscals);
    datasend+='&fpa_fiscals_str=' + encodeURIComponent($.base64.encode(JSON.stringify(fpa_fiscals)));
    

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-company-sub-item-exec.php?id=' + from_php_id,
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


  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
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
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map').parent().show();
      }
    }
    gks_myscroll();
  });
  
  
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
      
        
          
        $('#company_sub_map_latitude').val(place_map_latitude);
        $('#company_sub_map_longitude').val(place_map_longitude);
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
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#company_sub_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#company_sub_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#company_sub_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#company_sub_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#company_sub_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#company_sub_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#company_sub_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#company_sub_nomos_id").val().trim());
    
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
					  $('#company_sub_map_latitude' ).val(data.pos.lat);
					  $('#company_sub_map_longitude').val(data.pos.lng);

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
      $("#company_id").val(ui.item.id);
      
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#company").val("");
          $("#company_id").val("");
        }
    }
  });  
  
  $('#company_sub_related_user').autocomplete({
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
    delay: 300, //default
    select: function( event, ui ) {
      $("#company_sub_related_user_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#company_sub_related_user").val("");
          $("#company_sub_related_user_id").val("");
        }
    }
  });  
  
  $('#company_sub_user').autocomplete({
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
    delay: 300, //default
    select: function( event, ui ) {
      $("#company_sub_user_id").val(ui.item.id);
      datasend='&company_sub_user_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#company_sub_user").val("");
          $("#company_sub_user_id").val("");
        }
    }
  });  

  
  
  
  $('#add_company_sub_user').click(function(event) {  
    
    datasend='';
    datasend+='company_sub_id=' + from_php_id;    
    datasend+='&user_id='  + encodeURI($("#company_sub_user_id").val().trim());    
    datasend+='&date_hire='  + encodeURI($("#date_hire").val().trim());    
    datasend+='&sxolio='  + encodeURIComponent($.base64.encode($("#hire_sxolio").val().trim()));    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-company-item-user_add.php',
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
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

  $('#aade_send_sub').change(function() {
    if ($(this).is(':checked')) {
      $('#div_aade_branch_sub').show();
      $('#div_aade_aade_mydata_user_id_sub').show();
      $('#div_aade_mydata_subscription_key_sub').show();
      $('#div_aade_mydata_live_sub').show();
    
    } else {
      $('#div_aade_branch_sub').hide();
      $('#div_aade_aade_mydata_user_id_sub').hide();
      $('#div_aade_mydata_subscription_key_sub').hide();
      $('#div_aade_mydata_live_sub').hide();
    }
  });
  
  function div_row_paroxos_show_hide() {
    if ($('#paroxos_send').is(':checked')) {
      elem=$('#aade_paroxos_id option:selected');
      if (elem.length==0) {
        $('.div_row_paroxos').show();
      } else {
        var paroxos_need_username=elem.attr('data-paroxos_need_username')=='1';
        var paroxos_need_password=elem.attr('data-paroxos_need_password')=='1';
        var paroxos_need_key=elem.attr('data-paroxos_need_key')=='1';
        $('.div_row_paroxos').each(function() {
          if ($(this).hasClass('div_paroxos_need_username')) {
            if (paroxos_need_username) $(this).show(); else $(this).hide();
          } else if ($(this).hasClass('div_paroxos_need_password')) {
            if (paroxos_need_password) $(this).show(); else $(this).hide();
          } else if ($(this).hasClass('div_paroxos_need_key')) {
            if (paroxos_need_key) $(this).show(); else $(this).hide();
          } else {
            $(this).show();
          }
        });
      }
    } else {
      $('.div_row_paroxos').hide();
    }
  }
  
  $('#paroxos_send').change(function() {
    div_row_paroxos_show_hide();
  });
  $('#aade_paroxos_id').change(function() {
    div_row_paroxos_show_hide();
  });
    
  gks_address_autocomplete('company_sub_odos','company_sub_arithmos','company_sub_orofos','company_sub_perioxi','company_sub_poli','company_sub_tk','company_sub_nomos_id','company_sub_country_id','company_sub_map_latitude','company_sub_map_longitude',true);

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
  $('#company_sub_map_latitude, #company_sub_map_longitude').on(mychange,function() {
    lat=parseFloat($('#company_sub_map_latitude').val());
    lng=parseFloat($('#company_sub_map_longitude').val());
    gks_this_map_set_pos(lat,lng);
  });


  $('#button_paroxos_check').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;}   

    datasend='company_sub_id=' + from_php_id;
    $('body').addClass("myloading");  
    $.ajax({
  		url: '/my/admin-company-sub-item-paroxos-check.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		data: datasend,
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
  				if (data.success == true) {
  				  myalert('ok:' + $.base64.decode(data.message));
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  				}
  			}
  		}
  	});
  	    
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
    document.getElementById('company_sub_map_latitude').value = event.latLng.lat();
    document.getElementById('company_sub_map_longitude').value = event.latLng.lng();
}
 
 
var map_is_open=false; 
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
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

          jQuery('#company_sub_map_latitude').val(place_map_latitude);
          jQuery('#company_sub_map_longitude').val(place_map_longitude);
          
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
