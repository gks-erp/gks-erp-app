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

  $('#poi_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
  


  $('#poi_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('poi_nomos_id',v,0);
  });  
  
  if (from_php_id==-1) {
    v=parseInt($('#poi_country_id').val());
    if (isNaN()) v=0;
    if (v>0) nomos_fill('poi_nomos_id',v,0);
  } 

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {


    
    datasend='';
    datasend+='&poi_descr='  + encodeURIComponent($.base64.encode($("#mypostform #poi_descr").val().trim()));
    datasend+='&poi_type_id='  + encodeURIComponent($("#mypostform #poi_type_id").val().trim());
    datasend+='&poi_locode='  + encodeURIComponent($.base64.encode($("#mypostform #poi_locode").val().trim()));
    datasend+='&poi_iata_code='  + encodeURIComponent($.base64.encode($("#mypostform #poi_iata_code").val().trim()));
    datasend+='&poi_icao_code='  + encodeURIComponent($.base64.encode($("#mypostform #poi_icao_code").val().trim()));
    datasend+='&poi_parent_id='  + encodeURIComponent($("#mypostform #poi_parent_id").attr('data-id').trim());
    datasend+='&poi_phone='  + encodeURIComponent($.base64.encode($("#mypostform #poi_phone").val().trim()));
    datasend+='&poi_email='  + encodeURIComponent($.base64.encode($("#mypostform #poi_email").val().trim()));
    datasend+='&poi_website='  + encodeURIComponent($.base64.encode($("#mypostform #poi_website").val().trim()));
    datasend+='&poi_odos='  + encodeURIComponent($.base64.encode($("#mypostform #poi_odos").val().trim()));
    datasend+='&poi_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #poi_arithmos").val().trim()));
    datasend+='&poi_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #poi_orofos").val().trim()));
    datasend+='&poi_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #poi_perioxi").val().trim()));
    datasend+='&poi_poli='  + encodeURIComponent($.base64.encode($("#mypostform #poi_poli").val().trim()));
    datasend+='&poi_tk='  + encodeURIComponent($.base64.encode($("#mypostform #poi_tk").val().trim()));
    datasend+='&poi_country_id='  + encodeURIComponent(($("#mypostform #poi_country_id").val().trim()));
    datasend+='&poi_nomos_id='  + encodeURIComponent(($("#mypostform #poi_nomos_id").val().trim()));
    datasend+='&poi_map_latitude='  + encodeURIComponent(($("#mypostform #poi_map_latitude").val().trim()));
    datasend+='&poi_map_longitude='  + encodeURIComponent(($("#mypostform #poi_map_longitude").val().trim()));
    datasend+='&poi_disable=' + (($('#mypostform #poi_disable').is(':checked')) ? '0':'1');
    datasend+='&poi_color='  + encodeURIComponent($.base64.encode($("#mypostform #poi_color").val().trim()));
    datasend+='&poi_comments='  + encodeURIComponent($.base64.encode($("#mypostform #poi_comments").val().trim()));
    datasend+='&poi_company_id_sub_id=' + encodeURIComponent($.base64.encode($("#poi_company_id_sub_id").val().trim()));
    datasend+='&poi_parastatiko_apodiji_journal_id='  + encodeURIComponent(($("#mypostform #poi_parastatiko_apodiji_journal_id").val().trim()));
    datasend+='&poi_parastatiko_apodiji_seira_id='  + encodeURIComponent(($("#mypostform #poi_parastatiko_apodiji_seira_id").val().trim()));
    datasend+='&poi_parastatiko_timologio_journal_id='  + encodeURIComponent(($("#mypostform #poi_parastatiko_timologio_journal_id").val().trim()));
    datasend+='&poi_parastatiko_timologio_seira_id='  + encodeURIComponent(($("#mypostform #poi_parastatiko_timologio_seira_id").val().trim()));
    
    
    
    //return;
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    if (map_is_open) {    
      myareas={};
      myareas.circles=[];
      myareas.rectangles=[];
      myareas.polygons=[];
      for (i = 0; i < allshapes.length; i++) {
        if (allshapes[i].type=='polygon') {
          item={};
          item.id=allshapes[i].gks_id;
          item.color=allshapes[i].fillColor;
          myarray=allshapes[i].getPath().getArray();
          item.points=[];
          for (j = 0; j < myarray.length; j++) {
            item.points.push({lat: myarray[j].lat(), lng: myarray[j].lng()});
          }
          myareas.polygons.push(item);
          
        } else if (allshapes[i].type=='circle') {
          item={};
          item.id=allshapes[i].gks_id;
          item.color=allshapes[i].fillColor;
          item.center={lat: allshapes[i].getCenter().lat(), lng: allshapes[i].getCenter().lng()} ;
          item.radius=allshapes[i].getRadius(); //se metra, apostasi
          myareas.circles.push(item);
          
        } else if (allshapes[i].type=='rectangle') {
          item={};
          item.id=allshapes[i].gks_id;
          item.color=allshapes[i].fillColor;
          item.corner_left_top={lng: allshapes[i].getBounds().getSouthWest().lng(), lat: allshapes[i].getBounds().getNorthEast().lat(), };
          item.corner_right_bottom={lng: allshapes[i].getBounds().getNorthEast().lng(), lat: allshapes[i].getBounds().getSouthWest().lat()};
          myareas.rectangles.push(item);
        }
      }
      //console.log(myareas);
      //datasend+='&myareas_str=' + encodeURIComponent($.base64.encode(JSON.stringify(myareas)));

      var bounds = new google.maps.LatLngBounds();
      for (i = 0; i < allshapes.length; i++) {
        this_bounds=getBounds(allshapes[i]);
        //console.log(this_bounds);
        bounds.union(this_bounds);
        //console.log(bounds);
      }
      //console.log(bounds);
      datasend+='&myareas_str=' + encodeURIComponent($.base64.encode(JSON.stringify(myareas)));
      if (allshapes.length==0) {
        item={
          north: 0,
          south: 0, 
          east:  0,
          west:  0
        }        
      } else {
        item={
          north: bounds.getNorthEast().lat(),
          south: bounds.getSouthWest().lat(), 
          east:  bounds.getNorthEast().lng(),
          west:  bounds.getSouthWest().lng()
        }
        //console.log(item);  
      }
      datasend+='&bounds_str=' + encodeURIComponent($.base64.encode(JSON.stringify(item)));
      //return;
      
      //console.log(myareas);
      //console.log(item);
      //return;
    } else {
      datasend+='&myareas_str=' + encodeURIComponent($.base64.encode('nochange'));
    }
    
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    
    //console.log(datasend);
    
  
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-poi-item-exec.php?id=' + from_php_id,
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
            if (data.redirect=='') {
              need_save=false;
  					  window.location.reload();
  					} else {
  					  need_save=false;
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


  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
      //$('#map_div').css('height','500px').css('margin-top','10px').show();
      $('#map_div').css('margin-top','10px').show();
      showmap_run();
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map_pos, #geocode_pos').prop('disabled',false);
    } else {
      if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
        $('#map_pos, #geocode_pos').prop('disabled',true);
        $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
        $('#map_div').hide();
      } else {
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map_div').show();
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
      
          
        $('#poi_map_latitude').val(place_map_latitude);
        $('#poi_map_longitude').val(place_map_longitude);
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
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#poi_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#poi_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#poi_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#poi_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#poi_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#poi_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#poi_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#poi_nomos_id").val().trim());
    
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
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#poi_map_latitude' ).val(data.pos.lat);
					  $('#poi_map_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title','GEO:' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  }); 


  



  //settings
  
  

 
  
  

  
 

  function poi_comments_change() {gks_resize_textarea($(this));}
  $('#poi_comments').on(mychange, poi_comments_change);
  gks_resize_textarea($('#poi_comments'));


  gks_address_autocomplete('poi_odos','poi_arithmos','poi_orofos','poi_perioxi','poi_poli','poi_tk','poi_nomos_id','poi_country_id','poi_map_latitude','poi_map_longitude',true);

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  

  $('#transfer_area').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-transfer-area.php',
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
      $("#transfer_area_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#transfer_area").val("");
          $("#transfer_area_id").val("");
        }
    }
  });

  $('#add_transfer_area2poi').click(function(event) {  
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το σημείο')); return;}	  
    datasend='';
    datasend+='poi_id=' + from_php_id;    
    datasend+='&from=poi&id='  + encodeURI($("#transfer_area_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-transfer-area-item-poi-add.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#transfer_area_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#transfer_area_table tbody').html(row_html);
            }
            
            $('.transfer_area_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.transfer_area_tr_new').each(function() {
              $(this).removeClass('transfer_area_tr_new').addClass('transfer_area_tr_exist');
            });
            var transfer_area_aa=0;
            $('#transfer_area_table .transfer_area_aa').each(function () {
              transfer_area_aa++;
              $(this).html(transfer_area_aa);  
            });
  
            $("body").removeClass("myloading");  
            
            $('#transfer_area').val('');
            $('#transfer_area_id').val('');					  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

  window.gks_fnc_transfer_area_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.transfer_area_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var transfer_area_aa=0;
      $('#transfer_area_table .transfer_area_aa').each(function () {
        transfer_area_aa++;
        $(this).html(transfer_area_aa);  
      });    
    });
  }

  $('#poi_map_latitude, #poi_map_longitude').on(mychange,function() {
    lat=parseFloat($('#poi_map_latitude').val());
    lng=parseFloat($('#poi_map_longitude').val());
    gks_this_map_set_pos(lat,lng);
  });
  
  
  $('#poi_type_id').change(function() {
    val=parseInt($(this).val());if (isNaN(val)) val=0;
    if (val==1 || val==3) $('#poi_locode_div').slideDown(); else $('#poi_locode_div').slideUp(); 
    if (val==2 || val==4) $('#poi_iata_code_div').slideDown(); else $('#poi_iata_code_div').slideUp(); 
    if (val==2 || val==4) $('#poi_icao_code_div').slideDown(); else $('#poi_icao_code_div').slideUp(); 
  });
  
  $('#map_fullscreen').click(function() {
  
    elem=$(this).find('span');
    sss=elem.attr('class');
    if (sss=='fa fa-compress') {
      gks_closeFullscreen();
      elem.attr('class','fa fa-expand');
      $('#map_div').removeClass('map_div_full');

      var element = $('#map_div').detach();
      $('#map_div_float').append(element);
      $('#map').css('height','');
      $('body').removeClass('noscroll');
      map.setOptions({
        gestureHandling: 'cooperative'
      });
      $('#gks_nav_session_header').show(); 
    } else {
      gks_openFullscreen();
      elem.attr('class','fa fa-compress');
      $('#map_div').addClass('map_div_full');
      
      var element = $('#map_div').detach();
      $('body').append(element);
      hhh=$('#map_panel').height();
      hhh+=10;
      $('#map').css('height','calc(100% - ' + hhh + 'px)');
      $('body').addClass('noscroll');
      map.setOptions({
        gestureHandling: 'greedy'
      });
      $('#gks_nav_session_header').hide();  
    }  
  });
  
  $('.map_nav_shape').click(function() {
    mydir=$(this).attr('data-dir');
    //console.log(mydir);
    if (allshapes.length==0) return;
    
    cc_cur=-1;
    for (i = 0; i < allshapes.length; i++) {
      if (allshapes[i].getEditable()) {
        cc_cur=i;
        break;
      }
    }
    if (mydir=='f') cc_cur=0;
    else if (mydir=='l') cc_cur=allshapes.length-1;
    else if (mydir=='1') cc_cur++;
    else if (mydir=='-1') cc_cur--;
    if (cc_cur<0) cc_cur=0;
    if (cc_cur>=allshapes.length) cc_cur=allshapes.length-1;
    //console.log(cc_cur);
    for (i = 0; i < allshapes.length; i++) {
      allshapes[i].setEditable(false);
    }
    allshapes[cc_cur].setEditable(true);
    selectedShape=allshapes[cc_cur];

    $('#map_nav_shape_label2').html((cc_cur+1)+ '/' + allshapes.length);
    
    map.fitBounds(getBounds(allshapes[cc_cur])); 
    
  });
  
  var measure_tool_run=false;
  $('#map_measure_tool').click(function() {
    if (measure_tool_run==false) {
      measureTool.start();
      $(this).removeClass('btn-primary').addClass('btn-warning');
      measure_tool_run=true;
    } else {
      measureTool.end();
      $(this).removeClass('btn-warning').addClass('btn-primary');
      measure_tool_run=false;
    }
    
  });
  $('#map_zoom_bounds').click(function() {
    gks_map_fitBounds();
  });
  $('#map_center_point').click(function() {
    var bounds = new google.maps.LatLngBounds();
    for (i = 0; i < allshapes.length; i++) {
      this_bounds=getBounds(allshapes[i]);
      bounds.union(this_bounds);
    }
    //if (bounds.Ha.hi!=-180 || bounds.Ha.lo!=180) {
    if (bounds.isEmpty()==false) {
      centerpoint=bounds.getCenter();
      marker.position=centerpoint;
      document.getElementById('poi_map_latitude').value = centerpoint.lat();
      document.getElementById('poi_map_longitude').value = centerpoint.lng();

      
    }
  });
  


  $('#poi_parent_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-poi.php',
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
      $('#poi_parent_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#poi_parent_id').val('').attr('data-id','0');
      }
    }
  });


  function poi_company_id_sub_id_change() {
    v=$('#poi_company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    if (v=='' || v=='0|0') {
      $('#div_poi_company_id_sub_id').hide();
    } else {
      $('#div_poi_company_id_sub_id').show();
    }
    
    
    filter_journal_seira('apodiji');  
    filter_journal_seira('timologio'); 
  }
  $('#poi_company_id_sub_id').change(poi_company_id_sub_id_change);
  
  function filter_journal_seira(myfor='') {
    var dc =0; var dcs=0;
    v=$('#poi_company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      dc=parseInt(parts[0]); if (isNaN(dc)) dc=0; 
      dcs=parseInt(parts[1]); if (isNaN(dcs)) dcs=0;
    }
    dj=$('#poi_parastatiko_' + myfor + '_journal_id').val();
    $('#poi_parastatiko_' + myfor + '_journal_id option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_c=$(this).attr('data-c');
        data_cs=$(this).attr('data-cs');
        if (data_c!=dc || data_cs!=dcs) $(this).hide(); else $(this).show();
      }
    });
    if ($('#poi_parastatiko_' + myfor + '_journal_id option[value=' + dj + ']').css('display')=='none') $('#poi_parastatiko_' + myfor + '_journal_id').val('0');
    dj=$('#poi_parastatiko_' + myfor + '_journal_id').val();  
    ds=$('#poi_parastatiko_' + myfor + '_seira_id').val();
    $('#poi_parastatiko_' + myfor + '_seira_id option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_j=$(this).attr('data-j');
        if (data_j!=dj) $(this).hide(); else $(this).show();
      }
    });
    if ($('#poi_parastatiko_' + myfor + '_seira_id option[value=' + ds + ']').css('display')=='none') $('#poi_parastatiko_' + myfor + '_seira_id').val('0');
  }

  
  filter_journal_seira('apodiji');  
  filter_journal_seira('timologio'); 
  
  function poi_parastatiko_apodiji_journal_id_change() {filter_journal_seira('apodiji');}
  function poi_parastatiko_timologio_journal_id_change() {filter_journal_seira('timologio');}

  $('#poi_parastatiko_apodiji_journal_id').change(poi_parastatiko_apodiji_journal_id_change); 
  $('#poi_parastatiko_timologio_journal_id').change(poi_parastatiko_timologio_journal_id_change); 
  


    
  
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
var myLatLng;
var measureTool;
var infoWindow_userpos=null;

