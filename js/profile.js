/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var need_save=false;

jQuery(document).ready(function($) {
  
  var dialog_add_bank_account;
  var file_cc=0;
  var delete_upload_click_cv;
  var delete_upload_click_photo;
  var myagefunction; 
  var set_profile_photo;
  var myprofilepososto_oldval=0;



  function myagefunction() {
    var dvalhtml = $('#form_genisi_date').val();
    if (dvalhtml=='' || dvalhtml=='__/__/____')  {
      $('#span_calc_age').html(gks_lang('Συμπληρώστε την ημερομηνία γέννησης'));
    } else {
      var dval = $('#form_genisi_date').datetimepicker('getValue');
      if (dval == null) {
        $('#span_calc_age').html(gks_lang('Συμπληρώστε την ημερομηνία γέννησης'));
      } else {
        $('#span_calc_age').html(calc_age(dval));
      }
    }
  }
  $('#form_genisi_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y',timepicker:false,onChangeDateTime: 
    function(dp,$input) {      
      myagefunction();
      need_save=true;
    }
  }));
  myagefunction();
  
  
  
  


    
 
 
  dialog_add_bank_account = $( "#dialog_add_bank_account" ).dialog({
    autoOpen: false,
    width: 600,
    height: 650,
    modal: true,
    buttons: [
      {
        id: "dialog_add_bank_account_add",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Προσθήκη'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          dialog_add_bank_account_iban  =$("#dialog_add_bank_account_iban").val().trim();
          dialog_add_bank_account_bank_id  =$("#dialog_add_bank_account_bank_id").val().trim();
          dialog_add_bank_account_dikaiouxos  =$("#dialog_add_bank_account_dikaiouxos").val().trim();
          
          if (dialog_add_bank_account_iban == '') {
            myalert('error:'+gks_lang('Το ΙΒΑΝ δεν μπορεί να είναι κενό'));
            return;
          }
          if (dialog_add_bank_account_bank_id == 0) {
            myalert('error:'+gks_lang('Επιλέξτε κάποια τράπεζα'));
            return;
          }
          if (dialog_add_bank_account_dikaiouxos =='') {
            myalert('error:'+gks_lang('Εισάγετε το όνομα του δικαιούχου'));
            return;            
          }
          $("#dialog_add_bank_account_progressbar").show();
  
  
          myreload = false;
    		  $.ajax({
    				url: '/my/profile-bank-account-add.php',
    				type: 'POST',
    				cache: false,
    				dataType: 'json',
    				data: 'iban=' + encodeURI(dialog_add_bank_account_iban) + '&bank_id=' + encodeURI(dialog_add_bank_account_bank_id) + '&dikaiouxos=' + encodeURI(dialog_add_bank_account_dikaiouxos),
    				error : function(jqXHR ,textStatus,  errorThrown) {
    					$("#dialog_add_bank_account_progressbar").hide();
    					myalert('error:' + jqXHR.responseText);
    				},				
    				success: function(data) {
      				if (!data) {
      				  $("#dialog_add_bank_account_progressbar").hide();
      				  myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
                if (typeof data.myreload !== 'undefined') myreload=data.myreload;
        				if (data.success == false){
        				  $("#dialog_add_bank_account_progressbar").hide();
        					if (data.message.length > 0){
        						myalert('error:' + $.base64.decode(data.message));
        						return;
        					} else {
        					  myalert('error:'+gks_lang('Σφάλμα')+'<br>'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        					  return;
        					}
        				} else {
        				  $("#dialog_add_bank_account_progressbar").hide();
        				  $('#table_bank_accounts tr:last').after($.base64.decode(data.html));
        				  $("#table_bank_accounts tr:last .mybankaccountdelete").click(mybankaccountdelete);
        				          				  
        				  dialog_add_bank_account.dialog('close'); 
      					  from_php_js_profilepososto_user = data.profilepososto_user;
      					  from_php_js_profilepososto_job = data.profilepososto_job;
      					  change_myprofilepososto();
      					        				  
        				  return;
        				}
      				
  				
        			}
    				}
    				
    			});   			
		    }	
      },
      {
        id: "dialog_add_bank_account_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }			
      },      
    ],
    
    close: function() {
      //form[ 0 ].reset();
      //allFields.removeClass( "ui-state-error" );
    }
  });   









