/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var gks_dialog_message;
var gks_dialog_big_message;
var gks_dialog_confirm;
var gks_dialog_visitor_details;
var coupon_delete_click;

var gks_dialog_visitor_details_heights=[460,750];


jQuery3(document).ready(function($) {    
  

  //console.log(json_rooms_list);
  
  $.base64.utf8encode = true;
  $.base64.utf8decode = true;  
  
  $.datetimepicker.setLocale(from_php_gks_datetimepicker_locale);
  
     
  function gks_myresize() {
    var gks_content_width=$(window).width();
   //var gks_content_width = $('#gks_content').width();
    if (gks_content_width>=768) {
      $('.gks_rsrv_bc1').each(function() {
        $(this).css('width','calc(60% - 20px)').css('float','left');
      });
      $('.gks_rsrv_bc2').each(function() {
        $(this).css('width','calc(40% - 20px)').css('float','left');
      });
      $('.gks_col6').each(function() {
        $(this).css('width','calc(50%)').css('float','left');
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
      $('.gks_rsrv_bc1').each(function() {
        $(this).css('width','calc(100% - 20px)').css('float','none');
      });
      $('.gks_rsrv_bc2').each(function() {
        $(this).css('width','calc(100% - 20px)').css('float','none');
      });
      $('.gks_col6').each(function() {
        $(this).css('width','calc(100%)').css('float','none');
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
    
  gks_dialog_message = $('#gks_dialog_message').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "from_php_lang_OK": {
        html:"<i class='fa fa-pen-square'></i> " + from_php_lang_OK,
        click: function() {
          $(this).dialog( "close" );
        }
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
    if (dwidth> 550) dwidth=550;
    if (dheight> 500) dheight=500;
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
        html:"<i class='fa fa-pen-square'></i> " + from_php_lang_OK,
        click: function() {
      
          $(this).dialog('close');
          
          switch (gks_dialog_confirm.function_ok) {
            case 'gks_book_c1':
              gks_check1=true;
              gks_book();
              break;       
            case 'gks_book_c2':
              gks_check2=true;
              gks_book();
              break;
            case 'delete_room':
              data_rsrv_aa = gks_dialog_confirm.param1;
              data_roomtype_aa = gks_dialog_confirm.param2;
              data_room_aa = gks_dialog_confirm.param3;
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_delete=1;
              
              //console.log('data_rsrv_aa:' + data_rsrv_aa + ' data_roomtype_aa:' + data_roomtype_aa + ' data_room_aa:' + data_room_aa);
              $('.gks_rsrv_br[data_rsrv_aa=' + data_rsrv_aa + '][data_roomtype_aa=' + data_roomtype_aa + '][data_room_aa=' + data_room_aa + ']').remove();
              if ($('.gks_rsrv_br[data_rsrv_aa=' + data_rsrv_aa + '][data_roomtype_aa=' + data_roomtype_aa + ']').length == 0) {
                $('.gks_rsrv_bd[data_rsrv_aa=' + data_rsrv_aa + '][data_roomtype_aa=' + data_roomtype_aa + ']').remove();
              }
              if ($('.gks_rsrv_bd[data_rsrv_aa=' + data_rsrv_aa + ']').length == 0) {
                $('.gks_rsrv_rs[data_rsrv_aa=' + data_rsrv_aa + ']').remove();
              }
              rooms_basket_edit(false,true,false,true);
              break;
            case 'basket_edit':
              basket_edit(false,true,false,gks_dialog_confirm.param1, gks_dialog_confirm.param2, gks_dialog_confirm.param3, gks_dialog_confirm.param4, gks_dialog_confirm.param5, gks_dialog_confirm.param6, gks_dialog_confirm.param7);
              break;
            case 'tpwarning':
              //$('body').removeClass('gks_myloading');
              //window.location.href = from_php_gks_api_page_checkout + from_php_gks_set_lang_url;
              gks_goto_chekcout_or_cart();
              break;
              
            default:
              gks_myalert('error: dialog_confirm function_ok');
              break;
          }
        
        }
      },
      "from_php_lang_Cancel" : {
        html: "<i class='fa fa-window-close'></i> " + from_php_lang_Cancel,
        click: function() {
          $(this).dialog('close');
          gks_check1=false;
          gks_check2=false;
        }
      },
    }
  });
  
  function gks_myconfirm(mymessage, function_ok,param1,param2,param3,param4,param5,param6,param7) {
    $("#gks_dialog_confirm_message").html(mymessage);
    gks_dialog_confirm.function_ok = function_ok;
    gks_dialog_confirm.param1 = param1;
    gks_dialog_confirm.param2 = param2;
    gks_dialog_confirm.param3 = param3;
    gks_dialog_confirm.param4 = param4;
    gks_dialog_confirm.param5 = param5;
    gks_dialog_confirm.param6 = param6;
    gks_dialog_confirm.param7 = param7;
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 500) dwidth=500;
    if (dheight> 500) dheight=500;
    gks_dialog_confirm.dialog('option', 'width', dwidth);
    gks_dialog_confirm.dialog('option', 'height', dheight);
    $('#gks_dialog_confirm').parent().css({position:'fixed'});      
    gks_dialog_confirm.dialog('open');
  };   
  
    
  gks_dialog_big_message = $( "#gks_dialog_big_message" ).dialog({
    autoOpen: false,
    width: 450,
    height: 330,
    modal: true,
    buttons: {
      "OK" : function() {
        $(this).dialog('close');
      }
    }
  });

  function gks_mybigalert(mymessage) {
    $("#gks_dialog_big_message_message").html(mymessage);
	  dwidth=$(window).width() * 0.96;
//	  if ($(window).width() >=980-20) {
//	    dheight=$(window).height() - 150;
//	  } else {
	    dheight=$(window).height() * 0.96;
//	  }
	  if (dwidth> 1000) dwidth=1000;
	  if (dheight> 800) dheight=800;
	  gks_dialog_big_message.dialog('option', 'width', dwidth);
	  gks_dialog_big_message.dialog('option', 'height', dheight);
	  $('#gks_dialog_big_message').parent().css({position:'fixed'});      
    gks_dialog_big_message.dialog('open');
  }; 


  gks_dialog_visitor_details = $( "#gks_dialog_visitor_details" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: {
      "from_php_lang_OK": {
        html:"<i class='fa fa-pen-square'></i> " + from_php_lang_OK,
        click : function() {
        
          data_rsrv_aa = gks_dialog_visitor_details.data_rsrv_aa;
          data_roomtype_aa = gks_dialog_visitor_details.data_roomtype_aa;
          data_room_aa =gks_dialog_visitor_details.data_room_aa;
          //console.log(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa]);
          
          
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].first_name = $('#dr_user_first_name').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].last_name = $('#dr_user_last_name').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email = $('#dr_user_email').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile = $('#dr_user_mobile').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].lang = $('#dr_user_lang').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_odos = $('#dr_user_ma_odos').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_arithmos = $('#dr_user_ma_arithmos').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_orofos = $('#dr_user_ma_orofos').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_poli = $('#dr_user_ma_poli').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_tk = $('#dr_user_ma_tk').val();
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id =  myparseInt($('#dr_user_ma_country_id').val());
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_nomos_id =  myparseInt($('#dr_user_ma_nomos_id').val());
          json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_same=0;
          
          if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].first_name == '' &&
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].last_name == '' &&
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email == '' &&
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile == '' &&
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].lang == '' &&
              json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id == 0) {
                
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_same=1;
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_odos = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_arithmos = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_orofos = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_perioxi = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_poli = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_tk = '';
            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_nomos_id =  0;
          }
          
          
          elemid=data_rsrv_aa + '_' + data_roomtype_aa + '_' + data_room_aa; 
          if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_same == 1) {
            $('#selecttype0_' + elemid ).prop( "checked", true );
            $('#selecttype1_' + elemid ).prop( "checked", false );
            $('#visitor_name_' + elemid).html('');
          } else {
            $('#selecttype0_' + elemid ).prop( "checked", false );
            $('#selecttype1_' + elemid ).prop( "checked", true );
            visitor_name='';
            visitor_name += json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].first_name + ' '+
                            json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].last_name;
            visitor_name=visitor_name.trim();
            if (visitor_name != '' ) visitor_name+= ', ';
            
            if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email != '')
              visitor_name += json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email + ', ';
            if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile != '')
              visitor_name += json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile + ', ';
            visitor_name=visitor_name.trim();
            
            if (visitor_name.length>1) visitor_name=visitor_name.substr(0, visitor_name.length - 1);
            $('#visitor_name_' + elemid).html(visitor_name);
          }
          
              
          //console.log(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa]);
          rooms_basket_edit(false,true,false,false);
          $(this).dialog('close');
        }
      },
      "from_php_lang_Cancel": {
        html: "<i class='fa fa-window-close'></i> " + from_php_lang_Cancel,
        click: function() {
          data_rsrv_aa = gks_dialog_visitor_details.data_rsrv_aa;
          data_roomtype_aa = gks_dialog_visitor_details.data_roomtype_aa;
          data_room_aa =gks_dialog_visitor_details.data_room_aa;
          elemid=data_rsrv_aa + '_' + data_roomtype_aa + '_' + data_room_aa; 
          if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_same == 1) {
            $('#selecttype0_' + elemid ).prop( "checked", true );
            $('#selecttype1_' + elemid ).prop( "checked", false );
          } else {
            $('#selecttype0_' + elemid ).prop( "checked", false );
            $('#selecttype1_' + elemid ).prop( "checked", true );
          }
          rooms_basket_edit(false,true,false,false);
          $(this).dialog('close');
        }
      }
    }
  });  
  

  $('.gks_rsrv_basket_visitor_check0').click(function () {
    data_rsrv_aa=$(this).attr('data_rsrv_aa');
    data_roomtype_aa=$(this).attr('data_roomtype_aa');
    data_room_aa=$(this).attr('data_room_aa');
    elemid=data_rsrv_aa + '_' + data_roomtype_aa + '_' + data_room_aa; 
    $('#visitor_name_' + elemid).html('');
    
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_same = 1;
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].first_name = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].last_name = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].lang = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_odos = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_arithmos = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_orofos = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_perioxi = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_poli = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_tk = '';
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_nomos_id =  0;
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id = 0;
    
    //console.log('data_rsrv_aa:' + data_rsrv_aa + ' data_roomtype_aa:' + data_roomtype_aa + ' data_room_aa:' + data_room_aa);
    rooms_basket_edit(false,true,false,false);
  });

  var dr_div_customer_more = 0;
  $('.gks_rsrv_basket_visitor_check1').click(function () {
    data_rsrv_aa=$(this).attr('data_rsrv_aa');
    data_roomtype_aa=$(this).attr('data_roomtype_aa');
    data_room_aa=$(this).attr('data_room_aa');
    

    if (dr_div_customer_more == 1) {
      $('#dr_div_customer_more_show').hide();
      $('.dr_divs_customer_more').each(function() {
        $(this).show();  
      }); 
      $('#dr_div_customer_more_hide').show();       
    } else {
      $('#dr_div_customer_more_hide').hide();
      $('.dr_divs_customer_more').each(function() {
        $(this).hide();  
      }); 
      $('#dr_div_customer_more_show').show();       
    }
    
    $('#dr_user_first_name').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].first_name);
    $('#dr_user_last_name').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].last_name);
    $('#dr_user_email').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].email);
    $('#dr_user_mobile').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].mobile);
    $('#dr_user_lang').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].lang);
    $('#dr_user_ma_odos').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_odos);
    $('#dr_user_ma_arithmos').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_arithmos);
    $('#dr_user_ma_orofos').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_orofos);
    $('#dr_user_ma_perioxi').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_perioxi);
    $('#dr_user_ma_poli').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_poli);
    $('#dr_user_ma_tk').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_tk);
    
    if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id > 0) {
      $('#dr_user_ma_country_id').val(json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id);
       nomos_fill('dr_user_ma_nomos_id',json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_country_id,json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].ma_nomos_id);
    } else {
      $('#dr_user_ma_country_id').val('0');
      $('#dr_user_ma_nomos_id').val('0');
    }
        
    //console.log('data_rsrv_aa:' + data_rsrv_aa + ' data_roomtype_aa:' + data_roomtype_aa + ' data_room_aa:' + data_room_aa);
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 500) dwidth=500;
    
    wwidth=$(window).width();
    if (wwidth>=768) {
  	  if (dheight> gks_dialog_visitor_details_heights[1]) dheight=gks_dialog_visitor_details_heights[1];
  	  if (dr_div_customer_more==0 && dheight > gks_dialog_visitor_details_heights[0]) dheight=gks_dialog_visitor_details_heights[0];
  	} else {
  	  //if (dheight > 960) dheight=960;
  	  //if (dr_div_customer_more==0) dheight=500;  	  
  	}    
    
    gks_dialog_visitor_details.data_rsrv_aa=data_rsrv_aa;
    gks_dialog_visitor_details.data_roomtype_aa=data_roomtype_aa;
    gks_dialog_visitor_details.data_room_aa=data_room_aa;
    
    //console.log(dheight);
    
    gks_dialog_visitor_details.dialog('option', 'width', dwidth);
    gks_dialog_visitor_details.dialog('option', 'height', dheight);
    $('#gks_dialog_visitor_details').parent().css({position:'fixed'});     
    gks_dialog_visitor_details.dialog('open');    
    
  });
    

  
  $('#dr_customer_more_show').click(function() {
    dr_div_customer_more=1;
    $('#dr_div_customer_more_show').hide();
    $('.dr_divs_customer_more').each(function() {
      $(this).show(500);  
    }); 
    $('#dr_div_customer_more_hide').show(500);
    
    if ($(window).width()>=768) {
  	  var dheighta=$(window).height() * 0.98;
  	  if (dheighta> gks_dialog_visitor_details_heights[1]) dheighta=gks_dialog_visitor_details_heights[1];
      gks_dialog_visitor_details.dialog("widget").animate({
          height: dheighta
      }, {
          duration: 500,
          step: function (now, tween) {
            gks_dialog_visitor_details.dialog("option", "height", now);
          }
      });    
    } 
  });
  $('#dr_customer_more_hide').click(function() {
    dr_div_customer_more=0;
    $('#dr_div_customer_more_hide').hide();
    $('.dr_divs_customer_more').each(function() {
      $(this).hide(500,function() {
        if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
        gks_myscroll(); 
      });  
    }); 
    $('#dr_div_customer_more_show').show(500);

    
    if ($(window).width()>=768) {
  	  var dheighta=$(window).height() * 0.98;
  	  if (dheighta> gks_dialog_visitor_details_heights[0]) dheighta=gks_dialog_visitor_details_heights[0];
      gks_dialog_visitor_details.dialog("widget").animate({
          height: dheighta
      }, {
          duration: 500,
          step: function (now, tween) {
            gks_dialog_visitor_details.dialog("option", "height", now);
          }
      });
    }
  });


  function nomos_fill(myelement,v,nomos_id) {  
    mydata = '&command=get_nomoi&id=' + v;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) {
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
				  gks_myalert('error:' + jqXHR.responseText);
			  },
        success: function(data) {
          if (!data) {
  					gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  				} else {
  				  if (typeof data.myreload !== 'undefined') myreload=data.myreload;
            if (data.success == true) {
              data=data.data;
              if (data.out) {
    				    for (i = 0; i < data.out.length; i++) {
    				      $('#' + this.myelement).append('<option value="' + data.out[i].id + '">' + data.out[i].descr + '</option>');
    				    }
    				    
    				    $('#' + this.myelement).val(this.nomos_id);
    				  }
    				  if (myreload) window.location.reload();
            } else {
              gks_myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
    
  }   
  
  $('#dr_user_ma_country_id').change(function() {
    v=$(this).val();
    nomos_fill('dr_user_ma_nomos_id',v,0);
  });
  
  $('.gks_rsrv_basket_delete_icon').click(function() {
    data_rsrv_aa=$(this).attr('data_rsrv_aa');
    data_roomtype_aa=$(this).attr('data_roomtype_aa');
    data_room_aa=$(this).attr('data_room_aa');
    //console.log('data_rsrv_aa:' + data_rsrv_aa + ' data_roomtype_aa:' + data_roomtype_aa + ' data_room_aa:' + data_room_aa);
    
    gks_myconfirm(from_php_lang_Surelyyouwanttodeletetheroom,'delete_room',data_rsrv_aa,data_roomtype_aa,data_room_aa);
  });
  
  
  
  
  
  
  
  function rooms_basket_edit(showloading,showerrors,gonext,reloadafter) {
    //console.log(json_rooms_list);
    //datasend = JSON.stringify(json_rooms_list);
    //console.log(datasend);
    
    rooms_items_disabled();
    
    basket_edit(showloading,showerrors,gonext,'rooms', 0, 0, 0, '', 0, reloadafter);

  }

  $('.rooms_items').change(function() {
    data_rsrv_aa=$(this).attr('data_rsrv_aa');
    data_roomtype_aa=$(this).attr('data_roomtype_aa');
    data_room_aa=$(this).attr('data_room_aa');
    //console.log();
    //console.log('data_rsrv_aa:' + data_rsrv_aa + ' data_roomtype_aa:' + data_roomtype_aa + ' data_room_aa:' + data_room_aa);
    value_room_id = parseInt($(this).val());
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].room_item_id = value_room_id;
    
    rooms_basket_edit(false,true,false,false);
    
  });
  
  function rooms_items_disabled() {
    
    $('.rooms_items').each(function() {
      var this_rsrv_aa=$(this).attr('data_rsrv_aa');
      var this_roomtype_aa=$(this).attr('data_roomtype_aa');
      var this_room_aa=$(this).attr('data_room_aa');
      var this_val = parseInt($(this).val());
      $(this).children('option').each(function() {
        thisval = parseInt($(this).attr('value'));
        if (thisval == 0) return;
        thiswill=false;
        end_loops:
        for(var data_rsrv_aa in json_rooms_list) {
          isoverlap=json_rooms_list[this_rsrv_aa].other_rsrv_time_overlap.includes(parseInt(data_rsrv_aa));
          if (data_rsrv_aa == this_rsrv_aa || isoverlap==true) {
            for(var data_roomtype_aa in json_rooms_list[data_rsrv_aa].selrooms) {
              for(var data_room_aa in json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items) {
                if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].room_item_id == thisval) {
                  if (json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].is_delete == 0) {
                    if (!(this_rsrv_aa == data_rsrv_aa && this_roomtype_aa == data_roomtype_aa && this_room_aa == data_room_aa)) {
                      thiswill=true;
                      break end_loops;
                    }
                  }
                }
              }
            }
          }
        }
        $(this).prop('disabled',thiswill);
      });
    });
  }
  
  rooms_items_disabled();    
    
    
    
   
  // old func    

  $('.basket_product_help').click(function(event){	
    myhelptext=$.base64.decode($(this).attr('data-help'));
    mybigalert(myhelptext);
    
  });



  $('.rowdelete').click(function(event){	
    //                                    basket_edit(showloading,showerrors,gonext,      mycmd,      myindex,                   myproduct_id,                  myobject,                    myfile,myvalue,reloadafter)
    gks_myconfirm(from_php_lang_Surelyyouwanttoremovetheproductfromyourcart,'basket_edit','rowdelete',$(this).attr('data-index'),$(this).attr('data-product_id'),$(this).attr('data-object'),'',    0,      false);
  });
  
  $('.button_plus1').click(function(event){	
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myvalue=parseInt($('#input_' + myindex + '_' + myproduct_id + '_' + myobject).val());
    myvalue=myvalue + 1;
    basket_edit(false,true,false, 'rowposotita', myindex, myproduct_id, myobject, '', myvalue, false);
  }); 

  $('.button_minus1').click(function(event){	
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myvalue=parseInt($('#input_' + myindex + '_' + myproduct_id + '_' + myobject).val());
    myvalue=myvalue - 1;
    if (isNaN(myvalue)) myvalue=1;
    if (myvalue<=0) {
      basket_edit(false,true,false, 'rowdelete',   myindex, myproduct_id, myobject, '', 0, false);
    } else {
      basket_edit(false,true,false, 'rowposotita', myindex, myproduct_id, myobject, '', myvalue, false);
    }
  }); 
    
  $('.input_rowposotita').change(function(event) {
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myvalue=parseInt($(this).val());
    if (isNaN(myvalue)) myvalue=1;
    if (myvalue < 1) myvalue=1;
    basket_edit(false,true,false, 'rowposotita', myindex, myproduct_id, myobject, '', myvalue, false);
  });
  
  $('.filedelete').click(function(event){	
    //                                     basket_edit(showloading,showerrors,gonext,      mycmd,      myindex,                   myproduct_id,                    myobject,                   myfile,                   myvalue,reloadafter)
    gks_myconfirm(from_php_lang_Surelyyouwanttoremovethefilefromthatproduct,'basket_edit','filedelete',$(this).attr('data-index'),$(this).attr('data-product_id'), $(this).attr('data-object'), $(this).attr('data-file'),0,     false);
  });

  $('.button_file_plus1').click(function(event){	
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myfile=        $(this).attr('data-file');
    myvalue=parseInt($('#input_file_' + myindex + '_' + myproduct_id + '_' + myobject + '_' + myfile).val());
    myvalue=myvalue + 1;
    basket_edit(false,true,false, 'fileposotita', myindex, myproduct_id, myobject, myfile, myvalue, false);
  }); 

  $('.button_file_minus1').click(function(event){	
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myfile=        $(this).attr('data-file');
    myvalue=parseInt($('#input_file_' + myindex + '_' + myproduct_id + '_' + myobject + '_' + myfile).val());
    myvalue=myvalue - 1;
    if (isNaN(myvalue)) myvalue=1;
    if (myvalue<=0) {
      basket_edit(false,true,false, 'filedelete',   myindex, myproduct_id, myobject, myfile, 0, false);
    } else {
      basket_edit(false,true,false, 'fileposotita', myindex, myproduct_id, myobject, myfile, myvalue, false);
    }
  });   

  $('.input_file_posotita').change(function(event) {
    myindex =      $(this).attr('data-index');
    myproduct_id = $(this).attr('data-product_id');
    myobject=      $(this).attr('data-object');
    myfile=        $(this).attr('data-file');
    myvalue=parseInt($(this).val());
    if (isNaN(myvalue)) myvalue=1;
    if (myvalue < 1) myvalue=1;
    basket_edit(false,true,false, 'fileposotita', myindex, myproduct_id, myobject, myfile, myvalue, false);
  }); 
  
  
  function basket_edit(showloading,showerrors,gonext,mycmd,myindex,myproduct_id,myobject,myfile,myvalue,reloadafter) {
      //$("body").addClass("myloading");
      
      //encodeURI
      mydatasend='';
      mydatasend+='&command=basket_edit';
      mydatasend+='&showloading='  + encodeURIComponent((showloading ? '1' : '0'));
      mydatasend+='&showerrors='  + encodeURIComponent((showerrors ? '1' : '0'));
      mydatasend+='&gonext='  + encodeURIComponent((gonext ? '1' : '0'));
      
      mydatasend+='&cmd=' + mycmd + '&myindex=' + myindex + '&product_id=' + myproduct_id + '&object=' + myobject + '&file=' + myfile + '&value=' + myvalue;
      
      datasend_rooms = JSON.stringify(json_rooms_list);
      mydatasend+='&datasend_rooms=' +  encodeURIComponent($.base64.encode(datasend_rooms));
      //console.log(mydatasend);
      //console.log(json_rooms_list);

      //return;
      if (gonext || showloading) $('body').addClass('gks_myloading'); 
      $('#gks_loading_roll').show(); 
      $('#gks_update').hide();
      
      
		  $.ajax({
				url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
				type: 'POST',
				cache: false,
				
				mycmd: mycmd,
				gks_showerrors: showerrors,
				gks_gonext: gonext,
				myindex: myindex,
				myproduct_id: myproduct_id,
				myobject: myobject,
				myfile: myfile,
				myvalue: myvalue,
				myreloadafter:reloadafter,
				dataType: 'json',
				data: mydatasend,
				error : function(jqXHR ,textStatus,  errorThrown) {
  				$('body').removeClass('gks_myloading'); 
  				$('#gks_loading_roll').hide(); 
  				$('#gks_update').show();
  				//console.log('error:' + jqXHR.responseText);
				  if (this.gks_showerrors) gks_myalert('error:' + jqXHR.responseText);
				},				
				success: function(data) {
				  $('body').removeClass('gks_myloading');
				  //if (!(data && data.success && this.gks_gonext)) $('body').removeClass('gks_myloading'); 
  				$('#gks_loading_roll').hide(); 
  				$('#gks_update').show();
  				//console.log(data);
  				if (!data) {
  				  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  				} else {
    				if (data.success == false){
    					if (data.message.length > 0){
    						if (this.gks_showerrors) gks_myalert('error:' + $.base64.decode(data.message));
    						
    					} else {
    					  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
    					}
    					return;
    				}
    				data=data.data;
    				
    				if (data.success == false){
    					if (data.message.length > 0){
    						if (this.gks_showerrors) gks_myalert('error:' + $.base64.decode(data.message));
    						
    					} else {
    					  if (this.gks_showerrors) gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
    					}
    					return;
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
  				    for (i = 0; i < data.out.length; i++) {
  				      if (data.out[i].type=='val') {
  				        $(data.out[i].id).val($.base64.decode(data.out[i].data));
  				      } else if (data.out[i].type=='html') {
  				        $(data.out[i].id).html($.base64.decode(data.out[i].data));
  				      }
  				    }
  				  }
  				  
  				  if (data.ids_hide) {
  				    for (i = 0; i < data.ids_hide.length; i++) {
				        $(data.ids_hide[i]).hide();
  				    }
  				  }
  				  if (data.ids_show) {
  				    for (i = 0; i < data.ids_show.length; i++) {
				        $(data.ids_show[i]).show();
  				    }
  				  }  				  
  				  
  				  row_id='';
				    if (this.mycmd=='rowdelete') {
				      row_id=this.myindex + '_' + this.myproduct_id + '_' + this.myobject;
				    }
				    if (data.also_delete) {
				      row_id=data.also_delete;
				    }
				    
				    
    			  if (data.allwarnings) {
  				    $(".tpwarning").tooltipster('destroy');
  				    $('[id^=warning_span_]').each(function(index) {
  				      $(this).html('');
  				    });
    			    allwarnings=JSON.parse($.base64.decode(data.allwarnings));
    			    //console.log(allwarnings);
  				    for (i = 0; i < allwarnings.length; i++) {
    			      //console.log(allwarnings[i]);
    			      myhtml= '<span class="tpwarning" title="' + allwarnings[i].tp + '" style="text-align:left">' + allwarnings[i].html + '</span>';
    			      //console.log(allwarnings[i].id);
    			      //console.log(myhtml);
    			      $('#' + allwarnings[i].id).append(myhtml);
    			    }
  				    $(".tpwarning").tooltipster({theme: 'tooltipster-noir'});
    			  }	
    			  
    			  				    
				    if (row_id != '') {
				      $('#row_root_' + row_id).hide(800, function() {
                $(this).remove();
                var mycount=0;
                var myclass='';
                $('#table-basket > tbody > tr').each(function( index ) {
                  myid= $(this).attr('id');
                  //console.log(myid);
                  if (myid.substring(0,9) == 'row_root_') {
                    mycount++;
                    if (mycount % 2) {
                      myclass='odd';
                    } else {
                      myclass='even';
                    }
                    $(this).attr('class',myclass);
                    $(this).children('.row_aa').html(mycount);
                    
                  } else {
                    $(this).attr('class',myclass);
                  }
                });
                if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
                gks_myscroll();                
              });
              
              
				      $('#row_extra_' + row_id).hide(800, function() {
                $(this).remove();
                if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
                gks_myscroll();                
              });
				    }
				    
				    //console.log(this.mycmd);
				    if (this.mycmd == 'filedelete') {
				      file_id = this.myindex + '_' + this.myproduct_id + '_' + this.myobject + '_' + this.myfile;
				      //console.log(file_id);
				      $('#' + file_id).hide(200, function() {
                $(this).remove();
                if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
                gks_myscroll(); 
              });
				      
				    }
    				if (this.mycmd == 'couponadd' || this.mycmd == 'coupondelete') {
    				  $(".tooltipster").tooltipster({theme: 'tooltipster-noir'});
    				  $(".coupon_delete").click(coupon_delete_click);
    				}
    								

            if (data && data.rsrv_count==0) {
              $('#gks_rsrv_h0').show();
              $('#gks_rsrv_h1').hide();
              $('#gks_total_div').hide();
            }
            if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
            gks_myscroll();  
    				
    				if (this.myreloadafter) {
    				  $('body').addClass('gks_myloading');
              window.location.reload();
              return;
            }
            
    				if (data.success){
    				  if (this.gks_gonext) {
    				    //$('body').removeClass('gks_myloading'); 
    				    //window.location.href = from_php_gks_api_page_checkout + from_php_gks_set_lang_url;
    				    gks_goto_chekcout_or_cart();
    				  }
            } 

            $('.tooltipster_basket').tooltipster({theme: 'tooltipster-noir'});
            
    			}
				}
				
			});
    
  }
  
  $('#coupon_use').click(function(event){	
    mycoupon=$('#input_coupon').val();
    if (mycoupon.trim() == '') {
      gks_myalert('error:' + from_php_lang_Typeyourcouponfirstinthetextbox);
      return; 
    }
    basket_edit(true,true,false, 'couponadd', 0, 0, 0, mycoupon, 0, false);
    
  });
  
  coupon_delete_click = function(event){	
    mycoupon=$(this).attr('data-coupon');
    basket_edit(true,true,false, 'coupondelete', 0, 0, 0, mycoupon, 0, false);
  }
  
  $('.coupon_delete').click(coupon_delete_click);
  $('.tooltipster').tooltipster({theme: 'tooltipster-noir'});
  
  $('#gks_search').click(function(event){
    $('body').removeClass('gks_myloading');
    window.location.href=from_php_gks_api_hotel_page_reservation_search + from_php_gks_set_lang_url;
    
  });
  
  $('#gks_update').click(function(event){
    rooms_basket_edit(true,true,false,false);
  });  
  
  $('#gks_checkout').click(function(event){

    if (products_posotita_val<=0) {
      gks_myalert('error:' + from_php_lang_Yourcartisempty); 
      return false;
    }
    
    mywarninglen= $(".tpwarning:visible").length + $('.gks_fa-exclamation-triangle:visible').length;
    if (mywarninglen > 0) {
      
      
      gks_myconfirm(from_php_lang_Therearewarnings.replace('[1]', mywarninglen),'tpwarning');
      return false;
    }
    rooms_basket_edit(true,true,true,false);
    

    //window.location.href=from_php_gks_api_page_checkout;
  });
  
  //header_basket_show
  //header_basket_checkout
  //header_basket_pay
  //header_basket_confirm
  
  
  $(".tpwarning").tooltipster({theme: 'tooltipster-noir'});
  
  var myunchecked=[];

  $('#test_add_basket').click(function() {
    mybasketdata='guid=&ids=&pid_124=3';
    add_test_basket(mybasketdata);
  });
  $('#test_add_basket2').click(function() {
    
    mybasketdata='guid=&ids=&pid_125=4';
    add_test_basket(mybasketdata);
    
  });
  
  function add_test_basket(mybasketdata) {

    photoSelectedGUID='';
    

    $('body').addClass('gks_myloading');
    $.ajax({
			url: '/my/basket-add.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: mybasketdata,
			sendguid: photoSelectedGUID,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('body').removeClass('gks_myloading');
				gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass('gks_myloading');
				//console.log(data);
				if (!data) {
					
					gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
				} else {
				  
				  
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
              					  
					if (data.success == true) {
					  if (data.myhtml) {
					    for (i = 0; i < data.myhtml.length; i++) {
					      $('#' + data.myhtml[i].id).html($.base64.decode(data.myhtml[i].html))
					      //console.log($.base64.decode(data.myhtml[i].html));
					    }
					  }
					  for (i = 0; i < myunchecked.length; i++) {
              $('#' + myunchecked[i]).prop('checked', false);
					  }
						gks_myalert('ok:' + from_php_lang_Successfullyaddedtocart);
						$('.tooltipster_basket').tooltipster({theme: 'tooltipster-noir'});
						
					} else {

              						  
						gks_myalert('error:' + $.base64.decode(data.message));
					}
					
				}
			}
			
		});   
    
  }
  
  $('.gks_input_rnum_adults').change(function() {
    data_room_max_visitors=parseInt($(this).attr('data_room_max_visitors'));
    if (isNaN(data_room_max_visitors)) data_room_max_visitors=0;
    if (data_room_max_visitors<=0) return;
    cur_val=parseInt($(this).val());
    if (isNaN(cur_val)) cur_val=0;
    rest = data_room_max_visitors-cur_val;
    
    data_rsrv_aa= $(this).attr('data_rsrv_aa');
    data_roomtype_aa= $(this).attr('data_roomtype_aa');
    data_room_aa= $(this).attr('data_room_aa');
    
    other_elem=$('.gks_input_rnum_childs[data_rsrv_aa='+data_rsrv_aa+'][data_roomtype_aa='+data_roomtype_aa+'][data_room_aa='+data_room_aa+']');
    other_val=parseInt(other_elem.val());
    if (isNaN(other_val)) return;
    if (other_val>rest) other_elem.val(rest);
    
    gks_input_rnum_calc($(this));
    
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_adults = cur_val;
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_childs = parseInt(other_elem.val());
    
    rooms_basket_edit(false,false,false,false);
  });
  $('.gks_input_rnum_childs').change(function() {
    data_room_max_visitors=parseInt($(this).attr('data_room_max_visitors'));
    if (isNaN(data_room_max_visitors)) data_room_max_visitors=0;
    if (data_room_max_visitors<=0) return;
    cur_val=parseInt($(this).val());
    if (isNaN(cur_val)) cur_val=0;
    rest = data_room_max_visitors-cur_val;
    
    data_rsrv_aa= $(this).attr('data_rsrv_aa');
    data_roomtype_aa= $(this).attr('data_roomtype_aa');
    data_room_aa= $(this).attr('data_room_aa');
    
    other_elem=$('.gks_input_rnum_adults[data_rsrv_aa='+data_rsrv_aa+'][data_roomtype_aa='+data_roomtype_aa+'][data_room_aa='+data_room_aa+']');
    other_val=parseInt(other_elem.val());
    if (isNaN(other_val)) return;
    if (other_val>rest) other_elem.val(rest);

    gks_input_rnum_calc($(this));

    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_adults = parseInt(other_elem.val());
    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_childs = cur_val;
    
    rooms_basket_edit(false,false,false,false); //showloading,showerrors,gonext
  });
    
  function gks_input_rnum_calc(elem) {
    var data_rsrv_aa=parseInt(elem.attr('data_rsrv_aa'));
    if (isNaN(data_rsrv_aa)) data_rsrv_aa=-1;
    if (data_rsrv_aa<0) return;
    var gks_total_visitors_adults=0;
    var gks_total_visitors_childs=0;
    var gks_rsrv_total_visitors_adults=0;
    var gks_rsrv_total_visitors_childs=0;
    
    $('.gks_input_rnum_adults').each(function() {
      data_rsrv_aa_this=parseInt($(this).attr('data_rsrv_aa'));
      if (isNaN(data_rsrv_aa_this)) data_rsrv_aa_this=-1;
      val=parseInt($(this).val());
      if (isNaN(val)) val=0;
      if (data_rsrv_aa_this>=0) {
        if (data_rsrv_aa_this==data_rsrv_aa) gks_rsrv_total_visitors_adults+=val;
        gks_total_visitors_adults+=val;
      }
    });
    $('.gks_input_rnum_childs').each(function() {
      data_rsrv_aa_this=parseInt($(this).attr('data_rsrv_aa'));
      if (isNaN(data_rsrv_aa_this)) data_rsrv_aa_this=-1;
      val=parseInt($(this).val());
      if (isNaN(val)) val=0;
      if (data_rsrv_aa_this>=0) {
        if (data_rsrv_aa_this==data_rsrv_aa) gks_rsrv_total_visitors_childs+=val;
        gks_total_visitors_childs+=val;
      }
    });
        
    $('.gks_rsrv_total_visitors_adults[data_rsrv_aa=' + data_rsrv_aa + ']').html(gks_rsrv_total_visitors_adults);
    $('.gks_rsrv_total_visitors_childs[data_rsrv_aa=' + data_rsrv_aa + ']').html(gks_rsrv_total_visitors_childs);
    
    $('#gks_total_visitors_adults').html(gks_total_visitors_adults);
    $('#gks_total_visitors_childs').html(gks_total_visitors_childs);
    
    //console.log('fff');
  }
  
  
  function gks_goto_chekcout_or_cart() {

    
    $('body').addClass('gks_myloading'); 
    mydatasend='';
    mydatasend+='&action=gks_hotel_gks_add_to_woo_exec_my_action';
    mydatasend+='&url_lang=' + from_php_url_lang;
    mydatasend+='&ui_lang=' + from_php_ui_lang;
    mydatasend+='&gks_erp_cookie_id=' + from_php_gks_erp_cookie_id;
    mydatasend+='&order_id=11';

		
    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        cache: false,
        dataType: "json",
        data:mydatasend,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          $('body').removeClass('gks_myloading'); 
				  gks_myalert('error:' + jqXHR.responseText);
			  },
        success: function(data) {
          //console.log(data);
          
          $('body').removeClass('gks_myloading'); 
          if (!data) {
  					gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
  				} else {
            if (data.success == true) {
              data=data.data;
              if (data.success == true) {
                window.location.href = from_php_gks_api_page_checkout + from_php_gks_set_lang_url;
                
              } else {
                gks_myalert('error:' + $.base64.decode(data.message));
              }
            } else {
              gks_myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });
    
  }
  
});
