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
      $('.gks_tdblock').each(function() {
        $(this).css('display','table-cell');
      });       
      $('#table-basket-header').show();
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
      
      $('.gks_tdblock').each(function() {
        $(this).css('display','block');
      });  
      $('#table-basket-header').hide();
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
              gks_myalert('error: dialog_confirm function_ok');
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
  
  

  $('.tooltipster').tooltipster({theme: 'tooltipster-noir'});
  
  $('#back_to_checkout').click(function(event){
    window.location.href=from_php_gks_api_page_checkout + from_php_gks_set_lang_url;
  });
  $('#header_basket_show').click(function(event){
    window.location.href=from_php_gks_api_hotel_page_reservation_basket + from_php_gks_set_lang_url;
  });
  $('#header_basket_checkout').click(function(event){
    window.location.href=from_php_gks_api_page_checkout + from_php_gks_set_lang_url;
  });
  $('#gks_update').click(function(event){
    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    if (d<=0) {
      gks_myalert('error:' + from_php_lang_Pleaseselectashippingmethod);
      return;
    }
    if (p<=0) {
      gks_myalert('error:' + from_php_lang_Pleaseselectapaymentmethod);
      return;
    }
        
    basket_edit(false,true,false,'delivery_payment', 0, d, p, '', 0);
    
    //basket_edit(true,true,false,'');
  });


  
    
  //header_basket_show
  //header_basket_checkout
  //header_basket_pay
  //header_basket_confirm



  $('input[name=radio_delivery_way]').click( function() {
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');

    $('input[name=radio_payment_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().children('.delivery_payment_label').addClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').addClass('delivery_payment_disabled');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
          $('#payment_acquirer_sxolio').html('');
          $('#button_html').html(from_php_lang_Paynow);
        }
      }
    });
    
    $('input[name=radio_delivery_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });

    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    basket_edit(false,true,false,'delivery_payment', 0, d, p, '', 0);    
    
    $('#delivery_method_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#delivery_method_sxolio').html(from_php_lang_ShippingComment + ': <i>' + myhtml + '</i>');

    if (d == 8) {
      $('#span_delivery_id_8').show();
    } else {
      $('#span_delivery_id_8').hide();
    }
  });
  
  
  
  
  $('input[name=radio_payment_way]').click(function() {
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');
    

    $('input[name=radio_delivery_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().children('.delivery_payment_label').addClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').addClass('delivery_payment_disabled');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
          $('#delivery_method_sxolio').html('');
        }
      }
    });

    $('input[name=radio_payment_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });    
    
    
    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    basket_edit(false,true,false,'delivery_payment', 0, d, p, '', 0);
    
    
    $('#payment_acquirer_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#payment_acquirer_sxolio').html(from_php_lang_PaymentComment + ': <i>' + myhtml + '</i>');
    
    myhtml= $.base64.decode($(this).attr('data-button-html'));
    if (myhtml=='') myhtml=from_php_lang_Paynow;
    $('#button_html').html(myhtml);
  });


  function basket_edit(showloading, showerrors,gonext, mycmd, myindex, myproduct_id, myobject, myfile, myvalue) {
      //$("body").addClass("gks_myloading;
      
      //encodeURI
      mydatasend='';
      mydatasend+='&command=basket_edit';
      mydatasend+='&showloading='  + encodeURIComponent((showloading ? '1' : '0'));
      mydatasend+='&showerrors='  + encodeURIComponent((showerrors ? '1' : '0'));
      mydatasend+='&gonext='  + encodeURIComponent((gonext ? '1' : '0'));
      
      mydatasend+='&cmd=' + mycmd + '&myindex=' + myindex + '&product_id=' + myproduct_id + '&object=' + myobject + '&file=' + myfile + '&value=' + myvalue;
      //console.log(mydatasend);
          
      if (gonext || showloading) $('body').addClass("gks_myloading"); 
      $('#gks_loading_roll').show(); 
      $('#gks_update').hide();
      
      $('#gks_payment_acquirer_piraeusbank').html('');
                
		  $.ajax({
				url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
				type: 'POST',
				cache: false,
				mycmd: mycmd,
				gks_showerrors: showerrors,
				gks_gonext: gonext,				
				myproduct_id: myproduct_id,
				myobject: myobject,
				myfile: myfile,
				myvalue: myvalue,
				
				dataType: 'json',
				data: mydatasend,
				error : function(jqXHR ,textStatus,  errorThrown) {
  				$("body").removeClass("gks_myloading"); 
  				$('#gks_loading_roll').hide(); 
  				$('#gks_update').show();
  				//console.log('error:' + jqXHR.responseText);
				  if (this.gks_showerrors) gks_myalert('error:' + jqXHR.responseText);
				},				
				success: function(data) {
				  if (!(data && data.success && this.gks_gonext)) $("body").removeClass("gks_myloading");
  				$('#gks_loading_roll').hide(); 
  				$('#gks_update').show();
  				//console.log(data);
  				if (!data) {
  				  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  				} else {
    				  
    				if (data.success == false) {
    					if (data.message.length > 0){
    						gks_myalert('error:' + $.base64.decode(data.message));
    					} else {
    					  gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
    					}
    				} else {
  				    data=data.data;
  				    if (data.success == false) {
      					if (data.message.length > 0){
      						gks_myalert('error:' + $.base64.decode(data.message));
      					} else {
      					  gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
      					}
      				} else {
    				 				  
                will_show=false;
                for (var item in data.tropoi_apostolis_all) {
      					  var obj = data.tropoi_apostolis_all[item];
                  if (obj.myisok!='0' && obj.id_delivery_method!=1) will_show=true;
      					  elem = $('#price_delivery_way_' + obj.id_delivery_method);
      					  elem.html( parseFloat(obj.dm_calc_kostos).mymoney());
                  elem2=$('#radio_delivery_way_' +  obj.id_delivery_method);
                  elem2.attr('data-type-o',obj.delivery_method_type_pa )
                  elem3=elem2.parent();
                  if (obj.myisok=='0') {
                    elem3.hide();
                    if (elem2.prop('checked')) elem2.prop('checked', false);
                  } else {
                    elem3.show();
                  } 
      					}
                if (will_show) {
                  $('#div_delivery_way').show();
                } else {
                  $('#div_delivery_way').hide();
                  $('#radio_delivery_way_1').prop('checked', true);
                }
    
                will_show=false;
      					for (var item in data.tropoi_pliromis_all) {
      					  var obj = data.tropoi_pliromis_all[item];
      					  if (obj.myisok!='0' && obj.id_payment_acquirer!=1) will_show=true;
      					  elem=$('#price_payment_way_' + obj.id_payment_acquirer);
      					  elem.html(parseFloat(obj.pa_calc_kostos).mymoney());
      					  elem2=$('#radio_payment_way_' +  obj.id_payment_acquirer);
      					  elem2.attr('data-type-o',obj.payment_acquirer_type_dm)
      					  elem3=elem2.parent();
      					  if (obj.myisok=='0') {
                    elem3.hide();
                    if (elem2.prop('checked')) elem2.prop('checked', false);
                  } else {
                    elem3.show();
                  }
      					}
    		        if (will_show) {
                  $('#div_payment_way').show();
                } else {
                  $('#div_payment_way').hide();
                  $('#radio_payment_way_1').prop('checked', true);
                }
                
                if (data.products_posotita) {
                  data.products_posotita= $.base64.decode(data.products_posotita);
                  $('#span_products_posotita1').html(data.products_posotita);
                  $('#span_products_posotita2').html(data.products_posotita);
                  if (data.products_posotita>0) {
                    $('#menu-item-1201').removeClass('myhidden');
                    $('#menu-item-1202').removeClass('myhidden');
                  } else {
                    $('#menu-item-1201').addClass('myhidden');
                    $('#menu-item-1202').addClass('myhidden');
                  }
                }			  
                
                products_total_val=0;       
                products_posotita_val=0;      
                if (data.products_netvalue) {
                  products_total_val = data.products_total_val;
                  products_posotita_val = data.products_posotita_val;
                  
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
      				  
      				  if (data.success == false){
        					if (data.message.length > 0){
        						if (this.gks_showerrors) gks_myalert('error:' + $.base64.decode(data.message));
        					} else {
        					  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
        					}
        				}
        				
    //    				velemp=$('input[name=radio_delivery_way]:visible:enabled:checked');
    //    				if (velemp.length==1 && velemp.val() == 1) {
    //    				  $('#div_delivery_way').hide();
    //    				} else {
    //    				  $('#div_delivery_way').show();
    //    				}
        				
        				//} else if (will_run_calc_pliroteo2==true) { 
      					//  velemp=$('input[name=radio_payment_way]:visible:enabled:checked');
      					//  if (velemp.length==1) {if (velemp.prop('checked')) {velemp.click();}}
        				//}
        				
      				  if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
                gks_myscroll();  
        				return;
        			}
    				}
    				
    			}
				}
				
			});           
    
  }
  
//  d=$('input[name=radio_delivery_way]:checked').val();
//  if (d === undefined || d === null) d=0;
//  if (d>0) {
//    $('#radio_delivery_way_' + d).trigger('click');
//  }
//  
//
//  
//  p=$('input[name=radio_payment_way]:checked').val();
//  if (p === undefined || p === null) p=0;
//  if (p>0) {
//    $('#radio_payment_way_' + p).trigger('click');
//  }
  
   
  
  $('#pay_now').click(function(event){	

    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    delivery_id_8=0;
    if (d == 8) {
      if ($('#delivery_id_8').val() == 0) {
        gks_myalert('error:' + from_php_lang_Pleaseselectthestoreyouwanttopickupyourproducts);
        return;  
      }
      delivery_id_8=$('#delivery_id_8').val();
    }
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    if (d<=0) {
      gks_myalert('error:' + from_php_lang_Pleaseselectashippingmethod);
      return;
    }
    if (p<=0) {
      gks_myalert('error:' + from_php_lang_Pleaseselectapaymentmethod);
      return;
    }
    
    mydatasend='';
    mydatasend+='&command=payment_edit';
    mydatasend+='&delivery_id_8=' + delivery_id_8;
    //console.log(mydatasend);
    
    $('#gks_payment_acquirer_piraeusbank').html('');
    
    $('body').addClass('gks_myloading');
    
	  $.ajax({
			url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: mydatasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$("body").removeClass("gks_myloading");
				gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
				  $("body").removeClass("gks_myloading");
				  gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
				} else {
				  //console.log(data);
  				if (data.success == false) {
  				  $("body").removeClass("gks_myloading");
  					if (data.message.length > 0){
  						gks_myalert('error:' + $.base64.decode(data.message));
  					} else {
  					  gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  					}
  				} else {
  				  data=data.data;
    				if (data.success == false) {
    				  $("body").removeClass("gks_myloading");
    					if (data.message.length > 0){
    						gks_myalert('error:' + $.base64.decode(data.message));
    					} else {
    					  gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
    					}

    				} else { 				  
            //$("body").removeClass("gks_myloading");
              if (data.piraeusbank != null && data.piraeusbank==true && data.piraeusbank_form!='') {
                $('#gks_payment_acquirer_piraeusbank').html($.base64.decode(data.piraeusbank_form));
                $('#gks_payment_acquirer_piraeusbank_submit').click();
                
                return;
              }
    				  if (data.url) {
                //gks_myalert('error: url : ' + $.base64.decode(data.url));
                $('body').addClass('gks_myloading');
                window.location.href = $.base64.decode(data.url);
              }
            }
          }    				  
  			}
			}
			
		});           
		   
  
  });  
  
  
  $('#dismiss_alert').click(function() {
    $('#div_dismiss_alert').hide(500);
    
  });
  
  
});
