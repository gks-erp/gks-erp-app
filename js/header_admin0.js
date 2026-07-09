/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


includeHTML_header();  
function includeHTML_header() {
  return;
  var z, i, elmnt, file, xhttp;
  z = document.getElementsByTagName("DIV");
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    file = elmnt.getAttribute('w3_include_my_header_admin');
    if (file) {
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {
            elmnt.outerHTML = this.responseText;
            includeHTML_header_end();
          }
          if (this.status == 404) {elmnt.outerHTML = "Page not found.";}
        }
      }      
      xhttp.open("GET", file, true);
      xhttp.send();
      return;
    }
  }
};


function includeHTML_header_end() {
  var script = document.createElement('script');
  script.src = '/my/js/header_admin1.js?v=' + from_php_gks_cache_version;
  document.getElementsByTagName('head')[0].appendChild(script);
}


includeHTML_header_menu();  
function includeHTML_header_menu() {
  //console.log('includeHTML_header_menu');
  var z, i, elmnt, file, xhttp;
  z = document.getElementsByTagName("DIV");
  //console.log(z.length);
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    file = elmnt.getAttribute('w3_include_my_header_admin_menu');
    //console.log(file);
    if (file) {
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {
            elmnt.outerHTML = this.responseText;
            includeHTML_header_menu_end();
          }
          if (this.status == 404) {elmnt.outerHTML = '<li class="parent"><a class="parent" href="#"><b>Error</b></a></li>';}
        }
      }      
      xhttp.open("GET", file, true);
      xhttp.send();
      return;
    }
  }
};


