/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var gks_dialog_message;
var gks_dialog_confirm;


jQuery3(document).ready(function($) {
  
  $.base64.utf8encode = true;
  $.base64.utf8decode = true;
  $.datetimepicker.setLocale(from_php_gks_datetimepicker_locale);

  $('.tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  
  function gks_formatMoney(n, c, d, t){
    
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
  }

  function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
  }

  function gks_myresize() {
    var gks_content_width=$(window).width();
   //var gks_content_width = $('#gks_content').width();
    if (gks_content_width>=768) {
      $('#gks_rsrv_s').css('width','70%').css('float','left');
      $('#gks_rsrv_r').css('width','calc(30% - 30px)').css('float','left');
      //$('.gks_label_search').each(function() {
      //  $(this).css('text-align','left');  
      //});
      $('.gks_checkout_col1').each(function() {
        $(this).css('width','50%');  
      });
      $('.gks_checkout_col2').each(function() {
        $(this).css('width','50%');  
      });
      $('.gks_col4').each(function() {
        $(this).css('width','calc(33.33%)').css('float','left');
      });
      $('.gks_left_center').each(function() {
        $(this).css('text-align','left');
      });
      $('.gks_right_center').each(function() {
        $(this).css('text-align','right');
      });
      
    } else {
      $('#gks_rsrv_s').css('width','calc(100% - 20px)').css('float','none');
      $('#gks_rsrv_r').css('width','calc(100% - 20px)').css('float','none');
      //$('.gks_label_search').each(function() {
      //  $(this).css('text-align','center');  
      //});
      $('.gks_checkout_col1').each(function() {
        $(this).css('width','100%');  
      });
      $('.gks_checkout_col2').each(function() {
        $(this).css('width','100%');  
      });
      $('.gks_col4').each(function() {
        $(this).css('width','calc(100%)').css('float','none');
      });
      $('.gks_left_center').each(function() {
        $(this).css('text-align','center');
      });
      $('.gks_right_center').each(function() {
        $(this).css('text-align','center');
      });      
    }
    
    gks_myscroll();
  }
  function gks_myscroll() {
    var gks_content_width=$(window).width();
    if (gks_content_width<=992) {
      $('#gks_rsrv_f').css('top',0);
      $('#gks_rsrv_f').removeClass('gks_rsrv_fs');
    } else {
      mytoppos = $('#gks_rsrv_f_pos').offset().top;
      window_he= $(window).height();
      myscroll = $(window).scrollTop();
      
      gks_rsrv_f_height = $('#gks_rsrv_f').height();
      //console.log(gks_rsrv_f_height);
      gks_rsrv_f_height+= 2*24 + 0; // + 10 + 4;// + 10; //apo to paddding + 10 gia safe/shadow
      
      newtop = - mytoppos + window_he - gks_rsrv_f_height + myscroll;
      extrah=0;
      diafora = mytoppos + newtop;
      if (diafora<700) {
        extrah= 700-diafora
        newtop+=extrah;
      }
      if (newtop>0) {
        newtop=0;
        $('#gks_rsrv_f').removeClass('gks_rsrv_fs');
      } else {
        $('#gks_rsrv_f').addClass('gks_rsrv_fs');
      }
      $('#gks_rsrv_f').css('top',newtop);
      
      //console.log(mytoppos + ' ' + window_he + ' ' + myscroll + ' ' + newtop + ' ' + diafora);
    }

  }  
  
  gks_myresize();
  $( window ).resize(function() {
    gks_myresize();
  });

  $(window).scroll(function() { 
    gks_myscroll();
  });
  
  
  gks_dialog_message = $('#gks_dialog_message').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "OK" : function() {
        $(this).dialog( "close" );
      }
    }
  });  
  function gks_myalert(mymessage) {
    $("#gks_dialog_message_ok").hide();
    $("#gks_dialog_message_error").hide();
    if (mymessage.substring(0, 6) == 'error:') {
       $("#gks_dialog_message_error").show();
       mymessage=mymessage.substring(6);
    }
    if (mymessage.substring(0, 3) == 'ok:') {
       $("#gks_dialog_message_ok").show();
       mymessage=mymessage.substring(3);
    }
    $("#gks_dialog_message_message").html(mymessage);
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 450) dwidth=450;
    if (dheight> 330) dheight=330;
    gks_dialog_message.dialog('option', 'width', dwidth);
    gks_dialog_message.dialog('option', 'height', dheight);
    $('#gks_dialog_message').parent().css({position:'fixed'});      
    gks_dialog_message.dialog('open');
  }; 

  
  gks_dialog_confirm = $( "#gks_dialog_confirm" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: {
      "from_php_lang_OK": {
        text:from_php_lang_OK,
        click: function() {
          $(this).dialog('close');
          
          switch (gks_dialog_confirm.function_ok) {
            default:
              myalert('error: dialog_confirm function_ok');
              break;
          }
        }
      },
      "from_php_lang_Cancel" : {
        text: from_php_lang_Cancel,
        click: function() {
          $(this).dialog('close');
        }
      },
    }
  });

  function gks_myconfirm(mymessage, function_ok,param1,param2,param3) {
    $("#gks_dialog_confirm_message").html(mymessage);
    gks_dialog_confirm.function_ok = function_ok;
    gks_dialog_confirm.param1 = param1;
    gks_dialog_confirm.param2 = param2;
    gks_dialog_confirm.param3 = param3;
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 500) dwidth=500;
    if (dheight> 500) dheight=500;
    gks_dialog_confirm.dialog('option', 'width', dwidth);
    gks_dialog_confirm.dialog('option', 'height', dheight);
    $('#gks_dialog_confirm').parent().css({position:'fixed'});      
    gks_dialog_confirm.dialog('open');
  }; 
  

  
  function nomos_fill(myelement,v,nomos_id) {  
    mydata = '&command=get_nomoi&id=' + v;
    $('#' + myelement + ' option').each(function() {
      if (parseInt($(this).attr('value')) >0 ) {
        $(this).remove();
      }
    });
    
    myreload = false;
    $.ajax({
        url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
        type: 'POST',
        cache: false,
        dataType: "json",
        data:mydata,
        myelement:myelement,
        nomos_id:nomos_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
				  myalert('error:' + jqXHR.responseText);
			  },
        success: function(data) {
          if (!data) {
  					gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  				} else {
  				  if (typeof data.myreload !== 'undefined') myreload=data.myreload;
            if (data.success == true) {
              data=data.data;
              
              if (data.out) {
    				    for (var i = 0; i < data.out.length; i++) {
    				      $('#' + this.myelement).append('<option value="' + data.out[i].id + '">' + data.out[i].descr + '</option>');
    				    }
    				    
    				    $('#' + this.myelement).val(this.nomos_id);
    				  }
    				  //if (myreload) window.location.reload();
    				  checkout_update(false,false,false);
            } else {
              gks_myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
    
  } 
  
  $('#dr_user_afm').on('input keyup paste', function() {
    $('#dr_user_afm_views_run').hide();
  });
  
  
  
  $('#dr_user_ma_country_id').change(function() {
    var v=$(this).val();

    data_ee=$(this).find('OPTION[value=' + v + ']').attr('data-ee');
    //console.log(data_ee);
    $('#dr_user_afm_views_run').hide();
    if (data_ee=='') {
      $('#dr_user_afm_ee_initials').hide().html('');
      $('#dr_user_afm').css('width','100%').removeClass('dr_user_afm_views');
    } else {
      $('#dr_user_afm_ee_initials').show().html(data_ee);
      $('#dr_user_afm').css('width','calc(100% - 75px)').addClass('dr_user_afm_views');
    }
    nomos_fill('dr_user_ma_nomos_id',v,0);
  });
  $('#other_dr_user_ma_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('other_dr_user_ma_nomos_id',v,0);
  });
  
  $('input[name=form_parastatiko]').click(function() {
    if ($(this).val() ==0) {
      $('#div_parastatiko_timologio').hide();  
    } else {
      $('#div_parastatiko_timologio').show();
    }
    if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
    gks_myscroll();
    checkout_update(false,false,false);
  });
  $('input[name=form_reservation_other]').click(function() {
    if ($(this).val() ==0) {
      $('#div_reservation_other').hide();  
    } else {
      $('#div_reservation_other').show();
    }
    if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
    gks_myscroll();
    checkout_update(false,false,false);
  });
   
  
  
   
  function checkout_update(showloading, showerrors,gonext) {
    datasend='';
    datasend+='&showloading='  + encodeURIComponent((showloading ? '1' : '0'));
    datasend+='&showerrors='  + encodeURIComponent((showerrors ? '1' : '0'));
    datasend+='&gonext='  + encodeURIComponent((gonext ? '1' : '0'));
    datasend+='&dr_user_first_name='  + encodeURIComponent($("#dr_user_first_name").val().trim());
    datasend+='&dr_user_last_name='  + encodeURIComponent($("#dr_user_last_name").val().trim());
    datasend+='&dr_user_email='  + encodeURIComponent($("#dr_user_email").val().trim());
    datasend+='&dr_user_mobile='  + encodeURIComponent($("#dr_user_mobile").val().trim());
    datasend+='&dr_user_lang='  + encodeURIComponent($("#dr_user_lang").val().trim());
    datasend+='&dr_user_ma_odos='  + encodeURIComponent($("#dr_user_ma_odos").val().trim());
    datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($("#dr_user_ma_arithmos").val().trim());
    datasend+='&dr_user_ma_orofos='  + encodeURIComponent($("#dr_user_ma_orofos").val().trim());
    datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($("#dr_user_ma_perioxi").val().trim());
    datasend+='&dr_user_ma_poli='  + encodeURIComponent($("#dr_user_ma_poli").val().trim());
    datasend+='&dr_user_ma_tk='  + encodeURIComponent($("#dr_user_ma_tk").val().trim());
    datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#dr_user_ma_country_id").val().trim());
    datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#dr_user_ma_nomos_id").val().trim());
    
    datasend+='&form_parastatiko=' +      encodeURI($('input[name=form_parastatiko]:checked').val());
    datasend+='&dr_user_eponimia='  + encodeURIComponent($("#dr_user_eponimia").val().trim());
    datasend+='&dr_user_title='  + encodeURIComponent($("#dr_user_title").val().trim());
    datasend+='&dr_user_afm='  + encodeURIComponent($("#dr_user_afm").val().trim());
    datasend+='&dr_user_doy='  + encodeURIComponent($("#dr_user_doy").val().trim());
    datasend+='&dr_user_epaggelma='  + encodeURIComponent($("#dr_user_epaggelma").val().trim());

    datasend+='&form_reservation_other=' +      encodeURI($('input[name=form_reservation_other]:checked').val());
    datasend+='&other_dr_user_first_name='  + encodeURIComponent($("#other_dr_user_first_name").val().trim());
    datasend+='&other_dr_user_last_name='  + encodeURIComponent($("#other_dr_user_last_name").val().trim());
    datasend+='&other_dr_user_email='  + encodeURIComponent($("#other_dr_user_email").val().trim());
    datasend+='&other_dr_user_mobile='  + encodeURIComponent($("#other_dr_user_mobile").val().trim());
    datasend+='&other_dr_user_lang='  + encodeURIComponent($("#other_dr_user_lang").val().trim());
    datasend+='&other_dr_user_ma_odos='  + encodeURIComponent($("#other_dr_user_ma_odos").val().trim());
    datasend+='&other_dr_user_ma_arithmos='  + encodeURIComponent($("#other_dr_user_ma_arithmos").val().trim());
    datasend+='&other_dr_user_ma_orofos='  + encodeURIComponent($("#other_dr_user_ma_orofos").val().trim());
    datasend+='&other_dr_user_ma_perioxi='  + encodeURIComponent($("#other_dr_user_ma_perioxi").val().trim());
    datasend+='&other_dr_user_ma_poli='  + encodeURIComponent($("#other_dr_user_ma_poli").val().trim());
    datasend+='&other_dr_user_ma_tk='  + encodeURIComponent($("#other_dr_user_ma_tk").val().trim());
    datasend+='&other_dr_user_ma_country_id='  + encodeURIComponent($("#other_dr_user_ma_country_id").val().trim());
    datasend+='&other_dr_user_ma_nomos_id='  + encodeURIComponent($("#other_dr_user_ma_nomos_id").val().trim());



    datasend+='&form_select_apostoli=' +  encodeURIComponent($('#form_select_apostoli').val().trim());
    datasend+='&form_ea_name=' +          encodeURIComponent($('#form_ea_name').val().trim());
    datasend+='&form_ea_phone=' +         encodeURIComponent($('#form_ea_phone').val().trim());
    datasend+='&form_ea_odos=' +          encodeURIComponent($('#form_ea_odos').val().trim());
    datasend+='&form_ea_arithmos=' +       encodeURIComponent($('#form_ea_arithmos').val().trim());
    datasend+='&form_ea_orofos=' +       encodeURIComponent($('#form_ea_orofos').val().trim());
    datasend+='&form_ea_perioxi=' +       encodeURIComponent($('#form_ea_perioxi').val().trim());
    datasend+='&form_ea_poli=' +          encodeURIComponent($('#form_ea_poli').val().trim());
    datasend+='&form_ea_tk=' +            encodeURIComponent($('#form_ea_tk').val().trim());


    if ($('#form_ea_country_id').val()==null)$('#form_ea_country_id').val(0);
    if ($('#form_ea_nomos_id').val()==null)$('#form_ea_nomos_id').val(0);
    datasend+='&form_ea_country_id=' +    encodeURIComponent($('#form_ea_country_id').val().trim());
    datasend+='&form_ea_nomos_id=' +      encodeURIComponent($('#form_ea_nomos_id').val().trim());


    //console.log(datasend);
    if (gonext || showloading) $('body').addClass("gks_myloading"); 
    $('#gks_loading_roll').show(); 
    $('#gks_update').hide();

    datasend+='&command=checkout_edit';
      
    $.ajax({
			url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
			type: 'POST',
			gks_showerrors: showerrors,
			gks_gonext: gonext,
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$("body").removeClass("gks_myloading"); 
				$('#gks_loading_roll').hide(); 
				$('#gks_update').show();
				//console.log('error:' + jqXHR.responseText);
			  if (this.gks_showerrors) gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $("body").removeClass("gks_myloading");
				if (!(data && data.success && this.gks_gonext)) $("body").removeClass("gks_myloading");
				$('#gks_loading_roll').hide(); 
				$('#gks_update').show();
				//console.log(data);
				if (!data) {
					if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
				} else {
  				data=data.data;
          
          products_total_val=0;
          if (data.products_netvalue) {
            products_total_val = data.products_total_val;
            
  				  $('#basket_products_netvalue').html($.base64.decode(data.products_netvalue));
  				  $('#basket_products_fpa').html($.base64.decode(data.products_fpa));
  				  $('#basket_kostos_apostolis').html($.base64.decode(data.kostos_apostolis));
  				  $('#basket_kostos_pliromis').html($.base64.decode(data.kostos_pliromis));
  				  $('#basket_products_total').html($.base64.decode(data.products_total));
          }
          
				  if (data.out) {
            //console.log(data.out);
				    for (var i = 0; i < data.out.length; i++) {
				      if (data.out[i].type=='val') {
				        $(data.out[i].id).val($.base64.decode(data.out[i].data));
				      } else if (data.out[i].type=='html') {
				        $(data.out[i].id).html($.base64.decode(data.out[i].data));
				      }
				    }
				  }
				  if (data.ids_hide) {
				    for (var i = 0; i < data.ids_hide.length; i++) {
			        $(data.ids_hide[i]).hide();
				    }
				  }
				  if (data.ids_show) {
				    for (var i = 0; i < data.ids_show.length; i++) {
			        $(data.ids_show[i]).show();
				    }
				  }  				  
				  

          if (data.views_run_img!='') {
            $('#dr_user_afm_views_run').html(data.views_run_img).show();
            $('#dr_user_afm_views_run .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
          }
          
          if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
          gks_myscroll(); 
            
          if (data.success == false){
  					if (data.message.length > 0){
  						if (this.gks_showerrors) gks_myalert('error:' + $.base64.decode(data.message));
  					} else {
  					  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  					}
  					//return;
  				} else {
  				  if (this.gks_gonext) {
  				    $('body').removeClass('gks_myloading'); 
  				    window.location.href = from_php_gks_api_page_payment + from_php_gks_set_lang_url;
  				  }
          } 
  								  
					
				}
			}
			
		});     
    return false; 
  }

  $('.gks_input_select').change(function() {
    checkout_update(false,false,false);
  });
  $('.gks_input_text').change(function() {
    checkout_update(false,false,false);
  });
    
  $('#gks_basket').click(function() {
    $('body').removeClass('gks_myloading'); 
    window.location.href = from_php_gks_api_hotel_page_reservation_basket + from_php_gks_set_lang_url;
  });
  $('#gks_update').click(function(event){
    checkout_update(true,true,false);
  }); 
    
  $('#gks_payment').click(function() {
    checkout_update(true,true,true);
  });
    
  $('#form_ea_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('form_ea_nomos_id',v,0);
  });
  



    
  $('#form_select_apostoli').change(function() {
    var v=$(this).val();
    extra_address_select(v);
    if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
    gks_myscroll();
  });    
  
  function extra_address_select(v) {
    
    if (v ==-1) {
      $('#div_extra_address').hide();  
      return;
    } else {
      $('#div_extra_address').show();
    }
    
    //console.log(v);
    //console.log(extra_address[v]);
    if (v==0) {
//      $('#form_ea_name').removeClass('mydisabled');
//      $('#form_ea_phone').removeClass('mydisabled');
//      $('#form_ea_odos').removeClass('mydisabled');
//      $('#form_ea_perioxi').removeClass('mydisabled');
//      $('#form_ea_poli').removeClass('mydisabled');
//      $('#form_ea_tk').removeClass('mydisabled');
//      $('#form_ea_country_id').removeClass('mydisabled');
//      $('#form_ea_nomos_id').removeClass('mydisabled');
//      
//      $('#form_ea_name').prop('readonly', false);
//      $('#form_ea_phone').prop('readonly', false);
//      $('#form_ea_odos').prop('readonly', false);
//      $('#form_ea_perioxi').prop('readonly', false);
//      $('#form_ea_poli').prop('readonly', false);
//      $('#form_ea_tk').prop('readonly', false);
//      $('#form_ea_country_id').attr("disabled", false);
//      $('#form_ea_nomos_id').attr("disabled", false);

      
      $('#form_ea_name').val('');
      $('#form_ea_phone').val('');
      $('#form_ea_odos').val('');
      $('#form_ea_arithmos').val('');
      $('#form_ea_orofos').val('');
      $('#form_ea_perioxi').val('');
      $('#form_ea_poli').val('');
      $('#form_ea_tk').val('');

      v1 = $('#dr_user_ma_country_id').val();
      v2 = $('#dr_user_ma_nomos_id').val();

      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
    } else {
//      $('#form_ea_name').addClass('mydisabled');
//      $('#form_ea_phone').addClass('mydisabled');
//      $('#form_ea_odos').addClass('mydisabled');
//      $('#form_ea_perioxi').addClass('mydisabled');
//      $('#form_ea_poli').addClass('mydisabled');
//      $('#form_ea_tk').addClass('mydisabled');
//      $('#form_ea_country_id').addClass('mydisabled');
//      $('#form_ea_nomos_id').addClass('mydisabled');
//      
//      $('#form_ea_name').prop('readonly', true);
//      $('#form_ea_phone').prop('readonly', true);
//      $('#form_ea_odos').prop('readonly', true);
//      $('#form_ea_perioxi').prop('readonly', true);
//      $('#form_ea_poli').prop('readonly', true);
//      $('#form_ea_tk').prop('readonly', true);
//      $('#form_ea_country_id').attr("disabled", true);
//      $('#form_ea_nomos_id').attr("disabled", true);
      
      
      $('#form_ea_name').val(extra_address[v].ea_name);
      $('#form_ea_phone').val(extra_address[v].ea_phone);
      $('#form_ea_odos').val(extra_address[v].ea_odos);
      $('#form_ea_arithmos').val(extra_address[v].ea_arithmos);
      $('#form_ea_orofos').val(extra_address[v].ea_orofos);
      $('#form_ea_perioxi').val(extra_address[v].ea_perioxi);
      $('#form_ea_poli').val(extra_address[v].ea_poli);
      $('#form_ea_tk').val(extra_address[v].ea_tk);

      v1 = extra_address[v].ea_country_id;
      v2 = extra_address[v].ea_nomos_id;
      
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      checkout_update(false,false,false);
    }
  }    
    
  if (from_php_SESSION_gks_basket_address_extra == 0) {
    $('#form_ea_country_id').val(from_php_SESSION_gks_basket_destination_data_country_id);
    nomos_fill('form_ea_nomos_id',from_php_SESSION_gks_basket_destination_data_country_id,from_php_SESSION_gks_basket_destination_data_nomos_id);
  } else if (from_php_SESSION_gks_basket_address_extra > 0) {  //diladi oxi -1 poy einai to idio  kai oxi nea dieuynsi
    extra_address_select(from_php_SESSION_gks_basket_address_extra);
  } 

 

  $('#header_basket_show').click(function(event){
    $('body').removeClass('gks_myloading'); 
    window.location.href = from_php_gks_api_hotel_page_reservation_basket + from_php_gks_set_lang_url;
  });

    
});