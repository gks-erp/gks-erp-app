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

    datasend+='&crm_machine_name='  +  encodeURIComponent($.base64.encode($("#mypostform #crm_machine_name").val().trim()));
    //datasend+='&crm_machine_descr='  +  encodeURIComponent($.base64.encode($("#mypostform #crm_machine_descr").val().trim()));
    datasend+='&crm_machine_descr='  + encodeURIComponent($.base64.encode(tinyMCE.get('crm_machine_descr').getContent()));
    datasend+='&crm_machine_serial_number='  +  encodeURIComponent($.base64.encode($("#mypostform #crm_machine_serial_number").val().trim()));
    datasend+='&crm_machine_product_id='  +  $("#mypostform #crm_machine_product_id").val().trim();
    datasend+='&crm_machine_brand_id='  +  $("#mypostform #crm_machine_brand_id").val().trim();
    datasend+='&crm_machine_user_id='  +  $("#mypostform #crm_machine_user_id").val().trim();
    datasend+='&users_extra_address_id='  +  $("#mypostform #users_extra_address_id").val().trim();
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crm-machine-item-exec.php?id=' + from_php_id,
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
  
  $('#crm_machine_product').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode: 'simple',
        //and_variable: 1,
        base_types:[0,1],
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
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
      $("#crm_machine_product_id").val(ui.item.id);
      need_save=true;
      
      datasend='cmd=get&id=' + ui.item.id + '&aa=1&sheets=0&quantity=1&user_id=0&anddescr=0&mydate=';
      //console.log(datasend);
      
      $.ajax({
  			url: 'admin-get-product-data.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  			  need_save=true;
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
  					  //console.log(data);
  					  $('#crm_machine_brand').val(data.brand_descr);
  					  $('#crm_machine_brand_id').val(data.brand_id);
  					} else {
  					  myalert('error:' + $.base64.decode(data.message));
  					}
  					  
  				}
  			}
  		});
		
		      
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#crm_machine_product").val("");
        $("#crm_machine_product_id").val("");
      }
      need_save=true;
    }
 
  });  
  
  
  $('#crm_machine_brand').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-brands.php',
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
      $("#crm_machine_brand_id").val(ui.item.id);
      need_save=true;
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#crm_machine_brand").val("");
        $("#crm_machine_brand_id").val("");
      }
      need_save=true;
    }
  });  

  function crm_machine_descr_change() {gks_resize_textarea($(this));}
  $('#crm_machine_descr').on('change keyup paste', crm_machine_descr_change);
  gks_resize_textarea($('#crm_machine_descr'));

  
  
  
  
  
  
  
  
  
  
  
  
  



  $('#users_extra_address_id').change(function() {
    v=$(this).val();
    extra_address_select(v);
    gks_myscroll();
    //calc_pliroteo();
  });

  function extra_address_select(v) {
    
    
    if (v ==-1) { //base address
      $('#dr_ea_').slideUp();
    } else {
      $('#dr_ea_').slideDown();
    }
    if (v==0) { //new address
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
      //$('#dr_user_ma_map_latitude').html('');
      //$('#dr_user_ma_map_longitude').html('');
      //map_set_point();
    } else {
      mydata = 'aid=' + v + '&user_id=' + $('#crm_machine_user_id').val();
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

                $('#dr_ea_name').html(data.data.ea_name);
                $('#dr_ea_phone').html(data.data.ea_phone);
                $('#dr_user_ma_odos').html(data.data.ea_odos);
                $('#dr_user_ma_arithmos').html(data.data.ea_arithmos);
                $('#dr_user_ma_orofos').html(data.data.ea_orofos);
                $('#dr_user_ma_perioxi').html(data.data.ea_perioxi);
                $('#dr_user_ma_poli').html(data.data.ea_poli);
                $('#dr_user_ma_tk').html(data.data.ea_tk);
                $('#dr_user_ma_country_id').html(data.data.ea_country_name);
                $('#dr_user_ma_nomos_id').html(data.data.ea_nomos_descr);
                //$('#map_latitude').val(data.data.ea_latitude);
                //$('#map_longitude').val(data.data.ea_longitude);
                //map_set_point();
              } else {
                myalert('error:' + $.base64.decode(data.message));
              }
            }
          }
      });
    }
  }


  $('#crm_machine_user').autocomplete({
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
    select: function( event, ui ) {
      need_save=true;
      old_val=$("#crm_machine_user_id").val();
      $("#crm_machine_user_id").val(ui.item.id);
      $('#autocomplete_crm_machine_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_crm_machine_user_id').show();
      
      gks_admin_get_user_data(ui.item.id, false);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
      	check_vies_valid_wait_timer_stop();
      	
        $("#crm_machine_user").val('');
        $("#crm_machine_user_id").val('');
        $('#autocomplete_crm_machine_user_id').hide(); 
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

        //$('#map_latitude').val('');
        //$('#map_longitude').val('');
        //map_set_point();
        //$('#fiscal_position_id').val(1);
        //$('#pricelist_id').val(1);
        //$('#birthday').val('');
        
        gks_myscroll(); 
      }
    }
  });    





  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
    check_vies_valid_wait_timer_stop();
      
    datasend='cmd=get&id=' + user_id + '&machine_id=' + from_php_id;
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