function includeHTML_header_menu_end() {
  for (i = 0; i< from_php_gks_main_menu_active.length; i++) {
    $('.' + from_php_gks_main_menu_active[i]).addClass('active');
  }
  
  jQuery(document).ready(function($) {
    
    //if (from_php_header_admin_menu_login) {$('#header_admin_menu_login').show();} else {$('#header_admin_menu_login').hide();}
    //if (from_php_header_admin_menu_asfalisi)  {$('#header_admin_menu_asfalisi').show();} else {$('#header_admin_menu_asfalisi').hide();}
    //if (from_php_header_admin_menu_banktra)  {$('#header_admin_menu_banktra').show();} else {$('#header_admin_menu_banktra').hide();}
    //$('#header_admin_menu_manual').html(from_php_header_admin_menu_manual);
    $('#header_admin_menu_statbots').html(from_php_header_admin_menu_statbots);
    //$('#header_admin_menu_xmpp').html(from_php_header_admin_menu_xmpp);
    //$('#header_admin_menu_toogle_table_rec_log').html(from_php_header_admin_menu_toogle_table_rec_log);
    //$('#header_admin_menu_toogle_def_filter_date').html(from_php_header_admin_menu_toogle_def_filter_date);
    
    
    var gks_header_search_xhr;
    var gks_header_search_text_old='';
    
    function gks_header_search_run(fromresize,elem) {
      if (fromresize && $('#gks_header_search_results').css('display') == 'none') return;
      
      if (fromresize==false) {
        var gks_header_search_text=elem.val().trim();
        if (gks_header_search_text=='') {
          gks_header_search_text_old='';
          $('#gks_header_search_results').hide();
          return;  
        }  
        if (gks_header_search_text_old==gks_header_search_text) return;
        gks_header_search_text_old=gks_header_search_text;
        
        if(gks_header_search_xhr && gks_header_search_xhr.readyState != 4){
          gks_header_search_xhr.abort();
        }
      }

      
      ww=$(window).width();
      wh=$(window).height();
      //console.log('gks_header_search_run',from_php_gks_user_settings_menu_sticky_top,w);
      if (ww<992) {
        srt=$('#gks_nav_session_header').height() - 48;
        srw=ww-60; if (srw>400) srw=400;
        srh=wh-60; if (srh<100) srh=100;
        srl=16;        
        $('#gks_header_search_results').css({position:'absolute',left:srl+'px',right:'unset',top:srt+'px',width:srw+'px',height:srh+'px',});
      } else {
        if (from_php_gks_menu_pos=='left') {
          srw=ww; if (srw>400) srw=400;
          srh=(wh-10);
          $('#gks_header_search_results').css({position:'fixed',left:'160px',right:'unset',top:'0px',width:srw+'px',height:srh+'px',});
        } else {
          if (from_php_gks_user_settings_menu_sticky_top=='1') {
            srr=10;
            srt=$('#gks_nav_session_header').height() + 2;
            srw=ww; if (srw>400) srw=400;
            srh=(wh-srt-10); //if (srh>400) srh=400;
            $('#gks_header_search_results').css({position:'fixed',left:'unset',right:srr+'px',top:srt+'px',width:srw+'px',height:srh+'px',});
          } else {
            srt=$('#gks_nav_session_header').height() + 2;
            srw=ww; if (srw>400) srw=400;
            srh=(wh-srt-10); //if (srh>400) srh=400;
            if (srh<100) srh=100;
            srl=ww-srw-10;
            $('#gks_header_search_results').css({position:'absolute',left:srl+'px',right:'unset',top:srt+'px',width:srw+'px',height:srh+'px',});
          }
        }
      }
      if (fromresize) return;
      
      $('#gks_header_search_results').show();
      $('#gks_header_search_results_hourglass').show();
      
      datasend='term=' + encodeURIComponent($.base64.encode(gks_header_search_text));
      
      gks_header_search_xhr = $.ajax({
  			url: 'admin-header-search-exec.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  			  if (textStatus != 'abort') {
  				  $('#gks_header_search_results_html').html('<div class="alert alert-danger" role="alert" style="text-align:left;">error:' + jqXHR.responseText + '</div>');
  			    $('#gks_header_search_results_hourglass').hide();
  			  }
  			},				
  			success: function(data) {
				  $('#gks_header_search_results_hourglass').hide();
  			  if (data.success) {
  				  $('#gks_header_search_results_html').html($.base64.decode(data.message));
  				} else {
  				  $('#gks_header_search_results_html').html('<div class="alert alert-danger" role="alert" style="text-align:left;">' + $.base64.decode(data.message) + '</div>');
  				}
        }
      });
    }
    
    
    $('.gks_header_search_elem').on('change keyup paste', function() {
      gks_header_search_run(false,$(this));
    });
    
    $(window).resize(function() {
      gks_header_search_run(true,null);
    });
    
    $('#gks_header_search_results').click(function(event) {
      event.stopPropagation();
    });
    
    
    $('body').click(function() {
      $('#gks_header_search_results').hide();
      $('.gks_google_autocomplete_div1').hide();
      //console.log('body click');
    });
    
    
  });

  
  
  
  //var script = document.createElement('script');
  //script.src = '/my/js/header_admin1.js?v=' + from_php_gks_cache_version;
  //document.getElementsByTagName('head')[0].appendChild(script);

  var script = document.createElement('script');
  script.src = '/my/js/_favorites.js?v=' + from_php_gks_cache_version;
  document.getElementsByTagName('head')[0].appendChild(script);

  var script = document.createElement('script');
  script.src = '/my/js/_notification.js?v=' + from_php_gks_cache_version;
  document.getElementsByTagName('head')[0].appendChild(script);

  var script = document.createElement('script');
  script.src = '/my/js/sub-menu.js?v=' + from_php_gks_cache_version;
  document.getElementsByTagName('head')[0].appendChild(script);

  
  $('#gks_narrow_icon').click(function() {
    if ($('gks_nav_parent').hasClass('gks_menu_narrow')) {
      $('gks_nav_parent').removeClass('gks_menu_narrow').addClass('gks_menu_nonarrow');
      narrow=0;
    } else {
      $('gks_nav_parent').removeClass('gks_menu_nonarrow').addClass('gks_menu_narrow');
      narrow=1;
    }  
    
    datasend='narrow=' + narrow;
    $.ajax({
			url: 'admin-users-menu-narrow.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
        //console.log(jqXHR.responseText);
        myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
        if (data.success == true) {
          
        } else {
          myalert('error:' + $.base64.decode(data.message));
        }
      }
    });    
  });

  
  $('.tooltipstermenu').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false, delay:0, animationDuration:0,position:(from_php_gks_menu_pos=='' ? ['bottom'] : ['right'])});

}