function initMap() {
  map = new google.maps.Map(
    document.getElementById('map'), 
    {
      center: myLatLng,
      zoom: 17,
      //gestureHandling: 'greedy'
      mapId: "gks1234567890",
    }
  );
  

    
  measureTool = new MeasureTool(map, {
    contextMenu: false,
  	unit: MeasureTool.UnitTypeId.METRIC //IMPERIAL
  });
		  
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title: gks_lang('Σημείο'),
    gmpDraggable: true,
  });
    

  var polyOptions = {
    strokeWeight: 0,
    fillOpacity: 0.45,
    editable: true,
    draggable: true
  };  

  drawingManager = new google.maps.drawing.DrawingManager({
      //drawingMode: google.maps.drawing.OverlayType.POLYGON,
      
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: [
          //google.maps.drawing.OverlayType.MARKER,
          //google.maps.drawing.OverlayType.CIRCLE,
          //google.maps.drawing.OverlayType.RECTANGLE,
          google.maps.drawing.OverlayType.POLYGON,
          //google.maps.drawing.OverlayType.POLYLINE,
        ],
      },                    
      markerOptions: {
        //icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
        draggable: true
      },
      polylineOptions: {
          editable: true,
          draggable: true
      },
      rectangleOptions: polyOptions,
      circleOptions: polyOptions,
      polygonOptions: polyOptions,
      map: map
  });

  google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
      var newShape = e.overlay;
      
      newShape.gks_id = new Date().getTime()+'_'+Math.floor(Math.random()*1000);
      //console.log(newShape.gks_id);
      
      newShape.type = e.type;
      allshapes.push(newShape);
      //console.log(allshapes);
      
      
   
      
              
      if (e.type !== google.maps.drawing.OverlayType.MARKER) {
          // Switch back to non-drawing mode after drawing a shape.
          drawingManager.setDrawingMode(null);

          // Add an event listener that selects the newly-drawn shape when the user
          // mouses down on it.
          google.maps.event.addListener(newShape, 'click', function (e) {
              if (e.vertex !== undefined) {
                  if (newShape.type === google.maps.drawing.OverlayType.POLYGON) {
                      var path = newShape.getPaths().getAt(e.path);
                      path.removeAt(e.vertex);
                      if (path.length < 3) {
                          newShape.setMap(null);
                      }
                  }
                  if (newShape.type === google.maps.drawing.OverlayType.POLYLINE) {
                      var path = newShape.getPath();
                      path.removeAt(e.vertex);
                      if (path.length < 2) {
                          newShape.setMap(null);
                      }
                  }
              }
              setSelection(this.gks_id);
          });
          setSelection(newShape.gks_id);
      }
      else {
          google.maps.event.addListener(newShape, 'click', function (e) {
              setSelection(this.gks_id);
          });
          setSelection(newShape.gks_id);
      }
  });  
  
  google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
  google.maps.event.addListener(map, 'click', clearSelection);
  document.getElementById('map_delete_button').addEventListener('click', map_deleteSelectedShape);
  document.getElementById('map_delete_all_button').addEventListener('click', map_deleteAllShapes);
    
  for (i = 0; i < from_php_map_areas.polygons.length; i++) {
    var newShapeAdd=new google.maps.Polygon({
      type: google.maps.drawing.OverlayType.POLYGON,
      fillColor: from_php_map_areas.polygons[i].color,
      strokeWeight: 0,
      fillOpacity: 0.45,
      editable: false,
      draggable: true,
      map:map,
      path:from_php_map_areas.polygons[i].points
    });
    newShapeAdd.gks_id=from_php_map_areas.polygons[i].id;
    allshapes.push(newShapeAdd);
    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
      setSelection(this.gks_id);
    }); 
    newShapeAdd=null; 
  }
  for (i = 0; i < from_php_map_areas.circles.length; i++) {
    var newShapeAdd=new google.maps.Circle({
      type: google.maps.drawing.OverlayType.CIRCLE,
      fillColor: from_php_map_areas.circles[i].color,
      strokeWeight: 0,
      fillOpacity: 0.45,
      editable: false,
      draggable: true,
      map:map,
      center: new google.maps.LatLng(from_php_map_areas.circles[i].center.lat, from_php_map_areas.circles[i].center.lng),
      radius: from_php_map_areas.circles[i].radius,
    });
    newShapeAdd.gks_id=from_php_map_areas.circles[i].id;    
    allshapes.push(newShapeAdd);
    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
      setSelection(this.gks_id);
    });
    newShapeAdd=null; 
  }
  for (i = 0; i < from_php_map_areas.rectangles.length; i++) {
    var newShapeAdd=new google.maps.Rectangle({
      type: google.maps.drawing.OverlayType.RECTANGLE,
      fillColor: from_php_map_areas.rectangles[i].color,
      strokeWeight: 0,
      fillOpacity: 0.45,
      editable: false,
      draggable: true,
      map:map,
      bounds: {
        north: from_php_map_areas.rectangles[i].corner_left_top.lat,
        south: from_php_map_areas.rectangles[i].corner_right_bottom.lat,
        east:  from_php_map_areas.rectangles[i].corner_right_bottom.lng,
        west:  from_php_map_areas.rectangles[i].corner_left_top.lng,
      },
    });
    newShapeAdd.gks_id=from_php_map_areas.rectangles[i].id;    
    allshapes.push(newShapeAdd);
    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
      setSelection(this.gks_id);
    }); 
    newShapeAdd=null; 
  }
  
  setTimeout(gks_map_fitBounds, 1000);

    
  $('#map_nav_shape_label2').html('0/' + allshapes.length);              
  
