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


  $('#mydate').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  
  if (from_php_mystate=='00draft') {
    function whi_mov_sxolio_change() {gks_resize_textarea($(this));}
    $('#whi_mov_sxolio').on(mychange, whi_mov_sxolio_change);
    gks_resize_textarea($('#whi_mov_sxolio'));  
  }

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });


  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
    
  function mysubmit() {

    datasend='';
    datasend+='&assets_whi_mov_status=' + encodeURIComponent($("#assets_whi_mov_status").val().trim());
    datasend+='&mydate=' + encodeURIComponent($("#mydate").val().trim());
    datasend+='&warehouse_id='  + encodeURIComponent($("#warehouse_id").attr('data-id').trim());
    datasend+='&whi_mov_sxolio='  + encodeURIComponent($.base64.encode($("#whi_mov_sxolio").val().trim()));

    
    datasend+=gks_custom_datasend();

    var assets_array=[];
    $('.item_tr').each(function() {
      item={};
      item.rec=$(this).attr('data-rec');
      item.asset_id=parseInt($(this).find('.item_asset').attr('data-id')); if (isNaN(item.asset_id)) item.asset_id=0;
      item.found=$(this).find('.item_posotita_found').attr('data-val');
      item.theori=$(this).find('.item_posotita_theori').attr('data-val');
      item.sxolio=$(this).find('.item_sxolio').val();
      if (item.asset_id>0) {
        assets_array.push(item);
      }
    });
    
    //console.log(assets_array);
    
    
    assets_str = encodeURIComponent($.base64.encode(JSON.stringify(assets_array)));
    datasend+='&assets_str=' + assets_str;
    
    //console.log(datasend);
    

    

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-whi-mov-item-exec.php?id=' + from_php_id,
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


  var last_aa=from_php_aa;
  function item_add_run(after_aa) {
  
    last_aa++;

    row_html='<tr class="item_tr" data-aa="' + last_aa + '" data-rec="0">' + 
    '<td class="mytdcm item_aa" nowrap>' + last_aa + '</td>' +
    '<td class="mytdcml" nowrap>' +
      '<input data-id="0" value="" class="item_asset form-control form-control-sm myneedsave" data-aa="' + last_aa + '" type="text">' +
    '</td>' +
          
    '<td class="mytdcm" nowrap>' +
      '<img src="img/1bg.png" border="0" width="32" data-aa="' + last_aa + '" class="item_posotita_theori" data-val="">' +
    '</td>' +
    '<td class="mytdcm" nowrap>' +
      '<img src="img/1bg.png" border="0" width="32" data-aa="' + last_aa + '" class="item_posotita_found" data-val="">' + 
    '</td>' +
    '<td class="mytdcml" nowrap>' +
      '<input value="" class="item_sxolio form-control form-control-sm myneedsave" data-aa="' + last_aa + '" type="text">' +
    '</td>' +
    '<td class="mytdcm" nowrap>' +
      '<i class="fas fa-trash-alt   item_remove" data-aa="' + last_aa + '"></i> ' +
      '<i class="fas fa-plus-circle item_add"    data-aa="' + last_aa + '"></i>' +
    '</td>' +
    '</tr>';


    if ($('.item_tr').length==0) {
      $('#mythistable tbody').append(row_html);
    } else {
      
      $('.item_tr[data-aa=' + after_aa + ']').after(row_html);
    }
    
    $('.item_asset[data-aa=' + last_aa + ']').autocomplete(item_asset_autocomplete);
    $('.item_posotita_found[data-aa=' + last_aa + ']').click(item_posotita_found_click);
    $('.item_add[data-aa=' + last_aa + ']').click(item_add_click);
    $('.item_remove[data-aa=' + last_aa + ']').click(item_remove_click);

    if (gks_page_loading==false) {
      $('.item_asset[data-aa=' + last_aa + ']').focus().select();
    }
    
    set_aa();
  }

  function set_aa() {
    $('.item_aa').each(function(index, elem) {
      $(this).html((index + 1));  
    });  
  }

  function item_add_click() {
    if (from_php_mystate!='00draft') return;
    need_save=true;
    after_aa=$(this).attr('data-aa');
    item_add_run(after_aa);
  }
  
  $('.item_add').click(item_add_click);  
  

  function item_remove_click() {
    if (from_php_mystate!='00draft') return;
    need_save=true;
    aa=parseInt( $(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    $('.item_tr[data-aa=' + aa +']').remove(); 
    if ($('.item_tr').length ==0) {
      item_add_run(0);  
    }
    set_aa();
  }
  $('.item_remove').click(item_remove_click);  
    
  function get_asset_theori(aa) {

    asset_id=parseInt($('.item_asset[data-aa=' + aa + ']').attr('data-id'));
    if (isNaN(asset_id)) asset_id=0;
    if (asset_id<=0) {
      $('.item_posotita_theori[data-aa=' + this.this_gks_aa + ']').attr('src','1bg.png').attr('data-val','');
    } else {
      datasend='asset_id=' + asset_id;
      $.ajax({
  			url: '/my/admin-autocomplete-asset-get-data.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			this_gks_aa:aa,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
    					//console.log(data.data);
    					if (data.data.asset_last_ergastirio_id==$('#ergastirio_id').attr('data-id') && data.data.is_fotografou=='0') {
    					  $('.item_posotita_theori[data-aa=' + this.this_gks_aa + ']').attr('src','img/1.png').attr('data-val','1');
    					} else {
    					  $('.item_posotita_theori[data-aa=' + this.this_gks_aa + ']').attr('src','img/0.png').attr('data-val','0');
    					}
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  			
  		});     
    }
  }
  
  item_asset_autocomplete={

    source: function(request, response) {
      mydata={
        term: request.term,
        //av:1,
        //uav:1,
        //andservice:1
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
    select: function( event, ui ) {
      need_save=true; 
      $("#asset_id").val(ui.item.id);
      $(this).attr('data-id',ui.item.id);
      aa=$(this).attr('data-aa');
      get_asset_theori(aa);

      
    },
    change: function (event, ui) {
      if(!ui.item){
        need_save=true; 
        $(this).val('').attr('data-id','0');
        aa=$(this).attr('data-aa');
        $('.item_posotita_theori[data-aa=' + aa + ']').attr('src','img/1bg.png').attr('data-val','');
      }
    }
  };
  $('.item_asset').autocomplete(item_asset_autocomplete);
  

  function item_posotita_found_click() {
    if (from_php_mystate!='00draft') return;
    need_save=false;
    data_val=$(this).attr('data-val');
    if (data_val=='1') $(this).attr('src','img/0.png').attr('data-val','0');
    else if (data_val=='0') $(this).attr('src','img/1bg.png').attr('data-val','');
    else if (data_val=='') $(this).attr('src','img/1.png').attr('data-val','1');      
  }
  $('.item_posotita_found').click(item_posotita_found_click);


  if (last_aa==0 && from_php_mystate=='00draft') {
    item_add_run(0);
  }






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


  