// new stuf


  $('#back_to_home').click(function(event){
    window.location.href='/my';
  });
  

  
    
  $('#form_ma_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('form_ma_nomos_id',v,0);
    need_save=true;
  });

  if (from_php_country_id==0) { 
    $('#form_ma_country_id').val(91);
    nomos_fill('form_ma_nomos_id',91,26);
  }   
  
  $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
  $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  
  $('#form_display_name').mousedown(function(event) {  
    
    
    var myfirst = $("#mypostform #form_first_name").val().trim();
    var mylast = $("#mypostform #form_last_name").val().trim();
    
    myitem=myfirst;
    if (myitem!='') {
      var myfound=false;
      $('#form_display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#form_display_name').append( new Option(myitem) );
      }
    }
    
    myitem=mylast;
    if (myitem!='') {
      var myfound=false;
      $('#form_display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#form_display_name').append( new Option(myitem) );
      }
    }
    
    myitem=(myfirst + ' ' + mylast).trim();
    if (myitem!='') {
      var myfound=false;
      $('#form_display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#form_display_name').append( new Option(myitem) );
      }
    }
    
    myitem=(mylast + ' ' + myfirst).trim();
    if (myitem!='') {
      var myfound=false;
      $('#form_display_name option').each(function() {
        if ($(this).text() == myitem) {
          myfound = true;
          return false;
        }
      });
      if (myfound==false) {
        $('#form_display_name').append( new Option(myitem) );
      }
    }
    