//  Metrisi.start();
//  document.querySelector('#start')
//      .addEventListener('click', () => measureTool.start());
//  document.querySelector('#end')
//      .addEventListener('click', () => measureTool.end());


  buildColorPalette();    
}
function gks_map_fitBounds() {
  
  var bounds = new google.maps.LatLngBounds();
  
  for (i = 0; i < allshapes.length; i++) {
    this_bounds=getBounds(allshapes[i]);
    bounds.union(this_bounds);
  }
  if (marker.position.lat!=0 && marker.position.lng!=0) {
    bounds.extend(marker.position);
  }
  //if (bounds.Ha.hi!=-180 || bounds.Ha.lo!=180) {
  if (bounds.isEmpty()==false) {
    map.fitBounds(bounds);
  }  
}


function handleEvent_Marker(event) {
  document.getElementById('poi_map_latitude').value = event.latLng.lat();
  document.getElementById('poi_map_longitude').value = event.latLng.lng();
}
 
 
var map_is_open=false; 
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
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

          jQuery('#poi_map_latitude').val(place_map_latitude);
          jQuery('#poi_map_longitude').val(place_map_longitude);
          
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

var drawingManager;
var selectedShape;
var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
var selectedColor;
var colorButtons = {};
var allshapes=new Array();

function clearSelection () {
  if (selectedShape) {
    if (selectedShape.type !== 'marker') {
        selectedShape.setEditable(false);
    }
    selectedShape = null;
    $('#map_nav_shape_label2').html('0/' + allshapes.length);
  }
}


