/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

jQuery(document).ready(function($) {
  //$.base64.utf8encode = true;
  //$.base64.utf8decode = true;
  //$.datetimepicker.setLocale(from_php_gks_datetimepicker_locale);

  
  is_notification_page=false;
  if (window.location.pathname=='/my/admin-notification.php') is_notification_page=true;
  
  //var audioping = new Audio('/audio/notif1.mp3');
  
  function close_notification_click() {
    nid=parseInt($(this).attr('data-id'));
    $('.div_notification[data-id=' + nid + ']').fadeOut(500,function() {
      $(this).remove();
      notification_reorder();  
    });
    $.ajax({
  		url: '/my/admin-notification_done.php?id=' + nid,
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);
  		},				
  		success: function(data) {
  			if (!data) {
  				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  			} else {
  				if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					//window.location.reload();
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  				}
  			}
  		}
  	});
  }
  
  function snooze_notification_change() {
    nid=parseInt($(this).attr('data-id'));
    nval=parseInt($(this).val());
    if (nval<=0) return;
    
    $('.div_notification[data-id=' + nid + ']').fadeOut(500,function() {
      $(this).remove();
      notification_reorder();  
    });

    $.ajax({
  		url: '/my/admin-notification_done.php?id=' + nid + '&snooze=' + nval,
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);
  		},				
  		success: function(data) {
  			if (!data) {
  				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  			} else {
  				if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					//window.location.reload();
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  				}
  			}
  		}
  	});
    
  }
  
  
  
  var notification_timer = setInterval(func_notification_timer, 30000);
  
  function func_notification_timer() {
    
    
    $.ajax({
  		url: '/my/admin-notification_get_data.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			console.log('error:' + jqXHR.responseText);
  		},				
  		success: function(data) {
  			if (!data) {
  				console.log('error:func_notification_timer');
  			} else {
  				if (data.success == true) {
  					
  					
  					if (is_notification_page==false) {
    					var curr_count=0;
    					
    					mynotif=data.data;
    					var id_notification=[];
    					for(i=0;i<mynotif.length;i++) {
    					  id_notification.push(mynotif[i].id);
    					}
  					  
    					var div_notification_exist=[];
    					$('.div_notification').each(function() {
                nid=parseInt($(this).attr('data-id'));
                if (isNaN(nid)) nid=0;
                if (nid>0) {
                  if (id_notification.includes(nid)) {
                    div_notification_exist.push(nid);
                    curr_count++;
                    nbottom=notification_buttom(curr_count) + 'px';
                    ebottom=$(this).css('bottom');
                    if (ebottom!=nbottom) {
                      $(this).css('bottom',ebottom).animate({'bottom':nbottom}, 'slow');
                    }
                    
                  } else {
                    $(this).fadeOut(1000,function() {
                      $(this).remove();
                    });                  
                  }
                } else {
                  $(this).remove();  
                }
    					});
    					
    					
    					for(i=0;i<mynotif.length;i++) {
    					  if (div_notification_exist.includes(mynotif[i].id)) {
    					    $('.ago_notification[data-id=' + mynotif[i].id + ']').html(mynotif[i].ago);
    					  } else {
    					    curr_count+=1;
      					  myhtml='<div class="div_notification" data-id="' + mynotif[i].id + '" style="bottom:' + notification_buttom(curr_count) + 'px;">' +
      					  '<i class="fas fa-times close_notification" data-id="' + mynotif[i].id + '"></i>' +
      					  '<div class="ago_notification" data-id="' + mynotif[i].id + '">' + mynotif[i].ago + '</div>' +
      					  '<div class="divtext_notification">' + mynotif[i].text  + '</div>' +
      					  '<div class="divcombo_notification">' +
      					  'Αναβολή: <select class="snooze_notification" data-id="' + mynotif[i].id + '">' + 
      					  '<option value="0"></option>' + 
      					  '<option value="1">1 ώρα</option>' + 
      					  '<option value="2">2 ώρες</option>' + 
      					  '<option value="3">3 ώρες</option>' + 
      					  '<option value="4">4 ώρες</option>' + 
      					  '<option value="8">8 ώρες</option>' + 
      					  '<option value="24">24 ώρες</option>' + 
      					  '<option value="48">48 ώρες</option>' + 
      					  '</select>' + 
      					  '</div>' +
      					  
      					  '</div>';
      					  $('#div_html_notification').append(myhtml);
      					  $('.close_notification[data-id=' + mynotif[i].id + ']').click(close_notification_click);
      					  $('.snooze_notification[data-id=' + mynotif[i].id + ']').change(snooze_notification_change);
      					  
      					  
      					  
      					  $('.div_notification[data-id=' + mynotif[i].id + ']').fadeIn(1000);
      					}
    				  }
    				  

      			}
      			
  				  if (data.ps) {
    				  //audioping.play();
    				  document.querySelector('#audioping').play();
    				}      			
  					$('.gks_notification_count').each(function() {
  					  if (data.data.length==0) {$(this).html('');$(this).hide();}
  					  else {$(this).html(data.data.length);$(this).show();}
  					});
  					
  				} else {
  					console.log('error:func_notification_timer:' + $.base64.decode(data.message));
  				}
  			}
  		}
  	});
  }
  
  function notification_reorder() {
    var cdiv_count=0;
    var elems_div_notification=$('.div_notification');
    elems_div_notification.each(function() {
      cdiv_count++;
      nbottom=notification_buttom(cdiv_count) + 'px';
      ebottom=$(this).css('bottom');
      if (ebottom!=nbottom) {
        $(this).css('bottom',ebottom).animate({'bottom':nbottom}, 'slow');
      }
    });
  	$('.gks_notification_count').each(function() {
  	  if (elems_div_notification.length==0) {$(this).html('');$(this).hide();}
  	  else {$(this).html(elems_div_notification.length);$(this).show();}
  	});
    
  }
  
  function notification_buttom(ii) {
    return ((ii-1)*124 + ii*2);
  }
  
  $('#new_notification').click(function() {
    //$('#dialog_notification_user').hide();
    //$('#dialog_notification_to_me').prop('checked', true);
    //$('#dialog_notification_to_other').prop('checked', false);
    //$('#dialog_notification_user').val('').attr('data-id','0');
    //$('#dialog_notification_message').val('');
    //$('#dialog_notification_currlink').prop('checked', true);
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 500) dwidth=500;
    if (dheight> 380) dheight=380;
    dialog_notification.dialog('option', 'width', dwidth);
    dialog_notification.dialog('option', 'height', dheight);
    $('#dialog_notification').parent().css({position:'fixed'});      
    dialog_notification.dialog('open');      
  });
  
  dialog_notification = $( "#dialog_notification" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "OK" : function() {
        myto=parseInt($('input[name=dialog_notification_to]:checked').val());
        if (myto!=0) {
          myto=parseInt($('#dialog_notification_user').attr('data-id'));
          if (isNaN(myto)) myto=0;
          if (myto<=0) {
            myalert('error:Επιλέξτε τον παραλήπτη'); return;  
          }
        }
        mytext=$('#dialog_notification_message').val().trim();
        if (mytext=='') {
          myalert('error:Πληκτρολογήστε το μήνυμά σας'); return;
        }
        mycl=(($('#dialog_notification_currlink').is(':checked')) ? 1:0);
        datasend='to=' + myto + '&message=' + encodeURIComponent($.base64.encode(mytext)) + '&cl=' + mycl;
        if (mycl==1) {
          datasend+='&mylink=' + encodeURIComponent($.base64.encode(window.location.href));
          datasend+='&mytitle=' + encodeURIComponent($.base64.encode(document.title));
          
        }
       
        $('body').addClass("myloading");
        
        $.ajax({
    			url: '/my/admin-notification_add.php',
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
      					dialog_notification.dialog('close');
      					myalert('ok:' + 'Το μήνυμα έχει καταχωρηθεί');
    					} else {
    						myalert('error:' + $.base64.decode(data.message));
    					}
    				}
    			}
    			
    		});     
  
  
              
      },
      "Άκυρο" : function() {
        $(this).dialog('close');
      },
    }
  });
  
  $('#dialog_notification_user').autocomplete({
    source: "admin-autocomplete-user.php?company=1",
    minLength: 3,
    delay: 300, //default
    autoFocus: true,
    select: function( event, ui ) {
      $("#dialog_notification_user").attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#dialog_notification_user").val('').attr('data-id','0');
      }
    }
  });
  $('#dialog_notification_to_me').click(function()    {$('#dialog_notification_user').hide();});
  $('#dialog_notification_to_other').click(function() {$('#dialog_notification_user').show().focus();});
  
  

});  