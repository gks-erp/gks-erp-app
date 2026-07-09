/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var ftp_pos_running=false;
var gks_pos_panel_pay_data_html='';  
var gks_worldline_implementation='';

jQuery(document).ready(function($) {
  

  var dialog_payment_with;
  dialog_payment_with = $('#dialog_payment_with').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_payment_with_start",
        html: '<i class="far fa-credit-card"></i> '+gks_lang('Έναρξη'),
        //icon: "ui-icon-print",  
        click: function() {
          dialog_payment_with_start(); 
        }
      },
      {
        id: "dialog_payment_with_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $( this ).dialog( "close" );
          fnc_gks_erp_app_mobile_pos_dialog_card_open(false);
        }
      },
      {
        id: "dialog_payment_with_close",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        click: function() {
          $( this ).dialog( "close" );
          fnc_gks_erp_app_mobile_pos_dialog_card_open(false);
        }
      },
      {
        id: "dialog_payment_with_wait",
        html: '<i class="fas fa-hourglass-half"></i> '+gks_lang('Παρακαλώ περιμένετε')+' ...',
        click: function() {
          
        }
      },      
    ],
  });
    
  window.div_payment_type_multi_item_pos_start_click=function(this_elem,transaction_type,doc_id,prev_eftpos_id,myforce) {
    if (transaction_type=='') {
      myalert('error:'+gks_lang('Δεν βρέθηκε ο τύπος συναλλαγής'));
      return;}
    
    mypage=window.location.pathname;
    if (mypage=='/my/admin-pos-run.php') {
      if (from_php_id==-1) {
        one_pway=this_elem.parent().attr('data-one_pway');
        gks_eftpos_step='div_payment_one_terminal_start|' + one_pway;
        this_elem.parent().parent().find('.gks_pos_panel_pay_btn').click();
        return;
      }
      
    }
    
    pp_type='';
    pp=0;
    asset_id=0;
    pp_price=0;
    id_acc_xxx_payment=0;
    asset_title='';
    mytitle='';
    mytext='';
    id_payment_acquirer=0;

    //viva +tip +installments
    //megeftpos +tip +installments
    //mellon +tip +installments
    //cardlink -tip -installments
    
    pawid=0;
    if (typeof this_elem[0]==='undefined') { //einai apo contextmenu
      this_elem=contextMenu_eftpos_transaction_actions_prev_elem;
      pawid=parseInt(this_elem.attr('data-pawid'));
    } else { // einai allou
      pawid=parseInt(this_elem.parent().find('input').attr('data-pawid'));
    }
    
    if (isNaN(pawid)) pawid=0;
    
    tip_enable=true;installments_enable=true;
    
    if (pawid==4) { //cardlink
      tip_enable=false;installments_enable=false;
    }
    if (typeof from_php_pos_installments !== 'undefined' && from_php_pos_installments < 2)  installments_enable=false;
    if (typeof from_php_pos_tip !== 'undefined' && from_php_pos_tip==false)  tip_enable=false;
    
    if (tip_enable) {
      $('#dialog_payment_with_tip_val').prop('disabled',false).val(0);
      $('#dialog_payment_with_tip_pososto').prop('disabled',false).val('0');
    } else {
      $('#dialog_payment_with_tip_val').prop('disabled',true).val(0);
      $('#dialog_payment_with_tip_pososto').prop('disabled',true).val('0');
    }
    if (installments_enable) {
      $('#dialog_payment_with_doseis_val').prop('disabled',false).val(0);
      if (typeof from_php_pos_installments!=='undefined') {
        $('#dialog_payment_with_doseis_val option').each(function() {
          vvv=parseInt($(this).attr('value'));  
          if (vvv>0) {
            if (vvv<=from_php_pos_installments) {
              $(this).show();  
            } else {
              $(this).hide();
            }
          }
        });
        
      }
    } else {
      $('#dialog_payment_with_doseis_val').prop('disabled',true).val(0);
    }
    
    tap_enable=true;
    iris_enable=false;
    //me to all_comanys_preferred_payment_methods ti tha kano ?? einai sto admin-eftpos-transaction.php

    $('#dialog_payment_show_card_terminal_btn_viva').hide();
    
    if (transaction_type=='sale') {
      if (pawid==1) { //viva 
        if (typeof from_php_preferred_payment_methods.viva!=='undefined') {
          tap_enable=from_php_preferred_payment_methods.viva.includes('tap');
          iris_enable=from_php_preferred_payment_methods.viva.includes('iris');
        }
        if (!(typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1)) {
          if (gks_is_mobile()) {
            $('#dialog_payment_show_card_terminal_btn_viva').show();
          }
        }
      }
      if (pawid==3) { //mellon
        if (typeof from_php_preferred_payment_methods.mellon!=='undefined') {
          tap_enable=from_php_preferred_payment_methods.mellon.includes('tap');
          iris_enable=from_php_preferred_payment_methods.mellon.includes('iris');
        }
      }      
      if (pawid==4) { //cardlink
        if (typeof from_php_preferred_payment_methods.cardlink!=='undefined') {
          tap_enable=from_php_preferred_payment_methods.cardlink.includes('tap');
          iris_enable=from_php_preferred_payment_methods.cardlink.includes('iris');
        }
      }
      if (pawid==5) { //epay
        if (typeof from_php_preferred_payment_methods.epay!=='undefined') {
          tap_enable=from_php_preferred_payment_methods.epay.includes('tap');
          iris_enable=from_php_preferred_payment_methods.epay.includes('iris');
        }
      }
      if (pawid==7) { //nexi
        if (typeof from_php_preferred_payment_methods.nexi!=='undefined') {
          tap_enable=from_php_preferred_payment_methods.nexi.includes('tap');
          iris_enable=from_php_preferred_payment_methods.nexi.includes('iris');
        }
      }      
      
                 
      if (mypage=='/my/admin-pos-run.php') {
        if (doc_id<=0) {
          myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
          return;
        }
      } else if (doc_id<=0 || need_save) {
        myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
        return;
      }
      if (this_elem.hasClass('div_payment_one_terminal_start')) {
        pp_type='one';
        pp=-1;
        asset_id=this_elem.parent().find('.div_payment_one_terminal_terminal').attr('data-asset_id');
        asset_title=this_elem.parent().find('.div_payment_one_terminal_terminal').val();
        
        if (mypage=='/my/admin-pos-run.php') {
          var mytotal=0;
          //var myvattotal=0;
          //var myovattotal=0;
          $('.gks_pos_item').each(function() {
            quantity=parseFloat($(this).attr('data-quantity')); if (isNaN(quantity)) quantity=0;
            priceperitem=parseFloat($(this).attr('data-priceperitem')); if (isNaN(priceperitem)) priceperitem=0;
            itemprice=quantity*priceperitem;
            //$(this).attr('data-totalprice',itemprice);
            //$(this).find('.gks_pos_item_price_val').html(itemprice.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
            mytotal+=itemprice; 
            //vatperitem=parseFloat($(this).attr('data-vatperitem')); if (isNaN(vatperitem)) vatperitem=0;
            //myvattotal+=(quantity*vatperitem);
            //ovatperitem=parseFloat($(this).attr('data-ovatperitem')); if (isNaN(ovatperitem)) ovatperitem=0;
            //myovattotal+=(quantity*ovatperitem);
          });
              
          pp_price=mytotal;
          id_payment_acquirer=parseInt(this_elem.parent().attr('data-one_pway'));
          if (isNaN(id_payment_acquirer)) id_payment_acquirer=0;
          
        } else {
          pp_price=parseFloat($('#gks_total_price_total').attr('data-val'));
        }
        id_acc_xxx_payment=-1;
      } else { //div_payment_type_multi_item_pos_start
        pp_type='multi';
        pp=parseInt(this_elem.attr('data-pp')); 
        asset_id=this_elem.parent().find('.div_payment_type_multi_item_pos_terminal').attr('data-asset_id');
        asset_title=this_elem.parent().find('.div_payment_type_multi_item_pos_terminal').val();
        if ($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').prop('nodeName')=='DIV') {
          pp_price=parseFloat($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').attr('data-lock_value'));
        } else {
          pp_price=parseFloat($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').val());
        }
        id_acc_xxx_payment=$('.div_payment_type_multi_item[data-pp=' + pp + ']').attr('data-rec_id');
      }


      if (isNaN(pp)) pp=0;
      if (isNaN(asset_id)) asset_id=0;
      if (isNaN(pp_price)) pp_price=0;
      if (isNaN(id_acc_xxx_payment)) id_acc_xxx_payment=0;
      
      if (asset_id<=0) {
        myalert('error:'+gks_lang('Επιλέξτε το πάγιο - τερματικό που θα γίνει η συναλλαγή'));
        return;}
      if (pp_price<=0) {
        myalert('error:'+gks_lang('Δεν βρέθηκε το ποσό'));
        return;}
      if (id_acc_xxx_payment<=0 && id_acc_xxx_payment!=-1) {
        myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το παραστατικό')+' (2)');
        return;}
      
      mytitle=gks_lang('Πληρωμή με POS');
      mytext=gks_lang('Σίγουρα θέλετε να κάνετε χρέωση του ποσού')+' <b>' +
        (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
        pp_price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
        (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
        '</b> ' +
        gks_lang('μέσω του τερματικού')+' <b>' + 
        asset_title + 
        '</b>;';
      $('#dialog_payment_with_icon2').attr('class', 'fas fa-arrow-circle-right');
      
    } else if (transaction_type=='fullvoid' || transaction_type=='fullvoiderp' || transaction_type=='refund' || transaction_type=='refunderp') {
      if (myforce==false) {
        if (contextMenu_eftpos_transaction_actions_prev_elem==null) {
          myalert('error:'+gks_lang('Δεν εντοπίστηκε η συναλλαγή')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
          return;        
        }
        if (contextMenu_eftpos_transaction_actions_prev_elem.hasClass('gks_eftpos_transaction_actions')) {
          //sel_elem=$('.gks_eftpos_transaction_actions[data-id=' + contextMenu_id_eftpos_transaction + ']');
          sel_elem=contextMenu_eftpos_transaction_actions_prev_elem;
        } else if (contextMenu_eftpos_transaction_actions_prev_elem.hasClass('gks_payment_next_actions')) { 
          //sel_elem=$('.gks_payment_next_actions[data-id=' + contextMenu_id_eftpos_transaction + ']');
          sel_elem=contextMenu_eftpos_transaction_actions_prev_elem;
        } else {
          myalert('error:'+gks_lang('Δεν εντοπίστηκε η συναλλαγή')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
          return;          
        }
        if (sel_elem.length!=1) {
          myalert('error:'+gks_lang('Δεν εντοπίστηκε η συναλλαγή')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
          return;
        }
        pp_price=parseFloat(sel_elem.attr('data-poso')); if (isNaN(pp_price)) pp_price=0;
        asset_id=parseInt(sel_elem.attr('data-asset_id')); if (isNaN(asset_id)) asset_id=0;
        asset_title=$.base64.decode(sel_elem.attr('data-asset_title')).trim(); 
        id_acc_xxx_payment=parseInt(sel_elem.attr('data-id_acc_xxx_payment'));if (isNaN(id_acc_xxx_payment)) id_acc_xxx_payment=0;
        
        
      } else {
        pp=parseInt(this_elem.attr('data-pp'));
        
        asset_id=parseInt($('.div_payment_type_multi_item_pos_terminal[data-pp=' + pp + ']').attr('data-asset_id'));
        asset_title=this_elem.parent().find('.div_payment_type_multi_item_pos_terminal').val();
        if ($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').prop('nodeName')=='DIV') {
          pp_price=parseFloat($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').attr('data-lock_value'));
        } else {
          pp_price=parseFloat($('.div_payment_type_multi_item_input[data-pp=' + pp + ']').val());
        }
        id_acc_xxx_payment=$('.div_payment_type_multi_item[data-pp=' + pp + ']').attr('data-rec_id');
        
      }
      
      
      
      if (pp_price<=0) {
        myalert('error:'+gks_lang('Δεν εντοπίστηκε το ποσό')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
        return;}
      if (asset_id<=0) {
        myalert('error:'+gks_lang('Δεν εντοπίστηκε το πάγιο')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
        return;}
      if (asset_title=='') {
        myalert('error:'+gks_lang('Δεν εντοπίστηκε το όνομα του παγίου')+'<br>'+gks_lang('Ανανεώστε την σελίδα'));
        return;}
      if (mypage!='/my/admin-eftpos-transaction.php') {
        if (id_acc_xxx_payment<=0 && id_acc_xxx_payment!=-1) {
          myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το παραστατικό')+' (3)');
          return;}
      }
            
      if (transaction_type=='fullvoid') {
        mytitle=gks_lang('Ακύρωση με POS');
        mytext=gks_lang('Σίγουρα θέλετε να ακυρώσετε την συναλλαγή και να επιστρέψετε το ποσό των') +
          '<br><b>' + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          pp_price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          '</b>' +
          '<br>'+gks_lang('στην κάρτα του πελάτη μέσω του τερματικού')+'<br><b>' + 
          asset_title + 
          '</b>;';
        $('#dialog_payment_with_icon2').attr('class', 'fas fa-arrow-circle-left');
      } else if (transaction_type=='fullvoiderp') {
        mytitle=gks_lang('Ακύρωση με POS');
        mytext=gks_lang('Σίγουρα θέλετε να ακυρώσετε την συναλλαγή μέσω ERP και παρόχου υπογραφών και να επιστρέψετε το ποσό των') +
          '<br><b>' + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          pp_price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          '</b>' +
          '<br>'+gks_lang('στην κάρτα του πελάτη μέσω του τερματικού')+'<br><b>' + 
          asset_title + 
          '</b> ;';
        $('#dialog_payment_with_icon2').attr('class', 'fas fa-arrow-circle-left');
      } else if (transaction_type=='refund') {
        mytitle=gks_lang('Επιστροφή με POS');
        mytext=gks_lang('Σίγουρα θέλετε να επιστρέψετε κάποια χρήματα από το μέγιστο ποσό των') +
          '<br><b>' + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          pp_price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          '</b>' +
          '<br>'+gks_lang('στην κάρτα του πελάτη μέσω του τερματικού')+'<br><b>' + 
          asset_title + 
          '</b>;';
        $('#dialog_payment_with_icon2').attr('class', 'fas fa-arrow-circle-left');
      } else if (transaction_type=='refunderp') {
        mytitle=gks_lang('Επιστροφή με POS');
        mytext=gks_lang('Σίγουρα θέλετε να επιστρέψετε κάποια χρήματα μέσω ERP και παρόχου υπογραφών από το μέγιστο ποσό των') +
          '<br><b>' + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW!='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          pp_price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,from_php_GKS_NUMBER_FORMAT_DECIMAL,from_php_GKS_NUMBER_FORMAT_THOUSAND) + 
          (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after' ? from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL : '') +
          '</b>' +
          '<br>'+gks_lang('στην κάρτα του πελάτη μέσω του τερματικού')+'<br><b>' + 
          asset_title + 
          '</b> ;';
        $('#dialog_payment_with_icon2').attr('class', 'fas fa-arrow-circle-left');
        
      }

      
      
      
      
      
    } else {
      myalert('error:'+gks_lang('Δεν έχει υλοποιηθεί ακόμα αυτή η ενέργεια')+': ' + transaction_type);
      return;  
    }
    
    
    if (tap_enable==false && iris_enable==false) tap_enable=true;

    $('#dialog_payment_with_ppm_radio_tap').prop('checked',false);
    $('#dialog_payment_with_ppm_radio_iris').prop('checked',false);

    if (tap_enable) $('#dialog_payment_with_ppm_radio_tap').prop('checked',true);
    else if (iris_enable) $('#dialog_payment_with_ppm_radio_iris').prop('checked',true);
    
    if (tap_enable) {
      $('#dialog_payment_with_ppm_tap').show();
    } else {
      $('#dialog_payment_with_ppm_tap').hide();
    }
    if (iris_enable) {
      $('#dialog_payment_with_ppm_iris').show();
    } else {
      $('#dialog_payment_with_ppm_iris').hide();
    }
    
    //$('#dialog_payment_with_tip_val').val(0).prop('disabled',false);
    //$('#dialog_payment_with_tip_pososto').val('0').prop('disabled',false);
    //$('#dialog_payment_with_doseis_val').val('0').prop('disabled',false);
    $('#dialog_payment_with_refund_val').val(pp_price).prop('disabled',false);

    if (transaction_type=='sale' || transaction_type=='saleerp') {
      $('#dialog_payment_with_tip').show();
      $('#dialog_payment_with_doseis').show();
      $('#dialog_payment_with_refund').hide();
    } else if (transaction_type=='fullvoid' || transaction_type=='fullvoiderp') {
      $('#dialog_payment_with_tip').hide();
      $('#dialog_payment_with_doseis').hide();
      $('#dialog_payment_with_refund').hide();
    } else if (transaction_type=='refund' || transaction_type=='refunderp') {
      $('#dialog_payment_with_tip').hide();
      $('#dialog_payment_with_doseis').hide();
      $('#dialog_payment_with_refund').show();
    } else {
      $('#dialog_payment_with_tip').hide();
      $('#dialog_payment_with_doseis').hide();
      $('#dialog_payment_with_refund').hide();      
    }
    
    
    
    
    $('#dialog_payment_with_icon').attr('class', 'far fa-credit-card dialog_payment_with_icon_transaction_type_' + transaction_type);
    $('#dialog_payment_with_icon2').addClass('dialog_payment_with_icon_transaction_type_' + transaction_type);
    
    
    $('#dialog_payment_with_title').html(mytitle);
    $('#dialog_payment_with_text1').html(mytext);
    $('#dialog_payment_with_text2').html('').hide();
    $('#dialog_payment_with_delete').hide();
    $('#dialog_payment_with_text3').html('').hide();
    $('#dialog_payment_with_text4').html('').hide();
    $('#dialog_payment_with_text5').html('').hide();
    $('#dialog_payment_with_text_error').html('').hide();
    
    
    $('#dialog_payment_with_start').show();
    $('#dialog_payment_with_cancel').show();
    $('#dialog_payment_with_close').hide();
    $('#dialog_payment_with_wait').hide();
    $('#dialog_payment_with_tdpb').hide();
    
    dialog_payment_with.gks_transaction_type=transaction_type;
    dialog_payment_with.gks_doc_id=doc_id;
    dialog_payment_with.gks_prev_eftpos_id=prev_eftpos_id;
    dialog_payment_with.gks_pp_type=pp_type;
    dialog_payment_with.gks_pp=pp;
    dialog_payment_with.gks_asset_id=asset_id;
    dialog_payment_with.gks_asset_title=asset_title;
    dialog_payment_with.gks_pp_price=pp_price;
    dialog_payment_with.gks_id_acc_xxx_payment=id_acc_xxx_payment;
    dialog_payment_with.gks_id_payment_acquirer=id_payment_acquirer;
    dialog_payment_with.gks_pawid=pawid;
    
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 600) dheight=600;
	  
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  } else if ($('body').hasClass('gks_erp_app_mobile')) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }
	  
	  dialog_payment_with.dialog('option', 'width', dwidth);
	  dialog_payment_with.dialog('option', 'height', dheight);
	  $('#dialog_payment_with').parent().css({position:'fixed'});      
    dialog_payment_with.dialog('open');
    
    
    
    if (mypage=='/my/admin-pos-run.php' && 
        typeof from_php_pos_auto_click_start_at_paywith !== 'undefined' &&
        from_php_pos_auto_click_start_at_paywith &&
        tip_enable==false && 
        installments_enable==false && 
        (transaction_type=='sale' || transaction_type=='saleerp') &&
        gks_pos_settings.min_clicks==true ) {
      $('#dialog_payment_with_start').click();
    } else {
      //if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
        $('#dialog_payment_with_start').focus();
      //}
    }
    
    
    fnc_gks_erp_app_mobile_pos_dialog_card_open(true);
    
    
     
  }
  
  $('.div_payment_one_terminal_start, .div_payment_type_multi_item_pos_start').click(function() {
    myforce=false;
    transaction_type='sale';
    mypage=window.location.pathname;
    if (mypage=='/my/admin-acc-pay-item.php') {
      elem=$('#pay_acc_journal_id');
      if (elem.length==1 && elem.prop('tagName')=='SELECT') {
        elemo=elem.find('option:selected');
        if (elemo.length==1 && elemo.attr('data-type_id')=='11' && elemo.attr('data-balance_pros')=='1') {
          transaction_type='refunderp';
          myforce=true;
        }
      } else if (elem.length==1 && elem.prop('tagName')=='INPUT' && elem.attr('data-type_id')=='11' && elem.attr('data-balance_pros')=='1') {
        transaction_type='refunderp';
        myforce=true;
      }
    }
    
    div_payment_type_multi_item_pos_start_click($(this),transaction_type,from_php_id,0,myforce);
  });
  
  $('#dialog_payment_with_tip_pososto').change(function() {
    pososto_val=parseInt($('#dialog_payment_with_tip_pososto').val()); if (isNaN(pososto_val)) pososto_val=0;
    if (pososto_val==0) {
      $('#dialog_payment_with_tip_val').val(0);
    } else {
      tipamount=((pososto_val/100) *dialog_payment_with.gks_pp_price).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $('#dialog_payment_with_tip_val').val(tipamount);
    } 
  });

  function dialog_payment_with_start() {
    
    $('#dialog_payment_with_text2').html('').hide();
    $('#dialog_payment_with_delete').hide();
    $('#dialog_payment_with_text3').html('').hide();
    $('#dialog_payment_with_text4').html('').hide();
    $('#dialog_payment_with_text5').html('').hide();
    $('#dialog_payment_with_text_error').html('').hide();
        
    $('#dialog_payment_with_wait').show();
    $('#dialog_payment_with_start').hide();
    $('#dialog_payment_with_cancel').hide();
    $('#dialog_payment_with_close').hide();
    $('#dialog_payment_with_tdpb').show();
    
    
    mypage=window.location.pathname;
    ftp_pos_running=true;
    
    datasend='';
    if (mypage=='/my/admin-pos-run.php') {
      datasend+='&gks_session_pos=' + encodeURIComponent($.base64.encode(gks_session_pos));
      datasend+='&gks_erp_cookie_id=' + encodeURIComponent($.base64.encode(gks_getCookie('gks_erp_cookie_id')));
    }    
    datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
    datasend+='&transaction_type=' + encodeURIComponent($.base64.encode(dialog_payment_with.gks_transaction_type));
    datasend+='&doc_id=' + dialog_payment_with.gks_doc_id;
    datasend+='&prev_eftpos_id=' + dialog_payment_with.gks_prev_eftpos_id;
    datasend+='&pp_type=' + encodeURIComponent($.base64.encode(dialog_payment_with.gks_pp_type));
    datasend+='&asset_id=' + dialog_payment_with.gks_asset_id;
    datasend+='&asset_title=' + encodeURIComponent($.base64.encode(dialog_payment_with.gks_asset_title));
    datasend+='&pp=' + dialog_payment_with.gks_pp;
    datasend+='&pp_price=' + dialog_payment_with.gks_pp_price;
    datasend+='&id_acc_xxx_payment=' + dialog_payment_with.gks_id_acc_xxx_payment;
    
    gks_worldline_implementation='';
    if (mypage=='/my/admin-pos-run.php') {
      datasend+='&id_payment_acquirer=' + dialog_payment_with.gks_id_payment_acquirer;
    
      if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
        if ( dialog_payment_with.gks_pawid==6) {//worldfline
          temp=fnc_gks_erp_app_mobile_get_worldline_terminal_data();
          if (temp!='') {
            tempo=JSON.parse(temp);
            if (typeof tempo =='object' && typeof tempo.id_asset !== 'undefined') {
              if (tempo.id_asset==dialog_payment_with.gks_asset_id) {
                datasend+='&worldline_implementation=app2app';
                gks_worldline_implementation='app2app';
              }
            }
          }
        }
      }
    }   


    tipAmount=parseFloat($('#dialog_payment_with_tip_val').val()); if (isNaN(tipAmount)) tipAmount=0;
    installments=parseInt($('#dialog_payment_with_doseis_val').val()); if (isNaN(installments)) installments=0;
    refund_val=parseFloat($('#dialog_payment_with_refund_val').val()); if (isNaN(refund_val)) refund_val=0;
    dialog_payment_with.gks_refund_val=refund_val; 
     
    datasend+='&tipAmount=' + tipAmount;
    datasend+='&installments=' + installments;
    datasend+='&refund_val=' + refund_val;
  
    if ($('#dialog_payment_with_ppm_radio_tap').is(':checked')) {
      datasend+='&preferred_payment_method=tap';
    } else if ($('#dialog_payment_with_ppm_radio_iris').is(':checked')) {
      datasend+='&preferred_payment_method=iris';
    } else {
      datasend+='&preferred_payment_method=tap';
    }

    $('#dialog_payment_with_tip_val').prop('disabled',true);
    $('#dialog_payment_with_tip_pososto').prop('disabled',true);
    $('#dialog_payment_with_doseis_val').prop('disabled',true);
    $('#dialog_payment_with_refund_val').prop('disabled',true);


    eftpos_sessionId='';
    eftpos_id_eftpos_transaction=0;
    //console.log(datasend);

    $('body').addClass('myloading');
    $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
      url: 'admin-eftpos-transaction-dialog-start.php',
      type: 'POST',
			cache: false,
			dataType: "json",
      data: datasend,
      gks_worldline_implementation_this:gks_worldline_implementation,
      error : function(jqXHR ,textStatus,  errorThrown) {
  			$('body').removeClass('myloading');
  			if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
  			myalert('error:' + myerror_show);
        $('#dialog_payment_with_wait').hide();
        $('#dialog_payment_with_start').show();
        $('#dialog_payment_with_cancel').show();
        $('#dialog_payment_with_close').hide();
        $('#dialog_payment_with_tdpb').hide();

        $('#dialog_payment_with_tip_val').prop('disabled',false);
        $('#dialog_payment_with_tip_pososto').prop('disabled',false);
        $('#dialog_payment_with_doseis_val').prop('disabled',false);
        $('#dialog_payment_with_refund_val').prop('disabled',false);

            
        if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
        ftp_pos_running=false;
  		},
      success: function( data ) {
        $('body').removeClass('myloading');
        if (data.success == true) {
          //console.log(data);
          eftpos_sessionId=data.data.sessionId;
          eftpos_id_eftpos_transaction=data.data.id_eftpos_transaction;
          if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
          eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
          $('#dialog_payment_with_text2').html(gks_lang('Αναμονή πληρωμής από το τερματικό')+'...').show();
          if (data.data.id_payment_acquirer_with==1 || data.data.id_payment_acquirer_with==6) {
            $('#dialog_payment_with_delete').show();
          }
          if (data.data.id_payment_acquirer_with==1) {
            if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
              fnc_gks_erp_app_mobile_viva_foreground();
            }
          }
          if (data.data.id_payment_acquirer_with==6) {
            
            if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
              if (this.gks_worldline_implementation_this=='app2app') {
                ssres=fnc_gks_erp_app_mobile_set_worldline_terminal_token(data.data.worldline_app2app_token);
                if (ssres!='OK') {
                  myalert('error:' + ssres);
                }
              } else {
                fnc_gks_erp_app_mobile_worldline_foreground();
              }
            }
          }
          
        } else {
          myalert('error:' + $.base64.decode(data.message));
          $('#dialog_payment_with_wait').hide();
          $('#dialog_payment_with_start').show();
          $('#dialog_payment_with_cancel').show();
          $('#dialog_payment_with_close').hide();
          $('#dialog_payment_with_tdpb').hide();

          $('#dialog_payment_with_tip_val').prop('disabled',false);
          $('#dialog_payment_with_tip_pososto').prop('disabled',false);
          $('#dialog_payment_with_doseis_val').prop('disabled',false);
          $('#dialog_payment_with_refund_val').prop('disabled',false);
          
          if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
          ftp_pos_running=false;	
        }
      }
    });
    
  }
  var eftpos_sessionId='';
  var eftpos_id_eftpos_transaction=0;
  var eftpos_timer_obj=null;
  var prev_data_data_status='';
  
  function eftpos_timer_fnc() {
    //console.log('timer_eftpos',eftpos_sessionId,eftpos_id_eftpos_transaction);
    
    datasend='';
    datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
    datasend+='&transaction_type=' + encodeURIComponent($.base64.encode(dialog_payment_with.gks_transaction_type));
    datasend+='&doc_id=' + dialog_payment_with.gks_doc_id;
    datasend+='&sessionId=' + encodeURIComponent($.base64.encode(eftpos_sessionId));
    datasend+='&id_eftpos_transaction=' + eftpos_id_eftpos_transaction;
    

    
    $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
      url: 'admin-eftpos-transaction-dialog-timer.php',
      type: 'POST',
			cache: false,
			dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        //deleteme
        //console.log(jqXHR.responseText);
        //eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
        //return;
        if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
  			//myalert('error:' + myerror_show);
  			
        $('#dialog_payment_with_text_error').html(gks_lang('Σφάλμα')+': ' + myerror_show).show();

        if (prev_data_data_status=='wait' ||
            prev_data_data_status=='request' ||
            prev_data_data_status=='processed') {
          eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
        }
  		},
      success: function( data ) {
        //deleteme
        //console.log(data);
        //eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
        //return;
        prev_data_data_status='';
        
        $('#dialog_payment_with_text_error').hide();
        if (data.success == true) {
          //console.log(data);
          if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
          if (data.data.status=='wait') {
            prev_data_data_status='wait';
            eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
          } else if (data.data.status=='request') {
            prev_data_data_status='request';
            $('#dialog_payment_with_text3').html(gks_lang('Σε αίτηση')+'...').show();
            eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
          } else if (data.data.status=='processed') {
            prev_data_data_status='processed';
            $('#dialog_payment_with_text4').html(gks_lang('Ξεκίνησε')+'...').show();
            eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
          } else if (data.data.status=='canceled') {
            $('#dialog_payment_with_delete').hide();
            $('#dialog_payment_with_text_error').html(gks_lang('Ακυρώθηκε από τον χρήστη')).show();
            $('#dialog_payment_with_wait').hide();
            $('#dialog_payment_with_start').show();
            $('#dialog_payment_with_cancel').show();
            $('#dialog_payment_with_close').hide();
            $('#dialog_payment_with_tdpb').hide();
            
            $('#dialog_payment_with_tip_val').prop('disabled',false);
            $('#dialog_payment_with_tip_pososto').prop('disabled',false);
            $('#dialog_payment_with_doseis_val').prop('disabled',false);
            $('#dialog_payment_with_refund_val').prop('disabled',false); 
                        
            eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;
            gks_erp_app_mobile_bringtofront('canceled');
          } else if (data.data.status=='abort') {
            $('#dialog_payment_with_delete').hide();
            $('#dialog_payment_with_text_error').html(gks_lang('Ματαιώθηκε η συναλλαγή')+'<br>'+$.base64.decode(data.message)).show();
            $('#dialog_payment_with_wait').hide();
            $('#dialog_payment_with_start').show();
            $('#dialog_payment_with_cancel').show();
            $('#dialog_payment_with_close').hide();
            $('#dialog_payment_with_tdpb').hide();

            $('#dialog_payment_with_tip_val').prop('disabled',false);
            $('#dialog_payment_with_tip_pososto').prop('disabled',false);
            $('#dialog_payment_with_doseis_val').prop('disabled',false);
            $('#dialog_payment_with_refund_val').prop('disabled',false); 
            
            eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;   
            gks_erp_app_mobile_bringtofront('abort');
              
          } else if (data.data.status=='done') {
            
            //eftpos_timer_obj=setTimeout(eftpos_timer_fnc,2000);
            //return;
            
            $('#dialog_payment_with_delete').hide();
            $('#dialog_payment_with_text5').html(gks_lang('Επιτυχής συναλλαγή !')).show();
            $('#dialog_payment_with_wait').hide();
            $('#dialog_payment_with_close').show();
            $('#dialog_payment_with_tdpb').hide();
            
            
            ftp_pos_running=false;
            mypage=window.location.pathname;
            if (mypage=='/my/admin-acc-inv-item.php' || mypage=='/my/admin-pos-run.php') {
  
              if ($('#radio_payment_type_one').is(':checked') || mypage=='/my/admin-pos-run.php') { //apo to one
                temp = ['refund','refunderp','refunderpfree','refundfree','fullvoid','fullvoiderp'];
                if (temp.includes(dialog_payment_with.gks_transaction_type)) {
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .div_payment_type_multi_item_row2_text').addClass('div_payment_type_multi_item_has_' + dialog_payment_with.gks_transaction_type);
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + ']').css('display','block');
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .div_payment_type_multi_item_row2_text').after('<div class="refundetc div_payment_type_multi_item_is_' + dialog_payment_with.gks_transaction_type + '" style="margin-top:10px;">' + data.data.transaction.html + '</div>');
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .refundetc .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .refundetc .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                } else {
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + ']').html('<div class="div_payment_type_multi_item_row2_text">'+data.data.transaction.html+'</div>');
                  $('input[name=radio_payment_way]').prop('disabled', true);
                  $('input[name=radio_payment_type_one_multi]').prop('disabled', true);
                  
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + '] .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                }
                
                if (mypage=='/my/admin-pos-run.php') {
                  $('.gks_pos_panel_pay_btn').each(function() {
                    one_pway=$(this).attr('data-id');
                    if (isNaN(one_pway)) one_pway=0;
                    if (one_pway!=data.data.transaction.id_payment_acquirer) {
                      $(this).parent().parent().remove();
                      
                    }
                  });  
                  $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + ']').parent().find('.gks_pos_panel_pay_btn').html(gks_lang('Αποστολή myData/Πάροχο'));
                  dialog_payment_with.dialog('close');
                  fnc_gks_erp_app_mobile_pos_dialog_card_open(false);
                  
                  //setTimeout(function() {
                    gks_eftpos_payment_ok=true;
                    if (gks_pos_settings.min_clicks==true) {
                      $('.div_payment_one_terminal[data-one_pway=' + data.data.transaction.id_payment_acquirer + ']').parent().find('.gks_pos_panel_pay_btn').click();
                    }
                    
                  //},500);
                  
                }
  
              } else { //apo to multi
                
                temp = ['refund','refunderp','refunderpfree','refundfree','fullvoid','fullvoiderp'];
                if (temp.includes(dialog_payment_with.gks_transaction_type)) {
                  //prepei na ginei eisagogei neas gramis
                  
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').addClass('div_payment_type_multi_item_has_' + dialog_payment_with.gks_transaction_type);
                  
                  max_pp=0;
                  $('.div_payment_type_multi_item').each(function() {
                    cpp=parseInt($(this).attr('data-pp'));if (isNaN(cpp)) cpp=0;
                    if (cpp>max_pp) max_pp=cpp;
                  });
                  curr_pp=max_pp+1;
                  
                  set_new_price=-dialog_payment_with.gks_pp_price;
                  if (['refund','refunderp','refunderpfree','refundfree'].includes(dialog_payment_with.gks_transaction_type)) {
                    set_new_price=-dialog_payment_with.gks_refund_val;
                  }
                  
                  pp_options='';
                  pay_html=
                  '<div data-pp="' + curr_pp + '" data-rec_id="' + data.data.transaction.id_acc_xxx_payment + '" class="div_payment_type_multi_item div_payment_type_multi_item_is_' + dialog_payment_with.gks_transaction_type + '">' +
                    '<div class="div_payment_type_multi_item_row1">' +
                      '<select data-pp="' + curr_pp + '" class="div_payment_type_multi_item_select form-control form-control-sm" disabled>' +
                        '<option value="' + data.data.transaction.id_payment_acquirer + '">' + data.data.transaction.payment_acquirer_name + '</option>' +
                      '</select>' +
                      '<input data-pp="' + curr_pp + '" value="' + set_new_price + '" class="div_payment_type_multi_item_input form-control form-control-sm" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" disabled>' +
                      '<div class="div_payment_type_multi_item_icons">' +
                        '<i data-pp="' + curr_pp + '" class="payment_type_multi_add fas fa-plus-circle"></i>' +
                      '</div>' +
                    '</div>' +
                    '<div class="div_payment_type_multi_item_row2">' +
                      '<div class="div_payment_type_multi_item_row2_text">' + 
                        '<div>'+data.data.transaction.html+'</div>' +
                      '</div>' + 
                      '<span class="div_payment_type_multi_item_pos_terminal" data-asset_id="' + data.data.transaction.asset_id + '" style="display:none;"/>' +
                      
                    '</div>' +
                  '</div>';
                      
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').after(pay_html);    
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .payment_type_multi_add').click(payment_type_multi_add_click);
  
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                  div_payment_type_multi_item_input_change();
                  
                } else {//p.x. sale 
                  
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2').html('<div>'+data.data.transaction.html+'</div>');
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_select').prop('disabled', true);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_input').prop('disabled', true);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .payment_type_multi_del').remove();
    
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                  
                  
                }
                
              }            
              
            } else if (mypage=='/my/admin-acc-pay-item.php') {

                temp = ['refund','refunderp','refunderpfree','refundfree','fullvoid','fullvoiderp'];
                if (temp.includes(dialog_payment_with.gks_transaction_type)) {
                  //prepei na ginei eisagogei neas gramis
                  
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').addClass('div_payment_type_multi_item_has_' + dialog_payment_with.gks_transaction_type);
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').find('button.div_payment_type_multi_item_pos_start').prop('disabled', true);
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').find('input.div_payment_type_multi_item_pos_terminal').prop('disabled', true);
                  
                  
                  
                  max_pp=0;
                  $('.div_payment_type_multi_item').each(function() {
                    cpp=parseInt($(this).attr('data-pp'));if (isNaN(cpp)) cpp=0;
                    if (cpp>max_pp) max_pp=cpp;
                  });
                  curr_pp=max_pp+1;
                  
                  set_new_price=-dialog_payment_with.gks_pp_price;
                  if (['refund','refunderp','refunderpfree','refundfree'].includes(dialog_payment_with.gks_transaction_type)) {
                    set_new_price=-dialog_payment_with.gks_refund_val;
                  }
                  
                  pp_options='';
                  pay_html=
                  '<div data-pp="' + curr_pp + '" data-rec_id="' + data.data.transaction.id_acc_xxx_payment + '" class="div_payment_type_multi_item div_payment_type_multi_item_is_' + dialog_payment_with.gks_transaction_type + '">' +
//                    '<div class="div_payment_type_multi_item_row1">' +
//                      '<select data-pp="' + curr_pp + '" class="div_payment_type_multi_item_select form-control form-control-sm" disabled>' +
//                        '<option value="' + data.data.transaction.id_payment_acquirer + '">' + data.data.transaction.payment_acquirer_name + '</option>' +
//                      '</select>' +
//                      '<input data-pp="' + curr_pp + '" value="' + set_new_price + '" class="div_payment_type_multi_item_input form-control form-control-sm" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" disabled>' +
//                      '<div class="div_payment_type_multi_item_icons">' +
//                        '<i data-pp="' + curr_pp + '" class="payment_type_multi_add fas fa-plus-circle"></i>' +
//                      '</div>' +
//                    '</div>' +
                    '<div class="div_payment_type_multi_item_row2">' +
                      '<div class="div_payment_type_multi_item_row2_text">' + 
                        '<div>'+data.data.transaction.html+'</div>' +
                      '</div>' + 
                      '<span class="div_payment_type_multi_item_pos_terminal" data-asset_id="' + data.data.transaction.asset_id + '" style="display:none;"/>' +
                      
                    '</div>' +
                  '</div>';
                      
                  $('.div_payment_type_multi_item[data-rec_id=' + dialog_payment_with.gks_id_acc_xxx_payment + ']').after(pay_html);    
                  //$('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .payment_type_multi_add').click(payment_type_multi_add_click);
  
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                  //div_payment_type_multi_item_input_change();
                  
                  if (typeof data.data.new_sum_row_id!=='undefined' && typeof data.data.new_sum_poso!=='undefined') {
                    poso_elem=$('.gks_eidos[data-recid=' + data.data.new_sum_row_id + '] input.div_payment_type_multi_item_input');
                    if (poso_elem.length==1) {
                      poso_val=parseFloat(poso_elem.val());
                      if (poso_val!=data.data.new_sum_poso) {
                        poso_elem.val(data.data.new_sum_poso);
                        poso_elem.parent().find('span').html(data.data.new_sum_poso_html + ' <img src="/my/img/warning.gif" style="width:16px;">').addClass('gks_need_save_has_change');
                        //calc_pliroteo();
                        need_save=true;  
                      }
                      
                    }
                    
                    
                  }
                  
                } else {//p.x. sale 
                  
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2').html('<div>'+data.data.transaction.html+'</div>');
                  //$('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_select').prop('disabled', true);
                  //$('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_input').prop('disabled', true);
                  //$('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .payment_type_multi_del').remove();
    
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
                  $('.div_payment_type_multi_item[data-rec_id=' + data.data.transaction.id_acc_xxx_payment + '] .div_payment_type_multi_item_row2 .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);
                  
                  
                }              
              
            } else if (mypage=='/my/admin-eftpos-transaction.php') {
              temp = ['refund','refunderp','refunderpfree','refundfree','fullvoid','fullvoiderp'];
              if (temp.includes(dialog_payment_with.gks_transaction_type)) {
                  $('tr.eftpos_tr[data-id=' + contextMenu_id_eftpos_transaction + ']').addClass('div_payment_type_multi_item_has_' + dialog_payment_with.gks_transaction_type);
                
                
              } else {
                
              }
              
              
            }
            
            
            eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;
            gks_erp_app_mobile_bringtofront('done');
          } else {
            myalert('error:' + data.data.status);
            $('#dialog_payment_with_delete').hide();
            $('#dialog_payment_with_text_error').html(data.data.status).show();
            
            $('#dialog_payment_with_tip_val').prop('disabled',false);
            $('#dialog_payment_with_tip_pososto').prop('disabled',false);
            $('#dialog_payment_with_doseis_val').prop('disabled',false);
            $('#dialog_payment_with_refund_val').prop('disabled',false);            
            
            eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;
          }
          
          
        } else {
          myalert('error:' + $.base64.decode(data.message));
          $('#dialog_payment_with_wait').hide();
          $('#dialog_payment_with_start').show();
          $('#dialog_payment_with_cancel').show();
          $('#dialog_payment_with_close').hide();
          $('#dialog_payment_with_tdpb').hide();
          $('#dialog_payment_with_delete').hide();
          $('#dialog_payment_with_text_error').html(gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message)).show();
        
          $('#dialog_payment_with_tip_val').prop('disabled',false);
          $('#dialog_payment_with_tip_pososto').prop('disabled',false);
          $('#dialog_payment_with_doseis_val').prop('disabled',false);
          $('#dialog_payment_with_refund_val').prop('disabled',false);  
                  
          if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
          ftp_pos_running=false; eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;
          
        }
      }
    });    
    
    
  }
  
  $('#dialog_payment_with_delete_run').click(function() {
    if (eftpos_sessionId=='') return;
    if (eftpos_id_eftpos_transaction==0) return;
    //console.log('delete_run',eftpos_sessionId);

    datasend='';
    datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
    datasend+='&transaction_type=' + encodeURIComponent($.base64.encode(dialog_payment_with.gks_transaction_type));
    datasend+='&doc_id=' + dialog_payment_with.gks_doc_id;
    datasend+='&sessionId=' + encodeURIComponent($.base64.encode(eftpos_sessionId));
    datasend+='&id_eftpos_transaction=' + eftpos_id_eftpos_transaction;
    $('body').addClass('myloading');
    $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
      url: 'admin-eftpos-transaction-dialog-abort.php',
      type: 'POST',
			cache: false,
			dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
  			$('body').removeClass('myloading');
        if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
  			myalert('error:' + myerror_show);
  			
  		},
      success: function( data ) {
        $('body').removeClass('myloading');
        if (data.success == true) {
          //console.log(data);
          myalert('ok:'+
          gks_lang('Η αίτηση ακύρωσης έχει σταλεί επιτυχώς')+'<br>'+
          gks_lang('Μεταβείτε στο [1], εάν δεν γίνει αυτόματα η μετάβαση, για να ακυρωθεί πραγματικά').replaceAll('[1]',data.pawid_descr)+'<br>'+
          gks_lang('Εάν σε 5-10 δευτερόλεπτα δεν ανταποκριθεί το [1] τότε ξαναπατήστε το κουμπί <b>Ακύρωση</b>').replaceAll('[1]',data.pawid_descr));
          //$('#dialog_payment_with_cancel').show();
          //$('#dialog_payment_with_wait').hide();
          
          if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
            if (data.pawid==1) {
              fnc_gks_erp_app_mobile_viva_foreground();
            } else if (data.pawid==6) {
              fnc_gks_erp_app_mobile_worldline_foreground();
            }
          }
                      
        } else {
          //{"detail":"That session was already marked for abort process"}
          if ($.base64.decode(data.message).includes('for abort process')) {
            if (eftpos_timer_obj!=null) clearTimeout(eftpos_timer_obj);
            $('#dialog_payment_with_delete').hide();
            $('#dialog_payment_with_text_error').html(gks_lang('Ματαιώθηκε η συναλλαγή')+'<br>'+$.base64.decode(data.message)).show();
            $('#dialog_payment_with_wait').hide();
            $('#dialog_payment_with_start').show();
            $('#dialog_payment_with_cancel').show();
            $('#dialog_payment_with_close').hide();
            $('#dialog_payment_with_tdpb').hide();

            $('#dialog_payment_with_tip_val').prop('disabled',false);
            $('#dialog_payment_with_tip_pososto').prop('disabled',false);
            $('#dialog_payment_with_doseis_val').prop('disabled',false);
            $('#dialog_payment_with_refund_val').prop('disabled',false); 
           
            eftpos_sessionId=''; eftpos_id_eftpos_transaction=0;   
            gks_erp_app_mobile_bringtofront('abort');            
            

            
                  
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      }
    });    
    
  });
  

	gks_eftpos_transaction_actions_contextMenu_config={
		//event: 'contextmenu',
		event: 'click',
    position: {
        my: 'left top',
        at: 'right+10 top-15', 
        children: '.context-click-subitem'
    },	
    		
    items: function(e) {
  		var arr = [];
  		arr.push({type: 'item', text: 'Transaction Details', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'transactiondetails',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Full Void', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'fullvoid',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Full Void ERP', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'fullvoiderp',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Refund', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'refund',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Refund ERP', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'refunderp',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Pre Auth Completion', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'preauthcompletion',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'Pre Auth Completion ERP', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'preauthcompletionerp',0,contextMenu_id_eftpos_transaction,false);
  		}});
  		arr.push({type: 'item', text: 'One Tap Preauth Completion', disabled: false, click: function(e){	
  		  e.preventDefault();  
				div_payment_type_multi_item_pos_start_click(e,'preauthonetapcompletion',0,contextMenu_id_eftpos_transaction,false);
  		}});

      return arr;
    }
	};  
  
	var contextMenu_eftpos_transaction_actions_prev_elem=null;
  window.gks_eftpos_transaction_actions_click=function(e) {
	  event.stopPropagation();
    contextMenu_id_eftpos_transaction = parseInt($(this).attr('data-id'));
    if (isNaN(contextMenu_id_eftpos_transaction)) contextMenu_id_eftpos_transaction=0;
    //oldstate = $(this).attr('data-id'); 
    //console.log(id_eftpos_transaction);
    //$('#contextmenu').hide();
    if (contextMenu_eftpos_transaction_actions_prev_elem!=null) {
      contextMenu_eftpos_transaction_actions_prev_elem.contextMenu('hide',e);  
    }
	  $(this).contextMenu('show',e);  
	  contextMenu_eftpos_transaction_actions_prev_elem=$(this);      
  }
  

	var contextMenu_id_eftpos_transaction=0;
  
  $('.gks_eftpos_transaction_actions, .gks_payment_next_actions').contextMenu(gks_eftpos_transaction_actions_contextMenu_config);
  $('.gks_eftpos_transaction_actions, .gks_payment_next_actions').click(gks_eftpos_transaction_actions_click);



  window.gks_div_payment_type_multi_item_pos_terminal_autocomplete=function(myelem) {
    myelem.autocomplete({
  
  
      source: function(request, response) {
        myelem=$(this)[0];
        myelem=myelem.element;
        
        //pp=myelem.attr('data-pp');
        pawid=myelem.attr('data-pawid');       
        if (isNaN(pawid)) pawid=0; 
        //console.log(pawid);
        
        mydata={
          term: request.term,
          ot:1,
          pawid: pawid,
          from_pos: 1
        };
        $.ajax({
          timeout: 3000, // sets timeout to 3 seconds
          url: 'admin-autocomplete-asset.php',
          dataType: "json",
          cache: false,
          data: mydata,
          error : function(jqXHR ,textStatus,  errorThrown) {
            if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
    			    myerror_show=jqXHR.responseText
    			  } else {
    			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
    			  }
      			myalert('error:' + myerror_show);
  			    				
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
      minLength: 0,
      delay: 300, //default
      autoFocus: true,
      select: function( event, ui ) {
        $(this).attr('data-asset_id',ui.item.id);
      },
      change: function (event, ui) {
        if(!ui.item){
          $(this).attr('data-asset_id','0').val('');
        }
      }
    });
  }    
  
  $('.div_payment_type_multi_item_pos_terminal, .div_payment_one_terminal_terminal').each(function() {
    if (from_php_perm_ret_edit==false) return;
    gks_div_payment_type_multi_item_pos_terminal_autocomplete($(this));
  });
    
    
  
  if ($('#gks_pos_panel_pay_data').length==1) {
    gks_pos_panel_pay_data_html=$('#gks_pos_panel_pay_data').html();
    
  }  
  
  
  window.gks_pos_menu_reset_click_otherjs=function() {
    
    $('.div_payment_one_terminal_start').click(function() {
      div_payment_type_multi_item_pos_start_click($(this),'sale',from_php_id,0,false);
    });
    $('.div_payment_one_terminal_terminal').each(function() {
      if (from_php_perm_ret_edit==false) return;
      gks_div_payment_type_multi_item_pos_terminal_autocomplete($(this));
    });
          
  }
  
  
  function gks_erp_app_mobile_bringtofront(ret_status) {
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
      if (ret_status=='done' && gks_worldline_implementation=='app2app') {
        gks_erp_app_mobile.showToast('EFT/POS Wordline ' + ret_status);
      } else {
        gks_erp_app_mobile.eft_pos_end(ret_status);
      }
    }
    
    myplay=true;
    if (typeof gks_pos_settings !== 'undefined' && typeof gks_pos_settings.audio !== 'undefined' && gks_pos_settings.audio==false) {
      myplay=false;
    }
      
    if (myplay && ret_status!='done') {
      setTimeout(function(ret_status_c,from_php_gks_cache_version_c) {
        ppp = new Audio('/my/audio/eft-pos-' + ret_status_c + '.mp3?v=' + from_php_gks_cache_version_c);
        ppp.play();
      },500,ret_status,from_php_gks_cache_version);
      
    }
  }
  
  
});

function fnc_gks_erp_app_mobile_pos_dialog_card_open(myval) {
  gks_and_app_pos_dialog_card_open=myval;
  if (typeof gks_erp_app_mobile === 'undefined') return '';
  return gks_erp_app_mobile.pos_dialog_card_open(myval);
}
