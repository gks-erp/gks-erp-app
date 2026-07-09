

/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var gks_plugins_js_admin_obj_send_message_datasend=[];
var gks_plugins_js_admin_obj_send_message_parameters=[];
var gks_plugins_js_admin_obj_send_message_type_change=[];

var mychange = 'change keyup paste';

jQuery(document).ready(function($) {

  
  
  var dialog_item_message;
  dialog_item_message = $('#dialog_item_message').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "button_dialog_item_message_ok",
        html: '<i class="fa fa-paper-plane"></i> '+gks_lang('Αποστολή','part2'),
        click: function() {
          mysubmit_dialog_item_message(false);
        },
      },
      {
        id: "button_dialog_item_message_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
  });


  var dialog_item_message_email_preview;
  dialog_item_message_email_preview = $('#dialog_item_message_email_preview').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "button_dialog_item_message_preview",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
  });
  

  $('#dialog_item_message_email_ispreview').click(function() {
      mysubmit_dialog_item_message(true);
  });
  
  function mysubmit_dialog_item_message(ispreview) {
    
    typeid=$('input[name=dialog_item_message_type]:checked').val();
    
    datasend='';
    datasend+='id=' + from_php_id;   
    if (typeof from_php_ctid != 'undefined') datasend+='&ctid=' + from_php_ctid;
    
    datasend+='&typeid='  + encodeURIComponent(typeid);
    if (typeid==2) {
      if (ispreview==false && $("#dialog_item_message_email_subject").val().trim().length < 1) {
        myalert('error:'+gks_lang('Εισάγετε κάποιο θέμα'));
        return false;
      }
      if (ispreview==false && $("#dialog_item_message_email_from").val().trim().length < 1) {
        myalert('error:'+gks_lang('Εισάγετε κάποιον αποστολέα'));
        return false;
      }
      if (ispreview==false && $("#dialog_item_message_email_to").val().trim().length < 1) {
        myalert('error:'+gks_lang('Εισάγετε κάποιον αποδέκτη'));
        return false;
      }
      message_val=tinyMCE.get('dialog_item_message_message_mc').getContent();
      if (ispreview==false && message_val=='') {
        myalert('error:'+gks_lang('Πληκτρολογήστε το κείμενο του μηνύματος'));
        return false;
      }
      
      datasend+='&message='  + encodeURIComponent($.base64.encode(message_val));
      datasend+='&template='  + encodeURIComponent($('#dialog_item_message_email_template').val());
      datasend+='&subject='  + encodeURIComponent($.base64.encode($('#dialog_item_message_email_subject').val()));
      datasend+='&email_from='  + encodeURIComponent($.base64.encode($('#dialog_item_message_email_from').val()));
      datasend+='&email_to='  + encodeURIComponent($.base64.encode($('#dialog_item_message_email_to').val()));

      var email_param='';
      var each_result=true;
      $("[id^=email_param_]").each(function( index ) {
//        if (ispreview==false && $(this).val().trim() == '') {
//          myalert('error:'+gks_lang('Η παράμετρος <b>[1]</b> είναι κενή').replaceAll('[1]',$(this).attr('id').substring(12)));
//          each_result=false;
//          return false;
//        }
        myeval=$(this).val().trim();
        //if (myeval=='' && ispreview) myeval='[[' + $(this).attr('id').substr(12) + ']]';
        email_param+='&' + $(this).attr('id') + '=' + encodeURIComponent($.base64.encode(myeval.replaceAll("\n",'<br>')));
      });
      if (each_result == false) return false; 
      datasend+=email_param;

      var tmp_list_files=[];
      if ($('#email_param_file_links').length==0) {//old style, all
        $('.dialog_item_message_email_attachments_checkbox:checked').each(function() {
          basefolder=$(this).attr('data-basefolder');
          if (typeof basefolder== 'undefined') basefolder='erpfi';
          
          attach_file={};
          attach_file.basefolder=basefolder;
          attach_file.path=$(this).attr('data-path');
          tmp_list_files.push(attach_file);
        });
      } else {
        $('.dialog_item_message_email_attachments_table tbody tr').each(function() {
          if ($(this).find('.dialog_item_message_email_attachments_checkbox').is(':checked')) {
            vvvv=$(this).find('td.gks_atta_shortcode_url_td a');
            if (vvvv.length==1) {
              //nothing, already in text
            } else {
              basefolder=$(this).find('.dialog_item_message_email_attachments_checkbox').attr('data-basefolder');
              if (typeof basefolder== 'undefined') basefolder='erpfi';
              
              attach_file={};
              attach_file.basefolder=basefolder;
              attach_file.path=$(this).find('.dialog_item_message_email_attachments_checkbox').attr('data-path');
              tmp_list_files.push(attach_file);
            }
          }
        });        
      }
      list_files_str = encodeURIComponent($.base64.encode(JSON.stringify(tmp_list_files)));
      datasend+='&list_files_str=' + list_files_str;
    
    } else if (typeid==3) { //sms
      message_val=$('#dialog_item_message_message_plain').val();
      datasend+='&message='  + encodeURIComponent($.base64.encode(message_val));
      datasend+='&template='  + encodeURIComponent($('#dialog_item_message_sms_template').val());
      datasend+='&sender_sms='  + encodeURIComponent($.base64.encode($('#dialog_item_message_sender_sms').val()));
      datasend+='&sender_sms_provider='  + encodeURIComponent($.base64.encode($('#dialog_item_message_sender_sms option:selected').attr('data-provider')));
      datasend+='&sender_sms_sender='  + encodeURIComponent($.base64.encode($('#dialog_item_message_sender_sms option:selected').attr('data-sender')));
      
      datasend+='&to_sms='  + encodeURIComponent($('#dialog_item_message_to_sms').val());
      
      var tmp_list_files=[];
      if ($('#sms_param_file_links').length==0) {//old style, all
        $('.dialog_item_message_email_attachments_checkbox:checked').each(function() {
          tmp_list_files.push($(this).attr('data-path'));
        });
      } else {
        $('.dialog_item_message_email_attachments_table tbody tr').each(function() {
          if ($(this).find('.dialog_item_message_email_attachments_checkbox').is(':checked')) {
            vvvv=$(this).find('td.gks_atta_shortcode_url_td a');
            if (vvvv.length==1) {
              //nothing, already in text
            } else {
              tmp_list_files.push($(this).find('.dialog_item_message_email_attachments_checkbox').attr('data-path'));
            }
          }
        });        
      }
      list_files_str = encodeURIComponent($.base64.encode(JSON.stringify(tmp_list_files)));
      datasend+='&list_files_str=' + list_files_str;
             
    } else if (typeid==4) { //viber
      message_val=$('#dialog_item_message_message_plain').val();
      datasend+='&message='  + encodeURIComponent($.base64.encode(message_val));
      datasend+='&template='  + encodeURIComponent($('#dialog_item_message_viber_template').val());
      datasend+='&to_viber='  + encodeURIComponent($('#dialog_item_message_to_viber').attr('data-user_id'));
      
      var tmp_list_files=[];
      if ($('#sms_param_file_links').length==0) {//old style, all
        $('.dialog_item_message_email_attachments_checkbox:checked').each(function() {
          tmp_list_files.push($(this).attr('data-path'));
        });
      } else {
        $('.dialog_item_message_email_attachments_table tbody tr').each(function() {
          if ($(this).find('.dialog_item_message_email_attachments_checkbox').is(':checked')) {
            vvvv=$(this).find('td.gks_atta_shortcode_url_td a');
            if (vvvv.length==1) {
              //nothing, already in text
            } else {
              tmp_list_files.push($(this).find('.dialog_item_message_email_attachments_checkbox').attr('data-path'));
            }
          }
        });        
      }
      list_files_str = encodeURIComponent($.base64.encode(JSON.stringify(tmp_list_files)));
      datasend+='&list_files_str=' + list_files_str;
             
    } else { 
      message_val=$('#dialog_item_message_message_plain').val();
      if (ispreview==false && message_val=='') {
        myalert('error:'+gks_lang('Πληκτρολογήστε το κείμενο του μηνύματος'));
        return false;
      }

      datasend+='&message='  + encodeURIComponent($.base64.encode(message_val));
      
      for(plugin_index=0; plugin_index < gks_plugins_js_admin_obj_send_message_datasend.length;plugin_index++) {
        datasend+=eval(gks_plugins_js_admin_obj_send_message_datasend[plugin_index]+'()');
      }
    
    }
    datasend+='&page='  + encodeURIComponent($.base64.encode(window.location.pathname));
    datasend+='&ispreview='   + (ispreview ? '1' : '0');
    
    if ($('#dialog_item_message_order_online_update_add').length==1) {
      if ($('#dialog_item_message_order_online_update_add').is(':checked')) {
        datasend+='&order_online=1';
        online_url=$('#online_url').attr('href');
        datasend+='&online_url=' + encodeURIComponent($.base64.encode(online_url));
      }
    }
    
    //console.log(tmp_list_files);
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-obj-send-message-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			thisispreview:ispreview,
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
					  //console.log(data);
  					if (this.thisispreview) {
  				    $('#dialog_item_message_email_preview_subject_text').html($.base64.decode(data.subject));
  				    $('#dialog_item_message_email_preview_preview').html('<iframe id="dialog_item_message_email_preview_iframe" src="' + $.base64.decode(data.preview_url) + '" style="width:100%;border:0px;height:300px;"></iframe>');
  				    $('#dialog_item_message_email_preview_iframe').on('load',  preview_iframe);

          	  dwidth=$(window).width() * 0.96;
              dheight=$(window).height() * 0.96;
          	  dialog_item_message_email_preview.dialog('option', 'width', dwidth);
          	  dialog_item_message_email_preview.dialog('option', 'height', dheight);
          	  $('#dialog_item_message_email_preview').parent().css({position:'fixed'});      
              dialog_item_message_email_preview.dialog('open'); 
    			      					  
  					} else {
    					myhtml=$.base64.decode(data.html);
    					//console.log(myhtml);
    					
    					if ($('#item_messages_body tr').length==0) {
    					  $('#item_messages_body').append(myhtml);
    					} else {
    					   $('#item_messages_body tr:first').before(myhtml);
    					}
    					var message_aa=0;
              $('#item_messages_body .message_aa').each(function() {
                message_aa++;
                $(this).html(message_aa);
              });
  
              $('#tr_messages_' + data.trid).find('.gks_email_view').click(gks_email_view_click);
              $('#tr_messages_' + data.trid).find('.gks_sms_view').click(gks_sms_view_click);
              $('#tr_messages_' + data.trid).find('.mydivexpand').click(gks_mydivexpand_click);
              
              dialog_item_message.dialog('close');
            }
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});      
    
  }

  function preview_iframe() {
    myscrollHeight=this.contentWindow.document.body.scrollHeight;
    myscrollHeight+=50;
    this.style.height = myscrollHeight + 'px';
  }
  
  window.gks_message_item_add_click_outside=function() {
    $('#dialog_item_message_type2').prop('checked',true);
    message_item_add_click('outside');
    //$('#dialog_item_message_type2').click();
  }
  window.gks_message_item_add_click_order_online=function() {
    if ($('#dialog_item_message_sender_sms option').length>0) {
      $('#dialog_item_message_type3').prop('checked',true);
    }
    if ($('#dialog_item_message_order_online_update_add').is(':checked')==false) {
      $('#dialog_item_message_order_online_update_add').click();
    }
    message_item_add_click('order_online');
    //$('#dialog_item_message_type2').click();
  }  
  
  var dialog_item_message_message_mc_loaded=false;
  
  $('#message_item_add').click(function() {
    event.stopPropagation();
    message_item_add_click('button');
  });

  function message_item_add_click(call_from) {
    
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    if (typeof need_save != 'undefined' && need_save==true) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    if ($('#online_enable').length==1 && $('#online_enable').is(':checked')) {
      $('#dialog_item_message_order_online_update').show();
      online_url=$('#online_url').attr('href');
      myurltext='URL: <a href="'+online_url+'" target="_blank" class="gks_link">'+online_url+'</a>'; 
      myurltext+=' <i class="fas fa-copy tooltipster" title="'+gks_lang('Αντιγραφή στο πρόχειρο')+'" id="online_url_copy2"></i>';
      myurltext+=' <i class="far fa-copy tooltipster" title="'+gks_lang('Αντιγραφή στο μήνυμα')+'" id="online_url_copy3"></i>';
      
      $('#dialog_item_message_online_url').html(myurltext);
      $('#online_url_copy2').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
      $('#online_url_copy3').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
      $('#email_param_online_url').val(online_url);
      
      $('#online_url_copy2').click(function() {
        val=$(this).parent().find('a').attr('href');  
        //console.log(val);
        navigator.clipboard.writeText(val);        
      });
      $('#online_url_copy3').click(function() {
        val=$(this).parent().find('a').attr('href');  
        
        vvv=$('#dialog_item_message_message_plain').val();
        if (vvv.includes(val)==false) {
          $('#dialog_item_message_message_plain').val(vvv+"\r\n"+val);
        }
        
               
      });
      
    } else {
      $('#dialog_item_message_order_online_update').hide();
    }
    
    dialog_item_message_type_change(call_from);
    
    
    if (dialog_item_message_message_mc_loaded==false) {
      dialog_item_message_message_mc_loaded=true;
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
        
        selector: '#dialog_item_message_message_mc',
        init_instance_callback: function(editor) {
          editor.on('Change', function(e) {
            //need_save=true;
          });
        },
        //readonly : (from_php_perm_ret_edit ? 0 : 1),
        setup: function(editor) {
          editor.on('init', function(e) {
            //console.log('The Editor has initialized.');
            message_item_add_continue(); 

            if (typeof run_from_steps!= 'undefined' && run_from_steps && typeof run_from_step_run!= 'undefined' && run_from_step_run=='email') {
              //console.log('email for send');
              if ($('#last_print_file').length==1) {
                var last_print_file=$('#last_print_file').text();
                //console.log(last_print_file); 
                if (last_print_file!='') {
                  $('.dialog_item_message_email_attachments_checkbox').each(function() {
                    dd=$(this).attr('data-path');
                    if (dd.endsWith('/' + last_print_file)) {
                      $(this).prop('checked', true);
                      //console.log('found last_print_file');
                      
                      setTimeout(function() {
                        $('#button_dialog_item_message_ok').click();
                        gks_eraseCookie('acc_inv_steps');
                      },1000,);                    
                      
                      return;
                    }
                  });
                }
              }
            }
          });
        }
      }); 
      
    } else {
      message_item_add_continue();
      set_def_text_plain();
    }
    
       
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  dialog_item_message.dialog('option', 'width', dwidth);
	  dialog_item_message.dialog('option', 'height', dheight);
	  $('#dialog_item_message').parent().css({position:'fixed'});     
    dialog_item_message.dialog('open'); 
      
  }
  
  function set_def_text_plain() {
    typeid=$('input[name=dialog_item_message_type]:checked').val();
    if (typeid==3) { //sms
      template_id=$('#dialog_item_message_sms_template').val();
      if (template_id!='0') {
        dialog_item_message_sms_template_change();
      }      
    } else if (typeid==4) { //viber
      template_id=$('#dialog_item_message_viber_template').val();
      if (template_id!='0') {
        dialog_item_message_viber_template_change();
      }
    }
      
  }

  function message_item_add_continue() {

    
    if (from_php_dialog_item_message_email_from_array.length>0) {
      if ($('#dialog_item_message_email_from').val()=='')
        $('#dialog_item_message_email_from').val(from_php_dialog_item_message_email_from_array[0]);
    }
    if ($('#dialog_item_message_email_to').val()=='') {
      if ($('#dr_user_email').length==1) {
        if ($('#dr_user_email').prop("tagName")=='INPUT') $('#dialog_item_message_email_to').val($('#dr_user_email').val());
        if ($('#dr_user_email').prop("tagName")=='DIV') $('#dialog_item_message_email_to').val($('#dr_user_email').text());
      }
      
      if ($('#email').length==1) $('#dialog_item_message_email_to').val($('#email').val());
      
    }
    
    
    
    
    if ($('#dialog_item_message_to_sms').val()=='') {
      if ($('#mobile').length==1) {
        $('#dialog_item_message_to_sms').val($('#mobile').val()); 
      } else if ($('#dr_user_mobile').length==1) {
        if ($('#dr_user_mobile').prop("tagName")=='INPUT') {
          $('#dialog_item_message_to_sms').val($('#dr_user_mobile').val());
        } else if ($('#dr_user_mobile').prop("tagName")=='DIV') {
          if ($('#dr_user_mobile > a').length>=1) {
            var temp_loopa=[];
            $('#dr_user_mobile > a').each(function() {
              temp_loopa.push($(this).text());
            });
            $('#dialog_item_message_to_sms').val(temp_loopa.join(', '));
          } else {
            $('#dialog_item_message_to_sms').val($('#dr_user_mobile').text());
          }
        }
      } else if ($('.gks_comm_phone_value').length>=1 && $('#ma_country_id').length==1) {
        if ($('#ma_country_id').val()=='91') {
          var temp_loopa=[];
          $('.gks_comm_phone_value').each(function() {
            vvv=$(this).val();
            if (vvv.startsWith('69')) {
              temp_loopa.push(vvv);
            }
          });
          $('#dialog_item_message_to_sms').val(temp_loopa.join(', '));
        } else {
          var temp_loopa=[];
          $('.gks_comm_phone_value').each(function() {
            temp_loopa.push($(this).val());
          });
          $('#dialog_item_message_to_sms').val(temp_loopa.join(', '));
        }
      }
    }
    
    
    $('#dialog_item_message_email_attachments').html('');    

    
    var file_list=[];
    
    
    
    var tmp_html_out='';has_invite_ics=false;
		if (window.location.pathname=='/my/admin-crm-task-item.php') {
      tmp_html_out+='<tr>' + 
      '<td style="width:50px;text-align: center;vertical-align: middle;"><input type="checkbox" class="dialog_item_message_email_attachments_checkbox" data-path="invite.ics"></td>' +
    	  '<td style="vertical-align: middle;padding-left:0px;word-break: break-all;">invite.ics</td>' +
  	    '<td style="vertical-align: middle;"></td>' +
	      '<td style="vertical-align: middle;text-align:right" nowrap=""> ~ 10 KB</td>' +
	      '<td></td>' + 
      '</tr>';
      has_invite_ics=true;
		}
		//table_imagelist_photo
    $('#filesobjectlist_table_imagelist_photo > tbody > tr').each(function() {
      tds=$(this).find('td');
      if (tds.length > 2) { //not folders
        var data_path=$(this).attr('data-path');
        //console.log(data_path);
        var tmp_html_out_tr=''; var file_name='';var shortcode_url='';
        tds.each(function(index, element) { //Integer index, Element element
          //console.log('index',index);
          if (index==0) {
            tmp_html_out1=element.outerHTML;
            tmp_html_out1=tmp_html_out1.replace('padding-left:0px;', '');
            tmp_html_out1=tmp_html_out1.replace('padding-left:10px;', '');
            tmp_html_out1=tmp_html_out1.replace('padding-left:20px;', '');
            tmp_html_out1=tmp_html_out1.replace('padding-left:30px;', '');
            
            tmp_html_out_tr+=tmp_html_out1;
            file_name+= $(element).text();
          } else if (index==4) {  
            tmp_html_out_tr+=element.outerHTML;
          } else if (index==2) {
            tmp_elem=$(element.outerHTML);
            
            if (tmp_elem.find('a').length==1) {
              tmp_elem.html(tmp_elem.find('a').html());
            }
            tmp_html_out_tr+=tmp_elem[0].outerHTML;
          } else if (index==7) {
            elemsu=$(this).find('img.filesobjectlist_set_public_file');
            if (elemsu.length==1) shortcode_url=elemsu.attr('data-shortcode_url');
            data_active=elemsu.attr('data-active'); 
            tmp_html_out_tr+='<td class="mytdcm gks_atta_shortcode_url_td" nowrap>'+ 
            (data_active=='0' 
             ? ('<img src="img/0bbl.png" class="gks_atta_shortcode_url_create" data-path="'+data_path+'"/>')
             : ('<a href="'+from_php_GKS_SITE_URL+'s/'+shortcode_url+'" target="_blank" data-shortcode_url="'+shortcode_url+'">'+shortcode_url+'</a>')
            ) + 
            '</td>';
          }
          
          
        });
        if (window.location.pathname=='/my/admin-crm-task-item.php' && tmp_html_out_tr.includes('>invite.ics<')) tmp_html_out_tr=''; //einai gia ta palia .ics arxeia
        if (tmp_html_out_tr!='') {
          file_item={};
          file_item.data_path=data_path;
          file_item.tmp_html_out_tr=tmp_html_out_tr;
          file_item.file_name=file_name;
          file_item.shortcode_url=shortcode_url;
          file_item.is_hidden=false;
          file_list.push(file_item);
          
        }
      }      

    });
    //console.log(file_list);
    print_pdf_file='';
    if ($('#gks_print').length==1) {
      if ($('#gks_print a > i.fa-download').length==1 && $('#gks_print a#last_print_file').length==1) {
        print_pdf_file=$('#gks_print a#last_print_file').text().trim();
      }
    }
    //console.log(print_pdf_file);
    
    has_hidden_cc=0;
    
    if (has_invite_ics || print_pdf_file!='') { //to ics emfanizete etsi kai allios
      for (fid=0; fid<file_list.length; fid++) {
        if (print_pdf_file==file_list[fid].file_name) {
          file_list[fid].is_hidden=false;
        } else {
          file_list[fid].is_hidden=true;
          has_hidden_cc++;  
        }
      }
    } else {
      for (fid=0; fid<file_list.length; fid++) {
        if (file_list[fid].file_name.endsWith('.xml') || file_list[fid].file_name.endsWith('.xml')) {
          file_list[fid].is_hidden=true;
          has_hidden_cc++;  
        }
      }      
    }
    

    
    if (has_hidden_cc>0) {
      $('#dialog_item_message_email_attachments_show_all').show();
    } else {
      $('#dialog_item_message_email_attachments_show_all').hide();
    }
    
    for (fid=0; fid<file_list.length; fid++) {
      tmp_html_out+='<tr ' + (file_list[fid].is_hidden ? 'style="display:none;"' : '') + '>' + 
      '<td style="width:50px;text-align: center;vertical-align: middle;"><input type="checkbox" class="dialog_item_message_email_attachments_checkbox" data-path="' + file_list[fid].data_path + '"></td>' +
      file_list[fid].tmp_html_out_tr + 
      '</tr>';
    }

    
    if (tmp_html_out=='') {
      tmp_html_out='<tr class="dialog_item_message_email_attachments_table_nofiles"><td colspan="5">'+gks_lang('Δεν βρέθηκαν αρχεία')+'</td></tr>';      
    }
    tmp_html_out='<table class="dialog_item_message_email_attachments_table table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" style="">' +

    '<thead>'+
      '<tr>'+
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'+
        '<th class="table-dark" scope="col" style="text-align: left !important;" width="0%">'+gks_lang('Όνομα')+'</th>'+
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'+gks_lang('Φωτό')+'</th>'+
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'+gks_lang('Μέγεθος')+'</th>'+
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-unlock-alt tooltipster_params" style="color: #0dcaf0;font-size: 120%;" title='+gks_lang('Δημόσιο')+'></i></th>'+
      '</tr>'+
    '</thead>'+ 
    '<tbody>'+   
    tmp_html_out +
    '</tbody>'+   
    '</table>';
    $('#dialog_item_message_email_attachments').html(tmp_html_out);
    
    
    $('.gks_atta_shortcode_url_create').click(gks_atta_shortcode_url_create_click);
  
    //console.log(tmp_html_out);
    
    typeid=$('input[name=dialog_item_message_type]:checked').val();
    if (typeid==2) {
      show_email_params($('#dialog_item_message_email_template').val());
    } else if (typeid==3 || typeid==4) {
      show_sms_params($('#dialog_item_message_sms_template').val());
    }    
    
  }
  
  function gks_atta_shortcode_url_create_click() {
    data_path=$(this).attr('data-path').trim();
    if (data_path=='') return;
    console.log(data_path);
    elemonback=$('.filesobjectlist_set_public_file[data-path="'+data_path+'"]');
    if (elemonback.length==0) return;
    elemonback.click();
  }
  
  $('#dialog_item_message_email_attachments_show_all').click(function() {
    $('.dialog_item_message_email_attachments_table tr').show();
    $(this).hide();    
  });
  
  
  function dialog_item_message_type_change(call_from) {
    typeid=$('input[name=dialog_item_message_type]:checked').val();
    
    if (typeid==1) { //esoteriki seimeiosi
      $('#dialog_item_message_email_template_div').hide();
      $('#dialog_item_message_sms_template_div').hide();
      $('#dialog_item_message_viber_template_div').hide();
      $('#dialog_item_message_email_subject_div').hide();
      $('#dialog_item_message_email_from_div').hide();
      $('#dialog_item_message_email_to_div').hide();
      
      $('#dialog_item_message_message_plain_div').show();
      $('#dialog_item_message_sms_chars').hide();
      $('#dialog_item_message_viber_format').hide();
      $('#dialog_item_message_message_mc_div').hide();
      $('#dialog_item_message_email_params_div').hide();
      $('#dialog_item_message_sms_params_div').hide();
      $('#dialog_item_message_email_attachments_div').hide();
      $('#dialog_item_message_email_ispreview_div').hide();
      $('#dialog_item_message_sender_sms_div').hide();
      $('#dialog_item_message_to_sms_div').hide();
      $('#dialog_item_message_to_viber_div').hide();
    } else if (typeid==2) { //email
      $('#dialog_item_message_email_template_div').show();
      $('#dialog_item_message_sms_template_div').hide();
      $('#dialog_item_message_viber_template_div').hide();
      $('#dialog_item_message_email_subject_div').show();
      $('#dialog_item_message_email_from_div').show();
      $('#dialog_item_message_email_to_div').show();
      
      $('#dialog_item_message_message_plain_div').hide();
      $('#dialog_item_message_sms_chars').hide();
      $('#dialog_item_message_viber_format').hide();
      $('#dialog_item_message_message_mc_div').show();
      $('#dialog_item_message_email_params_div').show();
      $('#dialog_item_message_sms_params_div').hide();
      $('#dialog_item_message_email_attachments_div').show();
      $('#dialog_item_message_email_ispreview_div').show();
      $('#dialog_item_message_sender_sms_div').hide();
      $('#dialog_item_message_to_sms_div').hide();
      $('#dialog_item_message_to_viber_div').hide();
    } else if (typeid==3) { //sms
      $('#dialog_item_message_email_template_div').hide();
      $('#dialog_item_message_sms_template_div').show();
      $('#dialog_item_message_viber_template_div').hide();
      $('#dialog_item_message_email_subject_div').hide();
      $('#dialog_item_message_email_from_div').hide();
      $('#dialog_item_message_email_to_div').hide();
      
      $('#dialog_item_message_message_plain_div').show();
      $('#dialog_item_message_sms_chars').show();
      $('#dialog_item_message_viber_format').hide();
      $('#dialog_item_message_message_mc_div').hide();
      $('#dialog_item_message_email_params_div').hide();
      $('#dialog_item_message_sms_params_div').show();
      $('#dialog_item_message_email_attachments_div').show();
      $('#dialog_item_message_email_ispreview_div').hide();
      $('#dialog_item_message_sender_sms_div').show();
      $('#dialog_item_message_to_sms_div').show();
      $('#dialog_item_message_to_viber_div').hide();
    } else if (typeid==4) { //viber
      $('#dialog_item_message_email_template_div').hide();
      $('#dialog_item_message_sms_template_div').hide();
      $('#dialog_item_message_viber_template_div').show();
      $('#dialog_item_message_email_subject_div').hide();
      $('#dialog_item_message_email_from_div').hide();
      $('#dialog_item_message_email_to_div').hide();
      
      $('#dialog_item_message_message_plain_div').show();
      $('#dialog_item_message_sms_chars').hide();
      $('#dialog_item_message_viber_format').show();
      $('#dialog_item_message_message_mc_div').hide();
      $('#dialog_item_message_email_params_div').hide();
      $('#dialog_item_message_sms_params_div').show();
      $('#dialog_item_message_email_attachments_div').show();
      $('#dialog_item_message_email_ispreview_div').hide();
      $('#dialog_item_message_sender_sms_div').hide();
      $('#dialog_item_message_to_sms_div').hide();
      $('#dialog_item_message_to_viber_div').show();
    }
    
    if (call_from=='element') {
      if (typeid==2) { //email
        dialog_item_message_email_template_change();
      } else if (typeid==3) { //sms)
        dialog_item_message_sms_template_change();
      } else if (typeid==4) { //viber
        dialog_item_message_viber_template_change();
      }
    }
    
    messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
    
	  for(plugin_index=0; plugin_index < gks_plugins_js_admin_obj_send_message_type_change.length;plugin_index++) {
      eval(gks_plugins_js_admin_obj_send_message_type_change[plugin_index]+'()');
    }
        
  }
  $('input[name=dialog_item_message_type]').change(function() {
    dialog_item_message_type_change('element');
  });
  
  $('#dialog_item_message_email_from').autocomplete({
    source: from_php_dialog_item_message_email_from_array, 
    minLength:0,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
  }).focus(function(){
    $(this).data("uiAutocomplete").search($(this).val());
  });
  
  
  function dialog_item_message_message_plain_change() {gks_resize_textarea($(this));}
  $('#dialog_item_message_message_plain').on(mychange, dialog_item_message_message_plain_change);  
  


  function dialog_item_message_email_template_change() {
    show_email_params($('#dialog_item_message_email_template').val());
  }
  $('#dialog_item_message_email_template').change(dialog_item_message_email_template_change);
  
  var prev_vals={};
  var timestamp = new Date().getTime();
  function show_email_params(template_id) {
    template_id=parseInt(template_id);if (isNaN(template_id)) template_id=0;
    if (template_id<=0) return;
    
    $("[id^=email_param_]").each(function( index ) {
      prev_vals[$(this).attr('id')]=$(this).val();
    });
    //console.log(prev_vals);
    
    datasend='&id=' + template_id;
    datasend+='&cmd=getparams';
    
    //$('body').addClass('myloading');
	  $.ajax({
			url: '/my/admin-email-templates-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass('myloading');
				if (!data) {
				  myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
  				if (data.success==true){
  				  //console.log(data);
  				  $('.gks_email_attachments_item').remove();
  				  
  				  $('.dialog_item_message_email_attachments_table tbody tr:first').before($.base64.decode(data.email_attachments));
  				  
  				  if ($('.dialog_item_message_email_attachments_table tbody tr').length<=1) {
  				    $('.dialog_item_message_email_attachments_table_nofiles').show();
  				  } else {
  				    $('.dialog_item_message_email_attachments_table_nofiles').hide();
  				  }
  				  
            $('#dialog_item_message_email_params').html($.base64.decode(data.email_params));

            $("[id^=email_param_]").each(function( index ) {
              eid=$(this).attr('id');
              if (typeof prev_vals[eid] != 'undefined') {
                $('#' + eid).val(prev_vals[eid]);
              }
            });
            
            $("[id^=email_param_]").each(function( index ) {
              jqs=$(this).attr('data-jqs');
              if (typeof jqs !== 'undefined') {
                jqs=jqs.trim();
                if (jqs!='') {
                  jqs=$.base64.decode(jqs);
                  //console.log(jqs);
                  mytext='';
                  elem=$(jqs);
                  if (elem.length>=1) {
                    if (elem.length>=2) elem=elem[0];
                    elemtype=elem.prop('tagName');
                    if (elemtype=='SELECT') {
                      mytext=elem.find('option:selected').text();
                    } else {
                      mytext=elem.prop('innerText');
                      if (mytext=='') {
                        mytext=elem.text();
                      }
                      if (mytext=='') {
                        mytext=elem.val();
                      }
                      if (mytext=='') {
                        mytext=elem.prop('innerText');
                      }
                    }
                    mytext=mytext.trim();
                    mytext=mytext.replace("\r\n",' ');
                    mytext=mytext.replace("\n",' ');
                    mytext=mytext.replace("\r",' ');
                    
                    if (mytext!='') $(this).val(mytext);
                  }
                }
                
              }
            });
            
            $('#email_param_id_order').val('#' + from_php_id);
            $('#email_param_id_hotel_reservation').val('#' + from_php_id);
            
            
            //$('#email_param_ref_number').val('#' + from_php_id);
            $('#email_param_ref_number').val('');
            if ($('.acc_inv_ref_number_head').length==1) $('#email_param_ref_number').val($('.acc_inv_ref_number_head').text());
            if ($('.order_ref_number_head').length==1) $('#sms_param_ref_number').val($('.order_ref_number_head').text());

            $('#email_param_contact_name').val('');
            if ($('.email_contact_name').length==1) {
              if ($('.email_contact_name').get(0).tagName=='INPUT') {
                $('#email_param_contact_name').val($('.email_contact_name').val());
              } else {
                $('#email_param_contact_name').val($('.email_contact_name').text());
              }
            } else if ($('input#user').length==1) {
              $('#email_param_contact_name').val($('input#user').val());
            } 
            if ( $('#dr_user_email').length==1) {
              if ($('#dr_user_email').get(0).tagName=='INPUT') {
                $('#email_param_email').val($('#dr_user_email').val());
              } else {
                $('#email_param_email').val($('#dr_user_email').text());
              }
            }
            if ( $('#dr_user_mobile').length==1) {
              if ($('#dr_user_mobile').get(0).tagName=='INPUT') {
                $('#email_param_mobile').val($('#dr_user_mobile').val());
              } else {
                $('#email_param_mobile').val($('#dr_user_mobile').text());
              }
            }            
            
            
            if ($('#email_param_from').length==1) {
              $('#dialog_item_message_email_from').val($('#email_param_from').val());
            } else {
              if (from_php_dialog_item_message_email_from_array.length>0) {
                $('#dialog_item_message_email_from').val(from_php_dialog_item_message_email_from_array[0]);
              }
            }
            
            if ($('#email_param_to').length==1) {
              $('#dialog_item_message_email_to').val($('#email_param_to').val());
            } else {
              //if ($('#dialog_item_message_email_to').val()=='') {
                if ($('#dr_user_email').length==1) {
                  if ($('#dr_user_email').prop("tagName")=='INPUT') $('#dialog_item_message_email_to').val($('#dr_user_email').val());
                  if ($('#dr_user_email').prop("tagName")=='DIV') $('#dialog_item_message_email_to').val($('#dr_user_email').text());
                }
              
                if ($('#email').length==1) {
                  $('#dialog_item_message_email_to').val($('#email').val());
                }
                if ($('.gks_comm_email_div').length>=1) {
                  var foundprimary=false;
                  $('.gks_comm_email_div').each(function() {
                    if ($(this).find('.gks_comm_email_primary_sel').length==1) {
                      vvv=$(this).find('.gks_comm_email_value').val().trim();
                      if (vvv!='') {
                        $('#dialog_item_message_email_to').val(vvv);
                        foundprimary=true;return;
                      }
                    }
                  });
                  if (foundprimary==false) {
                    $('.gks_comm_email_div').each(function() {
                      vvv=$(this).find('.gks_comm_email_value').val().trim();
                      if (vvv!='') {
                        $('#dialog_item_message_email_to').val(vvv);
                        foundprimary=true;return;
                      }
                    });                    
                  }
                }
                
              //}
              
            }
      
      
            if ($('#email_param_poso').length==1) {
              if ($('#affect_balance_all_poso').is(':checked')) {
                poso_type=$('input[name=affect_balance_all_poso_type]:checked').val();
                poso=0;
                switch (poso_type) {
                  case 'price_net': poso=parseFloat($('#bal_gks_total_price_net').attr('data-val')); break;
                  case 'price_netfpa': poso=parseFloat($('#bal_gks_total_price_netfpa').attr('data-val')); break;
                  case 'price_total': poso=parseFloat($('#bal_gks_total_price_total').attr('data-val')); break;
                  case 'pliroteo': poso=parseFloat($('#bal_gks_pliroteo').attr('data-val')); break;
                }
                if (isNaN(poso)) poso=0;
              } else {
                poso=parseFloat($('#affect_balance_poso').val());
                if (isNaN(poso)) poso=0;
              }
              
              if (poso!=0) $('#email_param_poso').val(poso.mymoney().replaceAll('&euro;','€')); 
              poso_pososto_30=0.3*poso;
              if (poso_pososto_30!=0) $('#email_param_poso_pososto_30').val(poso_pososto_30.mymoney().replaceAll('&euro;','€')); 
               
            }
            
            if ($('#email_param_bank_deposit_9digit').length==1) {
              if ($('#bank_deposit_9digit').length==1) $('#email_param_bank_deposit_9digit').val($('#bank_deposit_9digit').text());
            }
      
            if ($('#email_param_apo').length==1) {
            	if ($('#task_planned_date_from').length==1) $('#email_param_apo').val($('#task_planned_date_from').val());
            }
            if ($('#email_param_eos').length==1) {
            	if ($('#task_planned_date_to').length==1) $('#email_param_eos').val($('#task_planned_date_to').val());
            }
            
            if ($('#email_param_perigrafi').length==1) {
            	if ($('#subject').length==1) $('#email_param_perigrafi').val($('#subject').val());
          	}
            
            if ($('#email_param_topothesia').length==1) {
            	temp=[];
            	if ($('#odos').length==1 && $('#odos').val()!='') temp.push($('#odos').val());
            	if ($('#arithmos').length==1 && $('#arithmos').val()!='') temp.push($('#arithmos').val());
            	if ($('#orofos').length==1 && $('#orofos').val()!='') temp.push($('#orofos').val());
            	if ($('#perioxi').length==1 && $('#perioxi').val()!='') temp.push($('#perioxi').val());
            	if ($('#poli').length==1 && $('#poli').val()!='') temp.push($('#poli').val());
            	if ($('#tk').length==1 && $('#tk').val()!='') temp.push($('#tk').val());
            	if ($('#nomos_id').length==1 && $('#nomos_id').val()!='0') temp.push($('#nomos_id option:selected').text());
            	if (temp.length>0) $('#email_param_topothesia').val(temp.join(', '));
            }
            
            if ($('#subject').length==1) var_email_param_subject=$('#subject').val();
            
            if (data.email_subject!='') {
              var_email_param_subject=$.base64.decode(data.email_subject);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_HUMAN_NAME]]',from_php_GKS_SITE_HUMAN_NAME);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_URL]]',from_php_GKS_SITE_URL);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_OFFICIAL_SITE_URL]]',from_php_GKS_OFFICIAL_SITE_URL);
              var_email_param_subject=var_email_param_subject.replaceAll('[[GKS_SITE_EMAIL]]',from_php_GKS_SITE_EMAIL);
              //var_email_param_subject=var_email_param_subject.replaceAll('[[id_order]]',from_php_id);
     					if (typeof from_php_id !== 'undefined') var_email_param_subject=var_email_param_subject.replaceAll('[[id]]',from_php_id);
              $("#dialog_item_message_email_subject").val(var_email_param_subject);
              
            }
            if (data.email_message!='') {
              var_email_param_message=$.base64.decode(data.email_message);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_HUMAN_NAME]]',from_php_GKS_SITE_HUMAN_NAME);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_URL]]',from_php_GKS_SITE_URL);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_OFFICIAL_SITE_URL]]',from_php_GKS_OFFICIAL_SITE_URL);
              var_email_param_message=var_email_param_message.replaceAll('[[GKS_SITE_EMAIL]]',from_php_GKS_SITE_EMAIL);
              if (typeof from_php_id !== 'undefined') var_email_param_message=var_email_param_message.replaceAll('[[id]]',from_php_id);
              pelatis_name='';
              if ($('#first_name').length==1 && $('#last_name').length==1) pelatis_name=($('#first_name').val() + ' ' + $('#last_name').val()).trim();
              var_email_param_message=var_email_param_message.replaceAll('[[pelatis_name]]',pelatis_name);
              
              
              tinyMCE.get('dialog_item_message_message_mc').setContent(var_email_param_message);
            }
            
            
            
      
            $('#dialog_item_message_email_params_div .tooltipster_params').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
            $('.set_def_bank_accounts').click(set_def_bank_accounts);
            if ($('#email_param_get_list_bank_accounts').length==1 && $('#email_param_get_list_bank_accounts').val()=='') {
              $('#email_param_get_list_bank_accounts').val(from_php_get_list_bank_accounts.replaceAll('<br>',"\n"));
            }
            $('.set_def_list_reservation_rooms').click(set_def_list_reservation_rooms);
            if ($('#email_param_get_list_reservation_rooms_gr').length==1 && $('#email_param_get_list_reservation_rooms_gr').val()=='') {
              if (typeof from_php_get_list_reservation_rooms_gr !== 'undefined') $('#email_param_get_list_reservation_rooms_gr').val(from_php_get_list_reservation_rooms_gr.replaceAll('<br>',"\n"));
            }   
        	  if ($('#email_param_get_list_reservation_rooms_en').length==1 && $('#email_param_get_list_reservation_rooms_en').val()=='') {
              if (typeof from_php_get_list_reservation_rooms_en !== 'undefined') $('#email_param_get_list_reservation_rooms_en').val(from_php_get_list_reservation_rooms_en.replaceAll('<br>',"\n"));
            } 
            
            
            $('.set_def_file_links').click(function() {
              set_def_file_links(true);
            });
            set_def_file_links(false);
            $('.dialog_item_message_email_attachments_checkbox').unbind('change').change(dialog_item_message_email_attachments_checkbox_change);
            
            
        	  for(plugin_index=0; plugin_index < gks_plugins_js_admin_obj_send_message_parameters.length;plugin_index++) {
              eval(gks_plugins_js_admin_obj_send_message_parameters[plugin_index]+'()');
            }
        	  
        	  //gks_myscroll();
        	  
  				} else {
  				  myalert('error:' + $.base64.decode(data.message));
  				}
  			}
			}
		});
		
  }

  var sms_message_template='';
  function show_sms_params(template_id) {
    template_id=parseInt(template_id);if (isNaN(template_id)) template_id=0;
    if (template_id<=0) return;
    
    $("[id^=sms_param_]").each(function( index ) {
      prev_vals[$(this).attr('id')]=$(this).val();
    });
    //console.log(prev_vals);
    
    datasend='&id=' + template_id;
    datasend+='&cmd=getparams';
    //$('body').addClass('myloading');
	  $.ajax({
			url: '/my/admin-sms-templates-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass('myloading');
				if (!data) {
				  myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
  				if (data.success==true){
  				  //console.log(data);
  				  $('.gks_sms_attachments_item').remove();
  				  
  				  $('.dialog_item_message_sms_attachments_table tr:first').before($.base64.decode(data.sms_attachments));
  				  
  				  if ($('.dialog_item_message_sms_attachments_table tr').length<=1) {
  				    $('.dialog_item_message_sms_attachments_table_nofiles').show();
  				  } else {
  				    $('.dialog_item_message_sms_attachments_table_nofiles').hide();
  				  }
  				  
            $('#dialog_item_message_sms_params').html($.base64.decode(data.sms_params));

            $("[id^=sms_param_]").each(function( index ) {
              eid=$(this).attr('id');
              if (typeof prev_vals[eid] != 'undefined') {
                $('#' + eid).val(prev_vals[eid]);
              }
            });
            
            $("[id^=sms_param_]").each(function( index ) {
              jqs=$(this).attr('data-jqs');
              if (typeof jqs !== 'undefined') {
                jqs=jqs.trim();
                if (jqs!='') {
                  jqs=$.base64.decode(jqs);
                  //console.log(jqs);
                  mytext='';
                  elem=$(jqs);
                  if (elem.length>=1) {
                    if (elem.length>=2) elem=elem[0];
                    elemtype=elem.prop('tagName');
                    if (elemtype=='SELECT') {
                      mytext=elem.find('option:selected').text();
                    } else {
                      mytext=elem.prop('innerText');
                      if (mytext=='') {
                        mytext=elem.text();
                      }
                      if (mytext=='') {
                        mytext=elem.val();
                      }
                      if (mytext=='') {
                        mytext=elem.prop('innerText');
                      }
                    }
                    mytext=mytext.trim();
                    mytext=mytext.replace("\r\n",' ');
                    mytext=mytext.replace("\n",' ');
                    mytext=mytext.replace("\r",' ');
                    
                    if (mytext!='') $(this).val(mytext);
                  }
                }
                
              }
            });
            
            $('#sms_param_id_order').val('#' + from_php_id);
            $('#sms_param_id_hotel_reservation').val('#' + from_php_id);
            
            
            //$('#sms_param_ref_number').val('#' + from_php_id);
            $('#sms_param_ref_number').val('');
            if ($('.acc_inv_ref_number_head').length==1) $('#sms_param_ref_number').val($('.acc_inv_ref_number_head').text());
            if ($('.order_ref_number_head').length==1) $('#sms_param_ref_number').val($('.order_ref_number_head').text());
            
            $('#sms_param_contact_name').val('');
            if ($('.email_contact_name').length==1) {
              if ($('.email_contact_name').get(0).tagName=='INPUT') {
                $('#sms_param_contact_name').val($('.email_contact_name').val());
              } else {
                $('#sms_param_contact_name').val($('.email_contact_name').text());
              }
            } else if ($('input#user').length==1) {
              $('#sms_param_contact_name').val($('input#user').val());
            } 

            if ( $('#dr_user_email').length==1) {
              if ($('#dr_user_email').get(0).tagName=='INPUT') {
                $('#sms_param_email').val($('#dr_user_email').val());
              } else {
                $('#sms_param_email').val($('#dr_user_email').text());
              }
            }
            if ( $('#dr_user_mobile').length==1) {
              if ($('#dr_user_mobile').get(0).tagName=='INPUT') {
                $('#sms_param_mobile').val($('#dr_user_mobile').val());
              } else {
                $('#sms_param_mobile').val($('#dr_user_mobile').text());
              }
            }
                        

            
            
            if ($('#sms_param_from').length==1) {
              $('#dialog_item_message_sender_sms').val($('#sms_param_from').val());
            } else {
              if (from_php_dialog_item_message_sender_sms_def!='') {
                $('#dialog_item_message_sender_sms').val(from_php_dialog_item_message_sender_sms_def);
              }
            }
            
            if ($('#sms_param_to').length==1) {
              $('#dialog_item_message_to_sms').val($('#sms_param_to').val());
            } else {
              //if ($('#dialog_item_message_to_sms').val()=='') {
                if ($('#dr_user_mobile').length==1) {
                  if ($('#dr_user_mobile').prop("tagName")=='INPUT') $('#dialog_item_message_to_sms').val($('#dr_user_mobile').val());
                  if ($('#dr_user_mobile').prop("tagName")=='DIV') $('#dialog_item_message_to_sms').val($('#dr_user_mobile').text());
                }
              
                if ($('#mobile').length==1) {
                  $('#dialog_item_message_to_sms').val($('#mobile').val());
                }
                
                if ($('.gks_comm_phone_div').length>=1) {
                  var foundprimary=false;
                  $('.gks_comm_phone_div').each(function() {
                    if ($(this).find('.gks_comm_phone_primary_sel').length==1) {
                      vvv=$(this).find('.gks_comm_phone_value').val().trim();
                      if (vvv!='') {
                        $('#dialog_item_message_to_sms').val(vvv);
                        foundprimary=true;return;
                      }
                    }
                  });
                  if (foundprimary==false) {
                    $('.gks_comm_phone_div').each(function() {
                      vvv=$(this).find('.gks_comm_phone_value').val().trim();
                      if (vvv!='') {
                        $('#dialog_item_message_to_sms').val(vvv);
                        foundprimary=true;return;
                      }
                    });                    
                  }
                }
                
              //}
              
            }
      
      
            if ($('#sms_param_poso').length==1) {
              if ($('#affect_balance_all_poso').is(':checked')) {
                poso_type=$('input[name=affect_balance_all_poso_type]:checked').val();
                poso=0;
                switch (poso_type) {
                  case 'price_net': poso=parseFloat($('#bal_gks_total_price_net').attr('data-val')); break;
                  case 'price_netfpa': poso=parseFloat($('#bal_gks_total_price_netfpa').attr('data-val')); break;
                  case 'price_total': poso=parseFloat($('#bal_gks_total_price_total').attr('data-val')); break;
                  case 'pliroteo': poso=parseFloat($('#bal_gks_pliroteo').attr('data-val')); break;
                }
                if (isNaN(poso)) poso=0;
              } else {
                poso=parseFloat($('#affect_balance_poso').val());
                if (isNaN(poso)) poso=0;
              }
              
              if (poso!=0) $('#sms_param_poso').val(poso.mymoney().replaceAll('&euro;','€')); 
              poso_pososto_30=0.3*poso;
              if (poso_pososto_30!=0) $('#sms_param_poso_pososto_30').val(poso_pososto_30.mymoney().replaceAll('&euro;','€')); 
               
            }
            
            if ($('#sms_param_bank_deposit_9digit').length==1) {
              if ($('#bank_deposit_9digit').length==1) $('#sms_param_bank_deposit_9digit').val($('#bank_deposit_9digit').text());
            }
      
            if ($('#sms_param_apo').length==1) {
            	if ($('#task_planned_date_from').length==1) $('#sms_param_apo').val($('#task_planned_date_from').val());
            }
            if ($('#sms_param_eos').length==1) {
            	if ($('#task_planned_date_to').length==1) $('#sms_param_eos').val($('#task_planned_date_to').val());
            }
            
            if ($('#sms_param_perigrafi').length==1) {
            	if ($('#subject').length==1) $('#sms_param_perigrafi').val($('#subject').val());
          	}
            
            if ($('#sms_param_topothesia').length==1) {
            	temp=[];
            	if ($('#odos').length==1 && $('#odos').val()!='') temp.push($('#odos').val());
            	if ($('#arithmos').length==1 && $('#arithmos').val()!='') temp.push($('#arithmos').val());
            	if ($('#orofos').length==1 && $('#orofos').val()!='') temp.push($('#orofos').val());
            	if ($('#perioxi').length==1 && $('#perioxi').val()!='') temp.push($('#perioxi').val());
            	if ($('#poli').length==1 && $('#poli').val()!='') temp.push($('#poli').val());
            	if ($('#tk').length==1 && $('#tk').val()!='') temp.push($('#tk').val());
            	if ($('#nomos_id').length==1 && $('#nomos_id').val()!='0') temp.push($('#nomos_id option:selected').text());
            	if (temp.length>0) $('#sms_param_topothesia').val(temp.join(', '));
            }
            
//            if (data.sms_subject!='') {
            var_sms_param_message='';
            if (data.sms_message!='') {
              var_sms_param_message=$.base64.decode(data.sms_message);
              var_sms_param_message=var_sms_param_message.replaceAll('[[GKS_SITE_HUMAN_NAME]]',from_php_GKS_SITE_HUMAN_NAME);
              var_sms_param_message=var_sms_param_message.replaceAll('[[GKS_SITE_URL]]',from_php_GKS_SITE_URL);
              var_sms_param_message=var_sms_param_message.replaceAll('[[GKS_OFFICIAL_SITE_URL]]',from_php_GKS_OFFICIAL_SITE_URL);
              //var_sms_param_message=var_sms_param_message.replaceAll('[[GKS_SITE_SMS]]',from_php_GKS_SITE_SMS);
              if (typeof from_php_id !== 'undefined') var_sms_param_message=var_sms_param_message.replaceAll('[[id]]',from_php_id);
              pelatis_name='';
              if ($('#first_name').length==1 && $('#last_name').length==1) pelatis_name=($('#first_name').val() + ' ' + $('#last_name').val()).trim();
              var_sms_param_message=var_sms_param_message.replaceAll('[[pelatis_name]]',pelatis_name);
              
              sms_message_template=var_sms_param_message;

//              $('#dialog_item_message_message_plain').val(var_sms_param_message);
//              messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
//              gks_resize_textarea($('#dialog_item_message_message_plain'));
            }
            
            
            
      
            $('#dialog_item_message_sms_params_div .tooltipster_params').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
            $('.set_def_bank_accounts').click(set_def_bank_accounts);
            $('.set_def_list_reservation_rooms').click(set_def_list_reservation_rooms);
            if ($('#sms_param_get_list_bank_accounts').length==1 && $('#sms_param_get_list_bank_accounts').val()=='') {
              $('#sms_param_get_list_bank_accounts').val(from_php_get_list_bank_accounts.replaceAll('<br>',"\n"));
            }
            if ($('#sms_param_get_list_reservation_rooms_gr').length==1 && $('#sms_param_get_list_reservation_rooms_gr').val()=='') {
              if (typeof from_php_get_list_reservation_rooms_gr !== 'undefined') $('#sms_param_get_list_reservation_rooms_gr').val(from_php_get_list_reservation_rooms_gr.replaceAll('<br>',"\n"));
            }   
        	  if ($('#sms_param_get_list_reservation_rooms_en').length==1 && $('#sms_param_get_list_reservation_rooms_en').val()=='') {
              if (typeof from_php_get_list_reservation_rooms_en !== 'undefined') $('#sms_param_get_list_reservation_rooms_en').val(from_php_get_list_reservation_rooms_en.replaceAll('<br>',"\n"));
            } 

            $('.set_def_file_links').click(function() {
              set_def_file_links(true);
            });
            set_def_file_links(false);
            $('.dialog_item_message_email_attachments_checkbox').unbind('change').change(dialog_item_message_email_attachments_checkbox_change);
            
            sms_message_template_render();
            $('#dialog_item_message_sms_params input, #dialog_item_message_sms_params textarea').on(mychange,dialog_item_message_sms_params_input_change);
            
        	  for(plugin_index=0; plugin_index < gks_plugins_js_admin_obj_send_message_parameters.length;plugin_index++) {
              eval(gks_plugins_js_admin_obj_send_message_parameters[plugin_index]+'()');
            }
        	  
        	  //gks_myscroll();
        	  
  				} else {
  				  myalert('error:' + $.base64.decode(data.message));
  				}
  			}
			}
		});
		
  }

  function sms_message_template_render() {
    var mytextrender=sms_message_template;
    $('#dialog_item_message_sms_params input, #dialog_item_message_sms_params textarea').each(function() {
      inid=$(this).attr('id');
      if (inid.startsWith('sms_param_') && inid.length>10) {
        inid2=inid.substring(10);
        inval=$(this).val().trim();
        mytextrender=mytextrender.replaceAll('[['+inid2+']]',inval);
      }
    });
      
    $('#dialog_item_message_message_plain').val(mytextrender);
    messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
    gks_resize_textarea($('#dialog_item_message_message_plain'));
  }
  function dialog_item_message_sms_params_input_change() {
    sms_message_template_render();
  }
  
  
  
  
  function set_def_bank_accounts() {
    $('#email_param_get_list_bank_accounts').val(from_php_get_list_bank_accounts.replaceAll('<br>',"\n"));
    $('#sms_param_get_list_bank_accounts').val(from_php_get_list_bank_accounts.replaceAll('<br>',"\n"));
    sms_message_template_render();
  }  
  function set_def_list_reservation_rooms() {
    if (typeof from_php_get_list_reservation_rooms_gr !== 'undefined') {
      $('#email_param_get_list_reservation_rooms_gr').val(from_php_get_list_reservation_rooms_gr.replaceAll('<br>',"\n"));
      $('#sms_param_get_list_reservation_rooms_gr').val(from_php_get_list_reservation_rooms_gr.replaceAll('<br>',"\n"));
      sms_message_template_render();
    }
    if (typeof from_php_get_list_reservation_rooms_en !== 'undefined') {
      $('#email_param_get_list_reservation_rooms_en').val(from_php_get_list_reservation_rooms_en.replaceAll('<br>',"\n"));
      $('#sms_param_get_list_reservation_rooms_en').val(from_php_get_list_reservation_rooms_en.replaceAll('<br>',"\n"));
      sms_message_template_render();
    }
  }
  function set_def_file_links(andrender) {
    if ($('#sms_param_file_links').length==0 && $('#email_param_file_links').length==0) return;
  
    var file_links_array=[];
    $('.dialog_item_message_email_attachments_table tbody tr').each(function() {
      if ($(this).find('.dialog_item_message_email_attachments_checkbox').is(':checked')) {
        vvvv=$(this).find('td.gks_atta_shortcode_url_td a');
        if (vvvv.length==1) {
          vvvv=vvvv.text();
          file_links_array.push(from_php_GKS_SITE_URL+'s/'+vvvv);
        }
      }
    });
    if ($('#sms_param_file_links').prop('tagName')=='INPUT') {
      $('#sms_param_file_links').val(file_links_array.join(' '));
    } else {
      $('#sms_param_file_links').val(file_links_array.join("\n"));
    }
    if ($('#email_param_file_links').prop('tagName')=='INPUT') {
      $('#email_param_file_links').val(file_links_array.join(' '));
    } else {
      $('#email_param_file_links').val(file_links_array.join("\n"));
    }
    
    if (andrender) sms_message_template_render();

  }
  function dialog_item_message_email_attachments_checkbox_change() {
    set_def_file_links(true);
  }
  
  $('#dialog_item_message_to_sms').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        comm_type:'phone',
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
    select: function( event, ui ) {
      $('#dialog_item_message_to_sms').attr('data-user_id',ui.item.user_id);
    },
    change: function (event, ui) {
        if(!ui.item){
        //  $('#dialog_item_message_to_sms').val('').attr('data-user_id','0');
          $('#dialog_item_message_to_sms').attr('data-user_id','0');
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
  
  
  $('#dialog_item_message_to_viber').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        viber: 1,
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
      $('#dialog_item_message_to_viber').attr('data-user_id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#dialog_item_message_to_viber').val('').attr('data-user_id','0');
        }
    },
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
  });
  


  $("#dialog_item_message_message_plain").on('change keyup paste', function() {
    messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
  });       
  messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
    

  function dialog_item_message_sms_template_change() {
    //data_text=$('#dialog_item_message_sms_template option:selected').attr('data-text').trim();
    //if (data_text=='') return;
    //data_text=$.base64.decode(data_text);
    //$('#dialog_item_message_message_plain').val(data_text);
    //messagesms_change('dialog_item_message_message_plain','dialog_item_message_sms_chars');
    //gks_resize_textarea($('#dialog_item_message_message_plain'));
    show_sms_params($('#dialog_item_message_sms_template').val());
  }
  
  $('#dialog_item_message_sms_template').change(dialog_item_message_sms_template_change);
  
  function dialog_item_message_viber_template_change() {
    //data_text=$('#dialog_item_message_viber_template option:selected').attr('data-text').trim();
    //if (data_text=='') return;
    //data_text=$.base64.decode(data_text);
    //$('#dialog_item_message_message_plain').val(data_text);
    //gks_resize_textarea($('#dialog_item_message_message_plain')); 
    show_sms_params($('#dialog_item_message_viber_template').val());
     
  }
  $('#dialog_item_message_viber_template').change(dialog_item_message_viber_template_change);



      
});



