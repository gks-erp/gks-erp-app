/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

const gks_datetimepicker_defaults={
  scrollMonth:false,
  scrollTime:false,
  scrollInput:false,
};

var gks_roomsarray = [];
var gks_adults_count=0;
var gks_childs_count=0;
var gks_rooms_count=0;
var gks_check_in_round='';
var gks_check_out_round='';
var gks_num_days=0;

var gks_calc_price=0;
var gks_calc_rooms=0;
var gks_calc_persons=0;
  
var gks_check1=false;
var gks_check2=false;

var gks_selected_rooms = [];
var hasfreerooms = false;
var gks_rooms_selection = [];


var gks_dialog_message;
var gks_dialog_confirm;
var gks_rooms_ages_index=[];

jQuery3(document).ready(function($) {
  
  $.base64.utf8encode = true;
  $.base64.utf8decode = true;
  $.datetimepicker.setLocale(from_php_datetimepicker_locale);




  function gks_myresize() {
    var gks_content_width=$(window).width();
   //var gks_content_width = $('#gks_content').width();
    if (gks_content_width>=768) {
      $('#gks_rsrv_s').css('width','30%').css('float','left');
      $('#gks_rsrv_r').css('width','calc(70% - 30px)').css('float','left');
      $('.gks_label_search').each(function() {
        $(this).css('text-align','left');  
      });
    } else {
      $('#gks_rsrv_s').css('width','calc(100% - 20px)').css('float','none');
      $('#gks_rsrv_r').css('width','calc(100% - 20px)').css('float','none');
      $('.gks_label_search').each(function() {
        $(this).css('text-align','center');  
      });
      
    }
    if (gks_content_width>=992) {
      $('.gks_rsrv_rtc1').each(function() {
        $(this).css('width','30%');  
      });
      $('.gks_rsrv_rtc2').each(function() {
        $(this).css('width','30%');  
      });
      $('.gks_rsrv_rtc3').each(function() {
        $(this).css('width','40%');  
      });
    } else {
      $('.gks_rsrv_rtc1').each(function() {
        $(this).css('width','100%');  
      });
      $('.gks_rsrv_rtc2').each(function() {
        $(this).css('width','100%');  
      });
      $('.gks_rsrv_rtc3').each(function() {
        $(this).css('width','100%');  
      });
    }
    tempe = $('.gks_rsrv_rtc1');
    
    if (tempe.length>=1) {
      var newimgmain=1;
      $('.gks_rsrv_rtc1').each(function() {
        newimgmain=$(this).width();return;
      });
      //console.log('newimgmain');
      //console.log(newimgmain);
      var newimgmain1=Math.floor(newimgmain*9/16);
      //console.log('newimgmain1');
      //console.log(newimgmain1);
      $('.gks_rsrv_img_main').each(function() {
        $(this).css('height',newimgmain1+'px');  
      });
      var newimgmain2=Math.floor(newimgmain/5);
      var newimgmain3=newimgmain2;
      //console.log('newimgmain2');
      //console.log(newimgmain2);
      if (newimgmain2 < 50) {newimgmain2=Math.floor(newimgmain/4);newimgmain3=newimgmain2;}
      if (newimgmain2 < 50) {newimgmain2=Math.floor(newimgmain/3);newimgmain3=newimgmain2;}
      if (newimgmain2 < 50) {newimgmain2=Math.floor(newimgmain/2);newimgmain3=Math.floor(newimgmain2*9/16);}
      if (newimgmain2 < 50) {newimgmain2=Math.floor(newimgmain);  newimgmain3=Math.floor(newimgmain2*9/16);}
      newimgmain=Math.floor(newimgmain);
      

      $('.gks_rsrv_img').each(function() {
        $(this).css('width',newimgmain2+'px').css('height',newimgmain3+'px');  
      });      
      //console.log('newimgmain2');
      //console.log(newimgmain2);
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
      gks_rsrv_f_height+= 24 + 10; // + 10 + 4;// + 10; //apo to paddding + 10 gia safe/shadow
      
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
  

  
  $('#gks_check_in' ).datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,
    minDate: from_php_minDate,
    maxDate: (from_php_max_reservation_date_time==0 ? null : from_php_max_reservation_date_time_date1),
    onChangeDateTime: function(dp,$input) {
      ii=$('#gks_check_in' ).datetimepicker('getValue');
      ii = new Date(ii.getFullYear(),ii.getMonth(),ii.getDate());
      ii.setDate(ii.getDate() + 1);
      ii_min=ii.getFullYear() + '-' + (ii.getMonth() + 1) + '-' + ii.getDate();
      $('#gks_check_out').datetimepicker({minDate: ii_min});
  
      oo=$('#gks_check_out').datetimepicker('getValue');
      if (oo == null || ii.getTime() >= oo.getTime()) {
        ii_min=gks_pad(ii.getDate(),2) + '/' + pad((ii.getMonth() + 1),2) + '/' + ii.getFullYear() ;
        $('#gks_check_out').datetimepicker({value: ii_min});
      }
      calc_days();
    }
  }));
  $('#gks_check_out').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,
    minDate: from_php_minDate,
    maxDate: (from_php_max_reservation_date_time==0 ? null : from_php_max_reservation_date_time_date2),
    onChangeDateTime: function(dp,$input) {
      calc_days();
    }
  }));

  var old_din= $('#gks_check_in'). datetimepicker('getValue');
  var old_dout=$('#gks_check_out').datetimepicker('getValue');
  old_din = new Date(old_din.getFullYear(),old_din.getMonth(),old_din.getDate(),from_php_defs_inh,0,0);
  old_dout = new Date(old_dout.getFullYear(),old_dout.getMonth(),old_dout.getDate(),from_php_defs_outh,0,0);
  

  function calc_days() {
    var din = $('#gks_check_in').val();
    if (din=='' || din=='__/__/____')  {
      $('#gks_num_days').html('0'); return;    
    }
    var dout = $('#gks_check_out').val();
    if (dout=='' || dout=='__/__/____')  {
      $('#gks_num_days').html('0'); return;    
    }
    din  = $('#gks_check_in'). datetimepicker('getValue');
    dout = $('#gks_check_out').datetimepicker('getValue');
    din = new Date(din.getFullYear(),din.getMonth(),din.getDate(),from_php_defs_inh,0,0);
    dout = new Date(dout.getFullYear(),dout.getMonth(),dout.getDate(),from_php_defs_outh,0,0);
    if (din.getTime() == old_din.getTime() && dout.getTime() == old_dout.getTime()) {
      return;
    }
    old_din = din;
    old_dout= dout;
    if (dout.getTime() <= din.getTime()) {
      $('#gks_num_days').html('0'); return;
    }
    if (din.getHours() < from_php_defs_inh) {
      din2 = new Date(din.getFullYear(),din.getMonth(),din.getDate() - 1 ,0,0,0);
    } else {
      din2 = new Date(din.getFullYear(),din.getMonth(),din.getDate(),0,0,0);
    }
    if (dout.getHours() > from_php_defs_outh) {
      dout2 = new Date(dout.getFullYear(),dout.getMonth(),dout.getDate() + 1,0,0,0);
    } else {
      dout2 = new Date(dout.getFullYear(),dout.getMonth(),dout.getDate(),0,0,0);
    }
    if (dout2.getTime() <= din2.getTime()) {
      $('#gks_num_days').html('0'); return;
    }
    var timeDiff = Math.abs(dout2.getTime() - din2.getTime());
    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
    $('#gks_num_days').html(diffDays);
  }
  
  
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
    buttons: [
      {
        id: "gks_dialog_confirm_ok",
        html: "<i class='gks_fa gks_fa-pen-square'></i> OK",
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
            default:
              myalert('error: dialog_confirm function_ok');
              break;
          }
        },
      },
      {
        id: "gks_dialog_confirm_cancel",
        html: "<i class='gks_fa gks_fa-window-close'></i> " + from_php_textcancel,
        click: function() {
          $(this).dialog('close');
          gks_check1=false;
          gks_check2=false;
        }
      },      
    ]

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
  
  $('#gks_mysearch').click(function() {
    gks_mysearch();
  });
  

  var gks_wpadminbar_zindex=-1;
  if ($('#wpadminbar').length>0) gks_wpadminbar_zindex=$('#wpadminbar').css('z-index');
  //console.log('gks_wpadminbar_zindex',gks_wpadminbar_zindex);
  
  
  function gks_mysearch() {  

    var child_age_error='';
    childs_and_ages=[];
    $('.childs_ages_list_select').each(function(index) {
      val=parseInt($(this).val());
      if (isNaN(val)) val=-1;
      mytxt= $(this).find('option:selected').text();
      if (mytxt=='') mytxt=from_php_text14.replaceAll('[1]',(index + 1).toString()); 
      childs_and_ages.push({age:val, txt:mytxt});
      if (val<0) {
        child_age_error+=from_php_text1.replaceAll('[1]',(index + 1).toString()) + '<br>';
      }
    });
    if (child_age_error!='') {
      gks_myalert('error:' + child_age_error);
      return;
    }
    
        
    //console.log('childs_and_ages',childs_and_ages);
    
    //console.log('gks_mysearch');
    datasend='';
    datasend+='&command=hotel_reservation_search';
    datasend+='&ui_lang='  + encodeURIComponent(from_php_ui_lang);
    datasend+='&gks_check_in='  + encodeURIComponent($("#gks_check_in").val().trim());
    datasend+='&gks_check_out='  + encodeURIComponent($("#gks_check_out").val().trim());
    datasend+='&gks_adults_count='  + encodeURIComponent($("#gks_adults_count").val().trim());
    if ($("#gks_childs_count").length>0) datasend+='&gks_childs_count='  + encodeURIComponent($("#gks_childs_count").val().trim());
    datasend+='&gks_rooms_count='  + encodeURIComponent($("#gks_rooms_count").val().trim());
    datasend+='&childs_and_ages=' + encodeURIComponent(JSON.stringify(childs_and_ages));
    

          
    //console.log(datasend);
    $('#gks_rsrv_f').hide();
    
    $('body').addClass('gks_myloading'); 
    $.ajax({
			url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('gks_myloading');
				gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('body').removeClass('gks_myloading');
				if (!data) {
					gks_myalert('error:' + from_php_text2);
				} else {
					if (data.success == true) {
					  data=data.data;
					  
					  if (data.success == true) {
  					//gks_myalert('ok:' + 'OK');
  					//console.log(data);
    					$('#gks_search_results').html($.base64.decode(data.html));
    					
  
    					gks_roomsarray = data.gks_roomsarray;
              gks_adults_count=data.gks_adults_count;
              gks_childs_count=data.gks_childs_count;
              gks_rooms_count=data.gks_rooms_count;
    					
    					gks_check_in_round = data.gks_check_in_round;
    					gks_check_out_round = data.gks_check_out_round;
    					gks_num_days = data.gks_num_days;
    					gks_rooms_selection = data.gks_rooms_selection;
    					
    					
    					
    					gks_calc_price=0;
              gks_calc_rooms=0;
              gks_calc_persons=0;
    					
              $('#gks_pbar_persons').progressbar( "option", "value", 0);
    					$('#gks_total_persons').html('0/' + (gks_adults_count + gks_childs_count));
              $('#gks_pbar_rooms').progressbar( "option", "value", 0);
    					$('#gks_total_rooms').html('0/' + gks_rooms_count);
    					$('#gks_total_price').html(
    					(from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW != 'after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL + ' ' : '') +
              gks_formatMoney(0, from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
              (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW == 'after' ? ' ' + from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '')
              );
      
    					$('.gks_rsrv_select').change(gks_rsrv_select_change);
    					$('.gks_jqtp').tooltipster({theme: 'tooltipster-noir'});
    					$('.gks_amenity2ml').click(gks_amenity2ml);
    					$('.gks_amenity2ll').click(gks_amenity2ll);
  
    					if (data.hasfreerooms) {
    					  hasfreerooms=true;
    					  //$('#gks_rsrv_f').show();
    					  $('#gks_book').css('background-color','');
    					  $('#gks_book').css('color','');
    					  $('#gks_book').css('cursor','');
    					  
    					  //console.log('gks_rooms_selection');
    					  //console.log(gks_rooms_selection);
    					  
    					  for(var data_id in gks_rooms_selection) {
    					    $('#gks_rsrv_select_id_' + data_id).val(gks_rooms_selection[data_id]);
    					  }
    					  //gks_rsrv_select_exec(room_type_id, aa);
    					  gks_rsrv_totals();
    					  //if (gks_rooms_selection.length>0) {
      					//  for (i=0;i<gks_rooms_selection.length;i++) {
      					//    $('#gks_rsrv_select_id_' + gks_rooms_selection[i].id).val(gks_rooms_selection[i].cc);
      					//  }
    					    
    					  //}
    					  
    					} else {
    					  hasfreerooms=false;
    					  //$('#gks_rsrv_f').hide();
    					  $('#gks_book').css('background-color','#888888');
    					  $('#gks_book').css('color','#999999');
    					  $('#gks_book').css('cursor','default');
    					}
    					
    					$('.gks_input_rnum_adults').change(gks_input_rnum_adults_change);
              $('.gks_input_rnum_childs').change(gks_input_rnum_childs_change);
    					$('.gks_input_child_age').change(gks_input_child_age_change);
    					gks_input_child_age_diable_option();
              $('.gks_input_rnum_child_kounies').change(gks_input_rnum_child_kounies_change);
              $('.gks_input_rnum_extra_beds').change(gks_input_rnum_extra_beds_change);
    					
    					
    					gks_check1=false;
              gks_check2=false;
              $('#gks_rsrv_f').fadeIn(1000); //show(1000);
              
              

              
              
              
              $(".lightgallery_room_type").lightGallery({
              	selector: '.lightgallery_photo',
              	thumbnail:true,
              	hideBarsDelay:1000,
              });
               
              Array.from(document.getElementsByClassName('lightgallery_room_type')).forEach(
                function(element, index, array) {
                  //console.log(element);
                  
                  $(element).on('onBeforeOpen.lg',function(event){
                    //console.log('onBeforeOpen');
                    if (gks_wpadminbar_zindex>0) $('#wpadminbar').css('z-index', 99999);
                  });
                  $(element).on('onCloseAfter.lg',function(event){
                    //console.log('onCloseAfter');
                    if (gks_wpadminbar_zindex>0) $('#wpadminbar').css('z-index', gks_wpadminbar_zindex);
                  });
                  
                }
              );               

           
    					gks_myresize();
              if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
              gks_myscroll();
            } else {
              gks_myalert('error:' + $.base64.decode(data.message));
            }
              					
					} else {
						gks_myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;     
  }
  
  function gks_amenity2ml(event) {
    
	  myelem = $(event.target);
	  myelem_id=myelem.attr('data-id'); 
	  $('.gks_amenity2m[data-id=' + myelem_id + ']').hide();
	  $('.gks_amenity2t[data-id=' + myelem_id + ']').show(300,function() {
	    if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
      gks_myscroll();
    });
	  $('.gks_amenity2l[data-id=' + myelem_id + ']').show();
	  
	  //console.log('gks_amenity2ml');
	  //console.log(myelem_id);
	      
  }
  function gks_amenity2ll(event) {
    
	  myelem = $(event.target);
	  myelem_id=myelem.attr('data-id'); 
	  $('.gks_amenity2m[data-id=' + myelem_id + ']').show();
	  $('.gks_amenity2t[data-id=' + myelem_id + ']').hide(300,function() {
	    if (typeof jQuery().getNiceScroll !=='undefined') jQuery("html").getNiceScroll().resize();
      gks_myscroll();
    });
	  $('.gks_amenity2l[data-id=' + myelem_id + ']').hide();
	  	  
	  //console.log('gks_amenity2ll');
	  //console.log(myelem_id);
  }

  $('#gks_pbar_rooms').progressbar({value: 0});
  gks_pbar_rooms = $('#gks_pbar_rooms');
  gks_pbar_roomsValue = gks_pbar_rooms.find('.ui-progressbar-value');
  gks_pbar_roomsValue.css({'background': '#00ff00'});
  
  $('#gks_pbar_persons').progressbar({value: 0});
  gks_pbar_persons = $('#gks_pbar_persons');
  gks_pbar_personsValue = gks_pbar_persons.find('.ui-progressbar-value');
  gks_pbar_personsValue.css({'background': '#d2eeff'});
  

  
  function gks_rsrv_select_change (event) {
	  myelem = $(event.target);
	  id_hotel_room_type=parseInt(myelem.attr('data-id')); 
	  if (isNaN(id_hotel_room_type)) id_hotel_room_type=0;
	  if (id_hotel_room_type<=0) return;
	  
	  new_trs=parseInt(myelem.val());
	  if (isNaN(new_trs)) new_trs=0;
	  
	  
    //console.log(id_hotel_room_type);
    //console.log(new_trs);
    
    
    exist_trs=$('#rooms_details_table_' + id_hotel_room_type + ' tr').length;
    
//    this_room_type_sel=[];
//    $('#rooms_details_table_' + id_hotel_room_type + ' tr .rooms_details_table_td').each(function() {
//      //$(this).remove();
//      adults= $(this).find('.gks_input_rnum_adults').val();
//      childs= $(this).find('.gks_input_rnum_childs').val();
//      this_room_type_sel.push({adults:adults,childs:childs});
//    });
    //console.log(this_room_type_sel);
    
    room_type_visitors_max=parseInt($(this).attr('data-max'));
    if (isNaN(room_type_visitors_max)) room_type_visitors_max=0;
    room_type_visitors=parseInt($(this).attr('data-adults'));
    if (isNaN(room_type_visitors)) room_type_visitors=0;
    room_type_child_kounies=parseInt($(this).attr('data-child_kounies'));
    if (isNaN(room_type_child_kounies)) room_type_child_kounies=0;
    room_type_extra_beds=parseInt($(this).attr('data-extra_beds'));
    if (isNaN(room_type_extra_beds)) room_type_extra_beds=0;
    
    
    
    var child_age_error='';
    var childs_and_ages_temp=[];
    $('.childs_ages_list_select').each(function(index) {
      val=parseInt($(this).val());
      if (isNaN(val)) val=-1;
      mytxt= $(this).find('option:selected').text();
      if (mytxt=='') mytxt=from_php_text14.replaceAll('[1]',(index + 1).toString()); 
      childs_and_ages_temp.push({age:val, txt:mytxt});
      if (val<0) {
        child_age_error+=from_php_text1.replaceAll('[1]',(index + 1).toString()) + '<br>';
      }
    });
    if (child_age_error!='') {
      gks_myalert('error:' + child_age_error);
      return;
    }
        
    if (new_trs > exist_trs) {
      for (ri=exist_trs+1; ri<=new_trs; ri++) {
 
        rt_html='';
        rt_html+='<tr class="">';
        rt_html+='<td class="rooms_details_table_td">';
        rt_html+='<span class="rooms_details_aa">#' + ri + '</span> ';
        
        rt_html+='<select ' +
            'class="gks_input_select gks_input_rnum_adults" ' +
            'data_room_aa="' + ri + '" ' +
            'data_room_type_id="' + id_hotel_room_type + '" ' +
            'data_room_max_visitors="' + room_type_visitors_max + '" ' +
            'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
        
        
        max_selectors=room_type_visitors;
        if (max_selectors>gks_adults_count) max_selectors=gks_adults_count;
        
        for(i=1;i<=max_selectors;i++) {
          rt_html+='<option value="' + i + '" ';
          rt_html+='>' + i + '</option>';
        }
        rt_html+='</select>x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
        
        if (childs_and_ages.length>0) {
          rt_html+='<select ' +
              'class="gks_input_select gks_input_rnum_childs" ' +
              'data_room_aa="' + ri + '" ' +
              'data_room_type_id="' + id_hotel_room_type + '" ' +
              'data_room_max_visitors="' + room_type_visitors_max + '" ' +
              'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
          
          
          max_selectors=room_type_visitors_max;
          if (max_selectors>childs_and_ages.length) max_selectors=childs_and_ages.length;
          
          for(i=1;i<=max_selectors;i++) {
            rt_html+='<option value="' + i + '"';
            rt_html+='>' + i + '</option>';
          }
          rt_html+='</select>x<i class="gks_fa gks_fa-child gks_rsrv_childicon" style="font-size:70%;"></i> ';  
          
          
          
          
          rt_html+='<div class="div_child_selectors" ' +
          'data_room_aa="' + ri + '" ' +
          'data_room_type_id="' + id_hotel_room_type + '" ' +
          '>' + from_php_text8 + ': '+   
          '<span class="child_selectors" data_room_aa="' + ri + '" data_room_type_id="' + id_hotel_room_type + '"></span>';
          
          max_selectors=childs_and_ages.length;
          if (max_selectors>room_type_visitors_max) max_selectors>room_type_visitors_max;
          //if (max_selectors>room_type_visitors_max) max_selectors>room_type_visitors_max;
          
          for(i=1;i<=max_selectors.length;i++) {
              
            rt_html+='<select ' +
                'class="gks_input_select gks_input_child_age" ' +
                'data_room_aa="' + ri + '" ' +
                'data_room_type_id="' + id_hotel_room_type + '" ' +
                'data_room_i="' + i + '" ' +
                'style="width:unset !important;padding: 4px 0px !important;"><option value=0></option>';
            

            for (ca=0;ca<childs_and_ages.length; ca++) {
               rt_html+='<option value=' + (ca+1).toString() +
               '>' + childs_and_ages[ca].age + ' ' + from_php_text13 + '</option>';
            }
            
            rt_html+='</select>';  
          }
          rt_html+='</div>';
        

          
          if (room_type_child_kounies > 0 && from_php_hotel_child_kounies_array.enable) {
            childs_under_6=0;
            for (ca=0;ca<childs_and_ages.length; ca++) {
              if (childs_and_ages[ca].age <= from_php_hotel_child_kounies_array.to) {
                childs_under_6++;
              }
            }
            if (childs_under_6>0) {
              rt_html+='<div class="div_gks_input_rnum_child_kounies" ' +
              'data_room_aa="' + ri + '" ' +
              'data_room_type_id="' + id_hotel_room_type + '" ' +
              '>' + from_php_text9 + ': ' +
              '<select ' +
              'class="gks_input_select gks_input_rnum_child_kounies" ' +
              'data_room_aa="' + ri + '" ' +
              'data_room_type_id="' + id_hotel_room_type + '" ' +
              'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
              max_selectors=room_type_child_kounies;
              if (max_selectors > childs_under_6) max_selectors=childs_under_6;
              for(i=1;i<=max_selectors;i++) {
                rt_html+='<option value="' + i + '">' + i + '</option>';
              }
              rt_html+='</select></div>';
              //$rt_html.=print_r($childs_and_ages,true);
            }
          }
        }

        if (room_type_extra_beds > 0 && from_php_hotel_extra_beds_array.enabled) {
          max_support_age=0;
          for(eb=0; eb<from_php_hotel_extra_beds_array.beds.length; eb++) {
            if (from_php_hotel_extra_beds_array.beds[eb].to > max_support_age) max_support_age=from_php_hotel_extra_beds_array.beds[eb].to;
          } 
          visitors_age_is_supported=0;
          for (ca=0;ca<childs_and_ages.length; ca++) {
            if (childs_and_ages[ca].age <= max_support_age) {
              visitors_age_is_supported++;
            }
          }
          if (max_support_age==18) { //ipostirizei kai adults
            visitors_age_is_supported+=gks_adults_count;
          }
          
          if (visitors_age_is_supported>0) {
            rt_html+='<div class="div_gks_input_rnum_extra_beds" ' +
            'data_room_aa="' + ri + '" ' +
            'data_room_type_id="' + id_hotel_room_type + '" ' +
            '>' + from_php_text10 + ': ' +
            '<select ' +
            'class="gks_input_select gks_input_rnum_extra_beds" ' +
            'data_room_aa="' + ri + '" ' +
            'data_room_type_id="' + id_hotel_room_type + '" ' +
            'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
            max_selectors=room_type_extra_beds;
            if (max_selectors > visitors_age_is_supported) max_selectors=visitors_age_is_supported;
            for(i=1;i<=max_selectors;i++) {
              rt_html+='<option value="' + i + '">' + i + '</option>';
            }
            rt_html+='</select></div>';
            //$rt_html.=print_r($childs_and_ages,true);
          }
        }              


  
        rt_html+='<div class="div_room_type_total_price" ' +
        'data_room_aa="' + ri + '" ' +
        'data_room_type_id="' + id_hotel_room_type + '" ' +
        '>' + from_php_text11 + ': <span class="room_type_total_price" ' +
        'data_room_aa="' + ri + '" ' +
        'data_room_type_id="' + id_hotel_room_type + '" ' +
        'data-val="0">' +
        '</span></div>';

        
        rt_html+='</td>'; 
        rt_html+='</tr>';
        if (exist_trs==0) {
          $('#rooms_details_table_' + id_hotel_room_type + ' tbody').append(rt_html);
        } else {
          $('#rooms_details_table_' + id_hotel_room_type + ' tr:last').after(rt_html);
        }
        exist_trs++;
        
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last .gks_input_rnum_adults').change(gks_input_rnum_adults_change);
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last .gks_input_rnum_childs').change(gks_input_rnum_childs_change);
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last .gks_input_child_age').change(gks_input_child_age_change);
        gks_input_child_age_diable_option();
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last .gks_input_rnum_child_kounies').change(gks_input_rnum_child_kounies_change);
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last .gks_input_rnum_extra_beds').change(gks_input_rnum_extra_beds_change);
        
        
      }
      $('#rooms_details_' + id_hotel_room_type).show();
    } else if (new_trs < exist_trs) {
      for (i=new_trs+1; i<=exist_trs; i++) {
        $('#rooms_details_table_' + id_hotel_room_type + ' tr:last').remove();
      }
      if (new_trs<=0) $('#rooms_details_' + id_hotel_room_type).hide();
    }
    
    $('#warning_msg').fadeOut(300, function() {gks_myscroll();});
    gks_myscroll();
    
    //gks_rsrv_select_exec(id_hotel_room_type, aa);
    gks_rsrv_totals();
    
  }
  
  

  
  $('#gks_book').click(function() {
    if (hasfreerooms==false) return;
    gks_book();
  });
  

  
  function gks_book() {  
    
    
    if (gks_roomsarray.length==0) {
      gks_myalert('error:' + from_php_text3);
      return;      
    }
    if (gks_calc_rooms<=0) {
      gks_myalert('error:' + from_php_text4);
      return;
    }
    
    if (gks_check1 ==  false && gks_calc_rooms != gks_rooms_count) {
      mytext = from_php_text5.replaceAll('[1]',gks_rooms_count.toString()).replaceAll('[2]',gks_calc_rooms.toString());
      gks_myconfirm(mytext,'gks_book_c1','','','');
      return;
    }
    
    if (gks_check2 ==  false && gks_calc_persons != (gks_adults_count + gks_childs_count)) {
      mytext = from_php_text6.replaceAll('[1]',(gks_adults_count + gks_childs_count).toString()).replaceAll('[2]',gks_calc_persons.toString());
      gks_myconfirm(mytext,'gks_book_c2','','','');
      return;
    }

    return_error=gks_rsrv_totals();
    if (return_error!='') {
      //console.log('return_error',return_error);
      gks_myalert('error:' + return_error);
      return;
    }
        
    gks_book_set_persons();
    
  }
  function gks_book_set_persons() {
    gks_check1=false;
    gks_check2=false;
    
    
    //console.log('gks_book_set_persons');
    //console.log(gks_selected_rooms);
    gks_send_data={
      command:'hotel_reservation_basket_add',
      check_in: gks_check_in_round,
      check_out: gks_check_out_round,
      adults: gks_adults_count,
      childs: gks_childs_count,
      rooms: gks_rooms_count,
      calc_persons: gks_calc_persons,
      calc_rooms: gks_calc_rooms,
      num_days: gks_num_days,
      selrooms: gks_selected_rooms,
    };
    
    //console.log(gks_send_data);
    //return;
      
    datasend = JSON.stringify(gks_send_data);
    //console.log(gks_send_data);
    
    $('body').addClass('gks_myloading'); 
    $.ajax({
			url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('gks_myloading');
				gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
				  $('body').removeClass('gks_myloading');
					gks_myalert('error:' + from_php_text2);
				} else {
					if (data.success == true) {

					  data=data.data;
  					if (data.success == true) {
  					  $('body').removeClass('gks_myloading');
  					  
					    window.location.href = from_php_gks_api_hotel_page_reservation_basket;
  					} else {
  					  $('body').removeClass('gks_myloading');
  						gks_myalert('error:' + $.base64.decode(data.message));
  					}					  
					} else {
						$('body').removeClass('gks_myloading');
            gks_myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;     
  }
  
  
  $('#gks_childs_count').change(function() {
  
    var val_num_childs=parseInt($('#gks_childs_count').val());
    if (isNaN(val_num_childs)) val_num_childs=0;
    //console.log(val_num_childs);
    
    $('.childs_ages_list_div').each(function() {
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa>0 && aa>val_num_childs) $(this).remove(); 
    });
    
    max_exist=$('.childs_ages_list_div:last');
    if (max_exist.length==0) max_exist=0;
    else {
      max_exist=parseInt(max_exist.attr('data-aa'));
      if (isNaN(max_exist)) max_exist=0;
    }
    //console.log(max_exist);
    for (ic=max_exist + 1 ;ic<=val_num_childs; ic++) {
      myhtml='';


      myhtml+='<div class="childs_ages_list_div" data-aa="' + ic + '">' +
      
                '<div class="gks_label_search" style="text-align: left;">' +
                from_php_text12.replaceAll('[1]',ic.toString()) +
                ':</div>' +
                '<div style="text-align:center;">' + 
                '<select class="gks_input_select childs_ages_list_select" id="childs_ages_list_' + ic + '" style="width:100%;max-width:320px;">' + 
                 '<option value="-1"></option>';
                  hascheck=false;
                  for(ia=0; ia<=from_php_hotel_child_accept_max_age; ia++) {
                    if (child_age_price_ap_array[ia]!='') {
                      myhtml+= '<option value="' + ia + '" ';
                      if (hascheck==false) {hascheck=true; myhtml+=' selected ';}
                      myhtml+= '>' + child_age_price_ap_array[ia] + '</option>';
                    }
                  }
            
      myhtml+=  '</select></div>' +
              '</div>';          
      
//      myhtml+= '<div class="form-group row childs_ages_list_div" data-aa="' + ic + '">' +
//                  '<label for="childs_ages_list_' + ic + '" class="childs_ages_list_label col-md-4 col-form-label form-control-sm text-md-right">Age ' + ic + 'st child:</label>' +
//                  '<div class="col-md-8">' + 
//                    '<select id="childs_ages_list_' + ic + '" class="childs_ages_list_select form-control form-control-sm">' +
//                      '<option value="-1"></option>';
//                     
//                      
//                      for(ia=0; ia<=17; ia++) {
//                        if (child_age_price_ap_array[ia]!='') {
//                          myhtml+= '<option value="' + ia + '" ';
//                          myhtml+= '>' + child_age_price_ap_array[ia] + '</option>';
//                        }
//                      }
//      myhtml+=     '</select>' +
//                  '</div>' +
//                '</div>';
      
      $('#childs_ages_list_main_div').append(myhtml);
      $('.childs_ages_list_select:last').change(childs_ages_list_select_change);
    }
    gks_myscroll();
  });
  
  function childs_ages_list_select_change() {
    
    
  }
  
  
  function gks_input_rnum_adults_change() {
    data_room_aa= $(this).attr('data_room_aa');
    data_room_type_id= $(this).attr('data_room_type_id');
    data_room_max_visitors=parseInt($(this).attr('data_room_max_visitors'));
    if (isNaN(data_room_max_visitors)) data_room_max_visitors=0;
    if (data_room_max_visitors<=0) return;
    
    extra_beds=parseInt($('.gks_input_rnum_extra_beds[data_room_aa='+data_room_aa+'][data_room_type_id='+data_room_type_id+']').val());
    if (isNaN(extra_beds)) extra_beds=0;
    data_room_max_visitors+=extra_beds;

    
    cur_val=parseInt($(this).val());
    if (isNaN(cur_val)) cur_val=0;
    rest = data_room_max_visitors-cur_val;
    

    other_elem=$('.gks_input_rnum_childs[data_room_aa='+data_room_aa+'][data_room_type_id='+data_room_type_id+']');
    if (other_elem.length>0) {
      other_val=parseInt(other_elem.val());
      if (isNaN(other_val)) return;
      if (other_val>rest) {
        other_elem.val(rest);
        gks_input_rnum_childs_change_elem($('.gks_input_rnum_childs[data_room_aa='+data_room_aa+'][data_room_type_id='+data_room_type_id+']'));
      }
    }
    
//    gks_input_rnum_calc($(this));
//    
//    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_adults = cur_val;
//    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_childs = parseInt(other_elem.val());
//    
//    rooms_basket_edit(false,false,false,false);
    room_type_id=parseInt($(this).attr('data_room_type_id')); if (isNaN(room_type_id)) room_type_id=0;
    aa=parseInt($(this).attr('data_room_aa')); if (isNaN(aa)) aa=0;
    if (room_type_id<=0 || aa<=0) return;

    
    
    gks_rsrv_select_exec(room_type_id, aa);
    
  }
  
  function gks_input_rnum_childs_change() {
    gks_input_rnum_childs_change_elem($(this));
    
  }
  function gks_input_rnum_childs_change_elem(elem) {
    data_room_aa= elem.attr('data_room_aa');
    data_room_type_id= elem.attr('data_room_type_id');
    
    data_room_max_visitors=parseInt(elem.attr('data_room_max_visitors'));
    if (isNaN(data_room_max_visitors)) data_room_max_visitors=0;
    if (data_room_max_visitors<=0) return;
    
    extra_beds=parseInt($('.gks_input_rnum_extra_beds[data_room_aa='+data_room_aa+'][data_room_type_id='+data_room_type_id+']').val());
    if (isNaN(extra_beds)) extra_beds=0;
    data_room_max_visitors+=extra_beds;
    
    cur_val=parseInt(elem.val());
    if (isNaN(cur_val)) cur_val=0;
    rest = data_room_max_visitors-cur_val;

    
    other_elem=$('.gks_input_rnum_adults[data_room_aa='+data_room_aa+'][data_room_type_id='+data_room_type_id+']');
    other_val=parseInt(other_elem.val());
    if (isNaN(other_val)) return;
    if (other_val>rest) other_elem.val(rest);

//    gks_input_rnum_calc(elem);
//
//    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_adults = parseInt(other_elem.val());
//    json_rooms_list[data_rsrv_aa].selrooms[data_roomtype_aa].rooms_items[data_room_aa].rnum_childs = cur_val;
//    
//    rooms_basket_edit(false,false,false,false); //showloading,showerrors,gonext
    room_type_id=parseInt(elem.attr('data_room_type_id')); if (isNaN(room_type_id)) room_type_id=0;
    aa=parseInt(elem.attr('data_room_aa')); if (isNaN(aa)) aa=0;
    if (room_type_id<=0 || aa<=0) return;

    cur_val=parseInt(elem.val());
    if (isNaN(cur_val)) cur_val=0;
    exist_selectors=$('.gks_input_child_age[data_room_type_id=' + data_room_type_id + '][data_room_aa=' + data_room_aa + ']').length;
    diafora=cur_val-exist_selectors;
    if (diafora==0) {
      //do nothing
    } else if (diafora>0) {
      
      start_i=exist_selectors+1;
      for (ii=0; ii< diafora;ii++) {
        i=start_i+ii;
        rt_html='<select ' +
            'class="gks_input_select gks_input_child_age" ' +
            'data_room_aa="' + data_room_aa + '" ' +
            'data_room_type_id="' + data_room_type_id + '" ' +
            'data_room_i="' + i + '" ' +
            'style="width:unset !important;padding: 4px 0px !important;"><option value=0></option>';
        
  
        for (ca=0;ca<childs_and_ages.length; ca++) {
           rt_html+='<option value=' + (ca+1).toString() +
           '>' + childs_and_ages[ca].age + ' ' + from_php_text13 + '</option>';
        }
        
        rt_html+='</select>';   
        if (i==1) {
          $('.child_selectors[data_room_type_id=' + data_room_type_id + '][data_room_aa=' + data_room_aa + ']').after(rt_html);
        } else {
          $('.gks_input_child_age[data_room_type_id=' + data_room_type_id + '][data_room_aa=' + data_room_aa + '][data_room_i=' + (i-1).toString() + ']').after(rt_html);
        }
        $('.gks_input_child_age[data_room_type_id=' + data_room_type_id + '][data_room_aa=' + data_room_aa + '][data_room_i=' + (i).toString() + ']').change(gks_input_child_age_change);
        gks_input_child_age_diable_option();
      }  
        
    } else if (diafora<0) {
      diafora=-diafora; //na ginei thetiko
      for (ii=0; ii< diafora;ii++) {
        $('.gks_input_child_age[data_room_type_id=' + data_room_type_id + '][data_room_aa=' + data_room_aa + ']:last').remove();
      }
    }
    
    gks_rsrv_select_exec(room_type_id, aa);
  }
  
  function gks_input_child_age_change() {
    //console.log('gks_input_child_age_change');
    room_type_id=parseInt($(this).attr('data_room_type_id')); if (isNaN(room_type_id)) room_type_id=0;
    aa=parseInt($(this).attr('data_room_aa')); if (isNaN(aa)) aa=0;
    if (room_type_id<=0 || aa<=0) return;
    gks_rsrv_select_exec(room_type_id, aa);
    gks_input_child_age_diable_option();
  }

  function gks_input_rnum_child_kounies_change() {
    room_type_id=parseInt($(this).attr('data_room_type_id')); if (isNaN(room_type_id)) room_type_id=0;
    aa=parseInt($(this).attr('data_room_aa')); if (isNaN(aa)) aa=0;
    if (room_type_id<=0 || aa<=0) return;
    gks_rsrv_select_exec(room_type_id, aa);
    
  }
  function gks_input_rnum_extra_beds_change() {
    room_type_id=parseInt($(this).attr('data_room_type_id')); if (isNaN(room_type_id)) room_type_id=0;
    aa=parseInt($(this).attr('data_room_aa')); if (isNaN(aa)) aa=0;
    if (room_type_id<=0 || aa<=0) return;
    
    var max_visitos=parseInt($('#gks_rsrv_select_id_' + room_type_id).attr('data-adults')); if (isNaN(max_visitos)) max_visitos=0;
    var eb_val=parseInt($(this).val()); if (isNaN(eb_val)) eb_val=0;
    
    elem=$('.gks_input_rnum_adults[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']');
    cccval=parseInt(elem.val()); if (isNaN(cccval)) cccval=0;
    elem.find('option').each(function() {
      ccval=parseInt($(this).attr('value')); 
      if (ccval>max_visitos) {
        if (ccval> (max_visitos+eb_val)) {
          $(this).remove();
        }
      }
    });
    
    for (j=max_visitos+1;j<=(max_visitos+eb_val);j++) {
      if (elem.find('option[value=' + j + ']').length==0) {
        elem.append('<option value=' + j + '>' + j + '</option>');
      }
    }
    
    //if (cccval>max_visitos + eb_val) 
    elem.val((max_visitos + eb_val).toString());
    
    
    gks_rsrv_select_exec(room_type_id, aa);
    
  }
  
  
  $('.gks_input_rnum_adults').change(gks_input_rnum_adults_change);
  $('.gks_input_rnum_childs').change(gks_input_rnum_childs_change);
  $('.gks_input_child_age').change(gks_input_child_age_change);
  $('.gks_input_rnum_child_kounies').change(gks_input_rnum_child_kounies_change);
  $('.gks_input_rnum_extra_beds').change(gks_input_rnum_extra_beds_change);
  
  
  function gks_input_child_age_diable_option() {
    
    gks_rooms_ages_index=[];
    $('.gks_input_child_age').each(function() {
      val=parseInt($(this).val()); if (isNaN(val)) val=0;
      if (val>0) {
        gks_rooms_ages_index.push(val);
      }
    });
    $('.gks_input_child_age').each(function() {
      var cur_val=parseInt($(this).val()); if (isNaN(val)) val=0;
      $(this).find('option').each(function() {
        opt_val=parseInt($(this).attr('value')); if (isNaN(opt_val)) opt_val=0; 
        if (opt_val>0) {
          if (opt_val>0 && opt_val!=cur_val && gks_rooms_ages_index.includes(opt_val)) {
            $(this).prop('disabled' , true);
          } else {
            $(this).prop('disabled' , false);
          }
        }
      });
      
    });
    //console.log(gks_rooms_ages_index);
  }
  gks_input_child_age_diable_option();
  

  function gks_rsrv_select_exec(room_type_id, aa) { 
    
    
    //console.log(room_type_id, aa);
    
    
    datasend='';
    datasend+='&command=hotel_reservation_search_calc';
    datasend+='&check_in=' + encodeURIComponent(gks_check_in_round);
    datasend+='&check_out='  + encodeURIComponent(gks_check_out_round);
    datasend+='&num_days='  + encodeURIComponent(gks_num_days);
    datasend+='&room_type_id='  + encodeURIComponent(room_type_id);
    datasend+='&num_adults='  + encodeURIComponent(gks_adults_count);
    //datasend+='&num_adults='  + encodeURIComponent($('.gks_input_rnum_adults[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']').val());
    datasend+='&num_childs='  + encodeURIComponent(gks_childs_count);
    //datasend+='&num_childs='  + encodeURIComponent($('.gks_input_rnum_childs[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']').val());
    datasend+='&postype='  + 'calc';
    
    
    
    var childs_and_ages_temp=[];
    var ttt=0;
    $('.gks_input_child_age[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']').each(function() {
      val=parseInt($(this).val());
      if (isNaN(val)) val=-1;
      if (val>0 && val <= childs_and_ages.length) {
        ttt++;
        childs_and_ages_temp.push({index: ttt, age:childs_and_ages[val-1].age});
      } 
    });
    
    
    childs_ages_list_str = encodeURIComponent($.base64.encode(JSON.stringify(childs_and_ages_temp)));
    datasend+='&childs_ages_list_str=' + childs_ages_list_str;

    rnum_child_kounies=parseInt($('.gks_input_rnum_child_kounies[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']').val());
    if (isNaN(rnum_child_kounies)) rnum_child_kounies=0; 
    datasend+='&rnum_child_kounies='  + encodeURIComponent(rnum_child_kounies);
    
    rnum_extra_beds=parseInt($('.gks_input_rnum_extra_beds[data_room_type_id=' + room_type_id + '][data_room_aa=' + aa + ']').val());
    if (isNaN(rnum_extra_beds)) rnum_extra_beds=0; 
    datasend+='&rnum_extra_beds='  + encodeURIComponent(rnum_extra_beds);

    
    //console.log(childs_and_ages_temp);
    //console.log(datasend);
    $.ajax({
			url: '/wp-content/plugins/gks_hotel/gks_hotel_ajax.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_room_type_id:room_type_id,
			gks_aa:aa,
			error : function(jqXHR ,textStatus,  errorThrown) {
				gks_myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					gks_myalert('error:' + from_php_text2);
				} else {
					if (data.success == true) {
					  data=data.data;
  					if (data.success == true) {
  					  $('.room_type_total_price[data_room_type_id=' + this.gks_room_type_id + '][data_room_aa=' + this.gks_aa + ']').html(data.price_html).attr('data-val',data.price);
  					  gks_rsrv_totals();
  					   
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
  
  function gks_rsrv_totals() {  
    //return;
    
    
    
    gks_calc_price=0;
    gks_calc_rooms=0;
    gks_calc_persons=0;
    gks_selected_rooms=[];
    var gks_sum_adults=0;
    var gks_sum_childs=0;
    
    
    
    var return_error='';
    for(i=0; i < gks_roomsarray.length; i++) {
      //console.log(gks_roomsarray[i]);
      val_sel=parseInt($('#gks_rsrv_select_id_' + gks_roomsarray[i].id).val());
      if (val_sel>0) {

        
        gks_calc_rooms+=val_sel;
        //gks_calc_persons+=val_sel * gks_roomsarray[i].visitors_max;
        
        var rooms=[];
        $('.gks_input_rnum_adults[data_room_type_id=' + gks_roomsarray[i].id + ']').each(function() {
          aa=$(this).attr('data_room_aa');if (isNaN(aa)) aa=0;
          rnum_adults = parseInt($('.gks_input_rnum_adults[data_room_type_id=' + gks_roomsarray[i].id + '][data_room_aa=' + aa + ']').val()); if (isNaN(rnum_adults)) rnum_adults=0;
          gks_sum_adults+=rnum_adults;
          
          var childs_and_ages_temp=[];
          var cc_child=0;
          $('.gks_input_child_age[data_room_type_id=' + gks_roomsarray[i].id + '][data_room_aa=' + aa + ']').each(function(index) {
            cc_child++;
            val=parseInt($(this).val());
            if (isNaN(val)) val=-1;
            if (val>=1) {
              //mytxt= $(this).find('option:selected').text();
              //if (mytxt=='') mytxt=(index + 1).toString() + 'st child'; 
              age=-1;
              if (val>0 && val <= childs_and_ages.length) {
              //if (typeof childs_and_ages[val-1] !=='undefined') {
                
                childs_and_ages_temp.push({index: cc_child, age:childs_and_ages[val-1].age});
              }
            } else {
              mytext = from_php_text7.replaceAll('[1]',$('.gks_rsrv_rthd[data-id=' + gks_roomsarray[i].id + ']').text()).replaceAll('[2]',aa.toString()).replaceAll('[3]',cc_child.toString());
              return_error+=mytext + '<br>';
            }
          });
          rnum_childs=childs_and_ages_temp.length;
          gks_sum_childs+=rnum_childs;
          
          room_price = parseFloat($('.room_type_total_price[data_room_type_id=' + gks_roomsarray[i].id + '][data_room_aa=' + aa + ']').attr('data-val'));
          if (isNaN(room_price)) room_price=0;
          gks_calc_price+=room_price;
          
          rnum_child_kounies=parseInt($('.gks_input_rnum_child_kounies[data_room_type_id=' + gks_roomsarray[i].id + '][data_room_aa=' + aa + ']').val());
          if (isNaN(rnum_child_kounies)) rnum_child_kounies=0; 

          rnum_extra_beds=parseInt($('.gks_input_rnum_extra_beds[data_room_type_id=' + gks_roomsarray[i].id + '][data_room_aa=' + aa + ']').val());
          if (isNaN(rnum_extra_beds)) rnum_extra_beds=0; 
          
          
          rooms.push({
            rnum_adults: rnum_adults, 
            rnum_childs: rnum_childs,
            childs_and_ages:childs_and_ages_temp,
            return_error:return_error,
            rnum_child_kounies:rnum_child_kounies,
            rnum_extra_beds:rnum_extra_beds,
          });
          
        });
        gks_selected_rooms.push({room_type_id:gks_roomsarray[i].id, num: val_sel, roomtype: gks_roomsarray[i], rooms_items:rooms});
      }
    }
    
    

    
    
    $('#gks_total_price').html(
    (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW != 'after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL + ' ' : '') +
    gks_formatMoney(gks_calc_price, from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
    (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW == 'after' ? ' ' + from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '')
    );
    
		$('#gks_total_rooms').html(gks_calc_rooms + '/' + gks_rooms_count);
    pososto=Math.round(gks_calc_rooms*100/gks_rooms_count);
    if (pososto<0)pososto=0;
    if (pososto>100)pososto=100;
    $('#gks_pbar_rooms').progressbar( "option", "value", pososto);
    
    sum_visitors=gks_adults_count + gks_childs_count;
    mymeion=0;
    if (gks_sum_adults>gks_adults_count) mymeion+= gks_sum_adults - gks_adults_count; //na bgoun ta parapanisia
    if (gks_sum_childs>gks_childs_count) mymeion+= gks_sum_childs - gks_childs_count; //na bgoun ta parapanisia
    gks_calc_persons=gks_sum_adults + gks_sum_childs; //-mymeion;
    
		$('#gks_total_persons').html(gks_calc_persons + '/' + sum_visitors);
    
    pososto=Math.round(gks_calc_persons*100/sum_visitors);
    if (pososto<0)pososto=0;
    if (pososto>100)pososto=100;
    $('#gks_pbar_persons').progressbar( "option", "value", pososto);
    
    gks_check1=false;
    gks_check2=false;
    //console.log(gks_selected_rooms);

    //console.log(return_error);

    return return_error;    
  }


});
