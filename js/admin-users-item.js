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
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});

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

  $('#submit_button_ok_custom').click(function(event) {mysubmit(); return false;});

  var file_cc=0;
    
  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {

          if (from_php_id<=0) {
            myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την επαφή'));
            return;
          }
                  
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα την επαφή'));
          }
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου') + ': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου') + ': ' + data.originalFiles[0]['size']);
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
              
              if ($('#form_user_photo').val() == '') {
                $('#form_user_photo').val(file.url_thumb);
                $('#form_user_photo_img').attr("src",file.url_thumb);  
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
      
	delete_upload_click_photo = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-users-item-photo-delete.php?id=' + uid,
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
  					
  					if (this.mydata_url == $('#form_user_photo').val()) {
  					  need_save=true;
    					if ($(".set_profile_photo").length == 0) {
    					  
                $('#form_user_photo').val('');
                $('#form_user_photo_img').attr("src",'/my/img/avatar.png');
                $('#reset_profile_photo').hide();
                
              } else {
                
                $(".set_profile_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_user_photo').val(data_url);
                  $('#form_user_photo_img').attr("src",data_url);
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
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την επαφή')); return;}	  
    need_save=true;
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_user_photo').val(data_url);
    $('#form_user_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την επαφή')); return;}	  
    need_save=true;
    $('#form_user_photo').val('');
    $('#form_user_photo_img').attr("src",'/my/img/avatar.png');   
    $('#reset_profile_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  }); 

  //end photo

  var dialog_protypdays;
  var file_cv_cc=0;
  var dialog_edit_company_sxolio;

  $('#date_hire').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#exit_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  function myagefunction() {
    var dvalhtml = $('#genisi_date').val();
    if (dvalhtml=='' || dvalhtml=='__/__/____')  {
      $('#span_calc_age').html(gks_lang('Συμπληρώστε την ημερομηνία γέννησης'));
    } else {
      var dval = $('#genisi_date').datetimepicker('getValue');
      if (dval == null) {
        $('#span_calc_age').html(gks_lang('Συμπληρώστε την ημερομηνία γέννησης'));
      } else {
        $('#span_calc_age').html(calc_age(dval));
      }
    }
  }
  $('#genisi_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y',timepicker:false,dayOfWeekStart:1,onChangeDateTime: function(dp,$input) {
      myagefunction();      
      need_save=true;
    }
  }));

  if ($('#genisi_date').length>0) myagefunction();
  

  
  
  

    
      

  
  function messagesms_countchars(a) {
    if (typeof a == 'undefined') {
      return false;  
    }

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
    var aa=messagesms_countchars( $("#messagesms").val() );

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
    aa=smscc + ' SMS, ' + leftcc + ' '+gks_lang('εναπομείναντες χαρακτήρες');
    $("#chars").html(aa);    
  }
  

  $("#messagesms").on(mychange, function() {
    messagesms_change();
  });   
  messagesms_change();    
      
  $(".smstext").click(function() {
    var mytext=$(this).attr('data-value');
    mytext = $.base64.decode(mytext);
    $("#messagesms").val(mytext);
    messagesms_change();
  });  
  
  
  $("#mybuttonsms").click(function() {
    
    myreload=false;
    
    if ($("#tosms").val().trim().length < 10) {
      myalert('error:'+gks_lang('Εισάγετε κάποιον αποδέκτη'));
      return false;
    }
    if ($("#messagesms").val().trim().length < 2) {
      myalert('error:'+gks_lang('Εισάγετε κάποιο μήνυμα'));
      return false;
    }
    
    smslen=messagesms_countchars( $("#messagesms").val() );
    if (smslen> 918) {
      myalert('error:'+gks_lang('Πολύ μεγάλο μέγεθος κειμένου'));
      return false;
    }    
    
    mydatasend='from=' + encodeURIComponent($.base64.encode($("#fromsms").val().trim()));
    mydatasend+='&to=' + encodeURIComponent($.base64.encode($("#tosms").val().trim()));
    mydatasend+='&message=' + encodeURIComponent($.base64.encode($("#messagesms").val().trim()));
    mydatasend+='&model=' + encodeURI('user_add');
    mydatasend+='&model_id=' +from_php_id;
    
    
    $("body").addClass("myloading");
	  $.ajax({
			url: '/my/admin-sms-send-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: mydatasend,
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
  				  myreload=true;
  				  myalert('ok:' + $.base64.decode(data.message));
  				  return;
  				}
				
		
  			}
			}
			
		});          
		
    
  });  
  

  

      

  


 
    
  function mysubmit() {
    
    datasend='';

 
    if ($("#mypostform #old_code").length>0) datasend+='&old_code='  + encodeURI($("#mypostform #old_code").val().trim());

    if ($("#mypostform #gks_lang").val()==null) {
      myalert('error:'+gks_lang('Ορίστε την γλώσσα'));
      return;  
    }

    datasend+='&gks_sex='  + encodeURIComponent($("#mypostform #gks_sex").val().trim());
    datasend+='&gks_lang='  + encodeURIComponent($("#mypostform #gks_lang").val().trim());
    datasend+='&user_login='  + encodeURI($("#mypostform #user_login").val().trim());
    datasend+='&myfirst_name='  + encodeURIComponent($.base64.encode($("#mypostform #myfirst_name").val().trim()));
    datasend+='&mylast_name='  + encodeURIComponent($.base64.encode($("#mypostform #mylast_name").val().trim()));
    datasend+='&user_nicename='  + encodeURIComponent($.base64.encode($("#mypostform #user_nicename").val().trim()));
    datasend+='&display_name='  + encodeURIComponent($.base64.encode($("#mypostform #display_name").val().trim()));
    datasend+='&viber_id='  + encodeURIComponent($.base64.encode($("#mypostform #viber_id").val().trim()));
    datasend+='&user_pass_pure='  + encodeURIComponent($.base64.encode($("#mypostform #user_pass_pure").val().trim()));
    if ($("#mypostform #user_pin").length>0) datasend+='&user_pin='  + encodeURIComponent($.base64.encode($("#mypostform #user_pin").val().trim()));
    
    
    datasend+='&fiscal_position_id='  + encodeURI($("#mypostform #fiscal_position_id").val().trim());
    datasend+='&pricelist_id='  + encodeURI($("#mypostform #pricelist_id").val().trim());
    datasend+='&generic_ekprosi='  + encodeURI($("#mypostform #generic_ekprosi").val().trim());

    datasend+='&job_title='  + encodeURIComponent($.base64.encode($("#mypostform #job_title").val().trim()));
    datasend+='&eponimia='  + encodeURIComponent($.base64.encode($("#mypostform #eponimia").val().trim()));
    datasend+='&title='  + encodeURIComponent($.base64.encode($("#mypostform #title").val().trim()));
    datasend+='&afm='  + encodeURIComponent($.base64.encode($("#mypostform #afm").val().trim()));
    datasend+='&doy='  + encodeURIComponent($.base64.encode($("#mypostform #doy").val().trim()));
    datasend+='&epaggelma='  + encodeURIComponent($.base64.encode($("#mypostform #epaggelma").val().trim()));
    
    
    datasend+='&gemi_number='  + encodeURIComponent($.base64.encode($("#mypostform #gemi_number").val().trim()));
    datasend+='&is_b2g=' + ($('#is_b2g').is(':checked') ? '1' : '0');
    datasend+='&b2g_aaht_code='  + encodeURIComponent($.base64.encode($("#mypostform #b2g_aaht_code").val().trim()));
    datasend+='&b2g_aaht_name='  + encodeURIComponent($.base64.encode($("#mypostform #b2g_aaht_name").val().trim()));
    datasend+='&b2g_aaht_foreas='  + encodeURIComponent($.base64.encode($("#mypostform #b2g_aaht_foreas").val().trim()));
    datasend+='&b2g_aaht_typos_forea='  + encodeURIComponent($.base64.encode($("#mypostform #b2g_aaht_typos_forea").val().trim()));
    datasend+='&b2g_aaht_kodikos_ekatharisis='  + encodeURIComponent($.base64.encode($("#mypostform #b2g_aaht_kodikos_ekatharisis").val().trim()));
    
    
    datasend+='&ma_branch='  + encodeURIComponent($.base64.encode($("#mypostform #ma_branch").val().trim()));
    datasend+='&ma_odos='  + encodeURIComponent($.base64.encode($("#mypostform #ma_odos").val().trim()));
    datasend+='&ma_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #ma_arithmos").val().trim()));
    datasend+='&ma_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #ma_orofos").val().trim()));
    datasend+='&ma_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #ma_perioxi").val().trim()));
    datasend+='&ma_poli='  + encodeURIComponent($.base64.encode($("#mypostform #ma_poli").val().trim()));
    datasend+='&ma_tk='  + encodeURIComponent($.base64.encode($("#mypostform #ma_tk").val().trim()));
    datasend+='&ma_country_id='  + encodeURIComponent($("#mypostform #ma_country_id").val().trim());
    datasend+='&ma_nomos_id='  + encodeURIComponent($("#mypostform #ma_nomos_id").val().trim());

    datasend+='&ma_latitude='  + encodeURIComponent($("#mypostform #ma_latitude").val().trim());
    datasend+='&ma_longitude='  + encodeURIComponent($("#mypostform #ma_longitude").val().trim());




    //datasend+='&phone_home='  + encodeURI($("#mypostform #phone_home").val().trim());
    datasend+='&user_HumanInitial='  + encodeURI($("#mypostform #user_HumanInitial").val().trim());
    
    datasend+='&amka='  + encodeURI($("#mypostform #amka").val().trim());
    datasend+='&ama_eam='  + encodeURI($("#mypostform #ama_eam").val().trim());
    datasend+='&arithmos_tautoitas='  + encodeURI($("#mypostform #arithmos_tautoitas").val().trim());
    datasend+='&arxi_ekdosis='  + encodeURI($("#mypostform #arxi_ekdosis").val().trim());
    datasend+='&onoma_patera='  + encodeURIComponent($.base64.encode($("#mypostform #onoma_patera").val().trim()));
    datasend+='&onoma_miteras='  + encodeURIComponent($.base64.encode($("#mypostform #onoma_miteras").val().trim()));
    datasend+='&oikogeniaki_katastasti_id='  + encodeURI($("#mypostform #oikogeniaki_katastasti_id").val().trim());
    datasend+='&oikogeniaki_katastasti_paidia='  + encodeURI($("#mypostform #oikogeniaki_katastasti_paidia").val().trim());

    datasend+='&pelati_sxolio='  + encodeURI($("#mypostform #pelati_sxolio").val().trim());
    datasend+='&order_sxolio='  + encodeURI($("#mypostform #order_sxolio").val().trim());
    

    if ($("#mypostform #genisi_date").length>0) datasend+='&genisi_date='  + encodeURI($("#mypostform #genisi_date").val().trim());




    if ($("#mypostform #description").length>0) datasend+='&description='  + encodeURI($("#mypostform #description").val().trim());
    if ($("#mypostform #ethnikotita").length>0) datasend+='&ethnikotita='  + encodeURI($("#mypostform #ethnikotita").val().trim());
    if ($("#mypostform #alli_apasxolisi").length>0) datasend+='&alli_apasxolisi='  + encodeURI($("#mypostform #alli_apasxolisi").val().trim());
    if ($("#mypostform #cv_proipiresia").length>0) datasend+='&cv_proipiresia='  + encodeURI($("#mypostform #cv_proipiresia").val().trim());
    if ($("#mypostform #cv_spoydes").length>0) datasend+='&cv_spoydes='  + encodeURI($("#mypostform #cv_spoydes").val().trim());
    if ($("#mypostform #cv_seminaria").length>0) datasend+='&cv_seminaria='  + encodeURI($("#mypostform #cv_seminaria").val().trim());
    if ($("#mypostform #cv_mitriki_glossa").length>0) datasend+='&cv_mitriki_glossa='  + encodeURI($("#mypostform #cv_mitriki_glossa").val().trim());
    if ($("#mypostform #cv_jenes_glosses").length>0) datasend+='&cv_jenes_glosses='  + encodeURI($("#mypostform #cv_jenes_glosses").val().trim());
    if ($("#mypostform #cv_sxesi_me_photografia").length>0) datasend+='&cv_sxesi_me_photografia='  + encodeURI($("#mypostform #cv_sxesi_me_photografia").val().trim());
    if ($("#mypostform #cv_has_bike").length>0) datasend+='&cv_has_bike=' + ($("#mypostform #cv_has_bike").is(":checked") ? '1' : '0');
    if ($("#mypostform #cv_has_motorcycle").length>0) datasend+='&cv_has_motorcycle=' + ($("#mypostform #cv_has_motorcycle").is(":checked") ? '1' : '0');
    if ($("#mypostform #cv_has_car").length>0) datasend+='&cv_has_car=' + ($("#mypostform #cv_has_car").is(":checked") ? '1' : '0');
    if ($("#mypostform #cv_has_car_theseis").length>0) datasend+='&cv_has_car_theseis='  + encodeURI($("#mypostform #cv_has_car_theseis").val().trim());
    if ($("#mypostform #cv_metaforiko_meso").length>0) datasend+='&cv_metaforiko_meso='  + encodeURI($("#mypostform #cv_metaforiko_meso").val().trim());
    if ($("#mypostform #sistasi_from").length>0) datasend+='&sistasi_from='  + encodeURI($("#mypostform #sistasi_from").val().trim());

    if ($("#mypostform #days_to_work1").length>0) {
      datasend+='&days_to_work=' + 
      (($('#days_to_work1').is(':checked')) ? '1':'0') + 
      (($('#days_to_work2').is(':checked')) ? '1':'0') + 
      (($('#days_to_work3').is(':checked')) ? '1':'0') + 
      (($('#days_to_work4').is(':checked')) ? '1':'0') + 
      (($('#days_to_work5').is(':checked')) ? '1':'0') + 
      (($('#days_to_work6').is(':checked')) ? '1':'0') + 
      (($('#days_to_work0').is(':checked')) ? '1':'0');
    }
    if ($('#mypostform .input_item_upload_cv_check').length>0) {
      var iiucc='';
      $('.input_item_upload_cv_check').each(function() {
        iiucc+='&iiucc' + $(this).attr('id').replace('input_item_upload_cv_check_','') + '=';
        if ($(this).is(':checked')) iiucc+='1'; else iiucc+='0';
      });
      datasend+=iiucc;
      //console.log(iiucc);
    }
    if ($('#mypostform .input_item_upload_cv').length>0) {
      var iiucd='';
      $('.input_item_upload_cv').each(function() {
        iiucd+='&iiucd' + $(this).attr('id').replace('input_item_upload_cv_','') + '=';
        iiucd+=encodeURI($(this).val().trim());
      });
      datasend+=iiucd;
      //console.log(iiucd);
    }



    var user_roles='';
    $('.rolecheckbox').each(function() {
      if ($(this).is(':checked')) user_roles+='&' + $(this).attr('id') + '=1';
    });
    
    //console.log(user_roles);
    
    datasend+=user_roles;




    var form_newsletter_email=[];
    $('.newsletter-email').each(function( index ) {
      myval= (($(this).is(":checked")) ? '1' : '0');
      item = [$(this).attr('data-id'),myval];
      form_newsletter_email.push(item);
    }); 
    form_newsletter_email = JSON.stringify(form_newsletter_email);
    datasend+='&form_newsletter_email='  + encodeURI(form_newsletter_email);
    
    var form_newsletter_sms=[];
    $('.newsletter-sms').each(function( index ) {
      myval= (($(this).is(":checked")) ? '1' : '0');
      item = [$(this).attr('data-id'),myval];
      form_newsletter_sms.push(item);
    }); 
    form_newsletter_sms = JSON.stringify(form_newsletter_sms);
    datasend+='&form_newsletter_sms='  + encodeURI(form_newsletter_sms);
    
    datasend+='&form_user_photo='  + encodeURI($("#form_user_photo").val().trim());

    
    var communication=[];
    $('.gks_comm_email_div').each(function() {
      val=$(this).find('.gks_comm_email_value').val();
      descr = $(this).find('.gks_comm_email_descr').val();
      ispr = ($(this).find('.gks_comm_email_primary').hasClass('gks_comm_email_primary_sel') ? 1 : 0);
      communication.push({type:'email', value: val, descr: descr, ispr: ispr});
    });
    $('.gks_comm_phone_div').each(function() {
      val=$(this).find('.gks_comm_phone_value').val();
      descr = $(this).find('.gks_comm_phone_descr').val();
      ispr = ($(this).find('.gks_comm_phone_primary').hasClass('gks_comm_phone_primary_sel') ? 1 : 0);
      communication.push({type:'phone', value: val, descr: descr, ispr: ispr});
    });
    $('.gks_comm_url_div').each(function() {
      val=$(this).find('.gks_comm_url_value').val();
      descr = $(this).find('.gks_comm_url_descr').val();
      ispr = ($(this).find('.gks_comm_url_primary').hasClass('gks_comm_url_primary_sel') ? 1 : 0);
      communication.push({type:'url', value: val, descr: descr, ispr: ispr});
    });
    datasend+='&communication='  + encodeURIComponent($.base64.encode(JSON.stringify(communication)));

    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    datasend+=gks_custom_datasend();

    //console.log(communication);
    //console.log(encodeURIComponent(JSON.stringify(communication)));
    
    //return;
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-users-item-exec.php?id=' + from_php_id,
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
  
  $('#display_name').mousedown(function(event) {  
    
    
    var myfirst = $("#mypostform #myfirst_name").val().trim();
    var mylast = $("#mypostform #mylast_name").val().trim();
    
    myitem=myfirst;
    if (myitem!='') {
      var myfound=false;
      $('#display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#display_name').append( new Option(myitem) );
      }
    }
    
    myitem=mylast;
    if (myitem!='') {
      var myfound=false;
      $('#display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#display_name').append( new Option(myitem) );
      }
    }
    
    myitem=(myfirst + ' ' + mylast).trim();
    if (myitem!='') {
      var myfound=false;
      $('#display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#display_name').append( new Option(myitem) );
      }
    }
    
    myitem=(mylast + ' ' + myfirst).trim();
    if (myitem!='') {
      var myfound=false;
      $('#display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#display_name').append( new Option(myitem) );
      }
    }

    myitem = $("#mypostform #user_nicename").val().trim();
    if (myitem!='') {
      var myfound=false;
      $('#display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#display_name').append( new Option(myitem) );
      }
    }      
    
    
    
  });
  
 
  
  


    
  
  var ethnikotita_tags = [gks_lang('Ελληνική'), gks_lang('Ρωσική'), gks_lang('Βουλγαρική'), gks_lang('Αλβανική'), gks_lang('Βρετανική'), gks_lang('Τουρκική'),gks_lang('Σερβική'),gks_lang('Γαλλική'),gks_lang('Γερμανική'),gks_lang('Ιταλική')];
  $('#ethnikotita').tagit({allowSpaces: true, availableTags: ethnikotita_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var alli_apasxolisi_tags = [gks_lang('Ιδιωτικός Υπάλληλος'), gks_lang('Δημόσιος Υπάλληλος'), gks_lang('Μερική απασχόληση'), gks_lang('Ελεύθερος Επαγγελματίας'), gks_lang('Άνεργος'), gks_lang('Άνεργος και κάτοχος κάρτας ανεργίας')];
  $('#alli_apasxolisi').tagit({allowSpaces: true, availableTags: alli_apasxolisi_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var cv_glosses_tags = [gks_lang('Ελληνικά'), gks_lang('Αγγλικά'), gks_lang('Γαλλικά'), gks_lang('Ιταλικά'), gks_lang('Γερμανικά'), gks_lang('Σερβικά'), gks_lang('Αλβανικά'), gks_lang('Ρωσικά'), gks_lang('Βουλγαρικά'), gks_lang('Τουρκικά')];
  $('#cv_mitriki_glossa').tagit({allowSpaces: true, availableTags: cv_glosses_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  $('#cv_jenes_glosses').tagit({allowSpaces: true, availableTags: cv_glosses_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  
  
  var cv_sxesi_me_photografia_tags = [gks_lang('Καμία'), gks_lang('Ερασιτεχνική'), gks_lang('Σπουδές σε ΙΕΚ'), gks_lang('Σπουδές σε ΤΕΙ'), gks_lang('Σπουδές σε ΑΕΙ')];
  $('#cv_sxesi_me_photografia').tagit({allowSpaces: true, availableTags: cv_sxesi_me_photografia_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});


  var cv_metaforiko_meso_tags = [gks_lang('Πεζός'), gks_lang('Πεζή'), gks_lang('MMM'), gks_lang('ΤΑΧΙ'), gks_lang('Ποδήλατο'), gks_lang('Μηχανή'), gks_lang('Αυτοκίνητο')];
  $('#cv_metaforiko_meso').tagit({allowSpaces: true, availableTags: cv_metaforiko_meso_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  
   
  
  
  $('#doy').autocomplete({
    source: "doy-autocomplete.php",
    minLength: 1,
    autoFocus: true,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $("#doy").val(ui.item.value);
    },
  });
    
    
  $('#group').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-usersgroups.php',
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
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },
    select: function( event, ui ) {
      $("#group_id").val(ui.item.id);
      datasend='&group_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#group").val("");
          $("#group_id").val("");
        }
    }
  });
  
  
  $('#add_group').click(function(event) {  
    
    datasend='';
    datasend+='user_id='+from_php_id;    
    datasend+='&id='  + encodeURI($("#group_id").val().trim());    
    //console.log(datasend);
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-usersgroups-item-user_add.php',
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



  $('#ipergastirio').autocomplete({
    source: "admin-autocomplete-ergastirio.php",
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#ipergastirio_id").val(ui.item.id);
      datasend='&ipergastirio_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ipergastirio").val("");
          $("#ipergastirio_id").val("");
        }
    }
  });
  
  
  $('#add_ipergastirio').click(function(event) {  
    
    datasend='';
    datasend+='ipethinosperioxis_id=' + from_php_id;    
    datasend+='&id='  + encodeURI($("#ipergastirio_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-ergastiria-item-ipethinosperioxis_add.php?andxm=1',
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
    
               
  $('#ipmagazi').autocomplete({
    source: "admin-autocomplete-magazi.php",
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#ipmagazi_id").val(ui.item.id);
      datasend='&ipmagazi_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ipmagazi").val("");
          $("#ipmagazi_id").val("");
        }
    }
  });
  
  
  $('#add_ipmagazi').click(function(event) {  
    
    datasend='';
    datasend+='ipethinosperioxis_id=' + from_php_id;    
    datasend+='&id='  + encodeURI($("#ipmagazi_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-magazia-item-ipethinosperioxis_add.php',
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
    

  $('#ommagazi').autocomplete({
    source: "admin-autocomplete-magazi.php",
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#ommagazi_id").val(ui.item.id);
      datasend='&ommagazi_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ommagazi").val("");
          $("#ommagazi_id").val("");
        }
    }
  });
  
  
  $('#add_ommagazi').click(function(event) {  
    
    datasend='';
    datasend+='omadarxis_id=' + from_php_id;    
    datasend+='&id='  + encodeURI($("#ommagazi_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-magazia-item-omadarxis_add.php',
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
  
  $('#company_user').autocomplete({
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
    autoFocus: true,
    delay: 300, //default
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#company_user_id").val(ui.item.id);
      datasend='&company_user_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#company_user").val("");
          $("#company_user_id").val("");
        }
    }
  });
  
  
  $('#add_company_user').click(function(event) {  
    
    datasend='';
    datasend+='user_id=' + from_php_id;    
    datasend+='&company_id='  + encodeURI($("#company_user_id").val().trim());    
    datasend+='&date_hire='  + encodeURI($("#date_hire").val().trim());    
    datasend+='&sxolio='  + encodeURIComponent($.base64.encode($("#companyusersxolio").val().trim()));
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-company-item-user_add.php',
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
       
       
  $('.myaddergasipeth').click(function(event) {  
    var data_ergasid=$(this).attr('data_ergasid');
    $("#exit_date").hide();
    myconfirm(gks_lang('Σίγουρα θέλετε ορίσετε τον τρέχον χρήστη ως Υπεύθυνος Περιοχής για όλα τα μαγαζιά αυτού του εργαστηρίου;'),'myaddergasipeth',from_php_id,data_ergasid);
  });
  
  

  
  
  var protypdays_array =JSON.parse(from_php_json_encode_protypdays_array);
  //console.log(protypdays_array);
  
  var protypdays_campanys_array=JSON.parse(from_php_json_encode_protypdays_campanys_array);
  //console.log(protypdays_campanys_array);
  var protypdays_campanys_selected=0;
  
  dialog_protypdays = $( "#dialog_protypdays" ).dialog({
    autoOpen: false,
    width: 600,
    height: 600,
    modal: true,
    buttons: [
      {
        html: '<i class="fas fa-check-circle"></i> '+ gks_lang('OK'),
        //icon: "ui-icon-circle-plus",
        click: function() {

          var protypdays_send='';
          $('.cell_protypdays').each(function( index ) {
            curval=parseInt($(this).attr('data-company'));
            
            if (curval>0) {
              protypdays_send+=curval + '|' + $(this).attr('id')+ ',';
            }
          });	  
   
          
          //return;
          
        
          datasend='&id=' + from_php_id;
          datasend+='&protypdays='  + encodeURI(protypdays_send);
          
          //console.log(datasend);
  
          
          $('body').addClass("myloading");  
          $.ajax({
      			url: '/my/admin-users-protypdays-change.php',
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
      					if (data.success == true) {
      					  $("body").removeClass("myloading");
                  
                  $('#span_protypdays_descr').html($.base64.decode(data.descr));
                  $('#span_protypdays_descr [data-toggle="tooltip"]').bootstrapTltip();
      					  protypdays_array =JSON.parse($.base64.decode(data.array));
      					  protypdays_campanys_array=JSON.parse($.base64.decode(data.protypdays_campanys_array));
      					  
      					  
      					  //console.log(protypdays_array);
      					  //console.log(protypdays_campanys_array);
      					  is_protypdays_ok();
        					//myalert('ok:' + $.base64.decode(data.message));
        					dialog_protypdays.dialog('close');
      					} else {
      					  $("body").removeClass("myloading");
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      		});

        },
      },
      {
        html: "<i class='fa fa-window-close'></i> " + gks_lang('Άκυρο'), 
        click: function() {
          $( this ).dialog( "close" );
        }
      },

    ]
  });  
  
  //console.log(protypdays_array);
  $("input[name='protypdays_company']").change(function() {
    protypdays_campanys_selected = this.value;
    //console.log(protypdays_campanys_selected);
        
  });
  
  
  $('#span_protypdays_change').click(function(event) {
    $("input[name='protypdays_company']").each(function( index ) {
      $(this).prop("checked", false);
    });	  
    
    protypdays_campanys_selected=0;
    protypdays_campanys_array_keys = Object.keys(protypdays_campanys_array);
    if (protypdays_campanys_array_keys.length==0)  {
      myalert('error:'+gks_lang('Δεν έχετε ορίσει κάποια εταιρεία'));
      return; 
    }
    if (protypdays_campanys_array_keys.length==1)  {
      //console.log(protypdays_campanys_array);
      //console.log(protypdays_campanys_array_keys[0]);
      protypdays_campanys_selected=protypdays_campanys_array_keys[0];
      $("#protypdays_company_" + protypdays_campanys_selected).prop("checked", true);
    }
        
    $('.cell_protypdays').each(function( index ) {
      $(this).attr('class','cell_protypdays');
      $(this).attr('data-company','0');
      
    });	  
    //pd_1_2
    for (i = 0; i < protypdays_array.length; i++) {
      myid='#pd_' + protypdays_array[i][0] + '_' + protypdays_array[i][1];
      $(myid).attr('data-company',protypdays_array[i][2]);
      $(myid).css('background-color', protypdays_array[i][4]);
    } 
	  dwidth=$(window).width() * 0.96;
	  dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 600) dheight=600;	  
	  
	  dialog_protypdays.dialog('option', 'width', dwidth);
	  dialog_protypdays.dialog('option', 'height', dheight);
	  $('#dialog_protypdays').parent().css({position:'fixed'});      
    dialog_protypdays.dialog('open');
             
  });
  
  $('.cell_protypdays').click(function() {
    if (protypdays_campanys_selected == 0) {
      myalert('error:'+gks_lang('Επιλέγτε πρώτα μια εταιρεία'));
      return; 
    }
    curval=parseInt($(this).attr('data-company'));
    if (curval==protypdays_campanys_selected) {
      $(this).attr('data-company','0');
      $(this).css('background-color', '');
    } else {
      $(this).attr('data-company',protypdays_campanys_selected);
      $(this).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
    }
  });  
  
  $('.cell_protypdays_th').click(function() {
    if (protypdays_campanys_selected == 0) {
      myalert('error:'+gks_lang('Επιλέγτε πρώτα μια εταιρεία'));
      return; 
    }    
    vvv= $(this).attr('data-id');
    cc = 0;
    if (parseInt($('#pd_1_' + vvv).attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_2_' + vvv).attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_3_' + vvv).attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_4_' + vvv).attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_5_' + vvv).attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (cc == 5) {
      $('#pd_1_' + vvv).attr('data-company','0'); $('#pd_1_' + vvv).css('background-color', '');
      $('#pd_2_' + vvv).attr('data-company','0'); $('#pd_2_' + vvv).css('background-color', '');
      $('#pd_3_' + vvv).attr('data-company','0'); $('#pd_3_' + vvv).css('background-color', '');
      $('#pd_4_' + vvv).attr('data-company','0'); $('#pd_4_' + vvv).css('background-color', '');
      $('#pd_5_' + vvv).attr('data-company','0'); $('#pd_5_' + vvv).css('background-color', '');
      
      
    } else {
      $('#pd_1_' + vvv).attr('data-company',protypdays_campanys_selected); $('#pd_1_' + vvv).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_2_' + vvv).attr('data-company',protypdays_campanys_selected); $('#pd_2_' + vvv).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_3_' + vvv).attr('data-company',protypdays_campanys_selected); $('#pd_3_' + vvv).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_4_' + vvv).attr('data-company',protypdays_campanys_selected); $('#pd_4_' + vvv).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_5_' + vvv).attr('data-company',protypdays_campanys_selected); $('#pd_5_' + vvv).css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
    }      
  }); 
  $('.cell_protypdays_tr').click(function() {
    if (protypdays_campanys_selected == 0) {
      myalert('error:'+gks_lang('Επιλέγτε πρώτα μια εταιρεία'));
      return; 
    }    
    vvv= $(this).attr('data-id');
    cc = 0;

    
    if (parseInt($('#pd_' + vvv + '_1').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_2').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_3').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_4').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_5').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_6').attr('data-company')) == protypdays_campanys_selected)  cc++;
    if (parseInt($('#pd_' + vvv + '_7').attr('data-company')) == protypdays_campanys_selected)  cc++;
    
    if (cc == 7) {
      $('#pd_' + vvv + '_1').attr('data-company','0'); $('#pd_' + vvv + '_1').css('background-color', '');
      $('#pd_' + vvv + '_2').attr('data-company','0'); $('#pd_' + vvv + '_2').css('background-color', '');
      $('#pd_' + vvv + '_3').attr('data-company','0'); $('#pd_' + vvv + '_3').css('background-color', '');
      $('#pd_' + vvv + '_4').attr('data-company','0'); $('#pd_' + vvv + '_4').css('background-color', '');
      $('#pd_' + vvv + '_5').attr('data-company','0'); $('#pd_' + vvv + '_5').css('background-color', '');
      $('#pd_' + vvv + '_6').attr('data-company','0'); $('#pd_' + vvv + '_6').css('background-color', '');
      $('#pd_' + vvv + '_7').attr('data-company','0'); $('#pd_' + vvv + '_7').css('background-color', '');
    } else {
      
      $('#pd_' + vvv + '_1').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_1').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_2').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_2').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_3').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_3').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_4').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_4').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_5').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_5').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_6').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_6').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
      $('#pd_' + vvv + '_7').attr('data-company',protypdays_campanys_selected); $('#pd_' + vvv + '_7').css('background-color', protypdays_campanys_array[protypdays_campanys_selected][1]);
    }      
  }); 


  
 
 
  var timer_protypdays_error; 
  
  function timer_protypdays_error_func() {
    if ($('#protypdays_errorsub').css('visibility') != 'hidden') {
      $('#protypdays_errorsub').css('visibility', 'hidden');
    } else {
      $('#protypdays_errorsub').css('visibility', 'visible');
    }
    
  }
  function is_protypdays_ok() {
    isok=true;
    
    protypdays_campanys_array_keys = Object.keys(protypdays_campanys_array);
    
    //console.log('protypdays_array');
    //console.log(protypdays_array.length);
    //console.log(protypdays_array);
    //console.log('protypdays_campanys_array');
    //console.log(protypdays_campanys_array_keys.length);
    //console.log(protypdays_campanys_array);
    //console.log(protypdays_campanys_array_keys);
    
    //protypdays_array
    //protypdays_campanys_array
    if (protypdays_campanys_array_keys.length == 0 && protypdays_array.length > 0) {
      isok=false;
    }
    if (protypdays_campanys_array_keys.length > 0 && protypdays_array.length == 0) {
      isok=false;
    }
    
    if (isok) {
      for (i=0; i<protypdays_array.length;i++) {
        if (protypdays_array[i][2] == 0) {
          isok=false;
          break;
        }
      }
    }
    if (isok) {
      for (i=0; i<protypdays_array.length;i++) {
        if (protypdays_campanys_array_keys.includes(protypdays_array[i][2] + '')== false){
          isok=false;
          break;
        }
      }
    }
    if (isok) {
      for (c=0; c<protypdays_campanys_array_keys.length;c++) {
        iscok=false;
        for (i=0; i<protypdays_array.length;i++) {
          if (protypdays_array[i][2] + '' == protypdays_campanys_array_keys[c]) {
             iscok = true; 
          }
        }
        if (iscok == false){
          isok=false;
          break;
        }
      }
    }
    
    
    window.clearTimeout(timer_protypdays_error);
    if (isok) {
      $('#protypdays_error').hide();
    } else {
      $('#protypdays_error').show();
      timer_protypdays_error = setInterval(timer_protypdays_error_func, 300);
    }
  }
  
  is_protypdays_ok();




  jqXHR = $('#mycv_upload').fileupload({
      dropZone:$('#f_button_add_files_cv'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          if (from_php_id<=0) {
            myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την επαφή'));
            return;
          }
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          var acceptFileTypes = ['.pdf','.zip','.rar','.txt','.doc','.docx','.docm','.wps','.htm','.html','.odt','.sxw','.rtf'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("\n"));
          } else {
        
            file_cv_cc++;
            data.mycc=file_cv_cc;

            data.submit();
            $('#progress-bar_cv').show();
            $('#progress-extended_cv').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              myhtmlimg='<span id="item_upload_cv_' + file.insert_id + '">';
              
              myhtmlimg+='<input type="checkbox" class="input_item_upload_cv_check"';
              myhtmlimg+=' name="input_item_upload_cv_check_' + file.insert_id + '"';
              myhtmlimg+=' id="input_item_upload_cv_check_' + file.insert_id + '"';
              myhtmlimg+=' title="'+gks_lang('Ορατό στο προφίλ του χρήστη')+'"';
              
              myhtmlimg+='> <input type="text" class="input_item_upload_cv"'
              myhtmlimg+=' name="input_item_upload_cv_' + file.insert_id + '"'; 
              myhtmlimg+=' id="input_item_upload_cv_' + file.insert_id + '"';
              myhtmlimg+=' value=""';
              myhtmlimg+=' placeholder="'+gks_lang('Περιγραφή π.χ. Βιογραφικό')+'"';
              myhtmlimg+=' style="width: 180px;max-width: 100%;margin-top: 4px;"';
              myhtmlimg+=' title="'+gks_lang('Περιγραφή του αρχείου π.χ. Βιογραφικό, συστατική επιστολή, πτυχίο')+'" autocomplete="' + autocomplete_gks_disable + '"';

              
              
              myhtmlimg+='> <a href="' + file.url + '" target="_blank">' + file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)</a> <a href="" class="delete_upload_cv" data-id="' + file.insert_id + '"><img src="/my/img/0.png" border="0" width="16" style="position: relative;top: 3px;"></a><br></span>';
              $('#imagelist_cv').append(myhtmlimg);
              
              
              $('#item_upload_cv_' + file.insert_id + ' .delete_upload_cv').click(delete_upload_click_cv);
            } 
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-bar_cv .bar_cv').css(
            'width',
            progress + '%'
        );
        $('#progress-extended_cv').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε'));
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progressfile_cv' + data.mycc + ' .bar_cv').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#progress-bar_cv').hide();
        $('#progress-extended_cv').hide();
      },
      
  });  
  
	delete_upload_click_cv = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    $.ajax({
			url: '/my/profile-cv-delete.php?fromadmin=1&id=' + uid + '&user_id=' + from_php_id,
			myuid: uid,
			type: 'POST',
			cache: false,
			dataType: 'json',
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
  					$('#item_upload_cv_' + this.myuid).remove();
  					$('#myfileid_cv_' + this.myuid).remove();
					  js_profilepososto_user = data.profilepososto_user;
					  js_profilepososto_job = data.profilepososto_job;

					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_cv').click(delete_upload_click_cv);
  

  
  
  var elems_email = Array.prototype.slice.call(document.querySelectorAll('.newsletter-email'));
  elems_email.forEach(function(html) {
    var switchery1 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  
  var elems_sms = Array.prototype.slice.call(document.querySelectorAll('.newsletter-sms'));
  elems_sms.forEach(function(html) {
    var switchery2 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });   
  var elems_roles = Array.prototype.slice.call(document.querySelectorAll('.rolecheckbox'));
  elems_roles.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });   
  
  
  
    
  $('#ma_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('ma_nomos_id',v,0);
  });  


  var dialog_gsis;
  var dialog_gsis_result=false;
  dialog_gsis = $( "#dialog_gsis" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_gsis_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Επαφής'),
        //icon: "ui-icon-circle-plus",
        click: function() {

          if (dialog_gsis_result.basic_rec.i_ni_flag_descr =='ΦΠ') {
            onomasia_parts = dialog_gsis_result.basic_rec.onomasia.split(' ');
            if (onomasia_parts.length>=2) {
              if ($('#myfirst_name').val()=='') $('#myfirst_name').val(onomasia_parts[1].trim());
              if ($('#mylast_name').val()=='')  $('#mylast_name').val(onomasia_parts[0].trim());
            }
          } 
          if ($('#user_nicename').val()=='' || $('#user_nicename').val().startsWith(gks_lang('Επαφή'))) {
            $('#user_nicename').val(dialog_gsis_result.basic_rec.onomasia);
            $('#display_name').append( new Option(dialog_gsis_result.basic_rec.onomasia) );
            $('#display_name').val(dialog_gsis_result.basic_rec.onomasia);
          }
          $('#gks_lang').val('el-GR');
          

  				$('#eponimia').val(dialog_gsis_result.basic_rec.onomasia);
  				$('#title').val(dialog_gsis_result.basic_rec.commer_title);
  				$('#afm').val(dialog_gsis_result.basic_rec.afm);
  				$('#doy').val(dialog_gsis_result.basic_rec.doy_descr);
          //$('#dr_user_epaggelma').val('');
          for (i=0;i < dialog_gsis_result.firm_act_tab.length; i++) {
            if (dialog_gsis_result.firm_act_tab[i].kind=='1') {
              if ($('#epaggelma').val()=='') $('#epaggelma').val(dialog_gsis_result.firm_act_tab[i].cdescr);
              break;
            }
          }
  				mynymber=dialog_gsis_result.basic_rec.postal_address_no.trim();
  				if (mynymber=='0') mynymber='';
  				if ($('#ma_branch').val()=='') $('#ma_branch').val('0');
  				if ($('#ma_odos').val()=='') $('#ma_odos').val((dialog_gsis_result.basic_rec.postal_address).trim());
          if ($('#ma_arithmos').val()=='') $('#ma_arithmos').val(mynymber);
          //$('#dr_user_ma_perioxi').val('');
  				if ($('#ma_poli').val()=='') $('#ma_poli').val(dialog_gsis_result.basic_rec.postal_area_description);
  				if ($('#ma_tk').val()=='') $('#ma_tk').val(dialog_gsis_result.basic_rec.postal_zip_code);
          if ($('#ma_country_id').val()!='91') {
            $('#ma_country_id').val(91);
            $('#ma_nomos_id').val('0');
            nomos_fill('ma_nomos_id',91,0);
          }
          

          if (dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
            if ($('#fiscal_position_id').val()=='0' || $('#fiscal_position_id').val()=='1') $('#fiscal_position_id').val(11);
            $('#pricelist_id').val(2);
          } else {
            if ($('#fiscal_position_id').val()=='0') $('#fiscal_position_id').val(1);
            $('#pricelist_id').val(1);
          }
          
          if (mycus_sup_from_hash=='sup') {
            if ($('#role_promitheutis').is(':checked') == false) $('#role_promitheutis').click();
          } else {
            if ($('#role_customer').is(':checked') == false) $('#role_customer').click();
          }
          if ($('#role_subscriber').is(':checked')) $('#role_subscriber').click();
          
          gks_myscroll();
          
          $( this ).dialog( "close" );
          needsave=true;
        }
        //showText: false
      },
      {
        id: "dialog_gsis_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'), 
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
        

  });  
  
  
  function btn_gsis_get_click() {
    $('#dialog_gsis_afm').val($('#afm').val());
    $('#dialog_gsis_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_gsis.dialog('option', 'width', dwidth);
	  dialog_gsis.dialog('option', 'height', dheight);
	  $('#dialog_gsis').parent().css({position:'fixed'});      
    dialog_gsis.dialog('open');    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    if (myafm_from_hash!='') {
      $('#dialog_gsis_afm').val(myafm_from_hash);
      myafm_from_hash=''; 
      dialog_gsis_run_click();
    }
    
  }    
  $('#btn_gsis_get').click(btn_gsis_get_click);
 
  
  function dialog_gsis_run_click() {
    //console.log('dialog_gsis_run');
    dialog_gsis_result=false;
    
    dialog_gsis_afm=$('#dialog_gsis_afm').val().trim();
    if (dialog_gsis_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_gsis_ok').button( "option", "disabled", true);
    $('#dialog_gsis_html').html('');
    
    company_id=0;
    company_sub_id=0;
    v=$('#company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
        
    datasend='afm=' + dialog_gsis_afm + '&company_id=' + company_id + '&force=1';
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-get-gisis.php',
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
				//console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  dialog_gsis_result=data.out;
					  //console.log(dialog_gsis_result);
					  //console.log(dialog_gsis_result.user_id);
					  
					  
					  outhtml='';
					  if (dialog_gsis_result.user_id>0) {
					    outhtml+='<div class="alert alert-danger" role="alert">';  
					    outhtml+=gks_lang('ΠΡΟΣΟΧΗ: Υπάρχει ήδη επαφή με αυτό το ΑΦΜ με όνομα')+' '+ dialog_gsis_result.gks_nickname + '<br>';  
					    outhtml+='<a href="admin-users-item.php?id=' + dialog_gsis_result.user_id + '" target="_blank" class="gks_link">'+gks_lang('Προβολή της επαφής')+'</a>';  
					    outhtml+='</div>';  
					  }
					  
					  
					  
  					outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
  					if (dialog_gsis_result.valid==1) { //true
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:green;color:white;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else if (dialog_gsis_result.valid==2) { //wait
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:yellow;color:yellow;">' + dialog_gsis_result.basic_rec.firm_flag_descr + '</div>';
  				  } else {
  				    outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:red;color:white;">' + dialog_gsis_result.error_text + '</div>';
  				  }
  					
  					
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Πεδίο')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Τιμή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΑΦΜ')+':</td><td>' + data.out.basic_rec.afm + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ ID')+':</td><td>' + data.out.basic_rec.doy + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΔΟΥ')+':</td><td>' + data.out.basic_rec.doy_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο')+':</td><td>' + data.out.basic_rec.i_ni_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ο Α.Φ.Μ. είναι ενεργός ή απενεργοποιημένος')+':</td><td>' + data.out.basic_rec.deactivation_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επιτηδευματίας, μη επιτηδευματίας ή πρώην επιτηδευματίας')+':</td><td>' + data.out.basic_rec.firm_flag_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Επωνυμία Επιχείρησης')+':</td><td>' + data.out.basic_rec.onomasia + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Τίτλος Επιχείρησης')+':</td><td>' + data.out.basic_rec.commer_title + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιγραφή μορφής Νομικού Προσώπου / Νομικής Οντότητας')+':</td><td>' + data.out.basic_rec.legal_status_descr + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Διεύθυνση Έδρας Επιχείρησης')+':</td><td>' + data.out.basic_rec.postal_address + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Αριθμός')+':</td><td>' + data.out.basic_rec.postal_address_no + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΤΚ')+':</td><td nowrap>' + data.out.basic_rec.postal_zip_code + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Περιοχή')+':</td><td>' + data.out.basic_rec.postal_area_description + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Έναρξης')+':</td><td>' + data.out.basic_rec.regist_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ημερομηνία Διακοπής')+':</td><td>' + data.out.basic_rec.stop_date + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α')+':</td><td>' + data.out.basic_rec.normal_vat_system_flag + '</td></tr>';
  					outhtml+='</tbody></table>';
  					
            outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Δραστηριότητες Επιχείρησης')+'</p>';
              
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:30%">'+gks_lang('Τύπος')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:20%">'+gks_lang('Κωδικός')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Περιγραφή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					for (i=0;i < data.out.firm_act_tab.length; i++) {
  					  outhtml+='<tr><td scope="row" style="text-align: center !important;">' + data.out.firm_act_tab[i].kdescr + '</td><td style="text-align: center !important;">' + data.out.firm_act_tab[i].code + '</td><td>' + data.out.firm_act_tab[i].cdescr+ '</td></tr>';
  					}
  					outhtml+='</tbody></table>';
  					$('#dialog_gsis_html').html(outhtml);
  					$('#dialog_gsis_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }
  
  $('#dialog_gsis_run').click(dialog_gsis_run_click);


  var dialog_vies;
  var dialog_vies_result=false;
  dialog_vies = $( "#dialog_vies" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_vies_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Επαφής'),
        //icon: "ui-icon-circle-plus",
        click: function() {

          //console.log(dialog_vies_result);
          
          
          if ($('#user_nicename').val()=='' || $('#user_nicename').val().startsWith(gks_lang('Επαφή')) && dialog_vies_result.traderName!='') {
            $('#user_nicename').val(dialog_vies_result.traderName);
            $('#display_name').append( new Option(dialog_vies_result.traderName) );
            $('#display_name').val(dialog_vies_result.traderName);
          }
          $('#gks_lang').val('en-US');
          
      
          if (dialog_vies_result.traderName!='') {
  				  $('#eponimia').val(dialog_vies_result.traderName);
  				  $('#title').val(dialog_vies_result.traderName);
  			  }
  				$('#afm').val(dialog_vies_result.vatNumber);

          if (dialog_vies_result.traderAddress!='') {
            if ($('#ma_odos').val()=='') $('#ma_odos').val(dialog_vies_result.traderAddress);
          }
          $('#ma_country_id').val(dialog_vies_result.id_country);
          nomos_fill('ma_nomos_id',dialog_vies_result.id_country,0);
          
          if (dialog_vies_result.valid) {
            if ($('#fiscal_position_id').val()=='0' || $('#fiscal_position_id').val()=='1') $('#fiscal_position_id').val(41);
            $('#pricelist_id').val(2);
          } else {
            if ($('#fiscal_position_id').val()=='0' || $('#fiscal_position_id').val()=='1') $('#fiscal_position_id').val(2);
            $('#pricelist_id').val(1);
          }
          gks_myscroll();
          $( this ).dialog( "close" );
          needsave=true;
        }
        //showText: false
      },    
      {
        id: "dialog_vies_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'), 
        //icon: "ui-icon-cancel",
        click: function() {

            $( this ).dialog( "close" );

        }
        //showText: false
      },
    ]
        

  });
  
  function btn_vies_get_click() {
    
    
    if (from_php_perm_ret_add==false) return;
    var tmp_country_text=$('#ma_country_id option:selected').text();
    $('#dialog_vies_country_ee option').each(function() {
      if ($(this).text()==tmp_country_text) {
        $('#dialog_vies_country_ee').val($(this).val());
        return;
      }
    });
    
    $('#dialog_vies_afm').val($('#afm').val());
    $('#dialog_vies_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_vies.dialog('option', 'width', dwidth);
	  dialog_vies.dialog('option', 'height', dheight);
	  $('#dialog_vies').parent().css({position:'fixed'});      
    dialog_vies.dialog('open');    
    $('#dialog_vies_ok').button( "option", "disabled", true);
    if (myafm_from_hash!='') {
      $('#dialog_vies_country_ee').val(mycountry_ee_from_hash);
      
      $('#dialog_vies_afm').val(myafm_from_hash);
      myafm_from_hash=''; 
      dialog_vies_run_click();
    }
  }
  $('#btn_vies_get').click(btn_vies_get_click);
 
  
  function dialog_vies_run_click() {
    dialog_vies_result=false;
    
    if ($('#dialog_vies_country_ee').val().trim()=='') {
      myalert('error:'+gks_lang('Επιλέξτε πρώτα την χώρα'));
      return;  
    }
        
    dialog_vies_afm=$('#dialog_vies_afm').val().trim();
    if (dialog_vies_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_vies_ok').button( "option", "disabled", true);
    $('#dialog_vies_html').html('');
    

    country_ee=$('#dialog_vies_country_ee').val();
    if (country_ee === undefined || country_ee === null) country_ee='';
    
       
    datasend='afm=' + dialog_vies_afm + '&country_ee=' + country_ee + '&force=1';
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-get-vies.php',
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
				//console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  dialog_vies_result=data.out;
					  //console.log(dialog_vies_result);
					  //console.log(dialog_vies_result.user_id);
					  
					  
					  outhtml='';
					  if (dialog_vies_result.user_id>0) {
					    outhtml+='<div class="alert alert-danger" role="alert">';  
					    outhtml+=gks_lang('ΠΡΟΣΟΧΗ: Υπάρχει ήδη επαφή με αυτό το ΑΦΜ με όνομα')+' '+dialog_vies_result.gks_nickname + '<br>';  
					    outhtml+='<a href="admin-users-item.php?id=' + dialog_vies_result.user_id + '" target="_blank" class="gks_link">'+gks_lang('Προβολή της επαφής')+'</a>';  
					    outhtml+='</div>';  
					  }
					  
					  
					  
  					outhtml+='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
  					if (dialog_vies_result.valid) {
  					  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:green;color:white;">'+gks_lang('Έγκυρο')+'</div>';
  				  } else { //if (dialog_vies_result.basic_rec.normal_vat_system_flag=='N') {
  				    outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:red;color:white;">'+gks_lang('Μη Έγκυρο')+'</div>';
  				  //} else {
  				  //  outhtml+='<div style="width:100%;text-align:center;padding:10px;border-radius:10px;margin-bottom:10px;background-color:yellow;color:black;">'+gks_lang('Κανονικό Καθεστώς Φ.Π.Α.: Άγνωστο')+'</div>';
  				  }
  					
  					
  					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
  					outhtml+='<thead><tr>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Πεδίο')+'</th>';
  					outhtml+='<th class="table-dark" scope="col" style="text-align: center !important;width:50%">'+gks_lang('Τιμή')+'</th>';
  					outhtml+='</tr></thead><tbody>';
  					
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('ΑΦΜ')+':</td><td>' + data.out.vatNumber + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Χώρα')+':</td><td>' + data.out.countryCode + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Όνομα Επιχείρησης')+':</td><td>' + data.out.traderName + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Διεύθυνση')+':</td><td>' + data.out.traderAddress + '</td></tr>';
  					outhtml+='<tr><td scope="row" style="text-align:right;">'+gks_lang('Τύπος')+':</td><td>' + data.out.traderCompanyType + '</td></tr>';

  					outhtml+='</tbody></table>';

  					$('#dialog_vies_html').html(outhtml);
  					$('#dialog_vies_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  
  $('#dialog_vies_run').click(dialog_vies_run_click);  
  


  var myafm_from_hash='';
  var mycountry_ee_from_hash='';
  var mycus_sup_from_hash='';
  myhash=window.location.hash;
  if (myhash.length>=4 && myhash.startsWith('#createfromafm=')) {
    myafm=myhash.substring(15);
    if (myafm.length>=9) {
      parts=myafm.split('|');
      myafm=parts[0];
      myafm_from_hash=myafm;
      mycus_sup_from_hash='';if (parts.length==2) mycus_sup_from_hash=parts[1];
      //console.log(myafm_from_hash);
      //console.log(mycus_sup_from_hash);
      $('#btn_gsis_get').click();
    }
  } else if (myhash.length>=4 && myhash.startsWith('#createfromvies=')) {
    myafm=myhash.substring(16);
    if (myafm.length>=5) {
      parts=myafm.split('|');
      if (parts.length==2) {
        mycountry_ee_from_hash=parts[0];
        myafm_from_hash=parts[1];
        //console.log(mycountry_ee_from_hash);
        //console.log(myafm_from_hash);
        $('#btn_vies_get').click();
      }
    }
  }
  



  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
      $('#map').parent().css('height','500px').css('margin-top','10px');
      showmap_run();
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map_pos, #geocode_pos').prop('disabled',false);
    } else {
      if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
        $('#map_pos, #geocode_pos').prop('disabled',true);
        $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
        $('#map').parent().hide();
      } else {
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map').parent().show();
      }
    }
    gks_myscroll();
  });

  

  
  $('#map_pos').click(function(event){
    if (infoWindow_userpos==null) infoWindow_userpos = new google.maps.InfoWindow({map: map});
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
  
        
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        map.setCenter(pos);
        
          
        marker.position=pos;
        place_map_latitude = marker.position.lat;
        place_map_longitude = marker.position.lng;
        infoWindow_userpos.open(map, marker);
        map.setZoom(17);
      
        //infoWindow_userpos.setPosition(pos);
          
        $('#ma_latitude').val(place_map_latitude);
        $('#ma_longitude').val(place_map_longitude);
        need_save=true;
        
      }, function() {
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });  
  
  $('#geocode_pos').tooltipster();
  $('#geocode_pos').click(function() {
    
    datasend='';
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#ma_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#ma_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#ma_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#ma_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#ma_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#ma_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#ma_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#ma_nomos_id").val().trim());
    
    $('#geocode_pos').prop('disabled',true);
    $('#geocode_pos_icon').html('<i class="fas fa-hourglass"></i>');
    //console.log(datasend);
    $.ajax({
			url: '/my/admin-get-geocode_pos.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#geocode_pos').prop('disabled',false);
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#ma_latitude' ).val(data.pos.lat);
					  $('#ma_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('GEO')+': ' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+$.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  });
  


  var emailTags = [
    gks_lang('Εργασίας'),
    gks_lang('Προσωπικό'),
    gks_lang('Άλλο'),
  ];
  var phoneTags = [
    gks_lang('Εργασία'),
    gks_lang('Κινητό'),
    gks_lang('Κινητό Εργασίας'),
    gks_lang('Κινητό Προσωπικό'),
    gks_lang('Σπίτι'),
    gks_lang('Φαξ'),
    gks_lang('Φαξ Σπιτιού'),
    gks_lang('Φαξ Εργασίας'),
    gks_lang('Σταθερό'),
    gks_lang('Σταθερό Σπιτιού'),
    gks_lang('Σταθερό Εργασίας'),
    gks_lang('Άλλο'),
  ];
  var urlTags = [
    gks_lang('Προσωπικό'),
    gks_lang('Εταιρικό'),
    gks_lang('Εταιρικό site'),
    gks_lang('Προφίλ'),
    gks_lang('Ιστολόγιο'),
    gks_lang('Άλλο'),
  ];  
  
  function gks_comm_email_primary_click() {
    $('.gks_comm_email_primary').tooltipster('destroy');
    $('.gks_comm_email_primary_sel').removeClass('gks_comm_email_primary_sel').attr('title',gks_lang('Ορισμός ως προεπιλογή'));
    $(this).addClass('gks_comm_email_primary_sel').attr('title',gks_lang('Προεπιλογή'));
    $('.gks_comm_email_primary').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
  }
  function gks_comm_email_delete_click() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    //console.log(aa);
    $('.gks_comm_email_div[data-aa=' + aa + ']').remove();
    gks_comm_email_icons();
    if ($('.gks_comm_email_primary_sel').length==0) $('.gks_comm_email_primary:first').addClass('gks_comm_email_primary_sel');
    need_save=true; 
  }
  function gks_comm_email_icons() {
    $('.gks_comm_email_add').hide();
    $('.gks_comm_email_add:last').show();
    if ($('.gks_comm_email_delete').length<=0) {
      gks_comm_email_add_click();
    }
  }
  function gks_comm_email_add_click() {
    aa=parseInt($('.gks_comm_email_div:last').attr('data-aa'));
    if (isNaN(aa)) aa=0;
    aa++;
    html='' +
    '<div class="row gks_comm_email_div" data-aa="' + aa + '">' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_email_value form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ. info@gks.gr')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' <i class="fas fa-envelope gks_comm_email_primary " data-aa="' + aa + '" title="'+gks_lang('Ορισμός ως προεπιλογή')+'"></i>' +
      '</div>' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_email_descr form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ. Εργασίας')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' <i class="fas fa-trash-alt gks_comm_email_delete" data-aa="' + aa + '"></i>' +
        ' <i class="fas fa-plus-circle gks_comm_email_add" data-aa="' + aa + '" style=""></i>' +
      '</div>' +
    '</div>';   
    
    $('#gks_comm_email_cont_div').append(html);
    $('.gks_comm_email_div[data-aa=' + aa + '] .gks_comm_email_primary').click(gks_comm_email_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
    $('.gks_comm_email_div[data-aa=' + aa + '] .gks_comm_email_delete').click(gks_comm_email_delete_click);
    $('.gks_comm_email_div[data-aa=' + aa + '] .gks_comm_email_add').click(gks_comm_email_add_click);
    $('.gks_comm_email_div[data-aa=' + aa + '] .gks_comm_email_descr').autocomplete({
      source: emailTags, 
      minLength:0,
      create: function(event, ui){
        $(this).attr('autocomplete',autocomplete_gks_disable);
      }
    }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
    $('.gks_comm_email_div[data-aa=' + aa + '] .myneedsave').on('input keyup paste', function() {
      need_save=true; 
    });
    gks_comm_email_icons();
    need_save=true; 
  }
  $('.gks_comm_email_primary').click(gks_comm_email_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});
  $('.gks_comm_email_delete').click(gks_comm_email_delete_click);
  $('.gks_comm_email_add').click(gks_comm_email_add_click);
  $('.gks_comm_email_descr').autocomplete({
    source: emailTags, 
    minLength:0,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    }
  }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
  


  function gks_comm_phone_primary_click() {
    $('.gks_comm_phone_primary').tooltipster('destroy');
    $('.gks_comm_phone_primary_sel').removeClass('gks_comm_phone_primary_sel').attr('title',gks_lang('Ορισμός ως προεπιλογή'));
    $(this).addClass('gks_comm_phone_primary_sel').attr('title',gks_lang('Προεπιλογή'));
    $('.gks_comm_phone_primary').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
  }
  function gks_comm_phone_delete_click() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    //console.log(aa);
    $('.gks_comm_phone_div[data-aa=' + aa + ']').remove();
    gks_comm_phone_icons();
    if ($('.gks_comm_phone_primary_sel').length==0) $('.gks_comm_phone_primary:first').addClass('gks_comm_phone_primary_sel');
    need_save=true; 
  }
  function gks_comm_phone_icons() {
    $('.gks_comm_phone_add').hide();
    $('.gks_comm_phone_add:last').show();
    if ($('.gks_comm_phone_delete').length<=0) {
      gks_comm_phone_add_click();
    }
  }
  function gks_comm_phone_add_click() {
    aa=parseInt($('.gks_comm_phone_div:last').attr('data-aa'));
    if (isNaN(aa)) aa=0;
    aa++;
    html='' +
    '<div class="row gks_comm_phone_div" data-aa="' + aa + '">' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_phone_value form-control form-control-sm myneedsave ' + from_php_gks_voip_params.class_input + '" value="" placeholder="'+gks_lang('π.χ. 6912345678')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' '+ from_php_gks_voip_params.html_after_input +
        ' <i class="fas fa-phone gks_comm_phone_primary " data-aa="' + aa + '" title="'+gks_lang('Ορισμός ως προεπιλογή')+'"></i>' +
      '</div>' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_phone_descr form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ. Κινητό')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' <i class="fas fa-trash-alt gks_comm_phone_delete" data-aa="' + aa + '"></i>' +
        ' <i class="fas fa-plus-circle gks_comm_phone_add" data-aa="' + aa + '" style=""></i>' +
      '</div>' +
    '</div>';   
    
    $('#gks_comm_phone_cont_div').append(html);
    $('.gks_comm_phone_div[data-aa=' + aa + '] .gks_voip_originate_after_input').click(gks_voip_originate_click);
    $('.gks_comm_phone_div[data-aa=' + aa + '] .gks_comm_phone_primary').click(gks_comm_phone_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
    $('.gks_comm_phone_div[data-aa=' + aa + '] .gks_comm_phone_delete').click(gks_comm_phone_delete_click);
    $('.gks_comm_phone_div[data-aa=' + aa + '] .gks_comm_phone_add').click(gks_comm_phone_add_click);
    $('.gks_comm_phone_div[data-aa=' + aa + '] .gks_comm_phone_descr').autocomplete({
      source: phoneTags, 
      minLength:0,
      create: function(event, ui){
        $(this).attr('autocomplete',autocomplete_gks_disable);
      }
    }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
    $('.gks_comm_phone_div[data-aa=' + aa + '] .myneedsave').on('input keyup paste', function() {
      need_save=true; 
    });
    
    
    gks_comm_phone_icons();
    need_save=true; 
  }
  $('.gks_comm_phone_primary').click(gks_comm_phone_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});
  $('.gks_comm_phone_delete').click(gks_comm_phone_delete_click);
  $('.gks_comm_phone_add').click(gks_comm_phone_add_click);
  $('.gks_comm_phone_descr').autocomplete({
    source: phoneTags, 
    minLength:0,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    }
  }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});

  

  function gks_comm_url_primary_click() {
    $('.gks_comm_url_primary').tooltipster('destroy');
    $('.gks_comm_url_primary_sel').removeClass('gks_comm_url_primary_sel').attr('title',gks_lang('Ορισμός ως προεπιλογή'));
    $(this).addClass('gks_comm_url_primary_sel').attr('title',gks_lang('Προεπιλογή'));
    $('.gks_comm_url_primary').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
  }
  function gks_comm_url_delete_click() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    //console.log(aa);
    $('.gks_comm_url_div[data-aa=' + aa + ']').remove();
    gks_comm_url_icons();
    if ($('.gks_comm_url_primary_sel').length==0) $('.gks_comm_url_primary:first').addClass('gks_comm_url_primary_sel');
    need_save=true; 
  }
  function gks_comm_url_icons() {
    $('.gks_comm_url_add').hide();
    $('.gks_comm_url_add:last').show();
    if ($('.gks_comm_url_delete').length<=0) {
      gks_comm_url_add_click();
    }
  }
  function gks_comm_url_add_click() {
    aa=parseInt($('.gks_comm_url_div:last').attr('data-aa'));
    if (isNaN(aa)) aa=0;
    aa++;
    html='' +
    '<div class="row gks_comm_url_div" data-aa="' + aa + '">' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_url_value form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ. www.gks.gr')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' <i class="fas fa-link gks_comm_url_primary " data-aa="' + aa + '" title="'+gks_lang('Ορισμός ως προεπιλογή')+'"></i>' +
      '</div>' +
      '<div class="col-md-6">' +
        '<input type="text" class="gks_comm_url_descr form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ. Εταιρικό site')+'" autocomplete="' + autocomplete_gks_disable + '">' +
        ' <i class="fas fa-trash-alt gks_comm_url_delete" data-aa="' + aa + '"></i>' +
        ' <i class="fas fa-plus-circle gks_comm_url_add" data-aa="' + aa + '" style=""></i>' +
      '</div>' +
    '</div>';   
    
    $('#gks_comm_url_cont_div').append(html);
    $('.gks_comm_url_div[data-aa=' + aa + '] .gks_comm_url_primary').click(gks_comm_url_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});;
    $('.gks_comm_url_div[data-aa=' + aa + '] .gks_comm_url_delete').click(gks_comm_url_delete_click);
    $('.gks_comm_url_div[data-aa=' + aa + '] .gks_comm_url_add').click(gks_comm_url_add_click);
    $('.gks_comm_url_div[data-aa=' + aa + '] .gks_comm_url_descr').autocomplete({
      source: urlTags, 
      minLength:0,
      create: function(event, ui){
        $(this).attr('autocomplete',autocomplete_gks_disable);
      }
    }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
    $('.gks_comm_url_div[data-aa=' + aa + '] .myneedsave').on('input keyup paste', function() {
      need_save=true; 
    });
    gks_comm_url_icons();
    need_save=true; 
  }
  $('.gks_comm_url_primary').click(gks_comm_url_primary_click).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});
  $('.gks_comm_url_delete').click(gks_comm_url_delete_click);
  $('.gks_comm_url_add').click(gks_comm_url_add_click);
  $('.gks_comm_url_descr').autocomplete({
    source: urlTags, 
    minLength:0,
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    }
  }).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});



  $('#viber_send_def_text').click(function() {
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την επαφή'));
      return;
    }
    if ($("#mypostform #viber_id").val().trim()=='') {
      myalert('error:'+gks_lang('Ορίστε το Viber ID'));
      return;
    }
      
    datasend='user_id=' + from_php_id; 
    datasend+='&viber_id='  + encodeURI($.base64.encode($("#mypostform #viber_id").val().trim()));
    //console.log(datasend);
    
    $('body').addClass("myloading");  
    $.ajax({
			url: '/my/admin-users-item-viber-test-send.php',
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
  					//window.location.reload();
  					myalert('ok:' + $.base64.decode(data.message));
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});    
  });  
  
  
  gks_address_autocomplete('ma_odos','ma_arithmos','ma_orofos','ma_perioxi','ma_poli','ma_tk','ma_nomos_id','ma_country_id','ma_latitude','ma_longitude',true);
  
  
  $('#gks_card_extra_address_btn').click(function() {
    
    $('html, body').animate({
      scrollTop: ($("#gks_card_extra_address").offset().top - 100)
    }, 2000);
  });

  $('#ma_latitude, #ma_longitude').on(mychange,function() {
    lat=parseFloat($('#ma_latitude').val());
    lng=parseFloat($('#ma_longitude').val());
    gks_this_map_set_pos(lat,lng);
  });

  
  $('#is_b2g').change(function() {
    if ($('#is_b2g').is(':checked')) {
      $('#div_is_b2g').show();
    } else {
      $('#div_is_b2g').hide();
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
  
    
});

var map;
var marker;
var place_map_latitude = from_php_map_latitude;
var place_map_longitude = from_php_map_longitude;
var myLatLng;
var infoWindow_userpos=null;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 17,
    mapId: "gks1234567890",
  });
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title: gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  });
}

