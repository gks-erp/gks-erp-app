/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// chrome://inspect/#workers
// about:debugging#workers

var gks_notification_via_worker=false;
var gks_notification_worker;
jQuery(document).ready(function($) {
  
  if (window.SharedWorker) {
    gks_notification_worker = new SharedWorker('/my/js/_notification_worker.js?v=15');
    gks_notification_worker.port.onmessage = function(e) {
      if (e.data.type === 'ping') {
        gks_notification_worker.port.postMessage({ type: 'pong' });
        return;
      }
      const data = e.data;
      gks_notification_timer_func_after(data);
    };
    gks_notification_worker.onerror = function(e) {
      console.error('[SharedWorker] Error:', e.message, e.filename, e.lineno);
    };
    gks_notification_worker.port.start();
    gks_notification_via_worker=true;
  } else {
    //Fallback
    console.log('SharedWorker no support');
  }
  
  is_notification_page=false;
  //if (window.location.pathname=='/my/admin-notification.php') is_notification_page=true;
  
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
  
  
  
  function gks_notification_timer_func() {
    $.ajax({
  		url: '/my/admin-notification_get_data.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			//console.log('error:' + jqXHR.responseText);
  			if (gks_notification_via_worker==false) {
  			  notification_timer = setTimeout(gks_notification_timer_func, 15000);
  			}
  		},
  		success: function(data) {
  		  if (gks_notification_via_worker==false) {
  		    notification_timer = setTimeout(gks_notification_timer_func, 15000);
  		  }
  			if (!data) {
  				//console.log('error:gks_notification_timer_func');
  			} else {
          gks_notification_timer_func_after(data);
  			}
  		}
  	});
  }

  function gks_notification_timer_func_after(data) {
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
					  //myhtml='<div class="div_notification" data-id="' + mynotif[i].id + '" style="bottom:' + notification_buttom(curr_count) + 'px;">' +
					  myhtml='<div class="div_notification" data-id="' + mynotif[i].id + '">' +
					  '<img class="close_notification" data-id="' + mynotif[i].id + '" src="img/delete.png" border="0">' +
					  //'<i class="fas fa-times close_notification" data-id="' + mynotif[i].id + '"></i>' +
					  
					  '<div class="ago_notification" data-id="' + mynotif[i].id + '">' + mynotif[i].ago + '</div>' +
					  '<div class="divtext_notification">' + mynotif[i].text  + '</div>' +
					  '<div class="divcombo_notification">' +
					  gks_lang('Αναβολή') + ': <select class="snooze_notification" data-id="' + mynotif[i].id + '">' + 
					  '<option value="0"></option>' + 
					  '<option value="1">' + gks_lang('1 ώρα') + '</option>' + 
					  '<option value="2">' + gks_lang('2 ώρες') + '</option>' + 
					  '<option value="3">' + gks_lang('3 ώρες') + '</option>' + 
					  '<option value="4">' + gks_lang('4 ώρες') + '</option>' + 
					  '<option value="8">' + gks_lang('8 ώρες') + '</option>' + 
					  '<option value="24">' + gks_lang('24 ώρες') + '</option>' + 
					  '<option value="48">' + gks_lang('48 ώρες') + '</option>' + 
					  '</select>' + 
					  '</div>' +
					  
					  '</div>';
					  //$('#div_html_notification').append(myhtml);
					  $('#div_notification_footer').after(myhtml);
					  $('.close_notification[data-id=' + mynotif[i].id + ']').click(close_notification_click);
					  $('.snooze_notification[data-id=' + mynotif[i].id + ']').change(snooze_notification_change);
					  
					  
					  
					  $('.div_notification[data-id=' + mynotif[i].id + ']').fadeIn(1000);
					}
			  }
			  
        notification_reorder(); 
        if (mynotif.length==0) $('#div_notification_footer').hide(); 
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
			//console.log('error:gks_notification_timer_func:' + $.base64.decode(data.message));
			if (typeof(data.user_not_login) !== 'undefined' && data.user_not_login && 
			    typeof(data.redirectto) !== 'undefined' && data.redirectto!='') {
			  need_save=false;
			  window.location.href=data.redirectto;      
			}
		}
  }
  if (gks_notification_via_worker==false) {
    var notification_timer = setTimeout(gks_notification_timer_func, 5000);
  }
  

  
  function notification_reorder() {
    var cdiv_count=0;
    var elems_div_notification=$('.div_notification');
//    elems_div_notification.each(function() {
//      cdiv_count++;
//      nbottom=notification_buttom(cdiv_count) + 'px';
//      ebottom=$(this).css('bottom');
//      if (ebottom!=nbottom) {
//        //$(this).css('bottom',ebottom).animate({'bottom':nbottom}, 'slow');
//      }
//    });
    dheight=elems_div_notification.length*(110+2); //124+2
    if (elems_div_notification.length>=1) {
      dheight+=42;
      $('#div_notification_footer').show();  
      if (elems_div_notification.length>=2) {
        $('#cmdSetAllAsReadfloat').show();
      } else {
        $('#cmdSetAllAsReadfloat').hide();
      }
    } else {
      $('#div_notification_footer').hide();  
    }
    //if (dheight > $(window).height()-88) dheight=$(window).height()-88;
    if (dheight>$(window).height()/2) dheight=$(window).height()/2;
    
    $('#div_html_notification').css('height',dheight + 'px');
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
    $('#dialog_notification_user').focus();      
  });
  
  dialog_notification = $( "#dialog_notification" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_notification_ok",
        html: '<i class="fa fa-paper-plane"></i> '+gks_lang('OK'),
        click: function() {
          myto=parseInt($('input[name=dialog_notification_to]:checked').val());
          if (myto!=0) {
            myto=parseInt($('#dialog_notification_user').attr('data-id'));
            if (isNaN(myto)) myto=0;
            if (myto<=0) {
              myalert('error:' + gks_lang('Επιλέξτε τον παραλήπτη')); return;  
            }
          }
          mytext=$('#dialog_notification_message').val().trim();
          if (mytext=='') {
            myalert('error:' + gks_lang('Πληκτρολογήστε το μήνυμά σας')); return;
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
        					myalert('ok:' + gks_lang('Το μήνυμα έχει καταχωρηθεί'));
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		});     
    
    
                
        }
      },
      {
        id: "dialog_notification_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $(this).dialog('close');
        }
      } 
    ]
  });
  
  $('#dialog_notification_user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        company: 1,
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
  
  $( window ).resize(function() {
    notification_reorder();
  });  

  $('#cmdSetAllAsReadfloat').click(function() {
    $.ajax({
			url: '/my/admin-notification_done.php?all=1',
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
  					//window.location.reload();
  					$('.div_notification').each(function() {
  					  $(this).remove();
  					});
  					$('#div_notification_footer').hide();
  					
  					if (gks_notification_via_worker==false) {
    					clearTimeout(notification_timer);
    					notification_timer = setTimeout(gks_notification_timer_func, 1000);
    				}
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		}); 
		
		
  });
  
  $('#notif_img_hide').click(function() {
    $('#div_html_notification').hide('slow');
  });
  

});  