//    myitem = $("#mypostform #form_user_nicename").val().trim();
//    if (myitem!='') {
//      var myfound=false;
//      $('#form_display_name option').each(function() {
//        if ($(this).text() == myitem) {
//          myfound = true;
//          return false;
//        }
//      });
//      if (myfound==false) {
//        $('#form_display_name').append( new Option(myitem) );
//      }
//    }    
    
  });  




  $('#form_select_apostoli').change(function() {
    var v=$(this).val();
    extra_address_select(v)
  }); 
  
  function extra_address_select(v) {
    
    if (v ==-1) {
      $('#div_extra_address').hide();  
      return;
    } else {
      $('#div_extra_address').show();
    }
    
    if (v==0) {
      $('#form_ea_name').removeClass('mydisabled');
      $('#form_ea_phone').removeClass('mydisabled');
      $('#form_ea_odos').removeClass('mydisabled');
      $('#form_ea_arithmos').removeClass('mydisabled');
      $('#form_ea_orofos').removeClass('mydisabled');
      $('#form_ea_perioxi').removeClass('mydisabled');
      $('#form_ea_poli').removeClass('mydisabled');
      $('#form_ea_tk').removeClass('mydisabled');
      $('#form_ea_country_id').removeClass('mydisabled');
      $('#form_ea_nomos_id').removeClass('mydisabled');
      
      $('#form_ea_name').prop('readonly', false);
      $('#form_ea_phone').prop('readonly', false);
      $('#form_ea_odos').prop('readonly', false);
      $('#form_ea_arithmos').prop('readonly', false);
      $('#form_ea_orofos').prop('readonly', false);
      $('#form_ea_perioxi').prop('readonly', false);
      $('#form_ea_poli').prop('readonly', false);
      $('#form_ea_tk').prop('readonly', false);
      $('#form_ea_country_id').attr("disabled", false);
      $('#form_ea_nomos_id').attr("disabled", false);

      
      $('#form_ea_name').val('');
      $('#form_ea_phone').val('');
      $('#form_ea_odos').val('');
      $('#form_ea_arithmos').val('');
      $('#form_ea_orofos').val('');
      $('#form_ea_perioxi').val('');
      $('#form_ea_poli').val('');
      $('#form_ea_tk').val('');

      v1 = $('#form_ma_country_id').val();
      v2 = $('#form_ma_nomos_id').val();

      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
    } else {
      $('#form_ea_name').addClass('mydisabled');
      $('#form_ea_phone').addClass('mydisabled');
      $('#form_ea_odos').addClass('mydisabled');
      $('#form_ea_arithmos').addClass('mydisabled');
      $('#form_ea_orofos').addClass('mydisabled');
      $('#form_ea_perioxi').addClass('mydisabled');
      $('#form_ea_poli').addClass('mydisabled');
      $('#form_ea_tk').addClass('mydisabled');
      $('#form_ea_country_id').addClass('mydisabled');
      $('#form_ea_nomos_id').addClass('mydisabled');
      
      $('#form_ea_name').prop('readonly', true);
      $('#form_ea_phone').prop('readonly', true);
      $('#form_ea_odos').prop('readonly', true);
      $('#form_ea_arithmos').prop('readonly', true);
      $('#form_ea_orofos').prop('readonly', true);
      $('#form_ea_perioxi').prop('readonly', true);
      $('#form_ea_poli').prop('readonly', true);
      $('#form_ea_tk').prop('readonly', true);
      $('#form_ea_country_id').attr("disabled", true);
      $('#form_ea_nomos_id').attr("disabled", true);
      
      
      $('#form_ea_name').val(extra_address[v].ea_name);
      $('#form_ea_phone').val(extra_address[v].ea_phone);
      $('#form_ea_odos').val(extra_address[v].ea_odos);
      $('#form_ea_arithmos').val(extra_address[v].ea_arithmos);
      $('#form_ea_orofos').val(extra_address[v].ea_orofos);
      $('#form_ea_perioxi').val(extra_address[v].ea_perioxi);
      $('#form_ea_poli').val(extra_address[v].ea_poli);
      $('#form_ea_tk').val(extra_address[v].ea_tk);

      v1 = extra_address[v].ea_country_id;
      v2 = extra_address[v].ea_nomos_id;
      
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
    }
  }

  if (from_php_first_ea_j > 0) {
    extra_address_select(from_php_first_ea_j);
  }     

  $('#delete_extra_address').click(function(event){
    var v=$('#form_select_apostoli').val();
    $('#form_extra_address_delete').val($('#form_extra_address_delete').val() + v + ',');
    $("#form_select_apostoli option[value='" + v + "']").remove();
    
    v=$('#form_select_apostoli').val();
    if (v==null) {
      extra_address_select(0);
      $('#div_extra_address').hide(); 
    } else {
      extra_address_select(v);
    }
    need_save=true;       
  });


  $('#mysave').click(function(event){
    //$("body").addClass("myloading");
    //$("body").removeClass("myloading");
    
    datasend='';
    var js_show_job_fields = $('#show_job_fields_checkbox').is(":checked");
    //alert(js_show_job_fields);
    
    datasend+='&show_job_fields=' + ((js_show_job_fields) ? '1' : '0'); 
    datasend+='&form_first_name='  + encodeURI($("#mypostform #form_first_name").val().trim());
    datasend+='&form_last_name='  + encodeURI($("#mypostform #form_last_name").val().trim());
    //datasend+='&form_user_nicename='  + encodeURI($("#mypostform #form_user_nicename").val().trim());
    datasend+='&form_display_name='  + encodeURI($("#mypostform #form_display_name").val().trim());
    datasend+='&form_gks_sex='  + encodeURI($("#mypostform #form_gks_sex").val().trim());
    datasend+='&form_gks_lang='  + encodeURI($("#mypostform #form_gks_lang").val().trim());
    datasend+='&form_password_old='  + encodeURI($("#mypostform #form_password_old").val().trim());
    datasend+='&form_password_new1='  + encodeURI($("#mypostform #form_password_new1").val().trim());
    datasend+='&form_password_new2='  + encodeURI($("#mypostform #form_password_new2").val().trim());
    datasend+='&form_user_photo='  + encodeURI($("#mypostform #form_user_photo").val().trim());
    datasend+='&form_user_email='  + encodeURI($("#mypostform #form_user_email").val().trim());
    datasend+='&form_user_mobile='  + encodeURI($("#mypostform #form_user_mobile").val().trim());
    datasend+='&form_phone_home='  + encodeURI($("#mypostform #form_phone_home").val().trim());
    datasend+='&form_user_url='  + encodeURI($("#mypostform #form_user_url").val().trim());
    datasend+='&form_ma_odos='  + encodeURI($("#mypostform #form_ma_odos").val().trim());
    datasend+='&form_ma_arithmos='  + encodeURI($("#mypostform #form_ma_arithmos").val().trim());
    datasend+='&form_ma_orofos='  + encodeURI($("#mypostform #form_ma_orofos").val().trim());
    datasend+='&form_ma_perioxi='  + encodeURI($("#mypostform #form_ma_perioxi").val().trim());
    datasend+='&form_ma_poli='  + encodeURI($("#mypostform #form_ma_poli").val().trim());
    datasend+='&form_ma_tk='  + encodeURI($("#mypostform #form_ma_tk").val().trim());
    datasend+='&form_ma_country_id='  + encodeURI($("#mypostform #form_ma_country_id").val().trim());
    datasend+='&form_ma_nomos_id='  + encodeURI($("#mypostform #form_ma_nomos_id").val().trim());
    datasend+='&form_extra_address_delete='  + encodeURI($("#mypostform #form_extra_address_delete").val().trim());
    datasend+='&form_eponimia='  + encodeURI($("#mypostform #form_eponimia").val().trim());
    datasend+='&form_title='  + encodeURI($("#mypostform #form_title").val().trim());
    datasend+='&form_afm='  + encodeURI($("#mypostform #form_afm").val().trim());
    datasend+='&form_doy='  + encodeURI($("#mypostform #form_doy").val().trim());
    datasend+='&form_epaggelma='  + encodeURI($("#mypostform #form_epaggelma").val().trim());
    if (js_show_job_fields) {
      datasend+='&form_genisi_date='  + encodeURI($("#mypostform #form_genisi_date").val().trim());
      datasend+='&form_description='  + encodeURI($("#mypostform #form_description").val().trim());
      datasend+='&form_ethnikotita='  + encodeURI($("#mypostform #form_ethnikotita").val().trim());
      datasend+='&form_alli_apasxolisi='  + encodeURI($("#mypostform #form_alli_apasxolisi").val().trim());
      datasend+='&form_cv_proipiresia='  + encodeURI($("#mypostform #form_cv_proipiresia").val().trim());
      datasend+='&form_cv_spoydes='  + encodeURI($("#mypostform #form_cv_spoydes").val().trim());
      datasend+='&form_cv_seminaria='  + encodeURI($("#mypostform #form_cv_seminaria").val().trim());
      datasend+='&form_cv_mitriki_glossa='  + encodeURI($("#mypostform #form_cv_mitriki_glossa").val().trim());
      datasend+='&form_cv_jenes_glosses='  + encodeURI($("#mypostform #form_cv_jenes_glosses").val().trim());
      datasend+='&form_cv_sxesi_me_photografia='  + encodeURI($("#mypostform #form_cv_sxesi_me_photografia").val().trim());
      datasend+='&form_cv_has_bike=' + ($("#mypostform #form_cv_has_bike").is(":checked") ? '1' : '0');
      datasend+='&form_cv_has_motorcycle=' + ($("#mypostform #form_cv_has_motorcycle").is(":checked") ? '1' : '0');
      datasend+='&form_cv_has_car=' + ($("#mypostform #form_cv_has_car").is(":checked") ? '1' : '0');
      datasend+='&form_cv_metaforiko_meso='  + encodeURI($("#mypostform #form_cv_metaforiko_meso").val().trim());
      
      datasend+='&form_amka='  + encodeURI($("#mypostform #form_amka").val().trim());
      datasend+='&form_ama_eam='  + encodeURI($("#mypostform #form_ama_eam").val().trim());
      datasend+='&form_arithmos_tautoitas='  + encodeURI($("#mypostform #form_arithmos_tautoitas").val().trim());
      datasend+='&form_arxi_ekdosis='  + encodeURI($("#mypostform #form_arxi_ekdosis").val().trim());
      datasend+='&form_onoma_patera='  + encodeURI($("#mypostform #form_onoma_patera").val().trim());
      datasend+='&form_onoma_miteras='  + encodeURI($("#mypostform #form_onoma_miteras").val().trim());
      datasend+='&form_oikogeniaki_katastasti_id='  + encodeURI($("#mypostform #form_oikogeniaki_katastasti_id").val().trim());
      datasend+='&form_oikogeniaki_katastasti_paidia=' + encodeURI($("#mypostform #form_oikogeniaki_katastasti_paidia").val().trim());
    }

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
    
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    

    
    $('body').addClass("myloading");
    myreload = false;
    $('#reqfields').hide(600);
    
    $.ajax({
			url: '/my/profile-exec.php',
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
				  if (typeof data.myreload !== 'undefined') myreload=data.myreload;
					if (data.success == true) {
					  myreload=false;
					  from_php_js_profilepososto_user = data.profilepososto_user;
					  from_php_js_profilepososto_job = data.profilepososto_job;
					  change_myprofilepososto();
  					must_show_reqfields=false;
  					reqfields = '<p style="text-align: left;">'+gks_lang('Για να συμπληρώσετε το προφίλ σας στο 100%, θα πρέπει να συμπληρώσετε και τα παρακάτω πεδία')+':<ol style="text-align: left;">';
  					
  					if (js_show_job_fields) {
  					  if (data.job_rf.length>0) must_show_reqfields = true;
  					  for (var i = 0; i < data.job_rf.length; i++) {
  					    reqfields += '<li><i>' + $.base64.decode(data.job_rf[i])+'</i></li>';
  					  }
  					} else {
  					  if (data.user_rf.length>0) must_show_reqfields = true;
  					  for (var i = 0; i < data.user_rf.length; i++) {
  					    reqfields += '<li><i>' + $.base64.decode(data.user_rf[i])+'</i></li>';
  					  }
  				  }
  				  reqfields+='</ol></p>';
  				  extra_alert_message='';
  				  if (must_show_reqfields) {
  				    extra_alert_message=', '+gks_lang('αλλά θα πρέπει να συμπληρώσετε μερικά πεδία ακόμη');
  				    $('#reqfields').html(reqfields);
  					  $('#reqfields').show(600);
  				  }
  				  
  					myalert('ok:' + gks_lang('Επιτυχής ενημέρωση') + extra_alert_message);
  					
  					$('#form_password_old').val('');
  					$('#form_password_new1').val('');
  					$('#form_password_new2').val('');
  					
  					need_save=false;
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;    
    
    
  });
  
  window.mybankaccountdelete = function(event,andconfirm){			
    
		myelem = $(event.target);
		myelem_class=myelem.attr('class');    
		myelem_id=myelem.attr('id');    
 
      
  
	  if (myelem_class.indexOf('hourglass') > -1) {
	    return; 
	  }
	  if (myelem_id.length<=7) {
	    return;
	  }
		id_rec=myelem_id.substring(7);


    if (andconfirm == undefined) {
      myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε τον τραπεζικό λογαριασμό;'),'mybankaccountdelete','','','',event,false);
      return; 
    } 		
    
    myelem.css('color', '#000000');  
		myelem.removeClass('fa-trash-alt').addClass('fa-hourglass-half');  
		
		myreload = false;
		
		$('body').addClass("myloading");
	  $.ajax({
			url: '/my/profile-bank-account-delete.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: 'id_rec=' + id_rec,
			send_myelem_id: myelem_id,

			send_id_rec: id_rec,
			error : function(jqXHR ,textStatus,  errorThrown) {
		    $("body").removeClass("myloading");
		    res_elem= $('#'+this.send_myelem_id);
  		  res_elem.removeClass('fa-hourglass-half').addClass('fa-trash-alt'); 
  		  res_elem.css('color', '#ff0000');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $("body").removeClass("myloading");
				if (!data) {
					//$(".guid-" + this.sendguid + " #loading_img").hide();
  		    res_elem= $('#'+this.send_myelem_id);
    		  res_elem.removeClass('fa-hourglass-half').addClass('fa-trash-alt');
    		  res_elem.css('color', '#ff0000');								
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  if (typeof data.myreload !== 'undefined') myreload=data.myreload;
					if (data.success == true) {
            $('#rowbankaccount-' + this.send_id_rec).hide(300, myremove(this.send_id_rec));
					  if (myreload) window.location.reload();
            from_php_js_profilepososto_user = data.profilepososto_user;
					  from_php_js_profilepososto_job = data.profilepososto_job;
					  change_myprofilepososto();
				  } else {
    		    res_elem= $('#'+this.send_myelem_id);
      		  res_elem.removeClass('fa-hourglass-half').addClass('fa-trash-alt');
      		  res_elem.css('color', '#ff0000');

					  						
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});      
	  return;
  }
  
  function myremove(id) {
    $('#rowbankaccount-' +id).remove();
  }
  
  
  $(".mybankaccountdelete").click(mybankaccountdelete);


  $('#addbankaccount').click(function(event){

    
    $("#dialog_add_bank_account_iban").val('');  
    $("#dialog_add_bank_account_bank_id").val(0);  
    $("#dialog_add_bank_account_dikaiouxos").val('');  
    
  
    $("#dialog_add_bank_account_progressbar").hide();
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 700) dwidth=700;
	  if (dheight> 500) dheight=500;
	  dialog_add_bank_account.dialog('option', 'width', dwidth);
	  dialog_add_bank_account.dialog('option', 'height', dheight);
	  $('#dialog_add_bank_account').parent().css({position:'fixed'});      
    dialog_add_bank_account.dialog('open');
    $("#dialog_add_bank_account_iban").focus();
    
          
  });
  
  
  jqXHR = $('#mycv_upload').fileupload({
      dropZone:$('#f_button_add_files_cv'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          var acceptFileTypes = ['.pdf','.zip','.rar','.txt','.doc','.docx','.docm','.wps','.htm','.html','.odt','.sxw','.rtf'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': '+ data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': '+ data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("\n"));
          } else {
        
            file_cc++;
            data.mycc=file_cc;

            data.submit();
            $('#progress-bar_cv').show();
            $('#progress-extended_cv').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              myhtmlimg='<span id="item_upload_cv_' + file.insert_id + '">';
              myhtmlimg+='<input type="text" class="input_item_upload_cv"'
              myhtmlimg+=' name="input_item_upload_cv_' + file.insert_id + '"'; 
              myhtmlimg+=' id="input_item_upload_cv_' + file.insert_id + '"';
              myhtmlimg+=' value=""';
              myhtmlimg+=' placeholder="'+gks_lang('Περιγραφή π.χ. Βιογραφικό')+'"';
              myhtmlimg+=' style="width: 300px;max-width: 100%;"';
              myhtmlimg+=' title="'+gks_lang('Περιγραφή του αρχείου π.χ. Βιογραφικό, συστατική επιστολή, πτυχίο')+'"';
              myhtmlimg+='> <a href="' + file.url + '" target="_blank">' + file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)</a> <a href="" class="delete_upload_cv" data-id="' + file.insert_id + '"><img src="/my/img/0.png" border="0" width="16"></a><br></span>';
              $('#imagelist_cv').append(myhtmlimg);
              $('#input_item_upload_cv_' + file.insert_id).on('input propertychange change',input_item_upload_cv_event);
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
			url: '/my/profile-cv-delete.php?id=' + uid,
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
					  from_php_js_profilepososto_user = data.profilepososto_user;
					  from_php_js_profilepososto_job = data.profilepososto_job;
					  change_myprofilepososto();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_cv').click(delete_upload_click_cv);



  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
    
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("\n"));
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
              myhtmlimg+='      <a href="" class="set_profile_photo"   data-url="' + file.url_thumb + '" title="'+gks_lang('Ορισμός ως φωτογραφία προφίλ')+'"><img src="/my/img/profile.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="'+gks_lang('Διαγραφή')+'"><img src="/my/img/0.png" border="0" width="16"></a>';
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
        myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε'));
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
			url: '/my/profile-photo-delete.php?id=' + uid,
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
            
					  from_php_js_profilepososto_user = data.profilepososto_user;
					  from_php_js_profilepososto_job = data.profilepososto_job;
					  change_myprofilepososto();
    				
    				need_save=true;
            
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
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_user_photo').val(data_url);
    $('#form_user_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    need_save=true;
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    $('#form_user_photo').val('');
    $('#form_user_photo_img').attr("src",'/my/img/avatar.png');   
    $('#reset_profile_photo').hide(); 
    need_save=true;
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  }); 
  
  var ethnikotita_tags = [gks_lang('Ελληνική'), gks_lang('Ρωσική'), gks_lang('Βουλγαρική'), gks_lang('Αλβανική'), gks_lang('Βρετανική'), gks_lang('Τουρκική'),gks_lang('Σερβική'),gks_lang('Γαλλική'),gks_lang('Γερμανική'),gks_lang('Ιταλική')];
  $('#form_ethnikotita').tagit({allowSpaces: true, availableTags: ethnikotita_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  
  
  var alli_apasxolisi_tags = [gks_lang('Ιδιωτικός Υπάλληλος'), gks_lang('Δημόσιος Υπάλληλος'), gks_lang('Μερική απασχόληση'), gks_lang('Ελεύθερος Επαγγελματίας'), gks_lang('Άνεργος'), gks_lang('Άνεργος και κάτοχος κάρτας ανεργίας')];
  $('#form_alli_apasxolisi').tagit({allowSpaces: true, availableTags: alli_apasxolisi_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  
  var cv_glosses_tags = [gks_lang('Καμία'),gks_lang('Ελληνικά'),  gks_lang('Αγγλικά'), gks_lang('Γαλλικά'), gks_lang('Ιταλικά'), gks_lang('Γερμανικά'), gks_lang('Σερβικά'), gks_lang('Αλβανικά'),gks_lang('Ρωσικά'), gks_lang('Βουλγαρικά'), gks_lang('Τουρκικά')];
  $('#form_cv_mitriki_glossa').tagit({allowSpaces: true, availableTags: cv_glosses_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;}, });
  $('#form_cv_jenes_glosses').tagit({allowSpaces: true, availableTags: cv_glosses_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  
  
  var cv_sxesi_me_photografia_tags = [gks_lang('Καμία'),gks_lang('Ερασιτεχνική'),gks_lang('Σπουδές σε ΙΕΚ'),gks_lang('Σπουδές σε ΤΕΙ'),gks_lang('Σπουδές σε ΑΕΙ')];
  $('#form_cv_sxesi_me_photografia').tagit({allowSpaces: true, availableTags: cv_sxesi_me_photografia_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});


  var cv_metaforiko_meso_tags = [gks_lang('Πεζός'),gks_lang('Πεζή'),gks_lang('ΜΜΜ'),gks_lang('ΤΑΧΙ'),gks_lang('Ποδήλατο'),gks_lang('Μηχανή'),gks_lang('Αυτοκίνητο')];
  $('#form_cv_metaforiko_meso').tagit({allowSpaces: true, availableTags: cv_metaforiko_meso_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});



  
  function change_myprofilepososto() {
    if ($('#show_job_fields_checkbox').is(":checked") ) {
      myval = from_php_js_profilepososto_job;
    } else {
      myval = from_php_js_profilepososto_user;
    }
    $('#myprofilepososto').css('width',myval + '%');
    $('#myprofilepososto').attr('aria-valuenow',myval);
    
    if (myval == myprofilepososto_oldval) {
      $('#myprofilepososto').css('width', '0%');
    }
    $('#myprofilepososto').animate({'width': myval + '%'});
    $('#myprofilepososto').html(myval + '%');
    
    myprofilepososto_oldval = myval;
  }
  change_myprofilepososto();

  
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
  
  
  var elems_show_job_fields_checkbox = Array.prototype.slice.call(document.querySelectorAll('#show_job_fields_checkbox'));
  elems_show_job_fields_checkbox.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
  });  
  var cv_has = Array.prototype.slice.call(document.querySelectorAll('.cv_has'));
  cv_has.forEach(function(html) {
    var switchery10 = new Switchery(html,gks_switchery_defaults());
  }); 
  var changeCheckbox = document.querySelector('#show_job_fields_checkbox');
  
  changeCheckbox.onchange = function() {
    $('#reqfields').hide();
    if ($('#show_job_fields_checkbox').is(":checked")) {
      $('.show_job_fields_class').each(function() {
        $(this).show(300);
      });    
    } else {
      $('.show_job_fields_class').each(function() {
        $(this).hide(300);
      });    
    }
    change_myprofilepososto();
  };

  $( "#form_doy" ).autocomplete({
    source: "doy-autocomplete.php",
    minLength: 1,
    select: function( event, ui ) {
      $("#form_doy").val(ui.item.value);
    },
 
  });


  var timers_auto_id_user_cv = new Array();
  
  input_item_upload_cv_event = function () {
    var id_user_cv = parseInt($(this).attr('id').replace('input_item_upload_cv_',''));
    var myval = $(this).val();

    if (timers_auto_id_user_cv[id_user_cv] != undefined) {
      clearTimeout(timers_auto_id_user_cv[id_user_cv]);
    }    
    timers_auto_id_user_cv[id_user_cv] = setTimeout(saveToDBauto, 1000, id_user_cv,myval);
  }
  $('.input_item_upload_cv').on('input propertychange change',input_item_upload_cv_event);
  
  
  function saveToDBauto(id_user_cv,myval) {
    
    //console.log(id_user_cv);
    //console.log(myval);    
    
    datasend='';
    datasend+='&id_user_cv='  + encodeURI(id_user_cv);
    datasend+='&myval='  + encodeURI(myval);
    $('#input_item_upload_cv_' + id_user_cv).css('background-color','greenyellow');
    $.ajax({
			url: '/my/profile-auto-save-user-cv.php',
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
					  //$('#input_item_upload_cv_' + data.id_user_cv).val($.base64.decode(data.val));
  					$('#input_item_upload_cv_' + data.id_user_cv).animate({'background-color': '#ffffff'}, 1000, function() {
  					  $(this).css('background-color','#ffffff');
  					});
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
  }

  

  
  
  
// standard ....  functions  
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

   
  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
    
});

