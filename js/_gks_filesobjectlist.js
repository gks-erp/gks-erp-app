/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var gks_filesexplore_div_start_run=false;
var gks_filesexplore_div_footer_show=false;

jQuery(document).ready(function($) {
  
  
  var file_filesobjectlist_cc=0;

  

  filesobjectlist_jqXHR = $('#filesobjectlist_form').fileupload({
      dropZone:$('#filesobjectlist_f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
          if (from_php_id<=0) {
            myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
            return;
          }
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("\n"));
          } else {
        
            file_filesobjectlist_cc++;
            data.mycc=file_filesobjectlist_cc;

            data.submit();
            $('#filesobjectlist_progress_bar_photo').show();
            $('#filesobjectlist_progress_extended_photo').show();
          }
      },
      done: function (e, data) {
          $('#filesobjectlist_table_imagelist_photo').show();
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              
              
              myhtmlimg=file.html_insert;
              
               

              if ($('#filesobjectlist_table_imagelist_photo tbody tr').length==0) {
                $('#filesobjectlist_table_imagelist_photo tbody').append(myhtmlimg);
              } else {
                $('#filesobjectlist_table_imagelist_photo tbody tr:last').after(myhtmlimg);
              }
              for(i=0;i<file.data_path.length;i++) {
                $('.filesobjectlist_delete_upload_photo[data-path="' + file.data_path[i] + '"]').click(filesobjectlist_delete_upload_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + file.data_path[i] + '"] td.tdimg_descr').click(filesobjectlist_edit_descr_click);
                $('.filesobjectlist_set_print_photo[data-path="' + file.data_path[i] + '"]').click(filesobjectlist_set_print_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + file.data_path[i] + '"] .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
                
              }

              $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
              $("#filesobjectlist_table_imagelist_photo").lightGallery({
              	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              
            }
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#filesobjectlist_progress_bar_photo .filesobjectlist_bar_photo').css(
            'width',
            progress + '%'
        );
        $('#filesobjectlist_progress_extended_photo').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:' + gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε'));
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#filesobjectlist_progresssinglefile_photo' + data.mycc + ' .filesobjectlist_bar_photo').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#filesobjectlist_progress_bar_photo').hide();
        $('#filesobjectlist_progress_extended_photo').hide();
      },
      
  });
      
	window.filesobjectlist_delete_upload_photo_click = function(event){	
    data_path=$(this).attr('data-path');

    //console.log(data_path);
    datasend='';
    datasend+='&data_path=' + encodeURIComponent($.base64.encode(data_path));
    datasend+='&object_name=' + encodeURIComponent($.base64.encode(from_php_filesobjectlist_object_name));
    
    $.ajax({
			url: '/my/admin-filesobjectlist-photo-delete.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(res_jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + res_jqXHR.responseText);
			},				
			success: function(data) {
				$('body').removeClass('myloading');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					
  					$('.filesobjectlist_delete_upload_photo[data-path="' + data.data_path + '"]').parent().parent().remove();  
            
            if ($('#filesobjectlist_table_imagelist_photo tr').length<=1) {
              $('#filesobjectlist_table_imagelist_photo').hide();
            }
            
            $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
            $("#filesobjectlist_table_imagelist_photo").lightGallery({
            	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
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

  $('.filesobjectlist_delete_upload_photo').click(filesobjectlist_delete_upload_photo_click);

	window.filesobjectlist_set_print_photo_click = function(event){	
    data_path=$(this).attr('data-path');
    data_value=$(this).attr('data-value');
    
    //console.log(data_path);

    datasend='&data_path=' + encodeURIComponent($.base64.encode(data_path));
    datasend+='&object_name=' + encodeURIComponent($.base64.encode(from_php_filesobjectlist_object_name));
    datasend+='&data_value=' + data_value;

    
    $.ajax({
			url: '/my/admin-filesobjectlist-photo-set-print.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(res_jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + res_jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  if (data.data_value==1) {
					    $('.filesobjectlist_set_print_photo[data-path="' + data.data_path + '"]').attr('src', 'img/1.png').attr('data-value','1');  
					  } else {
					    $('.filesobjectlist_set_print_photo[data-path="' + data.data_path + '"]').attr('src', 'img/0b.png').attr('data-value','0');  
					  }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }

  $('.filesobjectlist_set_print_photo').click(filesobjectlist_set_print_photo_click);



  window.filesobjectlist_edit_descr_click=function(event) {
    event.stopPropagation();
    data_path=$(this).parent().attr('data-path');
    if (data_path === undefined) return;
    mytext=$(this).html(); 
    if (mytext.startsWith('<input')) return;
    if (mytext === undefined) mytext='';
    mytext=mytext.trim();    
    $(this).attr('data-old-value',mytext);
    
    myid='';
    html='<input type="text" value="' + mytext + '" class="filesobjectlist_edit_descr_input form-control form-control-sm" >';
    html+='<i class="filesobjectlist_edit_descr_save fas fa-save"></i>';
    html+='<i class="filesobjectlist_edit_descr_cancel fas fa-window-close"></i>';
    
    $(this).html(html).addClass('filesobjectlist_edit_descr_edit');
    $(this).find('.filesobjectlist_edit_descr_save').click(filesobjectlist_edit_descr_save_click);
    $(this).find('.filesobjectlist_edit_descr_cancel').click(filesobjectlist_edit_descr_cancel_click);
    $(this).find('.filesobjectlist_edit_descr_input').focus().select();
    //console.log('filesobjectlist_edit_descr_click');
  }
  $('#filesobjectlist_table_imagelist_photo td.tdimg_descr').click(filesobjectlist_edit_descr_click);
  
  
  window.filesobjectlist_edit_descr_save_click=function(event) {
    event.stopPropagation();
    data_path=$(this).parent().parent().attr('data-path');
    if (data_path === undefined) return;
    data_value=$(this).parent().find('input').val();

    datasend='&data_path=' + encodeURIComponent($.base64.encode(data_path));
    datasend+='&object_name=' + encodeURIComponent($.base64.encode(from_php_filesobjectlist_object_name));
    datasend+='&data_value=' + encodeURIComponent($.base64.encode(data_value));;
    datasend+='&field=descr';

    
    $.ajax({
			url: '/my/admin-filesobjectlist-photo-set-print.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(res_jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + res_jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.data_path + '"] .tdimg_descr').html(data.data_value).removeClass('filesobjectlist_edit_descr_edit');
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		}); 
  }
  
  window.filesobjectlist_edit_descr_cancel_click=function(event) {
    event.stopPropagation();
    mytext=$(this).parent().attr('data-old-value');
    if (mytext === undefined) return;
    data_path=$(this).parent().parent().attr('data-path');
    if (data_path === undefined) return;
    $(this).parent().html(mytext).removeClass('filesobjectlist_edit_descr_edit');
  }
  
  var dialog_filesobjectlist_set_public_file_enable_click_return=false;
  $('#dialog_filesobjectlist_set_public_file_enable').on('change', function() {
    if (dialog_filesobjectlist_set_public_file_enable_click_return) return;
    if ($('#dialog_filesobjectlist_set_public_file_enable').is(':checked')) {
      $('#dialog_filesobjectlist_set_public_file_date').prop('disabled',false);
      //$('#dialog_filesobjectlist_set_public_file_date_div').show();
    } else {
      $('#dialog_filesobjectlist_set_public_file_date').prop('disabled',true);
      //$('#dialog_filesobjectlist_set_public_file_date_div').hide();
    }
  });
     
  window.filesobjectlist_set_public_file_click=function(event) {
    event.stopPropagation();
    //console.log('filesobjectlist_set_public_file_click');

    data_path=$(this).attr('data-path');
    if (data_path === undefined) data_path='';
    if (data_path=='') return;
    
    data_expire_date=$(this).attr('data-expire_date');
    if (data_expire_date === undefined) data_expire_date='';
    
    dialog_filesobjectlist_set_public_file_enable_click_return=true;
    if (data_expire_date=='') {
      if ($('#dialog_filesobjectlist_set_public_file_enable').is(':checked')) {
        $('#dialog_filesobjectlist_set_public_file_enable').click();
      }  
      $('#dialog_filesobjectlist_set_public_file_date').prop('disabled',true);
      $('#dialog_filesobjectlist_set_public_file_date').val('').datetimepicker('setOptions',{value: null});
    } else {
      if ($('#dialog_filesobjectlist_set_public_file_enable').is(':checked')==false) {
        $('#dialog_filesobjectlist_set_public_file_enable').click();
      }  
      $('#dialog_filesobjectlist_set_public_file_date').prop('disabled',false);
      $('#dialog_filesobjectlist_set_public_file_date').val(data_expire_date).datetimepicker('setOptions',{value: data_expire_date});
    }
    dialog_filesobjectlist_set_public_file_enable_click_return=false;




    
    public_file_url=$(this).attr('data-shortcode_url');
    if (public_file_url === undefined) public_file_url='';
    if (public_file_url!='') {
      public_file_url=from_php_GKS_SITE_URL + 's/' + public_file_url;
      public_file_url='<a href="' + public_file_url + '" class="gks_link" target="_blank">' + public_file_url + '</a>' +
                     ' <a href="' + public_file_url + '?d" class="gks_link" target="_blank"><i class="fas fa-download" style="color1:blue;"></i></a>';
      //console.log(public_file_url);
    }
    $('#dialog_filesobjectlist_set_public_file_url').html(public_file_url);
    data_myopencount=$(this).attr('data-myopencount');
    if (data_myopencount === undefined) data_myopencount='';
    if (data_myopencount=='0') data_myopencount='';
    $('#dialog_filesobjectlist_set_public_file_myopencount').html(data_myopencount);
    
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 600) dwidth=600;
    if (dheight> 550) dheight=550;
    dialog_filesobjectlist_set_public_file.dialog('option', 'width', dwidth);
    dialog_filesobjectlist_set_public_file.dialog('option', 'height', dheight);
    $('#dialog_filesobjectlist_set_public_file').parent().css({position:'fixed'});
    dialog_filesobjectlist_set_public_file.gks_data_path=data_path;
    dialog_filesobjectlist_set_public_file.dialog('open');
    $('#dialog_filesobjectlist_set_public_file_cancel').focus();
  }
  $('#filesobjectlist_table_imagelist_photo .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
  
  
  

  var dialog_filesobjectlist_set_public_file;
  dialog_filesobjectlist_set_public_file = $( '#dialog_filesobjectlist_set_public_file' ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        tabIndex: 1,
        id: "dialog_filesobjectlist_set_public_file_ok",
        html: "<i class='fas fa-save'></i> " + gks_lang('OK'),
        //icon: "ui-icon-print",  
        click: function() { 
          
          if ($('#dialog_filesobjectlist_set_public_file_enable').is(':checked')) {
            data_value=$('#dialog_filesobjectlist_set_public_file_date').val();
            if (data_value=='__/__/____ __:__') data_value='';
            if (data_value=='') {
              myalert('error:' + gks_lang('Ορίστε μία Ημερομηνία λήξης'));
              return;
            }
          } else {
            data_value='';
          }
          
          datasend='&data_path=' + encodeURIComponent($.base64.encode(dialog_filesobjectlist_set_public_file.gks_data_path));
          datasend+='&object_name=' + encodeURIComponent($.base64.encode(from_php_filesobjectlist_object_name));
          datasend+='&data_value=' + encodeURIComponent(data_value);;
          datasend+='&field=expire_date';
      
          
          $.ajax({
      			url: '/my/admin-filesobjectlist-photo-set-print.php?id=' + from_php_id,
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			error : function(res_jqXHR ,textStatus,  errorThrown) {
      				myalert('error:' + res_jqXHR.responseText);
      			},				
      			success: function(data) {
      				if (!data) {
      					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
      					if (data.success == true) {
      					  //console.log(data);
      					  $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.data_path + '"] .fol_selpublic').html(data.data_value);
      					  if (data.data_value.includes('tooltipster')) {
      					    $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.data_path + '"] .fol_selpublic .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true,interactive:true});
      					  }
      					  $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.data_path + '"] .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
      					  dialog_filesobjectlist_set_public_file.dialog('close');
      					  if (data.action=='create') {
      					    myalert('ok:' + gks_lang('Δημιουργήθηκε το δημόσιο URL') + ': <br><a href="' + data.public_shortcode_full + '" class="gks_link" target="_blank">' + data.public_shortcode_full + '</a><br><a href="' + data.public_shortcode_full + '?d" class="gks_link" target="_blank"><i class="fas fa-download" style="font-size:200%;color:blue;"></i></a>');
      					  }
      					  
      					  
      					  if (data.data_value.includes('data-active="1"')) {
        					  $('.dialog_item_message_email_attachments_checkbox[data-path="' + data.data_path + '"]').
        					    parent().parent().
        					    find('td.gks_atta_shortcode_url_td').html(
        					    '<a href="'+data.public_shortcode_full+'" target="_blank" data-shortcode_url="">'+data.public_shortcode_ws+'</a>'
        					    );
        					}
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		}); 
      		          
          
        }
      },
      {
        tabIndex: 2,
        id: "dialog_filesobjectlist_set_public_file_cancel",
        html: "<i class='fa fa-window-close'></i> " + gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      }          
    
    ],
    open: function () {
      $('#dialog_filesobjectlist_set_public_file_cancel').blur();
    },
    
  });   

  var elem_file_enable = document.querySelector('#dialog_filesobjectlist_set_public_file_enable');
  if (elem_file_enable != null) {
    var dialog_filesobjectlist_set_public_file_enable = new Switchery(elem_file_enable);
  }
  $('#dialog_filesobjectlist_set_public_file_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,inline11111111:true,}));

  
  
  $("#filesobjectlist_table_imagelist_photo").lightGallery({
  	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });
  
  
  var gks_video_support=false;
  var gks_webcam_devices_found=[];
  var curr_devicelabel='';
  var gks_webcam_stream=null;
  var gks_webcam_video_elem=null;
  var gks_webcam_video_ww=0; gks_webcam_video_hh=0;
  var gks_webcam_data_picture='';
  $('#gks_webcam_start').click(function() {
    $('#gks_webcam_panel').show();
    $('body').addClass('gks_webcam_panel_body');  
    $('#gks_webcam_pixels').html('');
    $('#gks_webcam_permissions').hide();
    $('#gks_webcam_select').hide();
    $('#gks_webcam_img').hide();
    $('#gks_webcam_video').show();
    $('#gks_webcam_capture').hide();
    $('#gks_webcam_cancel_capture').hide();
    $('#gks_webcam_save').hide();
    $('#gks_webcam_message_ok').hide();
    
    if (!navigator.mediaDevices?.enumerateDevices) {
      myalert('error:' + gks_lang('Δεν υποστηρίζει ο φυλλομετρητής την λειτουργία enumerateDevices οπότε δεν μπορεί να γίνει η λήψη φωτογραφίας'));  
      //console.log("enumerateDevices() not supported.");
      return;
    } 
    gks_webcam_getdevices(true);
  });
  
  function gks_webcam_getdevices(startStream) {
    $('#gks_webcam_select option').remove();
    
    navigator.mediaDevices
    .enumerateDevices()
    .then((devices) => {
      gks_video_support=false;
      gks_webcam_devices_found=[];
      devices.forEach((device) => {
        if (device.kind=='videoinput') {
          gks_video_support=true;
          if (device.deviceId!='') {
            if (gks_webcam_devices_found.length==0) {
              $('#gks_webcam_select').append('<option value="">--</option>');
            }
            gks_webcam_devices_found.push(device);
            $('#gks_webcam_select').append('<option value="' + device.deviceId + '">' + device.label + '</option>');
          }
        }
        //console.log(device);
        //console.log('device', `${device.kind}: ${device.label} id = ${device.deviceId}`);
      });
      
      if (gks_video_support==false)  {
        myalert('error:' + gks_lang('Δεν υποστηρίζει ο φυλλομετρητής την λειτουργία video'));
        return;
      }
      
      $('#gks_webcam_permissions').show();

      if (gks_webcam_devices_found.length==0) {
        $('#gks_webcam_select').hide();
        $('#gks_webcam_permissions').show();
      } else {
        $('#gks_webcam_select').show();
        $('#gks_webcam_permissions').hide();
        
        if (startStream) gks_webcam_permissions_click();
        
        if (typeof curr_devicelabel != 'undefined') {
          $('#gks_webcam_select option').each(function() {
            if ($(this).text() == curr_devicelabel) {
              $('#gks_webcam_select').val($(this).attr('value'));
              return;
            } 
          });      
        }
          
      }
      gks_webcam_panel_buttons_resize();

    })  
    .catch((err) => {
      myalert('error:' + gks_lang('Σφάλμα') + ' ' + err.name + ': ' + err.message);
      console.error('mediaDevices', `${err.name}: ${err.message}`);
    });
      
  }

  var gks_webcam_constraints = {
    video: {
      width: { min: 100, ideal: 10000 },
      height: { min: 100, ideal: 10000 },
    },
  };
    
  function gks_webcam_permissions_click() {


    navigator.mediaDevices
      .getUserMedia(gks_webcam_constraints)
      .then((mystream) => {
        /* use the stream */
        gks_webcam_stream=mystream;

        
        mytracks=gks_webcam_stream.getVideoTracks();
        if (mytracks.length>0) {
          $('#gks_webcam_select').val('');
          curr_devicelabel=mytracks[0].label;
        }
        
        //console.log('the stream is ok');
        gks_webcam_video_elem = document.getElementById('gks_webcam_video');
        gks_webcam_video_elem.srcObject = gks_webcam_stream;
        gks_webcam_video_elem.onloadedmetadata = () => {
          gks_webcam_video_elem.play();
        };
        
        gks_webcam_video_elem.addEventListener("playing", gks_webcam_video_elem_playing);
              
        
        gks_webcam_getdevices(false);
        $('#gks_webcam_capture').show();
        gks_webcam_panel_buttons_resize();
      })
      .catch((err) => {
        myalert('error:' + gks_lang('Σφάλμα') + ' ' + err.name + ': ' + err.message);
        
        //console.log('error',err); /* handle the error */
    });    
  }
  
  $('#gks_webcam_permissions').click(gks_webcam_permissions_click);
  
  function gks_webcam_video_elem_playing() {
    gks_webcam_video_ww=gks_webcam_video_elem.videoWidth;
    gks_webcam_video_hh=gks_webcam_video_elem.videoHeight;
    
    $('#gks_webcam_video').css({width:gks_webcam_video_ww+'px',height:gks_webcam_video_hh+'px'});
    $('#gks_webcam_pixels').html(gks_webcam_video_ww+'x'+gks_webcam_video_hh);
    gks_webcam_panel_buttons_resize();
    $('#gks_webcam_canvas').css({width:gks_webcam_video_ww,height:gks_webcam_video_hh});
    
    //console.log(gks_webcam_video_elem.videoWidth);
    //console.log(gks_webcam_video_elem.videoHeight);    
  }
  
  function gks_webcam_stop_click() {
    $('#gks_webcam_panel').hide();
    $('body').removeClass('gks_webcam_panel_body');
    
    if (gks_webcam_stream!==null) {
      gks_webcam_stream.getTracks().forEach(function(track) {
        track.stop();
      });
      gks_webcam_video_elem.srcObject=null; 
      gks_webcam_video_elem.removeEventListener('playing',gks_webcam_video_elem_playing);
    }
  }
  $('#gks_webcam_stop').click(gks_webcam_stop_click);
  
  $('#gks_webcam_select').change(function() {
    if ($('#gks_webcam_cancel_capture').css('display')!='none') {
      gks_webcam_cancel_capture_click();
    }
    
    if (gks_webcam_stream!==null) {
      gks_webcam_stream.getTracks().forEach(function(track) {
        track.stop();
      });
    }
    sel_device=$('#gks_webcam_select').val();
    if (sel_device!='') {

      gks_webcam_constraints.video.deviceId=sel_device;
      
      gks_webcam_permissions_click();
      //return navigator.mediaDevices.getUserMedia(constraints).
      //  then(gotStream).catch(handleError);   
    } 
    
  });
  
  $('#gks_webcam_capture').click(function() {
    

    if (gks_webcam_video_ww>0 && gks_webcam_video_hh>0) {
      //$('#gks_webcam_canvas').css({width:gks_webcam_video_ww,height:gks_webcam_video_hh});
      document.querySelector('#audio_camera_click').play();
      gks_webcam_canvas_elem=document.getElementById('gks_webcam_canvas');
      gks_webcam_canvas_elem.width=gks_webcam_video_ww;
      gks_webcam_canvas_elem.height=gks_webcam_video_hh;
      
      const context = gks_webcam_canvas_elem.getContext("2d");
      context.drawImage(document.getElementById('gks_webcam_video'), 0, 0, gks_webcam_video_ww, gks_webcam_video_hh);
  
      gks_webcam_data_picture = gks_webcam_canvas_elem.toDataURL('image/jpeg',0.8);
      $('#gks_webcam_img').attr('src', gks_webcam_data_picture);
      //console.log(gks_webcam_data_picture.length);
      $('#gks_webcam_video').hide();
      $('#gks_webcam_capture').hide();
      $('#gks_webcam_img').show();
      $('#gks_webcam_cancel_capture').show();
      $('#gks_webcam_save').show();
      
      $('#gks_webcam_size').html(' ' + (gks_webcam_data_picture.length*(3/4)/1024).formatMoney(1, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + 'KB');
    } else {
      myalert('error:' + gks_lang('Σφάλμα. Δεν έχουν ορισθεί οι διαστάσεις'));
    }
    gks_webcam_panel_buttons_resize();
  });
  
  function gks_webcam_cancel_capture_click() {
    $('#gks_webcam_video').show();
    $('#gks_webcam_capture').show();
    $('#gks_webcam_img').hide();
    $('#gks_webcam_cancel_capture').hide();
    $('#gks_webcam_save').hide();
    $('#gks_webcam_img').attr('src', '');
    gks_webcam_panel_buttons_resize();    
  }
  
  $('#gks_webcam_cancel_capture').click(gks_webcam_cancel_capture_click);
  
  $('#gks_webcam_save').click(function() {
    
    datasend='';
    datasend+='&data_picture=' + encodeURIComponent(gks_webcam_data_picture);
    datasend+='&object_name=' + encodeURIComponent($.base64.encode(from_php_filesobjectlist_object_name));
    
    $('body').addClass('myloading');
    $.ajax({
			url: '/my/admin-filesobjectlist-webcam-save.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('body').removeClass('myloading');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  gks_webcam_cancel_capture_click();
					  $('#gks_webcam_message_ok').html('<i id="dialog_message_ok" class="fas fa-check-circle" style="color: rgb(0, 226, 32); font-size: 500%;"></i><br>' + $.base64.decode(data.message));
					  ww1=($('#gks_webcam_message_ok')).width();
					  hh1=($('#gks_webcam_message_ok')).height();
					  ww2=($('#gks_webcam_panel_video')).width();
					  hh2=($('#gks_webcam_panel_video')).height();
					  ll=((ww2-ww1)/2).myround(0);
					  tt=((hh2-hh1)/2).myround(0);
					  
					  $('#gks_webcam_message_ok').css({left:ll,top:tt,opacity:1}).show();
					  $('#gks_webcam_message_ok').animate({
                opacity: 0,
              }, 3000, function() {
                $('#gks_webcam_message_ok').hide();  
              });
            
            file_filesobjectlist_cc++; //mallon einai axristo ...
            $('#filesobjectlist_table_imagelist_photo').show();
            myhtmlimg=data.html_tr;
            if ($('#filesobjectlist_table_imagelist_photo tbody tr').length==0) {
              $('#filesobjectlist_table_imagelist_photo tbody').append(myhtmlimg);
            } else {
              $('#filesobjectlist_table_imagelist_photo tbody tr:last').after(myhtmlimg);
            }
           
            $('.webcam_new_add .filesobjectlist_delete_upload_photo').click(filesobjectlist_delete_upload_photo_click);
            $('.webcam_new_add td.tdimg_descr').click(filesobjectlist_edit_descr_click);
            $('.webcam_new_add .filesobjectlist_set_print_photo').click(filesobjectlist_set_print_photo_click);
            $('.webcam_new_add .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);

            $('.webcam_new_add').removeClass('webcam_new_add');
          
            $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
            $("#filesobjectlist_table_imagelist_photo").lightGallery({
            	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
            	thumbnail:true,
            	hideBarsDelay:1000,
            });  
  
  
            //myalert('ok:' + $.base64.decode(data.message));
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
    
    
  });

  
  function gks_webcam_panel_buttons_resize() {
   hh=$('#gks_webcam_panel_buttons').height();
   $('#gks_webcam_panel_video').css({height: 'calc(100% - ' + hh + 'px'});
   //console.log(hh);
  }
  if ($('#gks_webcam_div').length==1) {
    new ResizeObserver(gks_webcam_panel_buttons_resize).observe(gks_webcam_div);
  }
  //$('#gks_webcam_div').resize(gks_webcam_panel_buttons_resize);  


  $('#gks_filesexplore_start_from_item_photos').click(function() {
    gks_filesexplore_div_footer_show=false;
    def_folder=$(this).attr('data-def_folder');
    if (gks_filesexplore_div_start_run==false) {
      gks_filesexplore_div_start('erpul',def_folder);
    } else {
      $('#gks_filesexplore_div').show(); 
      gks_filesexplore_get_folder_data('erpul',def_folder);
    }
  });  
  $('#gks_filesexplore_start_from_item_list').click(function() {
    gks_filesexplore_div_footer_show=false;
    def_folder=$(this).attr('data-def_folder');
    if (gks_filesexplore_div_start_run==false) {
      gks_filesexplore_div_start('erpfi',def_folder);
    } else {
      $('#gks_filesexplore_div').show();  
      gks_filesexplore_get_folder_data('erpfi',def_folder);
    }
  });  
  
  var gks_filesexplode_page='';
  var gks_filesexplode_view='grid';
  
  window.gks_filesexplore_div_start = function(def_basefolder_in='',def_folder_in='') {
    gks_filesexplore_div_start_run=true;
    gks_filesexplode_page=window.location.pathname.substring(4);
    
    def_basefolder='erpfi';
    if (def_basefolder_in!='') def_basefolder=def_basefolder_in;
    def_folder='/';
    if (def_folder_in!='') def_folder=def_folder_in;
    
    def_view='grid';
    if (gks_filesexplode_page=='admin-files-explore.php' && window.location.hash!='') {
      myhash=window.location.hash.replace('#', '');
      myhash=decodeURIComponent(myhash);
      urlParams = new URLSearchParams(myhash);
      if (urlParams.get('feb')!=null) def_basefolder=urlParams.get('feb');
      if (urlParams.get('fef')!=null) def_folder=urlParams.get('fef');
      if (urlParams.get('fev')!=null) def_view=urlParams.get('fev');
    }
    gks_filesexplode_view=def_view;
    
    start_html=`
    <div id="gks_filesexplore_div" class="` + (gks_filesexplode_page=='admin-files-explore.php' ? '' : 'gks_filesexplore_div_in_item') + `" style="display:none">
      <div id="gks_filesexplore_div_inner">
        <div id="gks_filesexplore_div_header">
          <i class="fas fa-window-close" id="gks_filesexplode_div_info_close"></i>
          <div id="gks_filesexplore_div_header_row1">` + gks_lang('Εξερεύνηση αρχείων') + `</div>
          <div id="gks_filesexplore_div_header_row2">
            <div>
              <label for="gks_filesexplore_basefolder">` + gks_lang('Θέση') + `:</label>
              <select id="gks_filesexplore_basefolder" class="form-control form-control-sm ">
                <option value="erplo" ` + (def_basefolder=='erplo' ? 'selected' : '') + `>` + gks_lang('ERP Λογότυπα') + `</option>
                <option value="erpfi" ` + (def_basefolder=='erpfi' ? 'selected' : '') + `>` + gks_lang('ERP Αρχεία') + `</option>
                <option value="erpul" ` + (def_basefolder=='erpul' ? 'selected' : '') + `>` + gks_lang('ERP Μεταφορτώσεις') + `</option>
                <option value="erpdl" ` + (def_basefolder=='erpdl' ? 'selected' : '') + `>` + gks_lang('ERP Λήψεις') + `</option>
                <option value="wodpr" ` + (def_basefolder=='wodpr' ? 'selected' : '') + `>` + gks_lang('Wordpress') + `</option>
              </select>
            </div>
            <div>
              <span>` + gks_lang('Φάκελος') + `:</span>
              <span id="gks_filesexplore_folder" data-folder="` + def_folder + `">` + def_folder + `</span>
            </div>
          </div>
        </div>
        <div id="gks_filesexplore_div_body">
          <div id="gks_filesexplore_div_panel"></div>
          <div id="gks_filesexplore_div_content">
            <div id="gks_filesexplore_div_settings">
              <i class="fa-solid fa-table-cells ` + (def_view=='grid' ? 'gks_filesexplore_settings_selected' : '') + `" id="gks_filesexplore_settings_grid" title="` + gks_lang('Προλολή πίνακα') + `"></i>
              <i class="fa-solid fa-bars ` + (def_view=='list' ? 'gks_filesexplore_settings_selected' : '') + `" id="gks_filesexplore_settings_list" title="` + gks_lang('Προβολή λίστας') + `"></i>
              <span id="gks_filesexplore_settings_dummy"></span>
              <span id="gks_filesexplore_files_count"></span>
            </div>
            <div id="gks_filesexplore_div_add"></div>
            <div id="gks_filesexplore_div_files" class="` + (def_view=='list' ? 'gks_filesexplore_div_files_list' : '') + `"></div>
          </div>
        </div>
        
        <div id="gks_filesexplore_div_footer" class="`;
        
        if (gks_filesexplore_div_footer_show==false) start_html+=' gks_filesexplore_div_footer_hide';
   
   start_html+=     
        `"><div>
          <button type="button" class="btn btn-primary" id="gks_filesexplore_div_footer_btn_ok">` + gks_lang('OK') + `</button>
          <button type="button" class="btn btn btn-danger" id="gks_filesexplore_div_footer_btn_cancel">` + gks_lang('Ακύρωση') + `</button>
        </div></div>
        
      </div>
    </div>
    `;
    
    if ($('#gks_filesexplore_div_insert').length==1) { //admin-files-explore.php
      $('#gks_filesexplore_div_insert').append(start_html);
    } else {
      $(document.body).append(start_html);
    }
    $('#gks_filesexplore_settings_grid').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
    $('#gks_filesexplore_settings_list').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
    if (gks_filesexplode_page!='admin-files-explore.php') {
      $('#gks_filesexplode_div_info_close').show().click(gks_filesexplode_div_info_close_click);
    }
    $('#gks_filesexplore_div_footer_btn_cancel').click(gks_filesexplore_div_footer_btn_cancel_click);
    $('#gks_filesexplore_div_footer_btn_ok').click(gks_filesexplore_div_footer_btn_ok_click);
    
    $('#gks_filesexplore_div').show();
    basefolder=$('#gks_filesexplore_basefolder').val();
    folder=$('#gks_filesexplore_folder').attr('data-folder');
    //console.log(basefolder,folder);
    
    gks_filesexplore_get_folder_data(basefolder,folder,false);
    
    $('#gks_filesexplore_basefolder').on('change keyup paste',function() {
      basefolder=$('#gks_filesexplore_basefolder').val();
      gks_filesexplore_get_folder_data(basefolder,'/',false);
    });
    
    $(window).resize(function() {
      if (gks_filesexplore_div_start_run==false) return;
      gks_filesexplore_file_width();
    });
    
    $('#gks_filesexplore_settings_grid').click(function() {
      $('#gks_filesexplore_settings_list').removeClass('gks_filesexplore_settings_selected');
      $(this).addClass('gks_filesexplore_settings_selected');
      $('#gks_filesexplore_div_files').removeClass('gks_filesexplore_div_files_list');
      gks_filesexplode_view='grid';
      if (gks_filesexplode_page=='admin-files-explore.php') {
        myhash = 
        'feb=' + encodeURIComponent(gks_get_folder_data_last_basefolder) + 
        '&fef=' + encodeURIComponent(gks_get_folder_data_last_folder) + 
        '&fev=' + encodeURIComponent(gks_filesexplode_view);
        document.location.hash=myhash;      
      }
    });
    $('#gks_filesexplore_settings_list').click(function() {
      $('#gks_filesexplore_settings_grid').removeClass('gks_filesexplore_settings_selected');
      $(this).addClass('gks_filesexplore_settings_selected');
      $('#gks_filesexplore_div_files').addClass('gks_filesexplore_div_files_list');
      gks_filesexplode_view='list';
      if (gks_filesexplode_page=='admin-files-explore.php') {
        myhash = 
        'feb=' + encodeURIComponent(gks_get_folder_data_last_basefolder) + 
        '&fef=' + encodeURIComponent(gks_get_folder_data_last_folder) + 
        '&fev=' + encodeURIComponent(gks_filesexplode_view);
        document.location.hash=myhash;
      }
    });
    
    
  }
  
  var gks_get_folder_data_chunk_curr=1;
  var gks_get_folder_data_chunk_all=1;
  var gks_get_folder_data_chunk_file='';
  var gks_get_folder_data_files_count=0;
  var gks_get_folder_data_last_basefolder='';
  var gks_get_folder_data_last_folder='';
  
  
  window.gks_filesexplore_get_folder_data=function(basefolder,folder) {
    gks_get_folder_data_last_basefolder=basefolder;
    gks_get_folder_data_last_folder=folder;
    
    if (gks_filesexplore_div_footer_show) {
      $('#gks_filesexplore_div_footer').removeClass('gks_filesexplore_div_footer_hide');
    } else {
      $('#gks_filesexplore_div_footer').addClass('gks_filesexplore_div_footer_hide')
    }
    
    if (gks_filesexplode_page=='admin-files-explore.php') {
      myhash = 
      'feb=' + encodeURIComponent(gks_get_folder_data_last_basefolder) + 
      '&fef=' + encodeURIComponent(gks_get_folder_data_last_folder) + 
      '&fev=' + encodeURIComponent(gks_filesexplode_view);
      document.location.hash=myhash;
    }
              
    gks_get_folder_data_chunk_curr=1;
    $('#gks_filesexplore_basefolder').val(basefolder);
    $('#gks_filesexplore_folder').attr('data-folder',folder);
    
    //$('#gks_filesexplore_div_panel').html('<div style="text-align:center"><img src="/my/img/wait.gif"></div>');
    //$('#gks_filesexplore_folder').html('<div style="text-align:center"><img src="/my/img/wait.gif"></div>');
    $('#gks_filesexplore_div_files').html('<div style="text-align:center"><img src="/my/img/wait.gif"></div>');
    
    datasend='cmd=' + encodeURIComponent($.base64.encode('get_folder_data'));
    datasend+='&basefolder=' + encodeURIComponent($.base64.encode(basefolder));
    datasend+='&folder=' + encodeURIComponent($.base64.encode(folder));
    
    $.ajax({
      url: '/my/admin-files-explore-cmd.php',
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        myalert('error:' + jqXHR.responseText);
      },
      success: function(data) {
        if (!data) {
          myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            //console.log(data);
            $('#gks_filesexplore_div_panel').html(data.html_dirs);
            $('.gks_filesexplore_dir').click(gks_filesexplore_dir_click);
            
            $('#gks_filesexplore_folder').html(data.breadcrumbs);
            $('.gks_filesexplore_breadcrumb').click(gks_filesexplore_breadcrumb_click);
            
            gks_filesexplore_file_width();
            $('#gks_filesexplore_div_files').html(data.html_files);
            $('.gks_filesexplore_file.newentry').click(gks_filesexplore_file_click).removeClass('newentry');
            
            myobj=$("#gks_filesexplore_div_files").data('lightGallery');
            if (typeof myobj!='undefined') myobj.destroy(true);
            $("#gks_filesexplore_div_files").lightGallery({
            	selector: '.gks_filesexplore_file_lightgallery',
            	thumbnail:true,
            	hideBarsDelay:1000,
            }); 
            
            
            
            
            gks_get_folder_data_chunk_all=data.files_chuncks;
            gks_get_folder_data_chunk_file=data.files_chunck_file;
            gks_get_folder_data_files_count=data.files_count;
            gks_filesexplore_loadmore_html();
            
            $('#gks_filesexplore_files_count').html(data.html_footer);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      }
    });    
    
  }
  
  function gks_filesexplore_loadmore_html() {
    if (gks_get_folder_data_chunk_file=='') return;
    if (gks_get_folder_data_chunk_curr >= gks_get_folder_data_chunk_all) return;
    
    ccc=$('.gks_filesexplore_file').length;
    if (ccc>=gks_get_folder_data_files_count) return;
    mytext=gks_lang('Εμφανίζονται [1] από [2] αρχεία');
    mytext=mytext.replace('[1]',ccc);
    mytext=mytext.replace('[2]',gks_get_folder_data_files_count);
    
    
    html='<div id="gks_filesexplore_files_loadmore">' +
      '<div><span id="gks_filesexplore_files_loadmore_text">' + mytext + '</span></div>'  +
      '<div><span id="gks_filesexplore_files_loadmore_button" class="btn btn-primary" >' + gks_lang('Φόρτωση περισσότερων') + '</span></div>' + 
    '</div>';
    
    $('#gks_filesexplore_div_files').append(html);
    $('#gks_filesexplore_files_loadmore_button').click(function() {
      gks_get_folder_data_chunk_curr++;
      $('#gks_filesexplore_files_loadmore').html('<div style="text-align:center"><img src="/my/img/wait.gif"></div>');

      //console.log(gks_get_folder_data_chunk_curr);

      datasend='cmd=' + encodeURIComponent($.base64.encode('get_folder_data_loadmore'));
      datasend+='&file=' + encodeURIComponent($.base64.encode(gks_get_folder_data_chunk_file));
      datasend+='&chunck=' + gks_get_folder_data_chunk_curr;
      
      $.ajax({
        url: '/my/admin-files-explore-cmd.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: datasend,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              //console.log(data);
              $('#gks_filesexplore_files_loadmore').remove();
              $('#gks_filesexplore_div_files').append(data.html_files);
              $('.gks_filesexplore_file.newentry').click(gks_filesexplore_file_click).removeClass('newentry');
              
              myobj=$("#gks_filesexplore_div_files").data('lightGallery');
              if (typeof myobj!='undefined') myobj.destroy(true);
              $("#gks_filesexplore_div_files").lightGallery({
              	selector: '.gks_filesexplore_file_lightgallery',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              gks_filesexplore_loadmore_html();
              
              
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
      }); 
      
    });
  }
  
  function gks_filesexplore_dir_click() {
    relpath=$(this).attr('data-relpath');
    basefolder=$('#gks_filesexplore_basefolder').val();
    gks_filesexplore_get_folder_data(basefolder,relpath,false);
    //console.log(basefolder,relpath);
  }
  function gks_filesexplore_breadcrumb_click() {
    relpath=$(this).attr('data-relpath');
    basefolder=$('#gks_filesexplore_basefolder').val();
    gks_filesexplore_get_folder_data(basefolder,relpath,false);
    //console.log(basefolder,relpath);
    return false; 
  }
  

  function gks_filesexplore_file_width() {
    hhhh=$(window).height(); 
    wwww=$(window).width();
    
    if (gks_filesexplode_page=='admin-files-explore.php') {
      dddd=hhhh;
      
      if ($('gks_nav_parent').hasClass('gks_menu_pos_left')==false) {
        tttt=$('gks_nav_parent').height();
        dddd-=tttt;
      } else {
        dddd-=15;
      }
      tttt=$('.gksitemheader').height() + 24;
      dddd-=tttt;
      tttt=$('#gks_nav_session_footer').height() + 16;
      dddd-=tttt;
      $('#gks_filesexplore_div').css('height', dddd + 'px');
      
      tttt=$('#gks_filesexplore_div_header').height();
      $('#gks_filesexplore_div_body').css('height','calc(100% - ' + tttt + 'px)');
     
      tttt=$('#gks_filesexplore_div_settings').height()+20;
      tttt-=$('#gks_filesexplore_div_add').height();
      $('#gks_filesexplore_div_files').css('height','calc(100% - ' + tttt + 'px)');
    } else {
      tttt=$('#gks_filesexplore_div_header').height();
      if ($('#gks_filesexplore_div_footer').hasClass('gks_filesexplore_div_footer_hide')==false) {
        tttt+=$('#gks_filesexplore_div_footer').outerHeight();
      }
      
      $('#gks_filesexplore_div_body').css('height','calc(100% - ' + tttt + 'px)');

      tttt=$('#gks_filesexplore_div_settings').height()+20;
      tttt-=$('#gks_filesexplore_div_add').height();
      $('#gks_filesexplore_div_files').css('height','calc(100% - ' + tttt + 'px)');
      
    }   
    
    
    if (wwww<=768) $('.gks_filesexplore_file').hide();
    wid=$('#gks_filesexplore_div_files').width() -8;
    if (wwww<=768) $('.gks_filesexplore_file').show();
    
    items=Math.round(wid/200);
    if (items<1) items=1;
    item=wid/items-8;
    
    //console.log(wwww,wid,items,item);
    //$('.gks_filesexplore_file').css('width',item + 'px');
    
    $('#gks_filesexplore_file_vars').remove();
    itemh=item+25;
    imgh=item-10;
    $(document.body).append(
    '<style id="gks_filesexplore_file_vars">' + "\n" +
    ':root {' + "\n" +
    ' --gks_filesexplore_file_width:' + item + 'px;' + "\n" +
    ' --gks_filesexplore_file_height:' + itemh + 'px;' + "\n" +
    ' --gks_filesexplore_file_img_height:' + imgh + 'px;' + "\n" +
    
    '}' + "\n" +
    '</style>');
    

  }
  
  function gks_filesexplore_file_click() {
    //console.log('ss');
    if ($(this).hasClass('gks_filesexplore_file_selected')) {
      $(this).removeClass('gks_filesexplore_file_selected')
    } else {
      $(this).addClass('gks_filesexplore_file_selected')
    }
  }
  function gks_filesexplode_div_info_close_click() {
    $('#gks_filesexplore_div').hide();
  }
  function gks_filesexplore_div_footer_btn_cancel_click() {
    $('#gks_filesexplore_div').hide();
  }
  function gks_filesexplore_div_footer_btn_ok_click() {
    if (typeof gks_filesexplore_div_footer_btn_ok_click_callback === "function") { 
      gks_filesexplore_div_footer_btn_ok_click_callback();
    }
  }
});

