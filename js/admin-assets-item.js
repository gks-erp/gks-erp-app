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


  $('#asset_date_activate').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#asset_date_aposirsi').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#oxima_next_kteo').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#oxima_liji_asfaleia').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));




  function asset_sxolio_change() {gks_resize_textarea($(this));}
  $('#asset_sxolio').on(mychange, asset_sxolio_change);
  gks_resize_textarea($('#asset_sxolio'));  



  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });


  function asset_type_change() {
    tval=parseInt($('#asset_type').val());
    if (tval==24 || tval==25) { //POS wireless, POS wired
      $('#tr_bank_id').show();
      $('#tr_xreosi_val').show();
      $('#tr_xreosi_type').show();
    } else {
      $('#tr_bank_id').hide();
      $('#tr_xreosi_val').hide();
      $('#tr_xreosi_type').hide();
    }
    asset_type_mixani0=from_php_asset_type_mixani0;
    if (tval==1 || tval==6 || tval==7 || tval==27 || tval==24 || tval==25) { //mixani, fakos, flas, mobile, POS wireless, POS wired
      if (asset_type_mixani0) {
        $('#asset_type_mixani0').show();  
        $('#asset_type_mixani0_reverse').hide();
      } else {
        $('#asset_type_mixani0').hide();
        $('#asset_type_mixani0_reverse').show();
      }
    } else {
      $('#asset_type_mixani0').show();
      $('#asset_type_mixani0_reverse').hide();
    }
    
    if (tval==1) { //mixani
      $('#tr_mixani_esn').show();
      $('#gks_assets_mixani_esn').show();
    } else {
      $('#tr_mixani_esn').hide();
      $('#gks_assets_mixani_esn').hide();
    }
    
    if (tval== 26) { //oximata
      $('#tr_oxima_elastika').show();
      $('#tr_oxima_km').show();
      $('#tr_oxima_next_service_km').show();
      $('#tr_oxima_next_kteo').show();
      $('#tr_oxima_liji_asfaleia').show();
      $('#gks_assets_oximata_km').show();
      $('#label_serialnumber').html(gks_lang('Αριθμός Πλαισίου')+':');
    } else {
      $('#tr_oxima_elastika').hide();
      $('#tr_oxima_km').hide();
      $('#tr_oxima_next_service_km').hide();
      $('#tr_oxima_next_kteo').hide();
      $('#tr_oxima_liji_asfaleia').hide();
      $('#gks_assets_oximata_km').hide();
      $('#label_serialnumber').html(gks_lang('Serial Number')+':');
    }
    
    if (tval== 13) { //PCs
      $('#tr_asset_thesi').show();
    } else {
      $('#tr_asset_thesi').hide();
    }
    if (tval== 13 || tval== 14) { //PCs, Laptop
      $('#tr_mac_address').show();
    } else {
      $('#tr_mac_address').hide();
    }
    if (tval== 23 || tval==24 || tval==25 || tval==27) {//Tablets, POS wired, POS wireless, mobile
      $('#card_viva').show();
      $('#card_megeftpos').show();
      $('#card_mellon').show();
      $('#card_cardlink').show();
      $('#card_epay').show();
      $('#card_worldline').show();
      $('#card_nexi').show();
    } else {
      $('#card_viva').hide();
      $('#card_megeftpos').hide();
      $('#card_mellon').hide();
      $('#card_cardlink').hide();
      $('#card_epay').hide();
      $('#card_worldline').hide();
      $('#card_nexi').hide();
    }
    gks_myscroll();
  }
  $('#asset_type').change(asset_type_change);
  asset_type_change();


      
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
    
  function mysubmit() {
    
    datasend='';

    datasend+='&asset_code='  + encodeURIComponent($.base64.encode($("#mypostform #asset_code").val().trim()));
    datasend+='&asset_title='  + encodeURIComponent($.base64.encode($("#mypostform #asset_title").val().trim()));
    datasend+='&asset_serialnumber='  + encodeURIComponent($.base64.encode($("#mypostform #asset_serialnumber").val().trim()));
    datasend+='&asset_type='  + encodeURIComponent($("#mypostform #asset_type").val().trim());
    datasend+='&asset_date_activate='  + encodeURIComponent($.base64.encode($("#mypostform #asset_date_activate").val().trim()));
    datasend+='&asset_date_aposirsi='  + encodeURIComponent($.base64.encode($("#mypostform #asset_date_aposirsi").val().trim()));
    datasend+='&asset_sxolio='  + encodeURIComponent($.base64.encode($("#mypostform #asset_sxolio").val().trim()));
    datasend+='&is_fotografou=' + (($('#is_fotografou').is(':checked')) ? '1':'0');
    datasend+='&asset_disable=' + (($('#asset_disable').is(':checked')) ? '0':'1');
    datasend+='&bank_id='  + encodeURIComponent($("#mypostform #bank_id").val().trim());
    datasend+='&xreosi_val='  + encodeURIComponent($("#mypostform #xreosi_val").val().trim());
    datasend+='&xreosi_type='  + encodeURIComponent($("#mypostform #xreosi_type").val().trim());
    datasend+='&oxima_elastika='  + encodeURIComponent($.base64.encode($("#mypostform #oxima_elastika").val().trim()));
    datasend+='&oxima_km='  + encodeURIComponent($("#mypostform #oxima_km").val().trim());
    datasend+='&oxima_next_service_km='  + encodeURIComponent($("#mypostform #oxima_next_service_km").val().trim());
    datasend+='&oxima_next_kteo='  + encodeURIComponent($.base64.encode($("#mypostform #oxima_next_kteo").val().trim()));
    datasend+='&oxima_liji_asfaleia='  + encodeURIComponent($.base64.encode($("#mypostform #oxima_liji_asfaleia").val().trim()));

    datasend+='&asset_thesi='  + encodeURIComponent($.base64.encode($("#mypostform #asset_thesi").val().trim()));
    datasend+='&mac_address='  + encodeURIComponent($.base64.encode($("#mypostform #mac_address").val().trim()));
    //datasend+='&mixani_esn='  + encodeURIComponent($("#mypostform #mixani_esn").val().trim());
    
    datasend+='&viva_company_id='  + encodeURIComponent($("#mypostform #viva_company_id").val().trim());
    datasend+='&viva_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #viva_terminal_id").val().trim()));
    datasend+='&viva_terminal_code='  + encodeURIComponent($.base64.encode($("#mypostform #viva_terminal_code").val().trim()));
    datasend+='&viva_action_after='  + encodeURIComponent($("#mypostform #viva_action_after").val().trim());
    datasend+='&viva_def_ref_pliromis='  + encodeURIComponent($.base64.encode($("#mypostform #viva_def_ref_pliromis").val().trim()));

    datasend+='&megeftpos_company_id='  + encodeURIComponent($("#mypostform #megeftpos_company_id").val().trim());
    datasend+='&megeftpos_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #megeftpos_terminal_id").val().trim()));
    datasend+='&megeftpos_static_ip='  + encodeURIComponent($.base64.encode($("#mypostform #megeftpos_static_ip").val().trim()));
    datasend+='&megeftpos_port='  + encodeURIComponent($("#mypostform #megeftpos_port").val().trim());
    datasend+='&megeftpos_protocol='  + encodeURIComponent($("#mypostform #megeftpos_protocol").val().trim());
    datasend+='&megeftpos_erp_app_id='  + encodeURIComponent($("#mypostform #megeftpos_erp_app_id").val().trim());
    datasend+='&megeftpos_api_key='  + encodeURIComponent($.base64.encode($("#mypostform #megeftpos_api_key").val().trim()));
    
    datasend+='&mellon_company_id='  + encodeURIComponent($("#mypostform #mellon_company_id").val().trim());
    datasend+='&mellon_id='  + encodeURIComponent($.base64.encode($("#mypostform #mellon_id").val().trim()));
    datasend+='&mellon_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #mellon_terminal_id").val().trim()));

    datasend+='&cardlink_company_id='  + encodeURIComponent($("#mypostform #cardlink_company_id").val().trim());
    datasend+='&cardlink_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #cardlink_terminal_id").val().trim()));
    datasend+='&cardlink_static_ip='  + encodeURIComponent($.base64.encode($("#mypostform #cardlink_static_ip").val().trim()));
    datasend+='&cardlink_port='  + encodeURIComponent($("#mypostform #cardlink_port").val().trim());
    datasend+='&cardlink_ecr2eftweb_erp_app_id='  + encodeURIComponent($("#mypostform #cardlink_ecr2eftweb_erp_app_id").val().trim());
    datasend+='&cardlink_ecr2eftweb_service_url='  + encodeURIComponent($.base64.encode($("#mypostform #cardlink_ecr2eftweb_service_url").val().trim()));
    
    datasend+='&epay_company_id='  + encodeURIComponent($("#mypostform #epay_company_id").val().trim());
    datasend+='&epay_id='  + encodeURIComponent($.base64.encode($("#mypostform #epay_id").val().trim()));
    datasend+='&epay_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #epay_terminal_id").val().trim()));

    datasend+='&worldline_company_id='  + encodeURIComponent($("#mypostform #worldline_company_id").val().trim());
    datasend+='&worldline_id='  + encodeURIComponent($.base64.encode($("#mypostform #worldline_id").val().trim()));
    datasend+='&worldline_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #worldline_terminal_id").val().trim()));

    datasend+='&nexi_company_id='  + encodeURIComponent($("#mypostform #nexi_company_id").val().trim());
    datasend+='&nexi_id='  + encodeURIComponent($.base64.encode($("#mypostform #nexi_id").val().trim()));
    datasend+='&nexi_terminal_id='  + encodeURIComponent($.base64.encode($("#mypostform #nexi_terminal_id").val().trim()));
    
   
    
    datasend+='&form_asset_photo='  + encodeURI($("#form_asset_photo").val().trim());
    
    
    
    //console.log(datasend);
    
        
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    //console.log(datasend);
    
    

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-item-exec.php?id=' + from_php_id,
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
    
  var file_cc=0;
    
  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα το πάγιο'));
          }
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("<br>"));
          } else {
        
            file_cc++;
            data.mycc=file_cc;

            data.submit();
            $('#progress-bar_photo').show();
            $('#progress-extended_photo').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              
              
              myhtmlimg='';
              myhtmlimg+='<div id="item_upload_photo_' + file.insert_id + '" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">';
              myhtmlimg+='  <a class="lightgalleryitem_user" href="' + file.url + '" data-download-url="' + file.url + '">';
              myhtmlimg+='    <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="' + file.url_thumb + '">';
              myhtmlimg+='  </a>';
              myhtmlimg+='  <br>';
              myhtmlimg+='  <div style="padding-top:4px">';
              myhtmlimg+='      <a href="" class="set_profile_photo"   data-url="' + file.url_thumb + '" title="' + gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία') + '"><img src="/my/img/icons/photo.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="' + gks_lang('Διαγραφή') + '"><img src="/my/img/0.png" border="0" width="16"></a>';
              myhtmlimg+='  </div>';
              myhtmlimg+='</div>';


              $('#imagelist_photo').append(myhtmlimg);
              $('#item_upload_photo_' + file.insert_id + ' .delete_upload_photo').click(delete_upload_click_photo);
              $('#item_upload_photo_' + file.insert_id + ' .set_profile_photo').click(set_profile_photo);
              
             
            
              $("#lightgallery_user").data('lightGallery').destroy(true);
              $("#lightgallery_user").lightGallery({
              	selector: '.lightgalleryitem_user',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              if ($('#form_asset_photo').val() == '') {
                $('#form_asset_photo').val(file.url_thumb);
                $('#form_asset_photo_img').attr("src",file.url_thumb);  
                $('#reset_profile_photo').show(); 
                need_save=true;         
              }
            }
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-bar_photo .bar_photo').css(
            'width',
            progress + '%'
        );
        $('#progress-extended_photo').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε')+'<br>' + data.jqXHR.responseText);
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progressfile_photo' + data.mycc + ' .bar_photo').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#progress-bar_photo').hide();
        $('#progress-extended_photo').hide();
      },
      
  });
      
	delete_upload_click_photo = function(event) {	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-assets-item-photo-delete.php?id=' + uid,
			myuid: uid,
			type: 'POST',
			cache: false,
			dataType: 'json',
			mydata_url:data_url,
			data: '',
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
  					$('#item_upload_photo_' + this.myuid).remove();
  					$('#myfileid_photo_' + this.myuid).remove();
  					
  					if (this.mydata_url == $('#form_asset_photo').val()) {
    					need_save=true;
    					if ($(".set_profile_photo").length == 0) {
    					  
                $('#form_asset_photo').val('');
                $('#form_asset_photo_img').attr("src",'/my/img/product.png');
                $('#reset_profile_photo').hide();
              } else {
                
                $(".set_profile_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_asset_photo').val(data_url);
                  $('#form_asset_photo_img').attr("src",data_url);
                  $('#reset_profile_photo').show();
                  return;
                });  					
      				}
            }
            
            $("#lightgallery_user").data('lightGallery').destroy(true);
            $("#lightgallery_user").lightGallery({
            	selector: '.lightgalleryitem_user',
            	thumbnail:true,
            	hideBarsDelay:1000,
            }); 
					  
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_photo').click(delete_upload_click_photo);

	set_profile_photo = function(event){	
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	  
    need_save=true;      
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_asset_photo').val(data_url);
    $('#form_asset_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	  
    need_save=true;
    $('#form_asset_photo').val('');
    $('#form_asset_photo_img').attr("src",'/my/img/product.png');   
    $('#reset_profile_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });        
  

  $('#anath_warehouse_id').autocomplete({
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
      $('#anath_warehouse_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#anath_warehouse_id').val('').attr('data-id','0');
      }
    }
  });
   
  $('#add_warehouse').click(function(event) {
    if (from_php_id<=0 || need_save) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	   
    datasend='';
    datasend+='asset_id=' + from_php_id;    
    datasend+='&warehouse_id='  + encodeURI($("#anath_warehouse_id").attr('data-id').trim());    
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-assets-item-move-warehouse.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

  $('#return_to_warehouse').click(function(event) {  
    if (from_php_id<=0 || need_save) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	
    
    datasend='';
    datasend+='asset_id=' + from_php_id;    
    datasend+='&warehouse_id=' + from_php_row_asset_last_warehouse_id;    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-item-move-warehouse.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });  

  $('#anath_sinergati_id').autocomplete({
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
      $('#anath_sinergati_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#anath_sinergati_id').val('').attr('data-id','0');
      }
    }
  });

  $('#add_sinergati').click(function(event) {
    if (from_php_id<=0 || need_save) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	   
    datasend='';
    datasend+='asset_id=' + from_php_id;    
    datasend+='&user_id='  + encodeURI($("#anath_sinergati_id").attr('data-id').trim());    
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-assets-item-move-user.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

  $('#anath_company_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-company.php',
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
      $('#anath_company_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#anath_company_id').val('').attr('data-id','0');
      }
    }
  });

  $('#add_company').click(function(event) {
    if (from_php_id<=0 || need_save) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	

    datasend='';
    datasend+='asset_id=' + from_php_id;    
    datasend+='&company_id='  + encodeURI($("#anath_company_id").attr('data-id').trim());    
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-assets-item-move-company.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

// 
//  if (from_php_GKS_TRANSFER) {
//    $('.transfer_oxima_type_id').autocomplete({
//      source: function(request, response) {
//        mydata={
//          term: request.term,
//        };
//        $.ajax({
//          url: 'admin-autocomplete-transfer-oxima-type.php',
//          dataType: "json",
//          cache: false,
//          data: mydata,
//          error : function(jqXHR ,textStatus,  errorThrown) {
//    				myalert('error:' + jqXHR.responseText);
//    			},
//          success: function( data ) {
//            if (data.success == true) {
//              response( data.list);
//            } else {
//              myalert('error:' + $.base64.decode(data.message));
//            }
//          }
//        });
//      },
//      minLength: 3,
//      delay: 300, //default
//      autoFocus: true,
//      select: function( event, ui ) {
//        $('.transfer_oxima_type_id').attr('data-id',ui.item.id);
//      },
//      change: function (event, ui) {
//        if(!ui.item){
//          $('.transfer_oxima_type_id').val('').attr('data-id','0');
//        }
//      }
//    });
//  }

  $('#megeftpos_protocol').change(function() {
    val_protocol=parseInt($(this).val()); if (isNaN(val_protocol)) val_protocol=0;
    $('.megeftpos_protocol').hide();
    $('.megeftpos_protocol' + val_protocol).show();
  
  });
  
  $('.megeftpos_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&id=' + $('#megeftpos_erp_app_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('megeftpos_run_command')); 
    datasend+='&group_cmd=megeftpos'; 
    
    $.ajax({
			url: '/my/admin-erp-app-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });
  
  $('.mellon_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&company_id=' + $('#mellon_company_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('mellon_run_command')); 
    datasend+='&group_cmd=mellon'; 
    
    $.ajax({
			url: '/my/admin-assets-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });
  
  $('.cardlink_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&id=' + $('#cardlink_ecr2eftweb_erp_app_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('cardlink_run_command')); 
    datasend+='&group_cmd=cardlink'; 
    
    $.ajax({
			url: '/my/admin-erp-app-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });

  $('.epay_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&company_id=' + $('#epay_company_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('epay_run_command')); 
    datasend+='&group_cmd=epay'; 
    
    $.ajax({
			url: '/my/admin-assets-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });
  

  $('.worldline_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&company_id=' + $('#worldline_company_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('worldline_run_command')); 
    datasend+='&group_cmd=worldline'; 
    
    $.ajax({
			url: '/my/admin-assets-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });

  $('.nexi_run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    var api_call=$(this).attr('data-api_call'); 
    var item_id=$(this).attr('id');  
    
    //console.log(api_call, item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='';
    datasend+='&company_id=' + $('#nexi_company_id').val();
    datasend+='&asset_id=' + from_php_id;
    datasend+='&api_call=' + encodeURIComponent($.base64.encode(api_call));
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('nexi_run_command')); 
    datasend+='&group_cmd=nexi'; 
    
    $.ajax({
			url: '/my/admin-assets-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					gks_myscroll();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
  });

  $('#oxima2type2transfer_transfer_title').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-transfer.php',
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
      $('#oxima2type2transfer_transfer_title').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#oxima2type2transfer_transfer_title').val('').attr('data-id','0');
        }
    }
  });

  $('#oxima2type2transfer_oxima_type').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-transfer-oxima-type.php',
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
      $('#oxima2type2transfer_oxima_type').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#oxima2type2transfer_oxima_type').val('').attr('data-id','0');
        }
    }
  });


  $('#add_oxima2type2transfer').click(function(event) {  
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το πάγιο')); return;}	  

    var datasend='';
    datasend+='&from=asset';
    datasend+='&asset_id=' + from_php_id;    
    datasend+='&transfer_id='  + encodeURI($('#oxima2type2transfer_transfer_title').attr('data-id').trim());    
    datasend+='&transfer_oxima_type_id='  + encodeURI($('#oxima2type2transfer_oxima_type').attr('data-id').trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-item-transfer-oxima_type.php',
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
  					//myalert('ok:' + 'OK');
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#oxima2type2transfer_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#oxima2type2transfer_table tbody').html(row_html);
            }
            
            $('.oxima2type2transfer_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.oxima2type2transfer_tr_new').each(function() {
              $(this).removeClass('oxima2type2transfer_tr_new').addClass('oxima2type2transfer_tr_exist');
            });
            var oxima2type2transfer_aa=0;
            $('#oxima2type2transfer_table .oxima2type2transfer_aa').each(function () {
              oxima2type2transfer_aa++;
              $(this).html(oxima2type2transfer_aa);  
            });
            $("body").removeClass("myloading");  
            $('#oxima2type2transfer_transfer_title').val('').attr('data-id','0');
            $('#oxima2type2transfer_oxima_type').val('').attr('data-id','0');
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }); 


  window.gks_fnc_oxima2type2transfer_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.oxima2type2transfer_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var oxima2type2transfer_aa=0;
      $('#oxima2type2transfer_table .oxima2type2transfer_aa').each(function () {
        oxima2type2transfer_aa++;
        $(this).html(oxima2type2transfer_aa);  
      });    
    });
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



