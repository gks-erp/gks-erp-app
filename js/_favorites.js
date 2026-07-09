/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

jQuery(document).ready(function($) {

  var dialog_favorites_add;
  dialog_favorites_add = $( "#dialog_favorites_add" ).dialog({
    autoOpen: false,
    width: 500,
    height: 300,
    modal: true,
    buttons: [
      {
        id: 'dialog_favorites_add_ok', 
        html: gks_lang('Προσθήκη'),
        click: function() {
          dialog_favorites_add_title=$('#dialog_favorites_add_title').val().trim();
          if (dialog_favorites_add_title =='') {
            myalert('error:' + gks_lang('Ορίστε το όνομα της σελίδας'));
            return;
          }
          dialog_favorites_add_url=$('#dialog_favorites_add_url').val().trim();
          if (dialog_favorites_add_url =='') {
            myalert('error:' + gks_lang('Ορίστε τον σύνδεσμο της σελίδας'));
            return;
          }
          //$(this).dialog('close');
          
          
          datasend='title=' + encodeURIComponent($.base64.encode(dialog_favorites_add_title));
          datasend+='&url=' + encodeURIComponent($.base64.encode(dialog_favorites_add_url));
          //console.log(datasend);
          //return;
    
          $("body").addClass("myloading");
          $.ajax({
      			url: '/my/admin-favorites-add-exec.php',
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
        					window.location.reload();
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      		});           
        }
      },
      {
        id:'dialog_favorites_add_cancel',
        html: gks_lang('Άκυρο'),
        click:function() {
          $(this).dialog('close');
        }
      },
    ],
    close: function() {
      $('#myfsearch').show();
    },
  });    
  
  $('#favorites_add').click(function() {
    myurl=window.location.href;
    $('#dialog_favorites_add_title').val(document.title);
    $('#dialog_favorites_add_url').val(myurl);
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 600) dwidth=600;
    if (dheight> 350) dheight=350;
    dialog_favorites_add.dialog('option', 'width', dwidth);
    dialog_favorites_add.dialog('option', 'height', dheight);
    $('#dialog_favorites_add').parent().css({position:'fixed'});     
    dialog_favorites_add.dialog('open');      
    
  });
  
  $('#header_search').keypress(function(e) {
    if(e.which == 13) {
      q=header_search
      q=$('#header_search').val();
      if (q=='') {
        myalert('error:' + gks_lang('Πληκτρολογήστε κάποιο όρο για αναζήτηση'));
        return;
      }
      //console.log(document.location);
      if (document.location.pathname =='/my/search.php') {
        document.location.href = '/my/search.php#q=' + encodeURI(q);
        window.location.reload();
      } else {
        document.location.href = '/my/search.php#q=' + encodeURI(q);  
      }
    }
  });
    


});  