function setSelection(gks_id) {
  shape=null;
  for (i = 0; i < allshapes.length; i++) {
    if (gks_id==allshapes[i].gks_id) {
      shape=allshapes[i];
      break;
    }
  }
  if (shape==null) return;
  
  if (shape.type !== 'marker') {
    clearSelection();
    shape.setEditable(true);
    selectColor(shape.get('fillColor') || shape.get('strokeColor'));
  }
  selectedShape = shape;
  
  cc_cur=0;
  for (i = 0; i < allshapes.length; i++) {
    if (selectedShape.gks_id==allshapes[i].gks_id) {
      cc_cur=i+1;
      break;
    }
  }
  $('#map_nav_shape_label2').html(cc_cur + '/' + allshapes.length);
}

function map_deleteSelectedShape () {
  if (selectedShape) {
    temp_array=[];
    for (i = 0; i < allshapes.length; i++) {
      if (selectedShape.gks_id!=allshapes[i].gks_id) {
        temp_array.push(allshapes[i]);
      }
    }
    allshapes=temp_array;
    $('#map_nav_shape_label2').html('0/' + allshapes.length);
    selectedShape.setMap(null);
  }
}
function map_deleteAllShapes() {
  for (i = 0; i < allshapes.length; i++) {
    allshapes[i].setMap(null);
  }
  allshapes=[];
  $('#map_nav_shape_label2').html('0/0');
}


