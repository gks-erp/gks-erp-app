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
var cache_file='';
var need_save=false;
//var mychange = 'input keyup paste';
var mychange = 'change keyup paste';
//var mychange = 'propertychange input change keyup paste';

//var mychange = 'change';

var childs_and_ages=[];
var other_rooms_ages_index=[];
var keep_exist_array=[];


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
      if (elem.css('display')!='none') {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }
    
  });

  window.hotel_id_change = function hotel_id_change() {
    //child_age_price_ap_array
    var hotel_id=parseInt($('#hotel_id').val()); if (isNaN(hotel_id)) hotel_id=0;
    if (hotel_id<=0) return;
    
    $('select.childs_ages_list_select').each(function() {
      var select_obj=$(this);
      var cval=parseInt($(this).val()); if (isNaN(cval)) cval=0;
      $(this).find('option').each(function() {
        ccval=parseInt($(this).attr('value')); if (isNaN(ccval)) ccval=0;
        if (ccval>=0) $(this).remove();
      });
      for(ia=0; ia<=from_php_GKS_HOTEL_CHILD_ACCEPT_MAX_AGE[hotel_id]; ia++) {
        if (child_age_price_ap_array[hotel_id][ia]!='') {
          myhtml= '<option value="' + ia + '" ';
          myhtml+= '>' + child_age_price_ap_array[hotel_id][ia] + '</option>';
          select_obj.append(myhtml);
        }
      }
      if (cval>=0) {
        elem=select_obj.find('option[value=' + cval + ']');
        if (elem.length>=1) {
          select_obj.val(cval);
        }
      }
    });
    
    
    $('#tableroomlist .itemtd5').each(function() {
      crval=parseInt($(this).attr('data-hotel_id')); if (isNaN(crval)) crval=0;
      if (crval==hotel_id) {
        $(this).removeClass('gks_room_for_delete'); 
        $(this).parent().find('.itemtd11').removeClass('gks_room_for_delete'); 
      } else {
        $(this).addClass('gks_room_for_delete');
        $(this).parent().find('.itemtd11').addClass('gks_room_for_delete'); 
      }
    });
    
    company_id=0;
    company_sub_id=0;
    v=$('#hotel_id option:selected').attr('data-company_id_sub_id');
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
    reservation_journal_id_fill('reservation_journal_id','reservation_seira_id',company_id,company_sub_id,0);
    
    
    fields_change_set_pososto();
    need_save=true;
    calc_pliroteo();    
  }
  
  $('#hotel_id').change(hotel_id_change);
  



  window.reservation_journal_id_change = function reservation_journal_id_change() {
    v=$('#reservation_journal_id').val();
    acc_journal_id=parseInt(v); if (isNaN(acc_journal_id)) acc_journal_id=0; 
    reservation_seira_id_fill('reservation_seira_id',acc_journal_id,0);
  }
  $('#reservation_journal_id').change(reservation_journal_id_change);  

  window.reservation_seira_id_change = function reservation_seira_id_change() {
    acc_seira_id=parseInt($('#reservation_seira_id').val()); if (isNaN(acc_seira_id)) acc_seira_id=0; 
    is_xeirografi=parseInt($('#reservation_seira_id option:selected').attr('data-is_xeirografi')); if (isNaN(is_xeirografi)) is_xeirografi=0; 
    if (is_xeirografi!=0) {
      $('#reservation_number_int').prop('disabled' , false);
      //$('#submit_button_080listing').show();
      //$('#submit_button_090ekdosi').hide();
    } else {
      $('#reservation_number_int').prop('disabled' , true);
      //$('#submit_button_080listing').hide();
      //$('#submit_button_090ekdosi').show();
    }
  }
  $('#reservation_seira_id').change(reservation_seira_id_change);
  
  
  $('#reservation_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  })); 

  $('#check_in' ).datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime: 
    function(dp,$input) {
      fields_change_set_pososto();
      calc_days();
      need_save=true;
      calc_pliroteo();
    }
  }));
  $('#check_out').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime: 
    function(dp,$input) {
      fields_change_set_pososto();
      calc_days();
      need_save=true;
      calc_pliroteo();
    }
  }));

  if ($('#check_in').length>0) {
    var old_din= $('#check_in'). datetimepicker('getValue').getTime();
    var old_dout=$('#check_out').datetimepicker('getValue').getTime();
  }
  
  function calc_days() {
    var din = $('#check_in').val();
    if (din=='' || din=='__/__/____')  {
      $('#num_days').val('0'); return;    
    }
    var dout = $('#check_out').val();
    if (dout=='' || dout=='__/__/____')  {
      $('#num_days').val('0'); return;    
    }
    
    din  = $('#check_in'). datetimepicker('getValue');
    dout = $('#check_out').datetimepicker('getValue');
    
    if (din.getTime() == old_din && dout.getTime() == old_dout) {
      return;
    }
    old_din = din.getTime();
    old_dout= dout.getTime();
    
    if (dout.getTime() <= din.getTime()) {
      $('#num_days').val('0'); return;
    }
    num_days=0;
    //console.log(din);
    //console.log(dout);
    //console.log('--');
    if (din.getHours() < from_php_defs_inh) {
      din2 = new Date(din.getFullYear(),din.getMonth(),din.getDate() - 1 ,0,0,0);
    } else {
      din2 = new Date(din.getFullYear(),din.getMonth(),din.getDate(),0,0,0);
    }
    if (dout.getHours() > from_php_defs_outh) {
      dout2 = new Date(dout.getFullYear(),dout.getMonth(),dout.getDate() + 1,0,0,0);
    } else {
      dout2 = new Date(dout.getFullYear(),dout.getMonth(),dout.getDate(),0,0,0);
    }
    //console.log(din2.getTime());
    //console.log(dout2.getTime());
    if (dout2.getTime() <= din2.getTime()) {
      $('#num_days').val('0'); return;
    }
    //console.log(din2);
    //console.log(dout2);
    //console.log('----');
    var timeDiff = Math.abs(dout2.getTime() - din2.getTime());
    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
    //console.log(diffDays);
    //console.log('------');
    $('#num_days').val(diffDays);
    
  }
  
  
  $('.deleterowbtn').click(function(event) {  
    var delete_id=$(this).attr('data-id');
    var delete_model=$(this).attr('data-model');
    var delete_backurl = $(this).attr('data-backurl');
    if (delete_id == undefined || delete_model == undefined) {
      return false; 
    }
    if (delete_backurl == undefined) delete_backurl='';
    myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε την εγγραφή;'),'deleterow',delete_model,delete_id,delete_backurl);
    return false;
  });  

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  $('#submit_button_010draft').click(function(event) {mysubmit('010draft'); return false;});
  $('#submit_button_040cancelled').click(function(event) {mysubmit('040cancelled'); return false;});
  $('#submit_button_050rejected').click(function(event) {mysubmit('050rejected'); return false;});
  $('#submit_button_070wait_payment').click(function(event) {mysubmit('070wait_payment'); return false;});
  $('#submit_button_080confirm').click(function(event) {mysubmit('080confirm'); return false;});
  $('#submit_button_100completed').click(function(event) {mysubmit('100completed'); return false;});
 
 




  
  var dialog_room_select_other_room=false;
  dialog_room = $('#dialog_room').dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    //position: 'center top',

    buttons: [
      {
        id: "dialog_room_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('OK'),
        //icon: "ui-icon-circle-plus",
        click: function() {

          //console.log(dialog_room.aa);
          var hotel_id=parseInt($('#hotel_id').val()); if (isNaN(hotel_id)) hotel_id=0;
          if (hotel_id<=0) return;
          
          if (myparseInt($('#dialog_room_room_id').val()) <=0) {
            myalert('error:'+gks_lang('Επιλέξτε κάποιο δωμάτιο'));
            return;  
          }
          
          myval1 = myparseInt($('#dialog_room_num_adults').val());
          new_visitors=dialog_room.visitors;
          new_visitors_max=dialog_room.visitors_max;
          extra_beds=parseInt($('#dialog_room_num_extra_beds').val());
          new_visitors+=extra_beds;
          new_visitors_max+=extra_beds;
          
          if (myval1 > new_visitors) {
            myalert('error:'+gks_lang('Το μέγιστο πλήθος ενηλίκων σε αυτό το δωμάτιο είναι')+':<br><b>' + new_visitors + '</b><br>'+gks_lang('Έχετε ορίσει περισσότερους')+':<br><b>' + myval1.toString() + '</b>');
            return;
          }
          myval2 = myparseInt($('#dialog_room_num_childs').val());
  //        if (myval2 > dialog_room.visitors_childs) {
  //          myalert('error:'+gks_lang('Το μέγιστο πλήθος παιδιών σε αυτό το δωμάτιο είναι')+':<br><b>' + dialog_room.visitors_childs + '</b><br>'+gks_lang('Έχετε ορίσει περισσότερα')+':<br><b>' + myval2.toString() + '</b>');
  //          return;
  //        }
          if (myval1 + myval2 > new_visitors_max) {
            myalert('error:'+gks_lang('Το μέγιστο πλήθος επισκεπτών σε αυτό το δωμάτιο είναι')+':<br><b>' + new_visitors_max + '</b><br>'+gks_lang('Έχετε ορίσει περισσότερους')+':<br><b>' + myval1.toString() + '</b> '+gks_lang('ενήλικες') + (myval2 > 0 ? ' και <b>' + myval2.toString() + '</b> '+gks_lang('παιδιά') : '' ));
            return;
          }
          if (parseFloat($('#dialog_room_ajia_total').val())<=0) {
            myalert('error:'+gks_lang('Ορίστε κάποια αξία για το δωμάτιο'));
            return;          
          }
          
          var has_one_unselected=false;
          var tmp_rchilds_ages_list=[];
          $('.rchilds_ages_list_item').each(function() {
            ci=parseInt($(this).val());
            if (isNaN(ci)) ci=-1;
            if (ci<0) has_one_unselected=true;
            ca=parseInt($(this).find('option:selected').attr('data-age'));
            if (isNaN(ca)) ca=-1;
            if (ci > 0) tmp_rchilds_ages_list.push({index: ci, age: ca});
          });
          if (has_one_unselected) {
            myalert('error:'+gks_lang('Ορίστε τα παιδιά σε αυτό το δωμάτιο'));
            //return; 
          }    
             
          rchilds_ages_list_item_warning();
          if (warning_index.length>0) {
            myalert('error:'+gks_lang('Διορθώστε πρώτα τα λάθη με την επιλογή των παιδιών'));
            return; 
          }
          
          need_save=true;
          
          if (dialog_room.aa == -1) {
            aa = json_rooms_list.length;
            json_rooms_list.push({'aa':aa,'add':1,'edit':0,'delete':0,'recid':-1});
          } else {
            aa = dialog_room.aa;
            json_rooms_list[aa].edit=1;
          }
          
          
  
          json_rooms_list[aa].room_descr = $('#dialog_room_room_descr').val();
          json_rooms_list[aa].room_type_descr = $('#dialog_room_room_type_descr').val();
          
          json_rooms_list[aa].visitors = dialog_room.visitors;
          json_rooms_list[aa].visitors_childs = dialog_room.visitors_childs;
          json_rooms_list[aa].visitors_max = dialog_room.visitors_max;
          json_rooms_list[aa].room_type_child_kounies = dialog_room.room_type_child_kounies;
          json_rooms_list[aa].room_type_extra_beds = dialog_room.room_type_extra_beds;
          
          json_rooms_list[aa].hotel_room_id = myparseInt($('#dialog_room_room_id').val());
          //$('#dialog_room_room_id_result').html('');
          json_rooms_list[aa].rnum_adults = myparseInt($('#dialog_room_num_adults').val());
          json_rooms_list[aa].rnum_childs = myparseInt($('#dialog_room_num_childs').val());
  
          json_rooms_list[aa].rchilds_ages_list = JSON.parse(JSON.stringify(tmp_rchilds_ages_list)); //CLONE Array.from(tmp_rchilds_ages_list);
          //console.log('tmp_rchilds_ages_list');
          //console.log(tmp_rchilds_ages_list);
          
          json_rooms_list[aa].rnum_child_kounies = myparseInt($('#dialog_room_num_child_kounies').val());
          json_rooms_list[aa].rnum_extra_beds = myparseInt($('#dialog_room_num_extra_beds').val());
          
          //$('#dialog_room_ajia_math').html('');
          json_rooms_list[aa].ajia_total =parseFloat($('#dialog_room_ajia_total').val()); 
          json_rooms_list[aa].gks_ekptosi_pososto = parseFloat($('#dialog_room_ekptosi_pososto').val());
          json_rooms_list[aa].rsxolio = $('#dialog_room_sxolio').val();
          json_rooms_list[aa].gks_nickname = $('#dialog_room_gks_nickname').val();
          json_rooms_list[aa].ruser_id = myparseInt($('#dialog_room_user_id').val());
          json_rooms_list[aa].ruser_first_name = $('#dialog_room_user_first_name').val();
          json_rooms_list[aa].ruser_last_name = $('#dialog_room_user_last_name').val();
          json_rooms_list[aa].ruser_email = $('#dialog_room_user_email').val();
          json_rooms_list[aa].ruser_mobile = $('#dialog_room_user_mobile').val();
          json_rooms_list[aa].ruser_lang = $('#dialog_room_user_lang').val();
          json_rooms_list[aa].ruser_ma_odos = $('#dialog_room_user_ma_odos').val();
          json_rooms_list[aa].ruser_ma_arithmos = $('#dialog_room_user_ma_arithmos').val();
          json_rooms_list[aa].ruser_ma_orofos = $('#dialog_room_user_ma_orofos').val();
          json_rooms_list[aa].ruser_ma_perioxi = $('#dialog_room_user_ma_perioxi').val();
          json_rooms_list[aa].ruser_ma_poli = $('#dialog_room_user_ma_poli').val();
          json_rooms_list[aa].ruser_ma_tk = $('#dialog_room_user_ma_tk').val();
          json_rooms_list[aa].ruser_ma_country_id = myparseInt($('#dialog_room_user_ma_country_id').val());
          json_rooms_list[aa].ruser_ma_nomos_id = myparseInt($('#dialog_room_user_ma_nomos_id').val());
          json_rooms_list[aa].ruser_fiscal_position_id = myparseInt($('#dialog_room_user_fiscal_position_id').val());
          json_rooms_list[aa].ruser_pricelist_id = myparseInt($('#dialog_room_user_pricelist_id').val());
          
          //console.log(json_rooms_list[aa].gks_ekptosi_pososto);
          
          //console.log('a1 ' + json_rooms_list[aa].ruser_id);
          
          if ($('#selecttype0').is(':checked')) json_rooms_list[aa].ruser_id = -1;
          //console.log('a2 ' + json_rooms_list[aa].ruser_id);
          
            
          //console.log(json_rooms_list[aa]);
          
          if (dialog_room.aa == -1) {
            mytr='';
            mytr+='<tr class="' + ((aa % 2 == 0) ? 'even' : 'odd') + '" data-aa="' + aa + '">';
              mytr+='<th class="itemtd1  mytdcm p-0 d-print-none" scope="row" nowrap>' + (aa + 1) + '</th>';
              mytr+='<td class="itemtd2  mytdcm p-0 d-print-none" nowrap><i class="editiconroom enterrow fas fa-pen" data-aa="' + aa + '" title="'+gks_lang('Προβολή')+'" style="cursor:pointer"></i></td>';
              mytr+='<td class="itemtd3  mytdcm p-0 d-print-none" nowrap>*';
              mytr+='<br><i class="fas fa-trash-alt deleteitem" data-aa="' + aa + '"></i></td>';
              mytr+='<td class="itemtd5  mytdcml" data-hotel_id="' + hotel_id + '">' + json_rooms_list[aa].room_descr + '</td>';
              mytr+='<td class="itemtd11 mytdcml" data-hotel_id="' + hotel_id + '">' + json_rooms_list[aa].room_type_descr + '</td>';
              
              mytr+='<td class="itemtd12" nowrap align="center">' +
                '<input type="number" class="form-control form-control-sm gks_ekptosi_pososto" data-aa="' + aa + '" ' +
                'value="' + json_rooms_list[aa].gks_ekptosi_pososto + '" ' +
                'data-prev-value="0" ' +
                'style="text-align:right;min-width:100px;" min=0 step="' + from_php_GKS_INPUT_STEP_POSOSTO + '" >' +
                '<div class="gks_coupon" data-aa="' + aa + '"><div ' +
                'class="gks_coupon_item" data-aa="' + aa + '" style="display:none;"></div></div>';
  
              
              mytr+='</td>';
              num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
              mytr+='<td class="itemtd14" nowrap align="center">' +
                '<input type="number" class="form-control form-control-sm gks_price_per_item" data-aa="' + aa + '" ' + 
                'value="' +
                (num_days>0 ? json_rooms_list[aa].ajia_total/num_days : 0) + 
                '" ' + 
                'style="text-align:right;min-width:100px;" min=0 step="' + from_php_GKS_INPUT_STEP_AJIA + '" >'; 
              
              mytr+='</td>';
              mytr+='<td class="itemtd6" nowrap align="center">' +
                '<input type="number" class="form-control form-control-sm gks_price_final" data-aa="' + aa + '" ' + 
                'value="' + json_rooms_list[aa].ajia_total + '" ' + 
                'style="text-align:right;min-width:100px;" min=0 step="' + from_php_GKS_INPUT_STEP_AJIA + '" >' + 
                
                '<div class="gks_ekptosi" data-aa="' + aa + '"><div ' +
                'class="gks_ekptosi_poso" data-aa="' + aa + '" style="display:none;"   ></div></div>';
  
              
  //            if (json_rooms_list[aa].ajia_total!=0) mytr+= 
  //              (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
  //              json_rooms_list[aa].ajia_total.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
  //              (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '');
  //            
              mytr+='</td>';
              mytr+='<td class="itemtd13 mytdcm" nowrap>' +
                '<i class="fa fa-info-circle room_info_price" data-aa="' + aa + '"></i>' +
              '</td>';
              
              mytr+='<td class="itemtd7 mytdcm" nowrap>';
              roomline='';
              if (json_rooms_list[aa].rnum_adults>0)
                roomline+='<i class="fa fa-male tooltipster" style="color:#aaaaaa;" title="'+gks_lang('Ενήλικες')+'"></i>' + json_rooms_list[aa].rnum_adults;
              if (json_rooms_list[aa].rnum_childs>0)  
                roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa-child tooltipster" style="color:#aaaaaa;font-size:80%;" title="'+gks_lang('Παιδιά')+'"></i>' + json_rooms_list[aa].rnum_childs;
              if (roomline!='') roomline+='<br>';
              if (json_rooms_list[aa].rnum_child_kounies>0)
                roomline+='<i class="fa fa-box tooltipster" style="color:#aaaaaa;font-size:90%;" title="'+gks_lang('Βρεφικό κρεβάτι')+'"></i>' + json_rooms_list[aa].rnum_child_kounies;
              if (json_rooms_list[aa].rnum_extra_beds>0)  
                roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa-bed tooltipster" style="color:#aaaaaa;" title="'+gks_lang('Επιπλέον κρεβάτι')+'"></i>' + json_rooms_list[aa].rnum_extra_beds;
              mytr+=roomline;
                
              mytr+='</td>';
            
              mytr+='<td class="itemtd8 mytdcm" nowrap>';
                roomline='';
                if (json_rooms_list[aa].ruser_id == -1) {
                  roomline+=gks_lang('Ίδιος πελάτης');
                } else  if (json_rooms_list[aa].ruser_id>0) {
                  if (json_rooms_list[aa].gks_nickname!='') roomline+='<a href="admin-users-item.php?id="' + json_rooms_list[aa].ruser_id + '>' + json_rooms_list[aa].gks_nickname + '</a>, ';
                  if (json_rooms_list[aa].ruser_email != '') roomline+='<a href="mailto:' + json_rooms_list[aa].ruser_email + '">' + json_rooms_list[aa].ruser_email + '</a>, ';
                  if (json_rooms_list[aa].ruser_mobile != '') roomline+='<span><a href="tel:' + json_rooms_list[aa].ruser_mobile + '" class="'+ from_php_gks_voip_params.class_span+'">' + json_rooms_list[aa].ruser_mobile + '</a>'+from_php_gks_voip_params.html_after_span+'</span>, ';
                  if (roomline.length>2) roomline=roomline.substr(0,roomline.length -2);
                } else {
                  if (json_rooms_list[aa].ruser_last_name != '' || json_rooms_list[aa].ruser_first_name != '') roomline+=json_rooms_list[aa].ruser_last_name + ' ' + json_rooms_list[aa].ruser_first_name + ', ';
                  if (json_rooms_list[aa].ruser_email != '') roomline+='<a href="mailto:' + json_rooms_list[aa].ruser_email + '">' + json_rooms_list[aa].ruser_email + '</a>, ';
                  if (json_rooms_list[aa].ruser_mobile != '') roomline+='<span><a href="tel:' + json_rooms_list[aa].ruser_mobile + '" class="'+ from_php_gks_voip_params.class_span+'">' + json_rooms_list[aa].ruser_mobile + '</a>'+from_php_gks_voip_params.html_after_span+'</span>, ';
                  
                  if (roomline.length>2) roomline=roomline.substr(0,roomline.length -2);
                }
                mytr+=roomline;
                
  //              if (json_rooms_list[aa].ruser_id==-1) {
  //                mytr+=gks_lang('Ίδιος πελάτης');
  //              } else if (json_rooms_list[aa].ruser_id>0) {
  //                mytr+='<a href="admin-users-item.php?id="' + json_rooms_list[aa].ruser_id + '>' + json_rooms_list[aa].gks_nickname + '</a>';
  //              } else {
  //                mytr+=json_rooms_list[aa].ruser_last_name + ' ' + json_rooms_list[aa].ruser_first_name;
  //              }
  //              if (json_rooms_list[aa].ruser_mobile != '') mytr+=', <a href="tel:' + json_rooms_list[aa].ruser_mobile + '">' + json_rooms_list[aa].ruser_mobile + '</a>';
  //              if (json_rooms_list[aa].ruser_email != '') mytr+=', <a href="mailto:' + json_rooms_list[aa].ruser_email + '">' + json_rooms_list[aa].ruser_email + '</a>';
              mytr+='</td>';
                        
              mytr+='<td class="itemtd9 mytdcm" nowrap>';
                if (json_rooms_list[aa].ruser_lang!='' && $('#dr_user_lang').val()!= json_rooms_list[aa].ruser_lang) mytr+='<img src="/my/img/flags/flags_iso/32/' + 
                gks_langs.find(element => element.id_lang==json_rooms_list[aa].ruser_lang).lang_ico.toLowerCase() + 
                '.png" title="' + 
                gks_langs.find(element => element.id_lang==json_rooms_list[aa].ruser_lang).lang_name + '">';
                if (json_rooms_list[aa].ruser_ma_country_id!=0 && myparseInt($('#dr_user_ma_country_id').val())!= json_rooms_list[aa].ruser_ma_country_id) mytr+=' <img src="/my/img/flags/flags_iso/32/' + 
                   gks_country.find(element => element.id_country==json_rooms_list[aa].ruser_ma_country_id).country_initials.toLowerCase() + 
                   '.png" title="' + 
                   gks_country.find(element => element.id_country==json_rooms_list[aa].ruser_ma_country_id).country_name + '">';
              mytr+='</td>';
              mytr+='<td class="itemtd10" align="left">' + nl2br(escapeHtml(json_rooms_list[aa].rsxolio)) + '</td>';
            mytr+='</tr>';
            
            if ($('#tableroomlist tbody tr:last').length == 0) {
              $('#tableroomlist tbody').append(mytr);
            } else {
              $('#tableroomlist tbody tr:last').after(mytr);   
            }
            $('#tableroomlist tbody tr:last .editiconroom').click(editiconroom_click);  
            $('#tableroomlist tbody tr:last .deleteitem').click(deleteitem_click);  
            $('#tableroomlist tbody tr:last .gks_ekptosi_pososto').on(mychange, gks_ekptosi_change);  
            
            $('#tableroomlist tbody tr:last .gks_coupon_item').click(gks_coupon_item_click);  
            $('#tableroomlist tbody tr:last .gks_price_final').
              on(mychange, gks_price_final_change).
              attr('data-ajia_table_math',$.base64.encode(dialog_room_ajia_table_math)).
              attr('data-ajia_table_html',$.base64.encode(dialog_room_ajia_table_html)).
              attr('data-ajia_table_array',dialog_room_ajia_table_array).
              attr('data-other_taxes_tooltip',dialog_room_other_taxes_tooltip);
  
            $('#tableroomlist tbody tr:last .gks_voip_originate_after_span').click(gks_voip_originate_click);
            room_info_price_tooltipster(aa);
  
            
          } else {
            if (dialog_room_select_other_room) {
              $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd5').attr('data-hotel_id',hotel_id).removeClass('gks_room_for_delete');
              $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd11').attr('data-hotel_id',hotel_id).removeClass('gks_room_for_delete');
            }
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd5').html(json_rooms_list[aa].room_descr);
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd11').html(json_rooms_list[aa].room_type_descr);
  
            
            roomline=
              (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
              json_rooms_list[aa].ajia_total.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
              (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '');
            //$('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd6').html(roomline);
            $('.gks_ekptosi_pososto[data-aa=' + aa + ']').val(json_rooms_list[aa].gks_ekptosi_pososto);
            $('.gks_price_final[data-aa=' + aa + ']').
              val(json_rooms_list[aa].ajia_total).
              attr('data-ajia_table_math',$.base64.encode(dialog_room_ajia_table_math)).
              attr('data-ajia_table_html',$.base64.encode(dialog_room_ajia_table_html)).
              attr('data-ajia_table_array',dialog_room_ajia_table_array).
              attr('data-other_taxes_tooltip',dialog_room_other_taxes_tooltip);
            
            room_info_price_tooltipster(aa);
            
  
    			  
  
            roomline='';
            if (json_rooms_list[aa].rnum_adults>0)
              roomline+='<i class="fa fa-male" style="color:#aaaaaa;"></i>' + json_rooms_list[aa].rnum_adults;
            if (json_rooms_list[aa].rnum_childs>0)  
              roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa-child" style="color:#aaaaaa;font-size:70%;"></i>' + json_rooms_list[aa].rnum_childs;
            if (roomline!='') roomline+='<br>';
            if (json_rooms_list[aa].rnum_child_kounies>0)
              roomline+='<i class="fa fa-box tooltipster" style="color:#aaaaaa;font-size:90%;" title="'.gks_lang('Βρεφικό κρεβάτι')+'"></i>' + json_rooms_list[aa].rnum_child_kounies;
            if (json_rooms_list[aa].rnum_extra_beds>0)  
              roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa fa-bed tooltipster" style="color:#aaaaaa;" title="'.gks_lang('Επιπλέον κρεβάτι')+'"></i>' + json_rooms_list[aa].rnum_extra_beds;
            
            
            
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd7').html(roomline);
            
            
            roomline='';
            if (json_rooms_list[aa].ruser_id == -1) {
              roomline+=gks_lang('Ίδιος πελάτης');
            } else  if (json_rooms_list[aa].ruser_id>0) {
              if (json_rooms_list[aa].gks_nickname!='') roomline+='<a href="admin-users-item.php?id=' + json_rooms_list[aa].ruser_id + '">' + json_rooms_list[aa].gks_nickname + '</a>, ';
              if (json_rooms_list[aa].ruser_email != '') roomline+='<a href="mailto:' + json_rooms_list[aa].ruser_email + '">' + json_rooms_list[aa].ruser_email + '</a>, ';
              if (json_rooms_list[aa].ruser_mobile != '') roomline+='<span><a href="tel:' + json_rooms_list[aa].ruser_mobile + '" class="'+ from_php_gks_voip_params.class_span+'">' + json_rooms_list[aa].ruser_mobile + '</a>'+from_php_gks_voip_params.html_after_span+'</span>, ';
              if (roomline.length>2) roomline=roomline.substr(0,roomline.length -2);
            } else {
              if (json_rooms_list[aa].ruser_last_name != '' || json_rooms_list[aa].ruser_first_name != '') roomline+=json_rooms_list[aa].ruser_last_name + ' ' + json_rooms_list[aa].ruser_first_name + ', ';
              if (json_rooms_list[aa].ruser_email != '') roomline+='<a href="mailto:' + json_rooms_list[aa].ruser_email + '">' + json_rooms_list[aa].ruser_email + '</a>, ';
              if (json_rooms_list[aa].ruser_mobile != '') roomline+='<span><a href="tel:' + json_rooms_list[aa].ruser_mobile + '" class="'+ from_php_gks_voip_params.class_span+'">' + json_rooms_list[aa].ruser_mobile + '</a>'+from_php_gks_voip_params.html_after_span+'</span>, ';
              if (roomline.length>2) roomline=roomline.substr(0,roomline.length -2);
            }
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd8').html(roomline);
            
            roomline='';
            if (json_rooms_list[aa].ruser_lang!='' && $('#dr_user_lang').val()!= json_rooms_list[aa].ruser_lang) roomline+='<img src="/my/img/flags/flags_iso/32/' + 
            gks_langs.find(element => element.id_lang==json_rooms_list[aa].ruser_lang).lang_ico.toLowerCase() + 
            '.png" title="' + 
            gks_langs.find(element => element.id_lang==json_rooms_list[aa].ruser_lang).lang_name + '">';
            if (json_rooms_list[aa].ruser_ma_country_id!=0 && myparseInt($('#dr_user_ma_country_id').val())!= json_rooms_list[aa].ruser_ma_country_id) roomline+=' <img src="/my/img/flags/flags_iso/32/' + 
            gks_country.find(element => element.id_country==json_rooms_list[aa].ruser_ma_country_id).country_initials.toLowerCase() + 
            '.png" title="' + 
            gks_country.find(element => element.id_country==json_rooms_list[aa].ruser_ma_country_id).country_name + '">';
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd9').html(roomline);
            
            roomline = nl2br(escapeHtml(json_rooms_list[aa].rsxolio));
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd10').html(roomline);
            
            $('#tableroomlist tbody tr[data-aa=' + aa + '] .gks_voip_originate_after_span').click(gks_voip_originate_click);          
          }
          
          $('#tableroomlist tbody tr[data-aa=' + aa + '] .itemtd7 .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
          
          
          $(this).dialog('close');
          //calc_pliroteo('gks_ekptosi',aa);
          calc_pliroteo('gks_price_final',aa);
          
			
		    }	
      },
      {
        id: "dialog_room_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }			
      },      
    ],    

  });
  

  var dialog_room_product_price_start_all_total=0;
  
  function myroomedit(aa) {
    dialog_room_select_other_room=false;
    
    dialog_room_div_customer_more = 0;
    if (dialog_room_div_customer_more == 1) {
      $('#dialog_room_div_customer_more_show').hide();
      $('.dialog_room_divs_customer_more').each(function() {
        $(this).show();  
      }); 
      $('#dialog_room_div_customer_more_hide').show();       
    } else {
      $('#dialog_room_div_customer_more_hide').hide();
      $('.dialog_room_divs_customer_more').each(function() {
        $(this).hide();  
      }); 
      $('#dialog_room_div_customer_more_show').show();       
    }
            
    $('#dialog_room_room_descr').val('');
    $('#dialog_room_room_type_descr').val('');
    $('#dialog_room_room_type_visitors_html').html('');
    $('#dialog_room_room_id').val('');
    $('#dialog_room_room_id_result').html('');
    $('#dialog_room_num_adults').val('');
    $('#dialog_room_num_childs').val('');
    $('#dialog_room_rchilds_ages_list_main_div').html('');
    $('#dialog_room_num_child_kounies').val('0');
    $('#dialog_room_num_extra_beds').val('0');
    
    $('#dialog_room_ajia_math').html('');
    $('#dialog_room_ajia_table').hide();
    $('#dialog_room_price_per_item').val('');
    $('#dialog_room_ajia_total').val('');
    $('#dialog_room_ekptosi_pososto').val($('#def_ekptosi').val());
    dialog_room_product_price_start_all_total=0;
    $('#dialog_room_gks_ekptosi_poso').html('');
    $('#dialog_room_sxolio').val('');
    $('#dialog_room_gks_nickname').val('');
    $('#dialog_room_user_id').val('');
    $('#dialog_room_user_first_name').val('');
    $('#dialog_room_user_last_name').val('');
    $('#dialog_room_user_email').val('');
    $('#dialog_room_user_mobile').val('');
    $('#dialog_room_user_lang').val('');
    $('#dialog_room_user_ma_odos').val('');
    $('#dialog_room_user_ma_arithmos').val('');
    $('#dialog_room_user_ma_orofos').val('');
    $('#dialog_room_user_ma_perioxi').val('');
    $('#dialog_room_user_ma_poli').val('');
    $('#dialog_room_user_ma_tk').val('');
    $('#dialog_room_user_ma_country_id').val(0);
    $('#dialog_room_user_ma_nomos_id option').each(function() { 
      if ($(this).attr('value') >0 ) {
        $(this).remove();
      }
    });
    $('#dialog_room_user_fiscal_position_id').val(0);
    $('#dialog_room_user_pricelist_id').val(0);
    
    
    
    
    if (aa == -1 || typeof json_rooms_list[aa] == 'undefined') {
      dialog_room.aa=-1;
      dialog_room.visitors = 1;
      dialog_room.visitors_childs = 0;
      dialog_room.visitors_max = 1;
      dialog_room.rchilds_ages_list=[];
      dialog_room.room_type_child_kounies=0;
      dialog_room.room_type_extra_beds=0;
      
      
      //dialog_room.rchild_kounies_ages_list=[];
      //dialog_room.rextra_beds_ages_list=[];
      $('#dialog_room_num_adults').attr('max', (dialog_room.visitors).toString());
  		$('#dialog_room_num_childs').attr('max', (dialog_room.visitors_max).toString());
  		
  		$('#selecttype0').prop('checked', true);
      $('.selecttypediv').each(function() {
        $(this).hide();
      });
      $('.dialog_room_pelatistype').each(function() {
        $(this).prop("disabled", false);
      });
 					  
 			//dialog_room_user_lang
 					  
      $('#dialog_room_user_lang').val($('#dr_user_lang').val());
      if (myparseInt($('#dr_user_ma_country_id').val()) > 0) {
        $('#dialog_room_user_ma_country_id').val($('#dr_user_ma_country_id').val());
        if (myparseInt($('#dr_user_ma_nomos_id').val())>0) {
          nomos_fill('dialog_room_user_ma_nomos_id',myparseInt($('#dr_user_ma_country_id').val()),myparseInt($('#dr_user_ma_nomos_id').val()));
        } else {
          nomos_fill('dialog_room_user_ma_nomos_id',myparseInt($('#dr_user_ma_country_id').val()),0);
        }
      }
      $('#dialog_room_user_fiscal_position_id').val($('#fiscal_position_id').val());
      $('#dialog_room_user_pricelist_id').val($('#pricelist_id').val());

      $('#div_dialog_room_num_child_kounies').hide();
      $('#dialog_room_num_child_kounies').val(0);
      $('#div_dialog_room_num_extra_beds').hide();
      $('#dialog_room_num_extra_beds').val(0);
      
      
 					  
    } else {
      dialog_room.aa=aa;
  		dialog_room.visitors = json_rooms_list[aa].visitors;
  		dialog_room.visitors_childs = json_rooms_list[aa].visitors_childs;
  		dialog_room.visitors_max = json_rooms_list[aa].visitors_max;
  		dialog_room.rchilds_ages_list =	JSON.parse(JSON.stringify(json_rooms_list[aa].rchilds_ages_list)); //CLONE Array.from(json_rooms_list[aa].rchilds_ages_list);
      dialog_room.room_type_child_kounies=json_rooms_list[aa].room_type_child_kounies
      dialog_room.room_type_extra_beds=json_rooms_list[aa].room_type_extra_beds;
      
      
  		//dialog_room.rchild_kounies_ages_list =	JSON.parse(JSON.stringify(json_rooms_list[aa].rchild_kounies_ages_list)); //CLONE Array.from(json_rooms_list[aa].rchilds_ages_list);
  		//dialog_room.rextra_beds_ages_list =	JSON.parse(JSON.stringify(json_rooms_list[aa].rextra_beds_ages_list)); //CLONE Array.from(json_rooms_list[aa].rchilds_ages_list);
      //console.log(json_rooms_list[aa]);

      
      $('#dialog_room_room_descr').val(json_rooms_list[aa].room_descr);
      $('#dialog_room_room_type_descr').val(json_rooms_list[aa].room_type_descr);
      $('#dialog_room_room_type_visitors_html').html('<i class="fa fa-male" style="color:#aaaaaa;"></i>' + json_rooms_list[aa].visitors + ' ' + '<i class="fa fa-child" style="color:#aaaaaa;font-size:80%;"></i>' + json_rooms_list[aa].visitors_childs + ', '+ gks_lang('μέγιστο')+':' + json_rooms_list[aa].visitors_max);
      $('#dialog_room_room_id').val(json_rooms_list[aa].hotel_room_id);
      $('#dialog_room_room_id_result').html('');
      $('#dialog_room_num_adults').val(json_rooms_list[aa].rnum_adults);
      $('#dialog_room_num_childs').val(json_rooms_list[aa].rnum_childs);
      rchilds_ages_list_html(json_rooms_list[aa].rnum_childs,json_rooms_list[aa].rchilds_ages_list, true, false);
      
      if (dialog_room.room_type_child_kounies<=0) {
        $('#div_dialog_room_num_child_kounies').hide();
        $('#dialog_room_num_child_kounies').val('0');
      } else {
        prev_val=json_rooms_list[aa].rnum_child_kounies;
        $('#dialog_room_num_child_kounies option').each(function() {
          tmp_val=parseInt($(this).val());
          if (tmp_val>0) {
            if (tmp_val<=json_rooms_list[aa].room_type_child_kounies) $(this).show(); else $(this).hide();
          }
        });
        if ($('#dialog_room_num_child_kounies option[value=' + prev_val + ']').css('display')=='none') {
          $('#dialog_room_num_child_kounies').val(0);
        } else {
          $('#dialog_room_num_child_kounies').val(prev_val);
        }
        $('#dialog_room_num_child_kounies').show();
        $('#div_dialog_room_num_child_kounies').show();
      }
      if (dialog_room.room_type_extra_beds<=0) {
        $('#div_dialog_room_num_extra_beds').hide();
        $('#dialog_room_num_extra_beds').val('0');
      } else {
        prev_val=json_rooms_list[aa].rnum_extra_beds;
        $('#dialog_room_num_extra_beds option').each(function() {
          tmp_val=parseInt($(this).val());
          if (tmp_val>0) {
            if (tmp_val<=json_rooms_list[aa].room_type_extra_beds) $(this).show(); else $(this).hide();
          }
        });
        if ($('#dialog_room_num_extra_beds option[value=' + prev_val + ']').css('display')=='none') {
          $('#dialog_room_num_extra_beds').val(0);
        } else {
          $('#dialog_room_num_extra_beds').val(prev_val);
        }
        $('#dialog_room_num_extra_beds').show();
        $('#div_dialog_room_num_extra_beds').show();
      }

      $('#dialog_room_ajia_math').html('');
      $('#dialog_room_ajia_table').hide();
      num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
      if (num_days==0) {
        $('#dialog_room_price_per_item').val('');
      } else {
        $('#dialog_room_price_per_item').val((json_rooms_list[aa].ajia_total/num_days).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
      }
      $('#dialog_room_ajia_total').val(json_rooms_list[aa].ajia_total);
      $('#dialog_room_ekptosi_pososto').val(json_rooms_list[aa].gks_ekptosi_pososto);
      $('#dialog_room_sxolio').val(json_rooms_list[aa].rsxolio);
      celem=$('.gks_ekptosi_poso[data-aa=' + aa + ']');
      $('#dialog_room_gks_ekptosi_poso').html(celem.html());
      if (celem.css('display')!='none') $('#dialog_room_gks_ekptosi_poso').show(); else $('#dialog_room_gks_ekptosi_poso').hide();
      dialog_room_product_price_start_all_total=parseFloat($('.gks_price_final[data-aa=' + aa + ']').attr('data-product_price_start_all_total'));
      if (isNaN(dialog_room_product_price_start_all_total)) dialog_room_product_price_start_all_total=0;
      //console.log(dialog_room_product_price_start_all_total);
      
      
      $('#dialog_room_gks_nickname').val(json_rooms_list[aa].gks_nickname);
      if (json_rooms_list[aa].ruser_id>0) $('#dialog_room_user_id').val(json_rooms_list[aa].ruser_id); else $('#dialog_room_user_id').val('0');
      $('#dialog_room_user_first_name').val(json_rooms_list[aa].ruser_first_name);
      $('#dialog_room_user_last_name').val(json_rooms_list[aa].ruser_last_name);
      $('#dialog_room_user_email').val(json_rooms_list[aa].ruser_email);
      $('#dialog_room_user_mobile').val(json_rooms_list[aa].ruser_mobile);
      $('#dialog_room_user_lang').val(json_rooms_list[aa].ruser_lang);
      $('#dialog_room_user_ma_odos').val(json_rooms_list[aa].ruser_ma_odos);
      $('#dialog_room_user_ma_arithmos').val(json_rooms_list[aa].ruser_ma_arithmos);
      $('#dialog_room_user_ma_orofos').val(json_rooms_list[aa].ruser_ma_orofos);
      $('#dialog_room_user_ma_perioxi').val(json_rooms_list[aa].ruser_ma_perioxi);
      $('#dialog_room_user_ma_poli').val(json_rooms_list[aa].ruser_ma_poli);
      $('#dialog_room_user_ma_tk').val(json_rooms_list[aa].ruser_ma_tk);
      
      if (json_rooms_list[aa].ruser_ma_country_id > 0) {
        $('#dialog_room_user_ma_country_id').val(json_rooms_list[aa].ruser_ma_country_id);
         nomos_fill('dialog_room_user_ma_nomos_id',json_rooms_list[aa].ruser_ma_country_id,json_rooms_list[aa].ruser_ma_nomos_id);
      }
      $('#dialog_room_user_fiscal_position_id').val(json_rooms_list[aa].ruser_fiscal_position_id);
      $('#dialog_room_user_pricelist_id').val(json_rooms_list[aa].ruser_pricelist_id);
      
      
      
      
      
      extra_beds=parseInt($('#dialog_room_num_extra_beds').val());
			$('#dialog_room_num_adults').attr('max', (dialog_room.visitors     + extra_beds).toString());
		  $('#dialog_room_num_childs').attr('max', (dialog_room.visitors_max + extra_beds).toString());
		  
		  if (json_rooms_list[aa].ruser_id == -1) {
		    $('#selecttype0').prop('checked', true);
        $('.selecttypediv').each(function() {
          $(this).hide();
        });	
        $('.dialog_room_pelatistype').each(function() {
          $(this).prop("disabled", false);
        });        	    
		  } else if (json_rooms_list[aa].ruser_id == 0) {
		    $('#selecttype1').prop('checked', true);
        $('.selecttypediv').each(function() {
          $(this).show();
        });		    
        $('.dialog_room_pelatistype').each(function() {
          $(this).prop("disabled", false);
        });        	    
		  } else {
		    $('#selecttype1').prop('checked', true);
        $('.selecttypediv').each(function() {
          $(this).show();
        });	
        $('.dialog_room_pelatistype').each(function() {
          $(this).prop("disabled", true);
        });        	    
		  }
		  
		  
		  dialog_room_ajia_table_html=$('.gks_price_final[data-aa=' + aa + ']').attr('data-ajia_table_html');
  		dialog_room_ajia_table_math=$('.gks_price_final[data-aa=' + aa + ']').attr('data-ajia_table_math');
  		dialog_room_other_taxes_tooltip=$('.gks_price_final[data-aa=' + aa + ']').attr('data-other_taxes_tooltip');
		  
		  if (dialog_room_ajia_table_html === undefined || dialog_room_ajia_table_html === null) dialog_room_ajia_table_html='';
		  if (dialog_room_ajia_table_math === undefined || dialog_room_ajia_table_math === null) dialog_room_ajia_table_math='';
		  if (dialog_room_other_taxes_tooltip === undefined || dialog_room_other_taxes_tooltip === null) dialog_room_other_taxes_tooltip='';
		  
		  
		  if ($('#dialog_room_ajia_table').hasClass('tooltipstered')) $('#dialog_room_ajia_table').tooltipster('destroy');
		  
		  content_html='';
		  if (dialog_room_ajia_table_html !='') {
		    content_html+='<div style="text-align:center"><b>'+gks_lang('Ανάλυση ανά μέρα')+'</b><br>' + $.base64.decode(dialog_room_ajia_table_math) + '</div>';
		    content_html+='<div style="text-align:center">' + $.base64.decode(dialog_room_ajia_table_html) + '</div>';
		  }
		  if (dialog_room_other_taxes_tooltip !='') 
		    content_html+='<div style="text-align:center"><b>'+gks_lang('Λοιποί φόροι, τέλη κτλ.')+'</b><br>' + $.base64.decode(dialog_room_other_taxes_tooltip) + '</div>';

		  if (content_html != '') {
  		  $('#dialog_room_ajia_table').show();
  		  $('#dialog_room_ajia_table').tooltipster({
    					    theme: 'tooltipster-noir',
    					    contentAsHTML: true,
    					    interactive:true,
    					    content: content_html,
    		});
      }
      
      $('#dialog_room_ajia_math').html($.base64.decode(dialog_room_ajia_table_math));
      
      
      
    }
    
    
  

      
    
    
	  dwidth=$(window).width() * 0.98;
	  dheight=$(window).height() * 0.98;
	  if (dwidth> 1200) dwidth=1200;
    wwidth=$(window).width();
    if (wwidth>=768) {
  	  //if (dheight> 960) dheight=960;
  	  //if (dialog_room_div_customer_more==0 && dheight > 631) dheight=631;
  	} else {
  	  ////if (dheight > 960) dheight=960;
  	  ////if (dialog_room_div_customer_more==0) dheight=500;  	  
  	}
	  dialog_room.dialog('option', 'width', dwidth);
	  dialog_room.dialog('option', 'height', dheight)      
    $('#dialog_room').parent().css({position:'fixed'});
    dialog_room.dialog('open');

    
  };
  
  function dialog_room_ekptosi_pososto_change() {
    val1=parseFloat($('#dialog_room_ekptosi_pososto').val());
    if (isNaN(val1)) val1=0;
    val2=(dialog_room_product_price_start_all_total*val1/100.0).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    val3=dialog_room_product_price_start_all_total - val2;
    $('#dialog_room_ajia_total').val(val3);
    
    num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
    if (num_days==0) {
      $('#dialog_room_price_per_item').val('');
    } else {
      $('#dialog_room_price_per_item').val((val3/num_days).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
    }
    
    $('#dialog_room_gks_ekptosi_poso').html(val2.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND));
    if (val2==0) $('#dialog_room_gks_ekptosi_poso').hide(); else $('#dialog_room_gks_ekptosi_poso').show();
    fields_change[dialog_room.aa]='gks_ekptosi';
    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
    //mysubmit('','calc_dialog_room', 'gks_price_final', dialog_room.aa, '', '');
  }
  $('#dialog_room_ekptosi_pososto').on(mychange, dialog_room_ekptosi_pososto_change);

  function dialog_room_price_per_item_change() {
    val1=parseFloat($('#dialog_room_price_per_item').val());
    if (isNaN(val1)) val1=0;
    num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
    
    $('#dialog_room_ajia_total').val(val1*num_days);
    
    fields_change[dialog_room.aa]='gks_price_final';
    mysubmit('','calc_dialog_room', 'gks_price_final', dialog_room.aa, '', '');
  }
  $('#dialog_room_price_per_item').on(mychange, dialog_room_price_per_item_change);  
  

  
  function dialog_room_ajia_total_change() {
    val1=parseFloat($('#dialog_room_ajia_total').val());
    if (isNaN(val1)) val1=0;
    if (dialog_room_product_price_start_all_total==0) {
      $('#dialog_room_gks_ekptosi_poso').hide();
    }
    val2=(dialog_room_product_price_start_all_total-val1)/dialog_room_product_price_start_all_total;
    val2=(100 * val2).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $('#dialog_room_ekptosi_pososto').val(val2);

    val2=dialog_room_product_price_start_all_total-val1;
    $('#dialog_room_gks_ekptosi_poso').html(val2.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND));
    if (val2==0) $('#dialog_room_gks_ekptosi_poso').hide(); else $('#dialog_room_gks_ekptosi_poso').show();

    num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
    if (num_days==0) {
      $('#dialog_room_price_per_item').val('');
    } else {
      $('#dialog_room_price_per_item').val((val1/num_days).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
    }
      
      
    fields_change[dialog_room.aa]='gks_price_final';
    mysubmit('','calc_dialog_room', 'gks_price_final', dialog_room.aa, '', '');
  }
  $('#dialog_room_ajia_total').on(mychange, dialog_room_ajia_total_change);  
  
  
  
  $('#addroom').click(function(event) {  
    myroomedit(-1);
  });
  $('#addroom2').click(function(event) {  
    event.stopPropagation();
    myroomedit(-1);
  });
  
  editiconroom_click = function(event) {  
    aa = parseInt($(this).attr('data-aa'));
    myroomedit(aa);
  }
  $('.editiconroom').click(editiconroom_click);
  
  
  deleteitem_click = function(event) {
    aa = parseInt($(this).attr('data-aa'));
    myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε το δωμάτιο;'),'deleteitem_this',aa);

  }
  $('.deleteitem').click(deleteitem_click);
  
  window.deleteitem_this = function(aa) {
    json_rooms_list[aa].delete=1;
    $('#tableroomlist tbody tr[data-aa=' + aa + ']').remove();
    calc_pliroteo();
  }
  
  
  $('#dialog_room_gks_nickname').autocomplete({
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
      $('#dialog_room_user_id').val(ui.item.id);
      datasend='';
      datasend+='&id=' + ui.item.id;
      $.ajax({
  			url: '/my/admin-autocomplete-user-data.php',
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
  					  $('#dialog_room_user_first_name').val(data.udata.first_name);
  					  $('#dialog_room_user_last_name').val(data.udata.last_name);
  					  $('#dialog_room_user_email').val(data.udata.user_email);
  					  $('#dialog_room_user_mobile').val(data.udata.moobile);
  					  $('#dialog_room_user_lang').val(data.udata.lang);
  					  $('#dialog_room_user_ma_odos').val(data.udata.odos);
  					  $('#dialog_room_user_ma_arithmos').val(data.udata.arithmos);
  					  $('#dialog_room_user_ma_orofos').val(data.udata.orofos);
  					  $('#dialog_room_user_ma_perioxi').val(data.udata.perioxi);
  					  $('#dialog_room_user_ma_poli').val(data.udata.poli);
  					  $('#dialog_room_user_ma_tk').val(data.udata.tk);
  					  $('#dialog_room_user_ma_country_id').val(data.udata.country_id);
  					  nomos_fill('dialog_room_user_ma_nomos_id',data.udata.country_id,data.udata.nomos_id);
  					  //$('#user_ma_nomos_id').val(data.udata.nomos_id);
  					  $('#dialog_room_user_fiscal_position_id').val(data.udata.fiscal_position_id);
  					  $('#dialog_room_user_pricelist_id').val(data.udata.pricelist_id);

              $('.dialog_room_pelatistype').each(function() {
                $(this).prop("disabled", true);
              });  					  
 
  					  //console.log(data);

  					  
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
      });           
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#dialog_room_gks_nickname').val('');
        $('#dialog_room_user_id').val('');
        $('.dialog_room_pelatistype').each(function() {
          $(this).prop("disabled", false);
        });        
   
      }
    }
  });
  
  
  $('#dialog_room_user_ma_country_id').each(function() {
    $(this).append('<option value="0"></option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'" data-ee="'+gks_country[i].country_ee+'">' + gks_country[i].country_name + '</option>');
    }   
  });
    
  $('#dialog_room_user_ma_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('dialog_room_user_ma_nomos_id',v,0);
  });

  $('#dialog_room_customer_more_show').click(function() {
    $('#dialog_room_div_customer_more_show').hide();
    $('.dialog_room_divs_customer_more').each(function() {
      $(this).show(500);  
    }); 
    $('#dialog_room_div_customer_more_hide').show(500);
    
    if ($(window).width()>=768) {
  	  var dheighta=$(window).height() * 0.98;
  	  if (dheighta> 960) dheighta=960;
      dialog_room.dialog("widget").animate({
          height: dheighta
      }, {
          duration: 500,
          step: function (now, tween) {
            dialog_room.dialog("option", "height", now);
          }
      });    
    } 
  });
  $('#dialog_room_customer_more_hide').click(function() {
    $('#dialog_room_div_customer_more_hide').hide();
    $('.dialog_room_divs_customer_more').each(function() {
      $(this).hide(500);  
    }); 
    $('#dialog_room_div_customer_more_show').show(500);
    
//    if ($(window).width()>=768) {
//  	  var dheighta=$(window).height() * 0.98;
//  	  if (dheighta> 631) dheighta=631;
//      dialog_room.dialog("widget").animate({
//          height: dheighta
//      }, {
//          duration: 500,
//          step: function (now, tween) {
//            dialog_room.dialog("option", "height", now);
//          }
//      });
//    }
  });


  
  function not_in_rooms() {
    myout='';
    for (i = 0; i < json_rooms_list.length; i++) {
      if (dialog_room.aa != i && json_rooms_list[i].delete == 0) {
        myout+=json_rooms_list[i].hotel_room_id.toString().trim() + '|';
      }
    }
    if (myout.length>0) myout=myout.substring(0, myout.length-1);
    //console.log(myout.trim());
    return myout;
  }
  
  var dialog_room_ajia_table_math='';
  var dialog_room_ajia_table_html='';
  var dialog_room_ajia_table_array='';
  var dialog_room_other_taxes_tooltip='';
  
  function dialog_room_room_descr_autocomplete_rchilds_ages_list() {
    var tmp_rchilds_ages_list=[];
    $('.rchilds_ages_list_item').each(function() {
      ci=parseInt($(this).val());
      if (isNaN(ci)) ci=-1;
      if (ci<0) has_one_unselected=true;
      ca=parseInt($(this).find('option:selected').attr('data-age'));
      if (isNaN(ca)) ca=-1;
      if (ci > 0) tmp_rchilds_ages_list.push({index: ci, age: ca});
    });
    return tmp_rchilds_ages_list;   
  }
  
  $('#dialog_room_room_descr').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        rsvid: from_php_id,
        showtype:1,
        showfloor:1,
        free: 1,
        id_hotel: encodeURIComponent($('#mypostform #hotel_id').val().trim()),
        check_in: encodeURIComponent($('#mypostform #check_in').val().trim()),
        check_out: encodeURIComponent($('#mypostform #check_out').val().trim()),
        mynotin: not_in_rooms(),
      };
      $.ajax({
        url: 'admin-autocomplete-hotel-room.php',
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
      $('#dialog_room_room_id').val(ui.item.id);
      $('#room_type_descr').val(ui.item.room_type_descr);

      
      datasend='';
      datasend+='&id_hotel=' + encodeURIComponent($('#mypostform #hotel_id').val().trim());
      datasend+='&roomid=' + ui.item.id;
      datasend+='&rsvid=' + from_php_id;
      datasend+='&check_in=' + encodeURIComponent($('#mypostform #check_in').val().trim());
      datasend+='&check_out='  + encodeURIComponent($('#mypostform #check_out').val().trim())
      datasend+='&rnum_adults='  + encodeURIComponent($('#dialog_room_num_adults').val().trim())
      datasend+='&rnum_childs='  + encodeURIComponent($('#dialog_room_num_childs').val().trim())
      datasend+='&rchilds_ages_list='  + encodeURIComponent($.base64.encode(JSON.stringify(dialog_room_room_descr_autocomplete_rchilds_ages_list())))
      
      $.ajax({
  			url: '/my/admin-hotel-reservation-item-room-select.php',
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
  					  dialog_room_select_other_room=true;
  					  
  					  $('#dialog_room_room_id').val(data.room_id);
  					  $('#dialog_room_room_descr').val($.base64.decode(data.room_descr));
  					  $('#dialog_room_room_type_descr').val($.base64.decode(data.room_type_descr));
  					  $('#dialog_room_room_type_visitors_html').html('<i class="fa fa-male" style="color:#aaaaaa;"></i>' + data.visitors + ' ' + '<i class="fa fa-child" style="color:#aaaaaa;font-size:80%;"></i>' + data.visitors_childs + ', '+gks_lang('μέγιστο')+':' + data.visitors_max);

  					  $('#dialog_room_room_id_result').html($.base64.decode(data.msg_aval));
  					  $('#dialog_room_ajia_math').html($.base64.decode(data.msg_price));
  					  
  					  $('#dialog_room_ajia_total').val(data.ajia_total_val);

              num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
              if (num_days==0) {
                $('#dialog_room_price_per_item').val('');
              } else {
                $('#dialog_room_price_per_item').val((data.ajia_total_val/num_days).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
              }  					  
  					  
  					  //$('#dialog_room_ekptosi_pososto').val(0);
  					  
  					  dialog_room.visitors = data.visitors;
  					  dialog_room.visitors_childs = data.visitors_childs;
  					  dialog_room.visitors_max = data.visitors_max;
  					  //console.log('dialog_room.visitors' + dialog_room.visitors);
  					  dialog_room.room_type_child_kounies=data.room_type_child_kounies;
              dialog_room.room_type_extra_beds=data.room_type_extra_beds;
  					  
  					  
              if (dialog_room.room_type_child_kounies<=0) {
                $('#div_dialog_room_num_child_kounies').hide();
                $('#dialog_room_num_child_kounies').val('0');
              } else {
                prev_val=parseInt($('#dialog_room_num_child_kounies').val());
                $('#dialog_room_num_child_kounies option').each(function() {
                  tmp_val=parseInt($(this).val());
                  if (tmp_val>0) {
                    if (tmp_val<=dialog_room.room_type_child_kounies) $(this).show(); else $(this).hide();
                  }
                });
                if ($('#dialog_room_num_child_kounies option[value=' + prev_val + ']').css('display')=='none') {
                  $('#dialog_room_num_child_kounies').val(0);
                } else {
                  $('#dialog_room_num_child_kounies').val(prev_val);
                }
                $('#dialog_room_num_child_kounies').show();
                $('#div_dialog_room_num_child_kounies').show();
              }
              if (dialog_room.room_type_extra_beds<=0) {
                $('#div_dialog_room_num_extra_beds').hide();
                $('#dialog_room_num_extra_beds').val('0');
              } else {
                prev_val=parseInt($('#dialog_room_num_extra_beds').val());
                $('#dialog_room_num_extra_beds option').each(function() {
                  tmp_val=parseInt($(this).val());
                  if (tmp_val>0) {
                    if (tmp_val<=dialog_room.room_type_extra_beds) $(this).show(); else $(this).hide();
                  }
                });
                if ($('#dialog_room_num_extra_beds option[value=' + prev_val + ']').css('display')=='none') {
                  $('#dialog_room_num_extra_beds').val(0);
                } else {
                  $('#dialog_room_num_extra_beds').val(prev_val);
                }
                $('#dialog_room_num_extra_beds').show();
                $('#div_dialog_room_num_extra_beds').show();
              }
              
                					  
  					  old_adults = myparseInt($('#dialog_room_num_adults').val());
  					  old_childs = myparseInt($('#dialog_room_num_childs').val());
  					  
  					  if (old_adults > dialog_room.visitors) old_adults=dialog_room.visitors;
  					  if (old_childs > dialog_room.visitors_childs) old_childs=dialog_room.visitors_childs;
  					  
  					  if ((old_adults + old_childs) > data.visitors_max) old_childs=data.visitors_max-old_adults;
  					  if (old_childs<0) old_childs=0; 
  					    
  					  $('#dialog_room_num_adults').val((old_adults).toString());
  					  $('#dialog_room_num_childs').val((old_childs).toString());
  					  
  					  
  					  extra_beds=parseInt($('#dialog_room_num_extra_beds').val());
  					  $('#dialog_room_num_adults').attr('max', (dialog_room.visitors     + extra_beds).toString());
  					  $('#dialog_room_num_childs').attr('max', (dialog_room.visitors_max + extra_beds).toString());
  					  
  					  rnum_childs=myparseInt($('#dialog_room_num_childs').val());
  					  rchilds_ages_list_html(rnum_childs,dialog_room.rchilds_ages_list, false, true);

  					  if ($('#dialog_room_ajia_table').hasClass('tooltipstered')) $('#dialog_room_ajia_table').tooltipster('destroy');
  					  
  					  
                    

  					  
  					  
  					  
//  					  $('#dialog_room_ajia_table').show();
//  					  $('#dialog_room_ajia_table').tooltipster({
//  					    theme: 'tooltipster-noir',
//  					    contentAsHTML: true,
//  					    interactive:true,
//  					    content: data.roomaf_html,
//  					  });
  					  //console.log(data.roomaf_html);
  					  //dialog_room_ajia_table_math=$.base64.decode(data.msg_price);
  					  //dialog_room_ajia_table_html=data.roomaf_html;
  					  //dialog_room_ajia_table_array=data.roomaf_array;
  					  //dialog_room_other_taxes_tooltip=data.roomaf_other_taxes_tooltip;
  					  
  					  dialog_room_product_price_start_all_total=data.ajia_total_val;
  					  dialog_room_ekptosi_pososto_change();
  					  
  					  // ginete klisi apo to dialog_room_ekptosi_pososto_change
  					  //mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
  					  
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
      });       
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#dialog_room_room_descr').val('');
        $('#dialog_room_room_type_descr').val('');
        $('#dialog_room_room_type_visitors_html').html('');
        $('#dialog_room_room_id').val('');
        $('#dialog_room_room_id_result').html('');
        $('#dialog_room_ajia_math').html('');
        $('#dialog_room_ajia_table').hide();
        $('#dialog_room_gks_ekptosi_poso').hide();
        $('#dialog_room_price_per_item').val('');
        $('#dialog_room_ajia_total').val('');

        $('#div_dialog_room_num_child_kounies').hide();
        $('#dialog_room_num_child_kounies').val(0);
        $('#div_dialog_room_num_extra_beds').hide();
        $('#dialog_room_num_extra_beds').val(0);
        
        dialog_room_product_price_start_all_total=0;
      } 
    }
  });

  
  $('#selecttype0').change(function() {
    $('.selecttypediv').each(function() {
      $(this).hide();
    });
  });
  $('#selecttype1').change(function() {
    $('.selecttypediv').each(function() {
      $(this).show();
    });
  });


 
  //user
  
  $('#user').autocomplete({
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
      need_save=true;
      old_val=$('#user_id').val();
      $('#user_id').val(ui.item.id);
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();
      $('#user_save').hide();

      
      
      gks_admin_get_user_data(ui.item.id, false);
    
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#user').val('');
        $('#user_id').val('');
        $('#autocomplete_user_id').hide(); 
        $('#balance_user_before').html((0).mymoney()).attr('data-val','0');
        $('#balance_user_after').html((0).mymoney());
        balance_user_after_calc();         
        $('#user_save').show();
        $('#div_pelati_sxolio').hide('fade', 'slow');
        $('#text_pelati_sxolio').html('');
                        
        $('#div_order_sxolio').hide('fade', 'slow');
        $('#text_order_sxolio').html('');   
           
        $('#dr_user_first_name').val('');
        $('#dr_user_last_name').val('');
        $('#dr_user_email').val('');
        $('#dr_user_mobile').val('');
        $('#dr_user_lang').val('el-GR');
        $('#dr_user_ma_odos').val('');
        $('#dr_user_ma_arithmos').val('');
        $('#dr_user_ma_orofos').val('');
        $('#dr_user_ma_perioxi').val('');
        $('#dr_user_ma_poli').val('');
        $('#dr_user_ma_tk').val('');
        
        $('#dr_user_eponimia').val('');
        $('#dr_user_title').val('');
        $('#dr_user_afm').val('');
        $('#dr_user_doy').val('');
        $('#dr_user_epaggelma').val('');
        
        
        $('#form_parastatiko_apodiji').click();
        
        $('#fiscal_position_id').val(1);
        $('#pricelist_id').val(1);
        //$('#def_ekptosi').val(0);  
        def_ekptosi_set_pre_set(0); 
             
        gks_myscroll(); 
        calc_pliroteo();             
      }
    }
  });  

  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
      
    datasend='cmd=get&id=' + user_id + '&reservation_id=' + from_php_id + '&page=reservation';
    $.ajax({
			url: 'admin-get-user-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_dialog_gsis_result:dialog_gsis_result,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log('gks_admin_get_user_data res');
					  //console.log(data);
					  //console.log(this.gks_dialog_gsis_result);
					  
            if (data.pelati_sxolio=='') {
              $('#div_pelati_sxolio').hide('fade', 'slow');
              $('#text_pelati_sxolio').html('');
            } else {
              $('#div_pelati_sxolio').show('fade', 'slow');
              $('#text_pelati_sxolio').html(data.pelati_sxolio);
            }
            if (data.order_sxolio=='') {
              $('#div_order_sxolio').hide('fade', 'slow');
              $('#text_order_sxolio').html('');
            } else {
              $('#div_order_sxolio').show('fade', 'slow');
              $('#text_order_sxolio').html(data.order_sxolio);
              
              mytext=$('#text_order_sxolio').text();
              exit_text=$('#note_production').val();
              if (exit_text!='') exit_text+="\r\n";
              exit_text+=mytext;
              $('#note_production').val(exit_text);
              //$('#note_production').focus();
            }
            
            $('#dr_user_first_name').val(data.first_name);
            $('#dr_user_last_name').val(data.last_name);
            $('#dr_user_email').val(data.email);
            $('#dr_user_mobile').val(data.mobile);
            $('#dr_user_lang').val(data.lang);
            $('#dr_user_ma_orofos').val(data.ma_orofos);
            $('#dr_user_ma_perioxi').val(data.ma_perioxi);
            $('#pricelist_id').val(data.pricelist_id);
            //$('#def_ekptosi').val(data.generic_ekprosi);            
            def_ekptosi_set_pre_set(data.generic_ekprosi);
                   
            if (this.gks_dialog_gsis_result === false) {
              //console.log('gks_dialog_gsis_result false');
              $('#dr_user_ma_odos').val(data.ma_odos);
              $('#dr_user_ma_arithmos').val(data.ma_arithmos);
              
              $('#dr_user_ma_poli').val(data.ma_poli);
              $('#dr_user_ma_tk').val(data.ma_tk);
              $('#dr_user_ma_country_id').val(data.ma_country_id);
              nomos_fill('dr_user_ma_nomos_id',data.ma_country_id,data.ma_nomos_id);
              dr_user_ma_country_id_change();
              
              
              
              $('#dr_user_eponimia').val(data.eponimia);
              $('#dr_user_title').val(data.title);
              $('#dr_user_afm').val(data.afm);
              $('#dr_user_doy').val(data.doy);
              $('#dr_user_epaggelma').val(data.epaggelma);
              
              $('#fiscal_position_id').val(data.fiscal_position_id);
              
              if (data.parastatiko==0) 
                $('#form_parastatiko_apodiji').click();
              else 
                $('#form_parastatiko_timologio').click();
              
   
                         
            } else {
              //console.log('gks_dialog_gsis_result true');
      				mynymber=this.gks_dialog_gsis_result.basic_rec.postal_address_no.trim();
      				if (mynymber=='0') mynymber='';
      				              
              $('#dr_user_ma_odos').val(this.gks_dialog_gsis_result.basic_rec.postal_address.trim());
              $('#dr_user_ma_arithmos').val(mynymber.trim());
              
              $('#dr_user_ma_poli').val(this.gks_dialog_gsis_result.basic_rec.postal_area_description);
              $('#dr_user_ma_tk').val(this.gks_dialog_gsis_result.basic_rec.postal_zip_code);
              $('#dr_user_ma_country_id').val(91);
              nomos_fill('dr_user_ma_nomos_id',91,data.ma_nomos_id);
              dr_user_ma_country_id_change();
              
              
              
              $('#dr_user_eponimia').val(this.gks_dialog_gsis_result.basic_rec.onomasia);
              $('#dr_user_title').val(this.gks_dialog_gsis_result.basic_rec.commer_title);
              $('#dr_user_afm').val(this.gks_dialog_gsis_result.basic_rec.afm);
              $('#dr_user_doy').val(this.gks_dialog_gsis_result.basic_rec.doy_descr);
              $('#dr_user_epaggelma').val('');
              for (i=0;i < this.gks_dialog_gsis_result.firm_act_tab.length; i++) {
                if (this.gks_dialog_gsis_result.firm_act_tab[i].kind=='1') {
                  $('#dr_user_epaggelma').val(this.gks_dialog_gsis_result.firm_act_tab[i].cdescr);
                  break;
                }
              }
              
              if (this.gks_dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
                $('#fiscal_position_id').val(11);
                $('#form_parastatiko_timologio').click();
              } else {
                $('#fiscal_position_id').val(1);
                $('#form_parastatiko_apodiji').click();
              }
            }
            
            //$('#div_pelati_acc_type_descr').html(data.acc_type_descr);
            
            def_ekptosi_set();

            $('#balance_user_before').html(data.balance_user_before.mymoney()).attr('data-val',data.balance_user_before);
            balance_user_after_calc();            
            
            gks_myscroll();
            calc_pliroteo(); 
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
  }   



  $('#coupon_use').click(function(event){	
    mycoupon=$('#input_coupon').val();
    if (mycoupon.trim() == '') {
      myalert('error:'+gks_lang('Πληκτρολογήστε πρώτα το κουπόνι στο πλαίσιο κειμένου'));
      return; 
    }
    
    //calc_pliroteo(field_name='', field_aa=-1, mycmd='', myfile='')
    calc_pliroteo('',-1,'couponadd', mycoupon);
    //basket_edit(true,true,false, 'couponadd', 0, 0, 0, mycoupon, 0, false);
    
  });
  
  coupon_delete_click = function(event){	
    mycoupon=$(this).attr('data-coupon');
    //myalert('error:' + mycoupon);
    calc_pliroteo('',-1,'coupondelete', mycoupon);
    //basket_edit(true,true,false, 'coupondelete', 0, 0, 0, mycoupon, 0, false);
  }
  
  $('.coupon_delete').click(coupon_delete_click);
   
  function gks_coupon_item_click() {	
    if ($(this).hasClass('gks_coupon_item_disabled')==false) return;
    
    field_aa=parseInt($(this).attr('data-aa'));
    if (isNaN(field_aa)) return;
    fields_change[field_aa]='';
    calc_pliroteo();
    
    //console.log($(this).attr('data-aa'));
    
  }
  $('.gks_coupon_item').click(gks_coupon_item_click);
  
  $('#def_ekptosi').on(mychange, function() {
    //console.log('def_ekptosi');
    def_ekptosi_set();
    calc_pliroteo();
  });

  function def_ekptosi_set_pre_set(new_val) {
    exist_val=parseFloat($('#def_ekptosi').val());
    if (isNaN(exist_val)) exist_val=0;
    //if (exist_val!=new_val) 
    
    $('#def_ekptosi').val(new_val);
    
  }
  $('#def_ekptosi_set').click(function() {
    def_ekptosi_set();
    calc_pliroteo();
  });
    
  function def_ekptosi_set() {
    def_ekptosi=parseFloat($('#def_ekptosi').val());
    if (isNaN(def_ekptosi)) def_ekptosi=0;


    $('.gks_ekptosi_pososto').each(function() {
      field_aa=parseInt($(this).attr('data-aa'));
      if (isNaN(field_aa)) field_aa=-1;
      if (field_aa>=0) {     
        celem=$('.gks_coupon_item[data-aa=' + field_aa + ']');
        will_set=false;
        if (celem.css('display')=='none') {
          will_set=true;
        } else {
          if (celem.hasClass('gks_coupon_item_disabled')) {
            will_set=true;
          }
        }
        if (will_set) {
          $(this).val(def_ekptosi);
          fields_change[field_aa]='gks_ekptosi';
        }
      }
    });
    
  }


  $('#dr_user_ma_country_id').each(function() {
    dbval=parseInt($(this).attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0">'+gks_lang('Χώρα')+'...</option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'" data-ee="'+gks_country[i].country_ee+'">' + gks_country[i].country_name + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  });
  

  $('#dr_user_ma_country_id').change(function() {
    dr_user_ma_country_id_change();
    var v=$('#dr_user_ma_country_id').val();
    nomos_fill('dr_user_ma_nomos_id',v,0);
    calc_pliroteo();    
  });
  
  
  function dr_user_ma_country_id_change() {
    var v=$('#dr_user_ma_country_id').val();
    
    data_ee=$('#dr_user_ma_country_id').find('OPTION[value=' + v + ']').attr('data-ee');
    $('#dr_user_afm_views_run').hide();
    if (data_ee=='') {
      $('#dr_user_afm_ee_initials').hide().html('');
      $('#dr_user_afm').css('width','100%').removeClass('dr_user_afm_views');
    } else {
      $('#dr_user_afm_ee_initials').show().html(data_ee);
      $('#dr_user_afm').css('width','calc(100% - 75px)').addClass('dr_user_afm_views');
    }
  }

  $('input[name=form_parastatiko]').click(function() {
    if ($(this).val() ==0) {
      $('#div_parastatiko_timologio').hide();  
    } else {
      $('#div_parastatiko_timologio').show();
    }
    need_save=true;
    gks_myscroll();
    calc_pliroteo();
  });  

  $('#dr_user_afm').change(function() {
    calc_pliroteo();
  });

  $('#dr_user_afm').on('input keyup paste', function() {
    $('#dr_user_afm_views_run').hide();
  });
  

  var dialog_gsis;
  var dialog_gsis_result=false;
  dialog_gsis = $('#dialog_gsis').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: 'dialog_gsis_ok',
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Κράτησης'),
        //icon: 'ui-icon-circle-plus',
        click: function() {
          
          
          if (dialog_gsis_result.user_id>0) {
            $('#user_id').val(dialog_gsis_result.user_id);
            $('#user').val(dialog_gsis_result.gks_nickname);
            $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + dialog_gsis_result.user_id);
            $('#user_save').hide();
            
            gks_admin_get_user_data(dialog_gsis_result.user_id, dialog_gsis_result);
                      
          } else {
            $('#user_id').val('');
            $('#user').val('');
            $('#autocomplete_user_id').hide();
            $('#user_save').show();
            
            
            $('#dr_user_first_name').val('');
            $('#dr_user_last_name').val('');
            if (dialog_gsis_result.basic_rec.i_ni_flag_descr =='ΦΠ') {
              var onomasia_parts = dialog_gsis_result.basic_rec.onomasia.split(' ');
              if (onomasia_parts.length>=2) {
                $('#dr_user_first_name').val(onomasia_parts[1].trim());
                $('#dr_user_last_name').val(onomasia_parts[0].trim());
              }
            }
            $('#dr_user_email').val('');
            $('#dr_user_mobile').val('');
            $('#dr_user_lang').val('el-GR');
  
                       
  
    				$('#dr_user_eponimia').val(dialog_gsis_result.basic_rec.onomasia);
    				$('#dr_user_title').val(dialog_gsis_result.basic_rec.commer_title);
    				$('#dr_user_afm').val(dialog_gsis_result.basic_rec.afm);
    				$('#dr_user_doy').val(dialog_gsis_result.basic_rec.doy_descr);
            $('#dr_user_epaggelma').val('');
            for (i=0;i < dialog_gsis_result.firm_act_tab.length; i++) {
              if (dialog_gsis_result.firm_act_tab[i].kind=='1') {
                $('#dr_user_epaggelma').val(dialog_gsis_result.firm_act_tab[i].cdescr);
                break;
              }
            }
    				mynymber=dialog_gsis_result.basic_rec.postal_address_no.trim();
    				if (mynymber=='0') mynymber='';
    				$('#dr_user_ma_odos').val(dialog_gsis_result.basic_rec.postal_address.trim());
    				$('#dr_user_ma_arithmos').val(mynymber);
            $('#dr_user_ma_orofos').val('');
            $('#dr_user_ma_perioxi').val('');
    				$('#dr_user_ma_poli').val(dialog_gsis_result.basic_rec.postal_area_description);
    				$('#dr_user_ma_tk').val(dialog_gsis_result.basic_rec.postal_zip_code);
            $('#dr_user_ma_country_id').val(91);
            $('#dr_user_ma_nomos_id').val('0');
            dr_user_ma_country_id_change();
            
            
            
            $('#form_select_apostoli option').each(function() { 
              if ($(this).attr('value') > 0 ) {
                $(this).remove();
              }
            });
            $('#form_select_apostoli').val(-1);
            $('#pricelist_id').val(1);
            //$('#def_ekptosi').val(0);
            if (dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
              $('#fiscal_position_id').val(11);
              $('#form_parastatiko_timologio').click();
            } else {
              $('#fiscal_position_id').val(1);
              $('#form_parastatiko_apodiji').click();
            }

            
            
            //def_ekptosi_set();
            gks_myscroll();
            calc_pliroteo();
          }
          
          $( this ).dialog('close');
        }
        //showText: false
      },
      {
        id: 'dialog_gsis_cancel',
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: 'ui-icon-cancel',
        click: function() {
          $( this ).dialog('close');
        }
        //showText: false
      },      
    ]
        

  });



  $('#btn_gsis_get').click(function() {
    //console.log('btn_gsis_get');
    if ($('input[name=form_parastatiko]:checked').val() == '1' && $('#dr_user_ma_country_id').val() == 91) {
      $('#dialog_gsis_afm').val($('#dr_user_afm').val());
    } else {
      $('#dialog_gsis_afm').val('');    
    }
    $('#dialog_gsis_html').html('');
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  //if (dheight> 600) dheight=600;
	  dialog_gsis.dialog('option', 'width', dwidth);
	  dialog_gsis.dialog('option', 'height', dheight);
	  $('#dialog_gsis').parent().css({position:'fixed'});    
    dialog_gsis.dialog('open');    
    $('#dialog_gsis_ok').button('option','disabled', true);
    
  });

  
  $('#dialog_gsis_run').click(function() {
    //console.log('dialog_gsis_run');
    dialog_gsis_result=false;
    
    dialog_gsis_afm=$('#dialog_gsis_afm').val().trim();
    if (dialog_gsis_afm=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το ΑΦΜ'));
      return;  
    }
    
    $('#dialog_gsis_ok').button('option','disabled', true);
    $('#dialog_gsis_html').html('');
    
    v=$('#hotel_id option:selected').attr('data-company_id_sub_id');
    company_id=0;
    company_sub_id=0;
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
        
    datasend='afm=' + dialog_gsis_afm + '&company_id=' + company_id + '&force=1';;

    $('body').addClass('myloading');
    $.ajax({
			url: '/my/admin-get-gisis.php',
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
				//console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  dialog_gsis_result=data.out;
					  
  					outhtml='<p style="text-align:center;font-size: 120%;font-weight: bold;">'+gks_lang('Αποτελέσματα')+'</p>';
  					
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
  					$('#dialog_gsis_ok').button('option','disabled', false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
		    
    
    
  });
  

  var dialog_user_save;
  dialog_user_save = $('#dialog_user_save').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: 'dialog_user_save_ok',
        text: gks_lang('Προσθήκη ή επιλογή χρήστη'),
        icon: 'ui-icon-circle-plus',
        click: function() {
          
          sel_elem=$('input[name=dialog_user_save_radio]:checked');
          if (sel_elem.length==0) {
            myalert('error:'+gks_lang('Επιλέξτε ή την προσθήκη νέου χρήστη ή έναν υπάρχον χρήστη'));
            return;  
          }
          select_user_id=sel_elem.val();
          //console.log(select_user_id);
          datasend=dialog_user_save.datasend;
          datasend+='&force=1&select_user_id=' + select_user_id;
          //console.log(datasend);
          
          $('body').addClass('myloading');
          $.ajax({
      			url: '/my/admin-users-add-exec.php',
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
                  $('#user_id').val(data.user_id);
                  $('#user').val(data.gks_nickname);
                  $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
                  $('#user_save').hide();
                  dialog_user_save.dialog('close');     					  
      					} else {
      					  myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      		});
      					            
          
          //$(this).dialog( "close" );
        }
        //showText: false
      },
      {
        id: 'dialog_user_save_cancel',
        text: gks_lang('Άκυρο'),
        icon: 'ui-icon-cancel',
        click: function() {
          $( this ).dialog('close');
        }
        //showText: false
      },      
    ]
        

  });
  
  $('#user_save').click(function() {
    
    datasend='';    
    datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($('#dr_user_first_name').val().trim()));
    datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($('#dr_user_last_name').val().trim()));
    datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($('#dr_user_email').val().trim()));
    datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($('#dr_user_mobile').val().trim()));
    datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($('#dr_user_lang').val().trim()));
    datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_odos').val().trim()));
    datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_arithmos').val().trim()));
    datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_orofos').val().trim()));
    datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($('#dr_user_ma_perioxi').val().trim()));
    datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($('#dr_user_ma_poli').val().trim()));
    datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($('#dr_user_ma_tk').val().trim()));
    datasend+='&dr_user_ma_country_id='  + encodeURIComponent($('#dr_user_ma_country_id').val().trim());
    datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($('#dr_user_ma_nomos_id').val().trim());
    datasend+='&form_parastatiko=' +      encodeURI($('input[name=form_parastatiko]:checked').val());
    datasend+='&dr_user_eponimia='  + encodeURIComponent($.base64.encode($('#dr_user_eponimia').val().trim()));
    datasend+='&dr_user_title='  + encodeURIComponent($.base64.encode($('#dr_user_title').val().trim()));
    datasend+='&dr_user_afm='  + encodeURIComponent($.base64.encode($('#dr_user_afm').val().trim()));
    datasend+='&dr_user_doy='  + encodeURIComponent($.base64.encode($('#dr_user_doy').val().trim()));
    datasend+='&dr_user_epaggelma='  + encodeURIComponent($.base64.encode($('#dr_user_epaggelma').val().trim()));


