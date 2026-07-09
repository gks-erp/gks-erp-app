/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



function gks_tinymce_init(gks_selector) {
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
        
    selector: gks_selector,
    init_instance_callback: function(editor) {
      editor.on('Change', function(e) {
        need_save=true;
      });
    },
    readonly : (from_php_perm_ret_edit ? 0 : 1),
  });
  //console.log('gks_tinymce_init',gks_selector);
}
gks_tinymce_init('.gks_tinymce');



var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;


jQuery(document).ready(function($) {
  
  var control_enter_active=false;
  
  $(document).on('keypress', function(event) {
    //var tag = e.target.tagName.toLowerCase();
    
    
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });


  $('#mydate_send, #mydate_return').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  





  if ($('#aitiolog').length>0) {
    function aitiolog_change() {gks_resize_textarea($(this));}
    $('#aitiolog').on(mychange, aitiolog_change);
    gks_resize_textarea($('#aitiolog'));  
    
  }

  if ($('#aitiolog2').length>0) {
    function aitiolog2_change() {gks_resize_textarea($(this));}
    $('#aitiolog2').on(mychange, aitiolog2_change);
    gks_resize_textarea($('#aitiolog2'));  
  }



  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });


  

      
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
    
  function mysubmit() {
    
    datasend='';
    datasend+='&mydate_send='  + encodeURIComponent($.base64.encode($("#mypostform #mydate_send").val().trim()));
    datasend+='&asset_id='  + encodeURIComponent($("#mypostform #asset_id").attr('data-id').trim());
    datasend+='&warehouse_id='  + encodeURIComponent($("#mypostform #warehouse_id").attr('data-id').trim());
    datasend+='&reason_id='  + encodeURIComponent($("#mypostform #reason_id").val().trim());
    datasend+='&aitiolog='  + encodeURIComponent($.base64.encode($("#mypostform #aitiolog").val().trim()));
    datasend+='&mixanikos_id='  + encodeURIComponent($("#mypostform #mixanikos_id").attr('data-id').trim());
    datasend+='&mydate_return='  + encodeURIComponent($.base64.encode($("#mypostform #mydate_return").val().trim()));
    datasend+='&aitiolog2='  + encodeURIComponent($.base64.encode($("#mypostform #aitiolog2").val().trim()));
    datasend+='&ajia='  + encodeURIComponent($("#mypostform #ajia").val().trim());
    datasend+='&isconfirm=' + (($('#isconfirm').is(':checked')) ? '1':'0');

    
    datasend+=gks_custom_datasend();
    
    //console.log(datasend);

    
    

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-service-item-exec.php?id=' + from_php_id,
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
    
  
   
  $('#asset_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        av:1,
        uav:1,
        andservice:1
      };
      $.ajax({
        url: 'admin-autocomplete-asset.php',
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
      $('#asset_id').attr('data-id',ui.item.id);
      curr_asset_type=ui.item.asset_type;
      filter_reason_id(curr_asset_type);  
      if (typeof(ui.item.asset_last_warehouse_id) !== 'undefined' && ui.item.asset_last_warehouse_id>0) {
        $('#warehouse_id').val(ui.item.warehouse_name).attr('data-id',ui.item.asset_last_warehouse_id);  
      }  
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#asset_id').val('').attr('data-id','0');
        from_php_prev_asset_type=0;
        $('#reason_id').val('0');
      }
    }
  });

  function filter_reason_id(val) {
    var curr_asset_type=val;
    //console.log(curr_asset_type);
    curr_reason_id=$('#reason_id').val();
    $('#reason_id option').each(function() {
      data_value= $(this).attr('value'); 
      if (data_value != '0') {
        data_types= $(this).attr('data-types'); 
        if (data_types.includes('[' + curr_asset_type + ']')) {
          $(this).show();
        } else {
          $(this).hide();
        }
      }
    });
    
    if ($('#reason_id option[value=' + curr_reason_id + ']').css('display') == 'none') {
      $('#reason_id').val('0');
    }     
    from_php_prev_asset_type = curr_asset_type;
  }
  
  
  filter_reason_id(from_php_prev_asset_type);
     

  $('#warehouse_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-warehouse.php',
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
      $('#warehouse_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#warehouse_id').val('').attr('data-id','0');
      }
    }
  });  

  $('#mixanikos_id').autocomplete({
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
    delay: 300, //default
    autoFocus: true,
    select: function( event, ui ) {
      $('#mixanikos_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#mixanikos_id').val('').attr('data-id','0');
      }
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
  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });
  window.onbeforeunload = function() {
    
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };
  need_save=false;
  //console.log('ready');
});