function handleEvent_Marker(event) {
    document.getElementById('ma_latitude').value = event.latLng.lat();
    document.getElementById('ma_longitude').value = event.latLng.lng();
}
 
var map_is_open=false;
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
    //place_map_latitude  = 40.6444460;
    //place_map_longitude = 22.914514;
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };      
          place_map_latitude = position.coords.latitude;
          place_map_longitude = position.coords.longitude;
          myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
          marker.position=pos;
          map.setOptions({center: pos});
          map.setOptions({zoom: 17});
          
          jQuery('#ma_latitude').val(place_map_latitude);
          jQuery('#ma_longitude').val(place_map_longitude);
            
          need_save=true;
          
          //console.log('2' + myLatLng);
      }, function() {
        
      });
    } 
  }      

  myLatLng = {lat: place_map_latitude, lng: place_map_longitude};

  initMap();
  marker.addListener('drag', handleEvent_Marker);
  marker.addListener('dragend', handleEvent_Marker);
  map_is_open=true;
}

window.gks_this_map_set_pos = function(lat,lng) {
  place_map_latitude=lat;
  place_map_longitude=lng;
  
  myLatLng = {lat: lat, lng: lng};
  if (typeof marker != 'undefined') marker.position=myLatLng;
  if (typeof marker != 'undefined') map.setOptions({center: myLatLng});
  //map.setOptions({zoom: 17});
}
