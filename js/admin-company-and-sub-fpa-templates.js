/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {

  $('.gks_mybasefpa').on(mychange,function() {
    base_id=parseInt($(this).attr('data-base_id')); if (isNaN(base_id)) base_id=0;
    if (base_id<=0) return;
    baseval=parseInt($(this).val()); if (isNaN(baseval)) baseval=0;
    if (baseval<=0) {
      $('.gks_div_fpa_base_' + base_id).hide();
    } else {
      $('.gks_div_fpa_base_' + base_id).show();
    }
  });
  
  $('#gks_fpa_template_show').click(function() {
    sss=$(this).attr('data-show');
    if (sss=='0') {
      $(this).attr('data-show','1').html(gks_lang('Απόκρυψη'));   
      $('#div_fpa_templates').slideDown();  
    } else {
      $(this).attr('data-show','0').html(gks_lang('Εμφάνιση')); 
      $('#div_fpa_templates').slideUp();  
    }  
  });
  
  var template_id_selected='';
  $('.gks_fpa_template_apply').click(function() {
    template_id=$(this).attr('data-template-id');
    //console.log(template_id);
    template_id_selected=template_id;
    myconfirm(gks_lang('Σίγουρα θέλετε να εφαρμόσετε τις προεπιλεγμένες ρυθμίσεις ΦΠΑ ;')+'<br>'+gks_lang('Οι υπάρχουσες ρυθμίσεις θα αντικατασταθούν'),'gks_fpa_template_apply_run');
    
  });
  
  window.gks_fpa_template_apply_run=function() {
    //console.log(template_id_selected);
    val1=[];val2=[];
    switch (template_id_selected) {
      case 'gr_normal':
        //fpa_base_id	fpa_id
        val1.push([1001,3001]);
        val1.push([1002,3002]);
        val1.push([1003,3003]);
        val1.push([1004,3007]);

        val2.push([1,1001,3001]);
        val2.push([1,1002,3002]);
        val2.push([1,1003,3003]);
        val2.push([1,1004,3007]);
        val2.push([2,1001,3001]);
        val2.push([2,1002,3002]);
        val2.push([2,1003,3003]);
        val2.push([2,1004,3007]);
        val2.push([3,1001,3001]);
        val2.push([3,1002,3002]);
        val2.push([3,1003,3003]);
        val2.push([3,1004,3007]);
        val2.push([4,1001,3004]);
        val2.push([4,1002,3005]);
        val2.push([4,1003,3006]);
        val2.push([4,1004,3007]);
        val2.push([11,1001,3001]);
        val2.push([11,1002,3002]);
        val2.push([11,1003,3003]);
        val2.push([11,1004,3007]);
        val2.push([12,1001,3001]);
        val2.push([12,1002,3002]);
        val2.push([12,1003,3003]);
        val2.push([12,1004,3007]);
        val2.push([21,1001,3004]);
        val2.push([21,1002,3005]);
        val2.push([21,1003,3006]);
        val2.push([21,1004,3007]);
        val2.push([22,1001,3004]);
        val2.push([22,1002,3005]);
        val2.push([22,1003,3006]);
        val2.push([22,1004,3007]);
        val2.push([31,1001,3007]);
        val2.push([31,1002,3007]);
        val2.push([31,1003,3007]);
        val2.push([31,1004,3007]);
        val2.push([32,1001,3007]);
        val2.push([32,1002,3007]);
        val2.push([32,1003,3007]);
        val2.push([32,1004,3007]);
        val2.push([41,1001,3007]);
        val2.push([41,1002,3007]);
        val2.push([41,1003,3007]);
        val2.push([41,1004,3007]);
        val2.push([42,1001,3007]);
        val2.push([42,1002,3007]);
        val2.push([42,1003,3007]);
        val2.push([42,1004,3007]);
        val2.push([51,1001,3007]);
        val2.push([51,1002,3007]);
        val2.push([51,1003,3007]);
        val2.push([51,1004,3007]);
        val2.push([52,1001,3007]);
        val2.push([52,1002,3007]);
        val2.push([52,1003,3007]);
        val2.push([52,1004,3007]);
        
        break;
      case 'gr_meiome':
        val1.push([1001,3004]);
        val1.push([1002,3005]);
        val1.push([1003,3006]);
        val1.push([1004,3007]);

        val2.push([1,1001,3004]);
        val2.push([1,1002,3005]);
        val2.push([1,1003,3006]);
        val2.push([1,1004,3007]);
        val2.push([2,1001,3004]);
        val2.push([2,1002,3005]);
        val2.push([2,1003,3006]);
        val2.push([2,1004,3007]);
        val2.push([3,1001,3004]);
        val2.push([3,1002,3005]);
        val2.push([3,1003,3006]);
        val2.push([3,1004,3007]);
        val2.push([11,1001,3001]);
        val2.push([11,1002,3002]);
        val2.push([11,1003,3003]);
        val2.push([11,1004,3007]);
        val2.push([12,1001,3001]);
        val2.push([12,1002,3002]);
        val2.push([12,1003,3003]);
        val2.push([12,1004,3007]);
        val2.push([21,1001,3004]);
        val2.push([21,1002,3005]);
        val2.push([21,1003,3006]);
        val2.push([21,1004,3007]);
        val2.push([22,1001,3004]);
        val2.push([22,1002,3005]);
        val2.push([22,1003,3006]);
        val2.push([22,1004,3007]);
        val2.push([31,1001,3007]);
        val2.push([31,1002,3007]);
        val2.push([31,1003,3007]);
        val2.push([31,1004,3007]);
        val2.push([32,1001,3007]);
        val2.push([32,1002,3007]);
        val2.push([32,1003,3007]);
        val2.push([32,1004,3007]);
        val2.push([41,1001,3007]);
        val2.push([41,1002,3007]);
        val2.push([41,1003,3007]);
        val2.push([41,1004,3007]);
        val2.push([42,1001,3007]);
        val2.push([42,1002,3007]);
        val2.push([42,1003,3007]);
        val2.push([42,1004,3007]);
        val2.push([51,1001,3007]);
        val2.push([51,1002,3007]);
        val2.push([51,1003,3007]);
        val2.push([51,1004,3007]);
        val2.push([52,1001,3007]);
        val2.push([52,1002,3007]);
        val2.push([52,1003,3007]);
        val2.push([52,1004,3007]);
        val2.push([4,1001,3004]);
        val2.push([4,1002,3005]);
        val2.push([4,1003,3006]);
        val2.push([4,1004,3007]);
        
        break;
      case 'gr_mideni':
        val1.push([1001,3007]);
        val1.push([1002,3007]);
        val1.push([1003,3007]);
        val1.push([1004,3007]);  
        
        val2.push([1,1001,3007]);
        val2.push([1,1002,3007]);
        val2.push([1,1003,3007]);
        val2.push([1,1004,3007]);
        val2.push([2,1001,3007]);
        val2.push([2,1002,3007]);
        val2.push([2,1003,3007]);
        val2.push([2,1004,3007]);
        val2.push([3,1001,3007]);
        val2.push([3,1002,3007]);
        val2.push([3,1003,3007]);
        val2.push([3,1004,3007]);
        val2.push([4,1001,3004]);
        val2.push([4,1002,3005]);
        val2.push([4,1003,3006]);
        val2.push([4,1004,3007]);
        val2.push([11,1001,3001]);
        val2.push([11,1002,3004]);
        val2.push([11,1003,3003]);
        val2.push([11,1004,3007]);
        val2.push([12,1001,3001]);
        val2.push([12,1002,3004]);
        val2.push([12,1003,3003]);
        val2.push([12,1004,3007]);
        val2.push([21,1001,3004]);
        val2.push([21,1002,3005]);
        val2.push([21,1003,3006]);
        val2.push([21,1004,3007]);
        val2.push([22,1001,3004]);
        val2.push([22,1002,3005]);
        val2.push([22,1003,3006]);
        val2.push([22,1004,3007]);
        val2.push([31,1001,3007]);
        val2.push([31,1002,3007]);
        val2.push([31,1003,3007]);
        val2.push([31,1004,3007]);
        val2.push([32,1001,3007]);
        val2.push([32,1002,3007]);
        val2.push([32,1003,3007]);
        val2.push([32,1004,3007]);
        val2.push([41,1001,3007]);
        val2.push([41,1002,3007]);
        val2.push([41,1003,3007]);
        val2.push([41,1004,3007]);
        val2.push([42,1001,3007]);
        val2.push([42,1002,3007]);
        val2.push([42,1003,3007]);
        val2.push([42,1004,3007]);
        val2.push([51,1001,3007]);
        val2.push([51,1002,3007]);
        val2.push([51,1003,3007]);
        val2.push([51,1004,3007]);
        val2.push([52,1001,3007]);
        val2.push([52,1002,3007]);
        val2.push([52,1003,3007]);
        val2.push([52,1004,3007]);
              
        break;
      case 'cy_normal':
        val1.push([1001,3011]);
        val1.push([1002,3005]);
        val1.push([1003,3013]);
        val1.push([1004,3007]);
        val1.push([1005,3014]);

        val2.push([1,1001,3011]);
        val2.push([1,1002,3005]);
        val2.push([1,1003,3013]);
        val2.push([1,1004,3007]);
        val2.push([2,1001,3011]);
        val2.push([2,1002,3005]);
        val2.push([2,1003,3013]);
        val2.push([2,1004,3007]);
        val2.push([3,1001,3011]);
        val2.push([3,1002,3005]);
        val2.push([3,1003,3013]);
        val2.push([3,1004,3007]);
        val2.push([11,1001,3011]);
        val2.push([11,1002,3005]);
        val2.push([11,1003,3013]);
        val2.push([11,1004,3007]);
        val2.push([12,1001,3011]);
        val2.push([12,1002,3005]);
        val2.push([12,1003,3013]);
        val2.push([12,1004,3007]);
        val2.push([21,1001,3011]);
        val2.push([21,1002,3005]);
        val2.push([21,1003,3013]);
        val2.push([21,1004,3007]);
        val2.push([22,1001,3011]);
        val2.push([22,1002,3005]);
        val2.push([22,1003,3013]);
        val2.push([22,1004,3007]);
        val2.push([31,1001,3007]);
        val2.push([31,1002,3007]);
        val2.push([31,1003,3007]);
        val2.push([31,1004,3007]);
        val2.push([32,1001,3007]);
        val2.push([32,1002,3007]);
        val2.push([32,1003,3007]);
        val2.push([32,1004,3007]);
        val2.push([41,1001,3007]);
        val2.push([41,1002,3007]);
        val2.push([41,1003,3007]);
        val2.push([41,1004,3007]);
        val2.push([42,1001,3007]);
        val2.push([42,1002,3007]);
        val2.push([42,1003,3007]);
        val2.push([42,1004,3007]);
        val2.push([51,1001,3007]);
        val2.push([51,1002,3007]);
        val2.push([51,1003,3007]);
        val2.push([51,1004,3007]);
        val2.push([52,1001,3007]);
        val2.push([52,1002,3007]);
        val2.push([52,1003,3007]);
        val2.push([52,1004,3007]);
        val2.push([4,1001,3011]);
        val2.push([4,1002,3005]);
        val2.push([4,1003,3013]);
        val2.push([4,1004,3007]);
        val2.push([31,1005,3007]);
        val2.push([52,1005,3007]);
        val2.push([51,1005,3007]);
        val2.push([42,1005,3007]);
        val2.push([41,1005,3007]);
        val2.push([32,1005,3007]);
        val2.push([1,1005,3014]);
        val2.push([22,1005,3014]);
        val2.push([21,1005,3014]);
        val2.push([12,1005,3014]);
        val2.push([11,1005,3014]);
        val2.push([3,1005,3014]);
        val2.push([2,1005,3014]);
        val2.push([4,1005,3014]);
        
        break;
      default:
        myalert('error:'+gks_lang('Λάθος ρύθμιση προτύπου'));
        return
    }
    $('.gks_div_fpa_base_1001').hide();
    $('.gks_div_fpa_base_1002').hide();
    $('.gks_div_fpa_base_1003').hide();
    $('.gks_div_fpa_base_1004').hide();
    $('.gks_div_fpa_base_1005').hide();
    
    $('.gks_mybasefpa').val('0');
    for (i=0; i<val1.length;i++) {
      $('.gks_mybasefpa[data-base_id=' + val1[i][0] + ']').val(val1[i][1]);
      $('.gks_div_fpa_base_' + val1[i][0]).show();
    }
    
    $('.gks_myfpa').val('0');
    for (i=0; i<val2.length;i++) $('.gks_myfpa[data-fiscal_id=' + val2[i][0] + '][data-base_id=' + val2[i][1] + ']').val(val2[i][2]);
    
  }
  
});

