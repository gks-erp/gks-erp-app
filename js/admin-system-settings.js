/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


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

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});  
  function mysubmit() {

    
    
    datasend='';
    
    var user_roles='';
    $('.rolecheckbox').each(function() {
      if ($(this).is(':checked')) user_roles+='&' + $(this).attr('id') + '=1';
    });
    //console.log(user_roles);
    datasend+=user_roles;
    
    datasend+='&GKS_SITE_HUMAN_NAME='  + encodeURIComponent($.base64.encode($("#GKS_SITE_HUMAN_NAME").val().trim()));
    datasend+='&GKS_OFFICIAL_SITE_URL='  + encodeURIComponent($.base64.encode($("#GKS_OFFICIAL_SITE_URL").val().trim()));
    datasend+='&GKS_SITE_NAME='  + encodeURIComponent($.base64.encode($("#GKS_SITE_NAME").val().trim()));
    datasend+='&GKS_LANG_DEFAULT='  + encodeURIComponent($.base64.encode($("#GKS_LANG_DEFAULT").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_DECIMAL='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_DECIMAL").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_THOUSAND='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_THOUSAND").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_CURRENCY_DECIMAL='  + encodeURIComponent($("#GKS_NUMBER_FORMAT_CURRENCY_DECIMAL").val().trim());
    datasend+='&GKS_NUMBER_FORMAT_CURRENCY_SYMBOL='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_CURRENCY_SYMBOL").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_DATE='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_DATE").val().trim()));
    datasend+='&GKS_NUMBER_FORMAT_TIME='  + encodeURIComponent($.base64.encode($("#GKS_NUMBER_FORMAT_TIME").val().trim()));
    
    

    datasend+='&GKS_PRODUCT_DESCR_SMALL=' + encodeURIComponent(($("#GKS_PRODUCT_DESCR_SMALL").is(':checked') ? '1' : '0'));
    datasend+='&GKS_PRODUCT_DESCR_BIG=' + encodeURIComponent(($("#GKS_PRODUCT_DESCR_BIG").is(':checked') ? '1' : '0'));
    
    datasend+='&GKS_HOTEL_BACKEND=' + encodeURIComponent(($("#GKS_HOTEL_BACKEND").is(':checked') ? '1' : '0'));
    datasend+='&GKS_HOTEL_RESERVATIONS_ONLINE=' + encodeURIComponent(($("#GKS_HOTEL_RESERVATIONS_ONLINE").is(':checked') ? '1' : '0'));
    
    datasend+='&GKS_CRM_ENABLE=' + encodeURIComponent(($("#GKS_CRM_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_CRM_LEADS_ENABLE=' + encodeURIComponent(($("#GKS_CRM_LEADS_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_CRM_TASKS_ENABLE=' + encodeURIComponent(($("#GKS_CRM_TASKS_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_CRM_MACHINE_ENABLE=' + encodeURIComponent(($("#GKS_CRM_MACHINE_ENABLE").is(':checked') ? '1' : '0'));
    
    datasend+='&GKS_WARE_HOUSE_ENABLE=' + encodeURIComponent(($("#GKS_WARE_HOUSE_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_PRODUCT_LOTS_SERIALS=' + encodeURIComponent(($("#GKS_PRODUCT_LOTS_SERIALS").is(':checked') ? '1' : '0'));
    
    
    
    datasend+='&GKS_ORDER_DEFAULT_DELIVERY='  + encodeURIComponent($("#GKS_ORDER_DEFAULT_DELIVERY").val().trim());
    datasend+='&GKS_ORDER_DEFAULT_PAYMENT='  + encodeURIComponent($("#GKS_ORDER_DEFAULT_PAYMENT").val().trim());
    
    
    datasend+='&GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK=' + encodeURIComponent(($("#GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK").is(':checked') ? '0' : '1'));
    datasend+='&GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK=' + encodeURIComponent(($("#GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK").is(':checked') ? '0' : '1'));
    datasend+='&GKS_BASKET_ROUND_DIAFORA_001=' + encodeURIComponent(($("#GKS_BASKET_ROUND_DIAFORA_001").is(':checked') ? '1' : '0'));
    datasend+='&GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI=' + encodeURIComponent(($("#GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI").is(':checked') ? '1' : '0'));
    datasend+='&GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI=' + encodeURIComponent(($("#GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI").is(':checked') ? '1' : '0'));
    datasend+='&GKS_INPUT_STEP_AJIA='  + encodeURIComponent($.base64.encode($("#GKS_INPUT_STEP_AJIA").val().trim()));
    datasend+='&GKS_INPUT_STEP_POSOTITA='  + encodeURIComponent($.base64.encode($("#GKS_INPUT_STEP_POSOTITA").val().trim()));
    datasend+='&GKS_INPUT_STEP_POSOSTO='  + encodeURIComponent($.base64.encode($("#GKS_INPUT_STEP_POSOSTO").val().trim()));
    datasend+='&GKS_BASKET_CALC_ITEM_DECIMAL='  + encodeURIComponent($("#GKS_BASKET_CALC_ITEM_DECIMAL").val().trim());
    datasend+='&GKS_BASKET_CALC_EKPTOSI_DECIMAL='  + encodeURIComponent($("#GKS_BASKET_CALC_EKPTOSI_DECIMAL").val().trim());
    
    
    
    datasend+='&GKS_ORDERS_ENABLE=' + encodeURIComponent(($("#GKS_ORDERS_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_COL_ITEMPRICE=' + encodeURIComponent(($("#GKS_ORDERS_COL_ITEMPRICE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA=' + encodeURIComponent(($("#GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_COL_FPA=' + encodeURIComponent(($("#GKS_ORDERS_COL_FPA").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_SETS=' + encodeURIComponent(($("#GKS_ORDERS_SETS").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_SETS_VALS=' + encodeURIComponent($.base64.encode($("#GKS_ORDERS_SETS_VALS").val().trim()));
    datasend+='&GKS_ORDERS_SHEETS=' + encodeURIComponent(($("#GKS_ORDERS_SHEETS").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_OCCASION=' + encodeURIComponent(($("#GKS_ORDERS_OCCASION").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ORDERS_PRODUCTION=' + encodeURIComponent(($("#GKS_ORDERS_PRODUCTION").is(':checked') ? '1' : '0'));


    datasend+='&GKS_ACC_ENABLE=' + encodeURIComponent(($("#GKS_ACC_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ACC_INV_COL_ITEMPRICE=' + encodeURIComponent(($("#GKS_ACC_INV_COL_ITEMPRICE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=' + encodeURIComponent(($("#GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ACC_INV_COL_FPA=' + encodeURIComponent(($("#GKS_ACC_INV_COL_FPA").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ACC_INV_EXTRA_OPEN=' + encodeURIComponent(($("#GKS_ACC_INV_EXTRA_OPEN").is(':checked') ? '1' : '0'));

    datasend+='&GKS_ASSETS_ENABLE=' + encodeURIComponent(($("#GKS_ASSETS_ENABLE").is(':checked') ? '1' : '0'));
    datasend+='&GKS_ERP_APP_MOBILE_VER=' + encodeURIComponent($('#GKS_ERP_APP_MOBILE_VER').val());

    
    datasend+='&GKS_SITE_EMAIL='  + encodeURIComponent($.base64.encode($("#GKS_SITE_EMAIL").val().trim()));
    datasend+='&GKS_EMAIL_HOST='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_HOST").val().trim()));
    datasend+='&GKS_EMAIL_PORT='  + encodeURIComponent($("#GKS_EMAIL_PORT").val().trim());
    datasend+='&GKS_EMAIL_SMTPAUTH='  + encodeURIComponent(($("#GKS_EMAIL_SMTPAUTH").is(':checked') ? '1' : '0'));
    datasend+='&GKS_EMAIL_USERNAME='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_USERNAME").val().trim()));
    datasend+='&GKS_EMAIL_PASSWORD='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_PASSWORD").val().trim()));
    datasend+='&GKS_EMAIL_BCC1='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_BCC1").val().trim()));
    datasend+='&GKS_EMAIL_BCC2='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_BCC2").val().trim()));
    datasend+='&GKS_EMAIL_BCC3='  + encodeURIComponent($.base64.encode($("#GKS_EMAIL_BCC3").val().trim()));
    
    datasend+='&GKS_SMS_SENDER='  + encodeURIComponent($.base64.encode($("#GKS_SMS_SENDER").val().trim()));
    datasend+='&GKS_SMS_TOKEN='  + encodeURIComponent($.base64.encode($("#GKS_SMS_TOKEN").val().trim()));
    
    datasend+='&GKS_VIBER_URI='  + encodeURIComponent($.base64.encode($("#GKS_VIBER_URI").val().trim()));
    datasend+='&GKS_VIBER_TOKEN='  + encodeURIComponent($.base64.encode($("#GKS_VIBER_TOKEN").val().trim()));
    
    
    datasend+='&GKS_GOOGLE_MAPS_API_KEY='  + encodeURIComponent($.base64.encode($("#GKS_GOOGLE_MAPS_API_KEY").val().trim()));
    datasend+='&GKS_GOOGLE_MAPS_API_KEY_SERVER='  + encodeURIComponent($.base64.encode($("#GKS_GOOGLE_MAPS_API_KEY_SERVER").val().trim()));
    datasend+='&GKS_AWS_BUCKET='  + encodeURIComponent($.base64.encode($("#GKS_AWS_BUCKET").val().trim()));
    datasend+='&GKS_AWS_KEY='  + encodeURIComponent($.base64.encode($("#GKS_AWS_KEY").val().trim()));
    datasend+='&GKS_AWS_SECRET='  + encodeURIComponent($.base64.encode($("#GKS_AWS_SECRET").val().trim()));
    datasend+='&GKS_AWS_FOLDER='  + encodeURIComponent($.base64.encode($("#GKS_AWS_FOLDER").val().trim()));
    datasend+='&GKS_SEND_ANYWHERE_API_KEY='  + encodeURIComponent($.base64.encode($("#GKS_SEND_ANYWHERE_API_KEY").val().trim()));
    datasend+='&GKS_AADE_MYDATA_SANDBOX_AFM='  + encodeURIComponent($.base64.encode($("#GKS_AADE_MYDATA_SANDBOX_AFM").val().trim()));
    datasend+='&GKS_AADE_MYDATA_SANDBOX_BRANCE='  + encodeURIComponent($("#GKS_AADE_MYDATA_SANDBOX_BRANCE").val().trim());
    datasend+='&GKS_AADE_MYDATA_SANDBOX_USER_ID='  + encodeURIComponent($.base64.encode($("#GKS_AADE_MYDATA_SANDBOX_USER_ID").val().trim()));
    datasend+='&GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY='  + encodeURIComponent($.base64.encode($("#GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY").val().trim()));


    datasend+='&custom_css_global=' + encodeURIComponent($.base64.encode(custom_css_global_editor.getValue()));
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-system-settings-exec.php',
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
  }
  
  $('#goback').click(function() {
    window.location.href='/my';  
  });

  var myswitchery = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
  myswitchery.forEach(function(html) {
    var switchery = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });    
    
  
  
  
  $('#GKS_VIBER_URI').on('change keyup paste',function() {
    myuri=$(this).val().trim();  
    if (myuri=='') {
      $('#GKS_VIBER_URI_link').html('').hide();
    } else {
      myhtml='<a href="viber://pa/info?uri=' + myuri + '" target="_blank">viber://pa/info?uri=' + myuri + '</a>';
      $('#GKS_VIBER_URI_link').html(myhtml).show();
    }
  });
  $('#GKS_VIBER_TOKEN').on('change keyup paste',function() {
    myuri=$(this).val().trim();  
    if (myuri=='') {
      $('#viber_hook_page_div').hide();
    } else {
      myhtml='<a href="viber://pa/info?uri=' + myuri + '" target="_blank">viber://pa/info?uri=' + myuri + '</a>';
      $('#viber_hook_page_div').show();
    }
  });
  
  
  $('#viber_hook_page_set').click(function() {
    if ($("#GKS_VIBER_TOKEN").val().trim()=='') {
      //myalert('error:'+gks_lang('Ορίστε το Token'));
      //return;
    }

    datasend='';
    datasend+='&GKS_VIBER_TOKEN='  + encodeURIComponent($.base64.encode($("#GKS_VIBER_TOKEN").val().trim()));
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-system-settings-viber-hook-set.php',
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
  					myalert('ok:' + $.base64.decode(data.message));
  					$('#viber_hook_page_span').html('<span style="color:white;padding:4px 10px;;border-radius:10px;font-size:80%;background-color:green;">'+gks_lang('Έχει ορισθεί σελίδα')+'</span>');
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('#viber_hook_page_span').html('<span style="color:white;padding:4px 10px;;border-radius:10px;font-size:80%;background-color:red;">'+gks_lang('Δεν έχει ορισθεί σελίδα')+'</span>');
					}
				}
			}
			
		});     

  });
  
  $('#GKS_HOTEL_BACKEND').change(function() {
    if ($(this).is(':checked')) {
      $('#GKS_HOTEL_BACKEND_div').slideDown();
    } else {
      $('#GKS_HOTEL_BACKEND_div').slideUp();
    }
  });  
  
  $('#GKS_CRM_ENABLE').change(function() {
    if ($(this).is(':checked')) {
      $('#GKS_CRM_ENABLE_div').slideDown();
    } else {
      $('#GKS_CRM_ENABLE_div').slideUp();
    }
  });


  
  $('#GKS_ORDERS_ENABLE').change(function() {
    if ($(this).is(':checked')) {
      $('#GKS_ORDERS_ENABLE_div').slideDown();
    } else {
      $('#GKS_ORDERS_ENABLE_div').slideUp();
    }
    deltia_praggelies_parastatika();
  });
  $('#GKS_ACC_ENABLE').change(function() {
    if ($(this).is(':checked')) {
      $('#GKS_ACC_ENABLE_div').slideDown();
    } else {
      $('#GKS_ACC_ENABLE_div').slideUp();
    }
    deltia_praggelies_parastatika();
  });  
  
  $('#GKS_WARE_HOUSE_ENABLE').change(function() {
    deltia_praggelies_parastatika()
  });
  
  function deltia_praggelies_parastatika() {
    
    if ($('#GKS_ORDERS_ENABLE').is(':checked') || 
        $('#GKS_ACC_ENABLE').is(':checked') || 
        $('#GKS_WARE_HOUSE_ENABLE').is(':checked')) {
          
      $('#deltia_praggelies_parastatika').slideDown();      
    } else {
      $('#deltia_praggelies_parastatika').slideUp();
    }
  }
  
  $('#GKS_ORDERS_SETS').change(function() {
    if ($(this).is(':checked')) {
      $('#div_GKS_ORDERS_SETS_VALS').slideDown();
    } else {
      $('#div_GKS_ORDERS_SETS_VALS').slideUp();
    }
  });
  

  $('#GKS_ORDERS_SETS_VALS').tagit({
    allowSpaces: true, 
    singleFieldDelimiter: ']][[',
    removeConfirmation: true,
    availableTags: [],
    showAutocompleteOnFocus : false,
    afterTagAdded:function()   {need_save=true;},
    afterTagRemoved:function() {need_save=true;},
  });
  
  var elems_roles = Array.prototype.slice.call(document.querySelectorAll('.rolecheckbox'));
  elems_roles.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  
  
  
  var custom_css_global_editor = CodeMirror.fromTextArea(document.getElementById("custom_css_global"), {
    lineNumbers: true,
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
      },
      "F11": function(cm) {
        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
      },
      "Esc": function(cm) {
        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
      }      
    },
    mode: {name: "css",globalVars: true }, //
    lineNumbers: true,
    spellcheck: true,
    autocorrect: true,
    autocapitalize: true,
    indentUnit:2,
    tabSize: 2,
    indentWithTabs:false,
    smartIndent:true,
    autoCloseBrackets: true,
    styleActiveLine: true,
    lineWrapping: true,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    //viewportMargin: 20, //Infinity
  });


  var jqXHR_array=[];
  $('.gks_logo_form').each(function() {
    cc=parseInt($(this).attr('data-cc'));if (isNaN(cc)) cc=0;
    if (cc>0) {

      jqXHR_array.push($('.gks_logo_form[data-cc=' + cc + ']').fileupload({
          dropZone:$('.f_button_add_files_photo[data-cc=' + cc + ']'),
        
          dataType: 'json',
          limitConcurrentUploads: 1,
          add: function (e, data) {
            
              var uploadErrors = [];
              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(data.originalFiles[0]['name']);
              ext=ext[0].toLowerCase();
              
              var acceptFileTypes = ['.png'];
              if(acceptFileTypes.indexOf(ext)<0) {
                  uploadErrors.push("Αρχείο: " + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
              }
              if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
                  uploadErrors.push("Αρχείο: " + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
              }
              
              cc=data.form.attr('data-cc');
              data.gkscc=cc;
              data.gksfilename=data.form.attr('data-filename');
              //$('.gks_logo_row[data-cc=' + cc + '] .gks_logo_img img').attr('src','img/wait.gif');
              $('.gks_logo_row[data-cc=' + cc + '] .gks_logo_wait').slideDown();
            
              if(uploadErrors.length > 0) {
                  myalert('error:' + uploadErrors.join("\n"));
              } else {
                data.submit();
                $('.progress-bar_photo[data-cc=' + cc + ']').show();
                $('.progress-extended_photo[data-cc=' + cc + ']').show();
              }
          },
          done: function (e, data) {
              $.each(data.result.files, function (index, file) {
                if (typeof file.error == 'undefined') {
                  cc=e.target.getAttribute('data-cc');
                  fff='_current/_img_site/' + e.target.getAttribute('data-filename') + '?v=' + (new Date()).getTime();
                  $('.gks_logo_row[data-cc=' + cc + '] .gks_logo_img img').attr('src',fff);
                  $('.gks_logo_row[data-cc=' + cc + '] .gks_logo_wait').slideUp();
                  $('.logo_update_show[data-cc=' + cc + ']').slideDown();
                } else {
                  myalert('error:' + file.error);
                }
              });
          },
          progressall: function (e, data) {
            cc=e.target.getAttribute('data-cc');
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.progress-bar_photo[data-cc=' + cc + '] .bar_photo').css(
                'width',
                progress + '%'
            );
            $('.progress-extended_photo[data-cc=' + cc + ']').html(_renderExtendedProgress(data));
          },
          fail: function (e, data) {
            myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε'));
            cc=e.target.getAttribute('data-cc');
            $('.gks_logo_row[data-cc=' + cc + '] .gks_logo_wait').slideUp();
          },
          progress: function (e, data) {
            //var progress = parseInt(data.loaded / data.total * 100, 10);
            //$('#progressfile_photo' + data.gkscc + ' .bar_photo').css(
            //    'width',
            //    progress + '%'
            //);
          },
          stop: function (e) {
            cc=e.target.getAttribute('data-cc');
            $('.progress-bar_photo[data-cc=' + cc + ']').hide();
            $('.progress-extended_photo[data-cc=' + cc + ']').hide();
            $('.gks_logo_row[data-cc=' + cc + '] .gks_logo_wait').slideUp();
          },
          
      }));
    }
        
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
  
  
  
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;

  if (window.location.href.includes('#google_keys')) {
      newurl=window.location.href.replace('#google_keys','');
			window.history.pushState({}, window.document.title, newurl);
      $([document.documentElement, document.body]).animate({
        scrollTop: ($('#GKS_GOOGLE_MAPS_API_KEY').offset().top - 100)
      }, 1000, 'swing', function() {
        $('#GKS_GOOGLE_MAPS_API_KEY').focus();

      });
  }
  
});
