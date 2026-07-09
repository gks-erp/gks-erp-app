/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/




jQuery(document).ready(function($) {
  
  $('#mydatejump').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y',timepicker:false,dayOfWeekStart:1,onChangeDateTime:function(ct,$i){
      var mynow = new Date(from_php_mynow_Y,from_php_mynow_m,from_php_mynow_d,0,0,0,0 );
      var mydiff = ct - mynow; //in milliseconds
      mydiff = mydiff/1000/86400;
      //console.log(ct);
      //console.log(mynow);
      //console.log(mydiff);
      mydiff=Math.round(mydiff);
      //console.log(mydiff);
      $('#sform_day').val(mydiff);
      //return;
      $('#sform').submit();
    }
  }));
  
  $('#hotel_id').change(function() {
    $('#sshotel_id').val($('#hotel_id').val());
    $('#sform').submit();
  });
  
  $('#splusdays').change(function() {
    $('#plusdays').val($('#splusdays').val());
    $('#sform').submit();
  });    
  
  $('.plan-room-type').click(function() {
    elem = $(this).parent().parent();
    id_roomtype=elem.attr('data-id-roomtype');
    c=$(this).attr('class');
    if (c.indexOf('fa-angle-down') !== -1) {
      $(this).removeClass('fa-angle-down');
      $(this).addClass('fa-angle-right');
      $('tr[data-roomtype-id=' + id_roomtype + ']').each(function( index ) {
        $(this).hide('fade', 500);
      });
    } else {
      $(this).removeClass('fa-angle-right');
      $(this).addClass('fa-angle-down');
      $('tr[data-roomtype-id=' + id_roomtype + ']').each(function( index ) {
        $(this).show('fade', 500);
      });      
    }
    
  });
  
  
  $('.mytdtt').tooltipster({
    theme: 'tooltipster-noir',
    contentAsHTML: true, 
    interactive:true,
    functionInit: function(instance, helper) {
      //console.log(instance);
      //console.log(helper);
      data_esrv_folio=$(helper.origin).attr('data-resrv_folio');
      //console.log(data_esrv_folio);
      //console.log(helper);
      items=data_esrv_folio.split('|');
      mytooltip='';
      for (i=0; i < items.length; i++) {
        if (items[i].trim()!='') {
          if (items[i][0]=='r') { //reservation
            rid=items[i].substring(1);
            //console.log(rid);
            mytooltip+=
            gks_lang('Κράτηση')+': <a href=admin-hotel-reservation-item.php?id=' + from_php_reservation_data[rid].id_hotel_reservation + '>#' + from_php_reservation_data[rid].id_hotel_reservation + '</a><br>' +
            from_php_reservation_data[rid].rstatusspan +
            gks_lang('Ονοματεπώνυμο')+': ' + from_php_reservation_data[rid].user_first_name + ' ' + from_php_reservation_data[rid].user_last_name + '<br>' + 
            gks_lang('Από')+': ' + from_php_reservation_data[rid].check_in + '<br>' + 
            gks_lang('Έως')+': ' + from_php_reservation_data[rid].check_out + '<br>' + 
            gks_lang('Διανυκτερεύσεις')+': ' + from_php_reservation_data[rid].num_days + '<br>' + 
            gks_lang('Δωμάτια')+': ' + from_php_reservation_data[rid].rooms_plithos + '<br>' + 
            gks_lang('Επισκέπτες')+': <i class="fa fa-male" style="color:#aaaaaa;"></i>' + from_php_reservation_data[rid].num_adults + 
            (from_php_reservation_data[rid].num_childs > 0 ? '<i class="fa fa-child" style="color:#aaaaaa;font-size:80%;"></i>' + from_php_reservation_data[rid].num_childs : '') +
              '<br>' +
            (from_php_reservation_data[rid].num_child_kounies > 0 ? gks_lang('Βρεφικά κρεβάτια')+': <i class="fa fa-box" style="color:#aaaaaa;font-size:90%;"></i>' + from_php_reservation_data[rid].num_child_kounies + '<br>' : '') +
            (from_php_reservation_data[rid].num_extra_beds > 0 ? gks_lang('Επιπλέον κρεβάτια')+': <i class="fa fa-bed" style="color:#aaaaaa;"></i>' + from_php_reservation_data[rid].num_extra_beds + '<br>' : '') +
            (from_php_reservation_data[rid].user_email!='' ? '<i class="fa fa-envelope"></i> <a href="mailto:' + from_php_reservation_data[rid].user_email + '">' + from_php_reservation_data[rid].user_email + '</a><br>' : '') +
            (from_php_reservation_data[rid].user_mobile!='' ? '<i class="fa fa-mobile"></i> <a href="tel:' + from_php_reservation_data[rid].user_mobile + '">' + from_php_reservation_data[rid].user_mobile + '</a><br>' : '') +
            (from_php_reservation_data[rid].lang_name!='' ? gks_lang('Γλώσσα')+': ' + from_php_reservation_data[rid].lang_name + '<br>' : '') +
            (from_php_reservation_data[rid].country_name!='' ? gks_lang('Χώρα')+': ' + from_php_reservation_data[rid].country_name + '<br>' : '') +
            
            (from_php_reservation_data[rid].gks_price_total!=0 ? 
              gks_lang('Σύνολο')+': <b>' + from_php_reservation_data[rid].gks_price_total.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + '</b>' + 
              ' ' + gks_lang('Ανά ημέρα') + ': <b>' + 
              (from_php_reservation_data[rid].num_days !=0 ? (from_php_reservation_data[rid].gks_price_total/from_php_reservation_data[rid].num_days).formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + '</b>': '') + '<br>'
              : '') + 
            
            (from_php_reservation_data[rid].user_notes!='' ? gks_lang('Σχόλιο από πελάτη')+': ' + from_php_reservation_data[rid].user_notes + '<br>' : '') + 
            (from_php_reservation_data[rid].sxolio!='' ? gks_lang('Σχόλιο κράτησης')+': ' + from_php_reservation_data[rid].sxolio + '<br>' : '') + 
            
            (from_php_reservation_data[rid].crm_channel_sale_descr!='' ? gks_lang('Κανάλι πωλήσεων')+': ' + from_php_reservation_data[rid].crm_channel_sale_descr + '<br>' : '') +
            (from_php_reservation_data[rid].crm_channel_contact_gks_nickname!='' ? gks_lang('Επαφή Πωλήσεων')+': ' + from_php_reservation_data[rid].crm_channel_contact_gks_nickname + '<br>' : '') +
            (from_php_reservation_data[rid].ads_campain_name!='' ? gks_lang('Καμπάνια')+': ' + from_php_reservation_data[rid].ads_campain_name + '<br>' : '') +
            (from_php_reservation_data[rid].crm_channel_url!='' ? gks_lang('URL')+': <a href="' + from_php_reservation_data[rid].crm_channel_url + '" target="_blank">' + from_php_reservation_data[rid].crm_channel_url + '</a><br>' : '') +
            (from_php_reservation_data[rid].crm_channel_code!='' ? gks_lang('Κωδικός CRM')+': ' + from_php_reservation_data[rid].crm_channel_code + '<br>' : '') +
            (from_php_reservation_data[rid].crm_channel_text!='' ? gks_lang('Σχόλιο CRM')+': ' + from_php_reservation_data[rid].crm_channel_text + '<br>' : '') +
            
            '<hr>';
          } else { //folio
            
          }
          
        }
      }
      mytooltip=mytooltip.replaceAll('<br><hr>','<hr>');
      if (mytooltip.endsWith('<hr>')) mytooltip=mytooltip.substring(0, mytooltip.length-4);
      
      instance.content(mytooltip);
    }  
  });  
	  
});
