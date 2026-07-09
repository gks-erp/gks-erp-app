/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

jQuery(document).ready(function($) {

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


    datasend+='&product_brand_descr='  + encodeURI($("#mypostform #product_brand_descr").val().trim());
    datasend+='&product_brand_parent_id='  + encodeURI($("#mypostform #product_brand_parent_id").val().trim());
    datasend+='&brand_comments='  + encodeURIComponent($.base64.encode(tinyMCE.get('brand_comments').getContent()));
    datasend+='&brand_disable=' + (($('#mypostform #brand_disable').is(':checked')) ? '0':'1');
    datasend+='&brand_photo='  + encodeURI($("#form_product_brand_photo").val().trim());
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-product-brands-item-exec.php?id=' + from_php_id,
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
  

  $('#product').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        onlydescr:1,
        and_variable:0,
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
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
      //$("#product").val(ui.item.descr);
      $("#product_id").val(ui.item.id);
      //datasend='&product_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#product").val("");
          $("#product_id").val("");
        }
    },
    create: function () {
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
          .appendTo(ul);
      };
    },
    open: function(event, ui) {
      var mymaxui_id=0;
      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
        temp=$(this).outerWidth();
        if (temp>mymaxui_id) mymaxui_id=temp;
      });
      var mymaxui_text=0;
      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text').each(function() {
        temp=$(this).outerWidth();
        if (temp>mymaxui_text) mymaxui_text=temp;
      });
      mymaxui_id+=4;
      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
        $(this).css({'min-width':mymaxui_id + 'px','display' : 'inline-block'});
      }); 
      mymaxui_text+=mymaxui_id + 4;
      $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
    },    
  });
  
  
  $('#add_product').click(function(event) {  
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την μάρκα')); return;}	
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&from=cat&product_id='  + encodeURI($("#product_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-product-brands-item-product-add.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#product_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#product_table tbody').html(row_html);
            }
            
            $('.product_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.product_tr_new').each(function() {
              $(this).removeClass('product_tr_new').addClass('product_tr_exist');
            });
            var product_aa=0;
            $('#product_table .product_aa').each(function () {
              product_aa++;
              $(this).html(product_aa);  
            });
            
  
            $("body").removeClass("myloading");  
            
            $('#product').val('');
            $('#product_id').val('');
          

					} else {
					  myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });   
  


  
  
  var file_cc=0;
    
  jqXHR = $('#myphoto_brand_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα την μάρκα'));
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
              myhtmlimg+='  <a class="lightgallery_item_brand" href="' + file.url + '" data-download-url="' + file.url + '">';
              myhtmlimg+='    <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="' + file.url_thumb + '">';
              myhtmlimg+='  </a>';
              myhtmlimg+='  <br>';
              myhtmlimg+='  <div style="padding-top:4px">';
              myhtmlimg+='      <a href="" class="set_brand_photo"   data-url="' + file.url_thumb + '" title="' + gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία') + '"><img src="/my/img/icons/photo.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_brand_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="' + gks_lang('Διαγραφή') + '"><img src="/my/img/0.png" border="0" width="16"></a>';
              myhtmlimg+='  </div>';
              myhtmlimg+='</div>';


              $('#imagelist_photo').append(myhtmlimg);
              $('#item_upload_photo_' + file.insert_id + ' .delete_brand_upload_photo').click(delete_brand_upload_photo_click);
              $('#item_upload_photo_' + file.insert_id + ' .set_brand_photo').click(set_brand_photo_click);
              
             
            
              $("#lightgallery_brand").data('lightGallery').destroy(true);
              $("#lightgallery_brand").lightGallery({
              	selector: '.lightgallery_item_brand',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              if ($('#form_product_brand_photo').val() == '') {
                $('#form_product_brand_photo').val(file.url_thumb);
                $('#form_product_brand_photo_img').attr("src",file.url_thumb);  
                $('#reset_brand_photo').show(); 
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
      
	delete_brand_upload_photo_click = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-product-brands-item-photo-delete.php?id=' + uid,
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
  					//$('#myfileid_photo_' + this.myuid).remove();
  					
  					if (this.mydata_url == $('#form_product_brand_photo').val()) {
    					need_save=true;
    					if ($(".set_brand_photo").length == 0) {
    					  
                $('#form_product_brand_photo').val('');
                $('#form_product_brand_photo_img').attr("src",'/my/img/product.png');
                $('#reset_brand_photo').hide();
              } else {
                
                $(".set_brand_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_product_brand_photo').val(data_url);
                  $('#form_product_brand_photo_img').attr("src",data_url);
                  $('#reset_brand_photo').show();
                  return;
                });  					
      				}
            }
            
            $("#lightgallery_brand").data('lightGallery').destroy(true);
            $("#lightgallery_brand").lightGallery({
            	selector: '.lightgallery_item_brand',
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

  $('.delete_brand_upload_photo').click(delete_brand_upload_photo_click);

	set_brand_photo_click = function(event){	
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την μάρκα')); return;}	  
    need_save=true;      
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_product_brand_photo').val(data_url);
    $('#form_product_brand_photo_img').attr("src",data_url);
    $('#reset_brand_photo').show();
    return false;
  }

  $('.set_brand_photo').click(set_brand_photo_click);

  $('#reset_brand_photo').click(function() {
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την μάρκα')); return;}	  
    need_save=true;
    $('#form_product_brand_photo').val('');
    $('#form_product_brand_photo_img').attr("src",'/my/img/product.png');   
    $('#reset_brand_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_brand").lightGallery({
  	selector: '.lightgallery_item_brand',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  


  

  window.gks_fnc_product_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.product_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var product_aa=0;
      $('#product_table .product_aa').each(function () {
        product_aa++;
        $(this).html(product_aa);  
      });    
    });
  }
  
  
  function eshop_sync_click() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την μάρκα'));
      return;
    }
    if ($(this).hasClass('fa-sync-alt')==false) return;
    
    eshop_id=parseInt($(this).attr('data-eshop_id'));
    if (isNaN(eshop_id)) eshop_id=0;
    if (eshop_id<=0) return;
    
    $(this).html('<img src="img/wait.gif" style="height: 22px;">').removeClass('fa-sync-alt');
    //console.log(eshop_id);
    
    datasend='product_brand_id=' + from_php_id + '&eshop_id=' + eshop_id;
    $.ajax({
			url: '/my/admin-product-brands-item-eshop-sync.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_eshop_id:eshop_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				$('.eshop_sync[data-eshop_id=' + this.gks_eshop_id +']').html('').addClass('fa-sync-alt');
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.eshop_sync[data-eshop_id=' + this.gks_eshop_id +']').html('').addClass('fa-sync-alt');
				} else {
				  
					if (data.success == true) {
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='ok') {
  					    myalert('ok:' + $.base64.decode(data.save_but_message), '',true);
  					  } else {
  					    myalert('error:' + $.base64.decode(data.save_but_message), '',true);
  					  }
  					} else {
  					  $('body').addClass("myloading");
    					window.location.reload();
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.eshop_sync[data-eshop_id=' + this.gks_eshop_id +']').html('').addClass('fa-sync-alt');
					}
				}
			}
			
		});    
  }
  $('.eshop_sync').click(eshop_sync_click);
    
  var dialog_eshoplink;
  dialog_eshoplink = $('#dialog_eshoplink').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_eshoplink_link",
        html: '<i class="fa-solid fa-link"></i> '+ gks_lang('Σύνδεση'), 
        click: function() {
          eshop_id=parseInt($('#dialog_eshoplink_eshop').val());
          if (isNaN(eshop_id)) eshop_id=0;
          if (eshop_id<=0) {
            myalert('error:'+gks_lang('Επιλέξτε ένα eshop'));
            return;
          }
          remote_brand_id=parseInt($('#dialog_eshoplink_list').val());
          if (isNaN(remote_brand_id)) remote_brand_id=0;
          if (remote_brand_id<=0) {
            myalert('error:'+gks_lang('Επιλέξτε μια μάρκα'));
            return;
          }
          datasend='product_brand_id=' + from_php_id + '&eshop_id=' + eshop_id + '&remote_brand_id=' + remote_brand_id + '&pluginname=' + encodeURIComponent($.base64.encode($('#dialog_eshoplink_eshop_pluginname').val()));
          //console.log(datasend);
          
          $('body').addClass("myloading");
          $.ajax({
      			url: '/my/admin-product-brands-item-eshop-link-remote-brand.php',
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			error : function(jqXHR ,textStatus,  errorThrown) {
      				$('body').removeClass("myloading");
      				myalert('error:' + jqXHR.responseText);
      			},				
      			success: function(data) {
      			  $('body').removeClass("myloading");
      				if (!data) {
      					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
      					if (data.success == true) {

                  
                  row_html=$.base64.decode(data.row_html);
                  //console.log(row_html);
                  
                  tr_first=$('#eshoplink_table tbody tr:first');
                  if (tr_first.length>=1) {
                    tr_first.before(row_html);
                  } else {
                    $('#eshoplink_table tbody').html(row_html);
                  }
                  
                  $('.eshoplink_tr_new .deleterow').click(deleterow_click); 
                  $('.eshoplink_tr_new .eshop_sync').click(eshop_sync_click); 
                  $('.eshoplink_tr_new .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});

                  $('.eshoplink_tr_new').each(function() {
                    $(this).removeClass('eshoplink_tr_new').addClass('eshoplink_tr_exist');
                  });
                  var eshoplink_aa=0;
                  $('#eshoplink_table .eshoplink_aa').each(function () {
                    eshoplink_aa++;
                    $(this).html(eshoplink_aa);  
                  });
                  
                  dialog_eshoplink.dialog( "close" );
                  $("body").removeClass("myloading");                  
                  
                  			  
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		});            
          
        }
      },
      {
        id: "dialog_eshoplink_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#eshoplink_add').click(function(event)   {
    event.stopPropagation();
    $('#dialog_eshoplink_eshop').val('0');
    $('#dialog_eshoplink_search').val('');
    $('#dialog_eshoplink_list').val('0');
    
    
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 580) dheight=580;
	  dialog_eshoplink.dialog('option', 'width', dwidth);
	  dialog_eshoplink.dialog('option', 'height', dheight);
	  $('#dialog_eshoplink').parent().css({position:'fixed'});     
    dialog_eshoplink.dialog('open');
    
  });

  $('#dialog_eshoplink_eshop').change(function() {
    eshop_id=parseInt($(this).val());
    if (isNaN(eshop_id)) eshop_id=0;

    $('#dialog_eshoplink_list option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (eshop_id<=0) return;
    
    $('body').addClass("myloading");
    datasend='eshop_id=' + eshop_id + '&pluginname=' + encodeURIComponent($.base64.encode($('#dialog_eshoplink_eshop_pluginname').val()));
    $.ajax({
			url: '/my/admin-product-brands-item-eshop-get-brands.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_eshop_id:eshop_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('body').removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            //console.log(data);
            elem=$('#dialog_eshoplink_list');
				    for (i = 0; i < data.plist.length; i++) {
				      elem.append('<option value="' + data.plist[i].i + '">' + data.plist[i].d + '</option>');
				    }	
				    $('#dialog_eshoplink_search').val('');
				    $('#dialog_eshoplink_search').focus();				  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
    
  });
  
  $('#dialog_eshoplink_eshop_pluginname').change(function() {
    $('#dialog_eshoplink_eshop').change();
  });

  $('#dialog_eshoplink_search').on(mychange, function() {
    sval=$(this).val().trim();
    //console.log(sval);
    
    $('#dialog_eshoplink_list option').each(function() { 
      if (sval=='' || $(this).text().search(sval)>=0) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });    
  });

  window.gks_fnc_eshoplink_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.eshoplink_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var eshoplink_aa=0;
      $('#eshoplink_table .eshoplink_aa').each(function () {
        eshoplink_aa++;
        $(this).html(eshoplink_aa);  
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

});

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
  readonly : (from_php_perm_ret_edit ? 0 : 1), 
});
