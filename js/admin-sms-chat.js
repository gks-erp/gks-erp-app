/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var need_save=false;
//var mychange = 'input keyup paste';
var mychange = 'change paste keyup';
//var mychange = 'keyup';
//var mychange = 'propertychange input change keyup paste';

//var mychange = 'change';
var gks_page_loading=true;
var hashchange_bypass=false;

jQuery.fn.scrollTo = function(elem, speed) { 
    $(this).animate({
        scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top 
    }, speed == undefined ? 1000 : speed); 
    return this; 
};

jQuery(document).ready(function($) {

  function escape_phone(a) {
    if (a=='') return '';
    if (a.startsWith('+')) return '\\' + a;
    return a;
    
  }

  function myresize() {
    mywidth=$(window).width();
    myheight=$(window).height();
    
    if (mywidth<=600) {
      $('#gks_main_panel').css('height','unset');
      
    } else if (mywidth<=900) {
      $('#gks_main_panel').css('height','unset');
      
    } else {
      ph=myheight;
      ph-=$('#gks_nav_session_header').outerHeight();
      ph-=$('.gksitemheader').outerHeight();
      ph-=$('#gks_nav_session_footer').outerHeight();
      
      $('#gks_main_panel').css('height',ph+'px');
    }
    
    
  }
  
  
  $(window).resize(myresize);
  myresize();
  
  
  function gks_sms_cmd(cmd,restdatasend) {
    datasend='';
    datasend+='&cmd=' + encodeURIComponent($.base64.encode(cmd));
    datasend+=restdatasend; 

    
    $.ajax({
			url: '/my/admin-sms-chat-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_cmd:cmd,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					//console.log(data);
  					if (this.gks_cmd=='get_chats') {
  					  $('#gks_main_panel_users_list').html(data.html);
  					  $('.gks_sms_user_div').click(gks_sms_user_div_click);



              setTimeout(check_change_hash, 1000);
    					  
  					} else if (this.gks_cmd=='get_from_number') {
  					  $('#gks_main_panel_chat_list2').html(data.html);
  					  $('#gks_main_panel_chat_list2 .gks_sms_chat_resend').click(gks_sms_chat_resend_click);
  					  
  					  $('#count_msgs').html(data.count_msgs);
  					  
  					  $('#gks_main_panel_chat_list').scrollTop($('#gks_main_panel_chat_list2')[0].scrollHeight);
  					  rechecks=data.rechecks;
  					  //console.log(rechecks);
  					  last_id=data.last_id;
  					  //console.log(last_id);
  					  insert_id_array=[];
              prev_date=data.prev_date;
              
  					  
  					} else if (this.gks_cmd=='updateme') {
  					  //console.log(data);
  					  if (data.msgs_ids.length>0) {
    					  for(i=0;i<data.msgs_ids.length; i++) {
    					    $('.gks_msgs_item[data-msg-id=' + data.msgs_ids[i] + ']').remove(); //gia na min exo dipla
    					  }
    					}
  					  
  					  if (data.count_msgs>0) {
  					    $('#gks_main_panel_chat_list2').append(data.html);
  					    for (i=0; i<data.msgs_ids.length; i++) {
  					      $('.gks_msgs_item[data-msg-id=' + data.msgs_ids[i] + '] .gks_sms_chat_resend').click(gks_sms_chat_resend_click);
  					    }
  					    
  					    last_id=data.last_id;
  					    $('#gks_main_panel_chat_list').scrollTop($('#gks_main_panel_chat_list2')[0].scrollHeight);
  					    prev_date=data.prev_date;
  					  }
  					  
					  
					    for (i=0; i<data.out_icons.length; i++) {
					      $('.gks_msgs_item[data-msg-id=' +  data.out_icons[i].id + '] .icons').html(data.out_icons[i].icons); 
					      $('.gks_msgs_item[data-msg-id=' +  data.out_icons[i].id + '] .icons .gks_sms_chat_resend').click(gks_sms_chat_resend_click);
					    }
					    
              for (i=0; i<data.finish_rechecks.length; i++) {
                if (rechecks.includes(data.finish_rechecks[i])) {
                  j = rechecks.indexOf(data.finish_rechecks[i]);
                  rechecks.splice(j, 1);
                }  
              }
              $('#count_msgs').html(data.total_sms);
              mytimer = setInterval(run_get_data, 3000);
  					  
  					} else if (this.gks_cmd=='sent_text') {
  					  //console.log(data);
  					  $('.gks_msgs_item[data-msg-id=' + data.gks_sms_send_insert_id + ']').remove(); //gia na min exo dipla
  					  
  					  $('#gks_main_panel_chat_list2').append(data.html);
  					  $('.gks_msgs_item_new').removeClass('gks_msgs_item_new');
  					  $('#gks_main_panel_chat_list').scrollTop($('#gks_main_panel_chat_list2')[0].scrollHeight);
  					  insert_id_array.push(data.gks_sms_send_insert_id);
  					  if (rechecks.includes(data.gks_sms_send_insert_id)==false) rechecks.push(data.gks_sms_send_insert_id);
  					  
  					  //console.log('insert_id_array',insert_id_array);
  					  //console.log('rechecks',rechecks);
  					  prev_date=data.prev_date;
  					  $('#count_msgs').html(data.total_sms);
  					  
  					  $('#new_message').val('').focus();
  					  gks_resize_textarea($('#new_message'));
  					} else if (this.gks_cmd=='get_user_details') {
  					  $('#gks_main_panel_details_list').html(data.html);
  					  $('#gks_main_panel_details_list .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
  					}
  					
  					
  					
  					
  					//need_save=false;
            
            
      			            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
        }
      }
    });
        
    
    
  }

  gks_sms_cmd('get_chats','');
  
  
  function gks_sms_user_div_click() {
    //console.log('gks_sms_user_div_click');
    $('.gks_sms_user_div_selected').removeClass('gks_sms_user_div_selected');
    $('.gks_sms_user_div_hidden').removeClass('gks_sms_user_div_hidden');
    $('#search_user').val('');
    
    $(this).addClass('gks_sms_user_div_selected');
    sel_number=$(this).attr('data-number');
    //console.log(sel_number);
    $('#gks_main_panel_chat_list2').html('<div style="text-align: center;"><img src="/my/img/wait.gif"></div>');
    
    restdatasend='&number=' + encodeURIComponent($.base64.encode(sel_number));
    gks_sms_cmd('get_from_number',restdatasend);
    
    user_id=parseInt($(this).attr('data-user_id'));
    if (isNaN(user_id)) user_id=0;
    if (user_id==0) {
      $('#gks_main_panel_details_list').html(
        '<div class="user_sxolio alert alert-danger" style="margin-top:10px;">' + 
        gks_lang('Δεν βρέθηκε σχετική επαφή για τον αριθμό')+':<br>' +
        '<strong>' + sel_number + '</strong>' + 
        '</div>' +
        '<a href="admin-users.php?search_string=' + sel_number + '" class="btn btn-primary btn-sm">' + 
        gks_lang('Αναζήτηση στις επαφές') + 
        '</a>'
        );
    } else {
      $('#gks_main_panel_details_list').html('<div style="text-align: center;"><img src="/my/img/wait.gif"></div>');
      
      restdatasend='&user_id=' + user_id;
      gks_sms_cmd('get_user_details',restdatasend);
      
    }
    find_elem=$('.gks_sms_user_div[data-number=' + escape_phone(sel_number) + ']');
    if (find_elem.length>0) {
      find_elem[0].scrollIntoView({block: "nearest",inline : 'nearest'});
      //find_elem[0].click();
    }
    hashchange_bypass=true;
    document.location.hash = 'number='+sel_number;
  }
  
  function new_message_change() {gks_resize_textarea($(this));}
  $('#new_message').on(mychange, new_message_change);
  gks_resize_textarea($('#new_message'));
  
  
  $('#send_message').click(function() {
    if (sel_number=='') {
      myalert('error:'+gks_lang('Επιλέξτε κάποια συνομιλία/επαφή/αποδέκτη'));
      return;  
    }
    mytext=$('#new_message').val().trim();
    if (mytext=='') {
      myalert('error:'+gks_lang('Εισάγετε κάποιο κείμενο'));
      return;  
    }
    
    restdatasend ='&number=' + encodeURIComponent($.base64.encode(sel_number));
    restdatasend+='&text=' + encodeURIComponent($.base64.encode(mytext));
    restdatasend+='&prev_date=' + encodeURIComponent($.base64.encode(prev_date));
    restdatasend+='&from=' + encodeURIComponent($.base64.encode($("#sms_from").val().trim()));
    restdatasend+='&sender_sms_provider='  + encodeURIComponent($.base64.encode($('#sms_from option:selected').attr('data-provider')));
    restdatasend+='&sender_sms_sender='  + encodeURIComponent($.base64.encode($('#sms_from option:selected').attr('data-sender')));

    
    gks_sms_cmd('sent_text',restdatasend);
    
  });
  
  //$('#search_user').click(function() {
  //  setTimeout(search_user_change, 300);
  //});
  $('#search_user').on("search", function() { 
    //console.log('searchsearchsearchsearchsearchsearchsearch');
    search_user_change();
  });
 
  function search_user_change() {
    
    var valtext=$('#search_user').val();
    //console.log('search_user_change',valtext);  
    if (valtext=='') {
      $('.gks_sms_user_div_hidden').removeClass('gks_sms_user_div_hidden');
      $('#search_user').val('');
      return;  
    }
    
    var valtext1=greeklish(valtext)
    var valtext2=greekkeybord(valtext)
    
    
    $('.gks_sms_user_div').each(function() {
      text1=$(this).attr('data-number');  
      text2=$(this).attr('data-user_name');  
      
      if (text1.includes(valtext1) || text2.includes(valtext1) || text1.includes(valtext2) || text2.includes(valtext2)) {
        $(this).removeClass('gks_sms_user_div_hidden');
      } else {
        $(this).addClass('gks_sms_user_div_hidden');
      }
      
      
    });
    
  }
  $('#search_user').on('paste keyup', search_user_change);

  
  
  $('#search_user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        comm_type:'phone',
        //mobile: 1,
        photo: 1,
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
      $('#search_user').attr('data-user_id',ui.item.user_id);
      $('#search_user').val('');
      $('.gks_sms_user_div_hidden').removeClass('gks_sms_user_div_hidden');
      
      phone_fix=ui.item.phone_fix;
      //console.log(phone_fix);
      find_elem=$('.gks_sms_user_div[data-number=' + escape_phone(phone_fix) + ']');
      if (find_elem.length>0) {
        find_elem[0].scrollIntoView({block: "nearest",inline : 'nearest'});
        find_elem[0].click();
      } else {
        //console.log('den iparxei');

        name_conv=greeklish(ui.item.user) + ' ' + greekkeybord(ui.item.user);
        name_conv=name_conv.replaceAll('"','');
        name_conv=name_conv.replaceAll("'",'');
        name_conv=name_conv.replaceAll('&','');
        name_conv=name_conv.trim();
        
        html=
        '<div class="gks_sms_user_div" data-number="' + phone_fix + '" data-user_id="' + ui.item.user_id + '" data-user_name="' + name_conv + '">'+
          '<div class="gks_sms_user_div_photo">' +
          ui.item.photo + 
          '</div>' +
          '<div class="gks_sms_user_div_detail">' +
            '<div class="gks_sms_user_div_name">' +
              ui.item.user + 
            '</div>' +
            '<div class="gks_sms_user_div_number">' +
            phone_fix +
            '</div>' +
            '<div class="gks_sms_user_div_date">' +
            '--' +
            '</div>' + 
          '</div>' + 
        '</div>';        
        
        $('#gks_main_panel_users_list').prepend(html);
        $('.gks_sms_user_div[data-number=' + escape_phone(phone_fix) + ']').click(gks_sms_user_div_click);
        $('.gks_sms_user_div[data-number=' + escape_phone(phone_fix) + ']')[0].scrollIntoView({block: "nearest",inline : 'nearest'});
        $('.gks_sms_user_div[data-number=' + escape_phone(phone_fix) + ']').click();
      }
      
    },
    change: function (event, ui) {
        if(!ui.item){
        //  $('#dialog_item_message_to_sms').val('').attr('data-user_id','0');
          $('#search_user').attr('data-user_id','0');
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
    


  function sms_template_change() {
    data_text=$('#sms_template option:selected').attr('data-text').trim();
    if (data_text=='') return;
    data_text=$.base64.decode(data_text);
    $('#new_message').val(data_text);
    messagesms_change('new_message','sms_chars');
    gks_resize_textarea($('#new_message'));
  }
  $('#sms_template').change(sms_template_change);
  
  
  

  
    
  var sel_number='';
  var rechecks=[];
  var last_id=0;
  var insert_id_array=[];
  var prev_date='';
  function run_get_data() {
    if (sel_number=='') return;
    
    clearInterval(mytimer);
    restdatasend='';
    restdatasend+='&number=' + encodeURIComponent($.base64.encode(sel_number));
    restdatasend+='&last_id=' + last_id; 
    restdatasend+='&prev_date=' + encodeURIComponent($.base64.encode(prev_date));
    
    rechecks_str = encodeURIComponent($.base64.encode(JSON.stringify(rechecks)));
    restdatasend+='&rechecks_str=' + rechecks_str; 
    
    insert_id_array_str = encodeURIComponent($.base64.encode(JSON.stringify(insert_id_array)));
    restdatasend+='&insert_id_array_str=' + insert_id_array_str; 
    
    
    
    //console.log('rechecks',rechecks);
    gks_sms_cmd('updateme',restdatasend);
  }
  
  mytimer = setInterval(run_get_data, 3000);

  function check_change_hash() {
    //console.log('check_change_hash');
    myhash=window.location.hash;
    if (myhash.length>=4 && myhash.startsWith('#number=')) {
      temp=myhash.substring(8);
      if (temp.length>=3) {
        find_elem=$('.gks_sms_user_div[data-number=' + escape_phone(temp) + ']');
        if (find_elem.length>=1) {
          $('.gks_sms_user_div[data-number=' + escape_phone(temp) + ']').click();
          find_elem[0].scrollIntoView({block: "nearest",inline : 'nearest'});
          hashchange_bypass=true;
          document.location.hash = '';
        }
      }
    }    
  }
  
  $(window).on('hashchange', function( e ) {
    
    //console.log(hashchange_bypass, 'hash changed' );
    if (hashchange_bypass==false) {
      check_change_hash();
    }
    hashchange_bypass=false;
    
  });

  function gks_sms_chat_resend_click() {
    data_id=parseInt($(this).attr('data-id'));  if (isNaN(data_id)) data_id=0;
    if (data_id<=0) return;
    //console.log('resend',data_id);    

    datasend='id=' + data_id + '&cmd=resend';
    $(this).css({'background-color':'darkgray','cursor': 'default'}).attr('title','').attr('data-id','');
    
    
    //$('body').addClass('myloading');
    $.ajax({
      url: 'admin-sms-cmd.php',
      type: 'POST',
      cache: false,
      dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        //$('body').removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},
      success: function( data ) {
        //$('body').removeClass('myloading');
        if (data.success == true) {
          //myalert('ok:' + $.base64.decode(data.message) + '<br>'+gks_lang('Ανανεώστε την σελίδα για να δείτε την νέα εγγραφή που μόλις προστέθηκε'));
        } else {
          myalert('error:' + $.base64.decode(data.message));
        }
      }
    });
        
  }
  
    
  
  
  //generic
  gks_page_loading=false;
//  $('.myneedsave').on('input change keyup paste', function() {
//    need_save=true; 
//  });
//
//  window.onbeforeunload = function() {
//    //if (from_php_perm_ret_edit==false) return;
//    if (need_save==false) return;
//    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
//  };

  need_save=false;
  
  //console.log('load end');

	  
});
    
  
  