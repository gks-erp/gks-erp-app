/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var run_from_steps=false;
var run_from_step_run='';

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
var gks_page_loading=true;

jQuery(document).ready(function($) {

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (from_php_perm_ret_edit==false) return;
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.css('display')!='none') {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });
  
  for(i=0;i<eidi_parastatikon_types.length;i++) {
    eidi_parastatikon_types[i].descr=$.base64.decode(eidi_parastatikon_types[i].descr);
    eidi_parastatikon_types[i].label=$.base64.decode(eidi_parastatikon_types[i].label);
  }
  for(i=0;i<aade_entitytype.length;i++) {
    aade_entitytype[i].descr=$.base64.decode(aade_entitytype[i].descr);
  }




  $('#mov_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));  
  $('#dispatch_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));  

  $('#dispatch_time').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'29:59',format:'H:i', step:15,timepicker:true,datepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));  


  
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
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      old_val=$("#user_id").val();
      $("#user_id").val(ui.item.id);
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();
      $('#user_save').hide();
      
      
      
      gks_admin_get_user_data(ui.item.id, false);
    
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#user").val('');
        $("#user_id").val('');

        $('#autocomplete_user_id').hide(); 
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
        
        $('#dr_user_ma_branch_fromuser').val('');
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
        
        $('#form_select_apostoli option').each(function() { 
          if ($(this).attr('value') > 0 ) {
            $(this).remove();
          }
        }); 
        $('#form_select_apostoli').val(-1);
        extra_address_select(-1);
        
        $('#fiscal_position_id').val(1);
        $('#pricelist_id').val(1);

        gks_myscroll(); 
        calc_pliroteo();             
      }
    }
  });

  


  $('.thisdeleterowbtn').click(function(event) {  
    if (from_php_perm_ret_edit==false) return; 
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
  $('#submit_button_080listing').click(function(event) {mysubmit('080listing'); return false;});
  $('#submit_button_090ekdosi').click(function(event) {mysubmit('090ekdosi'); return false;});

  
  $('#submit_button_010draft').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να επαναφέρετε το δελτίο αποστολής σε πρόχειρη κατάσταση;'),
    'gks_mysubmit_draft');
    return false;
  });
  window.gks_mysubmit_draft = function() {
    mysubmit('010draft');
  }
    
  $('#submit_button_040cancelled').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να ακυρώσετε το δελτίο αποστολής;')+'<br>'+gks_lang('Θα δημιουργηθεί ένα νέο ακυρωτικό δελτίο αποστολής το οποίο θα πρέπει να το εκδώσετε'),
    'gks_mysubmit_cancel');
    return false;
  });
  window.gks_mysubmit_cancel = function() {
    mysubmit('040cancelled');
  }
  
  $('#submit_button_credit_memo').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να δημιουργήσετε δελτίο επιστροφής για το τρέχον δελτίο;')+'<br>'+gks_lang('Θα δημιουργηθεί ένα νέο δελτίο αποστολής το οποίο θα πρέπει να το εκδώσετε'),
    'gks_mysubmit_credit_memo');
    return false;
  });
  window.gks_mysubmit_credit_memo = function() {
    mysubmit('credit_memo');
  }
  
  function mysubmit(mov_state = '') {
    if (from_php_perm_ret_edit==false) return;
    
    datasend='';
    datasend+='&gks_lock=' + (from_php_gks_lock ? '1' : '0');
    datasend+='&gks_number_lock=' + (from_php_number_gks_lock ? '1' : '0');
    datasend+='&gks_user_lock=' + (from_php_user_gks_lock ? '1' : '0');
    datasend+='&mov_state=' + encodeURIComponent($.base64.encode(mov_state));

    if (mov_state=='aade_send') {
      aade_mydata_live=($('#aade_mydata_live').is(':checked') ? 1 : 0);
      datasend+='&aade_mydata_live=' + aade_mydata_live;
    } else if (mov_state=='paroxos_send') {
      aade_mydata_live=($('#paroxos_mydata_live').is(':checked') ? 1 : 0);
      datasend+='&paroxos_mydata_live=' + aade_mydata_live;
    }

     
    if (from_php_gks_lock == false) {
      
      if ($("#company_id_sub_id").length > 0) datasend+='&company_id_sub_id=' + encodeURIComponent($.base64.encode($("#company_id_sub_id").val().trim()));
      if ($("#mov_whi_journal_id").length > 0) datasend+='&mov_whi_journal_id=' + encodeURIComponent($("#mov_whi_journal_id").val().trim());
      if ($("#mov_whi_seira_id").length > 0) datasend+='&mov_whi_seira_id=' + encodeURIComponent($("#mov_whi_seira_id").val().trim());
      if ($("#reverse_delivery_purpose").length > 0) datasend+='&reverse_delivery_purpose=' + encodeURIComponent($("#reverse_delivery_purpose").val().trim());
      if ($("#mov_whi_number_int").length > 0) datasend+='&mov_whi_number_int=' + encodeURIComponent($("#mov_whi_number_int").val().trim());
      datasend+='&aade_skopos_diakinisis_id=' +      encodeURIComponent($('#aade_skopos_diakinisis_id').val().trim());
      datasend+='&aade_skopos_19_descr='  + encodeURIComponent($.base64.encode($("#aade_skopos_19_descr").val().trim()));
      datasend+='&mov_date=' + encodeURIComponent($("#mov_date").val().trim());
      
      datasend+='&user_id=' + encodeURIComponent($("#user_id").val().trim());
      datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($("#dr_user_first_name").val().trim()));
      datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($("#dr_user_last_name").val().trim()));
      datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($("#dr_user_email").val().trim()));
      datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($("#dr_user_mobile").val().trim()));
      datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($("#dr_user_lang").val().trim()));
      datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_odos").val().trim()));
      datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_arithmos").val().trim()));
      datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_orofos").val().trim()));
      datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($("#dr_user_ma_perioxi").val().trim()));
      datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($("#dr_user_ma_poli").val().trim()));
      datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($("#dr_user_ma_tk").val().trim()));
      datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#dr_user_ma_country_id").val().trim());
      datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#dr_user_ma_nomos_id").val().trim());
      datasend+='&dr_user_eponimia='  + encodeURIComponent($.base64.encode($("#dr_user_eponimia").val().trim()));
      datasend+='&dr_user_title='  + encodeURIComponent($.base64.encode($("#dr_user_title").val().trim()));
      datasend+='&dr_user_afm='  + encodeURIComponent($.base64.encode($("#dr_user_afm").val().trim()));
      datasend+='&dr_user_doy='  + encodeURIComponent($.base64.encode($("#dr_user_doy").val().trim()));
      datasend+='&dr_user_epaggelma='  + encodeURIComponent($.base64.encode($("#dr_user_epaggelma").val().trim()));
  
  
      datasend+='&form_select_apostoli=' +  encodeURIComponent($('#form_select_apostoli').val().trim());
      datasend+='&form_ea_name=' +          encodeURIComponent($.base64.encode($('#form_ea_name').val().trim()));
      datasend+='&form_ea_phone=' +         encodeURIComponent($.base64.encode($('#form_ea_phone').val().trim()));
      datasend+='&form_ea_odos=' +          encodeURIComponent($.base64.encode($('#form_ea_odos').val().trim()));
      datasend+='&form_ea_arithmos=' +      encodeURIComponent($.base64.encode($('#form_ea_arithmos').val().trim()));
      datasend+='&form_ea_orofos=' +       encodeURIComponent($.base64.encode($('#form_ea_orofos').val().trim()));
      datasend+='&form_ea_perioxi=' +       encodeURIComponent($.base64.encode($('#form_ea_perioxi').val().trim()));
      datasend+='&form_ea_poli=' +          encodeURIComponent($.base64.encode($('#form_ea_poli').val().trim()));
      datasend+='&form_ea_tk=' +            encodeURIComponent($.base64.encode($('#form_ea_tk').val().trim()));
      if ($('#form_ea_country_id').val()==null)$('#form_ea_country_id').val(0);
      if ($('#form_ea_nomos_id').val()==null)$('#form_ea_nomos_id').val(0);
      datasend+='&form_ea_country_id=' +    encodeURIComponent($('#form_ea_country_id').val().trim());
      datasend+='&form_ea_nomos_id=' +      encodeURIComponent($('#form_ea_nomos_id').val().trim());
      datasend+='&fiscal_position_id=' +      encodeURIComponent($('#fiscal_position_id').val().trim());
      datasend+='&pricelist_id=' +      encodeURIComponent($('#pricelist_id').val().trim());

      datasend+='&warehouses_id_from=' + $("#mypostform #warehouses_id_from").attr('data-id');
      datasend+='&load_branch=' +     encodeURIComponent($('#load_branch').val().trim());
      datasend+='&load_odos=' +       encodeURIComponent($.base64.encode($('#load_odos').val().trim()));
      datasend+='&load_arithmos=' +   encodeURIComponent($.base64.encode($('#load_arithmos').val().trim()));
      datasend+='&load_orofos=' +     encodeURIComponent($.base64.encode($('#load_orofos').val().trim()));
      datasend+='&load_perioxi=' +    encodeURIComponent($.base64.encode($('#load_perioxi').val().trim()));
      datasend+='&load_poli=' +       encodeURIComponent($.base64.encode($('#load_poli').val().trim()));
      datasend+='&load_tk=' +         encodeURIComponent($.base64.encode($('#load_tk').val().trim()));
      datasend+='&load_country_id=' + encodeURIComponent($('#load_country_id').val().trim());
      datasend+='&load_nomos_id=' +   encodeURIComponent($('#load_nomos_id').val().trim());

    
      datasend+='&warehouses_id_to=' + $("#mypostform #warehouses_id_to").attr('data-id');
      datasend+='&deli_branch=' +     encodeURIComponent($('#deli_branch').val().trim());
      datasend+='&deli_odos=' +       encodeURIComponent($.base64.encode($('#deli_odos').val().trim()));
      datasend+='&deli_arithmos=' +   encodeURIComponent($.base64.encode($('#deli_arithmos').val().trim()));
      datasend+='&deli_orofos=' +    encodeURIComponent($.base64.encode($('#deli_orofos').val().trim()));
      datasend+='&deli_perioxi=' +    encodeURIComponent($.base64.encode($('#deli_perioxi').val().trim()));
      datasend+='&deli_poli=' +       encodeURIComponent($.base64.encode($('#deli_poli').val().trim()));
      datasend+='&deli_tk=' +         encodeURIComponent($.base64.encode($('#deli_tk').val().trim()));
      datasend+='&deli_country_id=' + encodeURIComponent($('#deli_country_id').val().trim());
      datasend+='&deli_nomos_id=' +   encodeURIComponent($('#deli_nomos_id').val().trim());
    
    }
    
    //datasend+='&price=' + encodeURIComponent($("#price").val().trim());
    datasend+='&note_doc=' + encodeURIComponent($.base64.encode($("#note_doc").val().trim()));
    datasend+='&note_logistirio=' + encodeURIComponent($.base64.encode($("#note_logistirio").val().trim()));
    

    d=$('input[name=radio_delivery_way]:checked');
    if (d.css('display')=='none') {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής'));
      return;
    }
    d=d.val();
    if (d === undefined || d === null) d=0;
    if (d<=0) {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής'));
      return;
    }
    
    
   
    
    delivery_id_8=0;
    if (d == 8) {
      if ($('#delivery_id_8').val() == 0) {
        myalert('error:' + gks_lang('Παρακαλώ επιλέξτε το κατάστημα που θέλετε να παραλάβετε τα προϊόντα σας'));
        return;  
      }
      delivery_id_8=$('#delivery_id_8').val();
    }
    
    datasend+='&tropos_apostolis=' + d;    
    datasend+='&delivery_id_8=' + delivery_id_8;    
    datasend+='&delivery_number=' + encodeURIComponent($.base64.encode($("#delivery_number").val().trim()));
    datasend+='&vehicle_number=' + encodeURIComponent($.base64.encode($("#vehicle_number").val().trim()));
    datasend+='&dispatch_date=' + encodeURIComponent($("#dispatch_date").val().trim());
    datasend+='&dispatch_time=' + encodeURIComponent($("#dispatch_time").val().trim());
    
    datasend+='&kostos_apostolis=' + $('#kostos_apostolis').val();
    
    datasend+='&kostos_apostolis_mode=' + kostos_apostolis_mode;
    
    
    
    //console.log(datasend);
    
    //datasend+='&mypropertiesheight=' + encodeURIComponent($('#divmyproperties').height());
    datasend+='&mypropertiesheight=' + encodeURIComponent(window.scrollY);
    //console.log($('#divmyproperties').height());
    
    if (from_php_gks_lock == false) {
      var other_entity_array=[];
      var other_entity_cc=0;
      var other_entity_has_error=false;
      $('.gks_other_entity_item').each(function() {
        other_entity_cc++;
        oeaa=parseInt($(this).attr('data-oeaa'));
        if (isNaN(oeaa)) oeaa=0;
        if (oeaa>0) {
          recid = $(this).attr('data-recid');
          aade_entitytype_id=parseInt($(this).find('.oeitem_aade_entitytype_id[data-oeaa=' + oeaa + ']').val());
          entity_user_id=parseInt($(this).find('.oeitem_entity_user_id[data-oeaa=' + oeaa + ']').attr('data-id'));
          address_extra=parseInt($(this).find('.oeitem_address_extra[data-oeaa=' + oeaa + ']').val());
          if (isNaN(recid)) recid=0;
          if (isNaN(aade_entitytype_id)) aade_entitytype_id=0;
          if (isNaN(entity_user_id)) entity_user_id=0;
          if (isNaN(address_extra)) address_extra=0;
          
          if (aade_entitytype_id>0 || entity_user_id>0 || address_extra==1 || address_extra>0) {
            if (aade_entitytype_id==0) {
              myalert('error:' + gks_lang('Επιλέξτε τον τύπο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή').replaceAll('[n]',gks_n_h(other_entity_cc)));
              other_entity_has_error=true;return;
            }
            if (entity_user_id==0) {
              myalert('error:' + gks_lang('Επιλέξτε τον συσχετιζόμενο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή').replaceAll('[n]',gks_n_h(other_entity_cc)));
              other_entity_has_error=true;return;
            }
            if (address_extra==0) {
              myalert('error:' + gks_lang('Επιλέξτε το υποκατάστημα στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή').replaceAll('[n]',gks_n_h(other_entity_cc)));
              other_entity_has_error=true;return;
            }
            itemoeaa={};
            itemoeaa.recid=recid;
            itemoeaa.oeaa=oeaa;
            itemoeaa.aade_entitytype_id=aade_entitytype_id;
            itemoeaa.entity_user_id=entity_user_id;
            itemoeaa.address_extra=address_extra;
            other_entity_array.push(itemoeaa);
          }
        }
      });
      //console.log(other_entity_array);return;
      if (other_entity_has_error) return;
      other_entity_array_str = encodeURIComponent($.base64.encode(JSON.stringify(other_entity_array)));
      datasend+='&other_entity_array_str=' + other_entity_array_str;
    } 
        
    if (from_php_gks_lock == false) {
      var correlated_invoices_array=[];
      var correlated_invoices_cc=0;
      var correlated_invoices_has_error=false;
      $('.gks_correlated_invoices_item').each(function() {
        correlated_invoices_cc++;
        coiaa=parseInt($(this).attr('data-coiaa'));
        if (isNaN(coiaa)) coiaa=0;
        if (coiaa>0) {
          recid = $(this).attr('data-recid');
          coi_mark=$(this).find('.coiitem_mark[data-coiaa=' + coiaa + ']').val().trim();
          if (coi_mark.startsWith('acc_inv#') || coi_mark.startsWith('acc_pay#') || coi_mark.startsWith('whi_mov#')) coi_mark='';
          if (coi_mark=='') {
            coi_mark=$(this).find('.coiitem_mark[data-coiaa=' + coiaa + ']').attr('data-coi_mark').trim();
          }
          coi_acc_inv_id=parseInt($(this).find('.coiitem_mark[data-coiaa=' + coiaa + ']').attr('data-coi_acc_inv_id'));
          coi_acc_pay_id=parseInt($(this).find('.coiitem_mark[data-coiaa=' + coiaa + ']').attr('data-coi_acc_pay_id'));
          coi_whi_mov_id=parseInt($(this).find('.coiitem_mark[data-coiaa=' + coiaa + ']').attr('data-coi_whi_mov_id'));
          if (isNaN(recid)) recid=0;
          if (isNaN(coi_acc_inv_id)) coi_acc_inv_id=0;
          if (isNaN(coi_acc_pay_id)) coi_acc_pay_id=0;
          if (isNaN(coi_whi_mov_id)) coi_whi_mov_id=0;
          if (coi_mark!='' || coi_acc_inv_id>0 || coi_acc_pay_id>0 || coi_whi_mov_id>0) {
            itemcoiaa={};
            itemcoiaa.recid=recid;
            itemcoiaa.coiaa=coiaa;
            itemcoiaa.coi_mark=coi_mark;
            itemcoiaa.coi_acc_inv_id=coi_acc_inv_id;
            itemcoiaa.coi_acc_pay_id=coi_acc_pay_id;
            itemcoiaa.coi_whi_mov_id=coi_whi_mov_id;
            correlated_invoices_array.push(itemcoiaa);
          }
        }
      });
      //console.log(correlated_invoices_array);return;
      correlated_invoices_array_str = encodeURIComponent($.base64.encode(JSON.stringify(correlated_invoices_array)));
      datasend+='&correlated_invoices_array_str=' + correlated_invoices_array_str;
    }    
    
    if (from_php_gks_lock == false) {
      var multiple_connected_marks_array=[];
      var multiple_connected_marks_cc=0;
      var multiple_connected_marks_has_error=false;
      $('.gks_multiple_connected_marks_item').each(function() {
        multiple_connected_marks_cc++;
        mcmaa=parseInt($(this).attr('data-mcmaa'));
        if (isNaN(mcmaa)) mcmaa=0;
        if (mcmaa>0) {
          recid = $(this).attr('data-recid');
          mcm_mark=$(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').val().trim();
          if (mcm_mark.startsWith('acc_inv#') || mcm_mark.startsWith('acc_pay#') || mcm_mark.startsWith('whi_mov#')) mcm_mark='';
          if (mcm_mark=='') {
            mcm_mark=$(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_mark').trim();
          }
          mcm_acc_inv_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_acc_inv_id'));
          mcm_acc_pay_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_acc_pay_id'));
          mcm_whi_mov_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_whi_mov_id'));
          if (isNaN(recid)) recid=0;
          if (isNaN(mcm_acc_inv_id)) mcm_acc_inv_id=0;
          if (isNaN(mcm_acc_pay_id)) mcm_acc_pay_id=0;
          if (isNaN(mcm_whi_mov_id)) mcm_whi_mov_id=0;
          if (mcm_mark!='' || mcm_acc_inv_id>0 ||  mcm_acc_pay_id>0 || mcm_whi_mov_id>0) {
            itemmcmaa={};
            itemmcmaa.recid=recid;
            itemmcmaa.mcmaa=mcmaa;
            itemmcmaa.mcm_mark=mcm_mark;
            itemmcmaa.mcm_acc_inv_id=mcm_acc_inv_id;
            itemmcmaa.mcm_acc_pay_id=mcm_acc_pay_id;
            itemmcmaa.mcm_whi_mov_id=mcm_whi_mov_id;
            multiple_connected_marks_array.push(itemmcmaa);
          }
        }
      });
      //console.log(multiple_connected_marks_array);return;
      multiple_connected_marks_array_str = encodeURIComponent($.base64.encode(JSON.stringify(multiple_connected_marks_array)));
      datasend+='&multiple_connected_marks_array_str=' + multiple_connected_marks_array_str;
    }
    
    if (from_php_gks_lock == false) {
      var packings_declarations_array=[];
      var packings_declarations_cc=0;
      var packings_declarations_has_error=false;
      $('.gks_packings_declarations_item').each(function() {
        packings_declarations_cc++;
        pdeaa=parseInt($(this).attr('data-pdeaa'));
        if (isNaN(pdeaa)) pdeaa=0;
        if (pdeaa>0) {
          recid = $(this).attr('data-recid');
          pde_type_id=parseInt($(this).find('.pde_packaging_type_id[data-pdeaa=' + pdeaa + ']').val());
          pde_quantity=parseInt($(this).find('.pde_packaging_quantity[data-pdeaa=' + pdeaa + ']').val());
          pde_type_6_descr=$(this).find('.pde_packaging_type_6_descr[data-pdeaa=' + pdeaa + ']').val().trim();
          
          if (isNaN(recid)) recid=0;
          if (isNaN(pde_type_id)) pde_type_id=0;
          if (isNaN(pde_quantity)) pde_quantity=0;
          if (pde_type_id>0 && pde_quantity>0) {
            itempdeaa={};
            itempdeaa.recid=recid;
            itempdeaa.pdeaa=pdeaa;
            itempdeaa.pde_type_id=pde_type_id;
            itempdeaa.pde_quantity=pde_quantity;
            itempdeaa.pde_type_6_descr=pde_type_6_descr;
            packings_declarations_array.push(itempdeaa);
          }
        }
      });
      //console.log(packings_declarations_array);return;
      packings_declarations_array_str = encodeURIComponent($.base64.encode(JSON.stringify(packings_declarations_array)));
      datasend+='&packings_declarations_array_str=' + packings_declarations_array_str;
    }
        
    if (from_php_gks_lock == false) {

      var eidi_array=[];
      $('.gks_quantity').each(function() {
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa>0) {
          id_whi_mov_product = $('.gks_eidos[data-aa=' + aa + ']').attr('data-recid');
          product_id = parseInt($('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product'));
          product_quantity = parseFloat($('.gks_quantity[data-aa=' + aa + ']').val());
          product_monada_id = parseInt($('.gks_monada_span[data-aa=' + aa + ']').attr('data-mon-id'));
          product_descr = $('.gks_descr[data-aa=' + aa + ']').val().trim();
          product_comments = $('.gks_comments[data-aa=' + aa + ']').val().trim();
          
          
          
          if (isNaN(product_quantity)) product_quantity=0;
          if (isNaN(product_monada_id)) product_monada_id=0;
           
          if (isNaN(product_id)) product_id=2;
    
    
          product_lots_serials=[];
          if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
            $('.div_gks_eidos_lots_serials[data-aa=' + aa + ']').each(function() {
              ls=parseInt($(this).attr('data-ls'));
              if (isNaN(ls)) ls=0;          
              if (ls>0) {
                lot_name=$('.gks_eidos_lots_serials_name[data-aa=' + aa + '][data-ls=' + ls + ']').val().trim();
                
                lot_product_quantity=parseFloat($('.gks_eidos_lots_serials_quantity[data-aa=' + aa + '][data-ls=' + ls + ']').val());
                if (isNaN(lot_product_quantity)) lot_product_quantity=0;
                if (lot_name!='' && lot_product_quantity!=0) {
                  lot_product_item={};
                  lot_product_item.lot_name=lot_name;
                  lot_product_item.lot_product_quantity=lot_product_quantity;
                  lot_product_item.lot_descr=$('.gks_eidos_lots_serials_descr[data-aa=' + aa + '][data-ls=' + ls + ']').val().trim();
                  lot_product_item.lot_date_production=$('.gks_eidos_lots_serials_date_production[data-aa=' + aa + '][data-ls=' + ls + ']').val().trim();
                  lot_product_item.lot_date_expire=$('.gks_eidos_lots_serials_date_expire[data-aa=' + aa + '][data-ls=' + ls + ']').val().trim();
                  product_lots_serials.push(lot_product_item);
                }
              }
            });
            //console.log(product_lots_serials);
          }
          
          if (product_id<=0) product_id=2;
          addthis=true;
          if (product_id==2 && product_quantity==0 && product_descr=='') addthis=false;
          if (addthis) {
            item={};
            item.aa=aa;
            item.id_whi_mov_product=id_whi_mov_product;
            item.product_id=product_id;
            item.product_quantity=product_quantity;
            item.product_monada_id=product_monada_id;
            item.product_descr=product_descr;
            item.product_comments=product_comments;
            item.product_lots_serials=product_lots_serials;
            eidi_array.push(item);
          }
          
        }
        
      });
      eidi_array_str = encodeURIComponent($.base64.encode(JSON.stringify(eidi_array)));
      
      datasend+='&eidi_array_str=' + eidi_array_str;
      

   
    }
    datasend+='&cache_file=' + cache_file;
    


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

    //console.log(eidi_array);
    //console.log(eidi_array);
    //console.log(datasend);

    //console.log(datasend);
    //return;

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-whi-mov-item-exec.php?id=' + from_php_id,
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
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='ok') {
  					    if (run_from_steps==false) {
  					      myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
  					      gks_eraseCookie('whi_mov_steps');
  					    } else {
  					      if (data.redirect=='') {
        					  window.location.reload();
        					} else {
        					  window.location.href = $.base64.decode(data.redirect);
        					}
  					    }
  					  } else {
  					    myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
  					    gks_eraseCookie('whi_mov_steps');
  					  }
  					} else {
    					if (data.redirect=='') {
    					  window.location.reload();
    					} else {
    					  window.location.href = $.base64.decode(data.redirect);
    					}
    					//gks_eraseCookie('whi_mov_steps');
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						gks_eraseCookie('whi_mov_steps');
					}
				}
			}
			
		});     

    return false;
  }  
  

  $('#myproperties').show();
  //console.log($('#divmyproperties').css('height'));
  //console.log($('#divmyproperties').height());
  
  //$('#divmyproperties').css('height','');
  //console.log($('#divmyproperties').height());

  

  
  
  
  $('#add_links_url').click(function(event) {  
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το δελτίο αποστολής'));
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
			url: '/my/admin-whi-mov-item-add-link.php',
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
  
  function gks_delete_eidos_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_aa=parseInt( $(this).attr('data-aa'));
    if (isNaN(delete_aa)) delete_aa=0;
    if (delete_aa<=0) return;
    $('.gks_eidos_2divs[data-aa=' + delete_aa +']').remove();
    //$('.gks_eidos[data-aa=' + delete_aa +']').remove();    
    //if (from_php_GKS_PRODUCT_LOTS_SERIALS) $('.gks_eidos_lots_serials[data-aa=' + delete_aa +']').remove();
    
    if ($('.gks_eidos').length ==0) {
      eidoi_add(false,0);  
    }
    
    $('#gks_products_count').html($('.gks_quantity').length);
    eidi_table_colors();
    calc_pliroteo('delete', delete_aa); 
    gks_myscroll();
  }
  
  $('.gks_delete_eidos').click(gks_delete_eidos_click);

  
  
  var mylgdef = $("#eidi_table");
  function mylgdef_restart() {
    if (!(mylgdef.data('lightGallery') === undefined)) {
      mylgdef.data('lightGallery').destroy(true);
    }
    mylgdef.lightGallery({selector: '.lightgalleryitem_user',thumbnail:true,hideBarsDelay:1000,});  
  }
  mylgdef_restart();
  
  function gks_code_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        mydata={
          term: request.term,
          onlycode1:1,
          //and_variable:1,
          base_types:[0,1],
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
      autoFocus: true,
      delay: 300, //default
      select: function( event, ui ) {
        need_save=true;
        //$("#product_id").val(ui.item.id);
        //$('#autocomplete_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
        //$('#autocomplete_product_id').show();
        //console.log(event);
        //console.log(ui);
        //console.log($(this).attr('data-aa'));
        
        aa=$(this).attr('data-aa');
        $('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product',ui.item.id);
        get_product_data(aa,1,false);
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          $(this).val('');
          //$("#product_id").val('');
          //$('#autocomplete_product_id').hide();
          $(this).attr('data-varos','0').
                  attr('data-ogos_x','0').
                  attr('data-ogos_y','0').
                  attr('data-ogos_z','0').
                  attr('data-need_apostoli','0');
                  
          aa=$(this).attr('data-aa');
          $('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product','0');
          $('.gks_descr[data-aa=' + aa + ']').val('');
          gks_resize_textarea($('.gks_descr[data-aa=' + aa + ']'));
          $('.gks_monada_span[data-aa=' + aa + ']').html('').attr('data-mon-id','0');
          
          $('.gks_photo_link[data-aa=' + aa + ']').attr('href','/my/img/product.png').attr('data-sub-html','').removeClass('lightgalleryitem_user').hide();
          $('.gks_img[data-aa=' + aa + ']').attr('src','/my/img/product.png');
          
          $('.gks_product_zoom[data-aa=' + aa + ']').hide();
          $('.gks_info_descr[data-aa=' + aa + ']').hide();
          
          if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
            $('.gks_eidos_lots_serials[data-aa=' + aa + ']').attr('data-val-lot-serial','');
            $('.gks_eidos_lots_serials[data-aa=' + aa + '] .gks_eidos_lots_serials_span').html('');
            $('.gks_eidos_lots_serials[data-aa=' + aa + ']').hide();//'blind', {}, 200
          }
          
          calc_pliroteo('code', aa);
          mylgdef_restart();
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
//    .autocomplete( "instance" )._renderItem = function( ul, item ) {
//      return $( "<li>" )
//        .append( "<div>" + item.label + "<br>" + item.desc + "</div>" )
//        .appendTo( ul );
//    };    
  }
  $('.gks_code').each(function() {
    gks_code_autocomplete($(this));
  });
  
  function get_product_data(aa,anddescr,fdavp) {
    if (from_php_perm_ret_edit==false) return;
    id_product=parseInt($('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product'));
    if (isNaN(id_product)) id_product=0;
    if (id_product<=0) return;
    
    
    if ($('.gks_quantity[data-aa=' + aa + ']').val()=='') {
      quantity=1;
      $('.gks_quantity[data-aa=' + aa + ']').val(quantity);
    } else {
      quantity=parseFloat($('.gks_quantity[data-aa=' + aa + ']').val());
      if (isNaN(quantity)) quantity=0;
      if (quantity<0) {
        quantity=0;
        $('.gks_quantity[data-aa=' + aa + ']').val(quantity);
      }
    }
    
    user_id=parseInt($('#user_id').val());
    if (isNaN(user_id)) user_id=0;
    
    //calc_pliroteo('code', aa);
    if (anddescr==0) {
      //console.log('exit get_product_data');
      return;
    }
    
    pricelist_id=parseInt($('#pricelist_id').val());
    if (isNaN(pricelist_id)) pricelist_id=0;
    
    
    datasend='cmd=get&id=' + id_product + 
            '&aa=' + aa + '&sheets=0&quantity=' + quantity + 
            '&user_id=' + user_id + 
            '&anddescr=' + anddescr + 
            '&pricelist_id=' + pricelist_id + 
            '&mydate=' + encodeURIComponent($('#mov_date').val().trim());
    
    $.ajax({
			url: 'admin-get-product-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_fdavp:fdavp,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
            
            
            $('.gks_monada_span[data-aa=' + data.aa + ']').html(data.monada_symbol).attr('data-mon-id',data.product_monada_id);
            
					  if (data.anddescr!=0) {
					    
					    
              $('.gks_descr[data-aa=' + data.aa + ']').val(data.product_descr);
              gks_resize_textarea($('.gks_descr[data-aa=' + data.aa + ']'));
              
              $('.gks_comments[data-aa=' + data.aa + ']').val(data.product_def_comments);
              gks_resize_textarea($('.gks_comments[data-aa=' + data.aa + ']'));
              
              if (data.product_photo=='') {
                $('.gks_photo_link[data-aa=' + data.aa + ']').attr('href','/my/img/product.png').attr('data-sub-html','').removeClass('lightgalleryitem_user').hide();
                $('.gks_img[data-aa=' + data.aa + ']').attr('src','/my/img/product.png');
              } else {
                $('.gks_photo_link[data-aa=' + data.aa + ']').attr('href',data.photo_url).attr('data-sub-html',data.product_code).addClass('lightgalleryitem_user').show();
                $('.gks_img[data-aa=' + data.aa + ']').attr('src',data.product_photo);
              }
 					    mylgdef_restart();
 					    
 					    $('.gks_product_zoom[data-aa=' + aa + ']').show();
 					    
   					  mybigdescr=$('.gks_info_descr[data-aa=' + aa + ']');
   					  if (mybigdescr.hasClass('tooltipster')) mybigdescr.tooltipster('destroy');
 					    if (data.product_descr_small=='') {
   					    mybigdescr.hide().attr('title' ,'').removeClass('tooltipster');
 					    } else {
   					    mybigdescr.show().attr('title' ,data.product_descr_small).addClass('tooltipster');
   					    mybigdescr.tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
   					  }
            }
            
            if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
              $('.gks_eidos_lots_serials[data-aa=' + data.aa + ']').attr('data-val-lot-serial',data.product_lot_serial);
              $('.gks_eidos_lots_serials[data-aa=' + data.aa + '] .gks_eidos_lots_serials_span').html(data.product_lot_serial_label);
            }
            
//            if (expand_gks_eidos_extra) {
//              if ($('.gks_eidos_extra[data-aa=' + data.aa + ']').css('display')=='none') {
//                $('.gks_eidos[data-aa=' + data.aa + ']').addClass('gks_eidos_radup');
//                $('.gks_eidos_extra[data-aa=' + data.aa + ']').addClass('gks_eidos_raddown').show('blind', {}, 500);
//                $('.gks_eidos_details[data-aa=' + data.aa + ']').animateRotate(0,180,500);
//              }
//            }
            
            if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
//              if ($('.gks_eidos_extra[data-aa=' + data.aa + ']').css('display')!='none') {
                product_lot_serial=$('.gks_eidos_lots_serials[data-aa=' + data.aa + ']').attr('data-val-lot-serial');
                if (product_lot_serial!='') {
                  $('.gks_eidos_lots_serials[data-aa=' + data.aa + ']').show('blind', {}, 500);
                  $('.gks_eidos[data-aa=' + data.aa + ']').addClass('gks_eidos_radup');
                  
                } else {
                  $('.gks_eidos_lots_serials[data-aa=' + data.aa + ']').hide('blind', {}, 500);
                  $('.gks_eidos[data-aa=' + data.aa + ']').removeClass('gks_eidos_radup');
                }
//              }
            }            

            if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
              var product_lot_serial=$('.gks_eidos_lots_serials[data-aa=' + data.aa + ']').attr('data-val-lot-serial');
              if (product_lot_serial=='') {
                $('.div_gks_eidos_lots_serials[data-aa=' + data.aa + ']').remove();
                gks_add_eidos_lots_serials_visible(data.aa);
              } else {
                
                $('.div_gks_eidos_lots_serials[data-aa=' + data.aa + ']').each(function() {
                  var temp_product_id=parseInt($(this).find('.gks_eidos_lots_serials_name').attr('data-product-id'));
                  if (isNaN(temp_product_id)) temp_product_id=0;
                  if (data.id!=temp_product_id) {
                    $(this).find('.gks_eidos_lots_serials_name').attr('data-product-id',data.id).val('');
                    $(this).find('.gks_eidos_lots_serials_descr').val('').prop('readonly',false);
                    $(this).find('.gks_eidos_lots_serials_date_production').val('').prop('readonly',false);
                    $(this).find('.gks_eidos_lots_serials_date_expire').val('').prop('readonly',false);
                    gks_resize_textarea($(this).find('.gks_eidos_lots_serials_descr'));
                    
                    if (product_lot_serial=='lot') {
                      $(this).find('.gks_eidos_lots_serials_quantity').prop('readonly',false);
                      
                    } else if (product_lot_serial=='serial') {
                      $(this).find('.gks_eidos_lots_serials_quantity').prop('readonly',true).val(1);
                    }
                    //console.log(temp_product_id);
                    //console.log(data);
                  }
                });
              }
              gks_eidos_lots_serials_quantity_calc(data.aa);
            }
             
            if (this.gks_fdavp) {
              $('.gks_code[data-aa=' + data.aa + ']').val(data.product_code);
              if (fdavp_array.length>=2) {
                fdavp_array.shift();
                get_product_data(fdavp_array[0],1,true);
              } else {
                //console.log('run calc_pliroteo');
                calc_pliroteo('code', data.aa);
                $('body').css('cursor', '');
              }
            } else {
              calc_pliroteo('code', data.aa);
            }
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
    //gks_myscroll();
    
  }

  
  
  function eidoi_add(fromloading,click_aa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_aa++;
    //console.log('fromloading' + fromloading);

    
    row_html=
        '<div class="gks_eidos_2divs" data-aa="' + last_aa + '">' +
          '<div class="form-group row gks_eidos" data-recid="0" data-aa="' + last_aa + '">' +
            '<div class="' + from_php_gkscols1 + '">';
              
    row_html+=  
              '<input type="text" class="form-control form-control-sm gks_code" data-aa="' + last_aa + '" value=""  style="" placeholder="'+gks_lang('Κωδικός')+'">' +
            '</div>' + 
            '<div class="' + from_php_gkscols2 + '">' +
              '<div class="text-left">' + 
                '<a class="gks_photo_link" data-aa="' + last_aa + '" tabIndex="-1" href="/my/img/product.png" style="display:none"><img class="gks_img" data-aa="' + last_aa + '" src="/my/img/product.png"></a>' +
              
              '<i class="gks_product_zoom enterrow fas fa-pen" data-aa="' + last_aa + '" data-id_product="0" title="'+gks_lang('Προβολή Είδους')+'" style="display:none"></i>' +
              '<i class="fas fa-info-circle gks_info_descr" data-aa="' + last_aa + '" title="" style="display:none"></i>' +
              '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="' + last_aa + '" placeholder="'+gks_lang('Περιγραφή')+'"></textarea>' + 
              '</div>' + 
            '</div>' +
            '<div class="' + from_php_gkscols3 + '">' +
              '<textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="' + last_aa + '" placeholder="'+gks_lang('Σχόλιο')+'"></textarea>' + 
            '</div>';
            

    row_html+=
            '<div class="' + from_php_gkscols5 + '">' +
              '<input type="number" class="form-control form-control-sm gks_quantity ' + (from_php_eidos_parastatikou_type_id==24 ? 'gks_quantity_apografi' : '') + '" data-aa="' + last_aa + '" data-prev-value="0" value="" style="text-align:right;" min="0" step="' + from_php_GKS_INPUT_STEP_POSOTITA + '" placeholder="'+gks_lang('Ποσότητα')+'">' +
              ' <span class="gks_monada_span" data-aa="' + last_aa + '"></span>' + 
            '</div>';
            

            
    row_html+=
            '<div class="' + from_php_gkscols8 + '">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_delete_eidos" data-aa="' + last_aa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_add_eidos"  data-aa="' + last_aa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +           
          '</div>';         
    
        if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
    row_html+=
    
          '<div class="form-group1 row gks_eidos_lots_serials gks_eidos_lots_serials_col1" data-aa="' + last_aa + '" style="padding-top: 4px;display:none;" data-val-lot-serial="">' +
            '<div class="col-12 col-sm-12  col-md-11 col-lg-11 col-xl-11 offset-md-1 offset-lg-1 offset-xl-1 gks_eidos_lots_serials_list" data-aa="' + last_aa + '">' +
              '<div class="div_eidos_lots_serials" data-aa="' + last_aa + '" style="">' +
                '<div class="form-group row div_add_eidos_lots_serials" data-aa="' + last_aa + '" style="margin: 0px;">' +
                  '<div class="col-8 col-sm-11 col-md-11 col-lg-11 gks_items_col text-center gks_eidos_lots_serials_label div_eidos_lots_serials_title">' +
                    gks_lang('Λίστα') +
                    '<span class="gks_eidos_lots_serials_span"></span>' +
                  '</div>' +
                  '<div class="col-4 col-sm-1 col-md-1 col-lg-1 gks_items_col text-center">' +
                    '<i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="' + last_aa + '" style=""></i>' +
                  '</div>' +
                '</div>' +
                '<div class="form-group1 row div_eidos_lots_serials_sum_quantity" data-aa="' + last_aa + '" style="margin: 0px;display:none;">' +
                  '<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-left gks_eidos_lots_serials_label div_eidos_lots_serials_title" style="margin: 0px;">' +
                     '<div class="gks_flock gks_flock_small form-control-sm">' +
                       gks_lang('Άθροισμα')+':' +
                     '</div>' +
                  '</div>' +
                  '<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-right gks_eidos_lots_serials_label">' +
                    '<div class="gks_flock gks_flock_small form-control-sm">' + 
                      '<img src="img/warning.gif" class="img_eidos_lots_serials_sum_quantity" data-aa="' + last_aa + '" style="display:none;"/>' +
                      '<span class="span_eidos_lots_serials_sum_quantity span_eidos_lots_serials_sum_quantity_lock" data-aa="' + last_aa + '"></span>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';

        }

    row_html+=
        '</div>';

            
    //details
    
    if (click_aa<=0) {
      $('#eidi_footer1').before(row_html);
    } else {
      $('.gks_eidos_2divs[data-aa=' + click_aa + ']').after(row_html);
    }
    
    $('.gks_add_eidos').show();  
    $('.gks_delete_eidos').show();

    gks_code_autocomplete($('.gks_code[data-aa=' + last_aa + ']'));
    $('.gks_code[data-aa=' + last_aa + ']').keyup(gks_code_keyup);
    $('.gks_descr[data-aa=' + last_aa + ']').on(mychange, gks_descr_keyup);
    $('.gks_descr[data-aa=' + last_aa + ']').on(mychange, gks_descr_change);
    $('.gks_comments[data-aa=' + last_aa + ']').keyup(gks_comments_keyup);
    $('.gks_comments[data-aa=' + last_aa + ']').on(mychange, gks_comments_change);

    
    
    $('.gks_quantity[data-aa=' + last_aa + ']').on(mychange, gks_quantity_change);

      

    $('.gks_add_eidos[data-aa=' + last_aa + ']').click(function() {gks_add_eidos_click(false,$(this));});
    $('.gks_delete_eidos[data-aa=' + last_aa + ']').click(gks_delete_eidos_click); //.hide();

    $('.gks_product_zoom[data-aa=' + last_aa + ']').click(gks_product_zoom_click);

    $('.gks_monada_span[data-aa=' + last_aa + ']').click(gks_monada_span_click);	
    $('.gks_monada_span[data-aa=' + last_aa + ']').contextMenu(gks_monada_span_contextMenu);

    $('.gks_add_eidos_lots_serials[data-aa=' + last_aa + ']').click(gks_add_eidos_lots_serials_click);

    if (fromloading==false) {
      if (from_php_enter_order.length>0) {
        $('.' + from_php_enter_order[0] + '[data-aa=' + last_aa + ']').focus().select();
      } else {
//        elemset= $('.gks_set[data-aa=' + last_aa + ']');
//        if (elemset.length>0) 
//          $('.gks_set[data-aa=' + last_aa + ']').focus().select();
//        else
          $('.gks_code[data-aa=' + last_aa + ']').focus().select();
      }
      
    }
    
    
    $('#gks_products_count').html($('.gks_quantity').length);
    eidi_table_colors();
    gks_myscroll();
  }
  
  $('#fiscal_position_id').change(function() {
    calc_pliroteo();
  });
  $('#pricelist_id').change(function() {
    calc_pliroteo();
  });
    

  
  var calc_pliroteo_xhr;
  var calc_pliroteo_timer=null;
  function calc_pliroteo(field_name='', field_aa=-1, mycmd='', myfile='') {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    //console.log('calc_pliroteo ' + field_name + ' ' + field_aa + ' ' + mycmd + ' ' + myfile);
    
    check_vies_valid_wait_timer_stop();
    
    if(calc_pliroteo_xhr && calc_pliroteo_xhr.readyState != 4){
      calc_pliroteo_xhr.abort();
    }
    if (calc_pliroteo_timer!=null) clearTimeout(calc_pliroteo_timer);
    calc_pliroteo_timer=setTimeout(calc_pliroteo_run,400,field_name, field_aa, mycmd, myfile);
  }
  function calc_pliroteo_run(field_name='', field_aa=-1, mycmd='', myfile='') {
    $('#calc_hourglass').show();
    
    d=$('input[name=radio_delivery_way]:checked');
    if (d.css('display')=='none') d=0;
    else {d=d.val(); if (d === undefined || d === null) d=0;}
    
    p=$('input[name=radio_payment_way]:checked');
    if (p.css('display')=='none') p=0;
    else {p=p.val(); if (p === undefined || p === null) p=0;}
    
    
    if (from_php_gks_lock) {
      
      mydata={};
      mydata.gks_lock=true;
      mydata.mycmd=mycmd;
      mydata.myfile=myfile;
      mydata.tropos_apostolis=d;
      mydata.kostos_apostolis=parseFloat($('#kostos_apostolis').val());
      if (isNaN(mydata.kostos_apostolis)) mydata.kostos_apostolis=0;
      
  
      mydata.kostos_apostolis_mode=kostos_apostolis_mode;
      
      mydata_str = encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
      datasend='&mydata_str=' + mydata_str;

    } else {



      
      // 'gks_peritem_net'
      
      
      
      
      
      var gks_products_posotita=0;
      var gks_products_varos=0;
      var gks_products_ogos=0;
      var gks_products_ogos_x=0;
      var gks_products_ogos_y=0;
      var gks_products_ogos_z=0;
      var gks_products_need_apostoli=0;
  
      
      company_id=0;
      company_sub_id=0;
      v=$('#company_id_sub_id').val();
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
  
      
      mydata={};
  
      //mydata.gks_number_lock=from_php_number_gks_lock;
      //mydata.gks_user_lock=from_php_user_gks_lock;
      mydata.mycmd=mycmd;

      mydata.mycmd=mycmd;
      mydata.myfile=myfile;
      mydata.company_id=company_id;
      mydata.company_sub_id=company_sub_id;
      mydata.mov_whi_journal_id = $('#mov_whi_journal_id').val();
      mydata.mov_whi_seira_id = $('#mov_whi_seira_id').val();
      mydata.mov_state = from_php_mov_state;
      mydata.aade_skopos_diakinisis_id=$('#aade_skopos_diakinisis_id').val();
      mydata.aade_skopos_19_descr=$('#aade_skopos_19_descr').val();
      mydata.mov_date = $('#mov_date').val();
      mydata.user_id = $('#user_id').val();
      mydata.first_name = $('#dr_user_first_name').val();
      mydata.last_name = $('#dr_user_last_name').val();
      mydata.email = $('#dr_user_email').val();
      mydata.mobile = $('#dr_user_mobile').val();
      mydata.lang = $('#dr_user_lang').val();
      mydata.ma_odos = $('#dr_user_ma_odos').val();
      mydata.ma_arithmos = $('#dr_user_ma_arithmos').val();
      mydata.ma_orofos = $('#dr_user_ma_orofos').val();
      mydata.ma_perioxi = $('#dr_user_ma_perioxi').val();
      mydata.ma_poli = $('#dr_user_ma_poli').val();
      mydata.ma_tk = $('#dr_user_ma_tk').val();
      mydata.ma_country_id = $('#dr_user_ma_country_id').val();
      mydata.ma_nomos_id = $('#dr_user_ma_nomos_id').val();
      mydata.eponimia = $('#dr_user_eponimia').val();
      mydata.title = $('#dr_user_title').val();
      mydata.afm = $('#dr_user_afm').val();
      mydata.doy = $('#dr_user_doy').val();
      mydata.epaggelma = $('#dr_user_epaggelma').val();
      
      mydata.address_extra = $('#form_select_apostoli').val();
      if ($('#form_select_apostoli').val()==-1) { //idia
        mydata.dd_name='';
        mydata.dd_phone='';
        mydata.dd_odos=$('#dr_user_ma_odos').val();
        mydata.dd_arithmos=$('#dr_user_ma_arithmos').val();
        mydata.dd_orofos=$('#dr_user_ma_orofos').val();
        mydata.dd_perioxi=$('#dr_user_ma_perioxi').val();
        mydata.dd_poli=$('#dr_user_ma_poli').val();
        mydata.dd_tk=$('#dr_user_ma_tk').val();
        mydata.dd_country_id=$('#dr_user_ma_country_id').val();
        mydata.dd_nomos_id=$('#dr_user_ma_nomos_id').val();
      } else {
        mydata.dd_name=$('#form_ea_name').val();
        mydata.dd_phone=$('#form_ea_phone').val();
        mydata.dd_odos=$('#form_ea_odos').val();
        mydata.dd_arithmos=$('#form_ea_arithmos').val();
        mydata.dd_orofos=$('#form_ea_orofos').val();
        mydata.dd_perioxi=$('#form_ea_perioxi').val();
        mydata.dd_poli=$('#form_ea_poli').val();
        mydata.dd_tk=$('#form_ea_tk').val();
        mydata.dd_country_id=$('#form_ea_country_id').val();
        mydata.dd_nomos_id=$('#form_ea_nomos_id').val();
      }
          
      
      
      
      
      mydata.need_afm=from_php_eidos_parastatikou_need_afm;
      mydata.fiscal_position_id = $('#fiscal_position_id').val();
      mydata.pricelist_id = $('#pricelist_id').val();
      
      
      mydata.gks_products_posotita=gks_products_posotita;
      mydata.gks_products_varos=gks_products_varos;
      mydata.gks_products_ogos=gks_products_ogos;
      mydata.gks_products_ogos_x=gks_products_ogos_x;
      mydata.gks_products_ogos_y=gks_products_ogos_y;
      mydata.gks_products_ogos_z=gks_products_ogos_z;
      mydata.tropos_apostolis=d;
      mydata.gks_products_need_apostoli=gks_products_need_apostoli;
      
      mydata.kostos_apostolis=parseFloat($('#kostos_apostolis').val());
      if (isNaN(mydata.kostos_apostolis)) mydata.kostos_apostolis=0;
      
  
      mydata.kostos_apostolis_mode=kostos_apostolis_mode;
  
      //console.log(mydata);
      
  
      var eidi_array=[];
      $('.gks_quantity').each(function() {
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa>0) {
          id_whi_mov_product = $('.gks_eidos[data-aa=' + aa + ']').attr('data-recid');
          product_id = parseInt($('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product'));
          product_quantity = parseFloat($('.gks_quantity[data-aa=' + aa + ']').val());
          product_monada_id = parseInt($('.gks_monada_span[data-aa=' + aa + ']').attr('data-mon-id'));
          product_descr = $('.gks_descr[data-aa=' + aa + ']').val().trim();
          product_comments = $('.gks_comments[data-aa=' + aa + ']').val().trim();
          
          if (isNaN(product_quantity)) product_quantity=0;
          if (isNaN(product_monada_id)) product_monada_id=0;
          if (isNaN(product_id)) product_id=2;
          if (product_id<=0) product_id=2;
          addthis=true;
          if (product_id==2 && product_quantity==0 && product_descr=='') addthis=false;
          if (addthis) {
            item={};
            item.aa=aa;
            item.id_whi_mov_product=id_whi_mov_product;
            item.product_id=product_id;
            item.product_quantity=product_quantity;
            item.product_monada_id=product_monada_id;
            item.product_descr=product_descr;
            item.product_comments=product_comments;
    
            
            eidi_array.push(item);
          }
          
        }
      });
      
      
      mydata.eidi_array=eidi_array;

      mydata_str = encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
      datasend='&mydata_str=' + mydata_str;
      
    }
    
    //console.log('datasend ['+ field_name + '] [' + field_aa + ']');
    //console.log(eidi_array[0]);
    //console.log('calc_pliroteo');
    
    calc_pliroteo_xhr = $.ajax({
			url: '/my/admin-whi-mov-item-calc-basket.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_field_name:field_name,
			gks_field_aa:field_aa,
			gks_mycmd:mycmd,
			gks_myfile:myfile,
			error : function(jqXHR ,textStatus,  errorThrown) {
				if (textStatus != 'abort') {
				  myalert('error:' + jqXHR.responseText);
				  $('#calc_hourglass').hide();
				}
			},				
			success: function(data) {
			  need_save=true;
			  $('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					//console.log('response');
  					cache_file=data.cache_file;
  					if (from_php_gks_lock==false) {

    				}
  					
  					
  					
  					for (var item in data.tropoi_apostolis_all) {
  					  var obj = data.tropoi_apostolis_all[item];
  					  elem = $('#price_delivery_way_' + obj.id_delivery_method);
  					  elem.html( parseFloat(obj.dm_calc_kostos).mymoney());
              elem2=$('#radio_delivery_way_' +  obj.id_delivery_method);
              elem2.attr('data-type-o',obj.delivery_method_type_pa )
              elem3=elem2.parent();
              if (obj.myisok=='0') {
                elem3.hide();
                if (elem2.prop('checked')) elem2.prop('checked', false);
              } else {
                elem3.show();
              } 
  					}
  					will_run_calc_pliroteo='';
  					var velemd=null; var velemd_length=0;
  					$('input[name=radio_delivery_way]:enabled').each(function() {
  					  if ($(this).parent().css('display') != 'none') {velemd=$(this);velemd_length++;}
  					});
  					  					
  					if (velemd_length==1) {
  					  if (!velemd.prop('checked')) {
  					    velemd.prop('checked', true); 
  					    will_run_calc_pliroteo=velemd.attr('id');
  					  }
  					} else {
  					  velemd=null; velemd_length=0;
  					  $('input[name=radio_delivery_way]:enabled:checked').each(function() {
  					    if ($(this).parent().css('display') != 'none') {velemd=$(this);velemd_length++;}
  					  });  					  
  					  
  					  if (velemd_length==0) {
  					    if (from_php_delivery_way_default>0 && 
  					        $('input[name=radio_delivery_way][value=' + from_php_delivery_way_default + ']:enabled').length==1 && 
  					        $('input[name=radio_delivery_way][value=' + from_php_delivery_way_default + ']:enabled').parent().css('display') != 'none') {
  					      $('#radio_delivery_way_' + from_php_delivery_way_default).prop('checked', true);
  					      will_run_calc_pliroteo='radio_delivery_way_' + from_php_delivery_way_default;
  					    }
  					  }
  					}
            

  					
  					
  					
  					
  					//console.log(data.eidi[0]);
  					
  					if (from_php_gks_lock==false) {
    					for(i=0;i < data.eidi.length;i++) {
                $('.gks_code[data-aa=' + data.eidi[i].aa + ']').attr('data-varos',data.eidi[i].varos).
                                                      attr('data-ogos_x',data.eidi[i].ogos_x).
                                                      attr('data-ogos_y',data.eidi[i].ogos_y).
                                                      attr('data-ogos_z',data.eidi[i].ogos_z).
                                                      attr('data-need_apostoli',data.eidi[i].need_apostoli);
              }
      
  
    					//console.log(data);
    					$('#gks_products_posotita').html(data.products_posotita).attr('data-val',data.products_posotita_val);
    					$('#gks_products_ogos').html(data.products_ogos);
    					$('#gks_products_varos').html(data.products_varos);
    					
    					
    					
    					
    				  if (data.check_vies.views_run_img!='') {
                $('#dr_user_afm_views_run').html(data.check_vies.views_run_img).show();
                $('#dr_user_afm_views_run .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
                if (data.check_vies.valid==2) check_vies_valid_wait_timer_restart();
              } else {
                $('#dr_user_afm_views_run').hide();
              }
    				  
    				  
    				  
    					timer_pist_orange_check();
    				}
  					if (kostos_apostolis_mode!='manual') $('#kostos_apostolis').val(data.kostos_apostolis_val);


  				  
  					
  					//console.log(data.check_vies);
  					//console.log(data.views_run_img);
  					
  					//console.log('will_run_calc_pliroteo: ' + will_run_calc_pliroteo);
  					if(will_run_calc_pliroteo!='') {
  					  $('#' + will_run_calc_pliroteo).click();
  					} 
  					
  					gks_myscroll();
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
  }
  
  
  function gks_product_zoom_click() {
    if (from_php_perm_ret_edit==false) return;
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    
    id_product = $('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product');
    if (isNaN(id_product)) id_product=0;
    if (id_product<=0) return;
    
    //console.log(id_product);
    myhref = 'admin-products-item.php?id=' + id_product;
    vvvp = window.open(myhref, '_blank'); //.focus();
    
  }
  $('.gks_product_zoom').click(gks_product_zoom_click);
  
    
  function next_enter_field_fnc(aa,fieldfrom,faultback) {
    //console.log('next_enter_field_fnc',aa,fieldfrom,faultback);
    
    if (control_enter_active) return;
    for (i=0; i<from_php_enter_order.length; i++) {
      if (from_php_enter_order[i]==fieldfrom) {
        if (i < (from_php_enter_order.length - 1)) {
          if (from_php_enter_order[i+1]=='new_row') {
            elemnext=$('.gks_code[data-aa=' + (aa+1) + ']');
            if (elemnext.length>0) {
              elem=$('.' + from_php_enter_order[0] + '[data-aa=' + (aa+1) + ']');
              if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
              else elem.focus().select();
            } else {
              $('.gks_add_eidos[data-aa=' + aa + ']').click();
            }
          } else {
            elem=$('.' + from_php_enter_order[i+1] + '[data-aa=' + aa + ']');
            if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
            else elem.focus().select();
          }
          return;
        }
      }
    }
    if (faultback=='new_row') {
      elemnext=$('.gks_code[data-aa=' + (aa+1) + ']');
      if (elemnext.length>0) {
        if (from_php_enter_order.length>0) {
          elemnextuo=$('.' + from_php_enter_order[0] + '[data-aa=' + (aa+1) + ']');
          if (elemnextuo.prop('nodeName')=='TEXTAREA') elemnextuo.focus();
          else elemnextuo.focus().select();
        } else {
//          elemnext_set=$('.gks_set[data-aa=' + (aa+1) + ']');
//          if (elemnext_set.length>0) {
//            if (elemnext_set.prop('nodeName')=='TEXTAREA') elemnext_set.focus();
//            else elemnext_set.focus().select();
//          } else {
            if (elemnext.prop('nodeName')=='TEXTAREA') elemnext.focus();
            else elemnext.focus().select();
//          }
        }
      } else {
        $('.gks_add_eidos[data-aa=' + aa + ']').click();
      }
    } else if (faultback!='') {
      elem=$('.' + faultback + '[data-aa=' + aa + ']');
      if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
      else elem.focus().select();
    }
  }
  
  function gks_code_keyup (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa<=0) return;
        next_enter_field_fnc(aa,'gks_code','gks_descr');
        return;
      }
    }
  }
  $('.gks_code').keyup(gks_code_keyup);
  
  function gks_descr_keyup (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        val=$(this).val();
        //console.log('val',val);
        if (val == '\n' || val.endsWith('\n\n')) {
          val=val.replace(/\n+$/, '');
          $(this).val(val);
          event.preventDefault();  
          aa=parseInt($(this).attr('data-aa'));
          if (isNaN(aa)) aa=0;
          if (aa<=0) return;
          next_enter_field_fnc(aa,'gks_descr','gks_comments');
          return;
        }
      }
    }
  }
  $('.gks_descr').keyup(gks_descr_keyup);
  
  function gks_descr_change() {gks_resize_textarea($(this));}
  $('.gks_descr').on(mychange, gks_descr_change);
  $('.gks_descr').each(function() {gks_resize_textarea($(this));});
  
  function gks_comments_keyup (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        val=$(this).val();
        //console.log('val',val);
        if (val == '\n' || val.endsWith('\n\n')) {
          val=val.replace(/\n+$/, '');
          $(this).val(val);
          event.preventDefault();  
          aa=parseInt($(this).attr('data-aa'));
          if (isNaN(aa)) aa=0;
          if (aa<=0) return;
          next_enter_field_fnc(aa,'gks_comments','gks_quantity');
          return;
        }
      }
    }
  }
  $('.gks_comments').keyup(gks_comments_keyup);
  
  function gks_comments_change() {gks_resize_textarea($(this));}
  $('.gks_comments').on(mychange, gks_comments_change);
  $('.gks_comments').each(function() {gks_resize_textarea($(this));});
  



  
  
  function gks_quantity_change (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    event.preventDefault();  
    aa_start=parseInt($(this).attr('data-aa'));
    if (isNaN(aa_start)) aa_start=0;
    if (aa_start<=0) return;
    aa=aa_start;
    if (event != undefined && event.which != undefined && event.which == 13) {
      next_enter_field_fnc(aa,'gks_quantity','new_row');
      return;
    }
    prev_value=parseFloat($(this).attr('data-prev-value'));
    if (isNaN(prev_value)) prev_value=0;
    curr_value=parseFloat($(this).val());
    if (isNaN(curr_value)) curr_value=0;
    if (curr_value!=prev_value) {
      //get_product_data(aa, 0);
      $(this).attr('data-prev-value',curr_value);
    }
    if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
      gks_eidos_lots_serials_quantity_calc(aa);
    }
    
    calc_pliroteo('gks_quantity',aa_start);
  }
  $('.gks_quantity').on(mychange, gks_quantity_change);
      
  


  

  function gks_add_eidos_click(fromloading,elem) {
    aa=elem.attr('data-aa');
    eidoi_add(fromloading,aa);
  }
    
  $('.gks_add_eidos').click(function() {gks_add_eidos_click(false,$(this));});

  
  
  
  
  $('#eidi_table').sortable({
    items: '.gks_eidos_2divs',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-aa'});
      eidi_table_sortable_after(mylist);
    }
  });
  
  function eidi_table_sortable_after(mylist) {
    //console.log(mylist);
    $('#eidi_table > .gks_eidos_2divs').each(function() {
      aa=$(this).attr('data-aa');
      $(this).attr('data-aa_temp',aa);
    });
    $('#eidi_table > .gks_eidos_2divs').each(function() {
      aa=$(this).attr('data-aa_temp');
      new_aa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==aa) {
          new_aa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_aa>=0) {
        new_aa++
        $(this).attr('data-aa',new_aa);
        $(this).find('*[data-aa=' + aa + ']').attr('data-aa',new_aa);
      }
      
    });
    eidi_table_colors();
  }  
  

  
  

  $('#user').keyup(function (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    event.preventDefault();  
    if (event != undefined && event.which != undefined && event.which == 13) {
      //$('#order_occasion').focus().select();
      return;
    }
  });
   
    
  

  $('#copy_text_pelati_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_pelati_sxolio').text();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });  


  $('#copy_text_order_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_order_sxolio').text();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });


  $('#copy_text_notes_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#notes').val();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });
  
 
  $('#copy_text_subnotes_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#subnotes').val();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });
    

  

  $('input[name=radio_delivery_way]').click( function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    
    if (from_php_GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK) {
      kostos_apostolis_mode='manual';  
    } else {
      kostos_apostolis_mode='auto';
    }
    
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');

    $('input[name=radio_payment_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().children('.delivery_payment_label').addClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').addClass('delivery_payment_disabled');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
          $('#payment_acquirer_sxolio').html('');
          $('#button_html').html(gks_lang('Πληρωμή τώρα'));
        }
      }
    });
    
    $('input[name=radio_delivery_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });

    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    //basket_edit(false,true,false,'delivery_payment', d, p, '', 0);    
    
    $('#delivery_method_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#delivery_method_sxolio').html(gks_lang('Σχόλιο τρόπου αποστολής') +': <i>' + myhtml + '</i>');

    if (d == 8) {
      $('#span_delivery_id_8').show();
      if ($('#delivery_id_8').val() == '0') {
        if ($('#delivery_id_8 option').length==2) {
          $('#delivery_id_8').val($($('#delivery_id_8 option')[1]).attr('value'));
        }
      }
    } else {
      $('#span_delivery_id_8').hide();
    }

    if (mytype=='delivery' || mytype=='pelatis' || mytype=='post') {
      $('#div_delivery_number').show();
      $('#div_vehicle_number').show();
      $('#div_dispatch_date').show();
      $('#div_dispatch_time').show();
    } else {
      $('#div_delivery_number').hide();
      $('#div_vehicle_number').hide();
      $('#div_dispatch_date').hide();
      $('#div_dispatch_time').hide();
    }
    calc_pliroteo();
    gks_myscroll();
  });
  
  
  
  


  
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
			url: '/my/admin-whi-mov-item-link-action.php',
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
			url: '/my/admin-whi-mov-item-link-timer.php?id=' + from_php_id,
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
    v=parseInt($('#dr_user_ma_country_id').val());
    if (isNaN(v)) v=0;
    //console.log(v);
    nomos_fill('dr_user_ma_nomos_id',v,0);
    calc_pliroteo();    
  });
  
  
  function dr_user_ma_country_id_change() {
    v=$('#dr_user_ma_country_id').val();
    
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
  
  $('#form_ea_country_id').each(function() {
    dbval=parseInt($(this).attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0">'+gks_lang('Χώρα')+'...</option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'">' + gks_country[i].country_name + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  });
      
  $('#form_ea_country_id').change(function() {
    v=parseInt($(this).val());
    if (isNaN(v)) v=0;
    nomos_fill('form_ea_nomos_id',v,0);
    calc_pliroteo();
  });
  
 
  
  $('#form_select_apostoli').change(function() {
    v=$(this).val();
    extra_address_select(v);
    gks_myscroll();
    calc_pliroteo();
  });    
  
  $('#dr_user_afm').change(function() {
    calc_pliroteo();
  });

  $('#dr_user_afm').on('input keyup paste', function() {
    $('#dr_user_afm_views_run').hide();
  }); 
  
  $('#kostos_apostolis').on(mychange, function() {
    kostos_apostolis_mode='manual';
    calc_pliroteo();
  });
  
  
  
  
   

 
  
  
  function extra_address_select(v) {
    
    if (v ==-1) {
      $('#div_extra_address').hide(); 
      auto_copy_warehouse_xxx_addr(); 
      return;
    } else {
      $('#div_extra_address').show();
    }
    if (v==0) {
      $('#form_ea_name').val('');
      $('#form_ea_phone').val('');
      $('#form_ea_branch').val('');
      $('#form_ea_odos').val('');
      $('#form_ea_arithmos').val('');
      $('#form_ea_orofos').val('');
      $('#form_ea_perioxi').val('');
      $('#form_ea_poli').val('');
      $('#form_ea_tk').val('');

      v1 = parseInt($('#dr_user_ma_country_id').val());
      if (isNaN(v1)) v1=0;
      v2 = parseInt($('#dr_user_ma_nomos_id').val());
      if (isNaN(v2)) v2=0;
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
      auto_copy_warehouse_xxx_addr();
      
    } else {
      v1=0;
      v2=0;
      $('#form_ea_country_id').val(v1);
      nomos_fill('form_ea_nomos_id',v1,v2);
      
      mydata = 'aid=' + v + '&user_id=' + $('#user_id').val();
      //console.log(mydata);
      
      $.ajax({
          url: "/my/admin-get-address.php",
          type: 'POST',
          cache: false,
          dataType: "json",
          data:mydata,
          error : function(jqXHR ,textStatus,  errorThrown) {
  				  myalert('error:' + jqXHR.responseText);
  			  },
          success: function(data) {
            if (!data) {
    					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    				} else {
              if (data.success == true) {
                //console.log(data);

                $('#form_ea_name').val(data.data.ea_name);
                $('#form_ea_phone').val(data.data.ea_phone);
                $('#form_ea_branch').val(data.data.ea_branch);
                $('#form_ea_odos').val(data.data.ea_odos);
                $('#form_ea_arithmos').val(data.data.ea_arithmos);
                $('#form_ea_orofos').val(data.data.ea_orofos);
                $('#form_ea_perioxi').val(data.data.ea_perioxi);
                $('#form_ea_poli').val(data.data.ea_poli);
                $('#form_ea_tk').val(data.data.ea_tk);
                $('#form_ea_country_id').val(data.data.ea_country_id);
                nomos_fill('form_ea_nomos_id',data.data.ea_country_id,data.data.ea_nomos_id);
                
                auto_copy_warehouse_xxx_addr();
                
              } else {
                myalert('error:' + $.base64.decode(data.message));
              }
            }
          }
      });
    }
  }

  function auto_copy_warehouse_xxx_addr() {
    if (from_php_eidos_parastatikou_type_id>0 && from_php_eidos_parastatikou_type_id != 23 && from_php_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi
      if ($('#warehouses_id_from').css('display')=='none') {
        if ($('#warehouses_id_from_triton').css('display')!='none') {
          $('#copy_warehouse_from_addr').click();
        }
      }
      if ($('#warehouses_id_to').css('display')=='none') {
        if ($('#warehouses_id_to_triton').css('display')!='none') {
          $('#copy_warehouse_to_addr').click();
        }
      }
    }    
  }

  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
      
    datasend='cmd=get&id=' + user_id + '&whi_mov_id=' + from_php_id;
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

              //$('#note_production').focus();
            }
            
            $('#dr_user_first_name').val(data.first_name);
            $('#dr_user_last_name').val(data.last_name);
            $('#dr_user_email').val(data.email);
            $('#dr_user_mobile').val((data.def_phone!='' ? data.def_phone : (data.phone_home!='' ? data.phone_home : data.mobile)));
            $('#dr_user_lang').val(data.lang);
            
            $('#pricelist_id').val(data.pricelist_id);


            $('#form_select_apostoli option').each(function() { 
              $(this).remove();
            }); 
            for (i = 0; i < data.extra_address.length; i++) {
  				    $('#form_select_apostoli').append('<option value="' + data.extra_address[i].id + '">' + data.extra_address[i].descr + '</option>');
  				  }     
  				  $('#form_select_apostoli').val(data.address_extra);      
            extra_address_select(data.address_extra);


                                    
            if (this.gks_dialog_gsis_result === false) {
              //console.log('gks_dialog_gsis_result false');
              $('#dr_user_ma_branch_fromuser').val(data.ma_branch);
              $('#dr_user_ma_odos').val(data.ma_odos);
              $('#dr_user_ma_arithmos').val(data.ma_arithmos);
              $('#dr_user_ma_orofos').val(data.ma_orofos);
              $('#dr_user_ma_perioxi').val(data.ma_perioxi);
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
   
            
            } else {
              //console.log('gks_dialog_gsis_result true');
      				mynymber=this.gks_dialog_gsis_result.basic_rec.postal_address_no.trim();
      				if (mynymber=='0') mynymber='';
      				
      				$('#dr_user_ma_branch_fromuser').val('0');              
              $('#dr_user_ma_odos').val(this.gks_dialog_gsis_result.basic_rec.postal_address.trim());
              $('#dr_user_ma_arithmos').val(mynymber);
              
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
              } else {
                $('#fiscal_position_id').val(1);
              }
            }

            //$('#div_pelati_acc_type_descr').html(data.acc_type_descr);
            
            

                        
            
            
            auto_copy_warehouse_xxx_addr();
            
            gks_myscroll();
            calc_pliroteo(); 
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
  }  
  
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
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Δελτίου Αποστολής'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          
          
          if (dialog_gsis_result.user_id>0) {
            $('#user_id').val(dialog_gsis_result.user_id);
            $('#user').val(dialog_gsis_result.gks_nickname);
            $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + dialog_gsis_result.user_id);
            $('#user_save').hide();
            
            gks_admin_get_user_data(dialog_gsis_result.user_id, dialog_gsis_result);
            
          } else {
            //$('#user_id').val('');
            //$('#user').val('');
            //$('#autocomplete_user_id').hide();
            //$('#user_save').show();
            
            //$('#dr_user_first_name').val('');
            //$('#dr_user_last_name').val('');
            if (dialog_gsis_result.basic_rec.i_ni_flag_descr =='ΦΠ') {
              onomasia_parts = dialog_gsis_result.basic_rec.onomasia.split(' ');
              if (onomasia_parts.length>=2) {
                if ($('#dr_user_first_name').val()=='') $('#dr_user_first_name').val(onomasia_parts[1].trim());
                if ($('#dr_user_last_name').val()=='')  $('#dr_user_last_name').val(onomasia_parts[0].trim());
              }
            }
            //$('#dr_user_email').val('');
            //$('#dr_user_mobile').val('');
            if ($('#dr_user_lang').val()=='') $('#dr_user_lang').val('el-GR');
  
                       
  
    				$('#dr_user_eponimia').val(dialog_gsis_result.basic_rec.onomasia);
    				$('#dr_user_title').val(dialog_gsis_result.basic_rec.commer_title);
    				$('#dr_user_afm').val(dialog_gsis_result.basic_rec.afm);
    				$('#dr_user_doy').val(dialog_gsis_result.basic_rec.doy_descr);
            //$('#dr_user_epaggelma').val('');
            for (i=0;i < dialog_gsis_result.firm_act_tab.length; i++) {
              if (dialog_gsis_result.firm_act_tab[i].kind=='1') {
                if ($('#dr_user_epaggelma').val()=='') $('#dr_user_epaggelma').val(dialog_gsis_result.firm_act_tab[i].cdescr);
                break;
              }
            }
    				mynymber=dialog_gsis_result.basic_rec.postal_address_no.trim();
    				if (mynymber=='0') mynymber='';
    				if ($('#dr_user_ma_odos').val()=='') $('#dr_user_ma_odos').val(dialog_gsis_result.basic_rec.postal_address.trim());
    		    if ($('#dr_user_ma_arithmos').val()=='') $('#dr_user_ma_arithmos').val(mynymber);
            //$('#dr_user_ma_perioxi').val('');
    				if ($('#dr_user_ma_poli').val()=='') $('#dr_user_ma_poli').val(dialog_gsis_result.basic_rec.postal_area_description);
    				if ($('#dr_user_ma_tk').val()=='') $('#dr_user_ma_tk').val(dialog_gsis_result.basic_rec.postal_zip_code);
            if ($('#dr_user_ma_country_id').val()=='0') $('#dr_user_ma_country_id').val(91);
            if ($('#dr_user_ma_nomos_id').val()=='0') $('#dr_user_ma_nomos_id').val('0').attr('data_nomos_id','');
            dr_user_ma_country_id_change();
            
            
            
            $('#form_select_apostoli option').each(function() { 
              if ($(this).attr('value') > 0 ) {
                $(this).remove();
              }
            });
            //$('#form_select_apostoli').val(-1);
            //extra_address_select(-1);
            //if ($('#pricelist_id').val()=='0') $('#pricelist_id').val(1);
            if (dialog_gsis_result.basic_rec.normal_vat_system_flag=='Y') {
              if ($('#fiscal_position_id').val()=='0' || $('#fiscal_position_id').val()=='1') $('#fiscal_position_id').val(11);
              $('#pricelist_id').val(2);
            } else {
              if ($('#fiscal_position_id').val()=='0') $('#fiscal_position_id').val(1);
              $('#pricelist_id').val(1);
            }

            auto_copy_warehouse_xxx_addr();
            
            gks_myscroll();
            calc_pliroteo();
          }
          
          $( this ).dialog( "close" );
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
  
  
  $('#btn_gsis_get').click(function() {
    $('#dialog_gsis_afm').val($('#dr_user_afm').val());
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
    
  });

  
  $('#dialog_gsis_run').click(function() {
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
        
    datasend='afm=' + dialog_gsis_afm + '&company_id=' + company_id + '&force=1';;
    
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
  					$('#dialog_gsis_ok').button( "option", "disabled", false);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
		    
    
    
  });
  

  var dialog_user_save;
  dialog_user_save = $( "#dialog_user_save" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_user_save_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Προσθήκη ή επιλογή χρήστη'),
        //icon: "ui-icon-circle-plus",
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
          
          $('body').addClass("myloading");
          $.ajax({
      			url: '/my/admin-users-add-exec.php',
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
                  $('#user_id').val(data.user_id);
                  $('#user').val(data.gks_nickname);
                  $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
                  $('#user_save').hide();
                  dialog_user_save.dialog( "close" );     					  
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
        id: "dialog_user_save_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
        

  });
  
  $('#user_save').click(function() {
    
    datasend='';    
    datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($("#dr_user_first_name").val().trim()));
    datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($("#dr_user_last_name").val().trim()));
    datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($("#dr_user_email").val().trim()));
    datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($("#dr_user_mobile").val().trim()));
    datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($("#dr_user_lang").val().trim()));
    datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_odos").val().trim()));
    datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_arithmos").val().trim()));
    datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_orofos").val().trim()));
    datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($("#dr_user_ma_perioxi").val().trim()));
    datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($("#dr_user_ma_poli").val().trim()));
    datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($("#dr_user_ma_tk").val().trim()));
    datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#dr_user_ma_country_id").val().trim());
    datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#dr_user_ma_nomos_id").val().trim());
    datasend+='&dr_user_eponimia='  + encodeURIComponent($.base64.encode($("#dr_user_eponimia").val().trim()));
    datasend+='&dr_user_title='  + encodeURIComponent($.base64.encode($("#dr_user_title").val().trim()));
    datasend+='&dr_user_afm='  + encodeURIComponent($.base64.encode($("#dr_user_afm").val().trim()));
    datasend+='&dr_user_doy='  + encodeURIComponent($.base64.encode($("#dr_user_doy").val().trim()));
    datasend+='&dr_user_epaggelma='  + encodeURIComponent($.base64.encode($("#dr_user_epaggelma").val().trim()));


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
    
    datasend+='&whi_mov_id=' + from_php_id;
    datasend+='&journal_id=' + $('#inv_acc_journal_id').val();
    datasend+='&seira_id=' + $('#inv_acc_seira_id').val();
    
        
    //console.log('user_save');
    //console.log(datasend);
    
    dialog_user_save.datasend=datasend;
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-users-add-exec.php',
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
  
  
  var gks_monada_span_aa=0;
  var sel_monada_id=0;
  function gks_monada_span_contextMenu_select(monada_id) {
    if (monada_id<0) return;
    if (gks_monada_span_aa<0) return;
    for (i=0; i<from_php_monades.length; i++) {
      if (from_php_monades[i].id == monada_id) {
        $('.gks_monada_span[data-aa=' + gks_monada_span_aa +']').html(from_php_monades[i].symbol).attr('data-mon-id',from_php_monades[i].id);
        calc_pliroteo('gks_quantity',gks_monada_span_aa);
        break;
      }
    }
  }
	var gks_monada_span_contextMenu={
		event: 'click',
    items: function(e) {
  		var arr = [];
  		for (i=0; i<from_php_monades.length; i++) {
  		  temptext=from_php_monades[i].descr + ' (' + from_php_monades[i].symbol + ')';
  		  if (sel_monada_id==from_php_monades[i].id) temptext='<b>' + temptext + '</b>';
    		arr.push({type: 'item', text: temptext, icon1: '', disabled: false, gks_monada_id: from_php_monades[i].id, click: function(e){	
    		  e.preventDefault();
  				gks_monada_span_contextMenu_select(this.gks_monada_id);
    		}});
    	}
      return arr;
    }
	};
	function gks_monada_span_click() {
    gks_monada_span_aa = parseInt($(this).attr('data-aa'));
    if (isNaN(gks_monada_span_aa)) gks_monada_span_aa=0;	  
    sel_monada_id=parseInt($(this).attr('data-mon-id')); 
    if (isNaN(sel_monada_id)) sel_monada_id=0;	  
	}
  $('.gks_monada_span').click(gks_monada_span_click);	
  $('.gks_monada_span').contextMenu(gks_monada_span_contextMenu);
  
  
  
  
  
  
 
  
  window.company_id_sub_id_change = function company_id_sub_id_change() {
    company_id=0;
    company_sub_id=0;
    v=$('#company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
    mov_whi_journal_id_fill('mov_whi_journal_id','mov_whi_seira_id',company_id,company_sub_id,0);
    
    gks_myscroll();
    calc_pliroteo(); 
    
  }
  $('#company_id_sub_id').change(company_id_sub_id_change);

  if (from_php_id == -1 && from_php_template_id==0) { 
    temp=$('#company_id_sub_id').val();
    if (temp!='' && temp!='0|0') {
      company_id_sub_id_change();
    } else if ($('#company_id_sub_id option').length==2) {
      $('#company_id_sub_id').val($($('#company_id_sub_id option')[1]).attr('value'));
      company_id_sub_id_change();
    }
  }

  
  



  window.mov_whi_journal_id_change = function mov_whi_journal_id_change() {
    v=$('#mov_whi_journal_id').val();
    acc_journal_id=parseInt(v); if (isNaN(acc_journal_id)) acc_journal_id=0; 
    mov_whi_seira_id_fill('mov_whi_seira_id',acc_journal_id,0);
    
    from_php_acc_eidos_parastatikou_id=parseInt($('#mov_whi_journal_id option:selected').attr('data-eidi_id'));
    from_php_eidos_parastatikou_type_id=parseInt($('#mov_whi_journal_id option:selected').attr('data-type_id'));
    from_php_eidos_parastatikou_need_prev=parseInt($('#mov_whi_journal_id option:selected').attr('data-need_prev'));
    from_php_eidos_parastatikou_need_afm=parseInt($('#mov_whi_journal_id option:selected').attr('data-need_afm'));
    from_php_eidos_parastatikou_stock_pros=parseInt($('#mov_whi_journal_id option:selected').attr('data-stock_pros'));
    from_php_acc_eidos_parastatikou_other_entity=parseInt($('#mov_whi_journal_id option:selected').attr('data-other_entity'));
    from_php_journal_has_correlated_invoices=parseInt($('#mov_whi_journal_id option:selected').attr('data-correlated_invoices'));
    from_php_journal_has_multiple_connected_marks=parseInt($('#inv_acc_journal_id option:selected').attr('data-multiple_connected_marks'));
    from_php_journal_has_packings_declarations=parseInt($('#inv_acc_journal_id option:selected').attr('data-packings_declarations'));
    
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    if (isNaN(from_php_eidos_parastatikou_type_id)) from_php_eidos_parastatikou_type_id=0;
    if (isNaN(from_php_eidos_parastatikou_need_prev)) from_php_eidos_parastatikou_need_prev=0;
    if (isNaN(from_php_eidos_parastatikou_need_afm)) from_php_eidos_parastatikou_need_afm=0;
    if (isNaN(from_php_eidos_parastatikou_stock_pros)) from_php_eidos_parastatikou_stock_pros=0;
    if (isNaN(from_php_acc_eidos_parastatikou_other_entity)) from_php_acc_eidos_parastatikou_other_entity=0;
    if (isNaN(from_php_journal_has_correlated_invoices)) from_php_journal_has_correlated_invoices=0;
    if (isNaN(from_php_journal_has_multiple_connected_marks)) from_php_journal_has_multiple_connected_marks=0;
    if (isNaN(from_php_journal_has_packings_declarations)) from_php_journal_has_packings_declarations=0;
    
    antisimvalomenos_label=gks_lang('αντισυμβαλλόμενος');
    for(i=0; i < eidi_parastatikon_types.length; i++) {
      if (eidi_parastatikon_types[i].id== from_php_eidos_parastatikou_type_id) {
        antisimvalomenos_label=eidi_parastatikon_types[i].label;
        break; 
      }
    }
    $('#antisimvalomenos_label').html(antisimvalomenos_label);

    if (from_php_eidos_parastatikou_need_afm == 0) {
      $('#div_parastatiko_timologio').hide(); 
    } else if (from_php_eidos_parastatikou_need_afm == 1) {
      $('#div_parastatiko_timologio').show();
    } else if (from_php_eidos_parastatikou_need_afm == -1) {
      $('#div_parastatiko_timologio').show();
    }
    
    if (from_php_acc_eidos_parastatikou_other_entity==0) {
      $('#div_other_entity').hide();
    } else {
      $('#div_other_entity').show();
    }
    if (from_php_journal_has_correlated_invoices==0) {
      $('#div_correlated_invoices').hide();
    } else {
      $('#div_correlated_invoices').show();
    }
    if (from_php_journal_has_multiple_connected_marks==0) {
      $('#div_multiple_connected_marks').hide();
    } else {
      $('#div_multiple_connected_marks').show();
    }
    if (from_php_journal_has_packings_declarations==0) {
      $('#div_packings_declarations').hide();
    } else {
      $('#div_packings_declarations').show();
    }
        
    set_warehouses_addrs();
    set_def_warehouses();
    gks_myscroll();
    calc_pliroteo();    
  }
  $('#mov_whi_journal_id').change(mov_whi_journal_id_change);
 
  function set_warehouses_addrs() {
    if (from_php_eidos_parastatikou_type_id == 24) { //apografi
      $('#div_show_user').hide();
      $('#warehouses_id_from_div').hide();
      $('.warehouses_id_from_elem').hide();$('#warehouses_id_from_triton').show();
      $('#warehouses_id_from_elem_div').hide();
      $('.warehouses_id_from_addr').hide();
      $('.warehouses_id_to_elem').show();$('#warehouses_id_to_triton').hide();
      $('.warehouses_id_to_elem_div').show();
      $('.warehouses_id_to_addr').hide();
      
      $('#warehouses_id_to_label').html(gks_lang('Αφορά'));
      $('#div_aade_skopos_diakinisis_id').hide();
      $('#div_fiscal_position_id').hide();
      $('#div_pricelist_id').hide();
      $('#div_apografi_label').show();
      $('.gks_quantity').addClass('gks_quantity_apografi');
      
    } else if (from_php_eidos_parastatikou_type_id == 23) { //endodiakinisi
      $('#div_show_user').hide();
      $('#warehouses_id_from_div').show();
      $('.warehouses_id_from_elem').show();$('#warehouses_id_from_triton').hide();
      $('#warehouses_id_from_elem_div').show();
      $('.warehouses_id_to_elem').show();$('#warehouses_id_to_triton').hide();
      $('.warehouses_id_to_elem_div').show();

      if (from_php_seira_isdeliverynote==0) {
        $('.warehouses_id_from_addr').hide();
        $('.warehouses_id_to_addr').hide();
      } else {
        $('.warehouses_id_from_addr').show();
        $('.warehouses_id_to_addr').show();
      }
      
      $('#warehouses_id_to_label').html(gks_lang('Προς'));
      $('#div_aade_skopos_diakinisis_id').show();
      $('#div_fiscal_position_id').show();
      $('#div_pricelist_id').show();
      $('#div_apografi_label').hide();
      $('.gks_quantity').removeClass('gks_quantity_apografi');
    } else {
      if (from_php_eidos_parastatikou_stock_pros == 1) {//erxete, auksanei to ypoloipo stock
        $('#div_show_user').show();
        $('#warehouses_id_from_div').show();
        $('.warehouses_id_from_elem').hide();$('#warehouses_id_from_triton').show();
        $('#warehouses_id_from_elem_div').show();
        $('.warehouses_id_to_elem').show();$('#warehouses_id_to_triton').hide();
        $('.warehouses_id_to_elem_div').show();

        if (from_php_seira_isdeliverynote==0) {
          $('.warehouses_id_from_addr').hide();
          $('.warehouses_id_to_addr').hide();
        } else {
          $('.warehouses_id_from_addr').show();
          $('.warehouses_id_to_addr').show();
        }

        $('#warehouses_id_to_label').html(gks_lang('Προς'));
        $('#div_aade_skopos_diakinisis_id').show();
        $('#div_fiscal_position_id').show();
        $('#div_pricelist_id').show();
        $('#div_apografi_label').hide();
        $('.gks_quantity').removeClass('gks_quantity_apografi');

      } else if (from_php_eidos_parastatikou_stock_pros == -1) { //feuvei, meionete to ypoloipo stock
        $('#div_show_user').show();
        $('#warehouses_id_from_div').show();
        $('.warehouses_id_from_elem').show();$('#warehouses_id_from_triton').hide();
        $('#warehouses_id_from_elem_div').show();
        $('.warehouses_id_to_elem').hide();$('#warehouses_id_to_triton').show();
        $('.warehouses_id_to_elem_div').show();

        if (from_php_seira_isdeliverynote==0) {
          $('.warehouses_id_from_addr').hide();
          $('.warehouses_id_to_addr').hide();
        } else {
          $('.warehouses_id_from_addr').show();
          $('.warehouses_id_to_addr').show();
        }  

        $('#warehouses_id_to_label').html(gks_lang('Προς'));
        $('#div_aade_skopos_diakinisis_id').show();
        $('#div_fiscal_position_id').show();
        $('#div_pricelist_id').show();
        $('#div_apografi_label').hide();
        $('.gks_quantity').removeClass('gks_quantity_apografi');
      }
    }

    
  } 
 
 
  function set_def_warehouses() {
    if (from_php_eidos_parastatikou_type_id>0 && from_php_eidos_parastatikou_type_id != 23 && from_php_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi
      var old_warehouses_id_from=parseInt($('#warehouses_id_from').attr('data-id'));
      var old_warehouses_id_to=  parseInt($('#warehouses_id_to').attr('data-id'));
      if (isNaN(old_warehouses_id_from)) old_warehouses_id_from=0;
      if (isNaN(old_warehouses_id_to)) old_warehouses_id_to=0;
       
      mydata={};
      mydata.term='***';
      company_id=0;
      company_sub_id=0;
      v=$('#company_id_sub_id').val();
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
      if (company_id>0) {
        mydata.company_id=company_id;
        if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
      }
      $.getJSON('admin-autocomplete-warehouse.php',mydata,function(data, textStatus, jqXHR) {
        if (textStatus=='success') {
          if ($('#warehouses_id_from').css('display')!='none') {
            myfound=false;
            for (i=0;i<data.list.length;i++) if (data.list[i].id==old_warehouses_id_from) {myfound=true;break;}
            if (myfound==false) {
              if (data.list.length>=1) {
                $('#warehouses_id_from').attr('data-id',data.list[0].id).val(data.list[0].value);
                $('#load_branch').val(data.list[0].warehouse_branch);
                $('#load_odos').val(data.list[0].warehouse_odos);
                $('#load_arithmos').val(data.list[0].warehouse_arithmos);
                $('#load_orofos').val(data.list[0].warehouse_orofos);
                $('#load_perioxi').val(data.list[0].warehouse_perioxi);
                $('#load_poli').val(data.list[0].warehouse_poli);
                $('#load_tk').val(data.list[0].warehouse_tk);
                $('#load_country_id').val(data.list[0].warehouse_country_id);
                nomos_fill('load_nomos_id',data.list[0].warehouse_country_id,data.list[0].warehouse_nomos_id);
              } else {
                $('#warehouses_id_from').attr('data-id',0).val('');
              }
            }
          }
          if ($('#warehouses_id_to').css('display')!='none') {
            myfound=false;
            for (i=0;i<data.list.length;i++) if (data.list[i].id==old_warehouses_id_to) {myfound=true;break;}
            if (myfound==false) {
              if (data.list.length>=1) {
                $('#warehouses_id_to').attr('data-id',data.list[0].id).val(data.list[0].value);
                $('#deli_branch').val(data.list[0].warehouse_branch);
                $('#deli_odos').val(data.list[0].warehouse_odos);
                $('#deli_arithmos').val(data.list[0].warehouse_arithmos);
                $('#deli_orofos').val(data.list[0].warehouse_orofos);
                $('#deli_perioxi').val(data.list[0].warehouse_perioxi);
                $('#deli_poli').val(data.list[0].warehouse_poli);
                $('#deli_tk').val(data.list[0].warehouse_tk);
                $('#deli_country_id').val(data.list[0].warehouse_country_id);
                nomos_fill('deli_nomos_id',data.list[0].warehouse_country_id,data.list[0].warehouse_nomos_id);
              } else {
                $('#warehouses_id_to').attr('data-id',0).val('');
              }
            }
          }
        } 
      });
    }    
  }
  
  if (from_php_id==-1 && from_php_template_id==0) {
    set_warehouses_addrs();
    set_def_warehouses();
  }
  
  window.mov_whi_seira_id_change = function mov_whi_seira_id_change() {
    whi_seira_id=parseInt($('#mov_whi_seira_id').val()); if (isNaN(whi_seira_id)) whi_seira_id=0; 
    is_xeirografi=parseInt($('#mov_whi_seira_id option:selected').attr('data-is_xeirografi')); if (isNaN(is_xeirografi)) is_xeirografi=0; 
    is_deliverynote=parseInt($('#mov_whi_seira_id option:selected').attr('data-is_deliverynote')); if (isNaN(is_deliverynote)) is_deliverynote=0; 
    is_reverse_delivery_note=parseInt($('#mov_whi_seira_id option:selected').attr('data-is_reverse_delivery_note')); if (isNaN(is_reverse_delivery_note)) is_reverse_delivery_note=0; 
    from_php_seira_isdeliverynote=is_deliverynote;
    //console.log('from_php_seira_isdeliverynote',from_php_seira_isdeliverynote);

    if (is_xeirografi!=0) {
      $('#mov_whi_number_int').prop('disabled' , false);
      $('#submit_button_080listing').show();
      $('#submit_button_090ekdosi').hide();
    } else {
      $('#mov_whi_number_int').prop('disabled' , true);
      $('#submit_button_080listing').hide();
      $('#submit_button_090ekdosi').show();
    }
    if (is_reverse_delivery_note!=0) {
      $('#reverse_delivery_purpose_div').show();
    } else {
      $('#reverse_delivery_purpose_div').hide();
    }
    set_warehouses_addrs();
    gks_myscroll();
    //calc_pliroteo();
  }
  $('#mov_whi_seira_id').change(mov_whi_seira_id_change);

  
  
  function eidi_table_colors() {
    $('.gks_eidos').each(function(index) {
      aa=$(this).attr('data-aa');
      
      if (index % 2) {
        $(this).removeClass('gks_eidos_even').addClass('gks_eidos_odd'); 
        $('.gks_eidos_extra[data-aa=' + aa + ']').removeClass('gks_eidos_even').addClass('gks_eidos_odd'); 
        if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
          $('.gks_eidos_lots_serials[data-aa=' + aa + ']').removeClass('gks_eidos_even').addClass('gks_eidos_odd'); 
        }
      } else {
        $(this).removeClass('gks_eidos_odd').addClass('gks_eidos_even');  
        $('.gks_eidos_extra[data-aa=' + aa + ']').removeClass('gks_eidos_odd').addClass('gks_eidos_even');  
        if (from_php_GKS_PRODUCT_LOTS_SERIALS) {
          $('.gks_eidos_lots_serials[data-aa=' + aa + ']').removeClass('gks_eidos_odd').addClass('gks_eidos_even');
        }  
      }
    });
    
  }
  eidi_table_colors();  
  
  
    
 

  


  
  
  
  


  $.fn.animateRotate = function(start,angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
      args.complete = $.proxy(args.complete, e);
      args.step = function(now) {
        $.style(e, 'transform', 'rotate(' + now + 'deg)');
        if (step) return step.apply(e, arguments);
      };
  
      $({deg: start}).animate({deg: angle}, args);
    });
  };
     

   
  
    
  


  
  
  
  
  //gks_myscroll();


  
 
  
  
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
    
    if ($('#gks_paroxos_send_pdf').length>0) {
      datasend+='&gks_paroxos_send_pdf=' + ($('#gks_paroxos_send_pdf').is(':checked') ? '1' : '0');  
    }
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
			url: 'admin-whi-mov-item-pdf.php?id=' + from_php_id,
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
    					    if (run_from_steps==false) {
    					      myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					      gks_eraseCookie('whi_mov_steps');
    					    } else {
          					if (data.redirect=='') {
          					  window.location.reload();
          					} else {
          					  window.location.href = $.base64.decode(data.redirect);
          					}    					      
    					    }
    					  } else {
    					    myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					    gks_eraseCookie('whi_mov_steps');
    					  }    					  
    					} else {
      					if (data.redirect=='') {
      					  window.location.reload();
      					} else {
      					  window.location.href = $.base64.decode(data.redirect);
      					}
      					gks_eraseCookie('whi_mov_steps');
      				}
      			}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						gks_eraseCookie('whi_mov_steps');
					}
				}
			}
			
		});     
  }
  
  
  $('#submit_button_print').click(function() {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το δελτίο αποστολής'));
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
    gks_paroxos_send_pdf_ed();
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  if (dheight> 650) dheight=650;
	  dialog_print.dialog('option', 'width', dwidth);
	  dialog_print.dialog('option', 'height', dheight);
	  $('#dialog_print').parent().css({position:'fixed'});      
    dialog_print.dialog('open'); 
    
    $('.gks_print_thump_div_selected').removeClass('gks_print_thump_div_selected');
    
    sel_company_id_sub_id=$('#company_id_sub_id').val();
    sel_mov_whi_journal_id=parseInt($('#mov_whi_journal_id').val()); if (isNaN(sel_mov_whi_journal_id)) sel_mov_whi_journal_id=0;
    sel_mov_whi_seira_id=parseInt($('#mov_whi_seira_id').val()); if (isNaN(sel_mov_whi_seira_id)) sel_mov_whi_seira_id=0;

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
                if (from_php_perm_print_forms[i].perm_acc_journal_ids.includes(sel_mov_whi_journal_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_seires_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_seires_ids.includes(sel_mov_whi_seira_id)==false) {
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
    gks_paroxos_send_pdf_ed();
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
  

  var dialog_aade;
  dialog_aade = $('#dialog_aade').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_aade_go",
        html: '<i class="fa fa-cloud-upload-alt"></i> '+gks_lang('Αποστολή'),
        //icon: "ui-icon-print",  
        click: function() {
          mysubmit('aade_send'); 
        }
      },
      {
        id: "dialog_aade_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#submit_button_aade_send').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Ενημερώστε πρώτα το παραστατικό'));
      return;
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 400) dheight=400;
	  dialog_aade.dialog('option', 'width', dwidth);
	  dialog_aade.dialog('option', 'height', dheight);
	  $('#dialog_aade').parent().css({position:'fixed'});      
    dialog_aade.dialog('open'); 

  });

  var dialog_paroxos;
  dialog_paroxos = $('#dialog_paroxos').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_paroxos_go",
        html: '<i class="fa fa-cloud-upload-alt"></i> '+gks_lang('Αποστολή'),
        //icon: "ui-icon-print",  
        click: function() {
          mysubmit('paroxos_send'); 
        }
      },
      {
        id: "dialog_paroxos_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#submit_button_paroxos_send').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Ενημερώστε πρώτα το δελτίο αποστολής'));
      return;
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 400) dheight=400;
	  dialog_paroxos.dialog('option', 'width', dwidth);
	  dialog_paroxos.dialog('option', 'height', dheight);
	  $('#dialog_paroxos').parent().css({position:'fixed'});      
    dialog_paroxos.dialog('open'); 

  });



  var timer_pist_orange=null;
  function timer_pist_orange_check() {
    if (from_php_is_credit_memo==false) return;
    //console.log('timer_pist_orange_check');
    tposotita=parseFloat($('#gks_products_posotita').attr('data-val'));
    rposotita=parseFloat($('#rest_products_posotita_sum').attr('data-val'));
    if (isNaN(tposotita)) tposotita=0;
    if (isNaN(rposotita)) rposotita=0;
    
    if (tposotita>rposotita) {
      if (timer_pist_orange == null) {
        timer_pist_orange = setInterval(function () {
          //console.log('timer_pist_orange');
          if ($('#gks_products_posotita').hasClass('span_bg_orange')) {
            $('#gks_products_posotita').removeClass('span_bg_orange');
            $('#rest_gks_price_net_sum').removeClass('span_bg_orange');
          } else {
            $('#gks_products_posotita').addClass('span_bg_orange');
            $('#rest_gks_price_net_sum').addClass('span_bg_orange');
          }
        }, 1000);        
      } 
    } else {
      if (timer_pist_orange != null) {
        clearTimeout(timer_pist_orange);
        timer_pist_orange=null;
        $('#gks_products_posotita').removeClass('span_bg_orange');
        $('#rest_gks_price_net_sum').removeClass('span_bg_orange');
      }
      
    }    
    

    
  }
  if (from_php_is_credit_memo) {
    timer_pist_orange_check();
  }

    
  


  $('#submit_button_create_acc_pay').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το δελτίο αποστολής'));
      return;
    }
    myconfirm(gks_lang('Σίγουρα θέλετε να δημιουργήσετε σχετική πληρωμή για το τρέχον δελτίο αποστολής;'),
    'gks_mysubmit_create_acc_pay');
    return false;
  });
  window.gks_mysubmit_create_acc_pay = function() {
    mysubmit('create_acc_pay');
  }    
  
  
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

  $('#warehouses_id_from').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      if (from_php_eidos_parastatikou_type_id>0 && from_php_eidos_parastatikou_type_id != 23 && from_php_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi

        company_id=0;
        company_sub_id=0;
        v=$('#company_id_sub_id').val();
        if (v === undefined || v === null) v='';
        parts=v.split('|');
        if (parts.length==2) {
          company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
          company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
        }
        //console.log(company_id,company_sub_id);
        if (company_id>0) {
          mydata.company_id=company_id;
          if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
        }
      }
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
    select: function( event, ui ) {
      $('#warehouses_id_from').attr('data-id',ui.item.id);
      $('#load_branch').val(ui.item.warehouse_branch);
      $('#load_odos').val(ui.item.warehouse_odos);
      $('#load_arithmos').val(ui.item.warehouse_arithmos);
      $('#load_orofos').val(ui.item.warehouse_orofos);
      $('#load_perioxi').val(ui.item.warehouse_perioxi);
      $('#load_poli').val(ui.item.warehouse_poli);
      $('#load_tk').val(ui.item.warehouse_tk);
      $('#load_country_id').val(ui.item.warehouse_country_id);
      nomos_fill('load_nomos_id',ui.item.warehouse_country_id,ui.item.warehouse_nomos_id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#warehouses_id_from').val('').attr('data-id','0');
      }
    }
  });
  $('#warehouses_id_to').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      if (from_php_eidos_parastatikou_type_id>0 && from_php_eidos_parastatikou_type_id != 23 && from_php_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi

        company_id=0;
        company_sub_id=0;
        v=$('#company_id_sub_id').val();
        if (v === undefined || v === null) v='';
        parts=v.split('|');
        if (parts.length==2) {
          company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
          company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
        }
        //console.log(company_id,company_sub_id);
        if (company_id>0) {
          mydata.company_id=company_id;
          if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
        }
      }
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
    select: function( event, ui ) {
      $('#warehouses_id_to').attr('data-id',ui.item.id);
      $('#deli_branch').val(ui.item.warehouse_branch);
      $('#deli_odos').val(ui.item.warehouse_odos);
      $('#deli_arithmos').val(ui.item.warehouse_arithmos);
      $('#deli_orofos').val(ui.item.warehouse_orofos);
      $('#deli_perioxi').val(ui.item.warehouse_perioxi);
      $('#deli_poli').val(ui.item.warehouse_poli);
      $('#deli_tk').val(ui.item.warehouse_tk);
      $('#deli_country_id').val(ui.item.warehouse_country_id);
      nomos_fill('deli_nomos_id',ui.item.warehouse_country_id,ui.item.warehouse_nomos_id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#warehouses_id_to').val('').attr('data-id','0');
      }
    }
  });
  
  function note_doc_change() {gks_resize_textarea($(this));}
  $('#note_doc').on(mychange, note_doc_change);
  gks_resize_textarea($('#note_doc'));
  
  function note_logistirio_change() {gks_resize_textarea($(this));}
  $('#note_logistirio').on(mychange, note_logistirio_change);
  gks_resize_textarea($('#note_logistirio'));
  

  function gks_eidos_lots_serials_name_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        
        myelem=$(this)[0];
        myelem=myelem.element;
        
        aa=myelem.attr('data-aa');
        ls=myelem.attr('data-ls');
        
        
        curr_product_id=parseInt($('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product'));
        if (isNaN(curr_product_id)) curr_product_id=2; if (curr_product_id<2) curr_product_id=2;
        //console.log($(this).element.attr('class'));
        //console.log(aa,ls,);
        
        mydata={
          term: request.term,
          product_id:curr_product_id,
          //and_variable:1,
        };
        $.ajax({
          url: 'admin-autocomplete-lot-serial.php',
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
      select: function( event, ui ) {
        need_save=true;
        aa=$(this).attr('data-aa');
        ls=$(this).attr('data-ls');
        datasend='cmd=get&id=' + ui.item.id +
                  '&aa=' + aa + 
                  '&ls=' + ls;
        
        $.ajax({
          gks_aa:aa,
          gks_ls:ls,
    			url: 'admin-get-lot-serial.php',
    			type: 'POST',
    			cache: false,
    			dataType: 'json',
    			data: datasend,
    			error : function(jqXHR ,textStatus,  errorThrown) {
    				myalert('error:' + jqXHR.responseText);
    			},				
    			success: function(data) {
    			  need_save=true;
    				if (!data) {
    					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    				} else {
    					if (data.success == true) {
    					  $('.gks_eidos_lots_serials_zoom[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').show().attr('href','admin-products-lots-item.php?id=' + data.id);
    					  $('.gks_eidos_lots_serials_name[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').attr('data-product-id',data.lotproduct_id);
    					  $('.gks_eidos_lots_serials_descr[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').val(data.lot_descr).prop('readonly',true);
    					  $('.gks_eidos_lots_serials_date_production[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').val(data.lot_date_production).prop('readonly',true);
    					  $('.gks_eidos_lots_serials_date_expire[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').val(data.lot_date_expire).prop('readonly',true);
    					  
    					  gks_resize_textarea($('.gks_eidos_lots_serials_descr[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']'));
    					} else {
    					  $('.gks_eidos_lots_serials_zoom[data-aa=' + this.gks_aa + '][data-ls=' + this.gks_ls + ']').hide();
    						myalert('error:' + $.base64.decode(data.message));
    					}
    				}
    			}
    		}); 
		
		                    
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          //$(this).val('');
          aa=$(this).attr('data-aa');
          ls=$(this).attr('data-ls');
          $('.gks_eidos_lots_serials_name[data-aa=' + aa + '][data-ls=' + ls + ']').attr('data-product-id','0');
          $('.gks_eidos_lots_serials_zoom[data-aa=' + aa + '][data-ls=' + ls + ']').hide();
          
				  $('.gks_eidos_lots_serials_descr[data-aa=' + aa + '][data-ls=' + ls + ']').val('').prop('readonly',false);
				  $('.gks_eidos_lots_serials_date_production[data-aa=' + aa + '][data-ls=' + ls + ']').val('').prop('readonly',false);
				  $('.gks_eidos_lots_serials_date_expire[data-aa=' + aa + '][data-ls=' + ls + ']').val('').prop('readonly',false);

          gks_resize_textarea($('.gks_eidos_lots_serials_descr[data-aa=' + aa + '][data-ls=' + ls + ']'));
          
        }
      },
           
    });
  }
  
  $('.gks_eidos_lots_serials_name').each(function() {
    gks_eidos_lots_serials_name_autocomplete($(this));
  });  

  function gks_eidos_lots_serials_descr_change() {gks_resize_textarea($(this));}
  $('.gks_eidos_lots_serials_descr').on(mychange, gks_eidos_lots_serials_descr_change);
  $('.gks_eidos_lots_serials_descr').each(function() {gks_resize_textarea($(this));});
  



  
  function gks_eidos_lots_serials_date_production_datetimepicker(myelem) {
    myelem.datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        if (from_php_perm_ret_edit==false) return;
        need_save=true;
      }
    }));
  }
  $('.gks_eidos_lots_serials_date_production').each(function() {
    gks_eidos_lots_serials_date_production_datetimepicker($(this));
  });  
  
  function gks_eidos_lots_serials_date_expire_datetimepicker(myelem) {
    myelem.datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        if (from_php_perm_ret_edit==false) return;
        need_save=true;
      }
    }));
  }
  $('.gks_eidos_lots_serials_date_expire').each(function() {
    gks_eidos_lots_serials_date_expire_datetimepicker($(this));
  });  
  
  function gks_eidos_lots_serials_quantity_calc(aa) {
    
    var span_eidos_lots_serials_sum_quantity=0;
    $('.gks_eidos_lots_serials_quantity[data-aa=' + aa + ']').each(function() {
      temp=parseFloat($(this).val());
      if (isNaN(temp)) temp=0;
      span_eidos_lots_serials_sum_quantity+=temp;
    });
    $('.span_eidos_lots_serials_sum_quantity[data-aa=' + aa + ']').html(span_eidos_lots_serials_sum_quantity.myNumberFormatNo0Local(true));
    
    product_quantity= parseFloat($('.gks_quantity[data-aa=' + aa + ']').val());
    if (isNaN(product_quantity)) product_quantity=0;
    if (Math.abs(product_quantity-span_eidos_lots_serials_sum_quantity)<=0.00001) {
      $('.img_eidos_lots_serials_sum_quantity[data-aa=' + aa + ']').hide();
    } else {
      $('.img_eidos_lots_serials_sum_quantity[data-aa=' + aa + ']').show();
    } 
  }
  

  function gks_eidos_lots_serials_quantity_change (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    event.preventDefault();  
    aa=parseInt($(this).attr('data-aa'));
    gks_eidos_lots_serials_quantity_calc(aa);
  }
  $('.gks_eidos_lots_serials_quantity').on(mychange, gks_eidos_lots_serials_quantity_change);

  function gks_add_eidos_lots_serials_visible(aa) {
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '] .gks_add_eidos_lots_serials').each(function() {
      $(this).hide();
    });
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '] .gks_add_eidos_lots_serials:last').show();
    if ($('.div_gks_eidos_lots_serials[data-aa=' + aa + ']').length==0) {
      $('.div_add_eidos_lots_serials[data-aa=' + aa + '] .gks_add_eidos_lots_serials').show();
      $('.div_eidos_lots_serials_sum_quantity[data-aa=' + aa + ']').hide();
    } else {
      $('.div_add_eidos_lots_serials[data-aa=' + aa + '] .gks_add_eidos_lots_serials').hide();
      $('.div_eidos_lots_serials_sum_quantity[data-aa=' + aa + ']').show();
    }
  }
  
  function gks_delete_eidos_lots_serials_click() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    ls=parseInt($(this).attr('data-ls'));
    if (isNaN(ls)) ls=0;
    if (ls<=0) return;
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + ']').remove();
    gks_add_eidos_lots_serials_visible(aa);
    gks_eidos_lots_serials_quantity_calc(aa);
  }
  $('.gks_delete_eidos_lots_serials').click(gks_delete_eidos_lots_serials_click);

  function gks_add_eidos_lots_serials_click() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
      
    ls=parseInt($('.div_gks_eidos_lots_serials[data-aa=' + aa + ']:last').attr('data-ls'));
    if (isNaN(ls)) ls=0;
    ls++;
    //console.log('aa: ' + aa + ' xx: ' + xx);

    product_lot_serial=$('.gks_eidos_lots_serials[data-aa=' + aa + ']').attr('data-val-lot-serial');
    
    div_lots_serials=
    
    '<div class="form-group1 row div_gks_eidos_lots_serials" style="margin: 0px;" data-aa="' + aa + '" data-ls="' + ls + '">' +
      '<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >' +
        '<input type="text" class="form-control form-control-sm gks_eidos_lots_serials_name" ' +
        'data-aa="' + aa + '" ' +
        'data-ls="' + ls + '" ' +
        'data-product-id="" ' +
        'value="" ' +
        'placeholder="'+gks_lang('Παρτίδα/Serial Number')+'">' +
        '<a href="#" class="gks_eidos_lots_serials_zoom" ' +
          'data-aa="' + aa + '" data-ls="' + ls + '" '+
          'style="display:none;" ' +
          '><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή Παρτίδας/Serial Number')+'"></i></a>' +
      '</div>' +
      '<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >' +
        '<input style="text-align:right;" type="number" class="form-control form-control-sm gks_eidos_lots_serials_quantity" ' +
        'data-aa="' + aa + '" ' +
        'data-ls="' + ls + '" ' +
        'value="1" ' +
        (product_lot_serial=='serial' ? ' readonly ' : '') +
        'min=0 step="' + from_php_GKS_INPUT_STEP_POSOTITA + '" ' +
        'placeholder="'+gks_lang('Ποσότητα')+'"' +
        '>' +
        '<i class="fas fa-boxes gks_eidos_lots_serials_balance"></i>' + 
      '</div>' +
      '<div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >' +
        '<textarea class="form-control form-control-sm gks_eidos_lots_serials_descr" rows="1" ' +
          'data-aa="' + aa + '" ' +
          'data-ls="' + ls + '" ' +
          'placeholder="'+gks_lang('Περιγραφή')+'" ' +
          '></textarea>' +
      '</div>' +
      '<div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >' +
        '<input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_production" ' +
        'data-aa="' + aa + '" ' +
        'data-ls="' + ls + '" ' +
        'value="" ' +
        'placeholder="'+gks_lang('Ημερ. Παραγωγής')+'">' +
      '</div>' +
      '<div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >' +
        '<input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_expire" ' +
        'data-aa="' + aa + '" ' +
        'data-ls="' + ls + '" ' +
        'value="" ' +
        'placeholder="'+gks_lang('Ημερ. Λήξης')+'">' +
      '</div>' +
      '<div class="col-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 gks_items_col text-center offset-sm-4 offset-md-4 offset-lg-0 ">' +
        '<i class="fas fa-trash-alt gks_delete_eidos_lots_serials" data-aa="' + aa + '" data-ls="' + ls + '" style=""></i> ' +
        '<i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="' + aa + '" style=""></i>' +
      '</div>' +
    '</div>';     
   
    if (ls==1) {//einai to proto
      $('.div_add_eidos_lots_serials[data-aa=' + aa + ']').after(div_lots_serials);
    } else {
      $('.div_gks_eidos_lots_serials[data-aa=' + aa + ']:last').after(div_lots_serials);
    }
    
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_name').each(function() {
      gks_eidos_lots_serials_name_autocomplete($(this));
    }); 
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_descr').on(mychange, gks_eidos_lots_serials_descr_change);
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_date_production').each(function() {
      gks_eidos_lots_serials_date_production_datetimepicker($(this));
    });
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_date_expire').each(function() {
      gks_eidos_lots_serials_date_expire_datetimepicker($(this));
    });
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_quantity').on(mychange, gks_eidos_lots_serials_quantity_change);
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_delete_eidos_lots_serials').click(gks_delete_eidos_lots_serials_click);
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_add_eidos_lots_serials').click(gks_add_eidos_lots_serials_click);
    $('.div_gks_eidos_lots_serials[data-aa=' + aa + '][data-ls=' + ls + '] .gks_eidos_lots_serials_balance').tooltipster(gks_eidos_lots_serials_balance_options);
    
    gks_add_eidos_lots_serials_visible(aa);
    gks_eidos_lots_serials_quantity_calc(aa);
  }
  
  $('.gks_add_eidos_lots_serials').click(gks_add_eidos_lots_serials_click);
  
  gks_eidos_lots_serials_balance_options={
    theme: 'tooltipster-noir',
    contentAsHTML: true, 
    interactive:true,
    animation: 'fade',
    updateAnimation: false,
    content: '<img src="img/wait.gif">',
    functionBefore: function(instance, helper) {

      // we'll make this function asynchronous and allow the tooltip to go ahead and show the loading notification while fetching our data
      //continueTooltip();
      
      elem=$(helper.origin).parent().find('.gks_eidos_lots_serials_quantity');
      aa=elem.attr('data-aa');
      ls=elem.attr('data-ls');
      lot_serial_text=$('.gks_eidos_lots_serials_name[data-aa=' + aa + '][data-ls=' + ls + ']').val();
      lot_product_id=$('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product');
      
      //console.log(aa,ls,lot_serial_text,lot_product_id);

      datasend=
        '&aa=' + aa +
        '&ls=' + ls +
        '&lot_serial_text=' + encodeURIComponent($.base64.encode(lot_serial_text)) +
        '&lot_product_id=' + lot_product_id;
      $.ajax({
        url: 'admin-get-lot-serial-balance.php',
        type: 'POST',
        cache: false,
		    dataType: 'json',
		    data: datasend,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				instance.content(gks_lang('Σφάλμα')+': ' + jqXHR.responseText);
  			},			    
        success: function(data) {
          if (data.success == true) {
            instance.content($.base64.decode(data.html));
          } else {
            instance.content($.base64.decode(data.message));
          }
        }
      });
    },
    functionAfter: function(instance, helper) {
      instance.content('<img src="img/wait.gif">');
    },
    
  };
  $('.gks_eidos_lots_serials_balance').tooltipster(gks_eidos_lots_serials_balance_options);

  function gks_paroxos_ajax(mycmd) {
   
    datasend='&mycmd='+mycmd;
    datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
    
    $('body').addClass("myloading"); 
    $.ajax({
			url: '/my/admin-acc-xxx-item-paroxos.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_mycmd:mycmd,
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
  					if (this.gks_mycmd=='paroxos_files_check') {
  					  $([document.documentElement, document.body]).animate({
                scrollTop: $("#gks_arxeia").offset().top
              }, 500);
            }
//  					need_save=false;
            if (typeof data.save_but_message == 'undefined') data.save_but_message='';
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='OK') {
  					    myalert('ok:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else if ($.base64.decode(data.message)=='INFO') {
  					    myalert('info:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else if ($.base64.decode(data.message)=='WARNING') {
  					    myalert('warning:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else {
  					    myalert('error:' + $.base64.decode(data.save_but_message), '' ,true);
  					  }
  					} else {
    					myalert('ok:' + $.base64.decode(data.message), '', true);
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  
    
  }    
      
  $('#paxoros_check_processing').click(function() {
    gks_paroxos_ajax('paroxos_status_check');
  });
  $('#paxoros_check_files').click(function() {
    gks_paroxos_ajax('paroxos_files_check');
  });
  $('#paxoros_send_pdf').click(function() {
    gks_paroxos_ajax('paxoros_send_pdf');
  });  
  $('#paroxos_get_docstate').click(function() {
    gks_paroxos_ajax('paroxos_get_docstate');
  });  
  
  if ($('#gks_paroxos_send_pdf').length==1) {
    var gks_paroxos_send_pdf_sw = new Switchery(document.querySelector('#gks_paroxos_send_pdf'),gks_switchery_defaults());
  }
  function gks_paroxos_send_pdf_ed() {
    if ($('#gks_paroxos_send_pdf').length==0) return;
    if ($('#dialog_print_file_type_pdf').prop('checked')) {
      gks_paroxos_send_pdf_sw.enable();
    } else {
      if ($('#gks_paroxos_send_pdf').is(':checked')) $('#gks_paroxos_send_pdf').click();
      gks_paroxos_send_pdf_sw.disable();
    }
  }
  
  gks_paroxos_send_pdf_ed();  

  
  gks_submit_button_090ekdosi_steps={
    theme: 'tooltipster-noir',
    contentAsHTML: true, 
    interactive:true,
    animation: 'fade',
    updateAnimation: false,
    //triggerClose: {touchleave: false},
		//delay: 3000,
		delayTouch: [300, 5000],  
		content: 
      '3) <button type="button" style="margin-bottom:10px;" class="btn btn-sm btn-primary" id="se_steps_email" onclick=\'gks_se_steps_start("email");\'>+ '+gks_lang('email')+'</button><br>' +
      '2) <button type="button" style="margin-bottom:10px;" class="btn btn-sm btn-primary" id="se_steps_print" onclick=\'gks_se_steps_start("print");\'>+ '+gks_lang('Εκτύπωση')+'</button><br>' +
      '1) <button type="button" style="margin-bottom:10px;" class="btn btn-sm btn-primary" id="se_steps_aade"  onclick=\'gks_se_steps_start("aade");\'>+ '+gks_lang('ΑΑΔΕ/Πάροχος')+'</button><br>',
    functionBefore: function(instance, helper) {
      //console.log('ssssssssssss');
      $('#se_steps_aade' ).click(function() {steps=[];steps.push('aade');se_steps_start(steps);});
      $('#se_steps_print').click(function() {steps=[];steps.push('aade');steps.push('print');se_steps_start(steps);});
      $('#se_steps_email').click(function() {steps=[];steps.push('aade');steps.push('print');steps.push('email');se_steps_start(steps);});
    },
    functionAfter: function(instance, helper) {
      //instance.content('<img src="img/wait.gif">');
      //console.log('sfffffff');
    },
    
  };
  $('#submit_button_090ekdosi').tooltipster(gks_submit_button_090ekdosi_steps);

  
  
  window.gks_se_steps_start=function (mystep) {
    steps=[];
    if (mystep=='email') {
      steps.push('aade');steps.push('print');steps.push('email');
    } else if (mystep=='print') {
      steps.push('aade');steps.push('print');
    } else if (mystep=='aade') {
      steps.push('aade');
    }
    if (steps.length==0) return;
    //console.log(steps);
    cvalue={};
    cvalue.id=from_php_id;
    cvalue.steps=steps;
    cvalue.done=[];
    gks_setCookie('whi_mov_steps',JSON.stringify(cvalue),300);
    $('#submit_button_090ekdosi').click();
    
  }
  
  
  
  cvalue=gks_getCookie('whi_mov_steps');
  if (cvalue!=null) {
    //console.log(cvalue);
    cvalue=JSON.parse(cvalue);
    if (cvalue.id>=0 && cvalue.id!=from_php_id) {
      gks_eraseCookie('whi_mov_steps');
    } else {
      if (cvalue.steps.includes('aade') && cvalue.done.includes('aade')==false) {
        //console.log('run aade');
        if ($('#submit_button_paroxos_send').length==1) {
          cvalue.done.push('aade');
          gks_setCookie('whi_mov_steps',JSON.stringify(cvalue),300);
          run_from_steps=true;run_from_step_run='aade';
          $('#submit_button_paroxos_send').click();
          $('#paroxos_mydata_live').click();
          $('#dialog_paroxos_go').click();
          
        } else if ($('#submit_button_aade_send').length==1) {
          cvalue.done.push('aade');run_from_step_run='aade';
          gks_setCookie('whi_mov_steps',JSON.stringify(cvalue),300);
          run_from_steps=true;
          $('#submit_button_aade_send').click();
          $('#aade_mydata_live').click();
          $('#dialog_aade_go').click();
        } else {
          gks_eraseCookie('whi_mov_steps');
        }
      } else if (cvalue.steps.includes('print') && cvalue.done.includes('print')==false) {
        //console.log('run print');
        if ($('#submit_button_print').length==1) {
          cvalue.done.push('print');run_from_step_run='print';
          gks_setCookie('whi_mov_steps',JSON.stringify(cvalue),300);
          run_from_steps=true;
          $('#submit_button_print').click();
  
          if ($('#gks_paroxos_send_pdf').length>0) {
            if ($('#gks_paroxos_send_pdf').is(':checked')==false) $('#gks_paroxos_send_pdf').click();
          }
          if ($('#gks_print_send_gks_erp_app').length>0) {
            if ($('#gks_print_send_gks_erp_app').is(':checked')==false) $('#gks_print_send_gks_erp_app').click();
          }       
          $('#dialog_print_ok').click();
        } else {
          gks_eraseCookie('whi_mov_steps');
        }
      } else if (cvalue.steps.includes('email') && cvalue.done.includes('email')==false) {
        //console.log('run email');run_from_step_run='email';
        if ($('#message_item_add').length==1) {
          cvalue.done.push('print');
          //gks_setCookie('whi_mov_steps',JSON.stringify(cvalue),300);
          run_from_steps=true;
          gks_message_item_add_click_outside();
          gks_eraseCookie('whi_mov_steps');
        } else {
          gks_eraseCookie('whi_mov_steps');
        }
      } else {
        gks_eraseCookie('whi_mov_steps');
      } 
    }
  }




        
  



  //1.0.9 start


  $('#load_country_id').each(function() {
    dbval=parseInt($(this).attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0">'+gks_lang('Χώρα')+'...</option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'">' + gks_country[i].country_name + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  });
      
  $('#load_country_id').change(function() {
    v=parseInt($(this).val());
    if (isNaN(v)) v=0;
    nomos_fill('load_nomos_id',v,0);
    calc_pliroteo();
  });
  
  $('#deli_country_id').each(function() {
    dbval=parseInt($(this).attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0">'+gks_lang('Χώρα')+'...</option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'">' + gks_country[i].country_name + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  });
      
  $('#deli_country_id').change(function() {
    v=parseInt($(this).val());
    if (isNaN(v)) v=0;
    nomos_fill('deli_nomos_id',v,0);
    calc_pliroteo();
  });  

  //1.0.9 end


  $('#copy_warehouse_from_addr').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if ($('#form_select_apostoli').val()=='-1') {
      $('#load_branch').val($('#dr_user_ma_branch_fromuser').val());
      $('#load_odos').val($('#dr_user_ma_odos').val());
      $('#load_arithmos').val($('#dr_user_ma_arithmos').val());
      $('#load_orofos').val($('#dr_user_ma_orofos').val());
      $('#load_perioxi').val($('#dr_user_ma_perioxi').val());
      $('#load_poli').val($('#dr_user_ma_poli').val());
      $('#load_tk').val($('#dr_user_ma_tk').val());
      $('#load_country_id').val($('#dr_user_ma_country_id').val());
      temp=$('#dr_user_ma_nomos_id').attr('data_nomos_id');
      if (typeof temp == 'undefined' || temp=='') temp=$('#dr_user_ma_nomos_id').val();
      nomos_fill('load_nomos_id',$('#dr_user_ma_country_id').val(),temp);
    } else {
      $('#load_branch').val($('#form_ea_branch').val());
      $('#load_odos').val($('#form_ea_odos').val());
      $('#load_arithmos').val($('#form_ea_arithmos').val());
      $('#load_orofos').val($('#form_ea_orofos').val());
      $('#load_perioxi').val($('#form_ea_perioxi').val());
      $('#load_poli').val($('#form_ea_poli').val());
      $('#load_tk').val($('#form_ea_tk').val());
      $('#load_country_id').val($('#form_ea_country_id').val());
      temp=$('#form_ea_nomos_id').attr('data_nomos_id');
      if (typeof temp == 'undefined' || temp=='') temp=$('#form_ea_nomos_id').val();
      nomos_fill('load_nomos_id',$('#form_ea_country_id').val(),temp);
    }
  });
  
  $('#copy_warehouse_to_addr').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if ($('#form_select_apostoli').val()=='-1') {
      $('#deli_branch').val($('#dr_user_ma_branch_fromuser').val());
      $('#deli_odos').val($('#dr_user_ma_odos').val());
      $('#deli_arithmos').val($('#dr_user_ma_arithmos').val());
      $('#deli_orofos').val($('#dr_user_ma_orofos').val());
      $('#deli_perioxi').val($('#dr_user_ma_perioxi').val());
      $('#deli_poli').val($('#dr_user_ma_poli').val());
      $('#deli_tk').val($('#dr_user_ma_tk').val());
      $('#deli_country_id').val($('#dr_user_ma_country_id').val());
      temp=$('#dr_user_ma_nomos_id').attr('data_nomos_id');
      if (typeof temp == 'undefined' || temp=='') temp=$('#dr_user_ma_nomos_id').val();
      nomos_fill('deli_nomos_id',$('#dr_user_ma_country_id').val(),temp);
    } else {
      $('#deli_branch').val($('#form_ea_branch').val());
      $('#deli_odos').val($('#form_ea_odos').val());
      $('#deli_arithmos').val($('#form_ea_arithmos').val());
      $('#deli_orofos').val($('#form_ea_orofos').val());
      $('#deli_perioxi').val($('#form_ea_perioxi').val());
      $('#deli_poli').val($('#form_ea_poli').val());
      $('#deli_tk').val($('#form_ea_tk').val());
      $('#deli_country_id').val($('#form_ea_country_id').val());
      temp=$('#form_ea_nomos_id').attr('data_nomos_id');
      if (typeof temp == 'undefined' || temp=='') temp=$('#form_ea_nomos_id').val();
      nomos_fill('deli_nomos_id',$('#form_ea_country_id').val(),temp);
    }
  });  

  gks_address_autocomplete('dr_user_ma_odos','dr_user_ma_arithmos','dr_user_ma_orofos','dr_user_ma_perioxi','dr_user_ma_poli','dr_user_ma_tk','dr_user_ma_nomos_id','dr_user_ma_country_id','','',true);
  gks_address_autocomplete('form_ea_odos','form_ea_arithmos','form_ea_orofos','form_ea_perioxi','form_ea_poli','form_ea_tk','form_ea_nomos_id','form_ea_country_id','','',true);
  gks_address_autocomplete('load_odos','load_arithmos','load_orofos','load_perioxi','load_poli','load_tk','load_nomos_id','load_country_id','','',true);
  gks_address_autocomplete('deli_odos','deli_arithmos','deli_orofos','deli_perioxi','deli_poli','deli_tk','deli_nomos_id','deli_country_id','','',true);

  $('.oeitem_aade_entitytype_id').each(function() {
    fill_oeitem_aade_entitytype_id($(this));
  });

  function fill_oeitem_aade_entitytype_id(elem) {
    dbval=parseInt(elem.attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    elem.append('<option value="0">'+gks_lang('Τύπος')+'...</option>');
    for(i=0;i<aade_entitytype.length;i++) {
      elem.append('<option value="' + aade_entitytype[i].id + '">' + aade_entitytype[i].descr + '</option>');
    }   
    elem.val(dbval); elem.removeAttr('data-dbval');
  }
  
  
  
  

  function gks_oeitem_entity_user_id_autocomplete(myelem) {
    myelem.autocomplete({
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
      autoFocus: true,
      delay: 300, //default
      select: function( event, ui ) {
        need_save=true;
        
        $(this).attr('data-id',ui.item.id);
        oeaa=parseInt($(this).attr('data-oeaa'));
        if (isNaN(oeaa)) oeaa=0;
        $('.autocomplete_entity_user_id[data-oeaa=' + oeaa + ']').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim()).show();
        
        gks_oeitem_entity_get_user_data(oeaa,'user');
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          $(this).val('');
          
          $(this).attr('data-id','0');
          oeaa=$(this).attr('data-oeaa');
          $('.autocomplete_entity_user_id[data-oeaa=' + oeaa + ']').attr('href', '#').hide();
          
          $('.oeitem_address_extra[data-oeaa=' + oeaa + '] option').remove();
          $('.oeitem_address_text[data-oeaa=' + oeaa + ']').html('');
          
        }
      },
            
    });
  }
  
  $('.oeitem_entity_user_id').each(function() {
    gks_oeitem_entity_user_id_autocomplete($(this));
  });  
  
  function gks_oeitem_entity_get_user_data(oeaa,call_from) {
    
    entity_user_id=parseInt($('.oeitem_entity_user_id[data-oeaa=' + oeaa + ']').attr('data-id'));
    if (isNaN(entity_user_id)) entity_user_id=0;
    if (entity_user_id<=0) return; 
    
    
    if (call_from=='user') {
      $('.oeitem_address_extra[data-oeaa=' + oeaa + '] option').remove();
      sub_id=-1;
    } else {
      sub_id=parseInt($('.oeitem_address_extra[data-oeaa=' + oeaa + ']').val());  
      if (isNaN(sub_id)) sub_id=-1;
    }
    //$('.oeitem_address_text[data-oeaa=' + oeaa + ']').html('<div><img src="img/wait.gif"></div>');
    
    datasend='id=' + entity_user_id + '&address_extra=' + sub_id + '&doc_table=gks_whi_mov';
    $.ajax({
			url: '/my/admin-get-user-other_entity_data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_oeaa:oeaa,
			gks_call_from:call_from,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.oeitem_address_text[data-oeaa=' + this.gks_oeaa + ']').html('error');
				} else {
					if (data.success == true) {
  					$('.oeitem_address_text[data-oeaa=' + this.gks_oeaa + ']').html(data.data.html);
  					if (this.gks_call_from=='user') {
    					elem=$('.oeitem_address_extra[data-oeaa=' + this.gks_oeaa + ']');
    					for(i=0; i< data.data.extra_address.length; i++) {
    					  elem.append('<option value="' + data.data.extra_address[i].id + '">' + data.data.extra_address[i].descr + '</option>');
    					}
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.oeitem_address_text[data-oeaa=' + this.gks_oeaa + ']').html('error');
					}
				}
			}
			
		}); 
		    
  }
  
  function oeitem_address_extra_change() {
    val=parseInt($(this).val());
    if (isNaN(val)) val=0;
    if ( val==0 || val<-1) return;
    oeaa=parseInt($(this).attr('data-oeaa'));
    if (isNaN(oeaa)) oeaa=0;
    
    gks_oeitem_entity_get_user_data(oeaa,'address_extra');
    
  }
  $('.oeitem_address_extra').change(oeitem_address_extra_change);

  $('#oeitem_entity_table').sortable({
    items: '.gks_other_entity_item',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-oeaa'});
      //console.log(mylist);
      oeitem_entity_table_sortable_after(mylist);
    }
  });

  function oeitem_entity_table_sortable_after(mylist) {
    //console.log(mylist);
    $('#oeitem_entity_table > .gks_other_entity_item').each(function() {
      oeaa=$(this).attr('data-oeaa');
      $(this).attr('data-oeaa_temp',oeaa);
    });
    $('#oeitem_entity_table > .gks_other_entity_item').each(function() {
      oeaa=$(this).attr('data-oeaa_temp');
      new_oeaa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==oeaa) {
          new_oeaa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_oeaa>=0) {
        new_oeaa++
        $(this).attr('data-oeaa',new_oeaa);
        $(this).find('*[data-oeaa=' + oeaa + ']').attr('data-oeaa',new_oeaa);
      }
      
    });      
    oeitem_entity_table_colors();
  } 

  function oeitem_entity_table_colors() {
    $('.gks_other_entity_item').each(function(index) {
      oeaa=$(this).attr('data-oeaa');
      if (index % 2) {
        $(this).removeClass('gks_other_entity_even').addClass('gks_other_entity_odd'); 
      } else {
        $(this).removeClass('gks_other_entity_odd').addClass('gks_other_entity_even');  
      }
    });
  }
  oeitem_entity_table_colors();

  function gks_other_entity_delete_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_oeaa=parseInt( $(this).attr('data-oeaa'));
    if (isNaN(delete_oeaa)) delete_oeaa=0;
    if (delete_oeaa<=0) return;
    $('.gks_other_entity_item[data-oeaa=' + delete_oeaa +']').remove(); 
    
    if ($('.gks_other_entity_item').length ==0) {
      other_entity_add(false,0);  
    }

    oeitem_entity_table_colors();
    gks_myscroll();
  }
  
  $('.gks_other_entity_delete').click(gks_other_entity_delete_click);
  
  

  function other_entity_add(fromloading,click_oeaa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_oeaa++;
    //console.log('fromloading' + fromloading);

    row_html=
          '<div class="form-group row gks_other_entity_item align-items-center" data-oeaa="' + last_oeaa + '" data-recid="0">' +
            '<div class="col-12 col-sm-6  col-md-4  col-lg-2">' +
              '<select data-dbval="0" class="oeitem_aade_entitytype_id form-control form-control-sm myneedsave" data-oeaa="' + last_oeaa + '">' +
              '</select>' +
            '</div>' +
            '<div class="col-12 col-sm-6  col-md-4  col-lg-2">' +
              '<input class="oeitem_entity_user_id form-control form-control-sm" ' +
              'data-oeaa="' + last_oeaa + '" ' +
              'value="" ' +
              'style="width:calc(100% - 22px);display:inline;" ' +
              'data-id="0" ' +
              'placeholder="'+gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες')+'" ' +
              '>' +
              ' <a data-oeaa="' + last_oeaa + '" class="autocomplete_entity_user_id" tabindex="-1" href="#" style="display:none"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή επαφής')+'"></i></a>' +
            '</div>' +
            '<div class="col-12 col-sm-6  col-md-4  col-lg-2">' +
              '<select class="oeitem_address_extra form-control form-control-sm myneedsave" data-oeaa="' + last_oeaa + '">' +
              '</select>' +
            '</div>' +
            '<div class="col-12 col-sm-10  col-md-10  col-lg-5">' +
              '<div class="oeitem_address_text" data-oeaa="' + last_oeaa + '">' +
              '</div>' +
            '</div>' +
            '<div class="col-12 col-sm-2  col-md-2  col-lg-1">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_other_entity_delete" data-oeaa="' + last_oeaa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_other_entity_add" data-oeaa="' + last_oeaa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';
    
    if (click_oeaa<=0) {
      $('#div_other_entity_footer').before(row_html);
    } else {
      $('.gks_other_entity_item[data-oeaa=' + click_oeaa + ']').after(row_html);
    }
    fill_oeitem_aade_entitytype_id($('.oeitem_aade_entitytype_id[data-oeaa=' + last_oeaa + ']'));
    gks_oeitem_entity_user_id_autocomplete($('.oeitem_entity_user_id[data-oeaa=' + last_oeaa + ']'));
    
    $('.oeitem_address_extra[data-oeaa=' + last_oeaa + ']').change(oeitem_address_extra_change);
    
    $('.gks_other_entity_add[data-oeaa=' + last_oeaa + ']').click(function() {gks_other_entity_add_click(false,$(this));});
    $('.gks_other_entity_delete[data-oeaa=' + last_oeaa + ']').click(gks_other_entity_delete_click); //.hide();
    if (fromloading==false) {
      $('.oeitem_aade_entitytype_id[data-oeaa=' + last_oeaa + ']').focus().select();
    }
    if (click_oeaa>0) {
      var mylist=[];
      $('.gks_other_entity_item').each(function() {
        mylist.push($(this).attr('data-oeaa'));
      });
      oeitem_entity_table_sortable_after(mylist);
    }
        
    oeitem_entity_table_colors();
    gks_myscroll();
  }

  function gks_other_entity_add_click(fromloading,elem) {
    oeaa=elem.attr('data-oeaa');
    other_entity_add(fromloading,oeaa);
  }
  $('.gks_other_entity_add').click(function() {gks_other_entity_add_click(false,$(this));});



  function gks_coiitem_mark_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        mydata={
          term: request.term,
        };
        $.ajax({
          url: 'admin-autocomplete-xxx-xxx.php',
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
      select: function( event, ui ) {
        need_save=true;
        
        //$(this).attr('data-id',ui.item.id);
        
        coiaa=parseInt($(this).attr('data-coiaa'));
        if (isNaN(coiaa)) coiaa=0;
        $(this).attr('data-coi_mark',ui.item.mark);
        $(this).attr('data-coi_acc_inv_id',ui.item.coi_acc_inv_id);
        $(this).attr('data-coi_acc_pay_id',ui.item.coi_acc_pay_id);
        $(this).attr('data-coi_whi_mov_id',ui.item.coi_whi_mov_id);
        gks_coiitem_get_data(coiaa);
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          //$(this).val('');
          
          coiaa=$(this).attr('data-coiaa');
          $(this).attr('data-coi_mark','');
          $(this).attr('data-coi_acc_inv_id','0');
          $(this).attr('data-coi_acc_pay_id','0');
          $(this).attr('data-coi_whi_mov_id','0');
          $('.coiitem_text[data-coiaa=' + coiaa + ']').html('');
          gks_coiitem_get_data(coiaa);
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
        limit_width=$(window).width()- $(event.target).offset().left - 20;
        if (mymaxui_text>limit_width) mymaxui_text=limit_width;
        $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
      },  
            
            
    });
  }
  
  $('.coiitem_mark').each(function() {
    gks_coiitem_mark_autocomplete($(this));
  });  
  
  function gks_coiitem_get_data(coiaa) {
    
    if (coiaa==0) return;
    
    elem=$('.coiitem_mark[data-coiaa=' + coiaa + ']');
    coi_mark=elem.val().trim();
    if (coi_mark.startsWith('acc_inv#') || coi_mark.startsWith('acc_pay#') || coi_mark.startsWith('whi_mov#')) coi_mark='';
    if (coi_mark=='') {
      coi_mark=elem.attr('data-coi_mark').trim();
    }
    coi_acc_inv_id=parseInt(elem.attr('data-coi_acc_inv_id'));
    coi_acc_pay_id=parseInt(elem.attr('data-coi_acc_pay_id'));
    coi_whi_mov_id=parseInt(elem.attr('data-coi_whi_mov_id'));
    
    if (isNaN(coi_acc_inv_id)) coi_acc_inv_id=0;
    if (isNaN(coi_acc_pay_id)) coi_acc_pay_id=0;
    if (isNaN(coi_whi_mov_id)) coi_whi_mov_id=0;
    
    $('.coiitem_text[data-coiaa=' + coiaa + ']').html('');
    
    //console.log(coi_mark,coi_acc_inv_id,coi_whi_mov_id);
    
    datasend='';
    datasend+='&coi_mark=' + encodeURIComponent($.base64.encode(coi_mark));
    datasend+='&coi_acc_inv_id=' + encodeURIComponent(coi_acc_inv_id);
    datasend+='&coi_acc_pay_id=' + encodeURIComponent(coi_acc_pay_id);
    datasend+='&coi_whi_mov_id=' + encodeURIComponent(coi_whi_mov_id);
    
    $.ajax({
			url: '/my/admin-get-xxx-xxx_data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_coiaa:coiaa,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.coiitem_text[data-coiaa=' + this.gks_coiaa + ']').html('error');
				} else {
					if (data.success == true) {
  					$('.coiitem_text[data-coiaa=' + this.gks_coiaa + ']').html(data.data.html);
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.coiitem_text[data-coiaa=' + this.gks_coiaa + ']').html('error');
					}
				}
			}
			
		}); 
		    
  }
 


  $('#coiitem_correlated_invoices_table').sortable({
    items: '.gks_correlated_invoices_item',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-coiaa'});
      //console.log(mylist);
      coiitem_sortable_after(mylist);
    }
  });

  function coiitem_sortable_after(mylist) {

    $('#coiitem_correlated_invoices_table > .gks_correlated_invoices_item').each(function() {
      coiaa=$(this).attr('data-coiaa');
      $(this).attr('data-coiaa_temp',coiaa);
    });
    $('#coiitem_correlated_invoices_table > .gks_correlated_invoices_item').each(function() {
      coiaa=$(this).attr('data-coiaa_temp');
      new_coiaa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==coiaa) {
          new_coiaa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_coiaa>=0) {
        new_coiaa++
        $(this).attr('data-coiaa',new_coiaa);
        $(this).find('*[data-coiaa=' + coiaa + ']').attr('data-coiaa',new_coiaa);
      }
      
    });      
    coiitem_table_colors();
  } 

  function coiitem_table_colors() {
    $('.gks_correlated_invoices_item').each(function(index) {
      coiaa=$(this).attr('data-coiaa');
      if (index % 2) {
        $(this).removeClass('gks_correlated_invoices_even').addClass('gks_correlated_invoices_odd'); 
      } else {
        $(this).removeClass('gks_correlated_invoices_odd').addClass('gks_correlated_invoices_even');  
      }
    });
  }
  coiitem_table_colors();

  function gks_correlated_invoices_delete_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_coiaa=parseInt( $(this).attr('data-coiaa'));
    if (isNaN(delete_coiaa)) delete_coiaa=0;
    if (delete_coiaa<=0) return;
    $('.gks_correlated_invoices_item[data-coiaa=' + delete_coiaa +']').remove(); 
    
    if ($('.gks_correlated_invoices_item').length ==0) {
      correlated_invoices_add(false,0);  
    }

    coiitem_table_colors();
    gks_myscroll();
  }
  
  $('.gks_correlated_invoices_delete').click(gks_correlated_invoices_delete_click);
  
  

  function correlated_invoices_add(fromloading,click_coiaa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_coiaa++;
    //console.log('fromloading' + fromloading);

    row_html=
          '<div class="form-group row gks_correlated_invoices_item align-items-center" data-coiaa="' + last_coiaa + '" data-recid="0">' +
            '<div class="col-12 col-sm-4  col-md-3 col-lg-2">' +
              '<input class="coiitem_mark form-control form-control-sm" ' +
              'value="" ' +
              'placeholder="'+gks_lang('ΜΑΡΚ ή #αριθμό ή @ημερομηνία')+' ..." ' +
              'data-coiaa="' + last_coiaa + '" ' +
              'data-coi_mark="" ' +
              'data-coi_acc_inv_id="0" ' +
              'data-coi_acc_pay_id="0" ' +
              'data-coi_whi_mov_id="0" ' +
              '>' +
            '</div>' +
            '<div class="col-12 col-sm-6  col-md-7  col-lg-9">' +
              '<div class="coiitem_text" data-coiaa="' + last_coiaa + '">' +
              '</div>' +
            '</div>' +
            '<div class="col-12 col-sm-2  col-md-2  col-lg-1">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_correlated_invoices_delete" data-coiaa="' + last_coiaa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_correlated_invoices_add" data-coiaa="' + last_coiaa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';
          
    
    
    if (click_coiaa<=0) {
      $('#div_correlated_invoices_footer').before(row_html);
    } else {
      $('.gks_correlated_invoices_item[data-coiaa=' + click_coiaa + ']').after(row_html);
    }
    gks_coiitem_mark_autocomplete($('.coiitem_mark[data-coiaa=' + last_coiaa + ']'));
    
    
    $('.gks_correlated_invoices_add[data-coiaa=' + last_coiaa + ']').click(function() {gks_correlated_invoices_add_click(false,$(this));});
    $('.gks_correlated_invoices_delete[data-coiaa=' + last_coiaa + ']').click(gks_correlated_invoices_delete_click);
    if (fromloading==false) {
      $('.coiitem_mark[data-coiaa=' + last_coiaa + ']').focus().select();
    }
    if (click_coiaa>0) {
      var mylist=[];
      $('.gks_correlated_invoices_item').each(function() {
        mylist.push($(this).attr('data-coiaa'));
      });
      coiitem_sortable_after(mylist);
    }
        
    coiitem_table_colors();
    gks_myscroll();
  }

  function gks_correlated_invoices_add_click(fromloading,elem) {
    coiaa=elem.attr('data-coiaa');
    correlated_invoices_add(fromloading,coiaa);
  }
  $('.gks_correlated_invoices_add').click(function() {gks_correlated_invoices_add_click(false,$(this));});


  function gks_mcmitem_mark_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        mydata={
          term: request.term,
        };
        $.ajax({
          url: 'admin-autocomplete-xxx-xxx.php',
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
      select: function( event, ui ) {
        need_save=true;
        
        //$(this).attr('data-id',ui.item.id);
        
        mcmaa=parseInt($(this).attr('data-mcmaa'));
        if (isNaN(mcmaa)) mcmaa=0;
        $(this).attr('data-mcm_mark',ui.item.mark);
        $(this).attr('data-mcm_acc_inv_id',ui.item.mcm_acc_inv_id);
        $(this).attr('data-mcm_acc_pay_id',ui.item.mcm_acc_pay_id);
        $(this).attr('data-mcm_whi_mov_id',ui.item.mcm_whi_mov_id);
        gks_mcmitem_get_data(mcmaa);
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          //$(this).val('');
          
          mcmaa=$(this).attr('data-mcmaa');
          $(this).attr('data-mcm_mark','');
          $(this).attr('data-mcm_acc_inv_id','0');
          $(this).attr('data-mcm_acc_pay_id','0');
          $(this).attr('data-mcm_whi_mov_id','0');
          $('.mcmitem_text[data-mcmaa=' + mcmaa + ']').html('');
          gks_mcmitem_get_data(mcmaa);
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
        limit_width=$(window).width()- $(event.target).offset().left - 20;
        if (mymaxui_text>limit_width) mymaxui_text=limit_width;
        $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
      },  
            
            
    });
  }
  
  $('.mcmitem_mark').each(function() {
    gks_mcmitem_mark_autocomplete($(this));
  });  
  
  function gks_mcmitem_get_data(mcmaa) {
    
    if (mcmaa==0) return;
    
    elem=$('.mcmitem_mark[data-mcmaa=' + mcmaa + ']');
    mcm_mark=elem.val().trim();
    if (mcm_mark.startsWith('acc_inv#') || mcm_mark.startsWith('acc_pay#') || mcm_mark.startsWith('whi_mov#')) mcm_mark='';
    if (mcm_mark=='') {
      mcm_mark=elem.attr('data-mcm_mark').trim();
    }
    mcm_acc_inv_id=parseInt(elem.attr('data-mcm_acc_inv_id'));
    mcm_acc_pay_id=parseInt(elem.attr('data-mcm_acc_pay_id'));
    mcm_whi_mov_id=parseInt(elem.attr('data-mcm_whi_mov_id'));
    
    if (isNaN(mcm_acc_inv_id)) mcm_acc_inv_id=0;
    if (isNaN(mcm_acc_pay_id)) mcm_acc_pay_id=0;
    if (isNaN(mcm_whi_mov_id)) mcm_whi_mov_id=0;
    
    $('.mcmitem_text[data-mcmaa=' + mcmaa + ']').html('');
    
    //console.log(mcm_mark,mcm_acc_inv_id,mcm_whi_mov_id);
    
    datasend='';
    datasend+='&mcm_mark=' + encodeURIComponent($.base64.encode(mcm_mark));
    datasend+='&mcm_acc_inv_id=' + encodeURIComponent(mcm_acc_inv_id);
    datasend+='&mcm_acc_pay_id=' + encodeURIComponent(mcm_acc_pay_id);
    datasend+='&mcm_whi_mov_id=' + encodeURIComponent(mcm_whi_mov_id);
    
    $.ajax({
			url: '/my/admin-get-xxx-xxx_data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_mcmaa:mcmaa,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html('error');
				} else {
					if (data.success == true) {
  					$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html(data.data.html);
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html('error');
					}
				}
			}
			
		}); 
		    
  }
 


  $('#mcmitem_multiple_connected_marks_table').sortable({
    items: '.gks_multiple_connected_marks_item',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-mcmaa'});
      //console.log(mylist);
      mcmitem_sortable_after(mylist);
    }
  });

  function mcmitem_sortable_after(mylist) {

    $('#mcmitem_multiple_connected_marks_table > .gks_multiple_connected_marks_item').each(function() {
      mcmaa=$(this).attr('data-mcmaa');
      $(this).attr('data-mcmaa_temp',mcmaa);
    });
    $('#mcmitem_multiple_connected_marks_table > .gks_multiple_connected_marks_item').each(function() {
      mcmaa=$(this).attr('data-mcmaa_temp');
      new_mcmaa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==mcmaa) {
          new_mcmaa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_mcmaa>=0) {
        new_mcmaa++
        $(this).attr('data-mcmaa',new_mcmaa);
        $(this).find('*[data-mcmaa=' + mcmaa + ']').attr('data-mcmaa',new_mcmaa);
      }
      
    });      
    mcmitem_table_colors();
  } 

  function mcmitem_table_colors() {
    $('.gks_multiple_connected_marks_item').each(function(index) {
      mcmaa=$(this).attr('data-mcmaa');
      if (index % 2) {
        $(this).removeClass('gks_multiple_connected_marks_even').addClass('gks_multiple_connected_marks_odd'); 
      } else {
        $(this).removeClass('gks_multiple_connected_marks_odd').addClass('gks_multiple_connected_marks_even');  
      }
    });
  }
  mcmitem_table_colors();

  function gks_multiple_connected_marks_delete_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_mcmaa=parseInt( $(this).attr('data-mcmaa'));
    if (isNaN(delete_mcmaa)) delete_mcmaa=0;
    if (delete_mcmaa<=0) return;
    $('.gks_multiple_connected_marks_item[data-mcmaa=' + delete_mcmaa +']').remove(); 
    
    if ($('.gks_multiple_connected_marks_item').length ==0) {
      multiple_connected_marks_add(false,0);  
    }

    mcmitem_table_colors();
    gks_myscroll();
  }
  
  $('.gks_multiple_connected_marks_delete').click(gks_multiple_connected_marks_delete_click);
  
  

  function multiple_connected_marks_add(fromloading,click_mcmaa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_mcmaa++;
    //console.log('fromloading' + fromloading);

    row_html=
          '<div class="form-group row gks_multiple_connected_marks_item align-items-center" data-mcmaa="' + last_mcmaa + '" data-recid="0">' +
            '<div class="col-12 col-sm-4  col-md-3 col-lg-2">' +
              '<input class="mcmitem_mark form-control form-control-sm" ' +
              'value="" ' +
              'placeholder="'+gks_lang('ΜΑΡΚ ή #αριθμό ή @ημερομηνία')+' ..." ' +
              'data-mcmaa="' + last_mcmaa + '" ' +
              'data-mcm_mark="" ' +
              'data-mcm_acc_inv_id="0" ' +
              'data-mcm_acc_pay_id="0" ' +
              'data-mcm_whi_mov_id="0" ' +
              '>' +
            '</div>' +
            '<div class="col-12 col-sm-6  col-md-7  col-lg-9">' +
              '<div class="mcmitem_text" data-mcmaa="' + last_mcmaa + '">' +
              '</div>' +
            '</div>' +
            '<div class="col-12 col-sm-2  col-md-2  col-lg-1">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_multiple_connected_marks_delete" data-mcmaa="' + last_mcmaa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_multiple_connected_marks_add" data-mcmaa="' + last_mcmaa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';
          
    
    
    if (click_mcmaa<=0) {
      $('#div_multiple_connected_marks_footer').before(row_html);
    } else {
      $('.gks_multiple_connected_marks_item[data-mcmaa=' + click_mcmaa + ']').after(row_html);
    }
    gks_mcmitem_mark_autocomplete($('.mcmitem_mark[data-mcmaa=' + last_mcmaa + ']'));
    
    
    $('.gks_multiple_connected_marks_add[data-mcmaa=' + last_mcmaa + ']').click(function() {gks_multiple_connected_marks_add_click(false,$(this));});
    $('.gks_multiple_connected_marks_delete[data-mcmaa=' + last_mcmaa + ']').click(gks_multiple_connected_marks_delete_click);
    if (fromloading==false) {
      $('.mcmitem_mark[data-mcmaa=' + last_mcmaa + ']').focus().select();
    }
    if (click_mcmaa>0) {
      var mylist=[];
      $('.gks_multiple_connected_marks_item').each(function() {
        mylist.push($(this).attr('data-mcmaa'));
      });
      mcmitem_sortable_after(mylist);
    }
        
    mcmitem_table_colors();
    gks_myscroll();
  }

  function gks_multiple_connected_marks_add_click(fromloading,elem) {
    mcmaa=elem.attr('data-mcmaa');
    multiple_connected_marks_add(fromloading,mcmaa);
  }
  $('.gks_multiple_connected_marks_add').click(function() {gks_multiple_connected_marks_add_click(false,$(this));});


  $('#pdeitem_packings_declarations_table').sortable({
    items: '.gks_packings_declarations_item',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-pdeaa'});
      //console.log(mylist);
      pdeitem_sortable_after(mylist);
    }
  });

  function pdeitem_sortable_after(mylist) {

    $('#pdeitem_packings_declarations_table > .gks_packings_declarations_item').each(function() {
      pdeaa=$(this).attr('data-pdeaa');
      $(this).attr('data-pdeaa_temp',pdeaa);
    });
    $('#pdeitem_packings_declarations_table > .gks_packings_declarations_item').each(function() {
      pdeaa=$(this).attr('data-pdeaa_temp');
      new_pdeaa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==pdeaa) {
          new_pdeaa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_pdeaa>=0) {
        new_pdeaa++
        $(this).attr('data-pdeaa',new_pdeaa);
        $(this).find('*[data-pdeaa=' + pdeaa + ']').attr('data-pdeaa',new_pdeaa);
      }
      
    });      
    pdeitem_table_colors();
  } 
  function pdeitem_table_colors() {
    $('.gks_packings_declarations_item').each(function(index) {
      pdeaa=$(this).attr('data-pdeaa');
      if (index % 2) {
        $(this).removeClass('gks_packings_declarations_even').addClass('gks_packings_declarations_odd'); 
      } else {
        $(this).removeClass('gks_packings_declarations_odd').addClass('gks_packings_declarations_even');  
      }
    });
  }
  pdeitem_table_colors();

  function gks_packings_declarations_delete_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_pdeaa=parseInt( $(this).attr('data-pdeaa'));
    if (isNaN(delete_pdeaa)) delete_pdeaa=0;
    if (delete_pdeaa<=0) return;
    $('.gks_packings_declarations_item[data-pdeaa=' + delete_pdeaa +']').remove(); 
    
    if ($('.gks_packings_declarations_item').length ==0) {
      packings_declarations_add(false,0);  
    }

    pdeitem_table_colors();
    gks_myscroll();
  }
  $('.gks_packings_declarations_delete').click(gks_packings_declarations_delete_click);

  function packings_declarations_add(fromloading,click_pdeaa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_pdeaa++;
    //console.log('fromloading' + fromloading);

    row_html=
          '<div class="form-group row gks_packings_declarations_item align-items-center" data-pdeaa="' + last_pdeaa + '" data-recid="0">' +
            '<div class="col-4">' +
              '<select data-pdeaa="' + last_pdeaa + '" '+
                'class="pde_packaging_type_id form-control form-control-sm myneedsave">' +
                '<option value="0"></option>'; 
        for (i=0;i<from_php_packagingTypes.length;i++) {
          row_html+='<option value="' + from_php_packagingTypes[i].id + '">' + from_php_packagingTypes[i].descr + '</option>'; 
        }              
    row_html+=
              '</select>' +
            '</div>' +
            '<div class="col-3">' +
              '<input data-pdeaa="' + last_pdeaa + '" type="number"  min="0" '+
                'class="pde_packaging_quantity form-control form-control-sm myneedsave" ' +
                'value="1" ' +
                '/>' +
            '</div>'+
            '<div class="col-3">' +
              '<input data-pdeaa="' + last_pdeaa + '" type="text" '+
                'class="pde_packaging_type_6_descr form-control form-control-sm myneedsave" ' +
                'value="" ' +
                'style="display:none;" '+
                '/>' +
            '</div>' +
            '<div class="col-2">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_packings_declarations_delete" data-pdeaa="' + last_pdeaa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_packings_declarations_add" data-pdeaa="' + last_pdeaa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';
          
    
    
    if (click_pdeaa<=0) {
      $('#div_packings_declarations_footer').before(row_html);
    } else {
      $('.gks_packings_declarations_item[data-pdeaa=' + click_pdeaa + ']').after(row_html);
    }

    $('.pde_packaging_type_id[data-pdeaa=' + last_pdeaa + ']').change(pde_packaging_type_id_change);
    $('.gks_packings_declarations_add[data-pdeaa=' + last_pdeaa + ']').click(function() {gks_packings_declarations_add_click(false,$(this));});
    $('.gks_packings_declarations_delete[data-pdeaa=' + last_pdeaa + ']').click(gks_packings_declarations_delete_click);

    if (click_pdeaa>0) {
      var mylist=[];
      $('.gks_packings_declarations_item').each(function() {
        mylist.push($(this).attr('data-pdeaa'));
      });
      pdeitem_sortable_after(mylist);
    }
        
    pdeitem_table_colors();
    gks_myscroll();
  }

  function gks_packings_declarations_add_click(fromloading,elem) {
    pdeaa=elem.attr('data-pdeaa');
    packings_declarations_add(fromloading,pdeaa);
  }
  $('.gks_packings_declarations_add').click(function() {gks_packings_declarations_add_click(false,$(this));});

  function pde_packaging_type_id_change() {
    vvv=parseInt($(this).attr('data-pdeaa'));
    if (isNaN(vvv)) vvv=0;if (vvv<=0) return;
    kkk=parseInt($(this).val());
    if (isNaN(kkk)) kkk=0; if (kkk<=0) return; 
    if (kkk==6) {
      $('.pde_packaging_type_6_descr[data-pdeaa=' + vvv + ']').show();   
    } else {
      $('.pde_packaging_type_6_descr[data-pdeaa=' + vvv + ']').hide();
    }    
  }
  $('.pde_packaging_type_id').change(pde_packaging_type_id_change);
  



  $('#aade_skopos_diakinisis_id').change(function() {
    val_sk19=parseInt($(this).val());
    if (isNaN(val_sk19)) val_sk19=0;
    if (val_sk19==22) {
      $('#aade_skopos_19_descr').show();
    } else {
      $('#aade_skopos_19_descr').hide();
    }
  });


  ///////////////////////////////////////////////////////// pre end




  
  
  //variable multi entry
  var dialog_add_product_def_row='';
  var dialog_add_product_def_col='';
  cvalue=gks_getCookie('gks_dialog_add_product');
  if (cvalue!=null) {
    cvalue=JSON.parse(cvalue);
    if (cvalue.dialog_add_product_def_row !== undefined || cvalue.dialog_add_product_def_row != null) dialog_add_product_def_row=cvalue.dialog_add_product_def_row;
    if (cvalue.dialog_add_product_def_col !== undefined || cvalue.dialog_add_product_def_col != null) dialog_add_product_def_col=cvalue.dialog_add_product_def_col;
  }
  
  $('#dialog_add_product_variables_button').click(function() {
    $('body').css('overflow','hidden'); 
    $('#dialog_add_product_variables_table').html('');
    $('#dialog_add_product_variables_product_id').val('').attr('data-id','0');
    $('#autocomplete_dialog_add_product_variables_product_id').hide();
    $('#dialog_add_product_row').val('0');
    $('#dialog_add_product_col').val('0');
    $('#dialog_add_product_row option[value!=\'0\']').remove();  
    $('#dialog_add_product_col option[value!=\'0\']').remove();
    $('#row_gks_multi_copies_enable_others').hide();
    $('#dialog_add_product_others').html('');    
    $('#dialog_add_product_variables_save').hide();
      
    $('#dialog_add_product_variables').show();
    $('#dialog_add_product_variables_product_id').focus();
  });
  
  $('#dialog_add_product_variables_close').click(function() {
    $('body').css('overflow',''); 
    $('#dialog_add_product_variables').hide();
  });
  $('#dialog_add_product_variables_cancel').click(function() {
    $('body').css('overflow',''); 
    $('#dialog_add_product_variables').hide();
  });  
  
  var fdavp_mustrun=0;var fdavp_hasrun=0;var fdavp_array=[];
  $('#dialog_add_product_variables_save').click(function() {
    $('body').css('overflow',''); 
    $('#dialog_add_product_variables').hide();
    $('body').css('cursor', 'progress');
    $('#calc_hourglass').show();
    setTimeout(function() {
      var tabledatavars=[];
      $('.dialog_add_product_variables_input').each(function() {
        product_id=parseInt($(this).attr('data-product_id'));
        if (isNaN(product_id)) product_id=0;
        posotita=parseFloat($(this).val()); 
        if (isNaN(posotita)) posotita=0;
        tabledatavars.push({'id':product_id,'pst':posotita});
      });
      //console.log(tabledatavars);
      
      elemdiv_for_remove=null;
      if ($('.gks_eidos_2divs').length==1) {
        if ($('.gks_eidos_2divs .gks_product_zoom').attr('data-id_product')=='0' &&
            $('.gks_eidos_2divs .gks_descr').val()=='' &&
            $('.gks_eidos_2divs  .gks_quantity').val()=='') {
          elemdiv_for_remove=$('.gks_eidos_2divs');
        }
      }
      
      fdavp_array=[];
      for(var itdv=0; itdv<tabledatavars.length; itdv++) {
        if (tabledatavars[itdv].pst>0) {
          elem=$('.gks_product_zoom[data-id_product=' + tabledatavars[itdv].id + ']:last');
          if (elem.length==1) {
            elemdiv=elem.parent().parent().parent().parent();
            elemdiv.find('.gks_quantity').val(tabledatavars[itdv].pst);
            aa=elemdiv.attr('data-aa');
            //fields_change[aa]='gks_quantity';
          } else if (elem.length==0) {
            //$('.gks_add_eidos:last').click();
            eidoi_add(false,0);
            elemdiv=$('.gks_eidos_2divs:last');
            
            elemdiv.find('.gks_quantity').val(tabledatavars[itdv].pst);
            aa=elemdiv.attr('data-aa');
            //elemdiv.find('.gks_code').val('ssss');
            
            elemdiv.find('.gks_product_zoom[data-aa=' + aa + ']').attr('data-id_product',tabledatavars[itdv].id);
            fdavp_array.push(aa);
            //fields_change[aa]='gks_quantity';
            //console.log('add product',tabledatavars[itdv]);
            $('#dialog_add_product_variables_button').focus();
          }
        }
      }
       
      for(var itdv=0; itdv<tabledatavars.length; itdv++) {
        if (tabledatavars[itdv].pst<=0) {
          elem=$('.gks_product_zoom[data-id_product=' + tabledatavars[itdv].id + ']:last');
          if (elem.length>0) {
            elem.parent().parent().parent().parent().remove();
          }
        } 
      }
      
      
            
      if (fdavp_array.length>0) {
        if (elemdiv_for_remove!=null) elemdiv_for_remove.remove();
        get_product_data(fdavp_array[0],1,true);
      } else {
        if ($('.gks_eidos_2divs').length==0) eidoi_add(false,0);
        $('#calc_hourglass').hide();
        $('body').css('cursor', '');
        calc_pliroteo();
      }
      $('#gks_products_count').html($('.gks_quantity').length);
      eidi_table_colors();
      gks_myscroll();
    }, 200);
  });
  
    
  var dialog_add_product_data=null;
  $('#dialog_add_product_variables_product_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:2,
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
    autoFocus: true,
    select: function( event, ui ) {
      $("#dialog_add_product_variables_product_id").attr('data-id',ui.item.id);
      $('#autocomplete_dialog_add_product_variables_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_dialog_add_product_variables_product_id').show();
      $('#dialog_add_product_variables_table').html('');
      datasend='cmd=get&id=' + ui.item.id;
      $.ajax({
        url: 'admin-get-product-variables.php',
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
              dialog_add_product_data=data.data;
              $('#dialog_add_product_row option[value!=\'0\']').remove();
              $('#dialog_add_product_col option[value!=\'0\']').remove();
              $('#row_gks_multi_copies_enable_others').hide();
              $('#dialog_add_product_others').html('');
              
              dialog_add_product_def_row_val=0;
              dialog_add_product_def_col_val=0;
              cc=0;
              for (var k in data.data.idiotites){
                cc++;
                item=data.data.idiotites[k];
                if (dialog_add_product_def_row==item.name) dialog_add_product_def_row_val=item.id_eshop_products_idiotites;
                if (dialog_add_product_def_col==item.name) dialog_add_product_def_col_val=item.id_eshop_products_idiotites;
                  
                optionitem='<option data-idiotita_id="' + item.idiotita_id + '" value="' + item.id_eshop_products_idiotites + '">' + item.name + '</option>';
                $('#dialog_add_product_row').append(optionitem);
                $('#dialog_add_product_col').append(optionitem);
              }
              if (dialog_add_product_def_row_val>0) $('#dialog_add_product_row').val(dialog_add_product_def_row_val);
              if (dialog_add_product_def_col_val>0) $('#dialog_add_product_col').val(dialog_add_product_def_col_val);
              
              if (cc>2) {
                html_select='<div class="dialog_add_product_other_div">';
                  html_select+='<select data-ii="[[ii]]" class="dialog_add_product_other form-control form-control-sm">';
                  html_select+='<option value="0">'+gks_lang('Κενό')+'</option>';
                  for (var k in data.data.idiotites){
                    item=data.data.idiotites[k];
                    html_select+='<option data-idiotita_id="' + item.idiotita_id + '" value="' + item.id_eshop_products_idiotites + '">' + item.name + '</option>';
                  }
                  html_select+='</select>';
                  html_select+=':';
                  html_select+='<select data-ii="[[ii]]" class="dialog_add_product_other_terms form-control form-control-sm">';
                  html_select+='<option value="0">--</option>';
                  html_select+='</select>';
                html_select+='</div>';
                
                for(ii=3;ii<=cc;ii++) {
                  html_append=html_select.replaceAll('[[ii]]',(ii-3));
                  $('#dialog_add_product_others').append(html_append);
                  if (ii<cc) $('#dialog_add_product_others').append('<div class="dialog_add_product_split"><i class="fas fa-circle"></i></div>');
                }
                $('.dialog_add_product_other').change(dialog_add_product_other_change);
                $('.dialog_add_product_other_terms').change(dialog_add_product_other_term_change);
                $('#row_gks_multi_copies_enable_others').show();
              }
              
              
              dialog_add_product_drawtable('row');
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
      }); 
      
    },
    change: function (event, ui) {

      if(!ui.item){
        $('#dialog_add_product_variables_table').html('');
        $('#dialog_add_product_variables_product_id').val('').attr('data-id','0');
        $('#autocomplete_dialog_add_product_variables_product_id').hide();
        $('#dialog_add_product_row').val('0');
        $('#dialog_add_product_col').val('0');
        $('#dialog_add_product_row option[value!=\'0\']').remove();  
        $('#dialog_add_product_col option[value!=\'0\']').remove();
        $('#row_gks_multi_copies_enable_others').hide();
        $('#dialog_add_product_others').html('');         
        $('#dialog_add_product_variables_save').hide();
      }
    }
    
  });  
  function dialog_add_product_drawtable(eventfrom,ii=0) {
    //console.log('dialog_add_product_drawtable',eventfrom);
    $('#dialog_add_product_variables_table').html('');
    ppitems=0;
    idepi_row=parseInt($('#dialog_add_product_row').val());
    if (isNaN(idepi_row)) idepi_row=0;
    idepi_col=parseInt($('#dialog_add_product_col').val());
    if (isNaN(idepi_col)) idepi_col=0;
    
    idepi_other=[];
    $('.dialog_add_product_other').each(function() {
      ttt=parseInt($(this).val());    
      if (isNaN(ttt)) ttt=0;
      idepi_other.push(ttt);
    });
    //console.log('idepi_other',idepi_other);
    

    
    idepi_other_needs_update=[];
    if (eventfrom=='other') idepi_other_needs_update.push(ii);
    
    //set kena
    val_check=0;
    if (eventfrom=='row') val_check=idepi_row;
    if (eventfrom=='col') val_check=idepi_col;
    if (eventfrom=='other') val_check=idepi_other[ii];
    
    if (val_check!=0) {
      if (eventfrom=='row') { 
        if (val_check==idepi_col) {$('#dialog_add_product_col').val('0'); idepi_col=0;}
        for(ik=0; ik<idepi_other.length;ik++) {
          if (val_check==idepi_other[ik]) {$('.dialog_add_product_other[data-ii='+ik+']').val('0');idepi_other[ik]=0;idepi_other_needs_update.push(ik);}
        }
      }
      if (eventfrom=='col') { 
        if (val_check==idepi_row) {$('#dialog_add_product_row').val('0'); idepi_row=0;}
        for(ik=0; ik<idepi_other.length;ik++) {
          if (val_check==idepi_other[ik]) {$('.dialog_add_product_other[data-ii='+ik+']').val('0');idepi_other[ik]=0;idepi_other_needs_update.push(ik);}
        }
      }
      if (eventfrom=='other') { 
        if (val_check==idepi_col) {$('#dialog_add_product_col').val('0'); idepi_col=0;}
        if (val_check==idepi_row) {$('#dialog_add_product_row').val('0'); idepi_row=0;}
        for(ik=0; ik<idepi_other.length;ik++) {
          if (ik!=ii) {
            if (val_check==idepi_other[ik]) {$('.dialog_add_product_other[data-ii='+ik+']').val('0');idepi_other[ik]=0;idepi_other_needs_update.push(ik);}
          }
        }
      }
    }
    //console.log(idepi_row,idepi_col,idepi_other);
    
    

    
    
    //fill kena
    array_ids=[];
    for (var k in dialog_add_product_data.idiotites) {
      array_ids.push(dialog_add_product_data.idiotites[k].id_eshop_products_idiotites);
    }            
    //console.log('array_ids',array_ids);
    
    if (idepi_row==0 && eventfrom!='row') {
      for(kk=0; kk<array_ids.length; kk++) {
        has_found=false;
        if (array_ids[kk]==idepi_col) has_found=true;
        else {
          for(ik=0; ik<idepi_other.length;ik++) {
            if (array_ids[kk]==idepi_other[ik]) {has_found=true; break;}
          }
        }
        if (has_found==false) {
          idepi_row=array_ids[kk]; $('#dialog_add_product_row').val(idepi_row);break;
        }
      }
    }
    if (idepi_col==0 && eventfrom!='col') {
      for(kk=0; kk<array_ids.length; kk++) {
        has_found=false;
        if (array_ids[kk]==idepi_row) has_found=true;
        else {
          for(ik=0; ik<idepi_other.length;ik++) {
            if (array_ids[kk]==idepi_other[ik]) {has_found=true; break;}
          }
        }
        if (has_found==false) {
          idepi_col=array_ids[kk]; $('#dialog_add_product_col').val(idepi_col);break;
        }
      }
    }    

    for(ikm=0; ikm<idepi_other.length;ikm++) {
      if (idepi_other[ikm]==0 && (ii!=ikm || eventfrom!='other')) {
        for(kk=0; kk<array_ids.length; kk++) {
          has_found=false;
          if (array_ids[kk]==idepi_row) has_found=true;
          else if (array_ids[kk]==idepi_col) has_found=true;
          else {
            for(ik=0; ik<idepi_other.length;ik++) {
              if (array_ids[kk]==idepi_other[ik]) {has_found=true; break;}
            }
          }
          if (has_found==false) {
            idepi_other[ikm]=array_ids[kk]; $('.dialog_add_product_other[data-ii='+ikm+']').val(idepi_other[ikm]);idepi_other_needs_update.push(ikm);break;
          }
        }
      }    
    }
    
    if (idepi_row==0 && idepi_col==0) {
      $('#dialog_add_product_variables_save').hide();
      return;
    }   
    
    
    //fill terms for other
    if (eventfrom!='other_term') {
      for(ik=0; ik<idepi_other.length;ik++) {
        if (idepi_other_needs_update.includes(ik)) {
          $('.dialog_add_product_other_terms[data-ii='+ik+'] option[value!=\'0\']').remove(); 
          if (idepi_other[ik]!=0) {
            for (var t in dialog_add_product_data.idiotites[idepi_other[ik]].terms) {
              term=dialog_add_product_data.idiotites[idepi_other[ik]].terms[t];
              optionitem='<option value="' + term.id_product_idiotita_term + '">' + term.idiotita_term_name + '</option>';
              $('.dialog_add_product_other_terms[data-ii='+ik+']').append(optionitem)
            }
          }
        }
      }
    }
    //console.log('a',idepi_row,idepi_col,idepi_other);



    filter_other_terms=[];
    $('.dialog_add_product_other_terms').each(function() {
      giev=parseInt($(this).val());if (isNaN(giev)) giev=-1;
      if (giev>0) filter_other_terms.push(giev);
    });
    //console.log('filter_other_terms',filter_other_terms);
         
    myhtml='<table id="dialog_add_product_variables_tabledata" class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">';
    myhtml+='<tbody>';
    

    
    if (idepi_row!=0 && idepi_col==0) {
      for (var t in dialog_add_product_data.idiotites[idepi_row].terms) {
        term=dialog_add_product_data.idiotites[idepi_row].terms[t];
        tid=term.id_product_idiotita_term;
        myhtml+='<tr>';
        myhtml+='<th class="table-dark" scope="col" width="0%" nowrap>' + term.idiotita_term_name + '</th>';
        
        pitem_one=null;
        products_found=0;
        for (var p in dialog_add_product_data.products) {
          pitem=dialog_add_product_data.products[p];
          cc1=0;cc2=0;
          for(cc3=0;cc3<filter_other_terms.length;cc3++) {
            cc1++;
            if (pitem.terms.includes(filter_other_terms[cc3])) cc2++;
          }
          if (pitem.terms.includes(tid) && cc1==cc2) {
            products_found++;
            pitem_one=pitem;
          }
        }
        if (products_found==0) {
          myhtml+='<td class="mytdcm" nowrap>--</td>';
        } else if (products_found==1) {
          var exist_val=0;
          $('.gks_product_zoom[data-id_product=' + pitem_one.id_product + ']').each(function() {
            exist_val=parseFloat($(this).parent().parent().parent().find('.gks_quantity').val());
            if (isNaN(exist_val)) exist_val=0;
            return; //mono to 1o na vrei  
          });
          myhtml+='<td class="mytdcm" nowrap><input data-product_id="' + pitem_one.id_product + '" title="' + pitem_one.product_descr_variable + '" value="' + (exist_val>0 ? exist_val : '') + '" type="number" class="dialog_add_product_variables_input form-control form-control-sm" min="0"></td>';
          ppitems++;
        } else {
          myhtml+='<td class="mytdcm" nowrap>' + products_found + ' '+gks_lang('είδη')+'</td>';
        }
        myhtml+='</tr>';
      }
    } 
    else if (idepi_row==0 && idepi_col!=0) {
      myhtml+='<tr>';
      for (var t in dialog_add_product_data.idiotites[idepi_col].terms) {
        term=dialog_add_product_data.idiotites[idepi_col].terms[t];
        myhtml+='<th class="table-dark" scope="col" width="0%" nowrap>' + term.idiotita_term_name + '</th>';
      }
      myhtml+='</tr>';
      myhtml+='<tr>';
      for (var t in dialog_add_product_data.idiotites[idepi_col].terms) {
        term=dialog_add_product_data.idiotites[idepi_col].terms[t];
        tid=term.id_product_idiotita_term;
        pitem_one=null;
        products_found=0;
        for (var p in dialog_add_product_data.products) {
          pitem=dialog_add_product_data.products[p];
          cc1=0;cc2=0;
          for(cc3=0;cc3<filter_other_terms.length;cc3++) {
            cc1++;
            if (pitem.terms.includes(filter_other_terms[cc3])) cc2++;
          }
          if (pitem.terms.includes(tid) && cc1==cc2) {
            products_found++;
            pitem_one=pitem;
          }
        }
        if (products_found==0) {
          myhtml+='<td class="mytdcm" nowrap>--</td>';
        } else if (products_found==1) {
          var exist_val=0;
          $('.gks_product_zoom[data-id_product=' + pitem_one.id_product + ']').each(function() {
            exist_val=parseFloat($(this).parent().parent().parent().find('.gks_quantity').val());
            if (isNaN(exist_val)) exist_val=0;
            return; //mono to 1o na vrei  
          });
          myhtml+='<td class="mytdcm" nowrap><input data-product_id="' + pitem_one.id_product + '" title="' + pitem_one.product_descr_variable + '" value="' + (exist_val>0 ? exist_val : '') + '" type="number" class="dialog_add_product_variables_input form-control form-control-sm" min="0"></td>';
          ppitems++;
        } else {
          myhtml+='<td class="mytdcm" nowrap>' + products_found + ' '+gks_lang('είδη')+'</td>';
        }
      }          
      myhtml+='</tr>';
      
    } 
    else if (idepi_row!=0 && idepi_col!=0) {

      myhtml+='<tr>';
      myhtml+='<th class="table-dark" scope="col" width="0%" nowrap>#</th>';
      for (var t in dialog_add_product_data.idiotites[idepi_col].terms) {
        term=dialog_add_product_data.idiotites[idepi_col].terms[t];
        myhtml+='<th class="table-dark" scope="col" width="0%" nowrap>' + term.idiotita_term_name + '</th>';
      }
      myhtml+='</tr>';  
      
      for (var t in dialog_add_product_data.idiotites[idepi_row].terms) {
        term=dialog_add_product_data.idiotites[idepi_row].terms[t];
        tid=term.id_product_idiotita_term;
        myhtml+='<tr>';
        myhtml+='<th class="table-dark" scope="col" width="0%" nowrap>' + term.idiotita_term_name + '</th>';
        
        
        
        for (var t2 in dialog_add_product_data.idiotites[idepi_col].terms) {
          term2=dialog_add_product_data.idiotites[idepi_col].terms[t2];
          tid2=term2.id_product_idiotita_term;
          
          pitem_one=null;
          products_found=0;
          for (var p in dialog_add_product_data.products) {
            pitem=dialog_add_product_data.products[p];
            cc1=0;cc2=0;
            for(cc3=0;cc3<filter_other_terms.length;cc3++) {
              cc1++;
              if (pitem.terms.includes(filter_other_terms[cc3])) cc2++;
            }
            if (pitem.terms.includes(tid) && pitem.terms.includes(tid2) && cc1==cc2) {
              products_found++;
              pitem_one=pitem;
            }
          }
          if (products_found==0) {
            myhtml+='<td class="mytdcm" nowrap>--</td>';
          } else if (products_found==1) {
            var exist_val=0;
            $('.gks_product_zoom[data-id_product=' + pitem_one.id_product + ']').each(function() {
              exist_val=parseFloat($(this).parent().parent().parent().find('.gks_quantity').val());
              if (isNaN(exist_val)) exist_val=0;
              return; //mono to 1o na vrei  
            });
            myhtml+='<td class="mytdcm" nowrap><input data-product_id="' + pitem_one.id_product + '" title="' + pitem_one.product_descr_variable + '" value="' + (exist_val>0 ? exist_val : '') + '" type="number" class="dialog_add_product_variables_input form-control form-control-sm" min="0"></td>';
            ppitems++;
          } else {
            myhtml+='<td class="mytdcm" nowrap>' + products_found + ' '+gks_lang('είδη')+'</td>';
          }
        }
        
        myhtml+='</tr>';
      }      
      
      
    }
    myhtml+='</tbody>';
    myhtml+='</table>';
    
    $('#dialog_add_product_variables_table').html(myhtml);
    if (ppitems>0) {
      $('#dialog_add_product_variables_save').show();
    } else {
      $('#dialog_add_product_variables_save').hide();
    }
  }
  
  $('#dialog_add_product_row').change(function() {
    dialog_add_product_def_row=$('#dialog_add_product_row option:selected').text();
    dialog_add_product_drawtable('row');
    cvalue={};
    cvalue.dialog_add_product_def_row=dialog_add_product_def_row;
    cvalue.dialog_add_product_def_col=dialog_add_product_def_col;
    gks_setCookie('gks_dialog_add_product',JSON.stringify(cvalue),100*24*60*60,'/my/admin-whi-mov-item.php');
  });  
  $('#dialog_add_product_col').change(function() {
    dialog_add_product_def_col=$('#dialog_add_product_col option:selected').text();
    dialog_add_product_drawtable('col');
    cvalue={};
    cvalue.dialog_add_product_def_row=dialog_add_product_def_row;
    cvalue.dialog_add_product_def_col=dialog_add_product_def_col;
    gks_setCookie('gks_dialog_add_product',JSON.stringify(cvalue),100*24*60*60,'/my/admin-whi-mov-item.php');
  });

  function dialog_add_product_other_change() {
    ii=parseInt($(this).attr('data-ii'));
    if (isNaN(ii)) ii=-1; if (ii<0) return;
    dialog_add_product_drawtable('other',ii);
  }
  function dialog_add_product_other_term_change() {
    ii=parseInt($(this).attr('data-ii'));
    if (isNaN(ii)) ii=-1; if (ii<0) return;
    dialog_add_product_drawtable('other_term',ii);
  }
  

  
  $('#gks_get_live_status_delivery_note').click(function() {
    gsdn_cid=$(this).attr('data-cid');
    gsdn_mark=$(this).attr('data-mark');
    gsdn_issuerVatNumber=$(this).attr('data-issuerVatNumber');
    if (gsdn_cid=='') {myalert('error:'.gks_lang('Δεν έχει ορισθεί η εταιρεία')); return;}
    if (gsdn_mark=='') {myalert('error:'.gks_lang('Δεν βρέθηκε το ΜΑΡΚ')); return;}
    
    $('#gks_get_live_status_delivery_note').prop('disabled',true);
    $('#gsdn_status').css('opacity','0.5');
    $('#gsdn_date').css('opacity','0.5');
    $('#gsdn_html_data').css('opacity','0.5');
    $('#gsdn_vat_issuer').css('opacity','0.5');
    $('#gsdn_vat_customer').css('opacity','0.5');
    $('#gsdn_records_cc').html('<i class="fas fa-hourglass" id="gsdn_hourglass" style="font-size: 150%;color: gray;vertical-align: bottom;"></i>');
    
    datasend='cmd=' + encodeURIComponent($.base64.encode('status'));
    datasend+='&call_from=' + encodeURIComponent($.base64.encode('item'));
    datasend+='&cid=' + encodeURIComponent($.base64.encode(gsdn_cid));
    datasend+='&mark=' + encodeURIComponent($.base64.encode(gsdn_mark));
    datasend+='&issuerVatNumber=' + encodeURIComponent($.base64.encode(gsdn_issuerVatNumber));
    $.ajax({
			url: '/my/admin-aade-delivery-note-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#gks_get_live_status_delivery_note').prop('disabled',false);
        $('#gsdn_status').html('').css('opacity','1').attr('class','');
        $('#gsdn_date').html('').css('opacity','1');
        $('#gsdn_html_data').html('<div class="alert alert-danger" role="alert">' + gks_lang('Σφάλμα') + ' ' + jqXHR.responseText + '</div>').css('opacity','1');
        $('#gsdn_vat_issuer').html('').css('opacity','1');
        $('#gsdn_vat_customer').html('').css('opacity','1');
        $('#gsdn_records_cc').html('').css('opacity','1');
			},
			success: function(data) {
				if (!data) {
  			  $('#gks_get_live_status_delivery_note').prop('disabled',false);
          $('#gsdn_status').html('').css('opacity','1').attr('class','');
          $('#gsdn_date').html('').css('opacity','1');
          $('#gsdn_html_data').html('<div class="alert alert-danger" role="alert">' + gks_lang('Σφάλμα') + ' ' + gks_lang('Παρακαλώ δοκιμάστε αργότερα') + '</div>').css('opacity','1');  
          $('#gsdn_vat_issuer').html('').css('opacity','1');
          $('#gsdn_vat_customer').html('').css('opacity','1');
          $('#gsdn_records_cc').html('').css('opacity','1');
				} else {
					if (data.success == true) {
    			  $('#gks_get_live_status_delivery_note').prop('disabled',false);
            $('#gsdn_status').html($.base64.decode(data.gsdn_status_descr)).attr('class','aade_delivery_status_' + data.gsdn_status).css('opacity','1');
            $('#gsdn_date').html($.base64.decode(data.date)).css('opacity','1');
            $('#gsdn_html_data').html($.base64.decode(data.html)).css('opacity','1');
            $('#gsdn_vat_issuer').html($.base64.decode(data.vat_issuer)).css('opacity','1');
            $('#gsdn_vat_customer').html($.base64.decode(data.vat_customer)).css('opacity','1');
            $('#gsdn_records_cc').html(data.records_cc).css('opacity','1');
					} else {
    			  $('#gks_get_live_status_delivery_note').prop('disabled',false);
            $('#gsdn_status').html('').css('opacity','1').attr('class','');
            $('#gsdn_date').html('').css('opacity','1');
            $('#gsdn_html_data').html('<div class="alert alert-danger" role="alert">' + gks_lang('Σφάλμα') + ' ' + $.base64.decode(data.message) + '</div>').css('opacity','1');
            $('#gsdn_vat_issuer').html('').css('opacity','1');
            $('#gsdn_vat_customer').html('').css('opacity','1');
            $('#gsdn_records_cc').html('').css('opacity','1');
					}
				}
			}
		});    
  });    
  
  // last of all 
  if (from_php_id==-1 && from_php_template_id==0) {
    eidoi_add(true,0);
    other_entity_add(true,0);
    correlated_invoices_add(true,0);
    multiple_connected_marks_add(true,0);
    packings_declarations_add(true,0);
    $('#user').focus().select(); //to customer for select amesa
  } else {
    if (last_aa==0) {
      if (from_php_gks_lock==false) eidoi_add(true,0);
    }
    if (last_oeaa==0) {
      if (from_php_gks_lock==false) other_entity_add(true,0);
    }
    if (last_coiaa==0) {
      if (from_php_gks_lock==false) correlated_invoices_add(true,0);
    }
    if (last_mcmaa==0) {
      if (from_php_gks_lock==false) multiple_connected_marks_add(true,0);
    }    
    if (last_pdeaa==0) {
      if (from_php_gks_lock==false) packings_declarations_add(true,0);
    }    
  }  

  if (from_php_id==-1 && from_php_template_id>0) {
    kostos_apostolis_mode='manual';
    calc_pliroteo();
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
  
  
  
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (from_php_perm_ret_edit==false) return;
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
  //console.log('load end');

	  
});
