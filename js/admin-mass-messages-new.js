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
  readonly :0,
    
});

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

jQuery(document).ready(function($) {
  

  function messagesms_countchars(a) {
    var mystring = a.trim();
    var aa=mystring.length;

    cc1 = (mystring.match(/\^/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\{/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/}/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\[/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/]/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/~/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\\/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\|/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/€/g) || []).length;
    aa=aa+cc1; 
    return aa;     
    
    //             ^ { } [ ] ~ \ | €    
  }
  
  
  
  function messagesms_change() {
    var aa=messagesms_countchars( $("#message").val() );

    if (aa>=0 && aa<=160) {
      smscc=1;
      leftcc=160-aa;
    } else if (aa<=306) {
      smscc=2;
      leftcc=306-aa;
    } else if (aa<=459) {
      smscc=3;
      leftcc=459-aa;
    } else if (aa<=612) {
      smscc=4;
      leftcc=612-aa;
    } else if (aa<=765) {
      smscc=5;
      leftcc=765-aa;
    } else if (aa<=918) {
      smscc=6;
      leftcc=918-aa;
    } else {
      smscc=0;
      leftcc=0;
    }
    aa=smscc + ' SMS, ' + leftcc + ' '+ gks_lang('εναπομείναντες χαρακτήρες');
    $("#chars").html(aa);    
  }
  
  $("#message").on('change keyup paste', function() {
    messagesms_change();
  });       
  messagesms_change();
      
  
  $('#mybutton_test').click(function() {
    mybutton_run(false);
  });    
  $('#mybutton').click(function() {
    mybutton_run(true);
  });
  
  function mybutton_run(isrealsend) {
    if ($("#message").val().trim().length < 2) {
      myalert('error:'+gks_lang('Εισάγετε κάποιο μήνυμα'));
      return false;
    }
    
    smslen=messagesms_countchars( $("#message").val() );
    if (smslen> 918) {
      myalert('error:'+gks_lang('Πολύ μεγάλο μέγεθος κειμένου'));
      return false;
    }  

    var datasend='';
    datasend+='&isrealsend=' + (isrealsend ? '1' : '0');
    datasend+='&sender_sms_provider='  + encodeURIComponent($.base64.encode($('#from option:selected').attr('data-provider')));
    datasend+='&sender_sms_sender='  + encodeURIComponent($.base64.encode($('#from option:selected').attr('data-sender')));
    //from=' + encodeURIComponent($.base64.encode($("#from").val().trim()));
    datasend+='&message=' + encodeURIComponent($.base64.encode($("#message").val().trim()));
    
    
    var mylist=[];
    if (isrealsend) {
      $('#users_list .user_result').each(function() {
        myiddd=parseInt($(this).attr('data-id'));
        if (isNaN(myiddd)) myiddd=0;
        if (myiddd>0) mylist.push(myiddd);
      });
      //console.log(mylist);
      datasend+='&mylist_str=' + encodeURIComponent($.base64.encode(JSON.stringify(mylist)));
    } else {
      datasend+='&test_send_sms='   + encodeURIComponent($.base64.encode($('#test_send_sms').val()));
      datasend+='&test_send_viber=' + encodeURIComponent($.base64.encode($('#test_send_viber').val()));
      datasend+='&test_send_email=' + encodeURIComponent($.base64.encode($('#test_send_email').val()));
    }
  
    datasend+='&send_with_sms=' + ($('#send_with_sms').is(':checked') ? '1' : '0');
    datasend+='&send_with_viber=' + ($('#send_with_viber').is(':checked') ? '1' : '0');
    datasend+='&send_with_email=' + ($('#send_with_email').is(':checked') ? '1' : '0');
    datasend+='&send_with_email_from=' + encodeURIComponent($.base64.encode($('#send_with_email_from').val()));
    datasend+='&send_with_email_template=' + encodeURIComponent($('#send_with_email_template').val());
    datasend+='&send_with_email_subject=' + encodeURIComponent($.base64.encode($('#send_with_email_subject').val()));
    datasend+='&viberbuttons=' + encodeURIComponent($.base64.encode($('#viberbuttons').val()));


    
    $("body").addClass("myloading");
	  $.ajax({
			url: '/my/admin-mass-messages-exec.php', 
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_isrealsend:isrealsend,
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
  				if (data.success == false){
  					if (data.message.length > 0){
  						myalert('error:' + $.base64.decode(data.message));
  						return;
  					} else {
  					  myalert('error:'+gks_lang('Σφάλμα')+'<br>'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  					  return;
  					}
  				} else {
  				  myalert('ok:' + $.base64.decode(data.message));
  				  
  				  $('#send_exec_result').html(data.out);
  				  $('#send_exec_result_table').show();
  				  if (this.gks_isrealsend) {
  				    need_save=false;
  				    $('#mybutton_test').prop('disabled', true);
  				    $('#mybutton').prop('disabled', true);
  				    
  				  } else {
  				    $('#mybutton').prop('disabled', false);
  				  }
  				  return;
  				}
  			}
			}
		});          
		
    
  }
  

  
  $("#make_search").click(function() {
    need_save=true;
    datasend='';
    var myroles=[];
    $('input.rolecheckbox:checked').each(function() {
      myroles.push($(this).val());
    });
    //console.log(myroles);
    datasend+='&myroles_str=' + encodeURIComponent($.base64.encode(JSON.stringify(myroles)));

    var mygroups=[];
    $('input.groupcheckbox:checked').each(function() {
      mygroups.push($(this).val());
    });
    //console.log(mygroups);
    datasend+='&mygroups_str=' + encodeURIComponent($.base64.encode(JSON.stringify(mygroups)));

    datasend+='&not_work_date_check=' + ($('#not_work_date_check').is(':checked') ? '1' : '0');
    datasend+='&not_work_date=' + encodeURIComponent($('#not_work_date').val());
    datasend+='&not_work_date_duration=' + $('#not_work_date_duration').val();

    datasend+='&work_date_check=' + ($('#work_date_check').is(':checked') ? '1' : '0');
    datasend+='&work_date=' + encodeURIComponent($('#work_date').val());
    datasend+='&work_date_duration=' + $('#work_date_duration').val();

    
    //console.log(datasend);
    $('#search_results').html(gks_lang('Εδώ θα εμφανιστούν τα αποτελέσματα<br>Παρακαλώ περιμένετε')+' ...');
    $('#results_button').hide();
     
    
    
    $("body").addClass("myloading");
	  $.ajax({
			url: '/my/admin-mass-messages-search.php',
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
  				if (data.success == false){
  					if (data.message.length > 0){
  						myalert('error:' + $.base64.decode(data.message));
  						return;
  					} else {
  					  myalert('error:'+gks_lang('Σφάλμα')+'<br>'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  					  return;
  					}
  				} else {
  				  //console.log(data.data);
  				  $('#search_results').html('');
  				  out_html='';
  				  for (i=0; i< data.data.length; i++) {
  				    
  				    if ($('#users_list .user_result[data-id=' + data.data[i].i + ']').length==0) {
    				    out_html+=
    				    '<div class="user_result" data-id="' + data.data[i].i + '"' +
    				    ' data-m="' + data.data[i].m + '"' +
    				    ' data-v="' + data.data[i].v + '"' +
    				    ' data-e="' + data.data[i].e + '"' +
    				    '>' +
    				      '<div class="user_result1">' + data.data[i].n + '</div>' +
    				      '<div class="user_result2">' + 
    				        (data.data[i].v==0 ? '' : '<img class="imgviber" src="img/viber.png">') + 
    				        ' ' + 
    				        (data.data[i].m==0 ? '' : '<img class="imgsms" src="img/sms.png">') + 
    				        ' ' + 
    				        (data.data[i].e==0 ? '' : '<img class="imgemail" src="img/email2.png">') + 
    				      '</div>' +
    				      
    				      '<div class="user_result3">' +
    				        '<img class="list_add" src="img/add.png">' +
    				        '<img class="list_del" src="img/delete.png">' +
    				      '</div>' +
    				    '</div>';
    				  }
  				  }
  				  $('#search_results').html(out_html);
  				  if (data.data.length>0) {
  				    $('#search_span2').html(gks_lang('Βρέθηκαν [1] αποτελέσματα').replaceAll('[1]',data.data.length));
  				  } else {
  				    $('#search_span2').html(gks_lang('Δεν βρέθηκαν αποτελέσματα'));
  				  }
  				  
  				  $('#search_results .list_add').click(add_to_list);
  				  $('#search_results .list_del').click(del_to_list);
  				  fix_spans();
  				  
  				  //myalert('ok:' + $.base64.decode(data.message));
  				  return;
  				}
  			}
			}
		});
  });
    
  $('#recommendation_user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        all:1,
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
      
      
      one_user_id=ui.item.id;
      if ($('#users_list .user_result[data-id=' + one_user_id + ']').length>0) {
        myalert('error:'+gks_lang('Υπάρχει ήδη στην λίστα'));
        return;
      }
      
      datasend='&one_user_id='+ui.item.id;
      $("body").addClass("myloading");
  	  $.ajax({
  			url: '/my/admin-mass-messages-search.php',
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
    				if (data.success == false){
    					if (data.message.length > 0){
    						myalert('error:' + $.base64.decode(data.message));
    						return;
    					} else {
    					  myalert('error:'+gks_lang('Σφάλμα')+'<br>'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    					  return;
    					}
    				} else {
    				  //console.log(data.data);
    				  need_save=true;
    				  if (data.data.length==1) {
      				  i=0;
      				  $('#users_list .user_result[data-id=' + data.data[i].i + ']').remove();

    				    out_html=
    				    '<div class="user_result" data-id="' + data.data[i].i + '"' +
    				    ' data-m="' + data.data[i].m + '"' +
    				    ' data-v="' + data.data[i].v + '"' +
    				    ' data-e="' + data.data[i].e + '"' +
    				    '>' +
    				      '<div class="user_result1">' + data.data[i].n + '</div>' +
    				      '<div class="user_result2">' + 
    				        (data.data[i].v==0 ? '' : '<img class="imgviber" src="img/viber.png">') + 
    				        ' ' + 
    				        (data.data[i].m==0 ? '' : '<img class="imgsms" src="img/sms.png">') + 
    				        ' ' + 
    				        (data.data[i].e==0 ? '' : '<img class="imgemail" src="img/email2.png">') + 
    				      '</div>' +
    				      
    				      '<div class="user_result3">' +
    				        '<img class="list_add"    src="img/add.png">' +
    				        '<img class="list_del" src="img/delete.png">' +
    				      '</div>' +
    				    '</div>';
    				    
    				    $('#users_list').append(out_html);
    				    
    				    $('#users_list .user_result[data-id=' + data.data[i].i + '] .list_add').click(add_to_list);
  				      $('#users_list .user_result[data-id=' + data.data[i].i + '] .list_del').click(del_to_list);
                fix_spans();

    				  } else {
                myalert('error:'+gks_lang('Δεν βρέθηκε ο χρήστης'));
    				  }
    				  //myalert('ok:' + $.base64.decode(data.message));
    				  //return;
    				}
    			}
  			}
  		});      
      
      
      
    },
    change: function (event, ui) {
      if(!ui.item){
        $("#recommendation_user").val("");

      }
    }
  });  

  function add_to_list() {
    need_save=true;
    elemid=$(this).parent().parent().attr('data-id');
    $('#users_list .user_result[data-id=' + elemid + ']').remove();
    
    $(this).parent().parent().detach().appendTo('#users_list');
    fix_spans();
  }
  function del_to_list() {
    need_save=true;
    elemid=$(this).parent().parent().attr('data-id');
    $('#search_results .user_result[data-id=' + elemid + ']').remove();

    $(this).parent().parent().detach().appendTo('#search_results');
    fix_spans();
  }
  function fix_spans() {
    if ($('#search_results .user_result').length==0) {
      $('#search_span').show();
      $('#search_span2').hide();
      $('#results_button').hide();
    } else {
      $('#search_span').hide();
      $('#search_span2').show();
      $('#results_button').show();
      $('#search_span2').html(gks_lang('Πλήθος')+': ' + $('#search_results .user_result').length);
    }    
    if ($('#users_list .user_result').length==0) {
      $('#users_span').show();
      $('#users_span2').hide();
      $('#list_button').hide();
    } else {
      $('#users_span').hide();
      $('#users_span2').show();
      $('#list_button').show();
      
      
      cc1=$('#users_list .user_result').length;
      cc2=$('#users_list .user_result[data-v=1]').length;
      cc3=$('#users_list .user_result[data-m=1][data-v=0]').length;
      cc4=$('#users_list .user_result[data-e=1][data-m=0][data-v=0]').length;
      cc5=$('#users_list .user_result[data-e=0][data-m=0][data-v=0]').length;
      myhtml=gks_lang('Πλήθος')+': ' + cc1 + '<br>' +
             gks_lang('Viber')+': ' + cc2 + '<br>' +
             gks_lang('SMS')+': ' + cc3 + '<br>' +
             gks_lang('email')+': ' + cc4 + '<br>' +
             gks_lang('Τίποτα')+': ' + cc5;
      $('#users_span2').html(myhtml);
      
    }
    
    
    
    //users_span
    //search_span
  }
  

  $('#results_button_all').click(function() {
    need_save=true;
    $('#search_results .list_add').each(function() {
      $(this).click();  
    });
    
  });
  $('#list_button_all').click(function() {
    need_save=true;
    $('#users_list .list_del').each(function() {
      $(this).click();  
    });
  });
  
  $('#viberbuttons_samples').change(function() {
    need_save=true;
    vvv=$(this).val();
    ttt='';
    if (vvv=='1') ttt=gks_lang('Ναι') + "\r\n" + gks_lang('Όχι');
    else if (vvv=='2') ttt=gks_lang('Ναι') + "\r\n" + gks_lang('Όχι') + "\r\n" + gks_lang('Ίσως');
    else if (vvv=='3') ttt=gks_lang('Με ενδιαφέρει') + "\r\n" + gks_lang('Δεν με ενδιαφέρει');
    else if (vvv=='4') ttt=gks_lang('Με ενδιαφέρει') + "\r\n" + gks_lang('Δεν με ενδιαφέρει') + "\r\n" + gks_lang('Δεν ξέρω');
    else if (vvv=='5') ttt=gks_lang('1 ώρα') + "\r\n" + gks_lang('1 ημέρα') + "\r\n" + gks_lang('1 εβδομάδα');
    else if (vvv=='6') ttt=gks_lang('Ναι|#006744|#ffffff') + "\r\n" + gks_lang('Όχι|#d80014|#ffffff') + "\r\n" + gks_lang('Ίσως|#fefdb3|#000bf3');
    
    if (ttt!='') $('#viberbuttons').val(ttt);

     
  });


  function message_change() {gks_resize_textarea($(this));}
  $('#message').on(mychange, message_change);
  gks_resize_textarea($('#message'));
  
  function viberbuttons_change() {gks_resize_textarea($(this));}
  $('#viberbuttons').on(mychange, viberbuttons_change);
  gks_resize_textarea($('#viberbuttons'));

  function sendby_checkbox_change() {
    $('.sendby_sms').hide();
    $('.sendby_viber').hide();
    $('.sendby_email').hide();
    if ($('#send_with_sms').is(':checked')) $('.sendby_sms').show();
    if ($('#send_with_viber').is(':checked')) $('.sendby_viber').show();
    if ($('#send_with_email').is(':checked')) $('.sendby_email').show();
  }
  
  $('.sendby_checkbox').change(sendby_checkbox_change);
  sendby_checkbox_change();
  


  $('#test_send_sms').tagit({
    allowSpaces: true, 
    showAutocompleteOnFocus : false,
    removeConfirmation: true,
    singleFieldDelimiter: ']][[',
    //placeholderText:'ssss',
    autocomplete: {
      //source: "admin-autocomplete-comm.php?comm_type=phone",
      source: function(request, response) {
        mydata={
          term: request.term,
          comm_type:'phone',
          fromtagit: 1,
          //mobile: 1,
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
    },
    preprocessTag: function(val) {
      return val;
    },
  });  

  $('#test_send_email').tagit({
    allowSpaces: true, 
    showAutocompleteOnFocus : false,
    removeConfirmation: true,
    singleFieldDelimiter: ']][[',
    //placeholderText:'ssss',
    autocomplete: {
      //source: "admin-autocomplete-comm.php?comm_type=phone",
      source: function(request, response) {
        mydata={
          term: request.term,
          comm_type:'email',
          fromtagit: 1,
          //mobile: 1,
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
    },
    preprocessTag: function(val) {
      return val;
    },
  }); 
  
 
  $('#test_send_viber').tagit({
    allowSpaces: true, 
    showAutocompleteOnFocus : false,
    removeConfirmation: true,
    singleFieldDelimiter: ']][[',
    //placeholderText:'ssss',
    autocomplete: {
      source: function(request, response) {
        mydata={
          term: request.term,
          viber:1,
          fromtagit: 1,
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
      delay: 300,
    },
    preprocessTag: function(val) {
      return val;
    },
  }); 
      
  
  if ($('#users_list .user_result').length>0) {
    //console.log('exist list');  
    $('#users_list .list_add').click(add_to_list);
  	$('#users_list .list_del').click(del_to_list);
  	fix_spans();    
    
  }
    

  //generic
  gks_page_loading=false;

  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };
  need_save=false;
  
  //console.log('load end');
    
});

