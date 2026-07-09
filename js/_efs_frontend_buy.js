/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



var gks_dialog_message;

jQuery3(document).ready(function($) {

  $.base64.utf8encode = true;
  $.base64.utf8decode = true;
   
  $.datetimepicker.setLocale(from_php_gks_datetimepicker_locale);
  
  
  function gks_formatMoney(n, c, d, t){
    
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
  }

  
  gks_dialog_message = $('#gks_dialog_message').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "from_php_lang_OK": {
        text:from_php_lang_OK,
        click: function() {
          $(this).dialog( "close" );
        }
      }
    }
  });  

  function gks_myalert(mymessage) {
    $("#gks_dialog_message_ok").hide();
    $("#gks_dialog_message_error").hide();
    if (mymessage.substring(0, 6) == 'error:') {
       $("#gks_dialog_message_error").show();
       mymessage=mymessage.substring(6);
    }
    if (mymessage.substring(0, 3) == 'ok:') {
       $("#gks_dialog_message_ok").show();
       mymessage=mymessage.substring(3);
    }
    $("#gks_dialog_message_message").html(mymessage);
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 550) dwidth=550;
    if (dheight> 500) dheight=500;
    gks_dialog_message.dialog('option', 'width', dwidth);
    gks_dialog_message.dialog('option', 'height', dheight);
    $('#gks_dialog_message').parent().css({position:'fixed'});      
    gks_dialog_message.dialog('open');
  }; 
    
  $('#gks_button_buy').click(function() {

    
    gks_email = $('#gks_email').val().trim();
    if (gks_email=='') {
      gks_myalert('error:' + from_php_lang_Pleasetypeyouremail);
      return;      
    }
    if (!validateEmail(gks_email)) {
      gks_myalert('error:' + from_php_lang_Theemailyouenteredisinvalid);
      return;       
    }
    
    gks_quantity=1;//parseInt($('#gks_quantity').val());
//    if (isNaN(gks_quantity)) gks_quantity=0;
//    if (gks_quantity<=0) {
//      gks_myalert('error:' + from_php_lang_Enterthequantity);
//      return;
//    }
    
    mybasketdata='&guid=';
    mybasketdata+='&gks_email=' + encodeURIComponent($.base64.encode(gks_email));
    mybasketdata+='&ids=';
    mybasketdata+='&pid_10001=' + gks_quantity;

    add_this_to_basket(mybasketdata);    
  });
  
  function calc_total() {
    
    quantity=parseInt($('#gks_quantity').val());
    if (isNaN(quantity)) quantity=0;
    total=quantity*49;
    if (quantity == 1) total=49;
    else if (quantity>1) total=49 + (quantity-1)*5;
    
    
    $('#gkd_total').html(total.formatMoney(2,'.',',') + ' &euro;');
  }
  //calc_total();

  $('#gks_quantity').on('input propertychange change', function(event) {
    calc_total();
  });

  function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }       
  
  
  function add_this_to_basket(mybasketdata) {

    photoSelectedGUID='';
    

    $('body').addClass('gks_myloading');
    $.ajax({
			url: '/my/basket-add.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: mybasketdata,
			sendguid: photoSelectedGUID,
			error : function(jqXHR ,textStatus,  errorThrown) {
				gks_myalert('error:' + jqXHR.responseText);
				$('body').removeClass('gks_myloading');
			},				
			success: function(data) {
				//console.log(data);
				if (!data) {
			    $('body').removeClass('gks_myloading');
					gks_myalert('error:' + from_php_lang_ErrorPleasetryagainlater);
				} else {
					if (data.success == true) {
            window.location.href = '/basket' + from_php_gks_set_lang_url;
					} else {
  			    $('body').removeClass('gks_myloading');
						gks_myalert('error:' + $.base64.decode(data.message));
					}
					
				}
			}
			
		});   
    
  }         
});