function selectColor (color) {
  selectedColor = color;
  for (var i = 0; i < colors.length; ++i) {
    var currColor = colors[i];
    //colorButtons[currColor].style.border = currColor == color ? '0px solid black' : '2px solid #fff';
    colorButtons[currColor].className  = currColor == color ? 'map_color_button map_color_button_select' : 'map_color_button';
      
  }

  // Retrieves the current options from the drawing manager and replaces the
  // stroke or fill color as appropriate.
  var polylineOptions = drawingManager.get('polylineOptions');
  polylineOptions.strokeColor = color;
  drawingManager.set('polylineOptions', polylineOptions);

  var rectangleOptions = drawingManager.get('rectangleOptions');
  rectangleOptions.fillColor = color;
  drawingManager.set('rectangleOptions', rectangleOptions);

  var circleOptions = drawingManager.get('circleOptions');
  circleOptions.fillColor = color;
  drawingManager.set('circleOptions', circleOptions);

  var polygonOptions = drawingManager.get('polygonOptions');
  polygonOptions.fillColor = color;
  drawingManager.set('polygonOptions', polygonOptions);
}

function setSelectedShapeColor (color) {
  if (selectedShape) {
    if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
      selectedShape.set('strokeColor', color);
    } else {
      selectedShape.set('fillColor', color);
    }
  }
}
function makeColorButton (color) {
  var button = document.createElement('span');
  button.className = 'map_color_button';
  button.style.backgroundColor = color;
  button.addEventListener('click', function () {
    selectColor(color);
    setSelectedShapeColor(color);
  });
  return button;
}

function buildColorPalette () {
  var colorPalette = document.getElementById('map_color_palette');
  for (var i = 0; i < colors.length; ++i) {
    var currColor = colors[i];
    var colorButton = makeColorButton(currColor);
    colorPalette.appendChild(colorButton);
    colorButtons[currColor] = colorButton;
  }
  selectColor(colors[0]);
}


function getBounds(mypolygon) {
  if (mypolygon.type=='polygon') {
    var bounds = new google.maps.LatLngBounds();
    var paths = mypolygon.getPaths();
    var pathitem;        
    for (var i = 0; i < paths.getLength(); i++) {
        pathitem = paths.getAt(i);
        for (var ii = 0; ii < pathitem.getLength(); ii++) {
            bounds.extend(pathitem.getAt(ii));
        }
    }
    return bounds;
  }
  if (mypolygon.type=='circle') {
    return mypolygon.getBounds();
  }
  
  if (mypolygon.type=='rectangle') {
    return mypolygon.getBounds();
  }
  
  return null;
}
            
            
            




            