//    datasend+='&form_select_apostoli=' +  encodeURIComponent($('#form_select_apostoli').val().trim());
//    datasend+='&form_ea_name=' +          encodeURIComponent($.base64.encode($('#form_ea_name').val().trim()));
//    datasend+='&form_ea_phone=' +         encodeURIComponent($.base64.encode($('#form_ea_phone').val().trim()));
//    datasend+='&form_ea_odos=' +          encodeURIComponent($.base64.encode($('#form_ea_odos').val().trim()));
//    datasend+='&form_ea_perioxi=' +       encodeURIComponent($.base64.encode($('#form_ea_perioxi').val().trim()));
//    datasend+='&form_ea_poli=' +          encodeURIComponent($.base64.encode($('#form_ea_poli').val().trim()));
//    datasend+='&form_ea_tk=' +            encodeURIComponent($.base64.encode($('#form_ea_tk').val().trim()));
//    if ($('#form_ea_country_id').val()==null)$('#form_ea_country_id').val(0);
//    if ($('#form_ea_nomos_id').val()==null)$('#form_ea_nomos_id').val(0);
//    datasend+='&form_ea_country_id=' +    encodeURIComponent($('#form_ea_country_id').val().trim());
//    datasend+='&form_ea_nomos_id=' +      encodeURIComponent($('#form_ea_nomos_id').val().trim());
    datasend+='&fiscal_position_id=' +      encodeURIComponent($('#fiscal_position_id').val().trim());
    datasend+='&pricelist_id=' +      encodeURIComponent($('#pricelist_id').val().trim());
    datasend+='&def_ekptosi=' + $('#def_ekptosi').val();
    
    datasend+='&hotel_reservation_id=' + from_php_id;
    datasend+='&journal_id=' + $('#reservation_journal_id').val();
    datasend+='&seira_id=' + $('#reservation_seira_id').val();    
    
    //console.log('user_save');
    //console.log(datasend);
    
    dialog_user_save.datasend=datasend;
    
    $('body').addClass('myloading');
    $.ajax({
			url: '/my/admin-users-add-exec.php',
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
				  
				    if (data.ask_user) {
				      //console.log(data.exist_rows);
    				      
    				  outhtml='';
    					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
    					outhtml+='<thead><tr>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:0%">'+gks_lang('Α/Α')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Επιλογή')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:30%">'+gks_lang('Υποκοριστικό')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:60%">'+gks_lang('Αναζήτηση')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Προβολή')+'</th>';
    					outhtml+='</tr></thead><tbody>';
  
    					for (i=0;i < data.exist_rows.length; i++) {
    					  outhtml+='<tr>' + 
    					  '<td scope="row" style="text-align: center !important;" nowrap>' + (i + 1) + '</td>' +
    					  '<td style="text-align: center !important;">' + '<input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_' + data.exist_rows[i].ID + '" value="' + data.exist_rows[i].ID + '"  title="'+gks_lang('Επιλογή χρήστη')+'"></td>' +
    					  '<td><label class="gks_label" for="dialog_user_save_radio_' + data.exist_rows[i].ID + '" style="vertical-align: middle;">' + data.exist_rows[i].gks_nickname + '</label></td>' + 
    					  '<td>' + data.exist_rows[i].descrs.join('<br>') + '</td>' +
    					  '<td style="text-align: center !important;"><a href="admin-users-item.php?id=' + data.exist_rows[i].ID + '" target="_blank"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή χρήστη')+'"></i></a></td>' + 
    					  '</tr>';
    					}
    					outhtml+='</tbody></table>';
  					  
  					  $('#dialog_user_save_html').html(outhtml);
          	  dwidth=$(window).width() * 0.96;
              dheight=$(window).height() * 0.96;
          	  if (dwidth> 850) dwidth=850;
          	  //if (dheight> 600) dheight=600;
          	  $('#dialog_user_save_radio_new').prop('disabled', false);
          	  dialog_user_save.dialog('option', 'width', dwidth);
          	  dialog_user_save.dialog('option', 'height', dheight);
          	  $('#dialog_user_save').parent().css({position:'fixed'});      
              dialog_user_save.dialog('open');    
  					  			      
				    } else {
              $('#user_id').val(data.user_id);
              $('#user').val(data.gks_nickname);
              $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
              $('#user_save').hide();				      
				    }
				    
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
	  });
    
  });
  
  $('#kostos_pliromis').on(mychange, function() {
    kostos_pliromis_mode='manual';
    calc_pliroteo();
  });
    
  $('input[name=radio_payment_way]').click(function() {
    kostos_pliromis_mode='auto';

    need_save=true;
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');

    $('input[name=radio_payment_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });    
    
    d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
   
    $('#payment_acquirer_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#payment_acquirer_sxolio').html(gks_lang('Σχόλιο τρόπου πληρωμής')+': <i>' + myhtml + '</i>');
    
    myhtml= $.base64.decode($(this).attr('data-button-html'));
    if (myhtml=='') myhtml=gks_lang('Πληρωμή τώρα');
    $('#button_html').html(myhtml);

    if (p==2) { 
      $('#div_bank_deposit_9digit').show();
    } else {
      $('#div_bank_deposit_9digit').hide();
    }
        
    calc_pliroteo();
    gks_myscroll();
  });
  
  function gks_ekptosi_change(event) {
    //console.log('gks_ekptosi_change');
    //console.log(event);
    
    need_save=true;
    event.preventDefault();  
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<0) return;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        $('.gks_price_per_item[data-aa=' + aa + ']').focus().select();
        return;
      }
    }
    calc_pliroteo('gks_ekptosi',aa);
    gks_myscroll();
  }
    
  $('.gks_ekptosi_pososto').on(mychange, gks_ekptosi_change);  
  
  function gks_price_per_item_change(event) {
    need_save=true;
    event.preventDefault();  
    aa_start=parseInt($(this).attr('data-aa'));
    if (isNaN(aa_start)) aa_start=-1;
    if (aa_start<0) return;

    aa=aa_start;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();   
      elem = $('.gks_price_final[data-aa=' + aa + ']');
      if (elem.length==1) {
        if (control_enter_active==false) elem.focus().select();
        return;
      } else {
        $('.gks_price_final[data-aa=' + aa + ']').focus().select();
      }
    }
    val=parseFloat($(this).val()); if (isNaN(val)) val=0; 
    num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
    $('.gks_price_final[data-aa=' + aa + ']').val((val*num_days).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
    
    calc_pliroteo('gks_price_final',aa_start);
    gks_myscroll();
  };
  $('.gks_price_per_item').on(mychange, gks_price_per_item_change);
  
  function gks_price_final_change(event) {
    //console.log(event);
    
    need_save=true;
    event.preventDefault();  
    aa_start=parseInt($(this).attr('data-aa'));
    if (isNaN(aa_start)) aa_start=-1;
    if (aa_start<0) return;

    aa=aa_start;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();   
      aa++;
      elem = $('.gks_ekptosi_pososto[data-aa=' + aa + ']');
      if (elem.length==1) {
        if (control_enter_active==false) elem.focus().select();
        return;
      } else {
        $('#kostos_pliromis').focus().select();
        //$('#addroom').click();
      }
    }
    calc_pliroteo('gks_price_final',aa_start);
    gks_myscroll();
  }    
    
  $('.gks_price_final').on(mychange, gks_price_final_change);
  
  $('#fiscal_position_id').change(function() {
    fields_change_set_pososto();
    calc_pliroteo();
  });
  $('#pricelist_id').change(function() {
    fields_change_set_pososto();
    calc_pliroteo();
  });
      
  var calc_pliroteo_xhr;
  var calc_pliroteo_timer=null;
  function calc_pliroteo(field_name='', field_aa=-1, mycmd='', myfile='') {
    //console.log('calc_pliroteo');
    need_save=true;
    
    check_vies_valid_wait_timer_stop();
    
    if(calc_pliroteo_xhr && calc_pliroteo_xhr.readyState != 4){
      calc_pliroteo_xhr.abort();
    }
    if (calc_pliroteo_timer!=null) clearTimeout(calc_pliroteo_timer);
    calc_pliroteo_timer=setTimeout(calc_pliroteo_run,400,field_name, field_aa, mycmd, myfile);
  }
  function calc_pliroteo_run(field_name='', field_aa=-1, mycmd='', myfile='') {

    if (field_aa>=0 && (field_name=='gks_ekptosi' || field_name=='gks_price_final')) {
      fields_change[field_aa]=field_name;
    }
    
    
    
  
  
    ajia_total=0;
    num_adults=0;
    num_childs=0;
    num_child_kounies=0;
    num_extra_beds=0;
    for (i = 0; i < json_rooms_list.length; i++) {
      if (json_rooms_list[i].delete==0) {
        ajia_total+=json_rooms_list[i].ajia_total;
        num_adults+=json_rooms_list[i].rnum_adults;
        num_childs+=json_rooms_list[i].rnum_childs;
        num_child_kounies+=json_rooms_list[i].rnum_child_kounies;
        num_extra_beds+=json_rooms_list[i].rnum_extra_beds;
      }
    }
    
    //console.log(ajia_total);
    //console.log(rnum_adults);
    //console.log(rnum_childs);
//      $('#sum_ajia_total').html(
//        (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
//        ajia_total.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
//        (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : ''));
    roomline='';
    if (num_adults>0)
      roomline+='<i class="fa fa-male tooltipster" style="color:#aaaaaa;" title="'+gks_lang('Ενήλικες')+'"></i>' + num_adults;
    if (num_childs>0)  
      roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa-child tooltipster" style="color:#aaaaaa;font-size:80%;" title="'+gks_lang('Παιδιά')+'"></i>' + num_childs;
    if (roomline!='') roomline+='<br>';
    if (num_child_kounies>0)
      roomline+='<i class="fa fa-box tooltipster" style="color:#aaaaaa;font-size:90%;" title="'+gks_lang('Βρεφικό κρεβάτι')+'"></i>' + num_child_kounies;
    if (num_extra_beds>0)  
      roomline+=(roomline=='' ? '' : ' ') + '<i class="fa fa-bed tooltipster" style="color:#aaaaaa;" title="'+gks_lang('Επιπλέον κρεβάτι')+'"></i>' + num_extra_beds;
    
    $('#sum_visitors').html(roomline);
    $('#sum_visitors .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
        
        
    var reaa=0;
    $('#tableroomlist tbody tr .itemtd1').each(function() {
      reaa++;
      $(this).html(reaa);
    });
    
    
    //mysubmit(reservation_state='', gks_postype='' ,field_name='', field_aa=-1, mycmd='', myfile='') {
    mysubmit('','calc', field_name, field_aa, mycmd, myfile);
    
  }


  
      
  function mysubmit(reservation_state='', gks_postype='' ,field_name='', field_aa=-1, mycmd='', myfile='') {
    if (from_php_perm_ret_edit==false) return;
    $('#calc_hourglass').show();
    
    //console.log($("#mypostform #sxolio").val().trim());
    //console.log($.base64.encode($("#mypostform #sxolio").val().trim()));
    //console.log(encodeURIComponent($.base64.encode($("#mypostform #sxolio").val().trim())));

    datasend='';

    datasend+='&gks_lock=' + (from_php_gks_lock ? '1' : '0');
    datasend+='&gks_number_lock=' + (from_php_number_gks_lock ? '1' : '0');
    datasend+='&gks_user_lock=' + (from_php_user_gks_lock ? '1' : '0');
    datasend+='&reservation_status=' + encodeURIComponent($.base64.encode(reservation_state));
    datasend+='&postype=' + encodeURIComponent(gks_postype);

    datasend+='&user_notes='  + encodeURIComponent($.base64.encode($('#mypostform #user_notes').val().trim()));
    datasend+='&sxolio='  + encodeURIComponent($.base64.encode($('#mypostform #sxolio').val().trim()));
    datasend+='&note_logistirio='  + encodeURIComponent($.base64.encode($('#mypostform #note_logistirio').val().trim()));
    datasend+='&cache_file=' + cache_file;
    
    
    if (from_php_gks_lock == false) {
    
      if (gks_postype=='') {
        rnum_adults=0;
        rnum_childs=0;
        for (i = 0; i < json_rooms_list.length; i++) {
          if (json_rooms_list[i].delete==0) {
            rnum_adults+=json_rooms_list[i].rnum_adults;
            rnum_childs+=json_rooms_list[i].rnum_childs;
          }
        }
        num_adults=parseInt($('#mypostform #num_adults').val());
        if (isNaN(num_adults)) num_adults=0;
        num_childs=parseInt($('#mypostform #num_childs').val());
        if (isNaN(num_childs)) num_childs=0;
        
        if (num_adults!=rnum_adults) {
          $('#mypostform #num_adults').focus();
          myalert('error:'+gks_lang('Δεν συμφωνεί το πλήθος των ενηλίκων της κράτησης με το άθροισμα των ενηλίκων από τα δωμάτια'));
          return;
        }
        if (num_childs!=rnum_childs) {
          $('#mypostform #num_childs').focus();
          myalert('error:'+gks_lang('Δεν συμφωνεί το πλήθος των παιδιών της κράτησης με το άθροισμα των παιδιών από τα δωμάτια'));
          return;
        }
      }
    
      if ($("#reservation_journal_id").length > 0) datasend+='&reservation_journal_id=' + encodeURIComponent($("#reservation_journal_id").val().trim());
      if ($("#reservation_seira_id").length > 0) datasend+='&reservation_seira_id=' + encodeURIComponent($("#reservation_seira_id").val().trim());
      if ($("#reservation_number_int").length > 0) datasend+='&reservation_number_int=' + encodeURIComponent($("#reservation_number_int").val().trim());
    
    
      datasend+='&mycmd=' + encodeURIComponent(mycmd);
      datasend+='&myfile=' + encodeURIComponent(myfile);
      datasend+='&hotel_id='  + encodeURIComponent($('#hotel_id').val().trim());
      datasend+='&reservation_date=' + encodeURIComponent($('#mypostform #reservation_date').val().trim());
      datasend+='&check_in='  + encodeURIComponent($('#mypostform #check_in').val().trim());
      datasend+='&check_out='  + encodeURIComponent($('#mypostform #check_out').val().trim());
      datasend+='&num_days='  + encodeURIComponent($('#mypostform #num_days').val().trim());
      datasend+='&num_adults='  + encodeURIComponent($('#mypostform #num_adults').val().trim());
      datasend+='&num_childs='  + encodeURIComponent($('#mypostform #num_childs').val().trim());
    
      datasend+='&user_id=' + encodeURIComponent($('#mypostform #user_id').val().trim());
      datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($('#dr_user_first_name').val().trim()));
      datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($('#dr_user_last_name').val().trim()));
      datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($('#dr_user_email').val().trim()));
      datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($('#dr_user_mobile').val().trim()));
      datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($('#dr_user_lang').val().trim()));
      datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_odos').val().trim()));
      datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_arithmos').val().trim()));
      datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($('#dr_user_ma_orofos').val().trim()));
      datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($('#dr_user_ma_perioxi').val().trim()));
      datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($('#dr_user_ma_poli').val().trim()));
      datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($('#dr_user_ma_tk').val().trim()));
      datasend+='&dr_user_ma_country_id='  + encodeURIComponent($('#dr_user_ma_country_id').val().trim());
      datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($('#dr_user_ma_nomos_id').val().trim());
      datasend+='&form_parastatiko=' +      encodeURI($('input[name=form_parastatiko]:checked').val());
      datasend+='&dr_user_eponimia='  + encodeURIComponent($.base64.encode($('#dr_user_eponimia').val().trim()));
      datasend+='&dr_user_title='  + encodeURIComponent($.base64.encode($('#dr_user_title').val().trim()));
      datasend+='&dr_user_afm='  + encodeURIComponent($.base64.encode($('#dr_user_afm').val().trim()));
      datasend+='&dr_user_doy='  + encodeURIComponent($.base64.encode($('#dr_user_doy').val().trim()));
      datasend+='&dr_user_epaggelma='  + encodeURIComponent($.base64.encode($('#dr_user_epaggelma').val().trim()));    
      
      datasend+='&fiscal_position_id=' +      encodeURIComponent($('#fiscal_position_id').val().trim());
      datasend+='&pricelist_id=' +      encodeURIComponent($('#pricelist_id').val().trim());
      datasend+='&def_ekptosi=' + $('#def_ekptosi').val();    
      
      //console.log($('#fiscal_position_id').val());
  
      coupons_str = encodeURIComponent($.base64.encode(JSON.stringify(coupons_array)));
      datasend+='&coupons_str=' + coupons_str;
      
      childs_ages_list=[];
      $('.childs_ages_list_select').each(function() {
        val=parseInt($(this).val());
        if (isNaN(val)) val=-1;
        //if (val>=0) {
          childs_ages_list.push(val);
        //} 
      });
      childs_ages_list_str = encodeURIComponent($.base64.encode(JSON.stringify(childs_ages_list)));
      datasend+='&childs_ages_list_str=' + childs_ages_list_str;
      //console.log(childs_ages_list);
      //console.log(childs_ages_list_str);
    
    
      for(i=0; i < json_rooms_list.length; i++) {
        elem=$('.gks_ekptosi_pososto[data-aa=' + json_rooms_list[i].aa + ']');
        if (elem.length == 1) {
          vv=parseFloat(elem.val());
          if (isNaN(vv)) vv=0;
          json_rooms_list[i].gks_ekptosi_pososto = vv;
  
          vv=parseFloat($('.gks_price_final[data-aa=' + json_rooms_list[i].aa + ']').val());
          if (isNaN(vv)) vv=0;
          json_rooms_list[i].ajia_total = vv;
        }
        
        json_rooms_list[i].pdata={};
        if (gks_postype=='') {
          elem_pdata=$('.gks_price_final[data-aa=' + json_rooms_list[i].aa + ']');
  
          product_price_coupon_use='';
          product_price_coupon_use_disabled=0;
          celem=$('.gks_coupon_item[data-aa=' + json_rooms_list[i].aa + ']');
          if (celem.css('display')!='none') {
            product_price_coupon_use=celem.text();
            if (celem.hasClass('gks_coupon_item_disabled')) product_price_coupon_use_disabled=1;
          }
          
          
          json_rooms_list[i].pdata.product_id                                  =elem_pdata.attr('data-product_id');
          json_rooms_list[i].pdata.product_fpa_base_id                         =elem_pdata.attr('data-product_fpa_base_id');                   
          json_rooms_list[i].pdata.product_fpa_id                              =elem_pdata.attr('data-product_fpa_id');                   
          json_rooms_list[i].pdata.product_fpa_pososto                         =elem_pdata.attr('data-product_fpa_pososto');              
          json_rooms_list[i].pdata.product_fpa_id_json                         =elem_pdata.attr('data-product_fpa_id_json');              
          json_rooms_list[i].pdata.product_price_include_vat                   =elem_pdata.attr('data-product_price_include_vat');        
          json_rooms_list[i].pdata.product_price_start_peritem_db              =elem_pdata.attr('data-product_price_start_peritem_db');   
          json_rooms_list[i].pdata.product_price_start_peritem_net             =elem_pdata.attr('data-product_price_start_peritem_net');  
          json_rooms_list[i].pdata.product_price_start_peritem_fpa             =elem_pdata.attr('data-product_price_start_peritem_fpa');  
          json_rooms_list[i].pdata.product_price_start_peritem_total           =elem_pdata.attr('data-product_price_start_peritem_total');
          json_rooms_list[i].pdata.product_price_start_all_net                 =elem_pdata.attr('data-product_price_start_all_net');      
          json_rooms_list[i].pdata.product_price_start_all_fpa                 =elem_pdata.attr('data-product_price_start_all_fpa');      
          json_rooms_list[i].pdata.product_price_start_all_total               =elem_pdata.attr('data-product_price_start_all_total');    
          json_rooms_list[i].pdata.product_price_final_peritem_db              =elem_pdata.attr('data-product_price_final_peritem_db');   
          json_rooms_list[i].pdata.product_price_final_peritem_net             =elem_pdata.attr('data-product_price_final_peritem_net');  
          json_rooms_list[i].pdata.product_price_final_peritem_fpa             =elem_pdata.attr('data-product_price_final_peritem_fpa');  
          json_rooms_list[i].pdata.product_price_final_peritem_total           =elem_pdata.attr('data-product_price_final_peritem_total');
          json_rooms_list[i].pdata.product_price_final_all_net                 =elem_pdata.attr('data-product_price_final_all_net');      
          json_rooms_list[i].pdata.product_price_final_all_fpa                 =elem_pdata.attr('data-product_price_final_all_fpa');      
          json_rooms_list[i].pdata.product_price_final_all_total               =elem_pdata.attr('data-product_price_final_all_total');    
          json_rooms_list[i].pdata.product_price_ekptosi_net                   =elem_pdata.attr('data-product_price_ekptosi_net');        
          json_rooms_list[i].pdata.product_price_ekptosi_pososto               =elem_pdata.attr('data-product_price_ekptosi_pososto');    
          json_rooms_list[i].pdata.product_pricelist_item_id                   =elem_pdata.attr('data-product_pricelist_item_id');        
          json_rooms_list[i].pdata.product_pricelist_item_percent              =elem_pdata.attr('data-product_pricelist_item_percent');   
          json_rooms_list[i].pdata.product_price_coupon_use                    =product_price_coupon_use;         
          json_rooms_list[i].pdata.product_price_coupon_use_disabled           =product_price_coupon_use_disabled;     
          
          json_rooms_list[i].pdata.ajia_table_math                             = $.base64.decode(elem_pdata.attr('data-ajia_table_math'));  
          json_rooms_list[i].pdata.ajia_table_html                             = $.base64.decode(elem_pdata.attr('data-ajia_table_html'));  
          json_rooms_list[i].pdata.ajia_table_array                            = elem_pdata.attr('data-ajia_table_array');  
          json_rooms_list[i].pdata.other_taxes_tooltip                         = $.base64.decode(elem_pdata.attr('data-other_taxes_tooltip'));  
          
          temp=elem_pdata.attr('data-other_taxes');
          if (temp === undefined || temp === null) temp='';
          if (temp!='') {
            other_taxes=JSON.parse($.base64.decode(temp));
          } else {
            other_taxes={};
            other_taxes.withheldPercentCategory=0;
            other_taxes.withheldAmount=0;
            other_taxes.otherTaxesPercentCategory=0;
            other_taxes.otherTaxesAmount=0;
            other_taxes.stampDutyPercentCategory=0;
            other_taxes.stampDutyAmount=0;
            other_taxes.feesPercentCategory=0;
            other_taxes.feesAmount=0;
          }
          //console.log(other_taxes);
          json_rooms_list[i].pdata.other_taxes=other_taxes;
          
          //console.log(json_rooms_list[i].pdata.ajia_table_math);
          //console.log(json_rooms_list[i].pdata.ajia_table_html);
          //console.log(json_rooms_list[i].pdata.ajia_table_array);
          
        }
      }
      
      json_rooms_list_send = JSON.parse(JSON.stringify(json_rooms_list));
      
      if (gks_postype == 'calc_dialog_room') {
        if (dialog_room.aa == -1) {
          aa = json_rooms_list_send.length;
          json_rooms_list_send.push({'aa':aa,'add':1,'edit':0,'delete':0,'recid':-1});
          fields_change[aa] = field_name; 
          field_aa=aa;
        } else {
          aa = dialog_room.aa;
          json_rooms_list_send[aa].edit=1;
          
        }
          var tmp_rchilds_ages_list=[];
          $('.rchilds_ages_list_item').each(function() {
            ci=parseInt($(this).val());
            if (isNaN(ci)) ci=-1;
            //if (ci<0) has_one_unselected=true;
            ca=parseInt($(this).find('option:selected').attr('data-age'));
            if (isNaN(ca)) ca=-1;
            if (ci > 0) tmp_rchilds_ages_list.push({index: ci, age: ca});
          });
          
          
          
          json_rooms_list_send[aa].room_descr = $('#dialog_room_room_descr').val();
          json_rooms_list_send[aa].room_type_descr = $('#dialog_room_room_type_descr').val();
          json_rooms_list_send[aa].hotel_room_id = myparseInt($('#dialog_room_room_id').val());
          json_rooms_list_send[aa].rnum_adults = myparseInt($('#dialog_room_num_adults').val());
          json_rooms_list_send[aa].rnum_childs = myparseInt($('#dialog_room_num_childs').val());
          json_rooms_list_send[aa].rchilds_ages_list = JSON.parse(JSON.stringify(tmp_rchilds_ages_list)); //CLONE Array.from(tmp_rchilds_ages_list);
  
          json_rooms_list_send[aa].rnum_child_kounies = myparseInt($('#dialog_room_num_child_kounies').val());
          json_rooms_list_send[aa].rnum_extra_beds = myparseInt($('#dialog_room_num_extra_beds').val());
          
          json_rooms_list_send[aa].visitors = dialog_room.visitors;
          json_rooms_list_send[aa].visitors_childs = dialog_room.visitors_childs;
          json_rooms_list_send[aa].visitors_max = dialog_room.visitors_max;
          
          json_rooms_list_send[aa].ajia_total =parseFloat($('#dialog_room_ajia_total').val()); 
          json_rooms_list_send[aa].gks_ekptosi_pososto = parseFloat($('#dialog_room_ekptosi_pososto').val());
          json_rooms_list_send[aa].rsxolio = $('#dialog_room_sxolio').val();
          json_rooms_list_send[aa].gks_nickname = $('#dialog_room_gks_nickname').val();
          json_rooms_list_send[aa].ruser_id = myparseInt($('#dialog_room_user_id').val());
          json_rooms_list_send[aa].ruser_first_name = $('#dialog_room_user_first_name').val();
          json_rooms_list_send[aa].ruser_last_name = $('#dialog_room_user_last_name').val();
          json_rooms_list_send[aa].ruser_email = $('#dialog_room_user_email').val();
          json_rooms_list_send[aa].ruser_mobile = $('#dialog_room_user_mobile').val();
          json_rooms_list_send[aa].ruser_lang = $('#dialog_room_user_lang').val();
          json_rooms_list_send[aa].ruser_ma_odos = $('#dialog_room_user_ma_odos').val();
          json_rooms_list_send[aa].ruser_ma_arithmos = $('#dialog_room_user_ma_arithmos').val();
          json_rooms_list_send[aa].ruser_ma_orofos = $('#dialog_room_user_ma_orofos').val();
          json_rooms_list_send[aa].ruser_ma_perioxi = $('#dialog_room_user_ma_perioxi').val();
          json_rooms_list_send[aa].ruser_ma_poli = $('#dialog_room_user_ma_poli').val();
          json_rooms_list_send[aa].ruser_ma_tk = $('#dialog_room_user_ma_tk').val();
          json_rooms_list_send[aa].ruser_ma_country_id = myparseInt($('#dialog_room_user_ma_country_id').val());
          json_rooms_list_send[aa].ruser_ma_nomos_id = myparseInt($('#dialog_room_user_ma_nomos_id').val());
          json_rooms_list_send[aa].ruser_fiscal_position_id = myparseInt($('#dialog_room_user_fiscal_position_id').val());
          json_rooms_list_send[aa].ruser_pricelist_id = myparseInt($('#dialog_room_user_pricelist_id').val());
                
          if ($('#selecttype0').is(':checked')) json_rooms_list_send[aa].ruser_id = -1;
          
      }
      //console.log(json_rooms_list[0].rnum_childs);
      //console.log(json_rooms_list_send[0].rnum_childs);
      
      //console.log(json_rooms_list_send);
      
      roolist = encodeURIComponent(JSON.stringify(json_rooms_list_send))
      datasend+='&roolist=' + roolist;
    
    
      datasend+='&fields_change=' + encodeURIComponent($.base64.encode(JSON.stringify(fields_change)));
      datasend+='&fields_change_curr_name=' + encodeURIComponent(field_name);
      datasend+='&fields_change_curr_aa=' + encodeURIComponent(field_aa);
    
    }
    
    
    p=$('input[name=radio_payment_way]:checked');
    if (p.css('display')=='none') {
      if (gks_postype=='') {
        myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής'));
        return;
      }
    }
    p=p.val();
    if (p === undefined || p === null) p=0;
    if (p<=0) {
      if (gks_postype=='') {
        myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής'));
        return;
      }
    }
    datasend+='&tropos_pliromis=' + p;    
    datasend+='&kostos_pliromis=' + $('#kostos_pliromis').val();
    datasend+='&kostos_pliromis_mode=' + kostos_pliromis_mode;


      
    datasend+='&affect_balance=' + ($('#affect_balance').is(':checked') ? '1' : '0');
    datasend+='&affect_balance_all_poso=' + ($('#affect_balance_all_poso').is(':checked') ? '1' : '0');
    baltype=$('input[name=affect_balance_all_poso_type]:checked');
    if (baltype.css('display')=='none') baltype='';
    else {baltype=baltype.val(); if (baltype === undefined || baltype === null) d='';}
    datasend+='&affect_balance_all_poso_type=' + encodeURIComponent(baltype);
    datasend+='&affect_balance_poso=' + $('#affect_balance_poso').val();

    
    datasend+='&assigned_id='  + $("#mypostform #assigned_id").attr('data-id');
    if (from_php_GKS_CRM_ENABLE) {
      datasend+='&crm_channel_id='  + $("#mypostform #crm_channel_id").val();
      datasend+='&crm_channel_contact_id='  + $("#mypostform #crm_channel_contact_id").attr('data-id');
      datasend+='&crm_channel_campain_id='  + $("#mypostform #crm_channel_campain_id").attr('data-id');
      datasend+='&crm_channel_url='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_url").val().trim()));
      datasend+='&crm_channel_code='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_code").val().trim()));
      datasend+='&crm_channel_text='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_text").val().trim()));
    }

    
    datasend+=gks_custom_datasend();
    
    //console.log(json_rooms_list);
    //console.log(roolist);
    //console.log(datasend);
    
    //return;
    
    if (gks_postype=='') $('body').addClass('myloading');
    
    calc_pliroteo_xhr = $.ajax({
			url: '/my/admin-hotel-reservation-item-exec.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_postype: gks_postype,
			gks_field_name:field_name,
			gks_field_aa:field_aa,			
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('myloading');
				if (textStatus!='abort') myalert('error:' + jqXHR.responseText);
				$('#calc_hourglass').hide();
				//console.log(jqXHR.responseText);
			},				
			success: function(data) {
				$('#calc_hourglass').hide();
				if (!data) {
				  $('body').removeClass('myloading');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
					  
  					if (this.gks_postype=='') {
  					  need_save=false;
    					if (data.save_but_message!='') {
    					  $("body").removeClass("myloading");
    					  if ($.base64.decode(data.message)=='ok') {
    					    myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					  } else {
    					    myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					  }
    					} else {
      					if (data.redirect=='') {
      					  window.location.reload();
      					} else {
      					  window.location.href = $.base64.decode(data.redirect);
      					}
      				}  	
    					return;
    			  } else if (this.gks_postype == 'calc_dialog_room') {
    			    cache_file=data.cache_file;
    			    //console.log(data);
    			    i=-1;
    			    for(ii=0;ii < data.eidi.length;ii++) {
    			      if (data.eidi[ii].aa == this.gks_field_aa) {
    			        i=ii;
    			        break;
    			      }
    			    }
    			    if (i==-1) i = data.eidi.length -1;
    			    
    			    
    			    //console.log(i);
    			    if (i != -1) {
    			      //console.log(data.eidi[i]);
    			      dialog_room_product_price_start_all_total = data.eidi[i].product_price_start_all_total;
    			      if ($('#dialog_room_ajia_total').is(":focus")==false) $('#dialog_room_ajia_total').val(data.eidi[i].product_price_final_all_total);
      
                val2=data.eidi[i].product_price_start_all_total - data.eidi[i].product_price_final_all_total;
                $('#dialog_room_gks_ekptosi_poso').html(val2.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND));
                if (val2==0) $('#dialog_room_gks_ekptosi_poso').hide(); else $('#dialog_room_gks_ekptosi_poso').show();

                $('#dialog_room_ajia_math').html((data.eidi[i].room_ajia_table.msg_price));
      				  if ($('#dialog_room_ajia_table').hasClass('tooltipstered')) $('#dialog_room_ajia_table').tooltipster('destroy');
      				  
      				  content_html='';
            		if (data.eidi[i].room_ajia_table.roomaf_html !='') {
		              content_html+='<div style="text-align:center"><b>'+gks_lang('Ανάλυση ανά μέρα')+'</b><br>' + data.eidi[i].room_ajia_table.msg_price + '</div>';
            		  content_html+='<div style="text-align:center">' + data.eidi[i].room_ajia_table.roomaf_html + '</div>';
            		}
            		if (data.eidi[i].roomaf_other_taxes_tooltip !='') 
            		  content_html+='<div style="text-align:center"><b>'+gks_lang('Λοιποί φόροι, τέλη κτλ.')+'</b><br>' + data.eidi[i].roomaf_other_taxes_tooltip + '</div>';

      				  
      				  
      				  $('#dialog_room_ajia_table').show();
      				  $('#dialog_room_ajia_table').tooltipster({
      				    theme: 'tooltipster-noir',
      				    contentAsHTML: true,
      				    interactive:true,
      				    content: content_html,
      				  });
      				  //console.log(data.roomaf_html);
      				  dialog_room_ajia_table_math=$.base64.decode(data.eidi[i].room_ajia_table.msg_price);
      				  dialog_room_ajia_table_html=data.eidi[i].room_ajia_table.roomaf_html;
      				  dialog_room_ajia_table_array=data.eidi[i].room_ajia_table.roomaf_array;
      				  dialog_room_other_taxes_tooltip=data.eidi[i].roomaf_other_taxes_tooltip;
      				  
      			    
      			  }
    			    
    			    //dialog_room_ajia_total
    			    
    			    gks_myscroll();
    			    return;
    				} else if (this.gks_postype == 'calc') {
    				  cache_file=data.cache_file;
    				  
    					for (var item in data.tropoi_pliromis_all) {
    					  var obj = data.tropoi_pliromis_all[item];
    					  
    					  elem=$('#price_payment_way_' + obj.id_payment_acquirer);
    					  elem.html(parseFloat(obj.pa_calc_kostos).mymoney());
    					  elem2=$('#radio_payment_way_' +  obj.id_payment_acquirer);
    					  elem2.attr('data-type-o',obj.payment_acquirer_type_dm)
    					  elem3=elem2.parent();
    					  if (obj.myisok=='0') {
                  elem3.hide();
                  if (elem2.prop('checked')) elem2.prop('checked', false);
                } else {
                  elem3.show();
                }
    					}
    					will_run_calc_pliroteo='';
    					var velemp=null; var velemp_length=0;
    					$('input[name=radio_payment_way]:enabled').each(function() {
    					  if ($(this).parent().css('display') != 'none') {velemp=$(this);velemp_length++;}
    					});
    					if (velemp_length==1) {
    					  if (!velemp.prop('checked')) {
    					    velemp.prop('checked', true); 
    					    will_run_calc_pliroteo=velemp.attr('id');
    					  }
    					} else {
    					  velemp=null; velemp_length=0;
    					  $('input[name=radio_payment_way]:enabled:checked').each(function() {
    					    if ($(this).parent().css('display') != 'none') {velemp=$(this);velemp_length++;}
    					  });
     					  
    					  if (velemp_length==0) {
    					    if (from_php_payment_way_default>0 && 
    					        $('input[name=radio_payment_way][value=' + from_php_payment_way_default + ']:enabled').length==1 && 
    					        $('input[name=radio_payment_way][value=' + from_php_payment_way_default + ']:enabled').parent().css('display') != 'none') {
    					      $('#radio_payment_way_' + from_php_payment_way_default).prop('checked', true);
    					      will_run_calc_pliroteo='radio_payment_way_' + from_php_payment_way_default;
    					    }
    					  }
    					}
    					
    					
    					if (from_php_gks_lock==false) {  
      					for(i=0;i < data.eidi.length;i++) {
      					 
      					  json_rooms_list[data.eidi[i].aa].ajia_total=data.eidi[i].product_price_final_all_total;
                  json_rooms_list[data.eidi[i].aa].gks_ekptosi_pososto=data.eidi[i].product_price_ekptosi_pososto; 
                  
                  
                  //console.log(data.eidi[i].product_price_ekptosi_pososto);
                  
                  $('.gks_price_final[data-aa=' + data.eidi[i].aa + ']').
                  
                                                         attr('data-product_id',data.eidi[i].product_id).
                                                         attr('data-product_fpa_base_id',data.eidi[i].product_fpa_base_id).
                                                         attr('data-product_fpa_id',data.eidi[i].product_fpa_id).
                                                         attr('data-product_fpa_pososto',data.eidi[i].product_fpa_pososto).
                                                         attr('data-product_fpa_id_json', $.base64.encode(data.eidi[i].product_fpa_id_json)).
                                                         attr('data-product_price_include_vat',data.eidi[i].product_price_include_vat).
                                                         attr('data-product_price_start_peritem_db',data.eidi[i].product_price_start_peritem_db).
                                                         attr('data-product_price_start_peritem_net',data.eidi[i].product_price_start_peritem_net).
                                                         attr('data-product_price_start_peritem_fpa',data.eidi[i].product_price_start_peritem_fpa).
                                                         attr('data-product_price_start_peritem_total',data.eidi[i].product_price_start_peritem_total).
                                                         attr('data-product_price_start_all_net',data.eidi[i].product_price_start_all_net).
                                                         attr('data-product_price_start_all_fpa',data.eidi[i].product_price_start_all_fpa).
                                                         attr('data-product_price_start_all_total',data.eidi[i].product_price_start_all_total).
                                                         attr('data-product_price_final_peritem_db',data.eidi[i].product_price_final_peritem_db).
                                                         attr('data-product_price_final_peritem_net',data.eidi[i].product_price_final_peritem_net).
                                                         attr('data-product_price_final_peritem_fpa',data.eidi[i].product_price_final_peritem_fpa).
                                                         attr('data-product_price_final_peritem_total',data.eidi[i].product_price_final_peritem_total).
                                                         attr('data-product_price_final_all_net',data.eidi[i].product_price_final_all_net).
                                                         attr('data-product_price_final_all_fpa',data.eidi[i].product_price_final_all_fpa).
                                                         attr('data-product_price_final_all_total',data.eidi[i].product_price_final_all_total).
                                                         attr('data-product_price_ekptosi_net',data.eidi[i].product_price_ekptosi_net).
                                                         attr('data-product_price_ekptosi_pososto',data.eidi[i].product_price_ekptosi_pososto).
                                                         attr('data-product_pricelist_item_id',data.eidi[i].product_pricelist_item_id).
                                                         attr('data-product_pricelist_item_percent',data.eidi[i].product_pricelist_item_percent).
                                                         attr('data-product_price_coupon_use',data.eidi[i].product_price_coupon_use).
                                                         attr('data-product_price_coupon_use_disabled',data.eidi[i].product_price_coupon_use_disabled).
                  
                                                         attr('data-fpa_descr_print',data.eidi[i].fpa_descr_print).
                                                         
                                                         attr('data-ajia_table_math',$.base64.encode(data.eidi[i].room_ajia_table.msg_price)).
                                                         attr('data-ajia_table_html',$.base64.encode(data.eidi[i].room_ajia_table.roomaf_html)).
                                                         attr('data-ajia_table_array',data.eidi[i].room_ajia_table.roomaf_array).
                                                         attr('data-other_taxes_tooltip',$.base64.encode(data.eidi[i].roomaf_other_taxes_tooltip)).
                                                         attr('data-other_taxes',$.base64.encode(JSON.stringify(data.eidi[i].other_taxes)));
                                                         
      					  
      					  room_info_price_tooltipster(data.eidi[i].aa);
      					  
      					  if (!(this.gks_field_name=='gks_price_final' && this.gks_field_aa == data.eidi[i].aa)) {
      					    $('.gks_price_final[data-aa=' + data.eidi[i].aa + ']').val(data.eidi[i].product_price_final_all_total);
      					  } 
      					

      					  if ($('.gks_price_per_item[data-aa=' + data.eidi[i].aa + ']').is(":focus")==false) {
      					    $('.gks_price_per_item[data-aa=' + data.eidi[i].aa + ']').val(data.eidi[i].product_price_final_peritem_total);
      					  }
                  
                  
                  $('.gks_ekptosi_pososto[data-aa=' + data.eidi[i].aa + ']').attr('data-prev-value', data.eidi[i].product_price_ekptosi_pososto); 
                  if (!(this.gks_field_name=='gks_ekptosi' && this.gks_field_aa == data.eidi[i].aa)) {
                     $('.gks_ekptosi_pososto[data-aa=' + data.eidi[i].aa + ']').val(data.eidi[i].product_price_ekptosi_pososto);
                  }
                  
                  if (data.eidi[i].ekptosi_poso_html=='')
                    $('.gks_ekptosi_poso[data-aa=' + data.eidi[i].aa + ']').html('').hide();
                  else
                    $('.gks_ekptosi_poso[data-aa=' + data.eidi[i].aa + ']').html(data.eidi[i].ekptosi_poso_html).show();
                   
                   
                  if (data.eidi[i].product_price_coupon_use=='')
                    if (data.eidi[i].product_price_coupon_use_disabled=='') 
                      $('.gks_coupon_item[data-aa=' + data.eidi[i].aa + ']').html('').hide();
                    else 
                      $('.gks_coupon_item[data-aa=' + data.eidi[i].aa + ']').html(data.eidi[i].product_price_coupon_use_disabled).addClass('gks_coupon_item_disabled').show();  
                  else
                    $('.gks_coupon_item[data-aa=' + data.eidi[i].aa + ']').html(data.eidi[i].product_price_coupon_use).removeClass('gks_coupon_item_disabled').show();
                    
                  
                  
                }
                
      					$('#gks_products_posotita').html(data.products_posotita);
      					$('#gks_products_ogos').html(data.products_ogos);
      					$('#gks_products_varos').html(data.products_varos);
                
      					$('#gks_total_price_net').html(data.products_netvalue).attr('data-val',data.products_netvalue_fl);
      					$('#bal_gks_total_price_net').html(data.products_netvalue).attr('data-val',data.products_netvalue_fl);
      					$('#gks_total_price_fpa').html(data.products_fpa).attr('data-val',data.products_fpa_fl);
      					if (data.products_fpa=='') $('#tr_gks_total_price_fpa').hide(); else $('#tr_gks_total_price_fpa').show();
      					$('#gks_total_price_netfpa').html(data.products_netfpa).attr('data-val',data.products_netfpa_fl);
      					$('#bal_gks_total_price_netfpa').html(data.products_netfpa).attr('data-val',data.products_netfpa_fl);
      					if (data.products_netfpa=='') $('#tr_gks_total_price_netfpa').hide(); else $('#tr_gks_total_price_netfpa').show();
  
      					
      					$('#totalWithheldAmount').html(data.totalWithheldAmount).attr('data-val',data.totalWithheldAmount_fl);
      					if (data.totalWithheldAmount=='') $('#tr_totalWithheldAmount').hide(); else $('#tr_totalWithheldAmount').show();
      					$('#totalOtherTaxesAmount').html(data.totalOtherTaxesAmount).attr('data-val',data.totalOtherTaxesAmount_fl);
      					if (data.totalOtherTaxesAmount=='') $('#tr_totalOtherTaxesAmount').hide(); else $('#tr_totalOtherTaxesAmount').show();
      					$('#totalStampDutyamount').html(data.totalStampDutyamount).attr('data-val',data.totalStampDutyamount_fl);
      					if (data.totalStampDutyamount=='') $('#tr_totalStampDutyamount').hide(); else $('#tr_totalStampDutyamount').show();
      					$('#totalFeesAmount').html(data.totalFeesAmount).attr('data-val',data.totalFeesAmount_fl);
      					if (data.totalFeesAmount=='') $('#tr_totalFeesAmount').hide(); else $('#tr_totalFeesAmount').show();
                
      					$('#gks_total_price_total').html(data.products_total).attr('data-val',data.products_total_fl);;
      					$('#bal_gks_total_price_total').html(data.products_total).attr('data-val',data.products_total_fl);
      					$('#sum_ajia_total').html(data.products_total);
                
      				  if (data.check_vies.views_run_img!='') {
                  $('#dr_user_afm_views_run').html(data.check_vies.views_run_img).show();
                  $('#dr_user_afm_views_run .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
                  if (data.check_vies.valid==2) check_vies_valid_wait_timer_restart();
                } else {
                  $('#dr_user_afm_views_run').hide();
                }
      				  
      				  
      				  $('#coupons_html').html(data.coupons_html);
      				  $('#coupons_html .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
      				  $('.coupon_delete').click(coupon_delete_click);
      				  coupons_array=data.coupons_array;
      				  //console.log(coupons_array);


              }
    					
    					//console.log(data.kostos_apostolis);
    					
    					
    					//KOSTAS
    					
    					//if (kostos_apostolis_mode!='manual') $('#kostos_apostolis').val(data.kostos_apostolis_val);
    					if (kostos_pliromis_mode!='manual') $('#kostos_pliromis').val(data.kostos_pliromis_val);
    					$('#gks_pliroteo').html(data.pliroteo);
    				  $('#bal_gks_pliroteo').html(data.pliroteo).attr('data-val',data.pliroteo_val);

    				  
    					if(will_run_calc_pliroteo!='') {
    					  $('#' + will_run_calc_pliroteo).click();
    					} 
    					balance_user_after_calc();   				  
    				  gks_myscroll();
    				  calc_efd(); 				  
    				}
    				
					} else {
						$('body').removeClass('myloading');
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  


  function fields_change_set_pososto() {
    //console.log(fields_change); 
    for(i=0; i<fields_change.length; i++) {
      if (fields_change[i]=='gks_price_final') fields_change[i]='gks_ekptosi';
    } 
    //console.log(fields_change); 
  }  

  function room_info_price_tooltipster(aa) {
    rpt=$('.room_info_price[data-aa=' + aa + ']');
    if (rpt.length==0) return;
    if (rpt.hasClass('tooltipstered')) rpt.tooltipster('destroy');
    sourceattr = $('.gks_price_final[data-aa=' + aa + ']');
    
    myhtml=
      '<div style="text-align:center"><b>'+gks_lang('Ανάλυση ανά μέρα')+'</b><br>' + 
      $.base64.decode(sourceattr.attr('data-ajia_table_math')) + '</div>' + 
      '<div style="text-align:center">' + $.base64.decode(sourceattr.attr('data-ajia_table_html')) + '</div>';
    temp=sourceattr.attr('data-other_taxes_tooltip');
    if (temp === undefined || temp === null) temp='';
    if (temp!='') myhtml+='<div style="text-align:center"><b>'+gks_lang('Λοιποί φόροι, τέλη κτλ.')+'</b><br>' + $.base64.decode(temp) + '</div>';
    
    rpt.tooltipster({
			    theme: 'tooltipster-noir',
			    contentAsHTML: true,
			    interactive:true,
			    content: myhtml,
	  });    
  }

  for (i = 0; i < json_rooms_list.length; i++) {
    if (dialog_room.aa != i && json_rooms_list[i].delete == 0) {
      room_info_price_tooltipster(json_rooms_list[i].aa);
    }
  }
  
  $('#num_childs').on(mychange, function() {
    var val_num_childs=parseInt($('#num_childs').val());
    if (isNaN(val_num_childs)) val_num_childs=0;
    //console.log(val_num_childs);
    hotel_id=parseInt($('#hotel_id').val()); if (isNaN(hotel_id)) hotel_id=0;
    if (hotel_id<=0) return;
    
    $('.childs_ages_list_div').each(function() {
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa>0 && aa>val_num_childs) $(this).remove(); 
    });
    
    max_exist=$('.childs_ages_list_div:last');
    if (max_exist.length==0) max_exist=0;
    else {
      max_exist=parseInt(max_exist.attr('data-aa'));
      if (isNaN(max_exist)) max_exist=0;
    }
    //console.log(max_exist);
    
    for (ic=max_exist + 1 ;ic<=val_num_childs; ic++) {
      myhtml='';

      myhtml+= '<div class="form-group row childs_ages_list_div" data-aa="' + ic + '">' +
                  '<label for="childs_ages_list_' + ic + '" class="childs_ages_list_label col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Ηλικία [n] παιδιού').replaceAll('[n]',gks_n_ho(ic)) + ':</label>' +
                  '<div class="col-md-8">' + 
                    '<select id="childs_ages_list_' + ic + '" class="childs_ages_list_select form-control form-control-sm">' +
                      '<option value="-1"></option>';
                     
                      
                      for(ia=0; ia<=from_php_GKS_HOTEL_CHILD_ACCEPT_MAX_AGE[hotel_id]; ia++) {
                        if (child_age_price_ap_array[hotel_id][ia]!='') {
                          myhtml+= '<option value="' + ia + '" ';
                          myhtml+= '>' + child_age_price_ap_array[hotel_id][ia] + '</option>';
                        }
                      }
      myhtml+=     '</select>' +
                  '</div>' +
                '</div>';
      
      $('#childs_ages_list_main_div').append(myhtml);
      $('.childs_ages_list_select:last').change(childs_ages_list_select_change);
    }
    
  });
  
  function childs_ages_list_select_change() {
    //console.log($(this).attr('id'));
    //console.log('childs_ages_list_select_change');

    childs_and_ages=[];
    $('.childs_ages_list_select').each(function(index) {
      val=parseInt($(this).val());
      if (isNaN(val)) val=-1;
      mytxt= $(this).find('option:selected').text();
      if (mytxt=='') mytxt=gks_lang('[n] παιδί').replace('[n]',gks_n_ho(index + 1));
      childs_and_ages.push({age:val, txt:mytxt});
    });
    
    //console.log('childs_and_ages');
    //console.log(childs_and_ages);  
    
    for(i=0; i < json_rooms_list.length;i++) {
      //rchilds_ages_list=[];
      found=false;
      for(j=0; j < json_rooms_list[i].rchilds_ages_list.length;j++) {
        for(k=0; k < childs_and_ages.length;k++) {
          if (json_rooms_list[i].rchilds_ages_list[j].index == (k + 1)) {
            json_rooms_list[i].rchilds_ages_list[j].age = childs_and_ages[k].age;
            found=true;
            break; 
          }
        } 
        if (found == false) {
          json_rooms_list[i].rchilds_ages_list[j].age=-1;
        }      
      }
    }
    
    calc_pliroteo();
  }
  
  $('.childs_ages_list_select').change(childs_ages_list_select_change);

  $('#dialog_room_num_adults').on('input propertychange change', function(event) {
    myval1 = myparseInt($('#dialog_room_num_adults').val());
    myval2 = myparseInt($('#dialog_room_num_childs').val());
    
    new_visitors=dialog_room.visitors;
    new_visitors_max=dialog_room.visitors_max;
    extra_beds=myparseInt($('#dialog_room_num_extra_beds').val());
    new_visitors+=extra_beds;
    new_visitors_max+=extra_beds;
    
    if (myval1 > new_visitors) {
      myval1=new_visitors;
      $('#dialog_room_num_adults').val((myval1).toString());
    }
    if ((myval1 + myval2) > new_visitors_max) {
      myval2 = new_visitors_max - myval1;
      if (myval2<0) myval2=0;
      $('#dialog_room_num_childs').val((myval2).toString());
    }
    rchilds_ages_list_html(myval2,dialog_room.rchilds_ages_list, false, true);
    fields_change[dialog_room.aa]='gks_ekptosi';
    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
  });

  function dialog_room_num_childs_change() {
    myval1 = myparseInt($('#dialog_room_num_adults').val());
    myval2 = myparseInt($('#dialog_room_num_childs').val());
    
    new_visitors=dialog_room.visitors;
    new_visitors_max=dialog_room.visitors_max;
    extra_beds=myparseInt($('#dialog_room_num_extra_beds').val());
    new_visitors+=extra_beds;
    new_visitors_max+=extra_beds;
    
    if (myval2 > new_visitors_max) {
      myval2=new_visitors_max;
      $('#dialog_room_num_childs').val((myval2).toString());
    }
    if ((myval1 + myval2) > new_visitors_max) {
      myval1 = new_visitors_max - myval2;
      if (myval1<0) myval1=0;
      $('#dialog_room_num_adults').val((myval1).toString());
    }
    rchilds_ages_list_html(myval2,dialog_room.rchilds_ages_list, false, true);
    fields_change[dialog_room.aa]='gks_ekptosi';
    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
    
  }
  $('#dialog_room_num_childs').on('input propertychange change', function(event) {
    dialog_room_num_childs_change();
  });
  
  $('#dialog_room_num_child_kounies').on('input propertychange change', function(event) {
    fields_change[dialog_room.aa]='gks_ekptosi';
    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
    
  });
  
  $('#dialog_room_num_extra_beds').on('input propertychange change', function(event) {

    extra_beds=parseInt($('#dialog_room_num_extra_beds').val());
		$('#dialog_room_num_adults').attr('max', (dialog_room.visitors     + extra_beds).toString());
	  $('#dialog_room_num_childs').attr('max', (dialog_room.visitors_max + extra_beds).toString());

    dialog_room_num_childs_change();		
//		vvv=parseInt($('#dialog_room_num_adults').val());
//		if (vvv > (dialog_room.visitors     + extra_beds)) {
//		  $('#dialog_room_num_adults').val(dialog_room.visitors     + extra_beds);
//		}
//		vvv=parseInt($('#dialog_room_num_childs').val());
//		if (vvv > (dialog_room.visitors_max + extra_beds)) {
//		  $('#dialog_room_num_childs').val(dialog_room.visitors_max + extra_beds);
//		}
		
//    fields_change[dialog_room.aa]='gks_ekptosi';
//    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
    
  });
  
  
  
  
  function rchilds_ages_list_html(rnum_childs,rchilds_ages_list_org, set_org_vals, keep_exist) {
    tmp_rchilds_ages_list = JSON.parse(JSON.stringify(rchilds_ages_list_org)); //CLONE Array.from(rchilds_ages_list_org);

    
    if (keep_exist) {
      $('.rchilds_ages_list_item').each(function(index ) {
        ci=parseInt($(this).val());
        if (isNaN(ci)) ci=-1;
        if (ci>0) keep_exist_array[index]=ci;
      });
    }
    //console.log('keep_exist_array');
    //console.log(keep_exist_array);
        
    other_rooms_ages_index=[];
    for (i = 0; i < json_rooms_list.length; i++) {
      if (dialog_room.aa != json_rooms_list[i].aa && json_rooms_list[i].delete == 0) {
        for(j=0; j<json_rooms_list[i].rchilds_ages_list.length;j++) {
          other_rooms_ages_index.push(json_rooms_list[i].rchilds_ages_list[j].index);
        }
      }
    }
    
    childs_and_ages=[];
    $('.childs_ages_list_select').each(function(index) {
      val=parseInt($(this).val());
      if (isNaN(val)) val=-1;
      mytxt= $(this).find('option:selected').text();
      if (mytxt=='') mytxt=gks_lang('[n] παιδί').replace('[n]',gks_n_ho(index + 1));
      childs_and_ages.push({age:val, txt:mytxt});
    });
   
    //console.log('other_rooms_ages_index');    
    //console.log(other_rooms_ages_index); 
    //console.log('tmp_rchilds_ages_list');
    //console.log(tmp_rchilds_ages_list);
    //console.log('childs_and_ages');
    //console.log(childs_and_ages);

    myhtml='';
    for (ci=1; ci <= rnum_childs; ci++) {
      myhtml+='<div class="form-group row">' + 
        '<label for="rchilds_ages_list_item_' + ci + '" data-aa="' + ci + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + 
        gks_lang('[n] παιδί').replace('[n]',gks_n_ho(ci)) +
        '</label>' +
        
        '<div class="col-md-8">' +
          '<select id="rchilds_ages_list_item_' + ci + '" data-aa="' + ci + '" class="rchilds_ages_list_item form-control form-control-sm">' +
          '<option value="-1"></option>';
      this_ok=false;
      for (cit=0;cit<childs_and_ages.length;cit++) {
        myhtml+='<option data-age="' + childs_and_ages[cit].age + '" value="' + (cit + 1).toString() + '"';
        if (other_rooms_ages_index.includes(cit + 1)) myhtml+=' disabled';
        if (set_org_vals && this_ok == false) {
          found=false;
          for(citc=0; citc<tmp_rchilds_ages_list.length;citc++) {
            if (tmp_rchilds_ages_list[citc].index == (cit + 1)) {
              tmp_rchilds_ages_list[citc].index=-1; 
              found=true; this_ok=true; break;
            }
          }
          if (found) myhtml+=' selected ';
        } else if (keep_exist) {
          if ((ci-1) < keep_exist_array.length) {
            if (keep_exist_array[ci-1] == (cit + 1)) {
              myhtml+=' selected ';
            }
          }
          
        }
        myhtml+='>' + childs_and_ages[cit].txt + '</option>';
      } 
      myhtml+='</select>' +
        '</div>' +
      '</div>';
    }
    $('#dialog_room_rchilds_ages_list_main_div').html(myhtml);
    $('.rchilds_ages_list_item').change(rchilds_ages_list_item_change);
    //console.log(childs_and_ages);

        
    
//    tmp_all=[];
//    for (cit=0;cit<childs_and_ages.length;cit++) {
//      tmp_all.push({age:childs_and_ages[cit].age, found:false});
//    }
//    //console.log(tmp_all);

   
    
    rchilds_ages_list_item_warning();
  }
  
  var warning_index=[];
  function rchilds_ages_list_item_warning() {
    var warning_exist=[];
    $('.rchilds_ages_list_item').each(function(index ) {
      $(this).css('background-color','unset');
      ci=parseInt($(this).val());
      if (isNaN(ci)) ci=-1;
      warning_exist[index]=ci;
      
    });
    warning_index=[];
    for (i=0; i < warning_exist.length; i++) {
      for (j=i + 1; j < warning_exist.length; j++) {
        if (warning_exist[i] > 0 && warning_exist[i] == warning_exist[j]) {
          if (warning_index.includes(i) == false) warning_index.push(i);
          if (warning_index.includes(j) == false) warning_index.push(j);
        }
      }
    }
    //console.log(warning_exist);
    //console.log(warning_index);
    //background-color: ;
    for (i=0; i < warning_index.length; i++) {
      $('#rchilds_ages_list_item_' + (warning_index[i] + 1)).css('background-color','rgba(255, 0, 0, 0.1)');
    }
  }
  
  function rchilds_ages_list_item_change() {
    //aa = parseInt($(this).attr('data-aa'));
    //console.log(aa);
    //console.log(dialog_room.rchilds_ages_list);
    rchilds_ages_list_item_warning();
    
    room_id=parseInt($('#dialog_room_room_id').val());
    if (isNaN(room_id)) room_id=-1;
    if (room_id<0) return;
    fields_change[dialog_room.aa]='gks_ekptosi';
    mysubmit('','calc_dialog_room', 'gks_ekptosi', dialog_room.aa, '', '');
    return;
//    
//    datasend='';
//    datasend+='&roomid=' + room_id;
//    datasend+='&rsvid=' + from_php_id;
//    datasend+='&check_in=' + encodeURIComponent($('#mypostform #check_in').val().trim());
//    datasend+='&check_out='  + encodeURIComponent($('#mypostform #check_out').val().trim())
//    datasend+='&rnum_adults='  + encodeURIComponent($('#dialog_room_num_adults').val().trim())
//    datasend+='&rnum_childs='  + encodeURIComponent($('#dialog_room_num_childs').val().trim())
//    datasend+='&rchilds_ages_list='  + encodeURIComponent($.base64.encode(JSON.stringify(dialog_room_room_descr_autocomplete_rchilds_ages_list())))
//    
//    $.ajax({
//			url: '/my/admin-hotel-reservation-item-room-select.php',
//			type: 'POST',
//			cache: false,
//			dataType: 'json',
//			data: datasend,
//			error : function(jqXHR ,textStatus,  errorThrown) {
//				myalert('error:' + jqXHR.responseText);
//			},				
//			success: function(data) {
//				if (!data) {
//					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
//				} else {
//					if (data.success == true) {
//
//					  $('#dialog_room_ajia_math').html($.base64.decode(data.msg_price));
//					  $('#dialog_room_ajia_total').val(data.ajia_total_val);
//					  if ($('#dialog_room_ajia_table').hasClass('tooltipstered')) $('#dialog_room_ajia_table').tooltipster('destroy');
//					  
//					  $('#dialog_room_ajia_table').show();
//					  $('#dialog_room_ajia_table').tooltipster({
//					    theme: 'tooltipster-noir',
//					    contentAsHTML: true,
//					    interactive:true,
//					    content: data.roomaf_html,
//					  });
//					  //console.log(data.roomaf_html);
//					  dialog_room_ajia_table_math=$.base64.decode(data.msg_price);
//					  dialog_room_ajia_table_html=data.roomaf_html;
//					  dialog_room_ajia_table_array=data.roomaf_array;
//					  dialog_room_other_taxes_tooltip=data.roomaf_other_taxes_tooltip;
//					  
//					  dialog_room_product_price_start_all_total=data.ajia_total_val;
//					  dialog_room_ekptosi_pososto_change();
//					  
//					} else {
//						myalert('error:' + $.base64.decode(data.message));
//					}
//				}
//			}
//    }); 
  }
  

  function user_notes_change() {gks_resize_textarea($(this));}
  $('#user_notes').on(mychange, user_notes_change);
  gks_resize_textarea($('#user_notes'));
  
  function sxolio_change() {gks_resize_textarea($(this));}
  $('#sxolio').on(mychange, sxolio_change);
  gks_resize_textarea($('#sxolio'));
  
  function note_logistirio_change() {gks_resize_textarea($(this));}
  $('#note_logistirio').on(mychange, note_logistirio_change);
  gks_resize_textarea($('#note_logistirio'));


  $('#affect_balance').change(function() {
    if ($('#affect_balance').is(':checked')) {
      $('#div_affect_balance_all_poso').show();
      if ($('#affect_balance_all_poso').is(':checked')) {
        $('#div_affect_balance_poso').hide();
        $('#small_affect_balance_all_poso').show();
      } else {
        $('#div_affect_balance_poso').show();
        $('#small_affect_balance_all_poso').hide();
      }
    } else {
      $('#div_affect_balance_all_poso').hide();
      $('#div_affect_balance_poso').hide();
    }
    balance_user_after_calc();
  });
  $('#affect_balance_all_poso').change(function() {
    if ($('#affect_balance_all_poso').is(':checked')) {
      $('#div_affect_balance_poso').hide();
      $('#small_affect_balance_all_poso').show();
    } else {
      $('#div_affect_balance_poso').show();
      $('#small_affect_balance_all_poso').hide();
    }
    balance_user_after_calc();
  });
  $('input[name=affect_balance_all_poso_type]').change(balance_user_after_calc);

  $('#affect_balance_poso').on(mychange ,balance_user_after_calc);
    
  function balance_user_after_calc() {
    before=parseFloat($('#balance_user_before').attr('data-val'));
    if (isNaN(before)) before=0;
    after=before;
    prosimo=from_php_eidos_parastatikou_balance_pros;
    if (prosimo == -100) { //akirotiko
      prosimo=0;
    } else {
      if (prosimo!= 1 && prosimo!= -1) prosimo=0;
    }
        
    if (1==1 || from_php_reservation_status=='070wait_payment' || from_php_reservation_status=='080confirm' || from_php_reservation_status=='100completed') {
      if ($('#affect_balance').is(':checked')) {
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
          after+=prosimo*poso;
        } else {
          poso=parseFloat($('#affect_balance_poso').val());
          if (isNaN(poso)) poso=0;
          after+=prosimo*poso;
        }
      }
    }
    $('#balance_user_after').html(after.mymoney());
  }
  if (!(from_php_reservation_status=='070wait_payment' || from_php_reservation_status=='080confirm' || from_php_reservation_status=='100completed')) {
    balance_user_after_calc();
  }  
  
  
  $('#assigned_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
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
      $('#assigned_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#assigned_id').val('').attr('data-id','0');
        }
    }
  });
  
  $('#crm_channel_id').change(function() {
    has_contact = parseInt($('#crm_channel_id option:selected').attr('data-contact'));
    has_campain = parseInt($('#crm_channel_id option:selected').attr('data-campain'));
    has_url = parseInt($('#crm_channel_id option:selected').attr('data-url'));
    has_code = parseInt($('#crm_channel_id option:selected').attr('data-code'));
    has_text = parseInt($('#crm_channel_id option:selected').attr('data-text'));
    //console.log(has_text,has_contact,contact_filter,has_campain,has_url);
    if (has_contact==0) $('#crm_channel_contact_id_div').slideUp(); else $('#crm_channel_contact_id_div').slideDown();
    if (has_campain==0) $('#crm_channel_campain_id_div').slideUp(); else $('#crm_channel_campain_id_div').slideDown();
    if (has_url==0) $('#crm_channel_url_div').slideUp(); else $('#crm_channel_url_div').slideDown();
    if (has_code==0) $('#crm_channel_code_div').slideUp(); else $('#crm_channel_code_div').slideDown();
    if (has_text==0) $('#crm_channel_text_div').slideUp(); else $('#crm_channel_text_div').slideDown();
    
  });
  


  $('#crm_channel_contact_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      contact_filter = $.base64.decode($('#crm_channel_id option:selected').attr('data-contact_filter'));
      if (contact_filter!='') {
        parts=contact_filter.split('&');
        for (i=0;i<parts.length;i++) {
          parts2=parts[i].split('=');
          if (parts2.length==2) {
            mydata[parts2[0]]=parts2[1];
          }
        }
      }
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
      need_save=true;
      $('#crm_channel_contact_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_contact_id').val('').attr('data-id','0');
      }
    }
  });
  
  $('#crm_channel_campain_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-campain.php',
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
      $('#crm_channel_campain_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_campain_id').val('').attr('data-id','0');
      }
    }
  });

  function crm_channel_text_change() {gks_resize_textarea($(this));}
  $('#crm_channel_text').on('change keyup paste', crm_channel_text_change);
  if ($('#crm_channel_text').length>0) gks_resize_textarea($('#crm_channel_text'));
  
  
  
  
  $('#submit_button_create_acc_inv').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την κράτηση'));
      return;
    }
    myconfirm(gks_lang('Σίγουρα θέλετε να δημιουργήσετε σχετικά παραστατικά για την τρέχον κράτηση;'),
    'gks_mysubmit_create_acc_inv');
    return false;
  });
  window.gks_mysubmit_create_acc_inv = function() {
    mysubmit('create_acc_inv');
  }
  
  $('#submit_button_create_acc_pay').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την κράτηση'));
      return;
    }
    myconfirm(gks_lang('Σίγουρα θέλετε να δημιουργήσετε σχετική πληρωμή για την τρέχον κράτηση;'),
    'gks_mysubmit_create_acc_pay');
    return false;
  });  
  window.gks_mysubmit_create_acc_pay = function() {
    mysubmit('create_acc_pay');
  }


  $('#add_links_url').click(function(event) {  
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την κράτηση'));
      return;
    }
    
    links_url=$("#links_url").val().trim();
    if (links_url=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε πρώτα την διεύθυνση'));
      return;
    }
    
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&link='  + encodeURIComponent($.base64.encode(links_url));    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-reservation-item-add-link.php',
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
  					myhtml=$.base64.decode(data.html);
  					//console.log(myhtml);
  					$('#tr_new_links_url').before(myhtml);

            $('#links_url').val('');
            $('#tr_links_url_' + data.trid).find('.deleterow').click(deleterow_click); 
  					$('#tr_links_url_' + data.trid).find('.download_action_start').click(download_action_start_click);

            var links_aa=0;
            $('#links_table .links_aa').each(function () {
              links_aa++;
              $(this).html(links_aa);  
            }); 

  					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  });

  window.gks_fnc_links_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('#tr_links_url_' + myargs[0]).hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var links_aa=0;
      $('#links_table .links_aa').each(function () {
        links_aa++;
        $(this).html(links_aa);  
      });    
    });
  }  





























   
  
  
  function download_action_start_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'start');
  }
  $('.download_action_start').click(download_action_start_click);
  
  function download_action_stop_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'stop');
  }
  $('.download_action_stop').click(download_action_stop_click);

  function download_action_reset_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'reset');
  }
  $('.download_action_reset').click(download_action_reset_click);

  $('.download_action_complete').click(download_action_reset_click);
  
  
  function download_action_func(data_id, myaction) {
    //console.log(data_id);
    //console.log(myaction);

    datasend='&id=' + data_id + '&action=' + myaction;
    
    $.ajax({
			url: '/my/admin-hotel-reservation-item-link-action.php',
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
  					if (data.action=='start') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-stop-circle download_action_stop" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>'); 
  					  $('.download_action_stop[data-id=' + data.id + ']').click(download_action_stop_click);
  					  $('.download-perc[data-id=' + data.id + ']').show();
  					  
  					  download_timer_start();
  					  
  					} else if (data.action=='stop') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
  					  $('.download_action_reset[data-id=' + data.id + ']').click(download_action_reset_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').html(gks_lang('Ακυρώθηκε από τον χρήστη')).show();
  					  download_timer_start();
  					} else if (data.action=='reset') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-file-download download_action_start" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>'); 
  					  $('.download_action_start[data-id=' + data.id + ']').click(download_action_start_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').hide();
  					  download_timer_start();
  					  
  					  
  					}
  					
  					
  					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  }
  
  var download_timer=null;
  
  
  function download_timer_run() {
    fff=new Date();
    //console.log(fff);
    $.ajax({
			url: '/my/admin-hotel-reservation-item-link-timer.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: '',
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				download_timer = setTimeout(download_timer_run, 2000);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					download_timer = setTimeout(download_timer_run, 2000);
				} else {
				  
					if (data.success == true) {
  					//console.log(data);
            for(i=0;i<data.data.length;i++) {
              $('.download_size_until_now[data-id=' + data.data[i].id + ']').html(data.data[i].now);
              if (data.data[i].status==2) { //ok exei teleiosei
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
                  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
                  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-check-circle download_action_complete" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>');
      					  $('.download-message[data-id=' + data.data[i].id + ']').hide();
      					  $('.download_action_complete[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
                }
              } else if (data.data[i].status==1) { //status =1
                $('.download-perc-bar[data-id=' + data.data[i].id + ']').attr('aria-valuenow', data.data[i].per).css('width', data.data[i].per + '%');
              } else if (data.data[i].status==3) { 
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
      					  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
      					  $('.download_action_reset[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
      					  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
      					  $('.download-message[data-id=' + data.data[i].id + ']').show();  
      					  $('.download-message[data-id=' + data.data[i].id + ']').html(data.data[i].msg);
      					}
              }
            }

  					
  					
  					//for(i=0;i<data.complete_td.length;i++) {
  					for(i=0;i<data.complete_td.length;i++) {
  					  if ($('.tddd[data-path="' + data.complete_td[i].relpath + '"]').length==0) {
  					    //console.log('|' + data.complete_td[i].relpath + '|');
  					    $('#filesobjectlist_table_imagelist_photo').show();
                //$('#filesobjectlist_table_imagelist_photo tr:last').after(data.complete_td[i].td);
                if ($('#filesobjectlist_table_imagelist_photo tbody tr').length==0) {
                  $('#filesobjectlist_table_imagelist_photo tbody').append(data.complete_td[i].td);
                } else {
                  $('#filesobjectlist_table_imagelist_photo tbody tr:last').after(data.complete_td[i].td);
                }                
                
                $('.filesobjectlist_delete_upload_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_delete_upload_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] td.tdimg_descr').click(filesobjectlist_edit_descr_click);
                $('.filesobjectlist_set_print_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_set_print_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
  
                $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
                $("#filesobjectlist_table_imagelist_photo").lightGallery({
                	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
                	thumbnail:true,
                	hideBarsDelay:1000,
                });   					    
  					  }		  
  					}

  					if (data.data.length<=0) {
  					  download_timer_stop();
  					} else {
  					  //setTimeout(, 3000);
  					  download_timer = setTimeout(download_timer_run, 2000);
  					}  					  					
  					//console.log(data);
  					  
					} else {
					  download_timer = setTimeout(download_timer_run, 2000);
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  
  function download_timer_stop() {
    clearTimeout(download_timer);
    download_timer=null;
  }
  function download_timer_start() {
    if (download_timer!=null) return;
    //download_timer = setInterval(download_timer_run, 1000);
    download_timer = setTimeout(download_timer_run, 2000);
    
  }
  if (from_php_need_download_timer==1) {
    download_timer_start();
  }




  function calc_efd() {
    efd=parseFloat($('#hotel_id option:selected').attr('data-efd')); if (isNaN(efd)) efd=0;
    num_days=parseInt($('#num_days').val()); if (isNaN(num_days)) num_days=0;
    rooms_plithos=0;
    for (i = 0; i < json_rooms_list.length; i++) {
      if (json_rooms_list[i].delete == 0) {
        rooms_plithos++;
      }
    }
    
    efd=efd*num_days*rooms_plithos;
    $('#gks_efd').html(
      (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
      efd.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
      (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : ''));
  }












  var dialog_print_zoom_slider_handle = $('#dialog_print_zoom_slider_handle');
  $('#dialog_print_zoom_slider').slider({
    min: 10,
    max: 200,
    value: 100,
    create: function() {
      dialog_print_zoom_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      dialog_print_zoom_slider_handle.text( ui.value + '%' );
    }
  });
    
  
  var dialog_print;
  dialog_print = $('#dialog_print').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_print_preview",
        html: '<i class="fa fa-print"></i> '+gks_lang('Προεπισκόπηση'),
        //icon: "ui-icon-print",  
        click: function() {
          dialog_print_button(true);
        }
      },
      {
        id: "dialog_print_ok",
        html: '<i class="fa fa-print"></i> '+gks_lang('Εκτύπωση'),
        //icon: "ui-icon-print",
        click: function() {
          dialog_print_button(false);
        }
      },
      {
        id: "dialog_print_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
    create: function( event, ui ) {
      $('#dialog_print_preview').before(
        '<span class="ui-button ui-corner-all-1 ui-widget" style="margin-right: 20px;">' +
        '<input type="checkbox" id="dialog_print_set_def" style="cursor:pointer;vertical-align: top;"> ' +
        '<label for="dialog_print_set_def" style="cursor:pointer;margin: 0px;margin-right: 20px;">'+gks_lang('Αποθήκευση ως προεπιλογές')+'</label>' +
        '</span>');
      
    },
    
  });
  
  function dialog_print_button(ispreview) {
    
    from_php_print_def_file_type='pdf';
    if ($('#dialog_print_file_type_pdf').prop('checked')) from_php_print_def_file_type='pdf';
    else if ($('#dialog_print_file_type_html').prop('checked')) from_php_print_def_file_type='html';
    else if ($('#dialog_print_file_type_jpg').prop('checked')) from_php_print_def_file_type='jpg';
    
    if ($('#dialog_print_grayscale_on').prop('checked')) from_php_print_def_grayscale=true;
    else if ($('#dialog_print_grayscale_off').prop('checked')) from_php_print_def_grayscale=false;
    
    if ($('#dialog_print_landscape_on').prop('checked')) from_php_print_def_landscape=true;
    else if ($('#dialog_print_landscape_off').prop('checked')) from_php_print_def_landscape=false;
    
    from_php_print_def_zoom=parseInt($('#dialog_print_zoom_slider').slider('value'));
    if (isNaN(from_php_print_def_zoom)) from_php_print_def_zoom=100;
    
    datasend='';
    datasend+='&file_type=' + from_php_print_def_file_type;
    datasend+='&grayscale=' + (from_php_print_def_grayscale ? '1' : '0');
    datasend+='&landscape=' + (from_php_print_def_landscape ? '1' : '0');
    datasend+='&zoom=' + from_php_print_def_zoom;
    datasend+='&preview=' + (ispreview ? '1' : '0');
    datasend+='&set_def=' + ($('#dialog_print_set_def').is(':checked') ? '1' : '0');
    if ($('#gks_print_send_gks_erp_app').length>0) {
      datasend+='&gks_print_send_gks_erp_app=' + ($('#gks_print_send_gks_erp_app').is(':checked') ? '1' : '0');  
    }
    
    form_id=0;
    elem_sel=$('.gks_print_thump_div_selected');
    if (elem_sel.length<=0) {
      myalert('error:'+gks_lang('Επιλέξτε την φόρμα εκτύπωσης'));
      return;
    }
    form_id=parseInt(elem_sel.attr('data-form_id'));
    if (isNaN(form_id)) form_id=0;
    datasend+='&form_id=' + form_id;
    
    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    $.ajax({
			url: 'admin-hotel-reservation-item-pdf.php?id=' + from_php_id,
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
					  if (data.preview_url!='') {
					   
              var preview_win = window.open(data.preview_url, '_blank');
              //console.log('preview_win');
              //console.log(preview_win);
              if (preview_win==null) {
                myalert('ok:'+gks_lang('Η προεπισκόπηση έχει δημιουργηθεί, αλλά δεν ήταν δυνατόν να ανοίξει σε άλλη καρτέλα')+'<br>' +
                gks_lang('Μπορείτε να το ανοίξετε από τον παρακάτω σύνδεσμο')+':<br>' +
                '<a href="' + data.preview_url + '" class="gks_link" class="gks_">'+gks_lang('σύνδεσμος')+'</a>');
              } else {
                preview_win.focus();
              }
        
					    
					  } else {
    					if (data.save_but_message!='') {
    					  if ($.base64.decode(data.message)=='ok') {
    					    myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					  } else {
    					    myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					  }
    					} else {
      					if (data.redirect=='') {
      					  window.location.reload();
      					} else {
      					  window.location.href = $.base64.decode(data.redirect);
      					}
      				}
      			}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }

  $('#submit_button_print').click(function() {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την κράτηση'));
      return;
    }
    $('#dialog_print_preview').prop('checked', false);
    if (from_php_print_def_file_type=='pdf') $('#dialog_print_file_type_pdf').prop('checked', true);
    else if (from_php_print_def_file_type=='html') $('#dialog_print_file_type_html').prop('checked', true);
    else if (from_php_print_def_file_type=='jpg') $('#dialog_print_file_type_jpg').prop('checked', true);
    if (from_php_print_def_grayscale) $('#dialog_print_grayscale_on').prop('checked', true);
    else $('#dialog_print_grayscale_off').prop('checked', true);
    if (from_php_print_def_landscape) $('#dialog_print_landscape_on').prop('checked', true);
    else $('#dialog_print_landscape_off').prop('checked', true);
    $('#dialog_print_zoom_slider').slider('option', 'value', from_php_print_def_zoom);
    dialog_print_zoom_slider_handle.text($('#dialog_print_zoom_slider').slider('value') + '%');
    
    if (from_php_print_def_file_type=='pdf') {
      $('#dialog_print_zoom_slider').slider('enable');
    } else if (from_php_print_def_file_type=='html') {
      $('#dialog_print_zoom_slider').slider('disable');
    } else if (from_php_print_def_file_type=='jpg') {
      $('#dialog_print_zoom_slider').slider('enable');
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  if (dheight> 650) dheight=650;
	  dialog_print.dialog('option', 'width', dwidth);
	  dialog_print.dialog('option', 'height', dheight);
	  $('#dialog_print').parent().css({position:'fixed'});
    dialog_print.dialog('open'); 
    
    $('.gks_print_thump_div_selected').removeClass('gks_print_thump_div_selected');
    

    sel_company_id_sub_id=$('#hotel_id option:selected').attr('data-company_id_sub_id');
    sel_reservation_journal_id=parseInt($('#reservation_journal_id').val()); if (isNaN(sel_reservation_journal_id)) sel_reservation_journal_id=0;
    sel_reservation_seira_id=parseInt($('#reservation_seira_id').val()); if (isNaN(sel_reservation_seira_id)) sel_reservation_seira_id=0;
    
    var temp=$('#dr_user_lang').val();
    if (temp=='') temp='el-GR';
    var temp_cc=0;
    var temp_hh=0;
    if (temp!='') {
      $('.gks_print_thump_div').each(function() {
        if ($(this).attr('data-lang')==temp) {
          data_form_id=parseInt($(this).attr('data-form_id')); if (isNaN(data_form_id)) data_form_id=0;
          will_show=true;
          for (i=0;i<from_php_perm_print_forms.length;i++) {
            if (from_php_perm_print_forms[i].id == data_form_id) {
              if (typeof(from_php_perm_print_forms[i].perm_company_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_company_ids.includes(sel_company_id_sub_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_journal_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_journal_ids.includes(sel_reservation_journal_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_seires_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_seires_ids.includes(sel_reservation_seira_id)==false) {
                  will_show=false;
                  break;
                }
              }
            }
          }
          
          if (will_show) {
            $(this).show();
            temp_cc++;
          } else {
            $(this).hide();
          }
        } else {
          $(this).hide();
          temp_hh++;
        }
      });
//      if (temp_cc==0) {
//        $('.gks_print_thump_div').show();
//      }
            
      if (from_php_print_def_forms[temp]!==undefined && $('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').length>0) {
        if ($('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').css('display')!='none') {
          $('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').addClass('gks_print_thump_div_selected');
        }
      }

      if ($('.gks_print_thump_div_selected').length==0) {
        temp_cc=0;
        $('.gks_print_thump_div').each(function() {
          if ($(this).css('display')!='none') temp_cc++;
        });
        if (temp_cc==1) {
          $('.gks_print_thump_div').each(function() {
            if ($(this).css('display')!='none') {
              $(this).addClass('gks_print_thump_div_selected');
              return; 
            }
          });          
        }
      }
      
      temp_cc=0;
      $('.gks_print_thump_div').each(function() {
        if ($(this).css('display')=='none') temp_cc++;
      });
      
      if (temp_cc==0) {
        $('#gks_print_thump_more_div').hide();
      } else {
        $('#gks_print_thump_more_div').show();
      }
      
    }
    
    fff=$('.gks_print_thump_div_selected');
    if (fff.length>0) {
      //document.getElementById('gggggg').scrollIntoView({block: "nearest",inline : 'nearest'});
      fff[0].scrollIntoView({block: "nearest",inline : 'nearest'});
    }
    
  });    
  
  $('#gks_print_thump_more_div').click(function() {
    $('.gks_print_thump_div').show();
    $('#gks_print_thump_more_div').hide();
  });
  

  
  $('input[name=dialog_print_file_type]').click( function() {
    val=$(this).val();
    //console.log(val);
    if (val==1) {
      $('#dialog_print_zoom_slider').slider('enable');
    } else if (val==2) {
      $('#dialog_print_zoom_slider').slider('disable');
    } else if (val==3) {
      $('#dialog_print_zoom_slider').slider('enable');
    }
  });
  
  
  $('.gks_print_thump_div').click(function() {
    form_id=parseInt($(this).attr('data-form_id'));
    if (isNaN(form_id)) form_id=0;
    if (form_id<=0) return;
    $('.gks_print_thump_div').each(function() {
      $(this).removeClass('gks_print_thump_div_selected');  
    });
    $(this).addClass('gks_print_thump_div_selected');
    
    val=$(this).attr('data-file_type');
    if (val=='pdf') $('#dialog_print_file_type_pdf').click();
    else if (val=='html') $('#dialog_print_file_type_html').click();
    else if (val=='jpg') $('#dialog_print_file_type_jpg').click();
   
    val=$(this).attr('data-landscape');
    if (val=='0') $('#dialog_print_landscape_off').click();
    else if (val=='1') $('#dialog_print_landscape_on').click();
    
    val=$(this).attr('data-grayscale');
    if (val=='0') $('#dialog_print_grayscale_off').click();
    else if (val=='1') $('#dialog_print_grayscale_on').click();
    
    val=$(this).attr('data-zoom');
    $('#dialog_print_zoom_slider').slider('option', 'value', val);
    dialog_print_zoom_slider_handle.text($('#dialog_print_zoom_slider').slider('value') + '%');
    
    
    
    
  });

  $('#dr_user_doy').autocomplete({
    source: "doy-autocomplete.php",
    minLength: 1,
    autoFocus: true,
    select: function( event, ui ) {
      $("#dr_user_doy").val(ui.item.value);
    },
  }); 
  
  ///////////////////////////////////////////////////////// pre end
  // last of all 

  
  if (from_php_id == -1 && from_php_template_id==0) {
    temp=$('#hotel_id').attr('data-company_id_sub_id');
    if (temp!='' && temp!='0|0') {
      hotel_id_change();
    } else if ($('#hotel_id option').length==1) {
      //$('#hotel_id').val($($('#hotel_id option')[1]).attr('value'));
      //hotel_id_change();
    }
  }
  
  if (from_php_id==-1 && from_php_template_id>0) {
    kostos_apostolis_mode='manual';
    kostos_pliromis_mode='manual';
    calc_pliroteo();
  }

  gks_address_autocomplete('dr_user_ma_odos',         'dr_user_ma_arithmos',          'dr_user_ma_orofos',          'dr_user_ma_perioxi',         'dr_user_ma_poli',          'dr_user_ma_tk',          'dr_user_ma_nomos_id',          'dr_user_ma_country_id',          '','',true);
  gks_address_autocomplete('dialog_room_user_ma_odos','dialog_room_user_ma_arithmos', 'dialog_room_user_ma_orofos', 'dialog_room_user_ma_perioxi','dialog_room_user_ma_poli', 'dialog_room_user_ma_tk', 'dialog_room_user_ma_nomos_id', 'dialog_room_user_ma_country_id', '','',true);

  //generic
  
  if (from_php_temp_mypropertiesheight!=0) {
    $('html').scrollTop(from_php_temp_mypropertiesheight);
  }
  
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
	  
});
